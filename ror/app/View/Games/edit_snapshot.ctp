<h2>Edit Snapshot</h2>
<?php
    echo $this->Form->create('GameSnapshot',array('url'=>'/games/edit_snapshot/'.$game_id.'/'.$snapshot_id));
    echo $this->Form->input('id');
    echo $this->Form->input('note');
    echo $this->Form->end('Submit');
?>