<!DOCTYPE html>
<html>
<head>
	<?php echo $this->Html->charset(); ?>
	<title>
		<?php echo $title_for_layout; ?>
	</title>
	
	<?php
		echo $this->Html->meta('icon');

		echo $this->Html->css('cake.generic');
		echo $this->Html->css('start/jquery-ui-1.10.3.custom.min');
		
		echo $this->Html->script('jquery-1.10.2.min.js');
		echo $this->Html->script('jquery-ui-1.10.3.custom.min.js');
		
		echo $this->fetch('meta');
		echo $this->fetch('css');
		echo $this->fetch('script');
	?>
</head>
<body>
	<div id="container">
		<div id="header">
			<h1>Leads</h1>
		</div>
		<ul id="menu">
			<li><a href="#">Item 1</a></li>
			<li><a href="#">Item 2</a></li>
			<li><a href="#">Item 3</a>
			<ul>
				<li><a href="#">Item 3-1</a></li>
				<li><a href="#">Item 3-2</a></li>
				<li><a href="#">Item 3-3</a></li>
				<li><a href="#">Item 3-4</a></li>
				<li><a href="#">Item 3-5</a></li>
			</ul>
			</li>
			<li><a href="#">Item 4</a></li>
			<li><a href="#">Item 5</a></li>
		</ul>
		<div id="content">

			<?php echo $this->Session->flash(); ?>

			<?php echo $this->fetch('content'); ?>
		</div>
		<div id="footer">
			
		</div>
	</div>
	<script type="text/javascript">
		$("#menu").menu();
	</script>
	<?php echo $this->element('sql_dump'); ?>
</body>
</html>
