PadGames.register('nim', function (k, cfg) {
  var heaps, rows, yours;

  function total() { return heaps.reduce(function (a, b) { return a + b; }, 0); }

  // The winning reply: leave a nim-sum of 0 - in misere, until only heaps of one are left,
  // then leave an odd number of them.
  function reply() {
    var big = heaps.filter(function (h) { return h > 1; }).length;
    if (cfg.misere && big <= 1) {
      var ones = heaps.filter(function (h) { return h === 1; }).length;
      for (var i = 0; i < heaps.length; i++) if (heaps[i] > 1) return [i, heaps[i] - (ones % 2 ? 0 : 1)];
      for (i = 0; i < heaps.length; i++) if (heaps[i]) return [i, 1];
    }
    var x = heaps.reduce(function (a, b) { return a ^ b; }, 0);
    if (x) for (i = 0; i < heaps.length; i++) if ((heaps[i] ^ x) < heaps[i]) return [i, heaps[i] - (heaps[i] ^ x)];
    var some = [];
    heaps.forEach(function (h, j) { if (h) some.push(j); });
    var j = k.pick(some);
    return [j, 1 + k.int(Math.min(heaps[j], 2))];
  }

  function take(h, n, who) {
    heaps[h] -= n;
    draw();
    if (!total()) {
      var lastWins = !cfg.misere;
      if ((who === 'you') === lastWins) return k.won(who === 'you' ? 'You took the last match - you win!' : 'The computer took the last match - you win!');
      return k.lost(who === 'you' ? 'You took the last match - the computer wins.' : 'The computer took the last match and wins.');
    }
    if (who === 'you') { yours = false; k.status('The computer thinks...'); k.later(computer, 600); }
    else { yours = true; k.status('The computer took ' + n + ' from heap ' + (h + 1) + '. Your turn.'); }
  }

  function computer() { var m = reply(); take(m[0], m[1], 'computer'); }

  function draw() {
    k.board.textContent = '';
    heaps.forEach(function (h, i) {
      var row = k.el('div', 'nim-heap');
      row.appendChild(k.el('span', 'nim-label', 'Heap ' + (i + 1)));
      for (var j = 0; j < h; j++) (function (j) {
        var m = k.el('button', 'nim-match');
        m.type = 'button';
        m.setAttribute('aria-label', 'take ' + (h - j) + ' from heap ' + (i + 1));
        m.title = 'Take ' + (h - j);
        m.addEventListener('mouseenter', function () { mark(row, j); });
        m.addEventListener('focus', function () { mark(row, j); });
        m.addEventListener('mouseleave', function () { mark(row, 99); });
        m.addEventListener('click', function () { if (yours && !k.over()) take(i, h - j, 'you'); });
        row.appendChild(m);
      })(j);
      if (!h) row.appendChild(k.el('span', 'nim-empty', 'empty'));
      k.board.appendChild(row);
    });
  }

  function mark(row, from) {
    [].forEach.call(row.querySelectorAll('.nim-match'), function (m, j) { m.classList.toggle('is-taking', j >= from); });
  }

  function newGame() {
    k.fresh();
    heaps = cfg.heaps.slice();
    draw();
    k.board.insertAdjacentElement('afterend', k.el('p', 'pg-note nim-rule'));
    var note = k.root.querySelector('.nim-rule');
    note.textContent = cfg.misere ? 'Take one or more matches from one heap. Who takes the last match loses.' : 'Take one or more matches from one heap. Who takes the last match wins.';
    if (cfg.opens === 'computer') { yours = false; k.status('The computer begins...'); k.later(computer, 600); }
    else { yours = true; k.status('Your turn - click a match to take it and the ones after it'); }
  }

  k.button('New game', function () { var n = k.root.querySelector('.nim-rule'); if (n) n.remove(); newGame(); });
  newGame();
});
