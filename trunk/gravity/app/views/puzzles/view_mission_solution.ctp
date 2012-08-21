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
		shipSprites.src = '/ship_sprites.png';

		
		
		
		
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
			contextBack.drawImage(planet, 0, 0);
			contextBack.scale(0.5, 0.5);
			contextBack.drawImage(planet, 150, 0);
		}
		
		function drawUI()
		{
			contextUI.clearRect(0, 0, canvasUI.width, canvasUI.height);
			contextUI.font = '24px Arial';
			contextUI.fillStyle =  '#FFFFFF';
    		contextUI.textAlign = "left";
			contextUI.fillRect(150,canvasUI.height - 57,20,20);
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
				
		}
		function clearCanvas()
		{
			contextFront.clearRect(0, 0, canvasFront.width, canvasFront.height);
		}

		function drawObjects()
		{
		
			
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