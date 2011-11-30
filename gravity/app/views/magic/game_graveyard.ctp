<?php
if($theirs)
{
	echo '<h2>Their Graveyard</h2>';
}else{
	echo '<h2>Your Graveyard</h2>';
}
echo '<div id="card_pool">';
echo $this->element('game_graveyard');
echo '</div>';
?>
<style>
	div#card_pool div {
		float:left;
	}
</style>
<script type="text/javascript">
$(document).ready(function() {
    setInterval("refreshGraveyard()",3000);
});

function returnCardToHand(deck_card_id)
{
	$.post('/magic/game_return_card_to_hand/<?php echo $game_id; ?>/'+deck_card_id, function(data) {
		if(data == 1) $('#DeckCard'+deck_card_id).remove();
	});
}

function refreshGraveyard()
{ 
	$('#card_pool').load('/magic/game_refresh_graveyard/<?php echo $game_id; ?>/<?php echo $theirs; ?>');
}
</script>