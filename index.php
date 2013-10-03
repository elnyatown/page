<?php
date_default_timezone_set( 'Europe/Moscow' );


include("kernel.class.php");
$test=new kernel();
$input["html"]=file_get_contents("global.template.tpl");
//$input["html"]="sdfghf  dfghdj {mod_mainmenu}  ghjfghjfg fghjfghj g  {mod_content:var1=TEST1,var2=TEST2} ehjhf ekjhf{mod_submenu:g1=D1,g2=D2} ekjrhf";
//var_dump($input["html"]);
$out=$test->getListModules($input);


//print_r($out["arrayListModulesTpl"]);








//echo("test");
?>




<!DOCTYPE HTML PUBLIC  "-//W3C//DTD HTML 4.01//EN" "www.w3.org/TR/html4/strict.dtd">
<!DOCTYPE html>
<html>
  <head>
    <title>Bootstrap 101 Template</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap 
    <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.0-wip/css/bootstrap.min.css">-->
	
	<link rel="stylesheet" href="bootstrap/css/bootstrap.css">
	    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="jquery.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed 
    <script src="//netdna.bootstrapcdn.com/bootstrap/3.0.0-wip/js/bootstrap.min.js"></script>-->
<script src="bootstrap/js/bootstrap.js"></script>
    <!-- Enable responsive features in IE8 with Respond.js (https://github.com/scottjehl/Respond) 
    <script src="bootstrap/js/respond.js"></script>-->
	
  </head>
  <body>
  <div class="container">
  <div class="row">
  <ul class="nav nav-pills">
  <li class="active"><a href="#">Home</a></li>
  <li><a href="#">Profile</a></li>
  <li><a href="#">Messages</a></li>
</ul>
  </div>
  
  <div class="row">
  <div class="col-md-9">10</div>
  <div class="col-md-3">2
  <ul class="nav nav-pills nav-stacked">
	<li class="active"><a href="#">Home</a></li>
	<li><a href="#">Profile</a></li>
	<li><a href="#">Messages</a></li>
	<li class="dropdown">
		<a class="dropdown-toggle" data-toggle="dropdown" href="#">
			Dropdown <span class="caret"></span>
		</a>
		<ul class="dropdown-menu">
			<li><a href="#">Profile</a></li>
			<li><a href="#">Messages ert  ert er t 3q4t qretertewrt wert wer tewr t</a></li>
		</ul>
  </li>
  
  
  
  
</ul>
  </div>
</div>


</div>
  </body>
</html>
