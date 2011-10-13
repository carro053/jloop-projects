<?php echo $ajax->form('optional_settings', 'post', array('url' => '/update_settings/'.$event['Event']['id'], 'update' => 'additional-options'));
echo $form->hidden('User.id',array('value' => $this->data['User']['id'])); 
echo $form->hidden('EventsUser.id',array('value' => $this->data['EventsUser']['id']));
echo $form->hidden('EventsUser.event_id',array('value' => $this->data['EventsUser']['event_id'])); ?>
<?php echo $form->checkbox('User.notify_text',array('style'=>'width:15px;','onchange'=>'document.getElementById("save_settings").style.display = "";')); ?> Notify me by text <?php if($this->data['User']['cell_number'] != '') { echo '- <a onclick="javascript:pop_sms();" style="cursor: pointer; cursor: hand; text-decoration:none;">'.$this->data['User']['cell_number'].'</a>'; echo $form->hidden('User.cell_subscribed',array('value'=>1)); echo $form->hidden('User.cell_number'); }else{ echo $form->hidden('User.cell_subscribed',array('value'=>0)); ?><?php } ?><br />
<?php echo $form->checkbox('User.notify_in',array('style'=>'width:15px;','onchange'=>'document.getElementById("save_settings").style.display = "";')); ?> Every time someone is IN<br />
<?php echo $form->checkbox('User.notify_out',array('style'=>'width:15px;','onchange'=>'document.getElementById("save_settings").style.display = "";')); ?> Every time someone is OUT<br />
<?php echo $form->checkbox('EventsUser.notify_reach_checked',array('style'=>'width:15px;','onchange'=>'document.getElementById("save_settings").style.display = "";')); ?> When we reach <input class="tiny" name="data[EventsUser][notify_reach_count]" id="EventsUserNotifyReachCount" type="text" size="2" value="<?php if($this->data['EventsUser']['notify_reach_count'] > 0) { echo $this->data['EventsUser']['notify_reach_count']; } ?>" onkeypress="if(this.value > 0) { document.getElementById('save_settings').style.display = ''; }" /> people.<br />
<?php echo $form->submit('Save Changes',array('div'=>false,'label'=>false,'style'=>'cursor:pointer;display:none;','id'=>'save_settings','name'=>'save_settings','onclick'=>'return validate_phone();')); ?>
</form>