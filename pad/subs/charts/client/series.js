// series.php in the browser: bar and line charts of several series, and horizontal bars
// (core.js says how these files are written).
// needs: core

(function (C) {

  'use strict';

  // $series is a Map, name => values, in the order of value=.

  C.padChartSeries = function (kind, labels, series, title, width, height, stacked) {

    var n = labels.length;

    if (!n) return '';

    if (series.size > 8) {
      C.padError('the chart has ' + series.size + ' series - eight at most, each its own colour');
      series = new Map(Array.from(series.entries()).slice(0, 8));
    }

    var names      = Array.from(series.keys());
    var k          = names.length;
    var horizontal = (kind === 'hbar');
    var line       = (kind === 'line');
    var desc       = [];

    labels.forEach(function (label, i) {
      var parts = [];
      series.forEach(function (values, name) { parts.push((k > 1 ? C.str(name) + ' ' : '') + C.padChartNumber(values[i])); });
      desc.push(label + ': ' + parts.join(', '));
    });

    var open = C.padChartOpen(kind, [labels, series, stacked], desc, title, width, height);
    var svg  = open[1];

    var min = 0, max = 0, i, j;

    for (i = 0; i < n; i++) {
      var up = 0, down = 0;
      series.forEach(function (values) {
        if (stacked) {
          if (values[i] >= 0) up += values[i];
          else down += values[i];
        } else {
          up   = Math.max(up, values[i]);
          down = Math.min(down, values[i]);
        }
      });
      max = Math.max(max, up);
      min = Math.min(min, down);
    }

    if (!isFinite(max - min)) return '';

    var ticks = C.padChartTicks(min, max, (horizontal ? width : height) < 200 ? 3 : 5);
    var low   = ticks[0];
    var high  = ticks[ticks.length - 1];

    var tickWidth = 0;
    ticks.forEach(function (tick) { tickWidth = Math.max(tickWidth, C.padChartWidth(C.padChartNumber(tick))); });

    var labelWidth = 0;
    labels.forEach(function (label) { labelWidth = Math.max(labelWidth, C.padChartWidth(label) + 8); });

    var right = 10, left;

    if (line && k > 1 && k <= 4) {
      var nameWidth = 0;
      names.forEach(function (name) { nameWidth = Math.max(nameWidth, C.padChartWidth(name)); });
      right = C.int(Math.min(width * 0.3, nameWidth + 18));
    }

    if (horizontal) {
      left  = C.int(Math.max(24, Math.min(width * 0.4, labelWidth + 4)));
      right = C.int(Math.max(right, Math.ceil(C.padChartWidth(C.padChartNumber(high)) / 2) + 4));
    } else
      left = Math.max(24, C.int(Math.ceil(tickWidth)) + 10);

    var top = 10;

    if (k > 1) {
      var legend = C.padChartLegend(names, left, 6, width - left - right);
      svg += legend[0];
      top += legend[1];
    }

    var bottom = 24;
    var plotW  = Math.max(1, width  - left - right);
    var plotH  = Math.max(1, height - top  - bottom);
    var band   = (horizontal ? plotH : plotW) / n;
    var at, mid;

    if (horizontal) {
      at  = function (v) { return left + (v - low) / (high - low) * plotW; };
      mid = function (i) { return top + (i + 0.5) * band; };
    } else {
      at  = function (v) { return top + (high - v) / (high - low) * plotH; };
      mid = function (i) { return left + (i + 0.5) * band; };
    }

    ticks.forEach(function (tick) {
      var p = C.padChartXY(at(tick));
      if (horizontal)
        svg += '<line class="pc-grid" x1="' + p + '" x2="' + p + '" y1="' + C.str(top) + '" y2="' + C.str(top + plotH) + '"/>'
             + '<text x="' + p + '" y="' + C.str(height - 8) + '" text-anchor="middle">' + C.padChartNumber(tick) + '</text>';
      else
        svg += '<line class="pc-grid" x1="' + C.str(left) + '" x2="' + C.str(width - right) + '" y1="' + p + '" y2="' + p + '"/>'
             + '<text x="' + C.str(left - 6) + '" y="' + C.padChartXY(at(tick) + 4) + '" text-anchor="end">' + C.padChartNumber(tick) + '</text>';
    });

    var every = horizontal ? Math.max(1, C.int(Math.ceil(13 / Math.max(1, band))))
                           : Math.max(1, C.int(Math.ceil(Math.min(labelWidth, 73) / Math.max(1, band))));

    labels.forEach(function (label, i) {
      if (i % every === 0) {
        if (horizontal)
          svg += '<text x="' + C.str(left - 6) + '" y="' + C.padChartXY(mid(i) + 4) + '" text-anchor="end">' + C.padChartAttr(C.padChartFit(label, left - 10)) + '</text>';
        else
          svg += '<text x="' + C.padChartXY(mid(i)) + '" y="' + C.str(height - 8) + '" text-anchor="middle">' + C.padChartAttr(C.padChartFit(label, band * every - 8)) + '</text>';
      }
    });

    var base = at(0);
    var tip  = function (i, name) { return C.padChartAttr(labels[i] + (k > 1 ? ' · ' + C.str(name) : '') + ': ' + C.padChartNumber(series.get(name)[i])); };

    if (line)
      svg += C.padChartSeriesLines(series, labels, at, mid, band, tip, top, height - bottom, width);

    else if (stacked) {

      var barW = Math.max(1, Math.min(24, band * 0.7));

      for (i = 0; i < n; i++) {
        var upS = 0, downS = 0;
        for (j = 0; j < names.length; j++) {
          var name = names[j];
          var v = series.get(name)[i], from, to;
          if (v >= 0) { from = upS;   upS   += v; to = upS;   }
          else        { from = downS; downS += v; to = downS; }
          var a = at(from), b = at(to), c = mid(i) - barW / 2, box;
          if (horizontal)
            box = 'x="' + C.padChartXY(Math.min(a, b)) + '" y="' + C.padChartXY(c) + '" width="' + C.padChartXY(Math.abs(b - a)) + '" height="' + C.padChartXY(barW) + '"';
          else
            box = 'x="' + C.padChartXY(c) + '" y="' + C.padChartXY(Math.min(a, b)) + '" width="' + C.padChartXY(barW) + '" height="' + C.padChartXY(Math.abs(b - a)) + '"';
          svg += '<rect class="pc-mark pc-seg ' + C.padChartSlot(j) + '" ' + box + '><title>' + tip(i, name) + '</title></rect>';
        }
      }

    } else {

      var slot  = (k === 1) ? Math.max(1, Math.min(24, band * 0.7)) : Math.min(band * 0.8 / k, 26);
      var barW2 = (k === 1) ? slot : Math.max(1, slot - 2);

      for (i = 0; i < n; i++)
        for (j = 0; j < names.length; j++) {
          var c2 = mid(i) + (j - (k - 1) / 2) * slot;
          var d  = C.padChartBarPath(c2 - barW2 / 2, c2 + barW2 / 2, base, at(series.get(names[j])[i]), !horizontal);
          svg += '<path class="pc-mark ' + C.padChartSlot(j) + '" d="' + d + '"><title>' + tip(i, names[j]) + '</title></path>';
        }

    }

    var bb = C.padChartXY(base);

    if (horizontal)
      svg += '<line class="pc-axis" x1="' + bb + '" x2="' + bb + '" y1="' + C.str(top) + '" y2="' + C.str(top + plotH) + '"/>';
    else
      svg += '<line class="pc-axis" x1="' + C.str(left) + '" x2="' + C.str(width - right) + '" y1="' + bb + '" y2="' + bb + '"/>';

    return svg + '</svg>';

  };

  C.padChartSeriesLines = function (series, labels, at, mid, band, tip, top, bottom, edge) {

    var svg = '', hits = '';
    var n   = labels.length;
    var hit = C.padChartXY(Math.max(8, Math.min(20, band / 2)));
    var ends = new Map();

    Array.from(series.keys()).forEach(function (name, j) {
      var line = '';
      series.get(name).forEach(function (v, i) {
        line += (i ? 'L' : 'M') + C.padChartXY(mid(i)) + ',' + C.padChartXY(at(v));
        hits += '<circle class="pc-hit" cx="' + C.padChartXY(mid(i)) + '" cy="' + C.padChartXY(at(v)) + '" r="' + hit + '"><title>' + tip(i, name) + '</title></circle>';
      });
      var last = at(series.get(name)[n - 1]);
      svg += '<path class="pc-l' + (j + 1) + '" d="' + line + '"/>'
           + '<circle class="pc-c' + (j + 1) + ' pc-ring" cx="' + C.padChartXY(mid(n - 1)) + '" cy="' + C.padChartXY(last) + '" r="4"/>';
      ends.set(name, last);
    });

    if (series.size > 1 && series.size <= 4) {

      // asort: by the end, equal ends in their order.

      var sorted = Array.from(ends.entries()).sort(function (a, b) { return a[1] - b[1]; });
      var y = new Map();
      var previous = -Infinity;

      sorted.forEach(function (e) { previous = Math.max(e[1], previous + 12); y.set(e[0], previous); });

      var over = C.end(y) - (bottom - 2);

      if (over > 0)
        y.forEach(function (value, name) { y.set(name, Math.max(top + 4, value - over)); });

      y.forEach(function (value, name) {
        svg += '<text x="' + C.padChartXY(mid(n - 1) + 8) + '" y="' + C.padChartXY(value + 4) + '">'
             + C.padChartAttr(C.padChartFit(name, edge - mid(n - 1) - 12)) + '</text>';
      });

    }

    return svg + hits;

  };

})(typeof window !== 'undefined' ? window.PadChart : globalThis.PadChart);
