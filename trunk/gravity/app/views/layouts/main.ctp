<?php /*<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="content-language" content="en" />
<meta name="description" content="" />
<meta name="keywords" content="" />
<title><?php echo $title_for_layout; ?></title>
<link rel="stylesheet" href="/forum/css/style.css" type="text/css" media="all" />
<script type="text/javascript" src="/forum/js/script.js"></script>
<script type="text/javascript" src="/js/jquery-1.6.2.min.js"></script>
</head>

<body>

<div id="wrapper">
	<div id="header">
		<h1>Gravity</h1>
		<!--Planets for Todd-->
		<ul id="menu">
			<!--<li><a href="/">Home</a></li>-->
			<li><a href="/forum">Forum</a></li>
			<!--<li><a href="/videos">Videos</a></li>-->
			<!--<li><a href="/screenshots">Screenshots</a></li>-->
			<li><a href="/magic/deck_index">Decks</a></li>
			<li><a href="/magic/game_index">Games</a></li>
			<?php if (!$this->Cupcake->user()) { ?>
			<li><a href="/forum/users/signup">Sign Up</a></li>
			<?php } ?>
		</ul>
		<span class="clear"><!-- --></span>
	</div>
    <div id="content">
    	<?php if (!empty($this->_crumbs)) echo $this->element('navigation'); ?>
    	<?php echo $content_for_layout; ?>
    </div><!-- end #main -->
    <div style="clear:both;">&nbsp;</div>

</div><!-- end #wrapper -->
</body>
</html>
*/ ?>
<?php echo $this->Html->docType('xhtml-trans'); ?> 
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php echo $this->Html->charset(); ?>
<title>
	<?php echo $this->Cupcake->settings['site_name']; ?> - 
	<?php echo $title_for_layout; ?>
</title>

<?php // Scripts
echo $this->Html->css('/forum/css/style.css');
echo $this->Html->script('/forum/js/jquery-1.5.min.js');
echo $this->Html->script('/forum/js/script.js');

if ($this->params['controller'] == 'home') {
	echo $this->Html->meta(__d('forum', 'RSS Feed - Latest Topics', true), array('action' => 'feed', 'ext' => 'rss'), array('type' => 'rss'));
} else if (isset($feedId) && in_array($this->params['controller'], array('categories', 'topics'))) {
	echo $this->Html->meta(__d('forum', 'RSS Feed - Content Review', true), array('action' => 'feed', $feedId, 'ext' => 'rss'), array('type' => 'rss'));
}

echo $scripts_for_layout; ?>
</head>

<body>
<div id="wrapper">  
	<div id="header">
    	<h1><?php echo $this->Cupcake->settings['site_name']; ?></h1>
        
        <ul id="menu">
        	<li<?php if ($menuTab == 'home') echo ' class="active"'; ?>><a href="/forum/">Forum</a></li>
        	<li<?php if ($menuTab == 'search') echo ' class="active"'; ?>><a href="/forum/search/index">Search</a></li>
        	<?php /*<li<?php if ($menuTab == 'rules') echo ' class="active"'; ?>><?php echo $this->Html->link(__d('forum', 'Rules', true), array('controller' => 'home', 'action' => 'rules')); ?></li>
        	<li<?php if ($menuTab == 'help') echo ' class="active"'; ?>><?php echo $this->Html->link(__d('forum', 'Help', true), array('controller' => 'home', 'action' => 'help')); ?></li>*/ ?>
        	<li<?php if ($menuTab == 'users') echo ' class="active"'; ?>><a href="/forum/users/listing">Users</a></li>
        	<li<?php if ($menuTab == 'Magic Decks') echo ' class="active"'; ?>><a href="/magic/deck_index">Magic Decks</a></li>
        	<li<?php if ($menuTab == 'Magic Games') echo ' class="active"'; ?>><a href="/magic/game_index">Magic Games</a></li>
            <?php if ($this->Cupcake->user() && $this->Cupcake->hasAccess('admin')) { ?>
        	<li><a href="/admin/forum/home">Admin</a></li>
            <?php } ?>
        </ul>
        
        <span class="clear"><!-- --></span>
    </div>
    
    <div id="content">
    	<?php echo $this->element('navigation'); ?>
        
		<?php echo $content_for_layout; ?>
 	</div>
</div>    
</body>
</html>