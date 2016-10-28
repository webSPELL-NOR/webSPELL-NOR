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

/*
@File:		Navigation
@Author:	Getschonnik
@Version:	1.1

*/
$_language->readModule('navigation');
function navigation_nodropdown($default_link) {
	# run if mod_Rewrite is activated
	$set = 0;
	$mr_res = mysqli_fetch_array(safe_query("SELECT * FROM `".PREFIX."settings` WHERE 1"));
	if($mr_res['modRewrite']==1) {
		$rem1 = explode("/",$_SERVER["REQUEST_URI"]); # "/.."
		$chks = explode("/",$rem1[1]);
		if(strpos($chks[0], '.') !== false) {
			$tmp = explode(".",$chks[0]);
			$newlink = "index.php?site=".$tmp[0];
			$set=1;
		} else {
			$tmp = explode("/",$chks[0]);
			$newlink = "index.php?site=".$tmp[0];
			$set=1;
		}
	}
	# run if mod_Rewrite deactivated
	try {
		if($set==0) {
			$link = explode("/",$_SERVER["REQUEST_URI"]);
			if(isset($link[1]))
				if($link[1]=="index.php") { $newlink = $default_link; } else { $newlink = $link[1];
			}
		}
		$rex = safe_query("SELECT * FROM `".PREFIX."navigation_sub` WHERE `link`='".$newlink."'");
		if(mysqli_num_rows($rex)) {
			$output = "";
			$rox = mysqli_fetch_array($rex);
			$res = safe_query("SELECT * FROM `".PREFIX."navigation_sub` WHERE `mnav_ID`='".intval($rox['mnav_ID'])."' AND `indropdown`='0' ORDER BY `sort`");
			while($row=mysqli_fetch_array($res)) {
				if(isset($_language->module[strtolower($row['name'])])) { $name = $_language->module[strtolower($row['name'])]; } else { $name = $row['name']; }
				$output .= '<li><a href="'.$row['link'].'">'.$name.'</a></li>';
			}
			return $output;
		}
	} catch (Exception $e) {
		if(DEBUG==="ON") {
			return $e->message();
		}
	}
}
# load main-nav && dropdown
try {
	$open = $GLOBALS["_template"]->loadTemplate("navigation","dd_open", array());
	echo $open;

	$res = safe_query("SELECT * FROM `".PREFIX."navigation_main` ORDER BY `sort`");
	$lo = 1;
	while($row=mysqli_fetch_array($res)) {
		if($row['isdropdown']==1) {
			$head_array = array();
			if(isset($_language->module[strtolower($row['name'])])) { $head_array['$name'] = $_language->module[strtolower($row['name'])]; } else { $head_array['$name'] = $row['name']; }
			$head_array['$link'] = $row['link'];
			if($lo==1) {
				if($loggedin) {
					$login_overview = '<li class="nav_login"><a href="index.php?site=loginoverview">'.$_language->module['overview'].'</a></li>';
				} else {
					$login_overview = '<li class="nav_login"><a href="index.php?site=login">'.$_language->module['login'].'</a></li>';
				}
				$ar = array(); $head_array['$login_overview'] =$login_overview;
			} else { $head_array['$login_overview'] =""; }
			$lo++;
			$head = $GLOBALS["_template"]->loadTemplate("navigation","dd_head", $head_array);
			echo $head;
			/* ropdown */
			$rex = safe_query("SELECT * FROM `".PREFIX."navigation_sub` WHERE `mnav_ID`='".$row['mnavID']."' ORDER BY `sort`");
			if(mysqli_num_rows($rex)) {
				$sopen = $GLOBALS["_template"]->loadTemplate("navigation","sub_open", array());
				echo $sopen;
				while($rox=mysqli_fetch_array($rex)) {
					if($rox['indropdown'] == 1) {
						$sub_array = array();
						if (strpos($rox['link'], 'http://') !== false) {
 							$sub_array['$link'] = $rox['link'].'" target="_blank';
 						} else {
 							$sub_array['$link'] = $rox['link'];
						}
						
						if(isset($_language->module[strtolower($rox['name'])])) { 
							$sub_array['$name'] = $_language->module[strtolower($rox['name'])]; 
						} else { 
							$sub_array['$name'] = $rox['name']; 
						}
						$sub = $GLOBALS["_template"]->loadTemplate("navigation","sub_nav", $sub_array);
						echo $sub;
					}
				}
				$sclose = $GLOBALS["_template"]->loadTemplate("navigation","sub_close", array());
				echo $sclose;
			}
		} else {
			$sub_array = array();
			if(isset($_language->module[strtolower($row['name'])])) {
				$sub_array['$name'] = $_language->module[strtolower($row['name'])];
			}
			else {
				$sub_array['$name'] = $row['name'];
			}
			$sub_array['$link'] = $row['link'];
			$main_head = $GLOBALS["_template"]->loadTemplate("navigation","main_head", $sub_array);
			echo $main_head;

		}
		unset($sub_array, $sub);
		$head = $GLOBALS["_template"]->loadTemplate("navigation","dd_foot", array());
		echo $head;
	}
} catch (Exception $e) {
	echo $e->message();
	return false;
}

# $navigation = $GLOBALS["_template"]->replaceTemplate("navigation", array());
# echo $navigation;
