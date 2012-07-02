<?php
class TwilioController extends AppController {

	public $name = "Twilio";
	
	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow("*");
	}
	
	public function index() {
		echo 123;
		die;
	}
}