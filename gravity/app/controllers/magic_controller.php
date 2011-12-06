<?php

class MagicController extends AppController {


	var $uses = array('Card','Deck','DeckCard','MagicGame','MagicGameDeck','MagicGameDeckCard','User');
	var $components = array('Auth');
	
	function beforeFilter()
 	{
 		$this->Auth->allow('game_spectate','game_refresh_spectate');
 		parent::beforeFilter();
 	}
	
	function deck_index()
	{
		$this->set('decks',$this->Deck->find('all',array('conditions'=>'Deck.user_id = '.$this->Auth->user('id'))));
		$this->Deck->bindModel(array('belongsTo'=>array('User'=>array('className'=>'User','foreign_key'=>'user_id'))));
		$this->set('others_decks',$this->Deck->find('all',array('conditions'=>'Deck.user_id != '.$this->Auth->user('id'))));
	}
	
	function deck_view($deck_id)
	{
		$this->Deck->bindModel(array('hasMany'=>array('DeckCard'=>array('className'=>'DeckCard','foreign_key'=>'deck_id','order'=>'DeckCard.card_id ASC'))));
		$deck = $this->Deck->find('first',array('conditions'=>'Deck.id = '.$deck_id));
		if(!isset($deck['Deck']['id'])) die('This deck doesn\'t exist.');
		$this->set('deck',$deck);
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
		$this->set('cards',$this->Card->find('all',array('order'=>'Card.mana DESC, Card.color ASC, Card.card_set_id ASC, Card.id ASC')));
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
		$this->Deck->bindModel(array('belongsTo'=>array('User'=>array('className'=>'User','foreign_key'=>'user_id'))));
		$this->set('decks',$this->Deck->find('all',array()));
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
			$this->Deck->bindModel(array('hasMany'=>array('DeckCard'=>array('className'=>'DeckCard','foreign_key'=>'deck_id','order'=>'DeckCard.card_id ASC'))));
			$deck_1 = $this->Deck->find('first',array('conditions'=>'Deck.id = '.$game['MagicGame']['user_1_deck_id']));
			$this->MagicGameDeck->create();
			$deck_1['Deck']['id'] = null;
			$this->MagicGameDeck->save($deck_1['Deck']);
			$new_deck_1_id = $this->MagicGameDeck->id;
			foreach($deck_1['DeckCard'] as $deck_card):
				$this->MagicGameDeckCard->create();
				$deck_card['id'] = null;
				$deck_card['magic_game_deck_id'] = $new_deck_1_id;
				$this->MagicGameDeckCard->save($deck_card);
			endforeach;
			$game['MagicGame']['user_1_deck_id'] = $new_deck_1_id;
			$this->Deck->bindModel(array('hasMany'=>array('DeckCard'=>array('className'=>'DeckCard','foreign_key'=>'deck_id','order'=>'DeckCard.card_id ASC'))));
			$deck_2 = $this->Deck->find('first',array('conditions'=>'Deck.id = '.$game['MagicGame']['user_2_deck_id']));
			$this->MagicGameDeck->create();
			$deck_2['Deck']['id'] = null;
			$this->MagicGameDeck->save($deck_2['Deck']);
			$new_deck_2_id = $this->MagicGameDeck->id;
			foreach($deck_2['DeckCard'] as $deck_card):
				$this->MagicGameDeckCard->create();
				$deck_card['id'] = null;
				$deck_card['magic_game_deck_id'] = $new_deck_2_id;
				$this->MagicGameDeckCard->save($deck_card);
			endforeach;
			$game['MagicGame']['user_2_deck_id'] = $new_deck_2_id;
			$this->MagicGame->save($game);
			$this->MagicGame->query('UPDATE `magic_game_deck_cards` SET `location` = "Library" WHERE `magic_game_deck_id` = '.$game['MagicGame']['user_1_deck_id'].' OR `magic_game_deck_id` = '.$game['MagicGame']['user_2_deck_id']);
			$this->MagicGame->query('UPDATE `magic_game_deck_cards` SET `location` = "Hand" WHERE `magic_game_deck_id` = '.$game['MagicGame']['user_1_deck_id'].' ORDER BY RAND() LIMIT 7');
			$this->MagicGame->query('UPDATE `magic_game_deck_cards` SET `location` = "Hand" WHERE `magic_game_deck_id` = '.$game['MagicGame']['user_2_deck_id'].' ORDER BY RAND() LIMIT 7');
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
		$current_hand_count = $this->MagicGameDeckCard->find('count',array('conditions'=>'MagicGameDeckCard.magic_game_deck_id = '.$deck_id.' AND MagicGameDeckCard.location = "Hand"'));
		$this->MagicGame->query('UPDATE `magic_game_deck_cards` SET `location` = "Library" WHERE `magic_game_deck_id` = '.$deck_id);
		$this->MagicGame->query('UPDATE `magic_game_deck_cards` SET `location` = "Hand" WHERE `magic_game_deck_id` = '.$deck_id.' ORDER BY RAND() LIMIT '.($current_hand_count - 1));
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
		$this->set('hand',$this->MagicGameDeckCard->find('all',array('conditions'=>'MagicGameDeckCard.magic_game_deck_id = '.$deck_id.' AND MagicGameDeckCard.location = "Hand"')));
	}
	
	function game_refresh_hand($game_id)
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
		$this->set('hand',$this->MagicGameDeckCard->find('all',array('conditions'=>'MagicGameDeckCard.magic_game_deck_id = '.$deck_id.' AND MagicGameDeckCard.location = "Hand"')));
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
		$this->MagicGameDeckCard->bindModel(array('belongsTo'=>array('Card'=>array('className'=>'Card','foreign_key'=>'card_id'))),false);
		$this->set('your_mana',$this->MagicGameDeckCard->find('all',array('conditions'=>'MagicGameDeckCard.magic_game_deck_id = '.$deck_id.' AND MagicGameDeckCard.location = "Battlefield" AND Card.mana = 1')));
		$this->set('your_cards',$this->MagicGameDeckCard->find('all',array('conditions'=>'MagicGameDeckCard.magic_game_deck_id = '.$deck_id.' AND MagicGameDeckCard.location = "Battlefield" AND Card.mana = 0')));
		$this->set('opponents_mana',$this->MagicGameDeckCard->find('all',array('conditions'=>'MagicGameDeckCard.magic_game_deck_id = '.$other_deck_id.' AND MagicGameDeckCard.location = "Battlefield" AND Card.mana = 1')));
		$this->set('opponents_cards',$this->MagicGameDeckCard->find('all',array('conditions'=>'MagicGameDeckCard.magic_game_deck_id = '.$other_deck_id.' AND MagicGameDeckCard.location = "Battlefield" AND Card.mana = 0')));
		$this->set('opponents_hand',$this->MagicGameDeckCard->find('count',array('conditions'=>'MagicGameDeckCard.magic_game_deck_id = '.$other_deck_id.' AND MagicGameDeckCard.location = "Hand"')));
	}
	
	function game_refresh_battlefield($game_id)
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
		$this->MagicGameDeckCard->bindModel(array('belongsTo'=>array('Card'=>array('className'=>'Card','foreign_key'=>'card_id'))),false);
		$this->set('your_mana',$this->MagicGameDeckCard->find('all',array('conditions'=>'MagicGameDeckCard.magic_game_deck_id = '.$deck_id.' AND MagicGameDeckCard.location = "Battlefield" AND Card.mana = 1')));
		$this->set('your_cards',$this->MagicGameDeckCard->find('all',array('conditions'=>'MagicGameDeckCard.magic_game_deck_id = '.$deck_id.' AND MagicGameDeckCard.location = "Battlefield" AND Card.mana = 0')));
		$this->set('opponents_mana',$this->MagicGameDeckCard->find('all',array('conditions'=>'MagicGameDeckCard.magic_game_deck_id = '.$other_deck_id.' AND MagicGameDeckCard.location = "Battlefield" AND Card.mana = 1')));
		$this->set('opponents_cards',$this->MagicGameDeckCard->find('all',array('conditions'=>'MagicGameDeckCard.magic_game_deck_id = '.$other_deck_id.' AND MagicGameDeckCard.location = "Battlefield" AND Card.mana = 0')));
		$this->set('opponents_hand',$this->MagicGameDeckCard->find('count',array('conditions'=>'MagicGameDeckCard.magic_game_deck_id = '.$other_deck_id.' AND MagicGameDeckCard.location = "Hand"')));
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
		$this->set('hand',$this->MagicGameDeckCard->find('all',array('conditions'=>'MagicGameDeckCard.magic_game_deck_id = '.$deck_id.' AND MagicGameDeckCard.location = "Graveyard"')));
		$this->set('theirs',$theirs);
	}
	
	function game_refresh_graveyard($game_id,$theirs)
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
		$this->set('hand',$this->MagicGameDeckCard->find('all',array('conditions'=>'MagicGameDeckCard.magic_game_deck_id = '.$deck_id.' AND MagicGameDeckCard.location = "Graveyard"')));
		$this->set('theirs',$theirs);
	}
	
	function game_library($game_id,$theirs)
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
		$this->set('hand',$this->MagicGameDeckCard->find('all',array('conditions'=>'MagicGameDeckCard.magic_game_deck_id = '.$deck_id.' AND MagicGameDeckCard.location = "library"')));
		$this->set('theirs',$theirs);
	}
	
	function game_refresh_library($game_id,$theirs)
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
		$this->set('hand',$this->MagicGameDeckCard->find('all',array('conditions'=>'MagicGameDeckCard.magic_game_deck_id = '.$deck_id.' AND MagicGameDeckCard.location = "Library"')));
		$this->set('theirs',$theirs);
	}
	
	function game_spectate($game_id)
	{
		$this->MagicGame->bindModel(array('belongsTo'=>array('User_1'=>array('className'=>'User','foreign_key'=>'user_1_id'),'User_2'=>array('className'=>'User','foreign_key'=>'user_2_id'))));
		$game = $this->MagicGame->findById($game_id);
		$this->set('game',$game);
		$this->MagicGameDeckCard->bindModel(array('belongsTo'=>array('Card'=>array('className'=>'Card','foreign_key'=>'card_id'))),false);
		$this->set('player_1_mana',$this->MagicGameDeckCard->find('all',array('conditions'=>'MagicGameDeckCard.magic_game_deck_id = '.$game['MagicGame']['user_1_deck_id'].' AND MagicGameDeckCard.location = "Battlefield" AND Card.mana = 1')));
		$this->set('player_1_cards',$this->MagicGameDeckCard->find('all',array('conditions'=>'MagicGameDeckCard.magic_game_deck_id = '.$game['MagicGame']['user_1_deck_id'].' AND MagicGameDeckCard.location = "Battlefield" AND Card.mana = 0')));
		$this->set('player_1_hand',$this->MagicGameDeckCard->find('count',array('conditions'=>'MagicGameDeckCard.magic_game_deck_id = '.$game['MagicGame']['user_1_deck_id'].' AND MagicGameDeckCard.location = "Hand"')));
		$this->set('player_2_mana',$this->MagicGameDeckCard->find('all',array('conditions'=>'MagicGameDeckCard.magic_game_deck_id = '.$game['MagicGame']['user_2_deck_id'].' AND MagicGameDeckCard.location = "Battlefield" AND Card.mana = 1')));
		$this->set('player_2_cards',$this->MagicGameDeckCard->find('all',array('conditions'=>'MagicGameDeckCard.magic_game_deck_id = '.$game['MagicGame']['user_2_deck_id'].' AND MagicGameDeckCard.location = "Battlefield" AND Card.mana = 0')));
		$this->set('player_2_hand',$this->MagicGameDeckCard->find('count',array('conditions'=>'MagicGameDeckCard.magic_game_deck_id = '.$game['MagicGame']['user_2_deck_id'].' AND MagicGameDeckCard.location = "Hand"')));
	}
	
	function game_refresh_spectate($game_id)
	{
		$this->MagicGame->bindModel(array('belongsTo'=>array('User_1'=>array('className'=>'User','foreign_key'=>'user_1_id'),'User_2'=>array('className'=>'User','foreign_key'=>'user_2_id'))));
		$game = $this->MagicGame->findById($game_id);
		$this->set('game',$game);
		$this->MagicGameDeckCard->bindModel(array('belongsTo'=>array('Card'=>array('className'=>'Card','foreign_key'=>'card_id'))),false);
		$this->set('player_1_mana',$this->MagicGameDeckCard->find('all',array('conditions'=>'MagicGameDeckCard.magic_game_deck_id = '.$game['MagicGame']['user_1_deck_id'].' AND MagicGameDeckCard.location = "Battlefield" AND Card.mana = 1')));
		$this->set('player_1_cards',$this->MagicGameDeckCard->find('all',array('conditions'=>'MagicGameDeckCard.magic_game_deck_id = '.$game['MagicGame']['user_1_deck_id'].' AND MagicGameDeckCard.location = "Battlefield" AND Card.mana = 0')));
		$this->set('player_1_hand',$this->MagicGameDeckCard->find('count',array('conditions'=>'MagicGameDeckCard.magic_game_deck_id = '.$game['MagicGame']['user_1_deck_id'].' AND MagicGameDeckCard.location = "Hand"')));
		$this->set('player_2_mana',$this->MagicGameDeckCard->find('all',array('conditions'=>'MagicGameDeckCard.magic_game_deck_id = '.$game['MagicGame']['user_2_deck_id'].' AND MagicGameDeckCard.location = "Battlefield" AND Card.mana = 1')));
		$this->set('player_2_cards',$this->MagicGameDeckCard->find('all',array('conditions'=>'MagicGameDeckCard.magic_game_deck_id = '.$game['MagicGame']['user_2_deck_id'].' AND MagicGameDeckCard.location = "Battlefield" AND Card.mana = 0')));
		$this->set('player_2_hand',$this->MagicGameDeckCard->find('count',array('conditions'=>'MagicGameDeckCard.magic_game_deck_id = '.$game['MagicGame']['user_2_deck_id'].' AND MagicGameDeckCard.location = "Hand"')));
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
		$this->MagicGame->query('UPDATE `magic_game_deck_cards` SET `tapped` = 0 WHERE `magic_game_deck_id` = '.$deck_id);
		$this->MagicGame->query('UPDATE `magic_game_deck_cards` SET `location` = "Hand" WHERE `magic_game_deck_id` = '.$deck_id.' AND `location` = "Library" ORDER BY RAND() LIMIT 1');
		$this->MagicGame->save($game);
		$this->redirect('/magic/game_battlefield/'.$game_id);
	}
	
	function game_give_card_to_opponent($game_id)
	{
		$game = $this->MagicGame->findById($game_id);
		if($this->Auth->user('id') == $game['MagicGame']['user_1_id'])
		{
			$deck_id = $game['MagicGame']['user_2_deck_id'];
		}else if($this->Auth->user('id') == $game['MagicGame']['user_2_id'])
		{
			$deck_id = $game['MagicGame']['user_1_deck_id'];
		}else{
			die('How did you get here?');
		}
		$this->MagicGame->query('UPDATE `magic_game_deck_cards` SET `location` = "Hand" WHERE `magic_game_deck_id` = '.$deck_id.' AND `location` = "Library" ORDER BY RAND() LIMIT 1');
		echo 1;
		exit();
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
		$this->MagicGameDeckCard->query('UPDATE `magic_game_deck_cards` SET `location` = "Battlefield", `tapped` = 0 WHERE `id` = '.$deck_card_id);
		echo 1;
		exit();		
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
		$this->MagicGameDeckCard->query('UPDATE `magic_game_deck_cards` SET `location` = "Graveyard" WHERE `id` = '.$deck_card_id);
		echo 1;
		exit();
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
		$this->MagicGameDeckCard->query('UPDATE `magic_game_deck_cards` SET `location` = "Hand" WHERE `id` = '.$deck_card_id);
		echo 1;
		exit();
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
		$deck_card = $this->MagicGameDeckCard->findById($deck_card_id);
		if($deck_card['MagicGameDeckCard']['tapped'])
		{
			$deck_card['MagicGameDeckCard']['tapped'] = 0;
			echo 1;
		}else{
			$deck_card['MagicGameDeckCard']['tapped'] = 1;
			echo 0.4;
		}
		$this->MagicGameDeckCard->save($deck_card);
		exit();
	}
	
	function game_raise_health($game_id)
	{
		$game = $this->MagicGame->findById($game_id);
		if($this->Auth->user('id') == $game['MagicGame']['user_1_id'])
		{
			$game['MagicGame']['user_1_hp']++;
		}else if($this->Auth->user('id') == $game['MagicGame']['user_2_id'])
		{
			$game['MagicGame']['user_2_hp']++;
		}else{
			die('How did you get here?');
		}
		$this->MagicGame->save($game);
		echo 1;
		exit();
	}
	
	function game_lower_health($game_id)
	{
		$game = $this->MagicGame->findById($game_id);
		if($this->Auth->user('id') == $game['MagicGame']['user_1_id'])
		{
			$game['MagicGame']['user_1_hp']--;
			if($game['MagicGame']['user_1_hp'] == 0) $game['MagicGame']['winner_id'] = 2;
		}else if($this->Auth->user('id') == $game['MagicGame']['user_2_id'])
		{
			$game['MagicGame']['user_2_hp']--;
			if($game['MagicGame']['user_2_hp'] == 0) $game['MagicGame']['winner_id'] = 1;
		}else{
			die('How did you get here?');
		}
		$this->MagicGame->save($game);
		echo 1;
		exit();
	}
	
	function odds_of_mana($draws = 14,$need_at_least = 6,$number_of_mana = 25)
	{
		$got_at_least = 0;
		for($i=0;$i<10000;$i++)
		{
			$got_mana = 0;
			for($k=0;$k<$draws;$k++)
			{
				if(rand(1,60 - $k) <= $number_of_mana - $got_mana) $got_mana++;
			}
			if($got_mana >= $need_at_least) $got_at_least++;
		}
		echo $got_at_least/100;		
	}
	
}

?>