<?php
echo '<h2>Your Field</h2>';
echo '<div id="card_pool">';
foreach($your_cards as $deck_card):
	echo '<div><a href="/magic/game_tap_card/'.$game_id.'/'.$deck_card['DeckCard']['id'].'"><img src="/files/magic_cards/'.$deck_card['DeckCard']['card_id'].'.jpg" /></a><br /<a href="/magic/game_discard_card/'.$game_id.'/'.$deck_card['DeckCard']['id'].'">Discard Card</a> <a href="/magic/game_return_card_to_hand/'.$game_id.'/'.$deck_card['DeckCard']['id'].'">Return to Hand</a></div>';
endforeach;
echo '</div>';

echo '<div style="clear:both";>&nbsp;</div>';
echo '<h2>Opponent\'s Field</h2>';
echo '<div id="card_pool">';
foreach($opponents_cards as $deck_card):
	echo '<div><img src="/files/magic_cards/'.$deck_card['DeckCard']['card_id'].'.jpg" /></div>';
endforeach;
echo '</div>';
 ?>
<style>
	div#card_pool div {
		float:left;
	}
</style>