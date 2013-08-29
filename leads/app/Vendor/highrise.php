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
	
	public function pushPerson($data) {
		$xml = '<person>
		  <first-name>John</first-name>
		  <last-name>Doe</last-name>
		  <title>CEO</title>
		  <company-name>API Test Company</company-name>
		  <background>A popular guy for random data</background>
		  <linkedin_url>http://us.linkedin.com/in/john-doe</linkedin_url>
		  <contact-data>
		    <email-addresses>
		      <email-address>
		        <address>john.doe@example.com</address>
		        <location>Work</location>
		      </email-address>
		    </email-addresses>
		    <phone-numbers>
		      <phone-number>
		        <number>555-555-5555</number>
		        <location>Work</location>
		      </phone-number>
		      <phone-number>
		        <number>555-666-6666</number>
		        <location>Home</location>
		      </phone-number>
		    </phone-numbers>
		  </contact-data>
		  <!-- start of custom fields -->
		  <subject_datas type="array">
		    <subject_data>
		      <value>Chicago</value>
		      <subject_field_id type="integer">2</subject_field_id>
		    </subject_data>
		  </subject_datas>
		  <!-- end of custom fields -->
		</person>';
		$resp = $this->post($xml, 'people.xml');
		return $resp;
	}
	
	public function pushCompany() {
		$xml = '<company>
		  <name>Doe Inc.</name>
		  <background>A popular company for random data</background>
		  <visible-to>Owner</visible-to>
		  <contact-data>
		    <email-addresses>
		      <email-address>
		        <address>corporate@example.com</address>
		        <location>Work</location>
		      </email-address>
		    </email-addresses>
		    <phone-numbers>
		      <phone-number>
		        <number>555-555-5555</number>
		        <location>Work</location>
		      </phone-number>
		      <phone-number>
		        <number>555-666-6667</number>
		        <location>Fax</location>
		      </phone-number>
		    </phone-numbers>
		  </contact-data>
		  <!-- custom fields -->
		  <subject_datas type="array">
		    <subject_data>
		      <value>Chicago</value>
		      <subject_field_id type="integer">2</subject_field_id>
		    </subject_data>
		  </subject_datas>
		</company>';
		$resp = $this->post($xml, 'companies.xml');
		return $resp;
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