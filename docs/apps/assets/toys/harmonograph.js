PadToys.register('harmonograph', function (k, cfg) {
  var S = 480, R = S * 0.23, DT = 0.012, MAXT = 260, MAXP = Math.ceil(MAXT / DT) + 2;
  var f = cfg.frequencies.slice(), damping = cfg.damping, rotary = cfg.rotary, phase = [0, Math.PI / 2, 0, Math.PI / 2, 0];
  var xs = new Float32Array(MAXP), ys = new Float32Array(MAXP), n = 0, t = 0, pace = 14, said = 0;
  var ctx = k.canvas(S, S, 'A harmonograph drawing', false), canvas = ctx.canvas;

  // The pen at time t: two pendulums add up across, two up and down, each dying away; the
  // rotary table moves the paper round in a circle as well.
  function pen(t) {
    var e = Math.exp(-damping * 0.001 * t);
    var x = (Math.sin(f[0] * t + phase[0]) + Math.sin(f[1] * t + phase[1])) * e;
    var y = (Math.sin(f[2] * t + phase[2]) + Math.sin(f[3] * t + phase[3])) * e;
    if (rotary) { var r = 0.45 * Math.exp(-damping * 0.0007 * t); x = x * 0.8 + r * Math.sin(0.997 * f[0] * t + phase[4]); y = y * 0.8 + r * Math.cos(0.997 * f[0] * t + phase[4]); }
    return [S / 2 + x * R, S / 2 + y * R];
  }

  function restart() { n = 0; t = 0; }
  function grow(dt) {
    var end = Math.min(MAXT, t + dt * pace);
    while (t < end && n < MAXP) { var p = pen(t); xs[n] = p[0]; ys[n] = p[1]; n++; t += DT; }
    if (t >= MAXT) { k.pause(); k.status('Drawn - Random for another, or move a slider'); if (k.playBtn) k.playBtn.textContent = 'Again'; }
  }

  // The line goes from the first colour to the last as the pendulums die away, in stretches
  // of one colour each.
  function draw() {
    ctx.fillStyle = k.color('canvas');
    ctx.fillRect(0, 0, S, S);
    var stops = [k.rgb('accent'), k.rgb('accent3'), k.rgb('accent2')], parts = 24, per = Math.ceil(n / parts);
    ctx.lineWidth = 0.9;
    ctx.lineJoin = 'round';
    for (var c = 0; c < parts; c++) {
      var from = c * per, to = Math.min(n - 1, (c + 1) * per);
      if (to - from < 1) break;
      var g = (from / MAXP) * (stops.length - 1), i0 = Math.min(stops.length - 2, Math.floor(g)), u = g - i0, a = stops[i0], b = stops[i0 + 1];
      ctx.strokeStyle = 'rgba(' + Math.round(a[0] + (b[0] - a[0]) * u) + ',' + Math.round(a[1] + (b[1] - a[1]) * u) + ',' + Math.round(a[2] + (b[2] - a[2]) * u) + ',0.85)';
      ctx.beginPath();
      ctx.moveTo(xs[from], ys[from]);
      for (var i = from + 1; i <= to; i++) ctx.lineTo(xs[i], ys[i]);
      ctx.stroke();
    }
    if (n && t < MAXT) {
      ctx.fillStyle = k.color('ink');
      ctx.beginPath(); ctx.arc(xs[n - 1], ys[n - 1], 3, 0, Math.PI * 2); ctx.fill();
    }
    k.info('Across ' + f[0] + ' and ' + f[1] + ' · up and down ' + f[2] + ' and ' + f[3] + ' · damping ' + damping + (rotary ? ' · rotary' : '') + ' · ' + Math.round(t / MAXT * 100) + '% drawn');
    var now = Date.now();
    if (now - said > 1000) {
      said = now;
      k.describe(canvas, 'A harmonograph figure of the frequencies ' + f.join(', ') + ', ' + Math.round(t / MAXT * 100) + ' percent drawn');
    }
  }

  k.loop(function (dt) { grow(dt); draw(); }, draw);

  // A random figure: small whole ratios between the pendulums, one detuned a little - the
  // drift that turns a still lissajous into the winding figure.
  function random() {
    var base = [1, 2, 3, 4, 5, 6];
    var a = k.pick(base), b = k.pick(base);
    f = [a, k.pick(base), b, a];
    f[3] = +(f[3] + k.pick([-1, 1]) * k.range(0.002, 0.02)).toFixed(3);
    f[1] = +(f[1] + k.range(-0.006, 0.006)).toFixed(3);
    phase = [0, k.range(0, Math.PI * 2), k.range(0, Math.PI * 2), k.range(0, Math.PI * 2), k.range(0, Math.PI * 2)];
    sliders.forEach(function (s, i) { s.set(f[i]); });
    restart();
    draw();
    k.status('Frequencies ' + f.join(', '));
    if (!k.playing) k.play();
  }
  function changed() { restart(); if (!k.playing) k.play(); }

  k.keys({ r: random });
  k.onPlay = function () { if (t >= MAXT) restart(); if (k.playBtn) k.playBtn.textContent = 'Pause'; k.status('Drawing'); };
  k.onPause = function () { if (t < MAXT) k.status('Paused at ' + Math.round(t / MAXT * 100) + '%'); };

  k.row();
  k.playButton();
  k.button('Random', random, 'A new figure (R)');
  k.button('Draw at once', function () { while (t < MAXT) grow(1); draw(); }, 'The whole figure now');
  k.check('Rotary', rotary, function (v) { rotary = v; changed(); });
  k.slider('Speed', 2, 60, 1, pace, function (v) { pace = v; });
  k.row();
  var sliders = ['Across 1', 'Across 2', 'Up 1', 'Up 2'].map(function (label, i) {
    return k.slider(label, 0.5, 10, 0.001, f[i], function (v) { f[i] = v; changed(); }, function (v) { return v.toFixed(3); });
  });
  k.slider('Damping', 0, 20, 1, damping, function (v) { damping = v; changed(); });
  k.theme(draw);

  k.still(function () { while (t < MAXT && n < MAXP) { var p = pen(t); xs[n] = p[0]; ys[n] = p[1]; n++; t += DT; } draw(); });
  k.begin('Drawing');
});
