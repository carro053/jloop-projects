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
						<td>Something</td>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>Todd</td>
						<td>Other</td>
					</tr>
					<tr>
						<td>Todd</td>
						<td>Other</td>
					</tr>
					<tr>
						<td>Todd</td>
						<td>Other</td>
					</tr>
				</tbody>
			</table>
		</div>
	<?php } ?>
</div>

<?php pr($groups); ?>

<script>
	$(function() {
		$( "#accordion" ).accordion();
	});
</script>