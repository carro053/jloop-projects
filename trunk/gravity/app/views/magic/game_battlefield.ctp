<?php
echo '<div id="battlefield">';
echo $this->element('game_battlefield');
echo '</div>';
 ?>
<style>
	div#card_pool div {
		float:left;
	}
</style>
<script type="text/javascript">
$(document).ready(function() {
    setInterval("refreshBattlefield()",3000);
});

function giveOpponentACard()
{
	if(confirm('Are you sure you want to?'))
	{
		$.post('/magic/game_give_card_to_opponent/<?php echo $game['MagicGame']['id']; ?>');
	}
}

function tapCard(deck_card_id)
{
	$.post('/magic/game_tap_card/<?php echo $game['MagicGame']['id']; ?>/'+deck_card_id, function(data) {
		$('#DeckCard'+deck_card_id).attr('style', 'opacity:'+data+';');
	});
}

function discardCard(deck_card_id)
{
	$.post('/magic/game_discard_card/<?php echo $game['MagicGame']['id']; ?>/'+deck_card_id, function(data) {
		if(data == 1) $('#DeckCard'+deck_card_id).remove();
	});
}

function returnCardToHand(deck_card_id)
{
	$.post('/magic/game_return_card_to_hand/<?php echo $game['MagicGame']['id']; ?>/'+deck_card_id, function(data) {
		if(data == 1) $('#DeckCard'+deck_card_id).remove();
	});
}

function refreshBattlefield()
{ 
	$('#battlefield').load('/magic/game_refresh_battlefield/<?php echo $game['MagicGame']['id']; ?>');
}
</script>