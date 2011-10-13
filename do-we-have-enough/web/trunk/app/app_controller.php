<?php
 
class AppController extends Controller
{
    var $layout = 'dowehaveenough_main';
	var $from = 'info@dowehaveenough.com';
	var $sitename = 'dowehaveenough.com';
	var $startpage = '';
	var $environment = '';
	var $meta_keywords = '';
	var $meta_description = '';
	var $site_root = '';
	var $access = array();	
	var $uses = array();
	var $components = array('uAuth','uCookie');
	var $helpers = array('Javascript','Html','Ajax');
	var $Sanitize;
	function startLog()
	{
		$db =& ConnectionManager::getDataSource('default');
        $db->fullDebug = true;
	}
	function beforeFilter()
	{
		$site_root = $_SERVER['SERVER_NAME'];
	
		if($site_root == 'localhost') $site_root = 'localhost:8888';
		$this->site_root = $site_root;
		$this->set('site_root', $site_root);
		$this->set('meta_keywords', $this->meta_keywords);
		$this->set('meta_keywords_add', "");
		$this->set('meta_description', $this->meta_description);
		if ($_SERVER['SERVER_NAME'] == "staging.dowehaveenough.com")
		{
			$this->set('environment', "staging");
			$this->environment = "staging";
		}elseif($_SERVER['SERVER_NAME'] == "dev.dowehaveenough.com")
		{
			$this->set('environment', "dev");
			$this->environment = "dev";
		}else{
			$this->set('environment', "www");
			$this->environment = "www";
		}
		$this->uAuth->admins = array('Admin','User');
		$this->uAuth->set();
		$this->set('uAuth',$this->uAuth);
		return true;
	}
	
	 function logSQL()
	 {
	 	ob_start();
		$db =& ConnectionManager::getDataSource('default');
		$db->showLog();
		$log_contents = ob_get_clean();
		$this->log( $log_contents ); 
	 	
	 }
	
	//////////////////////////old functions
	/*
	function checkSession()
    {
        // If the session info hasn't been set...
        if (!$this->Session->check('User'))
        {
            // Force the user to login
            $this->redirect('/users/login');
            exit();
        }
    }
	*/
}
?>