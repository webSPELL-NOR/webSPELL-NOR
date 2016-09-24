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

$_language->readModule('history', false, true);

if (!ispageadmin($userID) || mb_substr(basename($_SERVER[ 'REQUEST_URI' ]), 0, 15) != "admincenter.php") {
    die($_language->module[ 'access_denied' ]);
}

#echo '<h1>&curren; ' . $_language->module[ 'history' ] . '</h1>';

if (isset($_POST[ 'submit' ])) {
    $history = $_POST[ 'message' ];
    $CAPCLASS = new \webspell\Captcha;
    if ($CAPCLASS->checkCaptcha(0, $_POST[ 'captcha_hash' ])) {
        if (mysqli_num_rows(safe_query("SELECT * FROM " . PREFIX . "history"))) {
            safe_query("UPDATE " . PREFIX . "history SET history='" . $history . "'");
        } else {
            safe_query("INSERT INTO " . PREFIX . "history (history) values( '" . $history . "') ");
        }
    } else {
        echo $_language->module[ 'transaction_invalid' ];
    }
}
$ergebnis = safe_query("SELECT * FROM " . PREFIX . "history");
$ds = mysqli_fetch_array($ergebnis);
$CAPCLASS = new \webspell\Captcha;
$CAPCLASS->createTransaction();
$hash = $CAPCLASS->getHash();

$_language->readModule('bbcode', true, true);

$addbbcode = $GLOBALS["_template"]->replaceTemplate("addbbcode", array());
$addflags = $GLOBALS["_template"]->replaceTemplate("flags_admin", array());

echo'<div class="panel panel-default">
<div class="panel-heading">
                            <i class="fa fa-history"></i> ' . $_language->module[ 'history' ] . '
                        </div>
                  <div class="panel-body">

<div class="row">
<div class="col-md-12">';

echo '<script>
					<!--
						function chkFormular() {
							if(!validbbcode(document.getElementById(\'message\').value, \'admin\')){
								return false;
							}
						}
					-->
				</script>';
  
	echo'<form class="form-horizontal" method="post" id="post" name="post" action="admincenter.php?site=history" onsubmit="return chkFormular();">
  <br /><small>'.$_language->module['you_can_use_html'].'</small><br /><br />';
  
	echo '
		      <div class="col-md-12 hidden-xs hidden-sm">
			  '.$addflags.'<br>
		        '.$addbbcode.'<br></div>';
			
	echo '<br /><textarea class="form-control" id="message" rows="25" cols="" name="message" style="width: 100%;">'.getinput($ds['history']).'</textarea>
  <br /><br /><input type="hidden" name="captcha_hash" value="'.$hash.'" /><button class="btn btn-success btn-xs" type="submit" name="submit" />'.$_language->module['update'].'</button>
  </form>
  </div>
  </div><div>
  </div>';
  
?>