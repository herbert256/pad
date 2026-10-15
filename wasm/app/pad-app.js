// PAD applications on PHP compiled to WebAssembly - the runtime of a directory wasm/app.mjs
// (pad pack) writes. It runs in three places:
//
//   the page opened from the disk (index.html, file://) - PHP in the tab, the pages in a frame
//   the service worker (sw.js) of the same directory served over https or localhost - an
//     installable application whose every request the worker answers, offline as well
//   Node, for wasm/verify-app.mjs - no DOM is needed for the requests
//
// A classic script, not a module: a file:// page may not import modules or fetch files beside
// it, so everything arrives as a <script> (importScripts in the worker) that sets a global on
// self - the PHP runtime (php.js), its binary (php-wasm.js), the engine with the applications
// (pad-bundle.js) and this.
//
//   const pad = await PadApp.start(bundle, { PHP, loadPHPRuntime, loader, internals, wasmBinary }, options)
//   await pad.handle({ method, url, headers, body })   -> { status, headers, bytes, url }
//   await pad.request('POST', '?guestbook', 'name=x')  -> the same, a redirect followed, with .text
//
// Every request runs the engine the way www/pad.php does, for the application its address
// names - a pack may hold several, each at <scope><app>/. What a request needs from a server
// is here: cookies are kept (sessions, CSRF tokens, flash messages, a remember-me), any
// method and header of the page's own JavaScript is passed on, a multipart body brings its
// uploads, an answer may be binary. The application's www/ files are answered from the
// bundle, the runtime's own calls under <scope>_pad/ (the panel's database and storage tools).
//
// What the applications write under DATA/ - SQLite databases, uploads, JSON files, sessions -
// is kept in IndexedDB (localStorage when there is none), the files that changed written after
// every request, and put back when the runtime starts: an application keeps its data between
// visits. Several tabs of the same pack opened from the disk tell each other what they wrote.

(function (root) {

  'use strict';

  const ROOT = '/pad';
  const DATA = `${ROOT}/DATA`;
  const TOOL = `${ROOT}/runtime`;
  const HOPS = 10;
  const LOG  = 200;

  const encoder = new TextEncoder();
  const decoder = new TextDecoder();

  const MIME = {
    css: 'text/css', js: 'text/javascript', mjs: 'text/javascript', json: 'application/json', map: 'application/json',
    png: 'image/png', jpg: 'image/jpeg', jpeg: 'image/jpeg', gif: 'image/gif', svg: 'image/svg+xml', webp: 'image/webp',
    avif: 'image/avif', ico: 'image/x-icon', woff: 'font/woff', woff2: 'font/woff2', ttf: 'font/ttf', otf: 'font/otf',
    txt: 'text/plain', html: 'text/html', htm: 'text/html', xml: 'application/xml', pdf: 'application/pdf', csv: 'text/csv',
    mp3: 'audio/mpeg', ogg: 'audio/ogg', wav: 'audio/wav', mp4: 'video/mp4', webm: 'video/webm', wasm: 'application/wasm',
    webmanifest: 'application/manifest+json', md: 'text/markdown', zip: 'application/zip'
  };

  // Request headers the runtime sets itself, or that would make PHP answer what the runtime
  // cannot read (a gzipped body).

  const OWN = new Set(['host', 'cookie', 'accept-encoding', 'content-length', 'connection', 'keep-alive',
                       'transfer-encoding', 'upgrade', 'te', 'origin', 'referer']);

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

  function bytesOf(body) {
    if (body === undefined || body === null) return undefined;
    if (body instanceof Uint8Array) return body;
    if (body instanceof ArrayBuffer) return new Uint8Array(body);
    if (ArrayBuffer.isView(body)) return new Uint8Array(body.buffer, body.byteOffset, body.byteLength);
    return encoder.encode(String(body));
  }

  function mimeOf(name) {
    const type = MIME[name.split('.').pop().toLowerCase()] || 'application/octet-stream';
    return /^(text\/|application\/(json|javascript|xml|manifest))|svg/.test(type) ? `${type}; charset=utf-8` : type;
  }

  // Request headers given as an object or as a Headers, as [name, value] pairs.

  function headerList(headers) {
    if (!headers) return [];
    if (Array.isArray(headers)) return headers;
    if (typeof headers.get === 'function' && typeof headers.entries === 'function') return Array.from(headers.entries());
    return Object.entries(headers);
  }

  function first(headers, name) {
    const value = headers && headers[name];
    return Array.isArray(value) ? value[0] : value;
  }

  // A small, stable hash of a file's bytes - the change mark of a file system that has no
  // modification times to read.

  function hash(bytes) {
    let h = 0x811c9dc5 ^ bytes.length;
    for (let i = 0; i < bytes.length; i++) h = Math.imul(h ^ bytes[i], 0x01000193);
    return (h >>> 0).toString(36) + ':' + bytes.length;
  }

  // ---- Where DATA/ is kept ------------------------------------------------------------
  //
  // Three stores with one face: load() all files of the pack, write(put, removed) in one
  // go, getMeta/setMeta for the cookies, clear(). The files are keyed by their path under
  // DATA/ and by the pack, so the packs of one origin - every file:// page shares one - keep
  // to their own.

  class MemoryStore {

    constructor() { this.kind = 'memory'; this.files = new Map(); this.meta = new Map(); }
    async load() { return new Map(this.files); }
    async write(put, removed) {
      for (const [path, bytes] of put) this.files.set(path, bytes.slice());
      for (const path of removed) this.files.delete(path);
    }
    async read(path) { return this.files.get(path) || null; }
    async getMeta(key) { return this.meta.has(key) ? JSON.parse(this.meta.get(key)) : null; }
    async setMeta(key, value) { this.meta.set(key, JSON.stringify(value)); }
    async clear() { this.files.clear(); this.meta.clear(); }

  }

  class IndexedStore {

    constructor(pack, db) { this.kind = 'indexeddb'; this.pack = pack; this.db = db; }

    static open(pack, factory) {
      return new Promise((resolve, reject) => {
        const request = factory.open('pad-offline', 1);
        request.onupgradeneeded = () => {
          request.result.createObjectStore('files');
          request.result.createObjectStore('meta');
        };
        request.onsuccess = () => resolve(new IndexedStore(pack, request.result));
        request.onerror = () => reject(request.error);
        request.onblocked = () => reject(new Error('IndexedDB is blocked by another tab'));
      });
    }

    key(path) { return `${this.pack}|${path}`; }
    range() { return IDBKeyRange.bound(`${this.pack}|`, `${this.pack}|￿`); }

    run(stores, mode, work) {
      return new Promise((resolve, reject) => {
        const tx = this.db.transaction(stores, mode);
        let answer;
        tx.oncomplete = () => resolve(answer);
        tx.onerror = () => reject(tx.error);
        tx.onabort = () => reject(tx.error || new Error('the IndexedDB transaction was aborted'));
        answer = work(tx);
      });
    }

    load() {
      const found = new Map();
      const skip = this.pack.length + 1;
      return this.run(['files'], 'readonly', tx => {
        tx.objectStore('files').openCursor(this.range()).onsuccess = event => {
          const cursor = event.target.result;
          if (!cursor) return;
          found.set(cursor.key.substring(skip), new Uint8Array(cursor.value));
          cursor.continue();
        };
      }).then(() => found);
    }

    write(put, removed) {
      return this.run(['files'], 'readwrite', tx => {
        const files = tx.objectStore('files');
        for (const [path, bytes] of put) files.put(bytes, this.key(path));
        for (const path of removed) files.delete(this.key(path));
      });
    }

    read(path) {
      let value = null;
      return this.run(['files'], 'readonly', tx => {
        tx.objectStore('files').get(this.key(path)).onsuccess = event => { value = event.target.result || null; };
      }).then(() => value && new Uint8Array(value));
    }

    getMeta(key) {
      let value = null;
      return this.run(['meta'], 'readonly', tx => {
        tx.objectStore('meta').get(this.key(key)).onsuccess = event => { value = event.target.result ?? null; };
      }).then(() => value);
    }

    setMeta(key, value) {
      return this.run(['meta'], 'readwrite', tx => { tx.objectStore('meta').put(value, this.key(key)); });
    }

    clear() {
      return this.run(['files', 'meta'], 'readwrite', tx => {
        tx.objectStore('files').delete(this.range());
        tx.objectStore('meta').delete(this.range());
      });
    }

  }

  // localStorage, for a browser without IndexedDB: one item per pack, the files base64 -
  // what fits in the few megabytes such a store allows.

  class LocalStore {

    constructor(pack, storage) { this.kind = 'localstorage'; this.storage = storage; this.item = `pad-offline:${pack}`; }
    all() { try { return JSON.parse(this.storage.getItem(this.item)) || { files: {}, meta: {} }; } catch (e) { return { files: {}, meta: {} }; } }
    keep(all) { this.storage.setItem(this.item, JSON.stringify(all)); }
    async load() { return new Map(Object.entries(this.all().files).map(([path, text]) => [path, fromBase64(text)])); }
    async write(put, removed) {
      const all = this.all();
      for (const [path, bytes] of put) all.files[path] = toBase64(bytes);
      for (const path of removed) delete all.files[path];
      this.keep(all);
    }
    async read(path) { const text = this.all().files[path]; return text ? fromBase64(text) : null; }
    async getMeta(key) { return this.all().meta[key] ?? null; }
    async setMeta(key, value) { const all = this.all(); all.meta[key] = value; this.keep(all); }
    async clear() { this.storage.removeItem(this.item); }

  }

  class PadApp {

    constructor(php, bundle, options = {}) {
      this.php      = php;
      this.bundle   = bundle;
      this.apps     = bundle.apps || [bundle.app];
      this.app      = this.apps[0];
      this.offline  = bundle.offline || {};
      this.origin   = options.origin || 'http://localhost';
      this.scope    = options.scope || '/';
      this.base     = `${this.origin}${this.scope}${this.app}/`;
      this.mode     = options.mode || 'node';
      this.store    = options.store || new MemoryStore();
      this.FS       = options.FS || null;
      this.timezone = options.timezone || (Intl.DateTimeFormat().resolvedOptions().timeZone || 'UTC');
      this.cookies  = [];
      this.stamps   = new Map();
      this.stale    = new Map();
      this.queue    = Promise.resolve();
      this.log      = [];
      this.logId    = 0;
      this.watchers = new Set();
      this.id       = Math.random().toString(36).slice(2);
      this.state    = { saved: 0, saving: false, error: '', files: 0, bytes: 0 };
      this.phpVersion = '';
      if (options.channel !== false && typeof BroadcastChannel === 'function' && this.mode !== 'node') {
        this.channel = new BroadcastChannel(`pad-offline:${bundle.id || this.app}`);
        this.channel.onmessage = event => this.heard(event.data);
      }
    }

    // PHP started with the given runtime (php.js's PadPHP, or the Node packages), the engine
    // and the applications written into its memory, DATA/ put back from the store.

    static async start(bundle, runtime, options = {}) {
      const settings = { phpWasmAsyncMode: 'asyncify', processId: 1 };
      if (runtime.wasmBinary) settings.wasmBinary = runtime.wasmBinary;
      const php = new runtime.PHP(await runtime.loadPHPRuntime(runtime.loader, settings));
      const FS = runtime.internals ? php[runtime.internals].FS : null;
      const store = options.store || await PadApp.openStore(bundle.id || bundle.app);
      const pad = new PadApp(php, bundle, Object.assign({}, options, { store, FS }));
      await pad.install();
      return pad;
    }

    static async openStore(pack) {
      if (root.indexedDB) {
        try { return await IndexedStore.open(pack, root.indexedDB); } catch (e) { /* a file:// page in a browser without it */ }
      }
      try { if (root.localStorage) return new LocalStore(pack, root.localStorage); } catch (e) { /* storage switched off */ }
      return new MemoryStore();
    }

    // The engine, the applications and their www/ directories into PHP's file system, each
    // file at its path in the repository under /pad, the boot script beside them.

    async install() {
      const made = new Set();
      const write = (name, content) => {
        const path = `${ROOT}/${name}`;
        const dir = path.substring(0, path.lastIndexOf('/'));
        if (!made.has(dir)) { if (!this.php.fileExists(dir)) this.php.mkdir(dir); made.add(dir); }
        this.php.writeFile(path, content);
      };
      for (const [name, text] of Object.entries(this.bundle.files)) write(name, text);
      for (const [name, text] of Object.entries(this.bundle.base64 || {})) write(name, fromBase64(text));
      write('boot.php', BOOT);
      if (!this.php.fileExists(TOOL)) this.php.mkdir(TOOL);
      if (!this.php.fileExists(`${DATA}/sessions`)) this.php.mkdir(`${DATA}/sessions`);
      await this.restore();
      this.cookies = (await this.store.getMeta('cookies')) || [];
      try { this.phpVersion = (await this.php.run({ code: '<?php echo PHP_VERSION;' })).text; } catch (e) { /* only for the panel */ }
    }

    // ---- Requests --------------------------------------------------------------------

    // Which part of the pack a URL is: an application and the path below it, the runtime's
    // own calls (_pad/...), or nothing of the pack.

    route(url) {
      const target = new URL(url, this.base);
      if (target.origin !== this.origin || !target.pathname.startsWith(this.scope)) return { target, none: true };
      let rest;
      try { rest = decodeURIComponent(target.pathname.substring(this.scope.length)); } catch (e) { return { target, none: true }; }
      if (rest === '_pad' || rest.startsWith('_pad/')) return { target, api: rest.substring(5) };
      let app = null;
      for (const one of this.apps)
        if ((rest === one || rest.startsWith(one + '/')) && (!app || one.length > app.length)) app = one;
      if (!app) return { target, none: true };
      return { target, app, path: rest.substring(app.length + 1), bare: rest === app };
    }

    // Is this URL - absolute, or relative to the page at 'from' - one this runtime answers?

    isMine(url, from) {
      let route;
      try { route = this.route(new URL(url, from || this.base).href); } catch (e) { return false; }
      return !route.none;
    }

    // The file of an application's www/ directory a URL names - a stylesheet, an image - as
    // { name, bytes, type }, or null when it names none (a page).

    asset(url, from) {
      const route = this.route(new URL(url, from || this.base).href);
      if (!route.app) return null;
      return this.assetFile(route.app, route.path);
    }

    assetFile(app, path) {
      if (!path || path.endsWith('/') || /\.php$/i.test(path)) return null;
      const name = `www/${app}/${path}`;
      let bytes = null;
      if (name in this.bundle.files) bytes = encoder.encode(this.bundle.files[name]);
      else if (this.bundle.base64 && name in this.bundle.base64) bytes = fromBase64(this.bundle.base64[name]);
      return bytes && { name, bytes, type: mimeOf(name) };
    }

    // One request, answered as a server would: a page by PHP (one at a time - one PHP
    // answers them all), a www/ file from the bundle, a call of the runtime's own.
    // request: { method, url, headers, body, kind } - headers an object or Headers.

    async handle(request) {
      const started = Date.now();
      const route = this.route(request.url);
      let answer;
      if (route.none)
        answer = this.plain(404, `There is no application at ${route.target.pathname}`);
      else if (route.api !== undefined)
        return this.api(route.api, route.target, request);
      else if (route.bare)
        answer = { status: 301, headers: { location: [`${this.scope}${route.app}/${route.target.search}`] }, bytes: new Uint8Array(0) };
      else {
        const file = this.assetFile(route.app, route.path);
        answer = file
          ? { status: 200, headers: { 'content-type': [file.type], 'cache-control': ['no-cache'] }, bytes: file.bytes, file: true }
          : await this.enqueue(() => this.page(route, request));
      }
      answer.url = route.target.href;
      this.record(request, answer, started, route);
      return answer;
    }

    // A request the way the page opened from the disk makes it - GET or POST a URL relative
    // to the first application's address, a redirect followed as a browser would.

    async request(method, url, body, headers, kind) {
      let target = new URL(url, this.base).href;
      for (let hop = 0; hop < HOPS; hop++) {
        const answer = await this.handle({ method, url: target, body, headers, kind: kind || 'page' });
        const location = first(answer.headers, 'location');
        if (location && answer.status >= 300 && answer.status < 400) {
          target = new URL(location, target).href;
          if (answer.status !== 307 && answer.status !== 308) { method = 'GET'; body = undefined; headers = undefined; }
          continue;
        }
        Object.defineProperty(answer, 'text', { get() { return decoder.decode(this.bytes); } });
        answer.html = answer.text;
        return answer;
      }
      throw new Error(`more than ${HOPS} redirects from ${url}`);
    }

    enqueue(task) {
      const run = this.queue.then(task, task);
      this.queue = run.catch(() => {});
      return run;
    }

    async page(route, request) {
      await this.refresh();
      const method = String(request.method || 'GET').toUpperCase();
      const target = route.target;
      const headers = {
        host: target.host,
        accept: 'text/html,*/*',
        'user-agent': (root.navigator && root.navigator.userAgent) || 'PAD wasm'
      };
      const given = headerList(request.headers);
      for (const [name, value] of given)
        if (!OWN.has(name.toLowerCase())) headers[name.toLowerCase()] = String(value);
      const cookie = this.cookieHeader(target.pathname);
      if (cookie) headers.cookie = cookie;
      let body = method === 'GET' || method === 'HEAD' ? undefined : (bytesOf(request.body) || new Uint8Array(0));
      if (body && !headers['content-type']) headers['content-type'] = 'application/x-www-form-urlencoded';
      const scheme = target.protocol.replace(':', '');
      const server = Object.assign({}, (this.offline[route.app] || {}).env || {}, {
        REMOTE_ADDR: '127.0.0.1', HTTP_HOST: target.host, SERVER_NAME: target.hostname,
        SERVER_PORT: target.port || (scheme === 'https' ? '443' : '80'), REQUEST_SCHEME: scheme,
        SCRIPT_NAME: `${this.scope}${route.app}/index.php`,
        SCRIPT_FILENAME: `${ROOT}/www/${route.app}/index.php`,
        PAD_APP: route.app, PAD_ROOT: this.scope, PAD_WASM: this.mode, PAD_TIMEZONE: this.timezone
      });
      if (scheme === 'https') server.HTTPS = 'on';
      let response;
      try {
        response = await this.php.run({
          scriptPath: `${ROOT}/boot.php`,
          relativeUri: target.pathname + target.search,
          method, headers, body, $_SERVER: server
        });
      } catch (error) {
        if (!error || !error.response) throw error;
        response = error.response;      // a page that ended with exit(1) - its answer stands
      }
      const answer = { status: response.httpStatusCode, headers: {}, bytes: response.bytes, errors: response.errors };
      for (const [name, values] of Object.entries(response.headers || {}))
        if (name !== 'set-cookie') answer.headers[name] = values;
      await this.takeCookies(response.headers['set-cookie'] || [], target);
      await this.persist();
      return answer;
    }

    plain(status, text) {
      return { status, headers: { 'content-type': ['text/plain; charset=utf-8'] }, bytes: encoder.encode(text) };
    }

    json(value, status = 200) {
      return { status, headers: { 'content-type': ['application/json; charset=utf-8'], 'cache-control': ['no-store'] }, bytes: encoder.encode(JSON.stringify(value)) };
    }

    // ---- Cookies ---------------------------------------------------------------------
    //
    // A jar as a browser keeps one - name and path, an expiry from Expires or Max-Age - for
    // the service worker cannot set a cookie in the browser, and a page opened from the disk
    // has none to set. It is kept with DATA/, so a reload stays logged in.

    async takeCookies(lines, target) {
      if (!lines.length) return;
      const here = target.pathname.substring(0, target.pathname.lastIndexOf('/')) || '/';
      for (const line of lines) {
        const [pair, ...attributes] = line.split(';');
        const at = pair.indexOf('=');
        if (at < 1) continue;
        const name = pair.substring(0, at).trim();
        const value = pair.substring(at + 1).trim();
        let path = here, expires = null;
        for (const attribute of attributes) {
          const [key, ...rest] = attribute.split('=');
          const word = key.trim().toLowerCase(), setting = rest.join('=').trim();
          if (word === 'path' && setting.startsWith('/')) path = setting;
          else if (word === 'max-age') expires = Date.now() + Number(setting) * 1000;
          else if (word === 'expires' && expires === null && !isNaN(Date.parse(setting))) expires = Date.parse(setting);
        }
        this.cookies = this.cookies.filter(c => !(c.name === name && c.path === path));
        if (value !== '' && value !== 'deleted' && (expires === null || expires > Date.now()))
          this.cookies.push({ name, value, path, expires });
      }
      await this.store.setMeta('cookies', this.cookies);
    }

    cookieHeader(pathname) {
      const now = Date.now();
      return this.cookies
        .filter(c => (c.expires === null || c.expires > now) && (pathname === c.path || pathname.startsWith(c.path.endsWith('/') ? c.path : c.path + '/')))
        .sort((a, b) => b.path.length - a.path.length)
        .map(c => `${c.name}=${c.value}`).join('; ');
    }

    // ---- DATA/ kept between visits ----------------------------------------------------

    // The files under DATA/ as they stand: path (under DATA/) -> a change mark - the time of
    // the last write to the millisecond and the size, read from Emscripten's file system,
    // or a hash of the bytes where that is not at hand.

    scan() {
      const found = new Map();
      const FS = this.FS;
      const walk = dir => {
        const names = FS ? FS.readdir(dir) : this.php.listFiles(dir);
        for (const name of names) {
          if (name === '.' || name === '..') continue;
          const path = `${dir}/${name}`;
          if (FS) {
            const stat = FS.stat(path);
            if (FS.isDir(stat.mode)) walk(path);
            else found.set(path.substring(DATA.length + 1), `${stat.mtime.getTime()}:${stat.size}`);
          } else if (this.php.isDir(path)) walk(path);
          else found.set(path.substring(DATA.length + 1), hash(this.php.readFileAsBuffer(path)));
        }
      };
      walk(DATA);
      return found;
    }

    async persist() {
      const now = this.scan();
      const put = [], removed = [];
      let bytes = 0;
      for (const [path, mark] of now) {
        const size = Number(mark.split(':').pop()) || 0;
        bytes += size;
        if (this.stamps.get(path) !== mark) put.push([path, this.php.readFileAsBuffer(`${DATA}/${path}`)]);
      }
      for (const path of this.stamps.keys()) if (!now.has(path)) removed.push(path);
      this.state.files = now.size;
      this.state.bytes = bytes;
      if (!put.length && !removed.length) return;
      this.state.saving = true;
      try {
        await this.store.write(put, removed);
        this.stamps = now;
        this.state.saved = Date.now();
        this.state.error = '';
        if (this.channel) this.channel.postMessage({ from: this.id, put: put.map(p => p[0]), removed });
      } catch (error) {
        this.state.error = `DATA/ could not be kept: ${error.message || error}`;
        if (root.console) console.error(this.state.error);
      } finally {
        this.state.saving = false;
      }
    }

    async restore() {
      let files = await this.store.load();
      if (!files.size) files = await this.legacy();
      for (const [path, bytes] of files) this.writeData(path, bytes);
      this.stamps = this.scan();
    }

    // A pack made before the IndexedDB store kept DATA/ of its one application in
    // localStorage: taken over once.

    async legacy() {
      const found = new Map();
      if (this.mode === 'node') return found;
      let storage;
      try { storage = root.localStorage; } catch (e) { return found; }
      const item = storage && storage.getItem(`pad-wasm:${this.app}:DATA`);
      if (!item) return found;
      try {
        for (const [path, text] of Object.entries(JSON.parse(item)))
          if (path.startsWith(`${DATA}/`)) found.set(path.substring(DATA.length + 1), fromBase64(text));
        await this.store.write(Array.from(found), []);
        storage.removeItem(`pad-wasm:${this.app}:DATA`);
      } catch (e) { /* a broken item is left as it was */ }
      return found;
    }

    writeData(path, bytes) {
      if (path.split('/').includes('..')) return;
      const full = `${DATA}/${path}`;
      const dir = full.substring(0, full.lastIndexOf('/'));
      if (!this.php.fileExists(dir)) this.php.mkdir(dir);
      this.php.writeFile(full, bytes);
    }

    // Another tab of the same pack wrote: the files it names are read again before this
    // runtime answers its next request.

    heard(message) {
      if (!message || message.from === this.id) return;
      for (const path of message.put || []) this.stale.set(path, 'put');
      for (const path of message.removed || []) this.stale.set(path, 'removed');
      if (message.forget) this.stale.set('*', 'forget');
    }

    async refresh() {
      if (!this.stale.size) return;
      const stale = this.stale;
      this.stale = new Map();
      if (stale.has('*')) { this.wipe(); await this.restore(); return; }
      for (const [path, what] of stale) {
        if (what === 'removed') { try { this.php.unlink(`${DATA}/${path}`); } catch (e) { /* gone already */ } continue; }
        const bytes = await this.store.read(path);
        if (bytes) this.writeData(path, bytes);
      }
      this.stamps = this.scan();
    }

    wipe() {
      for (const path of this.scan().keys()) { try { this.php.unlink(`${DATA}/${path}`); } catch (e) { /* gone already */ } }
      this.stamps = new Map();
    }

    async forget() {
      this.wipe();
      this.cookies = [];
      await this.store.clear();
      if (!this.php.fileExists(`${DATA}/sessions`)) this.php.mkdir(`${DATA}/sessions`);
      this.state.saved = Date.now();
      if (this.channel) this.channel.postMessage({ from: this.id, forget: true });
      return this.json({ forgotten: true });
    }

    // ---- The runtime's own calls: <scope>_pad/... ------------------------------------

    async api(call, target, request) {
      const q = target.searchParams;
      const method = String(request.method || 'GET').toUpperCase();
      switch (call) {
        case 'ready': return this.json({ ready: true, build: this.bundle.build });
        case 'state': return this.json(Object.assign({ online: !root.navigator || root.navigator.onLine !== false }, this.state, { log: this.logId }));
        case 'info':  return this.json(this.info());
        case 'log':   return this.json({ log: this.log.filter(entry => entry.id > Number(q.get('after') || 0)) });
        case 'forget':
          if (method !== 'POST') return this.json({ error: 'forget is a POST' }, 405);
          return this.enqueue(() => this.forget());
        case 'databases': case 'rows': case 'files': case 'file': case 'export': case 'backup':
          return this.enqueue(() => this.tool(call, Object.fromEntries(q.entries())));
        case 'query': case 'import': case 'reset': case 'restore': {
          if (method !== 'POST') return this.json({ error: `${call} is a POST` }, 405);
          const body = bytesOf(request.body) || new Uint8Array(0);
          const input = Object.fromEntries(q.entries());
          if (call === 'query') Object.assign(input, JSON.parse(decoder.decode(body) || '{}'));
          return this.enqueue(() => this.tool(call, input, call === 'import' || call === 'restore' ? body : null));
        }
        default: return this.json({ error: `there is no call _pad/${call}` }, 404);
      }
    }

    // A call of wasm/app/pad-runtime.php, the PHP side of the database and storage tools.

    async tool(action, input, upload) {
      await this.refresh();
      input.action = action;
      input.name = input.name || this.bundle.id || this.app;
      this.php.writeFile(`${TOOL}/in.json`, JSON.stringify(input));
      if (upload) this.php.writeFile(`${TOOL}/upload.bin`, upload);
      let response;
      try {
        response = await this.php.run({ scriptPath: `${TOOL}/pad-runtime.php` });
      } catch (error) {
        if (!error || !error.response) throw error;
        response = error.response;
      }
      if (upload) this.php.unlink(`${TOOL}/upload.bin`);
      let answer;
      try { answer = JSON.parse(response.text); } catch (e) { answer = { error: (response.text || response.errors || 'no answer').trim() }; }
      if (['import', 'reset', 'restore', 'query'].includes(action)) await this.persist();
      if (answer.file && !answer.error) {
        const bytes = this.php.readFileAsBuffer(answer.file);
        if (answer.remove) this.php.unlink(answer.file);
        return { status: 200, bytes, headers: {
          'content-type': [answer.type], 'cache-control': ['no-store'],
          'content-disposition': [`attachment; filename="${answer.name.replace(/[^A-Za-z0-9._-]/g, '_')}"`]
        } };
      }
      return this.json(answer, answer.error ? 400 : 200);
    }

    info() {
      return {
        build: this.bundle.build, built: this.bundle.built, commit: this.bundle.commit, name: this.bundle.name,
        apps: this.apps.map(name => Object.assign({ name, url: `${this.scope}${name}/` }, this.offline[name] || {}, { env: undefined })),
        mode: this.mode, store: this.store.kind, php: this.phpVersion, scope: this.scope,
        files: this.state.files, bytes: this.state.bytes, saved: this.state.saved
      };
    }

    // ---- The request log, for the panel ----------------------------------------------

    record(request, answer, started, route) {
      const headers = new Map(headerList(request.headers).map(([name, value]) => [name.toLowerCase(), String(value)]));
      const get = name => headers.get(name) || '';
      let kind = answer.file ? 'file' : request.kind || '';
      if (!kind) kind = /text\/event-stream/.test(get('accept')) ? 'sse'
                      : get('hx-request') ? 'htmx'
                      : /^\s*padLive=|[&]padLive=/.test(typeof request.body === 'string' ? request.body : '') ? 'live'
                      : 'fetch';
      const entry = {
        id: ++this.logId, at: started, ms: Date.now() - started, kind,
        method: String(request.method || 'GET').toUpperCase(),
        url: route.target.pathname.substring(this.scope.length) + route.target.search,
        status: answer.status, type: String(first(answer.headers, 'content-type') || '').split(';')[0],
        size: answer.bytes ? answer.bytes.length : 0
      };
      this.log.push(entry);
      if (this.log.length > LOG) this.log.shift();
      for (const watcher of this.watchers) { try { watcher(entry); } catch (e) { /* a closed panel */ } }
    }

  }

  // The boot script every page request runs - www/pad.php for a server: the application and
  // the mount prefix come from the runtime, the sessions are kept under DATA/ (and with it),
  // the clock shows the browser's local time.

  const BOOT = `<?php
  $padHome = '${ROOT}';
  $padApps = '${ROOT}/apps/';
  $padData = '${ROOT}/DATA/';
  $padApp  = $_SERVER ['PAD_APP'];
  $padRoot = $_SERVER ['PAD_ROOT'];
  if ( in_array ( $_SERVER ['PAD_TIMEZONE'] ?? '', timezone_identifiers_list () ) )
    date_default_timezone_set ( $_SERVER ['PAD_TIMEZONE'] );
  session_save_path ( '${DATA}/sessions' );
  include '${ROOT}/pad/pad.php';
?>`;

  PadApp.MemoryStore  = MemoryStore;
  PadApp.IndexedStore = IndexedStore;
  PadApp.LocalStore   = LocalStore;
  PadApp.MIME         = MIME;
  PadApp.mimeOf       = mimeOf;

  root.PadApp = PadApp;
  if (typeof module !== 'undefined' && module.exports) module.exports = PadApp;

})(typeof self !== 'undefined' ? self : globalThis);
