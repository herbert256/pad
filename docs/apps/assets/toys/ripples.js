PadToys.register('ripples', function (k, cfg) {
  var GW = 200, GH = 140, SCALE = 3, SPONGE = 18, C2 = 0.24;
  var setup = cfg.setup, freq = cfg.frequency, walls = false, steps = 0, rain = 0, said = 0;
  var u = new Float32Array(GW * GH), p = new Float32Array(GW * GH), wall = new Uint8Array(GW * GH), damp = new Float32Array(GW * GH), sources = [];
  var ctx = k.canvas(GW, GH, 'A ripple tank', true), canvas = ctx.canvas;
  canvas.style.width = GW * SCALE + 'px';
  var image = ctx.createImageData(GW, GH), pixels = new Uint32Array(image.data.buffer), lut = new Uint32Array(256), wallColour = 0;

  // The edges soak the waves up - a band where the water is damped more toward the rim - so
  // the tank looks as if it went on.
  for (var y = 0; y < GH; y++) for (var x = 0; x < GW; x++) {
    var e = Math.min(x, y, GW - 1 - x, GH - 1 - y);
    damp[y * GW + x] = e < SPONGE ? 0.999 - 0.2 * Math.pow((SPONGE - e) / SPONGE, 2) : 0.999;
  }

  function pack(c) { return (255 << 24 | Math.round(c[2]) << 16 | Math.round(c[1]) << 8 | Math.round(c[0])) >>> 0; }
  // Crests in the accent, troughs in the second accent, still water the canvas.
  function colours() {
    var bg = k.rgb('canvas'), hi = k.rgb('accent'), lo = k.rgb('accent2');
    for (var i = 0; i < 256; i++) {
      var v = (i - 127.5) / 127.5, a = Math.abs(v), c = v > 0 ? hi : lo;
      lut[i] = pack([bg[0] + (c[0] - bg[0]) * a, bg[1] + (c[1] - bg[1]) * a, bg[2] + (c[2] - bg[2]) * a]);
    }
    wallColour = pack(k.rgb('ink'));
  }

  function arrange(name) {
    u.fill(0); p.fill(0); wall.fill(0); sources = []; steps = 0;
    var x, y;
    if (name === 'two') {
      sources.push({ x: 70, y: GH / 2 - 16 }, { x: 70, y: GH / 2 + 16 });
    } else if (name === 'slits') {
      for (y = 0; y < GH; y++) if (Math.abs(y - (GH / 2 - 14)) > 3 && Math.abs(y - (GH / 2 + 14)) > 3) { wall[y * GW + 70] = 1; wall[y * GW + 71] = 1; }
      for (y = SPONGE; y < GH - SPONGE; y += 2) sources.push({ x: 24, y: y });
    }
  }

  function drop(cx, cy, amp, r) {
    for (var dy = -r; dy <= r; dy++) for (var dx = -r; dx <= r; dx++) {
      var x = Math.round(cx) + dx, y = Math.round(cy) + dy;
      if (x < 1 || y < 1 || x >= GW - 1 || y >= GH - 1) continue;
      var d2 = (dx * dx + dy * dy) / (r * r);
      if (d2 <= 1) u[y * GW + x] += amp * Math.exp(-d2 * 3);
    }
  }

  // One step of the wave equation: each point pulled toward its neighbours - the eight of
  // them, the corners weighed less, so a ring spreads round rather than square - with what it
  // had a step before for its speed; walls stay still.
  function step() {
    var t = steps * 2 * Math.PI * freq * 0.01;
    sources.forEach(function (s) { u[Math.round(s.y) * GW + Math.round(s.x)] = Math.sin(t) * (setup === 'slits' ? 0.5 : 1.2); });
    for (var y = 1; y < GH - 1; y++) {
      var row = y * GW;
      for (var x = 1; x < GW - 1; x++) {
        var i = row + x;
        if (wall[i]) { p[i] = 0; continue; }
        var lap = (4 * (u[i - 1] + u[i + 1] + u[i - GW] + u[i + GW]) + u[i - GW - 1] + u[i - GW + 1] + u[i + GW - 1] + u[i + GW + 1] - 20 * u[i]) / 6;
        p[i] = (2 * u[i] - p[i] + C2 * lap) * damp[i];
      }
    }
    var swap = u; u = p; p = swap;
    steps++;
  }

  function draw() {
    for (var i = 0; i < u.length; i++) {
      if (wall[i]) { pixels[i] = wallColour; continue; }
      var v = Math.tanh(u[i] * 2.2);
      pixels[i] = lut[Math.round((v + 1) * 127.5)];
    }
    ctx.putImageData(image, 0, 0);
    var now = Date.now();
    if (now - said > 1000) {
      said = now;
      var n = 0;
      for (var j = 0; j < wall.length; j++) n += wall[j];
      k.info({ two: 'Two sources in step', slits: 'A straight wave through two slits', pond: 'Raindrops on a pond' }[setup] + ' · frequency ' + freq + (n ? ' · ' + n + ' wall cells' : ''));
      k.describe(canvas, 'A ripple tank: ' + { two: 'two sources beating in step, their waves crossing in stripes of interference', slits: 'a straight wave passing two slits in a wall and spreading out beyond', pond: 'raindrops falling, rings spreading and crossing' }[setup]);
    }
  }

  k.loop(function (dt) {
    if (setup === 'pond') { rain -= dt; if (rain <= 0) { drop(k.range(20, GW - 20), k.range(20, GH - 20), k.range(1.2, 2.4), 3); rain = k.range(0.4, 1.4); } }
    for (var s = 0; s < 3; s++) step();
    draw();
  }, draw);

  // The pointer touches the water - or, with walls ticked or the Shift key down, builds a
  // wall where it goes; the right button rubs walls out.
  var last = null;
  function along(pt, fn) {
    var from = last || pt, n = Math.max(1, Math.ceil(Math.hypot(pt.x - from.x, pt.y - from.y)));
    for (var s = 0; s <= n; s++) fn(from.x + (pt.x - from.x) * s / n, from.y + (pt.y - from.y) * s / n);
    last = pt;
  }
  function build(x, y, on) {
    for (var dy = -1; dy <= 1; dy++) for (var dx = -1; dx <= 1; dx++) {
      var X = Math.round(x) + dx, Y = Math.round(y) + dy;
      if (X > 0 && Y > 0 && X < GW - 1 && Y < GH - 1) { wall[Y * GW + X] = on ? 1 : 0; if (on) { u[Y * GW + X] = 0; p[Y * GW + X] = 0; } }
    }
  }
  var mode = null;
  k.pointer(canvas, {
    down: function (pt, e) {
      last = null;
      mode = e.button === 2 ? 'erase' : (walls || pt.shift) ? 'wall' : 'water';
      if (mode === 'water') drop(pt.x, pt.y, 2.5, 4);
      else along(pt, function (x, y) { build(x, y, mode === 'wall'); });
      if (!k.playing) draw();
    },
    move: function (pt, e, pressed) {
      if (!pressed || !mode) return;
      if (mode === 'water') along(pt, function (x, y) { drop(x, y, 0.25, 3); });
      else along(pt, function (x, y) { build(x, y, mode === 'wall'); });
      if (!k.playing) draw();
    },
    up: function () { last = null; mode = null; }
  });

  k.keys({ c: function () { u.fill(0); p.fill(0); draw(); }, w: function () { wallBox.checked = walls = !walls; } });
  k.onPlay = function () { k.status('Rippling - click or drag to touch the water'); };
  k.onPause = function () { k.status('Still'); };

  k.row();
  k.playButton();
  k.button('Calm', function () { u.fill(0); p.fill(0); draw(); k.status('The water is calm'); }, 'Still the water (C)');
  k.button('Clear walls', function () { wall.fill(0); draw(); k.status('No walls'); }, 'Take the walls away');
  k.select('Setup', [['two', 'Two sources'], ['slits', 'Two slits'], ['pond', 'Pond']], setup, function (v) { setup = v; arrange(v); draw(); k.status({ two: 'Two sources', slits: 'Two slits', pond: 'A pond in the rain' }[v]); });
  k.row();
  var wallBox = k.check('Build walls', walls, function (v) { walls = v; k.status(v ? 'The pointer builds walls - right button rubs them out' : 'The pointer touches the water'); });
  k.slider('Frequency', 1, 10, 1, freq, function (v) { freq = v; });
  k.theme(function () { colours(); draw(); });

  colours();
  arrange(setup);
  k.begin();
});
