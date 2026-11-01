// dual.php in the browser: bars on the left axis, a line on the right one (core.js says how
// these files are written).
// needs: core

(function (C) {

  'use strict';

  C.padChartDual = function (rows, width, height) {

    var guess   = C.padChartGuess(rows);
    var numbers = guess[0], names = guess[1];

    var used  = [];
    var label = C.padChartPick('label', names,   used);
    var value = C.padChartPick('value', numbers, used);
    var line  = C.padChartPick('line',  numbers, used);

    if (value === '' || line === '' || value === line) {
      if (C.bool(rows))
        C.padError("a dual axis chart needs two numeric fields - value='bars' and line='line'");
      return '';
    }

    var table  = C.padChartTable(rows, label, [value, line]);
    var labels = table[0], series = table[1];

    var n = labels.length;

    if (!n) return '';

    var keys     = Array.from(series.keys());
    var barName  = C.str(keys[0]);
    var lineName = C.str(keys[1]);
    var bars     = series.get(keys[0]);
    var points   = series.get(keys[1]);

    var desc = labels.map(function (text, i) {
      return text + ': ' + barName + ' ' + C.padChartNumber(bars[i]) + ', ' + lineName + ' ' + C.padChartNumber(points[i]);
    });

    var title = C.str(C.padTagParm('title', barName + ' and ' + lineName + (label !== '' ? ' by ' + label : '')));

    var open = C.padChartOpen('dual', [labels, series], desc, title, width, height);
    var svg  = open[1];

    var count = height < 200 ? 3 : 5;
    var left  = C.padChartTicks(Math.min(0, C.lo(bars)), Math.max(0, C.hi(bars)), count);

    if (!isFinite(left[left.length - 1] - left[0])) return '';

    var right   = C.padChartDualTicks(left, C.lo(points), C.hi(points), count);
    var aligned = (right.length === left.length);

    var lowL  = left[0];
    var highL = left[left.length - 1];
    var lowR  = right[0];
    var highR = right[right.length - 1];

    var leftW = 0, rightW = 0;
    left.forEach(function (tick)  { leftW  = Math.max(leftW,  C.padChartWidth(C.padChartNumber(tick))); });
    right.forEach(function (tick) { rightW = Math.max(rightW, C.padChartWidth(C.padChartNumber(tick))); });

    var marginL = Math.max(24, C.int(Math.ceil(leftW))  + 10);
    var marginR = Math.max(24, C.int(Math.ceil(rightW)) + 10);

    var legend = C.padChartLegend([barName + ' (left)', lineName + ' (right)'], marginL, 6, width - marginL - marginR);

    svg += legend[0];

    var top    = 10 + legend[1];
    var bottom = 24;
    var plotW  = Math.max(1, width  - marginL - marginR);
    var plotH  = Math.max(1, height - top - bottom);
    var band   = plotW / n;
    var mid    = function (i) { return marginL + (i + 0.5) * band; };
    var yL     = function (v) { return top + (highL - v) / (highL - lowL) * plotH; };
    var yR     = function (v) { return top + (highR - v) / (highR - lowR) * plotH; };

    left.forEach(function (tick) {
      svg += '<line class="pc-grid" x1="' + C.str(marginL) + '" x2="' + C.str(width - marginR) + '" y1="' + C.padChartXY(yL(tick)) + '" y2="' + C.padChartXY(yL(tick)) + '"/>'
           + '<text class="pc-c1" x="' + C.str(marginL - 6) + '" y="' + C.padChartXY(yL(tick) + 4) + '" text-anchor="end">' + C.padChartNumber(tick) + '</text>';
    });

    right.forEach(function (tick) {
      if (!aligned)
        svg += '<line class="pc-grid" stroke-dasharray="2 3" x1="' + C.str(marginL) + '" x2="' + C.str(width - marginR) + '" y1="' + C.padChartXY(yR(tick)) + '" y2="' + C.padChartXY(yR(tick)) + '"/>';
      svg += '<text class="pc-c2" x="' + C.str(width - marginR + 6) + '" y="' + C.padChartXY(yR(tick) + 4) + '">' + C.padChartNumber(tick) + '</text>';
    });

    var labelW = 0;
    labels.forEach(function (text) { labelW = Math.max(labelW, C.padChartWidth(text) + 8); });

    var every = Math.max(1, C.int(Math.ceil(labelW / Math.max(1, band))));

    labels.forEach(function (text, i) {
      if (i % every === 0)
        svg += '<text x="' + C.padChartXY(mid(i)) + '" y="' + C.str(height - 8) + '" text-anchor="middle">' + C.padChartAttr(text) + '</text>';
    });

    var barW = Math.max(1, Math.min(28, band * 0.6));
    var base = yL(0);

    bars.forEach(function (v, i) {
      svg += '<path class="pc-mark pc-c1" d="' + C.padChartBarPath(mid(i) - barW / 2, mid(i) + barW / 2, base, yL(v)) + '">'
           + '<title>' + C.padChartAttr(labels[i] + ' · ' + barName + ': ' + C.padChartNumber(v)) + '</title></path>';
    });

    svg += '<line class="pc-axis" x1="' + C.str(marginL) + '" x2="' + C.str(width - marginR) + '" y1="' + C.padChartXY(base) + '" y2="' + C.padChartXY(base) + '"/>';

    var path = '', dots = '';

    points.forEach(function (v, i) {
      path += (i ? 'L' : 'M') + C.padChartXY(mid(i)) + ',' + C.padChartXY(yR(v));
      dots += '<circle class="pc-mark pc-c2 pc-ring" cx="' + C.padChartXY(mid(i)) + '" cy="' + C.padChartXY(yR(v)) + '" r="4">'
            + '<title>' + C.padChartAttr(labels[i] + ' · ' + lineName + ': ' + C.padChartNumber(v)) + '</title></circle>';
    });

    svg += '<path class="pc-l2" d="' + path + '"/>' + dots;

    return svg + '</svg>';

  };

  C.padChartDualTicks = function (left, min, max, count) {

    var stepL = left[1] - left[0];
    var below = C.int(C.round(-left[0] / stepL));
    var above = C.int(C.round(left[left.length - 1] / stepL));

    if ((min < 0 && below === 0) || (max > 0 && above === 0) || (min === 0 && max === 0))
      return C.padChartTicks(Math.min(0, min), Math.max(0, max), count);

    var need = Math.max(max > 0 ? max / above : 0, min < 0 ? -min / below : 0);

    if (!(need > 0) || !isFinite(need) || !isFinite(Math.log10(need)))
      return C.padChartTicks(Math.min(0, min), Math.max(0, max), count);

    var step = C.pow10(Math.floor(Math.log10(need)));

    [1, 2, 2.5, 5, 10].some(function (times) {
      if (step * times >= need * (1 - 1e-9)) { step *= times; return true; }
      return false;
    });

    if (!(step > 0) || !isFinite(step))
      return C.padChartTicks(Math.min(0, min), Math.max(0, max), count);

    var digits = Math.max(10, 10 - C.int(Math.floor(Math.log10(step))));
    var ticks  = [];

    for (var k = -below; k <= above; k++)
      ticks.push(C.round(k * step, digits));

    return ticks;

  };

})(typeof window !== 'undefined' ? window.PadChart : globalThis.PadChart);
