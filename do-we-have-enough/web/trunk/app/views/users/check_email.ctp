<script type="text/javascript">
	var userGroups = <?php echo json_encode($user['Group']); ?>;
	
	function populateTextAreaWithEmails(group_id) {
		var groupList = document.getElementById('GroupList');
		groupList.innerHTML = '';
		groupList.value = '';
		for(var n in userGroups) {
			if(userGroups[n].id == group_id) {
				//alert(userGroups[n].name);
				for(var m in userGroups[n].User) {
					//alert(userGroups[n].User[m].User.email);
					groupList.innerHTML += userGroups[n].User[m].User.email+"\r\n";
					groupList.value += userGroups[n].User[m].User.email+"\r\n";
				}
			}
		}
	}
</script>
<?php
if (count($user['Group']) > 0) {
?>or <?php 
$groups = array(0=>'Select a past group');
foreach($user['Group'] as $group):
	$groups[$group['id']] = $group['name'].' ('.(count($group['User'])+1).')';
endforeach;
echo $form->select('Group.id',$groups,null,array('onchange'=>'javascript:populateTextAreaWithEmails(this.value); javascript:set_dummy_id(this.value); '),null); ?>
<?php echo $form->hidden('User.id',array('value'=>$user['User']['id'])); ?>
<? } ?>
