// likert.php in the browser: answers on an ordered scale, a bar per question centred on the
// zero line (core.js says how these files are written).
// needs: core

(function (C) {

  'use strict';

  function sum(list) { return list.reduce(function (s, v) { return s + v; }, 0); }

  C.padChartLikert = function (rows, width, height) {

    var guess   = C.padChartGuess(rows);
    var used    = [];
    var label   = C.padChartPick('label', guess[1], used);
    var answers = C.padChartFields(C.padTagParm('value'));

    if (!answers.length)
      answers = guess[0].filter(function (field) { return field !== label; });

    if (answers.length < 2 || answers.length > 8) {
      if (C.count(rows))
        C.padError('a likert chart has two to eight answers in value=, from the most negative to the most positive - it has ' + answers.length);
      if (answers.length < 2)
        return '';
      answers = answers.slice(0, 8);
    }

    if (C.count(rows))
      answers.forEach(function (answer) {
        if (!C.padChartFieldSomewhere(rows, answer))
          C.padError("the likert chart's answer '" + C.padMakeSafe(answer, 30) + "' is no field of its rows");
      });

    var n    = answers.length;
    var side = Math.trunc(n / 2);

    // Per question: its name, the count of each answer and their total.

    var bars  = [];
    var index = 0;

    C.values(rows).forEach(function (row) {

      row = C.padChartRow(row);
      index++;

      var name   = C.padChartText(row, label);
      var counts = [];

      name = (name !== '') ? name : 'Row ' + index;

      answers.forEach(function (answer) {

        var count = C.get(row, answer);
        count = C.padChartFinite(count) ? C.num(count) : 0;

        if (count < 0) {
          C.padError("the likert chart's row " + index + ", '" + C.padMakeSafe(name, 30) + "', has " + C.padChartNumber(count)
                   + " answers '" + C.padMakeSafe(answer, 30) + "' - a count is 0 or more");
          count = 0;
        }

        counts.push(count);

      });

      bars.push([name, counts, sum(counts)]);

    });

    if (!bars.some(function (bar) { return bar[2] > 0; }))
      return '';

    // ranked: the largest share of positive answers on top - the questions without answers
    // after them, all in their own order where they compare equal.

    var positive = function (bar) { return bar[2] > 0 ? sum(bar[1].slice(n - side)) / bar[2] : -1; };

    if (C.bool(C.padTagParm('ranked', false)))
      bars.sort(function (p, q) {
        var a = positive(q), b = positive(p);
        return a < b ? -1 : (a > b ? 1 : 0);
      });

    var share = function (count, total) { return C.padChartNumber(C.round(count / total * 100, 1)) + '%'; };
    var desc  = [];

    bars.forEach(function (bar) {

      var name = bar[0], counts = bar[1], total = bar[2];

      if (total <= 0) {
        desc.push(name + ': no answers');
        return;
      }

      var parts = answers.map(function (answer, i) {
        return answer + ' ' + C.padChartNumber(counts[i]) + ' (' + share(counts[i], total) + ')';
      });

      desc.push(name + ': ' + C.padChartNumber(total) + ' answers - ' + parts.join(', '));

    });

    var title = C.str(C.padTagParm('title', 'Answers' + (label !== '' ? ' by ' + label : '')));

    var svg = C.padChartLikertColors(C.padChartOpen('likert', [bars, answers], desc, title, width, height)[1], n);

    // The extent of each side: the answers below the middle and half the neutral one left,
    // the rest right - the scale takes in the widest side of any question, on both sides.

    var ext = 0;

    bars.forEach(function (bar) {
      var counts = bar[1], total = bar[2];
      if (total > 0) {
        var half = (n % 2) ? counts[side] / 2 : 0;
        ext = Math.max(ext, (sum(counts.slice(0, side)) + half) / total, (sum(counts.slice(n - side)) + half) / total);
      }
    });

    var labelW = 0;
    bars.forEach(function (bar) { labelW = Math.max(labelW, C.padChartWidth(bar[0])); });

    var left  = 12 + Math.min(labelW, width / 3) + 12;
    var right = 16;
    var plotW = Math.max(20, width - left - right);
    var mid   = left + plotW / 2;
    var ticks = C.padChartTicks(0, ext * 100, Math.max(1, Math.min(5, C.int(plotW / 2 / 60))));
    var reach = ticks[ticks.length - 1] / 100;
    var at    = function (p) { return mid + p / reach * plotW / 2; };

    var legend = C.padChartLegend(answers, left, 6, plotW);

    svg += legend[0];

    var top    = 10 + legend[1];
    var bottom = 24;
    var plotH  = Math.max(1, height - top - bottom);
    var band   = plotH / bars.length;
    var barH   = Math.max(2, Math.min(28, band * 0.7));
    var floor  = C.padChartXY(top + plotH);

    ticks.forEach(function (tick) {
      (tick > 0 ? [-1, 1] : [1]).forEach(function (sign) {
        var p = C.padChartXY(at(sign * tick / 100));
        svg += '<line class="pc-grid" x1="' + p + '" x2="' + p + '" y1="' + C.padChartXY(top) + '" y2="' + floor + '"/>'
             + '<text x="' + p + '" y="' + C.str(height - 8) + '" text-anchor="middle">' + C.padChartNumber(tick) + '%</text>';
      });
    });

    // The zero line, under the bars: the neutral answer's share is written across it.

    svg += '<line class="pc-axis" x1="' + C.padChartXY(mid) + '" x2="' + C.padChartXY(mid) + '" y1="' + C.padChartXY(top) + '" y2="' + floor + '"/>';

    // A question: its name, and its answers from left to right as parts of one bar, the
    // first starting as far left of the zero line as the negative side reaches.

    bars.forEach(function (bar, b) {

      var name = bar[0], counts = bar[1], total = bar[2];
      var y    = top + (b + 0.5) * band;

      svg += '<text x="' + C.padChartXY(left - 12) + '" y="' + C.padChartXY(y + 4) + '" text-anchor="end">' + C.padChartAttr(C.padChartFit(name, left - 24)) + '</text>';

      if (total <= 0)
        return;

      var from = -(sum(counts.slice(0, side)) + ((n % 2) ? counts[side] / 2 : 0)) / total;

      answers.forEach(function (answer, i) {

        var to = from + counts[i] / total;

        if (counts[i] > 0) {

          var x0   = at(from);
          var w    = at(to) - x0;
          var text = C.padChartNumber(C.round(counts[i] / total * 100)) + '%';

          svg += '<rect class="pc-mark pc-gap ' + C.padChartSlot(i) + '" x="' + C.padChartXY(x0) + '" y="' + C.padChartXY(y - barH / 2) + '" width="' + C.padChartXY(w) + '" height="' + C.padChartXY(barH) + '">'
               + '<title>' + C.padChartAttr(name + ' · ' + answer + ': ' + C.padChartNumber(counts[i]) + ' of ' + C.padChartNumber(total) + ', ' + share(counts[i], total)) + '</title></rect>';

          if (barH >= 12 && C.padChartWidth(text) + 6 <= w)
            svg += '<text class="pc-in" x="' + C.padChartXY(x0 + w / 2) + '" y="' + C.padChartXY(y + 4) + '" text-anchor="middle" pointer-events="none">' + text + '</text>';

        }

        from = to;

      });

    });

    return svg + '</svg>';

  };

  // The default colours of a likert chart of n answers, as a diverging scale (likert.php).

  C.padChartLikertColors = function (svg, n) {

    var ends  = [['#eb6834', '#d95926'], ['#2a78d6', '#3987e5']];
    var grey  = ['#898781', '#898781'];
    var face  = ['#fcfcfb', '#1a1a19'];
    var side  = Math.trunc(n / 2);
    var light = '', both = '';

    for (var i = 0; i < n; i++) {

      var end, d;

      if (i < side) { end = ends[0]; d = side - i; }
      else if (i >= n - side) { end = ends[1]; d = i - (n - side) + 1; }
      else { end = grey; d = 0; }

      var w    = (d === 0) ? 0.45 : ((side > 1) ? 0.4 + 0.6 * (d - 1) / (side - 1) : 1);
      var day  = C.padChartMix(end[0], face[0], w);
      var name = '--pad-chart-' + (i ? i + 1 : 'series');

      light += name + ':' + day + ';';
      both  += name + ':light-dark(' + day + ',' + C.padChartMix(end[1], face[1], w) + ');';

    }

    var rules = '.pc-lk' + n + '{' + light + '}@supports (color:light-dark(#000,#fff)){.pc-lk' + n + '{' + both + '}}';

    svg = svg.replace(/^(<svg [^>]*class="pad-chart pad-chart-likert)"/, '$1 pc-lk' + n + '"');

    var close = svg.indexOf('</style>');

    return svg.slice(0, close) + rules + svg.slice(close);

  };

  // Two colours mixed, '#rrggbb' each: w of the first, the rest of the second.

  C.padChartMix = function (one, two, w) {
    var hex = '#';
    for (var c = 1; c <= 5; c += 2) {
      var a = parseInt(one.substr(c, 2), 16), b = parseInt(two.substr(c, 2), 16);
      var v = C.int(C.round(b + (a - b) * w));
      hex += (v < 16 ? '0' : '') + v.toString(16);
    }
    return hex;
  };

})(typeof window !== 'undefined' ? window.PadChart : globalThis.PadChart);
