<script type="text/javascript">
	function validate_form()
	{
		$("div.errortip").remove();
		var valid = 1;
		id = 'current_events';
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
		if(valid == 1)
		{
			return true;
		}else{
			return false;
		}
	}
</script>
<div class="container">
	<div class="copy">
        <h2>My Events</h2>
        <p>Enter your email address and we will send you an email with all the links you need for each of the events you are a part of.</p>
        <form action="/current_events" method="post" id="current_events" class="event" name="current_events" enctype="multipart/form-data">
        <div>
            <input name="data[User][email]" id="UserEmail" type="text" value="Enter your email" onfocus="javascript:if(this.value == 'Enter your email') { this.value = ''; } the_tooltip(this,'All we need is your email.','email');" onblur="javascript:if(this.value == '') { this.value = 'Enter your email'; }" />
        </div>
		<?php echo $form->submit('Submit',array('onclick'=>'return validate_form();','id'=>'current','name'=>'current')); ?>
        </form>
    </div><!-- end .copy -->
</div><!-- end .container -->