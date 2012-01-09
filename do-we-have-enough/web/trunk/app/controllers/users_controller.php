<?php

class UsersController extends AppController
{

	var $name = 'Users';
	var $uses = array('User','Event','Group','GroupsUser','EventsUser','Sms','Notification','UserMobileDevice');
	var $helpers = array('Html','Javascript','Error','Ajax','Form');
	var $components = array('uAuth','uCookie','RequestHandler','Postmark');
	var $access = array ();
	function beforeFilter() {
        parent::beforeFilter();
    }
	function test_postmark()
	{
		//$this->Postmark->to = '<michael@jloop.com>';
		//$this->Postmark->subject = 'This is the subject';
		//$this->Postmark->textMessage = 'Hey Jay, just wanted to let you know it is working.
//The Production Team';
		//$result = $this->Postmark->send();
		$headers = 'From: events@dowehaveenough.com'. "\r\n" .'Reply-To: events@dowehaveenough.com'. "\r\n" .'X-Mailer: PHP/' . phpversion();
		mail($email_to, $email_subject, $email_msg, $headers);	
		exit();
	}
	function generate_hash()
	{
		$unique = 0;
		while($unique == 0)
		{
			$length = rand(4,8);
			$hash = '';
			for($i=0;$i<$length;$i++)
			{
				$which = rand(1,62);
				if($which < 11)	$hash .= chr(rand(48, 57));
				if($which >= 11 && $which < 37)	$hash .= chr(rand(65, 90));
				if($which >= 37 && $which < 63)	$hash .= chr(rand(97, 122));
			}
			$check = $this->EventsUser->find('EventsUser.hash = "'.$hash.'"');
			if(!isset($check['EventsUser']['id']))
			{
				$second_check = $this->Event->find('Event.hash = "'.$hash.'"');
				if(!isset($second_check['Event']['id'])) $unique = 1;
			}
		}
		return $hash;
	}
	
	function event_list()
	{
		$this->layout = 'dowehaveenough_main';
		$this->set('data',$this->Event->findAll('Event.created > (NOW() - INTERVAL 9 DAY)',null,'Event.created DESC'));
	}
	
	function home()
	{
		$this->set('page_title','A Simple Event Planner');
		$this->set('meta_description','Do We Have Enough? Need to find out if you have enough players for a ball game... Enough seats to fill a poker table... Enough participants to hold today\'s meeting?');
		$this->set('page','home');
		$this->layout = 'dowehaveenough_main';
	}
	function help()
	{
		$this->set('page_title','Help');
		$this->set('meta_description','Do We Have Enough? Our help section will walk you through creating an event to determine, Do We Have Enough?');
		$this->layout = 'dowehaveenough_main';
	}
	function create()
	{
		$this->set('page_title','Create an event and find out');
		$this->set('meta_description','Do We Have Enough? Have a reason to get together? Create an event Now. When is it? Where is it? How many people to make it happen? Send out an invite and let DWHE notify you when it is on.');
		$this->layout = 'dowehaveenough_main';
	}
	function old_create()
	{
		$this->layout = 'dowehaveenough_old';
	}
	function check_email()
	{
		$this->User->bindModel(array('hasAndBelongsToMany'=>array('Group' =>array('className'=>'Group','joinTable'=>'groups_users','foreignKey'=>'user_id','associationForeignKey'=>'group_id','conditions'=>'Group.name != "" AND Group.name != "Name this group"','order'=> '','limit'=> '','unique'=>true,'finderQuery'=>'','deleteQuery'=>''))));
		$user = $this->User->find('User.email = "'.$this->params['data']['User']['email'].'"');
		if(isset($user['User']['id']))
		{
			$this->uAuth->set(0,$user['User']['id']);
			$this->set('user',$user);
			$this->render('check_email','ajax');
		}else{
			exit();
		}
	}
	
	function select_group($group_id)
	{
		$this->set('group',$this->Group->find('Group.id = '.$group_id));
		$this->User->bindModel(array('hasAndBelongsToMany'=>array('Group' =>array('className'=>'Group','joinTable'=>'groups_users','foreignKey'=>'user_id','associationForeignKey'=>'group_id','conditions'=>'','order'=> '','limit'=> '','unique'=>true,'finderQuery'=>'','deleteQuery'=>''))));
		$user = $this->User->find('User.email = "'.$this->uAuth->email.'"');
		$this->set('user',$user);
	}
	function edit_group($group_id)
	{
		$this->Group->save($this->params['data']);
		$this->set('group',$this->Group->find('Group.id = '.$group_id));
	}
	function test_time()
	{
		echo strtotime('28 July 2009 11pm').'|';
		
		echo strtotime('28 July 2009 6pm').'|';
		echo strtotime('28 July 2009 6:00pm').'|';
		echo strtotime('28 July 2009 6:00 pm').'|';
		echo strtotime('28 July 2009 6:00 am').'|';
		exit();
	}
	function save_event()
	{
		$this->params['data']['Event']['date'] = date('Y-m-d', strtotime($this->params['data']['Event']['cancel_email']));
		$i=0;
		$current_url = '/event/'.strtolower(str_replace(array('/',' ','"',"'"),array('-','-','',''),$this->params['data']['Event']['name'])).'/'.strtolower(str_replace(array('/',' ','"',"'"),array('-','-','',''),$this->params['data']['Event']['when']));
		while($i<1)
		{
			$already = $this->Event->find('Event.url = "'.$current_url.'"');
			if(!isset($already['Event']['id']))
			{
				$i=1;
			}else{
				$current_url = '/event/'.$this->generate_hash().'/'.strtolower(str_replace(array('/',' ','"',"'"),array('-','-','',''),$this->params['data']['Event']['name'])).'/'.strtolower(str_replace(array('/',' ','"',"'"),array('-','-','',''),$this->params['data']['Event']['when']));
			}
		}
		if($this->params['data']['Event']['cancel_email'] == 1)
		{
			if($this->params['data']['Event']['cancel_email_date'] != 0 && $this->params['data']['Event']['cancel_email_date'] != 1)
			{
				$dates = explode('/',$this->params['data']['Event']['cancel_email_date']);
				$this->params['data']['Event']['cancel_email_date'] = date('j F Y',mktime(null,null,null,$dates[0],$dates[1],$dates[2]));
			}
			if($this->params['data']['Event']['cancel_email_date'] == 0) $this->params['data']['Event']['cancel_email_date'] = date('j F Y',mktime(null,null,null,date('n'),date('j'),date('Y')));
			if($this->params['data']['Event']['cancel_email_date'] == 1) $this->params['data']['Event']['cancel_email_date'] = date('j F Y',mktime(null,null,null,date('n'),date('j')+1,date('Y')));
			if(strtotime($this->params['data']['Event']['cancel_email_date'].' '.$this->params['data']['Event']['cancel_email_time']) > 0)
			{
				$time_str = strtotime($this->params['data']['Event']['cancel_email_date'].' '.$this->params['data']['Event']['cancel_email_time']);
			}else{
				$time_str = strtotime($this->params['data']['Event']['cancel_email_date']);
			} 
			$time_str = $time_str - (3600*($this->params['data']['Event']['timezone'] + 8));
			$this->params['data']['Event']['cancel_email'] = date('Y-m-d H:i:s',$time_str);
		}else{
			$this->params['data']['Event']['cancel_email'] = '0000-00-00 00:00:00';
		}
		if($this->params['data']['Event']['status_email'] == 1)
		{
			if($this->params['data']['Event']['status_email_date'] != 0 && $this->params['data']['Event']['status_email_date'] != 1)
			{
				$dates = explode('/',$this->params['data']['Event']['status_email_date']);
				$this->params['data']['Event']['status_email_date'] = date('j F Y',mktime(null,null,null,$dates[0],$dates[1],$dates[2]));
			}
			if($this->params['data']['Event']['status_email_date'] == 0) $this->params['data']['Event']['status_email_date'] = date('j F Y',mktime(null,null,null,date('n'),date('j'),date('Y')));
			if($this->params['data']['Event']['status_email_date'] == 1) $this->params['data']['Event']['status_email_date'] = date('j F Y',mktime(null,null,null,date('n'),date('j')+1,date('Y')));
			if(strtotime($this->params['data']['Event']['status_email_date'].' '.$this->params['data']['Event']['status_email_time']) > 0)
			{
				$time_str = strtotime($this->params['data']['Event']['status_email_date'].' '.$this->params['data']['Event']['status_email_time']);
			}else{
				$time_str = strtotime($this->params['data']['Event']['status_email_date']);
			} 
			$time_str = $time_str - (3600*($this->params['data']['Event']['timezone'] + 8));
			$this->params['data']['Event']['status_email'] = date('Y-m-d H:i:s',$time_str);
		}else{
			$this->params['data']['Event']['status_email'] = '0000-00-00 00:00:00';
		}
		if($this->params['data']['Event']['where'] == 'Where is it?') $this->params['data']['Event']['where'] = '';
		if($this->params['data']['Event']['details'] == 'Tell me more...') $this->params['data']['Event']['details'] = '';
		$this->Event->save($this->params['data']['Event']);
		$event_id = $this->Event->getLastInsertId();
		if($this->params['data']['Group']['id'] > 0)
		{
			$group_id = $this->params['data']['Group']['id'];
			
			$this->GroupsUser->bindModel(array('belongsTo'=>array('User' =>array('className'=>'User','foreignKey'=>'user_id','conditions'=>'','order'=> '','limit'=> ''))));
			$users = $this->GroupsUser->findAll('GroupsUser.group_id = '.$this->params['data']['Group']['id'].' AND GroupsUser.unsubscribed = 0');
			foreach($users as $user):
				if($user['User']['unsubscribed'] != 1)
				{
					$this->EventsUser->set(array('id'=>NULL));
					$event_user['EventsUser']['id'] = NULL;
					$event_user['EventsUser']['event_id'] = $event_id;
					$event_user['EventsUser']['user_id'] = $user['GroupsUser']['user_id'];
					$event_user['EventsUser']['hash'] = $this->generate_hash();
					$this->EventsUser->save($event_user);
				}
			endforeach;
		}else{
			if($this->params['data']['Group']['name'] == '' || $this->params['data']['Group']['name'] == 'Name this group') $this->params['data']['Group']['name'] = $this->params['data']['Event']['name'].' - '.$this->params['data']['Event']['when'];
			$this->Group->save($this->params['data']['Group']);
			$group_id = $this->Group->getLastInsertId();
			$list = $this->params['data']['Group']['list'].','.$this->params['data']['User']['email'];
			$list = str_replace(array(chr(13),' '),array(',',''),$list);
			$list = explode(',',$list);
			$list = array_unique($list);
			foreach($list as $email):
				$email = trim($email);
				if(preg_match('/^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/',$email))
				{
					$unsubscribed = 0;
					$user_exist = $this->User->find('User.email = "'.$email.'"');
					if(isset($user_exist['User']['id']))
					{
						$user_id = $user_exist['User']['id'];
						$unsubscribed = $user_exist['User']['unsubscribed'];
					}else{
						$this->User->set(array('id'=>NULL));
						$user['User']['id'] = NULL;
						$user['User']['email'] = $email;
						$this->User->save($user);
						$user_id = $this->User->getLastInsertId();
					}
					if($email == $this->params['data']['User']['email']) $this->params['data']['User']['id'] = $user_id;
					if($unsubscribed != 1)
					{
						$this->GroupsUser->set(array('id'=>NULL));
						$group_user['GroupsUser']['id'] = NULL;
						$group_user['GroupsUser']['group_id'] = $group_id;
						$group_user['GroupsUser']['user_id'] = $user_id;
						$this->GroupsUser->save($group_user);
						
						$this->EventsUser->set(array('id'=>NULL));
						$event_user['EventsUser']['id'] = NULL;
						$event_user['EventsUser']['event_id'] = $event_id;
						$event_user['EventsUser']['user_id'] = $user_id;
						$event_user['EventsUser']['hash'] = $this->generate_hash();
						$this->EventsUser->save($event_user);
					}
				}
			endforeach;
		}
		$event['Event']['id'] = $event_id;
		$event['Event']['group_id'] = $group_id;
		$event['Event']['user_id'] = $this->params['data']['User']['id'];
		$event['Event']['hash'] = $this->generate_hash();
		$event['Event']['url'] = $current_url;
		$this->Event->save($event);
		$activation_link = 'http://'.$this->environment.'.dowehaveenough.com/activate_event/'.$event['Event']['hash'];
		
		$email_to = $this->params['data']['User']['email'];
		$email_from = "events@dowehaveenough.com";
		$email_subject = "Validation required to setup event";
		$email_msg = "Your event is almost ready to roll out, we just need you to click this validation link to get those invites rolling.
		
		".$activation_link;
		//$this->Postmark->to = '<'.$email_to.'>';
		//$this->Postmark->subject = $email_subject;
		//$this->Postmark->textMessage = $email_msg;
		//$result = $this->Postmark->send();
		$headers = 'From: events@dowehaveenough.com'. "\r\n" .'Reply-To: events@dowehaveenough.com'. "\r\n" .'X-Mailer: PHP/' . phpversion();
		mail($email_to, $email_subject, $email_msg, $headers);		
	}
	
	function validate_event($hash,$device_hash=null)
	{
		if($device_hash)
		{
			$device = $this->UserMobileDevice->find('UserMobileDevice.validator = "'.$device_hash.'"');
			$device['UserMobileDevice']['validator'] = '';
			$this->UserMobileDevice->save($device);
		}
		$this->User->bindModel(array('hasMany'=>array('UserMobileDevice' =>array('className'=>'UserMobileDevice','foreignKey'=>'user_id','conditions'=>'UserMobileDevice.device_token != "" AND UserMobileDevice.notify_push = 1 AND UserMobileDevice.validator = ""','order'=> '','limit'=> ''))));
		$this->Event->bindModel(array('hasAndBelongsToMany'=>array('User' =>array('className'=>'User','joinTable'=>'events_users','foreignKey'=>'event_id','associationForeignKey'=>'user_id','conditions'=>'','order'=> '','limit'=> '','unique'=>true,'finderQuery'=>'','deleteQuery'=>''))));
		$this->Event->bindModel(array('belongsTo'=>array('Host' =>array('className'=>'User','foreignKey'=>'user_id','conditions'=>'','order'=> '','limit'=> ''))));
		$event = $this->Event->find('Event.hash = "'.$hash.'" AND Event.validated = 0',null,null,4);
		if(isset($event['Event']['id']))
		{
			$event['Event']['validated'] = 1;
			$this->Event->save($event);
			$this->uAuth->set($event['Event']['id'],$event['Event']['user_id']);
			$this->uCookie->write(strtolower(str_replace(' ','-',$event['Event']['name'])).'-'.$event['Event']['id'],array('user_id'=>$event['Event']['user_id']),'+7 Day');
			$this->set('event_link',$event['Event']['url']);
			$host_name = $event['Host']['email'];
			if($event['Host']['name'] != '') $host_name = $event['Host']['name'];
			foreach($event['User'] as $user):
				$email_to = $user['email'];
				$email_from = "events@dowehaveenough.com";
				$email_subject = $event['Event']['name']." - ".$event['Event']['when']." - Do we have enough?";
				$email_msg = $host_name." has sent you an invitation to:
				
".$event['Event']['name']."
 * When: ".$event['Event']['when']."
";
if($event['Event']['where'] != '') $email_msg .= " * Where: ".$event['Event']['where']."
";
$email_msg .= " * We need: ".$event['Event']['need']." people.
";
if($event['Event']['details'] != '') $email_msg .= " * Additional details: ".$event['Event']['details']."
";
$email_msg .= "

ARE YOU IN?
 ** Yes, I'm in! - http://".$this->environment.".dowehaveenough.com/event_status/".$user['EventsUser']['hash']."/1
 ** Nope, I'm out - http://".$this->environment.".dowehaveenough.com/event_status/".$user['EventsUser']['hash']."/2

----

OTHER OPTIONS
 ** I'm 50/50 - http://".$this->environment.".dowehaveenough.com/event_status/".$user['EventsUser']['hash']."/3
";
if($event['Event']['cannot_bring_guests'] == 0) $email_msg .= " ** Yes, I'm in & bringing extra - http://".$this->environment.".dowehaveenough.com/event_status/".$user['EventsUser']['hash']."/4
";

$email_msg .= "
----

Direct Link to Event: http://".$this->environment.".dowehaveenough.com/go_to_event/".$user['EventsUser']['hash']."



----

If you wish to unsubscribe from dowehaveenough.com - http://".$this->environment.".dowehaveenough.com/unsubscribe/".$user['email']."/".$user['EventsUser']['hash'];
				//$this->Postmark->to = '<'.$email_to.'>';
				//$this->Postmark->subject = $email_subject;
				//$this->Postmark->textMessage = $email_msg;
				//$result = $this->Postmark->send();
				$headers = 'From: events@dowehaveenough.com'. "\r\n" .'Reply-To: events@dowehaveenough.com'. "\r\n" .'X-Mailer: PHP/' . phpversion();
				mail($email_to, $email_subject, $email_msg, $headers);	
				$user['latest_event'] = $event['Event']['id'];
				$this->User->save($user);
				foreach($user['UserMobileDevice'] as $device):
					$this->Notification->save_notification($user['id'],$device['device_token'],'You have been invited to '.$event['Event']['name'],$event['Event']['id'],2);
				endforeach;
				if($user['notify_text'] == 1)
				{
					$message = $host_name.' has invited you to '.$event['Event']['name'].' - '.$event['Event']['when'].'. To reply, txt IAMIN, IAMOUT, or IAM50. For status, txt ENOUGH?';
					$this->send_sms($user['id'],$message);
				}
			endforeach;
		}else{
			$this->render('event_not_found');
		}
	}
	
	function validate_user($hash,$status=NULL)
	{
		$this->EventsUser->bindModel(array('belongsTo'=>array('User' =>array('className'=>'User','foreignKey'=>'user_id','conditions'=>'','order'=> '','limit'=> ''),'Event' =>array('className'=>'Event','foreignKey'=>'event_id','conditions'=>'Event.validated = 1','order'=> '','limit'=> ''))));
		$events_user = $this->EventsUser->find('EventsUser.hash = "'.$hash.'"');
		if(isset($events_user['Event']['id']))
		{
			if($status)
			{
				$events_user['EventsUser']['status'] = $status;
				if($status == 4)
				{
					$events_user['EventsUser']['status'] = 1;
					$events_user['EventsUser']['guests'] = 1;
				}
				$events_user['EventsUser']['status_changed'] = date('Y-m-d H:i:s'); 
				$this->EventsUser->save($events_user);
			}
			$this->uAuth->set($events_user['Event']['id'],$events_user['User']['id']);
			$this->uCookie->write(strtolower(str_replace(' ','-',$events_user['Event']['name'])).'-'.$events_user['Event']['id'],array('user_id'=>$events_user['User']['id']),'+7 Day');
			$this->redirect($events_user['Event']['url']);
		}else{
			$this->render('event_not_found');
		}
	}
	
	
	function event()
	{
		$this->set('page','event');
		$this->set('noindex','1');
		$url = $_SERVER['REQUEST_URI'];
		$this->Event->bindModel(array('hasAndBelongsToMany'=>array('User' =>array('className'=>'User','joinTable'=>'events_users','foreignKey'=>'event_id','associationForeignKey'=>'user_id','conditions'=>'','order'=> '','limit'=> '','unique'=>true,'finderQuery'=>'','deleteQuery'=>''))));
		$event = $this->Event->find('Event.url = "'.$url.'" AND Event.validated = 1');
		if(isset($event['Event']['id']))
		{
			$access = 0;
			if($this->uAuth->event_id == $event['Event']['id'])
			{
				$access = 1;
			}else{
				$cookie = $this->uCookie->read(strtolower(str_replace(' ','-',$event['Event']['name'])).'-'.$event['Event']['id']);
				if($cookie['user_id'] > 0)
				{
					$this->uAuth->set($event['Event']['id'],$cookie['user_id']);
					$access = 1;
				}
			}
			if($access == 0)
			{
				$this->render('no_access');
				
			}else{
				$this->set('page_title',$event['Event']['name'].' - '.$event['Event']['when']);
				$this->set('event',$event);
				$this->User->bindModel(array('hasOne'=>array('EventsUser' =>array('className'=>'EventsUser','foreignKey'=>'user_id','conditions'=>'EventsUser.event_id = '.$event['Event']['id'],'order'=> '','limit'=> ''))));
				$the_user = $this->User->find('User.id = '.$this->uAuth->user_id);
				$this->set('the_user',$the_user);
				$this->data = $the_user;
			}
		}else{
			$this->render('event_not_found');
		}
	}
	
	function change_status($status)
	{
		
		$this->EventsUser->bindModel(array('belongsTo'=>array('Event' =>array('className'=>'Event','foreignKey'=>'event_id','conditions'=>'','order'=> '','limit'=> ''))));
		$events_user = $this->EventsUser->find('EventsUser.event_id = '.$this->uAuth->event_id.' AND EventsUser.user_id = '.$this->uAuth->user_id);
		$events_user['EventsUser']['status'] = $status;
		$events_user['EventsUser']['status_changed'] = date('Y-m-d H:i:s'); 
		if($status == 4)
		{
			$events_user['EventsUser']['status'] = 1;
			$events_user['EventsUser']['guests'] = 1;
		}else{
			$events_user['EventsUser']['guests'] = 0;
		}
		$this->EventsUser->save($events_user);
		$this->Event->bindModel(array('hasAndBelongsToMany'=>array('User' =>array('className'=>'User','joinTable'=>'events_users','foreignKey'=>'event_id','associationForeignKey'=>'user_id','conditions'=>'','order'=> '','limit'=> '','unique'=>true,'finderQuery'=>'','deleteQuery'=>''))));
		$event = $this->Event->find('Event.url = "'.$events_user['Event']['url'].'" AND Event.validated = 1');
		$access = 0;
		if($this->uAuth->event_id == $event['Event']['id'])
		{
		}else{
			$cookie = $this->uCookie->read(strtolower(str_replace(' ','-',$event['Event']['name'])).'-'.$event['Event']['id']);
			if($cookie['user_id'] > 0)
			{
				$this->uAuth->set($event['Event']['id'],$cookie['user_id']);
			}
		}
		$this->set('event',$event);
		$this->User->bindModel(array('hasOne'=>array('EventsUser' =>array('className'=>'EventsUser','foreignKey'=>'user_id','conditions'=>'EventsUser.event_id = '.$event['Event']['id'],'order'=> '','limit'=> ''))));
		$the_user = $this->User->find('User.id = '.$this->uAuth->user_id);
		$this->set('the_user',$the_user);
		$this->data = $the_user;
		$this->render('change_status','ajax');
	}
	function change_guests($increase)
	{
		
		$this->EventsUser->bindModel(array('belongsTo'=>array('Event' =>array('className'=>'Event','foreignKey'=>'event_id','conditions'=>'','order'=> '','limit'=> ''))));
		$events_user = $this->EventsUser->find('EventsUser.event_id = '.$this->uAuth->event_id.' AND EventsUser.user_id = '.$this->uAuth->user_id);
		if($increase == 1)
		{
			$events_user['EventsUser']['guests'] = $events_user['EventsUser']['guests'] + 1;
		}else{
			$events_user['EventsUser']['guests'] = $events_user['EventsUser']['guests'] - 1;
		}
		$this->EventsUser->save($events_user);
		$this->Event->bindModel(array('hasAndBelongsToMany'=>array('User' =>array('className'=>'User','joinTable'=>'events_users','foreignKey'=>'event_id','associationForeignKey'=>'user_id','conditions'=>'','order'=> '','limit'=> '','unique'=>true,'finderQuery'=>'','deleteQuery'=>''))));
		$event = $this->Event->find('Event.url = "'.$events_user['Event']['url'].'" AND Event.validated = 1');
		$access = 0;
		if($this->uAuth->event_id == $event['Event']['id'])
		{
		}else{
			$cookie = $this->uCookie->read(strtolower(str_replace(' ','-',$event['Event']['name'])).'-'.$event['Event']['id']);
			if($cookie['user_id'] > 0)
			{
				$this->uAuth->set($event['Event']['id'],$cookie['user_id']);
			}
		}
		$this->set('event',$event);
		$this->User->bindModel(array('hasOne'=>array('EventsUser' =>array('className'=>'EventsUser','foreignKey'=>'user_id','conditions'=>'EventsUser.event_id = '.$event['Event']['id'],'order'=> '','limit'=> ''))));
		$the_user = $this->User->find('User.id = '.$this->uAuth->user_id);
		$this->set('the_user',$the_user);
		$this->data = $the_user;
		$this->render('change_status','ajax');
	}	
	function current_events()
	{
		$this->set('page_title','My Events');
		$this->set('meta_description','Do You Have Enough? Do We Have Enough? Need to find out if you have enough players for a ball gameÉ Enough seats to fill a poker tableÉ Enough participants to hold today\'s meeting?');
		if(isset($this->params['data']['User']['email']))
		{
			$this->User->bindModel(array('hasAndBelongsToMany'=>array('Event' =>array('className'=>'Event','joinTable'=>'events_users','foreignKey'=>'user_id','associationForeignKey'=>'event_id','conditions'=>'Event.validated = 1','order'=> 'Event.created DESC','limit'=> '','unique'=>true,'finderQuery'=>'','deleteQuery'=>''))));
			$user = $this->User->find("User.email = '".$this->params['data']['User']['email']."'");
			if(isset($user['User']['id']))
			{
				$email_to = $user['User']['email'];
				$email_from = "events@dowehaveenough.com";
				$email_subject = "A list of all of your events.";
				$email_msg = "Here is the list of all of your events that you requested.


	";
				foreach($user['Event'] as $event):
						$email_msg .= $event['name'].": http://".$this->environment.".dowehaveenough.com/go_to_event/".$event['EventsUser']['hash']."
	";
				endforeach;
				$email_msg .= "


----

If you wish to unsubscribe from dowehaveenough.com - http://".$this->environment.".dowehaveenough.com/unsubscribe/".$user['User']['email'];
				//$this->Postmark->to = '<'.$email_to.'>';
				//$this->Postmark->subject = $email_subject;
				//$this->Postmark->textMessage = $email_msg;
				//$result = $this->Postmark->send();
				$headers = 'From: events@dowehaveenough.com'. "\r\n" .'Reply-To: events@dowehaveenough.com'. "\r\n" .'X-Mailer: PHP/' . phpversion();
				mail($email_to, $email_subject, $email_msg, $headers);	
				$this->set('message','An email has been sent. Thank you.');
				$this->render('current_events_sent');
			}else{
				$this->set('message','No one was found in any of the events with that email address.');
				$this->render('current_events_sent');
			}
		}
	}
	function my_events()
	{
		$this->User->bindModel(array('hasAndBelongsToMany'=>array('Event' =>array('className'=>'Event','joinTable'=>'events_users','foreignKey'=>'user_id','associationForeignKey'=>'event_id','conditions'=>'Event.validated = 1','order'=> 'Event.created DESC','limit'=> '','unique'=>true,'finderQuery'=>'','deleteQuery'=>''))));
		$this->set('data',$this->User->find("User.id = ".$this->uAuth->user_id));
	}
	
	function update_name($event_id)
	{
		$this->User->save($this->params['data']);
		$this->Event->bindModel(array('hasAndBelongsToMany'=>array('User' =>array('className'=>'User','joinTable'=>'events_users','foreignKey'=>'event_id','associationForeignKey'=>'user_id','conditions'=>'','order'=> '','limit'=> '','unique'=>true,'finderQuery'=>'','deleteQuery'=>''))));
		$event = $this->Event->find('Event.id = "'.$event_id.'" AND Event.validated = 1');
		$access = 0;
		if($this->uAuth->event_id == $event['Event']['id'])
		{
		}else{
			$cookie = $this->uCookie->read(strtolower(str_replace(' ','-',$event['Event']['name'])).'-'.$event['Event']['id']);
			if($cookie['user_id'] > 0)
			{
				$this->uAuth->set($event['Event']['id'],$cookie['user_id']);
			}
		}
		$this->set('event',$event);
		$this->User->bindModel(array('hasOne'=>array('EventsUser' =>array('className'=>'EventsUser','foreignKey'=>'user_id','conditions'=>'EventsUser.event_id = '.$event['Event']['id'],'order'=> '','limit'=> ''))));
		$the_user = $this->User->find('User.id = '.$this->uAuth->user_id);
		$this->set('the_user',$the_user);
		$this->data = $the_user;
		$this->render('change_status','ajax');
	}
	
	function update_settings($event_id)
	{
		if($this->params['data']['User']['cell_number'] == 'Enter Cell Number' && isset($this->params['data']['User']['cell_number'])) $this->params['data']['User']['cell_number'] = '';
		$this->User->save($this->params['data']['User']);
		$this->EventsUser->save($this->params['data']['EventsUser']);
		$this->render('update_settings','ajax');
	}
	
	function invite($event_id,$group_id)
	{
		$message = array();
		$this->set('event_id',$event_id);
		$this->User->bindModel(array('hasMany'=>array('UserMobileDevice' =>array('className'=>'UserMobileDevice','foreignKey'=>'user_id','conditions'=>'UserMobileDevice.device_token != "" AND UserMobileDevice.notify_push = 1 AND UserMobileDevice.validator = ""','order'=> '','limit'=> ''))));
		$user_exist = $this->User->find('User.email = "'.$this->params['data']['NewUser']['email'].'"');
		$unsubscribed = 0;
		if(isset($user_exist['User']['id']))
		{
			if($user_exist['User']['unsubscribed'] == 1)
			{
				$unsubscribed = 1;
			}else{
				$user['User']['id'] = $user_exist['User']['id'];
				$user['User']['notify_text'] = $user_exist['User']['notify_text'];
				$user['User']['email'] = $user_exist['User']['email'];
				$user['UserMobileDevice'] = $user_exist['UserMobileDevice'];
				$user_id = $user_exist['User']['id'];
			}
		}else{
			$this->User->set(array('id'=>NULL));
			$user['User']['id'] = NULL;
			$user['User']['email'] = $this->params['data']['NewUser']['email'];
			$this->User->save($user);
			$user_id = $this->User->getLastInsertId();
		}
		if($unsubscribed == 0)
		{
			if($this->params['data']['NewUser']['to_group'] == 1)
			{
				$in_group = $this->GroupsUser->find('GroupsUser.user_id = '.$user_id.' AND GroupsUser.group_id = '.$group_id);
				if(isset($in_group['GroupsUser']['id']))
				{
					if($in_group['GroupsUser']['unsubscribed'] == 1)
					{
						$message['unsubscribed_from_group'] = 1;
					}else{
						$message['already_in_group'] = 1;
					}
				}else{
					$this->GroupsUser->set(array('id'=>NULL));
					$group_user['GroupsUser']['id'] = NULL;
					$group_user['GroupsUser']['group_id'] = $group_id;
					$group_user['GroupsUser']['user_id'] = $user_id;
					$this->GroupsUser->save($group_user);
					$message['added_to_group'] = 1;
				}
			}
			$in_event = $this->EventsUser->find('EventsUser.user_id = '.$user_id.' AND EventsUser.event_id = '.$event_id);
			if(isset($in_event['EventsUser']['id']))
			{
				$message['already_in_event'] = 1;
			}else{
				$this->EventsUser->set(array('id'=>NULL));
				$event_user['EventsUser']['id'] = NULL;
				$event_user['EventsUser']['event_id'] = $event_id;
				$event_user['EventsUser']['user_id'] = $user_id;
				$event_user['EventsUser']['hash'] = $this->generate_hash();
				$this->EventsUser->save($event_user);
				$message['added_to_event'] = 1;
				$host = $this->User->find('User.id = '.$this->uAuth->user_id);
				$event = $this->Event->find('Event.id = '.$event_id);
				$host_name = $host['User']['email'];
				if($host['User']['name'] != '') $host_name = $host['User']['name'];
				$email_to = $this->params['data']['NewUser']['email'];
				$email_from = "events@dowehaveenough.com";
				$email_subject = $event['Event']['name']." - ".$event['Event']['when']." - Do we have enough?";
				$email_msg = $host_name." has sent you an invitation to:
				
".$event['Event']['name']."
 * When: ".$event['Event']['when']."
";
if($event['Event']['where'] != '') $email_msg .= " * Where: ".$event['Event']['where']."
";
$email_msg .= " * We need: ".$event['Event']['need']." people.

ARE YOU IN?
 ** Yes, I'm in! - http://".$this->environment.".dowehaveenough.com/event_status/".$event_user['EventsUser']['hash']."/1
 ** Nope, I'm out - http://".$this->environment.".dowehaveenough.com/event_status/".$event_user['EventsUser']['hash']."/2

----

OTHER OPTIONS
 ** I'm 50/50 - http://".$this->environment.".dowehaveenough.com/event_status/".$event_user['EventsUser']['hash']."/3
";
if($event['Event']['cannot_bring_guests'] == 0) $email_msg .= " ** Yes, I'm in & bringing extra - http://".$this->environment.".dowehaveenough.com/event_status/".$event_user['EventsUser']['hash']."/4
";

$email_msg .= "
----

Direct Link to Event: http://".$this->environment.".dowehaveenough.com/go_to_event/".$event_user['EventsUser']['hash']."



----

If you wish to unsubscribe from dowehaveenough.com - http://".$this->environment.".dowehaveenough.com/unsubscribe/".$user['User']['email']."/".$event_user['EventsUser']['hash'];
				//$this->Postmark->to = '<'.$email_to.'>';
				//$this->Postmark->subject = $email_subject;
				//$this->Postmark->textMessage = $email_msg;
				//$result = $this->Postmark->send();
				$headers = 'From: events@dowehaveenough.com'. "\r\n" .'Reply-To: events@dowehaveenough.com'. "\r\n" .'X-Mailer: PHP/' . phpversion();
				mail($email_to, $email_subject, $email_msg, $headers);	
				$user['User']['id'] = $user_id;
				$user['User']['latest_event'] = $event['Event']['id'];
				$this->User->save($user);
				foreach($user['UserMobileDevice'] as $device):
					$this->Notification->save_notification($user_id,$device['device_token'],'You have been invited to '.$event['Event']['name'],$event['Event']['id'],2);
				endforeach;
				if($user['User']['notify_text'] == 1)
				{
					$message = $host_name.' has invited you to '.$event['Event']['name'].' - '.$event['Event']['when'].'.
Reply with IAMIN, IAMOUT, IAM50, or ENOUGH? to find out the status of the event.';
					$this->send_sms($user['User']['id'],$message);
				}
			}
		}else{
			$message['unsubscribed'] = 1;
		}
		$this->set('message',$message);
		$this->render('invite','ajax');
	}
	function new_invite($event_id)
	{
		$this->set('event',$this->Event->find('Event.id = '.$event_id));
		$this->render('new_invite','ajax');
	}
	function unsubscribe($email=null,$hash=null)
	{
		if($hash)
		{
			$this->EventsUser->bindModel(array('belongsTo'=>array('Event' =>array('className'=>'Event','foreignKey'=>'event_id','conditions'=>'','order'=> '','limit'=> ''))));
			$event = $this->EventsUser->find('EventsUser.hash = "'.$hash.'"');
			if(isset($event['EventsUser']['id'])) $this->set('event',$event);
		}
		$this->set('user',$this->User->find('User.email = "'.$email.'"'));
	}
	
	function unsubscribe_from_dwhe($email)
	{
		
		$this->User->bindModel(array('hasMany'=>array('EventsUser' =>array('className'=>'EventsUser','foreignKey'=>'user_id','conditions'=>'','order'=> '','limit'=> ''),'GroupsUser' =>array('className'=>'GroupsUser','foreignKey'=>'user_id','conditions'=>'','order'=> '','limit'=> ''))));
		$user = $this->User->find('User.email = "'.$email.'"');
		$user['User']['unsubscribed'] = 1;
		$this->User->save($user);
		foreach($user['EventsUser'] as $event):
			$this->EventsUser->delete($event['id']);
		endforeach;
		foreach($user['GroupsUser'] as $group):
			$this->GroupsUser->delete($group['id']);
		endforeach;
	}
	
	function unsubscribe_from_event($hash)
	{
		$event = $this->EventsUser->find('EventsUser.hash = "'.$hash.'"');
		$event['EventsUser']['unsubscribed'] = 1;
		$this->EventsUser->save($event);
	}
	function unsubscribe_from_group($hash,$group_id,$user_id)
	{
		$event = $this->EventsUser->find('EventsUser.hash = "'.$hash.'"');
		$event['EventsUser']['unsubscribed'] = 1;
		$this->EventsUser->save($event);
		$group_user = $this->GroupsUser->find('GroupsUser.user_id = '.$user_id.' AND GroupsUser.group_id = '.$group_id);
		$group_user['GroupsUser']['unsubscribed'] = 1;
		$this->GroupsUser->save($group_user);
	}
	
	//Notification Functionality
	
	function notify()
	{
		$this->User->bindModel(array('hasMany'=>array('UserMobileDevice' =>array('className'=>'UserMobileDevice','foreignKey'=>'user_id','conditions'=>'UserMobileDevice.device_token != "" AND UserMobileDevice.notify_push = 1 AND UserMobileDevice.validator = ""','order'=> '','limit'=> ''))));
		$this->Event->bindModel(array('hasAndBelongsToMany'=>array('User' =>array('className'=>'User','joinTable'=>'events_users','foreignKey'=>'event_id','associationForeignKey'=>'user_id','conditions'=>'','order'=> '','limit'=> '','unique'=>true,'finderQuery'=>'','deleteQuery'=>''))));
		$events = $this->Event->findAll('Event.validated = 1 AND Event.active > 0 AND Event.created > (NOW() - INTERVAL 3 MONTH)',null,null,null,null,4);
		foreach($events as $event):
			$in = 0;
			$out = 0;
			$fifty = 0;
			$user_in_recent = array();
			$user_out_recent = array();
			foreach($event['User'] as $user):
				if($user['EventsUser']['status'] == 1) $in = $in + 1 + $user['EventsUser']['guests'];
				if($user['EventsUser']['status'] == 2) $out++;
				if($user['EventsUser']['status'] == 3) $fifty++;
				if(date('Y-m-d H:i:s',mktime(date('G'),date('i')-5,date('s'),date('n'),date('j'),date('Y'))) < $user['EventsUser']['status_changed'] && $user['EventsUser']['status'] == 1) $user_in_recent[] = $user;
				if(date('Y-m-d H:i:s',mktime(date('G'),date('i')-5,date('s'),date('n'),date('j'),date('Y'))) < $user['EventsUser']['status_changed'] && $user['EventsUser']['status'] == 2) $user_out_recent[] = $user;
			endforeach;
			if($in >= $event['Event']['need'] && $event['Event']['active'] < 3)
			{
				//notify we are on
				$email_from = "events@dowehaveenough.com";
				$email_subject = $event['Event']['name'].' is ON!';
				$email_headers = "From: ".$email_from;
				foreach($event['User'] as $user):
					if($user['EventsUser']['unsubscribed'] == 0)
					{
						$email_msg = "With ".$in." people in so far ".$event['Event']['name']." is ON!
	
	----
	
	Direct Link to Event: http://".$this->environment.".dowehaveenough.com/go_to_event/".$user['EventsUser']['hash']."
	
	
	
	----
	
	If you wish to unsubscribe from dowehaveenough.com - http://".$this->environment.".dowehaveenough.com/unsubscribe/".$user['email']."/".$user['EventsUser']['hash'];
						$email_to = $user['email'];
						//$this->Postmark->to = '<'.$email_to.'>';
						//$this->Postmark->subject = $email_subject;
						//$this->Postmark->textMessage = $email_msg;
						//$result = $this->Postmark->send();
						$headers = 'From: events@dowehaveenough.com'. "\r\n" .'Reply-To: events@dowehaveenough.com'. "\r\n" .'X-Mailer: PHP/' . phpversion();
						mail($email_to, $email_subject, $email_msg, $headers);
						foreach($user['UserMobileDevice'] as $device):
							$this->Notification->save_notification($user['id'],$device['device_token'],$event['Event']['name'].' is on!',$event['Event']['id'],3);
						endforeach;
						if($user['notify_text'] == 1)
						{
							$message = ' '.$event['Event']['name'].' is on!
We have '.$in.' in,
'.$out.' out,
and '.$fifty.' 50/50.';
							$this->send_sms($user['id'],$message);
						}
					}
				endforeach;	
				$event['Event']['active'] = 3;
				$this->Event->save($event['Event']);
			}elseif($event['Event']['cancel_email'] != '0000-00-00 00:00:00' && $event['Event']['cancel_email'] < date('Y-m-d H:i:s') && $event['Event']['active'] < 3)
			{
				//notify we are off
				$email_from = "events@dowehaveenough.com";
				$email_subject = $event['Event']['name'].' is OFF';
				$email_headers = "From: ".$email_from;
				foreach($event['User'] as $user):
					if($user['EventsUser']['unsubscribed'] == 0)
					{
						$email_msg = "We didn't get enough people in so sadly ".$event['Event']['name']." is OFF.
						
	----
	
	Direct Link to Event: http://".$this->environment.".dowehaveenough.com/go_to_event/".$user['EventsUser']['hash']."
	
	----
	
	Create a new event at http://".$this->environment.".dowehaveenough.com/create
	
	
	
	----
	
	If you wish to unsubscribe from dowehaveenough.com - http://".$this->environment.".dowehaveenough.com/unsubscribe/".$user['email'];
						$email_to = $user['email'];
						//$this->Postmark->to = '<'.$email_to.'>';
						//$this->Postmark->subject = $email_subject;
						//$this->Postmark->textMessage = $email_msg;
						//$result = $this->Postmark->send();
						$headers = 'From: events@dowehaveenough.com'. "\r\n" .'Reply-To: events@dowehaveenough.com'. "\r\n" .'X-Mailer: PHP/' . phpversion();
						mail($email_to, $email_subject, $email_msg, $headers);
						foreach($user['UserMobileDevice'] as $device):
							$this->Notification->save_notification($user['id'],$device['device_token'],$event['Event']['name'].' is off.',$event['Event']['id'],3);
						endforeach;
						if($user['notify_text'] == 1)
						{
							$message = ' '.$event['Event']['name'].' is off.
We had '.$in.' in,
'.$out.' out,
and '.$fifty.' 50/50.';
							$this->send_sms($user['id'],$message);
						}
					}
				endforeach;	
			
				$event['Event']['active'] = 0;
				$this->Event->save($event['Event']);
			}elseif($event['Event']['status_email'] != '0000-00-00 00:00:00' && $event['Event']['status_email'] < date('Y-m-d H:i:s') && $event['Event']['active'] < 2)
			{
				//notify we still need some people!
				$email_from = "events@dowehaveenough.com";
				$email_subject = $event['Event']['name'].' needs more!';
				$email_headers = "From: ".$email_from;
				foreach($event['User'] as $user):
					if($user['EventsUser']['unsubscribed'] == 0)
					{
						$email_msg = "We only have ".$in." in so far and we need ".$event['Event']['need']." for ".$event['Event']['name']." to be on.
					
	----
	
	Direct Link to Event: http://".$this->environment.".dowehaveenough.com/go_to_event/".$user['EventsUser']['hash']."
	
	
	
	
	----
	
	If you wish to unsubscribe from dowehaveenough.com - http://".$this->environment.".dowehaveenough.com/unsubscribe/".$user['email']."/".$user['EventsUser']['hash'];
						$email_to = $user['email'];
						//$this->Postmark->to = '<'.$email_to.'>';
						//$this->Postmark->subject = $email_subject;
						//$this->Postmark->textMessage = $email_msg;
						//$result = $this->Postmark->send();
						$headers = 'From: events@dowehaveenough.com'. "\r\n" .'Reply-To: events@dowehaveenough.com'. "\r\n" .'X-Mailer: PHP/' . phpversion();
						mail($email_to, $email_subject, $email_msg, $headers);	
						foreach($user['UserMobileDevice'] as $device):
							$this->Notification->save_notification($user['id'],$device['device_token'],$event['Event']['name'].' needs more!.',$event['Event']['id'],3);
						endforeach;
						if($user['notify_text'] == 1)
						{
							$message = ' '.$event['Event']['name'].' needs more! We have '.$in.' in, '.$out.' out, and '.$fifty.' 50/50. Still need at least '.$event['Event']['need'].'!';
							$this->send_sms($user['id'],$message);
						}
					}
				endforeach;	
				
				$event['Event']['active'] = 2;
				$this->Event->save($event['Event']);
			}
			foreach($event['User'] as $user):
				if($user['EventsUser']['unsubscribed'] == 0)
				{
					if($user['EventsUser']['notify_reach_checked'] == 1 && $user['EventsUser']['notify_reach_count'] <= $in && $user['EventsUser']['notified_reached'] != 1)
					{
						//notify user we have X amount
						$email_from = "events@dowehaveenough.com";
						$email_subject = $event['Event']['name'].' has '.$in.' in!';
						$email_headers = "From: ".$email_from;
						$email_msg = "You wanted to be notified when ".$event['Event']['name']." has at least ".$user['EventsUser']['notify_reach_count']." in and guess what? It does!
					
	----
	
	Direct Link to Event: http://".$this->environment.".dowehaveenough.com/go_to_event/".$user['EventsUser']['hash']."
	
	
	
	
	----
	
	If you wish to unsubscribe from dowehaveenough.com - http://".$this->environment.".dowehaveenough.com/unsubscribe/".$user['email']."/".$user['EventsUser']['hash'];
						$email_to = $user['email'];
						//$this->Postmark->to = '<'.$email_to.'>';
						//$this->Postmark->subject = $email_subject;
						//$this->Postmark->textMessage = $email_msg;
						//$result = $this->Postmark->send();
						$headers = 'From: events@dowehaveenough.com'. "\r\n" .'Reply-To: events@dowehaveenough.com'. "\r\n" .'X-Mailer: PHP/' . phpversion();
						mail($email_to, $email_subject, $email_msg, $headers);
						foreach($user['UserMobileDevice'] as $device):
							$this->Notification->save_notification($user['id'],$device['device_token'],$event['Event']['name'].' now has '.$in.' in.',$event['Event']['id'],3);
						endforeach;
						if($user['notify_text'] == 1)
						{
							$message = ' '.$event['Event']['name'].' now has '.$in.' in.';
							$this->send_sms($user['id'],$message);
						}
						$user['EventsUser']['notified_reached'] = 1;
						$this->EventsUser->save($user['EventsUser']);
					}
					if($user['notify_in'] == 1)
					{
						$people_are_in = 0;
						foreach($user_in_recent as $ins):
							if($ins['id'] != $user['id'])
							{
								$people_are_in = 1;
							}
						endforeach;
						if($people_are_in > 0)
						{
							
							//notify who has joined in
							$email_from = "events@dowehaveenough.com";
							$email_subject = 'People are IN for '.$event['Event']['name'];
							$email_headers = "From: ".$email_from;
							$email_msg = "The following people are IN for ".$event['Event']['name'].":
	";
							foreach($user_in_recent as $ins):
								if($ins['id'] != $user['id'])
								{
									if($ins['name'] != '')
									{
										$email_msg .= "
	".$ins['name'];
									}else{
										$email_msg .= "
	".$ins['email'];
									}
								}
							endforeach;
							$email_msg .= "
	
	----
	
	Direct Link to Event: http://".$this->environment.".dowehaveenough.com/go_to_event/".$user['EventsUser']['hash']."
	
	
	
	
	----
	
	If you wish to unsubscribe from dowehaveenough.com - http://".$this->environment.".dowehaveenough.com/unsubscribe/".$user['email']."/".$user['EventsUser']['hash'];
							$email_to = $user['email'];
							//$this->Postmark->to = '<'.$email_to.'>';
							//$this->Postmark->subject = $email_subject;
							//$this->Postmark->textMessage = $email_msg;
							//$result = $this->Postmark->send();
							$headers = 'From: events@dowehaveenough.com'. "\r\n" .'Reply-To: events@dowehaveenough.com'. "\r\n" .'X-Mailer: PHP/' . phpversion();
							mail($email_to, $email_subject, $email_msg, $headers);
							foreach($user['UserMobileDevice'] as $device):
								$this->Notification->save_notification($user['id'],$device['device_token'],'People are in for '.$event['Event']['name'],$event['Event']['id'],1);
							endforeach;
							if($user['notify_text'] == 1)
							{
								$message = '';
								$i=0;
								foreach($user_in_recent as $ins):
									if($ins['id'] != $user['id'])
									{
										if($i > 0) $message .= ', ';
										if($ins['name'] != '')
										{
											$message .= $ins['name'];
										}else{
											$message .= $ins['email'];
										}
										$i++;
									}
								endforeach;
								if($i > 1)
								{
									$message .= ' are in for '.$event['Event']['name'].'.';
								}else{
									$message .= ' is in for '.$event['Event']['name'].'.';
								}
								$this->send_sms($user['id'],$message);
							}
						}
					}
					if($user['notify_out'] == 1)
					{
						$people_are_out = 0;
						foreach($user_out_recent as $outs):
							if($outs['id'] != $user['id'])
							{
								$people_are_out = 1;
							}
						endforeach;
						if($people_are_out > 0)
						{
							//notify who has joouted out
							$email_from = "events@dowehaveenough.com";
							$email_subject = 'People are OUT for '.$event['Event']['name'];
							$email_headers = "From: ".$email_from;
							$email_msg = "The following people are OUT for ".$event['Event']['name'].":
	";
							foreach($user_out_recent as $outs):
								if($outs['id'] != $user['id'])
								{
									if($outs['name'] != '')
									{
										$email_msg .= "
	".$outs['name'];
									}else{
										$email_msg .= "
	".$outs['email'];
									}
								}
							endforeach;
							$email_msg .= "
	
	----
	
	Direct Link to Event: http://".$this->environment.".dowehaveenough.com/go_to_event/".$user['EventsUser']['hash']."
	
	
	
	
	----
	
	If you wish to unsubscribe from dowehaveenough.com - http://".$this->environment.".dowehaveenough.com/unsubscribe/".$user['email']."/".$user['EventsUser']['hash'];
							$email_to = $user['email'];
							//$this->Postmark->to = '<'.$email_to.'>';
							//$this->Postmark->subject = $email_subject;
							//$this->Postmark->textMessage = $email_msg;
							//$result = $this->Postmark->send();
							$headers = 'From: events@dowehaveenough.com'. "\r\n" .'Reply-To: events@dowehaveenough.com'. "\r\n" .'X-Mailer: PHP/' . phpversion();
							mail($email_to, $email_subject, $email_msg, $headers);
							foreach($user['UserMobileDevice'] as $device):
								$this->Notification->save_notification($user['id'],$device['device_token'],'People are out for '.$event['Event']['name'],$event['Event']['id'],1);
							endforeach;
							if($user['notify_text'] == 1)
							{
								$message = '';
								$i=0;
								foreach($user_out_recent as $outs):
									if($outs['id'] != $user['id'])
									{
										if($i > 0) $message .= ', ';
										if($outs['name'] != '')
										{
											$message .= $outs['name'];
										}else{
											$message .= $outs['email'];
										}
										$i++;
									}
								endforeach;
								if($i > 1)
								{
									$message .= ' are out for '.$event['Event']['name'].'.';
								}else{
									$message .= ' is out for '.$event['Event']['name'].'.';
								}
								$this->send_sms($user['id'],$message);
							}
						}
					}
				}
			endforeach;
		endforeach;
		header('HTTP/1.1 200 OK');
		exit();
	}
			
	
	
	//Begin SMS Functionality
	function subscribe_sms()
	{
		if($this->uAuth->user_id > 0)
		{
			$this->layout = 'none';
			$this->set('user_id',$this->uAuth->user_id);
		}else{
			$this->uAuth->set();
			$this->layout = 'none';
			$this->set('user_id',$this->uAuth->user_id);
		}
	}
	function send_sms($uid,$msg)
	{
		$api_url = 'https://api.zeepmobile.com/messaging/2008-07-14/send_message';
		$api_key = '8d6868f9-c88e-4210-9296-14d32ba84ea3';
		$secret_access_key = 'c1f4e8220ec5d39f3f146c868c0ad2e2427f96c0';
		$http_date = gmdate(DATE_RFC822);
		$content = "user_id=$uid&body=" . urlencode($msg);
		$b64_mac = base64_encode(hash_hmac("sha1",$api_key.$http_date .$content,$secret_access_key, TRUE));
		$authentication = "Zeep " . $api_key . ":$b64_mac";
		
		$opts = array(
			'http' => array(
			  'method' => 'POST',
			  'header' =>
				"Authorization: ".$authentication."\r\n" .
				"Date: ".$http_date."\r\n" .
				"Content-Type: application/x-www-form-urlencoded\r\n" .
				"Content-Length: " . strval(strlen($content)) . "\r\n",
			  'content' => $content
			)
		);
		
		$context = stream_context_create($opts);
		$sock = fopen($api_url, 'r', false, $context);
		$result = '';
		if ($sock) {
			while (!feof($sock)) {
			  $result .= fgets($sock, 4096);
			}
			fclose($sock);
		}
		return trim($result);
	}
	function receive_sms()
	{
		header('HTTP/1.1 200 OK');
		header('Content-type: text/plain');
		$sms['Sms']['event'] = $_POST['event'];
		$sms['Sms']['uid'] = $_POST['uid'];
		$sms['Sms']['min'] = $_POST['min'];
		if($sms['Sms']['uid'] == '[your authenticated user]')
		{
			$sms['Sms']['user_id'] = 11;
			$user['User']['id'] = 11;
		}else{
			$uid = $_POST['uid'];
			$this->User->bindModel(array('hasOne'=>array('EventsUser' =>array('className'=>'EventsUser','foreignKey'=>'user_id','conditions'=>'EventsUser.event_id = User.latest_event','order'=> '','limit'=> ''))));
			$this->User->bindModel(array('belongsTo'=>array('Event' =>array('className'=>'Event','foreignKey'=>'latest_event','conditions'=>'','order'=> '','limit'=> ''))));
			if($uid > 0)
			{
				$user = $this->User->find("User.id = '".$uid."'");
			}else{
				$user = $this->User->find("User.cell_number = '".substr($sms['Sms']['min'],-10,3)."-".substr($sms['Sms']['min'],-7,3)."-".substr($sms['Sms']['min'],-4,4)."'");
			}
			if(!isset($user['User']['id']))
			{
				$string = ' You need to be a part of DWHE to subscribe to it.';
				header('Content-length: '.strlen($string));
				echo $string;
				$this->Sms->save($sms);
				exit();
			}else{
				$sms['Sms']['user_id'] = $user['User']['id'];
			}
		}
		if($sms['Sms']['event'] == 'MO')
		{
			$sms['Sms']['body'] = $_POST['body'];
			if(trim(strtolower($sms['Sms']['body'])) == 'in')
			{
				$user['EventsUser']['status'] = 1;
				$user['EventsUser']['status_changed'] = date('Y-m-d H:i:s');
				$this->EventsUser->save($user['EventsUser']);
				$string = ' You are now in for '.$user['Event']['name'];
				header('Content-length: '.strlen($string));
				echo $string;
				$this->Sms->save($sms);
				exit();
			}elseif(trim(strtolower($sms['Sms']['body'])) == 'out')
			{
				$user['EventsUser']['status'] = 2;
				$user['EventsUser']['status_changed'] = date('Y-m-d H:i:s');
				$this->EventsUser->save($user['EventsUser']);
				$string = ' You are now out for '.$user['Event']['name'];
				header('Content-length: '.strlen($string));
				echo $string;
				$this->Sms->save($sms);
				exit();
			}else{
				$string = ' Sorry we could not recognize your command.';
				header('Content-length: '.strlen($string));
				echo $string;
				$this->Sms->save($sms);
				exit();
			}
				
		}
		if($sms['Sms']['event'] == 'SUBSCRIPTION_UPDATE')
		{
			$sms['Sms']['min'] = $_POST['min'];
			$user['User']['cell_number'] = substr($sms['Sms']['min'],-10,3).'-'.substr($sms['Sms']['min'],-7,3).'-'.substr($sms['Sms']['min'],-4,4);
			$this->User->save($user);
			$string = ' You are now connected to DWHE. Check out our help page for how to take advantage of texting with DWHE.';
			header('Content-length: '.strlen($string));
			echo $string;
			$this->Sms->save($sms);
			exit();
		}
		$message = explode(' ',$sms['Sms']['body']);
		$string = ' Status Updated';
		header('Content-length: '.strlen($string));
		echo $string;
		$this->Sms->save($sms);
		exit();
	}
	function iamin()
	{
		header('HTTP/1.1 200 OK');
		header('Content-type: text/plain');
		$sms['Sms']['event'] = $_POST['event'];
		$sms['Sms']['uid'] = $_POST['uid'];
		$sms['Sms']['min'] = $_POST['min'];
		if($sms['Sms']['uid'] == '[your authenticated user]')
		{
			$sms['Sms']['user_id'] = 11;
			$user['User']['id'] = 11;
		}else{
			$uid = $_POST['uid'];
			$this->User->bindModel(array('hasOne'=>array('EventsUser' =>array('className'=>'EventsUser','foreignKey'=>'user_id','conditions'=>'EventsUser.event_id = User.latest_event','order'=> '','limit'=> ''))));
			$this->User->bindModel(array('belongsTo'=>array('Event' =>array('className'=>'Event','foreignKey'=>'latest_event','conditions'=>'','order'=> '','limit'=> ''))));
			$user = $this->User->find("User.cell_number = '".substr($sms['Sms']['min'],-10,3)."-".substr($sms['Sms']['min'],-7,3)."-".substr($sms['Sms']['min'],-4,4)."'");
			if(!isset($user['User']['id']))
			{
				$string = ' You need to be a part of DWHE to subscribe to it.';
				header('Content-length: '.strlen($string));
				echo $string;
				$this->Sms->save($sms);
				exit();
			}else{
				$sms['Sms']['user_id'] = $user['User']['id'];
			}
		}
		$sms['Sms']['body'] = $_POST['body'];
		$user['EventsUser']['status'] = 1;
		$user['EventsUser']['status_changed'] = date('Y-m-d H:i:s');
		$this->EventsUser->save($user['EventsUser']);
		$string = ' You are now in for '.$user['Event']['name'].'.';
		if($user['Event']['cannot_bring_guests'] == 0)
		{
			$string .= '
txt PLUS1 to show that you are bringing a guest!';
		}
		header('Content-length: '.strlen($string));
		echo $string;
		$this->Sms->save($sms);
		exit();	
	}
	function iamout()
	{
		header('HTTP/1.1 200 OK');
		header('Content-type: text/plain');
		$sms['Sms']['event'] = $_POST['event'];
		$sms['Sms']['uid'] = $_POST['uid'];
		$sms['Sms']['min'] = $_POST['min'];
		if($sms['Sms']['uid'] == '[your authenticated user]')
		{
			$sms['Sms']['user_id'] = 11;
			$user['User']['id'] = 11;
		}else{
			$uid = $_POST['uid'];
			$this->User->bindModel(array('hasOne'=>array('EventsUser' =>array('className'=>'EventsUser','foreignKey'=>'user_id','conditions'=>'EventsUser.event_id = User.latest_event','order'=> '','limit'=> ''))));
			$this->User->bindModel(array('belongsTo'=>array('Event' =>array('className'=>'Event','foreignKey'=>'latest_event','conditions'=>'','order'=> '','limit'=> ''))));
			$user = $this->User->find("User.cell_number = '".substr($sms['Sms']['min'],-10,3)."-".substr($sms['Sms']['min'],-7,3)."-".substr($sms['Sms']['min'],-4,4)."'");
			if(!isset($user['User']['id']))
			{
				$string = ' You need to be a part of DWHE to subscribe to it.';
				header('Content-length: '.strlen($string));
				echo $string;
				$this->Sms->save($sms);
				exit();
			}else{
				$sms['Sms']['user_id'] = $user['User']['id'];
			}
		}
		$sms['Sms']['body'] = $_POST['body'];
		$user['EventsUser']['status'] = 2;
		$user['EventsUser']['guests'] = 0;
		$user['EventsUser']['status_changed'] = date('Y-m-d H:i:s');
		$this->EventsUser->save($user['EventsUser']);
		$string = ' You are now out for '.$user['Event']['name'];
		header('Content-length: '.strlen($string));
		echo $string;
		$this->Sms->save($sms);
		exit();	
	}
	function iam50()
	{
		header('HTTP/1.1 200 OK');
		header('Content-type: text/plain');
		$sms['Sms']['event'] = $_POST['event'];
		$sms['Sms']['uid'] = $_POST['uid'];
		$sms['Sms']['min'] = $_POST['min'];
		if($sms['Sms']['uid'] == '[your authenticated user]')
		{
			$sms['Sms']['user_id'] = 11;
			$user['User']['id'] = 11;
		}else{
			$uid = $_POST['uid'];
			$this->User->bindModel(array('hasOne'=>array('EventsUser' =>array('className'=>'EventsUser','foreignKey'=>'user_id','conditions'=>'EventsUser.event_id = User.latest_event','order'=> '','limit'=> ''))));
			$this->User->bindModel(array('belongsTo'=>array('Event' =>array('className'=>'Event','foreignKey'=>'latest_event','conditions'=>'','order'=> '','limit'=> ''))));
			$user = $this->User->find("User.cell_number = '".substr($sms['Sms']['min'],-10,3)."-".substr($sms['Sms']['min'],-7,3)."-".substr($sms['Sms']['min'],-4,4)."'");
			if(!isset($user['User']['id']))
			{
				$string = ' You need to be a part of DWHE to subscribe to it.';
				header('Content-length: '.strlen($string));
				echo $string;
				$this->Sms->save($sms);
				exit();
			}else{
				$sms['Sms']['user_id'] = $user['User']['id'];
			}
		}
		$sms['Sms']['body'] = $_POST['body'];
		$user['EventsUser']['status'] = 3;
		$user['EventsUser']['guests'] = 0;
		$user['EventsUser']['status_changed'] = date('Y-m-d H:i:s');
		$this->EventsUser->save($user['EventsUser']);
		$string = ' You are now 50/50 for '.$user['Event']['name'];
		header('Content-length: '.strlen($string));
		echo $string;
		$this->Sms->save($sms);
		exit();	
	}
	function plus1()
	{
		header('HTTP/1.1 200 OK');
		header('Content-type: text/plain');
		$sms['Sms']['event'] = $_POST['event'];
		$sms['Sms']['uid'] = $_POST['uid'];
		$sms['Sms']['min'] = $_POST['min'];
		if($sms['Sms']['uid'] == '[your authenticated user]')
		{
			$sms['Sms']['user_id'] = 11;
			$user['User']['id'] = 11;
		}else{
			$uid = $_POST['uid'];
			$this->User->bindModel(array('hasOne'=>array('EventsUser' =>array('className'=>'EventsUser','foreignKey'=>'user_id','conditions'=>'EventsUser.event_id = User.latest_event','order'=> '','limit'=> ''))));
			$this->User->bindModel(array('belongsTo'=>array('Event' =>array('className'=>'Event','foreignKey'=>'latest_event','conditions'=>'','order'=> '','limit'=> ''))));
			$user = $this->User->find("User.cell_number = '".substr($sms['Sms']['min'],-10,3)."-".substr($sms['Sms']['min'],-7,3)."-".substr($sms['Sms']['min'],-4,4)."'");
			if(!isset($user['User']['id']))
			{
				$string = ' You need to be a part of DWHE to subscribe to it.';
				header('Content-length: '.strlen($string));
				echo $string;
				$this->Sms->save($sms);
				exit();
			}else{
				$sms['Sms']['user_id'] = $user['User']['id'];
			}
		}
		$sms['Sms']['body'] = $_POST['body'];
		$user['EventsUser']['guests'] = $user['EventsUser']['guests'] + 1;
		$user['EventsUser']['status_changed'] = date('Y-m-d H:i:s');
		$this->EventsUser->save($user['EventsUser']);
		$string = ' It is now you and +'.$user['EventsUser']['guests'].' for '.$user['Event']['name'].'.
txt MINUS1 to remove guests.';
		header('Content-length: '.strlen($string));
		echo $string;
		$this->Sms->save($sms);
		exit();
	}
	function minus1()
	{
		header('HTTP/1.1 200 OK');
		header('Content-type: text/plain');
		$sms['Sms']['event'] = $_POST['event'];
		$sms['Sms']['uid'] = $_POST['uid'];
		$sms['Sms']['min'] = $_POST['min'];
		if($sms['Sms']['uid'] == '[your authenticated user]')
		{
			$sms['Sms']['user_id'] = 11;
			$user['User']['id'] = 11;
		}else{
			$uid = $_POST['uid'];
			$this->User->bindModel(array('hasOne'=>array('EventsUser' =>array('className'=>'EventsUser','foreignKey'=>'user_id','conditions'=>'EventsUser.event_id = User.latest_event','order'=> '','limit'=> ''))));
			$this->User->bindModel(array('belongsTo'=>array('Event' =>array('className'=>'Event','foreignKey'=>'latest_event','conditions'=>'','order'=> '','limit'=> ''))));
			$user = $this->User->find("User.cell_number = '".substr($sms['Sms']['min'],-10,3)."-".substr($sms['Sms']['min'],-7,3)."-".substr($sms['Sms']['min'],-4,4)."'");
			if(!isset($user['User']['id']))
			{
				$string = ' You need to be a part of DWHE to subscribe to it.';
				header('Content-length: '.strlen($string));
				echo $string;
				$this->Sms->save($sms);
				exit();
			}else{
				$sms['Sms']['user_id'] = $user['User']['id'];
			}
		}
		$sms['Sms']['body'] = $_POST['body'];
		if($user['EventsUser']['guests'] == 0)
		{
			$string = ' You weren\'t bringing any guests to '.$user['Event']['name'].' already.';
			header('Content-length: '.strlen($string));
			echo $string;
			$this->Sms->save($sms);
			exit();
		}else{
			$user['EventsUser']['guests'] = $user['EventsUser']['guests'] - 1;
			$user['EventsUser']['status_changed'] = date('Y-m-d H:i:s');
			$this->EventsUser->save($user['EventsUser']);
			if($user['EventsUser']['guests'] == 0)
			{
				$string = ' It is now just you for '.$user['Event']['name'];
			}else{
				$string = ' It is now you and +'.$user['EventsUser']['guests'].' for '.$user['Event']['name'];
			}
			header('Content-length: '.strlen($string));
			echo $string;
			$this->Sms->save($sms);
			exit();
		}
	}
	function enough()
	{
		header('HTTP/1.1 200 OK');
		header('Content-type: text/plain');
		$sms['Sms']['event'] = $_POST['event'];
		$sms['Sms']['uid'] = $_POST['uid'];
		$sms['Sms']['min'] = $_POST['min'];
		if($sms['Sms']['uid'] == '[your authenticated user]')
		{
			$sms['Sms']['user_id'] = 11;
			$user['User']['id'] = 11;
		}else{
			$uid = $_POST['uid'];
			$this->User->bindModel(array('hasOne'=>array('EventsUser' =>array('className'=>'EventsUser','foreignKey'=>'user_id','conditions'=>'EventsUser.event_id = User.latest_event','order'=> '','limit'=> ''))));
			$this->User->bindModel(array('belongsTo'=>array('Event' =>array('className'=>'Event','foreignKey'=>'latest_event','conditions'=>'','order'=> '','limit'=> ''))));
			$user = $this->User->find("User.cell_number = '".substr($sms['Sms']['min'],-10,3)."-".substr($sms['Sms']['min'],-7,3)."-".substr($sms['Sms']['min'],-4,4)."'");
			if(!isset($user['User']['id']))
			{
				$string = ' You need to be a part of DWHE to subscribe to it.';
				header('Content-length: '.strlen($string));
				echo $string;
				$this->Sms->save($sms);
				exit();
			}else{
				$sms['Sms']['user_id'] = $user['User']['id'];
			}
		}
		$sms['Sms']['body'] = $_POST['body'];
		$this->Event->bindModel(array('hasAndBelongsToMany'=>array('User' =>array('className'=>'User','joinTable'=>'events_users','foreignKey'=>'event_id','associationForeignKey'=>'user_id','conditions'=>'','order'=> '','limit'=> '','unique'=>true,'finderQuery'=>'','deleteQuery'=>''))));
		$event = $this->Event->find('Event.id = '.$user['User']['latest_event']);
		$in = 0;
		$out = 0;
		$fifty = 0;
		$user_in_recent = array();
		$user_out_recent = array();
		foreach($event['User'] as $user):
			if($user['EventsUser']['status'] == 1) $in = $in + 1 + $user['EventsUser']['guests'];
			if($user['EventsUser']['status'] == 2) $out++;
			if($user['EventsUser']['status'] == 3) $fifty++;
		endforeach;
		if($event['Event']['active'] == 0)
		{
			$string = ' '.$event['Event']['name'].' is off.
We had '.$in.' in,
'.$out.' out,
and '.$fifty.' 50/50.';
			header('Content-length: '.strlen($string));
			echo $string;
			$this->Sms->save($sms);
			exit();
		}elseif($in >= $event['Event']['need'])
		{
			$string = ' '.$event['Event']['name'].' is on!
We have '.$in.' in,
'.$out.' out,
and '.$fifty.' 50/50.';
			header('Content-length: '.strlen($string));
			echo $string;
			$this->Sms->save($sms);
			exit();
		}else{
			$string = ' '.$event['Event']['name'].' needs more!
We have '.$in.' in,
'.$out.' out,
and '.$fifty.' 50/50.
Still need at least '.$event['Event']['need'].'!';
			header('Content-length: '.strlen($string));
			echo $string;
			$this->Sms->save($sms);
			exit();
		}
	}
	function test_sms($uid)
	{
			$this->User->bindModel(array('hasOne'=>array('EventsUser' =>array('className'=>'EventsUser','foreignKey'=>'user_id','conditions'=>'EventsUser.event_id = User.latest_event','order'=> '','limit'=> ''))));
			$this->User->bindModel(array('belongsTo'=>array('Event' =>array('className'=>'Event','foreignKey'=>'latest_event','conditions'=>'','order'=> '','limit'=> ''))));
			$user = $this->User->find("User.id = ".$uid."");
			print_r($user);
			exit();
	}
	function test_ez_sms()
	{
		$ch=curl_init('https://app.eztexting.com/api/sending');
		curl_setopt($ch,CURLOPT_POST,1);
		curl_setopt($ch,CURLOPT_POSTFIELDS,"user=jloop&pass=9DsUTJ7a&phonenumber=4246341622&subject=The Subject&message=The Message&express=1");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		$data = curl_exec($ch);
		print($data);
		exit();
	}
}
?>