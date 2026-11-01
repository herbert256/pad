// rings.php in the browser: concentric progress rings against their goals (core.js says how
// these files are written).
// needs: core

(function (C) {

  'use strict';

  C.padChartRings = function (rows, width, height) {

    var guess   = C.padChartGuess(rows);
    var numbers = guess[0], names = guess[1];
    var used    = [];
    var field   = C.padChartPick('value', numbers, used);
    var label   = C.padChartPick('label', names, used);
    var to      = C.str(C.padTagParm('to', 100));
    var rings   = [];
    var desc    = [];
    var index   = 0;

    if (C.isNumeric(to) && (!C.padChartFinite(to) || C.float(to) <= 0)) {
      C.padError('the goal of a rings chart is a number above 0');
      return '';
    }

    C.values(rows).forEach(function (row) {

      row = C.padChartRow(row);
      index++;
      var v   = C.get(row, field);
      var end = C.isNumeric(to) ? to : C.get(row, to);

      if (!C.padChartFinite(v) || C.float(v) < 0 || !C.padChartFinite(end) || C.float(end) <= 0)
        return;

      var name = C.padChartText(row, label);
      name = (name !== '') ? name : 'Row ' + index;
      v    = C.num(v);
      end  = C.num(end);
      var part = v / end;

      rings.push([name, v, end, part]);
      desc.push(name + ': ' + C.padChartNumber(v) + ' of ' + C.padChartNumber(end) + ', ' + C.padChartNumber(C.round(part * 100)) + '%');

    });

    if (!rings.length)
      return '';

    if (rings.length > 8) {
      C.padError('the rings chart has ' + rings.length + ' rows - eight at most, each its own colour');
      rings = rings.slice(0, 8);
      desc  = desc.slice(0, 8);
    }

    var title = C.str(C.padTagParm('title', C.ucfirst(field !== '' ? field : 'value') + (C.isNumeric(to) ? '' : ' of ' + to)));

    var open = C.padChartOpen('rings', rings, desc, title, width, height);
    var svg  = open[1];

    var legendW = 0;
    rings.forEach(function (ring) {
      legendW = Math.max(legendW, C.padChartWidth(ring[0]), C.padChartWidth(C.padChartNumber(ring[1]) + ' of ' + C.padChartNumber(ring[2]) + ' · 100%'));
    });

    var n     = rings.length;
    var r     = Math.max(16, Math.min((height - 20) / 2, (width - Math.min(legendW, width / 2) - 64) / 2));
    var thick = Math.max(3, Math.min(30, r * 0.72 / (n * 1.2)));
    var gap   = thick * 0.2;
    var cx    = 12 + r;
    var cy    = height / 2;

    rings.forEach(function (ring, i) {

      var part   = ring[3];
      var radius = r - thick / 2 - i * (thick + gap);
      var line   = 'pc-l' + (i + 1);
      var stroke = 'stroke-width:' + C.padChartXY(thick);
      var tip    = '<title>' + C.padChartAttr(desc[i]) + '</title>';

      svg += '<circle class="' + line + '" stroke-opacity="0.18" style="' + stroke + '" cx="' + C.padChartXY(cx) + '" cy="' + C.padChartXY(cy) + '" r="' + C.padChartXY(radius) + '">' + tip + '</circle>';

      if (part >= 1)
        svg += '<circle class="pc-mark ' + line + '" style="' + stroke + '" cx="' + C.padChartXY(cx) + '" cy="' + C.padChartXY(cy) + '" r="' + C.padChartXY(radius) + '">' + tip + '</circle>';
      else if (part > 0) {
        var a = 2 * Math.PI * part;
        svg += '<path class="pc-mark ' + line + '" style="' + stroke + '" d="M' + C.padChartXY(cx) + ',' + C.padChartXY(cy - radius)
             + 'A' + C.padChartXY(radius) + ',' + C.padChartXY(radius) + ' 0 ' + (part > 0.5 ? 1 : 0) + ' 1 '
             + C.padChartXY(cx + radius * Math.sin(a)) + ',' + C.padChartXY(cy - radius * Math.cos(a)) + '">' + tip + '</path>';
      }

    });

    var lx    = cx + r + 28;
    var lineH = Math.min(36, (height - 12) / n);
    var two   = (lineH >= 30);
    var ly    = cy - n * lineH / 2;
    var room  = width - lx - 8;

    rings.forEach(function (ring, i) {

      var name = ring[0], v = ring[1], end = ring[2], part = ring[3];
      var y     = ly + i * lineH + (two ? 0 : lineH / 2 - 10);
      var value = (end === 100) ? C.padChartNumber(v) + '%' : C.padChartNumber(v) + ' of ' + C.padChartNumber(end) + ' · ' + C.padChartNumber(C.round(part * 100)) + '%';
      svg += '<circle class="' + C.padChartSlot(i) + '" cx="' + C.padChartXY(lx + 5) + '" cy="' + C.padChartXY(y + 10) + '" r="5"/>';

      if (two)
        svg += '<text x="' + C.padChartXY(lx + 16) + '" y="' + C.padChartXY(y + 14) + '" style="font-weight:600">' + C.padChartAttr(C.padChartFit(name, room - 16)) + '</text>'
             + '<text x="' + C.padChartXY(lx + 16) + '" y="' + C.padChartXY(y + 28) + '">' + C.padChartAttr(C.padChartFit(value, room - 16)) + '</text>';
      else
        svg += '<text x="' + C.padChartXY(lx + 16) + '" y="' + C.padChartXY(y + 14) + '">' + C.padChartAttr(C.padChartFit(name + ': ' + value, room - 16)) + '</text>';

    });

    return svg + '</svg>';

  };

})(typeof window !== 'undefined' ? window.PadChart : globalThis.PadChart);
