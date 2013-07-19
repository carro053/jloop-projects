<?php
App::uses('AppController', 'Controller');

class ScrapesController extends AppController {

	public $uses = array('Scrape');
	
	public function index() {
		$limit = (!empty($_GET['limit']) ? $_GET['limit'] : 50);
		$page = (!empty($_GET['page']) ? $_GET['page'] : 1);
		
		$conditions = array('Lead.status' => 0);
		
		//search conditions
		//$conditions['OR']['Project.location LIKE'] = '%'.$search.'%';
		if(!empty($_GET['category'])) {
			$conditions['Scrape.category'] = $_GET['category'];
		}
		
		$scrapes = $this->Scrape->find('all', array(
			'conditions' => $conditions,
			'page' => $page,
			'limit' => $limit
		));
		$this->set('scrapes', $scrapes);
		
		$count = $this->Scrape->find('count', array('conditions' => $conditions));
		$this->set('count', $count);
		
		$categories_raw = $this->Scrape->find('all', array(
			'fields' => array(
				'DISTINCT category'
			)
		));
		$categories = array('' => 'Any');
		foreach($categories_raw as $category) {
			$categories[$category['Scrape']['category']] = $category['Scrape']['category'];
		}
		$this->set('categories', $categories);
		
	}
	
	public function view($id) {
		$this->layout = false;
		$scrape = $this->Scrape->findById($id);
		$this->set('scrape', $scrape);
	}
	
	public function test() {
		if($this->request->is('post')) {
			$scrape = $this->Scrape->parseURL($this->request->data['Scrape']['URL']);
			$this->set('scrape', $scrape);
		}
	}
}