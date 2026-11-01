// The parity line of ./ci.sh: the browser's checker judges the cases of
// forms/client_parity.php as padValidate does - the same field passes or fails, with the
// same message. The page is rendered on the command line, the checker run in a bare context
// with what it needs of a browser. The checker is the one a page gets: the runtime,
// www/assets/rules/validate.js, with the script of every rule of pad/subs/rules/types/ - written
// by {validator}, so a rule added to the directory is judged here as it is in the page. The
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

const checker = render('forms/client_parity_checker', '{validator/}').match(/<script[^>]*>([\s\S]*)<\/script>/);

if (!checker) {
  console.error('{validator/} wrote no script');
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

const watched = render('forms/client_parity_watch',
  "{form 'trip', client}{input 'delivery'/}{input 'address', rules='required_if:delivery,ship'/}" +
  "{input 'password'/}{input 'confirm', rules='same:password'/}{input 'start'/}{input 'end', rules='after:start'/}{/form}")
  .match(/data-pad-rules="([^"]*)"/);

const watching = [];

if (!watched) watching.push('{form ..., client} wrote no data-pad-rules');
else {
  const listeners = {};
  const byId = {};
  const page = {
    addEventListener(type, each) { (listeners[type] = listeners[type] || []).push(each); },
    getElementById(id) { return byId[id] || null; },
    createElement() { return { id: '', textContent: '', remove() { delete byId[this.id]; } }; }
  };
  const rules = watched[1].replace(/&quot;/g, '"').replace(/&amp;/g, '&');
  const form = { elements: [], hasAttribute(name) { return name === 'data-pad-rules'; }, querySelector() { return null; },
                 getAttribute(name) { return name === 'data-pad-rules' ? rules : null; }, contains(span) { return byId[span.id] === span; } };
  const controls = {};
  [['delivery', 'ship'], ['address', ''], ['password', 'secret'], ['confirm', 'secret'], ['start', '2026-05-01'], ['end', '']].forEach(([name, value]) => {
    const attrs = {};
    form.elements.push(controls[name] = { name, value, id: name, type: 'text', form, focus() {}, closest() { return null; },
      getAttribute(one) { return one in attrs ? attrs[one] : null; }, setAttribute(one, to) { attrs[one] = String(to); },
      removeAttribute(one) { delete attrs[one]; }, insertAdjacentElement(where, span) { byId[span.id] = span; } });
  });
  class PageData {
    constructor(of) { this.entries = of.elements.map(one => [one.name, one.value]); }
    getAll(name) { return this.entries.filter(one => one[0] === name).map(one => one[1]); }
    forEach(each) { this.entries.forEach(one => each(one[1], one[0])); }
  }
  const world = { window: {}, document: page, FormData: PageData };
  vm.runInNewContext(checker[1], world);
  const fire = (type, name, value) => {
    if (value !== undefined) controls[name].value = value;
    (listeners[type] || []).forEach(each => each({ target: controls[name] }));
  };
  const expect = (step, name, text) => {
    const span = byId[name + '-error'];
    const got = span ? span.textContent : '';
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
  if (world.window.padValidate.form(form) !== false)
    watching.push('watcher: the form was sent with an address required when delivery is ship');
  expect('the form sent', 'address', 'Address is required when Delivery is ship');
  fire('change', 'delivery', 'collect');
  expect('the other changed after the form was sent', 'address', '');
}

failed.push(...watching);

if (failed.length) {
  console.error(failed.join('\n'));
  process.exit(1);
}

console.log(`${cases.length} cases judged alike by padValidate and the browser's checker, as values and as a form; the watcher holds`);
