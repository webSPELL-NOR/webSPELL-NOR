<style type="text/css">
div.installer_trans { position: fixed; background-color: rgba(0, 0, 0, 0.5); height: 100%; width: 100%; overflow: auto; top: 0; left: 0; z-index: 99998;}
div.install_plugin { display: block; z-index: 99999; width: 80%; margin: 0 auto;} 

.jumbotron.bg {
	background: url('http://www.designperformance.de/images/plugin-install-banner.jpg') no-repeat;
	background-size: cover
}
.jumbotron h1 {
	color: #FFF;
	padding: 5px;
	text-shadow: 1px 2px 0px rgba(0, 0, 0, 0.4);
}
.jumbotron p {
	padding: 5px;
	color: #FFF;
	text-shadow: 0 2px 1px rgba(0, 0, 0, 0.6);
}
.text-muted { color: #fff !important; }
.quicklinks { color: white !important; }
.margto8{margin-top:8px;}
</style>
<script>
function goBack() {
    window.history.back();
}
</script>
<?php

#@info:	settings
$plugin_table 	= 	"clan_rules"; 							// name of the mysql table
$str			=	"Clan Rules"; 							// name of the plugin
$admin_file 	=	"../index.php?site=clan_rules_admin";	// administration file
$activate 		=	"1";									// plugin activate 1 yes | 0 no
$author			=	"T-Seven";								// author
$website		= 	"http://designperformance.de";			// authors website
$index_link		=	"clan_rules,clan_rules_admin";			// index file (without extension, also no .php)
$sc_link 		=	"";  									// sc_ file (visible as module/box)
$hiddenfiles 	=	"";										// hiddenfiles (background working, no display anywhere)
$version		=	"1.0";									// current version, visit authors website for updates, fixes, ..
$path			=	"_plugins/clan_rules/";					// plugin files location
$navi_link		=	"clan_rules";					 		// navi link file (index.php?site=...)

#@info: database
$install = "CREATE TABLE `" . PREFIX . "clan_rules` (
  `clan_rulesID` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL default '',
  `text` text NOT NULL,
  `sort` int(11) NOT NULL DEFAULT '0',
  `displayed` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '1',
  PRIMARY KEY (`clan_rulesID`)
) AUTO_INCREMENT=1
  DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_unicode_ci";

  

# 	= = =		/!\ DO NOT EDIT THE LINES BELOW !!!		= = =
# 	= = =		/!\ DO NOT EDIT THE LINES BELOW !!!		= = =
# 	= = =		/!\ DO NOT EDIT THE LINES BELOW !!!		= = =

# 	= = =		/!\ Ab hier nichts mehr ändern !!!		= = =

  
$add_plugin = "INSERT INTO `".PREFIX."plugins` (`name`, `admin_file`, `activate`, `author`, `website`, `index_link`, `sc_link`, `hiddenfiles`, `version`, `path`) 
				VALUES ('$str', '$admin_file', $activate, '$author', '$website', '$index_link', '$sc_link', '$hiddenfiles', '$version', '$path');";

$add_navigation = "INSERT INTO `".PREFIX."navigation_sub` (`mnav_ID`, `name`, `link`, `sort`, `indropdown`) 
					VALUES ('2','$str', 'index.php?site=$navi_link', '1', '1');";
	
			
if(!ispageadmin($userID)) { echo ("Access denied!"); return false; }		
			
echo "<div class='installer_trans'><div class='install_plugin'>
      	<div class='header clearfix'>
      	    <nav>
       		   <ul class='nav nav-pills pull-right margto8'>
       		     <li role='presentation'><a class='quicklinks' href='http://webspell-nor.de/'>Support</a></li>
       		     <li role='presentation'><a class='quicklinks' href='http://www.webspell.org/index.php?site=license'>License</a></li>
        	     <li role='presentation'><a class='quicklinks' href='http://webspell-nor.de/index.php?site=about'>About</a></li>
        	  </ul>
        	</nav>
      		<h3 class='text-muted'>WebSpell plugin Installation</h3>
      	</div>

        <div class='jumbotron bg'>
      	  <h1>webSPELL NOR  <u>$str</u> plugin</h1>
      		  <p>super powerful, responsive features, easy to adjust
       		  one of the easiest content management systems on earth
       		  wonderful bootstrap or photoshop templates
       		  lots of Add-ons and modifications for all types of websites
       		  a community behind you for all issues and problems
       		  </p>
        </div>
    ";
      

	echo "<div class='panel panel-default'>
			<div class='panel-heading'>
				<h3 class='panel-title'>$str Database Installation</h3>
			</div>
			<div class='panel-body'>";
	
		# if table exists
		try {
			if(mysqli_query($_database, $install)) { 
				echo "<div class='alert alert-success'>$str installation successful <br />";
				echo "$str installation erfolgreich <br />";
				echo "$str installation r&eacute;ussie <br /></div>";
			} else {
					echo "<div class='alert alert-warning'>$str entry already exists <br />";
					echo "$str Eintrag schon vorhanden <br />";
					echo "$str Entrée existe déjà <br /></div>";
					echo "<hr>";
			}	
		} CATCH (EXCEPTION $x) {
				echo "<div class='alert alert-danger'>$str installation failed <br />";
				echo "Send the following line to the support team:<br /><br />";
				echo "<pre>".$x->message()."</pre>		
					  </div>";
		}


		# Add to Plugin-Manager
		if(mysqli_num_rows(safe_query("SELECT name FROM `".PREFIX."plugins` WHERE name ='".$str."'"))>0) {
					echo "<div class='alert alert-warning'>$str Plugin Manager entry already exists <br />";
					echo "$str Plugin Manager Eintrag schon vorhanden <br />";
					echo "$str Entrée Plugin Manager existe déjà <br /></div>";
					echo "<hr>";
		} else {
			try {
				if(safe_query($add_plugin)) { 
					echo "<div class='alert alert-success'>$str added to the plugin manager <br />";
					echo "$str wurde dem Plugin Manager hinzugef&uuml;gt <br />";
					echo "$str a &eacute;t&eacute; ajout&eacute; au manager de plugin <br />";
					echo "<a href = '/admin/admincenter.php?site=plugin-manager' target='_blank'><b>LINK => Plugin Manager</b></a></div>";
				} else {
					echo "<div class='alert alert-danger'>Add to plugin manager failed <br />";
					echo "Zum Plugin Manager hinzuf&uuml;gen fehlgeschlagen <br />";
					echo "Echec d'ajout au manager de plugin <br /></div>";
				}	
			} CATCH (EXCEPTION $x) {
					echo "<div class='alert alert-danger'>$str installation failed <br />";
					echo "Send the following line to the support team:<br /><br />";
					echo "<pre>".$x->message()."</pre>		
						  </div>";
			}
		}

		# Add to navigation
		if(mysqli_num_rows(safe_query("SELECT * FROM `".PREFIX."navigation_sub` WHERE `name`='$str' AND `link`='index.php?site=$navi_link'"))>0) {
					echo "<div class='alert alert-warning'>$str Navigation entry already exists <br />";
					echo "$str Navigationseintrag schon vorhanden <br />";
					echo "$str Entrée Navigation existe déjà <br /></div>";
					
		} else {
			try {
				if(safe_query($add_navigation)) { 
					echo "<div class='alert alert-success'>$str added to the Navigation <br />";
					echo "$str wurde der Navigation hinzugef&uuml;gt <br />";
					echo "$str a &eacute;t&eacute; ajout&eacute; la navigation <br />";
					echo "<a href = '/admin/admincenter.php?site=navigation' target='_blank'><b>LINK => Navigation</b></a></div>";
				} else {
					echo "<div class='alert alert-danger'>Add to Navigation failed <br />";
					echo "Zur Navigation hinzuf&uuml;gen fehlgeschlagen<br />";
					echo "Echec d'ajout la navigation <br /></div>";
				}	
			} CATCH (EXCEPTION $x) {
					echo "<div class='alert alert-danger'>$str installation failed <br />";
					echo "Send the following line to the support team:<br /><br />";
					echo "<pre>".$x->message()."</pre>		
						  </div>";
			}
		}

		echo "</div></div>";

		
	
	echo "<div class='panel panel-default'>
		<div class='panel-heading'>
			<h3 class='panel-title'>Info</h3></div>
		<div class='panel-body'>";
	echo "<div class='alert alert-danger'>View the readme of the plugin manager to display the $str<br />";
	echo "Lese die Anleitung zum plugin manager um $str anzeigen zu lassen <br />";
	echo "Regarder le readme du manager de plugin pour afficher le $str <br />";
	echo "$str installation --- <br />";
	echo "</div>";

	echo "<button class='btn btn-default btn-sm' onclick='goBack()'>Go Back</button>";

	echo "</div></div>
	
		</div></div>
	";
	
 ?>