<h2>Versions for <?php echo $game['Game']['title']; ?></h2>
<h3><a href="/games/index/">&larr;Back To Game List</a></h3>
<table id="sortable">
	<thead>
		<tr>
			<th>#</th>
			<th>Created</th>
			<th>Question</th>
			<th>User</th>
			<th width="300px">&nbsp;</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach($versions as $key=>$version) { ?>
			<tr>
				<td><?php echo (count($versions) - $key); ?></td>
				<td><?php echo date('F jS, Y - g:ia',strtotime($version['QuestionVersion']['created'])); ?></td>
				<td><?php echo $version['QuestionVersion']['title']; if($version['QuestionVersion']['version'] == 1) { echo '(Created)'; }else{ echo '(Version '.$version['QuestionVersion']['version'].')'; } ?></td>
				<td><?php echo $version['User']['username']; ?></td>
				<td><a class="button" href="/games/play/<?php echo $game['Game']['id']; ?>/<?php echo strtotime($version['QuestionVersion']['created']); ?>" target="_blank">Play Snapshot</a></td>
			</tr>
		<?php } ?>
	</tbody>
</table>