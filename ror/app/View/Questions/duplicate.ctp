<h2>Select A Game To Duplicate To</h2>
<?php
    echo $this->Form->create('Question',array('url'=>'/questions/duplicate/'.$question_id));
    echo $this->Form->input('game_id',array('options'=>$games));
    echo $this->Form->end('Submit');
?>