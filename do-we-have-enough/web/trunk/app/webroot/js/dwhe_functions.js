// JavaScript Document
var which_element;
function validate_email(value)
{
	var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
	if(reg.test(value) != false) {
		document.getElementById("add_to").style.display = "";
	}else{
		document.getElementById("add_to").style.display = "none";
	}
}
function validate_submit_email()
{
	var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
	if(reg.test(document.getElementById("NewUserEmail").value) == false) {
		alert('Please enter a valid email address.');
		document.getElementById("NewUserEmail").focus();
		return false;
	}
	return true;
}
function toggle_options(whichLayer)
{
	if (document.getElementById)
	{
		// this is the way the standards work
		var style2 = document.getElementById(whichLayer).style;
		if(style2.display == 'none')
		{
			style2.display = "";
		}else{
			style2.display = "none";
		}
	}else if (document.all)
	{
		// this is the way old msie versions work
		var style2 = document.all[whichLayer].style;
		if(style2.display == 'none')
		{
			style2.display = "";
		}else{
			style2.display = "none";
		}
	}else if (document.layers)
	{
		// this is the way nn4 works
		var style2 = document.layers[whichLayer].style;
		if(style2.display == 'none')
		{
			style2.display = "";
		}else{
			style2.display = "none";
		}
	}
}
function name_group(group_list)
{
	if (document.getElementById)
	{
		// this is the way the standards work
		var style2 = document.getElementById('GroupNameParent').style;
		style2.display = "";
	}else if (document.all)
	{
		// this is the way old msie versions work
		var style2 = document.all['GroupNameParent'].style;
		style2.display = "";
	}else if (document.layers)
	{
		// this is the way nn4 works
		var style2 = document.layers['GroupNameParent'].style;
		style2.display = "";
	}
}
function pop_sms()
{
	var day = new Date();
	var time = day.getTime();
	eval("page" + time + " = window.open('/subscribe_sms', '" + time + "', 'toolbar=0,scrollbars=0,location=0,statusbar=0,menubar=0,resizable=0,width=900,height=300,left = 190,top = 362');");
}
	
function validate_phone()
{		
	id = 'UserCellSubscribed';
	if (document.getElementById)
		var subscribed = document.getElementById(id);
	else if (document.all)
		var subscribed = document.all[id];
	else if (document.layers)
		var subscribed = document.layers[id];
	id = 'UserNotifyText';
	if (document.getElementById)
		var text = document.getElementById(id);
	else if (document.all)
		var text = document.all[id];
	else if (document.layers)
		var text = document.layers[id];
	var myRegExpPhoneNumber = /\d\d\d-\d\d\d-\d\d\d\d/;
	if(text.checked)
	{
		if(subscribed.value == 0)
		{
			var day = new Date();
			var time = day.getTime();
			eval("page" + time + " = window.open('/subscribe_sms', '" + time + "', 'toolbar=0,scrollbars=0,location=0,statusbar=0,menubar=0,resizable=0,width=900,height=300,left = 190,top = 362');");
		}
	}
	return true;
}
function cancel_change(value)
{
	if(value == 2)
	{
		which_element = 'EventCancelEmailDate';
		tb_show('', '#TB_inline?height=260&width=230&inlineId=cancel_date&modal=true', null);
	}
}
function status_change(value)
{
	if(value == 2)
	{
		which_element = 'EventStatusEmailDate';
		tb_show('', '#TB_inline?height=260&width=230&inlineId=status_date&modal=true', null);
	}
}
function select_date()
{
	which_element = 'EventDate';
	tb_show('', '#TB_inline?height=260&width=230&inlineId=event_date&modal=true', null);
}
function am_or_pm(object)
{
	if(object.value != '')
	{
		if(object.value.search(/am|pm/i) < 0)
		{
			return true
		}
	}
	return false;
}
function set_dummy_id(id_value)
{
	id = 'create_event';
	if (document.getElementById)
		var returnVar = document.getElementById(id);
	else if (document.all)
		var returnVar = document.all[id];
	else if (document.layers)
		var returnVar = document.layers[id];
	if(id_value > 0)
	{
		returnVar.GroupList.disabled = 'disabled';
		returnVar.GroupName.disabled = 'disabled';
		returnVar.GroupList.setAttribute("class", "tall-glass-of-water-disabled");
		returnVar.GroupName.setAttribute("class", "med-small-disabled");
	}else{
		returnVar.GroupList.disabled = '';
		returnVar.GroupName.disabled = '';
		returnVar.GroupList.setAttribute("class", "tall-glass-of-water");
		returnVar.GroupName.setAttribute("class", "med-small");
	}
	returnVar.GroupDummyId.value = id_value;
}
function validate_form()
{
	$("div.errortip").remove();
	var valid = 1;
	id = 'create_event';
	if (document.getElementById)
		var returnVar = document.getElementById(id);
	else if (document.all)
		var returnVar = document.all[id];
	else if (document.layers)
		var returnVar = document.layers[id];
		
			
	var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
	if(reg.test(returnVar.UserEmail.value) == false) {
		$(returnVar.UserEmail).after("<div class='errortip' id='erroremail'>We need a valid email address from you!</div>");
		//returnVar.UserEmail.focus();
		valid = 0;
	}
	
	if (returnVar.EventName.value == "" || returnVar.EventName.value == "What is the event?")
	{
		$(returnVar.EventName).after("<div class='errortip' id='errorname'>Can't have an event without a name!</div>");
		valid = 0;
	}
	if (returnVar.EventDate.value == "" || returnVar.EventDate.value == "What is the date?")
	{
		$(returnVar.EventDate).after("<div class='errortip' id='errorname'>Can't have an event without a date!</div>");
		valid = 0;
	}
	if (returnVar.EventWhen.value == "" || returnVar.EventWhen.value == "When is it?")
	{
		$(returnVar.EventWhen).after("<div class='errortip' id='errorwhen'>When is your event?</div>");
		valid = 0;
	}
	if (returnVar.EventWhere.value == "" || returnVar.EventWhere.value == "Where is it?")
	{
		$(returnVar.EventWhere).after("<div class='errortip' id='errorwhere'>Where is your event?</div>");
		valid = 0;
	}
	if (!(returnVar.EventNeed.value > -1) )
	{
		$(returnVar.EventNeed).after("<div class='errortip' id='errorneed'>How many people do you need?<br />(Give us a number!)</div>");
		valid = 0;
	}
	if(returnVar.GroupDummyId.value > 0)
	{
		
	}else{
		if (returnVar.GroupList.value == "" || returnVar.GroupList.value == "Who's invited?")
		{
			$(returnVar.GroupList).after("<div class='errortip' id='errorlist'>Who's invited?</div>");
			valid = 0;
		}else{
			var i = 0;
			var j = 0;
			var emails = returnVar.GroupList.value.split(',');
			for (i = 0; i < emails.length; i++) {
				var secondemails = emails[i].split('\n');
				for (j = 0; j < secondemails.length; j++) {
					if(secondemails[j].replace(/^\s*(.*?)\s*$/,"$1") != '')
					{
						if(reg.test(secondemails[j].replace(/^\s*(.*?)\s*$/,"$1")) == false) {
							$(returnVar.GroupList).after("<div class='errortip' id='errorlist'>Please check your list, emails only!</div>");
							valid = 0;
						}
					}
				}
			}	
		}
	}
	if (returnVar.EventCancelEmail.checked && returnVar.EventCancelEmailTime.value == "")
	{
		$(returnVar.EventCancelEmail).after("<div class='errortip' id='errorcancel'>At what time do you want the cancelation email to be sent?</div>");
		valid = 0;
	}else{
		if (returnVar.EventCancelEmail.checked && am_or_pm(returnVar.EventCancelEmailTime))
		{
			$(returnVar.EventCancelEmail).after("<div class='errortip' id='errorcancel'>Is the cancelation email time AM or PM?</div>");
			valid = 0;
		}
	}
	if (returnVar.EventStatusEmail.checked && returnVar.EventStatusEmailTime.value == "")
	{
		$(returnVar.EventStatusEmail).after("<div class='errortip' id='errorstatus'>At what time do you want the status email to be sent?</div>");
		valid = 0;
	}else{
		if (returnVar.EventStatusEmail.checked && am_or_pm(returnVar.EventStatusEmailTime))
		{
			$(returnVar.EventStatusEmail).after("<div class='errortip' id='errorstatus'>Is the status email time AM or PM?</div>");
			valid = 0;
		}
	}
	if(valid == 1)
	{
		return true;
	}else{
		return false;
	}
}