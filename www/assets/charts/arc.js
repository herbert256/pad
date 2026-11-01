// arc.php in the browser: the arc diagram, links as half circles over the nodes on a line
// (core.js says how these files are written). Its function is padChartArcDiagram; the
// padChartArc of core.js is the ring segment.
// needs: core

(function (C) {

  'use strict';

  C.padChartArcDiagram = function (rows, width, height) {

    var guess  = C.padChartGuess(rows);
    var names  = guess[1];
    var used   = [];
    var sField = C.padChartPick('source', names, used);
    var tField = C.padChartPick('target', names, used);
    var vField = C.str(C.padTagParm('value'));

    if (sField === '' || tField === '')
      C.padError("the arc diagram has no field for the two ends of a link - source='name', target='name'");

    var title = C.str(C.padTagParm('title', 'Links between ' + (C.bool(sField) ? sField : 'source') + ' and ' + (C.bool(tField) ? tField : 'target')));

    var links = new Map(), nodes = new Map();

    C.values(rows).forEach(function (row) {

      row = C.padChartRow(row);
      var s = C.padChartText(row, sField);
      var t = C.padChartText(row, tField);
      var v = (vField === '') ? 1 : C.get(row, vField);

      if (s === '' || t === '' || !C.padChartFinite(v) || C.float(v) < 0)
        return;

      [s, t].forEach(function (node) {
        if (!nodes.has(node)) nodes.set(node, [nodes.size, 0]);
      });

      var key = JSON.stringify([s, t]);

      if (links.has(key))
        links.get(key)[2] += C.num(v);
      else {
        links.set(key, [s, t, C.num(v)]);
        if (s !== t) {
          nodes.get(s)[1]++;
          nodes.get(t)[1]++;
        }
      }

    });

    links = Array.from(links.values());

    if (!links.length)
      return '';

    var count = nodes.size;
    var desc  = [count + (count === 1 ? ' node, ' : ' nodes, ') + links.length + (links.length === 1 ? ' link' : ' links')];
    links.forEach(function (l) { desc.push(l[0] + ' → ' + l[1] + (vField !== '' ? ': ' + C.padChartNumber(l[2]) : '')); });

    var open = C.padChartOpen('arc', links, desc, title, width, height);
    var svg  = open[1];

    var n       = nodes.size;
    var longest = 0;
    nodes.forEach(function (e, node) { longest = Math.max(longest, C.padChartWidth(C.padChartFit(node, 110))); });

    var side = Math.max(16, Math.min(longest / 2 + 4, width / 4));
    var step = (width - 2 * side) / Math.max(1, n - 1);
    var turn, left, right, bottom;

    if (n === 1 || step >= longest + 8) {
      turn   = 0;
      left   = Math.max(16, Math.min(longest / 2 + 4, width / 4));
      right  = left;
      bottom = 30;
    } else if (step >= 14) {
      turn   = 45;
      left   = Math.max(16, longest * 0.71 + 4);
      right  = 16;
      bottom = longest * 0.71 + 30;
    } else {
      turn   = 90;
      left   = right = 16;
      bottom = longest + 30;
    }

    bottom   = Math.min(bottom, height * 0.45);
    var base = height - bottom;
    step     = (n > 1) ? (width - left - right) / (n - 1) : 0;
    var x    = function (node) { return (n > 1) ? left + nodes.get(node)[0] * step : width / 2; };
    var dot  = function (node) { return Math.min(9, Math.max(3, Math.min(step / 2 - 1, 3 + 1.4 * Math.sqrt(nodes.get(node)[1])))); };

    var widest = 0;
    links.forEach(function (l) { widest = Math.max(widest, Math.abs(x(l[1]) - x(l[0])) / 2); });

    var flat = (widest > 0) ? Math.min(1, (base - 12) / widest) : 1;
    var vMax = C.hi(links.map(function (l) { return l[2]; }));

    links.forEach(function (l) {

      var s = l[0], t = l[1], v = l[2];

      if (s === t)
        return;

      var x0 = Math.min(x(s), x(t));
      var x1 = Math.max(x(s), x(t));
      var rx = (x1 - x0) / 2;
      var ry = rx * flat;

      var stroke = (vField !== '' && vMax > 0) ? 1 + 5 * v / vMax : 1.5;

      svg += '<path class="pc-edge" d="M' + C.padChartXY(x0) + ',' + C.padChartXY(base) + 'A' + C.padChartXY(rx) + ',' + C.padChartXY(ry) + ' 0 0 1 ' + C.padChartXY(x1) + ',' + C.padChartXY(base)
           + '" stroke-width="' + C.padChartXY(stroke) + '"><title>'
           + C.padChartAttr(s + ' → ' + t + (vField !== '' ? ': ' + C.padChartNumber(v) : '')) + '</title></path>';

    });

    if (n > 1)
      svg += '<line class="pc-grid" x1="' + C.padChartXY(left) + '" x2="' + C.padChartXY(width - right) + '" y1="' + C.padChartXY(base) + '" y2="' + C.padChartXY(base) + '"/>';

    var room = (turn === 0) ? Math.max(step - 4, longest) : (turn === 45 ? (bottom - 24) / 0.71 : bottom - 24);

    nodes.forEach(function (e, node) {

      var k  = e[1];
      var nx = x(node);
      var r  = dot(node);
      var ty = base + r + 12;

      svg += '<circle class="pc-mark pc-ring pc-c1" cx="' + C.padChartXY(nx) + '" cy="' + C.padChartXY(base) + '" r="' + C.padChartXY(r) + '"><title>'
           + C.padChartAttr(node + ': ' + k + (k === 1 ? ' link' : ' links')) + '</title></circle>';

      var name = C.padChartAttr(C.padChartFit(node, Math.min(110, room)));

      if (turn === 0)
        svg += '<text x="' + C.padChartXY(nx) + '" y="' + C.padChartXY(ty) + '" text-anchor="middle">' + name + '</text>';
      else
        svg += '<text x="' + C.padChartXY(nx + 3) + '" y="' + C.padChartXY(ty - 6) + '" text-anchor="end" transform="rotate(-' + turn + ' ' + C.padChartXY(nx + 3) + ' ' + C.padChartXY(ty - 6) + ')">' + name + '</text>';

    });

    return svg + '</svg>';

  };

})(typeof window !== 'undefined' ? window.PadChart : globalThis.PadChart);
