<?php
class ScrapeResultsShell extends AppShell {
	
	public $uses = array(
		'Result',
		'Scrape'
	);
	
    public function main() {
        $this->out('Begin Scrapping');
        $results = $this->Result->find('all', array(
        	'conditions' => array(
        		'scraped' => 0,
        		'will_be_scraped' => 1
        	),
        	'limit' => 10
        ));
        $this->out('Results to Scrape: '.count($results));
        foreach($results as $result) {
	        $check = $this->Scrape->findByItunesId($result['Result']['itunes_id']);
	        if(empty($check)) {
	        	$this->out('  Scraping: '.$result['Result']['itunes_link']);
		        $this->Scrape->create();
		        $scrape = $this->Scrape->parseURL($result['Result']['itunes_link']);
		        $scrape['Lead']['type'] = 'Google Search Automatic iTunes Scrape';
		        $this->Scrape->save($scrape);
	        }else{
		        $this->out('  Link already scraped: '.$result['Result']['itunes_link']);
	        }
	        
	        $this->Result->save(array(
	        	'id' => $result['Result']['id'],
	        	'scraped' => 1
	        ));
        }
    }
    
}