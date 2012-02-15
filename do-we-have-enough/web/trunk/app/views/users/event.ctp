<div id="main" class="clearfix">
	<?php
	$in = 0;
	$out = 0;
	$fifty = 0;
	$total = count($event['User']);
	foreach($event['User'] as $user):
		if($user['EventsUser']['status'] == 1) $in = $in + 1 + $user['EventsUser']['guests'];
		if($user['EventsUser']['status'] == 2) $out++;
		if($user['EventsUser']['status'] == 3) $fifty++;
	endforeach;
	?>
	<div class="container">
        <div class="left">
        	<h2>Event info for:<br />
            <span><?php echo $event['Event']['name']; ?></span></h2>
            <h2>When:<br />
            <span><?php echo $event['Event']['when'].', '.date('n/j/Y', strtotime($event['Event']['date'])); ?></span></h2>
            <?php if($event['Event']['where'] != '' && $event['Event']['where'] != 'Where is it?') { ?><h2>Where:<br />
            <span><?php echo $event['Event']['where']; ?></span></h2><?php } ?>
            <?php if($event['Event']['details'] != '') { ?>
            <p class="arrow"><a id="btn-event-details" href="">Event Details</a></p>
            <div id="event-details" style="margin-top:0px;width:300px;display:none;">
                	<p><?php echo nl2br($event['Event']['details']); ?></p>
            </div>
            <?php } ?>
            <?php if($in > 0) { ?>
        	<h2>Who we got so far!<br />
            <span><?php echo $in; ?> of <?php echo $event['Event']['need']; ?> needed.</span></h2>
            <ul class="in">
            	<?php foreach($event['User'] as $user):
                $name = $user['email'];
                if($user['name'] != '') $name = $user['name'];
                if($user['EventsUser']['status'] == 1)
                {
                    
                    if($user['EventsUser']['guests'] > 0)
                    {
                        echo '<li>'.$name.' +'.$user['EventsUser']['guests'].'</li>';
                    }else{
                        echo '<li>'.$name.'</li>';
                    }
                }
            	endforeach; ?>
            </ul>
            <?php }else{ ?>
            <h2>No one is in yet.<br />
            <span>We need at least <?php echo $event['Event']['need']; ?>.</span></h2>
            <?php } ?>
            <?php if($fifty > 0) { ?>
        	<h2>Who is 50/50?</h2>
            <ul class="in">
            	<?php foreach($event['User'] as $user):
                $name = $user['email'];
                if($user['name'] != '') $name = $user['name'];
                if($user['EventsUser']['status'] == 3)
                {
                    
                    if($user['EventsUser']['guests'] > 0)
                    {
                        echo '<li>'.$name.' +'.$user['EventsUser']['guests'].'</li>';
                    }else{
                        echo '<li>'.$name.'</li>';
                    }
                }
           		endforeach; ?>
            </ul>
            <?php } ?>
        </div><!-- end .left -->
        <div class="right">
            <?php 
            if($the_user['EventsUser']['status'] != 0) echo '<h2 style="margin-bottom:2px;">Your Status</h2>';
			if($the_user['EventsUser']['status'] == 1)
			{
				echo "<span class=\"yes-im-in-selected\"></span>";
				if($the_user['EventsUser']['guests'] > 0)
				{
					echo "<div class=\"plus-guest\">+".$the_user['EventsUser']['guests']."</div>";
				}
				if($event['Event']['cannot_bring_guests'] == 0)
				{	
					echo $ajax->link("", '/change_guests/1', array('update'=>'main','class'=>'button add-guest'));
					echo $ajax->link("", '/change_guests/0', array('update'=>'main','class'=>'button lose-guest'));
				}
			}
			if($the_user['EventsUser']['status'] == 2) echo "<span class=\"nope-im-out-selected\"></span>";
			if($the_user['EventsUser']['status'] == 3) echo "<span class=\"im-50-selected\"></span>"; ?>
			<?php
			if($the_user['EventsUser']['status'] == 0)
			{ ?>
			<?php echo $ajax->link("Yes! I'm in!", '/change_status/1', array('update'=>'main','class'=>'button yes-im-in')); ?>
			<?php echo $ajax->link("Nope. I'm out.", '/change_status/2', array('update'=>'main','class'=>'button nope-im-out')); ?>
			<?php echo $ajax->link("I'm 50/50.", '/change_status/3', array('update'=>'main','class'=>'button im-50')); ?>
			<?php }else{
			if($the_user['EventsUser']['status'] != 1) echo $ajax->link('', '/change_status/1', array('update'=>'main','class'=>'button actually-im-in'));
			if($the_user['EventsUser']['status'] != 2) echo $ajax->link("", '/change_status/2', array('update'=>'main','class'=>'button whoops-im-out'));
			if($the_user['EventsUser']['status'] != 3) echo $ajax->link("I'm 50/50.", '/change_status/3', array('update'=>'main','class'=>'button im-50'));
			}
			?>
        	<?php if($the_user['User']['name'] == '') 
			{ 
			echo $ajax->form('update_name', 'post', array('url' => '/update_name/'.$event['Event']['id'], 'update' => 'main','class'=>'event'));
			echo $form->hidden('User.id',array('value' => $the_user['User']['id']));
			echo $form->input('User.name',array('div'=>false,'label'=>false,'class'=>'med-small','value'=>'My name is...','onfocus'=>"javascript:if(this.value == 'My name is...') { this.value = ''; } the_side_tooltip(this,'Tell us your name so we can show it instead of your email.','name');",'onblur'=>"javascript:if(this.value == '') { this.value = 'My name is...'; }",'onkeyup'=>'if(this.value != "") { document.getElementById("save_name").style.display = ""; }else{ document.getElementById("save_name").style.display = "none"; }','style'=>'margin-bottom:0px;')).' '.$form->submit('Save Name',array('div'=>false,'label'=>false,'style'=>'cursor:pointer;display:none;','id'=>'save_name','name'=>'save_name')).'</form>';
			} ?>
        	<?php if($event['Event']['cannot_invite_others'] == 0) { ?>
        	<div id="invite">
				<?php echo $ajax->form('invite', 'post', array('url' => '/invite/'.$event['Event']['id'].'/'.$event['Event']['group_id'], 'update' => 'invite','class'=>'event')); ?>
                <p style="color: #7DC3E1; margin-top:5px;">Want to invite someone else?</p>
                <input name="data[NewUser][email]" id="NewUserEmail" class="med-small" type="text" value="Enter email address here" onfocus="javascript:if(this.value == 'Enter email address here') { this.value = ''; } the_side_tooltip(this,'Invite one email at a time to the event. Thanks.','new');" onblur="javascript:if(this.value == '') { this.value = 'Enter email address here'; }" onkeyup="validate_email(this.value);" style="margin-bottom:0px;" />
                <div>
                    <?php echo $form->checkbox('NewUser.to_group',array()); ?><label for="invite_to_group">Add this person to the group</label>
                    <?php echo $form->submit('Invite',array('div'=>false,'label'=>false,'style'=>'cursor:pointer;display:none;','id'=>'add_to','name'=>'add_to','onclick'=>'return validate_submit_email();')); ?>
                </div>
                </form>
            </div>
            <?php } ?>
        </div><!-- end .right -->
    </div><!-- end .container -->
    <div class="container">
    	<div class="left">
        	<p class="arrow"><a id="btn-invited" href=""><?php echo $total; ?> people were invited.</a></p>
            <div id="invited" style="display:none;">
                <ul>
                <?php foreach($event['User'] as $user):
                    echo '<li>';
                    if($user['name'] != '')
                    {
                        echo $user['name'];
                    }else{
                        echo $user['email'];
                    }
                    if($user['EventsUser']['status'] > 0) echo ' - ';
                    if($user['EventsUser']['status'] == 1) echo 'In';
                    if($user['EventsUser']['status'] == 2) echo 'Out';
                    if($user['EventsUser']['status'] == 3) echo '50/50';
                    if($user['EventsUser']['guests'] > 0) echo ' +'.$user['EventsUser']['guests'];
                    echo '</li>';
                    endforeach;
                    ?>
                </ul>
            </div>
        </div><!-- end .left -->
    	<div class="right">
            <p class="arrow"><a id="btn-additional-options" href="">Optional notification settings</a></p>
                <div id="additional-options" style="display:none;">
                    <?php echo $ajax->form('optional_settings', 'post', array('url' => '/update_settings/'.$event['Event']['id'], 'update' => 'additional-options'));
                    echo $form->hidden('User.id',array('value' => $the_user['User']['id'])); 
                    echo $form->hidden('EventsUser.id',array('value' => $the_user['EventsUser']['id']));
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
                </div>
            </div><!-- end .right -->
    </div><!-- end .container -->
</div>