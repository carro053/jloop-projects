<?php
    echo $this->Form->create('Game',array('url'=>'/games/add','type' => 'file'));
    echo $this->Form->input('id');
    echo $this->Form->input('title');
    echo $this->Form->input('icon',array('type'=>'file','label'=>'Icon<br />(42x42)'));
    echo $this->Form->end('Submit');
?>