<?php
if (!ispageadmin($userID) || mb_substr(basename($_SERVER['REQUEST_URI']), 0, 15) != "admincenter.php") {
	die("Access Denied | Zugriff verweigert");
}

$tmp = safe_query("SELECT * FROM `".PREFIX."navigation_main`");
$x = 0; $main_nav = "";
if(mysqli_num_rows($tmp)) {
	$main_sort = ""; $y=1;
	while($tmp2=mysqli_fetch_array($tmp)) {
		$main_sort .= '<option value="'.$y.'">'.$y.'</option>';
		$main_nav .= '<option value="'.$tmp2['mnavID'].'">'.$tmp2['name'].'</option>';
		$x++; $y++;
	}
}
unset($tmp, $tmp2);
$tmp = safe_query("SELECT * FROM `".PREFIX."navigation_sub`");
$sub_sort = "";
if(mysqli_num_rows($tmp)) {
	$sub_sort = ""; $y=1;
	while($tmp2=mysqli_fetch_array($tmp)) {
		$sub_sort .= '<option value="'.$y.'">'.$y.'</option>';
		$y++;
	}
}
if(isset($_GET['do'])) { $do = $_GET['do']; } else { $do=""; }
if(isset($_GET['op'])) { $op = $_GET['op']; } else { $op=""; }
if(isset($_GET['mnav'])) { $mnav = $_GET['mnav']; } else { $mnav=""; }
if(isset($_GET['snav'])) { $snav = $_GET['snav']; } else { $snav=""; }
#lang -> add language->Module()                  ### T O D O ###
$msg_success = "Operation Successfull / Vorgang Erfolgreich durchgef&uuml;hrt";
$msg_failed = "Operation Failed / Vorgang fehlgeschlagen";
$msg_order_sort = "The main and sub-navigation is displayed by the numerical order.<br />Die Haupt- und Unternavigation wird sortiert nach der Numerischen Reihenfolge dargestellt. <br /><br />";
# delete main nav
if($do=="del" && $mnav!="") {
	$run = safe_query("DELETE FROM `".PREFIX."navigation_main` WHERE `mnavID` = '".$mnav."' LIMIT 1");
	if($run) {
		echo $msg_success;
	} else {
		echo $msg_failed;
	}
	redirect("admincenter.php?site=navigation",3);
	return false;
}
# new main navi
if($do=="new" && $op=="main") {
	$x = $x +1;
	if(isset($main_sort)) { $main_sort .= '<option value="'.$x.'" selected>'.$x.'</option>'; } else { $main_sort = '<option value="'.$x.'" selected>'.$x.'</option>'; }
	echo '<div class="panel panel-default">
<div class="panel-heading">
                            <i class="fa fa-indent"></i> Navigation
                        </div>
                        <div class="panel-body">

                        <a href="admincenter.php?site=navigation" class="white">Navigation</a>
                         &raquo; New Main-Navigation<br><br>';
	echo '
		<form role="form" action="admincenter.php?site=navigation" method="POST">
			<div class="form-group" style="padding: 8px;">
			<label>Name</label><input type="text" class="form-control" id="name" name="name" value="">
			<label>Link</label><input type="text" class="form-control" id="link" name="link" value="">
			<label for="sort">Sort</label><select class="form-control" name="sort" id="sort">'.$main_sort.'</select>
			<label>Dropdown?</label><select class="form-control" name="dropdown" id="dropdown"><option value="0">0</option><option value="1" selected>1</option></select>
			<br />
			<button type="submit" name="sve_newm" class="btn btn-default">Save / Speichern</button></div></form></div></div>
	';
	return false;
}
# save main new
if(isset($_POST['sve_newm'])) {
	$run = safe_query("INSERT INTO `".PREFIX."navigation_main` (`name`, `link`, `sort`, `isdropdown`) VALUES ('".$_POST['name']."', '".$_POST['link']."', '".$_POST['sort']."', '".$_POST['dropdown']."');");
	if($run) {
		echo $msg_success;
	} else {
		echo $msg_failed;
	}
	redirect("admincenter.php?site=navigation",3);
	return false;
}
# save main edit
if(isset($_POST['sve_edit'])) {
	$id=intval($_POST['mID']);
	try {
		$run = safe_query("UPDATE `".PREFIX."navigation_main` SET `name` = '".$_POST['name']."', `link` = '".$_POST['link']."', `sort` = '".$_POST['sort']."', `isdropdown` = '".$_POST['dropdown']."' WHERE `mnavID` = '".$id." LIMIT 1';");
		if($run) {
			echo $msg_success;
			redirect("admincenter.php?site=navigation", "3");
		} else {
			echo $msg_failed;
			redirect("admincenter.php?site=navigation", "3");
		}
	} catch (Exception $e) {
		echo $e-message();
	}
	return false;
}
# delete sub nav
if($do=="del" && $snav!="") {
	$run = safe_query("DELETE FROM `".PREFIX."navigation_sub` WHERE `snavID` = '".$snav."' LIMIT 1");
	if($run) {
		echo $msg_success;
	} else {
		echo $msg_failed;
	}
	redirect("admincenter.php?site=navigation",3);
	return false;
}
# save sub edit
if(isset($_POST['sve_subedit'])) {
	$id=intval($_POST['sID']);
	try {
		$run = safe_query("UPDATE `".PREFIX."navigation_sub` SET `mnav_ID` = '".$_POST['mnav']."', `name` = '".$_POST['name']."', `link` = '".$_POST['link']."', `sort` = '".$_POST['sort']."', `indropdown` = '".$_POST['dropdown']."' WHERE `snavID` = '".$id."' LIMIT 1;");
		if($run) {
			echo $msg_success;
			redirect("admincenter.php?site=navigation", "3");
		} else {
			echo $msg_failed;
			redirect("admincenter.php?site=navigation", "3");
		}
	} catch (Exception $e) {
		echo $e-message();
	}
	return false;
}
# new sub navi
if($do=="new" && $op=="sub") {
	$x = $x +1;
	echo '<div class="panel panel-default">
<div class="panel-heading">
                            <i class="fa fa-indent"></i> Navigation
                        </div>
                        <div class="panel-body">

                        <a href="admincenter.php?site=navigation" class="white">Navigation</a>
                         &raquo; New Sub-Navigation<br><br>';
	echo '
		<form role="form" action="admincenter.php?site=navigation" method="POST">
			<div class="form-group" style="padding: 8px;">
			<label>Name</label><input type="text" class="form-control" id="name" name="name" value="">
			<label>Link</label><input type="text" class="form-control" id="link" name="link" value="">
			<label for="sort">Sort</label><select class="form-control" name="sort" id="sort">'.$sub_sort.'</select>
			<label>in Dropdown?</label><select class="form-control" name="indropdown" id="indropdown"><option value="0">0</option><option value="1" selected>1</option></select>
			<label for="mna">MainNav</label><select class="form-control" name="mnav" id="mnav">'.$main_nav.'</select>
			<br />
			<button type="submit" name="sve_news" class="btn btn-default">Save / Speichern</button></div></form></div></div>
	';
	return false;
}
# save sub new
if(isset($_POST['sve_news'])) {
	$run = safe_query("INSERT INTO `".PREFIX."navigation_sub` (`mnav_ID`, `name`, `link`, `sort`, `indropdown`) VALUES ('".$_POST['mnav']."', '".$_POST['name']."', '".$_POST['link']."', '".$_POST['sort']."', '".$_POST['indropdown']."');");
	if($run) {
		echo $msg_success;
	} else {
		echo $msg_failed;
	}
	redirect("admincenter.php?site=navigation",3);
	return false;
}
?>
<script type="text/javascript">
function edit_main(id,name, link) {
	var obj = document.getElementById(id)
	obj.innerHTML = ' <form role="form" action="admincenter.php?site=navigation" method="POST"><div class="form-group" style="padding: 8px;"><input type="hidden" value="'+ id +'" name="mID" /><label>Name</label><input type="text" class="form-control" id="name" name="name" value="'+name+'"><label>Link</label><input type="text" class="form-control" id="link" name="link" value="'+link+'"><label for="sort">Sort</label><select class="form-control" name="sort" id="sort"><?php echo $main_sort; ?></select><label>Dropdown?</label><select class="form-control" name="dropdown" id="dropdown"><option value="0">0</option><option value="1" selected>1</option></select><br /><button type="submit" name="sve_edit" class="btn btn-default">Save / Speichern</button></div></form>';
}
function edit_sub(id,name,link,sort) {
	var obj = document.getElementById(id)
	obj.innerHTML = '<form role="form" action="admincenter.php?site=navigation" method="POST"><div class="form-group" style="padding: 8px;"><input type="hidden" value="'+ id +'" name="sID" /><label>Name</label><input type="text" class="form-control" id="name" name="name" value="'+name+'"><label>Link</label><input type="text" class="form-control" id="link" name="link" value="'+link+'"><label for="sort">Sort</label><select class="form-control" name="sort" id="sort"><?php echo $main_sort; ?></select><label>Dropdown?</label><select class="form-control" name="dropdown" id="dropdown"><option value="0">0</option><option value="1" selected>1</option></select><label for="mnav">MainNav</label><select class="form-control" name="mnav" id="mnav"><?php echo $main_nav; ?></select><br /><button type="submit" name="sve_subedit" class="btn btn-default">Save / Speichern</button></div></form>';
}
</script>
<div class="panel panel-default">
<div class="panel-heading">
                            <i class="fa fa-indent"></i> Navigation
                        </div>
                        <div class="panel-body">
<a href="admincenter.php?site=navigation&do=new&op=main" class="btn btn-primary btn-xs" type="button">New Main-Navigation</a> &nbsp;&nbsp;|&nbsp;&nbsp; <a href="admincenter.php?site=navigation&do=new&op=sub" class="btn btn-primary btn-xs" type="button">New Sub-Navigation</a><br />
<br />
<?php
echo $msg_order_sort;
$res = safe_query("SELECT * FROM `".PREFIX."navigation_main` ORDER BY `sort` ");
while($row=mysqli_fetch_array($res)) {
	echo '
<div class="panel panel-default">
	<div class="panel-heading">
		
		  <div class="col-sm-8">&nbsp;&nbsp;<span id="'.$row['mnavID'].'">('.$row['sort'].') '.$row['name'].'</span></div>
		  <div class="col-sm-4 text-right">


		  

<a href="javascript: edit_main('.$row['mnavID'].', \''.$row['name'].'\', \''.$row['link'].'\')" class="hidden-xs hidden-sm btn btn-warning btn-xs" type="button">Edit</a>
<a href="javascript: edit_main('.$row['mnavID'].', \''.$row['name'].'\', \''.$row['link'].'\')" class="mobile visible-xs visible-sm" type="button"><i class="fa fa-pencil"></i></a>


		   &nbsp;&nbsp; 




		  

<a href="admincenter.php?site=navigation&do=del&mnav='.$row['mnavID'].'" class="hidden-xs hidden-sm btn btn-danger btn-xs" type="button">Delete</a>
<a href="admincenter.php?site=navigation&do=del&mnav='.$row['mnavID'].'" class="mobile visible-xs visible-sm" type="button"><i class="fa fa-times"></i></a>



		  
		</div>
	</div>';

	$rex = safe_query("SELECT * FROM `".PREFIX."navigation_sub` WHERE `mnav_ID`='".$row['mnavID']."' ORDER BY `sort` ");
	while($rox=mysqli_fetch_array($rex)) {
		echo '
	<div class="panel-body">
		
		 
		  <div class="col-sm-8">&nbsp;&nbsp;<span id="'.$rox['snavID'].'">('.$rox['sort'].') MN: '.$rox['mnav_ID'].' Name: '.$rox['name'].' Link: '.$rox['link'].'</span></div>
		  <div class="col-sm-4 text-right">

		  
<a href="javascript: edit_sub('.$rox['snavID'].', \''.$rox['name'].'\', \''.$rox['link'].'\', \''.$rox['sort'].'\')" class="hidden-xs hidden-sm btn btn-warning btn-xs" type="button">Edit</a>
<a href="javascript: edit_sub('.$rox['snavID'].', \''.$rox['name'].'\', \''.$rox['link'].'\', \''.$rox['sort'].'\')" class="mobile visible-xs visible-sm" type="button"><i class="fa fa-pencil"></i></a>

		   &nbsp;&nbsp; 

		  

<a href="admincenter.php?site=navigation&do=del&snav='.$rox['snavID'].'" class="hidden-xs hidden-sm btn btn-danger btn-xs" type="button">Delete</a>
<a href="admincenter.php?site=navigation&do=del&snav='.$rox['snavID'].'" class="mobile visible-xs visible-sm" type="button"><i class="fa fa-times"></i></a>
		    
		    
		
	    </div>
	</div><hr>';

	}
	echo '</div>';
}
?></div>
</div>