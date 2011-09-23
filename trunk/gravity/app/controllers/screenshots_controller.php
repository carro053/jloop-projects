<?php
class ScreenshotsController extends AppController {
	var $name = 'Screenshots';
	var $helpers = array('Html', 'Session');
	var $uses = array('Screenshot');
	
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