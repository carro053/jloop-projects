<?php
echo '<h2>Your Field</h2>';
echo '<div id="card_pool">';
foreach($your_cards as $deck_card):
	echo '<div><a href="/magic/game_tap_card/'.$game['MagicGame']['id'].'/'.$deck_card['DeckCard']['id'].'"><img';
	if($deck_card['DeckCard']['tapped']) echo ' style="opacity:0.4;"';
	echo ' src="/files/magic_cards/'.$deck_card['DeckCard']['card_id'].'.jpg" /></a><br /><a href="/magic/game_discard_card/'.$game['MagicGame']['id'].'/'.$deck_card['DeckCard']['id'].'">Discard Card</a> <a href="/magic/game_return_card_to_hand/'.$game['MagicGame']['id'].'/'.$deck_card['DeckCard']['id'].'">Return to Hand</a></div>';
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
echo '<h2>Opponent\'s Field</h2>';
echo '<div id="card_pool">';
foreach($opponents_cards as $deck_card):
	echo '<div><img';
	if($deck_card['DeckCard']['tapped']) echo ' style="opacity:0.4;"';
	echo ' src="/files/magic_cards/'.$deck_card['DeckCard']['card_id'].'.jpg" /></div>';
endforeach;
echo '</div>';
 ?>
<style>
	div#card_pool div {
		float:left;
	}
</style>