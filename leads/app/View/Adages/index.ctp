<h2>Review AdAge Scrapes</h2>

<?php
	echo $this->Form->create('Adage', array('type' => 'get'));

	echo $this->Form->input('search', array(
		'label' => 'Search (Name, category, specialty, region, state)',
		'value' => !empty($_GET['search']) ? $_GET['search'] : null
	));
	echo $this->Form->end('Filter');
?>

<p><?php echo $count; ?> result(s).</p>

<table>
	<thead>
		<tr>
			<th>Name <?php echo $this->element('sorter', array('uri' => 'Adages/index', 'field' => 'Adage.name')); ?></th>
			<th>Categories <?php echo $this->element('sorter', array('uri' => 'Adages/index', 'field' => 'categories')); ?></th>
			<th>Specialties <?php echo $this->element('sorter', array('uri' => 'Adages/index', 'field' => 'specialties')); ?></th>
			<th>Region <?php echo $this->element('sorter', array('uri' => 'Adages/index', 'field' => 'regions')); ?></th>
			<th>State <?php echo $this->element('sorter', array('uri' => 'Adages/index', 'field' => 'Adage.state')); ?></th>
			<th></th>
		</tr>
	</thead>
	<tbody>
		<?php foreach($adages as $adage) { ?>
			<tr>
				<td><?php echo $adage['Adage']['name']; ?></td>
				<td><?php echo $adage['Adage']['categories']; ?></td>
				<td><?php echo $adage['Adage']['specialties']; ?></td>
				<td><?php echo $adage['Adage']['regions']; ?></td>
				<td><?php echo $adage['Adage']['state']; ?></td>
				<td><a class="dialog" href="/Adages/view/<?php echo $adage['Adage']['id']; ?>">View</a></td>
			</tr>
		<?php } if(empty($adages)) { ?>
			<tr><td colspan="5">There are currently no adages</td></tr>
		<?php } ?>
	</tbody>
</table>

<?php echo $this->element('pager', array('totalItems' => $count, 'uri' => 'Adages/index')); ?>