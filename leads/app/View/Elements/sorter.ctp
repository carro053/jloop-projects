<?php
//$uri
//$field

$direction = !empty($_GET['direction']) ? $_GET['direction'] : 'asc';
$opposite_direction = $direction == 'asc' ? 'desc' : 'asc';

$params = '';
foreach($_GET as $key => $value) {
	if(is_array($value)) {
		foreach($value as $v) {
			$params .= '&'.$key.'[]='.$v;
		}
	}elseif($key != 'order' && $key != 'direction') {
		$params .= '&'.$key.'='.$value;
	}
}

$link = '/'.$uri.'?order='.$field.'&direction='.$opposite_direction.$params;

$arrow = '<span class="ui-icon ui-icon-minus"></span>';
if(!empty($_GET['order']) && $_GET['order'] == $field) {
	$arrow = $direction == 'asc' ? '<span class="ui-icon ui-icon-arrowreturnthick-1-n"></span>' : '<span class="ui-icon ui-icon-arrowreturnthick-1-s"></span>';
}
?>
<a href="<?php echo $link; ?>"><?php echo $arrow; ?></a>

