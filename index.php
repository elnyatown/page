<?php
date_default_timezone_set( 'Europe/Moscow' );


include("kernel.class.php");
$test=new kernel();
$input["html"]=file_get_contents("global.template.tpl");
//$input["html"]="sdfghf  dfghdj {mod_mainmenu}  ghjfghjfg fghjfghj g  {mod_content:var1=TEST1,var2=TEST2} ehjhf ekjhf{mod_submenu:g1=D1,g2=D2} ekjrhf";
//var_dump($input["html"]);
$out=$test->getListModules($input);


print_r($out["arrayListModulesTpl"]);








//echo("test");
?>




<!DOCTYPE HTML PUBLIC  "-//W3C//DTD HTML 4.01//EN" "www.w3.org/TR/html4/strict.dtd">
<html>
  <head>
  <meta charset="utf-8">
    <title>Шаблон Bootstrap 101</title>
    <!-- Bootstrap -->
    <link href="bootstrap/css/bootstrap.css" rel="stylesheet">
	<link href="bootstrap/css/bootstrap-responsive.css">
	<script src="jq.js"></script>
    
	
	<script src="/bootstrap/js/bootstrap.js"></script>

  </head>
 <body>


 
<!--
  <div class="container">
<div class="row">
  <div class="navbar navbar-static-top ">
 <ul class="nav">
  <li class="active">
    <a href="#">Домой</a>
  </li>
  <li class="divider-vertical"></li>
  <li><a href="#">Link</a></li>
  <li class="divider-vertical"></li>
  <li><a href="#">Link</a></li>
</ul>
</div>

</div>
  
  
 <div class="row">
  <div class="span6">.xcvbx 88888 88888 8888 8888 cvb. kujhg k hlkhfdl hvk lkhwe kejh ljwerlkjehr jkerh lkjke hvkljehkljvh jhwer lkewhjk hlkjhwe v.</div>
  <div class="span4 offset2">.
  
  <form class="form-horizontal">
  <div class="control-group">
    <label class="control-label" for="inputEmail">Email</label>
    <div class="controls">
      <input type="text" id="inputEmail" placeholder="Email">
    </div>
  </div>
  <div class="control-group">
    <label class="control-label" for="inputPassword">Password</label>
    <div class="controls">
      <input type="password" id="inputPassword" placeholder="Password">
    </div>
  </div>
  <div class="control-group">
    <div class="controls">
      <label class="checkbox">
        <input type="checkbox"> Remember me
      </label>
      <button type="submit" class="btn">Sign in</button>
    </div>
  </div>
</form>
</div>
</div>
	
<div class="row">
  <div class="span12">
  
  <div class="dropdown">
<a class="dropdown-toggle" data-toggle="dropdown" data-target="#" href="#">Dropdown trigger</a>
  <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu">
  <li><a tabindex="-1" href="#">Действие</a></li>
  <li><a tabindex="-1" href="#">Другое действие</a></li>
  <li><a tabindex="-1" href="#"></a></li>
  <li class="divider"></li>
  <li><a tabindex="-1" href="#">Отделенный пункт</a></li>
</ul>
</div>
  </div>
  
  


  
</div>
	
	
	
	</div>
-->
	</body>
<html>
