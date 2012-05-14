<?php
    echo $this->Form->create('Game',array('url'=>'/games/add','type' => 'file'));
    echo $this->Form->input('title');
    echo $this->Form->input('icon',array('type'=>'file'));
    echo $this->Form->end('Submit');
?>