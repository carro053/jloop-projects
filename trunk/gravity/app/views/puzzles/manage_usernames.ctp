<h2>Usernames Needing Review</h2>
<table>
	<thead>
		<tr>
			<th>New Username</th>
			<th>Previous Username</th>
			<th>Account Made</th>
			<th></th>
		</tr>
	</thead>
	<tbody>
		<?php foreach($accounts as $account): ?>
			<tr id="account_<?php echo $account['Account']['id']; ?>">
				<td><?php echo $account['Account']['temp_username']; ?></td>
				<td><?php echo $account['Account']['username']; ?></td>
				<td><?php echo date('F jS, Y',strtotime($account['Account']['created'])); ?></td>
				<td><a href="" onclick="approveUsername(<?php echo $account['Account']['id']; ?>); return false;">Approve</a></td>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>

<script type="text/javascript">
	
	function approveUsername(account_id)
	{
		$('#account_'+account_id).remove();
		$.ajax({
			type: "POST",
			url: "/puzzles/approve_username/"+account_id,
			success: function(response)
			{
				
			}
		});
	}
</script>