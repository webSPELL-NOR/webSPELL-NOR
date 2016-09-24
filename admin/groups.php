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

$_language->readModule('groups', false, true);

if (!isforumadmin($userID) || mb_substr(basename($_SERVER[ 'REQUEST_URI' ]), 0, 15) != "admincenter.php") {
    die($_language->module[ 'access_denied' ]);
}

if (isset($_GET[ 'action' ])) {
    $action = $_GET[ 'action' ];
} else {
    $action = '';
}

if ($action == "delete") {
    $CAPCLASS = new \webspell\Captcha;
    if ($CAPCLASS->checkCaptcha(0, $_GET[ 'captcha_hash' ])) {
        if (!$_GET[ 'fgrID' ]) {
            die('missing fgrID... <a href="admincenter.php?site=groups">back</a>');
        }
        safe_query("ALTER TABLE " . PREFIX . "user_forum_groups DROP `" . $_GET[ 'fgrID' ] . "`");
        safe_query("DELETE FROM " . PREFIX . "forum_groups WHERE fgrID='" . $_GET[ 'fgrID' ] . "'");

        redirect("admincenter.php?site=groups", "", 0);
    } else {
        echo $_language->module[ 'transaction_invalid' ];
    }
} elseif ($action == "add") {
    echo '<div class="panel panel-default">
    <div class="panel-heading">
                            <i class="fa fa-users"></i> '.$_language->module['groups'].'
                        </div>
                        <div class="panel-body">
    <a href="admincenter.php?site=groups" class="white">' . $_language->module[ 'groups' ] .
        '</a> &raquo; ' . $_language->module[ 'add_group' ] . '<br><br>';

    $CAPCLASS = new \webspell\Captcha;
    $CAPCLASS->createTransaction();
    $hash = $CAPCLASS->getHash();
  
  echo'<form class="form-horizontal" method="post" action="admincenter.php?site=groups&amp;action=save">
   <div class="form-group">
    <label class="col-sm-2 control-label">'.$_language->module['group_name'].':</label>
    <div class="col-sm-8"><span class="text-muted small"><em>
      <input class="form-control" type="text" name="name" /></em></span>
    </div>
  </div>
  <div class="form-group">
    <div class="col-sm-offset-2 col-sm-8">
		<input type="hidden" name="captcha_hash" value="'.$hash.'" />
		<button class="btn btn-success btn-xs" type="submit" name="save" />'.$_language->module['add_group'].'</button>
    </div>
  </div>
  </form>
  </div>
  </div>';
} elseif ($action == "save") {
    if (!$_POST[ 'name' ]) {
        die('<b>' . $_language->module[ 'error_group' ] .
            '</b><br /><br /><a href="admincenter.php?site=groups&amp;action=add">&laquo; ' .
            $_language->module[ 'back' ] . '</a>');
    }
    $CAPCLASS = new \webspell\Captcha;
    if ($CAPCLASS->checkCaptcha(0, $_POST[ 'captcha_hash' ])) {
        safe_query("INSERT INTO " . PREFIX . "forum_groups ( name ) values( '" . $_POST[ 'name' ] . "' ) ");
        $id = mysqli_insert_id($_database);
        if (!safe_query("ALTER TABLE " . PREFIX . "user_forum_groups ADD `" . $id . "` INT( 1 ) NOT NULL ; ")) {
            safe_query("ALTER TABLE " . PREFIX . "user_forum_groups DROP `" . $id . "`");
            safe_query("ALTER TABLE " . PREFIX . "user_forum_groups ADD `" . $id . "` INT( 1 ) NOT NULL ; ");
        }

        redirect("admincenter.php?site=groups", "", 0);
    } else {
        echo $_language->module[ 'transaction_invalid' ];
    }
} elseif ($action == "saveedit") {
    $name = $_POST[ 'name' ];
    $CAPCLASS = new \webspell\Captcha;
    if ($CAPCLASS->checkCaptcha(0, $_POST[ 'captcha_hash' ])) {
        safe_query(
            "UPDATE " . PREFIX . "forum_groups SET name='" . $name . "' WHERE fgrID='" . $_POST[ 'fgrID' ] .
            "'"
        );
        redirect("admincenter.php?site=groups", "", 0);
    } else {
        echo $_language->module[ 'transaction_invalid' ];
    }
} elseif ($action == "edit") {
    echo '<div class="panel panel-default">
    <div class="panel-heading">
                            <i class="fa fa-users"></i> '.$_language->module['groups'].'
                        </div>
                        <div class="panel-body">
    <a href="admincenter.php?site=groups" class="white">' . $_language->module[ 'groups' ] .
        '</a> &raquo; ' . $_language->module[ 'edit_group' ] . '<br><br>';

    if (!$_GET[ 'fgrID' ]) {
        die('<b>' . $_language->module[ 'error_groupid' ] .
            '</b><br /><br /><a href="admincenter.php?site=groups">&laquo; ' . $_language->module[ 'back' ] . '</a>');
    }
    $ergebnis = safe_query("SELECT * FROM " . PREFIX . "forum_groups WHERE fgrID='" . $_GET[ 'fgrID' ] . "'");
    $ds = mysqli_fetch_array($ergebnis);

    $CAPCLASS = new \webspell\Captcha;
    $CAPCLASS->createTransaction();
    $hash = $CAPCLASS->getHash();
  
  echo'<form class="form-horizontal" method="post" action="admincenter.php?site=groups&amp;action=saveedit">
   <div class="form-group">
    <label class="col-sm-2 control-label">'.$_language->module['group_name'].':</label>
    <div class="col-sm-8"><span class="text-muted small"><em>
      <input class="form-control" type="text" name="name" value="'.getinput($ds["name"]).'" /></em></span>
    </div>
  </div>
  <div class="form-group">
    <div class="col-sm-offset-2 col-sm-8">
		<input type="hidden" name="captcha_hash" value="'.$hash.'" /><input name="fgrID" type="hidden" value="'.$ds["fgrID"].'" />
		<button class="btn btn-success btn-xs" type="submit" name="save" />'.$_language->module['edit_group'].'</button>
    </div>
  </div>
  </form>
  </div>
  </div>';
}

else {
	
  echo'<div class="panel panel-default">
  <div class="panel-heading">
                            <i class="fa fa-users"></i> '.$_language->module['groups'].'
                        </div>
                        <div class="panel-body">';
  
  echo'<a href="admincenter.php?site=groups&amp;action=add" class="btn btn-primary btn-xs" type="button">' . $_language->module[ 'new_group' ] . '</a><br /><br />';

	$ergebnis = safe_query("SELECT * FROM " . PREFIX . "forum_groups ORDER BY fgrID");
	
  echo'<table class="table table-striped">
    <thead>
      <th><b>'.$_language->module['group_name'].'</b></th>
      <th><b>'.$_language->module['actions'].'</b></th>
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

        echo '<tr>
      <td><b>'.getinput($ds['name']).'</b></td>
      <td><a href="admincenter.php?site=groups&amp;action=edit&amp;fgrID='.$ds["fgrID"].'" class="hidden-xs hidden-sm btn btn-warning btn-xs" type="button">' . $_language->module[ 'edit' ] . '</a>

        <input class="hidden-xs hidden-sm btn btn-danger btn-xs" type="button" onclick="MM_confirm(\'' . $_language->module['really_delete'] . '\', \'admincenter.php?site=groups&amp;action=delete&amp;fgrID='.$ds["fgrID"].'&amp;captcha_hash='.$hash.'\')" value="' . $_language->module['delete'] . '" />

    <a href="admincenter.php?site=groups&amp;action=edit&amp;fgrID='.$ds["fgrID"].'"  class="mobile visible-xs visible-sm" type="button"><i class="fa fa-pencil"></i></a>
      <a class="mobile visible-xs visible-sm" type="button" onclick="MM_confirm(\'' . $_language->module['really_delete'] . '\', \'admincenter.php?site=groups&amp;action=delete&amp;fgrID='.$ds["fgrID"].'&amp;captcha_hash='.$hash.'\')" /><i class="fa fa-times"></i></a>


      </td>
		</tr>';
      
      $i++;
	}

	echo'</table></div></div>';
}
echo '</div>';
?>