<h2>Games</h2>
<a class="button" href="/games/add">Add Game</a>
<table>
	<thead>
		<tr>
			<th>Icon</th>
			<th>Title</th>
			<th style="text-align:center;">Questions</th>
			<th style="text-align:center;">Version</th>
			<th>&nbsp;</th>
		</tr>
	</thead>
	<tbody>
	<?php if(!empty($games)) { ?>
		<?php foreach($games as $game) { ?>
			<tr>
				<td><img src="/img/game_icons/<?php if($game['Game']['has_icon']) { echo $game['Game']['id'].'.png'; }else{ echo 'default.png'; } ?>" width="41" /></td>
				<td><?php echo $game['Game']['title']; ?></td>
				<td style="text-align:center;"><?php echo count($game['Question']); ?></td>
				<td style="text-align:center;"><a href="/games/version_history/<?php echo $game['Game']['id']; ?>"><?php echo $game['Game']['version']; ?></a></td>
				<td><a class="button" href="/games/play/<?php echo $game['Game']['id']; ?>" target="_blank">Play</a>&nbsp;<a class="button" href="/questions/index/<?php echo $game['Game']['id']; ?>">Questions</a>&nbsp;<a class="button" href="/games/snapshots/<?php echo $game['Game']['id']; ?>">Snapshots</a>&nbsp;<a class="button" href="/games/edit/<?php echo $game['Game']['id']; ?>">Edit</a>&nbsp;<a class="button" href="/Games/export_to_csv/<?php echo $game['Game']['id']; ?>">Export to CSV</a>&nbsp;<a class="button" href="/games/delete/<?php echo $game['Game']['id']; ?>" onclick="return confirm('Are you sure you want to delete this game?');">Delete</a></td>
			</tr>
		<?php } ?>
	<?php } else { ?>
		<tr><td colspan="3">There are currently no games.</td></tr>
	<?php } ?>
	</tbody>
</table>