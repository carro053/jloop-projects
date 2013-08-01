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
		echo $this->Form->input('twitter');
		echo $this->Form->input('facebook');
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
		echo '<input type="hidden" value="" name="data[Tag][]">';
		foreach($tags as $key => $tag) {
			$checked = false;
			foreach($this->request->data['Lead']['Tag'] as $_tag) {
				if($_tag['id'] == $tag['Tag']['id']) {
					$checked = true;
					break;
				}
			}
			echo $this->Form->input('Tag.'.$key, array(
				'type' => 'checkbox',
				'label' => $tag['Tag']['name'],
				'value' => $tag['Tag']['id'],
				'hiddenField' => false,
				'checked' => $checked,
				'div' => false
			));
		}
		echo '</fieldset></div>';
		echo $this->Form->input('Note.0.text', array('label' => 'Note'));
		echo $this->Form->input('Note.0.user_id', array('type' => 'hidden', 'value' => $authUser['id']));
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
					<td><?php echo $note['User']['username']; ?></td>
					<td><?php echo $note['created']; ?></td>
				</tr>
			<?php } ?>
		</tbody>
	</table>
	
	<?php pr($lead); ?>
	
	<?php
		/*
		echo $this->Form->create('Note', array('id' => 'NoteForm', 'url' => '#', 'type' => 'post'));
		echo $this->Form->input('lead_id', array('type' => 'hidden', 'value' => $lead['id']));
		echo $this->Form->input('text', array('id' => 'NoteText'));
		echo $this->Form->end('Add Note to Lead');
		*/
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
		
		//pass lead data to chrome extension content script
		var lead = <?php echo json_encode($lead); ?>;
		<?php if(empty($lead['name'])) { ?>
			lead.name = '<?php echo $this->request->data['Lead']['name']; ?>';
		<?php } ?>
		window.postMessage(lead, "*");
	</script>
</div>