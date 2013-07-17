<?php
	echo $this->Form->create('Scrape');
	echo $this->Form->input('category');
	echo $this->Form->end('Submit');
?>

<table>
	<thead>
		<tr>
			<th>App</th>
			<th></th>
		</tr>
	</thead>
	<tbody>
		<?php foreach($scarpes as $scrape) { ?>
			<tr>
				<td><?php echo $scrape['Scrape']['name']; ?></td>
				<td><a class="dialog" href="/Scrapes/view/">Rate</a></td>
			</tr>
		<?php } if(empty($scarpes)) { ?>
			<tr><td colspan="4">There are currently no scrapes</td></tr>
		<?php } ?>
	</tbody>
</table>

<script type="text/javascript">
	$('.dialog').click(function(event) {
		var url = $(this).attr('href');
		$('<div/>')
			.dialog({
				modal: true,
				width: 'auto',
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
</script>