// ecdf.php in the browser: the empirical cumulative distribution, a step line per group - with
// padChartQuantile of distribution.js (core.js says how these files are written).
// needs: core, distribution

(function (C) {

  'use strict';

  C.padChartEcdf = function (rows, width, height) {

    var numbers = C.padChartGuess(rows)[0];

    var used  = [];
    var field = C.padChartPick('value', numbers, used);
    var label = C.str(C.padTagParm('label'));

    if (field === '') {
      if (C.count(rows))
        C.padError("an ecdf chart needs a numeric field - value='field'");
      return '';
    }

    if (C.count(rows) && !C.padChartFieldSomewhere(rows, field))
      C.padError("the ecdf chart's value='" + C.padMakeSafe(field, 30) + "' is no field of its rows");

    // Per group, in the order the groups first appear: its numbers, and the rows of it whose
    // value is no number.

    var groups  = new Map();
    var missing = new Map();

    C.values(rows).forEach(function (row) {

      row = C.padChartRow(row);

      var v    = C.get(row, field);
      var name = C.key(label !== '' ? C.padChartText(row, label) : C.ucfirst(field));

      if (!groups.has(name)) groups.set(name, []);
      if (!missing.has(name)) missing.set(name, 0);

      if (C.padChartFinite(v))
        groups.get(name).push(C.num(v));
      else
        missing.set(name, missing.get(name) + 1);

    });

    // The steps of a group: each value it has once, with the count of the values at or
    // below it.

    var sets = new Map();
    var desc = [];

    groups.forEach(function (list, name) {

      var n    = list.length;
      var none = missing.get(name) ? ', ' + C.str(missing.get(name)) + (missing.get(name) === 1 ? ' row' : ' rows') + ' without a number' : '';

      if (!n) {
        desc.push(C.str(name) + ': no values' + none);
        return;
      }

      var values = list.slice().sort(function (a, b) { return a - b; });
      var steps  = [];

      values.forEach(function (v, i) {
        if (steps.length && steps[steps.length - 1][0] === v)
          steps[steps.length - 1][1] = i + 1;
        else
          steps.push([v, i + 1]);
      });

      sets.set(name, [values, steps, desc.length]);

      desc.push(C.str(name) + ': ' + n + ' ' + (n === 1 ? 'value' : 'values') + ', median ' + C.padChartNumber(C.padChartQuantile(values, 0.5))
              + ', range ' + C.padChartNumber(values[0]) + ' – ' + C.padChartNumber(values[n - 1]) + none);

    });

    if (!sets.size)
      return '';

    var title = C.str(C.padTagParm('title', C.ucfirst(field) + (label !== '' ? ' by ' + label : '') + ', cumulative share'));

    var svg = C.padChartOpen('ecdf', sets, desc, title, width, height)[1];

    var lo = null, hi = null;

    sets.forEach(function (set) {
      var values = set[0];
      lo = Math.min(lo === null ? Infinity : lo, values[0]);
      hi = Math.max(hi === null ? -Infinity : hi, values[values.length - 1]);
    });

    var legendH = 0;

    if (sets.size > 1) {
      var legend = C.padChartLegend(Array.from(sets.keys()), 12, 6, width - 24);
      svg += legend[0];
      legendH = legend[1];
    }

    // The share axis on the left - 0 to 100% in quarters, in halves on a low chart - and the
    // value axis along the bottom, its widest half number kept inside the picture.

    var top    = legendH + 12;
    var bottom = 24;
    var plotH  = Math.max(10, height - top - bottom);
    var shares = (plotH >= 120) ? [0, 0.25, 0.5, 0.75, 1] : [0, 0.5, 1];
    var left   = C.int(Math.ceil(C.padChartWidth('100%'))) + 10;
    var ticks  = C.padChartTicks(lo, hi, Math.max(3, Math.min(8, C.int((width - left) / 90))));
    var low    = ticks[0];
    var high   = ticks[ticks.length - 1];
    var right  = Math.max(12, C.int(Math.ceil(C.padChartWidth(C.padChartNumber(high)) / 2)) + 4);
    var plotW  = Math.max(10, width - left - right);
    var base   = top + plotH;
    var x      = function (v) { return left + (v - low) / (high - low) * plotW; };
    var y      = function (p) { return top + (1 - p) * plotH; };

    shares.forEach(function (share) {
      svg += '<line class="pc-grid" x1="' + C.str(left) + '" x2="' + C.str(left + plotW) + '" y1="' + C.padChartXY(y(share)) + '" y2="' + C.padChartXY(y(share)) + '"/>'
           + '<text x="' + C.str(left - 6) + '" y="' + C.padChartXY(y(share) + 4) + '" text-anchor="end">' + C.str(share * 100) + '%</text>';
    });

    ticks.forEach(function (tick) {
      svg += '<line class="pc-grid" x1="' + C.padChartXY(x(tick)) + '" x2="' + C.padChartXY(x(tick)) + '" y1="' + C.str(top) + '" y2="' + C.str(base) + '"/>'
           + '<text x="' + C.padChartXY(x(tick)) + '" y="' + C.str(height - 8) + '" text-anchor="middle">' + C.padChartNumber(tick) + '</text>';
    });

    svg += '<line class="pc-axis" x1="' + C.str(left) + '" x2="' + C.str(left + plotW) + '" y1="' + C.str(base) + '" y2="' + C.str(base) + '"/>';

    // A step line per group: from 0 at the left end of the axis, along to each value and up
    // by its share, from 100% on along to the right end. Past eight groups the grey of
    // 'Other', as the legend has it.

    var i = 0;

    sets.forEach(function (set, name) {

      var n     = set[0].length;
      var steps = set[1];
      var line  = (i < 8) ? 'pc-l' + (i + 1) : 'pc-edge';
      var d     = 'M' + C.padChartXY(x(low)) + ',' + C.padChartXY(y(0));

      steps.forEach(function (step) {
        d += 'H' + C.padChartXY(x(step[0])) + 'V' + C.padChartXY(y(step[1] / n));
      });

      svg += '<path class="' + line + '" d="' + d + 'H' + C.padChartXY(x(high)) + '"><title>' + C.padChartAttr(desc[set[2]]) + '</title></path>';

      if (steps.length <= 100)
        steps.forEach(function (step) {
          svg += '<circle class="pc-hit" cx="' + C.padChartXY(x(step[0])) + '" cy="' + C.padChartXY(y(step[1] / n)) + '" r="6"><title>'
               + C.padChartAttr((sets.size > 1 ? C.str(name) + ': ' : '') + 'at or below ' + C.padChartNumber(step[0]) + ': ' + C.str(step[1]) + ' of ' + C.str(n) + ', '
                                + C.padChartNumber(C.round(step[1] / n * 100, 1)) + '%') + '</title></circle>';
        });

      i++;

    });

    return svg + '</svg>';

  };

})(typeof window !== 'undefined' ? window.PadChart : globalThis.PadChart);
