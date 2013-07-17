<div id="LeadFormContainer">
	<?php
		echo $this->Form->create('Lead', array('id' => 'LeadUpdateForm', 'url' => '#'));
		echo $this->Form->input('id', array('value' => $lead['id']));
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