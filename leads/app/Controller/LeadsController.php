<?php
App::uses('AppController', 'Controller');

class LeadsController extends AppController {

	public function gather() {
	}
	
	public function qualify() {
	}
	
	public function update() {
		$this->layout = false;
		if($this->request->is('post')) {
			pr($this->request->data);
			$this->render('/Elements/lead_form');
		}
		die('Only Post');
	}
}