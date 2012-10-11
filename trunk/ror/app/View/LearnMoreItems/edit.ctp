<h2>Edit Learn More Item</h2>
<?php
    echo $this->Form->create('LearnMoreItem',array('url'=>'/learn_more_items/edit/'.$this->data['LearnMoreItem']['id'],'type' => 'file'));
    echo $this->Form->input('id');
    echo $this->Form->input('label');
    echo $this->Form->input('url',array('style'=>'width:500px'));
    echo $this->Form->end('Submit');
?>