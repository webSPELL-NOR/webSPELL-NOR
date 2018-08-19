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

$_language->readModule('privacy_policy');

$title_privacy_policy = $GLOBALS["_template"]->replaceTemplate("title_privacy_policy", array());
echo $title_privacy_policy;

$ergebnis = safe_query("SELECT * FROM " . PREFIX . "privacy_policy");
if (mysqli_num_rows($ergebnis)) {
    $ds = mysqli_fetch_array($ergebnis);

    $privacy_policy = htmloutput($ds[ 'privacy_policy' ]);
    $privacy_policy = toggle($privacy_policy, 1);

    #$bg1 = BG_1;
    $data_array = array();
    $data_array['$privacy_policy'] = $privacy_policy;
    $privacy_policy = $GLOBALS["_template"]->replaceTemplate("privacy_policy", $data_array);
    echo $privacy_policy;
} else {
    echo generateAlert($_language->module['no_privacy_policy'], 'alert-info');
}
