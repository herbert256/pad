PadGames.register('memory', function (k, cfg) {
  var FACES = ['\u{1F34E}', '\u{1F34C}', '\u{1F347}', '\u{1F352}', '\u{1F349}', '\u{1F34D}', '\u{1F95D}', '\u{1F351}', '\u{1F965}',
               '\u{1F955}', '\u{1F33D}', '\u{1F344}', '\u{1F41D}', '\u{1F98B}', '\u{1F422}', '\u{1F419}', '\u{1F98A}', '\u{1F989}'];
  var cards, open, found, turns, busy, buttons;

  function face(b, shown) {
    b.classList.toggle('is-open', shown);
    b.querySelector('.mem-face').textContent = shown ? cards[b.index] : '';
    b.setAttribute('aria-label', 'card ' + (b.index + 1) + (shown ? ', ' + cards[b.index] : ', face down'));
  }

  function turn(b) {
    if (busy || k.over() || b.classList.contains('is-open')) return;
    face(b, true);
    open.push(b);
    if (open.length < 2) return;
    turns++;
    var a = open[0], c = open[1];
    if (cards[a.index] === cards[c.index]) {
      a.classList.add('is-found');
      c.classList.add('is-found');
      a.disabled = c.disabled = true;
      open = [];
      found++;
      if (found === cfg.pairs) return k.won('All ' + found + ' pairs in ' + turns + ' turns!');
      k.status(found + ' of ' + cfg.pairs + ' pairs - ' + turns + ' turns');
    } else {
      busy = true;
      k.status(found + ' of ' + cfg.pairs + ' pairs - ' + turns + ' turns');
      k.later(function () { face(a, false); face(c, false); open = []; busy = false; }, 900);
    }
  }

  function newGame() {
    k.fresh();
    var faces = k.shuffle((cfg.faces && cfg.faces.length ? cfg.faces : FACES).slice()).slice(0, cfg.pairs);
    cards = k.shuffle(faces.concat(faces));
    open = [];
    found = 0;
    turns = 0;
    busy = false;
    var grid = k.el('div', 'mem-grid');
    grid.style.gridTemplateColumns = 'repeat(' + Math.ceil(Math.sqrt(cards.length)) + ', var(--mem-size))';
    buttons = cards.map(function (_, i) {
      var b = k.el('button', 'mem-card');
      b.type = 'button';
      b.index = i;
      b.appendChild(k.el('span', 'mem-face'));
      b.addEventListener('click', function () { turn(b); });
      face(b, false);
      grid.appendChild(b);
      return b;
    });
    k.board.appendChild(grid);
    k.status('Find the ' + cfg.pairs + ' pairs - turn two cards over');
  }

  k.button('New game', newGame);
  newGame();
});
