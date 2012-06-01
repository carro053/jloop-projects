<?php
    echo $this->Form->create('GameSnapshot',array('url'=>'/games/add_snapshot/'.$game_id.'/'.$version_id));
    echo $this->Form->input('note');
    echo $this->Form->end('Submit');
?>