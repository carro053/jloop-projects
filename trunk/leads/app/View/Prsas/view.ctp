<div id="leadOverlay">
	<table>
		<tbody>
			<?php
				if(!empty($prsa['Prsa']['company_url'])) {
					echo $this->Html->tableCells(array(array(
						$this->Html->tag('strong', 'Name'),
						$this->Html->link(
							$prsa['Prsa']['name'],
							$prsa['Lead']['website'],
							array('target' => '_blank')
						)
					)));
				} else {
					echo $this->Html->tableCells(array(array(
						$this->Html->tag('strong', 'Name'),
						$prsa['Prsa']['name']
					)));
				}
				echo $this->Html->tableCells(array(array(
					$this->Html->tag('strong', 'Industry Specializations'),
					$prsa['Prsa']['industry_specializations']
				)));
				echo $this->Html->tableCells(array(array(
					$this->Html->tag('strong', 'Practice Specializations'),
					$prsa['Prsa']['practice_specializations']
				)));
				echo $this->Html->tableCells(array(array(
					$this->Html->tag('strong', 'Number of Employees'),
					$prsa['Prsa']['employees']
				)));
			?>
		</tbody>
	</table>
	
	<h3>Make it a Lead!</h3>
	
	<?php echo $this->element('lead_form', array(
		'lead' => $prsa['Lead'],
		'defaultName' => '',
		'defaultCompany' => $prsa['Prsa']['name'],
		'defaultWebsite' => $prsa['Prsa']['company_url'],
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