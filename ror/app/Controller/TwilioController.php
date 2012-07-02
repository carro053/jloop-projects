<?php
class TwilioController extends AppController {

	public $name = "TwilioController";
	
	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow("*");
	}
	
	public function index() {
		echo 123;
		die;
	}
}