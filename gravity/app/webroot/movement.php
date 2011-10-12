

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
					var color = this.data.laserColor;
					var side = 0;
					if(this.data.tracking_distance > 0)
					{
						side = 1;
					}
					lasers.push(
						{x: x, y: y, angle: angle, color: color, side: side }
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
		var ship_type = Math.floor(Math.random()*3) + 1;
		var shipData = getShipData(ship_type);
		player  = new StarShip(
			{
				x: target.data.x = window.innerWidth / 2,
				y: target.data.y = window.innerHeight / 2,
				w: 39,
				h: 40,
				xOffset: 20,
				yOffset: 20,
				speed: 200,
				angular_speed: 200,
				angle: 0,
				tracking_distance: 0,
				ship: ship_type,
				xRightLaser: shipData.xRightLaser,
				yRightLaser: shipData.yRightLaser,
				xLeftLaser: shipData.xLeftLaser,
				yLeftLaser: shipData.yLeftLaser,
				laserColor: shipData.laserColor,
				laser_side: 0,
				shields: 10,
				last_fired: 0,
				target: target
			}
		);
		
		var shipSpritesheet = new SpriteSheet(
			[
				{id: 1, x:  0, y:  0, w: 39, h: 40},
				{id: 2, x: 39, y:  0, w: 39, h: 40},
				{id: 3, x: 78, y:  0, w: 39, h: 40},
				{id: 4, x:  0, y: 40, w: 39, h: 40},
				{id: 5, x: 39, y: 40, w: 39, h: 40},
				{id: 6, x: 78, y: 40, w: 39, h: 40},
			]
		);
		
		var shipSprites = new Image();
		shipSprites.src = 'ship_sprites.png';
		
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
			
			shipSprites.onload = initialize();
			
			window.onkeypress = function(e) {
				//console.log('e.which = ' + e.which + ', e.keyCode = ' + e.keyCode);
				switch(e.which) {
					case 49:				
						player.data.ship = 1;
						player.data.xRightLaser = 5;
						player.data.yRightLaser = -25;
						player.data.xLeftLaser = -6;
						player.data.yLeftLaser = -25;
						player.data.laserColor = 'rgb(0,255,0)';
						break;
					case 50:
						player.data.ship = 2;						
						player.data.xRightLaser = 12;
						player.data.yRightLaser = -5;
						player.data.xLeftLaser = -12;
						player.data.yLeftLaser = -5;
						player.data.laserColor = 'rgb(0,255,0)';
						
						break;
					case 51:
						player.data.ship = 3;						
						player.data.xRightLaser = 13;
						player.data.yRightLaser = -12;
						player.data.xLeftLaser = -14;
						player.data.yLeftLaser = -12;
						player.data.laserColor = 'rgb(0,255,0)';
						
						break;
					case 52:
						player.data.ship = 4;					
						player.data.xRightLaser = 10;
						player.data.yRightLaser = -23;
						player.data.xLeftLaser = -10;
						player.data.yLeftLaser = -23;
						player.data.laserColor = 'rgb(255,0,0)';
						
						break;
					case 53:
						player.data.ship = 5;						
						player.data.xRightLaser = 1;
						player.data.yRightLaser = -10;
						player.data.xLeftLaser = -1;
						player.data.yLeftLaser = -10;
						player.data.laserColor = 'rgb(255,0,0)';
						
						break;
					case 54:
						player.data.ship = 6;					
						player.data.xRightLaser = 16;
						player.data.yRightLaser = -15;
						player.data.xLeftLaser = -14;
						player.data.yLeftLaser = -10;
						player.data.laserColor = 'rgb(255,0,0)';
						break;
					case 102:
						player.fire_laser();
						break;
					case 56:
						addEnemy(-50,-50);
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
				var hit = 0;
				lasers[l].x += Math.cos((lasers[l].angle - 90) *(Math.PI/180)) * 500 * timer.getSeconds();
				lasers[l].y += Math.sin((lasers[l].angle - 90) *(Math.PI/180)) * 500 * timer.getSeconds();
				if(lasers[l].x < 0 || lasers[l].y < 0 || lasers[l].x > canvasFront.width || lasers[l].y > canvasFront.height)
				{
					lasers.splice(l, 1);
				}else{
					if(lasers[l].side == 1)
					{
						if(lasers[l].x < player.data.x + player.data.w / 2 && lasers[l].x > player.data.x - player.data.w / 2 && lasers[l].y < player.data.y + player.data.h / 2 && lasers[l].y > player.data.y - player.data.h / 2)
						{
							var imgd = contextFront.getImageData(lasers[l].x, lasers[l].y, 1, 1);
							var pix = imgd.data;
							for (var i = 0, n = pix.length; i < n; i += 4) {
								if(pix[i+3] > 0)
								{
									hit = 1;
									player.data.shields -= 1;
									if(player.data.shields == 0)
									{
										alert('You are dead.');
									}
								}
							}
						}
					}else{
						for(var s in ships)
						{
							if(lasers[l].x < ships[s].data.x + ships[s].data.w / 2 && lasers[l].x > ships[s].data.x - ships[s].data.w / 2 && lasers[l].y < ships[s].data.y + ships[s].data.h / 2 && lasers[l].y > ships[s].data.y - ships[s].data.h / 2)
							{
								var imgd = contextFront.getImageData(lasers[l].x, lasers[l].y, 1, 1);
								var pix = imgd.data;
								for (var i = 0, n = pix.length; i < n; i += 4) if(pix[i+3] > 0) hit = 1;
								if(hit == 1)
								{
									ships[s].data.shields -= 1;
									if(ships[s].data.shields == 0)
									{
										ships.splice(s, 1);
									}
								}
							}
						}
					}
				}
				if(hit == 1)
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
			var sprite = shipSpritesheet.getSprite(player.data.ship);
			contextFront.translate(player.data.x, player.data.y);
			contextFront.rotate(player.data.angle * Math.PI / 180);sprite.x, sprite.y, sprite.w, sprite.h, 10, 50, sprite.w, sprite.h
			contextFront.drawImage(shipSprites, sprite.x, sprite.y, sprite.w, sprite.h, -player.data.xOffset, -player.data.yOffset, sprite.w, sprite.h);
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
				var sprite = shipSpritesheet.getSprite(ships[s].data.ship);
				contextFront.translate(ships[s].data.x, ships[s].data.y);
				contextFront.rotate(ships[s].data.angle * Math.PI / 180);
				contextFront.drawImage(shipSprites, sprite.x, sprite.y, sprite.w, sprite.h, -ships[s].data.xOffset, -ships[s].data.yOffset, sprite.w, sprite.h);
				contextFront.rotate(-ships[s].data.angle * Math.PI / 180);
				contextFront.translate(-ships[s].data.x, -ships[s].data.y);
			}
			
		}
		function getShipData(ship_type)
		{
			shipData = new Object;
			switch(ship_type) {
				case 1:				
					shipData.ship = 1;
					shipData.xRightLaser = 5;
					shipData.yRightLaser = -25;
					shipData.xLeftLaser = -6;
					shipData.yLeftLaser = -25;
					shipData.laserColor = 'rgb(0,255,0)';
					break;
				case 2:
					shipData.ship = 2;						
					shipData.xRightLaser = 12;
					shipData.yRightLaser = -5;
					shipData.xLeftLaser = -12;
					shipData.yLeftLaser = -5;
					shipData.laserColor = 'rgb(0,255,0)';
					
					break;
				case 3:
					shipData.ship = 3;						
					shipData.xRightLaser = 13;
					shipData.yRightLaser = -12;
					shipData.xLeftLaser = -14;
					shipData.yLeftLaser = -12;
					shipData.laserColor = 'rgb(0,255,0)';
					
					break;
				case 4:
					shipData.ship = 4;					
					shipData.xRightLaser = 10;
					shipData.yRightLaser = -23;
					shipData.xLeftLaser = -10;
					shipData.yLeftLaser = -23;
					shipData.laserColor = 'rgb(255,0,0)';
					
					break;
				case 5:
					shipData.ship = 5;						
					shipData.xRightLaser = 1;
					shipData.yRightLaser = -10;
					shipData.xLeftLaser = -1;
					shipData.yLeftLaser = -10;
					shipData.laserColor = 'rgb(255,0,0)';
					
					break;
				case 6:
					shipData.ship = 6;					
					shipData.xRightLaser = 16;
					shipData.yRightLaser = -15;
					shipData.xLeftLaser = -14;
					shipData.yLeftLaser = -10;
					shipData.laserColor = 'rgb(255,0,0)';
					break;
			}
			return shipData;
		}
		function addEnemy(x,y)
		{
			var w = 39;
			var h = 40;
			var xOffset = 20;
			var yOffset = 20;
			var speed = 150;
			var angular_speed = 150;
			var angle = Math.floor(Math.random()*360);
			var tracking_distance = Math.floor(Math.random()*200) + 100;
			var ship_type = Math.floor(Math.random()*3) + 4;
			var shipData = getShipData(ship_type);
			var laser_side = 0;
			var shields = 3;
			var last_fired = 0;
			var mytarget = player;
			var ship  = new StarShip(
				{
					x: x,
					y: y,
					w: w,
					h: h,
					xOffset: xOffset,
					yOffset: yOffset,
					speed: speed,
					angular_speed: angular_speed,
					angle: angle,
					tracking_distance: tracking_distance,
					ship: ship_type,
					xRightLaser: shipData.xRightLaser,
					yRightLaser: shipData.yRightLaser,
					xLeftLaser: shipData.xLeftLaser,
					yLeftLaser: shipData.yLeftLaser,
					laserColor: shipData.laserColor,
					laser_side: laser_side,
					shields: shields,
					last_fired: last_fired,
					target: mytarget
				}
			);
			ships.push(ship);
		}
	</script>
	<canvas id="canvasBack" style="position:absolute;"></canvas>
	<canvas id="canvasFront" style="position:absolute;"></canvas>
</body>
</html>