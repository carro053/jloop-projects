<?php

ini_set('display_errors',1); 
error_reporting(E_ALL);
setlocale(LC_MONETARY, 'en_US');

include("projections_stuff.php");

if(isset($_GET['year'])) {
	$myyear = $_GET['year'];
} else {
	$myyear = "2017";
}

if(isset($_GET['end']) && ($_GET['end'] != "Today") && ($_GET['end'] != "")) {
	$enddate = $_GET['end'].', 01, 01';
} else {
	$enddate = date("Y, m, d");
}
//echo $enddate;
//techno = 8a802a3a-7074-492e-bde5-281d646b395d
//ibm = 38625e33-6eaf-4e7c-a04d-311d32becfb2

$where = '(Contact.ContactID=Guid("38625e33-6eaf-4e7c-a04d-311d32becfb2") || Contact.ContactID=Guid("8a802a3a-7074-492e-bde5-281d646b395d")) && Date>=DateTime('.$myyear.', 01, 01) && Date<=DateTime('.$enddate.') && Reference.Contains("WO") && Status != "DELETED" && Status != "VOIDED"';
//echo $where;


$response = $XeroOAuth->request('GET', $XeroOAuth->url('Invoices', 'core'), array('where' => $where));


if ($XeroOAuth->response['code'] == 200) {
	$inv = $XeroOAuth->parseResponse($XeroOAuth->response['response'], $XeroOAuth->response['format']);
	//pr($inv);
	$grandtotal = 0;
	if (count($inv->Invoices[0]) == 0) {
		exit();
	}
	foreach($inv->Invoices[0] as $invoice) {
		//pr($invoice);
		$numHours = 0;
		echo date('m/d/Y', strtotime($invoice->Date)).",";
		echo $invoice->InvoiceNumber.",";
		echo $invoice->Reference.",";
		//echo $invoice->Total;
		//echo "<br>";
		$grandtotal += floatval($invoice->Total);
		
		$invresponse = $XeroOAuth->request('GET', $XeroOAuth->url('Invoices/'.$invoice->InvoiceID, 'core'), array());
		if ($XeroOAuth->response['code'] == 200) {
			$invdetails = $XeroOAuth->parseResponse($XeroOAuth->response['response'], $XeroOAuth->response['format']);
			foreach($invdetails->Invoices->Invoice->LineItems->LineItem as $lineitem) {
				//echo $lineitem->Description." - ".$lineitem->Quantity."<br>";
				$numHours += floatval($lineitem->Quantity);
			}
		}
		//echo "Total Hours: ".$numHours."<br>";
		//$rate = $invoice->Total/$numHours;
		//echo "Rate: ".$rate."<br>";
		echo $numHours.",";
		echo $invoice->Total.",";
		echo $invoice->AmountDue;
		
		echo "\n";
		
		
	}
	//pr($accounts->Reports);
	//echo "Grand total: ".$grandtotal;
	
} else {
	outputError($XeroOAuth);
}






?>