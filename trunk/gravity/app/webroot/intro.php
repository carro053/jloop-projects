<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Intro</title>
<script type="text/javascript" src="/matrix.js"></script>
<script type="text/javascript" src="/projective.js"></script>
<script type="text/javascript" charset="utf-8">
    var timer = null;
    var t = 0, rx = 0, ry = 0, rz = 0, oldpoints;
    function runDemo() {
      oldpoints = [].concat(points);
      timer = setTimeout(demoTick, 20);
    }
    function demoTick() {
      t += 0.01;
      
      points[0] = [Math.round(t * 20), 500 - Math.round(t * 100)];
      points[1] = [200 - Math.round(t * 20), 500 - Math.round(t * 100)];
      points[2] = [Math.round(t * 20), 600 - Math.round(t * 110)];
      points[3] = [200 - Math.round(t * 20), 600 - Math.round(t * 110)];

      update();

      if (timer) {
        timer = setTimeout(demoTick, 20);        
      }else {
        points = oldpoints;
        update();        
      }
    }
    function stopDemo() {
      timer = null;
    }
  </script>
</head>
<body style="background-color:#000000;">
  <h2>Projective texturing using Canvas and JavaScript</h2>
  <div id="demo">
    <div id="canvas"><canvas id="introcanvas" width="800" height="1000"></canvas></div>
  </div>
</body>
</html>