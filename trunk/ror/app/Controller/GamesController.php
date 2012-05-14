<?php

App::uses('AppController', 'Controller');
class GamesController extends AppController {
	public $name = 'Games';
	public $helpers = array('Html', 'Session');
	public $uses = array('Game','Question');
	
	public function index() {
		$games = $this->Game->find('all');
		$this->set('games',$games);
	}
	
	function add()
	{
		if(isset($this->data))
		{
			if($this->data['Game']['icon']['error'] == 0 && $this->data['Game']['icon']['size'] > 0) $this->data['Game']['has_icon'] = 1;
			if($this->Game->save($this->data))
			{
				if($this->data['Game']['icon']['error'] == 0 && $this->data['Game']['icon']['size'] > 0)
				{
					move_uploaded_file($this->data['Game']['icon']['tmp_name'], WWW_ROOT.'img'.DS.'game_icons'.DS.$this->Game->id.'.png');
				}
				$this->redirect('/games');
			}
		}
	}
	
	function edit($game_id)
	{
		$game = $this->Game->findById($game_id);
		if(isset($this->data))
		{
			if($this->data['Game']['icon']['error'] == 0 && $this->data['Game']['icon']['size'] > 0) $this->data['Game']['has_icon'] = 1;
			if($this->Game->save($this->data))
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
	
	public function play($game_id,$question_id=false) {
		$this->layout = false;
		
		$this->set('game',$this->Game->findById($game_id));
		$this->set('question_id',$question_id);
	}
	
	public function json_data() {
		$this->layout = false;
	}
	
}
?>