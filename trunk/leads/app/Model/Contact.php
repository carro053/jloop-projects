<?php
App::uses('AppModel', 'Model');

class Contact extends AppModel {

	public $belongsTo = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id'
		)
	);
	
}
