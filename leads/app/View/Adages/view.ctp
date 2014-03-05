<div id="#leadOverlay">
	<table>
		<tbody>
			<?php
				echo $this->Html->tableCells(array(array(
					$this->Html->tag('strong', 'Name'),
					$this->Html->link(
						$adage['Adage']['name'],
						$adage['Lead']['website'],
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
					$this->Html->tag('strong', 'Categories'),
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
			?>
		</tbody>
	</table>
	
	<h3>Make it a Lead!</h3>
	
	<?php echo $this->element('lead_form', array(
		'lead' => $adage['Lead'],
		'defaultName' => '',
		'defaultCompany' => $adage['Adage']['name'],
		'defaultWebsite' => $adage['Adage']['company_url'],
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
</div>