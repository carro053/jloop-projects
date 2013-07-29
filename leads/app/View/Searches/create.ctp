<h2>iTunes Search</h2>
<?php
	echo $this->Form->create('Search');
	echo $this->Form->input('search_terms', array('label' => 'Search Terms (e.x. automotive, fitness, etc.)'));
	echo $this->Form->input('is_not_iphone_5');
	echo $this->Form->input('is_not_ipad_only');
	echo $this->Form->input('use_date_range', array('onchange' => 'toggleDates(this);'));
	echo '<div id="date-range" style="display:none;">';
	echo $this->Form->input('start_date', array('class' => 'date-range'));
	echo $this->Form->input('end_date', array('class' => 'date-range'));
	echo '</div>';
	
?>
<h3>Google Search Preview: <a id="previewLink" href="#" target="_blank"></a></h3>
<a href="/files/chrome_leadinator.zip" style="background-color: red;">Download the Chrome Extension to scrape results right from the Google results page!</a>
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