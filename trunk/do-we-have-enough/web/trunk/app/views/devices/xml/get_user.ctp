<user_data>
<id><?php echo $user['User']['name']; ?></id>
<email><?php echo $user['User']['email']; ?></email>
<name><?php echo $user['User']['name']; ?></name>
<cell_number><?php echo $user['User']['cell_number']; ?></cell_number>
<notify_text><?php echo $user['User']['notify_text']; ?></notify_text>
<notify_in><?php echo $user['User']['notify_in']; ?></notify_in>
<notify_out><?php echo $user['User']['notify_out']; ?></notify_out>
<notify_event_change><?php echo $user['User']['notify_event_change']; ?></notify_event_change>
<app_notify_in><?php echo $user['User']['app_notify_in']; ?></app_notify_in>
<app_notify_out><?php echo $user['User']['app_notify_out']; ?></app_notify_out>
<app_notify_event_change><?php echo $user['User']['app_notify_event_change']; ?></app_notify_event_change>
<latest_event_id><?php echo $user['Event']['id']; ?></latest_event_id>
<latest_event_name><?php echo $user['Event']['name']; ?></latest_event_name>
<latest_event_when><?php echo $user['Event']['when']; ?></latest_event_when>
</user_data>