<?php
App::uses('AppController', 'Controller');

class AdagesController extends AppController {

	public function getDirectoryListing() {
		App::import('Vendor', 'phpQuery/phpQuery');
				
		$html = file_get_contents('http://adage.com/directory');
		
		$doc = phpQuery::newDocumentHTML($html);
		
		pr($doc);
		exit;
	}

}