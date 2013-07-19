<table>
	<thead>
		<tr>
			<th>Name</th>
			<th>Type</th>
			<th>Rating</th>
			<th>Contact</th>
			<th>Created</th>
			<th>&nbsp;</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach($leads as $lead) { ?>
			<tr>
				<td>TODO!</td>
				<td><?php echo $lead['Lead']['model']; ?></td>
				<td><?php echo $lead['Lead']['rating']; ?></td>
				<td><?php echo '<a href="mailto:'.$lead['Lead']['email'].'">'.$lead['Lead']['email'].'</a> | '.$lead['Lead']['phone']; ?></td>
				<td><?php echo $lead['Lead']['created']; ?></td>
				<td><a class="dialog" href="/<?php echo Inflector::pluralize($lead['Lead']['model']); ?>/view/<?php echo $lead['Lead']['model_id']; ?>">View</a></td>
			</tr>
		<?php } if(empty($lead)) { ?>
			<tr><td colspan="5">There are currently no leads</td></tr>
		<?php } ?>
	</tbody>
</table>

<?php pr($leads); ?>