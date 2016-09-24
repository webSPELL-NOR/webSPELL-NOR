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

$_language->readModule('rubrics', false, true);

if (!isnewsadmin($userID) || mb_substr(basename($_SERVER[ 'REQUEST_URI' ]), 0, 15) != "admincenter.php") {
    die($_language->module[ 'access_denied' ]);
}

if (isset($_POST[ 'save' ])) {
    $CAPCLASS = new \webspell\Captcha;
    if ($CAPCLASS->checkCaptcha(0, $_POST[ 'captcha_hash' ])) {
        if (checkforempty(array('name'))) {
            safe_query("INSERT INTO " . PREFIX . "news_rubrics ( rubric ) values( '" . $_POST[ 'name' ] . "' ) ");
            $id = mysqli_insert_id($_database);

            $filepath = "../images/news-rubrics/";

            $errors = array();

            //TODO: should be loaded from root language folder
            $_language->readModule('formvalidation', true);

            $upload = new \webspell\HttpUpload('pic');
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
                            $file = $id . $endung;

                            if ($upload->saveAs($filepath . $file, true)) {
                                @chmod($filepath . $file, $new_chmod);
                                safe_query(
                                    "UPDATE " . PREFIX . "news_rubrics
                                    SET pic='" . $file . "' WHERE rubricID='" . $id . "'"
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
    $CAPCLASS = new \webspell\Captcha;
    if ($CAPCLASS->checkCaptcha(0, $_POST[ 'captcha_hash' ])) {
        if (checkforempty(array('name'))) {
            safe_query(
                "UPDATE
                    `" . PREFIX . "news_rubrics`
                SET
                    `rubric` = '" . $_POST[ 'name' ] . "'
                WHERE
                    `rubricID` = '" . $_POST[ 'rubricID' ] . "'"
            );

            $id = $_POST[ 'rubricID' ];
            $filepath = "../images/news-rubrics/";

            $errors = array();

            //TODO: should be loaded from root language folder
            $_language->readModule('formvalidation', true);

            $upload = new \webspell\HttpUpload('pic');
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
                            $file = $id . $endung;

                            if ($upload->saveAs($filepath . $file, true)) {
                                @chmod($filepath . $file, $new_chmod);
                                safe_query(
                                    "UPDATE " . PREFIX . "news_rubrics
                                    SET pic='" . $file . "' WHERE rubricID='" . $id . "'"
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
} elseif (isset($_GET[ 'delete' ])) {
    $CAPCLASS = new \webspell\Captcha;
    if ($CAPCLASS->checkCaptcha(0, $_GET[ 'captcha_hash' ])) {
        $rubricID = (int)$_GET[ 'rubricID' ];
        $filepath = "../images/news-rubrics/";
        safe_query("DELETE FROM " . PREFIX . "news_rubrics WHERE rubricID='$rubricID'");
        if (file_exists($filepath . $rubricID . '.gif')) {
            @unlink($filepath . $rubricID . '.gif');
        }
        if (file_exists($filepath . $rubricID . '.jpg')) {
            @unlink($filepath . $rubricID . '.jpg');
        }
        if (file_exists($filepath . $rubricID . '.png')) {
            @unlink($filepath . $rubricID . '.png');
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
                            <i class="fa fa-indent"></i> ' . $_language->module[ 'news_rubrics' ] . '
                        </div>
            <div class="panel-body">

<a href="admincenter.php?site=rubrics" class="white">'.$_language->module['news_rubrics'].'</a> &raquo; '.$_language->module['add_rubric'].'<br><br>';

	echo'<form class="form-horizontal" method="post" action="admincenter.php?site=rubrics" enctype="multipart/form-data">
		<div class="form-group">
    <label class="col-sm-2 control-label">'.$_language->module['rubric_name'].':</label>
    <div class="col-sm-8">
      <input type="text" class="form-control" name="name"  />
    </div>
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">'.$_language->module['picture_upload'].':</label>
    <div class="col-sm-8"><span class="text-muted small"><em>
     <p class="form-control-static"><input name="pic" type="file" size="40" /></p></em></span>
    </div>
  </div>
  <div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
      <input type="hidden" name="captcha_hash" value="'.$hash.'" /><button class="btn btn-success btn-xs" type="submit" name="save" />'.$_language->module['add_rubric'].'</button>
    </div>
  </div>
  </form></div></div>';

} elseif ($action == "edit") {
    $CAPCLASS = new \webspell\Captcha;
    $CAPCLASS->createTransaction();
    $hash = $CAPCLASS->getHash();
    echo '<div class="panel panel-default"><div class="panel-heading">
                            <i class="fa fa-indent"></i> ' . $_language->module[ 'news_rubrics' ] . '
                        </div>
                <div class="panel-body">

<a href="admincenter.php?site=rubrics" class="white">' . $_language->module[ 'news_rubrics' ] .
        '</a> &raquo; ' . $_language->module[ 'edit_rubric' ] . '<br><br>';

    $rubricID = (int)$_GET[ 'rubricID' ];
    $ergebnis = safe_query("SELECT * FROM " . PREFIX . "news_rubrics WHERE rubricID='$rubricID'");
    $ds = mysqli_fetch_array($ergebnis);

	echo'<form class="form-horizontal" method="post" action="admincenter.php?site=rubrics" enctype="multipart/form-data">
  <div class="form-group">
    <label class="col-sm-2 control-label">'.$_language->module['rubric_name'].':</label>
    <div class="col-sm-8"><span class="text-muted small"><em>
      <input type="text" class="form-control" name="name" value="'.getinput($ds['rubric']).'" /></em></span>
    </div>
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">'.$_language->module['picture'].':</label>
    <div class="col-sm-8">
      <p class="form-control-static"><img src="../images/news-rubrics/'.$ds['pic'].'" alt="" /></p>
    </div>
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">'.$_language->module['picture_upload'].':</label>
    <div class="col-sm-8"><span class="text-muted small"><em>
     <p class="form-control-static"><input name="pic" type="file" size="40" /></p></em></span>
    </div>
  </div>
  <div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
     <input type="hidden" name="captcha_hash" value="'.$hash.'" /><input type="hidden" name="rubricID" value="'.$ds['rubricID'].'" /><button class="btn-success btn-xs" type="submit" name="saveedit" />'.$_language->module['edit_rubric'].'</button>
    </div>
  </div>
  </form></div></div>';
}

else {

  echo'<div class="panel panel-default">
  <div class="panel-heading">
                            <i class="fa fa-indent"></i> ' . $_language->module[ 'news_rubrics' ] . '
                        </div>
    <div class="panel-body">

<div class="row">
<div class="col-md-12">';

	echo'<a href="admincenter.php?site=rubrics&amp;action=add" class="btn btn-primary btn-xs" type="button">' . $_language->module[ 'new_rubric' ] . '</a><br /><br />';

	$ergebnis = safe_query("SELECT * FROM " . PREFIX . "news_rubrics ORDER BY rubric");
	
  echo'<table class="table table-striped">
    <thead>
      <tr>
      <th><b>'.$_language->module['rubric_name'].':</b></th>
      <th><b>'.$_language->module['picture'].':</b></th>
      <th><b>'.$_language->module['actions'].':</b></th>
   		</tr></thead>
          <tbody>';
	 $CAPCLASS = new \webspell\Captcha;
    $CAPCLASS->createTransaction();
    $hash = $CAPCLASS->getHash();
    $i = 1;
    while ($ds = mysqli_fetch_array($ergebnis)) {
        if ($i % 2) {
            $td = 'td1';
        } else {
            $td = 'td2';
        }
    
		echo'<tr>
      <td>'.getinput($ds['rubric']).'</td>
      <td><img style="width: 100%; max-width: 460px" src="../images/news-rubrics/'.$ds['pic'].'" alt="" width="100%" /></td>
      <td><a href="admincenter.php?site=rubrics&amp;action=edit&amp;rubricID='.$ds['rubricID'].'" class="hidden-xs hidden-sm btn btn-warning btn-xs" type="button">' . $_language->module[ 'edit' ] . '</a>

      <input class="hidden-xs hidden-sm btn btn-danger btn-xs" type="button" onclick="MM_confirm(\''.$_language->module['really_delete'].'\', \'admincenter.php?site=rubrics&amp;delete=true&amp;rubricID='.$ds['rubricID'].'&amp;captcha_hash='.$hash.'\')" value="'.$_language->module['delete'].'" />

      <a href="admincenter.php?site=rubrics&amp;action=edit&amp;rubricID='.$ds['rubricID'].'"  class="mobile visible-xs visible-sm" type="button"><i class="fa fa-pencil"></i></a>
      <a class="mobile visible-xs visible-sm" type="button" onclick="MM_confirm(\'' . $_language->module['really_delete'] . '\', \'admincenter.php?site=rubrics&amp;delete=true&amp;rubricID='.$ds['rubricID'].'&amp;captcha_hash='.$hash.'\')" /><i class="fa fa-times"></i></a></td>
    </tr>';
      
      $i++;
	}
	echo'</tbody></table>';
}
echo '</div></div></div></div>';
?>