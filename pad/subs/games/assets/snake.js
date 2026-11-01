PadGames.register('snake', function (k, cfg) {
  var N = cfg.size || 18, CELL = 20, canvas, ctx, body, dir, queued, apple, score, running, tick, best = 0;

  // A colour of the game's palette as the canvas can paint it: the custom property is
  // light-dark(...) text, so a probe element resolves it to the colour of the page's scheme.
  var probe = k.el('span', 'pg-hide'), colours = {};
  k.root.appendChild(probe);
  function css(name) {
    if (!colours[name]) { probe.style.color = 'var(' + name + ')'; colours[name] = getComputedStyle(probe).color; }
    return colours[name];
  }

  function placeApple() {
    var free = [];
    for (var i = 0; i < N * N; i++) if (body.indexOf(i) < 0) free.push(i);
    apple = free.length ? k.pick(free) : -1;
  }

  function draw() {
    ctx.fillStyle = css('--pad-game-cell');
    ctx.fillRect(0, 0, N * CELL, N * CELL);
    ctx.fillStyle = css('--pad-game-shade');
    for (var i = 0; i < N * N; i++) if ((Math.floor(i / N) + i % N) % 2) ctx.fillRect((i % N) * CELL, Math.floor(i / N) * CELL, CELL, CELL);
    if (apple >= 0) {
      ctx.fillStyle = css('--pad-game-bad');
      ctx.beginPath();
      ctx.arc((apple % N) * CELL + CELL / 2, Math.floor(apple / N) * CELL + CELL / 2, CELL / 2 - 3, 0, Math.PI * 2);
      ctx.fill();
    }
    body.forEach(function (j, n) {
      ctx.fillStyle = n === 0 ? css('--pad-game-good') : css('--pad-game-accent');
      ctx.fillRect((j % N) * CELL + 1, Math.floor(j / N) * CELL + 1, CELL - 2, CELL - 2);
    });
  }

  function step() {
    if (queued.length) dir = queued.shift();
    var head = body[0], r = Math.floor(head / N) + dir[0], c = head % N + dir[1];
    if (cfg.wrap) { r = (r + N) % N; c = (c + N) % N; }
    var next = r * N + c, tail = body[body.length - 1];
    if (r < 0 || c < 0 || r >= N || c >= N || (body.indexOf(next) >= 0 && next !== tail)) return over();
    body.unshift(next);
    if (next === apple) {
      score++;
      placeApple();
      k.status(score + (score === 1 ? ' apple' : ' apples'));
      restart();
    } else body.pop();
    draw();
  }

  function speed() { return 1000 / Math.min(20, cfg.speed + score * 0.3); }

  function restart() { k.stop(); if (running) k.every(step, speed()); }

  function over() {
    running = false;
    best = Math.max(best, score);
    draw();
    k.lost('The snake hit ' + (cfg.wrap ? 'itself' : 'a wall or itself') + ' - ' + score + (score === 1 ? ' apple' : ' apples') + (best ? ', best ' + best : '') + '.');
    playBtn.textContent = 'Play again';
  }

  function turn(d) {
    return function () {
      if (!running) return start();
      var last = queued.length ? queued[queued.length - 1] : dir;
      if (last[0] === -d[0] && last[1] === -d[1]) return;
      if (last[0] === d[0] && last[1] === d[1]) return;
      if (queued.length < 3) queued.push(d);
    };
  }

  function reset() {
    k.fresh();
    canvas = k.el('canvas', 'snk-field');
    canvas.width = canvas.height = N * CELL;
    canvas.setAttribute('role', 'img');
    canvas.setAttribute('aria-label', 'The snake field');
    canvas.tabIndex = 0;
    ctx = canvas.getContext('2d');
    k.board.appendChild(canvas);
    var mid = Math.floor(N / 2) * N + Math.floor(N / 2);
    body = [mid, mid - 1, mid - 2];
    dir = [0, 1];
    queued = [];
    score = 0;
    placeApple();
    draw();
  }

  function start() {
    if (k.over() || !body) reset();
    if (running) return;
    running = true;
    playBtn.textContent = 'Pause';
    canvas.focus();
    k.status(score ? score + ' apples' : 'Eat the apples - arrow keys, swipe or the buttons');
    restart();
  }

  function pause() { running = false; k.stop(); playBtn.textContent = 'Play'; k.status('Paused - ' + score + ' apples'); }

  var keys = { ArrowUp: turn([-1, 0]), ArrowDown: turn([1, 0]), ArrowLeft: turn([0, -1]), ArrowRight: turn([0, 1]) };
  keys.w = keys.ArrowUp; keys.s = keys.ArrowDown; keys.a = keys.ArrowLeft; keys.d = keys.ArrowRight;
  keys[' '] = function () { running ? pause() : start(); };
  k.keys(keys);
  k.swipe(function (key) { keys[key](); });
  var playBtn = k.button('Play', function () { running ? pause() : start(); });
  [['Up', 'ArrowUp', '↑'], ['Left', 'ArrowLeft', '←'], ['Down', 'ArrowDown', '↓'], ['Right', 'ArrowRight', '→']].forEach(function (b) {
    k.button(b[2], keys[b[1]], 'snk-pad').setAttribute('aria-label', b[0]);
  });
  document.addEventListener('visibilitychange', function () { if (document.hidden && running) pause(); });
  reset();
  k.status('Press Play, or an arrow key, to start');
});
