<div class="container">
	<div class="copy">
		<h2>30 Latest Events Made</h2>
        <?php 
        $i = 0;
        $j = 0;
        $date = '';
        foreach($data as $event):
        	if($date != date('Y-m-d',strtotime($data[$i]['Event']['created'])))
        	{
        		echo '<h3>'.date('F jS',strtotime($data[$i]['Event']['created'])).'</h3>';
        	}
        	echo '<p>'.$event['Event']['name'].' - '.$event['Event']['when'].' - '.$event['Event']['where'].'</p>';
        	$date = date('Y-m-d',strtotime($data[$i]['Event']['created']));
        endforeach;
        ?> 
    </div><!-- end .copy -->
</div><!-- end .container -->
