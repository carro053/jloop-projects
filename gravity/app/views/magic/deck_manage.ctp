<?php echo '<h2>Deck: '.$deck['Deck']['name'].' - Cards</h2>';
echo '<div id="current_cards">';
$current_card_id = 0;
$current_card_count = 1;
foreach($deck['DeckCard'] as $deck_card):
	if($current_card_id != $deck_card['card_id'])
	{
		if($current_card_id != 0) echo '<div><a href="" onclick="remove_card('.$current_card_id.'); return false;"><img src="/files/magic_cards/'.$current_card_id.'.jpg" /></a>x'.$current_card_count.'</div>';
		$current_card_id = $deck_card['card_id'];
		$current_card_count = 1;
	}else{
		$current_card_count++;
	}
endforeach;
if($current_card_id != 0) echo '<div><a href="" onclick="remove_card('.$current_card_id.'); return false;"><img src="/files/magic_cards/'.$current_card_id.'.jpg" /></a>x'.$current_card_count.'</div>';
echo '</div>';
echo '<h2>Card Pool</h2>';
foreach($cards as $card):
	echo '<div><a href="" onclick="add_card('.$card['Card']['id'].'); return false;"><img src="/files/magic_cards/'.$card['Card']['id'].'.jpg" /></a></div>';
endforeach; ?>
<script type="text/javascript">
	function add_card(card_id)
	{
		$('#current_cards').load('/magic/deck_add_card/<?php echo $deck['Deck']['id']; ?>/'+card_id);
	}
	function remove_card(card_id)
	{
		$('#current_cards').load('/magic/deck_remove_card/<?php echo $deck['Deck']['id']; ?>/'+card_id);
	}
</script>