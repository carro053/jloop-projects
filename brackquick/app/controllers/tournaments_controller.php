<?php
class TournamentsController extends AppController {

	public $name = 'Tournaments';
	public $helpers = array('Html', 'Session');
	public $uses = array('Match','Group','Tournament','User');
	
	private function rand_string($length)
	{
		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";	

		$size = strlen($chars);
		for($i=0;$i<$length;$i++)
		{
			$str .= $chars[rand(0,$size-1)];
		}
		return $str;
	}
	
	
	public function create()
	{
		$this->Group->bindModel(array('hasAndBelongsToMany'=>array('User'=>array('className'=>'User','joinTable'=>'groups_users','foreignKey'=>'group_id','associationForeignKey'=>'user_id'))));
		$this->set('group',$this->Group->find('first',array('conditions'=>'Group.id = 1','recursive'=>'1')));
	}
	
	
	
}
?>