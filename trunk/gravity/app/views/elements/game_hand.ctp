<?php
foreach($hand as $deck_card):
	echo '<div id="DeckCard'.$deck_card['MagicGameDeckCard']['id'].'"><a href="" onclick="playCard('.$deck_card['MagicGameDeckCard']['id'].'); return false;"><img src="/files/magic_cards/'.$deck_card['MagicGameDeckCard']['card_id'].'.jpg" /></a></div>';
endforeach;
?>