<?php

class PuzzlesController extends AppController {


	var $uses = array('Puzzle','Account','PuzzlePlanet','PuzzleAstronaut','PuzzleSolution','PuzzleSolutionWayPoint','PuzzleItem','PuzzleVote');
	var $components = array('Auth');
	
	function beforeFilter()
 	{
 		$this->Auth->allow('savePuzzle','getPuzzles','getPuzzle','getPuzzleWithSolution','saveSolution','saveImage','getPuzzleTimes','voteForPuzzle');
 		parent::beforeFilter();
 	}
 	
 	function confidence_rating($ups,$downs)
 	{
 		if($ups + $downs == 0)
 			return 0;
 		if($ups == 0)
 			return $downs;
		$n = $ups + $downs;
		//z = 1.0; #1.0 = 85%, 1.6 = 95%
		$z = 1.281551565545; # 80% confidence
		$p = $ups/$n;
    	$left = $p + 1/(2*$n)*$z*$z;
    	$right = $z*sqrt($p*(1-$p)/$n + $z*$z/(4*$n*$n));
    	$under = 1+1/$n*$z*$z;
    	return ($left - $right) / $under;
	}
 	
 	function hot_rating($ups,$downs,$seconds)
 	{
		$s = $ups - $downs;
		$order = log(max(abs(s), 1), 10);
		if($s > 0)
		{
			$sign = 1;
		}elseif($s < 0)
		{
			$sign = -1;
		}else{
			$sign = 0;
		}
		$seconds = $seconds - 1134028003;
		return round($order + $sign * $seconds / 45000, 7);
 	}
 	
 	function voteForPuzzle($device_id,$puzzle_id,$vote)
 	{
 	
 		$account = $this->Account->find('first',array('conditions'=>'Account.device_id = "'.$device_id.'"'));
 		if(isset($account['Account']['id']))
 		{
 			$account_id = $account['Account']['id'];
 			$previous_vote = $this->PuzzleVote->find('first',array('conditions'=>'PuzzleVote.account_id = '.$account_id.' AND PuzzleVote.puzzle_id = '.$puzzle_id));
 		}else{
 			$account['Account']['id'] = null;
 			$account['Account']['device_id'] = $device_id;
 			$this->Account->save($account);
 			$account_id = $this->Account->id;
 		}
 		
 		$puzzle = $this->Puzzle->findById($puzzle_id);
 		
 		if(!isset($previous_vote['PuzzleVote']['id']))
 		{
 			$previous_vote = array('PuzzleVote'=>array('id'=>null,'puzzle_id'=>$puzzle_id,'account_id'=>$account_id,'vote'=>$vote));
 			if($vote == 1)
 			{
 				$puzzle['Puzzle']['up_votes']++;
 			}else{
 				$puzzle['Puzzle']['down_votes']--;
 			}
 		}else{
 			if($previous_vote['PuzzleVote']['vote'] == 1 && $vote == -1)
 			{
 				$puzzle['Puzzle']['up_votes']--;
 				$puzzle['Puzzle']['down_votes']++;
 			}elseif($previous_vote['PuzzleVote']['vote'] == -1 && $vote == 1)
 			{
 				$puzzle['Puzzle']['up_votes']++;
 				$puzzle['Puzzle']['down_votes']--;
 			}
 			$previous_vote['PuzzleVote']['vote'] = $vote;
 		}
 		$this->PuzzleVote->save($previous_vote);
 		
 		$puzzle['Puzzle']['hot_rating'] = $this->hot_rating($puzzle['Puzzle']['up_votes'],$puzzle['Puzzle']['down_votes'],strtotime($puzzle['Puzzle']['created']));
 		$puzzle['Puzzle']['confidence_rating'] = $this->confidence_rating($puzzle['Puzzle']['up_votes'],$puzzle['Puzzle']['down_votes']);
 		$this->Puzzle->save($puzzle);
 		echo 1;
 		exit;
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
 		$device_id = $json_data->device_id;
 		$account = $this->Account->find('first',array('conditions'=>'Account.device_id = "'.$device_id.'"'));
 		if(isset($account['Account']['id']))
 		{
 			$account_id = $account['Account']['id'];
 		}else{
 			$account['Account']['id'] = null;
 			$account['Account']['device_id'] = $device_id;
 			$this->Account->save($account);
 			$account_id = $this->Account->id;
 		}
 		
 		$puzzle['Puzzle']['account_id'] = $account_id;
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
 			$this->PuzzleItem->query('DELETE FROM `puzzle_items` WHERE `puzzle_id` = '.$puzzle_id);
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
 			$newplanet['PuzzlePlanet']['anti_gravity'] = $planet->antiGravity;
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
 		foreach($json_data->items as $item)
 		{
 			$newitem['PuzzleItem']['id'] = null;
 			$newitem['PuzzleItem']['puzzle_id'] = $puzzle_id;
 			$newitem['PuzzleItem']['type'] = $item->type;
 			$newitem['PuzzleItem']['x'] = $item->x;
 			$newitem['PuzzleItem']['y'] = $item->y;
 			$this->PuzzleItem->save($newitem);
 		}
 		echo $puzzle_id;
 		exit;
 	}
 	
 	function saveSolution($puzzle_id)
 	{
 		$json_data = json_decode($_POST['json_data']);
 		CakeLog::write('savePuzzle',print_r($json_data,true));
 		$device_id = $json_data->device_id;
 		$account = $this->Account->find('first',array('conditions'=>'Account.device_id = "'.$device_id.'"'));
 		if(isset($account['Account']['id']))
 		{
 			$account_id = $account['Account']['id'];
 		}else{
 			$account['Account']['id'] = null;
 			$account['Account']['device_id'] = $device_id;
 			$this->Account->save($account);
 			$account_id = $this->Account->id;
 		}
 		
 		$puzzle = $this->Puzzle->find('first',array('conditions'=>'Puzzle.id = '.$puzzle_id));
 		if($puzzle['Puzzle']['least_fuel_used'] == 0 || $puzzle['Puzzle']['least_fuel_used'] > $json_data->fuel_used) $puzzle['Puzzle']['least_fuel_used'] = $json_data->fuel_used;
 		if($puzzle['Puzzle']['fastest_solution'] == 0 || $puzzle['Puzzle']['fastest_solution'] > $json_data->travelTime) $puzzle['Puzzle']['fastest_solution'] = $json_data->travelTime;
 		$this->Puzzle->save($puzzle);
 		
 		$save_this = 0;
 		$any_previous = $this->PuzzleSolution->find('count',array('conditions'=>'PuzzleSolution.puzzle_id = '.$puzzle_id.' AND PuzzleSolution.account_id = '.$account_id));
 		if($any_previous == 0) $save_this = 1;
 		$previous_fuel = $this->PuzzleSolution->find('first',array('conditions'=>'PuzzleSolution.puzzle_id = '.$puzzle_id.' AND PuzzleSolution.account_id = '.$account_id.' AND PuzzleSolution.fuel_used > '.$json_data->fuel_used));
 		$previous_time = $this->PuzzleSolution->find('first',array('conditions'=>'PuzzleSolution.puzzle_id = '.$puzzle_id.' AND PuzzleSolution.account_id = '.$account_id.' AND PuzzleSolution.time > '.$json_data->travelTime));
 		if(isset($previous_fuel['PuzzleSolution']['id']) || isset($previous_time['PuzzleSolution']['id'])) $save_this = 1;
 		if($save_this)
 		{
	 		$puzzle_solution['PuzzleSolution']['puzzle_id'] = $puzzle_id;
	 		$puzzle_solution['PuzzleSolution']['account_id'] = $account_id;
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
	 		
		 	$best_fuel = $this->PuzzleSolution->find('first',array('conditions'=>'PuzzleSolution.puzzle_id = '.$puzzle_id.' AND PuzzleSolution.account_id = '.$account_id,'order'=>'PuzzleSolution.fuel_used ASC'));
		 	$best_time = $this->PuzzleSolution->find('first',array('conditions'=>'PuzzleSolution.puzzle_id = '.$puzzle_id.' AND PuzzleSolution.account_id = '.$account_id,'order'=>'PuzzleSolution.time ASC'));
		 	$remove_these = $this->PuzzleSolution->find('all',array('conditions'=>'PuzzleSolution.puzzle_id = '.$puzzle_id.' AND PuzzleSolution.account_id = '.$account_id.' AND PuzzleSolution.id != '.$best_fuel['PuzzleSolution']['id'].' AND PuzzleSolution.id != '.$best_time['PuzzleSolution']['id']));
		 	foreach($remove_these as $these):
 				$this->PuzzleSolutionWayPoint->query('DELETE FROM `puzzle_solution_way_points` WHERE `puzzle_solution_id` = '.$these['PuzzleSolution']['id']);
 				$this->PuzzleSolution->delete($these['PuzzleSolution']['id']);
		 	endforeach;
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
 			$return_planets[] = array('x'=>$planet['PuzzlePlanet']['x'],'y'=>$planet['PuzzlePlanet']['y'],'radius'=>$planet['PuzzlePlanet']['radius'],'density'=>$planet['PuzzlePlanet']['density'],'antiGravity'=>$planet['PuzzlePlanet']['anti_gravity'],'hasMoon'=>$planet['PuzzlePlanet']['hasMoon'],'moonAngle'=>$planet['PuzzlePlanet']['moonAngle']);	
 		endforeach;
 		$return['planets'] = $return_planets;
 		$items = $this->PuzzleItem->find('all',array('conditions'=>'PuzzleItem.puzzle_id = '.$puzzle_id));
 		$return_items = array();
 		foreach($items as $item):
 			$return_items[] = array('type'=>$item['PuzzleItem']['type'],'x'=>$item['PuzzleItem']['x'],'y'=>$item['PuzzleItem']['y']);	
 		endforeach;
 		$return['items'] = $return_items;
 		echo json_encode($return);
 		exit;
 	}
 	
 	function getPuzzleWithSolution($puzzle_id,$solution_id)
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
 			$return_planets[] = array('x'=>$planet['PuzzlePlanet']['x'],'y'=>$planet['PuzzlePlanet']['y'],'radius'=>$planet['PuzzlePlanet']['radius'],'density'=>$planet['PuzzlePlanet']['density'],'antiGravity'=>$planet['PuzzlePlanet']['anti_gravity'],'hasMoon'=>$planet['PuzzlePlanet']['hasMoon'],'moonAngle'=>$planet['PuzzlePlanet']['moonAngle']);	
 		endforeach;
 		$return['planets'] = $return_planets;
 		$items = $this->PuzzleItem->find('all',array('conditions'=>'PuzzleItem.puzzle_id = '.$puzzle_id));
 		$return_items = array();
 		foreach($items as $item):
 			$return_items[] = array('type'=>$item['PuzzleItem']['type'],'x'=>$item['PuzzleItem']['x'],'y'=>$item['PuzzleItem']['y']);	
 		endforeach;
 		$return['items'] = $return_items;
 		$solution_points = array();
 		$solution_way_points = $this->PuzzleSolutionWayPoint->find('all',array('conditions'=>'PuzzleSolutionWayPoint.puzzle_solution_id = '.$solution_id,'order'=>'PuzzleSolutionWayPoint.order ASC'));
 		foreach($solution_way_points as $way_point):
 			$solution_points[] = array('x'=>$way_point['PuzzleSolutionWayPoint']['x'],'y'=>$way_point['PuzzleSolutionWayPoint']['y']);
 		endforeach;
 		$return['way_points'] = $solution_points;
 		echo json_encode($return);
 		exit;
 	}
 	
 	
 	function getPuzzleTimes($puzzle_id,$device_id)
 	{
 		$account = $this->Account->find('first',array('conditions'=>'Account.device_id = "'.$device_id.'"'));
 		if(isset($account['Account']['id']))
 		{
 			$account_id = $account['Account']['id'];
 		}else{
 			$account['Account']['id'] = null;
 			$account['Account']['device_id'] = $device_id;
 			$this->Account->save($account);
 			$account_id = $this->Account->id;
 		}
 		$return = array();
 		$puzzle = $this->Puzzle->find('first',array('conditions'=>'Puzzle.id = '.$puzzle_id));
 		$return['least_fuel'] = $puzzle['Puzzle']['least_fuel_used'];
 		$return['fastest_time'] = $puzzle['Puzzle']['fastest_solution'];
 		$fastest_time = $this->PuzzleSolution->find('first',array('conditions'=>'PuzzleSolution.puzzle_id = '.$puzzle_id,'order'=>'PuzzleSolution.time ASC'));
 		if(isset($fastest_time['PuzzleSolution']['id']))
 		{
 			$return['fastest_time_id'] = $fastest_time['PuzzleSolution']['id'];
 		}else{
 			$return['fastest_time_id'] = 0;
 		}
 		$least_fuel = $this->PuzzleSolution->find('first',array('conditions'=>'PuzzleSolution.puzzle_id = '.$puzzle_id,'order'=>'PuzzleSolution.fuel_used ASC'));
 		if(isset($least_fuel['PuzzleSolution']['id']))
 		{
 			$return['least_fuel_id'] = $least_fuel['PuzzleSolution']['id'];
 		}else{
 			$return['least_fuel_id'] = 0;
 		}
 		
 		
 		$your_fastest_time = $this->PuzzleSolution->find('first',array('conditions'=>'PuzzleSolution.puzzle_id = '.$puzzle_id.' AND PuzzleSolution.account_id = '.$account_id,'order'=>'PuzzleSolution.time ASC'));
 		if(isset($your_fastest_time['PuzzleSolution']['id']))
 		{
 			$return['your_fastest_time'] = $your_fastest_time['PuzzleSolution']['time'];
 			$return['your_fastest_time_id'] = $your_fastest_time['PuzzleSolution']['id'];
 		}else{
 			$return['your_fastest_time'] = 0;
 			$return['your_fastest_time_id'] = 0;
 		}
 		$your_least_fuel = $this->PuzzleSolution->find('first',array('conditions'=>'PuzzleSolution.puzzle_id = '.$puzzle_id.' AND PuzzleSolution.account_id = '.$account_id,'order'=>'PuzzleSolution.fuel_used ASC'));
 		if(isset($your_least_fuel['PuzzleSolution']['id']))
 		{
 			$return['your_least_fuel'] = $your_least_fuel['PuzzleSolution']['fuel_used'];
 			$return['your_least_fuel_id'] = $your_least_fuel['PuzzleSolution']['id'];
 		}else{
 			$return['your_least_fuel'] = 0;
 			$return['your_least_fuel_id'] = 0;
 		}
 		echo json_encode($return);
 		exit;
 	}
	
}

?>