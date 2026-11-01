// marimekko.php in the browser: the mosaic chart, columns as wide as their totals and parted
// into shares (core.js says how these files are written).
// needs: core

(function (C) {

  'use strict';

  C.padChartMarimekko = function (rows, width, height) {

    var guess   = C.padChartGuess(rows);
    var numbers = guess[0], names = guess[1];

    var fields = C.padChartFields(C.padTagParm('value'));
    if (!fields.length) {
      var label0 = C.str(C.padTagParm('label'));
      fields = numbers.filter(function (f) { return f !== label0; });
    }
    var used  = fields.slice();
    var label = C.padChartPick('label', names, used);

    if (fields.length < 2) {
      C.padError('a marimekko chart needs two value fields or more, it has ' + fields.length);
      return '';
    }

    if (fields.length > 8) {
      C.padError('the marimekko chart has ' + fields.length + ' value fields - eight at most, each its own colour');
      fields = fields.slice(0, 8);
    }

    var columns = [];
    var index   = 0;

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

      if (C.sum(values) <= 0)
        return;

      var name = C.padChartText(row, label);
      columns.push([name !== '' ? name : String(index), values, C.sum(values)]);

    });

    var grand = C.sum(columns.map(function (col) { return col[2]; }));

    if (!columns.length || !isFinite(grand))
      return '';

    var pct  = function (v, of) { return C.padChartNumber(C.round(v / of * 100, 1)) + '%'; };
    var desc = [];

    columns.forEach(function (col) {
      var parts = fields.map(function (field, i) { return field + ' ' + C.padChartNumber(col[1][i]) + ' (' + pct(col[1][i], col[2]) + ')'; });
      desc.push(col[0] + ', ' + C.padChartNumber(col[2]) + ' (' + pct(col[2], grand) + ' of all): ' + parts.join(', '));
    });

    var title = C.str(C.padTagParm('title', fields.map(C.ucfirst).join(', ') + (label !== '' ? ' by ' + label : '')));

    var open = C.padChartOpen('marimekko', [columns, fields], desc, title, width, height);
    var svg  = open[1];

    var legend = C.padChartLegend(fields.map(C.ucfirst), 10, 6, width - 20);
    svg += legend[0];

    var left   = 10 + C.padChartWidth('100%') + 8;
    var right  = width - 10;
    var top    = legend[1] + 14;
    var bottom = height - 36;
    var plotW  = Math.max(1, right - left);
    var plotH  = Math.max(1, bottom - top);

    [0, 25, 50, 75, 100].forEach(function (tick) {
      var y = bottom - tick / 100 * plotH;
      svg += '<line class="pc-axis" x1="' + C.padChartXY(left - 4) + '" x2="' + C.padChartXY(left) + '" y1="' + C.padChartXY(y) + '" y2="' + C.padChartXY(y) + '"/>'
           + '<text x="' + C.padChartXY(left - 7) + '" y="' + C.padChartXY(y + 4) + '" text-anchor="end">' + tick + '%</text>';
    });

    var x = left;

    columns.forEach(function (col) {

      var name = col[0], values = col[1], total = col[2];
      var w = total / grand * plotW;
      var y = bottom;

      values.forEach(function (v, i) {

        var h = v / total * plotH;
        y -= h;

        if (v <= 0)
          return;

        var text = pct(v, total);

        svg += '<rect class="pc-mark pc-gap ' + C.padChartSlot(i) + '" x="' + C.padChartXY(x) + '" y="' + C.padChartXY(y) + '" width="' + C.padChartXY(w) + '" height="' + C.padChartXY(h) + '">'
             + '<title>' + C.padChartAttr(name + ' · ' + fields[i] + ': ' + C.padChartNumber(v) + ' (' + text + ' of ' + name + ')') + '</title></rect>';

        if (C.padChartWidth(text) + 8 <= w && h >= 16)
          svg += '<text class="pc-in" x="' + C.padChartXY(x + w / 2) + '" y="' + C.padChartXY(y + h / 2 + 4) + '" text-anchor="middle">' + text + '</text>';

      });

      svg += '<text x="' + C.padChartXY(x + w / 2) + '" y="' + C.padChartXY(bottom + 15) + '" text-anchor="middle">' + C.padChartAttr(C.padChartFit(name, w - 4)) + '</text>'
           + '<text x="' + C.padChartXY(x + w / 2) + '" y="' + C.padChartXY(bottom + 29) + '" text-anchor="middle" opacity="0.75">'
           + C.padChartAttr(C.padChartFit(C.padChartNumber(total) + ' · ' + pct(total, grand), w - 4)) + '</text>';

      x += w;

    });

    return svg + '</svg>';

  };

})(typeof window !== 'undefined' ? window.PadChart : globalThis.PadChart);
