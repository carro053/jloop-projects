<?php
foreach($hand as $deck_card):
	echo '<div id="DeckCard'.$deck_card['DeckCard']['id'].'">';
	if(!$theirs) echo '<a href="" onclick="returnCardToHand('.$deck_card['DeckCard']['id'].'); return false;">';
	echo '<img src="/files/magic_cards/'.$deck_card['DeckCard']['card_id'].'.jpg" />';
	if(!$theirs) echo '</a>';
	echo '</div>';
endforeach;
?>