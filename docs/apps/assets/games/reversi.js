PadGames.register('reversi', function (k, cfg) {
  var N = 8, DIRS = [[-1,-1],[-1,0],[-1,1],[0,-1],[0,1],[1,-1],[1,0],[1,1]];
  // How good a square is to hold: the corners best, the squares beside them worst.
  var WEIGHT = [
    100, -20, 10,  5,  5, 10, -20, 100,
    -20, -50, -2, -2, -2, -2, -50, -20,
     10,  -2,  1,  1,  1,  1,  -2,  10,
      5,  -2,  1,  0,  0,  1,  -2,   5,
      5,  -2,  1,  0,  0,  1,  -2,   5,
     10,  -2,  1,  1,  1,  1,  -2,  10,
    -20, -50, -2, -2, -2, -2, -50, -20,
    100, -20, 10,  5,  5, 10, -20, 100];
  var disc, cells, turn, human, count;

  function flips(i, who) {
    if (disc[i]) return [];
    var r = Math.floor(i / N), c = i % N, all = [];
    DIRS.forEach(function (d) {
      var line = [], rr = r + d[0], cc = c + d[1];
      while (rr >= 0 && cc >= 0 && rr < N && cc < N && disc[rr * N + cc] === 3 - who) { line.push(rr * N + cc); rr += d[0]; cc += d[1]; }
      if (line.length && rr >= 0 && cc >= 0 && rr < N && cc < N && disc[rr * N + cc] === who) all = all.concat(line);
    });
    return all;
  }

  function legal(who) { var m = []; for (var i = 0; i < N * N; i++) if (flips(i, who).length) m.push(i); return m; }

  function draw() {
    var hints = (cfg.two || turn === human) ? legal(turn) : [];
    count = [0, 0, 0];
    disc.forEach(function (d, i) {
      count[d]++;
      var c = cells[i];
      c.classList.toggle('is-black', d === 1);
      c.classList.toggle('is-white', d === 2);
      c.classList.toggle('is-hint', hints.indexOf(i) >= 0);
      c.setAttribute('aria-label', 'abcdefgh'[i % N] + (Math.floor(i / N) + 1) + ', ' + (d === 1 ? 'black' : d === 2 ? 'white' : hints.indexOf(i) >= 0 ? 'a move' : 'empty'));
    });
    score.textContent = 'Black ' + count[1] + ' - White ' + count[2];
  }

  function play(i) {
    var f = flips(i, turn);
    if (!f.length) return;
    disc[i] = turn;
    f.forEach(function (j) { disc[j] = turn; });
    cells.forEach(function (c) { c.classList.remove('is-last'); });
    cells[i].classList.add('is-last');
    next();
  }

  function next() {
    turn = 3 - turn;
    if (!legal(turn).length) {
      turn = 3 - turn;
      if (!legal(turn).length) return end();
      k.status((turn === 1 ? 'White' : 'Black') + ' cannot move and passes.');
    }
    draw();
    if (!cfg.two && turn !== human) { k.later(computer, 450); if (!k.statusEl.textContent.match(/passes/)) k.status('The computer thinks...'); }
    else if (!k.statusEl.textContent.match(/passes/) || cfg.two) k.status(cfg.two ? (turn === 1 ? 'Black' : 'White') + ' to play' : 'Your turn');
    else k.later(function () { k.status('Your turn'); }, 1200);
  }

  function end() {
    draw();
    var b = count[1], w = count[2];
    if (cfg.two) return k.won(b === w ? 'A draw, ' + b + ' each.' : (b > w ? 'Black' : 'White') + ' wins, ' + Math.max(b, w) + ' to ' + Math.min(b, w) + '.');
    var mine = human === 1 ? b : w, theirs = human === 1 ? w : b;
    if (mine > theirs) k.won('You win, ' + mine + ' to ' + theirs + '!');
    else if (mine < theirs) k.lost('The computer wins, ' + theirs + ' to ' + mine + '.');
    else k.won('A draw, ' + mine + ' each.');
  }

  function computer() {
    var best = -1e9, choices = [];
    legal(turn).forEach(function (i) {
      var f = flips(i, turn), s = WEIGHT[i] + f.length;
      disc[i] = turn; f.forEach(function (j) { disc[j] = turn; });
      s -= legal(3 - turn).length * 2;
      disc[i] = 0; f.forEach(function (j) { disc[j] = 3 - turn; });
      if (s > best) { best = s; choices = [i]; } else if (s === best) choices.push(i);
    });
    play(k.pick(choices));
  }

  var score;

  function newGame() {
    k.fresh();
    disc = new Array(N * N).fill(0);
    disc[27] = disc[36] = 2;
    disc[28] = disc[35] = 1;
    var grid = k.el('div', 'rvs-grid');
    cells = disc.map(function (_, i) {
      var c = k.el('button', 'rvs-cell');
      c.type = 'button';
      c.addEventListener('click', function () { if (!k.over() && (cfg.two || turn === human)) play(i); });
      grid.appendChild(c);
      return c;
    });
    score = k.el('p', 'rvs-score');
    k.board.appendChild(grid);
    k.board.appendChild(score);
    turn = 1;
    human = (!cfg.two && cfg.opens === 'computer') ? 2 : 1;
    draw();
    if (!cfg.two && human === 2) { k.status('The computer opens with black...'); k.later(computer, 450); }
    else k.status(cfg.two ? 'Black to play' : 'Your turn - you play black');
  }

  k.button('New game', newGame);
  newGame();
});
