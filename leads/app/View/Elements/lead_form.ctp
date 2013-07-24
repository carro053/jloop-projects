<div id="LeadFormContainer">
	<?php
		if(empty($this->request->data['Lead']))
			$this->request->data['Lead'] = $lead;
		if(empty($this->request->data['Lead']['name']) && !empty($defaultName))
			$this->request->data['Lead']['name'] = $defaultName;
		
		echo $this->Form->create('Lead', array('id' => 'LeadUpdateForm', 'url' => '#', 'type' => 'post'));
		echo $this->Form->input('id');
		echo $this->Form->input('name');
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
		echo '<div id="tags"><fieldset><legend>Tags</legend>';
		foreach($tags as $tag) {
			echo $this->Form->input('Tag.'.$tag['Tag']['name'], array(
				'type' => 'checkbox',
				'label' => $tag['Tag']['name'],
				'value' => $tag['Tag']['id'],
				'hiddenField' => false,
				'div' => false
			));
		}
		echo '</fieldset></div>';
		echo $this->Form->end('Save');
	?>
	
	<table>
		<thead>
			<tr>
				<th>Note</th>
				<th>Author</th>
				<th>Date</th>
			</tr>
		</thead>
		<tbody id="LeadNotes">
			<?php foreach($lead['Note'] as $note) { ?>
				<tr>
					<td><?php echo $note['text']; ?></td>
					<td><?php echo $note['user_id']; ?></td>
					<td><?php echo $note['created']; ?></td>
				</tr>
			<?php } ?>
		</tbody>
	</table>
		
	<?php
		echo $this->Form->create('Note', array('id' => 'NoteForm', 'url' => '#', 'type' => 'post'));
		echo $this->Form->input('lead_id', array('type' => 'hidden', 'value' => $lead['id']));
		echo $this->Form->input('text', array('id' => 'NoteText'));
		echo $this->Form->end('Add Note to Lead');
	?>
	
	<script type="text/javascript">
		$('.radio').buttonset();
		$('#tags').buttonset();
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
						if(data != 'error') {
							json_data = eval('(' + data + ')');
							$('#LeadNotes').prepend('<tr><td>'+json_data['text']+'</td><td>'+json_data['user_id']+'</td><td>'+json_data['created']+'</td></tr>');
							$('#NoteText').val('');
						} else {
							alert('Something went horribly wrong and your note wasn\'t saved');
						}
					}
				);
			} else {
				alert('Please enter some text for your note!');
			}
			return false;
		});
	</script>
</div>