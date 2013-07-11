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
		<nav>
			<a href="">Pre-Leads</a>
			<a href="">Leads</a>
			<ul class="menu">
			  <li><a href="#"><span class="ui-icon ui-icon-disk"></span>Save</a></li>
			  <li><a href="#"><span class="ui-icon ui-icon-zoomin"></span>Zoom In</a></li>
			  <li><a href="#"><span class="ui-icon ui-icon-zoomout"></span>Zoom Out</a></li>
			  <li class="ui-state-disabled"><a href="#"><span class="ui-icon ui-icon-print"></span>Print...</a></li>
			  <li>
			    <a href="#">Playback</a>
			    <ul>
			      <li><a href="#"><span class="ui-icon ui-icon-seek-start"></span>Prev</a></li>
			      <li><a href="#"><span class="ui-icon ui-icon-stop"></span>Stop</a></li>
			      <li><a href="#"><span class="ui-icon ui-icon-play"></span>Play</a></li>
			      <li><a href="#"><span class="ui-icon ui-icon-seek-end"></span>Next</a></li>
			    </ul>
			  </li>
			</ul>
		</nav>
		<div id="content">

			<?php echo $this->Session->flash(); ?>

			<?php echo $this->fetch('content'); ?>
		</div>
		<div id="footer">
			
		</div>
	</div>
	<script type="text/javascript">
		$('a').button();
		$('.menu').menu();
	</script>
	<?php echo $this->element('sql_dump'); ?>
</body>
</html>
