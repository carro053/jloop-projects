<table>
	<thead>
		<th><input type="checkbox" checked="checked" onclick="toggleSelectAll(this);" /></th>
		<th>App Store Link</th>
	</thead>
	<tbody id="search-results">
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

<script type="text/javascript">
	function toggleSelectAll(checkbox) {
		if($(checkbox).attr('checked'))
		{
			console.log('checked');
			$('#search-results tr input').attr('checked', 'checked');
		}
		else
		{
			console.log('NOT checked');
			$('#search-results tr input').removeAttr('checked');
		}
	}
</script>