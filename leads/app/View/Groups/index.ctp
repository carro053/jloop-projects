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

<?php pr($groups); ?>