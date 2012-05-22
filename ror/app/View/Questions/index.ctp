<h2>Questions for <?php echo $game['Game']['title']; ?></h2>
<h3><a href="/games">&larr;Back To Games List</a></h3>
<a class="button" href="/questions/add/<?php echo $game['Game']['id']; ?>">Add Question</a>

<table id="sortable">
	<thead>
		<tr>
			<th>Question</th>
			<th>Status <?php echo $this->Form->input('status',array('div'=>array('style'=>'display:inline'),'label'=>false,'options'=>$status_options,'onchange'=>'change_status_filter(this.value);')); ?></th>
			<th>Drag to order</th>
			<th width="240px">&nbsp;</th>
		</tr>
	</thead>
	<tbody>
	<?php if(!empty($game['Question'])) { ?>
		<?php foreach($game['Question'] as $question) { ?>
			<tr id="question_<?php echo $question['id']; ?>">
				<td><?php echo $question['title']; ?></td>
				<td><?php echo $question['status']; ?></td>
				<td width="90" style="text-align:center;"><img width="11" height="11" src="/site-admin/img/icon_reorder.png" class="handleBar"></td>
				<td><a class="button" href="/games/play/<?php echo $game['Game']['id']; ?>/<?php echo $question['order']; ?>" target="_blank">Play Question</a>&nbsp;<a class="button" href="/questions/edit/<?php echo $question['id']; ?>">Edit</a>&nbsp;<a class="button" href="/questions/delete/<?php echo $question['id']; ?>" onclick="return confirm('Are you sure you want to delete this question?');">Delete</a></td>
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
	alert(value);
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