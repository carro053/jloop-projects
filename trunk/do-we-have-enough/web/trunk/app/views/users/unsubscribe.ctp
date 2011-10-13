<div class="container">
	<div class="copy">
        <h2>Unsubscribe</h2>
        <?php if(isset($event['EventsUser']['id'])) { ?>
        <p>If you want to unsubscribe FROM THIS EVENT: <a href="/unsubscribe_from_event/<?php echo $event['EventsUser']['hash']; ?>">Click here.</a></p>
        <p>If you want to unsubscribe FROM THIS EVENT AND GROUP: <a href="/unsubscribe_from_group/<?php echo $event['EventsUser']['hash']; ?>/<?php echo $event['Event']['group_id']; ?>/<?php echo $user['User']['id']; ?>">Click here.</a></p>
        <?php } ?>
        <p>If you want to unsubscribe FROM ALL EMAILS FROM THIS SITE FOREVERMORE: <a href="/unsubscribe_from_dwhe/<?php echo $user['User']['email']; ?>">Click here.</a></p>
    </div><!-- end .copy -->
</div><!-- end .container -->