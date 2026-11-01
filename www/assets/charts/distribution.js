// distribution.php in the browser: the histogram and the box plot, and the parts density.js
// shares with them (core.js says how these files are written).
// needs: core

(function (C) {

  'use strict';

  // min () and max () of a list of numbers, without spreading it over the arguments.

  function low(list)  { return list.reduce(function (m, v) { return v < m ? v : m; }); }
  function high(list) { return list.reduce(function (m, v) { return v > m ? v : m; }); }

  // PHP 8's / : a division by zero throws, where JavaScript answers INF - a histogram whose
  // plot is 0px wide ends there on the server, and so it does here.

  function div(a, b) { if (b === 0) throw new Error('Division by zero'); return a / b; }

  C.padChartValues = function (rows, field) {

    var values = [];

    C.values(rows).forEach(function (row) {
      row = C.padChartRow(row);
      var v = C.get(row, field);
      if (C.padChartFinite(v))
        values.push(C.num(v));
    });

    return values;

  };

  // The frame both kinds share: answers the svg so far, the left edge and the function that
  // places a value.

  C.padChartValueAxis = function (svg, ticks, width, height, top, bottom) {

    var lo    = ticks[0];
    var hi    = ticks[ticks.length - 1];
    var tickW = 0;

    ticks.forEach(function (tick) { tickW = Math.max(tickW, C.padChartWidth(C.padChartNumber(tick))); });

    var left  = Math.max(24, C.int(Math.ceil(tickW)) + 10);
    var plotH = height - top - bottom;
    var y     = function (v) { return top + (hi - v) / (hi - lo) * plotH; };

    ticks.forEach(function (tick) {
      svg += '<line class="pc-grid" x1="' + C.str(left) + '" x2="' + C.str(width - 10) + '" y1="' + C.padChartXY(y(tick)) + '" y2="' + C.padChartXY(y(tick)) + '"/>'
           + '<text x="' + C.str(left - 6) + '" y="' + C.padChartXY(y(tick) + 4) + '" text-anchor="end">' + C.padChartNumber(tick) + '</text>';
    });

    return [svg, left, y];

  };

  C.padChartHistogram = function (rows, width, height) {

    var numbers = C.padChartGuess(rows)[0];

    var used   = [];
    var field  = C.padChartPick('value', numbers, used);
    var values = C.padChartValues(rows, field);

    if (!values.length)
      return '';

    var edges = C.padChartTicks(low(values), high(values), Math.max(1, Math.min(100, C.int(C.padTagParm('bins', 10)))));

    if (edges.length < 2)
      edges.push(edges[0] + 1);

    var step = edges[1] - edges[0];

    if (!values.some(function (v) { return v !== Math.floor(v); })) {
      step  = Math.ceil(step);
      edges = [];
      var lowest = low(values), highest = high(values);
      for (var edge = Math.floor(lowest / step) * step;
           !edges.length || edges.length < 2 || (step > 1 ? edges[edges.length - 1] < highest : edges[edges.length - 1] <= highest);
           edge += step)
        edges.push(edge);
    }

    var bins   = edges.length - 1;
    var counts = [];
    for (var b = 0; b < bins; b++) counts.push(0);

    values.forEach(function (v) {
      var bin = Math.max(0, Math.min(bins - 1, C.int(Math.floor((v - edges[0]) / step))));
      while (bin < bins - 1 && v >= edges[bin + 1])
        bin++;
      while (bin > 0 && v < edges[bin])
        bin--;
      counts[bin]++;
    });

    var range = function (i) { return C.padChartNumber(edges[i]) + ' – ' + C.padChartNumber(edges[i + 1]); };
    var desc  = counts.map(function (count, i) { return range(i) + ': ' + count; });

    var title = C.str(C.padTagParm('title', C.ucfirst(field !== '' ? field : 'value') + ', ' + values.length + ' values'));

    var svg = C.padChartOpen('histogram', [edges, counts], desc, title, width, height)[1];

    var most  = high(counts);
    var ticks = C.padChartTicks(0, most, height < 200 ? 3 : 5);
    ticks = ticks.filter(function (t) { return Math.abs(t - C.round(t)) < 1e-9; });

    while (ticks[ticks.length - 1] < most)
      ticks.push(ticks[ticks.length - 1] + (ticks.length > 1 ? ticks[1] - ticks[0] : 1));

    var axis = C.padChartValueAxis(svg, ticks, width, height, 10, 24);
    svg = axis[0];
    var left = axis[1], y = axis[2];

    var plotW = width - left - 10;
    var binW  = plotW / bins;
    var edgeW = 0;

    edges.forEach(function (edge) { edgeW = Math.max(edgeW, C.padChartWidth(C.padChartNumber(edge)) + 8); });

    var every = Math.max(1, C.int(Math.ceil(div(edgeW, binW))));

    counts.forEach(function (count, i) {
      var x = left + i * binW;
      if (count)
        svg += '<rect class="pc-mark pc-gap pc-c1" x="' + C.padChartXY(x) + '" y="' + C.padChartXY(y(count)) + '" width="' + C.padChartXY(binW) + '" height="' + C.padChartXY(y(0) - y(count)) + '">'
             + '<title>' + C.padChartAttr(desc[i]) + '</title></rect>';
    });

    edges.forEach(function (edge, i) {
      if (i % every === 0)
        svg += '<text x="' + C.padChartXY(left + i * binW) + '" y="' + C.str(height - 8) + '" text-anchor="middle">' + C.padChartNumber(edge) + '</text>';
    });

    svg += '<line class="pc-axis" x1="' + C.str(left) + '" x2="' + C.str(width - 10) + '" y1="' + C.padChartXY(y(0)) + '" y2="' + C.padChartXY(y(0)) + '"/>';

    return svg + '</svg>';

  };

  // A quantile of sorted values, interpolated between the two around it.

  C.padChartQuantile = function (sorted, q) {

    var at = (sorted.length - 1) * q;
    var lo = C.int(Math.floor(at));
    var hi = C.int(Math.ceil(at));

    return sorted[lo] + (sorted[hi] - sorted[lo]) * (at - lo);

  };

  C.padChartBoxplot = function (rows, width, height) {

    var numbers = C.padChartGuess(rows)[0];

    var used   = [];
    var field  = C.padChartPick('value', numbers, used);
    var label  = C.str(C.padTagParm('label'));
    var groups = new Map();

    C.values(rows).forEach(function (row) {
      row = C.padChartRow(row);
      var v = C.get(row, field);
      if (C.padChartFinite(v)) {
        var name = C.key(label !== '' ? C.padChartText(row, label) : C.ucfirst(field));
        if (!groups.has(name)) groups.set(name, []);
        groups.get(name).push(C.num(v));
      }
    });

    if (!groups.size)
      return '';

    var boxes = new Map();
    var desc  = [];

    groups.forEach(function (list, name) {

      var values = list.slice().sort(function (a, b) { return a - b; });

      var q1  = C.padChartQuantile(values, 0.25);
      var med = C.padChartQuantile(values, 0.5);
      var q3  = C.padChartQuantile(values, 0.75);
      var iqr = q3 - q1;
      var inside  = values.filter(function (v) { return v >= q1 - 1.5 * iqr && v <= q3 + 1.5 * iqr; });
      var outside = values.filter(function (v) { return v < q1 - 1.5 * iqr || v > q3 + 1.5 * iqr; });

      boxes.set(name, [low(inside), q1, med, q3, high(inside), outside, values.length]);

      desc.push(C.str(name) + ': median ' + C.padChartNumber(med) + ', quartiles ' + C.padChartNumber(q1) + ' – ' + C.padChartNumber(q3)
              + ', range ' + C.padChartNumber(values[0]) + ' – ' + C.padChartNumber(values[values.length - 1]) + ', ' + values.length + ' values');

    });

    var title = C.str(C.padTagParm('title', C.ucfirst(field !== '' ? field : 'value') + (label !== '' ? ' by ' + label : '')));

    var svg = C.padChartOpen('boxplot', boxes, desc, title, width, height)[1];

    var all = [];
    groups.forEach(function (values) { all = all.concat(values); });

    var axis = C.padChartValueAxis(svg, C.padChartTicks(low(all), high(all), height < 200 ? 3 : 5), width, height, 10, 24);
    svg = axis[0];
    var left = axis[1], y = axis[2];

    var n    = boxes.size;
    var band = (width - left - 10) / n;
    var boxW = Math.max(6, Math.min(56, band * 0.5));
    var i    = 0;

    boxes.forEach(function (box, name) {

      var lo = box[0], q1 = box[1], med = box[2], q3 = box[3], hi = box[4], out = box[5];

      var c    = left + (i + 0.5) * band;
      var x0   = c - boxW / 2;
      var slot = C.padChartSlot(n > 1 ? i : 0);
      var tip  = '<title>' + C.padChartAttr(desc[i]) + '</title>';

      svg += '<line class="pc-whisker" x1="' + C.padChartXY(c) + '" x2="' + C.padChartXY(c) + '" y1="' + C.padChartXY(y(hi)) + '" y2="' + C.padChartXY(y(q3)) + '"/>'
           + '<line class="pc-whisker" x1="' + C.padChartXY(c) + '" x2="' + C.padChartXY(c) + '" y1="' + C.padChartXY(y(q1)) + '" y2="' + C.padChartXY(y(lo)) + '"/>'
           + '<line class="pc-whisker" x1="' + C.padChartXY(c - boxW / 4) + '" x2="' + C.padChartXY(c + boxW / 4) + '" y1="' + C.padChartXY(y(hi)) + '" y2="' + C.padChartXY(y(hi)) + '"/>'
           + '<line class="pc-whisker" x1="' + C.padChartXY(c - boxW / 4) + '" x2="' + C.padChartXY(c + boxW / 4) + '" y1="' + C.padChartXY(y(lo)) + '" y2="' + C.padChartXY(y(lo)) + '"/>'
           + '<rect class="pc-mark pc-box ' + slot + '" x="' + C.padChartXY(x0) + '" y="' + C.padChartXY(y(q3)) + '" width="' + C.padChartXY(boxW) + '" height="' + C.padChartXY(Math.max(1, y(q1) - y(q3))) + '" rx="3">' + tip + '</rect>'
           + '<line class="pc-median" x1="' + C.padChartXY(x0) + '" x2="' + C.padChartXY(x0 + boxW) + '" y1="' + C.padChartXY(y(med)) + '" y2="' + C.padChartXY(y(med)) + '"/>';

      out.forEach(function (v) {
        svg += '<circle class="' + slot + ' pc-ring" cx="' + C.padChartXY(c) + '" cy="' + C.padChartXY(y(v)) + '" r="4.5"><title>' + C.padChartAttr(C.str(name) + ': ' + C.padChartNumber(v)) + '</title></circle>';
      });

      svg += '<text x="' + C.padChartXY(c) + '" y="' + C.str(height - 8) + '" text-anchor="middle">' + C.padChartAttr(C.padChartFit(name, band - 4)) + '</text>';

      i++;

    });

    return svg + '</svg>';

  };

})(typeof window !== 'undefined' ? window.PadChart : globalThis.PadChart);
