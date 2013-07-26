<?php
App::uses('AppController', 'Controller');

class GroupsController extends AppController {

	public function index() {
		$groups = $this->Group->find('all');
		$this->set('groups', $groups);
	}
	
	public function addLeads() {
		$this->layout = false;
		if($this->request->is('post')) {
			$existing = $this->Group->findByName($this->request->data['Group']['name']);
			pr($existing);
			$group = array(
				'Group' => array(
					'id' => !empty($existing['Group']['id']) ? $existing['Group']['id'] : null,
					'name' => $this->request->data['Group']['name']
				)
			);
			foreach($existing['Lead'] as $lead) {
				$group['Lead'][] = $lead['id'];
			}
			foreach($this->request->data['Leads'] as $lead_id => $on) {
				$group['Lead'][] = $lead_id;
			}
			if($this->Group->saveAll($group))
				return $this->render('/Elements/form_success');
		}
		die('Only Post');
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