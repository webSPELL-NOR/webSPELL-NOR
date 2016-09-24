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

$_language->readModule('faqcategories', false, true);

if (!ispageadmin($userID) || mb_substr(basename($_SERVER[ 'REQUEST_URI' ]), 0, 15) != "admincenter.php") {
    die($_language->module[ 'access_denied' ]);
}

if (isset($_GET[ 'delcat' ])) {
    $faqcatID = $_GET[ 'faqcatID' ];
    $CAPCLASS = new \webspell\Captcha;
    if ($CAPCLASS->checkCaptcha(0, $_GET[ 'captcha_hash' ])) {
        safe_query("DELETE FROM " . PREFIX . "faq WHERE faqcatID='$faqcatID'");
        safe_query("DELETE FROM " . PREFIX . "faq_categories WHERE faqcatID='$faqcatID'");
    } else {
        echo $_language->module[ 'transaction_invalid' ];
    }
} elseif (isset($_POST[ 'sortieren' ])) {
    $sortfaqcat = $_POST[ 'sortfaqcat' ];
    $CAPCLASS = new \webspell\Captcha;
    if ($CAPCLASS->checkCaptcha(0, $_POST[ 'captcha_hash' ])) {
        if (is_array($sortfaqcat)) {
            foreach ($sortfaqcat as $sortstring) {
                $sorter = explode("-", $sortstring);
                safe_query("UPDATE " . PREFIX . "faq_categories SET sort='$sorter[1]' WHERE faqcatID='$sorter[0]' ");
            }
        }
    } else {
        echo $_language->module[ 'transaction_invalid' ];
    }
} elseif (isset($_POST[ 'savecat' ])) {
    $faqcatname = $_POST[ 'faqcatname' ];
    $description = $_POST[ 'message' ];
    $CAPCLASS = new \webspell\Captcha;
    if ($CAPCLASS->checkCaptcha(0, $_POST[ 'captcha_hash' ])) {
        if (checkforempty(array('faqcatname'))) {
            safe_query(
                "INSERT INTO
                    " . PREFIX . "faq_categories (
                        faqcatname,
                        description,
                        sort
                    )
                    VALUES (
                        '$faqcatname',
                        '$description',
                        '1'
                    )"
            );
        } else {
            echo $_language->module[ 'information_incomplete' ];
        }
    } else {
        echo $_language->module[ 'transaction_invalid' ];
    }
} elseif (isset($_POST[ 'saveeditcat' ])) {
    $faqcatname = $_POST[ 'faqcatname' ];
    $description = $_POST[ 'message' ];
    $faqcatID = $_POST[ 'faqcatID' ];
    $CAPCLASS = new \webspell\Captcha;
    if ($CAPCLASS->checkCaptcha(0, $_POST[ 'captcha_hash' ])) {
        if (checkforempty(array('faqcatname'))) {
            safe_query(
                "UPDATE " . PREFIX .
                "faq_categories SET faqcatname='$faqcatname', description='$description' WHERE faqcatID='$faqcatID' "
            );
        } else {
            echo $_language->module[ 'information_incomplete' ];
        }
    } else {
        echo $_language->module[ 'transaction_invalid' ];
    }
}

if (isset($_GET[ 'action' ])) {
    if ($_GET[ 'action' ] == "addcat") {
        $CAPCLASS = new \webspell\Captcha;
        $CAPCLASS->createTransaction();
        $hash = $CAPCLASS->getHash();

        $_language->readModule('bbcode', true, true);

        $addbbcode = $GLOBALS["_template"]->replaceTemplate("addbbcode", array());
        $addflags = $GLOBALS["_template"]->replaceTemplate("flags_admin", array());
    
    echo'<div class="panel panel-default">
    <div class="panel-heading">
                            <i class="fa fa-info"></i> '.$_language->module['faq_categories'].'
                        </div>
            <div class="panel-body">
    <a href="admincenter.php?site=faqcategories" class="white">'.$_language->module['faq_categories'].'</a> &raquo; '.$_language->module['add_category'].'<br><br>';
    
    echo '<script language="JavaScript" type="text/javascript">
					<!--
						function chkFormular() {
							if(!validbbcode(document.getElementById(\'message\').value, \'admin\')){
								return false;
							}
						}
					-->
				</script>';
    
    echo '<form class="form-horizontal" method="post" action="admincenter.php?site=faqcategories" id="post" name="post" enctype="multipart/form-data" onsubmit="return chkFormular();">
	<div class="form-group">
    <label class="col-sm-2 control-label">'.$_language->module['category_name'].':</label>
    <div class="col-sm-8"><span class="text-muted small"><em>
      <input type="text" class="form-control" name="faqcatname" /></em></span>
    </div>
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">'.$_language->module['description'].':</label>
    <div class="col-sm-8"><span class="text-muted small"><em>
     <textarea class="form-control" id="message" rows="10" cols="" name="message"></textarea></em></span>
    </div>
  </div>
  <div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
     <input type="hidden" name="captcha_hash" value="'.$hash.'" /><button class="btn btn-success btn-xs" type="submit" name="savecat" />'.$_language->module['add_category'].'</button>
    </div>
  </div>
    </form>
    </div>
  </div>';
	 } elseif ($_GET[ 'action' ] == "editcat") {
        $faqcatID = $_GET[ 'faqcatID' ];

        $ergebnis = safe_query("SELECT * FROM " . PREFIX . "faq_categories WHERE faqcatID='$faqcatID'");
        $ds = mysqli_fetch_array($ergebnis);

        $CAPCLASS = new \webspell\Captcha;
        $CAPCLASS->createTransaction();
        $hash = $CAPCLASS->getHash();

        $_language->readModule('bbcode', true, true);

        $addbbcode = $GLOBALS["_template"]->replaceTemplate("addbbcode", array());
        $addflags = $GLOBALS["_template"]->replaceTemplate("flags_admin", array());
    
    echo'<div class="panel panel-default"><div class="panel-heading">
                            <i class="fa fa-info"></i> '.$_language->module['faq_categories'].'
                        </div>
            <div class="panel-body">
    <a href="admincenter.php?site=faqcategories" class="white">'.$_language->module['faq_categories'].'</a> &raquo; '.$_language->module['edit_category'].'<br><br>';

    echo '<script language="JavaScript" type="text/javascript">
					<!--
						function chkFormular() {
							if(!validbbcode(document.getElementById(\'message\').value, \'admin\')){
								return false;
							}
						}
					-->
				</script>';
    
    echo '<form class="form-horizontal" method="post" action="admincenter.php?site=faqcategories" id="post" name="post" onsubmit="return chkFormular();">
	<div class="form-group">
    <label class="col-sm-2 control-label">'.$_language->module['category_name'].':</label>
    <div class="col-sm-8"><span class="text-muted small"><em>
      <input type="text" class="form-control" name="faqcatname" value="'.getinput($ds['faqcatname']).'" /></em></span>
    </div>
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">'.$_language->module['description'].':</label>
    <div class="col-sm-8"><span class="text-muted small"><em>
     <textarea class="form-control" id="message" rows="10" cols="" name="message">'.getinput($ds['description']).'</textarea></em></span>
    </div>
  </div>
  <div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
     <input type="hidden" name="captcha_hash" value="'.$hash.'" /><input type="hidden" name="faqcatID" value="'.$faqcatID.'" /><button class="btn btn-success btn-xs" type="submit" name="saveeditcat" />'.$_language->module['edit_category'].'</button>
    </div>
  </div>
    </form>
    </div>
  </div>';
	}
}

else {
	
  echo '<div class="panel panel-default"><div class="panel-heading">
                            <i class="fa fa-info"></i> '.$_language->module['faq_categories'].'
                        </div>
        <div class="panel-body">';
  
  echo'<a href="admincenter.php?site=faqcategories&amp;action=addcat" class="btn btn-primary btn-xs" type="button">' . $_language->module[ 'new_category' ] . '</a><br /><br />';	

	echo'<form method="post" action="admincenter.php?site=faqcategories">
  <table class="table table-striped">
    <thead>
      <th><b>'.$_language->module['faq_categories'].'</b></th>
      <th><b>'.$_language->module['actions'].'</b></th>
      <th><b>'.$_language->module['sort'].'</b></th>
    </thead>';

	$ergebnis = safe_query("SELECT * FROM " . PREFIX . "faq_categories ORDER BY sort");
    $tmp = mysqli_fetch_assoc(safe_query("SELECT count(faqcatID) as cnt FROM " . PREFIX . "faq_categories"));
    $anz = $tmp[ 'cnt' ];

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
            <td class="' . $td . '"><b>' . getinput($ds[ 'faqcatname' ]) . '</b>
            <br />' . cleartext($ds[ 'description' ], true, 'admin') . '</td>
      <td><a href="admincenter.php?site=faqcategories&amp;action=editcat&amp;faqcatID='.$ds['faqcatID'].'" class="hidden-xs hidden-sm btn btn-warning btn-xs" type="button">' . $_language->module[ 'edit' ] . '</a>

        <input class="hidden-xs hidden-sm btn btn-danger btn-xs" type="button" onclick="MM_confirm(\'' . $_language->module['really_delete'] . '\', \'admincenter.php?site=faqcategories&amp;delcat=true&amp;faqcatID='.$ds['faqcatID'].'&amp;captcha_hash='.$hash.'\')" value="' . $_language->module['delete'] . '" />
      
      <a href="admincenter.php?site=faqcategories&amp;action=editcat&amp;faqcatID='.$ds['faqcatID'].'"  class="mobile visible-xs visible-sm" type="button"><i class="fa fa-pencil"></i></a>
     <a class="mobile visible-xs visible-sm" type="button" onclick="MM_confirm(\'' . $_language->module['really_delete'] . '\', \'admincenter.php?site=faqcategories&amp;delcat=true&amp;faqcatID='.$ds['faqcatID'].'&amp;captcha_hash='.$hash.'\')" /><i class="fa fa-times"></i></a></td>
      <td><select name="sortfaqcat[]">';
		
    for ($n = 1; $n <= $anz; $n++) {
            if ($ds[ 'sort' ] == $n) {
                echo '<option value="' . $ds[ 'faqcatID' ] . '-' . $n . '" selected="selected">' . $n . '</option>';
            } else {
                echo '<option value="' . $ds[ 'faqcatID' ] . '-' . $n . '">' . $n . '</option>';
            }
        }
    
		echo'</select></td>
    </tr>';
    
    $i++;
	}
	echo'<tr>
      <td class="td_head" colspan="3" align="right"><input type="hidden" name="captcha_hash" value="'.$hash.'" /><input class="btn btn-primary btn-xs" type="submit" name="sortieren" value="'.$_language->module['to_sort'].'" /></td>
    </tr>
  </table>
  </form>';
}
echo '</div></div>';
?>