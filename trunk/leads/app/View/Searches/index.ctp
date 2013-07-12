<table>
	<thead>
		<th>Search Terms</th>
		<th>iPhone 5 Status</th>
		<th>iPad-only</th>
		<th>Date Range</th>
	</thead>
	<tbody>
		<?php foreach($searches as $search) { ?>
			<tr>
				<td><?php echo $search['Search']['search_terms']; ?></td>
				<td><?php echo $search['Search']['is_not_iphone_5'] ? 'Not iPhone 5 Optimized' : 'iPhone 5 Optimized'; ?></td>
				<td><?php echo $search['Search']['is_not_ipad_only'] ? 'Not iPad-only' : 'iPad-only'; ?></td>
				<td><?php echo $search['Search']['use_date_range'] ? 'YES' : 'NOPE'; ?></td>
			</tr>
		<?php } if(empty($searches)) { ?>
			<tr><td colspan="4">There are currently no searches</td></tr>
		<?php } ?>
	</tbody>
</table>

<?php pr($searches); ?>