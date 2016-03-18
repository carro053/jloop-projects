<?php
	
	ini_set('display_errors',1); 
error_reporting(E_ALL);
	
	
if (!isset($_GET['expenses'])) {
	$numMos4Expenses = 3;
} else {
	$numMos4Expenses = floatval($_GET['expenses']);
}
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
$prevmo = floatval($mo)-1;
$nextyr = $yr;
$prevyr = $yr;
if ($nextmo == 13) {
	$nextmo = 1;
	$nextyr = floatval($yr) + 1;
}
if ($prevmo == 0) {
	$prevmo = 12;
	$prevyr = floatval($yr) - 1;
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
	

require_once('Chargify-PHP-Client/lib/Chargify.php');
$test = FALSE;
/*
$customer = new ChargifyCustomer(NULL, $test);
$customers = $customer->getAllCustomers();
echo '<h2>Array of 50 customer objects</h2>';
print_r($customers);

$customer0 = new ChargifyCustomer(NULL, $test);
$customer0->id = $customers[0]->id;
$customer_x = $customer0->getByID();
echo '<h2>Single customer object by ID</h2>';
print_r($customer_x);
*/

$sub = new ChargifySubscription(NULL, $test);
$sub->setActiveDomain("realatomic", "OPoM2KVP7almaPyrAgpJ");
$subs = $sub->getAll();
echo '<h2>Array of Subscriptions</h2>';
//print_r($subs);

$endMo = strtotime($month_array[floatval($mo)]." ".$days.", ".$yr." 23:59:59");
$chargifyTotal = 0;

foreach($subs as $s) {
	
	$price = floatval($s->product->price_in_cents)/100;
	$nextSched = strtotime($s->next_assessment_at);
	if ($nextSched < $endMo) $thisMonth = "true";
	else $thisMonth = "false";
				
	if ($thisMonth == "true" && $s->state == "active") {
		$chargifyTotal += $price;
	}
	
	
	echo 'name: '.$s->customer->email.'<br>';
	echo 'price: '.$price.'<br>';
	echo 'next: '.$s->next_assessment_at.'<br>';
	echo 'this month: '.$thisMonth.'<br>';
	echo 'status: '.$s->state.'<br>';
	echo '<br>';
}

echo "Chargify Total: ".$chargifyTotal;
?>