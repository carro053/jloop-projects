<h2>Manual Itunes Scrape</h2>
<a href="/files/chrome_leadinator.crx">Download the Chrome Extension to scrape results right from the Google results page!</a>
<?php
	echo $this->Form->create('Scrape');
	echo $this->Form->input('itunes_link');
	echo $this->Form->end('Scrape');