<?php echo '<h2>Deck:'.$deck['Deck']['name'].' - Cards</h2>';
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
if($current_card_id != 0) echo '<div><img src="/files/magic_cards/'.$current_card_id.'.jpg" />x'.$current_card_count.'</div>';
echo '<h2>Card Pool</h2>';
foreach($cards as $card):
	echo '<div><a href="/magic/deck_add_card/'.$deck['Deck']['id'].'/'.$card['Card']['id'].'"><img src="/files/magic_cards/'.$card['Card']['id'].'.jpg" /></a></div>';
endforeach; ?>