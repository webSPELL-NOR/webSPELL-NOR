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

// important data include
include("_mysql.php");
include("_settings.php");
include("_functions.php");
include("_plugin.php"); #Plugin-Manager

$_language->readModule('index');

$index_language = $_language->module;
// end important data include

$sql = safe_query("SELECT module, le_activated FROM ".PREFIX."moduls WHERE le_activated = '1'");
if(mysqli_num_rows($sql)) {
    while($row = mysqli_fetch_array($sql)) {
        $hide1[] = $row['module'];
    }
}
else {
    $hide1 = array();
}

$sql = safe_query("SELECT module, re_activated FROM ".PREFIX."moduls WHERE re_activated = '1'");
if(mysqli_num_rows($sql)) {
    while($row = mysqli_fetch_array($sql)) {
        $hide2[] = $row['module'];
    }
}
else {
    $hide2 = array();
}

$sql = safe_query("SELECT module, activated FROM ".PREFIX."moduls WHERE activated = '1'");
if(mysqli_num_rows($sql)) {
    while($row = mysqli_fetch_array($sql)) {
        $hide3[] = $row['module'];
    }
}
else {
    $hide3 = array();
}


header('X-UA-Compatible: IE=edge,chrome=1');
?>
<!DOCTYPE html>
<html lang="<?php echo $_language->language ?>">
<head>
    <?php
    if (
        (isset($_SESSION[ 'language' ]) && ($_SESSION[ 'language' ] == 'ac')) ||
        (isset($_COOKIE[ 'language' ]) && ($_COOKIE[ 'language' ] == 'ac'))
    ) {
        echo '<script type="text/javascript">
            var _jipt = [];
            _jipt.push([\'project\', \'webspell-cms\']);
        </script>
        <script type="text/javascript" src="//cdn.crowdin.com/jipt/jipt.js"></script>';
    }
    ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="utf-8">
    <meta name="description" content="Website using webSPELL-NOR CMS">
    <meta name="author" content="webspell-nor.de">

    <!-- Head & Title include -->
    <title><?php 
     $pm = new plugin_manager();
     if(isset($_GET['site']) AND $pm->plugin_updatetitle($_GET['site'])) {
      echo $pm->plugin_updatetitle($_GET['site']);
     } else {  
      echo PAGETITLE; 
     }
     ?></title>
    <base href="<?php echo $rewriteBase; ?>">
    <link href="css/page.css" rel="stylesheet">
    <?php foreach ($components['css'] as $component) {
        echo '<link href="' . $component . '" rel="stylesheet">';
	}
    ?>
    
    <link href="css/scrolling-nav.css" rel="stylesheet">
    <link href="css/styles.css.php" rel="styleSheet" type="text/css">
    <link href="css/button.css.php" rel="styleSheet" type="text/css">
    

    <link href="_stylesheet.css" rel="stylesheet">

    <link href="tmp/rss.xml" rel="alternate" type="application/rss+xml" title="<?php echo $myclanname; ?> - RSS Feed">
    <!-- bbcode.js include -->
	<?php foreach ($components['js'] as $component) {
    echo '<script src="' . $component . '"></script>';
	}
	?>

	<!-- Plugin-Manager 1.2 load css/js -->
	<?php 
		$load = new plugin_manager();
		echo ($load->plugin_loadheadfile());
	?>
	<script src="js/bbcode.js" type="text/javascript"></script>
    <!-- end Head & Title include -->
    
</head>
<body>
<div class="wrapper">
<!-- Fixed navbar -->
    

         <nav class="navbar navbar-default navbar-fixed-top" role="navigation">
        <div class="container">
            <div class="navbar-header page-scroll">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand page-scroll" href="#"><?php echo $myclanname ?></a>
            </div>

            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse navbar-ex1-collapse">
            <ul class="nav navbar-nav visible-xs">
                <li class="dropdown">
                    <?php if($loggedin) {
                        echo
                            '<a href="index.php?site=loginoverview">' . getnickname($userID) . '</a>';
                    } else {
                        echo '<a href="index.php?site=login">' . ucfirst($index_language[ 'login' ]) . '</a>';
                    } ?>
                </li>
            </ul>
			<?php include("navigation.php"); ?>
        </div>
    </div> <!-- /container -->
    </nav> <!-- nav end -->
      <?php include ('sc_carousel.php'); ?>   <br><br> 
    <div class="container">
    
    	<div class="row">
        <?php // show left column
            if (!in_array($site, $hide3)) {
?>
            <?php // show left column
            if (!in_array($site, $hide1)) {
?>
                <!-- left column -->
                <div id="leftcol" class="col-lg-3 visible-lg"><br>
                    <!-- poll include -->
                    <h2><?php echo $index_language[ 'poll' ]; ?></h2>
                    <hr>
                    <?php include("poll.php"); ?>
                    <!-- end poll include -->
                    <hr class="grey">
                    
                    <h3><?php echo $index_language[ 'lasttopics' ]; ?></h3>
                    <?php include("latesttopics.php"); ?>
                    <!-- end poll include -->
                    <hr class="grey">

                    <!-- pic of the moment include -->
                    <h3><?php echo $index_language[ 'pic_of_the_moment' ]; ?></h3>

                    <?php include("sc_potm.php"); ?>
                    <!-- end pic of the moment include -->
                    <hr class="grey">

                    <!-- randompic include -->
                    <h3><?php echo $index_language[ 'random_user' ]; ?></h3>
                    <?php include("sc_randompic.php"); ?>
                    <!-- end randompic include -->
                    <hr class="grey">
                    
                    <!-- articles include -->
                    <h3><?php echo $index_language[ 'articles' ]; ?></h3>
                    <?php include("sc_articles.php"); ?>
                    <!-- end articles include -->
                    <hr class="grey">

                    <!-- downloads include -->
                    <h3><?php echo $index_language[ 'downloads' ]; ?></h3>
                    <?php include("sc_files.php"); ?>
                    <!-- end downloads include -->
                    <hr class="grey">

                    <!-- servers include -->
                    <h3><?php echo $index_language[ 'server' ]; ?></h3>
                    <?php include("sc_servers.php"); ?>
                    <!-- end servers include -->
                    <hr class="grey">

                    <!-- sponsors include -->
                    <h3><?php echo $index_language[ 'sponsors' ]; ?></h3>

                    <?php include("sc_sponsors.php"); ?>
                    <!-- end sponsors include -->
                    <hr class="grey">
					
                    <!-- partners include -->
                    <h3><?php echo $index_language[ 'partners' ]; ?></h3>

                    <?php include("partners.php"); ?>
                    <!-- end partners include -->
                    <hr class="grey">

                </div>
            <?php
            // end of show left column
            } ?>
            <?php
            // end of show right column
            } ?>
            <!-- main content area -->
            <div id="maincol" class="
            <?php
            if (in_array($site, $hide1)) {
                echo "col-lg-9 col-sm-9 col-xs-12";
            }
            elseif (in_array($site, $hide2)) {
                echo "col-lg-9 col-sm-9 col-xs-12";
            }
            elseif (in_array($site, $hide3)) {
                echo "col-lg-12 col-sm-12 col-xs-12";
            } else {
                echo "col-lg-6 col-sm-9 col-xs-12";
            }
            ?>">
                <?php
                if (!isset($site)) {
                    $site = "news";
                }
                $invalide = array('\\', '/', '/\/', ':', '.');
                $site = str_replace($invalide, ' ', $site);
					
				$_language->readModule('plugin');
				$plugin = new plugin_manager();
				$plugin->set_debug(DEBUG);
				if(!empty($site) AND $plugin->is_plugin($site)>0) {
					$data = $plugin->plugin_data($site); 
					$plugin_path = $data['path'];
					$check = $plugin->plugin_check($data, $site);
					if($check['status']==1) {
						include($check['data']);
					} else {
						echo $check['data'];
					}
				} else {
					if (!file_exists($site . ".php")) {
						$site = "news";
					}
					include($site . ".php");
				}
				?>
			</div>
            <?php // show right column
            if (!in_array($site, $hide3)) {
?>
<?php // show right column
            if (!in_array($site, $hide2)) {
?>
            <!-- right column -->
            <div id="rightcol" class="col-md-3 col-sm-3 hidden-xs"><br>
                <!-- login include -->
                <div class="hidden-xs">                    
                    <?php include("login.php"); ?>
                    <hr class="grey">
                </div>
 
                <div class="visible-sm">
                    <h3><?php echo $index_language[ 'topics' ]; ?></h3>
                    <?php include("latesttopics.php"); ?>
                </div>

                <div class="visible-lg">
                    <h3><?php echo $index_language[ 'hotest_news' ]; ?></h3>
                    <?php include("sc_topnews.php"); ?>
                    <hr class="grey">
                </div>

                <div class="visible-sm">
                    <!-- headlines include -->
                    <h3><?php echo $index_language[ 'latest_news' ]; ?></h3>
                    <?php include("sc_headlines.php"); ?>
                    <!-- end headlines include -->
                    <hr class="grey">
                </div>

                <div class="visible-lg">
                    <!-- squads include -->
                    <h3><?php echo $index_language[ 'squads' ]; ?></h3>

                    <?php include("sc_squads.php"); ?>
                    <!-- end squads include -->
                    <hr class="grey">
                </div>

                <div class="visible-sm">
                    <!-- clanwars include -->
                    <h3><?php echo $index_language[ 'matches' ]; ?></h3>
                    <?php include("sc_results.php"); ?>
                    <!-- end clanwars include -->
                    <hr class="grey">
                </div>

                <div class="visible-lg">
                    <!-- demos include -->
                    <h3><?php echo $index_language[ 'demos' ]; ?></h3>
                    <?php include("sc_demos.php"); ?>
                    <!-- end demos include -->
                    <hr class="grey">
                </div>

                <div class="visible-lg">
                    <!-- upcoming events include -->
                    <h3><?php echo $index_language[ 'upcoming_events' ]; ?></h3>
                    <?php include("sc_upcoming.php"); ?>
                    <!-- end upcoming events include -->
                    <hr class="grey">
                </div>

                <div class="visible-lg">
                    <!-- shoutbox include -->
                    <h3><?php echo $index_language[ 'shoutbox' ]; ?></h3>

                    <?php include("shoutbox.php"); ?>
                    <!-- end shoutbox include -->
                    <hr class="grey">
                </div>

                <div class="visible-lg">
                    <!-- statistics include -->
                    <h3><?php echo $index_language[ 'statistics' ]; ?></h3>
                    <?php include("counter.php"); ?>
                    <!-- end statistics include -->
                </div>
            </div>
            <?php
            // end of show right column
            } ?>
            <?php
            // end of show right column
            } ?>
        </div> <!-- row-end -->
    </div> <!-- container-content-end -->
    
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-md-4 col-sm-3">
                    <h2><?php echo $index_language[ 'aboutus' ]; ?></h2>
                    <div class="about_foot"><?php include("about_foot.php"); ?></div>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-3">
                    <h3><?php echo $index_language[ 'language_switch' ]; ?></h3>
                    <?php include("sc_language.php"); ?>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-3 hidden-xs">
                    <h3><?php echo $index_language[ 'newsletter' ]; ?></h3>
                    <?php include("sc_newsletter.php"); ?>
                </div>
            </div>
            <br>
        </div>
        <div id="footer">
            <div class="container">
                Copyright&copy; by <?php echo $myclanname ?> <?php date("Y"); ?> - <small>Template by <a href="http://www.2one-designs.de" target="_blank">Argu</a> and <a href="http://www.designperformance.de" target="_blank">T-Seven</a></small>
                <!-- if you want, you can use this network-items by your own -->
                    <div class="pull-right">
                        <a href="https://www.facebook.com/webspellnor"><i id="social-fb" class="fa fa-facebook-square fa-3x social"></i></a>
                        <a href="https://twitter.com"><i id="social-tw" class="fa fa-twitter-square fa-3x social"></i></a>
                        <a href="https://plus.google.com/"><i id="social-gp" class="fa fa-google-plus-square fa-3x social"></i></a>
                        <a href="mailto:admin@yoursite.com"><i id="social-em" class="fa fa-envelope-square fa-3x social"></i></a>      
                    </div>
                
            </div>
        </div>
    </footer>    
</div>  <!-- wrapper-end --> 
<div class="scroll-top-wrapper">  <!-- scroll to top feature -->
	<span class="scroll-top-inner">
		<i class="fa fa-2x fa-arrow-circle-up"></i>
	</span>
</div>
    
<script>
    webshim.setOptions('basePath', 'components/webshim/js-webshim/minified/shims/');
    //request the features you need:
    webshim.setOptions("forms-ext",
    {
        replaceUI: false,
        types: "date time datetime-local"
    });
    webshim.polyfill('forms forms-ext');
</script>
<script>
	  $("body").tooltip({   
		selector: "[data-toggle='tooltip']",
		container: "body"
	})
</script>
<!-- Scrolling Nav JavaScript -->
    <script src="js/jquery.easing.min.js"></script>
    <script src="js/scrolling-nav.js"></script>
    

</body>
</html>