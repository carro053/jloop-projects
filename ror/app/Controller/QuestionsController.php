<?php

App::uses('AppController', 'Controller');
class QuestionsController extends AppController {
	public $name = 'Questions';
	public $helpers = array('Html', 'Session');
	public $uses = array('Game','Question');
	
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
					'order'=>$order
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
			$question['Question']['clue_text'] = $this->removeInvis(nl2br($question['Question']['clue_text']));
			$question['Question']['question_text'] = $this->removeInvis(nl2br($question['Question']['question_text']));
			$question['Question']['insight_text'] = $this->removeInvis(nl2br($question['Question']['insight_text']));
			$question['Question']['prize_text'] = $this->removeInvis(nl2br($question['Question']['prize_text']));
			$question['Question']['game_id'] = $game_id;
			$question['Question']['order'] = $this->Question->find('count',array('conditions'=>'Question.game_id = '.$game_id));
			if($this->Question->save($question))
			{
				if($this->data['Question']['clue_image']['error'] == 0 && $this->data['Question']['clue_image']['size'] > 0)
				{
					move_uploaded_file($this->data['Question']['clue_image']['tmp_name'], WWW_ROOT.'img'.DS.'clues'.DS.$this->Question->id.'.png');
				}
				if($this->data['Question']['question_image']['error'] == 0 && $this->data['Question']['question_image']['size'] > 0)
				{
					move_uploaded_file($this->data['Question']['question_image']['tmp_name'], WWW_ROOT.'img'.DS.'questions'.DS.$this->Question->id.'.png');
				}
				if($this->data['Question']['insight_image']['error'] == 0 && $this->data['Question']['insight_image']['size'] > 0)
				{
					move_uploaded_file($this->data['Question']['insight_image']['tmp_name'], WWW_ROOT.'img'.DS.'insights'.DS.$this->Question->id.'.png');
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
				if($preview)
				{
					$question = $this->Question->findById($this->Question->id);
					$this->redirect('/games/play/'.$game_id.'/'.$question['Question']['order']);
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
			$question['Question']['clue_text'] = $this->removeInvis(nl2br($question['Question']['clue_text']));
			$question['Question']['question_text'] = $this->removeInvis(nl2br($question['Question']['question_text']));
			$question['Question']['insight_text'] = $this->removeInvis(nl2br($question['Question']['insight_text']));
			$question['Question']['prize_text'] = $this->removeInvis(nl2br($question['Question']['prize_text']));
			if($this->Question->save($question))
			{
				if($this->data['Question']['clue_image']['error'] == 0 && $this->data['Question']['clue_image']['size'] > 0)
				{
					move_uploaded_file($this->data['Question']['clue_image']['tmp_name'], WWW_ROOT.'img'.DS.'clues'.DS.$this->Question->id.'.png');
				}
				if($this->data['Question']['question_image']['error'] == 0 && $this->data['Question']['question_image']['size'] > 0)
				{
					move_uploaded_file($this->data['Question']['question_image']['tmp_name'], WWW_ROOT.'img'.DS.'questions'.DS.$this->Question->id.'.png');
				}
				if($this->data['Question']['insight_image']['error'] == 0 && $this->data['Question']['insight_image']['size'] > 0)
				{
					move_uploaded_file($this->data['Question']['insight_image']['tmp_name'], WWW_ROOT.'img'.DS.'insights'.DS.$this->Question->id.'.png');
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
				if($preview)
				{
					$question = $this->Question->findById($this->Question->id);
					$this->redirect('/games/play/'.$game_id.'/'.$question['Question']['order']);
				}else{
					$this->redirect('/questions/index/'.$game_id);
				}
			}
		} else {
			$question['Question']['clue_text'] = str_replace('<br />','
',$question['Question']['clue_text']);
			$question['Question']['question_text'] = str_replace('<br />','
',$question['Question']['question_text']);
			$question['Question']['insight_text'] = str_replace('<br />','
',$question['Question']['insight_text']);
			$question['Question']['prize_text'] = str_replace('<br />','
',$question['Question']['prize_text']);
			$this->data = $question;
		}
	}
	
	function delete($question_id)
	{
		$question = $this->Question->findById($question_id);
		if($this->Question->delete($question_id))
			$this->redirect('/questions/index/'.$question['Question']['game_id']);
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
	
}
?>