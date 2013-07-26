<?php /*
<table>
	<thead>
		<tr>
			<th>Name <?php echo $this->element('sorter', array('uri' => 'Groups/index', 'field' => 'name')); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php foreach($groups as $group) { ?>
			<tr>
				<td><?php echo $group['Group']['name']; ?></td>
			</tr>
		<?php } if(empty($groups)) { ?>
			<tr><td>There are currently no groups. What were you thinking?</td></tr>
		<?php } ?>
	</tbody>
</table>
*/ ?>

<div id="accordion">
	<?php foreach($groups as $group) { ?>
		<h3><?php echo $group['Group']['name']; ?></h3>
		<div>
			<table>
				<thead>
					<tr>
						<td>Name</td>
						<td>Type</td>
						<td>Rating</td>
						<td>Email</td>
						<td>Phone</td>
						<td>&nbsp;</td>
					</tr>
				</thead>
				<tbody>
					<?php foreach($group['Lead'] as $lead) { ?>
						<tr>
							<td><?php echo $lead['name']; ?></td>
							<td><?php echo $lead['type']; ?></td>
							<td><?php echo $lead['rating']; ?></td>
							<td><?php echo $lead['email']; ?></td>
							<td><?php echo $lead['phone']; ?></td>
							<td><a class="dialog" href="/<?php echo Inflector::pluralize($lead['model']); ?>/view/<?php echo $lead['model_id']; ?>">View</a></td>
						</tr>
					<?php } if(empty($group['Lead'])) { ?>
						<tr><td colspan="6">There are currently no leads in this group. Bummer.</td></tr>
					<?php } ?>
				</tbody>
			</table>
		</div>
	<?php } ?>
</div>

<?php pr($groups); ?>

<script>
	$(function() {
		$( "#accordion" ).accordion({
			heightStyle: "content"
		});
	});
</script>