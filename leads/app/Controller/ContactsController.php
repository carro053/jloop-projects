<?php
App::uses('AppController', 'Controller');

class ContactsController extends AppController {

	public function updateField() {
		pr($_POST);
		header('HTTP 400 Bad Request', true, 400);
        echo "This field is required!";
		die;
	}
	
}