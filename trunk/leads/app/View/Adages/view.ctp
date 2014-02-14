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
			echo $this->Html->tableCells(array(array(
				$this->Html->tag('strong', 'Number of Employees'),
				$adage['Adage']['employees']
			)));
			echo $this->Html->tableCells(array(array(
				$this->Html->tag('strong', 'Specialties'),
				$adage['Adage']['specialties']
			)));
			echo $this->Html->tableCells(array(array(
				$this->Html->tag('strong', 'Address'),
				$adage['Adage']['address']
			)));
			echo $this->Html->tableCells(array(array(
				$this->Html->tag('strong', 'City'),
				$adage['Adage']['city']
			)));
			echo $this->Html->tableCells(array(array(
				$this->Html->tag('strong', 'State'),
				$adage['Adage']['state']
			)));
			echo $this->Html->tableCells(array(array(
				$this->Html->tag('strong', 'Zip'),
				$adage['Adage']['zip']
			)));
			echo $this->Html->tableCells(array(array(
				$this->Html->tag('strong', 'Country'),
				$adage['Adage']['country']
			)));
			echo $this->Html->tableCells(array(array(
				$this->Html->tag('strong', 'Phone'),
				$adage['Adage']['phone']
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