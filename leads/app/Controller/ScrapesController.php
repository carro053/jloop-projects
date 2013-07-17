<?php
App::uses('AppController', 'Controller');

class ScrapesController extends AppController {

	public $uses = array('Scrape');
	
	public function index() {
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
		
		$conditions = array();
		$scarpes = $this->Scrape->find('all', array(
			'conditions' => $conditions,
		));
		$this->set('scarpes', $scarpes);
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
