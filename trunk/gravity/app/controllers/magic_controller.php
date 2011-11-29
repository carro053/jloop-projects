<?php

class MagicController extends AppController {


	var $uses = array('Card','Deck','DeckCard','MagicGame');
	var $components = array('Auth');
	
	function deck_index()
	{
		echo $this->Auth->user('id');
		pr($this->Auth);
		exit();
		$this->set('decks',$this->Deck->find('all',array('Deck.user_id = ')));
	}
	
}

?>