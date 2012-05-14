<h2>Games</h2>
<a class="button" href="/games/add">Add Game</a>
<table>
	<thead>
		<tr>
			<th>Icon</th>
			<th>Title</th>
			<th>&nbsp;</th>
		</tr>
	</thead>
	<tbody>
	<?php if(!empty($games)) { ?>
		<?php foreach($games as $game) { ?>
			<tr>
				<td><img src="/img/game_icons/<?php if($game['Game']['has_icon']) { echo $game['Game']['id'].'.png'; }else{ echo 'default.png'; } ?>" /></td>
				<td><?php echo $game['Game']['title']; ?></td>
				<td><a class="button" href="/questions/index/<?php echo $game['Game']['id']; ?>">View Questions</a>&nbsp;<a class="button" href="/games/edit/<?php echo $game['Game']['id']; ?>">Edit</a>&nbsp;<a class="button" href="/games/delete/<?php echo $game['Game']['id']; ?>" onclick="return confirm('Are you sure you want to delete this game?');">Delete</a></td>
			</tr>
		<?php } ?>
	<?php } else { ?>
		<tr><td colspan="2">There are currently no games.</td></tr>
	<?php } ?>
	</tbody>
</table>