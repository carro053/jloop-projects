

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
		player.x = player.y = target.x = target.y = 0;
		player.w = 39;
		player.h = 40;
		player.xOffset = 20;
		player.yOffset = 20;
		player.speed = 150;
		player.angular_speed = 150;
		player.angle = 0;
		player.xRightLaser = 7;
		player.YRightLaser = -20;
		
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
				target.x = e.clientX - this.offsetLeft;
				target.y = e.clientY - this.offsetTop;
			};
			
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
						break;
					case 50:
						shipImage.src = 'phantom_small.png';
						
						break;
					case 51:
						shipImage.src = 'mantis_small.png';
						
						break;
					case 52:
						shipImage.src = 'defender_small.png';
						
						break;
					case 53:
						shipImage.src = 'freighter_small.png';
						
						break;
					case 54:
						shipImage.src = 'thunderclap_small.png';
						break;
					case 55:
						var cos = Math.cos((player.angle - 90) * (Math.PI/180));
						var sin = Math.sin((player.angle - 90) * (Math.PI/180));
						var x = player.x + player.xRightLaser + player.xRightLaser;
						var y = player.y + player.xRightLaser + player.xRightLaser;
						var angle = player.angle;
						alert(sin+"1"+cos+"2"+x+"3"+y);
						lasers.push(
							{x: x, y: y, angle: angle}
						);
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
			if(distance > 2)
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
				player.x += Math.cos((player.angle - 90) *(Math.PI/180)) * player.speed * timer.getSeconds();
				player.y += Math.sin((player.angle - 90) *(Math.PI/180)) * player.speed * timer.getSeconds();
			}
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
			
			
		}

		function clearCanvas()
		{
			contextFront.clearRect(0, 0, canvasFront.width, canvasFront.height);
		}

		function drawObjects()
		{
			var si = Math.floor(player.angle / 15) * 15;
			var sprite = fury.getSprite(0);
			contextFront.translate(player.x, player.y);
			contextFront.rotate(player.angle * Math.PI / 180);
			contextFront.drawImage(shipImage, sprite.x, sprite.y, sprite.w, sprite.h, -player.xOffset, -player.yOffset, sprite.w, sprite.h);
			contextFront.rotate(-player.angle * Math.PI / 180);
			contextFront.translate(-player.x, -player.y);
			for(var l in lasers)
			{
				contextFront.beginPath();
				contextFront.moveTo(lasers[l].x,lasers[l].y);
				contextFront.lineTo(lasers[l].x + 10 * Math.cos((lasers[l].angle - 90) *(Math.PI/180)),lasers[l].y + 10 * Math.sin((lasers[l].angle - 90) *(Math.PI/180)));
				contextFront.closePath();
				contextFront.strokeStyle = "#00FF00";
				contextFront.stroke();
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