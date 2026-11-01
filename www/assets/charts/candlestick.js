// candlestick.php in the browser: a price day by day (core.js says how these files are written).
// needs: core

(function (C) {

  'use strict';

  // padNumberAbbreviate ( $number, $precision ) of lib/number.php, for a number: 1.2M.

  function abbreviate(number, precision) {
    var units = ['', 'K', 'M', 'B', 'T', 'Q'];
    var unit  = 0;
    while (unit < units.length - 1 && Math.abs(C.round(number / Math.pow(1000, unit), precision)) >= 1000)
      unit++;
    var n = C.round(number / Math.pow(1000, unit), precision);
    if (n === 0) n = 0;
    var text = C.numberFormat(n, precision, '.', '');
    if (text.indexOf('.') >= 0)
      text = text.replace(/0+$/, '').replace(/\.$/, '');
    return text + units[unit];
  }

  C.padChartCandlestick = function (rows, width, height) {

    var guess  = C.padChartGuess(rows);
    var used   = [];
    var label  = C.padChartPick('label', guess[1], used);
    var fields = {};

    ['open', 'high', 'low', 'close'].forEach(function (price) { fields[price] = C.str(C.padTagParm(price, price)); });

    var volume = C.str(C.padTagParm('volume'));
    var days   = [];
    var index  = 0;

    C.values(rows).forEach(function (row) {

      row = C.padChartRow(row);
      index++;

      var day = {};
      var prices = ['open', 'high', 'low', 'close'];

      for (var p = 0; p < prices.length; p++) {
        if (!C.padChartFinite(C.get(row, fields[prices[p]])))
          return;
        day[prices[p]] = C.num(C.get(row, fields[prices[p]]));
      }

      // max ( $day ), then min ( $day ) of the day with that high in it.

      day.high   = Math.max(day.open, day.high, day.low, day.close);
      day.low    = Math.min(day.open, day.high, day.low, day.close);
      day.volume = (volume !== '' && C.padChartFinite(C.get(row, volume))) ? Math.max(0, C.num(C.get(row, volume))) : null;

      var text  = C.padChartText(row, label);
      day.label = (text !== '') ? text : String(index);
      days.push(day);

    });

    if (!days.length) {
      if (C.count(rows))
        C.padError("the candlestick chart has no row with four prices - open='" + fields.open + "', high='" + fields.high
                   + "', low='" + fields.low + "', close='" + fields.close + "'");
      return '';
    }

    var change = function (day) { return day.close - day.open; };
    var tip    = function (day) {
      return day.label + ': open ' + C.padChartNumber(day.open) + ', high ' + C.padChartNumber(day.high)
           + ', low ' + C.padChartNumber(day.low) + ', close ' + C.padChartNumber(day.close)
           + (day.volume !== null ? ', volume ' + C.padChartNumber(day.volume) : '');
    };
    var desc = days.map(tip);

    var title = C.str(C.padTagParm('title', 'Open, high, low and close' + (label !== '' ? ' by ' + label : '')));

    var open = C.padChartOpen('candlestick', days, desc, title, width, height);
    var svg  = open[1];

    // The price axis takes in the lowest low and the highest high - no zero.

    var ticks = C.padChartTicks(C.lo(days.map(function (d) { return d.low; })),
                                C.hi(days.map(function (d) { return d.high; })), height < 200 ? 3 : 5);
    var low   = ticks[0];
    var high  = ticks[ticks.length - 1];

    if (high === low)
      high = low + 1;

    var tickW = 0;
    ticks.forEach(function (tick) { tickW = Math.max(tickW, C.padChartWidth(C.padChartNumber(tick))); });

    var volumes = days.map(function (d) { return d.volume; }).filter(function (v) { return v !== null; });
    var most    = volumes.length ? C.hi(volumes) : 0;
    var mostTxt = (most > 0) ? abbreviate(most, 1) : '';
    tickW = Math.max(tickW, C.padChartWidth(mostTxt));

    var left   = Math.max(24, C.int(Math.ceil(tickW)) + 10);
    var right  = 10;
    var top    = 10;
    var bottom = 24;
    var plotW  = Math.max(1, width  - left - right);
    var plotH  = Math.max(1, height - top  - bottom);
    var priceH = (most > 0) ? plotH * 0.78 : plotH;
    var n      = days.length;
    var band   = plotW / n;
    var bodyW  = Math.max(1, Math.min(14, band * 0.6));
    var mid    = function (i) { return left + (i + 0.5) * band; };
    var y      = function (v) { return top + (high - v) / (high - low) * priceH; };

    ticks.forEach(function (tick) {
      svg += '<line class="pc-grid" x1="' + C.str(left) + '" x2="' + C.str(width - right) + '" y1="' + C.padChartXY(y(tick)) + '" y2="' + C.padChartXY(y(tick)) + '"/>'
           + '<text x="' + C.str(left - 6) + '" y="' + C.padChartXY(y(tick) + 4) + '" text-anchor="end">' + C.padChartNumber(tick) + '</text>';
    });

    // The days under the plot, thinned out when they would overlap.

    var labelW = 0;
    days.forEach(function (day) { labelW = Math.max(labelW, C.padChartWidth(day.label) + 8); });

    var every = Math.max(1, C.int(Math.ceil(labelW / Math.max(1, band))));

    days.forEach(function (day, i) {
      if (i % every === 0)
        svg += '<text x="' + C.padChartXY(mid(i)) + '" y="' + C.str(height - 8) + '" text-anchor="middle">' + C.padChartAttr(day.label) + '</text>';
    });

    // The volume: light bars in the lower fifth, its highest number at the left.

    if (most > 0) {

      var floor = top + plotH;
      var volH  = plotH * 0.18;

      days.forEach(function (day, i) {
        if (day.volume !== null) {
          var h = day.volume / most * volH;
          svg += '<rect class="pc-mark ' + (change(day) >= 0 ? 'pc-c3' : 'pc-c8') + '" fill-opacity="0.3" x="' + C.padChartXY(mid(i) - bodyW / 2) + '"'
               + ' y="' + C.padChartXY(floor - h) + '" width="' + C.padChartXY(bodyW) + '" height="' + C.padChartXY(h) + '">'
               + '<title>' + C.padChartAttr(day.label + ': volume ' + C.padChartNumber(day.volume)) + '</title></rect>';
        }
      });

      svg += '<text x="' + C.str(left - 6) + '" y="' + C.padChartXY(floor - volH + 4) + '" text-anchor="end" opacity="0.75">' + C.padChartAttr(mostTxt) + '</text>'
           + '<line class="pc-axis" x1="' + C.str(left) + '" x2="' + C.str(width - right) + '" y1="' + C.padChartXY(floor) + '" y2="' + C.padChartXY(floor) + '"/>';

    }

    // A candle per day: the wick from low to high, the body from open to close over it.

    days.forEach(function (day, i) {

      var cls  = (change(day) >= 0) ? 3 : 8;
      var top0 = y(Math.max(day.open, day.close));
      var h    = Math.max(1, Math.abs(y(day.open) - y(day.close)));

      svg += '<g class="pc-mark"><title>' + C.padChartAttr(tip(day)) + '</title>'
           + '<line class="pc-l' + cls + '" x1="' + C.padChartXY(mid(i)) + '" x2="' + C.padChartXY(mid(i)) + '"'
           + ' y1="' + C.padChartXY(y(day.high)) + '" y2="' + C.padChartXY(y(day.low)) + '"/>'
           + '<rect class="pc-c' + cls + '" x="' + C.padChartXY(mid(i) - bodyW / 2) + '" y="' + C.padChartXY(top0) + '"'
           + ' width="' + C.padChartXY(bodyW) + '" height="' + C.padChartXY(h) + '" rx="1"/></g>';

    });

    return svg + '</svg>';

  };

})(typeof window !== 'undefined' ? window.PadChart : globalThis.PadChart);
