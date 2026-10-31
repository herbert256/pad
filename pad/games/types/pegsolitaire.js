PadGames.register('pegsolitaire', function (k) {
  var SHAPE = ['..ooo..', '..ooo..', 'ooooooo', 'ooooooo', 'ooooooo', '..ooo..', '..ooo..'];
  var holes, chosen, history, buttons;

  function hole(r, c) { return r >= 0 && c >= 0 && r < 7 && c < 7 && SHAPE[r][c] === 'o'; }

  function jumps(from) {
    var r = Math.floor(from / 7), c = from % 7, list = [];
    [[0, 1], [1, 0], [0, -1], [-1, 0]].forEach(function (d) {
      var mr = r + d[0], mc = c + d[1], tr = r + 2 * d[0], tc = c + 2 * d[1];
      if (hole(tr, tc) && holes[mr * 7 + mc] && !holes[tr * 7 + tc]) list.push([mr * 7 + mc, tr * 7 + tc]);
    });
    return list;
  }

  function anyMove() { for (var i = 0; i < 49; i++) if (holes[i] && jumps(i).length) return true; return false; }

  function left() { return holes.filter(Boolean).length; }

  function draw() {
    var targets = chosen === null ? [] : jumps(chosen).map(function (j) { return j[1]; });
    buttons.forEach(function (b, i) {
      if (!b) return;
      b.classList.toggle('is-peg', !!holes[i]);
      b.classList.toggle('is-chosen', chosen === i);
      b.classList.toggle('is-target', targets.indexOf(i) >= 0);
      b.setAttribute('aria-label', 'row ' + (Math.floor(i / 7) + 1) + ', column ' + (i % 7 + 1) + ', ' + (holes[i] ? 'peg' : 'empty') + (targets.indexOf(i) >= 0 ? ', a jump' : ''));
    });
    undo.disabled = !history.length;
  }

  function click(i) {
    if (k.over()) return;
    if (holes[i]) {
      chosen = (chosen === i || !jumps(i).length) ? null : i;
      if (holes[i] && !jumps(i).length) k.status('That peg has no jump.');
      draw();
      return;
    }
    if (chosen === null) return;
    var j = jumps(chosen).filter(function (x) { return x[1] === i; })[0];
    if (!j) return;
    holes[chosen] = 0;
    holes[j[0]] = 0;
    holes[i] = 1;
    history.push([chosen, j[0], i]);
    chosen = anyJumpFrom(i) ? i : null;
    var n = left();
    if (n === 1) { draw(); return k.won(holes[24] ? 'One peg left, in the middle - a perfect game!' : 'One peg left - you win!'); }
    if (!anyMove()) { draw(); return k.lost('No jump left - ' + n + ' pegs remain.'); }
    k.status(n + ' pegs left');
    draw();
  }

  function anyJumpFrom(i) { return jumps(i).length > 0; }

  var undo;

  function newGame() {
    k.fresh();
    holes = [];
    for (var i = 0; i < 49; i++) holes.push(hole(Math.floor(i / 7), i % 7) ? 1 : 0);
    holes[24] = 0;
    chosen = null;
    history = [];
    var grid = k.el('div', 'peg-grid');
    buttons = holes.map(function (_, i) {
      if (!hole(Math.floor(i / 7), i % 7)) { grid.appendChild(k.el('span', 'peg-off')); return null; }
      var b = k.el('button', 'peg-hole');
      b.type = 'button';
      b.addEventListener('click', function () { click(i); });
      grid.appendChild(b);
      return b;
    });
    k.board.appendChild(grid);
    draw();
    k.status('32 pegs - click a peg, then the hole it jumps to');
  }

  k.button('Start again', newGame);
  undo = k.button('Undo', function () {
    var h = history.pop();
    if (!h) return;
    k.root.classList.remove('is-won', 'is-lost');
    k.root.removeAttribute('data-result');
    holes[h[0]] = holes[h[1]] = 1;
    holes[h[2]] = 0;
    chosen = null;
    k.status(left() + ' pegs left');
    draw();
  });
  newGame();
});
