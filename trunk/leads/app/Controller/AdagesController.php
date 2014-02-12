<?php
App::uses('AppController', 'Controller');

class AdagesController extends AppController {

	public function getDirectoryListing() {
		//echo 'hello';
		//exit;
	
		App::import('Vendor', 'phpQuery/phpQuery');
				
		$html = file_get_contents('http://adage.com/directory');
		
		$doc = phpQuery::newDocumentHTML($html);
		
		pr($doc);
		
		echo 'start directory listing';
		pr(pq('a.directory_entry'));
		
		exit;
	}

}