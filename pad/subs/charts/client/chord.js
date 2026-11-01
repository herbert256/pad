// chord.php in the browser: flows between the members of one group, ribbons across a ring
// (core.js says how these files are written).
// needs: core

(function (C) {

  'use strict';

  C.padChartChord = function (rows, width, height) {

    var guess   = C.padChartGuess(rows);
    var numbers = guess[0], names = guess[1];
    var used    = [];
    var sField  = C.padChartPick('source', names,   used);
    var tField  = C.padChartPick('target', names,   used);
    var vField  = C.padChartPick('value',  numbers, used);

    if (sField === '' || tField === '')
      C.padError("the chord diagram has no field for the two ends of a flow - source='name', target='name'");

    var title = C.str(C.padTagParm('title', 'Flows between ' + (C.bool(sField) ? sField : 'source') + ' and ' + (C.bool(tField) ? tField : 'target')));

    var links = new Map();

    C.values(rows).forEach(function (row) {

      row = C.padChartRow(row);
      var v = (vField === '') ? 1 : C.get(row, vField);
      var s = C.padChartText(row, sField);
      var t = C.padChartText(row, tField);

      if (s === '' || t === '' || s === t || !C.padChartFinite(v) || C.float(v) <= 0)
        return;

      var key = JSON.stringify([s, t]);

      if (links.has(key))
        links.get(key)[2] += C.num(v);
      else
        links.set(key, [s, t, C.num(v)]);

    });

    links = Array.from(links.values());

    if (!links.length)
      return '';

    var nodes = new Map(), inn = new Map(), out = new Map();

    links.forEach(function (l) {
      [l[0], l[1]].forEach(function (node) {
        if (!nodes.has(node)) {
          nodes.set(node, nodes.size);
          inn.set(node, 0); out.set(node, 0);
        }
      });
      out.set(l[0], out.get(l[0]) + l[2]);
      inn.set(l[1], inn.get(l[1]) + l[2]);
    });

    var total = C.sum(out) + C.sum(inn);

    if (!isFinite(total) || total <= 0)
      return '';

    var desc = links.map(function (l) { return l[0] + ' → ' + l[1] + ': ' + C.padChartNumber(l[2]); });

    var open = C.padChartOpen('chord', links, desc, title, width, height);
    var svg  = open[1];

    var n       = nodes.size;
    var longest = 0;
    nodes.forEach(function (i, node) { longest = Math.max(longest, C.padChartWidth(C.padChartFit(node, 110))); });

    var ring  = Math.max(20, Math.min((width - 2 * longest - 28) / 2, (height - 44) / 2));
    var thick = Math.max(4, Math.min(14, ring * 0.07));
    var inner = ring - thick;
    var cx    = width / 2;
    var cy    = height / 2;
    var gap   = (n > 1) ? Math.min(0.04, 2 * Math.PI * 0.25 / n) : 0;
    var scale = (2 * Math.PI - n * gap) / total;

    var start = new Map();
    var a     = gap / 2;

    nodes.forEach(function (i, node) {
      start.set(node, a);
      a += (out.get(node) + inn.get(node)) * scale + gap;
    });

    var ends = new Map();
    nodes.forEach(function (i, node) { ends.set(node, []); });

    links.forEach(function (l, i) {
      var s = l[0], t = l[1];
      ends.get(s).push([(nodes.get(t) - nodes.get(s) + n) % n, 0, i]);
      ends.get(t).push([(nodes.get(s) - nodes.get(t) + n) % n, 1, i]);
    });

    var from = [], to = [];

    ends.forEach(function (list, node) {
      list = list.slice().sort(function (p, q) {
        return spaceship(q[0], p[0]) || spaceship(p[1], q[1]) || spaceship(p[2], q[2]);
      });
      var at = start.get(node);
      list.forEach(function (e) {
        var sweep = links[e[2]][2] * scale;
        if (e[1] === 0)
          from[e[2]] = [at, at + sweep];
        else
          to[e[2]]   = [at, at + sweep];
        at += sweep;
      });
    });

    var at = function (angle) { return C.padChartXY(cx + inner * Math.sin(angle)) + ',' + C.padChartXY(cy - inner * Math.cos(angle)); };
    var r  = C.padChartXY(inner);
    var c  = C.padChartXY(cx) + ',' + C.padChartXY(cy);

    links.forEach(function (l, i) {

      var s0 = from[i][0], s1 = from[i][1];
      var t0 = to[i][0],   t1 = to[i][1];

      var d = 'M' + at(s0) + 'A' + r + ',' + r + ' 0 ' + (s1 - s0 > Math.PI ? 1 : 0) + ' 1 ' + at(s1)
            + 'Q' + c + ' ' + at(t0) + 'A' + r + ',' + r + ' 0 ' + (t1 - t0 > Math.PI ? 1 : 0) + ' 1 ' + at(t1)
            + 'Q' + c + ' ' + at(s0) + 'Z';

      svg += '<path class="pc-mark pc-seg ' + C.padChartSlot(nodes.get(l[0])) + '" fill-opacity="0.6" d="' + d + '"><title>'
           + C.padChartAttr(l[0] + ' → ' + l[1] + ': ' + C.padChartNumber(l[2])) + '</title></path>';

    });

    nodes.forEach(function (i, node) {

      var a0 = start.get(node);
      var a1 = a0 + (out.get(node) + inn.get(node)) * scale;

      svg += '<path class="pc-mark ' + C.padChartSlot(i) + '" d="' + C.padChartArc(cx, cy, inner + 2, ring, a0, a1) + '"><title>'
           + C.padChartAttr(node + ': ' + C.padChartNumber(out.get(node)) + ' out, ' + C.padChartNumber(inn.get(node)) + ' in') + '</title></path>';

      var mid    = (a0 + a1) / 2;
      var sx     = Math.sin(mid);
      var cy0    = Math.cos(mid);
      var x      = cx + (ring + 6) * sx;
      var y      = cy - (ring + 6) * cy0 + 4;
      var anchor = (sx > 0.15) ? '' : ((sx < -0.15) ? ' text-anchor="end"' : ' text-anchor="middle"');

      if (Math.abs(sx) <= 0.15)
        y += (cy0 > 0) ? -4 : 6;

      svg += '<text x="' + C.padChartXY(x) + '" y="' + C.padChartXY(y) + '"' + anchor + '>' + C.padChartAttr(C.padChartFit(node, 110)) + '</text>';

    });

    return svg + '</svg>';

  };

  function spaceship(a, b) { return a < b ? -1 : (a > b ? 1 : 0); }

})(typeof window !== 'undefined' ? window.PadChart : globalThis.PadChart);
