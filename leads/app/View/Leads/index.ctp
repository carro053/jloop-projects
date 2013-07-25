<h2>Leads</h2>

<?php
	echo $this->Form->create('Lead', array('type' => 'get'));
	echo $this->Form->input('type', array(
		'value' => !empty($_GET['type']) ? $_GET['type'] : null
	));
	echo $this->Form->input('search', array(
		'label' => 'Search (Name, Email)',
		'value' => !empty($_GET['search']) ? $_GET['search'] : null
	));
	echo $this->Form->end('Filter');
?>

<table>
	<thead>
		<tr>
			<th>Name <?php echo $this->element('sorter', array('uri' => 'Leads/index', 'field' => 'name')); ?></th>
			<th>Type</th>
			<th>Rating <?php echo $this->element('sorter', array('uri' => 'Leads/index', 'field' => 'rating')); ?></th>
			<th>Email</th>
			<th>Phone</th>
			<th>Created <?php echo $this->element('sorter', array('uri' => 'Leads/index', 'field' => 'created')); ?></th>
			<th>Tags</th>
			<th>Notes</th>
			<th></th>
		</tr>
	</thead>
	<tbody>
		<?php foreach($leads as $lead) { ?>
			<tr>
				<td><?php echo $lead['Lead']['name']; ?></td>
				<td><?php echo $lead['Lead']['type']; ?></td>
				<td><?php echo $lead['Lead']['rating']; ?></td>
				<td><?php echo '<a href="mailto:'.$lead['Lead']['email'].'">'.$lead['Lead']['email'].'</a>'; ?></td>
				<td><?php echo $lead['Lead']['phone']; ?></td>
				<td><?php echo $lead['Lead']['created']; ?></td>
				<td><?php foreach($lead['Tag'] as $key => $tag) if($key != 0) echo ', '; echo $tag['name']; ?></td>
				<td><?php echo count($lead['Note']); ?></td>
				<td><a class="dialog" href="/<?php echo Inflector::pluralize($lead['Lead']['model']); ?>/view/<?php echo $lead['Lead']['model_id']; ?>">View</a></td>
			</tr>
		<?php } if(empty($lead)) { ?>
			<tr><td colspan="5">There are currently no leads</td></tr>
		<?php } ?>
	</tbody>
</table>

<?php echo $this->element('pager', array('totalItems' => $count, 'uri' => 'Leads/index')); ?>