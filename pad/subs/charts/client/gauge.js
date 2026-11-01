// gauge.php in the browser: one number on a half ring (core.js says how these files are written).
// needs: core

(function (C) {

  'use strict';

  C.padChartGauge = function (rows, width, height) {

    var value = C.padTagParm('value');
    var row   = [];
    var first = C.values(rows);

    if (first.length)
      row = C.padChartRow(first[0]);

    if (!C.isNumeric(value)) {
      var numbers = C.padChartGuess(rows)[0];
      var field   = (C.str(value) !== '') ? C.str(value) : (numbers[0] === undefined ? '' : numbers[0]);
      value = C.get(row, field);
    }

    // No number to show: nothing, and the @else@ shows.

    if (!C.padChartFinite(value))
      return '';

    value = C.num(value);

    var from = C.padTagParm('from', 0);
    var to   = C.padTagParm('to', 100);

    if (!C.padChartFinite(from) || !C.padChartFinite(to) || C.float(to) <= C.float(from)) {
      C.padError('the gauge needs from= below to=');
      return '';
    }

    from = C.num(from);
    to   = C.num(to);

    var label = C.str(C.padTagParm('label'));
    label = (C.get(row, label) !== null && C.isScalar(C.get(row, label))) ? C.str(C.get(row, label)) : label;

    var unit   = C.str(C.padTagParm('unit'));
    var target = C.padTagParm('target');
    var bands  = C.str(C.padTagParm('bands')).split(',').map(function (b) { return C.trim(b); }).filter(C.isNumeric);

    // sort ( $bands, SORT_NUMERIC ): stable, by the number.

    bands.sort(function (a, b) { var x = C.float(a), y = C.float(b); return x < y ? -1 : (x > y ? 1 : 0); });

    var shown = C.padChartNumber(value) + unit;
    var title = C.str(C.padTagParm('title', label !== '' ? label : 'Gauge'));

    var desc = [(label !== '' ? label + ': ' : '') + shown + ' on a scale of ' + C.padChartNumber(from) + ' to ' + C.padChartNumber(to) + unit];

    if (C.padChartFinite(target))
      desc.push('target ' + C.padChartNumber(C.num(target)) + unit);

    var open = C.padChartOpen('gauge', [value, from, to, bands, target, label], desc, title, width, height);
    var svg  = open[1];

    var r     = Math.max(20, Math.min(width / 2 - 16, height - 52));
    var inner = r * 0.7;
    var cx    = width / 2;
    var cy    = 12 + r;
    var at    = function (v) { return -Math.PI / 2 + (Math.min(to, Math.max(from, v)) - from) / (to - from) * Math.PI; };

    // The track, in bands when they are given, else one piece.

    if (bands.length) {
      var edges = [from].concat(bands.map(C.float).slice(0, 2), [to]);
      var kinds = (edges.length === 3) ? ['pc-c3', 'pc-c8'] : ['pc-c3', 'pc-c4', 'pc-c8'];
      kinds.forEach(function (kind, i) {
        svg += '<path class="' + kind + ' pc-band" d="' + C.padChartArc(cx, cy, inner, r, at(edges[i]), at(edges[i + 1])) + '"/>';
      });
    } else
      svg += '<path class="pc-track" d="' + C.padChartArc(cx, cy, inner, r, -Math.PI / 2, Math.PI / 2) + '"/>';

    // The value: a ring of its own just inside the track.

    if (at(value) > -Math.PI / 2)
      svg += '<path class="pc-c1 pc-mark" d="' + C.padChartArc(cx, cy, inner + (bands.length ? (r - inner) * 0.3 : 0), bands.length ? r - (r - inner) * 0.3 : r, -Math.PI / 2, at(value)) + '">'
           + '<title>' + C.padChartAttr(desc[0]) + '</title></path>';

    if (C.padChartFinite(target)) {
      var a = at(C.num(target));
      svg += '<line class="pc-target" x1="' + C.padChartXY(cx + (inner - 6) * Math.sin(a)) + '" y1="' + C.padChartXY(cy - (inner - 6) * Math.cos(a)) + '"'
           + ' x2="' + C.padChartXY(cx + (r + 6) * Math.sin(a)) + '" y2="' + C.padChartXY(cy - (r + 6) * Math.cos(a)) + '">'
           + '<title>' + C.padChartAttr('Target: ' + C.padChartNumber(C.num(target)) + unit) + '</title></line>';
    }

    var big = Math.max(14, Math.min(40, inner * 0.45));
    svg += '<text x="' + C.padChartXY(cx) + '" y="' + C.padChartXY(cy - 2) + '" text-anchor="middle" style="font-size:' + C.padChartXY(big) + 'px;font-weight:600">' + C.padChartAttr(shown) + '</text>';

    if (label !== '')
      svg += '<text x="' + C.padChartXY(cx) + '" y="' + C.padChartXY(cy + 18) + '" text-anchor="middle">' + C.padChartAttr(C.padChartFit(label, width - 20)) + '</text>';

    svg += '<text x="' + C.padChartXY(cx - (r + inner) / 2) + '" y="' + C.padChartXY(cy + 16) + '" text-anchor="middle">' + C.padChartNumber(from) + '</text>'
         + '<text x="' + C.padChartXY(cx + (r + inner) / 2) + '" y="' + C.padChartXY(cy + 16) + '" text-anchor="middle">' + C.padChartNumber(to) + '</text>';

    return svg + '</svg>';

  };

})(typeof window !== 'undefined' ? window.PadChart : globalThis.PadChart);
