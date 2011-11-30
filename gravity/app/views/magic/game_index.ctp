<h2>My Games</h2>
<?php
foreach($games as $game):
	if($user_id != $game['MagicGame']['user_1_id'] && $game['MagicGame']['user_2_deck_id'] == 0)
	{
		echo '<div>SelectDeck</div>';
	}elseif($game['MagicGame']['user_2_deck_id'] == 0)
	{
		echo '<div>Waiting for Opponent to Select Deck</div>';
	}else{
		echo '<div><a target="_blank" href="/magic/game_hand/'.$game['MagicGame']['id'].'">View Hand</a> | <a target="_blank" href="/magic/game_battlefield/'.$game['MagicGame']['id'].'">View Battlefield</a></div>';
	}
endforeach;
if(count($$games) == 0) echo '<div>You have no games yet.</div>';
echo '<h2>Start New Game</h2>';
echo $this->Form->create('MagicGame',array('url' => array('controller' => 'magic', 'action' => 'game_create')));
$deck_array = array();
foreach($decks as $deck):
	$deck_array[$deck['Deck']['id']] = $deck['Deck']['name'];
endforeach;
echo $this->Form->input('MagicGame.user_1_deck_id',array('options'=>$deck_array,'label'=>'Deck'));
$opponent_array = array();
foreach($users as $user):
	$opponent_array[$user['User']['id']] = $user['User']['username'];
endforeach;
echo $this->Form->input('MagicGame.user_2_id',array('options'=>$opponent_array,'label'=>'Against'));
echo $this->Form->end('Create Game');
?>