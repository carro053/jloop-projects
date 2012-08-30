<!doctype html>
<html>
<head>
	<script type="text/javascript" src="/forum/js/jquery-1.5.min.js"></script>
</head>
<body>
	<script type="text/javascript">
		$(document).ready(function(){
			var isiPhone = navigator.userAgent.toLowerCase().indexOf("iphone");
			var isiPad = navigator.userAgent.toLowerCase().indexOf("ipad");
			var isiPod = navigator.userAgent.toLowerCase().indexOf("ipod");
			if(isiPhone > -1 || isiPad > -1 || isiPod > -1 || 1 == 1)
			{
				$('#applink').click();
			}else{
				window.location = '/viewSolution/<?php echo $puzzle_id; ?>/<?php echo $solution_id; ?>';
			}
		});
		function applink(fail){
			if(confirm('Do you want to view this Mission Solution in the Free App?'))
			{
			    return function(){
			        var clickedAt = +new Date;
			        // During tests on 3g/3gs this timeout fires immediately if less than 500ms.
			        setTimeout(function(){
			            // To avoid failing on return to MobileSafari, ensure freshness!
			            if (+new Date - clickedAt < 2000){
			                window.location = fail;
			            }
			        }, 500);    
			    };
			}else{
				window.location = '/viewSolution/<?php echo $puzzle_id; ?>/<?php echo $solution_id; ?>';
			}
		}
	</script>
	<a id="applink" href="spaceflight://viewSolution/<?php echo $puzzle_id; ?>/<?php echo $solution_id; ?>" onclick="applink('itms://itunes.apple.com/us/app/tortilla-soup-surfer/id476450448?mt=8');">open spaceflight with fallback to appstore</a>
</body>
</html>