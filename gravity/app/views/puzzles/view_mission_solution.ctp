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
		var canvasScene = new Object();
		var contextScene = new Object();
		var canvasBack = new Object();
		var contextBack = new Object();
		var canvasUI = new Object();
		var contextUI = new Object();
		
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
			
			var anti = new Image();
			anti.src = "/img/anti_gravity.png";
			
			var astro = new Image();
			astro.src = "/img/astronaut.png";
			
			var fuel = new Image();
			fuel.src = "/img/fuel.png";
			
			<?php foreach($data['planets'] as $planet): ?>
			contextScene.save();
			contextScene.scale(<?php echo ($planet['radius'] / 70 / 2); ?>, <?php echo ($planet['radius'] / 70 / 2); ?>);
			contextScene.drawImage(<?php if($planet['antiGravity']) { echo 'anti'; }else{ echo 'planet'; } ?>, <?php echo (($planet['x'] - $planet['radius']) / ($planet['radius'] / 70 / 2)); ?>, <?php echo ((768 - $planet['y'] - $planet['radius']) / ($planet['radius'] / 70 / 2)); ?>);
			contextScene.restore();
			<?php endforeach; ?>
			
			<?php foreach($data['astronauts'] as $astro): ?>
			contextScene.save();
			contextScene.scale(<?php echo (0.5); ?>, <?php echo (0.5); ?>);
			contextScene.drawImage(astro, <?php echo (($astro['x'] - 8) / (0.5)); ?>, <?php echo ((768 - $astro['y'] - 12) / (0.5)); ?>);
			contextScene.restore();
			<?php endforeach; ?>
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
	<div style="position:block;width:1024px;height:768px;">
		<canvas id="canvasBack" style="position:absolute;width:1024px;height:768px;background:url(/img/stars.jpg);" width="1024" height="768"></canvas>
		<canvas id="canvasScene" style="position:absolute;width:1024px;height:768px;" width="1024" height="768"></canvas>
		<canvas id="canvasFront" style="position:absolute;width:1024px;height:768px;" width="1024" height="768"></canvas>
		<canvas id="canvasUI" style="position:absolute;width:1024px;height:768px;" width="1024" height="768"></canvas>
	</div>
	<pre>
	<?php print_r($data); ?>
	</pre>
</body>
</html>