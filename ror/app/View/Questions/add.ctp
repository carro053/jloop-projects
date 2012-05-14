<?php
    echo $this->Form->create('Question',array('url'=>'/questions/add/'.$game_id,'type' => 'file'));
    echo $this->Form->input('title');
    echo $this->Form->input('clue_type',array('options'=>array('text'=>'Text','image'=>'Image'),'onchange'=>'change_type(this);'));
    echo $this->Form->input('clue_text');
    echo $this->Form->input('clue_image');
    echo $this->Form->input('question_type',array('options'=>array('text'=>'Text','image'=>'Image')));
    echo $this->Form->input('question_text');
    echo $this->Form->input('question_image',array('type'=>'file'));
    echo $this->Form->input('insight_type',array('options'=>array('text'=>'Text','image'=>'Image')));
    echo $this->Form->input('insight_text');
    echo $this->Form->input('insight_image',array('type'=>'file'));
    echo $this->Form->input('answer_1_type',array('options'=>array('text'=>'Text','image'=>'Image')));
    echo $this->Form->input('answer_1_text');
    echo $this->Form->input('answer_1_image',array('type'=>'file'));
    echo $this->Form->input('answer_1_type',array('options'=>array('text'=>'Text','image'=>'Image')));
    echo $this->Form->input('answer_1_text');
    echo $this->Form->input('answer_1_image',array('type'=>'file'));
    echo $this->Form->input('answer_1_type',array('options'=>array('text'=>'Text','image'=>'Image')));
    echo $this->Form->input('answer_1_text');
    echo $this->Form->input('answer_1_image',array('type'=>'file'));
    echo $this->Form->input('answer_1_type',array('options'=>array('text'=>'Text','image'=>'Image')));
    echo $this->Form->input('answer_1_text');
    echo $this->Form->input('answer_1_image',array('type'=>'file'));
    echo $this->Form->input('correct_answer',array('options'=>array(1=>1,2=>2,3=>3,4=>4)));
    echo $this->Form->input('has_prize');
    echo $this->Form->end('Submit');
?>
<script type="text/javascript">
	function change_type(item)
	{
		var theName = $(item).attr('id');
		theName = theName.substr(8,theName.length-12);
		var what = item.value;
		if(what == 'image')
		{
			$('#Question'+theName+'Text').parent().hide();
		}else{
			$('#Question'+theName+'Image').parent().hide();
		}
	}
	$(function(){
		$('select').each(function(index) {
			if($(this).value == 'image' || $(this).value == 'text')
			{
				change_type($(this));
			}
		});
	});
</script>