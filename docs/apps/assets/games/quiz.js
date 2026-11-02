PadGames.register('quiz', function (k, cfg) {
  var list, at, score, answered;

  function ask() {
    k.board.textContent = '';
    var q = list[at], box = k.el('div', 'quz-box');
    box.appendChild(k.el('p', 'quz-count', 'Question ' + (at + 1) + ' of ' + list.length));
    box.appendChild(k.el('p', 'quz-question', q[0]));
    var answers = k.el('div', 'quz-answers'), shuffled = k.shuffle(q[1].map(function (a, i) { return [a, i === 0]; }));
    answered = false;
    shuffled.forEach(function (a) {
      var b = k.el('button', 'quz-answer', a[0]);
      b.type = 'button';
      if (a[1]) b.setAttribute('data-right', '');
      b.addEventListener('click', function () {
        if (answered || k.over()) return;
        answered = true;
        [].forEach.call(answers.children, function (x) { x.disabled = true; if (x.hasAttribute('data-right')) x.classList.add('is-right'); });
        if (a[1]) { score++; k.status('Right! ' + score + ' of ' + (at + 1)); }
        else { b.classList.add('is-wrong'); k.status('Not quite - the answer is "' + q[1][0] + '". ' + score + ' of ' + (at + 1)); }
        nextBtn.hidden = false;
        nextBtn.textContent = at + 1 < list.length ? 'Next question' : 'See the score';
        nextBtn.focus();
      });
      answers.appendChild(b);
    });
    box.appendChild(answers);
    nextBtn = k.el('button', 'pg-button quz-next', 'Next question');
    nextBtn.type = 'button';
    nextBtn.hidden = true;
    nextBtn.addEventListener('click', function () {
      at++;
      if (at < list.length) return ask();
      k.board.textContent = '';
      var text = score + ' of ' + list.length + ' right';
      score * 2 >= list.length ? k.won(text + (score === list.length ? ' - every one!' : '.')) : k.lost(text + '.');
    });
    box.appendChild(nextBtn);
    k.board.appendChild(box);
    if (!at) k.status(list.length + ' questions - choose an answer');
  }

  var nextBtn;

  function newGame() {
    k.fresh();
    list = cfg.order ? cfg.questions.slice() : k.shuffle(cfg.questions.slice());
    at = 0;
    score = 0;
    ask();
  }

  k.button('Start again', newGame);
  newGame();
});
