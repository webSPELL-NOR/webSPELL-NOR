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

chdir('../');
include("_mysql.php");
include("_settings.php");
include("_functions.php");
Include("_plugin.php");
chdir('admin');

$load = new plugin_manager();
$_language->readModule('admincenter', false, true);

if(isset($_GET['site'])) $site = $_GET['site'];
else
if(isset($site)) unset($site);
$username='<b>'.getnickname($userID).'</b>';


if (isset($_GET['site'])) {
    $site = $_GET['site'];
} elseif (isset($site)) {
    unset($site);
}

$admin=isanyadmin($userID);
if (!$loggedin) {
    die($_language->module['not_logged_in']);
}
if (!$admin) {
    die($_language->module['access_denied']);
}

if (!isset($_SERVER['REQUEST_URI'])) {
    $arr = explode("/", $_SERVER['PHP_SELF']);
    $_SERVER['REQUEST_URI'] = "/" . $arr[count($arr)-1];
    if ($_SERVER['argv'][0]!="") {
        $_SERVER['REQUEST_URI'] .= "?" . $_SERVER['argv'][0];
    }
}

function admincenternav($catID)
{
    global $userID;
    $links = '';
    $ergebnis = safe_query("SELECT * FROM ".PREFIX."dashnavi_links WHERE catID='$catID' ORDER BY sort");
    while ($ds=mysqli_fetch_array($ergebnis)) {
        $accesslevel = 'is'.$ds['accesslevel'].'admin';
        if ($accesslevel($userID)) {
            $links .= '<li><a href="'.$ds['url'].'">'.$ds['name'].'</a></li>';
        }
    }
    return $links;
}

function addonnav()
{
    global $userID;

    $links = '';
    $ergebnis = safe_query("SELECT * FROM ".PREFIX."dashnavi_categories WHERE sort>'9' ORDER BY sort");
    while ($ds=mysqli_fetch_array($ergebnis)) {
        $links .= '<li>
        <a href="#"><i class="fa fa-plus"></i> '.$ds['name'].'<span class="fa arrow"></span></a>';
        $catlinks = safe_query("SELECT * FROM ".PREFIX."dashnavi_links WHERE catID='".$ds['catID']."' ORDER BY sort");
        while ($db=mysqli_fetch_array($catlinks)) {
            $accesslevel = 'is'.$db['accesslevel'].'admin';
            if ($accesslevel($userID)) {
                $links .= '<ul class="nav nav-second-level">
                                    <li><a href="'.$db['url'].'">'.$ds['name'].'</a></li>        
                        </ul>';
            }
        }
        $links .= '';
    }
    return $links;
}
if ($userID && !isset($_GET[ 'userID' ]) && !isset($_POST[ 'userID' ])) {
$ds =
        mysqli_fetch_array(safe_query("SELECT registerdate FROM `" . PREFIX . "user` WHERE userID='" . $userID . "'"));
    $username = '<a href="../index.php?site=profile&amp;id=' . $userID . '">' . getnickname($userID) . '</a>';
    $lastlogin = getformatdatetime($_SESSION[ 'ws_lastlogin' ]);
    $registerdate = getformatdatetime($ds[ 'registerdate' ]);

    $data_array = array();
    $data_array['$username'] = $username;
    $data_array['$lastlogin'] = $lastlogin;
    $data_array['$registerdate'] = $registerdate;
}

   
    if ($getavatar = getavatar($userID)) {
        $l_avatar = '<img src="../images/avatars/' . $getavatar . '" alt="Avatar" class="img-circle profile_img">';
    } else {
        $l_avatar = $_language->module[ 'n_a' ];
    }
   
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Webspell NOR - Bootstrap Admin Theme</title>

    <!-- Bootstrap Core CSS -->
    <link href="../components/bootstrap/bootstrap.min.css" rel="stylesheet">

   
    <!-- Custom CSS -->
    <link href="../components/admin/css/page.css" rel="stylesheet">

    <!-- Menu CSS -->
    <link href="../components/admin/css/menu.min.css" rel="stylesheet">

    <!-- Custom Fonts -->
    <link href="../components/font-awesome/font-awesome.min.css" rel="stylesheet" type="text/css">

    <!-- Style CSS -->
    <link href="../components/admin/css/style.css" rel="stylesheet">
    <link href="../css/button.css.php" rel="styleSheet" type="text/css">
    <link href="../components/admin/css/bootstrap-switch.css" rel="stylesheet">
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

  </head>

<body>

    <div id="wrapper">

        <!-- Navigation -->
        <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <img src="../components/admin/images/setting.png" class="img-circle hidden-xs"> <a class="navbar-brand" href="admincenter.php">WebSPELL NOR</a>
            </div>
            <!-- /.navbar-header -->

            <ul class="nav navbar-top-links navbar-right">
                
                <!-- /.dropdown -->
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        <i class="fa fa-question-circle"></i> Support <i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-user">
                   <li><a href="https://webspell-nor.de/index.php?site=forum"><i class="fa fa-commenting-o"></i> Forum</a>
                        </li>
                        <li class="divider"></li>
                        <li><a href="https://discord.gg/KVSfXQa"><i class="fa fa fa-comments"></i> open Discord</a>
                        </li>
                    </ul>
                    <!-- /.dropdown-user -->
                </li>
               
                <!-- /.dropdown -->

                <!-- /.dropdown -->
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        <i class="fa fa-times"></i> Logout <i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-user">
                        
                        <li><a href="../index.php"><i class="fa fa-undo"></i> Back to Website</a>
                        </li>
                        <li class="divider"></li>
                        <li><a href="../logout.php"><i class="fa fa-sign-out"></i> Logout</a>
                        </li>
                    </ul>
                    <!-- /.dropdown-user -->
                </li>
               
                <!-- /.dropdown -->

            </ul>
            <!-- /.navbar-top-links -->

            <!-- sidebar-links -->

            <div class="navbar-default sidebar" role="navigation">
                <div class="sidebar-nav navbar-collapse">

        
 
                    <ul class="nav" id="side-menu">
                        <li class="sidebar-search">
                            <div class="input-group custom-search-form">
                                
                                
                                <div class="profile_pic">
                <?php echo $l_avatar ?>
              </div>
              <div class="profile_info">
                <span>Welcome,</span>
                <h2><?php echo $username ?></h2>
        
              </div>
                           
                            </div>
                            <!-- /input-group -->
                        </li>
                        
                        <li>
                            <a href="#"><i class="fa fa-area-chart"></i> <?php echo $_language->module['main_panel']; ?><span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">
                                    <li><a href="admincenter.php"><?php echo $_language->module['overview']; ?></a></li>
                                    <li><a href="admincenter.php?site=page_statistic"><?php echo $_language->module['page_statistics']; ?></a></li>
                                    <li><a href="admincenter.php?site=visitor_statistic"><?php echo $_language->module['visitor_statistics']; ?></a></li>
                                       <?php echo admincenternav(1); ?>
                                                                        
                            </ul>
                            <!-- /.nav-second-level -->
                        </li>
                        
                        
                        <?php if(isuseradmin($userID)) { ?>    
                        <li>
                            <a href="#"><i class="fa fa-user"></i> <?php echo $_language->module['user_administration']; ?><span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">
                                    <li><a href="admincenter.php?site=users"><?php echo $_language->module['registered_users']; ?></a></li>
                                    <li><a href="admincenter.php?site=squads"><?php echo $_language->module['squads']; ?></a></li>
                                    <li><a href="admincenter.php?site=members"><?php echo $_language->module['clanmembers']; ?></a></li>
                                    <li><a href="admincenter.php?site=contact"><?php echo $_language->module['contact']; ?></a></li>
                                    <li><a href="admincenter.php?site=newsletter"><?php echo $_language->module['newsletter']; ?></a></li>
                                    <?php echo admincenternav(2); ?>
                            </ul>
                            <!-- /.nav-second-level -->
                        </li>
                        
        
                        <?php } if (ispageadmin($userID)) { ?>
                        <li>
                            <a href="#"><i class="fa fa-warning"></i> <?php echo $_language->module['spam']; ?><span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">
                                    <li><a href="admincenter.php?site=spam&amp;action=forum_spam"><?php echo $_language->module['blocked_content']; ?></a></li>
                                    <li><a href="admincenter.php?site=spam&amp;action=user"><?php echo $_language->module['spam_user']; ?></a></li>
                                    <li><a href="admincenter.php?site=spam&amp;action=multi"><?php echo $_language->module['multiaccounts']; ?></a></li>
                                    <li><a href="admincenter.php?site=spam&amp;action=api_log"><?php echo $_language->module['api_log']; ?></a></li>
                              <?php echo admincenternav(3); ?>   
                            </ul>
                            <!-- /.nav-second-level -->
                        </li>
                       
                        <?php }if(isnewsadmin($userID) || isfileadmin($userID) || ispageadmin($userID)) { ?>
                        <li>
                            <a href="#"><i class="fa fa-indent"></i> <?php echo $_language->module['rubrics']; ?><span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">
                                <?php } if(isnewsadmin($userID)) { ?>
                                <li><a href="admincenter.php?site=rubrics"><?php echo $_language->module['news_rubrics']; ?></a></li>
                                <li><a href="admincenter.php?site=newslanguages"><?php echo $_language->module['news_languages']; ?></a></li>
                                <?php } if(isfileadmin($userID)) { ?>
                                <li><a href="admincenter.php?site=filecategories"><?php echo $_language->module['file_categories']; ?></a></li>
                                <?php } if(ispageadmin($userID)) { ?>
                                <li><a href="admincenter.php?site=faqcategories"><?php echo $_language->module['faq_categories']; ?></a></li>
                                <li><a href="admincenter.php?site=linkcategories"><?php echo $_language->module['link_categories']; ?></a></li>
                                 <?php echo admincenternav(4); ?>
                            </ul>
                            <!-- /.nav-second-level -->
                        </li>
                       

                        <?php } if(ispageadmin($userID)) { ?>   
                        <li>
                            <a href="#"><i class="fa fa-pencil-square"></i> <?php echo $_language->module['settings']; ?><span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">
                                <li><a href="admincenter.php?site=settings"><?php echo $_language->module['settings']; ?></a></li>
                                <li><a href="admincenter.php?site=styles"><?php echo $_language->module['styles']; ?></a></li>
                                <li><a href="admincenter.php?site=dashnavi"><?php echo $_language->module['dashnavi']; ?></a></li>
                                <li><a href="admincenter.php?site=navigation"><?php echo $_language->module['web_navigation']; ?></a></li>
                                <li><a href="admincenter.php?site=countries"><?php echo $_language->module['countries']; ?></a></li>
                                <li><a href="admincenter.php?site=games"><?php echo $_language->module['games']; ?></a></li>
                                <li><a href="admincenter.php?site=modrewrite"><?php echo $_language->module['modrewrite']; ?></a></li>
                                <li><a href="admincenter.php?site=database"><?php echo $_language->module['database']; ?></a></li>
                                <li><a href="admincenter.php?site=update&amp;action=update"><?php echo $_language->module['update_webspell']; ?></a></li>
                                <li><a href="admincenter.php?site=email"><?php echo $_language->module['email']; ?></a></li>
                            
                        <?php echo admincenternav(5); ?>
                        </ul>
                            <!-- /.nav-second-level -->
                        </li>
                        <li>
                            <a href="#"><i class="fa fa-font"></i> <?php echo $_language->module['content']; ?><span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">
                                <li><a href="admincenter.php?site=carousel"><?php echo $_language->module['carousel']; ?></a></li>
                                <li><a href="admincenter.php?site=static"><?php echo $_language->module['static_pages']; ?></a></li>
                                <li><a href="admincenter.php?site=faq"><?php echo $_language->module['faq']; ?></a></li>
                                <li><a href="admincenter.php?site=servers"><?php echo $_language->module['servers']; ?></a></li>
                                <li><a href="admincenter.php?site=sponsors"><?php echo $_language->module['sponsors']; ?></a></li>
                                <li><a href="admincenter.php?site=partners"><?php echo $_language->module['partners']; ?></a></li>
                                <li><a href="admincenter.php?site=history"><?php echo $_language->module['history']; ?></a></li>
                                <li><a href="admincenter.php?site=about"><?php echo $_language->module['about_us']; ?></a></li>
                                <li><a href="admincenter.php?site=imprint"><?php echo $_language->module['imprint']; ?></a></li>
                                <li><a href="admincenter.php?site=bannerrotation"><?php echo $_language->module['bannerrotation']; ?></a></li>
                                <?php echo admincenternav(6); ?>
                            </ul>
                            <!-- /.nav-second-level -->
                        </li>

                        <?php
                        } if (isforumadmin($userID)) {
                        ?>

                        <li>
                            <a href="#"><i class="fa fa-list"></i> <?php echo $_language->module['forum']; ?><span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">
                                <li><a href="admincenter.php?site=boards"><?php echo $_language->module['boards']; ?></a></li>
                                <li><a href="admincenter.php?site=groups"><?php echo $_language->module['manage_user_groups']; ?></a></li>
                                <li><a href="admincenter.php?site=group-users"><?php echo $_language->module['manage_group_users']; ?></a></li>
                                <li><a href="admincenter.php?site=ranks"><?php echo $_language->module['user_ranks']; ?></a></li>
                                <?php echo admincenternav(7); ?>
                            </ul>
                            <!-- /.nav-second-level -->
                        </li>
 
                        <?php
                        } if (isgalleryadmin($userID)) {
                        ?>
                        <li>
                            <a href="#"><i class="fa fa-file-image-o"></i> <?php echo $_language->module['gallery']; ?><span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">
                                <li><a href="admincenter.php?site=gallery&amp;part=groups"><?php echo $_language->module['manage_groups']; ?></a></li>
                                <li><a href="admincenter.php?site=gallery&amp;part=gallerys"><?php echo $_language->module['manage_galleries']; ?></a></li>
                                <?php echo admincenternav(8); ?>
                            </ul>
                            <!-- /.nav-second-level -->
                        </li>

                        <?php
                        } if (ispageadmin($userID)) {
                        ?>
                        <li>
                            <a href="#"><i class="fa fa-arrow-right"></i> <?php echo $_language->module['plugin_base']; ?><span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">
                            <li><a href="admincenter.php?site=plugin-manager"><?php echo $_language->module['plugin_manages']; ?></a></li>
                            
                                <?php echo admincenternav(9); ?>
                            </ul>
                            <!-- /.nav-second-level -->
                        </li>

                                
                        <?php echo addonnav(); ?>
                        </li>
                        <?php
                        } ?>
                       
                    </ul>
                </div>
                <!-- /.sidebar-collapse -->
        <!-- Copy -->
        <div class="copy">
        <em>&nbsp;&copy; 2016 webspell-nor.de&nbsp;Admin Template by <a href="http://designperformance.de/" target="_blank">T-Seven</a></em>
        </div>
            </div>
            <!-- /.navbar-static-side -->
        </nav>

        <div id="page-wrapper">
            <div class="row">
                
                <!-- /.col-lg-12 -->
                <div class="col-lg-12">
                <br>
                <?php
    if (isset($site) && $site!="news") {
        $invalide = array('\\','/','//',':','.');
        $site = str_replace($invalide, ' ', $site);
        if (file_exists($site.'.php')) {
            include($site.'.php');
        } else {
			// Load Plugins-Admin-File (if exists)
			chdir("../");
			$plugin = $load->plugin_data($site,0,true);
			$plugin_path = $plugin['path'];
			if(file_exists($plugin_path."admin/".$plugin['admin_file'].".php")) {		
				include($plugin_path."admin/".$plugin['admin_file'].".php");
			} else {
				chdir("admin");
			echo "<b>Modul [or] Plugin Not found</b><br /><br />";
				include('overview.php');
			}
        }
    } else {
        include('overview.php');
    }

    
    ?>

            </div>
            </div>
            <!-- /.row -->
        </div>
        <!-- /#page-wrapper -->
    </div>
    <!-- /#wrapper -->

 <!-- jQuery -->
    <script src="../components/jquery/jquery.min.js"></script>
    <link rel="stylesheet" type="text/css" media="all" href="../components/admin/css/style-nav.css">
    <link href="../components/admin/css/bootstrap-colorpicker.min.css" rel="stylesheet">
    <script src="../components/admin/js/bootstrap-colorpicker.js"></script>
    <script>  
        jQuery(function($) { 
            $('#cp1').colorpicker(); 
            $('#cp2').colorpicker();
            $('#cp3').colorpicker();
            $('#cp4').colorpicker();
            $('#cp5').colorpicker();
            $('#cp6').colorpicker();
            $('#cp7').colorpicker();
            $('#cp8').colorpicker();
            $('#cp9').colorpicker();
            $('#cp10').colorpicker();
            $('#cp11').colorpicker();
            $('#cp12').colorpicker();
            $('#cp13').colorpicker();
            $('#cp14').colorpicker();
            $('#cp15').colorpicker();
            $('#cp16').colorpicker();
            $('#cp17').colorpicker();
            $('#cp18').colorpicker();
            $('#cp19').colorpicker();
            $('#cp20').colorpicker();
            $('#cp21').colorpicker();
            $('#cp22').colorpicker();
            $('#cp23').colorpicker();
            $('#cp24').colorpicker();
            $(document).ready(function(){
                $('[data-toggle="tooltip"]').tooltip(); 
            });
        }); 
    </script>
    <!-- Bootstrap Core JavaScript -->
    <script src="../components/bootstrap/bootstrap.min.js"></script>

    <!-- Menu Plugin JavaScript -->
    <script src="../components/admin/js/menu.min.js"></script>

    <!-- Custom Theme JavaScript -->
    <script src="../components/admin/js/page.js"></script>

<script src="../components/admin/js/index.js"></script>
<script>
        var calledfrom='admin';
    </script>
    <script src="../js/bbcode.js"></script>
<script src="../components/admin/js/bootstrap-switch.js"></script>

</body>
</html>
