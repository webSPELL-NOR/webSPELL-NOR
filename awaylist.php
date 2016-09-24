<?php
$_language->readModule('awaylist');

#eval ("\$title_awaylist = \"".gettemplate("title_awaylist")."\";");
#echo $title_awaylist;
$template = $GLOBALS["_template"]->replaceTemplate("title_awaylist", $data_array);
echo $template;

$ergebnis=safe_query("SELECT * FROM ".PREFIX."awaylist_settings");
$ds=mysqli_fetch_array($ergebnis);

if(isset($_POST['action'])) $action = $_POST['action'];
else $action='';
if ($action == "send") {
 
 	  $name = $userID;
	  $awayfrom1 = $_POST['awayfrom'];
	  $awayfrom = strtotime($awayfrom1);  
	  $awayto1 = $_POST['awayto'];
	  $awayto = strtotime($awayto1);
	  $reason = trim(stripslashes($_POST['reason']));
	
//Überprüfung der Eingabefelder  
	  if(!$reason) {
	  echo ''.$_language->module['no_reason'].'<br /><br /><b><a href="javascript:history.back()">'.$_language->module['back'].'</a></b>';
	  }
	  
	  elseif (!preg_match("/[0-9]{2}-[0-9]{2}-[0-9]{4}/",$awayfrom1)) {
	  echo ''.$_language->module['no_date'].'<br /><br /><b><a href="javascript:history.back()">'.$_language->module['back'].'</a></b>';
	  }
	 
	  elseif (!preg_match("/[0-9]{2}-[0-9]{2}-[0-9]{4}/",$awayto1)) {
	  echo ''.$_language->module['no_date'].'<br /><br /><b><a href="javascript:history.back()">'.$_language->module['back'].'</a></b>';
	  }
	  
	  else {
		safe_query("INSERT INTO ".PREFIX."awaylist (name, awayfrom, awayto, reason) values ( '$name', '$awayfrom', '$awayto', '$reason' ) ");
		#redirect('index.php?site=awaylist');
		redirect('index.php?site=awaylist', $_language->module['submitted'], '3');
		
//Email-Benachrichtigung
	  $notification=$ds['notification'];
	  $mailaddy=$ds['mailaddy'];
	  if($ds['showonly'] == "mem") {
	  $memberoruser=$_language->module['clanmember'];
	  }
	  if($ds['showonly'] == "reg") {
	  $memberoruser=$_language->module['communitymember'];
	  }
	  
		if($notification == "1") {
$empfaenger = "$mailaddy";
$empfaengerCC = array('<>');
$absender = 'Awaylist Notifier<donot@reply>';
// Betreff
$subject = $_language->module['new_absence'];
// Nachricht
$message = ''.$_language->module['message1'].''.$memberoruser.''.$_language->module['message2'].'';

// Baut Header der Mail zusammen
$headers .= 'From:' . $absender . "\n";
$headers .= 'Reply-To:' . $reply . "\n"; 
$headers .= 'X-Mailer: PHP/' . phpversion() . "\n"; 
$headers .= 'X-Sender-IP: ' . $REMOTE_ADDR . "\n"; 
$headers .= "Content-type: text/html\n";

// Extrahiere Emailadressen
$empfaengerString = $empfaenger;
$empfaengerCCString = implode(',', $empfaengerCC);

$headers .= 'Cc: ' . $empfaengerCCString . "\n";

/* Verschicken der Mail */
mail($empfaengerString, $subject, $message, $headers);
}
//Ende Email-Benachrichtigung		
		
		#redirect('index.php?site=awaylist');
		redirect('index.php?site=awaylist', $_language->module['submitted'], '3');
		#echo $_language->module['submitted'];
}

}
//
//
//Überprüfung ob sichtbar für Community oder Clanmember
elseif($ds['showonly'] == "mem") {

// Löschen veralteter Einträge
	$heute = strtotime(date ("d-m-Y"));
// Zum manuellen testen der Löschfunktion
	//$heute = "2011-03-20";
	$ergebnis = safe_query("DELETE FROM ".PREFIX."awaylist WHERE awayto<='$heute'");
//	$ds = mysqli_fetch_array($ergebnis);
		
  $ergebnis = safe_query("SELECT * FROM ".PREFIX."awaylist");

if(isclanmember($userID)) {
#eval ("\$awaylist = \"".gettemplate("awaylist")."\";");
#echo $awaylist;
$template = $GLOBALS["_template"]->replaceTemplate("awaylist", $data_array);
echo $template;
//Ausgabe aller Abwesenheiten falls aktiviert
if($ds['showaways'] == "1") {

#eval ("\$awaylist_tablehead = \"".gettemplate("awaylist_tablehead")."\";");
#echo $awaylist_tablehead;
$template = $GLOBALS["_template"]->replaceTemplate("awaylist_tablehead", $data_array);
echo $template;

$ergebnis=safe_query("SELECT * FROM ".PREFIX."awaylist");
$n=1;
while($ds=mysqli_fetch_array($ergebnis)) {
 if($n%2) {
			$bg1=BG_1;
			$bg2=BG_2;
		}
		else {
			$bg1=BG_3;
			$bg2=BG_4;
		}
// Begrenzung der Ausgabe von Grund auf 25 Zeichen, komplette Ausgabe per Tooltip
  $reasonshort = $ds['reason'];
  if(strlen($reasonshort)>15) {
	    $reasonshort=substr($reasonshort, 0, 15);
		$reasonshort.='..';
	}	

$nick =	cleartext('[flag]'.getcountry($ds['name']).'[/flag]').' <a href="index.php?site=profile&amp;id='.$ds['name'].'">'.getnickname($ds['name']).'</a>';
$awayfrom2 = $ds['awayfrom'];
$awayfrom = date("d-m-Y",$awayfrom2);
$awayto2 = $ds['awayto'];
$awayto = date("d-m-Y",$awayto2);
$reason = $ds['reason'];





// Löschen eigener Abwesenheiten
$name = $userID;
if ($name == $ds['name']) { 
$delete='<input type="button" class="button" onClick="MM_goToURL(\'parent\',\'index.php?site=awaylist&delete=true&ID='.$ds['ID'].'\');return document.MM_returnValue" value="'.$_language->module['delete'].'" name="'.$_language->module['delete'].'" id="delete">';
}
else {
$delete='';
}

//if($_GET['delete']) {
if(isset($_GET['delete'])) $delete2 = $_GET['delete'];
else $delete2='';
if ($delete2 == "true") {

$ID = $_GET['ID'];
$query = "DELETE FROM ".PREFIX."awaylist WHERE ID = $ID";
#$dummy = mysqli_query($query);

#redirect('index.php?site=awaylist');
redirect('index.php?site=awaylist', $_language->module['submitted'], '3');
}




$data_array = array();
$data_array['$nick'] = cleartext('[flag]'.getcountry($ds['name']).'[/flag]').' <a href="index.php?site=profile&amp;id='.$ds['name'].'">'.getnickname($ds['name']).'</a>';
$data_array['$awayfrom2'] = $ds['awayfrom'];
$data_array['$awayfrom'] = date("d-m-Y",$awayfrom2);
$data_array['$awayto2'] = $ds['awayto'];
$data_array['$awayto'] = date("d-m-Y",$awayto2);
$data_array['$reason'] = $ds['reason'];
$data_array['$reasonshort'] = substr($reasonshort, 0, 15);
$data_array['$reasonshort'] = $reasonshort.='..';
	

#$data_array['$delete'] = $delete;




#eval ("\$awaylist_tablecontent = \"".gettemplate("awaylist_tablecontent")."\";");
#echo $awaylist_tablecontent;
$template = $GLOBALS["_template"]->replaceTemplate("awaylist_tablecontent", $data_array);
echo $template;

$n++;
}
$template = $GLOBALS["_template"]->replaceTemplate("awaylist_tablefoot", $data_array);
echo $template;

#eval ("\$awaylist_tablefoot = \"".gettemplate("awaylist_tablefoot")."\";");
#echo $awaylist_tablefoot;

}
}
else {
echo $_language->module['clanmember_only'];
}
}
elseif($ds['showonly'] == "reg") {
if(!$userID) echo ''.$_language->module['login_first'].'<br><br>

	  &#8226; <a href="index.php?site=register">'.$_language->module['register_now'].'</a><br>
	  &#8226; <a href="index.php?site=login">'.$_language->module['login'].'</a>';

else {

$template = $GLOBALS["_template"]->replaceTemplate("awaylist", $data_array);
echo $template;	
#eval ("\$awaylist = \"".gettemplate("awaylist")."\";");
#echo $awaylist;
	  
// Löschen veralteter Einträge
	$heute = strtotime(date ("d-m-Y"));
// Zum manuellen testen der Löschfunktion
	//$heute = "2011-02-20";
	$ergebnis = safe_query("DELETE FROM ".PREFIX."awaylist WHERE awayto<='$heute'");
//	$ds = mysqli_fetch_array($ergebnis);
		
  $ergebnis = safe_query("SELECT * FROM ".PREFIX."awaylist");

//Ausgabe aller Abwesenheiten falls aktiviert
if($ds['showaways'] == "1") {

#eval ("\$awaylist_tablehead = \"".gettemplate("awaylist_tablehead")."\";");
#echo $awaylist_tablehead;
$template = $GLOBALS["_template"]->replaceTemplate("awaylist_tablehead", $data_array);
echo $template;        

$ergebnis=safe_query("SELECT * FROM ".PREFIX."awaylist");
$n=1;
while($ds=mysqli_fetch_array($ergebnis)) {
 if($n%2) {
			$bg1=BG_1;
			$bg2=BG_2;
		}
		else {
			$bg1=BG_3;
			$bg2=BG_4;
		}
// Begrenzung der Ausgabe von Grund auf 15 Zeichen, komplette Ausgabe per Tooltip
  $reasonshort = $ds['reason'];
  if(strlen($reasonshort)>15) {
	    $reasonshort=substr($reasonshort, 0, 15);
		$reasonshort.='..';
	}	


				
$nick =	cleartext('[flag]'.getcountry($ds['name']).'[/flag]').' <a href="index.php?site=profile&amp;id='.$ds['name'].'">'.getnickname($ds['name']).'</a>';
$awayfrom2 = $ds['awayfrom'];
$awayfrom = date("d-m-Y",$awayfrom2);
$awayto2 = $ds['awayto'];
$awayto = date("d-m-Y",$awayto2);
$reason = $ds['reason'];



// Löschen eigener Abwesenheiten
$name = $userID;
if ($name == $ds['name']) { 
$delete='<input type="button" class="button" onClick="MM_goToURL(\'parent\',\'index.php?site=awaylist&delete=true&ID='.$ds['ID'].'\');return document.MM_returnValue" value="'.$_language->module['delete'].'" name="'.$_language->module['delete'].'" id="delete">';
}
else {
$delete='';
}
//if($_GET['delete']) {
if(isset($_GET['delete'])) $delete3 = $_GET['delete'];
else $delete3='';
if ($delete3 == "true") {

$ID = $_GET['ID'];
$query = "DELETE FROM ".PREFIX."awaylist WHERE ID = $ID";
#$dummy = mysqli_query($query);
#redirect('index.php?site=awaylist');
redirect('index.php?site=awaylist', $_language->module['submitted'], '3');
}


$data_array = array();
$data_array['$nick'] = cleartext('[flag]'.getcountry($ds['name']).'[/flag]').' <a href="index.php?site=profile&amp;id='.$ds['name'].'">'.getnickname($ds['name']).'</a>';
$data_array['$awayfrom2'] = $ds['awayfrom'];
$data_array['$awayfrom'] = date("d-m-Y",$awayfrom2);
$data_array['$awayto2'] = $ds['awayto'];
$data_array['$awayto'] = date("d-m-Y",$awayto2);
$data_array['$reason'] = $ds['reason'];
$data_array['$reasonshort'] = substr($reasonshort, 0, 15);
$data_array['$reasonshort'] = $reasonshort.='..';
	

$data_array['$delete'] = $delete;


#eval ("\$awaylist_tablecontent = \"".gettemplate("awaylist_tablecontent")."\";");
#echo $awaylist_tablecontent;
$template = $GLOBALS["_template"]->replaceTemplate("awaylist_tablecontent", $data_array);
echo $template;

$n++;
}

#eval ("\$awaylist_tablefoot = \"".gettemplate("awaylist_tablefoot")."\";");
#echo $awaylist_tablefoot;
$template = $GLOBALS["_template"]->replaceTemplate("awaylist_tablefoot", $data_array);
echo $template;
}
}
}

?>

