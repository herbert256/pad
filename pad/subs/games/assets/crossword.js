PadGames.register('crossword', function (k, cfg) {
  var R = cfg.rows, C = cfg.cols, letters, inputs = [], across = true, current = null, clueItems = [];

  letters = new TextDecoder().decode(Uint8Array.from(atob(cfg.letters), function (c) { return c.charCodeAt(0); })).split('\n');

  // The clue of a square in a direction, and the squares of that clue.
  function clueOf(i, way) {
    for (var n = 0; n < cfg.clues.length; n++) if (cfg.clues[n].across === way && cfg.clues[n].squares.indexOf(i) >= 0) return cfg.clues[n];
    return null;
  }

  function focus(i, way) {
    if (way !== undefined) across = way;
    if (!clueOf(i, across)) across = !across;
    current = clueOf(i, across);
    inputs[i].focus();
    mark();
  }

  function mark() {
    inputs.forEach(function (inp) { if (inp) inp.parentNode.classList.remove('is-word'); });
    clueItems.forEach(function (li) { li.classList.remove('is-current'); });
    if (!current) return;
    current.squares.forEach(function (j) { inputs[j].parentNode.classList.add('is-word'); });
    var li = clueItems[cfg.clues.indexOf(current)];
    li.classList.add('is-current');
    clueBar.textContent = current.n + (current.across ? ' across: ' : ' down: ') + current.clue;
  }

  function step(i, forward) {
    if (!current) return;
    var at = current.squares.indexOf(i), next = current.squares[at + (forward ? 1 : -1)];
    if (next !== undefined) inputs[next].focus();
  }

  function check(marking) {
    var right = 0, total = 0, wrong = 0;
    inputs.forEach(function (inp, i) {
      if (!inp) return;
      total++;
      var v = inp.value.toUpperCase(), ok = v === letters[i].toUpperCase();
      if (ok) right++;
      if (marking) inp.parentNode.classList.toggle('is-wrong', !!v && !ok);
      if (v && !ok) wrong++;
    });
    if (right === total) {
      inputs.forEach(function (inp) { if (inp) { inp.readOnly = true; inp.parentNode.classList.remove('is-wrong'); } });
      return k.won('Solved - every word is right!');
    }
    if (marking) k.status(wrong ? wrong + (wrong === 1 ? ' letter is wrong' : ' letters are wrong') + ' - marked in red' : 'Nothing wrong so far - ' + (total - right) + ' squares to go.');
  }

  function reveal(squares) {
    squares.forEach(function (j) { inputs[j].value = letters[j]; inputs[j].parentNode.classList.add('is-revealed'); inputs[j].parentNode.classList.remove('is-wrong'); });
    check(false);
  }

  var grid = k.el('div', 'cwd-grid'), clueBar = k.el('p', 'cwd-bar');
  grid.style.gridTemplateColumns = 'repeat(' + C + ', var(--cwd-size))';
  var numbers = {};
  cfg.clues.forEach(function (c) { numbers[c.squares[0]] = c.n; });

  for (var i = 0; i < R * C; i++) (function (i) {
    if (!letters[i]) { grid.appendChild(k.el('span', 'cwd-block')); inputs.push(null); return; }
    var sq = k.el('span', 'cwd-square'), inp = k.el('input', 'cwd-input');
    if (numbers[i]) sq.appendChild(k.el('span', 'cwd-number', numbers[i]));
    inp.maxLength = 1;
    inp.autocomplete = 'off';
    inp.setAttribute('autocapitalize', 'characters');
    inp.setAttribute('aria-label', 'row ' + (Math.floor(i / C) + 1) + ', column ' + (i % C + 1) + (numbers[i] ? ', ' + numbers[i] : ''));
    inp.addEventListener('focus', function () { if (!current || current.squares.indexOf(i) < 0) { current = clueOf(i, across) || clueOf(i, !across); if (current) across = current.across; mark(); } inp.select(); });
    inp.addEventListener('mousedown', function (e) {
      if (document.activeElement === inp && clueOf(i, !across)) { e.preventDefault(); focus(i, !across); }
    });
    inp.addEventListener('input', function () {
      inp.value = inp.value.slice(-1).toUpperCase();
      sq.classList.remove('is-wrong', 'is-revealed');
      if (inp.value) step(i, true);
      check(false);
    });
    inp.addEventListener('keydown', function (e) {
      var r = Math.floor(i / C), c = i % C, to = null;
      if (e.key === 'Backspace' && !inp.value) { step(i, false); e.preventDefault(); return; }
      if (e.key === 'ArrowRight') to = [r, c + 1, true];
      if (e.key === 'ArrowLeft') to = [r, c - 1, true];
      if (e.key === 'ArrowDown') to = [r + 1, c, false];
      if (e.key === 'ArrowUp') to = [r - 1, c, false];
      if (!to) return;
      e.preventDefault();
      for (var rr = to[0], cc = to[1]; rr >= 0 && cc >= 0 && rr < R && cc < C; rr += to[0] - r, cc += to[1] - c) {
        if (inputs[rr * C + cc]) { focus(rr * C + cc, to[2]); return; }
        if (to[0] === r && to[1] === c) break;
      }
    });
    sq.appendChild(inp);
    grid.appendChild(sq);
    inputs.push(inp);
  })(i);

  var lists = k.el('div', 'cwd-lists');
  [true, false].forEach(function (way) {
    var mine = cfg.clues.filter(function (c) { return c.across === way; });
    if (!mine.length) return;
    var sec = k.el('section', 'cwd-clues');
    sec.appendChild(k.el('h4', null, way ? 'Across' : 'Down'));
    var ol = k.el('ol');
    mine.forEach(function (c) {
      var li = k.el('li', null);
      li.value = c.n;
      var b = k.el('button', 'cwd-clue', c.clue + ' (' + c.squares.length + ')');
      b.type = 'button';
      b.addEventListener('click', function () { focus(c.squares[0], c.across); });
      li.appendChild(b);
      ol.appendChild(li);
      clueItems[cfg.clues.indexOf(c)] = li;
    });
    sec.appendChild(ol);
    lists.appendChild(sec);
  });
  if (cfg.skipped && cfg.skipped.length) lists.appendChild(k.el('p', 'pg-note', 'Not placed: ' + cfg.skipped.join(', ')));

  var wrap = k.el('div', 'cwd-wrap'), left = k.el('div', 'cwd-left');
  left.appendChild(clueBar);
  left.appendChild(grid);
  wrap.appendChild(left);
  wrap.appendChild(lists);
  k.board.appendChild(wrap);

  k.button('Check', function () { if (!k.over()) check(true); });
  k.button('Reveal word', function () { if (!k.over() && current) reveal(current.squares); });
  k.button('Reveal all', function () { if (!k.over()) { var all = []; inputs.forEach(function (inp, j) { if (inp) all.push(j); }); reveal(all); } });
  k.button('Clear', function () {
    inputs.forEach(function (inp) { if (inp) { inp.value = ''; inp.readOnly = false; inp.parentNode.classList.remove('is-wrong', 'is-revealed'); } });
    k.root.classList.remove('is-won');
    k.root.removeAttribute('data-result');
    k.status(cfg.clues.length + ' words - click a square or a clue and type');
  });
  k.status(cfg.clues.length + ' words - click a square or a clue and type');
});
