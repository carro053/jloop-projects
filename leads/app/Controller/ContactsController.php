<?php
App::uses('AppController', 'Controller');

class ContactsController extends AppController {

	public function updateField() {
		if(!empty($_POST['pk']) && !empty($_POST['name'])) {
			$this->Contact->id = $_POST['pk'];
			if(!$this->Contact->saveField($_POST['name'], $_POST['value'], false))
				throw new ForbiddenException();
			
		}
		die;
	}
	
}