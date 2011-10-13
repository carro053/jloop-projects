<div class="container">
	<div class="copy">
        <h2>My Events</h2>
        <p><?php foreach($data['Event'] as $event):
        echo '<a href="http://'.$environment.'.dowehaveenough.com/go_to_event/'.$event['EventsUser']['hash'].'">'.$event['name'].' - '.$event['when'].' - '.$event['where'].'</a><br />';
        endforeach; ?>
        </p>
    </div><!-- end .copy -->
</div><!-- end .container -->