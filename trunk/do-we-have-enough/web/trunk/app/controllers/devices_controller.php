<?php

class DevicesController extends AppController
{

	var $name = 'Devices';
	var $uses = array('User','UserMobileDevice','Event','Group','GroupsUser','EventsUser','Sms','Notification');
	var $helpers = array('Html','Javascript','Error','Ajax','Form');
	var $components = array('uAuth','uCookie','RequestHandler');
	var $access = array ();
	function beforeFilter() {
        parent::beforeFilter();
    }
    
    function generate_device_hash()
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
			$check = $this->UserMobileDevice->find('UserMobileDevice.validator = "'.$hash.'"');
			if(!isset($check['UserMobileDevice']['id'])) $unique = 1;
		}
		return $hash;
	}
	function generate_event_hash()
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
    
    function submit_for_validation()
	{
		$this->User->bindModel(array('hasOne'=>array('UserMobileDevice'=>array('foreignKey'=>false,'conditions'=> array('User.id = UserMobileDevice.user_id')))));
		$user = $this->User->find('User.email = "'.$_POST['email_address'].'" AND UserMobileDevice.device_id = "'.$_POST['device_id'].'"');
		if($user['User']['app_notify_event_change'] == 0 && $user['User']['app_notify_in'] == 0 && $user['User']['app_notify_out'] == 0)
		{
			$user['User']['app_notify_event_change'] = $user['User']['notify_event_change'];
			$user['User']['app_notify_in'] = $user['User']['notify_in'];
			$user['User']['app_notify_out'] = $user['User']['notify_out'];
			$this->User->save($user);
		}
		if(isset($user['User']['id']))
		{
			if($user['UserMobileDevice']['validator'] != '')
			{
				$this->set('result','alreadyExists');
			}else{
				$this->set('result','alreadyValid');			
			}
		}else{
			$user = $this->User->find('User.email = "'.$_POST['email_address'].'"');
			if(!isset($user['User']['id']))
			{
				$user['User']['email'] = $_POST['email_address'];
				$this->User->save($user);
				$user_id = $this->User->getLastInsertId();
			}else{
				$user_id = $user['User']['id'];
			}
			$device['UserMobileDevice']['user_id'] = $user_id;
			$device['UserMobileDevice']['device_id'] = $_POST['device_id'];
			$device['UserMobileDevice']['validator'] = $this->generate_device_hash();
			$this->UserMobileDevice->save($device);
			$activation_link = 'http://'.$this->environment.'.dowehaveenough.com/validate_device/'.$user_id.'/'.$device['UserMobileDevice']['validator'];
			
			$email_to = $_POST['email_address'];
			$email_from = "events@dowehaveenough.com";
			$email_subject = "Validation required for Mobile Device";
			$email_msg = "Hopefully you were expecting this email so we can validate your email to use with the Do We Have Enough App. If so we just need you to click this validation link and you will be good to go. If not, some one may be trying to pretend to you and invite people to events you aren't actually hosting! Oh my!
			
			".$activation_link;
			///SEND THE MESSAGE
			$email_headers = "From: ".$email_from;
			$success = mail($email_to, $email_subject, $email_msg, $email_headers);
			$this->set('result','true');
		}
		$this->render('result');
	}
	function validate_device($user_id,$device_hash)
	{
		$validate = $this->UserMobileDevice->find('UserMobileDevice.user_id = '.$user_id.' AND UserMobileDevice.validator = "'.$device_hash.'"');
		if(isset($validate['UserMobileDevice']['id']))
		{
			$validate['UserMobileDevice']['validator'] = '';
			$this->UserMobileDevice->save($validate);	
		}else{
			$this->render('error_validating');
		}
	}
	function invalidate_device()
	{
		$this->User->bindModel(array('hasOne'=>array('UserMobileDevice'=>array('foreignKey'=>false,'conditions'=> array('User.id = UserMobileDevice.user_id')))));
		$user = $this->User->find('User.email = "'.$_POST['email_address'].'" AND UserMobileDevice.device_id = "'.$_POST['device_id'].'"');
		if(isset($user['UserMobileDevice']['id']))
		{
			$this->UserMobileDevice->delete($user['UserMobileDevice']['id']);
			$this->set('result','true');	
		}else{
			$this->set('result','false');
		}
		$this->render('result');
	}
	function check_for_validation()
	{
		$this->User->bindModel(array('hasOne'=>array('UserMobileDevice'=>array('foreignKey'=>false,'conditions'=> array('User.id = UserMobileDevice.user_id')))));
		$user = $this->User->find('User.email = "'.$_POST['email_address'].'" AND UserMobileDevice.device_id = "'.$_POST['device_id'].'" AND UserMobileDevice.validator = ""');
		if(isset($user['User']['id']))
		{
			$this->set('result','true');
		}else{
			$this->set('result','false');
		}
		$this->render('result');
	}
	function retrieve_groups()
	{
		$this->User->bindModel(array('hasAndBelongsToMany'=>array('Group' =>array('className'=>'Group','joinTable'=>'groups_users','foreignKey'=>'user_id','associationForeignKey'=>'group_id','conditions'=>'Group.name != "" AND Group.name != "Name this group"','order'=> '','limit'=> '','unique'=>true,'finderQuery'=>'','deleteQuery'=>''))));
		$this->User->bindModel(array('hasOne'=>array('UserMobileDevice'=>array('foreignKey'=>false,'conditions'=> array('User.id = UserMobileDevice.user_id')))));
		$user = $this->User->find('User.email = "'.$_POST['email_address'].'" AND UserMobileDevice.device_id = "'.$_POST['device_id'].'" AND UserMobileDevice.validator = ""');
		//$user = $this->User->find('User.email = "jay@jloop.com"');
		if(isset($user['User']['id']))
		{
			foreach($user['Group'] as $key=>$group)
			{
				$user['Group'][$key]['member_count'] = $this->GroupsUser->findCount('GroupsUser.group_id = '.$group['id'].' AND GroupsUser.unsubscribed = 0');
			}
			$this->set('groups',$user['Group']);
		}else{
			$this->set('result','false');
			$this->render('result');
		}
	}
	function create_event()
	{
		$this->User->bindModel(array('hasOne'=>array('UserMobileDevice'=>array('foreignKey'=>false,'conditions'=> array('User.id = UserMobileDevice.user_id')))));
		$user = $this->User->find('User.email = "'.$_POST['email_address'].'" AND UserMobileDevice.device_id = "'.$_POST['device_id'].'" AND UserMobileDevice.validator = ""');
		if(isset($user['User']['id']))
		{
			$i=0;
			$current_url = '/event/'.strtolower(str_replace(array('/',' ','"',"'"),array('-','-','',''),$_POST['event_name'])).'/'.strtolower(str_replace(array('/',' ','"',"'"),array('-','-','',''),$_POST['event_when']));
			while($i<1)
			{
				$already = $this->Event->find('Event.url = "'.$current_url.'"');
				if(!isset($already['Event']['id']))
				{
					$i=1;
				}else{
					$current_url = '/event/'.$this->generate_event_hash().'/'.strtolower(str_replace(array('/',' ','"',"'"),array('-','-','',''),$_POST['event_name'])).'/'.strtolower(str_replace(array('/',' ','"',"'"),array('-','-','',''),$_POST['event_when']));
				}
			}
			$event['Event']['user_id'] = $user['User']['id'];
			$event['Event']['hash'] = $this->generate_event_hash();
			$event['Event']['validated'] = 1;
			$event['Event']['url'] = $current_url;
			$event['Event']['name'] = $_POST['event_name'];
			$event['Event']['date'] = date('Y-m-d',strtotime($_POST['event_when']));
			$event['Event']['when'] = date('g:ia',strtotime($_POST['event_when']));
			//$event['Event']['date'] = date('Y-m-d');
			//$event['Event']['when'] = $_POST['event_when'];
			$event['Event']['where'] = $_POST['event_where'];
			$event['Event']['need'] = $_POST['event_need'];
			$event['Event']['details'] = $_POST['event_details'];
			if(time() < strtotime($_POST['cancel_email'])) $event['Event']['cancel_email'] = date('Y-m-d H:i:s',strtotime($_POST['cancel_email']));
			if(time() < strtotime($_POST['status_email'])) $event['Event']['status_email'] = date('Y-m-d H:i:s',strtotime($_POST['status_email']));
			$event['Event']['cannot_invite_others'] = $_POST['event_cannot_invite_others'];
			$event['Event']['cannot_bring_guests'] = $_POST['event_cannot_bring_guests'];
			if($this->Event->save($event))
			{
				$event_id = $this->Event->getLastInsertId();
				if($_POST['group_id'] > 0)
				{
					$group_id = $_POST['group_id'];
					$this->GroupsUser->bindModel(array('belongsTo'=>array('User' =>array('className'=>'User','foreignKey'=>'user_id','conditions'=>'','order'=> '','limit'=> ''))));
					$users = $this->GroupsUser->findAll('GroupsUser.group_id = '.$_POST['group_id'].' AND GroupsUser.unsubscribed = 0');
					foreach($users as $user):
						if($user['User']['unsubscribed'] != 1)
						{
							$this->EventsUser->set(array('id'=>NULL));
							$event_user['EventsUser']['id'] = NULL;
							$event_user['EventsUser']['event_id'] = $event_id;
							$event_user['EventsUser']['user_id'] = $user['GroupsUser']['user_id'];
							$event_user['EventsUser']['hash'] = $this->generate_event_hash();
							$this->EventsUser->save($event_user);
						}
					endforeach;
				}else{
					if($_POST['group_name'] != '')
					{
						$group['Group']['name'] = $_POST['group_name'];
					}else{
						$group['Group']['name'] = $_POST['event_name'].' - '.$_POST['event_when'];
					}
					$this->Group->save($group);
					$group_id = $this->Group->getLastInsertId();
					$list = $_POST['group_members'].','.$_POST['email_address'];
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
								$event_user['EventsUser']['hash'] = $this->generate_event_hash();
								$this->EventsUser->save($event_user);
							}
						}
					endforeach;
				}
				$save_event['Event']['id'] = $event_id;
				$save_event['Event']['group_id'] = $group_id;
				$this->Event->save($save_event);
				$this->User->bindModel(array('hasMany'=>array('UserMobileDevice' =>array('className'=>'UserMobileDevice','foreignKey'=>'user_id','conditions'=>'UserMobileDevice.device_token != "" AND UserMobileDevice.notify_push = 1 AND UserMobileDevice.validator = ""','order'=> '','limit'=> ''))));
				$this->Event->bindModel(array('hasAndBelongsToMany'=>array('User' =>array('className'=>'User','joinTable'=>'events_users','foreignKey'=>'event_id','associationForeignKey'=>'user_id','conditions'=>'','order'=> '','limit'=> '','unique'=>true,'finderQuery'=>'','deleteQuery'=>''))));
				$this->Event->bindModel(array('belongsTo'=>array('Host' =>array('className'=>'User','foreignKey'=>'user_id','conditions'=>'','order'=> '','limit'=> ''))));
				$event = $this->Event->find('Event.id = '.$event_id,null,null,4);
				$host_name = $event['Host']['email'];
				if($event['Host']['name'] != '') $host_name = $event['Host']['name'];
				foreach($event['User'] as $user):
					
					//email based off user setting
					if($user['notify_event_change'] || (!$user['notify_event_change'] && !$user['app_notify_event_change']))
					{
						$email_to = $user['email'];
						$email_from = "events@dowehaveenough.com";
						$email_subject = $event['Event']['name']." - ".$event['Event']['when']." - Do we have enough?";
						$email_msg = $host_name." has sent you an invitation to:

".$event['Event']['name']."
 * When: ".$event['Event']['when'].", ".date('F jS, Y', strtotime($event['Event']['date']))."
";				
if($event['Event']['where'] != '') $email_msg .= " * Where: ".$event['Event']['where']."
";				
$email_msg .= " * We need: ".$event['Event']['need']." people.
";				
if($event['Event']['details'] != '') $email_msg .= " * Additional details: ".$event['Event']['details']."
";				
$email_msg .= "
				
ARE YOU IN?	
 ** Yes, I'm IN! - http://".$this->environment.".dowehaveenough.com/event_status/".$user['EventsUser']['hash']."/1
 ** Nope, I'm OUT. - http://".$this->environment.".dowehaveenough.com/event_status/".$user['EventsUser']['hash']."/2
				
----			
				
OTHER OPTIONS
 ** I'm 50/50 - http://".$this->environment.".dowehaveenough.com/event_status/".$user['EventsUser']['hash']."/3
";				
if($event['Event']['cannot_bring_guests'] == 0) $email_msg .= " ** Yes, I'm IN & bringing extra - http://".$this->environment.".dowehaveenough.com/event_status/".$user['EventsUser']['hash']."/4
";				
				
$email_msg .= "
----			
				
Direct Link to Event: http://".$this->environment.".dowehaveenough.com/go_to_event/".$user['EventsUser']['hash']."
			
			
			
----		
			
If you wish to unsubscribe from dowehaveenough.com - http://".$this->environment.".dowehaveenough.com/unsubscribe/".$user['email']."/".$user['EventsUser']['hash'];
						///SEND THE MESSAGE
						$email_headers = "From: ".$email_from;
						$success = mail($email_to, $email_subject, $email_msg, $email_headers);
					}
					
					$user['latest_event'] = $event['Event']['id'];
					$this->User->save($user);
					
					//app notification based off user setting
					if($user['app_notify_event_change'])
					{
						foreach($user['UserMobileDevice'] as $device):
							$this->Notification->save_notification($user['id'],$device['device_token'],'You have been invited to '.$event['Event']['name'],$event['Event']['id'],2);
						endforeach;
					}
					/*if($user['notify_text'] == 1)
					{
						$message = $host_name.' has invited you to '.$event['Event']['name'].' - '.$event['Event']['when'].'. To reply, txt IAMIN, IAMOUT, or IAM50. For status, txt ENOUGH?';
						$this->send_sms($user['id'],$message);
					}*/
				endforeach;
				$this->set('status','active');
				$this->set('event_id',$event_id);
			}else{
				$this->set('status','error');
				$this->set('event_id', 'false');				
			}
		}else{
			$user = $this->User->find('User.email = "'.$_POST['email_address'].'"');
			if(!isset($user['User']['id']))
			{
				$user['User']['email'] = $_POST['email_address'];
				$this->User->save($user);
				$user_id = $this->User->getLastInsertId();
			}else{
				$user_id = $user['User']['id'];
			}
			$device['UserMobileDevice']['user_id'] = $user_id;
			$device['UserMobileDevice']['device_id'] = $_POST['device_id'];
			$device['UserMobileDevice']['validator'] = $this->generate_device_hash();
			$this->UserMobileDevice->save($device);
			$i=0;
			$current_url = '/event/'.strtolower(str_replace(array('/',' ','"',"'"),array('-','-','',''),$_POST['event_name'])).'/'.strtolower(str_replace(array('/',' ','"',"'"),array('-','-','',''),$_POST['event_when']));
			while($i<1)
			{
				$already = $this->Event->find('Event.url = "'.$current_url.'"');
				if(!isset($already['Event']['id']))
				{
					$i=1;
				}else{
					$current_url = '/event/'.$this->generate_event_hash().'/'.strtolower(str_replace(array('/',' ','"',"'"),array('-','-','',''),$_POST['event_name'])).'/'.strtolower(str_replace(array('/',' ','"',"'"),array('-','-','',''),$_POST['event_when']));
				}
			}
			$event['Event']['user_id'] = $user_id;
			$event['Event']['hash'] = $this->generate_event_hash();
			$event['Event']['validated'] = 0;
			$event['Event']['url'] = $current_url;
			$event['Event']['name'] = $_POST['event_name'];
			$event['Event']['date'] = date('Y-m-d',strtotime($_POST['event_when']));
			$event['Event']['when'] = date('g:ia',strtotime($_POST['event_when']));
			$event['Event']['where'] = $_POST['event_where'];
			$event['Event']['need'] = $_POST['event_need'];
			$event['Event']['details'] = $_POST['event_details']; 
			if(time() < strtotime($_POST['cancel_email'])) $event['Event']['cancel_email'] = $_POST['cancel_email'];
			if(time() < strtotime($_POST['status_email'])) $event['Event']['status_email'] = $_POST['status_email'];
			$event['Event']['cannot_invite_others'] = $_POST['event_cannot_invite_others'];
			$event['Event']['cannot_bring_guests'] = $_POST['event_cannot_bring_guests'];
			if($this->Event->save($event))
			{
				$event_id = $this->Event->getLastInsertId();
				if($_POST['group_id'] > 0)
				{
					$group_id = $_POST['group_id'];
					$this->GroupsUser->bindModel(array('belongsTo'=>array('User' =>array('className'=>'User','foreignKey'=>'user_id','conditions'=>'','order'=> '','limit'=> ''))));
					$users = $this->GroupsUser->findAll('GroupsUser.group_id = '.$_POST['group_id'].' AND GroupsUser.unsubscribed = 0');
					foreach($users as $user):
						if($user['User']['unsubscribed'] != 1)
						{
							$this->EventsUser->set(array('id'=>NULL));
							$event_user['EventsUser']['id'] = NULL;
							$event_user['EventsUser']['event_id'] = $event_id;
							$event_user['EventsUser']['user_id'] = $user['GroupsUser']['user_id'];
							$event_user['EventsUser']['hash'] = $this->generate_event_hash();
							$this->EventsUser->save($event_user);
						}
					endforeach;
				}else{
					if($_POST['group_name'] != '')
					{
						$group['Group']['name'] = $_POST['group_name'];
					}else{
						$group['Group']['name'] = $_POST['event_name'].' - '.$_POST['event_when'];
					}
					$this->Group->save($group);
					$group_id = $this->Group->getLastInsertId();
					$list = $_POST['group_members'].','.$_POST['email_address'];
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
								$event_user['EventsUser']['hash'] = $this->generate_event_hash();
								$this->EventsUser->save($event_user);
							}
						}
					endforeach;
				}
				$save_event['Event']['id'] = $event_id;
				$save_event['Event']['group_id'] = $group_id;
				$this->Event->save($save_event);
				$this->set('status','pending_validation');
				$this->set('event_id', 'false');
				$activation_link = 'http://'.$this->environment.'.dowehaveenough.com/activate_event/'.$event['Event']['hash'].'/'.$device['UserMobileDevice']['validator'];
		
				$email_to = $_POST['email_address'];
				$email_from = "events@dowehaveenough.com";
				$email_subject = "Validation required to setup event";
				$email_msg = "Your event is almost ready to roll out, we just need you to click this validation link to get those invites rolling. This is a one time thing we need to do to validate your email on your mobile device.
				
				".$activation_link;
				///SEND THE MESSAGE
				$email_headers = "From: ".$email_from;
				$success = mail($email_to, $email_subject, $email_msg, $email_headers);
			}else{
				$this->set('status','error');
				$this->set('event_id', 'false');
			}
		}
	}
	function get_event()
	{
		$this->User->bindModel(array('hasOne'=>array('UserMobileDevice'=>array('foreignKey'=>false,'conditions'=> array('User.id = UserMobileDevice.user_id')))));
		$user = $this->User->find('User.email = "'.$_POST['email_address'].'" AND UserMobileDevice.device_id = "'.$_POST['device_id'].'" AND UserMobileDevice.validator = ""');
		if(isset($user['User']['id']))
		{
			$this->Event->bindModel(array('hasAndBelongsToMany'=>array('User' =>array('className'=>'User','joinTable'=>'events_users','foreignKey'=>'event_id','associationForeignKey'=>'user_id','conditions'=>'','order'=> '','limit'=> '','unique'=>true,'finderQuery'=>'','deleteQuery'=>''))));
			$event = $this->Event->find('Event.id = '.$_POST['event_id'].' AND Event.validated = 1');
			$this->set('event',$event);
			$this->User->bindModel(array('hasOne'=>array('EventsUser' =>array('className'=>'EventsUser','foreignKey'=>'user_id','conditions'=>'EventsUser.event_id = '.$event['Event']['id'],'order'=> '','limit'=> ''))));	
			$the_user = $this->User->find('User.id = '.$user['User']['id']);
			$this->set('user',$the_user);
		}else{
			$this->set('result','false');
			$this->render('result');
		}
	}
	function invite_user()
	{
		$this->User->bindModel(array('hasOne'=>array('UserMobileDevice'=>array('foreignKey'=>false,'conditions'=> array('User.id = UserMobileDevice.user_id')))));
		$the_user = $this->User->find('User.email = "'.$_POST['email_address'].'" AND UserMobileDevice.device_id = "'.$_POST['device_id'].'" AND UserMobileDevice.validator = ""');
		if(isset($the_user['User']['id']))
		{
			$this->User->bindModel(array('hasMany'=>array('UserMobileDevice' =>array('className'=>'UserMobileDevice','foreignKey'=>'user_id','conditions'=>'UserMobileDevice.device_token != "" AND UserMobileDevice.notify_push = 1 AND UserMobileDevice.validator = ""','order'=> '','limit'=> ''))));
			$user_exist = $this->User->find('User.email = "'.$_POST['invite_email_address'].'"');
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
				$user['User']['email'] = $_POST['invite_email_address'];
				$this->User->save($user);
				$user_id = $this->User->getLastInsertId();
			}
			if($unsubscribed == 0)
			{
				$event = $this->Event->find('Event.id = '.$_POST['event_id']);
				$event_id = $event['Event']['id'];
				if($_POST['add_to_group'] == 1)
				{
					$in_group = $this->GroupsUser->find('GroupsUser.user_id = '.$user_id.' AND GroupsUser.group_id = '.$event['Event']['group_id']);
					if(isset($in_group['GroupsUser']['id']))
					{
						if($in_group['GroupsUser']['unsubscribed'] == 1)
						{
							$this->set('result','This user has unsubscribed from this group.');
						}else{
							$this->set('result','This user is already in this group.');
						}
						$error = 1;
					}else{
						$this->GroupsUser->set(array('id'=>NULL));
						$group_user['GroupsUser']['id'] = NULL;
						$group_user['GroupsUser']['group_id'] = $event['Event']['group_id'];
						$group_user['GroupsUser']['user_id'] = $user_id;
						$this->GroupsUser->save($group_user);
						$message['added_to_group'] = 1;
					}
				}
				if(!isset($error))
				{
					$in_event = $this->EventsUser->find('EventsUser.user_id = '.$user_id.' AND EventsUser.event_id = '.$event_id);
					if(isset($in_event['EventsUser']['id']))
					{
						$this->set('result','This user is already a part of this event.');
					}else{
						$this->EventsUser->set(array('id'=>NULL));
						$event_user['EventsUser']['id'] = NULL;
						$event_user['EventsUser']['event_id'] = $event_id;
						$event_user['EventsUser']['user_id'] = $user_id;
						$event_user['EventsUser']['hash'] = $this->generate_event_hash();
						$this->EventsUser->save($event_user);
						$host = $this->User->find('User.id = '.$the_user['User']['id']);
						$host_name = $host['User']['email'];
						if($host['User']['name'] != '') $host_name = $host['User']['name'];
						$email_to = $_POST['invite_email_address'];
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
 ** Yes, I'm IN! - http://".$this->environment.".dowehaveenough.com/event_status/".$event_user['EventsUser']['hash']."/1
 ** Nope, I'm OUT. - http://".$this->environment.".dowehaveenough.com/event_status/".$event_user['EventsUser']['hash']."/2
					
----				
					
OTHER OPTIONS		
 ** I'm 50/50 - http://".$this->environment.".dowehaveenough.com/event_status/".$event_user['EventsUser']['hash']."/3
";					
if($event['Event']['cannot_bring_guests'] == 0) $email_msg .= " ** Yes, I'm IN & bringing extra - http://".$this->environment.".dowehaveenough.com/event_status/".$event_user['EventsUser']['hash']."/4
";					
					
$email_msg .= "		
----				
					
Direct Link to Event: http://".$this->environment.".dowehaveenough.com/go_to_event/".$event_user['EventsUser']['hash']."
					
					
					
----				
					
If you wish to unsubscribe from dowehaveenough.com - http://".$this->environment.".dowehaveenough.com/unsubscribe/".$user['User']['email']."/".$event_user['EventsUser']['hash'];
						///SEND THE MESSAGE
						$email_headers = "From: ".$email_from;
						$success = mail($email_to, $email_subject, $email_msg, $email_headers);
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
						$this->set('result','true');
					}
				}
			}else{
				$this->set('result','This user is unsubscribed from DWHE.');
			}
		}else{
			$this->set('result','false');
		}
		$this->render('result');
	}
	
	function get_event_list()
	{
		$this->User->bindModel(array('hasAndBelongsToMany'=>array('Event' =>array('className'=>'Event','joinTable'=>'events_users','foreignKey'=>'user_id','associationForeignKey'=>'event_id','conditions'=>'Event.validated = 1','order'=> 'Event.created DESC','limit'=> '','unique'=>true,'finderQuery'=>'','deleteQuery'=>''))));
		$this->User->bindModel(array('hasOne'=>array('UserMobileDevice'=>array('foreignKey'=>false,'conditions'=> array('User.id = UserMobileDevice.user_id')))));
		$user = $this->User->find('User.email = "'.$_POST['email_address'].'" AND UserMobileDevice.device_id = "'.$_POST['device_id'].'" AND UserMobileDevice.validator = ""');
		//$user = $this->User->find('User.email = "jay@jloop.com"');
		if(isset($user['User']['id']))
		{
			foreach($user['Event'] as $key=>$event)
			{
				$count = 0;
				$members_in = $this->EventsUser->findAll('EventsUser.event_id = '.$event['id'].' AND EventsUser.status = 1');
				foreach($members_in as $event_user):
					if($event_user['EventsUser']['status'] == 1)
					{
						$count = $count + 1 + $event_user['EventsUser']['guests'];
					}
				endforeach;
				$user['Event'][$key]['members_in'] = $count;
			}
			$this->set('user',$user);
		}else{
			$this->set('result','false');
			$this->render('result');
		}
	}
	function set_notify_when()
	{
		$this->User->bindModel(array('hasOne'=>array('UserMobileDevice'=>array('foreignKey'=>false,'conditions'=> array('User.id = UserMobileDevice.user_id')),'EventsUser' =>array('className'=>'EventsUser','foreignKey'=>'user_id','conditions'=>'EventsUser.event_id = '.$_POST['event_id'],'order'=> '','limit'=> ''))));
		$user = $this->User->find('User.email = "'.$_POST['email_address'].'" AND UserMobileDevice.device_id = "'.$_POST['device_id'].'"');
		if(isset($user['User']['id']))
		{
			if($_POST['notify_when'] == 0)
			{
				$user['EventsUser']['notify_reach_checked'] = 0;
			}else{
				$user['EventsUser']['notify_reach_checked'] = 1;			
			}
			$user['EventsUser']['notified_reached'] = 0;
			$user['EventsUser']['notify_reach_count'] = $_POST['notify_when'];
			$this->EventsUser->save($user['EventsUser']);
			$this->set('result','true');	
		}else{
			$this->set('result','false');
		}
		$this->render('result');
	}
	function get_user()
	{
		$this->User->bindModel(array('belongsTo'=>array('Event'=>array('foreignKey'=>'latest_event','conditions'=> ''))));
		$this->User->bindModel(array('hasOne'=>array('UserMobileDevice'=>array('foreignKey'=>false,'conditions'=> array('User.id = UserMobileDevice.user_id')))));
		$user = $this->User->find('User.email = "'.$_POST['email_address'].'" AND UserMobileDevice.device_id = "'.$_POST['device_id'].'" AND UserMobileDevice.validator = ""');
		if(isset($user['User']['id']))
		{
		
			$this->set('user',$user);
		}else{
			$this->set('result','false');
			$this->render('result');
		}
	}
	function save_user()
	{
		$this->User->bindModel(array('hasOne'=>array('UserMobileDevice'=>array('foreignKey'=>false,'conditions'=> array('User.id = UserMobileDevice.user_id')))));
		$user = $this->User->find('User.email = "'.$_POST['email_address'].'" AND UserMobileDevice.device_id = "'.$_POST['device_id'].'" AND UserMobileDevice.validator = ""');
		if(isset($user['User']['id']))
		{
			if(isset($_POST['name'])) $user['User']['name'] = trim($_POST['name']);
			if(isset($_POST['cell_number'])) $user['User']['cell_number'] = $_POST['cell_number'];
			if(isset($_POST['notify_text'])) $user['User']['notify_text'] = $_POST['notify_text'];
			if(isset($_POST['notify_event_change'])) $user['User']['notify_event_change'] = $_POST['notify_event_change'];
			if(isset($_POST['notify_in'])) $user['User']['notify_in'] = $_POST['notify_in'];
			if(isset($_POST['notify_out'])) $user['User']['notify_out'] = $_POST['notify_out'];
			if(isset($_POST['app_notify_event_change'])) $user['User']['app_notify_event_change'] = $_POST['app_notify_event_change'];
			if(isset($_POST['app_notify_in'])) $user['User']['app_notify_in'] = $_POST['app_notify_in'];
			if(isset($_POST['app_notify_out'])) $user['User']['app_notify_out'] = $_POST['app_notify_out'];
			/*if(isset($_POST['notify_push']))
			{
				$user['UserMobileDevice']['notify_push'] = $_POST['notify_push'];
				$this->UserMobileDevice->save($user);
			}*/
			if($this->User->save($user))
			{
				$this->set('result','true');
				$this->render('result');
			}else{
				$this->set('result','false');
				$this->render('result');
			}
		}else{
			$this->set('result','false');
			$this->render('result');
		}
	}
	function save_token()
	{
		$this->User->bindModel(array('hasOne'=>array('UserMobileDevice'=>array('foreignKey'=>false,'conditions'=> array('User.id = UserMobileDevice.user_id')))));
		$user = $this->User->find('User.email = "'.$_POST['email_address'].'" AND UserMobileDevice.device_id = "'.$_POST['device_id'].'" AND UserMobileDevice.validator = ""');
		if(isset($user['User']['id']))
		{
			$user['UserMobileDevice']['device_token'] = $_POST['device_token'];
			if($this->UserMobileDevice->save($user))
			{
				$this->set('result','true');
				$this->render('result');
			}else{
				$this->set('result','false');
				$this->render('result');
			}
		}else{
			$this->set('result','false');
			$this->render('result');
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
	function set_my_event_status()
	{
		$this->User->bindModel(array('hasOne'=>array('UserMobileDevice'=>array('foreignKey'=>false,'conditions'=> array('User.id = UserMobileDevice.user_id')),'EventsUser' =>array('className'=>'EventsUser','foreignKey'=>'user_id','conditions'=>'EventsUser.event_id = '.$_POST['event_id'],'order'=> '','limit'=> ''))));
		$user = $this->User->find('User.email = "'.$_POST['email_address'].'" AND UserMobileDevice.device_id = "'.$_POST['device_id'].'"');
		if(isset($user['UserMobileDevice']['id']))
		{
			$user['EventsUser']['status'] = $_POST['status'];
			$user['EventsUser']['guests'] = $_POST['guests'];
			$user['EventsUser']['status_changed'] = date('Y-m-d H:i:s'); 
			$this->EventsUser->save($user['EventsUser']);
			$this->set('result','true');	
		}else{
			$this->set('result','false');
		}
		$this->render('result');
	}
	function push_notifications()
	{
		$notifications = $this->Notification->findAll('Notification.sent = 0',null,'Notification.device_token ASC, Notification.level DESC');
		if ($this->environment == "dev") {
			$apnsHost = 'gateway.sandbox.push.apple.com';
			$apnsPort = 2195;
			$apnsCert = '/var/www/vhosts/dowehaveenough.com/subdomains/dev/httpdocs/app/webroot/dev-cert.pem';
		} else {
			$apnsHost = 'gateway.push.apple.com';
			$apnsPort = 2195;
			$apnsCert = '/var/www/vhosts/dowehaveenough.com/httpdocs/app/webroot/prod-cert.pem';
		}

		$streamContext = stream_context_create();
		stream_context_set_option($streamContext, 'ssl', 'local_cert', $apnsCert);
		//stream_context_set_option($streamContext, 'ssl', 'passphrase', 'a4d6s5');
		//stream_context_set_option($streamContext, 'ssl', 'verify_peer', false);
		$apns = stream_socket_client('ssl://' . $apnsHost . ':' . $apnsPort, $error, $errorString, 2, STREAM_CLIENT_CONNECT,$streamContext);
		if (!$apns)
		{
			print "Failed to connect".$error." ".$errorString;
		}else{
			$current_token = '';
			foreach($notifications as $notification):
				$notification['Notification']['sent'] = 1;
				$this->Notification->save($notification);
				if($current_token != $notification['Notification']['device_token'])
				{
					$payload = '';
					$current_token = $notification['Notification']['device_token'];
					$payload['aps'] = array('alert' => $notification['Notification']['alert'], 'sound' => 'default');
					$payload['push_data'] = array('event_id' => ''.$notification['Notification']['event_id'].'');
					$payload = json_encode($payload);
					$apnsMessage = chr(0).chr(0).chr(32).pack('H*',str_replace(' ', '',$notification['Notification']['device_token'])).chr(0).chr(strlen($payload)).$payload;
					fwrite($apns, $apnsMessage);
				}
			endforeach;
		}
		socket_close($apns);
		fclose($apns);
		exit();
	}
	function get_feedback()
	{
		$apnsHost = 'feedback.sandbox.push.apple.com';
		$apnsPort = 2196;
			$apnsCert = '/var/www/vhosts/dowehaveenough.com/subdomains/dev/httpdocs/app/webroot/dev-cert.pem';

		$streamContext = stream_context_create();
		stream_context_set_option($streamContext, 'ssl', 'local_cert', $apnsCert);
		//stream_context_set_option($streamContext, 'ssl', 'passphrase', 'a4d6s5');
		//stream_context_set_option($streamContext, 'ssl', 'verify_peer', false);
		$apns = stream_socket_client('ssl://' . $apnsHost . ':' . $apnsPort, $error, $errorString, 2, STREAM_CLIENT_CONNECT,$streamContext);
		if (!$apns)
		{
			print "Failed to connect".$error." ".$errorString;
		}else{
			echo 'Connected to feedback sandbox...';
        	while(($in = fread($apns, 1024)) != EOF)
        	{
            	echo $in;   
        	}
        	socket_close($fp);
        	fclose($fp);
		}
		exit();
	}
}
?>