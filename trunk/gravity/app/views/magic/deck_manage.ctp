<?php echo '<h2>Deck: '.$deck['Deck']['name'].' - Cards</h2>';
echo '<div id="current_cards">';
echo $this->element('deck_cards');
echo '</div>';
echo '<div style="clear:both;">&nbsp;</div>';
echo '<h2>Card Pool</h2>';
echo '<div id="card_pool">';
foreach($cards as $card):
	echo '<div><a href="" onclick="add_card('.$card['Card']['id'].'); return false;"><img src="/files/magic_cards/'.$card['Card']['id'].'.jpg" /></a></div>';
endforeach;
echo '</div>';
 ?>
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
<style>
	div#current_cards div {
		float:left;
	}
	div#card_pool div {
		float:left;
	}
</style>