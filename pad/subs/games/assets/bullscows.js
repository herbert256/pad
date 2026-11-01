PadGames.register('bullscows', function (k, cfg) {
  var D = cfg.digits || 4, MAX = cfg.tries || 10, secret, guesses, input, list, form;

  function newGame() {
    k.fresh();
    var digits = k.shuffle('0123456789'.split(''));
    secret = digits.slice(0, D).join('');
    guesses = 0;
    form = k.el('form', 'blc-form');
    var label = k.el('label', 'blc-label', 'Your guess');
    input = k.el('input', 'pg-input blc-input');
    input.inputMode = 'numeric';
    input.maxLength = D;
    input.autocomplete = 'off';
    input.id = k.root.id + '-guess';
    label.htmlFor = input.id;
    var go = k.el('button', 'pg-button', 'Guess');
    go.type = 'submit';
    form.appendChild(label);
    form.appendChild(input);
    form.appendChild(go);
    form.addEventListener('submit', function (e) { e.preventDefault(); guess(input.value.trim()); });
    list = k.el('ol', 'blc-list');
    k.board.appendChild(form);
    k.board.appendChild(list);
    k.status('Find the number of ' + D + ' different digits - ' + MAX + ' guesses');
  }

  function guess(g) {
    if (k.over()) return;
    if (!new RegExp('^\\d{' + D + '}$').test(g)) return k.status('A guess is ' + D + ' digits.');
    if (new Set(g.split('')).size !== D) return k.status('The digits of a guess are all different.');
    guesses++;
    var bulls = 0, cows = 0;
    for (var i = 0; i < D; i++) {
      if (g[i] === secret[i]) bulls++;
      else if (secret.indexOf(g[i]) >= 0) cows++;
    }
    var li = k.el('li', 'blc-guess');
    li.appendChild(k.el('span', 'blc-digits', g));
    li.appendChild(k.el('span', 'blc-bulls', bulls + (bulls === 1 ? ' bull' : ' bulls')));
    li.appendChild(k.el('span', 'blc-cows', cows + (cows === 1 ? ' cow' : ' cows')));
    list.appendChild(li);
    input.value = '';
    if (bulls === D) { input.disabled = true; return k.won('Found ' + secret + ' in ' + guesses + (guesses === 1 ? ' guess!' : ' guesses!')); }
    if (guesses >= MAX) { input.disabled = true; return k.lost('Out of guesses - the number was ' + secret + '.'); }
    k.status(bulls + ' bulls, ' + cows + ' cows - ' + (MAX - guesses) + ' guesses left');
    input.focus();
  }

  k.button('New number', newGame);
  newGame();
});
