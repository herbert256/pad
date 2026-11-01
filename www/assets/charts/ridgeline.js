// ridgeline.php in the browser: a density curve per group on a baseline of its own - with
// padChartQuantile of distribution.js (core.js says how these files are written).
// needs: core, distribution

(function (C) {

  'use strict';

  C.padChartRidgeline = function (rows, width, height) {

    var numbers = C.padChartGuess(rows)[0];

    var used    = [];
    var field   = C.padChartPick('value', numbers, used);
    var label   = C.str(C.padTagParm('label'));
    var band    = C.padTagParm('bandwidth');
    var overlap = C.padTagParm('overlap');

    if (field === '') {
      if (C.count(rows))
        C.padError("a ridgeline chart needs a numeric field - value='field'");
      return '';
    }

    if (C.count(rows) && !C.padChartFieldSomewhere(rows, field))
      C.padError("the ridgeline chart's value='" + C.padMakeSafe(field, 30) + "' is no field of its rows");

    if (band !== '' && (!C.padChartFinite(band) || C.float(band) <= 0)) {
      C.padError('the bandwidth of a ridgeline chart is a number above 0');
      band = '';
    }

    if (overlap !== '' && (!C.padChartFinite(overlap) || C.float(overlap) <= 0 || C.float(overlap) > 10)) {
      C.padError('the overlap of a ridgeline chart is a number above 0 and up to 10 - the bands its tallest ridge rises');
      overlap = '';
    }

    overlap = (overlap !== '') ? C.num(overlap) : 1.5;

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

    // One bandwidth for every ridge, from all the values together, with the fallbacks of the
    // density curve when the rule gives none.

    var all = [];
    groups.forEach(function (values) { values.forEach(function (v) { all.push(v); }); });

    all.sort(function (a, b) { return a - b; });

    var count = all.length;
    var mean  = all.reduce(function (s, v) { return s + v; }, 0) / count;
    var sd    = Math.sqrt(all.reduce(function (s, v) { return s + (v - mean) * (v - mean); }, 0) / Math.max(1, count - 1));
    var iqr   = (C.padChartQuantile(all, 0.75) - C.padChartQuantile(all, 0.25)) / 1.34;
    var h     = (band !== '') ? C.num(band) : 0.9 * (iqr > 0 ? Math.min(sd, iqr) : sd) * Math.pow(count, -0.2);

    if (!(h > 0) || !isFinite(h))
      h = (sd > 0 && isFinite(sd)) ? sd : ((all[count - 1] - all[0]) / 10 || Math.max(Math.abs(mean) * 0.1, 1e-9));

    if (!(h > 0) || !isFinite(h))
      h = 1;

    var sets = new Map();
    var desc = [];

    groups.forEach(function (list, name) {

      var values = list.slice().sort(function (a, b) { return a - b; });
      var n      = values.length;
      var med    = C.padChartQuantile(values, 0.5);

      sets.set(name, [values, med]);

      desc.push(C.str(name) + ': ' + n + ' ' + (n === 1 ? 'value' : 'values') + ', median ' + C.padChartNumber(med)
              + ', quartiles ' + C.padChartNumber(C.padChartQuantile(values, 0.25)) + ' – ' + C.padChartNumber(C.padChartQuantile(values, 0.75))
              + ', range ' + C.padChartNumber(values[0]) + ' – ' + C.padChartNumber(values[n - 1]));

    });

    desc.push('one bandwidth for every ridge, ' + C.padChartNumber(h));

    var title = C.str(C.padTagParm('title', C.ucfirst(field) + (label !== '' ? ' by ' + label : ', ' + count + ' values')));

    var svg = C.padChartOpen('ridgeline', [sets, h, overlap], desc, title, width, height)[1];

    // The names on the left, at most a third of the width; the value axis along the bottom,
    // wide enough for the tails of every ridge.

    var labelW = 0;
    sets.forEach(function (set, name) { labelW = Math.max(labelW, C.padChartWidth(name)); });

    var n      = sets.size;
    var left   = 12 + Math.min(labelW, width / 3) + 10;
    var ticks  = C.padChartTicks(all[0] - 2.5 * h, all[count - 1] + 2.5 * h, Math.max(3, Math.min(8, C.int((width - left) / 90))));
    var low    = ticks[0];
    var high   = ticks[ticks.length - 1];
    var right  = Math.max(14, C.int(Math.ceil(C.padChartWidth(C.padChartNumber(high)) / 2)) + 4);
    var top    = 8;
    var bottom = 24;
    var plotW  = Math.max(10, width - left - right);
    var plotH  = Math.max(10, height - top - bottom);
    var step   = plotH / (n - 1 + overlap);
    var x      = function (v) { return left + (v - low) / (high - low) * plotW; };
    var base   = function (i) { return top + (overlap + i) * step; };

    // Each ridge read at about every 3px of the axis - and where a bell narrower than that
    // would fall between the points, at the middle value of each pixel's values and a
    // bandwidth to either side of it, as the density curve does.

    var steps  = Math.max(40, Math.min(240, C.int(plotW / 3)));
    var curves = new Map();
    var peak   = 0;

    sets.forEach(function (set, name) {

      var values = set[0];
      var points = [], s;

      for (s = 0; s <= steps; s++)
        points.push(low + (high - low) * s / steps);

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

      curves.set(name, points.map(function (at) {
        var d = C.padChartKde(values, h, at);
        peak = Math.max(peak, d);
        return [at, d];
      }));

    });

    var rise = function (d) { return d / (peak || 1) * overlap * step; };

    ticks.forEach(function (tick) {
      svg += '<line class="pc-grid" x1="' + C.padChartXY(x(tick)) + '" x2="' + C.padChartXY(x(tick)) + '" y1="' + C.str(top) + '" y2="' + C.padChartXY(base(n - 1)) + '"/>'
           + '<text x="' + C.padChartXY(x(tick)) + '" y="' + C.str(height - 8) + '" text-anchor="middle">' + C.padChartNumber(tick) + '</text>';
    });

    // The ridges from the top down, each over the ones behind it: its baseline, the shape
    // with the surface around it and the group's tooltip, the median, and the name.

    var i = 0;

    curves.forEach(function (points, name) {

      var b    = base(i);
      var med  = sets.get(name)[1];
      var line = '';

      points.forEach(function (p, s) { line += (s ? 'L' : 'M') + C.padChartXY(x(p[0])) + ',' + C.padChartXY(b - rise(p[1])); });

      svg += '<line class="pc-grid" x1="' + C.padChartXY(left) + '" x2="' + C.padChartXY(left + plotW) + '" y1="' + C.padChartXY(b) + '" y2="' + C.padChartXY(b) + '"/>'
           + '<path class="pc-mark pc-c1 pc-gap" fill-opacity="0.85" d="' + line + 'L' + C.padChartXY(x(points[points.length - 1][0])) + ',' + C.padChartXY(b)
           + 'L' + C.padChartXY(x(points[0][0])) + ',' + C.padChartXY(b) + 'Z"><title>' + C.padChartAttr(desc[i]) + '</title></path>'
           + '<line class="pc-ring" x1="' + C.padChartXY(x(med)) + '" x2="' + C.padChartXY(x(med)) + '" y1="' + C.padChartXY(b) + '" y2="' + C.padChartXY(b - rise(C.padChartKde(sets.get(name)[0], h, med))) + '">'
           + '<title>' + C.padChartAttr(C.str(name) + ': median ' + C.padChartNumber(med)) + '</title></line>'
           + '<text x="' + C.padChartXY(left - 10) + '" y="' + C.padChartXY(b - 3) + '" text-anchor="end">' + C.padChartAttr(C.padChartFit(name, left - 22)) + '</text>';

      i++;

    });

    svg += '<line class="pc-axis" x1="' + C.padChartXY(left) + '" x2="' + C.padChartXY(left + plotW) + '" y1="' + C.padChartXY(base(n - 1)) + '" y2="' + C.padChartXY(base(n - 1)) + '"/>';

    return svg + '</svg>';

  };

  // The Gaussian kernel density of values at x, for the bandwidth h.

  C.padChartKde = function (values, h, x) {
    var sum = 0;
    values.forEach(function (v) { var t = (x - v) / h; sum += Math.exp(-0.5 * (t * t)); });
    return sum / (values.length * h * Math.sqrt(2 * Math.PI));
  };

})(typeof window !== 'undefined' ? window.PadChart : globalThis.PadChart);
