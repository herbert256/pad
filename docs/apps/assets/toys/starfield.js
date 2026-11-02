PadToys.register('starfield', function (k, cfg) {
  var W = 640, H = 400, DEPTH = 1000, MAX = 3000;
  var n = cfg.stars, speed = cfg.speed, warp = cfg.warp, boost = 1, aim = { x: 0, y: 0 }, view = { x: 0, y: 0 }, said = 0;
  var sx = new Float32Array(MAX), sy = new Float32Array(MAX), sz = new Float32Array(MAX), pz = new Float32Array(MAX), tint = new Uint8Array(MAX);
  var ctx = k.canvas(W, H, 'A field of stars rushing past', false), canvas = ctx.canvas;
  canvas.style.background = 'var(--pad-toy-night)';
  var TINTS = [[255, 255, 255], [200, 220, 255], [255, 236, 200], [255, 210, 190], [190, 205, 255]];

  // A star is a point in a box ahead of the ship; when it passes, it is put back far away.
  function place(i, far) {
    sx[i] = k.range(-W, W);
    sy[i] = k.range(-H, H);
    sz[i] = far ? DEPTH : k.range(1, DEPTH);
    pz[i] = sz[i];
    tint[i] = k.int(TINTS.length);
  }
  for (var i = 0; i < MAX; i++) place(i, false);

  function step(dt) {
    var v = speed * 60 * boost * (warp ? 3 : 1) * dt;
    // The heading eases toward where the pointer aims, so the stars swing round smoothly.
    view.x += (aim.x - view.x) * Math.min(1, dt * 3);
    view.y += (aim.y - view.y) * Math.min(1, dt * 3);
    for (var i = 0; i < n; i++) {
      pz[i] = sz[i];
      sz[i] -= v;
      sx[i] -= view.x * v * 0.9;
      sy[i] -= view.y * v * 0.9;
      if (sz[i] < 1 || Math.abs(sx[i]) > W * 1.5 || Math.abs(sy[i]) > H * 1.5) place(i, true);
    }
  }

  function draw(clear) {
    ctx.fillStyle = clear ? k.color('night') : k.alpha('night', warp ? 0.35 : 0.85);
    ctx.fillRect(0, 0, W, H);
    var f = 260, cx = W / 2, cy = H / 2;
    for (var i = 0; i < n; i++) {
      var x = cx + sx[i] / sz[i] * f, y = cy + sy[i] / sz[i] * f;
      if (x < -20 || y < -20 || x > W + 20 || y > H + 20) continue;
      var near = 1 - sz[i] / DEPTH, t = TINTS[tint[i]], a = Math.min(1, near * 1.4 + 0.1), r = Math.max(0.4, near * 2.6);
      ctx.strokeStyle = ctx.fillStyle = 'rgba(' + t[0] + ',' + t[1] + ',' + t[2] + ',' + a.toFixed(2) + ')';
      if (warp || boost > 1) {
        var ox = cx + sx[i] / pz[i] * f, oy = cy + sy[i] / pz[i] * f;
        ctx.lineWidth = r;
        ctx.beginPath(); ctx.moveTo(ox, oy); ctx.lineTo(x, y); ctx.stroke();
      } else {
        ctx.beginPath(); ctx.arc(x, y, r, 0, Math.PI * 2); ctx.fill();
      }
    }
    k.info(n + ' stars · speed ' + speed + (warp ? ' · warp' : '') + (boost > 1 ? ' · boost' : ''));
    var now = Date.now();
    if (now - said > 1500) {
      said = now;
      k.describe(canvas, 'Stars rushing past in the dark' + (warp ? ', stretched into streaks at warp' : '') + (Math.hypot(view.x, view.y) > 0.1 ? ', the ship turning' : ', heading straight on'));
    }
  }

  k.loop(function (dt) { step(dt); draw(false); }, function () { draw(true); });

  k.pointer(canvas, {
    down: function () { boost = 4; },
    move: function (p) { aim = { x: (p.x / W - 0.5) * 2, y: (p.y / H - 0.5) * 2 }; },
    up: function () { boost = 1; },
    leave: function () { aim = { x: 0, y: 0 }; }
  });

  function steer(dx, dy) { return function () { aim = { x: Math.max(-1, Math.min(1, aim.x + dx)), y: Math.max(-1, Math.min(1, aim.y + dy)) }; }; }
  function toggleWarp(v) { warp = v; warpBox.checked = v; k.status(v ? 'Warp' : 'Cruising'); }
  k.keys({ ArrowLeft: steer(-0.25, 0), ArrowRight: steer(0.25, 0), ArrowUp: steer(0, -0.25), ArrowDown: steer(0, 0.25), w: function () { toggleWarp(!warp); }, c: function () { aim = { x: 0, y: 0 }; } });
  k.onPlay = function () { k.status('Flying - the pointer steers, hold the button to speed up'); };
  k.onPause = function () { k.status('Adrift'); };

  k.row();
  k.playButton();
  k.button('Straight on', function () { aim = { x: 0, y: 0 }; }, 'Head straight on again (C)');
  var warpBox = k.check('Warp', warp, function (v) { toggleWarp(v); });
  k.slider('Stars', 50, MAX, 50, n, function (v) { n = v; });
  k.slider('Speed', 1, 20, 1, speed, function (v) { speed = v; });
  k.theme(function () { draw(true); });

  draw(true);
  k.begin();
});
