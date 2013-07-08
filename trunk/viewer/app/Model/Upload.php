<?php
App::uses('AppModel', 'Model');

class Upload extends AppModel {

	var $hasMany = array(
		'Image'
	);
	
	public function getRemoteList() {
		$ch = curl_init('https://basecamp.com/2042279/api/v1/projects/2921430/attachments.json');
		$header = array('User-Agent: JLOOP File Viewer (todd@jloop.com)');
		curl_setopt($ch, CURLOPT_USERPWD, 'tpastell:werttrew1');
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($ch);
		curl_close($ch);
		$data = json_decode($response, true);
		return $data;
	}
	
	public function downloadFile($id, $url) {
		$ch = curl_init($url);
		$header = array('User-Agent: JLOOP File Viewer (todd@jloop.com)');
		curl_setopt($ch, CURLOPT_USERPWD, 'tpastell:werttrew1');
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($ch);
		curl_close($ch);
		file_put_contents(WWW_ROOT.'/files/uploads/'.$id, $response);
	}
	
	public function createImages($id) {
		/* simpler to just convert it to jpg no matter what
		$mime_type = exec('file -bi '.WWW_ROOT.'/files/uploads/'.$id);
		if(strpos($mime_type, 'application/pdf') !== false) {
			exec("convert $id ");
		}
		elseif(strpos($mime_type, 'image') !== false) {
			
		}
		else {
			return false;
		}*/
		//exec('convert '.WWW_ROOT.'/files/uploads/'.$id.' -resize "1000x2000<" '.WWW_ROOT.'/files/uploads/'.$id.'.jpg');
		//exec('convert '.WWW_ROOT.'/files/uploads/'.$id.' -resize "x100" '.WWW_ROOT.'/files/uploads/'.$id.'.jpg');
		exec('convert '.WWW_ROOT.'/files/uploads/'.$id.' '.WWW_ROOT.'/files/uploads/'.$id.'.jpg');
		if($handle = opendir(WWW_ROOT.'/files/uploads/')) {
			while(($file = readdir($handle)) !== false) {
				if(preg_match('/^'.$id.'\.|^'.$id.'-/', $file)) {
					$this->Image->saveFromUpload($id, $file);
				}
			}
			closedir($handle);
		}
		
	}
	
}