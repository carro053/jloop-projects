
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>

  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  
  <title>Projective texturing using Canvas and JavaScript</title>

  <style type="text/css" media="screen">

  body {
    height: 100%;
    font-family: "Lucida Grande", "Lucida Sans Unicode", sans-serif;
  }
  
  #demo {
    position: relative;
    height: 100%;
  }
  
  div.handle {
    position: absolute;
    background: #39f;
    width: 9px;
    height: 9px;
    border: 1px solid black;
    margin-left: -5px;
    margin-top: -5px;
    z-index: 2;
  }

  #canvas canvas {
    position: absolute;
  }
  
  form {
    float: right;
    margin-right: 5em;
  }
  form div {
    margin: 1em;
  }
  
  form div, .box {
    display: block;
    padding: 0.5em;
    background: #cdf;
  }
  
  </style>
  
  <script type="text/javascript" src="jquery.js"></script>
  <script type="text/javascript" src="matrix.js"></script>
  <script type="text/javascript" src="projective.js"></script>
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
	</script>
  <script type="text/javascript" charset="utf-8">
    var imageCnt = 0;
    function changeImage() {      
      imageCnt = (imageCnt + 1) % 3;
      options.image = 'image'+ (imageCnt + 1) +'.jpg';
      refresh();
    }
    var timer = new Timer();
	var gameInterval;
    var canvas_width = 800;
    var canvas_height = 800;
    var intro_time = 10;
    var demod = null;
    var t = 0, rx = 0, ry = 0, rz = 0, oldpoints;
    function runDemo() {
    	
    demod = 1;
      oldpoints = [].concat(points);
      $('div.handle').hide();
      t=0;
		timer.tick();
      gameInterval = setInterval(demoTick, 20);
      $('#demo-button').html('Stop demo');
    }
    function demoTick() {
      t += timer.getSeconds();
      var behind_t = 2 * t - intro_time;
      if(t >= intro_time) stopDemo();
      points[0] = [0 + Math.round(canvas_width / 2 * t / intro_time), canvas_height - Math.round(canvas_height * 3 / 4 * t / intro_time)];

      points[2] = [0 + Math.round(canvas_width / 2 * behind_t / intro_time), canvas_height - Math.round(canvas_height * 3 / 4 * behind_t / intro_time)];
      points[1] = [canvas_width - Math.round(canvas_width / 2 * t / intro_time), canvas_height - Math.round(canvas_height * 3 / 4 * t / intro_time)];
      points[3] = [canvas_width - Math.round(canvas_width / 2 * behind_t / intro_time), canvas_height - Math.round(canvas_height * 3 / 4 * behind_t / intro_time)];
      update();
    }
    function stopDemo() {
    demod = null;
      $('#demo-button').html('Run demo');
      $('div.handle').show();
      clearInterval(gameInterval);
    }
  </script>

</head>

<body>
  
  <h2>Projective texturing using Canvas and JavaScript</h2>

  <div id="demo">
    <div class="handle"></div>
    <div class="handle"></div>
    <div class="handle"></div>
    <div class="handle"></div>

    <div id="canvas"></div>
  </div>

  <p class="box"><strong>Drag the blue handles or change the settings to the right.</strong></p>
  
  <p><small>By Steven Wittens. For more information, see the related <a href="http://acko.net/blog/projective-texturing-with-canvas">blog post</a>. Tested on Safari and Firefox 3.</small></p>
  
  <form>
  <div><button onclick="changeImage(); return false;">Change image</button><button onclick="if (!demod) runDemo(); else stopDemo(); return false;" id="demo-button">Run Demo</button></div>
  <div><label><input type="checkbox" value="1" checked="checked" onchange="options.wireframe = this.checked; update();"> Show subdivision wireframe</label></div>
  <div><label><strong>Patch size</strong>: <input type="text" size="3" value="64" onkeyup="options.patchSize = this.value; update()"> pixels</label></div>
  <div><label><strong>Subdivision limit</strong>: <input type="text" size="3" value="5" onkeyup="options.subdivisionLimit = this.value; update()"> steps</label></div>
  </form>

    <script type="text/javascript">
    var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
    document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
    </script>
    <script type="text/javascript">
    var pageTracker = _gat._getTracker("UA-288349-2");
    pageTracker._initData();
    pageTracker._trackPageview();
    </script>
    
</body>

</html>
