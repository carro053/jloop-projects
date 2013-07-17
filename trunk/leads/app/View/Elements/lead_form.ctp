<?php
	if(empty($lead))
		$lead = array();
	
	echo $this->Form->create('Lead', array('id' => 'LeadUpdateForm','url' => '/Leads/update'));
	echo $this->Form->input('id');
	echo $this->Form->input('email');
	echo $this->Form->input('phone');
	echo $this->Form->input('rating', array(
		'type' => 'radio',
		'options' => array(
			1 => 1,
			2 => 2,
			3 => 3,
			4 => 4,
			5 => 5
		),
		'legend' => false,
		'hiddenField' => false
	));
	echo $this->Form->end('Save');
?>
<script type="text/javascript">
	$('.radio').buttonset();
	$('#LeadUpdateForm').submit(function() {
		console.log('form submit');
		return false;
	});
</script>