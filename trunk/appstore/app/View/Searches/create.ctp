<?php
	$this->Form->create('Search');
	$this->Form->input('not_iphone_5', array('type' => 'checkbox'));
	$this->Form->input('not_ipad', array('type' => 'checkbox'));
	$this->Form->input('custom_terms');
	$this->Form->end('Submit');