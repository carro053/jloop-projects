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
		$this->getTwilioUser();
		App::import('Vendor', 'Twilio', array('file' => 'Twilio' . DS . 'Services' . DS . 'Twilio.php'));
	}
	
	private function getTwilioUser() {
		if(isset($_REQUEST['From'])) {
			$user = $this->TwilioUser->findByNumber($_REQUEST['From']);
			if(!$user) {
				$this->registerTwilioUser();
			} elseif(empty($user['TwilioUser']['name'])) {
				$this->updateTwilioUser();
			}
		} else {
			echo 'Not SMS';
			//die;
			$this->updateTwilioUser();
		}
	}
	
	public function registerTwilioUser() {
		$this->log('registerTwilioUser', 'debug');
		$this->TwilioUser->create();
		$user['TwilioUser']['number'] = $_REQUEST['From'];
		$this->TwilioUser->save($user);
		$this->render('registerTwilioUser');
	}
	
	public function updateTwilioUser() {
		$this->log('updateTwilioUser', 'debug');
		$this->log($user, 'debug');
		$this->render('updateTwilioUser');
	}
	
	public function conversation($destroy=false) {
		$this->log('conversation', 'debug');
		$conversation = array(
			0 => array(
				'message' => 'Text "answer"',
				'answer' => 'answer',
				'error' => 'Please try again. Text "answer"'
			),
			1 => array(
				'message' => 'Please text the words/numbers in the orange box. [123]',
				'answer' => '123',
				'error' => 'Please try again. [123]'
			),
			2 => array(
				'message' => 'Great! Now text "321"',
				'answer' => '321',
				'error' => 'Please try again. [321]'
			),
			3 => array(
				'message' => 'Fantastic! You are entered to win this week\'s prize!',
				'answer' => '987643',
				'error' => 'You are already entered to win.'
			),
		);
		$counter = $this->Session->read('Counter');
		if(!$counter)
			$counter = 0;
		
		if($conversation[$counter]['answer'] == $_REQUEST['Body']) {
			$counter++;
			$text = $conversation[$counter]['message'];
		} else {
			$text = $conversation[$counter]['error'];
		}
		$this->set('text', $text);
		//$this->log($_REQUEST, 'debug');
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