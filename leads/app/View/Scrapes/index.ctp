<?php
	echo $this->Form->create('Scrape', array('type' => 'get'));
	echo $this->Form->input('category');
	echo $this->Form->end('Filter');
?>

<table>
	<thead>
		<tr>
			<th>App</th>
			<th>Category</th>
			<th>Price</th>
			<th>Updated</th>
			<th></th>
		</tr>
	</thead>
	<tbody>
		<?php foreach($scrapes as $scrape) { ?>
			<tr>
				<td><?php echo $scrape['Scrape']['name']; ?></td>
				<td><?php echo $scrape['Scrape']['category']; ?></td>
				<td><?php echo $scrape['Scrape']['price']; ?></td>
				<td><?php echo $scrape['Scrape']['updated']; ?></td>
				<td><a class="dialog" href="/Scrapes/view/<?php echo $scrape['Scrape']['id']; ?>">Rate</a></td>
			</tr>
		<?php } if(empty($scrape)) { ?>
			<tr><td colspan="5">There are currently no scrapes</td></tr>
		<?php } ?>
	</tbody>
</table>

<?php echo $this->element('pager', array('totalItems' => $count, 'uri' => 'Scrapes/index')); ?>