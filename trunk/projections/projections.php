<?php

ini_set('display_errors',1); 
error_reporting(E_ALL);

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

/**
 * Persist the OAuth access token and session handle somewhere
 * In my example I am just using the session, but in real world, this is should be a storage engine
 *
 * @param array $params the response parameters as an array of key=value pairs
 */
function persistSession($response)
{
    if (isset($response)) {
        $_SESSION['access_token']       = $response['oauth_token'];
        $_SESSION['oauth_token_secret'] = $response['oauth_token_secret'];
      	if(isset($response['oauth_session_handle']))  $_SESSION['session_handle']     = $response['oauth_session_handle'];
    } else {
        return false;
    }

}

/**
 * Retrieve the OAuth access token and session handle
 * In my example I am just using the session, but in real world, this is should be a storage engine
 *
 */
function retrieveSession()
{
    if (isset($_SESSION['access_token'])) {
        $response['oauth_token']            =    $_SESSION['access_token'];
        $response['oauth_token_secret']     =    $_SESSION['oauth_token_secret'];
        $response['oauth_session_handle']   =    $_SESSION['session_handle'];
        return $response;
    } else {
        return false;
    }

}

function outputError($XeroOAuth)
{
    echo 'Error: ' . $XeroOAuth->response['response'] . PHP_EOL;
    pr($XeroOAuth);
}

/**
 * Debug function for printing the content of an object
 *
 * @param mixes $obj
 */
function pr($obj)
{

    if (!is_cli())
        echo '<pre style="word-wrap: break-word">';
    if (is_object($obj))
        print_r($obj);
    elseif (is_array($obj))
        print_r($obj);
    else
        echo $obj;
    if (!is_cli())
        echo '</pre>';
}

function is_cli()
{
    return (PHP_SAPI == 'cli' && empty($_SERVER['REMOTE_ADDR']));
}



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



echo 'now';
$response = $XeroOAuth->request('GET', $XeroOAuth->url('RepeatingInvoices', 'core'), array('Where' => 'Schedule.NextScheduledDate>=DateTime(2014,7,1) && Schedule.NextScheduledDate<DateTime(2014,8,1)'));
if ($XeroOAuth->response['code'] == 200) {
	$accounts = $XeroOAuth->parseResponse($XeroOAuth->response['response'], $XeroOAuth->response['format']);
	echo "There are " . count($accounts->RepeatingInvoices[0]). " matching invoices in this Xero organisation, the first one is: </br>";
	pr($accounts->RepeatingInvoices[0]->RepeatingInvoice);
} else {
	outputError($XeroOAuth);
}

?>