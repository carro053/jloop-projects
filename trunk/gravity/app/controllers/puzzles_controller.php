<?php

class PuzzlesController extends AppController {


	var $uses = array('Puzzle','Account','PuzzlePlanet','PuzzleAstronaut');
	
	function beforeFilter()
 	{
 		parent::beforeFilter();
 	}
 	
 	function savePuzzle()
 	{
 		$json_data = json_decode($_POST['json_data']);
 		
 		CakeLog::write('savePuzzle', print_r($json_data));
 		exit;
 	}
	
}

?>