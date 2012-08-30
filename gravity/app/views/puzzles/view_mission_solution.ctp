<!doctype html>
<html>
<head>
</head>
<body>
	<script type="text/javascript">
			var isiPhone = navigator.userAgent.toLowerCase().indexOf("iphone");
			var isiPad = navigator.userAgent.toLowerCase().indexOf("ipad");
			var isiPod = navigator.userAgent.toLowerCase().indexOf("ipod");
			if(isiPhone > -1 || isiPad > -1 || isiPod > -1 || 1 == 1)
			{
				document.location = 'spaceflight://viewSolution/<?php echo $puzzle_id; ?>/<?php echo $solution_id; ?>';
				  setTimeout(function(){
				    if(confirm('You do not seem to have iThrown installed, do you want to go download it now?')){
				      document.location = 'http://itunes.apple.com/us/app/tortilla-soup-surfer/id476450448?mt=8';
				    }
				  }, 300);
		
			}else{
				window.location = '/viewSolution/<?php echo $puzzle_id; ?>/<?php echo $solution_id; ?>';
			}
	</script>
</body>
</html>