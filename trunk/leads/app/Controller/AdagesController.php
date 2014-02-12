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
		//pr(pq('a.directory_entry'));
		
		foreach(pq('a.directory_entry') as $key => $a) {
			echo pq($a)->text().'<hr>';
			echo pq($a)->attr('href').'<hr>';
		}
		
		exit;
	}

}