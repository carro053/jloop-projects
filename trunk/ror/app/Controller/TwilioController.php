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
					$text = 'Thank you, '.$user['TwilioUser']['name'].', for updating your name! Text "answer" to play.';
				} else {
					$text = 'Enter your name';
				}
			} else {
				$conversation = array(
					0 => array(
						'check' => 'answer',
						'right_text' => 'Please text the word "chicken"',
						'wrong_text' => 'To play, text the word "answer"'
					),
					1 => array(
						'check' => 'chicken',
						'right_text' => 'Great job '.$user['TwilioUser']['name'].'! Please text the word "bacon"',
						'wrong_text' => 'Sorry, that is incorrect. Text "chicken"'
					),
					2 => array(
						'check' => 'bacon',
						'right_text' => 'Excellent '.$user['TwilioUser']['name'].'! Please text the word "avocado"',
						'wrong_text' => 'Sorry, that is incorrect. Text "bacon"'
					),
					3 => array(
						'check' => 'avocado',
						'right_text' => 'Awesome '.$user['TwilioUser']['name'].'! Please text the word "bread"',
						'wrong_text' => 'Sorry, that is incorrect. Text "avocado"'
					),
					4 => array(
						'check' => 'bread',
						'right_text' => 'Fantastic '.$user['TwilioUser']['name'].'! You are entered to win this weeks prize!',
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
			}
		} else {
			echo 'Not SMS';
			die;
		}
		$this->set('text', $text);
		//$this->log($_REQUEST, 'debug');
	}
	
	public function test() {
		$users = $this->TwilioUser->find('all');
		echo '<pre>';
		print_r($users);
		echo '</pre>';
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
				"Hey $name, want to find out what I ate for lunch? Text \"34734314 answer\" to begin!"
			);
			
			echo "Sent message to $name";
		}
		die;
	}
	
	public function sms() {
		header("content-type: text/xml");
		echo '<?xml version="1.0" encoding="UTF-8"?>'."\n";
		
		if(isset($_REQUEST['From']) && !empty($_REQUEST['From'])) {
			$user = $this->TwilioUser->findByNumber($_REQUEST['From']);
			if(!$user) {
				$this->TwilioUser->create();
				$user['TwilioUser']['number'] = $_REQUEST['From'];
				$this->TwilioUser->save($user);
				$text = 'Welcome! To enter: please text your answer from the orange box.';
			} elseif(empty($user['TwilioUser']['orange'])) {
				if(isset($_REQUEST['Body']) && !empty($_REQUEST['Body'])) {
					$user['TwilioUser']['orange'] = $_REQUEST['Body'];
					$this->TwilioUser->save($user);
					$text = 'Great job! Almost done: please text your answer from the blue box.';
				} else {
					$text = 'Please text your answer from the orange box.';
				}
			} elseif(empty($user['TwilioUser']['blue'])) {
				if(isset($_REQUEST['Body']) && !empty($_REQUEST['Body'])) {
					$user['TwilioUser']['blue'] = $_REQUEST['Body'];
					$this->TwilioUser->save($user);
					//look up user to see if in database to skip code part
					$text = 'Awesome! To complete your entry, please text the 5-digit code on the bottom of your form.';
				} else {
					$text = 'Please text your answer from the blue box.';
				}
			} elseif(empty($user['TwilioUser']['code'])) {
				if(isset($_REQUEST['Body']) && !empty($_REQUEST['Body'])) {
					$user['TwilioUser']['code'] = $_REQUEST['Body'];
					$this->TwilioUser->save($user);
					//look up user from codes in database
					$text = 'Thanks [NAME]. You are entered in this week\'s contest. Winners announced Feb 14.';
					//error message if user not found
					//Sorry, we could not find that code in our database.
				} else {
					$text = 'Please text the 5-digit code on the bottom of your form.';
				}
			} else {
				$text = 'You are already entered in this week\'s contest. Winners announced Feb 14.';
			}
		} else {
			echo 'Not SMS';
			die;
		}
		$this->set('text', $text);
		//$this->log($_REQUEST, 'debug');
	}
}