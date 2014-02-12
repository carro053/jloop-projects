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

	//JDM, GOOD, id = 901, Alcone BAD, id = 83
	public function scrapeAdage($id) {
		App::import('Vendor', 'phpQuery/phpQuery');
		$adage = $this->Adage->findById($id);
		$html = file_get_contents($adage['Adage']['url']);
		$doc = phpQuery::newDocumentHTML($html);
		
		if($html) {
			//check if "Access Denied" message
			if(!pq('.warning')->length) {
				$adage['Adage']['address'] = strip_tags(pq('.address')->text());
				$adage['Adage']['city'] = strip_tags(pq('.city')->text());
				$adage['Adage']['state'] = strip_tags(pq('.state')->text());
				$adage['Adage']['zip'] = strip_tags(pq('.zip')->text());
				$adage['Adage']['country'] = strip_tags(pq('.country')->text());
				$adage['Adage']['phone'] = strip_tags(pq('.attribute-phone')->text());
				$adage['Adage']['company_url'] = strip_tags(pq('div.attribute-url a')->text());
				$adage['Adage']['employees'] = strip_tags(pq('.employees')->text());
				$adage['Adage']['regions'] = strip_tags(pq('ul.region li')->text());
				$adage['Adage']['specialties'] = strip_tags(pq('ul.speciality li')->text()); //
				$adage['Adage']['categories'] = strip_tags(pq('ul.category li')->text());
			} else {
				$adage['Adage']['access_denied'] = 1;
			}
			$this->Adage->save($adage, false);
			$adage['Adage']['scraped'] = 1;
			pr($adage);
		} else {
			//mark as errored if the 
			$adage['Adage']['network_error'] = 1;
		}
		return '1';
	}
}