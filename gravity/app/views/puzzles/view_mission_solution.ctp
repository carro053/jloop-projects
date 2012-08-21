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
<body style="margin:0;overflow:hidden;">
	<script type="text/javascript">
		var scene = 'select';
		var gameInterval;
		var gameTime = 0;
		var timer = new Timer();
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
		shipSprites.src = 'ship_sprites.png';

		
		
		
		
		var canvasFront = new Object();
		var contextFront = new Object();
		var canvasBack = new Object();
		var contextBack = new Object();
		var canvasUI = new Object();
		var contextUI = new Object();
		
		window.onload = function() {
			
			canvasBack = document.getElementById('canvasBack');
			contextBack = canvasBack.getContext('2d');
			canvasBack.width = window.innerWidth;
			canvasBack.height = window.innerHeight;
			
			canvasFront = document.getElementById('canvasFront');
			contextFront = canvasFront.getContext('2d');
			canvasFront.width = window.innerWidth;
			canvasFront.height = window.innerHeight;
			
			
			canvasUI = document.getElementById('canvasUI');
			contextUI = canvasUI.getContext('2d');
			canvasUI.width = window.innerWidth;
			canvasUI.height = window.innerHeight;
			
			canvasUI.onmousedown = function(e) {
				if(scene == 'select' && ship_targeted)
				{
					scene = 'game';
					player.data.x = ship_target.data.x;
					player.data.y = ship_target.data.y;
					player.data.angle = ship_target.data.angle;
					player.data.ship = ship_target.data.ship;
					player.data.tracking_distance = 0;
					clearInterval(gameInterval);
					startGame();
				}else if(scene == 'game' && heat_level < max_heat)
				{
					player.fire_laser();
				}
			};
			
			canvasUI.onmousemove = function(e) {
				target.data.x = e.clientX - this.offsetLeft;
				target.data.y = e.clientY - this.offsetTop;
			};
			
			shipSprites.onload = initialize();
			
			window.onkeypress = function(e) {
				switch(e.which) {
					case 119:
						player.data.speed = normalSpeed * 3 / 2;
						break;
					case 115:
						player.data.speed = normalSpeed / 2;
						break;
					case 100:
						fire_lasers = 1;
						break;
				}
			}
			window.onkeyup = function(e) {
				switch(e.which) {
					case 87:
						speed_change = 1;
						player.data.speed = normalSpeed;
						break;
					case 83:
						speed_change = 1;	
						player.data.speed = normalSpeed;
						break;
					case 68:
						fire_lasers = 0;
						break;
					case 69:
						if(ship_targeted) player.fire_missile();
						break;
					case 49:				
						/*player.data.ship = 1;
						player.data.xRightLaser = 5;
						player.data.yRightLaser = -25;
						player.data.xLeftLaser = -6;
						player.data.yLeftLaser = -25;
						player.data.laserColor = 'rgb(0,255,0)';*/
						addEnemy(-150,-150,6);
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
				}
			}
		};
		function initialize()
		{
			scene = 'select';
			reset_game();
			contextUI.clearRect(0, 0, canvasUI.width, canvasUI.height);
			contextFront.clearRect(0, 0, canvasUI.width, canvasUI.height);
			drawBackground();
			//shipSelect();
		}
		
		function startGame()
		{
			reset_game();
			drawUI();
			timer.tick();
			gameInterval = setInterval(gameLoop, 20);
		}
		function drawBackground()
		{
			var planet = new Image();
			planet.src = "/img/planet_4.png";
			contextBack.scale(2.0, 2.0);
			contextBack.drawImage(planet, 150, 0);
		}
		
		function drawUI()
		{
			contextUI.clearRect(0, 0, canvasUI.width, canvasUI.height);
			contextUI.font = '24px Arial';
			contextUI.fillStyle =  '#FFFFFF';
    		contextUI.textAlign = "left";
			contextUI.fillText('Level: '+level,20,canvasUI.height - 160);
			contextUI.fillText('Score: '+score,20,canvasUI.height - 130);
			contextUI.fillText('Shields: '+player.data.shields,20,canvasUI.height - 100);
			contextUI.fillText('Missiles: '+missile_count,20,canvasUI.height - 70);
			contextUI.fillText('Heat Level:',20,canvasUI.height - 40);
			contextUI.fillStyle =  'rgba('+Math.round((255*heat_level)/max_heat)+','+Math.round((255*(max_heat-heat_level))/max_heat)+',0,1)';
			contextUI.fillRect(150,canvasUI.height - 57,20,20);
		}

		function gameLoop()
		{
			updateObjects();
			clearCanvas();
			drawObjects();
			drawUI();
			timer.tick();
			if(player.data.shields <= 0)
			{
				var ship_text = 'ships';
				if(score == 1) ship_text = 'ship';
				if(confirm('You have died. You destroyed '+score+' '+ship_text+'. Press OK to play again.'))
				{
					clearInterval(gameInterval);
					initialize();
				}else{
					clearInterval(gameInterval);
				}
			}
		}

		function updateObjects()
		{
			gameTime += timer.getSeconds();
			if(player.data.speed != normalSpeed)
			{
				if(heat_level < max_heat && speed_change == 1)
				{
					heat_level += 5000 * timer.getSeconds();
				}else{
					speed_change = 0;
					player.data.speed = normalSpeed;
				}
			}
			if(fire_lasers == 1)
			{
				if(heat_level < max_heat)
				{
					player.fire_laser();
				}else{
					fire_lasers = 0;
				}
			}
			player.update();
			ship_targeted = 0;
			closest_ship = 100;
			for(var s in ships)
			{
				ships[s].update();
			}
			heat_level -= 3000 * timer.getSeconds();
			if(heat_level < 0) heat_level = 0;
			if(ships.length == 0)
			{
				ship_targeted = 0;
				clearInterval(gameInterval);
				gameInterval = setInterval(hyperspaceLoop, 20);
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
			for(var m in missiles)
			{
				contextFront.fillStyle =  '#FFFB00';
				contextFront.beginPath();
				contextFront.arc(missiles[m].x, missiles[m].y, 2, 0, Math.PI*2, true); 
				contextFront.closePath();
				contextFront.fill();
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
			if(ship_targeted)
			{
				var targeted = new Image();
				targeted.src = 'targeted.png';
				contextFront.translate(ship_target.data.x, ship_target.data.y);
				contextFront.rotate(ship_target.data.angle * Math.PI / 180);
				contextFront.drawImage(targeted,-20,-20);
				contextFront.rotate(-ship_target.data.angle * Math.PI / 180);
				contextFront.translate(-ship_target.data.x, -ship_target.data.y);
			}
			
		}
		function reset_game()
		{
			timer.previousTime = new Date().getTime();
			timer.currentTime = new Date().getTime();
		}
		
	</script>
	<div style="position:absolute;width:1024px;height:768px;">
	<canvas id="canvasBack" style="position:block;width:1024px;height:768px;background:url(/img/stars.jpg);"></canvas>
	<canvas id="canvasFront" style="position:block;width:1024px;height:768px;"></canvas>
	<canvas id="canvasUI" style="position:block;width:1024px;height:768px;"></canvas>
	</div>
</body>
</html>