<h2>My Games</h2>
<?php
$deck_array = array();
foreach($decks as $deck):
	$deck_array[$deck['Deck']['id']] = $deck['Deck']['name'].' - '.$deck['User']['username'];
endforeach;
foreach($games as $game):
	if($game['MagicGame']['winner_id'] > 0)
	{	echo '<div>';
		if($user_id == $game['MagicGame']['user_1_id'])
		{
			echo 'VS '.$game['User_2']['username'];
		}else{
			echo 'VS '.$game['User_1']['username'];
		}
		echo ' - <a target="_blank" href="/magic/game_hand/'.$game['MagicGame']['id'].'">View Hand</a> | <a target="_blank" href="/magic/game_battlefield/'.$game['MagicGame']['id'].'">View Battlefield</a>';
		if($user_id == $game['MagicGame']['user_1_id'])
		{
			if($game['MagicGame']['winner_id'] == 1)
			{
				echo ' - You Won';
			}else{
				echo ' - '.$game['User_2']['username'].' Won';
			}
		}else{
			if($game['MagicGame']['winner_id'] == 1)
			{
				echo ' - '.$game['User_1']['username'].' Won';
			}else{
				echo ' - You Won';
			}
		}
		echo '</div>';
	}elseif($user_id != $game['MagicGame']['user_1_id'] && $game['MagicGame']['user_2_deck_id'] == 0)
	{
		echo '<div>VS '.$game['User_1']['username'];
		echo $this->Form->create('MagicGame',array('url' => array('controller' => 'magic', 'action' => 'game_start')));
		echo $this->Form->input('MagicGame.id',array('value'=>$game['MagicGame']['id']));
		echo $this->Form->input('MagicGame.user_2_deck_id',array('options'=>$deck_array,'label'=>'Deck'));
		echo $this->Form->end('Start Game');
		echo '</div>';
	}elseif($game['MagicGame']['user_2_deck_id'] == 0)
	{
		echo '<div>Waiting for '.$game['User_2']['username'].' to Select Deck</div>';
	}else{
		echo '<div>';
		if($user_id == $game['MagicGame']['user_1_id'])
		{
			echo 'VS '.$game['User_2']['username'];
		}else{
			echo 'VS '.$game['User_1']['username'];
		}
		echo ' - <a target="_blank" href="/magic/game_hand/'.$game['MagicGame']['id'].'">View Hand</a> | <a target="_blank" href="/magic/game_battlefield/'.$game['MagicGame']['id'].'">View Battlefield</a></div>';
	}
endforeach;
if(count($games) == 0) echo '<div>You have no games yet.</div>';
echo '<h2>Start New Game</h2>';
echo $this->Form->create('MagicGame',array('url' => array('controller' => 'magic', 'action' => 'game_create')));

echo $this->Form->input('MagicGame.user_1_deck_id',array('options'=>$deck_array,'label'=>'Deck'));
$opponent_array = array();
foreach($users as $user):
	$opponent_array[$user['User']['id']] = $user['User']['username'];
endforeach;
echo $this->Form->input('MagicGame.user_2_id',array('options'=>$opponent_array,'label'=>'Against'));
echo $this->Form->end('Create Game');
?>