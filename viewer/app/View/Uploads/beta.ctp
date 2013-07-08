<div id="viewer"></div><!-- end #viewer -->
<a id="btn-reveal" href="#">+</a>
<div id="library">
	<div id="media" class="container">
	<?php foreach($uploads as $upload) { ?>
		<div class="row">
			<h3><?php echo $upload['Upload']['name']; ?></h3>
			<?php foreach($upload['Image'] as $image) { ?>
				<img class="draggable" src="<?php echo $image['src_small']; ?>" />
			<?php } ?>
		</div>
	<?php } ?>
	</div>
</div><!-- end #library -->
<script>
$(document).ready(function(){
	$('a#btn-reveal').click(function(e){
		if ($(this).hasClass('selected')){
			$(this).removeClass('selected').html('<span>+</span>');
			$(this).animate({left: 0});
			$('#library').animate({left: '-500px'});
		} else {
			$(this).addClass('selected').html('<span>-</span>');
			$(this).animate({left: '500px'});
			$('#library').animate({left: 0});
		}
		e.preventDefault();
	});

	$('.draggable').draggable({
		appendTo: 'body',
		cursor: 'pointer',
		helper: 'clone',
		revert: 'invalid',
		start: function(event, ui){
			$(ui.helper).css('z-index', 5000);
		}
	});
	
	$('#viewer').droppable({
		drop: function(event, ui){
			var viewerSurface = $(this);
			if ($(ui.draggable).hasClass('draggable')){
				viewerMedia.init(viewerSurface, ui);
			}
		}
	});
});

var viewerMedia = {
	init: function(viewerSurface, droppedObj){
		var droppedSrc = $(droppedObj.draggable).attr('src');
		var fullSrc = droppedSrc.replace('-s.jpg', '.jpg');
		var viewerPos = viewerSurface.offset();
		var droppedX = droppedObj.position.left - viewerPos.left;
		var droppedY = droppedObj.position.top - viewerPos.top;
		viewerMedia.open(viewerSurface, fullSrc, droppedX, droppedY);
	},
	open: function(viewer, source, xPos, yPos){
		/*$(viewer).append('<div style="left: '+ xPos +'px; top: '+ yPos +'px;" class="draggable-added"><a class="btn-remove" href="#">&times;</a><a class="btn-zoom ui-resizable-handle ui-resizable-se" href="#">&harr;</a><img src="'+ source +'" /></div>');*/
		$(viewer).append('<div style="left: '+xPos+'px; top: '+yPos+'px;" class="draggable-added"><a class="btn-remove" href="#">&times;</a><img src="'+source+'" /></div>');
		viewerMedia.addHandlers();
	},
	addHandlers: function() {
		/*$('.draggable-added').draggable({
			cursor: 'pointer',
			stack:	'.draggable-added'
		}).hover(function(){
			$(this).children('a.btn-remove').stop(true, true).fadeIn(500);
		}, function(){
			$(this).children('a.btn-remove').stop(true, true).fadeOut(500);
		}).resizable({
			aspectRatio: true,
			handles: {'se': '.btn-zoom'}
		});*/
		
		$('.draggable-added').draggable({
			cursor: 'pointer',
			stack:	'.draggable-added'
		}).hover(function(){
			$(this).children('a.btn-remove').stop(true, true).fadeIn(500);
		}, function(){
			$(this).children('a.btn-remove').stop(true, true).fadeOut(500);
		});
		
		$('.draggable-added img').resizable({
			aspectRatio: true/*,
			handles: {'se': '.btn-zoom'}*/
		});
		
		$('.draggable-added a.btn-remove').click(function(e){
			$(this).parent('.draggable-added').remove();
			e.preventDefault();
		});
	}
};
</script>