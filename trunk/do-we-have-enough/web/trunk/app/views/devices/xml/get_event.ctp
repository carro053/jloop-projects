<event_data>
<event_name><?php echo $event['Event']['name']; ?></event_name>
<event_when><?php echo $event['Event']['when']; ?></event_when>
<event_where><?php echo $event['Event']['where']; ?></event_where>
<event_need><?php echo $event['Event']['need']; ?></event_need>
<event_details><?php echo $event['Event']['details']; ?></event_details>
<event_active><?php echo $event['Event']['active']; ?></event_active>
<event_cannot_invite_others><?php echo $event['Event']['cannot_invite_others']; ?></event_cannot_invite_others>
<event_cannot_bring_guests><?php echo $event['Event']['cannot_bring_guests']; ?></event_cannot_bring_guests>
<event_members>
<?php foreach($event['User'] as $member) : ?>
<event_member>
<status><?php echo $member['EventsUser']['status'];?></status>
<guests><?php echo $member['EventsUser']['guests']; ?></guests>
<name><?php if($member['name'] != '') { echo $member['name']; }else{ echo $member['email']; } ?></name>
</event_member>
<?php endforeach ?>
</event_members>
<event_your_status><?php echo $user['EventsUser']['status']; ?></event_your_status>
<event_your_guests><?php echo $user['EventsUser']['guests']; ?></event_your_guests>
<notify_when><?php if($user['EventsUser']['notify_reach_checked'] == 1) { echo $user['EventsUser']['notify_reach_count']; }else{ echo '0'; } ?></notify_when>
</event_data>