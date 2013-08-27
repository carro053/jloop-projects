<h2>Leads</h2>

<?php
	echo $this->Form->create('Lead', array('type' => 'get'));
	echo $this->Form->input('form', array(
		'type' => 'hidden',
		'value' => true
	));
	echo $this->Form->input('type', array(
		'value' => !empty($_GET['type']) ? $_GET['type'] : null
	));
	echo $this->Form->input('search', array(
		'label' => 'Search (Name, Email, Twitter, Facebook, LinkedIn)',
		'value' => !empty($_GET['search']) ? $_GET['search'] : null
	));
	
	echo $this->Form->input('RatingAtLeast', array(
		'label' => 'Rating at least',
		'type' => 'select',
		'options' => array(
			'' => ' ',
			1 => '1',
			2 => '2',
			3 => '3',
			4 => '4',
			5 => '5'
		),
		'value' => !empty($_GET['RatingAtLeast']) ? $_GET['RatingAtLeast'] : null
	));
	
	echo $this->Form->input('RatingLessThan', array(
		'label' => 'Rating less than',
		'type' => 'select',
		'options' => array(
			'' => ' ',
			2 => '2',
			3 => '3',
			4 => '4',
			5 => '5'
		),
		'value' => !empty($_GET['RatingLessThan']) ? $_GET['RatingLessThan'] : null
	));
	
	echo '<div class="tags"><fieldset><legend>Include Tags</legend>';
	foreach($tags as $key => $tag) {
		$checked = false;
		if(!empty($_GET['IncludeTag'])) {
			foreach($_GET['IncludeTag'] as $tag_id) {
				if($tag_id == $tag['Tag']['id']) {
					$checked = true;
					break;
				}
			}
		}
		echo $this->Form->input('IncludeTag[]', array(
			'id' => 'LeadIncludeTag'.$key,
			'type' => 'checkbox',
			'label' => $tag['Tag']['name'],
			'value' => $tag['Tag']['id'],
			'hiddenField' => false,
			'checked' => $checked,
			'div' => false,
			'onchange' => 'console.log(this.checked)'
		));
	}
	echo '</fieldset></div>';
	echo '<div class="tags"><fieldset><legend>Exclude Tags</legend>';
	foreach($tags as $key => $tag) {
		$checked = false;
		if(!empty($_GET['ExcludeTag'])) {
			foreach($_GET['ExcludeTag'] as $tag_id) {
				if($tag_id == $tag['Tag']['id']) {
					$checked = true;
					break;
				}
			}
		}
		echo $this->Form->input('ExcludeTag[]', array(
			'id' => 'LeadExcludeTag'.$key,
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

<?php /*<a href="http://<?php echo $_SERVER['HTTP_HOST'].'/Leads/index/1?'.$_SERVER['QUERY_STRING']; ?>" onclick="return confirm('NOTE: Any leads without email addresses will NOT be exported');">Export these results to Mailman</a> */ ?>

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
			<th>In Group(s)</th>
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
				<td>
					<?php
						$i = 0;
						foreach($lead['Group'] as $group) {
							echo $group['name'];
							if($i < count($lead['Group']) - 1)
								echo ', ';
							$i++;
						}
						if(empty($lead['Group']))
							echo 'NONE';
					?>
				</td>
				<td><a class="dialog" href="/<?php echo Inflector::pluralize($lead['Lead']['model']); ?>/view/<?php echo $lead['Lead']['model_id']; ?>">View</a></td>
			</tr>
		<?php } if(empty($lead)) { ?>
			<tr><td colspan="5">There are no leads that match this search.</td></tr>
		<?php } ?>
	</tbody>
</table>

<?php
	echo $this->Form->input('Group.name');
	echo $this->Form->end('Add to Group');
?>

<?php echo $this->element('pager', array('totalItems' => $count, 'uri' => 'Leads/index')); ?>

<script type="text/javascript">
	$('.tags').buttonset();
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