// The service worker of a packed PAD application (wasm/app.mjs, pad pack), for the pack served
// over https or on localhost: PHP compiled to WebAssembly runs here, in the worker, and answers
// every request of the pack's address - the pages, the www/ files, the fetches, posts, uploads
// and event streams of the pages' own JavaScript - the way a PHP server would, offline as well.
// The architecture of WordPress Playground, with PHP in the worker itself: one PHP for every
// tab, so two windows of the application see the same SQLite database.
//
// The pack's files are kept in the worker's cache when it installs - the PHP runtime with
// importScripts, so it is there when the browser starts the worker again - and PHP starts when
// the first request needs it. What the applications write under DATA/ is kept in IndexedDB by
// the runtime (pad-app.js) after every request: a browser may stop an idle worker at any time,
// and the next request starts PHP again with the data as it was.
//
// A page of the application gets pad-panel.js: it keeps this worker awake while the page is
// open (a ping every 20 seconds), shows the panel, and offers a new build of the pack when one
// is waiting. Written by wasm/app.mjs, which fills in the build and the list of files.

'use strict';

const BUILD   = '__BUILD__';
const FILES   = __FILES__;
const RUNTIME = ['php.js', 'php-wasm.js', 'pad-bundle.js', 'pad-app.js'];
const CACHE   = `pad-offline-${BUILD}`;
const SCOPE   = new URL(self.registration.scope);
const SHELL   = new Set(FILES.concat(RUNTIME).map(file => new URL(file, SCOPE).pathname));

let pad = null;
let starting = null;
let failure = null;

self.addEventListener('install', event => {
  importScripts(...RUNTIME);
  event.waitUntil((async () => {
    const cache = await caches.open(CACHE);
    let done = 0;
    for (const file of FILES) {
      const url = new URL(file, SCOPE);
      const response = await fetch(new Request(url, { cache: 'reload' }));
      if (!response.ok) throw new Error(`${file} answered HTTP ${response.status}`);
      await cache.put(url, response);
      tell({ type: 'pad-progress', done: ++done, total: FILES.length, file });
    }
    if (!self.registration.active) self.skipWaiting();     // the first one starts at once; a new build waits to be asked
  })());
});

self.addEventListener('activate', event => {
  event.waitUntil((async () => {
    for (const key of await caches.keys())
      if (key.startsWith('pad-offline-') && key !== CACHE) await caches.delete(key);
    await self.clients.claim();
    tell({ type: 'pad-activated', build: BUILD });
  })());
});

self.addEventListener('message', event => {
  const data = event.data || {};
  if (data.type === 'pad-activate') self.skipWaiting();
  if (data.type === 'pad-ping' || data.type === 'pad-boot') event.waitUntil(data.type === 'pad-boot' ? start().catch(() => {}) : Promise.resolve());
});

self.addEventListener('fetch', event => {
  const url = new URL(event.request.url);
  if (url.origin !== SCOPE.origin || !url.pathname.startsWith(SCOPE.pathname)) return;
  if (url.pathname === SCOPE.pathname || SHELL.has(url.pathname)) event.respondWith(shell(event.request, url));
  else event.respondWith(answer(event.request));
});

// The pack's own files: from the cache, the network when the cache has none.

async function shell(request, url) {
  const cache = await caches.open(CACHE);
  const key = url.pathname === SCOPE.pathname ? new URL('index.html', SCOPE) : new URL(url.pathname, SCOPE);
  return (await cache.match(key)) || fetch(request);
}

function start() {
  if (!starting) starting = boot().catch(error => { failure = error; starting = null; throw error; });
  return starting;
}

async function boot() {
  const began = Date.now();
  if (!self.PAD_PHP_WASM || !self.PadApp) importScripts(...RUNTIME);
  const text = self.PAD_PHP_WASM;
  self.PAD_PHP_WASM = null;
  const gzipped = new Uint8Array(text.length);
  const binary = atob(text);
  for (let i = 0; i < binary.length; i++) gzipped[i] = binary.charCodeAt(i);
  const stream = new Blob([gzipped.subarray(0, binary.length)]).stream().pipeThrough(new DecompressionStream('gzip'));
  const wasmBinary = new Uint8Array(await new Response(stream).arrayBuffer());
  pad = await self.PadApp.start(self.PAD_BUNDLE, Object.assign({}, self.PadPHP, { wasmBinary }),
                                { mode: 'pwa', origin: SCOPE.origin, scope: SCOPE.pathname });
  failure = null;
  console.log(`PAD ${self.PAD_BUNDLE.build} started in the service worker in ${Date.now() - began} ms`);
  return pad;
}

// A request of the application: PHP's answer, as a Response. A page asked for while PHP is
// still starting gets a short page of its own that waits for it and then loads again.

async function answer(request) {
  const navigate = request.mode === 'navigate';
  const url = new URL(request.url);
  if (!pad) {
    if (url.pathname === `${SCOPE.pathname}_pad/state`)
      return json({ booting: true, saving: false, error: failure ? String(failure.message || failure) : '', online: navigator.onLine });
    const ready = start();
    if (navigate && !(await Promise.race([ready.then(() => true, () => true), wait(600)]))) return splash();
    try { await ready; } catch (error) { return fatal(error); }
  }
  const body = request.method === 'GET' || request.method === 'HEAD' ? undefined : new Uint8Array(await request.arrayBuffer());
  let result;
  try {
    result = await pad.handle({ method: request.method, url: request.url, headers: request.headers, body, kind: navigate ? 'page' : '' });
  } catch (error) {
    return fatal(error);
  }
  const headers = new Headers();
  for (const [name, values] of Object.entries(result.headers || {}))
    for (const value of [].concat(values)) { try { headers.append(name, value); } catch (e) { /* a header a Response may not carry */ } }
  headers.delete('content-length');
  headers.delete('content-encoding');
  let bytes = result.bytes;
  if (navigate && /^text\/html/i.test(headers.get('content-type') || ''))
    bytes = inject(bytes);
  const status = result.status < 200 || result.status > 599 ? 200 : result.status;
  if (status >= 300 && status < 400 && headers.has('location'))
    headers.set('location', new URL(headers.get('location'), request.url).href);
  return new Response([101, 103, 204, 205, 304].includes(status) || request.method === 'HEAD' ? null : bytes, { status, headers });
}

// The panel's script, last in the page's body - the badge, the keep-alive, the update offer -
// and the manifest in its head, which makes every page of the pack one the browser offers to
// install, not only the launcher.

function inject(bytes) {
  let html = new TextDecoder().decode(bytes);
  const lower = html.toLowerCase();
  const tag = `<script src="${SCOPE.pathname}pad-panel.js" data-pad-scope="${SCOPE.pathname}" data-pad-panel="${self.PAD_BUNDLE.panel}" defer></script>`;
  const at = lower.lastIndexOf('</body>');
  html = at < 0 ? html + tag : html.substring(0, at) + tag + html.substring(at);
  const head = lower.indexOf('</head>');
  if (head >= 0 && !/<link[^>]+rel=["']?manifest/i.test(html.substring(0, head)))
    html = html.substring(0, head) + `<link rel="manifest" href="${SCOPE.pathname}manifest.webmanifest">` + html.substring(head);
  return new TextEncoder().encode(html);
}

function splash() {
  return new Response(`<!DOCTYPE html>
<html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Starting ${escape(self.PAD_BUNDLE.name)}</title>
<style>
  :root { color-scheme: light dark; --back: #f8fafc; --text: #0f172a; --dim: #64748b; }
  @media (prefers-color-scheme: dark) { :root { --back: #0b1120; --text: #e2e8f0; --dim: #94a3b8; } }
  html, body { height: 100%; margin: 0; }
  body { display: grid; place-items: center; background: var(--back); color: var(--text); font: 15px/1.5 system-ui, sans-serif; }
  .box { text-align: center; }
  .ring { width: 56px; height: 56px; margin: 0 auto 18px; border-radius: 50%; border: 4px solid color-mix(in srgb, var(--dim) 25%, transparent);
          border-top-color: #6366f1; animation: spin 0.9s linear infinite; }
  p { margin: 4px 0; } small { color: var(--dim); }
  @keyframes spin { to { transform: rotate(1turn); } }
</style></head>
<body><div class="box"><div class="ring"></div><p>Starting PHP in your browser</p><small>${escape(self.PAD_BUNDLE.name)} runs here, without a server</small></div>
<script>fetch('${SCOPE.pathname}_pad/ready', { cache: 'no-store' }).then(() => location.reload(), () => setTimeout(() => location.reload(), 1000));</script>
</body></html>`, { status: 200, headers: { 'content-type': 'text/html; charset=utf-8', 'cache-control': 'no-store' } });
}

function fatal(error) {
  console.error(error);
  return new Response(`PAD could not answer this request: ${error && error.message || error}`, { status: 500, headers: { 'content-type': 'text/plain; charset=utf-8' } });
}

function json(value) {
  return new Response(JSON.stringify(value), { headers: { 'content-type': 'application/json', 'cache-control': 'no-store' } });
}

function escape(text) {
  return String(text).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
}

function wait(ms) {
  return new Promise(resolve => setTimeout(() => resolve(false), ms));
}

async function tell(message) {
  for (const client of await self.clients.matchAll({ includeUncontrolled: true })) client.postMessage(message);
}
