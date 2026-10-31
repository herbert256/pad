PadGames.register('gomoku', function (k, cfg) {
  var N = cfg.size || 15, DIRS = [[0, 1], [1, 0], [1, 1], [1, -1]];
  var stones, points, turn, moves;

  function at(r, c) { return (r < 0 || c < 0 || r >= N || c >= N) ? -1 : stones[r * N + c]; }

  // The longest run of one colour through (r, c), and whether its ends are open.
  function run(r, c, who, d) {
    var n = 1, open = 0, i, rr, cc;
    for (i = 1; ; i++) { rr = r + d[0] * i; cc = c + d[1] * i; if (at(rr, cc) !== who) break; n++; }
    if (at(rr, cc) === 0) open++;
    for (i = 1; ; i++) { rr = r - d[0] * i; cc = c - d[1] * i; if (at(rr, cc) !== who) break; n++; }
    if (at(rr, cc) === 0) open++;
    return { n: n, open: open };
  }

  function five(r, c, who) {
    for (var d = 0; d < 4; d++) {
      var line = [[r, c]], i, rr, cc;
      for (i = 1; at(rr = r + DIRS[d][0] * i, cc = c + DIRS[d][1] * i) === who; i++) line.push([rr, cc]);
      for (i = 1; at(rr = r - DIRS[d][0] * i, cc = c - DIRS[d][1] * i) === who; i++) line.push([rr, cc]);
      if (line.length >= 5) return line;
    }
    return null;
  }

  // What a stone of who at (r, c) would be worth: its runs, longer and more open worth more.
  function worth(r, c, who) {
    var total = 0;
    stones[r * N + c] = who;
    for (var d = 0; d < 4; d++) {
      var x = run(r, c, who, DIRS[d]);
      if (x.n >= 5) total += 100000;
      else if (x.open === 0) continue;
      else total += [0, 1, 10, 100, 1000][x.n] * (x.open === 2 ? 4 : 1);
    }
    stones[r * N + c] = 0;
    return total;
  }

  function computer() {
    var best = -1, choices = [], me = turn, you = 3 - turn;
    for (var r = 0; r < N; r++) for (var c = 0; c < N; c++) {
      if (stones[r * N + c]) continue;
      var near = false;
      for (var dr = -2; dr <= 2 && !near; dr++) for (var dc = -2; dc <= 2; dc++) if (at(r + dr, c + dc) > 0) { near = true; break; }
      if (!near && moves) continue;
      var s = worth(r, c, me) * 1.1 + worth(r, c, you);
      if (s > best) { best = s; choices = [r * N + c]; } else if (s === best) choices.push(r * N + c);
    }
    if (!moves) choices = [Math.floor(N / 2) * N + Math.floor(N / 2)];
    place(k.pick(choices));
  }

  function place(i) {
    var r = Math.floor(i / N), c = i % N;
    stones[i] = turn;
    moves++;
    var p = points[i];
    p.classList.add(turn === 1 ? 'is-black' : 'is-white');
    p.disabled = true;
    p.setAttribute('aria-label', name(i) + ', ' + (turn === 1 ? 'black' : 'white'));
    points.forEach(function (q) { q.classList.remove('is-last'); });
    p.classList.add('is-last');
    var line = five(r, c, turn);
    if (line) {
      line.forEach(function (rc) { points[rc[0] * N + rc[1]].classList.add('is-line'); });
      points.forEach(function (q) { q.disabled = true; });
      if (cfg.two) return k.won((turn === 1 ? 'Black' : 'White') + ' has five in a row!');
      return turn === you0 ? k.won('Five in a row - you win!') : k.lost('The computer has five in a row.');
    }
    if (moves === N * N) return k.won('The board is full - a draw.');
    turn = 3 - turn;
    if (!cfg.two && turn !== you0) { k.status('The computer thinks...'); k.later(computer, 250); }
    else k.status(cfg.two ? (turn === 1 ? 'Black' : 'White') + ' to play' : 'Your turn');
  }

  function name(i) { return 'ABCDEFGHJKLMNOPQRST'[i % N] + (N - Math.floor(i / N)); }

  var you0;

  function newGame() {
    k.fresh();
    stones = new Array(N * N).fill(0);
    moves = 0;
    var grid = k.el('div', 'gmk-grid');
    grid.style.gridTemplateColumns = 'repeat(' + N + ', var(--gmk-size))';
    points = stones.map(function (_, i) {
      var p = k.el('button', 'gmk-point');
      p.type = 'button';
      p.setAttribute('aria-label', name(i) + ', free');
      p.addEventListener('click', function () { if (!p.disabled && (cfg.two || turn === you0)) place(i); });
      grid.appendChild(p);
      return p;
    });
    k.board.appendChild(grid);
    turn = 1;
    you0 = (!cfg.two && cfg.opens === 'computer') ? 2 : 1;
    if (!cfg.two && you0 === 2) { k.status('The computer opens with black...'); k.later(computer, 250); }
    else k.status(cfg.two ? 'Black to play' : 'Your turn - you play black');
  }

  k.button('New game', newGame);
  newGame();
});
