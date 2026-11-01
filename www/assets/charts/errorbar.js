// errorbar.php in the browser: estimates with the interval around them, as given (core.js says
// how these files are written).
// needs: core

(function (C) {

  'use strict';

  C.padChartErrorbar = function (rows, width, height) {

    var guess = C.padChartGuess(rows, true);
    var low   = C.str(C.padTagParm('low',  'low'));
    var high  = C.str(C.padTagParm('high', 'high'));
    var used  = [low, high];
    var label = C.padChartPick('label', guess[1], used);
    var value = C.padChartPick('value', guess[0], used);

    if (value === '') {
      if (C.count(rows))
        C.padError("the errorbar chart needs a numeric field for its points - value='field'");
      return '';
    }

    var interval = C.padTagParm('interval', '');

    if (typeof interval !== 'string' && typeof interval !== 'number') {
      C.padError("the errorbar chart's interval= is a text naming its whiskers - interval='95% CI'");
      interval = '';
    }

    interval = C.trim(C.str(interval));

    if (C.count(rows))
      [['low', low], ['high', high]].forEach(function (pair) {
        if (!C.padChartFieldSomewhere(rows, pair[1]))
          C.padError("the errorbar chart has no bound " + pair[0] + "='" + C.padMakeSafe(pair[1], 30) + "' in its rows - name its field with " + pair[0] + '=');
      });

    // Per row: its name, the estimate and the two bounds - null for none, or for both when one
    // is missing or the low lies above the high.

    var points = [];
    var index  = 0;

    C.values(rows).forEach(function (row) {

      row = C.padChartRow(row);
      index++;

      var v = C.get(row, value);

      if (!C.padChartFinite(v))
        return;

      var name = C.padChartText(row, label);
      name = (name !== '') ? name : String(index);

      var lo = C.get(row, low);
      var hi = C.get(row, high);
      lo = C.padChartFinite(lo) ? C.num(lo) : null;
      hi = C.padChartFinite(hi) ? C.num(hi) : null;

      if (lo === null || hi === null)
        lo = hi = null;
      else if (lo > hi) {
        C.padError("the errorbar chart's row " + index + ", '" + C.padMakeSafe(name, 30) + "', has its low " + C.padChartNumber(lo)
                   + ' above its high ' + C.padChartNumber(hi) + " - low='" + C.padMakeSafe(low, 30) + "', high='" + C.padMakeSafe(high, 30) + "'");
        lo = hi = null;
      }

      points.push([name, C.num(v), lo, hi]);

    });

    if (!points.length) {
      if (C.count(rows))
        C.padError("the errorbar chart has no row with a number in value='" + C.padMakeSafe(value, 30) + "'");
      return '';
    }

    var what = C.ucfirst(value);
    var span = (interval !== '') ? interval : C.ucfirst(low + ' to ' + high);
    var tip  = function (point) {
      return point[0] + ': ' + C.padChartNumber(point[1])
           + ((point[2] === null) ? ', no interval'
             : ((interval !== '') ? ', ' + interval + ' ' + C.padChartNumber(point[2]) + ' – ' + C.padChartNumber(point[3])
                                  : ', ' + low + ' ' + C.padChartNumber(point[2]) + ', ' + high + ' ' + C.padChartNumber(point[3])));
    };
    var desc  = points.map(tip);
    var title = C.str(C.padTagParm('title', what + (interval !== '' ? ' with ' + interval : '') + (label !== '' ? ' by ' + label : '')));

    var open = C.padChartOpen('errorbar', [points, interval], desc, title, width, height);
    var svg  = open[1];

    var values = [];
    points.forEach(function (point) {
      if (point[2] === null) values.push(point[1]);
      else values.push(point[1], point[2], point[3]);
    });

    var ticks     = C.padChartTicks(C.lo(values), C.hi(values), height < 200 ? 3 : 5);
    var scaleLow  = ticks[0];
    var scaleHigh = ticks[ticks.length - 1];

    if (scaleHigh === scaleLow)
      scaleHigh = scaleLow + 1;

    var tickW = 0;
    ticks.forEach(function (tick) { tickW = Math.max(tickW, C.padChartWidth(C.padChartNumber(tick))); });

    var left  = Math.max(24, C.int(Math.ceil(tickW)) + 10);
    var right = 10;
    var plotW = Math.max(1, width - left - right);

    var legend = C.padChartErrorbarKey(what, span, left, 6, plotW);

    svg += legend[0];

    var top    = 10 + legend[1];
    var bottom = 24;
    var plotH  = Math.max(1, height - top - bottom);
    var n      = points.length;
    var band   = plotW / n;
    var cap    = Math.max(2, Math.min(7, band * 0.2));
    var r      = Math.max(2.5, Math.min(5, band * 0.25));
    var mid    = function (i) { return left + (i + 0.5) * band; };
    var y      = function (v) { return top + (scaleHigh - v) / (scaleHigh - scaleLow) * plotH; };

    ticks.forEach(function (tick) {
      svg += '<line class="pc-grid" x1="' + C.str(left) + '" x2="' + C.str(width - right) + '" y1="' + C.padChartXY(y(tick)) + '" y2="' + C.padChartXY(y(tick)) + '"/>'
           + '<text x="' + C.str(left - 6) + '" y="' + C.padChartXY(y(tick) + 4) + '" text-anchor="end">' + C.padChartNumber(tick) + '</text>';
    });

    if (scaleLow < 0 && scaleHigh > 0)
      svg += '<line class="pc-axis" x1="' + C.str(left) + '" x2="' + C.str(width - right) + '" y1="' + C.padChartXY(y(0)) + '" y2="' + C.padChartXY(y(0)) + '"/>';

    var labelW = 0;
    points.forEach(function (point) { labelW = Math.max(labelW, C.padChartWidth(point[0]) + 8); });

    var every = Math.max(1, C.int(Math.ceil(Math.min(labelW, 73) / Math.max(1, band))));

    points.forEach(function (point, i) {
      if (i % every === 0)
        svg += '<text x="' + C.padChartXY(mid(i)) + '" y="' + C.str(height - 8) + '" text-anchor="middle">' + C.padChartAttr(C.padChartFit(point[0], band * every - 8)) + '</text>';
    });

    points.forEach(function (point, i) {

      var v = point[1], lo = point[2], hi = point[3];
      var x = C.padChartXY(mid(i));

      svg += '<g class="pc-mark"><title>' + C.padChartAttr(desc[i]) + '</title>';

      if (lo !== null) {
        var a   = C.padChartXY(mid(i) - cap);
        var b   = C.padChartXY(mid(i) + cap);
        var yLo = C.padChartXY(y(lo));
        var yHi = C.padChartXY(y(hi));
        svg += '<line class="pc-l1" x1="' + x + '" x2="' + x + '" y1="' + yHi + '" y2="' + yLo + '"/>'
             + '<line class="pc-l1" x1="' + a + '" x2="' + b + '" y1="' + yHi + '" y2="' + yHi + '"/>'
             + '<line class="pc-l1" x1="' + a + '" x2="' + b + '" y1="' + yLo + '" y2="' + yLo + '"/>';
      }

      svg += '<circle class="pc-c1 pc-ring" cx="' + x + '" cy="' + C.padChartXY(y(v)) + '" r="' + C.padChartXY(r) + '"/></g>';

    });

    return svg + '</svg>';

  };

  C.padChartErrorbarKey = function (what, span, x, y, width) {

    var second = x + C.padChartWidth(what) + 26;
    var top    = y;

    if (second + C.padChartWidth(span) + 14 > x + width) {
      second = x;
      top   += 16;
    }

    var shown = C.padChartFit(span, x + width - second - 14);
    var whole = (shown !== span) ? '<title>' + C.padChartAttr(span) + '</title>' : '';
    var first = C.padChartFit(what, width - 14);
    var line  = function (x1, x2, y1, y2) {
      return '<line class="pc-l1" x1="' + C.padChartXY(x1) + '" x2="' + C.padChartXY(x2) + '" y1="' + C.padChartXY(y1) + '" y2="' + C.padChartXY(y2) + '"/>';
    };

    var svg = '<circle class="pc-c1" cx="' + C.padChartXY(x + 5) + '" cy="' + C.padChartXY(y + 5) + '" r="4"/>'
            + '<text x="' + C.padChartXY(x + 14) + '" y="' + C.padChartXY(y + 9) + '">' + C.padChartAttr(first) + '</text>'
            + line(second + 5, second + 5, top, top + 10)
            + line(second + 2, second + 8, top, top)
            + line(second + 2, second + 8, top + 10, top + 10)
            + '<text x="' + C.padChartXY(second + 14) + '" y="' + C.padChartXY(top + 9) + '">' + whole + C.padChartAttr(shown) + '</text>';

    return ['<g class="pc-key">' + svg + '</g>', top - y + 16];

  };

})(typeof window !== 'undefined' ? window.PadChart : globalThis.PadChart);
