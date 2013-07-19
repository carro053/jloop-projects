<h2>Users</h2>

<a href="/Users/create">Create</a>

<table>
	<thead>
		<tr>
			<th>Username</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach($users as $user) { ?>
			<tr>
				<td><?php echo $user['User']['username']; ?></td>
			</tr>
		<?php } ?>
	</tbody>
</table>