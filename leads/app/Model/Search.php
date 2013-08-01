<?php
App::uses('AppModel', 'Model');

class Search extends AppModel {
	
	public $hasMany = array(
        'Result' => array(
            'className'  => 'Result',
            'foreignKey' => 'search_id'
        )
    );
		
	public function runQuery($query, $start) {
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
		
		
		$return = array();
		$return['items'] = array();
		$return['total_results'] = $result['searchInformation']['totalResults'];
		foreach($result['items'] as $item) {
			$return['items'][] = array('itunes_link' => $item['link'], 'itunes_id' => $this->parseItunesId($item['link']));
		}
		return $return;
	}
	
	public function buildQueryString($search) {
		// Example executing a search with your custom search id.
		$query = 'site:itunes.apple.com/us "Open iTunes to buy and download apps." '; //search US store, only iOS apps (not Mac apps or music, etc.)
		
		if($search['Search']['is_not_iphone_5'])
			$query .= '-"This app is optimized for iPhone 5." ';
		if($search['Search']['is_not_ipad_only'])
			$query .= '-"Compatible with iPad." ';
		/*
		if($search['Search']['use_date_range']) {
			$query .= 'daterange:'.$search['Search']['start_date']['year'].'-'.$search['Search']['start_date']['month'].'-'.$search['Search']['start_date']['day'].'..'.$search['Search']['end_date']['year'].'-'.$search['Search']['end_date']['month'].'-'.$search['Search']['end_date']['day'].' ';
		*/
		
		if($search['Search']['use_date']) {
			
		}
		$query .= $search['Search']['search_terms'];
		
		return $query;
	}
}