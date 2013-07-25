<?php
App::uses('AppModel', 'Model');

class Group extends AppModel {

	public $validate = array(
		'name' => 'notEmpty'
	);
	
	public $hasAndBelongsToMany = array(
		'Lead' => array(
			'unique' => false/*'keepExisting'*/
		)
	);
}
