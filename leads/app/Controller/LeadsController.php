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
		if(!empty($_GET['type'])) {
			$conditions['Lead.model'] = $_GET['type'];
		}
		if(!empty($_GET['search'])) {
			$conditions['OR']['Lead.name LIKE'] = '%'.$_GET['search'].'%';
			$conditions['OR']['Lead.email LIKE'] = '%'.$_GET['search'].'%';
		}
		
		$leads = $this->Lead->find('all', array(
			'conditions' => $conditions,
			'page' => $page,
			'limit' => $limit,
			'recursive' => -1
		));
		$this->set('leads', $leads);
		
		$count = $this->Lead->find('count', array('conditions' => $conditions));
		$this->set('count', $count);
		
		$types_raw = $this->Lead->find('all', array(
			'fields' => array(
				'DISTINCT model'
			)
		));
		$types = array('' => 'Any');
		foreach($types_raw as $type) {
			$types[$type['Lead']['model']] = $type['Lead']['model'];
		}
		$this->set('types', $types);
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
				$note = $this->Note->read();
				echo json_encode($note['Note']);
			} else {
				echo 'error';
			}
			exit;
		}
		die('Only Post');
	}
}
