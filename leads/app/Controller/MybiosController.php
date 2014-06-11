<?php
App::uses('AppController', 'Controller');

class MybiosController extends AppController {

	public function beforeFilter() {
		parent::beforeFilter();
	}

	public function scrapeSearchPage() {
		$this->layout = false;
	
		//last page: http://mybio.org/exhibitor?exhibitor_page=190
		App::import('Vendor', 'phpQuery/phpQuery');
		
		$base_url = 'http://mybio.org/exhibitor?exhibitor_page=';
		for($i = 1; $i <= 190; $i++) {
			$html = file_get_contents($base_url.$i);
			$doc = phpQuery::newDocumentHTML($html);
			foreach(pq('div.exhibitor-list-section h5 a') as $key => $a) {
				echo pq($a)->attr('href').'<br>';
				
				$this->Mybio->create();
				$mybio = array();
				$mybio['Mybio']['url'] = pq($a)->attr('href');
				$mybio['Mybio']['name'] = pq($a)->text();
				$this->Mybio->save($mybio);
			}
			break;
		}
		
		exit;
	}
	
	public function scrapeDetailPage() {
		/* examples
		http://mybio.org/exhibitor/member/85974
		http://mybio.org/exhibitor/member/78638
		*/
		
		App::import('Vendor', 'phpQuery/phpQuery');
		$mybios = $this->Mybio->find('all', array('limit' => 10, 'conditions' => array('Mybio.scraped' => '0')));
		foreach($mybios as $mybio) {
			$html = file_get_contents($mybio['Mybio']['url']);
			$doc = phpQuery::newDocumentHTML($html);
			
			
			
		}
	}
}