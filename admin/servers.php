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

$_language->readModule('servers', false, true);

if (!ispageadmin($userID) || mb_substr(basename($_SERVER[ 'REQUEST_URI' ]), 0, 15) != "admincenter.php") {
    die($_language->module[ 'access_denied' ]);
}

if (isset($_POST[ 'save' ])) {
    $CAPCLASS = new \webspell\Captcha;
    if ($CAPCLASS->checkCaptcha(0, $_POST[ 'captcha_hash' ])) {
        safe_query(
            "INSERT INTO
                `" . PREFIX . "servers` (
                    `name`,
                    `ip`,
                    `game`,
                    `info`
                )
                VALUES(
                    '" . $_POST[ 'name' ] . "',
                    '" . $_POST[ 'serverip' ] . "',
                    '" . $_POST[ 'game' ] . "',
                    '" . $_POST[ 'message' ] . "'
                    )"
        );
    } else {
        echo $_language->module[ 'transaction_invalid' ];
    }
} elseif (isset($_POST[ 'saveedit' ])) {
    $CAPCLASS = new \webspell\Captcha;
    if ($CAPCLASS->checkCaptcha(0, $_POST[ 'captcha_hash' ])) {
        safe_query(
            "UPDATE " . PREFIX . "servers SET name='" . $_POST[ 'name' ] . "', ip='" . $_POST[ 'serverip' ] .
            "', game='" . $_POST[ 'game' ] . "', info='" . $_POST[ 'message' ] . "' WHERE serverID='" .
            $_POST[ 'serverID' ] . "'"
        );
    } else {
        echo $_language->module[ 'transaction_invalid' ];
    }
} elseif (isset($_POST[ 'sort' ])) {
    $CAPCLASS = new \webspell\Captcha;
    if ($CAPCLASS->checkCaptcha(0, $_POST[ 'captcha_hash' ])) {
        if (is_array($_POST[ 'sortlist' ])) {
            foreach ($_POST[ 'sortlist' ] as $sortstring) {
                $sorter = explode("-", $sortstring);
                safe_query("UPDATE " . PREFIX . "servers SET sort='$sorter[1]' WHERE serverID='$sorter[0]' ");
            }
        }
    } else {
        echo $_language->module[ 'transaction_invalid' ];
    }
} elseif (isset($_GET[ 'delete' ])) {
    $CAPCLASS = new \webspell\Captcha;
    if ($CAPCLASS->checkCaptcha(0, $_GET[ 'captcha_hash' ])) {
        safe_query("DELETE FROM " . PREFIX . "servers WHERE serverID='" . $_GET[ 'serverID' ] . "'");
    } else {
        echo $_language->module[ 'transaction_invalid' ];
    }
}

$games = '';
$gamesa = safe_query("SELECT tag, name FROM " . PREFIX . "games ORDER BY name");
while ($dv = mysqli_fetch_array($gamesa)) {
    $games .= '<option value="' . $dv[ 'tag' ] . '">' . getinput($dv[ 'name' ]) . '</option>';
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

    $_language->readModule('bbcode', true, true);

    $addbbcode = $GLOBALS["_template"]->replaceTemplate("addbbcode", array());
    $addflags = $GLOBALS["_template"]->replaceTemplate("flags_admin", array());

    echo '<div class="panel panel-default">
    <div class="panel-heading">
                            <i class="fa fa-gamepad"></i> '.$_language->module['servers'].'
                        </div>
                        <div class="panel-body">
  
  <a href="admincenter.php?site=servers" class="white">' . $_language->module[ 'servers' ] .
        '</a> &raquo; ' . $_language->module[ 'add_server' ] . '<br><br>';

    echo '<script>
        function chkFormular() {
            if(!validbbcode(document.getElementById(\'message\').value, \'admin\')){
                return false;
            }
        }
    </script>';
  
	echo '<form class="form-horizontal" method="post" id="post" name="post" action="admincenter.php?site=servers" onsubmit="return chkFormular();">
	 <div class="row">

<div class="col-md-6">

<div class="form-group">
    <label class="col-sm-3 control-label">'.$_language->module['server_name'].':</label>
    <div class="col-sm-9"><span class="text-muted small"><em>
      <input class="form-control" type="text" name="name" size="60" /></em></span>
    </div>
  </div>
  <div class="form-group">
    <label class="col-sm-3 control-label">'.$_language->module['ip_port'].':</label>
    <div class="col-sm-9"><span class="text-muted small"><em>
    <input class="form-control" type="text" name="serverip" size="60" /></em></span>
    </div>
  </div>

  </div>
  

<div class="col-md-6">
  <div class="form-group">
    <label class="col-sm-2 control-label">'.$_language->module['game'].':</label>
    <div class="col-sm-8"><span class="text-muted small"><em>
		<select class="form-control" name="game">'.$games.'</select></em></span>
    </div>
  </div>
</div>
</div>

  <div class="row">
  <div class="col-md-12">
  
  '.$addflags.'<br>'.$addbbcode.'<br>
  </div></div>

  <div class="form-group">
   <div class="col-md-12">
      <textarea class="form-control" id="message" name="message" rows="10" cols="" ></textarea>
    </div>
  </div>
  <div class="form-group">
    <div class="col-md-12">
		<input type="hidden" name="captcha_hash" value="'.$hash.'" />
		<button class="btn btn-success btn-xs" type="submit" name="save"  />'.$_language->module['add_server'].'</button>
    </div>
  </div>

  </div>
  </div>
  </form></div>
  </div>';
} elseif($action=="edit") {

  echo'<div class="panel panel-default">
    <div class="panel-heading">
                            <i class="fa fa-gamepad"></i> '.$_language->module['servers'].'
                        </div>
                        <div class="panel-body">
  
  <a href="admincenter.php?site=servers" class="white">'.$_language->module['servers'].'</a> &raquo; '.$_language->module['edit_server'].'<br><br>';
	
      $CAPCLASS = new \webspell\Captcha;
    $CAPCLASS->createTransaction();
    $hash = $CAPCLASS->getHash();

    $_language->readModule('bbcode', true, true);

    $addbbcode = $GLOBALS["_template"]->replaceTemplate("addbbcode", array());
    $addflags = $GLOBALS["_template"]->replaceTemplate("flags_admin", array());

    $serverID = $_GET[ 'serverID' ];
    $ergebnis = safe_query("SELECT * FROM " . PREFIX . "servers WHERE serverID='" . $serverID . "'");
    $ds = mysqli_fetch_array($ergebnis);

    $games = str_replace(' selected="selected"', '', $games);
    $games = str_replace('value="' . $ds[ 'game' ] . '"', 'value="' . $ds[ 'game' ] . '" selected="selected"', $games);

    echo '<script>
        function chkFormular() {
            if(!validbbcode(document.getElementById(\'message\').value, \'admin\')){
                return false;
            }
        }
    </script>';

    
  echo '<form class="form-horizontal" method="post" id="post" name="post" action="admincenter.php?site=servers" onsubmit="return chkFormular();">
<div class="row">

<div class="col-md-6">

   <div class="form-group">
    <label class="col-sm-3 control-label">'.$_language->module['server_name'].':</label>
    <div class="col-sm-9"><span class="text-muted small"><em>
      <input class="form-control" type="text" name="name" value="'.getinput($ds['name']).'" /></em></span>
    </div>
  </div>
  
  <div class="form-group">
    <label class="col-sm-3 control-label">'.$_language->module['ip_port'].':</label>
    <div class="col-sm-9"><span class="text-muted small"><em>
		<input class="form-control" type="text" name="serverip" value="'.getinput($ds['ip']).'" /></em></span>
    </div>
  </div>

  </div>
  

<div class="col-md-6">

<div class="form-group">
    <label class="col-sm-2 control-label">'.$_language->module['game'].':</label>
    <div class="col-sm-8"><span class="text-muted small"><em>
    <select class="form-control" name="game">'.$games.'</select></em></span>
    </div>
  </div>
</div>
</div>

  <div class="row">
  <div class="col-md-12">
  
<div class="form-group">
  <div class="col-md-12">
  '.$addflags.'<br>'.$addbbcode.'<br>
  </div></div>

  <div class="form-group">
   <div class="col-md-12">
      <textarea class="form-control" id="message" name="message" rows="10" cols="" >'.getinput($ds['info']).'</textarea>
    </div>
  </div>

  <div class="form-group">
    <div class="col-md-12">
		<input type="hidden" name="serverID" value="'.$serverID.'" /><input type="hidden" name="captcha_hash" value="'.$hash.'" />
		<button class="btn btn-success btn-xs" type="submit" name="saveedit"  />'.$_language->module['edit_server'].'</button>
    </div>
  </div>

  </div>
  </div>
  </form></div>
  </div>';
}

else {
	
  echo'<div class="panel panel-default">
    <div class="panel-heading">
                            <i class="fa fa-gamepad"></i> '.$_language->module['servers'].'
                        </div>
                        <div class="panel-body">';
  
  echo'<a href="admincenter.php?site=servers&amp;action=add" class="btn btn-primary btn-xs" type="button">' . $_language->module[ 'new_server' ] . '</a><br /><br />';

	$ergebnis = safe_query("SELECT * FROM " . PREFIX . "servers ORDER BY sort");
    $anz = mysqli_num_rows($ergebnis);
    if ($anz) {
        $CAPCLASS = new \webspell\Captcha;
        $CAPCLASS->createTransaction();
        $hash = $CAPCLASS->getHash();
  
  echo'<form method="post" name="ws_servers" action="admincenter.php?site=servers">
    <table class="table table-striped">
      <thead>
        <th><b>'.$_language->module['servers'].'</b></th>
        <th><b>'.$_language->module['actions'].'</b></th>
        <th><b>'.$_language->module['sort'].'</b></th>
      </thead>';

		$i = 1;
        while ($ds = mysqli_fetch_array($ergebnis)) {
            if ($i % 2) {
                $td = 'td1';
            } else {
                $td = 'td2';
            }

            $list = '<select name="sortlist[]">';
            $counter = mysqli_num_rows($ergebnis);
            for ($n = 1; $n <= $counter; $n++) {
                $list .= '<option value="' . $ds[ 'serverID' ] . '-' . $n . '">' . $n . '</option>';
            }
            $list .= '</select>';
            $list = str_replace(
                'value="' . $ds[ 'serverID' ] . '-' . $ds[ 'sort' ] . '"',
                'value="' . $ds[ 'serverID' ] . '-' . $ds[ 'sort' ] . '" selected="selected"',
                $list
            );

            echo '<tr>
        <td><img src="../images/games/'.is_gamefilexist('../images/games/', $ds[ 'game' ]).'" width="13" height="13" border="0" alt="" /> <a href="hlsw://'.$ds['ip'].'"><b>'.$ds['ip'].'</b></a><br /><b>'.getinput($ds['name']).'</b><br />

        <span class="text-muted small"><em>'.cleartext($ds['info'],1,'admin').'</em></span>
        </td>
        <td><a href="admincenter.php?site=servers&amp;action=edit&amp;serverID='.$ds['serverID'].'" class="hidden-xs hidden-sm btn btn-warning btn-xs" type="button">' . $_language->module[ 'edit' ] . '</a>

        <input class="hidden-xs hidden-sm btn btn-danger btn-xs" type="button" onclick="MM_confirm(\'' . $_language->module['really_delete'] . '\', \'admincenter.php?site=servers&amp;delete=true&amp;serverID='.$ds['serverID'].'&amp;captcha_hash='.$hash.'\')" value="' . $_language->module['delete'] . '" />

    <a href="admincenter.php?site=servers&amp;action=edit&amp;serverID='.$ds['serverID'].'"  class="mobile visible-xs visible-sm" type="button"><i class="fa fa-pencil"></i></a>
      <a class="mobile visible-xs visible-sm" type="button" onclick="MM_confirm(\'' . $_language->module['really_delete'] . '\', \'admincenter.php?site=servers&amp;delete=true&amp;serverID='.$ds['serverID'].'&amp;captcha_hash='.$hash.'\')" /><i class="fa fa-times"></i></a></td>
        <td>'.$list.'</td>
      </tr>';
        
        $i++;
		}
		echo'<tr>
        <td colspan="3" class="td_head" align="right"><input type="hidden" name="captcha_hash" value="'.$hash.'" /><button class="btn btn-primary btn-xs" type="submit" name="sort" />'.$_language->module['to_sort'].'</button></td>
      </tr>
    </table>
    </form>';
	}
	else echo $_language->module['no_server'];
}
echo '</div></div>';
?>