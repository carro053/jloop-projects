<?php 
echo '<h2>Your Field</h2>';
echo '<a href="/magic/game_graveyard/'.$game['MagicGame']['id'].'/0" target="_blank">View Your Graveyard</a>';
echo '<div id="card_pool">';
foreach($your_cards as $deck_card):
	echo '<div id="DeckCard'.$deck_card['DeckCard']['id'].'"><a href="" onclick="tapCard('.$deck_card['DeckCard']['id'].'); return false;"><img';
	if($deck_card['DeckCard']['tapped']) echo ' style="opacity:0.4;"';
	echo ' src="/files/magic_cards/'.$deck_card['DeckCard']['card_id'].'.jpg" /></a><br /><a href="" onclick="discardCard('.$deck_card['DeckCard']['id'].'); return false;">Discard Card</a> <a href="" onclick="returnCardToHand('.$deck_card['DeckCard']['id'].'); return false;">Return to Hand</a></div>';
endforeach;
echo '</div>';

echo '<div style="clear:both";>&nbsp;</div>';
if($your_number == $game['MagicGame']['turn'] + 1)
{
	echo '<h2>Your Turn</h2>';
	echo '<a href="/magic/game_end_turn/'.$game['MagicGame']['id'].'" onclick="return confirm(\'Are you sure you want to end your turn?\');">End Turn</a>';
}elseif($your_number == 1)
{
	echo '<h2>'.$game['User_2']['username'].'\'s Turn</h2>';
}else{
	echo '<h2>'.$game['User_1']['username'].'\'s Turn</h2>';	
}
if($your_number == 1)
{
	$opponents_name = $game['User_2']['username'];
}else{	
	$opponents_name = $game['User_1']['username'];
}
echo '<h2>'.$opponents_name.'\'s Field</h2>';
echo '<a href="/magic/game_graveyard/'.$game['MagicGame']['id'].'/1" target="_blank">View Their Graveyard</a> - <a href="" onclick="giveOpponentACard(); return false;">Give '.$opponents_name.' A Card</a>';
echo '<div id="card_pool">';
foreach($opponents_cards as $deck_card):
	echo '<div><img';
	if($deck_card['DeckCard']['tapped']) echo ' style="opacity:0.4;"';
	echo ' src="/files/magic_cards/'.$deck_card['DeckCard']['card_id'].'.jpg" /></div>';
endforeach;
echo '</div>';
?>