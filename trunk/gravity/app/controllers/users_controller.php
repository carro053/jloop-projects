<?php 
class UsersController extends AppController
{
	var $name = 'Users';
	var $uses = array('User');
	var $components = array('Auth');
	
	function login()
	{
		
	}
	
	function logout()
	{
		$this->redirect($this->Auth->logout());
	}
	
	function hash($password)
	{
		die($this->Auth->password($password));
	}
}
?>