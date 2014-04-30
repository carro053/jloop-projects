<?php
App::uses('AppController', 'Controller');

class PrsasController extends AppController {

	//example URLs
	//http://www.prsa.org/Network/FindAFirm/Search?StartDisplay=1&xName=&xCompany=&xCity=&xIndSpec=&xState=CA&xZip=&xCountry=&xDesignation=&xPracSpec=&xSearchOutput=IND
	//http://www.prsa.org/Network/FindAFirm/Search?StartDisplay=11&xName=&xCompany=&xCity=&xIndSpec=&xState=CA&xZip=&xCountry=&xDesignation=&xPracSpec=&xSearchOutput=IND
	//http://www.prsa.org/Network/FindAFirm/Search?StartDisplay=51&xName=&xCompany=&xCity=&xIndSpec=&xState=CA&xZip=&xCountry=&xDesignation=&xPracSpec=&xSearchOutput=IND
	
	public function scrapeIndex() {
		echo '<pre>';
	
		App::import('Vendor', 'phpQuery/phpQuery');
		
		$has_more_pages = true;
		$i = 1;
		$increment = 10;
		while($has_more_pages) {
			$url = 'http://www.prsa.org/Network/FindAFirm/Search?StartDisplay='.$i.'&xName=&xCompany=&xCity=&xIndSpec=&xState=CA&xZip=&xCountry=&xDesignation=&xPracSpec=&xSearchOutput=IND';
			$html = file_get_contents($url);
			if($html) {
				//There were 57 matches
				$total_matches = preg_match('There were [.0-9]+ matches', $html);
				echo 'total matches: '.$total_matches;
			
				$doc = phpQuery::newDocumentHTML($html);
				
				
				
			} else {
				echo 'Error fetching: '.$url.'<br>';
			}
			if($i > 50) //JUST IN CASE WE GO TOO FAR!!!
				$has_more_pages = false;
			$i += $increment;
		}
		exit;
	}
}