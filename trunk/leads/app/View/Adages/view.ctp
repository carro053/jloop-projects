<table>
	<tbody>
		<?php
			echo $this->Html->tableCells(array(array(
				$this->Html->tag('strong', 'Name'),
				$this->Html->link(
					$adage['Adage']['name'],
					$adage['Adage']['company_url'],
					array('target' => '_blank')
				)
			)));
			echo $this->Html->tableCells(array(array(
				$this->Html->tag('strong', 'AdAge Link'),
				$this->Html->link(
					$adage['Adage']['url'],
					$adage['Adage']['url'],
					array('target' => '_blank')
				)
			)));
			echo $this->Html->tableCells(array(array(
				$this->Html->tag('strong', 'Category'),
				$adage['Adage']['categories']
			)));
			
		?>
	</tbody>
</table>

<h3>Make it a Lead!</h3>

<?php echo $this->element('lead_form', array(
	'lead' => $adage['Lead'],
	'defaultName' => '',
	'defaultCompany' => $adage['Adage']['name'],
	'tags' => $tags
)); ?>

<script type="text/javascript">
	try {
		init();
	}
	catch(error){
		console.warn(error);
	}
</script>