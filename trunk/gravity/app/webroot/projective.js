/**
 * Projective texturing using Canvas.
 *
 * (c) Steven Wittens 2008
 * http://www.acko.net/
 */

var points = [
  [100, 100],
  [200 + Math.random() * 200, 100],
  [100, 200 + Math.random() * 200],
  [200 + Math.random() * 200, 200 + Math.random() * 200]
];

var options = {
  image: 'intro_text.png'
};

var refresh, update;

(function () {

var drag = null;
var offset = null;
var canvas = null, ctx = null, transform = null;
var image = null, iw = 0, ih = 0;

window.onload = function () {
  init();
};

/**
 * Refresh image.
 */
refresh = function () {
  image = new Image();
  image.onload = update;
  image.src = 'intro_text.png';
}

/**
 * Initialize the handles and canvas.
 */
function init() {

  // Create canvas and load image.
  canvas = document.getElementById('introcanvas');
  refresh();
  // Render image.
  update();
  runDemo();
}

/**
 * Update the display to match a new point configuration.
 */
update = function () {
  // Get extents.
  var minX = Infinity, maxX = -Infinity, minY = Infinity, maxY = -Infinity;
  for(var p in points)
  {
    minX = Math.min(minX, Math.floor(points[p][0]));
    maxX = Math.max(maxX, Math.ceil(points[p][0]));
    minY = Math.min(minY, Math.floor(points[p][1]));
    maxY = Math.max(maxY, Math.ceil(points[p][1]));
  }
  
  minX--; minY--; maxX++; maxY++;
  var width = maxX - minX;
  var height = maxY - minY;

  // Reshape canvas.
  canvas.style.left = minX +'px';
  canvas.style.top = minY +'px';
  canvas.width = width;
  canvas.height = height;
  
  // Measure texture.
  iw = image.width;
  ih = image.height;

  // Set up basic drawing context.
  ctx = canvas.getContext("2d");
  ctx.translate(-minX, -minY);
  ctx.clearRect(minX, minY, width, height);
  ctx.strokeStyle = "rgb(220,0,100)";

  transform = getProjectiveTransform(points);

  // Begin subdivision process.
  var ptl = transform.transformProjectiveVector([0, 0, 1]);
  var ptr = transform.transformProjectiveVector([1, 0, 1]);
  var pbl = transform.transformProjectiveVector([0, 1, 1]);
  var pbr = transform.transformProjectiveVector([1, 1, 1]);

  ctx.beginPath();
  ctx.moveTo(ptl[0], ptl[1]);
  ctx.lineTo(ptr[0], ptr[1]);
  ctx.lineTo(pbr[0], pbr[1]);
  ctx.lineTo(pbl[0], pbl[1]);
  ctx.closePath();
  ctx.clip();
  divide(0, 0, 1, 1, ptl, ptr, pbl, pbr, options.subdivisionLimit);

}

/**
 * Render a projective patch.
 */
function divide(u1, v1, u4, v4, p1, p2, p3, p4, limit) {

  // Render this patch.
  ctx.save();

  // Set clipping path.
  ctx.beginPath();
  ctx.moveTo(p1[0], p1[1]);
  ctx.lineTo(p2[0], p2[1]);
  ctx.lineTo(p4[0], p4[1]);
  ctx.lineTo(p3[0], p3[1]);
  ctx.closePath();
  //ctx.clip();
  
  // Get patch edge vectors.
  var d12 = [p2[0] - p1[0], p2[1] - p1[1]];
  var d24 = [p4[0] - p2[0], p4[1] - p2[1]];
  var d43 = [p3[0] - p4[0], p3[1] - p4[1]];
  var d31 = [p1[0] - p3[0], p1[1] - p3[1]];
  
  // Find the corner that encloses the most area
  var a1 = Math.abs(d12[0] * d31[1] - d12[1] * d31[0]);
  var a2 = Math.abs(d24[0] * d12[1] - d24[1] * d12[0]);
  var a4 = Math.abs(d43[0] * d24[1] - d43[1] * d24[0]);
  var a3 = Math.abs(d31[0] * d43[1] - d31[1] * d43[0]);
  var amax = Math.max(Math.max(a1, a2), Math.max(a3, a4));
  var dx = 0, dy = 0, padx = 0, pady = 0;
  
  // Align the transform along this corner.
  switch (amax) {
    case a1:
      ctx.transform(d12[0], d12[1], -d31[0], -d31[1], p1[0], p1[1]);
      // Calculate 1.05 pixel padding on vector basis.
      if (u4 != 1) padx = 1.05 / Math.sqrt(d12[0] * d12[0] + d12[1] * d12[1]);
      if (v4 != 1) pady = 1.05 / Math.sqrt(d31[0] * d31[0] + d31[1] * d31[1]);
      break;
    case a2:
      ctx.transform(d12[0], d12[1],  d24[0],  d24[1], p2[0], p2[1]);
      // Calculate 1.05 pixel padding on vector basis.
      if (u4 != 1) padx = 1.05 / Math.sqrt(d12[0] * d12[0] + d12[1] * d12[1]);
      if (v4 != 1) pady = 1.05 / Math.sqrt(d24[0] * d24[0] + d24[1] * d24[1]);
      dx = -1;
      break;
    case a4:
      ctx.transform(-d43[0], -d43[1], d24[0], d24[1], p4[0], p4[1]);
      // Calculate 1.05 pixel padding on vector basis.
      if (u4 != 1) padx = 1.05 / Math.sqrt(d43[0] * d43[0] + d43[1] * d43[1]);
      if (v4 != 1) pady = 1.05 / Math.sqrt(d24[0] * d24[0] + d24[1] * d24[1]);
      dx = -1;
      dy = -1;
      break;
    case a3:
      // Calculate 1.05 pixel padding on vector basis.
      ctx.transform(-d43[0], -d43[1], -d31[0], -d31[1], p3[0], p3[1]);
      if (u4 != 1) padx = 1.05 / Math.sqrt(d43[0] * d43[0] + d43[1] * d43[1]);
      if (v4 != 1) pady = 1.05 / Math.sqrt(d31[0] * d31[0] + d31[1] * d31[1]);
      dy = -1;
      break;
  }
  
  // Calculate image padding to match.
  var du = (u4 - u1);
  var dv = (v4 - v1);
  var padu = padx * du;
  var padv = pady * dv;
  
  ctx.drawImage(
    image,
    u1 * iw,
    v1 * ih,
    Math.min(u4 - u1 + padu, 1) * iw,
    Math.min(v4 - v1 + padv, 1) * ih,
    dx, dy,
    1 + padx, 1 + pady
  );
  ctx.restore();
}

/**
 * Calculate a projective transform that maps [0,1]x[0,1] onto the given set of points.
 */
function getProjectiveTransform(points) {
  var eqMatrix = new Matrix(9, 8, [
    [ 1, 1, 1,   0, 0, 0, -points[3][0],-points[3][0],-points[3][0] ], 
    [ 0, 1, 1,   0, 0, 0,  0,-points[2][0],-points[2][0] ],
    [ 1, 0, 1,   0, 0, 0, -points[1][0], 0,-points[1][0] ],
    [ 0, 0, 1,   0, 0, 0,  0, 0,-points[0][0] ],

    [ 0, 0, 0,  -1,-1,-1,  points[3][1], points[3][1], points[3][1] ],
    [ 0, 0, 0,   0,-1,-1,  0, points[2][1], points[2][1] ],
    [ 0, 0, 0,  -1, 0,-1,  points[1][1], 0, points[1][1] ],
    [ 0, 0, 0,   0, 0,-1,  0, 0, points[0][1] ]

  ]);
  
  var kernel = eqMatrix.rowEchelon().values;
  var transform = new Matrix(3, 3, [
    [-kernel[0][8], -kernel[1][8], -kernel[2][8]],
    [-kernel[3][8], -kernel[4][8], -kernel[5][8]],
    [-kernel[6][8], -kernel[7][8],             1]
  ]);
  return transform;
}

})();