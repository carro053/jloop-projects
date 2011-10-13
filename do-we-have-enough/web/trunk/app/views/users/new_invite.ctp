<?php echo $ajax->form('invite', 'post', array('url' => '/invite/'.$event['Event']['id'].'/'.$event['Event']['group_id'], 'update' => 'invite','class'=>'event')); ?>
<p style="color: #7DC3E1; margin-top:5px;">Want to invite someone else?</p>
<input name="data[NewUser][email]" id="NewUserEmail" class="med-small" type="text" value="Enter email address here" onfocus="javascript:if(this.value == 'Enter email address here') { this.value = ''; } the_side_tooltip(this,'Invite one email at a time to the event. Thanks.','new');" onblur="javascript:if(this.value == '') { this.value = 'Enter email address here'; }" onkeyup="validate_email(this.value);" style="margin-bottom:0px;" />
<div>
    <?php echo $form->checkbox('NewUser.to_group',array()); ?><label for="invite_to_group">Add this person to the group</label>
    <?php echo $form->submit('Invite',array('div'=>false,'label'=>false,'style'=>'cursor:pointer;display:none;','id'=>'add_to','name'=>'add_to','onclick'=>'return validate_submit_email();')); ?>
</div>
</form>