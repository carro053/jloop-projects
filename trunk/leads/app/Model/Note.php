<?php
App::uses('AppModel', 'Model');

class Note extends AppModel {

	public $belongsTo = array('User');
	
	public $validate = array(
		'text' => array(
			array(
				'rule' => 'notEmpty',
				'required' => false,
				'allowEmpty' => true
			)
		)
	);
	
}