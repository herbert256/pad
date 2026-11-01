// radial.php in the browser: the rose, and bars bent into rings (core.js says how these files
// are written).
// needs: core

(function (C) {

  'use strict';

  C.padChartRadial = function (kind, rows, width, height) {

    var guess   = C.padChartGuess(rows);
    var numbers = guess[0], names = guess[1];

    var fields = C.padChartFields(C.padTagParm('value'));
    if (!fields.length) {
      var label0 = C.str(C.padTagParm('label'));
      fields = numbers.filter(function (f) { return f !== label0; }).slice(0, 1);
    }

    var used  = fields.slice();
    var label = C.padChartPick('label', names, used);

    if (!fields.length) return '';

    if (kind === 'radialbar' && fields.length > 1) {
      C.padError('a radialbar chart draws one value field, it has ' + fields.length);
      fields = fields.slice(0, 1);
    }

    if (fields.length > 8) {
      C.padError('the rose has ' + fields.length + ' value fields - eight at most, each its own colour');
      fields = fields.slice(0, 8);
    }

    var items = [];
    var gaps  = [];
    var read  = 0;
    var index = 0;

    C.values(rows).forEach(function (row) {

      row = C.padChartRow(row);
      index++;
      var values = [];
      var gap    = {};

      for (var i = 0; i < fields.length; i++) {
        var v = C.get(row, fields[i]);
        if (!C.padChartFinite(v) || C.float(v) < 0) {
          if (kind !== 'rose') return;
          v = 0;
          gap[i] = true;
        } else
          read++;
        values.push(C.num(v));
      }

      gaps[items.length] = gap;

      var name = C.padChartText(row, label);
      items.push([name !== '' ? name : String(index), values]);

    });

    if (!items.length || !read) return '';

    var title = C.str(C.padTagParm('title', fields.map(C.ucfirst).join(' and ') + (label !== '' ? ' by ' + label : '')));

    if (kind === 'rose')
      return C.padChartRose(items, fields, title, width, height, gaps);

    return C.padChartRadialBar(items, title, width, height);

  };

  C.padChartRose = function (items, fields, title, width, height, gaps) {

    if (gaps === undefined) gaps = [];

    var k     = fields.length;
    var n     = items.length;
    var total = C.hi(items.map(function (item) { return item[1].reduce(function (s, v) { return s + v; }, 0); }));

    if (!isFinite(total)) return '';

    var desc = items.map(function (item, at) {
      var parts = fields.map(function (field, i) {
        return (k > 1 ? field + ' ' : '') + ((gaps[at] && gaps[at][i]) ? 'no value' : C.padChartNumber(item[1][i]));
      });
      return item[0] + ': ' + parts.join(', ');
    });

    var open = C.padChartOpen('rose', [items, fields], desc, title, width, height);
    var svg  = open[1];

    var legendH = 0;

    if (k > 1) {
      var legend = C.padChartLegend(fields.map(C.ucfirst), 10, 6, width - 20);
      svg += legend[0];
      legendH = legend[1];
    }

    var labelW = 0;
    items.forEach(function (item) { labelW = Math.max(labelW, C.padChartWidth(item[0])); });

    labelW     = Math.min(labelW, width * 0.2);
    var r      = Math.max(20, Math.min(width / 2 - labelW - 14, (height - legendH - 36) / 2));
    var cx     = width / 2;
    var cy     = legendH + 18 + (height - legendH - 36) / 2;
    var ticks  = C.padChartTicks(0, total > 0 ? total : 1, r < 100 ? 2 : 4);
    var scale  = ticks[ticks.length - 1];
    var radius = function (v) { return r * Math.sqrt(Math.max(0, v) / scale); };
    var step   = 2 * Math.PI / n;
    var i;

    ticks.forEach(function (tick) {
      if (tick > 0)
        svg += '<circle class="pc-grid" fill="none" cx="' + C.padChartXY(cx) + '" cy="' + C.padChartXY(cy) + '" r="' + C.padChartXY(radius(tick)) + '"/>';
    });

    for (i = 0; i < n; i++)
      svg += '<line class="pc-grid" x1="' + C.padChartXY(cx) + '" y1="' + C.padChartXY(cy) + '" x2="' + C.padChartXY(cx + r * Math.sin((i + 0.5) * step)) + '" y2="' + C.padChartXY(cy - r * Math.cos((i + 0.5) * step)) + '"/>';

    items.forEach(function (item, i) {

      var name = item[0];
      var a0   = (i - 0.5) * step;
      var a1   = a0 + step;
      var sum  = 0;

      item[1].forEach(function (v, j) {

        var inner = radius(sum);
        sum += v;
        var outer = radius(sum);

        if (v <= 0) return;

        svg += '<path class="pc-mark pc-gap ' + (k > 1 ? C.padChartSlot(j) : 'pc-c1') + '" d="' + C.padChartArc(cx, cy, inner, outer, a0, a1) + '"><title>'
             + C.padChartAttr(name + (k > 1 ? ' · ' + fields[j] : '') + ': ' + C.padChartNumber(v)) + '</title></path>';

      });

    });

    var s = Math.sin(-step / 2);
    var c = Math.cos(-step / 2);

    ticks.forEach(function (tick) {
      if (tick > 0 && tick < scale)
        svg += '<text class="pc-ring" paint-order="stroke" x="' + C.padChartXY(cx + radius(tick) * s + 3) + '" y="' + C.padChartXY(cy - radius(tick) * c + 4) + '">' + C.padChartNumber(tick) + '</text>';
    });

    items.forEach(function (item, i) {
      var a      = i * step;
      var s      = Math.sin(a);
      var c      = Math.cos(a);
      var anchor = (s > 0.2) ? 'start' : (s < -0.2 ? 'end' : 'middle');
      svg += '<text x="' + C.padChartXY(cx + (r + 6) * s) + '" y="' + C.padChartXY(cy - (r + 6) * c + 4 + (c < -0.5 ? 6 : 0)) + '" text-anchor="' + anchor + '">'
           + C.padChartAttr(C.padChartFit(item[0], labelW + 6)) + '</text>';
    });

    return svg + '</svg>';

  };

  C.padChartRadialBar = function (items, title, width, height) {

    var high  = C.hi(items.map(function (item) { return item[1][0]; }));
    var top   = C.padTagParm('to');
    var given = (C.padChartFinite(top) && C.float(top) > 0);

    var ticks = C.padChartTicks(0, given ? C.num(top) : (high > 0 ? high : 1), 5);
    var scale = ticks[ticks.length - 1];

    if (given) {
      scale = C.num(top);
      ticks = ticks.filter(function (tick) { return tick < scale - scale * 1e-9; });
      ticks.push(scale);
    }

    var n    = items.length;
    var full = 1.5 * Math.PI;
    var desc = items.map(function (item) { return item[0] + ': ' + C.padChartNumber(item[1][0]); });

    desc.push('on a scale of 0 to ' + C.padChartNumber(scale));

    var open = C.padChartOpen('radialbar', [items, scale], desc, title, width, height);
    var svg  = open[1];

    var tickW = 0;
    ticks.forEach(function (tick) { tickW = Math.max(tickW, C.padChartWidth(C.padChartNumber(tick))); });

    var r     = Math.max(20, Math.min(height / 2 - 18, width / 2 - Math.max(30, tickW + 12)));
    var cx    = width / 2;
    var cy    = height / 2 + 2;
    var hole  = r * 0.22;
    var band  = (r - hole) / n;
    var thick = Math.min(28, band * 0.72);
    var at    = function (v) { return Math.min(v, scale) / scale * full; };

    ticks.forEach(function (tick) {
      var a = at(tick);
      var s = Math.sin(a);
      var c = Math.cos(a);
      svg += '<line class="pc-grid" x1="' + C.padChartXY(cx + hole * s) + '" y1="' + C.padChartXY(cy - hole * c) + '" x2="' + C.padChartXY(cx + (r + 3) * s) + '" y2="' + C.padChartXY(cy - (r + 3) * c) + '"/>';
      var anchor = (s > 0.3) ? 'start' : (s < -0.3 ? 'end' : 'middle');
      svg += '<text x="' + C.padChartXY(cx + (r + 7) * s) + '" y="' + C.padChartXY(cy - (r + 7) * c + 4 + (c < -0.5 ? 5 : 0) - (c > 0.5 ? 1 : 0)) + '" text-anchor="' + anchor + '">'
           + C.padChartNumber(tick) + '</text>';
    });

    items.forEach(function (item, i) {

      var name  = item[0];
      var v     = item[1][0];
      var mid   = r - (i + 0.5) * band;
      var inner = mid - thick / 2;
      var outer = mid + thick / 2;
      var slot  = C.padChartSlot(i);
      var sweep = at(v);
      var tip   = C.padChartAttr(name + ': ' + C.padChartNumber(v) + (v > scale ? ' (past the scale)' : ''));

      svg += '<path class="' + slot + '" fill-opacity="0.15" d="' + C.padChartArc(cx, cy, inner, outer, 0, full) + '"/>';

      if (sweep > 0)
        svg += '<path class="pc-mark ' + slot + '" d="' + C.padChartArc(cx, cy, inner, outer, 0, Math.max(sweep, 0.004)) + '"><title>' + tip + '</title></path>';

      svg += '<text x="' + C.padChartXY(cx - 6) + '" y="' + C.padChartXY(cy - mid + 4) + '" text-anchor="end">'
           + C.padChartAttr(C.padChartFit(name, cx - 10)) + '</text>';

      var text  = C.padChartNumber(v);
      var textW = C.padChartWidth(text);
      var past  = sweep + (textW + 8) / mid;
      var a;

      if (past < full - 0.05) {
        a = sweep + (textW / 2 + 5) / mid;
        svg += '<text x="' + C.padChartXY(cx + mid * Math.sin(a)) + '" y="' + C.padChartXY(cy - mid * Math.cos(a) + 4) + '" text-anchor="middle">' + C.padChartAttr(text) + '</text>';
      } else if (thick >= 12) {
        a = sweep - (textW / 2 + 5) / mid;
        svg += '<text class="pc-in" x="' + C.padChartXY(cx + mid * Math.sin(a)) + '" y="' + C.padChartXY(cy - mid * Math.cos(a) + 4) + '" text-anchor="middle">' + C.padChartAttr(text) + '</text>';
      }

    });

    return svg + '</svg>';

  };

})(typeof window !== 'undefined' ? window.PadChart : globalThis.PadChart);
