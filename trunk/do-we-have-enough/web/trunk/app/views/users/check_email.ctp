<script type="text/javascript">
	var userGroups = <?php json_encode($user['Group']); ?>;
</script>
<?php
if (count($user['Group']) > 0) {
?>or <?php 
$groups = array(0=>'Select a past group');
foreach($user['Group'] as $group):
	$groups[$group['id']] = $group['name'].' ('.(count($group['User'])+1).')';
endforeach;
echo $form->select('Group.id',$groups,null,array('onchange'=>'javascript:set_dummy_id(this.value); javascript:alert(123);'),null); ?>
<?php echo $form->hidden('User.id',array('value'=>$user['User']['id'])); ?>
<? } ?>