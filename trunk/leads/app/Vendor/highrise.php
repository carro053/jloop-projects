<?php
class Highrise {
	
	private $apiToken = '91826f752ed853d8ac81bbc9650232ab';
	private $baseUrl = 'https://jloop.highrisehq.com/';
	
	private $toBePrintedTags = array(
		'greg-to-print' => 'greg-to-print',
		'jay-to-print' => 'jay-to-print',
		'jen-to-print' => 'jen-to-print'
	);
	
	private $printedTags = array(
		'greg-printed' => 'greg-printed',
		'jay-printed' => 'jay-printed',
		'jen-printed' => 'jen-printed'
	);
	
	private function post($postfields = '', $uri = '') {
		$curl = curl_init($this->baseUrl.$uri);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_USERPWD, $this->apiToken.':x');
		curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/xml'));
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $postfields);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER,0);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST,0);
		$resp = curl_exec($curl);
		curl_close($curl);
		$parsed_resp = simplexml_load_string($resp);
		return $parsed_resp;
	}
	
	private function get($uri = '') {
		$curl = curl_init($this->baseUrl.$uri);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_USERPWD, $this->apiToken.':x');
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER,0);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST,0);
		$resp = curl_exec($curl);
		curl_close($curl);
		$parsed_resp = simplexml_load_string($resp);
		return $parsed_resp;
	}
	
	public function cleanToBePrintedTags() {
		$all_tags = $this->get('tags.xml');
		echo '<pre>';
		print_r($all_tags);
		
		print_r($this->printedTags);
		$tag_ids_to_find = array();
		foreach($all_tags->tag as $tag) {
			echo $tag->name.' '.$tag->id.'<br>';
			var_dump((string)$tag->name);
			if(isset($this->printedTags[(string)$tag->name])) {
				$tag_ids_to_find[$tag->id] = $tag->name;
			}
		}
		print_r($tag_ids_to_find);
		exit;
	}
	
	public function tagPerson($tag_name, $contact_id) {
		$xml = '<name>'.$tag_name.'</name>';
		$resp = $this->post($xml, 'people/'.$contact_id.'/tags.xml');
		return $resp;
	}
	
	public function pushPerson($contact, $lead) {
		$xml = '<person>
		  <first-name>'.htmlspecialchars($contact['first_name']).'</first-name>
		  <last-name>'.htmlspecialchars($contact['last_name']).'</last-name>
		  <title>'.htmlspecialchars($contact['title']).'</title>
		  <company-name>'.htmlspecialchars($lead['company']).'</company-name>
		  <background>'.htmlspecialchars($contact['background_info']).'</background>
		  <linkedin_url>'.htmlspecialchars($contact['linkedin']).'</linkedin_url>
		  <contact-data>
		    <email-addresses>
		      <email-address>
		        <address>'.htmlspecialchars($contact['email']).'</address>
		        <location>Work</location>
		      </email-address>
		    </email-addresses>
		    <phone-numbers>
		      <phone-number>
		        <number>'.htmlspecialchars($contact['phone']).'</number>
		        <location>Work</location>
		      </phone-number>
		    </phone-numbers>
		    <addresses>
		    	<address>
			      <city>'.htmlspecialchars($lead['city']).'</city>
			      <country>'.htmlspecialchars($lead['country']).'</country>
			      <state>'.htmlspecialchars($lead['state']).'</state>
			      <street>'.htmlspecialchars($lead['address']).'</street>
			      <zip>'.htmlspecialchars($lead['zip']).'</zip>
			      <location>Work</location>
			    </address>
			    <address>
			      <city>'.htmlspecialchars($contact['city']).'</city>
			      <country>'.htmlspecialchars($contact['country']).'</country>
			      <state>'.htmlspecialchars($contact['state']).'</state>
			      <street>'.htmlspecialchars($contact['address']).'</street>
			      <zip>'.htmlspecialchars($contact['zip']).'</zip>
			      <location>Other</location>
			    </address>
			  </addresses>
			  <twitter-accounts>
			    <twitter-account>
			      <username>'.htmlspecialchars($contact['twitter']).'</username>
			    </twitter-account>
			  </twitter-accounts>
			  <instant-messengers>
			    <instant-messenger>
			      <address>'.htmlspecialchars($contact['im']).'</address>
			      <protocol>AIM</protocol>
			      <location>Work</location>
			    </instant-messenger>
			  </instant-messengers>
			  <web-addresses>
			    <web-address>
			      <url>'.htmlspecialchars($contact['website']).'</url>
			      <location>Work</location>
			    </web-address>
			  </web-addresses>
		  </contact-data>
		  <subject_datas type="array">
			  <subject_data>
			    <value>'.htmlspecialchars($contact['linkedin']).'</value>
			    <subject_field_id type="integer">808592</subject_field_id>
			    <subject_field_label>Linkedin</subject_field_label>
			  </subject_data>
		  </subject_datas>
		</person>';
		$resp = $this->post($xml, 'people.xml');
		return $resp;
	}
	
	public function pushCompany($lead) { //https://github.com/37signals/highrise-api/blob/master/sections/data_reference.md#company
		$xml = '<company>
		  <name>'.htmlspecialchars($lead['company']).'</name>
		  <background></background>
		  <visible-to>Everyone</visible-to>
		  <contact-data>
		    <email-addresses>
		      <email-address>
		        <address>'.htmlspecialchars($lead['email']).'</address>
		        <location>Work</location>
		      </email-address>
		    </email-addresses>
		    <phone-numbers>
		      <phone-number>
		        <number>'.htmlspecialchars($lead['phone']).'</number>
		        <location>Work</location>
		      </phone-number>
		    </phone-numbers>
		    <addresses>
			    <address>
			      <city>'.htmlspecialchars($lead['city']).'</city>
			      <country>'.htmlspecialchars($lead['country']).'</country>
			      <state>'.htmlspecialchars($lead['state']).'</state>
			      <street>'.htmlspecialchars($lead['address']).'</street>
			      <zip>'.htmlspecialchars($lead['zip']).'</zip>
			      <location>Work</location>
			    </address>
			  </addresses>
			  <twitter-accounts>
			    <twitter-account>
			      <username>'.htmlspecialchars($lead['twitter']).'</username>
			    </twitter-account>
			  </twitter-accounts>
		  </contact-data>
		  <subject_datas type="array">
			  <subject_data>
			    <value>'.htmlspecialchars($lead['facebook']).'</value>
			    <subject_field_id type="integer">808590</subject_field_id>
			    <subject_field_label>Facebook</subject_field_label>
			  </subject_data>
			  <subject_data>
			    <value>'.htmlspecialchars($lead['linkedin']).'</value>
			    <subject_field_id type="integer">808592</subject_field_id>
			    <subject_field_label>Linkedin</subject_field_label>
			  </subject_data>
		  </subject_datas>
		</company>';
		$resp = $this->post($xml, 'companies.xml');
		return (int)$resp->id;
	}
	
	public function pushDeal($lead, $company_id) {
		$tags = '';
		foreach($lead['Tag'] as $key=>$tag) {
			if($key != 0)
				$tags .= ', ';
			$tags .= $tag['name'];
		}
		$xml = '<deal>
		  <name>'.htmlspecialchars($lead['Lead']['name']).'</name>
		  <!-- optional fields -->
		  <party-id type="integer">'.$company_id.'</party-id>
		  <background>Tags: '.$tags.'</background>
		</deal>';
		$resp = $this->post($xml, 'deals.xml');
		return $resp;
	}
}