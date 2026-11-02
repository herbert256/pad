PadToys.register('life', function (k, cfg) {
  var W = cfg.width, H = cfg.height, wrap = !!cfg.wrap;
  var CELL = Math.max(3, Math.min(14, Math.floor(640 / W), Math.floor(480 / H)));
  var born = new Uint8Array(9), keep = new Uint8Array(9);
  cfg.born.forEach(function (n) { born[n] = 1; });
  cfg.survive.forEach(function (n) { keep[n] = 1; });

  var cells = new Uint8Array(W * H), next = new Uint8Array(W * H), age = new Uint16Array(W * H), fade = new Float32Array(W * H);
  var gen = 0, alive = 0, acc = 0, speed = cfg.speed, cursor = { x: W >> 1, y: H >> 1 }, keyboard = false, said = 0;
  var ctx = k.canvas(W * CELL, H * CELL, 'Game of Life', false), canvas = ctx.canvas;

  function census() {
    alive = 0;
    for (var i = 0; i < cells.length; i++) alive += cells[i];
  }

  // One generation: every cell counts its eight neighbours - across the edges when wrap
  // joins them, else the cells beyond the edge are dead.
  function step() {
    var changed = false, x, y, n, i, up, down, left, right;
    alive = 0;
    for (y = 0; y < H; y++) {
      up = y - 1; down = y + 1;
      if (wrap) { up = (up + H) % H; down = down % H; }
      for (x = 0; x < W; x++) {
        left = x - 1; right = x + 1;
        if (wrap) { left = (left + W) % W; right = right % W; }
        n = 0;
        if (up >= 0) {
          if (left >= 0) n += cells[up * W + left];
          n += cells[up * W + x];
          if (right < W) n += cells[up * W + right];
        }
        if (left >= 0) n += cells[y * W + left];
        if (right < W) n += cells[y * W + right];
        if (down < H) {
          if (left >= 0) n += cells[down * W + left];
          n += cells[down * W + x];
          if (right < W) n += cells[down * W + right];
        }
        i = y * W + x;
        var was = cells[i], now = was ? keep[n] : born[n];
        next[i] = now;
        if (now !== was) changed = true;
        if (now) { age[i] = was ? Math.min(age[i] + 1, 60000) : 0; alive++; }
        else if (was) fade[i] = 1;
      }
    }
    var t = cells; cells = next; next = t;
    gen++;
    return changed;
  }

  function draw(dt) {
    var dark = k.dark(), i, x, y;
    ctx.fillStyle = k.color('canvas');
    ctx.fillRect(0, 0, W * CELL, H * CELL);
    if (CELL >= 6) {
      ctx.fillStyle = k.color('grid');
      for (x = 1; x < W; x++) ctx.fillRect(x * CELL, 0, 1, H * CELL);
      for (y = 1; y < H; y++) ctx.fillRect(0, y * CELL, W * CELL, 1);
    }
    // The cells that died lately glow faintly a while, so the movement leaves a wake.
    var decay = Math.pow(0.12, dt || 0), wake = k.rgb('accent'), pad = CELL >= 6 ? 1 : 0;
    for (i = 0; i < cells.length; i++) {
      if (fade[i] > 0.02 && !cells[i]) {
        ctx.fillStyle = 'rgba(' + wake[0] + ',' + wake[1] + ',' + wake[2] + ',' + (fade[i] * (dark ? 0.35 : 0.22)).toFixed(3) + ')';
        ctx.fillRect((i % W) * CELL + pad, Math.floor(i / W) * CELL + pad, CELL - pad, CELL - pad);
      }
      fade[i] = fade[i] > 0.02 ? fade[i] * decay : 0;
    }
    var young = k.color('accent2'), old = k.color('accent');
    for (i = 0; i < cells.length; i++)
      if (cells[i]) {
        ctx.fillStyle = age[i] < 2 ? young : old;
        ctx.fillRect((i % W) * CELL + pad, Math.floor(i / W) * CELL + pad, CELL - pad, CELL - pad);
      }
    if (keyboard && document.activeElement === canvas) {
      ctx.strokeStyle = k.color('ink');
      ctx.lineWidth = 2;
      ctx.strokeRect(cursor.x * CELL + 1, cursor.y * CELL + 1, CELL - 2, CELL - 2);
    }
    k.info('Generation ' + gen + ' · ' + alive + ' alive · ' + cfg.rule + (wrap ? ' · edges joined' : ''));
    var now = Date.now();
    if (now - said > 1000) {
      said = now;
      k.describe(canvas, 'Game of Life: a grid of ' + W + ' by ' + H + ' cells, ' + alive + ' alive, generation ' + gen);
    }
  }

  function advance() {
    var changed = step();
    if (!alive) { k.pause(); k.status('Everything died out at generation ' + gen + ' - draw or pick a pattern'); }
    else if (!changed) { k.pause(); k.status('Settled into a still life at generation ' + gen); }
  }

  k.loop(function (dt) {
    acc += dt * speed;
    var n = 0;
    while (acc >= 1 && n < 4 && k.playing) { advance(); acc -= 1; n++; }
    if (acc > 1) acc = 0;
    draw(dt);
  }, function () { draw(0); });

  function clear() {
    cells.fill(0); age.fill(0); fade.fill(0); gen = 0; alive = 0;
  }

  // A pattern in the middle of the grid - the gun near the top left, where its gliders have
  // room to fly - or cells scattered at random, a quarter of them alive.
  function place(name) {
    clear();
    if (name === 'random') {
      for (var i = 0; i < cells.length; i++) cells[i] = k.chance(0.25) ? 1 : 0;
    } else if (cfg.patterns[name]) {
      var rows = cfg.patterns[name], h = rows.length, w = Math.max.apply(null, rows.map(function (r) { return r.length; }));
      var ox = name === 'gun' ? 2 : Math.floor((W - w) / 2), oy = name === 'gun' ? 2 : Math.floor((H - h) / 2);
      rows.forEach(function (r, y) {
        for (var x = 0; x < r.length; x++)
          if (r[x] === 'O' && ox + x >= 0 && ox + x < W && oy + y >= 0 && oy + y < H) cells[(oy + y) * W + ox + x] = 1;
      });
    }
    census();
    draw(0);
  }

  // Drawing: the first cell pressed says whether the stroke brings cells to life or clears
  // them; a fast stroke fills the cells between two pointer events too.
  var painting = 0, lastCell = null;
  function cellAt(p) { return { x: Math.max(0, Math.min(W - 1, Math.floor(p.x / CELL))), y: Math.max(0, Math.min(H - 1, Math.floor(p.y / CELL))) }; }
  function paint(c) {
    var from = lastCell || c, steps = Math.max(Math.abs(c.x - from.x), Math.abs(c.y - from.y), 1);
    for (var s = 0; s <= steps; s++) {
      var x = Math.round(from.x + (c.x - from.x) * s / steps), y = Math.round(from.y + (c.y - from.y) * s / steps), i = y * W + x;
      if (cells[i] !== (painting > 0 ? 1 : 0)) {
        cells[i] = painting > 0 ? 1 : 0;
        age[i] = 0;
        if (!cells[i]) fade[i] = 1;
      }
    }
    lastCell = c;
    census();
    if (!k.playing || k.held) draw(0);
  }
  k.pointer(canvas, {
    down: function (p) {
      keyboard = false;
      var c = cellAt(p);
      painting = cells[c.y * W + c.x] ? -1 : 1;
      lastCell = null;
      paint(c);
    },
    move: function (p, e, pressed) { if (pressed && painting) paint(cellAt(p)); },
    up: function () { painting = 0; lastCell = null; }
  });

  function moveCursor(dx, dy) {
    return function () {
      keyboard = true;
      cursor.x = (cursor.x + dx + W) % W;
      cursor.y = (cursor.y + dy + H) % H;
      if (!k.playing || k.held) draw(0);
    };
  }
  function toggleCursor() {
    keyboard = true;
    var i = cursor.y * W + cursor.x;
    cells[i] = cells[i] ? 0 : 1;
    age[i] = 0;
    census();
    if (!k.playing || k.held) draw(0);
  }
  function stepOnce() { k.pause(); advance(); draw(0); k.status('Generation ' + gen); }
  function scatter() { place('random'); k.status('New random cells'); }
  function wipe() { clear(); draw(0); k.status('Cleared - draw cells with the pointer, or the arrow keys and Enter'); }

  k.keys({
    ArrowUp: moveCursor(0, -1), ArrowDown: moveCursor(0, 1), ArrowLeft: moveCursor(-1, 0), ArrowRight: moveCursor(1, 0),
    Enter: toggleCursor, s: stepOnce, n: stepOnce, c: wipe, r: scatter
  });
  canvas.addEventListener('blur', function () { if (!k.playing) draw(0); });

  k.onPlay = function () { k.status('Running at ' + speed + ' generations a second'); };
  k.onPause = function () { k.status('Paused at generation ' + gen); };

  k.row();
  k.playButton();
  k.button('Step', stepOnce, 'One generation (S)');
  k.button('Random', scatter, 'Scatter random cells (R)');
  k.button('Clear', wipe, 'Clear the grid (C)');
  k.row();
  var names = Object.keys(cfg.patterns), labels = { yours: 'Your pattern', glider: 'Glider', spaceship: 'Spaceship', pulsar: 'Pulsar', gun: 'Glider gun',
    pentadecathlon: 'Pentadecathlon', rpentomino: 'R-pentomino', acorn: 'Acorn', diehard: 'Diehard', random: 'Random', empty: 'Empty' };
  k.select('Pattern', names.concat(['random', 'empty']).map(function (n) { return [n, labels[n] || n]; }), cfg.pattern, function (v) {
    place(v);
    k.status((labels[v] || v) + (v === 'empty' ? ' grid - draw on it' : ' placed'));
  });
  k.slider('Speed', 1, 60, 1, speed, function (v) { speed = v; }, function (v) { return v + '/s'; });
  k.check('Wrap edges', wrap, function (v) { wrap = v; });
  k.theme(function () { draw(0); });

  place(cfg.pattern);
  k.begin('Running - draw on the grid to add cells');
});
