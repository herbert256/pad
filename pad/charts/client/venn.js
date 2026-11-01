// venn.php in the browser: how two or three sets overlap (core.js says how these files are
// written).
// needs: core

(function (C) {

  'use strict';

  C.padChartVenn = function (rows, width, height) {

    var guess   = C.padChartGuess(rows);
    var numbers = guess[0], names = guess[1];
    var used    = [];
    var field   = C.padChartPick('sets',  names,   used);
    var value   = C.padChartPick('value', numbers, used);
    var sets = [], sizes = [], desc = [];

    C.values(rows).forEach(function (row) {

      row = C.padChartRow(row);
      var v = C.get(row, value);

      if (!C.padChartFinite(v) || C.float(v) < 0)
        return;

      var members = [];
      C.padChartFields(C.padChartText(row, field).split('&').join(',')).forEach(function (m) {
        if (members.indexOf(m) < 0) members.push(m);
      });

      if (!members.length)
        return;

      if (members.length === 1 && sets.indexOf(members[0]) < 0)
        sets.push(members[0]);

      sizes.push([members, C.num(v)]);
      desc.push(members.join(' & ') + ': ' + C.padChartNumber(C.num(v)));

    });

    var named = sets.slice();

    sizes.forEach(function (e) {
      if (e[0].length === 1 && e[1] <= 0)
        sets = sets.filter(function (set) { return e[0].indexOf(set) < 0; });
    });

    if (sets.length > 3) {
      C.padError('the venn chart has ' + sets.length + ' sets - three at most');
      sets = sets.slice(0, 3);
    }

    // $size: a PHP array, keyed '0', '0,1', '0,1,2' - a Map by that text, in its order.

    var size = new Map();

    sizes.forEach(function (e) {

      var key = [];

      for (var m = 0; m < e[0].length; m++) {
        var at = sets.indexOf(e[0][m]);
        if (at < 0) {
          if (named.indexOf(e[0][m]) < 0)
            C.padError("the venn chart has an overlap with '" + C.padMakeSafe(e[0][m], 30) + "', a set without a row of its own");
          return;
        }
        key.push(at);
      }

      key.sort(function (a, b) { return a - b; });
      size.set(key.join(','), e[1]);

    });

    var n = sets.length;
    var sz = function (k) { var v = size.get(String(k)); return v === undefined ? 0 : v; };

    for (var i = 0; i < n; i++)
      if (sz(i) <= 0)
        return '';

    if (!n || !isFinite(C.sum(size)))
      return '';

    var pair = function (a, b) { return Math.min(sz(a + ',' + b), size.get(String(a)), size.get(String(b))); };
    var all  = (n === 3) ? Math.min(sz('0,1,2'), pair(0, 1), pair(0, 2), pair(1, 2)) : 0;
    var only = new Map();

    if (n === 1)
      only.set(1, size.get('0'));
    else if (n === 2)
      only = new Map([[1, size.get('0') - pair(0, 1)], [2, size.get('1') - pair(0, 1)], [3, pair(0, 1)]]);
    else
      only = new Map([[1, size.get('0') - pair(0, 1) - pair(0, 2) + all],
                      [2, size.get('1') - pair(0, 1) - pair(1, 2) + all],
                      [4, size.get('2') - pair(0, 2) - pair(1, 2) + all],
                      [3, pair(0, 1) - all],
                      [5, pair(0, 2) - all],
                      [6, pair(1, 2) - all],
                      [7, all]]);

    only.forEach(function (v, k) { only.set(k, Math.max(0, v)); });

    var title = C.str(C.padTagParm('title', sets.join(', ')));

    var open = C.padChartOpen('venn', [sets, size], desc, title, width, height);
    var svg  = open[1];

    var radius = [];
    for (i = 0; i < n; i++)
      radius[i] = Math.sqrt(size.get(String(i)) / Math.PI);

    var apart = function (a, b) { return C.padChartVennDistance(radius[a], radius[b], pair(a, b)); };

    var at = [[0, 0]];

    if (n >= 2)
      at[1] = [apart(0, 1), 0];

    if (n === 3) {
      var ab = at[1][0];
      var ac = apart(0, 2);
      var bc = apart(1, 2);
      var x  = (ab > 1e-9) ? (ac * ac - bc * bc + ab * ab) / (2 * ab) : ac;
      at[2]  = [x, Math.sqrt(Math.max(0, ac * ac - x * x))];
    }

    var nameW = 0;
    sets.forEach(function (set) { nameW = Math.max(nameW, C.padChartWidth(set)); });

    var x0 = Infinity, y0 = Infinity, x1 = -Infinity, y1 = -Infinity;

    at.forEach(function (p, i) {
      x0 = Math.min(x0, p[0] - radius[i]);
      x1 = Math.max(x1, p[0] + radius[i]);
      y0 = Math.min(y0, p[1] - radius[i]);
      y1 = Math.max(y1, p[1] + radius[i]);
    });

    var mx    = Math.min(width * 0.25, nameW + 14);
    var my    = 22;
    var scale = Math.max(1e-9, Math.min((width - 2 * mx) / (x1 - x0), (height - 2 * my) / (y1 - y0)));
    var dx    = width  / 2 - (x0 + x1) / 2 * scale;
    var dy    = height / 2 - (y0 + y1) / 2 * scale;
    var c     = [];
    var r     = [];

    at.forEach(function (p, i) {
      c[i] = [dx + p[0] * scale, dy + p[1] * scale];
      r[i] = radius[i] * scale;
    });

    c.forEach(function (p, i) {
      svg += '<circle class="pc-mark ' + C.padChartSlot(i) + '" fill-opacity="0.25" cx="' + C.padChartXY(p[0]) + '" cy="' + C.padChartXY(p[1]) + '" r="' + C.padChartXY(r[i]) + '">'
           + '<title>' + C.padChartAttr(sets[i] + ': ' + C.padChartNumber(size.get(String(i)))) + '</title></circle>';
    });

    c.forEach(function (p, i) {
      svg += '<circle class="pc-l' + (i + 1) + '" cx="' + C.padChartXY(p[0]) + '" cy="' + C.padChartXY(p[1]) + '" r="' + C.padChartXY(r[i]) + '"/>';
    });

    var mid = [c.reduce(function (s, p) { return s + p[0]; }, 0) / n, c.reduce(function (s, p) { return s + p[1]; }, 0) / n];

    c.forEach(function (p, i) {
      var x = p[0], y = p[1];
      var ux  = x - mid[0];
      var uy  = y - mid[1];
      var len = Math.sqrt(ux * ux + uy * uy);
      var u   = (len > 1e-6) ? [ux / len, uy / len] : [Math.sin(2 * Math.PI * i / n), -Math.cos(2 * Math.PI * i / n)];
      ux = u[0]; uy = u[1];
      var anchor = (ux > 0.35) ? 'start' : (ux < -0.35 ? 'end' : 'middle');
      var tx     = x + ux * (r[i] + 6);
      var ty     = y + uy * (r[i] + 6) + (uy > 0.35 ? 11 : (uy < -0.35 ? 0 : 4));
      var room   = (anchor === 'end') ? tx - 4 : (anchor === 'start' ? width - tx - 4 : 2 * Math.min(tx, width - tx) - 8);
      svg += '<text x="' + C.padChartXY(tx) + '" y="' + C.padChartXY(ty) + '" text-anchor="' + anchor + '">' + C.padChartAttr(C.padChartFit(sets[i], Math.min(width * 0.4, room))) + '</text>';
    });

    var best = new Map();
    var grid = 60;

    for (var gx = 0; gx <= grid; gx++)
      for (var gy = 0; gy <= grid; gy++) {

        var px    = dx + (x0 + (x1 - x0) * gx / grid) * scale;
        var py    = dy + (y0 + (y1 - y0) * gy / grid) * scale;
        var mask  = 0;
        var clear = Infinity;

        for (i = 0; i < c.length; i++) {
          var ex = px - c[i][0], ey = py - c[i][1];
          var d  = Math.sqrt(ex * ex + ey * ey);
          clear  = Math.min(clear, Math.abs(d - r[i]));
          if (d < r[i])
            mask |= 1 << i;
        }

        if (mask && (!best.has(mask) || clear > best.get(mask)[2]))
          best.set(mask, [px, py, clear]);

      }

    Array.from(best.keys()).sort(function (a, b) { return a - b; }).forEach(function (mask) {

      var b = best.get(mask);
      var v = only.has(mask) ? only.get(mask) : 0;

      if (v <= 0 || b[2] < 8)
        return;

      var inside = [];
      sets.forEach(function (set, i) { if (mask & (1 << i)) inside.push(set); });

      var text = C.padChartNumber(v);

      if (C.padChartWidth(text) > b[2] * 2.4)
        return;

      svg += '<text x="' + C.padChartXY(b[0]) + '" y="' + C.padChartXY(b[1] + 4) + '" text-anchor="middle">' + text
           + '<title>' + C.padChartAttr(inside.join(' & ') + (n > inside.length ? ' only' : '') + ': ' + text) + '</title></text>';

    });

    return svg + '</svg>';

  };

  C.padChartVennDistance = function (r1, r2, overlap) {

    var small = Math.min(r1, r2);

    if (overlap <= 0)
      return r1 + r2 + small * 0.15;

    if (overlap >= Math.PI * small * small - 1e-12)
      return Math.abs(r1 - r2);

    var low  = Math.abs(r1 - r2);
    var high = r1 + r2;

    for (var step = 0; step < 60; step++) {
      var d = (low + high) / 2;
      if (C.padChartVennLens(r1, r2, d) > overlap)
        low = d;
      else
        high = d;
    }

    return (low + high) / 2;

  };

  C.padChartVennLens = function (r1, r2, d) {

    if (d >= r1 + r2)
      return 0;

    if (d <= Math.abs(r1 - r2)) {
      var m = Math.min(r1, r2);
      return Math.PI * (m * m);
    }

    var a = r1 * r1 * Math.acos(Math.max(-1, Math.min(1, (d * d + r1 * r1 - r2 * r2) / (2 * d * r1))));
    var b = r2 * r2 * Math.acos(Math.max(-1, Math.min(1, (d * d + r2 * r2 - r1 * r1) / (2 * d * r2))));
    var k = 0.5 * Math.sqrt(Math.max(0, (-d + r1 + r2) * (d + r1 - r2) * (d - r1 + r2) * (d + r1 + r2)));

    return a + b - k;

  };

})(typeof window !== 'undefined' ? window.PadChart : globalThis.PadChart);
