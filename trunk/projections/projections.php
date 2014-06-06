<?php

ini_set('display_errors',1); 
error_reporting(E_ALL);


include("projections_stuff.php")


////

$response = $XeroOAuth->request('GET', $XeroOAuth->url('RepeatingInvoices', 'core'), array('Where' => 'Schedule.NextScheduledDate>=DateTime(2014,7,1) && Schedule.NextScheduledDate<DateTime(2014,8,1)'));
if ($XeroOAuth->response['code'] == 200) {
	$accounts = $XeroOAuth->parseResponse($XeroOAuth->response['response'], $XeroOAuth->response['format']);
	echo "There are " . count($accounts->RepeatingInvoices[0]). " matching invoices in this Xero organisation, the first one is: </br>";
	pr($accounts->RepeatingInvoices[0]->RepeatingInvoice);
} else {
	outputError($XeroOAuth);
}

?>