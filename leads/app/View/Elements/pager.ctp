<?php
//$totalItems
//$uri

$itemsPerPage = !empty($_GET['limit']) ? $_GET['limit'] : 50;

$currentPage = !empty($_GET['page']) ? $_GET['page'] : 1;

$params = '';

foreach($_GET as $key => $value) {
	if(is_array($value)) {
		foreach($value as $v) {
			$params .= '&'.$key.'[]='.$v;
		}
	}elseif($key != 'page') {
		$params .= '&'.$key.'='.$value;
	}
}

function formatLink($_page = 1, $_uri = '', $_params = '') {
	return '/'.$_uri.'?page='.$_page.$_params;
}

$lastPage = ceil( $totalItems / $itemsPerPage );
?>
<style type="text/css">
	.pager {
		float: right;
	}
	.pager label {
		display: inline;
	}
	.pager select {
		vertical-align: inherit;
	}
</style>
<script type="text/javascript">
	function goToPage(page) {
		window.location = '<?php echo '/'.$uri.'?page=\'+page+\''.$params;; ?>';
	}
</script>
<div class="pager">
	<?php
		if( $currentPage - 1 > 0 ) {
			echo '<a class="button" href="' . formatLink( ( $currentPage - 1 ), $uri, $params ) . '">Prev</a>';
		}
		if( $currentPage + 1 <= $lastPage ) {
			echo '<a class="button blue" href="' . formatLink( ( $currentPage + 1 ), $uri, $params ) . '">Next</a>';
		}
	?>
	<label>Go to page</label>
	<select onchange="goToPage(this.value)">
		<?php
			for($p = 1; $p <= $lastPage; $p++) {
				echo '<option value="'.$p.'"'.($p == $currentPage ? ' selected' : '').'>'.$p.'</option>';
			}
		?>
	</select>
</div>