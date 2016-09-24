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

$_language->readModule('faq', false, true);

if (!ispageadmin($userID) || mb_substr(basename($_SERVER[ 'REQUEST_URI' ]), 0, 15) != "admincenter.php") {
    die($_language->module[ 'access_denied' ]);
}

if (isset($_GET[ 'delete' ])) {
    $faqID = $_GET[ 'faqID' ];
    $CAPCLASS = new \webspell\Captcha;
    if ($CAPCLASS->checkCaptcha(0, $_GET[ 'captcha_hash' ])) {
        safe_query(
            "DELETE FROM
                `" . PREFIX . "faq`
            WHERE
                `faqID` = '" . $faqID . "'"
        );
        \webspell\Tags::removeTags('faq', $faqID);
    } else {
        echo $_language->module[ 'transaction_invalid' ];
    }
} elseif (isset($_POST[ 'sortieren' ])) {
    $sortfaq = $_POST[ 'sortfaq' ];

    $CAPCLASS = new \webspell\Captcha;
    if ($CAPCLASS->checkCaptcha(0, $_POST[ 'captcha_hash' ])) {
        if (is_array($sortfaq)) {
            foreach ($sortfaq as $sortstring) {
                $sorter = explode("-", $sortstring);
                safe_query("UPDATE `" . PREFIX . "faq` SET `sort` = '$sorter[1]' WHERE `faqID` = '" . $sorter[0] . "'");
            }
        }
    } else {
        echo $_language->module[ 'transaction_invalid' ];
    }
} elseif (isset($_POST[ 'save' ])) {
    $faqcat = $_POST[ 'faqcat' ];
    $question = $_POST[ 'question' ];
    $answer = $_POST[ 'message' ];
    $CAPCLASS = new \webspell\Captcha;
    if ($CAPCLASS->checkCaptcha(0, $_POST[ 'captcha_hash' ])) {
        if (checkforempty(array('question', 'message'))) {
            if ($faqcat == "") {
                redirect('admincenter.php?site=faq', $_language->module[ 'no_faq_selected' ], 3);
                exit;
            }
            safe_query(
                "INSERT INTO
                    `" . PREFIX . "faq` (
                        `faqcatID`,
                        `date`,
                        `question`,
                        `answer`,
                        `sort`
                    )
                VALUES (
                    '$faqcat',
                    '" . time() . "',
                    '$question',
                    '$answer',
                    '1'
                )"
            );
            $id = mysqli_insert_id($_database);
            \webspell\Tags::setTags('faq', $id, $_POST[ 'tags' ]);
        } else {
            echo $_language->module[ 'information_incomplete' ];
        }
    } else {
        echo $_language->module[ 'transaction_invalid' ];
    }
} elseif (isset($_POST[ 'saveedit' ])) {
    $faqcat = $_POST[ 'faqcat' ];
    $question = $_POST[ 'question' ];
    $answer = $_POST[ 'message' ];
    $faqID = $_POST[ 'faqID' ];
    $CAPCLASS = new \webspell\Captcha;
    if ($CAPCLASS->checkCaptcha(0, $_POST[ 'captcha_hash' ])) {
        if (checkforempty(array('question', 'message'))) {
            safe_query(
                "UPDATE
                    `" . PREFIX . "faq`
                SET
                    `faqcatID` = '$faqcat',
                    `date` = '" . time() . "',
                    `question` = '$question',
                    `answer` = '$answer'
                WHERE
                    `faqID` = '$faqID'"
            );
            \webspell\Tags::setTags('faq', $faqID, $_POST[ 'tags' ]);
        } else {
            echo $_language->module[ 'information_incomplete' ];
        }
    } else {
        echo $_language->module[ 'transaction_invalid' ];
    }
}

if (isset($_GET[ 'action' ])) {
    if ($_GET[ 'action' ] == "add") {
        $ergebnis = safe_query("SELECT * FROM `" . PREFIX . "faq_categories` ORDER BY `sort`");
        $faqcats = '<select class="form-control" name="faqcat">';
        while ($ds = mysqli_fetch_array($ergebnis)) {
            $faqcats .= '<option value="' . $ds[ 'faqcatID' ] . '">' . getinput($ds[ 'faqcatname' ]) . '</option>';
        }
        $faqcats .= '</select>';

        if (isset($_GET[ 'answer' ])) {
            echo '<span style="color: red">' . $_language->module[ 'no_category_selected' ] . '</span>';
            $question = $_GET[ 'question' ];
            $answer = $_GET[ 'answer' ];
        } else {
            $question = "";
            $answer = "";
        }

        $CAPCLASS = new \webspell\Captcha;
        $CAPCLASS->createTransaction();
        $hash = $CAPCLASS->getHash();

        $_language->readModule('bbcode', true, true);

        $addbbcode = $GLOBALS["_template"]->replaceTemplate("addbbcode", array());
        $addflags = $GLOBALS["_template"]->replaceTemplate("flags_admin", array());

        echo '<div class="panel panel-default">
         <div class="panel-heading">
                            <i class="fa fa-credit-card"></i> '.$_language->module['faq'].'
                        </div>
                        <div class="panel-body">
        <a href="admincenter.php?site=faq" class="white">' . $_language->module[ 'faq' ] .
            '</a> &raquo; ' . $_language->module[ 'add_faq' ] . '<br><br>';

        echo '<script>
            function chkFormular() {
                if(!validbbcode(document.getElementById(\'message\').value, \'admin\')){
                    return false;
                }
            }
        </script>';
		
    echo'<form class="form-horizontal" method="post" id="post" name="post" action="admincenter.php?site=faq" onsubmit="return chkFormular();">
     <div class="row">
	 <div class="form-group">
    <label class="col-sm-2 control-label">'.$_language->module['category'].'</label>
    <div class="col-sm-8"><span class="text-muted small"><em>
      '.$faqcats.'</em></span>
    </div>
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">'.$_language->module['faq'].'</label>
    <div class="col-sm-8"><span class="text-muted small"><em>
		<input class="form-control" type="text" name="question" value="'.$question.'" size="97" /></em></span>
    </div>
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">'.$_language->module['tags'].'</label>
    <div class="col-sm-8"><span class="text-muted small"><em>
		<input class="form-control" type="text" name="tags" value="" size="97" /></em></span>
    </div>
  </div>
   </div>
 

  
  <div class="col-md-12">';

 	$addbbcode = $GLOBALS["_template"]->replaceTemplate("addbbcode", array());
    $addflags = $GLOBALS["_template"]->replaceTemplate("flags_admin", array());
  
  echo ''.$addflags.'<br>'.$addbbcode.'<br>';
  echo '
  <div class="form-group">
    
    <div class="col-md-12"><span class="text-muted small"><em>
      <textarea class="form-control" id="message" name="message" rows="10" cols="" >'.$answer.'</textarea></em></span>
    </div>
  </div>
  <div class="form-group">
    <div class="col-md-12">
		<input type="hidden" name="captcha_hash" value="'.$hash.'" />
		<button class="btn btn-success btn-xs" type="submit" name="save"  />'.$_language->module['add_faq'].'</button>
    </div>
  </div>
    </form></div>
  </div>';
	} elseif ($_GET[ 'action' ] == "edit") {
        $faqID = $_GET[ 'faqID' ];

        $ergebnis = safe_query("SELECT * FROM `" . PREFIX . "faq` WHERE `faqID` = '$faqID'");
        $ds = mysqli_fetch_array($ergebnis);

        $faqcategory = safe_query("SELECT * FROM `" . PREFIX . "faq_categories` ORDER BY `sort`");
        $faqcats = '<select class="form-control" name="faqcat">';
        while ($dc = mysqli_fetch_array($faqcategory)) {
            $selected = '';
            if ($dc[ 'faqcatID' ] == $ds[ 'faqcatID' ]) {
                $selected = ' selected="selected"';
            }
            $faqcats .= '<option value="' . $dc[ 'faqcatID' ] . '"' . $selected . '>' . getinput($dc[ 'faqcatname' ]) .
                '</option>';
        }
        $faqcats .= '</select>';

        $tags = \webspell\Tags::getTags('faq', $faqID);

        $CAPCLASS = new \webspell\Captcha;
        $CAPCLASS->createTransaction();
        $hash = $CAPCLASS->getHash();

        $_language->readModule('bbcode', true, true);

        $tags = \webspell\Tags::getTags('faq', $faqID);
        $addbbcode = $GLOBALS["_template"]->replaceTemplate("addbbcode", array());
        $addflags = $GLOBALS["_template"]->replaceTemplate("flags_admin", array());

        echo '<div class="panel panel-default">
        <div class="panel-heading">
                            <i class="fa fa-credit-card"></i> '.$_language->module['faq'].'
                        </div>
                        <div class="panel-body">
        <a href="admincenter.php?site=faq" class="white">' . $_language->module[ 'faq' ] .
            '</a> &raquo; ' . $_language->module[ 'edit_faq' ] . '<br><br>';

        echo '<script>
            function chkFormular() {
                if(!validbbcode(document.getElementById(\'message\').value, \'admin\')){
                    return false;
                }
            }
        </script>';
		
    echo '<form class="form-horizontal" method="post" id="post" name="post" action="admincenter.php?site=faq" onsubmit="return chkFormular();">
    <div class="row">
	 <div class="form-group">
    <label class="col-sm-2 control-label">'.$_language->module['category'].':</label>
    <div class="col-sm-8"><span class="text-muted small"><em>
      '.$faqcats.'
    </div>
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">'.$_language->module['faq'].':</label>
    <div class="col-sm-8"><span class="text-muted small"><em>
		<input class="form-control" type="text" name="question" value="'.getinput($ds['question']).'" size="97" /></em></span>
    </div>
  </div>
<div class="form-group">
    <label class="col-sm-2 control-label">' . $_language->module[ 'tags' ] . ':</label>
    <div class="col-sm-8"><span class="text-muted small"><em>
		<input class="form-control" type="text" name="tags" value="' . $tags . '" size="97" /></em></span>
	</div>
  </div>


  </div>
 

  
  <div class="col-md-12">';
  
 	$addbbcode = $GLOBALS["_template"]->replaceTemplate("addbbcode", array());
    $addflags = $GLOBALS["_template"]->replaceTemplate("flags_admin", array());
  
  echo ''.$addflags.'<br>'.$addbbcode.'<br>';
  echo '
  <div class="form-group">
    
    <div class="col-md-12"><span class="text-muted small"><em>
      <textarea class="form-control" id="message" name="message" rows="10" cols="" >'.getinput($ds['answer']).'</textarea></em></span>
    </div>
  </div>
  <div class="form-group">
    <div class="col-md-12">
		<input type="hidden" name="captcha_hash" value="'.$hash.'" /><input type="hidden" name="faqID" value="'.$faqID.'" />
		<button class="btn btn-success btn-xs" type="submit" name="saveedit"  />'.$_language->module['edit_faq'].'</button>
    </div>
  </div>

  </div>
    </form></div>
  </div>';
	}
}

else {
	
  echo '<div class="panel panel-default">
  <div class="panel-heading">
                            <i class="fa fa-credit-card"></i> '.$_language->module['faq'].'
                        </div>
                        <div class="panel-body">';
  
  echo'<a href="admincenter.php?site=faq&amp;action=add" class="btn btn-primary btn-xs" type="button">' . $_language->module[ 'new_faq' ] . '</a><br /><br />';	

	echo'<form method="post" action="admincenter.php?site=faq">
  <table class="table table-striped">
    <thead>
      <th><b>' . $_language->module['faq'] . '</b></th>
      <th><b>' . $_language->module['actions'] . '</b></th>
      <th><b>' . $_language->module['sort'] . '</b></th>
    </thead>';

	$ergebnis = safe_query("SELECT * FROM `" . PREFIX . "faq_categories` ORDER BY `sort`");
    $tmp = mysqli_fetch_assoc(safe_query("SELECT count(faqcatID) as cnt FROM `" . PREFIX . "faq_categories`"));
    $anz = $tmp[ 'cnt' ];

    $CAPCLASS = new \webspell\Captcha;
    $CAPCLASS->createTransaction();
    $hash = $CAPCLASS->getHash();
    while ($ds = mysqli_fetch_array($ergebnis)) {
        echo '<tr>
            <td class="td_head" colspan="3">
                <b>' . $ds[ 'faqcatname' ] . '</b><br>
                <small>' . cleartext($ds[ 'description' ], true, 'admin') . '</small>
            </td>
        </tr>';

        $faq = safe_query("SELECT * FROM `" . PREFIX . "faq` WHERE `faqcatID` = '$ds[faqcatID]' ORDER BY `sort`");
        $tmp = mysqli_fetch_assoc(
            safe_query(
                "SELECT count(faqID) as cnt FROM `" . PREFIX . "faq` WHERE `faqcatID` = '$ds[faqcatID]'"
            )
        );
        $anzfaq = $tmp[ 'cnt' ];

        $i = 1;
        while ($db = mysqli_fetch_array($faq)) {
            if ($i % 2) {
                $td = 'td1';
            } else {
                $td = 'td2';
            }

            echo '<tr>
        <td><b>- '.getinput($db['question']).'</b></td>
        <td><a href="admincenter.php?site=faq&amp;action=edit&amp;faqID='.$db['faqID'].'" class="hidden-xs hidden-sm btn btn-warning btn-xs" type="button">' . $_language->module[ 'edit' ] . '</a>

        <input class="hidden-xs hidden-sm btn btn-danger btn-xs" type="button" onclick="MM_confirm(\'' . $_language->module['really_delete'] . '\', \'admincenter.php?site=faq&amp;delete=true&amp;faqID='.$db['faqID'].'&amp;captcha_hash='.$hash.'\')" value="' . $_language->module['delete'] . '" />

	  <a href="admincenter.php?site=faq&amp;action=edit&amp;faqID='.$db['faqID'].'"  class="mobile visible-xs visible-sm" type="button"><i class="fa fa-pencil"></i></a>
      <a class="mobile visible-xs visible-sm" type="button" onclick="MM_confirm(\'' . $_language->module['really_delete'] . '\', \'admincenter.php?site=faq&amp;delete=true&amp;faqID='.$db['faqID'].'&amp;captcha_hash='.$hash.'\')" /><i class="fa fa-times"></i></a>
        </td>
        <td><select name="sortfaq[]">';
            for ($j = 1; $j <= $anzfaq; $j++) {
                if ($db[ 'sort' ] == $j) {
                    echo '<option value="' . $db[ 'faqID' ] . '-' . $j . '" selected="selected">' . $j .
                    '</option>';
                } else {
                    echo '<option value="' . $db[ 'faqID' ] . '-' . $j . '">' . $j . '</option>';
                }
            }
            echo '</select></td></tr>';
      
      $i++;
		}
	}

	echo'<tr>
      <td class="td_head" colspan="3" align="right"><input type="hidden" name="captcha_hash" value="'.$hash.'" /><button class="btn btn-primary btn-xs" type="submit" name="sortieren" />'.$_language->module['to_sort'].'</button></td>
    </tr>
  </table>
  </form>';
}
echo '</div></div>';
?>