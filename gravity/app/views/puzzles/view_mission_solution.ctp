<!doctype html>
<html>
<head>
	<title><?php echo $data['title']; ?></title>
    <script type="text/javascript">
    
		var isiPhone = navigator.userAgent.toLowerCase().indexOf("iphone");
		var isiPad = navigator.userAgent.toLowerCase().indexOf("ipad");
		var isiPod = navigator.userAgent.toLowerCase().indexOf("ipod");
		if(isiPhone > -1 || isiPad > -1 || isiPod > -1)
		{
			document.location = 'spaceflight://viewSolution/<?php echo $puzzle_id; ?>/<?php echo $solution_id; ?>';
			  /*setTimeout(function(){
			    if(confirm('You do not seem to have Space Flight installed, do you want to go download it now?')){
			      document.location = 'http://itunes.apple.com/us/app/tortilla-soup-surfer/id476450448?mt=8';
			    }
			  }, 5000);*/
	
		}
		var Timer = function() {
			this.previousTime = new Date().getTime();
			this.currentTime = new Date().getTime();
		};

		Timer.prototype = {
			getSeconds: function() {
				return (this.currentTime - this.previousTime) / 1000;
			},

			tick: function() {
				this.previousTime = this.currentTime;
				this.currentTime = new Date().getTime();
				return null;
			}
		};
		
		var SpriteSheet = function(sprites) {
			this.sprites = sprites;
		};
		
		SpriteSheet.prototype = {
			getSprite: function(id) {
				for(var i = 0; i < this.sprites.length; i++) {
					if(this.sprites[i].id == id) {
						return this.sprites[i];
					}
				}
				return null;
			}
		};
		
		var SpriteSequence = function(frames, sprites) {
			this.frames = frames;
			this.sprites = sprites;
			this.currentFrame = 0;
			this.frameDuration = frames[0].t;
		};
		
		SpriteSequence.prototype = {
			animate: function(deltaTime) {
				this.frameDuration -= deltaTime;
				if(this.frameDuration <= 0) {
					this.currentFrame++;
					if(this.currentFrame == this.frames.length) {
						this.currentFrame = 0;
					}
					this.frameDuration = this.frames[this.currentFrame].t;
				}
			},
			
			getFrame: function() {
				return this.sprites.getSprite(this.frames[this.currentFrame].id);
			}
		};
	</script>
</head>
<body>
	<script type="text/javascript">
		var scene = 'select';
		var gameInterval;
		var gameTime = 0;
		var timer = new Timer();
		var beacons = new Array();	
		var planets = new Array();
		var wells = new Array();
		var astronauts = new Array();
		var items = new Array();
		var minSpeed = 125.0
		var gConstant = 6000000.0
		var fuelPower = 14.0
		var saveThreshold = 15
		var moon_density = 0.020
		var minFuel = 0.0
		var maxFuel = 400.0
		var shipMass = 1.0
		var MAXPOINTS = 10000
		var point_threshold = 40
		var gDistanceConstant = 0.85
		var abyssDistance = 1300
		var currentSpeed = minSpeed;
		var total_travel_time = 0;
		var total_fuel_spent = 0;
		var time_locations = new Array();
		var images = new Array();
		
		
        var xOrbit = 11.0 / 8.0;
        var yOrbit = 3.0 / 2.0;
        var theMoonRadius = 2.0 / 7.0;
		<?php foreach($data['way_points'] as $beacon): ?>
		addBeacon(<?php echo ($beacon['x']); ?>,<?php echo (768 - $beacon['y']); ?>);
		<?php endforeach; ?>
		//var soundExplosion = new Audio('/explosion.mp3');
		var shipSpritesheet = new SpriteSheet(
			[
				{id: 1, x:  0, y:  0, w: 39, h: 40},
				{id: 2, x: 39, y:  0, w: 39, h: 40},
				{id: 3, x: 78, y:  0, w: 39, h: 40},
				{id: 4, x:  0, y: 40, w: 39, h: 40},
				{id: 5, x: 39, y: 40, w: 39, h: 40},
				{id: 6, x: 78, y: 40, w: 39, h: 40}
			]
		);
		
		var shipSprites = new Image();
		shipSprites.src = '/ship_sprites.png';

		
		
		
		
		var canvasFront = new Object();
		var contextFront = new Object();
		var canvasScene = new Object();
		var contextScene = new Object();
		var canvasBack = new Object();
		var contextBack = new Object();
		var canvasUI = new Object();
		var contextUI = new Object();
		
		var planetImage = new Image();
		planetImage.src = "/img/planet_4.png";
		
		
		var wellImage = new Image();
		wellImage.src = "/img/gravity_well.png";
		
		var antiImage = new Image();
		antiImage.src = "/img/anti_gravity.png";
		
		var astroImage = new Image();
		astroImage.src = "/img/astronaut.png";
		
		var fuelImage = new Image();
		fuelImage.src = "/img/fuel.png";
		
		var beacon_1Image = new Image();
		beacon_1Image.src = "/img/beacon_1.png";
		
		var beacon_2Image = new Image();
		beacon_2Image.src = "/img/beacon_2.png";
		
		var space_stationImage = new Image();
		space_stationImage.src = "/img/space_station.png";
		
		var shipImage = new Image();
		shipImage.src = "/img/fury.png";
		
		window.onload = function() {
			
			canvasBack = document.getElementById('canvasBack');
			contextBack = canvasBack.getContext('2d');			
			
			canvasScene = document.getElementById('canvasScene');
			contextScene = canvasScene.getContext('2d');
			
			canvasFront = document.getElementById('canvasFront');
			contextFront = canvasFront.getContext('2d');
			
			
			canvasUI = document.getElementById('canvasUI');
			contextUI = canvasUI.getContext('2d');
			
			canvasUI.onmousedown = function(e) {
			};
			
			canvasUI.onmousemove = function(e) {
			};
			
			shipSprites.onload = initialize();
		};
		function initialize()
		{
			scene = 'select';
			reset_game();
			contextUI.clearRect(0, 0, canvasUI.width, canvasUI.height);
			contextFront.clearRect(0, 0, canvasFront.width, canvasFront.height);
			drawBackground();
			startGame();
		}
		
		function startGame()
		{
			reset_game();
			drawUI();
			timer.tick();
			gameInterval = setInterval(gameLoop, 33);
		}
		function drawBackground()
		{
			
			<?php foreach($data['planets'] as $planet): ?>
			contextScene.save();
			contextScene.scale(<?php echo ($planet['radius'] / 70 / 2); ?>, <?php echo ($planet['radius'] / 70 / 2); ?>);
			contextScene.drawImage(<?php if($planet['antiGravity']) { echo 'antiImage'; }else{ echo 'planetImage'; } ?>, <?php echo (($planet['x'] - $planet['radius']) / ($planet['radius'] / 70 / 2)); ?>, <?php echo ((768 - $planet['y'] - $planet['radius']) / ($planet['radius'] / 70 / 2)); ?>);
			contextScene.restore();
			
			addPlanet(<?php echo $planet['x']; ?>,<?php echo (768 - $planet['y']); ?>,<?php echo $planet['radius']; ?>,<?php echo $planet['density']; ?>,<?php echo $planet['antiGravity']; ?>,<?php echo $planet['hasMoon']; ?>,<?php echo $planet['moonAngle']; ?>);
			<?php endforeach; ?>
			
			contextScene.save();
			contextScene.scale(<?php echo (1); ?>, <?php echo (1); ?>);
			contextScene.drawImage(space_stationImage, <?php echo (($data['startData']['0'] - 16) / (1)); ?>, <?php echo ((768 - $data['startData']['1'] - 16) / (1)); ?>);
			contextScene.restore();
			
			contextScene.save();
			contextScene.scale(<?php echo (1); ?>, <?php echo (1); ?>);
			contextScene.drawImage(space_stationImage, <?php echo (($data['endData']['0'] - 16) / (1)); ?>, <?php echo ((768 - $data['endData']['1'] - 16) / (1)); ?>);
			contextScene.restore();
			
			<?php foreach($data['wells'] as $well): ?>
			
			contextScene.save();
			contextScene.scale(<?php echo (1 / 2); ?>, <?php echo (1 / 2); ?>);
			contextScene.drawImage(wellImage, <?php echo (($well['x'] - 32) / (1 / 2)); ?>, <?php echo ((768 - $well['y'] - 32) / (1 / 2)); ?>);
			contextScene.restore();
			addWell(<?php echo $well['x']; ?>,<?php echo (768 - $well['y']); ?>,<?php echo $well['power']; ?>);
			<?php endforeach; ?>
			
			
			<?php foreach($data['astronauts'] as $astro): ?>
			addAstronaut(<?php echo ($astro['x']); ?>, <?php echo (768 - $astro['y']); ?>);
			<?php endforeach; ?>
			<?php foreach($data['items'] as $item): ?>
			addItem(<?php echo ($item['x']); ?>, <?php echo (768 - $item['y']); ?>);
			<?php endforeach; ?>
			
			var temp_beacons = new Array();
			var temp_beacon_start = new Object();
			temp_beacon_start.x = <?php echo (($data['startData']['0']) / (1)); ?>;
			temp_beacon_start.y = <?php echo ((768 - $data['startData']['1']) / (1)); ?>;
			temp_beacons.push(temp_beacon_start);
			for(b in beacons)
			{
				temp_beacons.push(beacons[b]);
			}
			var temp_beacon_end = new Object();
			temp_beacon_end.x = <?php echo (($data['endData']['0']) / (1)); ?>;
			temp_beacon_end.y = <?php echo ((768 - $data['endData']['1']) / (1)); ?>;
			temp_beacons.push(temp_beacon_end);
			
			while(temp_beacons.length < 4)
			{
            	temp_beacons.push(temp_beacons[temp_beacons.length - 1]);
            }
        	var sz = temp_beacons.length;
        	var	error = 50;
        	currentSpeed = minSpeed;
        	if (sz > 0)
        	{
        		fitCurvePoints(temp_beacons,sz,error);
        	}
		}
		
		function addPlanet(x,y,radius,density,antiGravity,hasMoon,moonAngle)
		{
			var planet = new Object();
			planet.x = x;
			planet.y = y;
			planet.radius = radius;
			planet.density = density;
			planet.antiGravity = antiGravity;
			planet.hasMoon = hasMoon;
			planet.moonAngle = moonAngle;
			planets.push(planet);
		}
		
		function addWell(x,y,power)
		{
			var well = new Object();
			well.x = x;
			well.y = y;
			well.power = power;
			wells.push(well);
		}
		
		function addItem(x,y)
		{
			var item = new Object();
			item.x = x;
			item.y = y;
			item.collected = 0;
			items.push(item);
		}
		
		function addAstronaut(x,y)
		{
			var astronaut = new Object();
			astronaut.x = x;
			astronaut.y = y;
			astronaut.collected = 0;
			astronauts.push(astronaut);
		}
		
		function addBeacon(x,y)
		{
			var beacon = new Object();
			beacon.x = x;
			beacon.y = y;
			beacons.push(beacon);
		}
		
		function updateBeacons()
		{
			var seconds = new Date().getSeconds()
			
			for(var b in beacons)
			{
				contextFront.save();
				contextFront.scale(<?php echo (0.5); ?>, <?php echo (0.5); ?>);
				if(seconds % 2 == 0)
				{
					contextFront.drawImage(beacon_1Image, (beacons[b].x - 11) / 0.5, (beacons[b].y - 11) / 0.5);
				}else{
					contextFront.drawImage(beacon_2Image, (beacons[b].x - 11) / 0.5, (beacons[b].y - 11) / 0.5);
				}
				contextFront.restore();
			}
		}
		
		function drawUI()
		{
		
		}

		function gameLoop()
		{
			updateObjects();
			clearCanvas();
			drawObjects();
			drawUI();
			timer.tick();
		}

		function updateObjects()
		{	
			gameTime += timer.getSeconds();
		}
		function clearCanvas()
		{
			contextFront.clearRect(0, 0, canvasFront.width, canvasFront.height);
		}

		function drawObjects()
		{
			updateBeacons();
			var found = false;
			for(t in time_locations)
			{
				if(time_locations[t].timestamp < gameTime)
				{
					for(a in astronauts)
					{
						if(astronauts[a].collected == 0)
						{
							var separation = Math.sqrt(Math.pow(astronauts[a].x - time_locations[t].x,2) + Math.pow(astronauts[a].y - time_locations[t].y,2));
	        				if(separation < saveThreshold)
	        				{
	            				astronauts[a].collected = 1;
	            			}
						}
					}
					for(i in items)
					{
						if(items[i].collected == 0)
						{
							var separation = Math.sqrt(Math.pow(items[i].x - time_locations[t].x,2) + Math.pow(items[i].y - time_locations[t].y,2));
	        				if(separation < saveThreshold)
	        				{
	            				items[i].collected = 1;
	            			}
						}
					}
				}else{
					contextFront.save();
					contextFront.scale(0.5, 0.5);
					contextFront.translate((time_locations[t].x) / 0.5, (time_locations[t].y) / 0.5);
					contextFront.rotate(2 * Math.PI - time_locations[t].angle + Math.PI / 2);
					contextFront.drawImage(shipImage, -22, -18);
					contextFront.restore();
					found = true;					
					break;					
				}
				
			}
			for(a in astronauts)
			{
				if(astronauts[a].collected == 0)
				{
					contextFront.save();
					contextFront.scale(0.5, 0.5);
					contextFront.drawImage(astroImage, (astronauts[a].x - 8) / (0.5), (astronauts[a].y - 12) / (0.5));
					contextFront.restore();
				}
			}
			for(i in items)
			{
				if(items[i].collected == 0)
				{
					contextFront.save();
					contextFront.scale(0.5, 0.5);
					contextFront.drawImage(fuelImage, (items[i].x - 8) / (0.5), (items[i].y - 12) / (0.5));
					contextFront.restore();
				}
			}
			if(!found)
			{
				reset_game();
			}
		}
		function reset_game()
		{
			for(a in astronauts)
			{
				astronauts[a].collected = 0;
			}
			for(i in items)
			{
				items[i].collected = 0;
			}
			gameTime = 0.0;
			timer.previousTime = new Date().getTime();
			timer.currentTime = new Date().getTime();
		}
		
function drawBezierCurve(n,curve)
{
    
    var t;
    var i;
    var steps;
    var length = 0.0;
    var total_time = 0.0;
    var dot = new Object();
    dot.x = 0.0;
    dot.y = 0.0;
    var previous_dot = new Object();
    previous_dot.x = 0.0;
    previous_dot.y = 0.0;
    var previous_previous_dot = new Object();
    previous_previous_dot.x = 0.0;
    previous_previous_dot.y = 0.0;
    steps = 100;
    var running_time = 0;
    var running_fuel = 0;
    for (i = 0; i <= steps; i++) {
        var this_length;
        t = i / steps;
        dot.x = bezierPointT(t,curve[0].x,curve[1].x,curve[2].x,curve[3].x);
        dot.y = bezierPointT(t,curve[0].y,curve[1].y,curve[2].y,curve[3].y);
        if (i > 1)
        {
            
            var x_diff = previous_dot.x - previous_previous_dot.x;
            var y_diff = previous_dot.y - previous_previous_dot.y;
            length += Math.sqrt(x_diff * x_diff + y_diff * y_diff);
            this_length = Math.sqrt(x_diff * x_diff + y_diff * y_diff);
            if((previous_dot.x - previous_previous_dot.x != 0.0 || previous_dot.y - previous_previous_dot.y != 0.0) && (dot.x - previous_dot.x != 0.0 || dot.y - previous_dot.y != 0.0))
            {
                point = new Array();
                point.push(previous_dot.x);
                point.push(previous_dot.y);
                var travel_time = this_length / currentSpeed;
                total_time += travel_time;
                total_travel_time += travel_time;
                var u_x = (previous_dot.x - previous_previous_dot.x) / Math.sqrt(Math.pow(previous_dot.x - previous_previous_dot.x, 2) + Math.pow(previous_dot.y - previous_previous_dot.y, 2));
                var u_y = (previous_dot.y - previous_previous_dot.y) / Math.sqrt(Math.pow(previous_dot.x - previous_previous_dot.x, 2) + Math.pow(previous_dot.y - previous_previous_dot.y, 2));
                
				var new_v_x = u_x * currentSpeed;
                var new_v_y = u_y * currentSpeed;
                var gx = 0.0;
                var gy = 0.0;
                for(var p in planets)
                {
                    var planetX = planets[p].x;
                    var planetY = planets[p].y;
                    var planetRadius = planets[p].radius;
                    var planetDensity = planets[p].density;
                    var planetMass = planetDensity * 4 / 3 * Math.PI * Math.pow(planetRadius, 3);
                    var gravity = gConstant * planetMass * shipMass / Math.pow(Math.sqrt(Math.pow(previous_dot.x - planetX,2) + Math.pow(previous_dot.y - planetY,2)) * gDistanceConstant, 2);
                    if(planets[p].antiGravity)
                        gravity = gravity * -1.0;
                    var g_x = (previous_dot.x - planetX) / Math.sqrt(Math.pow(previous_dot.x - planetX, 2) + Math.pow(previous_dot.y - planetY, 2));
                    var g_y = (previous_dot.y - planetY) / Math.sqrt(Math.pow(previous_dot.x - planetX, 2) + Math.pow(previous_dot.y - planetY, 2));
                    gx += g_x * gravity;
                    gy += g_y * gravity;
                    if (planets[p].hasMoon) {
                        var currentMoonAngle = planets[p].moonAngle + Math.PI / planets[p].radius * (planets[p].density + 0.012) / 0.03 / 2.0 * 60.0 * total_travel_time;
						var moonX = planets[p].x + Math.cos(currentMoonAngle) * planets[p].radius * xOrbit;
                        var moonY = planets[p].y + Math.sin(currentMoonAngle) * planets[p].radius * yOrbit;
                        var moonRadius = planets[p].radius * theMoonRadius;
                        var moonDensity = moon_density;
                        var moonMass = moonDensity * 4 / 3 * Math.PI * Math.pow(moonRadius, 3);
						var moonGravity = gConstant * moonMass * shipMass / Math.pow(Math.sqrt(Math.pow(previous_dot.x - moonX,2) + Math.pow(previous_dot.y - moonY,2)) * gDistanceConstant, 2);
                        var mg_x = (previous_dot.x - moonX) / Math.sqrt(Math.pow(previous_dot.x - moonX, 2) + Math.pow(previous_dot.y - moonY, 2));
                        var mg_y = (previous_dot.y - moonY) / Math.sqrt(Math.pow(previous_dot.x - moonX, 2) + Math.pow(previous_dot.y - moonY, 2));
                        
						gx += mg_x * moonGravity;
                        gy += mg_y * moonGravity;
                    }
                    
                }
                
                for (var w in wells) {
                    var wellX = wells[w].x;
                    var wellY = wells[w].y;
                    var wellPower = wells[w].power;
                    var gravity = gConstant * wellPower * shipMass / Math.pow(Math.sqrt(Math.pow(previous_dot.x - wellX,2) + Math.pow(previous_dot.y - wellY,2)) * gDistanceConstant,2);
                    var g_x = (previous_dot.x - wellX) / Math.sqrt(Math.pow(previous_dot.x - wellX, 2) + Math.pow(previous_dot.y - wellY, 2));
                    var g_y = (previous_dot.y - wellY) / Math.sqrt(Math.pow(previous_dot.x - wellX, 2) + Math.pow(previous_dot.y - wellY, 2));
                    gx += g_x * gravity;
                    gy += g_y * gravity;
                }
                
                if(gx < 0)
                {
                    new_v_x += Math.sqrt(gx * -1.0 / shipMass) * travel_time;
                }else{
                    new_v_x -= Math.sqrt(gx * 1.0 / shipMass) * travel_time;
                }
                if(gy < 0)
                {
                    new_v_y += Math.sqrt(gy * -1.0 / shipMass) * travel_time;
                }else{
                    new_v_y -= Math.sqrt(gy * 1.0 / shipMass) * travel_time;
                }
                var new_x = new_v_x * travel_time;
                var new_y = new_v_y * travel_time;
                var newSpeed = Math.sqrt(Math.pow(new_v_x,2) + Math.pow(new_v_y,2));
                var n_x = new_x / Math.sqrt(Math.pow(new_x, 2) + Math.pow(new_y, 2));
                var n_y = new_y / Math.sqrt(Math.pow(new_x, 2) + Math.pow(new_y, 2));
                var v_x = (dot.x - previous_dot.x) / Math.sqrt(Math.pow(dot.x - previous_dot.x, 2) + Math.pow(dot.y - previous_dot.y, 2));
                var v_y = (dot.y - previous_dot.y) / Math.sqrt(Math.pow(dot.x - previous_dot.x, 2) + Math.pow(dot.y - previous_dot.y, 2));
                var scalar = newSpeed * (n_x * v_x + n_y * v_y);
                var w = Math.sqrt(Math.pow(newSpeed,2) - Math.pow(scalar,2));
                var fuelSpent = 0;
                if(scalar >= minSpeed)
                {
                    currentSpeed = scalar;
                }else {
                    fuelSpent += Math.pow(minSpeed - scalar,2) * shipMass / 2;
                    currentSpeed = minSpeed;
                }
                if(w > 0)
                {
                	fuelSpent += Math.pow(w,2) * shipMass / 2;;
                }
                fuelSpent /= fuelPower;
                total_fuel_spent += fuelSpent;
                var energyMeter = Math.min(Math.log(fuelSpent / travel_time / 5) / Math.log(10), 2);
                if(energyMeter < 0)
                {
                	energyMeter = 0.0;
                }
                if(energyMeter > 1)
                {
                    energyMeter -= 1;
                    contextScene.strokeStyle = rgbToHex(255,Math.ceil((1 - energyMeter) * 255),0);
                }else{
                    contextScene.strokeStyle = rgbToHex(Math.ceil(energyMeter * 255),255,0);
                }
		        contextScene.beginPath();
		        contextScene.moveTo(previous_previous_dot.x, previous_previous_dot.y);
		        contextScene.lineTo(previous_dot.x, previous_dot.y);
		        contextScene.stroke();
		        var time_location = new Object();
		        time_location.timestamp = total_travel_time - travel_time;
		        time_location.x = previous_dot.x;
		        time_location.y = previous_dot.y;
		        time_location.angle = Math.atan2(v_x, v_y);
		        time_locations.push(time_location);
                if(i == steps)
                {     	
			        contextScene.beginPath();
			        contextScene.moveTo(previous_dot.x, previous_dot.y);
			        contextScene.lineTo(dot.x, dot.y);
			        contextScene.stroke();
			        
			        var last_time_location = new Object();
			        last_time_location.timestamp = total_travel_time;
			        last_time_location.x = previous_dot.x;
			        last_time_location.y = previous_dot.y;
			        last_time_location.angle = Math.atan2(v_x, v_y);
			        time_locations.push(last_time_location);
		       	}
            }
        }
        if(dot.x - previous_dot.x != 0.0 || dot.y - previous_dot.y != 0.0)
        {
            previous_previous_dot.x = previous_dot.x;
            previous_previous_dot.y = previous_dot.y;
            previous_dot.x = dot.x;
            previous_dot.y = dot.y;
        }
    }
}

function componentToHex(c) {
    var hex = c.toString(16);
    return hex.length == 1 ? "0" + hex : hex;
}

function rgbToHex(r, g, b) {
    return "#" + componentToHex(r) + componentToHex(g) + componentToHex(b);
}

function bezierPointT(t,start,control_1,control_2,end) {
    /* Formula from Wikipedia article on Bezier curves. */
    return              start * (1.0 - t) * (1.0 - t)  * (1.0 - t) 
    + 3.0 *  control_1 * (1.0 - t) * (1.0 - t)  * t 
    + 3.0 *  control_2 * (1.0 - t) * t          * t
    +              end * t         * t          * t;
}

function fitCurvePoints(d,nPts,error)
{
    var	tHat1;
    var tHat2;	/*  Unit tangent vectors at endpoints */
    
    tHat1 = computeLeftTangent(d, 0);
    tHat2 = computeRightTangent(d, nPts - 1);
    fitCubicPoints(d,0,nPts - 1,tHat1,tHat2,error);
}

function fitCubicPoints(d,first,last,tHat1,tHat2,error) {
    var bezCurve = new Array(); /*Control points of fitted Bezier curve*/
    var	u;		/*  Parameter values for point  */
    var	uPrime = new Array();	/*  Improved parameter values */
    var	maxError;	/*  Maximum fitting error	 */
    var		nPts;		/*  Number of points in subset  */
    var	iterationError; /*Error below which you try iterating  */
    var		maxIterations = 4; /*  Max times to try iterating  */
    var	tHatCenter;   	/* Unit tangent vector at splitPoint */
    var		i;
    var splitPoint = new Object();
    
    iterationError = error * error;
    nPts = last - first + 1;
    
    /*  Use heuristic if region only has two points in it */
    if (nPts == 2) {
	    var dist = v2DistanceBetween2Points(d[last], d[first]) / 3.0;
		bezCurve[0] = d[first];
		bezCurve[3] = d[last];
		bezCurve[1] = v2Add(bezCurve[0], v2Scale(tHat1, dist));
		bezCurve[2] = v2Add(bezCurve[3], v2Scale(tHat2, dist));
        drawBezierCurve(3,bezCurve);
		return;
    }
    
    /*  Parameterize points, and attempt to fit curve */
    u = chordLengthParameterize(d, first, last);
    bezCurve = generateBezierPoints(d,first,last,u,tHat1,tHat2);
    
    /*  Find max deviation of points to fitted curve */
    maxError = computeMaxErrorPoints(d,first,last,bezCurve,u,splitPoint);
    if (maxError < error) {
        //NSLog(@"Met error criteria --- 3 points");
		drawBezierCurve(3,bezCurve);
		return;
    }
    
    /*  If error not too large, try some reparameterization  */
    /*  and iteration */
    if (maxError < iterationError) {
		for (i = 0; i < maxIterations; i++) {
            uPrime = reparameterizePoints(d,first,last,u,bezCurve);
            bezCurve = generateBezierPoints(d,first,last,uPrime,tHat1,tHat2);
            maxError = computeMaxErrorPoints(d,first,last,bezCurve,uPrime,splitPoint);
	    	if (maxError < error) {
                //NSLog(@"Met error criteria at segment %d --- 3 points", i);
                drawBezierCurve(3,bezCurve);
                return;
            }
            u = uPrime;
        }
    }
    
    /* Fitting failed -- split at max error point and fit recursively */
    //NSLog(@"Fitting failed --- splitting");
    tHatCenter = computeCenterTangent(d, splitPoint.value);
    fitCubicPoints(d,first,splitPoint.value,tHat1,tHatCenter,error);
    tHatCenter = v2Negate(tHatCenter);
    fitCubicPoints(d,splitPoint.value,last,tHatCenter,tHat2,error);
}

function generateBezierPoints(d,first,last,uPrime,tHat1,tHat2) {
    var 	i;
    var 	A = new Array();	/* Precomputed rhs for eqn	*/
    var 	nPts;			/* Number of pts in sub-curve */
    var 	C = new Array();			/* Matrix C		*/
    var 	X = new Array();			/* Matrix X			*/
    var 	det_C0_C1;		/* Determinants of matrices	*/
    var det_C0_X;
    var det_X_C1;
    var 	alpha_l;	/* Alpha values, left and right	*/
    var alpha_r;
    var 	tmp;			/* Utility variable		*/
    var	bezCurve = new Array();	/* RETURN bezier curve ctl pts	*/
    
    bezCurve[0] = new Object();
    bezCurve[3] = new Object();
    nPts = last - first + 1;
    
    
    /* Compute the A's	*/
    for (i = 0; i < nPts; i++) {
		var		v1, v2;
		v1 = tHat1;
		v2 = tHat2;
		v1 = v2Scale(v1, b1(uPrime[i]));
		v2 = v2Scale(v2, b2(uPrime[i]));
		A[i] = new Array();
		A[i][0] = v1;
		A[i][1] = v2;
    }
    
    /* Create the C and X matrices	*/
    C[0] = new Array();
    C[0][0] = 0.0;
    C[0][1] = 0.0;
    C[1] = new Array();
    C[1][0] = 0.0;
    C[1][1] = 0.0;
    X[0]    = 0.0;
    X[1]    = 0.0;
    
    for (i = 0; i < nPts; i++) {
        C[0][0] += v2Dot(A[i][0], A[i][0]);
		C[0][1] += v2Dot(A[i][0], A[i][1]);
        /*					C[1][0] += V2Dot(&A[i][0], &A[i][1]);*/	
		C[1][0] = C[0][1];
		C[1][1] += v2Dot(A[i][1], A[i][1]);
        
		tmp = v2SubII(d[first + i],
                      v2AddII(
                              v2ScaleIII(d[first], b0(uPrime[i])),
                              v2AddII(
                                      v2ScaleIII(d[first], b1(uPrime[i])),
                                      v2AddII(
                                              v2ScaleIII(d[last], b2(uPrime[i])),
                                              v2ScaleIII(d[last], b3(uPrime[i]))))));
        
        
        X[0] += v2Dot(A[i][0], tmp);
        X[1] += v2Dot(A[i][1], tmp);
    }
    /* Compute the determinants of C and X	*/
    det_C0_C1 = C[0][0] * C[1][1] - C[1][0] * C[0][1];
    det_C0_X  = C[0][0] * X[1]    - C[1][0] * X[0];
    det_X_C1  = X[0]    * C[1][1] - X[1]    * C[0][1];
    /* Finally, derive alpha values	*/
    if(det_C0_C1 == 0)
    {
    	alpha_l = 0.0;
    	alpha_r = 0.0;
    }else{
	    alpha_l = det_X_C1 / det_C0_C1;
	    alpha_r = det_C0_X / det_C0_C1;
    }
    /* If alpha negative, use the Wu/Barsky heuristic (see text) */
    /* (if alpha is 0, you get coincident control points that lead to
     * divide by zero in any subsequent NewtonRaphsonRootFind() call. */
    var segLength = v2DistanceBetween2Points(d[last], d[first]);
    var epsilon = 1.0e-6 * segLength;
    if (alpha_l < epsilon || alpha_r < epsilon)
    {
		/* fall back on standard (probably inaccurate) formula, and subdivide further if needed. */
		var dist = segLength / 3.0;
	    bezCurve[0].x = d[first].x;
	    bezCurve[0].y = d[first].y;
	    bezCurve[3].x = d[last].x;
	    bezCurve[3].y = d[last].y;
		bezCurve[1] = v2Add(bezCurve[0], v2Scale(tHat1, dist));
		bezCurve[2] = v2Add(bezCurve[3], v2Scale(tHat2, dist));
		return (bezCurve);
    }
    
    /*  First and last control points of the Bezier curve are */
    /*  positioned exactly at the first and last data points */
    /*  Control points 1 and 2 are positioned an alpha distance out */
    /*  on the tangent vectors, left and right, respectively */
    bezCurve[0].x = d[first].x;
    bezCurve[0].y = d[first].y;
    bezCurve[3].x = d[last].x;
    bezCurve[3].y = d[last].y;
    bezCurve[1] = v2Add(bezCurve[0], v2Scale(tHat1, alpha_l));
    bezCurve[2] = v2Add(bezCurve[3], v2Scale(tHat2, alpha_r));
    return (bezCurve);
}

function reparameterizePoints(d,first,last,u,bezCurve) {
    var 	nPts = last-first+1;	
    var 	i;
    var	uPrime = new Array();		/*  New parameter values	*/
    
    for (i = first; i <= last; i++) {
        uPrime[i-first] = newtonRaphsonRootFindCurve(bezCurve,d[i],u[i-first]);
    }
    return (uPrime);
}

function newtonRaphsonRootFindCurve(Q,P,u) {
    var 		numerator;
    var denominator;
    var 		Q1 = new Array();
    var Q2 = new Array();	/*  Q' and Q''			*/
    var		Q_u;
    var Q1_u;
    var Q2_u; /*u evaluated at Q, Q', & Q''	*/
    var 		uPrime;		/*  Improved u			*/
    var 		i;
    
    /* Compute Q(u)	*/
    Q_u = bezierIIDegree(3,Q,u);
    
    /* Generate control vertices for Q'	*/
    for (i = 0; i <= 2; i++) {
    	Q1[i] = new Object();
		Q1[i].x = (Q[i+1].x - Q[i].x) * 3.0;
		Q1[i].y = (Q[i+1].y - Q[i].y) * 3.0;
    }
    
    /* Generate control vertices for Q'' */
    for (i = 0; i <= 1; i++) {
    	Q2[i] = new Object();
		Q2[i].x = (Q1[i+1].x - Q1[i].x) * 2.0;
		Q2[i].y = (Q1[i+1].y - Q1[i].y) * 2.0;
    }
    
    /* Compute Q'(u) and Q''(u)	*/
    Q1_u = bezierIIDegree(2,Q1,u);
    Q2_u = bezierIIDegree(1,Q2,u);
    
    /* Compute f(u)/f'(u) */
    numerator = (Q_u.x - P.x) * (Q1_u.x) + (Q_u.y - P.y) * (Q1_u.y);
    denominator = (Q1_u.x) * (Q1_u.x) + (Q1_u.y) * (Q1_u.y) +
    (Q_u.x - P.x) * (Q2_u.x) + (Q_u.y - P.y) * (Q2_u.y);
    if (denominator == 0.0) return u;
    
    /* u = u - f(u)/f'(u) */
    uPrime = u - (numerator/denominator);
    return (uPrime);
}

function bezierIIDegree(degree,V,t) {
    var 	i;
    var j;		
    var 	Q;	        /* Point on curve at parameter t	*/
    var 	Vtemp = new Array();;		/* Local copy of control points		*/
    
    /* Copy array	*/
    for (i = 0; i <= degree; i++) {
    	Vtemp[i] = new Object();
    	Vtemp[i].x = V[i].x;
    	Vtemp[i].y = V[i].y;
    }
    
    /* Triangle computation	*/
    for (i = 1; i <= degree; i++) {	
		for (j = 0; j <= degree-i; j++) {
	    	Vtemp[j].x = (1.0 - t) * Vtemp[j].x + t * Vtemp[j+1].x;
	    	Vtemp[j].y = (1.0 - t) * Vtemp[j].y + t * Vtemp[j+1].y;
		}
    }
    
    Q = Vtemp[0];
    return Q;
}
/*
 *  B0, B1, B2, B3 :
 *	Bezier multipliers
 */
function b0(u)
{
    var tmp = 1.0 - u;
    return (tmp * tmp * tmp);
}


function b1(u)
{
    var tmp = 1.0 - u;
    return (3 * u * (tmp * tmp));
}

function b2(u)
{
    var tmp = 1.0 - u;
    return (3 * u * u * tmp);
}

function b3(u)
{
    return (u * u * u);
}



/*
 * ComputeLeftTangent, ComputeRightTangent, ComputeCenterTangent :
 *Approximate unit tangents at endpoints and "center" of digitized curve
 */
function computeLeftTangent(d, end)
{
    var	tHat1;
    tHat1 = v2SubII(d[end+1], d[end]);
    tHat1 = v2Normalize(tHat1);
    return tHat1;
}

function computeRightTangent(d, end)
{
    var	tHat2;
    tHat2 = v2SubII(d[end-1], d[end]);
    tHat2 = v2Normalize(tHat2);
    return tHat2;
}


function computeCenterTangent(d, center)
{
    var	V1;
    var V2;
    var tHatCenter = new Object();
    V1 = v2SubII(d[center-1], d[center]);
    V2 = v2SubII(d[center], d[center+1]);
    tHatCenter.x = (V1.x + V2.x)/2.0;
    tHatCenter.y = (V1.y + V2.y)/2.0;
    tHatCenter = v2Normalize(tHatCenter);
    return tHatCenter;
}


/*
 *  ChordLengthParameterize :
 *	Assign parameter values to digitized points 
 *	using relative distances between points.
 */
function chordLengthParameterize(d, first, last)
{
    var		i;	
    var	u = new Array();			/*  Parameterization		*/
    
    u[0] = 0.0;
    for (i = first+1; i <= last; i++) {
		u[i-first] = u[i-first-1] +
        v2DistanceBetween2Points(d[i], d[i-1]);
    }
    
    for (i = first + 1; i <= last; i++) {
		u[i-first] = u[i-first] / u[last-first];
    }
    
    return(u);
}




/*
 *  ComputeMaxError :
 *	Find the maximum squared distance of digitized points
 *	to fitted curve.
 */
function computeMaxErrorPoints(d,first,last,bezCurve,u,splitPoint) {
    var		i;
    var	maxDist;		/*  Maximum error		*/
    var	dist;		/*  Current error		*/
    var	P;			/*  Point on curve		*/
    var	v;			/*  Vector from point to curve	*/
    
    splitPoint.value = (last - first + 1)/2;
    maxDist = 0.0;
    for (i = first + 1; i < last; i++) {
        P = bezierIIDegree(3,bezCurve,u[i-first]);
		v = v2SubII(P, d[i]);
		dist = v2SquaredLength(v);
		if (dist >= maxDist) {
	    	maxDist = dist;
	    	splitPoint.value = i;
		}
    }
    return (maxDist);
}
function v2AddII(a, b)
{
    var	c = new Object();
    c.x = a.x + b.x;
    c.y = a.y + b.y;
    return (c);
}
function v2ScaleIII(v, s)
{
    var result = new Object();
    result.x = v.x * s;
    result.y = v.y * s;
    return (result);
}

function v2SubII(a, b)
{
    var	c = new Object();
    c.x = a.x - b.x;
    c.y = a.y - b.y;
    return (c);
}

function v2Normalize(v)
{
    var len = v2Length(v);
	if (len != 0.0)
	{
		v.x /= len;
		v.y /= len;
	}
	return(v);
}
function v2Length(a) 
{
	return(Math.sqrt(v2SquaredLength(a)));
}
function v2SquaredLength(a) 
{
	return((a.x * a.x)+(a.y * a.y));
}

function v2DistanceBetween2Points(a, b)
{
    var dx = a.x - b.x;
    var dy = a.y - b.y;
	return(Math.sqrt((dx*dx)+(dy*dy)));
}
function v2Scale(v, newlen)
{
	var result = new Object();
    var len = v2Length(v);
	if (len != 0.0)
	{
		result.x = v.x * newlen/len;
		result.y = v.y * newlen/len;
	}
	return(result);
}
function v2Dot(a, b) 
{
	return((a.x*b.x)+(a.y*b.y));
}
function v2Add(a, b)
{
	var c = new Object();
	c.x = a.x+b.x;
	c.y = a.y+b.y;
	return(c);
}

function v2Sub(a, b)
{
	var c = new Object();
	c.x = a.x-b.x;
	c.y = a.y-b.y;
	return(c);
}
function v2Negate(v) 
{
	v.x = -v.x;
	v.y = -v.y;
	return(v);
}
function applink(fail){
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
}
		
	</script>
	<div style="width:1171px;height:902px;margin:0 auto;background-image:url('/img/ipad.jpg');background-repeat:no-repeat;background-color:#FFF;padding-top:115px;padding-left:160px;">
		<div style="position:block;width:1024px;height:768px;;">
			<canvas id="canvasBack" style="position:absolute;width:1024px;height:768px;background:url(/img/stars.jpg);" width="1024" height="768"></canvas>
			<canvas id="canvasScene" style="position:absolute;width:1024px;height:768px;" width="1024" height="768"></canvas>
			<canvas id="canvasFront" style="position:absolute;width:1024px;height:768px;" width="1024" height="768"></canvas>
			<canvas id="canvasUI" style="position:absolute;width:1024px;height:768px;" width="1024" height="768"></canvas>
		</div>
		<div style="display:none;">
			<img src="/img/planet_4.png" />
			<img src="/img/gravity_well.png" />
			<img src="/img/anti_gravity.png" />
			<img src="/img/astronaut.png" />
			<img src="/img/fuel.png" />
			<img src="/img/space_station.png" />
		</div>
	</div>
</body>
</html>