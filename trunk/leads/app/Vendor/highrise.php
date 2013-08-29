<?php
class Highrise {
	
	private $apiToken = '91826f752ed853d8ac81bbc9650232ab';
	private $baseUrl = 'https://jloop.highrisehq.com/';
	
	public function pushPerson() {
		$curl = curl_init($this->baseUrl.'people.xml');
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_USERPWD, $this->apiToken.':x'); 
		curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/xml'));
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, '<person>
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
</person>');

		curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
		curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);

		$xml = curl_exec($curl);
		curl_close($curl);	
		
		return $xml;
	}
	
}