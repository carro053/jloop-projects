<?php
App::uses('AppModel', 'Model');

class Adage extends AppModel {

	public $belongsTo = array('Lead');
	
}