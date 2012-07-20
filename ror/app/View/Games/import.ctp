<h2>Select A Host To Import To</h2>
<?php
    echo $this->Form->create('Game',array('url'=>'/games/import_to_host/'.$snapshot_id));
    echo $this->Form->input('host_id',array('options'=>$hosts));
    echo $this->Form->end('Submit');
?>
<p>Submitting this form will import all of the questions belonging to this snapshot into the Main RoR Tool for the Host you choose.</p>