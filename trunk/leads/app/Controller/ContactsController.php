<?php
App::uses('AppController', 'Controller');

class ContactsController extends AppController {

	public function updateField() {
		pr($_POST);
		header(null, null, 508);
        echo "This field is required!";
		die;
	}
	
}