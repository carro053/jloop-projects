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
		$projectTotal += floatval($inv->AmountDue);
	}
	echo "TOTAL PROJECTED: ".money_format('%n',$projectTotal)."<br/><br/>";
	//pr($accounts->Invoices[0]->Invoice[0]);
} else {
	outputError($XeroOAuth);
}

echo 'RECURRING invoices scheduled:<br />Monthly invoices:<br />';
$response = $XeroOAuth->request('GET', $XeroOAuth->url('RepeatingInvoices', 'core'), array('Where' => 'Schedule.Unit=="MONTHLY" && Schedule.Unit=="1" && Type=="ACCREC"'));
if ($XeroOAuth->response['code'] == 200) {
	$accounts = $XeroOAuth->parseResponse($XeroOAuth->response['response'], $XeroOAuth->response['format']);
	echo "There are " . count($accounts->RepeatingInvoices[0]->RepeatingInvoice). " monthly invoices: </br>";
	$recurTotal =0;
	foreach($accounts->RepeatingInvoices[0]->RepeatingInvoice as $inv) {
		echo $inv->Contact->Name.": ".$inv->Total."<br />";
		$recurTotal += floatval($inv->Total);
	}
	echo "TOTAL monthly: ".money_format('%n', $recurTotal)."<br /><br />";
	
	pr($accounts->RepeatingInvoices[0]->RepeatingInvoice[0]);
} else {
	outputError($XeroOAuth);
}

echo 'Other Recurring invoices in this month:<br />';
$response = $XeroOAuth->request('GET', $XeroOAuth->url('RepeatingInvoices', 'core'), array('Where' => 'Schedule.NextScheduledDate>=DateTime(2014,7,1) && Schedule.NextScheduledDate<DateTime(2014,8,1) && Schedule.Unit!="1" && Type=="ACCREC"'));
if ($XeroOAuth->response['code'] == 200) {
	$accounts = $XeroOAuth->parseResponse($XeroOAuth->response['response'], $XeroOAuth->response['format']);
	echo "There are " . count($accounts->RepeatingInvoices[0]->RepeatingInvoice). " other invoices: </br>";
	$recurOtherTotal =0;
	foreach($accounts->RepeatingInvoices[0]->RepeatingInvoice as $inv) {
		echo $inv->Contact->Name.": ".$inv->Total."<br />";
		$recurOtherTotal += floatval($inv->Total);
	}
	$recurTotal += $recurOtherTotal;
	echo "TOTAL other: ".money_format('%n', $recurOtherTotal)."<br /><br />";
	echo "TOTAL all recurring: ".money_format('%n', $recurTotal)."<br /><br />";
}

//
?>