<?php
App::uses('AppController', 'Controller');

class GroupsController extends AppController {

	public function index() {
		$groups = $this->Group->find('all');
		$this->set('groups', $groups);
	}
	
	public function addLeads() {
		if($this->request->is('post')) {
			pr($this->request->data);
		}
		die;
	}
	
	/*public function create() {
		if($this->request->is('post')) {
			if($this->Tag->save($this->request->data)) {
				$this->Session->setFlash('Tag created');
				return $this->redirect('/Tags/index');
			}
		}
	}
	
	public function update($id) {
		if($this->request->is('post') || $this->request->is('put')) {
			if($this->Tag->save($this->request->data)) {
				$this->Session->setFlash('Tag updated');
				return $this->redirect('/Tags/index');
			}
		}else{
			$this->request->data = $this->Tag->findById($id);
		}
	}*/
}