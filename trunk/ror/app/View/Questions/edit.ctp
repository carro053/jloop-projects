<h3><a href="/questions/index/<?php echo $game_id; ?>">&larr;Back To Question List</a></h3>
<table>
	<thead>
		<?php echo $this->Form->create('Question',array('url'=>'/questions/add/'.$game_id,'type' => 'file')); ?>
		<?php echo $this->Form->input('id'); ?>
		<tr>
			<th><label>Status</label></th>
			<th><?php echo $this->Form->input('status',array('label'=>false,'options'=>$status_options)); ?></th>
			<th><?php echo $this->Form->input('show_notes',array('onclick'=>'change_show_notes(this);')); ?></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td><label>Title</label></td>
			<td><?php echo $this->Form->input('title',array('label'=>false)); ?></td>
			<td class="note"><?php ?></td>
		</tr>
		<tr>
			<td><label>Time</label></td>
			<td><?php echo $this->Form->input('time',array('label'=>false,'value'=>'Today, 12:00pm')); ?></td>
			<td class="note"><?php ?></td>
		</tr>
		<tr>
			<td><label>Clue Type</label></td>
			<td><?php echo $this->Form->input('clue_type',array('label'=>false,'options'=>array('text'=>'Text','image'=>'Image'),'onchange'=>'change_type(this);')); ?></td>
			<td class="note"><?php ?></td>
		</tr>
		<tr>
			<td><label>Clue</label></td>
			<td>
				<?php echo $this->Form->input('clue_text',array('label'=>false)); ?>
				<?php if(is_file(WWW_ROOT.'img'.DS.'clues'.DS.$this->data['Question']['id'].'.png')) echo '<img id="ClueImage" src="/img/clues/'.$this->data['Question']['id'].'.png" />'; ?>
				<?php echo $this->Form->input('clue_image',array('label'=>false,'type'=>'file')); ?>
			</td>
			<td class="note"><?php echo $this->Form->input('clue_note',array('label'=>false)); ?></td>
		</tr>
		<tr>
			<td><label>Question Type</label></td>
			<td><?php echo $this->Form->input('question_type',array('label'=>false,'options'=>array('text'=>'Text','image'=>'Image'),'onchange'=>'change_type(this);'));; ?></td>
			<td class="note"><?php ?></td>
		</tr>
		<tr>
			<td><label>Question</label></td>
			<td>
				<?php echo $this->Form->input('question_text',array('label'=>false)); ?>
				<?php if(is_file(WWW_ROOT.'img'.DS.'questions'.DS.$this->data['Question']['id'].'.png')) echo '<img id="QuestionImage" src="/img/questions/'.$this->data['Question']['id'].'.png" />'; ?>
				<?php echo $this->Form->input('question_image',array('label'=>false,'type'=>'file')); ?>
			</td>
			<td class="note"><?php echo $this->Form->input('question_note',array('label'=>false)); ?></td>
		</tr>
		<tr>
			<td><label>Insight Type</label></td>
			<td><?php echo $this->Form->input('insight_type',array('label'=>false,'options'=>array('text'=>'Text','image'=>'Image'),'onchange'=>'change_type(this);'));; ?></td>
			<td class="note"><?php ?></td>
		</tr>
		<tr>
			<td><label>Insight</label></td>
			<td>
				<?php echo $this->Form->input('insight_text',array('label'=>false)); ?>
				<?php if(is_file(WWW_ROOT.'img'.DS.'insights'.DS.$this->data['Question']['id'].'.png')) echo '<img id="InsightImage" src="/img/insights/'.$this->data['Question']['id'].'.png" />'; ?>
				<?php echo $this->Form->input('insight_image',array('label'=>false,'type'=>'file')); ?>
			</td>
			<td class="note"><?php echo $this->Form->input('insight_note',array('label'=>false)); ?></td>
		</tr>
		<tr>
			<td><label>Answer Type</label></td>
			<td><?php echo $this->Form->input('answer_type',array('label'=>false,'options'=>array('text'=>'Text','image'=>'Image'),'onchange'=>'change_type(this);')); ?></td>
			<td class="note"><?php ?></td>
		</tr>
		<tr>
			<td><label>Answer 1</label></td>
			<td>
				<?php echo $this->Form->input('answer_1_text',array('label'=>false)); ?>
				<?php if(is_file(WWW_ROOT.'img'.DS.'answers'.DS.$this->data['Question']['id'].'-1.png')) echo '<img id="Answer1Image" src="/img/answers/'.$this->data['Question']['id'].'-1.png" />'; ?>
				<?php echo $this->Form->input('answer_1_image',array('label'=>false,'type'=>'file')); ?>
			</td>
			<td class="note"><?php echo $this->Form->input('answer_1_note',array('label'=>false)); ?></td>
		</tr>
		<tr>
			<td><label>Answer 2</label></td>
			<td>
				<?php echo $this->Form->input('answer_2_text',array('label'=>false)); ?>
				<?php if(is_file(WWW_ROOT.'img'.DS.'answers'.DS.$this->data['Question']['id'].'-2.png')) echo '<img id="Answer2Image" src="/img/answers/'.$this->data['Question']['id'].'-2.png" />'; ?>
				<?php echo $this->Form->input('answer_2_image',array('label'=>false,'type'=>'file')); ?>
			</td>
			<td class="note"><?php echo $this->Form->input('answer_2_note',array('label'=>false)); ?></td>
		</tr>
		<tr>
			<td><label>Answer 3</label></td>
			<td>
				<?php echo $this->Form->input('answer_3_text',array('label'=>false)); ?>
				<?php if(is_file(WWW_ROOT.'img'.DS.'answers'.DS.$this->data['Question']['id'].'-3.png')) echo '<img id="Answer3Image" src="/img/answers/'.$this->data['Question']['id'].'-3.png" />'; ?>
				<?php echo $this->Form->input('answer_3_image',array('label'=>false,'type'=>'file')); ?>
			</td>
			<td class="note"><?php echo $this->Form->input('answer_3_note',array('label'=>false)); ?></td>
		</tr>
		<tr>
			<td><label>Answer 4</label></td>
			<td>
				<?php echo $this->Form->input('answer_4_text',array('label'=>false)); ?>
				<?php if(is_file(WWW_ROOT.'img'.DS.'answers'.DS.$this->data['Question']['id'].'-4.png')) echo '<img id="Answer4Image" src="/img/answers/'.$this->data['Question']['id'].'-4.png" />'; ?>
				<?php echo $this->Form->input('answer_4_image',array('label'=>false,'type'=>'file')); ?>
			</td>
			<td class="note"><?php echo $this->Form->input('answer_4_note',array('label'=>false)); ?></td>
		</tr>
		<tr>
			<td><label>Correct Answer</label></td>
			<td><?php echo $this->Form->input('correct_answer',array('label'=>false,'options'=>array(0=>1,1=>2,2=>3,3=>4))); ?></td>
			<td class="note"><?php ?></td>
		</tr>
		<tr>
			<td><label>Has Prize</label></td>
			<td><?php  echo $this->Form->input('has_prize',array('label'=>false)); ?></td>
			<td class="note"><?php ?></td>
		</tr>
		<tr>
			<td><label>Prize Image (241×132)</label></td>
			<td>
				<?php if(is_file(WWW_ROOT.'img'.DS.'prizes'.DS.$this->data['Question']['id'].'.png')) echo '<img id="PrizeImage" src="/img/prizes/'.$this->data['Question']['id'].'.png" />'; ?>
				<?php echo $this->Form->input('prize_image',array('label'=>false,'type'=>'file')); ?>
			</td>
			<td class="note"><?php ?></td>
		</tr>
		<tr>
			<td><label>Prize Text</label></td>
			<td><?php echo $this->Form->input('prize_text',array('label'=>false)); ?></td>
			<td class="note"><?php echo $this->Form->input('prize_note',array('label'=>false)); ?></td>
		</tr>
		<tr>
			<td><?php echo $this->Form->end('Submit'); ?></td>
			<td><?php  ?></td>
			<td class="note"><?php ?></td>
		</tr>
	</tbody>
</table>

<script type="text/javascript">
	function change_type(item)
	{
		var theName = $(item).attr('id');
		theName = theName.substr(8,theName.length-12);
		var what = $('#Question'+theName+'Type option:selected').val();
		if(theName == 'Answer')
		{
			if(what == 'image')
			{
				$('#Question'+theName+'1Text').parent().hide();
				$('#Question'+theName+'1Image').parent().show();
				$('#'+theName+'1Image').show();
				$('#Question'+theName+'2Text').parent().hide();
				$('#Question'+theName+'2Image').parent().show();
				$('#'+theName+'2Image').show();
				$('#Question'+theName+'3Text').parent().hide();
				$('#Question'+theName+'3Image').parent().show();
				$('#'+theName+'3Image').show();
				$('#Question'+theName+'4Text').parent().hide();
				$('#Question'+theName+'4Image').parent().show();
				$('#'+theName+'4Image').show();
			}else{
				$('#Question'+theName+'1Image').parent().hide();
				$('#'+theName+'1Image').hide();
				$('#Question'+theName+'1Text').parent().show();
				$('#Question'+theName+'2Image').parent().hide();
				$('#'+theName+'2Image').hide();
				$('#Question'+theName+'2Text').parent().show();
				$('#Question'+theName+'3Image').parent().hide();
				$('#'+theName+'3Image').hide();
				$('#Question'+theName+'3Text').parent().show();
				$('#Question'+theName+'4Image').parent().hide();
				$('#'+theName+'4Image').hide();
				$('#Question'+theName+'4Text').parent().show();
			}
		}else{
			if(what == 'image')
			{
				$('#Question'+theName+'Text').parent().hide();
				$('#Question'+theName+'Image').parent().show();
				$('#'+theName+'Image').show();
			}else{
				$('#Question'+theName+'Image').parent().hide();
				$('#'+theName+'Image').hide();
				$('#Question'+theName+'Text').parent().show();
			}
		}
	}
	function change_show_notes(checkbox)
	{
		if(checkbox.checked)
			$('.note div').css('visibility','visible');
		else
			$('.note div').css('visibility','hidden');
	}
	$(function(){
		$('select').each(function(index) {
			var theName = $(this).attr('id');
			theName = theName.substr(theName.length-4);
			if(theName == 'Type')
			{
				if(!$('#Question'+theName+'Type option:selected').val()) $("#"+theName+" option[text=text]").attr("selected","selected");
				change_type($(this));
			}
		});
		console.log( $('#QuestionShowNotes').checked );
	});
</script>