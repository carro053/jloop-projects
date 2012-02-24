<pre><?php print_r($user); ?></pre>
<?php
if (count($user['Group']) > 0) {
?>or <?php 
$groups = array(0=>'Select a past group');
foreach($user['Group'] as $group):
	$groups[$group['id']] = $group['name'].' ('.(count($group['User'])+1).')';
endforeach;
echo $form->select('Group.id',$groups,null,array('onchange'=>'javascript:set_dummy_id(this.value);'),null); ?>
<?php echo $form->hidden('User.id',array('value'=>$user['User']['id'])); ?>
<? } ?>