PadGames.register('scramble', function (k, cfg) {
  var WORDS = ['template', 'variable', 'function', 'occurrence', 'sequence', 'property', 'parameter', 'expression',
               'condition', 'iteration', 'database', 'fragment', 'wrapper', 'callback', 'element', 'pattern'];
  var pool, word, tiles, answer, round, score, rounds;

  function shuffled(w) {
    var a = w.split(''), s;
    for (var t = 0; t < 20; t++) { s = k.shuffle(a.slice()).join(''); if (s !== w) return s; }
    return s;
  }

  function show() {
    answerEl.textContent = '';
    answer.forEach(function (i) { answerEl.appendChild(k.el('span', 'scr-slot is-set', tiles[i].textContent)); });
    for (var j = answer.length; j < word.length; j++) answerEl.appendChild(k.el('span', 'scr-slot'));
    answerEl.setAttribute('aria-label', 'your answer: ' + (answer.map(function (i) { return tiles[i].textContent; }).join('') || 'nothing yet'));
  }

  function pickTile(i) {
    if (k.over() || tiles[i].disabled) return;
    tiles[i].disabled = true;
    answer.push(i);
    show();
    if (answer.length === word.length) {
      var spelled = answer.map(function (j) { return tiles[j].textContent; }).join('');
      if (spelled === word) { score++; next('"' + word + '" - right!'); }
      else { k.status('"' + spelled + '" is not it - try again'); k.later(clearAnswer, 700); }
    }
  }

  function clearAnswer() { answer = []; tiles.forEach(function (t) { t.disabled = false; }); show(); }

  function next(said) {
    round++;
    if (round > rounds) {
      draw();
      var text = (said ? said + ' ' : '') + 'You found ' + score + ' of ' + rounds + '.';
      return score === rounds ? k.won(text) : (score * 2 >= rounds ? k.won(text) : k.lost(text));
    }
    deal();
    if (said) k.status(said + ' Word ' + round + ' of ' + rounds + ', ' + score + ' found');
  }

  var tilesEl, answerEl;

  function draw() { tilesEl.textContent = ''; answerEl.textContent = ''; }

  function deal() {
    word = pool[(round - 1) % pool.length];
    var letters = shuffled(word);
    tilesEl.textContent = '';
    tiles = letters.split('').map(function (ch, i) {
      var b = k.el('button', 'scr-tile', ch);
      b.type = 'button';
      b.addEventListener('click', function () { pickTile(i); });
      tilesEl.appendChild(b);
      return b;
    });
    answer = [];
    show();
    k.status('Word ' + round + ' of ' + rounds + ', ' + score + ' found - spell the word again');
  }

  function newGame() {
    k.fresh();
    pool = k.shuffle((cfg.words && cfg.words.length ? cfg.words : WORDS).slice());
    rounds = Math.min(cfg.rounds || 10, Math.max(pool.length, 1) * 3);
    round = 1;
    score = 0;
    answerEl = k.el('div', 'scr-answer');
    tilesEl = k.el('div', 'scr-tiles');
    k.board.appendChild(answerEl);
    k.board.appendChild(tilesEl);
    deal();
  }

  k.root.addEventListener('keydown', function (e) {
    if (e.altKey || e.ctrlKey || e.metaKey || k.over()) return;
    if (e.key === 'Backspace') { var last = answer.pop(); if (last !== undefined) { tiles[last].disabled = false; show(); } e.preventDefault(); return; }
    var ch = e.key.toLowerCase();
    for (var i = 0; i < tiles.length; i++) if (!tiles[i].disabled && tiles[i].textContent === ch) { pickTile(i); e.preventDefault(); return; }
  });
  k.button('Clear', function () { if (!k.over()) clearAnswer(); });
  k.button('Skip', function () { if (!k.over()) next('It was "' + word + '".'); });
  k.button('New game', newGame);
  newGame();
});
