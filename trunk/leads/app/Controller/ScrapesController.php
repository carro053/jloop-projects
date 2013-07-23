<?php
App::uses('AppController', 'Controller');

class ScrapesController extends AppController {

	public $uses = array('Scrape');
	
	public function index() {
		$limit = (!empty($_GET['limit']) ? $_GET['limit'] : 50);
		$page = (!empty($_GET['page']) ? $_GET['page'] : 1);
		$order = (!empty($_GET['order']) ? $_GET['order'] : 'Scrape.name').' '.(!empty($_GET['direction']) ? $_GET['direction'] : 'asc');
		
		$conditions = array('Lead.status' => 0);
		
		//search conditions
		if(!empty($_GET['type'])) {
			$types = array(
				'manual' => 'Manual iTunes Scrape',
				'auto' => 'Google Search Automatic iTunes Scrape'
			);
			if(!empty($types[$_GET['type']]))
				$conditions['Lead.type'] =  $types[$_GET['type']];
		}
		if(!empty($_GET['category'])) {
			$conditions['Scrape.category'] = $_GET['category'];
		}
		/*if(!empty($_GET['released_updated'])) {
			$conditions['Scrape.released_updated <'] = $_GET['released_updated']['year'].'-'.$_GET['released_updated']['month'].'-'.$_GET['released_updated']['day'];
		}*/
		if(!empty($_GET['ratings_all'])) {
			$conditions['Scrape.ratings_all >='] = $_GET['ratings_all'];
		}
		if(!empty($_GET['ratings_all_count'])) {
			$conditions['Scrape.ratings_all_count >='] = $_GET['ratings_all_count'];
		}
		if(!empty($_GET['iphone5'])) {
			if($_GET['iphone5'] == 'yes')
				$conditions['Scrape.requirements LIKE'] = '%This app is optimized for iPhone 5.%';
			if($_GET['iphone5'] == 'no')
				$conditions['Scrape.requirements NOT LIKE'] = '%This app is optimized for iPhone 5.%';
		}
		if(!empty($_GET['search'])) {
			$conditions['OR']['Scrape.name LIKE'] = '%'.$_GET['search'].'%';
			$conditions['OR']['Scrape.seller LIKE'] = '%'.$_GET['search'].'%';
			$conditions['OR']['Scrape.copyright LIKE'] = '%'.$_GET['search'].'%';
			$conditions['OR']['Scrape.description LIKE'] = '%'.$_GET['search'].'%';
		}
		$scrapes = $this->Scrape->find('all', array(
			'conditions' => $conditions,
			'order' => $order,
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
		$scrape = $this->Scrape->find('first', array('conditions' => 'Scrape.id = '.$id, 'recursive' => 2));
		$this->set('scrape', $scrape);
	}
	
	public function create() {
		if($this->request->is('post')) {
			$itunes_id = $this->Scrape->parseItunesId($this->request->data['Scrape']['itunes_link']);
			$check = $this->Scrape->findByItunesId($itunes_id);
	        if(empty($check)) {
		        $this->Scrape->create();
		        $scrape = $this->Scrape->parseURL($this->request->data['Scrape']['itunes_link']);
		        $scrape['Lead']['type'] = 'Manual iTunes Scrape';
		        if(!empty($scrape['itunes_link']) && $this->Scrape->save($scrape)) {
			        $this->Session->setFlash('Link has been successfully scraped.');
		        }else{
			        $this->Session->setFlash('Link was not a valid iTunes link.');
		        }
	        }else{
	        	$this->Session->setFlash('Link has previously been scraped already.');
	        }
		}
	}
	
	public function test() {
		if($this->request->is('post')) {
			$scrape = $this->Scrape->parseURL($this->request->data['Scrape']['URL']);
			$this->set('scrape', $scrape);
		}
	}
}
