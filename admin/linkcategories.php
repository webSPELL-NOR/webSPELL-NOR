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

$_language->readModule('linkcategories', false, true);

if (!ispageadmin($userID) || mb_substr(basename($_SERVER[ 'REQUEST_URI' ]), 0, 15) !== "admincenter.php") {
    die($_language->module[ 'access_denied' ]);
}

if (isset($_POST[ 'save' ])) {
    $CAPCLASS = new \webspell\Captcha;
    if ($CAPCLASS->checkCaptcha(0, $_POST[ 'captcha_hash' ])) {
        if (checkforempty(array('name'))) {
            safe_query("INSERT INTO " . PREFIX . "links_categorys ( name ) values( '" . $_POST[ 'name' ] . "' ) ");
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
                "UPDATE " . PREFIX . "links_categorys SET name='" . $_POST[ 'name' ] . "' WHERE linkcatID='" .
                $_POST[ 'linkcatID' ] . "'"
            );
        } else {
            echo $_language->module[ 'information_incomplete' ];
        }
    } else {
        echo $_language->module[ 'transaction_invalid' ];
    }
} elseif (isset($_GET[ 'delete' ])) {
    $CAPCLASS = new \webspell\Captcha;
    if ($CAPCLASS->checkCaptcha(0, $_GET[ 'captcha_hash' ])) {
        safe_query("DELETE FROM " . PREFIX . "links_categorys WHERE linkcatID='" . $_GET[ 'linkcatID' ] . "'");
        safe_query("DELETE FROM " . PREFIX . "links WHERE linkcatID='" . $_GET[ 'linkcatID' ] . "'");
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
                            <i class="fa fa-link"></i> '.$_language->module['link_categories'].'
                        </div>
                        <div class="panel-body">
  <a href="admincenter.php?site=linkcategories" class="white">'.$_language->module['link_categories'].'</a> &raquo; '.$_language->module['add_category'].'<br><br>';
  
  echo'<form class="form-horizontal" method="post" action="admincenter.php?site=linkcategories">
  <div class="form-group">
    <label class="col-sm-2 control-label">'.$_language->module['category_name'].':</label>
    <div class="col-sm-8"><span class="text-muted small"><em>
      <input class="form-control" type="text" name="name" /></em></span>
    </div>
  </div>
  <div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
      <input type="hidden" name="captcha_hash" value="'.$hash.'" /><button class="btn btn-success btn-xs" type="submit" name="save" />'.$_language->module['add_category'].'</button>
    </div>
  </div>
  </form></div>
  </div>';
} elseif ($action == "edit") {
    $CAPCLASS = new \webspell\Captcha;
    $CAPCLASS->createTransaction();
    $hash = $CAPCLASS->getHash();
  
  echo'<div class="panel panel-default">
  <div class="panel-heading">
                            <i class="fa fa-link"></i> '.$_language->module['link_categories'].'
                        </div>
                        <div class="panel-body">
  <a href="admincenter.php?site=linkcategories" class="white">'.$_language->module['link_categories'].'</a> &raquo; '.$_language->module['edit_category'].'<br><br>';

	$ergebnis =
        safe_query("SELECT * FROM " . PREFIX . "links_categorys WHERE linkcatID='" . $_GET[ 'linkcatID' ] . "'");
    $ds = mysqli_fetch_array($ergebnis);

	echo'<form class="form-horizontal" method="post" action="admincenter.php?site=linkcategories">
	<div class="form-group">
    <label class="col-sm-2 control-label">'.$_language->module['category_name'].':</label>
    <div class="col-sm-8"><span class="text-muted small"><em>
      <input class="form-control" type="text" name="name" value="'.getinput($ds['name']).'" /></em></span>
    </div>
  </div>
  <div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
      <input type="hidden" name="captcha_hash" value="'.$hash.'" /><input type="hidden" name="linkcatID" value="'.$ds['linkcatID'].'" /><button class="btn btn-success btn-xs" type="submit" name="saveedit" />'.$_language->module['edit_category'].'</button>
    </div>
  </div>
  </form></div>
  </div>';
}

else {
	
  echo'<div class="panel panel-default">
  <div class="panel-heading">
                            <i class="fa fa-link"></i> '.$_language->module['link_categories'].'
                        </div>
                        <div class="panel-body">';
  
  echo'<a href="admincenter.php?site=linkcategories&amp;action=add" class="btn btn-primary btn-xs" type="button">' . $_language->module[ 'new_category' ] . '</a><br /><br />';

	$ergebnis = safe_query("SELECT * FROM " . PREFIX . "links_categorys ORDER BY name");
	
  echo'<table class="table table-striped">
    <thead>
      <th><b>'.$_language->module['category_name'].'</b></th>
      <th class="text-right"><b>'.$_language->module['actions'].'</b></th>
    </thead>';

	 $i = 1;
    $CAPCLASS = new \webspell\Captcha;
    $CAPCLASS->createTransaction();
    $hash = $CAPCLASS->getHash();

    while ($ds = mysqli_fetch_array($ergebnis)) {
        if ($i % 2) {
            $td = 'td1';
        } else {
            $td = 'td2';
        }
      
		echo'<tr>
      <td>'.getinput($ds['name']).'</td>
      <td class="text-right"><a href="admincenter.php?site=linkcategories&amp;action=edit&amp;linkcatID='.$ds['linkcatID'].'" class="hidden-xs hidden-sm btn btn-warning btn-xs" type="button">' . $_language->module[ 'edit' ] . '</a>

        <input class="hidden-xs hidden-sm btn btn-danger btn-xs" type="button" onclick="MM_confirm(\'' . $_language->module['really_delete'] . '\', \'admincenter.php?site=linkcategories&amp;delete=true&amp;linkcatID='.$ds['linkcatID'].'&amp;captcha_hash='.$hash.'\')" value="' . $_language->module['delete'] . '" />
        
        <a href="admincenter.php?site=linkcategories&amp;action=edit&amp;linkcatID='.$ds['linkcatID'].'"  class="mobile visible-xs visible-sm" type="button"><i class="fa fa-pencil"></i></a>
      <a class="mobile visible-xs visible-sm" type="button" onclick="MM_confirm(\'' . $_language->module['really_delete'] . '\', \'admincenter.php?site=linkcategories&amp;delete=true&amp;linkcatID='.$ds['linkcatID'].'&amp;captcha_hash='.$hash.'\')" /><i class="fa fa-times"></i></a></td>
    </tr>';
      
      $i++;
	}
	echo'</table>';
}
echo '</div>
  </div>';
?>