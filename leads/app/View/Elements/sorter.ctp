<?php
//$uri
//$field

$direction = !empty($_GET['direction']) ? $_GET['direction'] : 'asc';
$opposite_direction = $direction == 'asc' ? 'desc' : 'asc';

$params = '';
foreach($_GET as $key => $value) {
	if($key != 'order' && $key != 'direction') {
		$params .= '&'.$key.'='.$value;
	}
}

$link = '/'.$uri.'?order='.$field.'&direction='.$opposite_direction.$params;

$arrow = '';
if(!empty($_GET['order']) && $_GET['order'] == $field) {
	$arrow = $direction == 'asc' ? '&uarr;' : '&darr;';
}
?>
<a href="<?php echo $link; ?>"><?php echo $arrow; ?></a>

