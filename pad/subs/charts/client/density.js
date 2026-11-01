// density.php in the browser: the density curve and the violin - with padChartQuantile and
// padChartValueAxis of distribution.js (core.js says how these files are written).
// needs: core, distribution

(function (C) {

  'use strict';

  C.padChartDensity = function (kind, rows, width, height) {

    var numbers = C.padChartGuess(rows)[0];

    var used   = [];
    var field  = C.padChartPick('value', numbers, used);
    var label  = C.str(C.padTagParm('label'));
    var band   = C.padTagParm('bandwidth');
    var groups = new Map();

    if (band !== '' && (!C.padChartFinite(band) || C.float(band) <= 0)) {
      C.padError('the bandwidth of a ' + kind + ' chart is a number above 0');
      band = '';
    }

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

    // Per group: the sorted values, the bandwidth and the five numbers the tooltips and the
    // violin's bar tell.

    var sets = new Map();
    var desc = [];

    groups.forEach(function (list, name) {

      var values = list.slice().sort(function (a, b) { return a - b; });

      var n    = values.length;
      var mean = values.reduce(function (s, v) { return s + v; }, 0) / n;
      var sd   = Math.sqrt(values.reduce(function (s, v) { return s + (v - mean) * (v - mean); }, 0) / Math.max(1, n - 1));
      var q1   = C.padChartQuantile(values, 0.25);
      var med  = C.padChartQuantile(values, 0.5);
      var q3   = C.padChartQuantile(values, 0.75);
      var iqr  = (q3 - q1) / 1.34;
      var h    = (band !== '') ? C.num(band) : 0.9 * (iqr > 0 ? Math.min(sd, iqr) : sd) * Math.pow(n, -0.2);

      if (!(h > 0) || !isFinite(h))
        h = (sd > 0 && isFinite(sd)) ? sd : ((values[n - 1] - values[0]) / 10 || Math.max(Math.abs(mean) * 0.1, 1e-9));

      if (!(h > 0) || !isFinite(h))
        h = 1;

      sets.set(name, [values, h, q1, med, q3]);

      desc.push(C.str(name) + ': ' + n + ' values, median ' + C.padChartNumber(med) + ', quartiles ' + C.padChartNumber(q1) + ' – ' + C.padChartNumber(q3)
              + ', range ' + C.padChartNumber(values[0]) + ' – ' + C.padChartNumber(values[n - 1]));

    });

    var count = 0;
    groups.forEach(function (values) { count += values.length; });

    var title = C.str(C.padTagParm('title', C.ucfirst(field !== '' ? field : 'value') + (label !== '' ? ' by ' + label : ', ' + count + ' values')));

    var svg = C.padChartOpen(kind, sets, desc, title, width, height)[1];

    var kde = function (values, h, x) {
      var sum = 0;
      values.forEach(function (v) { var t = (x - v) / h; sum += Math.exp(-0.5 * (t * t)); });
      return sum / (values.length * h * Math.sqrt(2 * Math.PI));
    };

    if (kind === 'violin')
      return C.padChartViolin(svg, sets, desc, kde, width, height);

    // The value axis along the bottom, wide enough for the tails of every curve.

    var lo = null, hi = null;

    sets.forEach(function (set) {
      var values = set[0], h = set[1];
      lo = Math.min(lo === null ? Infinity : lo, values[0] - 2.5 * h);
      hi = Math.max(hi === null ? -Infinity : hi, values[values.length - 1] + 2.5 * h);
    });

    var ticks   = C.padChartTicks(lo, hi, Math.max(3, Math.min(8, C.int(width / 90))));
    var low     = ticks[0];
    var high    = ticks[ticks.length - 1];
    var legendH = 0;

    if (sets.size > 1) {
      var legend = C.padChartLegend(Array.from(sets.keys()), 12, 6, width - 24);
      svg += legend[0];
      legendH = legend[1];
    }

    var left   = 14;
    var right  = 14;
    var top    = legendH + 14;
    var bottom = 24;
    var plotW  = width - left - right;
    var plotH  = Math.max(10, height - top - bottom);
    var x      = function (v) { return left + (v - low) / (high - low) * plotW; };
    var steps  = Math.max(40, Math.min(240, C.int(plotW / 3)));
    var curves = new Map();
    var peak   = 0;

    sets.forEach(function (set, name) {

      var values = set[0], h = set[1];
      var points = [], s;

      for (s = 0; s <= steps; s++)
        points.push(low + (high - low) * s / steps);

      // A bell narrower than the steps falls between them: the curve is read at the middle
      // value of each pixel's values too, and a bandwidth to either side of it.

      if ((high - low) / steps > h / 2) {
        var pixels = new Map();
        values.forEach(function (v) {
          var p = C.int(Math.floor((v - low) / (high - low) * plotW));
          if (!pixels.has(p)) pixels.set(p, []);
          pixels.get(p).push(v);
        });
        pixels.forEach(function (inside) {
          var v = inside[Math.trunc(inside.length / 2)];
          points.push(v - h, v, v + h);
        });
        points.sort(function (a, b) { return a - b; });
      }

      var curve = [];

      points.forEach(function (at) {
        var d = kde(values, h, at);
        curve.push([at, d]);
        peak = Math.max(peak, d);
      });

      curves.set(name, curve);

    });

    var base = top + plotH;
    var y    = function (d) { return base - d / ((peak || 1) * 1.08) * plotH; };

    ticks.forEach(function (tick) {
      svg += '<line class="pc-grid" x1="' + C.padChartXY(x(tick)) + '" x2="' + C.padChartXY(x(tick)) + '" y1="' + C.padChartXY(top) + '" y2="' + C.padChartXY(base) + '"/>'
           + '<text x="' + C.padChartXY(x(tick)) + '" y="' + C.str(height - 8) + '" text-anchor="middle">' + C.padChartNumber(tick) + '</text>';
    });

    // A curve per group: the wash with the group's tooltip, the line over it, and the median
    // dashed from the baseline up to the curve.

    var i = 0;

    curves.forEach(function (points, name) {

      var slot   = C.padChartSlot(sets.size > 1 ? i : 0);
      var stroke = (i < 8) ? 'pc-l' + (i + 1) : 'pc-edge';
      var line   = '';

      points.forEach(function (p, s) { line += (s ? 'L' : 'M') + C.padChartXY(x(p[0])) + ',' + C.padChartXY(y(p[1])); });

      var set = sets.get(name);
      var med = set[3];

      svg += '<path class="pc-mark ' + slot + '" fill-opacity="0.18" d="' + line + 'L' + C.padChartXY(x(high)) + ',' + C.padChartXY(base) + 'L' + C.padChartXY(x(low)) + ',' + C.padChartXY(base) + 'Z">'
           + '<title>' + C.padChartAttr(desc[i]) + '</title></path>'
           + '<path class="' + stroke + '" d="' + line + '"/>'
           + '<line class="' + stroke + '" style="stroke-width:1.5" stroke-dasharray="4 3" x1="' + C.padChartXY(x(med)) + '" x2="' + C.padChartXY(x(med)) + '"'
           + ' y1="' + C.padChartXY(base) + '" y2="' + C.padChartXY(y(kde(set[0], set[1], med))) + '">'
           + '<title>' + C.padChartAttr(C.str(name) + ': median ' + C.padChartNumber(med)) + '</title></line>';

      i++;

    });

    svg += '<line class="pc-axis" x1="' + C.str(left) + '" x2="' + C.str(width - right) + '" y1="' + C.padChartXY(base) + '" y2="' + C.padChartXY(base) + '"/>';

    return svg + '</svg>';

  };

  // The violins: a band per group along the bottom, the values up the left as a box plot has
  // them, every shape on one scale of density.

  C.padChartViolin = function (svg, sets, desc, kde, width, height) {

    var all = [];
    sets.forEach(function (set) { all = all.concat(set[0]); });

    var lowest  = all.reduce(function (m, v) { return v < m ? v : m; });
    var highest = all.reduce(function (m, v) { return v > m ? v : m; });

    var axis = C.padChartValueAxis(svg, C.padChartTicks(lowest, highest, height < 200 ? 3 : 5), width, height, 10, 24);
    svg = axis[0];
    var left = axis[1], y = axis[2];

    var n      = sets.size;
    var band   = (width - left - 10) / n;
    var half   = Math.max(4, Math.min(80, band * 0.45));
    var shapes = new Map();
    var peak   = 0;

    sets.forEach(function (set, name) {
      var values = set[0], h = set[1];
      var from  = values[0];
      var to    = values[values.length - 1];
      var steps = Math.max(2, Math.min(200, C.int(Math.abs(y(from) - y(to)) / 2)));
      var shape = [];
      for (var s = 0; s <= steps; s++) {
        var at = from + (to - from) * s / steps;
        var d  = kde(values, h, at);
        shape.push([at, d]);
        peak = Math.max(peak, d);
      }
      shapes.set(name, shape);
    });

    var i = 0;

    shapes.forEach(function (points, name) {

      var set = sets.get(name);
      var values = set[0], q1 = set[2], med = set[3], q3 = set[4];

      var c    = left + (i + 0.5) * band;
      var w    = function (d) { return d / peak * half; };
      var slot = C.padChartSlot(n > 1 ? i : 0);
      var out  = '', back = '';

      points.forEach(function (p, s) {
        out  += (s ? 'L' : 'M') + C.padChartXY(c + w(p[1])) + ',' + C.padChartXY(y(p[0]));
        back  = 'L' + C.padChartXY(c - w(p[1])) + ',' + C.padChartXY(y(p[0])) + back;
      });

      var line = (n > 1 && i >= 8) ? 'pc-edge' : 'pc-l' + ((n > 1 ? i : 0) + 1);
      var bar  = Math.max(3, Math.min(8, half * 0.16));

      svg += '<path class="pc-mark ' + slot + '" fill-opacity="0.35" d="' + out + back + 'Z"><title>' + C.padChartAttr(desc[i]) + '</title></path>'
           + '<path class="' + line + '" style="stroke-width:1.5" d="' + out + back + 'Z"/>'
           + '<line class="pc-axis" style="stroke-width:1.5" x1="' + C.padChartXY(c) + '" x2="' + C.padChartXY(c) + '" y1="' + C.padChartXY(y(values[0])) + '" y2="' + C.padChartXY(y(values[values.length - 1])) + '"/>'
           + '<line class="pc-axis" style="stroke-width:' + C.padChartXY(bar) + '" x1="' + C.padChartXY(c) + '" x2="' + C.padChartXY(c) + '" y1="' + C.padChartXY(y(q1)) + '" y2="' + C.padChartXY(y(q3)) + '">'
           + '<title>' + C.padChartAttr(C.str(name) + ': quartiles ' + C.padChartNumber(q1) + ' – ' + C.padChartNumber(q3)) + '</title></line>'
           + '<circle class="pc-in" cx="' + C.padChartXY(c) + '" cy="' + C.padChartXY(y(med)) + '" r="' + C.padChartXY(Math.max(2, bar * 0.45)) + '">'
           + '<title>' + C.padChartAttr(C.str(name) + ': median ' + C.padChartNumber(med)) + '</title></circle>'
           + '<text x="' + C.padChartXY(c) + '" y="' + C.str(height - 8) + '" text-anchor="middle">' + C.padChartAttr(C.padChartFit(name, band - 4)) + '</text>';

      i++;

    });

    return svg + '</svg>';

  };

})(typeof window !== 'undefined' ? window.PadChart : globalThis.PadChart);
