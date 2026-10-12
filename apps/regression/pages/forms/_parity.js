// The parity line of ./ci.sh: the browser's checker, pad/lib/validate.js, judges the cases of
// forms/client_parity.php as padValidate does - the same field passes or fails, with the
// same message. The page is rendered on the command line, the checker run in a bare context
// with what it needs of a browser.

'use strict';

const { execFileSync } = require('child_process');
const fs = require('fs');
const path = require('path');
const vm = require('vm');

const home = path.resolve(__dirname, '../../../..');
const answer = execFileSync('php', [path.join(home, 'editors/render.php'), 'regression/pages', 'forms/client_parity'],
                            { env: Object.assign({}, process.env, { PAD_HOME: home }), encoding: 'utf8' });

const cases = JSON.parse(answer).parity;

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
  Object.keys(one.data).forEach(name => [].concat(one.data[name]).forEach(value => entries.push([name, value])));
  const names = [...new Set(Object.keys(one.client).concat(Object.keys(one.data)))];
  const elements = names.map(name => ({ name, id: name, type: 'text', focus() {}, setAttribute() {}, getAttribute() { return null; },
                                        removeAttribute() {}, insertAdjacentElement() {} }));
  return { entries, elements, hasAttribute() { return true; }, querySelector() { return null; }, contains() { return false; },
           getAttribute(name) { return name === 'data-pad-rules' ? JSON.stringify(one.client) : null; } };
}

const context = { window: {}, document, URL, FormData };

vm.runInNewContext(fs.readFileSync(path.join(home, 'pad/lib/validate.js'), 'utf8'), context);

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

if (failed.length) {
  console.error(failed.join('\n'));
  process.exit(1);
}

console.log(`${cases.length} cases judged alike by padValidate and the browser's checker, as values and as a form`);
