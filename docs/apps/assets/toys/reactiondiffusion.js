PadToys.register('reactiondiffusion', function (k, cfg) {
  var W = cfg.width, H = cfg.height, N = W * H, SCALE = Math.max(1, Math.min(4, Math.floor(Math.min(600 / W, 480 / H))));
  // Pearson (1993): Du = 2e-5 and Dv = 1e-5 on a square 2.5 across split into 256 steps, a
  // time step of 1 - Du dt / dx² and Dv dt / dx² - with the five-point Laplacian.
  var DU = 0.2097152, DV = 0.1048576, STILL = 4000, BUDGET = 10;
  var F = cfg.feed, K = cfg.kill, speed = cfg.speed, start = cfg.initial, presets = cfg.patterns, pattern = cfg.pattern;
  if (!presets[pattern] || presets[pattern][0] !== F || presets[pattern][1] !== K) pattern = 'yours';
  var u = new Float32Array(N), v = new Float32Array(N), u2 = new Float32Array(N), v2 = new Float32Array(N);
  var steps = 0, acc = 0, said = 0, cursor = { x: W >> 1, y: H >> 1 }, keyboard = false, developing = 0;
  var NAMES = { coral: 'Coral', mitosis: 'Mitosis', spots: 'Spots', mazes: 'Mazes', worms: 'Worms', holes: 'Holes', waves: 'Waves', chaos: 'Chaos', yours: 'Your own' };

  var ctx = k.canvas(W, H, 'Reaction-diffusion', true), canvas = ctx.canvas;
  canvas.classList.remove('is-pixels');
  canvas.style.width = W * SCALE + 'px';
  var image = ctx.createImageData(W, H), pixels = new Uint32Array(image.data.buffer), lut = new Uint32Array(256);

  function pack(c) { return (255 << 24 | Math.round(c[2]) << 16 | Math.round(c[1]) << 8 | Math.round(c[0])) >>> 0; }
  // No V is the canvas, some the accent, the most the accent deepened toward the ink - so it
  // reads in either scheme.
  function colours() {
    var c = k.rgb('accent'), ink = k.rgb('ink'), stops = [k.rgb('canvas'), c, [c[0] + (ink[0] - c[0]) * 0.5, c[1] + (ink[1] - c[1]) * 0.5, c[2] + (ink[2] - c[2]) * 0.5]];
    for (var i = 0; i < 256; i++) {
      var t = i / 255 * 2, j = Math.min(1, Math.floor(t)), f = t - j, a = stops[j], b = stops[j + 1];
      lut[i] = pack([a[0] + (b[0] - a[0]) * f, a[1] + (b[1] - a[1]) * f, a[2] + (b[2] - a[2]) * f]);
    }
  }

  // One step of the model on the grid, its edges joined: U is fed toward 1 at the rate F, V
  // is taken away at F + k, and where they meet U V² turns U into V.
  function step() {
    for (var y = 0; y < H; y++) {
      var row = y * W, up = ((y + H - 1) % H) * W, down = ((y + 1) % H) * W;
      for (var x = 0; x < W; x++) {
        var i = row + x, l = row + (x ? x - 1 : W - 1), r = row + (x < W - 1 ? x + 1 : 0);
        var a = u[i], b = v[i], abb = a * b * b;
        u2[i] = a + DU * (u[l] + u[r] + u[up + x] + u[down + x] - 4 * a) - abb + F * (1 - a);
        v2[i] = b + DV * (v[l] + v[r] + v[up + x] + v[down + x] - 4 * b) + abb - (F + K) * b;
      }
    }
    var t = u; u = u2; u2 = t;
    t = v; v = v2; v2 = t;
    steps++;
  }

  // A square of the grid set to U = 1/2, V = 1/4 - Pearson's start - wrapping round the edges.
  function square(cx, cy, size) {
    for (var dy = 0; dy < size; dy++) for (var dx = 0; dx < size; dx++) {
      var x = ((cx + dx) % W + W) % W, y = ((cy + dy) % H + H) % H, i = y * W + x;
      u[i] = 0.5;
      v[i] = 0.25;
    }
  }
  // What the grid starts as: all U and no V, then V in a square in the middle (Pearson's
  // twenty by twenty), in a few squares here and there, or speckled all over - and every
  // value stirred by a percent, which breaks the symmetry of a square.
  function begin(kind) {
    u.fill(1);
    v.fill(0);
    steps = 0;
    acc = 0;
    if (kind === 'center') square((W >> 1) - 10, (H >> 1) - 10, 20);
    else if (kind === 'noise') {
      for (var y = 0; y < H; y += 4) for (var x = 0; x < W; x += 4) if (k.chance(0.08)) square(x, y, 4);
    } else {
      var n = Math.max(3, Math.round(N / 2600));
      for (var s = 0; s < n; s++) square(k.int(W), k.int(H), 6 + k.int(8));
    }
    for (var i = 0; i < N; i++) {
      u[i] *= 1 + (k.rand() - 0.5) * 0.02;
      v[i] *= 1 + (k.rand() - 0.5) * 0.02;
    }
  }

  function covered() {
    var n = 0;
    for (var i = 0; i < N; i++) if (v[i] > 0.1) n++;
    return Math.round(100 * n / N);
  }
  function rate(x) { return (+x.toFixed(4)).toString(); }
  function draw() {
    for (var i = 0; i < N; i++) {
      var b = v[i] * 640;
      pixels[i] = lut[b <= 0 ? 0 : b >= 255 ? 255 : b | 0];
    }
    ctx.putImageData(image, 0, 0);
    if (keyboard && document.activeElement === canvas) {
      ctx.strokeStyle = k.color('mark');
      ctx.lineWidth = 1;
      ctx.beginPath();
      ctx.arc(cursor.x + 0.5, cursor.y + 0.5, 4, 0, Math.PI * 2);
      ctx.stroke();
    }
    k.info('Step ' + steps.toLocaleString('en') + ' · ' + NAMES[pattern] + ' · feed ' + rate(F) + ' · kill ' + rate(K) + ' · ' + speed + ' steps a second');
    var now = Date.now();
    if (now - said > 1000 || !k.playing || k.held) {
      said = now;
      k.describe(canvas, 'Reaction-diffusion, ' + (pattern === 'yours' ? 'feed ' + rate(F) + ' and kill ' + rate(K) : 'the ' + NAMES[pattern].toLowerCase() + ' pattern') + ', after ' + steps + ' steps: V covers ' + covered() + '% of the grid');
    }
  }

  // The steps go by a clock of their own - speed a second, whatever the screen's rate - and
  // a frame takes at most BUDGET milliseconds of them, so a slow machine runs slower rather
  // than falling behind for good.
  k.loop(function (dt) {
    acc += dt * speed;
    var n = Math.floor(acc), t0 = performance.now();
    acc -= n;
    while (n-- > 0) {
      step();
      if (performance.now() - t0 > BUDGET) { acc = 0; break; }
    }
    draw();
  }, draw);

  // What waits for Play shows the pattern grown: STILL steps worked out a slice at a time
  // out of sight, drawn once when they are done.
  function develop() {
    var job = ++developing, reduced = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    k.status('Growing the pattern for a still picture ...');
    (function slice() {
      if (job !== developing || k.playing) return;
      var t0 = performance.now();
      while (steps < STILL && performance.now() - t0 < BUDGET) step();
      if (steps < STILL) { setTimeout(slice, 0); return; }
      draw();
      k.status((reduced ? 'Paused, as reduced motion is asked for: the' : 'The') + ' pattern after ' + STILL + ' steps - press Play to watch it grow');
    })();
  }
  k.still(develop);

  // Painting: the pointer drops V where it goes, Shift or the right button wipes it away.
  var wiping = false, last = null;
  function dab(cx, cy, wipe) {
    var R = 4;
    for (var dy = -R; dy <= R; dy++) for (var dx = -R; dx <= R; dx++) {
      if (dx * dx + dy * dy > R * R) continue;
      var x = ((Math.round(cx) + dx) % W + W) % W, y = ((Math.round(cy) + dy) % H + H) % H, i = y * W + x;
      u[i] = wipe ? 1 : 0.5;
      v[i] = wipe ? 0 : 0.25;
    }
  }
  function stroke(p) {
    var from = last || p, n = Math.max(1, Math.ceil(Math.hypot(p.x - from.x, p.y - from.y) / 2));
    for (var s = 0; s <= n; s++) dab(from.x + (p.x - from.x) * s / n, from.y + (p.y - from.y) * s / n, wiping);
    last = p;
    developing++;
    if (!k.playing || k.held) draw();
  }
  k.pointer(canvas, {
    down: function (p, e) { keyboard = false; wiping = e.button === 2 || p.shift; last = null; stroke(p); },
    move: function (p, e, pressed) { if (pressed) stroke(p); },
    up: function () { last = null; }
  });

  function moveCursor(dx, dy) {
    return function () {
      keyboard = true;
      cursor.x = (cursor.x + dx * 4 + W) % W;
      cursor.y = (cursor.y + dy * 4 + H) % H;
      if (!k.playing || k.held) draw();
    };
  }
  function restart() { developing++; begin(start); draw(); k.status('Started again - ' + { spots: 'V in a few squares', center: 'V in a square in the middle', noise: 'V speckled all over' }[start]); }
  function wipe() { developing++; u.fill(1); v.fill(0); steps = 0; draw(); k.status('Cleared - paint V with the pointer, or the arrow keys and Enter'); }
  function stepOn() { developing++; k.pause(); for (var i = 0; i < 100; i++) step(); draw(); k.status('Step ' + steps); }

  k.keys({
    ArrowUp: moveCursor(0, -1), ArrowDown: moveCursor(0, 1), ArrowLeft: moveCursor(-1, 0), ArrowRight: moveCursor(1, 0),
    Enter: function () { keyboard = true; developing++; dab(cursor.x, cursor.y, false); if (!k.playing || k.held) draw(); },
    Delete: function () { keyboard = true; developing++; dab(cursor.x, cursor.y, true); if (!k.playing || k.held) draw(); },
    r: restart, c: wipe, s: stepOn
  });
  canvas.addEventListener('blur', function () { if (!k.playing) draw(); });

  // The map of the rates: kill across, feed up. Under the curve F = 4 (F + k)², k = √F / 2 - F,
  // the grid can only fade back to all U; the patterns live along it. A dot a named pattern,
  // the ring the rates now; click or drag to pick rates of your own.
  var MW = 180, MH = 150, K0 = 0.03, K1 = 0.07, F0 = 0, F1 = 0.09;
  var mctx = k.canvas(MW, MH, 'The map of the feed and kill rates', false), map = mctx.canvas;
  map.tabIndex = -1;
  function mx(kk) { return 8 + (kk - K0) / (K1 - K0) * (MW - 16); }
  function my(ff) { return MH - 8 - (ff - F0) / (F1 - F0) * (MH - 16); }
  function drawMap() {
    mctx.fillStyle = k.color('canvas');
    mctx.fillRect(0, 0, MW, MH);
    mctx.strokeStyle = k.color('line');
    mctx.lineWidth = 1;
    mctx.beginPath();
    for (var s = 0; s <= 120; s++) {
      var ff = F0 + (F1 - F0) * s / 120, kk = Math.sqrt(ff) / 2 - ff;
      if (s) mctx.lineTo(mx(kk), my(ff)); else mctx.moveTo(mx(kk), my(ff));
    }
    mctx.stroke();
    mctx.fillStyle = k.color('muted');
    mctx.font = '10px system-ui, sans-serif';
    mctx.fillText('kill →', MW - 40, MH - 2);
    mctx.save(); mctx.translate(9, 40); mctx.rotate(-Math.PI / 2); mctx.fillText('feed →', 0, 0); mctx.restore();
    for (var name in presets) {
      mctx.fillStyle = name === pattern ? k.color('accent2') : k.color('muted');
      mctx.beginPath();
      mctx.arc(mx(presets[name][1]), my(presets[name][0]), name === pattern ? 3.5 : 2.5, 0, Math.PI * 2);
      mctx.fill();
    }
    mctx.strokeStyle = k.color('accent');
    mctx.lineWidth = 2;
    mctx.beginPath();
    mctx.arc(Math.max(4, Math.min(MW - 4, mx(K))), Math.max(4, Math.min(MH - 4, my(F))), 6, 0, Math.PI * 2);
    mctx.stroke();
    k.describe(map, 'The map of the feed and kill rates: feed ' + rate(F) + ', kill ' + rate(K) + (pattern === 'yours' ? '' : ', the ' + NAMES[pattern].toLowerCase() + ' pattern'));
  }
  function pickAt(p) {
    var kk = K0 + (p.x - 8) / (MW - 16) * (K1 - K0), ff = F0 + (MH - 8 - p.y) / (MH - 16) * (F1 - F0);
    setRates(Math.round(Math.max(0, Math.min(0.1, ff)) * 10000) / 10000, Math.round(Math.max(0, Math.min(0.1, kk)) * 10000) / 10000, 'yours');
  }
  k.pointer(map, { down: pickAt, move: function (p, e, pressed) { if (pressed) pickAt(p); } });

  function setRates(ff, kk, name) {
    F = ff;
    K = kk;
    pattern = name;
    feedIn.set(F);
    killIn.set(K);
    if (patternIn.value !== name) patternIn.value = name;
    drawMap();
    if (!k.playing || k.held) draw();
  }

  k.onPlay = function () { developing++; k.status('Reacting - paint V with the pointer, Shift wipes it'); };
  k.onPause = function () { k.status('Paused at step ' + steps); };

  k.row();
  k.playButton();
  k.button('Step', stepOn, 'A hundred steps (S)');
  k.button('Start again', restart, 'The start again (R)');
  k.button('Clear', wipe, 'All U, no V (C)');
  k.row();
  var patternIn = k.select('Pattern', Object.keys(presets).concat(['yours']).map(function (n) { return [n, NAMES[n] || n]; }), pattern, function (v) {
    if (v === 'yours') return;
    setRates(presets[v][0], presets[v][1], v);
    k.status(NAMES[v] + ': feed ' + rate(F) + ', kill ' + rate(K));
  }).querySelector('select');
  k.select('Start', [['spots', 'Spots'], ['center', 'Square'], ['noise', 'Noise']], start, function (v) { start = v; restart(); });
  k.row();
  var feedIn = k.slider('Feed', 0, 0.1, 0.0001, F, function (v) { F = v; pattern = 'yours'; patternIn.value = 'yours'; drawMap(); if (!k.playing || k.held) draw(); }, rate);
  var killIn = k.slider('Kill', 0, 0.1, 0.0001, K, function (v) { K = v; pattern = 'yours'; patternIn.value = 'yours'; drawMap(); if (!k.playing || k.held) draw(); }, rate);
  k.slider('Speed', 60, 3000, 60, speed, function (v) { speed = v; }, function (v) { return v + '/s'; });
  k.theme(function () { colours(); drawMap(); draw(); });

  colours();
  begin(start);
  drawMap();
  draw();
  k.begin('Reacting - paint V with the pointer, Shift wipes it');
});
