<?php

App::uses('AppController', 'Controller');
class GamesController extends AppController {
	public $name = 'Games';
	public $helpers = array('Html', 'Session');
	public $uses = array('Game','Question','QuestionVersion');
	
	public function beforeFilter()
	{
		$this->Auth->allow('play');
	}
	
	public function index() {
		$games = $this->Game->find('all');
		$this->set('games',$games);
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
	
	public function play($game_id) {
		$this->layout = false;
		$this->set('preview_timers',0);
		$this->set('game',$this->Game->findById($game_id));
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
			$this->set('question',$this->QuestionVersion->find('first',array('conditions'=>'QuestionVersion.question_id '.$question_id,'order'=>'QuestionVersion.created DESC')));
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
			$this->set('question',$this->QuestionVersion->find('first',array('conditions'=>'QuestionVersion.question_id '.$question_id,'order'=>'QuestionVersion.created DESC')));
		}
		$this->render('play');
	}
	
	public function export($game_id)
	{
		$this->layout = false;
		$this->Game->bindModel(array(
			'hasMany'=>array(
				'Question'=>array(
					'className'=>'Question',
					'foreignKey'=>'game_id',
					'order'=>'Question.order ASC'
				)
			)
		));
		$this->set('game',$this->Game->findById($game_id));
	}
	
	public function json_data($game_id,$version_id=0) {
		$this->layout = false;
		if($version_id > 0)
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
	
}
?>