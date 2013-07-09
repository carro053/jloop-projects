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
}
