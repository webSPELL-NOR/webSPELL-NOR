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

$_language->readModule('sc_randompic');

$all_users = array();$res = safe_query("SELECT userID FROM `".PREFIX."user`");
while($row=mysqli_fetch_array($res)) { 
	$all_users[ ] = $row[ 'userID' ]; 
}
$picpath = './images/userpics/';
//get randomPic
$anz = count($all_users);
if ($anz) {
	$the_user = $all_users[ rand(0, ($anz - 1)) ];
	$nickname = getnickname($the_user);
	$nickname_fixed = getinput($nickname);
	$registerdate = getregistered($the_user);
	$rex = safe_query("SELECT * FROM `".PREFIX."user` WHERE `userID`='".intval($the_user)."'");
	$rox = mysqli_fetch_array($rex);
	if(isset($rox[ 'userpic' ])) {
		if($rox[ 'userpic' ]=="") { 
			$upic = "nouserpic.png"; 
		} 
		else { 
			$upic = $rox[ 'userpic' ]; 
		}
	} 
	else { 
		$upic = "nouserpic.png"; 
	}
	$picurl = $picpath . $upic;

	$data_array = array();
	$data_array['$picID'] = $the_user;
	$data_array['$picurl'] = $picurl;
	$data_array['$nickname_fixed'] = $nickname_fixed;
	$data_array['$nickname'] = $nickname;
	$data_array['$registerdate'] = $registerdate;
	$sc_randompic = $GLOBALS["_template"]->replaceTemplate("sc_randompic", $data_array);
	echo $sc_randompic;
} else {
	echo $_language->module[ 'no_user' ];
}
