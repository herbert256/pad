// bullet.php in the browser: measures against their targets (core.js says how these files are written).
// needs: core

(function (C) {

  'use strict';

  C.padChartBullet = function (rows, width, height) {

    var guess   = C.padChartGuess(rows);
    var used    = [];
    var field   = C.padChartPick('value', guess[0], used);
    var label   = C.padChartPick('label', guess[1], used);
    var target  = C.str(C.padTagParm('target'));
    var to      = C.str(C.padTagParm('to'));
    var bands   = C.padChartFields(C.padTagParm('bands'));

    if (bands.length > 4) {
      C.padError('a bullet chart takes four bands at most, it has ' + bands.length);
      bands = bands.slice(0, 4);
    }

    if (to !== '' && C.isNumeric(to) && (!C.padChartFinite(to) || C.float(to) <= 0)) {
      C.padError('the scale of a bullet chart ends at a number above 0');
      return '';
    }

    // Per row: its name, value, target, scale and the thresholds of its bands.

    var bullets = [];
    var desc    = [];
    var index   = 0;

    C.values(rows).forEach(function (row) {

      row = C.padChartRow(row);
      index++;
      var v = C.get(row, field);

      if (!C.padChartFinite(v) || C.float(v) < 0)
        return;

      v = C.num(v);

      var goal = C.isNumeric(target) ? target : (target !== '' ? C.get(row, target) : null);
      goal = C.padChartFinite(goal) ? C.num(goal) : null;

      var end = C.isNumeric(to) ? to : (to !== '' ? C.get(row, to) : null);
      end = (C.padChartFinite(end) && C.float(end) > 0) ? C.num(end) : null;

      var cuts = [];

      bands.forEach(function (band) {
        if (!C.isNumeric(band) && C.padChartFinite(C.get(row, band)))
          cuts.push([false, C.num(C.get(row, band))]);
        else if (C.isNumeric(band))
          cuts.push([true, C.num(band) / 100]);
      });

      if (end === null) {
        var high  = C.hi([v, goal === null ? 0 : goal].concat((cuts.length ? cuts : [[true, 0]]).map(function (c) { return c[0] ? 0 : c[1]; })));
        var round = C.padChartTicks(0, high > 0 ? high : 1, 4);
        end = round[round.length - 1];
      }

      cuts = cuts.map(function (c) { return c[0] ? c[1] * end : c[1]; })
                 .filter(function (c) { return c > 0 && c < end; })
                 .sort(function (a, b) { return a < b ? -1 : (a > b ? 1 : 0); });

      var name = C.padChartText(row, label);
      name = (name !== '') ? name : 'Row ' + index;

      bullets.push([name, v, goal, end, cuts]);
      desc.push(name + ': ' + C.padChartNumber(v) + (goal !== null ? ', target ' + C.padChartNumber(goal) : '')
                + ', scale 0 – ' + C.padChartNumber(end));

    });

    if (!bullets.length)
      return '';

    var title = C.str(C.padTagParm('title', C.ucfirst(field !== '' ? field : 'value') + (target !== '' && !C.isNumeric(target) ? ' against ' + target : '')));

    var open = C.padChartOpen('bullet', bullets, desc, title, width, height);
    var svg  = open[1];

    // The names on the left, at most a third of the width; then a track per row.

    var labelW = 0;
    bullets.forEach(function (bullet) { labelW = Math.max(labelW, C.padChartWidth(bullet[0])); });

    var n      = bullets.length;
    var left   = 12 + Math.min(labelW, width / 3) + 12;
    var right  = 16;
    var plotW  = Math.max(10, width - left - right);
    var rowH   = (height - 12) / n;
    var trackH = Math.max(6, Math.min(28, rowH - 26));

    bullets.forEach(function (bullet, i) {

      var name = bullet[0], v = bullet[1], goal = bullet[2], end = bullet[3], cuts = bullet[4];

      var top   = 6 + i * rowH + (rowH - trackH - 16) / 2;
      var x     = function (at) { return left + Math.min(1, Math.max(0, at / end)) * plotW; };
      var edges = [0].concat(cuts, [end]);
      var steps = edges.length - 1;

      // The bands from dark to light.

      for (var b = 0; b < steps; b++)
        svg += '<rect class="pc-co" fill-opacity="' + C.padChartXY(steps === 1 ? 0.2 : 0.5 - 0.35 * b / (steps - 1)) + '" x="' + C.padChartXY(x(edges[b])) + '" y="' + C.padChartXY(top) + '"'
             + ' width="' + C.padChartXY(x(edges[b + 1]) - x(edges[b])) + '" height="' + C.padChartXY(trackH) + '">'
             + '<title>' + C.padChartAttr(name + ': ' + C.padChartNumber(edges[b]) + ' – ' + C.padChartNumber(edges[b + 1])) + '</title></rect>';

      var barH = Math.max(2, trackH / 3);
      svg += '<rect class="pc-mark pc-c1" x="' + C.padChartXY(left) + '" y="' + C.padChartXY(top + (trackH - barH) / 2) + '" width="' + C.padChartXY(Math.max(0.5, x(v) - left)) + '" height="' + C.padChartXY(barH) + '">'
           + '<title>' + C.padChartAttr(desc[i]) + '</title></rect>';

      if (goal !== null)
        svg += '<line class="pc-axis" style="stroke-width:2.5" x1="' + C.padChartXY(x(goal)) + '" x2="' + C.padChartXY(x(goal)) + '"'
             + ' y1="' + C.padChartXY(top + trackH * 0.15) + '" y2="' + C.padChartXY(top + trackH * 0.85) + '">'
             + '<title>' + C.padChartAttr(name + ': target ' + C.padChartNumber(goal)) + '</title></line>';

      svg += '<text x="' + C.padChartXY(left - 12) + '" y="' + C.padChartXY(top + trackH / 2 + 4) + '" text-anchor="end">' + C.padChartAttr(C.padChartFit(name, left - 24)) + '</text>';

      // The ticks: round numbers of the row's own scale, as many as fit.

      var ticks = C.padChartTicks(0, end, Math.max(2, Math.min(6, C.int(plotW / 70))));

      ticks.forEach(function (tick) {
        if (tick <= end + 1e-9)
          svg += '<line class="pc-axis" x1="' + C.padChartXY(x(tick)) + '" x2="' + C.padChartXY(x(tick)) + '" y1="' + C.padChartXY(top + trackH) + '" y2="' + C.padChartXY(top + trackH + 3) + '"/>'
               + '<text x="' + C.padChartXY(x(tick)) + '" y="' + C.padChartXY(top + trackH + 14) + '" text-anchor="middle" style="font-size:9.5px">' + C.padChartNumber(tick) + '</text>';
      });

    });

    return svg + '</svg>';

  };

})(typeof window !== 'undefined' ? window.PadChart : globalThis.PadChart);
