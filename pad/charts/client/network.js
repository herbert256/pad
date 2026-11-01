// network.php in the browser: links between nodes, laid out by force or on a circle (core.js
// says how these files are written). The force layout starts from the sines and cosines of a
// circle rounded to ten decimals, in network.php too: the C library of PHP and the browser's
// Math part in the last bit for some angles, and the hundreds of rounds of the force made that
// a node half a pixel elsewhere.
// needs: core

(function (C) {

  'use strict';

  C.padChartNetwork = function (rows, width, height) {

    var guess   = C.padChartGuess(rows);
    var names   = guess[1];
    var used    = [];
    var sField  = C.padChartPick('source', names, used);
    var tField  = C.padChartPick('target', names, used);
    var vField  = C.str(C.padTagParm('value'));
    var layout  = C.str(C.padTagParm('layout', 'force')).replace(/[A-Z]/g, function (c) { return c.toLowerCase(); });
    var arrows  = C.padTagParm('arrows', false);
    arrows = (arrows !== false && arrows !== '' && arrows !== 0 && arrows !== '0');

    if (sField === '' || tField === '')
      C.padError("the network has no field for the two ends of a link - source='name', target='name'");

    if (layout !== 'force' && layout !== 'radial') {
      C.padError("the network has no layout named '" + C.padMakeSafe(layout, 20) + "' - force or radial");
      layout = 'force';
    }

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
    var desc  = [count + (count === 1 ? ' node, ' : ' nodes, ') + links.length + (links.length === 1 ? ' link' : ' links')
                 + ((layout === 'force' && count > 300) ? ' - more than 300 nodes, set on a circle, not laid out by force' : '')];
    links.forEach(function (l) { desc.push(l[0] + ' → ' + l[1] + (vField !== '' ? ': ' + C.padChartNumber(l[2]) : '')); });

    var open = C.padChartOpen('network', [links, layout, arrows], desc, title, width, height);
    var id   = open[0];
    var svg  = open[1];

    var n     = nodes.size;
    var named = (n <= 40);
    var index = Array.from(nodes.keys());
    var pos   = [];

    index.forEach(function (node, i) {
      var a = 2 * Math.PI * i / n;
      pos[i] = [C.round(Math.sin(a), 10), - C.round(Math.cos(a), 10)];
    });

    var pairs = [];
    links.forEach(function (l) {
      if (l[0] !== l[1]) pairs.push([nodes.get(l[0])[0], nodes.get(l[1])[0]]);
    });

    if (layout === 'force' && n > 2 && n <= 300)
      pos = C.padChartForce(pos, pairs);

    var longest = 0;
    if (named)
      index.forEach(function (node) { longest = Math.max(longest, C.padChartWidth(C.padChartFit(node, 120))); });

    var xs = pos.map(function (p) { return p[0]; });
    var ys = pos.map(function (p) { return p[1]; });
    var x0 = C.lo(xs), xw = C.hi(xs) - x0;
    var y0 = C.lo(ys), yw = C.hi(ys) - y0;

    var padLeft  = (layout === 'radial' && named) ? longest + 18 : 16;
    var padRight = named ? longest + 18 : 16;
    var plotW    = Math.max(1, width - padLeft - padRight);
    var padTop   = (layout === 'radial' && named) ? 30 : 16;
    var plotH    = Math.max(1, height - 2 * padTop);

    var px = function (i) { return xw < 1e-9 ? padLeft + plotW / 2 : padLeft + (pos[i][0] - x0) / xw * plotW; };
    var py = function (i) { return yw < 1e-9 ? padTop + plotH / 2 : padTop + (pos[i][1] - y0) / yw * plotH; };
    var pr = function (node) { return Math.min(12, 4 + 1.5 * Math.sqrt(nodes.get(node)[1])); };

    if (arrows)
      svg += '<defs><marker id="' + id + '-arrow" viewBox="0 0 10 10" refX="9" refY="5" markerWidth="7" markerHeight="7" orient="auto-start-reverse">'
           + '<path class="pc-arrow" d="M0,0L10,5L0,10Z"/></marker></defs>';

    var vMax = C.hi(links.map(function (l) { return l[2]; }));

    links.forEach(function (l) {

      var s = l[0], t = l[1], v = l[2];

      if (s === t)
        return;

      var a  = nodes.get(s)[0];
      var b  = nodes.get(t)[0];
      var ax = px(a), ay = py(a);
      var bx = px(b), by = py(b);
      var length;

      if (arrows && (length = Math.hypot(bx - ax, by - ay)) > 0) {
        var cut = Math.min(length / 2, pr(t) + 2);
        bx -= (bx - ax) / length * cut;
        by -= (by - ay) / length * cut;
      }

      var stroke = (vField !== '' && vMax > 0) ? 1 + 3 * v / vMax : 1.5;

      svg += '<line class="pc-edge" x1="' + C.padChartXY(ax) + '" y1="' + C.padChartXY(ay) + '" x2="' + C.padChartXY(bx) + '" y2="' + C.padChartXY(by) + '" stroke-width="' + C.padChartXY(stroke) + '"'
           + (arrows ? ' marker-end="url(#' + id + '-arrow)"' : '') + '><title>'
           + C.padChartAttr(s + ' → ' + t + (vField !== '' ? ': ' + C.padChartNumber(v) : '')) + '</title></line>';

    });

    index.forEach(function (node, i) {

      var x = px(i);
      var y = py(i);
      var r = pr(node);
      var k = nodes.get(node)[1];

      svg += '<circle class="pc-mark pc-ring pc-c1" cx="' + C.padChartXY(x) + '" cy="' + C.padChartXY(y) + '" r="' + C.padChartXY(r) + '"><title>'
           + C.padChartAttr(node + ': ' + k + (k === 1 ? ' link' : ' links')) + '</title></circle>';

      if (!named)
        return;

      var anchor, lx, ly;

      if (layout === 'radial') {
        var dx = pos[i][0];
        anchor = (dx > 0.1) ? '' : ((dx < -0.1) ? ' text-anchor="end"' : ' text-anchor="middle"');
        lx     = x + (dx > 0.1 ? r + 4 : (dx < -0.1 ? -r - 4 : 0));
        ly     = y + (Math.abs(dx) <= 0.1 ? (pos[i][1] < 0 ? -r - 4 : r + 12) : 4);
      } else {
        anchor = '';
        lx     = x + r + 4;
        ly     = y + 4;
      }

      svg += '<text x="' + C.padChartXY(lx) + '" y="' + C.padChartXY(ly) + '"' + anchor + '>' + C.padChartAttr(C.padChartFit(node, 120)) + '</text>';

    });

    return svg + '</svg>';

  };

  // Fruchterman and Reingold, as network.php has it.

  C.padChartForce = function (pos, pairs) {

    var n      = pos.length;
    var k      = Math.sqrt(4 / n);
    var rounds = Math.max(30, Math.min(300, C.int(3e6 / (n * n))));
    var i, j, dx, dy, f;

    pos = pos.map(function (p) { return [p[0], p[1]]; });

    for (var round = 0; round < rounds; round++) {

      var heat = 0.2 * (1 - round / rounds);
      var move = [];
      for (i = 0; i < n; i++) move.push([0, 0]);

      for (i = 0; i < n; i++)
        for (j = i + 1; j < n; j++) {
          dx = pos[i][0] - pos[j][0];
          dy = pos[i][1] - pos[j][1];
          var d2 = Math.max(1e-6, dx * dx + dy * dy);
          f = k * k / d2;
          move[i][0] += dx * f;  move[i][1] += dy * f;
          move[j][0] -= dx * f;  move[j][1] -= dy * f;
        }

      pairs.forEach(function (p) {
        var a = p[0], b = p[1];
        var ex = pos[a][0] - pos[b][0];
        var ey = pos[a][1] - pos[b][1];
        var g  = Math.sqrt(ex * ex + ey * ey) / k;
        move[a][0] -= ex * g;  move[a][1] -= ey * g;
        move[b][0] += ex * g;  move[b][1] += ey * g;
      });

      for (i = 0; i < n; i++) {
        var mx = move[i][0] - pos[i][0] * k * 0.5;
        var my = move[i][1] - pos[i][1] * k * 0.5;
        var d  = Math.sqrt(mx * mx + my * my);
        if (d > 0) {
          var step = Math.min(d, heat);
          pos[i][0] += mx / d * step;
          pos[i][1] += my / d * step;
        }
      }

    }

    return pos;

  };

})(typeof window !== 'undefined' ? window.PadChart : globalThis.PadChart);
