PadToys.register('mandelbrot', function (k, cfg) {
  var W = 560, H = 400, SIZE = 1024;
  var maxIter = cfg.iterations, palette = cfg.palette, julia = cfg.julia, home = { cx: cfg.center[0], cy: cfg.center[1], zoom: cfg.zoom };
  var view = { cx: home.cx, cy: home.cy, zoom: home.zoom }, saved = null;
  var values = new Float32Array(W * H), lut = new Uint32Array(SIZE), inside = 0, offset = 0;
  var ctx = k.canvas(W, H, 'Mandelbrot set', true), canvas = ctx.canvas;
  var image = ctx.createImageData(W, H), pixels = new Uint32Array(image.data.buffer);
  var job = null, drawn = 0;

  function pack(c) { return (255 << 24 | Math.round(c[2]) << 16 | Math.round(c[1]) << 8 | Math.round(c[0])) >>> 0; }

  // A palette is a few colours, blended into SIZE steps the escape counts cycle through;
  // mono runs from the canvas to the ink, so it follows the page's scheme.
  var STOPS = {
    ocean: [[0, 7, 100], [32, 107, 203], [237, 255, 255], [255, 170, 0], [0, 2, 0]],
    fire: [[0, 0, 0], [120, 0, 10], [230, 60, 0], [255, 190, 20], [255, 250, 210], [120, 0, 10]],
    rainbow: null,
    mono: null
  };
  function makeLut() {
    var stops = STOPS[palette];
    if (palette === 'mono') { var c = k.rgb('canvas'), i = k.rgb('ink'), a = k.rgb('accent'); stops = [c, a, i, a]; }
    for (var n = 0; n < SIZE; n++) {
      var f = n / SIZE;
      if (palette === 'rainbow') { lut[n] = pack(hsl(f * 360, 0.85, 0.55)); continue; }
      var x = f * stops.length, j = Math.floor(x), t = x - j, p = stops[j % stops.length], q = stops[(j + 1) % stops.length];
      lut[n] = pack([p[0] + (q[0] - p[0]) * t, p[1] + (q[1] - p[1]) * t, p[2] + (q[2] - p[2]) * t]);
    }
    inside = palette === 'mono' ? pack(k.rgb('ink')) : pack([0, 0, 0]);
  }
  function hsl(h, s, l) {
    var c = (1 - Math.abs(2 * l - 1)) * s, x = c * (1 - Math.abs((h / 60) % 2 - 1)), m = l - c / 2, r = 0, g = 0, b = 0;
    if (h < 60) { r = c; g = x; } else if (h < 120) { r = x; g = c; } else if (h < 180) { g = c; b = x; }
    else if (h < 240) { g = x; b = c; } else if (h < 300) { r = x; b = c; } else { r = c; b = x; }
    return [(r + m) * 255, (g + m) * 255, (b + m) * 255];
  }

  function span() { return 3.5 / view.zoom; }
  function toPlane(px, py) { var s = span() / W; return { x: view.cx + (px - W / 2) * s, y: view.cy - (py - H / 2) * s }; }

  // The escape count of a point, smoothed by how far past the escape radius it got, so the
  // bands blend; -1 for a point that stays.
  function escape(x, y) {
    var zx, zy, cx, cy, n = 0, xx, yy;
    if (julia) { zx = x; zy = y; cx = julia[0]; cy = julia[1]; }
    else {
      zx = 0; zy = 0; cx = x; cy = y;
      // The main cardioid and the bulb left of it never escape: skipped at once.
      var q = (x - 0.25) * (x - 0.25) + y * y;
      if (q * (q + (x - 0.25)) <= 0.25 * y * y || (x + 1) * (x + 1) + y * y <= 0.0625) return -1;
    }
    while (n < maxIter) {
      xx = zx * zx; yy = zy * zy;
      if (xx + yy > 256) break;
      zy = 2 * zx * zy + cy;
      zx = xx - yy + cx;
      n++;
    }
    if (n >= maxIter) return -1;
    return n + 1 - Math.log(Math.log(zx * zx + zy * zy) / 2 / Math.LN2) / Math.LN2;
  }

  function colourAt(v) { return v < 0 ? inside : lut[(Math.floor(v * 12 + offset) % SIZE + SIZE) % SIZE]; }
  function paint() {
    for (var i = 0; i < values.length; i++) pixels[i] = colourAt(values[i]);
    ctx.putImageData(image, 0, 0);
    if (!julia && document.activeElement === canvas) {
      ctx.strokeStyle = 'rgba(255,255,255,0.8)';
      ctx.lineWidth = 1;
      ctx.beginPath();
      ctx.moveTo(W / 2 - 8, H / 2 + 0.5); ctx.lineTo(W / 2 + 8, H / 2 + 0.5);
      ctx.moveTo(W / 2 + 0.5, H / 2 - 8); ctx.lineTo(W / 2 + 0.5, H / 2 + 8);
      ctx.stroke();
    }
  }

  // Rendering goes in two passes - blocks of four pixels, then every pixel - a slice of rows
  // per animation frame, so the page stays free and a new view can start before the last is
  // done. It runs whether the colours cycle or not.
  function render() {
    if (job) cancelAnimationFrame(job.raf);
    job = { pass: 0, y: 0, raf: 0 };
    k.status('Drawing ...');
    describe();
    work();
  }
  function work() {
    var started = performance.now(), block = job.pass === 0 ? 4 : 1;
    while (job.y < H && performance.now() - started < 14) {
      for (var x = 0; x < W; x += block) {
        var p = toPlane(x + block / 2, job.y + block / 2), v = escape(p.x, p.y);
        if (block === 1) values[job.y * W + x] = v;
        else for (var dy = 0; dy < block && job.y + dy < H; dy++) for (var dx = 0; dx < block && x + dx < W; dx++) values[(job.y + dy) * W + x + dx] = v;
      }
      job.y += block;
    }
    paint();
    if (job.y >= H) {
      if (job.pass === 0) { job.pass = 1; job.y = 0; }
      else { job = null; drawn++; done(); return; }
    }
    job.raf = requestAnimationFrame(work);
  }
  function done() {
    k.status(julia ? 'The Julia set of ' + complex(julia[0], julia[1]) + ' - click to zoom, Mandelbrot to go back'
                   : 'Click to zoom in, Shift-click to zoom out, drag to move');
  }
  function complex(x, y) { return x.toFixed(4) + (y < 0 ? ' − ' : ' + ') + Math.abs(y).toFixed(4) + 'i'; }
  function describe() {
    var z = view.zoom >= 1000 ? Math.round(view.zoom).toLocaleString('en') : +view.zoom.toPrecision(3);
    k.info('Centre ' + complex(view.cx, view.cy) + ' · zoom ' + z + '× · ' + maxIter + ' iterations');
    k.describe(canvas, (julia ? 'The Julia set of ' + complex(julia[0], julia[1]) : 'The Mandelbrot set') + ', centred on ' + complex(view.cx, view.cy) + ' at ' + z + ' times zoom');
  }

  function zoomAt(px, py, f) {
    var p = toPlane(px, py);
    view.cx = p.x; view.cy = p.y;
    view.zoom = Math.max(0.5, Math.min(1e13, view.zoom * f));
    render();
  }

  // A press that does not move is a click - zoom; one that moves drags the picture along,
  // shown at once and drawn anew when let go.
  var press = null;
  k.pointer(canvas, {
    down: function (p, e) { press = { x: p.x, y: p.y, cx: view.cx, cy: view.cy, moved: false, out: p.shift || e.button === 2 }; },
    move: function (p, e, pressed) {
      if (!pressed || !press) return;
      var dx = p.x - press.x, dy = p.y - press.y;
      if (!press.moved && Math.abs(dx) + Math.abs(dy) < 4) return;
      press.moved = true;
      var s = span() / W;
      view.cx = press.cx - dx * s;
      view.cy = press.cy + dy * s;
      ctx.putImageData(image, Math.round(dx), Math.round(dy));
      describe();
    },
    up: function (p) {
      if (!press) return;
      if (press.moved) render();
      else zoomAt(p.x, p.y, press.out ? 0.5 : 2);
      press = null;
    }
  });

  function pan(dx, dy) { return function () { var s = span() / W; view.cx += dx * W / 8 * s; view.cy -= dy * H / 8 * s; render(); }; }
  function toJulia() {
    if (julia) return;
    saved = { cx: view.cx, cy: view.cy, zoom: view.zoom };
    julia = [view.cx, view.cy];
    view = { cx: 0, cy: 0, zoom: 1.2 };
    juliaBtn.textContent = 'Mandelbrot';
    render();
  }
  function toMandelbrot() {
    if (!julia) return;
    julia = null;
    view = saved || { cx: -0.6, cy: 0, zoom: 1 };
    juliaBtn.textContent = 'Julia';
    render();
  }
  function reset() { view = { cx: home.cx, cy: home.cy, zoom: home.zoom }; julia = cfg.julia; juliaBtn.textContent = julia ? 'Mandelbrot' : 'Julia'; render(); }

  k.keys({
    ArrowLeft: pan(-1, 0), ArrowRight: pan(1, 0), ArrowUp: pan(0, -1), ArrowDown: pan(0, 1),
    '+': function () { zoomAt(W / 2, H / 2, 2); }, '=': function () { zoomAt(W / 2, H / 2, 2); }, '-': function () { zoomAt(W / 2, H / 2, 0.5); },
    j: function () { julia ? toMandelbrot() : toJulia(); }, r: reset
  });
  canvas.addEventListener('focus', function () { if (!job) paint(); });
  canvas.addEventListener('blur', function () { if (!job) paint(); });

  k.loop(function (dt) { offset = (offset + dt * 90) % SIZE; if (!job) paint(); }, paint);
  k.onPlay = function () { k.status('The colours cycle - Pause holds them'); };
  k.onPause = function () { if (!job) done(); };

  k.row();
  k.playButton().title = 'Cycle the colours';
  k.button('Zoom in', function () { zoomAt(W / 2, H / 2, 2); }, 'Zoom in at the middle (+)');
  k.button('Zoom out', function () { zoomAt(W / 2, H / 2, 0.5); }, 'Zoom out (-)');
  var juliaBtn = k.button(julia ? 'Mandelbrot' : 'Julia', function () { julia ? toMandelbrot() : toJulia(); }, 'The Julia set of the point in the middle, and back (J)');
  k.button('Reset', reset, 'The first view (R)');
  k.row();
  k.select('Palette', [['ocean', 'Ocean'], ['fire', 'Fire'], ['rainbow', 'Rainbow'], ['mono', 'Ink']], palette, function (v) { palette = v; makeLut(); paint(); });
  var levels = [50, 100, 200, 300, 500, 1000, 2000, 5000], at = 0;
  levels.forEach(function (v, i) { if (Math.abs(v - maxIter) < Math.abs(levels[at] - maxIter)) at = i; });
  k.slider('Iterations', 0, levels.length - 1, 1, at, function (v) { maxIter = levels[v]; render(); }, function (v) { return levels[v]; });
  k.theme(function () { makeLut(); });

  makeLut();
  render();
  k.begin();
  if (!k.playing) done();
});
