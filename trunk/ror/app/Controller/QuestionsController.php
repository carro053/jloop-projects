<?php

App::uses('AppController', 'Controller');
class QuestionsController extends AppController {
	public $name = 'Questions';
	public $helpers = array('Html', 'Session');
	public $uses = array('Game','Question','QuestionVersion');
	
	
	
	function mexport($game_id)
	{
		App::import('Vendor', 'RestRequest', array('file' => 'RestRequest.inc.php'));
		
		$questions = $this->Question->find('all',array('conditions'=>'Question.game_id = '.$game_id,'limit'=>1));
		
		foreach($questions as $question):
			$data = array();
			unset($data);
			$data = array();
			if($question['Question']['answer_type'] == 'image')
			{
				$data['type'] = 'PictureQuestion';
				$data['answer'] = $question['Question']['correct_answer'];
				$data['answer1Image'] = WWW_ROOT.'img'.DS.'answers'.DS.$question['Question']['id'].'-1.png';
				$data['answer2Image'] = WWW_ROOT.'img'.DS.'answers'.DS.$question['Question']['id'].'-2.png';
				$data['answer3Image'] = WWW_ROOT.'img'.DS.'answers'.DS.$question['Question']['id'].'-3.png';
				$data['answer4Image'] = WWW_ROOT.'img'.DS.'answers'.DS.$question['Question']['id'].'-4.png';
				$data['answer'] = '';
				$data['answer1'] = '';
				$data['answer2'] = '';
				$data['answer3'] = '';
				$data['answer4'] = '';
				//image stuff
			}else{
				if(trim($question['Question']['answer_1_text']) == '' && trim($question['Question']['answer_2_text']) == '' && trim($question['Question']['answer_3_text']) == 'True' && trim($question['Question']['answer_4_text']) == 'False')
				{
					$data['type'] = 'YesNoQuestion';
					if($question['Question']['correct_answer'] == 2)
					{
						$data['answer'] = '0';
					}else{
						$data['answer'] = '1';
					}
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
				$data['clueImage'] = WWW_ROOT.'img'.DS.'clues'.DS.$question['Question']['id'].'.png';
				//image stuff goes here
			}
			
			if($question['Question']['question_type'] == 'text')
			{
				$data['question'] = $question['Question']['question_text'];
			}else{
				$data['question'] = null;
				$data['questionImage'] = WWW_ROOT.'img'.DS.'questions'.DS.$question['Question']['id'].'.png';
				//image stuff goes here
			}
			
			if($question['Question']['insight_type'] == 'text')
			{
				$data['insightText'] = $question['Question']['insight_text'];
			}else{
				$data['insightText'] = null;
				$data['insightImage'] = WWW_ROOT.'img'.DS.'insights'.DS.$question['Question']['id'].'.png';
				//image stuff goes here
			}
			$data['state'] = 'Draft';
			$data['gameId'] = 231;
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
			$request = new RestRequest('http://admin:MyAdminPass87@50.56.194.198:8282/RingorangWebService/rservice/game/getQuestionDetails/'.$result->l, 'GET');
			pr($data);
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
				echo strlen(file_get_contents($data['answer1Image']));
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
				echo strlen(file_get_contents($data['answer2Image']));
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
			
			
			echo '<hr>';
			
			//$request = new RestRequest('http://admin:MyAdminPass87@50.56.194.198:8282/RingorangWebService/rservice/game/createQuestion', 'POST',$xml);
			//$request->execute();
			//$response = $request->getResponseBody();
			//pr($request);
			
		endforeach;
		exit;
	}
	
	public function beforeFilter()
	{
		$this->set('status_options',array(
			'First Draft'=>'First Draft',
			'Design Needed'=>'Design Needed',
			'Second Draft'=>'Second Draft',
			'Client Review'=>'Client Review',
			'Client Approved'=>'Client Approved'
		));
	}	
	
	public function index($game_id,$status_filter=null)
	{
		$this->set('status_filter',$status_filter);
		$order = 'Question.order ASC';
		if($status_filter)
		{
			$order = 'Question.status = "'.$status_filter.'" DESC';
		}
		$this->Game->bindModel(array(
			'hasMany'=>array(
				'Question'=>array(
					'className'=>'Question',
					'foreignKey'=>'game_id',
					'order'=>$order,
					'conditions'=>'Question.deleted = 0'
				)
			)
		));
		$game = $this->Game->findById($game_id);
		$this->set('game', $game);
	}
	
	public function deleted($game_id)
	{
		$this->Game->bindModel(array(
			'hasMany'=>array(
				'Question'=>array(
					'className'=>'Question',
					'foreignKey'=>'game_id',
					'conditions'=>'Question.deleted = 1'
				)
			)
		));
		$game = $this->Game->findById($game_id);
		$this->set('game', $game);
	}
	
	function add($game_id,$preview=false)
	{
		if(isset($this->data['Question']))
		{
			$question = $this->data;
			$question['Question']['clue_text'] = htmlspecialchars($this->removeInvis(nl2br($question['Question']['clue_text'])), ENT_QUOTES);
			$question['Question']['question_text'] = htmlspecialchars($this->removeInvis(nl2br($question['Question']['question_text'])), ENT_QUOTES);
			$question['Question']['insight_text'] = htmlspecialchars($this->removeInvis(nl2br($question['Question']['insight_text'])), ENT_QUOTES);
			$question['Question']['prize_text'] = htmlspecialchars($this->removeInvis(nl2br($question['Question']['prize_text'])), ENT_QUOTES);
			$question['Question']['answer_1_text'] = htmlspecialchars($this->removeInvis(nl2br($question['Question']['answer_1_text'])), ENT_QUOTES);
			$question['Question']['answer_2_text'] = htmlspecialchars($this->removeInvis(nl2br($question['Question']['answer_2_text'])), ENT_QUOTES);
			$question['Question']['answer_3_text'] = htmlspecialchars($this->removeInvis(nl2br($question['Question']['answer_3_text'])), ENT_QUOTES);
			$question['Question']['answer_4_text'] = htmlspecialchars($this->removeInvis(nl2br($question['Question']['answer_4_text'])), ENT_QUOTES);
			$question['Question']['clue_note'] = htmlspecialchars($this->removeInvis(nl2br($question['Question']['clue_note'])), ENT_QUOTES);
			$question['Question']['question_note'] = htmlspecialchars($this->removeInvis(nl2br($question['Question']['question_note'])), ENT_QUOTES);
			$question['Question']['insight_note'] = htmlspecialchars($this->removeInvis(nl2br($question['Question']['insight_note'])), ENT_QUOTES);
			$question['Question']['prize_note'] = htmlspecialchars($this->removeInvis(nl2br($question['Question']['prize_note'])), ENT_QUOTES);
			$question['Question']['answer_1_note'] = htmlspecialchars($this->removeInvis(nl2br($question['Question']['answer_1_note'])), ENT_QUOTES);
			$question['Question']['answer_2_note'] = htmlspecialchars($this->removeInvis(nl2br($question['Question']['answer_2_note'])), ENT_QUOTES);
			$question['Question']['answer_3_note'] = htmlspecialchars($this->removeInvis(nl2br($question['Question']['answer_3_note'])), ENT_QUOTES);
			$question['Question']['answer_4_note'] = htmlspecialchars($this->removeInvis(nl2br($question['Question']['answer_4_note'])), ENT_QUOTES);
			$question['Question']['game_id'] = $game_id;
			$question['Question']['order'] = $this->Question->find('count',array('conditions'=>'Question.game_id = '.$game_id));
			if($this->Question->save($question))
			{
				if($this->data['Question']['clue_image']['error'] == 0 && $this->data['Question']['clue_image']['size'] > 0)
				{
					if(move_uploaded_file($this->data['Question']['clue_image']['tmp_name'], WWW_ROOT.'img'.DS.'clues'.DS.$this->Question->id.'-O.png'))
					{
						$this->generateClueInsightImage(WWW_ROOT.'img'.DS.'clues'.DS.$this->Question->id.'-O.png', WWW_ROOT.'img'.DS.'clues'.DS.$this->Question->id.'.png');
					}
				}
				if($this->data['Question']['question_image']['error'] == 0 && $this->data['Question']['question_image']['size'] > 0)
				{
					if(move_uploaded_file($this->data['Question']['question_image']['tmp_name'], WWW_ROOT.'img'.DS.'questions'.DS.$this->Question->id.'-O.png'))
					{
						$this->generateQuestionImage(WWW_ROOT.'img'.DS.'questions'.DS.$this->Question->id.'-O.png', WWW_ROOT.'img'.DS.'questions'.DS.$this->Question->id.'.png');
					}
				}
				if($this->data['Question']['insight_image']['error'] == 0 && $this->data['Question']['insight_image']['size'] > 0)
				{
					if(move_uploaded_file($this->data['Question']['insight_image']['tmp_name'], WWW_ROOT.'img'.DS.'insights'.DS.$this->Question->id.'-O.png'))
					{
						$this->generateClueInsightImage(WWW_ROOT.'img'.DS.'insights'.DS.$this->Question->id.'-O.png', WWW_ROOT.'img'.DS.'insights'.DS.$this->Question->id.'.png');
					}
				}
				if($this->data['Question']['answer_1_image']['error'] == 0 && $this->data['Question']['answer_1_image']['size'] > 0)
				{
					if(move_uploaded_file($this->data['Question']['answer_1_image']['tmp_name'], WWW_ROOT.'img'.DS.'answers'.DS.$this->Question->id.'-1-O.png'))
					{
						$this->generateAnswerImage(WWW_ROOT.'img'.DS.'answers'.DS.$this->Question->id.'-1-O.png', WWW_ROOT.'img'.DS.'answers'.DS.$this->Question->id.'-1.png');
					}
				}
				if($this->data['Question']['answer_2_image']['error'] == 0 && $this->data['Question']['answer_2_image']['size'] > 0)
				{
					if(move_uploaded_file($this->data['Question']['answer_2_image']['tmp_name'], WWW_ROOT.'img'.DS.'answers'.DS.$this->Question->id.'-2-O.png'))
					{
						$this->generateAnswerImage(WWW_ROOT.'img'.DS.'answers'.DS.$this->Question->id.'-2-O.png', WWW_ROOT.'img'.DS.'answers'.DS.$this->Question->id.'-2.png');
					}
				}
				if($this->data['Question']['answer_3_image']['error'] == 0 && $this->data['Question']['answer_3_image']['size'] > 0)
				{
					if(move_uploaded_file($this->data['Question']['answer_3_image']['tmp_name'], WWW_ROOT.'img'.DS.'answers'.DS.$this->Question->id.'-3-O.png'))
					{
						$this->generateAnswerImage(WWW_ROOT.'img'.DS.'answers'.DS.$this->Question->id.'-3-O.png', WWW_ROOT.'img'.DS.'answers'.DS.$this->Question->id.'-3.png');
					}
				}
				if($this->data['Question']['answer_4_image']['error'] == 0 && $this->data['Question']['answer_4_image']['size'] > 0)
				{
					if(move_uploaded_file($this->data['Question']['answer_4_image']['tmp_name'], WWW_ROOT.'img'.DS.'answers'.DS.$this->Question->id.'-4-O.png'))
					{
						$this->generateAnswerImage(WWW_ROOT.'img'.DS.'answers'.DS.$this->Question->id.'-4-O.png', WWW_ROOT.'img'.DS.'answers'.DS.$this->Question->id.'-4.png');
					}
				}
				if($this->data['Question']['prize_image']['error'] == 0 && $this->data['Question']['prize_image']['size'] > 0)
				{
					move_uploaded_file($this->data['Question']['prize_image']['tmp_name'], WWW_ROOT.'img'.DS.'prizes'.DS.$this->Question->id.'.png');
				}
				$this->version_up_question($this->Question->id);
				if($preview)
				{
					$this->redirect('/games/preview_question/'.$game_id.'/'.$this->Question->id);
				}else{
					$this->redirect('/questions/index/'.$game_id);
				}
			}
		}
		$this->set('game_id', $game_id);
	}
	
	function edit($question_id,$preview=false)
	{
		$question = $this->Question->findById($question_id);
		$game_id = $question['Question']['game_id'];
		$this->set('game_id',$game_id);
		if(isset($this->data['Question']))
		{
			$question = $this->data;
			$question['Question']['clue_text'] = htmlspecialchars($this->removeInvis(nl2br($question['Question']['clue_text'])), ENT_QUOTES);
			$question['Question']['question_text'] = htmlspecialchars($this->removeInvis(nl2br($question['Question']['question_text'])), ENT_QUOTES);
			$question['Question']['insight_text'] = htmlspecialchars($this->removeInvis(nl2br($question['Question']['insight_text'])), ENT_QUOTES);
			$question['Question']['prize_text'] = htmlspecialchars($this->removeInvis(nl2br($question['Question']['prize_text'])), ENT_QUOTES);
			$question['Question']['answer_1_text'] = htmlspecialchars($this->removeInvis(nl2br($question['Question']['answer_1_text'])), ENT_QUOTES);
			$question['Question']['answer_2_text'] = htmlspecialchars($this->removeInvis(nl2br($question['Question']['answer_2_text'])), ENT_QUOTES);
			$question['Question']['answer_3_text'] = htmlspecialchars($this->removeInvis(nl2br($question['Question']['answer_3_text'])), ENT_QUOTES);
			$question['Question']['answer_4_text'] = htmlspecialchars($this->removeInvis(nl2br($question['Question']['answer_4_text'])), ENT_QUOTES);
			$question['Question']['clue_note'] = htmlspecialchars($this->removeInvis(nl2br($question['Question']['clue_note'])), ENT_QUOTES);
			$question['Question']['question_note'] = htmlspecialchars($this->removeInvis(nl2br($question['Question']['question_note'])), ENT_QUOTES);
			$question['Question']['insight_note'] = htmlspecialchars($this->removeInvis(nl2br($question['Question']['insight_note'])), ENT_QUOTES);
			$question['Question']['prize_note'] = htmlspecialchars($this->removeInvis(nl2br($question['Question']['prize_note'])), ENT_QUOTES);
			$question['Question']['answer_1_note'] = htmlspecialchars($this->removeInvis(nl2br($question['Question']['answer_1_note'])), ENT_QUOTES);
			$question['Question']['answer_2_note'] = htmlspecialchars($this->removeInvis(nl2br($question['Question']['answer_2_note'])), ENT_QUOTES);
			$question['Question']['answer_3_note'] = htmlspecialchars($this->removeInvis(nl2br($question['Question']['answer_3_note'])), ENT_QUOTES);
			$question['Question']['answer_4_note'] = htmlspecialchars($this->removeInvis(nl2br($question['Question']['answer_4_note'])), ENT_QUOTES);
			if($this->Question->save($question))
			{
				if($this->data['Question']['clue_image']['error'] == 0 && $this->data['Question']['clue_image']['size'] > 0)
				{
					if(move_uploaded_file($this->data['Question']['clue_image']['tmp_name'], WWW_ROOT.'img'.DS.'clues'.DS.$this->Question->id.'-O.png'))
					{
						$this->generateClueInsightImage(WWW_ROOT.'img'.DS.'clues'.DS.$this->Question->id.'-O.png', WWW_ROOT.'img'.DS.'clues'.DS.$this->Question->id.'.png');
					}
				}
				if($this->data['Question']['question_image']['error'] == 0 && $this->data['Question']['question_image']['size'] > 0)
				{
					if(move_uploaded_file($this->data['Question']['question_image']['tmp_name'], WWW_ROOT.'img'.DS.'questions'.DS.$this->Question->id.'-O.png'))
					{
						$this->generateQuestionImage(WWW_ROOT.'img'.DS.'questions'.DS.$this->Question->id.'-O.png', WWW_ROOT.'img'.DS.'questions'.DS.$this->Question->id.'.png');
					}
				}
				if($this->data['Question']['insight_image']['error'] == 0 && $this->data['Question']['insight_image']['size'] > 0)
				{
					if(move_uploaded_file($this->data['Question']['insight_image']['tmp_name'], WWW_ROOT.'img'.DS.'insights'.DS.$this->Question->id.'-O.png'))
					{
						$this->generateClueInsightImage(WWW_ROOT.'img'.DS.'insights'.DS.$this->Question->id.'-O.png', WWW_ROOT.'img'.DS.'insights'.DS.$this->Question->id.'.png');
					}
				}
				if($this->data['Question']['answer_1_image']['error'] == 0 && $this->data['Question']['answer_1_image']['size'] > 0)
				{
					if(move_uploaded_file($this->data['Question']['answer_1_image']['tmp_name'], WWW_ROOT.'img'.DS.'answers'.DS.$this->Question->id.'-1-O.png'))
					{
						$this->generateAnswerImage(WWW_ROOT.'img'.DS.'answers'.DS.$this->Question->id.'-1-O.png', WWW_ROOT.'img'.DS.'answers'.DS.$this->Question->id.'-1.png');
					}
				}
				if($this->data['Question']['answer_2_image']['error'] == 0 && $this->data['Question']['answer_2_image']['size'] > 0)
				{
					if(move_uploaded_file($this->data['Question']['answer_2_image']['tmp_name'], WWW_ROOT.'img'.DS.'answers'.DS.$this->Question->id.'-2-O.png'))
					{
						$this->generateAnswerImage(WWW_ROOT.'img'.DS.'answers'.DS.$this->Question->id.'-2-O.png', WWW_ROOT.'img'.DS.'answers'.DS.$this->Question->id.'-2.png');
					}
				}
				if($this->data['Question']['answer_3_image']['error'] == 0 && $this->data['Question']['answer_3_image']['size'] > 0)
				{
					if(move_uploaded_file($this->data['Question']['answer_3_image']['tmp_name'], WWW_ROOT.'img'.DS.'answers'.DS.$this->Question->id.'-3-O.png'))
					{
						$this->generateAnswerImage(WWW_ROOT.'img'.DS.'answers'.DS.$this->Question->id.'-3-O.png', WWW_ROOT.'img'.DS.'answers'.DS.$this->Question->id.'-3.png');
					}
				}
				if($this->data['Question']['answer_4_image']['error'] == 0 && $this->data['Question']['answer_4_image']['size'] > 0)
				{
					if(move_uploaded_file($this->data['Question']['answer_4_image']['tmp_name'], WWW_ROOT.'img'.DS.'answers'.DS.$this->Question->id.'-4-O.png'))
					{
						$this->generateAnswerImage(WWW_ROOT.'img'.DS.'answers'.DS.$this->Question->id.'-4-O.png', WWW_ROOT.'img'.DS.'answers'.DS.$this->Question->id.'-4.png');
					}
				}
				if($this->data['Question']['prize_image']['error'] == 0 && $this->data['Question']['prize_image']['size'] > 0)
				{
					move_uploaded_file($this->data['Question']['prize_image']['tmp_name'], WWW_ROOT.'img'.DS.'prizes'.DS.$this->Question->id.'.png');
				}
				$this->version_up_question($this->Question->id);
				if($preview)
				{
					$this->redirect('/games/preview_question/'.$game_id.'/'.$this->Question->id);
				}else{
					$this->redirect('/questions/edit/'.$this->Question->id);
				}
			}
		} else {
			$question['Question']['clue_text'] = str_replace('<br />','
',htmlspecialchars_decode($question['Question']['clue_text'], ENT_QUOTES));
			$question['Question']['question_text'] = str_replace('<br />','
',htmlspecialchars_decode($question['Question']['question_text'], ENT_QUOTES));
			$question['Question']['insight_text'] = str_replace('<br />','
',htmlspecialchars_decode($question['Question']['insight_text'], ENT_QUOTES));
			$question['Question']['prize_text'] = str_replace('<br />','
',htmlspecialchars_decode($question['Question']['prize_text'], ENT_QUOTES));
			$question['Question']['answer_1_text'] = str_replace('<br />','
',htmlspecialchars_decode($question['Question']['answer_1_text'], ENT_QUOTES));
			$question['Question']['answer_2_text'] = str_replace('<br />','
',htmlspecialchars_decode($question['Question']['answer_2_text'], ENT_QUOTES));
			$question['Question']['answer_3_text'] = str_replace('<br />','
',htmlspecialchars_decode($question['Question']['answer_3_text'], ENT_QUOTES));
			$question['Question']['answer_4_text'] = str_replace('<br />','
',htmlspecialchars_decode($question['Question']['answer_4_text'], ENT_QUOTES));

			$question['Question']['clue_note'] = str_replace('<br />','
',htmlspecialchars_decode($question['Question']['clue_note'], ENT_QUOTES));
			$question['Question']['question_note'] = str_replace('<br />','
',htmlspecialchars_decode($question['Question']['question_note'], ENT_QUOTES));
			$question['Question']['insight_note'] = str_replace('<br />','
',htmlspecialchars_decode($question['Question']['insight_note'], ENT_QUOTES));
			$question['Question']['prize_note'] = str_replace('<br />','
',htmlspecialchars_decode($question['Question']['prize_note'], ENT_QUOTES));
			$question['Question']['answer_1_note'] = str_replace('<br />','
',htmlspecialchars_decode($question['Question']['answer_1_note'], ENT_QUOTES));
			$question['Question']['answer_2_note'] = str_replace('<br />','
',htmlspecialchars_decode($question['Question']['answer_2_note'], ENT_QUOTES));
			$question['Question']['answer_3_note'] = str_replace('<br />','
',htmlspecialchars_decode($question['Question']['answer_3_note'], ENT_QUOTES));
			$question['Question']['answer_4_note'] = str_replace('<br />','
',htmlspecialchars_decode($question['Question']['answer_4_note'], ENT_QUOTES));
			$this->data = $question;
		}
	}
	
	/*function initialVersions()
	{
		$questions = $this->Question->find('all');
		foreach($questions as $question):
			$this->version_up_question($question['Question']['id']);
		endforeach;
		exit;
	}*/
	
	function version_history($question_id)
	{
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
		$this->Question->bindModel(
			array(
				'hasMany'=>array(
	        		'QuestionVersion' => array(
	            		'className' => 'QuestionVersion',
	            		'foreignKey' => 'question_id',
	            		'order' => 'QuestionVersion.created DESC'
	        		)
	        	)
	        )
        );
		$this->set('question',$this->Question->find('first',array('conditions'=>'Question.id = '.$question_id,'recursive'=>2)));
	}
	
	function version_up_question($question_id)
	{
		$question = $this->Question->findById($question_id);
		$question['Question']['version']++;
		$this->Question->save($question);
		
		$version['QuestionVersion'] = $question['Question'];
		$version['QuestionVersion']['id'] = null;
		$version['QuestionVersion']['question_id'] = $question_id;
		$version['QuestionVersion']['user_id'] = $this->Auth->user('id');
		unset($version['QuestionVersion']['created']);
		unset($version['QuestionVersion']['modified']);
		$this->QuestionVersion->save($version);
		$version_id = $this->QuestionVersion->id;
		
		if(is_file(WWW_ROOT.'img'.DS.'clues'.DS.$question_id.'.png')) copy(WWW_ROOT.'img'.DS.'clues'.DS.$question_id.'.png',WWW_ROOT.'img'.DS.'clues'.DS.$question_id.'-'.$version_id.'.png');
		if(is_file(WWW_ROOT.'img'.DS.'clues'.DS.$question_id.'-O.png')) copy(WWW_ROOT.'img'.DS.'clues'.DS.$question_id.'-O.png',WWW_ROOT.'img'.DS.'clues'.DS.$question_id.'-O-'.$version_id.'.png');
		
		if(is_file(WWW_ROOT.'img'.DS.'questions'.DS.$question_id.'.png')) copy(WWW_ROOT.'img'.DS.'questions'.DS.$question_id.'.png',WWW_ROOT.'img'.DS.'questions'.DS.$question_id.'-'.$version_id.'.png');
		if(is_file(WWW_ROOT.'img'.DS.'questions'.DS.$question_id.'-O.png')) copy(WWW_ROOT.'img'.DS.'questions'.DS.$question_id.'-O.png',WWW_ROOT.'img'.DS.'questions'.DS.$question_id.'-O-'.$version_id.'.png');
		
		if(is_file(WWW_ROOT.'img'.DS.'insights'.DS.$question_id.'.png')) copy(WWW_ROOT.'img'.DS.'insights'.DS.$question_id.'.png',WWW_ROOT.'img'.DS.'insights'.DS.$question_id.'-'.$version_id.'.png');
		if(is_file(WWW_ROOT.'img'.DS.'insights'.DS.$question_id.'-O.png')) copy(WWW_ROOT.'img'.DS.'insights'.DS.$question_id.'-O.png',WWW_ROOT.'img'.DS.'insights'.DS.$question_id.'-O-'.$version_id.'.png');
		
		if(is_file(WWW_ROOT.'img'.DS.'answers'.DS.$question_id.'-1.png')) copy(WWW_ROOT.'img'.DS.'answers'.DS.$question_id.'-1.png',WWW_ROOT.'img'.DS.'answers'.DS.$question_id.'-1-'.$version_id.'.png');
		if(is_file(WWW_ROOT.'img'.DS.'answers'.DS.$question_id.'-1-O.png')) copy(WWW_ROOT.'img'.DS.'answers'.DS.$question_id.'-1-O.png',WWW_ROOT.'img'.DS.'answers'.DS.$question_id.'-1-O-'.$version_id.'.png');
		
		if(is_file(WWW_ROOT.'img'.DS.'answers'.DS.$question_id.'-2.png')) copy(WWW_ROOT.'img'.DS.'answers'.DS.$question_id.'-2.png',WWW_ROOT.'img'.DS.'answers'.DS.$question_id.'-2-'.$version_id.'.png');
		if(is_file(WWW_ROOT.'img'.DS.'answers'.DS.$question_id.'-2-O.png')) copy(WWW_ROOT.'img'.DS.'answers'.DS.$question_id.'-2-O.png',WWW_ROOT.'img'.DS.'answers'.DS.$question_id.'-2-O-'.$version_id.'.png');
		
		if(is_file(WWW_ROOT.'img'.DS.'answers'.DS.$question_id.'-3.png')) copy(WWW_ROOT.'img'.DS.'answers'.DS.$question_id.'-3.png',WWW_ROOT.'img'.DS.'answers'.DS.$question_id.'-3-'.$version_id.'.png');
		if(is_file(WWW_ROOT.'img'.DS.'answers'.DS.$question_id.'-3-O.png')) copy(WWW_ROOT.'img'.DS.'answers'.DS.$question_id.'-3-O.png',WWW_ROOT.'img'.DS.'answers'.DS.$question_id.'-3-O-'.$version_id.'.png');
		
		if(is_file(WWW_ROOT.'img'.DS.'answers'.DS.$question_id.'-4.png')) copy(WWW_ROOT.'img'.DS.'answers'.DS.$question_id.'-4.png',WWW_ROOT.'img'.DS.'answers'.DS.$question_id.'-4-'.$version_id.'.png');
		if(is_file(WWW_ROOT.'img'.DS.'answers'.DS.$question_id.'-4-O.png')) copy(WWW_ROOT.'img'.DS.'answers'.DS.$question_id.'-4-O.png',WWW_ROOT.'img'.DS.'answers'.DS.$question_id.'-4-O-'.$version_id.'.png');
		
		if(is_file(WWW_ROOT.'img'.DS.'prizes'.DS.$question_id.'.png')) copy(WWW_ROOT.'img'.DS.'prizes'.DS.$question_id.'.png',WWW_ROOT.'img'.DS.'prizes'.DS.$question_id.'-'.$version_id.'.png');
		if(is_file(WWW_ROOT.'img'.DS.'prizes'.DS.$question_id.'-O.png')) copy(WWW_ROOT.'img'.DS.'prizes'.DS.$question_id.'-O.png',WWW_ROOT.'img'.DS.'prizes'.DS.$question_id.'-O-'.$version_id.'.png');
		
		return true;
	}
	
	function set_to_this_version($question_id,$to_id)
	{
		$question = $this->Question->findById($question_id);
		$version = $this->QuestionVersion->findById($to_id);
		$version['QuestionVersion']['id'] = null;
		$version['QuestionVersion']['user_id'] = $this->Auth->user('id');
		$version['QuestionVersion']['version'] = $question['Question']['version'] + 1;
		unset($version['QuestionVersion']['created']);
		unset($version['QuestionVersion']['modified']);
		$this->QuestionVersion->save($version);
		$version_id = $this->QuestionVersion->id;
		
		if(is_file(WWW_ROOT.'img'.DS.'clues'.DS.$question_id.'-'.$to_id.'.png')) copy(WWW_ROOT.'img'.DS.'clues'.DS.$question_id.'-'.$to_id.'.png',WWW_ROOT.'img'.DS.'clues'.DS.$question_id.'-'.$version_id.'.png');
		if(is_file(WWW_ROOT.'img'.DS.'clues'.DS.$question_id.'-O-'.$to_id.'.png')) copy(WWW_ROOT.'img'.DS.'clues'.DS.$question_id.'-O-'.$to_id.'.png',WWW_ROOT.'img'.DS.'clues'.DS.$question_id.'-O-'.$version_id.'.png');
		
		if(is_file(WWW_ROOT.'img'.DS.'questions'.DS.$question_id.'-'.$to_id.'.png')) copy(WWW_ROOT.'img'.DS.'questions'.DS.$question_id.'-'.$to_id.'.png',WWW_ROOT.'img'.DS.'questions'.DS.$question_id.'-'.$version_id.'.png');
		if(is_file(WWW_ROOT.'img'.DS.'questions'.DS.$question_id.'-O-'.$to_id.'.png')) copy(WWW_ROOT.'img'.DS.'questions'.DS.$question_id.'-O-'.$to_id.'.png',WWW_ROOT.'img'.DS.'questions'.DS.$question_id.'-O-'.$version_id.'.png');
		
		if(is_file(WWW_ROOT.'img'.DS.'insights'.DS.$question_id.'-'.$to_id.'.png')) copy(WWW_ROOT.'img'.DS.'insights'.DS.$question_id.'-'.$to_id.'.png',WWW_ROOT.'img'.DS.'insights'.DS.$question_id.'-'.$version_id.'.png');
		if(is_file(WWW_ROOT.'img'.DS.'insights'.DS.$question_id.'-O-'.$to_id.'.png')) copy(WWW_ROOT.'img'.DS.'insights'.DS.$question_id.'-O-'.$to_id.'.png',WWW_ROOT.'img'.DS.'insights'.DS.$question_id.'-O-'.$version_id.'.png');
		
		if(is_file(WWW_ROOT.'img'.DS.'answers'.DS.$question_id.'-1-'.$to_id.'.png')) copy(WWW_ROOT.'img'.DS.'answers'.DS.$question_id.'-1-'.$to_id.'.png',WWW_ROOT.'img'.DS.'answers'.DS.$question_id.'-1-'.$version_id.'.png');
		if(is_file(WWW_ROOT.'img'.DS.'answers'.DS.$question_id.'-1-O-'.$to_id.'.png')) copy(WWW_ROOT.'img'.DS.'answers'.DS.$question_id.'-1-O-'.$to_id.'.png',WWW_ROOT.'img'.DS.'answers'.DS.$question_id.'-1-O-'.$version_id.'.png');
		
		if(is_file(WWW_ROOT.'img'.DS.'answers'.DS.$question_id.'-2-'.$to_id.'.png')) copy(WWW_ROOT.'img'.DS.'answers'.DS.$question_id.'-2-'.$to_id.'.png',WWW_ROOT.'img'.DS.'answers'.DS.$question_id.'-2-'.$version_id.'.png');
		if(is_file(WWW_ROOT.'img'.DS.'answers'.DS.$question_id.'-2-O-'.$to_id.'.png')) copy(WWW_ROOT.'img'.DS.'answers'.DS.$question_id.'-2-O-'.$to_id.'.png',WWW_ROOT.'img'.DS.'answers'.DS.$question_id.'-2-O-'.$version_id.'.png');
		
		if(is_file(WWW_ROOT.'img'.DS.'answers'.DS.$question_id.'-3-'.$to_id.'.png')) copy(WWW_ROOT.'img'.DS.'answers'.DS.$question_id.'-3-'.$to_id.'.png',WWW_ROOT.'img'.DS.'answers'.DS.$question_id.'-3-'.$version_id.'.png');
		if(is_file(WWW_ROOT.'img'.DS.'answers'.DS.$question_id.'-3-O-'.$to_id.'.png')) copy(WWW_ROOT.'img'.DS.'answers'.DS.$question_id.'-3-O-'.$to_id.'.png',WWW_ROOT.'img'.DS.'answers'.DS.$question_id.'-3-O-'.$version_id.'.png');
		
		if(is_file(WWW_ROOT.'img'.DS.'answers'.DS.$question_id.'-4-'.$to_id.'.png')) copy(WWW_ROOT.'img'.DS.'answers'.DS.$question_id.'-4-'.$to_id.'.png',WWW_ROOT.'img'.DS.'answers'.DS.$question_id.'-4-'.$version_id.'.png');
		if(is_file(WWW_ROOT.'img'.DS.'answers'.DS.$question_id.'-4-O-'.$to_id.'.png')) copy(WWW_ROOT.'img'.DS.'answers'.DS.$question_id.'-4-O-'.$to_id.'.png',WWW_ROOT.'img'.DS.'answers'.DS.$question_id.'-4-O-'.$version_id.'.png');
		
		if(is_file(WWW_ROOT.'img'.DS.'prizes'.DS.$question_id.'-'.$to_id.'.png')) copy(WWW_ROOT.'img'.DS.'prizes'.DS.$question_id.'-'.$to_id.'.png',WWW_ROOT.'img'.DS.'prizes'.DS.$question_id.'-'.$version_id.'.png');
		if(is_file(WWW_ROOT.'img'.DS.'prizes'.DS.$question_id.'-O-'.$to_id.'.png')) copy(WWW_ROOT.'img'.DS.'prizes'.DS.$question_id.'-O-'.$to_id.'.png',WWW_ROOT.'img'.DS.'prizes'.DS.$question_id.'-O-'.$version_id.'.png');
		
		$save['Question'] = $version['QuestionVersion'];
		$save['Question']['id'] = $question_id;
		$save['Question']['created'] = $question['Question']['created'];
		$this->Question->save($save);
		
		
		if(is_file(WWW_ROOT.'img'.DS.'clues'.DS.$question_id.'-'.$version_id.'.png')) copy(WWW_ROOT.'img'.DS.'clues'.DS.$question_id.'-'.$version_id.'.png',WWW_ROOT.'img'.DS.'clues'.DS.$question_id.'.png');
		if(is_file(WWW_ROOT.'img'.DS.'clues'.DS.$question_id.'-O-'.$version_id.'.png')) copy(WWW_ROOT.'img'.DS.'clues'.DS.$question_id.'-O-'.$version_id.'.png',WWW_ROOT.'img'.DS.'clues'.DS.$question_id.'-O.png');
		
		if(is_file(WWW_ROOT.'img'.DS.'questions'.DS.$question_id.'-'.$version_id.'.png')) copy(WWW_ROOT.'img'.DS.'questions'.DS.$question_id.'-'.$version_id.'.png',WWW_ROOT.'img'.DS.'questions'.DS.$question_id.'.png');
		if(is_file(WWW_ROOT.'img'.DS.'questions'.DS.$question_id.'-O-'.$version_id.'.png')) copy(WWW_ROOT.'img'.DS.'questions'.DS.$question_id.'-O-'.$version_id.'.png',WWW_ROOT.'img'.DS.'questions'.DS.$question_id.'-O.png');
		
		if(is_file(WWW_ROOT.'img'.DS.'insights'.DS.$question_id.'-'.$version_id.'.png')) copy(WWW_ROOT.'img'.DS.'insights'.DS.$question_id.'-'.$version_id.'.png',WWW_ROOT.'img'.DS.'insights'.DS.$question_id.'.png');
		if(is_file(WWW_ROOT.'img'.DS.'insights'.DS.$question_id.'-O-'.$version_id.'.png')) copy(WWW_ROOT.'img'.DS.'insights'.DS.$question_id.'-O-'.$version_id.'.png',WWW_ROOT.'img'.DS.'insights'.DS.$question_id.'-O.png');
		
		if(is_file(WWW_ROOT.'img'.DS.'answers'.DS.$question_id.'-1-'.$version_id.'.png')) copy(WWW_ROOT.'img'.DS.'answers'.DS.$question_id.'-1-'.$version_id.'.png',WWW_ROOT.'img'.DS.'answers'.DS.$question_id.'-1.png');
		if(is_file(WWW_ROOT.'img'.DS.'answers'.DS.$question_id.'-1-O-'.$version_id.'.png')) copy(WWW_ROOT.'img'.DS.'answers'.DS.$question_id.'-1-O-'.$version_id.'.png',WWW_ROOT.'img'.DS.'answers'.DS.$question_id.'-1-O.png');
		
		if(is_file(WWW_ROOT.'img'.DS.'answers'.DS.$question_id.'-2-'.$version_id.'.png')) copy(WWW_ROOT.'img'.DS.'answers'.DS.$question_id.'-2-'.$version_id.'.png',WWW_ROOT.'img'.DS.'answers'.DS.$question_id.'-2.png');
		if(is_file(WWW_ROOT.'img'.DS.'answers'.DS.$question_id.'-2-O-'.$version_id.'.png')) copy(WWW_ROOT.'img'.DS.'answers'.DS.$question_id.'-2-O-'.$version_id.'.png',WWW_ROOT.'img'.DS.'answers'.DS.$question_id.'-2-O.png');
		
		if(is_file(WWW_ROOT.'img'.DS.'answers'.DS.$question_id.'-3-'.$version_id.'.png')) copy(WWW_ROOT.'img'.DS.'answers'.DS.$question_id.'-3-'.$version_id.'.png',WWW_ROOT.'img'.DS.'answers'.DS.$question_id.'-3.png');
		if(is_file(WWW_ROOT.'img'.DS.'answers'.DS.$question_id.'-3-O-'.$version_id.'.png')) copy(WWW_ROOT.'img'.DS.'answers'.DS.$question_id.'-3-O-'.$version_id.'.png',WWW_ROOT.'img'.DS.'answers'.DS.$question_id.'-3-O.png');
		
		if(is_file(WWW_ROOT.'img'.DS.'answers'.DS.$question_id.'-4-'.$version_id.'.png')) copy(WWW_ROOT.'img'.DS.'answers'.DS.$question_id.'-4-'.$version_id.'.png',WWW_ROOT.'img'.DS.'answers'.DS.$question_id.'-4.png');
		if(is_file(WWW_ROOT.'img'.DS.'answers'.DS.$question_id.'-4-O-'.$version_id.'.png')) copy(WWW_ROOT.'img'.DS.'answers'.DS.$question_id.'-4-O-'.$version_id.'.png',WWW_ROOT.'img'.DS.'answers'.DS.$question_id.'-4-O.png');
		
		if(is_file(WWW_ROOT.'img'.DS.'prizes'.DS.$question_id.'-'.$version_id.'.png')) copy(WWW_ROOT.'img'.DS.'prizes'.DS.$question_id.'-'.$version_id.'.png',WWW_ROOT.'img'.DS.'prizes'.DS.$question_id.'.png');
		if(is_file(WWW_ROOT.'img'.DS.'prizes'.DS.$question_id.'-O-'.$version_id.'.png')) copy(WWW_ROOT.'img'.DS.'prizes'.DS.$question_id.'-O-'.$version_id.'.png',WWW_ROOT.'img'.DS.'prizes'.DS.$question_id.'-O.png');
		
		$this->redirect('/questions/version_history/'.$question_id);
		
	}
	
	function delete($question_id)
	{
		$question = $this->Question->findById($question_id);
		$question['Question']['deleted'] = 1;
		$this->Question->save($question);
		$this->version_up_question($question_id);
		$questions = $this->Question->find('all',array('conditions'=>'Question.game_id = '.$question['Question']['game_id'].' AND Question.deleted = 0','order'=>'Question.order ASC'));
		foreach($questions as $i=>$question):
			$question['Question']['order'] = $i;
			$this->Question->save($question);
		endforeach;
		$this->redirect('/questions/index/'.$question['Question']['game_id']);
	}
	
	function undelete($question_id)
	{
		$question = $this->Question->findById($question_id);
		$question['Question']['deleted'] = 0;
		$question['Question']['order'] = $this->Question->find('count',array('conditions'=>'Question.game_id = '.$question['Question']['game_id'].' AND Question.deleted = 0'));
		$this->Question->save($question);
		$this->version_up_question($question_id);
		$this->redirect('/questions/deleted/'.$question['Question']['game_id']);
	}
	
	function set_order()
    {
    	$i=0;
		foreach($this->data['question'] as $id):
			$order['Question']['id'] = $id;
			$order['Question']['order'] = $i;
			$i++;
			$this->Question->save($order,false);
		endforeach;
		exit();
    }
	
	function test()
	{
		$data = list($width, $height) = getimagesize(WWW_ROOT.'img'.DS.'test.jpg');
		pr($data);
		pr($height);
		die;
	}
	
	function export()
	{
		App::import('Vendor', 'RestRequest', array('file' => 'RestRequest.inc.php'));
		
		/*$request = new RestRequest('http://admin:MyAdminPass87@50.56.194.198:8282/RingorangWebService/rservice/game/getList?appId=22&offset=0&count=10', 'GET');
		$request->execute();
		$response = $request->getResponseBody();
		echo $response;*/
		
		$data = array(
			
		);
		$request = new RestRequest('http://admin:MyAdminPass87@50.56.194.198:8282/RingorangWebService/rservice/game/getQuestionDetails/1301', 'GET');
		$request->execute();
		$response = $request->getResponseBody();
		echo $response;
		die;
	}
}
?>