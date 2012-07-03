<?php
class TwilioController extends AppController {

	public $name = 'Twilio';
	public $uses = array(
		'TwilioUser',
		
	);
	public $layout = false;
	
	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow('*');
	}
	
	public function conversation() {
		header("content-type: text/xml");
		echo '<?xml version="1.0" encoding="UTF-8"?>'."\n";
		if(isset($_REQUEST['From']) && !empty($_REQUEST['From'])) {
			$user = $this->TwilioUser->findByNumber($_REQUEST['From']);
			if(!$user) {
				$this->TwilioUser->create();
				$user['TwilioUser']['number'] = $_REQUEST['From'];
				$this->TwilioUser->save($user);
				$text = 'Please enter your name.';
			} elseif(empty($user['TwilioUser']['name'])) {
				if(isset($_REQUEST['Body']) && !empty($_REQUEST['Body'])) {
					$user['TwilioUser']['name'] = $_REQUEST['Body'];
					$this->TwilioUser->save($user);
					$text = 'Thank you for updating your name!';
				} else {
					$text = 'Enter your name';
				}
			}
		} else {
			echo 'Not SMS';
			die;
		}
		
		$conversation = array(
			0 => array(
				'check' => 'answer',
				'right_text' => 'Please text the word "chicken"',
				'wrong_text' => 'To play, text the word "answer"'
			),
			1 => array(
				'check' => 'chicken',
				'right_text' => 'Great! Please text the word "bacon"',
				'wrong_text' => 'Sorry, that is incorrect. Text "chicken"'
			),
			2 => array(
				'check' => 'bacon',
				'right_text' => 'Excellent! Please text the word "avocado"',
				'wrong_text' => 'Sorry, that is incorrect. Text "bacon"'
			),
			3 => array(
				'check' => 'avocado',
				'right_text' => 'Awesome! Please text the word "bread"',
				'wrong_text' => 'Sorry, that is incorrect. Text "avocado"'
			),
			4 => array(
				'check' => 'bread',
				'right_text' => 'Fantastic! You are entered to win this weeks prize!',
				'wrong_text' => 'Sorry, that is incorrect. Text "bread"'
			),
		);
		
		if(!isset($conversation[$user['TwilioUser']['stage']])) {
			if($user['TwilioUser']['stage'] >= count($conversation)) {
				$text = 'You are already entered to win this week!';
			} else {
				$text = 'Something went wrong...';
			}
		}elseif($conversation[$user['TwilioUser']['stage']]['check'] == $_REQUEST['Body']) {
			$text = $conversation[$user['TwilioUser']['stage']]['right_text'];
			$user['TwilioUser']['stage']++;
			$this->TwilioUser->save($user);
		} else {
			$text = $conversation[$user['TwilioUser']['stage']]['wrong_text'];
		}
		
		$this->set('text', $text);
		//$this->log($_REQUEST, 'debug');
	}
	
	public function test() {
		$users = $this->TwilioUser->find('all');
		print_r($users);
		die;
	}
	
	public function sendTest() {
		App::import('Vendor', 'Twilio', array('file' => 'Twilio' . DS . 'Services' . DS . 'Twilio.php'));
		
		$AccountSid = "AC381c7e26c9a5de66108a8fd7f46f841a";
		$AuthToken = "2616c3dda6897aa8f1dc3c0f346f18b1";
		
		$client = new Services_Twilio($AccountSid, $AuthToken);
		
		$people = array(
			//"+18183899197" => "Chris",
			//"+13105031577" => "Todd",
			"+14246341622" => "Mike",
		);
		
		foreach ($people as $number => $name) {
			$sms = $client->account->sms_messages->create(
				"+14155992671",
				$number,
				"Hey $name, want to find out what I ate for lunch? Text \"34734314 answer\" to begin your EPIC journey!"
			);
			
			echo "Sent message to $name";
		}
		die;
	}
}