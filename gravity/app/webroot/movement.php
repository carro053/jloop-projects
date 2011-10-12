

<!doctype html>
<html>
<head>
    <script type="text/javascript">
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
		
		var StarShip = function(ship) {
			this.data = ship;
		}
		
		StarShip.prototype = {
			fire_laser: function() {
				if(this.data.last_fired > 1 / lps)
				{
					this.data.last_fired = 0;
					var cos = Math.cos((this.data.angle + 90) * (Math.PI/180));
					var sin = Math.sin((this.data.angle + 90) * (Math.PI/180));
					var x;
					var y;
					if(this.data.laser_side == 1)
					{
						this.data.laser_side = 0;
						x = this.data.x + sin * this.data.xRightLaser + cos * this.data.yRightLaser;
						y = this.data.y + sin * this.data.yRightLaser - cos * this.data.xRightLaser;
					}else{
						this.data.laser_side = 1;
						x = this.data.x + sin * this.data.xLeftLaser + cos * this.data.yLeftLaser;
						y = this.data.y + sin * this.data.yLeftLaser - cos * this.data.xLeftLaser;
					}
					var angle = this.data.angle;
					var color = this.data.laser_color;
					lasers.push(
						{x: x, y: y, angle: angle, color: color}
					);
				}
			},
			update: function() {
				this.data.last_fired += timer.getSeconds();
				var distance = Math.sqrt(Math.pow(this.data.target.data.x - this.data.x, 2) + Math.pow(this.data.target.data.y - this.data.y, 2));
				if(distance > 1)
				{
					//target angle
					var ta = Math.atan2(this.data.target.data.y - this.data.y,this.data.target.data.x - this.data.x) * 180 / Math.PI + 90;
					if(ta < 0) ta += 360;
					if(distance > this.data.tracking_distance && Math.round(ta) != Math.round(this.data.angle))
					{
						//angle diff
						var ad = ta - this.data.angle;
						//change angle by this
						var ca = 0;
						if(ad < -180) ad += 360;
						if(ad > 180) ad -= 360;
						
						if(ad < 0)//turn left
						{
							ca = -this.data.angular_speed * timer.getSeconds();
						}else{//turn right
							ca = this.data.angular_speed * timer.getSeconds();
						}
						if(Math.abs(ca) > Math.abs(ad))
						{
							this.data.angle = ta;
						}else{
							this.data.angle += ca;
							if(this.data.angle < 0) this.data.angle += 360;
							if(this.data.angle >= 360) this.data.angle -= 360;
						}
					}else if(this.data.tracking_distance != 0 && Math.round(ta) == Math.round(this.data.angle)){
						this.fire_laser();
					}
				}
				this.data.x += Math.cos((this.data.angle - 90) *(Math.PI/180)) * this.data.speed * timer.getSeconds();
				this.data.y += Math.sin((this.data.angle - 90) *(Math.PI/180)) * this.data.speed * timer.getSeconds();
			
			}
		}
		
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
		var timer = new Timer();
		var lasers = new Array();
		var target = new Object;
		target.data = new Object;
		var player = new Object;
		var ships = new Array();
		var lps = 10;
		player.x = target.data.x = window.innerWidth / 2;
		player.y = target.data.y = window.innerHeight / 2;
		player.w = 39;
		player.h = 40;
		player.xOffset = 20;
		player.yOffset = 20;
		player.speed = 200;
		player.angular_speed = 200;
		player.angle = 0;
		player.xRightLaser = 5;
		player.yRightLaser = -15;
		player.xLeftLaser = -6;
		player.yLeftLaser = -15;
		player.laser_color = 'rgb(0,255,0)';
		player.shields = 10;
		player.laser_side = 0;
		player.last_fired = 0;
		player.target = target;
		player.tracking_distance = 0;
		
		player = new StarShip(player);
		
		
		
		
		
		var level = [
			//{x: 100, y: 100, w: 50, h: 100},
			//{x: 100, y: 20, w: 10, h: 10},
			//{x: 200, y: 200, w: 160, h: 20}
		];
		
		var fury = new SpriteSheet(
			[
				{id: 0, x: 0, y: 0, w: 39, h: 40},
				{id: 15, x: 152, y: 0, w: 38, h: 38},
				{id: 30, x: 114, y: 76, w: 38, h: 38},
				{id: 45, x: 76, y: 114, w: 38, h: 38},
				{id: 60, x: 114, y: 114, w: 38, h: 38},
				{id: 75, x: 152, y: 114, w: 38, h: 38},
				{id: 90, x: 190, y: 114, w: 38, h: 38},
				{id: 105, x: 38, y: 0, w: 38, h: 38},
				{id: 120, x: 76, y: 0, w: 38, h: 38},
				{id: 135, x: 114, y: 0, w: 38, h: 38},
				{id: 150, x: 190, y: 0, w: 38, h: 38},
				{id: 165, x: 0, y: 38, w: 38, h: 38},
				{id: 180, x: 38, y: 38, w: 38, h: 38},
				{id: 195, x: 76, y: 38, w: 38, h: 38},
				{id: 210, x: 114, y: 38, w: 38, h: 38},
				{id: 225, x: 152, y: 38, w: 38, h: 38},
				{id: 240, x: 190, y: 38, w: 38, h: 38},
				{id: 255, x: 0, y: 76, w: 38, h: 38},
				{id: 270, x: 38, y: 76, w: 38, h: 38},
				{id: 285, x: 76, y: 76, w: 38, h: 38},
				{id: 300, x: 152, y: 76, w: 38, h: 38},
				{id: 315, x: 190, y: 76, w: 38, h: 38},
				{id: 330, x: 0, y: 114, w: 38, h: 38},
				{id: 345, x: 38, y: 114, w: 38, h: 38},
			]
		);
		
		var shipImage = new Image();
		shipImage.src = 'small_fury.png';
		
		var canvasFront = new Object();
		var contextFront = new Object();
		var canvasBack = new Object();
		var contextBack = new Object();
		
		window.onload = function() {
			canvasFront = document.getElementById('canvasFront');
			contextFront = canvasFront.getContext('2d');
			canvasFront.width = window.innerWidth;
			canvasFront.height = window.innerHeight;
			
			canvasFront.onmousedown = function(e) {
				player.fire_laser();
			};
			
			canvasFront.onmousemove = function(e) {
				target.data.x = e.clientX - this.offsetLeft;
				target.data.y = e.clientY - this.offsetTop;
			};
			
			canvasBack = document.getElementById('canvasBack');
			contextBack = canvasBack.getContext('2d');
			canvasBack.width = window.innerWidth;
			canvasBack.height = window.innerHeight;
			
			shipImage.onload = initialize();
			
			window.onkeypress = function(e) {
				//console.log('e.which = ' + e.which + ', e.keyCode = ' + e.keyCode);
				switch(e.keyCode) {
					case 49:
						shipImage.src = 'fury_small.png';						
						player.xRightLaser = 5;
						player.yRightLaser = -25;
						player.xLeftLaser = -6;
						player.yLeftLaser = -25;
						player.laser_color = 'rgb(0,255,0)';
						break;
					case 50:
						shipImage.src = 'phantom_small.png';						
						player.xRightLaser = 12;
						player.yRightLaser = -5;
						player.xLeftLaser = -12;
						player.yLeftLaser = -5;
						player.laser_color = 'rgb(0,255,0)';
						
						break;
					case 51:
						shipImage.src = 'mantis_small.png';						
						player.xRightLaser = 13;
						player.yRightLaser = -12;
						player.xLeftLaser = -14;
						player.yLeftLaser = -12;
						player.laser_color = 'rgb(0,255,0)';
						
						break;
					case 52:
						shipImage.src = 'defender_small.png';						
						player.xRightLaser = 10;
						player.yRightLaser = -23;
						player.xLeftLaser = -10;
						player.yLeftLaser = -23;
						player.laser_color = 'rgb(255,0,0)';
						
						break;
					case 53:
						shipImage.src = 'freighter_small.png';						
						player.xRightLaser = 1;
						player.yRightLaser = -10;
						player.xLeftLaser = -1;
						player.yLeftLaser = -10;
						player.laser_color = 'rgb(255,0,0)';
						
						break;
					case 54:
						shipImage.src = 'thunderclap_small.png';						
						player.xRightLaser = 16;
						player.yRightLaser = -15;
						player.xLeftLaser = -14;
						player.yLeftLaser = -10;
						player.laser_color = 'rgb(255,0,0)';
						break;
					case 102:
						player.fire_laser();
						break;
					case 56:
						var x = window.innerWidth / 2;
						var y = window.innerHeight / 2;
						var w = 39;
						var h = 40;
						var xOffset = 20;
						var yOffset = 20;
						var speed = 150;
						var angular_speed = 150;
						var angle = Math.floor(Math.random()*360);
						var tracking_distance = Math.floor(Math.random()*200) + 100;
						var xRightLaser = 5;
						var yRightLaser = -25;
						var xLeftLaser = -6;
						var yLeftLaser = -25;
						var laser_color = 'rgb(0,255,0)';
						var laser_side = 0;
						var sImage = new Image();
						sImage.src = 'small_fury.png';
						var shields = 3;
						var last_fired = 0;
						var mytarget = player;
						var ship  = new StarShip({x: x, y: y, w: w, h: h, xOffset: xOffset, yOffset: yOffset, speed: speed, angular_speed: angular_speed, angle: angle, tracking_distance: tracking_distance, xRightLaser: xRightLaser, yRightLaser: yRightLaser, xLeftLaser: xLeftLaser, yLeftLaser: yLeftLaser, laser_color: laser_color, laser_side: laser_side, sImage: sImage, shields: shields, last_fired: last_fired, target: mytarget });
						ships.push(ship);
						break;
					default:
						break;
				}
			}
		};
		function initialize()
		{
			drawBackground();
			timer.tick();
			setInterval(gameLoop, 20);
		}
		
		function drawBackground()
		{
			contextBack.fillStyle =  '#000000';
			contextBack.fillRect(0, 0, canvasBack.width, canvasBack.height);
			
			contextBack.fillStyle =  '#26537c';
			for(var n in level)
			{
				contextBack.fillRect(level[n].x, level[n].y, level[n].w, level[n].h);
			}
		}

		function gameLoop()
		{
			updateObjects();
			clearCanvas();
			drawObjects();
			timer.tick();
		}

		function updateObjects()
		{
			player.update();
			for(var s in ships)
			{
				ships[s].update();
			}
			for(var l in lasers)
			{
				
				lasers[l].x += Math.cos((lasers[l].angle - 90) *(Math.PI/180)) * 500 * timer.getSeconds();
				lasers[l].y += Math.sin((lasers[l].angle - 90) *(Math.PI/180)) * 500 * timer.getSeconds();
				if(lasers[l].x < 0 || lasers[l].y < 0 || lasers[l].x > canvasFront.width || lasers[l].y > canvasFront.height)
				{
					lasers.splice(l, 1);
				}
			}
			
		}

		function clearCanvas()
		{
			contextFront.clearRect(0, 0, canvasFront.width, canvasFront.height);
		}

		function drawObjects()
		{
			contextFront.translate(player.data.x, player.data.y);
			contextFront.rotate(player.data.angle * Math.PI / 180);
			contextFront.drawImage(shipImage, 0, 0, player.data.w, player.data.h, -player.data.xOffset, -player.data.yOffset, player.data.w, player.data.h);
			contextFront.rotate(-player.data.angle * Math.PI / 180);
			contextFront.translate(-player.data.x, -player.data.y);
			for(var l in lasers)
			{
				contextFront.beginPath();
				contextFront.moveTo(lasers[l].x - 10 * Math.cos((lasers[l].angle - 90) *(Math.PI/180)),lasers[l].y - 10 * Math.sin((lasers[l].angle - 90) *(Math.PI/180)));
				contextFront.lineTo(lasers[l].x,lasers[l].y);
				contextFront.closePath();
				contextFront.strokeStyle = lasers[l].color;
				contextFront.stroke();
			}
			for(var s in ships)
			{
				var sprite = fury.getSprite(0);
				contextFront.translate(ships[s].data.x, ships[s].data.y);
				contextFront.rotate(ships[s].data.angle * Math.PI / 180);
				contextFront.drawImage(ships[s].data.sImage, 0, 0, ships[s].data.w, ships[s].data.h, -ships[s].data.xOffset, -ships[s].data.yOffset, ships[s].data.w, ships[s].data.h);
				contextFront.rotate(-ships[s].data.angle * Math.PI / 180);
				contextFront.translate(-ships[s].data.x, -ships[s].data.y);
			}
			
		}
	</script>
	<canvas id="canvasBack" style="position:absolute;"></canvas>
	<canvas id="canvasFront" style="position:absolute;"></canvas>
</body>
</html>s