<?php
App::uses('AppController', 'Controller');

class AdagesController extends AppController {

	public function getDirectoryListing() {
		//echo 'hello';
		//exit;
	
		App::import('Vendor', 'phpQuery/phpQuery');
		
		//Adage uses AJAX to load the actual company names, so you'll have to save the rendered page in your browser and upload it to the following location
		$html = file_get_contents('http://'.$_SERVER['HTTP_HOST'].'/files/adage_directory.html');
		$doc = phpQuery::newDocumentHTML($html);
		
		echo 'start directory listing<br>';
		
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
		App::import('Vendor', 'phpQuery/phpQuery');
		$adage = $this->Adage->findById($id);
		$html = file_get_contents($adage['Adage']['url']);
		$doc = phpQuery::newDocumentHTML($html);
		
		if($html) {
			
			var_dump(pq('.warning')->length);
			
			exit;
			
			
			
			
			$adage['Adage']['scraped'] = 1;
			
			pr($adage);
			$this->Adage->save($adage, false);
		} else {
			//mark as errored if the 
			$adage['Adage']['error'] = 1;
		}
		return '1';
	}
}