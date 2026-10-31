PadToys.register('galton', function (k, cfg) {
  var W = 520, H = 540, TOP = 46, HOP = 0.075;
  var levels = cfg.levels, total = cfg.balls, bias = cfg.bias, rate = 30, wait = 0, dropped = 0, queued = 0, flying = [], bins = [], said = 0;
  var ctx = k.canvas(W, H, 'A Galton board', false), canvas = ctx.canvas;
  var dx, dy, binTop, r;

  function layout() {
    dx = (W - 40) / (levels + 1);
    dy = Math.min(dx * 0.85, (H * 0.5 - TOP) / levels);
    binTop = TOP + levels * dy + 18;
    r = Math.max(1.5, Math.min(5, dx * 0.16));
    bins = [];
    for (var i = 0; i <= levels; i++) bins.push(0);
  }
  function peg(i, j) { return { x: W / 2 + (j - i / 2) * dx, y: TOP + i * dy }; }

  // A ball hops from peg to peg, one row a step: at each it goes right by the bias, else left,
  // and the number of times it went right is its bin.
  function drop() {
    flying.push({ row: 0, right: 0, t: 0, go: k.chance(bias / 100) });
    dropped++;
  }

  function step(dt) {
    if (queued > 0 || (k.playing && dropped < total)) {
      wait -= dt;
      while (wait <= 0 && (queued > 0 || dropped < total)) {
        drop();
        if (queued > 0) queued--;
        wait += 1 / (queued > 0 ? 400 : rate);
      }
    }
    for (var i = flying.length - 1; i >= 0; i--) {
      var b = flying[i];
      b.t += dt / HOP;
      // Hop 0 brings the ball from the funnel onto the first peg; every hop after it leaves a
      // peg the way chosen there - levels of them, the last one into a bin.
      while (b.t >= 1) {
        b.t -= 1;
        if (b.row >= 1) { if (b.go) b.right++; b.go = k.chance(bias / 100); }
        b.row++;
        if (b.row > levels) { bins[b.right]++; flying.splice(i, 1); break; }
      }
    }
    if (dropped >= total && !flying.length && k.playing && !queued) finished();
  }
  function finished() {
    k.pause();
    k.status('All ' + total + ' balls dropped - mean bin ' + mean().toFixed(2) + ', expected ' + (levels * bias / 100).toFixed(2));
    if (k.playBtn) k.playBtn.textContent = 'Again';
  }
  function landed() { return bins.reduce(function (a, b) { return a + b; }, 0); }
  function mean() { var n = landed(), s = 0; bins.forEach(function (c, i) { s += c * i; }); return n ? s / n : 0; }

  function draw() {
    ctx.fillStyle = k.color('canvas');
    ctx.fillRect(0, 0, W, H);
    // The pegs, and a funnel over the first.
    ctx.fillStyle = k.color('muted');
    for (var i = 0; i < levels; i++) for (var j = 0; j <= i; j++) { var p = peg(i, j); ctx.beginPath(); ctx.arc(p.x, p.y, Math.max(1.5, r * 0.6), 0, Math.PI * 2); ctx.fill(); }
    ctx.strokeStyle = k.color('line');
    ctx.lineWidth = 2;
    ctx.beginPath(); ctx.moveTo(W / 2 - 40, 6); ctx.lineTo(W / 2 - 8, TOP - 14); ctx.moveTo(W / 2 + 40, 6); ctx.lineTo(W / 2 + 8, TOP - 14); ctx.stroke();
    // The bins: a wall between each two, the balls in each piled as a bar, scaled down when
    // the tallest would leave the board.
    var floor = H - 8, room = floor - binTop, n = landed(), most = Math.max.apply(null, bins), unit = Math.min(r * 2, room / Math.max(1, most));
    var expected = bins.map(function (c, i) { return n * choose(levels, i) * Math.pow(bias / 100, i) * Math.pow(1 - bias / 100, levels - i); });
    unit = Math.min(unit, room / Math.max(1, Math.max.apply(null, expected) * 1.05));
    ctx.strokeStyle = k.color('grid');
    ctx.lineWidth = 1;
    for (i = 0; i <= levels + 1; i++) { var x = W / 2 + (i - (levels + 1) / 2) * dx; ctx.beginPath(); ctx.moveTo(x, binTop); ctx.lineTo(x, floor); ctx.stroke(); }
    ctx.fillStyle = k.color('accent');
    bins.forEach(function (c, i) { var x = W / 2 + (i - levels / 2) * dx, h = c * unit; ctx.fillRect(x - dx / 2 + 2, floor - h, dx - 4, h); });
    ctx.strokeStyle = k.color('line');
    ctx.beginPath(); ctx.moveTo(10, floor); ctx.lineTo(W - 10, floor); ctx.stroke();
    // The bell the balls should make, for as many as have landed.
    if (n) {
      ctx.strokeStyle = k.color('accent2');
      ctx.lineWidth = 2;
      ctx.beginPath();
      var mu = levels * bias / 100, sd = Math.sqrt(levels * bias / 100 * (1 - bias / 100));
      for (var s = 0; s <= 200; s++) {
        var kx = -0.5 + s / 200 * (levels + 1), y = n * Math.exp(-0.5 * Math.pow((kx - mu) / sd, 2)) / (sd * Math.sqrt(2 * Math.PI));
        var px = W / 2 + (kx - levels / 2) * dx, py = floor - y * unit;
        if (s) ctx.lineTo(px, Math.max(binTop, py)); else ctx.moveTo(px, Math.max(binTop, py));
      }
      ctx.stroke();
    }
    ctx.fillStyle = k.color('accent3');
    flying.forEach(function (b) {
      var a = b.row === 0 ? { x: W / 2, y: TOP - 18 } : peg(b.row - 1, b.right), to;
      if (b.row >= levels) to = { x: a.x + (b.go ? dx / 2 : -dx / 2), y: binTop };
      else to = peg(b.row, b.right + (b.go ? 1 : 0));
      if (b.row === 0) to = peg(0, 0);
      var x = a.x + (to.x - a.x) * b.t, y = a.y + (to.y - a.y) * b.t - Math.sin(b.t * Math.PI) * dy * 0.45 - r;
      ctx.beginPath(); ctx.arc(x, y, r, 0, Math.PI * 2); ctx.fill();
    });
    k.info('Dropped ' + dropped + ' of ' + total + ' · mean bin ' + mean().toFixed(2) + ' (expected ' + (levels * bias / 100).toFixed(2) + ') · going right ' + bias + '%');
    var now = Date.now();
    if (now - said > 1500) {
      said = now;
      k.describe(canvas, 'A Galton board of ' + levels + ' rows of pegs: ' + n + ' balls in ' + (levels + 1) + ' bins, the fullest ' + most + ', piling up in a bell curve');
    }
  }
  function choose(n, kk) { var c = 1; for (var i = 1; i <= kk; i++) c = c * (n - kk + i) / i; return c; }

  k.loop(function (dt) { step(dt); draw(); }, draw);

  function reset() { layout(); flying = []; dropped = 0; queued = 0; draw(); }
  function many(n) { queued += n; total = Math.max(total, dropped + queued + flying.length); totalSlider.set(Math.min(5000, total)); if (!k.playing) k.play(); }

  k.pointer(canvas, { down: function () { many(1); } });
  k.keys({ d: function () { many(1); }, h: function () { many(100); }, r: function () { reset(); k.status('An empty board'); } });
  k.onPlay = function () { if (dropped >= total && !flying.length && !queued) reset(); if (k.playBtn) k.playBtn.textContent = 'Pause'; k.status('Pouring - click the board to drop one more'); };
  k.onPause = function () { if (dropped < total || flying.length) k.status('Paused after ' + dropped + ' balls'); };

  k.row();
  k.playButton();
  k.button('Drop 1', function () { many(1); }, 'One ball (D)');
  k.button('Drop 100', function () { many(100); }, 'A hundred at once (H)');
  k.button('Empty', function () { reset(); k.status('An empty board'); }, 'Start again (R)');
  k.row();
  k.slider('Rows', 4, 20, 1, levels, function (v) { levels = v; reset(); });
  k.slider('Right', 5, 95, 1, bias, function (v) { bias = v; reset(); }, function (v) { return v + '%'; });
  k.slider('Pour', 1, 200, 1, rate, function (v) { rate = v; }, function (v) { return v + '/s'; });
  var totalSlider = k.slider('Balls', 10, 5000, 10, total, function (v) { total = v; });
  k.theme(draw);

  reset();
  k.begin();
});
