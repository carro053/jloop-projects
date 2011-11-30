<?php

class MagicController extends AppController {


	var $uses = array('Card','Deck','DeckCard','MagicGame','User');
	var $components = array('Auth');
	
	function deck_index()
	{
		$this->set('decks',$this->Deck->find('all',array('conditions'=>'Deck.user_id = '.$this->Auth->user('id'))));
	}
	
	function deck_create()
	{
		if(isset($this->data['Deck']['name']))
		{
			$this->Deck->create();
			$this->data['Deck']['user_id'] = $this->Auth->user('id');
			$this->Deck->save($this->data);
			$this->redirect('/magic/deck_manage/'.$this->Deck->id);
		}else{
			$this->redirect('/magic/deck_index');
		}
	}
	
	function deck_manage($deck_id)
	{
		$this->Deck->bindModel(array('hasMany'=>array('DeckCard'=>array('className'=>'DeckCard','foreign_key'=>'deck_id','order'=>'DeckCard.card_id ASC'))));
		$deck = $this->Deck->find('first',array('conditions'=>'Deck.id = '.$deck_id.' AND Deck.user_id = '.$this->Auth->user('id')));
		if(!isset($deck['Deck']['id'])) die('This is not your deck.');
		$this->set('deck',$deck);
		$this->set('cards',$this->Card->find('all',array('order'=>'Card.color ASC, Card.card_set_id ASC, Card.id ASC')));
	}
	
	function deck_add_card($deck_id,$card_id)
	{
		$this->DeckCard->create();
		$deck_card['DeckCard']['deck_id'] = $deck_id;
		$deck_card['DeckCard']['card_id'] = $card_id;
		$this->DeckCard->save($deck_card);
		$this->Deck->bindModel(array('hasMany'=>array('DeckCard'=>array('className'=>'DeckCard','foreign_key'=>'deck_id','order'=>'DeckCard.card_id ASC'))));
		$deck = $this->Deck->find('first',array('conditions'=>'Deck.id = '.$deck_id.' AND Deck.user_id = '.$this->Auth->user('id')));
		if(!isset($deck['Deck']['id'])) die('This is not your deck.');
		$this->set('deck',$deck);
		$this->layout = false;
		$this->render('deck_current_cards');
	}
	
	function deck_remove_card($deck_id,$card_id)
	{
		$deck_card = $this->DeckCard->find('first',array('conditions'=>'DeckCard.deck_id = '.$deck_id.' AND DeckCard.card_id = '.$card_id));
		if(isset($deck_card['DeckCard']['id']))
		{
			$this->DeckCard->delete($deck_card['DeckCard']['id']);
		}else{
			//echo 0;
		}
		$this->Deck->bindModel(array('hasMany'=>array('DeckCard'=>array('className'=>'DeckCard','foreign_key'=>'deck_id','order'=>'DeckCard.card_id ASC'))));
		$deck = $this->Deck->find('first',array('conditions'=>'Deck.id = '.$deck_id.' AND Deck.user_id = '.$this->Auth->user('id')));
		if(!isset($deck['Deck']['id'])) die('This is not your deck.');
		$this->set('deck',$deck);
		$this->layout = false;
		$this->render('deck_current_cards');
	}
	
	function game_index()
	{
		$this->set('games',$this->MagicGame->find('all',array('conditions'=>'MagicGame.user_1_id = '.$this->Auth->user('id').' OR MagicGame.user_2_id = '.$this->Auth->user('id'))));
		$this->set('decks',$this->Deck->find('all',array('conditions'=>'Deck.user_id = '.$this->Auth->user('id'))));
		$this->set('users',$this->User->find('all',array('conditions'=>'User.id != '.$this->Auth->user('id'))));
		$this->set('user_id',$this->Auth->user('id'));
	}
	
	function game_create()
	{
		if(isset($this->data['MagicGame']['user_1_deck_id']))
		{
			$this->MagicGame->create();
			$this->data['MagicGame']['user_1_id'] = $this->Auth->user('id');
			$this->MagicGame->save($this->data);
			$this->redirect('/magic/game_index');
		}else{
			die('How did you get here?');
		}
	}
	
	function game_start()
	{
		if(isset($this->data['MagicGame']['id']))
		{
			$turn = rand(0,1);
			$this->data['MagicGame']['turn'] = $turn;
			$this->MagicGame->save($this->data);
			$game = $this->MagicGame->findById($this->data['MagicGame']['id']);
			$this->MagicGame->query('UPDATE `deck_cards` SET `location` = "Library" WHERE `deck_id` = '.$game['MagicGame']['user_1_deck_id'].' OR `deck_id` = '.$game['MagicGame']['user_2_deck_id']);
			$this->MagicGame->query('UPDATE `deck_cards` SET `location` = "Hand" WHERE `deck_id` = '.$game['MagicGame']['user_1_deck_id'].' LIMIT 7 ORDER BY RAND()');
			$this->MagicGame->query('UPDATE `deck_cards` SET `location` = "Hand" WHERE `deck_id` = '.$game['MagicGame']['user_2_deck_id'].' LIMIT 7 ORDER BY RAND()');
			$this->redirect('/magic/game_battlefield/'.$this->data['MagicGame']['id']);
		}else{
			die('How did you get here?');
		}
	}
	
	function game_mulligan($game_id)
	{
		$game = $this->MagicGame->findById($game_id);
		if($this->Auth->user('id') == $game['MagicGame']['user_1_id'])
		{
			$deck_id = $game['MagicGame']['user_1_deck_id'];
		}else if($this->Auth->user('id') == $game['MagicGame']['user_2_id'])
		{
			$deck_id = $game['MagicGame']['user_2_deck_id'];
		}else{
			die('How did you get here?');
		}		
		$current_hand_count = $this->DeckCard->find('count',array('conditions'=>'DeckCard.deck_id = '.$deck_id.' AND DeckCard.location = "Hand"'));
		$this->MagicGame->query('UPDATE `deck_cards` SET `location` = "Library" WHERE `deck_id` = '.$deck_id);
		$this->MagicGame->query('UPDATE `deck_cards` SET `location` = "Hand" WHERE `deck_id` = '.$game['MagicGame']['user_1_deck_id'].' LIMIT '.($current_hand_count - 1).' ORDER BY RAND()');
		$this->redirect('/magic/game_hand/'.$game_id);
	}
	
	function game_hand($game_id)
	{
		$game = $this->MagicGame->findById($game_id);
		$this->set('game',$game);
		if($this->Auth->user('id') == $game['MagicGame']['user_1_id'])
		{
			$deck_id = $game['MagicGame']['user_1_deck_id'];
		}else if($this->Auth->user('id') == $game['MagicGame']['user_2_id'])
		{
			$deck_id = $game['MagicGame']['user_2_deck_id'];
		}else{
			die('How did you get here?');
		}
		$this->set('hand',$this->DeckCard->find('all',array('conditions'=>'DeckCard.deck_id = '.$deck_id.' AND DeckCard.location = "Hand"','order'=>'DeckCard.mana ASC')));
	}
	
	function game_battlefield($game_id)
	{
		$game = $this->MagicGame->findById($game_id);
		if($this->Auth->user('id') == $game['MagicGame']['user_1_id'])
		{
			$deck_id = $game['MagicGame']['user_1_deck_id'];
			$other_deck_id = $game['MagicGame']['user_2_deck_id'];
		}else if($this->Auth->user('id') == $game['MagicGame']['user_2_id'])
		{
			$deck_id = $game['MagicGame']['user_2_deck_id'];
			$other_deck_id = $game['MagicGame']['user_1_deck_id'];
		}else{
			die('How did you get here?');
		}
		$this->set('your_cards',$this->DeckCard->find('all',array('conditions'=>'DeckCard.deck_id = '.$deck_id.' AND DeckCard.location = "Battlefield"','order'=>'DeckCard.mana ASC')));
		$this->set('opponent_cards',$this->DeckCard->find('all',array('conditions'=>'DeckCard.deck_id = '.$other_deck_id.' AND DeckCard.location = "Battlefield"','order'=>'DeckCard.mana ASC')));
	}
	
	function game_end_turn($game_id)
	{
		$game = $this->MagicGame->findById($game_id);
		if($game['MagicGame']['turn'])
		{
			$game['MagicGame']['turn'] = 0;
			$deck_id = $game['MagicGame']['user_1_deck_id'];
		}else{
			$game['MagicGame']['turn'] = 1;
			$deck_id = $game['MagicGame']['user_2_deck_id'];
		}
		$this->MagicGame->query('UPDATE `deck_cards` SET `tapped` = 0 WHERE `deck_id` = '.$deck_id);
		$this->MagicGame->query('UPDATE `deck_cards` SET `location` = "Hand" WHERE `deck_id` = '.$deck_id.' AND `location` = "Library" LIMIT 1 ORDER BY RAND()');
		$this->MagicGame->save($game);
		$this->redirect('/magic/game_hand/'.$game_id);
	}
	
	function game_play_card($game_id,$deck_card_id)
	{
		$game = $this->MagicGame->findById($game_id);
		if($this->Auth->user('id') == $game['MagicGame']['user_1_id'])
		{
			$deck_id = $game['MagicGame']['user_1_deck_id'];
		}else if($this->Auth->user('id') == $game['MagicGame']['user_2_id'])
		{
			$deck_id = $game['MagicGame']['user_2_deck_id'];
		}else{
			die('How did you get here?');
		}
		$this->DeckCard->query('UPDATE `deck_cards` SET `location` = "Battlefield" WHERE `id` = '.$deck_card_id);
		$this->redirect('/magic/game_hand/'.$game_id);		
	}
	
	function game_discard_card($game_id,$deck_card_id)
	{
		$game = $this->MagicGame->findById($game_id);
		if($this->Auth->user('id') == $game['MagicGame']['user_1_id'])
		{
			$deck_id = $game['MagicGame']['user_1_deck_id'];
		}else if($this->Auth->user('id') == $game['MagicGame']['user_2_id'])
		{
			$deck_id = $game['MagicGame']['user_2_deck_id'];
		}else{
			die('How did you get here?');
		}
		$this->DeckCard->query('UPDATE `deck_cards` SET `location` = "Graveyard" WHERE `id` = '.$deck_card_id);
		$this->redirect('/magic/game_battlefield/'.$game_id);
	}
	
	function game_return_card_to_hand($game_id,$deck_card_id)
	{
		$game = $this->MagicGame->findById($game_id);
		if($this->Auth->user('id') == $game['MagicGame']['user_1_id'])
		{
			$deck_id = $game['MagicGame']['user_1_deck_id'];
		}else if($this->Auth->user('id') == $game['MagicGame']['user_2_id'])
		{
			$deck_id = $game['MagicGame']['user_2_deck_id'];
		}else{
			die('How did you get here?');
		}
		$this->DeckCard->query('UPDATE `deck_cards` SET `location` = "Hand" WHERE `id` = '.$deck_card_id);
		$this->redirect('/magic/game_battlefield/'.$game_id);
	}
	
	function game_tap_card($game_id,$deck_card_id)
	{
		$game = $this->MagicGame->findById($game_id);
		if($this->Auth->user('id') == $game['MagicGame']['user_1_id'])
		{
			$deck_id = $game['MagicGame']['user_1_deck_id'];
		}else if($this->Auth->user('id') == $game['MagicGame']['user_2_id'])
		{
			$deck_id = $game['MagicGame']['user_2_deck_id'];
		}else{
			die('How did you get here?');
		}
		$this->DeckCard->query('UPDATE `deck_cards` SET `tapped` = 1 WHERE `id` = '.$deck_card_id);
		$this->redirect('/magic/game_battlefield/'.$game_id);
	}
	
}

?>