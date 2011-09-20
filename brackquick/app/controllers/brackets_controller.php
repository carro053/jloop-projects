<?php
class BracketsController extends AppController {

	public $name = 'Brackets';
	public $helpers = array('Html', 'Session');
	public $uses = array('Match','Group','Tournament','User');
	
	private function rand_string($length)
	{
		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";	

		$size = strlen($chars);
		$str = '';
		for($i=0;$i<$length;$i++)
		{
			$str .= $chars[rand(0,$size-1)];
		}
		return $str;
	}
	public function make_players($limit)
	{
		for($i=1;$i<$limit+1;$i++):
			echo $i.'<br />
';
		endfor;	
		exit();
	}
	
	public function create()
	{
		$this->Group->bindModel(array('hasAndBelongsToMany'=>array('User'=>array('className'=>'User','joinTable'=>'groups_users','foreignKey'=>'group_id','associationForeignKey'=>'user_id'))));
		$this->set('group',$this->Group->find('first',array('conditions'=>'Group.id = 1','recursive'=>'1')));
	}
	
	public function quick_create()
	{
		if(!empty($this->data))
		{
			$quick = $this->data;
			$quick['Tournament']['id'] = null;
			$quick['Tournament']['hash'] = $this->rand_string(8);
			$quick['Tournament']['quick'] = 1;
			$quick['Tournament']['status'] = 'Started';
			$this->Tournament->save($quick);
			$tid = $this->Tournament->id;
			$temp_comps = explode(chr(13),$quick['Tournament']['competitors']);
			$competitors = array();
			foreach($temp_comps as $comp):
				if(trim($comp) != '') $competitors[] = trim($comp);
			endforeach;			
			$first_round = $this->create_bracket($tid,count($competitors),$quick['Tournament']['type']);
			if($quick['Tournament']['seeding'] == 1) shuffle($competitors);
			while(count($competitors) < pow(2,$first_round)) $competitors[] = 'BYE';
			$matches = $this->Match->find('all',array('conditions'=>'Match.tournament_id = '.$tid.' AND Match.round = '.$first_round,'order'=>'Match.order ASC'));
			foreach($matches as $match):
				$match['Match']['top_user'] = $competitors[$match['Match']['top_internal_seed']-1];
				$match['Match']['bottom_user'] = $competitors[$match['Match']['bottom_internal_seed']-1];
				if($match['Match']['bottom_user'] == 'BYE')
				{
					$match['Match']['status'] = 'Bye';
					$winner_to = $this->Match->find('first',array('conditions'=>'Match.tournament_id = '.$match['Match']['tournament_id'].' AND Match.round = '.($first_round-1).' AND (Match.top_internal_seed = '.$match['Match']['top_internal_seed'].' OR Match.bottom_internal_seed = '.$match['Match']['top_internal_seed'].')'));
					if($winner_to['Match']['top_internal_seed'] == $match['Match']['top_internal_seed'])
					{
						$winner_to['Match']['top_user'] = $match['Match']['top_user'];
					}else{
						$winner_to['Match']['bottom_user'] = $match['Match']['top_user'];
					}
					if($winner_to['Match']['top_user'] != '' && $winner_to['Match']['bottom_user'] != '')
					{
						$winner_to['Match']['status'] = 'Playing';
					}else{
						$winner_to['Match']['status'] = 'Waiting';
					}
					$this->Match->save($winner_to);
					if($quick['Tournament']['type'] == 'Double Elimination')
					{
						$loser_to = $this->Match->find('first',array('conditions'=>'Match.tournament_id = '.$match['Match']['tournament_id'].' AND Match.round < '.$first_round.' AND Match.bottom_seed = '.$match['Match']['bottom_internal_seed']));
						$loser_to['Match']['status'] = 'Bye';
						$loser_to['Match']['bottom_user'] = 'BYE';
						$this->Match->save($loser_to);
					}
				}else{
					$match['Match']['status'] = 'Playing';
				}
				$this->Match->save($match);
			endforeach;
			$this->redirect('/brackets/view/'.$quick['Tournament']['hash'].'/'.str_replace(' ','',$quick['Tournament']['name']));
		}
	}
	
	public function view($hash)
	{
		
		$this->Tournament->bindModel(array('hasMany'=>array('Match'=>array('className'=>'Match','foreignKey'=>'tournament_id','order'=>'Match.round DESC, Match.order ASC'))));
		$this->set('tournament',$this->Tournament->find('first',array('conditions'=>'Tournament.hash = "'.$hash.'"','recursive'=>'1')));
	}
	
	private function create_bracket($tournament_id,$user_count,$tournament_type)
	{
		$rounds = 0;
		while(pow(2,$rounds) < ceil($user_count/2)) $rounds++;
		$bracket = array();
		for($cr=0;$cr<=$rounds;$cr++):
			for($mc=1;$mc<=pow(2,$cr);$mc++):
				if($mc == 1)
				{
					$seed = 1;
				}else{
					$base = $cr;
					while($base > 0):
						if(($mc-1)%pow(2,$base) == 0) break;
						$base--;
					endwhile;
					$previous_seed = $bracket[$cr][$mc-pow(2,$base)]['top_seed'];
					$seed = pow(2,$cr-$base) + 1 - $previous_seed;
				}
				$bracket[$cr][$mc]['id'] = null;
				$bracket[$cr][$mc]['tournament_id'] = $tournament_id;
				$bracket[$cr][$mc]['round'] = $cr+1;
				$bracket[$cr][$mc]['order'] = $mc;
				$bracket[$cr][$mc]['status'] = 'Waiting';
				$bracket[$cr][$mc]['top_internal_seed'] = $seed;
				$bracket[$cr][$mc]['top_seed'] = $seed;
				$bracket[$cr][$mc]['bottom_internal_seed'] = pow(2,$cr+1)-$seed+1;
				$bracket[$cr][$mc]['bottom_seed'] = pow(2,$cr+1)-$seed+1;
				$this->Match->save($bracket[$cr][$mc]);
			endfor; 
		endfor;
		if($tournament_type == 'Double Elimination')
		{
			$match['id'] = null;
			$match['tournament_id'] = $tournament_id;
			$match['round'] = 0;
			$match['order'] = 0;
			$match['status'] = 'Waiting';
			$match['top_internal_seed'] = 1;
			$match['top_seed'] = 1;
			$match['top_user'] = '';
			$match['bottom_internal_seed'] = 2;
			$match['bottom_seed'] = 2;
			$match['bottom_user'] = '';
			$this->Match->save($match);
			
			$match['id'] = null;
			$match['tournament_id'] = $tournament_id;
			$match['round'] = -1;
			$match['order'] = 0;
			$match['status'] = 'Waiting';
			$match['top_internal_seed'] = 2;
			$match['top_seed'] = 2;
			$match['top_user'] = '';
			$match['bottom_internal_seed'] = 1;
			$match['bottom_seed'] = 1;
			$match['bottom_user'] = '';
			$this->Match->save($match);
			if($rounds > 0)
			{
				$match['id'] = null;
				$match['tournament_id'] = $tournament_id;
				$match['round'] = 0.5;
				$match['order'] = 2;
				$match['status'] = 'Waiting';
				$match['top_internal_seed'] = 2;
				$match['top_seed'] = 2;
				$match['top_user'] = '';
				$match['bottom_internal_seed'] = 3;
				$match['bottom_seed'] = 3;
				$match['bottom_user'] = '';
				$this->Match->save($match);
				
				$match['id'] = null;
				$match['tournament_id'] = $tournament_id;
				$match['round'] = 1;
				$match['order'] = 2;
				$match['status'] = 'Waiting';
				$match['top_internal_seed'] = 3;
				$match['top_seed'] = 3;
				$match['top_user'] = '';
				$match['bottom_internal_seed'] = 4;
				$match['bottom_seed'] = 4;
				$match['bottom_user'] = '';
				$this->Match->save($match);
			}
			for($cr=2;$cr<=$rounds;$cr++):
				$fms = $this->Match->find('all',array('conditions'=>'Match.tournament_id = '.$tournament_id.' AND Match.round = '.($cr-1).' AND Match.order > '.pow(2,$cr-2),'order'=>'Match.order ASC'));
				$order = pow(2,$cr-1) + 1;
				foreach($fms as $fm):
					$match['id'] = null;
					$match['tournament_id'] = $tournament_id;
					$match['round'] = ($cr-0.5);
					$match['order'] = $order;
					$match['status'] = 'Waiting';
					$match['top_internal_seed'] = $fm['Match']['top_internal_seed'];
					$match['top_seed'] = $fm['Match']['top_internal_seed'];
					$match['top_user'] = '';
					if($fm['Match']['top_internal_seed']%2 != 0)
					{
						$bottom_seed = pow(2,$cr+1) - $fm['Match']['top_internal_seed'];
					}else{
						$bottom_seed = pow(2,$cr+1) - $fm['Match']['top_internal_seed'] + 2;
					}
					$match['bottom_internal_seed'] = $bottom_seed;
					$match['bottom_seed'] = $bottom_seed;
					$match['bottom_user'] = '';
					$this->Match->save($match);
					
					$match['id'] = null;
					$match['tournament_id'] = $tournament_id;
					$match['round'] = $cr;
					$match['order'] = $order;
					$match['status'] = 'Waiting';
					$match['top_internal_seed'] = $bottom_seed;
					$match['top_seed'] = $bottom_seed;
					$match['top_user'] = '';
					$bottom_seed = pow(2,$cr+1) + pow(2,$cr) + 1 - $bottom_seed;
					$match['bottom_internal_seed'] = $bottom_seed;
					$match['bottom_seed'] = $bottom_seed;
					$match['bottom_user'] = '';
					$this->Match->save($match);
					$order++;
					
					$match['id'] = null;
					$match['tournament_id'] = $tournament_id;
					$match['round'] = ($cr-0.5);
					$match['order'] = $order;
					$match['status'] = 'Waiting';
					$match['top_internal_seed'] = $fm['Match']['bottom_internal_seed'];
					$match['top_seed'] = $fm['Match']['bottom_internal_seed'];
					$match['top_user'] = '';
					if($fm['Match']['bottom_internal_seed']%2 != 0)
					{
						$bottom_seed = pow(2,$cr+1) - $fm['Match']['bottom_internal_seed'];
					}else{
						$bottom_seed = pow(2,$cr+1) - $fm['Match']['bottom_internal_seed'] + 2;
					}
					$match['bottom_internal_seed'] = $bottom_seed;
					$match['bottom_seed'] = $bottom_seed;
					$match['bottom_user'] = '';
					$this->Match->save($match);
					
					$match['id'] = null;
					$match['tournament_id'] = $tournament_id;
					$match['round'] = $cr;
					$match['order'] = $order;
					$match['status'] = 'Waiting';
					$match['top_internal_seed'] = $bottom_seed;
					$match['top_seed'] = $bottom_seed;
					$match['top_user'] = '';
					$bottom_seed = pow(2,$cr+1) + pow(2,$cr) + 1 - $bottom_seed;
					$match['bottom_internal_seed'] = $bottom_seed;
					$match['bottom_seed'] = $bottom_seed;
					$match['bottom_user'] = '';
					$this->Match->save($match);
					$order++;
				endforeach;
			endfor;
				
		}
		return $rounds+1;
	}
	
	
	
}
?>