<?php
App::uses('AppModel', 'Model');

class Lead extends AppModel {

	public $validate = array(
		//'email' => 'email'
	);
	
	public $hasMany = array(
		'Contact' => array(
			'className' => 'Contact',
			'foreignKey' => 'lead_id'
		),
		'Note' => array(
			'className' => 'Note',
			'foreignKey' => 'lead_id',
			'order' => 'Note.created DESC'
		)
	);
	
	public $hasAndBelongsToMany = array(
		'Tag',
		'Group'
	);
}
