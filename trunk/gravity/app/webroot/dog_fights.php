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
					var side  = this.data.side;
					lasers.push(
						{x: x, y: y, angle: angle, color: color, side: side }
					);
				}
			},
			update: function() {				
				if(this.data.target == null || this.data.target.data.dead == 1)
				{						
					if(this.data.side == 'republic' && empire_count > 0 || this.data.side == 'empire' && republic_count > 0)
					{
						var target_found = 0;
						while(target_found == 0)
						{
							var randShip = Math.random() * (ships.length - 1);
							if(ships[randShip].data.side != this.data.side && ships[randShip].data.dead == 0)
							{
								target_found = 1;
								this.data.target = ships[randShip];
							}
						}
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
		var scene = 'select';
		var stars = new Array();
		var ship_targeted = 0;
		var ship_target = new Object;
		var closest_ship;
		var arrived = 0;
		var hyperspaceCharge = 0;
		var jumpTime = 0;
		var gameInterval;
		var gameTime = 0;
		var level = 0;
		var timer = new Timer();
		var lasers = new Array();
		var fire_lasers = 0;
		var missiles = new Array();
		var missile_count = 10;
		var heat_level = 0;
		var max_heat = 25000;
		var speed_change = 1;
		var explosions = new Array();
		var ships = new Array();
		var squads = new Array();
		var empire_count = 0;
		var republic_count = 0;
		var lps = 10;
		var score = 0;
		var normalSpeed;
		var maxShields;
		var maxMissiles;
		var squad_separation = 30;
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
		
		var explosion = new SpriteSheet(
			[
				{ id:1, x:0, y:0, w:30, h:45},
			    { id:10, x:30, y:0, w:30, h:45},
				{ id:11, x:60, y:0, w:30, h:45},
				{ id:12, x:90, y:0, w:30, h:45},
				{ id:13, x:0, y:45, w:30, h:45},
				{ id:2, x:30, y:45, w:30, h:45},
				{ id:3, x:60, y:45, w:30, h:45},
				{ id:4, x:90, y:45, w:30, h:45},
				{ id:5, x:0, y:90, w:30, h:45},
				{ id:6, x:30, y:90, w:30, h:45},
				{ id:7, x:60, y:90, w:30, h:45},
				{ id:8, x:90, y:90, w:30, h:45},
				{ id:9, x:0, y:135, w:30, h:45}
			]
		);
		
		var explodeFrame = new Object;
		var explosionImage = new Image();
		explosionImage.src = 'explosion.png';

		
		
		
		
		var canvasFront = new Object();
		var contextFront = new Object();
		var canvasBack = new Object();
		var contextBack = new Object();
		
		window.onload = function() {
			
			canvasBack = document.getElementById('canvasBack');
			contextBack = canvasBack.getContext('2d');
			canvasBack.width = window.innerWidth;
			canvasBack.height = window.innerHeight;
			
			canvasFront = document.getElementById('canvasFront');
			contextFront = canvasFront.getContext('2d');
			canvasFront.width = window.innerWidth;
			canvasFront.height = window.innerHeight;
			
			shipSprites.onload = initialize();
		};
		function initialize()
		{
			scene = 'select';
			reset_game();
			contextFront.clearRect(0, 0, canvasFront.width, canvasFront.height);
			drawBackground();
			
			
				addShip('republic',-50,100,5);
				
				addShip('empire',window.innerWidth + 50,100,1);
			
			
			timer.tick();
			gameInterval = setInterval(gameLoop, 20);
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

		function gameLoop()
		{
			updateObjects();
			clearCanvas();
			drawObjects();
			timer.tick();
		}

		function updateObjects()
		{
			gameTime += timer.getSeconds();
			ship_targeted = 0;
			closest_ship = 100;
			for(var s in ships)
			{
				ships[s].update();
			}
			heat_level -= 3000 * timer.getSeconds();
			if(heat_level < 0) heat_level = 0;
			updateLasers();
			updateExplosions();
			if(ships.length == 0)
			{
				ship_targeted = 0;
				clearInterval(gameInterval);
				gameInterval = setInterval(hyperspaceLoop, 20);
			}			
		}
		function updateExplosions()
		{
			for(var e in explosions)
			{
				explosions[e].explode.animate(timer.getSeconds());
				explodeFrame = explosions[e].explode.getFrame();
				contextFront.drawImage(explosionImage, explodeFrame.x, explodeFrame.y, explodeFrame.w, explodeFrame.h, explosions[e].data.x, explosions[e].data.y, explodeFrame.w, explodeFrame.h);
				if(explosions[e].explode.currentFrame == 12)
					explosions.splice(e, 1);
			}
		}
		function updateLasers()
		{
			for(var l in lasers)
			{
				var hit = 0;
				lasers[l].x += Math.cos((lasers[l].angle - 90) *(Math.PI/180)) * 600 * timer.getSeconds();
				lasers[l].y += Math.sin((lasers[l].angle - 90) *(Math.PI/180)) * 600 * timer.getSeconds();
				if(lasers[l].x < 0 || lasers[l].y < 0 || lasers[l].x > canvasFront.width || lasers[l].y > canvasFront.height)
				{
					lasers.splice(l, 1);
				}else{
					for(var s in ships)
					{
						if(lasers[l].x < ships[s].data.x + ships[s].data.w / 2 && lasers[l].x > ships[s].data.x - ships[s].data.w / 2 && lasers[l].y < ships[s].data.y + ships[s].data.h / 2 && lasers[l].y > ships[s].data.y - ships[s].data.h / 2 && ships[s].data.side != lasers[l].side)
						{
							var imgd = contextFront.getImageData(lasers[l].x, lasers[l].y, 1, 1);
							var pix = imgd.data;
							for (var i = 0, n = pix.length; i < n; i += 4) if(pix[i+3] > 0) hit = 1;
							if(hit == 1)
							{
								ships[s].data.shields -= 1;
								if(ships[s].data.shields <= 0)
								{
									score++;
									newExplosion(ships[s].data.x,ships[s].data.y);
									ships[s].data.dead = 1;										
									if(ships[s].dataside == 'republic')
									{
										republic_count--;
									}else{
										empire_count--;
									}
									ships.splice(s, 1);
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
		
		function newExplosion(x,y)
		{
			var new_explosion = new	Object;
			new_explosion.data = new Object;
			new_explosion.data.x = x - 14;
			new_explosion.data.y = y - 26;
			new_explosion.explode = new SpriteSequence(
				[
					{id: 1, t: 0.05},
					{id: 2, t: 0.05},
					{id: 3, t: 0.05},
					{id: 4, t: 0.05},
					{id: 5, t: 0.05},
					{id: 6, t: 0.05},
					{id: 7, t: 0.05},
					{id: 8, t: 0.05},
					{id: 9, t: 0.05},
					{id: 10, t: 0.05},
					{id: 11, t: 0.05},
					{id: 12, t: 0.05},
					{id: 13, t: 0.05}
				],
				explosion
			);
			explosions.push(new_explosion);
		}
		function clearCanvas()
		{
			contextFront.clearRect(0, 0, canvasFront.width, canvasFront.height);
		}

		function drawObjects()
		{
			updateExplosions();
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
		function getShipData(ship_type)
		{
			shipData = new Object;
			switch(ship_type) {
				case 1:
					//fury
					shipData.ship = 1;
					//shipData.trackingDistance = Math.floor(Math.random()*200) + 100;
					shipData.trackingDistance = 1;
					shipData.xRightLaser = 5;
					shipData.yRightLaser = -25;
					shipData.xLeftLaser = -6;
					shipData.yLeftLaser = -25;
					shipData.laserColor = 'rgb(0,255,0)';
					shipData.speed = 150;
					shipData.angular_speed = 150;
					shipData.shields = 2;
					shipData.missiles = 10;
					break;
				case 2:
					//phantom
					shipData.ship = 2;
					//shipData.trackingDistance = Math.floor(Math.random()*200) + 100;		
					shipData.trackingDistance = 1;				
					shipData.xRightLaser = 12;
					shipData.yRightLaser = -5;
					shipData.xLeftLaser = -12;
					shipData.yLeftLaser = -5;
					shipData.laserColor = 'rgb(0,255,0)';
					shipData.speed = 150;
					shipData.angular_speed = 150;
					shipData.shields = 2;
					shipData.missiles = 15;
					
					break;
				case 3:
					//mantis
					shipData.ship = 3;	
					//shipData.trackingDistance = Math.floor(Math.random()*200) + 100;			
					shipData.trackingDistance = 1;		
					shipData.xRightLaser = 13;
					shipData.yRightLaser = -12;
					shipData.xLeftLaser = -14;
					shipData.yLeftLaser = -12;
					shipData.laserColor = 'rgb(0,255,0)';
					shipData.speed = 200;
					shipData.angular_speed = 150;
					shipData.shields = 3;
					shipData.missiles = 20;
					
					break;
				case 4:
					//defender
					shipData.ship = 4;
					//shipData.trackingDistance = Math.floor(Math.random()*150) + 100;	
					shipData.trackingDistance = 1;				
					shipData.xRightLaser = 10;
					shipData.yRightLaser = -23;
					shipData.xLeftLaser = -10;
					shipData.yLeftLaser = -23;
					shipData.laserColor = 'rgb(255,0,0)';
					shipData.speed = 150;
					shipData.angular_speed = 150;
					shipData.shields = 2;
					shipData.missiles = 10;
					
					break;
				case 5:
					//freighter
					shipData.ship = 5;	
					//shipData.trackingDistance = Math.floor(Math.random()*150);		
					shipData.trackingDistance = 1;			
					shipData.xRightLaser = 1;
					shipData.yRightLaser = -10;
					shipData.xLeftLaser = -1;
					shipData.yLeftLaser = -10;
					shipData.laserColor = 'rgb(255,0,0)';
					shipData.speed = 200;
					shipData.angular_speed = 150;
					shipData.shields = 3;
					shipData.missiles = 10;
					
					break;
				case 6:
					//thunderclap
					shipData.ship = 6;					
					//shipData.trackingDistance = Math.floor(Math.random()*200) + 100;
					shipData.trackingDistance = 1;
					shipData.xRightLaser = 16;
					shipData.yRightLaser = -15;
					shipData.xLeftLaser = -14;
					shipData.yLeftLaser = -10;
					shipData.laserColor = 'rgb(255,0,0)';
					shipData.speed = 150;
					shipData.angular_speed = 150;
					shipData.shields = 2;
					shipData.missiles = 10;
					break;
			}
			return shipData;
		}
		function addSquad(side,ship_type,amount,x,y)
		{
			squads.push = 1;
			var squad_number = squads.length;
			addEnemy(x,y,ship_type,null,null,squad_number);
			var squadLeader = ships.length - 1;
			for(var i=1;i < amount;i++)
			{
				var squadX;
				var squadY;
				var cos = Math.cos((ships[squadLeader].data.angle + 90) * (Math.PI/180));
				var sin = Math.sin((ships[squadLeader].data.angle + 90) * (Math.PI/180));
				var position = Math.ceil(i / 2);
				if(i%2 == 0)
				{
					squadX = x - position * squad_separation * sin + position * squad_separation * cos;
					squadY = y + position * squad_separation * sin + position * squad_separation * cos;
				}else{
					squadX = x + position * squad_separation * sin + position * squad_separation * cos;
					squadY = y + position * squad_separation * sin - position * squad_separation * cos;
				}
				addShip(side,squadX,squadY,ship_type,ships[squadLeader],i,squad_number);
			}
		}
		
		function addShip(side,x,y,set_ship_type,squad_leader,squad_position,squad_number)
		{
			var w = 39;
			var h = 40;
			var xOffset = 20;
			var yOffset = 20;
			var angle = 90;
			var ship_type = Math.floor(Math.random()*3) + 4;
			if(set_ship_type) ship_type = set_ship_type;
			var shipData = getShipData(ship_type);
			var laser_side = 0;
			var last_fired = 0;
			var mytarget = null;
			if(side == 'republic' && empire_count > 0 || side == 'empire' && republic_count > 0)
			{
				var target_found = 0;
				while(target_found == 0)
				{
					var randShip = Math.random() * (ships.length - 1);
					alert(randShip);
					if(ships[randShip].data.side != side && ships[randShip].data.dead == 0)
					{
						target_found = 1;
						mytarget = ships[randShip];
					}
				}
			}
			var ship  = new StarShip(
				{
					side: side,
					x: x,
					y: y,
					w: w,
					h: h,
					xOffset: xOffset,
					yOffset: yOffset,
					angle: angle,
					ship: ship_type,
					tracking_distance: shipData.trackingDistance,
					xRightLaser: shipData.xRightLaser,
					yRightLaser: shipData.yRightLaser,
					xLeftLaser: shipData.xLeftLaser,
					yLeftLaser: shipData.yLeftLaser,
					laserColor: shipData.laserColor,
					speed: shipData.speed,
					angular_speed: shipData.angular_speed,
					shields: shipData.shields,
					laser_side: laser_side,
					last_fired: last_fired,
					target: mytarget,
					squad_leader: squad_leader,
					squad_position: squad_position,
					squad_number:squad_number,
					dead: 0
				}
			);
			ships.push(ship);
			if(side == 'republic')
			{
				republic_count++;
			}else{
				empire_count++;
			}
		}
		function reset_game()
		{
			timer.previousTime = new Date().getTime();
			timer.currentTime = new Date().getTime();
			gameTime = 0;
			lasers.length = 0;
			missiles.length = 0;
			ships.length = 0;
			squads.length = 0;
		}
		
	</script>
	<canvas id="canvasBack" style="position:absolute;"></canvas>
	<canvas id="canvasFront" style="position:absolute;"></canvas>
</body>
</html>