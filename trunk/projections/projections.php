<?php

ini_set('display_errors',1); 
error_reporting(E_ALL);
setlocale(LC_MONETARY, 'en_US');

include("projections_stuff.php");


////

echo 'month of june<br/><br/>';

echo 'PROJECTED invoices:<br />';
$response = $XeroOAuth->request('GET', $XeroOAuth->url('Invoices', 'core'), array('Where' => 'Status=="DRAFT" && Date>=DateTime(2014,7,1) && Date<DateTime(2014,8,1)'));
if ($XeroOAuth->response['code'] == 200) {
	$accounts = $XeroOAuth->parseResponse($XeroOAuth->response['response'], $XeroOAuth->response['format']);
	echo "There are " . count($accounts->Invoices[0]->Invoice). " to date <br/>";
	//pr($accounts->Invoices[0]->Invoice);
	$projectTotal = 0;
	foreach($accounts->Invoices[0]->Invoice as $inv) {
		echo date('M-d', strtotime($inv->DueDate)).": ";
		echo $inv->Contact->Name.": ".$inv->Reference." - ".$inv->AmountDue."<br/>";
		$projectTotal += intval($inv->AmountDue);
	}
	echo "TOTAL PROJECTED: ".money_format('%n',$projectTotal)."<br/><br/>";
	pr($accounts->Invoices[0]->Invoice[0]);
} else {
	outputError($XeroOAuth);
}

$response = $XeroOAuth->request('GET', $XeroOAuth->url('RepeatingInvoices', 'core'), array('Where' => 'Schedule.NextScheduledDate>=DateTime(2014,7,1) && Schedule.NextScheduledDate<DateTime(2014,8,1)'));
if ($XeroOAuth->response['code'] == 200) {
	$accounts = $XeroOAuth->parseResponse($XeroOAuth->response['response'], $XeroOAuth->response['format']);
	echo "There are " . count($accounts->RepeatingInvoices[0]). " matching invoices in this Xero organisation, the first one is: </br>";
	pr($accounts->RepeatingInvoices[0]->RepeatingInvoice);
} else {
	outputError($XeroOAuth);
}
//
?>