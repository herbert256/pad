PadGames.register('nonogram', function (k, cfg) {
  var PICTURES = [
    ['.##.##.', '#######', '#######', '.#####.', '..###..', '...#...'],
    ['...#...', '..###..', '.#####.', '#######', '...#...', '...#...', '..###..'],
    ['..####..', '.#....#.', '#.#..#.#', '#......#', '#.#..#.#', '#..##..#', '.#....#.', '..####..'],
    ['#......#', '##....##', '#.#..#.#', '#..##..#', '#......#', '#......#'],
    ['...##...', '..####..', '.##..##.', '##....##', '########', '##....##', '##....##'],
    ['.#.#.#.', '#######', '.#####.', '..###..', '..###..', '.#####.', '#######']
  ];
  var pic, R, C, state, cells, marking, markBtn;

  function runs(line) {
    var out = [], n = 0;
    for (var i = 0; i < line.length; i++) {
      if (line[i]) n++;
      else if (n) { out.push(n); n = 0; }
    }
    if (n) out.push(n);
    return out.length ? out : [0];
  }

  function row(r, of) { var l = []; for (var c = 0; c < C; c++) l.push(of(r, c)); return l; }
  function col(c, of) { var l = []; for (var r = 0; r < R; r++) l.push(of(r, c)); return l; }
  function want(r, c) { return pic[r][c] === '#' ? 1 : 0; }
  function have(r, c) { return state[r * C + c] === 1 ? 1 : 0; }
  function same(a, b) { return a.join(',') === b.join(','); }

  function check() {
    var done = true;
    for (var r = 0; r < R; r++) {
      var ok = same(runs(row(r, have)), runs(row(r, want)));
      rowClues[r].classList.toggle('is-done', ok);
      if (!ok) done = false;
    }
    for (var c = 0; c < C; c++) {
      ok = same(runs(col(c, have)), runs(col(c, want)));
      colClues[c].classList.toggle('is-done', ok);
      if (!ok) done = false;
    }
    if (done) {
      cells.forEach(function (b) { b.disabled = true; });
      k.won('Solved - the picture is found!');
    } else k.status(state.filter(function (s) { return s === 1; }).length + ' filled');
  }

  function set(i, value) {
    state[i] = value;
    var b = cells[i];
    b.classList.toggle('is-filled', value === 1);
    b.classList.toggle('is-marked', value === 2);
    b.setAttribute('aria-label', 'row ' + (Math.floor(i / C) + 1) + ', column ' + (i % C + 1) + ', ' + ['empty', 'filled', 'marked empty'][value]);
  }

  var rowClues, colClues;

  function newGame(pictureGiven) {
    k.fresh();
    pic = pictureGiven || k.pick(PICTURES);
    R = pic.length;
    C = pic[0].length;
    state = new Array(R * C).fill(0);
    var table = k.el('table', 'ngm-table'), head = k.el('tr');
    head.appendChild(k.el('th'));
    colClues = [];
    for (var c = 0; c < C; c++) {
      var th = k.el('th', 'ngm-col');
      runs(col(c, want)).forEach(function (n) { th.appendChild(k.el('span', null, n)); });
      head.appendChild(th);
      colClues.push(th);
    }
    table.appendChild(head);
    rowClues = [];
    cells = [];
    for (var r = 0; r < R; r++) {
      var tr = k.el('tr'), rh = k.el('th', 'ngm-row', runs(row(r, want)).join(' '));
      tr.appendChild(rh);
      rowClues.push(rh);
      for (c = 0; c < C; c++) (function (i) {
        var td = k.el('td'), b = k.el('button', 'ngm-cell');
        b.type = 'button';
        b.addEventListener('click', function () {
          if (k.over()) return;
          set(i, marking ? (state[i] === 2 ? 0 : 2) : (state[i] === 0 ? 1 : state[i] === 1 ? 2 : 0));
          check();
        });
        b.addEventListener('contextmenu', function (e) { e.preventDefault(); if (!k.over()) { set(i, state[i] === 2 ? 0 : 2); check(); } });
        td.appendChild(b);
        tr.appendChild(td);
        cells.push(b);
        set(i, 0);
      })(r * C + c);
      table.appendChild(tr);
    }
    k.board.appendChild(table);
    marking = false;
    if (markBtn) markBtn.classList.remove('is-on');
    check();
    k.status('Fill the squares the numbers ask for - right-click or "Mark" to mark a square empty');
  }

  markBtn = k.button('Mark empty', function () { marking = !marking; markBtn.classList.toggle('is-on', marking); markBtn.setAttribute('aria-pressed', marking ? 'true' : 'false'); });
  markBtn.setAttribute('aria-pressed', 'false');
  k.button('Clear', function () { state.forEach(function (_, i) { set(i, 0); }); k.root.classList.remove('is-won'); k.root.removeAttribute('data-result'); cells.forEach(function (b) { b.disabled = false; }); check(); });
  if (!(cfg.picture && cfg.picture.length)) k.button('New picture', function () { newGame(); });
  newGame(cfg.picture && cfg.picture.length ? cfg.picture : null);
});
