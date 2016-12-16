<div class="container">
	<div class="copy">
		<h2>Group Email List</h2>
		<p>
        <?php 
        $i = 0;
        $j = 0;
        $date = '';
        foreach($data as $user):
        		echo '
        		</p>
        		<h3>'.$user['Email'].'</h3>
        		<p>';
        endforeach;
        ?>
        </p>
    </div><!-- end .copy -->
</div><!-- end .container -->
