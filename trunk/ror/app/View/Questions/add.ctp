<h3><a href="/questions/index/<?php echo $game_id; ?>">&larr;Back To Question List</a></h3>
<table>
	<thead>
	</thead>
	<tbody>
		<?php echo $this->Form->create('Question',array('url'=>'/questions/add/'.$game_id,'type' => 'file')); ?>
		<tr>
			<td><label>Title</label></td>
			<td><?php echo $this->Form->input('title',array('label'=>false)); ?></td>
			<td><?php ?></td>
		</tr>
		<tr>
			<td><label>Time</label></td>
			<td><?php echo $this->Form->input('time',array('label'=>false,'value'=>'Today, 12:00pm')); ?></td>
			<td><?php ?></td>
		</tr>
		<tr>
			<td><label>Clue Type</label></td>
			<td><?php echo $this->Form->input('clue_type',array('label'=>false,'options'=>array('text'=>'Text','image'=>'Image'),'onchange'=>'change_type(this);')); ?></td>
			<td><?php ?></td>
		</tr>
		<tr>
			<td><label>Clue Text</label></td>
			<td><?php echo $this->Form->input('clue_text',array('label'=>false)); ?></td>
			<td><?php ?></td>
		</tr>
		<tr>
			<td><label>Clue Image</label></td>
			<td><?php echo $this->Form->input('clue_image',array('label'=>false,'type'=>'file')); ?></td>
			<td><?php ?></td>
		</tr>
		<tr>
			<td><label>Question Type</label></td>
			<td><?php echo $this->Form->input('question_type',array('label'=>false,'options'=>array('text'=>'Text','image'=>'Image'),'onchange'=>'change_type(this);'));; ?></td>
			<td><?php ?></td>
		</tr>
		<tr>
			<td><label>Question Text</label></td>
			<td><?php echo $this->Form->input('question_text',array('label'=>false)); ?></td>
			<td><?php ?></td>
		</tr>
		<tr>
			<td><label>Question Image</label></td>
			<td><?php echo $this->Form->input('question_image',array('label'=>false,'type'=>'file')); ?></td>
			<td><?php ?></td>
		</tr>
		<tr>
			<td><label>Insight Type</label></td>
			<td><?php echo $this->Form->input('insight_type',array('label'=>false,'options'=>array('text'=>'Text','image'=>'Image'),'onchange'=>'change_type(this);'));; ?></td>
			<td><?php ?></td>
		</tr>
		<tr>
			<td><label>Insight Text</label></td>
			<td><?php echo $this->Form->input('insight_text',array('label'=>false)); ?></td>
			<td><?php ?></td>
		</tr>
		<tr>
			<td><label>Insight Image</label></td>
			<td><?php echo $this->Form->input('insight_image',array('label'=>false,'type'=>'file')); ?></td>
			<td><?php ?></td>
		</tr>
		<tr>
			<td><label>Answer Type</label></td>
			<td><?php echo $this->Form->input('answer_type',array('label'=>false,'options'=>array('text'=>'Text','image'=>'Image'),'onchange'=>'change_type(this);')); ?></td>
			<td><?php ?></td>
		</tr>
		<tr>
			<td><label>Answer 1 Text</label></td>
			<td><?php echo $this->Form->input('answer_1_text',array('label'=>false)); ?></td>
			<td><?php ?></td>
		</tr>
		<tr>
			<td><label>Answer 1 Image</label></td>
			<td><?php echo $this->Form->input('answer_1_image',array('label'=>false,'type'=>'file')); ?></td>
			<td><?php ?></td>
		</tr>
	</tbody>
</table>
<?php
    
    
    
    echo $this->Form->input('answer_2_text');
    echo $this->Form->input('answer_2_image',array('type'=>'file'));
    echo $this->Form->input('answer_3_text');
    echo $this->Form->input('answer_3_image',array('type'=>'file'));
    echo $this->Form->input('answer_4_text');
    echo $this->Form->input('answer_4_image',array('type'=>'file'));
    echo $this->Form->input('correct_answer',array('options'=>array(0=>1,1=>2,2=>3,3=>4)));
    echo $this->Form->input('has_prize');
    echo $this->Form->input('prize_image',array('type'=>'file','label'=>'Prize Image<br />(241×132)'));
    echo $this->Form->input('prize_text');
    echo $this->Form->end('Submit');
?>
<script type="text/javascript">
	function change_type(item)
	{
		var theName = $(item).attr('id');
		theName = theName.substr(8,theName.length-12);
		var what = item.value;
		if(theName == 'Answer')
		{
			if(what == 'image')
			{
				$('#Question'+theName+'1Text').parent().hide();
				$('#Question'+theName+'1Image').parent().show();
				$('#Question'+theName+'2Text').parent().hide();
				$('#Question'+theName+'2Image').parent().show();
				$('#Question'+theName+'3Text').parent().hide();
				$('#Question'+theName+'3Image').parent().show();
				$('#Question'+theName+'4Text').parent().hide();
				$('#Question'+theName+'4Image').parent().show();
			}else{
				$('#Question'+theName+'1Image').parent().hide();
				$('#Question'+theName+'1Text').parent().show();
				$('#Question'+theName+'2Image').parent().hide();
				$('#Question'+theName+'2Text').parent().show();
				$('#Question'+theName+'3Image').parent().hide();
				$('#Question'+theName+'3Text').parent().show();
				$('#Question'+theName+'4Image').parent().hide();
				$('#Question'+theName+'4Text').parent().show();
			}
		}else{
			if(what == 'image')
			{
				$('#Question'+theName+'Text').parent().hide();
				$('#Question'+theName+'Image').parent().show();
			}else{
				$('#Question'+theName+'Image').parent().hide();
				$('#Question'+theName+'Text').parent().show();
			}
		}
	}
	$(function(){
		$('select').each(function(index) {
			var theName = $(this).attr('id');
			theName = theName.substr(theName.length-4);
			if(theName == 'Type')
			{
				if(!$(this).value) $("#"+theName+" option[text=text]").attr("selected","selected");
				change_type($(this));
			}
		});
	});
</script>