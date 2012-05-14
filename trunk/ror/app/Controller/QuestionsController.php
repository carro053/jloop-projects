<?php

App::uses('AppController', 'Controller');
class GamesController extends AppController {
	public $name = 'Games';
	public $helpers = array('Html', 'Session');
	public $uses = array('Game','Question');
	
	public function index($game_id)
	{
		$this->Game->bindModel(array(
			'hasMany'=>array(
				'Question'=>array(
					'className'=>'Question',
					'foreignKey'=>'game_id',
					'order'=>'Question.order ASC'
				)
			)
		));
		$game = $this->Game->findById($game_id);
		$this->set('game', $game);
	}
	
	function add($game_id)
	{
		if(isset($this->data['Question']))
		{
			$question = $this->data;
			$question['Question']['game_id'] = $game_id;
			$question['Question']['order'] = $this->Question->find('count',array('conditions'=>'Question.game_id = '.$game_id));
			if($this->Question->save($question))
				$this->redirect('/questions/index/'.$game_id);
		}
		$this->set('game_id', $game_id);
	}
	
	function edit($question_id)
	{
		$question = $this->Question->findById($question_id);
		if(isset($this->data))
		{
			if($this->Question->save($this->data))
				$this->redirect('/questions/index/'.$question['Question']['game_id']);
		} else {
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
		foreach($this->params['form']['question'] as $id):
			$order['Question']['id'] = $id;
			$order['Question']['order'] = $i;
			$i++;
			$this->Question->save($order,false);
		endforeach;
		exit();
    }
	
}
?>