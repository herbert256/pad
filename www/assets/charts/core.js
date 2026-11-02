// The charts drawn in the browser: {chart 'bar', data='sales', client/}. The server gathers the
// rows and the tag's options and writes them into the page (subs/charts/_lib/client.php); this file
// and the file of the chart's family draw the SVG here, from them - the same SVG the server
// draws, character for character, so a chart looks and reads the same either way.
//
// The files of this directory are the PHP of subs/charts/_lib/ written again in JavaScript, a file
// for a file and a function for a function, with the names the PHP has: padChartNumber is
// padChartNumber. A PHP builtin whose answer JavaScript would write otherwise has a helper
// here that answers as PHP does - a float as text, round, number_format, is_numeric, the
// (int) cast, mb_strlen - and a PHP array is a Map, whose keys keep their order and their
// kind: the field "2024" of a row is the key 2024, as PHP has it.
//
// The page gets this file once, the file of each family it draws once, then a span per
// chart that the SVG replaces: PadChart.mount ( span ) reads its data-pad-chart.

(function (C) {

  'use strict';

  if (C.ready) return;
  C.ready = true;

  // -------------------------------------------------------------------------------------
  // PHP's answers
  // -------------------------------------------------------------------------------------

  // A key of a PHP array: a string that is a whole number in canonical form is that number.

  C.key = function (k) {
    if (typeof k === 'number') return Number.isInteger(k) ? k : Math.trunc(k);
    if (typeof k === 'boolean') return k ? 1 : 0;
    if (k === null || k === undefined) return '';
    k = String(k);
    if (/^(0|-?[1-9][0-9]*)$/.test(k) && Number.isSafeInteger(Number(k))) return Number(k);
    return k;
  };

  // The values the server sends: a PHP array as { m: [ [ key, value ], ... ] }, read into a Map.

  C.decode = function (v) {
    if (Array.isArray(v)) return v.map(C.decode);
    if (v !== null && typeof v === 'object') {
      if (Array.isArray(v.m)) {
        var map = new Map();
        v.m.forEach(function (pair) { map.set(C.key(pair[0]), C.decode(pair[1])); });
        return map;
      }
      var out = {};
      Object.keys(v).forEach(function (k) { out[k] = C.decode(v[k]); });
      return out;
    }
    return v;
  };

  C.isArray  = function (v) { return v instanceof Map || Array.isArray(v); };
  C.isString = function (v) { return typeof v === 'string'; };
  C.isScalar = function (v) { return typeof v === 'string' || typeof v === 'number' || typeof v === 'boolean'; };

  C.entries = function (v) {
    if (v instanceof Map) return Array.from(v.entries());
    if (Array.isArray(v)) return v.map(function (x, i) { return [i, x]; });
    if (v === null || v === undefined || v === false) return [];
    if (typeof v === 'object') return Object.keys(v).map(function (k) { return [C.key(k), v[k]]; });
    return [[0, v]];
  };

  C.values = function (v) { return C.entries(v).map(function (e) { return e[1]; }); };
  C.keys   = function (v) { return C.entries(v).map(function (e) { return e[0]; }); };
  C.count  = function (v) { return v instanceof Map ? v.size : Array.isArray(v) ? v.length : (v === null || v === undefined ? 0 : 1); };

  // $array [$key] ?? $default

  C.get = function (arr, k, dflt) {
    if (dflt === undefined) dflt = null;
    var v;
    if (arr instanceof Map) v = arr.get(C.key(k));
    else if (Array.isArray(arr)) v = arr[C.key(k)];
    else if (arr !== null && typeof arr === 'object') v = arr[k];
    return (v === undefined || v === null) ? dflt : v;
  };

  C.has = function (arr, k) { return C.get(arr, k) !== null; };

  // A Map made of pairs, or of the keys of a list with one value - array_fill_keys.

  C.fillKeys = function (keys, value) {
    var map = new Map();
    keys.forEach(function (k) { map.set(C.key(k), typeof value === 'function' ? value() : value); });
    return map;
  };

  // is_numeric: leading and trailing white space, a sign, digits with a point, an exponent.

  var NUMERIC = /^[ \t\n\r\v\f]*[+-]?([0-9]+(\.[0-9]*)?|\.[0-9]+)([eE][+-]?[0-9]+)?[ \t\n\r\v\f]*$/;

  C.isNumeric = function (v) {
    if (typeof v === 'number') return true;
    if (typeof v !== 'string') return false;
    return NUMERIC.test(v);
  };

  // $value + 0, (float) and (int): a numeric string as its number, else the number its numeric
  // start makes - '12abc' 12 - else 0.

  var PREFIX = /^[ \t\n\r\v\f]*[+-]?([0-9]+(\.[0-9]*)?|\.[0-9]+)([eE][+-]?[0-9]+)?/;

  C.float = function (v) {
    if (typeof v === 'number') return v;
    if (typeof v === 'boolean') return v ? 1 : 0;
    if (v === null || v === undefined) return 0;
    if (v instanceof Map || Array.isArray(v)) return C.count(v) ? 1 : 0;
    var m = PREFIX.exec(String(v));
    return m ? parseFloat(m[0].trim()) : 0;
  };

  C.num = C.float;

  C.int = function (v) {
    if (typeof v === 'string') {
      var m = PREFIX.exec(v);
      if (!m) return 0;
      v = parseFloat(m[0].trim());
    } else v = C.float(v);
    if (!isFinite(v)) return 0;
    var t = Math.trunc(v);
    return Object.is(t, -0) ? 0 : t;
  };

  C.bool = function (v) {
    if (v instanceof Map || Array.isArray(v)) return C.count(v) > 0;
    return !(v === false || v === null || v === undefined || v === 0 || v === '' || v === '0');
  };

  // 10 ** $k for a whole $k: the correctly rounded power, as C's pow gives it.

  C.pow10 = function (k) { return Number.isInteger(k) ? Number('1e' + k) : Math.pow(10, k); };

  // A number as PHP writes it in a string: fourteen significant digits, %.14G.

  C.fstr = function (n) {
    if (typeof n !== 'number') return C.str(n);
    if (Number.isNaN(n)) return 'NAN';
    if (n === Infinity) return 'INF';
    if (n === -Infinity) return '-INF';
    if (Object.is(n, -0)) return '-0';
    if (Number.isInteger(n) && Math.abs(n) < 1e15) return String(n);
    var parts = n.toExponential(13).split('e');
    var mant  = parts[0].indexOf('.') >= 0 ? parts[0].replace(/\.?0+$/, '') : parts[0];
    var exp   = parseInt(parts[1], 10);
    if (exp < -4 || exp >= 14) {
      if (mant.indexOf('.') < 0) mant += '.0';
      return mant + 'E' + (exp < 0 ? '-' : '+') + Math.abs(exp);
    }
    var s = n.toPrecision(14);
    return s.indexOf('.') >= 0 ? s.replace(/\.?0+$/, '') : s;
  };

  // (string) $value

  C.str = function (v) {
    if (v === null || v === undefined || v === false) return '';
    if (v === true) return '1';
    if (typeof v === 'number') return C.fstr(v);
    if (v instanceof Map || Array.isArray(v)) return 'Array';
    return String(v);
  };

  // round (), PHP 8.4 and later: the decimal midpoint is the edge - 2.675 to two decimals is 2.68.

  var POW10 = []; for (var p = 0; p <= 22; p++) POW10.push(Number('1e' + p));

  function intpow10(power) { return (power < 0 || power > 22) ? Math.pow(10, power) : POW10[power]; }
  function copysign(a, b) { return (b < 0 || Object.is(b, -0)) ? -Math.abs(a) : Math.abs(a); }

  C.round = function (value, places) {
    value  = C.float(value);
    places = places === undefined ? 0 : C.int(places);
    if (!isFinite(value) || value === 0) return value;
    var exponent = intpow10(Math.abs(places));
    var tmp, tmp2;
    if (value >= 0) { tmp = Math.floor(places > 0 ? value * exponent : value / exponent); tmp2 = tmp + 1; }
    else            { tmp = Math.ceil (places > 0 ? value * exponent : value / exponent); tmp2 = tmp - 1; }
    if ((places > 0 ? tmp2 / exponent : tmp2 * exponent) === value) tmp = tmp2;
    if (Math.abs(tmp) >= 1e16) return value;
    var edge = places > 0 ? Math.abs((tmp + copysign(0.5, tmp)) / exponent) : Math.abs((tmp + copysign(0.5, tmp)) * exponent);
    if (Math.abs(value) >= edge) tmp = tmp + copysign(1, tmp);
    if (Math.abs(places) < 23) tmp = places > 0 ? tmp / exponent : tmp * exponent;
    else tmp = Number(String(tmp) + 'e' + (-places));
    return tmp;
  };

  // number_format ()

  C.numberFormat = function (n, decimals, point, sep) {
    decimals = Math.max(0, C.int(decimals === undefined ? 0 : decimals));
    point    = point === undefined ? '.' : point;
    sep      = sep   === undefined ? ',' : sep;
    var d = C.round(C.float(n), decimals);
    var negative = d < 0;
    d = Math.abs(d);
    if (!isFinite(d)) return (negative ? '-' : '') + (Number.isNaN(d) ? 'nan' : 'inf');
    var fixed;
    if (d < 1e21) fixed = d.toFixed(decimals);
    else fixed = BigInt(d).toString() + (decimals > 0 ? '.' + '0'.repeat(decimals) : '');
    if (negative && Number(fixed) === 0) negative = false;
    var dot  = fixed.indexOf('.');
    var whole = dot >= 0 ? fixed.slice(0, dot) : fixed;
    var frac  = dot >= 0 ? fixed.slice(dot + 1) : '';
    var grouped = '';
    while (whole.length > 3) { grouped = sep + whole.slice(-3) + grouped; whole = whole.slice(0, -3); }
    grouped = whole + grouped;
    return (negative ? '-' : '') + grouped + (decimals > 0 ? point + frac : '');
  };

  // number_format of a PHP integer: its digits, grouped - no rounding, which a float needs.

  C.intFormat = function (big) {
    var negative = big < 0n, digits = (negative ? -big : big).toString(), grouped = '';
    while (digits.length > 3) { grouped = ',' + digits.slice(-3) + grouped; digits = digits.slice(0, -3); }
    return (negative ? '-' : '') + digits + grouped;
  };

  // sprintf ( '%.3g', $n ), the one format the charts use: three significant digits, PHP's way.

  C.g3 = function (n) {
    n = C.float(n);
    var exp = n === 0 ? 0 : Math.floor(Math.log10(Math.abs(n)));
    var e   = n.toExponential(2).split('e');
    exp = parseInt(e[1], 10);
    if (exp < -4 || exp >= 3) {
      var mant = e[0].replace(/(\.\d*?)0+$/, '$1');
      if (/\.$/.test(mant)) mant += '0';
      return mant + 'e' + (exp < 0 ? '-' : '+') + Math.abs(exp);
    }
    var s = n.toPrecision(3);
    return s.indexOf('.') >= 0 ? s.replace(/\.?0+$/, '') : s;
  };

  C.ucfirst = function (s) {
    s = C.str(s);
    return s.length && s[0] >= 'a' && s[0] <= 'z' ? s[0].toUpperCase() + s.slice(1) : s;
  };

  C.mbStrlen = function (s) { return Array.from(C.str(s)).length; };
  C.mbSubstr = function (s, start, len) {
    var chars = Array.from(C.str(s));
    if (start < 0) start = Math.max(0, chars.length + start);
    return chars.slice(start, len === undefined ? undefined : (len < 0 ? chars.length + len : start + len)).join('');
  };
  C.strlen = function (s) { return new TextEncoder().encode(C.str(s)).length; };

  C.trim = function (s, chars) {
    s = C.str(s);
    var set = chars === undefined ? ' \t\n\r\0\x0B' : chars;
    var a = 0, b = s.length;
    while (a < b && set.indexOf(s[a]) >= 0) a++;
    while (b > a && set.indexOf(s[b - 1]) >= 0) b--;
    return s.slice(a, b);
  };

  C.htmlspecialchars = function (s) {
    return C.str(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
                   .replace(/"/g, '&quot;').replace(/'/g, '&#039;');
  };

  // PHP 8's <=> : numbers and numeric strings by their number, other strings as strings.

  C.cmp = function (a, b) {
    if (typeof a === 'boolean' || typeof b === 'boolean' || a === null || b === null) {
      var x = C.bool(a), y = C.bool(b);
      return x === y ? 0 : (x ? 1 : -1);
    }
    var na = typeof a === 'number' || C.isNumeric(a), nb = typeof b === 'number' || C.isNumeric(b);
    if (na && nb) { var u = C.float(a), v = C.float(b); return u < v ? -1 : (u === v ? 0 : 1); }
    if (typeof a === 'number' && typeof b === 'string') a = C.str(a);
    if (typeof b === 'number' && typeof a === 'string') b = C.str(b);
    return C.strcmp(C.str(a), C.str(b));
  };

  // Two strings as PHP compares them: by their bytes - in UTF-8, by code point, where
  // JavaScript's < compares UTF-16 units and puts an emoji before a full-width sign.

  C.strcmp = function (a, b) {
    var i = 0, j = 0;
    while (i < a.length && j < b.length) {
      var x = a.codePointAt(i), y = b.codePointAt(j);
      if (x !== y) return x < y ? -1 : 1;
      i += x > 0xffff ? 2 : 1;
      j += y > 0xffff ? 2 : 1;
    }
    return (i < a.length) ? 1 : (j < b.length ? -1 : 0);
  };

  // padMakeSafe ( $input, $len ) for the messages: control characters as spaces, white space
  // folded to one - PHP's /\s+/u, the Unicode separators too, not U+FEFF - cut to $len bytes on
  // a whole character, trimmed.

  C.padMakeSafe = function (input, len) {
    if (len === undefined) len = 2048;
    var text = C.str(input).replace(/[\u0000-\u001F\u007F-\u009F]/g, ' ').replace(/[^\S\uFEFF]+/g, ' ');
    if (C.strlen(text) > len) {
      var cut = '', size = 0;
      for (var ch of text) {
        var n = C.strlen(ch);
        if (size + n > len) break;
        cut += ch; size += n;
      }
      text = cut;
    }
    return C.trim(text);
  };

  C.eq = function (a, b) { return C.cmp(a, b) === 0; };

  // max () and min () of a list, or of their arguments, as PHP compares: the value so far is
  // replaced when it compares above (min) or below (max) the next - so a NaN, which compares
  // above everything, is kept by max and taken by min as PHP does.

  C.max = function () {
    var list = arguments.length === 1 && C.isArray(arguments[0]) ? C.values(arguments[0]) : Array.prototype.slice.call(arguments);
    return list.reduce(function (m, v) { return C.cmp(m, v) < 0 ? v : m; });
  };

  C.min = function () {
    var list = arguments.length === 1 && C.isArray(arguments[0]) ? C.values(arguments[0]) : Array.prototype.slice.call(arguments);
    return list.reduce(function (m, v) { return C.cmp(m, v) > 0 ? v : m; });
  };

  // Math.min.apply ( null, list ) and Math.max.apply - NaN wins, -0 is below 0, an empty
  // list gives Infinity and -Infinity - without the arguments a long list overflows.

  C.lo = function (list) {
    var m = Infinity;
    for (var i = 0; i < list.length; i++) {
      var v = +list[i];
      if (v !== v) return NaN;
      if (v < m || (v === 0 && m === 0 && Object.is(v, -0))) m = v;
    }
    return m;
  };

  C.hi = function (list) {
    var m = -Infinity;
    for (var i = 0; i < list.length; i++) {
      var v = +list[i];
      if (v !== v) return NaN;
      if (v > m || (v === 0 && m === 0 && Object.is(m, -0))) m = v;
    }
    return m;
  };

  C.sum = function (list) { return C.values(list).reduce(function (s, v) { return s + C.float(v); }, 0); };

  C.end = function (list) { var v = C.values(list); return v.length ? v[v.length - 1] : false; };

  // array_column ( $rows, $column ) of lists or Maps.

  C.column = function (rows, column) {
    var out = [];
    C.values(rows).forEach(function (row) { var v = C.get(row, column); if (v !== null) out.push(v); });
    return out;
  };

  C.log10 = Math.log10;

  // -------------------------------------------------------------------------------------
  // The chart's tag: what the server handed over, in place of the PAD level
  // -------------------------------------------------------------------------------------

  C.spec = null;

  function ChartError(message) { this.message = message; this.name = 'ChartError'; }
  ChartError.prototype = Object.create(Error.prototype);
  C.ChartError = ChartError;

  // padError: the strict check of the server reports a mistake, and so does this - the chart
  // shows the message in its place. Without the check the drawing goes on, as on the server.

  C.padError = function (message) {
    if (C.spec && C.spec.check === false) return false;
    throw new ChartError(message);
  };

  C.padTagParm = function (name, dflt) {
    if (dflt === undefined) dflt = '';
    var params = (C.spec && C.spec.params) || {};
    return (Object.prototype.hasOwnProperty.call(params, name) && params[name] !== null) ? params[name] : dflt;
  };

  C.padChartGather = function () { return C.spec.rows; };
  C.padChartSeqName = function () { return C.spec.name || ''; };
  C.padChartWhat = function () { return C.spec.what || 'chart'; };
  C.padStyleNonce = function () { return C.spec.nonce || ''; };

  C.padChartSize = function (width, height) {
    return [Math.max(20, C.int(C.padTagParm('width', width))), Math.max(10, C.int(C.padTagParm('height', height)))];
  };

  C.padChartAttr = function (text) { return C.htmlspecialchars(C.str(text)); };

  C.padChartDedent = function (source) {
    var lines = C.trim(String(source).replace(/\r\n/g, '\n'), '\n\r').split('\n');
    var indent = Infinity;
    lines.forEach(function (line) {
      if (C.trim(line) !== '') indent = Math.min(indent, line.length - line.replace(/^[ \t]+/, '').length);
    });
    if (indent === Infinity || indent === 0) return lines.join('\n');
    return lines.map(function (line) { return line.slice(Math.min(indent, line.length - line.replace(/^[ \t]+/, '').length)); }).join('\n');
  };

  // -------------------------------------------------------------------------------------
  // chart.php
  // -------------------------------------------------------------------------------------

  C.padChartPlain = function (kind, rows, width, height) {

    var wide    = (kind === 'sparkline');
    var seqName = C.padChartSeqName();
    var label   = C.str(C.padTagParm('label'));
    var value   = C.str(C.padTagParm('value'));
    var name;

    if (seqName !== '') name = seqName;
    else if (value !== '') name = C.padChartFields(value).map(C.ucfirst).join(' and ') + (label !== '' ? ' by ' + label : '');
    else name = C.ucfirst(kind) + ' chart';

    var title = C.str(C.padTagParm('title', name));

    if (kind === 'pie' || kind === 'donut')
      return C.padChartPie(kind, C.padChartPoints(rows, label, value), title, width, height);

    var fields  = C.padChartFields(value);
    var stacked = (kind === 'bar' || kind === 'hbar') ? C.padTagParm('stacked', false) : false;
    stacked = (stacked !== false && stacked !== '' && stacked !== 0 && stacked !== '0');

    if (kind === 'hbar' || (!wide && fields.length > 1)) {
      var table = C.padChartTable(rows, label, fields);
      return C.padChartSeries(kind, table[0], table[1], title, width, height, stacked);
    }

    return C.padChart(kind, C.padChartPoints(rows, label, value, !wide), title, width, height);

  };

  C.padChartPoints = function (rows, label, value, axis) {

    if (axis === undefined) axis = true;

    var points = [];
    var index  = 0;

    C.entries(rows).forEach(function (entry) {

      var key = entry[0], row = entry[1];

      index++;

      if (!C.isArray(row)) {
        if (C.padChartFinite(row, axis))
          points.push([typeof key === 'string' ? key : String(index), C.num(row)]);
        return;
      }

      var v = null;

      if (value !== '')
        v = C.get(row, value);
      else
        C.entries(row).some(function (e) {
          if (!(typeof e[0] === 'string' && e[0] === label) && C.isNumeric(e[1])) { v = e[1]; return true; }
          return false;
        });

      if (!C.padChartFinite(v, axis)) return;

      var l = null;

      if (label !== '')
        l = C.get(row, label);
      else
        C.values(row).some(function (field) {
          if (C.isScalar(field) && !C.isNumeric(field)) { l = field; return true; }
          return false;
        });

      if (!C.isScalar(l))
        l = typeof key === 'string' ? key : index;

      points.push([C.str(l), C.num(v)]);

    });

    return points;

  };

  C.padChartFinite = function (value, axis) {
    if (axis === undefined) axis = true;
    return C.isNumeric(value) && isFinite(C.float(value)) && (!axis || Math.abs(C.float(value)) <= 1e300);
  };

  C.padChartNumber = function (n) {

    var f = C.float(n);

    if (!isFinite(f))
      return Number.isNaN(f) ? '' : (f > 0 ? '∞' : '-∞');

    if ((typeof n === 'number' && Number.isInteger(n) && Math.abs(n) < 9007199254740992) ||
        (typeof n === 'string' && /^[+-]?(0|[1-9][0-9]*)$/.test(C.trim(n)) && Math.abs(Number(C.trim(n))) <= 9223372036854775807))
      return C.intFormat(typeof n === 'number' ? BigInt(n) : BigInt(C.trim(n).replace(/^\+/, '')));

    n = f;

    var needed;
    for (needed = 0; needed < 15; needed++) {
      var scaled = n * C.pow10(needed);
      if (Math.abs(scaled - C.round(scaled)) <= Math.abs(scaled) * 16 * Number.EPSILON)
        break;
    }

    if (n !== 0 && Math.abs(n) < 1e-14)
      return C.g3(n);

    var decimals = (needed <= 3 || n === 0) ? needed : Math.min(needed, Math.max(2, 1 - C.int(Math.floor(Math.log10(Math.abs(n))))));

    return C.numberFormat(n, decimals, '.', ',');

  };

  C.padChartTicks = function (min, max, count) {

    if (count === undefined) count = 5;
    min = C.float(min); max = C.float(max);

    if (min !== max && Math.abs(max - min) <= Math.max(Math.abs(min), Math.abs(max)) * 1e-12)
      max = min;

    if (min === max) {
      if (max > 0) min = 0;
      else if (max < 0) max = 0;
      else max = 1;
    }

    var span = max - min;
    var step = Math.max(2.2250738585072014e-308, C.pow10(Math.floor(Math.log10(span / count))));

    [1, 2, 2.5, 5, 10].some(function (times) {
      if (span / (step * times) <= count) { step *= times; return true; }
      return false;
    });

    var low    = Math.floor(min / step) * step;
    var high   = Math.ceil(max / step) * step;
    var digits = Math.max(10, 10 - C.int(Math.floor(Math.log10(step))));
    var ticks  = [];

    for (var tick = low; tick <= high + step / 2 && ticks.length < 1000; tick += step)
      ticks.push(C.round(tick, digits));

    return ticks;

  };

  C.padChartXY = function (n) {
    var s = C.numberFormat(n, 1, '.', '');
    s = s.replace(/0+$/, '');
    return s.replace(/\.$/, '');
  };

  C.padChartStyle = function (more, kind) {

    var roles = [['series', '#2a78d6', '#3987e5'], ['text', '#52514e', '#c3c2b7'], ['grid', '#e4e3df', '#3a3a37'], ['surface', '#fcfcfb', '#1a1a19']];

    if (more)
      roles = roles.concat([['2', '#eb6834', '#d95926'], ['3', '#1baf7a', '#199e70'], ['4', '#eda100', '#c98500'], ['5', '#e87ba4', '#d55181'],
                            ['6', '#008300', '#008300'], ['7', '#4a3aa7', '#9085e9'], ['8', '#e34948', '#e66767'], ['other', '#898781', '#898781'],
                            ['heat-0', '#cde2fb', '#0d366b'], ['heat-1', '#9ec5f4', '#104281'], ['heat-2', '#6da7ec', '#184f95'],
                            ['heat-3', '#3987e5', '#1c5cab'], ['heat-4', '#256abf', '#2a78d6'], ['heat-5', '#184f95', '#5598e7'],
                            ['heat-6', '#0d366b', '#9ec5f4']]);

    var light = '', both = '';

    roles.forEach(function (r) {
      light += '--pad-chart-' + r[0] + ':' + r[1] + ';';
      both  += '--pad-chart-' + r[0] + ':light-dark(' + r[1] + ',' + r[2] + ');';
    });

    return '<style' + C.padStyleNonce() + '>'
         + ':where(.pad-chart){' + light + '}'
         + '@supports (color:light-dark(#000,#fff)){:where(.pad-chart){' + both + '}}'
         + '.pad-chart text{fill:var(--pad-chart-text);font:11px system-ui,sans-serif}'
         + '.pad-chart .pc-grid{stroke:var(--pad-chart-grid);stroke-width:1}'
         + '.pad-chart .pc-axis{stroke:var(--pad-chart-text);stroke-width:1}'
         + '.pad-chart .pc-bar{fill:var(--pad-chart-series)}'
         + '.pad-chart .pc-bar:hover{opacity:.8}'
         + '.pad-chart .pc-line{fill:none;stroke:var(--pad-chart-series);stroke-width:2;stroke-linejoin:round;stroke-linecap:round}'
         + '.pad-chart .pc-area{fill:var(--pad-chart-series);opacity:.1}'
         + '.pad-chart .pc-dot{fill:var(--pad-chart-series);stroke:var(--pad-chart-surface);stroke-width:2}'
         + '.pad-chart .pc-hit{fill:transparent}'
         + (more ? C.padChartStyleMore() + C.padChartStyleParts(kind || '') : '')
         + '</style>';

  };

  C.padChartStyleMore = function () {

    var css = ':where(.pad-chart){--pad-chart-1:var(--pad-chart-series)}';

    for (var slot = 1; slot <= 8; slot++)
      css += '.pad-chart .pc-c' + slot + '{fill:var(--pad-chart-' + slot + ')}'
           + '.pad-chart .pc-l' + slot + '{fill:none;stroke:var(--pad-chart-' + slot + ');stroke-width:2;stroke-linejoin:round;stroke-linecap:round}';

    for (var step = 0; step <= 6; step++)
      css += '.pad-chart .pc-h' + step + '{fill:var(--pad-chart-heat-' + step + ')}';

    return css
         + '.pad-chart .pc-co{fill:var(--pad-chart-other)}'
         + '.pad-chart .pc-mark:hover{opacity:.8}'
         + '.pad-chart .pc-gap{stroke:var(--pad-chart-surface);stroke-width:2}'
         + '.pad-chart .pc-seg{stroke:var(--pad-chart-surface);stroke-width:1}'
         + '.pad-chart .pc-ring{stroke:var(--pad-chart-surface);stroke-width:2}'
         + '.pad-chart .pc-frame{fill:none;stroke:var(--pad-chart-surface);stroke-width:4}'
         + '.pad-chart .pc-bubble{fill-opacity:.75}'
         + '.pad-chart .pc-dp1{opacity:.75}.pad-chart .pc-dp2{opacity:.55}.pad-chart .pc-dp3{opacity:.4}'
         + '.pad-chart .pc-link{fill:var(--pad-chart-series);opacity:.3}'
         + '.pad-chart .pc-link:hover{opacity:.55}'
         + '.pad-chart .pc-edge{stroke:var(--pad-chart-text);stroke-opacity:.35;fill:none}'
         + '.pad-chart .pc-arrow{fill:var(--pad-chart-text);fill-opacity:.5}'
         + '.pad-chart .pc-trend{fill:none;stroke:var(--pad-chart-text);stroke-width:1.5;stroke-dasharray:4 3}'
         + '.pad-chart .pc-in{fill:#fff;stroke:rgba(0,0,0,.55);stroke-width:2px;paint-order:stroke;stroke-linejoin:round}'
         + '.pad-chart .pc-big{font-size:16px;font-weight:600}';

  };

  C.padChartStyleParts = function (kind) {

    var rules = { gauge:    '.pad-chart .pc-track{fill:var(--pad-chart-grid)}.pad-chart .pc-band{opacity:.3}'
                          + '.pad-chart .pc-target{stroke:var(--pad-chart-text);stroke-width:2.5}',
                  radar:    '.pad-chart .pc-area-fill{fill-opacity:.15}.pad-chart .pc-tick{font-size:9px;opacity:.75}',
                  boxplot:  '.pad-chart .pc-whisker{stroke:var(--pad-chart-text);stroke-width:1.5}.pad-chart .pc-box{fill-opacity:.35}'
                          + '.pad-chart .pc-median{stroke:var(--pad-chart-text);stroke-width:2.5}',
                  calendar: '.pad-chart .pc-none{fill:var(--pad-chart-grid)}',
                  gantt:    '.pad-chart .pc-todo{opacity:.3}.pad-chart .pc-target{stroke:var(--pad-chart-text);stroke-width:2.5}' };

    return rules[kind] || '';

  };

  // The opening of the SVG. The id is the server's - made from the rows and the options, the
  // way padChartOpen makes one from what it draws - and a second chart opened while this one is
  // drawn gets a number behind it.

  C.padChartOpen = function (kind, hash, desc, title, width, height, more) {

    if (more === undefined) more = true;

    C.opened = (C.opened || 0) + 1;

    var id    = C.spec.id + (C.opened > 1 ? '-' + C.opened : '');
    var count = desc.length;

    if (count > 60)
      desc = desc.slice(0, 60).concat(['and ' + (count - 60) + ' more']);

    var svg = '<svg xmlns="http://www.w3.org/2000/svg" class="pad-chart pad-chart-' + kind + '" role="img"'
            + ' aria-labelledby="' + id + '-title ' + id + '-desc" width="' + C.str(width) + '" height="' + C.str(height) + '" viewBox="0 0 ' + C.str(width) + ' ' + C.str(height) + '">'
            + '<title id="' + id + '-title">' + C.padChartAttr(title) + '</title>'
            + '<desc id="' + id + '-desc">' + C.padChartAttr(desc.join('; ')) + '</desc>'
            + C.padChartStyle(more, kind);

    return [id, svg];

  };

  C.padChart = function (kind, points, title, width, height) {

    if (!points.length) return '';

    var n    = points.length;
    var vals = points.map(function (p) { return p[1]; });
    var desc = points.map(function (p) { return p[0] + ': ' + C.padChartNumber(p[1]); });

    var open = C.padChartOpen(kind, points, desc, title, width, height, false);
    var svg  = open[1];

    if (kind === 'sparkline')
      return svg + C.padChartSparkline(points, width, height) + '</svg>';

    var ticks = C.padChartTicks(Math.min(0, C.lo(vals)), Math.max(0, C.hi(vals)), height < 200 ? 3 : 5);
    var low   = ticks[0];
    var high  = ticks[ticks.length - 1];

    var tickWidth = 0;
    ticks.forEach(function (tick) { tickWidth = Math.max(tickWidth, C.strlen(C.padChartNumber(tick))); });

    var left   = Math.max(24, C.int(Math.ceil(tickWidth * 6.5)) + 10);
    var right  = 10;
    var top    = 10;
    var bottom = 24;
    var plotW  = width  - left - right;
    var plotH  = height - top  - bottom;
    var band   = plotW / n;

    var y = function (v) { return top + (high - v) / (high - low) * plotH; };
    var x = function (i) { return left + (i + 0.5) * band; };

    ticks.forEach(function (tick) {
      svg += '<line class="pc-grid" x1="' + C.str(left) + '" x2="' + C.str(width - right) + '" y1="' + C.padChartXY(y(tick)) + '" y2="' + C.padChartXY(y(tick)) + '"/>'
           + '<text x="' + C.str(left - 6) + '" y="' + C.padChartXY(y(tick) + 4) + '" text-anchor="end">' + C.padChartNumber(tick) + '</text>';
    });

    var labelWidth = 0;
    points.forEach(function (p) { labelWidth = Math.max(labelWidth, C.mbStrlen(p[0]) * 6.5 + 8); });

    var every = Math.max(1, C.int(Math.ceil(Math.min(labelWidth, 73) / Math.max(1, band))));

    points.forEach(function (p, i) {
      if (i % every === 0)
        svg += '<text x="' + C.padChartXY(x(i)) + '" y="' + C.str(height - 8) + '" text-anchor="middle">' + C.padChartAttr(C.padChartFit(p[0], band * every - 8)) + '</text>';
    });

    var base = y(0);

    if (kind === 'bar') {

      var barW = Math.max(1, Math.min(24, band * 0.7));

      points.forEach(function (p, i) {

        var x0  = x(i) - barW / 2;
        var x1  = x0 + barW;
        var end = y(p[1]);
        var r   = Math.min(4, barW / 2, Math.abs(base - end));
        var dir = (end <= base) ? 1 : -1;

        var d = 'M' + C.padChartXY(x0) + ',' + C.padChartXY(base)
              + 'V' + C.padChartXY(end + dir * r)
              + 'Q' + C.padChartXY(x0) + ',' + C.padChartXY(end) + ' ' + C.padChartXY(x0 + r) + ',' + C.padChartXY(end)
              + 'H' + C.padChartXY(x1 - r)
              + 'Q' + C.padChartXY(x1) + ',' + C.padChartXY(end) + ' ' + C.padChartXY(x1) + ',' + C.padChartXY(end + dir * r)
              + 'V' + C.padChartXY(base) + 'Z';

        svg += '<path class="pc-bar" d="' + d + '"><title>' + C.padChartAttr(p[0] + ': ' + C.padChartNumber(p[1])) + '</title></path>';

      });

    } else {

      var line = '';
      points.forEach(function (p, i) { line += (i ? 'L' : 'M') + C.padChartXY(x(i)) + ',' + C.padChartXY(y(p[1])); });

      svg += '<path class="pc-area" d="' + line + 'L' + C.padChartXY(x(n - 1)) + ',' + C.padChartXY(base) + 'L' + C.padChartXY(x(0)) + ',' + C.padChartXY(base) + 'Z"/>'
           + '<path class="pc-line" d="' + line + '"/>'
           + '<circle class="pc-dot" cx="' + C.padChartXY(x(n - 1)) + '" cy="' + C.padChartXY(y(points[n - 1][1])) + '" r="4"/>';

      var hit = C.padChartXY(Math.max(8, Math.min(20, band / 2)));

      points.forEach(function (p, i) {
        svg += '<circle class="pc-hit" cx="' + C.padChartXY(x(i)) + '" cy="' + C.padChartXY(y(p[1])) + '" r="' + hit + '"><title>'
             + C.padChartAttr(p[0] + ': ' + C.padChartNumber(p[1])) + '</title></circle>';
      });

    }

    svg += '<line class="pc-axis" x1="' + C.str(left) + '" x2="' + C.str(width - right) + '" y1="' + C.padChartXY(base) + '" y2="' + C.padChartXY(base) + '"/>';

    return svg + '</svg>';

  };

  C.padChartSparkline = function (points, width, height) {

    var vals = points.map(function (p) { return p[1]; });
    var min  = C.lo(vals);
    var max  = C.hi(vals);
    var n    = points.length;
    var pad  = 5;

    var x    = function (i) { return n === 1 ? width / 2 : pad + i * (width - 2 * pad) / (n - 1); };
    var span = max - min;
    var half = max / 2 - min / 2;
    var y;

    if (isFinite(span) && span !== 0) y = function (v) { return pad + (max - v) / span * (height - 2 * pad); };
    else if (!isFinite(span) && half !== 0) y = function (v) { return pad + (max / 2 - v / 2) / half * (height - 2 * pad); };
    else y = function () { return height / 2; };

    var line = '';
    points.forEach(function (p, i) { line += (i ? 'L' : 'M') + C.padChartXY(x(i)) + ',' + C.padChartXY(y(p[1])); });

    return '<path class="pc-line" d="' + line + '"/>'
         + '<circle class="pc-dot" cx="' + C.padChartXY(x(n - 1)) + '" cy="' + C.padChartXY(y(points[n - 1][1])) + '" r="3"/>';

  };

  // -------------------------------------------------------------------------------------
  // parts.php
  // -------------------------------------------------------------------------------------

  C.padChartFields = function (list) {
    var fields = [];
    C.str(list).split(',').forEach(function (field) {
      field = C.trim(field);
      if (field !== '') fields.push(field);
    });
    return fields;
  };

  C.padChartRow = function (row) {
    return C.isArray(row) ? row : new Map([['value', row]]);
  };

  C.padChartGuess = function (rows, sparse) {
    if (sparse) {
      var kinds = new Map(), all = C.values(rows);
      for (var i = 0; i < all.length; i++) {
        C.entries(C.padChartRow(all[i])).forEach(function (e) {
          var key = C.str(e[0]), v = e[1];
          if (kinds.has(key) && kinds.get(key) !== null) return;
          kinds.set(key, (v === null || v === undefined || (typeof v === 'string' && C.trim(v) === '')) ? null
                         : (C.isNumeric(v) ? 'number' : (C.isScalar(v) ? 'name' : 'other')));
        });
        if (!Array.from(kinds.values()).some(function (k) { return k === null; }))
          break;
      }
      var nums = [], strs = [];
      kinds.forEach(function (kind, key) {
        if (kind === 'number') nums.push(key);
        else if (kind === 'name') strs.push(key);
      });
      return [nums, strs];
    }
    var first = C.values(rows)[0];
    if (first === undefined) return [[], []];
    var row = C.padChartRow(first), numbers = [], names = [];
    C.entries(row).forEach(function (e) {
      if (C.isNumeric(e[1])) numbers.push(C.str(e[0]));
      else if (C.isScalar(e[1])) names.push(C.str(e[0]));
    });
    return [numbers, names];
  };

  // padChartPick ( $option, $guessed, &$used ): $used is the list it adds to.

  C.padChartPick = function (option, guessed, used) {
    var field = C.str(C.padTagParm(option));
    var label = (option === 'label') ? '' : C.str(C.padTagParm('label'));
    if (field === '')
      guessed.some(function (name) {
        if (used.indexOf(name) < 0 && name !== label) { field = name; return true; }
        return false;
      });
    if (field !== '') used.push(field);
    return field;
  };

  // padChartFieldSomewhere: whether a field is one of some row's, with a value or without one.

  C.padChartFieldSomewhere = function (rows, field) {
    var key = C.key(field);
    return C.values(rows).some(function (row) {
      return C.keys(C.padChartRow(row)).some(function (k) { return k === key; });
    });
  };

  C.padChartText = function (row, field) {
    var value = (field !== '') ? C.get(row, field, '') : '';
    return C.isScalar(value) ? C.str(value) : '';
  };

  C.padChartTable = function (rows, label, fields) {

    if (fields.length <= 1) {
      var points = C.padChartPoints(rows, label, fields[0] === undefined ? '' : fields[0]);
      var one = new Map();
      one.set(C.key(C.ucfirst(fields[0] === undefined ? 'value' : fields[0])), points.map(function (p) { return p[1]; }));
      return [points.map(function (p) { return p[0]; }), one];
    }

    var labels = [];
    var series = new Map();
    fields.forEach(function (f) { series.set(C.key(C.ucfirst(f)), []); });
    var index = 0;

    C.entries(rows).forEach(function (entry) {

      var key = entry[0];
      index++;
      var row = C.padChartRow(entry[1]);
      var values = [];

      for (var f = 0; f < fields.length; f++) {
        var value = C.get(row, fields[f]);
        if (!C.padChartFinite(value)) return;
        values.push(C.num(value));
      }

      var text = '';

      if (label !== '') text = C.padChartText(row, label);
      else
        C.values(row).some(function (field) {
          if (C.isScalar(field) && !C.isNumeric(field)) { text = C.str(field); return true; }
          return false;
        });

      labels.push(text !== '' ? text : (typeof key === 'string' ? key : String(index)));

      Array.from(series.keys()).forEach(function (name, i) { series.get(name).push(values[i]); });

    });

    return [labels, series];

  };

  C.padChartSlot = function (index) { return index < 8 ? 'pc-c' + (index + 1) : 'pc-co'; };

  C.padChartWidth = function (text) { return C.mbStrlen(C.str(text)) * 6.5; };

  C.padChartFit = function (text, width) {
    text = C.str(text);
    var max = C.int(Math.floor(width / 6.5));
    if (C.mbStrlen(text) <= max) return text;
    return max < 2 ? '' : C.mbSubstr(text, 0, max - 1) + '…';
  };

  C.padChartLegend = function (names, x, y, width) {

    var svg = '', left = x, top = y;

    C.values(names).forEach(function (name, i) {

      var w = C.padChartWidth(name) + 26;

      if (left > x && left + w > x + width) { left = x; top += 16; }

      var shown = C.padChartFit(name, x + width - left - 14);
      var whole = (shown !== C.str(name)) ? '<title>' + C.padChartAttr(name) + '</title>' : '';

      svg += '<rect class="' + C.padChartSlot(i) + '" x="' + C.padChartXY(left) + '" y="' + C.padChartXY(top) + '" width="10" height="10" rx="2"/>'
           + '<text x="' + C.padChartXY(left + 14) + '" y="' + C.padChartXY(top + 9) + '">' + whole + C.padChartAttr(shown) + '</text>';

      left += w;

    });

    return ['<g class="pc-key">' + svg + '</g>', top - y + 16];

  };

  C.padChartBarPath = function (from, to, base, end, vertical) {

    if (vertical === undefined) vertical = true;

    var r = Math.min(4, (to - from) / 2, Math.abs(base - end));
    var dir;

    if (vertical) {
      dir = (end <= base) ? 1 : -1;
      return 'M' + C.padChartXY(from) + ',' + C.padChartXY(base)
           + 'V' + C.padChartXY(end + dir * r)
           + 'Q' + C.padChartXY(from) + ',' + C.padChartXY(end) + ' ' + C.padChartXY(from + r) + ',' + C.padChartXY(end)
           + 'H' + C.padChartXY(to - r)
           + 'Q' + C.padChartXY(to) + ',' + C.padChartXY(end) + ' ' + C.padChartXY(to) + ',' + C.padChartXY(end + dir * r)
           + 'V' + C.padChartXY(base) + 'Z';
    }

    dir = (end >= base) ? -1 : 1;

    return 'M' + C.padChartXY(base) + ',' + C.padChartXY(from)
         + 'H' + C.padChartXY(end + dir * r)
         + 'Q' + C.padChartXY(end) + ',' + C.padChartXY(from) + ' ' + C.padChartXY(end) + ',' + C.padChartXY(from + r)
         + 'V' + C.padChartXY(to - r)
         + 'Q' + C.padChartXY(end) + ',' + C.padChartXY(to) + ' ' + C.padChartXY(end + dir * r) + ',' + C.padChartXY(to)
         + 'H' + C.padChartXY(base) + 'Z';

  };

  C.padChartArc = function (cx, cy, inner, outer, a0, a1) {

    var at = function (a, r) { return C.padChartXY(cx + r * Math.sin(a)) + ',' + C.padChartXY(cy - r * Math.cos(a)); };
    var o = C.padChartXY(outer);
    var i = C.padChartXY(inner);
    var d;

    if (a1 - a0 >= 2 * Math.PI - 1e-9) {
      d = 'M' + at(0, outer) + 'A' + o + ',' + o + ' 0 1 1 ' + at(Math.PI, outer) + 'A' + o + ',' + o + ' 0 1 1 ' + at(0, outer) + 'Z';
      if (inner > 0)
        d += 'M' + at(0, inner) + 'A' + i + ',' + i + ' 0 1 0 ' + at(Math.PI, inner) + 'A' + i + ',' + i + ' 0 1 0 ' + at(0, inner) + 'Z';
      return d;
    }

    var large = (a1 - a0 > Math.PI) ? 1 : 0;

    if (inner <= 0)
      return 'M' + C.padChartXY(cx) + ',' + C.padChartXY(cy) + 'L' + at(a0, outer) + 'A' + o + ',' + o + ' 0 ' + large + ' 1 ' + at(a1, outer) + 'Z';

    return 'M' + at(a0, outer) + 'A' + o + ',' + o + ' 0 ' + large + ' 1 ' + at(a1, outer)
         + 'L' + at(a1, inner) + 'A' + i + ',' + i + ' 0 ' + large + ' 0 ' + at(a0, inner) + 'Z';

  };

  // -------------------------------------------------------------------------------------
  // The kinds - subs/charts/types/, a line each - and the drawing of one chart
  // -------------------------------------------------------------------------------------

  C.types = {
    slope: function () { return C.padChartSlope(C.padChartGather(), ...C.padChartSize(600,300)); },
    pareto: function () { return C.padChartPareto(C.padChartGather(), ...C.padChartSize(600,300)); },
    arc:          function () { return C.padChartArcDiagram(C.padChartGather(), ...C.padChartSize(600, 300)); },
    area:         function () { return C.padChartArea('area', C.padChartGather(), ...C.padChartSize(600, 300)); },
    bar:          function () { return C.padChartPlain('bar', C.padChartGather(), ...C.padChartSize(600, 300)); },
    boxplot:      function () { return C.padChartBoxplot(C.padChartGather(), ...C.padChartSize(600, 300)); },
    bubble:       function () { return C.padChartScatter('bubble', C.padChartGather(), C.padChartSeqName(), ...C.padChartSize(600, 300)); },
    bullet:       function () { return C.padChartBullet(C.padChartGather(), ...C.padChartSize(600, 300)); },
    calendar:     function () { return C.padChartCalendar(C.padChartGather(), ...C.padChartSize(720, 150)); },
    candlestick:  function () { return C.padChartCandlestick(C.padChartGather(), ...C.padChartSize(600, 300)); },
    chord:        function () { return C.padChartChord(C.padChartGather(), ...C.padChartSize(600, 400)); },
    column:       function () { return C.padChartPlain('bar', C.padChartGather(), ...C.padChartSize(600, 300)); },
    density:      function () { return C.padChartDensity('density', C.padChartGather(), ...C.padChartSize(600, 300)); },
    donut:        function () { return C.padChartPlain('donut', C.padChartGather(), ...C.padChartSize(480, 280)); },
    dumbbell:     function () { return C.padChartDumbbell(C.padChartGather(), ...C.padChartSize(600, 300)); },
    dual:         function () { return C.padChartDual(C.padChartGather(), ...C.padChartSize(600, 300)); },
    ecdf:         function () { return C.padChartEcdf(C.padChartGather(), ...C.padChartSize(600, 300)); },
    errorbar:     function () { return C.padChartErrorbar(C.padChartGather(), ...C.padChartSize(600, 300)); },
    funnel:       function () { return C.padChartFunnel('funnel', C.padChartGather(), ...C.padChartSize(600, 300)); },
    gantt:        function () { return C.padChartGantt(C.padChartGather(), ...C.padChartSize(600, 300)); },
    gauge:        function () { return C.padChartGauge(C.padChartGather(), ...C.padChartSize(320, 200)); },
    hbar:         function () { return C.padChartPlain('hbar', C.padChartGather(), ...C.padChartSize(600, 300)); },
    heatmap:      function () { return C.padChartHeatmap(C.padChartGather(), ...C.padChartSize(600, 300)); },
    hexbin:       function () { return C.padChartHexbin(C.padChartGather(), ...C.padChartSize(600, 400)); },
    histogram:    function () { return C.padChartHistogram(C.padChartGather(), ...C.padChartSize(600, 300)); },
    likert:       function () { return C.padChartLikert(C.padChartGather(), ...C.padChartSize(600, 300)); },
    line:         function () { return C.padChartPlain('line', C.padChartGather(), ...C.padChartSize(600, 300)); },
    marimekko:    function () { return C.padChartMarimekko(C.padChartGather(), ...C.padChartSize(600, 300)); },
    multiples:    function () { return C.padChartMultiples(C.padChartGather(), ...C.padChartSize(600, 400)); },
    network:      function () { return C.padChartNetwork(C.padChartGather(), ...C.padChartSize(600, 400)); },
    orgchart:     function () { return C.padChartOrg(C.padChartGather(), ...C.padChartSize(600, 400)); },
    pack:         function () { return C.padChartPack(C.padChartGather(), ...C.padChartSize(600, 400)); },
    parallel:     function () { return C.padChartParallel(C.padChartGather(), ...C.padChartSize(600, 400)); },
    parliament:   function () { return C.padChartParliament(C.padChartGather(), ...C.padChartSize(480, 280)); },
    pie:          function () { return C.padChartPlain('pie', C.padChartGather(), ...C.padChartSize(480, 280)); },
    proportional: function () { return C.padChartProportional(C.padChartGather(), ...C.padChartSize(600, 300)); },
    pyramid:      function () { return C.padChartFunnel('pyramid', C.padChartGather(), ...C.padChartSize(600, 300)); },
    radar:        function () { return C.padChartRadar(C.padChartGather(), ...C.padChartSize(600, 400)); },
    radialbar:    function () { return C.padChartRadial('radialbar', C.padChartGather(), ...C.padChartSize(480, 280)); },
    ridgeline:    function () { return C.padChartRidgeline(C.padChartGather(), ...C.padChartSize(600, 400)); },
    rings:        function () { return C.padChartRings(C.padChartGather(), ...C.padChartSize(480, 280)); },
    rose:         function () { return C.padChartRadial('rose', C.padChartGather(), ...C.padChartSize(480, 280)); },
    sankey:       function () { return C.padChartSankey(C.padChartGather(), ...C.padChartSize(600, 400)); },
    scatter:      function () { return C.padChartScatter('scatter', C.padChartGather(), C.padChartSeqName(), ...C.padChartSize(600, 300)); },
    sparkline:    function () { return C.padChartPlain('sparkline', C.padChartGather(), ...C.padChartSize(120, 32)); },
    spiral:       function () { return C.padChartSpiral(C.padChartGather(), ...C.padChartSize(480, 420)); },
    stream:       function () { return C.padChartArea('stream', C.padChartGather(), ...C.padChartSize(600, 300)); },
    sunburst:     function () { return C.padChartHierarchy('sunburst', C.padChartGather(), ...C.padChartSize(480, 280)); },
    treemap:      function () { return C.padChartHierarchy('treemap', C.padChartGather(), ...C.padChartSize(600, 400)); },
    venn:         function () { return C.padChartVenn(C.padChartGather(), ...C.padChartSize(480, 280)); },
    violin:       function () { return C.padChartDensity('violin', C.padChartGather(), ...C.padChartSize(600, 300)); },
    waffle:       function () { return C.padChartWaffle(C.padChartGather(), ...C.padChartSize(480, 280)); },
    waterfall:    function () { return C.padChartWaterfall(C.padChartGather(), ...C.padChartSize(600, 300)); }
  };

  // The SVG of one chart, from what the server handed over: { kind, id, rows, params, name,
  // what, nonce, check, zone }. '' for a chart without points, as on the server.

  C.render = function (spec) {
    var before = C.spec, opened = C.opened;
    C.spec   = { kind: spec.kind, id: spec.id, rows: C.decode(spec.rows), params: C.decode(spec.params || {}),
                 name: spec.name || '', what: spec.what || 'chart', nonce: spec.nonce || '',
                 check: spec.check !== false, zone: spec.zone || 'UTC' };
    C.opened = 0;
    try {
      var draw = C.types[spec.kind];
      if (!draw) throw new ChartError("the chart has no kind named '" + spec.kind + "'");
      return C.str(draw());
    } finally {
      C.spec   = before;
      C.opened = opened;
    }
  };

  // A span of the page: the SVG takes its place, or - a mistake the strict check reports on
  // the server - a line saying what it is.

  C.mount = function (el) {
    if (!el || !el.getAttribute || el.getAttribute('data-pad-chart') === null) return;
    var svg;
    try {
      svg = C.render(JSON.parse(el.getAttribute('data-pad-chart')));
    } catch (e) {
      if (window.console) console.error('PAD chart:', e);
      var note = document.createElement('span');
      note.className = 'pad-chart-error';
      note.textContent = 'PAD: ' + (e && e.message ? e.message : String(e));
      el.replaceWith(note);
      return;
    }
    if (svg === '') { el.remove(); return; }
    var holder = document.createElement('span');
    holder.innerHTML = svg;
    el.replaceWith.apply(el, Array.from(holder.childNodes));
  };

  C.mountAll = function (root) {
    Array.from((root || document).querySelectorAll('span[data-pad-chart]')).forEach(C.mount);
  };

  if (typeof document !== 'undefined')
    document.addEventListener('DOMContentLoaded', function () { C.mountAll(); });

})(typeof window !== 'undefined' ? (window.PadChart = window.PadChart || {}) : (globalThis.PadChart = globalThis.PadChart || {}));
