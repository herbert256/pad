PadToys.register('automaton', function (k, cfg) {
  var W = cfg.cells, SCALE = Math.max(1, Math.floor(480 / W)), H = Math.round(W * 2 / 3);
  var rule = cfg.rule, speed = cfg.speed, initial = cfg.initial, acc = 0, made = 0, said = 0;
  var rows = new Uint8Array(W * H), top = 0, count = 0;
  var ctx = k.canvas(W, H, 'Cellular automaton', true), canvas = ctx.canvas;
  canvas.style.width = W * SCALE + 'px';
  var image = ctx.createImageData(W, H), pixels = new Uint32Array(image.data.buffer);

  function pack(c) { return (255 << 24 | c[2] << 16 | c[1] << 8 | c[0]) >>> 0; }

  // The rows are a ring: the newest is written over the oldest once the canvas is full, and
  // drawing starts from the oldest, so the picture scrolls up.
  function row(n) { return ((top + n) % H) * W; }
  function newest() { return row(count - 1); }

  function restart() {
    rows.fill(0);
    top = 0; count = 1; made = 1;
    if (initial === 'random') for (var x = 0; x < W; x++) rows[x] = k.chance(0.5) ? 1 : 0;
    else rows[W >> 1] = 1;
  }

  // The next row: each cell from the three above it, read as a number from 0 to 7 - the bit
  // of that number in the rule is the cell. The row wraps at its ends.
  function step() {
    var from = newest(), to;
    if (count < H) { to = row(count); count++; }
    else { to = top * W; top = (top + 1) % H; }
    for (var x = 0; x < W; x++) {
      var l = rows[from + (x + W - 1) % W], c = rows[from + x], r = rows[from + (x + 1) % W];
      rows[to + x] = (rule >> (l << 2 | c << 1 | r)) & 1;
    }
    made++;
  }

  function draw() {
    var bg = pack(k.rgb('canvas')), on = pack(k.rgb('accent')), last = pack(k.rgb('accent2'));
    for (var n = 0; n < H; n++) {
      var src = n < count ? row(n) : -1, dst = n * W, here = n === count - 1 ? last : on;
      for (var x = 0; x < W; x++) pixels[dst + x] = (src >= 0 && rows[src + x]) ? here : bg;
    }
    ctx.putImageData(image, 0, 0);
    k.info('Rule ' + rule + ' · row ' + made.toLocaleString('en'));
    var now = Date.now();
    if (now - said > 1000) {
      said = now;
      k.describe(canvas, 'Elementary cellular automaton rule ' + rule + ': ' + Math.min(count, H) + ' rows of ' + W + ' cells, the newest at the bottom');
    }
  }

  k.loop(function (dt) {
    acc += dt * speed;
    while (acc >= 1) { step(); acc -= 1; }
    draw();
  }, draw);

  k.pointer(canvas, {
    down: function (p) {
      var x = Math.max(0, Math.min(W - 1, Math.floor(p.x))), at = newest() + x;
      rows[at] = rows[at] ? 0 : 1;
      k.status('Cell ' + (x + 1) + ' of the newest row turned ' + (rows[at] ? 'on' : 'off'));
      if (!k.playing) draw();
    }
  });

  // The eight cases of the rule, from 111 down to 000 as Wolfram draws them: three cells and
  // the cell they make, a button each that flips what they make.
  var cases = [];
  function showRule() {
    cases.forEach(function (b, i) {
      var n = 7 - i, on = (rule >> n) & 1;
      b.setAttribute('aria-pressed', on ? 'true' : 'false');
      b.setAttribute('aria-label', 'Neighbours ' + ((n >> 2) & 1 ? 'on' : 'off') + ', ' + ((n >> 1) & 1 ? 'on' : 'off') + ', ' + (n & 1 ? 'on' : 'off') + ' make a cell ' + (on ? 'on' : 'off'));
    });
    if (ruleSlider) ruleSlider.set(rule);
  }
  function setRule(v, say) {
    rule = v;
    showRule();
    if (say) k.status('Rule ' + rule);
    if (!k.playing) draw();
  }

  function stepOnce() { k.pause(); step(); draw(); }
  k.keys({ s: stepOnce, n: stepOnce, r: function () { restart(); draw(); } });
  k.onPlay = function () { k.status('Growing rule ' + rule + ' - click a case to flip it'); };
  k.onPause = function () { k.status('Paused at row ' + made.toLocaleString('en')); };

  k.row();
  k.playButton();
  k.button('Step', stepOnce, 'One row (S)');
  k.button('Restart', function () { restart(); draw(); k.status('Started again from ' + (initial === 'random' ? 'a random row' : 'one cell')); }, 'Start again (R)');
  k.select('Start', [['single', 'One cell'], ['random', 'Random row']], initial, function (v) { initial = v; restart(); draw(); });
  k.row();
  var ruleSlider = null, strip = k.place(k.el('div', 'pta-rule'));
  strip.setAttribute('role', 'group');
  strip.setAttribute('aria-label', 'The eight cases of the rule');
  for (var i = 0; i < 8; i++) {
    var b = k.el('button', 'pta-case'), n = 7 - i;
    b.type = 'button';
    for (var j = 2; j >= 0; j--) b.appendChild(k.el('span', 'pta-cell' + ((n >> j) & 1 ? ' is-on' : '')));
    b.appendChild(k.el('span', 'pta-out'));
    b.addEventListener('click', (function (n) { return function () { setRule(rule ^ (1 << n), true); }; })(n));
    strip.appendChild(b);
    cases.push(b);
  }
  k.row();
  ruleSlider = k.slider('Rule', 0, 255, 1, rule, function (v) { setRule(v, false); });
  k.select('Famous', [['', 'Pick one'], ['30', '30 - chaos'], ['90', '90 - Sierpinski'], ['110', '110 - universal'], ['184', '184 - traffic'],
                      ['45', '45'], ['73', '73'], ['150', '150 - XOR'], ['105', '105'], ['22', '22']], '', function (v) {
    if (v) { setRule(+v, true); restart(); draw(); }
  });
  k.slider('Speed', 1, 120, 1, speed, function (v) { speed = v; }, function (v) { return v + '/s'; });
  showRule();
  k.theme(draw);

  restart();
  k.begin();
});
