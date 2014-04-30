<?php
App::uses('AppController', 'Controller');

class PrsasController extends AppController {

	//example URLs
	//http://www.prsa.org/Network/FindAFirm/Search?StartDisplay=1&xName=&xCompany=&xCity=&xIndSpec=&xState=CA&xZip=&xCountry=&xDesignation=&xPracSpec=&xSearchOutput=IND
	//http://www.prsa.org/Network/FindAFirm/Search?StartDisplay=11&xName=&xCompany=&xCity=&xIndSpec=&xState=CA&xZip=&xCountry=&xDesignation=&xPracSpec=&xSearchOutput=IND
	//http://www.prsa.org/Network/FindAFirm/Search?StartDisplay=51&xName=&xCompany=&xCity=&xIndSpec=&xState=CA&xZip=&xCountry=&xDesignation=&xPracSpec=&xSearchOutput=IND
	
	public function scrapeIndex() {
		set_time_limit(120); //don't let the script run for more than 2 minutes
		echo '<pre>';
	
		App::import('Vendor', 'phpQuery/phpQuery');
		
		$i = 1;
		$increment = 10;
		while(true) {
			$url = 'http://www.prsa.org/Network/FindAFirm/Search?StartDisplay='.$i.'&xName=&xCompany=&xCity=&xIndSpec=&xState=CA&xZip=&xCountry=&xDesignation=&xPracSpec=&xSearchOutput=IND';
			$html = file_get_contents($url);
			if($html) {
			
				//There were 57 matches
				preg_match_all('/There were ([\d]+) matches/', $html, $matches);
				pr($matches);
				
				if(isset($matches[1][0]) && $i > $matches[1][0]) {
					echo 'reached max results at '.$i;
					break;
				}
				
				$doc = phpQuery::newDocumentHTML($html);
				
				//check to see if our favorite div exists
				if(pq('div.contentCol')->length) {
					$prsa = array();
					foreach(pq('div.contentCol table tr td.secondtitle') as $name_td_node) {
						$prsa['Prsa']['name'] = pq($name_td_node)->find('span b')->html();
						
						$address_node_td_node = pq($name_td_node)->parent()->next()->find('td');
						
						$address_parts = explode('<br>', pq($address_node_td_node)->html());
						
						pr($prsa);
						pr($address_parts);
						
						$cleaned_address_parts = array();
						foreach($address_parts as $part) {
							$part = trim($part);
							if(!empty($part))
								$cleaned_address_parts[] = $part;
						}
						
						echo 'cleaned parts<br>';
						pr($cleaned_address_parts);
						
						if(count($cleaned_address_parts) == 2) {
							$prsa['Prsa']['address'] = $cleaned_address_parts[0];
							$other_address_parts = explode(',', $cleaned_address_parts[1]);
						}
						
						if(count($cleaned_address_parts) == 3) {
							$prsa['Prsa']['address'] = $cleaned_address_parts[0].', '.$cleaned_address_parts[1];
							$other_address_parts = explode(',', $cleaned_address_parts[2]);
						}
						
						$cleaned_other_address_parts = array();
						if(!empty($other_address_parts)) {
							foreach($other_address_parts as $part) {
								$part = trim($part);
								if(!empty($part))
									$cleaned_other_address_parts[] = $part;
							}
						}
						
						echo 'cleaned other parts<br>';
						pr($cleaned_other_address_parts);
					}
				}
				
			} else {
				echo 'Error fetching: '.$url.'<br>';
			}
			if($i > 20) //JUST IN CASE WE GO TOO FAR!!!
				break;
			$i += $increment;
		}
		exit;
	}
}