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
		<div id="nav">
			<a href="/Leads/gather">Lead Gathering</a>
			<a href="/Leads/qualify">Lead Qualifying</a>
			<a href="/Leads">Leads</a>
		</div>
		<div id="content">

			<?php echo $this->Session->flash(); ?>

			<?php echo $this->fetch('content'); ?>
		</div>
		<div id="footer">
			
		</div>
	</div>
	<script type="text/javascript">
		function init() {
			$('input[type=submit], a, button').button();
			$('.menu').menu();
		}
		init();
	</script>
	<?php echo $this->element('sql_dump'); ?>
</body>
</html>
