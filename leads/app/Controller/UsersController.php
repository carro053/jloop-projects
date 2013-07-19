<?php
App::uses('AppController', 'Controller');

class UsersController extends AppController {

	public function beforeFilter() {
		parent::beforeFilter();
		//$this->Auth->allow('create');
	}
	
	public function index() {
		$users = $this->User->find('all');
		$this->set('users', $users);
	}
	
	public function create() {
		if($this->request->is('post')) {
			if($this->User->save($this->request->data)) {
				$this->Session->setFlash('User created');
				return $this->redirect('/Users/index');
			}
		}
	}
	
	public function login() {
		if($this->request->is('post')) {
			if($this->Auth->login()) {
				return $this->redirect($this->Auth->redirect());
			} else {
				$this->Session->setFlash('Invalid username or password, try again');
			}
		}
	}
	
	public function logout() {
		return $this->redirect($this->Auth->logout());
	}
}