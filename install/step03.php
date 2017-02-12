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
$fatal_error = false;
if (version_compare(PHP_VERSION, '5.3.0', '<')) {
    $php_version_check = '<span class="label label-danger">'.$_language->module['no'].'</span>';
    $fatal_error = true;
} else {
    $php_version_check = '<span class="label label-success">'.$_language->module['yes'].'</span>';
}

if (function_exists('mysqli_connect')) {
    $mysql_check = '<span class="label label-success">'.$_language->module['available'].'</span>';
} else {
    $mysql_check = '<span class="label label-danger">'.$_language->module['unavailable'].'</span>';
    $fatal_error = true;
}

if (function_exists('mb_substr')) {
    $mb_check = '<span class="label label-success">'.$_language->module['available'].'</span>';
} else {
    $mb_check = '<span class="label label-danger">'.$_language->module['unavailable'].'</span>';
    $fatal_error = true;
}

?>
<div class="row marketing">
    <div class="col-xs-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title"><?php echo $_language->module['set_chmod']; ?></h3>
			</div>
			<div class="panel-body">
            <div class="table-responsive">
<table class="table table-striped table-hover">
<tr>
  <td colspan="2"><h3><?php echo $_language->module['check_requirements']; ?>:</h3></td>
</tr>
<tr>
  <td><?php echo $_language->module['php_version']; ?> &gt;= 5.2</td>
  <td><?php echo $php_version_check; ?></td>
</tr>
<tr>
  <td><?php echo $_language->module['multibyte_support']; ?></td>
  <td><?php echo $mb_check; ?></td>
</tr>
<tr>
  <td><?php echo $_language->module['mysql_support']; ?></td>
  <td><?php echo $mysql_check; ?></td>
</tr>
<tr>
  <td>_mysql.php</td>
  <td><?php
		if (@file_exists('../_mysql.php') && @is_writable('../_mysql.php')) {
			echo '<span class="label label-success">' . $_language->module['writeable'] . '</span>';
		} else if (is_writable('..')) {
			echo '<span class="label label-success">' . $_language->module['writeable'] . '</span>';
		} else {
			echo '<span class="label label-danger">' . $_language->module['unwriteable'] . '</span><br>
		<div class="alert alert-danger">' . $_language->module['mysql_error'] . '</div>';
		} ?></td>
</tr>
<tr>
  <td>_stylesheet.css</td>
  <td><?php
		if (@file_exists('../_stylesheet.css') && @is_writable('../_stylesheet.css')) {
			echo '<span class="label label-success">' . $_language->module['writeable'] . '</span>';
		} else if (is_writable('..')) {
			echo '<span class="label label-success">' . $_language->module['writeable'] . '</span>';
		} else {
			echo '<span class="label label-danger">' . $_language->module['unwriteable'] . '</span><br>
		<div class="alert alert-danger">' . $_language->module['stylesheet_error'] . '</div>';
		} ?></td>
</tr>
<tr>
  <td><?php echo $_language->module['setting_chmod']; ?></td>
  <td><?php
		$chmodfiles = Array('_mysql.php', '_stylesheet.css', 'demos/', 'downloads/', 'images/articles-pics', 'images/avatars', 'images/banner', 'images/bannerrotation', 'images/clanwar-screens', 'images/flags', 'images/gallery/large', 'images/gallery/thumb', 'images/games', 'images/icons/ranks', 'images/links', 'images/linkus', 'images/news-pics', 'images/news-rubrics', 'images/partners', 'images/smileys', 'images/sponsors', 'images/squadicons', 'images/userpics', 'tmp/');
		sort($chmodfiles);
		$error = array();
		foreach ($chmodfiles as $file) {
	if (!is_writable('../' . $file)) {
		echo '-> ' . $file . '';
	if (!@chmod('../' . $file, 0777)) $error[] = $file . '';
		}
	}
	?><?php
		if (count($error)) {
			sort($error);
			echo '<span class="label label-danger">' . $_language->module['chmod_error'] . '</span>:';
			foreach ($error as $value)
				echo '<span class="label label-danger">' . $value . '</span>';
			} else echo '<span class="label label-success">' . $_language->module['successful'] . '</span>';
		?></td>
</tr>
</table>
            </div>
            <input type="hidden" name="hp_url" value="<?php echo str_replace('http://', '', $_POST['hp_url']); ?>">
				<?php if (!$fatal_error) { ?>
                    <div class="pull-right"><a class="btn btn-primary" href="javascript:document.ws_install.submit()">continue</a></div>
                <?php } ?>
			</div>
		</div>
    </div>
</div> <!-- end row -->
