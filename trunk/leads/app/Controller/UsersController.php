<?php
App::uses('AppController', 'Controller');

class UsersController extends AppController {
	
	public function index() {
		$users = $this->User->find('all');
		pr($users);
		
	}
	
	public function create() {
		if($this->request->is('post')) {
			if($this->User->save($this->request->data)) {
				return $this->redirect('/Users/index');
			}
		}
	}
	
	public function login() {
		
	}
	
	public function logout() {
		
	}
}