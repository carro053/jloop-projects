<div id="LeadFormContainer">
	<?php
		if(empty($this->request->data['Lead']['id']))
			$this->request->data['Lead']['id'] = $lead['id'];
		
		echo $this->Form->create('Lead', array('id' => 'LeadUpdateForm', 'url' => '#', 'type' => 'post'));
		echo $this->Form->input('id');
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
	<script type="text/javascript">
		$('.radio').buttonset();
		$('#LeadUpdateForm').submit(function() {
			console.log('SUBMITTING');
			$.post('/Leads/update',
				$(this).serialize(),
				function(data) {
					$('#LeadFormContainer').replaceWith(data);
				}
			);
			return false;
		});
	</script>
</div>