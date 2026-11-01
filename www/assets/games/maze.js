PadGames.register('maze', function (k, cfg) {
  var R = cfg.height || 12, C = cfg.width || 18, S = 24;
  var open, at, steps, svg, you, trail, NS = 'http://www.w3.org/2000/svg';

  function svgEl(tag, attrs) {
    var e = document.createElementNS(NS, tag);
    for (var a in attrs) e.setAttribute(a, attrs[a]);
    return e;
  }

  // Dug by a random depth-first walk: every cell reached once, so one way joins any two.
  // open[i] holds the sides a cell is open to: 1 up, 2 right, 4 down, 8 left.
  function dig() {
    open = new Array(R * C).fill(0);
    var seen = new Array(R * C).fill(false), stack = [0];
    seen[0] = true;
    while (stack.length) {
      var i = stack[stack.length - 1], r = Math.floor(i / C), c = i % C, next = [];
      if (r > 0 && !seen[i - C]) next.push([i - C, 1, 4]);
      if (c < C - 1 && !seen[i + 1]) next.push([i + 1, 2, 8]);
      if (r < R - 1 && !seen[i + C]) next.push([i + C, 4, 1]);
      if (c > 0 && !seen[i - 1]) next.push([i - 1, 8, 2]);
      if (!next.length) { stack.pop(); continue; }
      var n = k.pick(next);
      open[i] |= n[1];
      open[n[0]] |= n[2];
      seen[n[0]] = true;
      stack.push(n[0]);
    }
  }

  function draw() {
    svg = svgEl('svg', { viewBox: '-2 -2 ' + (C * S + 4) + ' ' + (R * S + 4), width: C * S + 4, height: R * S + 4, role: 'img', 'aria-label': 'A maze of ' + R + ' by ' + C + ' cells' });
    svg.appendChild(svgEl('rect', { x: (C - 1) * S + 3, y: (R - 1) * S + 3, width: S - 6, height: S - 6, rx: 4, 'class': 'mz-goal' }));
    var d = '';
    for (var i = 0; i < R * C; i++) {
      var x = (i % C) * S, y = Math.floor(i / C) * S;
      if (!(open[i] & 1)) d += 'M' + x + ',' + y + 'h' + S;
      if (!(open[i] & 8)) d += 'M' + x + ',' + y + 'v' + S;
      if (i % C === C - 1 && !(open[i] & 2)) d += 'M' + (x + S) + ',' + y + 'v' + S;
      if (Math.floor(i / C) === R - 1 && !(open[i] & 4)) d += 'M' + x + ',' + (y + S) + 'h' + S;
    }
    trail = svgEl('polyline', { points: '', 'class': 'mz-trail' });
    svg.appendChild(trail);
    svg.appendChild(svgEl('path', { d: d, 'class': 'mz-wall' }));
    you = svgEl('circle', { r: S / 2 - 5, 'class': 'mz-you' });
    svg.appendChild(you);
    k.board.appendChild(svg);
    place();
  }

  var path;

  function place() {
    you.setAttribute('cx', (at % C) * S + S / 2);
    you.setAttribute('cy', Math.floor(at / C) * S + S / 2);
    trail.setAttribute('points', path.map(function (i) { return ((i % C) * S + S / 2) + ',' + (Math.floor(i / C) * S + S / 2); }).join(' '));
  }

  function go(bit, delta) {
    return function () {
      if (k.over()) return;
      if (!(open[at] & bit)) return;
      at += delta;
      steps++;
      // Stepping back along the trail takes the step off it.
      if (path.length > 1 && path[path.length - 2] === at) path.pop(); else path.push(at);
      place();
      if (at === R * C - 1) return k.won('Out in ' + steps + ' steps!');
      k.status(steps + (steps === 1 ? ' step' : ' steps'));
    };
  }

  var keys = { ArrowUp: go(1, -C), ArrowRight: go(2, 1), ArrowDown: go(4, C), ArrowLeft: go(8, -1) };
  keys.w = keys.ArrowUp; keys.d = keys.ArrowRight; keys.s = keys.ArrowDown; keys.a = keys.ArrowLeft;

  function newGame() {
    k.fresh();
    dig();
    at = 0;
    steps = 0;
    path = [0];
    draw();
    k.board.tabIndex = 0;
    k.status('Reach the green square - arrow keys, swipe or the buttons');
  }

  k.keys(keys);
  k.swipe(function (key) { keys[key](); });
  [['Up', 'ArrowUp', '↑'], ['Left', 'ArrowLeft', '←'], ['Down', 'ArrowDown', '↓'], ['Right', 'ArrowRight', '→']].forEach(function (b) {
    var btn = k.button(b[2], keys[b[1]], 'mz-pad');
    btn.setAttribute('aria-label', b[0]);
  });
  k.button('New maze', newGame);
  newGame();
});
