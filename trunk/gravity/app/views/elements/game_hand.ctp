<?php
foreach($hand as $deck_card):
	echo '<div id="DeckCard'.$deck_card['DeckCard']['id'].'"><a href="" onclick="playCard('.$deck_card['DeckCard']['id'].'); return false;"><img src="/files/magic_cards/'.$deck_card['DeckCard']['card_id'].'.jpg" /></a></div>';
endforeach;
?>