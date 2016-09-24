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

if($site=="partners") {
	$_language->readModule('sc_partners');
	echo    '<div class="page-header">
			<h2><a href="index.php?site=partners">'.$_language->module['partner'].'</a></h2>
			</div>';
}

$ergebnis = safe_query(
    "SELECT
        *
    FROM
        " . PREFIX . "partners
    WHERE
        displayed = '1'
    ORDER BY
        sort"
);
if (mysqli_num_rows($ergebnis)) {
    echo '<ul class="list-group">';
    while ($db = mysqli_fetch_array($ergebnis)) {
        $partnerID = $db[ 'partnerID' ];
        $banner = $db[ 'banner' ];
        $alt = htmlspecialchars($db[ 'name' ]);
        $title = htmlspecialchars($db[ 'name' ]);
        $img = 'images/partners/' . $db[ 'banner' ];
        $name = $db[ 'name' ];
        $img_str = '<img src="images/partners/' . $db[ 'banner' ] . '" alt="' . $alt . '" title="' . $title . '">';
        if (is_file($img) && file_exists($img)) {
            $text = $img_str;
        } else {
            $text = $name;
        }
		$link = '<a href="'.$db['url'].'" onclick="setTimeout(function(){window.location.href=\'out.php?partnerID=' . $db['partnerID'] . '\', 1000})" target="_blank">' . $text . '</a>';
		
		$script = '<script>	
		window.addEventListener("load", function(){
    var boy'.$db['partnerID'].' = document.getElementById("box_'.$db['partnerID'].'")
    boy'.$db['partnerID'].'.addEventListener("touchstart", function(e){
		setTimeout(function(){window.location.href="out.php?partnerID=' . $db['partnerID'] . '", 200}) 
        e.preventDefault()
    }, false)
    boy'.$db['partnerID'].'.addEventListener("touchmove", function(e){
        e.preventDefault()
    }, false)
    boy'.$db['partnerID'].'.addEventListener("touchend", function(e){
				window.open("'.$db['url'].'", "_blank")
        e.preventDefault()
    }, false)
}, false)	
</script>';
		
        $data_array = array();
        $data_array['$partnerID'] = $partnerID;
        $data_array['$link'] = $link;
        $data_array['$script'] = $script;
        $data_array['$title'] = $title;
        $sc_partners = $GLOBALS["_template"]->replaceTemplate("sc_partners", $data_array);
        echo $sc_partners;
    }
    echo '</ul>';
}
?>
