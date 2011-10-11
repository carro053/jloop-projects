
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
		
		var target = new Object;
		var player = new Object;
		player.x = player.y = target.x = target.y = 300;
		player.w = player.h = 16;
		player.xOffset = 8;
		player.yOffset = 12;
		player.speed = 50;
		player.angular_speed = 25;
		player.angle = 90;
		
		var level = [
			{x: 100, y: 100, w: 50, h: 100},
			{x: 100, y: 20, w: 10, h: 10},
			{x: 200, y: 200, w: 160, h: 20}
		];
		
		var whiteMage = new SpriteSheet(
			[
				{id: 1, x: 0, y: 32, w: 16, h: 16},
				{id: 2, x: 16, y: 32, w: 16, h: 16},
				{id: 3, x: 32, y: 32, w: 16, h: 16},
				{id: 4, x: 48, y: 32, w: 16, h: 16},
				{id: 5, x: 0, y: 48, w: 16, h: 16},
				{id: 6, x: 16, y: 48, w: 16, h: 16},
				{id: 7, x: 32, y: 48, w: 16, h: 16},
				{id: 8, x: 48, y: 48, w: 16, h: 16}
			]
		);
		
		var walkLeft = new SpriteSequence(
			[
				{id: 1, t: 0.2},
				{id: 5, t: 0.2},
			],
			whiteMage
		);
		
		var walkRight = new SpriteSequence(
			[
				{id: 2, t: 0.2},
				{id: 6, t: 0.2},
			],
			whiteMage
		);
		
		var walkDown = new SpriteSequence(
			[
				{id: 3, t: 0.2},
				{id: 7, t: 0.2},
			],
			whiteMage
		);
		
		var walkUp = new SpriteSequence(
			[
				{id: 4, t: 0.2},
				{id: 8, t: 0.2},
			],
			whiteMage
		);
		
		var walkFrame = new Object();
		
		var whiteMageImage = new Image();
		whiteMageImage.src = 'whiteMage.png';
		
		var canvasFront = new Object();
		var contextFront = new Object();
		var canvasBack = new Object();
		var contextBack = new Object();
		
		window.onload = function() {
			canvasFront = document.getElementById('canvasFront');
			contextFront = canvasFront.getContext('2d');
			
			canvasFront.onclick = function(e) {
				target.x = e.clientX - this.offsetLeft - player.xOffset;
				target.y = e.clientY - this.offsetTop - player.yOffset;
			};
			
			canvasBack = document.getElementById('canvasBack');
			contextBack = canvasBack.getContext('2d');
			
			whiteMageImage.onload = initialize();
		};
		
		function initialize()
		{
			drawBackground();
			timer.tick();
			setInterval(gameLoop, 20);
		}
		
		function drawBackground()
		{
			contextBack.fillStyle =  '#ffb040';
			contextBack.fillRect(0, 0, canvasBack.width, canvasBack.height);
			
			contextBack.fillStyle =  '#26537c';
			for(var n in level)
			{
				contextBack.fillRect(level[n].x, level[n].y, level[n].w, level[n].h);
			}
			walkFrame = walkRight.getFrame();
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
				if(ta < 0) d += 360;
				if(ta != player.angle)
				{
					//angle diff
					var ad = ta - player.angle;
					//change angle by this
					var ca = 0;
					if(ad < -180) ad += 360;
					
					if(ad < 0)//turn left
					{
						ca =  -player.angular_speed * timer.getSeconds();
					}else{//turn right
						ca = player.angular_speed * timer.getSeconds();
					}
					if(ca > ad)
					{
						player.angle = ta;
					}else{
						player.angle += ca;
						if(player.angle < 0) player.angle += 360;
						if(player.angle >= 360) player.angle -= 360;
					}
					//alert(Math.cos((player.angle - 90) *(Math.PI/180)) + " " +Math.sin((player.angle - 90) *(Math.PI/180)));
					player.x += Math.cos((player.angle - 90) *(Math.PI/180)) * player.speed * timer.getSeconds();
					player.y += Math.sin((player.angle - 90) *(Math.PI/180)) * player.speed * timer.getSeconds();
					
				}else{
					player.x += (target.x - player.x) / distance * player.speed * timer.getSeconds();
					player.y += (target.y - player.y) / distance * player.speed * timer.getSeconds();
				}
				if(Math.abs(player.x - target.x) > Math.abs(player.y - target.y))
				{
					if(player.x > target.x)
					{
						walkLeft.animate(timer.getSeconds());
						walkFrame = walkLeft.getFrame();
					}else{
						walkRight.animate(timer.getSeconds());
						walkFrame = walkRight.getFrame();
					}
				}else{
					if(player.y > target.y)
					{
						walkUp.animate(timer.getSeconds());
						walkFrame = walkUp.getFrame();
					}else{
						walkDown.animate(timer.getSeconds());
						walkFrame = walkDown.getFrame();
					}
				}
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
		}

		function clearCanvas()
		{
			contextFront.clearRect(0, 0, canvasFront.width, canvasFront.height);
		}

		function drawObjects()
		{
			contextFront.drawImage(whiteMageImage, walkFrame.x, walkFrame.y, walkFrame.w, walkFrame.h, player.x, player.y, walkFrame.w, walkFrame.h);
		}
		
		/*
		var sprite = whiteMage.getSprite(1);
		contextFront.drawImage(whiteMageImage, sprite.x, sprite.y, sprite.w, sprite.h, 10, 50, sprite.w, sprite.h);
		
		var sprite = whiteMage.getSprite(2);
		contextFront.drawImage(whiteMageImage, sprite.x, sprite.y, sprite.w, sprite.h, 30, 50, sprite.w, sprite.h);
		
		var sprite = whiteMage.getSprite(3);
		contextFront.drawImage(whiteMageImage, sprite.x, sprite.y, sprite.w, sprite.h, 50, 50, sprite.w, sprite.h);
		
		var sprite = whiteMage.getSprite(4);
		contextFront.drawImage(whiteMageImage, sprite.x, sprite.y, sprite.w, sprite.h, 70, 50, sprite.w, sprite.h);
		
		var sprite = whiteMage.getSprite(5);
		contextFront.drawImage(whiteMageImage, sprite.x, sprite.y, sprite.w, sprite.h, 90, 50, sprite.w, sprite.h);
		*/
	</script>
	<canvas id="canvasBack" width="600" height="400" style="position:absolute;"></canvas>
	<canvas id="canvasFront" width="600" height="400" style="position:absolute;"></canvas>
</body>
</html>