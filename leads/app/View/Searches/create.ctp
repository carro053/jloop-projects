<h2>iTunes Search</h2>
<?php
	echo $this->Form->create('Search');
	echo $this->Form->input('search_terms', array('label' => 'Search Terms (e.x. automotive, fitness, etc.)'));
	echo $this->Form->input('is_not_iphone_5');
	echo $this->Form->input('is_not_ipad_only');
	echo $this->Form->input('use_date_range', array('onchange' => 'toggleDates(this);'));
	echo '<div id="date-range" style="display:none;">';
	echo $this->Form->input('start_date', array('class' => 'date-range'));
	echo $this->Form->input('end_date', array('class' => 'date-range'));
	echo '</div>';
	
?>
<a href="#" onclick="ajax_google_search_preview();">Preview</a>
<h3>Google Search Preview: <a id="previewLink" href="#" target="_blank"></a></h3>
<?	
	echo $this->Form->end('Search');
?>



<script type="text/javascript">
	$("#SearchCreateForm").change(function() {
		google_search_preview();
	});
	$("#SearchCreateForm").keyup(function() {
		google_search_preview();
	});
	
	function ajax_google_search_preview() {
		$.post('/Searches/ajaxGetGoogleSearchPreviewLink',
			$('#SearchCreateForm').serialize(),
			function(data) {
				$('#previewLink').html(data);
				$('#previewLink').attr('href', data);
			}
		);
		return false;
	}

	function toggleDates(checkbox) {
		if($(checkbox).is(':checked'))
			$('#date-range').show();
		else
			$('#date-range').hide();
	}
	
	function google_search_preview() {
		console.log('form changed');
		var form = $("#SearchCreateForm").serializeObject();
		console.log(form);
	}
	
	$.fn.serializeObject = function()
	{
	    var o = {};
	    var a = this.serializeArray();
	    $.each(a, function() {
	        if (o[this.name] !== undefined) {
	            if (!o[this.name].push) {
	                o[this.name] = [o[this.name]];
	            }
	            o[this.name].push(this.value || '');
	        } else {
	            o[this.name] = this.value || '';
	        }
	    });
	    return o;
	};
</script>