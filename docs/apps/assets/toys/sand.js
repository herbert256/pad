PadToys.register('sand', function (k, cfg) {
  var W = cfg.width, H = cfg.height, SCALE = Math.max(2, Math.min(6, Math.floor(640 / W), Math.floor(440 / H)));
  var EMPTY = 0, SAND = 1, WATER = 2, WALL = 3, WOOD = 4, FIRE = 5, PLANT = 6, SMOKE = 7;
  var MATS = [['sand', SAND, 'Sand'], ['water', WATER, 'Water'], ['wall', WALL, 'Wall'], ['wood', WOOD, 'Wood'], ['fire', FIRE, 'Fire'], ['plant', PLANT, 'Plant'], ['erase', EMPTY, 'Erase']];
  var kind = new Uint8Array(W * H), tone = new Uint8Array(W * H), life = new Uint8Array(W * H), stamp = new Uint8Array(W * H), frame = 1;
  var brush = cfg.brush, current = MATS.filter(function (m) { return m[0] === cfg.material; })[0][1], said = 0;
  // Every chance a grain takes comes from the toy's generator, so a seed= gives the same
  // scene and the same first frames.
  var rnd = k.rand;
  var ctx = k.canvas(W, H, 'A box of falling sand', true), canvas = ctx.canvas;
  canvas.style.width = W * SCALE + 'px';
  var image = ctx.createImageData(W, H), pixels = new Uint32Array(image.data.buffer);

  function put(i, m) {
    kind[i] = m;
    tone[i] = Math.floor(rnd() * 255);
    life[i] = m === FIRE ? 30 + k.int(50) : m === SMOKE ? 40 + k.int(40) : 0;
  }
  function move(i, j) {
    var m = kind[j], t = tone[j], l = life[j];
    kind[j] = kind[i]; tone[j] = tone[i]; life[j] = life[i]; stamp[j] = frame;
    kind[i] = m; tone[i] = t; life[i] = l; stamp[i] = frame;
  }
  function at(x, y) { return (x < 0 || y < 0 || x >= W || y >= H) ? -1 : y * W + x; }
  function is(x, y, m) { var i = at(x, y); return i >= 0 && kind[i] === m; }
  function free(x, y) { var i = at(x, y); return i >= 0 && (kind[i] === EMPTY || kind[i] === SMOKE); }

  // The grains that fall are moved bottom row first, the ones that rise top row first, each
  // row in a direction picked anew, so nothing drifts to one side; a grain moves once a frame.
  function step() {
    frame = frame === 255 ? 1 : frame + 1;
    var x, y, row, dir, i;
    for (y = H - 1; y >= 0; y--) {
      dir = rnd() < 0.5;
      for (row = 0; row < W; row++) {
        x = dir ? row : W - 1 - row;
        i = y * W + x;
        if (stamp[i] === frame) continue;
        var m = kind[i];
        if (m === SAND) fallSand(x, y, i);
        else if (m === WATER) fallWater(x, y, i);
        else if (m === PLANT) grow(x, y);
      }
    }
    for (y = 0; y < H; y++) {
      dir = rnd() < 0.5;
      for (row = 0; row < W; row++) {
        x = dir ? row : W - 1 - row;
        i = y * W + x;
        if (stamp[i] === frame) continue;
        if (kind[i] === FIRE) burn(x, y, i);
        else if (kind[i] === SMOKE) rise(x, y, i);
      }
    }
  }
  function fallSand(x, y, i) {
    var below = at(x, y + 1);
    if (below >= 0 && (kind[below] === EMPTY || kind[below] === SMOKE || (kind[below] === WATER && rnd() < 0.6))) return move(i, below);
    var d = rnd() < 0.5 ? 1 : -1;
    if (free(x + d, y + 1) || is(x + d, y + 1, WATER)) return move(i, at(x + d, y + 1));
    if (free(x - d, y + 1) || is(x - d, y + 1, WATER)) return move(i, at(x - d, y + 1));
  }
  function fallWater(x, y, i) {
    if (free(x, y + 1)) return move(i, at(x, y + 1));
    var d = rnd() < 0.5 ? 1 : -1;
    if (free(x + d, y + 1)) return move(i, at(x + d, y + 1));
    if (free(x - d, y + 1)) return move(i, at(x - d, y + 1));
    // Water that cannot fall runs sideways, a few grains at a time, until it finds its level.
    for (var s = 1; s <= 3; s++) { if (!free(x + d * s, y)) break; if (s === 3 || !free(x + d * (s + 1), y)) return move(i, at(x + d * s, y)); }
    if (free(x - d, y)) return move(i, at(x - d, y));
  }
  function grow(x, y) {
    if (rnd() > 0.04) return;
    var d = [[1, 0], [-1, 0], [0, 1], [0, -1]][Math.floor(rnd() * 4)], j = at(x + d[0], y + d[1]);
    if (j >= 0 && kind[j] === WATER) { put(j, PLANT); stamp[j] = frame; }
  }
  function burn(x, y, i) {
    for (var n = 0; n < 4; n++) {
      var d = [[1, 0], [-1, 0], [0, 1], [0, -1]][n], j = at(x + d[0], y + d[1]);
      if (j < 0) continue;
      if ((kind[j] === WOOD && rnd() < 0.05) || (kind[j] === PLANT && rnd() < 0.15)) { put(j, FIRE); stamp[j] = frame; }
      else if (kind[j] === WATER && rnd() < 0.3) { put(i, SMOKE); if (rnd() < 0.5) put(j, EMPTY); return; }
    }
    if (--life[i] <= 0) { put(i, rnd() < 0.5 ? SMOKE : EMPTY); return; }
    var side = Math.floor(rnd() * 3) - 1;
    if (rnd() < 0.6 && kind[at(x + side, y - 1)] === EMPTY && y > 0) move(i, at(x + side, y - 1));
  }
  function rise(x, y, i) {
    if (--life[i] <= 0 || y === 0) { put(i, EMPTY); return; }
    var side = Math.floor(rnd() * 3) - 1, j = at(x + side, y - 1);
    if (j >= 0 && kind[j] === EMPTY && rnd() < 0.7) move(i, j);
  }

  // The colours of the grains: natural ones that read on a light and a dark canvas, the
  // walls in the page's muted ink; every grain a shade of its own.
  // bg, the canvas colour, is what smoke fades into.
  var table, bg;
  function colours() {
    var wall = k.rgb('muted'), dark = k.dark();
    bg = k.rgb('canvas');
    table = [bg, [224, 190, 118], dark ? [70, 140, 235] : [52, 120, 220], wall, [128, 82, 46], [255, 140, 30], [56, 168, 82], dark ? [120, 120, 125] : [175, 175, 180]];
  }
  function draw() {
    for (var i = 0; i < kind.length; i++) {
      var m = kind[i], c = table[m];
      if (m === EMPTY) { pixels[i] = (255 << 24 | c[2] << 16 | c[1] << 8 | c[0]) >>> 0; continue; }
      var v = (tone[i] / 255 - 0.5) * (m === WATER ? 0.12 : 0.22), r = c[0], g = c[1], b = c[2];
      if (m === FIRE) { var heat = life[i] / 80; g = 60 + heat * 170 + v * 100; r = 255; b = 20 + heat * 40; }
      if (m === SMOKE) { var f = Math.min(1, life[i] / 60) * 0.6; r = bg[0] + (r - bg[0]) * f; g = bg[1] + (g - bg[1]) * f; b = bg[2] + (b - bg[2]) * f; }
      r = Math.max(0, Math.min(255, r * (1 + v))); g = Math.max(0, Math.min(255, g * (1 + v))); b = Math.max(0, Math.min(255, b * (1 + v)));
      pixels[i] = (255 << 24 | b << 16 | g << 8 | r) >>> 0;
    }
    ctx.putImageData(image, 0, 0);
    var now = Date.now();
    if (now - said > 1000) {
      said = now;
      var counts = [0, 0, 0, 0, 0, 0, 0, 0];
      for (var j = 0; j < kind.length; j++) counts[kind[j]]++;
      var parts = MATS.filter(function (m) { return m[1] && counts[m[1]]; }).map(function (m) { return m[2].toLowerCase() + ' ' + counts[m[1]].toLocaleString('en'); });
      k.info(parts.length ? 'Grains: ' + parts.join(' · ') : 'An empty box - paint something');
      k.describe(canvas, 'A box of falling sand, ' + W + ' by ' + H + ' grains' + (parts.length ? ': ' + parts.join(', ') : ', empty'));
    }
  }

  k.loop(function () { step(); draw(); }, draw);

  // Painting: a round brush, sand and water sprinkled - half the spots of the brush - so
  // they pour as grains; the rest laid solid. The right button erases.
  var last = null;
  function paint(p, m) {
    var cx = Math.floor(p.x), cy = Math.floor(p.y), from = last || { x: cx, y: cy }, steps = Math.max(1, Math.abs(cx - from.x), Math.abs(cy - from.y));
    for (var s = 0; s <= steps; s++) {
      var mx = Math.round(from.x + (cx - from.x) * s / steps), my = Math.round(from.y + (cy - from.y) * s / steps);
      for (var dy = -brush + 1; dy < brush; dy++) for (var dx = -brush + 1; dx < brush; dx++) {
        if (dx * dx + dy * dy >= brush * brush) continue;
        var i = at(mx + dx, my + dy);
        if (i < 0) continue;
        if ((m === SAND || m === WATER || m === FIRE) && rnd() < 0.5) continue;
        if (m === EMPTY || kind[i] === EMPTY || kind[i] === SMOKE || m === WALL) put(i, m);
      }
    }
    last = { x: cx, y: cy };
    if (!k.playing || k.held) draw();
  }
  var using = current;
  k.pointer(canvas, {
    down: function (p, e) { last = null; using = e.button === 2 ? EMPTY : current; paint(p, using); },
    move: function (p, e, pressed) { if (pressed) paint(p, using); },
    up: function () { last = null; }
  });

  function scene(name) {
    kind.fill(0); life.fill(0);
    var x, y;
    if (name === 'hourglass') {
      var mid = W >> 1, neck = Math.max(2, Math.round(W / 60));
      for (y = 4; y < H - 4; y++) {
        var t = Math.abs(y - H / 2) / (H / 2 - 4), half = Math.round(neck + t * (H / 2 - 8) * 0.9);
        put(at(mid - half - 1, y), WALL); put(at(mid + half + 1, y), WALL);
        put(at(mid - half - 2, y), WALL); put(at(mid + half + 2, y), WALL);
        if (y > 8 && y < H / 2 - 4) for (x = mid - half; x <= mid + half; x++) if (rnd() < 0.9) put(at(x, y), SAND);
      }
      for (x = mid - Math.round(H / 2); x <= mid + Math.round(H / 2); x++) { put(at(x, 3), WALL); put(at(x, H - 4), WALL); }
    } else if (name === 'garden') {
      for (y = H - 12; y < H; y++) for (x = 0; x < W; x++) put(at(x, y), SAND);
      for (y = H - 30; y < H - 12; y++) for (x = Math.round(W * 0.55); x < Math.round(W * 0.85); x++) put(at(x, y), WATER);
      for (y = H - 30; y < H - 12; y++) { put(at(Math.round(W * 0.55) - 1, y), WALL); put(at(Math.round(W * 0.85), y), WALL); }
      for (x = Math.round(W * 0.1); x < Math.round(W * 0.4); x++) for (y = H - 16; y < H - 12; y++) put(at(x, y), WOOD);
      for (x = Math.round(W * 0.6); x < Math.round(W * 0.62); x++) put(at(x, H - 13), PLANT);
      for (x = Math.round(W * 0.12); x < Math.round(W * 0.15); x++) put(at(x, H - 17), FIRE);
    }
  }

  var buttons = [];
  function pick(m) {
    current = m;
    buttons.forEach(function (b) { b.setAttribute('aria-pressed', b.mat === m ? 'true' : 'false'); });
  }
  var keys = { c: function () { scene('empty'); draw(); k.status('Emptied'); } };
  k.onPlay = function () { k.status('Running - paint with the pointer, right button erases'); };
  k.onPause = function () { k.status('Paused - you can still paint'); };

  k.row();
  k.playButton();
  k.button('Clear', keys.c, 'Empty the box (C)');
  k.select('Scene', [['hourglass', 'Hourglass'], ['garden', 'Garden'], ['empty', 'Empty']], cfg.scene, function (v) { scene(v); draw(); k.status('The ' + v + ' scene'); });
  k.slider('Brush', 1, 12, 1, brush, function (v) { brush = v; });
  k.row();
  var group = k.place(k.el('div', 'pts-mats'));
  group.setAttribute('role', 'group');
  group.setAttribute('aria-label', 'Material to paint with');
  MATS.forEach(function (m, n) {
    var b = k.el('button', 'pt-button pts-mat pts-' + m[0], m[2]);
    b.type = 'button';
    b.mat = m[1];
    b.title = m[2] + ' (' + (n + 1) + ')';
    b.addEventListener('click', function () { pick(m[1]); k.status(m[2] + (m[1] ? ' - paint with the pointer' : ' - rub out with the pointer')); });
    group.appendChild(b);
    buttons.push(b);
    keys[String(n + 1)] = function () { pick(m[1]); k.status(m[2]); };
  });
  k.keys(keys);
  k.theme(colours);

  colours();
  pick(current);
  scene(cfg.scene);
  k.begin();
});
