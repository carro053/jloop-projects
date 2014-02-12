<?php
App::uses('AppController', 'Controller');

class AdagesController extends AppController {

	public function getDirectoryListing() {
		//echo 'hello';
		//exit;
	
		App::import('Vendor', 'phpQuery/phpQuery');
		
		//Adage uses AJAX to load the actual company names, so you'll have to save the rendered page in your browser and upload it to the following location
		$html = file_get_contents('http://'.$_SERVER['HTTP_HOST'].'/files/adage_directory.html');
		
		//echo $html;
		//exit;
		
		$doc = phpQuery::newDocumentHTML($html);
		
		pr($doc);
		
		echo 'start directory listing';
		//pr(pq('a.directory_entry'));
		
		foreach(pq('a.directory_entry') as $key => $a) {
			
			echo pq($a)->text().'<hr>';
			echo pq($a)->attr('href').'<hr>';
			
			$this->Adage->create();
			$adage = array();
			$adage['Adage']['name'] = pq($a)->text();
			$adage['Adage']['url'] = pq($a)->attr('href');
			$this->Adage->save($adage, false);
		}
		
		exit;
	}

	public function scrapeAdage($id) {
		
	}
}