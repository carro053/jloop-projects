<?php
App::uses('AppController', 'Controller');

class ContactsController extends AppController {
	
	public function updateField() {
		if(!empty($_POST['pk']) && !empty($_POST['name'])) {
			$this->Contact->id = $_POST['pk'];
			if(!$this->Contact->saveField($_POST['name'], $_POST['value'], false))
				throw new ForbiddenException();
		}
		die;
	}
	
	public function index() {
		$limit = (!empty($_GET['limit']) ? $_GET['limit'] : 50);
		$page = (!empty($_GET['page']) ? $_GET['page'] : 1);
		$order = (!empty($_GET['order']) ? $_GET['order'] : 'user_id').' '.(!empty($_GET['direction']) ? $_GET['direction'] : 'desc');
		
		$conditions = array();
		
		if(isset($_GET['user_id']) && $_GET['user_id'] != '') {
			$conditions[] = array('Contact.user_id' => $_GET['user_id']);
		}
		
		$this->Contact->bindModel(array(
			'belongsTo' => array(
				'User' => array(
					'className' => 'Lead',
					'foreignKey' => 'lead_id'
				)
			)
		));
		
		$contacts = $this->Contact->find('all', array(
			'conditions' => $conditions,
			'order' => $order,
			'page' => $page,
			'limit' => $limit
		));
		$this->set('contacts', $contacts);
		
		$count = $this->Contact->find('count', array('conditions' => $conditions));
		$this->set('count', $count);
		
		$users = array(
			'' => 'Any',
			'0' => 'Unassigned'
		);
		$users += $this->Contact->User->find('list', array(
			'fields' => array('User.id', 'User.username'),
			'order' => 'User.username ASC'
		));
		$this->set('users', $users);
	}
	
}