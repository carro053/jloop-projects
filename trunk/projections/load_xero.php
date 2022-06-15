<?php 
include(__DIR__."/vendor/autoload.php");
	
class XeroComponent {
	var $accountingAPI;
	function readCache() {
		$cache = json_decode(file_get_contents("xero_cache.json") , true);
		if(strtotime($cache['xero_access_created']) < strtotime('-29 minutes')) {
			$cache['xero_access_token'] = false;
		}
		return $cache;
	}
	function writeCache($xero_refresh_token = null, $xero_access_token = null, $xero_tenant_id = null) {
		$cache = json_decode(file_get_contents("xero_cache.json") , true);
		if($xero_refresh_token !== null) {
			$cache['xero_refresh_token'] = $xero_refresh_token;
		}
		if($xero_access_token !== null) {
			$cache['xero_access_token'] = $xero_access_token;
			$cache['xero_access_created'] = date('Y-m-d H:i:s');
		}
		if($xero_tenant_id !== null) {
			$cache['xero_tenant_id'] = $xero_tenant_id;
		}
		return file_put_contents("xero_cache.json" , json_encode($cache));
	}

	function setRefreshToken($refresh_token='') {
		$this->writeCache($refresh_token);
		$cache = $this->readCache();
		$refresh_token = $cache['xero_refresh_token'];
		echo $refresh_token;
		echo '<hr>';
		$fresh_access_token = $this->getAccessToken(true);
		echo $fresh_access_token;
		echo '<hr>';
		$fresh_tenant_id = $this->getTenantID($fresh_access_token,true);
		echo $fresh_tenant_id;
	}

	function getAccessToken($fresh=false) {
		$cache = $this->readCache();
		$access_token = $cache['xero_access_token'];
		if(!empty($access_token) && !$fresh) {
			return $access_token;
		}
		$refresh_token =$cache['xero_refresh_token'];
		//if(!empty($refresh_token)) {
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
			$header[] = 'Authorization: Basic ODMyMTY3M0Y0RDdGNDgxRDhDQTc4RjUwNzU4NTIxQzk6NUQtdW1JTFEyeGZDUm10c1dMU3JPVUQ1bUhjcFppdTBpWFBueXdsbFRfSUNCdTMx';
			curl_setopt($ch,CURLOPT_HTTPHEADER, $header);

			//So that curl_exec returns the contents of the cURL; rather than echoing it
			curl_setopt($ch,CURLOPT_RETURNTRANSFER, true); 

			//execute post
			$result = curl_exec($ch);
			$decoded = json_decode($result, true);
			if(!empty($decoded['refresh_token'])) {
				$this->writeCache($decoded['refresh_token']);
			}
			if(!empty($decoded['access_token'])) {
				$this->writeCache(null,$decoded['access_token']);
				return $decoded['access_token']; 
			}
		//}
		return '';
	}

	function revokeRefreshToken() {
		$cache = $this->readCache();
		$refresh_token = $cache['xero_refresh_token'];
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
			$header[] = 'Authorization: Basic ODMyMTY3M0Y0RDdGNDgxRDhDQTc4RjUwNzU4NTIxQzk6NUQtdW1JTFEyeGZDUm10c1dMU3JPVUQ1bUhjcFppdTBpWFBueXdsbFRfSUNCdTMx';
			curl_setopt($ch,CURLOPT_HTTPHEADER, $header);

			//So that curl_exec returns the contents of the cURL; rather than echoing it
			curl_setopt($ch,CURLOPT_RETURNTRANSFER, true); 

			//execute post
			$result = curl_exec($ch);
			$decoded = json_decode($result, true);
		}
		exit;
	}

	function getTenantID($fresh=false) {
		$cache = $this->readCache();
		$tenant_id = $cache['xero_tenant_id'];
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
			if($tenant['tenantName'] == 'JLOOP') {
				$tenantID = $tenant['tenantId'];
				break;
			}
		}
		if(!empty($tenantID)) {
			$this->writeCache(null,null,$tenantID);
			return $tenantID;
		}
		return '';
	}

	function initAPIs() {
		$access_token = $this->getAccessToken();
		$config = XeroAPI\XeroPHP\Configuration::getDefaultConfiguration()->setAccessToken( (string)$access_token );	

		$this->accountingAPI = new XeroAPI\XeroPHP\Api\AccountingApi(
			new GuzzleHttp\Client(),
			$config
		);
	}
	function getInvoices($where = null, $contact_ids = null, $statuses = null) {
		$this->initAPIs();
		$order = "Date ASC";

		try {
			$apiResponse = $this->accountingAPI->getInvoices($this->getTenantID(), null, $where, $order, null, null, $contact_ids, $statuses);
			return $apiResponse->getInvoices();
		} catch (Exception $e) {
			echo 'Exception when calling AccountingApi->getInvoices: ', $e->getMessage(), PHP_EOL;
		}
		
		exit;
	}

}