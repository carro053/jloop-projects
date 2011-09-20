<?php
class MatchesController extends AppController {

	public $name = 'Matches';
	public $helpers = array('Html', 'Session');
	public $uses = array('Match','Group','Tournament','User');
	
	public function submit_scores($tournament_hash,$match_id)
	{
		$tournament = $this->Tournament->find('first',array('conditions'=>'Tournament.hash = "'.$tournament_hash.'"'));
		if($tournament['Tournament']['quick'] != 1)
		{
			//for non quick bracket score submitting.
		}
		$match = $this->Match->find('first',array('conditions'=>'Match.id = '.$match_id.' AND Match.tournament_id = '.$tournament['Tournament']['id']));
		if(isset($match['Match']['id']))
		{
		
			if(!empty($this->data))
			{
				$save = $this->data;
				$save['Match']['id'] = $match_id;
				$save['Match']['status'] = 'Finished';
				if(isset($save['Match']['top']))
				{
					$save['Match']['winner'] = 'top';
				}else{
					$save['Match']['winner'] = 'bottom';
				}
				$this->Match->save($save);
				if($tournament['Tournament']['type'] == 'Double Elimination')
				{
					if($match['Match']['round'] == 0)
					{
						if(isset($save['Match']['top']))
						{
							$tournament['Tournament']['status'] = 'Finished';
							$tournament['Tournament']['winner'] = $match['Match']['top_user'];
							$this->Tournament->save($tournament);
							$this->Match->query('DELETE FROM `matches` WHERE `tournament_id` = '.$match['Match']['tournament_id'].' AND `round` = -1');
						}else{
							$move_to = $this->Match->find('first',array('conditions'=>'Match.tournament_id = '.$match['Match']['tournament_id'].' AND Match.round = -1'));		
							$move_to['Match']['bottom_user'] = $match['Match']['top_user'];
							$move_to['Match']['bottom_seed'] = $match['Match']['top_seed'];
							$move_to['Match']['top_user'] = $match['Match']['bottom_user'];
							$move_to['Match']['top_seed'] = $match['Match']['bottom_seed'];
							$move_to['Match']['status'] = 'Playing';
							$this->Match->save($move_to);
						}
					}elseif($match['Match']['round'] == -1)
					{
						$tournament['Tournament']['status'] = 'Finished';
						if(isset($save['Match']['top']))
						{
							$tournament['Tournament']['winner'] = $match['Match']['top_user'];
						}else{
							$tournament['Tournament']['winner'] = $match['Match']['bottom_user'];
						}
						$this->Tournament->save($tournament);
					}elseif($match['Match']['order'] <= pow(2,$match['Match']['round']-1))
					{
						$find = .5;
						while(!isset($winner_to['Match']['id']) && $match['Match']['round'] - $find >= - 1):
							$winner_to = $this->Match->find('first',array('conditions'=>'Match.tournament_id = '.$match['Match']['tournament_id'].' AND Match.round = '.($match['Match']['round']-$find).' AND (Match.top_internal_seed = '.$match['Match']['top_internal_seed'].' OR Match.bottom_internal_seed = '.$match['Match']['top_internal_seed'].') AND Match.status != "Bye"','order'=>'Match.round DESC, Match.order ASC'));
							$find = $find + .5;
						endwhile;
						if(isset($winner_to))
						{
							if($winner_to['Match']['top_internal_seed'] == $match['Match']['top_internal_seed'])
							{
								if(isset($save['Match']['top']))
								{
									$winner_to['Match']['top_user'] = $match['Match']['top_user'];
									$winner_to['Match']['top_seed'] = $match['Match']['top_seed'];
								}else{
									$winner_to['Match']['top_user'] = $match['Match']['bottom_user'];
									$winner_to['Match']['top_seed'] = $match['Match']['bottom_seed'];
								}
							}else{
								if(isset($save['Match']['top']))
								{
									$winner_to['Match']['bottom_user'] = $match['Match']['top_user'];
									$winner_to['Match']['bottom_seed'] = $match['Match']['top_seed'];
								}else{
									$winner_to['Match']['bottom_user'] = $match['Match']['bottom_user'];
									$winner_to['Match']['bottom_seed'] = $match['Match']['bottom_seed'];
								}
							}
							if($winner_to['Match']['top_user'] != '' && $winner_to['Match']['bottom_user'] != '') $winner_to['Match']['status'] = 'Playing';
							$this->Match->save($winner_to);
						}
						
						$find = .5;
						while(!isset($loser_to['Match']['id']) && $match['Match']['round'] - $find >= - 1):
							$loser_to = $this->Match->find('first',array('conditions'=>'Match.tournament_id = '.$match['Match']['tournament_id'].' AND Match.round = '.($match['Match']['round']-$find).' AND (Match.top_internal_seed = '.$match['Match']['bottom_internal_seed'].' OR Match.bottom_internal_seed = '.$match['Match']['bottom_internal_seed'].') AND Match.status != "Bye"','order'=>'Match.round DESC, Match.order ASC'));
							$find = $find + .5;
						endwhile;
						if(isset($loser_to))
						{
							if($loser_to['Match']['top_internal_seed'] == $match['Match']['bottom_internal_seed'])
							{
								if(isset($save['Match']['top']))
								{
									$loser_to['Match']['top_user'] = $match['Match']['bottom_user'];
									$loser_to['Match']['top_seed'] = $match['Match']['bottom_seed'];
								}else{
									$loser_to['Match']['top_user'] = $match['Match']['top_user'];
									$loser_to['Match']['top_seed'] = $match['Match']['top_seed'];
								}
							}else{
								if(isset($save['Match']['top']))
								{
									$loser_to['Match']['bottom_user'] = $match['Match']['bottom_user'];
									$loser_to['Match']['bottom_seed'] = $match['Match']['bottom_seed'];
								}else{
									$loser_to['Match']['bottom_user'] = $match['Match']['top_user'];
									$loser_to['Match']['bottom_seed'] = $match['Match']['top_seed'];
								}
							}
							if($loser_to['Match']['top_user'] != '' && $loser_to['Match']['bottom_user'] != '') $loser_to['Match']['status'] = 'Playing';
							$this->Match->save($loser_to);
						}
					}else{
						$find = .5;
						while(!isset($winner_to['Match']['id']) && $match['Match']['round'] - $find >= - 1):
							$winner_to = $this->Match->find('first',array('conditions'=>'Match.tournament_id = '.$match['Match']['tournament_id'].' AND Match.round = '.($match['Match']['round']-$find).' AND (Match.top_internal_seed = '.$match['Match']['top_internal_seed'].' OR Match.bottom_internal_seed = '.$match['Match']['top_internal_seed'].') AND Match.status != "Bye"','order'=>'Match.round DESC, Match.order ASC'));
							$find = $find + .5;
						endwhile;
						if(isset($winner_to))
						{
							if($winner_to['Match']['top_internal_seed'] == $match['Match']['top_internal_seed'])
							{
								if(isset($save['Match']['top']))
								{
									$winner_to['Match']['top_user'] = $match['Match']['top_user'];
									$winner_to['Match']['top_seed'] = $match['Match']['top_seed'];
								}else{
									$winner_to['Match']['top_user'] = $match['Match']['bottom_user'];
									$winner_to['Match']['top_seed'] = $match['Match']['bottom_seed'];
								}
							}else{
								if(isset($save['Match']['top']))
								{
									$winner_to['Match']['bottom_user'] = $match['Match']['top_user'];
									$winner_to['Match']['bottom_seed'] = $match['Match']['top_seed'];
								}else{
									$winner_to['Match']['bottom_user'] = $match['Match']['bottom_user'];
									$winner_to['Match']['bottom_seed'] = $match['Match']['bottom_seed'];
								}
							}
							if($winner_to['Match']['top_user'] != '' && $winner_to['Match']['bottom_user'] != '') $winner_to['Match']['status'] = 'Playing';
							$this->Match->save($winner_to);
						}
					}
				}else{
					if($tournament['Tournament']['type'] == 'Single Elimination' && $match['Match']['round'] == 1)
					{
						$tournament['Tournament']['status'] = 'Finished';
						if(isset($save['Match']['top']))
						{
							$tournament['Tournament']['winner'] = $match['Match']['top_user'];
						}else{
							$tournament['Tournament']['winner'] = $match['Match']['bottom_user'];
						}
						$this->Tournament->save($tournament);
					}else{
						$move_to = $this->Match->find('first',array('conditions'=>'Match.tournament_id = '.$match['Match']['tournament_id'].' AND Match.round = '.($match['Match']['round']-1).' AND (Match.top_internal_seed = '.$match['Match']['top_internal_seed'].' OR Match.bottom_internal_seed = '.$match['Match']['top_internal_seed'].')'));
						if($move_to['Match']['top_internal_seed'] == $match['Match']['top_internal_seed'])
						{
							if(isset($save['Match']['top']))
							{
								$move_to['Match']['top_user'] = $match['Match']['top_user'];
								$move_to['Match']['top_seed'] = $match['Match']['top_seed'];
							}else{
								$move_to['Match']['top_user'] = $match['Match']['bottom_user'];
								$move_to['Match']['top_seed'] = $match['Match']['bottom_seed'];
							}
						}else{
							if(isset($save['Match']['top']))
							{
								$move_to['Match']['bottom_user'] = $match['Match']['top_user'];
								$move_to['Match']['bottom_seed'] = $match['Match']['top_seed'];
							}else{
								$move_to['Match']['bottom_user'] = $match['Match']['bottom_user'];
								$move_to['Match']['bottom_seed'] = $match['Match']['bottom_seed'];
							}
						}
						if($move_to['Match']['top_user'] != '' && $move_to['Match']['bottom_user'] != '') $move_to['Match']['status'] = 'Playing';
						$this->Match->save($move_to);
					}
				}
				$this->redirect('/brackets/view/'.$tournament['Tournament']['hash'].'/'.str_replace(' ','',$tournament['Tournament']['name']));
			}else{
				$this->set('match',$match);
				$this->set('tournament',$tournament);
			}
		}else{
			echo 'You are trying to go to a page that you can\'t get to on your own. Sorry.';
			exit();
		}
	}
}
?>