<?php pr($_GET); ?>
<h2>Leads</h2>

<?php
	echo $this->Form->create('Lead', array('type' => 'get'));
	echo $this->Form->input('type', array(
		'value' => !empty($_GET['type']) ? $_GET['type'] : null
	));
	echo $this->Form->input('search', array(
		'label' => 'Search (Name, Email, Twitter, Facebook)',
		'value' => !empty($_GET['search']) ? $_GET['search'] : null
	));
	echo '<div id="tags"><fieldset><legend>Tags</legend>';
	foreach($tags as $key => $tag) {
		$checked = false;
		/*foreach($this->request->data['Lead']['Tag'] as $_tag) {
			if($_tag['id'] == $tag['Tag']['id']) {
				$checked = true;
				break;
			}
		}*/
		echo $this->Form->input('Tag['.$key.']', array(
			'type' => 'checkbox',
			'label' => $tag['Tag']['name'],
			'value' => $tag['Tag']['id'],
			'hiddenField' => false,
			'checked' => $checked,
			'div' => false
		));
	}
	echo '</fieldset></div>';
	echo $this->Form->end('Filter');
?>

<p><?php echo $count; ?> result(s).</p>

<?php echo $this->Form->create('Leads'); ?>

<table>
	<thead>
		<tr>
			<th><input type="checkbox" onchange="toggleSelectAll(this);" /></th>
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
	<tbody id="filtered-leads">
		<?php foreach($leads as $lead) { ?>
			<tr>
				<td><input name="data[Leads][<?php echo $lead['Lead']['id']; ?>]" type="checkbox" /></td>
				<td><?php echo $lead['Lead']['name']; ?></td>
				<td><?php echo $lead['Lead']['type']; ?></td>
				<td><?php echo $lead['Lead']['rating']; ?></td>
				<td><?php echo '<a href="mailto:'.$lead['Lead']['email'].'">'.$lead['Lead']['email'].'</a>'; ?></td>
				<td><?php echo $lead['Lead']['phone']; ?></td>
				<td><?php echo $lead['Lead']['created']; ?></td>
				<td><?php
					foreach($lead['Tag'] as $key => $tag) {
						if($key != 0)
							echo ', ';
						echo $tag['name'];
					}
				?></td>
				<td><?php echo count($lead['Note']); ?></td>
				<td><a class="dialog" href="/<?php echo Inflector::pluralize($lead['Lead']['model']); ?>/view/<?php echo $lead['Lead']['model_id']; ?>">View</a></td>
			</tr>
		<?php } if(empty($lead)) { ?>
			<tr><td colspan="5">There are currently no leads</td></tr>
		<?php } ?>
	</tbody>
</table>

<?php
	echo $this->Form->input('Group.name');
	echo $this->Form->end('Add to Group');
?>

<?php echo $this->element('pager', array('totalItems' => $count, 'uri' => 'Leads/index')); ?>

<script type="text/javascript">
	//$('#tags').buttonset();
	var groups = <?php echo json_encode($groups); ?>;
	$("#GroupName").autocomplete({
		source: function( request, response ) {
			$.getJSON('/Groups/getJSON', {
				term: request
			}, response );
		},
		minLength: 0
	}).focus(function() {
		$(this).autocomplete('search');
	});
    
	$('#LeadsIndexForm').submit(function() {
		$.post('/Groups/addLeads',
			$(this).serialize(),
			function(data) {
				$('<div/>').html(data).dialog({
					modal: true
				});
			}
		);
		return false;
	});
		
	function toggleSelectAll(checkbox) {
		if($(checkbox).is(':checked'))
			$('#filtered-leads tr input').prop('checked', true);
		else
			$('#filtered-leads tr input').removeAttr('checked');
	}
</script>