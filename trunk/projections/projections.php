<?php

ini_set('display_errors',1); 
error_reporting(E_ALL);
setlocale(LC_MONETARY, 'en_US');

include("projections_stuff.php");
if (!isset($_GET['month'])) {
	$mo = date('n');
} else {
	$mo = $_GET['month'];
}
if (!isset($_GET['year'])) {
	$yr = date('Y');
} else {
	$yr = $_GET['year'];
}
$month_array = array("N/A","January","February","March","April","May","June","July","August","September","October","November","December");

////

echo 'month of '.$month_array[floatval($mo)].', '.$yr.'<br/><br/>';


echo 'INVOICED to date:<br />';
$response = $XeroOAuth->request('GET', $XeroOAuth->url('Reports/ProfitAndLoss', 'core'), array('Where' => 'fromDate="2014-2-1" && toDate="2014-2-28"'));
if ($XeroOAuth->response['code'] == 200) {
	$accounts = $XeroOAuth->parseResponse($XeroOAuth->response['response'], $XeroOAuth->response['format']);
	$invoicedTotal = 0;
	foreach($accounts->Reports[0]->Report[0]->Rows[0]->Row as $row) {
		//echo $row->RowType;
		if ($row->RowType == "Section") {
			if ($row->Title == "Less Cost of Sales") break;
			echo "<strong>".$row->Title."</strong>";
			foreach ($row->Rows->Row as $sectionrow) {
				echo "<br />";
				echo $sectionrow->Cells->Cell[0]->Value." = ".$sectionrow->Cells->Cell[1]->Value;
				$invoicedTotal = floatval($sectionrow->Cells->Cell[1]->Value);
			}
		}
		echo "<br />";
	}
	//pr($accounts->Reports);
} else {
	outputError($XeroOAuth);
}
/*
echo "<br />";
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
$response = $XeroOAuth->request('GET', $XeroOAuth->url('RepeatingInvoices', 'core'), array('Where' => 'Schedule.Unit=="MONTHLY" && Schedule.Period==1 && Type=="ACCREC"'));
if ($XeroOAuth->response['code'] == 200) {
	$accounts = $XeroOAuth->parseResponse($XeroOAuth->response['response'], $XeroOAuth->response['format']);
	echo "There are " . count($accounts->RepeatingInvoices[0]->RepeatingInvoice). " monthly invoices: </br>";
	$recurTotal =0;
	foreach($accounts->RepeatingInvoices[0]->RepeatingInvoice as $inv) {
		//echo $inv->Contact->Name.": ".$inv->Total."<br />";
		$recurTotal += floatval($inv->Total);
	}
	echo "TOTAL monthly: ".money_format('%n', $recurTotal)."<br /><br />";
	
	//pr($accounts->RepeatingInvoices[0]->RepeatingInvoice[0]);
} else {
	outputError($XeroOAuth);
}

echo 'Other Recurring invoices in this month:<br />';
$response = $XeroOAuth->request('GET', $XeroOAuth->url('RepeatingInvoices', 'core'), array('Where' => 'Schedule.NextScheduledDate>=DateTime(2014,7,1) && Schedule.NextScheduledDate<DateTime(2014,8,1) && Schedule.Period>1 && Type=="ACCREC"'));
if ($XeroOAuth->response['code'] == 200) {
	$accounts = $XeroOAuth->parseResponse($XeroOAuth->response['response'], $XeroOAuth->response['format']);
	echo "There are " . count($accounts->RepeatingInvoices[0]->RepeatingInvoice). " other invoices: </br>";
	$recurOtherTotal =0;
	foreach($accounts->RepeatingInvoices[0]->RepeatingInvoice as $inv) {
		//echo $inv->Contact->Name.": ".$inv->Total."<br />";
		$recurOtherTotal += floatval($inv->Total);
	}
	$recurTotal += $recurOtherTotal;
	echo "TOTAL other: ".money_format('%n', $recurOtherTotal)."<br /><br />";
	echo "TOTAL all recurring: ".money_format('%n', $recurTotal)."<br /><br />";
}



$projectedTotal = $invoicedTotal + $projectTotal + $recurTotal + $recurOtherTotal;
echo '<strong>PROJECTED TOTAL FOR MONTH: '.money_format('%n', $projectedTotal)."</strong>";
//
*/
?>