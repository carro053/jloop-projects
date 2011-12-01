<?php echo '<h2>Deck: '.$deck['Deck']['name'].' - Cards</h2>';
echo '<div id="current_cards">';
$current_card_id = 0;
$current_card_count = 1;
foreach($deck['DeckCard'] as $deck_card):
	if($current_card_id != $deck_card['card_id'])
	{
		if($current_card_id != 0) echo '<div><img src="/files/magic_cards/'.$current_card_id.'.jpg" /><br />x'.$current_card_count.'</div>';
		$current_card_id = $deck_card['card_id'];
		$current_card_count = 1;
	}else{
		$current_card_count++;
	}
endforeach;
if($current_card_id != 0) echo '<div><img src="/files/magic_cards/'.$current_card_id.'.jpg" /><br />x'.$current_card_count.'</div>';
echo '<h2>Total Cards: '.count($deck['DeckCard']).'</h2>';

echo '</div>';
?>
<style>
	div#current_cards div {
		float:left;
	}
	div#card_pool div {
		float:left;
	}
</style>