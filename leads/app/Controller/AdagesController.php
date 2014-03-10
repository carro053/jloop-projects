<?php
App::uses('AppController', 'Controller');

class AdagesController extends AppController {

	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow(array('scrapeAllAdages'));
	}

	public function index() {
		$limit = (!empty($_GET['limit']) ? $_GET['limit'] : 50);
		$page = (!empty($_GET['page']) ? $_GET['page'] : 1);
		$order = (!empty($_GET['order']) ? $_GET['order'] : 'Adage.name').' '.(!empty($_GET['direction']) ? $_GET['direction'] : 'asc');
		
		$conditions = array('Lead.status' => 0);
		if(!empty($_GET['search'])) {
			$conditions['OR']['Adage.name LIKE'] = '%'.$_GET['search'].'%';
			$conditions['OR']['Adage.categories LIKE'] = '%'.$_GET['search'].'%';
			$conditions['OR']['Adage.specialties LIKE'] = '%'.$_GET['search'].'%';
			$conditions['OR']['Adage.regions LIKE'] = '%'.$_GET['search'].'%';
			$conditions['OR']['Adage.state LIKE'] = '%'.$_GET['search'].'%';
		}
		
		$adages = $this->Adage->find('all', array(
			'conditions' => $conditions,
			'order' => $order,
			'page' => $page,
			'limit' => $limit
		));
		$this->set('adages', $adages);
		$count = $this->Adage->find('count', array('conditions' => $conditions));
		$this->set('count', $count);
	}
	
	public function view($id) {
		$this->layout = false;
		$adage = $this->Adage->find('first', array('conditions' => 'Adage.id = '.$id, 'recursive' => 3));
		$this->set('adage', $adage);
		
		$tags = $this->Adage->Lead->Tag->find('all');
		$this->set('tags', $tags);
		
		$assignable_users = $this->Scrape->Lead->Contact->User->find('list', array(
			'fields' => array('User.id', 'User.username'),
			'order' => 'User.username ASC'
		));
		$this->set('assignable_users', $assignable_users);
	}

	public function convertIntoLeads() {
		$adages = $this->Adage->find('all');
		foreach($adages as $adage) {
			$lead = array();
			$this->Adage->Lead->create();
			$lead['Lead']['model'] = 'Adage';
			$lead['Lead']['model_id'] = $adage['Adage']['id'];
			$lead['Lead']['type'] = 'AdAge Scrape';
			$lead['Lead']['company'] = $adage['Adage']['name'];
			$lead['Lead']['phone'] = $adage['Adage']['phone'];
			$lead['Lead']['address'] = $adage['Adage']['address'];
			$lead['Lead']['city'] = $adage['Adage']['city'];
			$lead['Lead']['zip'] = $adage['Adage']['zip'];
			$lead['Lead']['state'] = $adage['Adage']['state'];
			$lead['Lead']['country'] = $adage['Adage']['country'];
			pr($lead);
			
			if($this->Adage->Lead->save($lead)) {
				$adage['Adage']['lead_id'] = $this->Adage->Lead->id;
				$this->Adage->save($adage);
			}
		}
		
		exit;
	}

	//adds http:// to company_urls because people are retarded
	public function fixCompanyUrls() {
		$adages = $this->Adage->find('all');
		foreach($adages as $adage) {
			if(!empty($adage['Adage']['company_url'])) {
				$adage['Adage']['company_url'] = 'http://'.$adage['Adage']['company_url'];
				$this->Adage->save($adage);
			}
		}
		exit;
	}

	public function getDirectoryListing() {
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

	public function scrapeAllAdages($limit = 20) {
		$adages = $this->Adage->find('all', array('conditions' => 'Adage.scraped = 0', 'limit' => $limit, 'fields' => 'Adage.id'));
		foreach($adages as $adage)
			$this->scrapeAdage($adage['Adage']['id']);
		exit;
	}
	
	//http://lookbook.adage.com/Agencies/JDM 		GOOD, id = 901
	//http://lookbook.adage.com/Agencies/Alcone		BAD, id = 83
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
			$adage['Adage']['scraped'] = 1;
			$this->Adage->save($adage, false);
			pr($adage);
		} else {
			//mark as errored if the 
			$adage['Adage']['network_error'] = 1;
		}
		return '1';
	}
}