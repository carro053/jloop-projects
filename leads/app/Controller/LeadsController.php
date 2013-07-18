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
			if(!empty($this->request->data['Lead']['id'])) {
				if($this->Lead->save($this->request->data)) {
					die('1');
				}
			}
			return $this->render('/Elements/lead_form');
		}
		die('Only Post');
	}
}
