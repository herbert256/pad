// The browser's checker of the form rules in a real browser, headless Chrome over CDP - by
// hand, as the suites run no browser; the validate line of ./ci.sh (_parity.js) walks the
// same cases and the same steps in Node with what the checker needs of a page.
//
//   node apps/regression/pages/forms/_chrome.mjs [http://localhost/pad/] [9222]
//
// Chrome listening on that DevTools port first (node v22 and later have WebSocket):
//   "/Applications/Google Chrome.app/Contents/MacOS/Google Chrome" --headless=new \
//     --remote-debugging-port=9222 --user-data-dir=/tmp/pad-chrome about:blank
//
// 1. Every case of forms/client_parity - the shared table of forms/_rules.php among them -
//    judged by padValidate.check in the page, and the cases of the rules that look at other
//    fields again as a real <form data-pad-rules> - hidden fields, FormData,
//    padValidate.form - each held to the server's message.
// 2. The watcher on forms/a_client_form_names_the_fields_its_rules_look_at, a {form ...,
//    client} of a template: real change, input and focusout events, the messages read from
//    the page.

const base = process.argv[2] || 'http://localhost/pad/';
const port = process.argv[3] || '9222';
const page = base + 'regression/pages/?forms/a_client_form_names_the_fields_its_rules_look_at';

const target = await (await fetch('http://127.0.0.1:' + port + '/json/new?' + encodeURIComponent(page), { method: 'PUT' })).json();
const ws = new WebSocket(target.webSocketDebuggerUrl);
await new Promise(ok => ws.addEventListener('open', ok));

let id = 0;
const waiting = {};
ws.addEventListener('message', event => {
  const message = JSON.parse(event.data);
  if (message.id && waiting[message.id]) { waiting[message.id](message); delete waiting[message.id]; }
});
const send = (method, params = {}) => new Promise(ok => { const at = ++id; waiting[at] = ok; ws.send(JSON.stringify({ id: at, method, params })); });
const run = async (fn, ...args) => {
  const answer = await send('Runtime.evaluate', { expression: `(${fn})(...${JSON.stringify(args)})`, awaitPromise: true, returnByValue: true });
  if (answer.result.exceptionDetails) throw new Error(JSON.stringify(answer.result.exceptionDetails));
  return answer.result.result.value;
};

for (let i = 0; i < 50 && !(await run(() => !!(window.padValidate && document.readyState === 'complete'))); i++)
  await new Promise(ok => setTimeout(ok, 100));

const failed = [];

// 1. The table.
const table = await run(async (base) => {
  const cases = (await (await fetch(base + 'regression/pages/?forms/client_parity&padInclude')).json()).parity;
  const ours = /^(required_if|required_unless|required_with|required_without|before|after|not_in|same)$/;
  const wrong = [];
  let forms = 0;
  cases.forEach(one => {
    const got = window.padValidate.check(one.client, one.data)[one.field] || '';
    if (got !== one.server) wrong.push(`${one.field} ${JSON.stringify(one.data)}: server ${JSON.stringify(one.server)}, browser ${JSON.stringify(got)}`);
    const spec = one.client[one.field];
    if (!spec.rules.some(rule => ours.test(rule[0]))) return;
    if (Array.isArray(one.data[one.field]) && !spec.list) return;
    forms++;
    const form = document.createElement('form');
    form.setAttribute('data-pad-rules', JSON.stringify(one.client));
    form.setAttribute('novalidate', '');
    const add = (name, value) => {
      const input = document.createElement('input');
      input.type = 'hidden'; input.name = name; input.value = value;
      form.appendChild(input);
    };
    Object.keys(one.data).forEach(name => {
      const value = one.data[name];
      if (Array.isArray(value)) value.forEach(item => add(name, item));
      else if (typeof value === 'string') add(name, value);
    });
    if (!form.querySelector(`[name="${CSS.escape(one.field)}"]`)) {
      const box = document.createElement('input');
      box.type = 'checkbox'; box.name = one.field;
      form.appendChild(box);
    }
    document.body.appendChild(form);
    const sent = window.padValidate.form(form);
    const span = form.querySelector('span.error');
    const shown = span ? span.textContent : '';
    if (sent !== (one.server === '') || shown !== one.server)
      wrong.push(`${one.field} ${JSON.stringify(one.data)} as a form: server ${JSON.stringify(one.server)}, browser ${JSON.stringify(shown)}`);
    form.remove();
  });
  return { count: cases.length, forms, wrong };
}, base);

failed.push(...table.wrong);

// 2. The watcher.
const watcher = await run(() => {
  const form = document.querySelector('form[data-pad-rules]');
  const field = name => form.elements[name];
  const wrong = [];
  const fire = (name, type, value, checked) => {
    const one = field(name);
    if (value !== undefined) one.value = value;
    if (checked !== undefined) one.checked = checked;
    one.dispatchEvent(new Event(type, { bubbles: true }));
  };
  const leave = name => field(name).dispatchEvent(new FocusEvent('focusout', { bubbles: true }));
  const expect = (step, name, text) => {
    const span = document.getElementById(name + '-error');
    const got = span ? span.textContent : '';
    const invalid = field(name).getAttribute('aria-invalid') === 'true';
    if (got !== text || invalid !== (text !== '')) wrong.push(`${step}: ${name} shows ${JSON.stringify(got)}${invalid ? ' (aria-invalid)' : ''}, not ${JSON.stringify(text)}`);
  };
  fire('delivery', 'change', 'ship');
  expect('a field never left is not checked when the other changes', 'address', '');
  leave('address');
  expect('a field left empty is not checked', 'address', '');
  fire('delivery', 'change', 'collect');
  fire('delivery', 'change', 'ship');
  expect('the other changed to the value', 'address', 'Address is required when Delivery is ship');
  fire('delivery', 'change', 'collect');
  expect('the other changed away from it', 'address', '');
  fire('password', 'input', 'secret');
  fire('confirm', 'input', 'secret');
  leave('confirm');
  expect('the confirmation matching', 'confirm', '');
  fire('password', 'input', 'secret2');
  expect('the password typed again', 'confirm', 'Confirm must be the same as Password');
  fire('password', 'input', 'secret');
  expect('the password typed back', 'confirm', '');
  fire('start', 'input', '2026-05-01');
  fire('end', 'input', '2026-04-01');
  leave('end');
  expect('an end before the start', 'end', 'End must be a date after Start');
  fire('start', 'change', '2026-03-01');
  expect('the start moved before it', 'end', '');
  leave('card');
  fire('kinds[]', 'change', undefined, true);
  expect('the gift box ticked', 'card', 'Card text is required when Kinds is gift');
  fire('kinds[]', 'change', undefined, false);
  expect('the gift box unticked', 'card', '');
  fire('delivery', 'change', 'ship');
  let stopped = false;
  form.addEventListener('submit', event => { stopped = event.defaultPrevented; event.preventDefault(); });
  form.requestSubmit();
  expect('the form sent', 'address', 'Address is required when Delivery is ship');
  if (document.activeElement !== field('address')) wrong.push('the form sent: the focus is not on the address');
  if (!form.querySelector('.error[role="alert"]')) wrong.push('the form sent: no message above the fields');
  fire('delivery', 'change', 'collect');
  expect('the other changed after the form was sent', 'address', '');
  return wrong;
});

failed.push(...watcher.map(one => 'watcher, ' + one));

await send('Page.close');
ws.close();

if (failed.length) {
  console.error(failed.join('\n'));
  process.exit(1);
}

console.log(`Chrome: ${table.count} cases judged alike by padValidate and the page's checker, ${table.forms} of them as a real form too; the watcher holds`);
