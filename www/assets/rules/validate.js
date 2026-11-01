// The browser's half of padValidate (lib/form.php): the same rules, the same messages, so
// a visitor sees what the server would say before the form leaves - and the server still
// checks every post. PAD writes this file into the page as one inline script, once:
// behind the first {form ..., client}, or where {validator} stands - with the script of
// every rule, www/assets/rules/<rule>.js, where the rules stand below, and which rules
// judge an empty field from the rules' own files (padValidateScript).
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
// A rule's script calls addRule ( name, { check, list } ): check ( value, arg, values,
// number ) judges one text, list ( items, arg, values, number ) a list - a rule without
// one fails every list, as the server's does. A rule without a script, or a value its
// script cannot judge as PHP does - a regex PCRE writes another way than JavaScript -
// passes here and is left to the server.

(function () {

  'use strict';

  if (window.padValidate) return;

  var rules = {};

  // The rules that judge an empty field, from their PHP files - written in by the server.
  var empties = /*empties*/{};

  function addRule(name, spec) {
    rules[name] = spec;
  }

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

  // What min, max and between measure, as padValidateSize: the length, or on a number field
  // the value.
  function size(value, number) {
    return number ? toNumber(value) : length(value);
  }

  // The items of a comma list as the server parts them: explode, then trim.
  function items(arg) {
    return String(arg).split(',').map(trim);
  }

  function alnum(c) {
    return /^[A-Za-z0-9]$/.test(c);
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

  // The items of a comma list as padRuleTexts takes them: trimmed, the empty ones left out.
  function texts(arg) {
    return items(arg).filter(function (one) { return one !== ''; });
  }

  function text(value) {
    return typeof value === 'string' || typeof value === 'number';
  }

  // Another field of the post as the rules that look at one read it, as padRuleSibling: its
  // text trimmed, or the trimmed texts of a list - kinds finds kinds[] and kinds[] finds
  // kinds - and '' for a field the form does not send, as for an empty one, or for anything
  // that is no one field's value.
  function sibling(values, name) {
    name = trim(String(name));
    var value = values[name];
    if (value === undefined) value = /\[\]$/.test(name) ? values[name.slice(0, -2)] : values[name + '[]'];
    if (Array.isArray(value)) return value.filter(text).map(function (one) { return trim(String(one)); });
    return text(value) ? trim(String(value)) : '';
  }

  // Filled as required judges it: a text that is not empty, a list with an item.
  function filled(value) {
    return Array.isArray(value) ? value.length > 0 : trim(String(value)) !== '';
  }

  // Whether the field required_if or required_unless names first holds one of the values
  // after it, as written - its text, or an item of its list (padRuleSiblingIs).
  function siblingIs(values, arg) {
    var parts = String(arg).split(',');
    var wanted = texts(parts.slice(1).join(','));
    var other = sibling(values, parts[0]);
    return (Array.isArray(other) ? other : [other]).some(function (one) { return wanted.indexOf(one) >= 0; });
  }

  // Whether one of the fields required_with or required_without names is filled (true) or
  // empty (false) - padRuleAny.
  function anyFilled(values, arg, wanted) {
    return texts(arg).some(function (name) { return filled(sibling(values, name)) === wanted; });
  }

  // A moment as before and after read one, as padRuleMoment: the ISO day, or the day with a
  // time - 2026-11-01T14:30 or 14:30:15 - made 2026-11-01T14:30:00, which compares as the
  // moments do; null for any other text. Date.parse read other texts in every browser.
  function moment(value) {
    var at = typeof value === 'string' && value.match(/^(\d{4})-(\d{2})-(\d{2})(?:T(\d{2}):(\d{2})(?::(\d{2}))?)?$/);
    if (!at) return null;
    var year = +at[1], month = +at[2], day = +at[3];
    var check = new Date(0);
    check.setUTCFullYear(year, month - 1, day);
    if (year < 1 || check.getUTCFullYear() !== year || check.getUTCMonth() !== month - 1 || check.getUTCDate() !== day) return null;
    var hour = at[4] || '00', minute = at[5] || '00', second = at[6] || '00';
    if (+hour > 23 || +minute > 59 || +second > 59) return null;
    return at[1] + '-' + at[2] + '-' + at[3] + 'T' + hour + ':' + minute + ':' + second;
  }

  // before and after (padRuleWhenArgument, padRuleWhen): a date written in the rule - four
  // digits, a dash, a digit - or the other field's value. A value that is no moment fails,
  // an other that is none passes.
  function dateArg(arg) {
    return /^\d{4}-\d/.test(trim(String(arg)));
  }

  function when(value, arg, values, side) {
    var mine = moment(value);
    if (mine === null) return false;
    var other = moment(dateArg(arg) ? trim(String(arg)) : sibling(values, arg));
    if (other === null) return true;
    return side < 0 ? mine < other : mine > other;
  }

  /*rules*/

  function rule(name, arg, value, values, number) {
    var spec = rules[name];
    if (!spec) return true;
    if (Array.isArray(value)) return spec.list ? !!spec.list(value, arg, values, number) : false;
    return !!spec.check(String(value), arg, values, number);
  }

  function message(text, label, shown) {
    return text.replace(/:label|:n/g, function (token) { return token === ':label' ? label : shown; });
  }

  // One field: the message of the first rule it breaks, as padValidate takes them - an empty
  // value is judged by the rules that judge one alone, a list where one value belongs is
  // refused whatever the rules say.
  function field(spec, value, values, label) {
    value = trim(value === undefined || value === null ? '' : value);
    label = label || spec.label;
    if (spec.rules.length && Array.isArray(value) && !spec.list) return message(spec.single, label, '');
    for (var i = 0; i < spec.rules.length; i++) {
      var one = spec.rules[i];
      if (empty(value) && !empties[one[0]]) continue;
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
  // A textarea's line breaks are LF in FormData and CR LF in the post the server counts:
  // counted as one, min:n blocked a form the server takes and max:n let one through.
  function sent(one) {
    return one.replace(/\r\n|\r|\n/g, '\r\n');
  }

  // A name of a list - kinds[] - is every value it has, as PHP files it, its field with rules
  // or not: the other field of same:kinds[] read as its last value was a text the server
  // never sees, and the confirmation passed here what the server refused.
  function valuesOf(form, rules) {
    var data = new FormData(form);
    var values = {};
    data.forEach(function (one, name) {
      if (typeof one !== 'string') return;
      if (/\[\]$/.test(name)) (values[name] = Array.isArray(values[name]) ? values[name] : []).push(sent(one));
      else values[name] = sent(one);
    });
    Object.keys(rules).forEach(function (name) {
      var all = data.getAll(name).filter(function (one) { return typeof one === 'string'; }).map(sent);
      values[name] = rules[name].list ? all : (all.length ? all[all.length - 1] : '');
    });
    return values;
  }

  function controlsOf(form, name) {
    return Array.prototype.filter.call(form.elements, function (one) { return one.name === name; });
  }

  // The text of the field's <label>, made fit for a sentence as padValidateLabel makes it.
  // A checkbox or radio is worded with the field's name, as {input} words it: its label
  // names one choice of the group. A rich field of pad/subs/inputs/ - a slider, a row of stars,
  // a signature whose value is a hidden input - carries the label it is worded with on its
  // frame, data-pad-label, as the server words its message.
  function labelOf(form, control) {
    var frame = control && control.closest ? control.closest('[data-pad-label]') : null;
    if (frame) return frame.getAttribute('data-pad-label');
    if (!control || !control.id || box(control)) return '';
    var label = form.querySelector('label[for="' + (window.CSS && CSS.escape ? CSS.escape(control.id) : control.id) + '"]');
    return label ? label.textContent.trim().replace(/[\s:*]+$/, '') : '';
  }

  // A hidden field a rich field posts - the list of a tag input, the code of the boxes of a
  // one-time code - has the id of the control that shows it, in data-pad-shows: its message
  // goes by that id, as the server's does.
  function idOf(name, control) {
    var shows = control && control.getAttribute ? control.getAttribute('data-pad-shows') : null;
    return shows || (control && control.id) || name.replace(/[^A-Za-z0-9_-]+/g, '-').replace(/^-+|-+$/g, '');
  }

  // A switch is one field, not a choice of a group: it has its label as a text field has.
  function box(control) {
    return (control.type === 'checkbox' || control.type === 'radio') && control.getAttribute('role') !== 'switch';
  }

  // The control that shows a hidden field's state - the canvas of a signature, the boxes of
  // a one-time code - named by the hidden field's data-pad-shows: it is marked invalid and
  // given the focus in the hidden field's place.
  function shownBy(control) {
    var id = control.getAttribute && control.getAttribute('data-pad-shows');
    return (id && document.getElementById(id)) || control;
  }

  // The error of a group of boxes goes by the id of its first box, as {input} writes it; the
  // error of a rich field after its frame, as the server writes it.
  function show(form, name, text) {
    var controls = controlsOf(form, name);
    var last = controls[controls.length - 1];
    if (!last) return;
    var id = idOf(name, box(last) ? controls[0] : last) + '-error';
    var span = document.getElementById(id);
    controls.map(shownBy).forEach(function (control) {
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
      var after = (last.closest && last.closest('[data-pad-label]')) || last;
      if (after === last && (last.type === 'checkbox' || last.type === 'radio') && last.nextElementSibling && last.nextElementSibling.tagName === 'LABEL')
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
      if (problem && !first && controls[0]) first = shownBy(controls[0]);
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
      if (form && shownBy(event.target).getAttribute('aria-invalid') === 'true')
        checkOne(form, event.target.name);
    });
  });

  window.padValidate = { check: check, field: field, form: checkForm };

})();
