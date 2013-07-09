<?php
	echo $this->Form->create('Search');
	echo $this->Form->input('search_terms');
	echo $this->Form->input('is_not_iphone_5');
	echo $this->Form->input('is_not_ipad_only');
	echo $this->Form->input('use_date_range', array('onchange' => 'toggleDates(this.value);');
	echo $this->Form->input('start_date', array('class' => 'date-range', 'style' => 'display:none;'));
	echo $this->Form->input('end_date', array('class' => 'date-range', 'style' => 'display:none;'));
	echo $this->Form->end('Submit');
?>

<script type="text/javascript">
	function toggleDates(checked) {
		if(checked)
			$('.date-range').show();
		else
			$('.date-range').hide();
	}
</script>