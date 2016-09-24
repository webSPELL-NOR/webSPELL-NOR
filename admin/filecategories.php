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

$_language->readModule('filecategorys', false, true);

if (!isfileadmin($userID) || mb_substr(basename($_SERVER[ 'REQUEST_URI' ]), 0, 15) != "admincenter.php") {
    die($_language->module[ 'access_denied' ]);
}

function generate_overview($filecats = '', $offset = '', $subcatID = 0)
{

    global $_language;
    $rubrics =
        safe_query("SELECT * FROM " . PREFIX . "files_categorys WHERE subcatID = '" . $subcatID . "' ORDER BY name");

    $i = 1;
    $CAPCLASS = new \webspell\Captcha;
    $CAPCLASS->createTransaction();
    $hash = $CAPCLASS->getHash();

    while ($ds = mysqli_fetch_array($rubrics)) {
        if ($i % 2) {
            $td = 'td1';
        } else {
            $td = 'td2';
        }
				
		$filecats .= '<tr>
        <td>'.$offset.getinput($ds['name']).'</td>
        <td class="text-right"><a href="admincenter.php?site=filecategories&amp;action=edit&amp;filecatID='.$ds['filecatID'].'" class="hidden-xs hidden-sm btn btn-warning btn-xs" type="button">' . $_language->module[ 'edit' ] . '</a>

        <input class="hidden-xs hidden-sm btn btn-danger btn-xs" type="button" onclick="MM_confirm(\'' . $_language->module['really_delete'] . '\', \'admincenter.php?site=filecategories&amp;delete=true&amp;filecatID='.$ds['filecatID'].'&amp;captcha_hash='.$hash.'\')" value="' . $_language->module['delete'] . '" />

		<a href="admincenter.php?site=filecategories&amp;action=edit&amp;filecatID='.$ds['filecatID'].'"  class="mobile visible-xs visible-sm" type="button"><i class="fa fa-pencil"></i></a>
      	<a class="mobile visible-xs visible-sm" type="button" onclick="MM_confirm(\'' . $_language->module['really_delete'] . '\', \'admincenter.php?site=filecategories&amp;delete=true&amp;filecatID='.$ds['filecatID'].'&amp;captcha_hash='.$hash.'\')" /><i class="fa fa-times"></i></a>
        </td>
    	</tr>';
	      
        $i++;

        if (mysqli_num_rows(safe_query(
            "SELECT * FROM " . PREFIX . "files_categorys WHERE subcatID = '" .
            $ds[ 'filecatID' ] . "'"
        ))) {
            $filecats .= generate_overview("", $offset . getinput($ds[ 'name' ]) . " &raquo; ", $ds[ 'filecatID' ]);
        }
    }

    return $filecats;
}

function delete_category($filecat)
{
    $rubrics = safe_query(
        "SELECT filecatID FROM " . PREFIX . "files_categorys WHERE subcatID = '" . $filecat .
        "' ORDER BY name"
    );
    if (mysqli_num_rows($rubrics)) {
        while ($ds = mysqli_fetch_assoc($rubrics)) {
            delete_category($ds[ 'filecatID' ]);
        }
    }
    safe_query("DELETE FROM " . PREFIX . "files_categorys WHERE filecatID='" . $filecat . "'");
    $files = safe_query("SELECT * FROM " . PREFIX . "files WHERE filecatID='" . $filecat . "'");
    while ($ds = mysqli_fetch_array($files)) {
        if (stristr($ds[ 'file' ], "http://") || stristr($ds[ 'file' ], "ftp://")) {
            @unlink('../downloads/' . $ds[ 'file' ]);
        }
    }
    safe_query("DELETE FROM " . PREFIX . "files WHERE filecatID='" . $filecat . "'");
}

/* start processing */

if (isset($_POST[ 'save' ])) {
    if (mb_strlen($_POST[ 'name' ]) > 0) {
        $CAPCLASS = new \webspell\Captcha;
        if ($CAPCLASS->checkCaptcha(0, $_POST[ 'captcha_hash' ])) {
            safe_query(
                "INSERT INTO " . PREFIX . "files_categorys ( name, subcatID ) values( '" . $_POST[ 'name' ] .
                "', '" . $_POST[ 'subcat' ] . "' ) "
            );
        } else {
            echo $_language->module[ 'transaction_invalid' ];
        }
    } else {
        redirect("admincenter.php?site=filecategories&amp;action=add", $_language->module[ 'enter_name' ], 3);
    }
} elseif (isset($_POST[ 'saveedit' ])) {
    if (mb_strlen($_POST[ 'name' ]) > 0) {
        $CAPCLASS = new \webspell\Captcha;
        if ($CAPCLASS->checkCaptcha(0, $_POST[ 'captcha_hash' ])) {
            safe_query(
                "UPDATE " . PREFIX . "files_categorys SET name='" . $_POST[ 'name' ] . "', subcatID = '" .
                $_POST[ 'subcat' ] . "' WHERE filecatID='" . $_POST[ 'filecatID' ] . "'"
            );
        } else {
            echo $_language->module[ 'transaction_invalid' ];
        }
    } else {
        redirect(
            "admincenter.php?site=filecategories&amp;action=edit&amp;filecatID=" . $_POST[ 'filecatID' ],
            $_language->module[ 'enter_name' ],
            3
        );
    }
} elseif (isset($_GET[ 'delete' ])) {
    $filecatID = $_GET[ 'filecatID' ];
    $CAPCLASS = new \webspell\Captcha;
    if ($CAPCLASS->checkCaptcha(0, $_GET[ 'captcha_hash' ])) {
        delete_category($filecatID);
    } else {
        echo $_language->module[ 'transaction_invalid' ];
    }
}

if (!isset($_GET[ 'action' ])) {
    $_GET[ 'action' ] = '';
}

if ($_GET[ 'action' ] == "add") {
    $filecats = generateFileCategoryOptions('<option value="0">' . $_language->module[ 'main' ] . '</option>', '- ');
    $CAPCLASS = new \webspell\Captcha;
    $CAPCLASS->createTransaction();
    $hash = $CAPCLASS->getHash();
  
  echo'<div class="panel panel-default">
  <div class="panel-heading">
                            <i class="fa fa-download"></i> '.$_language->module['file_categories'].'
                        </div>
                        <div class="panel-body">
  <a href="admincenter.php?site=filecategories" class="white">'.$_language->module['file_categories'].'</a> &raquo; '.$_language->module['add_category'].'<br><br>';
  
	echo'<form class="form-horizontal" method="post" action="admincenter.php?site=filecategories">
	<div class="form-group">
    <label class="col-sm-2 control-label">'.$_language->module['category_name'].':</label>
    <div class="col-sm-8"><span class="text-muted small"><em>
      <input type="text" class="form-control" name="name" /></em></span>
    </div>
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">'.$_language->module['sub_category'].':</label>
    <div class="col-sm-8"><span class="text-muted small"><em>
     <select class="form-control" name="subcat">'.$filecats.'</select></em></span>
    </div>
  </div>
  <div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
      <input type="hidden" name="captcha_hash" value="'.$hash.'" /><button class="btn btn-success btn-xs" type="submit" name="save" />'.$_language->module['add_category'].'</button>
    </div>
  </div>
  </form></div>
  </div>';
} elseif ($_GET[ 'action' ] == "edit") {
    $filecatID = $_GET[ 'filecatID' ];
    $ergebnis = safe_query("SELECT * FROM " . PREFIX . "files_categorys WHERE filecatID='$filecatID'");
    $ds = mysqli_fetch_array($ergebnis);

    $filecats = generateFileCategoryOptions('<option value="0">' . $_language->module[ 'main' ] . '</option>', '- ');

    $filecats = str_replace(
        'value="' . $ds[ 'subcatID' ] . '"',
        'value="' . $ds[ 'subcatID' ] . '" selected="selected"',
        $filecats
    );
    $CAPCLASS = new \webspell\Captcha;
    $CAPCLASS->createTransaction();
    $hash = $CAPCLASS->getHash();
	
	echo'<div class="panel panel-default">
  <div class="panel-heading">
                            <i class="fa fa-download"></i> '.$_language->module['file_categories'].'
                        </div>
        <div class="panel-body">
	<a href="admincenter.php?site=filecategories" class="white">'.$_language->module['file_categories'].'</a> &raquo; '.$_language->module['edit_category'].'<br><br>';
  
  echo'<form class="form-horizontal" method="post" action="admincenter.php?site=filecategories" enctype="multipart/form-data">
  <div class="form-group">
    <label class="col-sm-2 control-label">'.$_language->module['category_name'].':</label>
    <div class="col-sm-8"><span class="text-muted small"><em>
      <input type="text" class="form-control" name="name" value="'.getinput($ds['name']).'" /></em></span>
    </div>
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">'.$_language->module['sub_category'].':</label>
    <div class="col-sm-8"><span class="text-muted small"><em>
     <select class="form-control" name="subcat">'.$filecats.'</select></em></span>
    </div>
  </div>
  <div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
      <input type="hidden" name="captcha_hash" value="'.$hash.'" /><input type="hidden" name="filecatID" value="'.$ds['filecatID'].'" /><button class="btn btn-success btn-xs" type="submit" name="saveedit" />'.$_language->module['edit_category'].'</button>
    </div>
  </div>
  </form></div>
  </div>';
}

else {
	
  echo'<div class="panel panel-default">
  <div class="panel-heading">
                            <i class="fa fa-download"></i> '.$_language->module['file_categories'].'
                        </div>
                        <div class="panel-body">';
  
  echo'<a href="admincenter.php?site=filecategories&amp;action=add" class="btn btn-primary btn-xs" type="button">' . $_language->module[ 'new_category' ] . '</a><br /><br />';

	echo'<table class="table">
    <thead>
      <th><b>'.$_language->module['category_name'].'</b></th>
      <th class="text-right"><b>'.$_language->module['actions'].'</b></th>
    </thead>';

	 $overview = generate_overview();
    echo $overview;

	echo'</table>';
}
echo '</div>
  </div>';
?>