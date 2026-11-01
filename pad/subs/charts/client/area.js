// area.php in the browser: amounts over time as filled areas, and the streamgraph (core.js
// says how these files are written).
// needs: core

(function (C) {

  'use strict';

  // PHP's <=> of two numbers: a NAN is neither equal nor less, so it answers 1. And max ()
  // and min () of numbers as PHP takes them: a NAN met along the way is kept by max when it
  // came first and taken by min whatever came before - Math's answer is NAN either way.

  function spaceship(a, b) { return a === b ? 0 : (a < b ? -1 : 1); }

  function phpMax(list) {
    var m = list[0];
    for (var i = 1; i < list.length; i++) if (spaceship(m, list[i]) < 0) m = list[i];
    return m;
  }

  function phpMin(list) {
    var m = list[0];
    for (var i = 1; i < list.length; i++) if (spaceship(m, list[i]) > 0) m = list[i];
    return m;
  }

  C.padChartArea = function (kind, rows, width, height) {

    var stream = (kind === 'stream');

    var guess   = C.padChartGuess(rows);
    var numbers = guess[0], names0 = guess[1];

    var used   = [];
    var label  = C.padChartPick('label', names0, used);
    var fields = C.padChartFields(C.padTagParm('value'));

    if (!fields.length)
      fields = numbers.filter(function (f) { return used.indexOf(f) < 0; });

    if (!fields.length) return '';

    var flag    = function (name) { var v = C.padTagParm(name, false); return !(v === false || v === '' || v === 0 || v === '0'); };
    var percent = stream ? false : flag('percent');
    var stacked = stream ? true  : (flag('stacked') || percent);

    if (fields.length > 8) {
      C.padError('the ' + kind + ' chart has ' + fields.length + ' series - eight at most, each its own colour');
      fields = fields.slice(0, 8);
    }

    var table  = C.padChartTable(rows, label, fields);
    var labels = table[0], series = table[1];

    var n = labels.length;

    if (!n) return '';

    if (stacked)
      series.forEach(function (values, name) {
        values.forEach(function (v, i) {
          if (v < 0) {
            C.padError('a stacked area takes no negative values - ' + C.str(name) + ' has ' + C.padChartNumber(v) + ' at ' + labels[i]);
            values[i] = 0;
          }
        });
      });

    var names  = Array.from(series.keys());
    var k      = names.length;
    var totals = [];
    var i;

    for (i = 0; i < n; i++) totals.push(0);

    series.forEach(function (values) {
      values.forEach(function (v, i) { totals[i] += v; });
    });

    var lower = new Map(), upper = new Map();

    if (!stacked)
      names.forEach(function (name) {
        lower.set(name, totals.map(function () { return 0; }));
        upper.set(name, series.get(name));
      });

    else {

      var order = stream ? C.padChartAreaInsideOut(series) : names;
      var level = stream ? C.padChartAreaWiggle(series, order, n) : totals.map(function () { return 0; });

      order.forEach(function (name) {
        lower.set(name, level.slice());
        series.get(name).forEach(function (v, i) {
          level[i] += (percent ? (totals[i] > 0 ? v / totals[i] * 100 : 0) : v);
        });
        upper.set(name, level.slice());
      });

    }

    var all = [];
    lower.forEach(function (values) { all = all.concat(values); });
    upper.forEach(function (values) { all = all.concat(values); });

    var allMax = phpMax(all), allMin = phpMin(all);

    if (!isFinite(allMax - allMin)) return '';

    var share = function (i, v) { return (totals[i] > 0) ? ' (' + C.padChartNumber(C.round(v / totals[i] * 100, 1)) + '%)' : ''; };
    var desc  = [];

    labels.forEach(function (text, i) {
      var parts = [];
      series.forEach(function (values, name) {
        parts.push((k > 1 ? C.str(name) + ' ' : '') + C.padChartNumber(values[i]) + (percent ? share(i, values[i]) : ''));
      });
      desc.push(text + ': ' + parts.join(', '));
    });

    var title = C.str(C.padTagParm('title', names.map(C.str).join(', ') + (label !== '' ? ' by ' + label : '')));

    var open = C.padChartOpen(kind, [labels, series, stacked, percent], desc, title, width, height);
    var svg  = open[1];

    var ticks;

    if (stream)
      ticks = [];
    else if (percent)
      ticks = [0, 25, 50, 75, 100];
    else
      ticks = C.padChartTicks(Math.min(0, allMin), Math.max(0, allMax), height < 200 ? 3 : 5);

    var low  = ticks.length ? ticks[0] : allMin;
    var high = ticks.length ? ticks[ticks.length - 1] : allMax;

    if (high === low)
      high = low + 1;

    if (stream) {
      var room = (high - low) * 0.04;
      low  -= room;
      high += room;
    }

    var tickW = 0;
    ticks.forEach(function (tick) { tickW = Math.max(tickW, C.padChartWidth(C.padChartNumber(tick) + (percent ? '%' : ''))); });

    var labelW = 0;
    labels.forEach(function (text) { labelW = Math.max(labelW, C.padChartWidth(text) + 8); });

    var inset = stream ? Math.min(labelW / 2, width / 6) : 0;
    var left  = stream ? 10 : Math.max(24, C.int(Math.ceil(tickW)) + 10);
    var right = Math.max(10, C.int(Math.ceil(labelW / 2 - inset)));
    var top   = 10;

    if (k > 1) {
      var legend = C.padChartLegend(names, left, 6, width - left - right);
      svg += legend[0];
      top += legend[1];
    }

    var bottom = 24;
    var plotW  = Math.max(1, width  - left - right);
    var plotH  = Math.max(1, height - top  - bottom);
    var step   = (n > 1) ? (plotW - 2 * inset) / (n - 1) : 0;
    var x      = function (i) { return (n > 1) ? left + inset + i * step : left + plotW / 2; };
    var y      = function (v) { return top + (high - v) / (high - low) * plotH; };

    ticks.forEach(function (tick) {
      svg += '<line class="pc-grid" x1="' + C.str(left) + '" x2="' + C.str(width - right) + '" y1="' + C.padChartXY(y(tick)) + '" y2="' + C.padChartXY(y(tick)) + '"/>'
           + '<text x="' + C.str(left - 6) + '" y="' + C.padChartXY(y(tick) + 4) + '" text-anchor="end">' + C.padChartNumber(tick) + (percent ? '%' : '') + '</text>';
    });

    var every = (n > 1) ? Math.max(1, C.int(Math.ceil(labelW / Math.max(1, step)))) : 1;

    labels.forEach(function (text, i) {
      if (i % every === 0)
        svg += '<text x="' + C.padChartXY(x(i)) + '" y="' + C.str(height - 8) + '" text-anchor="middle">' + C.padChartAttr(text) + '</text>';
    });

    var edge = function (values, back) {
      var xs = [], ys = [];
      values.forEach(function (v, i) { xs.push(x(i)); ys.push(y(v)); });
      return C.padChartAreaEdge(xs, ys, stream, back);
    };

    names.forEach(function (name, j) {

      var cls = C.padChartSlot(j);
      var sum = C.padChartNumber(C.sum(series.get(name)));
      var d;

      if (n === 1) {
        var x0 = x(0) - plotW / 4;
        d = 'M' + C.padChartXY(x0) + ',' + C.padChartXY(y(upper.get(name)[0])) + 'H' + C.padChartXY(x0 + plotW / 2)
          + 'V' + C.padChartXY(y(lower.get(name)[0])) + 'H' + C.padChartXY(x0) + 'Z';
      } else
        d = edge(upper.get(name), false) + 'L' + edge(lower.get(name), true).slice(1) + 'Z';

      if (stacked)
        svg += '<path class="' + cls + ' pc-seg" fill-opacity="' + (stream ? '0.9' : '0.85') + '" d="' + d + '"><title>' + C.padChartAttr(C.str(name) + ': ' + sum + ' in all') + '</title></path>';
      else
        svg += '<path class="' + cls + '" fill-opacity="' + (k > 1 ? '0.18' : '0.25') + '" d="' + d + '"><title>' + C.padChartAttr(C.str(name) + ': ' + sum + ' in all') + '</title></path>'
             + '<path class="pc-l' + (j + 1) + '" d="' + (n > 1 ? edge(upper.get(name), false) : '') + '"/>';

    });

    if (!stream)
      svg += '<line class="pc-axis" x1="' + C.str(left) + '" x2="' + C.str(width - right) + '" y1="' + C.padChartXY(y(Math.max(low, 0))) + '" y2="' + C.padChartXY(y(Math.max(low, 0))) + '"/>';

    var colW = (n > 1) ? step : plotW;

    labels.forEach(function (text, i) {
      svg += '<rect class="pc-hit" x="' + C.padChartXY(Math.max(left, x(i) - colW / 2)) + '" y="' + C.str(top) + '"'
           + ' width="' + C.padChartXY(Math.min(x(i) + colW / 2, width - right) - Math.max(left, x(i) - colW / 2)) + '" height="' + C.str(plotH) + '">'
           + '<title>' + C.padChartAttr(desc[i]) + '</title></rect>';
    });

    return svg + '</svg>';

  };

  C.padChartAreaInsideOut = function (series) {

    var peaks = new Map();
    var keys  = Array.from(series.keys());

    series.forEach(function (values, name) {
      peaks.set(name, values.indexOf(C.hi(values)));
    });

    var position = function (name) { return keys.findIndex(function (key) { return C.eq(name, key); }); };

    var names = keys.slice().sort(function (a, b) {
      return spaceship(peaks.get(a), peaks.get(b)) || spaceship(position(a), position(b));
    });

    var top = [], bottom = [];
    var up = 0, down = 0;

    names.forEach(function (name) {
      if (up < down) {
        top.push(name);
        up += C.sum(series.get(name));
      } else {
        bottom.push(name);
        down += C.sum(series.get(name));
      }
    });

    return bottom.reverse().concat(top);

  };

  C.padChartAreaWiggle = function (series, order, n) {

    var base = [0];
    var y    = 0;
    var j;

    for (j = 1; j < n; j++) {

      var s1 = 0, s2 = 0, below = 0;

      order.forEach(function (name) {
        var now    = series.get(name)[j];
        var before = series.get(name)[j - 1];
        var s3     = (now - before) / 2 + below;
        below += now - before;
        s1    += now;
        s2    += s3 * now;
      });

      if (s1 !== 0)
        y -= s2 / s1;

      base.push(y);

    }

    var mid = [];

    for (j = 0; j < n; j++) {
      var sum = 0;
      order.forEach(function (name) { sum += series.get(name)[j]; });
      mid.push(base[j] + sum / 2);
    }

    var shift = (phpMax(mid) + phpMin(mid)) / 2;

    return base.map(function (b) { return b - shift; });

  };

  C.padChartAreaEdge = function (xs, ys, smooth, back) {

    var n = xs.length;
    var d, i;

    if (back)
      d = 'M' + C.padChartXY(xs[n - 1]) + ',' + C.padChartXY(ys[n - 1]);
    else
      d = 'M' + C.padChartXY(xs[0]) + ',' + C.padChartXY(ys[0]);

    if (!smooth || n < 3) {
      if (n > 1) {
        if (back) for (i = n - 2; i >= 0; i--) d += 'L' + C.padChartXY(xs[i]) + ',' + C.padChartXY(ys[i]);
        else      for (i = 1; i <= n - 1; i++) d += 'L' + C.padChartXY(xs[i]) + ',' + C.padChartXY(ys[i]);
      }
      return d;
    }

    var t = C.padChartAreaTangents(xs, ys);

    var segment = function (a, b) {
      var h = (xs[b] - xs[a]) / 3;
      return 'C' + C.padChartXY(xs[a] + h) + ',' + C.padChartXY(ys[a] + t[a] * h)
           + ' ' + C.padChartXY(xs[b] - h) + ',' + C.padChartXY(ys[b] - t[b] * h)
           + ' ' + C.padChartXY(xs[b]) + ',' + C.padChartXY(ys[b]);
    };

    if (back)
      for (i = n - 1; i > 0; i--) d += segment(i, i - 1);
    else
      for (i = 0; i < n - 1; i++) d += segment(i, i + 1);

    return d;

  };

  C.padChartAreaTangents = function (xs, ys) {

    var n = xs.length;
    var s = [], t = [];
    var i;

    for (i = 0; i < n - 1; i++)
      s[i] = (xs[i + 1] !== xs[i]) ? (ys[i + 1] - ys[i]) / (xs[i + 1] - xs[i]) : 0;

    for (i = 1; i < n - 1; i++) {
      var h0 = xs[i] - xs[i - 1];
      var h1 = xs[i + 1] - xs[i];
      var p  = (h0 + h1 !== 0) ? (s[i - 1] * h1 + s[i] * h0) / (h0 + h1) : 0;
      t[i] = (spaceship(s[i - 1], 0) + spaceship(s[i], 0)) * phpMin([Math.abs(s[i - 1]), Math.abs(s[i]), 0.5 * Math.abs(p)]);
    }

    t[0]     = (3 * s[0] - t[1]) / 2;
    t[n - 1] = (3 * s[n - 2] - t[n - 2]) / 2;

    return t;

  };

})(typeof window !== 'undefined' ? window.PadChart : globalThis.PadChart);
