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
 		$json_data = json_decode($_POST['json_data']);
 		CakeLog::write('savePuzzle',print_r($json_data,true).' '.$json_data->total_fuel.' '.$json_data->puzzle_id);
 		$puzzle['Puzzle']['account_id'] = 1;
 		$puzzle['Puzzle']['total_fuel'] = $json_data->total_fuel;
 		$puzzle['Puzzle']['start_x'] = 1;
 		$puzzle['Puzzle']['start_y'] = 1;
 		$puzzle['Puzzle']['end_x'] = 1;
 		$puzzle['Puzzle']['end_y'] = 1;
 		if($json_data->puzzle_id > 0)
 		{
 			$puzzle['Puzzle']['id'] = $json_data['puzzle_id'];
 			$this->Puzzle->save($puzzle);
 			$puzzle_id = $puzzle['Puzzle']['id'];
 			$this->PuzzlePlanet->query('DELETE FROM `puzzle_planets` WHERE `puzzle_id` = '.$puzzle_id);
 			$this->PuzzleAstronaut->query('DELETE FROM `puzzle_astronauts` WHERE `puzzle_id` = '.$puzzle_id);
 		}else{
 			$this->Puzzle->save($puzzle);
 			$puzzle_id = $this->Puzzle->getLastInsertId();
 		}
 		foreach($json_data['planets'] as $planet)
 		{
 			$newplanet['PuzzlePlanet']['puzzle_id'] = $puzzle_id;
 			$newplanet['PuzzlePlanet']['x'] = $planet->x;
 			$newplanet['PuzzlePlanet']['y'] = $planet->y;
 			$newplanet['PuzzlePlanet']['radius'] = $planet->radius;
 			$newplanet['PuzzlePlanet']['density'] = $planet->density;
 			$newplanet['PuzzlePlanet']['hasMoon'] = $planet->hasMoon;
 			$newplanet['PuzzlePlanet']['moonAngle'] = $planet->moonAngle;
 			$this->PuzzlePlanet->save($newplanet);
 		}
 		foreach($json_data['astronauts'] as $astronaut)
 		{
 			$newastro['PuzzlePlanet']['puzzle_id'] = $puzzle_id;
 			$newastro['PuzzlePlanet']['x'] = $astronaut->x;
 			$newastro['PuzzlePlanet']['y'] = $astronaut->y;
 			$this->PuzzleAstronaut->save($newastro);
 		}
 		
 		CakeLog::write('savePuzzle', ' MIKE '.$puzzle_id);
 		echo $puzzle_id;
 		exit;
 	}
	
}

?>