<h2>Tags</h2>

<a href="/Tags/create">Create</a>

<table>
	<thead>
		<tr>
			<th>Name</th>
			<th></th>
		</tr>
	</thead>
	<tbody>
		<?php foreach($tags as $tag) { ?>
			<tr>
				<td><?php echo $tag['Tag']['name']; ?></td>
				<td><a href="/Tags/update/<?php echo $tag['Tag']['id']; ?>">Edit</a></td>
			</tr>
		<?php } ?>
	</tbody>
</table>