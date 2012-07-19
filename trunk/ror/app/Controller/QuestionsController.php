<?php

App::uses('AppController', 'Controller');
class QuestionsController extends AppController {
	public $name = 'Questions';
	public $helpers = array('Html', 'Session');
	public $uses = array('Game','Question','QuestionVersion');
	
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
		
		$request = new RestRequest('http://admin:MyAdminPass87@50.56.194.198:8282/RingorangWebService/rservice/game/getList?appId=22&offset=0&count=10', 'GET');
		//$request->setUsername('admin');
		//$request->setPassword('MyAdminPass87');
		$request->execute();
		
		pr($request);
		die;
	}
}
?>