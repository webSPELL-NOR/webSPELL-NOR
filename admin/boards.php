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

$_language->readModule('boards', false, true);

if (!isforumadmin($userID) || mb_substr(basename($_SERVER[ 'REQUEST_URI' ]), 0, 15) != "admincenter.php") {
    die($_language->module[ 'access_denied' ]);
}

if (isset($_POST[ 'savemods' ])) {
    $boardID = $_POST[ 'boardID' ];
    if (isset($_POST[ 'mods' ])) {
        $mods = $_POST[ 'mods' ];
        $CAPCLASS = new \webspell\Captcha;
        if ($CAPCLASS->checkCaptcha(0, $_POST[ 'captcha_hash' ])) {
            safe_query("DELETE FROM " . PREFIX . "forum_moderators WHERE boardID='$boardID'");
            if (is_array($mods)) {
                foreach ($mods as $id) {
                    safe_query(
                        "INSERT INTO
                            `" . PREFIX . "forum_moderators` (
                                `boardID`,
                                `userID`
                            )
                            values (
                                '$boardID',
                                '$id'
                            ) "
                    );
                }
            }
        } else {
            echo $_language->module[ 'transaction_invalid' ];
        }
    } else {
        $CAPCLASS = new \webspell\Captcha;
        if ($CAPCLASS->checkCaptcha(0, $_POST[ 'captcha_hash' ])) {
            safe_query(
                "DELETE FROM
                    `" . PREFIX . "forum_moderators`
                WHERE
                    `boardID` = '" .$boardID . "'"
            );
        } else {
            echo $_language->module[ 'transaction_invalid' ];
        }
    }
} elseif (isset($_GET[ 'delete' ])) {
    $boardID = $_GET[ 'boardID' ];
    $CAPCLASS = new \webspell\Captcha;
    if ($CAPCLASS->checkCaptcha(0, $_GET[ 'captcha_hash' ])) {
        safe_query(
            "DELETE FROM
                `" . PREFIX . "forum_posts`
            WHERE
                `boardID` = '" . $boardID . "'"
        );
        safe_query(
            "DELETE
                `topics`.*,
                `moved`.*
            FROM
                `" . PREFIX . "forum_topics` AS `topics`
			LEFT JOIN
			    `" . PREFIX . "forum_topics` AS `moved`
			    ON (`topics`.`topicID` = `moved`.`moveID`)
			WHERE
			    `topics`.`boardID` = '" . $boardID . "'"
        );
        safe_query(
            "DELETE FROM
                `" . PREFIX . "forum_boards`
            WHERE
                `boardID` = '" . $boardID . "' "
        );
        safe_query(
            "DELETE FROM
                `" . PREFIX . "forum_moderators`
            WHERE
                `boardID` = '" . $boardID . "' "
        );
    } else {
        echo $_language->module[ 'transaction_invalid' ];
    }
} elseif (isset($_GET[ 'delcat' ])) {
    $catID = $_GET[ 'catID' ];
    $CAPCLASS = new \webspell\Captcha;
    if ($CAPCLASS->checkCaptcha(0, $_GET[ 'captcha_hash' ])) {
        safe_query(
            "UPDATE
                `" . PREFIX . "forum_boards`
            SET
                `category` = '0'
            WHERE
                `category` = '" . $catID . "'"
        );
        safe_query(
            "DELETE FROM
                `" . PREFIX . "forum_categories`
            WHERE
                `catID` = '" . (int)$catID . "'"
        );
    } else {
        echo $_language->module[ 'transaction_invalid' ];
    }
} elseif (isset($_POST[ 'sortieren' ])) {
    $sortcat = $_POST[ 'sortcat' ];
    $sortboards = $_POST[ 'sortboards' ];
    if (isset($_POST[ "hideboards" ])) {
        $hideboards = $_POST[ 'hideboards' ];
    } else {
        $hideboards = "";
    }

    if (is_array($sortcat)) {
        foreach ($sortcat as $sortstring) {
            $sorter = explode("-", $sortstring);
            safe_query("UPDATE " . PREFIX . "forum_categories SET sort='$sorter[1]' WHERE catID='$sorter[0]' ");
        }
    }
    if (is_array($sortboards)) {
        foreach ($sortboards as $sortstring) {
            $sorter = explode("-", $sortstring);
            safe_query("UPDATE " . PREFIX . "forum_boards SET sort='$sorter[1]' WHERE boardID='$sorter[0]' ");
        }
    }
} elseif (isset($_POST[ 'save' ])) {
    $kath = $_POST[ 'kath' ];
    $name = $_POST[ 'name' ];
    $boardinfo = $_POST[ 'boardinfo' ];
    if (isset($_POST[ 'readgrps' ])) {
        $readgrps = implode(";", $_POST[ 'readgrps' ]);
    } else {
        $readgrps = '';
    }
    if (isset($_POST[ 'writegrps' ])) {
        $writegrps = implode(";", $_POST[ 'writegrps' ]);
    } else {
        $writegrps = '';
    }

    if ($kath == "") {
        $kath = 0;
    }
    $CAPCLASS = new \webspell\Captcha;
    if ($CAPCLASS->checkCaptcha(0, $_POST[ 'captcha_hash' ])) {
        safe_query(
            "INSERT INTO
                `" . PREFIX . "forum_boards` (
                    `category`,
                    `name`,
                    `info`,
                    `readgrps`,
                    `writegrps`,
                    `sort`
                )
                values(
                    '$kath',
                    '$name',
                    '$boardinfo',
                    '$readgrps',
                    '$writegrps',
                    '1'
                )"
        );
    } else {
        echo $_language->module[ 'transaction_invalid' ];
    }
} elseif (isset($_POST[ 'savecat' ])) {
    $catname = $_POST[ 'catname' ];
    $catinfo = $_POST[ 'catinfo' ];
    if (isset($_POST[ 'readgrps' ])) {
        $readgrps = implode(";", $_POST[ 'readgrps' ]);
    } else {
        $readgrps = '';
    }
    $CAPCLASS = new \webspell\Captcha;
    if ($CAPCLASS->checkCaptcha(0, $_POST[ 'captcha_hash' ])) {
        safe_query(
            "INSERT INTO
                `" . PREFIX . "forum_categories` (
                    `readgrps`,
                    `name`,
                    `info`,
                    `sort`
                )
                VALUES (
                    '" . $readgrps . "',
                    '" .$catname . "',
                    '" . $catinfo . "',
                    '1')"
        );
    } else {
        echo $_language->module[ 'transaction_invalid' ];
    }
} elseif (isset($_POST[ 'saveedit' ])) {
    $kath = $_POST[ 'kath' ];
    $name = $_POST[ 'name' ];
    $boardinfo = $_POST[ 'boardinfo' ];
    $boardID = $_POST[ 'boardID' ];
    if (isset($_POST[ 'readgrps' ])) {
        $readgrps = implode(";", $_POST[ 'readgrps' ]);
    } else {
        $readgrps = '';
    }
    if (isset($_POST[ 'writegrps' ])) {
        $writegrps = implode(";", $_POST[ 'writegrps' ]);
    } else {
        $writegrps = '';
    }

    $CAPCLASS = new \webspell\Captcha;
    if ($CAPCLASS->checkCaptcha(0, $_POST[ 'captcha_hash' ])) {
        safe_query(
            "UPDATE
                " . PREFIX . "forum_boards
            SET
                category='$kath',
                name='$name',
                info='$boardinfo',
                readgrps='$readgrps',
                writegrps='$writegrps'
            WHERE
                boardID='$boardID'"
        );
        safe_query(
            "UPDATE
                `" . PREFIX . "forum_topics`
            SET
                `readgrps` = '" . $readgrps . "',
                `writegrps` = '" . $writegrps . "'
            WHERE
                `boardID` = '" . $boardID . "'"
        );
    } else {
        echo $_language->module[ 'transaction_invalid' ];
    }
} elseif (isset($_POST[ 'saveeditcat' ])) {
    $catname = $_POST[ 'catname' ];
    $catinfo = $_POST[ 'catinfo' ];
    $catID = $_POST[ 'catID' ];
    if (isset($_POST[ 'readgrps' ])) {
        $readgrps = implode(";", $_POST[ 'readgrps' ]);
    } else {
        $readgrps = '';
    }
    $CAPCLASS = new \webspell\Captcha;
    if ($CAPCLASS->checkCaptcha(0, $_POST[ 'captcha_hash' ])) {
        safe_query(
            "UPDATE
                " . PREFIX . "forum_categories
            SET
                `readgrps` = '" . $readgrps . "',
                `name` = '" . $catname . "',
                `info` = '" . $catinfo . "'
            WHERE
                `catID` = '" . $catID . "'"
        );
    } else {
        echo $_language->module[ 'transaction_invalid' ];
    }
}

if (isset($_GET[ 'action' ])) {
    $action = $_GET[ 'action' ];
} else {
    $action = '';
}

if ($action == "mods") {
    echo '<div class="panel panel-default">
     <div class="panel-heading">
                            <i class="fa fa-list"></i> '.$_language->module['boards'].'
                        </div>
                        <div class="panel-body">
    <a href="admincenter.php?site=boards" class="white">' . $_language->module[ 'boards' ] .
        '</a> &raquo; ' . $_language->module[ 'moderators' ] . '<br><br>';

    $boardID = $_GET[ 'boardID' ];

    $moderators = safe_query("SELECT * FROM `" . PREFIX . "user_groups` WHERE `moderator` = '1'");
    $ergebnis = safe_query("SELECT * FROM `" . PREFIX . "forum_boards` WHERE `boardID` = '" . $boardID . "'");
    $ds = mysqli_fetch_array($ergebnis);

    echo $_language->module[ 'choose_moderators' ] . ' <span class="text-muted small"><em>' . $ds[ 'name' ] . '</em></span><br><br>';

    echo '<form method="post" action="admincenter.php?site=boards">
  <span class="text-muted small"><em><select class="form-control" name="mods[]" multiple="multiple" size="10">';

    while ($dm = mysqli_fetch_array($moderators)) {
        $nick = getnickname($dm[ 'userID' ]);
        $ismod = mysqli_num_rows(
            safe_query(
                "SELECT
                    *
                FROM
                    `" . PREFIX . "forum_moderators`
                WHERE
                    `boardID` = '" . $boardID . "'
                AND
                    `userID` = '" . $dm[ 'userID' ] . "'"
            )
        );
        if ($ismod) {
            echo '<option value="' . $dm[ 'userID' ] . '" selected="selected">' . $nick . '</option>';
        } else {
            echo '<option value="' . $dm[ 'userID' ] . '">' . $nick . '</option>';
        }
    }
    $CAPCLASS = new \webspell\Captcha;
    $CAPCLASS->createTransaction();
    $hash = $CAPCLASS->getHash();

    echo '</select></em></span><br /><br />
	<input type="hidden" name="captcha_hash" value="' . $hash . '" />
	<input type="hidden" name="boardID" value="' . $boardID . '" />
	<input class="btn btn-success btn-xs" type="submit" name="savemods" value="' . $_language->module[ 'select_moderators' ] . '" />
	</form>
	</div>
  </div>';
} elseif ($action == "add") {
    echo '<div class="panel panel-default">
     <div class="panel-heading">
                            <i class="fa fa-list"></i> '.$_language->module['boards'].'
                        </div>
                        <div class="panel-body">
    <a href="admincenter.php?site=boards" class="white">' . $_language->module[ 'boards' ] .
        '</a> &raquo; ' . $_language->module[ 'add_board' ] . '<br><br>';

    $ergebnis = safe_query(
        "SELECT * FROM `" . PREFIX . "forum_categories` ORDER BY `sort`"
    );
    $cats = '<select class="form-control" name="kath">';
    while ($ds = mysqli_fetch_array($ergebnis)) {
        $cats .= '<option value="' . $ds[ 'catID' ] . '">' . getinput($ds[ 'name' ]) . '</option>';
    }
    $cats .= '</select>';

    $sql = safe_query("SELECT * FROM `" . PREFIX . "forum_groups`");
    $groups = '';
    while ($db = mysqli_fetch_array($sql)) {
        $groups .= '<option value="' . $db[ 'fgrID' ] . '">' . getinput($db[ 'name' ]) . '</option>';
    }
    $CAPCLASS = new \webspell\Captcha;
    $CAPCLASS->createTransaction();
    $hash = $CAPCLASS->getHash();
    echo '<script>
	<!--
	function unselect_all(select_id) {
		select_element = document.getElementById(select_id);
		for(var i = 0; i < select_element.length; i++) {
			select_element.options[i].selected = false;
		}
	}
	-->
	</script>';
	
  echo'<form class="form-horizontal" method="post" action="admincenter.php?site=boards">
   <div class="form-group">
    <label class="col-sm-2 control-label">'.$_language->module['category'].':</label>
    <div class="col-sm-8"><span class="text-muted small"><em>
      '.$cats.'</em></span>
    </div>
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">'.$_language->module['boardname'].':</label>
    <div class="col-sm-8"><span class="text-muted small"><em>
      <input class="form-control" type="text" name="name" /></em></span>
    </div>
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">'.$_language->module['boardinfo'].':</label>
    <div class="col-sm-8"><span class="text-muted small"><em>
      <input class="form-control" type="text" name="boardinfo" /></em></span>
    </div>
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">'.$_language->module['read_right'].':<br><span class="text-muted small"><em><a href="javascript:unselect_all(\'readgrps\');">'.$_language->module['unselect_all'].'</a><br /><br />
      '.$_language->module['read_right_info_board'].'</em></span></label>
    <div class="col-sm-8"><span class="text-muted small"><em>
      <select class="form-control" id="readgrps" name="readgrps[]" multiple="multiple" size="10">
        <option value="user">'.$_language->module['registered_users'].'</option>
        '.$groups.'
      </select></em></span>
    </div>
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">'.$_language->module['write_right'].':<br><span class="text-muted small"><em><a href="javascript:unselect_all(\'writegrps\');">'.$_language->module['unselect_all'].'</a><br /><br />
      '.$_language->module['write_right_info_board'].'</em></span></label>
    <div class="col-sm-8"><span class="text-muted small"><em>
      <select class="form-control" id="writegrps" name="writegrps[]" multiple="multiple" size="10">
        <option value="user" selected="selected">'.$_language->module['registered_users'].'</option>
        '.$groups.'
      </select></em></span>
    </div>
  </div>
  <div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
		<input type="hidden" name="captcha_hash" value="'.$hash.'" />
		<button class="btn btn-success btn-xs" type="submit" name="save" />'.$_language->module['add_board'].'</button>
    </div>
  </div>
  </form>
  </div>
  </div>';
} elseif ($action == "edit") {
    echo '<div class="panel panel-default">
     <div class="panel-heading">
                            <i class="fa fa-list"></i> '.$_language->module['boards'].'
                        </div>
                        <div class="panel-body">
    <a href="admincenter.php?site=boards" class="white">' . $_language->module[ 'boards' ] .
        '</a> &raquo; ' . $_language->module[ 'edit_board' ] . '</h4';

    $boardID = $_GET[ 'boardID' ];

    $ergebnis = safe_query("SELECT * FROM `" . PREFIX . "forum_boards` WHERE `boardID` = '$boardID'");
    $ds = mysqli_fetch_array($ergebnis);

    $category = safe_query("SELECT * FROM `" . PREFIX . "forum_categories` ORDER BY `sort`");
    $cats = '<select class="form-control" name="kath">';
    while ($dc = mysqli_fetch_array($category)) {
        if ($ds[ 'category' ] == $dc[ 'catID' ]) {
            $selected = " selected=\"selected\"";
        } else {
            $selected = "";
        }
        $cats .= '<option value="' . $dc[ 'catID' ] . '"' . $selected . '>' . getinput($dc[ 'name' ]) . '</option>';
    }
    $cats .= '</select>';

    $groups = array();
    $sql = safe_query("SELECT * FROM `" . PREFIX . "forum_groups`");
    while ($db = mysqli_fetch_array($sql)) {
        $groups[ $db[ 'fgrID' ] ] = $db[ 'name' ];
    }

    $readgrps = '';
    $writegrps = '';

    $grps = explode(";", $ds[ 'readgrps' ]);
    if (in_array('user', $grps)) {
        $readgrps .= '<option value="user" selected="selected">' . $_language->module[ 'registered_users' ] .
            '</option>';
    } else {
        $readgrps .= '<option value="user">' . $_language->module[ 'registered_users' ] . '</option>';
    }
    foreach ($groups as $fgrID => $name) {
        if (in_array($fgrID, $grps)) {
            $selected = ' selected="selected"';
        } else {
            $selected = '';
        }
        $readgrps .= '<option value="' . $fgrID . '"' . $selected . '>' . getinput($name) . '</option>';
    }

    $grps = explode(";", $ds[ 'writegrps' ]);
    if (in_array('user', $grps)) {
        $writegrps .= '<option value="user" selected="selected">' . $_language->module[ 'registered_users' ] .
            '</option>';
    } else {
        $writegrps .= '<option value="user">' . $_language->module[ 'registered_users' ] . '</option>';
    }
    foreach ($groups as $fgrID => $name) {
        if (in_array($fgrID, $grps)) {
            $selected = ' selected="selected"';
        } else {
            $selected = '';
        }
        $writegrps .= '<option value="' . $fgrID . '"' . $selected . '>' . getinput($name) . '</option>';
    }
    $CAPCLASS = new \webspell\Captcha;
    $CAPCLASS->createTransaction();
    $hash = $CAPCLASS->getHash();

    echo '<script>
	<!--
	function unselect_all(select_id) {
		select_element = document.getElementById(select_id);
		for(var i = 0; i < select_element.length; i++) {
			select_element.options[i].selected = false;
		}
	}
	-->
	</script>';
	
  echo'<form class="form-horizontal" method="post" action="admincenter.php?site=boards">
  <div class="form-group">
    <label class="col-sm-2 control-label">'.$_language->module['category'].':</label>
    <div class="col-sm-8"><span class="text-muted small"><em>
      '.$cats.'</em></span>
    </div>
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">'.$_language->module['boardname'].':</label>
    <div class="col-sm-8"><span class="text-muted small"><em>
      <input class="form-control" type="text" name="name" value="'.getinput($ds['name']).'" /></em></span>
    </div>
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">'.$_language->module['boardinfo'].':</label>
    <div class="col-sm-8"><span class="text-muted small"><em>
      <input class="form-control" type="text" name="boardinfo" value="'.getinput($ds['info']).'" /></em></span>
    </div>
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">'.$_language->module['read_right'].':<br><span class="text-muted small"><em><a href="javascript:unselect_all(\'readgrps\');">'.$_language->module['unselect_all'].'</a><br /><br />
      '.$_language->module['read_right_info_board'].'</em></span></label>
    <div class="col-sm-8"><span class="text-muted small"><em>
      <select class="form-control" id="readgrps" name="readgrps[]" multiple="multiple" size="10">'.$readgrps.'</select></em></span>
    </div>
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">'.$_language->module['write_right'].':<br><span class="text-muted small"><em><a href="javascript:unselect_all(\'writegrps\');">'.$_language->module['unselect_all'].'</a><br /><br />
      '.$_language->module['write_right_info_board'].'</em></span></label>
    <div class="col-sm-8"><span class="text-muted small"><em>
      <select class="form-control" id="writegrps" name="writegrps[]" multiple="multiple" size="10">'.$writegrps.'</select></em></span>
    </div>
  </div>
  <div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
		<input type="hidden" name="captcha_hash" value="'.$hash.'" /><input type="hidden" name="boardID" value="'.$boardID.'" />
		<button class="btn btn-success btn-xs" type="submit" name="saveedit" />'.$_language->module['edit_board'].'</button>
    </div>
  </div>
  </form>
  </div>
  </div>';
} elseif ($action == "addcat") {
    echo '<div class="panel panel-default">
     <div class="panel-heading">
                            <i class="fa fa-list"></i> '.$_language->module['boards'].'
                        </div>
                        <div class="panel-body">
    <a href="admincenter.php?site=boards" class="white">' . $_language->module[ 'boards' ] .
        '</a> &raquo; ' . $_language->module[ 'add_category' ] . '<br><br>';

    $sql = safe_query("SELECT * FROM `" . PREFIX . "forum_groups`");
    $groups = '<select class="form-control" id="readgrps" name="readgrps[]" multiple="multiple" size="10">
  <option value="user">' . $_language->module[ 'registered_users' ] . '</option>';
    while ($db = mysqli_fetch_array($sql)) {
        $groups .= '<option value="' . $db[ 'fgrID' ] . '">' . getinput($db[ 'name' ]) . '</option>';
    }
    $groups .= '</select>';
    $CAPCLASS = new \webspell\Captcha;
    $CAPCLASS->createTransaction();
    $hash = $CAPCLASS->getHash();
    echo '<script>
	<!--
	function unselect_all(select_id) {
		select_element = document.getElementById(select_id);
		for(var i = 0; i < select_element.length; i++) {
			select_element.options[i].selected = false;
		}
	}
	-->
	</script>';
	
  echo'<form class="form-horizontal" method="post" action="admincenter.php?site=boards" enctype="multipart/form-data">
  <div class="form-group">
    <label class="col-sm-2 control-label">'.$_language->module['category_name'].':</label>
    <div class="col-sm-8"><span class="text-muted small"><em>
      <input class="form-control" type="text" name="catname" /></em></span>
    </div>
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">'.$_language->module['category_info'].':</label>
    <div class="col-sm-8"><span class="text-muted small"><em>
      <input class="form-control" type="text" name="catinfo" /></em></span>
    </div>
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">'.$_language->module['read_right'].':<br><span class="text-muted small"><em><a href="javascript:unselect_all(\'readgrps\');">'.$_language->module['unselect_all'].'</a><br /><br />
      '.$_language->module['right_info_category'].'</em></span></label>
    <div class="col-sm-8"><span class="text-muted small"><em>
      '.$groups.'</em></span>
    </div>
  </div>
  <div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
		<input type="hidden" name="captcha_hash" value="'.$hash.'" />
		<button class="btn btn-success btn-xs" type="submit" name="savecat" />'.$_language->module['add_category'].'</button>
    </div>
  </div>
  </form>
  </div>
  </div>';
} elseif ($action == "editcat") {
    echo '<div class="panel panel-default">
     <div class="panel-heading">
                            <i class="fa fa-list"></i> '.$_language->module['boards'].'
                        </div>
                        <div class="panel-body">
    <a href="admincenter.php?site=boards" class="white">' . $_language->module[ 'boards' ] .
        '</a> &raquo; ' . $_language->module[ 'edit_category' ] . '<br><br>';

    $catID = $_GET[ 'catID' ];

    $ergebnis = safe_query("SELECT * FROM `" . PREFIX . "forum_categories` WHERE `catID` = '$catID'");
    $ds = mysqli_fetch_array($ergebnis);

    $usergrps = explode(";", $ds[ 'readgrps' ]);
    $sql = safe_query("SELECT * FROM `" . PREFIX . "forum_groups`");
    $groups = '<select class="form-control" id="readgrps" name="readgrps[]" multiple="multiple" size="10">';
    if (in_array('user', $usergrps)) {
        $groups .= '<option value="user" selected="selected">' . $_language->module[ 'registered_users' ] . '</option>';
    } else {
        $groups .= '<option value="user">' . $_language->module[ 'registered_users' ] . '</option>';
    }
    while ($db = mysqli_fetch_array($sql)) {
        if (in_array($db[ 'fgrID' ], $usergrps)) {
            $selected = ' selected="selected"';
        } else {
            $selected = '';
        }
        $groups .= '<option value="' . $db[ 'fgrID' ] . '" ' . $selected . '>' . getinput($db[ 'name' ]) . '</option>';
    }
    $groups .= '</select>';
    $CAPCLASS = new \webspell\Captcha;
    $CAPCLASS->createTransaction();
    $hash = $CAPCLASS->getHash();
    echo '<script>
	<!--
	function unselect_all(select_id) {
		select_element = document.getElementById(select_id);
		for(var i = 0; i < select_element.length; i++) {
			select_element.options[i].selected = false;
		}
	}
	-->
	</script>';
	
  echo'<form class="form-horizontal" method="post" action="admincenter.php?site=boards">
  <div class="form-group">
    <label class="col-sm-2 control-label">'.$_language->module['category_name'].':</label>
    <div class="col-sm-8"><span class="text-muted small"><em>
      <input class="form-control" type="text" name="catname" value="'.getinput($ds['name']).'" /></em></span>
    </div>
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">'.$_language->module['category_info'].':</label>
    <div class="col-sm-8"><span class="text-muted small"><em>
      <input class="form-control" type="text" name="catinfo" value="'.getinput($ds['info']).'" /></em></span>
    </div>
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">'.$_language->module['read_right'].':<br><span class="text-muted small"><em><a href="javascript:unselect_all(\'readgrps\');">'.$_language->module['unselect_all'].'</a><br /><br />
      '.$_language->module['right_info_category'].'</em></span></label>
    <div class="col-sm-8"><span class="text-muted small"><em>
      '.$groups.'</em></span>
    </div>
  </div>
  <div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
		<input type="hidden" name="captcha_hash" value="'.$hash.'" /><input type="hidden" name="catID" value="'.$catID.'" />
		<button class="btn btn-success btn-xs" type="submit" name="saveeditcat" />'.$_language->module['edit_category'].'</button>
    </div>
  </div>
  </form>
  </div>
  </div>';
}

else {

	echo'<div class="panel panel-default">
    <div class="panel-heading">
                            <i class="fa fa-list"></i> '.$_language->module['boards'].'
                        </div>

                        <div class="panel-body">';

	echo'
	<a href="admincenter.php?site=boards&amp;action=addcat" class="btn btn-primary btn-xs" type="button">' . $_language->module[ 'new_category' ] . '</a>
	<a href="admincenter.php?site=boards&amp;action=add" class="btn btn-primary btn-xs" type="button">' . $_language->module[ 'new_board' ] . '</a><br /><br />';	

	echo'<form method="post" action="admincenter.php?site=boards">
  <table class="table">
    <thead>
      <th><b>'.$_language->module['boardname'].'</b></th>
      <th><b>'.$_language->module['mods'].'</b></th>
      <th><b>'.$_language->module['actions'].'</b></th>
      <th><b>'.$_language->module['sort'].'</b></th>
    </thead>';

	$ergebnis = safe_query("SELECT * FROM `" . PREFIX . "forum_categories` ORDER BY `sort`");
    $tmp = mysqli_fetch_assoc(safe_query("SELECT count(catID) as cnt FROM `" . PREFIX . "forum_categories`"));
    $anz = $tmp[ 'cnt' ];

    $CAPCLASS = new \webspell\Captcha;
    $CAPCLASS->createTransaction();
    $hash = $CAPCLASS->getHash();
    while ($ds = mysqli_fetch_array($ergebnis)) {
        echo '<tr class="breadcrumb">
	      <td><b>'.getinput($ds['name']).'</b><br /><small>'.getinput($ds['info']).'</small></td>
	      <td></td>
	      <td>

	      <a href="admincenter.php?site=boards&amp;action=editcat&amp;catID='.$ds['catID'].'" class="hidden-xs hidden-sm btn btn-warning btn-xs" type="button">' . $_language->module[ 'edit' ] . '</a>
        <input class="hidden-xs hidden-sm btn btn-danger btn-xs" type="button" onclick="MM_confirm(\'' . $_language->module['really_delete_category'] . '\', \'admincenter.php?site=boards&amp;delcat=true&amp;catID='.$ds['catID'].'&amp;captcha_hash='.$hash.'\')" value="' . $_language->module['delete'] . '" />


	  <a href="admincenter.php?site=boards&amp;action=editcat&amp;catID='.$ds['catID'].'"  class="mobile visible-xs visible-sm" type="button"><i class="fa fa-pencil"></i></a>
      <a class="mobile visible-xs visible-sm" type="button" onclick="MM_confirm(\'' . $_language->module['really_delete_category'] . '\', \'admincenter.php?site=boards&amp;delcat=true&amp;catID='.$ds['catID'].'&amp;captcha_hash='.$hash.'\')" /><i class="fa fa-times"></i></a>


	      </td>
	      <td><select name="sortcat[]">';
      
		for ($n = 1; $n <= $anz; $n++) {
            if ($ds[ 'sort' ] == $n) {
                echo '<option value="' . $ds[ 'catID' ] . '-' . $n . '" selected="selected">' . $n . '</option>';
            } else {
                echo '<option value="' . $ds[ 'catID' ] . '-' . $n . '">' . $n . '</option>';
            }
        }

        echo '</select></td>
	    </tr>';

        $boards = safe_query(
            "SELECT * FROM `" . PREFIX . "forum_boards` WHERE `category` = '" . $ds[ 'catID' ] . "' ORDER BY `sort`"
        );
        $tmp = mysqli_fetch_assoc(
            safe_query(
                "SELECT count(boardID) as cnt FROM `" . PREFIX . "forum_boards` WHERE `category` = '$ds[catID]'"
            )
        );
        $anzboards = $tmp[ 'cnt' ];

        $i = 1;
        $CAPCLASS = new \webspell\Captcha;
        $CAPCLASS->createTransaction();
        $hash = $CAPCLASS->getHash();
        while ($db = mysqli_fetch_array($boards)) {
            if ($i % 2) {
                $td = 'td1';
            } else {
                $td = 'td2';
            }

            echo '<tr>
	        <td class="'.$td.'">'.$db['name'].'<br /><small>'.$db['info'].'</small></td>
	        <td class="'.$td.'">

<a href="admincenter.php?site=boards&amp;action=mods&amp;boardID='.$db['boardID'].'" class="hidden-xs hidden-sm btn btn-primary btn-xs" type="button">' . $_language->module[ 'mods' ] . '</a>
<a href="admincenter.php?site=boards&amp;action=mods&amp;boardID='.$db['boardID'].'" class="mobile visible-xs visible-sm" type="button"><i class="fa fa-pencil"></i></a>

			</td>
	        <td class="'.$td.'">

	        <a href="admincenter.php?site=boards&amp;action=edit&amp;boardID='.$db['boardID'].'" class="hidden-xs hidden-sm btn btn-warning btn-xs" type="button">' . $_language->module[ 'edit' ] . '</a>

        <input class="hidden-xs hidden-sm btn btn-danger btn-xs" type="button" onclick="MM_confirm(\'' . $_language->module['really_delete_board'] . '\', \'admincenter.php?site=boards&amp;delete=true&amp;boardID='.$db['boardID'].'&amp;captcha_hash='.$hash.'\')" value="' . $_language->module['delete'] . '" />

	  
	  <a href="admincenter.php?site=boards&amp;action=edit&amp;boardID='.$db['boardID'].'"  class="mobile visible-xs visible-sm" type="button"><i class="fa fa-pencil"></i></a>
      <a class="mobile visible-xs visible-sm" type="button" onclick="MM_confirm(\'' . $_language->module['really_delete_board'] . '\', \'admincenter.php?site=boards&amp;delete=true&amp;boardID='.$db['boardID'].'&amp;captcha_hash='.$hash.'\')" /><i class="fa fa-times"></i></a>


	        </td>
	        <td class="'.$td.'"><select name="sortboards[]">';

            for ($j = 1; $j <= $anzboards; $j++) {
                if ($db[ 'sort' ] == $j) {
                    echo '<option value="' . $db[ 'boardID' ] . '-' . $j . '" selected="selected">' . $j . '</option>';
                } else {
                    echo '<option value="' . $db[ 'boardID' ] . '-' . $j . '">' . $j . '</option>';
                }
            }

            echo '</select></td>
	      </tr>';

            $i++;
        }
    }

    $boards = safe_query("SELECT * FROM `" . PREFIX . "forum_boards` WHERE `category`='0' ORDER BY `sort`");
    $tmp = mysqli_fetch_assoc(
        safe_query(
            "SELECT count(boardID) as cnt FROM `" . PREFIX . "forum_boards` WHERE `category` = '0'"
        )
    );
    $anzboards = $tmp[ 'cnt' ];
    $CAPCLASS = new \webspell\Captcha;
    $CAPCLASS->createTransaction();
    $hash = $CAPCLASS->getHash();
    while ($db = mysqli_fetch_array($boards)) {


		echo'<tr bgcolor="#dcdcdc">
      <td bgcolor="#FFFFFF"><b>'.getinput($db['name']).'</b></td>
      <td bgcolor="#FFFFFF">

      
      <a href="admincenter.php?site=boards&amp;action=mods&amp;boardID='.$db['boardID'].'" class="hidden-xs hidden-sm btn btn-primary btn-xs" type="button">' . $_language->module[ 'mods' ] . '</a>
<a href="admincenter.php?site=boards&amp;action=mods&amp;boardID='.$db['boardID'].'" class="mobile visible-xs visible-sm" type="button"><i class="fa fa-pencil"></i></a>
      </td>
      <td bgcolor="#FFFFFF">

      <a href="admincenter.php?site=boards&amp;action=edit&amp;boardID='.$db['boardID'].'" class="hidden-xs hidden-sm btn btn-warning btn-xs" type="button">' . $_language->module[ 'edit' ] . '</a>

        <input class="hidden-xs hidden-sm btn btn-danger btn-xs" type="button" onclick="MM_confirm(\'' . $_language->module['really_delete'] . '\', \'admincenter.php?site=boards&amp;delete=true&amp;boardID='.$db['boardID'].'&amp;captcha_hash='.$hash.'\')" value="' . $_language->module['delete'] . '" />

	  
	  <a href="admincenter.php?site=boards&amp;action=edit&amp;boardID='.$db['boardID'].'" class="mobile visible-xs visible-sm" type="button"><i class="fa fa-pencil"></i></a>
      <a class="mobile visible-xs visible-sm" type="button" onclick="MM_confirm(\'' . $_language->module['really_delete'] . '\', \'admincenter.php?site=boards&amp;delete=true&amp;boardID='.$db['boardID'].'&amp;captcha_hash='.$hash.'\')" /><i class="fa fa-times"></i></a></td>
      <td bgcolor="#FFFFFF"><select name="sort[]">';

        for ($n = 1; $n <= $anzboards; $n++) {
            if ($ds[ 'sort' ] == $n) {
                echo '<option value="' . $db[ 'boardID' ] . '-' . $n . '" selected="selected">' . $n . '</option>';
            } else {
                echo '<option value="' . $db[ 'boardID' ] . '-' . $n . '">' . $n . '</option>';
            }
        }
            echo '</select></td></tr>';
	}
	
  echo'<tr>
      <td colspan="5" align="right"><input class="btn btn-primary btn-xs" type="submit" name="sortieren" value="'.$_language->module['to_sort'].'" /></td>
    </tr>
  </table>
  </form>';
}
echo '</div></div>';
?>