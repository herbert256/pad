PadGames.register('dotsandboxes', function (k, cfg) {
  var H = cfg.height || 4, W = cfg.width || 4;
  // The lines: first the ones across, (H + 1) rows of W, then the ones down, H rows of W + 1.
  var HL = (H + 1) * W, NL = HL + H * (W + 1), NB = H * W;
  var BOXES = [], SIDES = [], AT = [];
  var drawn, sides, owner, score, turn, human, lines, boxes, scoreEl, focus, busy, last, lastBy;

  // The boxes a line is a side of, the four lines of a box, and where a line stands in the grid
  // of dots, lines and boxes: row and column, 2 * H + 1 by 2 * W + 1.
  for (var l = 0; l < NL; l++) {
    var r, c, list = [];
    if (l < HL) {
      r = Math.floor(l / W); c = l % W;
      if (r > 0) list.push((r - 1) * W + c);
      if (r < H) list.push(r * W + c);
      AT.push([2 * r, 2 * c + 1]);
    } else {
      r = Math.floor((l - HL) / (W + 1)); c = (l - HL) % (W + 1);
      if (c > 0) list.push(r * W + c - 1);
      if (c < W) list.push(r * W + c);
      AT.push([2 * r + 1, 2 * c]);
    }
    BOXES.push(list);
  }
  for (var b = 0; b < NB; b++) {
    r = Math.floor(b / W); c = b % W;
    SIDES.push([r * W + c, (r + 1) * W + c, HL + r * (W + 1) + c, HL + r * (W + 1) + c + 1]);
  }

  function lineName(l) {
    var r = AT[l][0], c = AT[l][1];
    return r % 2 === 0 ? 'row ' + (r / 2 + 1) + ', column ' + ((c + 1) / 2) + ' to ' + ((c + 1) / 2 + 1)
                       : 'column ' + (c / 2 + 1) + ', row ' + ((r + 1) / 2) + ' to ' + ((r + 1) / 2 + 1);
  }

  function who(p) {
    if (cfg.two) return 'Player ' + p;
    return p === human ? 'You' : 'The computer';
  }

  function letter(p) { return cfg.two ? String(p) : (p === human ? 'Y' : 'C'); }

  // The missing side of a box with three, in the lines dr.
  function open(box, dr) {
    for (var i = 0; i < 4; i++) if (!dr[SIDES[box][i]]) return SIDES[box][i];
    return -1;
  }

  // How many boxes the other side takes after line l is drawn: every box with three sides it
  // can close, for as long as there is one - and how many boxes l leaves with three sides.
  function handed(l) {
    var dr = drawn.slice(), sd = sides.slice(), n = 0, threes = 0;
    function put(x) { dr[x] = 1; BOXES[x].forEach(function (bx) { if (++sd[bx] === 4) n++; }); }
    put(l);
    BOXES[l].forEach(function (bx) { if (sd[bx] === 3) threes++; });
    n = 0;
    for (;;) {
      var next = -1;
      for (var bx = 0; bx < NB; bx++) if (sd[bx] === 3) { next = open(bx, dr); break; }
      if (next < 0) return [n, threes];
      put(next);
    }
  }

  // The unowned boxes that hang together through lines not drawn yet, without the ones left out.
  function biggestPart(without) {
    var seen = new Array(NB).fill(false), most = 0;
    without.forEach(function (bx) { seen[bx] = true; });
    for (var s = 0; s < NB; s++) {
      if (seen[s] || owner[s]) continue;
      var stack = [s], size = 0;
      seen[s] = true;
      while (stack.length) {
        var bx = stack.pop();
        size++;
        SIDES[bx].forEach(function (x) {
          if (drawn[x]) return;
          BOXES[x].forEach(function (y) { if (!seen[y] && !owner[y]) { seen[y] = true; stack.push(y); } });
        });
      }
      most = Math.max(most, size);
    }
    return most;
  }

  // The double-dealing move: at the last two boxes of a chain, with no safe line left anywhere,
  // draw the far side of the second box instead of taking both - the other player takes the two
  // and has to open the next chain. Worth it while a chain of three or more is still to come.
  function doubleDeal() {
    var threes = [];
    for (var bx = 0; bx < NB; bx++) if (!owner[bx] && sides[bx] === 3) threes.push(bx);
    if (threes.length !== 1) return -1;
    var a = threes[0], mid = open(a, drawn), next = BOXES[mid].filter(function (y) { return y !== a; });
    if (next.length !== 1 || sides[next[0]] !== 2) return -1;
    var bb = next[0], far = SIDES[bb].filter(function (x) { return !drawn[x] && x !== mid; })[0];
    var beyond = BOXES[far].filter(function (y) { return y !== bb; });
    if (beyond.length && sides[beyond[0]] >= 2) return -1;
    return biggestPart([a, bb]) >= 3 ? far : -1;
  }

  function choose() {
    var free = [], capture = [], safe = [];
    for (var x = 0; x < NL; x++) {
      if (drawn[x]) continue;
      free.push(x);
      var bs = BOXES[x];
      if (bs.some(function (y) { return sides[y] === 3; })) capture.push(x);
      else if (bs.every(function (y) { return sides[y] <= 1; })) safe.push(x);
    }
    if (capture.length) {
      if (!safe.length) { var dd = doubleDeal(); if (dd >= 0) return dd; }
      return k.pick(capture);
    }
    if (safe.length) return k.pick(safe);
    // Every line gives boxes away: the one that gives the fewest - in two boxes, the line
    // between them, which leaves the other player no way to give them back.
    var best = Infinity, choices = [];
    free.forEach(function (y) {
      var h = handed(y), cost = h[0] * 10 - h[1];
      if (cost < best) { best = cost; choices = [y]; } else if (cost === best) choices.push(y);
    });
    return k.pick(choices);
  }

  function label(l) {
    var line = lines[l];
    line.setAttribute('aria-label', lineName(l) + (drawn[l] ? ', drawn' : ', free'));
    line.setAttribute('aria-disabled', drawn[l] || k.over() ? 'true' : 'false');
  }

  function showScore() {
    scoreEl.textContent = who(1) + ' ' + score[1] + ' - ' + who(2) + ' ' + score[2];
  }

  // A line drawn: the boxes it closes are taken, and the player goes on when it closed one.
  function play(l) {
    var took = 0;
    drawn[l] = turn;
    BOXES[l].forEach(function (bx) {
      if (++sides[bx] !== 4) return;
      owner[bx] = turn;
      score[turn]++;
      took++;
      boxes[bx].classList.add('is-p' + turn);
      boxes[bx].textContent = letter(turn);
    });
    // The lines of the turn played last stand out until the other player draws.
    if (lastBy !== turn) {
      last.forEach(function (x) { lines[x].classList.remove('is-last'); });
      last = [];
      lastBy = turn;
    }
    last.push(l);
    lines[l].classList.add('is-drawn', 'is-last', 'is-p' + turn);
    label(l);
    showScore();
    if (!took) turn = 3 - turn;
    return took;
  }

  function end() {
    lines.forEach(function (line, l) { label(l); });
    var a = score[1], z = score[2];
    if (cfg.two) return k.won(a === z ? 'A draw, ' + a + ' boxes each.' : 'Player ' + (a > z ? 1 : 2) + ' wins, ' + Math.max(a, z) + ' boxes to ' + Math.min(a, z) + '.');
    var mine = score[human], theirs = score[3 - human];
    if (mine > theirs) k.won('You win, ' + mine + ' boxes to ' + theirs + '!');
    else if (mine < theirs) k.lost('The computer wins, ' + theirs + ' boxes to ' + mine + '.');
    else k.won('A draw, ' + mine + ' boxes each.');
  }

  function over() { return score[1] + score[2] === NB; }

  // The computer's turn: a line every moment, as long as it closes boxes.
  function computer() {
    busy = true;
    var count = 0, taken = 0;
    (function step() {
      var l = choose(), took = play(l);
      count++;
      taken += took;
      if (over()) { busy = false; return end(); }
      if (took) return k.later(step, 380);
      busy = false;
      k.status('The computer drew ' + (count === 1 ? 'the line ' + lineName(l) : count + ' lines')
               + (taken ? ' and took ' + taken + (taken === 1 ? ' box' : ' boxes') : '') + ' - your turn');
    })();
  }

  function click(l) {
    if (drawn[l] || busy || k.over() || (!cfg.two && turn !== human)) return;
    var p = turn, took = play(l);
    if (over()) return end();
    if (took) return k.status(who(p) + ' took ' + (took === 1 ? 'a box' : 'two boxes') + ' - ' + (cfg.two ? 'Player ' + p + ' draws' : 'draw') + ' again');
    if (cfg.two) return k.status('Player ' + turn + ' to play');
    k.status('The computer thinks...');
    k.later(computer, 380);
  }

  // The arrow keys move along the lines: left and right in a row of the grid, up and down to
  // the row above or below, at the line nearest the one left.
  function move(l, key) {
    var r = AT[l][0], c = AT[l][1], best = -1, gap = Infinity;
    for (var x = 0; x < NL; x++) {
      var rr = AT[x][0], cc = AT[x][1], d;
      if (key === 'ArrowLeft' || key === 'ArrowRight') {
        if (rr !== r || (key === 'ArrowLeft' ? cc >= c : cc <= c)) continue;
        d = Math.abs(cc - c);
      } else {
        if (rr !== r + (key === 'ArrowUp' ? -1 : 1)) continue;
        d = Math.abs(cc - c) * 2 + (cc > c ? 1 : 0);
      }
      if (d < gap) { gap = d; best = x; }
    }
    return best;
  }

  function aim(l) {
    lines[focus].tabIndex = -1;
    focus = l;
    lines[l].tabIndex = 0;
  }

  function newGame() {
    k.fresh();
    drawn = new Array(NL).fill(0);
    sides = new Array(NB).fill(0);
    owner = new Array(NB).fill(0);
    score = [0, 0, 0];
    last = [];
    lastBy = 0;
    busy = false;
    turn = 1;
    human = (!cfg.two && cfg.opens === 'computer') ? 2 : 1;
    var grid = k.el('div', 'dnb-grid'), cells = {};
    grid.setAttribute('role', 'group');
    grid.setAttribute('aria-label', 'The lines between the dots, ' + (H + 1) + ' rows of ' + (W + 1));
    grid.style.gridTemplateColumns = 'repeat(' + W + ', var(--dnb-dot) var(--dnb-box)) var(--dnb-dot)';
    grid.style.gridTemplateRows = 'repeat(' + H + ', var(--dnb-dot) var(--dnb-box)) var(--dnb-dot)';
    lines = [];
    boxes = [];
    AT.forEach(function (at, x) { cells[at[0] + ',' + at[1]] = x; });
    for (var gr = 0; gr <= 2 * H; gr++) for (var gc = 0; gc <= 2 * W; gc++) {
      if (gr % 2 === 0 && gc % 2 === 0) { grid.appendChild(k.el('span', 'dnb-dot')); continue; }
      if (gr % 2 === 1 && gc % 2 === 1) {
        var box = k.el('span', 'dnb-box');
        box.setAttribute('aria-hidden', 'true');
        boxes.push(box);
        grid.appendChild(box);
        continue;
      }
      (function (x) {
        var line = k.el('button', 'dnb-line ' + (gr % 2 === 0 ? 'dnb-h' : 'dnb-v'));
        line.type = 'button';
        line.tabIndex = -1;
        line.addEventListener('click', function () { aim(x); click(x); });
        line.addEventListener('keydown', function (e) {
          if (e.altKey || e.ctrlKey || e.metaKey || !/^Arrow/.test(e.key)) return;
          e.preventDefault();
          var to = move(x, e.key);
          if (to >= 0) { aim(to); lines[to].focus(); }
        });
        lines[x] = line;
        grid.appendChild(line);
      })(cells[gr + ',' + gc]);
    }
    focus = 0;
    lines[0].tabIndex = 0;
    lines.forEach(function (line, x) { label(x); });
    scoreEl = k.el('p', 'dnb-score');
    k.board.appendChild(grid);
    k.board.appendChild(scoreEl);
    showScore();
    if (!cfg.two && human === 2) { k.status('The computer begins...'); k.later(computer, 380); }
    else k.status(cfg.two ? 'Player 1 to play - draw a line between two dots' : 'Your turn - draw a line between two dots');
  }

  k.button('New game', newGame);
  newGame();
});
