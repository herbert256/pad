PadGames.register('tictactoe', function (k, cfg) {
  var LINES = [[0,1,2],[3,4,5],[6,7,8],[0,3,6],[1,4,7],[2,5,8],[0,4,8],[2,4,6]];
  var cells, board, turn, grid;

  function winner(b) {
    for (var i = 0; i < LINES.length; i++) {
      var l = LINES[i];
      if (b[l[0]] && b[l[0]] === b[l[1]] && b[l[0]] === b[l[2]]) return { mark: b[l[0]], line: l };
    }
    return b.indexOf('') < 0 ? { mark: '' } : null;
  }

  // Minimax over the whole game: the computer is O, a win sooner scores higher.
  function score(b, mark, depth) {
    var w = winner(b);
    if (w) return w.mark === 'O' ? 10 - depth : (w.mark === 'X' ? depth - 10 : 0);
    var best = mark === 'O' ? -99 : 99;
    for (var i = 0; i < 9; i++) if (!b[i]) {
      b[i] = mark;
      var s = score(b, mark === 'O' ? 'X' : 'O', depth + 1);
      b[i] = '';
      best = mark === 'O' ? Math.max(best, s) : Math.min(best, s);
    }
    return best;
  }

  function computer() {
    var best = -99, moves = [];
    for (var i = 0; i < 9; i++) if (!board[i]) {
      board[i] = 'O';
      var s = score(board, 'X', 1);
      board[i] = '';
      if (s > best) { best = s; moves = [i]; } else if (s === best) moves.push(i);
    }
    place(k.pick(moves));
  }

  function place(i) {
    board[i] = turn;
    cells[i].textContent = turn;
    cells[i].classList.add(turn === 'X' ? 'is-x' : 'is-o');
    cells[i].disabled = true;
    cells[i].setAttribute('aria-label', 'square ' + (i + 1) + ', ' + turn);
    var w = winner(board);
    if (w) {
      cells.forEach(function (c) { c.disabled = true; });
      if (w.line) w.line.forEach(function (j) { cells[j].classList.add('is-line'); });
      if (!w.mark) return k.won("A draw - nobody wins.");
      if (cfg.two) return k.won(w.mark + ' wins!');
      return w.mark === 'X' ? k.won('You win!') : k.lost('The computer wins.');
    }
    turn = turn === 'X' ? 'O' : 'X';
    if (!cfg.two && turn === 'O') { k.status('The computer thinks...'); k.later(computer, 350); }
    else k.status(cfg.two ? turn + ' to play' : 'Your turn - you are X');
  }

  function newGame() {
    k.fresh();
    board = ['', '', '', '', '', '', '', '', ''];
    grid = k.el('div', 'ttt-grid');
    grid.setAttribute('role', 'grid');
    cells = board.map(function (_, i) {
      var c = k.el('button', 'ttt-cell');
      c.type = 'button';
      c.setAttribute('aria-label', 'square ' + (i + 1) + ', empty');
      c.addEventListener('click', function () { if (!c.disabled && (cfg.two || turn === 'X')) place(i); });
      grid.appendChild(c);
      return c;
    });
    k.board.appendChild(grid);
    turn = 'X';
    if (!cfg.two && cfg.opens === 'computer') { turn = 'O'; k.status('The computer opens...'); k.later(computer, 350); }
    else k.status(cfg.two ? 'X to play' : 'Your turn - you are X');
  }

  k.button('New game', newGame);
  newGame();
});
