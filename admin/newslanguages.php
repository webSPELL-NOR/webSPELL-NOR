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

$_language->readModule('newslanguages', false, true);

if (!isnewsadmin($userID) || mb_substr(basename($_SERVER[ 'REQUEST_URI' ]), 0, 15) != "admincenter.php") {
    die($_language->module[ 'access_denied' ]);
}

if (isset($_POST[ 'save' ])) {
    $CAPCLASS = new \webspell\Captcha;
    if ($CAPCLASS->checkCaptcha(0, $_POST[ 'captcha_hash' ])) {
        if (checkforempty(array('language', 'lang', 'alt'))) {
            safe_query(
                "INSERT INTO
                    " . PREFIX . "news_languages (
                        language,
                        lang,
                        alt
                    )
                    VALUES (
                        '" . $_POST[ 'language' ] . "',
                        '" . $_POST[ 'lang' ] . "',
                        '" . $_POST[ 'alt' ] . "'
                    )"
            );
        } else {
            echo $_language->module[ 'information_incomplete' ];
        }
    } else {
        echo $_language->module[ 'transaction_invalid' ];
    }
} elseif (isset($_POST[ 'saveedit' ])) {
    $CAPCLASS = new \webspell\Captcha;
    if ($CAPCLASS->checkCaptcha(0, $_POST[ 'captcha_hash' ])) {
        if (checkforempty(array('language', 'lang', 'alt'))) {
            safe_query(
                "UPDATE
                    " . PREFIX . "news_languages
                SET
                    language='" . $_POST[ 'language' ] . "',
                    lang='" . $_POST[ 'lang' ] . "',
                    alt='" . $_POST[ 'alt' ] . "'
                WHERE
                    langID='" . $_POST[ 'langID' ] . "'"
            );
        } else {
            echo $_language->module[ 'information_incomplete' ];
        }
    } else {
        echo $_language->module[ 'transaction_invalid' ];
    }
} elseif (isset($_GET[ 'delete' ])) {
    $CAPCLASS = new \webspell\Captcha;
    if ($CAPCLASS->checkCaptcha(0, $_GET[ 'captcha_hash' ])) {
        safe_query("DELETE FROM " . PREFIX . "news_languages WHERE langID='" . $_GET[ 'langID' ] . "'");
    } else {
        echo $_language->module[ 'transaction_invalid' ];
    }
}

$langs = '';
$getlangs = safe_query("SELECT country, short FROM " . PREFIX . "countries ORDER BY country");
while ($dt = mysqli_fetch_array($getlangs)) {
    $langs .= '<option value="' . $dt[ 'short' ] . '">' . $dt[ 'country' ] . '</option>';
}

if (isset($_GET[ 'action' ])) {
    $action = $_GET[ 'action' ];
} else {
    $action = '';
}

if ($action == "add") {
    $CAPCLASS = new \webspell\Captcha;
    $CAPCLASS->createTransaction();
    $hash = $CAPCLASS->getHash();
    $flag = '[flag][/flag]';
    $country = flags($flag, 'admin');
    $country = str_replace("<img", "<img id='getcountry'", $country);

  echo'<div class="panel panel-default"><div class="panel-heading">
                            <i class="fa fa-file-text"></i> '.$_language->module['news_languages'].'
                        </div>
      <div class="panel-body">
  <a href="admincenter.php?site=newslanguages" class="white">'.$_language->module['news_languages'].'</a> &raquo; '.$_language->module['add_language'].'<br><br>';
  
  echo'<form class="form-horizontal" method="post" action="admincenter.php?site=newslanguages">
    <div class="form-group">
    <label class="col-sm-2 control-label">'.$_language->module['language'].'</label>
    <div class="col-sm-8">
      <input type="text" class="form-control" name="language" />
    </div>
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">'.$_language->module['title'].'</label>
    <div class="col-sm-8">
     <input class="form-control" type="text" name="alt" />
    </div>
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">'.$_language->module['flag'].'</label>
    <div class="col-sm-8">
     <select class="form-control" name="lang" onchange="document.getElementById(\'getcountry\').src=\'../images/flags/\'+this.options[this.selectedIndex].value+\'.gif\'">'.$langs.'</select> &nbsp; '.$_language->module['preview'].': '.$country.'
    </div>
  </div>
  <div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
      <input type="hidden" name="captcha_hash" value="'.$hash.'" /><button class="btn btn-success btn-xs" type="submit" name="save" />'.$_language->module['add_language'].'</button>
    </div>
  </div>
  </form></div>
  </div>';

} elseif ($action == "edit") {
    $CAPCLASS = new \webspell\Captcha;
    $CAPCLASS->createTransaction();
    $hash = $CAPCLASS->getHash();
  
  echo'<div class="panel panel-default">
  <div class="panel-heading">
                            <i class="fa fa-file-text"></i> '.$_language->module['news_languages'].'
                        </div>
            <div class="panel-body">
  <a href="admincenter.php?site=newslanguages" class="white">'.$_language->module['news_languages'].'</a> &raquo; '.$_language->module['edit_language'].'<br><br>';

	 $ergebnis = safe_query("SELECT * FROM " . PREFIX . "news_languages WHERE langID='" . $_GET[ 'langID' ] . "'");
    $ds = mysqli_fetch_array($ergebnis);
    $flag = '[flag]' . $ds[ 'lang' ] . '[/flag]';
    $country = flags($flag, 'admin');
    $country = str_replace("<img", "<img id='getcountry'", $country);
    $langs = str_replace(' selected="selected"', '', $langs);
    $langs = str_replace('value="' . $ds[ 'lang' ] . '"', 'value="' . $ds[ 'lang' ] . '" selected="selected"', $langs);
  
  echo'<form class="form-horizontal" method="post" action="admincenter.php?site=newslanguages">
  <div class="form-group">
    <label class="col-sm-2 control-label">'.$_language->module['language'].'</label>
    <div class="col-sm-8">
      <input type="text" class="form-control" name="language" value="'.getinput($ds['language']).'" />
    </div>
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">'.$_language->module['title'].'</label>
    <div class="col-sm-8">
     <input class="form-control" type="text" name="alt" value="'.getinput($ds['alt']).'" />
    </div>
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">'.$_language->module['flag'].'</label>
    <div class="col-sm-8">
     <select class="form-control" name="lang" onchange="document.getElementById(\'getcountry\').src=\'../images/flags/\'+this.options[this.selectedIndex].value+\'.gif\'">'.$langs.'</select> &nbsp; '.$_language->module['preview'].': '.$country.'
    </div>
  </div>
  <div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
      <input type="hidden" name="captcha_hash" value="'.$hash.'" /><input type="hidden" name="langID" value="'.$ds['langID'].'" /><button class="btn btn-success btn-xs" type="submit" name="saveedit" />'.$_language->module['edit_language'].'</button>
    </div>
  </div>
  </form></div>
  </div>';
}

else {

  if(isset($_GET['page'])) $page=(int)$_GET['page'];
  else $page = 1;
	
  echo'<div class="panel panel-default">
  <div class="panel-heading">
                            <i class="fa fa-file-text"></i> '.$_language->module['news_languages'].'
                        </div>
            <div class="panel-body">';
  
  echo'<a href="admincenter.php?site=newslanguages&amp;action=add" class="btn btn-primary btn-xs" type="button">' . $_language->module[ 'new_language' ] . '</a><br /><br />';

	
  $alle=safe_query("SELECT langID FROM ".PREFIX."news_languages");
  $gesamt = mysqli_num_rows($alle);
  $pages=1;

  $max='15';

  for ($n=$max; $n<=$gesamt; $n+=$max) {
    if($gesamt>$n) $pages++;
  }

  if($pages>1) $page_link = makepagelink("admincenter.php?site=newslanguages", $page, $pages);
    else $page_link='';

  if ($page == "1") {
    $ergebnis = safe_query("SELECT * FROM ".PREFIX."news_languages ORDER BY lang ASC LIMIT 0,$max");
    $n=1;
  }
  else {
    $start=$page*$max-$max;
    $ergebnis = safe_query("SELECT * FROM ".PREFIX."news_languages ORDER BY lang ASC LIMIT $start,$max");
    $n = ($gesamt+1)-$page*$max+$max;
  }
  
   echo'<table class="table table-striped">
    <thead>
      <th><b>'.$_language->module['flag'].'</b></th>
      <th><b>'.$_language->module['language'].'</b></th>
      <th><b>'.$_language->module['title'].'</b></th>
      <th><b>'.$_language->module['actions'].'</b></th>
    </thead>';
  $n=1;

  $CAPCLASS = new \webspell\Captcha;
    $CAPCLASS->createTransaction();
    $hash = $CAPCLASS->getHash();

  while($ds=mysqli_fetch_array($ergebnis)) {


$getflag = '<img src="../images/flags/' . $ds[ 'lang' ] . '.gif" alt="' . $ds[ 'alt' ] . '">';
      
    echo'<tr>
      <td>'.$getflag.'</td>
      <td>'.getinput($ds['language']).'</td>
      <td>'.getinput($ds['alt']).'</td>
      <td><a href="admincenter.php?site=newslanguages&amp;action=edit&amp;langID='.$ds['langID'].'" class="hidden-xs hidden-sm btn btn-warning btn-xs" type="button">' . $_language->module[ 'edit' ] . '</a>

        <input class="hidden-xs hidden-sm btn btn-danger btn-xs" type="button" onclick="MM_confirm(\'' . $_language->module['really_delete'] . '\', \'admincenter.php?site=newslanguages&amp;delete=true&amp;langID='.$ds['langID'].'&amp;captcha_hash='.$hash.'\')" value="' . $_language->module['delete'] . '" />

      <a href="admincenter.php?site=newslanguages&amp;action=edit&amp;langID='.$ds['langID'].'"  class="mobile visible-xs visible-sm" type="button"><i class="fa fa-pencil"></i></a>
      <a class="mobile visible-xs visible-sm" type="button" onclick="MM_confirm(\'' . $_language->module['really_delete'] . '\', \'admincenter.php?site=newslanguages&amp;delete=true&amp;langID='.$ds['langID'].'&amp;captcha_hash='.$hash.'\')" /><i class="fa fa-times"></i></a>

      </td>
    </tr>';

$n++;
  }
    echo'</table>';
  
if($pages>1) echo $page_link;


 echo ' </div></div>';
}
?>

