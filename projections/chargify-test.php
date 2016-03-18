<?php
	

require_once('Chargify-PHP-Client/lib/Chargify.php');
$test = FALSE;

$customer = new ChargifyCustomer(NULL, $test);
$customers = $customer->getAllCustomers();
echo '<h2>Array of 50 customer objects</h2>';
print_r($customers);

$customer0 = new ChargifyCustomer(NULL, $test);
$customer0->id = $customers[0]->id;
$customer_x = $customer0->getByID();
echo '<h2>Single customer object by ID</h2>';
print_r($customer_x);

?>