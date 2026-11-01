// radar.php in the browser: a spoke per measure, a polygon per row (core.js says how these
// files are written).
// needs: core

(function (C) {

  'use strict';

  C.padChartRadar = function (rows, width, height) {

    var guess   = C.padChartGuess(rows);
    var numbers = guess[0], names = guess[1];

    var fields = C.padChartFields(C.padTagParm('value'));
    if (!fields.length) fields = numbers;
    var used   = fields.slice();
    var label  = C.padChartPick('label', names, used);

    if (fields.length < 3) {
      C.padError('a radar chart needs three measures or more in value=, it has ' + fields.length);
      return '';
    }

    var series = new Map();
    var index  = 0;

    C.values(rows).forEach(function (row) {

      row = C.padChartRow(row);
      index++;
      var values = [];

      for (var f = 0; f < fields.length; f++) {
        var v = C.get(row, fields[f]);
        if (!C.padChartFinite(v) || C.float(v) < 0)
          return;
        values.push(C.num(v));
      }

      // A name a row before had is told apart by its number - Phone (2).

      var name = C.padChartText(row, label);
      name = (name !== '') ? name : 'Row ' + index;
      var base = name;

      for (var n = 2; series.has(C.key(name)); n++)
        name = base + ' (' + n + ')';

      series.set(C.key(name), values);

    });

    if (!series.size)
      return '';

    if (series.size > 8) {
      C.padError('the radar chart has ' + series.size + ' rows - eight at most, each its own colour');
      series = new Map(Array.from(series.entries()).slice(0, 8));
    }

    var to    = C.padTagParm('to');
    var high  = -Infinity;
    series.forEach(function (values) { values.forEach(function (v) { high = Math.max(high, v); }); });
    var ticks = C.padChartTicks(0, C.padChartFinite(to) && C.float(to) > 0 ? C.num(to) : Math.max(high, 1e-9), 4);
    var scale = ticks[ticks.length - 1];
    var n     = fields.length;
    var desc  = [];

    series.forEach(function (values, name) {
      var parts = fields.map(function (field, i) { return field + ' ' + C.padChartNumber(values[i]); });
      desc.push(C.str(name) + ': ' + parts.join(', '));
    });

    var title = C.str(C.padTagParm('title', fields.map(C.ucfirst).join(', ')));

    var svg = C.padChartOpen('radar', [fields, series, scale], desc, title, width, height)[1];

    var legendH = 0;

    if (series.size > 1) {
      var legend = C.padChartLegend(Array.from(series.keys()), 10, 6, width - 20);
      svg += legend[0];
      legendH = legend[1];
    }

    var labelW = 0;
    fields.forEach(function (field) { labelW = Math.max(labelW, C.padChartWidth(field)); });

    var r  = Math.max(20, Math.min((width - 2 * Math.min(labelW, width * 0.25)) / 2 - 12, (height - legendH - 40) / 2));
    var cx = width / 2;
    var cy = legendH + 20 + r;
    var sin = function (i) { return C.round(Math.sin(2 * Math.PI * i / n), 10); };
    var cos = function (i) { return C.round(Math.cos(2 * Math.PI * i / n), 10); };

    var pt = function (i, v) { return [cx + v / scale * r * sin(i), cy - v / scale * r * cos(i)]; };

    // The rings at the ticks, the spokes, and the measures named past their spokes.

    ticks.forEach(function (tick) {
      if (tick <= 0)
        return;
      var d = '';
      for (var i = 0; i < n; i++) {
        var p = pt(i, tick);
        d += (i ? 'L' : 'M') + C.padChartXY(p[0]) + ',' + C.padChartXY(p[1]);
      }
      svg += '<path class="pc-grid" fill="none" d="' + d + 'Z"/>';
      if (tick < scale)
        svg += '<text class="pc-tick" x="' + C.padChartXY(cx + 4) + '" y="' + C.padChartXY(cy - tick / scale * r - 3) + '">' + C.padChartNumber(tick) + '</text>';
    });

    fields.forEach(function (field, i) {
      var p = pt(i, scale), x = p[0], y = p[1];
      svg += '<line class="pc-grid" x1="' + C.padChartXY(cx) + '" y1="' + C.padChartXY(cy) + '" x2="' + C.padChartXY(x) + '" y2="' + C.padChartXY(y) + '"/>';
      var s      = sin(i);
      var c      = cos(i);
      var anchor = (s > 0.2) ? 'start' : (s < -0.2 ? 'end' : 'middle');
      var room   = (anchor === 'middle') ? width : (anchor === 'start' ? width - x - 10 : x - 10);
      svg += '<text x="' + C.padChartXY(x + 8 * s) + '" y="' + C.padChartXY(y - 8 * c + 4 + (c < -0.5 ? 6 : 0)) + '" text-anchor="' + anchor + '">'
           + C.padChartAttr(C.padChartFit(C.ucfirst(field), room)) + '</text>';
    });

    // A polygon per row: a light fill, its outline, a point per measure with its tooltip.

    var j = 0;

    series.forEach(function (values, name) {

      var d = '', dots = '';

      values.forEach(function (v, i) {
        var p = pt(i, Math.min(v, scale));
        d    += (i ? 'L' : 'M') + C.padChartXY(p[0]) + ',' + C.padChartXY(p[1]);
        dots += '<circle class="' + C.padChartSlot(j) + ' pc-ring" cx="' + C.padChartXY(p[0]) + '" cy="' + C.padChartXY(p[1]) + '" r="3.5">'
              + '<title>' + C.padChartAttr(C.str(name) + ' · ' + fields[i] + ': ' + C.padChartNumber(v)) + '</title></circle>';
      });

      svg += '<path class="' + C.padChartSlot(j) + ' pc-area-fill" d="' + d + 'Z"/>'
           + '<path class="pc-l' + (j + 1) + '" d="' + d + 'Z"/>' + dots;

      j++;

    });

    return svg + '</svg>';

  };

})(typeof window !== 'undefined' ? window.PadChart : globalThis.PadChart);
