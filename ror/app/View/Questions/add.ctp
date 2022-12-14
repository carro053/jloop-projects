<h3><a href="/questions/index/<?php echo $game_id; ?>">&larr;Back To Question List</a></h3>
<?php echo $this->Form->create('Question',array('url'=>'/questions/add/'.$game_id,'type' => 'file')); ?>
<table>
	<thead>
		<tr>
			<th><label>Status</label></th>
			<th><?php echo $this->Form->input('status',array('label'=>false,'options'=>$status_options)); ?></th>
			<th><?php echo $this->Form->input('show_notes',array('checked'=>'checked','onclick'=>'change_show_notes(this);')); ?></th>
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
				<?php echo $this->Form->input('clue_image',array('label'=>'(288x204)','type'=>'file')); ?>
			</td>
			<td class="note"><?php echo $this->Form->input('clue_note',array('label'=>false)); ?></td>
		</tr>
		<tr>
			<td><label>Question Type</label></td>
			<td><?php echo $this->Form->input('question_type',array('label'=>false,'options'=>array('text'=>'Text','image'=>'Image'),'onchange'=>'change_type(this);')); ?></td>
			<td class="note"><?php ?></td>
		</tr>
		<tr>
			<td><label>Question</label></td>
			<td>
				<?php echo $this->Form->input('question_text',array('label'=>false)); ?>
				<?php echo $this->Form->input('question_image',array('label'=>'(289x97)','type'=>'file')); ?>
			</td>
			<td class="note"><?php echo $this->Form->input('question_note',array('label'=>false)); ?></td>
		</tr>
		<tr>
			<td><label>Insight Type</label></td>
			<td><?php echo $this->Form->input('insight_type',array('label'=>false,'options'=>array('text'=>'Text','image'=>'Image'),'onchange'=>'change_type(this);')); ?></td>
			<td class="note"><?php ?></td>
		</tr>
		<tr>
			<td><label>Insight</label></td>
			<td>
				<?php echo $this->Form->input('insight_text',array('label'=>false)); ?>
				<?php echo $this->Form->input('insight_image',array('label'=>'(288x204)','type'=>'file')); ?>
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
				<?php echo $this->Form->input('answer_1_text',array('type'=>'textarea','label'=>false)); ?>
				<?php echo $this->Form->input('answer_1_image',array('label'=>'(139x79)','type'=>'file')); ?>
			</td>
			<td class="note"><?php echo $this->Form->input('answer_1_note',array('label'=>false)); ?></td>
		</tr>
		<tr>
			<td><label>Answer 2</label></td>
			<td>
				<?php echo $this->Form->input('answer_2_text',array('type'=>'textarea','label'=>false)); ?>
				<?php echo $this->Form->input('answer_2_image',array('label'=>'(139x79)','type'=>'file')); ?>
			</td>
			<td class="note"><?php echo $this->Form->input('answer_2_note',array('label'=>false)); ?></td>
		</tr>
		<tr>
			<td><label>Answer 3</label></td>
			<td>
				<?php echo $this->Form->input('answer_3_text',array('type'=>'textarea','label'=>false)); ?>
				<?php echo $this->Form->input('answer_3_image',array('label'=>'(139x79)','type'=>'file')); ?>
			</td>
			<td class="note"><?php echo $this->Form->input('answer_3_note',array('label'=>false)); ?></td>
		</tr>
		<tr>
			<td><label>Answer 4</label></td>
			<td>
				<?php echo $this->Form->input('answer_4_text',array('type'=>'textarea','label'=>false)); ?>
				<?php echo $this->Form->input('answer_4_image',array('label'=>'(139x79)','type'=>'file')); ?>
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
			<td><label>Prize Image (241??132)</label></td>
			<td><?php echo $this->Form->input('prize_image',array('label'=>false,'type'=>'file')); ?></td>
			<td class="note"><?php ?></td>
		</tr>
		<tr>
			<td><label>Prize Text</label></td>
			<td><?php echo $this->Form->input('prize_text',array('label'=>false)); ?></td>
			<td class="note"><?php echo $this->Form->input('prize_note',array('label'=>false)); ?></td>
		</tr>
		<tr>
			<td><label>Learn More Item</label></td>
			<td><?php echo $this->Form->input('learn_more_item_id',array('label'=>false,'options'=>$learn_more_items)); ?></td>
			<td class="note"></td>
		</tr>
		<tr>
			<td><label>Learn More Title</label></td>
			<td><?php echo $this->Form->input('learn_more_title',array('label'=>false)); ?></td>
			<td class="note"><?php echo $this->Form->input('learn_more_title_note',array('label'=>false)); ?></td>
		</tr>
		<tr>
			<td><label>Learn More URL</label></td>
			<td><?php echo $this->Form->input('learn_more_url',array('label'=>false)); ?></td>
			<td class="note"><?php echo $this->Form->input('learn_more_url_note',array('label'=>false)); ?></td>
		</tr>
		<tr>
			<td></td>
			<td>
				<a class="button" href="#" onclick="submit_form(0); return false;">Submit</a>
				<a class="button" href="#" onclick="submit_form(1); return false;">Submit and Preview</a>
			</td>
			<td class="note"><?php ?></td>
		</tr>
	</tbody>
</table>
</form>

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
	function change_show_notes(checkbox)
	{
		if(checkbox.checked)
			$('.note div').css('visibility','visible');
		else
			$('.note div').css('visibility','hidden');
	}
	function submit_form(preview)
	{
		action = "<?php echo '/questions/add/'.$game_id; ?>";
		if(preview)
		{
			action += '/1';
		}else{
			action += '/0';
		}
		$('#QuestionAddForm').attr('action',action);
		$('#QuestionAddForm').submit();
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