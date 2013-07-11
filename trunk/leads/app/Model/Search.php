<?php
App::uses('AppModel', 'Model');

class Search extends AppModel {
	
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
