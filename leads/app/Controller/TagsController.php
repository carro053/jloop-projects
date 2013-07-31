<?php
App::uses('AppController', 'Controller');

class TagsController extends AppController {

	public function index() {
		$tags = $this->Tag->find('all');
		$this->set('tags', $tags);
	}
	
	public function create() {
		if($this->request->is('post')) {
			if($this->Tag->save($this->request->data)) {
				$this->Session->setFlash('Tag created');
				return $this->redirect('/Tags/index');
			}
		}
	}
	
	public function update($id) {
		if($this->request->is('post') || $this->request->is('put')) {
			if($this->Tag->save($this->request->data)) {
				$this->Session->setFlash('Tag updated');
				return $this->redirect('/Tags/index');
			}
		}else{
			$this->request->data = $this->Tag->findById($id);
		}
	}
	
	public function delete($id = null) {
		if(!empty($id) && $this->Tag->delete($id)) {
			$this->Tag->query('DELETE FROM `leads_tags` WHERE `tag_id` = '.$id);
			$this->Session->setFlash('Tag deleted');
		}
		return $this->redirect('/Tags/index');
	}
}