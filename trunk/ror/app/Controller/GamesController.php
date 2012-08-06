<?php

App::uses('AppController', 'Controller');
class GamesController extends AppController {
	public $name = 'Games';
	public $helpers = array('Html', 'Session');
	public $uses = array('Game','Question','QuestionVersion','GameSnapshot');
	
	public function beforeFilter()
	{
		$this->Auth->allow('json_data','play','play_question','play_version');
	}
	
	public function refresher()
	{
		echo '<meta http-equiv="refresh" content="60;url=/games/refresher"><h2>Here is a random number: '.(rand() % 100  + 1).'</h2>';
		exit;
	}
	
	public function index() {
		
		$this->Game->bindModel(array(
			'hasMany'=>array(
				'Question'=>array(
					'className'=>'Question',
					'foreignKey'=>'game_id',
					'conditions'=>'Question.deleted = 0'
				)
			)
		));
		$games = $this->Game->find('all');
		foreach($games as $key=>$game):
			$count = $this->QuestionVersion->find('count',array('conditions'=>'QuestionVersion.question_id IN (SELECT `id` FROM `questions` WHERE `game_id` = '.$game['Game']['id'].')'));
			$games[$key]['Game']['version'] = $count;		
		endforeach;
		$this->set('games',$games);
	}
	public function snapshots($game_id)
	{
		$this->Game->bindModel(array(
			'hasMany'=>array(
				'GameSnapshot'=>array(
					'className'=>'GameSnapshot',
					'foreignKey'=>'game_id',
					'order'=>'GameSnapshot.time DESC'
				)
			)
		));
		$game = $this->Game->findById($game_id);
		$this->set('game', $game);
	}
	public function add_snapshot($game_id,$version_id=0)
	{
		$this->set('game_id',$game_id);
		$this->set('version_id',$version_id);
		if(isset($this->data['GameSnapshot']))
		{
			$snapshot = $this->data;
			$snapshot['GameSnapshot']['note'] = htmlspecialchars($this->removeInvis(nl2br($snapshot['GameSnapshot']['note'])), ENT_QUOTES);
			if($version_id == 0)
			{
				$snapshot['GameSnapshot']['time'] = time();
				$count = $this->QuestionVersion->find('count',array('conditions'=>'QuestionVersion.question_id IN (SELECT `id` FROM `questions` WHERE `game_id` = '.$game_id.')'));
				$snapshot['GameSnapshot']['version'] = $count;
			}else{
				$version = $this->QuestionVersion->findById($version_id);
				$snapshot['GameSnapshot']['time'] = strtotime($version['QuestionVersion']['created']);
				$count = $this->QuestionVersion->find('count',array('conditions'=>'QuestionVersion.question_id IN (SELECT `id` FROM `questions` WHERE `game_id` = '.$game_id.') AND QuestionVersion.created <= "'.$version['QuestionVersion']['created'].'"'));
				$snapshot['GameSnapshot']['version'] = $count;
			}
			$snapshot['GameSnapshot']['published'] = 1;
			$snapshot['GameSnapshot']['game_id'] = $game_id;
			if($this->GameSnapshot->save($snapshot))
			{
				$this->redirect('/games/snapshots/'.$game_id);
			}
		}
	}
	
	public function edit_snapshot($game_id,$snapshot_id)
	{
		$this->set('game_id',$game_id);
		$this->set('snapshot_id',$snapshot_id);
		$snapshot = $this->GameSnapshot->findById($snapshot_id);
		if(isset($this->data['GameSnapshot']))
		{
			$snapshot = $this->data;
			$snapshot['GameSnapshot']['note'] = htmlspecialchars($this->removeInvis(nl2br($snapshot['GameSnapshot']['note'])), ENT_QUOTES);
			if($this->GameSnapshot->save($snapshot))
			{
				$this->redirect('/games/snapshots/'.$game_id);
			}
		} else {
			$snapshot['GameSnapshot']['note'] = str_replace('<br />','
',htmlspecialchars_decode($snapshot['GameSnapshot']['note'], ENT_QUOTES));
			$this->data = $snapshot;
		}
	}
	
	public function delete_snapshot($game_id,$snapshot_id)
	{
		$this->GameSnapshot->delete($snapshot_id);
		$this->redirect('/games/snapshots/'.$game_id);
	}
	
	public function publish_snapshot($game_id,$snapshot_id)
	{
		$snapshot['GameSnapshot']['id'] = $snapshot_id;
		$snapshot['GameSnapshot']['published'] = 1;
		$this->GameSnapshot->save($snapshot);
		$this->redirect('/games/snapshots/'.$game_id);
	}
	
	public function unpublish_snapshot($game_id,$snapshot_id)
	{
		$snapshot['GameSnapshot']['id'] = $snapshot_id;
		$snapshot['GameSnapshot']['published'] = 0;
		$this->GameSnapshot->save($snapshot);
		$this->redirect('/games/snapshots/'.$game_id);
	}
	
	public function add()
	{
		if(isset($this->data['Game']))
		{
			$game = $this->data;
			if($this->data['Game']['icon']['error'] == 0 && $this->data['Game']['icon']['size'] > 0) $game['Game']['has_icon'] = 1;
			if($this->Game->save($game))
			{
				if($this->data['Game']['icon']['error'] == 0 && $this->data['Game']['icon']['size'] > 0)
				{
					move_uploaded_file($this->data['Game']['icon']['tmp_name'], WWW_ROOT.'img'.DS.'game_icons'.DS.$this->Game->id.'.png');
				}
				$this->redirect('/games');
			}
		}
	}
	
	public function edit($game_id)
	{
		$game = $this->Game->findById($game_id);
		if(isset($this->data['Game']))
		{
			$game = $this->data;
			if($this->data['Game']['icon']['error'] == 0 && $this->data['Game']['icon']['size'] > 0) $game['Game']['has_icon'] = 1;
			if($this->Game->save($game))
			{
				if($this->data['Game']['icon']['error'] == 0 && $this->data['Game']['icon']['size'] > 0)
				{
					move_uploaded_file($this->data['Game']['icon']['tmp_name'], WWW_ROOT.'img'.DS.'game_icons'.DS.$game_id.'.png');
				}
				$this->redirect('/games');
			}
		} else {
			$this->data = $game;
		}
	}
	
	public function delete($game_id)
	{
		if($this->Game->delete($game_id))
			$this->Game->query('DELETE FROM `questions` WHERE `game_id` = '.$game_id);
		$this->redirect('/games');
	}
	
	public function version_history($game_id)
	{
		$game = $this->Game->findById($game_id);
		$this->set('game',$game);
		$this->QuestionVersion->bindModel(
			array(
				'belongsTo'=> array(
	        		'User' => array(
	            		'className' => 'User',
	            		'foreignKey' => 'user_id'
	        		)
	        	)
	        )
	    );
		$versions = $this->QuestionVersion->find('all',array('conditions'=>'QuestionVersion.question_id IN (SELECT `id` FROM `questions` WHERE `game_id` = '.$game_id.')','order'=>'QuestionVersion.created DESC','recursive'=>2));
		$this->set('versions',$versions);
	}
	
	
	
	public function play_version($game_id,$snapshot_id) {
		$snapshot = $this->GameSnapshot->findById($snapshot_id);
		if(isset($snapshot['GameSnapshot']['id']) && $snapshot['GameSnapshot']['published'] == 1)
		{
			$this->layout = false;
			$this->set('preview_timers',0);
			$this->set('game',$this->Game->findById($game_id));
			$this->set('snapshot',$snapshot['GameSnapshot']['time']);
		}else{
			echo '<h2>This version of the game is no longer available.</h2>';
			exit;
		}
		$this->render('play');
	}
	
	public function play($game_id,$snapshot=0) {
		$this->layout = false;
		$this->set('preview_timers',0);
		$this->set('game',$this->Game->findById($game_id));
		if($snapshot > 0) $this->set('snapshot',$snapshot);
	}
	
	public function preview($game_id) {
		$this->layout = false;
		$this->set('preview_timers',1);
		$this->set('game',$this->Game->findById($game_id));
		$this->render('play');
	}
	
	public function play_question($game_id,$question_id,$version_id=0) {
		$this->layout = false;
		$this->set('preview_timers',0);
		$this->set('game',$this->Game->findById($game_id));
		if($version_id > 0)
		{
			$this->set('question',$this->QuestionVersion->findById($version_id));
		}else{
			$this->set('question',$this->QuestionVersion->find('first',array('conditions'=>'QuestionVersion.question_id = '.$question_id,'order'=>'QuestionVersion.created DESC')));
		}
		$this->render('play');
	}
	
	public function preview_question($game_id,$question_id,$version_id=0) {
		$this->layout = false;
		$this->set('preview_timers',1);
		$this->set('game',$this->Game->findById($game_id));
		if($version_id > 0)
		{
			$this->set('question',$this->QuestionVersion->findById($version_id));
		}else{
			$this->set('question',$this->QuestionVersion->find('first',array('conditions'=>'QuestionVersion.question_id = '.$question_id,'order'=>'QuestionVersion.created DESC')));
		}
		$this->render('play');
	}
	
	public function export($game_id,$time=0)
	{
		$this->layout = false;
		if($time > 0)
		{
			$this->Question->bindModel(array(
				'hasMany'=>array(
					'QuestionVersion'=>array(
						'className'=>'QuestionVersion',
						'foreignKey'=>'question_id',
						'order'=>'QuestionVersion.created DESC',
						'limit'=>1,
						'conditions'=>'QuestionVersion.created <= "'.date('Y-m-d H:i:s',$time).'"'
					)
				)
			));
			$this->Game->bindModel(array(
				'hasMany'=>array(
					'Question'=>array(
						'className'=>'Question',
						'foreignKey'=>'game_id',
						'order'=>'Question.order ASC'
					)
				)
			));
			
			$snap = $this->GameSnapshot->find('first',array('conditions'=>'GameSnapshot.time = '.$time.' AND GameSnapshot.game_id = '.$game_id));
			if(isset($snap['GameSnapshot']['id'])) $this->set('snapshot',$snap);
		}else{
			$this->Question->bindModel(array(
				'hasMany'=>array(
					'QuestionVersion'=>array(
						'className'=>'QuestionVersion',
						'foreignKey'=>'question_id',
						'order'=>'QuestionVersion.created DESC',
						'limit'=>1
					)
				)
			));
			$this->Game->bindModel(array(
				'hasMany'=>array(
					'Question'=>array(
						'className'=>'Question',
						'foreignKey'=>'game_id',
						'order'=>'Question.order ASC'
					)
				)
			));
		}
		$this->set('game',$this->Game->find('first',array('conditions'=>'Game.id = '.$game_id,'recursive'=>2)));
	}
	
	public function import($snapshot_id)
	{
		
		App::import('Vendor', 'RestRequest', array('file' => 'RestRequest.inc.php'));
		$request = new RestRequest('http://admin:MyAdminPass87@50.56.194.198:8282/RingorangWebService/rservice/app/getList?count=1000&offset=0', 'GET');
		$request->execute();
		$response = $request->getResponseBody();
		$response = simplexml_load_string($response);
		$hosts = array();
		foreach($response->list as $ahost):
			$hosts[intval($ahost->id)] = strval($ahost->name);
		endforeach;
		$this->set('hosts',$hosts);
		$this->set('snapshot_id',$snapshot_id);
	}
	
	public function import_to_host($snapshot_id)
	{
		set_time_limit(0);
		App::import('Vendor', 'RestRequest', array('file' => 'RestRequest.inc.php'));
		
		$host_id = $this->data['Game']['host_id'];
		
		$snapshot = $this->GameSnapshot->findById($snapshot_id);
		$this->Question->bindModel(array(
			'hasMany'=>array(
				'QuestionVersion'=>array(
					'className'=>'QuestionVersion',
					'foreignKey'=>'question_id',
					'order'=>'QuestionVersion.created DESC',
					'limit'=>1,
					'conditions'=>'QuestionVersion.created <= "'.date('Y-m-d H:i:s',$snapshot['GameSnapshot']['time']).'"'
				)
			)
		));
		$this->Game->bindModel(array(
			'hasMany'=>array(
				'Question'=>array(
					'className'=>'Question',
					'foreignKey'=>'game_id',
					'order'=>'Question.order ASC'
				)
			)
		));
		$game = $this->Game->find('first',array('conditions'=>'Game.id = '.$snapshot['GameSnapshot']['game_id'],'recursive'=>2));
		$gameXML = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<gameExtended>
	<application>
		<id>'.$host_id.'</id>
	</application>
	<beginDate>1342767600000</beginDate>
	<canUserUnsubscribe>true</canUserUnsubscribe>
	<clueTimer>30</clueTimer>
	<dayStartTime>1342796400000</dayStartTime>
	<dayStopTime>1342850400000</dayStopTime>
	<descr>'.$snapshot['GameSnapshot']['note'].' | Imported: '.date('F jS, Y').'</descr>
	<endDate>1343199600000</endDate>
	<id>0</id>
	<insightTimer>30</insightTimer>
	<isAgeRequired>false</isAgeRequired>
	<isRecurringGame>false</isRecurringGame>
	<makeupPercent>75</makeupPercent>
	<maxDallions>300</maxDallions>
	<minDallions>100</minDallions>
	<name>'.$game['Game']['title'].'</name>
	<questionTimer>30</questionTimer>
	<state>Active</state>
</gameExtended>';

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "http://50.56.194.198/RingorangWebService/rservice/game/create");
		curl_setopt($ch, CURLOPT_PORT, 8282);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/xml", "Content-Length: ".strlen($gameXML)));
		curl_setopt($ch, CURLOPT_VERBOSE, true);
		curl_setopt($ch, CURLOPT_USERPWD, "admin:MyAdminPass87");
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $gameXML);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
		$result = curl_exec($ch);
		curl_close($ch);
		$result = simplexml_load_string($result);
		$game_id = $result->l;
		
		foreach($game['Question'] as $quest):
			if(isset($quest['QuestionVersion'][0]['id']) && $quest['QuestionVersion'][0]['deleted'] == 0) {
				$question['Question'] = $quest['QuestionVersion'][0];
				$question['Question']['id'] = $quest['id'];
				$data = array();
				unset($data);
				$data = array();
				if($question['Question']['answer_type'] == 'image')
				{
					$data['type'] = 'PictureQuestion';
					$data['answer'] = $question['Question']['correct_answer'];
					$data['answer1Image'] = WWW_ROOT.'img'.DS.'answers'.DS.$question['Question']['id'].'-1-O-'.$quest['QuestionVersion'][0]['id'].'.png';
					$data['answer2Image'] = WWW_ROOT.'img'.DS.'answers'.DS.$question['Question']['id'].'-2-O-'.$quest['QuestionVersion'][0]['id'].'.png';
					$data['answer3Image'] = WWW_ROOT.'img'.DS.'answers'.DS.$question['Question']['id'].'-3-O-'.$quest['QuestionVersion'][0]['id'].'.png';
					$data['answer4Image'] = WWW_ROOT.'img'.DS.'answers'.DS.$question['Question']['id'].'-4-O-'.$quest['QuestionVersion'][0]['id'].'.png';
					$data['answer1'] = '';
					$data['answer2'] = '';
					$data['answer3'] = '';
					$data['answer4'] = '';
					//image stuff
				}else{
					if(trim($question['Question']['answer_1_text']) == '' && trim($question['Question']['answer_2_text']) == '')
					{
						$data['type'] = 'YesNoQuestion';
						if($question['Question']['correct_answer'] == 2)
						{
							$data['answer'] = '0';
						}else{
							$data['answer'] = '1';
						}					
						$data['answer1'] = $question['Question']['answer_3_text'];
						$data['answer2'] = $question['Question']['answer_4_text'];
						$data['answer3'] = '';
						$data['answer4'] = '';
					}else{
						$data['type'] = 'SimpleQuestion';
						$data['answer'] = $question['Question']['correct_answer'];
						$data['answer1'] = $question['Question']['answer_1_text'];
						$data['answer2'] = $question['Question']['answer_2_text'];
						$data['answer3'] = $question['Question']['answer_3_text'];
						$data['answer4'] = $question['Question']['answer_4_text'];
					}
				}
				$data['clueType'] = ucwords($question['Question']['clue_type']);
				$data['questionType'] = ucwords($question['Question']['question_type']);
				$data['insightType'] = ucwords($question['Question']['insight_type']);
				
				if($question['Question']['clue_type'] == 'text')
				{
					$data['clueText'] = $question['Question']['clue_text'];
				}else{
					$data['clueText'] = null;
					$data['clueImage'] = WWW_ROOT.'img'.DS.'clues'.DS.$question['Question']['id'].'-O-'.$quest['QuestionVersion'][0]['id'].'.png';
					//image stuff goes here
				}
				
				if($question['Question']['question_type'] == 'text')
				{
					$data['question'] = $question['Question']['question_text'];
				}else{
					$data['question'] = null;
					$data['questionImage'] = WWW_ROOT.'img'.DS.'questions'.DS.$question['Question']['id'].'-O-'.$quest['QuestionVersion'][0]['id'].'.png';
					//image stuff goes here
				}
				
				if($question['Question']['insight_type'] == 'text')
				{
					$data['insightText'] = $question['Question']['insight_text'];
				}else{
					$data['insightText'] = null;
					$data['insightImage'] = WWW_ROOT.'img'.DS.'insights'.DS.$question['Question']['id'].'-O-'.$quest['QuestionVersion'][0]['id'].'.png';
					//image stuff goes here
				}
				if($data['type'] == 'PictureQuestion')
				{
					$data['state'] = 'Draft';
				}else{
					$data['state'] = 'Active';
				}
				$data['gameId'] = $game_id;
				$data['lang'] = 'en_us';
				
				$xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
	<question>
		<answer>'.$data['answer'].'</answer>
		<answer1>'.$data['answer1'].'</answer1>
		<answer2>'.$data['answer2'].'</answer2>
		<answer3>'.$data['answer3'].'</answer3>
		<answer4>'.$data['answer4'].'</answer4>
		<id>0</id>
		<clueText>'.$data['clueText'].'</clueText>
		<question>'.$data['question'].'</question>
		<game>
			<id>'.$data['gameId'].'</id>
		</game>
		<insightText>'.$data['insightText'].'</insightText>
		<langs>en_us</langs>
		<type>'.$data['type'].'</type>
		<state>'.$data['state'].'</state>
	</question>';
	
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, "http://50.56.194.198/RingorangWebService/rservice/game/createQuestion");
				curl_setopt($ch, CURLOPT_PORT, 8282);
				curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/xml", "Content-Length: ".strlen($xml)));
				curl_setopt($ch, CURLOPT_VERBOSE, true);
				curl_setopt($ch, CURLOPT_USERPWD, "admin:MyAdminPass87");
				curl_setopt($ch, CURLOPT_POST, true);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
	  			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
				$result = curl_exec($ch);
				curl_close($ch);
				$result = simplexml_load_string($result);
				if($result->l > 0)
				{
					$request = new RestRequest('http://admin:MyAdminPass87@50.56.194.198:8282/RingorangWebService/rservice/game/getQuestionDetails/'.$result->l, 'GET');
					$request->execute();
					$response = $request->getResponseBody();
					$response = simplexml_load_string($response);
					$clueImageId = $response->clueImage->id;
					$insightImageId = $response->insightImage->id;
					$answer1ImageId = $response->pictureAnswer1->id;
					$answer2ImageId = $response->pictureAnswer2->id;
					$answer3ImageId = $response->pictureAnswer3->id;
					$answer4ImageId = $response->pictureAnswer4->id;
					$questionImageId = $response->questionImage->id;
					if(isset($data['answer1Image']))
					{
						$ch = curl_init();
						curl_setopt($ch, CURLOPT_URL, "http://50.56.194.198/RingorangWebService/rservice/custom/updateCustomPicture/".$answer1ImageId);
						curl_setopt($ch, CURLOPT_PORT, 8282);
						curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: image/png", "Content-Length: ".strlen(file_get_contents($data['answer1Image']))));
						curl_setopt($ch, CURLOPT_VERBOSE, true);
		        		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible;)");
						curl_setopt($ch, CURLOPT_USERPWD, "admin:MyAdminPass87");
						curl_setopt($ch, CURLOPT_POST, true);
						curl_setopt($ch, CURLOPT_POSTFIELDS, file_get_contents($data['answer1Image']));
						$result = curl_exec($ch);
						curl_close($ch);
					}
					if(isset($data['answer2Image']))
					{
						$ch = curl_init();
						curl_setopt($ch, CURLOPT_URL, "http://50.56.194.198/RingorangWebService/rservice/custom/updateCustomPicture/".$answer2ImageId);
						curl_setopt($ch, CURLOPT_PORT, 8282);
						curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: image/png", "Content-Length: ".strlen(file_get_contents($data['answer2Image']))));
						curl_setopt($ch, CURLOPT_VERBOSE, true);
		        		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible;)");
						curl_setopt($ch, CURLOPT_USERPWD, "admin:MyAdminPass87");
						curl_setopt($ch, CURLOPT_POST, true);
						curl_setopt($ch, CURLOPT_POSTFIELDS, file_get_contents($data['answer2Image']));
						$result = curl_exec($ch);
						curl_close($ch);
					}
					
					if(isset($data['answer3Image']))
					{
						$ch = curl_init();
						curl_setopt($ch, CURLOPT_URL, "http://50.56.194.198/RingorangWebService/rservice/custom/updateCustomPicture/".$answer3ImageId);
						curl_setopt($ch, CURLOPT_PORT, 8282);
						curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: image/png", "Content-Length: ".strlen(file_get_contents($data['answer3Image']))));
						curl_setopt($ch, CURLOPT_VERBOSE, true);
						curl_setopt($ch, CURLOPT_USERPWD, "admin:MyAdminPass87");
						curl_setopt($ch, CURLOPT_POST, true);
						curl_setopt($ch, CURLOPT_POSTFIELDS, file_get_contents($data['answer3Image']));
						$result = curl_exec($ch);
						curl_close($ch);
					}
					
					if(isset($data['answer4Image']))
					{
						$ch = curl_init();
						curl_setopt($ch, CURLOPT_URL, "http://50.56.194.198/RingorangWebService/rservice/custom/updateCustomPicture/".$answer4ImageId);
						curl_setopt($ch, CURLOPT_PORT, 8282);
						curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: image/png", "Content-Length: ".strlen(file_get_contents($data['answer4Image']))));
						curl_setopt($ch, CURLOPT_VERBOSE, true);
						curl_setopt($ch, CURLOPT_USERPWD, "admin:MyAdminPass87");
						curl_setopt($ch, CURLOPT_POST, true);
						curl_setopt($ch, CURLOPT_POSTFIELDS, file_get_contents($data['answer4Image']));
						$result = curl_exec($ch);
						curl_close($ch);
					}
					
					if(isset($data['clueImage']))
					{
						$ch = curl_init();
						curl_setopt($ch, CURLOPT_URL, "http://50.56.194.198/RingorangWebService/rservice/custom/updateCustomPicture/".$clueImageId);
						curl_setopt($ch, CURLOPT_PORT, 8282);
						curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: image/png", "Content-Length: ".strlen(file_get_contents($data['clueImage']))));
						curl_setopt($ch, CURLOPT_VERBOSE, true);
						curl_setopt($ch, CURLOPT_USERPWD, "admin:MyAdminPass87");
						curl_setopt($ch, CURLOPT_POST, true);
						curl_setopt($ch, CURLOPT_POSTFIELDS, file_get_contents($data['clueImage']));
						$result = curl_exec($ch);
						curl_close($ch);
					}
					
					if(isset($data['questionImage']))
					{
						$ch = curl_init();
						curl_setopt($ch, CURLOPT_URL, "http://50.56.194.198/RingorangWebService/rservice/custom/updateCustomPicture/".$questionImageId);
						curl_setopt($ch, CURLOPT_PORT, 8282);
						curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: image/png", "Content-Length: ".strlen(file_get_contents($data['questionImage']))));
						curl_setopt($ch, CURLOPT_VERBOSE, true);
						curl_setopt($ch, CURLOPT_USERPWD, "admin:MyAdminPass87");
						curl_setopt($ch, CURLOPT_POST, true);
						curl_setopt($ch, CURLOPT_POSTFIELDS, file_get_contents($data['questionImage']));
						$result = curl_exec($ch);
						curl_close($ch);
					}
					
					if(isset($data['insightImage']))
					{
						$ch = curl_init();
						curl_setopt($ch, CURLOPT_URL, "http://50.56.194.198/RingorangWebService/rservice/custom/updateCustomPicture/".$insightImageId);
						curl_setopt($ch, CURLOPT_PORT, 8282);
						curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: image/png", "Content-Length: ".strlen(file_get_contents($data['insightImage']))));
						curl_setopt($ch, CURLOPT_VERBOSE, true);
						curl_setopt($ch, CURLOPT_USERPWD, "admin:MyAdminPass87");
						curl_setopt($ch, CURLOPT_POST, true);
						curl_setopt($ch, CURLOPT_POSTFIELDS, file_get_contents($data['insightImage']));
						$result = curl_exec($ch);
						curl_close($ch);
					}
				}
			}
		endforeach;
		echo 'Success!<br /><a href="http://50.56.194.198:8383/RingorangAdminPanel-0.1/question/unapprovedList?currentGameId='.$game_id.'">Link to Questions</a>';
		exit;
	}
	
	public function json_data($game_id,$snapshot=0,$version_id=0) {
		$this->layout = false;
		if($snapshot > 0)
		{
			$this->Question->bindModel(array(
				'hasMany'=>array(
					'QuestionVersion'=>array(
						'className'=>'QuestionVersion',
						'foreignKey'=>'question_id',
						'order'=>'QuestionVersion.created DESC',
						'limit'=>1,
						'conditions'=>'QuestionVersion.created <= "'.date('Y-m-d H:i:s',$snapshot).'"'
					)
				)
			));
			$this->Game->bindModel(array(
				'hasMany'=>array(
					'Question'=>array(
						'className'=>'Question',
						'foreignKey'=>'game_id',
						'order'=>'Question.order ASC'
					)
				)
			));
		}elseif($version_id > 0)
		{
			$version = $this->QuestionVersion->findById($version_id);
			$this->Question->bindModel(array(
				'hasMany'=>array(
					'QuestionVersion'=>array(
						'className'=>'QuestionVersion',
						'foreignKey'=>'question_id',
						'order'=>'QuestionVersion.created DESC',
						'limit'=>1,
						'conditions'=>'QuestionVersion.id = '.$version_id
					)
				)
			));
			$this->Game->bindModel(array(
				'hasMany'=>array(
					'Question'=>array(
						'className'=>'Question',
						'foreignKey'=>'game_id',
						'order'=>'Question.order ASC',
						'conditions'=>'Question.id = '.$version['QuestionVersion']['question_id']
					)
				)
			));
		}else{
			$this->Question->bindModel(array(
				'hasMany'=>array(
					'QuestionVersion'=>array(
						'className'=>'QuestionVersion',
						'foreignKey'=>'question_id',
						'order'=>'QuestionVersion.created DESC',
						'limit'=>1
					)
				)
			));
			$this->Game->bindModel(array(
				'hasMany'=>array(
					'Question'=>array(
						'className'=>'Question',
						'foreignKey'=>'game_id',
						'order'=>'Question.order ASC'
					)
				)
			));
		}
		$this->set('game',$this->Game->find('first',array('conditions'=>'Game.id = '.$game_id,'recursive'=>2)));
	}
	
	function check_image_folders() {
		echo '<hr>';
		echo "/img/clues<br>";
		$formatted_file_count = 0;
		$original_file_count = 0;
		$dir = ROOT.'/app/webroot/img/clues';
		if($resource = opendir($dir)) {
			while(($file = readdir($resource)) !== false) {
				$parts = explode('.', $file);
				if(is_numeric($parts[0]))
					$formatted_file_count++;
				elseif(preg_match('/-O$/', $parts[0]))
					$original_file_count++;
			}
			closedir($resource);
		}
		echo "formatted files: ".$formatted_file_count."<br>";
		echo "original files: ".$original_file_count."<br>";
		
		echo '<hr>';
		echo "/img/questions<br>";
		$formatted_file_count = 0;
		$original_file_count = 0;
		$dir = ROOT.'/app/webroot/img/questions';
		if($resource = opendir($dir)) {
			while(($file = readdir($resource)) !== false) {
				$parts = explode('.', $file);
				if(is_numeric($parts[0]))
					$formatted_file_count++;
				elseif(preg_match('/-O$/', $parts[0]))
					$original_file_count++;
			}
			closedir($resource);
		}
		echo "formatted files: ".$formatted_file_count."<br>";
		echo "original files: ".$original_file_count."<br>";
		
		echo '<hr>';
		echo "/img/insights<br>";
		$formatted_file_count = 0;
		$original_file_count = 0;
		$dir = ROOT.'/app/webroot/img/insights';
		if($resource = opendir($dir)) {
			while(($file = readdir($resource)) !== false) {
				$parts = explode('.', $file);
				if(is_numeric($parts[0]))
					$formatted_file_count++;
				elseif(preg_match('/-O$/', $parts[0]))
					$original_file_count++;
			}
			closedir($resource);
		}
		echo "formatted files: ".$formatted_file_count."<br>";
		echo "original files: ".$original_file_count."<br>";
		
		echo '<hr>';
		echo "/img/answers<br>";
		$formatted_file_count = 0;
		$original_file_count = 0;
		$dir = ROOT.'/app/webroot/img/answers';
		if($resource = opendir($dir)) {
			while(($file = readdir($resource)) !== false) {
				$parts = explode('.', $file);
				if(substr_count($parts[0], "-") == 1)
					$formatted_file_count++;
				elseif(preg_match('/-O$/', $parts[0]))
					$original_file_count++;
			}
			closedir($resource);
		}
		echo "formatted files: ".$formatted_file_count."<br>";
		echo "original files: ".$original_file_count."<br>";
		
		die;
	}
	
}
?>