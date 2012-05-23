<h2>Users</h2>
<a class="button" href="/users/add/">Add User</a>
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