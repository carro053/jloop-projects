<?php

App::uses('AppController', 'Controller');
class GamesController extends AppController {
	public $name = 'Games';
	public $helpers = array('Html', 'Session');
	public $uses = array();
	
	public function index() {
		$games = $this->Game->find('all');
		$this->set('games',$games);
	}
	
}
?>