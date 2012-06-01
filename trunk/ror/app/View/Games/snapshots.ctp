<h2>Snapshots for <?php echo $game['Game']['title']; ?></h2>
<h3><a href="/games">&larr;Back To Games List</a></h3>
<a class="button" href="/games/add_snapshot/<?php echo $game['Game']['id']; ?>">Create Snapshot of Current Version</a>

<table id="sortable">
	<thead>
		<tr>
			<th width="60px">Version</th>
			<th>Note</th>
			<th width="300px">&nbsp;</th>
		</tr>
	</thead>
	<tbody>
	<?php if(!empty($game['GameSnapshot'])) { ?>
		<?php foreach($game['GameSnapshot'] as $snapshot) { ?>
			<tr>
				<td width="60px"><?php echo $snapshot['version']; ?></td>
				<td><?php echo $snapshot['note']; ?></td>
				<td><?php if($snapshot['published'] == 1) { ?><a class="button" href="/games/play_version/<?php echo $game['Game']['id']; ?>/<?php echo $snapshot['id']; ?>" target="_blank">Play</a>&nbsp;<a class="button" href="/games/unpublish_snapshot/<?php echo $game['Game']['id']; ?>/<?php echo $snapshot['id']; ?>">Unpublish</a><?php }else{ ?><a class="button" href="/games/publish_snapshot/<?php echo $game['Game']['id']; ?>/<?php echo $snapshot['id']; ?>">Publish</a><?php } ?>&nbsp;<a class="button" href="/games/edit_snapshot/<?php echo $game['Game']['id']; ?>/<?php echo $snapshot['id']; ?>">Edit</a>&nbsp;<a class="button" href="/games/delete_snapshot/<?php echo $game['Game']['id']; ?>/<?php echo $snapshot['id']; ?>" onclick="return confirm('Are you sure you want to delete this snapshot?');">Delete</a></td>
			</tr>
		<?php } ?>
	<?php } else { ?>
		<tr><td colspan="3">There are currently no snapshots for this game.</td></tr>
	<?php } ?>
	</tbody>
</table>
