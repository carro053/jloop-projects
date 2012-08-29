<?php

class PuzzlesController extends AppController {


	var $uses = array('Puzzle','Account','PuzzlePlanet','PuzzleAstronaut','PuzzleSolution','PuzzleSolutionWayPoint','PuzzleItem','PuzzleVote');
	var $components = array('Auth');
	
	function beforeFilter()
 	{
 		$this->Auth->allow('savePuzzle','getPuzzles','getPuzzle','getPuzzleWithSolution','saveSolution','saveImage','getPuzzleTimes','voteForPuzzle','updateAllPuzzles','viewMissionSolution','getTotalMissions','getAccountInfo','saveAccountInfo','savePushToken');
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
		$degree = 3; // first X votes is worth the same as the next X*X etc.
		$time_weight = 604800; //one week is worth another degree of votes to the degree
		$order = log(max(abs($s), 1), $degree);
		if($s > 0)
		{
			$sign = 1;
		}elseif($s < 0)
		{
			$sign = -1;
		}else{
			$sign = 0;
		}
		$seconds = $seconds - 1325404800; //time since Jan 1st 2012 changing this just shifts the rating linearaly.
		return round($order + $sign * $seconds / $time_weight, 7);
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
 				$puzzle['Puzzle']['down_votes']++;
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
 	
 	function updateAllPuzzles()
 	{
 		$puzzles = $this->Puzzle->find('all');
 		foreach($puzzles as $puzzle):
 			$puzzle['Puzzle']['hot_rating'] = $this->hot_rating($puzzle['Puzzle']['up_votes'],$puzzle['Puzzle']['down_votes'],strtotime($puzzle['Puzzle']['created']));
 			$puzzle['Puzzle']['confidence_rating'] = $this->confidence_rating($puzzle['Puzzle']['up_votes'],$puzzle['Puzzle']['down_votes']);
 			$this->Puzzle->save($puzzle);
 		endforeach;
 		exit;
 	}
 	
 	function saveImage($puzzle_id,$hd=0)
 	{
 		echo 'YES';
 		if($hd)
 		{
 			$hd_part = '@2x';
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
 		$puzzle['Puzzle']['title'] = $json_data->name;
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
 	
 	function getAccountInfo($device_id)
 	{
 		$account = $this->Account->find('first',array('conditions'=>'Account.device_id = "'.$device_id.'"'));
 		if(isset($account['Account']['id']))
 		{
 			$account_id = $account['Account']['id'];
 			if($account['Account']['temp_username'] != '')
 			{
 				$username = $account['Account']['temp_username'];
 			}else{
 				$username = $account['Account']['username'];
 			}
 		}else{
 			$account['Account']['id'] = null;
 			$account['Account']['device_id'] = $device_id;
 			$this->Account->save($account);
 			$account_id = $this->Account->id;
 			$username = "";
 		}
 		echo json_encode(array('account_id'=>$account_id,'username'=>$username));
 		exit;	
 	}
 	
 	function saveAccountInfo()
 	{
 		$json_data = json_decode($_POST['json_data']);
 		if(isset($json_data->account_id))
 		{
 			$this->Account->id = $json_data->account_id;
 		}else{
	 		$device_id = $json_data->device_id;
	 		$account = $this->Account->find('first',array('conditions'=>'Account.device_id = "'.$device_id.'"'));
	 		if(isset($account['Account']['id']))
	 		{
	 			$this->Account->id = $account['Account']['id'];
	 		}else{
	 			$account['Account']['id'] = null;
	 			$account['Account']['device_id'] = $device_id;
	 			$this->Account->save($account);
	 		}
 		}
 		$this->Account->saveField('temp_username',$json_data->username,false);
 		
 		mail('michael@jloop.com','SF Account Name Submitted','A new account name has been submitted: '.$json_data->username.'.');
 		exit;	
 	}
 	function manage_usernames()
 	{
 		$this->layout = 'default';
 		$this->set('accounts',$this->Account->find('all',array('conditions'=>'Account.temp_username != ""')));
 	}
 	
 	function approve_username($account_id)
 	{
 		$account = $this->Account->findById($account_id);
 		$account['Account']['username'] = $account['Account']['temp_username'];
 		$account['Account']['temp_username'] = '';
 		$this->Account->save($account);
 		if($account['Account']['push_token'])
 		{
			if (1 == 1) {
				$apnsHost = 'gateway.sandbox.push.apple.com';
				$apnsPort = 2195;
				$apnsCert = '/var/www/vhosts/jloop.com/subdomains/gravity/httpdocs/apns-dev.pem';
			} else {
				$apnsHost = 'gateway.push.apple.com';
				$apnsPort = 2195;
				$apnsCert = '/var/www/vhosts/jloop.com/subdomains/gravity/httpdocs/apns-prod.pem';
			}
	
			$streamContext = stream_context_create();
			stream_context_set_option($streamContext, 'ssl', 'local_cert', $apnsCert);
			stream_context_set_option($streamContext, 'ssl', 'passphrase', 'a4d6s5');
			//stream_context_set_option($streamContext, 'ssl', 'verify_peer', false);
			$apns = stream_socket_client('ssl://' . $apnsHost . ':' . $apnsPort, $error, $errorString, 2, STREAM_CLIENT_CONNECT,$streamContext);
			if (!$apns)
			{
				print "Failed to connect".$error." ".$errorString;
			}else{
				$payload['aps'] = array('alert' => 'Your username, '.$account['Account']['username'].', has been approved.', 'sound' => 'default');
				$payload['push_data'] = array();
				$payload = json_encode($payload);
				$apnsMessage = chr(0).chr(0).chr(32).pack('H*',str_replace(' ', '',$account['Account']['push_token'])).chr(0).chr(strlen($payload)).$payload;
				fwrite($apns, $apnsMessage);
			}
			fclose($apns);
		}
 		exit;
 	}
 	
 	function savePushToken()
 	{
 		$account = $this->Account->findByDeviceId($_POST['device_id']);
 		$this->Account->id = $account['Account']['id'];
 		$this->Account->saveField('push_token',$_POST['token'],false);
 		exit;
 	}
 	
 	function teststuff($puzzle_id)
 	{
 	$this->Puzzle->bindModel(array('belongsTo'=>array('Account'=>array('className'=>'Account','foreign_key'=>'account_id'))));
 		$this->PuzzleSolution->bindModel(array('belongsTo'=>array('Puzzle'=>array('className'=>'Puzzle','foreign_key'=>'puzzle_id'))));
 		$fastest_time = $this->PuzzleSolution->find('first',array('conditions'=>'PuzzleSolution.puzzle_id = '.$puzzle_id,'order'=>'PuzzleSolution.time ASC','recursive'=>2));
 		echo '<pre>';
 		print_r($fastest_time);
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
 		if($puzzle['Puzzle']['most_fuel_remaining'] == 0 || $puzzle['Puzzle']['most_fuel_remaining'] < $json_data->fuel_remaining) $puzzle['Puzzle']['most_fuel_remaining'] = $json_data->fuel_remaining;
 		if($puzzle['Puzzle']['fastest_solution'] == 0 || $puzzle['Puzzle']['fastest_solution'] > $json_data->travelTime) $puzzle['Puzzle']['fastest_solution'] = $json_data->travelTime;
 		$this->Puzzle->save($puzzle);
 		
 		$save_this = 0;
 		$any_previous = $this->PuzzleSolution->find('count',array('conditions'=>'PuzzleSolution.puzzle_id = '.$puzzle_id.' AND PuzzleSolution.account_id = '.$account_id));
 		if($any_previous == 0) $save_this = 1;
 		$previous_fuel = $this->PuzzleSolution->find('first',array('conditions'=>'PuzzleSolution.puzzle_id = '.$puzzle_id.' AND PuzzleSolution.account_id = '.$account_id.' AND PuzzleSolution.fuel_remaining < '.$json_data->fuel_remaining));
 		$previous_time = $this->PuzzleSolution->find('first',array('conditions'=>'PuzzleSolution.puzzle_id = '.$puzzle_id.' AND PuzzleSolution.account_id = '.$account_id.' AND PuzzleSolution.time > '.$json_data->travelTime));
 		if(isset($previous_fuel['PuzzleSolution']['id']) || isset($previous_time['PuzzleSolution']['id'])) $save_this = 1;
 		if($save_this)
 		{
	 		$puzzle_solution['PuzzleSolution']['puzzle_id'] = $puzzle_id;
	 		$puzzle_solution['PuzzleSolution']['account_id'] = $account_id;
	 		$puzzle_solution['PuzzleSolution']['fuel_remaining'] = $json_data->fuel_remaining;
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
	 	}
	 	
 		$this->Puzzle->bindModel(array('belongsTo'=>array('Account'=>array('className'=>'Account','foreign_key'=>'account_id'))));
 		$this->PuzzleSolution->bindModel(array('belongsTo'=>array('Puzzle'=>array('className'=>'Puzzle','foreign_key'=>'puzzle_id'))));
 		$fastest_time = $this->PuzzleSolution->find('first',array('conditions'=>'PuzzleSolution.puzzle_id = '.$puzzle_id,'order'=>'PuzzleSolution.time ASC','recursive'=>2));
 		$this->Puzzle->bindModel(array('belongsTo'=>array('Account'=>array('className'=>'Account','foreign_key'=>'account_id'))));
 		$this->PuzzleSolution->bindModel(array('belongsTo'=>array('Puzzle'=>array('className'=>'Puzzle','foreign_key'=>'puzzle_id'))));
 		$most_fuel = $this->PuzzleSolution->find('first',array('conditions'=>'PuzzleSolution.puzzle_id = '.$puzzle_id,'order'=>'PuzzleSolution.fuel_remaining DESC','recursive'=>2));
 		if($puzzle_solution_id == $fastest_time['PuzzleSolution']['id'] && $puzzle_solution_id == $most_fuel['PuzzleSolution']['id'])
 		{
 			if (1 == 1) {
				$apnsHost = 'gateway.sandbox.push.apple.com';
				$apnsPort = 2195;
				$apnsCert = '/var/www/vhosts/jloop.com/subdomains/gravity/httpdocs/apns-dev.pem';
			} else {
				$apnsHost = 'gateway.push.apple.com';
				$apnsPort = 2195;
				$apnsCert = '/var/www/vhosts/jloop.com/subdomains/gravity/httpdocs/apns-prod.pem';
			}
	
			$streamContext = stream_context_create();
			stream_context_set_option($streamContext, 'ssl', 'local_cert', $apnsCert);
			stream_context_set_option($streamContext, 'ssl', 'passphrase', 'a4d6s5');
			//stream_context_set_option($streamContext, 'ssl', 'verify_peer', false);
			$apns = stream_socket_client('ssl://' . $apnsHost . ':' . $apnsPort, $error, $errorString, 2, STREAM_CLIENT_CONNECT,$streamContext);
			if (!$apns)
			{
				print "Failed to connect".$error." ".$errorString;
			}else{
				$payload['aps'] = array('alert' => 'Someone has set a new Fuel and Time record for one of your Missions!', 'sound' => 'default');
				$payload['push_data'] = array();
				$payload = json_encode($payload);
				$apnsMessage = chr(0).chr(0).chr(32).pack('H*',str_replace(' ', '',$fastest_time['Puzzle']['account']['push_token'])).chr(0).chr(strlen($payload)).$payload;
				fwrite($apns, $apnsMessage);
			}
			fclose($apns);
 		}elseif($puzzle_solution_id == $fastest_time['PuzzleSolution']['id'])
 		{
 			if (1 == 1) {
				$apnsHost = 'gateway.sandbox.push.apple.com';
				$apnsPort = 2195;
				$apnsCert = '/var/www/vhosts/jloop.com/subdomains/gravity/httpdocs/apns-dev.pem';
			} else {
				$apnsHost = 'gateway.push.apple.com';
				$apnsPort = 2195;
				$apnsCert = '/var/www/vhosts/jloop.com/subdomains/gravity/httpdocs/apns-prod.pem';
			}
	
			$streamContext = stream_context_create();
			stream_context_set_option($streamContext, 'ssl', 'local_cert', $apnsCert);
			stream_context_set_option($streamContext, 'ssl', 'passphrase', 'a4d6s5');
			//stream_context_set_option($streamContext, 'ssl', 'verify_peer', false);
			$apns = stream_socket_client('ssl://' . $apnsHost . ':' . $apnsPort, $error, $errorString, 2, STREAM_CLIENT_CONNECT,$streamContext);
			if (!$apns)
			{
				print "Failed to connect".$error." ".$errorString;
			}else{
				$payload['aps'] = array('alert' => 'Someone has set a new Time record for one of your Missions!', 'sound' => 'default');
				$payload['push_data'] = array();
				$payload = json_encode($payload);
				$apnsMessage = chr(0).chr(0).chr(32).pack('H*',str_replace(' ', '',$fastest_time['Puzzle']['account']['push_token'])).chr(0).chr(strlen($payload)).$payload;
				fwrite($apns, $apnsMessage);
			}
			fclose($apns);
 		}elseif($puzzle_solution_id == $most_fuel['PuzzleSolution']['id'])
 		{
 			if (1 == 1) {
				$apnsHost = 'gateway.sandbox.push.apple.com';
				$apnsPort = 2195;
				$apnsCert = '/var/www/vhosts/jloop.com/subdomains/gravity/httpdocs/apns-dev.pem';
			} else {
				$apnsHost = 'gateway.push.apple.com';
				$apnsPort = 2195;
				$apnsCert = '/var/www/vhosts/jloop.com/subdomains/gravity/httpdocs/apns-prod.pem';
			}
	
			$streamContext = stream_context_create();
			stream_context_set_option($streamContext, 'ssl', 'local_cert', $apnsCert);
			stream_context_set_option($streamContext, 'ssl', 'passphrase', 'a4d6s5');
			//stream_context_set_option($streamContext, 'ssl', 'verify_peer', false);
			$apns = stream_socket_client('ssl://' . $apnsHost . ':' . $apnsPort, $error, $errorString, 2, STREAM_CLIENT_CONNECT,$streamContext);
			if (!$apns)
			{
				print "Failed to connect".$error." ".$errorString;
			}else{
				$payload['aps'] = array('alert' => 'Someone has set a new Fuel record for one of your Missions!', 'sound' => 'default');
				$payload['push_data'] = array();
				$payload = json_encode($payload);
				$apnsMessage = chr(0).chr(0).chr(32).pack('H*',str_replace(' ', '',$most_fuel['Puzzle']['account']['push_token'])).chr(0).chr(strlen($payload)).$payload;
				fwrite($apns, $apnsMessage);
			}
			fclose($apns);
 		}
 		exit;
 	}
 	function getTotalMissions()
 	{
 		
 		$return = array($this->Puzzle->find('count'));
 		echo json_encode($return);
 		exit;
 	}
 	
 	function getPuzzles($order=1,$index = 0, $per_page = 15)
 	{
 		$order_condition = 'Puzzle.hot_rating DESC';
 		if($order)
 		{
 			$order_condition = 'Puzzle.hot_rating DESC';
 		}else{
 			$order_condition = 'Puzzle.created DESC';
 		}
 		$puzzles = $this->Puzzle->find('all',array('order'=>$order_condition,'limit'=>$per_page,'offset'=>$index));
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
		$this->Puzzle->bindModel(array('belongsTo'=>array('Account'=>array('className'=>'Account','foreign_key'=>'account_id'))));
 		$puzzle = $this->Puzzle->find('first',array('conditions'=>'Puzzle.id = '.$puzzle_id));
 		$return['title'] = $puzzle['Puzzle']['title'];
 		$return['startData'] = array($puzzle['Puzzle']['start_x'],$puzzle['Puzzle']['start_y']);
 		$return['endData'] = array($puzzle['Puzzle']['end_x'],$puzzle['Puzzle']['end_y']);
 		$return['total_fuel'] = $puzzle['Puzzle']['total_fuel'];
 		$return['made_by'] = 'SpaceCadet #'.$puzzle['Account']['id'];
 		if($puzzle['Account']['username'] != '') $return['made_by'] = $puzzle['Account']['username'];
 		$return['most_fuel_remaining'] = $puzzle['Puzzle']['most_fuel_remaining'];
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
 		$return['title'] = $puzzle['Puzzle']['title'];
 		$return['startData'] = array($puzzle['Puzzle']['start_x'],$puzzle['Puzzle']['start_y']);
 		$return['endData'] = array($puzzle['Puzzle']['end_x'],$puzzle['Puzzle']['end_y']);
 		$return['total_fuel'] = $puzzle['Puzzle']['total_fuel'];
 		$return['most_fuel_remaining'] = $puzzle['Puzzle']['most_fuel_remaining'];
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
 	
 	function viewMissionSolution($puzzle_id,$solution_id)
 	{
 		$this->layout = false;
 		$return = array();
 		$puzzle = $this->Puzzle->find('first',array('conditions'=>'Puzzle.id = '.$puzzle_id));
 		$return['title'] = $puzzle['Puzzle']['title'];
 		$return['startData'] = array($puzzle['Puzzle']['start_x'],$puzzle['Puzzle']['start_y']);
 		$return['endData'] = array($puzzle['Puzzle']['end_x'],$puzzle['Puzzle']['end_y']);
 		$return['total_fuel'] = $puzzle['Puzzle']['total_fuel'];
 		$return['most_fuel_remaining'] = $puzzle['Puzzle']['most_fuel_remaining'];
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
 		$this->set('data',$return); 	
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
		$this->Puzzle->bindModel(array('belongsTo'=>array('Account'=>array('className'=>'Account','foreign_key'=>'account_id'))));
		$this->Puzzle->bindModel(array('hasOne'=>array('PuzzleVote'=>array('className'=>'PuzzleVote','foreign_key'=>'puzzle_id','conditions'=>'PuzzleVote.account_id = '.$account_id))));
 		$puzzle = $this->Puzzle->find('first',array('conditions'=>'Puzzle.id = '.$puzzle_id));
 		if($puzzle['PuzzleVote']['id'] > 0)
 		{
 			$return['vote'] = $puzzle['PuzzleVote']['vote'];
 		}else{
 			$return['vote'] = 0;
 		}
 		$return['title'] = $puzzle['Puzzle']['title'];
 		$return['made_by'] = 'SpaceCadet #'.$puzzle['Account']['id'];
 		if($puzzle['Account']['username'] != '') $return['made_by'] = $puzzle['Account']['username'];
 		$return['total_fuel'] = $puzzle['Puzzle']['total_fuel'];
 		$return['most_fuel_remaining'] = $puzzle['Puzzle']['most_fuel_remaining'];
 		$return['fastest_time'] = $puzzle['Puzzle']['fastest_solution'];
 		$fastest_time = $this->PuzzleSolution->find('first',array('conditions'=>'PuzzleSolution.puzzle_id = '.$puzzle_id,'order'=>'PuzzleSolution.time ASC'));
 		if(isset($fastest_time['PuzzleSolution']['id']))
 		{
 			$return['fastest_time'] = $fastest_time['PuzzleSolution']['time'];
 			$return['fastest_time_id'] = $fastest_time['PuzzleSolution']['id'];
 		}else{
 			$return['fastest_time_id'] = 0;
 		}
 		$most_fuel = $this->PuzzleSolution->find('first',array('conditions'=>'PuzzleSolution.puzzle_id = '.$puzzle_id,'order'=>'PuzzleSolution.fuel_remaining DESC'));
 		if(isset($most_fuel['PuzzleSolution']['id']))
 		{
 			$return['most_fuel_remaining'] = $most_fuel['PuzzleSolution']['fuel_remaining'];
 			$return['most_fuel_id'] = $most_fuel['PuzzleSolution']['id'];
 		}else{
 			$return['most_fuel_id'] = 0;
 		}
 		
 		
 		$your_fastest_time = $this->PuzzleSolution->find('first',array('conditions'=>'PuzzleSolution.puzzle_id = '.$puzzle_id.' AND PuzzleSolution.account_id = '.$account_id,'order'=>'PuzzleSolution.time ASC'));
 		if(isset($your_fastest_time['PuzzleSolution']['id']))
 		{
 			$return['your_fastest_time'] = $your_fastest_time['PuzzleSolution']['time'];
 			$return['your_fastest_time_id'] = $your_fastest_time['PuzzleSolution']['id'];
 		}else{
 			$return['your_fastest_time'] = 0;
 			$return['your_fastest_time_id'] = 0;
 			$return['your_fastest_time_placement'] = 0;
 		}
 		$your_most_fuel = $this->PuzzleSolution->find('first',array('conditions'=>'PuzzleSolution.puzzle_id = '.$puzzle_id.' AND PuzzleSolution.account_id = '.$account_id,'order'=>'PuzzleSolution.fuel_remaining DESC'));
 		if(isset($your_most_fuel['PuzzleSolution']['id']))
 		{
 			$return['your_most_fuel'] = $your_most_fuel['PuzzleSolution']['fuel_remaining'];
 			$return['your_most_fuel_id'] = $your_most_fuel['PuzzleSolution']['id'];
 		}else{
 			$return['your_most_fuel'] = 0;
 			$return['your_most_fuel_id'] = 0;
 			$return['your_most_fuel_placement'] = 0;
 		}
 		
 		$return['your_account_id'] = $account_id;
 		$fastest_times = $this->PuzzleSolution->find('all',array('conditions'=>'PuzzleSolution.puzzle_id = '.$puzzle_id,'order'=>'MIN(PuzzleSolution.time) ASC','group' => 'PuzzleSolution.account_id','limit'=>10,'fields' => array('MIN(PuzzleSolution.time) AS PuzzleSolution__best_time','PuzzleSolution.id','PuzzleSolution.account_id','PuzzleSolution.puzzle_id','PuzzleSolution.time')));
 		$return['fastest_times'] = array();
 		foreach($fastest_times  as $key=>$time):
 			if($time['PuzzleSolution']['account_id'] == $account_id)
 			{
 				$return['your_fastest_time_placement'] = $key + 1;
 			}
 			$this->PuzzleSolution->bindModel(array('belongsTo'=>array('Account'=>array('className'=>'Account','foreign_key'=>'account_id'))));
 			$solution = $this->PuzzleSolution->find('first',array('conditions'=>'PuzzleSolution.puzzle_id = '.$puzzle_id.' AND PuzzleSolution.account_id = '.$time['PuzzleSolution']['account_id'].' AND PuzzleSolution.time = '.$time[0]['PuzzleSolution__best_time']));
 			if($solution['Account']['username'] == '')
 			{
 				$username = 'SpaceCadet #'.$solution['Account']['id'];
 			}else{
 				$username = $solution['Account']['username'];
 			}
 			$return['fastest_times'][] = array('time'=>$time[0]['PuzzleSolution__best_time'],'id'=>$solution['PuzzleSolution']['id'],'account_id'=>$time['PuzzleSolution']['account_id'],'username'=>$username);
 		endforeach;
 		
 		$most_fuels = $this->PuzzleSolution->find('all',array('conditions'=>'PuzzleSolution.puzzle_id = '.$puzzle_id,'order'=>'MAX(PuzzleSolution.fuel_remaining) DESC','group' => 'PuzzleSolution.account_id','limit'=>10,'fields' => array('MAX(PuzzleSolution.fuel_remaining) AS PuzzleSolution__best_fuel','PuzzleSolution.id','PuzzleSolution.account_id','PuzzleSolution.puzzle_id','PuzzleSolution.fuel_remaining')));
 		$return['most_fuels'] = array();
 		foreach($most_fuels  as $key=>$fuel):
 			if($fuel['PuzzleSolution']['account_id'] == $account_id)
 			{
 				$return['your_most_fuel_placement'] = $key + 1;
 			}
 			$this->PuzzleSolution->bindModel(array('belongsTo'=>array('Account'=>array('className'=>'Account','foreign_key'=>'account_id'))));
 			$solution = $this->PuzzleSolution->find('first',array('conditions'=>'PuzzleSolution.puzzle_id = '.$puzzle_id.' AND PuzzleSolution.account_id = '.$fuel['PuzzleSolution']['account_id'].' AND PuzzleSolution.fuel_remaining = '.$fuel[0]['PuzzleSolution__best_fuel']));
 			if($solution['Account']['username'] == '')
 			{
 				$username = 'SpaceCadet #'.$solution['Account']['id'];
 			}else{
 				$username = $solution['Account']['username'];
 			}
 			$return['most_fuels'][] = array('fuel'=>$fuel[0]['PuzzleSolution__best_fuel'],'id'=>$solution['PuzzleSolution']['id'],'account_id'=>$fuel['PuzzleSolution']['account_id'],'username'=>$username);
 		endforeach;
 		
 		echo json_encode($return);
 		exit;
 	}
	
}

?>