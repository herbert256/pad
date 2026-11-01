// pie.php in the browser: pie and donut (core.js says how these files are written).
// needs: core

(function (C) {

  'use strict';

  C.padChartPie = function (kind, points, title, width, height) {

    points = points.filter(function (p) { return p[1] > 0; });
    var folded = (points.length > 8);

    if (folded) {

      // arsort keeps equal values in their order; the seven largest are kept.

      var order = points.map(function (p, i) { return i; })
                        .sort(function (a, b) { return points[b][1] - points[a][1]; });
      var keep  = order.slice(0, 7);
      var kept  = [];
      var other = 0;

      points.forEach(function (p, i) {
        if (keep.indexOf(i) >= 0) kept.push(p);
        else other += p[1];
      });

      points = kept;
      points.push(['Other', other]);

    }

    var total = points.reduce(function (s, p) { return s + p[1]; }, 0);

    if (!points.length || !isFinite(total) || total <= 0)
      return '';

    var share = function (v) { return C.padChartNumber(C.round(v / total * 100, 1)) + '%'; };
    var desc  = points.map(function (p) { return p[0] + ': ' + C.padChartNumber(p[1]) + ' (' + share(p[1]) + ')'; });

    var open = C.padChartOpen(kind, points, desc, title, width, height);
    var svg  = open[1];

    var r     = Math.max(10, Math.min(height - 20, width * 0.5 - 20) / 2);
    var cx    = 10 + r;
    var cy    = height / 2;
    var inner = (kind === 'donut') ? r * 0.6 : 0;
    var a     = 0;
    var slot  = function (i) { return (folded && i === points.length - 1) ? 'pc-co' : C.padChartSlot(i); };

    points.forEach(function (p, i) {
      var sweep = p[1] / total * 2 * Math.PI;
      var d     = C.padChartArc(cx, cy, inner, r, a, a + sweep);
      a += sweep;
      svg += '<path class="pc-mark pc-gap ' + slot(i) + '" fill-rule="evenodd" d="' + d + '"><title>'
           + C.padChartAttr(p[0] + ': ' + C.padChartNumber(p[1]) + ' (' + share(p[1]) + ')') + '</title></path>';
    });

    if (inner)
      svg += '<text class="pc-big" x="' + C.padChartXY(cx) + '" y="' + C.padChartXY(cy + 6) + '" text-anchor="middle">' + C.padChartNumber(total) + '</text>';

    var x = cx + r + 24;
    var y = Math.max(4, cy - points.length * 9);

    points.forEach(function (p, i) {
      var text = p[0] + '  ' + C.padChartNumber(p[1]) + ' (' + share(p[1]) + ')';
      svg += '<rect class="' + slot(i) + '" x="' + C.padChartXY(x) + '" y="' + C.padChartXY(y + i * 18) + '" width="10" height="10" rx="2"/>'
           + '<text x="' + C.padChartXY(x + 16) + '" y="' + C.padChartXY(y + i * 18 + 9) + '">' + C.padChartAttr(C.padChartFit(text, width - x - 20)) + '</text>';
    });

    return svg + '</svg>';

  };

})(typeof window !== 'undefined' ? window.PadChart : globalThis.PadChart);
