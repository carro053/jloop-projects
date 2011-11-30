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
</style>
<script type="text/javascript">
$(document).ready(function() {
    setInterval("refreshHand()",3000);
});

function playCard(deck_card_id)
{
	$.post('/magic/game_play_card/<?php echo $game_id; ?>/'+deck_card_id, function(data) {
		refreshand();
	});
}

function refreshHand()
{ 
	$('#card_pool').load('/magic/game_refresh_hand/<?php echo $game_id; ?>');
}
</script>