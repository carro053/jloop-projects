<?php

echo 'test';
require 'lib/XeroOAuth.php';

define ( 'BASE_PATH', dirname(__FILE__) );
define ( "XRO_APP_TYPE", "Private" );
define ( "OAUTH_CALLBACK", "oob" );
$useragent = "XeroOAuth-PHP Private App Test";

$signatures = array (
		'consumer_key' => 'VKVCKW3NUXKEPORW7ZWOHBIB7DYTCL',
		'shared_secret' => 'E2T16UNAVRADG6BQHRIXPCMQKEXXSU',
		// API versions
		'core_version' => '2.0',
		'payroll_version' => '1.0' 
);

if (XRO_APP_TYPE == "Private" || XRO_APP_TYPE == "Partner") {
	$signatures ['rsa_private_key'] = BASE_PATH . '/certs/privatekey.pem';
	$signatures ['rsa_public_key'] = BASE_PATH . '/certs/publickey.cer';
}

$XeroOAuth = new XeroOAuth ( array_merge ( array (
		'application_type' => XRO_APP_TYPE,
		'oauth_callback' => OAUTH_CALLBACK,
		'user_agent' => $useragent 
), $signatures ) );


$initialCheck = $XeroOAuth->diagnostics ();
$checkErrors = count ( $initialCheck );
if ($checkErrors > 0) {
	// you could handle any config errors here, or keep on truckin if you like to live dangerously
	foreach ( $initialCheck as $check ) {
		echo 'Error: ' . $check . PHP_EOL;
	}
} else {
	$session = persistSession ( array (
			'oauth_token' => $XeroOAuth->config ['consumer_key'],
			'oauth_token_secret' => $XeroOAuth->config ['shared_secret'],
			'oauth_session_handle' => '' 
	) );
	$oauthSession = retrieveSession ();
	
	if (isset ( $oauthSession ['oauth_token'] )) {
		$XeroOAuth->config ['access_token'] = $oauthSession ['oauth_token'];
		$XeroOAuth->config ['access_token_secret'] = $oauthSession ['oauth_token_secret'];
		
		//include 'tests/tests.php';
	}
	
	//testLinks ();
}




$response = $XeroOAuth->request('GET', $XeroOAuth->url('RepeatingInvoices', 'core'), array('Where' => 'Schedule.NextScheduledDate>=DateTime(2014,7,1) && Schedule.NextScheduledDate<DateTime(2014,8,1)'));
if ($XeroOAuth->response['code'] == 200) {
	$accounts = $XeroOAuth->parseResponse($XeroOAuth->response['response'], $XeroOAuth->response['format']);
	echo "There are " . count($accounts->RepeatingInvoices[0]). " matching invoices in this Xero organisation, the first one is: </br>";
	pr($accounts->RepeatingInvoices[0]->RepeatingInvoice);
} else {
	outputError($XeroOAuth);
}

?>