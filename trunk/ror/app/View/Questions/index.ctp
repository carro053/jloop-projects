<h2><?php echo count($game['Question']); ?> Question<?php if(count($game['Question']) != 1) echo 's'; ?> for <?php echo $game['Game']['title']; ?></h2>
<h3><a href="/games">&larr;Back To Games List</a></h3>
<a class="button" href="/questions/add/<?php echo $game['Game']['id']; ?>">Add Question</a>
<a class="button" href="/games/export/<?php echo $game['Game']['id']; ?>">Export Game</a>
<a class="button" href="/games/add_snapshot/<?php echo $game['Game']['id']; ?>">Create Snapshot of Current Version</a>
<a class="button" href="/questions/deleted/<?php echo $game['Game']['id']; ?>">Deleted Questions</a>


<table id="sortable">
	<thead>
		<tr>
			<th>Question</th>
			<?php array_unshift($status_options,array(''=>'')); ?>
			<th>Status <?php echo $this->Form->input('status',array('div'=>array('style'=>'display:inline'),'label'=>false,'options'=>$status_options,'onchange'=>'change_status_filter(this.value);','value'=>$status_filter)); ?></th>
			<th style="text-align:center;">
				<?php
				if($status_filter)
					echo '<a class="button" href="/questions/index/'.$game['Game']['id'].'">Enable Drag Order</a>';
				else
					echo 'Drag to Order';
				?>
			</th>
			<th style="text-align:center;">Version</th>
			<th width="300px">&nbsp;</th>
		</tr>
	</thead>
	<tbody>
	<?php if(!empty($game['Question'])) { ?>
		<?php foreach($game['Question'] as $question) { ?>
			<tr id="question_<?php echo $question['id']; ?>" <?php if($question['status'] == $status_filter && $status_filter) echo 'class="success"'; ?>>
				<td><?php echo $question['title']; ?></td>
				<td><?php echo $question['status']; ?></td>
				<td width="150" style="text-align:center;"><?php if(!$status_filter) echo '<img width="11" height="11" src="/site-admin/img/icon_reorder.png" class="handleBar">'; ?></td>
				<td style="text-align:center;">
					<?php if($question['version'] > 1) { ?>
					<a href="/questions/version_history/<?php echo $question['id']; ?>"><?php echo $question['version']; ?></a>
					<?php }else{ ?>
					1					
					<?php } ?>
				</td>
				<td><a class="button" href="/games/play_question/<?php echo $game['Game']['id']; ?>/<?php echo $question['id']; ?>" target="_blank">Play</a>&nbsp;<a class="button" href="/games/preview_question/<?php echo $game['Game']['id']; ?>/<?php echo $question['id']; ?>" target="_blank">Preview</a>&nbsp;<a class="button" href="/questions/edit/<?php echo $question['id']; ?>">Edit</a>&nbsp;<a class="button" href="/questions/delete/<?php echo $question['id']; ?>" onclick="return confirm('Are you sure you want to delete this question?');">Delete</a></td>
			</tr>
		<?php } ?>
	<?php } else { ?>
		<tr><td colspan="3">There are currently no questions for this game.</td></tr>
	<?php } ?>
	</tbody>
</table>

<script type="text/javascript">
function change_status_filter(value)
{
	window.location = "/questions/index/<?php echo $game['Game']['id']; ?>/"+value;
}
$(function(){
	$("#sortable tbody").sortable({
		handle : '.handleBar',
		tolerance: 'pointer',
		update : function () {
			serial = $('#sortable tbody').sortable('serialize');
			$.ajax({
				url: "/questions/set_order",
				type: "post",
				data: serial,
				error: function(){
					alert("theres an error with AJAX");
				}
			});
		}
	});
	$("#sortable tbody").disableSelection();
});
</script>