<script type="text/javascript" src="/js/jquery-1.6.2.min.js"></script>
<?php
srand(ceil(time()/300));
function setAsMaze($x,$y)
{
	global $grid, $wall_list;
	$grid[$x][$y] = 1;
	if(isset($grid[$x-1][$y]) && $grid[$x-1][$y] == 0) $wall_list[] = ($x-1).'-'.($y);
	if(isset($grid[$x+1][$y]) && $grid[$x+1][$y] == 0) $wall_list[] = ($x+1).'-'.($y);
	if(isset($grid[$x][$y-1]) && $grid[$x][$y-1] == 0) $wall_list[] = ($x).'-'.($y-1);
	if(isset($grid[$x][$y+1]) && $grid[$x][$y+1] == 0) $wall_list[] = ($x).'-'.($y+1);
	return true;
}

$grid = array();
if(isset($_GET['width']))
{
	$width = $_GET['width'];
}else{
	$width = 50;
}
if(isset($_GET['height']))
{
	$height = $_GET['height'];
}else{
	$height = 50;
}
if(isset($_GET['thickness']))
{
	$thickness = $_GET['thickness'];
}else{
	$thickness = 20;
}
for($x=0;$x<$width;$x++):
	$grid[$x] = array();
	for($y=0;$y<$height;$y++):
		$grid[$x][$y] = 0;
	endfor;
endfor;
$wall_list = array();
//setAsMaze($width/2-1,$height/2-1);
setAsMaze(0,0);

while(count($wall_list) > 0)
{
	$random_wall_index = array_rand($wall_list);
	$random_wall = explode('-',$wall_list[$random_wall_index]);
	$x = $random_wall[0];
	$y = $random_wall[1];
	if(isset($grid[$x-1][$y]) && $grid[$x-1][$y] == 1 && isset($grid[$x+1][$y]) && $grid[$x+1][$y] == 0)
	{
		$grid[$x][$y] = 1;
		setAsMaze($x+1,$y);
	}elseif(isset($grid[$x+1][$y]) && $grid[$x+1][$y] == 1 && isset($grid[$x-1][$y]) && $grid[$x-1][$y] == 0)
	{
		$grid[$x][$y] = 1;
		setAsMaze($x-1,$y);
	}elseif(isset($grid[$x][$y-1]) && $grid[$x][$y-1] == 1 && isset($grid[$x][$y+1]) && $grid[$x][$y+1] == 0)
	{
		$grid[$x][$y] = 1;
		setAsMaze($x,$y+1);
	}elseif(isset($grid[$x][$y+1]) && $grid[$x][$y+1] == 1 && isset($grid[$x][$y-1]) && $grid[$x][$y-1] == 0)
	{
		$grid[$x][$y] = 1;
		setAsMaze($x,$y-1);
	}
	unset($wall_list[$random_wall_index]);
}


?>
<div style="position:absolute;background-color:#000;padding:<?php echo $thickness; ?>px;width:<?php echo ($width-1) * $thickness; ?>px;height:<?php echo ($height-1) * $thickness; ?>px;">
<?php 
foreach($grid as $x=>$row): 
	foreach($row as $y=>$box):
		if($box == 1)
		{
			$color = '#000';
			$class = 'maze';
		}
		if($box == 0)
		{
			$color = '#000';
			$class = 'wall';
		}
		echo '<div visited="0" id="'.$x.'-'.$y.'" class="'.$class.'" truecolor="white" style="width:'.$thickness.'px;height:'.$thickness.'px;position:absolute;background-color:'.$color.';left:'.(($x+1)*$thickness).'px;top:'.(($y+1)*$thickness).'px;"></div>';
	endforeach;
endforeach; 
?>
</div>


<script type="text/javascript">
	var currentX = 0;
	var currentY = 0;

	var previousX = 0;
	var previousY = 0;
	var interval;
	$(document).ready(function(){
		
		$('#'+currentX+'-'+currentY).attr('truecolor','blue');
		$('#'+currentX+'-'+currentY).css('background-color','#0000FF');
		for(var x=-3;x<4;x++)
		{
			for(var y=-3;y<4;y++)
			{
				if($('#'+(currentX + x)+'-'+(currentY + y)).is('.maze'))
				{
					if($('#'+(currentX + x)+'-'+(currentY + y)).attr('truecolor') == 'blue')
					{
						$('#'+(currentX + x)+'-'+(currentY + y)).css('background-color','#0000FF');
					}
					if($('#'+(currentX + x)+'-'+(currentY + y)).attr('truecolor') == 'red')
					{
						$('#'+(currentX + x)+'-'+(currentY + y)).css('background-color','#FF0000');
					}
					if($('#'+(currentX + x)+'-'+(currentY + y)).attr('truecolor') == 'white')
					{
						$('#'+(currentX + x)+'-'+(currentY + y)).css('background-color','#FFFFFF');
					}
				}
			}
		}
		//interval = setInterval( "moveInMaze()", 20 );
		document.onkeydown = checkKey;
	});
	
	function checkKey(e) {
    	e = e || window.event;
    	//alert(e.keyCode);
    	
		var xDirection = 0;
		var yDirection = 0;
    	if(e.keyCode == 37)
    	{
    		if($('#'+(currentX - 1)+'-'+(currentY + 0)).is('.maze'))
    		{
    			xDirection = -1;
    		}
    	}
    	if(e.keyCode == 38)
    	{
    		if($('#'+(currentX + 0)+'-'+(currentY - 1)).is('.maze'))
    		{
    			yDirection = -1;
    		}
    	}
    	if(e.keyCode == 39)
    	{
    		if($('#'+(currentX + 1)+'-'+(currentY + 0)).is('.maze'))
    		{
    			xDirection = 1;
    		}
    	}
    	if(e.keyCode == 40)
    	{
    		if($('#'+(currentX + 0)+'-'+(currentY + 1)).is('.maze'))
    		{
    			yDirection = 1;
    		}
    	}
    	if(xDirection != 0 || yDirection != 0)
    	{
    		var currentPreviousX = currentX;
			var currentPreviousY = currentY;
			previousX = currentX;
			previousY = currentY;
			currentX = currentPreviousX + xDirection;
			currentY = currentPreviousY + yDirection;
			if(parseInt($('#'+previousX+'-'+previousY).attr('visited')) > 0 && parseInt($('#'+currentX+'-'+currentY).attr('visited')) != 0)
			{
				$('#'+previousX+'-'+previousY).attr('truecolor','white');
				//$('#'+previousX+'-'+previousY).css('background-color','#FFFFFF');
			}else{
				$('#'+previousX+'-'+previousY).attr('truecolor','red');
				//$('#'+previousX+'-'+previousY).css('background-color','#FF0000');
			}
			$('#'+currentX+'-'+currentY).attr('truecolor','blue');
			if(currentX == <?php echo $width - 2; ?> && currentY == <?php echo $height - 2; ?>)
			{
				$('.maze').each(function(index) {
		    		if($(this).attr('truecolor') == 'blue')
					{
						$(this).css('background-color','#0000FF');
					}
					if($(this).attr('truecolor') == 'red')
					{
						$(this).css('background-color','#FF0000');
					}
					if($(this).attr('truecolor') == 'white')
					{
						$(this).css('background-color','#FFFFFF');
					}
				});
			}else{
				$('.maze').each(function(index) {
		    		$(this).css('background-color','#000000');
				});
				for(var x=-3;x<4;x++)
				{
					for(var y=-3;y<4;y++)
					{
						if($('#'+(currentX + x)+'-'+(currentY + y)).is('.maze'))
						{
							if($('#'+(currentX + x)+'-'+(currentY + y)).attr('truecolor') == 'blue')
							{
								$('#'+(currentX + x)+'-'+(currentY + y)).css('background-color','#0000FF');
							}
							if($('#'+(currentX + x)+'-'+(currentY + y)).attr('truecolor') == 'red')
							{
								$('#'+(currentX + x)+'-'+(currentY + y)).css('background-color','#FF0000');
							}
							if($('#'+(currentX + x)+'-'+(currentY + y)).attr('truecolor') == 'white')
							{
								$('#'+(currentX + x)+'-'+(currentY + y)).css('background-color','#FFFFFF');
							}
						}
					}
				}
			}
    	}
	}

	
	
	function moveInMaze()
	{

		var paths = 0;
		if($('#'+(currentX + 1)+'-'+currentY).is('.maze')) paths++;
		if($('#'+(currentX - 1)+'-'+currentY).is('.maze')) paths++;
		if($('#'+(currentX)+'-'+(currentY + 1)).is('.maze')) paths++;
		if($('#'+(currentX)+'-'+(currentY - 1)).is('.maze')) paths++;
		if(paths > 1 || (currentX == previousX && currentY == previousY))
		{
			//Not A Dead End
			var minimumVisited = 1000;
			if((currentX + 1 != previousX || currentY != previousY) && $('#'+(currentX + 1)+'-'+currentY).is('.maze') && parseInt($('#'+(currentX + 1)+'-'+currentY).attr('visited')) < minimumVisited) minimumVisited = parseInt($('#'+(currentX + 1)+'-'+currentY).attr('visited'));
			if((currentX - 1 != previousX || currentY != previousY) && $('#'+(currentX - 1)+'-'+currentY).is('.maze') && parseInt($('#'+(currentX - 1)+'-'+currentY).attr('visited')) < minimumVisited) minimumVisited = parseInt($('#'+(currentX - 1)+'-'+currentY).attr('visited'));
			if((currentX != previousX || currentY + 1 != previousY) && $('#'+(currentX)+'-'+(currentY + 1)).is('.maze') && parseInt($('#'+currentX+'-'+(currentY + 1)).attr('visited')) < minimumVisited) minimumVisited = parseInt($('#'+currentX+'-'+(currentY + 1)).attr('visited'));
			if((currentX != previousX || currentY - 1 != previousY) && $('#'+(currentX)+'-'+(currentY - 1)).is('.maze') && parseInt($('#'+currentX+'-'+(currentY - 1)).attr('visited')) < minimumVisited) minimumVisited = parseInt($('#'+currentX+'-'+(currentY - 1)).attr('visited'));
			var directionFound = 0;
			while(!directionFound)
			{
				var direction = Math.floor(Math.random()*4);
				var xDirection = 0;
				var yDirection = 0;
				if(direction == 0)
				{
					xDirection = -1;
				}else if(direction == 1)
				{
					xDirection = 1;
				}else if(direction == 2)
				{
					yDirection = -1;
				}else if(direction == 3)
				{
					yDirection = 1;
				}
				
				if((currentX + xDirection != previousX || currentY + yDirection != previousY) && $('#'+(currentX + xDirection)+'-'+(currentY + yDirection)).is('.maze') && parseInt($('#'+(currentX + xDirection)+'-'+(currentY + yDirection)).attr('visited')) == minimumVisited)
				{
					directionFound = 1;
					var currentPreviousX = currentX;
					var currentPreviousY = currentY;
					previousX = currentX;
					previousY = currentY;
					currentX = currentPreviousX + xDirection;
					currentY = currentPreviousY + yDirection;
				}
			}
			if(parseInt($('#'+previousX+'-'+previousY).attr('visited')) > 0 && parseInt($('#'+currentX+'-'+currentY).attr('visited')) != 0)
			{
				$('#'+previousX+'-'+previousY).attr('truecolor','white');
				//$('#'+previousX+'-'+previousY).css('background-color','#FFFFFF');
			}else{
				$('#'+previousX+'-'+previousY).attr('truecolor','red');
				//$('#'+previousX+'-'+previousY).css('background-color','#FF0000');
			}
		}else{
			//Dead End
			var currentPreviousX = previousX;
			var currentPreviousY = previousY;
			previousX = currentX;
			previousY = currentY;
			currentX = currentPreviousX;
			currentY = currentPreviousY;
			$('#'+previousX+'-'+previousY).attr('truecolor','white');
			//$('#'+previousX+'-'+previousY).css('background-color','#FFFFFF');
			<?php if(isset($_GET['bryce'])) { ?>
			alert('BAM!');
			<?php } ?>
		}
		$('#'+previousX+'-'+previousY).attr('visited',parseInt($('#'+previousX+'-'+previousY).attr('visited')) + 1);
		$('#'+currentX+'-'+currentY).attr('truecolor','blue');
		//$('#'+currentX+'-'+currentY).css('background-color','#0000FF');
		if(currentX == <?php echo $width - 2; ?> && currentY == <?php echo $height - 2; ?>)
		{
			clearInterval(interval);
			console.log('end');
		}
		$('.maze').each(function(index) {
    		$(this).css('background-color','#000000');
		});
		for(var x=-3;x<4;x++)
		{
			for(var y=-3;y<4;y++)
			{
				if($('#'+(currentX + x)+'-'+(currentY + y)).is('.maze'))
				{
					if($('#'+(currentX + x)+'-'+(currentY + y)).attr('truecolor') == 'blue')
					{
						$('#'+(currentX + x)+'-'+(currentY + y)).css('background-color','#0000FF');
					}
					if($('#'+(currentX + x)+'-'+(currentY + y)).attr('truecolor') == 'red')
					{
						$('#'+(currentX + x)+'-'+(currentY + y)).css('background-color','#FF0000');
					}
					if($('#'+(currentX + x)+'-'+(currentY + y)).attr('truecolor') == 'white')
					{
						$('#'+(currentX + x)+'-'+(currentY + y)).css('background-color','#FFFFFF');
					}
				}
			}
		}
		
	}
</script>