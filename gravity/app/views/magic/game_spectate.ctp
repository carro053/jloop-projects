<?php
echo '<div id="spectate">';
echo $this->element('game_spectate');
echo '</div>';
 ?>
<style>
	div#card_pool div {
		float:left;
	}
</style>
<script type="text/javascript">
$(document).ready(function() {
    setInterval("refreshSpectate()",3000);
});

function refreshSpectate()
{ 
	$('#spectate').load('/magic/game_refresh_spectate/<?php echo $game['MagicGame']['id']; ?>');
}
</script>