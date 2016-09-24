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

$_language->readModule('gallery', false, true);

if (!isgalleryadmin($userID) || mb_substr(basename($_SERVER[ 'REQUEST_URI' ]), 0, 15) != "admincenter.php") {
    die($_language->module[ 'access_denied' ]);
}

$galclass = new \webspell\Gallery;

if (isset($_GET[ 'part' ])) {
    $part = $_GET[ 'part' ];
} else {
    $part = '';
}
if (isset($_GET[ 'action' ])) {
    $action = $_GET[ 'action' ];
} else {
    $action = '';
}

if ($part == "groups") {
    if (isset($_POST[ 'save' ])) {
        $CAPCLASS = new \webspell\Captcha;
        if ($CAPCLASS->checkCaptcha(0, $_POST[ 'captcha_hash' ])) {
            if (checkforempty(array('name'))) {
                safe_query(
                    "INSERT INTO " . PREFIX . "gallery_groups ( name, sort ) values( '" . $_POST[ 'name' ] . "', '1' ) "
                );
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
                    "UPDATE " . PREFIX . "gallery_groups SET name='" . $_POST[ 'name' ] . "'
                    WHERE groupID='" . $_POST[ 'groupID' ] . "'"
                );
            } else {
                echo $_language->module[ 'information_incomplete' ];
            }
        } else {
            echo $_language->module[ 'transaction_invalid' ];
        }
    } elseif (isset($_POST[ 'sort' ])) {
        $CAPCLASS = new \webspell\Captcha;
        if ($CAPCLASS->checkCaptcha(0, $_POST[ 'captcha_hash' ])) {
            if (isset($_POST[ 'sortlist' ])) {
                if (is_array($_POST[ 'sortlist' ])) {
                    foreach ($_POST[ 'sortlist' ] as $sortstring) {
                        $sorter = explode("-", $sortstring);
                        safe_query(
                            "UPDATE " . PREFIX . "gallery_groups
                            SET sort='$sorter[1]'
                            WHERE groupID='$sorter[0]' "
                        );
                    }
                }
            }
        } else {
            echo $_language->module[ 'transaction_invalid' ];
        }
    } elseif (isset($_GET[ 'delete' ])) {
        $CAPCLASS = new \webspell\Captcha;
        if ($CAPCLASS->checkCaptcha(0, $_GET[ 'captcha_hash' ])) {
            $db_result = safe_query("SELECT * FROM " . PREFIX . "gallery WHERE groupID='" . $_GET[ 'groupID' ] . "'");
            $any = mysqli_num_rows($db_result);
            if ($any) {
                echo $_language->module[ 'galleries_available' ] . '<br /><br />';
            } else {
                safe_query("DELETE FROM " . PREFIX . "gallery_groups WHERE groupID='" . $_GET[ 'groupID' ] . "'");
            }
        } else {
            echo $_language->module[ 'transaction_invalid' ];
        }
    }

    if ($action == "add") {
        $CAPCLASS = new \webspell\Captcha;
        $CAPCLASS->createTransaction();
        $hash = $CAPCLASS->getHash();
    
    echo'<div class="panel panel-default">
    <div class="panel-heading">
                            <i class="fa fa-file-image-o"></i> '.$_language->module['gallery'].'
                        </div>
                        <div class="panel-body">
   <a href="admincenter.php?site=gallery&amp;part=groups" class="white">'.$_language->module['groups'].'</a> &raquo; '.$_language->module['add_group'].'<br><br>';
    
    echo'<form class="form-horizontal" method="post" action="admincenter.php?site=gallery&amp;part=groups">
  <div class="form-group">
    <label class="col-sm-2 control-label">'.$_language->module['group_name'].':</label>
    <div class="col-sm-8"><span class="text-muted small"><em>
      <input class="form-control" type="text" name="name" size="60" /></em></span>
    </div>
  </div>
  <div class="form-group">
    <div class="col-sm-offset-2 col-sm-8">
	<input type="hidden" name="captcha_hash" value="'.$hash.'" />
      <button class="btn btn-success btn-xs" type="submit" name="save" />'.$_language->module['add_group'].'</button>
    </div>
  </div>
</form>
    </div></div>';
	 } elseif ($action == "edit") {
        $CAPCLASS = new \webspell\Captcha;
        $CAPCLASS->createTransaction();
        $hash = $CAPCLASS->getHash();
        $ergebnis = safe_query("SELECT * FROM " . PREFIX . "gallery_groups WHERE groupID='" . $_GET[ 'groupID' ] . "'");
        $ds = mysqli_fetch_array($ergebnis);

		echo'<div class="panel panel-default">
        <div class="panel-heading">
                            <i class="fa fa-file-image-o"></i> '.$_language->module['gallery'].'
                        </div>
                        <div class="panel-body">
		<a href="admincenter.php?site=gallery&amp;part=groups" class="white">'.$_language->module['groups'].'</a> &raquo; '.$_language->module['edit_group'].'<br><br>';
    
    echo'<form class="form-horizontal" method="post" action="admincenter.php?site=gallery&amp;part=groups">
	  <div class="form-group">
    <label class="col-sm-2 control-label">'.$_language->module['group_name'].':</label>
    <div class="col-sm-8"><span class="text-muted small"><em>
      <input class="form-control" type="text" name="name" value="'.getinput($ds['name']).'" /></em></span>
    </div>
  </div>
  <div class="form-group">
    <div class="col-sm-offset-2 col-sm-8">
	<input type="hidden" name="captcha_hash" value="'.$hash.'" /><input type="hidden" name="groupID" value="'.$ds['groupID'].'" />
      <button class="btn btn-success btn-xs" type="submit" name="saveedit" />'.$_language->module['edit_group'].'</button>
    </div>
  </div>
    </form></div></div>';
	}
  
	else {
		echo'<div class="panel panel-default">
        <div class="panel-heading">
                            <i class="fa fa-file-image-o"></i> '.$_language->module['gallery'].'
                        </div>
                        <div class="panel-body">
		<h4>'.$_language->module['groups'].'</h4>';

    echo'<a href="admincenter.php?site=gallery&amp;part=groups&amp;action=add" class="btn btn-primary btn-xs" type="button">' . $_language->module[ 'new_group' ] . '</a><br /><br />';

		$ergebnis = safe_query("SELECT * FROM " . PREFIX . "gallery_groups ORDER BY sort");
		
    echo'<form method="post" name="ws_gallery" action="admincenter.php?site=gallery&amp;part=groups">
    <table class="table table-striped">
      <thead>
        <th><b>'.$_language->module['group_name'].'</b></th>
        <th><b>'.$_language->module['actions'].'</b></th>
        <th><b>'.$_language->module['sort'].'</b></th>
      </thead>';
      
		$n = 1;
        $CAPCLASS = new \webspell\Captcha;
        $CAPCLASS->createTransaction();
        $hash = $CAPCLASS->getHash();
        while ($ds = mysqli_fetch_array($ergebnis)) {
            
            $list = '<select name="sortlist[]">';
            $counter = mysqli_num_rows($ergebnis);
            for ($i = 1; $i <= $counter; $i++) {
                $list .= '<option value="' . $ds[ 'groupID' ] . '-' . $i . '">' . $i . '</option>';
            }
            $list .= '</select>';
            $list = str_replace(
                'value="' . $ds[ 'groupID' ] . '-' . $ds[ 'sort' ] . '"',
                'value="' . $ds[ 'groupID' ] . '-' . $ds[ 'sort' ] . '" selected="selected"',
                $list
            );
            echo '<tr>
        <td>'.$ds['name'].'</td>
        <td><a href="admincenter.php?site=gallery&amp;part=groups&amp;action=edit&amp;groupID='.$ds['groupID'].'" class="hidden-xs hidden-sm btn btn-warning btn-xs" type="button">' . $_language->module[ 'edit' ] . '</a>

        <input class="hidden-xs hidden-sm btn btn-danger btn-xs" type="button" onclick="MM_confirm(\'' . $_language->module['really_delete_group'] . '\', \'admincenter.php?site=gallery&amp;part=groups&amp;delete=true&amp;groupID='.$ds['groupID'].'&amp;captcha_hash='.$hash.'\')" value="' . $_language->module['delete'] . '" />

      <a href="admincenter.php?site=gallery&amp;part=groups&amp;action=edit&amp;groupID='.$ds['groupID'].'"  class="mobile visible-xs visible-sm" type="button"><i class="fa fa-pencil"></i></a>
      <a class="mobile visible-xs visible-sm" type="button" onclick="MM_confirm(\'' . $_language->module['really_delete_group'] . '\', \'admincenter.php?site=gallery&amp;part=groups&amp;delete=true&amp;groupID='.$ds['groupID'].'&amp;captcha_hash='.$hash.'\')" /><i class="fa fa-times"></i></a>


        </td>
        <td>'.$list.'</td>
		 	</tr>';
      $n++;
		}
		echo'<tr>
      <td class="td_head" colspan="3" align="right"><input type="hidden" name="captcha_hash" value="'.$hash.'" /><button class="btn btn-primary btn-xs" type="submit" name="sort" />'.$_language->module['to_sort'].'</button></td>
      </tr>
    </table>
    </form></div></div>';
    }
} elseif ($part == "gallerys") {
    if (isset($_POST[ 'save' ])) {
        $CAPCLASS = new \webspell\Captcha;
        if ($CAPCLASS->checkCaptcha(0, $_POST[ 'captcha_hash' ])) {
            if (checkforempty(array('name'))) {
                safe_query(
                    "INSERT INTO " . PREFIX . "gallery ( name, date, groupID )
                    values( '" . $_POST[ 'name' ] . "', '" . time() . "', '" . $_POST[ 'group' ] . "' ) "
                );
                $id = mysqli_insert_id($_database);
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
                if (!isset($_POST[ 'group' ])) {
                    $_POST[ 'group' ] = 0;
                }
                safe_query(
                    "UPDATE " . PREFIX . "gallery SET name='" . $_POST[ 'name' ] . "', groupID='" .
                    $_POST[ 'group' ] . "' WHERE galleryID='" . $_POST[ 'galleryID' ] . "'"
                );
            } else {
                echo $_language->module[ 'information_incomplete' ];
            }
        } else {
            echo $_language->module[ 'transaction_invalid' ];
        }
    } elseif (isset($_POST[ 'saveftp' ])) {
        $dir = '../images/gallery/';
        if (isset($_POST[ 'comment' ])) {
            $comment = $_POST[ 'comment' ];
        } else {
            $comment = array();
        }
        if (isset($_POST[ 'name' ])) {
            $name = $_POST[ 'name' ];
        } else {
            $name = array();
        }
        if (isset($_POST[ 'pictures' ])) {
            $pictures = $_POST[ 'pictures' ];
        } else {
            $pictures = array();
        }
        $i = 0;
        $CAPCLASS = new \webspell\Captcha;
        if ($CAPCLASS->checkCaptcha(0, $_POST[ 'captcha_hash' ])) {
            foreach ($pictures as $picture) {
                $typ = getimagesize($dir . $picture);
                switch ($typ[ 2 ]) {
                    case 1:
                        $typ = '.gif';
                        break;
                    case 2:
                        $typ = '.jpg';
                        break;
                    case 3:
                        $typ = '.png';
                        break;
                }
                if (isset($name[ $i ])) {
                    $insertname = $name[ $i ];
                } else {
                    $insertname = $picture;
                }
                safe_query(
                    "INSERT INTO " . PREFIX .
                    "gallery_pictures ( galleryID, name, comment, comments) VALUES ('" . $_POST[ 'galleryID' ] .
                    "', '" . $insertname . "', '" . $comment[ $i ] . "', '" . $_POST[ 'comments' ] . "' )"
                );
                $insertid = mysqli_insert_id($_database);
                copy($dir . $picture, $dir . 'large/' . $insertid . $typ);
                $galclass->saveThumb($dir . 'large/' . $insertid . $typ, $dir . 'thumb/' . $insertid . '.jpg');
                @unlink($dir . $picture);
                $i++;
            }
        } else {
            echo $_language->module[ 'transaction_invalid' ];
        }
    } elseif (isset($_POST[ 'saveform' ])) {
        $dir = '../images/gallery/';
        $picture = $_FILES[ 'picture' ];
        $CAPCLASS = new \webspell\Captcha;
        if ($CAPCLASS->checkCaptcha(0, $_POST[ 'captcha_hash' ])) {
            //TODO: should be loaded from root language folder
            $_language->readModule('formvalidation', true);

            $upload = new \webspell\HttpUpload('picture');
            if ($upload->hasFile()) {
                if ($upload->hasError() === false) {
                    $mime_types = array('image/jpeg', 'image/png', 'image/gif');
                    if ($upload->supportedMimeType($mime_types)) {
                        $imageInformation = getimagesize($upload->getTempFile());
                        if (is_array($imageInformation)) {
                            switch ($imageInformation[2]) {
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

                            if ($_POST[ 'name' ]) {
                                $insertname = $_POST[ 'name' ];
                            } else {
                                $insertname = $picture[ 'name' ];
                            }

                            safe_query(
                                "INSERT INTO " . PREFIX ."gallery_pictures (
                                    galleryID,
                                    name,
                                    comment,
                                    comments
                                ) VALUES (
                                    '" . $_POST[ 'galleryID' ] ."',
                                    '" . $insertname . "',
                                    '" . $_POST[ 'comment' ] . "',
                                    '" . $_POST[ 'comments' ] . "'
                                )"
                            );

                            $insertid = mysqli_insert_id($_database);

                            $filepath = $dir . 'large/';
                            $file = $insertid . $endung;

                            if ($upload->saveAs($filepath . $file, true)) {
                                @chmod($filepath . $file, $new_chmod);
                                $galclass->saveThumb($filepath . $file, $dir . 'thumb/' . $insertid . '.jpg');
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
			if(isset($error)) {
				if (count($errors)) {
					$errors = array_unique($errors);
					echo generateErrorBoxFromArray($_language->module['errors_there'], $errors);
				}
			}
        } else {
            echo $_language->module[ 'transaction_invalid' ];
        }
    } elseif (isset($_GET[ 'delete' ])) {
        //SQL
        $CAPCLASS = new \webspell\Captcha;
        if ($CAPCLASS->checkCaptcha(0, $_GET[ 'captcha_hash' ])) {
            if (safe_query("DELETE FROM " . PREFIX . "gallery WHERE galleryID='" . $_GET[ 'galleryID' ] . "'")) {
                //FILES
                $ergebnis = safe_query(
                    "SELECT picID FROM " . PREFIX . "gallery_pictures WHERE galleryID='" .
                    $_GET[ 'galleryID' ] . "'"
                );
                while ($ds = mysqli_fetch_array($ergebnis)) {
                    @unlink('../images/gallery/thumb/' . $ds[ 'picID' ] . '.jpg'); //thumbnails
                    $path = '../images/gallery/large/';
                    if (file_exists($path . $ds[ 'picID' ] . '.jpg')) {
                        $path = $path . $ds[ 'picID' ] . '.jpg';
                    } elseif (file_exists($path . $ds[ 'picID' ] . '.png')) {
                        $path = $path . $ds[ 'picID' ] . '.png';
                    } else {
                        $path = $path . $ds[ 'picID' ] . '.gif';
                    }
                    @unlink($path); //large
                    safe_query(
                        "DELETE FROM " . PREFIX . "comments WHERE parentID='" . $ds[ 'picID' ] .
                        "' AND type='ga'"
                    );
                }
                safe_query("DELETE FROM " . PREFIX . "gallery_pictures WHERE galleryID='" . $_GET[ 'galleryID' ] . "'");
            }
        } else {
            echo $_language->module[ 'transaction_invalid' ];
        }
    }

    if ($action == "add") {
        $ergebnis = safe_query("SELECT * FROM " . PREFIX . "gallery_groups");
        $any = mysqli_num_rows($ergebnis);
        if ($any) {
            $groups = '<select class="form-control" name="group">';
            while ($ds = mysqli_fetch_array($ergebnis)) {
                $groups .= '<option value="' . $ds[ 'groupID' ] . '">' . getinput($ds[ 'name' ]) . '</option>';
            }
            $groups .= '</select>';
            $CAPCLASS = new \webspell\Captcha;
            $CAPCLASS->createTransaction();
            $hash = $CAPCLASS->getHash();
	    
			echo'<div class="panel panel-default">
            <div class="panel-heading">
                            <i class="fa fa-file-image-o"></i> '.$_language->module['gallery'].'
                        </div>
                        <div class="panel-body">
            <a href="admincenter.php?site=gallery&amp;part=gallerys" class="white">'.$_language->module['galleries'].'</a> &raquo; '.$_language->module['add_gallery'].'<br><br>';
	    
	    echo'<form class="form-horizontal" method="post" action="admincenter.php?site=gallery&amp;part=gallerys&amp;action=upload">

        <div class="form-group">
    <label class="col-sm-2 control-label">'.$_language->module['gallery_name'].':</label>
    <div class="col-sm-8"><span class="text-muted small"><em>
      <input class="form-control" type="text" name="name" size="60" /></em></span>
    </div>
  </div>
    <div class="form-group">
    <label class="col-sm-2 control-label">'.$_language->module['group'].':</label>
    <div class="col-sm-8"><span class="text-muted small"><em>
      '.$groups.'</em></span>
    </div>
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">'.$_language->module['pic_upload'].':</label>
    <div class="col-sm-8"><span class="text-muted small"><em>
      <select class="form-control" name="upload">
              <option value="ftp">'.$_language->module['ftp'].'</option>
              <option value="form">'.$_language->module['formular'].'</option>
            </select></em></span>
    </div>
  </div>
  <div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
      <input type="hidden" name="captcha_hash" value="'.$hash.'" />
      <input class="btn btn-success btn-xs" type="submit" name="save" value="'.$_language->module['add_gallery'].'" />

    </div>
  </div>
   </form>
	    <br /><span class="text-muted small"><em>'.$_language->module['ftp_info'].' "http://'.$hp_url.'/images/gallery"</em></span></div></div>';
	} else {
            echo '<br />' . $_language->module[ 'need_group' ];
        }
    } elseif ($action == "edit") {
        $CAPCLASS = new \webspell\Captcha;
        $CAPCLASS->createTransaction();
        $hash = $CAPCLASS->getHash();
        $ergebnis = safe_query("SELECT * FROM " . PREFIX . "gallery_groups");
        $groups = '<select class="form-control" name="group">';
        while ($ds = mysqli_fetch_array($ergebnis)) {
            $groups .= '<option value="' . $ds[ 'groupID' ] . '">' . getinput($ds[ 'name' ]) . '</option>';
        }
        $groups .= '</select>';
        $ergebnis = safe_query("SELECT * FROM " . PREFIX . "gallery WHERE galleryID='" . $_GET[ 'galleryID' ] . "'");
        $ds = mysqli_fetch_array($ergebnis);
        $groups = str_replace(
            'value="' . $ds[ 'groupID' ] . '"',
            'value="' . $ds[ 'groupID' ] . '" selected="selected"',
            $groups
        );

		echo'<div class="panel panel-default">
         <div class="panel-heading">
                            <i class="fa fa-file-image-o"></i> '.$_language->module['gallery'].'
                        </div>
                        <div class="panel-body">
        <a href="admincenter.php?site=gallery&amp;part=gallerys" class="white">'.$_language->module['galleries'].'</a> &raquo; '.$_language->module['edit_gallery'].'<br><br>';
    
    echo'<form class="form-horizontal" method="post" action="admincenter.php?site=gallery&amp;part=gallerys">

    <div class="form-group">
    <label class="col-sm-2 control-label">'.$_language->module['gallery_name'].':</label>
    <div class="col-sm-8"><span class="text-muted small"><em>
      <input class="form-control" type="text" name="name" value="'.getinput($ds['name']).'" /></em></span>
    </div>
  </div>';
  if($ds['userID'] != 0) echo '
  <div class="form-group">
    <label class="col-sm-2 control-label">'.$_language->module['usergallery_of'].':</label>
    <div class="col-sm-8"><span class="text-muted small"><em>
      <a href="../index.php?site=profile&amp;id='.$userID.'" target="_blank">'.getnickname($ds['userID']).'</a></em></span>
    </div>
  </div>';
        else echo '
<div class="form-group">
    <label class="col-sm-2 control-label">'.$_language->module['group'].':</label>
    <div class="col-sm-8"><span class="text-muted small"><em>
      '.$groups.'</em></span>
    </div>
  </div>';
        echo'
        <div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
      <input type="hidden" name="captcha_hash" value="'.$hash.'" /><input type="hidden" name="galleryID" value="'.$ds['galleryID'].'" /></td>
        <td><input class="btn btn-success btn-xs" type="submit" name="saveedit" value="'.$_language->module['edit_gallery'].'" />
        </div>
  </div>

    </form></div></div>';
	   } elseif ($action == "upload") {
        echo '<div class="panel panel-default">
         <div class="panel-heading">
                            <i class="fa fa-file-image-o"></i> '.$_language->module['gallery'].'
                        </div>

                <div class="panel-body">
        <a href="admincenter.php?site=gallery&amp;part=gallerys" class="white">' .
            $_language->module[ 'galleries' ] . '</a> &raquo; ' . $_language->module[ 'upload' ] . '<br><br>';
        $dir = '../images/gallery/';
        if (isset($_POST[ 'upload' ])) {
            $upload_type = $_POST[ 'upload' ];
        } elseif (isset($_GET[ 'upload' ])) {
            $upload_type = $_GET[ 'upload' ];
        } else {
            $upload_type = null;
        }
        if (isset($_POST[ 'galleryID' ])) {
            $id = $_POST[ 'galleryID' ];
        } elseif (isset($_GET[ 'galleryID' ])) {
            $id = $_GET[ 'galleryID' ];
        }
        if ($upload_type == "ftp") {
            $CAPCLASS = new \webspell\Captcha;
            $CAPCLASS->createTransaction();
            $hash = $CAPCLASS->getHash();
            echo '<form method="post" action="admincenter.php?site=gallery&amp;part=gallerys">
              <table width="100%" border="0" cellspacing="1" cellpadding="3">
                <tr>
                  <td>';
            $pics = array();
            $picdir = opendir($dir);
            while (false !== ($file = readdir($picdir))) {
                if ($file != "." && $file != "..") {
                    if (is_file($dir . $file)) {
                        if ($info = getimagesize($dir . $file)) {
                            if ($info[ 2 ] == 1 || $info[ 2 ] == 2 || $info[ 2 ] == 3) {
                                $pics[ ] = $file;
                            }
                        }
                    }
                }
            }
            closedir($picdir);
            natcasesort($pics);
            reset($pics);
			
      		echo'<form method="post" action="admincenter.php?site=gallery&amp;part=gallerys">
		      <table width="100%" border="0" cellspacing="1" cellpadding="3">
		        <tr>
		          <td>';
		
			$pics = Array();
			$picdir = opendir($dir);
			while (false !== ($file = readdir($picdir))) {
				if ($file != "." && $file != "..") {
					if(is_file($dir.$file)) {
						if($info = getimagesize($dir.$file)) {
							if($info[2]==1 OR $info[2]==2 || $info[2]==3) $pics[] = $file;
						}
					}
				}
			}
			closedir($picdir);
			natcasesort ($pics);
			reset ($pics);
					
		    echo '<table class="table">
		        <tr>
		          <td></td>
		          <td><b>'.$_language->module['filename'].'</b></td>
		          <td><b>'.$_language->module['name'].'</b></td>
		          <td><b>'.$_language->module['comment'].'</b></td>
		        </tr>';
		
			foreach ($pics as $val) {
                if (is_file($dir . $val)) {
							
					echo '<tr>
		            <td><input type="checkbox" value="'.$val.'" name="pictures[]" checked="checked" /></td>
		            <td><a href="'.$dir.$val.'" target="_blank">'.$val.'</a></td>
		            <td><input type="text" name="name[]" size="40" /></td>
		            <td><input type="text" name="comment[]" size="40" /></td>
		          </tr>';
		
				}
			}

			echo '</table></td>
		          </tr>
		          <tr>
		            <td><br /><b>'.$_language->module['visitor_comments'].'</b> &nbsp;
		            <select name="comments">
		              <option value="0">'.$_language->module['disable_comments'].'</option>
		              <option value="1">'.$_language->module['enable_user_comments'].'</option>
		              <option value="2" selected="selected">'.$_language->module['enable_visitor_comments'].'</option>
		            </select></td>
		          </tr>
		          <tr>
		            <td><br /><input type="hidden" name="captcha_hash" value="'.$hash.'" /><input type="hidden" name="galleryID" value="'.$id.'" />
		            <input class="btn btn-primary btn-xs" type="submit" name="saveftp" value="'.$_language->module['upload'].'" /></td>
		          </tr>
		        </table>
		        </form></div></div>';
        
		} elseif ($upload_type == "form") {
            $CAPCLASS = new \webspell\Captcha;
            $CAPCLASS->createTransaction();
            $hash = $CAPCLASS->getHash();

			echo'<form method="post" action="admincenter.php?site=gallery&amp;part=gallerys" enctype="multipart/form-data">
			<table class="table">
        <tr>
          <td><b>'.$_language->module['name'].'</b></td>
          <td><input type="text" name="name" size="60" /></td>
        </tr>
        <tr>
          <td><b>'.$_language->module['comment'].'</b></td>
          <td><input type="text" name="comment" size="60" maxlength="255" /></td>
        </tr>
        <tr>
          <td><b>'.$_language->module['visitor_comments'].'</b></td>
          <td><select name="comments">
            <option value="0">'.$_language->module['disable_comments'].'</option>
            <option value="1">'.$_language->module['enable_user_comments'].'</option>
            <option value="2" selected="selected">'.$_language->module['enable_visitor_comments'].'</option>
          </select></td>
        </tr>
        <tr>
          <td><b>'.$_language->module['picture'].'</b></td>
          <td><input name="picture" type="file" size="40" /></td>
        </tr>
        <tr>
          <td><input type="hidden" name="captcha_hash" value="'.$hash.'" /><input type="hidden" name="galleryID" value="'.$id.'" /></td>
          <td><input class="btn btn-primary btn-xs" type="submit" name="saveform" value="'.$_language->module['upload'].'" /></td>
        </tr>
      </table>
      </form></div></div>';
		}
	}
  
	else {
		echo'<div class="panel panel-default">
        <div class="panel-heading">
                            <i class="fa fa-file-image-o"></i> '.$_language->module['gallery'].'
                        </div>
                        <div class="panel-body">
		
        '.$_language->module['galleries'].'<br><br>';
    
    echo'<a href="admincenter.php?site=gallery&amp;part=gallerys&amp;action=add" class="btn btn-primary btn-xs" type="button">' . $_language->module[ 'new_gallery' ] . '</a><br /><br />';

		echo'<form method="post" name="ws_gallery" action="admincenter.php?site=gallery&amp;part=gallerys">
		<table class="table table-striped">
      <thead>
        <th><b>'.$_language->module['gallery_name'].'</b></th>
         <th></th>
        <th align="center"><b>'.$_language->module['actions'].'</b></th>
      </thead>';

		$ergebnis = safe_query("SELECT * FROM " . PREFIX . "gallery_groups ORDER BY sort");
        while ($ds = mysqli_fetch_array($ergebnis)) {
            echo '<tr>
      <td class="td_head" colspan="3"><b>' . getinput($ds[ 'name' ]) . '</b></td>
    </tr>';
            $galleries = safe_query(
                "SELECT * FROM " . PREFIX .
                "gallery WHERE groupID='$ds[groupID]' AND userID='0' ORDER BY date"
            );
            $CAPCLASS = new \webspell\Captcha;
            $CAPCLASS->createTransaction();
            $hash = $CAPCLASS->getHash();
            $i = 1;
            while ($db = mysqli_fetch_array($galleries)) {
                
                echo '<tr>
          <td><a href="../index.php?site=gallery&amp;galleryID='.$db['galleryID'].'" target="_blank">'.getinput($db['name']).'</a></td>
          <td align="center">

          
          <a href="admincenter.php?site=gallery&amp;part=gallerys&amp;action=upload&amp;upload=form&amp;galleryID='.$db['galleryID'].'" class="btn btn-primary btn-xs" type="button">'.$_language->module['add_img'].' ('.$_language->module['per_form'].')</a>


         <a href="admincenter.php?site=gallery&amp;part=gallerys&amp;action=upload&amp;upload=ftp&amp;galleryID='.$db['galleryID'].'" class="btn btn-primary btn-xs" type="button">'.$_language->module['add_img'].' ('.$_language->module['per_ftp'].')</a>   

  </td>
          <td><a href="admincenter.php?site=gallery&amp;part=gallerys&amp;action=edit&amp;galleryID='.$db['galleryID'].'" class="hidden-xs hidden-sm btn btn-warning btn-xs" type="button">' . $_language->module[ 'edit' ] . '</a>

        <input class="hidden-xs hidden-sm btn btn-danger btn-xs" type="button" onclick="MM_confirm(\'' . $_language->module['really_delete_gallery'] . '\', \'admincenter.php?site=gallery&amp;part=gallerys&amp;delete=true&amp;galleryID='.$db['galleryID'].'&amp;captcha_hash='.$hash.'\')" value="' . $_language->module['delete'] . '" />

      <a href="admincenter.php?site=gallery&amp;part=gallerys&amp;action=edit&amp;galleryID='.$db['galleryID'].'"  class="mobile visible-xs visible-sm" type="button"><i class="fa fa-pencil"></i></a>
      <a class="mobile visible-xs visible-sm" type="button" onclick="MM_confirm(\'' . $_language->module['really_delete_gallery'] . '\', \'admincenter.php?site=gallery&amp;part=gallerys&amp;delete=true&amp;galleryID='.$db['galleryID'].'&amp;captcha_hash='.$hash.'\')" /><i class="fa fa-times"></i></a>



          </td>
        </tr>';
      
      $i++;
		  }
    }
		echo'</table></form></div></div><br />';
    
    echo'<div class="panel panel-default">
    <div class="panel-heading">
                            <i class="fa fa-file-image-o"></i> '.$_language->module['gallery'].'
                        </div>
                        <div class="panel-body">
    '.$_language->module['usergalleries'].'</br><br>';

		$ergebnis=safe_query("SELECT * FROM ".PREFIX."gallery WHERE userID!='0'");
		
    echo'<form method="post" name="ws_gallery" action="admincenter.php?site=gallery&amp;part=gallerys">
    <table class="table table-striped">
      <thead>
        <th><b>'.$_language->module['gallery_name'].'</b></th>
        <th><b>'.$_language->module['usergallery_of'].'</b></th>
        <th><b>'.$_language->module['actions'].'</b></th>
      </thead>';
      
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
            echo '<tr>
        <td><a href="../index.php?site=gallery&amp;galleryID='.$ds['galleryID'].'" target="_blank">'.getinput($ds['name']).'</a></td>
        <td><a href="../index.php?site=profile&amp;id='.$userID.'" target="_blank">'.getnickname($ds['userID']).'</a></td>
        <td><a href="admincenter.php?site=gallery&amp;part=gallerys&amp;action=edit&amp;galleryID='.$ds['galleryID'].'" class="hidden-xs hidden-sm btn btn-warning btn-xs" type="button">' . $_language->module[ 'edit' ] . '</a>

        <input class="hidden-xs hidden-sm btn btn-danger btn-xs" type="button" onclick="MM_confirm(\'' . $_language->module['really_delete_gallery'] . '\', \'admincenter.php?site=gallery&amp;part=gallerys&amp;delete=true&amp;galleryID='.$ds['galleryID'].'&amp;captcha_hash='.$hash.'\')" value="' . $_language->module['delete'] . '" />

	  <a href="admincenter.php?site=gallery&amp;part=gallerys&amp;action=edit&amp;galleryID='.$ds['galleryID'].'"  class="mobile visible-xs visible-sm" type="button"><i class="fa fa-pencil"></i></a>
      <a class="mobile visible-xs visible-sm" type="button" onclick="MM_confirm(\'' . $_language->module['really_delete_gallery'] . '\', \'admincenter.php?site=gallery&amp;part=gallerys&amp;delete=true&amp;galleryID='.$ds['galleryID'].'&amp;captcha_hash='.$hash.'\')" /><i class="fa fa-times"></i></a>
        </td>
      </tr>';
      
      $i++;
		}
		echo'</table></form>';
	}
}
echo '</div></div>';
?>