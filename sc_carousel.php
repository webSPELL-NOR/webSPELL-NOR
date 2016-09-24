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
 
$_language->readModule('carousel');
 
$carousel = safe_query("SELECT * FROM " . PREFIX . "carousel WHERE (displayed = '1') ORDER BY sort");
 
echo '<div id="myCarousel" class="carousel slide" data-ride="carousel"> <!-- Carousel start -->
      <!-- Indicators -->
      <ol class="carousel-indicators">';
       if(mysqli_num_rows($carousel)) {
           for($i=0; $i<=(mysqli_num_rows($carousel)-1); $i++) {
               if($i==0) {
                    echo '<li data-target="#myCarousel" data-slide-to="'.$i.'" class="active"></li>';
               } else {
                    echo '<li data-target="#myCarousel" data-slide-to="'.$i.'"></li>';
               }
           }       
       }
       echo '</ol><div class="carousel-inner" role="listbox">';
$x = 1;
if (mysqli_num_rows($carousel)) {
    while ($db = mysqli_fetch_array($carousel)) {
        $title=""; $link=""; $description="";
        if($x==1) { echo '<div class="item active">'; } else { echo '<div class="item">'; }
        if (!empty($db[ 'carousel_pic' ])) {
            $carousel_pic = '<img src="images/carousel/' . $db[ 'carousel_pic' ] . '" alt="' . htmlspecialchars($db[ 'title' ]) .
                '" class="img-responsive img-mobile">';
        } else {
            $title = $db[ 'title' ];
        }
        $carouselID = $db[ 'carouselID' ];
		$title = $db[ 'title' ];
        if (stristr($db[ 'link' ], "http://")) {
			$link = $db[ 'link' ];
			
		} else {
			$link = 'http://' . $db[ 'link' ] . '';
		}
        $description = $db[ 'description' ];
 
        $data_array = array();
        $data_array['$carouselID'] = $carouselID;
        $data_array['$carousel_pic'] = $carousel_pic;
        $data_array['$title'] = $title;
        $data_array['$link'] = $link;
        $data_array['$description'] = $description;
        $sc_carousel = $GLOBALS["_template"]->replaceTemplate("sc_carousel", $data_array);
        echo $sc_carousel;
        echo '</div>'; $x++;
    }
    echo '</div>';
}
    echo '<a class="left carousel-control" href="#myCarousel" role="button" data-slide="prev">
        <i class="fa fa-chevron-left fa-2x" aria-hidden="true"></i>
        <span class="sr-only">Previous</span>
      </a>
      <a class="right carousel-control" href="#myCarousel" role="button" data-slide="next">
        <i class="fa fa-chevron-right fa-2x" aria-hidden="true"></i>
        <span class="sr-only">Next</span>
      </a>
    </div>';