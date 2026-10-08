// Checks the runtime of wasm/app.mjs outside a browser: loads @php-wasm/node, installs the
// pad-bundle.js wasm/app.mjs wrote for an application with the same wasm/app/pad-app.js the
// page uses, and walks the demo application - pages, a post with its CSRF token through the
// redirect to the flash message, a form that breaks its rules.
//
//   NODE_PATH=/tmp/pad-wasm/node_modules node wasm/app.mjs demo
//   npm install --prefix /tmp/pad-wasm @php-wasm/node
//   NODE_PATH=/tmp/pad-wasm/node_modules node wasm/verify-app.mjs [<directory>]   (DATA/wasm/demo/)
//
// Exits 0 when every step answers as expected.

import { createRequire } from 'module';
import { readFileSync } from 'fs';
import { dirname, join } from 'path';
import { fileURLToPath, pathToFileURL } from 'url';
import vm from 'vm';

const here    = dirname(fileURLToPath(import.meta.url));
const require = createRequire(join(process.env.NODE_PATH || process.cwd(), 'noop.js'));
const { PHP, loadPHPRuntime } = await import(pathToFileURL(require.resolve('@php-wasm/universal')).href);
const { loadNodeRuntime } = await import(pathToFileURL(require.resolve('@php-wasm/node')).href);

const dir = process.argv[2] || join(here, '../DATA/wasm/demo');
const sandbox = { window: {} };
vm.runInNewContext(readFileSync(join(dir, 'pad-bundle.js'), 'utf8'), sandbox);
const PadApp = require(join(here, 'app', 'pad-app.js'));

const pad = new PadApp(new PHP(await loadNodeRuntime('8.4', { emscriptenOptions: { processId: 1 } })), sandbox.window.PAD_BUNDLE);
pad.install();

let failed = 0;

function check(label, ok, answer) {
  if (!ok) failed++;
  console.log(`${ok ? 'ok  ' : 'FAIL'} ${label}${ok ? '' : '\n     HTTP ' + answer.status + ' ' + answer.html.slice(0, 300).replace(/\s+/g, ' ')}`);
}

let answer = await pad.request('GET', '');
check('the home page', answer.status === 200 && /<h1>Welcome to PAD Demo<\/h1>/.test(answer.html), answer);

answer = await pad.request('GET', '?guestbook');
const token = (answer.html.match(/name="padCsrfToken" value="([0-9a-f]+)"/) || [])[1];
check('a form carries the CSRF token', !!token, answer);

answer = await pad.request('POST', '?guestbook', `padCsrfToken=${token}&action=add&name=Ann&comment=Offline`);
check('a post is redirected to the flash message', answer.url.endsWith('?guestbook') && /Thank you for signing/.test(answer.html) && /Offline/.test(answer.html), answer);

answer = await pad.request('POST', '?guestbook', 'action=add&name=Bob&comment=x');
check('a post without the token is refused', answer.status === 403, answer);

answer = await pad.request('GET', '?contact');
const again = (answer.html.match(/name="padCsrfToken" value="([0-9a-f]+)"/) || [])[1];
answer = await pad.request('POST', '?contact', `padCsrfToken=${again}&padForm=contact&name=Ann`);
check('a form that breaks its rules comes back with messages', /is required/.test(answer.html), answer);

answer = await pad.request('GET', '?nosuchpage');
check('a page that does not exist', answer.status === 404, answer);

console.log(`${failed ? 'not all' : 'all'} steps as expected`);
process.exit(failed ? 1 : 0);
