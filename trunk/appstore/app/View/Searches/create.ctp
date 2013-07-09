<?php
	echo $this->Form->create('Search');
	echo $this->Form->input('search_terms');
	echo $this->Form->input('is_not_iphone_5', array('type' => 'checkbox'));
	echo $this->Form->input('is_not_ipad_only', array('type' => 'checkbox'));
	echo $this->Form->input('start_date');
	echo $this->Form->input('end_date');
	echo $this->Form->end('Submit');