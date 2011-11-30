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
		$this->MagicGame->bindModel(array('belongsTo'=>array('User_1'=>array('className'=>'User','foreign_key'=>'user_1_id'),'User_2'=>array('className'=>'User','foreign_key'=>'user_2_id'))));
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
			$this->MagicGame->query('UPDATE `deck_cards` SET `location` = "Hand" WHERE `deck_id` = '.$game['MagicGame']['user_1_deck_id'].' ORDER BY RAND() LIMIT 7');
			$this->MagicGame->query('UPDATE `deck_cards` SET `location` = "Hand" WHERE `deck_id` = '.$game['MagicGame']['user_2_deck_id'].' ORDER BY RAND() LIMIT 7');
			$this->redirect('/magic/game_index');
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
		$this->MagicGame->query('UPDATE `deck_cards` SET `location` = "Hand" WHERE `deck_id` = '.$game['MagicGame']['user_1_deck_id'].' ORDER BY RAND() LIMIT '.($current_hand_count - 1));
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
		$this->set('game_id',$game_id);
		$this->set('hand',$this->DeckCard->find('all',array('conditions'=>'DeckCard.deck_id = '.$deck_id.' AND DeckCard.location = "Hand"')));
	}
	
	function game_battlefield($game_id)
	{
		$this->MagicGame->bindModel(array('belongsTo'=>array('User_1'=>array('className'=>'User','foreign_key'=>'user_1_id'),'User_2'=>array('className'=>'User','foreign_key'=>'user_2_id'))));
		$game = $this->MagicGame->findById($game_id);
		if($this->Auth->user('id') == $game['MagicGame']['user_1_id'])
		{
			$this->set('your_number',1);
			$deck_id = $game['MagicGame']['user_1_deck_id'];
			$other_deck_id = $game['MagicGame']['user_2_deck_id'];
		}else if($this->Auth->user('id') == $game['MagicGame']['user_2_id'])
		{
			$this->set('your_number',2);
			$deck_id = $game['MagicGame']['user_2_deck_id'];
			$other_deck_id = $game['MagicGame']['user_1_deck_id'];
		}else{
			die('How did you get here?');
		}
		$this->set('game',$game);
		$this->set('your_cards',$this->DeckCard->find('all',array('conditions'=>'DeckCard.deck_id = '.$deck_id.' AND DeckCard.location = "Battlefield"')));
		$this->set('opponents_cards',$this->DeckCard->find('all',array('conditions'=>'DeckCard.deck_id = '.$other_deck_id.' AND DeckCard.location = "Battlefield"')));
	}
	
	function game_graveyard($game_id,$theirs)
	{
		$game = $this->MagicGame->findById($game_id);
		$this->set('game',$game);
		if($this->Auth->user('id') == $game['MagicGame']['user_1_id'])
		{
			if($theirs)
			{
				$deck_id = $game['MagicGame']['user_2_deck_id'];
			}else{
				$deck_id = $game['MagicGame']['user_1_deck_id'];
			}
		}else if($this->Auth->user('id') == $game['MagicGame']['user_2_id'])
		{
			if($theirs)
			{
				$deck_id = $game['MagicGame']['user_1_deck_id'];
			}else{
				$deck_id = $game['MagicGame']['user_2_deck_id'];
			}
		}else{
			die('How did you get here?');
		}
		$this->set('game_id',$game_id);
		$this->set('hand',$this->DeckCard->find('all',array('conditions'=>'DeckCard.deck_id = '.$deck_id.' AND DeckCard.location = "Graveyard"')));
		$this->set('theirs',$theirs);
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
		$this->MagicGame->query('UPDATE `deck_cards` SET `location` = "Hand" WHERE `deck_id` = '.$deck_id.' AND `location` = "Library" ORDER BY RAND() LIMIT 1');
		$this->MagicGame->save($game);
		$this->redirect('/magic/game_battlefield/'.$game_id);
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
		$this->DeckCard->query('UPDATE `deck_cards` SET `location` = "Battlefield", `tapped` = 0 WHERE `id` = '.$deck_card_id);
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
	
	function game_return_card_to_hand($game_id,$deck_card_id,$from_graveyard=null)
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
		if($from_graveyard == 1)
		{
			$this->redirect('/magic/game_graveyard/'.$game_id.'/0');
		}elseif($from_graveyard == 2)
		{
			$this->redirect('/magic/game_graveyard/'.$game_id.'/1');
		}else{
			$this->redirect('/magic/game_battlefield/'.$game_id);
		}
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
		$deck_card = $this->DeckCard->findById($deck_card_id);
		if($deck_card['DeckCard']['tapped'])
		{
			$deck_card['DeckCard']['tapped'] = 0;
		}else{
			$deck_card['DeckCard']['tapped'] = 1;
		}
		$this->DeckCard->save($deck_card);
		$this->redirect('/magic/game_battlefield/'.$game_id);
	}
	
}

?>