<?php

App::uses('AppController', 'Controller');
class LearnMoreItemsController extends AppController {
	public $name = 'LearnMoreItems';
	public $helpers = array('Html', 'Session');
	public $uses = array('LearnMoreItem');

	
	public function beforeFilter()
	{
	}	
	
	public function index()
	{
		$this->set('items',$this->LearnMoreItem->find('all',array('conditions'=>'LearnMoreItem.deleted = 0','order'=>'LearnMoreItem.label ASC')));
	}
	
	public function deleted()
	{
		$this->set('items',$this->LearnMoreItem->find('all',array('conditions'=>'LearnMoreItem.deleted = 1','order'=>'LearnMoreItem.label ASC')));
	}
	
	function add()
	{
		if(isset($this->data['LearnMoreItem']))
		{
			$learn_more_item = $this->data;
			if($this->LearnMoreItem->save($learn_more_item))
			{
				$this->redirect('/learn_more_items/index/');
			}
		}
	}
	
	function edit($learn_more_item_id)
	{
		$learn_more_item = $this->LearnMoreItem->findById($learn_more_item_id);
		if(isset($this->data['LearnMoreItem']))
		{
			$learn_more_item = $this->data;
			if($this->LearnMoreItem->save($learn_more_item))
			{
				$this->redirect('/learn_more_items/index/');
			}
		} else {
			$this->data = $learn_more_item;
		}
	}
	
	function delete($learn_more_item_id)
	{
		/*$learn_more_item = $this->LearnMoreItem->findById($learn_more_item_id);
		$learn_more_item['LearnMoreItem']['deleted'] = 1;
		$this->LearnMoreItem->save($learn_more_item);
		$this->version_up_learn_more_item($learn_more_item_id);
		$learn_more_items = $this->LearnMoreItem->find('all',array('conditions'=>'LearnMoreItem.game_id = '.$learn_more_item['LearnMoreItem']['game_id'].' AND LearnMoreItem.deleted = 0','order'=>'LearnMoreItem.order ASC'));
		foreach($learn_more_items as $i=>$learn_more_item):
			$learn_more_item['LearnMoreItem']['order'] = $i;
			$this->LearnMoreItem->save($learn_more_item);
		endforeach;
		$this->redirect('/learn_more_items/index/');*/
	}
	
	function undelete($learn_more_item_id)
	{
		$learn_more_item = $this->LearnMoreItem->findById($learn_more_item_id);
		$learn_more_item['LearnMoreItem']['deleted'] = 0;
		$learn_more_item['LearnMoreItem']['order'] = $this->LearnMoreItem->find('count',array('conditions'=>'LearnMoreItem.game_id = '.$learn_more_item['LearnMoreItem']['game_id'].' AND LearnMoreItem.deleted = 0'));
		$this->LearnMoreItem->save($learn_more_item);
		$this->version_up_learn_more_item($learn_more_item_id);
		$this->redirect('/learn_more_items/deleted/'.$learn_more_item['LearnMoreItem']['game_id']);
	}
}
?>