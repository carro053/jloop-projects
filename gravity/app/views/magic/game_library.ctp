<?php
if($theirs)
{
	echo '<h2>Their Library</h2>';
}else{
	echo '<h2>Your Library</h2>';
}
echo '<div id="card_pool">';
echo $this->element('game_library');
echo '</div>';
?>
<style>
	div#card_pool div {
		float:left;
	}
</style>
<script type="text/javascript">
$(document).ready(function() {
    setInterval("refreshLibrary()",3000);
});

function returnCardToHand(deck_card_id)
{
	$.post('/magic/game_return_card_to_hand/<?php echo $game_id; ?>/'+deck_card_id, function(data) {
		if(data == 1) $('#DeckCard'+deck_card_id).remove();
	});
}

function refreshLibrary()
{ 
	$('#card_pool').load('/magic/game_refresh_library/<?php echo $game_id; ?>/<?php echo $theirs; ?>');
}
</script>