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
const context = { window: {}, document: { addEventListener() {} }, URL };

vm.runInNewContext(fs.readFileSync(path.join(home, 'pad/lib/validate.js'), 'utf8'), context);

const failed = [];

for (const one of cases) {
  const got = context.window.padValidate.check(one.client, one.data)[one.field] || '';
  if (got !== one.server)
    failed.push(`${one.field} ${JSON.stringify(one.data)}: the server says ${JSON.stringify(one.server)}, the browser ${JSON.stringify(got)}`);
}

if (failed.length) {
  console.error(failed.join('\n'));
  process.exit(1);
}

console.log(`${cases.length} cases judged alike by padValidate and the browser's checker`);
