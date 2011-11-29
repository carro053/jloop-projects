<?php
$current_card_id = 0;
$current_card_count = 1;
foreach($deck['DeckCard'] as $deck_card):
	if($current_card_id != $deck_card['card_id'])
	{
		if($current_card_id != 0) echo '<div><a href="/magic/deck_remove_card/'.$deck['Deck']['id'].'/'.$current_card_id.'"><img src="/files/magic_cards/'.$current_card_id.'.jpg" /></a>x'.$current_card_count.'</div>';
		$current_card_id = $deck_card['card_id'];
		$current_card_count = 1;
	}else{
		$current_card_count++;
	}
endforeach;
if($current_card_id != 0) echo '<div><a href="/magic/deck_remove_card/'.$deck['Deck']['id'].'/'.$current_card_id.'"><img src="/files/magic_cards/'.$current_card_id.'.jpg" /></a>x'.$current_card_count.'</div>';
?>