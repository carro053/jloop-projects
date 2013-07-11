<?php
App::uses('AppController', 'Controller');

class SearchesController extends AppController {

	public $uses = array('Search');
	
	public function test() {
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
		
		// Example executing a search with your custom search id.
		$result = $search->cse->listCse('site:itunes.apple.com/us -"This app is optimized for iPhone 5." "Open iTunes to buy and download apps." -"Compatible with iPad."', array(
		  'cx' => '007301418745006324333:d--m5x9_aui', // The custom search engine ID to scope this search query.
		));
		print "<pre>" . print_r($result, true) . "</pre>";
		exit;
		
		/*
		// Example executing a search with the URL of a linked custom search engine.
		$result = $search->cse->listCse('burrito', array(
		  'cref' => 'http://www.google.com/cse/samples/vegetarian.xml',
		));
		print "<pre>" . print_r($result, true) . "</pre>";
		*/
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
			

			
			$result_items = array();
			
			$result = $this->run_query($query, 1);
			$result_items = array_merge($result_items, $result['items']);
			
			$num_pages = 0;
			if($result['total_results'] >= 100)
				$num_pages = 10;
			else
				$num_pages = ceil($result['total_results'] / 10) - 1;
				
			if($result['total_results'] > 10) {
				for($i = 1; $i < $num_pages; $i++) {
					echo 'i: '.$i.'<br />';
					$result = $this->run_query($query, $i * 10 + 1);
					$result_items = array_merge($result_items, $result['items']);
				}
			}
			
			echo $result['total_results'].'<br />';
			pr($result_items);
			exit;
		}
	}
	
	public function view($search_id) {
		
	}
	
	private function run_query($query, $start) {
		App::import('Vendor', 'google-api/Google_Client');
		App::import('Vendor', 'google-api/contrib/Google_CustomsearchService');
		
		//session_start();
		$client = new Google_Client();
		$client->setApplicationName('App Finder');
		// Docs: http://code.google.com/apis/customsearch/v1/using_rest.html
		// Visit https://code.google.com/apis/console?api=customsearch to generate
		// your developer key (simple api key).
		$client->setDeveloperKey('AIzaSyBeJoG_O5wa2aAqAsWihNnCLmckfDB6kNQ');
		$search = new Google_CustomsearchService($client);
				
		$result = $search->cse->listCse($query, array(
		  'cx' => '007301418745006324333:d--m5x9_aui', // The custom search engine ID to scope this search query.
		  'start' => $start
		));
		print "<pre>" . print_r($result, true) . "</pre>";
		
		
		$return = array();
		$return['items'] = array();
		$return['total_results'] = $result['searchInformation']['totalResults'];
		foreach($result['items'] as $item) {
			$return['items'][] = $item['link'];
		}
		return $return;
	}
}