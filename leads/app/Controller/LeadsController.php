<?php
App::uses('AppController', 'Controller');

class LeadsController extends AppController {

	var $uses = array('Lead','Note');

	public function index() {
		$types_raw = $this->Lead->find('all', array(
			'fields' => array(
				'DISTINCT type'
			)
		));
		$types = array('' => 'Any');
		foreach($types_raw as $type) {
			$types[$type['Lead']['type']] = $type['Lead']['type'];
		}
		$this->set('types', $types);
		
		$groups = array('' => 'New');
		$groups += $this->Lead->Group->find('list');
		$this->set('groups', $groups);
		
		$tags = $this->Lead->Tag->find('all');
		$this->set('tags', $tags);
		
		if(!isset($_GET['IncludeTag']) && !isset($_GET['form'])) {
			foreach($tags as $tag) {
				if($tag['Tag']['filter_default'] == 'Included')
					$_GET['IncludeTag'][] = $tag['Tag']['id'];
			}
		}
		if(!isset($_GET['ExcludeTag']) && !isset($_GET['form'])) {
			foreach($tags as $tag) {
				if($tag['Tag']['filter_default'] == 'Excluded')
					$_GET['ExcludeTag'][] = $tag['Tag']['id'];
			}
		}
		
		$limit = (!empty($_GET['limit']) ? $_GET['limit'] : 50);
		$page = (!empty($_GET['page']) ? $_GET['page'] : 1);
		$order = (!empty($_GET['order']) ? $_GET['order'] : 'rating').' '.(!empty($_GET['direction']) ? $_GET['direction'] : 'desc');
		
		$conditions = array();
		$conditions[] = 'Lead.status > 0';
		
		//search conditions
		if(!empty($_GET['type'])) {
			$conditions['Lead.type'] = $_GET['type'];
		}
		if(!empty($_GET['search'])) {
			$conditions['OR']['Lead.name LIKE'] = '%'.$_GET['search'].'%';
			$conditions['OR']['Lead.email LIKE'] = '%'.$_GET['search'].'%';
			$conditions['OR']['Lead.twitter LIKE'] = '%'.$_GET['search'].'%';
			$conditions['OR']['Lead.facebook LIKE'] = '%'.$_GET['search'].'%';
		}
		if(!empty($_GET['IncludeTag'])) {
			$conditions[] = array('Lead.id IN (SELECT `lead_id` from `leads_tags` WHERE `tag_id` IN ('.implode(',', $_GET['IncludeTag']).'))');
		}
		if(!empty($_GET['ExcludeTag'])) {
			$conditions[] = array('Lead.id NOT IN (SELECT `lead_id` from `leads_tags` WHERE `tag_id` IN ('.implode(',', $_GET['ExcludeTag']).'))');
		}
		
		$leads = $this->Lead->find('all', array(
			'conditions' => $conditions,
			'order' => $order,
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
				if($this->Lead->saveAll($this->request->data)) {
					if(isset($this->request->data['Lead']['is_chrome_extension']))
						die('1');
					return $this->render('/Elements/form_success');
				}
			}
			return $this->render('/Elements/lead_form');
		}
		die('Only Post');
	}
	
	/*
	public function addNote() {
		$this->layout = false;
		if($this->request->is('post')) {
			$user = $this->Auth->user();
			$this->request->data['Note']['user_id'] = $user['id'];
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
	*/
}
