<?php
    echo $this->Form->create('Tournament', array('action' => 'quick_create'));
    echo $this->Form->input('name');
    echo $this->Form->input('type',array('options'=>'Single Elimination'));
    echo $this->Form->input('seeding',array('options'=>array(0=>'By Order Given',1=>'Random')));
    echo $this->Form->input('users',array('type'=>'textarea','label'=>'Competitors<br />One per Line'));
    echo $this->Form->end('Create Quick Bracket');
?>
