// multiples.php in the browser: small multiples, the same small line chart once per group on
// one scale, in a grid (core.js says how these files are written).
// needs: core

(function (C) {

  'use strict';

  C.padChartMultiples = function (rows, width, height) {

    var guess   = C.padChartGuess(rows);
    var numbers = guess[0], names = guess[1];

    var used  = [];
    var by    = C.padChartPick('by',    names,   used);
    var label = C.padChartPick('label', names,   used);
    var value = C.padChartPick('value', numbers, used);

    if (by === '' || value === '') {
      if (C.bool(rows))
        C.padError("small multiples need a field to group by and a number - by='field' and value='field'");
      return '';
    }

    var groups = new Map();
    var labels = new Map();
    var index  = 0;

    C.values(rows).forEach(function (row) {

      row = C.padChartRow(row);
      index++;
      var v = C.get(row, value);

      if (!C.padChartFinite(v))
        return;

      var group = C.key(C.padChartText(row, by));
      var text  = C.padChartText(row, label);
      text = C.key((text !== '') ? text : String(index));

      if (!labels.has(text))
        labels.set(text, labels.size);

      if (!groups.has(group)) groups.set(group, new Map());
      groups.get(group).set(labels.get(text), C.num(v));

    });

    if (!groups.size)
      return '';

    // ksort: the points of a group in the order of their labels.

    groups.forEach(function (points, group) {
      groups.set(group, new Map(Array.from(points.entries()).sort(function (a, b) { return a[0] - b[0]; })));
    });

    labels = Array.from(labels.keys());

    var n   = labels.length;
    var g   = groups.size;
    var all = [];
    var desc = [];

    groups.forEach(function (points) { points.forEach(function (v) { all.push(v); }); });

    groups.forEach(function (points, group) {
      var parts = [];
      points.forEach(function (v, i) { parts.push(C.str(labels[i]) + ' ' + C.padChartNumber(v)); });
      desc.push(C.str(group) + ': ' + parts.join(', '));
    });

    var title = C.str(C.padTagParm('title', C.ucfirst(value) + (label !== '' ? ' by ' + label : '') + ', per ' + by));

    var svg = C.padChartOpen('multiples', [labels, groups], desc, title, width, height)[1];

    // The common scale, four ticks or so, and the margin its labels take.

    var lowest  = all.reduce(function (m, v) { return v < m ? v : m; });
    var highest = all.reduce(function (m, v) { return v > m ? v : m; });

    var ticks = C.padChartTicks(Math.min(0, lowest), Math.max(0, highest), 4);
    var low   = ticks[0];
    var high  = ticks[ticks.length - 1];

    var tickW = 0;
    ticks.forEach(function (tick) { tickW = Math.max(tickW, C.padChartWidth(C.padChartNumber(tick))); });

    var left   = Math.max(20, C.int(Math.ceil(tickW)) + 8);
    var right  = 8;
    var top    = 4;
    var bottom = 18;

    // The grid: columns= when given, else the count whose panels come nearest to 1.6 : 1.

    var columns = C.int(C.padTagParm('columns', 0));
    var cellW, cellH;

    if (columns < 1) {
      var best = -1;
      for (var c = 1; c <= g; c++) {
        cellW = (width  - left - right) / c;
        cellH = (height - top - bottom) / Math.ceil(g / c);
        var score = Math.min(cellW / 1.6, cellH);
        if (score > best) {
          best    = score;
          columns = c;
        }
      }
    }

    // No columns= and no count giving the panels room: PHP's g / 0 throws, and so does this.

    columns   = Math.min(columns, g);
    if (columns === 0) throw new Error('Division by zero');
    var lines = C.int(Math.ceil(g / columns));
    cellW     = (width  - left - right) / columns;
    cellH     = (height - top - bottom) / lines;
    var gap   = 12;
    var head  = 18;

    // One panel: its name above, the grid lines of the common ticks, the wash and the line,
    // and a target per point that carries the tooltip.

    var j = 0;

    groups.forEach(function (points, group) {

      var col   = j % columns;
      var line  = Math.trunc(j / columns);
      var x0    = left + col * cellW;
      var y0    = top + line * cellH + head;
      var plotW = Math.max(1, cellW - gap);
      var plotH = Math.max(1, cellH - head - 6);
      var x     = function (i) { return n > 1 ? x0 + i / (n - 1) * plotW : x0 + plotW / 2; };
      var y     = function (v) { return y0 + (high - v) / (high - low) * plotH; };

      svg += '<text x="' + C.padChartXY(x0) + '" y="' + C.padChartXY(y0 - 6) + '" style="font-weight:600">' + C.padChartAttr(C.padChartFit(group, plotW)) + '</text>';

      ticks.forEach(function (tick) {
        svg += '<line class="pc-grid" x1="' + C.padChartXY(x0) + '" x2="' + C.padChartXY(x0 + plotW) + '" y1="' + C.padChartXY(y(tick)) + '" y2="' + C.padChartXY(y(tick)) + '"/>';
        if (col === 0)
          svg += '<text x="' + C.padChartXY(x0 - 5) + '" y="' + C.padChartXY(y(tick) + 4) + '" text-anchor="end">' + C.padChartNumber(tick) + '</text>';
      });

      if (line === lines - 1 || j + columns >= g) {
        svg += '<text x="' + C.padChartXY(x(0)) + '" y="' + C.padChartXY(y0 + plotH + 14) + '"' + (n > 1 ? '' : ' text-anchor="middle"') + '>'
             + C.padChartAttr(C.padChartFit(labels[0], plotW / 2)) + '</text>';
        if (n > 1)
          svg += '<text x="' + C.padChartXY(x(n - 1)) + '" y="' + C.padChartXY(y0 + plotH + 14) + '" text-anchor="end">'
               + C.padChartAttr(C.padChartFit(labels[n - 1], plotW / 2)) + '</text>';
      }

      var base = y(Math.max(low, Math.min(0, high)));
      var path = '';
      var hits = '';
      var hit  = C.padChartXY(Math.max(4, Math.min(12, n > 1 ? plotW / (n - 1) / 2 : 12)));

      points.forEach(function (v, i) {
        path += (path === '' ? 'M' : 'L') + C.padChartXY(x(i)) + ',' + C.padChartXY(y(v));
        hits += '<circle class="pc-hit" cx="' + C.padChartXY(x(i)) + '" cy="' + C.padChartXY(y(v)) + '" r="' + hit + '"><title>'
              + C.padChartAttr(C.str(group) + ' · ' + C.str(labels[i]) + ': ' + C.padChartNumber(v)) + '</title></circle>';
      });

      var keys  = Array.from(points.keys());
      var first = keys[0];
      var last  = keys[keys.length - 1];

      svg += '<path class="pc-area" d="' + path + 'L' + C.padChartXY(x(last)) + ',' + C.padChartXY(base) + 'L' + C.padChartXY(x(first)) + ',' + C.padChartXY(base) + 'Z"/>'
           + '<line class="pc-axis" x1="' + C.padChartXY(x0) + '" x2="' + C.padChartXY(x0 + plotW) + '" y1="' + C.padChartXY(base) + '" y2="' + C.padChartXY(base) + '" stroke-opacity="0.5"/>'
           + '<path class="pc-line" d="' + path + '"/>'
           + '<circle class="pc-dot" cx="' + C.padChartXY(x(last)) + '" cy="' + C.padChartXY(y(points.get(last))) + '" r="3"/>'
           + hits;

      j++;

    });

    return svg + '</svg>';

  };

})(typeof window !== 'undefined' ? window.PadChart : globalThis.PadChart);
