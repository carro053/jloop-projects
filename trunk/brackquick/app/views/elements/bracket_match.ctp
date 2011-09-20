<div style="height:42px;padding-top:9px;">
<table style="height:42px;" cellpadding="0" cellspacing="0">
	<tr class="top-seed">
		<td class="seed">
			<?php if($match['top_user'] != '') echo $match['top_seed']; ?>
		</td>
		<td class="competitor">
			<div><?php if($match['top_user'] != '')
			{
				echo $match['top_user'];
			}elseif(isset($letters[$match['top_internal_seed']]))
			{
				echo '<i>'.strtoupper($letters[$match['top_internal_seed']]).'</i>';
			} ?></div>
		</td>
		<td class="score<?php if($match['status'] == 'Finished' && $match['winner'] == 'top') echo ' winner'; ?>">
			<?php if($match['status'] == 'Finished')
			{
				echo $match['top_score'];
			}elseif($match['status'] == 'Playing')
			{
				echo '<a href="/matches/submit_scores/'.$tournament['Tournament']['hash'].'/'.$match['id'].'">?</a>';
			} ?>
		</td>
	</tr>
	<tr class="bottom-seed">
		<td class="seed">
			<?php if($match['bottom_user'] != '') echo $match['bottom_seed']; ?>
		</td>
		<td class="competitor">
			<div><?php if($match['bottom_user'] != '')
			{
				echo $match['bottom_user'];
			}elseif(isset($letters[$match['bottom_internal_seed']]))
			{
				echo '<i>'.strtoupper($letters[$match['bottom_internal_seed']]).'</i>';
			} ?></div>
		</td>
		<td class="score<?php if($match['status'] == 'Finished' && $match['winner'] == 'bottom') echo ' winner'; ?>">
			<?php if($match['status'] == 'Finished')
			{
				echo $match['bottom_score'];
			}elseif($match['status'] == 'Playing')
			{
				echo '<a href="/matches/submit_scores/'.$tournament['Tournament']['hash'].'/'.$match['id'].'">?</a>';
			} ?>
		</td>
	</tr>
</table>
<?php if($match['status'] != 'Finished' && $tournament['Tournament']['type'] == 'Double Elimination' && $match['order'] <= pow(2,$match['round']-1) && $match['round'] != -1) { ?>
<div style="position:relative;top:-40px;right:23px;color:#000;background-color:#FFF; width:20px;text-align:center;border-style:solid;border-width:1px;"><?php echo strtoupper($letter); ?></div>
<?php } ?>
</div>