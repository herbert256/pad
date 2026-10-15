// Packs PAD applications into a directory that runs in a browser - PHP compiled to
// WebAssembly, the PAD engine, the applications with their www/ files, and the runtime that
// answers their requests (wasm/app/). One directory, two ways to run it:
//
//   opened from the disk     double-click index.html: file://, no server, no network
//   served over https        (or http://localhost) an installable application: a service
//                            worker answers every request, offline after the first visit
//
// What the applications write under DATA/ - SQLite databases, uploads, sessions - is kept
// in the browser's IndexedDB between visits.
//
//   pad pack notes                                  the same, through the pad command
//   NODE_PATH=~/.cache/pad-wasm/node_modules node wasm/app.mjs notes            DATA/wasm/notes/
//   node wasm/app.mjs notes,ledger,kanban DATA/wasm/offline --name='PAD offline'
//
//   --name=x       the name the installed application has (one application: its own name)
//   --panel=x      button (default): a small badge opens the runtime's panel - database,
//                  storage, requests; key: Ctrl+Shift+K only; none: no panel at all
//   --id=x         where its data is kept - packs of the same id share it (default: the
//                  applications' names, so a new pack of them finds the data of the last)
//
// The packages come from NODE_PATH, else ~/.cache/pad-wasm/node_modules, where
// pad pack --install puts them (wasm/package.json lists them). What it writes:
//
//   index.html        the page: from the disk the pages in a frame, over http(s) the service
//                     worker's start; with several applications a launcher
//   sw.js             the service worker: PHP for every tab of the application, offline
//   pad-app.js        the runtime - requests, cookies, DATA/ kept in IndexedDB
//   pad-panel.js      the panel: database (tables, SQL, export, import), storage, requests
//   pad-apps.js       the applications' names, colours and icons, for the launcher
//   pad-bundle.js     the engine and the applications (wasm/lib/bundle.mjs)
//   php.js            @php-wasm/universal and the PHP 8.4 loader, bundled into a classic script
//   php-wasm.js       the PHP binary, gzipped and base64-encoded (a file:// page cannot fetch)
//   manifest.webmanifest, icons/   what makes it installable
//
// A file:// page may not import a module or fetch a file beside it, which is why each part is
// a classic script setting a global - one the service worker reads with importScripts too.

import { createRequire } from 'module';
import { readFileSync, writeFileSync, mkdirSync, readdirSync, statSync, existsSync, copyFileSync, rmSync } from 'fs';
import { dirname, join, relative } from 'path';
import { fileURLToPath } from 'url';
import { gzipSync } from 'zlib';
import { createHash } from 'crypto';
import { execSync } from 'child_process';
import { homedir } from 'os';
import { home, collect, offline } from './lib/bundle.mjs';

const here    = dirname(fileURLToPath(import.meta.url));
const modules = process.env.NODE_PATH || join(homedir(), '.cache', 'pad-wasm', 'node_modules');
const require = createRequire(join(modules, 'noop.js'));

const args    = process.argv.slice(2);
const options = Object.fromEntries(args.filter(a => a.startsWith('--')).map(a => {
  const [key, ...value] = a.substring(2).split('=');
  return [key, value.length ? value.join('=') : true];
}));
const words   = args.filter(a => !a.startsWith('--'));
const apps    = (words[0] || '').split(',').map(a => a.trim().replace(/^\/+|\/+$/g, '')).filter(Boolean);

if (!apps.length)
  exit('usage: node wasm/app.mjs <app>[,<app>...] [<directory>] [--name=x] [--panel=button|key|none] [--id=x]');

for (const app of apps)
  if (app.startsWith('_') || app.includes('..') || !existsSync(join(home, 'apps', app)) || !statSync(join(home, 'apps', app)).isDirectory())
    exit(`there is no application '${app}' in apps/`);

for (const known of Object.keys(options))
  if (!['name', 'panel', 'id'].includes(known)) exit(`there is no option --${known} - name, panel, id`);

const panel = options.panel || 'button';
if (!['button', 'key', 'none'].includes(panel)) exit(`--panel is button, key or none, not '${panel}'`);

try { require.resolve('@php-wasm/universal'); require.resolve('@php-wasm/web-8-4'); require.resolve('esbuild'); }
catch (e) { exit(`the @php-wasm packages are not in ${modules} - pad pack --install puts them there (npm, from wasm/package.json)`); }

const out = words[1] || join(home, 'DATA', 'wasm', apps.map(a => a.replace(/\//g, '_')).join('+'));
mkdirSync(join(out, 'icons'), { recursive: true });

// The applications and what they say about themselves offline.

const settings = Object.fromEntries(apps.map(app => [app, offline(app)]));
const firstApp = settings[apps[0]];
const name     = String(options.name || (apps.length === 1 ? firstApp.name : 'PAD applications'));
const id       = String(options.id || apps.join('+'));

const { files, base64, left, skipped } = collect(apps);

let commit = '';
try { commit = execSync('git rev-parse --short HEAD', { cwd: home, stdio: ['ignore', 'pipe', 'ignore'] }).toString().trim(); } catch (e) { /* no git */ }

const runtime = ['pad-app.js', 'pad-panel.js', 'sw.js', 'index.html'].map(f => readFileSync(join(here, 'app', f)));
const build   = createHash('sha256').update(JSON.stringify({ files, base64, settings, panel, name, id })).update(Buffer.concat(runtime)).digest('hex').substring(0, 12);
const built   = new Date().toISOString().replace('T', ' ').substring(0, 19) + ' UTC';

const offlineOut = Object.fromEntries(apps.map(app => [app, Object.assign({}, settings[app], { icon: undefined, icons: undefined })]));
const bundle  = { id, name, apps, offline: offlineOut, panel, build, built, commit, files, base64 };
writeFileSync(join(out, 'pad-bundle.js'), 'self.PAD_BUNDLE = ' + JSON.stringify(bundle) + ';\n');

// The launcher's list: each application's name, colours and icon (its SVG as a data URL).

function wwwFile(app, file) {
  const path = join(home, 'www', app, file || '');
  return file && existsSync(path) && statSync(path).isFile() ? path : null;
}

const launcher = apps.map(app => {
  const s = settings[app], svg = wwwFile(app, s.icon);
  return { app, name: s.name, short: s.short, description: s.description, theme: s.theme, background: s.background,
           start: s.start, icon: svg ? 'data:image/svg+xml;base64,' + readFileSync(svg).toString('base64') : '' };
});
writeFileSync(join(out, 'pad-apps.js'), 'self.PAD_APPS = ' + JSON.stringify({ id, name, build, panel, apps: launcher }) + ';\n');

// PHP: the runtime bundled into one classic script, the binary beside it. The loader names
// its .wasm as a URL - new URL(..., import.meta.url) - which a classic script cannot have:
// it is the page's (or the worker's) own address, as nothing fetches it - the binary is
// handed in as wasmBinary.

const esbuild = require('esbuild');
const loader  = join(dirname(require.resolve('@php-wasm/web-8-4')), 'asyncify', 'php_8_4.js');
const binary  = join(dirname(loader), readdirSync(dirname(loader)).find(n => /^8_4_\d+$/.test(n)), 'php_8_4.wasm');
const entry   = join(out, '.php-entry.mjs');

writeFileSync(entry, `
  import { PHP, loadPHPRuntime, __private__dont__use } from ${JSON.stringify(require.resolve('@php-wasm/universal').replace(/index\.cjs$/, 'index.js'))};
  import * as loader from ${JSON.stringify(loader)};
  self.PadPHP = { PHP, loadPHPRuntime, loader, internals: __private__dont__use };
`);

await esbuild.build({
  entryPoints: [entry], outfile: join(out, 'php.js'), bundle: true, format: 'iife',
  platform: 'browser', target: 'es2022', minify: true, legalComments: 'none',
  loader: { '.wasm': 'empty' }, logLevel: 'error',
  define: { 'import.meta.url': 'self.location.href' },
  external: ['worker_threads', 'fs', 'path', 'os', 'crypto', 'child_process', 'url', 'module', 'events']   // Node only, never reached here
});

rmSync(entry, { force: true });

const phpVersion = binary.match(/8_4_(\d+)/)[0].replace(/_/g, '.');
writeFileSync(join(out, 'php-wasm.js'), 'self.PAD_PHP_WASM = "' + gzipSync(readFileSync(binary), { level: 9 }).toString('base64') + '";\n');

// Installable: the manifest and its icons - the application's own when there is one
// application that has them, PAD's otherwise.

const icons = {};
const own   = apps.length === 1 ? firstApp.icons || {} : {};
for (const [size, fallback] of [['192', 'pad-192.png'], ['512', 'pad-512.png'], ['maskable', 'pad-maskable-512.png']]) {
  const source = wwwFile(apps[0], own[size]) || join(here, 'app', 'icons', fallback);
  copyFileSync(source, join(out, 'icons', `icon-${size}.png`));
  icons[size] = `icons/icon-${size}.png`;
}
const svgIcon = (apps.length === 1 && wwwFile(apps[0], firstApp.icon)) || join(here, 'app', 'icons', 'pad.svg');
copyFileSync(svgIcon, join(out, 'icons', 'icon.svg'));

writeFileSync(join(out, 'manifest.webmanifest'), JSON.stringify({
  name, short_name: apps.length === 1 ? firstApp.short : 'PAD',
  description: apps.length === 1 ? firstApp.description : `${apps.map(a => settings[a].name).join(', ')} - PAD applications that run in the browser`,
  id: './', start_url: './', scope: './', display: 'standalone',
  theme_color: firstApp.theme, background_color: firstApp.background,
  icons: [
    { src: icons['192'], sizes: '192x192', type: 'image/png' },
    { src: icons['512'], sizes: '512x512', type: 'image/png' },
    { src: icons.maskable, sizes: '512x512', type: 'image/png', purpose: 'maskable' },
    { src: 'icons/icon.svg', sizes: 'any', type: 'image/svg+xml' }
  ]
}, null, 2) + '\n');

// The page, the worker and the runtime.

const html = s => String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');

writeFileSync(join(out, 'index.html'), readFileSync(join(here, 'app', 'index.html'), 'utf8')
  .split('__TITLE__').join(html(name)).split('__THEME__').join(html(firstApp.theme)).split('__BACKGROUND__').join(html(firstApp.background)));

const precache = ['./', 'index.html', 'pad-apps.js', 'pad-bundle.js', 'pad-app.js', 'pad-panel.js', 'php.js', 'php-wasm.js',
                  'manifest.webmanifest', 'icons/icon.svg', ...Object.values(icons)];

writeFileSync(join(out, 'sw.js'), readFileSync(join(here, 'app', 'sw.js'), 'utf8')
  .split('__BUILD__').join(build).split('__FILES__').join(JSON.stringify(precache)));

copyFileSync(join(here, 'app', 'pad-app.js'), join(out, 'pad-app.js'));
copyFileSync(join(here, 'app', 'pad-panel.js'), join(out, 'pad-panel.js'));

writeFileSync(join(out, 'README.txt'), `${name} - PAD in the browser
${'='.repeat(name.length + 22)}

${apps.length === 1 ? 'The PAD application' : 'The PAD applications'} ${apps.join(', ')}, with PHP ${phpVersion} compiled to WebAssembly
and the PAD engine, running in the browser - no PHP server.

  From the disk     open index.html (double-click it). No server and no network are needed.
  Installable       put this directory on any https web server (or serve it on this machine:
                    php -S 127.0.0.1:8090 -t .) and open it: a service worker answers every
                    request, the browser offers to install it, and it works offline after
                    the first visit.

What the applications write - their SQLite databases, uploads, sessions - is kept in the
browser's IndexedDB; clearing the browser's site data forgets it. ${panel === 'none' ? '' : `The panel (${panel === 'key' ? 'Ctrl+Shift+K' : 'the round badge in a corner, or Ctrl+Shift+K'})
exports and imports the databases and makes a backup of all of it.`}

Built ${built} from the PAD repository${commit ? ' at ' + commit : ''} with wasm/app.mjs (pad pack), build ${build}.
`);

const size = n => (statSync(join(out, n)).size / 1048576).toFixed(1) + ' MB';
console.log(`${relative(process.cwd(), out) || '.'}/: ${apps.join(', ')} - ${Object.keys(files).length + Object.keys(base64).length} files with the engine (${left} large sequence tables left out), build ${build}`);
console.log(`  pad-bundle.js ${size('pad-bundle.js')}, php.js ${size('php.js')}, php-wasm.js ${size('php-wasm.js')} (PHP ${phpVersion})`);
if (skipped.length) console.log(`  not packed: ${skipped.join(', ')}`);

function exit(message) {
  console.error(message);
  process.exit(1);
}
