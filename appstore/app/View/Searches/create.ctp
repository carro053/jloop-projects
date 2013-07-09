<?php
	echo $this->Form->create('Search');
	echo $this->Form->input('not_iphone_5', array('type' => 'checkbox'));
	echo $this->Form->input('not_ipad', array('type' => 'checkbox'));
	echo $this->Form->input('custom_terms');
	echo $this->Form->end('Submit');