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
			$group = array(
				'Group' => array(
					'id' => !empty($existing['Group']['id']) ? $existing['Group']['id'] : null,
					'name' => $this->request->data['Group']['name']
				)
			);
			if(!empty($existing['Lead'])) {
				foreach($existing['Lead'] as $lead) {
					$group['Lead'][] = $lead['id'];
				}	
			}
			foreach($this->request->data['Leads'] as $lead_id => $on) {
				$group['Lead'][] = $lead_id;
			}
			if($this->Group->saveAll($group))
				return $this->render('/Elements/form_success');
		}
		die('Only Post');
	}
	
	public function getJSON() {
		$groups_raw = $this->Group->find('list');
		$groups = array();
		foreach($groups_raw as $group) {
			$groups[] = $group;
		}
		die(json_encode($groups));
	}
	
	public function removeLead($groups_leads_id) {
		$this->loadModel('GroupsLead');
		if($this->GroupsLead->delete($groups_leads_id)) {
			echo 1;
			die();
		}
		die('Error');
	}
	
	public function delete($id) {
		$group = $this->Group->findById($id);
		pr($group);
		die;
		/*if($this->Group->delete($id)) {
			foreach($group['Lead'] as $lead) {
				
			}
		}*/
		$this->Session->setFlash('Group Deleted');
		return $this->redirect('/Groups/index');
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