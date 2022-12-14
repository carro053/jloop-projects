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
		echo $this->Html->css('jqueryui-editable');
		
		echo $this->Html->script('jquery-1.10.2.min.js');
		echo $this->Html->script('jquery-ui-1.10.3.custom.min.js');
		echo $this->Html->script('jqueryui-editable.min.js');
		
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
		<?php if(!empty($authUser)) { ?>
			<div id="nav">
				<a href="/Leads/gather">Lead Gathering</a>
				<a href="/Leads/qualify">Lead Qualifying</a>
				<a href="/Leads">Leads</a>
				<a href="/Groups">Lead Groups</a>
				<a href="/Contacts">Contacts</a>
				<div class="right">
					<span>Logged in as <?php echo $authUser['username']; ?></span>
					<a href="/Users">Users</a>
					<a href="/Tags">Tags</a>
					<a href="/Users/logout">Logout</a>
				</div>
			</div>
		<?php } ?>
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
			
			$('.dialog').unbind('click');
			$('.dialog').click(function(event) {
				var url = $(this).attr('href');
				$('<div/>')
					.dialog({
						modal: true,
						width: '100%',
						draggable: false,
						autoOpen: false,
						close: function(event, ui) {
							$(this).remove();
						}
					})
					.load(url, function() {
						$(this).dialog('open');
					});
				return false;
			});
		}
		init();
		
		function exportToHighrise(lead_id) {
			var time = new Date().getTime();
			$.ajax({
				url: "/Leads/ajaxExportToHighrise/"+lead_id+"?t="+time,
				success: function(data){
					if(!isNaN(data)) {
						$('.highrise-export-'+lead_id).replaceWith('<a href="https://jloop.highrisehq.com/companies/'+data+'" target="_blank" class="highrise-export-'+lead_id+'">View in Highrise</a>');
						$('.highrise-export-'+lead_id).button();
					}
				},
				error: function(){
					alert('There was an error with AJAX.');
				}
			});
		}
		
		function exportPersonToHighrise(contact_id) {
			if(confirm('Exporting this contact will copy all of their information into Highrise and create a company if none exists. Are you sure you want to export? Because one time, Daniel hit this button and he didn\'t really want to export, so now this message has to be here forever.')) {
				var time = new Date().getTime();
				$.ajax({
					url: "/Leads/ajaxExportPersonToHighrise/"+contact_id+"?t="+time,
					success: function(data){
						if(!isNaN(data)) {
							console.log('contact exported');
							$('#export_person-'+contact_id).replaceWith('<a href="https://jloop.highrisehq.com/people/'+data+'" target="_blank" id="export_person-'+contact_id+'">View in Highrise</a>');
							$('#export_person-'+contact_id).button();
						}
					},
					error: function(){
						alert('There was an error with AJAX.');
					}
				});
			}
		}
	</script>
	<?php echo $this->element('sql_dump'); ?>
	
	<input id="chrome-extension-info" type="hidden" value="0" />
</body>
</html>