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
		for($i = 81; $i <= 90; $i++) {
			echo 'page '.$i,'<br><br>';
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
			
			//Networks list
			foreach(pq('ul.networks-list li a') as $key => $a) {
				if(pq($a)->attr('title') == 'Website')
					$mybio['Mybio']['website'] = pq($a)->attr('href');
					
				if(pq($a)->attr('title') == 'Linkedin')
					$mybio['Mybio']['linkedin'] = pq($a)->attr('href');
				
				if(pq($a)->attr('title') == 'Twitter')
					$mybio['Mybio']['twitter'] = pq($a)->attr('href');
					
				if(pq($a)->attr('title') == 'Facebook')
					$mybio['Mybio']['facebook'] = pq($a)->attr('href');
			}
			
			//address, phone, fax
			foreach(pq('div.sidebar') as $key => $sidebar_div) {
				if(strpos(pq($sidebar_div)->html(), '<h5>Location</h5>') !== false) {
					$mybio['Mybio']['address'] = '';
					if(pq('.street-address', $sidebar_div)->text() != '')
						$mybio['Mybio']['address'] .= pq('.street-address', $sidebar_div)->text();
					if(pq('.extended-address', $sidebar_div)->text() != '')
						$mybio['Mybio']['address'] .= ' '.pq('.extended-address', $sidebar_div)->text();
					if(pq('.locality', $sidebar_div)->text() != '')
						$mybio['Mybio']['city'] = pq('.locality', $sidebar_div)->text();
					if(pq('.region', $sidebar_div)->text() != '')
						$mybio['Mybio']['state'] = pq('.region', $sidebar_div)->text();
					if(pq('.postal-code', $sidebar_div)->text() != '')
						$mybio['Mybio']['zip'] = pq('.postal-code', $sidebar_div)->text();
					if(pq('.country-name', $sidebar_div)->text() != '')
						$mybio['Mybio']['country'] = pq('.country-name', $sidebar_div)->text();
						
					if(pq('div.contact-phone', $sidebar_div) != '') {
						pq('div.contact-phone span.contact-label', $sidebar_div)->remove();
						$mybio['Mybio']['phone'] = pq('div.contact-phone', $sidebar_div)->text();
					}
					
					if(pq('div.contact-fax', $sidebar_div) != '') {
						pq('div.contact-fax span.contact-label', $sidebar_div)->remove();
						$mybio['Mybio']['fax'] = pq('div.contact-fax', $sidebar_div)->text();
					}
					
					break;
				}
			}
			
			pr($mybio);
			
			$mybio['Mybio']['scraped'] = 1;
			$this->Mybio->save($mybio);
		}
		
		exit;
	}
}