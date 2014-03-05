<div id="LeadFormContainer">
	<?php
		
		if(empty($this->request->data['Lead']))
			$this->request->data['Lead'] = $lead;
		if(empty($this->request->data['Lead']['name']) && !empty($defaultName))
			$this->request->data['Lead']['name'] = $defaultName;
		if(empty($this->request->data['Lead']['company']) && !empty($defaultCompany))
			$this->request->data['Lead']['company'] = $defaultCompany;
		if(empty($this->request->data['Lead']['website']) && !empty($defaultWebsite))
			$this->request->data['Lead']['website'] = $defaultWebsite;
		
		echo $this->Form->create('Lead', array('id' => 'LeadUpdateForm', 'url' => '#', 'type' => 'post'));
		echo $this->Form->input('id');
		echo $this->Form->input('name');
		echo $this->Form->input('company');
		echo $this->Form->input('website');
		echo $this->Form->input('status', array('type' => 'hidden', 'value' => 1));
		echo $this->Form->input('email');
		echo $this->Form->input('twitter');
		echo $this->Form->input('facebook');
		echo $this->Form->input('linkedin');
		echo $this->Form->input('phone');
		echo $this->Form->input('address');
		echo $this->Form->input('city');
		echo $this->Form->input('state');
		echo $this->Form->input('zip');
		echo $this->Form->input('country');
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
	?>
	
	<table>
		<thead>
			<tr>
				<th>Contact</th>
				<th>Phone</th>
				<th>Email</th>
				<th>IM</th>
				<th>Website</th>
				<th>Address</th>
				<th>Linkedin</th>
				<th>Twitter</th>
				<th>Background Info</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($lead['Contact'] as $contact) { ?>
				<tr>
					<td><?php echo $this->Editable->contact('first_name', $contact).' '.$this->Editable->contact('last_name', $contact).'<br />'.$this->Editable->contact('title', $contact); ?></td>
					<td><?php echo $this->Editable->contact('phone', $contact); ?></td>
					<td><?php echo $this->Editable->contact('email', $contact); ?></td>
					<td><?php echo $this->Editable->contact('im', $contact); ?></td>
					<td><?php echo $this->Editable->contact('website', $contact); ?></td>
					<td><?php echo $this->Editable->contact('address', $contact).'<br />'.$this->Editable->contact('city', $contact).' '.$this->Editable->contact('state', $contact).' '.$this->Editable->contact('zip', $contact).' '.$this->Editable->contact('country', $contact); ?></td>
					<td><?php echo $this->Editable->contact('linkedin', $contact); ?></td>
					<td><?php echo $this->Editable->contact('twitter', $contact); ?></td>
					<td><?php echo $this->Editable->contact('background_info', $contact); ?></td>
				</tr>
			<?php } ?>
			<?php if(empty($lead['Contact'])) { ?>
				<tr><td colspan="9">No contacts entered yet.</td></tr>
			<?php } ?>
		</tbody>
	</table>
	
	<?php
		echo $this->Form->input('Contact.0.first_name');
		echo $this->Form->input('Contact.0.last_name');
		echo $this->Form->input('Contact.0.title');
		echo $this->Form->input('Contact.0.phone');
		echo $this->Form->input('Contact.0.email');
		echo $this->Form->input('Contact.0.im');
		echo $this->Form->input('Contact.0.website');
		echo $this->Form->input('Contact.0.address');
		echo $this->Form->input('Contact.0.city');
		echo $this->Form->input('Contact.0.state');
		echo $this->Form->input('Contact.0.zip');
		echo $this->Form->input('Contact.0.country');
		echo $this->Form->input('Contact.0.linkedin');
		echo $this->Form->input('Contact.0.twitter');
		echo $this->Form->input('Contact.0.background_info');
	?>
	
	<table>
		<thead>
			<tr>
				<th>Note</th>
				<th>Author</th>
				<th>Date</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($lead['Note'] as $note) { ?>
				<tr>
					<td><?php echo $note['text']; ?></td>
					<td><?php echo $note['User']['username']; ?></td>
					<td><?php echo $note['created']; ?></td>
				</tr>
			<?php } ?>
			<?php if(empty($lead['Note'])) { ?>
				<tr><td colspan="3">No notes entered yet.</td></tr>
			<?php } ?>
		</tbody>
	</table>
	
	<?php
		echo $this->Form->input('Note.0.text', array('label' => 'Note'));
		echo $this->Form->input('Note.0.user_id', array('type' => 'hidden', 'value' => $authUser['id']));
		echo $this->Form->end('Save');
	?>
	
	<?php if(!empty($this->request->data['Lead']['id'])) {
		if(empty($this->request->data['Lead']['highrise_id'])) { ?>
			<a href="#" onclick="exportToHighrise(<?php echo $this->request->data['Lead']['id']; ?>); return false;" class="highrise-export-<?php echo $this->request->data['Lead']['id']; ?>">Export to Highrise</a>
		<?php } else { ?>
			<a href="https://jloop.highrisehq.com/companies/<?php echo $this->request->data['Lead']['highrise_id']; ?>" target="_blank" class="highrise-export-<?php echo $this->request->data['Lead']['id']; ?>">View in Highrise</a>
		<?php } ?>
	<?php } ?>
	
	<script type="text/javascript">
		$('.radio').buttonset();
		$('#tags').buttonset();
		$('#LeadUpdateForm').submit(function() {
			$.post('/Leads/update',
				$(this).serialize(),
				function(data) {
					//$('#LeadFormContainer').replaceWith(data);
					if(data == 'updated') {
					
						var time = new Date().getTime();
						$.ajax({
							url: "/<?php echo Inflector::pluralize($this->request->data['Lead']['model']); ?>/view/<?php echo $this->request->data['Lead']['model_id']; ?>"+"?t="+time,
							type: "GET",
							success: function(leadFormData){
								//console.log(leadFormData);
								//$('#LeadFormContainer').replaceWith($("#LeadFormContainer", $(leadFormData)));
								
								var elements = $(leadFormData);
								var found = $('#LeadFormContainer', leadFormData);
								console.log('alleged inner HTML');
								console.log(found.html());
								
								$('#LeadFormContainer').replaceWith(leadFormData);
							},
							error: function(){
								alert('There was an error with AJAX.');
							}
						});
					}
				}
			);
			return false;
		});
		$('.editable').editable({
			mode: 'inline'
		});
		
		//pass lead data to chrome extension content script
		var extension_info = <?php echo json_encode($lead); ?>;
		<?php if(empty($lead['name'])) { ?>
			extension_info.name = '<?php echo $this->request->data['Lead']['name']; ?>';
		<?php } ?>
		extension_info.site_url = "<?php echo $_SERVER['HTTP_HOST']; ?>";
		if(extension_info.site_url.indexOf('dev') != -1)
			extension_info.environment = 'DEV';
		else
			extension_info.environment = 'LIVE';
		window.postMessage(extension_info, "*");
		console.log('message should be sent to extension');
	</script>
</div>