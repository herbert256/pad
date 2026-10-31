PadToys.register('orbits', function (k, cfg) {
  var W = 640, H = 440, G = 1, SOFT = 9, MAXB = 40, COLOURS = ['accent', 'good', 'accent2', 'accent3', 'bad', 'mark'];
  var system = cfg.system, trailLen = cfg.trail, speed = cfg.speed, bodies = [], time = 0, merges = 0, said = 0, aim = null;
  var ctx = k.canvas(W, H, 'Bodies in orbit', false), canvas = ctx.canvas;
  canvas.style.background = 'var(--pad-toy-night)';
  var sky = [];
  for (var s = 0; s < 120; s++) sky.push([k.range(0, W), k.range(0, H), k.range(0.2, 0.8)]);

  function body(x, y, vx, vy, m, colour) {
    bodies.push({ x: x, y: y, vx: vx, vy: vy, m: m, colour: colour || COLOURS[bodies.length % COLOURS.length], trail: [] });
  }
  function radius(b) { return Math.max(2, Math.cbrt(b.m) * 0.9); }
  // A body on a circle round another: the speed that holds it there, at right angles.
  function around(c, r, a, m, colour) {
    var v = Math.sqrt(G * c.m / r);
    body(c.x + Math.cos(a) * r, c.y + Math.sin(a) * r, c.vx - Math.sin(a) * v, c.vy + Math.cos(a) * v, m, colour);
    return bodies[bodies.length - 1];
  }

  function arrange(name) {
    bodies = []; time = 0; merges = 0;
    if (name === 'solar') {
      body(0, 0, 0, 0, 6000, 'mark');
      around(bodies[0], 62, k.range(0, 6.3), 2, 'muted');
      around(bodies[0], 104, k.range(0, 6.3), 6, 'accent2');
      // The moon stays well inside the reach of its planet's pull, half its Hill radius, or
      // the sun would take it away.
      var earth = around(bodies[0], 152, k.range(0, 6.3), 30, 'accent');
      around(earth, 9, 0, 0.05, 'glow');
      around(bodies[0], 205, k.range(0, 6.3), 5, 'bad');
    } else if (name === 'binary') {
      var m = 2600, d = 110, v = Math.sqrt(G * m / (2 * d));
      body(-d / 2, 0, 0, -v, m, 'mark');
      body(d / 2, 0, 0, v, m, 'accent2');
      around({ x: 0, y: 0, vx: 0, vy: 0, m: 2 * m }, 205, 0.5, 4, 'accent');
    } else if (name === 'eight') {
      // Chenciner and Montgomery's figure eight: three equal bodies on one path, scaled.
      var L = 160, M = 1600, f = Math.sqrt(G * M / L);
      body(0.97000436 * L, -0.24308753 * L, 0.46620369 * f, 0.43236573 * f, M, 'accent');
      body(-0.97000436 * L, 0.24308753 * L, 0.46620369 * f, 0.43236573 * f, M, 'accent2');
      body(0, 0, -0.93240737 * f, -0.86473146 * f, M, 'good');
    } else {
      for (var i = 0; i < 30; i++) {
        var r = Math.sqrt(k.rand()) * 170, a = k.range(0, Math.PI * 2), w = 0.018;
        body(Math.cos(a) * r, Math.sin(a) * r * 0.8, -Math.sin(a) * r * w, Math.cos(a) * r * w * 0.8, k.range(20, 90));
      }
    }
  }

  function accel() {
    bodies.forEach(function (b) { b.ax = 0; b.ay = 0; });
    for (var i = 0; i < bodies.length; i++) for (var j = i + 1; j < bodies.length; j++) {
      var a = bodies[i], b = bodies[j], dx = b.x - a.x, dy = b.y - a.y, d2 = dx * dx + dy * dy + SOFT, inv = G / (d2 * Math.sqrt(d2));
      a.ax += dx * inv * b.m; a.ay += dy * inv * b.m;
      b.ax -= dx * inv * a.m; b.ay -= dy * inv * a.m;
    }
  }
  // Leapfrog steps keep the orbits closed for a long time; bodies that touch become one,
  // their momentum kept.
  function step(h) {
    bodies.forEach(function (b) { b.vx += b.ax * h / 2; b.vy += b.ay * h / 2; b.x += b.vx * h; b.y += b.vy * h; });
    accel();
    bodies.forEach(function (b) { b.vx += b.ax * h / 2; b.vy += b.ay * h / 2; });
    for (var i = 0; i < bodies.length; i++) for (var j = bodies.length - 1; j > i; j--) {
      var a = bodies[i], b = bodies[j];
      if (Math.hypot(a.x - b.x, a.y - b.y) < (radius(a) + radius(b)) * 0.8) {
        var m = a.m + b.m, big = a.m >= b.m ? a : b;
        a.vx = (a.vx * a.m + b.vx * b.m) / m; a.vy = (a.vy * a.m + b.vy * b.m) / m;
        a.x = (a.x * a.m + b.x * b.m) / m; a.y = (a.y * a.m + b.y * b.m) / m;
        a.colour = big.colour; a.m = m;
        bodies.splice(j, 1);
        merges++;
        accel();
      }
    }
    bodies = bodies.filter(function (b) { return Math.hypot(b.x, b.y) < 4000; });
  }

  function draw() {
    ctx.fillStyle = k.color('night');
    ctx.fillRect(0, 0, W, H);
    sky.forEach(function (st) { ctx.fillStyle = k.alpha('glow', st[2] * 0.5); ctx.fillRect(st[0], st[1], 1, 1); });
    ctx.save();
    ctx.translate(W / 2, H / 2);
    ctx.lineWidth = 1.2;
    bodies.forEach(function (b) {
      var t = b.trail;
      for (var c = 0, parts = 6, per = Math.ceil(t.length / parts); c < parts; c++) {
        var from = c * per, to = Math.min(t.length - 1, (c + 1) * per);
        if (to - from < 1) continue;
        ctx.strokeStyle = k.alpha(b.colour, ((c + 1) / parts * 0.6).toFixed(2));
        ctx.beginPath(); ctx.moveTo(t[from][0], t[from][1]);
        for (var i = from + 1; i <= to; i++) ctx.lineTo(t[i][0], t[i][1]);
        ctx.stroke();
      }
    });
    bodies.forEach(function (b) {
      var r = radius(b);
      if (b.m > 1000) { ctx.fillStyle = k.alpha(b.colour, 0.18); ctx.beginPath(); ctx.arc(b.x, b.y, r * 2.2, 0, Math.PI * 2); ctx.fill(); }
      ctx.fillStyle = k.color(b.colour);
      ctx.beginPath(); ctx.arc(b.x, b.y, r, 0, Math.PI * 2); ctx.fill();
    });
    if (aim) {
      ctx.strokeStyle = k.color('glow');
      ctx.setLineDash([4, 4]);
      ctx.beginPath(); ctx.moveTo(aim.x0, aim.y0); ctx.lineTo(aim.x1, aim.y1); ctx.stroke();
      ctx.setLineDash([]);
      ctx.fillStyle = k.color('glow');
      ctx.beginPath(); ctx.arc(aim.x0, aim.y0, 3, 0, Math.PI * 2); ctx.fill();
    }
    ctx.restore();
    k.info(bodies.length + (bodies.length === 1 ? ' body' : ' bodies') + (merges ? ' · ' + merges + (merges === 1 ? ' merger' : ' mergers') : '') + ' · time ' + time.toFixed(0));
    var now = Date.now();
    if (now - said > 1500) {
      said = now;
      k.describe(canvas, bodies.length + (bodies.length === 1 ? ' body' : ' bodies') + ' moving under gravity, drawing their paths' + (merges ? ', ' + merges + ' merged' : ''));
    }
  }

  k.loop(function (dt) {
    var h = 0.16 * speed / 5, n = 8;
    for (var i = 0; i < n; i++) step(h / n * dt * 60);
    time += dt * speed / 5 * 10;
    if (trailLen) bodies.forEach(function (b) { b.trail.push([b.x, b.y]); if (b.trail.length > trailLen) b.trail.splice(0, b.trail.length - trailLen); });
    draw();
  }, draw);

  // Throwing a planet: where the drag starts it is put, the way and length of the drag are
  // its speed.
  k.pointer(canvas, {
    down: function (p) { aim = { x0: p.x - W / 2, y0: p.y - H / 2, x1: p.x - W / 2, y1: p.y - H / 2 }; if (!k.playing) draw(); },
    move: function (p, e, pressed) { if (pressed && aim) { aim.x1 = p.x - W / 2; aim.y1 = p.y - H / 2; if (!k.playing) draw(); } },
    up: function () {
      if (!aim) return;
      if (bodies.length >= MAXB) bodies.shift();
      body(aim.x0, aim.y0, (aim.x1 - aim.x0) * 0.04, (aim.y1 - aim.y0) * 0.04, 6);
      aim = null;
      k.status('A planet thrown in - ' + bodies.length + ' bodies');
      accel();
      if (!k.playing) k.play();
    }
  });

  function reset() { arrange(system); accel(); draw(); }
  k.keys({ r: reset, c: function () { bodies.forEach(function (b) { b.trail = []; }); draw(); } });
  k.onPlay = function () { k.status('Orbiting - drag across the sky to throw in a planet'); };
  k.onPause = function () { k.status('Time stands still'); };

  k.row();
  k.playButton();
  k.button('Reset', function () { reset(); k.status('Back to the start'); }, 'Start again (R)');
  k.button('Clear trails', function () { bodies.forEach(function (b) { b.trail = []; }); draw(); }, 'Wipe the paths (C)');
  k.select('System', [['solar', 'Sun and planets'], ['binary', 'Two stars'], ['eight', 'Figure eight'], ['swarm', 'Swarm']], system, function (v) { system = v; reset(); k.status({ solar: 'A sun and its planets', binary: 'Two stars and a planet', eight: 'Three bodies on one figure eight', swarm: 'A swarm falling together' }[v]); });
  k.row();
  k.slider('Trail', 0, 2000, 50, trailLen, function (v) { trailLen = v; bodies.forEach(function (b) { if (b.trail.length > v) b.trail.splice(0, b.trail.length - v); }); });
  k.slider('Speed', 1, 10, 1, speed, function (v) { speed = v; });
  k.theme(draw);

  reset();
  k.begin();
});
