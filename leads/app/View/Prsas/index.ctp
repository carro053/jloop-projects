<h2>Review PRSA Scrapes</h2>

<?php
	echo $this->Form->create('Prsa', array('type' => 'get'));

	echo $this->Form->input('search', array(
		'label' => 'Search (Name, Industry Specializations, Practice Specializations, state)',
		'value' => !empty($_GET['search']) ? $_GET['search'] : null
	));
	echo $this->Form->end('Filter');
?>

<p><?php echo $count; ?> result(s).</p>

<table>
	<thead>
		<tr>
			<th>Name <?php echo $this->element('sorter', array('uri' => 'Prsas/index', 'field' => 'Prsa.name')); ?></th>
			<th>Industry Specializations <?php echo $this->element('sorter', array('uri' => 'Prsas/index', 'field' => 'industry_specializations')); ?></th>
			<th>Practice Specializations <?php echo $this->element('sorter', array('uri' => 'Prsas/index', 'field' => 'practice_specializations')); ?></th>
			<th>State <?php echo $this->element('sorter', array('uri' => 'Prsas/index', 'field' => 'Prsa.state')); ?></th>
			<th></th>
		</tr>
	</thead>
	<tbody>
		<?php foreach($prsas as $prsa) { ?>
			<tr>
				<td><?php echo $prsa['Prsa']['name']; ?></td>
				<td><?php echo $prsa['Prsa']['industry_specializations']; ?></td>
				<td><?php echo $prsa['Prsa']['practice_specializations']; ?></td>
				<td><?php echo $prsa['Prsa']['state']; ?></td>
				<td><a class="dialog" href="/Prsas/view/<?php echo $prsa['Prsa']['id']; ?>">View</a></td>
			</tr>
		<?php } if(empty($prsas)) { ?>
			<tr><td colspan="5">There are currently no PRSA Scrapes</td></tr>
		<?php } ?>
	</tbody>
</table>

<?php echo $this->element('pager', array('totalItems' => $count, 'uri' => 'Prsas/index')); ?>