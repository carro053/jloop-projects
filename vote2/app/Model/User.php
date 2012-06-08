<?php
class User extends AppModel {
	
	public $validate = array(
		'username' => array(
			array(
				'rule' => 'alphaNumeric',
				'required' => true,
				'message' => 'Letters and numbers only'
			),
			array(
				'rule' => array('between', 3, 12),
				'message' => 'Between 3 and 12 characters'
			)
		),
		'password1' => array(
			'rule' => array('identical', 'password2'),
			'message' => 'Match passwords'
		)
	);
	
	public function beforeSave() {
	    if(isset($this->data[$this->alias]['password'])) {
	        $this->data[$this->alias]['password'] = AuthComponent::password($this->data[$this->alias]['password']);
	    }
	    return true;
	}
}