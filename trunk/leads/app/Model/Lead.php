<?php
App::uses('AppModel', 'Model');

class Lead extends AppModel {

	public $validate = array(
		'email' => 'email'
	);
	
	public $hasMany = array(
		'Note' => array(
			'className' => 'Note',
			'foreignKey' => 'lead_id',
			'order' => 'Note.created DESC'
		)
	);
}
