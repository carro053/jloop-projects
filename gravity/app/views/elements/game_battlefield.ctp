<?php 
if($your_number == 1)
{
	$opponents_name = $game['User_2']['username'];
	$opponents_number = 2;
}else{	
	$opponents_name = $game['User_1']['username'];
	$opponents_number = 1;
}
if($game['MagicGame']['winner_id'] == 1)
{
	echo '<h2>';
	if($your_number == 1)
	{
		echo 'You Won';
	}else{
		echo $opponents_name.' Won';
	}
	echo '</h2>';
}elseif($game['MagicGame']['winner_id'] == 2)
{
	echo '<h2>';
	if($your_number == 2)
	{
		echo 'You Won';
	}else{
		echo $opponents_name.' Won';
	}
	echo '</h2>';
}
echo '<h2>Your Field | <a href="" onclick="loseAHitPoint(); return false;">-</a> <span id="MyHealth">'.$game['MagicGame']['user_'.$your_number.'_hp'].'</span> <a href="" onclick="gainAHitPoint(); return false;">+</a></h2>';
echo '<a href="/magic/game_graveyard/'.$game['MagicGame']['id'].'/0" target="_blank">View Your Graveyard</a> - <a href="/magic/game_library/'.$game['MagicGame']['id'].'/0" target="_blank">View Your Library</a>';
echo '<div id="card_pool">';
foreach($your_cards as $deck_card):
	echo '<div id="DeckCard'.$deck_card['MagicGameDeckCard']['id'].'"><a href="" onclick="tapCard('.$deck_card['MagicGameDeckCard']['id'].'); return false;"><img';
	if($deck_card['MagicGameDeckCard']['tapped']) echo ' style="opacity:0.4;"';
	echo ' src="/files/magic_cards/'.$deck_card['MagicGameDeckCard']['card_id'].'.jpg" /></a><a class="discard_link" href="" onclick="discardCard('.$deck_card['MagicGameDeckCard']['id'].'); return false;"><img src="/img/discard_card.png" /></a> <a class="hand_link" href="" onclick="returnCardToHand('.$deck_card['MagicGameDeckCard']['id'].'); return false;"><img src="/img/return_card_to_hand.png" /></a></div>';
endforeach;
echo '</div>';

echo '<div style="clear:both";>&nbsp;</div>';
if($your_number == $game['MagicGame']['turn'] + 1)
{
	echo '<h2>Your Turn</h2>';
	echo '<a href="/magic/game_end_turn/'.$game['MagicGame']['id'].'" onclick="return confirm(\'Are you sure you want to end your turn?\');"><img src="/img/end_turn.png" /></a>';
}elseif($your_number == 1)
{
	echo '<h2>'.$game['User_2']['username'].'\'s Turn</h2>';
}else{
	echo '<h2>'.$game['User_1']['username'].'\'s Turn</h2>';	
}

echo '<h2>'.$opponents_name.'\'s Field | '.$game['MagicGame']['user_'.$opponents_number.'_hp'];
if($opponents_hand == 1)
{
	echo ' | '.$opponents_hand.' Card In Hand';
}else{
	echo ' | '.$opponents_hand.' Cards In Hand';
}
echo '</h2>';
echo '<a href="/magic/game_graveyard/'.$game['MagicGame']['id'].'/1" target="_blank">View Their Graveyard</a> - <a href="/magic/game_library/'.$game['MagicGame']['id'].'/1" target="_blank">View Their Library</a> - <a href="" onclick="giveOpponentACard(); return false;">Give '.$opponents_name.' A Random Card</a>';
echo '<div id="card_pool">';
foreach($opponents_cards as $deck_card):
	echo '<div><img';
	if($deck_card['MagicGameDeckCard']['tapped']) echo ' style="opacity:0.4;"';
	echo ' src="/files/magic_cards/'.$deck_card['MagicGameDeckCard']['card_id'].'.jpg" /></div>';
endforeach;
echo '</div>';
?>
<script type="text/javascript">
$('div','#card_pool').hover(
	function () {
		$('.discard_link',this).show();
		$('.hand_link',this).show();
	},
	function () {
		$('.discard_link',this).hide();
		$('.hand_link',this).hide();
	}
);
</script>