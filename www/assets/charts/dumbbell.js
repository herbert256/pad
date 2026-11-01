// dumbbell.php in the browser: two values per category joined by a line (core.js says how
// these files are written).
// needs: core

(function (C) {

  'use strict';

  C.padChartDumbbell = function (rows, width, height) {

    var guess = C.padChartGuess(rows);
    var used  = [];
    var label = C.padChartPick('label', guess[1], used);
    var from  = C.padChartPick('from',  guess[0], used);
    var to    = C.padChartPick('to',    guess[0], used);

    if (from === '' || to === '' || from === to) {
      if (C.count(rows))
        C.padError("a dumbbell chart needs two numeric fields - from='field' and to='field'");
      return '';
    }

    if (C.count(rows))
      [['from', from], ['to', to]].forEach(function (pair) {
        if (!C.padChartFieldSomewhere(rows, pair[1]))
          C.padError("the dumbbell chart's " + pair[0] + "='" + C.padMakeSafe(pair[1], 30) + "' is no field of its rows");
      });

    var ends = C.padChartFields(C.padTagParm('names'));

    if (ends.length && ends.length !== 2) {
      C.padError("a dumbbell chart names its two ends - names='before, after', it has " + ends.length);
      ends = [];
    }

    var fromName = ends.length ? ends[0] : C.ucfirst(from);
    var toName   = ends.length ? ends[1] : C.ucfirst(to);

    // Per row: its name and the two values, null for an end that has no number.

    var bells = [];
    var index = 0;

    C.values(rows).forEach(function (row) {

      row = C.padChartRow(row);
      index++;

      var a = C.get(row, from);
      var b = C.get(row, to);
      a = C.padChartFinite(a) ? C.num(a) : null;
      b = C.padChartFinite(b) ? C.num(b) : null;

      if (a === null && b === null)
        return;

      var name = C.padChartText(row, label);
      bells.push([name !== '' ? name : 'Row ' + index, a, b]);

    });

    if (!bells.length) {
      if (C.count(rows))
        C.padError("the dumbbell chart has no row with a number in from='" + C.padMakeSafe(from, 30) + "' or to='" + C.padMakeSafe(to, 30) + "'");
      return '';
    }

    if (C.bool(C.padTagParm('ranked', false)))
      bells.sort(function (p, q) {
        var one = (p[1] === null || p[2] === null);
        var two = (q[1] === null || q[2] === null);
        if (one || two)
          return one === two ? 0 : (one ? 1 : -1);
        var cp = p[2] - p[1], cq = q[2] - q[1];
        return cq < cp ? -1 : (cq > cp ? 1 : 0);
      });

    var show = function (v) { return v === null ? 'not given' : C.padChartNumber(v); };

    var desc = bells.map(function (bell) {
      return bell[0] + ': ' + fromName + ' ' + show(bell[1]) + ', ' + toName + ' ' + show(bell[2])
           + ((bell[1] !== null && bell[2] !== null) ? ', change ' + C.padChartChange(bell[2] - bell[1]) : '');
    });

    var title = C.str(C.padTagParm('title', fromName + ' to ' + toName + (label !== '' ? ' by ' + label : '')));

    var open = C.padChartOpen('dumbbell', [bells, fromName, toName], desc, title, width, height);
    var svg  = open[1];

    var values = [];
    bells.forEach(function (bell) {
      [bell[1], bell[2]].forEach(function (v) { if (v !== null) values.push(v); });
    });

    var labelW = 0;
    bells.forEach(function (bell) { labelW = Math.max(labelW, C.padChartWidth(bell[0])); });

    var n     = bells.length;
    var left  = 12 + Math.min(labelW, width / 3) + 12;
    var ticks = C.padChartTicks(C.lo(values), C.hi(values), Math.max(2, Math.min(6, C.int((width - left - 16) / 70))));
    var low   = ticks[0];
    var high  = ticks[ticks.length - 1];

    if (high === low)
      high = low + 1;

    var tickW = 0;
    ticks.forEach(function (tick) { tickW = Math.max(tickW, C.padChartWidth(C.padChartNumber(tick))); });

    var right = Math.max(16, C.int(Math.ceil(tickW / 2)) + 4);
    var plotW = Math.max(10, width - left - right);

    var legend = C.padChartLegend([fromName, toName], left, 6, plotW);

    svg += legend[0];

    var top    = 10 + legend[1];
    var bottom = 24;
    var plotH  = Math.max(1, height - top - bottom);
    var band   = plotH / n;
    var r      = Math.max(2.5, Math.min(6, band * 0.3));
    var x      = function (v) { return left + (v - low) / (high - low) * plotW; };
    var mid    = function (i) { return top + (i + 0.5) * band; };
    var floor  = C.padChartXY(top + plotH);

    ticks.forEach(function (tick) {
      var p = C.padChartXY(x(tick));
      svg += '<line class="pc-grid" x1="' + p + '" x2="' + p + '" y1="' + C.padChartXY(top) + '" y2="' + floor + '"/>'
           + '<text x="' + p + '" y="' + C.str(height - 8) + '" text-anchor="middle">' + C.padChartNumber(tick) + '</text>';
    });

    if (low < 0 && high > 0)
      svg += '<line class="pc-axis" x1="' + C.padChartXY(x(0)) + '" x2="' + C.padChartXY(x(0)) + '" y1="' + C.padChartXY(top) + '" y2="' + floor + '"/>';

    bells.forEach(function (bell, i) {

      var name = bell[0], a = bell[1], b = bell[2];
      var y = C.padChartXY(mid(i));

      svg += '<text x="' + C.padChartXY(left - 12) + '" y="' + C.padChartXY(mid(i) + 4) + '" text-anchor="end">' + C.padChartAttr(C.padChartFit(name, left - 24)) + '</text>';

      if (a !== null && b !== null)
        svg += '<line class="pc-edge" stroke-width="3" x1="' + C.padChartXY(x(a)) + '" x2="' + C.padChartXY(x(b)) + '" y1="' + y + '" y2="' + y + '">'
             + '<title>' + C.padChartAttr(desc[i]) + '</title></line>';

      [[a, fromName, 1], [b, toName, 2]].forEach(function (end) {
        if (end[0] !== null)
          svg += '<circle class="pc-mark pc-c' + end[2] + ' pc-ring" cx="' + C.padChartXY(x(end[0])) + '" cy="' + y + '" r="' + C.padChartXY(r) + '">'
               + '<title>' + C.padChartAttr(name + ' · ' + end[1] + ': ' + C.padChartNumber(end[0])) + '</title></circle>';
      });

    });

    return svg + '</svg>';

  };

  C.padChartChange = function (change) {

    return (change > 0 ? '+' : '') + C.padChartNumber(change);

  };

})(typeof window !== 'undefined' ? window.PadChart : globalThis.PadChart);
