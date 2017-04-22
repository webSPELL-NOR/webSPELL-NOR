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

include("_mysql.php");
include("_settings.php");
include("_functions.php");

echo '<!DOCTYPE html>
<html lang="en">
<head>
	<base href="'.$rewriteBase.'">
    <meta charset="utf-8">
    <meta name="description" content="Clanpage using webSPELL 4 CMS">
    <meta name="author" content="webspell.org">
    <meta name="copyright" content="Copyright 2005-2015 by webspell.org">
    <meta name="generator" content="webSPELL">
    <title>Smilies</title>';
    $componentsCss = generateComponents($components['css'], 'css');
    echo $componentsCss;
    
    echo '
    <link href="_stylesheet.css" rel="stylesheet">
    <script src="js/bbcode.js"></script>
</head>

<body>
<div class="container">
	<div class="page-header">
	    <h1>Smilies</h1>
	</div>
	<div class="row">
	
		<div class="col-md-12 col-xs-12 col-sm-12">
			<ul class="list-inline">';
				$emojiQuery = safe_query("SELECT * FROM ".PREFIX."smileys");
				while($sm = mysqli_fetch_array($emojiQuery)) {
					echo '<li>
						<a href="javascript:AddCodeFromWindow(\'' . $sm['pattern'] . ' \')"><i class="em em-'.$sm['name'].'"></i></a>
					</li>';
				}
	echo'		</ul>
		</div>
	
	</div>
</div>
</body>
</html>';
