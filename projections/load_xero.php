<?php 

loadPackage(__DIR__."/lib/xero-php-oauth2");

	

var $accountingAPI;


function setRefreshToken($refresh_token='') {
		Cache::set(array('duration' => '+300 days'));
		Cache::write('xero_refresh_token', $refresh_token);
		Cache::set(array('duration' => '+300 days'));
		$refresh_token = Cache::read('xero_refresh_token');
		echo $refresh_token;
		echo '<hr>';
		$fresh_access_token = $this->getAccessToken(true);
		echo $fresh_access_token;
		echo '<hr>';
		$fresh_tenant_id = $this->getTenantID($fresh_access_token,true);
		echo $fresh_tenant_id;
	}

	public function getAccessToken($fresh=false) {
		Cache::set(array('duration' => '+29 minutes'));
		$access_token = Cache::read('xero_access_token');
		if(!empty($access_token) && !$fresh) {
			return $access_token;
		}
		Cache::set(array('duration' => '+300 days'));
		$refresh_token = Cache::read('xero_refresh_token');
		if(!empty($refresh_token)) {
			$url = 'https://identity.xero.com/connect/token';

			//The data you want to send via POST
			$fields = [
				'grant_type' => 'refresh_token',
				'refresh_token' => $refresh_token
			];

			//url-ify the data for the POST
			$fields_string = http_build_query($fields);

			//open connection
			$ch = curl_init();

			//set the url, number of POST vars, POST data
			curl_setopt($ch,CURLOPT_URL, $url);
			curl_setopt($ch,CURLOPT_POST, true);
			curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);

			$header = [];
			$header[] = 'Content-type: application/x-www-form-urlencoded';
			$header[] = 'Authorization: Basic MDgwNUIwOTAyQkM1NDY4MzhBQUE5MjQwQTRDQTJGMTM6OGNqVGJEYzBQTnBOU1JPLXAzMVpSaEwxR2ZLaF9jRTA5dVQzUXJWdWlsMjl3WFRz';
			curl_setopt($ch,CURLOPT_HTTPHEADER, $header);

			//So that curl_exec returns the contents of the cURL; rather than echoing it
			curl_setopt($ch,CURLOPT_RETURNTRANSFER, true); 

			//execute post
			$result = curl_exec($ch);
			$decoded = json_decode($result, true);
			if(!empty($decoded['refresh_token'])) {
				Cache::set(array('duration' => '+300 days'));
				Cache::write('xero_refresh_token', $decoded['refresh_token']);
			}
			if(!empty($decoded['access_token'])) {
				Cache::set(array('duration' => '+29 minutes'));
				Cache::write('xero_access_token', $decoded['access_token']);
				return $decoded['access_token']; 
			}
		}
		return '';
	}

	public function revokeRefreshToken() {
		Cache::set(array('duration' => '+300 days'));
		$refresh_token = Cache::read('xero_refresh_token');
		if(!empty($refresh_token)) {

			$url = 'https://identity.xero.com/connect/revocation';

			//The data you want to send via POST
			$fields = [
				'token' => $refresh_token
			];

			//url-ify the data for the POST
			$fields_string = http_build_query($fields);

			//open connection
			$ch = curl_init();

			//set the url, number of POST vars, POST data
			curl_setopt($ch,CURLOPT_URL, $url);
			curl_setopt($ch,CURLOPT_POST, true);
			curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);

			$header = [];
			$header[] = 'Content-type: application/x-www-form-urlencoded';
			$header[] = 'Authorization: Basic MDgwNUIwOTAyQkM1NDY4MzhBQUE5MjQwQTRDQTJGMTM6VG5vSWxlVGtGRlFpTXl5Q25VUE5ZRmlMWDUwS1Q2SnJTbDEyeUphWGhFeUZnZUJR';
			curl_setopt($ch,CURLOPT_HTTPHEADER, $header);

			//So that curl_exec returns the contents of the cURL; rather than echoing it
			curl_setopt($ch,CURLOPT_RETURNTRANSFER, true); 

			//execute post
			$result = curl_exec($ch);
			$decoded = json_decode($result, true);
		}
		exit;
	}

	public function getTenantID($fresh=false) {
		Cache::set(array('duration' => '+29 minutes'));
		$tenant_id = Cache::read('xero_tenant_id');
		if(!empty($tenant_id) && !$fresh) {
			return $tenant_id;
		}
		$url = 'https://api.xero.com/connections';

		//open connection
		$ch = curl_init();

		//set the url, number of POST vars, POST data
		curl_setopt($ch,CURLOPT_URL, $url);
		curl_setopt($ch,CURLOPT_POST, false);

		$header = [];
		$header[] = 'Content-type: application/json';
		$header[] = 'Authorization: Bearer '.$this->getAccessToken();
		curl_setopt($ch,CURLOPT_HTTPHEADER, $header);

		//So that curl_exec returns the contents of the cURL; rather than echoing it
		curl_setopt($ch,CURLOPT_RETURNTRANSFER, true); 

		//execute post
		$result = curl_exec($ch);
		$decoded = json_decode($result, true);
		$tenantID = '';
		foreach($decoded as $tenant) {
			if(ENVIRONMENT == 'Production' && $tenant['tenantName'] == 'Odeum, LLC') {
				$tenantID = $tenant['tenantId'];
				break;
			} else if(ENVIRONMENT != 'Production' && $tenant['tenantName'] == 'Demo Company (US)') {
				$tenantID = $tenant['tenantId'];
				break;
			} 
		}
		if(!empty($tenantID)) {
			Cache::set(array('duration' => '+29 minutes'));
			Cache::write('xero_tenant_id', $tenantID);
			return $tenantID;
		}
		return '';
	}

	public function initAPIs() {
		$access_token = $this->getAccessToken();
		$config = XeroAPI\XeroPHP\Configuration::getDefaultConfiguration()->setAccessToken( (string)$access_token );	
  
		$this->accountingAPI = new XeroAPI\XeroPHP\Api\AccountingApi(
			new GuzzleHttp\Client(),
			$config
		);
    }
    public function getInvoices() {
		$this->initAPIs();

		try {
			$apiResponse = $this->accountingAPI->getInvoice($this->getTenantID());
			return $apiResponse->getInvoices();
		} catch (Exception $e) {
			echo 'Exception when calling AccountingApi->getInvoice: ', $e->getMessage(), PHP_EOL;
		}
		
		exit;
	}
