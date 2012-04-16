<?php

class PuzzlesController extends AppController {


	var $uses = array('Puzzle','Account','PuzzlePlanet','PuzzleAstronaut');
	var $components = array('Auth');
	
	function beforeFilter()
 	{
 		$this->Auth->allow('savePuzzle');
 		parent::beforeFilter();
 	}
 	
 	function savePuzzle()
 	{
 		CakeLog::write('savePuzzle','test');
 		/*
 		$json_data = json_decode($_POST['json_data']);
 		
 		CakeLog::write('savePuzzle', print_r($json_data).' MIKE '.$_POST['json_data']);*/
 		exit;
 	}
	
}

?>