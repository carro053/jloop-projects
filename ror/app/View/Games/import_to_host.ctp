<h2>Select A Game Series To Import To</h2>
<?php
    echo $this->Form->create('Game',array('url'=>'/games/import_to_host_with_series/'.$snapshot_id.'/'.$environment.'/'.$host_id));
    echo $this->Form->input('series_id',array('options'=>$series));
    echo $this->Form->end('Submit');
?>
<p>Submitting this form will import all of the questions belonging to this snapshot into the Main RoR Tool for the Host you chose and tie it to this game series.</p>