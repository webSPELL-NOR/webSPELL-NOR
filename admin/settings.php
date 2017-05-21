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

$_language->readModule('settings', false, true);

if (!ispageadmin($userID) || mb_substr(basename($_SERVER[ 'REQUEST_URI' ]), 0, 15) != "admincenter.php") {
    die($_language->module[ 'access_denied' ]);
}

if (isset($_POST[ 'submit' ])) {
    $CAPCLASS = new \webspell\Captcha;
    if ($CAPCLASS->checkCaptcha(0, $_POST[ 'captcha_hash' ])) {
        safe_query(
            "UPDATE
                " . PREFIX . "settings
            SET
                hpurl='" . $_POST[ 'url' ] . "',
                clanname='" . $_POST[ 'clanname' ] . "',
                clantag='" . $_POST[ 'clantag' ] . "',
                adminname='" . $_POST[ 'admname' ] . "',
                adminemail='" . $_POST[ 'admmail' ] . "',
                news='" . $_POST[ 'news' ] . "',
                newsarchiv='" . $_POST[ 'newsarchiv' ] . "',
                headlines='" . $_POST[ 'headlines' ] . "',
                headlineschars='" . $_POST[ 'headlineschars' ] . "',
                topnewschars='" . $_POST[ 'topnewschars' ] . "',
                articles='" . $_POST[ 'articles' ] . "',
                latestarticles='" . $_POST[ 'latestart' ] . "',
                articleschars='" . $_POST[ 'articlesch' ] . "',
                clanwars='" . $_POST[ 'clanwars' ] . "',
                results='" . $_POST[ 'results' ] . "',
                upcoming='" . $_POST[ 'upcoming' ] . "',
                shoutbox='" . $_POST[ 'shoutbox' ] . "',
                sball='" . $_POST[ 'sball' ] . "',
                sbrefresh='" . $_POST[ 'refresh' ] . "',
                topics='" . $_POST[ 'topics' ] . "',
                posts='" . $_POST[ 'posts' ] . "',
                latesttopics='" . $_POST[ 'latesttopics' ] . "',
                latesttopicchars='" . $_POST[ 'latesttopicchars' ] . "',
                awards='" . $_POST[ 'awards' ] . "',
                demos='" . $_POST[ 'demos' ] . "',
                guestbook='" . $_POST[ 'guestbook' ] . "',
                feedback='" . $_POST[ 'feedback' ] . "',
                messages='" . $_POST[ 'messages' ] . "',
                users='" . $_POST[ 'users' ] . "',
                sessionduration='" . $_POST[ 'sessionduration' ] . "',
                gb_info='" . isset($_POST[ 'gb_info' ]) . "',
                picsize_l='" . $_POST[ 'picsize_l' ] . "',
                picsize_h='" . $_POST[ 'picsize_h' ] . "',
                pictures='" . $_POST[ 'pictures' ] . "',
                publicadmin='" . isset($_POST[ 'publicadmin' ]) . "',
                thumbwidth='" . $_POST[ 'thumbwidth' ] . "',
                usergalleries='" . isset($_POST[ 'usergalleries' ]) . "',
                maxusergalleries='" . ($_POST[ 'maxusergalleries' ] * 1024 * 1024) . "',
                profilelast='" . $_POST[ 'lastposts' ] . "',
                default_language='" . $_POST[ 'language' ] . "',
                insertlinks='" . isset($_POST[ 'insertlinks' ]) . "',
                search_min_len='" . $_POST[ 'searchminlen' ] . "',
                max_wrong_pw='" . intval($_POST[ 'max_wrong_pw' ]) . "',
                captcha_type='" . intval($_POST[ 'captcha_type' ]) . "',
                captcha_bgcol='" . $_POST[ 'captcha_bgcol' ] . "',
                captcha_fontcol='" . $_POST[ 'captcha_fontcol' ] . "',
                captcha_math='" . $_POST[ 'captcha_math' ] . "',
                captcha_noise='" . $_POST[ 'captcha_noise' ] . "',
                captcha_linenoise='" . $_POST[ 'captcha_linenoise' ] . "',
                spamapikey='" . $_POST[ 'spamapikey' ] . "',
                spamapihost='" . $_POST[ 'spamapihost' ] . "',
                spammaxposts='" . $_POST[ 'spammaxposts' ] . "',
                spam_check='" . isset($_POST[ 'spam_check' ]) . "',
                spamapiblockerror='" . isset($_POST[ 'spamapiblockerror' ]) . "',
                detect_language='" . isset($_POST[ 'detectLanguage' ]) . "',
                date_format='" . $_POST[ 'date_format' ] . "',
                time_format='" . $_POST[ 'time_format' ] . "',
                user_guestbook='" . $_POST[ 'user_guestbook' ] . "',
                autoresize='" . $_POST[ 'autoresize' ] . "',
                register_per_ip='"	. isset($_POST[ 'register_per_ip' ]) . "',
                sc_demos='" . intval($_POST[ 'sc_demos' ]) . "',
                sc_files='" . intval($_POST[ 'sc_files' ]) . "' "
        );
        safe_query("UPDATE " . PREFIX . "styles SET title='" . $_POST[ 'title' ] . "' ");
        redirect("admincenter.php?site=settings", "", 0);
    } else {
        redirect("admincenter.php?site=settings", $_language->module[ 'transaction_invalid' ], 3);
    }
} else {
    $settings = safe_query("SELECT * FROM " . PREFIX . "settings");
    $ds = mysqli_fetch_array($settings);

    $styles = safe_query("SELECT * FROM " . PREFIX . "styles");
    $dt = mysqli_fetch_array($styles);

    if ($ds[ 'gb_info' ]) {
        $gb_info = '<input type="checkbox" name="gb_info" value="1" checked="checked"
		/>';
    } else {
        $gb_info = '<input type="checkbox" name="gb_info" value="1" />';
    }
    
    if ($ds[ 'register_per_ip' ]) {
        $register_per_ip = '<input type="checkbox" name="register_per_ip" value="1" checked="checked"
		/>';
    } else {
        $register_per_ip = '<input type="checkbox" name="register_per_ip" value="1" />';
    }

    if ($ds[ 'spam_check' ]) {
        $spam_check = '<input type="checkbox" name="spam_check" value="1" checked="checked"
        />';
    } else {
        $spam_check = '<input type="checkbox" name="spam_check" value="1" />';
    }

    if ($ds[ 'detect_language' ]) {
        $visitor_language = '<input type="checkbox" name="detectLanguage" value="1" checked="checked"
        />';
    } else {
        $visitor_language = '<input type="checkbox" name="detectLanguage" value="1" />';
    }

    if ($ds[ 'publicadmin' ]) {
        $publicadmin = " checked=\"checked\"";
    } else {
        $publicadmin = "";
    }
    if ($ds[ 'usergalleries' ]) {
        $usergalleries = " checked=\"checked\"";
    } else {
        $usergalleries = "";
    }

    if ($ds[ 'spamapiblockerror' ]) {
        $spamapiblockerror = '<input type="checkbox" name="spamapiblockerror" value="1" checked="checked"
        />';
    } else {
        $spamapiblockerror = '<input type="checkbox" name="spamapiblockerror" value="1" />';
    }

    $langdirs = '';
    $filepath = "../languages/";

    $mysql_langs = array();
    $query = safe_query("SELECT lang, language FROM " . PREFIX . "news_languages");
    while ($sql_lang = mysqli_fetch_assoc($query)) {
        $mysql_langs[ $sql_lang[ 'lang' ] ] = $sql_lang[ 'language' ];
    }
    $langs = array();
    if ($dh = opendir($filepath)) {
        while ($file = mb_substr(readdir($dh), 0, 2)) {
            if ($file != "." && $file != ".." && is_dir($filepath . $file)) {
                if (isset($mysql_langs[ $file ])) {
                    $name = $mysql_langs[ $file ];
                    $name = ucfirst($name);
                    $langs[ $name ] = $file;
                } else {
                    $langs[ $file ] = $file;
                }
            }
        }
        closedir($dh);
    }
    ksort($langs, SORT_NATURAL);
    foreach ($langs as $lang => $flag) {
        $langdirs .= '<option value="' . $flag . '">' . $lang . '</option>';
    }
    $lang = $default_language;
    $langdirs = str_replace('value="' . $lang . '"', 'value="' . $lang . '" selected="selected"', $langdirs);

    if ($ds[ 'insertlinks' ]) {
        $insertlinks = '<input type="checkbox" name="insertlinks" value="1" checked="checked"
        />';
    } else {
        $insertlinks = '<input type="checkbox" name="insertlinks" value="1" />';
    }

    $captcha_style = "<option value='0'>" . $_language->module[ 'captcha_only_text' ] . "</option><option value='2'>" .
        $_language->module[ 'captcha_both' ] . "</option><option value='1'>" .
        $_language->module[ 'captcha_only_math' ] . "</option>";
    $captcha_style = str_replace(
        "value='" . $ds[ 'captcha_math' ] . "'",
        "value='" . $ds[ 'captcha_math' ] . "' selected='selected'",
        $captcha_style
    );

    $captcha_type = "<option value='0'>" . $_language->module[ 'captcha_text' ] . "</option><option value='2'>" .
        $_language->module[ 'captcha_autodetect' ] . "</option><option value='1'>" .
        $_language->module[ 'captcha_image' ] . "</option>";
    $captcha_type = str_replace(
        "value='" . $ds[ 'captcha_type' ] . "'",
        "value='" . $ds[ 'captcha_type' ] . "' selected='selected'",
        $captcha_type
    );

    $sc_demos = "<option value='1'>" . $_language->module[ 'demos_top' ] . "</option><option value='2'>" .
        $_language->module[ 'demos_latest' ] . "</option>";
    $sc_demos = str_replace(
        "value='" . $ds[ 'sc_demos' ] . "'",
        "value='" . $ds[ 'sc_demos' ] . "' selected='selected'",
        $sc_demos
    );

    $sc_files = "<option value='1'>" . $_language->module[ 'files_top' ] . "</option><option value='2'>" .
        $_language->module[ 'files_latest' ] . "</option>";
    $sc_files = str_replace(
        "value='" . $ds[ 'sc_files' ] . "'",
        "value='" . $ds[ 'sc_files' ] . "' selected='selected'",
        $sc_files
    );



    $format_date = "<option value='d.m.y'>DD.MM.YY</option>
                    <option value='d.m.Y'>DD.MM.YYYY</option>
                    <option value='j.n.y'>D.M.YY</option>
                    <option value='j.n.Y'>D.M.YYYY</option>
                    <option value='y-m-d'>YY-MM-DD</option>
                    <option value='Y-m-d'>YYYY-MM-DD</option>
                    <option value='y/m/d'>YY/MM/DD</option>
                    <option value='Y/m/d'>YYYY/MM/DD</option>";
    $format_date = str_replace(
        "value='" . $ds[ 'date_format' ] . "'",
        "value='" . $ds[ 'date_format' ] . "' selected='selected'",
        $format_date
    );

    $format_time = "<option value='G:i'>H:MM</option>
                    <option value='H:i'>HH:MM</option>
                    <option value='G:i a'>H:MM am/pm</option>
                    <option value='H:i a'>HH:MM am/pm</option>
                    <option value='G:i A'>H:MM AM/PM</option>
                    <option value='H:i A'>HH:MM AM/PM</option>
                    <option value='G:i:s'>H:MM:SS</option>
                    <option value='H:i:s'>HH:MM:SS</option>
                    <option value='G:i:s a'>H:MM:SS am/pm</option>
                    <option value='H:i:s a'>HH:MM:SS am/pm</option>
                    <option value='G:i:s A'>H:MM:SS AM/PM</option>
                    <option value='H:i:s A'>HH:MM:SS AM/PM</option>";
    $format_time = str_replace(
        "value='" . $ds[ 'time_format' ] . "'",
        "value='" . $ds[ 'time_format' ] . "' selected='selected'",
        $format_time
    );

    $autoresize = "<option value='0'>" . $_language->module[ 'autoresize_off' ] . "</option><option value='2'>" .
        $_language->module[ 'autoresize_js' ] . "</option><option value='1'>" . $_language->module[ 'autoresize_php' ] .
        "</option>";
    $autoresize = str_replace(
        "value='" . $ds[ 'autoresize' ] . "'",
        "value='" . $ds[ 'autoresize' ] . "' selected='selected'",
        $autoresize
    );

    $user_gbook = "<option value='0'>" . $_language->module[ 'deactivated' ] . "</option><option value='1'>" .
        $_language->module[ 'activated' ] . "</option>";
    $user_gbook = str_replace(
        "value='" . $ds[ 'user_guestbook' ] . "'",
        "value='" . $ds[ 'user_guestbook' ] . "' selected='selected'",
        $user_gbook
    );

    $CAPCLASS = new \webspell\Captcha;
    $CAPCLASS->createTransaction();
    $hash = $CAPCLASS->getHash();
    ?>
    <form method="post" action="admincenter.php?site=settings">
        <div class="panel panel-default">
            <div class="panel-heading">
                <?php echo $_language->module[ 'settings' ]; ?>
            </div>

            <div class="panel-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="row bt">
                            <div class="col-md-6">
                                <?php echo $_language->module['page_title']; ?>:
                            </div>

                            <div class="col-md-6">
                                <span class="pull-right text-muted small"><em data-toggle="tooltip" title="<?php echo $_language->module[ 'tooltip_2' ]; ?>"><input class="form-control" name="title" type="text" value="<?php echo getinput($dt['title']); ?>" size="35"></em></span>
                            </div>
                        </div>

                        <div class="row bt">
                            <div class="col-md-6">
                                <?php echo $_language->module['clan_name']; ?>:
                            </div>

                            <div class="col-md-6">
                                <span class="pull-right text-muted small"><em data-toggle="tooltip" title="<?php echo $_language->module[ 'tooltip_3' ]; ?>"><input class="form-control" type="text" name="clanname" value="<?php echo getinput($ds['clanname']); ?>" size="35"></em></span>
                            </div>
                        </div>

                        <div class="row bt">
                            <div class="col-md-6">
                                <?php echo $_language->module['admin_name']; ?>:
                            </div>

                            <div class="col-md-6">
                                <span class="pull-right text-muted small"><em data-toggle="tooltip" title="<?php echo $_language->module[ 'tooltip_5' ]; ?>"><input class="form-control" type="text" name="admname" value="<?php echo getinput($ds['adminname']); ?>" size="35" ></em></span>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="row bt">
                            <div class="col-md-6">
                                <?php echo $_language->module['page_url']; ?>:
                            </div>

                            <div class="col-md-6">
                                <span class="pull-right text-muted small"><em data-toggle="tooltip" title="<?php echo $_language->module[ 'tooltip_1' ]; ?>"><input class="form-control" type="text" name="url" value="<?php echo getinput($ds['hpurl']); ?>" size="35"></em></span>
                            </div>
                        </div>

                        <div class="row bt">
                            <div class="col-md-6">
                                <?php echo $_language->module['clan_tag']; ?>:
                            </div>

                            <div class="col-md-6">
                                <span class="pull-right text-muted small"><em data-toggle="tooltip" title="<?php echo $_language->module[ 'tooltip_4' ]; ?>"><input class="form-control" type="text" name="clantag" value="<?php echo getinput($ds['clantag']); ?>" size="35"></em></span>
                            </div>
                        </div>

                        <div class="row bt">
                            <div class="col-md-6">
                                <?php echo $_language->module['admin_email']; ?>:
                            </div>

                            <div class="col-md-6">
                                <span class="pull-right text-muted small"><em data-toggle="tooltip" title="<?php echo $_language->module[ 'tooltip_6' ]; ?>"><input class="form-control" type="text" name="admmail" value="<?php echo getinput($ds['adminemail']); ?>" size="35"></em></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div><br>

        <div class="panel panel-default">
            <div class="panel-heading">
                <?php echo $_language->module['additional_options']; ?>
            </div>

            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12">
                        <a class="btn btn-danger" href="admincenter.php?site=lock"><?php echo $_language->module['pagelock']; ?></a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <?php echo $_language->module['news']; ?>
                    </div>

                    <div class="panel-body">
                        <div class="row bt">
                            <div class="col-md-6">
                                <?php echo $_language->module['news']; ?>:
                            </div>

                            <div class="col-md-6">
                                <span class="pull-right text-muted small"><em data-toggle="tooltip" title="<?php echo $_language->module[ 'tooltip_7' ]; ?>"><input class="form-control" name="news" type="text" value="<?php echo $ds['news']; ?>" size="3"></em></span>
                            </div>
                        </div>

                        <div class="row bt">
                            <div class="col-md-6">
                                <?php echo $_language->module['archive']; ?>:
                            </div>

                            <div class="col-md-6">
                                <span class="pull-right text-muted small"><em data-toggle="tooltip" title="<?php echo $_language->module[ 'tooltip_10' ]; ?>"><input class="form-control" name="newsarchiv" type="text" value="<?php echo $ds['newsarchiv']; ?>" size="3"></em></span>
                            </div>
                        </div>

                        <div class="row bt">
                            <div class="col-md-6">
                                <?php echo $_language->module['headlines']; ?>:
                            </div>

                            <div class="col-md-6">
                                <span class="pull-right text-muted small"><em data-toggle="tooltip" title="<?php echo $_language->module[ 'tooltip_13' ]; ?>"><input class="form-control" type="text" name="headlines" value="<?php echo $ds['headlines']; ?>" size="3"></em></span>
                            </div>
                        </div>

                        <div class="row bt">
                            <div class="col-md-6">
                                <?php echo $_language->module['max_length_headlines']; ?>:
                            </div>

                            <div class="col-md-6">
                                <span class="pull-right text-muted small"><em data-toggle="tooltip" title="<?php echo $_language->module[ 'tooltip_16' ]; ?>"><input class="form-control" type="text" name="headlineschars" value="<?php echo $ds['headlineschars']; ?>" size="3"></em></span>
                            </div>
                        </div>

                        <div class="row bt">
                            <div class="col-md-6">
                                <?php echo $_language->module['max_length_topnews']; ?>:
                            </div>

                            <div class="col-md-6">
                                <span class="pull-right text-muted small"><em data-toggle="tooltip" title="<?php echo $_language->module[ 'tooltip_51' ]; ?>"><input class="form-control" type="text" name="topnewschars" value="<?php echo $ds['topnewschars']; ?>" size="3"></em></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <?php echo $_language->module['gallery']; ?>
                    </div>

                    <div class="panel-body">
                        <div class="row bt">
                            <div class="col-md-6">
                                <?php echo $_language->module['pictures']; ?>:
                            </div>

                            <div class="col-md-6">
                                <span class="pull-right text-muted small"><em data-toggle="tooltip" title="<?php echo $_language->module[ 'tooltip_9' ]; ?>"><input class="form-control" type="text" name="pictures" value="<?php echo $ds['pictures']; ?>" size="3"></em></span>
                            </div>
                        </div>

                        <div class="row bt">
                            <div class="col-md-6">
                                <?php echo $_language->module['thumb_width']; ?>:
                            </div>

                            <div class="col-md-6">
                                <span class="pull-right text-muted small"><em data-toggle="tooltip" title="<?php echo $_language->module[ 'tooltip_12' ]; ?>"><input class="form-control" type="text" name="thumbwidth" value="<?php echo $ds['thumbwidth']; ?>" size="3"></em></span>
                            </div>
                        </div>

                        <div class="row bt">
                            <div class="col-md-6">
                                <?php echo $_language->module['space_user']; ?>:
                            </div>

                            <div class="col-md-6">
                                <span class="pull-right text-muted small"><em data-toggle="tooltip" title="<?php echo $_language->module[ 'tooltip_15' ]; ?>"><input class="form-control" type="text" name="maxusergalleries" value="<?php echo ($ds['maxusergalleries']/(1024*1024)); ?>" size="3"></em></span>
                            </div>
                        </div>

                        <div class="row bt">
                            <div class="col-md-6">
                                <?php echo $_language->module['allow_usergalleries']; ?>:
                            </div>

                            <div class="col-md-6">
                                <span class="pull-right text-muted small"><em data-toggle="tooltip" title="<?php echo $_language->module[ 'tooltip_18' ]; ?>"><input class="form-control" type="checkbox" name="usergalleries" value="1" <?php echo $usergalleries; ?>></em></span>
                            </div>
                        </div>

                        <div class="row bt">
                            <div class="col-md-6">
                                <?php echo $_language->module['public_admin']; ?>:
                            </div>

                            <div class="col-md-6">
                                <span class="pull-right text-muted small"><em data-toggle="tooltip" title="<?php echo $_language->module[ 'tooltip_19' ]; ?>"><input class="form-control" type="checkbox" name="publicadmin" value="1" <?php echo $publicadmin; ?>></em></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <?php echo $_language->module['forum']; ?>
                    </div>

                    <div class="panel-body">
                        <div class="row bt">
                            <div class="col-md-6">
                                <?php echo $_language->module['forum_topics']; ?>:
                            </div>

                            <div class="col-md-6">
                                <span class="pull-right text-muted small"><em data-toggle="tooltip" title="<?php echo $_language->module[ 'tooltip_8' ]; ?>"><input class="form-control" type="text" name="topics" value="<?php echo $ds['topics']; ?>" size="3" ></em></span>
                            </div>
                        </div>

                        <div class="row bt">
                            <div class="col-md-6">
                                <?php echo $_language->module['forum_posts']; ?>:
                            </div>

                            <div class="col-md-6">
                                <span class="pull-right text-muted small"><em data-toggle="tooltip" title="<?php echo $_language->module[ 'tooltip_11' ]; ?>"><input class="form-control" type="text" name="posts" value="<?php echo $ds['posts']; ?>" size="3"></em></span>
                            </div>
                        </div>

                        <div class="row bt">
                            <div class="col-md-6">
                                <?php echo $_language->module['latest_topics']; ?>:
                            </div>

                            <div class="col-md-6">
                                <span class="pull-right text-muted small"><em data-toggle="tooltip" title="<?php echo $_language->module[ 'tooltip_14' ]; ?>"><input class="form-control" type="text" name="latesttopics" value="<?php echo $ds['latesttopics']; ?>" size="3"></em></span>
                            </div>
                        </div>

                        <div class="row bt">
                            <div class="col-md-6">
                                <?php echo $_language->module['max_length_latest_topics']; ?>:
                            </div>

                            <div class="col-md-6">
                                <span class="pull-right text-muted small"><em data-toggle="tooltip" title="<?php echo $_language->module[ 'tooltip_42' ]; ?>"><input class="form-control" type="text" name="latesttopicchars" value="<?php echo $ds['latesttopicchars']; ?>" size="3"></em></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <?php echo $_language->module['captcha']; ?>
                    </div>

                    <div class="panel-body">
                        <div class="row bt">
                            <div class="col-md-6">
                                <?php echo $_language->module['captcha_type']; ?>:
                            </div>

                            <div class="col-md-6">
                                <span class="pull-right text-muted small"><em data-toggle="tooltip" title="<?php echo $_language->module[ 'tooltip_44' ]; ?>"><select class="form-control" name="captcha_type">
                                    <?php echo $captcha_type;?>
                                </select></em></span>
                            </div>
                        </div>

                        <div class="row bt">
                            <div class="col-md-6">
                                <?php echo $_language->module['captcha_bgcol']; ?>:
                            </div>

                            <div class="col-md-6">
                                <span class="pull-right text-muted small"><em data-toggle="tooltip" title="<?php echo $_language->module[ 'tooltip_45' ]; ?>"><input class="form-control" type="text" name="captcha_bgcol" size="8" value="<?php echo $ds['captcha_bgcol']; ?>"></em></span>
                            </div>
                        </div>

                        <div class="row bt">
                            <div class="col-md-6">
                                <?php echo $_language->module['captcha_fontcol']; ?>:
                            </div>

                            <div class="col-md-6">
                                <span class="pull-right text-muted small"><em data-toggle="tooltip" title="<?php echo $_language->module[ 'tooltip_46' ]; ?>"><input class="form-control" type="text" name="captcha_fontcol" size="8" value="<?php echo $ds['captcha_fontcol']; ?>"></em></span>
                            </div>
                        </div>

                        <div class="row bt">
                            <div class="col-md-6">
                                <?php echo $_language->module['captcha_style']; ?>:
                            </div>

                            <div class="col-md-6">
                                <span class="pull-right text-muted small"><em data-toggle="tooltip" title="<?php echo $_language->module[ 'tooltip_47' ]; ?>"><select class="form-control" name="captcha_math">
                                    <?php echo $captcha_style;?>
                                </select></em></span>
                            </div>
                        </div>

                        <div class="row bt">
                            <div class="col-md-6">
                                <?php echo $_language->module['captcha_noise']; ?>:
                            </div>

                            <div class="col-md-6">
                                <span class="pull-right text-muted small"><em data-toggle="tooltip" title="<?php echo $_language->module[ 'tooltip_48' ]; ?>"><input class="form-control" type="text" name="captcha_noise" size="3" value="<?php echo $ds['captcha_noise']; ?>"></em></span>
                            </div>
                        </div>

                        <div class="row bt">
                            <div class="col-md-6">
                                <?php echo $_language->module['captcha_linenoise']; ?>:
                            </div>

                            <div class="col-md-6">
                                <span class="pull-right text-muted small"><em data-toggle="tooltip" title="<?php echo $_language->module[ 'tooltip_49' ]; ?>"><input class="form-control" type="text" name="captcha_linenoise" size="3" value="<?php echo $ds['captcha_linenoise']; ?>"></em></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <?php echo $_language->module['articles']; ?>
                    </div>

                    <div class="panel-body">
                        <div class="row bt">
                            <div class="col-md-6">
                                <?php echo $_language->module['articles']; ?>:
                            </div>

                            <div class="col-md-6">
                                <span class="pull-right text-muted small"><em data-toggle="tooltip" title="<?php echo $_language->module[ 'tooltip_20' ]; ?>"><input class="form-control" name="articles" type="text" value="<?php echo $ds['articles']; ?>" size="3"></em></span>
                            </div>
                        </div>

                        <div class="row bt">
                            <div class="col-md-6">
                                <?php echo $_language->module['latest_articles']; ?>:
                            </div>

                            <div class="col-md-6">
                                <span class="pull-right text-muted small"><em data-toggle="tooltip" title="<?php echo $_language->module[ 'tooltip_22' ]; ?>"><input class="form-control" name="latestart" type="text" id="latestart" value="<?php echo $ds['latestarticles']; ?>" size="3"></em></span>
                            </div>
                        </div>

                        <div class="row bt">
                            <div class="col-md-6">
                                <?php echo $_language->module['max_length_latest_articles']; ?>:
                            </div>

                            <div class="col-md-6">
                                <span class="pull-right text-muted small"><em data-toggle="tooltip" title="<?php echo $_language->module[ 'tooltip_24' ]; ?>"><input class="form-control" name="articlesch" type="text" id="articlesch" value="<?php echo $ds['articleschars']; ?>" size="3"></em></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <?php echo $_language->module['clanwars']; ?>
                    </div>

                    <div class="panel-body">
                        <div class="row bt">
                            <div class="col-md-6">
                                <?php echo $_language->module['clanwars']; ?>:
                            </div>

                            <div class="col-md-6">
                                <span class="pull-right text-muted small"><em data-toggle="tooltip" title="<?php echo $_language->module[ 'tooltip_28' ]; ?>"><input class="form-control" type="text" name="clanwars" value="<?php echo $ds['clanwars']; ?>" size="3"></em></span>
                            </div>
                        </div>

                        <div class="row bt">
                            <div class="col-md-6">
                                <?php echo $_language->module['latest_results']; ?>:
                            </div>

                            <div class="col-md-6">
                                <span class="pull-right text-muted small"><em data-toggle="tooltip" title="<?php echo $_language->module[ 'tooltip_30' ]; ?>"><input class="form-control" name="results" type="text" value="<?php echo $ds['results']; ?>" size="3"></em></span>
                            </div>
                        </div>

                        <div class="row bt">
                            <div class="col-md-6">
                                <?php echo $_language->module['upcoming_actions']; ?>:
                            </div>

                            <div class="col-md-6">
                                <span class="pull-right text-muted small"><em data-toggle="tooltip" title="<?php echo $_language->module[ 'tooltip_32' ]; ?>"><input class="form-control" type="text" name="upcoming" value="<?php echo $ds['upcoming']; ?>" size="3"></em></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <?php echo $_language->module['shoutbox']; ?>
                    </div>

                    <div class="panel-body">
                        <div class="row bt">
                            <div class="col-md-6">
                                <?php echo $_language->module['shoutbox']; ?>:
                            </div>

                            <div class="col-md-6">
                                <span class="pull-right text-muted small"><em data-toggle="tooltip" title="<?php echo $_language->module[ 'tooltip_37' ]; ?>"><input class="form-control" type="text" name="shoutbox" value="<?php echo $ds['shoutbox']; ?>" size="3"></em></span>
                            </div>
                        </div>

                        <div class="row bt">
                            <div class="col-md-6">
                                <?php echo $_language->module['shoutbox_all_messages']; ?>:
                            </div>

                            <div class="col-md-6">
                                <span class="pull-right text-muted small"><em data-toggle="tooltip" title="<?php echo $_language->module[ 'tooltip_38' ]; ?>"><input class="form-control" type="text" name="sball" value="<?php echo $ds['sball']; ?>" size="3"></em></span>
                            </div>
                        </div>

                        <div class="row bt">
                            <div class="col-md-6">
                                <?php echo $_language->module['shoutbox_refresh']; ?>:
                            </div>

                            <div class="col-md-6">
                                <span class="pull-right text-muted small"><em data-toggle="tooltip" title="<?php echo $_language->module[ 'tooltip_39' ]; ?>"><input class="form-control" type="text" name="refresh" value="<?php echo $ds['sbrefresh']; ?>" size="3"></em></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading">
                        <?php echo $_language->module[ 'sc_modules' ]; ?>
                    </div>

                    <div class="panel-body">
                        <div class="row bt">
                            <div class="col-md-6">
                                <?php echo $_language->module[ 'demos' ]; ?>:
                            </div>

                            <div class="col-md-6">
                                <span class="pull-right text-muted small"><em data-toggle="tooltip" title="<?php echo $_language->module[ 'tooltip_61' ]; ?>"><select class="form-control" name="sc_demos">
                                    <?php echo $sc_demos; ?>
                                </select></em></span>
                            </div>
                        </div>

                        <div class="row bt">
                            <div class="col-md-6">
                                <?php echo $_language->module[ 'files' ]; ?>:
                            </div>

                            <div class="col-md-6">
                                <span class="pull-right text-muted small"><em data-toggle="tooltip" title="<?php echo $_language->module[ 'tooltip_62' ]; ?>"><select class="form-control" name="sc_files">
                                    <?php echo $sc_files; ?>
                                </select></em></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <?php echo $_language->module[ 'spamfilter' ]; ?>
                    </div>

                    <div class="panel-body">
                        <div class="row bt">
                            <div class="col-md-6">
                                <?php echo $_language->module[ 'spamapikey' ]; ?>:
                            </div>

                            <div class="col-md-6">
                                <span class="pull-right text-muted small"><em data-toggle="tooltip" title="<?php echo $_language->module[ 'tooltip_54' ]; ?>"><input class="form-control" type="text" name="spamapikey" value="<?php echo $ds[ 'spamapikey' ]; ?>" size="32"></em></span>
                            </div>
                        </div>

                        <div class="row bt">
                            <div class="col-md-6">
                                <?php echo $_language->module[ 'spamapihost' ]; ?>:
                            </div>

                            <div class="col-md-6">
                                <span class="pull-right text-muted small"><em data-toggle="tooltip" title="<?php echo $_language->module[ 'tooltip_55' ]; ?>"><input class="form-control" type="text" name="spamapihost" value="<?php echo $ds[ 'spamapihost' ]; ?>" size="32"></em></span>
                            </div>
                        </div>

                        <div class="row bt">
                            <div class="col-md-6">
                                <?php echo $_language->module[ 'spammaxposts' ]; ?>:
                            </div>

                            <div class="col-md-6">
                                <span class="pull-right text-muted small"><em data-toggle="tooltip" title="<?php echo $_language->module[ 'tooltip_56' ]; ?>"><input class="form-control" type="text" name="spammaxposts" value="<?php echo $ds[ 'spammaxposts' ]; ?>" size="3"></em></span>
                            </div>
                        </div>

                        <div class="row bt">
                            <div class="col-md-6">
                                <?php echo $_language->module[ 'spam_check' ]; ?>:
                            </div>

                            <div class="col-md-6">
                                <span class="pull-right text-muted small"><em data-toggle="tooltip" title="<?php echo $_language->module[ 'tooltip_53' ]; ?>"><?php echo $spam_check; ?></em></span>
                            </div>
                        </div>

                        <div class="row bt">
                            <div class="col-md-6">
                                <?php echo $_language->module[ 'spamapiblockerror' ]; ?>:
                            </div>

                            <div class="col-md-6">
                                <span class="pull-right text-muted small"><em data-toggle="tooltip" title="<?php echo $_language->module[ 'tooltip_57' ]; ?>"><?php echo $spamapiblockerror; ?></em></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <?php echo $_language->module['other']; ?>
                    </div>

                    <div class="panel-body">
                        <div class="col-md-6">
                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <div class="row bt">
                                        <div class="col-md-6">
                                            <?php echo $_language->module['awards']; ?>:
                                        </div>

                                        <div class="col-md-6">
                                            <span class="pull-right text-muted small"><em data-toggle="tooltip" title="<?php echo $_language->module[ 'tooltip_21' ]; ?>"><input class="form-control" name="awards" type="text" value="<?php echo $ds['awards']; ?>" size="3"></em></span>
                                        </div>
                                    </div>

                                    <div class="row bt">
                                        <div class="col-md-6">
                                            <?php echo $_language->module['demos']; ?>:
                                        </div>

                                        <div class="col-md-6">
                                            <span class="pull-right text-muted small"><em data-toggle="tooltip" title="<?php echo $_language->module[ 'tooltip_23' ]; ?>"><input class="form-control" name="demos" type="text" value="<?php echo $ds['demos']; ?>" size="3"></em></span>
                                        </div>
                                    </div>

                                    <div class="row bt">
                                        <div class="col-md-6">
                                            <?php echo $_language->module['guestbook']; ?>:
                                        </div>

                                        <div class="col-md-6">
                                            <span class="pull-right text-muted small"><em data-toggle="tooltip" title="<?php echo $_language->module[ 'tooltip_25' ]; ?>"><input class="form-control" type="text" name="guestbook" value="<?php echo $ds['guestbook']; ?>" size="3"></em></span>
                                        </div>
                                    </div>

                                    <div class="row bt">
                                        <div class="col-md-6">
                                            <?php echo $_language->module[ 'user_guestbook' ]; ?>:
                                        </div>

                                        <div class="col-md-6">
                                            <span class="pull-right text-muted small"><em data-toggle="tooltip" title="<?php echo $_language->module[ 'tooltip_60' ]; ?>"><select class="form-control" name="user_guestbook">
                                                <?php echo $user_gbook; ?>
                                            </select></em></span>
                                        </div>
                                    </div>

                                    <div class="row bt">
                                        <div class="col-md-6">
                                            <?php echo $_language->module['comments']; ?>:
                                        </div>

                                        <div class="col-md-6">
                                            <span class="pull-right text-muted small"><em data-toggle="tooltip" title="<?php echo $_language->module[ 'tooltip_26' ]; ?>"><input class="form-control" type="text" name="feedback" value="<?php echo $ds['feedback']; ?>" size="3"></em></span>
                                        </div>
                                    </div>

                                    <div class="row bt">
                                        <div class="col-md-6">
                                            <?php echo $_language->module['messenger']; ?>:
                                        </div>

                                        <div class="col-md-6">
                                            <span class="pull-right text-muted small"><em data-toggle="tooltip" title="<?php echo $_language->module[ 'tooltip_27' ]; ?>"><input class="form-control" type="text" name="messages" value="<?php echo $ds['messages']; ?>" size="3"></em></span>
                                        </div>
                                    </div>

                                    <div class="row bt">
                                        <div class="col-md-6">
                                            <?php echo $_language->module['registered_users']; ?>:
                                        </div>

                                        <div class="col-md-6">
                                            <span class="pull-right text-muted small"><em data-toggle="tooltip" title="<?php echo $_language->module[ 'tooltip_29' ]; ?>"><input class="form-control" type="text" name="users" value="<?php echo $ds['users']; ?>" size="3"></em></span>
                                        </div>
                                    </div>

                                    <div class="row bt">
                                        <div class="col-md-6">
                                            <?php echo $_language->module['profile_last_posts']; ?>:
                                        </div>

                                        <div class="col-md-6">
                                            <span class="pull-right text-muted small"><em data-toggle="tooltip" title="<?php echo $_language->module[ 'tooltip_31' ]; ?>"><input class="form-control" name="lastposts" type="text" id="lastposts" value="<?php echo $ds['profilelast']; ?>" size="3"></em></span>
                                        </div>
                                    </div>

                                    <div class="row bt">
                                        <div class="col-md-6">
                                            <?php echo $_language->module[ 'format_date' ]; ?>:
                                        </div>

                                        <div class="col-md-6">
                                            <span class="pull-right text-muted small"><em data-toggle="tooltip" title="<?php echo $_language->module[ 'tooltip_58' ]; ?>"><select class="form-control" name="date_format" style="text-align: right;">
                                                <?php echo $format_date; ?>
                                            </select></em></span>
                                        </div>
                                    </div>

                                    <div class="row bt">
                                        <div class="col-md-6">
                                            <?php echo $_language->module[ 'format_time' ]; ?>:
                                        </div>

                                        <div class="col-md-6">
                                            <span class="pull-right text-muted small"><em data-toggle="tooltip" title="<?php echo $_language->module[ 'tooltip_59' ]; ?>"><select class="form-control" name="time_format" style="text-align: right;">
                                                <?php echo $format_time; ?>
                                            </select></em></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <div class="row bt">
                                        <div class="col-md-6">
                                            <?php echo $_language->module['default_language']; ?>:
                                        </div>

                                        <div class="col-md-6">
                                            <span class="pull-right text-muted small"><em data-toggle="tooltip" title="<?php echo $_language->module[ 'tooltip_40' ]; ?>"><select class="form-control" name="language">
                                                <?php echo $langdirs; ?>
                                            </select></em></span>
                                        </div>
                                    </div>

                                    <div class="row bt">
                                        <div class="col-md-6">
                                            <?php echo $_language->module['msg_on_gb_entry']; ?>:
                                        </div>

                                        <div class="col-md-6">
                                            <span class="pull-right text-muted small"><em data-toggle="tooltip" title="<?php echo $_language->module[ 'tooltip_36' ]; ?>"><?php echo $gb_info; ?></em></span>
                                        </div>
                                    </div>

                                    <div class="row bt">
                                        <div class="col-md-6">
                                            <?php echo $_language->module['insert_links']; ?>:
                                        </div>

                                        <div class="col-md-6">
                                            <span class="pull-right text-muted small"><em data-toggle="tooltip" title="<?php echo $_language->module[ 'tooltip_41' ]; ?>"><?php echo $insertlinks; ?></em></span>
                                        </div>
                                    </div>

                                    <div class="row bt">
                                        <div class="col-md-6">
                                            <?php echo $_language->module['login_duration']; ?>:
                                        </div>

                                        <div class="col-md-6">
                                            <span class="pull-right text-muted small"><em data-toggle="tooltip" title="<?php echo $_language->module[ 'tooltip_33' ]; ?>"><input class="form-control" type="text" name="sessionduration" value="<?php echo $ds['sessionduration']; ?>" size="3"></em></span>
                                        </div>
                                    </div>

                                    <div class="row bt">
                                        <div class="col-md-6">
                                            <?php echo $_language->module['search_min_length']; ?>:
                                        </div>

                                        <div class="col-md-6">
                                            <span class="pull-right text-muted small"><em data-toggle="tooltip" title="<?php echo $_language->module[ 'tooltip_17' ]; ?>"><input class="form-control" type="text" name="searchminlen" value="<?php echo $ds['search_min_len']; ?>" size="3"></em></span>
                                        </div>
                                    </div>

                                    <div class="row bt">
                                        <div class="col-md-6">
                                            <?php echo $_language->module['max_wrong_pw']; ?>:
                                        </div>

                                        <div class="col-md-6">
                                            <span class="pull-right text-muted small"><em data-toggle="tooltip" title="<?php echo $_language->module[ 'tooltip_43' ]; ?>"><input class="form-control" type="text" name="max_wrong_pw" value="<?php echo $ds['max_wrong_pw']; ?>" size="3"></em></span>
                                        </div>
                                    </div>

                                    <div class="row bt">
                                        <div class="col-md-4">
                                            <?php echo $_language->module['content_size']; ?>: B x H
                                        </div>
										<div class="col-md-8 pull-right">	
	                                        <div class="row">
	                                            <div class="col-md-9">
	                                                <span class="pull-right text-muted small"><em data-toggle="tooltip" title="<?php echo $_language->module[ 'tooltip_34' ]; ?>"><input class="form-control" type="text" name="picsize_l" value="<?php echo $ds['picsize_l']; ?>" size="3"></em></span>
	                                            </div>
	
	                                            <div class="col-md-3">
	                                                <span class="pull-right text-muted small"><em data-toggle="tooltip" title="<?php echo $_language->module[ 'tooltip_35' ]; ?>"><input class="form-control" type="text" name="picsize_h" value="<?php echo $ds['picsize_h']; ?>" size="3"></em></span>
	                                            </div>
	                                        </div>
										</div>
                                    </div>

                                    <div class="row bt">
                                        <div class="col-md-6">
                                            <?php echo $_language->module['autoresize']; ?>:
                                        </div>

                                        <div class="col-md-6">
                                            <span class="pull-right text-muted small">
                                            	<em data-toggle="tooltip" title="<?php echo $_language->module[ 'tooltip_50' ]; ?>">
                                            		<select class="form-control" name="autoresize">
														<?php echo $autoresize;?>
                                            		</select>
                                            	</em>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="row bt">
                                        <div class="col-md-6">
                                            <?php echo $_language->module[ 'register_per_ip' ]; ?>:
                                        </div>

                                        <div class="col-md-6">
                                            <span class="pull-right text-muted small">
                                            	<em data-toggle="tooltip" title="<?php echo $_language->module[ 'tooltip_63' ]; ?>">
                                            		<?php echo $register_per_ip; ?>                                            	
                                            	</em>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="row bt">
                                        <div class="col-md-6">
                                            <?php echo $_language->module[ 'detect_visitor_language' ]; ?>:
                                        </div>

                                        <div class="col-md-6">
                                            <span class="pull-right text-muted small">
                                            	<em data-toggle="tooltip" title="<?php echo $_language->module[ 'tooltip_52' ]; ?>">
                                            		<?php echo $visitor_language; ?>                                            	
                                            	</em>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div><br>
        <input type="hidden" name="captcha_hash" value="<?php echo $hash; ?>"> <button class="btn btn-primary btn-xs" type="submit" name="submit"><?php echo $_language->module['update']; ?>
    </form>
<?php
}
echo '<br></div></div>';
?>

