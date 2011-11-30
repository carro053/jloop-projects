<?php
if($theirs)
{
	echo '<h2>Their Graveyard</h2>';
}else{
	echo '<h2>Your Graveyard</h2>';
}
echo '<div id="card_pool">';
foreach($hand as $deck_card):
	echo '<div>';
	if(!$theirs) echo '<a href="/magic/game_return_card_to_hand/'.$game_id.'/'.$deck_card['DeckCard']['id'].'/1">';
	echo '<img src="/files/magic_cards/'.$deck_card['DeckCard']['card_id'].'.jpg" />';
	if(!$theirs) echo '</a>';
	echo '</div>';
endforeach;
echo '</div>';
?>
<style>
	div#card_pool div {
		float:left;
	}
</style>