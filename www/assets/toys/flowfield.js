PadToys.register('flowfield', function (k, cfg) {
  var W = 640, H = 420, MAX = 6000;
  var n = cfg.particles, scale = cfg.scale, speed = cfg.speed, fade = cfg.fade, z = 0, whirl = null, fields = 1, said = 0, washed = 0;
  var x = new Float32Array(MAX), y = new Float32Array(MAX), px = new Float32Array(MAX), py = new Float32Array(MAX), age = new Uint16Array(MAX), span = new Uint16Array(MAX);
  var ctx = k.canvas(W, H, 'A flow field drawn by drifting particles', false), canvas = ctx.canvas;

  // Perlin's improved noise, its permutation shuffled by the toy's generator: a seed= gives
  // the same field, a click a new one.
  var perm = new Uint8Array(512);
  function reseed() {
    var p = [];
    for (var i = 0; i < 256; i++) p.push(i);
    k.shuffle(p);
    for (i = 0; i < 512; i++) perm[i] = p[i & 255];
  }
  function fadeCurve(t) { return t * t * t * (t * (t * 6 - 15) + 10); }
  function lerp(t, a, b) { return a + t * (b - a); }
  function grad(h, x, y, z) {
    h &= 15;
    var u = h < 8 ? x : y, v = h < 4 ? y : (h === 12 || h === 14 ? x : z);
    return ((h & 1) ? -u : u) + ((h & 2) ? -v : v);
  }
  function noise(x, y, z) {
    var X = Math.floor(x) & 255, Y = Math.floor(y) & 255, Z = Math.floor(z) & 255;
    x -= Math.floor(x); y -= Math.floor(y); z -= Math.floor(z);
    var u = fadeCurve(x), v = fadeCurve(y), w = fadeCurve(z);
    var A = perm[X] + Y, AA = perm[A] + Z, AB = perm[A + 1] + Z, B = perm[X + 1] + Y, BA = perm[B] + Z, BB = perm[B + 1] + Z;
    return lerp(w, lerp(v, lerp(u, grad(perm[AA], x, y, z), grad(perm[BA], x - 1, y, z)), lerp(u, grad(perm[AB], x, y - 1, z), grad(perm[BB], x - 1, y - 1, z))),
                   lerp(v, lerp(u, grad(perm[AA + 1], x, y, z - 1), grad(perm[BA + 1], x - 1, y, z - 1)), lerp(u, grad(perm[AB + 1], x, y - 1, z - 1), grad(perm[BB + 1], x - 1, y - 1, z - 1))));
  }

  function spawn(i) {
    x[i] = px[i] = k.range(0, W);
    y[i] = py[i] = k.range(0, H);
    age[i] = 0;
    span[i] = 80 + k.int(320);
  }
  for (var i = 0; i < MAX; i++) spawn(i);

  function clear() { ctx.fillStyle = k.color('canvas'); ctx.fillRect(0, 0, W, H); }

  // The colours run round the palette with the direction of the current.
  var bands = [];
  function colours() {
    var a = 0.16 + (k.dark() ? 0.08 : 0), stops = ['accent', 'accent3', 'accent2', 'good'];
    bands = [];
    for (var b = 0; b < 32; b++) {
      var f = b / 32 * stops.length, j = Math.floor(f), t = f - j, p = k.rgb(stops[j % stops.length]), q = k.rgb(stops[(j + 1) % stops.length]);
      bands.push('rgba(' + Math.round(p[0] + (q[0] - p[0]) * t) + ',' + Math.round(p[1] + (q[1] - p[1]) * t) + ',' + Math.round(p[2] + (q[2] - p[2]) * t) + ',' + a.toFixed(2) + ')');
    }
  }

  function step(dt) {
    var f = 0.0012 + scale * 0.0011, v = speed * 0.6 * dt * 60, i, a;
    z += dt * 0.04;
    // The trails fade by a thin wash of the canvas colour - gathered up and laid on every
    // few frames, as a wash too thin rounds to nothing in eight bits and leaves a grey haze.
    if (fade) {
      washed += fade * 0.006 * dt * 60;
      if (washed >= 0.12) { ctx.fillStyle = k.alpha('canvas', Math.min(1, washed).toFixed(3)); ctx.fillRect(0, 0, W, H); washed = 0; }
    }
    ctx.lineWidth = 1;
    var byBand = [];
    for (i = 0; i < n; i++) {
      a = noise(x[i] * f, y[i] * f, z) * Math.PI * 2.5;
      var dx = Math.cos(a) * v, dy = Math.sin(a) * v;
      // The pointer stirs: inside its reach the particles also go round it.
      if (whirl) {
        var wx = x[i] - whirl.x, wy = y[i] - whirl.y, d = Math.hypot(wx, wy);
        if (d < 90 && d > 1) { var s = (1 - d / 90) * v * 2.2; dx += -wy / d * s; dy += wx / d * s; }
      }
      px[i] = x[i]; py[i] = y[i];
      x[i] += dx; y[i] += dy;
      if (++age[i] > span[i] || x[i] < 0 || y[i] < 0 || x[i] >= W || y[i] >= H) { spawn(i); continue; }
      var band = Math.floor(((Math.atan2(dy, dx) / (Math.PI * 2)) + 1) % 1 * 32);
      (byBand[band] = byBand[band] || []).push(i);
    }
    // One path per colour band: a few dozen strokes a frame rather than thousands.
    for (var b = 0; b < byBand.length; b++) {
      if (!byBand[b]) continue;
      ctx.strokeStyle = bands[b];
      ctx.beginPath();
      byBand[b].forEach(function (j) { ctx.moveTo(px[j], py[j]); ctx.lineTo(x[j], y[j]); });
      ctx.stroke();
    }
    var now = Date.now();
    if (now - said > 1000) {
      said = now;
      k.info(n.toLocaleString('en') + ' particles · field ' + fields + ' · scale ' + scale + ' · fade ' + (fade || 'off'));
      k.describe(canvas, 'A flow field: ' + n + ' particles drifting on currents of noise, their trails painting the flow' + (whirl ? ', stirred by the pointer' : ''));
    }
  }

  k.loop(function (dt) { step(dt); }, function () { k.info(n.toLocaleString('en') + ' particles · field ' + fields + ' · scale ' + scale + ' · fade ' + (fade || 'off')); });

  function newField() { reseed(); fields++; clear(); for (var i = 0; i < n; i++) spawn(i); k.status('Field ' + fields); }
  k.pointer(canvas, {
    down: function (p) { whirl = { x: p.x, y: p.y, t: Date.now() }; },
    move: function (p, e, pressed) { whirl = { x: p.x, y: p.y, t: whirl ? whirl.t : Date.now() }; },
    up: function (p) { if (whirl && Date.now() - whirl.t < 250) newField(); },
    leave: function () { whirl = null; }
  });

  k.keys({ r: newField, c: clear });
  k.onPlay = function () { k.status('Drifting - the pointer stirs, a click makes a new field'); };
  k.onPause = function () { k.status('Paused'); };

  k.row();
  k.playButton();
  k.button('New field', newField, 'Another field of currents (R)');
  k.button('Clear', clear, 'Wipe the trails (C)');
  k.slider('Particles', 100, MAX, 100, n, function (v) { for (var i = n; i < v; i++) spawn(i); n = v; });
  k.row();
  k.slider('Scale', 1, 10, 1, scale, function (v) { scale = v; });
  k.slider('Speed', 1, 10, 1, speed, function (v) { speed = v; });
  k.slider('Fade', 0, 10, 1, fade, function (v) { fade = v; }, function (v) { return v || 'off'; });
  k.theme(function () { colours(); clear(); });

  reseed();
  colours();
  clear();
  k.still(function () { for (var i = 0; i < 240; i++) step(1 / 60); k.info(n.toLocaleString('en') + ' particles · field ' + fields + ' · scale ' + scale + ' · fade ' + (fade || 'off')); });
  k.begin();
});
