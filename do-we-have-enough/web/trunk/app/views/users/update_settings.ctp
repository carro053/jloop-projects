<?php echo $ajax->form('optional_settings', 'post', array('url' => '/update_settings/'.$event['Event']['id'], 'update' => 'additional-options'));
echo $form->hidden('User.id',array('value' => $this->data['User']['id'])); 
echo $form->hidden('EventsUser.id',array('value' => $this->data['EventsUser']['id']));
echo $form->hidden('EventsUser.event_id',array('value' => $this->data['EventsUser']['event_id'])); ?>

<p style="color: #7DC3E1; margin-top:5px;margin-bottom:0px;">Email me...</p>
<?php echo $form->checkbox('User.notify_event_change',array('style'=>'width:15px;','onchange'=>'document.getElementById("save_settings").style.display = "";')); ?> Event status updates.<br />
<?php echo $form->checkbox('User.notify_in',array('style'=>'width:15px;','onchange'=>'document.getElementById("save_settings").style.display = "";')); ?> Every time someone is IN<br />
<?php echo $form->checkbox('User.notify_out',array('style'=>'width:15px;','onchange'=>'document.getElementById("save_settings").style.display = "";')); ?> Every time someone is OUT<br />

<?php if(count($the_user['UserMobileDevice']) > 0) { ?>
<p style="color: #7DC3E1; margin-top:5px;margin-bottom:0px;">Send me a push notification...</p>
<?php echo $form->checkbox('User.app_notify_event_change',array('style'=>'width:15px;','onchange'=>'document.getElementById("save_settings").style.display = "";')); ?> For event status updates.<br />
<?php echo $form->checkbox('User.app_notify_in',array('style'=>'width:15px;','onchange'=>'document.getElementById("save_settings").style.display = "";')); ?> Every time someone is IN<br />
<?php echo $form->checkbox('User.app_notify_out',array('style'=>'width:15px;','onchange'=>'document.getElementById("save_settings").style.display = "";')); ?> Every time someone is OUT<br />
<?php } ?>
<br />
<?php echo $form->checkbox('EventsUser.notify_reach_checked',array('style'=>'width:15px;','onchange'=>'document.getElementById("save_settings").style.display = "";')); ?> When we reach <input class="tiny" name="data[EventsUser][notify_reach_count]" id="EventsUserNotifyReachCount" type="text" size="2" value="<?php if($this->data['EventsUser']['notify_reach_count'] > 0) { echo $this->data['EventsUser']['notify_reach_count']; } ?>" onkeypress="if(this.value > 0) { document.getElementById('save_settings').style.display = ''; }" /> people.<br />
<?php echo $form->submit('Save Changes',array('div'=>false,'label'=>false,'style'=>'cursor:pointer;display:none;','id'=>'save_settings','name'=>'save_settings','onclick'=>'return validate_phone();')); ?>
</form>