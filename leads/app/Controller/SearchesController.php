<?php
App::uses('AppController', 'Controller');

class SearchesController extends AppController {

	public $uses = array('Search','Result');
	
	public function index() {
		$this->Search->unbindModel(array('hasMany' => array('Result')));
		$this->set('searches', $this->Search->find('all', array('order' => 'Search.created DESC')));
	}
		
	public function create() {
		if ($this->request->is('post')) {
			App::import('Vendor', 'google-api/Google_Client');
			App::import('Vendor', 'google-api/contrib/Google_CustomsearchService');
			
			session_start();
			$client = new Google_Client();
			$client->setApplicationName('App Finder');
			// Docs: http://code.google.com/apis/customsearch/v1/using_rest.html
			// Visit https://code.google.com/apis/console?api=customsearch to generate
			// your developer key (simple api key).
			$client->setDeveloperKey('AIzaSyBeJoG_O5wa2aAqAsWihNnCLmckfDB6kNQ');
			$search = new Google_CustomsearchService($client);
			
			
			/*
			// Example executing a search with your custom search id.
			$query = 'site:itunes.apple.com/us "Open iTunes to buy and download apps." '; //search US store, only iOS apps (not Mac apps or music, etc.)
			
			if($this->request->data['Search']['is_not_iphone_5'])
				$query .= '-"This app is optimized for iPhone 5." ';
			if($this->request->data['Search']['is_not_ipad_only'])
				$query .= '-"Compatible with iPad." ';
			if($this->request->data['Search']['use_date_range']) {
				$query .= 'daterange:'.$this->request->data['Search']['start_date']['year'].'-'.$this->request->data['Search']['start_date']['month'].'-'.$this->request->data['Search']['start_date']['day'].'..'.$this->request->data['Search']['end_date']['year'].'-'.$this->request->data['Search']['end_date']['month'].'-'.$this->request->data['Search']['end_date']['day'].' ';
			}
			$query .= $this->request->data['Search']['search_terms'];
			*/
			
			$query = $this->Search->buildQueryString($this->request->data);
			
			$result_items = array();
			
			$result = $this->Search->runQuery($query, 1);
			$result_items = array_merge($result_items, $result['items']);
			
			$num_pages = 0;
			if($result['total_results'] >= 100)
				$num_pages = 10;
			else
				$num_pages = ceil($result['total_results'] / 10) - 1;
				
			if($result['total_results'] > 10) {
				for($i = 1; $i < $num_pages; $i++) {
					//echo 'i: '.$i.'<br />';
					$result = $this->Search->runQuery($query, $i * 10 + 1);
					$result_items = array_merge($result_items, $result['items']);
				}
			}
			
			//echo $result['total_results'].'<br />';
			//pr($result_items);
			
			$this->Search->create();
			$this->Search->save($this->request->data);
			
			foreach($result_items as $result_item) {
				$check = $this->Result->findByItunesId($result_item['itunes_id']);
				if(empty($check)) {
					$this->Result->create();
					$db_result['Result'] = $result_item;
					$db_result['Result']['search_id'] = $this->Search->id;
					$this->Result->save($db_result);
				}
			}
			
			//echo 'This is about to redirect to '.$this->Search->id;
			$this->redirect('/Searches/view/'.$this->Search->id);
		}
	}
	
	public function view($search_id) {
		if ($this->request->is('post')) {
			
			foreach($this->request->data['Results'] as $s=>$result) {
				$db_result = array();
				$db_result['Result']['id'] = $s;
				$db_result['Result']['will_be_scraped'] = 1;
				$this->Result->save($db_result);
			}
			
			$this->Session->setFlash('Your results will be scraped');
			$this->redirect('/Searches/create');
		} else {
			$this->set('search', $this->Search->findById($search_id));
		}
	}
	
	public function ajaxGetGoogleSearchPreviewLink() {
		$this->layout = false;
		if($this->request->is('post')) {
			echo 'https://www.google.com/search?q='.urlencode($this->Search->buildQueryString($this->request->data));
			exit;
		}
		die('Only Post');
	}
}
