<?php
    echo $this->Form->create('Question',array('url'=>'/questions/add/'.$game_id,'type' => 'file'));
    echo $this->Form->input('title');
    echo $this->Form->input('clue_type',array('options'=>array('text'=>'Text','image'=>'Image'),'onchange'=>'change_type(this);'));
    echo $this->Form->input('clue_text');
    echo $this->Form->input('clue_image',array('type'=>'file'));
    echo $this->Form->input('question_type',array('options'=>array('text'=>'Text','image'=>'Image'),'onchange'=>'change_type(this);'));
    echo $this->Form->input('question_text');
    echo $this->Form->input('question_image',array('type'=>'file'));
    echo $this->Form->input('insight_type',array('options'=>array('text'=>'Text','image'=>'Image'),'onchange'=>'change_type(this);'));
    echo $this->Form->input('insight_text');
    echo $this->Form->input('insight_image',array('type'=>'file'));
    echo $this->Form->input('answer_1_type',array('options'=>array('text'=>'Text','image'=>'Image'),'onchange'=>'change_type(this);'));
    echo $this->Form->input('answer_1_text');
    echo $this->Form->input('answer_1_image',array('type'=>'file'));
    echo $this->Form->input('answer_2_type',array('options'=>array('text'=>'Text','image'=>'Image'),'onchange'=>'change_type(this);'));
    echo $this->Form->input('answer_2_text');
    echo $this->Form->input('answer_2_image',array('type'=>'file'));
    echo $this->Form->input('answer_3_type',array('options'=>array('text'=>'Text','image'=>'Image'),'onchange'=>'change_type(this);'));
    echo $this->Form->input('answer_3_text');
    echo $this->Form->input('answer_3_image',array('type'=>'file'));
    echo $this->Form->input('answer_4_type',array('options'=>array('text'=>'Text','image'=>'Image'),'onchange'=>'change_type(this);'));
    echo $this->Form->input('answer_4_text');
    echo $this->Form->input('answer_4_image',array('type'=>'file'));
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
			$('#Question'+theName+'Image').parent().show();
		}else{
			$('#Question'+theName+'Image').parent().hide();
			$('#Question'+theName+'Text').parent().show();
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