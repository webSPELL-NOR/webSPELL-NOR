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

$_language->readModule('bannerrotation', false, true);

if (!ispageadmin($userID) || mb_substr(basename($_SERVER['REQUEST_URI']), 0, 15) != "admincenter.php") {
    die($_language->module['access_denied']);
}

$filepath = "../images/bannerrotation/";

if (isset($_GET['action'])) {
    $action = $_GET['action'];
} else {
    $action = '';
}

if ($action == "add") {
    echo '<div class="panel panel-default">
    <div class="panel-heading">
                            <i class="fa fa-repeat"></i> '.$_language->module['bannerrotation'].'
                        </div>
                        <div class="panel-body">
    <a href="admincenter.php?site=bannerrotation" class="white">' .
    $_language->module['bannerrotation'] . '</a> &raquo; ' . $_language->module['add_banner'] . '<br><br>';

    $CAPCLASS = new \webspell\Captcha;
    $CAPCLASS->createTransaction();
    $hash = $CAPCLASS->getHash();
	
  echo'<form class="form-horizontal" method="post" action="admincenter.php?site=bannerrotation" enctype="multipart/form-data">
  <div class="row">

<div class="col-md-6">

  
  <div class="form-group">
    <label class="col-sm-4 control-label">'.$_language->module['banner_name'].':</label>
    <div class="col-sm-8"><span class="text-muted small"><em>
      <input class="form-control" type="text" name="bannername" maxlength="255" /></em></span>
    </div>
  </div>
  <div class="form-group">
    <label class="col-sm-4 control-label">'.$_language->module['banner_url'].':</label>
    <div class="col-sm-8"><span class="text-muted small"><em>
      <input type="text" class="form-control" name="bannerurl" size="60" value="http://" /></em></span>
    </div>
  </div>

  </div>

<div class="col-md-6">
<div class="form-group">
    <label class="col-sm-4 control-label">'.$_language->module['banner_upload'].':</label>
    <div class="col-sm-8"><span class="text-muted small"><em>
      <input name="banner" type="file" size="40" /></em></span>
    </div>
  </div>
</div>

<div class="col-md-12">

  <div class="form-group">
    <label class="col-sm-2 control-label">'.$_language->module['is_displayed'].':</label>
    <div class="col-sm-8"><span class="text-muted small"><em>
      <input type="checkbox" name="displayed" value="1" checked="checked" /></em></span>
    </div>
  </div>
  <div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
		<input type="hidden" name="captcha_hash" value="'.$hash.'" />
		<button class="btn btn-success btn-xs" type="submit" name="save"  />'.$_language->module['add_banner'].'</button>
    </div>
  </div>
  </div>
  </form></div>
  </div>';
} elseif($action=="edit") {

  echo'<div class="panel panel-default">
  <div class="panel-heading">
                            <i class="fa fa-repeat"></i> '.$_language->module['bannerrotation'].'
                        </div>
                        <div class="panel-body">
  <a href="admincenter.php?site=bannerrotation" class="white">'.$_language->module['bannerrotation'].'</a> &raquo; '.$_language->module['edit_banner'].'<br><br>';

	$ds = mysqli_fetch_array(
        safe_query(
            "SELECT
                *
            FROM
                " . PREFIX . "bannerrotation
            WHERE
                bannerID='" . (int) $_GET["bannerID"] . "'"
        )
    );
    if (file_exists($filepath . $ds['banner'])) {
        $pic = '<img src="' . $filepath . $ds['banner'] . '" alt="' . $ds['banner'] . '">';
    } else {
        $pic = $_language->module['no_upload'];
    }

    if ($ds['displayed'] == '1') {
        $displayed = '<input type="checkbox" name="displayed" value="1" checked="checked">';
    } else {
        $displayed = '<input type="checkbox" name="displayed" value="1">';
    }

    $CAPCLASS = new \webspell\Captcha;
    $CAPCLASS->createTransaction();
    $hash = $CAPCLASS->getHash();

  echo'<form class="form-horizontal" method="post" action="admincenter.php?site=bannerrotation" enctype="multipart/form-data">
  <input type="hidden" name="bannerID" value="'.$ds['bannerID'].'" />

  <div class="row">

<div class="col-md-6">

   <div class="form-group">
    <label class="col-sm-4 control-label">'.$_language->module['banner_name'].':</label>
    <div class="col-sm-8"><span class="text-muted small"><em>
      <input type="text" class="form-control" name="bannername" size="60" maxlength="255" value="'.getinput($ds['bannername']).'" /></em></span>
    </div>
  </div>
  <div class="form-group">
    <label class="col-sm-4 control-label">'.$_language->module['banner_url'].':</label>
    <div class="col-sm-8"><span class="text-muted small"><em>
      <input type="text" class="form-control" name="bannerurl" value="'.getinput($ds['bannerurl']).'" /></em></span>
    </div>
  </div>


  </div>

<div class="col-md-6">

 <div class="form-group">
    <label class="col-sm-3 control-label">'.$_language->module['present_banner'].':</label>
    <div class="col-sm-8"><span class="text-muted small"><em>
      '.$pic.'</em></span>
    </div>
  </div>
    <div class="form-group">
    <label class="col-sm-3 control-label">'.$_language->module['banner_upload'].':</label>
    <div class="col-sm-8"><span class="text-muted small"><em>
      <input name="banner" type="file" size="40" /></em></span>
    </div>
  </div>

  </div>

   
<div class="col-md-12">
  <div class="form-group">
    <label class="col-sm-2 control-label">'.$_language->module['is_displayed'].':</label>
    <div class="col-sm-8"><span class="text-muted small"><em>
      '.$displayed.'</em></span>
    </div>
  </div>
  <div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
		<input type="hidden" name="captcha_hash" value="'.$hash.'" />
		<button class="btn btn-success btn-xs" type="submit" name="saveedit"  />'.$_language->module['edit_banner'].'</button>
    </div>
  </div>

  </div>
  </form></div>
  </div>';
} elseif (isset($_POST["save"])) {
    $bannername = $_POST["bannername"];
    $bannerurl = $_POST["bannerurl"];
    if (isset($_POST["displayed"])) {
        $displayed = 1;
    } else {
        $displayed = 0;
    }

    //TODO: should be loaded from root language folder
    $_language->readModule('formvalidation', true);

    $upload = new \webspell\HttpUpload('banner');

    $CAPCLASS = new \webspell\Captcha;
    if ($CAPCLASS->checkCaptcha(0, $_POST['captcha_hash'])) {
        if ($bannername && $bannerurl) {
            if (!isWebURLorProtocolRelative($bannerurl)) {
                $bannerurl = 'http://' . $bannerurl;
            }

            safe_query(
                "INSERT INTO
                        `" . PREFIX . "bannerrotation` (
                            `bannerID`,
                            `bannername`,
                            `bannerurl`,
                            `displayed`,
                            `date`
                        )
                        values(
                            '',
                            '" . $bannername . "',
                            '" . $bannerurl . "',
                            '" . $displayed . "',
                            '" . time() . "'
                        )"
            );

            $id = mysqli_insert_id($_database);

            $errors = array();

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
                            $file = $id . $endung;

                            if ($upload->saveAs($filepath . $file, true)) {
                                @chmod($filepath . $file, $new_chmod);
                                safe_query(
                                    "UPDATE
                                        `" . PREFIX . "bannerrotation`
                                    SET
                                        `banner` = '" . $file . "'
                                    WHERE
                                        `bannerID` = '" . $id . "'"
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
                echo generateErrorBoxFromArray($_language->module['errors_there'], $errors);
            } else {
                redirect("admincenter.php?site=bannerrotation", "", 0);
            }
        } else {
            echo generateErrorBox($_language->module['fill_correctly']);
        }
    } else {
        echo generateErrorBox($_language->module['transaction_invalid']);
    }
} elseif (isset($_POST["saveedit"])) {
    $bannername = $_POST["bannername"];
    $bannerurl = $_POST["bannerurl"];
    if (isset($_POST["displayed"])) {
        $displayed = 1;
    } else {
        $displayed = 0;
    }
    $CAPCLASS = new \webspell\Captcha;
    if ($CAPCLASS->checkCaptcha(0, $_POST['captcha_hash'])) {
        if ($bannername && $bannerurl) {
            if (!isWebURLorProtocolRelative($bannerurl)) {
                $bannerurl = 'http://' . $bannerurl;
            }
            safe_query(
                "UPDATE
                            `" . PREFIX . "bannerrotation`
                        SET
                            `bannername` = '" . $bannername . "',
                            `bannerurl` = '" . $bannerurl . "',
                            `displayed` = '" . $displayed . "'
                        WHERE
                            `bannerID` = '" . (int) $_POST["bannerID"] . "'"
            );

            $errors = array();
            //TODO: should be loaded from root language folder
            $_language->readModule('formvalidation', true);

            $upload = new \webspell\HttpUpload('banner');

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
                            $file = (int) $_POST["bannerID"] . $endung;

                            if ($upload->saveAs($filepath . $file, true)) {
                                @chmod($filepath . $file, $new_chmod);
                                safe_query(
                                    "UPDATE
                                        `" . PREFIX . "bannerrotation`
                                    SET
                                        `banner` = '" . $file . "'
                                    WHERE
                                        `bannerID` = '" . (int) $_POST["bannerID"] . "'"
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
                echo generateErrorBoxFromArray($_language->module['errors_there'], $errors);
            } else {
                redirect("admincenter.php?site=bannerrotation", "", 0);
            }
        } else {
            echo generateErrorBox($_language->module['fill_correctly']);
        }
    } else {
        echo generateErrorBox($_language->module['transaction_invalid']);
    }
} elseif (isset($_GET["delete"])) {
    $CAPCLASS = new \webspell\Captcha;
    if ($CAPCLASS->checkCaptcha(0, $_GET['captcha_hash'])) {
        if (safe_query(
            "DELETE FROM
                `" . PREFIX . "bannerrotation`
                WHERE
                `bannerID` = '" . (int) $_GET["bannerID"] . "'"
        )
        ) {
            if (file_exists($filepath . $_GET["bannerID"] . '.jpg')) {
                unlink($filepath . $_GET["bannerID"] . '.jpg');
            }
            if (file_exists($filepath . $_GET["bannerID"] . '.gif')) {
                unlink($filepath . $_GET["bannerID"] . '.gif');
            }
            if (file_exists($filepath . $_GET["bannerID"] . '.png')) {
                unlink($filepath . $_GET["bannerID"] . '.png');
            }
            redirect("admincenter.php?site=bannerrotation", "", 0);
        } else {
            redirect("admincenter.php?site=bannerrotation", "", 0);
        }
    } else {
        echo $_language->module['transaction_invalid'];
    }
} else {

  echo'<div class="panel panel-default">
  <div class="panel-heading">
                            <i class="fa fa-repeat"></i> '.$_language->module['bannerrotation'].'
                        </div>
        <div class="panel-body">';
  
  echo'<a href="admincenter.php?site=bannerrotation&amp;action=add" class="btn btn-primary btn-xs" type="button">' . $_language->module[ 'new_banner' ] . '</a><br /><br />';
  
  echo'<form method="post" action="admincenter.php?site=bannerrotation">
  <table class="table table-striped">
    <thead>
      <th><b>'.$_language->module['banner'].'</b></th>
      <th><b>'.$_language->module['banner_url'].'</b></th>
      <th><b>'.$_language->module['clicks'].'</b></th>
      <th class="hidden-xs hidden-sm"><b>'.$_language->module['is_displayed'].'</b></th>
      <th><b>'.$_language->module['actions'].'</b></th>
    </thead>';
  
  $CAPCLASS = new \webspell\Captcha;
    $CAPCLASS->createTransaction();
    $hash = $CAPCLASS->getHash();

    $qry = safe_query("SELECT * FROM `" . PREFIX . "bannerrotation` ORDER BY `bannerID`");
    $anz = mysqli_num_rows($qry);
    if ($anz) {
        $i = 1;
        while ($ds = mysqli_fetch_array($qry)) {
            if ($i % 2) {
                $td = 'td1';
            } else {
                $td = 'td2';
            }

            if ($ds['displayed'] == 1) {
                $displayed = '<font color="green"><b>' . $_language->module['yes'] . '</b></font>';
            } else {
                $displayed = '<font color="red"><b>' . $_language->module['no'] . '</b></font>';
            }

            if (!isWebURLorProtocolRelative($ds['bannerurl'])) {
                $ds['bannerurl'] = 'http://' . $ds['bannerurl'];
            }

            $bannerurl = '<a href="' . getinput($ds['bannerurl']) . '" target="_blank">' .
                            getinput($ds['bannerurl']) .'</a>';


            $days = round((time() - $ds['date']) / (60 * 60 * 24));
            if ($days) {
                $perday = round($ds['hits'] / $days, 2);
            } else {
                $perday = $ds['hits'];
            }

            echo '<tr>
        <td>'.getinput($ds['bannername']).'</td>
        <td>'.$bannerurl.'</td>
        <td>'.$ds['hits'].' ('.$perday.')</td>
        <td class="hidden-xs hidden-sm">'.$displayed.'</td>
        <td><a href="admincenter.php?site=bannerrotation&amp;action=edit&amp;bannerID='.$ds['bannerID'].'" class="hidden-xs hidden-sm btn btn-warning btn-xs" type="button">' . $_language->module[ 'edit' ] . '</a>

        <input class="hidden-xs hidden-sm btn btn-danger btn-xs" type="button" onclick="MM_confirm(\'' . $_language->module['really_delete'] . '\', \'admincenter.php?site=bannerrotation&amp;delete=true&amp;bannerID='.$ds['bannerID'].'&amp;captcha_hash='.$hash.'\')" value="' . $_language->module['delete'] . '" />

	  <a href="admincenter.php?site=bannerrotation&amp;action=edit&amp;bannerID='.$ds['bannerID'].'"  class="mobile visible-xs visible-sm" type="button"><i class="fa fa-pencil"></i></a>
      <a class="mobile visible-xs visible-sm" type="button" onclick="MM_confirm(\'' . $_language->module['really_delete'] . '\', \'admincenter.php?site=bannerrotation&amp;delete=true&amp;bannerID='.$ds['bannerID'].'&amp;captcha_hash='.$hash.'\')" /><i class="fa fa-times"></i></a>


        </td>
      </tr>';
      
      $i++;
		}
	}
  else echo'<tr><td class="td1" colspan="5">'.$_language->module['no_entries'].'</td></tr>';
	
  echo '</table></form>';
}
echo '</div></div>';
?>