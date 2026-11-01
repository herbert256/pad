// proportional.php in the browser: sizes compared by area, a circle or a square per row
// (core.js says how these files are written).
// needs: core

(function (C) {

  'use strict';

  C.padChartProportional = function (rows, width, height) {

    var guess   = C.padChartGuess(rows);
    var numbers = guess[0], names = guess[1];
    var used    = [];
    var field   = C.padChartPick('value', numbers, used);
    var label   = C.padChartPick('label', names, used);
    var square  = C.padTagParm('square', false);
    square = (square !== false && square !== '' && square !== 0 && square !== '0');
    var items   = [];
    var index   = 0;

    C.values(rows).forEach(function (row) {
      row = C.padChartRow(row);
      index++;
      var v = C.get(row, field);
      if (!C.padChartFinite(v) || C.float(v) <= 0)
        return;
      var name = C.padChartText(row, label);
      items.push([name !== '' ? name : 'Row ' + index, C.num(v)]);
    });

    if (!items.length)
      return '';

    var desc = items.map(function (item) { return item[0] + ': ' + C.padChartNumber(item[1]); });

    var title = C.str(C.padTagParm('title', C.ucfirst(field !== '' ? field : 'value') + (label !== '' ? ' by ' + label : '')));

    var open = C.padChartOpen('proportional', [items, square], desc, title, width, height);
    var svg  = open[1];

    var max    = C.hi(items.map(function (item) { return item[1]; }));
    var gap    = 14;
    var textH  = 32;
    var layout = function (big) {
      var lines = [];
      var line  = [];
      var used  = 0;
      items.forEach(function (item, i) {
        var size = big * Math.sqrt(item[1] / max);
        var cell = Math.max(size, Math.min(C.padChartWidth(item[0]), 140), C.padChartWidth(C.padChartNumber(item[1]))) + gap;
        if (line.length && used + cell > width - 16) {
          lines.push([line, used]);
          line = [];
          used = 0;
        }
        line.push([i, size, cell]);
        used += cell;
      });
      lines.push([line, used]);
      var tall = 0;
      lines.forEach(function (l) { tall += C.hi(l[0].map(function (e) { return e[1]; })) + textH + 10; });
      return [lines, tall];
    };

    var low  = 4;
    var high = Math.max(4, Math.min(height - textH - 20, width - 16 - gap));

    for (var round = 0; round < 30; round++) {
      var mid = (low + high) / 2;
      if (layout(mid)[1] <= height - 12)
        low = mid;
      else
        high = mid;
    }

    var done  = layout(low);
    var lines = done[0];
    var tall  = done[1];

    var top = (height - tall) / 2 + 4;

    lines.forEach(function (l) {

      var line = l[0], used = l[1];
      var base = top + C.hi(line.map(function (e) { return e[1]; }));
      var x    = (width - used) / 2;

      svg += '<line class="pc-grid" x1="' + C.padChartXY(x) + '" x2="' + C.padChartXY(x + used - gap) + '" y1="' + C.padChartXY(base) + '" y2="' + C.padChartXY(base) + '"/>';

      line.forEach(function (e) {

        var i = e[0], size = e[1], cell = e[2];
        var name = items[i][0], v = items[i][1];

        var c   = x + (cell - gap) / 2;
        var tip = '<title>' + C.padChartAttr(desc[i]) + '</title>';

        if (square)
          svg += '<rect class="pc-mark pc-c1" x="' + C.padChartXY(c - size / 2) + '" y="' + C.padChartXY(base - size) + '" width="' + C.padChartXY(Math.max(0.5, size)) + '" height="' + C.padChartXY(Math.max(0.5, size)) + '" rx="2">' + tip + '</rect>';
        else
          svg += '<circle class="pc-mark pc-c1" cx="' + C.padChartXY(c) + '" cy="' + C.padChartXY(base - size / 2) + '" r="' + C.padChartXY(Math.max(0.5, size / 2)) + '">' + tip + '</circle>';

        svg += '<text x="' + C.padChartXY(c) + '" y="' + C.padChartXY(base + 15) + '" text-anchor="middle" style="font-weight:600">' + C.padChartAttr(C.padChartNumber(v)) + '</text>'
             + '<text x="' + C.padChartXY(c) + '" y="' + C.padChartXY(base + 29) + '" text-anchor="middle">' + C.padChartAttr(C.padChartFit(name, cell - 4)) + '</text>';

        x += cell;

      });

      top = base + textH + 10;

    });

    return svg + '</svg>';

  };

})(typeof window !== 'undefined' ? window.PadChart : globalThis.PadChart);
