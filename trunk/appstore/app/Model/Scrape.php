<?php
App::uses('AppModel', 'Model');

class Scrape extends AppModel {
	
	public function parseURL($url) {
		App::import('Vendor', 'phpQuery/phpQuery');
		
		$html = file_get_contents($url);
		
		$doc = phpQuery::newDocumentHTML($html);
		
		$scrape = array();
		$scrape['name'] = pq('#title h1')->html();
		$scrape['company'] = preg_replace('/^By /', null, pq('#title h2')->html());;
		$scrape['itunes_link'] = pq('.product a')->attr('href');
		$is_universal = pq('.fat-binary-blurb')->html();
		$scrape['is_universal'] = empty($is_universal) ? false : true;
		$scrape['price'] = pq('.price')->html();
		$scrape['category'] = pq('.genre a')->html();
		$released_updated = pq('.release-date')->text();
		$scrape['is_updated'] = preg_match('/Updated:/', $released_updated);
		$scrape['updated'] = date('Y-m-d', strtotime(preg_replace('/^\S+\s/', null, $released_updated)));
		$scrape['version'] = preg_replace('/^\S+\s/', null, pq('li:contains("Version:")')->text());
		$scrape['size'] = preg_replace('/^\S+\s/', null, pq('li:contains("Size:")')->text());
		$scrape['languages'] = preg_replace('/^\S+\s/', null, pq('li.language')->text());
		$scrape['seller'] = preg_replace('/^\S+\s/', null, pq('li:contains("Seller:")')->text());
		$scrape['copyright'] = pq('li.copyright')->text();
		$scrape['requirements'] = preg_replace('/^\S+\s/', null, pq('p:contains("Requirements:")')->text());
		$ratings_current = pq('div.customer-ratings div:contains("Current Version:") + div')->attr('aria-label');
		$scrape['ratings_current'] = preg_replace('/,.*/', null, $ratings_current);
		preg_match('/(?<=, )\d*/', $ratings_current, $ratings_current_count);
		$scrape['ratings_current_count'] = $ratings_current_count[0];
		$ratings_all = pq('div.customer-ratings div:contains("All Versions:") + div')->attr('aria-label');
		$scrape['ratings_all'] = preg_replace('/,.*/', null, $ratings_all);
		preg_match('/(?<=, )\d*/', $ratings_all, $ratings_all_count);
		$scrape['ratings_all_count'] = $ratings_all_count[0];
		$scrape['description'] = pq('h4:contains("Description") + p')->html();
		foreach(pq('div.app-links a') as $key => $a) {
			$scrape['link_'.($key + 1).'_name'] = pq($a)->text();
			$scrape['link_'.($key + 1).'_href'] = pq($a)->attr('href');
		}
		
		return $scrape;
	}
	
}
