<?php

class uAuthComponent
{
	var $components = array('Session');
	
	/**
	* id of the logged in user
	*
	* @var unknown_type
	* @access private
	*/
	var $id = null;
	
	/**
	* username of the logged in user
	*
	* @var string
	* @access private
	*/
	var $event_id = null;
	var $user_id = null;
	
	/**
	* role assigned to the logged in user
	*
	* @var string
	* @access private
	*/
	var $role = null;
	

	/**
	* if admins passes then set admin var
	*
	* @var bool
	* @access private
	*/
	var $admin = null;
	/**
	* if role assigned to the logged in user matches admins array
	*
	* @var array
	* @access private
	*/
	var $admins = array('Admin');
	
	/**
	* Error messages to be displayed if the user is short of access for the requested action.
	*
	* @var string
	* @access private
	*/
	var $errors = null;
	/**
	* Function to check the session and return local vars
	*
	* @param string $data used for login method
	* @return errors
	*/
	function set($event_id=NULL,$user_id=NULL)
	{
		//mail("jay@jloop.com", "test", "test");
		if($event_id)
		{
			$this->Session->write('dowehaveenough.event_id', $event_id);
		}
		if($user_id)
		{
			$this->Session->write('dowehaveenough.user_id', $user_id);
		}
		if($this->Session->check('dowehaveenough') && $this->Session->valid('dowehaveenough'))
		{
			$this->event_id = $this->Session->read('dowehaveenough.event_id');
			$this->user_id = $this->Session->read('dowehaveenough.user_id');
		}
		elseif($this->Session->error())
		{
			return $this->Session->error();
		}
	}

	/**
	* changerole method changes role in session
	*
	* @return errors
	*/
	function changerole($role)
	{
		$this->Session->write('dowehaveenough.role', $role);
		if($this->Session->check('dowehaveenough') && $this->Session->valid('dowehaveenough'))
		{
			$this->role = $this->Session->read('dowehaveenough.role');
			if(in_array($this->role,$this->admins))
			{
				$this->admin = 1;	
			}
		}
		elseif($this->Session->error())
		{
			return $this->Session->error();
		}
	}
	/**
	* changerole method changes role in session
	*
	* @return errors
	*/
	function changeUsername($username)
	{
		$this->Session->write('dowehaveenough.username', $username);
		if($this->Session->check('dowehaveenough') && $this->Session->valid('dowehaveenough'))
		{
			$this->username = $this->Session->read('dowehaveenough.username');
		}
		elseif($this->Session->error())
		{
			return $this->Session->error();
		}
	}
	/**
	* changeemail method changes email in session
	*
	* @return errors
	*/
	function changeEmail($email)
	{
		$this->Session->write('dowehaveenough.email', $email);
		if($this->Session->check('dowehaveenough') && $this->Session->valid('dowehaveenough'))
		{
			$this->email = $this->Session->read('dowehaveenough.email');
		}
		elseif($this->Session->error())
		{
			return $this->Session->error();
		}
	}
	/**
	* logout method deletes session
	*
	* @return errors
	*/
	function logout()
	{
		$this->Session->del('dowehaveenough.event_id');
		$this->Session->del('dowehaveenough.user_id');
		$this->Session->del('dowehaveenough');
		if($this->Session->error())
		{
			return $this->Session->error();
		}
	}
	
	/**
	* Function to check the access for the action based on the access list
	*
	* @param string $action The action for which we need to check the access
	* @param array $access Access array for the controller's actions
	* @return boolean
	*/
	function check($action, $access = '')
	{
		if (is_array($access) && array_key_exists($action, $access))
		{
			if($this->role)
			{	
				if (in_array($this->role, $access[$action]['role']))
				{
					return true;
				}
				else
				{
					return false; 
				}
			}
			else
			{
				return false; 
			}
		}
		return	true;
	} 
}
?>