<?php
	

require_once('Chargify-PHP-Client/lib/Chargify.php');
$test = FALSE;

$customer = new ChargifyCustomer(NULL, $test);
$customers = $customer->getAllCustomers();
echo '<h2>Array of 50 customer objects</h2>';
print_r($customers);

?>