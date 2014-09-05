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
$days = cal_days_in_month(CAL_GREGORIAN, $mo, $yr);
$nextmo = floatval($mo)+1;
$nextyr = $yr;
if ($nextmo == 13) {
	$nextmo = 1;
	$nextyr = floatval($yr) + 1;
}
if (strtotime($month_array[floatval($mo)]." ".$days.", ".$yr." 23:59:59") < time()) {
	$past = true;
	//echo "past";
} else $past = false;
$curmo = intval(date('n'));
if ($mo == date('n')) $thismonth = true;
else $thismonth = false;
$recurTotal =0;
$invoicedTotal = 0;
$projectTotal = 0;
$recurCount = 0;
$recurOtherTotal =0;
$weeklyCount = 0;
$weeklyTotal = 0;
////

echo 'month of '.$month_array[floatval($mo)].', '.$yr.'<br/><br/>';



echo '------------------------INVOICED to date:<br />';
$response = $XeroOAuth->request('GET', $XeroOAuth->url('Reports/ProfitAndLoss', 'core'), array('fromDate' => $yr.'-'.$mo.'-1','toDate' => $yr.'-'.$mo.'-'.$days));
if ($XeroOAuth->response['code'] == 200) {
	$accounts = $XeroOAuth->parseResponse($XeroOAuth->response['response'], $XeroOAuth->response['format']);
	
	$endofrevenue = false;
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
} else {
	outputError($XeroOAuth);
}
echo "<br /><br />";
echo "------------------------";

if (!$past) {

echo 'PROJECTED invoices:<br />';
$response = $XeroOAuth->request('GET', $XeroOAuth->url('Invoices', 'core'), array('Where' => 'Status=="DRAFT" && Date>=DateTime('.$yr.','.$mo.',1) && Date<DateTime('.$nextyr.','.$nextmo.',1) && Type=="ACCREC"'));
if ($XeroOAuth->response['code'] == 200) {
	$accounts = $XeroOAuth->parseResponse($XeroOAuth->response['response'], $XeroOAuth->response['format']);
	//echo "There are " . count($accounts->Invoices[0]->Invoice). " to date <br/>";
	//pr($accounts->Invoices[0]->Invoice);
	
	if (count($accounts->Invoices)>0) {
		foreach($accounts->Invoices[0]->Invoice as $inv) {
			echo " * ".date('M-d', strtotime($inv->Date)).": ";
			echo $inv->Contact->Name.": ".$inv->Reference." - ".$inv->AmountDue."<br/>";
			$projectTotal += floatval($inv->AmountDue);
		}
	}
	
	echo "Total projected: ".money_format('%n',$projectTotal)."<br/><br/>";
	//pr($accounts->Invoices[0]->Invoice[0]);
} else {
	outputError($XeroOAuth);
}

echo "------------------------";

$recur_array = array();
echo 'RECURRING invoices:<br />';
$response = $XeroOAuth->request('GET', $XeroOAuth->url('RepeatingInvoices', 'core'), array('Where' => 'Schedule.NextScheduledDate>=DateTime('.$yr.','.$mo.',1) && (Schedule.Unit=="MONTHLY" || Schedule.Unit=="Annual") && Schedule.NextScheduledDate<DateTime('.$nextyr.','.$nextmo.',1) && Type=="ACCREC"'));

if ($XeroOAuth->response['code'] == 200) {
	$accounts = $XeroOAuth->parseResponse($XeroOAuth->response['response'], $XeroOAuth->response['format']);
	echo "There are " . count($accounts->RepeatingInvoices[0]->RepeatingInvoice). " recurring invoices currently scheduled. </br>";
	
	foreach($accounts->RepeatingInvoices[0]->RepeatingInvoice as $inv) {
		echo $inv->Contact->Name.": ".$inv->Total."<br />";
		$recurOtherTotal += floatval($inv->Total);
		array_push($recur_array, strval($inv->RepeatingInvoiceID));
	}
	//$recurTotal += $recurOtherTotal;
	//echo "TOTAL all recurring: ".money_format('%n', $recurTotal)."<br /><br />";
	
	//pr($accounts->RepeatingInvoices);
	//pr($recur_array);
}

if (!$thismonth) {
	//echo 'RECURRING invoices scheduled:<br />Monthly invoices:<br />';
	$response = $XeroOAuth->request('GET', $XeroOAuth->url('RepeatingInvoices', 'core'), array('Where' => 'Schedule.Unit=="MONTHLY" && Schedule.Period==1 && Type=="ACCREC"'));
	if ($XeroOAuth->response['code'] == 200) {
		$accounts = $XeroOAuth->parseResponse($XeroOAuth->response['response'], $XeroOAuth->response['format']);
		//echo "There are " . count($accounts->RepeatingInvoices[0]->RepeatingInvoice). " monthly invoices: </br>";
		
		
		foreach($accounts->RepeatingInvoices[0]->RepeatingInvoice as $inv) {
			
			if (!in_array(strval($inv->RepeatingInvoiceID), $recur_array)) {
				$recurTotal += floatval($inv->Total);
				$recurCount ++;
				///echo "ADDED: ".$inv->Contact->Name.": ".$inv->Total."<br />";
			} else {
				//echo "NOT ADDED: ".$inv->Contact->Name.": ".$inv->Total."<br />";
			}
			
		}
		
		//pr($accounts->RepeatingInvoices[0]->RepeatingInvoice[0]);
	} else {
		outputError($XeroOAuth);
	}
}

//echo 'RECURRING invoices scheduled:<br />Weekly invoices:<br />';
	$response = $XeroOAuth->request('GET', $XeroOAuth->url('RepeatingInvoices', 'core'), array('Where' => 'Schedule.Unit=="WEEKLY" && Type=="ACCREC"'));
	if ($XeroOAuth->response['code'] == 200) {
		$accounts = $XeroOAuth->parseResponse($XeroOAuth->response['response'], $XeroOAuth->response['format']);
		//echo "There are " . count($accounts->RepeatingInvoices[0]->RepeatingInvoice). " weekly invoices: </br>";
		
		
		foreach($accounts->RepeatingInvoices[0]->RepeatingInvoice as $inv) {
			//$weeksInMo = 4;
			//print_r($inv);
			$nextSched = strtotime($inv->Schedule->NextScheduledDate);
			$endSched = strtotime($inv->Schedule->EndDate);
			$endMo = strtotime($month_array[floatval($mo)]." ".$days.", ".$yr." 23:59:59");
			$startMo = strtotime($month_array[floatval($mo)]." 1, ".$yr." 00:00:00");
			$aweek = 604800;
			$interval = floatval($inv->Schedule->Period);
			//echo "interval: ".$interval."</br>";
			//echo "NextStr: ".strtotime($month_array[floatval($mo)]." ".$days.", ".$yr." 23:59:59")."</br>";
			if ($nextSched < $endMo) {
				$schedTest = $nextSched;
				do {
					if ($schedTest > $startMo && $schedTest < $endSched) {
						$weeklyTotal += floatval($inv->Total);
						$weeklyCount ++;
					}
					$schedTest += $aweek*$interval;
				} while ($schedTest < $endMo);
				//echo "Next Date: ".$inv->Schedule->NextScheduledDate;
				
				///echo "ADDED: ".$inv->Contact->Name.": ".$inv->Total."<br />";
			//} else {
				//echo "NOT ADDED: ".$inv->Contact->Name.": ".$inv->Total."<br />";
			}
			
		}
		
		//pr($accounts->RepeatingInvoices[0]->RepeatingInvoice[0]);
	} else {
		outputError($XeroOAuth);
	}

//end if not this month

	echo "There are " . $recurCount. " monthly invoices not yet scheduled in this month.</br>";
	echo "Total recurring scheduled: ".money_format('%n', $recurOtherTotal)."<br />";
	echo "Total monthly not-yet-scheduled: ".money_format('%n', $recurTotal)."<br />";
	echo "There are " . $weeklyCount ." weekly not-yet-scheduled: ".money_format('%n', $weeklyTotal)."<br /><br />";
} // end if past


echo "------------------------";

$projectedTotal = $invoicedTotal + $projectTotal + $recurOtherTotal + $recurTotal + $weeklyTotal;
echo '<strong>PROJECTED TOTAL FOR MONTH: '.money_format('%n', $projectedTotal)."</strong>";

echo "<br /><br />";

echo '------------------------EXPENSES:<br />';
if ($curmo < 7) {
	$startyr = intval($yr) -1;
	$startmo = $curmo +6;
	if ($curmo == 1) {
		$lastmo = 12;
		$lastmoyr = intval(date('Y'))-1;
		
	} else {
		$lastmo = $curmo - 1;
		$lastmoyr = date('Y');
	}
	
} else {
	$startyr = $yr;
	$startmo = $curmo - 6;
	$lastmo = $curmo - 1;
	$lastmoyr = $yr;
}
$lastmodays = cal_days_in_month(CAL_GREGORIAN, $lastmo, $lastmoyr);


$response = $XeroOAuth->request('GET', $XeroOAuth->url('Reports/ProfitAndLoss', 'core'), array('fromDate' => $startyr.'-'.$startmo.'-1','toDate' => $lastmoyr.'-'.$lastmo.'-'.$lastmodays));
if ($XeroOAuth->response['code'] == 200) {
	$accounts = $XeroOAuth->parseResponse($XeroOAuth->response['response'], $XeroOAuth->response['format']);
	
	$expenseTotal = 0;
	foreach($accounts->Reports[0]->Report[0]->Rows[0]->Row as $row) {
		//echo $row->RowType;
		if ($row->RowType == "Section") {
			if (count($row->Rows) > 0) {
				foreach ($row->Rows->Row as $sectionrow) {
					if ($sectionrow->Cells->Cell[0]->Value == "Total Cost of Sales") {
						$expenseTotal += floatval($sectionrow->Cells->Cell[1]->Value);
					} else if ($sectionrow->Cells->Cell[0]->Value == "Total Other Income and Expense") {
						
						$expenseTotal += (0 - floatval($sectionrow->Cells->Cell[1]->Value));
						//echo "<br />";
						//echo " * ";
						//echo $sectionrow->Cells->Cell[0]->Value." = ".$sectionrow->Cells->Cell[1]->Value;
					}
					
				}
			}
		}
		//echo "<br />";
	}
	//pr($accounts->Reports);
} else {
	outputError($XeroOAuth);
}
$averageExpenses = $expenseTotal/6;
$profit = $projectedTotal - $averageExpenses;
//echo 'Expenses for the <i>last 6 months</i>: '.money_format('%n', $expenseTotal)."<br />";
echo 'AVERAGE monthly expenses for the <i>last 6 months</i>: '.money_format('%n', $averageExpenses)."<br /><br />";
echo "------------------------<strong>";
if ($profit < 0) echo "LOSS FOR THIS MONTH: <red>".money_format('%n', $profit)."</red>";
else echo "PROFIT FOR THIS MONTH: ".money_format('%n', $profit);
echo "</strong><br /><br />";

//

?>