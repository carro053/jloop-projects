<?php
	if(!empty($scrape)) {
		echo '<h3>Data</h3>';
		echo '<pre>';
		print_r($scrape);
		echo '</pre>';
	}
	
	echo $this->Form->create();
	echo $this->Form->input('URL', array('label' => 'URL'));
	echo $this->Form->end('Scrape');
?>

<h3>Examples</h3>
<input value="https://itunes.apple.com/us/app/tortilla-soup-surfer/id476450448?mt=8" onclick="this.select()" />
<input value="https://itunes.apple.com/us/app/strava-run-gps-running-training/id488914018?mt=8" onclick="this.select()" />
<input value="https://itunes.apple.com/us/app/hay-day/id506627515?mt=8" onclick="this.select()" />
<input value="https://itunes.apple.com/us/app/infinity-blade-ii/id447689011?mt=8" onclick="this.select()" />
<p>Hello</p>