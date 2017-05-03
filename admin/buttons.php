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

$_language->readModule('styles', false, true);

if (!ispageadmin($userID) || mb_substr(basename($_SERVER[ 'REQUEST_URI' ]), 0, 15) != "admincenter.php") {
    die($_language->module[ 'access_denied' ]);
}

echo '<div class="panel panel-default">
  <div class="panel-heading">
                            <i class="fa fa-thumbs-up"></i> '.$_language->module['styles'].'
                        </div>
                        </div>
                        <div class="panel panel-default">
                        
            <ul class="nav nav-tabs-primary">    
    <li role="presentation"><a href="admincenter.php?site=styles">Style</a></li>
    <li role="presentation" class="active"><a href="admincenter.php?site=buttons">Buttons</a></li>
    <li role="presentation"><a href="admincenter.php?site=moduls">Module</a></li>
    <li role="presentation"><a href="admincenter.php?site=css">.css</a></li>
</ul>
<ol class="breadcrumb-primary"> </ol>
 <div class="panel-body">';

if (isset($_POST[ 'submit' ])) {
    $CAPCLASS = new \webspell\Captcha;
    if ($CAPCLASS->checkCaptcha(0, $_POST[ 'captcha_hash' ])) {
        $error = array();
        $sem = '/^#[a-fA-F0-9]{6}/';
        
        if (count($error)) {
            echo '<b>' . $_language->module[ 'errors' ] . ':</b><br /><ul>';

            foreach ($error as $err) {
                echo '<li>' . $err . '</li>';
            }
            echo '</ul><br /><input type="button" onclick="javascript:history.back()" value="' .
                $_language->module[ 'back' ] . '" />';
        } else {
            safe_query(
                "UPDATE " . PREFIX . "buttons
                SET button1='" . $_POST[ 'button1' ] . "',
                button2='" . $_POST[ 'button2' ] . "',
                button3='" . $_POST[ 'button3' ] . "',
                button4='" . $_POST[ 'button4' ] . "',
                button5='" . $_POST[ 'button5' ] . "',
                button6='" . $_POST[ 'button6' ] . "',
                button7='" . $_POST[ 'button7' ] . "',
                button8='" . $_POST[ 'button8' ] . "',
                button9='" . $_POST[ 'button9' ] . "',
                button10='" . $_POST[ 'button10' ] . "',
                button11='" . $_POST[ 'button11' ] . "',
                button12='" . $_POST[ 'button12' ] . "',
                button13='" . $_POST[ 'button13' ] . "',
                button14='" . $_POST[ 'button14' ] . "',
                button15='" . $_POST[ 'button15' ] . "',
                button16='" . $_POST[ 'button16' ] . "',
                button17='" . $_POST[ 'button17' ] . "',
                button18='" . $_POST[ 'button18' ] . "',
                button19='" . $_POST[ 'button19' ] . "',
                button20='" . $_POST[ 'button20' ] . "',
                button21='" . $_POST[ 'button21' ] . "',
                button22='" . $_POST[ 'button22' ] . "',
                button23='" . $_POST[ 'button23' ] . "',
                button24='" . $_POST[ 'button24' ] . "' "
            );
           
            redirect("admincenter.php?site=buttons", "", 0);
        }
    } else {
        echo $_language->module[ 'transaction_invalid' ];
    }
} else {
    $ergebnis = safe_query("SELECT * FROM " . PREFIX . "buttons");
    $ds = mysqli_fetch_array($ergebnis);

    

    $CAPCLASS = new \webspell\Captcha;
    $CAPCLASS->createTransaction();
    $hash = $CAPCLASS->getHash();


    echo '<form class="form-horizontal" method="post" action="admincenter.php?site=buttons" enctype="multipart/form-data">

<div class="page-header">
  <h1>Button</h1>
</div> 
<table class="table table-striped">
    <thead>
    <th>Button:</th>
      <th>background-color:</th>
      <th>hover bg-color:</th>
      
      <th>font color:</th>
      <th>border color:</th>
      
    </thead> </table>
<div class="row">

<div class="col-md-12">

<button class="btn btn-default" />Default</button><br><br>
</div>

<div class="col-md-3">

<div class="form-group">
    <label class="col-sm-4 control-label">#ffffff</label>
    <div id="cp1" class="input-group colorpicker-component col-sm-7"><span class="text-muted small"><em>
    <input type="text" value="' . $ds[ 'button1' ] . '" class="form-control" name="button1" /></em></span> <span class="input-group-addon"><i></i></span> 
    </div>
  </div>
  </div>
  <div class="col-md-3">

  <div class="form-group">
  <label class="col-sm-4 control-label">#e6e6e6</label>
    <div id="cp2" class="input-group colorpicker-component col-sm-7"><span class="text-muted small"><em>
    <input type="text" value="' . $ds[ 'button2' ] . '" class="form-control" name="button2" /></em></span> <span class="input-group-addon"><i></i></span> 
    </div>
  </div>

  </div>
  <div class="col-md-3">
 <div class="form-group">
 <label class="col-sm-4 control-label">#333333</label>
     <div id="cp3" class="input-group colorpicker-component col-sm-7"><span class="text-muted small"><em>
    <input type="text" value="' . $ds[ 'button3' ] . '" class="form-control" name="button3" /></em></span> <span class="input-group-addon"><i></i></span> 
    </div>
  </div>
</div>

<div class="col-md-3">
 <div class="form-group">
 <label class="col-sm-4 control-label">#adadad</label>
     <div id="cp19" class="input-group colorpicker-component col-sm-7"><span class="text-muted small"><em>
    <input type="text" value="' . $ds[ 'button19' ] . '" class="form-control" name="button19" /></em></span> <span class="input-group-addon"><i></i></span> 
    </div>
  </div>
</div>

  <div class="col-md-12">
<hr>
</div>

<div class="col-md-12">

<button class="btn btn-primary" />Primary</button><br><br>
</div>

<div class="col-md-3">

<div class="form-group">
    <label class="col-sm-4 control-label">#0088cc</label>
    <div id="cp4" class="input-group colorpicker-component col-sm-7"><span class="text-muted small"><em>
    <input type="text" value="' . $ds[ 'button4' ] . '" class="form-control" name="button4" /></em></span> <span class="input-group-addon"><i></i></span> 
    </div>
  </div>
  </div>
  <div class="col-md-3">

  <div class="form-group">
  <label class="col-sm-4 control-label">#0044cc</label>
    <div id="cp5" class="input-group colorpicker-component col-sm-7"><span class="text-muted small"><em>
    <input type="text" value="' . $ds[ 'button5' ] . '" class="form-control" name="button5" /></em></span> <span class="input-group-addon"><i></i></span> 
    </div>
  </div>

  </div>
  <div class="col-md-3">
 <div class="form-group">
 <label class="col-sm-4 control-label">#ffffff</label>
     <div id="cp6" class="input-group colorpicker-component col-sm-7"><span class="text-muted small"><em>
    <input type="text" value="' . $ds[ 'button6' ] . '" class="form-control" name="button6" /></em></span> <span class="input-group-addon"><i></i></span> 
    </div>
  </div>
</div>

<div class="col-md-3">
 <div class="form-group">
 <label class="col-sm-4 control-label">#2E6DA4</label>
     <div id="cp20" class="input-group colorpicker-component col-sm-7"><span class="text-muted small"><em>
    <input type="text" value="' . $ds[ 'button20' ] . '" class="form-control" name="button20" /></em></span> <span class="input-group-addon"><i></i></span> 
    </div>
  </div>
</div>

  <div class="col-md-12">
<hr>
</div>


<div class="col-md-12">

<button class="btn btn-success" />Success</button><br><br>
</div>

<div class="col-md-3">

<div class="form-group">
    <label class="col-sm-4 control-label">#5cb85c</label>
    <div id="cp7" class="input-group colorpicker-component col-sm-7"><span class="text-muted small"><em>
    <input type="text" value="' . $ds[ 'button7' ] . '" class="form-control" name="button7" /></em></span> <span class="input-group-addon"><i></i></span> 
    </div>
  </div>
  </div>
  <div class="col-md-3">

  <div class="form-group">
  <label class="col-sm-4 control-label">#449d44</label>
    <div id="cp8" class="input-group colorpicker-component col-sm-7"><span class="text-muted small"><em>
    <input type="text" value="' . $ds[ 'button8' ] . '" class="form-control" name="button8" /></em></span> <span class="input-group-addon"><i></i></span> 
    </div>
  </div>

  </div>
  <div class="col-md-3">
 <div class="form-group">
 <label class="col-sm-4 control-label">#ffffff</label>
     <div id="cp9" class="input-group colorpicker-component col-sm-7"><span class="text-muted small"><em>
    <input type="text" value="' . $ds[ 'button9' ] . '" class="form-control" name="button9" /></em></span> <span class="input-group-addon"><i></i></span> 
    </div>
  </div>
</div>

<div class="col-md-3">
 <div class="form-group">
 <label class="col-sm-4 control-label">#398439</label>
     <div id="cp21" class="input-group colorpicker-component col-sm-7"><span class="text-muted small"><em>
    <input type="text" value="' . $ds[ 'button21' ] . '" class="form-control" name="button21" /></em></span> <span class="input-group-addon"><i></i></span> 
    </div>
  </div>
</div>

  <div class="col-md-12">
<hr>
</div>

<div class="col-md-12">

<button class="btn btn-info" />Info</button><br><br>
</div>

<div class="col-md-3">

<div class="form-group">
    <label class="col-sm-4 control-label">#5bc0de</label>
    <div id="cp10" class="input-group colorpicker-component col-sm-7"><span class="text-muted small"><em>
    <input type="text" value="' . $ds[ 'button10' ] . '" class="form-control" name="button10" /></em></span> <span class="input-group-addon"><i></i></span> 
    </div>
  </div>
  </div>
  <div class="col-md-3">

  <div class="form-group">
  <label class="col-sm-4 control-label">#2f96b4</label>
    <div id="cp11" class="input-group colorpicker-component col-sm-7"><span class="text-muted small"><em>
    <input type="text" value="' . $ds[ 'button11' ] . '" class="form-control" name="button11" /></em></span> <span class="input-group-addon"><i></i></span> 
    </div>
  </div>

  </div>
  <div class="col-md-3">
 <div class="form-group">
 <label class="col-sm-4 control-label">#ffffff</label>
     <div id="cp12" class="input-group colorpicker-component col-sm-7"><span class="text-muted small"><em>
    <input type="text" value="' . $ds[ 'button12' ] . '" class="form-control" name="button12" /></em></span> <span class="input-group-addon"><i></i></span> 
    </div>
  </div>
</div>

<div class="col-md-3">
 <div class="form-group">
 <label class="col-sm-4 control-label">#269abc</label>
     <div id="cp22" class="input-group colorpicker-component col-sm-7"><span class="text-muted small"><em>
    <input type="text" value="' . $ds[ 'button22' ] . '" class="form-control" name="button22" /></em></span> <span class="input-group-addon"><i></i></span> 
    </div>
  </div>
</div>

  <div class="col-md-12">
<hr>
</div>

<div class="col-md-12">

<button class="btn btn-warning" />Warning</button><br><br>
</div>

<div class="col-md-3">

<div class="form-group">
    <label class="col-sm-4 control-label">#fbb450</label>
    <div id="cp13" class="input-group colorpicker-component col-sm-7"><span class="text-muted small"><em>
    <input type="text" value="' . $ds[ 'button13' ] . '" class="form-control" name="button13" /></em></span> <span class="input-group-addon"><i></i></span> 
    </div>
  </div>
  </div>
  <div class="col-md-3">

  <div class="form-group">
  <label class="col-sm-4 control-label">#f89406</label>
    <div id="cp14" class="input-group colorpicker-component col-sm-7"><span class="text-muted small"><em>
    <input type="text" value="' . $ds[ 'button14' ] . '" class="form-control" name="button14" /></em></span> <span class="input-group-addon"><i></i></span> 
    </div>
  </div>

  </div>
  <div class="col-md-3">
 <div class="form-group">
 <label class="col-sm-4 control-label">#ffffff</label>
     <div id="cp15" class="input-group colorpicker-component col-sm-7"><span class="text-muted small"><em>
    <input type="text" value="' . $ds[ 'button15' ] . '" class="form-control" name="button15" /></em></span> <span class="input-group-addon"><i></i></span> 
    </div>
  </div>
</div>

<div class="col-md-3">
 <div class="form-group">
 <label class="col-sm-4 control-label">#d58512</label>
     <div id="cp23" class="input-group colorpicker-component col-sm-7"><span class="text-muted small"><em>
    <input type="text" value="' . $ds[ 'button23' ] . '" class="form-control" name="button23" /></em></span> <span class="input-group-addon"><i></i></span> 
    </div>
  </div>
</div>

  <div class="col-md-12">
<hr>
</div>

<div class="col-md-12">

<button class="btn btn-danger" />Danger</button><br><br>
</div>

<div class="col-md-3">

<div class="form-group">
    <label class="col-sm-4 control-label">#ee5f5b</label>
    <div id="cp16" class="input-group colorpicker-component col-sm-7"><span class="text-muted small"><em>
    <input type="text" value="' . $ds[ 'button16' ] . '" class="form-control" name="button16" /></em></span> <span class="input-group-addon"><i></i></span> 
    </div>
  </div>
  </div>
  <div class="col-md-3">

  <div class="form-group">
  <label class="col-sm-4 control-label">#bd362f</label>
    <div id="cp17" class="input-group colorpicker-component col-sm-7"><span class="text-muted small"><em>
    <input type="text" value="' . $ds[ 'button17' ] . '" class="form-control" name="button17" /></em></span> <span class="input-group-addon"><i></i></span> 
    </div>
  </div>

  </div>
  <div class="col-md-3">
 <div class="form-group">
 <label class="col-sm-4 control-label">#ffffff</label>
     <div id="cp18" class="input-group colorpicker-component col-sm-7"><span class="text-muted small"><em>
    <input type="text" value="' . $ds[ 'button18' ] . '" class="form-control" name="button18" /></em></span> <span class="input-group-addon"><i></i></span> 
    </div>
  </div>
</div>

<div class="col-md-3">
 <div class="form-group">
 <label class="col-sm-4 control-label">#ac2925</label>
     <div id="cp24" class="input-group colorpicker-component col-sm-7"><span class="text-muted small"><em>
    <input type="text" value="' . $ds[ 'button24' ] . '" class="form-control" name="button24" /></em></span> <span class="input-group-addon"><i></i></span> 
    </div>
  </div>
</div>

  <div class="col-md-12">
<hr>
</div>












  <div class="form-group">
    <div class="col-sm-offset-1 col-sm-11">
      <input type="hidden" name="captcha_hash" value="'.$hash.'" />
  <button class="btn btn-primary btn-xs" type="submit" name="submit" />'.$_language->module['update'].'</button>
    </div>
  </div>
</form>';
}
echo '</div></div>';
?>
