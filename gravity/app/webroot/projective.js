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
  image.src = options.image;
}

/**
 * Initialize the handles and canvas.
 */
function init() {

  // Create canvas and load image.
  refresh();
  // Render image.
  update();
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
  });
  
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