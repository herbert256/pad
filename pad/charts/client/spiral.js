// spiral.php in the browser: a long series wound up, a turn per period (core.js says how these
// files are written).
// needs: core

(function (C) {

  'use strict';

  // padMakeSafe ( $input, $len ) of lib/template.php, for the message: control characters and
  // runs of white space one space, cut to $len bytes of whole characters, trimmed.

  function makeSafe(input, len) {
    var text = C.str(input).replace(/[\u0000-\u001F\u007F-\u009F]/g, ' ').replace(/\s+/gu, ' ');
    if (C.strlen(text) > len) {
      var out = '';
      Array.from(text).some(function (ch) {
        if (C.strlen(out + ch) > len) return true;
        out += ch;
        return false;
      });
      text = out;
    }
    return C.trim(text);
  }

  C.padChartSpiral = function (rows, width, height) {

    var guess  = C.padChartGuess(rows);
    var used   = [];
    var value  = C.padChartPick('value', guess[0], used);
    var label  = C.padChartPick('label', guess[1], used);
    var turn   = C.str(C.padTagParm('turn'));
    var period = C.padTagParm('period', 12);

    // (int) of a number past PHP's integers is not that number: 1e30 is no period either.

    if (!C.isNumeric(period) || C.float(period) < 2 || C.float(period) >= 9223372036854775808 || C.float(period) !== C.int(period)) {
      C.padError("the period of a spiral is a whole number of rows to a turn, 2 or more - not '" + makeSafe(period, 20) + "'");
      period = 12;
    }

    period = C.int(period);

    var points = [];
    var index  = 0;

    C.values(rows).forEach(function (row) {

      row = C.padChartRow(row);
      index++;
      var v = C.get(row, value);

      if (!C.padChartFinite(v))
        return;

      var text = C.padChartText(row, label);
      points.push([text !== '' ? text : String(index), C.num(v), C.padChartText(row, turn)]);

    });

    var n = points.length;

    if (!n)
      return '';

    var vals = points.map(function (p) { return p[1]; });
    var min  = C.lo(vals);
    var max  = C.hi(vals);
    var span = max - min;
    var step = function (v) { return (isFinite(span) && span > 0) ? C.int(C.round((v - min) / span * 6)) : 6; };
    var name = function (p) { return p[0] + (p[2] !== '' ? ' ' + p[2] : ''); };
    var desc = points.map(function (p) { return name(p) + ': ' + C.padChartNumber(p[1]); });

    var title = C.str(C.padTagParm('title', C.ucfirst(value) + (label !== '' ? ' by ' + label : '') + ', ' + period + ' to a turn'));

    var open = C.padChartOpen('spiral', [points, period], desc, title, width, height);
    var svg  = open[1];

    // The room: the labels of the first turn around the outside, the scale at the foot.

    var labelW = 0, q;
    if (label !== '')
      for (q = 0; q < Math.min(period, n); q++)
        labelW = Math.max(labelW, C.padChartWidth(points[q][0]));

    var roomX = (label !== '') ? labelW + 14 : 6;
    var roomY = (label !== '') ? 20 : 6;
    var scale = 26;
    var R     = Math.max(10, Math.min(width / 2 - roomX, (height - scale) / 2 - roomY));
    var cx    = width / 2;
    var cy    = roomY + R + 4;

    // The band: w apart from turn to turn, 90% of that wide, from r0 at the start.

    var turns = n / period;
    var w     = 0.78 * R / (turns + 0.5);
    var r0    = 0.22 * R;

    if (r0 < 0.6 * w) {
      w  = R / (turns + 1.1);
      r0 = 0.6 * w;
    }

    var half  = 0.45 * w;
    var angle = function (t) { return 2 * Math.PI * t / period; };
    var rad   = function (t) { return r0 + w * t / period; };
    var at    = function (t, r) { return C.padChartXY(cx + r * Math.sin(angle(t))) + ',' + C.padChartXY(cy - r * Math.cos(angle(t))); };
    var parts = Math.max(2, C.int(Math.ceil(72 / period)));
    var i;

    // A segment per row: its outer edge along the spiral, back along the inner one.

    points.forEach(function (p, i) {

      var outer = [], inner = [];

      for (var s = 0; s <= parts; s++) {
        var t = i + s / parts;
        outer.push(at(t, rad(t) + half));
        inner.push(at(t, rad(t) - half));
      }

      var d = 'M' + outer.join('L') + 'L' + inner.reverse().join('L') + 'Z';

      svg += '<path class="pc-mark pc-seg pc-h' + step(p[1]) + '" d="' + d + '"><title>' + C.padChartAttr(desc[i]) + '</title></path>';

    });

    // The turn names, where each turn begins - when the band and the room allow.

    if (turn !== '' && 2 * half >= 13)
      for (i = 0; i < n; i += period) {
        var text = points[i][2];
        if (text !== '' && C.padChartWidth(text) <= 2 * 2 * Math.PI * rad(i) / period - 4)
          svg += '<text class="pc-in" x="' + C.padChartXY(cx) + '" y="' + C.padChartXY(cy - rad(i) + 4) + '" text-anchor="middle" pointer-events="none">'
               + C.padChartAttr(text) + '</text>';
      }

    // The labels of the first turn, one past the middle of each position, thinned out.

    if (label !== '') {

      var ring  = rad(n) + half + 8;
      var gap   = 2 * Math.PI * ring / period;
      var every = Math.max(1, C.int(Math.ceil((labelW + 6) / Math.max(1, gap))));

      for (q = 0; q < Math.min(period, n); q += every) {
        var a      = angle(q + 0.5);
        var sn     = Math.sin(a);
        var cs     = Math.cos(a);
        var anchor = (sn > 0.3) ? 'start' : (sn < -0.3 ? 'end' : 'middle');
        svg += '<text x="' + C.padChartXY(cx + ring * sn) + '" y="' + C.padChartXY(cy - ring * cs + 4 + (cs < -0.5 ? 4 : 0) - (cs > 0.5 ? 2 : 0)) + '" text-anchor="' + anchor + '">'
             + C.padChartAttr(points[q][0]) + '</text>';
      }

    }

    // The scale: the lowest value, the seven steps, the highest.

    var low  = C.padChartNumber(min);
    var high = C.padChartNumber(max);
    var x    = (width - C.padChartWidth(low) - C.padChartWidth(high) - 7 * 16 - 12) / 2;
    var y    = height - 16;

    svg += '<text x="' + C.padChartXY(x) + '" y="' + C.padChartXY(y + 9) + '">' + C.padChartAttr(low) + '</text>';
    x   += C.padChartWidth(low) + 6;

    for (var k = 0; k <= 6; k++)
      svg += '<rect class="pc-h' + k + '" x="' + C.padChartXY(x + k * 16) + '" y="' + C.padChartXY(y) + '" width="16" height="10"/>';

    svg += '<text x="' + C.padChartXY(x + 7 * 16 + 6) + '" y="' + C.padChartXY(y + 9) + '">' + C.padChartAttr(high) + '</text>';

    return svg + '</svg>';

  };

})(typeof window !== 'undefined' ? window.PadChart : globalThis.PadChart);
