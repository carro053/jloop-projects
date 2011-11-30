<?php
echo '<h2>Your Hand</h2>';
echo '<div id="card_pool">';
foreach($hand as $deck_card):
	echo '<div><a href="/magic/game_play_card/'.$deck_id.'/'.$deck_card['DeckCard']['id'].'"><img src="/files/magic_cards/'.$deck_card['DeckCard']['card_id'].'.jpg" /></a></div>';
endforeach;
echo '</div>';
echo '<div style="clear:both";>&nbsp;</div>';
echo '<a href="/magic/game_mulligan/'.$game_id.'" onclick="return confirm(\'Are you sure?\');">Mulligan Hand</a>';
?>
<style>
	div#card_pool div {
		float:left;
	}
</style>