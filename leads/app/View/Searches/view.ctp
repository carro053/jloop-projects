

<table>
	<thead>
		<th><input type="checkbox" checked="checked" /></th>
		<th>App Store Link</th>
	</thead>
	<tbody>
		<?php foreach($search['Result'] as $result) { ?>
			<tr>
				<td><input type="checkbox" checked="checked" /></td>
				<td><a href="<?php echo $result['itunes_link']; ?>" target="_blank"><?php echo $result['itunes_link']; ?></a></td>
			</tr>
		<?php } if(empty($search['Result'])) { ?>
			<tr><td colspan="2">There are no results from this search.</td></tr>
		<?php } ?>
	</tbody>
</table>

<?php pr($search); ?>