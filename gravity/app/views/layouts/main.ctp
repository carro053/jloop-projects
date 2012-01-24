<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="content-language" content="en" />
<meta name="description" content="" />
<meta name="keywords" content="" />
<title><?php echo $title_for_layout; ?></title>
<link rel="stylesheet" href="/forum/css/style.css" type="text/css" media="all" />
<link rel="stylesheet" href="/css/main.css" type="text/css" media="all" />
<script type="text/javascript" src="/forum/js/script.js"></script>
<script type="text/javascript" src="/js/jquery-1.6.2.min.js"></script>
</head>

<body>

<div id="container">
	<div id="header">
	<!--Planets for Todd-->
	<ul id="nav">
		<li><a href="/">Home</a></li>
		<li><a href="/forum">Forum</a></li>
		<li><a href="/videos">Videos</a></li>
		<li><a href="/screenshots">Screenshots</a></li>
		<li><a href="/magic/deck_index">Decks</a></li>
		<li><a href="/magic/game_index">Games</a></li>
		<?php if (!$this->Cupcake->user()) { ?>
		<li><a href="/forum/users/signup">Sign Up</a></li>
		<?php } ?>
	</ul>
	</div>
    <div id="main">
    	<?php if (!empty($this->_crumbs)) echo $this->element('navigation'); ?>
    	<?php echo $content_for_layout; ?>
    </div><!-- end #main -->
    <div style="clear:both;">&nbsp;</div>

</div><!-- end #container -->
</body>
</html>