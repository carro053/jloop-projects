<form class="event">
<p style="color: #7DC3E1; margin-top:5px;"><?php 
if(isset($message['unsubscribed']))
{
	echo 'Your friend has unsubscribed from DWHE<br />and can not be added to any events.';
}else{
	if(isset($message['already_in_group']))
	{
		if(isset($message['already_in_event']))
		{
			echo 'Your friend is already part<br />of the event and group.';
		}elseif(isset($message['added_to_event']))
		{
			echo 'Your friend was added to the<br />event but was already in the group.';
		}else{
			echo 'Your friend is already<br />part of the group.';
		}
	}elseif(isset($message['added_to_group']))
	{
		if(isset($message['already_in_event']))
		{
			echo 'Your friend was added to the<br />group but was already part of the event.';
		}elseif(isset($message['added_to_event']))
		{
			echo 'Your friend was added<br />to the event and the group.';
		}else{
			echo 'Your friend was added of the group.';
		}
	}elseif(isset($message['already_in_event']))
	{
		echo 'Your friend was already part of the event.';
	}elseif(isset($message['added_to_event']))
	{
		echo 'Your friend was added to the event.';
	}elseif(isset($message['unsubscribed_from_group']))
	{
		echo 'Your friend has unsubscribed from this group.';
	}else{
		echo 'You must select whether to add<br />to the event, group, or both!';
	}
}
 ?><br />
Click <?php echo $ajax->link('here', '/new_invite/'.$event_id, array('update'=>'invite')); ?> to invite another friend.</p></form>