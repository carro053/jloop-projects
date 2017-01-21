<?php

ini_set('display_errors',1); 
error_reporting(E_ALL);
setlocale(LC_MONETARY, 'en_US');

include("projections_stuff.php");

// Contact.ContactID = Guid("e6cc6256-4e28-4196-87b7-b7a6d5006570")
// ?where=Contact.ContactID+%3d+Guid(%22e6cc6256-4e28-4196-87b7-b7a6d5006570%22)

$response = $XeroOAuth->request('GET', $XeroOAuth->url('Invoices', 'core'), array('Contact.ContactID' => 'Guid(%22e6cc6256-4e28-4196-87b7-b7a6d5006570%22)','fromDate' => '2016-09-1','toDate' => '2016-12-31'));
if ($XeroOAuth->response['code'] == 200) {
	$inv = $XeroOAuth->parseResponse($XeroOAuth->response['response'], $XeroOAuth->response['format']);
	pr($inv);
	/*
	foreach($accounts->Reports[0]->Report[0]->Rows[0]->Row as $row) {
		//echo $row->RowType;
		if ($row->RowType == "Section") {
			if ($row->Title == "Less Cost of Sales") break;
			echo "<strong>".$row->Title."</strong>";
			if (count($row->Rows) > 0) {
				foreach ($row->Rows->Row as $sectionrow) {
					echo "<br />";
					echo " * ";
					echo $sectionrow->Cells->Cell[0]->Value." = ".$sectionrow->Cells->Cell[1]->Value;
					$invoicedTotal = floatval($sectionrow->Cells->Cell[1]->Value);
					if ($sectionrow->Cells->Cell[0]->Value == "Total Revenue") $endofrevenue = true;
				}
			}
		}
		echo "<br />";
		if ($endofrevenue) break;
	}
	//pr($accounts->Reports);
	*/
} else {
	outputError($XeroOAuth);
}






?>