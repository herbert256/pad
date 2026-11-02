// The parity line of ./ci.sh: the browser's checker judges the cases of
// forms/client_parity.php as padValidate does - the same field passes or fails, with the
// same message. The page is rendered on the command line, the checker run in a bare context
// with what it needs of a browser. The checker is the one a page gets: the runtime,
// www/assets/rules/validate.js, with the script of every rule of pad/subs/rules/types/ - written
// by {pad:validator}, so a rule added to the directory is judged here as it is in the page. The
// cases hold the shared table of forms/_rules.php too - the rules that look at other fields
// and not_in - and a form's watcher is walked through the steps of a visitor below.

'use strict';

const { execFileSync } = require('child_process');
const path = require('path');
const vm = require('vm');

const home = path.resolve(__dirname, '../../../..');
const render = (page, input) => execFileSync('php', [path.join(home, 'editors/render.php'), 'regression/pages', page].concat(input ? ['--source=-'] : []),
                                             { env: Object.assign({}, process.env, { PAD_HOME: home }), encoding: 'utf8', input });

const cases = JSON.parse(render('forms/client_parity')).parity;

const checker = render('forms/client_parity_checker', '{pad:validator/}').match(/<script[^>]*>([\s\S]*)<\/script>/);

if (!checker) {
  console.error('{pad:validator/} wrote no script');
  process.exit(1);
}

// Of a browser: a form whose FormData carries the case's values - the last of a name, every
// one of a list - with a control for every name, and the message shown beside a field.
class FormData {
  constructor(form) { this.entries = form.entries; }
  getAll(name) { return this.entries.filter(one => one[0] === name).map(one => one[1]); }
  forEach(each) { this.entries.forEach(one => each(one[1], one[0])); }
}

let shown = [];

const document = {
  addEventListener() {},
  getElementById() { return null; },
  createElement() { return { set textContent(text) { shown.push(text); } }; }
};

function formOf(one) {
  const entries = [];
  // A textarea's value in FormData holds its line breaks as LF; the post carries them as
  // CR LF, which is what the server - and so the case's data - sees.
  Object.keys(one.data).forEach(name => [].concat(one.data[name]).forEach(value => entries.push([name, typeof value === 'string' ? value.replace(/\r\n/g, '\n') : value])));
  const names = [...new Set(Object.keys(one.client).concat(Object.keys(one.data)))];
  const elements = names.map(name => ({ name, id: name, type: 'text', focus() {}, setAttribute() {}, getAttribute() { return null; },
                                        removeAttribute() {}, insertAdjacentElement() {} }));
  return { entries, elements, hasAttribute() { return true; }, querySelector() { return null; }, contains() { return false; },
           getAttribute(name) { return name === 'data-pad-rules' ? JSON.stringify(one.client) : null; } };
}

const context = { window: {}, document, URL, FormData };

vm.runInNewContext(checker[1], context);

const failed = [];

for (const one of cases) {
  const got = context.window.padValidate.check(one.client, one.data)[one.field] || '';
  if (got !== one.server)
    failed.push(`${one.field} ${JSON.stringify(one.data)}: the server says ${JSON.stringify(one.server)}, the browser ${JSON.stringify(got)}`);

  // The same values as a form in the page sends them: the field of same: has its partner
  // from the form, though that one has no rules. A list on a field of one value is no
  // form's: its post carries the last value alone.
  if (Array.isArray(one.data[one.field]) && !one.client[one.field].list)
    continue;
  shown = [];
  const sent = context.window.padValidate.form(formOf(one));
  if (sent !== (one.server === '') || (shown[0] || '') !== one.server)
    failed.push(`${one.field} ${JSON.stringify(one.data)} as a form: the server says ${JSON.stringify(one.server)}, the browser ${JSON.stringify(shown[0] || '')}`);
}

// The watcher: a field whose rules look at another - same:, required_if:, after: - is checked
// again when that one changes, if it shows a message or was checked or left before. A form
// of the rules= a template gives, in a world of its own with what the checker touches of a
// page: controls with their attributes, the messages it puts after them by id, the events
// it listens to. The same steps a visitor takes in headless Chrome hold alike there:
// _chrome.mjs, run by hand, drives them and the cases above through a real page.

// The world of one form: fields is [ name, value, more ] each - a value null is not posted,
// more the key a step names it by, its id and its attributes when they are not the name's;
// server the messages the server wrote beside them, by name; action the address the form
// posts to; count says how often the checker read the rules, made the values (FormData) and
// walked the form's controls.
const rendered = {};

function world(template, fields, server, action) {
  const found = (rendered[template] = rendered[template] || render('forms/client_parity_watch', template)).match(/data-pad-rules="([^"]*)"/);
  if (!found) return null;
  const listeners = {};
  const byId = {};
  const count = { rules: 0, values: 0, walks: 0 };
  const elements = [];
  const page = {
    addEventListener(type, each) { (listeners[type] = listeners[type] || []).push(each); },
    getElementById(id) { return byId[id] || elements.find(one => one.id === id) || null; },
    createElement() { return { id: '', textContent: '', remove() { delete byId[this.id]; } }; }
  };
  const rules = found[1].replace(/&quot;/g, '"').replace(/&amp;/g, '&');
  const form = { action, get elements() { count.walks++; return elements; }, hasAttribute(name) { return name === 'data-pad-rules'; },
                 contains(span) { return byId[span.id] === span; },
                 querySelector(selector) {
                   const shows = selector.match(/^\[data-pad-shows="(.*)"\]$/);
                   return (shows && elements.find(one => one.getAttribute('data-pad-shows') === shows[1])) || null;
                 },
                 getAttribute(name) { if (name !== 'data-pad-rules') return null; count.rules++; return rules; } };
  const controls = {};
  fields.forEach(([name, value, more = {}]) => {
    const attrs = Object.assign({}, more.attrs);
    elements.push(controls[more.key || name] = { name, value, id: more.id || name, type: 'text', form, focus() {}, closest() { return null; },
      getAttribute(one) { return one in attrs ? attrs[one] : null; }, setAttribute(one, to) { attrs[one] = String(to); },
      removeAttribute(one) { delete attrs[one]; }, insertAdjacentElement(where, span) { byId[span.id] = span; } });
  });
  Object.keys(server || {}).forEach(name => {
    byId[name + '-error'] = { id: name + '-error', textContent: server[name], remove() { delete byId[this.id]; } };
    controls[name].setAttribute('aria-invalid', 'true');
    controls[name].setAttribute('aria-describedby', name + '-error');
  });
  class PageData {
    constructor() { count.values++; this.entries = elements.filter(one => one.name && one.value !== null).map(one => [one.name, one.value]); }
    getAll(name) { return this.entries.filter(one => one[0] === name).map(one => one[1]); }
    forEach(each) { this.entries.forEach(one => each(one[1], one[0])); }
  }
  const scope = { window: {}, document: page, FormData: PageData, URL };
  vm.runInNewContext(checker[1], scope);
  const fire = (type, name, value) => {
    if (value !== undefined) controls[name].value = value;
    (listeners[type] || []).forEach(each => each({ target: controls[name] }));
  };
  const shown = name => byId[name + '-error'] ? byId[name + '-error'].textContent : '';
  return { form, controls, count, fire, shown, padValidate: scope.window.padValidate };
}

const watching = [];

const tripForm = "{pad:form 'trip', client}{pad:input 'delivery'/}{pad:input 'address', rules='required_if:delivery,ship'/}" +
                 "{pad:input 'password'/}{pad:input 'confirm', rules='same:password'/}{pad:input 'start'/}{pad:input 'end', rules='after:start'/}{/pad:form}";

const trip = world(tripForm,
                   [['delivery', 'ship'], ['address', ''], ['password', 'secret'], ['confirm', 'secret'], ['start', '2026-05-01'], ['end', '']]);

if (!trip) watching.push('{pad:form ..., client} wrote no data-pad-rules');
else {
  const { fire, controls } = trip;
  const expect = (step, name, text) => {
    const got = trip.shown(name);
    const invalid = controls[name].getAttribute('aria-invalid') === 'true';
    if (got !== text || invalid !== (text !== ''))
      watching.push(`watcher, ${step}: ${name} shows ${JSON.stringify(got)}${invalid ? ' (aria-invalid)' : ''}, not ${JSON.stringify(text)}`);
  };
  fire('change', 'delivery', 'collect');
  expect('a field never left is not checked when the other changes', 'address', '');
  fire('focusout', 'address');
  expect('a field left empty is not checked', 'address', '');
  fire('change', 'delivery', 'ship');
  expect('the other changed to the value', 'address', 'Address is required when Delivery is ship');
  fire('change', 'delivery', 'collect');
  expect('the other changed away from it', 'address', '');
  fire('input', 'delivery', 'ship');
  fire('input', 'address', 'Main Street 1');
  expect('the field filled in', 'address', '');
  fire('focusout', 'confirm');
  fire('input', 'password', 'secret2');
  expect('the password typed again', 'confirm', 'Confirm must be the same as Password');
  fire('input', 'password', 'secret');
  expect('the password typed back to match', 'confirm', '');
  fire('focusout', 'end', '2026-04-01');
  expect('an end before the start', 'end', 'End must be a date after Start');
  fire('input', 'start', '2026-03-01');
  expect('the start moved before it', 'end', '');
  fire('input', 'start', '');
  expect('the start emptied', 'end', '');
  controls.address.value = '';
  if (trip.padValidate.form(trip.form) !== false)
    watching.push('watcher: the form was sent with an address required when delivery is ship');
  expect('the form sent', 'address', 'Address is required when Delivery is ship');
  fire('change', 'delivery', 'collect');
  expect('the other changed after the form was sent', 'address', '');
}

// The page of a failed post: a message the server wrote beside a field the visitor has not
// touched since speaks about the value the field still holds. The message of a rule that
// looks at the field that changed follows it; one the page's PHP added - or one of a rule
// the checker leaves to the server - stays, unless the checker has a message of its own.
const posted = (server, steps) => {
  const back = world(tripForm, [['delivery', 'ship'], ['address', ''], ['password', 'secret'], ['confirm', 'secret'], ['start', '2026-05-01'], ['end', '2026-06-01']], server);
  if (!back) return watching.push('{pad:form ..., client} wrote no data-pad-rules');
  steps(back, (step, name, text) => {
    const got = back.shown(name);
    const invalid = back.controls[name].getAttribute('aria-invalid') === 'true';
    if (got !== text || invalid !== (text !== ''))
      watching.push(`watcher on a failed post, ${step}: ${name} shows ${JSON.stringify(got)}${invalid ? ' (aria-invalid)' : ''}, not ${JSON.stringify(text)}`);
  });
};

posted({ end: 'End is fully booked' }, ({ fire }, expect) => {
  fire('input', 'start', '2026-03-02');
  expect("the start moved, the page's own message of an end not touched", 'end', 'End is fully booked');
  fire('input', 'start', '2026-07-01');
  expect('the start moved past the end, the checker has a message of its own', 'end', 'End must be a date after Start');
  fire('input', 'start', '2026-03-02');
  expect('the start moved back, the message the checker gave goes', 'end', '');
});

posted({ address: 'Address is required when Delivery is ship', end: 'End must be a date after Start' }, ({ fire }, expect) => {
  fire('change', 'delivery', 'collect');
  expect("the other changed away from the value, the server's message of that rule follows it", 'address', '');
  fire('input', 'start', '2026-03-02');
  expect("the start moved before the end, the server's message of after:start follows it", 'end', '');
});

// A field watched under the place PHP files it: phones names the list phones[0] is an item
// of, first_name the field first.name is.
const filedForm = world("{pad:form 'reach', client}{pad:input 'phones[0]'/}{pad:input 'code', rules='required_with:phones'/}" +
                        "{pad:input 'first.name'/}{pad:input 'note', rules='required_if:first_name,x'/}{/pad:form}",
                        [['phones[0]', ''], ['code', ''], ['first.name', ''], ['note', '']]);

if (!filedForm) watching.push('{pad:form ..., client} wrote no data-pad-rules');
else {
  [['code', 'phones[0]', '0612', 'Code is required when Phones is filled in'], ['note', 'first.name', 'x', 'Note is required when First name is x']].forEach(([name, other, value, text]) => {
    filedForm.fire('focusout', name);
    filedForm.fire('input', other, value);
    if (filedForm.shown(name) !== text)
      watching.push(`watcher: ${name} shows ${JSON.stringify(filedForm.shown(name))} when ${other} became ${value}, not ${JSON.stringify(text)}`);
  });
}

// What the post leaves out is read from the query string of the address the form posts to,
// as the server reads the request (padRequestInput): posted to ?checkout&delivery=collect&
// newsletter=1 without them, the page has delivery collect and a newsletter, and a field the
// form does send wins (request/form_rules_that_look_at_other_fields_post holds the server).
const checkoutForm = "{pad:form 'checkout', client}{pad:input 'address', rules='required_unless:delivery,collect'/}" +
                     "{pad:input 'email', rules='required_with:newsletter'/}{/pad:form}";

[[[], { address: '', email: 'Email is required when Newsletter is filled in' }],
 [[['delivery', 'ship'], ['newsletter', '']], { address: 'Address is required unless Delivery is collect', email: '' }]].forEach(([sent, want]) => {
  const out = world(checkoutForm, [['address', ''], ['email', '']].concat(sent), {}, 'http://localhost/pad/shop/?checkout&delivery=collect&newsletter=1');
  if (!out) return watching.push('{pad:form ..., client} wrote no data-pad-rules');
  out.padValidate.form(out.form);
  Object.keys(want).forEach(name => {
    if (out.shown(name) !== want[name])
      watching.push(`the query string of the address posted to, ${sent.length ? 'with' : 'without'} delivery and newsletter in the post: ${name} shows ${JSON.stringify(out.shown(name))}, not ${JSON.stringify(want[name])}`);
  });
});

// A multiselect is left when the focus leaves its filter box, which has no name of its own:
// the select, hidden, names it in data-pad-shows. Left empty, it is checked again when the
// field its rules look at changes, as a text field left empty is.
const picks = world("{pad:form 'pick', client}{pad:input 'plan'/}{input:multiselect 'extras', label='Extras', options='a, b, c', rules='required_if:plan,pro'/}{/pad:form}",
                    [['plan', ''], ['extras[]', null, { key: 'select', id: 'extras-select', attrs: { 'data-pad-shows': 'extras' } }], ['', '', { key: 'filter', id: 'extras' }]]);

if (!picks) watching.push('{pad:form ..., client} wrote no data-pad-rules');
else {
  picks.fire('focusout', 'filter');
  picks.fire('input', 'plan', 'pro');
  const invalid = picks.controls.filter.getAttribute('aria-invalid') === 'true';
  if (picks.shown('extras') !== 'Extras is required when Plan is pro' || !invalid)
    watching.push(`watcher: a multiselect left empty shows ${JSON.stringify(picks.shown('extras'))}${invalid ? ' (aria-invalid on its filter box)' : ''} when plan became pro`);
}

// The values a page's script hands padValidate.field again after changing them are read
// anew: the filing of the values is kept only while one check of many fields runs.
const kinds = cases.find(one => one.client.note && one.client.note.rules[0][1] === 'kinds,gift');
const again = { 'kinds[0]': 'book', note: '' };
const before = context.window.padValidate.field(kinds.client.note, '', again);
again['kinds[0]'] = 'gift';
const after = context.window.padValidate.field(kinds.client.note, '', again);
if (before !== '' || after !== 'Note is required when Kinds is gift')
  watching.push(`padValidate.field on values changed between two calls said ${JSON.stringify(before)} and ${JSON.stringify(after)}`);

// A keystroke in a field that many fields watch reads the rules, the values and the controls
// once for all of them: read again for each, it took time growing with the square of their
// number - half a second a key at 500 watchers.
const keystroke = watchers => {
  const big = world("{pad:form 'big', client}{pad:input 'master'/}" +
                    Array.from({ length: watchers }, (none, i) => `{pad:input 'f${i}', rules='required_with:master'/}`).join('') + '{/pad:form}',
                    [['master', '']].concat(Array.from({ length: watchers }, (none, i) => ['f' + i, ''])));
  if (!big) return null;
  big.padValidate.form(big.form);
  Object.keys(big.count).forEach(key => { big.count[key] = 0; });
  big.fire('input', 'master', 'a');
  big.count.shown = Array.from({ length: watchers }, (none, i) => big.shown('f' + i)).filter(text => /^F\d+ is required when Master is filled in$/.test(text)).length;
  return big.count;
};

const few = keystroke(2), many = keystroke(40);

if (!few || !many) watching.push('{pad:form ..., client} wrote no data-pad-rules');
else if (few.shown !== 2 || many.shown !== 40)
  watching.push(`watcher: a keystroke in a field 2 and 40 fields watch showed ${few.shown} and ${many.shown} messages`);
else if (few.rules !== many.rules || few.values !== many.values || few.walks !== many.walks)
  watching.push(`watcher: a keystroke in a field 2 fields watch read the rules ${few.rules}, the values ${few.values} and the controls ${few.walks} times, ` +
                `one in a field 40 fields watch ${many.rules}, ${many.values} and ${many.walks} times`);

failed.push(...watching);

if (failed.length) {
  console.error(failed.join('\n'));
  process.exit(1);
}

console.log(`${cases.length} cases judged alike by padValidate and the browser's checker, as values and as a form; the watcher holds`);
