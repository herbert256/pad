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
// left or - once it showed a message - while it is typed in. A field whose rules look at
// another - same:password, required_if:delivery,ship, after:start, its watch - is checked
// again when that one changes, if it shows a message or was checked or left before. Its
// values are read as the post would carry them (FormData): the last value of a name, every
// value of name[].
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

  // The rules that judge an empty field, and those that look at other fields of the post,
  // from their PHP files ('empty', 'fields') - written in by the server.
  var empties = /*empties*/{};
  var lookers = /*lookers*/{};

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

  // A name as PHP reads it, padFormPath: the spaces before it dropped, a space or a dot before
  // the first [ an underscore, then each [key] a level, [] the list itself - up to a ] that
  // no [ follows. A [ that never closes is an underscore too.
  function path(name) {
    name = String(name).replace(/^ +/, '');
    var at = name.match(/^([^\[]*)((?:\[[^\]]*\])+)/);
    if (!at) return [name.replace(/[ .\[]/g, '_')];
    return [at[1].replace(/[ .]/g, '_')].concat(at[2].slice(1, -1).split(']['));
  }

  // The values filed as PHP files the post they make: emails[0] an item of the list emails,
  // first.name the field first_name, user[kind] the kind of user. A level is a Map, which
  // keeps the order PHP keeps; [] adds after the highest number, as PHP adds.
  function filed(values) {
    var top = new Map();
    Object.keys(values).forEach(function (name) {
      var at = path(name), value = values[name];
      if (at[0] === '') return;
      (at[at.length - 1] === '' && Array.isArray(value) ? value : [value]).forEach(function (one) {
        var level = top;
        at.forEach(function (key, i) {
          if (key === '' && i) key = next(level);
          if (i === at.length - 1) return level.set(key, one);
          if (!(level.get(key) instanceof Map)) level.set(key, new Map());
          level = level.get(key);
        });
      });
    });
    return top;
  }

  function next(level) {
    var high = -1;
    level.forEach(function (one, key) { if (/^(0|-?[1-9]\d*)$/.test(key) && +key > high) high = +key; });
    return String(high + 1);
  }

  // The filing of the values a check of many fields reads - check, the checks of a form -
  // made once for them all and kept only while it runs (batch): the values a page's script
  // hands padValidate.field may have changed by its next call.
  var filing = null;

  function filingOf(values) {
    if (!filing) return filed(values);
    if (filing.values !== values) filing = { values: values, top: filed(values) };
    return filing.top;
  }

  function batch(run) {
    filing = { values: null, top: null };
    try { return run(); } finally { filing = null; }
  }

  // A field of the post where PHP files it, as padFormFind finds it in the post PHP filed:
  // [ found, value ], a level of the filing a Map. The values hold the names the form sends,
  // so the key that is the name itself is no answer - kinds[] and kinds[1] are one list.
  function find(values, name) {
    var at = path(String(name)), level = filingOf(values);
    for (var i = 0; i < at.length; i++) {
      if (at[i] === '' && i) break;
      var map = level instanceof Map;
      if (map ? !level.has(at[i]) : !(level && typeof level === 'object' && Object.prototype.hasOwnProperty.call(level, at[i]))) return [false, undefined];
      level = map ? level.get(at[i]) : level[at[i]];
    }
    return [true, level];
  }

  // A level of the filing as PHP has it: a list - its keys 0, 1, 2 in that order - an
  // array, one with keys of its own null.
  function listOf(level) {
    if (!(level instanceof Map)) return level;
    var items = [], at = 0, keyed = false;
    level.forEach(function (one, key) { keyed = keyed || key !== String(at++); items.push(one); });
    return keyed ? null : items;
  }

  // Another field of the post as the rules that look at one read it, as padRuleSibling: its
  // text trimmed, or the trimmed texts of a list - kinds finds kinds[] and kinds[] finds
  // kinds, emails[] the items emails[0] and emails[1], first_name the field first.name - and
  // '' for a field neither the post nor the query string has (valuesOf), as for an empty one,
  // or for anything that is no one field's value.
  function sibling(values, name) {
    name = trim(String(name));
    var found = find(values, name), value = found[1];
    if (!found[0] && !/\[\]$/.test(name)) value = find(values, name + '[]')[1];
    value = listOf(value);
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
  // time - 2026-11-01T14:30, 14:30:15 or 14:30:15.25, as a datetime-local field with a step
  // under a second posts it - made 2026-11-01T14:30:00.000, which compares as the moments
  // do; null for any other text. Date.parse read other texts in every browser.
  function moment(value) {
    var at = typeof value === 'string' && value.match(/^(\d{4})-(\d{2})-(\d{2})(?:T(\d{2}):(\d{2})(?::(\d{2})(?:\.(\d{1,3}))?)?)?$/);
    if (!at) return null;
    var year = +at[1], month = +at[2], day = +at[3];
    var check = new Date(0);
    check.setUTCFullYear(year, month - 1, day);
    if (year < 1 || check.getUTCFullYear() !== year || check.getUTCMonth() !== month - 1 || check.getUTCDate() !== day) return null;
    var hour = at[4] || '00', minute = at[5] || '00', second = at[6] || '00', fraction = at[7] || '';
    if (+hour > 23 || +minute > 59 || +second > 59) return null;
    return at[1] + '-' + at[2] + '-' + at[3] + 'T' + hour + ':' + minute + ':' + second + '.' + (fraction + '000').slice(0, 3);
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

  // after_or_equal and before_or_equal (padRuleWhenSame): whether the field's moment is the
  // other's, the date of the rule or the other field's.
  function whenSame(value, arg, values) {
    var mine = moment(value);
    return mine !== null && mine === moment(dateArg(arg) ? trim(String(arg)) : sibling(values, arg));
  }

  // gt, gte, lt and lte (padRuleCompare): [ the field's measure, the other's ] - a number of
  // the rule, or the other field measured alike - or null when there is none: an other that
  // is empty or a list, or on a number field no number as is_numeric reads one.
  function compare(value, arg, values, number) {
    var given = trim(String(arg));
    if (/^[+-]?(\d+(\.\d*)?|\.\d+)([eE][+-]?\d+)?$/.test(given)) return [size(value, number), toNumber(given)];
    var other = sibling(values, arg);
    if (typeof other !== 'string' || other === '') return null;
    if (number && !/^[ \t\n\r\v\f]*[+-]?(\d+(\.\d*)?|\.\d+)([eE][+-]?\d+)?[ \t\n\r\v\f]*$/.test(other)) return null;
    return [size(value, number), size(other, number)];
  }

  // min_items and max_items (padRuleItems): the items of a list that are filled in.
  function counted(list) {
    return list.filter(function (one) { return typeof one !== 'string' || trim(one) !== ''; }).length;
  }

  // min_words and max_words (padRuleWords): the runs of characters between Unicode's
  // White_Space, written out as the server writes it - \s holds another set here.
  function words(value) {
    return String(value).split(/[\t-\r \u0085\u00a0\u1680\u2000-\u200a\u2028\u2029\u202f\u205f\u3000]+/).filter(function (one) { return one !== ''; }).length;
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
    return batch(function () {
      var errors = {};
      Object.keys(rules || {}).forEach(function (name) {
        var problem = field(rules[name], values[name], values);
        if (problem) errors[name] = problem;
      });
      return errors;
    });
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
  // One walk of the entries: the values of a rule's field taken with getAll walked them all
  // again for every field.
  //
  // What the post leaves out - a radio group without a choice, a box not ticked, a field the
  // form does not have - is read from the query string of the address the form posts to, by
  // the name PHP files it under, as the server reads the request (padRequestInput: the post,
  // then the query string): posted to ?checkout&delivery=collect without a delivery, the page
  // has delivery collect, and its rules judge that. Read as empty here, required_unless:
  // delivery,collect blocked a post the server takes, required_with:newsletter on
  // ?x&newsletter=1 let one through it refuses.
  function valuesOf(form, rules) {
    var all = Object.create(null), values = {}, posted = Object.create(null);
    new FormData(form).forEach(function (one, name) {
      if (typeof one !== 'string') return;
      (all[name] = all[name] || []).push(sent(one));
      posted[path(name)[0]] = true;
    });
    queryOf(form).forEach(function (one) {
      if (!posted[path(one[0])[0]]) (all[one[0]] = all[one[0]] || []).push(one[1]);
    });
    Object.keys(all).forEach(function (name) {
      values[name] = /\[\]$/.test(name) ? all[name] : all[name][all[name].length - 1];
    });
    Object.keys(rules).forEach(function (name) {
      var list = all[name] || [];
      values[name] = rules[name].list ? list : (list.length ? list[list.length - 1] : '');
    });
    return values;
  }

  // The names and values of the query string of the address the form posts to - its action,
  // the page's own address when it has none - as [ name, value ] each.
  function queryOf(form) {
    var entries = [];
    try {
      var address = typeof form.action === 'string' && form.action ? form.action : window.location.href;
      new URL(address).searchParams.forEach(function (one, name) { entries.push([name, one]); });
    } catch (e) {}
    return entries;
  }

  // The controls of the form by name, in one walk of its elements: looked up field by field,
  // every field judged walked them all again.
  function controlsBy(form) {
    var by = Object.create(null);
    Array.prototype.forEach.call(form.elements, function (one) {
      if (one.name) (by[one.name] = by[one.name] || []).push(one);
    });
    return by;
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

  // The error of a group of boxes goes by the id of its first box, as {input} writes it.
  function errorId(name, controls) {
    var last = controls[controls.length - 1];
    return idOf(name, box(last) ? controls[0] : last) + '-error';
  }

  // The error of a rich field goes after its frame, as the server writes it.
  function show(form, name, text, controls) {
    var last = controls[controls.length - 1];
    if (!last) return;
    var id = errorId(name, controls);
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
    if (span.textContent !== text) span.textContent = text;
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

  // The fields of each form checked or left so far, by name: a change of a field their
  // rules look at checks them again (recheck).
  var marked = new WeakMap();

  function marks(form) {
    var seen = marked.get(form);
    if (!seen) marked.set(form, seen = {});
    return seen;
  }

  // One field judged and its message shown, with the rules, the values and the controls of
  // the form read once for all the fields judged together: read again for each, a field
  // that many fields watch took time growing with their square on every keystroke.
  function judge(form, rules, values, controls, name) {
    var mine = controls[name] || [];
    var problem = field(rules[name], values[name], values, labelOf(form, mine[mine.length - 1]));
    show(form, name, problem, mine);
    marks(form)[name] = true;
    return problem;
  }

  function checkOne(form, name) {
    var rules = rulesOf(form);
    if (!rules[name]) return true;
    return batch(function () { return !judge(form, rules, valuesOf(form, rules), controlsBy(form), name); });
  }

  function checkForm(form) {
    var rules = rulesOf(form);
    var values = valuesOf(form, rules);
    var controls = controlsBy(form);
    var first = null;
    batch(function () {
      Object.keys(rules).forEach(function (name) {
        var mine = controls[name] || [];
        if (judge(form, rules, values, controls, name) && !first && mine[0]) first = shownBy(mine[0]);
      });
    });
    banner(form, !!first);
    if (first && first.focus) first.focus();
    return !first;
  }

  function ruled(target) {
    return target && target.form && target.form.hasAttribute('data-pad-rules') && target.name ? target.form : null;
  }

  function showing(controls) {
    return controls.some(function (control) { return shownBy(control).getAttribute('aria-invalid') === 'true'; });
  }

  // Where PHP files a name, up to its first [] - kinds and kinds[] one place, first.name and
  // first_name too.
  function place(name) {
    var at = path(trim(String(name))), list = at.indexOf('', 1);
    return list < 0 ? at : at.slice(0, list);
  }

  // Whether a field changed is the field a watch names, or stands in it or holds it: emails[0]
  // changes what required_without:emails[] reads, user[kind] what a rule on user does.
  function touches(watched, changed) {
    var a = place(watched), b = place(changed);
    for (var i = 0; i < Math.min(a.length, b.length); i++) if (a[i] !== b[i]) return false;
    return true;
  }

  // A field whose rules look at other fields has their names in its watch, from the rules'
  // fields (padValidateClient): when one of them changes, the field is checked again if it
  // shows a message - the server's or the checker's - or was checked or left before. Only
  // the field typed in was checked: an "is required when Delivery is ship" stayed after
  // delivery became collect, and "must be the same as Password" after the password was
  // typed again to match. A name is where PHP files it here, as sibling reads it: kinds and
  // kinds[] are one, emails[0] stands in emails[] (touches). The rules, the values and the
  // controls are read once for all the fields checked again.
  //
  // A message the server wrote on a field not checked or left since speaks about the value
  // the field still holds, which a change of another field does not change: it stays unless
  // the checker has a message of its own for the field, or it is the message of one of the
  // field's rules that look at other fields, which follows them (follows). A message the
  // page's PHP added - End is fully booked - or one of a rule the checker leaves to the
  // server - a regex only PCRE reads - went when the start was moved, and the field had not
  // been touched.
  function recheck(form, name) {
    var rules = rulesOf(form), seen = marks(form), values = null, controls = null;
    batch(function () {
      Object.keys(rules).forEach(function (one) {
        if (one === name || !(rules[one].watch || []).some(function (other) { return touches(other, name); })) return;
        controls = controls || controlsBy(form);
        var mine = controls[one] || [];
        if (!seen[one] && !showing(mine)) return;
        values = values || valuesOf(form, rules);
        var label = labelOf(form, mine[mine.length - 1]) || rules[one].label;
        var problem = field(rules[one], values[one], values, label);
        if (!seen[one] && !problem && !follows(rules[one], said(one, mine), label)) return;
        show(form, one, problem, mine);
        seen[one] = true;
      });
    });
  }

  // The message shown beside a field, '' when there is none.
  function said(name, controls) {
    var span = controls.length ? document.getElementById(errorId(name, controls)) : null;
    return span ? span.textContent : '';
  }

  // Whether a message is the one a rule of the field that looks at other fields gives.
  function follows(spec, text, label) {
    return spec.rules.some(function (one) { return lookers[one[0]] && message(one[2], label, one[3]) === text; });
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

  // A field left empty is not checked - a required one would speak before it was filled in
  // - but it is left, and a change of a field its rules look at checks it from then on.
  document.addEventListener('focusout', function (event) {
    var target = fieldOf(event.target), form = ruled(target);
    if (form && (target.value !== '' || document.getElementById(idOf(target.name, target) + '-error')))
      checkOne(form, target.name);
    if (form) marks(form)[target.name] = true;
  });

  // A control without a name that a field shows itself in - the filter box of a multiselect,
  // which its select names in data-pad-shows - is that field when the focus leaves it: the
  // focus is never in the hidden select, so a multiselect left empty was never left, and a
  // change of a field its rules look at - required_if:plan,pro - did not check it.
  function fieldOf(target) {
    if (!target || target.name || !target.id || !target.form || !target.form.querySelector) return target;
    var id = window.CSS && CSS.escape ? CSS.escape(target.id) : target.id;
    return target.form.querySelector('[data-pad-shows="' + id + '"]') || target;
  }

  ['input', 'change'].forEach(function (type) {
    document.addEventListener(type, function (event) {
      var form = ruled(event.target);
      if (!form) return;
      if (shownBy(event.target).getAttribute('aria-invalid') === 'true')
        checkOne(form, event.target.name);
      recheck(form, event.target.name);
    });
  });

  window.padValidate = { check: check, field: field, form: checkForm };

})();
