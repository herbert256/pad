// gantt.php in the browser: tasks in time, a bar from start to end (core.js says how these
// files are written). The dates are read by calendar.js, which the page has before this file:
// its top comment says which forms JavaScript reads.
// needs: core, calendar

(function (C) {

  'use strict';

  var D = C.padChartDates;

  C.padChartGantt = function (rows, width, height) {

    rows = C.values(rows).map(C.padChartRow);

    var first = rows.length ? rows[0] : [];
    var dates = [], other = [];

    C.entries(first).forEach(function (e) {
      var field = e[1];
      if (C.isScalar(field) && !C.isNumeric(field) && C.padChartStamp(field) !== false)
        dates.push(C.str(e[0]));
      else if (C.isScalar(field) && !C.isNumeric(field))
        other.push(C.str(e[0]));
    });

    var used   = [];
    var fField = C.padChartPick('from',  dates, used);
    var tField = C.padChartPick('to',    dates, used);
    var label  = C.padChartPick('label', other, used);
    var cField = C.str(C.padTagParm('color'));
    var pField = C.str(C.padTagParm('progress'));

    // Numbers - week 1 to 12 - when every row with both ends has numbers there.

    var numeric = true;

    rows.forEach(function (row) {
      var a = C.get(row, fField, ''), b = C.get(row, tField, '');
      if (!C.isScalar(a) || !C.isScalar(b) || C.trim(C.str(a)) === '' || C.trim(C.str(b)) === '')
        return;
      if (!C.isNumeric(a) || !C.isNumeric(b))
        numeric = false;
    });

    var at     = function (v) { return numeric ? (C.isNumeric(v) ? C.num(v) : false) : C.padChartStamp(v); };
    var tasks  = [];
    var groups = new Map();

    rows.forEach(function (row, i) {

      var a = at(C.get(row, fField, ''));
      var b = at(C.get(row, tField, ''));

      if (a === false || b === false)
        return;

      if (b < a) { var swap = a; a = b; b = swap; }

      var group = (cField !== '') ? C.padChartText(row, cField) : '';
      var done  = (pField !== '' && C.isNumeric(C.get(row, pField, ''))) ? Math.max(0, Math.min(100, C.num(C.get(row, pField)))) : null;

      if (!groups.has(group))
        groups.set(group, groups.size);

      var name = C.padChartText(row, label);
      tasks.push([name !== '' ? name : 'Task ' + (i + 1), a, b, group, done]);

    });

    if (!tasks.length) {
      if (rows.length)
        C.padError("the gantt chart has no tasks with a start and an end - from='field' and to='field'");
      return '';
    }

    // Tasks with a time of day - a plan of hours - show it.

    var timed = false;

    tasks.forEach(function (task) {
      if (!numeric && (task[1] % 86400 !== 0 || task[2] % 86400 !== 0))
        timed = true;
    });

    var show = function (v) { return numeric ? C.padChartNumber(v) : D.gmdate(timed ? 'M j, Y H:i' : 'M j, Y', v); };
    var desc = tasks.map(function (task) {
      return task[0] + ': ' + show(task[1]) + ' – ' + show(task[2]) + (task[3] !== '' ? ' (' + task[3] + ')' : '')
           + (task[4] !== null ? ', ' + C.padChartNumber(task[4]) + '% done' : '');
    });

    var title = C.str(C.padTagParm('title', 'Plan'));

    var open = C.padChartOpen('gantt', tasks, desc, title, width, height);
    var svg  = open[1];

    var low  = C.lo(tasks.map(function (task) { return task[1]; }));
    var high = C.hi(tasks.map(function (task) { return task[2]; }));
    var mark = C.padTagParm('mark');

    mark = (mark !== '' && mark !== true) ? at(mark) : false;

    if (high === low)
      high = low + (numeric ? 1 : 86400);

    var labelW = 0;
    tasks.forEach(function (task) { labelW = Math.max(labelW, C.padChartWidth(task[0]) + 20); });

    var left  = C.int(Math.max(40, Math.min(width * 0.3, labelW)));
    var right = 14;
    var top   = 8;

    if (groups.size > 1) {
      var legend = C.padChartLegend(Array.from(groups.keys()), left, 6, width - left - right);
      svg += legend[0];
      top += legend[1];
    }

    var bottom = 22;
    var plotW  = width - left - right;
    var band   = (height - top - bottom) / tasks.length;
    var barH   = Math.max(3, Math.min(22, band * 0.65));
    var x      = function (v) { return left + (v - low) / (high - low) * plotW; };

    // The time axis: round numbers, or the first days of months, weeks or days.

    C.padChartGanttTicks(low, high, numeric, plotW, timed).forEach(function (text, tick) {
      var p = C.padChartXY(x(C.float(tick)));
      svg += '<line class="pc-grid" x1="' + p + '" x2="' + p + '" y1="' + C.str(top) + '" y2="' + C.padChartXY(height - bottom) + '"/>'
           + '<text x="' + p + '" y="' + C.str(height - 7) + '" text-anchor="middle">' + C.padChartAttr(text) + '</text>';
    });

    tasks.forEach(function (task, i) {

      var name = task[0], done = task[4];
      var y    = top + (i + 0.5) * band;
      var x0   = x(task[1]);
      var w    = Math.max(2, x(task[2]) - x0);
      var slot = C.padChartSlot(groups.get(task[3]));
      var tip  = '<title>' + C.padChartAttr(desc[i]) + '</title>';

      svg += '<text x="' + C.str(left - 8) + '" y="' + C.padChartXY(y + 4) + '" text-anchor="end">' + C.padChartAttr(C.padChartFit(name, left - 12)) + '</text>';

      if (done === null)
        svg += '<rect class="pc-mark ' + slot + '" x="' + C.padChartXY(x0) + '" y="' + C.padChartXY(y - barH / 2) + '" width="' + C.padChartXY(w) + '" height="' + C.padChartXY(barH) + '" rx="3">' + tip + '</rect>';
      else
        svg += '<g class="pc-mark">' + tip + '<rect class="' + slot + ' pc-todo" x="' + C.padChartXY(x0) + '" y="' + C.padChartXY(y - barH / 2) + '" width="' + C.padChartXY(w) + '" height="' + C.padChartXY(barH) + '" rx="3"/>'
             + '<rect class="' + slot + '" x="' + C.padChartXY(x0) + '" y="' + C.padChartXY(y - barH / 2) + '" width="' + C.padChartXY(w * done / 100) + '" height="' + C.padChartXY(barH) + '" rx="3"/></g>';

    });

    if (mark !== false && mark >= low && mark <= high) {
      var p = C.padChartXY(x(mark));
      svg += '<line class="pc-target" stroke-dasharray="4 3" x1="' + p + '" x2="' + p + '" y1="' + C.padChartXY(top - 2) + '" y2="' + C.padChartXY(height - bottom) + '">'
           + '<title>' + C.padChartAttr(show(mark)) + '</title></line>';
    }

    return svg + '</svg>';

  };

  // The ticks of the time axis, a Map of value => text: round numbers on a numeric axis; else
  // the first of every month, week or day - whichever leaves room - or of every quarter or
  // more years on a span of years. Days are seconds since 1970 in UTC.

  C.padChartGanttTicks = function (low, high, numeric, plotW, timed) {

    var ticks = new Map();

    if (numeric) {
      C.padChartTicks(low, high, Math.max(2, C.int(plotW / 70))).forEach(function (tick) {
        if (tick >= low && tick <= high)
          ticks.set(C.str(tick), C.padChartNumber(tick));
      });
      return ticks;
    }

    var days  = (high - low) / 86400;
    var room  = Math.max(1, plotW / 48);
    var start = Math.trunc(low);
    var day   = Math.floor(start / 86400) * 86400;
    var t, form, next;

    if (timed && days <= 2) {
      var hours = 12;
      [1, 2, 3, 6, 12].some(function (h) { if (days * 24 / h <= room) { hours = h; return true; } return false; });
      t    = day + Math.trunc(D.parts(start).h / hours) * hours * 3600;
      form = (D.gmdate('Ymd', Math.trunc(low)) === D.gmdate('Ymd', Math.trunc(high))) ? 'H:i' : 'M j H:i';
      next = function (v) { return v + hours * 3600; };
    } else if (days <= room) {
      var step = Math.max(1, C.int(Math.ceil(days / room)));
      t    = day;
      form = 'M j';
      next = function (v) { return v + step * 86400; };
    } else if (days / 7 <= room) {
      var weeks = Math.max(1, C.int(Math.ceil(days / 7 / room)));
      t    = day - (D.parts(day).dow + 6) % 7 * 86400;
      form = 'M j';
      next = function (v) { return v + weeks * 7 * 86400; };
    } else {
      var months = Math.max(1, C.int(Math.ceil(days / 30.4 / room)));
      var y = D.parts(start).y;
      if (months <= 12) {
        months = months <= 1 ? 1 : (months <= 3 ? 3 : (months <= 6 ? 6 : 12));
        t    = D.stamp(y, 1, 1);
        form = days > 400 ? 'M Y' : 'M';
        next = function (v) { var p = D.parts(v), m = p.m - 1 + months; return D.stamp(p.y + Math.floor(m / 12), m - Math.floor(m / 12) * 12 + 1, 1); };
      } else {
        var years = 100;
        [2, 5, 10, 25, 50, 100].some(function (n) { if (n * 12 >= months) { years = n; return true; } return false; });
        while (years * 12 < months)
          years *= 10;
        t    = D.stamp(C.int(Math.ceil(y / years) * years), 1, 1);
        form = 'Y';
        next = function (v) { return D.stamp(D.parts(v).y + years, 1, 1); };
      }
    }

    for (; t <= high && ticks.size <= 2 * room; t = next(t))
      if (t >= low)
        ticks.set(t, D.gmdate(form, t));

    return ticks;

  };

})(typeof window !== 'undefined' ? window.PadChart : globalThis.PadChart);
