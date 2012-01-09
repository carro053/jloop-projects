<div class="container">
    <form action="/save_event" method="post" class="event" id="create_event" name="create_event" enctype="multipart/form-data">
         <?php echo $form->hidden('Group.id',array('value'=>'0')); ?>
        <div>
        	<input name="data[User][email]" id="UserEmail" type="text" value="Your email" onfocus="javascript:if(this.value == 'Your email') { this.value = ''; } the_tooltip(this,'Enter your email address please. :)','email');" onblur="javascript:if(this.value == '') { this.value = 'Your email'; }else{ check_email(this.value); }" />
        </div>
        <div>
        	<input name="data[Event][name]" id="EventName" type="text" value="What is the event?" onfocus="javascript:if(this.value == 'What is the event?') { this.value = ''; } the_tooltip(this,'This is the event name, like \'Late Night Poker\'','name');" onblur="javascript:if(this.value == '') { this.value = 'What is the event?'; }" />
        </div>
        <div>
        	<input name="data[Event][when]" id="EventWhen" type="text" value="When is it?" onfocus="javascript:if(this.value == 'When is it?') { this.value = ''; } the_tooltip(this,'When is your event?<br />eg: \'11pm Tonight\'','when');" onblur="javascript:if(this.value == '') { this.value = 'When is it?'; }" />
        </div>
        
		<div>
        	<input name="data[Event][date]" id="EventDate" type="text" value="What is the date?" onfocus="javascript:if(this.value == 'What is the date?') { this.value = ''; }" onblur="javascript:if(this.value == '') { this.value = 'What is the date?'; }" />
        </div>
        
        <div>
        	<input name="data[Event][where]" id="EventWhere" type="text" value="Where is it?" onfocus="javascript:if(this.value == 'Where is it?') { this.value = ''; } the_tooltip(this,'Where is your event? eg: \'The Queen Mary\'','where');" onblur="javascript:if(this.value == '') { this.value = 'Where is it?'; }" />
        </div>
        <div>
        	<input name="data[Event][need]" id="EventNeed" type="text" value="How many people do you need?" onfocus="javascript:if(this.value == 'How many people do you need?') { this.value = ''; } the_tooltip(this,'How many people do you need?','need');" onblur="javascript:if(this.value == '') { this.value = 'How many people do you need?'; }" />
        </div>
        
        <p class="arrow"><a id="btn-options" href="">Choose some options</a>&nbsp;&nbsp;<a id="btn-details" href="">Add some details</a></p>
        <div id="details" class="tall-glass" style="display:none;">
        	<textarea name="data[Event][details]" id="EventDetails" class="tall-glass-of-water" onfocus="javascript:if(this.value == 'Tell me more...') { this.value = ''; } the_tooltip(this,'Any special details you would like to include about your event.','details');" onblur="javascript:if(this.value == '') { this.value = 'Tell me more...'; }">Tell me more...</textarea>
        </div>
        <div id="options" style="display:none;width:575px;">
            <div class="optionrow">
                <?php echo $form->checkbox('Event.cannot_invite_others'); ?>
                <label for="cannot_invite"><span>Users cannot invite others</span></label>
                <?php echo $form->checkbox('Event.cannot_bring_guests'); ?>
                <label for="cannot_bring"><span>Users cannot bring guests</span></label>
            </div>
            <div class="optionrow">
                <?php echo $form->checkbox('Event.cancel_email'); ?>
                <label style="margin-right: 10px;" for="cancel_event"><span>Cancel this event&nbsp;</span></label>
                <?php echo $form->select('Event.cancel_email_date',array('Today','Tomorrow','Another Date'),null,array('class'=>'select_date','onchange'=>'javascript:cancel_change(this.value);'),null); ?>
                <span>&nbsp;at </span>
                <input class="small" name="data[Event][cancel_email_time]" id="EventCancelEmailTime" type="text" onfocus="javascript:the_tooltip(this,'Simple like 12pm<br />or 9:30am','cancel');" />
                <span> if we haven't reached enough</span>
            </div>
            <div class="optionrow">
                <?php echo $form->checkbox('Event.status_email'); ?>
                <label style="margin-right: 10px;" for="send_status"><span>Send status email</span></label>
                <?php echo $form->select('Event.status_email_date',array('Today','Tomorrow','Another Date'),null,array('class'=>'select_date','onchange'=>'javascript:status_change(this.value);'),null); ?>
                <span>&nbsp;at </span>
                <input class="small" name="data[Event][status_email_time]" id="EventStatusEmailTime" type="text" onfocus="javascript:the_tooltip(this,'Simple like 12pm<br />or 9:30am','status');" />
                <span> if we haven't reached enough</span>
            </div>
            <div style="display:none;" id="cancel_date"><input class="w8em format-d-m-y divider-dash highlight-days-67 no-fade" id="dp-normal-1" name="dp-normal-1" type="hidden" /></div>
            <div style="display:none;" id="status_date"><input class="w8em format-d-m-y divider-dash highlight-days-67 no-fade" id="dp-normal-2" name="dp-normal-2" type="hidden" /></div>
            <div class="optionrow" align="center">
				<span>Timezone: </span>
            	<select name="data[Event][timezone]" id="EventTimezone">
				      <option value="-12.0">GMT -12:00</option>
				      <option value="-11.0">GMT -11:00</option>
				      <option value="-10.0">Hawaii</option>
				      <option value="-9.0">Alaska</option>
				      <option value="-8.0" selected="selected">Pacific</option>
				      <option value="-7.0">Mountain</option>
				      <option value="-6.0">Central</option>
				      <option value="-5.0">Eastern</option>
				      <option value="-4.0">GMT -4:00</option>
				      <option value="-3.5">GMT -3:30</option>
				      <option value="-3.0">GMT -3:00</option>
				      <option value="-2.0">GMT -2:00</option>
				      <option value="-1.0">GMT -1:00</option>
				      <option value="0.0">GMT</option>
				      <option value="1.0">GMT +1:00</option>
				      <option value="2.0">GMT +2:00</option>
				      <option value="3.0">GMT +3:00</option>
				      <option value="3.5">GMT +3:30</option>
				      <option value="4.0">GMT +4:00</option>
				      <option value="4.5">GMT +4:30</option>
				      <option value="5.0">GMT +5:00</option>
				      <option value="5.5">GMT +5:30</option>
				      <option value="5.75">GMT +5:45</option>
				      <option value="6.0">GMT +6:00</option>
				      <option value="7.0">GMT +7:00</option>
				      <option value="8.0">GMT +8:00</option>
				      <option value="9.0">GMT +9:00</option>
				      <option value="9.5">GMT +9:30</option>
				      <option value="10.0">GMT +10:00</option>
				      <option value="11.0">GMT +11:00</option>
				      <option value="12.0">GMT +12:00</option>
				</select>
            </div>
        </div><!-- end #options -->
        <div class="tall-glass"><textarea name="data[Group][list]" id="GroupList" class="tall-glass-of-water" onfocus="javascript:if(this.value == 'Who\'s invited?') { this.value = ''; } the_tooltip(this,'Separate emails by commas or returns.','list');" onblur="javascript:if(this.value == '') { this.value = 'Who\'s invited?'; }" onkeypress="javascript:if(this.value != '' && this.value != 'Who\'s invited?') name_group(this.value);">Who's invited?</textarea></div>
        <div class="indent" id="GroupNameParent" style="display:none;"><input name="data[Group][name]" id="GroupName" class="med-small" type="text" value="Name this group" onfocus="javascript:if(this.value == 'Name this group') { this.value = ''; } the_tooltip(this,'Name this group for future reference.','group');" onblur="javascript:if(this.value == '') { this.value = 'Name this group'; }" /></div>
        <input type="hidden" id="GroupDummyId" value="0" />
        <div id="previous_groups" class="indent"></div>
        <div class="formspacer">&nbsp;</div>
        <?php echo $form->submit('CREATE!',array('onclick'=>'return validate_form();','style'=>'cursor:pointer;','id'=>'create')); ?>
    </form>
    <?php echo $ajax->observeField('UserEmail',array('url' => array( 'controller' => 'users','action' => 'check_email'),'frequency' => 0.2,'update' => 'previous_groups')); ?>
</div><!-- end .container -->