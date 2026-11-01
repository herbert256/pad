PadToys.register('ant', function (k, cfg) {
  var N = cfg.size, rule = cfg.rule, speed = cfg.speed, CELL = Math.max(1, Math.floor(480 / N));
  var grid = new Uint8Array(N * N), ants = [], steps = 0, acc = 0, said = 0;
  var DIRS = [[0, -1], [1, 0], [0, 1], [-1, 0]];
  var ctx = k.canvas(N, N, "Langton's ant", true), canvas = ctx.canvas;
  canvas.style.width = N * CELL + 'px';
  var image = ctx.createImageData(N, N), pixels = new Uint32Array(image.data.buffer), table = [];

  // The colours of the squares: the canvas for the first, then steps through the palette -
  // the ink alone for the two-colour ant.
  function colours() {
    var stops = rule.length === 2 ? ['ink'] : ['accent', 'accent3', 'accent2', 'mark', 'good', 'bad'];
    table = [pack(k.rgb('canvas'))];
    for (var i = 1; i < rule.length; i++) {
      var f = rule.length === 2 ? 0 : (i - 1) / Math.max(1, rule.length - 2) * (stops.length - 1);
      var a = k.rgb(stops[Math.floor(f)]), b = k.rgb(stops[Math.min(stops.length - 1, Math.floor(f) + 1)]), t = f - Math.floor(f);
      table.push(pack([a[0] + (b[0] - a[0]) * t, a[1] + (b[1] - a[1]) * t, a[2] + (b[2] - a[2]) * t]));
    }
    for (var j = 0; j < grid.length; j++) pixels[j] = table[grid[j]];
  }
  function pack(c) { return (255 << 24 | Math.round(c[2]) << 16 | Math.round(c[1]) << 8 | Math.round(c[0])) >>> 0; }

  function addAnt(x, y, d) {
    if (ants.length >= 8) ants.shift();
    ants.push({ x: x, y: y, d: d });
  }

  function reset() {
    grid.fill(0);
    ants = [];
    steps = 0;
    var n = cfg.ants, mid = N >> 1;
    for (var i = 0; i < n; i++) {
      if (n === 1) addAnt(mid, mid, 0);
      else addAnt((mid + Math.round(Math.cos(i / n * Math.PI * 2) * N / 6) + N) % N, (mid + Math.round(Math.sin(i / n * Math.PI * 2) * N / 6) + N) % N, k.int(4));
    }
    colours();
  }

  // One step of every ant: turn by the colour of its square, step the colour on, move ahead -
  // the grid is a torus, so an ant leaving one side comes back at the other.
  function step() {
    for (var a = 0; a < ants.length; a++) {
      var ant = ants[a], i = ant.y * N + ant.x, c = grid[i], turn = rule.charCodeAt(c);
      if (turn === 82) ant.d = (ant.d + 1) & 3;
      else if (turn === 76) ant.d = (ant.d + 3) & 3;
      else if (turn === 85) ant.d = (ant.d + 2) & 3;
      c = (c + 1) % rule.length;
      grid[i] = c;
      pixels[i] = table[c];
      ant.x = (ant.x + DIRS[ant.d][0] + N) % N;
      ant.y = (ant.y + DIRS[ant.d][1] + N) % N;
    }
    steps++;
  }

  function draw() {
    ctx.putImageData(image, 0, 0);
    ctx.fillStyle = k.color('bad');
    ants.forEach(function (ant) { ctx.fillRect(ant.x - (CELL < 3 ? 1 : 0), ant.y - (CELL < 3 ? 1 : 0), CELL < 3 ? 3 : 1, CELL < 3 ? 3 : 1); });
    k.info('Step ' + steps.toLocaleString('en') + ' · ' + ants.length + (ants.length === 1 ? ' ant' : ' ants') + ' · rule ' + rule);
    var now = Date.now();
    if (now - said > 1000) {
      said = now;
      k.describe(canvas, "Langton's ant, rule " + rule + ': ' + ants.length + (ants.length === 1 ? ' ant' : ' ants') + ' on a grid of ' + N + ' by ' + N + ' squares after ' + steps + ' steps');
    }
  }

  k.loop(function (dt) {
    acc += dt * speed;
    var n = Math.min(Math.floor(acc), 200000);
    acc -= Math.floor(acc);
    for (var i = 0; i < n; i++) step();
    draw();
  }, draw);

  k.pointer(canvas, {
    down: function (p) {
      addAnt(Math.max(0, Math.min(N - 1, Math.floor(p.x))), Math.max(0, Math.min(N - 1, Math.floor(p.y))), 0);
      k.status('An ant put down - ' + ants.length + ' walking');
      if (!k.playing) draw();
    }
  });

  function stepOnce() { k.pause(); step(); draw(); }
  k.keys({ s: stepOnce, n: stepOnce, c: function () { reset(); draw(); } });
  k.onPlay = function () { k.status('Walking - click the grid to put down another ant'); };
  k.onPause = function () { k.status('Paused after ' + steps.toLocaleString('en') + ' steps'); };

  var rules = [['RL', 'RL - Langton'], ['LLRR', 'LLRR - symmetric'], ['RLR', 'RLR - chaos'], ['LRRRRRLLR', 'LRRRRRLLR - square'],
               ['RRLLLRLLLRRR', 'RRLLLRLLLRRR - triangle'], ['LLRRRLRLRLLR', 'LLRRRLRLRLLR - highway'], ['RRLL', 'RRLL - brain'], ['LRRL', 'LRRL - cardioid']];
  if (!rules.some(function (r) { return r[0] === rule; })) rules.unshift([rule, rule]);

  k.row();
  k.playButton();
  k.button('Step', stepOnce, 'One step (S)');
  k.button('Clear', function () { reset(); draw(); k.status('Cleared - ' + ants.length + (ants.length === 1 ? ' ant' : ' ants') + ' back at the start'); }, 'Start again (C)');
  k.row();
  k.select('Rule', rules, rule, function (v) { rule = v; reset(); draw(); k.status('Rule ' + rule); });
  var levels = [1, 10, 50, 200, 1000, 3000, 10000, 50000];
  var at = 0;
  levels.forEach(function (v, i) { if (Math.abs(Math.log(v) - Math.log(speed)) < Math.abs(Math.log(levels[at]) - Math.log(speed))) at = i; });
  k.slider('Speed', 0, levels.length - 1, 1, at, function (v) { speed = levels[v]; }, function (v) { return levels[v].toLocaleString('en') + '/s'; });
  k.theme(colours);

  reset();
  k.begin();
});
