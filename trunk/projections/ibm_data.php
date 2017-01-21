<?php

ini_set('display_errors',1); 
error_reporting(E_ALL);
setlocale(LC_MONETARY, 'en_US');

include("projections_stuff.php");

$response = $XeroOAuth->request('GET', $XeroOAuth->url('Invoices/INV-36916', 'core'), array());
pr($response);
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