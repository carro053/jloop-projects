<h3>Users</h3>
<table>
	<thead>
		<tr>
			<th>Username</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach($users as $user): ?>
			<tr>
				<td><?php echo $user['User']['username']; ?></td>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>