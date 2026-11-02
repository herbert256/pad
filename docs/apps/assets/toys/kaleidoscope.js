PadToys.register('kaleidoscope', function (k, cfg) {
  var S = 520, C = S / 2, KEEP = 30000;
  var mirrors = cfg.mirrors, brush = cfg.brush, colour = cfg.colour, reflect = true, fade = true, hue = k.range(0, 360), washed = 0;
  var strokes = [], last = null, idle = 2, wander = { t: k.range(0, 100), x: 0, y: 0, a: k.range(1, 3), b: k.range(1, 3) }, said = 0;
  var ctx = k.canvas(S, S, 'A kaleidoscope', false), canvas = ctx.canvas;

  function ink() {
    if (colour === 'ink') return k.color('ink');
    if (colour === 'palette') return k.color(['accent', 'accent2', 'accent3', 'good', 'mark'][Math.floor(hue / 72) % 5]);
    return 'hsl(' + Math.round(hue) + ',85%,' + (k.dark() ? 62 : 48) + '%)';
  }

  // A stroke drawn in one place is drawn in every wedge round the middle - and, with the
  // mirrors reflecting, flipped in each as well: the symmetry of the tube.
  function segment(s) {
    ctx.strokeStyle = s.c;
    ctx.lineWidth = s.w;
    ctx.lineCap = 'round';
    ctx.beginPath();
    for (var i = 0; i < s.n; i++) {
      var a = i * Math.PI * 2 / s.n, cos = Math.cos(a), sin = Math.sin(a);
      ctx.moveTo(C + s.x0 * cos - s.y0 * sin, C + s.x0 * sin + s.y0 * cos);
      ctx.lineTo(C + s.x1 * cos - s.y1 * sin, C + s.x1 * sin + s.y1 * cos);
      if (s.r) {
        ctx.moveTo(C + s.x0 * cos + s.y0 * sin, C + s.x0 * sin - s.y0 * cos);
        ctx.lineTo(C + s.x1 * cos + s.y1 * sin, C + s.x1 * sin - s.y1 * cos);
      }
    }
    ctx.stroke();
  }
  function line(x0, y0, x1, y1) {
    var s = { x0: x0 - C, y0: y0 - C, x1: x1 - C, y1: y1 - C, c: ink(), w: brush, n: mirrors, r: reflect };
    segment(s);
    strokes.push(s);
    if (strokes.length > KEEP) strokes.splice(0, strokes.length - KEEP);
    hue = (hue + Math.hypot(x1 - x0, y1 - y0) * 0.4) % 360;
  }
  function paper() { ctx.fillStyle = k.color('canvas'); ctx.fillRect(0, 0, S, S); }
  function repaint() { paper(); strokes.forEach(segment); }

  // The wandering pen: two slow sums of waves for its place, so it loops and curls without
  // ever quite repeating.
  function roam(dt) {
    wander.t += dt * 0.6;
    var t = wander.t, x = C + (Math.sin(t * wander.a) * 0.6 + Math.sin(t * 2.3 + 1) * 0.3) * C * 0.9, y = C + (Math.cos(t * wander.b) * 0.6 + Math.sin(t * 1.7) * 0.3) * C * 0.9;
    if (wander.x) line(wander.x, wander.y, x, y);
    wander.x = x; wander.y = y;
  }

  k.loop(function (dt) {
    if (fade) {
      washed += dt * 0.35;
      if (washed >= 0.06) { ctx.fillStyle = k.alpha('canvas', washed.toFixed(3)); ctx.fillRect(0, 0, S, S); washed = 0; strokes = strokes.slice(-4000); }
    }
    idle += dt;
    if (idle > 1.5 && !last) roam(dt); else wander.x = 0;
    var now = Date.now();
    if (now - said > 1500) {
      said = now;
      k.info(mirrors + ' mirrors' + (reflect ? ', reflecting' : '') + ' · brush ' + brush + ' · ' + { rainbow: 'rainbow', ink: 'ink', palette: 'the palette' }[colour] + (idle > 1.5 ? ' · the pen wanders' : ''));
      k.describe(canvas, 'A kaleidoscope of ' + mirrors + ' mirrored wedges, ' + (idle > 1.5 ? 'a wandering pen drawing a pattern' : 'a pattern drawn by hand'));
    }
  }, function () { k.info(mirrors + ' mirrors' + (reflect ? ', reflecting' : '') + ' · brush ' + brush); });

  k.pointer(canvas, {
    down: function (p) { last = p; idle = 0; line(p.x, p.y, p.x + 0.01, p.y); },
    move: function (p, e, pressed) { if (!pressed || !last) return; idle = 0; line(last.x, last.y, p.x, p.y); last = p; },
    up: function () { last = null; idle = 0; }
  });

  function save() {
    var a = document.createElement('a');
    a.href = canvas.toDataURL('image/png');
    a.download = 'kaleidoscope.png';
    document.body.appendChild(a);
    a.click();
    a.remove();
    k.status('Saved as kaleidoscope.png');
  }
  function clear() { strokes = []; paper(); k.status('A clean glass'); }

  k.keys({ c: clear });
  k.onPlay = function () { k.status('Draw on it - when you stop, a pen of its own goes on'); };
  k.onPause = function () { k.status('Paused - drawing still works'); };

  k.row();
  k.playButton();
  k.button('Clear', clear, 'A clean glass (C)');
  k.button('Save', save, 'Keep the picture as an image');
  k.select('Colour', [['rainbow', 'Rainbow'], ['palette', 'Palette'], ['ink', 'Ink']], colour, function (v) { colour = v; });
  k.row();
  k.slider('Mirrors', 3, 24, 1, mirrors, function (v) { mirrors = v; });
  k.slider('Brush', 1, 20, 1, brush, function (v) { brush = v; });
  k.check('Reflect', reflect, function (v) { reflect = v; });
  k.check('Fade', fade, function (v) { fade = v; });
  k.theme(repaint);

  paper();
  k.still(function () { for (var i = 0; i < 360; i++) roam(1 / 60); });
  k.begin();
});
