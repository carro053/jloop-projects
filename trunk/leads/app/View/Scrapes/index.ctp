<h2>Review Itunes App Scrapes</h2>

<?php
	echo $this->Form->create('Scrape', array('type' => 'get'));
	echo $this->Form->input('category', array(
		'value' => !empty($_GET['category']) ? $_GET['category'] : null
	));
	echo $this->Form->input('updated', array(
		'label' => 'Last Updated/Released Before',
		'selected' => !empty($_GET['updated']) ? $_GET['updated']['year'].'-'.$_GET['updated']['month'].'-'.$_GET['updated']['day'] : null
	));
	echo $this->Form->input('iphone5', array(
		'label' => 'iPhone 5 Optimization',
		'options' => array(
			'' => 'Any',
			'no' => 'Not Optimized',
			'yes' => 'Optimized'
		),
		'value' => !empty($_GET['iphone5']) ? $_GET['iphone5'] : null
	));
	echo $this->Form->input('search', array(
		'label' => 'Search (Name, Seller, Copyright, Description)',
		'value' => !empty($_GET['search']) ? $_GET['search'] : null
	));
	echo $this->Form->end('Filter');
?>

<table>
	<thead>
		<tr>
			<th>App <?php echo $this->element('sorter', array('uri' => 'Scrapes/index', 'field' => 'name')); ?></th>
			<th>Category <?php echo $this->element('sorter', array('uri' => 'Scrapes/index', 'field' => 'category')); ?></th>
			<th>Price <?php echo $this->element('sorter', array('uri' => 'Scrapes/index', 'field' => 'price')); ?></th>
			<th>Released/Updated <?php echo $this->element('sorter', array('uri' => 'Scrapes/index', 'field' => 'updated')); ?></th>
			<th></th>
		</tr>
	</thead>
	<tbody>
		<?php foreach($scrapes as $scrape) { ?>
			<tr>
				<td><?php echo $scrape['Scrape']['name']; ?></td>
				<td><?php echo $scrape['Scrape']['category']; ?></td>
				<td><?php echo $scrape['Scrape']['price']; ?></td>
				<td><?php echo $scrape['Scrape']['updated']; ?></td>
				<td><a class="dialog" href="/Scrapes/view/<?php echo $scrape['Scrape']['id']; ?>">View</a></td>
			</tr>
		<?php } if(empty($scrape)) { ?>
			<tr><td colspan="5">There are currently no scrapes</td></tr>
		<?php } ?>
	</tbody>
</table>

<?php echo $this->element('pager', array('totalItems' => $count, 'uri' => 'Scrapes/index')); ?>