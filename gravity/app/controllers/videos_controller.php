<?php
class VideosController extends AppController {
	var $name = 'Videos';
	var $helpers = array('Html', 'Session');
	var $uses = array('Video');
	
	public function beforeFilter() {
		parent::beforeFilter();
		
		$this->Auth->allow('*');
		
		if (isset($this->params['admin'])) {
			$this->Toolbar->verifyAdmin();
			$this->layout = 'admin';
		}
	}
	
	function index()
	{
	
	}
}

?>