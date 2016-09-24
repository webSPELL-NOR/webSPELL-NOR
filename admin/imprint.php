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

$_language->readModule('imprint', false, true);

if (!ispageadmin($userID) || mb_substr(basename($_SERVER[ 'REQUEST_URI' ]), 0, 15) != "admincenter.php") {
    die($_language->module[ 'access_denied' ]);
}

#echo '<h1>&curren; ' . $_language->module[ 'imprint' ] . '</h1>';

if (isset($_POST[ 'submit' ])) {
    $imprint = $_POST[ 'message' ];
    $CAPCLASS = new \webspell\Captcha;
    if ($CAPCLASS->checkCaptcha(0, $_POST[ 'captcha_hash' ])) {
        safe_query("UPDATE `" . PREFIX . "settings` SET imprint='" . $_POST[ 'type' ] . "'");

        if (mysqli_num_rows(safe_query("SELECT * FROM `" . PREFIX . "imprint`"))) {
            safe_query("UPDATE `" . PREFIX . "imprint` SET imprint='$imprint'");
        } else {
            safe_query("INSERT INTO `" . PREFIX . "imprint` (imprint) values( '$imprint') ");
        }
        redirect("admincenter.php?site=imprint", "", 0);
    } else {
        echo $_language->module[ 'transaction_invalid' ];
    }
} else {
    $type1 = '';
    $type0 = '';
    if ($imprint_type) {
        $type1 = 'checked="checked"';
    } else {
        $type0 = 'checked="checked"';
    }

    $ergebnis = safe_query("SELECT * FROM `" . PREFIX . "imprint`");
    $ds = mysqli_fetch_array($ergebnis);
    $CAPCLASS = new \webspell\Captcha;
    $CAPCLASS->createTransaction();
    $hash = $CAPCLASS->getHash();

    $_language->readModule('bbcode', true, true);

    $addbbcode = $GLOBALS["_template"]->replaceTemplate("addbbcode", array());
    $addflags = $GLOBALS["_template"]->replaceTemplate("flags_admin", array());
	
  echo '<script language="JavaScript" type="text/javascript">
					<!--
						function chkFormular() {
							if(!validbbcode(document.getElementById(\'message\').value, \'admin\')){
								return false;
							}
						}
					-->
				</script>';
  
	echo'<div class="panel panel-default">
    <div class="panel-heading">
                            <i class="fa fa-product-hunt"></i> ' . $_language->module['imprint'] . '
                        </div>
<div class="panel-body">

<div class="row">
<div class="col-md-12">
<form class="form-horizontal" method="post" id="post" name="post" action="admincenter.php?site=imprint" onsubmit="return chkFormular();">
  <input type="radio" name="type" value="0" ' . $type0 . ' /> ' . $_language->module['automatic'] . '<br />
  <input type="radio" name="type" value="1" ' . $type1 . ' /> ' . $_language->module['manual'] . '<br /><br /><b>' . $_language->module['imprint'] . '</b><br /><small>' . $_language->module['you_can_use_html'] . '</small><br /><br />';
	
	echo '<div class="col-md-12">
			  ' . $addflags . '<br>
		        ' . $addbbcode . '<br></div>';
	
  echo '<br /><textarea class="form-control" id="message" name="message" rows="25" cols="" style="width: 100%;">' . getinput($ds['imprint']) . '</textarea><br /><br /><input type="hidden" name="captcha_hash" value="' . $hash . '" />
  <button class="btn btn-success btn-xs" type="submit" name="submit"  />' . $_language->module['update'] . '</button>
  </form></div></div></div></div>';
}
?>