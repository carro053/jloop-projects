<?php
App::uses('AppModel', 'Model');

class Lead extends AppModel {

	public $validate = array(
		'email' => 'email'
	);
}
