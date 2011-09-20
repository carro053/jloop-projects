<?php
    echo $this->Form->create('Tournament', array('url'=>'/brackets/quick_create'));
    echo $this->Form->input('name',array('label'=>'Bracket Name'));
    echo $this->Form->input('type',array('options'=>array('Single Elimination'=>'Single Elimination','Double Elimination'=>'Double Elimination')));
    echo $this->Form->input('seeding',array('options'=>array(0=>'By Order Given',1=>'Random')));
    echo $this->Form->input('competitors',array('type'=>'textarea','label'=>'Competitors<br />One per Line'));
    echo $this->Form->end('Create Quick Bracket');
?>
