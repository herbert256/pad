PadToys.register('lsystem', function (k, cfg) {
  var S = 520, LIMIT = 400000, NAMES = { yours: 'Your system', plant: 'Fern', bush: 'Bush', weed: 'Weed', koch: 'Koch snowflake', island: 'Koch island',
    dragon: 'Dragon curve', levy: 'Lévy C curve', sierpinski: 'Sierpinski arrowhead', hilbert: 'Hilbert curve', gosper: 'Gosper curve', crystal: 'Crystal' };
  var name = cfg.system, sys = cfg.systems[name], iterations = cfg.iterations, angle = cfg.angle, wind = false, windT = 0;
  var segs = new Float32Array(0), count = 0, shown = 0, maxDepth = 1, branching = false, word = '', said = 0;
  var ctx = k.canvas(S, S, 'An L-system', false), canvas = ctx.canvas;

  // The word after so many rewritings - every letter by its rule at once - cut short when it
  // grows past what draws in a moment.
  function rewrite() {
    var w = sys.axiom, n = 0;
    for (; n < iterations; n++) {
      var out = '';
      for (var i = 0; i < w.length; i++) { var r = sys.rules[w[i]]; out += r !== undefined ? r : w[i]; if (out.length > LIMIT) break; }
      if (out.length > LIMIT) break;
      w = out;
    }
    if (n < iterations) k.status('Grown ' + n + ' times - one more would be too large to draw');
    word = w;
    branching = w.indexOf('[') >= 0;
  }

  // The turtle walks the word: F and G a line, f a step, + and - a turn - plus a sway when the
  // wind blows, more the further out a branch is - [ and ] keep and take back a place, |
  // turns about. The lines are fitted to the canvas afterwards.
  function walk(sway) {
    var x = 0, y = 0, a = branching ? -90 : 0, stack = [], depth = 0, list = [], d2r = Math.PI / 180;
    maxDepth = 1;
    for (var i = 0; i < word.length; i++) {
      var c = word[i];
      if (c === 'F' || c === 'G' || c === 'f') {
        var nx = x + Math.cos(a * d2r), ny = y + Math.sin(a * d2r);
        if (c !== 'f') list.push(x, y, nx, ny, depth);
        x = nx; y = ny;
      } else if (c === '+') a += angle + (sway ? sway(depth) : 0);
      else if (c === '-') a -= angle - (sway ? sway(depth) : 0);
      else if (c === '|') a += 180;
      else if (c === '[') { stack.push(x, y, a, depth); depth++; if (depth > maxDepth) maxDepth = depth; }
      else if (c === ']' && stack.length) { depth = stack.pop(); a = stack.pop(); y = stack.pop(); x = stack.pop(); }
    }
    segs = new Float32Array(list);
    count = list.length / 5;
  }
  var fitted = null;
  function fit() {
    {
      var x0 = Infinity, y0 = Infinity, x1 = -Infinity, y1 = -Infinity;
      for (var i = 0; i < count; i++) {
        var o = i * 5;
        x0 = Math.min(x0, segs[o], segs[o + 2]); x1 = Math.max(x1, segs[o], segs[o + 2]);
        y0 = Math.min(y0, segs[o + 1], segs[o + 3]); y1 = Math.max(y1, segs[o + 1], segs[o + 3]);
      }
      var s = (S - 30) / Math.max(x1 - x0, y1 - y0, 1e-9);
      fitted = { s: s, ox: (S - (x1 - x0) * s) / 2 - x0 * s, oy: branching ? S - 12 - y1 * s : (S - (y1 - y0) * s) / 2 - y0 * s };
    }
  }

  function colourOf(i) {
    // A plant goes from bark - the orange greyed by the muted ink - at the stem to green at
    // the tips; a curve runs through the palette from its start to its end.
    var o = i * 5, f = branching ? Math.sqrt(segs[o + 4] / maxDepth) : i / Math.max(1, count), stops;
    if (branching) {
      var o2 = k.rgb('accent2'), ink = k.rgb('muted'), bark = [o2[0] * 0.6 + ink[0] * 0.4, o2[1] * 0.6 + ink[1] * 0.4, o2[2] * 0.6 + ink[2] * 0.4];
      stops = [bark, k.rgb('mark'), k.rgb('good')];
    } else stops = [k.rgb('accent'), k.rgb('accent3'), k.rgb('accent2')];
    var g = Math.min(0.999, f) * (stops.length - 1), j = Math.floor(g), t = g - j, a = stops[j], b = stops[j + 1];
    return 'rgb(' + Math.round(a[0] + (b[0] - a[0]) * t) + ',' + Math.round(a[1] + (b[1] - a[1]) * t) + ',' + Math.round(a[2] + (b[2] - a[2]) * t) + ')';
  }
  // The lines from one to another, in runs of one colour: a few dozen strokes, not thousands.
  function strokes(from, to) {
    var f = fitted, run = 48, step = Math.max(1, Math.ceil((to - from) / run));
    ctx.lineCap = 'round';
    ctx.lineWidth = branching ? 1.2 : Math.max(0.6, Math.min(2, 900 / Math.sqrt(count + 1) / 30));
    if (branching) {
      // A branching plant goes by depth, so a stroke per depth keeps its colours right.
      for (var d = 0; d <= maxDepth; d++) {
        var any = false;
        ctx.beginPath();
        for (var i = from; i < to; i++) {
          var o = i * 5;
          if (segs[o + 4] !== d) continue;
          if (!any) { ctx.strokeStyle = colourOf(i); any = true; }
          ctx.moveTo(f.ox + segs[o] * f.s, f.oy + segs[o + 1] * f.s);
          ctx.lineTo(f.ox + segs[o + 2] * f.s, f.oy + segs[o + 3] * f.s);
        }
        ctx.lineWidth = Math.max(0.7, 2.6 - d * 0.35);
        if (any) ctx.stroke();
      }
      return;
    }
    for (var s = from; s < to; s += step) {
      var e = Math.min(to, s + step);
      ctx.strokeStyle = colourOf(s);
      ctx.beginPath();
      ctx.moveTo(f.ox + segs[s * 5] * f.s, f.oy + segs[s * 5 + 1] * f.s);
      for (var j = s; j < e; j++) ctx.lineTo(f.ox + segs[j * 5 + 2] * f.s, f.oy + segs[j * 5 + 3] * f.s);
      ctx.stroke();
    }
  }
  function draw() {
    ctx.fillStyle = k.color('canvas');
    ctx.fillRect(0, 0, S, S);
    strokes(0, Math.min(shown, count));
    describe();
  }
  function describe() {
    k.info((NAMES[name] || name) + ' · ' + iterations + (iterations === 1 ? ' iteration' : ' iterations') + ' · angle ' + (+angle.toFixed(1)) + '° · ' + count.toLocaleString('en') + ' lines' + (shown < count ? ' · ' + Math.round(shown / count * 100) + '% drawn' : ''));
    var now = Date.now();
    if (now - said > 800) {
      said = now;
      k.describe(canvas, 'The L-system ' + (NAMES[name] || name) + ' after ' + iterations + ' iterations: ' + count + ' lines' + (shown < count ? ', ' + Math.round(shown / count * 100) + ' percent drawn' : ''));
    }
  }

  function grow() { rewrite(); walk(null); fit(); shown = 0; ctx.fillStyle = k.color('canvas'); ctx.fillRect(0, 0, S, S); }

  // Growing draws the lines in about three seconds, only the new ones each frame; once grown,
  // the wind - when it blows - sways the branches and draws all of them again.
  k.loop(function (dt) {
    if (shown < count) {
      var next = Math.min(count, shown + Math.max(1, Math.ceil(count / 3.2 * dt)));
      strokes(shown, next);
      shown = next;
      describe();
      if (shown >= count && !wind) { k.pause(); k.status('Grown - ' + count.toLocaleString('en') + ' lines; Play grows it again'); if (k.playBtn) k.playBtn.textContent = 'Grow'; }
      return;
    }
    if (!wind || !branching) { k.pause(); k.status('Grown - ' + count.toLocaleString('en') + ' lines; Play grows it again'); if (k.playBtn) k.playBtn.textContent = 'Grow'; return; }
    {
      windT += dt;
      walk(function (d) { return Math.sin(windT * 1.6 + d * 0.7) * 1.6 * d / maxDepth + Math.sin(windT * 0.7) * 0.8; });
      shown = count;
      draw();
    }
  }, draw);

  // Dragging sideways bends the turn: the whole figure is drawn again at the new angle.
  var dragFrom = null;
  k.pointer(canvas, {
    down: function (p) { dragFrom = { x: p.x, angle: angle }; },
    move: function (p, e, pressed) {
      if (!pressed || !dragFrom) return;
      angle = Math.max(1, Math.min(180, dragFrom.angle + (p.x - dragFrom.x) * 0.1));
      angleSlider.set(angle);
      walk(null);
      fit();
      shown = count;
      draw();
    },
    up: function () { if (dragFrom && angle !== dragFrom.angle) k.status('Angle ' + (+angle.toFixed(1)) + '°'); dragFrom = null; }
  });

  function choose(v) {
    name = v;
    sys = cfg.systems[v];
    iterations = Math.min(sys.iterations, sys.most);
    angle = sys.angle;
    iterSlider.max = sys.most;
    iterSlider.set(iterations);
    angleSlider.set(angle);
    windBox.disabled = !(sys.axiom + Object.keys(sys.rules).map(function (r) { return sys.rules[r]; }).join('')).match(/\[/);
    grow();
    if (!k.playing) k.play();
  }

  k.keys({
    ArrowUp: function () { if (iterations < sys.most) { iterations++; iterSlider.set(iterations); grow(); if (!k.playing) k.play(); } },
    ArrowDown: function () { if (iterations > 1) { iterations--; iterSlider.set(iterations); grow(); if (!k.playing) k.play(); } },
    ArrowLeft: function () { angle = Math.max(1, angle - 1); angleSlider.set(angle); walk(null); fit(); shown = count; draw(); },
    ArrowRight: function () { angle = Math.min(180, angle + 1); angleSlider.set(angle); walk(null); fit(); shown = count; draw(); }
  });
  k.onPlay = function () { if (shown >= count) grow(); if (k.playBtn) k.playBtn.textContent = 'Pause'; k.status('Growing ' + (NAMES[name] || name).toLowerCase()); };
  k.onPause = function () { if (shown < count) k.status('Paused while growing'); };

  k.row();
  k.playButton();
  k.button('Draw at once', function () { if (shown < count) { strokes(shown, count); shown = count; describe(); } }, 'The whole figure now');
  k.select('System', Object.keys(cfg.systems).map(function (n) { return [n, NAMES[n] || n]; }), name, choose);
  var windBox = k.check('Wind', wind, function (v) { wind = v; if (v && !k.playing) k.play(); if (!v) { walk(null); fit(); draw(); } k.status(v ? 'The wind blows' : 'Still'); });
  k.row();
  var iterSlider = k.slider('Iterations', 1, sys.most, 1, iterations, function (v) { iterations = v; grow(); if (!k.playing) k.play(); });
  var angleSlider = k.slider('Angle', 1, 180, 0.5, angle, function (v) { angle = v; walk(null); fit(); shown = count; draw(); }, function (v) { return v + '°'; });
  windBox.disabled = !(sys.axiom + Object.keys(sys.rules).map(function (r) { return sys.rules[r]; }).join('')).match(/\[/);
  k.theme(draw);

  grow();
  k.still(function () { shown = count; draw(); });
  k.begin();
});
