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

$_language->readModule('sponsors');

$title_sponsors = $GLOBALS["_template"]->replaceTemplate("title_sponsors", array());
echo $title_sponsors;

$ergebnis = safe_query("SELECT * FROM " . PREFIX . "sponsors WHERE displayed = '1' ORDER BY sort");
if (mysqli_num_rows($ergebnis)) {
    $i = 1;
    while ($ds = mysqli_fetch_array($ergebnis)) {
	    
	    
	    if ($ds[ 'url' ] != '') {
            if (stristr($ds[ 'url' ], "http://")) { 
                $sponsor = '<a href="' . htmlspecialchars($ds[ 'url' ]) . '" onfocus="setTimeout(function(){window.location.href=\'out.php?sponsorID=' . $ds['sponsorID'] . '\', 1000})" target="_blank" rel="nofollow">' . $ds['name'] . '</a>';
            } else {
                $sponsor = '<a href="http://' . htmlspecialchars($ds[ 'url' ]) . '" onfocus="setTimeout(function(){window.location.href=\'out.php?sponsorID=' . $ds['sponsorID'] . '\', 1000})" target="_blank" rel="nofollow">' . $ds['name'] . '</a>';
            }
        } else {
            $sponsor = $_language->module[ 'n_a' ];
        }
        
        if ($ds[ 'banner' ] != '') {
            if (stristr($ds[ 'url' ], "http://")) { 
               $banner = '<a href="'.$ds['url'].'" id="sponsor_' . $ds[ 'sponsorID' ] . '" onfocus="setTimeout(function(){window.location.href=\'out.php?sponsorID=' . $ds[ 'sponsorID' ] . '\', 1000})" target="_blank"><img src="images/sponsors/' .
			   $ds['banner'] . '" alt="' . htmlspecialchars($ds[ 'name' ]) . '" class="img-responsive"></a>';
            } else {
                $banner = '<a href="http://'. $ds[ 'url' ].'" id="sponsor_' . $ds[ 'sponsorID' ] . '" onfocus="setTimeout(function(){window.location.href=\'out.php?sponsorID=' . $ds[ 'sponsorID' ] . '\', 1000})" target="_blank"><img src="images/sponsors/' .
            $ds['banner'] . '" alt="' . htmlspecialchars($ds[ 'name' ]) . '" class="img-responsive"></a>';
            }
        } else {
            $banner = '';
        }
	    
 
        $info = cleartext($ds['info']);
 

		$script = '<script>	
		window.addEventListener("load", function(){
    var box'.$ds['sponsorID'].' = document.getElementById("box_'.$ds['sponsorID'].'")
    box'.$ds['sponsorID'].'.addEventListener("touchstart", function(e){
		setTimeout(function(){window.location.href="out.php?sponsorID=' . $ds['sponsorID'] . '", 200}) 
        e.preventDefault()
    }, false)
    box'.$ds['sponsorID'].'.addEventListener("touchmove", function(e){
        e.preventDefault()
    }, false)
    box'.$ds['sponsorID'].'.addEventListener("touchend", function(e){
				window.open("'.$ds['url'].'", "_blank")
        e.preventDefault()
    }, false)
}, false)	
</script>';	
			
        $data_array = array();
        $data_array['$sponsor'] = $sponsor;
        $data_array['$sponsorID'] = $ds['sponsorID'];
        $data_array['$banner'] = $banner;
        $data_array['$info'] = $info;
        $data_array['$link'] = $link;
        $sponsors = $GLOBALS["_template"]->replaceTemplate("sponsors", $data_array);
        echo $sponsors;
		echo $script;
        $i++;
    }
} else {
    echo generateAlert($_language->module['no_sponsors'], 'alert-info');
}
?>