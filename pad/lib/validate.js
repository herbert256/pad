// The browser's half of padValidate (lib/form.php): the same rules, the same messages, so
// a visitor sees what the server would say before the form leaves - and the server still
// checks every post. PAD writes this file into the page as one inline script, once:
// behind the first {form ..., client}, or where {validator} stands.
//
//   padValidate.check(rules, values)   { field: message } for the fields that broke a rule,
//                                      rules as padValidateClient() exports them, values
//                                      a plain object (a list for a field named tags[])
//   padValidate.field(spec, value, values, label)
//                                      the message of one field, '' when it holds
//   padValidate.form(form)             checks a <form data-pad-rules> and shows the
//                                      messages the way {input} does; true when it holds
//
// Every form with data-pad-rules is checked when it is sent, and a field again when it is
// left or - once it showed a message - while it is typed in. Its values are read as the
// post would carry them (FormData): the last value of a name, every value of name[].
//
// A rule the browser cannot judge as PHP does - a regex PCRE writes another way than
// JavaScript - passes here and is left to the server.

(function () {

  'use strict';

  if (window.padValidate) return;

  var numberPattern = /^[+-]?(\d+(\.\d*)?|\.\d+)([eE][+-]?\d+)?$/;

  // The pattern of PHP's FILTER_VALIDATE_EMAIL (ext/filter), as it is: a pattern of our own
  // took a@b.123 and a@1.2.3.4, which PHP refuses, and refused user@[127.0.0.1], which it
  // takes - a form the server would accept could not be sent.
  var emailPattern = /^(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){255,})(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){65,}@)(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22))(?:\.(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-+[a-z0-9]+)*\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-+[a-z0-9]+)*)|(?:\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\]))$/i;

  // PHP's trim: space, tab, the line ends, NUL and vertical tab - not the no-break space and
  // the other spaces of Unicode that String.prototype.trim takes too, which refused a value
  // of one no-break space as empty while the server took it.
  function trim(value) {
    return typeof value === 'string' ? value.replace(/^[ \t\n\r\0\x0B]+|[ \t\n\r\0\x0B]+$/g, '') : value;
  }

  function empty(value) {
    return value === '' || value === null || value === undefined || (Array.isArray(value) && value.length === 0);
  }

  // PHP's (float) of a text: the number its start spells, 0 when it spells none.
  function toNumber(value) {
    var found = String(value).match(/^\s*[+-]?(\d+(\.\d*)?|\.\d+)([eE][+-]?\d+)?/);
    return found ? parseFloat(found[0]) : 0;
  }

  // mb_strlen counts characters, not the UTF-16 units of .length.
  function length(value) {
    return Array.from(String(value)).length;
  }

  function email(value) {
    return value.length <= 320 && emailPattern.test(value);
  }

  // FILTER_VALIDATE_INT: a whole number PHP's integers hold - past them it is no number to
  // the server, and integer|max:10 said "at most 10" here, "a whole number" there.
  function integer(value) {
    var found = value.match(/^([+-]?)(0|[1-9]\d*)$/);
    if (!found) return false;
    var limit = found[1] === '-' ? '9223372036854775808' : '9223372036854775807';
    return found[2].length < limit.length || (found[2].length === limit.length && found[2] <= limit);
  }

  // FILTER_VALIDATE_URL of an http or https address, step by step as PHP takes it: every
  // character one a URL may hold (php_filter_url drops the others, and a dropped one fails
  // it); the authority parted as parse_url parts it - the user info up to its last @, a port
  // of up to five characters after the last :, read as strtol reads it; the host a name of
  // letters, digits and hyphens, a dot only between two of them, or an IPv6 address in
  // brackets; the user info of the characters RFC 3986 allows. The URL parser of the
  // browser converted what PHP refuses - http://exämple.com, ex%41mple.com - and refused
  // what it takes: https://999.2.3.4.
  function url(value) {
    if (!/^https?:\/\//i.test(value) || /[^A-Za-z0-9$\-_.+!*'(),{}|\\^~\[\]`<>#%";\/?:@&=]/.test(value)) return false;
    var rest = value.replace(/^https?:\/\//i, '');
    var authority = rest.slice(0, rest.search(/[\/?#]|$/));
    var at = authority.lastIndexOf('@');
    var host = authority.slice(at + 1);
    if (!(host.charAt(0) === '[' && host.slice(-1) === ']')) {
      var colon = host.lastIndexOf(':');
      if (colon >= 0) {
        var port = host.slice(colon + 1);
        if (port.length > 5) return false;
        if (port !== '') {
          var number = port.match(/^[+-]?\d+/);
          if (!number || +number[0] < 0 || +number[0] > 65535) return false;
        }
        host = host.slice(0, colon);
      }
    }
    if (host === '') return false;
    if (!(host.charAt(0) === '[' && host.slice(-1) === ']' && ipv6(host.slice(1, -1))) && !hostname(host)) return false;
    if (at < 0) return true;
    var info = authority.slice(0, at), split = info.indexOf(':');
    return userinfo(split < 0 ? info : info.slice(0, split)) && (split < 0 || userinfo(info.slice(split + 1)));
  }

  function alnum(c) {
    return /^[A-Za-z0-9]$/.test(c);
  }

  // _php_filter_validate_domain for a host name: a trailing dot left out, at most 253
  // characters, a letter or digit first, no hyphen last, a label at most 63 long.
  function hostname(host) {
    var end = host.length;
    if (end > 0 && host.charAt(end - 1) === '.') end--;
    if (end > 253 || !alnum(host.charAt(0)) || host.slice(-1) === '-') return false;
    for (var i = 0, size = 1; i < end; i++) {
      var c = host.charAt(i);
      if (c === '.') {
        if (host.charAt(i + 1) === '.' || !alnum(host.charAt(i - 1)) || !alnum(host.charAt(i + 1))) return false;
        size = 1;
      } else {
        if (size > 63 || (c !== '-' && !alnum(c))) return false;
        size++;
      }
    }
    return true;
  }

  function ipv4(text) {
    var parts = text.split('.');
    return parts.length === 4 && parts.every(function (one) { return /^(0|[1-9]\d{0,2})$/.test(one) && +one <= 255; });
  }

  // _php_filter_validate_ipv6: groups of one to four hex digits, one :: at most, an IPv4
  // address as the last two groups.
  function ipv6(text) {
    if (text.indexOf(':') < 0) return false;
    var blocks = 0, compressed = false, at = 0, end = text.length;
    var dot = text.indexOf('.');
    if (dot >= 0) {
      var start = text.lastIndexOf(':', dot) + 1;
      if (!ipv4(text.slice(start))) return false;
      end = start;
      if (end < 2) return false;
      if (text.charAt(end - 2) !== ':') end--;
      blocks = 2;
    }
    while (at < end) {
      if (text.charAt(at) === ':') {
        if (++at >= end) return false;
        if (text.charAt(at) === ':') {
          if (compressed) return false;
          compressed = true;
          blocks++;
          if (++at === end) return blocks <= 8;
        } else if (at - 1 === 0) return false;
      }
      var digits = text.slice(at, end).match(/^[0-9A-Fa-f]*/)[0].length;
      at += digits;
      if (digits < 1 || digits > 4 || ++blocks > 8) return false;
    }
    return (compressed && blocks <= 8) || blocks === 8;
  }

  // is_userinfo_valid: a %-escape there starts with a digit.
  function userinfo(text) {
    return /^(?:[A-Za-z0-9\-._~!$&'()*+,;=:]|%[0-9][0-9A-Fa-f])*$/.test(text);
  }

  // padValidateDate takes what date_parse reads as a day of a month of a year - more forms
  // than can be told alike here. The ISO day, with a time to 24:59:60, and the day written
  // with / . or - are judged; a text without a digit is no date; any other passes here and
  // is left to the server - Date.parse refused what date_parse takes (2026-W42, 20260105),
  // took what it refuses, and reads it another way in every browser.
  function date(value) {
    var iso = value.match(/^(\d{4})-(\d{1,2})-(\d{1,2})(?:[Tt ](\d{1,2}):(\d{2})(?::(\d{2}))?)?$/);
    if (iso) {
      if (iso[4] !== undefined && (+iso[4] > 24 || +iso[5] > 59 || +(iso[6] || 0) > 60)) return false;
      return calendarDay(+iso[1], +iso[2], +iso[3]);
    }
    var dmy = value.match(/^(\d{1,2})[\/.-](\d{1,2})[\/.-](\d{4})$/);
    if (dmy) return value.charAt(dmy[1].length) === '/' ? calendarDay(+dmy[3], +dmy[1], +dmy[2]) : calendarDay(+dmy[3], +dmy[2], +dmy[1]);
    return /\d/.test(value);
  }

  // checkdate: Date.UTC took a year of 0 to 99 for 1900 to 1999, and 0099-12-31 was no date.
  function calendarDay(year, month, day) {
    var moment = new Date(0);
    moment.setUTCFullYear(year, month - 1, day);
    return year > 0 && moment.getUTCFullYear() === year && moment.getUTCMonth() === month - 1 && moment.getUTCDate() === day;
  }

  // A PCRE pattern as JavaScript knows it: its delimiters taken off, the flags JavaScript
  // has kept. null when it cannot be read, or JavaScript would read it another way - the
  // server judges that one alone: an escape of PCRE's own (\A, \z, \R, \x{41} ...) is a
  // plain letter here, [[:alnum:]] a set of its characters, \p a p without u, and under u
  // PHP takes \w, \d and \b of every script, JavaScript of ASCII alone - José was refused.
  // Without u PCRE reads the bytes of a value: one beyond ASCII is the server's too.
  function regex(pattern) {
    var open = pattern.charAt(0);
    var close = { '(': ')', '{': '}', '[': ']', '<': '>' }[open] || open;
    var end = pattern.lastIndexOf(close);
    if (end <= 0 || /[A-Za-z0-9\\\s]/.test(open)) return null;
    var flags = pattern.slice(end + 1);
    if (/[^imsuxXUAD]/.test(flags) || /[xXUAD]/.test(flags)) return null;
    var body = pattern.slice(1, end);
    var bare = body.replace(/\\\\/g, '');
    if (/\\([AzZQERhHKGeNXCagoLlUuvV]|x\{)/.test(bare) || /\[:\^?[a-z]+:\]/.test(bare)) return null;
    if (flags.indexOf('u') < 0 ? /\\[pP]/.test(bare) : /\\[wWdDbB]/.test(bare)) return null;
    try { return new RegExp(body, flags.replace(/[^imsu]/g, '')); } catch (e) { return null; }
  }

  function rule(name, arg, value, values, number) {
    if (Array.isArray(value)) {
      if (name === 'required') return value.length > 0;
      if (name === 'in') return value.every(function (one) { return typeof one === 'string' && rule('in', arg, trim(one), values, number); });
      return false;
    }
    value = String(value);
    var size = number ? toNumber(value) : length(value);
    switch (name) {
      case 'required': return value !== '';
      case 'accepted': return ['1', 'on', 'yes', 'true'].indexOf(value.toLowerCase()) >= 0;
      case 'email':    return email(value);
      case 'url':      return url(value);
      case 'numeric':  return numberPattern.test(value);
      case 'integer':  return integer(value);
      case 'min':      return size >= toNumber(arg);
      case 'max':      return size <= toNumber(arg);
      case 'in':       return arg.split(',').map(trim).indexOf(value) >= 0;
      case 'regex':    var pattern = regex(arg); return pattern && (pattern.unicode || !/[^\x00-\x7f]/.test(value)) ? pattern.test(value) : true;
      case 'same':     var other = values[arg]; return typeof other !== 'object' && value === trim(String(other === undefined ? '' : other));
      case 'date':     return date(value);
    }
    return true;
  }

  function message(text, label, shown) {
    return text.replace(/:label|:n/g, function (token) { return token === ':label' ? label : shown; });
  }

  // One field: the message of the first rule it breaks, as padValidate takes them - an empty
  // value is judged by required and accepted alone, a list where one value belongs is
  // refused whatever the rules say.
  function field(spec, value, values, label) {
    value = trim(value === undefined || value === null ? '' : value);
    label = label || spec.label;
    if (spec.rules.length && Array.isArray(value) && !spec.list) return message(spec.single, label, '');
    for (var i = 0; i < spec.rules.length; i++) {
      var one = spec.rules[i];
      if (empty(value) && one[0] !== 'required' && one[0] !== 'accepted') continue;
      if (!rule(one[0], one[1], value, values, spec.number)) return message(one[2], label, one[3]);
    }
    return '';
  }

  function check(rules, values) {
    var errors = {};
    Object.keys(rules || {}).forEach(function (name) {
      var problem = field(rules[name], values[name], values);
      if (problem) errors[name] = problem;
    });
    return errors;
  }

  // ---------------------------------------------------------------- forms in the page

  function rulesOf(form) {
    try { return JSON.parse(form.getAttribute('data-pad-rules') || '{}'); } catch (e) { return {}; }
  }

  // Every field of the form, as the post carries it - the other field of same: has no rules
  // of its own, and read as empty it refused every confirmation that matched.
  function valuesOf(form, rules) {
    var data = new FormData(form);
    var values = {};
    data.forEach(function (one, name) {
      if (typeof one === 'string') values[name] = one;
    });
    Object.keys(rules).forEach(function (name) {
      var all = data.getAll(name).filter(function (one) { return typeof one === 'string'; });
      values[name] = rules[name].list ? all : (all.length ? all[all.length - 1] : '');
    });
    return values;
  }

  function controlsOf(form, name) {
    return Array.prototype.filter.call(form.elements, function (one) { return one.name === name; });
  }

  // The text of the field's <label>, made fit for a sentence as padValidateLabel makes it.
  function labelOf(form, control) {
    if (!control || !control.id) return '';
    var label = form.querySelector('label[for="' + (window.CSS && CSS.escape ? CSS.escape(control.id) : control.id) + '"]');
    return label ? label.textContent.trim().replace(/[\s:*]+$/, '') : '';
  }

  function idOf(name, control) {
    return (control && control.id) || name.replace(/[^A-Za-z0-9_-]+/g, '-').replace(/^-+|-+$/g, '');
  }

  function show(form, name, text) {
    var controls = controlsOf(form, name);
    var last = controls[controls.length - 1];
    if (!last) return;
    var id = idOf(name, last) + '-error';
    var span = document.getElementById(id);
    controls.forEach(function (control) {
      if (text) {
        control.setAttribute('aria-invalid', 'true');
        control.setAttribute('aria-describedby', id);
      } else if (control.getAttribute('aria-describedby') === id) {
        control.removeAttribute('aria-invalid');
        control.removeAttribute('aria-describedby');
      }
    });
    if (!text) {
      if (span && form.contains(span)) span.remove();
      return;
    }
    if (!span) {
      span = document.createElement('span');
      span.className = 'error';
      span.id = id;
      var after = last;
      if ((last.type === 'checkbox' || last.type === 'radio') && last.nextElementSibling && last.nextElementSibling.tagName === 'LABEL')
        after = last.nextElementSibling;
      after.insertAdjacentElement('afterend', span);
    }
    span.textContent = text;
  }

  function banner(form, on) {
    var text = form.getAttribute('data-pad-error');
    var shown = form.querySelector(':scope > .error[role="alert"]');
    if (!text) return;
    if (on && !shown) {
      shown = document.createElement('div');
      shown.className = 'error';
      shown.setAttribute('role', 'alert');
      shown.textContent = text;
      form.insertBefore(shown, form.firstChild);
    } else if (!on && shown) {
      shown.remove();
    }
  }

  function checkOne(form, name) {
    var rules = rulesOf(form);
    if (!rules[name]) return true;
    var values = valuesOf(form, rules);
    var controls = controlsOf(form, name);
    var problem = field(rules[name], values[name], values, labelOf(form, controls[controls.length - 1]));
    show(form, name, problem);
    return !problem;
  }

  function checkForm(form) {
    var rules = rulesOf(form);
    var values = valuesOf(form, rules);
    var first = null;
    Object.keys(rules).forEach(function (name) {
      var controls = controlsOf(form, name);
      var problem = field(rules[name], values[name], values, labelOf(form, controls[controls.length - 1]));
      show(form, name, problem);
      if (problem && !first) first = controls[0];
    });
    banner(form, !!first);
    if (first && first.focus) first.focus();
    return !first;
  }

  function ruled(target) {
    return target && target.form && target.form.hasAttribute('data-pad-rules') && target.name ? target.form : null;
  }

  // Captured on the document, so a form that fails stops here - before a live region, htmx
  // or any other script that sends forms got the event.
  document.addEventListener('submit', function (event) {
    var form = event.target;
    if (!form.hasAttribute || !form.hasAttribute('data-pad-rules')) return;
    if (!checkForm(form)) {
      event.preventDefault();
      event.stopImmediatePropagation();
    }
  }, true);

  document.addEventListener('focusout', function (event) {
    var form = ruled(event.target);
    if (form && (event.target.value !== '' || document.getElementById(idOf(event.target.name, event.target) + '-error')))
      checkOne(form, event.target.name);
  });

  ['input', 'change'].forEach(function (type) {
    document.addEventListener(type, function (event) {
      var form = ruled(event.target);
      if (form && event.target.getAttribute('aria-invalid') === 'true')
        checkOne(form, event.target.name);
    });
  });

  window.padValidate = { check: check, field: field, form: checkForm };

})();
