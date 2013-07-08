<a href="/Uploads/update">Update</a>
<!--<a href="/Uploads/reset">Reset</a>-->
<?php foreach($uploads as $upload) { ?>
	<h3><?php echo $upload['Upload']['name']; ?></h3>
	<?php foreach($upload['Image'] as $image) { ?>
		<div class="draggable">
			<img class="resizable" src="<?php echo $image['src']; ?>" />
		</div>
	<?php } ?>
<?php } ?>
<script>
	var z = 1;
	$(function() {
		
		//$(".resizable").resizable();

		$(".draggable").draggable({
			start: function() {
				z++;
				$(this).css('z-index', z);
			}
		});
	});
</script>
<style>
	.draggable { display: inline; }
	.resizable { border: 5px dotted red; }
</style>