PadToys.register('pendulum', function (k, cfg) {
  var W = 600, H = 440, OX = W / 2, OY = 210, SCALE = 100, L1 = 1, L2 = 1;
  var g = cfg.gravity, count = cfg.pendulums, trailLen = cfg.trail, start = [cfg.angles[0] * Math.PI / 180, cfg.angles[1] * Math.PI / 180];
  var COLOURS = ['accent', 'accent2', 'accent3', 'good', 'mark', 'bad', 'ink', 'muted'], OFFSET = 0.01 * Math.PI / 180;
  var ps = [], time = 0, said = 0, grab = null;
  var ctx = k.canvas(W, H, 'A double pendulum', false), canvas = ctx.canvas;

  // The state of one pendulum is [θ1, θ2, ω1, ω2]: both arms a metre, both bobs a kilogram.
  function deriv(s) {
    var t1 = s[0], t2 = s[1], w1 = s[2], w2 = s[3], d = t1 - t2, den = 3 - Math.cos(2 * d);
    return [w1, w2,
      (-3 * g * Math.sin(t1) - g * Math.sin(t1 - 2 * t2) - 2 * Math.sin(d) * (w2 * w2 * L2 + w1 * w1 * L1 * Math.cos(d))) / (L1 * den),
      (2 * Math.sin(d) * (2 * w1 * w1 * L1 + 2 * g * Math.cos(t1) + w2 * w2 * L2 * Math.cos(d))) / (L2 * den)];
  }
  // A fourth-order Runge-Kutta step, small enough that the energy holds for minutes.
  function rk4(s, h) {
    var a = deriv(s), b = deriv(add(s, a, h / 2)), c = deriv(add(s, b, h / 2)), d = deriv(add(s, c, h));
    return [0, 1, 2, 3].map(function (i) { return s[i] + h / 6 * (a[i] + 2 * b[i] + 2 * c[i] + d[i]); });
  }
  function add(s, d, h) { return [s[0] + d[0] * h, s[1] + d[1] * h, s[2] + d[2] * h, s[3] + d[3] * h]; }
  function energy(s) {
    var t1 = s[0], t2 = s[1], w1 = s[2], w2 = s[3];
    var kin = 0.5 * w1 * w1 * L1 * L1 + 0.5 * (w1 * w1 * L1 * L1 + w2 * w2 * L2 * L2 + 2 * w1 * w2 * L1 * L2 * Math.cos(t1 - t2));
    return kin - 2 * g * L1 * Math.cos(t1) - g * L2 * Math.cos(t2);
  }
  function bobs(s) {
    var x1 = OX + SCALE * L1 * Math.sin(s[0]), y1 = OY + SCALE * L1 * Math.cos(s[0]);
    return [x1, y1, x1 + SCALE * L2 * Math.sin(s[1]), y1 + SCALE * L2 * Math.cos(s[1])];
  }

  // Every pendulum after the first starts a hundredth of a degree further out than the one
  // before: for a few seconds they swing as one, then each goes its own way.
  function reset(angles) {
    ps = [];
    time = 0;
    for (var i = 0; i < count; i++) {
      var s = [angles[0] + i * OFFSET, angles[1] + i * OFFSET, 0, 0];
      ps.push({ s: s, trail: [], e0: energy(s), colour: COLOURS[i % COLOURS.length] });
    }
  }

  function draw() {
    ctx.fillStyle = k.color('canvas');
    ctx.fillRect(0, 0, W, H);
    ctx.lineCap = 'round';
    ctx.lineJoin = 'round';
    // The trail fades from the bob back: drawn in ten stretches, the oldest faintest. The
    // first pendulum is drawn last, on top: while the copies still swing as one, it is the one
    // seen.
    var order = ps.slice().reverse();
    order.forEach(function (p) {
      var t = p.trail, parts = 10, per = Math.ceil(t.length / parts);
      ctx.lineWidth = 1.6;
      for (var c = 0; c < parts; c++) {
        var from = c * per, to = Math.min(t.length - 1, (c + 1) * per);
        if (to - from < 1) continue;
        ctx.strokeStyle = k.alpha(p.colour, ((c + 1) / parts * 0.75).toFixed(3));
        ctx.beginPath();
        ctx.moveTo(t[from][0], t[from][1]);
        for (var i = from + 1; i <= to; i++) ctx.lineTo(t[i][0], t[i][1]);
        ctx.stroke();
      }
    });
    order.forEach(function (p) {
      var b = bobs(p.s), n = ps.indexOf(p);
      ctx.strokeStyle = n === 0 ? k.color('ink') : k.alpha('ink', 0.35);
      ctx.lineWidth = 2.5;
      ctx.beginPath();
      ctx.moveTo(OX, OY); ctx.lineTo(b[0], b[1]); ctx.lineTo(b[2], b[3]);
      ctx.stroke();
      ctx.fillStyle = k.color(p.colour);
      ctx.beginPath(); ctx.arc(b[0], b[1], 10, 0, Math.PI * 2); ctx.fill();
      ctx.beginPath(); ctx.arc(b[2], b[3], 10, 0, Math.PI * 2); ctx.fill();
    });
    ctx.fillStyle = k.color('ink');
    ctx.beginPath(); ctx.arc(OX, OY, 4, 0, Math.PI * 2); ctx.fill();
    var kept = ps.length ? energy(ps[0].s) / ps[0].e0 : 1;
    k.info('Time ' + time.toFixed(1) + ' s · ' + count + (count === 1 ? ' pendulum' : ' pendulums') + ' · gravity ' + g + ' m/s²' + (isFinite(kept) && ps[0].e0 ? ' · energy kept ' + (100 * Math.min(kept, 2 - kept)).toFixed(2) + '%' : ''));
    var now = Date.now();
    if (now - said > 1000) {
      said = now;
      var deg = function (r) { var d = ((r * 180 / Math.PI) % 360 + 540) % 360 - 180; return Math.round(d); };
      k.describe(canvas, 'A double pendulum: the upper arm at ' + deg(ps[0].s[0]) + ' degrees, the lower at ' + deg(ps[0].s[1]) + ' degrees from straight down' + (count > 1 ? ', with ' + (count - 1) + ' more started a little apart' : ''));
    }
  }

  k.loop(function (dt) {
    if (grab) { draw(); return; }
    var steps = Math.max(1, Math.round(dt * 480)), h = dt / steps;
    ps.forEach(function (p) {
      for (var i = 0; i < steps; i++) p.s = rk4(p.s, h);
      if (trailLen) {
        var b = bobs(p.s);
        p.trail.push([b[2], b[3]]);
        if (p.trail.length > trailLen) p.trail.splice(0, p.trail.length - trailLen);
      }
    });
    time += dt;
    draw();
  }, draw);

  // Dragging a bob holds the pendulum there - every copy with it - and lets go with no speed.
  k.pointer(canvas, {
    down: function (p) {
      var b = bobs(ps[0].s), d1 = Math.hypot(p.x - b[0], p.y - b[1]), d2 = Math.hypot(p.x - b[2], p.y - b[3]);
      if (Math.min(d1, d2) > 30) return false;
      grab = d2 <= d1 ? 2 : 1;
      canvas.style.cursor = 'grabbing';
      return true;
    },
    move: function (p, e, pressed) {
      if (!grab) {
        var b = bobs(ps[0].s);
        canvas.style.cursor = Math.min(Math.hypot(p.x - b[0], p.y - b[1]), Math.hypot(p.x - b[2], p.y - b[3])) < 30 ? 'grab' : '';
        return;
      }
      var s = ps[0].s, a1 = s[0], a2 = s[1];
      if (grab === 1) a1 = Math.atan2(p.x - OX, p.y - OY);
      else { var b1 = bobs(s); a2 = Math.atan2(p.x - b1[0], p.y - b1[1]); }
      reset([a1, a2]);
      if (!k.playing || k.held) draw();
    },
    up: function () {
      if (!grab) return;
      grab = null;
      canvas.style.cursor = '';
      start = [ps[0].s[0], ps[0].s[1]];
      k.status('Let go - swinging from where you held it');
      if (!k.playing) k.play();
    }
  });

  function randomStart() { start = [k.range(-Math.PI, Math.PI), k.range(-Math.PI, Math.PI)]; reset(start); draw(); k.status('A new start'); }
  k.keys({ r: randomStart, c: function () { ps.forEach(function (p) { p.trail = []; }); draw(); } });
  k.onPlay = function () { k.status('Swinging - drag a bob to hold it'); };
  k.onPause = function () { k.status('Paused at ' + time.toFixed(1) + ' seconds'); };

  k.row();
  k.playButton();
  k.button('Reset', function () { reset(start); draw(); k.status('Back at the start'); }, 'The same start again');
  k.button('Random', randomStart, 'Start from random angles (R)');
  k.button('Clear trail', function () { ps.forEach(function (p) { p.trail = []; }); draw(); }, 'Clear the trail (C)');
  k.row();
  k.slider('Pendulums', 1, 8, 1, count, function (v) { count = v; reset([ps[0].s[0], ps[0].s[1]]); draw(); });
  k.slider('Gravity', 1, 30, 0.1, g, function (v) { g = v; ps.forEach(function (p) { p.e0 = energy(p.s); }); }, function (v) { return v.toFixed(1); });
  k.slider('Trail', 0, 3000, 50, trailLen, function (v) { trailLen = v; }, function (v) { return v; });
  k.theme(draw);

  reset(start);
  k.begin();
});
