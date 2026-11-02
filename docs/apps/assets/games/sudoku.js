PadGames.register('sudoku', function (k, cfg) {
  var puzzle, solution, value, notes, cells, selected, noting, noteBtn, pad, many = !!cfg.many;

  function peers(i) {
    var r = Math.floor(i / 9), c = i % 9, br = r - r % 3, bc = c - c % 3, list = [];
    for (var j = 0; j < 9; j++) { list.push(r * 9 + j, j * 9 + c, (br + Math.floor(j / 3)) * 9 + bc + j % 3); }
    return list.filter(function (x) { return x !== i; });
  }
  var PEERS = []; for (var p = 0; p < 81; p++) PEERS.push(peers(p));

  // Solutions of a grid, at most 'limit': the square with the fewest digits left first.
  function solve(grid, limit, random) {
    var g = grid.slice(), out = [];
    (function go() {
      if (out.length >= limit) return;
      var best = -1, bestCands = null;
      for (var i = 0; i < 81; i++) if (!g[i]) {
        var used = {}, cands = [];
        PEERS[i].forEach(function (q) { used[g[q]] = true; });
        for (var d = 1; d <= 9; d++) if (!used[d]) cands.push(d);
        if (!cands.length) return;
        if (!bestCands || cands.length < bestCands.length) { best = i; bestCands = cands; if (cands.length === 1) break; }
      }
      if (best < 0) { out.push(g.slice()); return; }
      if (random) k.shuffle(bestCands);
      for (var n = 0; n < bestCands.length && out.length < limit; n++) { g[best] = bestCands[n]; go(); }
      g[best] = 0;
    })();
    return out;
  }

  // A new puzzle: a full grid, then squares taken out in a random order as long as the
  // puzzle keeps its one solution.
  function generate(givens) {
    var full = solve(new Array(81).fill(0), 1, true)[0], grid = full.slice(), order = k.shuffle(Array.from({ length: 81 }, function (_, i) { return i; }));
    var shown = 81;
    for (var n = 0; n < order.length && shown > givens; n++) {
      var i = order[n], keep = grid[i];
      grid[i] = 0;
      if (solve(grid, 2, false).length !== 1) grid[i] = keep; else shown--;
    }
    return { puzzle: grid, solution: full };
  }

  function draw() {
    var sel = selected, digit = sel !== null ? value[sel] : 0;
    cells.forEach(function (b, i) {
      var v = value[i], wrongPlace = v && PEERS[i].some(function (q) { return value[q] === v; });
      b.classList.toggle('is-given', !!puzzle[i]);
      b.classList.toggle('is-selected', sel === i);
      b.classList.toggle('is-peer', sel !== null && PEERS[sel].indexOf(i) >= 0);
      b.classList.toggle('is-same', !!digit && v === digit && sel !== i);
      b.classList.toggle('is-clash', !!wrongPlace && !puzzle[i]);
      b.textContent = '';
      if (v) b.textContent = v;
      else if (notes[i].length) {
        var box = k.el('span', 'sdk-notes');
        for (var d = 1; d <= 9; d++) box.appendChild(k.el('span', null, notes[i].indexOf(d) >= 0 ? d : ''));
        b.appendChild(box);
      }
      b.setAttribute('aria-label', 'row ' + (Math.floor(i / 9) + 1) + ', column ' + (i % 9 + 1) + ', ' + (v ? v + (puzzle[i] ? ', given' : '') : 'empty'));
    });
  }

  function select(i) { selected = i; draw(); if (cells[i] !== document.activeElement) cells[i].focus(); }

  function enter(d) {
    if (k.over() || selected === null || puzzle[selected]) return;
    var i = selected;
    if (noting && d) {
      if (value[i]) return;
      var at = notes[i].indexOf(d);
      if (at >= 0) notes[i].splice(at, 1); else notes[i].push(d);
    } else {
      value[i] = (value[i] === d) ? 0 : d;
      notes[i] = [];
      if (d && value[i]) PEERS[i].forEach(function (q) { var j = notes[q].indexOf(d); if (j >= 0) notes[q].splice(j, 1); });
    }
    draw();
    progress();
  }

  function progress() {
    var empty = value.filter(function (v) { return !v; }).length;
    if (!empty) {
      var clash = value.some(function (v, i) { return PEERS[i].some(function (q) { return value[q] === v; }); });
      if (!clash) {
        selected = null;
        draw();
        return k.won('Solved!');
      }
      return k.status('Every square is filled, but something clashes - check marks the wrong ones.');
    }
    k.status(empty + ' squares to go' + (many ? ' - this puzzle has more than one solution' : ''));
  }

  function newGame(fixed) {
    k.fresh();
    var made = fixed || generate(cfg.givens || 30);
    puzzle = made.puzzle;
    solution = made.solution;
    value = puzzle.slice();
    notes = value.map(function () { return []; });
    selected = null;
    var grid = k.el('div', 'sdk-grid');
    grid.setAttribute('role', 'grid');
    cells = value.map(function (_, i) {
      var b = k.el('button', 'sdk-cell');
      b.type = 'button';
      b.addEventListener('click', function () { select(i); });
      b.addEventListener('focus', function () { if (selected !== i) { selected = i; draw(); } });
      grid.appendChild(b);
      return b;
    });
    pad = k.el('div', 'sdk-pad');
    for (var d = 1; d <= 10; d++) (function (d) {
      var b = k.el('button', 'sdk-key', d === 10 ? '⌫' : d);
      b.type = 'button';
      b.setAttribute('aria-label', d === 10 ? 'erase' : 'digit ' + d);
      b.addEventListener('click', function () { enter(d === 10 ? 0 : d); if (selected !== null) cells[selected].focus(); });
      pad.appendChild(b);
    })(d);
    var wrap = k.el('div', 'sdk-wrap');
    wrap.appendChild(grid);
    wrap.appendChild(pad);
    k.board.appendChild(wrap);
    draw();
    progress();
  }

  function move(dr, dc) {
    return function () {
      var i = selected === null ? 0 : selected, r = (Math.floor(i / 9) + dr + 9) % 9, c = (i % 9 + dc + 9) % 9;
      select(r * 9 + c);
    };
  }

  var keys = { ArrowUp: move(-1, 0), ArrowDown: move(1, 0), ArrowLeft: move(0, -1), ArrowRight: move(0, 1),
               Backspace: function () { enter(0); }, Delete: function () { enter(0); }, '0': function () { enter(0); },
               n: function () { noteBtn.click(); } };
  for (var d = 1; d <= 9; d++) keys[String(d)] = (function (d) { return function () { enter(d); }; })(d);
  k.keys(keys);

  noteBtn = k.button('Notes', function () { noting = !noting; noteBtn.classList.toggle('is-on', noting); noteBtn.setAttribute('aria-pressed', noting ? 'true' : 'false'); });
  noteBtn.setAttribute('aria-pressed', 'false');
  k.button('Hint', function () {
    if (k.over()) return;
    var i = (selected !== null && !puzzle[selected] && value[selected] !== solution[selected]) ? selected : -1;
    if (i < 0) { var open = []; value.forEach(function (v, j) { if (v !== solution[j]) open.push(j); }); if (!open.length) return; i = k.pick(open); }
    value[i] = solution[i];
    notes[i] = [];
    selected = i;
    draw();
    progress();
  });
  k.button('Check', function () {
    if (k.over()) return;
    var wrong = 0;
    cells.forEach(function (b, i) { var bad = value[i] && value[i] !== solution[i]; b.classList.toggle('is-wrong', !!bad); if (bad) wrong++; });
    k.status(wrong ? wrong + (wrong === 1 ? ' square is wrong' : ' squares are wrong') + ' - marked in red' : 'Nothing wrong so far.');
    k.later(function () { cells.forEach(function (b) { b.classList.remove('is-wrong'); }); }, 2500);
  });
  if (cfg.puzzle) newGame({ puzzle: cfg.puzzle.split('').map(Number), solution: cfg.solution.split('').map(Number) });
  else { k.button('New puzzle', function () { newGame(); }); newGame(); }
});
