PadGames.register('klondike', function (k, cfg) {
  // A card is a number: its suit the 13s (spades, hearts, diamonds, clubs), its rank the rest.
  var SUITS = ['spades', 'hearts', 'diamonds', 'clubs'], SIGNS = ['♠', '♥', '♦', '♣'];
  var RANKS = ['A', '2', '3', '4', '5', '6', '7', '8', '9', '10', 'J', 'Q', 'K'];
  var NAMES = ['ace', '2', '3', '4', '5', '6', '7', '8', '9', '10', 'jack', 'queen', 'king'];
  var DRAW = cfg.draw || 1;
  var s, history, chosen, moves, table, homeBtn, undoBtn;

  function suit(c) { return Math.floor(c / 13); }
  function rank(c) { return c % 13; }
  function red(c) { return suit(c) === 1 || suit(c) === 2; }
  function name(c) { return 'the ' + NAMES[rank(c)] + ' of ' + SUITS[suit(c)]; }

  // The piles: stock and waste, the four foundations - one a suit - and the seven of the
  // table, whose cards are [card, face up].
  function deal() {
    var pack = [];
    for (var c = 0; c < 52; c++) pack.push(c);
    k.shuffle(pack);
    var t = { stock: [], waste: [], home: [[], [], [], []], piles: [] };
    for (var p = 0; p < 7; p++) {
      t.piles.push([]);
      for (var i = 0; i <= p; i++) t.piles[p].push([pack.pop(), i === p]);
    }
    t.stock = pack;
    return t;
  }

  function copy(t) {
    return { stock: t.stock.slice(), waste: t.waste.slice(), home: t.home.map(function (h) { return h.slice(); }),
             piles: t.piles.map(function (p) { return p.map(function (x) { return x.slice(); }); }) };
  }

  function homeTakes(c) { return s.home[suit(c)].length === rank(c); }

  function pileTakes(p, c) {
    var pile = s.piles[p];
    if (!pile.length) return rank(c) === 12;
    var top = pile[pile.length - 1];
    return top[1] && rank(top[0]) === rank(c) + 1 && red(top[0]) !== red(c);
  }

  // What is picked up: the waste's top card, a foundation's top card, or a pile's face-up
  // cards from the one clicked.
  function cards(from) {
    if (from.where === 'waste') return s.waste.length ? [s.waste[s.waste.length - 1]] : [];
    if (from.where === 'home') { var h = s.home[from.at]; return h.length ? [h[h.length - 1]] : []; }
    return s.piles[from.at].slice(from.index).map(function (x) { return x[0]; });
  }

  function take(from, n) {
    if (from.where === 'waste') s.waste.pop();
    else if (from.where === 'home') s.home[from.at].pop();
    else {
      var pile = s.piles[from.at];
      pile.splice(pile.length - n, n);
      if (pile.length && !pile[pile.length - 1][1]) pile[pile.length - 1][1] = true;
    }
  }

  function remember() {
    history.push(copy(s));
    if (history.length > 500) history.shift();
  }

  // A move of what from holds to a foundation or a pile - FALSE when the rules forbid it.
  function move(from, to) {
    var list = cards(from), c = list[0];
    if (c === undefined) return false;
    if (to.where === 'home') {
      if (list.length !== 1 || to.at !== suit(c) || !homeTakes(c)) return false;
    } else if (to.where === 'pile') {
      if (from.where === 'pile' && from.at === to.at) return false;
      if (!pileTakes(to.at, c)) return false;
    } else return false;
    remember();
    var turned = from.where === 'pile' && from.index > 0 && !s.piles[from.at][from.index - 1][1];
    take(from, list.length);
    if (to.where === 'home') s.home[to.at].push(c);
    else list.forEach(function (x) { s.piles[to.at].push([x, true]); });
    moves++;
    chosen = null;
    var said = (list.length > 1 ? 'Moved ' + name(c) + ' and ' + (list.length - 1) + (list.length === 2 ? ' card' : ' cards') + ' on it' : 'Moved ' + name(c))
             + (to.where === 'home' ? ' home' : s.piles[to.at].length === list.length ? ' to an empty pile' : ' onto ' + name(s.piles[to.at][s.piles[to.at].length - list.length - 1][0]))
             + (turned ? ', and turned up ' + name(s.piles[from.at][from.index - 1][0]) : '') + '.';
    after(said);
    return true;
  }

  // Where a card goes when it is clicked twice: home first, then a pile it fits on, from the left.
  function best(from) {
    var list = cards(from);
    if (list.length === 1 && homeTakes(list[0])) return { where: 'home', at: suit(list[0]) };
    for (var p = 0; p < 7; p++) if (!(from.where === 'pile' && from.at === p) && pileTakes(p, list[0])
                                   && !(rank(list[0]) === 12 && from.where === 'pile' && from.index === 0)) return { where: 'pile', at: p };
    return null;
  }

  function turnStock() {
    if (k.over()) return;
    remember();
    chosen = null;
    if (s.stock.length) {
      for (var i = 0; i < DRAW && s.stock.length; i++) s.waste.push(s.stock.pop());
      moves++;
      after('Turned up ' + name(s.waste[s.waste.length - 1]) + '.');
    } else if (s.waste.length) {
      s.stock = s.waste.reverse();
      s.waste = [];
      after('The waste goes back to the stock.');
    } else { history.pop(); k.status('The stock and the waste are empty.'); }
  }

  // Every card that can go home goes, until none can.
  function sendHome() {
    if (k.over()) return;
    var sent = 0, went = true;
    remember();
    while (went) {
      went = false;
      var tops = [{ where: 'waste' }];
      for (var p = 0; p < 7; p++) if (s.piles[p].length) tops.push({ where: 'pile', at: p, index: s.piles[p].length - 1 });
      for (var i = 0; i < tops.length; i++) {
        var c = cards(tops[i])[0];
        if (c !== undefined && homeTakes(c)) {
          take(tops[i], 1);
          s.home[suit(c)].push(c);
          sent++; moves++; went = true;
        }
      }
    }
    if (!sent) { history.pop(); k.status('No card can go home now.'); return; }
    chosen = null;
    after(sent === 1 ? 'One card went home.' : sent + ' cards went home.');
  }

  function homeCount() { return s.home.reduce(function (a, h) { return a + h.length; }, 0); }

  function after(said) {
    draw();
    if (homeCount() === 52) {
      homeBtn.disabled = undoBtn.disabled = true;
      return k.won('All 52 cards are home, in ' + moves + ' moves!');
    }
    k.status(said + ' ' + homeCount() + ' of 52 home.');
  }

  // The board, drawn again after every move; the focus goes back to the place it was at.
  function cardEl(c, up, key, label) {
    var b = k.el('button', 'kl-card' + (up ? ' is-up' + (red(c) ? ' is-red' : '') : ' is-down'));
    b.type = 'button';
    b.setAttribute('data-key', key);
    if (up) {
      b.setAttribute('data-card', c);
      b.appendChild(k.el('span', 'kl-corner', RANKS[rank(c)] + SIGNS[suit(c)]));
      b.appendChild(k.el('span', 'kl-pip', SIGNS[suit(c)]));
      b.firstChild.setAttribute('aria-hidden', 'true');
      b.lastChild.setAttribute('aria-hidden', 'true');
    }
    b.setAttribute('aria-label', label);
    return b;
  }

  function slotEl(cls, key, label, text) {
    var b = k.el('button', 'kl-slot ' + cls, text);
    b.type = 'button';
    b.setAttribute('data-key', key);
    b.setAttribute('aria-label', label);
    return b;
  }

  function same(a, b) { return a && b && a.where === b.where && a.at === b.at && a.index === b.index; }

  function draw() {
    var had = k.root.contains(document.activeElement) && document.activeElement.getAttribute('data-key');
    table.textContent = '';
    var top = k.el('div', 'kl-top'), stockEl, wasteEl = k.el('div', 'kl-waste');
    stockEl = s.stock.length ? cardEl(0, false, 'stock', 'Stock, ' + s.stock.length + ' cards - turn up ' + (DRAW === 1 ? 'one' : DRAW))
                             : slotEl('kl-stock-empty', 'stock', s.waste.length ? 'Stock, empty - turn the waste back' : 'Stock, empty', s.waste.length ? '↻' : '');
    stockEl.classList.add('kl-stock');
    stockEl.addEventListener('click', turnStock);
    top.appendChild(stockEl);
    var shown = s.waste.slice(-DRAW);
    if (!shown.length) wasteEl.appendChild(slotEl('kl-blank', 'waste', 'Waste, empty'));
    shown.forEach(function (c, i) {
      var last = i === shown.length - 1, b;
      if (last) {
        b = cardEl(c, true, 'waste', 'Waste: ' + name(c));
        b.addEventListener('click', function () { pick({ where: 'waste' }); });
        if (chosen && chosen.where === 'waste') b.classList.add('is-chosen');
      } else {
        b = k.el('span', 'kl-card is-up is-under' + (red(c) ? ' is-red' : ''));
        b.appendChild(k.el('span', 'kl-corner', RANKS[rank(c)] + SIGNS[suit(c)]));
        b.setAttribute('aria-hidden', 'true');
      }
      wasteEl.appendChild(b);
    });
    top.appendChild(wasteEl);
    top.appendChild(k.el('span', 'kl-gap'));
    s.home.forEach(function (h, f) {
      var b, to = { where: 'home', at: f };
      if (h.length) {
        b = cardEl(h[h.length - 1], true, 'home' + f, 'Foundation of ' + SUITS[f] + ': up to ' + name(h[h.length - 1]).replace('the ', ''));
        if (chosen && chosen.where === 'home' && chosen.at === f) b.classList.add('is-chosen');
      } else b = slotEl('kl-home', 'home' + f, 'Foundation of ' + SUITS[f] + ', empty', SIGNS[f]);
      if (chosen && cards(chosen).length === 1 && cards(chosen)[0] !== undefined && suit(cards(chosen)[0]) === f && homeTakes(cards(chosen)[0])) b.classList.add('is-target');
      b.addEventListener('click', function () { if (chosen && !(chosen.where === 'home' && chosen.at === f)) { if (!move(chosen, to)) refuse(); } else if (h.length) pick({ where: 'home', at: f }); });
      top.appendChild(b);
    });
    table.appendChild(top);
    var row = k.el('div', 'kl-piles');
    s.piles.forEach(function (pile, p) {
      var col = k.el('div', 'kl-pile'), to = { where: 'pile', at: p };
      col.setAttribute('role', 'group');
      col.setAttribute('aria-label', 'Pile ' + (p + 1));
      if (chosen && pileTakes(p, cards(chosen)[0]) && !(chosen.where === 'pile' && chosen.at === p)) col.classList.add('is-target');
      if (!pile.length) {
        var e = slotEl('kl-blank', 'pile' + p, 'Pile ' + (p + 1) + ', empty');
        e.addEventListener('click', function () { if (chosen && !move(chosen, to)) refuse(); });
        col.appendChild(e);
      }
      pile.forEach(function (x, i) {
        var at = { where: 'pile', at: p, index: i }, b;
        if (x[1]) {
          b = cardEl(x[0], true, 'pile' + p + '-' + i, 'Pile ' + (p + 1) + ': ' + name(x[0]) + (i < pile.length - 1 ? ', ' + (pile.length - 1 - i) + ' on it' : ''));
          if (chosen && chosen.where === 'pile' && chosen.at === p && i >= chosen.index) b.classList.add('is-chosen');
          b.addEventListener('click', function () {
            // With a card picked up, the top card of another pile is where it goes when it may;
            // else the card clicked is picked up instead.
            if (chosen && !same(chosen, at) && !(chosen.where === 'pile' && chosen.at === p) && i === pile.length - 1 && move(chosen, to)) return;
            pick(at);
          });
        } else {
          b = cardEl(x[0], false, 'pile' + p + '-' + i, 'Pile ' + (p + 1) + ': a card face down');
          b.setAttribute('aria-disabled', 'true');
          b.tabIndex = -1;
        }
        if (i > 0) b.classList.add(pile[i - 1][1] ? 'after-up' : 'after-down');
        col.appendChild(b);
      });
      row.appendChild(col);
    });
    table.appendChild(row);
    homeBtn.disabled = k.over();
    undoBtn.disabled = k.over() || !history.length;
    if (had) {
      var back = table.querySelector('[data-key="' + had + '"]');
      if (!back && /^pile\d/.test(had)) back = [].filter.call(table.querySelectorAll('[data-key^="' + had.replace(/-.*/, '') + '"]'), function (b) { return !b.classList.contains('is-down'); }).pop();
      if (back) back.focus();
    }
  }

  function refuse() { k.status('It cannot go there - pick another place, or the card again to put it down.'); }

  // A card clicked: picked up, sent on when it was picked already, or put down.
  function pick(at) {
    if (k.over()) return;
    if (same(chosen, at)) {
      var to = best(at);
      if (to && move(at, to)) return;
      chosen = null;
      draw();
      return k.status('Put down - it has no place to go.');
    }
    if (!cards(at).length) return;
    chosen = at;
    draw();
    var list = cards(at);
    k.status('Picked up ' + name(list[0]) + (list.length > 1 ? ' and the ' + (list.length - 1) + ' on it' : '') + ' - click where it goes, or it again to send it on.');
  }

  function newGame() {
    k.fresh();
    s = deal();
    history = [];
    chosen = null;
    moves = 0;
    table = k.el('div', 'kl-table');
    k.board.appendChild(table);
    draw();
    k.status('Build the four suits up from ace to king - click a card, then where it goes.');
  }

  k.button('New deal', function () { newGame(); });
  homeBtn = k.button('Home', sendHome);
  homeBtn.setAttribute('aria-label', 'Send every card that can go home');
  undoBtn = k.button('Undo', function () {
    if (!history.length || k.over()) return;
    s = history.pop();
    chosen = null;
    moves++;
    draw();
    k.status('Took the last move back. ' + homeCount() + ' of 52 home.');
  });
  newGame();
});
