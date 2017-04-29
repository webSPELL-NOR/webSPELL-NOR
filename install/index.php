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

session_name("ws_session");
session_start();
header('content-type: text/html; charset=utf-8');
include("../src/func/language.php");
include("../src/func/user.php");
include("../version.php");
if (version_compare(PHP_VERSION, '5.3.7', '>') && version_compare(PHP_VERSION, '5.5.0', '<')) {
	include('../src/func/password.php');
}

$_language = new \webspell\Language();

if (!isset($_SESSION['language'])) {
    $_SESSION['language'] = "en";
}

if (isset($_GET['lang'])) {
    if ($_language->setLanguage($_GET['lang'])) {
        $_SESSION['language'] = $_GET['lang'];
    }
    header("Location: index.php");
    exit();
}

$_language->setLanguage($_SESSION['language']);
$_language->readModule('index');

if (isset($_GET['step'])) {
    $_language->readModule('step'.(int)$_GET['step'], true);
} else {
    $_language->readModule('step0', true);
}

if (!isset($_GET['step'])) {
    $_GET['step'] = "";
}

?>

<!DOCTYPE html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="description" content="Clanpage using webSPELL 4 CMS">
    <meta name="author" content="webspell.org">
    <meta name="copyright" content="Copyright 2005-2014 by webspell.org">
    <meta name="generator" content="webSPELL">
    <title>webSPELL NOR Installation</title>
    <link href="../components/bootstrap/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome Include -->
    <link href="../components/font-awesome/font-awesome.min.css" rel="stylesheet" type="text/css">
    <link href="../_stylesheet.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
    <script src="../components/jquery/jquery.min.js"></script>
    <script src="install.js"></script>
</head>
<body>
   <div class="container">
      <div class="header clearfix">
        <nav>
          <ul class="nav nav-pills pull-right">
            <li role="presentation"><a href="http://webspell-nor.de/">Support</a></li>
            <li role="presentation"><a href="http://www.webspell.org/index.php?site=license">License</a></li>
            <li role="presentation"><a href="http://webspell-nor.de/index.php?site=about">About</a></li>
          </ul>
        </nav>
        <h3 class="text-muted">WebSpell Installation</h3>
      </div>

      <div class="jumbotron bg">
        <h1>WebSpell NOR</h1>
        <p>super powerful, responsive features, easy to adjust
        one of the easiest content management systems on earth
        wonderful bootstrap or photoshop templates
        lots of Add-ons and modifications for all types of websites
        a community behind you for all issues and problems
        </p>
      </div>

      <div class="row marketing">
        <div class="col-xs-12">
            <?php
                echo '<form action="index.php?step=' . ($_GET['step'] + 1) . '" method="post" name="ws_install">';
                include('step0' . $_GET['step'] . '.php');
            ?>
		</div>
    <footer class="footer">
		<div class="container">
        	<hr />
			<p class="text-muted"><small><i class="fa fa-copyright"></i> <?php echo date("Y"); ?> by <a href="https://webspell-nor.de/" target="_blank">WebSpell NOR</a> & <a href="http://www.webspell.org" target="_blank">Webspell.org</a></small></p>
		</div>
    </footer>
	</div>
    </div> <!-- /container -->
<script src="../components/bootstrap/bootstrap.min.js"></script>
	<script>
	  $("body").tooltip({   
		selector: "[data-toggle='tooltip']",
		container: "body"
	})
	</script>
</body>
</html>