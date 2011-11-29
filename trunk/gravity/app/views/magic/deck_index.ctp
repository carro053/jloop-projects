<h2>My Decks</h2>
<?php foreach($decks as $deck): ?>
<div><a href="/magic/deck_manage/<?php echo $deck['Deck']['id']; ?>"><?php echo $deck['Deck']['name']; ?></a></div>
<?php endforeach;
if(count($decks) == 0) echo '<div>You have no decks yet.</div>';
echo '<h2>Create New Deck</h2>';
echo $this->Form->create('Deck',array('url' => array('controller' => 'magic', 'action' => 'deck_create')));
echo $this->Form->input('Deck.name');
echo $this->Form->end('Create');
?>