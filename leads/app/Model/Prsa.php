<?php
App::uses('AppModel', 'Model');

class Prsa extends AppModel {

	public $belongsTo = array('Lead');
	
	/*
	public function afterSave($created) {
		if($created) {
			$this->Lead->create();
			$lead = array(
				'model' => $this->name,
				'model_id' => $this->id,
				'type' => $this->data['Lead']['type']
			);
			if(!$this->Lead->save($lead, false))
				die('An error has occurred while saving the lead for '.$this->name.', id: '.$this->id);
			if(!$this->saveField('lead_id', $this->Lead->id))
				die('An error has occurred while saving the lead_id for '.$this->name.', id: '.$this->id.', lead_id: '.$this->Lead->id);
		}
	}
	*/
}