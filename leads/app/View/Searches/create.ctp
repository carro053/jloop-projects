<h2>iTunes Search</h2>
<?php
	$years = array();
	for($i = date('Y'); $i > 2007; $i--)
		$years[$i] = $i;
	
	$months = array();
	$months['*'] = 'Any';
	for( $i = 1 ;  $i <= 12 ; $i++ )
		$months[gmdate( "M" , mktime( 0 , 0 , 0 , $i, 1 ) )] = gmdate( "M" , mktime( 0 , 0 , 0 , $i, 1 ) );
	
	$days = array();
	$days['*'] = 'Any';
	for($i = 1; $i <= 31; $i++)
		$days[$i] = $i;

	echo $this->Form->create('Search');
	echo $this->Form->input('search_terms', array('label' => 'Search Terms (e.x. automotive, fitness, etc.)'));
	echo $this->Form->input('is_not_iphone_5');
	echo $this->Form->input('is_not_ipad_only');
	echo $this->Form->input('use_date', array('onchange' => 'toggleDates(this);'));
	echo '<div id="date-range" style="display:none;">';
	echo $this->Form->input('year', array('class' => 'date-range', 'options' => $years));
	echo $this->Form->input('month', array('class' => 'date-range', 'options' => $months));
	echo $this->Form->input('day', array('class' => 'date-range', 'options' => $days));
	echo '</div>';
	
?>
<h3>Google Search Preview: <a id="previewLink" href="#" target="_blank"></a></h3>
<a href="/files/chrome_leadinator.zip">Download the Chrome Extension to scrape results right from the Google results page!</a>
<?	
	echo $this->Form->end('Search');
?>



<script type="text/javascript">
	ajax_google_search_preview();

	$("#SearchCreateForm").change(function() {
		ajax_google_search_preview();
	});
	$("#SearchCreateForm").keyup(function() {
		ajax_google_search_preview();
	});
	
	function ajax_google_search_preview() {
		$.post('/Searches/ajaxGetGoogleSearchPreviewLink',
			$('#SearchCreateForm').serialize(),
			function(data) {
				$('#previewLink').html(data);
				$('#previewLink').attr('href', data);
			}
		);
		return false;
	}

	function toggleDates(checkbox) {
		if($(checkbox).is(':checked'))
			$('#date-range').show();
		else
			$('#date-range').hide();
	}
</script>