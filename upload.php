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

include("_mysql.php");
include("_settings.php");
include("_functions.php");
$_language->readModule('upload');
if (!isanyadmin($userID)) {
    die($_language->module[ 'no_access' ]);
}

if (isset($_GET[ 'cwID' ])) {
    $filepath = "images/clanwar-screens/";
    $table = "clanwars";
    $tableid = "cwID";
    $id = (int)$_GET[ 'cwID' ];
} elseif (isset($_GET[ 'newsID' ])) {
    $filepath = "images/news-pics/";
    $table = "news";
    $tableid = "newsID";
    $id = (int)$_GET[ 'newsID' ];
} elseif (isset($_GET[ 'articlesID' ])) {
    $filepath = "images/articles-pics/";
    $table = "articles";
    $tableid = "articlesID";
    $id = (int)$_GET[ 'articlesID' ];
} else {
    die($_language->module[ 'invalid_access' ]);
}

if (isset($_GET[ 'action' ])) {
    $action = $_GET[ 'action' ];
} else {
    $action = null;
}

if (isset($_POST[ 'submit' ])) {
    $_language->readModule('formvalidation', true);

    $screen = new \webspell\HttpUpload('screen');

    if ($screen->hasFile()) {
        if ($screen->hasError() === false) {
            $file = $id . '_' . time() . "." .$screen->getExtension();
            $new_name = $filepath . $file;
            if ($screen->saveAs($new_name)) {
                @chmod($new_name, $new_chmod);
                $ergebnis = safe_query("SELECT screens FROM " . PREFIX . "$table WHERE $tableid='$id'");
                $ds = mysqli_fetch_array($ergebnis);
                $screens = explode("|", $ds[ 'screens' ]);
                $screens[ ] = $file;
                $screens_string = implode("|", $screens);

                safe_query(
                    "UPDATE
                    " . PREFIX . $table . "
                    SET
                        screens='" . $screens_string . "'
                    WHERE
                        " . $tableid . "='" . (int)$id . "'"
                );
            }
        }
    }
    header("Location: upload.php?$tableid=$id");
} elseif ($action == "delete") {
    $file = basename($_GET[ 'file' ]);
    if (file_exists($filepath . $file)) {
        @unlink($filepath . $file);
    }

    $ergebnis = safe_query("SELECT screens FROM " . PREFIX . "$table WHERE $tableid='$id'");
    $ds = mysqli_fetch_array($ergebnis);
    $screens = explode("|", $ds[ 'screens' ]);
    foreach ($screens as $pic) {
        if ($pic != $file) {
            $newscreens[ ] = $pic;
        }
    }
    if (is_array($newscreens)) {
        $newscreens_string = implode("|", $newscreens);
    }
    safe_query("UPDATE " . PREFIX . "$table SET screens='$newscreens_string' WHERE $tableid='$id'");

    header("Location: upload.php?$tableid=$id");
} else {
    echo '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="utf-8">
    <meta name="description" content="Website using webSPELL-NOR CMS">
    <meta name="author" content="webspell-nor.de">    
    <title>' . $_language->module[ 'file_upload' ] . '</title>
    <link href="css/page.css" rel="stylesheet">';
    foreach ($components['css'] as $component) {
        echo '<link href="' . $component . '" rel="stylesheet">';
	}
	foreach ($components['js'] as $component) {
    echo '<script src="' . $component . '"></script>';
	}
    echo '<script src="js/bbcode.js"></script>
	</head>
<body>
<div class="panel panel-default">
<div class="panel-heading">' . $_language->module[ 'file_upload' ] . '</div>
<form method="post" action="upload.php?' . $tableid . '=' . $id . '" enctype="multipart/form-data">
<div class="col-md-12">
	<div class="row">
		<table class="table no-border">
		    <tr>
			        <td><input type="file" name="screen" class="form-control"></td>
			        <td class="pull-right"><input type="submit" name="submit" value="' . $_language->module[ 'upload' ] . '" class="btn btn-primary"></td>
			        </tr>
			        </table>
			        </div>
</div>
<div class="col-md-12">
<div class="row">
<table class="table">
    <tr>
        <hr>
        <h2>' . $_language->module[ 'existing_files' ] . ':</h2>
        <table class="table">';

    $ergebnis = safe_query("SELECT screens FROM " . PREFIX . "$table WHERE $tableid='$id'");

    $ds = mysqli_fetch_array($ergebnis);
    $screens = array();
    if (!empty($ds[ 'screens' ])) {
        $screens = explode("|", $ds[ 'screens' ]);
    }
    if (is_array($screens)) {
        foreach ($screens as $screen) {
            if ($screen != "") {
                echo '<tr>
            <td><a href="' . $filepath . $screen . '" target="_blank">' . $screen . '</a></td>
            <td>
                <input type="text" name="pic" size="70"
                value="&lt;img src=&quot;' . $filepath . $screen . '&quot; border=&quot;0&quot; align=&quot;left&quot; style=&quot;padding:4px;&quot; alt=&quot;&quot; /&gt;" class="form-control">
            </td>
            <td>
                <input type="button" onclick="AddCodeFromWindow(\'[img]' . $filepath . $screen . '[/img] \')"
                    value="' . $_language->module[ 'add_to_message' ] . '" class="btn btn-primary">
            </td>
            <td>
                <input type="button" onclick="MM_confirm(
                        \'' . $_language->module[ 'delete' ] . '\',
                        \'upload.php?action=delete&amp;' . $tableid . '=' . $id . '&amp;file=' . basename($screen) . '\'
                    )" value="' . $_language->module[ 'delete' ] . '" class="btn btn-danger">
            </td>
            </tr>';
            }
        }
    }

    echo '</table></td>
    </tr>
    </table>
    </form>
    <div class="panel-footer">
		<input type="button" onclick="javascript:self.close()" value="' . $_language->module[ 'close_window' ] . '" class="btn btn-default">
	</div>
	<div class="clearfix"></div>
    </div>
    </div>
    </div>
    </body>
    </html>';
}
