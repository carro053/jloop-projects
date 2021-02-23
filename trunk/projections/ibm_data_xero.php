<?php
set_time_limit(0);
ini_set('display_errors',1); 
error_reporting(E_ALL);
setlocale(LC_MONETARY, 'en_US');

include("load_xero.php");
$xeroComponent = new XeroComponent();
if(isset($_GET['year'])) {
	$myyear = $_GET['year'];
} else {
	$myyear = "2018";
}

if(isset($_GET['end']) && (strtolower($_GET['end']) != "today") && ($_GET['end'] != "")) {
	$enddate = date($_GET['end'].', 12, 31');
} else {
	$enddate = date("Y, m, d");
}
//echo $enddate;
//techno = 8a802a3a-7074-492e-bde5-281d646b395d
//ibm = 38625e33-6eaf-4e7c-a04d-311d32becfb2

$where = 'Date>=DateTime('.$myyear.', 01, 01) && Date<=DateTime('.$enddate.') && Reference.Contains("WO")';
//echo $where;
$contact_ids = '38625e33-6eaf-4e7c-a04d-311d32becfb2,8a802a3a-7074-492e-bde5-281d646b395d';
$statuses = 'AUTHORISED,DRAFT,SUBMITTED';
//$response = $XeroOAuth->request('GET', $XeroOAuth->url('Invoices', 'core'), array('where' => $where));
$invoices = $xeroComponent->getInvoices($where, $contact_ids,$statuses);
if(!empty($_GET['debug'])) {
    echo '<pre>';
    print_r($invoices);
    echo '</pre>';
    echo '<br>';
}
if(!empty($invoices)) {
	$grandtotal = 0;
	foreach($invoices as $invoice) {
		$numHours = 0;
		echo date('m/d/Y', strtotime(convertXeroDate($invoice->getDate()))).",";
		echo $invoice->GetInvoiceNumber().",";
		echo $invoice->getReference().",";
		//echo $invoice->Total;
		//echo "<br>";
		$grandtotal += floatval($invoice->getTotal());
			foreach($invoice->getLineItems() as $lineitem) {
				//echo $lineitem->Description." - ".$lineitem->Quantity."<br>";
				$numHours += floatval($lineitem->getQuantity());
			}
		//echo "Total Hours: ".$numHours."<br>";
		//$rate = $invoice->Total/$numHours;
		//echo "Rate: ".$rate."<br>";
		echo $numHours.",";
		echo $invoice->getTotal().",";
		echo $invoice->getAmountDue();
		
		echo "\n";
		
		
	}
	//echo "Grand total: ".$grandtotal;
}
function convertXeroDate($date) {
	preg_match('/(\d{10})(\d{3})([\+\-]\d{4})/', $date, $matches);
	$dt = DateTime::createFromFormat("U.u.O",vsprintf('%2$s.%3$s.%4$s', $matches));
	return $dt->format('Y-m-d');
}
exit;

// if ($XeroOAuth->response['code'] == 200) {
// 	$inv = $XeroOAuth->parseResponse($XeroOAuth->response['response'], $XeroOAuth->response['format']);
// 	///reorder based on date
// 	$inv_array = array();
// 	foreach($inv->Invoices[0] as $invoice) {
// 		$inv_array[] = $invoice;
// 	}
// 	function cmp($a, $b)
// 	{
// 	    if(strtotime($a->Date) < strtotime($b->Date)) {
// 		    return -1;
// 	    } else {
// 		    return 1;
// 	    }
// 	}
	
// 	usort($inv_array, "cmp");
// 	//pr($inv_array);
	
// 	//pr($inv);
// 	$grandtotal = 0;
// 	if (count($inv->Invoices[0]) == 0) {
// 		exit();
// 	}
// 	foreach($inv_array as $invoice) {
// 		//pr($invoice);
// 		$numHours = 0;
// 		echo date('m/d/Y', strtotime($invoice->Date)).",";
// 		echo $invoice->InvoiceNumber.",";
// 		echo $invoice->Reference.",";
// 		//echo $invoice->Total;
// 		//echo "<br>";
// 		$grandtotal += floatval($invoice->Total);
		
// 		$invresponse = $XeroOAuth->request('GET', $XeroOAuth->url('Invoices/'.$invoice->InvoiceID, 'core'), array());
// 		if ($XeroOAuth->response['code'] == 200) {
// 			$invdetails = $XeroOAuth->parseResponse($XeroOAuth->response['response'], $XeroOAuth->response['format']);
// 			foreach($invdetails->Invoices->Invoice->LineItems->LineItem as $lineitem) {
// 				//echo $lineitem->Description." - ".$lineitem->Quantity."<br>";
// 				$numHours += floatval($lineitem->Quantity);
// 			}
// 		}
// 		//echo "Total Hours: ".$numHours."<br>";
// 		//$rate = $invoice->Total/$numHours;
// 		//echo "Rate: ".$rate."<br>";
// 		echo $numHours.",";
// 		echo $invoice->Total.",";
// 		echo $invoice->AmountDue;
		
// 		echo "\n";
		
		
// 	}
// 	//pr($accounts->Reports);
// 	//echo "Grand total: ".$grandtotal;
	
// } else {
// 	outputError($XeroOAuth);
// }






?>