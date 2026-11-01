// hexbin.php in the browser: many points on two axes, counted in a grid of hexagons (core.js
// says how these files are written).
// needs: core

(function (C) {

  'use strict';

  C.padChartHexbin = function (rows, width, height) {

    var numbers = C.padChartGuess(rows)[0];

    var used   = [];
    var xField = C.padChartPick('x', numbers, used);
    var yField = C.padChartPick('y', numbers, used);
    var bins   = C.padTagParm('bins', 20);

    if (xField === '' || yField === '' || xField === yField) {
      if (C.count(rows))
        C.padError("a hexbin chart needs two numeric fields - x='field' and y='field'");
      return '';
    }

    if (C.count(rows))
      [['x', xField], ['y', yField]].forEach(function (pair) {
        if (!C.padChartFieldSomewhere(rows, pair[1]))
          C.padError("the hexbin chart's " + pair[0] + "='" + C.padMakeSafe(pair[1], 30) + "' is no field of its rows");
      });

    if (!C.padChartFinite(bins) || C.float(bins) !== Math.floor(C.float(bins)) || C.float(bins) < 2 || C.float(bins) > 100) {
      C.padError('the bins of a hexbin chart are a whole number from 2 to 100 - the hexagons across the plot');
      bins = 20;
    }

    bins = C.int(bins);

    var points = [];
    var left   = 0;

    C.values(rows).forEach(function (row) {
      row = C.padChartRow(row);
      var x = C.get(row, xField), y = C.get(row, yField);
      if (C.padChartFinite(x) && C.padChartFinite(y))
        points.push([C.num(x), C.num(y)]);
      else
        left++;
    });

    if (!points.length)
      return '';

    var xs = points.map(function (p) { return p[0]; });
    var ys = points.map(function (p) { return p[1]; });

    if (!isFinite(C.hi(xs) - C.lo(xs)) || !isFinite(C.hi(ys) - C.lo(ys)))
      return '';

    var xTicks = C.padChartTicks(C.lo(xs), C.hi(xs), width  < 300 ? 3 : 5);
    var yTicks = C.padChartTicks(C.lo(ys), C.hi(ys), height < 200 ? 3 : 5);
    var xLow   = xTicks[0], xHigh = xTicks[xTicks.length - 1];
    var yLow   = yTicks[0], yHigh = yTicks[yTicks.length - 1];

    var tickWidth = 0;
    yTicks.forEach(function (tick) { tickWidth = Math.max(tickWidth, C.padChartWidth(C.padChartNumber(tick))); });

    var margin = Math.max(24, C.int(Math.ceil(tickWidth)) + 10);
    var right  = C.int(Math.max(10, Math.ceil(C.padChartWidth(C.padChartNumber(xHigh)) / 2) + 4));
    var top    = 38;
    var bottom = 38;
    var plotW  = Math.max(1, width  - margin - right);
    var plotH  = Math.max(1, height - top - bottom);

    // The grid: bins hexagons across, of radius r; a row every 1.5 r, every second one half a
    // hexagon to the right. A point's hexagon is the nearest middle of the two rows around
    // it and of the two columns around it in each.

    var r  = plotW / (bins * Math.sqrt(3));
    var dx = r * Math.sqrt(3);
    var dy = r * 1.5;

    var cells = new Map();

    points.forEach(function (point) {

      var px   = (point[0] - xLow) / (xHigh - xLow) * plotW;
      var py   = (yHigh - point[1]) / (yHigh - yLow) * plotH;
      var j0   = C.int(Math.floor(py / dy));
      var best = null;

      for (var j = j0; j <= j0 + 1; j++) {
        var shift = (j % 2) ? dx / 2 : 0;
        var i0    = C.int(Math.floor((px - shift) / dx));
        for (var i = i0; i <= i0 + 1; i++) {
          var ex = i * dx + shift - px;
          var ey = j * dy - py;
          var d  = ex * ex + ey * ey;
          if (best === null || d < best[0])
            best = [d, i, j];
        }
      }

      var key = best[2] * 1000 + best[1] + 1;
      cells.set(key, (cells.get(key) || 0) + 1);

    });

    var keys = Array.from(cells.keys()).sort(function (a, b) { return a - b; });

    // A hexagon's middle in the numbers of the axes, for its tooltip.

    var middle = function (key) {
      var j  = Math.trunc(key / 1000);
      var i  = key % 1000 - 1;
      var cx = i * dx + ((j % 2) ? dx / 2 : 0);
      var cy = j * dy;
      return [cx, cy, xLow + cx / plotW * (xHigh - xLow), yHigh - cy / plotH * (yHigh - yLow)];
    };

    var total = points.length;
    var most  = Math.max.apply(null, keys.map(function (key) { return cells.get(key); }));
    var tip   = function (key, count) {
      var m = middle(key);
      return C.padChartNumber(count) + (count === 1 ? ' point' : ' points') + ' around ' + xField + ' ' + C.padChartNumber(C.round(m[2], 2)) + ', ' + yField + ' ' + C.padChartNumber(C.round(m[3], 2));
    };

    var order = keys.slice().sort(function (a, b) { return cells.get(b) - cells.get(a); });

    var desc = [C.padChartNumber(total) + (total === 1 ? ' point' : ' points') + ' in ' + C.padChartNumber(keys.length) + (keys.length === 1 ? ' hexagon' : ' hexagons')
              + ', at most ' + C.padChartNumber(most) + ' in one' + (left ? ', ' + C.padChartNumber(left) + (left === 1 ? ' row' : ' rows') + ' without two numbers' : '')];

    order.forEach(function (key) { desc.push(tip(key, cells.get(key))); });

    var title = C.str(C.padTagParm('title', C.ucfirst(yField) + ' against ' + xField + ', ' + C.padChartNumber(total) + (total === 1 ? ' point' : ' points')));

    var open = C.padChartOpen('hexbin', [cells, bins], desc, title, width, height);
    var id   = open[0];
    var svg  = open[1];

    // The scale above the plot: one point, the six steps, the most in one.

    svg += '<text x="' + C.str(margin) + '" y="15">1</text>';

    var at = margin + C.padChartWidth('1') + 6;

    for (var step = 1; step <= 6; step++)
      svg += '<rect class="pc-h' + step + '" x="' + C.padChartXY(at + (step - 1) * 16) + '" y="6" width="16" height="10"/>';

    svg += '<text x="' + C.padChartXY(at + 6 * 16 + 6) + '" y="15">' + C.padChartNumber(most) + (most === 1 ? ' point' : ' points') + ' a hexagon</text>';

    var px = function (v) { return margin + (v - xLow) / (xHigh - xLow) * plotW; };
    var py = function (v) { return top + (yHigh - v) / (yHigh - yLow) * plotH; };

    yTicks.forEach(function (tick) {
      svg += '<line class="pc-grid" x1="' + C.str(margin) + '" x2="' + C.str(margin + plotW) + '" y1="' + C.padChartXY(py(tick)) + '" y2="' + C.padChartXY(py(tick)) + '"/>'
           + '<text x="' + C.str(margin - 6) + '" y="' + C.padChartXY(py(tick) + 4) + '" text-anchor="end">' + C.padChartNumber(tick) + '</text>';
    });

    xTicks.forEach(function (tick) {
      svg += '<line class="pc-grid" x1="' + C.padChartXY(px(tick)) + '" x2="' + C.padChartXY(px(tick)) + '" y1="' + C.str(top) + '" y2="' + C.str(top + plotH) + '"/>'
           + '<text x="' + C.padChartXY(px(tick)) + '" y="' + C.str(top + plotH + 16) + '" text-anchor="middle">' + C.padChartNumber(tick) + '</text>';
    });

    svg += '<text x="' + C.padChartXY(margin + plotW / 2) + '" y="' + C.str(height - 6) + '" text-anchor="middle">' + C.padChartAttr(xField) + '</text>'
         + '<text x="4" y="' + C.str(top - 8) + '">' + C.padChartAttr(yField) + '</text>';

    // The hexagons, row by row, cut at the plot: six corners around the middle, the first
    // at the top.

    svg += '<clipPath id="' + id + '-plot"><rect x="' + C.str(margin) + '" y="' + C.str(top) + '" width="' + C.str(plotW) + '" height="' + C.str(plotH) + '"/></clipPath>'
         + '<g clip-path="url(#' + id + '-plot)">';

    keys.forEach(function (key) {

      var count = cells.get(key);
      var m     = middle(key);
      var d     = '';

      for (var k = 0; k < 6; k++) {
        var a = Math.PI / 3 * k - Math.PI / 2;
        d += (k ? 'L' : 'M') + C.padChartXY(margin + m[0] + r * Math.cos(a)) + ',' + C.padChartXY(top + m[1] + r * Math.sin(a));
      }

      svg += '<path class="pc-mark pc-seg pc-h' + Math.floor((count * 6 + most - 1) / most) + '" d="' + d + 'Z"><title>' + C.padChartAttr(tip(key, count)) + '</title></path>';

    });

    svg += '</g><line class="pc-axis" x1="' + C.str(margin) + '" x2="' + C.str(margin + plotW) + '" y1="' + C.str(top + plotH) + '" y2="' + C.str(top + plotH) + '"/>';

    return svg + '</svg>';

  };

})(typeof window !== 'undefined' ? window.PadChart : globalThis.PadChart);
