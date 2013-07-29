<div id="accordion">
	<?php foreach($groups as $group) { ?>
		<h3><?php echo $group['Group']['name']; ?></h3>
		<div>
			<table>
				<thead>
					<tr>
						<td>Name</td>
						<td>Type</td>
						<td>Rating</td>
						<td>Email</td>
						<td>Phone</td>
						<td>&nbsp;</td>
					</tr>
				</thead>
				<tbody>
					<?php foreach($group['Lead'] as $lead) { ?>
						<tr>
							<td><?php echo $lead['name']; ?></td>
							<td><?php echo $lead['type']; ?></td>
							<td><?php echo $lead['rating']; ?></td>
							<td><?php echo $lead['email']; ?></td>
							<td><?php echo $lead['phone']; ?></td>
							<td><a class="dialog" href="/<?php echo Inflector::pluralize($lead['model']); ?>/view/<?php echo $lead['model_id']; ?>">View</a>&nbsp;<a href="#" onclick="">Delete From Group</a></td>
						</tr>
					<?php } if(empty($group['Lead'])) { ?>
						<tr><td colspan="6">There are currently no leads in this group. Bummer.</td></tr>
					<?php } ?>
				</tbody>
			</table>
		</div>
	<?php } ?>
</div>

<?php pr($groups); ///Groups/removeLead ?>

<script>
	$(function() {
		$( "#accordion" ).accordion({
			active: false,
			collapsible: true,
			heightStyle: "content"
		});
	});
	
	function removeLead(group_lead_id, tr) {
		
	}
</script>