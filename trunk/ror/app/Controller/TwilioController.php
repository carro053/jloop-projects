<?php
class TwilioController extends AppController {

	public $name = 'Twilio';
	
	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow('*');
		App::import('Vendor', 'Twilio', array('file' => 'Twilio' . DS . 'Services' . DS . 'Twilio.php'));
	}
	
	public function index() {
		echo 123;
		die;
	}
	
	public function conversation() {
		$counter = $this->Session->read('Counter');
		if(!$counter)
			$counter = 0;
		
		$counter++;
		
		$this->Session->write('Counter', $counter);
		
		$this->log("Coutner: $counter", 'debug');
		die;
	}
	
	public function sendTest() {
		$AccountSid = "AC381c7e26c9a5de66108a8fd7f46f841a";
		$AuthToken = "2616c3dda6897aa8f1dc3c0f346f18b1";
		
		$client = new Services_Twilio($AccountSid, $AuthToken);
		
		$people = array(
			//"+18183899197" => "Chris",
			"+13105031577" => "Todd",
			//"+14246341622" => "Mike",
		);
		
		foreach ($people as $number => $name) {
			$sms = $client->account->sms_messages->create(
				"+14155992671",
				$number,
				"Hey $name, Monkey Party at 6PM. Bring Bananas!"
			);
			
			echo "Sent message to $name";
		}
	}
}