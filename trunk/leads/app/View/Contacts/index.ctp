<h2>Contacts</h2>

<?php
	echo $this->Form->create('Contact', array('type' => 'get'));
	echo $this->Form->input('user_id', array(
		'label' => 'Assigned User',
		'options' => $users,
		'value' => !empty($_GET['user_id']) ? $_GET['user_id'] : null
	));
	echo $this->Form->end('Filter');
?>

<p><?php echo $count; ?> result(s).</p>

<table>
	<thead>
		<tr>
			<th>First Name</th>
			<th>Last Name</th>
			<th>Title</th>
			<th>Phone</th>
			<th>Email</th>
			<th>IM</th>
			<th>Website</th>
			<th>Address</th>
			<th>Linkedin</th>
			<th>Twitter</th>
			<th>Background Info</th>
			<th>Assigned User</th>
			<th></th>
		</tr>
	</thead>
	<tbody>
		<?php foreach($contacts as $contact) { ?>
			<tr>
				<td><?php echo $contact['Contact']['first_name']; ?></td>
				<td><?php echo $contact['Contact']['last_name']; ?></td>
				<td><?php echo $contact['Contact']['title']; ?></td>
				<td><?php echo $contact['Contact']['phone']; ?></td>
				<td><?php if(!empty($contact['Contact']['email'])) echo '<a href="mailto:'.$contact['Contact']['email'].'">'.$contact['Contact']['email'].'</a>'; ?></td>
				<td><?php echo $contact['Contact']['im']; ?></td>
				<td><?php if(!empty($contact['Contact']['website'])) echo '<a target="_blank" href="'.$contact['Contact']['website'].'">'.$contact['Contact']['website'].'</a>'; ?></td>
				<td>
					<?php echo $contact['Contact']['address']; ?><br>
					<?php echo $contact['Contact']['city']; ?>, 
					<?php echo $contact['Contact']['state']; ?> 
					<?php echo $contact['Contact']['zip']; ?><br>
					<?php echo $contact['Contact']['country']; ?>
				</td>
				<td><?php if(!empty($contact['Contact']['linkedin'])) echo '<a target="_blank" href="'.$contact['Contact']['linkedin'].'">Linkedin</a>'; ?></td>
				<td><?php echo $contact['Contact']['twitter']; ?></td>
				<td><?php echo $contact['Contact']['background_info']; ?></td>
				<td><?php echo $contact['User']['username']; ?></td>
				<td><a class="dialog" href="/<?php echo Inflector::pluralize($contact['Lead']['model']); ?>/view/<?php echo $contact['Lead']['model_id']; ?>">Lead</a></td>
			</tr>
		<?php } if(empty($contacts)) { ?>
			<tr><td colspan="12">There are no contacts that match this search.</td></tr>
		<?php } ?>
	</tbody>
</table>

<?php echo $this->element('pager', array('totalItems' => $count, 'uri' => 'Contacts/index')); ?>