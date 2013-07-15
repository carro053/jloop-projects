<?php
App::uses('AppController', 'Controller');

class ScrapesController extends AppController {

	public $uses = array('Scrape');
	
	public function index() {
		$categories = $this->Scrape->find('list', array(
			'fields' => array(
				'DISTINCT category'
			)
		));
		pr($categories);
	}
	
	public function test() {
		if($this->request->is('post')) {
			$scrape = $this->Scrape->parseURL($this->request->data['Scrape']['URL']);
			$this->set('scrape', $scrape);
		}
	}
}
