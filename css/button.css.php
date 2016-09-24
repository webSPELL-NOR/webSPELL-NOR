<?php
  chdir('../');
include("_mysql.php");
include("_settings.php");
include("_functions.php");


header('Content-type: text/css');
$sql = safe_query("select * from ".PREFIX."buttons");

$ds = mysqli_fetch_array($sql);
?>
.btn-default{}
.btn-default{
color:<?php echo $ds['button3']?>;
background-color:<?php echo $ds['button1']?>;
border-color:#ccc
}
.btn-default.focus,.btn-default:focus{
color:<?php echo $ds['button3']?>;
background-color:<?php echo $ds['button2']?>;
border-color:#8c8c8c
}
.btn-default:hover{
color:<?php echo $ds['button3']?>;
background-color:<?php echo $ds['button2']?>;
border-color:#adadad
}
.btn-default.active,.btn-default:active,.open>.dropdown-toggle.btn-default{
color:<?php echo $ds['button3']?>;
background-color:<?php echo $ds['button2']?>;
border-color:#adadad
}


<!-- Primary ==================================== -->


.btn-primary{}
.btn-primary{ 
  color:<?php echo $ds['button6']?>; 
  background-color:<?php echo $ds['button4']?>; 
  border-color:#2E6DA4; 
} 
 
.btn-primary:hover, 
.btn-primary:focus, 
.btn-primary:active, 
.btn-primary.active, 
.open .dropdown-toggle.btn-primary { 
  color: <?php echo $ds['button6']?>; 
  background-color: <?php echo $ds['button5']?>; 
  border-color: #2E6DA4; 
} 
 
.btn-primary:active, 
.btn-primary.active, 
.open .dropdown-toggle.btn-primary { 
  background-image: none; 
} 
 
.btn-primary.disabled, 
.btn-primary[disabled], 
fieldset[disabled] .btn-primary, 
.btn-primary.disabled:hover, 
.btn-primary[disabled]:hover, 
fieldset[disabled] .btn-primary:hover, 
.btn-primary.disabled:focus, 
.btn-primary[disabled]:focus, 
fieldset[disabled] .btn-primary:focus, 
.btn-primary.disabled:active, 
.btn-primary[disabled]:active, 
fieldset[disabled] .btn-primary:active, 
.btn-primary.disabled.active, 
.btn-primary[disabled].active, 
fieldset[disabled] .btn-primary.active { 
  background-color: <?php echo $ds['button4']?>; 
  border-color: #2E6DA4; 
} 
 
.btn-primary .badge { 
  color: #0088CC; 
  background-color: #ffffff; 
}


<!-- Success ==================================== -->

.btn-success{}
.btn-success{
color:<?php echo $ds['button9']?>;
background-color:<?php echo $ds['button7']?>;
border-color:#4cae4c
}
.btn-success.focus,.btn-success:focus{
color:<?php echo $ds['button9']?>;
background-color:<?php echo $ds['button8']?>;
border-color:#255625
}
.btn-success:hover{
color:<?php echo $ds['button9']?>;
background-color:<?php echo $ds['button8']?>;
border-color:#398439
}
.btn-success.active,.btn-success:active,.open>.dropdown-toggle.btn-success{
color:<?php echo $ds['button9']?>;
background-color:<?php echo $ds['button8']?>;
border-color:#398439
}

<!-- info ==================================== -->

.btn-info{}
.btn-info{
color:<?php echo $ds['button12']?>;
background-color:<?php echo $ds['button10']?>;
border-color:#46b8da
}
.btn-info.focus,.btn-info:focus{
color:<?php echo $ds['button12']?>;
background-color:<?php echo $ds['button11']?>;
border-color:#1b6d85
}
.btn-info:hover{
color:<?php echo $ds['button12']?>;
background-color:<?php echo $ds['button11']?>;
border-color:#269abc
}
.btn-info.active,.btn-info:active,.open>.dropdown-toggle.btn-info{
color:<?php echo $ds['button12']?>;
background-color:<?php echo $ds['button11']?>;
border-color:#269abc
}

<!-- warning ==================================== -->

.btn-warning{}
.btn-warning{
color:<?php echo $ds['button15']?>;
background-color:<?php echo $ds['button13']?>;
border-color:#eea236
}
.btn-warning.focus,.btn-warning:focus{
color:<?php echo $ds['button15']?>;
background-color:<?php echo $ds['button14']?>;
border-color:#985f0d
}
.btn-warning:hover{
color:<?php echo $ds['button15']?>;
background-color:<?php echo $ds['button14']?>;
border-color:#d58512
}
.btn-warning.active,.btn-warning:active,.open>.dropdown-toggle.btn-warning{
color:<?php echo $ds['button15']?>;
background-color:<?php echo $ds['button14']?>;
border-color:#d58512
}

<!-- warning ==================================== -->

.btn-danger{}
.btn-danger{
color:<?php echo $ds['button18']?>;
background-color:<?php echo $ds['button16']?>;
border-color:#d43f3a
}
.btn-danger.focus,.btn-danger:focus{
color:<?php echo $ds['button18']?>;
background-color:<?php echo $ds['button17']?>;
border-color:#761c19
}
.btn-danger:hover{
color:<?php echo $ds['button18']?>;
background-color:<?php echo $ds['button17']?>;
border-color:#ac2925
}
.btn-danger.active,.btn-danger:active,.open>.dropdown-toggle.btn-danger{
color:<?php echo $ds['button18']?>;
background-color:<?php echo $ds['button17']?>;
border-color:#ac2925
}