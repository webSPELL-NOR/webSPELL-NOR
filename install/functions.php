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

class Transaction
{
    private $database;
    private $success;
    private $errors = array();

    function __construct($database)
    {
        $this->database = $database;
        $this->success = true;
    }

    function addQuery($query)
    {
        if (!mysqli_query($this->database, $query)) {
            $this->success = false;
            $this->errors[] = mysqli_error($this->database);
        }
    }

    function successful()
    {
        if ($this->success) {
            $this->database->commit();
            return true;
        } else {
            //$this->error = mysqli_error($this->database);
            $this->database->rollback();
            return false;
        }
    }

    function getError()
    {
        return implode("<br/>", $this->errors);
    }
}

function update_progress($functions_to_call)
{
    return '<div id="todo_list" style="display:none;">' . json_encode($functions_to_call) . '</div><div class="progress">
  <div id="progress_bar" class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
    <span class="sr-only">0%</span>
  </div>
</div><div id="details_text" style="height: 150px; overflow-y:scroll;"></div>';
}

function update_clearfolder($_database)
{
    global $_language;
    include("../src/func/filesystem.php");
    $remove_install = @rm_recursive("./");
    if ($remove_install) {
        return array('status' => 'success', 'message' => $_language->module['folder_removed']);
    } else {
        return array('status' => 'success', 'message' => $_language->module['delete_folder']);
    }
}

/** fixme */
function updateMySQLConfig()
{
    global $_language;
    include('../_mysql.php');
    /** variables from _mysql.php
     * @var string $host
     * @var string $user
     * @var string $pwd
     * @var string $db
     */
    $new_content = '<?php
$host = ' . var_export($host, true) . ';
$user = ' . var_export($user, true) . ';
$pwd = ' . var_export($pwd, true) . ';
$db = ' . var_export($db, true) . ';
if (!defined("PREFIX")) {
    define("PREFIX", ' . var_export(PREFIX, true) . ');
}
';
    $ret = file_put_contents('../_mysql.php', $new_content);
    if ($ret === false) {
        echo $_language->module['write_failed'];
    }
}

function update_base_1($_database)
{
    $transaction = new Transaction($_database);
    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "about`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "about` (
  `about` longtext NOT NULL
  ) DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_unicode_ci");

    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "articles`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "articles` (
  `articlesID` int(11) NOT NULL AUTO_INCREMENT,
  `date` int(14) NOT NULL default '0',
  `title` varchar(255) NOT NULL default '',
  `content` text NOT NULL,
  `screens` text NOT NULL,
  `poster` int(11) NOT NULL default '0',
  `link1` varchar(255) NOT NULL default '',
  `url1` varchar(255) NOT NULL default '',
  `window1` int(1) NOT NULL default '0',
  `link2` varchar(255) NOT NULL default '',
  `url2` varchar(255) NOT NULL default '',
  `window2` int(1) NOT NULL default '0',
  `link3` varchar(255) NOT NULL default '',
  `url3` varchar(255) NOT NULL default '',
  `window3` int(1) NOT NULL default '0',
  `link4` varchar(255) NOT NULL default '',
  `url4` varchar(255) NOT NULL default '',
  `window4` int(1) NOT NULL default '0',
  `votes` int(11) NOT NULL default '0',
  `points` int(11) NOT NULL default '0',
  `rating` int(11) NOT NULL default '0',
  `saved` int(1) NOT NULL default '0',
  `viewed` int(11) NOT NULL default '0',
  `comments` int(1) NOT NULL default '0',
  PRIMARY KEY  (`articlesID`)
) AUTO_INCREMENT=1
  DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_unicode_ci");

    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "awards`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "awards` (
  `awardID` int(11) NOT NULL AUTO_INCREMENT,
  `date` int(14) NOT NULL default '0',
  `squadID` int(11) NOT NULL default '0',
  `award` varchar(255) NOT NULL default '',
  `homepage` varchar(255) NOT NULL default '',
  `rang` int(11) NOT NULL default '0',
  `info` text NOT NULL,
  PRIMARY KEY  (`awardID`)
) AUTO_INCREMENT=1
  DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_unicode_ci");
    if ($transaction->successful()) {
        return array('status' => 'success', 'message' => 'Created tables starting with "a"');
    } else {
        return array('status' => 'fail', 'message' => 'Failed to create tables starting with "a"<br/>' . $transaction->getError());
    }
}

function update_base_2($_database)
{
    $transaction = new Transaction($_database);
    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "banner`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "banner` (
  `bannerID` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL default '',
  `url` varchar(255) NOT NULL default '',
  `banner` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`bannerID`)
) AUTO_INCREMENT=1
  DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_unicode_ci");

    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "buddys`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "buddys` (
  `buddyID` int(11) NOT NULL AUTO_INCREMENT,
  `userID` int(11) NOT NULL default '0',
  `buddy` int(11) NOT NULL default '0',
  `banned` int(1) NOT NULL default '0',
  PRIMARY KEY  (`buddyID`)
) AUTO_INCREMENT=1
  DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_unicode_ci");
    if ($transaction->successful()) {
        return array('status' => 'success', 'message' => 'Created tables starting with "b"');
    } else {
        return array('status' => 'fail', 'message' => 'Failed to create tables starting with "b"<br/>' . $transaction->getError());
    }
}

function update_base_3($_database)
{
    $transaction = new Transaction($_database);

    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "cash_box`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "cash_box` (
  `cashID` int(11) NOT NULL AUTO_INCREMENT,
  `date` int(14) NOT NULL default '0',
  `paydate` int(14) NOT NULL default '0',
  `usedfor` text NOT NULL,
  `info` text NOT NULL,
  `totalcosts` double(8,2) NOT NULL default '0.00',
  `usercosts` double(8,2) NOT NULL default '0.00',
  `squad` int(11) NOT NULL default '0',
  `konto` text NOT NULL,
  PRIMARY KEY  (`cashID`)
) AUTO_INCREMENT=1
  DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_unicode_ci");

    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "cash_box_payed`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "cash_box_payed` (
  `payedID` int(11) NOT NULL AUTO_INCREMENT,
  `cashID` int(11) NOT NULL default '0',
  `userID` int(11) NOT NULL default '0',
  `costs` double(8,2) NOT NULL default '0.00',
  `date` int(14) NOT NULL default '0',
  `payed` int(1) NOT NULL default '0',
  PRIMARY KEY  (`payedID`)
) AUTO_INCREMENT=1
  DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_unicode_ci");

    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "challenge`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "challenge` (
  `chID` int(11) NOT NULL AUTO_INCREMENT,
  `date` int(14) NOT NULL default '0',
  `cwdate` int(14) NOT NULL default '0',
  `squadID` varchar(255) NOT NULL default '',
  `opponent` varchar(255) NOT NULL default '',
  `opphp` varchar(255) NOT NULL default '',
  `oppcountry` char(2) NOT NULL default '',
  `league` varchar(255) NOT NULL default '',
  `map` varchar(255) NOT NULL default '',
  `server` varchar(255) NOT NULL default '',
  `email` varchar(255) NOT NULL default '',
  `info` text NOT NULL,
  PRIMARY KEY  (`chID`)
) AUTO_INCREMENT=1
  DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_unicode_ci");

    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "clanwars`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "clanwars` (
  `cwID` int(11) NOT NULL AUTO_INCREMENT,
  `date` int(14) NOT NULL default '0',
  `squad` int(11) NOT NULL default '0',
  `game` varchar(10) NOT NULL default '',
  `league` varchar(255) NOT NULL default '',
  `leaguehp` varchar(255) NOT NULL default '',
  `opponent` varchar(255) NOT NULL default '',
  `opptag` varchar(255) NOT NULL default '',
  `oppcountry` char(2) NOT NULL default '',
  `opphp` varchar(255) NOT NULL default '',
  `maps` varchar(255) NOT NULL default '',
  `hometeam` varchar(255) NOT NULL default '',
  `oppteam` varchar(255) NOT NULL default '',
  `server` varchar(255) NOT NULL default '',
  `homescr1` int(11) NOT NULL default '0',
  `oppscr1` int(11) NOT NULL default '0',
  `homescr2` int(11) NOT NULL default '0',
  `oppscr2` int(11) NOT NULL default '0',
  `screens` text NOT NULL,
  `report` text NOT NULL,
  `comments` int(1) NOT NULL default '0',
  `linkpage` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`cwID`)
) AUTO_INCREMENT=1
  DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_unicode_ci");

    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "comments`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "comments` (
  `commentID` int(11) NOT NULL AUTO_INCREMENT,
  `parentID` int(11) NOT NULL default '0',
  `type` char(2) NOT NULL default '',
  `userID` int(11) NOT NULL default '0',
  `nickname` varchar(255) NOT NULL default '',
  `date` int(14) NOT NULL default '0',
  `comment` text NOT NULL,
  `url` varchar(255) NOT NULL default '',
  `email` varchar(255) NOT NULL default '',
  `ip` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`commentID`)
) AUTO_INCREMENT=1
  DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_unicode_ci");

    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "counter`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "counter` (
  `hits` int(20) NOT NULL default '0',
  `online` int(14) NOT NULL default '0'
) DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_unicode_ci");

    $transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "counter` (`hits`, `online`) VALUES (1, '" . time() . "')");

    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "counter_iplist`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "counter_iplist` (
  `dates` varchar(255) NOT NULL default '',
  `del` int(20) NOT NULL default '0',
  `ip` varchar(255) NOT NULL default ''
) DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_unicode_ci");

    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "counter_stats`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "counter_stats` (
  `dates` varchar(255) NOT NULL default '',
  `count` int(20) NOT NULL default '0'
) DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_unicode_ci");

    if ($transaction->successful()) {
        return array('status' => 'success', 'message' => 'Created tables starting with "c"');
    } else {
        return array('status' => 'fail', 'message' => 'Failed to create tables starting with "c"<br/>' . $transaction->getError());
    }
}

function update_base_4($_database)
{
    $transaction = new Transaction($_database);
    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "demos`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "demos` (
  `demoID` int(11) NOT NULL AUTO_INCREMENT,
  `date` int(14) NOT NULL default '0',
  `game` varchar(255) NOT NULL default '',
  `clan1` varchar(255) NOT NULL default '',
  `clan2` varchar(255) NOT NULL default '',
  `clantag1` varchar(255) NOT NULL default '',
  `clantag2` varchar(255) NOT NULL default '',
  `url1` varchar(255) NOT NULL default '',
  `url2` varchar(255) NOT NULL default '',
  `country1` char(2) NOT NULL default '',
  `country2` char(2) NOT NULL default '',
  `league` varchar(255) NOT NULL default '',
  `leaguehp` varchar(255) NOT NULL default '',
  `maps` varchar(255) NOT NULL default '',
  `player` varchar(255) NOT NULL default '',
  `file` varchar(255) NOT NULL default '',
  `downloads` int(11) NOT NULL default '0',
  `votes` int(11) NOT NULL default '0',
  `points` int(11) NOT NULL default '0',
  `rating` int(11) NOT NULL default '0',
  `comments` int(1) NOT NULL default '0',
  `accesslevel` int(1) NOT NULL default '0',
  PRIMARY KEY  (`demoID`)
) AUTO_INCREMENT=1
  DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_unicode_ci");
    if ($transaction->successful()) {
        return array('status' => 'success', 'message' => 'Created tables starting with "d"');
    } else {
        return array('status' => 'fail', 'message' => 'Failed to create tables starting with "d"<br/>' . $transaction->getError());
    }
}

function update_base_5($_database)
{
    $transaction = new Transaction($_database);
    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "files`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "files` (
  `fileID` int(11) NOT NULL AUTO_INCREMENT,
  `filecatID` int(11) NOT NULL default '0',
  `date` int(14) NOT NULL default '0',
  `filename` varchar(255) NOT NULL default '',
  `filesize` varchar(255) NOT NULL default '',
  `info` varchar(255) NOT NULL default '',
  `file` varchar(255) NOT NULL default '',
  `downloads` int(11) NOT NULL default '0',
  `accesslevel` int(1) NOT NULL default '0',
  PRIMARY KEY  (`fileID`)
) AUTO_INCREMENT=1
  DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_unicode_ci");

    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "files_categorys`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "files_categorys` (
  `filecatID` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`filecatID`)
) AUTO_INCREMENT=1
  DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_unicode_ci");

    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "forum_announcements`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "forum_announcements` (
  `announceID` int(11) NOT NULL AUTO_INCREMENT,
  `boardID` int(11) NOT NULL default '0',
  `intern` int(1) NOT NULL default '0',
  `userID` int(11) NOT NULL default '0',
  `date` int(14) NOT NULL default '0',
  `topic` varchar(255) NOT NULL default '',
  `announcement` text NOT NULL,
  PRIMARY KEY  (`announceID`)
) AUTO_INCREMENT=1
  DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_unicode_ci");

    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "forum_boards`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "forum_boards` (
  `boardID` int(11) NOT NULL AUTO_INCREMENT,
  `category` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `info` varchar(255) NOT NULL default '',
  `intern` int(1) NOT NULL default '0',
  `sort` int(2) NOT NULL default '0',
  PRIMARY KEY  (`boardID`)
) AUTO_INCREMENT=3
  DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_unicode_ci");

    $transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "forum_boards` (`boardID`, `category`, `name`, `info`, `intern`, `sort`) VALUES (1, 1, 'Main Board', 'The general public board', 0, 1)");
    $transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "forum_boards` (`boardID`, `category`, `name`, `info`, `intern`, `sort`) VALUES (2, 2, 'Main Board', 'The general intern board', 1, 1)");

    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "forum_categories`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "forum_categories` (
  `catID` int(11) NOT NULL AUTO_INCREMENT,
  `intern` int(1) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `info` varchar(255) NOT NULL default '',
  `sort` int(11) NOT NULL default '0',
  PRIMARY KEY  (`catID`)
) AUTO_INCREMENT=3
  DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_unicode_ci");

    $transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "forum_categories` (`catID`, `intern`, `name`, `info`, `sort`) VALUES (1, 0, 'Public Boards', '', 2)");
    $transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "forum_categories` (`catID`, `intern`, `name`, `info`, `sort`) VALUES (2, 1, 'Intern Boards', '', 3)");

    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "forum_moderators`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "forum_moderators` (
  `modID` int(11) NOT NULL AUTO_INCREMENT,
  `boardID` int(11) NOT NULL default '0',
  `userID` int(11) NOT NULL default '0',
  PRIMARY KEY  (`modID`)
) AUTO_INCREMENT=1
  DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_unicode_ci");

    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "forum_notify`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "forum_notify` (
  `notifyID` int(11) NOT NULL AUTO_INCREMENT,
  `topicID` int(11) NOT NULL default '0',
  `userID` int(11) NOT NULL default '0',
  PRIMARY KEY  (`notifyID`)
) AUTO_INCREMENT=1
  DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_unicode_ci");

    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "forum_posts`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "forum_posts` (
  `postID` int(11) NOT NULL AUTO_INCREMENT,
  `boardID` int(11) NOT NULL default '0',
  `topicID` int(11) NOT NULL default '0',
  `date` int(14) NOT NULL default '0',
  `poster` int(11) NOT NULL default '0',
  `message` text NOT NULL,
  PRIMARY KEY  (`postID`)
) AUTO_INCREMENT=1
  DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_unicode_ci");

    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "forum_ranks`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "forum_ranks` (
  `rankID` int(11) NOT NULL AUTO_INCREMENT,
  `rank` varchar(255) NOT NULL default '',
  `pic` varchar(255) NOT NULL default '',
  `postmin` int(11) NOT NULL default '0',
  `postmax` int(11) NOT NULL default '0',
  `special` int(1) NULL DEFAULT '0',
  PRIMARY KEY  (`rankID`)
) AUTO_INCREMENT=9
  DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_unicode_ci");

    $transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "forum_ranks` (`rankID`, `rank`, `pic`, `postmin`, `postmax`) VALUES (1, 'Rank 1', 'rank1.gif', 0, 9)");
    $transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "forum_ranks` (`rankID`, `rank`, `pic`, `postmin`, `postmax`) VALUES (2, 'Rank 2', 'rank2.gif', 10, 24)");
    $transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "forum_ranks` (`rankID`, `rank`, `pic`, `postmin`, `postmax`) VALUES (3, 'Rank 3', 'rank3.gif', 25, 49)");
    $transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "forum_ranks` (`rankID`, `rank`, `pic`, `postmin`, `postmax`) VALUES (4, 'Rank 4', 'rank4.gif', 50, 199)");
    $transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "forum_ranks` (`rankID`, `rank`, `pic`, `postmin`, `postmax`) VALUES (5, 'Rank 5', 'rank5.gif', 200, 399)");
    $transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "forum_ranks` (`rankID`, `rank`, `pic`, `postmin`, `postmax`) VALUES (6, 'Rank 6', 'rank6.gif', 400, 2147483647)");
    $transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "forum_ranks` (`rankID`, `rank`, `pic`, `postmin`, `postmax`) VALUES (7, 'Administrator', 'admin.gif', 0, 0)");
    $transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "forum_ranks` (`rankID`, `rank`, `pic`, `postmin`, `postmax`) VALUES (8, 'Moderator', 'moderator.gif', 0, 0)");

    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "forum_topics`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "forum_topics` (
  `topicID` int(11) NOT NULL AUTO_INCREMENT,
  `boardID` int(11) NOT NULL default '0',
  `icon` varchar(255) NOT NULL default '',
  `intern` int(1) NOT NULL default '0',
  `userID` int(11) NOT NULL default '0',
  `date` int(14) NOT NULL default '0',
  `topic` varchar(255) NOT NULL default '',
  `lastdate` int(14) NOT NULL default '0',
  `lastposter` int(11) NOT NULL default '0',
  `replys` int(11) NOT NULL default '0',
  `views` int(11) NOT NULL default '0',
  `closed` int(1) NOT NULL default '0',
  `moveID` int(11) NOT NULL default '0',
  PRIMARY KEY  (`topicID`)
) AUTO_INCREMENT=1
  DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_unicode_ci");

    if ($transaction->successful()) {
        return array('status' => 'success', 'message' => 'Created tables starting with "f"');
    } else {
        return array('status' => 'fail', 'message' => 'Failed to create tables starting with "f"<br/>' . $transaction->getError());
    }
}

function update_base_6($_database)
{
    $transaction = new Transaction($_database);
    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "games`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "games` (
  `gameID` int(3) NOT NULL AUTO_INCREMENT,
  `tag` varchar(10) NOT NULL default '',
  `name` varchar(20) NOT NULL default '',
  PRIMARY KEY  (`gameID`)
) PACK_KEYS=0 AUTO_INCREMENT=8
  DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_unicode_ci");

    $transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "games` (`gameID`, `tag`, `name`) VALUES (1, 'cs', 'Counter-Strike')");
    $transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "games` (`gameID`, `tag`, `name`) VALUES (2, 'ut', 'Unreal Tournament')");
    $transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "games` (`gameID`, `tag`, `name`) VALUES (3, 'to', 'Tactical Ops')");
    $transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "games` (`gameID`, `tag`, `name`) VALUES (4, 'hl2', 'Halflife 2')");
    $transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "games` (`gameID`, `tag`, `name`) VALUES (5, 'wc3', 'Warcraft 3')");
    $transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "games` (`gameID`, `tag`, `name`) VALUES (6, 'hl', 'Halflife')");
    $transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "games` (`gameID`, `tag`, `name`) VALUES (7, 'bf', 'Battlefield')");

    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "guestbook`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "guestbook` (
  `gbID` int(11) NOT NULL AUTO_INCREMENT,
  `date` int(14) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `email` varchar(255) NOT NULL default '',
  `hp` varchar(255) NOT NULL default '',
  `icq` varchar(255) NOT NULL default '',
  `ip` varchar(255) NOT NULL default '',
  `comment` text NOT NULL,
  PRIMARY KEY  (`gbID`)
) AUTO_INCREMENT=1
  DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_unicode_ci");
    if ($transaction->successful()) {
        return array('status' => 'success', 'message' => 'Created tables starting with "g"');
    } else {
        return array('status' => 'fail', 'message' => 'Failed to create tables starting with "g"<br/>' . $transaction->getError());
    }
}

function update_base_7($_database)
{
    $transaction = new Transaction($_database);
    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "history`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "history` (
  `history` text NOT NULL
) DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_unicode_ci");
    if ($transaction->successful()) {
        return array('status' => 'success', 'message' => 'Created tables starting with "h"');
    } else {
        return array('status' => 'fail', 'message' => 'Failed to create tables starting with "h"<br/>' . $transaction->getError());
    }
}

function update_base_8($_database)
{
    $transaction = new Transaction($_database);
    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "links`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "links` (
  `linkID` int(11) NOT NULL AUTO_INCREMENT,
  `linkcatID` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `url` varchar(255) NOT NULL default '',
  `info` varchar(255) NOT NULL default '',
  `banner` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`linkID`)
) AUTO_INCREMENT=2
  DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_unicode_ci");

    $transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "links` (`linkID`, `linkcatID`, `name`, `url`, `info`, `banner`) VALUES (1, 1, 'webSPELL.org', 'http://www.webspell.org', 'webspell.org: Webdesign und Webdevelopment', '1.gif')");

    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "links_categorys`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "links_categorys` (
  `linkcatID` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`linkcatID`)
) AUTO_INCREMENT=2
  DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_unicode_ci");

    $transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "links_categorys` (`linkcatID`, `name`) VALUES (1, 'Webdesign')");

    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "linkus`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "linkus` (
  `bannerID` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL default '',
  `file` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`bannerID`)
) AUTO_INCREMENT=1
  DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_unicode_ci");
    if ($transaction->successful()) {
        return array('status' => 'success', 'message' => 'Created tables starting with "l"');
    } else {
        return array('status' => 'fail', 'message' => 'Failed to create tables starting with "l"<br/>' . $transaction->getError());
    }
}

function update_base_9($_database)
{
    $transaction = new Transaction($_database);
    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "messenger`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "messenger` (
  `messageID` int(11) NOT NULL AUTO_INCREMENT,
  `userID` int(11) NOT NULL default '0',
  `date` int(14) NOT NULL default '0',
  `fromuser` int(11) NOT NULL default '0',
  `touser` int(11) NOT NULL default '0',
  `title` varchar(255) NOT NULL default '',
  `message` text NOT NULL,
  `viewed` int(11) NOT NULL default '0',
  PRIMARY KEY  (`messageID`)
) AUTO_INCREMENT=1
  DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_unicode_ci");
    if ($transaction->successful()) {
        return array('status' => 'success', 'message' => 'Created tables starting with "m"');
    } else {
        return array('status' => 'fail', 'message' => 'Failed to create tables starting with "m"<br/>' . $transaction->getError());
    }
}

function update_base_10($_database)
{
    $transaction = new Transaction($_database);
    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "news`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "news` (
  `newsID` int(11) NOT NULL AUTO_INCREMENT,
  `date` int(14) NOT NULL default '0',
  `rubric` int(11) NOT NULL default '0',
  `lang1` char(2) NOT NULL default '',
  `headline1` varchar(255) NOT NULL default '',
  `content1` text NOT NULL,
  `lang2` char(2) NOT NULL default '',
  `headline2` varchar(255) NOT NULL default '',
  `content2` text NOT NULL,
  `screens` text NOT NULL,
  `poster` int(11) NOT NULL default '0',
  `link1` varchar(255) NOT NULL default '',
  `url1` varchar(255) NOT NULL default '',
  `window1` int(11) NOT NULL default '0',
  `link2` varchar(255) NOT NULL default '',
  `url2` varchar(255) NOT NULL default '',
  `window2` int(11) NOT NULL default '0',
  `link3` varchar(255) NOT NULL default '',
  `url3` varchar(255) NOT NULL default '',
  `window3` int(11) NOT NULL default '0',
  `link4` varchar(255) NOT NULL default '',
  `url4` varchar(255) NOT NULL default '',
  `window4` int(11) NOT NULL default '0',
  `saved` int(1) NOT NULL default '1',
  `published` int(11) NOT NULL default '0',
  `comments` int(1) NOT NULL default '0',
  `cwID` int(11) NOT NULL default '0',
  PRIMARY KEY  (`newsID`)
) AUTO_INCREMENT=1
  DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_unicode_ci");

    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "news_languages`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "news_languages` (
  `langID` int(11) NOT NULL AUTO_INCREMENT,
  `language` varchar(255) NOT NULL default '',
  `lang` char(2) NOT NULL default '',
  `alt` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`langID`)
) AUTO_INCREMENT=12
  DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_unicode_ci");

    $transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "news_languages` (`langID`, `language`, `lang`, `alt`) VALUES (1, 'english', 'uk', 'english')");
    $transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "news_languages` (`langID`, `language`, `lang`, `alt`) VALUES (2, 'french', 'fr', 'french')");
    $transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "news_languages` (`langID`, `language`, `lang`, `alt`) VALUES (3, 'german', 'de', 'german')");
  

    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "news_rubrics`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "news_rubrics` (
  `rubricID` int(11) NOT NULL AUTO_INCREMENT,
  `rubric` varchar(255) NOT NULL default '',
  `pic` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`rubricID`)
) AUTO_INCREMENT=1
  DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_unicode_ci");

    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "newsletter`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "newsletter` (
  `email` varchar(255) NOT NULL default '',
  `pass` varchar(255) NOT NULL default ''
) DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_unicode_ci");
    if ($transaction->successful()) {
        return array('status' => 'success', 'message' => 'Created tables starting with "n"');
    } else {
        return array('status' => 'fail', 'message' => 'Failed to create tables starting with "n"<br/>' . $transaction->getError());
    }
}

function update_base_11($_database)
{
    $transaction = new Transaction($_database);
    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "partners`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "partners` (
  `partnerID` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL default '',
  `url` varchar(255) NOT NULL default '',
  `banner` varchar(255) NOT NULL default '',
  `sort` int(11) NOT NULL default '0',
  PRIMARY KEY  (`partnerID`)
) PACK_KEYS=0 AUTO_INCREMENT=2
  DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_unicode_ci");

    
    $transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "partners` (`partnerID`, `name`, `url`, `banner`, `sort`) VALUES (1, 'WebSPELL | NOR', 'http://www.webspell-nor.de', '1.png', 1)");
    $transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "partners` (`partnerID`, `name`, `url`, `banner`, `sort`) VALUES (2, '2One Designs', 'http://www.2one-designs.de', '4.png', 2)");
    $transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "partners` (`partnerID`, `name`, `url`, `banner`, `sort`) VALUES (3, 'Design Performance', 'http://www.designperformance.de', '5.jpg', 3)");

    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "poll`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "poll` (
  `pollID` int(10) NOT NULL AUTO_INCREMENT,
  `aktiv` int(1) NOT NULL default '0',
  `laufzeit` bigint(20) NOT NULL default '0',
  `titel` varchar(255) NOT NULL default '',
  `o1` varchar(255) NOT NULL default '',
  `o2` varchar(255) NOT NULL default '',
  `o3` varchar(255) NOT NULL default '',
  `o4` varchar(255) NOT NULL default '',
  `o5` varchar(255) NOT NULL default '',
  `o6` varchar(255) NOT NULL default '',
  `o7` varchar(255) NOT NULL default '',
  `o8` varchar(255) NOT NULL default '',
  `o9` varchar(255) NOT NULL default '',
  `o10` varchar(255) NOT NULL default '',
  `comments` int(1) NOT NULL default '0',
  PRIMARY KEY  (`pollID`)
) AUTO_INCREMENT=1
  DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_unicode_ci");

    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "poll_votes`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "poll_votes` (
  `pollID` int(10) NOT NULL default '0',
  `o1` int(11) NOT NULL default '0',
  `o2` int(11) NOT NULL default '0',
  `o3` int(11) NOT NULL default '0',
  `o4` int(11) NOT NULL default '0',
  `o5` int(11) NOT NULL default '0',
  `o6` int(11) NOT NULL default '0',
  `o7` int(11) NOT NULL default '0',
  `o8` int(11) NOT NULL default '0',
  `o9` int(11) NOT NULL default '0',
  `o10` int(11) NOT NULL default '0',
  PRIMARY KEY  (`pollID`)
) DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_unicode_ci");
    if ($transaction->successful()) {
        return array('status' => 'success', 'message' => 'Created tables starting with "p"');
    } else {
        return array('status' => 'fail', 'message' => 'Failed to create tables starting with "p"<br/>' . $transaction->getError());
    }
}

function update_base_12($_database)
{
    global $url;
    global $adminmail;
    $transaction = new Transaction($_database);
    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "servers`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "servers` (
  `serverID` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL default '',
  `ip` varchar(255) NOT NULL default '',
  `game` char(10) NOT NULL default '',
  `info` text NOT NULL,
  PRIMARY KEY  (`serverID`)
) AUTO_INCREMENT=1
  DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_unicode_ci");

    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "settings`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "settings` (
  `settingID` int(11) NOT NULL AUTO_INCREMENT,
  `hpurl` varchar(255) NOT NULL default '',
  `clanname` varchar(255) NOT NULL default '',
  `clantag` varchar(255) NOT NULL default '',
  `adminname` varchar(255) NOT NULL default '',
  `adminemail` varchar(255) NOT NULL default '',
  `news` int(11) NOT NULL default '0',
  `newsarchiv` int(11) NOT NULL default '0',
  `headlines` int(11) NOT NULL default '0',
  `headlineschars` int(11) NOT NULL default '0',
  `articles` int(11) NOT NULL default '0',
  `latestarticles` int(11) NOT NULL default '0',
  `articleschars` int(11) NOT NULL default '0',
  `clanwars` int(11) NOT NULL default '0',
  `results` int(11) NOT NULL default '0',
  `upcoming` int(11) NOT NULL default '0',
  `shoutbox` int(11) NOT NULL default '0',
  `sball` int(11) NOT NULL default '0',
  `sbrefresh` int(11) NOT NULL default '0',
  `topics` int(11) NOT NULL default '0',
  `posts` int(11) NOT NULL default '0',
  `latesttopics` int(11) NOT NULL default '0',
  `hideboards` int(1) NOT NULL default '0',
  `awards` int(11) NOT NULL default '0',
  `demos` int(11) NOT NULL default '0',
  `guestbook` int(11) NOT NULL default '0',
  `feedback` int(11) NOT NULL default '0',
  `messages` int(11) NOT NULL default '0',
  `users` int(11) NOT NULL default '0',
  `profilelast` int(11) NOT NULL default '0',
  `topnewsID` int(11) NOT NULL default '0',
  `register_per_ip` int(1) NOT NULL default '1',
  PRIMARY KEY  (`settingID`)
) AUTO_INCREMENT=2
  DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_unicode_ci");

    $transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "settings` (`settingID`, `hpurl`, `clanname`, `clantag`, `adminname`, `adminemail`, `news`, `newsarchiv`, `headlines`, `headlineschars`, `articles`, `latestarticles`, `articleschars`, `clanwars`, `results`, `upcoming`, `shoutbox`, `sball`, `sbrefresh`, `topics`, `posts`, `latesttopics`, `hideboards`, `awards`, `demos`, `guestbook`, `feedback`, `messages`, `users`, `profilelast`, `topnewsID`, `register_per_ip`) VALUES
     (1, '" . $url . "', 'Clanname', 'MyClan', 'Admin-Name', '" . $adminmail . "', 10, 20, 10, 22, 20, 5, 20, 20, 5, 5, 5, 30, 60, 20, 10, 10, 1, 20, 20, 20, 20, 20, 60, 10, 27, 1)");

    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "shoutbox`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "shoutbox` (
  `shoutID` int(11) NOT NULL AUTO_INCREMENT,
  `date` int(14) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `message` varchar(255) NOT NULL default '',
  `ip` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`shoutID`)
) AUTO_INCREMENT=1
  DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_unicode_ci");

    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "sponsors`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "sponsors` (
  `sponsorID` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL default '',
  `url` varchar(255) NOT NULL default '',
  `info` text NOT NULL,
  `banner` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`sponsorID`)
) AUTO_INCREMENT=1
  DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_unicode_ci");

    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "squads`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "squads` (
  `squadID` int(11) NOT NULL AUTO_INCREMENT,
  `gamesquad` int(11) NOT NULL default '1',
  `name` varchar(255) NOT NULL default '',
  `icon` varchar(255) NOT NULL default '',
  `info` varchar(255) NOT NULL default '',
  `sort` int(11) NOT NULL default '0',
  PRIMARY KEY  (`squadID`)
) AUTO_INCREMENT=1
  DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_unicode_ci");

    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "squads_members`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "squads_members` (
  `sqmID` int(11) NOT NULL AUTO_INCREMENT,
  `squadID` int(11) NOT NULL default '0',
  `userID` int(11) NOT NULL default '0',
  `position` varchar(255) NOT NULL default '',
  `activity` int(1) NOT NULL default '0',
  `sort` int(11) NOT NULL default '0',
  `joinmember` int(1) NOT NULL default '0',
  `warmember` int(1) NOT NULL default '0',
  PRIMARY KEY  (`sqmID`)
) AUTO_INCREMENT=1
  DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_unicode_ci");
  
  $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "styles`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "styles` (
  `styleID` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL default '',
  `win` varchar(255) NOT NULL default '',
  `loose` varchar(255) NOT NULL default '',
  `draw` varchar(255) NOT NULL default '',
  `nav1` varchar(255) NOT NULL default '',
  `nav2` varchar(255) NOT NULL default '',
  `nav3` varchar(255) NOT NULL default '',
  `nav4` varchar(255) NOT NULL default '',
  `nav5` varchar(255) NOT NULL default '',
  `nav6` varchar(255) NOT NULL default '',
  `body1` varchar(255) NOT NULL default '',
  `body2` varchar(255) NOT NULL default '',
  `body3` varchar(255) NOT NULL default '',
  `body4` varchar(255) NOT NULL default '',
  `typo1` varchar(255) NOT NULL default '',
  `typo2` varchar(255) NOT NULL default '',
  `typo3` varchar(255) NOT NULL default '',
  `typo4` varchar(255) NOT NULL default '',
  `typo5` varchar(255) NOT NULL default '',
  `typo6` varchar(255) NOT NULL default '',
  `typo7` varchar(255) NOT NULL default '',
  `typo8` varchar(255) NOT NULL default '',
  `foot1` varchar(255) NOT NULL default '',
  `foot2` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`styleID`)
) AUTO_INCREMENT=2
  DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_unicode_ci");

    $transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "styles` (`styleID`, `title`, `win`, `loose`, `draw`, `nav1`, `nav2`, `nav3`, `nav4`, `nav5`, `nav6`, `body1`, `body2`, `body3`, `body4`, `typo1`, `typo2`, `typo3`, `typo4`, `typo5`, `typo6`, `typo7`, `typo8`, `foot1`, `foot2`) VALUES (1, 'WebSPELL NOR', '#00cc00', '#dd0000', '#ff6600', '#ffffff', '16px', '#000000', '#5bc0de', '#5bc0de', '3px', 'Helvetica Neue, Helvetica, Arial, sans-serif', '13px', '#ffffff', '#000000', '#6a6565', '#5bc0de', '#999999', '#5bc0de', '13px', '#5bc0de', '1px', '#000000', '#726868', '#ffffff')");

  $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "buttons`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "buttons` (
  `buttonID` int(11) NOT NULL AUTO_INCREMENT,
  `button1` varchar(255) NOT NULL default '',
  `button2` varchar(255) NOT NULL default '',
  `button3` varchar(255) NOT NULL default '',
  `button4` varchar(255) NOT NULL default '',
  `button5` varchar(255) NOT NULL default '',
  `button6` varchar(255) NOT NULL default '',
  `button7` varchar(255) NOT NULL default '',
  `button8` varchar(255) NOT NULL default '',
  `button9` varchar(255) NOT NULL default '',
  `button10` varchar(255) NOT NULL default '',
  `button11` varchar(255) NOT NULL default '',
  `button12` varchar(255) NOT NULL default '',
  `button13` varchar(255) NOT NULL default '',
  `button14` varchar(255) NOT NULL default '',
  `button15` varchar(255) NOT NULL default '',
  `button16` varchar(255) NOT NULL default '',
  `button17` varchar(255) NOT NULL default '',
  `button18` varchar(255) NOT NULL default '',
  `button19` varchar(255) NOT NULL default '',
  `button20` varchar(255) NOT NULL default '',
  `button21` varchar(255) NOT NULL default '',
  `button22` varchar(255) NOT NULL default '',
  `button23` varchar(255) NOT NULL default '',
  `button24` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`buttonID`)
) AUTO_INCREMENT=2
  DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_unicode_ci");

    $transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "buttons` (`buttonID`, `button1`, `button2`, `button3`, `button4`, `button5`, `button6`, `button7`, `button8`, `button9`, `button10`, `button11`, `button12`, `button13`, `button14`, `button15`, `button16`, `button17`, `button18`, `button19`, `button20`, `button21`, `button22`, `button23`, `button24`) VALUES (1, '#ffffff', '#e6e6e6', '#333333', '#0088cc', '#0044cc', '#ffffff', '#5cb85c', '#449d44', '#ffffff', '#5bc0de', '#2f96b4', '#ffffff', '#ef7814', '#f89406', '#ffffff', '#da0c0c', '#950d0d', '#ffffff', '#adadad', '#2e6da4', '#398439', '#269abc', '#d58512', '#ac2925')");

    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "moduls`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "moduls` (
  `modulID` int(11) NOT NULL AUTO_INCREMENT,
  `module` varchar(255) NOT NULL default '',
  `le_activated` int(11) NOT NULL default '0',
  `re_activated` int(11) NOT NULL default '0',
  `activated` int(11) NOT NULL default '0',
  `deactivated` int(11) NOT NULL DEFAULT '0',
  `sort` int(11) NOT NULL default '0',
  PRIMARY KEY  (`modulID`)
) AUTO_INCREMENT=44
   DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_unicode_ci");

	$transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "moduls` (`modulID`, `module`, `le_activated`, `re_activated`, `activated`, `deactivated`, `sort`) VALUES (1, 'forum', 0, 0, 0, 0, 14)");
  $transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "moduls` (`modulID`, `module`, `le_activated`, `re_activated`, `activated`, `deactivated`, `sort`) VALUES (2, 'news', 0, 0, 0, 0, 28)");
  $transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "moduls` (`modulID`, `module`, `le_activated`, `re_activated`, `activated`, `deactivated`, `sort`) VALUES (3, 'faq', 0, 0, 0, 0, 12)");
  $transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "moduls` (`modulID`, `module`, `le_activated`, `re_activated`, `activated`, `deactivated`, `sort`) VALUES (4, 'squads', 0, 0, 0, 0, 40)");
  $transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "moduls` (`modulID`, `module`, `le_activated`, `re_activated`, `activated`, `deactivated`, `sort`) VALUES (5, 'about', 0, 0, 0, 0, 1)");
  $transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "moduls` (`modulID`, `module`, `le_activated`, `re_activated`, `activated`, `deactivated`, `sort`) VALUES (6, 'articles', 0, 0, 0, 0, 2)");
  $transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "moduls` (`modulID`, `module`, `le_activated`, `re_activated`, `activated`, `deactivated`, `sort`) VALUES (7, 'forum_topic', 0, 0, 0, 0, 15)");
  $transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "moduls` (`modulID`, `module`, `le_activated`, `re_activated`, `activated`, `deactivated`, `sort`) VALUES (8, 'loginoverview', 0, 0, 0, 0, 23)");
  $transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "moduls` (`modulID`, `module`, `le_activated`, `re_activated`, `activated`, `deactivated`, `sort`) VALUES (9, 'cashbox', 0, 0, 0, 0, 6)");
  $transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "moduls` (`modulID`, `module`, `le_activated`, `re_activated`, `activated`, `deactivated`, `sort`) VALUES (10, 'buddies', 0, 0, 0, 0, 4)");
  $transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "moduls` (`modulID`, `module`, `le_activated`, `re_activated`, `activated`, `deactivated`, `sort`) VALUES (11, 'messenger', 0, 0, 0, 0, 26)");
  $transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "moduls` (`modulID`, `module`, `le_activated`, `re_activated`, `activated`, `deactivated`, `sort`) VALUES (12, 'myprofile', 0, 0, 0, 0, 27)");
  $transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "moduls` (`modulID`, `module`, `le_activated`, `re_activated`, `activated`, `deactivated`, `sort`) VALUES (13, 'profile', 0, 0, 0, 0, 34)");
  $transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "moduls` (`modulID`, `module`, `le_activated`, `re_activated`, `activated`, `deactivated`, `sort`) VALUES (14, 'usergallery', 0, 0, 0, 0, 41)");
  $transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "moduls` (`modulID`, `module`, `le_activated`, `re_activated`, `activated`, `deactivated`, `sort`) VALUES (15, 'awards', 0, 0, 0, 0, 3)");
  $transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "moduls` (`modulID`, `module`, `le_activated`, `re_activated`, `activated`, `deactivated`, `sort`) VALUES (16, 'calendar', 0, 0, 0, 0, 5)");
  $transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "moduls` (`modulID`, `module`, `le_activated`, `re_activated`, `activated`, `deactivated`, `sort`) VALUES (17, 'challenge', 0, 0, 0, 0, 7)");
  $transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "moduls` (`modulID`, `module`, `le_activated`, `re_activated`, `activated`, `deactivated`, `sort`) VALUES (18, 'clanwars', 0, 0, 0, 0, 8)");
  $transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "moduls` (`modulID`, `module`, `le_activated`, `re_activated`, `activated`, `deactivated`, `sort`) VALUES (19, 'contact', 0, 0, 0, 0, 9)");
  $transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "moduls` (`modulID`, `module`, `le_activated`, `re_activated`, `activated`, `deactivated`, `sort`) VALUES (20, 'counter_stats', 0, 0, 0, 0, 10)");
  $transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "moduls` (`modulID`, `module`, `le_activated`, `re_activated`, `activated`, `deactivated`, `sort`) VALUES (21, 'demos', 0, 0, 0, 0, 11)");
  $transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "moduls` (`modulID`, `module`, `le_activated`, `re_activated`, `activated`, `deactivated`, `sort`) VALUES (22, 'files', 0, 0, 0, 0, 13)");
  $transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "moduls` (`modulID`, `module`, `le_activated`, `re_activated`, `activated`, `deactivated`, `sort`) VALUES (23, 'gallery', 0, 0, 0, 0, 16)");
  $transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "moduls` (`modulID`, `module`, `le_activated`, `re_activated`, `activated`, `deactivated`, `sort`) VALUES (24, 'guestbook', 0, 0, 0, 0, 17)");
  $transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "moduls` (`modulID`, `module`, `le_activated`, `re_activated`, `activated`, `deactivated`, `sort`) VALUES (25, 'history', 0, 0, 0, 0, 18)");
  $transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "moduls` (`modulID`, `module`, `le_activated`, `re_activated`, `activated`, `deactivated`, `sort`) VALUES (26, 'imprint', 0, 0, 0, 0, 19)");
  $transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "moduls` (`modulID`, `module`, `le_activated`, `re_activated`, `activated`, `deactivated`, `sort`) VALUES (27, 'joinus', 0, 0, 0, 0, 20)");
  $transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "moduls` (`modulID`, `module`, `le_activated`, `re_activated`, `activated`, `deactivated`, `sort`) VALUES (28, 'links', 0, 0, 0, 0, 21)");
  $transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "moduls` (`modulID`, `module`, `le_activated`, `re_activated`, `activated`, `deactivated`, `sort`) VALUES (29, 'linkus', 0, 0, 0, 0, 22)");
  $transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "moduls` (`modulID`, `module`, `le_activated`, `re_activated`, `activated`, `deactivated`, `sort`) VALUES (30, 'lostpassword', 0, 0, 0, 0, 24)");
  $transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "moduls` (`modulID`, `module`, `le_activated`, `re_activated`, `activated`, `deactivated`, `sort`) VALUES (31, 'members', 0, 0, 0, 0, 25)");
  $transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "moduls` (`modulID`, `module`, `le_activated`, `re_activated`, `activated`, `deactivated`, `sort`) VALUES (32, 'newsletter', 0, 0, 0, 0, 30)");
  $transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "moduls` (`modulID`, `module`, `le_activated`, `re_activated`, `activated`, `deactivated`, `sort`) VALUES (34, 'partners', 0, 0, 0, 0, 31)");
  $transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "moduls` (`modulID`, `module`, `le_activated`, `re_activated`, `activated`, `deactivated`, `sort`) VALUES (35, 'poll', 0, 0, 0, 0, 32)");
  $transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "moduls` (`modulID`, `module`, `le_activated`, `re_activated`, `activated`, `deactivated`, `sort`) VALUES (36, 'polls', 0, 0, 0, 0, 33)");
  $transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "moduls` (`modulID`, `module`, `le_activated`, `re_activated`, `activated`, `deactivated`, `sort`) VALUES (37, 'register', 0, 0, 0, 0, 35)");
  $transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "moduls` (`modulID`, `module`, `le_activated`, `re_activated`, `activated`, `deactivated`, `sort`) VALUES (38, 'registered_users', 0, 0, 0, 0, 36)");
  $transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "moduls` (`modulID`, `module`, `le_activated`, `re_activated`, `activated`, `deactivated`, `sort`) VALUES (39, 'search', 0, 0, 0, 0, 37)");
  $transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "moduls` (`modulID`, `module`, `le_activated`, `re_activated`, `activated`, `deactivated`, `sort`) VALUES (40, 'server', 0, 0, 0, 0, 38)");
  $transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "moduls` (`modulID`, `module`, `le_activated`, `re_activated`, `activated`, `deactivated`, `sort`) VALUES (41, 'sponsors', 0, 0, 0, 0, 39)");
  $transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "moduls` (`modulID`, `module`, `le_activated`, `re_activated`, `activated`, `deactivated`, `sort`) VALUES (42, 'whoisonline', 0, 0, 0, 0, 42)");
  $transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "moduls` (`modulID`, `module`, `le_activated`, `re_activated`, `activated`, `deactivated`, `sort`) VALUES (43, 'news_comments', 0, 0, 0, 0, 29)");

$transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "comments_settings`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "comments_settings` (
  `ident` varchar(255) NOT NULL,
  `modul` varchar(255) NOT NULL,
  `id` varchar(255) NOT NULL,
  `parent` varchar(255) NOT NULL
) AUTO_INCREMENT=7
   DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_unicode_ci");

$transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "comments_settings` (`ident`, `modul`, `id`, `parent`) VALUES ('ne', 'news', 'newsID', 'comments')");
$transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "comments_settings` (`ident`, `modul`, `id`, `parent`) VALUES ('ar', 'articles', 'articlesID', 'comments')");
$transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "comments_settings` (`ident`, `modul`, `id`, `parent`) VALUES ('ga', 'gallery_pictures', 'picID', 'comments')");
$transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "comments_settings` (`ident`, `modul`, `id`, `parent`) VALUES ('cw', 'clanwars', 'cwID', 'comments')");
$transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "comments_settings` (`ident`, `modul`, `id`, `parent`) VALUES ('de', 'demos', 'demoID', 'comments')");
$transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "comments_settings` (`ident`, `modul`, `id`, `parent`) VALUES ('po', 'poll', 'pollID', 'comments')");

    if ($transaction->successful()) {
        return array('status' => 'success', 'message' => 'Created tables starting with "s"');
    } else {
        return array('status' => 'fail', 'message' => 'Failed to create tables starting with "s"<br/>' . $transaction->getError());
    }
}

function update_base_13($_database)
{
    global $adminname;
    global $adminpassword;
    global $adminmail;
    
    $new_pepper = Gen_PasswordPepper();
    $adminhash = password_hash($adminpassword.$new_pepper,PASSWORD_BCRYPT,array('cost'=>12));
    
    $transaction = new Transaction($_database);
    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "upcoming`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "upcoming` (
  `upID` int(11) NOT NULL AUTO_INCREMENT,
  `date` int(14) NOT NULL default '0',
  `type` char(1) NOT NULL default '',
  `squad` int(11) NOT NULL default '0',
  `opponent` varchar(255) NOT NULL default '',
  `opptag` varchar(255) NOT NULL default '',
  `opphp` varchar(255) NOT NULL default '',
  `oppcountry` char(2) NOT NULL default '',
  `maps` varchar(255) NOT NULL default '',
  `server` varchar(255) NOT NULL default '',
  `league` varchar(255) NOT NULL default '',
  `leaguehp` varchar(255) NOT NULL default '',
  `warinfo` text NOT NULL,
  `short` varchar(255) NOT NULL default '',
  `title` varchar(255) NOT NULL default '',
  `enddate` int(14) NOT NULL default '0',
  `country` char(2) NOT NULL default '',
  `location` varchar(255) NOT NULL default '',
  `locationhp` varchar(255) NOT NULL default '',
  `dateinfo` text NOT NULL,
  PRIMARY KEY  (`upID`)
) AUTO_INCREMENT=1
  DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_unicode_ci");

    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "upcoming_announce`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "upcoming_announce` (
  `annID` int(11) NOT NULL AUTO_INCREMENT,
  `upID` int(11) NOT NULL default '0',
  `userID` int(11) NOT NULL default '0',
  `status` char(1) NOT NULL default '',
  PRIMARY KEY  (`annID`)
) AUTO_INCREMENT=1
  DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_unicode_ci");

    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "user`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "user` (
  `userID` int(11) NOT NULL AUTO_INCREMENT,
  `registerdate` int(14) NOT NULL default '0',
  `lastlogin` int(14) NOT NULL default '0',
  `username` varchar(255) NOT NULL default '',
  `password` varchar(255) NOT NULL default '',
  `nickname` varchar(255) NOT NULL default '',
  `email` varchar(255) NOT NULL default '',
  `firstname` varchar(255) NOT NULL default '',
  `lastname` varchar(255) NOT NULL default '',
  `sex` char(1) NOT NULL default '',
  `country` varchar(255) NOT NULL default '',
  `town` varchar(255) NOT NULL default '',
  `birthday` int(14) NOT NULL default '0',
  `icq` varchar(255) NOT NULL default '',
  `avatar` varchar(255) NOT NULL default '',
  `usertext` varchar(255) NOT NULL default '',
  `userpic` varchar(255) NOT NULL default '',
  `clantag` varchar(255) NOT NULL default '',
  `clanname` varchar(255) NOT NULL default '',
  `clanhp` varchar(255) NOT NULL default '',
  `clanirc` varchar(255) NOT NULL default '',
  `clanhistory` varchar(255) NOT NULL default '',
  `cpu` varchar(255) NOT NULL default '',
  `mainboard` varchar(255) NOT NULL default '',
  `ram` varchar(255) NOT NULL default '',
  `monitor` varchar(255) NOT NULL default '',
  `graphiccard` varchar(255) NOT NULL default '',
  `soundcard` varchar(255) NOT NULL default '',
  `connection` varchar(255) NOT NULL default '',
  `keyboard` varchar(255) NOT NULL default '',
  `mouse` varchar(255) NOT NULL default '',
  `mousepad` varchar(255) NOT NULL default '',
  `newsletter` int(1) NOT NULL default '1',
  `about` text NOT NULL,
  `pmgot` int(11) NOT NULL default '0',
  `pmsent` int(11) NOT NULL default '0',
  `visits` int(11) NOT NULL default '0',
  `banned` int(1) NOT NULL default '0',
  `ip` varchar(255) NOT NULL default '',
  `topics` text NOT NULL,
  `articles` text NOT NULL,
  `demos` text NOT NULL,
  `special_rank` INT(11) NULL DEFAULT '0',
  PRIMARY KEY  (`userID`)
) AUTO_INCREMENT=2
  DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_unicode_ci");
  
  	$transaction->addQuery("ALTER TABLE ".PREFIX."user ADD password_hash VARCHAR(255) NOT NULL AFTER password");
	$transaction->addQuery("ALTER TABLE ".PREFIX."user ADD password_pepper VARCHAR(255) NOT NULL AFTER password_hash");
	
	
    $transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "user` (`userID`, `registerdate`, `lastlogin`, `username`, `password_hash`, `nickname`, `email`, `firstname`, `lastname`, `sex`, `country`, `town`, `birthday`, `icq`, `avatar`, `usertext`, `userpic`, `clantag`, `clanname`, `clanhp`, `clanirc`, `clanhistory`, `cpu`, `mainboard`, `ram`, `monitor`, `graphiccard`, `soundcard`, `connection`, `keyboard`, `mouse`, `mousepad`, `newsletter`, `about`, `pmgot`, `pmsent`, `visits`, `banned`, `ip`, `topics`, `articles`, `demos`)
      VALUES (1, '" . time() . "', '" . time() . "', '" . $adminname . "', '" . $adminhash . "', '" . $adminname . "', '" . $adminmail . "', '', '', 'u', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 1, '', 0, 0, 0, '', '', '', '', '')");
    
	$transaction->addQuery("UPDATE `".PREFIX."user` SET password_pepper = '".$new_pepper."' WHERE userID = '1'");
	
    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "user_gbook`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "user_gbook` (
  `userID` int(11) NOT NULL default '0',
  `gbID` int(11) NOT NULL AUTO_INCREMENT,
  `date` int(14) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `email` varchar(255) NOT NULL default '',
  `hp` varchar(255) NOT NULL default '',
  `icq` varchar(255) NOT NULL default '',
  `ip` varchar(255) NOT NULL default '',
  `comment` text NOT NULL,
  PRIMARY KEY  (`gbID`)
) AUTO_INCREMENT=1
  DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_unicode_ci");

    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "user_groups`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "user_groups` (
  `usgID` int(11) NOT NULL AUTO_INCREMENT,
  `userID` int(11) NOT NULL default '0',
  `news` int(1) NOT NULL default '0',
  `newsletter` int(1) NOT NULL default '0',
  `polls` int(1) NOT NULL default '0',
  `forum` int(1) NOT NULL default '0',
  `moderator` int(1) NOT NULL default '0',
  `internboards` int(1) NOT NULL default '0',
  `clanwars` int(1) NOT NULL default '0',
  `feedback` int(1) NOT NULL default '0',
  `user` int(1) NOT NULL default '0',
  `page` int(1) NOT NULL default '0',
  `files` int(1) NOT NULL default '0',
  `cash` int(1) NOT NULL default '0',
  PRIMARY KEY  (`usgID`)
) AUTO_INCREMENT=2
  DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_unicode_ci");

    $transaction->addQuery("INSERT INTO " . PREFIX . "user_groups (usgID, userID, news, newsletter, polls, forum, moderator, internboards, clanwars, feedback, user, page, files)
VALUES (1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1)");

    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "user_visitors`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "user_visitors` (
  `visitID` int(11) NOT NULL AUTO_INCREMENT,
  `userID` int(11) NOT NULL default '0',
  `visitor` int(11) NOT NULL default '0',
  `date` int(14) NOT NULL default '0',
  PRIMARY KEY  (`visitID`)
) AUTO_INCREMENT=1
  DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_unicode_ci");
    if ($transaction->successful()) {
        return array('status' => 'success', 'message' => 'Created tables starting with "u"');
    } else {
        return array('status' => 'fail', 'message' => 'Failed to create tables starting with "u"<br/>' . $transaction->getError());
    }
}

function update_base_14($_database)
{
    $transaction = new Transaction($_database);
    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "whoisonline`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "whoisonline` (
  `time` int(14) NOT NULL default '0',
  `ip` varchar(20) NOT NULL default '',
  `userID` int(11) NOT NULL default '0',
  `nickname` varchar(255) NOT NULL default '',
  `site` varchar(255) NOT NULL default ''
) DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_unicode_ci");

    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "whowasonline`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "whowasonline` (
  `time` int(14) NOT NULL default '0',
  `ip` varchar(20) NOT NULL default '',
  `userID` int(11) NOT NULL default '0',
  `nickname` varchar(255) NOT NULL default '',
  `site` varchar(255) NOT NULL default ''
) DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_unicode_ci");
    if ($transaction->successful()) {
        return array('status' => 'success', 'message' => 'Created tables starting with "w"');
    } else {
        return array('status' => 'fail', 'message' => 'Failed to create tables starting with "w"<br/>' . $transaction->getError());
    }
}

function update_31_4beta4($_database)
{
    $transaction = new Transaction($_database);
    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "about`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "about` (
  `about` longtext NOT NULL
 ) DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_unicode_ci");

    $transaction->addQuery("ALTER TABLE `" . PREFIX . "awards` ADD `homepage` VARCHAR( 255 ) NOT NULL ,
 ADD `rang` INT DEFAULT '0' NOT NULL ,
 ADD `info` TEXT NOT NULL");

    $transaction->addQuery("ALTER TABLE `" . PREFIX . "cash_box` ADD `squad` INT NOT NULL ,
 ADD `konto` TEXT NOT NULL");

    $transaction->addQuery("ALTER TABLE `" . PREFIX . "clanwars` ADD `linkpage` VARCHAR( 255 ) NOT NULL");
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "clanwars` CHANGE `game` `game` VARCHAR( 5 ) NOT NULL");

    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "counter_stats`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "counter_stats` (
  `dates` varchar(255) NOT NULL default '',
  `count` int(20) NOT NULL default '0'
 ) DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_unicode_ci");

    $transaction->addQuery("ALTER TABLE `" . PREFIX . "demos` ADD `accesslevel` INT( 1 ) DEFAULT '0' NOT NULL");
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "files` ADD `accesslevel` INT( 1 ) DEFAULT '0' NOT NULL");

    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "games`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "games` (
  `gameID` int(3) NOT NULL AUTO_INCREMENT,
  `tag` varchar(10) NOT NULL default '',
  `name` varchar(20) NOT NULL default '',
  PRIMARY KEY  (`gameID`)
 ) AUTO_INCREMENT=8
   DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_unicode_ci");

    $transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "games` (`gameID`, `tag`, `name`) VALUES (1, 'cs', 'Counter-Strike')");
    $transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "games` (`gameID`, `tag`, `name`) VALUES (2, 'ut', 'Unreal Tournament')");
    $transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "games` (`gameID`, `tag`, `name`) VALUES (3, 'to', 'Tactical Ops')");
    $transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "games` (`gameID`, `tag`, `name`) VALUES (4, 'hl2', 'Halflife 2')");
    $transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "games` (`gameID`, `tag`, `name`) VALUES (7, 'bf', 'Battlefield')");
    $transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "games` (`gameID`, `tag`, `name`) VALUES (5, 'wc3', 'Warcraft 3')");
    $transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "games` (`gameID`, `tag`, `name`) VALUES (6, 'hl', 'Halflife')");

    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "linkus`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "linkus` (
  `bannerID` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL default '',
  `file` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`bannerID`)
 ) AUTO_INCREMENT=1
   DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_unicode_ci");

    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "newsletter`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "newsletter` (
  `email` varchar(255) NOT NULL default '',
  `pass` varchar(255) NOT NULL default ''
 ) DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_unicode_ci");

    $transaction->addQuery("ALTER TABLE `" . PREFIX . "poll` ADD `laufzeit` BIGINT(20) NOT NULL after `aktiv`");

    $transaction->addQuery("ALTER TABLE `" . PREFIX . "servers` DROP `showed`");

    $transaction->addQuery("ALTER TABLE `" . PREFIX . "settings` CHANGE `bannerrot` `profilelast` INT( 11 ) DEFAULT '0' NOT NULL");

    $transaction->addQuery("ALTER TABLE `" . PREFIX . "settings` ADD `topnewsID` INT NOT NULL");

    $transaction->addQuery("ALTER TABLE `" . PREFIX . "squads_members` ADD `joinmember` INT(1) DEFAULT '0' NOT NULL ,
 ADD `warmember` INT(1) DEFAULT '0' NOT NULL");

    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "user_gbook`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "user_gbook` (
  `userID` int(11) NOT NULL default '0',
  `gbID` int(11) NOT NULL AUTO_INCREMENT,
  `date` int(14) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `email` varchar(255) NOT NULL default '',
  `hp` varchar(255) NOT NULL default '',
  `icq` varchar(255) NOT NULL default '',
  `ip` varchar(255) NOT NULL default '',
  `comment` text NOT NULL,
  PRIMARY KEY  (`gbID`)
 ) AUTO_INCREMENT=1
   DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_unicode_ci");

    $transaction->addQuery("ALTER TABLE `" . PREFIX . "servers` CHANGE `game` `game` CHAR( 10 ) NOT NULL");
    if ($transaction->successful()) {
        return array('status' => 'success', 'message' => 'Updated to webSPELL 4 Beta 4');
    } else {
        return array('status' => 'fail', 'message' => 'Failed to update to webSPELL 4 Beta 4<br/>' . $transaction->getError());
    }
}

function update_4beta4_4beta5($_database)
{
    $transaction = new Transaction($_database);

    $transaction->addQuery("ALTER TABLE `" . PREFIX . "settings` ADD `sessionduration` INT( 3 ) NOT NULL");
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "settings` ADD `closed` INT( 1 ) DEFAULT '0' NOT NULL");

    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "lock`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "lock` (
  `time` INT NOT NULL ,
  `reason` TEXT NOT NULL
 ) DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_unicode_ci");

    $transaction->addQuery("ALTER TABLE `" . PREFIX . "news` ADD `intern` INT( 1 ) DEFAULT '0' NOT NULL");
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "guestbook` ADD `admincomment` TEXT NOT NULL");
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "settings` ADD `gb_info` INT( 1 ) DEFAULT '1' NOT NULL");

    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "static`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "static` (
  `staticID` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR( 255 ) NOT NULL ,
  `accesslevel` INT( 1 ) NOT NULL ,
  PRIMARY KEY ( `staticID` )
  ) DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_unicode_ci");

    if ($transaction->successful()) {
        return array('status' => 'success', 'message' => 'Updated to webSPELL 4 Beta 5');
    } else {
        return array('status' => 'fail', 'message' => 'Failed to update to webSPELL 4 Beta 5<br/>' . $transaction->getError());
    }

}

function update_4beta5_4beta6($_database)
{
    $transaction = new Transaction($_database);

    $transaction->addQuery("ALTER TABLE `" . PREFIX . "user` ADD `mailonpm` INT( 1 ) DEFAULT '0' NOT NULL");

    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "imprint`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "imprint` (
  `imprintID` INT NOT NULL AUTO_INCREMENT ,
  `imprint` TEXT NOT NULL ,
  PRIMARY KEY ( `imprintID` )
  ) DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_unicode_ci");

    $transaction->addQuery("ALTER TABLE `" . PREFIX . "settings` ADD `imprint` INT( 1 ) DEFAULT '0' NOT NULL");
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "poll` ADD `hosts` TEXT NOT NULL");
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "files` CHANGE `info` `info` TEXT NOT NULL");
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "user` ADD `homepage` VARCHAR( 255 ) NOT NULL AFTER `newsletter`");

    if ($transaction->successful()) {
        return array('status' => 'success', 'message' => 'Updated to webSPELL 4 Beta 6');
    } else {
        return array('status' => 'fail', 'message' => 'Failed to update to webSPELL 4 Beta 6<br/>' . $transaction->getError());
    }

}

function update_4beta6_4final_1($_database)
{
    $transaction = new Transaction($_database);

    //files
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "files` ADD `votes` INT NOT NULL ,
  ADD `points` INT NOT NULL ,
  ADD `rating` INT NOT NULL");
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "files` ADD `mirrors` TEXT NOT NULL AFTER `file`");

    $transaction->addQuery("ALTER TABLE `" . PREFIX . "user` ADD `files` TEXT NOT NULL AFTER `demos`");
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "settings` ADD `picsize_l` INT DEFAULT '450' NOT NULL");
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "settings` ADD `picsize_h` INT DEFAULT '500' NOT NULL");
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "files` ADD `poster` INT NOT NULL");

	   //carousel
    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "carousel`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "carousel` (
  `carouselID` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `link` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `carousel_pic` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `sort` int(11) NOT NULL DEFAULT '1',
  `displayed` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '1',
  PRIMARY KEY ( `carouselID` )
  ) DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_unicode_ci");
  
  $transaction->addQuery("INSERT INTO `".PREFIX."carousel` (`title`, `link`, `description`, `carousel_pic`, `sort`, `displayed`) VALUES
('Carousel Entry #1', 'https://webspell-nor.de/', 'The Bootstrap Carousel in Webspell? No way?! Yes we did it!', '1.jpg', '1', '1'),
('Carousel Entry #2', 'https://webspell-nor.de/', 'The Bootstrap Carousel in Webspell? No way?! Yes we did it!', '2.jpg', '1', '1'),
('Carousel Entry #3', 'https://webspell-nor.de/', 'The Bootstrap Carousel in Webspell? No way?! Yes we did it!', '3.jpg', '1', '1')");

	// Navigation
	$transaction->addQuery("CREATE TABLE `" . PREFIX . "navigation_main` (
  `mnavID` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL default '',
  `link` varchar(255) NOT NULL default '',
  `sort` int(2) NOT NULL default '0',
  `isdropdown` int(1) NOT NULL default '1',
  PRIMARY KEY  (`mnavID`)
) AUTO_INCREMENT=1
  DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_unicode_ci");
  
  $transaction->addQuery("INSERT INTO `".PREFIX."navigation_main` (`mnavID`, `name`, `link`, `sort`, `isdropdown`) VALUES
(1, 'main', '#', 1, 1),
(2, 'Team', '#', 1, 1),
(3, 'community', '#', 3, 1),
(4, 'media', '#', 4, 1),
(5, 'miscellaneous', '#', 5, 1);");
  
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "navigation_sub` (
  `snavID` int(11) NOT NULL AUTO_INCREMENT,
  `mnav_ID` int(11) NOT NULL default '0', 
  `name` varchar(255) NOT NULL default '',
  `link` varchar(255) NOT NULL default '',
  `sort` int(2) NOT NULL default '0',
  `indropdown` int(1) NOT NULL default '1',
  PRIMARY KEY  (`snavID`)
) AUTO_INCREMENT=1
  DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_unicode_ci");
	
	$transaction->addQuery("INSERT INTO `".PREFIX."navigation_sub` (`snavID`, `mnav_ID`, `name`, `link`, `sort`, `indropdown`) VALUES
(1, 1, 'News', 'index.php?site=news', 0, 1),
(2, 1, 'Archive', 'index.php?site=news&action=archive', 1, 1),
(3, 1, 'Articles', 'index.php?site=articles', 1, 1),
(4, 1, 'Calendar', 'index.php?site=calendar', 1, 1),
(5, 1, 'FAQ', 'index.php?site=faq', 1, 1),
(6, 1, 'Search', 'index.php?site=search', 1, 1),
(7, 2, 'About_Us', 'index.php?site=about', 1, 1),
(8, 2, 'Squads', 'index.php?site=squads', 1, 1),
(9, 2, 'Members', 'index.php?site=members', 1, 1),
(11, 2, 'Matches', 'index.php?site=clanwars', 1, 1),
(12, 2, 'History', 'index.php?site=history', 1, 1),
(13, 2, 'Awards', 'index.php?site=awards', 1, 1),
(14, 3, 'Forum', 'index.php?site=forum', 1, 1),
(15, 3, 'Guestbook', 'index.php?site=guestbook', 1, 1),
(16, 3, 'Registered_users', 'index.php?site=registered_users', 1, 1),
(17, 3, 'whoisonline', 'index.php?site=whoisonline', 1, 1),
(18, 3, 'Polls', 'index.php?site=polls', 1, 1),
(19, 3, 'Server', 'index.php?site=server', 1, 1),
(20, 4, 'Downloads', 'index.php?site=files', 1, 1),
(21, 4, 'Demos', 'index.php?site=demos', 1, 1),
(22, 4, 'Links', 'index.php?site=links', 1, 1),
(23, 4, 'Gallery', 'index.php?site=gallery', 1, 1),
(24, 4, 'Links_us', 'index.php?site=linkus', 1, 1),
(25, 5, 'Sponsors', 'index.php?site=sponsors', 1, 1),
(26, 5, 'Newsletter', 'index.php?site=newsletter', 1, 1),
(27, 5, 'Contact', 'index.php?site=contact', 1, 1),
(28, 5, 'fight_us', 'index.php?site=challenge', 1, 1),
(29, 5, 'join_us', 'index.php?site=joinus', 1, 1),
(30, 5, 'Imprint', 'index.php?site=imprint', 1, 1);");

	
    //gallery
    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "gallery`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "gallery` (
  `galleryID` INT NOT NULL AUTO_INCREMENT ,
  `userID` INT NOT NULL ,
  `name` VARCHAR( 255 ) NOT NULL ,
  `date` INT( 14 ) NOT NULL ,
  `groupID` INT NOT NULL ,
  PRIMARY KEY ( `galleryID` )
  ) DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_unicode_ci");

    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "gallery_groups`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "gallery_groups` (
  `groupID` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR( 255 ) NOT NULL ,
  `sort` INT NOT NULL ,
  PRIMARY KEY ( `groupID` )
  ) DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_unicode_ci");

    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "gallery_pictures`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "gallery_pictures` (
  `picID` INT NOT NULL AUTO_INCREMENT ,
  `galleryID` INT NOT NULL ,
  `name` VARCHAR( 255 ) NOT NULL ,
  `comment` TEXT NOT NULL ,
  `views` INT DEFAULT '0' NOT NULL ,
  `comments` INT( 1 ) DEFAULT '1' NOT NULL ,
  PRIMARY KEY ( `picID` )
  ) DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_unicode_ci");

    $transaction->addQuery("ALTER TABLE `" . PREFIX . "settings` ADD `pictures` INT DEFAULT '12' NOT NULL");
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "settings` ADD `publicadmin` INT( 1 ) DEFAULT '1' NOT NULL");
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "user_groups` ADD `gallery` INT( 1 ) NOT NULL");
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "settings` ADD `thumbwidth` INT DEFAULT '130' NOT NULL");
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "settings` ADD `usergalleries` INT( 1 ) DEFAULT '1' NOT NULL");

    $transaction->addQuery("ALTER TABLE `" . PREFIX . "user` ADD `gallery_pictures` TEXT NOT NULL AFTER `files`");

    $transaction->addQuery("ALTER TABLE `" . PREFIX . "gallery_pictures` ADD `votes` INT NOT NULL ,
  ADD `points` INT NOT NULL ,
  ADD `rating` INT NOT NULL");

    $transaction->addQuery("ALTER TABLE `" . PREFIX . "settings` ADD `maxusergalleries` INT DEFAULT '1048576' NOT NULL");

    //country-list
    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "countries`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "countries` (
  `countryID` INT NOT NULL AUTO_INCREMENT ,
  `country` VARCHAR( 255 ) NOT NULL ,
  `short` VARCHAR( 3 ) NOT NULL ,
  PRIMARY KEY ( `countryID` )
  ) DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_unicode_ci");

    $transaction->addQuery("INSERT INTO `" . PREFIX . "countries` ( `countryID` , `country` , `short` )
  VALUES
  ('', 'Argentina', 'ar'),
  ('', 'Australia', 'au'),
  ('', 'Austria', 'at'),
  ('', 'Belgium', 'be'),
  ('', 'Bosnia Herzegowina', 'ba'),
  ('', 'Brazil', 'br'),
  ('', 'Bulgaria', 'bg'),
  ('', 'Canada', 'ca'),
  ('', 'Chile', 'cl'),
  ('', 'China', 'cn'),
  ('', 'Colombia', 'co'),
  ('', 'Czech Republic', 'cz'),
  ('', 'Croatia', 'hr'),
  ('', 'Cyprus', 'cy'),
  ('', 'Denmark', 'dk'),
  ('', 'Estonia', 'ee'),
  ('', 'Finland', 'fi'),
  ('', 'Faroe Islands', 'fo'),
  ('', 'France', 'fr'),
  ('', 'Germany', 'de'),
  ('', 'Greece', 'gr'),
  ('', 'Hungary', 'hu'),
  ('', 'Iceland', 'is'),
  ('', 'Ireland', 'ie'),
  ('', 'Israel', 'il'),
  ('', 'Italy', 'it'),
  ('', 'Japan', 'jp'),
  ('', 'Korea', 'kr'),
  ('', 'Latvia', 'lv'),
  ('', 'Lithuania', 'lt'),
  ('', 'Luxemburg', 'lu'),
  ('', 'Malaysia', 'my'),
  ('', 'Malta', 'mt'),
  ('', 'Netherlands', 'nl'),
  ('', 'Mexico', 'mx'),
  ('', 'Mongolia', 'mn'),
  ('', 'New Zealand', 'nz'),
  ('', 'Norway', 'no'),
  ('', 'Poland', 'pl'),
  ('', 'Portugal', 'pt'),
  ('', 'Romania', 'ro'),
  ('', 'Russian Federation', 'ru'),
  ('', 'Singapore', 'sg'),
  ('', 'Slovak Republic', 'sk'),
  ('', 'Slovenia', 'si'),
  ('', 'Taiwan', 'tw'),
  ('', 'South Africa', 'za'),
  ('', 'Spain', 'es'),
  ('', 'Sweden', 'se'),
  ('', 'Syria', 'sy'),
  ('', 'Switzerland', 'ch'),
  ('', 'Tibet', 'ti'),
  ('', 'Tunisia', 'tn'),
  ('', 'Turkey', 'tr'),
  ('', 'Ukraine', 'ua'),
  ('', 'United Kingdom', 'uk'),
  ('', 'USA', 'us'),
  ('', 'Venezuela', 've'),
  ('', 'Yugoslavia', 'rs'),
  ('', 'European Union', 'eu')");
    if ($transaction->successful()) {
        return array('status' => 'success', 'message' => 'Updated to webSPELL 4 Beta 6 Part 1');
    } else {
        return array('status' => 'fail', 'message' => 'Failed to update to webSPELL 4 Beta 6 Part 1<br/>' . $transaction->getError());
    }
}

function update_4beta6_4final_2($_database)
{
    $transaction = new Transaction($_database);

    //smileys
    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "smileys`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "smileys` (
  `smileyID` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL default '',
  `alt` varchar(255) NOT NULL default '',
  `pattern` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`smileyID`),
  UNIQUE KEY `name` (`name`)
) AUTO_INCREMENT=16
  DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_unicode_ci");

    $transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('--1', '--1', ':--1:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('-1', '-1', ':-1:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('100', '100', ':100:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('1234', '1234', ':1234:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('8ball', '8ball', ':8ball:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('a', 'a', ':a:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('ab', 'ab', ':ab:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('abc', 'abc', ':abc:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('abcd', 'abcd', ':abcd:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('accept', 'accept', ':accept:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('aerial_tramway', 'aerial_tramway', ':aerial_tramway:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('airplane', 'airplane', ':airplane:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('alarm_clock', 'alarm_clock', ':alarm_clock:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('alien', 'alien', ':alien:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('ambulance', 'ambulance', ':ambulance:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('anchor', 'anchor', ':anchor:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('angel', 'angel', ':angel:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('anger', 'anger', ':anger:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('angry', 'angry', ':angry:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('anguished', 'anguished', ':anguished:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('ant', 'ant', ':ant:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('apple', 'apple', ':apple:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('aquarius', 'aquarius', ':aquarius:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('aries', 'aries', ':aries:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('arrow_backward', 'arrow_backward', ':arrow_backward:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('arrow_double_down', 'arrow_double_down', ':arrow_double_down:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('arrow_double_up', 'arrow_double_up', ':arrow_double_up:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('arrow_down', 'arrow_down', ':arrow_down:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('arrow_down_small', 'arrow_down_small', ':arrow_down_small:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('arrow_forward', 'arrow_forward', ':arrow_forward:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('arrow_heading_down', 'arrow_heading_down', ':arrow_heading_down:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('arrow_heading_up', 'arrow_heading_up', ':arrow_heading_up:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('arrow_left', 'arrow_left', ':arrow_left:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('arrow_lower_left', 'arrow_lower_left', ':arrow_lower_left:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('arrow_lower_right', 'arrow_lower_right', ':arrow_lower_right:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('arrow_right', 'arrow_right', ':arrow_right:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('arrow_right_hook', 'arrow_right_hook', ':arrow_right_hook:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('arrow_up', 'arrow_up', ':arrow_up:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('arrow_up_down', 'arrow_up_down', ':arrow_up_down:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('arrow_up_small', 'arrow_up_small', ':arrow_up_small:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('arrow_upper_left', 'arrow_upper_left', ':arrow_upper_left:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('arrow_upper_right', 'arrow_upper_right', ':arrow_upper_right:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('arrows_clockwise', 'arrows_clockwise', ':arrows_clockwise:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('arrows_counterclockwise', 'arrows_counterclockwise', ':arrows_counterclockwise:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('art', 'art', ':art:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('articulated_lorry', 'articulated_lorry', ':articulated_lorry:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('astonished', 'astonished', ':astonished:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('atm', 'atm', ':atm:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('b', 'b', ':b:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('baby', 'baby', ':baby:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('baby_bottle', 'baby_bottle', ':baby_bottle:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('baby_chick', 'baby_chick', ':baby_chick:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('baby_symbol', 'baby_symbol', ':baby_symbol:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('back', 'back', ':back:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('baggage_claim', 'baggage_claim', ':baggage_claim:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('balloon', 'balloon', ':balloon:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('ballot_box_with_check', 'ballot_box_with_check', ':ballot_box_with_check:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('bamboo', 'bamboo', ':bamboo:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('banana', 'banana', ':banana:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('bangbang', 'bangbang', ':bangbang:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('bank', 'bank', ':bank:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('bar_chart', 'bar_chart', ':bar_chart:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('barber', 'barber', ':barber:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('baseball', 'baseball', ':baseball:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('basketball', 'basketball', ':basketball:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('bath', 'bath', ':bath:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('bathtub', 'bathtub', ':bathtub:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('battery', 'battery', ':battery:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('bear', 'bear', ':bear:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('bee', 'bee', ':bee:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('beer', 'beer', ':beer:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('beers', 'beers', ':beers:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('beetle', 'beetle', ':beetle:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('beginner', 'beginner', ':beginner:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('bell', 'bell', ':bell:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('bento', 'bento', ':bento:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('bicyclist', 'bicyclist', ':bicyclist:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('bike', 'bike', ':bike:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('bikini', 'bikini', ':bikini:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('bird', 'bird', ':bird:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('birthday', 'birthday', ':birthday:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('black_circle', 'black_circle', ':black_circle:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('black_joker', 'black_joker', ':black_joker:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('black_medium_small_square', 'black_medium_small_square', ':black_medium_small_square:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('black_medium_square', 'black_medium_square', ':black_medium_square:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('black_nib', 'black_nib', ':black_nib:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('black_small_square', 'black_small_square', ':black_small_square:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('black_square', 'black_square', ':black_square:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('black_square_button', 'black_square_button', ':black_square_button:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('blossom', 'blossom', ':blossom:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('blowfish', 'blowfish', ':blowfish:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('blue_book', 'blue_book', ':blue_book:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('blue_car', 'blue_car', ':blue_car:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('blue_heart', 'blue_heart', ':blue_heart:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('blush', 'blush', ':blush:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('boar', 'boar', ':boar:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('boat', 'boat', ':boat:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('bomb', 'bomb', ':bomb:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('book', 'book', ':book:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('bookmark', 'bookmark', ':bookmark:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('bookmark_tabs', 'bookmark_tabs', ':bookmark_tabs:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('books', 'books', ':books:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('boom', 'boom', ':boom:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('boot', 'boot', ':boot:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('bouquet', 'bouquet', ':bouquet:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('bow', 'bow', ':bow:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('bowling', 'bowling', ':bowling:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('bowtie', 'bowtie', ':bowtie:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('boy', 'boy', ':boy:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('bread', 'bread', ':bread:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('bride_with_veil', 'bride_with_veil', ':bride_with_veil:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('bridge_at_night', 'bridge_at_night', ':bridge_at_night:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('briefcase', 'briefcase', ':briefcase:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('broken_heart', 'broken_heart', ':broken_heart:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('bug', 'bug', ':bug:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('bulb', 'bulb', ':bulb:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('bullettrain_front', 'bullettrain_front', ':bullettrain_front:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('bullettrain_side', 'bullettrain_side', ':bullettrain_side:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('bus', 'bus', ':bus:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('busstop', 'busstop', ':busstop:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('bust_in_silhouette', 'bust_in_silhouette', ':bust_in_silhouette:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('busts_in_silhouette', 'busts_in_silhouette', ':busts_in_silhouette:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('cactus', 'cactus', ':cactus:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('cake', 'cake', ':cake:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('calendar', 'calendar', ':calendar:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('calling', 'calling', ':calling:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('camel', 'camel', ':camel:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('camera', 'camera', ':camera:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('cancer', 'cancer', ':cancer:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('candy', 'candy', ':candy:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('capital_abcd', 'capital_abcd', ':capital_abcd:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('capricorn', 'capricorn', ':capricorn:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('car', 'car', ':car:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('card_index', 'card_index', ':card_index:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('carousel_horse', 'carousel_horse', ':carousel_horse:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('cat', 'cat', ':cat:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('cat2', 'cat2', ':cat2:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('cd', 'cd', ':cd:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('chart', 'chart', ':chart:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('chart_with_downwards_trend', 'chart_with_downwards_trend', ':chart_with_downwards_trend:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('chart_with_upwards_trend', 'chart_with_upwards_trend', ':chart_with_upwards_trend:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('checkered_flag', 'checkered_flag', ':checkered_flag:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('cherries', 'cherries', ':cherries:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('cherry_blossom', 'cherry_blossom', ':cherry_blossom:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('chestnut', 'chestnut', ':chestnut:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('chicken', 'chicken', ':chicken:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('children_crossing', 'children_crossing', ':children_crossing:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('chocolate_bar', 'chocolate_bar', ':chocolate_bar:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('christmas_tree', 'christmas_tree', ':christmas_tree:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('church', 'church', ':church:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('cinema', 'cinema', ':cinema:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('circus_tent', 'circus_tent', ':circus_tent:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('city_sunrise', 'city_sunrise', ':city_sunrise:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('city_sunset', 'city_sunset', ':city_sunset:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('cl', 'cl', ':cl:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('clap', 'clap', ':clap:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('clapper', 'clapper', ':clapper:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('clipboard', 'clipboard', ':clipboard:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('clock1', 'clock1', ':clock1:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('clock10', 'clock10', ':clock10:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('clock1030', 'clock1030', ':clock1030:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('clock11', 'clock11', ':clock11:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('clock1130', 'clock1130', ':clock1130:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('clock12', 'clock12', ':clock12:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('clock1230', 'clock1230', ':clock1230:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('clock130', 'clock130', ':clock130:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('clock2', 'clock2', ':clock2:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('clock230', 'clock230', ':clock230:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('clock3', 'clock3', ':clock3:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('clock330', 'clock330', ':clock330:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('clock4', 'clock4', ':clock4:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('clock430', 'clock430', ':clock430:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('clock5', 'clock5', ':clock5:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('clock530', 'clock530', ':clock530:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('clock6', 'clock6', ':clock6:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('clock630', 'clock630', ':clock630:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('clock7', 'clock7', ':clock7:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('clock730', 'clock730', ':clock730:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('clock8', 'clock8', ':clock8:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('clock830', 'clock830', ':clock830:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('clock9', 'clock9', ':clock9:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('clock930', 'clock930', ':clock930:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('closed_book', 'closed_book', ':closed_book:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('closed_lock_with_key', 'closed_lock_with_key', ':closed_lock_with_key:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('closed_umbrella', 'closed_umbrella', ':closed_umbrella:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('cloud', 'cloud', ':cloud:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('clubs', 'clubs', ':clubs:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('cn', 'cn', ':cn:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('cocktail', 'cocktail', ':cocktail:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('coffee', 'coffee', ':coffee:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('cold_sweat', 'cold_sweat', ':cold_sweat:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('collision', 'collision', ':collision:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('computer', 'computer', ':computer:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('confetti_ball', 'confetti_ball', ':confetti_ball:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('confounded', 'confounded', ':confounded:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('confused', 'confused', ':confused:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('congratulations', 'congratulations', ':congratulations:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('construction', 'construction', ':construction:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('construction_worker', 'construction_worker', ':construction_worker:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('convenience_store', 'convenience_store', ':convenience_store:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('cookie', 'cookie', ':cookie:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('cool', 'cool', ':cool:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('cop', 'cop', ':cop:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('copyright', 'copyright', ':copyright:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('corn', 'corn', ':corn:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('couple', 'couple', ':couple:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('couple_with_heart', 'couple_with_heart', ':couple_with_heart:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('couplekiss', 'couplekiss', ':couplekiss:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('cow', 'cow', ':cow:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('cow2', 'cow2', ':cow2:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('credit_card', 'credit_card', ':credit_card:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('crocodile', 'crocodile', ':crocodile:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('crossed_flags', 'crossed_flags', ':crossed_flags:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('crown', 'crown', ':crown:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('cry', 'cry', ':cry:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('crying_cat_face', 'crying_cat_face', ':crying_cat_face:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('crystal_ball', 'crystal_ball', ':crystal_ball:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('cupid', 'cupid', ':cupid:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('curly_loop', 'curly_loop', ':curly_loop:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('currency_exchange', 'currency_exchange', ':currency_exchange:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('curry', 'curry', ':curry:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('custard', 'custard', ':custard:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('customs', 'customs', ':customs:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('cyclone', 'cyclone', ':cyclone:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('dancer', 'dancer', ':dancer:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('dancers', 'dancers', ':dancers:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('dango', 'dango', ':dango:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('dart', 'dart', ':dart:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('dash', 'dash', ':dash:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('date', 'date', ':date:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('de', 'de', ':de:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('deciduous_tree', 'deciduous_tree', ':deciduous_tree:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('department_store', 'department_store', ':department_store:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('diamond_shape_with_a_dot_inside', 'diamond_shape_with_a_dot_inside', ':diamond_shape_with_a_dot_inside:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('diamonds', 'diamonds', ':diamonds:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('disappointed', 'disappointed', ':disappointed:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('disappointed_relieved', 'disappointed_relieved', ':disappointed_relieved:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('dizzy', 'dizzy', ':dizzy:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('dizzy_face', 'dizzy_face', ':dizzy_face:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('do_not_litter', 'do_not_litter', ':do_not_litter:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('dog', 'dog', ':dog:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('dog2', 'dog2', ':dog2:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('dollar', 'dollar', ':dollar:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('dolls', 'dolls', ':dolls:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('dolphin', 'dolphin', ':dolphin:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('donut', 'donut', ':donut:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('door', 'door', ':door:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('doughnut', 'doughnut', ':doughnut:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('dragon', 'dragon', ':dragon:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('dragon_face', 'dragon_face', ':dragon_face:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('dress', 'dress', ':dress:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('dromedary_camel', 'dromedary_camel', ':dromedary_camel:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('droplet', 'droplet', ':droplet:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('dvd', 'dvd', ':dvd:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('e-mail', 'e-mail', ':e-mail:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('ear', 'ear', ':ear:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('ear_of_rice', 'ear_of_rice', ':ear_of_rice:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('earth_africa', 'earth_africa', ':earth_africa:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('earth_americas', 'earth_americas', ':earth_americas:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('earth_asia', 'earth_asia', ':earth_asia:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('egg', 'egg', ':egg:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('eggplant', 'eggplant', ':eggplant:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('eight', 'eight', ':eight:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('eight_pointed_black_star', 'eight_pointed_black_star', ':eight_pointed_black_star:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('eight_spoked_asterisk', 'eight_spoked_asterisk', ':eight_spoked_asterisk:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('electric_plug', 'electric_plug', ':electric_plug:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('elephant', 'elephant', ':elephant:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('email', 'email', ':email:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('end', 'end', ':end:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('envelope', 'envelope', ':envelope:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('es', 'es', ':es:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('euro', 'euro', ':euro:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('european_castle', 'european_castle', ':european_castle:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('european_post_office', 'european_post_office', ':european_post_office:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('evergreen_tree', 'evergreen_tree', ':evergreen_tree:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('exclamation', 'exclamation', ':exclamation:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('expressionless', 'expressionless', ':expressionless:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('eyeglasses', 'eyeglasses', ':eyeglasses:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('eyes', 'eyes', ':eyes:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('facepunch', 'facepunch', ':facepunch:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('factory', 'factory', ':factory:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('fallen_leaf', 'fallen_leaf', ':fallen_leaf:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('family', 'family', ':family:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('fast_forward', 'fast_forward', ':fast_forward:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('fax', 'fax', ':fax:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('fearful', 'fearful', ':fearful:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('feelsgood', 'feelsgood', ':feelsgood:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('feet', 'feet', ':feet:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('ferris_wheel', 'ferris_wheel', ':ferris_wheel:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('file_folder', 'file_folder', ':file_folder:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('finnadie', 'finnadie', ':finnadie:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('fire', 'fire', ':fire:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('fire_engine', 'fire_engine', ':fire_engine:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('fireworks', 'fireworks', ':fireworks:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('first_quarter_moon', 'first_quarter_moon', ':first_quarter_moon:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('first_quarter_moon_with_face', 'first_quarter_moon_with_face', ':first_quarter_moon_with_face:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('fish', 'fish', ':fish:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('fish_cake', 'fish_cake', ':fish_cake:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('fishing_pole_and_fish', 'fishing_pole_and_fish', ':fishing_pole_and_fish:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('fist', 'fist', ':fist:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('five', 'five', ':five:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('flags', 'flags', ':flags:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('flashlight', 'flashlight', ':flashlight:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('floppy_disk', 'floppy_disk', ':floppy_disk:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('flower_playing_cards', 'flower_playing_cards', ':flower_playing_cards:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('flushed', 'flushed', ':flushed:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('foggy', 'foggy', ':foggy:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('football', 'football', ':football:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('fork_and_knife', 'fork_and_knife', ':fork_and_knife:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('fountain', 'fountain', ':fountain:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('four', 'four', ':four:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('four_leaf_clover', 'four_leaf_clover', ':four_leaf_clover:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('fr', 'fr', ':fr:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('free', 'free', ':free:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('fried_shrimp', 'fried_shrimp', ':fried_shrimp:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('fries', 'fries', ':fries:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('frog', 'frog', ':frog:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('frowning', 'frowning', ':frowning:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('fu', 'fu', ':fu:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('fuelpump', 'fuelpump', ':fuelpump:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('full_moon', 'full_moon', ':full_moon:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('full_moon_with_face', 'full_moon_with_face', ':full_moon_with_face:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('game_die', 'game_die', ':game_die:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('gb', 'gb', ':gb:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('gem', 'gem', ':gem:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('gemini', 'gemini', ':gemini:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('ghost', 'ghost', ':ghost:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('gift', 'gift', ':gift:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('gift_heart', 'gift_heart', ':gift_heart:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('girl', 'girl', ':girl:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('globe_with_meridians', 'globe_with_meridians', ':globe_with_meridians:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('goat', 'goat', ':goat:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('goberserk', 'goberserk', ':goberserk:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('godmode', 'godmode', ':godmode:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('golf', 'golf', ':golf:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('grapes', 'grapes', ':grapes:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('green_apple', 'green_apple', ':green_apple:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('green_book', 'green_book', ':green_book:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('green_heart', 'green_heart', ':green_heart:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('grey_exclamation', 'grey_exclamation', ':grey_exclamation:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('grey_question', 'grey_question', ':grey_question:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('grimacing', 'grimacing', ':grimacing:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('grin', 'grin', ':grin:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('grinning', 'grinning', ':grinning:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('guardsman', 'guardsman', ':guardsman:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('guitar', 'guitar', ':guitar:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('gun', 'gun', ':gun:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('haircut', 'haircut', ':haircut:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('hamburger', 'hamburger', ':hamburger:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('hammer', 'hammer', ':hammer:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('hamster', 'hamster', ':hamster:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('hand', 'hand', ':hand:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('handbag', 'handbag', ':handbag:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('hankey', 'hankey', ':hankey:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('hash', 'hash', ':hash:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('hatched_chick', 'hatched_chick', ':hatched_chick:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('hatching_chick', 'hatching_chick', ':hatching_chick:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('headphones', 'headphones', ':headphones:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('hear_no_evil', 'hear_no_evil', ':hear_no_evil:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('heart', 'heart', ':heart:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('heart_decoration', 'heart_decoration', ':heart_decoration:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('heart_eyes', 'heart_eyes', ':heart_eyes:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('heart_eyes_cat', 'heart_eyes_cat', ':heart_eyes_cat:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('heartbeat', 'heartbeat', ':heartbeat:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('heartpulse', 'heartpulse', ':heartpulse:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('hearts', 'hearts', ':hearts:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('heavy_check_mark', 'heavy_check_mark', ':heavy_check_mark:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('heavy_division_sign', 'heavy_division_sign', ':heavy_division_sign:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('heavy_dollar_sign', 'heavy_dollar_sign', ':heavy_dollar_sign:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('heavy_exclamation_mark', 'heavy_exclamation_mark', ':heavy_exclamation_mark:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('heavy_minus_sign', 'heavy_minus_sign', ':heavy_minus_sign:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('heavy_multiplication_x', 'heavy_multiplication_x', ':heavy_multiplication_x:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('heavy_plus_sign', 'heavy_plus_sign', ':heavy_plus_sign:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('helicopter', 'helicopter', ':helicopter:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('herb', 'herb', ':herb:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('hibiscus', 'hibiscus', ':hibiscus:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('high_brightness', 'high_brightness', ':high_brightness:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('high_heel', 'high_heel', ':high_heel:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('hocho', 'hocho', ':hocho:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('honey_pot', 'honey_pot', ':honey_pot:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('honeybee', 'honeybee', ':honeybee:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('horse', 'horse', ':horse:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('horse_racing', 'horse_racing', ':horse_racing:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('hospital', 'hospital', ':hospital:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('hotel', 'hotel', ':hotel:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('hotsprings', 'hotsprings', ':hotsprings:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('hourglass', 'hourglass', ':hourglass:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('hourglass_flowing_sand', 'hourglass_flowing_sand', ':hourglass_flowing_sand:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('house', 'house', ':house:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('house_with_garden', 'house_with_garden', ':house_with_garden:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('hurtrealbad', 'hurtrealbad', ':hurtrealbad:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('hushed', 'hushed', ':hushed:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('ice_cream', 'ice_cream', ':ice_cream:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('icecream', 'icecream', ':icecream:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('id', 'id', ':id:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('ideograph_advantage', 'ideograph_advantage', ':ideograph_advantage:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('imp', 'imp', ':imp:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('inbox_tray', 'inbox_tray', ':inbox_tray:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('incoming_envelope', 'incoming_envelope', ':incoming_envelope:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('information_desk_person', 'information_desk_person', ':information_desk_person:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('information_source', 'information_source', ':information_source:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('innocent', 'innocent', ':innocent:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('interrobang', 'interrobang', ':interrobang:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('iphone', 'iphone', ':iphone:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('it', 'it', ':it:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('izakaya_lantern', 'izakaya_lantern', ':izakaya_lantern:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('jack_o_lantern', 'jack_o_lantern', ':jack_o_lantern:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('japan', 'japan', ':japan:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('japanese_castle', 'japanese_castle', ':japanese_castle:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('japanese_goblin', 'japanese_goblin', ':japanese_goblin:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('japanese_ogre', 'japanese_ogre', ':japanese_ogre:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('jeans', 'jeans', ':jeans:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('joy', 'joy', ':joy:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('joy_cat', 'joy_cat', ':joy_cat:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('jp', 'jp', ':jp:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('key', 'key', ':key:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('keycap_ten', 'keycap_ten', ':keycap_ten:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('kimono', 'kimono', ':kimono:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('kiss', 'kiss', ':kiss:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('kissing', 'kissing', ':kissing:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('kissing_cat', 'kissing_cat', ':kissing_cat:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('kissing_closed_eyes', 'kissing_closed_eyes', ':kissing_closed_eyes:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('kissing_face', 'kissing_face', ':kissing_face:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('kissing_heart', 'kissing_heart', ':kissing_heart:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('kissing_smiling_eyes', 'kissing_smiling_eyes', ':kissing_smiling_eyes:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('koala', 'koala', ':koala:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('koko', 'koko', ':koko:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('kr', 'kr', ':kr:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('large_blue_circle', 'large_blue_circle', ':large_blue_circle:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('large_blue_diamond', 'large_blue_diamond', ':large_blue_diamond:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('large_orange_diamond', 'large_orange_diamond', ':large_orange_diamond:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('last_quarter_moon', 'last_quarter_moon', ':last_quarter_moon:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('last_quarter_moon_with_face', 'last_quarter_moon_with_face', ':last_quarter_moon_with_face:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('laughing', 'laughing', ':laughing:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('leaves', 'leaves', ':leaves:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('ledger', 'ledger', ':ledger:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('left_luggage', 'left_luggage', ':left_luggage:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('left_right_arrow', 'left_right_arrow', ':left_right_arrow:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('leftwards_arrow_with_hook', 'leftwards_arrow_with_hook', ':leftwards_arrow_with_hook:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('lemon', 'lemon', ':lemon:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('leo', 'leo', ':leo:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('leopard', 'leopard', ':leopard:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('libra', 'libra', ':libra:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('light_rail', 'light_rail', ':light_rail:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('link', 'link', ':link:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('lips', 'lips', ':lips:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('lipstick', 'lipstick', ':lipstick:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('lock', 'lock', ':lock:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('lock_with_ink_pen', 'lock_with_ink_pen', ':lock_with_ink_pen:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('lollipop', 'lollipop', ':lollipop:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('loop', 'loop', ':loop:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('loudspeaker', 'loudspeaker', ':loudspeaker:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('love_hotel', 'love_hotel', ':love_hotel:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('love_letter', 'love_letter', ':love_letter:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('low_brightness', 'low_brightness', ':low_brightness:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('m', 'm', ':m:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('mag', 'mag', ':mag:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('mag_right', 'mag_right', ':mag_right:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('mahjong', 'mahjong', ':mahjong:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('mailbox', 'mailbox', ':mailbox:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('mailbox_closed', 'mailbox_closed', ':mailbox_closed:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('mailbox_with_mail', 'mailbox_with_mail', ':mailbox_with_mail:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('mailbox_with_no_mail', 'mailbox_with_no_mail', ':mailbox_with_no_mail:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('man', 'man', ':man:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('man_with_gua_pi_mao', 'man_with_gua_pi_mao', ':man_with_gua_pi_mao:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('man_with_turban', 'man_with_turban', ':man_with_turban:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('mans_shoe', 'mans_shoe', ':mans_shoe:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('maple_leaf', 'maple_leaf', ':maple_leaf:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('mask', 'mask', ':mask:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('massage', 'massage', ':massage:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('meat_on_bone', 'meat_on_bone', ':meat_on_bone:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('mega', 'mega', ':mega:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('melon', 'melon', ':melon:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('memo', 'memo', ':memo:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('mens', 'mens', ':mens:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('metal', 'metal', ':metal:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('metro', 'metro', ':metro:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('microphone', 'microphone', ':microphone:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('microscope', 'microscope', ':microscope:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('milky_way', 'milky_way', ':milky_way:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('minibus', 'minibus', ':minibus:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('minidisc', 'minidisc', ':minidisc:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('mobile_phone_off', 'mobile_phone_off', ':mobile_phone_off:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('money_with_wings', 'money_with_wings', ':money_with_wings:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('moneybag', 'moneybag', ':moneybag:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('monkey', 'monkey', ':monkey:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('monkey_face', 'monkey_face', ':monkey_face:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('monorail', 'monorail', ':monorail:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('moon', 'moon', ':moon:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('mortar_board', 'mortar_board', ':mortar_board:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('mount_fuji', 'mount_fuji', ':mount_fuji:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('mountain_bicyclist', 'mountain_bicyclist', ':mountain_bicyclist:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('mountain_cableway', 'mountain_cableway', ':mountain_cableway:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('mountain_railway', 'mountain_railway', ':mountain_railway:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('mouse', 'mouse', ':mouse:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('mouse2', 'mouse2', ':mouse2:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('movie_camera', 'movie_camera', ':movie_camera:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('moyai', 'moyai', ':moyai:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('muscle', 'muscle', ':muscle:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('mushroom', 'mushroom', ':mushroom:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('musical_keyboard', 'musical_keyboard', ':musical_keyboard:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('musical_note', 'musical_note', ':musical_note:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('musical_score', 'musical_score', ':musical_score:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('mute', 'mute', ':mute:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('nail_care', 'nail_care', ':nail_care:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('name_badge', 'name_badge', ':name_badge:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('neckbeard', 'neckbeard', ':neckbeard:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('necktie', 'necktie', ':necktie:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('negative_squared_cross_mark', 'negative_squared_cross_mark', ':negative_squared_cross_mark:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('neutral_face', 'neutral_face', ':neutral_face:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('new', 'new', ':new:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('new_moon', 'new_moon', ':new_moon:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('new_moon_with_face', 'new_moon_with_face', ':new_moon_with_face:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('newspaper', 'newspaper', ':newspaper:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('ng', 'ng', ':ng:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('nine', 'nine', ':nine:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('no_bell', 'no_bell', ':no_bell:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('no_bicycles', 'no_bicycles', ':no_bicycles:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('no_entry', 'no_entry', ':no_entry:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('no_entry_sign', 'no_entry_sign', ':no_entry_sign:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('no_good', 'no_good', ':no_good:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('no_mobile_phones', 'no_mobile_phones', ':no_mobile_phones:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('no_mouth', 'no_mouth', ':no_mouth:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('no_pedestrians', 'no_pedestrians', ':no_pedestrians:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('no_smoking', 'no_smoking', ':no_smoking:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('non-potable_water', 'non-potable_water', ':non-potable_water:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('nose', 'nose', ':nose:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('notebook', 'notebook', ':notebook:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('notebook_with_decorative_cover', 'notebook_with_decorative_cover', ':notebook_with_decorative_cover:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('notes', 'notes', ':notes:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('nut_and_bolt', 'nut_and_bolt', ':nut_and_bolt:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('o', 'o', ':o:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('o2', 'o2', ':o2:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('ocean', 'ocean', ':ocean:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('octocat', 'octocat', ':octocat:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('octopus', 'octopus', ':octopus:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('oden', 'oden', ':oden:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('office', 'office', ':office:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('ok', 'ok', ':ok:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('ok_hand', 'ok_hand', ':ok_hand:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('ok_woman', 'ok_woman', ':ok_woman:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('older_man', 'older_man', ':older_man:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('older_woman', 'older_woman', ':older_woman:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('on', 'on', ':on:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('oncoming_automobile', 'oncoming_automobile', ':oncoming_automobile:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('oncoming_bus', 'oncoming_bus', ':oncoming_bus:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('oncoming_police_car', 'oncoming_police_car', ':oncoming_police_car:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('oncoming_taxi', 'oncoming_taxi', ':oncoming_taxi:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('one', 'one', ':one:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('open_file_folder', 'open_file_folder', ':open_file_folder:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('open_hands', 'open_hands', ':open_hands:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('open_mouth', 'open_mouth', ':open_mouth:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('ophiuchus', 'ophiuchus', ':ophiuchus:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('orange_book', 'orange_book', ':orange_book:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('outbox_tray', 'outbox_tray', ':outbox_tray:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('ox', 'ox', ':ox:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('package', 'package', ':package:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('page_facing_up', 'page_facing_up', ':page_facing_up:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('page_with_curl', 'page_with_curl', ':page_with_curl:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('pager', 'pager', ':pager:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('palm_tree', 'palm_tree', ':palm_tree:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('panda_face', 'panda_face', ':panda_face:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('paperclip', 'paperclip', ':paperclip:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('parking', 'parking', ':parking:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('part_alternation_mark', 'part_alternation_mark', ':part_alternation_mark:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('partly_sunny', 'partly_sunny', ':partly_sunny:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('passport_control', 'passport_control', ':passport_control:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('paw_prints', 'paw_prints', ':paw_prints:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('peach', 'peach', ':peach:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('pear', 'pear', ':pear:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('pencil', 'pencil', ':pencil:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('pencil2', 'pencil2', ':pencil2:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('penguin', 'penguin', ':penguin:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('pensive', 'pensive', ':pensive:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('performing_arts', 'performing_arts', ':performing_arts:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('persevere', 'persevere', ':persevere:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('person_frowning', 'person_frowning', ':person_frowning:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('person_with_blond_hair', 'person_with_blond_hair', ':person_with_blond_hair:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('person_with_pouting_face', 'person_with_pouting_face', ':person_with_pouting_face:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('phone', 'phone', ':phone:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('pig', 'pig', ':pig:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('pig2', 'pig2', ':pig2:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('pig_nose', 'pig_nose', ':pig_nose:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('pill', 'pill', ':pill:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('pineapple', 'pineapple', ':pineapple:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('pisces', 'pisces', ':pisces:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('pizza', 'pizza', ':pizza:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('plus1', 'plus1', ':plus1:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('point_down', 'point_down', ':point_down:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('point_left', 'point_left', ':point_left:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('point_right', 'point_right', ':point_right:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('point_up', 'point_up', ':point_up:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('point_up_2', 'point_up_2', ':point_up_2:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('police_car', 'police_car', ':police_car:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('poodle', 'poodle', ':poodle:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('poop', 'poop', ':poop:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('post_office', 'post_office', ':post_office:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('postal_horn', 'postal_horn', ':postal_horn:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('postbox', 'postbox', ':postbox:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('potable_water', 'potable_water', ':potable_water:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('pouch', 'pouch', ':pouch:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('poultry_leg', 'poultry_leg', ':poultry_leg:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('pound', 'pound', ':pound:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('pouting_cat', 'pouting_cat', ':pouting_cat:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('pray', 'pray', ':pray:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('princess', 'princess', ':princess:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('punch', 'punch', ':punch:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('purple_heart', 'purple_heart', ':purple_heart:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('purse', 'purse', ':purse:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('pushpin', 'pushpin', ':pushpin:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('put_litter_in_its_place', 'put_litter_in_its_place', ':put_litter_in_its_place:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('question', 'question', ':question:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('rabbit', 'rabbit', ':rabbit:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('rabbit2', 'rabbit2', ':rabbit2:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('racehorse', 'racehorse', ':racehorse:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('radio', 'radio', ':radio:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('radio_button', 'radio_button', ':radio_button:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('rage', 'rage', ':rage:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('rage1', 'rage1', ':rage1:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('rage2', 'rage2', ':rage2:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('rage3', 'rage3', ':rage3:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('rage4', 'rage4', ':rage4:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('railway_car', 'railway_car', ':railway_car:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('rainbow', 'rainbow', ':rainbow:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('raised_hand', 'raised_hand', ':raised_hand:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('raised_hands', 'raised_hands', ':raised_hands:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('raising_hand', 'raising_hand', ':raising_hand:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('ram', 'ram', ':ram:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('ramen', 'ramen', ':ramen:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('rat', 'rat', ':rat:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('recycle', 'recycle', ':recycle:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('red_car', 'red_car', ':red_car:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('red_circle', 'red_circle', ':red_circle:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('registered', 'registered', ':registered:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('relaxed', 'relaxed', ':relaxed:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('relieved', 'relieved', ':relieved:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('repeat', 'repeat', ':repeat:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('repeat_one', 'repeat_one', ':repeat_one:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('restroom', 'restroom', ':restroom:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('revolving_hearts', 'revolving_hearts', ':revolving_hearts:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('rewind', 'rewind', ':rewind:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('ribbon', 'ribbon', ':ribbon:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('rice', 'rice', ':rice:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('rice_ball', 'rice_ball', ':rice_ball:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('rice_cracker', 'rice_cracker', ':rice_cracker:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('rice_scene', 'rice_scene', ':rice_scene:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('ring', 'ring', ':ring:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('rocket', 'rocket', ':rocket:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('roller_coaster', 'roller_coaster', ':roller_coaster:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('rooster', 'rooster', ':rooster:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('rose', 'rose', ':rose:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('rotating_light', 'rotating_light', ':rotating_light:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('round_pushpin', 'round_pushpin', ':round_pushpin:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('rowboat', 'rowboat', ':rowboat:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('ru', 'ru', ':ru:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('rugby_football', 'rugby_football', ':rugby_football:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('runner', 'runner', ':runner:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('running', 'running', ':running:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('running_shirt_with_sash', 'running_shirt_with_sash', ':running_shirt_with_sash:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('sa', 'sa', ':sa:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('sagittarius', 'sagittarius', ':sagittarius:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('sailboat', 'sailboat', ':sailboat:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('sake', 'sake', ':sake:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('sandal', 'sandal', ':sandal:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('santa', 'santa', ':santa:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('satellite', 'satellite', ':satellite:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('satisfied', 'satisfied', ':satisfied:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('saxophone', 'saxophone', ':saxophone:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('school', 'school', ':school:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('school_satchel', 'school_satchel', ':school_satchel:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('scissors', 'scissors', ':scissors:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('scorpius', 'scorpius', ':scorpius:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('scream', 'scream', ':scream:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('scream_cat', 'scream_cat', ':scream_cat:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('scroll', 'scroll', ':scroll:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('seat', 'seat', ':seat:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('secret', 'secret', ':secret:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('see_no_evil', 'see_no_evil', ':see_no_evil:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('seedling', 'seedling', ':seedling:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('seven', 'seven', ':seven:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('shaved_ice', 'shaved_ice', ':shaved_ice:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('sheep', 'sheep', ':sheep:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('shell', 'shell', ':shell:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('ship', 'ship', ':ship:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('shipit', 'shipit', ':shipit:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('shirt', 'shirt', ':shirt:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('shit', 'shit', ':shit:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('shoe', 'shoe', ':shoe:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('shower', 'shower', ':shower:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('signal_strength', 'signal_strength', ':signal_strength:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('six', 'six', ':six:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('six_pointed_star', 'six_pointed_star', ':six_pointed_star:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('ski', 'ski', ':ski:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('skull', 'skull', ':skull:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('sleeping', 'sleeping', ':sleeping:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('sleepy', 'sleepy', ':sleepy:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('slot_machine', 'slot_machine', ':slot_machine:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('small_blue_diamond', 'small_blue_diamond', ':small_blue_diamond:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('small_orange_diamond', 'small_orange_diamond', ':small_orange_diamond:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('small_red_triangle', 'small_red_triangle', ':small_red_triangle:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('small_red_triangle_down', 'small_red_triangle_down', ':small_red_triangle_down:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('smile', 'smile', ':smile:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('smile_cat', 'smile_cat', ':smile_cat:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('smiley', 'smiley', ':smiley:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('smiley_cat', 'smiley_cat', ':smiley_cat:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('smiling_imp', 'smiling_imp', ':smiling_imp:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('smirk', 'smirk', ':smirk:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('smirk_cat', 'smirk_cat', ':smirk_cat:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('smoking', 'smoking', ':smoking:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('snail', 'snail', ':snail:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('snake', 'snake', ':snake:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('snowboarder', 'snowboarder', ':snowboarder:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('snowflake', 'snowflake', ':snowflake:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('snowman', 'snowman', ':snowman:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('sob', 'sob', ':sob:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('soccer', 'soccer', ':soccer:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('soon', 'soon', ':soon:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('sos', 'sos', ':sos:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('sound', 'sound', ':sound:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('space_invader', 'space_invader', ':space_invader:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('spades', 'spades', ':spades:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('spaghetti', 'spaghetti', ':spaghetti:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('sparkle', 'sparkle', ':sparkle:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('sparkler', 'sparkler', ':sparkler:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('sparkles', 'sparkles', ':sparkles:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('sparkling_heart', 'sparkling_heart', ':sparkling_heart:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('speak_no_evil', 'speak_no_evil', ':speak_no_evil:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('speaker', 'speaker', ':speaker:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('speech_balloon', 'speech_balloon', ':speech_balloon:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('speedboat', 'speedboat', ':speedboat:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('squirrel', 'squirrel', ':squirrel:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('star', 'star', ':star:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('star2', 'star2', ':star2:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('stars', 'stars', ':stars:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('station', 'station', ':station:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('statue_of_liberty', 'statue_of_liberty', ':statue_of_liberty:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('steam_locomotive', 'steam_locomotive', ':steam_locomotive:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('stew', 'stew', ':stew:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('straight_ruler', 'straight_ruler', ':straight_ruler:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('strawberry', 'strawberry', ':strawberry:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('stuck_out_tongue', 'stuck_out_tongue', ':stuck_out_tongue:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('stuck_out_tongue_closed_eyes', 'stuck_out_tongue_closed_eyes', ':stuck_out_tongue_closed_eyes:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('stuck_out_tongue_winking_eye', 'stuck_out_tongue_winking_eye', ':stuck_out_tongue_winking_eye:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('sun_with_face', 'sun_with_face', ':sun_with_face:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('sunflower', 'sunflower', ':sunflower:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('sunglasses', 'sunglasses', ':sunglasses:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('sunny', 'sunny', ':sunny:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('sunrise', 'sunrise', ':sunrise:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('sunrise_over_mountains', 'sunrise_over_mountains', ':sunrise_over_mountains:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('surfer', 'surfer', ':surfer:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('sushi', 'sushi', ':sushi:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('suspect', 'suspect', ':suspect:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('suspension_railway', 'suspension_railway', ':suspension_railway:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('sweat', 'sweat', ':sweat:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('sweat_drops', 'sweat_drops', ':sweat_drops:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('sweat_smile', 'sweat_smile', ':sweat_smile:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('sweet_potato', 'sweet_potato', ':sweet_potato:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('swimmer', 'swimmer', ':swimmer:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('symbols', 'symbols', ':symbols:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('syringe', 'syringe', ':syringe:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('tada', 'tada', ':tada:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('tanabata_tree', 'tanabata_tree', ':tanabata_tree:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('tangerine', 'tangerine', ':tangerine:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('taurus', 'taurus', ':taurus:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('taxi', 'taxi', ':taxi:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('tea', 'tea', ':tea:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('telephone', 'telephone', ':telephone:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('telephone_receiver', 'telephone_receiver', ':telephone_receiver:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('telescope', 'telescope', ':telescope:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('tennis', 'tennis', ':tennis:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('tent', 'tent', ':tent:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('thought_balloon', 'thought_balloon', ':thought_balloon:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('three', 'three', ':three:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('thumbsdown', 'thumbsdown', ':thumbsdown:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('thumbsup', 'thumbsup', ':thumbsup:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('ticket', 'ticket', ':ticket:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('tiger', 'tiger', ':tiger:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('tiger2', 'tiger2', ':tiger2:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('tired_face', 'tired_face', ':tired_face:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('tm', 'tm', ':tm:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('toilet', 'toilet', ':toilet:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('tokyo_tower', 'tokyo_tower', ':tokyo_tower:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('tomato', 'tomato', ':tomato:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('tongue', 'tongue', ':tongue:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('top', 'top', ':top:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('tophat', 'tophat', ':tophat:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('tractor', 'tractor', ':tractor:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('traffic_light', 'traffic_light', ':traffic_light:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('train', 'train', ':train:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('train2', 'train2', ':train2:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('tram', 'tram', ':tram:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('triangular_flag_on_post', 'triangular_flag_on_post', ':triangular_flag_on_post:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('triangular_ruler', 'triangular_ruler', ':triangular_ruler:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('trident', 'trident', ':trident:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('triumph', 'triumph', ':triumph:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('trolleybus', 'trolleybus', ':trolleybus:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('trollface', 'trollface', ':trollface:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('trophy', 'trophy', ':trophy:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('tropical_drink', 'tropical_drink', ':tropical_drink:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('tropical_fish', 'tropical_fish', ':tropical_fish:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('truck', 'truck', ':truck:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('trumpet', 'trumpet', ':trumpet:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('tshirt', 'tshirt', ':tshirt:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('tulip', 'tulip', ':tulip:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('turtle', 'turtle', ':turtle:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('tv', 'tv', ':tv:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('twisted_rightwards_arrows', 'twisted_rightwards_arrows', ':twisted_rightwards_arrows:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('two', 'two', ':two:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('two_hearts', 'two_hearts', ':two_hearts:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('two_men_holding_hands', 'two_men_holding_hands', ':two_men_holding_hands:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('two_women_holding_hands', 'two_women_holding_hands', ':two_women_holding_hands:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('u5272', 'u5272', ':u5272:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('u5408', 'u5408', ':u5408:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('u55b6', 'u55b6', ':u55b6:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('u6307', 'u6307', ':u6307:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('u6708', 'u6708', ':u6708:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('u6709', 'u6709', ':u6709:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('u6e80', 'u6e80', ':u6e80:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('u7121', 'u7121', ':u7121:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('u7533', 'u7533', ':u7533:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('u7981', 'u7981', ':u7981:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('u7a7a', 'u7a7a', ':u7a7a:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('uk', 'uk', ':uk:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('umbrella', 'umbrella', ':umbrella:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('unamused', 'unamused', ':unamused:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('underage', 'underage', ':underage:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('unlock', 'unlock', ':unlock:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('up', 'up', ':up:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('us', 'us', ':us:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('v', 'v', ':v:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('vertical_traffic_light', 'vertical_traffic_light', ':vertical_traffic_light:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('vhs', 'vhs', ':vhs:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('vibration_mode', 'vibration_mode', ':vibration_mode:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('video_camera', 'video_camera', ':video_camera:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('video_game', 'video_game', ':video_game:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('violin', 'violin', ':violin:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('virgo', 'virgo', ':virgo:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('volcano', 'volcano', ':volcano:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('vs', 'vs', ':vs:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('walking', 'walking', ':walking:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('waning_crescent_moon', 'waning_crescent_moon', ':waning_crescent_moon:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('waning_gibbous_moon', 'waning_gibbous_moon', ':waning_gibbous_moon:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('warning', 'warning', ':warning:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('watch', 'watch', ':watch:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('water_buffalo', 'water_buffalo', ':water_buffalo:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('watermelon', 'watermelon', ':watermelon:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('wave', 'wave', ':wave:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('wavy_dash', 'wavy_dash', ':wavy_dash:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('waxing_crescent_moon', 'waxing_crescent_moon', ':waxing_crescent_moon:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('waxing_gibbous_moon', 'waxing_gibbous_moon', ':waxing_gibbous_moon:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('wc', 'wc', ':wc:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('weary', 'weary', ':weary:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('wedding', 'wedding', ':wedding:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('whale', 'whale', ':whale:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('whale2', 'whale2', ':whale2:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('wheelchair', 'wheelchair', ':wheelchair:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('white_check_mark', 'white_check_mark', ':white_check_mark:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('white_circle', 'white_circle', ':white_circle:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('white_flower', 'white_flower', ':white_flower:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('white_large_square', 'white_large_square', ':white_large_square:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('white_medium_small_square', 'white_medium_small_square', ':white_medium_small_square:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('white_medium_square', 'white_medium_square', ':white_medium_square:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('white_small_square', 'white_small_square', ':white_small_square:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('white_square_button', 'white_square_button', ':white_square_button:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('wind_chime', 'wind_chime', ':wind_chime:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('wine_glass', 'wine_glass', ':wine_glass:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('wink', 'wink', ':wink:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('wolf', 'wolf', ':wolf:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('woman', 'woman', ':woman:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('womans_clothes', 'womans_clothes', ':womans_clothes:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('womans_hat', 'womans_hat', ':womans_hat:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('womens', 'womens', ':womens:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('worried', 'worried', ':worried:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('wrench', 'wrench', ':wrench:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('x', 'x', ':x:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('yellow_heart', 'yellow_heart', ':yellow_heart:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('yen', 'yen', ':yen:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('yum', 'yum', ':yum:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('zap', 'zap', ':zap:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('zero', 'zero', ':zero:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('zzz', 'zzz', ':zzz:')");

    //clanwars
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "clanwars` ADD `hltv` VARCHAR( 255 ) NOT NULL AFTER `server`");

    //polls
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "poll` ADD `intern` INT( 1 ) DEFAULT '0' NOT NULL");

    //games
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "games` CHANGE `name` `name` VARCHAR( 255 ) NOT NULL");

    //servers
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "servers` ADD `sort` INT DEFAULT '1' NOT NULL");

    //scrolltext
    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "scrolltext`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "scrolltext` (
  `text` longtext NOT NULL,
  `delay` int(11) NOT NULL default '100',
  `direction` varchar(255) NOT NULL default ''
) DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_unicode_ci");

    //superuser
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "user_groups` ADD `super` INT( 1 ) DEFAULT '0' NOT NULL");
    $transaction->addQuery("UPDATE `" . PREFIX . "user_groups` SET super='1' WHERE userID='1' ");

    //bannerrotation
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "bannerrotation` (
  `bannerID` int(11) NOT NULL AUTO_INCREMENT,
  `banner` varchar(255) NOT NULL default '',
  `bannername` varchar(255) NOT NULL default '',
  `bannerurl` varchar(255) NOT NULL default '',
  `displayed` varchar(255) NOT NULL default '',
  `hits` int(11) default '0',
  `date` int(11) NOT NULL default '0',
  PRIMARY KEY  (`bannerID`),
  UNIQUE KEY `banner` (`banner`)
  ) DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_unicode_ci");

    $transaction->addQuery("ALTER TABLE `" . PREFIX . "user` CHANGE `connection` `verbindung` VARCHAR( 255 ) NOT NULL DEFAULT ''");

    //converting clanwars-TABLE
    $clanwarQry = mysqli_query($_database, "SELECT * FROM " . PREFIX . "clanwars");
    $total = mysqli_num_rows($clanwarQry);
    if ($total) {
        while ($olddata = mysqli_fetch_array($clanwarQry)) {
            $id = $olddata['cwID'];
            $maps = $olddata['maps'];
            $scoreHome1 = $olddata['homescr1'];
            $scoreHome2 = $olddata['homescr2'];
            $scoreOpp1 = $olddata['oppscr1'];
            $scoreOpp2 = $olddata['oppscr2'];

            // do the convertation
            if (!empty($scoreHome2)) {
                $scoreHome = $scoreHome1 . '||' . $scoreHome2;
            } else {
                $scoreHome = $scoreHome1;
            }

            if (!empty($scoreOpp2)) {
                $scoreOpp = $scoreOpp1 . '||' . $scoreOpp2;
            } else {
                $scoreOpp = $scoreOpp1;
            }

            // update database, set new structure
            if (mysqli_query($_database, "ALTER TABLE `" . PREFIX . "clanwars` CHANGE `homescr1` `homescore` TEXT NOT NULL")) {
                $transaction->addQuery("ALTER TABLE `" . PREFIX . "clanwars` CHANGE `oppscr1` `oppscore` TEXT NOT NULL");
                if (mysqli_query($_database, "ALTER TABLE `" . PREFIX . "clanwars` DROP `homescr2`")) {
                    $transaction->addQuery("ALTER TABLE `" . PREFIX . "clanwars` DROP `oppscr2`");
                    // save converted data into the database
                    $transaction->addQuery("UPDATE " . PREFIX . "clanwars SET homescore='" . $scoreHome . "', oppscore='" . $scoreOpp . "', maps='" . $maps . "' WHERE cwID='" . $id . "'");
                }
            }
        }
    } else {
        $transaction->addQuery("ALTER TABLE `" . PREFIX . "clanwars` CHANGE `homescr1` `homescore` TEXT");
        $transaction->addQuery("ALTER TABLE `" . PREFIX . "clanwars` CHANGE `oppscr1` `oppscore` TEXT");
        $transaction->addQuery("ALTER TABLE `" . PREFIX . "clanwars` DROP `homescr2`");
        $transaction->addQuery("ALTER TABLE `" . PREFIX . "clanwars` DROP `oppscr2`");
    }

    if ($transaction->successful()) {
        return array('status' => 'success', 'message' => 'Updated to webSPELL 4 Beta 6 Part 2');
    } else {
        return array('status' => 'fail', 'message' => 'Failed to update to webSPELL 4 Beta 6 Part 2<br/>' . $transaction->getError());
    }
}

function update_40000_40100($_database)
{
    $transaction = new Transaction($_database);

    // FAQ
    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "faq`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "faq` (
  `faqID` INT(11) NOT NULL AUTO_INCREMENT,
  `faqcatID` INT(11) NOT NULL DEFAULT '0',
  `question` VARCHAR(255) NOT NULL DEFAULT '',
  `answer` VARCHAR(255) NOT NULL DEFAULT '',
  `sort` INT(11) NOT NULL DEFAULT '0',
  PRIMARY KEY  (`faqID`)
  ) AUTO_INCREMENT=1
  DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_unicode_ci");

    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "faq_categories`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "faq_categories` (
  `faqcatID` INT(11) NOT NULL AUTO_INCREMENT,
  `faqcatname` VARCHAR(255) NOT NULL DEFAULT '',
  `description` TEXT NOT NULL,
  `sort` INT(11) NOT NULL DEFAULT '0',
  PRIMARY KEY  (`faqcatID`)
  ) AUTO_INCREMENT=1
  DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_unicode_ci");

    // Admin Member Beschreibung
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "user` ADD `userdescription` TEXT NOT NULL");

    // Forum Sticky Function
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "forum_topics` ADD `sticky` INT(1) NOT NULL DEFAULT '0'");

    // birthday converter
    mysqli_query($_database, "ALTER TABLE `" . PREFIX . "user` ADD `birthday2` DATETIME NOT NULL AFTER `birthday`");
    $q = mysqli_query($_database, "SELECT userID, birthday FROM `" . PREFIX . "user`");
    while ($ds = mysqli_fetch_array($q)) {
        $transaction->addQuery("UPDATE `" . PREFIX . "user` SET birthday2='" . date("Y", $ds['birthday']) . "-" . date("m", $ds['birthday']) . "-" . date("d", $ds['birthday']) . "' WHERE userID='" . $ds['userID'] . "'");
    }
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "user` DROP `birthday`");
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "user` CHANGE `birthday2` `birthday` DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL");

    if ($transaction->successful()) {
        return array('status' => 'success', 'message' => 'Updated to webSPELL 4.1');
    } else {
        return array('status' => 'fail', 'message' => 'Failed to update to webSPELL 4.1<br/>' . $transaction->getError());
    }

}

function update_40100_40101($_database)
{
    $transaction = new Transaction($_database);
    //forum speedfix
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "forum_boards` ADD `topics` INT DEFAULT '0' NOT NULL");
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "forum_boards` ADD `posts` INT DEFAULT '0' NOT NULL");

    $q = mysqli_query($_database, "SELECT boardID FROM `" . PREFIX . "forum_boards`");
    while ($ds = mysqli_fetch_array($q)) {
        $topics = mysqli_num_rows(mysqli_query($_database, "SELECT topicID FROM `" . PREFIX . "forum_topics` WHERE boardID='" . $ds['boardID'] . "' AND moveID='0'"));
        $posts = mysqli_num_rows(mysqli_query($_database, "SELECT postID FROM `" . PREFIX . "forum_posts` WHERE boardID='" . $ds['boardID'] . "'"));
        if (($posts - $topics) < 0) $posts = 0;
        else $posts = $posts - $topics;
        $transaction->addQuery("UPDATE `" . PREFIX . "forum_boards` SET topics='" . $topics . "' , posts='" . $posts . "' WHERE boardID='" . $ds['boardID'] . "'");
    }

    //add captcha
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "captcha` (
  `hash` VARCHAR(255) NOT NULL DEFAULT '',
  `captcha` INT(11) NOT NULL DEFAULT '0',
  `deltime` INT(11) NOT NULL DEFAULT '0',
  PRIMARY KEY  (`hash`)
  ) DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_unicode_ci");

    //useractivation
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "user` ADD `activated` varchar(255) NOT NULL default '1'");

    //counter: max. online
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "counter` ADD `maxonline` INT NOT NULL");

    //faq
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "faq` CHANGE `answer` `answer` TEXT NOT NULL");

    if ($transaction->successful()) {
        return array('status' => 'success', 'message' => 'Updated to webSPELL 4.1.1');
    } else {
        return array('status' => 'fail', 'message' => 'Failed to update to webSPELL 4.1.1<br/>' . $transaction->getError());
    }
}

function update_40101_420_1($_database)
{
    $transaction = new Transaction($_database);

    //set default language
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "settings` ADD `default_language` VARCHAR( 2 ) DEFAULT 'uk' NOT NULL");

    //user groups
    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "user_forum_groups`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "user_forum_groups` (
  `usfgID` int(11) NOT NULL auto_increment,
  `userID` int(11) NOT NULL default '0',
  PRIMARY KEY  (`usfgID`)
  ) AUTO_INCREMENT=0
  DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_unicode_ci");

    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "forum_groups`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "forum_groups` (
  `fgrID` int(11) NOT NULL auto_increment,
  `name` varchar(32) NOT NULL default '0',
  PRIMARY KEY  (`fgrID`)
  ) AUTO_INCREMENT=0
  DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_unicode_ci");

    $transaction->addQuery("ALTER TABLE `" . PREFIX . "static` ADD `content` TEXT NOT NULL ");
    $get = mysqli_query($_database, "SELECT * FROM " . PREFIX . "static");
    while ($ds = mysqli_fetch_assoc($get)) {
        $file = "../html/" . $ds['name'];
        if (file_exists($file)) {
            $content = file_get_contents($file);
            if (get_magic_quotes_gpc()) {
                $content = stripslashes($content);
            }
            if (function_exists("mysqli_real_escape_string")) {
                $content = mysqli_real_escape_string($_database, $content);
            } else {
                $content = addslashes($content);
            }
            $transaction->addQuery("UPDATE " . PREFIX . "static SET content='" . $content . "' WHERE staticID='" . $ds['staticID'] . "'");
            @unlink($file);
        }
    }
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "squads` CHANGE `info` `info` TEXT  NOT NULL ");
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "forum_boards` ADD `writegrps` text NOT NULL AFTER `intern`");
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "forum_topics` ADD `writegrps` text NOT NULL AFTER `intern`");

    $transaction->addQuery("ALTER TABLE `" . PREFIX . "forum_announcements` ADD `readgrps` text NOT NULL AFTER `intern`");
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "forum_categories` ADD `readgrps` text NOT NULL AFTER `intern`");
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "forum_boards` ADD `readgrps` text NOT NULL AFTER `intern`");
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "forum_topics` ADD `readgrps` text NOT NULL AFTER `intern`");

    //add group 1 and convert intern to group 1
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "user_forum_groups` ADD `1` INT( 1 ) NOT NULL ;");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "forum_groups` ( `fgrID` , `name` ) VALUES ('1', 'Old intern board users');");

    $transaction->addQuery("UPDATE `" . PREFIX . "forum_announcements` SET `readgrps` = '1' WHERE `intern` = 1");
    $transaction->addQuery("UPDATE `" . PREFIX . "forum_categories` SET `readgrps` = '1' WHERE `intern` = 1");
    $transaction->addQuery("UPDATE `" . PREFIX . "forum_boards` SET `readgrps` = '1', `writegrps` = '1' WHERE `intern` = 1");
    $transaction->addQuery("UPDATE `" . PREFIX . "forum_topics` SET `readgrps` = '1', `writegrps` = '1' WHERE `intern` = 1");

    $transaction->addQuery("ALTER TABLE `" . PREFIX . "forum_announcements` DROP `intern`");
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "forum_categories` DROP `intern`");
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "forum_boards` DROP `intern`");
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "forum_topics` DROP `intern`");
    if ($transaction->successful()) {
        return array('status' => 'success', 'message' => 'Updated to webSPELL 4.2 Part 1');
    } else {
        return array('status' => 'fail', 'message' => 'Failed to update to webSPELL 4.2 Part 1<br/>' . $transaction->getError());
    }

}

function update_40101_420_2($_database)
{
    $transaction = new Transaction($_database);

    $sql = mysqli_query($_database, "SELECT `boardID` FROM `" . PREFIX . "forum_boards`");
    while ($ds = mysqli_fetch_array($sql)) {
        $anz_topics = mysqli_num_rows(mysqli_query($_database, "SELECT boardID FROM `" . PREFIX . "forum_topics` WHERE `boardID` = " . $ds['boardID']));
        $anz_posts = mysqli_num_rows(mysqli_query($_database, "SELECT boardID FROM `" . PREFIX . "forum_posts` WHERE `boardID` = " . $ds['boardID']));
        $anz_announcements = mysqli_num_rows(mysqli_query($_database, "SELECT boardID FROM `" . PREFIX . "forum_announcements` WHERE `boardID` = " . $ds['boardID']));
        $anz_topics = $anz_topics + $anz_announcements;
        $transaction->addQuery("UPDATE `" . PREFIX . "forum_boards` SET `topics` = '" . $anz_topics . "', `posts` = '" . $anz_posts . "' WHERE `boardID` = " . $ds['boardID']);
    }

    //add all internboards user to "Intern board user"
    $sql = mysqli_query($_database, "SELECT `userID` FROM `" . PREFIX . "user_groups` WHERE `internboards` = '1'");
    while ($ds = mysqli_fetch_array($sql)) {
        if (mysqli_num_rows(mysqli_query($_database, "SELECT userID FROM `" . PREFIX . "user_forum_groups` WHERE `userID`=" . $ds['userID']))) $transaction->addQuery("UPDATE `" . PREFIX . "user_forum_groups` SET `1`='1' WHERE `userID`='" . $ds['userID'] . "'");
        else $transaction->addQuery("INSERT INTO `" . PREFIX . "user_forum_groups` (`userID`, `1`) VALUES (" . $ds['userID'] . ", 1)");
    }
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "user_groups` DROP `internboards`");

    //add games cell to squads
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "squads` ADD `games` TEXT NOT NULL AFTER `gamesquad`");

    //add email_hide cell to user
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "user` ADD `email_hide` INT( 1 ) NOT NULL DEFAULT '1' AFTER `email`");
    $transaction->addQuery("UPDATE `" . PREFIX . "user` SET `email_hide` = '1' WHERE `email_hide` = '0'");

    //add userIDs cell to poll
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "poll` ADD `userIDs` TEXT NOT NULL");

    //add table for banned ips
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "banned_ips` (
                   `banID` int(11) NOT NULL auto_increment,
                   `ip` varchar(255) NOT NULL,
                   `deltime` int(15) NOT NULL,
                   `reason` varchar(255) NULL,
                   PRIMARY KEY  (`banID`)
                 ) DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_unicode_ci");

    //add table for wrong logins
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "failed_login_attempts` (
                              `ip` varchar(255) NOT NULL default '',
                              `wrong` int(2) default '0',
                              PRIMARY KEY  (`ip`)
                            ) DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_unicode_ci");

    //news multilanguage
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "news_contents` (
    `newsID` INT NOT NULL ,
    `language` VARCHAR( 2 ) NOT NULL ,
    `headline` VARCHAR( 255 ) NOT NULL ,
    `content` TEXT NOT NULL
    ) DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_unicode_ci");
    if ($transaction->successful()) {
        return array('status' => 'success', 'message' => 'Updated to webSPELL 4.2 Part 2');
    } else {
        return array('status' => 'fail', 'message' => 'Failed to update to webSPELL 4.2 Part 2<br/>' . $transaction->getError());
    }

}

function update_40101_420_3($_database)
{
    $transaction = new Transaction($_database);

    //news converter
    $q = mysqli_query($_database, "SELECT newsID, lang1, lang2, headline1, headline2, content1, content2 FROM `" . PREFIX . "news`");
    while ($ds = mysqli_fetch_array($q)) {
        if ($ds['headline1'] != "" or $ds['content1'] != "") {
            if (get_magic_quotes_gpc()) $content1 = str_replace('\r\n', "\n", $ds['content1']);
            else $content1 = str_replace('\r\n', "\n", mysqli_real_escape_string($_database, $ds['content1']));
            $transaction->addQuery("INSERT INTO " . PREFIX . "news_contents (newsID, language, headline, content) VALUES ('" . $ds['newsID'] . "', '" . mysqli_real_escape_string($_database, $ds['lang1']) . "', '" . mysqli_real_escape_string($_database, $ds['headline1']) . "', '" . $content1 . "')");
        }
        if ($ds['headline2'] != "" or $ds['content2'] != "") {
            if (get_magic_quotes_gpc()) $content2 = str_replace('\r\n', "\n", $ds['content2']);
            else $content2 = str_replace('\r\n', "\n", mysqli_real_escape_string($_database, $ds['content2']));
            $transaction->addQuery("INSERT INTO " . PREFIX . "news_contents (newsID, language, headline, content) VALUES ('" . $ds['newsID'] . "', '" . mysqli_real_escape_string($_database, $ds['lang2']) . "', '" . mysqli_real_escape_string($_database, $ds['headline2']) . "', '" . $content2 . "')");
        }
    }

    $transaction->addQuery("ALTER TABLE `" . PREFIX . "news` DROP `lang1`");
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "news` DROP `headline1`");
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "news` DROP `content1`");
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "news` DROP `lang2`");
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "news` DROP `headline2`");
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "news` DROP `content2`");
    if ($transaction->successful()) {
        return array('status' => 'success', 'message' => 'Updated to webSPELL 4.2 Part 3');
    } else {
        return array('status' => 'fail', 'message' => 'Failed to update to webSPELL 4.2 Part 3<br/>' . $transaction->getError());
    }

}

function update_40101_420_4($_database)
{
    $transaction = new Transaction($_database);
    //article multipage
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "articles_contents` (
      `articlesID` INT( 11 ) NOT NULL ,
      `content` TEXT NOT NULL ,
      `page` INT( 2 ) NOT NULL
    ) DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_unicode_ci");

    //article converter
    $sql = mysqli_query($_database, "SELECT articlesID, content FROM " . PREFIX . "articles");
    while ($ds = mysqli_fetch_array($sql)) {
        if (get_magic_quotes_gpc()) {
            $content = str_replace('\r\n', "\n", $ds['content']);
        } else {
            $content = str_replace('\r\n', "\n", mysqli_real_escape_string($_database, $ds['content']));
        }
        $transaction->addQuery("INSERT INTO " . PREFIX . "articles_contents (articlesID, content, page) VALUES ('" . $ds['articlesID'] . "', '" . $content . "', '0')");
    }

    $transaction->addQuery("ALTER TABLE `" . PREFIX . "user` ADD `language` VARCHAR( 2 ) NOT NULL");

    //add news writer right column
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "user_groups` ADD `news_writer` INT( 1 ) NOT NULL AFTER `news`");

    //add sub cat column
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "files_categorys` ADD `subcatID` INT( 11 ) NOT NULL DEFAULT '0'");
    if ($transaction->successful()) {
        return array('status' => 'success', 'message' => 'Updated to webSPELL 4.2 Part 4');
    } else {
        return array('status' => 'fail', 'message' => 'Failed to update to webSPELL 4.2 Part 4<br/>' . $transaction->getError());
    }

}

function update_40101_420_5($_database)
{
    $transaction = new Transaction($_database);
    //announcement converter
    $sql = mysqli_query($_database, "SELECT * FROM " . PREFIX . "forum_announcements");
    while ($ds = mysqli_fetch_assoc($sql)) {
        $ds['topic'] = mysqli_real_escape_string($_database, $ds['topic']);
        $ds['announcement'] = mysqli_real_escape_string($_database, $ds['announcement']);
        $sql_board = mysqli_query($_database, "SELECT readgrps, writegrps
								FROM " . PREFIX . "forum_boards
								WHERE boardID = '" . $ds['boardID'] . "'");
        $rules = mysqli_fetch_assoc($sql_board);
        $transaction->addQuery("INSERT INTO " . PREFIX . "forum_topics
				( boardID, readgrps, writegrps, userID, date, lastdate, topic, lastposter, sticky)
				VALUES
				('" . $ds['boardID'] . "', '" . $rules['readgrps'] . "', '" . $rules['writegrps'] . "', '" . $ds['userID'] . "', '" . $ds['date'] . "', '" . $ds['date'] . "', '" . $ds['topic'] . "', '" . $ds['userID'] . "', '1')");
        $annID = mysqli_insert_id($_database);
        $transaction->addQuery("INSERT INTO " . PREFIX . "forum_posts
				( boardID, topicID, date, poster, message)
				VALUES
				( '" . $ds['boardID'] . "', '" . $annID . "', '" . $ds['date'] . "', '" . $ds['userID'] . "', '" . $ds['announcement'] . "')");
        $transaction->addQuery("UPDATE " . PREFIX . "forum_boards
					SET topics=topics+1
					WHERE boardID = '" . $ds['boardID'] . "' ");
        $transaction->addQuery("DELETE FROM " . PREFIX . "forum_announcements
					WHERE announceID='" . $ds['announceID'] . "' ");
    }

    // clanwar converter
    $get = mysqli_query($_database, "SELECT cwID, maps, hometeam, homescore, oppscore FROM " . PREFIX . "clanwars");
    while ($ds = mysqli_fetch_assoc($get)) {
        $maps = explode("||", $ds['maps']);
        if (function_exists("mysqli_real_escape_string")) {
            $theMaps = mysqli_real_escape_string($_database, serialize($maps));
        } else {
            $theMaps = addslashes(serialize($maps));
        }
        $hometeam = serialize(explode("|", $ds['hometeam']));
        $homescore = serialize(explode("||", $ds['homescore']));
        $oppscore = serialize(explode("||", $ds['oppscore']));
        $cwID = $ds['cwID'];
        $transaction->addQuery("UPDATE " . PREFIX . "clanwars SET maps='" . $theMaps . "', hometeam='" . $hometeam . "', homescore='" . $homescore . "', oppscore='" . $oppscore . "' WHERE cwID='" . $cwID . "'");
    }
    if ($transaction->successful()) {
        return array('status' => 'success', 'message' => 'Updated to webSPELL 4.2 Part 5');
    } else {
        return array('status' => 'fail', 'message' => 'Failed to update to webSPELL 4.2 Part 5<br/>' . $transaction->getError());
    }

}

function update_40101_420_6($_database)
{
    $transaction = new Transaction($_database);

    // converter board-speedup :)
    $transaction->addQuery("UPDATE " . PREFIX . "user SET topics='|'");
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "user` CHANGE `topics` `topics` TEXT NOT NULL");

    // update for email-change-activation
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "user` ADD `email_change` VARCHAR(255) NOT NULL AFTER `email_hide`,
				ADD `email_activate` VARCHAR(255) NOT NULL AFTER `email_change`");

    //add insertlinks cell to settings
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "settings` ADD `insertlinks` INT( 1 ) NOT NULL DEFAULT '1' AFTER `default_language`");

    //add search string min len and max wrong password cell to settings
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "settings` ADD `search_min_len` INT( 3 ) NOT NULL DEFAULT '3' AFTER `insertlinks`");
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "settings` ADD `max_wrong_pw` INT( 2 ) NOT NULL DEFAULT '10' AFTER `search_min_len`");

    //set default sex to u(nknown)
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "user` CHANGE `sex` `sex` CHAR( 1 ) NOT NULL DEFAULT 'u' ");

    // convert banned to varchar
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "user` CHANGE `banned` `banned` VARCHAR(255) NULL DEFAULT NULL ");
    $transaction->addQuery("UPDATE `" . PREFIX . "user` SET banned='perm' WHERE banned='1'");
    $transaction->addQuery("UPDATE `" . PREFIX . "user` SET banned=(NULL) WHERE banned='0'");
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "user` ADD `ban_reason` VARCHAR(255) NOT NULL AFTER `banned`");

    $transaction->addQuery("ALTER TABLE `" . PREFIX . "settings` DROP `hideboards`");

    //add lastpostID to topics for latesttopics
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "forum_topics` ADD `lastpostID` INT NOT NULL DEFAULT '0' AFTER `lastposter`");

    //add color parameter for scrolltext
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "scrolltext` ADD `color` VARCHAR(7) NOT NULL DEFAULT '#000000'");

    //add new games
    $transaction->addQuery("UPDATE `" . PREFIX . "games` SET `name` = 'Battlefield 1942' WHERE `name` = 'Battlefield'");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "games` ( `gameID` , `tag` , `name` )
		VALUES
			('', 'aa', 'Americas Army'),
			('', 'aoe', 'Age of Empires 3'),
			('', 'b21', 'Battlefield 2142'),
			('', 'bf2', 'Battlefield 2'),
			('', 'bfv', 'Battlefield Vietnam'),
			('', 'c3d', 'Carom 3D'),
			('', 'cc3', 'Command &amp; Conquer'),
			('', 'cd2', 'Call of Duty 2'),
			('', 'cd4', 'Call of Duty 4'),
			('', 'cod', 'Call of Duty'),
			('', 'coh', 'Company of Heroes'),
			('', 'crw', 'Crysis Wars'),
			('', 'cry', 'Crysis'),
			('', 'css', 'Counter-Strike: Source'),
			('', 'cz', 'Counter-Strike: Condition Zero'),
			('', 'dds', 'Day of Defeat: Source'),
			('', 'dod', 'Day of Defeat'),
			('', 'dow', 'Dawn of War'),
			('', 'dta', 'DotA'),
			('', 'et', 'Enemy Territory'),
			('', 'fc', 'FarCry'),
			('', 'fer', 'F.E.A.R.'),
			('', 'fif', 'FIFA'),
			('', 'fl', 'Frontlines: Fuel of War'),
			('', 'hal', 'HALO'),
			('', 'jk2', 'Jedi Knight 2'),
			('', 'jk3', 'Jedi Knight 3'),
			('', 'lfs', 'Live for Speed'),
			('', 'lr2', 'LotR: Battle for Middle Earth 2'),
			('', 'lr', 'LotR: Battle for Middle Earth'),
			('', 'moh', 'Medal of Hornor'),
			('', 'nfs', 'Need for Speed'),
			('', 'pes', 'Pro Evolution Soccer'),
			('', 'q3', 'Quake 3'),
			('', 'q4', 'Quake 4'),
			('', 'ql', 'Quakelive'),
			('', 'rdg', 'Race Driver Grid'),
			('', 'sc2', 'Starcraft 2'),
			('', 'sc', 'Starcraft'),
			('', 'sof', 'Soldier of Fortune 2'),
			('', 'sw2', 'Star Wars: Battlefront 2'),
			('', 'sw', 'Star Wars: Battlefront'),
			('', 'swa', 'SWAT 4'),
			('', 'tf2', 'Team Fortress 2'),
			('', 'tf', 'Team Fortress'),
			('', 'tm', 'TrackMania'),
			('', 'ut3', 'Unreal Tournament 3'),
			('', 'ut4', 'Unreal Tournament 2004'),
			('', 'war', 'War Rock'),
			('', 'wic', 'World in Conflict'),
			('', 'wow', 'World of Warcraft'),
			('', 'wrs', 'Warsow')");

    //add new countries
    $transaction->addQuery("INSERT INTO `" . PREFIX . "countries` ( `countryID` , `country` , `short` )
		VALUES
			('', 'Albania', 'al'),
			('', 'Algeria', 'dz'),
			('', 'American Samoa', 'as'),
			('', 'Andorra', 'ad'),
			('', 'Angola', 'ao'),
			('', 'Anguilla', 'ai'),
			('', 'Antarctica', 'aq'),
			('', 'Antigua and Barbuda', 'ag'),
			('', 'Armenia', 'am'),
			('', 'Aruba', 'aw'),
			('', 'Azerbaijan', 'az'),
			('', 'Bahamas', 'bz'),
			('', 'Bahrain', 'bh'),
			('', 'Bangladesh', 'bd'),
			('', 'Barbados', 'bb'),
			('', 'Belarus', 'by'),
			('', 'Benelux', 'bx'),
			('', 'Benin', 'bj'),
			('', 'Bermuda', 'bm'),
			('', 'Bhutan', 'bt'),
			('', 'Bolivia', 'bo'),
			('', 'Botswana', 'bw'),
			('', 'Bouvet Island', 'bv'),
			('', 'British Indian Ocean Territory', 'io'),
			('', 'Brunei Darussalam', 'bn'),
			('', 'Burkina Faso', 'bf'),
			('', 'Burundi', 'bi'),
			('', 'Cambodia', 'kh'),
			('', 'Cameroon', 'cm'),
			('', 'Cape Verde', 'cv'),
			('', 'Cayman Islands', 'ky'),
			('', 'Central African Republic', 'cf'),
			('', 'Christmas Island', 'cx'),
			('', 'Cocos Islands', 'cc'),
			('', 'Comoros', 'km'),
			('', 'Congo', 'cg'),
			('', 'Cook Islands', 'ck'),
			('', 'Costa Rica', 'cr'),
			('', 'Cote d\'Ivoire', 'ci'),
			('', 'Cuba', 'cu'),
			('', 'Democratic Congo', 'cd'),
			('', 'Democratic Korea', 'kp'),
			('', 'Djibouti', 'dj'),
			('', 'Dominica', 'dm'),
			('', 'Dominican Republic', 'do'),
			('', 'East Timor', 'tp'),
			('', 'Ecuador', 'ec'),
			('', 'Egypt', 'eg'),
			('', 'El Salvador', 'sv'),
			('', 'England', 'en'),
			('', 'Eritrea', 'er'),
			('', 'Ethiopia', 'et'),
			('', 'Falkland Islands', 'fk'),
			('', 'Fiji', 'fj'),
			('', 'French Polynesia', 'pf'),
			('', 'French Southern Territories', 'tf'),
			('', 'Gabon', 'ga'),
			('', 'Gambia', 'gm'),
			('', 'Georgia', 'ge'),
			('', 'Ghana', 'gh'),
			('', 'Gibraltar', 'gi'),
			('', 'Greenland', 'gl'),
			('', 'Grenada', 'gd'),
			('', 'Guadeloupe', 'gp'),
			('', 'Guam', 'gu'),
			('', 'Guatemala', 'gt'),
			('', 'Guinea', 'gn'),
			('', 'Guinea-Bissau', 'gw'),
			('', 'Guyana', 'gy'),
			('', 'Haiti', 'ht'),
			('', 'Heard Islands', 'hm'),
			('', 'Holy See', 'va'),
			('', 'Honduras', 'hn'),
			('', 'Hong Kong', 'hk'),
			('', 'India', 'in'),
			('', 'Indonesia', 'id'),
			('', 'Iran', 'ir'),
			('', 'Iraq', 'iq'),
			('', 'Jamaica', 'jm'),
			('', 'Jordan', 'jo'),
			('', 'Kazakhstan', 'kz'),
			('', 'Kenia', 'ke'),
			('', 'Kiribati', 'ki'),
			('', 'Kuwait', 'kw'),
			('', 'Kyrgyzstan', 'kg'),
			('', 'Lao People\'s', 'la'),
			('', 'Lebanon', 'lb'),
			('', 'Lesotho', 'ls'),
			('', 'Liberia', 'lr'),
			('', 'Libyan Arab Jamahiriya', 'ly'),
			('', 'Liechtenstein', 'li'),
			('', 'Macau', 'mo'),
			('', 'Macedonia', 'mk'),
			('', 'Madagascar', 'mg'),
			('', 'Malawi', 'mw'),
			('', 'Maldives', 'mv'),
			('', 'Mali', 'ml'),
			('', 'Marshall Islands', 'mh'),
			('', 'Mauritania', 'mr'),
			('', 'Mauritius', 'mu'),
			('', 'Micronesia', 'fm'),
			('', 'Moldova', 'md'),
			('', 'Monaco', 'mc'),
			('', 'Montserrat', 'ms'),
			('', 'Morocco', 'ma'),
			('', 'Mozambique', 'mz'),
			('', 'Myanmar', 'mm'),
			('', 'Namibia', 'nb'),
			('', 'Nauru', 'nr'),
			('', 'Nepal', 'np'),
			('', 'Netherlands Antilles', 'an'),
			('', 'New Caledonia', 'nc'),
			('', 'Nicaragua', 'ni'),
			('', 'Nigeria', 'ng'),
			('', 'Niue', 'nu'),
			('', 'Norfolk Island', 'nf'),
			('', 'Northern Ireland', 'nx'),
			('', 'Northern Mariana Islands', 'mp'),
			('', 'Oman', 'om'),
			('', 'Pakistan', 'pk'),
			('', 'Palau', 'pw'),
			('', 'Palestinian', 'ps'),
			('', 'Panama', 'pa'),
			('', 'Papua New Guinea', 'pg'),
			('', 'Paraguay', 'py'),
			('', 'Peru', 'pe'),
			('', 'Philippines', 'ph'),
			('', 'Pitcairn', 'pn'),
			('', 'Puerto Rico', 'pr'),
			('', 'Qatar', 'qa'),
			('', 'Reunion', 're'),
			('', 'Rwanda', 'rw'),
			('', 'Saint Helena', 'sh'),
			('', 'Saint Kitts and Nevis', 'kn'),
			('', 'Saint Lucia', 'lc'),
			('', 'Saint Pierre and Miquelon', 'pm'),
			('', 'Saint Vincent', 'vc'),
			('', 'Samoa', 'ws'),
			('', 'San Marino', 'sm'),
			('', 'Sao Tome and Principe', 'st'),
			('', 'Saudi Arabia', 'sa'),
			('', 'Scotland', 'sc'),
			('', 'Senegal', 'sn'),
			('', 'Sierra Leone', 'sl'),
			('', 'Solomon Islands', 'sb'),
			('', 'Somalia', 'so'),
			('', 'South Georgia', 'gs'),
			('', 'Sri Lanka', 'lk'),
			('', 'Sudan', 'sd'),
			('', 'Suriname', 'sr'),
			('', 'Svalbard and Jan Mayen', 'sj'),
			('', 'Swaziland', 'sz'),
			('', 'Tajikistan', 'tj'),
			('', 'Tanzania', 'tz'),
			('', 'Thailand', 'th'),
			('', 'Togo', 'tg'),
			('', 'Tokelau', 'tk'),
			('', 'Tonga', 'to'),
			('', 'Trinidad and Tobago', 'tt'),
			('', 'Turkmenistan', 'tm'),
			('', 'Turks_and Caicos Islands', 'tc'),
			('', 'Tuvalu', 'tv'),
			('', 'Uganda', 'ug'),
			('', 'United Arab Emirates', 'ae'),
			('', 'Uruguay', 'uy'),
			('', 'Uzbekistan', 'uz'),
			('', 'Vanuatu', 'vu'),
			('', 'Vietnam', 'vn'),
			('', 'Virgin Islands (British)', 'vg'),
			('', 'Virgin Islands (USA)', 'vi'),
			('', 'Wales', 'wa'),
			('', 'Wallis and Futuna', 'wf'),
			('', 'Western Sahara', 'eh'),
			('', 'Yemen', 'ye'),
			('', 'Zambia', 'zm'),
			('', 'Zimbabwe', 'zw')");

    
    //add sponsors click counter, small banner, mainsponsor option, sort and display choice
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "sponsors` ADD `banner_small` varchar(255) NOT NULL default '', ADD `displayed` varchar(255) NOT NULL default '1', ADD `mainsponsor` varchar(255) NOT NULL default '0', ADD `hits` int(11) default '0', ADD `date` int(14) NOT NULL default '0', ADD `sort` int(11) NOT NULL default '1' AFTER `banner`");
    $transaction->addQuery("UPDATE `" . PREFIX . "sponsors` SET `date` = '" . time() . "' WHERE `date` = '0'");

    //add parnters click counter and display choice
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "partners` ADD `displayed` varchar(255) NOT NULL default '1', ADD `hits` int(11) default '0', ADD `date` int(14) NOT NULL default '0' AFTER `banner`");
    $transaction->addQuery("UPDATE `" . PREFIX . "partners` SET `date` = '" . time() . "' WHERE `date` = '0'");

    //add latesttopicchars to settings
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "settings` ADD `latesttopicchars` int(11) NOT NULL default '0' AFTER `latesttopics`");
    $transaction->addQuery("UPDATE `" . PREFIX . "settings` SET `latesttopicchars` = '18' WHERE `latesttopicchars` = '0'");

    //add maxtopnewschars to settings
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "settings` ADD `topnewschars` int(11) NOT NULL default '0' AFTER `headlineschars`");
    $transaction->addQuery("UPDATE `" . PREFIX . "settings` SET `topnewschars` = '200' WHERE `topnewschars` = '0'");

    //add captcha and bancheck to settings
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "settings` ADD `captcha_math` int(1) NOT NULL default '2' AFTER `max_wrong_pw`");
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "settings` ADD `captcha_bgcol` varchar(7) NOT NULL default '#FFFFFF' AFTER `captcha_math`");
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "settings` ADD `captcha_fontcol` varchar(7) NOT NULL default '#000000' AFTER `captcha_bgcol`");
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "settings` ADD `captcha_type` int(1) NOT NULL default '2' AFTER `captcha_fontcol`");
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "settings` ADD `captcha_noise` int(3) NOT NULL default '100' AFTER `captcha_type`");
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "settings` ADD `captcha_linenoise` int(2) NOT NULL default '10' AFTER `captcha_noise`");
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "settings` ADD `bancheck` INT( 13 ) NOT NULL");

    //add small icon to squads
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "squads` ADD `icon_small` varchar(255) NOT NULL default '' AFTER `icon`");

    // add autoresize to settings
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "settings` ADD `autoresize` int(1) NOT NULL default '1' AFTER `captcha_linenoise`");

    // add contacts for mail formular
    $getadminmail = mysqli_fetch_array(mysqli_query($_database, "SELECT adminemail FROM `" . PREFIX . "settings`"));
    $adminmail = $getadminmail['adminemail'];

    $transaction->addQuery("CREATE TABLE IF NOT EXISTS `" . PREFIX . "contact` (
      `contactID` int(11) NOT NULL auto_increment,
      `name` varchar(100) NOT NULL,
      `email` varchar(200) NOT NULL,
      `sort` int(11) NOT NULL default '0',
        PRIMARY KEY ( `contactID` )
      ) AUTO_INCREMENT=2
       DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_unicode_ci");

    $transaction->addQuery("INSERT INTO `" . PREFIX . "contact` (`contactID`, `name`, `email`, `sort`) VALUES
	  (1, 'Administrator', '" . $adminmail . "', 1);");

    // add date to faqs
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "faq` ADD `date` int(14) NOT NULL default '0' AFTER `faqcatID`");
    $transaction->addQuery("UPDATE `" . PREFIX . "faq` SET `date` = '" . time() . "' WHERE `date` = '0'");

    // remove nickname from who is/was online
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "whoisonline` DROP `nickname`");
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "whowasonline` DROP `nickname`");

    // set default to none in user table
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "user` CHANGE `clantag` `clantag` varchar(255) NOT NULL default ''");
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "user` CHANGE `clanname` `clanname` varchar(255) NOT NULL default ''");
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "user` CHANGE `clanirc` `clanirc` varchar(255) NOT NULL default ''");
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "user` CHANGE `clanhistory` `clanhistory` varchar(255) NOT NULL default ''");
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "user` CHANGE `cpu` `cpu` varchar(255) NOT NULL default ''");
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "user` CHANGE `mainboard` `mainboard` varchar(255) NOT NULL default ''");
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "user` CHANGE `ram` `ram` varchar(255) NOT NULL default ''");
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "user` CHANGE `monitor` `monitor` varchar(255) NOT NULL default ''");
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "user` CHANGE `graphiccard` `graphiccard` varchar(255) NOT NULL default ''");
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "user` CHANGE `soundcard` `soundcard` varchar(255) NOT NULL default ''");
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "user` CHANGE `keyboard` `keyboard` varchar(255) NOT NULL default ''");
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "user` CHANGE `mouse` `mouse` varchar(255) NOT NULL default ''");
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "user` CHANGE `mousepad` `mousepad` varchar(255) NOT NULL default ''");
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "user` CHANGE `verbindung` `verbindung` VARCHAR( 255 ) NOT NULL default ''");

    $transaction->addQuery("UPDATE `" . PREFIX . "user` SET `clantag` = '' WHERE `clantag` = 'n/a'");
    $transaction->addQuery("UPDATE `" . PREFIX . "user` SET `clanname` = '' WHERE `clanname` = 'n/a'");
    $transaction->addQuery("UPDATE `" . PREFIX . "user` SET `clanirc` = '' WHERE `clanirc` = 'n/a'");
    $transaction->addQuery("UPDATE `" . PREFIX . "user` SET `clanhistory` = '' WHERE `clanhistory` = 'n/a'");
    $transaction->addQuery("UPDATE `" . PREFIX . "user` SET `cpu` = '' WHERE `cpu` = 'n/a'");
    $transaction->addQuery("UPDATE `" . PREFIX . "user` SET `mainboard` = '' WHERE `mainboard` = 'n/a'");
    $transaction->addQuery("UPDATE `" . PREFIX . "user` SET `ram` = '' WHERE `ram` = 'n/a'");
    $transaction->addQuery("UPDATE `" . PREFIX . "user` SET `monitor` = '' WHERE `monitor` = 'n/a'");
    $transaction->addQuery("UPDATE `" . PREFIX . "user` SET `graphiccard` = '' WHERE `graphiccard` = 'n/a'");
    $transaction->addQuery("UPDATE `" . PREFIX . "user` SET `soundcard` = '' WHERE `soundcard` = 'n/a'");
    $transaction->addQuery("UPDATE `" . PREFIX . "user` SET `verbindung` = '' WHERE `verbindung` = 'n/a'");
    $transaction->addQuery("UPDATE `" . PREFIX . "user` SET `keyboard` = '' WHERE `keyboard` = 'n/a'");
    $transaction->addQuery("UPDATE `" . PREFIX . "user` SET `mouse` = '' WHERE `mouse` = 'n/a'");
    $transaction->addQuery("UPDATE `" . PREFIX . "user` SET `mousepad` = '' WHERE `mousepad` = 'n/a'");
    $transaction->addQuery("UPDATE `" . PREFIX . "user` SET `verbindung` = '' WHERE `verbindung` = 'n/a'");

    if ($transaction->successful()) {
        return array('status' => 'success', 'message' => 'Updated to webSPELL 4.2 Part 6');
    } else {
        return array('status' => 'fail', 'message' => 'Failed to update to webSPELL 4.2 Part 6<br/>' . $transaction->getError());
    }

}

function update_40101_420_7($_database)
{
    $transaction = new Transaction($_database);
    //Reverter of wrong escapes
    if (get_magic_quotes_gpc()) {
        @ini_set("max_execution_time", "300");
        @set_time_limit(300);

        // Fix About Us
        $get = mysqli_query($_database, "SELECT about FROM " . PREFIX . "about");
        if (mysqli_num_rows($get)) {
            $ds = mysqli_fetch_assoc($get);
            $transaction->addQuery("UPDATE " . PREFIX . "about SET about='" . $ds['about'] . "'");
        }

        // Fix History
        $get = mysqli_query($_database, "SELECT history FROM " . PREFIX . "history");
        if (mysqli_num_rows($get)) {
            $ds = mysqli_fetch_assoc($get);
            $transaction->addQuery("UPDATE " . PREFIX . "history SET history='" . $ds['history'] . "'");
        }

        // Fix Comments
        $get = mysqli_query($_database, "SELECT commentID, nickname, comment, url, email FROM " . PREFIX . "comments");
        while ($ds = mysqli_fetch_assoc($get)) {
            $transaction->addQuery("UPDATE " . PREFIX . "comments SET 	nickname='" . $ds['nickname'] . "',
															comment='" . $ds['comment'] . "',
															url='" . $ds['url'] . "',
															email='" . $ds['email'] . "'
															WHERE commentID='" . $ds['commentID'] . "'");
        }
    }
    if ($transaction->successful()) {
        return array('status' => 'success', 'message' => 'Updated to webSPELL 4.2 Part 7');
    } else {
        return array('status' => 'fail', 'message' => 'Failed to update to webSPELL 4.2 Part 7<br/>' . $transaction->getError());
    }

}

function update_40101_420_8($_database)
{
    $transaction = new Transaction($_database);

    // Fix Articles
    $get = mysqli_query($_database, "SELECT articlesID, title, url1, url2, url3, url4 FROM " . PREFIX . "articles");
    while ($ds = mysqli_fetch_assoc($get)) {
        $title = $ds['title'];
        $url1 = $ds['url1'];
        $url2 = $ds['url2'];
        $url3 = $ds['url3'];
        $url4 = $ds['url4'];
        $transaction->addQuery("UPDATE " . PREFIX . "articles SET title='" . $title . "', url1='" . $url1 . "', url2='" . $url2 . "', url3='" . $url3 . "', url4='" . $url4 . "' WHERE articlesID='" . $ds['articlesID'] . "'");
    }

    // Fix Profiles
    $get = mysqli_query($_database, "SELECT  userID, nickname, email, firstname, lastname, sex, country, town,
									birthday, icq, usertext, clantag, clanname, clanhp,
									clanirc, clanhistory, cpu, mainboard, ram, monitor,
									graphiccard, soundcard, verbindung, keyboard, mouse,
									mousepad, mailonpm, newsletter, homepage, about FROM " . PREFIX . "user");
    while ($ds = mysqli_fetch_assoc($get)) {
        $id = $ds['userID'];
        unset($ds['userID']);
        $string = '';
        foreach ($ds as $key => $value) {
            $string .= $key . "='" . $value . "', ";
        }
        $set = substr($string, 0, -2);
        $transaction->addQuery("UPDATE " . PREFIX . "user SET " . $set . " WHERE userID='" . $id . "'");
    }

    @ini_set("max_execution_time", "300");
    @set_time_limit(300);

    // Fix Userguestbook
    $get = mysqli_query($_database, "SELECT gbID, name, email, hp, comment FROM " . PREFIX . "user_gbook");
    while ($ds = mysqli_fetch_assoc($get)) {
        $transaction->addQuery("UPDATE " . PREFIX . "user_gbook SET name='" . $ds['name'] . "',
															comment='" . $ds['comment'] . "',
															hp='" . $ds['hp'] . "',
															email='" . $ds['email'] . "'
															WHERE gbID='" . $ds['gbID'] . "'");
    }

    // Fix Messenges
    $get = mysqli_query($_database, "SELECT messageID, message FROM " . PREFIX . "messenger");
    while ($ds = mysqli_fetch_assoc($get)) {
        $transaction->addQuery("UPDATE " . PREFIX . "messenger SET message='" . $ds['message'] . "' WHERE messageID='" . $ds['messageID'] . "'");
    }
    if ($transaction->successful()) {
        return array('status' => 'success', 'message' => 'Updated to webSPELL 4.2 Part 8');
    } else {
        return array('status' => 'fail', 'message' => 'Failed to update to webSPELL 4.2 Part 8<br/>' . $transaction->getError());
    }

}

function update_40101_420_9($_database)
{
    $transaction = new Transaction($_database);

    @ini_set("max_execution_time", "300");
    @set_time_limit(300);

    // Fix Forum
    $get = mysqli_query($_database, "SELECT topicID, topic FROM " . PREFIX . "forum_topics");
    while ($ds = mysqli_fetch_assoc($get)) {
        $transaction->addQuery("UPDATE " . PREFIX . "forum_topics SET topic='" . $ds['topic'] . "' WHERE topicID='" . $ds['topicID'] . "'");
    }

    @ini_set("max_execution_time", "300");
    @set_time_limit(300);

    $get = mysqli_query($_database, "SELECT postID, message FROM " . PREFIX . "forum_posts");
    while ($ds = mysqli_fetch_assoc($get)) {
        $transaction->addQuery("UPDATE " . PREFIX . "forum_posts SET message='" . $ds['message'] . "' WHERE postID='" . $ds['postID'] . "'");
    }

    if ($transaction->successful()) {
        return array('status' => 'success', 'message' => 'Updated to webSPELL 4.2 Part 9');
    } else {
        return array('status' => 'fail', 'message' => 'Failed to update to webSPELL 4.2 Part 9<br/>' . $transaction->getError());
    }
}

function update_420_430_1($_database)
{
    $transaction = new Transaction($_database);

    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "forum_posts_spam`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "forum_posts_spam` (
    `postID` int(11) NOT NULL AUTO_INCREMENT,
    `boardID` int(11) NOT NULL default '0',
    `topicID` int(11) NOT NULL default '0',
    `date` int(14) NOT NULL default '0',
    `poster` int(11) NOT NULL default '0',
    `message` text NOT NULL,
    `rating` varchar(255) NOT NULL default '',
    PRIMARY KEY (`postID`)
    ) AUTO_INCREMENT=1
     DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_unicode_ci");

    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "forum_topics_spam`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "forum_topics_spam` (
    `topicID` int(11) NOT NULL AUTO_INCREMENT,
    `boardID` int(11) NOT NULL,
    `userID` int(11) NOT NULL,
    `date` int(14) NOT NULL,
    `icon` varchar(255) NOT NULL,
    `topic` varchar(255) NOT NULL,
    `sticky` int(1) NOT NULL,
    `message` text NOT NULL,
    `rating` varchar(255) NOT NULL DEFAULT '',
    PRIMARY KEY (`topicID`)
    ) AUTO_INCREMENT=1
     DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_unicode_ci");

    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "comments_spam`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "comments_spam` (
    `commentID` int(11) NOT NULL AUTO_INCREMENT,
    `parentID` int(11) NOT NULL DEFAULT '0',
    `type` char(2) NOT NULL DEFAULT '',
    `userID` int(11) NOT NULL DEFAULT '0',
    `nickname` varchar(255) NOT NULL DEFAULT '',
    `date` int(14) NOT NULL DEFAULT '0',
    `comment` text NOT NULL,
    `url` varchar(255) NOT NULL DEFAULT '',
    `email` varchar(255) NOT NULL DEFAULT '',
    `ip` varchar(255) NOT NULL DEFAULT '',
    `rating` varchar(255) NOT NULL DEFAULT '',
    PRIMARY KEY (`commentID`)
    ) AUTO_INCREMENT=1
     DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_unicode_ci");
	 
	$transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "api_log`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "api_log` (
    `date` int(11) NOT NULL,
    `message` varchar(255) NOT NULL,
    `data` text NOT NULL
    ) DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_unicode_ci");

    $transaction->addQuery("ALTER TABLE `" . PREFIX . "settings` ADD `spam_check` int(1) NOT NULL default '0'");
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "settings` ADD `detect_language` int(1) NOT NULL default '0'");
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "settings` ADD `spamapikey` varchar(32) NOT NULL default ''");
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "settings` ADD `spamapihost` varchar(255) NOT NULL default ''");
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "settings` ADD `spammaxposts` int(11) NOT NULL default '0'");
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "settings` ADD `spamapiblockerror` int(1) NOT NULL default '0'");
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "settings` ADD `date_format` varchar(255) NOT NULL default 'd.m.Y'");
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "settings` ADD `time_format` varchar(255) NOT NULL default 'H:i'");
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "settings` ADD `user_guestbook` int(1) NOT NULL default '1'");
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "settings` ADD `sc_files` int(1) NOT NULL default '1'");
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "settings` ADD `sc_demos` int(1) NOT NULL default '1'");

    $transaction->addQuery("UPDATE `" . PREFIX . "settings` SET spamapihost='https://api.webspell.org/'");

    $transaction->addQuery("ALTER TABLE `" . PREFIX . "user` ADD `date_format` varchar(255) NOT NULL default 'd.m.Y'");
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "user` ADD `time_format` varchar(255) NOT NULL default 'H:i'");
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "user` ADD `hdd` varchar(255) NOT NULL default ''");
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "user` ADD `headset` varchar(255) NOT NULL default ''");
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "user` ADD `user_guestbook` int(1) NOT NULL default '1'");

    $transaction->addQuery("ALTER TABLE `" . PREFIX . "settings` ADD `modRewrite` int(1) NOT NULL default '0'");

    
    //edit countries
    $transaction->addQuery("INSERT INTO `" . PREFIX . "countries` ( `countryID` , `country` , `short` )
    VALUES
     ('', 'Afghanistan', 'af'),
     ('', 'Aland Islands', 'ax'),
     ('', 'Bahamas', 'bs'),
     ('', 'Saint Barthelemy', 'bl'),
     ('', 'Caribbean Netherlands', 'bq'),
     ('', 'Chad', 'td'),
     ('', 'Curacao', 'cw'),
     ('', 'French Guiana', 'gf'),
     ('', 'Guernsey', 'gg'),
     ('', 'Equatorial Guinea', 'gq'),
     ('', 'Canary Islands', 'ic'),
     ('', 'Isle of Man', 'im'),
     ('', 'Jersey', 'je'),
     ('', 'Kosovo', 'xk'),
     ('', 'Martinique', 'mq'),
     ('', 'Mayotte', 'yt'),
     ('', 'Montenegro', 'me'),
     ('', 'Namibia', 'na'),
     ('', 'Niger', 'ne'),
     ('', 'Saint Barthelemy', 'bl'),
     ('', 'Saint Martin', 'mf'),
     ('', 'Serbia', 'rs'),
     ('', 'South Sudan', 'ss'),
     ('', 'Timor-Leste', 'tl')
  ");

    $transaction->addQuery("UPDATE `" . PREFIX . "countries` SET country='Bosnia and Herzegowina' WHERE short = 'ba'");
    $transaction->addQuery("UPDATE `" . PREFIX . "countries` SET country='Brunei' WHERE short = 'bn'");
    $transaction->addQuery("UPDATE `" . PREFIX . "countries` SET country='Belize' WHERE short = 'bz'");
    $transaction->addQuery("UPDATE `" . PREFIX . "countries` SET country='Ivory Coast' WHERE short = 'ci'");
    $transaction->addQuery("UPDATE `" . PREFIX . "countries` SET country='South Georgia and the South Sandwich Islands' WHERE short = 'gs'");
    $transaction->addQuery("UPDATE `" . PREFIX . "countries` SET country='Kenya' WHERE short = 'ke'");
    $transaction->addQuery("UPDATE `" . PREFIX . "countries` SET country='North Korea' WHERE short = 'kp'");
    $transaction->addQuery("UPDATE `" . PREFIX . "countries` SET country='South Korea' WHERE short = 'kr'");
    $transaction->addQuery("UPDATE `" . PREFIX . "countries` SET country='Burma' WHERE short = 'mm'");
    $transaction->addQuery("UPDATE `" . PREFIX . "countries` SET country='Laos' WHERE short = 'la'");
    $transaction->addQuery("UPDATE `" . PREFIX . "countries` SET country='Libya' WHERE short = 'ly'");
    $transaction->addQuery("UPDATE `" . PREFIX . "countries` SET country='Russia' WHERE short = 'ru'");
    $transaction->addQuery("UPDATE `" . PREFIX . "countries` SET country='Seychelles' WHERE short = 'sc'");
    $transaction->addQuery("UPDATE `" . PREFIX . "countries` SET country='Slovakia' WHERE short = 'sk'");
    $transaction->addQuery("UPDATE `" . PREFIX . "countries` SET country='Turks and Caicos Islands' WHERE short = 'tc'");
    $transaction->addQuery("UPDATE `" . PREFIX . "countries` SET country='Vatican City' WHERE short = 'va'");
    $transaction->addQuery("UPDATE `" . PREFIX . "countries` SET country='Luxembourg' WHERE short = 'lu'");

    $transaction->addQuery("DELETE FROM `" . PREFIX . "countries` WHERE `short` = 'bv'");
    $transaction->addQuery("DELETE FROM `" . PREFIX . "countries` WHERE `short` = 'gp'");
    $transaction->addQuery("DELETE FROM `" . PREFIX . "countries` WHERE `short` = 'hm'");
    $transaction->addQuery("DELETE FROM `" . PREFIX . "countries` WHERE `short` = 'io'");
    $transaction->addQuery("DELETE FROM `" . PREFIX . "countries` WHERE `short` = 'nb'");
    $transaction->addQuery("DELETE FROM `" . PREFIX . "countries` WHERE `short` = 'nx'");
    $transaction->addQuery("DELETE FROM `" . PREFIX . "countries` WHERE `short` = 'pm'");
    $transaction->addQuery("DELETE FROM `" . PREFIX . "countries` WHERE `short` = 'sj'");
    $transaction->addQuery("DELETE FROM `" . PREFIX . "countries` WHERE `short` = 'ti'");
    $transaction->addQuery("DELETE FROM `" . PREFIX . "countries` WHERE `short` = 'wa'");
    $transaction->addQuery("DELETE FROM `" . PREFIX . "countries` WHERE `short` = 'yu'");
    $transaction->addQuery("DELETE FROM `" . PREFIX . "countries` WHERE `short` = 'tp'");
    if ($transaction->successful()) {
        return array('status' => 'success', 'message' => 'Updated to webSPELL 4.3 Part 1');
    } else {
        return array('status' => 'fail', 'message' => 'Failed to update to webSPELL 4.3 Part 1<br/>' . $transaction->getError());
    }
}

function update_420_430_2($_database)
{
    $transaction = new Transaction($_database);

    $transaction->addQuery("CREATE TABLE `" . PREFIX . "tags` (
  `rel` varchar(255) NOT NULL,
  `ID` int(11) NOT NULL,
  `tag` varchar(255) NOT NULL
) DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_unicode_ci");

    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "dashnavi_categories`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "dashnavi_categories` (
  `catID` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `default` int(1) NOT NULL DEFAULT '0',
  `sort` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`catID`)
) AUTO_INCREMENT=9
 DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_unicode_ci");

    $transaction->addQuery("INSERT INTO `" . PREFIX . "dashnavi_categories` ( `catID` , `name`, `default`, `sort` ) VALUES ('1', 'main', '1', '1');");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "dashnavi_categories` ( `catID` , `name`, `default`, `sort` ) VALUES ('2', 'user', '1', '2');");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "dashnavi_categories` ( `catID` , `name`, `default`, `sort` ) VALUES ('3', 'spam', '1', '3');");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "dashnavi_categories` ( `catID` , `name`, `default`, `sort` ) VALUES ('4', 'rubrics', '1', '4');");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "dashnavi_categories` ( `catID` , `name`, `default`, `sort` ) VALUES ('5', 'settings', '1', '5');");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "dashnavi_categories` ( `catID` , `name`, `default`, `sort` ) VALUES ('6', 'content', '1', '6');");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "dashnavi_categories` ( `catID` , `name`, `default`, `sort` ) VALUES ('7', 'forum', '1', '7');");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "dashnavi_categories` ( `catID` , `name`, `default`, `sort` ) VALUES ('8', 'gallery', '1', '8');");

	// NOR Template
	$transaction->addQuery("INSERT INTO `" . PREFIX . "dashnavi_categories` ( `catID` , `name`, `default`, `sort` ) VALUES ('9', 'plugins', '1', '9');");
   
   $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "dashnavi_links`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "dashnavi_links` (
  `linkID` int(11) NOT NULL AUTO_INCREMENT,
  `catID` int(11) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  `url` varchar(255) NOT NULL DEFAULT '',
  `accesslevel` varchar(255) NOT NULL DEFAULT '',
  `sort` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`linkID`)
) AUTO_INCREMENT=1
 DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_unicode_ci");
 
    $transaction->addQuery("ALTER TABLE `" . PREFIX . "countries` ADD `fav` int(1) NOT NULL default '0'");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "modrewrite` (
    `ruleID` int(11) NOT NULL AUTO_INCREMENT,
    `regex` text NOT NULL,
    `link` text NOT NULL,
    `fields` text NOT NULL,
    `replace_regex` text NOT NULL,
    `replace_result` text NOT NULL,
    `rebuild_regex` text NOT NULL,
    `rebuild_result` text NOT NULL,
    PRIMARY KEY (`ruleID`)
    ) AUTO_INCREMENT=1
     DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_unicode_ci");

    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('about.html','index.php?site=about','a:0:{}','index\\\\.php\\\\?site=about','about.html','about\\\\.html','index.php?site=about')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('articles.html','index.php?site=articles','a:0:{}','index\\\\.php\\\\?site=articles','articles.html','articles\\\\.html','index.php?site=articles')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('articles/{articlesID}.html','index.php?site=articles&action=show&articlesID={articlesID}','a:1:{s:10:\"articlesID\";s:7:\"integer\";}','index\\\\.php\\\\?site=articles[&|&amp;]*action=show[&|&amp;]*articlesID=([0-9]+)','articles/$3.html','articles\\\\/([0-9]+?)\\\\.html','index.php?site=articles&action=show&articlesID=$1')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('articles/{articlesID}/edit.html','articles.php?action=edit&articlesID={articlesID}','a:1:{s:10:\"articlesID\";s:7:\"integer\";}','articles\\\\.php\\\\?action=edit[&|&amp;]*articlesID=([0-9]+)','articles/$3/edit.html','articles\\\\/([0-9]+?)\\\\/edit\\\\.html','articles.php?action=edit&articlesID=$1')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('articles/{page}/{articlesID}.html','index.php?site=articles&action=show&articlesID={articlesID}&page={page}','a:2:{s:10:\"articlesID\";s:7:\"integer\";s:4:\"page\";s:7:\"integer\";}','index\\\\.php\\\\?site=articles[&|&amp;]*action=show[&|&amp;]*articlesID=([0-9]+)[&|&amp;]*page=([0-9]+)','articles/$4/$3.html','articles\\\\/([0-9]+?)\\\\/([0-9]+?)\\\\.html','index.php?site=articles&action=show&articlesID=$2&page=$1')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('articles/{sort}/{type}/{page}.html','index.php?site=articles&sort={sort}&type={type}&page={page}','a:3:{s:4:\"sort\";s:6:\"string\";s:4:\"type\";s:6:\"string\";s:4:\"page\";s:7:\"integer\";}','index\\\\.php\\\\?site=articles[&|&amp;]*sort=(\\\\w*?)[&|&amp;]*type=(\\\\w*?)[&|&amp;]*page=([0-9]+)','articles/$3/$4/$5.html','articles\\\\/(\\\\w*?)\\\\/(\\\\w*?)\\\\/([0-9]+?)\\\\.html','index.php?site=articles&sort=$1&type=$2&page=$3')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('articles/{sort}/{type}/1.html','index.php?site=articles&sort={sort}&type={type}','a:2:{s:4:\"sort\";s:6:\"string\";s:4:\"type\";s:6:\"string\";}','index\\\\.php\\\\?site=articles[&|&amp;]*sort=(\\\\w*?)[&|&amp;]*type=(\\\\w*?)','articles/$3/$4/1.html','articles\\\\/(\\\\w*?)\\\\/(\\\\w*?)\\\\/1\\\\.html','index.php?site=articles&sort=$1&type=$2')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('awards.html','index.php?site=awards','a:0:{}','index\\\\.php\\\\?site=awards','awards.html','awards\\\\.html','index.php?site=awards')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('awards/{awardID}.html','index.php?site=awards&action=details&awardID={awardID}','a:1:{s:7:\"awardID\";s:7:\"integer\";}','index\\\\.php\\\\?site=awards[&|&amp;]*action=details[&|&amp;]*awardID=([0-9]+)','awards/$3.html','awards\\\\/([0-9]+?)\\\\.html','index.php?site=awards&action=details&awardID=$1')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('awards/{sort}/{type}/{page}.html','index.php?site=awards&sort={sort}&type={type}&page={page}','a:3:{s:4:\"page\";s:7:\"integer\";s:4:\"sort\";s:6:\"string\";s:4:\"type\";s:6:\"string\";}','index\\\\.php\\\\?site=awards[&|&amp;]*sort=(\\\\w*?)[&|&amp;]*type=(\\\\w*?)[&|&amp;]*page=([0-9]+)','awards/$3/$4/$5.html','awards\\\\/(\\\\w*?)\\\\/(\\\\w*?)\\\\/([0-9]+?)\\\\.html','index.php?site=awards&sort=$1&type=$2&page=$3')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('awards/{sort}/{type}/{page}.html','index.php?site=awards&page={page}&sort={sort}&type={type}','a:3:{s:4:\"type\";s:6:\"string\";s:4:\"page\";s:7:\"integer\";s:4:\"sort\";s:6:\"string\";}','index\\\\.php\\\\?site=awards[&|&amp;]*page=([0-9]+)[&|&amp;]*sort=(\\\\w*?)[&|&amp;]*type=(\\\\w*?)','awards/$4/$5/$3.html','awards\\\\/(\\\\w*?)\\\\/(\\\\w*?)\\\\/([0-9]+?)\\\\.html','index.php?site=awards&page=$3&sort=$1&type=$2')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('awards/{sort}/{type}/1.html','index.php?site=awards&sort={sort}&type={type}','a:2:{s:4:\"sort\";s:6:\"string\";s:4:\"type\";s:6:\"string\";}','index\\\\.php\\\\?site=awards[&|&amp;]*sort=(\\\\w*?)[&|&amp;]*type=(\\\\w*?)','awards/$3/$4/1.html','awards\\\\/(\\\\w*?)\\\\/(\\\\w*?)\\\\/1\\\\.html','index.php?site=awards&sort=$1&type=$2')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('awards/{squadID}/{sort}/{type}/{page}.html','index.php?site=awards&action=showsquad&squadID={squadID}&sort={sort}&type={type}&page={page}','a:4:{s:7:\"squadID\";s:7:\"integer\";s:4:\"page\";s:7:\"integer\";s:4:\"sort\";s:6:\"string\";s:4:\"type\";s:6:\"string\";}','index\\\\.php\\\\?site=awards[&|&amp;]*action=showsquad[&|&amp;]*squadID=([0-9]+)[&|&amp;]*sort=(\\\\w*?)[&|&amp;]*type=(\\\\w*?)[&|&amp;]*page=([0-9]+)','awards/$3/$4/$5/$6.html','awards\\\\/([0-9]+?)\\\\/(\\\\w*?)\\\\/(\\\\w*?)\\\\/([0-9]+?)\\\\.html','index.php?site=awards&action=showsquad&squadID=$1&sort=$2&type=$3&page=$4')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('awards/{squadID}/{sort}/{type}/{page}.html','index.php?site=awards&action=showsquad&squadID={squadID}&page={page}&sort={sort}&type={type}','a:4:{s:7:\"squadID\";s:7:\"integer\";s:4:\"page\";s:7:\"integer\";s:4:\"sort\";s:6:\"string\";s:4:\"type\";s:6:\"string\";}','index\\\\.php\\\\?site=awards[&|&amp;]*action=showsquad[&|&amp;]*squadID=([0-9]+)[&|&amp;]*page=([0-9]+)[&|&amp;]*sort=(\\\\w*?)[&|&amp;]*type=(\\\\w*?)','awards/$3/$5/$6/$4.html','awards\\\\/([0-9]+?)\\\\/(\\\\w*?)\\\\/(\\\\w*?)\\\\/([0-9]+?)\\\\.html','index.php?site=awards&action=showsquad&squadID=$1&page=$4&sort=$2&type=$3')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('awards/{squadID}/{sort}/{type}/1.html','index.php?site=awards&action=showsquad&squadID={squadID}&sort={sort}&type={type}','a:3:{s:7:\"squadID\";s:7:\"integer\";s:4:\"sort\";s:6:\"string\";s:4:\"type\";s:6:\"string\";}','index\\\\.php\\\\?site=awards[&|&amp;]*action=showsquad[&|&amp;]*squadID=([0-9]+)[&|&amp;]*sort=(\\\\w*?)[&|&amp;]*type=(\\\\w*?)','awards/$3/$4/$5/1.html','awards\\\\/([0-9]+?)\\\\/(\\\\w*?)\\\\/(\\\\w*?)\\\\/1\\\\.html','index.php?site=awards&action=showsquad&squadID=$1&sort=$2&type=$3')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('awards/edit/{awardID}.html','index.php?site=awards&action=edit&awardID={awardID}','a:1:{s:7:\"awardID\";s:7:\"integer\";}','index\\\\.php\\\\?site=awards[&|&amp;]*action=edit[&|&amp;]*awardID=([0-9]+)','awards/edit/$3.html','awards\\\\/edit\\\\/([0-9]+?)\\\\.html','index.php?site=awards&action=edit&awardID=$1')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('awards/new.html','index.php?site=awards&action=new','a:0:{}','index\\\\.php\\\\?site=awards[&|&amp;]*action=new','awards/new.html','awards\\\\/new\\\\.html','index.php?site=awards&action=new')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('buddies.html','index.php?site=buddies','a:0:{}','index\\\\.php\\\\?site=buddies','buddies.html','buddies\\\\.html','index.php?site=buddies')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('calendar.html','index.php?site=calendar','a:0:{}','index\\\\.php\\\\?site=calendar','calendar.html','calendar\\\\.html','index.php?site=calendar')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('calendar.html#event','index.php?site=calendar#event','a:0:{}','index\\\\.php\\\\?site=calendar#event','calendar.html#event','calendar\\\\.html#event','index.php?site=calendar#event')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('calendar/{year}/{month}.html','index.php?site=calendar&month={month}&year={year}','a:2:{s:5:\"month\";s:7:\"integer\";s:4:\"year\";s:7:\"integer\";}','index\\\\.php\\\\?site=calendar[&|&amp;]*month=([0-9]+)[&|&amp;]*year=([0-9]+)','calendar/$4/$3.html','calendar\\\\/([0-9]+?)\\\\/([0-9]+?)\\\\.html','index.php?site=calendar&month=$2&year=$1')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('calendar/{year}/{month}/{tag}.html','index.php?site=calendar&tag={tag}&month={month}&year={year}#event','a:3:{s:3:\"tag\";s:7:\"integer\";s:5:\"month\";s:7:\"integer\";s:4:\"year\";s:7:\"integer\";}','index\\\\.php\\\\?site=calendar[&|&amp;]*tag=([0-9]+)[&|&amp;]*month=([0-9]+)[&|&amp;]*year=([0-9]+)#event','calendar/$5/$4/$3.html','calendar\\\\/([0-9]+?)\\\\/([0-9]+?)\\\\/([0-9]+?)\\\\.html','index.php?site=calendar&tag=$3&month=$2&year=$1#event')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('calendar/{year}/{month}/{tag}.html','index.php?site=calendar&tag={tag}&month={month}&year={year}','a:3:{s:3:\"tag\";s:7:\"integer\";s:5:\"month\";s:7:\"integer\";s:4:\"year\";s:7:\"integer\";}','index\\\\.php\\\\?site=calendar[&|&amp;]*tag=([0-9]+)[&|&amp;]*month=([0-9]+)[&|&amp;]*year=([0-9]+)','calendar/$5/$4/$3.html','calendar\\\\/([0-9]+?)\\\\/([0-9]+?)\\\\/([0-9]+?)\\\\.html','index.php?site=calendar&tag=$3&month=$2&year=$1')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('calendar/adddate.html','index.php?site=calendar&action=adddate','a:0:{}','index\\\\.php\\\\?site=calendar[&|&amp;]*action=adddate','calendar/adddate.html','calendar\\\\/adddate\\\\.html','index.php?site=calendar&action=adddate')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('calendar/addwar.html','index.php?site=calendar&action=addwar','a:0:{}','index\\\\.php\\\\?site=calendar[&|&amp;]*action=addwar','calendar/addwar.html','calendar\\\\/addwar\\\\.html','index.php?site=calendar&action=addwar')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('calendar/announce/{upID}.html','index.php?site=calendar&action=announce&upID={upID}','a:1:{s:4:\"upID\";s:7:\"integer\";}','index\\\\.php\\\\?site=calendar[&|&amp;]*action=announce[&|&amp;]*upID=([0-9]+)','calendar/announce/$3.html','calendar\\\\/announce\\\\/([0-9]+?)\\\\.html','index.php?site=calendar&action=announce&upID=$1')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('calendar/delete/{upID}.html','calendar.php?action=delete&upID={upID}','a:1:{s:4:\"upID\";s:7:\"integer\";}','calendar\\\\.php\\\\?action=delete[&|&amp;]*upID=([0-9]+)','calendar/delete/$3.html','calendar\\\\/delete\\\\/([0-9]+?)\\\\.html','calendar.php?action=delete&upID=$1')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('calendar/editdate/{upID}.html','index.php?site=calendar&action=editdate&upID={upID}','a:1:{s:4:\"upID\";s:7:\"integer\";}','index\\\\.php\\\\?site=calendar[&|&amp;]*action=editdate[&|&amp;]*upID=([0-9]+)','calendar/editdate/$3.html','calendar\\\\/editdate\\\\/([0-9]+?)\\\\.html','index.php?site=calendar&action=editdate&upID=$1')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('calendar/editwar/{upID}.html','index.php?site=calendar&action=editwar&upID={upID}','a:1:{s:4:\"upID\";s:7:\"integer\";}','index\\\\.php\\\\?site=calendar[&|&amp;]*action=editwar[&|&amp;]*upID=([0-9]+)','calendar/editwar/$3.html','calendar\\\\/editwar\\\\/([0-9]+?)\\\\.html','index.php?site=calendar&action=editwar&upID=$1')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('calendar/event/save.html','calendar.php?action=savedate','a:0:{}','calendar\\\\.php\\\\?action=savedate','calendar/event/save.html','calendar\\\\/event\\\\/save\\\\.html','calendar.php?action=savedate')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('calendar/event/saveedit.html','calendar.php?action=saveeditdate','a:0:{}','calendar\\\\.php\\\\?action=saveeditdate','calendar/event/saveedit.html','calendar\\\\/event\\\\/saveedit\\\\.html','calendar.php?action=saveeditdate')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('calendar/month/{month}.html','index.php?site=calendar&month={month}','a:1:{s:5:\"month\";s:7:\"integer\";}','index\\\\.php\\\\?site=calendar[&|&amp;]*month=([0-9]+)','calendar/month/$3.html','calendar\\\\/month\\\\/([0-9]+?)\\\\.html','index.php?site=calendar&month=$1')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('calendar/new/{upID}.html','clanwars.php?action=new&upID={upID}','a:1:{s:4:\"upID\";s:7:\"integer\";}','clanwars\\\\.php\\\\?action=new[&|&amp;]*upID=([0-9]+)','calendar/new/$3.html','calendar\\\\/new\\\\/([0-9]+?)\\\\.html','clanwars.php?action=new&upID=$1')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('calendar/war/save.html','calendar.php?action=savewar','a:0:{}','calendar\\\\.php\\\\?action=savewar','calendar/war/save.html','calendar\\\\/war\\\\/save\\\\.html','calendar.php?action=savewar')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('calendar/war/saveedit.html','calendar.php?action=saveeditwar','a:0:{}','calendar\\\\.php\\\\?action=saveeditwar','calendar/war/saveedit.html','calendar\\\\/war\\\\/saveedit\\\\.html','calendar.php?action=saveeditwar')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('cashbox.html','index.php?site=cashbox','a:0:{}','index\\\\.php\\\\?site=cashbox','cashbox.html','cashbox\\\\.html','index.php?site=cashbox')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('cashbox/{id}.html','index.php?site=cashbox&id={id}','a:1:{s:2:\"id\";s:7:\"integer\";}','index\\\\.php\\\\?site=cashbox[&|&amp;]*id=([0-9]+)','cashbox/$3.html','cashbox\\\\/([0-9]+?)\\\\.html','index.php?site=cashbox&id=$1')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('cashbox/action.html','cashbox.php','a:0:{}','cashbox\\\\.php','cashbox/action.html','cashbox\\\\/action\\\\.html','cashbox.php')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('cashbox/delete/{id}.html','cashbox.php?delete=true&id={id}','a:1:{s:2:\"id\";s:7:\"integer\";}','cashbox\\\\.php\\\\?delete=true[&|&amp;]*id=([0-9]+)','cashbox/delete/$3.html','cashbox\\\\/delete\\\\/([0-9]+?)\\\\.html','cashbox.php?delete=true&id=$1')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('cashbox/edit/{id}.html','index.php?site=cashbox&action=edit&id={id}','a:1:{s:2:\"id\";s:7:\"integer\";}','index\\\\.php\\\\?site=cashbox[&|&amp;]*action=edit[&|&amp;]*id=([0-9]+)','cashbox/edit/$3.html','cashbox\\\\/edit\\\\/([0-9]+?)\\\\.html','index.php?site=cashbox&action=edit&id=$1')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('cashbox/new.html','index.php?site=cashbox&action=new','a:0:{}','index\\\\.php\\\\?site=cashbox[&|&amp;]*action=new','cashbox/new.html','cashbox\\\\/new\\\\.html','index.php?site=cashbox&action=new')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('cashbox/new.html','index.php?site=cashbox&action=new','a:0:{}','index\\\\.php\\\\?site=cashbox[&|&amp;]*action=new','cashbox/new.html','cashbox\\\\/new\\\\.html','index.php?site=cashbox&action=new')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('challenge.html','index.php?site=challenge','a:0:{}','index\\\\.php\\\\?site=challenge','challenge.html','challenge\\\\.html','index.php?site=challenge')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('challenge/{type}.html','index.php?site=challenge&type={type}','a:1:{s:4:\"type\";s:6:\"string\";}','index\\\\.php\\\\?site=challenge[&|&amp;]*type=(\\\\w*?)','challenge/$3.html','challenge\\\\/(\\\\w*?)\\\\.html','index.php?site=challenge&type=$1')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('challenge/save.html','index.php?site=challenge&action=save','a:0:{}','index\\\\.php\\\\?site=challenge[&|&amp;]*action=save','challenge/save.html','challenge\\\\/save\\\\.html','index.php?site=challenge&action=save')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('clanwars.html','index.php?site=clanwars','a:0:{}','index\\\\.php\\\\?site=clanwars','clanwars.html','clanwars\\\\.html','index.php?site=clanwars')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('clanwars/{id}.html','index.php?site=clanwars_details&cwID={id}','a:1:{s:2:\"id\";s:7:\"integer\";}','index\\\\.php\\\\?site=clanwars_details[&|&amp;]*cwID=([0-9]+)','clanwars/$3.html','clanwars\\\\/([0-9]+?)\\\\.html','index.php?site=clanwars_details&cwID=$1')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('clanwars/{only}/{id}.html','index.php?site=clanwars&action=showonly&only={only}&id={id}','a:2:{s:2:\"id\";s:7:\"integer\";s:4:\"only\";s:6:\"string\";}','index\\\\.php\\\\?site=clanwars[&|&amp;]*action=showonly[&|&amp;]*only=(\\\\w*?)[&|&amp;]*id=([0-9]+)','clanwars/$3/$4.html','clanwars\\\\/(\\\\w*?)\\\\/([0-9]+?)\\\\.html','index.php?site=clanwars&action=showonly&only=$1&id=$2')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('clanwars/{only}/{id}/{sort}/{type}/{page}.html','index.php?site=clanwars&action=showonly&id={id}&sort={sort}&type={type}&only={only}&page={page}','a:5:{s:4:\"page\";s:7:\"integer\";s:2:\"id\";s:7:\"integer\";s:4:\"sort\";s:6:\"string\";s:4:\"type\";s:6:\"string\";s:4:\"only\";s:6:\"string\";}','index\\\\.php\\\\?site=clanwars[&|&amp;]*action=showonly[&|&amp;]*id=([0-9]+)[&|&amp;]*sort=(\\\\w*?)[&|&amp;]*type=(\\\\w*?)[&|&amp;]*only=(\\\\w*?)[&|&amp;]*page=([0-9]+)','clanwars/$6/$3/$4/$5/$7.html','clanwars\\\\/(\\\\w*?)\\\\/([0-9]+?)\\\\/(\\\\w*?)\\\\/(\\\\w*?)\\\\/([0-9]+?)\\\\.html','index.php?site=clanwars&action=showonly&id=$2&sort=$3&type=$4&only=$1&page=$5')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('clanwars/{only}/{id}/{sort}/{type}/{page}.html','index.php?site=clanwars&action=showonly&id={id}&page={page}&sort={sort}&type={type}&only={only}','a:5:{s:2:\"id\";s:7:\"integer\";s:4:\"page\";s:7:\"integer\";s:4:\"sort\";s:6:\"string\";s:4:\"type\";s:6:\"string\";s:4:\"only\";s:6:\"string\";}','index\\\\.php\\\\?site=clanwars[&|&amp;]*action=showonly[&|&amp;]*id=([0-9]+)[&|&amp;]*page=([0-9]+)[&|&amp;]*sort=(\\\\w*?)[&|&amp;]*type=(\\\\w*?)[&|&amp;]*only=(\\\\w*?)','clanwars/$7/$3/$5/$6/$4.html','clanwars\\\\/(\\\\w*?)\\\\/([0-9]+?)\\\\/(\\\\w*?)\\\\/(\\\\w*?)\\\\/([0-9]+?)\\\\.html','index.php?site=clanwars&action=showonly&id=$2&page=$5&sort=$3&type=$4&only=$1')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('clanwars/{only}/{id}/{sort}/{type}/{page}.html','index.php?site=clanwars&action=showonly&id={id}&only={only}&page={page}&sort={sort}&type={type}','a:5:{s:2:\"id\";s:7:\"integer\";s:4:\"only\";s:6:\"string\";s:4:\"page\";s:7:\"integer\";s:4:\"sort\";s:6:\"string\";s:4:\"type\";s:6:\"string\";}','index\\\\.php\\\\?site=clanwars[&|&amp;]*action=showonly[&|&amp;]*id=([0-9]+)[&|&amp;]*only=(\\\\w*?)[&|&amp;]*page=([0-9]+)[&|&amp;]*sort=(\\\\w*?)[&|&amp;]*type=(\\\\w*?)','clanwars/$4/$3/$6/$7/$5.html','clanwars\\\\/(\\\\w*?)\\\\/([0-9]+?)\\\\/(\\\\w*?)\\\\/(\\\\w*?)\\\\/([0-9]+?)\\\\.html','index.php?site=clanwars&action=showonly&id=$2&only=$1&page=$5&sort=$3&type=$4')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('clanwars/{only}/{id}/{sort}/{type}/1.html','index.php?site=clanwars&action=showonly&id={id}&sort={sort}&type={type}&only={only}','a:4:{s:2:\"id\";s:7:\"integer\";s:4:\"sort\";s:6:\"string\";s:4:\"type\";s:6:\"string\";s:4:\"only\";s:6:\"string\";}','index\\\\.php\\\\?site=clanwars[&|&amp;]*action=showonly[&|&amp;]*id=([0-9]+)[&|&amp;]*sort=(\\\\w*?)[&|&amp;]*type=(\\\\w*?)[&|&amp;]*only=(\\\\w*?)','clanwars/$6/$3/$4/$5/1.html','clanwars\\\\/(\\\\w*?)\\\\/([0-9]+?)\\\\/(\\\\w*?)\\\\/(\\\\w*?)\\\\/1\\\\.html','index.php?site=clanwars&action=showonly&id=$2&sort=$3&type=$4&only=$1')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('clanwars/{only}/{squadID}/{sort}/DESC/1.html','index.php?site=clanwars&action=showonly&id={squadID}&sort={sort}&only={only}','a:3:{s:7:\"squadID\";s:7:\"integer\";s:4:\"only\";s:6:\"string\";s:4:\"sort\";s:6:\"string\";}','index\\\\.php\\\\?site=clanwars[&|&amp;]*action=showonly[&|&amp;]*id=([0-9]+)[&|&amp;]*sort=(\\\\w*?)[&|&amp;]*only=(\\\\w*?)','clanwars/$5/$3/$4/DESC/1.html','clanwars\\\\/(\\\\w*?)\\\\/([0-9]+?)\\\\/(\\\\w*?)\\\\/DESC\\\\/1\\\\.html','index.php?site=clanwars&action=showonly&id=$2&sort=$3&only=$1')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('clanwars/new.html','clanwars.php?action=new','a:0:{}','clanwars\\\\.php\\\\?action=new','clanwars/new.html','clanwars\\\\/new\\\\.html','clanwars.php?action=new')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('clanwars/stats.html','index.php?site=clanwars&action=stats','a:0:{}','index\\\\.php\\\\?site=clanwars[&|&amp;]*action=stats','clanwars/stats.html','clanwars\\\\/stats\\\\.html','index.php?site=clanwars&action=stats')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('comments.html','comments.php','a:0:{}','comments\\\\.php','comments.html','comments\\\\.html','comments.php')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('comments/delete.html','comments.php?delete=true','a:0:{}','comments\\\\.php\\\\?delete=true','comments/delete.html','comments\\\\/delete\\\\.html','comments.php?delete=true')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('contact.html','index.php?site=contact','a:0:{}','index\\\\.php\\\\?site=contact','contact.html','contact\\\\.html','index.php?site=contact')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('counter.html','index.php?site=counter_stats','a:0:{}','index\\\\.php\\\\?site=counter_stats','counter.html','counter\\\\.html','index.php?site=counter_stats')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('demos.html','index.php?site=demos','a:0:{}','index\\\\.php\\\\?site=demos','demos.html','demos\\\\.html','index.php?site=demos')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('demos/{demoID}/edit.html','index.php?site=demos&action=edit&demoID={demoID}','a:1:{s:6:\"demoID\";s:7:\"integer\";}','index\\\\.php\\\\?site=demos[&|&amp;]*action=edit[&|&amp;]*demoID=([0-9]+)','demos/$3/edit.html','demos\\\\/([0-9]+?)\\\\/edit\\\\.html','index.php?site=demos&action=edit&demoID=$1')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('demos/{demoID}/show.html','index.php?site=demos&action=showdemo&demoID={demoID}','a:1:{s:6:\"demoID\";s:7:\"integer\";}','index\\\\.php\\\\?site=demos[&|&amp;]*action=showdemo[&|&amp;]*demoID=([0-9]+)','demos/$3/show.html','demos\\\\/([0-9]+?)\\\\/show\\\\.html','index.php?site=demos&action=showdemo&demoID=$1')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('demos/{game}/{sort}/{type}/{page}.html','index.php?site=demos&action=showgame&game={game}&page={page}&sort={sort}&type={type}','a:4:{s:4:\"game\";s:6:\"string\";s:4:\"page\";s:7:\"integer\";s:4:\"sort\";s:6:\"string\";s:4:\"type\";s:6:\"string\";}','index\\\\.php\\\\?site=demos[&|&amp;]*action=showgame[&|&amp;]*game=(\\\\w*?)[&|&amp;]*page=([0-9]+)[&|&amp;]*sort=(\\\\w*?)[&|&amp;]*type=(\\\\w*?)','demos/$3/$5/$6/$4.html','demos\\\\/(\\\\w*?)\\\\/(\\\\w*?)\\\\/(\\\\w*?)\\\\/([0-9]+?)\\\\.html','index.php?site=demos&action=showgame&game=$1&page=$4&sort=$2&type=$3')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('demos/{sort}/{type}/{page}.html','index.php?site=demos&page={page}&sort={sort}&type={type}','a:3:{s:4:\"page\";s:7:\"integer\";s:4:\"sort\";s:6:\"string\";s:4:\"type\";s:6:\"string\";}','index\\\\.php\\\\?site=demos[&|&amp;]*page=([0-9]+)[&|&amp;]*sort=(\\\\w*?)[&|&amp;]*type=(\\\\w*?)','demos/$4/$5/$3.html','demos\\\\/(\\\\w*?)\\\\/(\\\\w*?)\\\\/([0-9]+?)\\\\.html','index.php?site=demos&page=$3&sort=$1&type=$2')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('demos/game/{game}.html','index.php?site=demos&action=showgame&game={game}','a:1:{s:4:\"game\";s:6:\"string\";}','index\\\\.php\\\\?site=demos[&|&amp;]*action=showgame[&|&amp;]*game=(\\\\w*?)','demos/game/$3.html','demos\\\\/game\\\\/(\\\\w*?)\\\\.html','index.php?site=demos&action=showgame&game=$1')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('demos/new.html','index.php?site=demos&action=new','a:0:{}','index\\\\.php\\\\?site=demos[&|&amp;]*action=new','demos/new.html','demos\\\\/new\\\\.html','index.php?site=demos&action=new')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('demos/save.html','demos.php','a:0:{}','demos\\\\.php','demos/save.html','demos\\\\/save\\\\.html','demos.php')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('download/demo/{demoID}','download.php?demoID={demoID}','a:1:{s:6:\"demoID\";s:7:\"integer\";}','download\\\\.php\\\\?demoID=([0-9]+)','download/demo/$3','download\\\\/demo\\\\/([0-9]+?)','download.php?demoID=$1')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('download/file/{fileID}','download.php?fileID={fileID}','a:1:{s:6:\"fileID\";s:7:\"integer\";}','download\\\\.php\\\\?fileID=([0-9]+)','download/file/$3','download\\\\/file\\\\/([0-9]+?)','download.php?fileID=$1')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('faq.html','index.php?site=faq','a:0:{}','index\\\\.php\\\\?site=faq','faq.html','faq\\\\.html','index.php?site=faq')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('faq/{catID}.html','index.php?site=faq&action=faqcat&faqcatID={catID}','a:1:{s:5:\"catID\";s:7:\"integer\";}','index\\\\.php\\\\?site=faq[&|&amp;]*action=faqcat[&|&amp;]*faqcatID=([0-9]+)','faq/$3.html','faq\\\\/([0-9]+?)\\\\.html','index.php?site=faq&action=faqcat&faqcatID=$1')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('faq/{catID}/{faqID}.html','index.php?site=faq&action=faq&faqID={faqID}&faqcatID={catID}','a:2:{s:5:\"faqID\";s:7:\"integer\";s:5:\"catID\";s:7:\"integer\";}','index\\\\.php\\\\?site=faq[&|&amp;]*action=faq[&|&amp;]*faqID=([0-9]+)[&|&amp;]*faqcatID=([0-9]+)','faq/$4/$3.html','faq\\\\/([0-9]+?)\\\\/([0-9]+?)\\\\.html','index.php?site=faq&action=faq&faqID=$2&faqcatID=$1')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('files.html','index.php?site=files','a:0:{}','index\\\\.php\\\\?site=files','files.html','files\\\\.html','index.php?site=files')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('files/category/{catID}','index.php?site=files&cat={catID}','a:1:{s:5:\"catID\";s:7:\"integer\";}','index\\\\.php\\\\?site=files[&|&amp;]*cat=([0-9]+)','files/category/$3','files\\\\/category\\\\/([0-9]+?)','index.php?site=files&cat=$1')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('files/file/{fileID}','index.php?site=files&file={fileID}','a:1:{s:6:\"fileID\";s:7:\"integer\";}','index\\\\.php\\\\?site=files[&|&amp;]*file=([0-9]+)','files/file/$3','files\\\\/file\\\\/([0-9]+?)','index.php?site=files&file=$1')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('files/report/{fileID}','index.php?site=files&action=report&link={fileID}','a:1:{s:6:\"fileID\";s:7:\"integer\";}','index\\\\.php\\\\?site=files[&|&amp;]*action=report[&|&amp;]*link=([0-9]+)','files/report/$3','files\\\\/report\\\\/([0-9]+?)','index.php?site=files&action=report&link=$1')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('forum.html','index.php?site=forum','a:0:{}','index\\\\.php\\\\?site=forum','forum.html','forum\\\\.html','index.php?site=forum')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('forum/{action}/board/{board}.html','index.php?site=forum&board={board}&action={action}','a:2:{s:5:\"board\";s:7:\"integer\";s:6:\"action\";s:6:\"string\";}','index\\\\.php\\\\?site=forum[&|&amp;]*board=([0-9]+)[&|&amp;]*action=(\\\\w*?)','forum/$4/board/$3.html','forum\\\\/(\\\\w*?)\\\\/board\\\\/([0-9]+?)\\\\.html','index.php?site=forum&board=$2&action=$1')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('forum/action.html','forum.php','a:0:{}','forum\\\\.php','forum/action.html','forum\\\\/action\\\\.html','forum.php')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('forum/actions/markall.html','index.php?site=forum&action=markall','a:0:{}','index\\\\.php\\\\?site=forum[&|&amp;]*action=markall','forum/actions/markall.html','forum\\\\/actions\\\\/markall\\\\.html','index.php?site=forum&action=markall')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('forum/board/{board}.html','index.php?site=forum&board={board}','a:1:{s:5:\"board\";s:7:\"integer\";}','index\\\\.php\\\\?site=forum[&|&amp;]*board=([0-9]+)','forum/board/$3.html','forum\\\\/board\\\\/([0-9]+?)\\\\.html','index.php?site=forum&board=$1')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('forum/board/{board}/addtopic.html','index.php?site=forum&addtopic=true&board={board}','a:1:{s:5:\"board\";s:7:\"integer\";}','index\\\\.php\\\\?site=forum[&|&amp;]*addtopic=true[&|&amp;]*board=([0-9]+)','forum/board/$3/addtopic.html','forum\\\\/board\\\\/([0-9]+?)\\\\/addtopic\\\\.html','index.php?site=forum&addtopic=true&board=$1')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('forum/cat/{cat}.html','index.php?site=forum&cat={cat}','a:1:{s:3:\"cat\";s:7:\"integer\";}','index\\\\.php\\\\?site=forum[&|&amp;]*cat=([0-9]+)','forum/cat/$3.html','forum\\\\/cat\\\\/([0-9]+?)\\\\.html','index.php?site=forum&cat=$1')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('gallery.html','index.php?site=gallery','a:0:{}','index\\\\.php\\\\?site=gallery','gallery.html','gallery\\\\.html','index.php?site=gallery')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('gallery/{galID}.html','index.php?site=gallery&galleryID={galID}','a:1:{s:5:\"galID\";s:7:\"integer\";}','index\\\\.php\\\\?site=gallery[&|&amp;]*galleryID=([0-9]+)','gallery/$3.html','gallery\\\\/([0-9]+?)\\\\.html','index.php?site=gallery&galleryID=$1')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('gallery/picture/{picID}.html','index.php?site=gallery&picID={picID}','a:1:{s:5:\"picID\";s:7:\"integer\";}','index\\\\.php\\\\?site=gallery[&|&amp;]*picID=([0-9]+)','gallery/picture/$3.html','gallery\\\\/picture\\\\/([0-9]+?)\\\\.html','index.php?site=gallery&picID=$1')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('gallery/usergalleries.html','index.php?site=gallery&groupID=0','a:0:{}','index\\\\.php\\\\?site=gallery[&|&amp;]*groupID=0','gallery/usergalleries.html','gallery\\\\/usergalleries\\\\.html','index.php?site=gallery&groupID=0')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('guestbook.html','index.php?site=guestbook','a:0:{}','index\\\\.php\\\\?site=guestbook','guestbook.html','guestbook\\\\.html','index.php?site=guestbook')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('guestbook/{type}/{page}.html','index.php?site=guestbook&page={page}&type={type}','a:2:{s:4:\"page\";s:7:\"integer\";s:4:\"type\";s:6:\"string\";}','index\\\\.php\\\\?site=guestbook[&|&amp;]*page=([0-9]+)[&|&amp;]*type=(\\\\w*?)','guestbook/$4/$3.html','guestbook\\\\/(\\\\w*?)\\\\/([0-9]+?)\\\\.html','index.php?site=guestbook&page=$2&type=$1')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('guestbook/add.html','index.php?site=guestbook&action=add','a:0:{}','index\\\\.php\\\\?site=guestbook[&|&amp;]*action=add','guestbook/add.html','guestbook\\\\/add\\\\.html','index.php?site=guestbook&action=add')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('guestbook/add/{id}.html','index.php?site=guestbook&action=add&messageID={id}','a:1:{s:2:\"id\";s:7:\"integer\";}','index\\\\.php\\\\?site=guestbook[&|&amp;]*action=add[&|&amp;]*messageID=([0-9]+)','guestbook/add/$3.html','guestbook\\\\/add\\\\/([0-9]+?)\\\\.html','index.php?site=guestbook&action=add&messageID=$1')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('guestbook/comment/{id}.html','index.php?site=guestbook&action=comment&guestbookID={id}','a:1:{s:2:\"id\";s:7:\"integer\";}','index\\\\.php\\\\?site=guestbook[&|&amp;]*action=comment[&|&amp;]*guestbookID=([0-9]+)','guestbook/comment/$3.html','guestbook\\\\/comment\\\\/([0-9]+?)\\\\.html','index.php?site=guestbook&action=comment&guestbookID=$1')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('help/bbcode.html','code.php','a:0:{}','code\\\\.php','help/bbcode.html','help\\\\/bbcode\\\\.html','code.php')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('help/smileys.html','smileys.php','a:0:{}','smileys\\\\.php','help/smileys.html','help\\\\/smileys\\\\.html','smileys.php')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('history.html','index.php?site=history','a:0:{}','index\\\\.php\\\\?site=history','history.html','history\\\\.html','index.php?site=history')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('imprint.html','index.php?site=imprint','a:0:{}','index\\\\.php\\\\?site=imprint','imprint.html','imprint\\\\.html','index.php?site=imprint')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('joinus.html','index.php?site=joinus','a:0:{}','index\\\\.php\\\\?site=joinus','joinus.html','joinus\\\\.html','index.php?site=joinus')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('joinus/save.html','index.php?site=joinus&action=save','a:0:{}','index\\\\.php\\\\?site=joinus[&|&amp;]*action=save','joinus/save.html','joinus\\\\/save\\\\.html','index.php?site=joinus&action=save')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('links.html','index.php?site=links','a:0:{}','index\\\\.php\\\\?site=links','links.html','links\\\\.html','index.php?site=links')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('links/{linkID}/edit.html','index.php?site=links&action=edit&linkID={linkID}','a:1:{s:6:\"linkID\";s:7:\"integer\";}','index\\\\.php\\\\?site=links[&|&amp;]*action=edit[&|&amp;]*linkID=([0-9]+)','links/$3/edit.html','links\\\\/([0-9]+?)\\\\/edit\\\\.html','index.php?site=links&action=edit&linkID=$1')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('links/category/{catID}.html','index.php?site=links&action=show&linkcatID={catID}','a:1:{s:5:\"catID\";s:7:\"integer\";}','index\\\\.php\\\\?site=links[&|&amp;]*action=show[&|&amp;]*linkcatID=([0-9]+)','links/category/$3.html','links\\\\/category\\\\/([0-9]+?)\\\\.html','index.php?site=links&action=show&linkcatID=$1')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('linkus.html','index.php?site=linkus','a:0:{}','index\\\\.php\\\\?site=linkus','linkus.html','linkus\\\\.html','index.php?site=linkus')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('linkus/{bannerID}/delete.html','linkus.php?delete=true&bannerID={bannerID}','a:1:{s:8:\"bannerID\";s:7:\"integer\";}','linkus\\\\.php\\\\?delete=true[&|&amp;]*bannerID=([0-9]+)','linkus/$3/delete.html','linkus\\\\/([0-9]+?)\\\\/delete\\\\.html','linkus.php?delete=true&bannerID=$1')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('linkus/{bannerID}/edit.html','index.php?site=linkus&action=edit&bannerID={bannerID}','a:1:{s:8:\"bannerID\";s:7:\"integer\";}','index\\\\.php\\\\?site=linkus[&|&amp;]*action=edit[&|&amp;]*bannerID=([0-9]+)','linkus/$3/edit.html','linkus\\\\/([0-9]+?)\\\\/edit\\\\.html','index.php?site=linkus&action=edit&bannerID=$1')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('linkus/new.html','index.php?site=linkus&action=new','a:0:{}','index\\\\.php\\\\?site=linkus[&|&amp;]*action=new','linkus/new.html','linkus\\\\/new\\\\.html','index.php?site=linkus&action=new')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('loginoverview.html','index.php?site=loginoverview','a:0:{}','index\\\\.php\\\\?site=loginoverview','loginoverview.html','loginoverview\\\\.html','index.php?site=loginoverview')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('logout.html','logout.php','a:0:{}','logout\\\\.php','logout.html','logout\\\\.html','logout.php')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('lostpassword.html','index.php?site=lostpassword','a:0:{}','index\\\\.php\\\\?site=lostpassword','lostpassword.html','lostpassword\\\\.html','index.php?site=lostpassword')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('members.html','index.php?site=members','a:0:{}','index\\\\.php\\\\?site=members','members.html','members\\\\.html','index.php?site=members')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('messenger.html','index.php?site=messenger','a:0:{}','index\\\\.php\\\\?site=messenger','messenger.html','messenger\\\\.html','index.php?site=messenger')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('messenger/{messageID}/read.html','index.php?site=messenger&action=show&id={messageID}','a:1:{s:9:\"messageID\";s:7:\"integer\";}','index\\\\.php\\\\?site=messenger[&|&amp;]*action=show[&|&amp;]*id=([0-9]+)','messenger/$3/read.html','messenger\\\\/([0-9]+?)\\\\/read\\\\.html','index.php?site=messenger&action=show&id=$1')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('messenger/{messageID}/reply.html','index.php?site=messenger&action=reply&id={messageID}','a:1:{s:9:\"messageID\";s:7:\"integer\";}','index\\\\.php\\\\?site=messenger[&|&amp;]*action=reply[&|&amp;]*id=([0-9]+)','messenger/$3/reply.html','messenger\\\\/([0-9]+?)\\\\/reply\\\\.html','index.php?site=messenger&action=reply&id=$1')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('messenger/action.html','messenger.php','a:0:{}','messenger\\\\.php','messenger/action.html','messenger\\\\/action\\\\.html','messenger.php')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('messenger/incoming.html','index.php?site=messenger&action=incoming','a:0:{}','index\\\\.php\\\\?site=messenger[&|&amp;]*action=incoming','messenger/incoming.html','messenger\\\\/incoming\\\\.html','index.php?site=messenger&action=incoming')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('messenger/new.html','index.php?site=messenger&action=newmessage','a:0:{}','index\\\\.php\\\\?site=messenger[&|&amp;]*action=newmessage','messenger/new.html','messenger\\\\/new\\\\.html','index.php?site=messenger&action=newmessage')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('messenger/outgoing.html','index.php?site=messenger&action=outgoing','a:0:{}','index\\\\.php\\\\?site=messenger[&|&amp;]*action=outgoing','messenger/outgoing.html','messenger\\\\/outgoing\\\\.html','index.php?site=messenger&action=outgoing')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('news.html','index.php?site=news','a:0:{}','index\\\\.php\\\\?site=news','news.html','news\\\\.html','index.php?site=news')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('news/{lang}/{newsID}.html','index.php?site=news_comments&newsID={newsID}&lang={lang}','a:2:{s:6:\"newsID\";s:7:\"integer\";s:4:\"lang\";s:6:\"string\";}','index\\\\.php\\\\?site=news_comments[&|&amp;]*newsID=([0-9]+)[&|&amp;]*lang=(\\\\w*?)','news/$4/$3.html','news\\\\/(\\\\w*?)\\\\/([0-9]+?)\\\\.html','index.php?site=news_comments&newsID=$2&lang=$1')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('news/{newsID}.html','index.php?site=news_comments&newsID={newsID}','a:1:{s:6:\"newsID\";s:7:\"integer\";}','index\\\\.php\\\\?site=news_comments[&|&amp;]*newsID=([0-9]+)','news/$3.html','news\\\\/([0-9]+?)\\\\.html','index.php?site=news_comments&newsID=$1')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('news/{newsID}/edit.html','news.php?action=edit&newsID={newsID}','a:1:{s:6:\"newsID\";s:7:\"integer\";}','news\\\\.php\\\\?action=edit[&|&amp;]*newsID=([0-9]+)','news/$3/edit.html','news\\\\/([0-9]+?)\\\\/edit\\\\.html','news.php?action=edit&newsID=$1')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('news/{newsID}/unpublish.html','news.php?quickactiontype=unpublish&newsID={newsID}','a:1:{s:6:\"newsID\";s:7:\"integer\";}','news\\\\.php\\\\?quickactiontype=unpublish[&|&amp;]*newsID=([0-9]+)','news/$3/unpublish.html','news\\\\/([0-9]+?)\\\\/unpublish\\\\.html','news.php?quickactiontype=unpublish&newsID=$1')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('news/action.html','news.php','a:0:{}','news\\\\.php','news/action.html','news\\\\/action\\\\.html','news.php')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('news/archive.html','index.php?site=news&action=archive','a:0:{}','index\\\\.php\\\\?site=news[&|&amp;]*action=archive','news/archive.html','news\\\\/archive\\\\.html','index.php?site=news&action=archive')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('news/archive/{sort}/{type}/{page}.html','index.php?site=news&action=archive&page={page}&sort={sort}&type={type}','a:3:{s:4:\"page\";s:7:\"integer\";s:4:\"sort\";s:6:\"string\";s:4:\"type\";s:6:\"string\";}','index\\\\.php\\\\?site=news[&|&amp;]*action=archive[&|&amp;]*page=([0-9]+)[&|&amp;]*sort=(\\\\w*?)[&|&amp;]*type=(\\\\w*?)','news/archive/$4/$5/$3.html','news\\\\/archive\\\\/(\\\\w*?)\\\\/(\\\\w*?)\\\\/([0-9]+?)\\\\.html','index.php?site=news&action=archive&page=$3&sort=$1&type=$2')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('news/archive/{sort}/{type}/1.html','index.php?site=news&action=archive&sort={sort}&type={type}','a:2:{s:4:\"sort\";s:6:\"string\";s:4:\"type\";s:6:\"string\";}','index\\\\.php\\\\?site=news[&|&amp;]*action=archive[&|&amp;]*sort=(\\\\w*?)[&|&amp;]*type=(\\\\w*?)','news/archive/$3/$4/1.html','news\\\\/archive\\\\/(\\\\w*?)\\\\/(\\\\w*?)\\\\/1\\\\.html','index.php?site=news&action=archive&sort=$1&type=$2')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('news/new.html','news.php?action=new','a:0:{}','news\\\\.php\\\\?action=new','news/new.html','news\\\\/new\\\\.html','news.php?action=new')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('news/unpublish.html','news.php?quickactiontype=unpublish','a:0:{}','news\\\\.php\\\\?quickactiontype=unpublish','news/unpublish.html','news\\\\/unpublish\\\\.html','news.php?quickactiontype=unpublish')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('news/unpublished.html','index.php?site=news&action=unpublished','a:0:{}','index\\\\.php\\\\?site=news[&|&amp;]*action=unpublished','news/unpublished.html','news\\\\/unpublished\\\\.html','index.php?site=news&action=unpublished')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('newsletter.html','index.php?site=newsletter','a:0:{}','index\\\\.php\\\\?site=newsletter','newsletter.html','newsletter\\\\.html','index.php?site=newsletter')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('newsletter/delete.html','index.php?site=newsletter&action=delete','a:0:{}','index\\\\.php\\\\?site=newsletter[&|&amp;]*action=delete','newsletter/delete.html','newsletter\\\\/delete\\\\.html','index.php?site=newsletter&action=delete')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('newsletter/forgot.html','index.php?site=newsletter&action=forgot','a:0:{}','index\\\\.php\\\\?site=newsletter[&|&amp;]*action=forgot','newsletter/forgot.html','newsletter\\\\/forgot\\\\.html','index.php?site=newsletter&action=forgot')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('newsletter/save.html','index.php?site=newsletter&action=save','a:0:{}','index\\\\.php\\\\?site=newsletter[&|&amp;]*action=save','newsletter/save.html','newsletter\\\\/save\\\\.html','index.php?site=newsletter&action=save')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('polls.html','index.php?site=polls','a:0:{}','index\\\\.php\\\\?site=polls','polls.html','polls\\\\.html','index.php?site=polls')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('polls/{pollID}.html','index.php?site=polls&pollID={pollID}','a:1:{s:6:\"pollID\";s:7:\"integer\";}','index\\\\.php\\\\?site=polls[&|&amp;]*pollID=([0-9]+)','polls/$3.html','polls\\\\/([0-9]+?)\\\\.html','index.php?site=polls&pollID=$1')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('polls/{pollID}/edit.html','index.php?site=polls&action=edit&pollID={pollID}','a:1:{s:6:\"pollID\";s:7:\"integer\";}','index\\\\.php\\\\?site=polls[&|&amp;]*action=edit[&|&amp;]*pollID=([0-9]+)','polls/$3/edit.html','polls\\\\/([0-9]+?)\\\\/edit\\\\.html','index.php?site=polls&action=edit&pollID=$1')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('polls/{pollID}/vote.html','index.php?site=polls&vote={pollID}','a:1:{s:6:\"pollID\";s:7:\"integer\";}','index\\\\.php\\\\?site=polls[&|&amp;]*vote=([0-9]+)','polls/$3/vote.html','polls\\\\/([0-9]+?)\\\\/vote\\\\.html','index.php?site=polls&vote=$1')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('polls/new.html','index.php?site=polls&action=new','a:0:{}','index\\\\.php\\\\?site=polls[&|&amp;]*action=new','polls/new.html','polls\\\\/new\\\\.html','index.php?site=polls&action=new')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('profile/{action}/{id}.html','index.php?site=profile&id={id}&action={action}','a:2:{s:2:\"id\";s:7:\"integer\";s:6:\"action\";s:6:\"string\";}','index\\\\.php\\\\?site=profile[&|&amp;]*id=([0-9]+)[&|&amp;]*action=(\\\\w*?)','profile/$4/$3.html','profile\\\\/(\\\\w*?)\\\\/([0-9]+?)\\\\.html','index.php?site=profile&id=$2&action=$1')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('profile/{action}/{id}.html','index.php?site=profile&action={action}&id={id}','a:2:{s:2:\"id\";s:7:\"integer\";s:6:\"action\";s:6:\"string\";}','index\\\\.php\\\\?site=profile[&|&amp;]*action=(\\\\w*?)[&|&amp;]*id=([0-9]+)','profile/$3/$4.html','profile\\\\/(\\\\w*?)\\\\/([0-9]+?)\\\\.html','index.php?site=profile&action=$1&id=$2')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('profile/{id}.html','index.php?site=profile&id={id}','a:1:{s:2:\"id\";s:7:\"integer\";}','index\\\\.php\\\\?site=profile[&|&amp;]*id=([0-9]+)','profile/$3.html','profile\\\\/([0-9]+?)\\\\.html','index.php?site=profile&id=$1')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('profile/edit.html','index.php?site=myprofile','a:0:{}','index\\\\.php\\\\?site=myprofile','profile/edit.html','profile\\\\/edit\\\\.html','index.php?site=myprofile')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('profile/mail.html','index.php?site=myprofile&action=editmail','a:0:{}','index\\\\.php\\\\?site=myprofile[&|&amp;]*action=editmail','profile/mail.html','profile\\\\/mail\\\\.html','index.php?site=myprofile&action=editmail')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('profile/password.html','index.php?site=myprofile&action=editpwd','a:0:{}','index\\\\.php\\\\?site=myprofile[&|&amp;]*action=editpwd','profile/password.html','profile\\\\/password\\\\.html','index.php?site=myprofile&action=editpwd')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('register.html','index.php?site=register','a:0:{}','index\\\\.php\\\\?site=register','register.html','register\\\\.html','index.php?site=register')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('search.html','index.php?site=search','a:0:{}','index\\\\.php\\\\?site=search','search.html','search\\\\.html','index.php?site=search')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('search/results.html','index.php?site=search&action=search','a:0:{}','index\\\\.php\\\\?site=search[&|&amp;]*action=search','search/results.html','search\\\\/results\\\\.html','index.php?site=search&action=search')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('search/submit.html','search.php','a:0:{}','search\\\\.php','search/submit.html','search\\\\/submit\\\\.html','search.php')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('server.html','index.php?site=server','a:0:{}','index\\\\.php\\\\?site=server','server.html','server\\\\.html','index.php?site=server')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('shoutbox.html','index.php?site=shoutbox_content&action=showall','a:0:{}','index\\\\.php\\\\?site=shoutbox_content[&|&amp;]*action=showall','shoutbox.html','shoutbox\\\\.html','index.php?site=shoutbox_content&action=showall')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('shoutbox/delete.html','shoutbox_content.php?action=delete','a:0:{}','shoutbox_content\\\\.php\\\\?action=delete','shoutbox/delete.html','shoutbox\\\\/delete\\\\.html','shoutbox_content.php?action=delete')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('sponsors.html','index.php?site=sponsors','a:0:{}','index\\\\.php\\\\?site=sponsors','sponsors.html','sponsors\\\\.html','index.php?site=sponsors')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('squads.html','index.php?site=squads','a:0:{}','index\\\\.php\\\\?site=squads','squads.html','squads\\\\.html','index.php?site=squads')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('squads/{squadID}.html','index.php?site=squads&action=show&squadID={squadID}','a:1:{s:7:\"squadID\";s:7:\"integer\";}','index\\\\.php\\\\?site=squads[&|&amp;]*action=show[&|&amp;]*squadID=([0-9]+)','squads/$3.html','squads\\\\/([0-9]+?)\\\\.html','index.php?site=squads&action=show&squadID=$1')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('usergallery.html','index.php?site=usergallery','a:0:{}','index\\\\.php\\\\?site=usergallery','usergallery.html','usergallery\\\\.html','index.php?site=usergallery')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('usergallery/{galleryID}/edit.html','index.php?site=usergallery&action=edit&galleryID={galleryID}','a:1:{s:9:\"galleryID\";s:7:\"integer\";}','index\\\\.php\\\\?site=usergallery[&|&amp;]*action=edit[&|&amp;]*galleryID=([0-9]+)','usergallery/$3/edit.html','usergallery\\\\/([0-9]+?)\\\\/edit\\\\.html','index.php?site=usergallery&action=edit&galleryID=$1')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('usergallery/{galleryID}/upload.html','index.php?site=usergallery&action=upload&upload=form&galleryID={galleryID}','a:1:{s:9:\"galleryID\";s:7:\"integer\";}','index\\\\.php\\\\?site=usergallery[&|&amp;]*action=upload[&|&amp;]*upload=form[&|&amp;]*galleryID=([0-9]+)','usergallery/$3/upload.html','usergallery\\\\/([0-9]+?)\\\\/upload\\\\.html','index.php?site=usergallery&action=upload&upload=form&galleryID=$1')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('usergallery/add.html','index.php?site=usergallery&action=add','a:0:{}','index\\\\.php\\\\?site=usergallery[&|&amp;]*action=add','usergallery/add.html','usergallery\\\\/add\\\\.html','index.php?site=usergallery&action=add')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('users.html','index.php?site=registered_users','a:0:{}','index\\\\.php\\\\?site=registered_users','users.html','users\\\\.html','index.php?site=registered_users')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('users/{type}/{sort}/{page}.html','index.php?site=registered_users&page={page}&sort={sort}&type={type}','a:3:{s:4:\"page\";s:7:\"integer\";s:4:\"sort\";s:6:\"string\";s:4:\"type\";s:6:\"string\";}','index\\\\.php\\\\?site=registered_users[&|&amp;]*page=([0-9]+)[&|&amp;]*sort=(\\\\w*?)[&|&amp;]*type=(\\\\w*?)','users/$5/$4/$3.html','users\\\\/(\\\\w*?)\\\\/(\\\\w*?)\\\\/([0-9]+?)\\\\.html','index.php?site=registered_users&page=$3&sort=$2&type=$1')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('users/ASC/{sort}/{page}.html','index.php?site=registered_users&sort={sort}&page={page}','a:2:{s:4:\"sort\";s:6:\"string\";s:4:\"page\";s:7:\"integer\";}','index\\\\.php\\\\?site=registered_users[&|&amp;]*sort=(\\\\w*?)[&|&amp;]*page=([0-9]+)','users/ASC/$3/$4.html','users\\\\/ASC\\\\/(\\\\w*?)\\\\/([0-9]+?)\\\\.html','index.php?site=registered_users&sort=$1&page=$2')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('whoisonline.html','index.php?site=whoisonline','a:0:{}','index\\\\.php\\\\?site=whoisonline','whoisonline.html','whoisonline\\\\.html','index.php?site=whoisonline')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('whoisonline.html#was','index.php?site=whoisonline#was','a:0:{}','index\\\\.php\\\\?site=whoisonline#was','whoisonline.html#was','whoisonline\\\\.html#was','index.php?site=whoisonline#was')");
    $transaction->addQuery("INSERT INTO `" . PREFIX . "modrewrite` (`regex`, `link`, `fields`, `replace_regex`, `replace_result`, `rebuild_regex`, `rebuild_result`) VALUES('whoisonline/{sort}/{type}.html','index.php?site=whoisonline&sort={sort}&type={type}','a:2:{s:4:\"sort\";s:6:\"string\";s:4:\"type\";s:6:\"string\";}','index\\\\.php\\\\?site=whoisonline[&|&amp;]*sort=(\\\\w*?)[&|&amp;]*type=(\\\\w*?)','whoisonline/$3/$4.html','whoisonline\\\\/(\\\\w*?)\\\\/(\\\\w*?)\\\\.html','index.php?site=whoisonline&sort=$1&type=$2')");

    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "cookies`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "cookies` (
  `userID` int(11) NOT NULL,
  `cookie` binary(64) NOT NULL,
  `expiration` int(14) NOT NULL,
  PRIMARY KEY (`userID`, `cookie`),
  INDEX (`expiration`)
    )");

    updateMySQLConfig();
    if ($transaction->successful()) {
        return array('status' => 'success', 'message' => 'Updated to webSPELL 4.3 Part 2');
    } else {
        return array('status' => 'fail', 'message' => 'Failed to update to webSPELL 4.3 Part 2<br/>' . $transaction->getError());
    }
}

function update_430a_121($_database) 
{
    $transaction = new Transaction($_database);
    
    // Plugin-Manager
	$transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "plugins`");
	$transaction->addQuery("CREATE TABLE `" . PREFIX . "plugins` (
   `pluginID` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `description` varchar(255) NOT NULL,
  `admin_file` text NOT NULL,
  `activate` int(1) NOT NULL DEFAULT '1',
  `author` varchar(200) NOT NULL DEFAULT '',
  `website` varchar(200) NOT NULL DEFAULT '',
  `index_link` varchar(255) NOT NULL DEFAULT '',
  `sc_link` varchar(20) NOT NULL DEFAULT '',
  `hiddenfiles` varchar(255) NOT NULL,
  `version` varchar(10) NOT NULL DEFAULT '',
  `path` varchar(255) NOT NULL DEFAULT '',
	  PRIMARY KEY  (`pluginID`)
	) AUTO_INCREMENT=1
	  DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_unicode_ci");
	  
	      
    if ($transaction->successful()) {
        return array('status' => 'success', 'message' => 'Updated to webSPELL NOR 1.2.1');
    } else {
        return array('status' => 'fail', 'message' => 'Failed to update to webSPELL NOR 1.2.1<br/>' . $transaction->getError());
    }
}

function update_122_123($_database) {
	$transaction = new Transaction($_database);
	
	$transaction->addQuery("ALTER TABLE ".PREFIX."user ADD password_hash VARCHAR(255) NOT NULL AFTER password");
	$transaction->addQuery("ALTER TABLE ".PREFIX."user ADD password_pepper VARCHAR(255) NOT NULL AFTER password_hash");
	$transaction->addQuery("ALTER TABLE `".PREFIX."plugins` CHANGE `install` `hiddenfiles` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT ''");
	$transaction->addQuery("ALTER TABLE `".PREFIX."plugins` CHANGE `desc` `admin_file` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL");
	$transaction->addQuery("ALTER TABLE `".PREFIX."plugins` CHANGE `index_link` `index_link` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT ''");
	
	if ($transaction->successful()) {
        return array('status' => 'success', 'message' => 'Updated to webSPELL NOR 1.2.3');
    } else {
        return array('status' => 'fail', 'message' => 'Failed to update to webSPELL NOR 1.2.3<br/>' . $transaction->getError());
    }
}

function update_123_124($_database) {
	$transaction = new Transaction($_database);
	
	$transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "styles`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "styles` (
  `styleID` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL default '',
  `win` varchar(255) NOT NULL default '',
  `loose` varchar(255) NOT NULL default '',
  `draw` varchar(255) NOT NULL default '',
  `nav1` varchar(255) NOT NULL default '',
  `nav2` varchar(255) NOT NULL default '',
  `nav3` varchar(255) NOT NULL default '',
  `nav4` varchar(255) NOT NULL default '',
  `nav5` varchar(255) NOT NULL default '',
  `nav6` varchar(255) NOT NULL default '',
  `body1` varchar(255) NOT NULL default '',
  `body2` varchar(255) NOT NULL default '',
  `body3` varchar(255) NOT NULL default '',
  `body4` varchar(255) NOT NULL default '',
  `typo1` varchar(255) NOT NULL default '',
  `typo2` varchar(255) NOT NULL default '',
  `typo3` varchar(255) NOT NULL default '',
  `typo4` varchar(255) NOT NULL default '',
  `typo5` varchar(255) NOT NULL default '',
  `typo6` varchar(255) NOT NULL default '',
  `typo7` varchar(255) NOT NULL default '',
  `typo8` varchar(255) NOT NULL default '',
  `foot1` varchar(255) NOT NULL default '',
  `foot2` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`styleID`)
) AUTO_INCREMENT=2
  DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_unicode_ci");

    $transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "styles` (`styleID`, `title`, `win`, `loose`, `draw`, `nav1`, `nav2`, `nav3`, `nav4`, `nav5`, `nav6`, `body1`, `body2`, `body3`, `body4`, `typo1`, `typo2`, `typo3`, `typo4`, `typo5`, `typo6`, `typo7`, `typo8`, `foot1`, `foot2`) VALUES (1, 'WebSPELL NOR', '#00cc00', '#dd0000', '#ff6600', '#ffffff', '16px', '#000000', '#5bc0de', '#5bc0de', '3px', 'Helvetica Neue, Helvetica, Arial, sans-serif', '13px', '#ffffff', '#000000', '#6a6565', '#5bc0de', '#999999', '#5bc0de', '13px', '#5bc0de', '1px', '#000000', '#726868', '#ffffff')");

  $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "buttons`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "buttons` (
  `buttonID` int(11) NOT NULL AUTO_INCREMENT,
  `button1` varchar(255) NOT NULL default '',
  `button2` varchar(255) NOT NULL default '',
  `button3` varchar(255) NOT NULL default '',
  `button4` varchar(255) NOT NULL default '',
  `button5` varchar(255) NOT NULL default '',
  `button6` varchar(255) NOT NULL default '',
  `button7` varchar(255) NOT NULL default '',
  `button8` varchar(255) NOT NULL default '',
  `button9` varchar(255) NOT NULL default '',
  `button10` varchar(255) NOT NULL default '',
  `button11` varchar(255) NOT NULL default '',
  `button12` varchar(255) NOT NULL default '',
  `button13` varchar(255) NOT NULL default '',
  `button14` varchar(255) NOT NULL default '',
  `button15` varchar(255) NOT NULL default '',
  `button16` varchar(255) NOT NULL default '',
  `button17` varchar(255) NOT NULL default '',
  `button18` varchar(255) NOT NULL default '',
  `button19` varchar(255) NOT NULL default '',
  `button20` varchar(255) NOT NULL default '',
  `button21` varchar(255) NOT NULL default '',
  `button22` varchar(255) NOT NULL default '',
  `button23` varchar(255) NOT NULL default '',
  `button24` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`buttonID`)
) AUTO_INCREMENT=2
  DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_unicode_ci");

    $transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "buttons` (`buttonID`, `button1`, `button2`, `button3`, `button4`, `button5`, `button6`, `button7`, `button8`, `button9`, `button10`, `button11`, `button12`, `button13`, `button14`, `button15`, `button16`, `button17`, `button18`, `button19`, `button20`, `button21`, `button22`, `button23`, `button24`) VALUES (1, '#ffffff', '#e6e6e6', '#333333', '#0088cc', '#0044cc', '#ffffff', '#5cb85c', '#449d44', '#ffffff', '#5bc0de', '#2f96b4', '#ffffff', '#ef7814', '#f89406', '#ffffff', '#da0c0c', '#950d0d', '#ffffff', '#adadad', '#2e6da4', '#398439', '#269abc', '#d58512', '#ac2925')");

    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "moduls`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "moduls` (
  `modulID` int(11) NOT NULL AUTO_INCREMENT,
  `module` varchar(255) NOT NULL default '',
  `le_activated` int(11) NOT NULL default '0',
  `re_activated` int(11) NOT NULL default '0',
  `activated` int(11) NOT NULL default '0',
  `sort` int(11) NOT NULL default '0',
  PRIMARY KEY  (`modulID`)
) AUTO_INCREMENT=44
   DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_unicode_ci");

	$transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "moduls` (`modulID`, `module`, `le_activated`, `re_activated`, `activated`, `sort`) VALUES (1, 'forum', 0, 0, 0, 14)");
	$transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "moduls` (`modulID`, `module`, `le_activated`, `re_activated`, `activated`, `sort`) VALUES (2, 'news', 0, 0, 0, 28)");
	$transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "moduls` (`modulID`, `module`, `le_activated`, `re_activated`, `activated`, `sort`) VALUES (3, 'faq', 0, 0, 0, 12)");
	$transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "moduls` (`modulID`, `module`, `le_activated`, `re_activated`, `activated`, `sort`) VALUES (4, 'squads', 0, 0, 0, 40)");
	$transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "moduls` (`modulID`, `module`, `le_activated`, `re_activated`, `activated`, `sort`) VALUES (5, 'about', 0, 0, 0, 1)");
	$transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "moduls` (`modulID`, `module`, `le_activated`, `re_activated`, `activated`, `sort`) VALUES (6, 'articles', 0, 0, 0, 2)");
	$transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "moduls` (`modulID`, `module`, `le_activated`, `re_activated`, `activated`, `sort`) VALUES (7, 'forum_topic', 0, 0, 0, 15)");
	$transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "moduls` (`modulID`, `module`, `le_activated`, `re_activated`, `activated`, `sort`) VALUES (8, 'loginoverview', 0, 0, 0, 23)");
	$transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "moduls` (`modulID`, `module`, `le_activated`, `re_activated`, `activated`, `sort`) VALUES (9, 'cashbox', 0, 0, 0, 6)");
	$transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "moduls` (`modulID`, `module`, `le_activated`, `re_activated`, `activated`, `sort`) VALUES (10, 'buddies', 0, 0, 0, 4)");
	$transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "moduls` (`modulID`, `module`, `le_activated`, `re_activated`, `activated`, `sort`) VALUES (11, 'messenger', 0, 0, 0, 26)");
	$transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "moduls` (`modulID`, `module`, `le_activated`, `re_activated`, `activated`, `sort`) VALUES (12, 'myprofile', 0, 0, 0, 27)");
	$transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "moduls` (`modulID`, `module`, `le_activated`, `re_activated`, `activated`, `sort`) VALUES (13, 'profile', 0, 0, 0, 34)");
	$transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "moduls` (`modulID`, `module`, `le_activated`, `re_activated`, `activated`, `sort`) VALUES (14, 'usergallery', 0, 0, 0, 41)");
	$transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "moduls` (`modulID`, `module`, `le_activated`, `re_activated`, `activated`, `sort`) VALUES (15, 'awards', 0, 0, 0, 3)");
	$transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "moduls` (`modulID`, `module`, `le_activated`, `re_activated`, `activated`, `sort`) VALUES (16, 'calendar', 0, 0, 0, 5)");
	$transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "moduls` (`modulID`, `module`, `le_activated`, `re_activated`, `activated`, `sort`) VALUES (17, 'challenge', 0, 0, 0, 7)");
	$transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "moduls` (`modulID`, `module`, `le_activated`, `re_activated`, `activated`, `sort`) VALUES (18, 'clanwars', 0, 0, 0, 8)");
	$transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "moduls` (`modulID`, `module`, `le_activated`, `re_activated`, `activated`, `sort`) VALUES (19, 'contact', 0, 0, 0, 9)");
	$transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "moduls` (`modulID`, `module`, `le_activated`, `re_activated`, `activated`, `sort`) VALUES (20, 'counter_stats', 0, 0, 0, 10)");
	$transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "moduls` (`modulID`, `module`, `le_activated`, `re_activated`, `activated`, `sort`) VALUES (21, 'demos', 0, 0, 0, 11)");
	$transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "moduls` (`modulID`, `module`, `le_activated`, `re_activated`, `activated`, `sort`) VALUES (22, 'files', 0, 0, 0, 13)");
	$transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "moduls` (`modulID`, `module`, `le_activated`, `re_activated`, `activated`, `sort`) VALUES (23, 'gallery', 0, 0, 0, 16)");
	$transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "moduls` (`modulID`, `module`, `le_activated`, `re_activated`, `activated`, `sort`) VALUES (24, 'guestbook', 0, 0, 0, 17)");
	$transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "moduls` (`modulID`, `module`, `le_activated`, `re_activated`, `activated`, `sort`) VALUES (25, 'history', 0, 0, 0, 18)");
	$transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "moduls` (`modulID`, `module`, `le_activated`, `re_activated`, `activated`, `sort`) VALUES (26, 'imprint', 0, 0, 0, 19)");
	$transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "moduls` (`modulID`, `module`, `le_activated`, `re_activated`, `activated`, `sort`) VALUES (27, 'joinus', 0, 0, 0, 20)");
	$transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "moduls` (`modulID`, `module`, `le_activated`, `re_activated`, `activated`, `sort`) VALUES (28, 'links', 0, 0, 0, 21)");
	$transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "moduls` (`modulID`, `module`, `le_activated`, `re_activated`, `activated`, `sort`) VALUES (29, 'linkus', 0, 0, 0, 22)");
	$transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "moduls` (`modulID`, `module`, `le_activated`, `re_activated`, `activated`, `sort`) VALUES (30, 'lostpassword', 0, 0, 0, 24)");
	$transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "moduls` (`modulID`, `module`, `le_activated`, `re_activated`, `activated`, `sort`) VALUES (31, 'members', 0, 0, 0, 25)");
	$transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "moduls` (`modulID`, `module`, `le_activated`, `re_activated`, `activated`, `sort`) VALUES (32, 'newsletter', 0, 0, 0, 30)");
	$transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "moduls` (`modulID`, `module`, `le_activated`, `re_activated`, `activated`, `sort`) VALUES (34, 'partners', 0, 0, 0, 31)");
	$transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "moduls` (`modulID`, `module`, `le_activated`, `re_activated`, `activated`, `sort`) VALUES (35, 'poll', 0, 0, 0, 32)");
	$transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "moduls` (`modulID`, `module`, `le_activated`, `re_activated`, `activated`, `sort`) VALUES (36, 'polls', 0, 0, 0, 33)");
	$transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "moduls` (`modulID`, `module`, `le_activated`, `re_activated`, `activated`, `sort`) VALUES (37, 'register', 0, 0, 0, 35)");
	$transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "moduls` (`modulID`, `module`, `le_activated`, `re_activated`, `activated`, `sort`) VALUES (38, 'registered_users', 0, 0, 0, 36)");
	$transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "moduls` (`modulID`, `module`, `le_activated`, `re_activated`, `activated`, `sort`) VALUES (39, 'search', 0, 0, 0, 37)");
	$transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "moduls` (`modulID`, `module`, `le_activated`, `re_activated`, `activated`, `sort`) VALUES (40, 'server', 0, 0, 0, 38)");
	$transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "moduls` (`modulID`, `module`, `le_activated`, `re_activated`, `activated`, `sort`) VALUES (41, 'sponsors', 0, 0, 0, 39)");
	$transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "moduls` (`modulID`, `module`, `le_activated`, `re_activated`, `activated`, `sort`) VALUES (42, 'whoisonline', 0, 0, 0, 42)");
	$transaction->addQuery("INSERT IGNORE INTO `" . PREFIX . "moduls` (`modulID`, `module`, `le_activated`, `re_activated`, `activated`, `sort`) VALUES (43, 'news_comments', 0, 0, 0, 29)");
	
	if ($transaction->successful()) {
        return array('status' => 'success', 'message' => 'Updated to webSPELL NOR 1.2.4');
    } else {
        return array('status' => 'fail', 'message' => 'Failed to update to webSPELL NOR 1.2.4<br/>' . $transaction->getError());
    }
}

function update_124_125($_database) {
	
	$transaction = new Transaction($_database);
	$transaction->addQuery("ALTER TABLE `".PREFIX."settings` ADD register_per_ip INT(1) NOT NULL default '1'");
	//smileys
    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "smileys`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "smileys` (
  `smileyID` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL default '',
  `alt` varchar(255) NOT NULL default '',
  `pattern` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`smileyID`),
  UNIQUE KEY `name` (`name`)
) AUTO_INCREMENT=16
  DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_unicode_ci");

    $transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('--1', '--1', ':--1:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('-1', '-1', ':-1:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('100', '100', ':100:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('1234', '1234', ':1234:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('8ball', '8ball', ':8ball:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('a', 'a', ':a:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('ab', 'ab', ':ab:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('abc', 'abc', ':abc:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('abcd', 'abcd', ':abcd:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('accept', 'accept', ':accept:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('aerial_tramway', 'aerial_tramway', ':aerial_tramway:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('airplane', 'airplane', ':airplane:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('alarm_clock', 'alarm_clock', ':alarm_clock:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('alien', 'alien', ':alien:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('ambulance', 'ambulance', ':ambulance:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('anchor', 'anchor', ':anchor:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('angel', 'angel', ':angel:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('anger', 'anger', ':anger:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('angry', 'angry', ':angry:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('anguished', 'anguished', ':anguished:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('ant', 'ant', ':ant:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('apple', 'apple', ':apple:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('aquarius', 'aquarius', ':aquarius:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('aries', 'aries', ':aries:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('arrow_backward', 'arrow_backward', ':arrow_backward:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('arrow_double_down', 'arrow_double_down', ':arrow_double_down:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('arrow_double_up', 'arrow_double_up', ':arrow_double_up:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('arrow_down', 'arrow_down', ':arrow_down:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('arrow_down_small', 'arrow_down_small', ':arrow_down_small:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('arrow_forward', 'arrow_forward', ':arrow_forward:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('arrow_heading_down', 'arrow_heading_down', ':arrow_heading_down:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('arrow_heading_up', 'arrow_heading_up', ':arrow_heading_up:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('arrow_left', 'arrow_left', ':arrow_left:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('arrow_lower_left', 'arrow_lower_left', ':arrow_lower_left:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('arrow_lower_right', 'arrow_lower_right', ':arrow_lower_right:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('arrow_right', 'arrow_right', ':arrow_right:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('arrow_right_hook', 'arrow_right_hook', ':arrow_right_hook:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('arrow_up', 'arrow_up', ':arrow_up:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('arrow_up_down', 'arrow_up_down', ':arrow_up_down:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('arrow_up_small', 'arrow_up_small', ':arrow_up_small:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('arrow_upper_left', 'arrow_upper_left', ':arrow_upper_left:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('arrow_upper_right', 'arrow_upper_right', ':arrow_upper_right:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('arrows_clockwise', 'arrows_clockwise', ':arrows_clockwise:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('arrows_counterclockwise', 'arrows_counterclockwise', ':arrows_counterclockwise:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('art', 'art', ':art:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('articulated_lorry', 'articulated_lorry', ':articulated_lorry:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('astonished', 'astonished', ':astonished:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('atm', 'atm', ':atm:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('b', 'b', ':b:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('baby', 'baby', ':baby:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('baby_bottle', 'baby_bottle', ':baby_bottle:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('baby_chick', 'baby_chick', ':baby_chick:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('baby_symbol', 'baby_symbol', ':baby_symbol:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('back', 'back', ':back:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('baggage_claim', 'baggage_claim', ':baggage_claim:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('balloon', 'balloon', ':balloon:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('ballot_box_with_check', 'ballot_box_with_check', ':ballot_box_with_check:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('bamboo', 'bamboo', ':bamboo:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('banana', 'banana', ':banana:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('bangbang', 'bangbang', ':bangbang:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('bank', 'bank', ':bank:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('bar_chart', 'bar_chart', ':bar_chart:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('barber', 'barber', ':barber:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('baseball', 'baseball', ':baseball:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('basketball', 'basketball', ':basketball:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('bath', 'bath', ':bath:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('bathtub', 'bathtub', ':bathtub:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('battery', 'battery', ':battery:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('bear', 'bear', ':bear:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('bee', 'bee', ':bee:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('beer', 'beer', ':beer:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('beers', 'beers', ':beers:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('beetle', 'beetle', ':beetle:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('beginner', 'beginner', ':beginner:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('bell', 'bell', ':bell:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('bento', 'bento', ':bento:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('bicyclist', 'bicyclist', ':bicyclist:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('bike', 'bike', ':bike:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('bikini', 'bikini', ':bikini:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('bird', 'bird', ':bird:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('birthday', 'birthday', ':birthday:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('black_circle', 'black_circle', ':black_circle:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('black_joker', 'black_joker', ':black_joker:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('black_medium_small_square', 'black_medium_small_square', ':black_medium_small_square:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('black_medium_square', 'black_medium_square', ':black_medium_square:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('black_nib', 'black_nib', ':black_nib:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('black_small_square', 'black_small_square', ':black_small_square:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('black_square', 'black_square', ':black_square:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('black_square_button', 'black_square_button', ':black_square_button:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('blossom', 'blossom', ':blossom:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('blowfish', 'blowfish', ':blowfish:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('blue_book', 'blue_book', ':blue_book:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('blue_car', 'blue_car', ':blue_car:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('blue_heart', 'blue_heart', ':blue_heart:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('blush', 'blush', ':blush:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('boar', 'boar', ':boar:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('boat', 'boat', ':boat:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('bomb', 'bomb', ':bomb:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('book', 'book', ':book:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('bookmark', 'bookmark', ':bookmark:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('bookmark_tabs', 'bookmark_tabs', ':bookmark_tabs:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('books', 'books', ':books:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('boom', 'boom', ':boom:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('boot', 'boot', ':boot:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('bouquet', 'bouquet', ':bouquet:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('bow', 'bow', ':bow:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('bowling', 'bowling', ':bowling:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('bowtie', 'bowtie', ':bowtie:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('boy', 'boy', ':boy:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('bread', 'bread', ':bread:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('bride_with_veil', 'bride_with_veil', ':bride_with_veil:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('bridge_at_night', 'bridge_at_night', ':bridge_at_night:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('briefcase', 'briefcase', ':briefcase:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('broken_heart', 'broken_heart', ':broken_heart:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('bug', 'bug', ':bug:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('bulb', 'bulb', ':bulb:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('bullettrain_front', 'bullettrain_front', ':bullettrain_front:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('bullettrain_side', 'bullettrain_side', ':bullettrain_side:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('bus', 'bus', ':bus:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('busstop', 'busstop', ':busstop:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('bust_in_silhouette', 'bust_in_silhouette', ':bust_in_silhouette:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('busts_in_silhouette', 'busts_in_silhouette', ':busts_in_silhouette:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('cactus', 'cactus', ':cactus:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('cake', 'cake', ':cake:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('calendar', 'calendar', ':calendar:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('calling', 'calling', ':calling:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('camel', 'camel', ':camel:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('camera', 'camera', ':camera:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('cancer', 'cancer', ':cancer:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('candy', 'candy', ':candy:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('capital_abcd', 'capital_abcd', ':capital_abcd:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('capricorn', 'capricorn', ':capricorn:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('car', 'car', ':car:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('card_index', 'card_index', ':card_index:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('carousel_horse', 'carousel_horse', ':carousel_horse:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('cat', 'cat', ':cat:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('cat2', 'cat2', ':cat2:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('cd', 'cd', ':cd:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('chart', 'chart', ':chart:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('chart_with_downwards_trend', 'chart_with_downwards_trend', ':chart_with_downwards_trend:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('chart_with_upwards_trend', 'chart_with_upwards_trend', ':chart_with_upwards_trend:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('checkered_flag', 'checkered_flag', ':checkered_flag:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('cherries', 'cherries', ':cherries:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('cherry_blossom', 'cherry_blossom', ':cherry_blossom:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('chestnut', 'chestnut', ':chestnut:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('chicken', 'chicken', ':chicken:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('children_crossing', 'children_crossing', ':children_crossing:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('chocolate_bar', 'chocolate_bar', ':chocolate_bar:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('christmas_tree', 'christmas_tree', ':christmas_tree:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('church', 'church', ':church:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('cinema', 'cinema', ':cinema:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('circus_tent', 'circus_tent', ':circus_tent:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('city_sunrise', 'city_sunrise', ':city_sunrise:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('city_sunset', 'city_sunset', ':city_sunset:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('cl', 'cl', ':cl:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('clap', 'clap', ':clap:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('clapper', 'clapper', ':clapper:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('clipboard', 'clipboard', ':clipboard:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('clock1', 'clock1', ':clock1:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('clock10', 'clock10', ':clock10:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('clock1030', 'clock1030', ':clock1030:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('clock11', 'clock11', ':clock11:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('clock1130', 'clock1130', ':clock1130:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('clock12', 'clock12', ':clock12:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('clock1230', 'clock1230', ':clock1230:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('clock130', 'clock130', ':clock130:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('clock2', 'clock2', ':clock2:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('clock230', 'clock230', ':clock230:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('clock3', 'clock3', ':clock3:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('clock330', 'clock330', ':clock330:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('clock4', 'clock4', ':clock4:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('clock430', 'clock430', ':clock430:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('clock5', 'clock5', ':clock5:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('clock530', 'clock530', ':clock530:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('clock6', 'clock6', ':clock6:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('clock630', 'clock630', ':clock630:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('clock7', 'clock7', ':clock7:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('clock730', 'clock730', ':clock730:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('clock8', 'clock8', ':clock8:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('clock830', 'clock830', ':clock830:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('clock9', 'clock9', ':clock9:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('clock930', 'clock930', ':clock930:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('closed_book', 'closed_book', ':closed_book:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('closed_lock_with_key', 'closed_lock_with_key', ':closed_lock_with_key:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('closed_umbrella', 'closed_umbrella', ':closed_umbrella:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('cloud', 'cloud', ':cloud:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('clubs', 'clubs', ':clubs:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('cn', 'cn', ':cn:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('cocktail', 'cocktail', ':cocktail:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('coffee', 'coffee', ':coffee:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('cold_sweat', 'cold_sweat', ':cold_sweat:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('collision', 'collision', ':collision:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('computer', 'computer', ':computer:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('confetti_ball', 'confetti_ball', ':confetti_ball:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('confounded', 'confounded', ':confounded:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('confused', 'confused', ':confused:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('congratulations', 'congratulations', ':congratulations:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('construction', 'construction', ':construction:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('construction_worker', 'construction_worker', ':construction_worker:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('convenience_store', 'convenience_store', ':convenience_store:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('cookie', 'cookie', ':cookie:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('cool', 'cool', ':cool:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('cop', 'cop', ':cop:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('copyright', 'copyright', ':copyright:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('corn', 'corn', ':corn:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('couple', 'couple', ':couple:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('couple_with_heart', 'couple_with_heart', ':couple_with_heart:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('couplekiss', 'couplekiss', ':couplekiss:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('cow', 'cow', ':cow:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('cow2', 'cow2', ':cow2:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('credit_card', 'credit_card', ':credit_card:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('crocodile', 'crocodile', ':crocodile:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('crossed_flags', 'crossed_flags', ':crossed_flags:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('crown', 'crown', ':crown:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('cry', 'cry', ':cry:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('crying_cat_face', 'crying_cat_face', ':crying_cat_face:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('crystal_ball', 'crystal_ball', ':crystal_ball:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('cupid', 'cupid', ':cupid:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('curly_loop', 'curly_loop', ':curly_loop:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('currency_exchange', 'currency_exchange', ':currency_exchange:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('curry', 'curry', ':curry:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('custard', 'custard', ':custard:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('customs', 'customs', ':customs:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('cyclone', 'cyclone', ':cyclone:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('dancer', 'dancer', ':dancer:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('dancers', 'dancers', ':dancers:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('dango', 'dango', ':dango:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('dart', 'dart', ':dart:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('dash', 'dash', ':dash:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('date', 'date', ':date:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('de', 'de', ':de:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('deciduous_tree', 'deciduous_tree', ':deciduous_tree:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('department_store', 'department_store', ':department_store:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('diamond_shape_with_a_dot_inside', 'diamond_shape_with_a_dot_inside', ':diamond_shape_with_a_dot_inside:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('diamonds', 'diamonds', ':diamonds:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('disappointed', 'disappointed', ':disappointed:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('disappointed_relieved', 'disappointed_relieved', ':disappointed_relieved:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('dizzy', 'dizzy', ':dizzy:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('dizzy_face', 'dizzy_face', ':dizzy_face:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('do_not_litter', 'do_not_litter', ':do_not_litter:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('dog', 'dog', ':dog:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('dog2', 'dog2', ':dog2:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('dollar', 'dollar', ':dollar:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('dolls', 'dolls', ':dolls:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('dolphin', 'dolphin', ':dolphin:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('donut', 'donut', ':donut:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('door', 'door', ':door:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('doughnut', 'doughnut', ':doughnut:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('dragon', 'dragon', ':dragon:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('dragon_face', 'dragon_face', ':dragon_face:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('dress', 'dress', ':dress:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('dromedary_camel', 'dromedary_camel', ':dromedary_camel:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('droplet', 'droplet', ':droplet:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('dvd', 'dvd', ':dvd:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('e-mail', 'e-mail', ':e-mail:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('ear', 'ear', ':ear:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('ear_of_rice', 'ear_of_rice', ':ear_of_rice:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('earth_africa', 'earth_africa', ':earth_africa:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('earth_americas', 'earth_americas', ':earth_americas:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('earth_asia', 'earth_asia', ':earth_asia:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('egg', 'egg', ':egg:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('eggplant', 'eggplant', ':eggplant:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('eight', 'eight', ':eight:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('eight_pointed_black_star', 'eight_pointed_black_star', ':eight_pointed_black_star:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('eight_spoked_asterisk', 'eight_spoked_asterisk', ':eight_spoked_asterisk:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('electric_plug', 'electric_plug', ':electric_plug:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('elephant', 'elephant', ':elephant:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('email', 'email', ':email:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('end', 'end', ':end:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('envelope', 'envelope', ':envelope:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('es', 'es', ':es:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('euro', 'euro', ':euro:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('european_castle', 'european_castle', ':european_castle:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('european_post_office', 'european_post_office', ':european_post_office:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('evergreen_tree', 'evergreen_tree', ':evergreen_tree:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('exclamation', 'exclamation', ':exclamation:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('expressionless', 'expressionless', ':expressionless:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('eyeglasses', 'eyeglasses', ':eyeglasses:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('eyes', 'eyes', ':eyes:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('facepunch', 'facepunch', ':facepunch:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('factory', 'factory', ':factory:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('fallen_leaf', 'fallen_leaf', ':fallen_leaf:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('family', 'family', ':family:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('fast_forward', 'fast_forward', ':fast_forward:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('fax', 'fax', ':fax:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('fearful', 'fearful', ':fearful:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('feelsgood', 'feelsgood', ':feelsgood:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('feet', 'feet', ':feet:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('ferris_wheel', 'ferris_wheel', ':ferris_wheel:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('file_folder', 'file_folder', ':file_folder:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('finnadie', 'finnadie', ':finnadie:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('fire', 'fire', ':fire:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('fire_engine', 'fire_engine', ':fire_engine:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('fireworks', 'fireworks', ':fireworks:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('first_quarter_moon', 'first_quarter_moon', ':first_quarter_moon:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('first_quarter_moon_with_face', 'first_quarter_moon_with_face', ':first_quarter_moon_with_face:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('fish', 'fish', ':fish:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('fish_cake', 'fish_cake', ':fish_cake:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('fishing_pole_and_fish', 'fishing_pole_and_fish', ':fishing_pole_and_fish:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('fist', 'fist', ':fist:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('five', 'five', ':five:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('flags', 'flags', ':flags:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('flashlight', 'flashlight', ':flashlight:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('floppy_disk', 'floppy_disk', ':floppy_disk:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('flower_playing_cards', 'flower_playing_cards', ':flower_playing_cards:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('flushed', 'flushed', ':flushed:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('foggy', 'foggy', ':foggy:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('football', 'football', ':football:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('fork_and_knife', 'fork_and_knife', ':fork_and_knife:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('fountain', 'fountain', ':fountain:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('four', 'four', ':four:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('four_leaf_clover', 'four_leaf_clover', ':four_leaf_clover:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('fr', 'fr', ':fr:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('free', 'free', ':free:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('fried_shrimp', 'fried_shrimp', ':fried_shrimp:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('fries', 'fries', ':fries:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('frog', 'frog', ':frog:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('frowning', 'frowning', ':frowning:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('fu', 'fu', ':fu:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('fuelpump', 'fuelpump', ':fuelpump:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('full_moon', 'full_moon', ':full_moon:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('full_moon_with_face', 'full_moon_with_face', ':full_moon_with_face:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('game_die', 'game_die', ':game_die:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('gb', 'gb', ':gb:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('gem', 'gem', ':gem:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('gemini', 'gemini', ':gemini:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('ghost', 'ghost', ':ghost:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('gift', 'gift', ':gift:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('gift_heart', 'gift_heart', ':gift_heart:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('girl', 'girl', ':girl:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('globe_with_meridians', 'globe_with_meridians', ':globe_with_meridians:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('goat', 'goat', ':goat:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('goberserk', 'goberserk', ':goberserk:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('godmode', 'godmode', ':godmode:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('golf', 'golf', ':golf:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('grapes', 'grapes', ':grapes:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('green_apple', 'green_apple', ':green_apple:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('green_book', 'green_book', ':green_book:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('green_heart', 'green_heart', ':green_heart:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('grey_exclamation', 'grey_exclamation', ':grey_exclamation:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('grey_question', 'grey_question', ':grey_question:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('grimacing', 'grimacing', ':grimacing:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('grin', 'grin', ':grin:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('grinning', 'grinning', ':grinning:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('guardsman', 'guardsman', ':guardsman:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('guitar', 'guitar', ':guitar:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('gun', 'gun', ':gun:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('haircut', 'haircut', ':haircut:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('hamburger', 'hamburger', ':hamburger:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('hammer', 'hammer', ':hammer:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('hamster', 'hamster', ':hamster:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('hand', 'hand', ':hand:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('handbag', 'handbag', ':handbag:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('hankey', 'hankey', ':hankey:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('hash', 'hash', ':hash:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('hatched_chick', 'hatched_chick', ':hatched_chick:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('hatching_chick', 'hatching_chick', ':hatching_chick:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('headphones', 'headphones', ':headphones:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('hear_no_evil', 'hear_no_evil', ':hear_no_evil:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('heart', 'heart', ':heart:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('heart_decoration', 'heart_decoration', ':heart_decoration:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('heart_eyes', 'heart_eyes', ':heart_eyes:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('heart_eyes_cat', 'heart_eyes_cat', ':heart_eyes_cat:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('heartbeat', 'heartbeat', ':heartbeat:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('heartpulse', 'heartpulse', ':heartpulse:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('hearts', 'hearts', ':hearts:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('heavy_check_mark', 'heavy_check_mark', ':heavy_check_mark:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('heavy_division_sign', 'heavy_division_sign', ':heavy_division_sign:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('heavy_dollar_sign', 'heavy_dollar_sign', ':heavy_dollar_sign:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('heavy_exclamation_mark', 'heavy_exclamation_mark', ':heavy_exclamation_mark:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('heavy_minus_sign', 'heavy_minus_sign', ':heavy_minus_sign:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('heavy_multiplication_x', 'heavy_multiplication_x', ':heavy_multiplication_x:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('heavy_plus_sign', 'heavy_plus_sign', ':heavy_plus_sign:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('helicopter', 'helicopter', ':helicopter:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('herb', 'herb', ':herb:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('hibiscus', 'hibiscus', ':hibiscus:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('high_brightness', 'high_brightness', ':high_brightness:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('high_heel', 'high_heel', ':high_heel:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('hocho', 'hocho', ':hocho:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('honey_pot', 'honey_pot', ':honey_pot:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('honeybee', 'honeybee', ':honeybee:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('horse', 'horse', ':horse:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('horse_racing', 'horse_racing', ':horse_racing:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('hospital', 'hospital', ':hospital:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('hotel', 'hotel', ':hotel:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('hotsprings', 'hotsprings', ':hotsprings:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('hourglass', 'hourglass', ':hourglass:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('hourglass_flowing_sand', 'hourglass_flowing_sand', ':hourglass_flowing_sand:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('house', 'house', ':house:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('house_with_garden', 'house_with_garden', ':house_with_garden:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('hurtrealbad', 'hurtrealbad', ':hurtrealbad:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('hushed', 'hushed', ':hushed:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('ice_cream', 'ice_cream', ':ice_cream:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('icecream', 'icecream', ':icecream:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('id', 'id', ':id:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('ideograph_advantage', 'ideograph_advantage', ':ideograph_advantage:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('imp', 'imp', ':imp:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('inbox_tray', 'inbox_tray', ':inbox_tray:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('incoming_envelope', 'incoming_envelope', ':incoming_envelope:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('information_desk_person', 'information_desk_person', ':information_desk_person:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('information_source', 'information_source', ':information_source:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('innocent', 'innocent', ':innocent:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('interrobang', 'interrobang', ':interrobang:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('iphone', 'iphone', ':iphone:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('it', 'it', ':it:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('izakaya_lantern', 'izakaya_lantern', ':izakaya_lantern:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('jack_o_lantern', 'jack_o_lantern', ':jack_o_lantern:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('japan', 'japan', ':japan:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('japanese_castle', 'japanese_castle', ':japanese_castle:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('japanese_goblin', 'japanese_goblin', ':japanese_goblin:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('japanese_ogre', 'japanese_ogre', ':japanese_ogre:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('jeans', 'jeans', ':jeans:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('joy', 'joy', ':joy:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('joy_cat', 'joy_cat', ':joy_cat:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('jp', 'jp', ':jp:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('key', 'key', ':key:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('keycap_ten', 'keycap_ten', ':keycap_ten:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('kimono', 'kimono', ':kimono:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('kiss', 'kiss', ':kiss:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('kissing', 'kissing', ':kissing:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('kissing_cat', 'kissing_cat', ':kissing_cat:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('kissing_closed_eyes', 'kissing_closed_eyes', ':kissing_closed_eyes:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('kissing_face', 'kissing_face', ':kissing_face:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('kissing_heart', 'kissing_heart', ':kissing_heart:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('kissing_smiling_eyes', 'kissing_smiling_eyes', ':kissing_smiling_eyes:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('koala', 'koala', ':koala:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('koko', 'koko', ':koko:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('kr', 'kr', ':kr:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('large_blue_circle', 'large_blue_circle', ':large_blue_circle:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('large_blue_diamond', 'large_blue_diamond', ':large_blue_diamond:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('large_orange_diamond', 'large_orange_diamond', ':large_orange_diamond:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('last_quarter_moon', 'last_quarter_moon', ':last_quarter_moon:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('last_quarter_moon_with_face', 'last_quarter_moon_with_face', ':last_quarter_moon_with_face:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('laughing', 'laughing', ':laughing:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('leaves', 'leaves', ':leaves:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('ledger', 'ledger', ':ledger:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('left_luggage', 'left_luggage', ':left_luggage:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('left_right_arrow', 'left_right_arrow', ':left_right_arrow:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('leftwards_arrow_with_hook', 'leftwards_arrow_with_hook', ':leftwards_arrow_with_hook:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('lemon', 'lemon', ':lemon:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('leo', 'leo', ':leo:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('leopard', 'leopard', ':leopard:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('libra', 'libra', ':libra:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('light_rail', 'light_rail', ':light_rail:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('link', 'link', ':link:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('lips', 'lips', ':lips:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('lipstick', 'lipstick', ':lipstick:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('lock', 'lock', ':lock:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('lock_with_ink_pen', 'lock_with_ink_pen', ':lock_with_ink_pen:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('lollipop', 'lollipop', ':lollipop:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('loop', 'loop', ':loop:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('loudspeaker', 'loudspeaker', ':loudspeaker:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('love_hotel', 'love_hotel', ':love_hotel:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('love_letter', 'love_letter', ':love_letter:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('low_brightness', 'low_brightness', ':low_brightness:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('m', 'm', ':m:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('mag', 'mag', ':mag:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('mag_right', 'mag_right', ':mag_right:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('mahjong', 'mahjong', ':mahjong:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('mailbox', 'mailbox', ':mailbox:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('mailbox_closed', 'mailbox_closed', ':mailbox_closed:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('mailbox_with_mail', 'mailbox_with_mail', ':mailbox_with_mail:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('mailbox_with_no_mail', 'mailbox_with_no_mail', ':mailbox_with_no_mail:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('man', 'man', ':man:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('man_with_gua_pi_mao', 'man_with_gua_pi_mao', ':man_with_gua_pi_mao:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('man_with_turban', 'man_with_turban', ':man_with_turban:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('mans_shoe', 'mans_shoe', ':mans_shoe:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('maple_leaf', 'maple_leaf', ':maple_leaf:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('mask', 'mask', ':mask:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('massage', 'massage', ':massage:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('meat_on_bone', 'meat_on_bone', ':meat_on_bone:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('mega', 'mega', ':mega:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('melon', 'melon', ':melon:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('memo', 'memo', ':memo:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('mens', 'mens', ':mens:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('metal', 'metal', ':metal:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('metro', 'metro', ':metro:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('microphone', 'microphone', ':microphone:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('microscope', 'microscope', ':microscope:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('milky_way', 'milky_way', ':milky_way:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('minibus', 'minibus', ':minibus:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('minidisc', 'minidisc', ':minidisc:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('mobile_phone_off', 'mobile_phone_off', ':mobile_phone_off:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('money_with_wings', 'money_with_wings', ':money_with_wings:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('moneybag', 'moneybag', ':moneybag:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('monkey', 'monkey', ':monkey:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('monkey_face', 'monkey_face', ':monkey_face:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('monorail', 'monorail', ':monorail:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('moon', 'moon', ':moon:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('mortar_board', 'mortar_board', ':mortar_board:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('mount_fuji', 'mount_fuji', ':mount_fuji:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('mountain_bicyclist', 'mountain_bicyclist', ':mountain_bicyclist:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('mountain_cableway', 'mountain_cableway', ':mountain_cableway:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('mountain_railway', 'mountain_railway', ':mountain_railway:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('mouse', 'mouse', ':mouse:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('mouse2', 'mouse2', ':mouse2:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('movie_camera', 'movie_camera', ':movie_camera:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('moyai', 'moyai', ':moyai:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('muscle', 'muscle', ':muscle:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('mushroom', 'mushroom', ':mushroom:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('musical_keyboard', 'musical_keyboard', ':musical_keyboard:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('musical_note', 'musical_note', ':musical_note:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('musical_score', 'musical_score', ':musical_score:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('mute', 'mute', ':mute:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('nail_care', 'nail_care', ':nail_care:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('name_badge', 'name_badge', ':name_badge:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('neckbeard', 'neckbeard', ':neckbeard:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('necktie', 'necktie', ':necktie:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('negative_squared_cross_mark', 'negative_squared_cross_mark', ':negative_squared_cross_mark:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('neutral_face', 'neutral_face', ':neutral_face:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('new', 'new', ':new:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('new_moon', 'new_moon', ':new_moon:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('new_moon_with_face', 'new_moon_with_face', ':new_moon_with_face:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('newspaper', 'newspaper', ':newspaper:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('ng', 'ng', ':ng:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('nine', 'nine', ':nine:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('no_bell', 'no_bell', ':no_bell:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('no_bicycles', 'no_bicycles', ':no_bicycles:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('no_entry', 'no_entry', ':no_entry:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('no_entry_sign', 'no_entry_sign', ':no_entry_sign:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('no_good', 'no_good', ':no_good:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('no_mobile_phones', 'no_mobile_phones', ':no_mobile_phones:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('no_mouth', 'no_mouth', ':no_mouth:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('no_pedestrians', 'no_pedestrians', ':no_pedestrians:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('no_smoking', 'no_smoking', ':no_smoking:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('non-potable_water', 'non-potable_water', ':non-potable_water:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('nose', 'nose', ':nose:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('notebook', 'notebook', ':notebook:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('notebook_with_decorative_cover', 'notebook_with_decorative_cover', ':notebook_with_decorative_cover:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('notes', 'notes', ':notes:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('nut_and_bolt', 'nut_and_bolt', ':nut_and_bolt:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('o', 'o', ':o:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('o2', 'o2', ':o2:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('ocean', 'ocean', ':ocean:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('octocat', 'octocat', ':octocat:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('octopus', 'octopus', ':octopus:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('oden', 'oden', ':oden:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('office', 'office', ':office:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('ok', 'ok', ':ok:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('ok_hand', 'ok_hand', ':ok_hand:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('ok_woman', 'ok_woman', ':ok_woman:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('older_man', 'older_man', ':older_man:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('older_woman', 'older_woman', ':older_woman:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('on', 'on', ':on:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('oncoming_automobile', 'oncoming_automobile', ':oncoming_automobile:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('oncoming_bus', 'oncoming_bus', ':oncoming_bus:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('oncoming_police_car', 'oncoming_police_car', ':oncoming_police_car:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('oncoming_taxi', 'oncoming_taxi', ':oncoming_taxi:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('one', 'one', ':one:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('open_file_folder', 'open_file_folder', ':open_file_folder:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('open_hands', 'open_hands', ':open_hands:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('open_mouth', 'open_mouth', ':open_mouth:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('ophiuchus', 'ophiuchus', ':ophiuchus:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('orange_book', 'orange_book', ':orange_book:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('outbox_tray', 'outbox_tray', ':outbox_tray:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('ox', 'ox', ':ox:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('package', 'package', ':package:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('page_facing_up', 'page_facing_up', ':page_facing_up:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('page_with_curl', 'page_with_curl', ':page_with_curl:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('pager', 'pager', ':pager:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('palm_tree', 'palm_tree', ':palm_tree:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('panda_face', 'panda_face', ':panda_face:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('paperclip', 'paperclip', ':paperclip:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('parking', 'parking', ':parking:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('part_alternation_mark', 'part_alternation_mark', ':part_alternation_mark:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('partly_sunny', 'partly_sunny', ':partly_sunny:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('passport_control', 'passport_control', ':passport_control:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('paw_prints', 'paw_prints', ':paw_prints:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('peach', 'peach', ':peach:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('pear', 'pear', ':pear:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('pencil', 'pencil', ':pencil:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('pencil2', 'pencil2', ':pencil2:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('penguin', 'penguin', ':penguin:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('pensive', 'pensive', ':pensive:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('performing_arts', 'performing_arts', ':performing_arts:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('persevere', 'persevere', ':persevere:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('person_frowning', 'person_frowning', ':person_frowning:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('person_with_blond_hair', 'person_with_blond_hair', ':person_with_blond_hair:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('person_with_pouting_face', 'person_with_pouting_face', ':person_with_pouting_face:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('phone', 'phone', ':phone:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('pig', 'pig', ':pig:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('pig2', 'pig2', ':pig2:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('pig_nose', 'pig_nose', ':pig_nose:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('pill', 'pill', ':pill:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('pineapple', 'pineapple', ':pineapple:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('pisces', 'pisces', ':pisces:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('pizza', 'pizza', ':pizza:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('plus1', 'plus1', ':plus1:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('point_down', 'point_down', ':point_down:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('point_left', 'point_left', ':point_left:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('point_right', 'point_right', ':point_right:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('point_up', 'point_up', ':point_up:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('point_up_2', 'point_up_2', ':point_up_2:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('police_car', 'police_car', ':police_car:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('poodle', 'poodle', ':poodle:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('poop', 'poop', ':poop:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('post_office', 'post_office', ':post_office:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('postal_horn', 'postal_horn', ':postal_horn:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('postbox', 'postbox', ':postbox:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('potable_water', 'potable_water', ':potable_water:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('pouch', 'pouch', ':pouch:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('poultry_leg', 'poultry_leg', ':poultry_leg:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('pound', 'pound', ':pound:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('pouting_cat', 'pouting_cat', ':pouting_cat:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('pray', 'pray', ':pray:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('princess', 'princess', ':princess:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('punch', 'punch', ':punch:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('purple_heart', 'purple_heart', ':purple_heart:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('purse', 'purse', ':purse:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('pushpin', 'pushpin', ':pushpin:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('put_litter_in_its_place', 'put_litter_in_its_place', ':put_litter_in_its_place:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('question', 'question', ':question:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('rabbit', 'rabbit', ':rabbit:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('rabbit2', 'rabbit2', ':rabbit2:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('racehorse', 'racehorse', ':racehorse:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('radio', 'radio', ':radio:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('radio_button', 'radio_button', ':radio_button:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('rage', 'rage', ':rage:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('rage1', 'rage1', ':rage1:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('rage2', 'rage2', ':rage2:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('rage3', 'rage3', ':rage3:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('rage4', 'rage4', ':rage4:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('railway_car', 'railway_car', ':railway_car:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('rainbow', 'rainbow', ':rainbow:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('raised_hand', 'raised_hand', ':raised_hand:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('raised_hands', 'raised_hands', ':raised_hands:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('raising_hand', 'raising_hand', ':raising_hand:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('ram', 'ram', ':ram:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('ramen', 'ramen', ':ramen:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('rat', 'rat', ':rat:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('recycle', 'recycle', ':recycle:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('red_car', 'red_car', ':red_car:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('red_circle', 'red_circle', ':red_circle:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('registered', 'registered', ':registered:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('relaxed', 'relaxed', ':relaxed:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('relieved', 'relieved', ':relieved:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('repeat', 'repeat', ':repeat:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('repeat_one', 'repeat_one', ':repeat_one:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('restroom', 'restroom', ':restroom:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('revolving_hearts', 'revolving_hearts', ':revolving_hearts:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('rewind', 'rewind', ':rewind:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('ribbon', 'ribbon', ':ribbon:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('rice', 'rice', ':rice:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('rice_ball', 'rice_ball', ':rice_ball:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('rice_cracker', 'rice_cracker', ':rice_cracker:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('rice_scene', 'rice_scene', ':rice_scene:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('ring', 'ring', ':ring:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('rocket', 'rocket', ':rocket:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('roller_coaster', 'roller_coaster', ':roller_coaster:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('rooster', 'rooster', ':rooster:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('rose', 'rose', ':rose:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('rotating_light', 'rotating_light', ':rotating_light:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('round_pushpin', 'round_pushpin', ':round_pushpin:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('rowboat', 'rowboat', ':rowboat:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('ru', 'ru', ':ru:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('rugby_football', 'rugby_football', ':rugby_football:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('runner', 'runner', ':runner:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('running', 'running', ':running:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('running_shirt_with_sash', 'running_shirt_with_sash', ':running_shirt_with_sash:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('sa', 'sa', ':sa:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('sagittarius', 'sagittarius', ':sagittarius:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('sailboat', 'sailboat', ':sailboat:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('sake', 'sake', ':sake:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('sandal', 'sandal', ':sandal:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('santa', 'santa', ':santa:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('satellite', 'satellite', ':satellite:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('satisfied', 'satisfied', ':satisfied:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('saxophone', 'saxophone', ':saxophone:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('school', 'school', ':school:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('school_satchel', 'school_satchel', ':school_satchel:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('scissors', 'scissors', ':scissors:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('scorpius', 'scorpius', ':scorpius:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('scream', 'scream', ':scream:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('scream_cat', 'scream_cat', ':scream_cat:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('scroll', 'scroll', ':scroll:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('seat', 'seat', ':seat:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('secret', 'secret', ':secret:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('see_no_evil', 'see_no_evil', ':see_no_evil:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('seedling', 'seedling', ':seedling:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('seven', 'seven', ':seven:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('shaved_ice', 'shaved_ice', ':shaved_ice:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('sheep', 'sheep', ':sheep:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('shell', 'shell', ':shell:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('ship', 'ship', ':ship:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('shipit', 'shipit', ':shipit:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('shirt', 'shirt', ':shirt:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('shit', 'shit', ':shit:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('shoe', 'shoe', ':shoe:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('shower', 'shower', ':shower:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('signal_strength', 'signal_strength', ':signal_strength:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('six', 'six', ':six:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('six_pointed_star', 'six_pointed_star', ':six_pointed_star:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('ski', 'ski', ':ski:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('skull', 'skull', ':skull:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('sleeping', 'sleeping', ':sleeping:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('sleepy', 'sleepy', ':sleepy:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('slot_machine', 'slot_machine', ':slot_machine:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('small_blue_diamond', 'small_blue_diamond', ':small_blue_diamond:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('small_orange_diamond', 'small_orange_diamond', ':small_orange_diamond:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('small_red_triangle', 'small_red_triangle', ':small_red_triangle:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('small_red_triangle_down', 'small_red_triangle_down', ':small_red_triangle_down:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('smile', 'smile', ':smile:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('smile_cat', 'smile_cat', ':smile_cat:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('smiley', 'smiley', ':smiley:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('smiley_cat', 'smiley_cat', ':smiley_cat:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('smiling_imp', 'smiling_imp', ':smiling_imp:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('smirk', 'smirk', ':smirk:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('smirk_cat', 'smirk_cat', ':smirk_cat:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('smoking', 'smoking', ':smoking:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('snail', 'snail', ':snail:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('snake', 'snake', ':snake:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('snowboarder', 'snowboarder', ':snowboarder:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('snowflake', 'snowflake', ':snowflake:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('snowman', 'snowman', ':snowman:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('sob', 'sob', ':sob:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('soccer', 'soccer', ':soccer:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('soon', 'soon', ':soon:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('sos', 'sos', ':sos:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('sound', 'sound', ':sound:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('space_invader', 'space_invader', ':space_invader:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('spades', 'spades', ':spades:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('spaghetti', 'spaghetti', ':spaghetti:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('sparkle', 'sparkle', ':sparkle:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('sparkler', 'sparkler', ':sparkler:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('sparkles', 'sparkles', ':sparkles:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('sparkling_heart', 'sparkling_heart', ':sparkling_heart:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('speak_no_evil', 'speak_no_evil', ':speak_no_evil:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('speaker', 'speaker', ':speaker:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('speech_balloon', 'speech_balloon', ':speech_balloon:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('speedboat', 'speedboat', ':speedboat:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('squirrel', 'squirrel', ':squirrel:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('star', 'star', ':star:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('star2', 'star2', ':star2:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('stars', 'stars', ':stars:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('station', 'station', ':station:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('statue_of_liberty', 'statue_of_liberty', ':statue_of_liberty:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('steam_locomotive', 'steam_locomotive', ':steam_locomotive:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('stew', 'stew', ':stew:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('straight_ruler', 'straight_ruler', ':straight_ruler:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('strawberry', 'strawberry', ':strawberry:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('stuck_out_tongue', 'stuck_out_tongue', ':stuck_out_tongue:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('stuck_out_tongue_closed_eyes', 'stuck_out_tongue_closed_eyes', ':stuck_out_tongue_closed_eyes:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('stuck_out_tongue_winking_eye', 'stuck_out_tongue_winking_eye', ':stuck_out_tongue_winking_eye:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('sun_with_face', 'sun_with_face', ':sun_with_face:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('sunflower', 'sunflower', ':sunflower:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('sunglasses', 'sunglasses', ':sunglasses:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('sunny', 'sunny', ':sunny:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('sunrise', 'sunrise', ':sunrise:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('sunrise_over_mountains', 'sunrise_over_mountains', ':sunrise_over_mountains:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('surfer', 'surfer', ':surfer:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('sushi', 'sushi', ':sushi:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('suspect', 'suspect', ':suspect:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('suspension_railway', 'suspension_railway', ':suspension_railway:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('sweat', 'sweat', ':sweat:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('sweat_drops', 'sweat_drops', ':sweat_drops:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('sweat_smile', 'sweat_smile', ':sweat_smile:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('sweet_potato', 'sweet_potato', ':sweet_potato:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('swimmer', 'swimmer', ':swimmer:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('symbols', 'symbols', ':symbols:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('syringe', 'syringe', ':syringe:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('tada', 'tada', ':tada:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('tanabata_tree', 'tanabata_tree', ':tanabata_tree:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('tangerine', 'tangerine', ':tangerine:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('taurus', 'taurus', ':taurus:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('taxi', 'taxi', ':taxi:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('tea', 'tea', ':tea:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('telephone', 'telephone', ':telephone:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('telephone_receiver', 'telephone_receiver', ':telephone_receiver:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('telescope', 'telescope', ':telescope:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('tennis', 'tennis', ':tennis:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('tent', 'tent', ':tent:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('thought_balloon', 'thought_balloon', ':thought_balloon:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('three', 'three', ':three:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('thumbsdown', 'thumbsdown', ':thumbsdown:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('thumbsup', 'thumbsup', ':thumbsup:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('ticket', 'ticket', ':ticket:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('tiger', 'tiger', ':tiger:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('tiger2', 'tiger2', ':tiger2:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('tired_face', 'tired_face', ':tired_face:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('tm', 'tm', ':tm:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('toilet', 'toilet', ':toilet:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('tokyo_tower', 'tokyo_tower', ':tokyo_tower:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('tomato', 'tomato', ':tomato:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('tongue', 'tongue', ':tongue:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('top', 'top', ':top:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('tophat', 'tophat', ':tophat:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('tractor', 'tractor', ':tractor:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('traffic_light', 'traffic_light', ':traffic_light:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('train', 'train', ':train:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('train2', 'train2', ':train2:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('tram', 'tram', ':tram:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('triangular_flag_on_post', 'triangular_flag_on_post', ':triangular_flag_on_post:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('triangular_ruler', 'triangular_ruler', ':triangular_ruler:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('trident', 'trident', ':trident:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('triumph', 'triumph', ':triumph:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('trolleybus', 'trolleybus', ':trolleybus:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('trollface', 'trollface', ':trollface:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('trophy', 'trophy', ':trophy:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('tropical_drink', 'tropical_drink', ':tropical_drink:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('tropical_fish', 'tropical_fish', ':tropical_fish:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('truck', 'truck', ':truck:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('trumpet', 'trumpet', ':trumpet:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('tshirt', 'tshirt', ':tshirt:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('tulip', 'tulip', ':tulip:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('turtle', 'turtle', ':turtle:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('tv', 'tv', ':tv:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('twisted_rightwards_arrows', 'twisted_rightwards_arrows', ':twisted_rightwards_arrows:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('two', 'two', ':two:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('two_hearts', 'two_hearts', ':two_hearts:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('two_men_holding_hands', 'two_men_holding_hands', ':two_men_holding_hands:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('two_women_holding_hands', 'two_women_holding_hands', ':two_women_holding_hands:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('u5272', 'u5272', ':u5272:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('u5408', 'u5408', ':u5408:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('u55b6', 'u55b6', ':u55b6:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('u6307', 'u6307', ':u6307:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('u6708', 'u6708', ':u6708:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('u6709', 'u6709', ':u6709:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('u6e80', 'u6e80', ':u6e80:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('u7121', 'u7121', ':u7121:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('u7533', 'u7533', ':u7533:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('u7981', 'u7981', ':u7981:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('u7a7a', 'u7a7a', ':u7a7a:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('uk', 'uk', ':uk:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('umbrella', 'umbrella', ':umbrella:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('unamused', 'unamused', ':unamused:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('underage', 'underage', ':underage:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('unlock', 'unlock', ':unlock:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('up', 'up', ':up:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('us', 'us', ':us:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('v', 'v', ':v:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('vertical_traffic_light', 'vertical_traffic_light', ':vertical_traffic_light:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('vhs', 'vhs', ':vhs:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('vibration_mode', 'vibration_mode', ':vibration_mode:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('video_camera', 'video_camera', ':video_camera:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('video_game', 'video_game', ':video_game:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('violin', 'violin', ':violin:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('virgo', 'virgo', ':virgo:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('volcano', 'volcano', ':volcano:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('vs', 'vs', ':vs:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('walking', 'walking', ':walking:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('waning_crescent_moon', 'waning_crescent_moon', ':waning_crescent_moon:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('waning_gibbous_moon', 'waning_gibbous_moon', ':waning_gibbous_moon:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('warning', 'warning', ':warning:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('watch', 'watch', ':watch:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('water_buffalo', 'water_buffalo', ':water_buffalo:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('watermelon', 'watermelon', ':watermelon:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('wave', 'wave', ':wave:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('wavy_dash', 'wavy_dash', ':wavy_dash:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('waxing_crescent_moon', 'waxing_crescent_moon', ':waxing_crescent_moon:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('waxing_gibbous_moon', 'waxing_gibbous_moon', ':waxing_gibbous_moon:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('wc', 'wc', ':wc:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('weary', 'weary', ':weary:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('wedding', 'wedding', ':wedding:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('whale', 'whale', ':whale:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('whale2', 'whale2', ':whale2:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('wheelchair', 'wheelchair', ':wheelchair:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('white_check_mark', 'white_check_mark', ':white_check_mark:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('white_circle', 'white_circle', ':white_circle:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('white_flower', 'white_flower', ':white_flower:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('white_large_square', 'white_large_square', ':white_large_square:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('white_medium_small_square', 'white_medium_small_square', ':white_medium_small_square:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('white_medium_square', 'white_medium_square', ':white_medium_square:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('white_small_square', 'white_small_square', ':white_small_square:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('white_square_button', 'white_square_button', ':white_square_button:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('wind_chime', 'wind_chime', ':wind_chime:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('wine_glass', 'wine_glass', ':wine_glass:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('wink', 'wink', ':wink:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('wolf', 'wolf', ':wolf:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('woman', 'woman', ':woman:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('womans_clothes', 'womans_clothes', ':womans_clothes:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('womans_hat', 'womans_hat', ':womans_hat:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('womens', 'womens', ':womens:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('worried', 'worried', ':worried:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('wrench', 'wrench', ':wrench:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('x', 'x', ':x:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('yellow_heart', 'yellow_heart', ':yellow_heart:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('yen', 'yen', ':yen:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('yum', 'yum', ':yum:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('zap', 'zap', ':zap:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('zero', 'zero', ':zero:')");
	$transaction->addQuery("INSERT INTO `" . PREFIX . "smileys` (`name`, `alt`, `pattern`) VALUES ('zzz', 'zzz', ':zzz:')");
	$transaction->addQuery("ALTER TABLE `" . PREFIX . "servers` CHANGE `game` `game` CHAR( 10 ) NOT NULL");
	$transaction->addQuery("ALTER TABLE `" . PREFIX . "games` CHANGE `tag` `tag` VARCHAR( 10 ) NOT NULL");
	
	//Update new Tablenames

	
	
	if ($transaction->successful()) {
        return array('status' => 'success', 'message' => 'Updated webSPELL NOR 1.2.5 Part 1');
    } else {
        return array('status' => 'fail', 'message' => 'Failed to update webSPELL NOR 1.2.5 Part 1 <br/>' . $transaction->getError());
    }
}

function update_124_125_2($_database) {
	$transaction = new Transaction($_database);
	
	$transaction->addQuery("ALTER TABLE `" . PREFIX. "plugins` add description VARCHAR(255) NOT NULL");
	$transaction->addQuery("ALTER TABLE `" . PREFIX. "addon_categories` RENAME TO `" . PREFIX ."dashnavi_categories`");
	$transaction->addQuery("ALTER TABLE `" . PREFIX. "addon_links` RENAME TO `" . PREFIX ."dashnavi_links`");
	$transaction->addQuery("ALTER TABLE `" . PREFIX. "moduls` add deactivated int(1) NOT NULL default '1'");
	$transaction->addQuery("DELETE FROM `" . PREFIX. "dashnavi_links` WHERE `name` = 'Plugin-Manager' AND `name` = 'Navigation' AND `name` = 'Carousel'");
	
	if ($transaction->successful()) {
        return array('status' => 'success', 'message' => 'Updated webSPELL NOR 1.2.5 Part 2');
    } else {
        return array('status' => 'fail', 'message' => 'Failed to update webSPELL NOR 1.2.5 Part 2 <br/>' . $transaction->getError());
    }
}

function update_420_125($_database) {
	$transaction = new Transaction($_database);
	include("../version.php");
	
	if($version != '4.3.0' ) {
		$transaction->addQuery("ALTER TABLE ".PREFIX."user CHANGE `connection` `verbindung` VARCHAR(255)");
	}
	$transaction->addQuery("ALTER TABLE ".PREFIX."user ADD password_hash VARCHAR(255) NOT NULL AFTER password");
	$transaction->addQuery("ALTER TABLE ".PREFIX."user ADD password_pepper VARCHAR(255) NOT NULL AFTER password_hash");
	
	   //carousel
    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "carousel`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "carousel` (
  `carouselID` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `link` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `carousel_pic` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `sort` int(11) NOT NULL DEFAULT '1',
  `displayed` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '1',
  PRIMARY KEY ( `carouselID` )
  ) DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_unicode_ci");
  
  $transaction->addQuery("INSERT INTO `".PREFIX."carousel` (`title`, `link`, `description`, `carousel_pic`, `sort`, `displayed`) VALUES
('Carousel Entry #1', 'https://webspell-nor.de/', 'The Bootstrap Carousel in Webspell? No way?! Yes we did it!', '1.jpg', '1', '1'),
('Carousel Entry #2', 'https://webspell-nor.de/', 'The Bootstrap Carousel in Webspell? No way?! Yes we did it!', '2.jpg', '1', '1'),
('Carousel Entry #3', 'https://webspell-nor.de/', 'The Bootstrap Carousel in Webspell? No way?! Yes we did it!', '3.jpg', '1', '1')");

	// Navigation
	$transaction->addQuery("CREATE TABLE `" . PREFIX . "navigation_main` (
  `mnavID` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL default '',
  `link` varchar(255) NOT NULL default '',
  `sort` int(2) NOT NULL default '0',
  `isdropdown` int(1) NOT NULL default '1',
  PRIMARY KEY  (`mnavID`)
) AUTO_INCREMENT=1
  DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_unicode_ci");
  
  $transaction->addQuery("INSERT INTO `".PREFIX."navigation_main` (`mnavID`, `name`, `link`, `sort`, `isdropdown`) VALUES
(1, 'main', '#', 1, 1),
(2, 'Team', '#', 1, 1),
(3, 'community', '#', 3, 1),
(4, 'media', '#', 4, 1),
(5, 'miscellaneous', '#', 5, 1);");
  
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "navigation_sub` (
  `snavID` int(11) NOT NULL AUTO_INCREMENT,
  `mnav_ID` int(11) NOT NULL default '0', 
  `name` varchar(255) NOT NULL default '',
  `link` varchar(255) NOT NULL default '',
  `sort` int(2) NOT NULL default '0',
  `indropdown` int(1) NOT NULL default '1',
  PRIMARY KEY  (`snavID`)
) AUTO_INCREMENT=1
  DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_unicode_ci");
  
  $transaction->addQuery("ALTER TABLE `" . PREFIX . "servers` CHANGE `game` `game` CHAR( 10 ) NOT NULL");
	
	$transaction->addQuery("INSERT INTO `".PREFIX."navigation_sub` (`snavID`, `mnav_ID`, `name`, `link`, `sort`, `indropdown`) VALUES
(1, 1, 'News', 'index.php?site=news', 0, 1),
(2, 1, 'Archive', 'index.php?site=news&action=archive', 1, 1),
(3, 1, 'Articles', 'index.php?site=articles', 1, 1),
(4, 1, 'Calendar', 'index.php?site=calendar', 1, 1),
(5, 1, 'FAQ', 'index.php?site=faq', 1, 1),
(6, 1, 'Search', 'index.php?site=search', 1, 1),
(7, 2, 'About_Us', 'index.php?site=about', 1, 1),
(8, 2, 'Squads', 'index.php?site=squads', 1, 1),
(9, 2, 'Members', 'index.php?site=members', 1, 1),
(11, 2, 'Matches', 'index.php?site=clanwars', 1, 1),
(12, 2, 'History', 'index.php?site=history', 1, 1),
(13, 2, 'Awards', 'index.php?site=awards', 1, 1),
(14, 3, 'Forum', 'index.php?site=forum', 1, 1),
(15, 3, 'Guestbook', 'index.php?site=guestbook', 1, 1),
(16, 3, 'Registered_users', 'index.php?site=registered_users', 1, 1),
(17, 3, 'whoisonline', 'index.php?site=whoisonline', 1, 1),
(18, 3, 'Polls', 'index.php?site=polls', 1, 1),
(19, 3, 'Server', 'index.php?site=server', 1, 1),
(20, 4, 'Downloads', 'index.php?site=files', 1, 1),
(21, 4, 'Demos', 'index.php?site=demos', 1, 1),
(22, 4, 'Links', 'index.php?site=links', 1, 1),
(23, 4, 'Gallery', 'index.php?site=gallery', 1, 1),
(24, 4, 'Links_us', 'index.php?site=linkus', 1, 1),
(25, 5, 'Sponsors', 'index.php?site=sponsors', 1, 1),
(26, 5, 'Newsletter', 'index.php?site=newsletter', 1, 1),
(27, 5, 'Contact', 'index.php?site=contact', 1, 1),
(28, 5, 'fight_us', 'index.php?site=challenge', 1, 1),
(29, 5, 'join_us', 'index.php?site=joinus', 1, 1),
(30, 5, 'Imprint', 'index.php?site=imprint', 1, 1);");
	
	if ($transaction->successful()) {
        return array('status' => 'success', 'message' => 'Updated webSPELL.org to webSPELL NOR');
    } else {
        return array('status' => 'fail', 'message' => 'Failed to update webSPELL.org to webSPELL NOR <br/>' . $transaction->getError());
    }

	
}

function update_PasswordHash($_database)
{
    $transaction = new Transaction($_database);
    // update user passwords for new hashing
    $q = mysqli_query($_database, "SELECT userID, password FROM `" . PREFIX . "user`");
    while ($ds = mysqli_fetch_assoc($q)) {
        $transaction->addQuery("UPDATE `" . PREFIX . "user` SET password='" . hash('sha512', substr($ds['password'], 0, 14) . $ds['password']) . "' WHERE userID=" . $ds['userID']);
    }
    if ($transaction->successful()) {
        return array('status' => 'success', 'message' => 'Updated password hashes');
    } else {
        return array('status' => 'fail', 'message' => 'Failed to update password hashes');
    }
}

function update_addSMTPSupport($_database)
{
    global $_database;
    $transaction = new Transaction($_database);
    $transaction->addQuery("DROP TABLE IF EXISTS `" . PREFIX . "email`");
    $transaction->addQuery("CREATE TABLE `" . PREFIX . "email` (
  `emailID` int(1) NOT NULL,
  `user` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `host` varchar(255) NOT NULL,
  `port` int(5) NOT NULL,
  `debug` int(1) NOT NULL,
  `auth` int(1) NOT NULL,
  `html` int(1) NOT NULL,
  `smtp` int(1) NOT NULL,
  `secure` int(1) NOT NULL
) DEFAULT CHARSET=utf8 DEFAULT COLLATE utf8_unicode_ci");

    $transaction->addQuery("INSERT INTO `" . PREFIX . "email` (emailID, user, password, host, port, debug, auth, html, smtp, secure)
VALUES (1, '', '', '', 25, 0, 0, 1, 0, 0)");

    $transaction->addQuery("ALTER TABLE `" . PREFIX . "email` ADD UNIQUE KEY emailID (emailID)");
    if ($transaction->successful()) {
        return array('status' => 'success', 'message' => 'Added SMTP support');
    } else {
        return array('status' => 'fail', 'message' => 'Failed to add SMTP support<br/>' . $transaction->getError());
    }
}

function update_updateLanguages($_database)
{
    # update languages in database

    global $_database;
    $transaction = new Transaction($_database);
    
    $transaction->addQuery("UPDATE `" . PREFIX . "news_languages` SET lang = 'en' WHERE lang = 'uk'");
    
    $transaction->addQuery("UPDATE `" . PREFIX . "settings` SET default_language = 'en' WHERE default_language = 'uk'");
    if ($transaction->successful()) {
        return array('status' => 'success', 'message' => 'Updated languages');
    } else {
        return array('status' => 'fail', 'message' => 'Failed to update languages<br/>' . $transaction->getError());
    }
}


