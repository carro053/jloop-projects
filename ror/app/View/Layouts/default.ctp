<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Admin: <?php echo $title_for_layout; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
<!-- Include external files and scripts here (See HTML helper for more info.) -->
<link rel="stylesheet" href="/site-admin/css/framework/screen.css" type="text/css" media="screen, projection" />
<link rel="stylesheet" href="/site-admin/css/framework/print.css" type="text/css" media="print" /> 
<!--[if lte IE 7]>
  <link rel="stylesheet" href="/site-admin/css/framework/ie.css" type="text/css" media="screen, projection" />
<![endif]-->
<link rel="stylesheet" href="/site-admin/css/framework_override.css" type="text/css" media="screen, projection" />

<script type="text/javascript" src="/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="/js/jquery-ui-1.8.19.custom.min.js"></script>

</head>
<body>

<!-- If you'd like some sort of menu to 
show up on all of your views, include it here -->
<div id="header">
	<div class="container"> 
		<?php if($session->read('Auth.User.id') > 0) { ?>
			<p class="tools-nav"><a href="/users/logout">Log Out</a>
    	<?php } ?>
    	<h1><a href="/">JLOOP Admin</a></h1>
	</div><!-- end .container -->
</div><!-- end #header -->

<div id="navbar"> 
	<div class="container">
		<ul>
			<?php if($session->read('Auth.User.id') > 0) { ?>
			<li><a href="/games">Games</a></li>
			<?php } ?>
		</ul>
	</div><!-- end .container -->		
</div><!-- end #navbar -->

<!-- Here's where I want my views to be displayed -->

<div id="content"> 
	<div class="container">
		<?php if(isset($breadcrumbs)) {
			echo '<p class="breadcrumbs">';
			foreach($breadcrumbs as $key=>$crumb):
				if($key != 0) echo ' &raquo; ';
				if($crumb['url'] == '') {
					echo $crumb['title'];
				}else{
					echo '<a href="'.$crumb['url'].'">'.$crumb['title'].'</a>';
				}
			endforeach;
			echo '</p>';
		}?>
		<?php
			echo $this->Session->flash();
			echo $this->Session->flash('auth');
			echo $content_for_layout;
		?>	
	</div><!-- end .container --> 
</div><!-- end #content -->

<!-- Add a footer to each displayed page -->
<div id="footer">
	<div class="container">
		<p>&copy;<?php echo date("Y"); ?> JLOOP<br />All rights reserved.</p>
	</div><!-- end .container -->
</div><!-- end #footer --> 
<?php echo $this->element('sql_dump'); ?>
</body>
</html>