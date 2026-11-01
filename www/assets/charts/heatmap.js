// heatmap.php in the browser: a grid of two categories, each cell coloured by its value
// (core.js says how these files are written).
// needs: core

(function (C) {

  'use strict';

  C.padChartHeatmap = function (rows, width, height) {

    var guess  = C.padChartGuess(rows);
    var used   = [];
    var rField = C.padChartPick('y',     guess[1], used);
    var cField = C.padChartPick('x',     guess[1], used);
    var vField = C.padChartPick('value', guess[0], used);

    var title = C.str(C.padTagParm('title', C.ucfirst(vField !== '' ? vField : 'value') + ' by ' + rField + ' and ' + cField));

    // $cells [$r] [$c]: the keys are PHP's - '8' the key 8 - which a Map of the text keeps
    // apart and in order as PHP does.

    var cells = new Map(), rowNames = new Map(), colNames = new Map();

    C.values(rows).forEach(function (row) {

      row = C.padChartRow(row);
      var v = C.get(row, vField);

      if (!C.padChartFinite(v))
        return;

      var r = C.padChartText(row, rField);
      var c = C.padChartText(row, cField);

      rowNames.set(r, true);
      colNames.set(c, true);

      if (!cells.has(r)) cells.set(r, new Map());
      var line = cells.get(r);
      line.set(c, (line.has(c) ? line.get(c) : 0) + C.num(v));

    });

    if (!cells.size)
      return '';

    rowNames = Array.from(rowNames.keys());
    colNames = Array.from(colNames.keys());

    var desc = [];
    var all  = [];

    cells.forEach(function (line, r) {
      line.forEach(function (v, c) {
        desc.push(r + ' · ' + c + ': ' + C.padChartNumber(v));
        all.push(v);
      });
    });

    var open = C.padChartOpen('heatmap', cells, desc, title, width, height);
    var svg  = open[1];

    var min  = C.lo(all);
    var max  = C.hi(all);
    var span = max - min;
    var step = function (v) { return (isFinite(span) && span > 0) ? C.int(C.round((v - min) / span * 6)) : 6; };

    var labelWidth = 0;
    rowNames.forEach(function (name) { labelWidth = Math.max(labelWidth, C.padChartWidth(name) + 8); });

    var colWidth = 0;
    colNames.forEach(function (name) { colWidth = Math.max(colWidth, C.padChartWidth(name) + 6); });

    var left   = C.int(Math.max(24, Math.min(width * 0.3, labelWidth + 4)));
    var top    = 10;
    var right  = 10;
    var bottom = 46;
    var cellW  = Math.max(1, width  - left - right) / colNames.length;
    var cellH  = Math.max(1, height - top  - bottom) / rowNames.length;
    var gridB  = top + cellH * rowNames.length;

    var everyRow = Math.max(1, C.int(Math.ceil(12 / cellH)));
    var everyCol = Math.max(1, C.int(Math.ceil(colWidth / cellW)));

    rowNames.forEach(function (name, i) {
      if (i % everyRow === 0)
        svg += '<text x="' + C.str(left - 6) + '" y="' + C.padChartXY(top + (i + 0.5) * cellH + 4) + '" text-anchor="end">' + C.padChartAttr(C.padChartFit(name, left - 10)) + '</text>';
    });

    colNames.forEach(function (name, j) {
      if (j % everyCol === 0)
        svg += '<text x="' + C.padChartXY(left + (j + 0.5) * cellW) + '" y="' + C.padChartXY(gridB + 15) + '" text-anchor="middle">' + C.padChartAttr(name) + '</text>';
    });

    // The cells there are, row by row in the order of the columns.

    var colAt = new Map();
    colNames.forEach(function (name, j) { colAt.set(name, j); });

    rowNames.forEach(function (r, i) {

      var line = Array.from((cells.get(r) || new Map()).entries());

      line.sort(function (a, b) { return colAt.get(a[0]) - colAt.get(b[0]); });

      line.forEach(function (cell) {

        var c = cell[0], v = cell[1];
        var j = colAt.get(c);
        var x = left + j * cellW;
        var y = top  + i * cellH;

        svg += '<rect class="pc-mark pc-gap pc-h' + step(v) + '" x="' + C.padChartXY(x) + '" y="' + C.padChartXY(y) + '" width="' + C.padChartXY(cellW) + '" height="' + C.padChartXY(cellH) + '">'
             + '<title>' + C.padChartAttr(r + ' · ' + c + ': ' + C.padChartNumber(v)) + '</title></rect>';

        var text = C.padChartNumber(v);

        if (cellH >= 16 && C.padChartWidth(text) + 8 <= cellW)
          svg += '<text' + (step(v) >= 4 ? ' class="pc-in"' : '') + ' x="' + C.padChartXY(x + cellW / 2) + '" y="' + C.padChartXY(y + cellH / 2 + 4) + '" text-anchor="middle" pointer-events="none">' + text + '</text>';

      });

    });

    // The scale: the lowest value, the seven steps, the highest.

    var low = C.padChartNumber(min);
    var x   = left + C.padChartWidth(low) + 6;
    var y   = height - 16;

    svg += '<text x="' + C.str(left) + '" y="' + C.padChartXY(y + 9) + '">' + low + '</text>';

    for (var k = 0; k <= 6; k++)
      svg += '<rect class="pc-h' + k + '" x="' + C.padChartXY(x + k * 16) + '" y="' + C.padChartXY(y) + '" width="16" height="10"/>';

    svg += '<text x="' + C.padChartXY(x + 7 * 16 + 6) + '" y="' + C.padChartXY(y + 9) + '">' + C.padChartNumber(max) + '</text>';

    return svg + '</svg>';

  };

})(typeof window !== 'undefined' ? window.PadChart : globalThis.PadChart);
