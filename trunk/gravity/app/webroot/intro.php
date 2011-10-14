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