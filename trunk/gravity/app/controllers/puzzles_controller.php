<?php

class PuzzlesController extends AppController {


	var $uses = array('Puzzle','Account','PuzzlePlanet','PuzzleAstronaut','PuzzleSolution','PuzzleSolutionWayPoint');
	var $components = array('Auth');
	
	function beforeFilter()
 	{
 		$this->Auth->allow('savePuzzle','getPuzzles','getPuzzle','saveSolution','saveImage');
 		parent::beforeFilter();
 	}
 	function saveImage($puzzle_id,$hd=0)
 	{
 		echo 'YES';
 		if($hd)
 		{
 			$hd_part = '-hd';
 		}else{
 			$hd_part = '';
 		}
        move_uploaded_file($_FILES['uploaded']['tmp_name'], $this->webroot.'files/puzzles/puzzle_'.$puzzle_id.$hd_part.'.jpg');
 		exit;
 	}
 	function savePuzzle()
 	{
 		$json_data = json_decode($_POST['json_data']);
 		CakeLog::write('savePuzzle',print_r($json_data,true));
 		$puzzle['Puzzle']['account_id'] = 1;
 		$puzzle['Puzzle']['total_fuel'] = $json_data->total_fuel;
 		$puzzle['Puzzle']['start_x'] = $json_data->startPoint[0];
 		$puzzle['Puzzle']['start_y'] = $json_data->startPoint[1];
 		$puzzle['Puzzle']['end_x'] = $json_data->endPoint[0];
 		$puzzle['Puzzle']['end_y'] = $json_data->endPoint[1];
 		if(isset($json_data->server_id) && $json_data->server_id > 0)
 		{
 			$puzzle['Puzzle']['id'] = $json_data->server_id;
 			$this->Puzzle->save($puzzle);
 			$puzzle_id = $puzzle['Puzzle']['id'];
 			$this->PuzzlePlanet->query('DELETE FROM `puzzle_planets` WHERE `puzzle_id` = '.$puzzle_id);
 			$this->PuzzleAstronaut->query('DELETE FROM `puzzle_astronauts` WHERE `puzzle_id` = '.$puzzle_id);
 		}else{
 			$this->Puzzle->save($puzzle);
 			$puzzle_id = $this->Puzzle->id;
 		}
 		foreach($json_data->planets as $planet)
 		{
 			$newplanet['PuzzlePlanet']['id'] = null;
 			$newplanet['PuzzlePlanet']['puzzle_id'] = $puzzle_id;
 			$newplanet['PuzzlePlanet']['x'] = $planet->x;
 			$newplanet['PuzzlePlanet']['y'] = $planet->y;
 			$newplanet['PuzzlePlanet']['radius'] = $planet->radius;
 			$newplanet['PuzzlePlanet']['density'] = $planet->density;
 			$newplanet['PuzzlePlanet']['hasMoon'] = $planet->hasMoon;
 			$newplanet['PuzzlePlanet']['moonAngle'] = $planet->moonAngle;
 			$this->PuzzlePlanet->save($newplanet);
 		}
 		foreach($json_data->astronauts as $astronaut)
 		{
 			$newastro['PuzzleAstronaut']['id'] = null;
 			$newastro['PuzzleAstronaut']['puzzle_id'] = $puzzle_id;
 			$newastro['PuzzleAstronaut']['x'] = $astronaut->x;
 			$newastro['PuzzleAstronaut']['y'] = $astronaut->y;
 			$this->PuzzleAstronaut->save($newastro);
 		}
 		echo $puzzle_id;
 		exit;
 	}
 	
 	function saveSolution($puzzle_id)
 	{
 		$json_data = json_decode($_POST['json_data']);
 		$puzzle = $this->Puzzle->find('first',array('conditions'=>'Puzzle.id = '.$puzzle_id));
 		if($puzzle['Puzzle']['least_fuel_used'] == 0 || $puzzle['Puzzle']['least_fuel_used'] > $json_data->fuel_used) $puzzle['Puzzle']['least_fuel_used'] = $json_data->fuel_used;
 		if($puzzle['Puzzle']['fastest_solution'] == 0 || $puzzle['Puzzle']['fastest_solution'] > $json_data->travelTime) $puzzle['Puzzle']['fastest_solution'] = $json_data->travelTime;
 		$this->Puzzle->save($puzzle);
 		$puzzle_solution['PuzzleSolution']['puzzle_id'] = $puzzle_id;
 		$puzzle_solution['PuzzleSolution']['account_id'] = 1;
 		$puzzle_solution['PuzzleSolution']['fuel_used'] = $json_data->fuel_used;
 		$puzzle_solution['PuzzleSolution']['time'] = $json_data->travelTime;
 		$this->PuzzleSolution->save($puzzle_solution);
 		$puzzle_solution_id = $this->PuzzleSolution->id;
 		$order = 1;
 		foreach($json_data->way_points as $point)
 		{
 			$newpoint['PuzzleSolutionWayPoint']['id'] = null;
 			$newpoint['PuzzleSolutionWayPoint']['puzzle_solution_id'] = $puzzle_solution_id;
 			$newpoint['PuzzleSolutionWayPoint']['order'] = $order;
 			$newpoint['PuzzleSolutionWayPoint']['x'] = $point->x;
 			$newpoint['PuzzleSolutionWayPoint']['y'] = $point->y;
 			$this->PuzzleSolutionWayPoint->save($newpoint);
 			$order++;
 		}
 		exit;
 	}
 	
 	function getPuzzles()
 	{
 		$puzzles = $this->Puzzle->find('all',array('order'=>'Puzzle.id ASC'));
 		$return = array();
 		foreach($puzzles as $puzzle):
 			$return[] = $puzzle['Puzzle'];
 		endforeach;
 		echo json_encode($return);
 		exit;
 	}
 	
 	function getPuzzle($puzzle_id)
 	{
 		$return = array();
 		$puzzle = $this->Puzzle->find('first',array('conditions'=>'Puzzle.id = '.$puzzle_id));
 		$return['startData'] = array($puzzle['Puzzle']['start_x'],$puzzle['Puzzle']['start_y']);
 		$return['endData'] = array($puzzle['Puzzle']['end_x'],$puzzle['Puzzle']['end_y']);
 		$return['total_fuel'] = $puzzle['Puzzle']['total_fuel'];
 		$return['least_fuel'] = $puzzle['Puzzle']['least_fuel_used'];
 		$return['fastest_time'] = $puzzle['Puzzle']['fastest_solution'];
 		$astronauts = $this->PuzzleAstronaut->find('all',array('conditions'=>'PuzzleAstronaut.puzzle_id = '.$puzzle_id));
 		$return_astros = array();
 		foreach($astronauts as $astronaut):
 			$return_astros[] = array('x'=>$astronaut['PuzzleAstronaut']['x'],'y'=>$astronaut['PuzzleAstronaut']['y']);	
 		endforeach;
 		$return['astronauts'] = $return_astros;
 		$planets = $this->PuzzlePlanet->find('all',array('conditions'=>'PuzzlePlanet.puzzle_id = '.$puzzle_id));
 		$return_planets = array();
 		foreach($planets as $planet):
 			$return_planets[] = array('x'=>$planet['PuzzlePlanet']['x'],'y'=>$planet['PuzzlePlanet']['y'],'radius'=>$planet['PuzzlePlanet']['radius'],'density'=>$planet['PuzzlePlanet']['density'],'hasMoon'=>$planet['PuzzlePlanet']['hasMoon'],'moonAngle'=>$planet['PuzzlePlanet']['moonAngle']);	
 		endforeach;
 		$return['planets'] = $return_planets;
 		echo json_encode($return);
 		exit;
 	}
	
}

?>