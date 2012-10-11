<h2>Learn More Items</h2>
<a class="button" href="/learn_more_items/add/">Add Learn More Item</a>
<table id="sortable">
	<thead>
		<tr>
			<th>Item</th>
			<th width="350px">&nbsp;</th>
		</tr>
	</thead>
	<tbody>
	<?php if(!empty($items)) { ?>
		<?php foreach($items as $item) { ?>
			<tr>
				<td><a href="<?php echo $item['LearnMoreItem']['url']; ?>" target="_blank"><?php echo $item['LearnMoreItem']['label']; ?></a></td>
				<td><a class="button" href="/learn_more_items/edit/<?php echo $learn_more_item['id']; ?>">Edit</a>&nbsp;<a class="button" href="/learn_more_items/delete/<?php echo $learn_more_item['id']; ?>" onclick="return confirm('Are you sure you want to delete this learn_more_item?');">Delete</a></td>
			</tr>
		<?php } ?>
	<?php } else { ?>
		<tr><td colspan="2">There are currently no learn more items.</td></tr>
	<?php } ?>
	</tbody>
</table> 