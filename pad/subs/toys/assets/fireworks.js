PadToys.register('fireworks', function (k, cfg) {
  var W = 640, H = 420, MAX = 6000, KINDS = ['peony', 'ring', 'willow', 'palm', 'crossette'];
  var rate = cfg.rate, gravity = cfg.gravity, shell = cfg.shell, wait = 0.4, sent = 0, said = 0;
  var ctx = k.canvas(W, H, 'Fireworks in a night sky', false), canvas = ctx.canvas;
  canvas.style.background = 'var(--pad-toy-night)';

  // The sparks are kept in flat arrays - a few thousand at a time - and a spark that dies
  // gives its place to the next one.
  var px = new Float32Array(MAX), py = new Float32Array(MAX), vx = new Float32Array(MAX), vy = new Float32Array(MAX);
  var life = new Float32Array(MAX), span = new Float32Array(MAX), drag = new Float32Array(MAX), size = new Float32Array(MAX);
  var red = new Uint8Array(MAX), green = new Uint8Array(MAX), blue = new Uint8Array(MAX), role = new Uint8Array(MAX), used = 0;
  var shells = [];

  var HUES = [[255, 70, 70], [255, 160, 40], [255, 230, 90], [90, 230, 120], [80, 180, 255], [190, 110, 255], [255, 110, 200], [255, 255, 255]];

  function spark(x, y, sx, sy, t, d, c, s, r) {
    if (used >= MAX) return;
    var i = used++;
    px[i] = x; py[i] = y; vx[i] = sx; vy[i] = sy; life[i] = t; span[i] = t; drag[i] = d; size[i] = s;
    red[i] = c[0]; green[i] = c[1]; blue[i] = c[2]; role[i] = r || 0;
  }

  // A shell climbs from the ground to the spot it aims at, a few sparks of its tail behind it.
  function launch(tx, ty) {
    var g = 60 + gravity * 18, x0 = Math.max(20, Math.min(W - 20, tx + k.range(-60, 60))), h = H - ty;
    var t = Math.sqrt(2 * h / g);
    shells.push({ x: x0, y: H, vx: (tx - x0) / t, vy: -g * t, g: g, kind: shell === 'mix' ? k.pick(KINDS) : shell, c: k.pick(HUES) });
    sent++;
  }

  function burst(s) {
    var i, a, v, n, c = s.c;
    if (s.kind === 'peony') {
      for (i = 0; i < 110; i++) { a = k.range(0, Math.PI * 2); v = Math.sqrt(k.rand()) * 150; spark(s.x, s.y, Math.cos(a) * v, Math.sin(a) * v, k.range(1.1, 1.7), 1.6, c, 1.8); }
    } else if (s.kind === 'ring') {
      var tilt = k.range(0.35, 1);
      for (i = 0; i < 70; i++) { a = i / 70 * Math.PI * 2; spark(s.x, s.y, Math.cos(a) * 130, Math.sin(a) * 130 * tilt, 1.4, 1.4, c, 1.8); }
      for (i = 0; i < 20; i++) { a = k.range(0, Math.PI * 2); v = k.range(0, 40); spark(s.x, s.y, Math.cos(a) * v, Math.sin(a) * v, 1, 1.4, [255, 255, 255], 1.4); }
    } else if (s.kind === 'willow') {
      for (i = 0; i < 120; i++) { a = k.range(0, Math.PI * 2); v = Math.sqrt(k.rand()) * 110; spark(s.x, s.y, Math.cos(a) * v, Math.sin(a) * v, k.range(2.2, 3.2), 0.9, [255, 200, 90], 1.4); }
    } else if (s.kind === 'palm') {
      n = 9;
      for (i = 0; i < n; i++) {
        a = -Math.PI / 2 + (i - (n - 1) / 2) * 0.33;
        for (var j = 0; j < 9; j++) { v = 90 + j * 9; spark(s.x, s.y, Math.cos(a) * v + k.range(-4, 4), Math.sin(a) * v, 1.5 + j * 0.05, 1.2, c, 2.2); }
      }
    } else {
      for (i = 0; i < 14; i++) { a = i / 14 * Math.PI * 2; spark(s.x, s.y, Math.cos(a) * 110, Math.sin(a) * 110, 0.55, 1.2, c, 2.2, 1); }
    }
  }

  // A crossette's spark splits in four when it burns out.
  function split(i) {
    var c = [red[i], green[i], blue[i]];
    for (var j = 0; j < 4; j++) { var a = j / 4 * Math.PI * 2 + Math.atan2(vy[i], vx[i]) + Math.PI / 4; spark(px[i], py[i], Math.cos(a) * 70 + vx[i] * 0.3, Math.sin(a) * 70 + vy[i] * 0.3, 0.9, 1.4, c, 1.6); }
  }

  function step(dt) {
    var g = 30 + gravity * 14, i;
    wait -= dt;
    if (rate > 0 && wait <= 0) {
      launch(k.range(W * 0.15, W * 0.85), k.range(H * 0.12, H * 0.45));
      wait = k.range(0.4, 1.6) * 3 / rate;
    }
    for (i = shells.length - 1; i >= 0; i--) {
      var s = shells[i];
      s.vy += s.g * dt;
      s.x += s.vx * dt;
      s.y += s.vy * dt;
      spark(s.x, s.y, k.range(-8, 8), k.range(10, 30), 0.35, 2, [255, 210, 150], 1.3);
      if (s.vy >= 0) { burst(s); shells.splice(i, 1); }
    }
    for (i = 0; i < used; i++) {
      var dd = Math.pow(1 - drag[i] * 0.5 * 0.6, dt * 4);
      vx[i] *= dd; vy[i] = vy[i] * dd + g * dt;
      px[i] += vx[i] * dt; py[i] += vy[i] * dt;
      life[i] -= dt;
      if (life[i] <= 0 || py[i] > H + 10) {
        if (role[i] === 1 && life[i] <= 0) split(i);
        used--;
        px[i] = px[used]; py[i] = py[used]; vx[i] = vx[used]; vy[i] = vy[used]; life[i] = life[used]; span[i] = span[used];
        drag[i] = drag[used]; size[i] = size[used]; red[i] = red[used]; green[i] = green[used]; blue[i] = blue[used]; role[i] = role[used];
        i--;
      }
    }
  }

  function draw(clear) {
    // The sky is painted over thinly each frame, so a spark leaves a short streak.
    ctx.globalCompositeOperation = 'source-over';
    ctx.fillStyle = clear ? k.color('night') : k.alpha('night', 0.28);
    ctx.fillRect(0, 0, W, H);
    ctx.globalCompositeOperation = 'lighter';
    for (var i = 0; i < used; i++) {
      var a = Math.min(1, life[i] / span[i] * 1.6);
      // A dying spark flickers - by the browser's own random number, which leaves the show
      // the seed decides as it is.
      if (life[i] < span[i] * 0.3 && Math.random() < 0.3) a *= 0.3;
      ctx.fillStyle = 'rgba(' + red[i] + ',' + green[i] + ',' + blue[i] + ',' + a.toFixed(2) + ')';
      ctx.fillRect(px[i] - size[i] / 2, py[i] - size[i] / 2, size[i], size[i]);
    }
    ctx.globalCompositeOperation = 'source-over';
    k.info(sent + (sent === 1 ? ' shell' : ' shells') + ' sent up · ' + used + ' sparks in the air');
    var now = Date.now();
    if (now - said > 1500) {
      said = now;
      k.describe(canvas, 'Fireworks in a night sky: ' + (shells.length ? shells.length + ' climbing, ' : '') + (used > 50 ? 'sparks falling' : 'the sky dark for a moment') + ' - ' + sent + ' sent up so far');
    }
  }

  k.loop(function (dt) { step(dt); draw(false); }, function () { draw(true); });

  k.pointer(canvas, {
    down: function (p) {
      launch(p.x, Math.min(p.y, H - 60));
      k.status('A shell is on its way');
      if (!k.playing) k.play();
    }
  });

  k.keys({ f: function () { launch(k.range(W * 0.2, W * 0.8), k.range(H * 0.15, H * 0.4)); if (!k.playing) k.play(); } });
  k.onPlay = function () { k.status(rate ? 'The show is on - click the sky to send one up' : 'Click the sky to send a shell up'); };
  k.onPause = function () { k.status('Paused'); };

  k.row();
  k.playButton();
  k.button('Fire', function () { launch(k.range(W * 0.2, W * 0.8), k.range(H * 0.15, H * 0.4)); if (!k.playing) k.play(); }, 'Send a shell up (F)');
  k.button('Finale', function () { for (var i = 0; i < 12; i++) launch(k.range(W * 0.1, W * 0.9), k.range(H * 0.1, H * 0.5)); if (!k.playing) k.play(); k.status('Finale!'); }, 'A dozen shells at once');
  k.select('Shell', [['mix', 'A mix'], ['peony', 'Peony'], ['ring', 'Ring'], ['willow', 'Willow'], ['palm', 'Palm'], ['crossette', 'Crossette']], shell, function (v) { shell = v; });
  k.row();
  k.slider('Show', 0, 10, 1, rate, function (v) { rate = v; wait = 0; }, function (v) { return v ? v : 'off'; });
  k.slider('Gravity', 0, 10, 1, gravity, function (v) { gravity = v; });
  k.theme(function () { draw(true); });

  draw(true);
  // Waiting, the sky shows a moment of the show: three shells sent up and run until they
  // have burst, as a still picture.
  k.still(function () {
    for (var i = 0; i < 3; i++) launch(W * (0.25 + i * 0.25), H * (0.22 + (i % 2) * 0.12));
    for (var f = 0; f < 175; f++) { step(1 / 60); draw(false); }
  });
  k.begin();
});
