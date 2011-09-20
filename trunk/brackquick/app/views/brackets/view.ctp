<style type="text/css">

tr.top-seed, tr.top-seed td {
	height:21px;
	margin:0px;
	padding:0px;
}
tr.bottom-seed, tr.bottom-seed td {
	height:21px;
	margin:0px;
	padding:0px;
}
table tr td {
	padding-top: 1px;
	height:20px;
	border: none;
}
table tr td.seed {
	text-align: center;
	width: 20px;
	color: #FFF;
	background-color: #E32;
}
table tr td.competitor {
	padding-left: 5px;
	height:21px;
	width: 135px;
	color: #FFF;
	background-color: #003D4C;
	overflow: hidden;
}
table tr td.competitor div {
	width: 135px;
	overflow: hidden;
}

table tr td.score {
	text-align: center;
	width: 20px;
	color: #000;
	background-color: #999;
}
table tr td.winner {
	text-align: center;
	width: 20px;
	color: #E32;
	background-color: teal;
}
.loser table tr td.competitor {
	width: 35px;
}
.loser table tr td.competitor div {
	width: 35px;
}

</style>
<h2><?php echo $tournament['Tournament']['name']; if($tournament['Tournament']['winner'] != '') echo ' - Winner: '.$tournament['Tournament']['winner']; ?></h2>
<?php $total_rounds = $tournament['Match'][0]['round'];
$letters = array();
$letter = 'a'; ?>
<div style="float:left;width:<?php if($tournament['Tournament']['type'] == 'Double Elimination') { echo (200*$total_rounds + 400); }else{ echo 200*$total_rounds; } ?>px;">
	<?php 
	$round = -2;
	foreach($tournament['Match'] as $match):
		if($round != ceil($match['round'])) {
			if($round != -2) echo '
	</div>';
			$round = $match['round'];
		 ?>
	<div style="width:200px;float:left;">
		<?php }
		if($match['round'] == -1)
		{
		$letters[1] = $letter.' If Needed';
		 ?>
		<div style="width:200px;">
			<div style="height:60px;padding-top:<?php echo ((pow(2,$total_rounds-2)*60)/pow(2,$match['round']) - 30); ?>px;padding-bottom:<?php echo ((pow(2,$total_rounds-2)*60)/pow(2,$match['round']) - 30); ?>px;width:180px;float:left;">
				<?php echo $this->element('bracket_match',array('match'=>$match,'tournament'=>$tournament,'letters'=>$letters)); ?>
			</div>
		</div>
		<?php }elseif($match['round'] == 0)
		{ ?>
		<div style="width:200px;">
			<div style="height:60px;padding-top:<?php echo ((pow(2,$total_rounds-2)*60)/pow(2,$match['round']-1) - 30); ?>px;padding-bottom:<?php echo ((pow(2,$total_rounds-2)*60)/pow(2,$match['round']-1) - 30); ?>px;width:180px;float:left;">
				<?php echo $this->element('bracket_match',array('match'=>$match,'tournament'=>$tournament,'letter'=>$letter)); ?>
			</div>
			<?php if($match['status'] != 'Finished' || $match['winner'] != 'top') { ?>
			<div style="width:20px;height:<?php echo ((pow(2,$total_rounds-2)*60)/pow(2,$match['round']-1)); ?>px;float:left;">
				<div style="border-style:none none solid none;border-width:2px;float:left; width:20px;height:<?php echo ((pow(2,$total_rounds-2)*60)/pow(2,$match['round']-1)); ?>px;"></div>
			</div>
			<?php } ?>
		</div>
		<?php }elseif($match['order'] > pow(2,$match['round']-1))
		{ ?>
		<?php 
		if($match['order'] == pow(2,ceil($match['round'])-1) + 1 && $match['round'] == ceil($match['round'])) echo '<div style="width:100px;float:left;padding-top:'.(ceil($match['round']-1)*30 + 30).'px;">';
		if($match['order'] == pow(2,ceil($match['round'])-1) + 1 && $match['round'] != ceil($match['round'])) echo '</div><div style="width:100px;float:left;padding-top:'.(ceil($match['round']-1)*30).'px;">'; ?>
		<div style="width:100px;" class="loser">
			<?php if($match['status'] != 'Bye') 
			{ ?>
				<div style="height:60px;padding-top:<?php echo ((pow(2,$total_rounds-2)*60)/pow(2,ceil($match['round'])) - 30); ?>px;padding-bottom:<?php echo ((pow(2,$total_rounds-2)*60)/pow(2,ceil($match['round'])) - 30); ?>px;width:80px;float:left;">
					<?php echo $this->element('bracket_match',array('match'=>$match,'tournament'=>$tournament,'letters'=>$letters));
					if(isset($letters[$match['top_internal_seed']])) unset($letters[$match['top_internal_seed']]); ?>
				</div>
				<?php if($match['round'] == ceil($match['round']))
				{ ?>
				<div style="width:20px;height:30px;margin-top:<?php echo (pow(2,$total_rounds-2)*60)/pow(2,ceil($match['round'])) - 30; ?>px;float:left;">
					<div style="border-style:none solid solid none;border-width:2px;height:30px;<?php echo (pow(2,$total_rounds-2)*60)/pow(2,ceil($match['round'])) - 30; ?>px;width:10px;float:left;"></div>
					<div style="border-style:solid none none none;border-width:2px;float:left; width:8px;"></div>
				</div>
				<?php }else{ ?>
				<div style="width:20px;height:<?php echo (pow(2,$total_rounds-1)*60)/pow(2,ceil($match['round'])); ?>px;float:left;">
					<?php if($match['order']%2 == 0) { ?>
					<div style="border-style:none solid solid none;border-width:2px;height:<?php echo (pow(2,$total_rounds-2)*60)/pow(2,ceil($match['round'])); ?>px;width:10px;float:left;"></div>
					<div style="border-style:solid none none none;border-width:2px;float:left; width:8px;"></div>
					<?php }else{ ?>
					<div style="border-style:solid solid none none;border-width:2px;height:<?php echo (pow(2,$total_rounds-2)*60)/pow(2,ceil($match['round'])); ?>px; position:relative;top:50%;width:10px;"></div>
					<div style="border-style:solid none none none;border-width:2px;float:left; width:10px;position:relative;top:<?php echo (pow(2,$total_rounds-2)*60)/pow(2,ceil($match['round'])) - 2; ?>px;left:10px;"></div>
					<?php } ?>
				</div>
				<?php } ?>
			<?php }else{ ?>
				<div style="height:60px;padding-top:<?php echo ((pow(2,$total_rounds-2)*60)/pow(2,ceil($match['round'])) - 30); ?>px;padding-bottom:<?php echo ((pow(2,$total_rounds-2)*60)/pow(2,ceil($match['round'])) - 30); ?>px;width:80px;float:left;">
				</div>
			<?php } ?>
		</div>
		<?php if($match['order'] == pow(2,ceil($match['round'])) && $match['round'] != ceil($match['round'])) echo '</div>'; ?>
		<?php }else{
		?>
		<div style="width:200px;">
			<?php if($match['status'] != 'Bye') 
			{ ?>
				<div style="height:60px;padding-top:<?php echo ((pow(2,$total_rounds-2)*60)/pow(2,$match['round']-1) - 30); ?>px;padding-bottom:<?php echo ((pow(2,$total_rounds-2)*60)/pow(2,$match['round']-1) - 30); ?>px;width:180px;float:left;">
					<?php echo $this->element('bracket_match',array('match'=>$match,'tournament'=>$tournament,'letter'=>$letter)); ?>
				</div>
				<?php if($match['round'] != 1 || $tournament['Tournament']['type'] == 'Double Elimination') { ?>
				<div style="width:20px;height:<?php echo (pow(2,$total_rounds-1)*60)/pow(2,$match['round']-1); ?>px;float:left;">
					<?php if($match['order']%2 == 0) { ?>
					<div style="border-style:none solid solid none;border-width:2px;height:<?php echo (pow(2,$total_rounds-2)*60)/pow(2,$match['round']-1); ?>px;width:10px;float:left;"></div>
					<div style="border-style:solid none none none;border-width:2px;float:left; width:8px;"></div>
					<?php }else{ ?>
					<div style="border-style:solid solid none none;border-width:2px;height:<?php echo (pow(2,$total_rounds-2)*60)/pow(2,$match['round']-1); ?>px; position:relative;top:50%;width:10px;"></div>
					<?php } ?>
				</div>
				<?php } ?>
			<?php }else{ ?>
				<div style="height:60px;padding-top:<?php echo ((pow(2,$total_rounds-2)*60)/pow(2,$match['round']-1) - 30); ?>px;padding-bottom:<?php echo ((pow(2,$total_rounds-2)*60)/pow(2,$match['round']-1) - 30); ?>px;width:200px;float:left;">
				</div>
			<?php } ?>
		</div>
		<?php 
			if($match['status'] != 'Bye')
			{
				$letters[$match['bottom_internal_seed']] = $letter;
				$letter++;
			}
		} ?>
	<?php endforeach; ?>
	</div>
</div>