<?php
App::uses('AppController', 'Controller');

class PrsasController extends AppController {
	var $uses = array('Prsa', 'Contact');

	public function index() {
		$limit = (!empty($_GET['limit']) ? $_GET['limit'] : 50);
		$page = (!empty($_GET['page']) ? $_GET['page'] : 1);
		$order = (!empty($_GET['order']) ? $_GET['order'] : 'Prsa.name').' '.(!empty($_GET['direction']) ? $_GET['direction'] : 'asc');
		
		$conditions = array('Lead.status' => 0);
		if(!empty($_GET['search'])) {
			$conditions['OR']['Prsa.name LIKE'] = '%'.$_GET['search'].'%';
			$conditions['OR']['Prsa.industry_specializations LIKE'] = '%'.$_GET['search'].'%';
			$conditions['OR']['Prsa.practice_specializations LIKE'] = '%'.$_GET['search'].'%';
			$conditions['OR']['Prsa.state LIKE'] = '%'.$_GET['search'].'%';
		}
		
		$prsas = $this->Prsa->find('all', array(
			'conditions' => $conditions,
			'order' => $order,
			'page' => $page,
			'limit' => $limit
		));
		$this->set('prsas', $prsas);
		$count = $this->Prsa->find('count', array('conditions' => $conditions));
		$this->set('count', $count);
	}

	public function view($id) {
		$this->layout = false;
		$prsa = $this->Prsa->find('first', array('conditions' => 'Prsa.id = '.$id, 'recursive' => 3));
		$this->set('prsa', $prsa);
		
		$tags = $this->Prsa->Lead->Tag->find('all');
		$this->set('tags', $tags);
		
		$assignable_users = array('0' => 'Unassigned');
		$assignable_users += $this->Prsa->Lead->Contact->User->find('list', array(
			'fields' => array('User.id', 'User.username'),
			'order' => 'User.username ASC'
		));
		$this->set('assignable_users', $assignable_users);
	}

	
	
	public function scrapeIndex() {
		//example URL: http://www.prsa.org/Network/FindAFirm/Search?StartDisplay=1&xName=&xCompany=&xCity=&xIndSpec=&xState=CA&xZip=&xCountry=&xDesignation=&xPracSpec=&xSearchOutput=IND
	
		Configure::write('debug', 2);
		set_time_limit(120); //don't let the script run for more than 2 minutes
		echo '<pre>';
	
		App::import('Vendor', 'phpQuery/phpQuery');
		
		$i = 1;
		$increment = 10;
		while(true) {
			$url = 'http://www.prsa.org/Network/FindAFirm/Search?StartDisplay='.$i.'&xName=&xCompany=&xCity=&xIndSpec=&xState=CA&xZip=&xCountry=&xDesignation=&xPracSpec=&xSearchOutput=IND'; //url with specified search paramters
			$html = file_get_contents($url);
			if($html) {
			
				//ex: There were 57 matches
				preg_match_all('/There were ([\d]+) matches/', $html, $matches);
				
				//stop the main loop if we go beyond the max search results
				if(isset($matches[1][0]) && $i > $matches[1][0]) {
					echo 'reached max results at '.$i;
					break;
				}
				
				$doc = phpQuery::newDocumentHTML($html);
				
				//check to see if our favorite content div exists
				if(pq('div.contentCol')->length) {
					$prsa = array();
					foreach(pq('div.contentCol table tr td.secondtitle') as $name_td_node) {
						$prsa['Prsa']['name'] = pq($name_td_node)->find('span b')->html();
						
						$address_td_node = pq($name_td_node)->parent()->next()->find('td');
						
						$address_parts = explode('<br>', pq($address_td_node)->html());
						
						//clean out the crap characters
						$cleaned_address_parts = array();
						foreach($address_parts as $part) {
							$part = trim($part);
							if(!empty($part))
								$cleaned_address_parts[] = $part;
						}
						
						if(count($cleaned_address_parts) == 2) {
							$prsa['Prsa']['address'] = $cleaned_address_parts[0];
							$other_address_parts = explode(',', $cleaned_address_parts[1]); //City, State, Zip
						}
						
						if(count($cleaned_address_parts) == 3) {
							$prsa['Prsa']['address'] = $cleaned_address_parts[0].', '.$cleaned_address_parts[1];
							$other_address_parts = explode(',', $cleaned_address_parts[2]); //City, State, Zip
						}
						
						//clean out the crap characters
						$cleaned_other_address_parts = array();
						if(!empty($other_address_parts)) {
							foreach($other_address_parts as $part) {
								$part = trim($part);
								if(!empty($part))
									$cleaned_other_address_parts[] = $part;
							}
						}
						
						$prsa['Prsa']['city'] = $cleaned_other_address_parts[0];
						preg_match_all('/([A-Z][A-Z])/', $cleaned_other_address_parts[1], $matches); //find State
						$prsa['Prsa']['state'] = $matches[1][0];
						preg_match_all('/([\d]+)/', $cleaned_other_address_parts[1], $matches); //find Zip
						$prsa['Prsa']['zip'] = $matches[1][0];
						
						$phone_td_node = pq($address_td_node)->parent()->next()->find('td');
						$prsa['Prsa']['phone'] = trim(pq($phone_td_node)->html());
						
						//loop through all remaining tr's until we hit the hr
						$current_tr_node = pq($phone_td_node)->parent()->next();
						$found_hr = false;
						while(pq($current_tr_node)->length && !$found_hr) {
							$check_hr = pq($current_tr_node)->find('hr');
							if(pq($check_hr)->length) {
								echo 'END OF RECORD';
								$found_hr = true;
							} else {
								if(preg_match_all('/fax: (\([\d]+\) [\d]+\-[\d]+)/', pq($current_tr_node)->html(), $matches))
									$prsa['Prsa']['fax'] = $matches[1][0];
								
								if(preg_match_all('/"Mailto:(.+?)"/', pq($current_tr_node)->html(), $matches))
									$prsa['Prsa']['email'] = $matches[1][0];
									
								if(preg_match_all('/website: <a href="(.+?)"/', pq($current_tr_node)->html(), $matches))
									$prsa['Prsa']['company_url'] = $matches[1][0];
								
								if(preg_match_all('/Number of Employees: ([\d]+)/', pq($current_tr_node)->html(), $matches))
									$prsa['Prsa']['employees'] = $matches[1][0];
								
								if(preg_match('/Industry Specializations:/', pq($current_tr_node)->html())) {
									$industry_specializations_node = pq($current_tr_node)->find('i');
									$prsa['Prsa']['industry_specializations'] = pq($industry_specializations_node)->html();
								}
								
								if(preg_match('/Practice Specializations:/', pq($current_tr_node)->html())) {
									$practice_specializations_node = pq($current_tr_node)->find('i');
									$prsa['Prsa']['practice_specializations'] = pq($practice_specializations_node)->html();
								}
								
								if(preg_match('/Contact:/', pq($current_tr_node)->html())) {
									$contact_name_node = pq($current_tr_node)->find('strong');
									$contact_name = pq($contact_name_node)->html();
									
									$contact_name_parts = explode(',', $contact_name);
									$contact_name = $contact_name_parts[0];
									$contact_name_parts = explode(' ', $contact_name);
									$prsa['Prsa']['contact_first_name'] = $contact_name_parts[0];
									$prsa['Prsa']['contact_last_name'] = $contact_name_parts[count($contact_name_parts) - 1];
									
									$contact_title_node = pq($current_tr_node)->find('i');
									$prsa['Prsa']['contact_title'] = pq($contact_title_node)->html();
								}
								
								$current_tr_node = pq($current_tr_node)->next();
							}
						}
						
						pr($prsa);
						
						$this->Prsa->create();
						$this->Prsa->save($prsa);
					}
				}
				
			} else {
				echo 'Error fetching: '.$url.'<br>';
			}
			if($i > 5000) //JUST IN CASE WE GO TOO FAR!!!
				break;
			$i += $increment;
		}
		exit;
	}
	
	public function convertToLeadsAndContacts() {
		Configure::write('debug', 2);
		set_time_limit(120); //don't let the script run for more than 2 minutes
	
		$prsas = $this->Prsa->find('all', array('conditions' => 'Prsa.lead_id = 0'));
		foreach($prsas as $prsa) {
			$lead = array();
			$this->Prsa->Lead->create();
			$lead['Lead']['model'] = 'Prsa';
			$lead['Lead']['model_id'] = $prsa['Prsa']['id'];
			$lead['Lead']['type'] = 'PRSA Scrape';
			$lead['Lead']['name'] = $prsa['Prsa']['name'];
			$lead['Lead']['company'] = $prsa['Prsa']['name'];
			$lead['Lead']['phone'] = $prsa['Prsa']['phone'];
			$lead['Lead']['address'] = $prsa['Prsa']['address'];
			$lead['Lead']['city'] = $prsa['Prsa']['city'];
			$lead['Lead']['zip'] = $prsa['Prsa']['zip'];
			$lead['Lead']['state'] = $prsa['Prsa']['state'];
			$lead['Lead']['country'] = $prsa['Prsa']['country'];
			$lead['Lead']['website'] = $prsa['Prsa']['company_url'];
			$lead['Lead']['email'] = $prsa['Prsa']['email'];
			
			pr($lead);
			
			if($this->Prsa->Lead->save($lead)) {
				$prsa['Prsa']['lead_id'] = $this->Prsa->Lead->id;
				$this->Prsa->save($prsa);
				
				if(!empty($prsa['Prsa']['contact_first_name'])) {
					$this->Contact->create();
					$contact = array();
					$contact['Contact']['lead_id'] = $this->Prsa->Lead->id;
					$contact['Contact']['first_name'] = $prsa['Prsa']['contact_first_name'];
					$contact['Contact']['last_name'] = $prsa['Prsa']['contact_last_name'];
					$contact['Contact']['title'] = $prsa['Prsa']['contact_title'];
					
					pr($contact);
					
					$this->Contact->save($contact, false);
				}
			}
		}
		
		exit;
	}
}