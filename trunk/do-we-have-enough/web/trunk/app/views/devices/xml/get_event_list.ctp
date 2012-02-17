<events>
<?php foreach($user['Event'] as $event): ?>
<event>
<id><?php echo $event['id']; ?></id>
<name><?php echo $event['name']; ?></name>
<active><?php echo $event['active']; ?></active>
<date><?php echo $event['date']; ?></date>
<when><?php echo date('M jS',strtotime($event['date'])).' at '.$event['when']; ?></when>
<need><?php echo $event['need']; ?></need>
<members_in><?php echo $event['members_in']; ?></members_in>
</event>
<?php endforeach; ?>
</events>

