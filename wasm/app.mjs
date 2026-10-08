// Packs a whole PAD application into a directory that runs in a browser opened from the disk -
// file://, no server, no network: PHP compiled to WebAssembly, the PAD engine, the application
// with its www/ files, and the page that answers its links and forms (wasm/app/).
//
//   npm install --prefix /tmp/pad-wasm @php-wasm/universal @php-wasm/web-8-4 esbuild
//   NODE_PATH=/tmp/pad-wasm/node_modules node wasm/app.mjs demo           DATA/wasm/demo/
//   NODE_PATH=/tmp/pad-wasm/node_modules node wasm/app.mjs demo out/demo  another directory
//
// Then open DATA/wasm/demo/index.html in the browser. What it writes:
//
//   index.html       the page: a frame filling the window that the application's pages show in
//   pad-app.js       the runtime - requests, cookies, redirects, DATA/ kept in localStorage
//   php.js           @php-wasm/universal and the PHP 8.4 loader, bundled into a classic script
//   php-wasm.js      the PHP binary, gzipped and base64-encoded (a file:// page cannot fetch)
//   pad-bundle.js    pad/, apps/<app>/, apps/_common/ and www/<app>/ - the same files and
//                    limits as wasm/build.php: no .sqlite, nothing over 64 KB
//
// A file:// page may not import a module or fetch a file beside it, which is why each part is
// a classic <script> setting a global.

import { createRequire } from 'module';
import { readFileSync, writeFileSync, mkdirSync, readdirSync, statSync, existsSync, copyFileSync } from 'fs';
import { dirname, join, relative } from 'path';
import { fileURLToPath } from 'url';
import { gzipSync } from 'zlib';
import { execSync } from 'child_process';

const here    = dirname(fileURLToPath(import.meta.url));
const home    = dirname(here);
const require = createRequire(join(process.env.NODE_PATH || process.cwd(), 'noop.js'));

const app = process.argv[2];
if (!app || !existsSync(join(home, 'apps', app)))
  exit(`usage: node wasm/app.mjs <app> [<directory>] - there is no application '${app || ''}' in apps/`);

const out = process.argv[3] || join(home, 'DATA', 'wasm', app);
mkdirSync(out, { recursive: true });

// The files, keyed by their path in the repository.

const files  = {};
const base64 = {};
let left = 0;

function collect(dir, keep = () => true) {
  if (!existsSync(dir)) return;
  for (const name of readdirSync(dir)) {
    const path = join(dir, name);
    const info = statSync(path);
    if (info.isDirectory()) { collect(path, keep); continue; }
    const key = relative(home, path).split('\\').join('/');
    if (name === '.DS_Store' || !keep(key)) continue;
    if (name.endsWith('.sqlite') || info.size > 65536) { left++; continue; }
    const bytes = readFileSync(path);
    const text = bytes.toString('utf8');
    if (Buffer.from(text, 'utf8').equals(bytes)) files[key] = text; else base64[key] = bytes.toString('base64');
  }
}

collect(join(home, 'pad'));
collect(join(home, 'apps', app));
collect(join(home, 'apps', '_common'));
collect(join(home, 'www', app), key => !key.endsWith('.php'));

let commit = '';
try { commit = execSync('git rev-parse --short HEAD', { cwd: home }).toString().trim(); } catch (e) { /* no git */ }

const bundle = { app, built: new Date().toISOString().replace('T', ' ').substring(0, 19) + ' UTC', commit, files, base64 };
writeFileSync(join(out, 'pad-bundle.js'), 'window.PAD_BUNDLE = ' + JSON.stringify(bundle) + ';\n');

// PHP: the runtime bundled into one classic script, the binary beside it. The loader names
// its .wasm as a URL - an import, or new URL(..., import.meta.url) from @php-wasm 3.1.57 on;
// the import is left empty and import.meta.url is the page's address, as nothing fetches
// it: the binary is handed in as wasmBinary.

const esbuild  = require('esbuild');
const loader   = join(dirname(require.resolve('@php-wasm/web-8-4')), 'asyncify', 'php_8_4.js');
const binary   = join(dirname(loader), readdirSync(dirname(loader)).find(name => /^8_4_\d+$/.test(name)), 'php_8_4.wasm');
const entry    = join(out, '.php-entry.mjs');

writeFileSync(entry, `
  import { PHP, loadPHPRuntime } from ${JSON.stringify(require.resolve('@php-wasm/universal').replace(/index\.cjs$/, 'index.js'))};
  import * as loader from ${JSON.stringify(loader)};
  window.PadPHP = { PHP, loadPHPRuntime, loader };
`);

await esbuild.build({
  entryPoints: [entry], outfile: join(out, 'php.js'), bundle: true, format: 'iife',
  platform: 'browser', target: 'es2022', minify: true, legalComments: 'none',
  loader: { '.wasm': 'empty' }, logLevel: 'error',
  define: { 'import.meta.url': 'location.href' },   // a classic script has no import.meta: the loader's
                                                    // new URL('./.../php_8_4.wasm', import.meta.url) threw
  external: [ 'worker_threads', 'fs', 'path', 'os', 'crypto', 'child_process', 'url', 'module', 'events' ]   // Node only, never reached here
});

execSync(`rm -f ${JSON.stringify(entry)}`);

writeFileSync(join(out, 'php-wasm.js'), 'window.PAD_PHP_WASM = "' + gzipSync(readFileSync(binary), { level: 9 }).toString('base64') + '";\n');

// The page and its runtime.

writeFileSync(join(out, 'index.html'), readFileSync(join(here, 'app', 'index.html'), 'utf8').split('__APP__').join(app));
copyFileSync(join(here, 'app', 'pad-app.js'), join(out, 'pad-app.js'));

writeFileSync(join(out, 'README.txt'), `PAD application '${app}' in the browser
${'='.repeat(30 + app.length)}

Open index.html in a browser (double-click it) - no web server and no network are needed.
PHP ${binary.match(/8_4_(\d+)/)[0].replace(/_/g, '.')} compiled to WebAssembly runs in the tab, with the PAD engine and the
application in its memory.

What the application writes under DATA/ is kept in the browser's localStorage; clearing
the browser's site data forgets it. Built ${bundle.built} from the PAD repository${commit ? ' at ' + commit : ''} with wasm/app.mjs.
`);

const size = name => (statSync(join(out, name)).size / 1048576).toFixed(1) + ' MB';
console.log(`${relative(process.cwd(), out) || '.'}/: ${Object.keys(files).length + Object.keys(base64).length} files of '${app}' and the engine (${left} large ones left out)`);
console.log(`  pad-bundle.js ${size('pad-bundle.js')}, php.js ${size('php.js')}, php-wasm.js ${size('php-wasm.js')}`);

function exit(message) {
  console.error(message);
  process.exit(1);
}
