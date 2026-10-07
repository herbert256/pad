// A whole PAD application on PHP compiled to WebAssembly, opened from the disk (file://) - the
// runtime of the page wasm/app.mjs writes. A classic script, not a module: a file:// page may
// not import modules or fetch files beside it, so everything arrives as a <script> that sets
// a global - the PHP runtime (php.js), its binary (php-wasm.js) and the engine with the
// application (pad-bundle.js).
//
//   const pad = await PadApp.start(bundle, PHP, loadPHPRuntime, loader, wasmBinary)
//   await pad.request('GET', '?guestbook')         -> { url, status, headers, html }
//   await pad.request('POST', url, 'name=x&...')    (a redirect is followed, as a browser does)
//
// Every request runs the engine the way www/pad.php does, for the application the bundle
// names, at the address http://localhost/<app>/. Cookies are kept here between requests, so
// sessions, CSRF tokens and flash messages work; what the application writes under DATA/ is
// kept in the browser's localStorage, so a guestbook survives closing the tab.
//
// The same file runs under Node for wasm/verify-app.mjs: no DOM is needed for the requests.

(function (root) {

  'use strict';

  const ROOT   = '/pad';
  const ORIGIN = 'http://localhost';
  const HOPS   = 10;
  const KEEP   = 2 * 1024 * 1024;     // what of DATA/ is saved in localStorage, at most

  const encoder = new TextEncoder();

  function boot(app) {
    return `<?php
  $padHome = '${ROOT}';
  $padApps = '${ROOT}/apps/';
  $padData = '${ROOT}/DATA/';
  $padApp  = '${app}';
  $padRoot = '/';
  if ( in_array ( $_SERVER ['PAD_TIMEZONE'] ?? '', timezone_identifiers_list () ) )
    date_default_timezone_set ( $_SERVER ['PAD_TIMEZONE'] );
  include '${ROOT}/pad/pad.php';
?>`;
  }

  function fromBase64(text) {
    const binary = atob(text);
    const bytes = new Uint8Array(binary.length);
    for (let i = 0; i < binary.length; i++) bytes[i] = binary.charCodeAt(i);
    return bytes;
  }

  function toBase64(bytes) {
    let binary = '';
    for (let i = 0; i < bytes.length; i += 32768)
      binary += String.fromCharCode.apply(null, bytes.subarray(i, i + 32768));
    return btoa(binary);
  }

  function storage() {
    try { return root.localStorage || null; } catch (e) { return null; }
  }

  class PadApp {

    constructor(php, bundle) {
      this.php     = php;
      this.bundle  = bundle;
      this.app     = bundle.app;
      this.base    = `${ORIGIN}/${bundle.app}/`;
      this.cookies = {};
      this.saveKey = `pad-wasm:${bundle.app}:DATA`;
      this.timezone = Intl.DateTimeFormat().resolvedOptions().timeZone || 'UTC';   // the clock shows local time
    }

    static async start(bundle, PHP, loadPHPRuntime, loader, wasmBinary) {
      const options = { phpWasmAsyncMode: 'asyncify', processId: 1 };
      if (wasmBinary) options.wasmBinary = wasmBinary;
      const php = new PHP(await loadPHPRuntime(loader, options));
      const pad = new PadApp(php, bundle);
      pad.install();
      pad.restore();
      return pad;
    }

    // The engine, the application and its www/ directory into PHP's in-memory file system,
    // each file at its path in the repository under /pad.

    install() {
      const made = new Set();
      const write = (name, content) => {
        const path = `${ROOT}/${name}`;
        const dir = path.substring(0, path.lastIndexOf('/'));
        if (!made.has(dir)) { if (!this.php.fileExists(dir)) this.php.mkdirTree(dir); made.add(dir); }
        this.php.writeFile(path, content);
      };
      for (const [name, text] of Object.entries(this.bundle.files)) write(name, text);
      for (const [name, text] of Object.entries(this.bundle.base64 || {})) write(name, fromBase64(text));
      write('DATA/.keep', '');
      write('boot.php', boot(this.app));
    }

    // GET or POST a URL of the application - relative to its address, as a link in its pages
    // is written - and follow the redirects; the answer of the last one is returned.

    async request(method, url, form) {
      let target = new URL(url, this.base);
      for (let hop = 0; hop < HOPS; hop++) {
        const headers = {
          host: 'localhost',
          accept: 'text/html,*/*',
          'user-agent': (root.navigator && root.navigator.userAgent) || 'PAD wasm',
          cookie: Object.entries(this.cookies).map(([k, v]) => `${k}=${v}`).join('; ')
        };
        let body;
        if (method === 'POST') {
          headers['content-type'] = 'application/x-www-form-urlencoded';
          body = encoder.encode(form || '');
        }
        const response = await this.php.run({
          scriptPath: `${ROOT}/boot.php`,
          relativeUri: target.pathname + target.search,
          method, headers, body,
          $_SERVER: {
            REMOTE_ADDR: '127.0.0.1', HTTP_HOST: 'localhost', SERVER_NAME: 'localhost',
            SERVER_PORT: '80', REQUEST_SCHEME: 'http',
            SCRIPT_NAME: `/${this.app}/index.php`,
            SCRIPT_FILENAME: `${ROOT}/www/${this.app}/index.php`,
            PAD_TIMEZONE: this.timezone
          }
        });
        this.takeCookies(response.headers['set-cookie'] || []);
        const location = (response.headers.location || [])[0];
        if (location && response.httpStatusCode >= 300 && response.httpStatusCode < 400) {
          target = new URL(location, target);
          method = 'GET';
          form = null;
          continue;
        }
        this.save();
        return { url: target.href, status: response.httpStatusCode, headers: response.headers, html: response.text, errors: response.errors };
      }
      throw new Error(`more than ${HOPS} redirects from ${url}`);
    }

    takeCookies(lines) {
      for (const line of lines) {
        const [pair, ...attributes] = line.split(';');
        const at = pair.indexOf('=');
        if (at < 1) continue;
        const name = pair.substring(0, at).trim();
        const value = pair.substring(at + 1).trim();
        const gone = value === '' || value === 'deleted' || attributes.some(a => {
          const [k, v] = a.split('=').map(s => s.trim().toLowerCase());
          return (k === 'max-age' && Number(v) <= 0) || (k === 'expires' && Date.parse(v) < Date.now());
        });
        if (gone) delete this.cookies[name]; else this.cookies[name] = value;
      }
    }

    // Is this URL - absolute, or relative to the page at 'from' - one of the application's?

    isMine(url, from) {
      const target = new URL(url, from || this.base);
      return target.origin === ORIGIN && target.pathname.startsWith(`/${this.app}/`);
    }

    // The file of the application's www/ directory a URL names - a stylesheet, an image - or
    // null when it names none (a page).

    asset(url, from) {
      const target = new URL(url, from || this.base);
      if (target.origin !== ORIGIN) return null;
      const name = 'www' + decodeURIComponent(target.pathname);
      if (name.endsWith('/') || name.endsWith('.php')) return null;
      if (name in this.bundle.files) return { name, text: this.bundle.files[name] };
      if (this.bundle.base64 && name in this.bundle.base64) return { name, bytes: fromBase64(this.bundle.base64[name]) };
      return null;
    }

    // What the application wrote under DATA/ - its JSON files, its logs - kept between visits.

    walk(dir, found) {
      for (const name of this.php.listFiles(dir)) {
        const path = `${dir}/${name}`;
        if (this.php.isDir(path)) this.walk(path, found); else found.push(path);
      }
      return found;
    }

    save() {
      const store = storage();
      if (!store) return;
      const kept = {};
      let size = 0;
      for (const path of this.walk(`${ROOT}/DATA`, [])) {
        const bytes = this.php.readFileAsBuffer(path);
        if (bytes.length > 262144 || size + bytes.length > KEEP) continue;
        size += bytes.length;
        kept[path] = toBase64(bytes);
      }
      try { store.setItem(this.saveKey, JSON.stringify(kept)); } catch (e) { /* over the quota: not kept */ }
    }

    restore() {
      const store = storage();
      if (!store) return;
      let kept;
      try { kept = JSON.parse(store.getItem(this.saveKey) || '{}'); } catch (e) { return; }
      for (const [path, text] of Object.entries(kept)) {
        if (!path.startsWith(`${ROOT}/DATA/`)) continue;
        const dir = path.substring(0, path.lastIndexOf('/'));
        if (!this.php.fileExists(dir)) this.php.mkdirTree(dir);
        this.php.writeFile(path, fromBase64(text));
      }
    }

    forget() {
      const store = storage();
      if (store) store.removeItem(this.saveKey);
    }

  }

  root.PadApp = PadApp;
  if (typeof module !== 'undefined' && module.exports) module.exports = PadApp;

})(typeof window !== 'undefined' ? window : globalThis);
