

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
		var player = new Object;
		var laser_side = 0;
		player.x = target.x = window.innerWidth / 2;
		player.y = target.y = window.innerHeight / 2;
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
		
		var ships = new Array();
		
		
		
		
		
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
			
			canvasFront.onclick = function(e) {
				var cos = Math.cos((player.angle + 90) * (Math.PI/180));
				var sin = Math.sin((player.angle + 90) * (Math.PI/180));
				var x;
				var y;
				if(laser_side == 1)
				{
					laser_side = 0;
					x = player.x + sin * player.xRightLaser + cos * player.yRightLaser;
					y = player.y + sin * player.yRightLaser - cos * player.xRightLaser;
				}else{
					laser_side = 1;
					x = player.x + sin * player.xLeftLaser + cos * player.yLeftLaser;
					y = player.y + sin * player.yLeftLaser - cos * player.xLeftLaser;
				}
				var angle = player.angle;
				var color = player.laser_color;
				lasers.push(
					{x: x, y: y, angle: angle, color: color}
				);
			};
			canvasFront.onmousemove = function(e) {
				target.x = e.clientX - this.offsetLeft;
				target.y = e.clientY - this.offsetTop;
			}
			
			canvasBack = document.getElementById('canvasBack');
			contextBack = canvasBack.getContext('2d');
			canvasBack.width = window.innerWidth;
			canvasBack.height = window.innerHeight;
			
			shipImage.onload = initialize();
			
			window.onkeypress = function(e) {
				console.log('e.which = ' + e.which + ', e.keyCode = ' + e.keyCode);
				switch(e.which) {
					case 49:
						shipImage.src = 'fury_small.png';						
						player.xRightLaser = 5;
						player.yRightLaser = -15;
						player.xLeftLaser = -6;
						player.yLeftLaser = -15;
						player.laser_color = 'rgb(0,255,0)';
						break;
					case 50:
						shipImage.src = 'phantom_small.png';						
						player.xRightLaser = 12;
						player.yRightLaser = 5;
						player.xLeftLaser = -12;
						player.yLeftLaser = 5;
						player.laser_color = 'rgb(0,255,0)';
						
						break;
					case 51:
						shipImage.src = 'mantis_small.png';						
						player.xRightLaser = 13;
						player.yRightLaser = -2;
						player.xLeftLaser = -14;
						player.yLeftLaser = -2;
						player.laser_color = 'rgb(0,255,0)';
						
						break;
					case 52:
						shipImage.src = 'defender_small.png';						
						player.xRightLaser = 10;
						player.yRightLaser = -13;
						player.xLeftLaser = -10;
						player.yLeftLaser = -13;
						player.laser_color = 'rgb(255,0,0)';
						
						break;
					case 53:
						shipImage.src = 'freighter_small.png';						
						player.xRightLaser = 1;
						player.yRightLaser = 0;
						player.xLeftLaser = -1;
						player.yLeftLaser = 0;
						player.laser_color = 'rgb(255,0,0)';
						
						break;
					case 54:
						shipImage.src = 'thunderclap_small.png';						
						player.xRightLaser = 16;
						player.yRightLaser = -5;
						player.xLeftLaser = -14;
						player.yLeftLaser = 0;
						player.laser_color = 'rgb(255,0,0)';
						break;
					case 55:
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
						var tracking_distance = Math.floor(Math.random()*200) + 200;
						var xRightLaser = 5;
						var yRightLaser = -15;
						var xLeftLaser = -6;
						var yLeftLaser = -15;
						var laser_color = 'rgb(0,255,0)';
						var laser_side = 0;
						var sImage = new Image();
						sImage.src = 'small_fury.png';
						ships.push(
							{x: x, y: y, w: w, h: h, xOffset: xOffset, yOffset: yOffset, speed: speed, angular_speed: angular_speed, angle: angle, tracking_distance: tracking_distance, xRightLaser: xRightLaser, yRightLaser: yRightLaser, xLeftLaser: xLeftLaser, yLeftLaser: yLeftLaser, laser_color: laser_color, laser_side: laser_side, sImage: sImage}
						);
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
			var lastX = player.x;
			var lastY = player.y;
			var distance = Math.sqrt(Math.pow(target.x - player.x, 2) + Math.pow(target.y - player.y, 2));
			if(distance > 1)
			{
				//target angle
				var ta = Math.atan2(target.y - player.y,target.x - player.x) * 180 / Math.PI + 90;
				if(ta < 0) ta += 360;
				if(ta != player.angle)
				{
					//angle diff
					var ad = ta - player.angle;
					//change angle by this
					var ca = 0;
					if(ad < -180) ad += 360;
					if(ad > 180) ad -= 360;
					
					if(ad < 0)//turn left
					{
						ca = -player.angular_speed * timer.getSeconds();
					}else{//turn right
						ca = player.angular_speed * timer.getSeconds();
					}
					if(Math.abs(ca) > Math.abs(ad))
					{
						player.angle = ta;
					}else{
						player.angle += ca;
						if(player.angle < 0) player.angle += 360;
						if(player.angle >= 360) player.angle -= 360;
					}
				}
			}
			player.x += Math.cos((player.angle - 90) *(Math.PI/180)) * player.speed * timer.getSeconds();
			player.y += Math.sin((player.angle - 90) *(Math.PI/180)) * player.speed * timer.getSeconds();
			for(var n in level)
			{
				if(
					(player.x > level[n].x || player.x + player.w > level[n].x) && 
					(player.x < level[n].x + level[n].w || player.x + player.w < level[n].x + level[n].w ) && 
					(player.y > level[n].y || player.y + player.h > level[n].y) && 
					(player.y < level[n].y + level[n].h || player.y + player.h < level[n].y + level[n].h)
				)
				{
					target.x = player.x = lastX;
					target.y = player.y = lastY;
				}
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
			
			for(var s in ships)
			{
				var distance = Math.sqrt(Math.pow(player.x - ships[s].x, 2) + Math.pow(player.y - ships[s].y, 2));
				if(distance > 1)
				{
					//target angle
					var ta = Math.atan2(player.y - ships[s].y,player.x - ships[s].x) * 180 / Math.PI + 90;
					if(ta < 0) ta += 360;
					
					if(distance > ships[s].tracking_distance && Math.round(ta) != Math.round(ships[s].angle))
					{
						console.log(distance);
						//angle diff
						var ad = ta - ships[s].angle;
						//change angle by this
						var ca = 0;
						if(ad < -180) ad += 360;
						if(ad > 180) ad -= 360;
						
						if(ad < 0)//turn left
						{
							ca = -ships[s].angular_speed * timer.getSeconds();
						}else{//turn right
							ca = ships[s].angular_speed * timer.getSeconds();
						}
						if(Math.abs(ca) > Math.abs(ad))
						{
							ships[s].angle = ta;
						}else{
							ships[s].angle += ca;
							if(ships[s].angle < 0) ships[s].angle += 360;
							if(ships[s].angle >= 360) ships[s].angle -= 360;
						}
					}else if(Math.round(ta) == Math.round(ships[s].angle)){
						var cos = Math.cos((ships[s].angle + 90) * (Math.PI/180));
						var sin = Math.sin((ships[s].angle + 90) * (Math.PI/180));
						var x;
						var y;
						if(ships[s].laser_side == 1)
						{
							ships[s].laser_side = 0;
							x = ships[s].x + sin * ships[s].xRightLaser + cos * ships[s].yRightLaser;
							y = ships[s].y + sin * ships[s].yRightLaser - cos * ships[s].xRightLaser;
						}else{
							ships[s].laser_side = 1;
							x = ships[s].x + sin * ships[s].xLeftLaser + cos * ships[s].yLeftLaser;
							y = ships[s].y + sin * ships[s].yLeftLaser - cos * ships[s].xLeftLaser;
						}
						var angle = ships[s].angle;
						var color = ships[s].laser_color;
						lasers.push(
							{x: x, y: y, angle: angle, color: color}
						);
					}
				}
				ships[s].x += Math.cos((ships[s].angle - 90) *(Math.PI/180)) * ships[s].speed * timer.getSeconds();
				ships[s].y += Math.sin((ships[s].angle - 90) *(Math.PI/180)) * ships[s].speed * timer.getSeconds();
			}
			
		}

		function clearCanvas()
		{
			contextFront.clearRect(0, 0, canvasFront.width, canvasFront.height);
		}

		function drawObjects()
		{
			contextFront.translate(player.x, player.y);
			contextFront.rotate(player.angle * Math.PI / 180);
			contextFront.drawImage(shipImage, 0, 0, player.w, player.h, -player.xOffset, -player.yOffset, player.w, player.h);
			contextFront.rotate(-player.angle * Math.PI / 180);
			contextFront.translate(-player.x, -player.y);
			for(var l in lasers)
			{
				contextFront.beginPath();
				contextFront.moveTo(lasers[l].x,lasers[l].y);
				contextFront.lineTo(lasers[l].x + 10 * Math.cos((lasers[l].angle - 90) *(Math.PI/180)),lasers[l].y + 10 * Math.sin((lasers[l].angle - 90) *(Math.PI/180)));
				contextFront.closePath();
				contextFront.strokeStyle = lasers[l].color;
				contextFront.stroke();
			}
			for(var s in ships)
			{
				var sprite = fury.getSprite(0);
				contextFront.translate(ships[s].x, ships[s].y);
				contextFront.rotate(ships[s].angle * Math.PI / 180);
				contextFront.drawImage(ships[s].sImage, 0, 0, ships[s].w, ships[s].h, -ships[s].xOffset, -ships[s].yOffset, ships[s].w, ships[s].h);
				contextFront.rotate(-ships[s].angle * Math.PI / 180);
				contextFront.translate(-ships[s].x, -ships[s].y);
			}
			
		}
		
		/*
		var sprite = fury.getSprite(1);
		contextFront.drawImage(shipImage, sprite.x, sprite.y, sprite.w, sprite.h, 10, 50, sprite.w, sprite.h);
		
		var sprite = fury.getSprite(2);
		contextFront.drawImage(shipImage, sprite.x, sprite.y, sprite.w, sprite.h, 30, 50, sprite.w, sprite.h);
		
		var sprite = fury.getSprite(3);
		contextFront.drawImage(shipImage, sprite.x, sprite.y, sprite.w, sprite.h, 50, 50, sprite.w, sprite.h);
		
		var sprite = fury.getSprite(4);
		contextFront.drawImage(shipImage, sprite.x, sprite.y, sprite.w, sprite.h, 70, 50, sprite.w, sprite.h);
		
		var sprite = fury.getSprite(5);
		contextFront.drawImage(shipImage, sprite.x, sprite.y, sprite.w, sprite.h, 90, 50, sprite.w, sprite.h);
		*/
	</script>
	<canvas id="canvasBack" style="position:absolute;"></canvas>
	<canvas id="canvasFront" style="position:absolute;"></canvas>
</body>
</html>s