PadGames.register('fifteen', function (k, cfg) {
  var N = cfg.size || 4, tiles, gap, moves, buttons, grid;

  function solved() { for (var i = 0; i < N * N - 1; i++) if (tiles[i] !== i + 1) return false; return true; }

  function neighbours(g) {
    var r = Math.floor(g / N), c = g % N, list = [];
    if (r > 0) list.push(g - N);
    if (r < N - 1) list.push(g + N);
    if (c > 0) list.push(g - 1);
    if (c < N - 1) list.push(g + 1);
    return list;
  }

  function slide(i, counting) {
    if (neighbours(gap).indexOf(i) < 0) return false;
    tiles[gap] = tiles[i];
    tiles[i] = 0;
    gap = i;
    if (counting) {
      moves++;
      draw();
      if (solved()) k.won('Solved in ' + moves + ' moves!');
      else k.status(moves + (moves === 1 ? ' move' : ' moves'));
    }
    return true;
  }

  function draw() {
    tiles.forEach(function (t, i) {
      var b = buttons[i];
      b.textContent = t || '';
      b.classList.toggle('is-gap', !t);
      b.classList.toggle('is-home', t === i + 1);
      b.disabled = !t || k.over();
      b.setAttribute('aria-label', t ? 'tile ' + t : 'gap');
    });
  }

  // Shuffled by random slides from the solved frame, so it can always be solved.
  function newGame() {
    k.fresh();
    tiles = [];
    for (var i = 1; i < N * N; i++) tiles.push(i);
    tiles.push(0);
    gap = N * N - 1;
    var last = -1;
    for (i = 0; i < N * N * 40 || solved(); i++) {
      var options = neighbours(gap).filter(function (j) { return j !== last; });
      var from = gap;
      slide(k.pick(options), false);
      last = from;
    }
    moves = 0;
    grid = k.el('div', 'ffn-grid');
    grid.style.gridTemplateColumns = 'repeat(' + N + ', var(--ffn-size))';
    buttons = tiles.map(function (_, i) {
      var b = k.el('button', 'ffn-tile');
      b.type = 'button';
      b.addEventListener('click', function () { if (!k.over()) slide(i, true); });
      grid.appendChild(b);
      return b;
    });
    k.board.appendChild(grid);
    draw();
    k.status('Put the tiles in order - click a tile beside the gap, or use the arrow keys');
  }

  // An arrow moves the tile on that side of the gap into it.
  function arrow(dr, dc) {
    return function () {
      if (k.over()) return;
      var r = Math.floor(gap / N) - dr, c = gap % N - dc;
      if (r >= 0 && c >= 0 && r < N && c < N) slide(r * N + c, true);
    };
  }

  var keys = { ArrowUp: arrow(-1, 0), ArrowDown: arrow(1, 0), ArrowLeft: arrow(0, -1), ArrowRight: arrow(0, 1) };
  k.keys(keys);
  k.swipe(function (key) { keys[key](); });
  k.button('Shuffle', newGame);
  newGame();
});
