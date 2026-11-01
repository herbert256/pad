// scatter.php in the browser: scatter and bubble charts, with the trend line (core.js says how
// these files are written).
// needs: core

(function (C) {

  'use strict';

  function low(list)  { return list.reduce(function (m, v) { return v < m ? v : m; }); }
  function high(list) { return list.reduce(function (m, v) { return v > m ? v : m; }); }

  C.padChartScatter = function (kind, rows, seqName, width, height) {

    var guess   = C.padChartGuess(rows);
    var numbers = guess[0];

    var used   = [];
    var xField = C.padChartPick('x', numbers, used);
    var yField = C.padChartPick('y', numbers, used);
    var sField = (kind === 'bubble') ? C.padChartPick('size', numbers, used) : '';
    var label  = C.str(C.padTagParm('label'));
    var group  = C.str(C.padTagParm('color'));
    var trend  = C.padTagParm('trend', false);
    trend = (trend !== false && trend !== '' && trend !== 0 && trend !== '0');
    var alone  = (yField === '');

    if (alone) {
      yField = xField;
      xField = '';
    }

    var title = C.str(C.padTagParm('title', seqName !== '' ? seqName
                                         : (yField !== '' ? C.ucfirst(yField) + (xField !== '' ? ' against ' + xField : '') : C.ucfirst(kind) + ' chart')));

    var dots   = [];
    var groups = new Map();
    var index  = 0;

    C.values(rows).forEach(function (row) {

      index++;
      row = C.padChartRow(row);

      var x = (xField === '') ? index : C.get(row, xField);
      var y = C.get(row, yField);
      var s = (sField === '') ? 1 : C.get(row, sField);

      if (!C.padChartFinite(x) || !C.padChartFinite(y) || !C.padChartFinite(s) || C.float(s) < 0)
        return;

      var g = C.padChartText(row, group);

      if (group !== '' && !groups.has(C.key(g)))
        groups.set(C.key(g), groups.size);

      dots.push([C.num(x), C.num(y), C.num(s), C.padChartText(row, label), g]);

    });

    if (!dots.length)
      return '';

    var xName = (xField === '') ? 'row' : xField;
    var tip   = function (dot) {
      return (dot[3] !== '' ? dot[3] + ': ' : '')
           + xName + ' ' + C.padChartNumber(dot[0]) + ', ' + yField + ' ' + C.padChartNumber(dot[1])
           + (sField !== '' ? ', ' + sField + ' ' + C.padChartNumber(dot[2]) : '');
    };

    var svg = C.padChartOpen(kind, dots, dots.map(tip), title, width, height)[1];

    var xs = dots.map(function (d) { return d[0]; });
    var ys = dots.map(function (d) { return d[1]; });

    if (!isFinite(high(xs) - low(xs)) || !isFinite(high(ys) - low(ys)))
      return '';

    // A bubble reaches past its centre, so its axes take in a tenth more on each side.

    var xPad = (sField !== '') ? (high(xs) - low(xs)) / 10 : 0;
    var yPad = (sField !== '') ? (high(ys) - low(ys)) / 10 : 0;

    var xTicks = C.padChartTicks(low(xs) - xPad, high(xs) + xPad, width  < 300 ? 3 : 5);
    var yTicks = C.padChartTicks(low(ys) - yPad, high(ys) + yPad, height < 200 ? 3 : 5);
    var xLow   = xTicks[0], xHigh = xTicks[xTicks.length - 1];
    var yLow   = yTicks[0], yHigh = yTicks[yTicks.length - 1];

    var tickWidth = 0;
    yTicks.forEach(function (tick) { tickWidth = Math.max(tickWidth, C.padChartWidth(C.padChartNumber(tick))); });

    // The group names - eight colours, the rest 'Other' - in a legend above the plot.

    var folded = (groups.size > 8);
    var keys   = Array.from(groups.keys());

    if (folded)
      keys = keys.slice(0, 8).concat(['Other']);

    var left  = Math.max(24, C.int(Math.ceil(tickWidth)) + 10);
    var right = C.int(Math.max(10, Math.ceil(C.padChartWidth(C.padChartNumber(xHigh)) / 2) + 4));
    var top   = 6;

    if (groups.size) {
      var legend = C.padChartLegend(keys, left, top, width - left - right);
      svg += legend[0];
      top += legend[1];
    }

    top += 16;
    var bottom = 38;
    var plotW  = Math.max(1, width  - left - right);
    var plotH  = Math.max(1, height - top  - bottom);

    var px = function (v) { return left + (v - xLow) / (xHigh - xLow) * plotW; };
    var py = function (v) { return top  + (yHigh - v) / (yHigh - yLow) * plotH; };

    yTicks.forEach(function (tick) {
      svg += '<line class="pc-grid" x1="' + C.str(left) + '" x2="' + C.str(left + plotW) + '" y1="' + C.padChartXY(py(tick)) + '" y2="' + C.padChartXY(py(tick)) + '"/>'
           + '<text x="' + C.str(left - 6) + '" y="' + C.padChartXY(py(tick) + 4) + '" text-anchor="end">' + C.padChartNumber(tick) + '</text>';
    });

    xTicks.forEach(function (tick) {
      svg += '<line class="pc-grid" x1="' + C.padChartXY(px(tick)) + '" x2="' + C.padChartXY(px(tick)) + '" y1="' + C.str(top) + '" y2="' + C.str(top + plotH) + '"/>'
           + '<text x="' + C.padChartXY(px(tick)) + '" y="' + C.str(top + plotH + 16) + '" text-anchor="middle">' + C.padChartNumber(tick) + '</text>';
    });

    svg += '<text x="' + C.padChartXY(left + plotW / 2) + '" y="' + C.str(height - 6) + '" text-anchor="middle">' + C.padChartAttr(xName) + '</text>'
         + '<text x="4" y="' + C.str(top - 8) + '">' + C.padChartAttr(yField) + '</text>'
         + '<line class="pc-axis" x1="' + C.str(left) + '" x2="' + C.str(left + plotW) + '" y1="' + C.str(top + plotH) + '" y2="' + C.str(top + plotH) + '"/>';

    // The dots: 4px, or a bubble whose area is its size, the largest drawn first so the small
    // ones stay on top.

    var sMax  = high(dots.map(function (d) { return d[2]; }));
    var rMax  = Math.max(6, Math.min(plotW, plotH) / 10);
    var order = dots.map(function (d, i) { return i; });

    if (sField !== '')
      order.sort(function (a, b) { return dots[b][2] < dots[a][2] ? -1 : (dots[b][2] > dots[a][2] ? 1 : 0); });

    order.forEach(function (i) {

      var dot  = dots[i];
      var r    = (sField === '') ? 4 : Math.max(2, (sMax > 0 ? Math.sqrt(dot[2] / sMax) : 0) * rMax);
      var slot = (group === '') ? 'pc-c1' : ((folded && groups.get(C.key(dot[4])) >= 8) ? 'pc-co' : C.padChartSlot(groups.get(C.key(dot[4]))));

      svg += '<circle class="pc-mark pc-ring ' + slot + (sField !== '' ? ' pc-bubble' : '') + '" cx="' + C.padChartXY(px(dot[0])) + '" cy="' + C.padChartXY(py(dot[1])) + '" r="' + C.padChartXY(r) + '"><title>'
           + C.padChartAttr((group !== '' ? dot[4] + ' · ' : '') + tip(dot)) + '</title></circle>';

    });

    if (trend)
      svg += C.padChartTrend(dots, xLow, xHigh, yLow, yHigh, px, py);

    return svg + '</svg>';

  };

  // The least-squares line through the dots, cut to the plot. Nothing when x does not vary.

  C.padChartTrend = function (dots, xLow, xHigh, yLow, yHigh, px, py) {

    var n = dots.length;

    if (n < 2)
      return '';

    var mx  = dots.reduce(function (s, d) { return s + d[0]; }, 0) / n;
    var my  = dots.reduce(function (s, d) { return s + d[1]; }, 0) / n;
    var sxx = 0, sxy = 0;

    dots.forEach(function (dot) {
      sxx += (dot[0] - mx) * (dot[0] - mx);
      sxy += (dot[0] - mx) * (dot[1] - my);
    });

    var tiny = 1e-12 * Math.abs(mx);

    if (!isFinite(sxx) || sxx <= n * (tiny * tiny) || sxx < 1e-300)
      return '';

    var slope     = sxy / sxx;
    var intercept = my - slope * mx;

    if (!isFinite(slope) || !isFinite(intercept))
      return '';

    var from = xLow;
    var to   = xHigh;

    if (slope !== 0) {
      var a = (yLow  - intercept) / slope;
      var b = (yHigh - intercept) / slope;
      from = Math.max(from, Math.min(a, b));
      to   = Math.min(to,   Math.max(a, b));
    }

    if (from >= to)
      return '';

    var at = function (x) { return C.padChartXY(px(x)) + ',' + C.padChartXY(py(slope * x + intercept)); };

    return '<path class="pc-trend" d="M' + at(from) + 'L' + at(to) + '"><title>Trend line</title></path>';

  };

})(typeof window !== 'undefined' ? window.PadChart : globalThis.PadChart);
