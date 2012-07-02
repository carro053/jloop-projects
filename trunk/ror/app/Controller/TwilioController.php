<?php
class TwilioController extends AppController {

	public $name = 'Twilio';
	
	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow('*');
		App::import('Vendor', 'Twilio', array('file' => 'Twilio' . DS . 'Services' . DS . 'Twilio.php'));
	}
	
	public function index() {
		echo 123;
		die;
	}
}