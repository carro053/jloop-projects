<?php
/* Send an SMS using Twilio. You can run this file 3 different ways:
*
* - Save it as sendnotifications.php and at the command line, run
* php sendnotifications.php
*
* - Upload it to a web host and load mywebhost.com/sendnotifications.php
* in a web browser.
* - Download a local server like WAMP, MAMP or XAMPP. Point the web root
* directory to the folder containing this file, and load
* localhost:8888/sendnotifications.php in a web browser.
*/
// Step 1: Download the Twilio-PHP library from twilio.com/docs/libraries,
// and move it into the folder containing this file.
require "Services/Twilio.php";
// Step 2: set our AccountSid and AuthToken from www.twilio.com/user/account
$AccountSid = "AC381c7e26c9a5de66108a8fd7f46f841a";
$AuthToken = "2616c3dda6897aa8f1dc3c0f346f18b1";
// Step 3: instantiate a new Twilio Rest Client
$client = new Services_Twilio($AccountSid, $AuthToken);
// Step 4: make an array of people we know, to send them a message.
// Feel free to change/add your own phone number and name here.
$people = array(
	//"+18183899197" => "Chris",
	"+13105031577" => "Todd",
	//"+14246341622" => "Mike",
);
// Step 5: Loop over all our friends. $number is a phone number above, and
// $name is the name next to it
foreach ($people as $number => $name) {
	$sms = $client->account->sms_messages->create(
		// Step 6: Change the 'From' number below to be a valid Twilio number
		// that you've purchased, or the Sandbox number
		"+14155992671",
		// the number we are sending to - Any phone number
		$number,
		// the sms body
		"Hey $name, Monkey Party at 6PM. Bring Bananas!"
	);
	// Display a confirmation message on the screen
	echo "Sent message to $name";
}