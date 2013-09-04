<?php
App::uses('AppController', 'Controller');

class ContactsController extends AppController {

	public function updateField() {
		pr($_POST);
		throw new NotFoundException();
		die;
	}
	
}