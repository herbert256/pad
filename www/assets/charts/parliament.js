// parliament.php in the browser: a dot per seat in rows of half circles (core.js says how
// these files are written).
// needs: core

(function (C) {

  'use strict';

  C.padChartParliament = function (rows, width, height) {

    var guess   = C.padChartGuess(rows);
    var numbers = guess[0], names = guess[1];

    var used    = [];
    var value   = C.padChartPick('value', numbers, used);
    var label   = C.padChartPick('label', names,   used);
    var parties = [];

    C.padChartPoints(rows, label, value).forEach(function (point) {

      var name = point[0], seats = point[1];

      if (seats !== Math.floor(seats)) {
        if (C.spec.check)
          C.padError('the parliament has a party of ' + C.padChartNumber(seats) + ' seats - seats are whole numbers');
        seats = C.round(seats);
      }

      if (seats > 0)
        parties.push([name, seats]);

    });

    var total = parties.reduce(function (s, p) { return s + p[1]; }, 0);

    if (!parties.length)
      return '';

    if (total > 5000) {
      if (C.spec.check)
        C.padError('the parliament has ' + C.padChartNumber(total) + ' seats - 5000 at most');
      return '';
    }

    parties.forEach(function (party) { party[1] = C.int(party[1]); });

    total = C.int(total);

    var majority = Math.trunc(total / 2) + 1;
    var desc     = [];

    parties.forEach(function (party) {
      desc.push(party[0] + ': ' + party[1] + ' ' + (party[1] === 1 ? 'seat' : 'seats'));
    });

    desc.push(total + ' ' + (total === 1 ? 'seat' : 'seats') + ', ' + majority + ' for a majority');

    var title = C.str(C.padTagParm('title', 'Seats' + (label !== '' ? ' by ' + label : '')));

    var open = C.padChartOpen('parliament', parties, desc, title, width, height);
    var svg  = open[1];

    var inner = 0.4;
    var space, radii, room, j;

    for (var k = 1; ; k++) {

      space = (1 - inner) / k;
      radii = [];
      room  = [];

      for (j = 0; j < k; j++) {
        radii[j] = inner + (j + 0.5) * space;
        room[j]  = C.int(Math.floor(Math.PI * radii[j] / space)) + 1;
      }

      if (room.reduce(function (s, n) { return s + n; }, 0) >= total)
        break;

    }

    var length = radii.reduce(function (s, r) { return s + r; }, 0);
    var count  = [], rest = [];

    radii.forEach(function (radius, j) {
      var exact = total * radius / length;
      count[j]  = Math.min(room[j], C.int(Math.floor(exact)));
      rest[j]   = exact - count[j];
    });

    while (count.reduce(function (s, n) { return s + n; }, 0) < total) {
      var best = null;
      radii.forEach(function (radius, j) {
        if (count[j] < room[j] && (best === null || rest[j] > rest[best]))
          best = j;
      });
      count[best]++;
      rest[best]--;
    }

    var places = [];

    radii.forEach(function (radius, j) {
      for (var t = 0; t < count[j]; t++)
        places.push([count[j] === 1 ? Math.PI / 2 : Math.PI * (1 - t / (count[j] - 1)), radius]);
    });

    var spaceship = function (a, b) { return a < b ? -1 : (a > b ? 1 : 0); };

    places.sort(function (a, b) { return spaceship(C.round(b[0], 9), C.round(a[0], 9)) || spaceship(b[1], a[1]); });

    var keys = parties.map(function (party) { return party[0] + ' ' + party[1]; });

    var legendH = C.padChartLegend(keys, 10, 0, width - 20)[1];

    var r   = Math.max(20, Math.min((width - 20) / 2 / (1 - 0.12 * space), (height - legendH - 22) / (1 + 0.26 * space)));
    var dot = Math.max(0.5, space * r * 0.38);
    var cx  = width / 2;
    var cy  = 8 + r * (1 - space / 2) + dot;

    var owner = [];

    parties.forEach(function (party, i) {
      for (var s = 0; s < party[1]; s++)
        owner.push(i);
    });

    places.forEach(function (place, p) {
      var angle = place[0], radius = place[1];
      var i = owner[p];
      svg += '<circle class="pc-mark ' + C.padChartSlot(i) + '" cx="' + C.padChartXY(cx + radius * r * Math.cos(angle)) + '" cy="' + C.padChartXY(cy - radius * r * Math.sin(angle)) + '"'
           + ' r="' + C.padChartXY(dot) + '"><title>' + C.padChartAttr(desc[i]) + '</title></circle>';
    });

    svg += '<text class="pc-big" x="' + C.padChartXY(cx) + '" y="' + C.padChartXY(cy - 18) + '" text-anchor="middle">' + total + '</text>'
         + '<text x="' + C.padChartXY(cx) + '" y="' + C.padChartXY(cy - 2) + '" text-anchor="middle">' + C.padChartAttr(majority + ' for a majority') + '</text>';

    var line = keys.reduce(function (s, key) { return s + (C.padChartWidth(key) + 26); }, 0) - 12;
    var from = (line <= width - 20) ? (width - line) / 2 : 10;

    var legend = C.padChartLegend(keys, from, cy + dot + 12, width - 20)[0];

    return svg + legend + '</svg>';

  };

})(typeof window !== 'undefined' ? window.PadChart : globalThis.PadChart);
