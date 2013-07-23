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

<h3>Google Search Preview: <span></span></h3>

<script type="text/javascript">
	$('#SearchCreateForm').change( function() {
		console.log('form changed');
	});
	
	$('#SearchCreateForm').keyup( function() {
		console.log('form changed');
	});


	function toggleDates(checkbox) {
		if($(checkbox).is(':checked'))
			$('#date-range').show();
		else
			$('#date-range').hide();
	}
</script>