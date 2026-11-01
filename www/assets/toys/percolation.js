PadToys.register('percolation', function (k, cfg) {
  var L = cfg.size, N = L * L, CELL = Math.max(2, Math.min(12, Math.floor(480 / L))), BUDGET = 10, STILL = 100;
  // The threshold of an endless square grid: four neighbours 0.59274621 (Newman and Ziff,
  // 2000); eight make the matching grid of four, whose threshold is one minus it.
  var PC = { 4: 0.592746, 8: 0.407254 };
  var p = cfg.probability, nb = cfg.neighbours, mode = cfg.mode, colour = cfg.colour, speed = cfg.speed;
  var rnd = new Float64Array(N), open = new Uint8Array(N), wet = new Uint8Array(N), label = new Int32Array(N), sizes = [], spans = {};
  var front = [], flowing = false, flowSteps = 0, deepest = 0, acc = 0, said = 0, told = '';
  var parent = new Int32Array(N + 2), order = new Int32Array(N), opened = 0, trials = [], rest = 0, TOP = N, BOTTOM = N + 1;
  var cursor = { x: L >> 1, y: L >> 1 }, keyboard = false, painting = 0;
  var MOVES = [[1, 0], [-1, 0], [0, 1], [0, -1], [1, 1], [1, -1], [-1, 1], [-1, -1]];

  var ctx = k.canvas(L, L, 'Site percolation', true), canvas = ctx.canvas;
  canvas.style.width = L * CELL + 'px';
  var image = ctx.createImageData(L, L), pixels = new Uint32Array(image.data.buffer);
  var CW = 260, CH = 200, cctx = k.canvas(CW, CH, 'The share of trials that got through by each chance', false), chart = cctx.canvas;
  chart.tabIndex = -1;

  function pack(c) { return (255 << 24 | Math.round(c[2]) << 16 | Math.round(c[1]) << 8 | Math.round(c[0])) >>> 0; }
  function mix(a, b, f) { return [a[0] + (b[0] - a[0]) * f, a[1] + (b[1] - a[1]) * f, a[2] + (b[2] - a[2]) * f]; }
  var cClosed, cOpen, cWet, cCursor, cClusters = [];
  function colours() {
    var bg = k.rgb('canvas');
    cClosed = pack(mix(bg, k.rgb('ink'), 0.45));
    cOpen = pack(bg);
    cWet = pack(k.rgb('accent'));
    cCursor = pack(k.rgb('mark'));
    cClusters = ['accent2', 'accent3', 'good', 'mark', 'bad'].map(function (r) { return pack(mix(bg, k.rgb(r), 0.8)); });
  }

  function each(i, fn) {
    var x = i % L, y = (i - x) / L;
    for (var d = 0; d < nb; d++) {
      var nx = x + MOVES[d][0], ny = y + MOVES[d][1];
      if (nx >= 0 && ny >= 0 && nx < L && ny < L) fn(ny * L + nx);
    }
  }

  // The clusters of open sites, each a number in label, and which of them join the top row to
  // the bottom one.
  function clusters() {
    label.fill(-1);
    sizes = [];
    spans = {};
    var stack = [];
    for (var i = 0; i < N; i++) {
      if (!open[i] || label[i] >= 0) continue;
      var id = sizes.length, n = 0, top = false, bottom = false;
      label[i] = id;
      stack.push(i);
      while (stack.length) {
        var j = stack.pop();
        n++;
        if (j < L) top = true;
        if (j >= N - L) bottom = true;
        each(j, function (q) { if (open[q] && label[q] < 0) { label[q] = id; stack.push(q); } });
      }
      sizes.push(n);
      if (top && bottom) spans[id] = true;
    }
  }

  // The water: every open site of the top row at once, then a step at a time to the open
  // neighbours of the wet ones - or all the way at once, for a toy that is not playing.
  function pour() {
    wet.fill(0);
    front = [];
    flowSteps = 0;
    deepest = 0;
    for (var x = 0; x < L; x++) if (open[x]) { wet[x] = 1; front.push(x); }
    flowing = front.length > 0;
  }
  function seep(quiet) {
    var next = [];
    front.forEach(function (i) {
      each(i, function (q) {
        if (open[q] && !wet[q]) { wet[q] = 1; next.push(q); deepest = Math.max(deepest, Math.floor(q / L)); }
      });
    });
    front = next;
    flowSteps++;
    if (!front.length) {
      flowing = false;
      k.pause();
      if (!quiet) k.status(through() ? 'The water got through in ' + flowSteps + ' steps' : 'The water stopped ' + (deepest + 1) + ' rows down, after ' + flowSteps + ' steps');
    }
  }
  function flood(quiet) { pour(); while (front.length) seep(quiet); }
  function through() { for (var x = N - L; x < N; x++) if (wet[x]) return true; return false; }

  // The grid at the chance p: a site is open while its own number is below p, so a larger p
  // opens more of the same sites. A site you opened or closed keeps it.
  function apply(restart) {
    for (var i = 0; i < N; i++) open[i] = rnd[i] < p ? 1 : 0;
    clusters();
    if (restart && k.playing && !k.held && mode === 'flow') pour();
    else flood();
  }
  function fresh() { for (var i = 0; i < N; i++) rnd[i] = k.rand(); apply(true); }

  // The experiment: an empty grid opened a site at a time in a random order, the sites
  // joined in a union-find with a site above the top row and one below the bottom, until
  // those two are one - the share open then is noted, and a new trial begins.
  function find(i) { while (parent[i] !== i) { parent[i] = parent[parent[i]]; i = parent[i]; } return i; }
  function union(a, b) { a = find(a); b = find(b); if (a !== b) parent[a] = b; }
  function trial() {
    open.fill(0);
    wet.fill(0);
    for (var i = 0; i < N + 2; i++) parent[i] = i;
    for (var j = 0; j < N; j++) order[j] = j;
    for (var s = N - 1; s > 0; s--) { var r = k.int(s + 1), t = order[s]; order[s] = order[r]; order[r] = t; }
    opened = 0;
  }
  function openOne() {
    var i = order[opened++];
    open[i] = 1;
    if (i < L) union(i, TOP);
    if (i >= N - L) union(i, BOTTOM);
    each(i, function (q) { if (open[q]) union(i, q); });
    if (find(TOP) !== find(BOTTOM)) return false;
    trials.push(opened / N);
    return true;
  }
  function wetNow() { var top = find(TOP); for (var i = 0; i < N; i++) wet[i] = open[i] && find(i) === top ? 1 : 0; }
  function runTrials(n) { for (var t = 0; t < n; t++) { trial(); while (!openOne()); } wetNow(); }

  function mean() { var s = 0; trials.forEach(function (v) { s += v; }); return s / trials.length; }
  function stderr() {
    if (trials.length < 2) return 0;
    var m = mean(), s = 0;
    trials.forEach(function (v) { s += (v - m) * (v - m); });
    return Math.sqrt(s / (trials.length - 1) / trials.length);
  }
  function fixed(x, n) { return x.toFixed(n); }
  function count() { var n = 0; for (var i = 0; i < N; i++) n += open[i]; return n; }

  function draw() {
    var i, spanning = false;
    for (var id in spans) { spanning = true; break; }
    for (i = 0; i < N; i++) {
      if (!open[i]) pixels[i] = cClosed;
      else if (colour === 'clusters' && mode === 'flow') pixels[i] = spans[label[i]] ? cWet : cClusters[label[i] % cClusters.length];
      else pixels[i] = wet[i] ? cWet : cOpen;
    }
    if (keyboard && document.activeElement === canvas) pixels[cursor.y * L + cursor.x] = cCursor;
    ctx.putImageData(image, 0, 0);
    var n = count(), share = Math.round(100 * n / N) + '% open';
    if (mode === 'flow') {
      var big = 0;
      sizes.forEach(function (s) { if (s > big) big = s; });
      k.info('Chance ' + fixed(p, 3) + ' · ' + share + ' · ' + sizes.length.toLocaleString('en') + ' clusters, the largest ' + big.toLocaleString('en') + ' sites · ' + (spanning ? 'a cluster spans the grid' : 'no cluster spans the grid') + ' · ' + nb + ' neighbours');
    } else
      k.info('Trial ' + (rest > 0 ? trials.length + ' got through' : trials.length + 1) + ': ' + n.toLocaleString('en') + ' of ' + N.toLocaleString('en') + ' open · ' + trials.length + (trials.length === 1 ? ' trial' : ' trials') + (trials.length ? ', mean ' + fixed(mean(), 4) + (trials.length > 1 ? ' ± ' + fixed(stderr(), 4) : '') : '') + ' · endless grid ' + PC[nb].toFixed(4));
    var now = Date.now(), what = mode + p + colour + nb + trials.length;
    if (now - said > 1000 || what !== told || !k.playing || k.held) {
      said = now;
      told = what;
      k.describe(canvas, 'Site percolation on a ' + L + ' by ' + L + ' grid, ' + nb + ' neighbours: ' + share + (mode === 'flow' ? ' at the chance ' + fixed(p, 3) + ', ' + (spanning ? 'a cluster spans it from top to bottom' : 'no cluster spans it') : ' in trial ' + (trials.length + 1) + ' of the experiment'));
      describeChart();
    }
  }

  // The curve: of the trials so far, the share that had got through by each chance - the
  // finite grid's own threshold where it rises, the endless grid's as a dashed line.
  function lo() { return Math.round((PC[nb] - 0.3) * 10) / 10; }
  function cx(x) { return 30 + (x - lo()) / 0.6 * (CW - 40); }
  function cy(y) { return CH - 26 - y * (CH - 40); }
  function drawChart() {
    cctx.fillStyle = k.color('canvas');
    cctx.fillRect(0, 0, CW, CH);
    cctx.strokeStyle = k.color('line');
    cctx.lineWidth = 1;
    cctx.beginPath(); cctx.moveTo(30, cy(1)); cctx.lineTo(30, cy(0)); cctx.lineTo(CW - 10, cy(0)); cctx.stroke();
    cctx.fillStyle = k.color('muted');
    cctx.font = '10px system-ui, sans-serif';
    cctx.textAlign = 'center';
    for (var tk = 0; tk <= 6; tk += 2) cctx.fillText(fixed(lo() + tk / 10, 1), cx(lo() + tk / 10), CH - 12);
    cctx.fillText('chance a site is open', CW / 2 + 10, CH - 1);
    cctx.textAlign = 'right';
    cctx.fillText('1', 25, cy(1) + 4);
    cctx.fillText('0', 25, cy(0) + 4);
    cctx.save(); cctx.translate(10, CH / 2 - 6); cctx.rotate(-Math.PI / 2); cctx.textAlign = 'center'; cctx.fillText('got through', 0, 0); cctx.restore();
    cctx.setLineDash([4, 3]);
    cctx.strokeStyle = k.color('muted');
    cctx.beginPath(); cctx.moveTo(cx(PC[nb]), cy(0)); cctx.lineTo(cx(PC[nb]), cy(1)); cctx.stroke();
    cctx.setLineDash([]);
    cctx.textAlign = 'right';
    cctx.fillText('endless grid ' + PC[nb].toFixed(4), cx(PC[nb]) - 4, cy(1) + 10);
    if (mode === 'flow') {
      cctx.strokeStyle = k.color('accent2');
      cctx.lineWidth = 2;
      var px = Math.max(30, Math.min(CW - 10, cx(p)));
      cctx.beginPath(); cctx.moveTo(px, cy(0)); cctx.lineTo(px, cy(1)); cctx.stroke();
    }
    if (trials.length) {
      var sorted = trials.slice().sort(function (a, b) { return a - b; });
      cctx.strokeStyle = k.color('accent');
      cctx.lineWidth = 2;
      cctx.beginPath();
      cctx.moveTo(30, cy(0));
      sorted.forEach(function (v, j) {
        var x = Math.max(30, Math.min(CW - 10, cx(v)));
        cctx.lineTo(x, cy(j / sorted.length));
        cctx.lineTo(x, cy((j + 1) / sorted.length));
      });
      cctx.lineTo(CW - 10, cy(1));
      cctx.stroke();
    } else {
      cctx.fillStyle = k.color('muted');
      cctx.textAlign = 'center';
      cctx.fillText('the experiment draws a curve here', CW / 2 + 10, cy(0.55));
    }
  }
  function describeChart() {
    k.describe(chart, trials.length
      ? 'Of ' + trials.length + ' trials, the share that got through by each chance: they got through at ' + fixed(mean(), 3) + ' on average, the endless grid at ' + PC[nb].toFixed(4)
      : 'No trials yet - the experiment draws the share of trials that got through by each chance; the endless grid gets through at ' + PC[nb].toFixed(4));
  }

  // Flow: the water a step further, speed × 4 steps a second. Experiment: sites opened at
  // 2^(speed - 6) times the grid a second, at most BUDGET milliseconds of it a frame; a trial
  // that got through stays a moment before the next begins.
  k.loop(function (dt) {
    if (mode === 'flow') {
      if (flowing) {
        acc += dt * speed * 4;
        while (acc >= 1 && flowing) { seep(); acc -= 1; }
      } else acc = 0;
    } else {
      if (rest > 0) { rest -= dt; if (rest <= 0) trial(); draw(); return; }
      acc += dt * N * Math.pow(2, speed - 6);
      var t0 = performance.now(), got = false;
      while (acc >= 1 && !got) {
        got = openOne();
        acc -= 1;
        if (performance.now() - t0 > BUDGET) { acc = 0; break; }
      }
      if (got) {
        acc = 0;
        wetNow();
        drawChart();
        k.status('Trial ' + trials.length + ' got through with ' + Math.round(100 * opened / N) + '% open');
        rest = speed >= 9 ? 0.15 : 0.6;
      } else wetNow();
    }
    draw();
  }, function () { draw(); drawChart(); });

  // What waits for Play shows the end: the water as far as it goes, or a hundred trials run.
  k.still(function () {
    if (mode === 'flow') flood(true);
    else if (!trials.length) { runTrials(STILL); k.status('A hundred trials run - press Play to watch more'); }
    draw();
    drawChart();
  });

  // A site clicked opens or closes - its own number set below or above any chance - and a
  // drag goes on as the first site went.
  function setSite(i, on) { rnd[i] = on ? -1 : 2; }
  function siteAt(pt) { return Math.max(0, Math.min(L - 1, Math.floor(pt.y))) * L + Math.max(0, Math.min(L - 1, Math.floor(pt.x))); }
  function paint(pt) {
    var i = siteAt(pt);
    if (!!open[i] === painting > 0) return;
    setSite(i, painting > 0);
    apply(false);
    draw();
  }
  k.pointer(canvas, {
    down: function (pt) {
      keyboard = false;
      if (mode !== 'flow') { k.status('The experiment opens the sites - switch to flow to open and close them yourself'); return; }
      painting = open[siteAt(pt)] ? -1 : 1;
      paint(pt);
    },
    move: function (pt, e, pressed) { if (pressed && painting && mode === 'flow') paint(pt); },
    up: function () { painting = 0; }
  });

  function moveCursor(dx, dy) {
    return function () {
      keyboard = true;
      cursor.x = (cursor.x + dx + L) % L;
      cursor.y = (cursor.y + dy + L) % L;
      draw();
    };
  }
  function toggleCursor() {
    keyboard = true;
    if (mode !== 'flow') return;
    var i = cursor.y * L + cursor.x;
    setSite(i, !open[i]);
    apply(false);
    draw();
    k.status('Site ' + (cursor.x + 1) + ', ' + (cursor.y + 1) + (open[i] ? ' opened' : ' closed'));
  }
  // The chance makes the grid of flow; an experiment's trial opens its own sites in a random
  // order, so there the chance is only kept for flow, the trial and its sites left alone.
  function chance() {
    if (mode === 'flow') apply(true);
    else k.status('The chance is for flow - the experiment opens its sites in a random order');
    draw();
    drawChart();
  }
  function setP(v) {
    p = Math.max(0, Math.min(1, Math.round(v * 1000) / 1000));
    pIn.set(p);
    chance();
  }
  function setMode(v) {
    mode = v;
    modeIn.value = v;
    acc = 0;
    rest = 0;
    if (mode === 'experiment') { trial(); k.status('The experiment: sites opened one by one until the water gets through, again and again'); }
    else { apply(true); k.status('Flow at the chance ' + fixed(p, 3)); }
    draw();
    drawChart();
  }
  function newGrid() {
    if (mode === 'flow') { fresh(); k.status('A new grid at the chance ' + fixed(p, 3)); }
    else { trials = []; trial(); k.status('The trials begin again'); }
    draw();
    drawChart();
  }

  k.keys({
    ArrowUp: moveCursor(0, -1), ArrowDown: moveCursor(0, 1), ArrowLeft: moveCursor(-1, 0), ArrowRight: moveCursor(1, 0), Enter: toggleCursor,
    PageUp: function () { setP(p + 0.01); }, PageDown: function () { setP(p - 0.01); },
    r: newGrid, m: function () { setMode(mode === 'flow' ? 'experiment' : 'flow'); },
    s: function () { k.pause(); if (mode === 'flow') { if (!flowing) pour(); seep(); } else if (openOne()) { wetNow(); drawChart(); trial(); } else wetNow(); draw(); }
  });
  canvas.addEventListener('blur', function () { if (keyboard) draw(); });

  // Play pours the water again, or lets it seep on from where a pause held it; it pauses by
  // itself when the water goes no further.
  k.onPlay = function () {
    if (mode === 'flow') { if (!flowing) pour(); k.status('The water seeps down from the top row'); }
    else k.status('Opening sites until the water gets through');
  };
  k.onPause = function () { if (mode !== 'flow' || flowing) k.status('Paused'); };

  k.row();
  k.playButton();
  k.button('Step', function () { k.keymap.s(); }, 'One step of the water, or one site opened (S)');
  k.button('New grid', newGrid, 'New random numbers, or the trials again (R)');
  var modeIn = k.select('Mode', [['flow', 'Flow'], ['experiment', 'Experiment']], mode, setMode).querySelector('select');
  k.row();
  var pIn = k.slider('Chance', 0, 1, 0.001, p, function (v) { p = v; chance(); }, function (v) { return v.toFixed(3); });
  k.select('Neighbours', [['4', '4, beside'], ['8', '8, corners too']], String(nb), function (v) { nb = +v; trials = []; if (mode === 'flow') apply(true); else trial(); draw(); drawChart(); });
  k.select('Colour', [['flow', 'The water'], ['clusters', 'Clusters']], colour, function (v) { colour = v; draw(); });
  k.slider('Speed', 1, 10, 1, speed, function (v) { speed = v; });
  k.theme(function () { colours(); draw(); drawChart(); });

  colours();
  for (var i = 0; i < N; i++) rnd[i] = k.rand();
  apply(false);
  if (mode === 'experiment') trial();
  draw();
  drawChart();
  k.begin(mode === 'flow' ? 'The water seeps down from the top row' : 'Opening sites until the water gets through');
});
