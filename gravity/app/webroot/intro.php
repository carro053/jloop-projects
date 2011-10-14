

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
			fire_missile: function() {
				if(missile_count > 0)
				{
					missile_count--;
					var cos = Math.cos((this.data.angle + 90) * (Math.PI/180));
					var sin = Math.sin((this.data.angle + 90) * (Math.PI/180));
					var x = this.data.x - cos * this.data.h / 2;
					var y = this.data.y - sin * this.data.h / 2;
					var angle = this.data.angle;
					var target = ship_target;
					missiles.push(
						{ x: x, y: y, angle: angle, target: target }
					);
				}
			},
			fire_laser: function() {
				if(this.data.last_fired > 1 / lps)
				{
					if(this.data.tracking_distance == 0) heat_level += 1000;
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
				if(this.data.tracking_distance > 0)
				{
					var mouse_distance = Math.sqrt(Math.pow(target.data.x - this.data.x, 2) + Math.pow(target.data.y - this.data.y, 2));
					if(mouse_distance < 100 && mouse_distance < closest_ship)
					{
						closest_ship = mouse_distance;
						ship_targeted = 1;
						ship_target = this;
					}
				}
				this.data.last_fired += timer.getSeconds();
				var distance = Math.sqrt(Math.pow(this.data.target.data.x - this.data.x, 2) + Math.pow(this.data.target.data.y - this.data.y, 2));
				if(this.data.squad_leader != null && this.data.squad_leader.data.dead == 0 && distance > 500)
				{
					var squadX;
					var squadY;
					var cos = Math.cos((this.data.squad_leader.data.angle + 90) * (Math.PI/180));
					var sin = Math.sin((this.data.squad_leader.data.angle + 90) * (Math.PI/180));
					var position = Math.ceil(this.data.squad_position / 2);
					if(this.data.squad_position % 2 == 0)
					{
						squadX = this.data.squad_leader.data.x - position * squad_separation * sin + position * squad_separation * cos;
						squadY = this.data.squad_leader.data.y + position * squad_separation * sin + position * squad_separation * cos;
					}else{
						squadX = this.data.squad_leader.data.x + position * squad_separation * sin + position * squad_separation * cos;
						squadY = this.data.squad_leader.data.y + position * squad_separation * sin - position * squad_separation * cos;
					}
					var squad_distance = Math.sqrt(Math.pow(squadX - this.data.x, 2) + Math.pow(squadY - this.data.y, 2));
					var bonus_speed = squad_distance * 2;
					if(bonus_speed > 100) bonus_speed = 100;
					if(squad_distance > 10)
					{
						var ta = Math.atan2(squadY - this.data.y,squadX - this.data.x) * 180 / Math.PI + 90;
						if(ta < 0) ta += 360;
						var ad = ta - this.data.angle;
						//change angle by this
						var ca = 0;
						if(ad < -180) ad += 360;
						if(ad > 180) ad -= 360;
						
						if(ad < 0)//turn left
						{
							ca = -(this.data.angular_speed + bonus_speed) * timer.getSeconds();
						}else{//turn right
							ca = (this.data.angular_speed + bonus_speed) * timer.getSeconds();
						}
						if(Math.abs(ca) > Math.abs(ad))
						{
							this.data.angle = ta;
						}else{
							this.data.angle += ca;
							if(this.data.angle < 0) this.data.angle += 360;
							if(this.data.angle >= 360) this.data.angle -= 360;
						}
						this.data.x += Math.cos((this.data.angle - 90) *(Math.PI/180)) * (this.data.speed + bonus_speed) * timer.getSeconds();
						this.data.y += Math.sin((this.data.angle - 90) *(Math.PI/180)) * (this.data.speed + bonus_speed) * timer.getSeconds();
					}else{
						var ta = this.data.squad_leader.data.angle;
						var ad = ta - this.data.angle;
						//change angle by this
						var ca = 0;
						if(ad < -180) ad += 360;
						if(ad > 180) ad -= 360;
						
						if(ad < 0)//turn left
						{
							ca = -(this.data.angular_speed + bonus_speed) * timer.getSeconds();
						}else{//turn right
							ca = (this.data.angular_speed + bonus_speed) * timer.getSeconds();
						}
						if(Math.abs(ca) > Math.abs(ad))
						{
							this.data.angle = ta;
						}else{
							this.data.angle += ca;
							if(this.data.angle < 0) this.data.angle += 360;
							if(this.data.angle >= 360) this.data.angle -= 360;
						}
						
						this.data.x = squadX;
						this.data.y = squadY;
					}
				}else{
					if(distance > 1)
					{
						//target angle
						var ta = Math.atan2(this.data.target.data.y - this.data.y,this.data.target.data.x - this.data.x) * 180 / Math.PI + 90;
						if(ta < 0) ta += 360;
						
						var firing_angle_tolerance = 30 * (200 - distance) / 200;
						if(firing_angle_tolerance < 0) firing_angle_tolerance = 0;
						
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
						}else if(this.data.tracking_distance != 0 && distance < 500 && Math.abs(Math.round(ta) - Math.round(this.data.angle)) <= firing_angle_tolerance){
							this.fire_laser();
						}
					}
					this.data.x += Math.cos((this.data.angle - 90) *(Math.PI/180)) * this.data.speed * timer.getSeconds();
					this.data.y += Math.sin((this.data.angle - 90) *(Math.PI/180)) * this.data.speed * timer.getSeconds();
				}
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
<body style="margin:0;overflow:hidden;">
	<script type="text/javascript">
		var introInterval;
		var timer = new Timer();
		var stars = new Array();
		var canvasIntro = new Object();
		var contextIntro = new Object();
		var canvasBack = new Object();
		var contextBack = new Object();
		
		window.onload = function() {
			
			canvasBack = document.getElementById('canvasBack');
			contextBack = canvasBack.getContext('2d');
			canvasBack.width = window.innerWidth;
			canvasBack.height = window.innerHeight;
			
			canvasIntro = document.getElementById('canvasIntro');
			contextIntro = canvasIntro.getContext('2d');
			canvasIntro.width = window.innerWidth;
			canvasIntro.height = window.innerHeight;
			
			var introText = new Image();
			introText.src = 'intro_text.png';
			introText.onload = function() { initialize(); };
		};
		function initialize()
		{
			drawBackground();
			
			contextIntro.font = '50px arial,sans-serif' ;
			contextIntro.fillStyle = 'green' ;
			contextIntro.setTransform (1, 0, 0, 0.2, 0, 0);
			contextIntro.fillText ('your text', 100, 100) ;
			contextIntro.setTransform (1, 0, 0, 1, 0, 0);
			//timer.tick();
			//introInterval = setInterval(introLoop, 20);
		}
		
		function drawBackground()
		{
			contextBack.fillStyle =  '#000000';
			contextBack.fillRect(0, 0, canvasBack.width, canvasBack.height);
			
			contextBack.fillStyle =  '#FFFFFF';
			for(var i = 0; i < canvasBack.width * canvasBack.height / 10000; i++)
			{
				var x = Math.floor(Math.random() * canvasBack.width);
				var y = Math.floor(Math.random() * canvasBack.height);
				var r = 0.5;
				stars.push({ x: x, y: y, r: r });
				contextBack.beginPath();
				contextBack.arc(x, y, r, 0, Math.PI*2, true); 
				contextBack.closePath();
				contextBack.fill();
			}
			for(var i = 0; i < canvasBack.width * canvasBack.height / 10000; i++)
			{
				var x = Math.floor(Math.random() * canvasBack.width);
				var y = Math.floor(Math.random() * canvasBack.height);
				var r = (Math.floor(Math.random() * 2) + 1) / 2;
				stars.push({ x: x, y: y, r: r });
				contextBack.beginPath();
				contextBack.arc(x, y, r, 0, Math.PI*2, true); 
				contextBack.closePath();
				contextBack.fill();
			}
			contextBack.strokeStyle =  '#FFFFFF';
		}

		function introLoop()
		{
			timer.tick();
		}
		
	</script>
	<canvas id="canvasBack" style="position:absolute;"></canvas>
	<canvas id="canvasIntro" style="position:absolute;"></canvas>
</body>
</html>