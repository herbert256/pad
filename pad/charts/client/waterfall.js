// waterfall.php in the browser: how a total is made up, step by step (core.js says how these
// files are written).
// needs: core

(function (C) {

  'use strict';

  C.padChartWaterfall = function (rows, width, height) {

    var label  = C.str(C.padTagParm('label'));
    var value  = C.str(C.padTagParm('value'));
    var points = C.padChartPoints(rows, label, value);
    var total  = C.str(C.padTagParm('total', 'Total'));

    if (!points.length) return '';

    var steps = [];
    var level = 0;

    points.forEach(function (p) {
      steps.push([p[0], level, level + p[1], p[1], p[1] >= 0 ? 'pc-c3' : 'pc-c8']);
      level += p[1];
    });

    if (total !== '')
      steps.push([total, 0, level, level, 'pc-c1']);

    if (!isFinite(level)) return '';

    var sign = function (v) { return (v > 0 ? '+' : '') + C.padChartNumber(v); };
    var desc = steps.map(function (s, i) {
      return s[0] + ': ' + ((total !== '' && i === steps.length - 1) ? C.padChartNumber(s[3]) : sign(s[3]));
    });

    var title = C.str(C.padTagParm('title', C.ucfirst(value !== '' ? value : 'value') + (label !== '' ? ' by ' + label : '')));

    var open = C.padChartOpen('waterfall', steps, desc, title, width, height);
    var svg  = open[1];

    var all   = [0].concat(steps.map(function (s) { return s[1]; }), steps.map(function (s) { return s[2]; }));
    var ticks = C.padChartTicks(C.lo(all), C.hi(all), height < 200 ? 3 : 5);
    var low   = ticks[0];
    var high  = ticks[ticks.length - 1];

    var tickW = 0;
    ticks.forEach(function (tick) { tickW = Math.max(tickW, C.padChartWidth(C.padChartNumber(tick))); });

    var left   = Math.max(24, C.int(Math.ceil(tickW)) + 10);
    var right  = 10;
    var top    = 18;
    var bottom = 24;
    var plotW  = width  - left - right;
    var plotH  = height - top  - bottom;
    var n      = steps.length;
    var band   = plotW / n;
    var barW   = Math.max(2, Math.min(48, band * 0.7));
    var y      = function (v) { return top + (high - v) / (high - low) * plotH; };
    var mid    = function (i) { return left + (i + 0.5) * band; };

    ticks.forEach(function (tick) {
      svg += '<line class="pc-grid" x1="' + C.str(left) + '" x2="' + C.str(width - right) + '" y1="' + C.padChartXY(y(tick)) + '" y2="' + C.padChartXY(y(tick)) + '"/>'
           + '<text x="' + C.str(left - 6) + '" y="' + C.padChartXY(y(tick) + 4) + '" text-anchor="end">' + C.padChartNumber(tick) + '</text>';
    });

    var labelW = 0;
    steps.forEach(function (s) { labelW = Math.max(labelW, C.padChartWidth(s[0]) + 8); });

    var every = Math.max(1, C.int(Math.ceil(labelW / Math.max(1, band))));

    steps.forEach(function (s, i) {

      var name = s[0], from = s[1], to = s[2], v = s[3], cls = s[4];
      var x0 = mid(i) - barW / 2;
      var y0 = y(Math.max(from, to));
      var h  = Math.max(1, Math.abs(y(from) - y(to)));

      svg += '<rect class="pc-mark ' + cls + '" x="' + C.padChartXY(x0) + '" y="' + C.padChartXY(y0) + '" width="' + C.padChartXY(barW) + '" height="' + C.padChartXY(h) + '" rx="2">'
           + '<title>' + C.padChartAttr(desc[i]) + '</title></rect>';

      if (i < n - 1)
        svg += '<line class="pc-edge" stroke-dasharray="3 3" x1="' + C.padChartXY(x0 + barW) + '" x2="' + C.padChartXY(mid(i + 1) - barW / 2) + '"'
             + ' y1="' + C.padChartXY(y(to)) + '" y2="' + C.padChartXY(y(to)) + '"/>';

      var text = (cls === 'pc-c1') ? C.padChartNumber(v) : sign(v);

      if (C.padChartWidth(text) <= band - 2)
        svg += '<text x="' + C.padChartXY(mid(i)) + '" y="' + C.padChartXY(y0 - 4) + '" text-anchor="middle">' + C.padChartAttr(text) + '</text>';

      if (i % every === 0)
        svg += '<text x="' + C.padChartXY(mid(i)) + '" y="' + C.str(height - 8) + '" text-anchor="middle">' + C.padChartAttr(C.padChartFit(name, band * every - 4)) + '</text>';

    });

    svg += '<line class="pc-axis" x1="' + C.str(left) + '" x2="' + C.str(width - right) + '" y1="' + C.padChartXY(y(0)) + '" y2="' + C.padChartXY(y(0)) + '"/>';

    return svg + '</svg>';

  };

})(typeof window !== 'undefined' ? window.PadChart : globalThis.PadChart);
