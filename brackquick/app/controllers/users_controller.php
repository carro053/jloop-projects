<?php
class UsersController extends AppController {

    public $name = 'Users';    
    public $components = array('Auth');
    function beforeFilter() {
        $this->Auth->allow('hashMe');
    }

    
    public function login() {
    }

    public function logout() {
        $this->redirect($this->Auth->logout());
    }
    public function hashMe($username,$password) {
        $data['User']['username'] = $username;
    	$data['User']['password'] = $password;
    	$hashedPasswords = $this->Auth->hashPasswords($data);
    	pr($hashedPasswords);
    	exit();
    }

}
?>