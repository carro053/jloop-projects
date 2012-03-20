<div class="container">
	<div class="copy">
        <h2>Today's Events</h2>
        <p><?php 
        $i = 0;
        $j = 0;
        $date = date('Y-m-d');
        while($date == date('Y-m-d'))
        {
        	if(date('Y-m-d') == $data[$j]['Event']['created'])
        	{
        		if($i != 0) echo '<br />';
        		echo $data[$j]['Event']['name'].' - '.$data[$j]['Event']['when'].' - '.$data[$j]['Event']['where'];
        		$i++;
        		$j++;
        	}	
        	$date = date('Y-m-d',strtotime($data[$i]['Event']['created']));
        }
        if($i==0) echo 'No events have been made today yet.';
        $i=0;
        echo "</p><h2>Yesterday's Events</h2><p>";
        while($date == date('Y-m-d',mktime(0,0,0,date('m'),date('d')-1,date('Y'))))
        {
        	if(date('Y-m-d',mktime(0,0,0,date('m'),date('d')-1,date('Y'))) == $data[$j]['Event']['created'])
        	{
        		if($i != 0) echo '<br />';
        		echo $data[$j]['Event']['name'].' - '.$data[$j]['Event']['when'].' - '.$data[$j]['Event']['where'];
        		$i++;
        		$j++;
        	}	
        	$date = date('Y-m-d',strtotime($data[$i]['Event']['created']));
        }
        if($i==0) echo 'No events were made yesterday.';
        $i=0;
        echo "</p><h2>Last Week's Events</h2><p>";
        while($j < count($data))
        {
        	if($i != 0) echo '<br />';
        	echo $data[$j]['Event']['created'].' | '.$data[$j]['Event']['name'].' - '.$data[$j]['Event']['when'].' - '.$data[$j]['Event']['where'];
        	$i++;
        	$j++;
        }
        if($i==0) echo 'No events were last week.'
        ?> 
        </p>
    </div><!-- end .copy -->
</div><!-- end .container -->
