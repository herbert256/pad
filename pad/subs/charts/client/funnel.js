// funnel.php in the browser: the funnel, and the pyramid - a triangle in layers, or the
// population pyramid of two fields back to back (core.js says how these files are written).
// needs: core

(function (C) {

  'use strict';

  C.padChartFunnel = function (kind, rows, width, height) {

    var guess   = C.padChartGuess(rows);
    var numbers = guess[0], names = guess[1];

    var fields = C.padChartFields(C.padTagParm('value'));
    if (!fields.length) {
      var label0 = C.str(C.padTagParm('label'));
      fields = numbers.filter(function (f) { return f !== label0; }).slice(0, 1);
    }

    var used  = fields.slice();
    var label = C.padChartPick('label', names, used);
    var most  = (kind === 'pyramid') ? 2 : 1;

    if (!fields.length) return '';

    if (fields.length > most) {
      C.padError('a ' + kind + ' chart draws ' + (most === 1 ? 'one value field' : 'one or two value fields') + ', it has ' + fields.length);
      fields = fields.slice(0, most);
    }

    var back  = (fields.length === 2);
    var items = [];
    var index = 0;

    C.values(rows).forEach(function (row) {

      row = C.padChartRow(row);
      index++;
      var values = [];

      for (var f = 0; f < fields.length; f++) {
        var v = C.get(row, fields[f]);
        if (!C.padChartFinite(v) || (C.float(v) < 0 && !back)) return;
        values.push(Math.abs(C.num(v)));
      }

      var name = C.padChartText(row, label);
      items.push([name !== '' ? name : String(index), values]);

    });

    if (!items.length) return '';

    var title = C.str(C.padTagParm('title', fields.map(C.ucfirst).join(' and ') + (label !== '' ? ' by ' + label : '')));

    if (back)
      return C.padChartPopulation(items, fields, title, width, height);

    if (kind === 'pyramid')
      return C.padChartTriangle(items, title, width, height);

    return C.padChartFunnelBars(items, title, width, height);

  };

  C.padChartFunnelBars = function (items, title, width, height) {

    var first = items[0][1][0];
    var high  = C.hi(items.map(function (item) { return item[1][0]; }));
    var share = function (v, of) { return of > 0 ? C.padChartNumber(C.round(v / of * 100, 1)) + '%' : ''; };
    var desc  = [];

    if (!isFinite(high) || high <= 0) return '';

    items.forEach(function (item, i) {
      var name = item[0], values = item[1], parts = [], one;
      if (i && (one = share(values[0], first)) !== '')
        parts.push(one + ' of the first');
      if (i && (one = share(values[0], items[i - 1][1][0])) !== '')
        parts.push(one + ' of the step before');
      desc.push(name + ': ' + C.padChartNumber(values[0]) + (parts.length ? ' (' + parts.join(', ') + ')' : ''));
    });

    var open = C.padChartOpen('funnel', items, desc, title, width, height);
    var svg  = open[1];

    var labelW = 0;
    items.forEach(function (item) { labelW = Math.max(labelW, C.padChartWidth(item[0])); });

    var n     = items.length;
    labelW    = Math.min(labelW, width * 0.25);
    var left  = 10 + labelW + 12;
    var right = width - 10 - C.padChartWidth('100%') - 12;
    var plotW = Math.max(10, right - left);
    var cx    = left + plotW / 2;
    var band  = (height - 8) / n;
    var barH  = Math.min(48, band * 0.62);
    var gap   = band - barH;
    var w     = function (v) { return Math.max(2, v / high * plotW); };
    var top   = function (i) { return 4 + i * band + (band - barH) / 2; };

    items.forEach(function (item, i) {

      var name = item[0];
      var v    = item[1][0];
      var y    = top(i);
      var bw   = w(v);
      var step = '';

      if (i < n - 1) {

        var next = items[i + 1][1][0];
        var nw   = w(next);
        var y1   = y + barH;
        var y2   = top(i + 1);
        step = share(next, v);

        svg += '<path class="pc-c1" fill-opacity="0.18" d="M' + C.padChartXY(cx - bw / 2) + ',' + C.padChartXY(y1) + 'L' + C.padChartXY(cx + bw / 2) + ',' + C.padChartXY(y1)
             + 'L' + C.padChartXY(cx + nw / 2) + ',' + C.padChartXY(y2) + 'L' + C.padChartXY(cx - nw / 2) + ',' + C.padChartXY(y2) + 'Z"><title>'
             + C.padChartAttr(items[i + 1][0] + ': ' + step + ' of ' + name) + '</title></path>';

        if (gap >= 13 && step !== '')
          svg += '<text x="' + C.padChartXY(cx) + '" y="' + C.padChartXY((y1 + y2) / 2 + 4) + '" text-anchor="middle">' + C.padChartAttr('↓ ' + step) + '</text>';

      }

      svg += '<rect class="pc-mark pc-c1" x="' + C.padChartXY(cx - bw / 2) + '" y="' + C.padChartXY(y) + '" width="' + C.padChartXY(bw) + '" height="' + C.padChartXY(barH) + '" rx="3">'
           + '<title>' + C.padChartAttr(desc[i]) + '</title></rect>';

      var mid   = y + barH / 2 + 4;
      var text  = C.padChartNumber(v);
      var textW = C.padChartWidth(text);

      if (textW + 10 <= bw)
        svg += '<text class="pc-in" x="' + C.padChartXY(cx) + '" y="' + C.padChartXY(mid) + '" text-anchor="middle">' + C.padChartAttr(text) + '</text>';
      else
        svg += '<text x="' + C.padChartXY(cx + bw / 2 + 5) + '" y="' + C.padChartXY(mid) + '">' + C.padChartAttr(text) + '</text>';

      svg += '<text x="' + C.padChartXY(left - 12) + '" y="' + C.padChartXY(mid) + '" text-anchor="end">' + C.padChartAttr(C.padChartFit(name, labelW)) + '</text>'
           + '<text x="' + C.padChartXY(width - 10) + '" y="' + C.padChartXY(mid) + '" text-anchor="end">' + C.padChartAttr(share(v, first)) + '</text>';

    });

    return svg + '</svg>';

  };

  C.padChartTriangle = function (items, title, width, height) {

    var total = items.reduce(function (s, item) { return s + item[1][0]; }, 0);

    if (!isFinite(total) || total <= 0) return '';

    var share = function (v) { return C.padChartNumber(C.round(v / total * 100, 1)) + '%'; };
    var desc  = items.map(function (item) { return item[0] + ': ' + C.padChartNumber(item[1][0]) + ' (' + share(item[1][0]) + ')'; });

    var open = C.padChartOpen('pyramid', items, desc, title, width, height);
    var svg  = open[1];

    var top   = 10;
    var tall  = height - 20;
    var base  = Math.max(20, Math.min(width * 0.5, tall * 1.25));
    var apex  = 10 + base / 2;
    var half  = function (y) { return (y - top) / tall * base / 2; };
    var sum   = 0;
    var marks = [];

    items.forEach(function (item, i) {

      var y0 = top + tall * Math.sqrt(sum / total);
      sum += item[1][0];
      var y1 = top + tall * Math.sqrt(Math.min(1, sum / total));

      if (item[1][0] <= 0) return;

      var d = 'M' + C.padChartXY(apex - half(y0)) + ',' + C.padChartXY(y0) + 'L' + C.padChartXY(apex + half(y0)) + ',' + C.padChartXY(y0)
            + 'L' + C.padChartXY(apex + half(y1)) + ',' + C.padChartXY(y1) + 'L' + C.padChartXY(apex - half(y1)) + ',' + C.padChartXY(y1) + 'Z';

      svg += '<path class="pc-mark pc-gap ' + C.padChartSlot(i) + '" d="' + d + '"><title>' + C.padChartAttr(desc[i]) + '</title></path>';

      marks.push([i, (y0 + y1) / 2]);

    });

    var at = marks.map(function (mark) { return mark[1]; });
    var m  = at.length;
    var j;

    for (j = 1; j < m; j++)
      at[j] = Math.max(at[j], at[j - 1] + 16);

    if (m && at[m - 1] > height - 8) {
      at[m - 1] = height - 8;
      for (j = m - 2; j >= 0; j--)
        at[j] = Math.min(at[j], at[j + 1] - 16);
    }

    var column = 10 + base + 24;

    marks.forEach(function (mark, j) {

      var i    = mark[0], y = mark[1];
      var edge = apex + half(y);
      var text = items[i][0] + '  ' + C.padChartNumber(items[i][1][0]) + ' (' + share(items[i][1][0]) + ')';

      svg += '<path class="pc-edge" d="M' + C.padChartXY(edge + 4) + ',' + C.padChartXY(y) + 'L' + C.padChartXY(column - 16) + ',' + C.padChartXY(y)
           + 'L' + C.padChartXY(column - 6) + ',' + C.padChartXY(at[j]) + '"/>'
           + '<rect class="' + C.padChartSlot(i) + '" x="' + C.padChartXY(column - 2) + '" y="' + C.padChartXY(at[j] - 5) + '" width="10" height="10" rx="2"/>'
           + '<text x="' + C.padChartXY(column + 14) + '" y="' + C.padChartXY(at[j] + 4) + '">' + C.padChartAttr(C.padChartFit(text, width - column - 24)) + '</text>';

    });

    return svg + '</svg>';

  };

  C.padChartPopulation = function (items, fields, title, width, height) {

    var high = 0;
    items.forEach(function (item) { high = Math.max(high, item[1][0], item[1][1]); });

    if (!isFinite(high)) return '';

    var desc = items.map(function (item) {
      return item[0] + ': ' + fields[0] + ' ' + C.padChartNumber(item[1][0]) + ', ' + fields[1] + ' ' + C.padChartNumber(item[1][1]);
    });

    var open = C.padChartOpen('pyramid', [items, fields], desc, title, width, height);
    var svg  = open[1];

    var legend = C.padChartLegend(fields.map(C.ucfirst), 10, 6, width - 20);
    svg += legend[0];

    var labelW = 0;
    items.forEach(function (item) { labelW = Math.max(labelW, C.padChartWidth(item[0])); });

    var n      = items.length;
    var middle = Math.min(labelW, width * 0.2) + 16;
    var cx     = width / 2;
    var halfW  = Math.max(10, (width - 20 - middle) / 2 - 10);
    var ticks  = C.padChartTicks(0, high > 0 ? high : 1, halfW < 200 ? 3 : 4);
    var scale  = ticks[ticks.length - 1];
    var top    = legend[1] + 14;
    var bottom = height - 22;
    var band   = (bottom - top) / n;
    var barH   = Math.max(1, Math.min(28, band * 0.8));
    var leftX  = cx - middle / 2;
    var rightX = cx + middle / 2;
    var len    = function (v) { return v / scale * halfW; };

    ticks.forEach(function (tick) {
      [leftX - len(tick), rightX + len(tick)].forEach(function (x) {
        svg += '<line class="pc-grid" x1="' + C.padChartXY(x) + '" x2="' + C.padChartXY(x) + '" y1="' + C.padChartXY(top) + '" y2="' + C.padChartXY(bottom) + '"/>'
             + '<text x="' + C.padChartXY(x) + '" y="' + C.padChartXY(bottom + 15) + '" text-anchor="middle">' + C.padChartNumber(tick) + '</text>';
      });
    });

    items.forEach(function (item, i) {

      var name = item[0], values = item[1];
      var y0 = bottom - (i + 1) * band + (band - barH) / 2;
      var y1 = y0 + barH;

      [0, 1].forEach(function (side) {
        var base = side ? rightX : leftX;
        var end  = side ? rightX + len(values[1]) : leftX - len(values[0]);
        if (values[side] > 0)
          svg += '<path class="pc-mark ' + C.padChartSlot(side) + '" d="' + C.padChartBarPath(y0, y1, base, end, false) + '"><title>'
               + C.padChartAttr(name + ' · ' + fields[side] + ': ' + C.padChartNumber(values[side])) + '</title></path>';
      });

      svg += '<text x="' + C.padChartXY(cx) + '" y="' + C.padChartXY((y0 + y1) / 2 + 4) + '" text-anchor="middle">' + C.padChartAttr(C.padChartFit(name, middle - 6)) + '</text>';

    });

    svg += '<line class="pc-axis" x1="' + C.padChartXY(leftX) + '" x2="' + C.padChartXY(leftX) + '" y1="' + C.padChartXY(top) + '" y2="' + C.padChartXY(bottom) + '"/>'
         + '<line class="pc-axis" x1="' + C.padChartXY(rightX) + '" x2="' + C.padChartXY(rightX) + '" y1="' + C.padChartXY(top) + '" y2="' + C.padChartXY(bottom) + '"/>';

    return svg + '</svg>';

  };

})(typeof window !== 'undefined' ? window.PadChart : globalThis.PadChart);
