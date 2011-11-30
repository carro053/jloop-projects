<?php
foreach($hand as $deck_card):
	echo '<div id="DeckCard'.$deck_card['MagicGameDeckCard']['id'].'">';
	if(!$theirs) echo '<a href="" onclick="returnCardToHand('.$deck_card['MagicGameDeckCard']['id'].'); return false;">';
	echo '<img src="/files/magic_cards/'.$deck_card['MagicGameDeckCard']['card_id'].'.jpg" />';
	if(!$theirs) echo '</a>';
	echo '</div>';
endforeach;
?>