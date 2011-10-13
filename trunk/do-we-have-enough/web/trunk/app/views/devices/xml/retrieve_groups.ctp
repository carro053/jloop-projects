<groups>
<?php foreach($groups as $group): ?>
<group>
<id><?php echo $group['id']; ?></id>
<name><?php echo $group['name']; ?></name>
<member_count><?php echo $group['member_count']; ?></member_count>
</group>
<?php endforeach; ?>
</groups>