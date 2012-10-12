<h2>Deleted Learn More Items</h2>
<a class="button" href="/learn_more_items/index/">Active Learn More Items</a>
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
				<td><a class="button" href="/learn_more_items/undelete/<?php echo $item['LearnMoreItem']['id']; ?>" onclick="return confirm('Are you sure you want to undelete this learn more item?');">Undelete</a></td>
			</tr>
		<?php } ?>
	<?php } else { ?>
		<tr><td colspan="2">There are currently no deleted learn more items.</td></tr>
	<?php } ?>
	</tbody>
</table> 