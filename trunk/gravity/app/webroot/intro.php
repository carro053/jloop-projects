
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
  
  <script type="text/javascript" charset="utf-8">
    var imageCnt = 0;
    function changeImage() {      
      imageCnt = (imageCnt + 1) % 3;
      options.image = 'image'+ (imageCnt + 1) +'.jpg';
      refresh();
    }
    
    var timer = null;
    var t = 0, rx = 0, ry = 0, rz = 0, oldpoints;
    function runDemo() {
      oldpoints = [].concat(points);
      $('div.handle').hide();
      timer = setTimeout(demoTick, 20);
      $('#demo-button').html('Stop demo');
    }
    function demoTick() {
      t += 0.01;
      rx += (Math.sin(t) + Math.sin(t * .332) + 1) * .1;
      ry += (Math.cos(t *.841) + Math.sin(t * .632) + .8) * .031;
      rz += (Math.cos(3 + t *.767) + Math.sin(-t * 1.132) - .8) * .011;
      
      var cx = Math.cos(rx), sx = Math.sin(rx), cy = Math.cos(ry), sy = Math.sin(ry), cz = Math.cos(rz), sz = Math.sin(rz);
      
      var pts = [[1, 0, 0], [0, 1, 0], [0, -1, 0], [-1, 0, 0]];
      for (i in pts) {
        var x1 = pts[i][0], y1 = pts[i][1], z1 = pts[i][2];
        var x2 = x1 * cy - z1 * sy,
            y2 = y1,
            z2 = x1 * sy + z1 * cy;

        var x3 = x2,
            y3 = y2 * cx - z2 * sx,
            z3 = y2 * sx + z2 * cx;

        var x4 = y3 * sz + x3 * cz,
            y4 = y3 * cz - x3 * sz,
            z4 = z3;
          
        points[i] = [x4 / (z4 + 2) * 300 + 300, y4 / (z4 + 2) * 300 + 300];
      }

      update();

      if (timer) {
        timer = setTimeout(demoTick, 20);        
      }
      else {
        points = oldpoints;
        update();        
      }
    }
    function stopDemo() {
      $('#demo-button').html('Run demo');
      $('div.handle').show();
      timer = null;
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
  <div><button onclick="changeImage(); return false;">Change image</button><button onclick="if (!timer) runDemo(); else stopDemo(); return false;" id="demo-button">Run Demo</button></div>
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
