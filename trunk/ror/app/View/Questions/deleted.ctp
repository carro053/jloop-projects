<h2>Questions for <?php echo $game['Game']['title']; ?></h2>
<h3><a href="/games">&larr;Back To Games List</a></h3>
<a class="button" href="/questions/index/<?php echo $game['Game']['id']; ?>">Active Question</a>

<table id="sortable">
	<thead>
		<tr>
			<th>Question</th>
			<th width="300px">&nbsp;</th>
		</tr>
	</thead>
	<tbody>
	<?php if(!empty($game['Question'])) { ?>
		<?php foreach($game['Question'] as $question) { ?>
			<tr>
				<td><?php echo $question['title']; ?></td>
				<td><a class="button" href="/games/preview_question/<?php echo $game['Game']['id']; ?>/<?php echo $question['id']; ?>" target="_blank">Preview</a>&nbsp;<a class="button" href="/questions/undelete/<?php echo $question['id']; ?>" onclick="return confirm('Are you sure you want to undelete this question?');">Undelete</a></td>
			</tr>
		<?php } ?>
	<?php } else { ?>
		<tr><td colspan="2">There are currently no deleted questions for this game.</td></tr>
	<?php } ?>
	</tbody>
</table>