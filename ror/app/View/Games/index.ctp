<h2>Games</h2>
<a class="button" href="/games/next_question_ready">Start Game</a>
<a class="button" href="/games/add">Add Game</a>
<table>
	<thead>
		<tr>
			<th>Title</th>
			<th>Status</th>
			<th>&nbsp;</th>
		</tr>
	</thead>
	<tbody>
	<?php if(!empty($games)) { ?>
		<?php foreach($games as $game) { ?>
			<tr>
				<td><?php echo $game['Game']['title']; ?></td>
				<td><?php if($game['Game']['active']) echo '<strong>Active</strong>'; else echo '&nbsp;'; ?></td>
				<td><a class="button" href="/games/activate/<?php echo $game['Game']['id']; ?>">Activate</a>&nbsp;<a class="button" href="/questions/question_list/<?php echo $game['Game']['id']; ?>">View Questions</a><?php if($game['Game']['game_ended'] == 1) { ?>&nbsp;<a class="button" href="/games/game_review/<?php echo $game['Game']['id']; ?>">Review Results</a><?php } ?>&nbsp;<a class="button" href="/games/edit/<?php echo $game['Game']['id']; ?>">Edit</a>&nbsp;<a class="button" href="/games/delete/<?php echo $game['Game']['id']; ?>" onclick="return confirm('Are you sure you want to delete this game?');">Delete</a></td>
			</tr>
		<?php } ?>
	<?php } else { ?>
		<tr><td colspan="3">There are currently no games.</td></tr>
	<?php } ?>
	</tbody>
</table>