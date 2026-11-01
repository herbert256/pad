// waffle.php in the browser: parts of a whole as squares (core.js says how these files are written).
// needs: core

(function (C) {

  'use strict';

  C.padChartWaffle = function (rows, width, height) {

    var guess   = C.padChartGuess(rows);
    var numbers = guess[0], names = guess[1];

    var used  = [];
    var value = C.padChartPick('value', numbers, used);
    var label = C.padChartPick('label', names,   used);
    var cells = C.padTagParm('cells', 100);

    if (!C.isNumeric(cells) || C.float(cells) < 4 || C.float(cells) > 2500 || C.float(cells) !== C.int(cells)) {
      if (C.spec.check)
        C.padError('the waffle needs cells= a whole number from 4 to 2500');
      cells = 100;
    }

    cells = C.int(cells);

    var points = C.padChartPoints(rows, label, value).filter(function (point) { return point[1] > 0; });
    var folded = (points.length > 8);

    if (folded) {

      // arsort keeps equal values in their order; the seven largest are kept.

      var order = points.map(function (p, i) { return i; })
                        .sort(function (a, b) { return points[a][1] < points[b][1] ? 1 : (points[a][1] > points[b][1] ? -1 : 0); });
      var keep  = order.slice(0, 7);
      var kept  = [];
      var other = 0;

      points.forEach(function (point, i) {
        if (keep.indexOf(i) >= 0) kept.push(point);
        else other += point[1];
      });

      points = kept;
      points.push(['Other', other]);

    }

    var total = points.reduce(function (s, p) { return s + p[1]; }, 0);

    if (!points.length || !isFinite(total) || total <= 0)
      return '';

    var count = [], rest = [];

    points.forEach(function (point, i) {
      var exact = point[1] / total * cells;
      count[i]  = C.int(Math.floor(exact));
      rest[i]   = exact - count[i];
    });

    var left = cells - count.reduce(function (s, n) { return s + n; }, 0);
    var ranked = rest.map(function (r, i) { return i; });

    ranked.sort(function (a, b) { return (rest[b] < rest[a] ? -1 : (rest[b] > rest[a] ? 1 : 0)) || (a < b ? -1 : (a > b ? 1 : 0)); });

    ranked.slice(0, left).forEach(function (i) { count[i]++; });

    var share = function (v) { return C.padChartNumber(C.round(v / total * 100, 1)) + '%'; };
    var slot  = function (i) { return (folded && i === points.length - 1) ? 'pc-co' : C.padChartSlot(i); };
    var desc  = [];

    points.forEach(function (point, i) {
      desc.push(point[0] + ': ' + C.padChartNumber(point[1]) + ' (' + share(point[1]) + ', ' + count[i] + ' of ' + cells + ' squares)');
    });

    var title = C.str(C.padTagParm('title', C.ucfirst(value !== '' ? value : 'value') + (label !== '' ? ' by ' + label : '')));

    var open = C.padChartOpen('waffle', [points, cells], desc, title, width, height);
    var svg  = open[1];

    var cols = Math.max(1, C.int(C.round(Math.sqrt(cells))));
    var rowN = C.int(Math.ceil(cells / cols));
    var size = Math.max(2, Math.min((height - 20) / rowN, (width * 0.55 - 20) / cols));
    var gap  = Math.max(1, size * 0.12);
    var x0   = 10;
    var y0   = (height - size * rowN) / 2;
    var cell = 0;
    var x, y;

    points.forEach(function (point, i) {

      var tip = C.padChartAttr(desc[i]);

      for (var k = 0; k < count[i]; k++, cell++) {
        x    = x0 + (cell % cols) * size;
        y    = y0 + Math.trunc(cell / cols) * size;
        svg += '<rect class="pc-mark ' + slot(i) + '" x="' + C.padChartXY(x) + '" y="' + C.padChartXY(y) + '" width="' + C.padChartXY(size - gap) + '" height="' + C.padChartXY(size - gap) + '"'
             + ' rx="' + C.padChartXY(Math.min(3, size * 0.15)) + '"><title>' + tip + '</title></rect>';
      }

    });

    x = x0 + cols * size + 22;
    y = Math.max(4, height / 2 - points.length * 9);

    points.forEach(function (point, i) {
      var text = point[0] + '  ' + C.padChartNumber(point[1]) + ' (' + share(point[1]) + ')';
      svg += '<rect class="' + slot(i) + '" x="' + C.padChartXY(x) + '" y="' + C.padChartXY(y + i * 18) + '" width="10" height="10" rx="2"/>'
           + '<text x="' + C.padChartXY(x + 16) + '" y="' + C.padChartXY(y + i * 18 + 9) + '">' + C.padChartAttr(C.padChartFit(text, width - x - 20)) + '</text>';
    });

    return svg + '</svg>';

  };

})(typeof window !== 'undefined' ? window.PadChart : globalThis.PadChart);
