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


# $_lang->readModule('clan_rules', false, true);
$pm = new plugin_manager(); 
$_lang = $pm->plugin_language("clan_rules_admin", $plugin_path);

if (!ispageadmin($userID)) { //|| mb_substr(basename($_SERVER[ 'REQUEST_URI' ]), 0, 15) != "admincenter.php") {
    echo $_lang[ 'access_denied' ]; return false;
}



if (isset($_GET[ 'action' ])) {
    $action = $_GET[ 'action' ];
} else {
    $action = '';
}

if ($action == "add") {

echo'<div class="page-header">
    <h2><a href="index.php?site=clan_rules_admin">' . $_lang[ 'clan_rules' ] . '</a></h2>
</div>';

    echo '<h4><a href="index.php?site=clan_rules_admin" class="white">' . $_lang[ 'clan_rules' ] .
    '</a> &raquo; ' . $_lang[ 'add_clan_rules' ] . '</h4>';

    $CAPCLASS = new \webspell\Captcha;
    $CAPCLASS->createTransaction();
    $hash = $CAPCLASS->getHash();

    $_language->readModule('bbcode', true, false);

    $addbbcode = $GLOBALS["_template"]->replaceTemplate("addbbcode", array());
    $addflags = $GLOBALS["_template"]->replaceTemplate("flags_admin", array());

    echo '<script>
    <!--
    function chkFormular() {
        if(!validbbcode(document.getElementById(\'message\').value, \'admin\')) {
           return false;
       }
   }
-->
</script>';

    echo '<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">New</h3>
    </div>
    <div class="panel-body">
    <form method="post" id="post" name="post" action="index.php?site=clan_rules_admin" enctype="multipart/form-data"
    onsubmit="return chkFormular();">
<table width="100%" border="0" cellspacing="1" cellpadding="2">
    
<tr>
  <td><b>' . $_lang[ 'clan_rules_name' ] . ':</b></td>
  <td><input class="form-control" type="text" name="title" size="60" maxlength="255" /></td>
</tr>

<tr>
  <td colspan="2">
  <hr>
    <b>' . $_lang[ 'description' ] . ':</b>
    <table width="99%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td valign="top">' . $addbbcode . '</td>
          </tr>
		<tr>
          <td valign="top">' . $addflags . '</td>
      </tr>
  </table>
  <br /><textarea class="form-control" id="message" rows="5" cols="" name="message" style="width: 100%;"></textarea>
</td>
</tr>
<tr>
  <td><b>' . $_lang[ 'is_displayed' ] . ':</b></td>
  <td><input type="checkbox" name="displayed" value="1" checked="checked" /></td>
</tr>

<tr>
  <td><input type="hidden" name="captcha_hash" value="' . $hash . '" /></td>
  </tr>

<tr>
  <td><input class="btn btn-success" type="submit" name="save" value="' . $_lang[ 'add_clan_rules' ] . '" /></td>
</tr>
</table>
</form></div></div>';
} elseif ($action == "edit") {

echo'<div class="page-header">
    <h2><a href="index.php?site=clan_rules_admin">' . $_lang[ 'clan_rules' ] . '</a></h2>
</div>';

    echo '<h4><a href="index.php?site=clan_rules_admin" class="white">' . $_lang[ 'clan_rules' ] .
    '</a> &raquo; ' . $_lang[ 'edit_clan_rules' ] . '</h4>';

    $ds = mysqli_fetch_array(
        safe_query(
            "SELECT * FROM " . PREFIX . "clan_rules WHERE clan_rulesID='" . $_GET[ "clan_rulesID" ] ."'"
        )
    );
    

    if ($ds[ 'displayed' ] == 1) {
        $displayed = '<input type="checkbox" name="displayed" value="1" checked="checked" />';
    } else {
        $displayed = '<input type="checkbox" name="displayed" value="1" />';
    }

    

    $CAPCLASS = new \webspell\Captcha;
    $CAPCLASS->createTransaction();
    $hash = $CAPCLASS->getHash();

    $_language->readModule('bbcode', true, false);

    $addbbcode = $GLOBALS["_template"]->replaceTemplate("addbbcode", array());
    $addflags = $GLOBALS["_template"]->replaceTemplate("flags_admin", array());

    echo '<script>
    <!--
    function chkFormular() {
        if(!validbbcode(document.getElementById(\'message\').value, \'admin\')) {
           return false;
       }
   }
-->
</script>';

    echo '<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">Edit</h3>
    </div>
    <div class="panel-body">
    
    <form method="post" id="post" name="post" action="index.php?site=clan_rules_admin"
    enctype="multipart/form-data" onsubmit="return chkFormular();">
<input type="hidden" name="clan_rulesID" value="' . $ds[ 'clan_rulesID' ] . '" />
<table width="100%" border="0" cellspacing="1" cellpadding="2">
    
<tr>
  <td><b>' . $_lang[ 'clan_rules_name' ] . ':</b></td>
  <td><input class="form-control" type="text" name="title" size="60" maxlength="255" value="' . getinput($ds[ 'title' ]) . '" /></td>
</tr>

<tr>
  <td colspan="2">
  <hr>
    <b>' . $_lang[ 'description' ] . ':</b>
    <table width="99%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td valign="top">' . $addbbcode . '</td>
          </tr>
		<tr>
          <td valign="top">' . $addflags . '</td>
      </tr>
  </table>
  <br /><textarea class="form-control" id="message" rows="5" cols="" name="message" style="width: 100%;">' . getinput($ds[ 'text' ]) .
        '</textarea>
</td>
</tr>
<tr>
  <td><b>' . $_lang[ 'is_displayed' ] . ':</b></td>
  <td>' . $displayed . '</td>
</tr>

<tr>
  <td><input type="hidden" name="captcha_hash" value="' . $hash . '" /></td>
  </tr>

<tr>
  <td><input class="btn btn-warning" type="submit" name="saveedit" value="' . $_lang[ 'edit_clan_rules' ] . '" /></td>
</tr>
</table>
</form>
</div></div>';
} elseif (isset($_POST[ 'sortieren' ])) {
    $CAPCLASS = new \webspell\Captcha;
    if ($CAPCLASS->checkCaptcha(0, $_POST[ 'captcha_hash' ])) {
        $sort = $_POST[ 'sort' ];
        if (is_array($sort)) {
            foreach ($sort as $sortstring) {
                $sorter = explode("-", $sortstring);
                safe_query("UPDATE " . PREFIX . "clan_rules SET sort='$sorter[1]' WHERE clan_rulesID='$sorter[0]' ");
                redirect("index.php?site=clan_rules_admin", "", 0);
            }
        }
    } else {
        echo $_lang[ 'transaction_invalid' ];
    }
} elseif (isset($_POST[ "save" ])) {
    $title = $_POST[ "title" ];
    
    $text = $_POST[ "message" ];
    if (isset($_POST[ "displayed" ])) {
        $displayed = 1;
    } else {
        $displayed = 0;
    }
    if (!$displayed) {
        $displayed = 0;
    }
    

    $CAPCLASS = new \webspell\Captcha;
    if ($CAPCLASS->checkCaptcha(0, $_POST[ 'captcha_hash' ])) {
        safe_query(
            "INSERT INTO " . PREFIX .
            "clan_rules (clan_rulesID, title,  text, displayed, sort) values('', '" . $title . "', '" . $text . "', '" . $displayed . "', '1')"
        );

        $id = mysqli_insert_id($_database);

        $errors = array();

        //TODO: should be loaded from root language folder
        $_language->readModule('formvalidation', true);

       
     

        if (count($errors)) {
            $errors = array_unique($errors);
            echo generateErrorBoxFromArray($_language->module['errors_there'], $errors);
        } else {
            redirect("index.php?site=clan_rules_admin", "", 0);
        }
    } else {
        echo $_language->module[ 'transaction_invalid' ];
    }
} elseif (isset($_POST[ "saveedit" ])) {
    $title = $_POST[ "title" ];
    
    $text = $_POST[ "message" ];
    if (isset($_POST[ "displayed" ])) {
        $displayed = 1;
    } else {
        $displayed = 0;
    }
    
    $CAPCLASS = new \webspell\Captcha;
    if ($CAPCLASS->checkCaptcha(0, $_POST[ 'captcha_hash' ])) {
        

        safe_query(
            "UPDATE " . PREFIX . "clan_rules SET title='" . $title . "', text='" . $text .
            "', displayed='" . $displayed . "' WHERE clan_rulesID='" .
            $_POST[ "clan_rulesID" ] . "'"
        );

        $id = $_POST[ 'clan_rulesID' ];

        $errors = array();

        //TODO: should be loaded from root language folder
        $_language->readModule('formvalidation', true);

        

        if (count($errors)) {
            $errors = array_unique($errors);
            echo generateErrorBoxFromArray($_language->module['errors_there'], $errors);
        } else {
            redirect("index.php?site=clan_rules_admin", "", 0);
        }
    } else {
		$_language->readModule('formvalidation', true);       
	   echo $_language->module[ 'transaction_invalid' ];
    }
} elseif (isset($_GET[ "delete" ])) {
    $CAPCLASS = new \webspell\Captcha;
    if ($CAPCLASS->checkCaptcha(0, $_GET[ 'captcha_hash' ])) {
        $get = safe_query("SELECT * FROM " . PREFIX . "clan_rules WHERE clan_rulesID='" . $_GET[ "clan_rulesID" ] . "'");
        $data = mysqli_fetch_assoc($get);

        if (safe_query("DELETE FROM " . PREFIX . "clan_rules WHERE clan_rulesID='" . $_GET[ "clan_rulesID" ] . "'")) {
           
            redirect("index.php?site=clan_rules_admin", "", 0);
        } else {
            redirect("index.php?site=clan_rules_admin", "", 0);
        }
    } else {
		print_r($plugin_language); return false;
        $_language->readModule('formvalidation', true);  
        echo $_language->module[ 'transaction_invalid' ];
    }
} else {
echo'<div class="page-header">
    <h2><a href="index.php?site=clan_rules_admin">' . $_lang[ 'clan_rules' ] . '</a></h2>
</div>';
    

    echo
    '<a class="btn btn-danger" href="index.php?site=clan_rules_admin&amp;action=add" class="input">' .
    $_lang[ 'new_clan_rules' ] . '</a><br /><br />';

    echo '<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">' . $_lang[ 'clan_rules' ] . '</h3>
    </div>
    
    
    <div class="table-responsive">
    
    <form method="post" action="index.php?site=clan_rules_admin">

<table class="table">
        <thead>
        <tr>

            <th width="29%" class="title"><b>' . $_lang[ 'clan_rules' ] . '</b></th>
            <th width="15%" class="title"><b>' . $_lang[ 'is_displayed' ] . '</b></th>
            <th width="20%" class="title"><b>' . $_lang[ 'actions' ] . '</b></th>
            <th width="8%" class="title"><b>' . $_lang[ 'sort' ] . '</b></th>
            
        </tr>
        </thead>
        <tbody>
   
        ';

    $CAPCLASS = new \webspell\Captcha;
    $CAPCLASS->createTransaction();
    $hash = $CAPCLASS->getHash();

    $qry = safe_query("SELECT * FROM " . PREFIX . "clan_rules ORDER BY sort");
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
            $displayed = '<font color="green"><b>' . $_lang[ 'yes' ] . '</b></font>' :
            $displayed = '<font color="red"><b>' . $_lang[ 'no' ] . '</b></font>';
            

            
                $title = getinput($ds[ 'title' ]);
           
                $title = getinput($ds[ 'title' ]);
           

            

            echo '<tr>
            <td width="29%" class="' . $td . '">' . $title . '</td>
            
            <td width="15%" class="' . $td . '">' . $displayed . '</td>
            
            <td width="20%" class="' . $td . '" >

            <a class="btn btn-warning btn-sm" href="index.php?site=clan_rules_admin&amp;action=edit&amp;clan_rulesID=' . $ds[ 'clan_rulesID' ] .
                '" class="input">' . $_lang[ 'edit' ] . '</a>

                <input class="btn btn-danger btn-sm" type="button" onclick="MM_confirm(\'' . $_lang[ 'really_delete' ] .
                    '\', \'index.php?site=clan_rules_admin&amp;delete=true&amp;clan_rulesID=' . $ds[ 'clan_rulesID' ] .
                    '&amp;captcha_hash=' . $hash . '\')" value="' . $_lang[ 'delete' ] . '" />

                   
                    </td>
				<td width="8%" class="' . $td . '" align="center"><select name="sort[]">';
            for ($j = 1; $j <= $anz; $j++) {
                if ($ds[ 'sort' ] == $j) {
                    echo '<option value="' . $ds[ 'clan_rulesID' ] . '-' . $j . '" selected="selected">' . $j .
                        '</option>';
                } else {
                    echo '<option value="' . $ds[ 'clan_rulesID' ] . '-' . $j . '">' . $j . '</option>';
                }
            }
            echo '</select>
</td>
</tr>';
            $i++;
        }
    } else {
        echo '<tr><td class="td1" colspan="6">' . $_lang[ 'no_entries' ] . '</td></tr>';
    }

    echo '<tr>
<td class="td_head" colspan="6" align="right"><input type="hidden" name="captcha_hash" value="' . $hash .
    '"><br><input class="btn btn-success" type="submit" name="sortieren" value="' . $_lang[ 'to_sort' ] . '" /></td>
</tr>
</tbody></table>
</form></div></div>';
}
