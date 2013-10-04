<!-- BEGIN: tpl -->


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>{TITLE}</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="Content-Language" content="ru" />
<meta http-equiv="Cache-Control" content="private" />
<meta name="description" content="{META_DESCRIPTION}" />
<meta http-equiv="description" content="{META_DESCRIPTION}" />
<meta name="Keywords" content="{META_KEYWORDS}" />
<meta http-equiv="Keywords" content="{META_KEYWORDS}" />
<meta name="Resource-type" content="document" />
<meta name="document-state" content="dynamic" />
<meta name="Robots" content="index,follow" />

<link rel="stylesheet" href="{URL}/themes/{THEME}/style.css" type="text/css" media="all" />
<link rel="stylesheet" href="{URL}/themes/{THEME}/jquery.fancybox-1.3.4.css" type="text/css" media="screen" />

<script type="text/javascript" src="/js/jquery-1.6.4.js"></script>
<script type="text/javascript" src="/js/jquery.mousewheel-3.0.4.pack.js"></script>
<script type="text/javascript" src="/js/jquery.fancybox-1.3.4.pack.js"></script>
<script type="text/javascript" src="/js/jquery.json-1.3.js"></script>
<script type="text/javascript" src="/js/kernel.class.js"></script>



</head>


<body>
<div id="top">&nbsp;</div>
<div id="wrapper">

	<!-- Header -->
	<div id="header">
		<h1><a href="/" title="Go to homepage">ELNYATOWN ENGEN</a></h1>
		<p class="title">Сайт города Ельни </p>

		<div id="header-in">
			<p>&hellip;общение объеденяет.</p>
		</div>

		<a href="#skip-menu" class="hidden">Skip menu</a>

		<!-- Menu -->
		<ul id="menu">
		<li><a href="http://forum.elnyatown.ru" target="_blank">Форум</a></li>
		<li><a href="http://elnyatown.ru" target="_blank">Чат</a></li>
		</ul>
			{mod_mainmenu}
			
		<!-- Menu end -->
	</div> <!-- Header end -->

	<div class="bar">&nbsp;</div>

<hr class="noscreen" />

<div id="skip-menu"></div>
<div id="mod_linelinks"> {mod_linelinks}</div>
	<div class="content">

		<!-- Left column -->
		<div class="column-left">
			<div class="column-left-in">
				{mod_content:var1=TEST1,var2=test2}

				{mod_lastnews}
			</div>
		</div> <!-- Left column end -->

		<hr class="noscreen" />

		<!-- Right column -->
		<div class="column-right" id="column-right">
			<div class="column-right-in">
				{mod_submenu}
			</div>
		</div> 
		<!-- Right column end -->
		<div class="cleaner">&nbsp;</div>
	</div> <!-- Content end -->

<hr class="noscreen" />

	<!-- Footer -->
	<div id="footer">
		<ul>
			<li><a href="#top">Top</a>&uarr;</li>
		</ul>

		<p class="cop">Copyright &copy; 2012 <a href="#">ElnyaTown Engen</a>. All Rights Reserved.</p>
	</div> <!-- Footer end -->

<hr class="noscreen" />

	{test_mod:var1=t1,var2=t2}
</div> <!-- Wrapper end -->
</body>

</html>



<!-- END: tpl -->
