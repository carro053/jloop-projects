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
						$prsa['Prsa']['address'] = pq($address_node_td_node)->html();
						pr($prsa);
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