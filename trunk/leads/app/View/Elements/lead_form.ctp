<div id="LeadFormContainer">
	<?php
		if(empty($this->request->data['Lead']))
			$this->request->data['Lead'] = $lead;
		
		echo $this->Form->create('Lead', array('id' => 'LeadUpdateForm', 'url' => '#', 'type' => 'post'));
		echo $this->Form->input('id');
		echo $this->Form->input('status', array('type' => 'hidden', 'value' => 1));
		echo $this->Form->input('email');
		echo $this->Form->input('phone');
		echo $this->Form->input('rating', array(
			'type' => 'radio',
			'options' => array(
				1 => 1,
				2 => 2,
				3 => 3,
				4 => 4,
				5 => 5
			),
			'hiddenField' => false
		));
		echo $this->Form->end('Save');
	?>
	
	<div id="LeadNotes">
	<?php
		pr($lead);
		foreach($lead['Note'] as $note) {
			echo '<p>'.$note.'</p>';
		}
	?>
	</div>
	
	<?php
		echo $this->Form->create('Note', array('id' => 'NoteForm', 'url' => '#', 'type' => 'post'));
		echo $this->Form->input('text', array('id' => 'NoteText'));
		echo $this->Form->end('Add Note to Lead');
	?>
	
	<script type="text/javascript">
		$('.radio').buttonset();
		$('#LeadUpdateForm').submit(function() {
			$.post('/Leads/update',
				$(this).serialize(),
				function(data) {
					$('#LeadFormContainer').replaceWith(data);
				}
			);
			return false;
		});
		
		$('#NoteForm').submit(function() {
			if($('#NoteText').val() != '')
			{
				$.post('/Leads/addNote',
					$(this).serialize(),
					function(data) {
						console.log(data);
						$('#LeadNotes').prepend('<p>'+data+'</p>');
					}
				);
			} else {
				alert('Please enter some text for your note!');
			}
			return false;
		});
	</script>
</div>