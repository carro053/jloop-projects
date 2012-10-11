<h2>Add Learn More Item</h2>
<?php
    echo $this->Form->create('LearnMoreItem',array('url'=>'/learn_more_items/add','type' => 'file'));
    echo $this->Form->input('label');
    echo $this->Form->input('url',array('style'=>'width:500px'));
    echo $this->Form->end('Submit');
?>