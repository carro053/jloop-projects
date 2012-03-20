<div class="container">
	<div class="copy">
		<h2>30 Latest Events Made</h2>
		<p>
        <?php 
        $i = 0;
        $j = 0;
        $date = '';
        foreach($data as $event):
        	if($date != date('Y-m-d',strtotime($data[$i]['Event']['created'])))
        	{
        		echo '
        		</p>
        		<h3>'.date('F jS',strtotime($data[$i]['Event']['created'])).'</h3>
        		<p>';
        	}
        	echo $event['Event']['name'].' - '.date('F jS',strtotime($event['Event']['date'])).' '.$event['Event']['when'].' - '.$event['Event']['where'].'<br />';
        	$date = date('Y-m-d',strtotime($data[$i]['Event']['created']));
        endforeach;
        ?>
        </p>
    </div><!-- end .copy -->
</div><!-- end .container -->
