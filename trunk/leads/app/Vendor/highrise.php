<?php
class Highrise {
	
	private $apiToken = '91826f752ed853d8ac81bbc9650232ab';
	private $baseUrl = 'https://jloop.highrisehq.com/';
	
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
	
	public function pushPerson($contact, $company) {
		$xml = '<person>
		  <first-name>'.htmlspecialchars($contact['first_name']).'</first-name>
		  <last-name>'.htmlspecialchars($contact['last_name']).'</last-name>
		  <title>'.htmlspecialchars($contact['title']).'</title>
		  <company-name>'.htmlspecialchars($company).'</company-name>
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
			      <city>'.htmlspecialchars($contact['city']).'</city>
			      <country>'.htmlspecialchars($contact['country']).'</country>
			      <state>'.htmlspecialchars($contact['state']).'</state>
			      <street>'.htmlspecialchars($contact['address']).'</street>
			      <zip>'.htmlspecialchars($contact['zip']).'</zip>
			      <location>Work</location>
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
			    <subject_field_label>Facebook</subject_field_label>
			  </subject_data>
			  <subject_data>
			    <value>'.htmlspecialchars($lead['linkedin']).'</value>
			    <subject_field_label>Linkedin</subject_field_label>
			  </subject_data>
		  </subject_datas>
		</company>';
		$resp = $this->post($xml, 'companies.xml');
		return (int)$resp->id;
	}
	
	public function pushDeal() {
		$xml = '<deal>
		  <name>#{name}</name>
		  <!-- optional fields -->
		  <party-id type="integer">#{party_id}</party-id>
		  <visible-to>Everyone</visible-to>
		  <group-id type="integer">#{group_id}</group-id>
		  <owner-id type="integer">#{owner_id}</owner-id>
		  <responsible-party-id type="integer">#{responsible_party_id}</responsible-party-id>
		  <category-id type="integer">#{category_id}</category-id>
		  <background>#{background}</background>
		  <currency>#{currency}</currency>
		  <price type="integer">#{price}</price>
		  <price-type>fixed</price-type>
		  <duration type="integer">#{duration}</duration>
		</deal>';
		$resp = $this->post($xml, 'deals.xml');
		return $resp;
	}
}