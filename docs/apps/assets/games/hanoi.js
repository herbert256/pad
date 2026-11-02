PadGames.register('hanoi', function (k, cfg) {
  var N = cfg.disks || 4, pegs, lifted, moves, buttons;
  var best = Math.pow(2, N) - 1;

  function draw() {
    pegs.forEach(function (stack, p) {
      var b = buttons[p];
      b.textContent = '';
      b.appendChild(k.el('span', 'hno-rod'));
      stack.forEach(function (d, j) {
        var disc = k.el('span', 'hno-disc hno-d' + d);
        disc.style.width = (30 + d * (110 / N)) + 'px';
        if (lifted === p && j === stack.length - 1) disc.classList.add('is-lifted');
        b.appendChild(disc);
      });
      b.classList.toggle('is-from', lifted === p);
      b.setAttribute('aria-label', 'peg ' + (p + 1) + ': ' + (stack.length ? 'discs ' + stack.slice().reverse().join(', ') + ' from the top' : 'empty') + (lifted === p ? ', disc lifted' : ''));
    });
  }

  function click(p) {
    if (k.over()) return;
    if (lifted === null) {
      if (!pegs[p].length) return k.status('That peg is empty.');
      lifted = p;
      k.status('Disc ' + pegs[p][pegs[p].length - 1] + ' lifted - choose a peg to put it on');
    } else if (lifted === p) {
      lifted = null;
      k.status(moves + ' moves');
    } else {
      var d = pegs[lifted][pegs[lifted].length - 1], top = pegs[p][pegs[p].length - 1];
      if (top !== undefined && top < d) { k.status('A larger disc never goes on a smaller one.'); return; }
      pegs[p].push(pegs[lifted].pop());
      lifted = null;
      moves++;
      if (pegs[2].length === N) {
        draw();
        return k.won('Done in ' + moves + ' moves' + (moves === best ? ' - the fewest there are!' : ' - it can be done in ' + best + '.'));
      }
      k.status(moves + (moves === 1 ? ' move' : ' moves') + ' - the fewest is ' + best);
    }
    draw();
  }

  function newGame() {
    k.fresh();
    pegs = [[], [], []];
    for (var d = N; d >= 1; d--) pegs[0].push(d);
    lifted = null;
    moves = 0;
    var row = k.el('div', 'hno-row');
    buttons = pegs.map(function (_, p) {
      var b = k.el('button', 'hno-peg');
      b.type = 'button';
      b.addEventListener('click', function () { click(p); });
      row.appendChild(b);
      return b;
    });
    k.board.appendChild(row);
    draw();
    k.status('Move the tower to the last peg - click a peg to lift its top disc');
  }

  k.keys({ '1': function () { click(0); }, '2': function () { click(1); }, '3': function () { click(2); } });
  k.button('Start again', newGame);
  newGame();
});
