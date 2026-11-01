// sankey.php in the browser: flows from node to node, in columns (core.js says how these
// files are written).
// needs: core

(function (C) {

  'use strict';

  C.padChartSankey = function (rows, width, height) {

    var guess   = C.padChartGuess(rows);
    var numbers = guess[0], names = guess[1];
    var used    = [];
    var sField  = C.padChartPick('source', names,   used);
    var tField  = C.padChartPick('target', names,   used);
    var vField  = C.padChartPick('value',  numbers, used);

    if (sField === '' || tField === '')
      C.padError("the sankey has no field for the two ends of a flow - source='name', target='name'");

    var title = C.str(C.padTagParm('title', 'Flows from ' + (C.bool(sField) ? sField : 'source') + ' to ' + (C.bool(tField) ? tField : 'target')));

    var links = new Map();

    C.values(rows).forEach(function (row) {

      row = C.padChartRow(row);
      var v = C.get(row, vField);
      var s = C.padChartText(row, sField);
      var t = C.padChartText(row, tField);

      if (!C.padChartFinite(v) || C.float(v) <= 0 || s === t || C.trim(s) === '' || C.trim(t) === '')
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

    // The nodes are PHP array keys: a Map by the name, the name standing for its key.

    var nodes = new Map(), inn = new Map(), out = new Map(), next = new Map(), degree = new Map();

    links.forEach(function (l) {
      [l[0], l[1]].forEach(function (node) {
        if (!nodes.has(node)) {
          nodes.set(node, nodes.size);
          inn.set(node, 0); out.set(node, 0);
          next.set(node, []);
          degree.set(node, 0);
        }
      });
      out.set(l[0], out.get(l[0]) + l[2]);
      inn.set(l[1], inn.get(l[1]) + l[2]);
      next.get(l[0]).push(l[1]);
      degree.set(l[1], degree.get(l[1]) + 1);
    });

    var column = new Map();
    nodes.forEach(function (i, node) { column.set(node, 0); });
    var queue = [];

    degree.forEach(function (count, node) { if (!count) queue.push(node); });

    var done = 0;

    while (queue.length) {
      var node = queue.shift();
      done++;
      next.get(node).forEach(function (to) {
        column.set(to, Math.max(column.get(to), column.get(node) + 1));
        degree.set(to, degree.get(to) - 1);
        if (!degree.get(to))
          queue.push(to);
      });
    }

    if (done < nodes.size) {
      if (!(C.spec && C.spec.check === false)) {
        var prev = new Map();
        links.forEach(function (l) {
          if (!prev.has(l[1])) prev.set(l[1], []);
          prev.get(l[1]).push(l[0]);
        });
        var at = null;
        degree.forEach(function (count, name) { if (at === null && count) at = name; });
        var walked = new Set();
        while (!walked.has(at)) {
          walked.add(at);
          var from = prev.get(at) || [];
          for (var p = 0; p < from.length; p++)
            if (degree.get(from[p])) { at = from[p]; break; }
        }
        C.padError("the flows of the sankey go round in a circle through '" + C.padMakeSafe(at, 40) + "'");
      }
      return '';
    }

    var last = C.hi(Array.from(column.values()));

    nodes.forEach(function (i, name) {
      if (!out.get(name)) column.set(name, last);
    });

    var desc = links.map(function (l) { return l[0] + ' → ' + l[1] + ': ' + C.padChartNumber(l[2]); });

    var open = C.padChartOpen('sankey', links, desc, title, width, height);
    var svg  = open[1];

    var size = new Map();
    nodes.forEach(function (i, name) { size.set(name, Math.max(inn.get(name), out.get(name))); });

    var columns = [];
    for (var c = 0; c <= last; c++) columns.push([]);
    nodes.forEach(function (i, name) { columns[column.get(name)].push(name); });

    var top   = 10;
    var plotH = Math.max(1, height - 20);
    var nodeW = 12;
    var most  = C.hi(columns.map(function (list) { return list.length; }));
    var gap   = Math.min(10, plotH * 0.4 / Math.max(1, most - 1));
    var scale = Infinity;
    var sizeSum = function (list) { return list.reduce(function (sum, name) { return sum + size.get(name); }, 0); };

    columns.forEach(function (list) {
      if (list.length)
        scale = Math.min(scale, (plotH - (list.length - 1) * gap) / sizeSum(list));
    });

    if (!isFinite(scale) || scale <= 0)
      return '';

    var x = new Map(), y = new Map(), h = new Map(), into = new Map();

    links.forEach(function (l) {
      if (!into.has(l[1])) into.set(l[1], []);
      into.get(l[1]).push([l[0], l[2]]);
    });

    columns.forEach(function (list, c) {

      if (c > 0) {
        var centre = new Map();
        list.forEach(function (name) {
          var sum = 0, weight = 0;
          (into.get(name) || []).forEach(function (e) {
            if (y.has(e[0])) {
              sum    += (y.get(e[0]) + h.get(e[0]) / 2) * e[1];
              weight += e[1];
            }
          });
          centre.set(name, weight ? sum / weight : Infinity);
        });
        list.sort(function (a, b) {
          return spaceship(centre.get(a), centre.get(b)) || spaceship(nodes.get(a), nodes.get(b));
        });
        columns[c] = list;
      }

      var total = sizeSum(list) * scale + (list.length - 1) * gap;
      var at    = top + (plotH - total) / 2;

      list.forEach(function (name) {
        x.set(name, 10 + c * (width - 20 - nodeW) / Math.max(1, last));
        y.set(name, at);
        h.set(name, Math.max(1, size.get(name) * scale));
        at += size.get(name) * scale + gap;
      });

    });

    var order = links.map(function (l, i) { return i; });
    order.sort(function (a, b) { return spaceship(y.get(links[a][1]), y.get(links[b][1])) || spaceship(a, b); });
    var fromY = [], leave = new Map(), enter = new Map();

    order.forEach(function (l) {
      var s = links[l][0];
      fromY[l] = y.get(s) + (leave.has(s) ? leave.get(s) : 0);
      leave.set(s, (leave.has(s) ? leave.get(s) : 0) + links[l][2] * scale);
    });

    order.sort(function (a, b) { return spaceship(y.get(links[a][0]), y.get(links[b][0])) || spaceship(a, b); });

    order.forEach(function (l) {

      var s = links[l][0], t = links[l][1], v = links[l][2];

      var band = v * scale;
      var y0   = fromY[l];
      var y1   = y.get(t) + (enter.has(t) ? enter.get(t) : 0);
      var x0   = x.get(s) + nodeW;
      var x1   = x.get(t);
      var xm   = (x0 + x1) / 2;

      enter.set(t, (enter.has(t) ? enter.get(t) : 0) + band);

      var d = 'M' + C.padChartXY(x0) + ',' + C.padChartXY(y0)
            + 'C' + C.padChartXY(xm) + ',' + C.padChartXY(y0) + ' ' + C.padChartXY(xm) + ',' + C.padChartXY(y1) + ' ' + C.padChartXY(x1) + ',' + C.padChartXY(y1)
            + 'V' + C.padChartXY(y1 + band)
            + 'C' + C.padChartXY(xm) + ',' + C.padChartXY(y1 + band) + ' ' + C.padChartXY(xm) + ',' + C.padChartXY(y0 + band) + ' ' + C.padChartXY(x0) + ',' + C.padChartXY(y0 + band)
            + 'Z';

      svg += '<path class="pc-link" d="' + d + '"><title>' + C.padChartAttr(s + ' → ' + t + ': ' + C.padChartNumber(v)) + '</title></path>';

    });

    var room = (width - 20 - nodeW) / Math.max(1, last) - nodeW - 12;

    nodes.forEach(function (i, name) {

      svg += '<rect class="pc-mark pc-c1" x="' + C.padChartXY(x.get(name)) + '" y="' + C.padChartXY(y.get(name)) + '" width="' + nodeW + '" height="' + C.padChartXY(h.get(name)) + '"><title>'
           + C.padChartAttr(name + ': ' + C.padChartNumber(size.get(name))) + '</title></rect>';

      var end = (column.get(name) === last);

      svg += '<text x="' + C.padChartXY(end ? x.get(name) - 6 : x.get(name) + nodeW + 6) + '" y="' + C.padChartXY(y.get(name) + h.get(name) / 2 + 4) + '"'
           + (end ? ' text-anchor="end"' : '') + '>' + C.padChartAttr(C.padChartFit(name, room)) + '</text>';

    });

    return svg + '</svg>';

  };

  function spaceship(a, b) { return a < b ? -1 : (a > b ? 1 : 0); }

})(typeof window !== 'undefined' ? window.PadChart : globalThis.PadChart);
