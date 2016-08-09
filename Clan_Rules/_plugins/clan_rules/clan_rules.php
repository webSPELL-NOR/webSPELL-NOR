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

# Sprachdateien aus dem Plugin-Ordner laden
	$pm = new plugin_manager(); 
	$plugin_language = $pm->plugin_language("clan_rules", $plugin_path);

# Installiert

if(file_exists('install-clan_rules.php')){ echo $plugin_language['del_install']; 
return false; }

# Titel ausgeben                                 
#@parameter: loadTemplate("Html-Datei-Name, "welchen-bereich", "daten(array)", "pluginpfad");

#title
$_language->readModule('clan_rules');
	$plugin_data= array();
    $plugin_data['$title']=$plugin_language['clan_rules'];

    

    $template = $GLOBALS["_template"]->loadTemplate("clan_rules","head", $plugin_data, $plugin_path);
    echo $template;

    #adminbutton
if(issuperadmin($userID)) {
echo '<a href="index.php?site=clan_rules_admin" class="btn btn-danger">' .
                    $plugin_language[ 'new_rules' ] . '</a><br><br>
                    ';
}
  #end
	
	$ergebnis = safe_query("SELECT * FROM " . PREFIX . "clan_rules WHERE displayed = '1' ORDER BY sort");
	if (mysqli_num_rows($ergebnis)) {
		$i = 1;
		while ($ds = mysqli_fetch_array($ergebnis)) {
			
			$title = cleartext($ds['title']);    
			$text = cleartext($ds['text']);

			$data_array = array();
			$data_array['$title'] = $title;
			$data_array['$text'] = $text;
        
# Den Plugin-Pfad mit übergeben (zum Anzeigen von Bsp Grafiken)
		$data_array['$pluginpath'] = substr_replace($plugin_path, "", -1);
		$data_array['$adminaction'] = "Adminactions for admins";
		
        $clan_rules = $GLOBALS["_template"]->loadTemplate("clan_rules","content_area", $data_array, $plugin_path);
        echo $clan_rules;
        $i++;
		
		// Footer
	#	$plugin_footer['$autor']="Author: Nickname";
		$plugin_footer['$autor']="";
		$template = $GLOBALS["_template"]->loadTemplate("clan_rules","footer", $plugin_footer, $plugin_path);
		echo $template;
		
		}
		
	} else {
		echo generateAlert($plugin_language['no_clan_rules'], 'alert-info');
	}
	

?>