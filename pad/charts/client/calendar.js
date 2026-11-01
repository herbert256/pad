// calendar.php in the browser: a year at a glance, a square per day (core.js says how these
// files are written). padChartStamp and the day arithmetic here serve gantt.js too.
// needs: core
//
// The dates: padChartStamp reads them as the PHP does - date_parse and DateTime, whose scanner
// (timelib) is written again below token for token, so a text is cut up the same way. Read:
// ISO dates and date-times with or without an offset (2026-03-14, 2026-3-4, 26-03-14, 2026-03,
// 2026-03-14T10:30:15.5+02:00), Unix timestamps, 20261012, ISO week and ordinal dates, the
// dates of mail and HTTP (Mon, 12 Oct 2026 10:00:00 GMT), 12 Oct 2026, Oct 8, 2026, 3/14/2026,
// 14.03.2026, 10 am, noon - a zone written as an offset, or as UTC, GMT, Z, EST/EDT, CST/CDT,
// MST/MDT, PST/PDT, BST, CET or CEST.
//
// Not read here, where PHP reads them: relative text (tomorrow, next monday, +2 days,
// @1760000000), a zone named by its identifier (Europe/Amsterdam) or by another abbreviation -
// a military letter too: in 2026-03-14 T the T is the zone of seven hours west - and a weekday
// PHP does not leave out (Thu2026-03-14, mondays). A row with one is left out, as a row whose
// date PHP cannot read. A date with a zone, and a timestamp, is moved into the page's timezone
// by the browser's zone data, which before 1970 can differ from PHP's (Europe/Amsterdam before
// 1940). A year= past 9999, or between -999 and -1, PHP reads as no year: here it is a mistake
// the chart shows.

(function (C) {

  'use strict';

  // -------------------------------------------------------------------------------------
  // Days and seconds of the proleptic Gregorian calendar, as PHP's DateTime counts them
  // -------------------------------------------------------------------------------------

  function daysFromCivil(y, m, d) {
    y -= (m <= 2) ? 1 : 0;
    var era = Math.floor(y / 400);
    var yoe = y - era * 400;
    var doy = Math.floor((153 * (m + (m > 2 ? -3 : 9)) + 2) / 5) + d - 1;
    var doe = yoe * 365 + Math.floor(yoe / 4) - Math.floor(yoe / 100) + doy;
    return era * 146097 + doe - 719468;
  }

  function civilFromDays(z) {
    z += 719468;
    var era = Math.floor(z / 146097);
    var doe = z - era * 146097;
    var yoe = Math.floor((doe - Math.floor(doe / 1460) + Math.floor(doe / 36524) - Math.floor(doe / 146096)) / 365);
    var doy = doe - (365 * yoe + Math.floor(yoe / 4) - Math.floor(yoe / 100));
    var mp  = Math.floor((5 * doy + 2) / 153);
    var d   = doy - Math.floor((153 * mp + 2) / 5) + 1;
    var m   = mp + (mp < 10 ? 3 : -9);
    return [yoe + era * 400 + (m <= 2 ? 1 : 0), m, d];
  }

  function leap(y) { return y % 4 === 0 && (y % 100 !== 0 || y % 400 === 0); }

  function monthDays(y, m) { return [31, leap(y) ? 29 : 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31][m - 1]; }

  function validDate(y, m, d) { return m >= 1 && m <= 12 && d >= 1 && d <= monthDays(y, m); }

  // checkdate (): a year of 1 to 32767 too.

  function checkdate(m, d, y) { return y >= 1 && y <= 32767 && validDate(y, m, d); }

  var MONTHS = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
  var DAYS   = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

  function pad(n, width) { var s = String(Math.abs(n)); while (s.length < width) s = '0' + s; return (n < 0 ? '-' : '') + s; }

  // The parts of a moment, seconds since 1970 read in UTC.

  function parts(stamp) {
    var days = Math.floor(stamp / 86400);
    var secs = stamp - days * 86400;
    var ymd  = civilFromDays(days);
    var dow  = ((days % 7) + 11) % 7;
    return { y: ymd[0], m: ymd[1], d: ymd[2], days: days, dow: dow, h: Math.floor(secs / 3600), i: Math.floor(secs % 3600 / 60), s: secs % 60 };
  }

  // The year of the ISO week a day is in: the year of its Thursday.

  function isoYear(days) {
    var n = ((days % 7) + 10) % 7 + 1;
    return civilFromDays(days - (n - 1) + 3)[0];
  }

  // gmdate () and DateTime::format () of a moment in UTC, for the letters the charts use.

  function gmdate(format, stamp) {
    var p = parts(stamp), out = '';
    for (var k = 0; k < format.length; k++) {
      var c = format[k];
      switch (c) {
        case 'Y': out += pad(p.y, 4); break;
        case 'm': out += pad(p.m, 2); break;
        case 'd': out += pad(p.d, 2); break;
        case 'j': out += p.d; break;
        case 'n': out += p.m; break;
        case 'M': out += MONTHS[p.m - 1]; break;
        case 'D': out += DAYS[p.dow]; break;
        case 'N': out += (p.dow === 0 ? 7 : p.dow); break;
        case 'H': out += pad(p.h, 2); break;
        case 'G': out += p.h; break;
        case 'i': out += pad(p.i, 2); break;
        case 's': out += pad(p.s, 2); break;
        case 'o': out += isoYear(p.days); break;
        default:  out += c;
      }
    }
    return out;
  }

  function stampOf(y, m, d, h, i, s) { return daysFromCivil(y, m, d) * 86400 + (h || 0) * 3600 + (i || 0) * 60 + (s || 0); }

  // The wall clock of a moment in a timezone, as seconds read in UTC: Intl knows the zones.

  var clocks = {};

  function wallClock(stamp, zone) {

    if (zone === 'UTC' || zone === 'Z' || zone === 'GMT')
      return stamp;

    var fixed = /^([+-])(\d{2}):(\d{2})$/.exec(zone);

    if (fixed)
      return stamp + (fixed[1] === '-' ? -1 : 1) * (Number(fixed[2]) * 3600 + Number(fixed[3]) * 60);

    if (!clocks[zone]) {
      try {
        clocks[zone] = new Intl.DateTimeFormat('en-US', { timeZone: zone, hourCycle: 'h23', era: 'short', year: 'numeric', month: 'numeric',
                                                          day: 'numeric', hour: 'numeric', minute: 'numeric', second: 'numeric' });
      } catch (e) {
        clocks[zone] = 'UTC';
      }
    }

    if (clocks[zone] === 'UTC')
      return stamp;

    var at = {};
    clocks[zone].formatToParts(new Date(stamp * 1000)).forEach(function (p) { at[p.type] = p.value; });

    var year = Number(at.year);
    if (/^B/.test(at.era || '')) year = 1 - year;

    return stampOf(year, Number(at.month), Number(at.day), Number(at.hour) % 24, Number(at.minute), Number(at.second));

  }

  C.padChartDates = { days: daysFromCivil, civil: civilFromDays, stamp: stampOf, gmdate: gmdate, parts: parts, wallClock: wallClock };

  // -------------------------------------------------------------------------------------
  // date_parse (): PHP's scanner of dates (timelib), its tokens with their patterns in the
  // order it tries them - the longest wins, of two as long the first. The tokens of the
  // forms above are read as it reads them; a text with another one - relative, a zone this
  // file does not know - is null: no date here. { y, m, d, h, i, s, zone, weekday }, zone
  // the offset in seconds of a date written with one, else null.
  // -------------------------------------------------------------------------------------

  function anyCase(word) { return word.replace(/[a-z]/g, function (c) { return '[' + c + c.toUpperCase() + ']'; }); }
  function words(list) { return '(?:' + list.split(' ').map(anyCase).join('|') + ')'; }

  var space      = '(?:[ \\t]+|\\u00a0+|\\u202f+)';
  var frac       = '\\.[0-9]+';
  var hour24     = '(?:[01]?[0-9]|2[0-4])';
  var hour24lz   = '(?:[01][0-9]|2[0-4])';
  var hour12     = '(?:0?[1-9]|1[0-2])';
  var minute     = '[0-5]?[0-9]';
  var minutelz   = '[0-5][0-9]';
  var second     = '(?:[0-5]?[0-9]|60)';
  var secondlz   = '(?:[0-5][0-9]|60)';
  var meridian   = '[AaPp]\\.?[Mm]\\.?[\\0\\t ]';
  var tz         = '(?:\\(?[A-Za-z]{1,6}\\)?|[A-Z][a-z]+(?:[_/-][A-Za-z]+)+)';
  var tzcorr     = '(?:GMT)?[+-](?:' + hour24 + '(?::?' + minute + ')?|' + hour24lz + minutelz + secondlz + '|' + hour24lz + ':' + minutelz + ':' + secondlz + ')';
  var daysuf     = '(?:st|nd|rd|th)';
  var month      = '(?:0?[0-9]|1[0-2])';
  var day        = '(?:(?:[0-2]?[0-9]|3[01])' + daysuf + '?)';
  var year       = '[0-9]{1,4}';
  var year4      = '[0-9]{4}';
  var dayofyear  = '(?:00[1-9]|0[1-9][0-9]|[12][0-9][0-9]|3[0-5][0-9]|36[0-6])';
  var weekofyear = '(?:0[1-9]|[1-4][0-9]|5[0-3])';
  var monthlz    = '(?:0[0-9]|1[0-2])';
  var daylz      = '(?:0[0-9]|[12][0-9]|3[01])';
  var dayfulls   = words('sundays mondays tuesdays wednesdays thursdays fridays saturdays');
  var dayfull    = words('sunday monday tuesday wednesday thursday friday saturday');
  var dayabbr    = words('sun mon tue wed thu fri sat');
  var daytext    = '(?:' + dayfulls + '|' + dayfull + '|' + dayabbr + '|' + words('weekday weekdays') + ')';
  var monthabbr  = words('jan feb mar apr may jun jul aug sep sept oct nov dec');
  var monthtext  = '(?:' + words('january february march april may june july august september october november december') + '|' + monthabbr
                 + '|I|II|III|IV|V|VI|VII|VIII|IX|X|XI|XII)';
  var reltextnumber = words('first second third fourth fifth sixth seventh eight eighth ninth tenth eleventh twelfth');
  var reltexttext   = words('next last previous this');
  var reltextunit   = '(?:' + words('ms') + '|µ[sS]|' + words('msec millisecond µsec microsecond usec sec second min minute hour day fortnight forthnight month year')
                    + '[sS]?|' + words('weeks') + '|' + daytext + ')';
  var datenoyear    = monthtext + '[ .\\t-]*' + day + '(?:[,.stndrh\\t ]+|\\0)';
  var time12        = hour12 + '[:.]' + minutelz;
  var t             = '[tT]';

  var SKIP = 'skip', ERROR = 'error', NONE = 'none';

  // [ pattern, action ]: an action a function, or NONE for a token this file does not read.

  var RULES = [
    [words('yesterday'), NONE],
    [words('now'), function () {}],
    [words('noon'), function (s) { unhaveTime(s); haveTime(s); s.h = 12; }],
    [words('midnight today'), function (s) { unhaveTime(s); }],
    [words('tomorrow'), NONE],
    ['@-?[0-9]+', NONE],
    ['@-?[0-9]+\\.[0-9]{0,6}', NONE],
    ['(?:' + anyCase('first day of') + '|' + anyCase('last day of') + ')', NONE],
    ['(?:' + anyCase('back of ') + '|' + anyCase('front of ') + ')' + hour24 + '(?:' + space + '?' + meridian + ')?', NONE],
    ['(?:' + reltextnumber + '|' + reltexttext + ')' + space + '(?:' + dayfulls + '|' + dayfull + '|' + dayabbr + ')' + space + anyCase('of'), NONE],
    ['(?:' + hour12 + space + '?' + meridian + '|' + time12 + space + '?' + meridian + '|' + hour12 + '[:.]' + minute + '[:.]' + secondlz + space + '?' + meridian + ')',
     function (s, p) { haveTime(s); timeOf(s, p); s.h += meridianOf(p, s.h); }],
    [hour12 + ':' + minutelz + ':' + secondlz + '[:.][0-9]+' + meridian,
     function (s, p) { haveTime(s); s.h = nr(p, 2); s.i = nr(p, 2); if (p.at() === ':' || p.at() === '.') s.s = nr(p, 2); s.h += meridianOf(p, s.h); }],
    ['(?:' + t + hour24 + '|' + t + '?' + hour24 + '[:.]' + minute + '(?:[:.]' + second + '(?:' + frac + ')?)?)',
     function (s, p) { haveTime(s); timeOf(s, p); }],
    [t + '?' + hour24lz + minutelz,
     function (s, p) {
       if (s.haveTime === 0) { s.h = nr(p, 2); s.i = nr(p, 2); s.s = 0; }
       else if (s.haveTime === 1) s.y = nr(p, 4);
       else { s.errors++; return; }
       s.haveTime++;
     }],
    [t + '?' + hour24lz + minutelz + secondlz,
     function (s, p) { haveTime(s); s.h = nr(p, 2); s.i = nr(p, 2); s.s = nr(p, 2); }],
    [month + '/' + day + '(?:/' + year + ')?',
     function (s, p) { haveDate(s); s.m = nr(p, 2); s.d = nr(p, 2); if (p.at() === '/') s.y = processYear(p, 4); }],
    ['(?:[+-]?' + year4 + '-' + monthlz + '-' + daylz + '|' + year4 + '/' + monthlz + '/' + daylz + '/?|' + year4 + '/' + month + '/' + day + ')',
     function (s, p) { haveDate(s); s.y = signedNr(s, p, 4); s.m = nr(p, 2); s.d = nr(p, 2); }],
    ['[0-9]{2}-' + monthlz + '-' + daylz,
     function (s, p) { haveDate(s); s.y = processYear(p, 4); s.m = nr(p, 2); s.d = nr(p, 2); }],
    ['[+-][0-9]{5,19}-' + monthlz + '-' + daylz,
     function (s, p) { haveDate(s); s.y = signedNr(s, p, 19); s.m = nr(p, 2); s.d = nr(p, 2); }],
    [year4 + '-' + month,
     function (s, p) { haveDate(s); s.y = processYear(p, 4); s.m = nr(p, 2); s.d = 1; }],
    [year + '-' + month + '-' + day,
     function (s, p) { haveDate(s); s.y = processYear(p, 4); s.m = nr(p, 2); s.d = nr(p, 2); }],
    [day + '[ \\t.-]*' + monthtext + '[ \\t.-]*' + year,
     function (s, p) { haveDate(s); s.d = nr(p, 2); skipSuffix(p); s.m = monthOf(p); s.y = processYear(p, 4); }],
    [day + '[.\\t-]' + month + '[.-]' + year4,
     function (s, p) { haveDate(s); s.d = nr(p, 2); s.m = nr(p, 2); s.y = nr(p, 4); }],
    [day + '[.\\t]' + month + '\\.[0-9]{2}',
     function (s, p) { haveDate(s); s.d = nr(p, 2); s.m = nr(p, 2); s.y = processYear(p, 2); }],
    [monthtext + '[ .\\t-]*' + year4,
     function (s, p) { haveDate(s); s.m = monthOf(p); s.y = processYear(p, 4); s.d = 1; }],
    [year4 + '[ .\\t-]*' + monthtext,
     function (s, p) { haveDate(s); s.y = processYear(p, 4); s.m = monthOf(p); s.d = 1; }],
    ['(?:' + monthtext + '[ .\\t-]*' + day + '[,.stndrh\\t ]+' + year + '|' + datenoyear + ')',
     function (s, p) { haveDate(s); s.m = monthOf(p); s.d = nr(p, 2); s.y = processYear(p, 4); }],
    [day + '[ .\\t-]*' + monthtext,
     function (s, p) { haveDate(s); s.d = nr(p, 2); skipSuffix(p); s.m = monthOf(p); }],
    [year4 + monthlz + daylz,
     function (s, p) { haveDate(s); s.y = nr(p, 4); s.m = nr(p, 2); s.d = nr(p, 2); }],
    ['(?:' + year4 + monthlz + daylz + 'T' + hour24 + ':' + minutelz + ':' + secondlz
     + '|' + year4 + monthlz + daylz + t + hour24 + minutelz + secondlz
     + '|' + year4 + '-' + monthlz + '-' + daylz + 'T' + hour24lz + ':' + minutelz + ':' + secondlz + frac + '(?:' + tzcorr + ')?'
     + '|' + year4 + '-' + month + '-' + day + 'T' + hour24 + ':' + minute + ':' + second
     + '|' + year4 + ':' + monthlz + ':' + daylz + ' ' + hour24lz + ':' + minutelz + ':' + secondlz + ')',
     function (s, p) {
       haveTime(s); haveDate(s);
       s.y = nr(p, 4); s.m = nr(p, 2); s.d = nr(p, 2); s.h = nr(p, 2); s.i = nr(p, 2); s.s = nr(p, 2);
       if (p.at() === '.') { fracOf(p); if (p.at()) zoneOf(s, p); }
     }],
    [year4 + '[.-]?' + dayofyear,
     function (s, p) { haveDate(s); s.y = processYear(p, 4); s.d = nr(p, 3); s.m = 1; }],
    [year4 + '-?W' + weekofyear + '-?[0-7]', NONE],
    [year4 + '-?W' + weekofyear, NONE],
    [monthabbr + '-' + daylz + '-' + year,
     function (s, p) { haveDate(s); s.m = monthOf(p); s.d = nr(p, 2); s.y = processYear(p, 4); }],
    [year + '-' + monthabbr + '-' + daylz,
     function (s, p) { haveDate(s); s.y = processYear(p, 4); s.m = monthOf(p); s.d = nr(p, 2); }],
    [day + '/' + monthabbr + '/' + year4 + ':' + hour24lz + ':' + minutelz + ':' + secondlz + space + tzcorr,
     function (s, p) {
       haveTime(s); haveDate(s);
       s.d = nr(p, 2); s.m = monthOf(p); s.y = nr(p, 4); s.h = nr(p, 2); s.i = nr(p, 2); s.s = nr(p, 2);
       eatSpaces(p); zoneOf(s, p);
     }],
    [year4, function (s, p) { s.y = nr(p, 4); }],
    [words('ago'), NONE],
    [daytext,
     function (s, p) {
       if (!WEEKDAY.test(p.text)) return NONE;
       s.weekday = true; unhaveTime(s);
     }],
    [reltexttext + space + anyCase('week'), NONE],
    ['(?:' + reltextnumber + '|' + reltexttext + ')' + space + reltextunit, NONE],
    ['(?:' + words('january february march april may june july august september october november december') + '|' + monthabbr + ')',
     function (s, p) { haveDate(s); s.m = monthWord(p); }],
    ['(?:' + tzcorr + '|' + tz + ')',
     function (s, p) {
       if (s.haveZone) { if (s.haveZone > 1) s.errors++; else s.warnings++; s.haveZone++; return; }
       s.haveZone++; eatSpaces(p); zoneOf(s, p);
     }],
    [datenoyear + '(?:' + time12 + space + '?' + meridian + '|' + hour12 + '[:.]' + minute + '[:.]' + secondlz + space + '?' + meridian + ')',
     function (s, p) {
       haveDate(s); s.m = monthOf(p); s.d = nr(p, 2);
       haveTime(s); s.h = nr(p, 2); s.i = nr(p, 2);
       if (p.at() === ':' || p.at() === '.') { s.s = nr(p, 2); if (p.at() === '.') fracOf(p); }
       s.h += meridianOf(p, s.h);
     }],
    [datenoyear + '(?:' + t + '?' + hour24 + '[:.]' + minute + '(?:[:.]' + second + ')?|' + t + '?' + hour24 + '[:.]' + minute + '[:.]' + secondlz + space + '?(?:' + tzcorr + '|' + tz + '))',
     function (s, p) {
       haveDate(s); s.m = monthOf(p); s.d = nr(p, 2);
       haveTime(s); s.h = nr(p, 2); s.i = nr(p, 2);
       if (p.at() === ':') { s.s = nr(p, 2); if (p.at() === '.') fracOf(p); }
       if (p.at()) zoneOf(s, p);
     }],
    ['[+-]*[ \\t]*[0-9]{1,13}' + space + '?(?:' + reltextunit + '|' + anyCase('week') + ')', NONE],
    ['[.,]', SKIP],
    [space, SKIP],
    ['[\\0\\n]', SKIP]
  ];

  var WEEKDAY = new RegExp('^(?:' + dayfull + '|' + dayabbr + ')$');

  RULES.forEach(function (rule) {
    rule.push(new RegExp(rule[0], 'y'), new RegExp('^(?:' + rule[0] + ')$'));
  });

  // The state of a parse, and the helpers of the actions - on the text of the token, p.

  function unhaveTime(s) { s.haveTime = 0; s.h = s.i = s.s = 0; }
  function haveTime(s)   { if (s.haveTime) { s.errors++; throw ERROR; } s.haveTime = 1; s.h = s.i = s.s = 0; }
  function haveDate(s)   { if (s.haveDate) { s.errors++; throw ERROR; } s.haveDate = 1; }

  function cursor(text) {
    return { text: text, pos: 0, at: function () { return this.pos < this.text.length ? this.text[this.pos] : ''; } };
  }

  function isDigit(c) { return c >= '0' && c <= '9' && c !== ''; }

  // timelib_get_nr: past what is no digit, at most max digits - null at the end.

  function nrEx(p, max) {
    while (!isDigit(p.at())) { if (p.at() === '') return null; p.pos++; }
    var from = p.pos;
    while (isDigit(p.at()) && p.pos - from < max) p.pos++;
    return p.text.slice(from, p.pos);
  }

  function nr(p, max) { var digits = nrEx(p, max); return digits === null ? null : Number(digits); }

  // A year of fewer than four digits below 100 is of this century or the last.

  function processYear(p, max) {
    var digits = nrEx(p, max);
    if (digits === null) return null;
    var y = Number(digits);
    return (digits.length < 4 && y < 100) ? y + (y < 70 ? 2000 : 1900) : y;
  }

  function signedNr(s, p, max) {
    while (!isDigit(p.at()) && p.at() !== '+' && p.at() !== '-') { if (p.at() === '') { s.errors++; return 0; } p.pos++; }
    var sign = 1;
    while (p.at() === '+' || p.at() === '-') { if (p.at() === '-') sign = -sign; p.pos++; }
    var digits = nrEx(p, max);
    if (digits === null) { s.errors++; return 0; }
    return sign * Number(digits);
  }

  function fracOf(p) { p.pos++; while (isDigit(p.at())) p.pos++; }

  function skipSuffix(p) {
    if (/\s/.test(p.at())) return;
    if (/^(?:nd|rd|st|th)$/i.test(p.text.substr(p.pos, 2))) p.pos += 2;
  }

  function eatSpaces(p) { while (/[ \t  ]/.test(p.at()) && p.at() !== '') p.pos++; }

  var MONTHS_BY_NAME = { jan: 1, feb: 2, mar: 3, apr: 4, may: 5, jun: 6, jul: 7, aug: 8, sep: 9, sept: 9, oct: 10, nov: 11, dec: 12,
                         i: 1, ii: 2, iii: 3, iv: 4, v: 5, vi: 6, vii: 7, viii: 8, ix: 9, x: 10, xi: 11, xii: 12,
                         january: 1, february: 2, march: 3, april: 4, june: 6, july: 7, august: 8, september: 9, october: 10, november: 11, december: 12 };

  function monthWord(p) {
    var from = p.pos;
    while (/[A-Za-z]/.test(p.at()) && p.at() !== '') p.pos++;
    return MONTHS_BY_NAME[p.text.slice(from, p.pos).toLowerCase()] || 0;
  }

  function monthOf(p) {
    while (/[ \t\-.\/]/.test(p.at()) && p.at() !== '') p.pos++;
    return monthWord(p);
  }

  function meridianOf(p, h) {
    while (p.at() !== '' && !/[AaPp]/.test(p.at())) p.pos++;
    var am = /[Aa]/.test(p.at());
    p.pos++;
    return am ? (h === 12 ? -12 : 0) : (h !== 12 ? 12 : 0);
  }

  // The hour, the minutes and the seconds of a time token.

  function timeOf(s, p) {
    s.h = nr(p, 2);
    if (p.at() === ':' || p.at() === '.') {
      s.i = nr(p, 2);
      if (p.at() === ':' || p.at() === '.') {
        s.s = nr(p, 2);
        if (p.at() === '.') fracOf(p);
      }
    }
  }

  // The zones this file knows: an offset, UTC and GMT, and the abbreviations PHP finds
  // first by these names - their offset from UTC in seconds, summer time in it.

  var ZONES = { utc: 0, gmt: 0, z: 0, est: -18000, edt: -14400, cst: -21600, cdt: -18000, mst: -25200, mdt: -21600,
                pst: -28800, pdt: -25200, bst: 3600, cet: 3600, cest: 7200 };

  function tzCor(s, p) {
    var from = p.pos;
    while (/[0-9:]/.test(p.at()) && p.at() !== '') p.pos++;
    var text = p.text.slice(from, p.pos), n = parseInt(text, 10) || 0;
    var after = function (k) { return parseInt(text.slice(k), 10) || 0; };
    switch (text.length) {
      case 1: case 2: return n * 3600;
      case 3: case 4:
        if (text[1] === ':') return n * 3600 + after(2) * 60;
        if (text[2] === ':') return n * 3600 + after(3) * 60;
        return Math.trunc(n / 100) * 3600 + (n % 100) * 60;
      case 5: if (text[2] === ':') return n * 3600 + after(3) * 60; break;
      case 6: return Math.trunc(n / 10000) * 3600 + Math.trunc(n / 100) % 100 * 60 + n % 100;
      case 8: if (text[2] === ':' && text[5] === ':') return n * 3600 + after(3) * 60 + after(6); break;
    }
    s.errors++;
    return 0;
  }

  function zoneOf(s, p) {
    while (/[ \t(]/.test(p.at()) && p.at() !== '') p.pos++;
    if (p.text.substr(p.pos, 4) === 'GMT+' || p.text.substr(p.pos, 4) === 'GMT-') p.pos += 3;
    if (p.at() === '+' || p.at() === '-') {
      var sign = p.at() === '-' ? -1 : 1;
      p.pos++;
      s.zone = sign * tzCor(s, p);
    } else {
      var from = p.pos;
      while (/[A-Za-z0-9\/_\-+]/.test(p.at()) && p.at() !== '') p.pos++;
      var word = p.text.slice(from, p.pos);
      if (word.length < 6 && Object.prototype.hasOwnProperty.call(ZONES, word.toLowerCase()))
        s.zone = ZONES[word.toLowerCase()];
      else if (word === 'UTC')
        s.zone = 0;
      else
        throw NONE;
    }
    while (p.at() === ')') p.pos++;
  }

  // The longest token at pos: [ its length, its rule ].

  function token(text, pos) {
    var best = null;
    RULES.forEach(function (rule) {
      rule[2].lastIndex = pos;
      var m = rule[2].exec(text);
      if (!m) return;
      var length = m[0].length;
      for (var l = text.length - pos; l > length; l--)
        if (rule[3].test(text.substr(pos, l))) { length = l; break; }
      if (!best || length > best[0]) best = [length, rule];
    });
    return best;
  }

  var parsedBefore = new Map();

  // lenient: as DateTime reads it - a time past its range, 24:00, rolls on.

  function dateParse(text, lenient) {

    var key = (lenient ? '1' : '0') + text;

    if (parsedBefore.has(key))
      return parsedBefore.get(key);

    var s = { y: null, m: null, d: null, h: null, i: null, s: null, zone: null, haveTime: 0, haveDate: 0, haveZone: 0, weekday: false, errors: 0, warnings: 0 };
    var buffer = text + '\0';
    var pos = 0;
    var result = null;

    try {
      while (pos < text.length) {
        var found = token(buffer, pos);
        if (!found) { s.errors++; pos++; continue; }
        var action = found[1][1], part = buffer.substr(pos, found[0]).replace(/\0+$/, '');
        pos += found[0];
        if (action === NONE) throw NONE;
        if (action !== SKIP && action(s, cursor(part)) === NONE) throw NONE;
      }
      if (s.haveTime && !lenient && !(s.h >= 0 && s.h <= 23 && s.i >= 0 && s.i <= 59 && s.s >= 0 && s.s <= 59)) s.warnings++;
      if (s.haveDate && !(s.y !== null && s.m !== null && s.d !== null && validDate(s.y, s.m, s.d))) s.warnings++;
      if (!s.errors && !s.warnings && s.y !== null && s.m !== null && s.d !== null)
        result = { y: s.y, m: s.m, d: s.d, h: s.h || 0, i: s.i || 0, s: s.s || 0, zone: s.zone, weekday: s.weekday };
    } catch (e) {
      if (e !== NONE && e !== ERROR) throw e;
    }

    if (parsedBefore.size > 50000)
      parsedBefore.clear();

    parsedBefore.set(key, result);
    return result;

  }

  // padUnprotect (): the stand-ins of a value as the characters they stand for.

  var STAND_INS = { '\uE07B': '{', '\uE07D': '}', '\uE07C': '|', '\uE03D': '=', '\uE02C': ',', '\uE040': '@', '\uE027': "'", '\uE022': '"', '\uE05C': '\\' };

  function unprotect(text) { return text.replace(/[\uE07B\uE07D\uE07C\uE03D\uE02C\uE040\uE027\uE022\uE05C]/g, function (c) { return STAND_INS[c]; }); }

  // -------------------------------------------------------------------------------------
  // calendar.php
  // -------------------------------------------------------------------------------------

  // A date as seconds since 1970 of its wall clock, written as if in UTC - or false.

  C.padChartStamp = function (when) {

    if (!C.isScalar(when))
      return false;

    when = C.trim(unprotect(C.str(when)));

    if (when === '')
      return false;

    if (C.padChartBasicDate(when))
      when = when.slice(0, 4) + '-' + when.slice(4, 6) + '-' + when.slice(6, 8);

    var m;

    if ((m = /^(\d{4})-?W(\d{2})(?:-?([1-7]))?([^\n]*)$/.exec(when)) && Number(m[2]) >= 1 && Number(m[2]) <= 53) {
      var jan4 = daysFromCivil(Number(m[1]), 1, 4);
      var day  = jan4 - (((jan4 % 7) + 10) % 7) + (Number(m[2]) - 1) * 7 + (Number(m[3] || 1) - 1);
      if (isoYear(day) !== Number(m[1]))
        return false;
      when = gmdate('Y-m-d', day * 86400) + m[4];
    } else if ((m = /^(\d{4})-(\d{3})((?:[T ][^\n]*)?)$/.exec(when)) && Number(m[2]) >= 1 && Number(m[2]) <= 365 + (checkdate(2, 29, Number(m[1])) ? 1 : 0))
      when = gmdate('Y-m-d', (daysFromCivil(Number(m[1]), 1, 1) + Number(m[2]) - 1) * 86400) + m[3];

    var digits = /^[0-9]+$/.test(when);
    var parsed = null, zoned;

    if (!digits) {

      if (!(parsed = dateParse(when)))
        return false;

      zoned = parsed.zone !== null;

      // The weekday is left out and what is left read again - the date decides; left with
      // digits only, that is a timestamp.

      if (parsed.weekday) {
        when   = C.trim(when.replace(/\b(?:mon|tue|tues|wed|thu|thur|thurs|fri|sat|sun)(?:day|sday|nesday|rsday|urday)?\b\.?,?[ \t\n\v\f\r]*/gi, ''));
        digits = /^[0-9]+$/.test(when);

        // A weekday PHP does not leave out - Thu2026-03-14 - moves the date to that day:
        // no date here.

        if (!digits && (!(parsed = dateParse(when, true)) || parsed.weekday))
          return false;
      }

    }

    zoned = digits || zoned;
    var moment;

    if (digits) {
      moment = Number(when);
      if (!(moment <= 4e11))
        return false;
    } else
      moment = stampOf(parsed.y, parsed.m, parsed.d, parsed.h, parsed.i, parsed.s) - (zoned ? parsed.zone : 0);

    var local = zoned ? wallClock(moment, C.spec.zone) : moment;

    if (parts(local).y > 9999)
      return false;

    return local;

  };

  C.padChartBasicDate = function (when) {
    var m = /^(\d{4})(\d{2})(\d{2})\n?$/.exec(C.str(when));
    return !!m && checkdate(Number(m[2]), Number(m[3]), Number(m[1]));
  };

  C.padChartCalendar = function (rows, width, height) {

    var guess  = C.padChartGuess(rows);
    var used   = [];
    var dField = C.padChartPick('date',  guess[1], used);
    var vField = C.padChartPick('value', guess[0], used);
    var days   = new Map();

    C.values(rows).forEach(function (row) {

      row = C.padChartRow(row);

      var when = C.padChartText(row, dField);
      var v    = (vField !== '') ? C.get(row, vField) : 1;
      var stamp;

      if (when === '' || !C.padChartFinite(v) || (stamp = C.padChartStamp(when)) === false)
        return;

      var day = gmdate('Y-m-d', stamp);
      days.set(day, (days.has(day) ? days.get(day) : 0) + C.num(v));

    });

    var yearOption = C.int(C.padTagParm('year', 0));
    var first, last;

    // Days from here on are whole days since 1970.

    if (yearOption) {
      var y = yearOption;
      if (y >= 10000 || (y < 0 && y > -1000))
        throw new C.ChartError("the year " + y + " is no year of four digits PHP reads");
      if (y > 0 && y < 100)
        y += (y < 70) ? 2000 : 1900;
      first = daysFromCivil(y, 1, 1);
      last  = daysFromCivil(y, 12, 31);
    } else if (days.size) {
      days = new Map(Array.from(days.entries()).sort(function (a, b) { return C.cmp(a[0], b[0]); }));
      first = dayOf(Array.from(days.keys())[0]);
      last  = dayOf(Array.from(days.keys())[days.size - 1]);
    } else
      return '';

    // The latest weeks a column of at least 3px each fits in the width, ten years at most.

    var keep = Math.min(3660, 7 * (Math.max(1, C.int(Math.floor((width - 34) / 3))) - 1));

    if (Math.abs(last - first) > keep)
      first = last - keep;

    var start = first - (isoDay(first) - 1);
    var weeks = Math.trunc(Math.abs(last - start) / 7) + 1;
    var from  = gmdate('Y-m-d', first * 86400);
    var to    = gmdate('Y-m-d', last * 86400);
    var shown = new Map();

    days.forEach(function (v, d) { if (d >= from && d <= to) shown.set(d, v); });

    var values = Array.from(shown.values());
    var max    = values.length ? Math.max(C.hi(values), 0) : 0;
    var total  = values.reduce(function (s, v) { return s + v; }, 0);
    var label  = vField !== '' ? vField : 'rows';
    var desc   = [C.padChartNumber(total) + ' ' + label + ' from ' + gmdate('M j, Y', first * 86400) + ' to ' + gmdate('M j, Y', last * 86400)
                  + ', on ' + values.filter(function (v) { return v !== 0; }).length + ' days'];

    if (shown.size) {
      // arsort: the highest, the first of equal ones.
      var top = null;
      shown.forEach(function (v, d) { if (top === null || v > top[1]) top = [d, v]; });
      desc.push('highest ' + C.padChartNumber(top[1]) + ' on ' + gmdate('M j, Y', dayOf(top[0]) * 86400));
    }

    var title = C.str(C.padTagParm('title', C.ucfirst(label) + ' per day'));

    var open = C.padChartOpen('calendar', [shown, from, to], desc, title, width, height);
    var svg  = open[1];

    var left = 30;
    var topY = 16;
    var cell = Math.max(3, Math.min((width - left - 4) / weeks, (height - topY - 26) / 7));
    var gap  = cell >= 8 ? 2 : 1;

    [[0, 'Mon'], [2, 'Wed'], [4, 'Fri']].forEach(function (r) {
      svg += '<text x="' + C.str(left - 6) + '" y="' + C.padChartXY(topY + r[0] * cell + cell / 2 + 3) + '" text-anchor="end">' + r[1] + '</text>';
    });

    var lastMonth = '';
    var lastLabel = -9;

    for (var week = 0; week < weeks; week++)
      for (var dow = 0; dow < 7; dow++) {

        var date  = start + week * 7 + dow;
        var stamp = date * 86400;
        var day   = gmdate('Y-m-d', stamp);

        if (date < first || date > last)
          continue;

        var x = left + week * cell;
        var y2 = topY + dow * cell;

        if (gmdate('Y-m', stamp) !== lastMonth) {
          lastMonth = gmdate('Y-m', stamp);
          if (week - lastLabel >= 3 && (parts(stamp).d <= 7 || week === 0)) {
            svg += '<text x="' + C.padChartXY(x) + '" y="11">' + gmdate('M', stamp) + '</text>';
            lastLabel = week;
          }
        }

        var v   = days.has(day) ? days.get(day) : 0;
        var cls = (v > 0 && max > 0) ? 'pc-h' + Math.max(0, Math.min(6, C.int(Math.ceil(v / max * 7)) - 1)) : 'pc-none';

        svg += '<rect class="pc-mark ' + cls + '" x="' + C.padChartXY(x) + '" y="' + C.padChartXY(y2) + '" width="' + C.padChartXY(cell - gap) + '" height="' + C.padChartXY(cell - gap) + '" rx="' + (cell >= 8 ? 2 : 0) + '">'
             + '<title>' + C.padChartAttr(gmdate('D M j, Y', stamp) + ': ' + C.padChartNumber(v)) + '</title></rect>';

      }

    // The scale under the grid, on the right: less, the steps, more.

    var sy   = topY + 7 * cell + 8;
    var size = Math.min(11, Math.max(6, cell - gap));
    var sx   = Math.max(left + 32, left + weeks * cell - 8 * (size + 2) - 30);

    svg += '<text x="' + C.padChartXY(sx - 6) + '" y="' + C.padChartXY(sy + size - 1) + '" text-anchor="end">Less</text>'
         + '<rect class="pc-none" x="' + C.padChartXY(sx) + '" y="' + C.padChartXY(sy) + '" width="' + C.str(size) + '" height="' + C.str(size) + '" rx="2"/>';

    for (var k = 0; k <= 6; k++)
      svg += '<rect class="pc-h' + k + '" x="' + C.padChartXY(sx + (k + 1) * (size + 2)) + '" y="' + C.padChartXY(sy) + '" width="' + C.str(size) + '" height="' + C.str(size) + '" rx="2"/>';

    svg += '<text x="' + C.padChartXY(sx + 8 * (size + 2) + 4) + '" y="' + C.padChartXY(sy + size - 1) + '">More</text>';

    return svg + '</svg>';

  };

  // A key of $days - gmdate ( 'Y-m-d' ) - as its day since 1970.

  function dayOf(key) {
    var m = /^(-?\d+)-(\d{2})-(\d{2})$/.exec(key);
    return daysFromCivil(Number(m[1]), Number(m[2]), Number(m[3]));
  }

  function isoDay(days) { return ((days % 7) + 10) % 7 + 1; }

})(typeof window !== 'undefined' ? window.PadChart : globalThis.PadChart);
