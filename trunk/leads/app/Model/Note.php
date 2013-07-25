<?php
App::uses('AppModel', 'Model');

class Note extends AppModel {

	public $belongsTo = array('User');
}