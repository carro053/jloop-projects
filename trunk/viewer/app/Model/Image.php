<?php
App::uses('AppModel', 'Model');

class Image extends AppModel {
	
	public $virtualFields = array(
	    'src' => 'CONCAT("/files/images/", Image.upload_id, "-", Image.id, ".jpg")',
	    'src_small' => 'CONCAT("/files/images/", Image.upload_id, "-", Image.id, "-s.jpg")'
	);

	public function saveFromUpload($upload_id, $file) {
		$this->create();
		$image = array(
			'upload_id' => $upload_id
		);
		if($this->save($image)) {
			copy(WWW_ROOT.'/files/uploads/'.$file, WWW_ROOT.'/files/images/'.$upload_id.'-'.$this->id.'.jpg');
			exec('convert '.WWW_ROOT.'/files/uploads/'.$file.' -resize "x100" '.WWW_ROOT.'/files/images/'.$upload_id.'-'.$this->id.'-s.jpg');
		}
	}
	
}