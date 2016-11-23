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

$_language->readModule('newsletter', false, true);

if (!isuseradmin($userID) || mb_substr(basename($_SERVER[ 'REQUEST_URI' ]), 0, 15) != "admincenter.php") {
    die($_language->module[ 'access_denied' ]);
}

#echo '<h1>&curren; ' . $_language->module[ 'newsletter' ] . '</h1>';

if (isset($_POST[ 'send' ]) || isset($_POST[ 'testen' ])) {
    $CAPCLASS = new \webspell\Captcha;
    if ($CAPCLASS->checkCaptcha(0, $_POST[ 'captcha_hash' ])) {
        $title = $_POST[ 'title' ];
        $testmail = $_POST[ 'testmail' ];
        $date = getformatdate(time());
        $message = str_replace('\r\n', "\n", $_POST[ 'message' ]);
        $message_html = nl2br($message);
        $receptionists = $_language->module[ 'receptionists' ];
        $error_send = $_language->module[ 'error_send' ];

        //use page's default language for newsletter
        $_language->setLanguage($default_language, true);
        $_language->readModule('newsletter', false, true);
        $no_htmlmail = $_language->module[ 'no_htmlmail' ];
        $remove = $_language->module[ 'remove' ];
        $profile = $_language->module[ 'profile' ];

        $emailbody = '<!--
' . $no_htmlmail . '
' . stripslashes($message) . '
 -->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
      <html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
	<head>
		<title>webSPELL Newsletter</title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<style type="text/css">
<!--
body { font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 11px; color: #666666; background-color: #FFFFFF;
border: 0px; margin: 5px; }
h3 {font-size: 16px; color: #515151; margin: 10px; padding: 0px; margin-top: 25px; text-align: center; }
img { border: none; }
.center { margin-left: auto; margin-right: auto; }
#newsletter { width: 650px; }
#footer { color: #8C8C8C; }
hr { height: 1px; background-color: #cdcdcd; color: #cdcdcd; border: none; margin: 6px 0px; }
a { color: #0066FF; text-decoration: none; }
a:hover { text-decoration: underline; }
-->
</style>
<!--[if lte IE 7]>
<style type="text/css">
hr { margin: 0px; }
</style>
<![endif]-->
	</head>
	<body>
		<div id="newsletter" class="center">
		<a href="http://' . $hp_url . '" target="_blank" ><img src="http://' . $hp_url .
            '/images/banner.gif" alt="" class="center" style="display: block;"></a>
			<h3>' . stripslashes($title) . '</h3>
			<span>' . stripslashes($message_html) . '</span>
			<hr>
			<span id="footer">' . $remove . ' <a href="http://' . $hp_url . '/index.php?site=myprofile">' . $profile .
            '</a>.</span>
		</div>
	</body>
</html>';

        if (isset($_POST[ 'testen' ])) {
            $bcc[ ] = $testmail;
            $_SESSION[ 'emailbody' ] = $message;
            $_SESSION[ 'title' ] = $title;
        } else {
            $emails = array();
            //clanmember

            if (isset($_POST[ 'sendto_clanmembers' ])) {
                $ergebnis = safe_query("SELECT userID FROM " . PREFIX . "squads_members GROUP BY userID");
                $anz = mysqli_num_rows($ergebnis);
                if ($anz) {
                    while ($ds = mysqli_fetch_array($ergebnis)) {
                        $emails[ ] = getemail($ds[ 'userID' ]);
                    }
                }
            }

            if (isset($_POST[ 'sendto_registered' ])) {
                $ergebnis = safe_query("SELECT * FROM " . PREFIX . "user WHERE newsletter='1'");
                $anz = mysqli_num_rows($ergebnis);
                if ($anz) {
                    while ($ds = mysqli_fetch_array($ergebnis)) {
                        $emails[ ] = $ds[ 'email' ];
                    }
                }
            }

            if (isset($_POST[ 'sendto_newsletter' ])) {
                $ergebnis = safe_query("SELECT * FROM " . PREFIX . "newsletter");
                $anz = mysqli_num_rows($ergebnis);
                if ($anz) {
                    while ($ds = mysqli_fetch_array($ergebnis)) {
                        $emails[ ] = $ds[ 'email' ];
                    }
                }
            }

            $bcc = $emails;
        }

        $success = true;
        $bcc = array_unique($bcc);
        $subject = $hp_title . " Newsletter";
        foreach ($bcc as $mailto) {
            $sendmail = \webspell\Email::sendEmail($admin_email, 'Newsletter', $mailto, $subject, $emailbody);
            if ($sendmail['result'] == 'fail') {
                $success = false;
            }
        }
        if ($success) {
            echo '<b>' . $receptionists . '</b><br /><br />' . implode(", ", $bcc);
            if (isset($sendmail['debug'])) {
                echo '<b> Debug </b>';
                echo '<br>' . $sendmail['debug'];
            }
        } else {
            if (isset($sendmail['debug'])) {
                echo '<b>' . $error_send . '</b>';
                echo '<br>' . $sendmail['error'];
                echo '<br>' . $sendmail['debug'];
            } else {
                echo '<b>' . $error_send . '</b>';
                echo '<br>' . $sendmail['error'];
            }
        }
        redirect("admincenter.php?site=newsletter", "", 5);
    } else {
        echo $_language->module[ 'transaction_invalid' ];
    }
} else {
    $CAPCLASS = new \webspell\Captcha;
    $CAPCLASS->createTransaction();
    $hash = $CAPCLASS->getHash();

    if (isset($_SESSION[ 'emailbody' ])) {
        $message = htmlspecialchars(stripslashes($_SESSION[ 'emailbody' ]));
    } else {
        $message = null;
    }
    if (isset($_SESSION[ 'title' ])) {
        $title = htmlspecialchars(stripslashes($_SESSION[ 'title' ]));
    } else {
        $title = null;
    }
    ?>



<div class="panel panel-default">
<div class="panel-heading">
                            <i class="fa fa-newspaper-o"></i> <?php echo $_language->module['newsletter']; ?>
                        </div>


<div class="panel-body">


<form class="form-horizontal" action="admincenter.php?site=newsletter" method="post">
<div class="form-group">
    <label class="col-sm-2 control-label"><?php echo $_language->module['title']; ?>:</label>
    <div class="col-sm-8">
      <input type="text" class="form-control" name="title" value="<?php echo $title;?>"/>
    </div>
  </div>
<div class="form-group">
    <label class="col-sm-2 control-label"><?php echo $_language->module['html_mail']; ?>:</label>
    <div class="col-sm-8">
      <textarea rows="15" class="form-control" cols=""  name="message"><?php echo $message;?></textarea>
    </div>
  </div>
<div class="form-group">
    <label class="col-sm-2 control-label"><?php echo $_language->module['test_newsletter']; ?>:</label>
    <div class="col-sm-5">
<input type="text" class="form-control" name="testmail" value="user@inter.net"/></div>
<div class="col-sm-5"><br><button class="btn btn-primary btn-xs" type="submit" name="testen" /><?php echo $_language->module['test']; ?></button>
    </div>
  </div>
<div class="form-group">
    <label class="col-sm-2 control-label"><?php echo $_language->module['send_to']; ?>:</label>
    <div class="col-sm-8">
      <input type="checkbox" name="sendto_clanmembers" value="1" checked="checked" /> <?php echo $_language->module['user_clanmembers']; ?> [<?php echo mysqli_num_rows(safe_query("SELECT userID FROM ".PREFIX."squads_members GROUP BY userID")).'&nbsp;'.$_language->module['users']; ?>]
    <br /><input type="checkbox" name="sendto_registered" value="1" checked="checked" /> <?php echo $_language->module['user_registered']; ?> [<?php echo mysqli_num_rows(safe_query("SELECT * FROM ".PREFIX."user WHERE newsletter='1'")).'&nbsp;'.$_language->module['users']; ?>]
    <br /><input type="checkbox" name="sendto_newsletter" value="1" checked="checked" /> <?php echo $_language->module['user_newsletter']; ?> [<?php echo mysqli_num_rows(safe_query("SELECT * FROM ".PREFIX."newsletter")).'&nbsp;'.$_language->module['users']; ?>]
    </div>
  </div>
<div class="form-group">
    <div class="col-sm-offset-2 col-sm-10"><input type="hidden" name="captcha_hash" value="<?php echo $hash; ?>"/>
  <button class="btn btn-primary btn-xs" type="submit" name="send"><?php echo $_language->module['send']; ?></button>
 </div>
</div>
</form> 
</div></div>

<?php 
}
echo '</div>';
?>