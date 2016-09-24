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
 
$_language->readModule('carousel', false, true);
 
if (!ispageadmin($userID) || mb_substr(basename($_SERVER[ 'REQUEST_URI' ]), 0, 15) != "admincenter.php") {
    die($_language->module[ 'access_denied' ]);
}
 
$filepath = "../images/carousel/";
 
if (isset($_GET[ 'action' ])) {
    $action = $_GET[ 'action' ];
} else {
    $action = '';
}
 
if ($action == "add") {
    echo '<div class="panel panel-default">
    <div class="panel-heading">
                            <i class="fa fa-object-group"></i> ' . $_language->module[ 'carousel' ] . '
                        </div>
                        <div class="panel-body">
    <a href="admincenter.php?site=carousel" class="white">' . $_language->module[ 'carousel' ] .
    '</a> &raquo; ' . $_language->module[ 'add_carousel' ] . '<br><br>';
 
    $CAPCLASS = new \webspell\Captcha;
    $CAPCLASS->createTransaction();
    $hash = $CAPCLASS->getHash();
 
    echo'<form class="form-horizontal" method="post" action="admincenter.php?site=carousel" enctype="multipart/form-data">
   <div class="form-group">
    <label class="col-sm-2 control-label">'.$_language->module['carousel_pic'].':</label>
    <div class="col-sm-8"><span class="text-muted small"><em>
    <input name="carousel_pic" type="file" size="40" /> <small>(' . $_language->module[ 'carousel_upload_info' ] . ')</small></em></span>
    </div>
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">'.$_language->module['title'].':</label>
    <div class="col-sm-8"><span class="text-muted small"><em>
      <input class="form-control" type="text" name="title" size="60" maxlength="255" /></em></span>
    </div>
  </div>
   <div class="form-group">
    <label class="col-sm-2 control-label">'.$_language->module['carousel_link'].':</label>
    <div class="col-sm-8"><span class="text-muted small"><em>
      <input class="form-control" type="text" name="link" size="60" maxlength="255" /></em></span>
    </div>
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">'.$_language->module['description'].':</label>
    <div class="col-sm-8"><span class="text-muted small"><em>
      <textarea class="form-control" id="description" rows="5" cols="" name="description" style="width: 100%;"></textarea></em></span>
    </div>
  </div>
<div class="form-group">
    <label class="col-sm-2 control-label">'.$_language->module['is_displayed'].':</label>
    <div class="col-sm-8"><span class="text-muted small"><em>
      <input type="checkbox" name="displayed" value="1" checked="checked" /></em></span>
    </div>
  </div>
  <div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
        <input type="hidden" name="captcha_hash" value="'.$hash.'" />
        <button class="btn btn-success btn-xs" type="submit" name="save"  />'.$_language->module['add_carousel'].'</button>
    </div>
  </div>
</form>
</div></div>';
} elseif ($action == "edit") {
    echo '<div class="panel panel-default">
    <div class="panel-heading">
                            <i class="fa fa-object-group"></i> ' . $_language->module[ 'carousel' ] . '
                        </div>
                        <div class="panel-body">
    <a href="admincenter.php?site=carousel" class="white">' . $_language->module[ 'carousel' ] .
    '</a> &raquo; ' . $_language->module[ 'edit_carousel' ] . '<br><br>';
 
    $ds = mysqli_fetch_array(
        safe_query(
            "SELECT * FROM " . PREFIX . "carousel WHERE carouselID='" . intval($_GET['carouselID']) ."'"
        )
    );
    if (!empty($ds[ 'carousel_pic' ])) {
        $pic = '<img class="img-thumbnail" style="width: 100%; max-width: 600px" src="' . $filepath . $ds[ 'carousel_pic' ] . '" alt="">';
    } else {
        $pic = $_language->module[ 'no_upload' ];
    }
 
    if ($ds[ 'displayed' ] == 1) {
        $displayed = '<input type="checkbox" name="displayed" value="1" checked="checked" />';
    } else {
        $displayed = '<input type="checkbox" name="displayed" value="1" />';
    }
 
    $CAPCLASS = new \webspell\Captcha;
    $CAPCLASS->createTransaction();
    $hash = $CAPCLASS->getHash();
 
    echo '<form class="form-horizontal" method="post" action="admincenter.php?site=carousel" enctype="multipart/form-data">
<input type="hidden" name="carouselID" value="' . $ds['carouselID'] . '" />
<div class="form-group">
    <label class="col-sm-2 control-label">'.$_language->module['current_pic'].':</label>
    <div class="col-sm-8"><span class="text-muted small"><em>'.$pic.'</em></span>
    </div>
  </div>
<div class="form-group">
    <label class="col-sm-2 control-label">'.$_language->module['carousel_upload_info'].':</label>
    <div class="col-sm-8"><span class="text-muted small"><em>
      <input name="carousel_pic" type="file" size="40" /></em></span>
    </div>
  </div>
<div class="form-group">
    <label class="col-sm-2 control-label">'.$_language->module['title'].':</label>
    <div class="col-sm-8"><span class="text-muted small"><em>
      <input class="form-control" type="text" name="title" size="60" maxlength="255" value="' . getinput($ds[ 'title' ]) . '" /></em></span>
    </div>
  </div>
<div class="form-group">
    <label class="col-sm-2 control-label">'.$_language->module['carousel_link'].':</label>
    <div class="col-sm-8"><span class="text-muted small"><em>
      <input class="form-control" type="text" name="link" size="60" value="' . getinput($ds[ 'link' ]) . '" /></em></span>
    </div>
  </div>
<div class="form-group">
    <label class="col-sm-2 control-label">'.$_language->module['description'].':</label>
    <div class="col-sm-8"><span class="text-muted small"><em>
      <textarea class="form-control" id="description" rows="5" cols="" name="description" style="width: 100%;">' . getinput($ds[ 'description' ]) .
        '</textarea></em></span>
    </div>
  </div>
<div class="form-group">
    <label class="col-sm-2 control-label">'.$_language->module['is_displayed'].':</label>
    <div class="col-sm-8"><span class="text-muted small"><em>
      <p class="form-control-static">'.$displayed.'</p></em></span>
    </div>
  </div>
<div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
        <input type="hidden" name="captcha_hash" value="'.$hash.'" />
        <button class="btn btn-success btn-xs" type="submit" name="saveedit"  />'.$_language->module['edit_carousel'].'</button>
    </div>
  </div>
</form>
</div></div>';
} elseif (isset($_POST[ 'sortieren' ])) {
    $CAPCLASS = new \webspell\Captcha;
    if ($CAPCLASS->checkCaptcha(0, $_POST[ 'captcha_hash' ])) {
        $sort = $_POST[ 'sort' ];
        if (is_array($sort)) {
            foreach ($sort as $sortstring) {
                $sorter = explode("-", $sortstring);
                safe_query("UPDATE " . PREFIX . "carousel SET sort='$sorter[1]' WHERE carouselID='$sorter[0]' ");
                redirect("admincenter.php?site=carousel", "", 0);
            }
        }
    } else {
        echo $_language->module[ 'transaction_invalid' ];
    }
} elseif (isset($_POST[ "save" ])) {
    $title = $_POST[ 'title' ];
    $link = $_POST[ 'link' ];
    $description = $_POST[ 'description' ];
    if (isset($_POST[ 'displayed' ])) {
        $displayed = 1;
    } else {
        $displayed = 0;
    }
    if (!$displayed) {
        $displayed = 0;
    }
 
    $CAPCLASS = new \webspell\Captcha;
    if ($CAPCLASS->checkCaptcha(0, $_POST[ 'captcha_hash' ])) {
    safe_query("INSERT INTO `".PREFIX."carousel` (title, link, description, displayed, sort) values ('".$title."', '".$link."', '".$description."', '".intval($displayed)."','1')");
               
        $id = mysqli_insert_id($_database);
 
        $errors = array();
 
        $upload = new \webspell\HttpUpload('carousel_pic');
        if ($upload->hasFile()) {
            if ($upload->hasError() === false) {
                $mime_types = array('image/jpeg','image/png','image/gif');
 
                if ($upload->supportedMimeType($mime_types)) {
                    $imageInformation =  getimagesize($upload->getTempFile());
 
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
                        $file = $id.$endung;
 
                        if ($upload->saveAs($filepath.$file, true)) {
                            @chmod($file, $new_chmod);
                            safe_query(
                                "UPDATE " . PREFIX . "carousel SET carousel_pic='" . $file . "' WHERE carouselID='" . $id . "'"
                            );
                        }
                    } else {
                        $errors[] = $_language->module[ 'broken_image' ];
                    }
                } else {
                    $errors[] = $_language->module[ 'unsupported_image_type' ];
                }
            } else {
                $errors[] = $upload->translateError();
            }
        }
        if (count($errors)) {
            $errors = array_unique($errors);
            echo generateErrorBoxFromArray($_language->module['errors_there'], $errors);
        } else {
            redirect("admincenter.php?site=carousel", "", 0);
        }
    } else {
        echo $_language->module[ 'transaction_invalid' ];
    }
} elseif (isset($_POST[ "saveedit" ])) {
    $title = $_POST[ "title" ];
    $link = $_POST[ "link" ];
    $description = $_POST[ "description" ];
    if (isset($_POST[ "displayed" ])) {
        $displayed = 1;
    } else {
        $displayed = 0;
    }
    $CAPCLASS = new \webspell\Captcha;
    if ($CAPCLASS->checkCaptcha(0, $_POST[ 'captcha_hash' ])) {
        if (stristr($link, 'http://')) {
            $link = $link;
        } else {
            $link = 'http://' . $link;
        }
 
        safe_query(
            "UPDATE " . PREFIX . "carousel SET title='" . $title . "', link='" . $link . "', description='" . $description .
            "', displayed='" . $displayed . "' WHERE carouselID='" .
            $_POST[ "carouselID" ] . "'"
        );
 
        $id = $_POST[ 'carouselID' ];
 
        $errors = array();
 
        $upload = new \webspell\HttpUpload('carousel_pic');
        if ($upload->hasFile()) {
            if ($upload->hasError() === false) {
                $mime_types = array('image/jpeg','image/png','image/gif');
 
                if ($upload->supportedMimeType($mime_types)) {
                    $imageInformation =  getimagesize($upload->getTempFile());
 
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
                        $file = $id.$endung;
 
                        if ($upload->saveAs($filepath.$file, true)) {
                            @chmod($file, $new_chmod);
                            safe_query(
                                "UPDATE " . PREFIX . "carousel SET carousel_pic='" . $file . "' WHERE carouselID='" . $id . "'"
                            );
                        }
                    } else {
                        $errors[] = $_language->module[ 'broken_image' ];
                    }
                } else {
                    $errors[] = $_language->module[ 'unsupported_image_type' ];
                }
            } else {
                $errors[] = $upload->translateError();
            }
        }
        if (count($errors)) {
            $errors = array_unique($errors);
            echo generateErrorBoxFromArray($_language->module['errors_there'], $errors);
        } else {
            redirect("admincenter.php?site=carousel", "", 0);
        }
    } else {
        echo $_language->module[ 'transaction_invalid' ];
    }
} elseif (isset($_GET[ "delete" ])) {
    $CAPCLASS = new \webspell\Captcha;
    if ($CAPCLASS->checkCaptcha(0, $_GET[ 'captcha_hash' ])) {
        $get = safe_query("SELECT * FROM " . PREFIX . "carousel WHERE carouselID='" . $_GET[ "carouselID" ] . "'");
        $data = mysqli_fetch_assoc($get);
 
        if (safe_query("DELETE FROM " . PREFIX . "carousel WHERE carouselID='" . $_GET[ "carouselID" ] . "'")) {
            @unlink($filepath.$data['carousel_pic']);
            redirect("admincenter.php?site=carousel", "", 0);
        } else {
            redirect("admincenter.php?site=carousel", "", 0);
        }
    } else {
        echo $_language->module[ 'transaction_invalid' ];
    }
} else {
    echo '<div class="panel panel-default">
    <div class="panel-heading">
                            <i class="fa fa-object-group"></i> ' . $_language->module[ 'carousel' ] . '
                        </div>
                        <div class="panel-body">';
 
    echo '<a href="admincenter.php?site=carousel&amp;action=add" class="btn btn-primary btn-xs" type="button">' . $_language->module[ 'new_carousel' ] . '</a><br /><br />';
 
    echo '<form method="post" action="admincenter.php?site=carousel">
    <table class="table table-striped">
    <thead>
      <th><b>'.$_language->module['title'].'</b></th>
      <th><b>'.$_language->module['carousel'].'</b></th>
      <th class="hidden-xs hidden-sm"><b>'.$_language->module['is_displayed'].'</b></th>
      <th><b>'.$_language->module['actions'].'</b></th>
      <th><b>'.$_language->module['sort'].'</b></th>
    </thead>';

   $CAPCLASS = new \webspell\Captcha;
    $CAPCLASS->createTransaction();
    $hash = $CAPCLASS->getHash();
 
    $qry = safe_query("SELECT * FROM " . PREFIX . "carousel ORDER BY sort");
    $anz = mysqli_num_rows($qry);
    if ($anz) {
        $i = 1;
        while ($ds = mysqli_fetch_array($qry)) {
            if ($i % 2) {
                $td = 'td1';
            } else {
                $td = 'td2';
            }
 
            $ds[ 'displayed' ] == 1 ?
            $displayed = '<font color="green"><b>' . $_language->module[ 'yes' ] . '</b></font>' :
            $displayed = '<font color="red"><b>' . $_language->module[ 'no' ] . '</b></font>';
           
            if (stristr($ds[ 'link' ], 'http://')) {
                $title = '<a href="' . getinput($ds[ 'link' ]) . '" target="_blank">' . getinput($ds[ 'title' ]) . '</a>';
            } else {
                $title = '<a href="http://' . getinput($ds[ 'link' ]) . '" target="_blank">' . getinput($ds[ 'title' ]) .
                '</a>';
            }
 
            echo '<tr>
           <td class="' . $td . '">' . $title . '</td>
           <td class="' . $td . '"><img class="img-thumbnail" style="width: 100%; max-width: 350px" align="center" src="../images/carousel/' . $ds[ 'carousel_pic' ] . '" alt="{img}" /></td>
           <td class="' . $td . '" align="center">' . $displayed . '</td>
           <td class="' . $td . '" align="center"><a href="admincenter.php?site=carousel&amp;action=edit&amp;carouselID=' . $ds[ 'carouselID' ] .
                '" class="hidden-xs hidden-sm btn btn-warning btn-xs" type="button">' . $_language->module[ 'edit' ] . '</a>

        <input class="hidden-xs hidden-sm btn btn-danger btn-xs" type="button" onclick="MM_confirm(\'' . $_language->module['really_delete'] . '\', \'admincenter.php?site=carousel&amp;delete=true&amp;carouselID=' . $ds[ 'carouselID' ] .
                    '&amp;captcha_hash=' . $hash . '\')" value="' . $_language->module['delete'] . '" />

      
      <a href="admincenter.php?site=carousel&amp;action=edit&amp;carouselID=' . $ds[ 'carouselID' ] . '"  class="mobile visible-xs visible-sm" type="button"><i class="fa fa-pencil"></i></a>
      <a class="mobile visible-xs visible-sm" type="button" onclick="MM_confirm(\'' . $_language->module['really_delete'] . '\', \'admincenter.php?site=carousel&amp;delete=true&amp;carouselID=' . $ds[ 'carouselID' ] .
                    '&amp;captcha_hash=' . $hash . '\')" /><i class="fa fa-times"></i></a></td>
<td class="' . $td . '" align="center"><select name="sort[]">';
            for ($j = 1; $j <= $anz; $j++) {
                if ($ds[ 'sort' ] == $j) {
                    echo '<option value="' . $ds[ 'carouselID' ] . '-' . $j . '" selected="selected">' . $j .
                        '</option>';
                } else {
                    echo '<option value="' . $ds[ 'carouselID' ] . '-' . $j . '">' . $j . '</option>';
                }
            }
            echo '</select>
</td>
</tr>';
            $i++;
        }
    } else {
        echo '<tr><td class="td1" colspan="6">' . $_language->module[ 'no_entries' ] . '</td></tr>';
    }
 
    echo '<tr>
<td class="td_head" colspan="6" align="right"><input type="hidden" name="captcha_hash" value="' . $hash .
    '"><input class="btn btn-primary btn-xs" type="submit" name="sortieren" value="' . $_language->module[ 'to_sort' ] . '" /></td>
</tr>
</table>
</form></div></div>';
}

    ?>