<?php
/*
##########################################################################
#                                                                        #
#           Version 4       /                        /   /               #
#          -----------__---/__---__------__----__---/---/-               #
#           | /| /  /___) /   ) (_ `   /   ) /___) /   /                 #
#          _|/_|/__(___ _(___/_(__)___/___/_(___ _/___/___               #
#                       Free Content / Management System                 #
#                                   /                                    #
#                                                                        #
#                                                                        #
#   Copyright 2005-2015 by webspell.org                                  #
#                                                                        #
#   visit webSPELL.org, webspell.info to get webSPELL for free           #
#   - Script runs under the GNU GENERAL PUBLIC LICENSE                   #
#   - It's NOT allowed to remove this copyright-tag                      #
#   -- http://www.fsf.org/licensing/licenses/gpl.html                    #
#                                                                        #
#   Code based on WebSPELL Clanpackage (Michael Gruber - webspell.at),   #
#   Far Development by Development Team - webspell.org                   #
#                                                                        #
#   visit webspell.org                                                   #
#                                                                        #
##########################################################################
*/

$_language->readModule('ranks', false, true);
$_language->readModule('rank_special', true, true);

if (!isforumadmin($userID) || mb_substr(basename($_SERVER[ 'REQUEST_URI' ]), 0, 15) != "admincenter.php") {
    die($_language->module[ 'access_denied' ]);
}

if (isset($_GET[ 'delete' ])) {
    $CAPCLASS = new \webspell\Captcha;
    if ($CAPCLASS->checkCaptcha(0, $_GET[ 'captcha_hash' ])) {
        $rankID = (int)$_GET[ 'rankID' ];
        safe_query("UPDATE " . PREFIX . "user SET special_rank='0' WHERE special_rank='" . $rankID . "'");
        safe_query("DELETE FROM " . PREFIX . "forum_ranks WHERE rankID='" . $rankID . "'");
    } else {
        echo $_language->module[ 'transaction_invalid' ];
    }
} elseif (isset($_POST[ 'save' ])) {
    $name = $_POST[ 'name' ];
    $max = $_POST[ 'max' ];
    $min = $_POST[ 'min' ];

    $CAPCLASS = new \webspell\Captcha;
    if ($CAPCLASS->checkCaptcha(0, $_POST[ 'captcha_hash' ])) {
        if (checkforempty(array('min', 'max')) || isset($_POST['special'])) {
            if ($max == "MAX") {
                $maximum = 2147483647;
            } else {
                $maximum = $max;
            }

            safe_query(
                "INSERT INTO
                    `" . PREFIX . "forum_ranks` (
                        `rank`,
                        `postmin`,
                        `postmax`,
                        `special`
                    )
                    VALUES (
                        '$name',
                        '$min',
                        '$maximum',
                        '".isset($_POST['special'])."'
                    )"
            );
            $id = mysqli_insert_id($_database);

            $filepath = "../images/icons/ranks/";

            $errors = array();


            //TODO: should be loaded from root language folder
            $_language->readModule('formvalidation', true);

            $upload = new \webspell\HttpUpload('rank');
            if ($upload->hasFile()) {
                if ($upload->hasError() === false) {
                    $mime_types = array('image/jpeg','image/png','image/gif');

                    if ($upload->supportedMimeType($mime_types)) {
                        $imageInformation = getimagesize($upload->getTempFile());

                        if (is_array($imageInformation)) {
                            switch ($imageInformation[ 2 ]) {
                                case 1:
                                    $endung = '.gif';
                                    break;
                                case 3:
                                    $endung = '.png';
                                    break;
                                default:
                                    $endung = '.jpg';
                                    break;
                            }
                            $file = $tag . $endung;

                            if ($upload->saveAs($filepath . $file, true)) {
                                @chmod($filepath . $file, $new_chmod);
                                safe_query(
                                    "UPDATE " . PREFIX . "forum_ranks SET pic='".$file."' WHERE rankID='".$id."'"
                                );
                            }
                        } else {
                            $errors[] = $_language->module['broken_image'];
                        }
                    } else {
                        $errors[] = $_language->module['unsupported_image_type'];
                    }
                } else {
                    $errors[] = $upload->translateError();
                }
            }
            if (count($errors)) {
                $errors = array_unique($errors);
                echo generateErrorBoxFromArray($_language->module['errors_there'], $errors);
            }
        } else {
            echo $_language->module[ 'information_incomplete' ];
        }
    } else {
        echo $_language->module[ 'transaction_invalid' ];
    }
} elseif (isset($_POST[ 'saveedit' ])) {
    $rank = $_POST[ 'rank' ];
    $min = $_POST[ 'min' ];
    $max = $_POST[ 'max' ];

    $CAPCLASS = new \webspell\Captcha;
    if ($CAPCLASS->checkCaptcha(0, $_POST[ 'captcha_hash' ])) {
        if (checkforempty(array('min', 'max'))) {
            $ergebnis = safe_query("SELECT * FROM " . PREFIX . "forum_ranks ORDER BY rankID");
            $anz = mysqli_num_rows($ergebnis);
            if ($anz) {
                while ($ds = mysqli_fetch_array($ergebnis)) {
                    if ($ds[ 'rank' ] != "Administrator" && $ds[ 'rank' ] != "Moderator") {
                        $id = $ds[ 'rankID' ];
                        if ($ds[ 'special' ] != 1) {
                            $minimum = $min[$id];
                            if ($max[ $id ] == "MAX") {
                                $maximum = 2147483647;
                            } else {
                                $maximum = $max[ $id ];
                            }
                        } else {
                            $maximum = 0;
                            $minimum = 0 ;
                        }
                        safe_query(
                            "UPDATE
                                " . PREFIX . "forum_ranks
                            SET
                                rank='".$rank[$id]."',
                                postmin='".$minimum."',
                                postmax='".$maximum."'
                            WHERE rankID='$id'"
                        );
                    }
                }
            }
        } else {
            echo $_language->module[ 'information_incomplete' ];
        }
    } else {
        echo $_language->module[ 'transaction_invalid' ];
    }
}

if (isset($_GET[ 'action' ])) {
    $action = $_GET[ 'action' ];
} else {
    $action = '';
}

if ($action == "add") {
    $CAPCLASS = new \webspell\Captcha;
    $CAPCLASS->createTransaction();
    $hash = $CAPCLASS->getHash();

    echo'<div class="panel panel-default">
     <div class="panel-heading">
                            <i class="fa fa-line-chart"></i> '.$_language->module['user_ranks'].'
                        </div>
                        <div class="panel-body">
  <a href="admincenter.php?site=ranks" class="white">'.$_language->module['user_ranks'].'</a> &raquo; '.$_language->module['add_rank'].'<br><br>';

    echo '<script type="text/javascript">
  function HideFields(state){
  	if(state == true){
  		document.getElementById(\'max\').style.display = "none";
  		document.getElementById(\'min\').style.display = "none";
  	}
  	else{
  		document.getElementById(\'max\').style.display = "";
  		document.getElementById(\'min\').style.display = "";
  	}
  }
  </script>';
  


  echo'<form class="form-horizontal" method="post" action="admincenter.php?site=ranks" enctype="multipart/form-data">
  <div class="row">

<div class="col-md-6">

    <div class="form-group">
    <label class="col-sm-4 control-label">'.$_language->module['rank_name'].':</label>
    <div class="col-sm-8"><span class="text-muted small"><em>
        <input class="form-control" type="text" name="name" size="60" /></em></span>
    </div>
  </div>

  <div class="form-group">
    <label class="col-sm-4 control-label">'.$_language->module['rank_icon'].':</label>
    <div class="col-sm-8"><span class="text-muted small"><em>
		<input name="rank" type="file" size="40" /></em></span>
    </div>
  </div>
  

  </div>

<div class="col-md-6">

    <div id="min" class="form-group">
    <label class="col-sm-4 control-label">'.$_language->module['min_posts'].':</label>
    <div class="col-sm-8"><span class="text-muted small"><em>
        <input class="form-control" type="text" name="min" size="4" /></em></span>
    </div>
  </div>
  <div id="max" class="form-group">
    <label class="col-sm-4 control-label">'.$_language->module['max_posts'].':</label>
    <div class="col-sm-8"><span class="text-muted small"><em>
        <input class="form-control" type="text" name="max" size="4" /></em></span>
    </div>
  </div>
  <div class="form-group">
    <label class="col-sm-4 control-label">' . $_language->module[ 'special_rank' ] . ':</label>
    <div class="col-sm-8"><span class="text-muted small"><em>
        <input type="checkbox" name="special" onchange="javascript:HideFields(this.checked);" value="1" /></em></span>
    </div>
  </div>

  </div>
  <div class="form-group">
    <div class="col-sm-offset-2 col-sm-8">
		<input type="hidden" name="captcha_hash" value="'.$hash.'" />
		<button class="btn btn-success btn-xs" type="submit" name="save" />'.$_language->module['add_rank'].'</button>
    </div>
  </div>
</form>
  </div>
  </div>';
}

else {
	
  echo'<div class="panel panel-default">
  <div class="panel-heading">
                            <i class="fa fa-line-chart"></i> '.$_language->module['user_ranks'].'
                        </div>
            <div class="panel-body">';
  
  echo'<a href="admincenter.php?site=ranks&amp;action=add" class="btn btn-primary btn-xs" type="button">' . $_language->module[ 'new_rank' ] . '</a><br /><br />';
	
  echo'<form method="post" action="admincenter.php?site=ranks">
  <table class="table table-striped">
    <thead>
      <th class="hidden-xs"><b>'.$_language->module['rank_icon'].'</b></th>
      <th><b>'.$_language->module['rank_name'].'</b></th>
      <th><b>' . $_language->module[ 'special_rank' ] . '</b></th>
      <th><b>'.$_language->module['min_posts'].'</b></th>
      <th><b>'.$_language->module['max_posts'].'</b></th>
      <th><b>'.$_language->module['actions'].'</b></th>
      </thead>';

	$ergebnis = safe_query("SELECT * FROM " . PREFIX . "forum_ranks ORDER BY postmax");
    $CAPCLASS = new \webspell\Captcha;
    $CAPCLASS->createTransaction();
    $hash = $CAPCLASS->getHash();
    $i = 1;
    while ($ds = mysqli_fetch_array($ergebnis)) {
        
        if ($ds[ 'rank' ] == "Administrator" || $ds[ 'rank' ] == "Moderator") {
            echo '<tr>
	        <td class="hidden-xs" align="center"><img src="../images/icons/ranks/' . $ds[ 'pic' ] . '" alt=""></td>
	        <td><span class="text-muted small"><em>' . $ds[ 'rank' ] . '</em></span></td>
	        <td align="center">x</td>
	        <td>&nbsp;</td>
	        <td>&nbsp;</td>
	        <td>&nbsp;</td>
	      </tr>';
        } else {
            if (mb_strlen(trim($ds[ 'postmax' ])) > 8) {
                $max = "MAX";
            } else {
                $max = $ds[ 'postmax' ];
            }

            $user_list = "";
            $min = '<input class="form-control" type="text" name="min['.$ds['rankID'].']" value="'.$ds['postmin'].'" size="6" dir="rtl" />';
            $max = '<input class="form-control" type="text" name="max['.$ds['rankID'].']" value="'.$max.'" size="6" dir="rtl" />';

            if ($ds['special']==1) {
                $get = safe_query(
                    "SELECT
                        nickname,
                        userID
                    FROM
                        `".PREFIX."user`
                    WHERE
                        special_rank = '" . $ds['rankID'] . "'"
                );
                $user_list = array();
                while ($user = mysqli_fetch_assoc($get)) {
                    $user_list[] = '<a href="admincenter.php?site=members&amp;action=edit&amp;id=' .
                        $user['userID'] . '">' . $user['nickname'] . '</a>';
                }
                $user_list = "<br/><small>" . $_language->module['used_for'] . ": " .
                    implode(", ", $user_list) . "</small>";
                $min = "";
                $max = "";
            }

             echo '<tr>
	        <td  class="hidden-xs" align="center"><img src="../images/icons/ranks/' . $ds[ 'pic' ] . '" alt=""></td>
	        <td><span class="text-muted small"><em><input class="form-control" type="text" name="rank[' . $ds[ 'rankID' ] . ']" value="' .
                getinput($ds[ 'rank' ]) . '" size="30" />'.$user_list.'</em></span></td>
            <td align="center"><span class="text-muted small"><em>' . (($ds[ 'special' ]==1) ? "x" : "") . '</em></span></td>

	        <td align="center"><span class="text-muted small"><em>'.$min.'</em></span></td>
	        <td align="center"><span class="text-muted small"><em>'.$max.'</em></span></td>
	        <td align="center">
                
                <input class="hidden-xs hidden-sm btn btn-danger btn-xs" type="button" onclick="MM_confirm(\'' . $_language->module['really_delete'] . '\', \'admincenter.php?site=ranks&amp;delete=true&amp;rankID=' .
                $ds[ 'rankID' ] . '&amp;captcha_hash=' . $hash . '\')" value="' . $_language->module['delete'] . '" />
                
                <a class="mobile visible-xs visible-sm" type="button" onclick="MM_confirm(\'' . $_language->module['really_delete'] . '\', \'admincenter.php?site=ranks&amp;delete=true&amp;rankID=' .
                $ds[ 'rankID' ] . '&amp;captcha_hash=' . $hash . '\')" /><i class="fa fa-times"></i></a>

                </td>
	      </tr>';
        }
        $i++;
    }
    echo '<tr>
      <td class="td_head" colspan="6" align="right"><input type="hidden" name="captcha_hash" value="' . $hash .
        '"><input class="btn btn-primary btn-xs" type="submit" name="saveedit" value="' . $_language->module[ 'update' ] . '" /></td>
    </tr>
  </table>
  </form></div></div>';
}
