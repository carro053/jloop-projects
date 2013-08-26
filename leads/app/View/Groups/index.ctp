<div id="accordion">
	<?php foreach($groups as $group) { ?>
		<h3><?php echo $group['Group']['name']; ?></h3>
		<div>
			<table>
				<thead>
					<tr>
						<th>Lead</th>
						<th>Company</th>
						<th>Contacts</th>
						<th>&nbsp;</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach($group['Lead'] as $lead) { ?>
						<tr id="groups_lead_id_<?php echo $lead['GroupsLead']['id']; ?>">
							<td><?php echo $lead['name']; ?></td>
							<td><?php echo $lead['company']; ?></td>
							<td>
								<table>
									<tr>
										<td><strong>Company</strong></td>
										<td>
											<?php echo $lead['address'].'<br />'.$lead['city'].' '.$lead['state'].' '.$lead['zip'].' '.$lead['country'].'<br /><br />'.$lead['email'].'<br /><br />'.$lead['phone']; ?>
										</td>
									</tr>
									<?php foreach($lead['Contact'] as $contact) { ?>
										<tr>
											<td><strong><?php echo $contact['first_name'].' '.$contact['last_name']; ?></strong><br /><?php echo $contact['title']; ?></td>
											<td>
												<?php echo $contact['address'].'<br />'.$contact['city'].' '.$contact['state'].' '.$contact['zip'].' '.$contact['country'].'<br /><br />'.$contact['email'].'<br /><br />'.$contact['phone']; ?>
											</td>
										</tr>
									<?php } ?>
								</table>
							</td>
							<td><a class="dialog" href="/<?php echo Inflector::pluralize($lead['model']); ?>/view/<?php echo $lead['model_id']; ?>">View</a>&nbsp;<a href="#" onclick="removeLead(<?php echo $lead['GroupsLead']['id']; ?>);">Delete From Group</a></td>
						</tr>
					<?php } if(empty($group['Lead'])) { ?>
						<tr><td colspan="4">There are currently no leads in this group. Bummer.</td></tr>
					<?php } ?>
				</tbody>
			</table>
			
			<a onclick="return confirm('Are you sure you want to delete this entire group? You\'re crazy.');" href="/Groups/delete/<?php echo $group['Group']['id']; ?>">Delete Group</a>
		</div>
	<?php } ?>
</div>

<script>
	$(function() {
		$( "#accordion" ).accordion({
			active: false,
			collapsible: true,
			heightStyle: "content"
		});
	});
	
	function removeLead(groups_lead_id) {
		if(confirm('Are you sure you want to delete this lead from this group?')) {
			var time = new Date().getTime();
			$.ajax({
				url: "/Groups/removeLead/"+groups_lead_id+"?t="+time,
				success: function(data){
					if(data == "1")
						$('#groups_lead_id_'+groups_lead_id).remove();
				},
				error: function(){
					alert('There was an error with AJAX.');
				}
			});
		}
		return false;
	}
</script>