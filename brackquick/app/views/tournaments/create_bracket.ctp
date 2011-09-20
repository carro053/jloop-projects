<div style="float:left;">
<?php foreach($bracket as $round): ?>
<div style="width:100px;height:<?php echo pow(2,count($bracket)-1)*60; ?>px;float:right;">
<?php foreach($round as $match): ?>
<div style="height:60px;padding-top:<?php echo ((pow(2,count($bracket)-2)*60)/count($round) - 30); ?>px;padding-bottom:<?php echo ((pow(2,count($bracket)-2)*60)/count($round) - 30); ?>px;width:100px;">
<?php echo $match['top_seed'].'<br />vs<br />'.$match['bottom_seed']; ?>
</div>
<?php endforeach; ?>
</div>
<?php endforeach; ?>
</div>