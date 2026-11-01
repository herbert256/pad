PadToys.register('boids', function (k, cfg) {
  var W = 640, H = 420, SEE = 48, NEAR = 18, MARGIN = 50, MAX = 600;
  var n = cfg.birds, sep = cfg.separation, ali = cfg.alignment, coh = cfg.cohesion, speed = cfg.speed;
  var x = new Float32Array(MAX), y = new Float32Array(MAX), vx = new Float32Array(MAX), vy = new Float32Array(MAX);
  var hawk = null, said = 0, cols = Math.ceil(W / SEE), rows = Math.ceil(H / SEE), heads = new Int32Array(cols * rows), nexts = new Int32Array(MAX);
  var ctx = k.canvas(W, H, 'A flock of birds', false), canvas = ctx.canvas;

  function spawn(i) {
    x[i] = k.range(MARGIN, W - MARGIN);
    y[i] = k.range(MARGIN, H - MARGIN);
    var a = k.range(0, Math.PI * 2);
    vx[i] = Math.cos(a) * 2;
    vy[i] = Math.sin(a) * 2;
  }
  for (var i = 0; i < MAX; i++) spawn(i);

  // The birds are filed in a grid of squares as wide as they see, so each looks only at the
  // birds of its own square and the eight around it - not at the whole flock.
  function file() {
    heads.fill(-1);
    for (var i = 0; i < n; i++) {
      var c = Math.min(cols - 1, Math.max(0, Math.floor(x[i] / SEE))) + Math.min(rows - 1, Math.max(0, Math.floor(y[i] / SEE))) * cols;
      nexts[i] = heads[c];
      heads[c] = i;
    }
  }

  function step(dt) {
    var f = dt * 60, top = 1.4 + speed * 0.35, i, j;
    file();
    for (i = 0; i < n; i++) {
      var ax = 0, ay = 0, cx = 0, cy = 0, sx = 0, sy = 0, seen = 0;
      var gx = Math.floor(x[i] / SEE), gy = Math.floor(y[i] / SEE);
      for (var oy = -1; oy <= 1; oy++) for (var ox = -1; ox <= 1; ox++) {
        var X = gx + ox, Y = gy + oy;
        if (X < 0 || Y < 0 || X >= cols || Y >= rows) continue;
        for (j = heads[X + Y * cols]; j >= 0; j = nexts[j]) {
          if (j === i) continue;
          var dx = x[j] - x[i], dy = y[j] - y[i], d2 = dx * dx + dy * dy;
          if (d2 > SEE * SEE) continue;
          seen++;
          ax += vx[j]; ay += vy[j];
          cx += x[j]; cy += y[j];
          if (d2 < NEAR * NEAR && d2 > 0) { var d = Math.sqrt(d2); sx -= dx / d * (NEAR - d) / NEAR; sy -= dy / d * (NEAR - d) / NEAR; }
        }
      }
      var steerX = 0, steerY = 0;
      if (seen) {
        steerX += (ax / seen - vx[i]) * ali * 0.012 + (cx / seen - x[i]) * coh * 0.0009;
        steerY += (ay / seen - vy[i]) * ali * 0.012 + (cy / seen - y[i]) * coh * 0.0009;
      }
      steerX += sx * sep * 0.06;
      steerY += sy * sep * 0.06;
      // The edges turn them back gently, as a wall of air would.
      if (x[i] < MARGIN) steerX += (MARGIN - x[i]) * 0.004; else if (x[i] > W - MARGIN) steerX -= (x[i] - W + MARGIN) * 0.004;
      if (y[i] < MARGIN) steerY += (MARGIN - y[i]) * 0.004; else if (y[i] > H - MARGIN) steerY -= (y[i] - H + MARGIN) * 0.004;
      // The hawk: within reach, fleeing beats every other rule.
      if (hawk) {
        var hx = x[i] - hawk.x, hy = y[i] - hawk.y, hd = Math.hypot(hx, hy);
        if (hd < 90 && hd > 0) { steerX += hx / hd * (90 - hd) * 0.012; steerY += hy / hd * (90 - hd) * 0.012; }
      }
      vx[i] += steerX * f;
      vy[i] += steerY * f;
      var s = Math.hypot(vx[i], vy[i]), lo = top * 0.45;
      if (s > top) { vx[i] *= top / s; vy[i] *= top / s; }
      else if (s < lo && s > 0) { vx[i] *= lo / s; vy[i] *= lo / s; }
    }
    for (i = 0; i < n; i++) {
      x[i] = Math.min(W - 1, Math.max(1, x[i] + vx[i] * f));
      y[i] = Math.min(H - 1, Math.max(1, y[i] + vy[i] * f));
    }
  }

  function draw() {
    ctx.fillStyle = k.color('canvas');
    ctx.fillRect(0, 0, W, H);
    // A bird is a small dart pointing the way it flies, its colour turning with its heading.
    var a = k.rgb('accent'), b = k.rgb('accent3'), c = k.rgb('good');
    for (var i = 0; i < n; i++) {
      var s = Math.hypot(vx[i], vy[i]) || 1, ux = vx[i] / s, uy = vy[i] / s, h = (Math.atan2(uy, ux) / Math.PI + 1) / 2;
      var p = h < 0.5 ? a : b, q = h < 0.5 ? b : c, t = (h % 0.5) * 2;
      ctx.fillStyle = 'rgb(' + Math.round(p[0] + (q[0] - p[0]) * t) + ',' + Math.round(p[1] + (q[1] - p[1]) * t) + ',' + Math.round(p[2] + (q[2] - p[2]) * t) + ')';
      ctx.beginPath();
      ctx.moveTo(x[i] + ux * 7, y[i] + uy * 7);
      ctx.lineTo(x[i] - ux * 4 - uy * 3.5, y[i] - uy * 4 + ux * 3.5);
      ctx.lineTo(x[i] - ux * 2, y[i] - uy * 2);
      ctx.lineTo(x[i] - ux * 4 + uy * 3.5, y[i] - uy * 4 - ux * 3.5);
      ctx.closePath();
      ctx.fill();
    }
    if (hawk) {
      ctx.strokeStyle = k.alpha('bad', 0.35);
      ctx.lineWidth = 1;
      ctx.beginPath(); ctx.arc(hawk.x, hawk.y, 90, 0, Math.PI * 2); ctx.stroke();
      ctx.fillStyle = k.color('bad');
      ctx.beginPath(); ctx.arc(hawk.x, hawk.y, 6, 0, Math.PI * 2); ctx.fill();
    }
    k.info(n + ' birds · separation ' + sep + ' · alignment ' + ali + ' · cohesion ' + coh);
    var now = Date.now();
    if (now - said > 1500) {
      said = now;
      var mx = 0, my = 0;
      for (var j = 0; j < n; j++) { mx += vx[j]; my += vy[j]; }
      var order = Math.hypot(mx, my) / n / (1.4 + speed * 0.35);
      k.describe(canvas, 'A flock of ' + n + ' birds' + (order > 0.6 ? ', flying mostly one way' : order > 0.3 ? ', in a few groups' : ', scattered') + (hawk ? ', fleeing the pointer' : ''));
    }
  }

  k.loop(function (dt) { step(dt); draw(); }, draw);

  k.pointer(canvas, {
    down: function (p) {
      // A click scatters the birds around it.
      for (var i = 0; i < n; i++) {
        var dx = x[i] - p.x, dy = y[i] - p.y, d = Math.hypot(dx, dy);
        if (d < 150 && d > 0) { vx[i] += dx / d * 5 * (1 - d / 150); vy[i] += dy / d * 5 * (1 - d / 150); }
      }
      hawk = { x: p.x, y: p.y };
      k.status('Scattered');
      if (!k.playing) draw();
    },
    move: function (p) { hawk = { x: p.x, y: p.y }; },
    leave: function () { hawk = null; if (!k.playing) draw(); }
  });

  k.onPlay = function () { k.status('Flying - the pointer is a hawk, a click scatters them'); };
  k.onPause = function () { k.status('Paused'); };

  k.row();
  k.playButton();
  k.button('Scatter', function () { for (var i = 0; i < n; i++) spawn(i); draw(); k.status('Every bird somewhere new'); }, 'Put every bird somewhere new');
  k.slider('Birds', 10, MAX, 10, n, function (v) { for (var i = n; i < v; i++) spawn(i); n = v; if (!k.playing) draw(); });
  k.slider('Speed', 1, 10, 1, speed, function (v) { speed = v; });
  k.row();
  k.slider('Separation', 0, 10, 1, sep, function (v) { sep = v; if (!k.playing) draw(); });
  k.slider('Alignment', 0, 10, 1, ali, function (v) { ali = v; if (!k.playing) draw(); });
  k.slider('Cohesion', 0, 10, 1, coh, function (v) { coh = v; if (!k.playing) draw(); });
  k.theme(draw);

  k.begin();
});
