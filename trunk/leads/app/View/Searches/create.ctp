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
	echo $this->Form->end('Search');
?>

<a href="#" onclick="ajax_google_search_preview();">Preview</a>
<h3>Google Search Preview: <a id="previewLink" href="#" target="_blank"></a></h3>

<script type="text/javascript">
	/*
	$('#SearchCreateForm').change( function() {
		ajax_google_search_preview();
	});
	
	$('#SearchCreateForm').keyup( function() {
		ajax_google_search_preview();
	});
	*/

	function ajax_google_search_preview() {
		console.log('ajax preview');
		$.post('/Searches/ajaxGetGoogleSearchPreviewLink',
			$('#SearchCreateForm').serialize(),
			function(data) {
				console.log(data);
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