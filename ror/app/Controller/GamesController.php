<?php

App::uses('AppController', 'Controller');
class GamesController extends AppController {
	public $name = 'Games';
	public $helpers = array('Html', 'Session');
	public $uses = array('Game','Question');
	
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
	
	public function play($game_id,$question_index=-1) {
		$this->layout = false;
		
		$this->set('game',$this->Game->findById($game_id));
		if($question_index >= 0) $this->set('question_index',$question_index);
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
	
	public function json_data($game_id) {
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
	
}
?>