<h2>Select The Environment To Import To</h2>
<?php
    echo $this->Form->create('Game',array('url'=>'/games/import/'.$snapshot_id));
    echo $this->Form->input('environment',array('options'=>array('staging'=>'Staging','live'=>'Live')));
    echo $this->Form->end('Select The Host');
?>