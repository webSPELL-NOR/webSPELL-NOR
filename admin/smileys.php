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

$_language->readModule('smileys', false, true);

if (!ispageadmin($userID) || mb_substr(basename($_SERVER[ 'REQUEST_URI' ]), 0, 15) != "admincenter.php") {
    die($_language->module[ 'access_denied' ]);
}

$filepath = "../images/smileys/";

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
                            <i class="fa fa-smile-o"></i> ' . $_language->module['smilies'] . '
                        </div>
                        <div class="panel-body">
 <a href="admincenter.php?site=smileys" class="white">' . $_language->module['smilies'] . '</a> &raquo; ' . $_language->module['add_smiley'] . '<br><br>';
  
  echo'<form class="form-horizontal" method="post" action="admincenter.php?site=smileys" enctype="multipart/form-data">
  <div class="form-group">
    <label class="col-sm-2 control-label">' . $_language->module['icon'] . '</label>
    <div class="col-sm-8">
      <input name="icon" type="file" size="40" />
    </div>
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">' . $_language->module['smiley_name'] . '</label>
    <div class="col-sm-8">
      <input class="form-control" type="text" name="alt" maxlength="255" />
    </div>
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">' . $_language->module['pattern'] . '</label>
    <div class="col-sm-8">
      <input class="form-control" type="text" name="pattern" size="5" maxlength="3" />
    </div>
  </div>
  <div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
		<input type="hidden" name="captcha_hash" value="' . $hash . '" />
		<button class="btn btn-success" type="submit" name="save"  />' . $_language->module['add_smiley'] . '</button>
    </div>
  </div>
  </form>';

} elseif ($action == "edit") {
    $CAPCLASS = new \webspell\Captcha;
    $CAPCLASS->createTransaction();
    $hash = $CAPCLASS->getHash();
    $ds = mysqli_fetch_array(
        safe_query(
            "SELECT * FROM " . PREFIX . "smileys WHERE smileyID='" . $_GET[ "smileyID" ] . "'"
        )
    );
    $pic = '<img src="../images/smileys/' . $ds[ 'name' ] . '" alt="' . getinput($ds[ 'alt' ]) . '">';

	echo'<div class="panel panel-default">
   <div class="panel-heading">
                            <i class="fa fa-smile-o"></i> ' . $_language->module['smilies'] . '
                        </div>
                        <div class="panel-body">
    <a href="admincenter.php?site=smileys" class="white">' . $_language->module['smilies'] . '</a> &raquo; ' . $_language->module['edit_smiley'] . '<br><br>';
  
  echo'<form class="form-horizontal" method="post" action="admincenter.php?site=smileys" enctype="multipart/form-data">
		<input type="hidden" name="smileyID" value="' . $ds['smileyID'] . '" />
    <div class="form-group">
    <label class="col-sm-2 control-label">' . $_language->module['present_icon'] . '</label>
    <div class="col-sm-8">
      <p class="form-control-static">' . $pic . '</p>
    </div>
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">' . $_language->module['icon'] . '</label>
    <div class="col-sm-8">
      <input name="icon" type="file" size="40" />
    </div>
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">' . $_language->module['smiley_name'] . '</label>
    <div class="col-sm-8">
      <input class="form-control" type="text" name="alt" maxlength="255" value="' . htmlspecialchars($ds['alt']) . '" />
    </div>
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">' . $_language->module['pattern'] . '</label>
    <div class="col-sm-8">
      <input class="form-control" type="text" name="pattern" size="5" maxlength="3" value="' . htmlspecialchars($ds['pattern']) . '" />
    </div>
  </div>
  <div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
		<input type="hidden" name="captcha_hash" value="' . $hash . '" />
		<button class="btn btn-success" type="submit" name="saveedit"  />' . $_language->module['edit_smiley'] . '</button>
    </div>
  </div>
  </form>
  </div>
  </div>';
  
} elseif (isset($_POST[ "save" ])) {
    $alt = $_POST[ "alt" ];
    $pattern = $_POST[ "pattern" ];
    $CAPCLASS = new \webspell\Captcha;
    if ($CAPCLASS->checkCaptcha(0, $_POST[ 'captcha_hash' ])) {
        if (checkforempty(array('pattern'))) {
            $errors = array();

            //TODO: should be loaded from root language folder
            $_language->readModule('formvalidation', true);

            $upload = new \webspell\HttpUpload('icon');
            if ($upload->hasFile()) {
                if ($upload->hasError() === false) {
                    $mime_types = array('image/gif');

                    if ($upload->supportedMimeType($mime_types)) {
                        $imageInformation = getimagesize($upload->getTempFile());
                        if (is_array($imageInformation)) {
                            $file = $pattern . ' . gif';

                            if ($upload->saveAs($filepath . $file, true)) {
                                @chmod($filepath . $file, $new_chmod);
                                safe_query(
                                    "INSERT INTO " . PREFIX . "smileys (
                                        name,
                                        alt,
                                        pattern
                                    ) VALUES (
                                        '" . $file . "',
                                        '" . $alt . "',
                                        '" . $pattern . "'
                                    )"
                                );
                            }
              redirect("admincenter.php?site=smileys", "", 0);
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
            echo '<b>' . $_language->module[ 'fill_form' ] .
                '</b><br /><br /><a href="javascript:history.back()">&laquo; ' . $_language->module[ 'back' ] . '</a>';
        }
    } else {
        echo $_language->module[ 'transaction_invalid' ];
    }
} elseif (isset($_POST[ "saveedit" ])) {
    $alt = $_POST[ "alt" ];
    $pattern = $_POST[ 'pattern' ];
    $CAPCLASS = new \webspell\Captcha;
    if ($CAPCLASS->checkCaptcha(0, $_POST[ 'captcha_hash' ])) {
        if (checkforempty(array('pattern'))) {
            safe_query(
                "UPDATE
                    " . PREFIX . "smileys
                SET
                    alt='" . $alt . "',
                    pattern='" . $pattern ."'
                WHERE smileyID='" . $_POST[ "smileyID" ] . "'"
            );


            $errors = array();

            //TODO: should be loaded from root language folder
            $_language->readModule('formvalidation', true);

            $upload = new \webspell\HttpUpload('icon');
            if ($upload->hasFile()) {
                if ($upload->hasError() === false) {
                    $mime_types = array('image/gif');

                    if ($upload->supportedMimeType($mime_types)) {
                        $imageInformation = getimagesize($upload->getTempFile());
                        if (is_array($imageInformation)) {
                            $file = $pattern . ' . gif';

                            if ($upload->saveAs($filepath . $file, true)) {
                                @chmod($filepath . $file, $new_chmod);
                redirect("admincenter.php?site=smileys", "", 0);
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
            } else {
        redirect("admincenter.php?site=smileys", "", 0);
      }
            if (count($errors)) {
                $errors = array_unique($errors);
                echo generateErrorBoxFromArray($_language->module['errors_there'], $errors);
            }
        } else {
            echo '<b>' . $_language->module[ 'fill_form' ] .
                '</b><br /><br /><a href="javascript:history.back()">&laquo; ' . $_language->module[ 'back' ] . '</a>';
        }
    } else {
        echo $_language->module[ 'transaction_invalid' ];
    }
} elseif (isset($_GET[ "delete" ])) {
    $CAPCLASS = new \webspell\Captcha;
    if ($CAPCLASS->checkCaptcha(0, $_GET[ 'captcha_hash' ])) {
        safe_query("DELETE FROM " . PREFIX . "smileys WHERE smileyID='" . $_GET[ "smileyID" ] . "'");
        redirect('admincenter.php?site=smileys', '', 0);
    } else {
        redirect('admincenter.php?site=smileys', $_language->module[ 'transaction_invalid' ], 3);
    }
} else {
	echo'<div class="panel panel-default">
  <div class="panel-heading">
                            <i class="fa fa-smile-o"></i> ' . $_language->module['smilies'] . '
                        </div>
<div class="panel-body">';
  
  echo'<a href="admincenter.php?site=smileys&amp;action=add" class="btn btn-primary btn-xs" type="button">' . $_language->module[ 'new_smiley' ] . '</a><br /><br />';
  
  echo'<form method="post" action="admincenter.php?site=smileys">
  <table class=" table table-striped">
    <thead>
      <th><b>' . $_language->module['icon'] . '</b></th>
      <th><b>' . $_language->module['smiley_name'] . '</b></th>
      <th><b>' . $_language->module['pattern'] . '</b></th>
      <th><b>' . $_language->module['actions'] . '</b></th>
    </thead>';
    
	 $ds = safe_query("SELECT * FROM " . PREFIX . "smileys");
    $anz = mysqli_num_rows($ds);
    if ($anz) {
        $CAPCLASS = new \webspell\Captcha;
        $CAPCLASS->createTransaction();
        $hash = $CAPCLASS->getHash();
        $i = 1;
        while ($smileys = mysqli_fetch_array($ds)) {
            if ($i % 2) {
                $td = 'td1';
            } else {
                $td = 'td2';
            }

            $pic = '<img src="../images/smileys/' . $smileys[ 'name' ] . '" alt="' . getinput($smileys[ 'alt' ]) . '">';
            if ($smileys[ 'alt' ] == "") {
                $smileys[ 'alt' ] = $smileys[ 'name' ];
            }
			
      echo'<tr>
        <td>' . $pic . '</td>
        <td>' . $smileys['alt'] . '</td>
        <td>' . $smileys['pattern'] . '</td>
        <td><a href="admincenter.php?site=smileys&amp;action=edit&amp;smileyID=' . $smileys['smileyID'] . '" class="hidden-xs hidden-sm btn btn-warning btn-xs" type="button">' . $_language->module[ 'edit' ] . '</a>

        <input class="hidden-xs hidden-sm btn btn-danger btn-xs" type="button" onclick="MM_confirm(\'' . $_language->module['really_delete'] . '\', \'admincenter.php?site=smileys&amp;delete=true&amp;smileyID=' . $smileys['smileyID'] . '&amp;captcha_hash=' . $hash . '\')" value="' . $_language->module['delete'] . '" />

		<a href="admincenter.php?site=smileys&amp;action=edit&amp;smileyID=' . $smileys['smileyID'] . '"  class="mobile visible-xs visible-sm" type="button"><i class="fa fa-pencil"></i></a>
        <a class="mobile visible-xs visible-sm" type="button" onclick="MM_confirm(\'' . $_language->module['really_delete'] . '\', \'admincenter.php?site=smileys&amp;delete=true&amp;smileyID=' . $smileys['smileyID'] . '&amp;captcha_hash=' . $hash . '\')" /><i class="fa fa-times"></i></a></td>
      </tr>';
      
      $i++;
		}
	} else echo'<tr><td class="td1">' . $_language->module['no_entries'] . '</td></tr>';
	echo '</table>
  </form>';
}
echo '</div></div>';
?>