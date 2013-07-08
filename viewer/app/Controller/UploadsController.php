<?php
App::uses('AppController', 'Controller');

class UploadsController extends AppController {
	
	public function index() {
		$uploads = $this->Upload->find('all');
		$this->set('uploads', $uploads);
	}
	
	public function beta() {
		$uploads = $this->Upload->find('all');
		$this->set('uploads', $uploads);
		$this->layout = 'beta';
	}
	
	public function reset() {
		$this->Upload->query('truncate uploads');
		$this->Upload->query('truncate images');
		$this->Session->setFlash('database reset');
		return $this->redirect(array('action' => 'index'));
	}

	public function update() {
		set_time_limit(0);
		$didUpdate = false;
		// get remote list of attachments
		$remote_uploads = $this->Upload->getRemoteList();
		//loop through attachments
		foreach($remote_uploads as $remote_upload) {
			//check if attachment not already exists in our system
			$existing_upload = $this->Upload->findByKey($remote_upload['key']);
			if(empty($existing_upload)) {
				//save upload
				$this->Upload->create();
				$upload = array(
					'key' => $remote_upload['key'],
					'name' => $remote_upload['name']
				);
				if($this->Upload->save($upload)) {
					//download raw upload
					$this->Upload->downloadFile($this->Upload->id, $remote_upload['url']);
					//save images
					$this->Upload->createImages($this->Upload->id);
					$didUpdate = true;
					break;
				}
				
			}
			
		}
		if($didUpdate)
			$this->Session->setFlash('1 upload added');
		else
			$this->Session->setFlash('no new uploads found');
		
		return $this->redirect(array('action' => 'index'));
	}
	
	public function test() {
		//$this->Upload->createImages(31);
		die;
	}
}
