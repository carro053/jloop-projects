<?php 
if($game['MagicGame']['winner_id'] == 1)
{
	echo '<h2>'.$game['User_1']['username'].' Won</h2>';
}elseif($game['MagicGame']['winner_id'] == 2)
{
	echo '<h2>'.$game['User_2']['username'].' Won</h2>';
}
echo '<h2>'.str_replace('s\'s','s\'',$game['User_1']['username'].'\'s').' Field | HP:'.$game['MagicGame']['user_1_hp'].'</h2>';
echo '<div id="mana_pool">';
foreach($player_1_mana as $deck_card):
	echo '<div><img';
	if($deck_card['MagicGameDeckCard']['tapped']) echo ' style="opacity:0.4;"';
	echo ' src="/files/magic_cards/'.$deck_card['MagicGameDeckCard']['card_id'].'.jpg" /></div>';
endforeach;
echo '</div>';
echo '<div style="clear:both;height:10px;">&nbsp;</div>';
echo '<div id="card_pool">';
foreach($player_1_cards as $deck_card):
	echo '<div><img';
	if($deck_card['MagicGameDeckCard']['tapped']) echo ' style="opacity:0.4;"';
	echo ' src="/files/magic_cards/'.$deck_card['MagicGameDeckCard']['card_id'].'.jpg" /></div>';
endforeach;
echo '</div>';
echo '<div style="clear:both;height:50px;">&nbsp;</div>';
if($game['MagicGame']['turn'] == 1)
{
	echo '<h2>'.str_replace('s\'s','s\'',$game['User_2']['username'].'\'s').' Turn</h2>';
}else{
	echo '<h2>'.str_replace('s\'s','s\'',$game['User_1']['username'].'\'s').' Turn</h2>';	
}
echo '<h2>'.str_replace('s\'s','s\'',$game['User_2']['username'].'\'s').' Field | HP:'.$game['MagicGame']['user_2_hp'].'</h2>';
echo '<div id="card_pool">';
foreach($player_2_cards as $deck_card):
	echo '<div><img';
	if($deck_card['MagicGameDeckCard']['tapped']) echo ' style="opacity:0.4;"';
	echo ' src="/files/magic_cards/'.$deck_card['MagicGameDeckCard']['card_id'].'.jpg" /></div>';
endforeach;
echo '</div>';
echo '<div style="clear:both;height:10px;">&nbsp;</div>';
echo '<div id="mana_pool">';
foreach($player_2_mana as $deck_card):
	echo '<div><img';
	if($deck_card['MagicGameDeckCard']['tapped']) echo ' style="opacity:0.4;"';
	echo ' src="/files/magic_cards/'.$deck_card['MagicGameDeckCard']['card_id'].'.jpg" /></div>';
endforeach;
echo '</div>';
echo '<div style="clear:both;height:50px;">&nbsp;</div>';
?>