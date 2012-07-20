<?php

App::uses('AppController', 'Controller');
class GamesController extends AppController {
	public $name = 'Games';
	public $helpers = array('Html', 'Session');
	public $uses = array('Game','Question','QuestionVersion','GameSnapshot');
	
	public function beforeFilter()
	{
		$this->Auth->allow('json_data','play','play_question','play_version');
	}
	
	public function refresher()
	{
		echo '<meta http-equiv="refresh" content="60;url=/games/refresher"><h2>Here is a random number: '.(rand() % 100  + 1).'</h2>';
		exit;
	}
	
	public function index() {
		
		$this->Game->bindModel(array(
			'hasMany'=>array(
				'Question'=>array(
					'className'=>'Question',
					'foreignKey'=>'game_id',
					'conditions'=>'Question.deleted = 0'
				)
			)
		));
		$games = $this->Game->find('all');
		foreach($games as $key=>$game):
			$count = $this->QuestionVersion->find('count',array('conditions'=>'QuestionVersion.question_id IN (SELECT `id` FROM `questions` WHERE `game_id` = '.$game['Game']['id'].')'));
			$games[$key]['Game']['version'] = $count;		
		endforeach;
		$this->set('games',$games);
	}
	public function snapshots($game_id)
	{
		$this->Game->bindModel(array(
			'hasMany'=>array(
				'GameSnapshot'=>array(
					'className'=>'GameSnapshot',
					'foreignKey'=>'game_id',
					'order'=>'GameSnapshot.time DESC'
				)
			)
		));
		$game = $this->Game->findById($game_id);
		$this->set('game', $game);
	}
	public function add_snapshot($game_id,$version_id=0)
	{
		$this->set('game_id',$game_id);
		$this->set('version_id',$version_id);
		if(isset($this->data['GameSnapshot']))
		{
			$snapshot = $this->data;
			$snapshot['GameSnapshot']['note'] = htmlspecialchars($this->removeInvis(nl2br($snapshot['GameSnapshot']['note'])), ENT_QUOTES);
			if($version_id == 0)
			{
				$snapshot['GameSnapshot']['time'] = time();
				$count = $this->QuestionVersion->find('count',array('conditions'=>'QuestionVersion.question_id IN (SELECT `id` FROM `questions` WHERE `game_id` = '.$game_id.')'));
				$snapshot['GameSnapshot']['version'] = $count;
			}else{
				$version = $this->QuestionVersion->findById($version_id);
				$snapshot['GameSnapshot']['time'] = strtotime($version['QuestionVersion']['created']);
				$count = $this->QuestionVersion->find('count',array('conditions'=>'QuestionVersion.question_id IN (SELECT `id` FROM `questions` WHERE `game_id` = '.$game_id.') AND QuestionVersion.created <= "'.$version['QuestionVersion']['created'].'"'));
				$snapshot['GameSnapshot']['version'] = $count;
			}
			$snapshot['GameSnapshot']['published'] = 1;
			$snapshot['GameSnapshot']['game_id'] = $game_id;
			if($this->GameSnapshot->save($snapshot))
			{
				$this->redirect('/games/snapshots/'.$game_id);
			}
		}
	}
	
	public function edit_snapshot($game_id,$snapshot_id)
	{
		$this->set('game_id',$game_id);
		$this->set('snapshot_id',$snapshot_id);
		$snapshot = $this->GameSnapshot->findById($snapshot_id);
		if(isset($this->data['GameSnapshot']))
		{
			$snapshot = $this->data;
			$snapshot['GameSnapshot']['note'] = htmlspecialchars($this->removeInvis(nl2br($snapshot['GameSnapshot']['note'])), ENT_QUOTES);
			if($this->GameSnapshot->save($snapshot))
			{
				$this->redirect('/games/snapshots/'.$game_id);
			}
		} else {
			$snapshot['GameSnapshot']['note'] = str_replace('<br />','
',htmlspecialchars_decode($snapshot['GameSnapshot']['note'], ENT_QUOTES));
			$this->data = $snapshot;
		}
	}
	
	public function delete_snapshot($game_id,$snapshot_id)
	{
		$this->GameSnapshot->delete($snapshot_id);
		$this->redirect('/games/snapshots/'.$game_id);
	}
	
	public function publish_snapshot($game_id,$snapshot_id)
	{
		$snapshot['GameSnapshot']['id'] = $snapshot_id;
		$snapshot['GameSnapshot']['published'] = 1;
		$this->GameSnapshot->save($snapshot);
		$this->redirect('/games/snapshots/'.$game_id);
	}
	
	public function unpublish_snapshot($game_id,$snapshot_id)
	{
		$snapshot['GameSnapshot']['id'] = $snapshot_id;
		$snapshot['GameSnapshot']['published'] = 0;
		$this->GameSnapshot->save($snapshot);
		$this->redirect('/games/snapshots/'.$game_id);
	}
	
	public function add()
	{
		if(isset($this->data['Game']))
		{
			$game = $this->data;
			if($this->data['Game']['icon']['error'] == 0 && $this->data['Game']['icon']['size'] > 0) $game['Game']['has_icon'] = 1;
			if($this->Game->save($game))
			{
				if($this->data['Game']['icon']['error'] == 0 && $this->data['Game']['icon']['size'] > 0)
				{
					move_uploaded_file($this->data['Game']['icon']['tmp_name'], WWW_ROOT.'img'.DS.'game_icons'.DS.$this->Game->id.'.png');
				}
				$this->redirect('/games');
			}
		}
	}
	
	public function edit($game_id)
	{
		$game = $this->Game->findById($game_id);
		if(isset($this->data['Game']))
		{
			$game = $this->data;
			if($this->data['Game']['icon']['error'] == 0 && $this->data['Game']['icon']['size'] > 0) $game['Game']['has_icon'] = 1;
			if($this->Game->save($game))
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
	
	public function delete($game_id)
	{
		if($this->Game->delete($game_id))
			$this->Game->query('DELETE FROM `questions` WHERE `game_id` = '.$game_id);
		$this->redirect('/games');
	}
	
	public function version_history($game_id)
	{
		$game = $this->Game->findById($game_id);
		$this->set('game',$game);
		$this->QuestionVersion->bindModel(
			array(
				'belongsTo'=> array(
	        		'User' => array(
	            		'className' => 'User',
	            		'foreignKey' => 'user_id'
	        		)
	        	)
	        )
	    );
		$versions = $this->QuestionVersion->find('all',array('conditions'=>'QuestionVersion.question_id IN (SELECT `id` FROM `questions` WHERE `game_id` = '.$game_id.')','order'=>'QuestionVersion.created DESC','recursive'=>2));
		$this->set('versions',$versions);
	}
	
	
	
	public function play_version($game_id,$snapshot_id) {
		$snapshot = $this->GameSnapshot->findById($snapshot_id);
		if(isset($snapshot['GameSnapshot']['id']) && $snapshot['GameSnapshot']['published'] == 1)
		{
			$this->layout = false;
			$this->set('preview_timers',0);
			$this->set('game',$this->Game->findById($game_id));
			$this->set('snapshot',$snapshot['GameSnapshot']['time']);
		}else{
			echo '<h2>This version of the game is no longer available.</h2>';
			exit;
		}
		$this->render('play');
	}
	
	public function play($game_id,$snapshot=0) {
		$this->layout = false;
		$this->set('preview_timers',0);
		$this->set('game',$this->Game->findById($game_id));
		if($snapshot > 0) $this->set('snapshot',$snapshot);
	}
	
	public function preview($game_id) {
		$this->layout = false;
		$this->set('preview_timers',1);
		$this->set('game',$this->Game->findById($game_id));
		$this->render('play');
	}
	
	public function play_question($game_id,$question_id,$version_id=0) {
		$this->layout = false;
		$this->set('preview_timers',0);
		$this->set('game',$this->Game->findById($game_id));
		if($version_id > 0)
		{
			$this->set('question',$this->QuestionVersion->findById($version_id));
		}else{
			$this->set('question',$this->QuestionVersion->find('first',array('conditions'=>'QuestionVersion.question_id = '.$question_id,'order'=>'QuestionVersion.created DESC')));
		}
		$this->render('play');
	}
	
	public function preview_question($game_id,$question_id,$version_id=0) {
		$this->layout = false;
		$this->set('preview_timers',1);
		$this->set('game',$this->Game->findById($game_id));
		if($version_id > 0)
		{
			$this->set('question',$this->QuestionVersion->findById($version_id));
		}else{
			$this->set('question',$this->QuestionVersion->find('first',array('conditions'=>'QuestionVersion.question_id = '.$question_id,'order'=>'QuestionVersion.created DESC')));
		}
		$this->render('play');
	}
	
	public function export($game_id,$time=0)
	{
		$this->layout = false;
		if($time > 0)
		{
			$this->Question->bindModel(array(
				'hasMany'=>array(
					'QuestionVersion'=>array(
						'className'=>'QuestionVersion',
						'foreignKey'=>'question_id',
						'order'=>'QuestionVersion.created DESC',
						'limit'=>1,
						'conditions'=>'QuestionVersion.created <= "'.date('Y-m-d H:i:s',$time).'"'
					)
				)
			));
			$this->Game->bindModel(array(
				'hasMany'=>array(
					'Question'=>array(
						'className'=>'Question',
						'foreignKey'=>'game_id',
						'order'=>'Question.order ASC'
					)
				)
			));
			
			$snap = $this->GameSnapshot->find('first',array('conditions'=>'GameSnapshot.time = '.$time.' AND GameSnapshot.game_id = '.$game_id));
			if(isset($snap['GameSnapshot']['id'])) $this->set('snapshot',$snap);
		}else{
			$this->Question->bindModel(array(
				'hasMany'=>array(
					'QuestionVersion'=>array(
						'className'=>'QuestionVersion',
						'foreignKey'=>'question_id',
						'order'=>'QuestionVersion.created DESC',
						'limit'=>1
					)
				)
			));
			$this->Game->bindModel(array(
				'hasMany'=>array(
					'Question'=>array(
						'className'=>'Question',
						'foreignKey'=>'game_id',
						'order'=>'Question.order ASC'
					)
				)
			));
		}
		$this->set('game',$this->Game->find('first',array('conditions'=>'Game.id = '.$game_id,'recursive'=>2)));
	}
	
	public function json_data($game_id,$snapshot=0,$version_id=0) {
		$this->layout = false;
		if($snapshot > 0)
		{
			$this->Question->bindModel(array(
				'hasMany'=>array(
					'QuestionVersion'=>array(
						'className'=>'QuestionVersion',
						'foreignKey'=>'question_id',
						'order'=>'QuestionVersion.created DESC',
						'limit'=>1,
						'conditions'=>'QuestionVersion.created <= "'.date('Y-m-d H:i:s',$snapshot).'"'
					)
				)
			));
			$this->Game->bindModel(array(
				'hasMany'=>array(
					'Question'=>array(
						'className'=>'Question',
						'foreignKey'=>'game_id',
						'order'=>'Question.order ASC'
					)
				)
			));
		}elseif($version_id > 0)
		{
			$version = $this->QuestionVersion->findById($version_id);
			$this->Question->bindModel(array(
				'hasMany'=>array(
					'QuestionVersion'=>array(
						'className'=>'QuestionVersion',
						'foreignKey'=>'question_id',
						'order'=>'QuestionVersion.created DESC',
						'limit'=>1,
						'conditions'=>'QuestionVersion.id = '.$version_id
					)
				)
			));
			$this->Game->bindModel(array(
				'hasMany'=>array(
					'Question'=>array(
						'className'=>'Question',
						'foreignKey'=>'game_id',
						'order'=>'Question.order ASC',
						'conditions'=>'Question.id = '.$version['QuestionVersion']['question_id']
					)
				)
			));
		}else{
			$this->Question->bindModel(array(
				'hasMany'=>array(
					'QuestionVersion'=>array(
						'className'=>'QuestionVersion',
						'foreignKey'=>'question_id',
						'order'=>'QuestionVersion.created DESC',
						'limit'=>1
					)
				)
			));
			$this->Game->bindModel(array(
				'hasMany'=>array(
					'Question'=>array(
						'className'=>'Question',
						'foreignKey'=>'game_id',
						'order'=>'Question.order ASC'
					)
				)
			));
		}
		$this->set('game',$this->Game->find('first',array('conditions'=>'Game.id = '.$game_id,'recursive'=>2)));
	}
	
	function check_image_folders() {
		echo "/img/clues<br>"
		$formatted_file_count = 0;
		$original_file_count = 0;
		$dir = ROOT.'/app/webroot/img/clues';
		if($resource = opendir($dir)) {
			while(($file = readdir($resource)) !== false) {
				$parts = explode('.', $file);
				if(is_numeric($parts[0]))
					$formatted_file_count++;
			}
			closedir($resource);
		}
		echo "formatted files: ".$formatted_file_count."<br>";
		echo "original files: ".$original_file_count."<br>";
		die;
	}
	
}
?>