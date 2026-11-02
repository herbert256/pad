PadToys.register('spirograph', function (k, cfg) {
  var S = 480, C = S / 2, COLOURS = ['accent', 'accent2', 'accent3', 'good', 'bad', 'mark'];
  var ring = cfg.ring, wheel = cfg.wheel, pen = cfg.pen, outside = cfg.outside, speed = 3, said = 0;
  var layers = [], layer = null;
  var ctx = k.canvas(S, S, 'A spirograph drawing', false), canvas = ctx.canvas;
  var paper = document.createElement('canvas'), pctx = paper.getContext('2d');
  paper.width = canvas.width; paper.height = canvas.height;
  pctx.setTransform(canvas.ratio, 0, 0, canvas.ratio, 0, 0);

  function gcd(a, b) { while (b) { var t = b; b = a % b; a = t; } return a; }

  // The sizes in pixels: the teeth stand for the radius, so a wheel of half the ring's teeth
  // is half its size; the whole figure fits the canvas, outside the ring as well.
  function geometry(l) {
    var scale = (S / 2 - 12) / (l.outside ? l.ring + 2 * l.wheel : l.ring);
    var turns = l.wheel / gcd(l.ring, l.wheel);
    return { R: l.ring * scale, r: l.wheel * scale, d: l.pen / 100 * l.wheel * scale, end: Math.PI * 2 * turns, turns: turns };
  }
  function at(l, g, th) {
    var sgn = l.outside ? 1 : -1, cr = l.outside ? g.R + g.r : g.R - g.r, phi = sgn * th * cr / g.r;
    var cx = C + cr * Math.cos(th), cy = C + cr * Math.sin(th);
    return { cx: cx, cy: cy, x: cx + g.d * Math.cos(phi), y: cy + g.d * Math.sin(phi), phi: phi };
  }

  function newLayer() {
    layer = { ring: ring, wheel: wheel, pen: pen, outside: outside, colour: COLOURS[layers.length % COLOURS.length], th: 0 };
    layers.push(layer);
  }
  // A layer's line from one angle to the next, in steps small enough to stay round.
  function trace(l, from, to) {
    var g = geometry(l), step = 0.02 * Math.min(1, l.wheel / 30), p = at(l, g, from);
    pctx.strokeStyle = k.alpha(l.colour, 0.9);
    pctx.lineWidth = 1.1;
    pctx.lineJoin = 'round';
    pctx.beginPath();
    pctx.moveTo(p.x, p.y);
    for (var th = from + step; th < to + step; th += step) { p = at(l, g, Math.min(th, to)); pctx.lineTo(p.x, p.y); }
    pctx.stroke();
  }
  function repaper() {
    pctx.fillStyle = k.color('canvas');
    pctx.fillRect(0, 0, S, S);
    layers.forEach(function (l) { if (l.th > 0) trace(l, 0, l.th); });
  }

  function draw() {
    ctx.setTransform(1, 0, 0, 1, 0, 0);
    ctx.drawImage(paper, 0, 0);
    ctx.setTransform(canvas.ratio, 0, 0, canvas.ratio, 0, 0);
    var g = geometry(layer), p = at(layer, g, layer.th);
    if (layer.th < g.end) {
      // The ring, the wheel with its teeth suggested by a dashed rim, and the pen.
      ctx.strokeStyle = k.alpha('muted', 0.55);
      ctx.lineWidth = 1;
      ctx.beginPath(); ctx.arc(C, C, g.R, 0, Math.PI * 2); ctx.stroke();
      ctx.setLineDash([3, 3]);
      ctx.beginPath(); ctx.arc(p.cx, p.cy, g.r, 0, Math.PI * 2); ctx.stroke();
      ctx.setLineDash([]);
      ctx.beginPath(); ctx.moveTo(p.cx, p.cy); ctx.lineTo(p.x, p.y); ctx.stroke();
      ctx.fillStyle = k.color(layer.colour);
      ctx.beginPath(); ctx.arc(p.x, p.y, 4, 0, Math.PI * 2); ctx.fill();
    }
    var turns = layer.th / (Math.PI * 2), all = g.turns;
    k.info('Ring ' + layer.ring + ' · wheel ' + layer.wheel + ' · pen ' + layer.pen + '%' + (layer.outside ? ' · outside' : '') + ' · turn ' + Math.min(all, Math.floor(turns) + 1) + ' of ' + all + ' · ' + layers.length + (layers.length === 1 ? ' layer' : ' layers'));
    var now = Date.now();
    if (now - said > 1000) {
      said = now;
      k.describe(canvas, 'A spirograph drawing of ' + layers.length + (layers.length === 1 ? ' layer' : ' layers') + ': a wheel of ' + layer.wheel + ' teeth rolling ' + (layer.outside ? 'outside' : 'inside') + ' a ring of ' + layer.ring + ', ' + Math.round(Math.min(1, layer.th / g.end) * 100) + ' percent drawn');
    }
  }

  function roll(by) {
    var g = geometry(layer), to = Math.min(g.end, layer.th + by);
    if (to <= layer.th) return;
    trace(layer, layer.th, to);
    layer.th = to;
    if (to >= g.end) {
      k.pause();
      k.status('The teeth met again after ' + g.turns + (g.turns === 1 ? ' turn' : ' turns') + ' - New layer draws over it');
      if (k.playBtn) k.playBtn.textContent = 'Again';
    }
  }

  k.loop(function (dt) { roll(dt * speed * 1.6); draw(); }, draw);

  // Dragging round the middle rolls the wheel by hand, as far as the pointer goes round.
  var hand = null;
  k.pointer(canvas, {
    down: function (p) { hand = Math.atan2(p.y - C, p.x - C); k.pause(); },
    move: function (p, e, pressed) {
      if (!pressed || hand === null) return;
      var a = Math.atan2(p.y - C, p.x - C), d = a - hand;
      if (d > Math.PI) d -= Math.PI * 2;
      if (d < -Math.PI) d += Math.PI * 2;
      hand = a;
      if (d > 0) { roll(d * 3); draw(); }
    },
    up: function () { hand = null; k.status('Rolled by hand - Play rolls on'); }
  });

  // A change of the wheel, the hole or the ring starts the layer again, unless it is done -
  // then the change goes into a new layer over it.
  function changed() {
    var g = geometry(layer);
    if (layer.th >= g.end) newLayer();
    else { layers.pop(); newLayer(); repaper(); }
    if (!k.playing) draw();
  }
  function fresh() { layers = []; newLayer(); repaper(); draw(); k.status('A clean sheet'); }
  function another() { newLayer(); draw(); k.status('A new layer in ' + layer.colour); if (!k.playing) k.play(); }

  k.keys({ n: another, c: fresh });
  k.onPlay = function () {
    if (still) { still = false; layers = []; newLayer(); repaper(); }
    else if (layer.th >= geometry(layer).end) newLayer();
    if (k.playBtn) k.playBtn.textContent = 'Pause';
    k.status('Rolling - drag round the middle to roll it by hand');
  };
  k.onPause = function () { if (layer.th < geometry(layer).end) k.status('Paused'); };

  k.row();
  k.playButton();
  k.button('New layer', another, 'Draw over it in the next colour (N)');
  k.button('Clear', fresh, 'A clean sheet (C)');
  k.check('Outside', outside, function (v) { outside = v; if (!v && wheel >= ring) { wheel = Math.floor(ring / 2); wheelSlider.set(wheel); } changed(); });
  k.slider('Speed', 1, 10, 1, speed, function (v) { speed = v; });
  k.row();
  k.select('Ring', [40, 60, 84, 96, 105, 120, 144, 150].concat([ring]).filter(function (v, i, a) { return a.indexOf(v) === i; }).sort(function (a, b) { return a - b; }).map(function (v) { return [String(v), v + ' teeth']; }),
           String(ring), function (v) { ring = +v; if (!outside && wheel >= ring) { wheel = ring - 1; wheelSlider.set(wheel); } changed(); });
  var wheelSlider = k.slider('Wheel', 10, 150, 1, wheel, function (v) { if (!outside && v >= ring) { v = ring - 1; wheelSlider.set(v); } wheel = v; changed(); });
  k.slider('Pen', 5, 100, 1, pen, function (v) { pen = v; changed(); }, function (v) { return v + '%'; });
  k.theme(function () { repaper(); draw(); });

  // Waiting, the toy shows the figure it is about to draw, finished; Play draws it anew.
  var still = false;
  k.still(function () { var g = geometry(layer); trace(layer, 0, g.end); layer.th = g.end; still = true; draw(); });

  newLayer();
  repaper();
  k.begin();
});
