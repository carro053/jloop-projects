<h2>Versions for <?php echo $question['Question']['title']; ?></h2>
<h3><a href="/questions/index/<?php echo $question['Question']['game_id']; ?>">&larr;Back To Question List</a></h3>
<table id="sortable">
	<thead>
		<tr>
			<th>#</th>
			<th>Created</th>
			<th>Status</th>
			<th>User</th>
			<th width="300px">&nbsp;</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach($question['QuestionVersion'] as $key=>$version) { ?>
			<tr>
				<td><?php echo ($key + 1); ?></td>
				<td><?php echo date('F jS, Y - g:ia',strtotime($version['created'])); ?></td>
				<td><?php echo $version['status']; ?></td>
				<td><?php echo $version['User']['username']; ?></td>
				<td><a class="button" href="/games/play_question/<?php echo $question['Question']['game_id']; ?>/<?php echo $question['Question']['id']; ?>/<?php echo $version['id']; ?>" target="_blank">Play</a>&nbsp;<a class="button" href="/games/preview_question/<?php echo $question['Question']['game_id']; ?>/<?php echo $question['Question']['id']; ?>/<?php echo $version['id']; ?>" target="_blank">Preview</a>
				<?php if($key + 1 == count($question['QuestionVersion'])) { ?>
				&nbsp;<a class="button" href="#">Current Version</a>
				<?php }else{ ?>
				&nbsp;<a class="button" href="/questions/set_to_this_version/<?php echo $question['Question']['id']; ?>/<?php echo $version['id']; ?>" onclick="return confirm('This will make this version the current version for this question, okay?');">Make Current</a></td>
				<?php } ?>
			</tr>
		<?php } ?>
	</tbody>
</table>