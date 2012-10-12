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
		$learn_more_item = $this->LearnMoreItem->findById($learn_more_item_id);
		$learn_more_item['LearnMoreItem']['deleted'] = 1;
		$this->LearnMoreItem->save($learn_more_item);
		$this->redirect('/learn_more_items/index/');
	}
	
	function undelete($learn_more_item_id)
	{
		$learn_more_item = $this->LearnMoreItem->findById($learn_more_item_id);
		$learn_more_item['LearnMoreItem']['deleted'] = 0;
		$this->LearnMoreItem->save($learn_more_item);
		$this->redirect('/learn_more_items/deleted/');
	}
	
	function cron_it()
	{
		$cards = array('ace','king','queen','jack','ten','nine','eight','seven','six','five','four','three','two');
		$suits = array('clubs','diamonds','hearts','spades');
		
		foreach($cards as $card):
		
			foreach($suits as $suit):
				$this->LearnMoreItem->create();
				$item['LearnMoreItem']['id'] = null;
				$item['LearnMoreItem']['label'] = ucwords($card).' of '.ucwords($suit);
				$item['LearnMoreItem']['url'] = 'https://nve.play-learn-win.com/the-deck/'.$card.'-of-'.$suit;
				$this->LearnMoreItem->save($item);
			endforeach;
		endforeach;
		exit;
		
	}
	
}
?>