<?php
echo '<h2>Your Hand</h2>';
echo '<div id="card_pool">';
echo $this->element('game_hand');
echo '</div>';
echo '<div style="clear:both";>&nbsp;</div>';
echo '<a href="/magic/game_mulligan/'.$game_id.'" onclick="return confirm(\'Are you sure?\');">Mulligan Hand</a>';
?>
<style>
	div#card_pool div {
		float:left;
	}
	
	div#card_pool div {
		background-color: #FFFFFF;
		width: 175px;
		height: 233px;
		position:relative;
		z-index: 10;
		-moz-transform: scale(0.75);
		-webkit-transform: scale(0.75);
	}
	div#card_pool div:hover {
		width: 175px;
		height: 233px;
		position:relative;
		z-index: 100;
		-moz-transform: scale(1);
		-webkit-transform: scale(1);
	}
</style>
<script type="text/javascript">
$(document).ready(function() {
    setInterval("refreshHand()",3000);
});

function playCard(deck_card_id)
{
	$.post('/magic/game_play_card/<?php echo $game_id; ?>/'+deck_card_id, function(data) {
		if(data == 1) $('#DeckCard'+deck_card_id).remove();
	});
}

function refreshHand()
{ 
	$('#card_pool').load('/magic/game_refresh_hand/<?php echo $game_id; ?>');
}
</script>