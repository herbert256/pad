PadToys.register('ising', function (k, cfg) {
  var L = cfg.size, N = L * L, CELL = Math.max(1, Math.min(8, Math.floor(512 / L))), BUDGET = 10, STILL = 300, KEEP = 300, AVG = 100;
  var TC = 2 / Math.log(1 + Math.SQRT2);
  var T = cfg.temperature, h = cfg.field, algorithm = cfg.algorithm, speed = cfg.speed, initial = cfg.initial;
  var spin = new Int8Array(N), accept = new Float64Array(10), parent = new Int32Array(N), size = new Int32Array(N), fresh = new Int8Array(N);
  var sweeps = 0, acc = 0, flips = 0, said = 0, told = '', history = [], m = 0, e = 0, developing = 0, painting = 0;

  var ctx = k.canvas(L, L, 'The Ising model', true), canvas = ctx.canvas;
  canvas.style.width = L * CELL + 'px';
  var image = ctx.createImageData(L, L), pixels = new Uint32Array(image.data.buffer);
  var SW = 260, SH = 170, sctx = k.canvas(SW, SH, 'The magnetisation over the last sweeps', false), strip = sctx.canvas;
  strip.tabIndex = -1;

  function pack(c) { return (255 << 24 | Math.round(c[2]) << 16 | Math.round(c[1]) << 8 | Math.round(c[0])) >>> 0; }
  var cUp, cDown;
  function colours() { cUp = pack(k.rgb('accent')); cDown = pack(k.rgb('canvas')); }

  // Onsager's exact values for a grid without end and no field, the coupling and Boltzmann's
  // constant 1: the spontaneous magnetisation (1 - sinh(2/T)^-4)^(1/8) below the critical
  // temperature, none above; the energy a spin -coth(2/T) [1 + 2/π (2 tanh²(2/T) - 1) K(κ)],
  // κ = 2 sinh(2/T) / cosh²(2/T), K the complete elliptic integral of the first kind -
  // π / (2 agm(1, √(1 - κ²))).
  function onsagerM(t) { return t >= TC ? 0 : Math.pow(1 - Math.pow(Math.sinh(2 / t), -4), 0.125); }
  function agm(a, b) { for (var i = 0; i < 40 && Math.abs(a - b) > 1e-15 * a; i++) { var c = (a + b) / 2; b = Math.sqrt(a * b); a = c; } return a; }
  function onsagerE(t) {
    var b = 1 / t, c = 2 * Math.tanh(2 * b) * Math.tanh(2 * b) - 1;
    if (Math.abs(c) < 1e-12) return -1 / Math.tanh(2 * b);
    var kappa = 2 * Math.sinh(2 * b) / (Math.cosh(2 * b) * Math.cosh(2 * b)), K = Math.PI / (2 * agm(1, Math.sqrt(Math.max(0, 1 - kappa * kappa))));
    return -1 / Math.tanh(2 * b) * (1 + 2 / Math.PI * c * K);
  }

  // The chance a flip is taken: e^(-ΔE/T) for ΔE = 2 s (Σ neighbours + h) - nine cases of
  // the neighbours' sum and the spin, worked out once a temperature and field.
  function rates() {
    for (var sum = -4; sum <= 4; sum += 2) for (var s = 0; s < 2; s++) {
      var dE = 2 * (s ? 1 : -1) * (sum + h);
      accept[(sum + 4) + s] = dE <= 0 ? 1 : Math.exp(-dE / T);
    }
  }
  function around(i) {
    var x = i % L, y = (i - x) / L;
    return spin[y * L + (x ? x - 1 : L - 1)] + spin[y * L + (x < L - 1 ? x + 1 : 0)] + spin[(y ? y - 1 : L - 1) * L + x] + spin[(y < L - 1 ? y + 1 : 0) * L + x];
  }
  // A Metropolis sweep: N spins picked at random, each flipped with its chance.
  function metropolis() {
    for (var n = 0; n < N; n++) {
      var i = k.int(N), s = spin[i];
      if (k.rand() < accept[around(i) + 4 + (s > 0 ? 1 : 0)]) spin[i] = -s;
    }
  }
  // Swendsen and Wang: every pair of like neighbours bonded with the chance 1 - e^(-2/T), the
  // bonded spins clusters, and every cluster of n spins set anew at once - up with the chance
  // 1 / (1 + e^(-2 h n / T)), so a half without a field. One such step is a sweep: every spin
  // is set once, and the move keeps the Boltzmann weights whatever the clusters are. Without
  // a field, up and down weigh the same, so a grid that came out turned over as a whole is
  // turned back - the picture would otherwise flash between the two colours, sweep by sweep,
  // below the critical point.
  function find(i) { while (parent[i] !== i) { parent[i] = parent[parent[i]]; i = parent[i]; } return i; }
  function bond(a, b) { a = find(a); b = find(b); if (a !== b) parent[a] = b; }
  function clusters() {
    var add = 1 - Math.exp(-2 / T), i, x, y;
    for (i = 0; i < N; i++) { parent[i] = i; size[i] = 0; fresh[i] = 0; }
    for (y = 0; y < L; y++) for (x = 0; x < L; x++) {
      i = y * L + x;
      var r = y * L + (x < L - 1 ? x + 1 : 0), d = (y < L - 1 ? y + 1 : 0) * L + x;
      if (spin[r] === spin[i] && k.rand() < add) bond(i, r);
      if (spin[d] === spin[i] && k.rand() < add) bond(i, d);
    }
    var before = 0, after = 0;
    for (i = 0; i < N; i++) { size[find(i)]++; before += spin[i]; }
    for (i = 0; i < N; i++) {
      var root = find(i);
      if (!fresh[root]) fresh[root] = k.rand() < 1 / (1 + Math.exp(-2 * h * size[root] / T)) ? 1 : -1;
      after += spin[i] = fresh[root];
    }
    if (h === 0 && before * after < 0) for (i = 0; i < N; i++) spin[i] = -spin[i];
  }
  function sweep() {
    if (algorithm === 'clusters') clusters(); else metropolis();
    sweeps++;
    measure();
    history.push(m);
    if (history.length > KEEP) history.shift();
  }

  // The magnetisation and the energy a spin: -Σ s s' over the bonds - each spin's right and
  // lower neighbour once - and -h Σ s.
  function measure() {
    var sum = 0, bonds = 0;
    for (var y = 0; y < L; y++) for (var x = 0; x < L; x++) {
      var i = y * L + x, s = spin[i];
      sum += s;
      bonds += s * (spin[y * L + (x < L - 1 ? x + 1 : 0)] + spin[(y < L - 1 ? y + 1 : 0) * L + x]);
    }
    m = sum / N;
    e = (-bonds - h * sum) / N;
  }
  function begin(kind) {
    for (var i = 0; i < N; i++) spin[i] = kind === 'up' ? 1 : (k.rand() < 0.5 ? 1 : -1);
    sweeps = 0;
    acc = 0;
    history = [];
    measure();
    history.push(m);
  }

  function mean(list, fn) { var s = 0; list.forEach(function (v) { s += fn(v); }); return list.length ? s / list.length : 0; }
  function num(x, n) { return (x < 0 ? '−' : '') + Math.abs(x).toFixed(n); }
  function draw() {
    for (var i = 0; i < N; i++) pixels[i] = spin[i] > 0 ? cUp : cDown;
    ctx.putImageData(image, 0, 0);
    var exact = h === 0 ? ' · Onsager: |m| ' + onsagerM(T).toFixed(3) + ', energy ' + num(onsagerE(T), 3) : '';
    k.info('Sweep ' + sweeps.toLocaleString('en') + ' · T ' + T.toFixed(2) + (Math.abs(T - TC) < 0.005 ? ' (critical)' : '') + (h ? ' · field ' + num(h, 2) : '') + ' · magnetisation ' + num(m, 3) + ' · energy ' + num(e, 3) + ' a spin' + exact);
    var now = Date.now(), what = T + '/' + h + algorithm;
    if (now - said > 1000 || what !== told || !k.playing || k.held) {
      said = now;
      told = what;
      var up = Math.round(50 * (1 + m));
      k.describe(canvas, 'The Ising model on a ' + L + ' by ' + L + ' grid at temperature ' + T.toFixed(2) + (T < TC ? ', below' : T > TC ? ', above' : ', at') + ' the critical ' + TC.toFixed(3) + ': ' + up + '% of the spins up, the magnetisation ' + num(m, 2));
      describeStrip();
    }
    drawStrip();
  }

  // The strip: the magnetisation sweep by sweep, from -1 to 1, with Onsager's ± value dashed
  // below the critical temperature; the mean of |m| and of the energy over the last sweeps.
  function sy(v) { return 12 + (1 - v) / 2 * (SH - 40); }
  function drawStrip() {
    sctx.fillStyle = k.color('canvas');
    sctx.fillRect(0, 0, SW, SH);
    sctx.strokeStyle = k.color('line');
    sctx.lineWidth = 1;
    sctx.beginPath(); sctx.moveTo(24, sy(1)); sctx.lineTo(24, sy(-1)); sctx.moveTo(24, sy(0)); sctx.lineTo(SW - 6, sy(0)); sctx.stroke();
    sctx.fillStyle = k.color('muted');
    sctx.font = '10px system-ui, sans-serif';
    sctx.textAlign = 'right';
    sctx.fillText('1', 20, sy(1) + 4);
    sctx.fillText('0', 20, sy(0) + 4);
    sctx.fillText('−1', 20, sy(-1) + 4);
    var exact = onsagerM(T);
    if (h === 0 && exact > 0) {
      sctx.setLineDash([4, 3]);
      sctx.strokeStyle = k.color('muted');
      sctx.beginPath(); sctx.moveTo(24, sy(exact)); sctx.lineTo(SW - 6, sy(exact)); sctx.moveTo(24, sy(-exact)); sctx.lineTo(SW - 6, sy(-exact)); sctx.stroke();
      sctx.setLineDash([]);
    }
    sctx.strokeStyle = k.color('accent');
    sctx.lineWidth = 1.5;
    sctx.beginPath();
    history.forEach(function (v, j) { var x = 24 + j / (KEEP - 1) * (SW - 30); if (j) sctx.lineTo(x, sy(v)); else sctx.moveTo(x, sy(v)); });
    sctx.stroke();
    var last = history.slice(-AVG);
    sctx.fillStyle = k.color('muted');
    sctx.textAlign = 'left';
    sctx.fillText('magnetisation, the last ' + KEEP + ' sweeps', 26, SH - 18);
    sctx.fillText('mean |m| ' + mean(last, Math.abs).toFixed(3) + (h === 0 ? ' · Onsager ' + exact.toFixed(3) : ''), 26, SH - 5);
  }
  function describeStrip() {
    var last = history.slice(-AVG);
    k.describe(strip, 'The magnetisation over the last ' + history.length + ' sweeps: its size ' + mean(last, Math.abs).toFixed(2) + ' on average' + (h === 0 ? ', Onsager\'s for a grid without end ' + onsagerM(T).toFixed(2) : ''));
  }

  // The sweeps go by a clock of their own - speed a second - at most BUDGET milliseconds of
  // them a frame.
  k.loop(function (dt) {
    acc += dt * speed;
    var n = Math.floor(acc), t0 = performance.now();
    acc -= n;
    while (n-- > 0) {
      sweep();
      if (performance.now() - t0 > BUDGET) { acc = 0; break; }
    }
    draw();
  }, draw);

  // What waits for Play shows the grid after STILL sweeps, worked out out of sight a slice
  // at a time and drawn once.
  k.still(function () {
    var job = ++developing, reduced = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    k.status('Working out a still picture ...');
    (function slice() {
      if (job !== developing || k.playing) return;
      var t0 = performance.now();
      while (sweeps < STILL && performance.now() - t0 < BUDGET) sweep();
      if (sweeps < STILL) { setTimeout(slice, 0); return; }
      draw();
      k.status((reduced ? 'Paused, as reduced motion is asked for: the' : 'The') + ' grid after ' + STILL + ' sweeps - press Play to watch');
    })();
  });

  // Painting: the pointer turns spins up, Shift or the right button down.
  function paint(p) {
    var cx = Math.floor(p.x), cy = Math.floor(p.y), R = Math.max(1, Math.round(L / 40));
    for (var dy = -R; dy <= R; dy++) for (var dx = -R; dx <= R; dx++) {
      if (dx * dx + dy * dy > R * R) continue;
      spin[((cy + dy + L) % L) * L + (cx + dx + L) % L] = painting;
    }
    measure();
    if (!k.playing || k.held) draw();
  }
  k.pointer(canvas, {
    down: function (p, ev) { developing++; painting = ev.button === 2 || p.shift ? -1 : 1; paint(p); },
    move: function (p, ev, pressed) { if (pressed && painting) paint(p); },
    up: function () { painting = 0; }
  });

  function setT(v) { T = Math.max(0.5, Math.min(5, v)); tIn.set(T); rates(); draw(); }
  function restart(kind) { developing++; initial = kind; begin(kind); draw(); k.status(kind === 'up' ? 'A cold start: every spin up' : 'A hot start: every spin at random'); }
  function stepOne() { developing++; k.pause(); sweep(); draw(); k.status('Sweep ' + sweeps); }

  k.keys({
    h: function () { setT(Math.round(T * 100 + 10) / 100); k.status('Warmer: T ' + T.toFixed(2)); },
    c: function () { setT(Math.round(T * 100 - 10) / 100); k.status('Cooler: T ' + T.toFixed(2)); },
    t: function () { setT(TC); k.status('At the critical temperature, 2.269'); },
    r: function () { restart('random'); }, u: function () { restart('up'); }, s: stepOne
  });

  k.onPlay = function () { developing++; k.status('Sweeping at ' + speed + ' a second - H warmer, C cooler, T the critical point'); };
  k.onPause = function () { k.status('Paused at sweep ' + sweeps); };

  k.row();
  k.playButton();
  k.button('Step', stepOne, 'One sweep (S)');
  k.button('Hot start', function () { restart('random'); }, 'Every spin at random (R)');
  k.button('Cold start', function () { restart('up'); }, 'Every spin up (U)');
  k.button('Critical', function () { k.keymap.t(); }, 'The critical temperature, 2.269 (T)');
  k.select('Algorithm', [['metropolis', 'Metropolis'], ['clusters', 'Swendsen-Wang']], algorithm, function (v) { algorithm = v; draw(); k.status(v === 'clusters' ? 'Swendsen-Wang: whole clusters set at once' : 'Metropolis: a spin at a time'); });
  k.row();
  var tIn = k.slider('Temperature', 0.5, 5, 0.01, T, function (v) { T = v; rates(); draw(); }, function (v) { return v.toFixed(2); });
  k.slider('Field', -1, 1, 0.01, h, function (v) { h = v; rates(); draw(); }, function (v) { return num(v, 2); });
  k.slider('Speed', 1, 100, 1, speed, function (v) { speed = v; }, function (v) { return v + '/s'; });
  k.theme(function () { colours(); draw(); });

  colours();
  rates();
  begin(initial);
  draw();
  k.begin('Sweeping - H warmer, C cooler, T the critical point');
});
