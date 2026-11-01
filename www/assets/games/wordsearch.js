PadGames.register('wordsearch', function (k, cfg) {
  var WORDS = ['tag', 'pipe', 'field', 'level', 'data', 'option', 'echo', 'page', 'query', 'loop'];
  var DIRS = [[0, 1], [1, 0], [1, 1], [-1, 1], [0, -1], [-1, 0], [-1, -1], [1, -1]];
  var N, grid, words, placed, found, cells, start, list;

  function fill() {
    for (var tries = 0; tries < 50; tries++) {
      grid = new Array(N * N).fill('');
      placed = [];
      var ok = true;
      words.forEach(function (w) {
        if (!ok) return;
        for (var t = 0; t < 300; t++) {
          var d = k.pick(DIRS), r = k.int(N), c = k.int(N), er = r + d[0] * (w.length - 1), ec = c + d[1] * (w.length - 1);
          if (er < 0 || ec < 0 || er >= N || ec >= N) continue;
          var fits = true;
          for (var i = 0; i < w.length; i++) { var g = grid[(r + d[0] * i) * N + c + d[1] * i]; if (g && g !== w[i]) { fits = false; break; } }
          if (!fits) continue;
          var at = [];
          for (i = 0; i < w.length; i++) { var j = (r + d[0] * i) * N + c + d[1] * i; grid[j] = w[i]; at.push(j); }
          placed.push({ word: w, at: at });
          return;
        }
        ok = false;
      });
      if (ok) break;
    }
    var abc = 'abcdefghijklmnopqrstuvwxyz';
    for (var i = 0; i < N * N; i++) if (!grid[i]) grid[i] = abc[k.int(26)];
  }

  // The cells from a to b, when they stand on one line.
  function line(a, b) {
    var r0 = Math.floor(a / N), c0 = a % N, r1 = Math.floor(b / N), c1 = b % N;
    var dr = Math.sign(r1 - r0), dc = Math.sign(c1 - c0), n = Math.max(Math.abs(r1 - r0), Math.abs(c1 - c0));
    if (r0 + dr * n !== r1 || c0 + dc * n !== c1) return null;
    var out = [];
    for (var i = 0; i <= n; i++) out.push((r0 + dr * i) * N + c0 + dc * i);
    return out;
  }

  function click(i) {
    if (k.over()) return;
    if (start === null) { start = i; cells[i].classList.add('is-start'); k.status('Now click the last letter of the word'); return; }
    var from = start;
    cells[from].classList.remove('is-start');
    start = null;
    var at = line(from, i);
    if (!at) return k.status('A word runs in a straight line - across, down or diagonal.');
    var hit = placed.filter(function (p) {
      return !found[p.word] && (p.at.join() === at.join() || p.at.slice().reverse().join() === at.join());
    })[0];
    if (!hit) return k.status('"' + at.map(function (j) { return grid[j]; }).join('') + '" is not one of the words.');
    found[hit.word] = true;
    hit.at.forEach(function (j) { cells[j].classList.add('is-found'); });
    list.querySelector('[data-word="' + hit.word + '"]').classList.add('is-found');
    var left = placed.length - Object.keys(found).length;
    if (!left) return k.won('All ' + placed.length + ' words found!');
    k.status('Found "' + hit.word + '" - ' + left + ' to go');
  }

  function newGame() {
    k.fresh();
    words = (cfg.words && cfg.words.length ? cfg.words : WORDS).map(function (w) { return w.replace(/[^a-z]/g, ''); }).filter(Boolean);
    N = cfg.size || Math.max(10, Math.max.apply(null, words.map(function (w) { return w.length; })) + 2);
    fill();
    found = {};
    start = null;
    var wrap = k.el('div', 'wds-wrap'), table = k.el('div', 'wds-grid');
    table.style.gridTemplateColumns = 'repeat(' + N + ', 30px)';
    cells = grid.map(function (ch, i) {
      var b = k.el('button', 'wds-cell', ch);
      b.type = 'button';
      b.setAttribute('aria-label', ch + ', row ' + (Math.floor(i / N) + 1) + ', column ' + (i % N + 1));
      b.addEventListener('click', function () { click(i); });
      table.appendChild(b);
      return b;
    });
    list = k.el('ul', 'wds-list');
    placed.map(function (p) { return p.word; }).sort().forEach(function (w) {
      var li = k.el('li', null, w);
      li.setAttribute('data-word', w);
      list.appendChild(li);
    });
    wrap.appendChild(table);
    wrap.appendChild(list);
    k.board.appendChild(wrap);
    k.status(placed.length + ' words hide here - click the first letter of one, then its last');
  }

  k.button('New puzzle', newGame);
  newGame();
});
