<?php
	echo $this->Form->create('Scrape');
	echo $this->Form->input('category');
	echo $this->Form->end('Submit');
?>

<table>
	<thead>
		<tr>
			<th>App</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach($scarpes as $scrape) { ?>
			<tr>
				<td><?php echo $scrape['Scrape']['name']; ?></td>
			</tr>
		<?php } if(empty($scarpes)) { ?>
			<tr><td colspan="4">There are currently no scrapes</td></tr>
		<?php } ?>
	</tbody>
</table>