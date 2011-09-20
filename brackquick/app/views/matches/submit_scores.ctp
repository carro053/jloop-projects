<?php
    echo $this->Form->create('Match', array('url'=>'/matches/submit_scores/'.$tournament['Tournament']['hash'].'/'.$match['Match']['id']));
    if(substr($match['Match']['top_user'],-1) == 's')
    {
    	$top_user = $match['Match']['top_user']."' Score";
    }else{
    	$top_user = $match['Match']['top_user']."'s Score";
    }
    if(substr($match['Match']['bottom_user'],-1) == 's')
    {
    	$bottom_user = $match['Match']['bottom_user']."' Score";
    }else{
    	$bottom_user = $match['Match']['bottom_user']."'s Score";
    }
    echo $this->Form->input('top_score',array('label'=>$top_user));
    echo $this->Form->input('bottom_score',array('label'=>$bottom_user));
    echo '<div class="submit"><input type="submit" value="'.$match['Match']['top_user'].' Won" name="data[Match][top]"> <input type="submit" value="'.$match['Match']['bottom_user'].' Won" name="data[Match][bottom]"></div>';
    echo '</form>';
?>
