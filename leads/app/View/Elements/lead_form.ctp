<?php
	if(empty($lead))
		$lead = array();
	
	echo $this->Form->create('Lead', array('url' => '/Leads/update'));
	echo $this->Form->input('id');
	echo $this->Form->input('email');
	echo $this->Form->input('phone');
	echo $this->Form->radio('rating', array(
		1 => 1,
		2 => 2,
		3 => 3,
		4 => 4,
		5 => 5
	));
	echo $this->Form->input('rating2', array(
		'type' => 'radio',
		'options' => array(
			1 => 1,
			2 => 2,
			3 => 3,
			4 => 4,
			5 => 5
		)
	));
	echo $this->Form->end('Save');
?>