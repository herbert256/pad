// parallel.php in the browser: parallel coordinates, an upright axis per measure and a line
// per row (core.js says how these files are written).
// needs: core

(function (C) {

  'use strict';

  C.padChartParallel = function (rows, width, height) {

    var guess   = C.padChartGuess(rows);
    var numbers = guess[0], names = guess[1];

    var fields = C.padChartFields(C.padTagParm('value'));
    if (!fields.length) fields = numbers;
    var group  = C.str(C.padTagParm('color'));
    var used   = fields.concat([group]);
    var label  = C.padChartPick('label', names, used);

    if (fields.length < 2) {
      C.padError('a parallel coordinates chart needs two measures or more in value=, it has ' + fields.length);
      return '';
    }

    var lines  = [];
    var groups = new Map();
    var index  = 0;

    C.values(rows).forEach(function (row) {

      row = C.padChartRow(row);
      index++;
      var values = [];

      for (var f = 0; f < fields.length; f++) {
        var v = C.get(row, fields[f]);
        if (!C.padChartFinite(v))
          return;
        values.push(C.num(v));
      }

      var g = C.padChartText(row, group);

      if (group !== '' && !groups.has(C.key(g)))
        groups.set(C.key(g), groups.size);

      var name = C.padChartText(row, label);
      lines.push([name !== '' ? name : 'Row ' + index, values, g]);

    });

    if (!lines.length)
      return '';

    var desc = lines.map(function (line) {
      var parts = fields.map(function (field, i) { return field + ' ' + C.padChartNumber(line[1][i]); });
      return line[0] + (line[2] !== '' ? ' (' + line[2] + ')' : '') + ': ' + parts.join(', ');
    });

    var title = C.str(C.padTagParm('title', fields.map(C.ucfirst).join(', ') + (group !== '' ? ' by ' + group : '')));

    var svg = C.padChartOpen('parallel', [fields, lines], desc, title, width, height)[1];

    var legendH = 0;

    if (groups.size > 1) {
      var keys = Array.from(groups.keys());
      if (keys.length > 8)
        keys = keys.slice(0, 8).concat(['Other']);
      var legend = C.padChartLegend(keys, 12, 6, width - 24);
      svg += legend[0];
      legendH = legend[1] + 6;
    }

    // An axis per measure: its round scale and the widest tick label beside it.

    var axes = fields.map(function (field, i) {
      var column = lines.map(function (line) { return line[1][i]; });
      var lo = column.reduce(function (m, v) { return v < m ? v : m; });
      var hi = column.reduce(function (m, v) { return v > m ? v : m; });
      var ticks = C.padChartTicks(lo, hi, height < 240 ? 3 : 4);
      var tickW = 0;
      ticks.forEach(function (tick) { tickW = Math.max(tickW, C.padChartWidth(C.padChartNumber(tick))); });
      return [ticks, tickW];
    });

    var n      = fields.length;
    var left   = Math.max(axes[0][1] + 10, C.padChartWidth(fields[0]) / 2 + 6, 12);
    var right  = Math.max(12, C.padChartWidth(fields[n - 1]) / 2 + 6);
    var top    = legendH + 30;
    var bottom = 12;
    var plotH  = Math.max(10, height - top - bottom);
    var step   = (width - left - right) / (n - 1);
    var x      = function (i) { return left + i * step; };
    var y      = function (i, v) {
      var ticks = axes[i][0];
      var lo = ticks[0], hi = ticks[ticks.length - 1];
      return top + (hi - v) / (hi - lo) * plotH;
    };

    // The lines first, so the axes and their numbers stay readable on top.

    var alone = group === '' ? C.padChartXY(Math.max(0.25, Math.min(0.8, 6 / Math.sqrt(lines.length) / 3))) : '0.65';

    lines.forEach(function (line, j) {

      var d = '';
      line[1].forEach(function (v, i) { d += (i ? 'L' : 'M') + C.padChartXY(x(i)) + ',' + C.padChartXY(y(i, v)); });

      var slot  = (group === '') ? 0 : groups.get(C.key(line[2]));
      var klass = (slot < 8) ? 'pc-l' + (slot + 1) : 'pc-edge';

      svg += '<path class="pc-mark ' + klass + '" stroke-opacity="' + alone + '" style="stroke-width:1.5" d="' + d + '">'
           + '<title>' + C.padChartAttr(desc[j]) + '</title></path>';

    });

    fields.forEach(function (field, i) {

      var ticks = axes[i][0];

      svg += '<line class="pc-axis" x1="' + C.padChartXY(x(i)) + '" x2="' + C.padChartXY(x(i)) + '" y1="' + C.padChartXY(top) + '" y2="' + C.padChartXY(top + plotH) + '"/>'
           + '<text x="' + C.padChartXY(x(i)) + '" y="' + C.padChartXY(top - 12) + '" text-anchor="middle" style="font-weight:600">'
           + C.padChartAttr(C.padChartFit(C.ucfirst(field), i === 0 || i === n - 1 ? step : step - 6)) + '</text>';

      ticks.forEach(function (tick) {
        svg += '<line class="pc-axis" x1="' + C.padChartXY(x(i) - 3) + '" x2="' + C.padChartXY(x(i)) + '" y1="' + C.padChartXY(y(i, tick)) + '" y2="' + C.padChartXY(y(i, tick)) + '"/>'
             + '<text class="pc-ring" style="paint-order:stroke;stroke-width:3px" x="' + C.padChartXY(x(i) - 5) + '" y="' + C.padChartXY(y(i, tick) + 3.5) + '" text-anchor="end">'
             + C.padChartNumber(tick) + '</text>';
      });

    });

    return svg + '</svg>';

  };

})(typeof window !== 'undefined' ? window.PadChart : globalThis.PadChart);
