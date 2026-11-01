PadToys.register('epicycles', function (k, cfg) {
  var W = 600, H = 440, MARGIN = 70, CURVE = 1024;
  var N = cfg.points, count = cfg.circles, order = cfg.order, speed = cfg.speed, shape = cfg.shape, own = cfg.drawing || [];
  var NAMES = { yours: 'Your drawing', heart: 'Heart', star: 'Star', square: 'Square', trefoil: 'Trefoil', drawn: 'What you drew' };
  var SAYS = { yours: 'your drawing', heart: 'a heart', star: 'a star', square: 'a square', trefoil: 'a trefoil knot', drawn: 'what you drew' };
  var ctx = k.canvas(W, H, 'Epicycles', false), canvas = ctx.canvas;
  var samples = [], terms = [], c0 = [0, 0], curve = new Float64Array(2 * CURVE), drawnPts = null, pen = null;
  var t = 0, turned = false, error = 0, rms = 1, showDrawing = true, said = 0, told = '';
  var COS = new Float64Array(N), SIN = new Float64Array(N);
  for (var m = 0; m < N; m++) { COS[m] = Math.cos(2 * Math.PI * m / N); SIN[m] = Math.sin(2 * Math.PI * m / N); }

  // The shapes, as closed paths of points: the heart curve x = 16 sin³t, y = 13 cos t -
  // 5 cos 2t - 2 cos 3t - cos 4t; a five-pointed star, its inner corners at 0.382 of the
  // outer; a square, whose corners no number of circles turns sharp; the trefoil knot seen
  // from above, x = sin t + 2 sin 2t, y = cos t - 2 cos 2t. y counts down, as on the canvas.
  function shapePoints(name) {
    var pts = [], i, a;
    if (name === 'yours') return own.slice();
    if (name === 'drawn') return drawnPts.slice();
    if (name === 'star') {
      for (i = 0; i < 10; i++) { a = -Math.PI / 2 + i * Math.PI / 5; var r = i % 2 ? 0.381966 : 1; pts.push([r * Math.cos(a), r * Math.sin(a)]); }
      return pts;
    }
    if (name === 'square') return [[-1, -1], [1, -1], [1, 1], [-1, 1]];
    for (i = 0; i < 720; i++) {
      a = 2 * Math.PI * i / 720;
      if (name === 'trefoil') pts.push([Math.sin(a) + 2 * Math.sin(2 * a), -(Math.cos(a) - 2 * Math.cos(2 * a))]);
      else pts.push([16 * Math.pow(Math.sin(a), 3), -(13 * Math.cos(a) - 5 * Math.cos(2 * a) - 2 * Math.cos(3 * a) - Math.cos(4 * a))]);
    }
    return pts;
  }

  // The path scaled to fit the canvas, room left round it for the circles, and walked at an
  // even pace: N samples, equally far apart along the closed path.
  function resample(pts) {
    var x0 = Infinity, y0 = Infinity, x1 = -Infinity, y1 = -Infinity, i;
    pts.forEach(function (p) { x0 = Math.min(x0, p[0]); y0 = Math.min(y0, p[1]); x1 = Math.max(x1, p[0]); y1 = Math.max(y1, p[1]); });
    var s = Math.min((W - 2 * MARGIN) / Math.max(x1 - x0, 1e-9), (H - 2 * MARGIN) / Math.max(y1 - y0, 1e-9));
    var fit = pts.map(function (p) { return [W / 2 + (p[0] - (x0 + x1) / 2) * s, H / 2 + (p[1] - (y0 + y1) / 2) * s]; });
    var lens = [], total = 0;
    for (i = 0; i < fit.length; i++) {
      var q = fit[(i + 1) % fit.length], d = Math.hypot(q[0] - fit[i][0], q[1] - fit[i][1]);
      lens.push(d);
      total += d;
    }
    if (!(total > 0)) return null;
    var out = [], seg = 0, before = 0;
    for (var j = 0; j < N; j++) {
      var at = j * total / N;
      while (seg < fit.length - 1 && before + lens[seg] < at) before += lens[seg++];
      var f = lens[seg] ? (at - before) / lens[seg] : 0, a = fit[seg], b = fit[(seg + 1) % fit.length];
      out.push([a[0] + (b[0] - a[0]) * f, a[1] + (b[1] - a[1]) * f]);
    }
    return out;
  }

  // The discrete Fourier transform of the samples, z = x + iy: c(f) = 1/N Σ z(n) e^(-2πi f n / N).
  // c(0) is the middle the circles hang from; every other f - one turn, minus one, two ... -
  // is a circle of radius |c(f)| that turns f times while the pen goes round once.
  function transform() {
    terms = [];
    for (var kk = 0; kk < N; kk++) {
      var re = 0, im = 0;
      for (var n = 0; n < N; n++) {
        var m = (kk * n) % N, x = samples[n][0], y = samples[n][1];
        re += x * COS[m] + y * SIN[m];
        im += y * COS[m] - x * SIN[m];
      }
      re /= N;
      im /= N;
      if (kk === 0) { c0 = [re, im]; continue; }
      terms.push({ f: kk <= N / 2 ? kk : kk - N, k: kk, re: re, im: im, r: Math.hypot(re, im) });
    }
    rms = 0;
    samples.forEach(function (p) { rms += (p[0] - c0[0]) * (p[0] - c0[0]) + (p[1] - c0[1]) * (p[1] - c0[1]); });
    rms = Math.sqrt(rms / N) || 1;
    arrange();
  }

  // The order the circles hang in: the largest first, or by how fast they turn - 1, -1, 2, -2.
  function arrange() {
    terms.sort(order === 'size'
      ? function (a, b) { return b.r - a.r || Math.abs(a.f) - Math.abs(b.f) || b.f - a.f; }
      : function (a, b) { return Math.abs(a.f) - Math.abs(b.f) || b.f - a.f; });
    measure();
  }

  // What the first count circles draw: the curve the pen goes round, and how far it is from
  // the samples - the root mean square distance at the samples' own places, as a share of
  // the drawing's root mean square distance from its middle.
  function measure() {
    var n, j, sum = 0;
    for (n = 0; n < N; n++) {
      var x = c0[0], y = c0[1];
      for (j = 0; j < count; j++) {
        var c = terms[j], m = ((c.k * n) % N + N) % N;
        x += c.re * COS[m] - c.im * SIN[m];
        y += c.re * SIN[m] + c.im * COS[m];
      }
      sum += (x - samples[n][0]) * (x - samples[n][0]) + (y - samples[n][1]) * (y - samples[n][1]);
    }
    error = Math.sqrt(sum / N) / rms;
    for (n = 0; n < CURVE; n++) {
      var p = at(n / CURVE, null);
      curve[2 * n] = p[0];
      curve[2 * n + 1] = p[1];
    }
  }
  // The pen at t, from 0 to 1 round; each(cx, cy, r, x, y) is told every circle on the way.
  function at(tt, each) {
    var x = c0[0], y = c0[1];
    for (var j = 0; j < count; j++) {
      var c = terms[j], a = 2 * Math.PI * c.f * tt, cs = Math.cos(a), sn = Math.sin(a), nx = x + c.re * cs - c.im * sn, ny = y + c.re * sn + c.im * cs;
      if (each) each(x, y, c.r, nx, ny);
      x = nx;
      y = ny;
    }
    return [x, y];
  }

  function percent() { return error < 0.0005 ? 'none' : (100 * error).toFixed(error < 0.1 ? 2 : 1) + '%'; }
  function draw() {
    ctx.fillStyle = k.color('canvas');
    ctx.fillRect(0, 0, W, H);
    ctx.lineJoin = 'round';
    ctx.lineCap = 'round';
    if (pen) {
      ctx.strokeStyle = k.color('ink');
      ctx.lineWidth = 2;
      ctx.beginPath();
      pen.forEach(function (p, i) { if (i) ctx.lineTo(p.x, p.y); else ctx.moveTo(p.x, p.y); });
      ctx.stroke();
      return;
    }
    if (showDrawing) {
      ctx.strokeStyle = k.color('line');
      ctx.lineWidth = 5;
      ctx.beginPath();
      samples.forEach(function (p, i) { if (i) ctx.lineTo(p[0], p[1]); else ctx.moveTo(p[0], p[1]); });
      ctx.closePath();
      ctx.stroke();
    }
    var upto = turned ? CURVE : Math.floor(t * CURVE);
    ctx.strokeStyle = k.color('accent');
    ctx.lineWidth = 2;
    ctx.beginPath();
    for (var n = 0; n <= upto && n <= CURVE; n++) {
      var i = n % CURVE;
      if (n) ctx.lineTo(curve[2 * i], curve[2 * i + 1]); else ctx.moveTo(curve[0], curve[1]);
    }
    if (!turned) { var tip0 = at(t, null); ctx.lineTo(tip0[0], tip0[1]); }
    ctx.stroke();
    var ring = k.alpha('muted', 0.45), arm = k.alpha('ink', 0.55);
    ctx.lineWidth = 1;
    var tip = at(t, function (cx, cy, r, nx, ny) {
      if (r < 0.4) return;
      ctx.strokeStyle = ring;
      ctx.beginPath(); ctx.arc(cx, cy, r, 0, Math.PI * 2); ctx.stroke();
      ctx.strokeStyle = arm;
      ctx.beginPath(); ctx.moveTo(cx, cy); ctx.lineTo(nx, ny); ctx.stroke();
    });
    ctx.fillStyle = k.color('ink');
    ctx.beginPath(); ctx.arc(c0[0], c0[1], 2.5, 0, Math.PI * 2); ctx.fill();
    ctx.fillStyle = k.color('accent2');
    ctx.beginPath(); ctx.arc(tip[0], tip[1], 4, 0, Math.PI * 2); ctx.fill();
    k.info(count + ' of ' + (N - 1) + ' circles · ' + (order === 'size' ? 'largest first' : 'slowest first') + ' · ' + N + ' samples · off by ' + percent());
    var now = Date.now(), what = count + shape + order;
    if (now - said > 1000 || what !== told || !k.playing || k.held) {
      said = now;
      told = what;
      k.describe(canvas, 'Epicycles: ' + count + (count === 1 ? ' circle redraws ' : ' circles turning on one another redraw ') + SAYS[shape] + ', off by ' + percent());
    }
  }

  // The pen goes round once in 40 / speed seconds; after the first time round the curve
  // stays drawn whole.
  k.loop(function (dt) {
    t += dt * speed / 40;
    if (t >= 1) { t -= Math.floor(t); turned = true; }
    draw();
  }, draw);
  k.still(function () { t = 0; turned = true; draw(); });

  function use(name, quiet) {
    var pts = resample(shapePoints(name));
    if (!pts) { k.status('That drawing has no length to take apart'); return false; }
    shape = name;
    samples = pts;
    transform();
    t = 0;
    turned = !k.playing;
    if (!quiet) k.status((NAMES[name] || name) + ': ' + count + ' circles, off by ' + percent());
    draw();
    return true;
  }
  function setCount(n) {
    count = Math.max(1, Math.min(N - 1, n));
    circlesIn.set(count);
    measure();
    draw();
  }

  // Drawing a closed curve of your own: press, draw, let go - it is closed and taken apart.
  k.pointer(canvas, {
    down: function (p) { pen = [p]; draw(); k.status('Drawing - let go to take it apart'); },
    move: function (p, e, pressed) {
      if (!pressed || !pen) return;
      var last = pen[pen.length - 1];
      if (Math.hypot(p.x - last.x, p.y - last.y) >= 2) { pen.push(p); draw(); }
    },
    up: function () {
      var drawn = pen;
      pen = null;
      if (!drawn || drawn.length < 3) { draw(); k.status('Too short - draw a closed curve, pressed all the way'); return; }
      drawnPts = drawn.map(function (p) { return [p.x, p.y]; });
      if (!shapeIn.querySelector('option[value="drawn"]')) { var o = k.el('option', '', NAMES.drawn); o.value = 'drawn'; shapeIn.appendChild(o); }
      if (use('drawn')) shapeIn.value = 'drawn';
    }
  });

  k.keys({
    ArrowUp: function () { setCount(count + 1); }, ArrowRight: function () { setCount(count + 1); }, '+': function () { setCount(count + 1); },
    ArrowDown: function () { setCount(count - 1); }, ArrowLeft: function () { setCount(count - 1); }, '-': function () { setCount(count - 1); },
    PageUp: function () { setCount(count + 10); }, PageDown: function () { setCount(count - 10); },
    o: function () { orderIn.value = order = order === 'size' ? 'frequency' : 'size'; arrange(); draw(); k.status(order === 'size' ? 'The largest circles first' : 'The slowest circles first'); },
    d: function () { drawingBox.checked = showDrawing = !showDrawing; draw(); }
  });

  k.onPlay = function () { if (turned && t === 0) turned = false; k.status('Turning - draw a closed curve to take it apart'); };
  k.onPause = function () { k.status('Paused - ' + count + ' circles, off by ' + percent()); };

  k.row();
  k.playButton();
  k.button('Again', function () { t = 0; turned = false; draw(); k.status('From the start again'); }, 'The pen from the start again');
  var shapes = (own.length ? ['yours'] : []).concat(['heart', 'star', 'square', 'trefoil']);
  var shapeIn = k.select('Drawing', shapes.map(function (n) { return [n, NAMES[n]]; }), shape, function (v) { if (!use(v)) shapeIn.value = shape; }).querySelector('select');
  var drawingBox = k.check('Show the drawing', showDrawing, function (v) { showDrawing = v; draw(); });
  k.row();
  var circlesIn = k.slider('Circles', 1, N - 1, 1, count, function (v) { count = v; measure(); draw(); }, function (v) { return v + ' of ' + (N - 1); });
  var orderIn = k.select('Order', [['size', 'Largest first'], ['frequency', 'Slowest first']], order, function (v) { order = v; arrange(); draw(); }).querySelector('select');
  k.slider('Speed', 1, 10, 1, speed, function (v) { speed = v; });
  k.theme(draw);

  // A drawing of no length - every point on one place - cannot be taken apart: a shape of
  // the toy's own is shown, where the circles would be none and the first frame would throw.
  if (!use(shape, true)) { use('heart', true); shapeIn.value = 'heart'; }
  k.begin('Turning - draw a closed curve to take it apart');
});
