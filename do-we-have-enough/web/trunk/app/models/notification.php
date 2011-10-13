<?php
class Notification extends AppModel
{
    var $name = 'Notification';
    
    
    function save_notification($user_id,$device_token,$alert,$event_id,$level)
    {
    	$notification['Notification']['id'] = NULL;
    	$notification['Notification']['user_id'] = $user_id;
    	$notification['Notification']['device_token'] = $device_token;
    	$notification['Notification']['alert'] = $alert;
    	$notification['Notification']['event_id'] = $event_id;
    	$notification['Notification']['level'] = $level;
    	if($this->save($notification))
    	{
    		return true;
    	}else{
    		return false;
    	}
    }
    
} ?>