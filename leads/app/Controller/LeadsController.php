<?php
App::uses('AppController', 'Controller');

class LeadsController extends AppController {

	var $uses = array('Lead','Note');

	public function index() {
		$limit = (!empty($_GET['limit']) ? $_GET['limit'] : 50);
		$page = (!empty($_GET['page']) ? $_GET['page'] : 1);
		
		$conditions = array();
		$conditions[] = 'Lead.status > 0';
		
		//search conditions
		/*
		if(!empty($_GET['category'])) {
			$conditions['Lead.category'] = $_GET['category'];
		}
		*/
		
		$leads = $this->Lead->find('all', array(
			'conditions' => $conditions,
			'page' => $page,
			'limit' => $limit
		));
		$this->set('leads', $leads);
		
		$count = $this->Lead->find('count', array('conditions' => $conditions));
		$this->set('count', $count);
	}

	public function gather() {
	}
	
	public function qualify() {
	}
	
	public function update() {
		$this->layout = false;
		if($this->request->is('post')) {
			if(!empty($this->request->data['Lead']['id'])) {
				if($this->Lead->save($this->request->data)) {
					return $this->render('/Elements/lead_form_success');
				}
			}
			return $this->render('/Elements/lead_form');
		}
		die('Only Post');
	}
	
	public function addNote() {
		$this->layout = false;
		if($this->request->is('post')) {
			if($this->Note->save($this->request->data)) {
				echo $this->request->data['Note']['text'];
			} else {
				echo 'error';
			}
			exit;
		}
		die('Only Post');
	}
}
