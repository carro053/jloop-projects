<?php
echo '<div id="spectate">';
echo $this->element('game_spectate');
echo '</div>';
 ?>
<style>
	div#mana_pool div {
		float:left;
	}
	
	div#mana_pool div {
		width: 112px;
		height: 156px;
		position:relative;
		z-index: 10;
		-moz-transform: scale(0.5);
		-webkit-transform: scale(0.5);
	}
	div#mana_pool div:hover {
		width: 112px;
		height: 156px;
		position:relative;
		z-index: 100;
		-moz-transform: scale(1);
		-webkit-transform: scale(1);
	}
	div#card_pool div {
		float:left;
	}
	
	div#card_pool div {
		width: 168px;
		height: 233px;
		position:relative;
		z-index: 10;
		-moz-transform: scale(0.75);
		-webkit-transform: scale(0.75);
	}
	div#card_pool div:hover {
		width: 168px;
		height: 233px;
		position:relative;
		z-index: 100;
		-moz-transform: scale(1);
		-webkit-transform: scale(1);
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