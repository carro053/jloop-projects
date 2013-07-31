<h2>Update Tag</h2>
<?php
	echo $this->Form->create('Tag');
	echo $this->Form->input('id');
	echo $this->Form->input('name');
	echo $this->Form->input('filter_default', array(
		'options' => array(
			'None' => 'None',
			'Included' => 'Included',
			'Excluded' => 'Excluded'
		)
	));
	echo $this->Form->end('Save');