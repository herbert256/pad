PadGames.register('hangman', function (k, cfg) {
  var WORDS = ['garden', 'window', 'planet', 'bridge', 'castle', 'forest', 'island', 'rocket', 'puzzle', 'orange',
               'library', 'harbour', 'lantern', 'pyramid', 'volcano', 'compass', 'blanket', 'penguin', 'thunder', 'whistle'];
  var MAX = cfg.tries || 6, word, shown, wrong, letters, gallows, keysEl;
  var PARTS = ['M10,140h100', 'M30,140V10', 'M30,10h60', 'M90,10v20', 'M90,48m-14,0a14,14 0 1,0 28,0a14,14 0 1,0 -28,0',
               'M90,62v40', 'M90,74l-18,16', 'M90,74l18,16', 'M90,102l-16,24', 'M90,102l16,24'];

  function draw() {
    // The base and the post first, the figure last: as many lines as wrong letters, the
    // figure spread over the tries there are.
    var NS = 'http://www.w3.org/2000/svg', parts = Math.round(wrong.length * PARTS.length / MAX);
    gallows.textContent = '';
    for (var i = 0; i < parts; i++) {
      var p = document.createElementNS(NS, 'path');
      p.setAttribute('d', PARTS[i]);
      gallows.appendChild(p);
    }
    shown.textContent = '';
    word.split('').forEach(function (ch) {
      shown.appendChild(k.el('span', 'hgm-letter' + (letters[ch] || !/[a-z]/.test(ch) ? ' is-shown' : ''), letters[ch] || !/[a-z]/.test(ch) ? ch : ''));
    });
    shown.setAttribute('aria-label', 'the word: ' + word.split('').map(function (ch) { return letters[ch] || !/[a-z]/.test(ch) ? ch : 'blank'; }).join(' '));
  }

  function guess(ch) {
    if (k.over() || letters[ch] !== undefined) return;
    letters[ch] = word.indexOf(ch) >= 0;
    var b = keysEl.querySelector('[data-letter="' + ch + '"]');
    if (b) { b.disabled = true; b.classList.add(letters[ch] ? 'is-hit' : 'is-miss'); }
    if (!letters[ch]) wrong.push(ch);
    draw();
    var open = word.split('').filter(function (c) { return /[a-z]/.test(c) && !letters[c]; }).length;
    if (!open) return k.won('You found "' + word + '"!');
    if (wrong.length >= MAX) {
      word.split('').forEach(function (c) { letters[c] = true; });
      draw();
      return k.lost('The word was "' + word + '".');
    }
    k.status((MAX - wrong.length) + ' wrong ' + (MAX - wrong.length === 1 ? 'guess' : 'guesses') + ' left' + (wrong.length ? ' - not in it: ' + wrong.join(' ') : ''));
  }

  function newGame() {
    k.fresh();
    word = k.pick(cfg.words && cfg.words.length ? cfg.words : WORDS);
    letters = {};
    wrong = [];
    var wrap = k.el('div', 'hgm-wrap');
    gallows = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
    gallows.setAttribute('viewBox', '0 0 120 150');
    gallows.setAttribute('class', 'hgm-gallows');
    gallows.setAttribute('aria-hidden', 'true');
    shown = k.el('p', 'hgm-word');
    keysEl = k.el('div', 'hgm-keys');
    'abcdefghijklmnopqrstuvwxyz'.split('').forEach(function (ch) {
      var b = k.el('button', 'hgm-key', ch);
      b.type = 'button';
      b.setAttribute('data-letter', ch);
      b.addEventListener('click', function () { guess(ch); });
      keysEl.appendChild(b);
    });
    var side = k.el('div', 'hgm-side');
    side.appendChild(shown);
    side.appendChild(keysEl);
    wrap.appendChild(gallows);
    wrap.appendChild(side);
    k.board.appendChild(wrap);
    draw();
    k.status('Guess the word of ' + word.length + ' letters - type or click a letter');
  }

  k.root.addEventListener('keydown', function (e) {
    if (e.altKey || e.ctrlKey || e.metaKey) return;
    if (/^[a-z]$/i.test(e.key)) { guess(e.key.toLowerCase()); e.preventDefault(); }
  });
  k.button('New word', newGame);
  newGame();
});
