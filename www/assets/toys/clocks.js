PadToys.register('clocks', function (k, cfg) {
  var show = cfg.show, zone = cfg.zone || undefined, rate = 1;
  var anchorShown = Date.now(), anchorReal = performance.now(), frozen = null, lastSecond = -1;
  var parts = null;
  try { parts = new Intl.DateTimeFormat('en-GB', { timeZone: zone, hour: 'numeric', minute: 'numeric', second: 'numeric', hourCycle: 'h23' }); }
  catch (e) { parts = new Intl.DateTimeFormat('en-GB', { hour: 'numeric', minute: 'numeric', second: 'numeric', hourCycle: 'h23' }); zone = undefined; }

  // The time the clocks show: the real time, or one set by hand, running at the chosen rate;
  // paused, it stands still.
  function shown() { return frozen !== null ? frozen : anchorShown + (performance.now() - anchorReal) * rate; }
  function anchor(ms) { anchorShown = ms; anchorReal = performance.now(); if (frozen !== null) frozen = ms; }
  function clock(ms) {
    var h = 0, m = 0, s = 0;
    parts.formatToParts(new Date(ms)).forEach(function (p) { if (p.type === 'hour') h = +p.value; else if (p.type === 'minute') m = +p.value; else if (p.type === 'second') s = +p.value; });
    return { h: h % 24, m: m, s: s, ms: ((ms % 1000) + 1000) % 1000 };
  }
  var NUMBERS = ['twelve', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine', 'ten', 'eleven'];
  function words(t) {
    var m5 = Math.floor(t.m / 5) * 5, h = (t.h + (m5 > 30 ? 1 : 0)) % 12, say = { 0: '', 5: 'five past', 10: 'ten past', 15: 'a quarter past', 20: 'twenty past',
      25: 'twenty-five past', 30: 'half past', 35: 'twenty-five to', 40: 'twenty to', 45: 'a quarter to', 50: 'ten to', 55: 'five to' }[m5];
    return 'It is ' + (say ? say + ' ' : '') + NUMBERS[h] + (m5 ? '' : " o'clock");
  }
  function digits(t) { return [t.h, t.m, t.s].map(function (v) { return (v < 10 ? '0' : '') + v; }).join(':'); }

  // The face: the minute marks, the hours in numbers, the hands - the second hand sweeps.
  var face = null, D = 260;
  if (show.indexOf('analog') >= 0) face = k.canvas(D, D, 'A clock face', false);
  function drawFace(t) {
    var c = face, r = D / 2 - 8, cx = D / 2, cy = D / 2, i, a;
    c.fillStyle = k.color('surface');
    c.fillRect(0, 0, D, D);
    c.fillStyle = k.color('canvas');
    c.beginPath(); c.arc(cx, cy, r, 0, Math.PI * 2); c.fill();
    c.strokeStyle = k.color('line'); c.lineWidth = 2; c.stroke();
    for (i = 0; i < 60; i++) {
      a = i / 60 * Math.PI * 2;
      var big = i % 5 === 0, r1 = r - (big ? 14 : 7);
      c.strokeStyle = big ? k.color('ink') : k.color('muted');
      c.lineWidth = big ? 3 : 1;
      c.beginPath(); c.moveTo(cx + Math.sin(a) * r1, cy - Math.cos(a) * r1); c.lineTo(cx + Math.sin(a) * (r - 3), cy - Math.cos(a) * (r - 3)); c.stroke();
    }
    c.fillStyle = k.color('ink');
    c.font = '600 17px system-ui, sans-serif';
    c.textAlign = 'center';
    c.textBaseline = 'middle';
    for (i = 1; i <= 12; i++) { a = i / 12 * Math.PI * 2; c.fillText(String(i), cx + Math.sin(a) * (r - 30), cy - Math.cos(a) * (r - 30)); }
    var sec = t.s + (rate === 1 ? t.ms / 1000 : 0), min = t.m + sec / 60, hr = (t.h % 12) + min / 60;
    hand(c, cx, cy, hr / 12, r * 0.52, 6, k.color('ink'));
    hand(c, cx, cy, min / 60, r * 0.8, 4, k.color('ink'));
    hand(c, cx, cy, sec / 60, r * 0.88, 1.6, k.color('bad'), r * 0.18);
    c.fillStyle = k.color('bad');
    c.beginPath(); c.arc(cx, cy, 5, 0, Math.PI * 2); c.fill();
  }
  function hand(c, cx, cy, turn, len, w, colour, back) {
    var a = turn * Math.PI * 2;
    c.strokeStyle = colour; c.lineWidth = w; c.lineCap = 'round';
    c.beginPath(); c.moveTo(cx - Math.sin(a) * (back || 0), cy + Math.cos(a) * (back || 0)); c.lineTo(cx + Math.sin(a) * len, cy - Math.cos(a) * len); c.stroke();
  }

  // The binary clock: a column per digit of HH:MM:SS, its lights the bits - 8, 4, 2 and 1
  // from the top.
  var bin = null, BW = 210, BH = 260;
  if (show.indexOf('binary') >= 0) bin = k.canvas(BW, BH, 'A binary clock', false);
  function drawBinary(t) {
    var c = bin, cols = [Math.floor(t.h / 10), t.h % 10, Math.floor(t.m / 10), t.m % 10, Math.floor(t.s / 10), t.s % 10], bits = [2, 4, 3, 4, 3, 4];
    c.fillStyle = k.color('canvas');
    c.fillRect(0, 0, BW, BH);
    var on = k.color('accent'), off = k.color('grid'), x0 = 40, step = 26;
    cols.forEach(function (v, i) {
      var x = x0 + i * step + (i >= 2 ? 12 : 0) + (i >= 4 ? 12 : 0);
      for (var b = 0; b < 4; b++) {
        var y = 40 + (3 - b) * 46;
        if (b >= bits[i]) continue;
        c.fillStyle = (v >> b) & 1 ? on : off;
        c.beginPath(); c.arc(x, y, 10, 0, Math.PI * 2); c.fill();
      }
      c.fillStyle = k.color('ink');
      c.font = '600 18px ui-monospace, monospace';
      c.textAlign = 'center';
      c.fillText(String(v), x, BH - 26);
    });
    c.fillStyle = k.color('muted');
    c.font = '11px system-ui, sans-serif';
    ['8', '4', '2', '1'].forEach(function (n, b) { c.fillText(n, 12, 41 + b * 46); });
  }

  // The word clock: a square of letters, the words of the time lit, and a dot for each
  // minute past the five.
  var ROWS = ['ITLISASAMPM', 'ACQUARTERDC', 'TWENTYFIVEX', 'HALFSTENFTO', 'PASTERUNINE', 'ONESIXTHREE', 'FOURFIVETWO', 'EIGHTELEVEN', 'SEVENTWELVE', 'TENSEOCLOCK'];
  var WORDS = { it: [0, 0, 2], is: [0, 3, 2], a: [1, 0, 1], quarter: [1, 2, 7], twenty: [2, 0, 6], fiveM: [2, 6, 4], half: [3, 0, 4], tenM: [3, 5, 3], to: [3, 9, 2],
    past: [4, 0, 4], 9: [4, 7, 4], 1: [5, 0, 3], 6: [5, 3, 3], 3: [5, 6, 5], 4: [6, 0, 4], 5: [6, 4, 4], 2: [6, 8, 3], 8: [7, 0, 5], 11: [7, 5, 6], 7: [8, 0, 5], 0: [8, 5, 6], 10: [9, 0, 3], oclock: [9, 5, 6] };
  var grid = null, letters = [], dots = [];
  if (show.indexOf('words') >= 0) {
    grid = k.el('div', 'ptc-words');
    grid.setAttribute('role', 'img');
    ROWS.forEach(function (row, r) {
      var line = k.el('div', 'ptc-row');
      line.setAttribute('aria-hidden', 'true');
      letters[r] = [];
      for (var c = 0; c < row.length; c++) { var s = k.el('span', 'ptc-letter', row[c]); line.appendChild(s); letters[r].push(s); }
      grid.appendChild(line);
    });
    var dl = k.el('div', 'ptc-dots');
    dl.setAttribute('aria-hidden', 'true');
    for (var d = 0; d < 4; d++) { dots.push(dl.appendChild(k.el('span', 'ptc-dot'))); }
    grid.appendChild(dl);
    k.stage.appendChild(grid);
  }
  function drawWords(t) {
    var m5 = Math.floor(t.m / 5) * 5, h = (t.h + (m5 > 30 ? 1 : 0)) % 12, lit = ['it', 'is'];
    ({ 0: ['oclock'], 5: ['fiveM', 'past'], 10: ['tenM', 'past'], 15: ['a', 'quarter', 'past'], 20: ['twenty', 'past'], 25: ['twenty', 'fiveM', 'past'], 30: ['half', 'past'],
       35: ['twenty', 'fiveM', 'to'], 40: ['twenty', 'to'], 45: ['a', 'quarter', 'to'], 50: ['tenM', 'to'], 55: ['fiveM', 'to'] })[m5].forEach(function (w) { lit.push(w); });
    lit.push(h);
    var on = {};
    lit.forEach(function (w) { var p = WORDS[w]; for (var i = 0; i < p[2]; i++) on[p[0] + ',' + (p[1] + i)] = true; });
    letters.forEach(function (row, r) { row.forEach(function (s, c) { s.classList.toggle('is-lit', !!on[r + ',' + c]); }); });
    dots.forEach(function (d, i) { d.classList.toggle('is-lit', i < t.m % 5); });
    grid.setAttribute('aria-label', 'A word clock: ' + words(t) + (t.m % 5 ? ', and ' + (t.m % 5) + (t.m % 5 === 1 ? ' minute' : ' minutes') : ''));
  }

  function draw() {
    var t = clock(shown());
    if (face) drawFace(t);
    if (bin) drawBinary(t);
    if (t.s !== lastSecond) {
      lastSecond = t.s;
      if (grid) drawWords(t);
      if (face) k.describe(face.canvas, 'A clock face showing ' + digits(t));
      if (bin) k.describe(bin.canvas, 'A binary clock showing ' + digits(t));
      k.info(digits(t) + (zone ? ' in ' + zone : '') + (rate !== 1 ? ' · running ' + rate + ' times fast' : '') + (rate === 1 && frozen === null && Math.abs(shown() - Date.now()) > 2000 ? ' · set by hand' : ''));
    }
  }

  k.loop(function () { draw(); }, draw);

  // Dragging round the face sets the minute hand there; past twelve the hour goes on or back.
  if (face) {
    var lastMin = null;
    k.pointer(face.canvas, {
      down: function (p) { lastMin = null; set(p); },
      move: function (p, e, pressed) { if (pressed) set(p); },
      up: function () { lastMin = null; k.status('Set to ' + digits(clock(shown())).slice(0, 5) + ' - Now puts them right'); }
    });
  }
  function set(p) {
    var a = Math.atan2(p.x - D / 2, -(p.y - D / 2)), m = ((a / (Math.PI * 2)) * 60 + 60) % 60, ms = shown(), t = clock(ms);
    var now = t.m + t.s / 60, d = m - now;
    if (lastMin !== null && Math.abs(d) > 30) d += d > 0 ? -60 : 60;
    if (lastMin === null && Math.abs(d) > 30) d += d > 0 ? -60 : 60;
    lastMin = m;
    anchor(ms + d * 60000);
    lastSecond = -1;
    draw();
  }

  function now() { rate = 1; anchor(Date.now()); speedSelect.querySelector('select').value = '1'; lastSecond = -1; draw(); k.status('The clocks tell the time' + (zone ? ' in ' + zone : '')); }
  k.onPlay = function () { if (frozen !== null) { anchor(frozen); frozen = null; } k.status('Running' + (zone ? ' - the time in ' + zone : '') + ' - drag the minute hand to set them'); };
  k.onPause = function () { frozen = shown(); k.status('Stopped at ' + digits(clock(frozen))); };
  k.keys({ n: now });

  k.row();
  k.playButton();
  k.button('Now', now, 'Back to the real time (N)');
  var speedSelect = k.select('Speed', [['1', 'Real time'], ['60', 'A minute a second'], ['600', 'Ten minutes a second'], ['3600', 'An hour a second']], '1', function (v) {
    var ms = shown(); rate = +v; anchor(ms); lastSecond = -1; k.status(rate === 1 ? 'Real time' : 'Running ' + rate + ' times fast');
  });
  k.theme(function () { lastSecond = -1; draw(); });

  draw();
  k.begin();
  if (!k.playing) frozen = shown();
});
