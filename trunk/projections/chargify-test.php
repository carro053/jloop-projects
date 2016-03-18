<?php
	

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
$subs = $sub->getAll();
echo '<h2>Array of Subscriptions</h2>';
//print_r($subs);

foreach($subs as $s) {
	echo 'name: '.$s->customer->email.'<br>';
	echo 'price: '.$s->product->price_in_cents.'<br>';
	echo 'next: '.$s->next_assessment_at.'<br>';
	echo 'status: '.$s->state.'<br>';
	echo '<br>';
}

?>