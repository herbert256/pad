// The panel of a packed PAD application (wasm/app.mjs, pad pack): what a server's console
// would show, for the PHP running in this browser - the SQLite databases under DATA/ (their
// tables and rows, a SQL console, export as .sqlite or SQL, import, reset), the storage
// (what is kept, how much room there is, a backup of all of it as one zip, restore, forget),
// the requests the application answered, and what runs here (PHP, the build, installed or
// not, a new build waiting).
//
// It opens from a small badge in the corner - its dot green when what the application wrote
// is kept, amber while it is being written, red when it could not be, blue offline - or with
// Ctrl+Shift+K. The pack decides: --panel=button (the badge), key (the keys only), none.
//
// Two places load it. Over https the service worker puts it last in every page of the
// application: it then also keeps the worker awake while the page is open, and offers a new
// build. From the disk the page around the frame loads it (index.html), and it talks to the
// runtime there directly. Either way the calls are the runtime's own, under <scope>_pad/.
// Everything is drawn in a shadow root, so neither the application's styles nor its scripts
// meet it.

(function () {

  'use strict';

  if (window.PadPanel) return;

  const script  = document.currentScript;
  const inShell = !!window.PadHost;
  const scope   = inShell ? '/' : (script && script.dataset.padScope) || '/';
  const mode    = inShell ? (window.PAD_BUNDLE && window.PAD_BUNDLE.panel) || 'button' : (script && script.dataset.padPanel) || 'button';
  const sw      = !inShell && navigator.serviceWorker;

  const api = inShell
    ? (path, init) => window.PadHost.api(path, init)
    : (path, init) => fetch(`${scope}_pad/${path}`, Object.assign({ cache: 'no-store' }, init));

  // ---- Over https: the worker kept awake, a new build offered --------------------------

  let waiting = null, installEvent = null, reloadAsked = false;

  if (sw && sw.controller) {
    const ping = () => sw.controller && sw.controller.postMessage({ type: 'pad-ping' });
    ping();
    setInterval(ping, 20000);
    sw.getRegistration(scope).then(registration => {
      if (!registration) return;
      const check = () => { if (registration.waiting) { waiting = registration.waiting; refreshAbout(); mark(); } };
      check();
      registration.addEventListener('updatefound', () => {
        const next = registration.installing;
        if (next) next.addEventListener('statechange', () => { if (next.state === 'installed') check(); });
      });
      if (navigator.onLine) registration.update().catch(() => {});
    });
    sw.addEventListener('controllerchange', () => { if (reloadAsked) location.reload(); });
  }

  window.addEventListener('beforeinstallprompt', event => { event.preventDefault(); installEvent = event; refreshAbout(); });
  window.addEventListener('appinstalled', () => { installEvent = null; refreshAbout(); });

  if (mode === 'none') {
    window.PadPanel = { toggle() {} };
    return;
  }

  // ---- The drawing ---------------------------------------------------------------------

  const BRACES = '<svg viewBox="0 0 512 512" aria-hidden="true"><g fill="none" stroke="currentColor" stroke-width="40" stroke-linecap="round" stroke-linejoin="round"><path d="M206 124c-44 0-50 26-50 62v34c0 26-14 36-40 36 26 0 40 10 40 36v34c0 36 6 62 50 62"/><path d="M306 124c44 0 50 26 50 62v34c0 26 14 36 40 36-26 0-40 10-40 36v34c0 36-6 62-50 62"/></g><circle cx="256" cy="256" r="30" fill="currentColor"/></svg>';

  const CSS = `
    :host { all: initial; --accent: #4f46e5; --back: #ffffff; --soft: #f1f5f9; --text: #0f172a; --dim: #64748b; --line: #e2e8f0;
            --ok: #16a34a; --warn: #d97706; --bad: #dc2626; --off: #2563eb; --code: #f8fafc;
            --shadow: 0 10px 40px rgb(15 23 42 / 22%); font: 13.5px/1.45 system-ui, -apple-system, "Segoe UI", sans-serif; color: var(--text); }
    @media (prefers-color-scheme: dark) {
      :host { --back: #0f172a; --soft: #1e293b; --text: #e2e8f0; --dim: #94a3b8; --line: #263449; --code: #0b1220; --accent: #818cf8;
              --shadow: 0 10px 40px rgb(0 0 0 / 55%); }
    }
    * { box-sizing: border-box; }
    [hidden] { display: none !important; }
    button, select, input, textarea { font: inherit; color: inherit; }
    .badge { position: fixed; right: 16px; bottom: 16px; z-index: 2147483000; width: 40px; height: 40px; border-radius: 50%;
             border: 1px solid var(--line); background: var(--back); color: var(--accent); display: grid; place-items: center;
             cursor: pointer; box-shadow: 0 2px 10px rgb(15 23 42 / 18%); opacity: 0.55; transition: opacity 0.2s, transform 0.2s; padding: 0; }
    .badge:hover, .badge:focus-visible { opacity: 1; transform: scale(1.06); outline: none; }
    .badge svg { width: 22px; height: 22px; }
    .dot { position: absolute; right: 1px; top: 1px; width: 11px; height: 11px; border-radius: 50%; background: var(--ok); border: 2px solid var(--back); }
    .dot.saving { background: var(--warn); animation: pulse 1s ease-in-out infinite; }
    .dot.error { background: var(--bad); }
    .dot.offline { background: var(--off); }
    .dot.update { background: var(--accent); animation: pulse 1.6s ease-in-out infinite; }
    @keyframes pulse { 50% { transform: scale(1.35); } }

    .drawer { position: fixed; top: 0; right: 0; bottom: 0; z-index: 2147483001; width: min(560px, 100vw); background: var(--back);
              box-shadow: var(--shadow); display: flex; flex-direction: column; transform: translateX(105%);
              transition: transform 0.22s cubic-bezier(.2,.8,.2,1); border-left: 1px solid var(--line); }
    .drawer.open { transform: none; }
    header { display: flex; align-items: center; gap: 12px; padding: 14px 16px 10px; }
    header .logo { width: 34px; height: 34px; border-radius: 9px; display: grid; place-items: center; color: #fff;
                   background: linear-gradient(135deg, #6366f1, #1d4ed8); flex: none; }
    header .logo svg { width: 22px; height: 22px; }
    header strong { display: block; font-size: 15px; }
    header small { color: var(--dim); }
    header .close { margin-left: auto; border: 0; background: none; font-size: 22px; line-height: 1; cursor: pointer; color: var(--dim); padding: 6px; border-radius: 8px; }
    header .close:hover { background: var(--soft); color: var(--text); }
    nav { display: flex; gap: 2px; padding: 0 12px; border-bottom: 1px solid var(--line); }
    nav button { border: 0; background: none; padding: 9px 12px; cursor: pointer; color: var(--dim); border-bottom: 2px solid transparent; margin-bottom: -1px; font-weight: 550; }
    nav button[aria-selected="true"] { color: var(--accent); border-bottom-color: var(--accent); }
    nav button:hover { color: var(--text); }
    section { flex: 1; overflow: auto; padding: 14px 16px 24px; }
    h3 { font-size: 12px; text-transform: uppercase; letter-spacing: 0.06em; color: var(--dim); margin: 18px 0 8px; font-weight: 650; }
    h3:first-child { margin-top: 2px; }
    .row { display: flex; flex-wrap: wrap; gap: 8px; align-items: center; margin: 8px 0; }
    .btn { border: 1px solid var(--line); background: var(--soft); padding: 6px 11px; border-radius: 8px; cursor: pointer; display: inline-flex; gap: 6px; align-items: center; white-space: nowrap; }
    .btn:hover { border-color: var(--accent); }
    .btn.primary { background: var(--accent); border-color: var(--accent); color: #fff; }
    .btn.danger:hover, .btn.danger.armed { border-color: var(--bad); color: var(--bad); }
    .btn.danger.armed { background: color-mix(in srgb, var(--bad) 12%, transparent); }
    .btn input[type=file] { display: none; }
    select { padding: 6px 8px; border-radius: 8px; border: 1px solid var(--line); background: var(--back); max-width: 100%; }
    .chips { display: flex; flex-wrap: wrap; gap: 6px; }
    .chip { border: 1px solid var(--line); border-radius: 999px; padding: 3px 10px; cursor: pointer; background: var(--back); }
    .chip small { color: var(--dim); margin-left: 4px; }
    .chip.on { border-color: var(--accent); color: var(--accent); background: color-mix(in srgb, var(--accent) 10%, transparent); }
    .grid-wrap { overflow: auto; border: 1px solid var(--line); border-radius: 10px; max-height: 42vh; margin-top: 10px; }
    table { border-collapse: collapse; width: 100%; font-size: 12.5px; }
    th, td { text-align: left; padding: 5px 9px; border-bottom: 1px solid var(--line); white-space: nowrap; max-width: 260px; overflow: hidden; text-overflow: ellipsis; vertical-align: top; }
    th { position: sticky; top: 0; background: var(--soft); font-weight: 600; z-index: 1; }
    td.null { color: var(--dim); font-style: italic; }
    td.num { text-align: right; font-variant-numeric: tabular-nums; }
    tr:last-child td { border-bottom: 0; }
    .pager { display: flex; justify-content: space-between; align-items: center; color: var(--dim); margin-top: 6px; }
    textarea { width: 100%; min-height: 96px; resize: vertical; border: 1px solid var(--line); border-radius: 10px; padding: 9px 10px;
               background: var(--code); font: 12.5px/1.5 ui-monospace, SFMono-Regular, Menlo, monospace; }
    textarea:focus, select:focus, .btn:focus-visible { outline: 2px solid color-mix(in srgb, var(--accent) 45%, transparent); outline-offset: 1px; }
    .result { margin-top: 10px; }
    .result .sql { font: 11.5px ui-monospace, Menlo, monospace; color: var(--dim); white-space: pre-wrap; word-break: break-word; }
    .error { color: var(--bad); white-space: pre-wrap; }
    .dim { color: var(--dim); }
    .empty { color: var(--dim); padding: 18px; text-align: center; border: 1px dashed var(--line); border-radius: 10px; }
    .meter { height: 8px; border-radius: 8px; background: var(--soft); overflow: hidden; margin: 6px 0 4px; }
    .meter span { display: block; height: 100%; background: linear-gradient(90deg, #6366f1, #22d3ee); }
    dl { display: grid; grid-template-columns: max-content 1fr; gap: 6px 16px; margin: 0; }
    dt { color: var(--dim); }
    dd { margin: 0; word-break: break-word; }
    a { color: var(--accent); }
    .status { display: inline-block; min-width: 34px; text-align: center; border-radius: 6px; padding: 0 5px; font-weight: 600; font-size: 11.5px; }
    .s2 { background: color-mix(in srgb, var(--ok) 15%, transparent); color: var(--ok); }
    .s3 { background: color-mix(in srgb, var(--off) 15%, transparent); color: var(--off); }
    .s4, .s5 { background: color-mix(in srgb, var(--bad) 15%, transparent); color: var(--bad); }
    .kind { font-size: 11px; color: var(--dim); border: 1px solid var(--line); border-radius: 5px; padding: 0 5px; }
    .toast { position: absolute; left: 16px; right: 16px; bottom: 16px; background: var(--text); color: var(--back); border-radius: 10px;
             padding: 10px 12px; display: flex; gap: 10px; align-items: center; box-shadow: var(--shadow); }
    .toast .btn { background: transparent; color: inherit; border-color: color-mix(in srgb, var(--back) 40%, transparent); margin-left: auto; }
    .toast.bad { background: var(--bad); color: #fff; }
    kbd { font: 11px ui-monospace, Menlo, monospace; border: 1px solid var(--line); border-bottom-width: 2px; border-radius: 4px; padding: 0 4px; background: var(--soft); }
    @media (max-width: 560px) { .drawer { width: 100vw; } th, td { max-width: 160px; } }
  `;

  const element = document.createElement('pad-panel');
  const root = element.attachShadow({ mode: 'open' });
  root.innerHTML = `
    <style>${CSS}</style>
    <button class="badge" title="PAD in this browser - Ctrl+Shift+K" aria-label="Open the PAD panel" ${mode === 'button' ? '' : 'hidden'}>${BRACES}<i class="dot"></i></button>
    <aside class="drawer" role="dialog" aria-label="PAD in this browser" aria-hidden="true">
      <header>
        <div class="logo">${BRACES}</div>
        <div><strong class="name">PAD</strong><small class="sub">PHP in this browser</small></div>
        <button class="close" aria-label="Close the panel" title="Close (Esc)">&times;</button>
      </header>
      <nav role="tablist">
        <button role="tab" data-tab="database">Database</button>
        <button role="tab" data-tab="storage">Storage</button>
        <button role="tab" data-tab="requests">Requests</button>
        <button role="tab" data-tab="about">About</button>
      </nav>
      <section data-tab="database" role="tabpanel"></section>
      <section data-tab="storage" role="tabpanel" hidden></section>
      <section data-tab="requests" role="tabpanel" hidden></section>
      <section data-tab="about" role="tabpanel" hidden></section>
      <div class="toast" role="status" hidden></div>
    </aside>`;

  const $ = selector => root.querySelector(selector);
  const drawer = $('.drawer');
  const dot = $('.dot');

  function make(tag, attributes, ...children) {
    const node = document.createElement(tag);
    for (const [key, value] of Object.entries(attributes || {})) {
      if (value === undefined || value === null || value === false) continue;
      if (key.startsWith('on')) node.addEventListener(key.substring(2), value);
      else if (key === 'class') node.className = value;
      else node.setAttribute(key, value === true ? '' : value);
    }
    for (const child of children.flat()) if (child !== null && child !== undefined && child !== false) node.append(child instanceof Node ? child : String(child));
    return node;
  }

  function size(bytes) {
    if (bytes === undefined || bytes === null) return '';
    const units = ['B', 'KB', 'MB', 'GB'];
    let n = Number(bytes), u = 0;
    while (n >= 1024 && u < units.length - 1) { n /= 1024; u++; }
    return (u ? n.toFixed(n < 10 ? 1 : 0) : n) + ' ' + units[u];
  }

  function when(seconds) {
    if (!seconds) return '';
    return new Date(seconds * 1000).toLocaleString(undefined, { dateStyle: 'short', timeStyle: 'short' });
  }

  // A button that asks once more before it does something that cannot be undone.

  function armed(label, sure, action) {
    let timer = null;
    const button = make('button', { class: 'btn danger', type: 'button' }, label);
    button.addEventListener('click', () => {
      if (!button.classList.contains('armed')) {
        button.classList.add('armed');
        button.textContent = sure;
        timer = setTimeout(() => { button.classList.remove('armed'); button.textContent = label; }, 4000);
        return;
      }
      clearTimeout(timer);
      button.classList.remove('armed');
      button.textContent = label;
      action();
    });
    return button;
  }

  function toast(message, options = {}) {
    const box = $('.toast');
    box.textContent = '';
    box.className = 'toast' + (options.bad ? ' bad' : '');
    box.append(make('span', null, message));
    if (options.reload) box.append(make('button', { class: 'btn', type: 'button', onclick: reloadPage }, 'Reload the page'));
    box.hidden = false;
    clearTimeout(toast.timer);
    toast.timer = setTimeout(() => { box.hidden = true; }, options.reload ? 12000 : 5000);
  }

  function reloadPage() {
    if (inShell) window.PadHost.navigate(window.PadHost.page, { push: false });
    else location.reload();
    $('.toast').hidden = true;
  }

  async function call(path, init) {
    const response = await api(path, init);
    const type = response.headers.get('content-type') || '';
    if (!/json/.test(type)) return response;
    const answer = await response.json();
    if (answer.error) throw new Error(answer.error);
    return answer;
  }

  async function download(path) {
    try {
      const response = await api(path);
      if (/json/.test(response.headers.get('content-type') || '')) throw new Error((await response.json()).error || 'no file');
      const name = ((response.headers.get('content-disposition') || '').match(/filename="?([^";]+)"?/) || [])[1] || 'download';
      const link = make('a', { href: URL.createObjectURL(await response.blob()), download: name });
      document.body.append(link);
      link.click();
      link.remove();
      setTimeout(() => URL.revokeObjectURL(link.href), 60000);
      toast(`Downloaded ${name}`);
    } catch (error) {
      toast(error.message, { bad: true });
    }
  }

  function picker(label, accept, chosen) {
    const input = make('input', { type: 'file', accept });
    input.addEventListener('change', () => { if (input.files[0]) chosen(input.files[0]); input.value = ''; });
    return make('label', { class: 'btn', tabindex: '0', onkeydown: e => { if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); input.click(); } } }, label, input);
  }

  // ---- Opening and closing ---------------------------------------------------------------

  const remembered = (() => { try { return JSON.parse(sessionStorage.getItem('pad-panel') || '{}'); } catch (e) { return {}; } })();
  let tab = remembered.tab || 'database';
  let open = false;

  function keep() {
    try { sessionStorage.setItem('pad-panel', JSON.stringify({ open, tab })); } catch (e) { /* storage off */ }
  }

  function toggle(force) {
    open = force === undefined ? !open : force;
    drawer.classList.toggle('open', open);
    drawer.setAttribute('aria-hidden', String(!open));
    keep();
    if (open) { show(tab); $('.close').focus(); }
  }

  function show(name) {
    tab = name;
    keep();
    for (const button of root.querySelectorAll('nav button')) button.setAttribute('aria-selected', String(button.dataset.tab === name));
    for (const section of root.querySelectorAll('section')) section.hidden = section.dataset.tab !== name;
    ({ database: drawDatabase, storage: drawStorage, requests: drawRequests, about: drawAbout })[name]();
  }

  $('.badge').addEventListener('click', () => toggle());
  $('.close').addEventListener('click', () => toggle(false));
  for (const button of root.querySelectorAll('nav button')) button.addEventListener('click', () => show(button.dataset.tab));
  drawer.addEventListener('keydown', event => { if (event.key === 'Escape') toggle(false); });
  document.addEventListener('keydown', event => {
    if ((event.ctrlKey || event.metaKey) && event.shiftKey && event.key.toLowerCase() === 'k') { event.preventDefault(); toggle(); }
  }, true);

  // ---- Database ----------------------------------------------------------------------

  const db = { list: [], name: '', table: '', offset: 0, limit: 50 };

  async function drawDatabase() {
    const section = root.querySelector('section[data-tab="database"]');
    section.textContent = '';
    section.append(make('p', { class: 'dim' }, 'Reading the databases...'));
    try {
      db.list = (await call('databases')).databases;
    } catch (error) {
      section.textContent = '';
      section.append(make('p', { class: 'error' }, error.message));
      return;
    }
    section.textContent = '';
    if (!db.list.length) {
      section.append(make('div', { class: 'empty' }, 'No SQLite database under DATA/ yet. An application makes its own on the first request that needs it - open one of its pages.'));
      return;
    }
    if (!db.list.some(d => d.name === db.name)) { db.name = db.list[0].name; db.table = ''; db.offset = 0; }
    const current = db.list.find(d => d.name === db.name);
    const select = make('select', { 'aria-label': 'Database', onchange: () => { db.name = select.value; db.table = ''; db.offset = 0; drawDatabase(); } },
      db.list.map(d => make('option', { value: d.name, selected: d.name === db.name }, `DATA/${d.name}  (${size(d.size)})`)));
    section.append(
      make('h3', null, 'Database'),
      make('div', { class: 'row' }, select),
      make('div', { class: 'row' },
        make('button', { class: 'btn', type: 'button', title: 'The database file itself - SQLite opens it anywhere', onclick: () => download(`export?db=${encodeURIComponent(db.name)}&format=sqlite`) }, '⬇ .sqlite'),
        make('button', { class: 'btn', type: 'button', title: 'The SQL that builds it again - tables, rows, indexes', onclick: () => download(`export?db=${encodeURIComponent(db.name)}&format=sql`) }, '⬇ .sql'),
        picker('⬆ Import', '.sqlite,.sqlite3,.db,.sql,.txt', file => confirmImport(file)),
        armed('Reset', 'Reset - sure?', async () => {
          try { await call(`reset?db=${encodeURIComponent(db.name)}`, { method: 'POST' }); toast(`DATA/${db.name} is gone - the application builds it again on its next request`, { reload: true }); drawDatabase(); }
          catch (error) { toast(error.message, { bad: true }); }
        })),
      make('div', { class: 'import-ask' }),
      make('h3', null, 'Tables'),
      make('div', { class: 'chips' }, current.tables.length ? current.tables.map(t => make('button', {
        class: 'chip' + (t.name === db.table ? ' on' : ''), type: 'button',
        onclick: () => { db.table = t.name; db.offset = 0; drawDatabase(); }
      }, t.name, make('small', null, t.type === 'view' ? 'view' : t.rows))) : make('span', { class: 'dim' }, 'no tables')),
      make('div', { class: 'rows' }),
      make('h3', null, 'SQL'),
      console_()
    );
    if (db.table) drawRows(section.querySelector('.rows'));
  }

  function confirmImport(file) {
    const box = root.querySelector('.import-ask');
    box.textContent = '';
    box.append(make('div', { class: 'row' },
      make('span', null, `Replace DATA/${db.name} with ${file.name} (${size(file.size)})?`),
      make('button', { class: 'btn primary', type: 'button', onclick: async () => {
        box.textContent = '';
        try {
          const answer = await call(`import?db=${encodeURIComponent(db.name)}`, { method: 'POST', body: new Uint8Array(await file.arrayBuffer()) });
          toast(`Imported: ${answer.tables.length} tables, ${size(answer.size)}`, { reload: true });
          db.table = '';
          drawDatabase();
        } catch (error) { toast(error.message, { bad: true }); }
      } }, 'Replace'),
      make('button', { class: 'btn', type: 'button', onclick: () => { box.textContent = ''; } }, 'Cancel')));
  }

  async function drawRows(box) {
    box.textContent = '';
    let answer;
    try { answer = await call(`rows?db=${encodeURIComponent(db.name)}&table=${encodeURIComponent(db.table)}&offset=${db.offset}&limit=${db.limit}`); }
    catch (error) { box.append(make('p', { class: 'error' }, error.message)); return; }
    box.append(grid(answer.columns, answer.rows));
    const last = Math.min(answer.total, db.offset + answer.rows.length);
    box.append(make('div', { class: 'pager' },
      make('button', { class: 'btn', type: 'button', disabled: db.offset === 0, onclick: () => { db.offset = Math.max(0, db.offset - db.limit); drawRows(box); } }, '‹'),
      make('span', null, answer.total ? `${db.offset + 1}–${last} of ${answer.total} rows` : 'no rows'),
      make('button', { class: 'btn', type: 'button', disabled: last >= answer.total, onclick: () => { db.offset += db.limit; drawRows(box); } }, '›')));
  }

  function grid(columns, rows) {
    return make('div', { class: 'grid-wrap' }, make('table', null,
      make('thead', null, make('tr', null, columns.map(c => make('th', null, c)))),
      make('tbody', null, rows.map(row => make('tr', null, row.map(value =>
        value === null ? make('td', { class: 'null' }, 'NULL')
        : make('td', { class: typeof value === 'number' ? 'num' : null, title: String(value).length > 40 ? String(value) : null }, String(value))))))));
  }

  let sqlText = '';

  function console_() {
    const area = make('textarea', { spellcheck: 'false', placeholder: db.table ? `SELECT * FROM "${db.table}" LIMIT 20` : 'SELECT name FROM sqlite_master', 'aria-label': 'SQL' });
    area.value = sqlText;
    const results = make('div');
    const run = async () => {
      sqlText = area.value;
      const sql = area.value.trim() || area.placeholder;
      results.textContent = '';
      try {
        const response = await api(`query?db=${encodeURIComponent(db.name)}`, { method: 'POST', headers: { 'content-type': 'application/json' }, body: JSON.stringify({ sql }) });
        const answer = await response.json();
        for (const result of answer.results || []) {
          const part = make('div', { class: 'result' }, make('div', { class: 'sql' }, `${result.sql}  ·  ${result.ms} ms`));
          if (result.columns) {
            part.append(grid(result.columns, result.rows));
            part.append(make('div', { class: 'dim' }, `${result.rows.length}${result.more ? '+' : ''} rows`));
          } else part.append(make('div', { class: 'dim' }, `${result.changes} rows changed`));
          results.append(part);
        }
        if (answer.error) results.append(make('p', { class: 'error' }, answer.error));
        if ((answer.results || []).some(r => !r.columns)) toast('The database changed', { reload: true });
      } catch (error) { results.append(make('p', { class: 'error' }, error.message)); }
    };
    area.addEventListener('keydown', event => { if ((event.ctrlKey || event.metaKey) && event.key === 'Enter') { event.preventDefault(); run(); } });
    return make('div', null, area,
      make('div', { class: 'row' }, make('button', { class: 'btn primary', type: 'button', onclick: run }, 'Run'),
                                    make('span', { class: 'dim' }, make('kbd', null, 'Ctrl'), ' + ', make('kbd', null, 'Enter'))),
      results);
  }

  // ---- Storage -----------------------------------------------------------------------

  async function drawStorage() {
    const section = root.querySelector('section[data-tab="storage"]');
    section.textContent = '';
    let files = [], info = {};
    try { [files, info] = await Promise.all([call('files').then(a => a.files), call('info')]); }
    catch (error) { section.append(make('p', { class: 'error' }, error.message)); return; }
    const total = files.reduce((sum, f) => sum + f.size, 0);
    const where = { indexeddb: 'IndexedDB', localstorage: 'localStorage', memory: 'memory only - not kept' }[info.store] || info.store;
    section.append(make('h3', null, 'What the applications wrote'),
      make('p', null, `${files.length} files under DATA/, ${size(total)}, kept in this browser's ${where}.`));
    if (navigator.storage && navigator.storage.estimate) {
      const estimate = await navigator.storage.estimate().catch(() => null);
      if (estimate && estimate.quota) {
        section.append(make('div', { class: 'meter' }, make('span', { style: `width: ${Math.max(1, Math.round(100 * estimate.usage / estimate.quota))}%` })),
                       make('div', { class: 'dim' }, `This site uses ${size(estimate.usage)} of the ${size(estimate.quota)} the browser allows it (the PHP runtime included).`));
      }
    }
    if (navigator.storage && navigator.storage.persisted) {
      const persisted = await navigator.storage.persisted().catch(() => false);
      const row = make('div', { class: 'row' }, make('span', null, persisted
        ? '✓ Kept until you clear this site\'s data yourself.'
        : 'The browser may clear it when the disk runs out of room.'));
      if (!persisted && navigator.storage.persist)
        row.append(make('button', { class: 'btn', type: 'button', onclick: async () => {
          const granted = await navigator.storage.persist().catch(() => false);
          toast(granted ? 'The browser keeps the data now' : 'The browser said no - installing the application usually makes it say yes', { bad: !granted });
          drawStorage();
        } }, 'Keep my data'));
      section.append(row);
    }
    section.append(make('h3', null, 'Backup'),
      make('div', { class: 'row' },
        make('button', { class: 'btn', type: 'button', onclick: () => download('backup') }, '⬇ Backup (.zip)'),
        picker('⬆ Restore', '.zip', async file => {
          try { const answer = await call('restore', { method: 'POST', body: new Uint8Array(await file.arrayBuffer()) }); toast(`Restored ${answer.restored} files`, { reload: true }); drawStorage(); }
          catch (error) { toast(error.message, { bad: true }); }
        }),
        armed('Forget everything', 'Forget it all - sure?', async () => {
          try { await call('forget', { method: 'POST' }); toast('Everything the applications wrote is gone', { reload: true }); drawStorage(); }
          catch (error) { toast(error.message, { bad: true }); }
        })),
      make('h3', null, 'Files'),
      files.length ? make('div', { class: 'grid-wrap' }, make('table', null,
        make('thead', null, make('tr', null, make('th', null, 'DATA/'), make('th', null, 'Size'), make('th', null, 'Changed'), make('th', null, ''))),
        make('tbody', null, files.map(f => make('tr', null,
          make('td', { title: f.path }, f.path + (f.sqlite ? '  ◆' : '')), make('td', { class: 'num' }, size(f.size)), make('td', null, when(f.modified)),
          make('td', null, make('button', { class: 'btn', type: 'button', title: 'Download', onclick: () => download(`file?path=${encodeURIComponent(f.path)}`) }, '⬇')))))))
      : make('div', { class: 'empty' }, 'Nothing written yet.'));
  }

  // ---- Requests ----------------------------------------------------------------------

  let seen = 0, cleared = 0, entries = [], polling = null;

  async function drawRequests() {
    const section = root.querySelector('section[data-tab="requests"]');
    if (!section.querySelector('table')) {
      section.textContent = '';
      section.append(make('div', { class: 'row' },
        make('span', { class: 'dim' }, 'What the application answered here - pages, fetches, live regions, event streams.'),
        make('button', { class: 'btn', type: 'button', style: 'margin-left: auto', onclick: () => { cleared = seen; entries = []; drawLog(); } }, 'Clear')),
        make('div', { class: 'grid-wrap', style: 'max-height: none' }, make('table', null,
          make('thead', null, make('tr', null, ['Time', '', 'Status', 'Kind', 'Address', 'ms', 'Size'].map(h => make('th', null, h)))),
          make('tbody'))));
    }
    await poll();
    clearInterval(polling);
    polling = setInterval(() => { if (open && tab === 'requests' && !document.hidden) poll(); else if (!open || tab !== 'requests') clearInterval(polling); }, 1000);
  }

  async function poll() {
    try {
      const answer = await call(`log?after=${seen}`);
      for (const entry of answer.log) { seen = Math.max(seen, entry.id); if (entry.id > cleared) entries.unshift(entry); }
      entries = entries.slice(0, 200);
      if (answer.log.length || !root.querySelector('section[data-tab="requests"] tbody tr')) drawLog();
    } catch (error) { /* the next poll */ }
  }

  function drawLog() {
    const body = root.querySelector('section[data-tab="requests"] tbody');
    if (!body) return;
    body.textContent = '';
    if (!entries.length) { body.append(make('tr', null, make('td', { colspan: '7', class: 'dim' }, 'No requests yet.'))); return; }
    for (const e of entries)
      body.append(make('tr', null,
        make('td', null, new Date(e.at).toLocaleTimeString()),
        make('td', null, e.method),
        make('td', null, make('span', { class: `status s${String(e.status).charAt(0)}` }, e.status)),
        make('td', null, make('span', { class: 'kind' }, e.kind)),
        make('td', { title: e.url }, '/' + e.url),
        make('td', { class: 'num' }, e.ms),
        make('td', { class: 'num' }, size(e.size))));
  }

  // ---- About -------------------------------------------------------------------------

  let aboutInfo = null;

  async function drawAbout() {
    const section = root.querySelector('section[data-tab="about"]');
    try { aboutInfo = await call('info'); }
    catch (error) { section.textContent = ''; section.append(make('p', { class: 'error' }, error.message)); return; }
    section.textContent = '';
    const info = aboutInfo;
    const installed = window.matchMedia && window.matchMedia('(display-mode: standalone)').matches;
    const how = inShell ? 'opened from the disk - PHP runs in this tab'
              : installed ? 'installed - PHP runs in the service worker, offline too'
              : 'in a browser tab - PHP runs in the service worker, offline too';
    section.append(make('h3', null, 'What runs here'), make('dl', null,
      make('dt', null, info.apps.length > 1 ? 'Applications' : 'Application'),
      make('dd', null, info.apps.map((a, i) => [i ? ', ' : '', inShell ? a.name : make('a', { href: a.url }, a.name)])),
      make('dt', null, 'How'), make('dd', null, how),
      make('dt', null, 'PHP'), make('dd', null, `${info.php} compiled to WebAssembly`),
      make('dt', null, 'Data'), make('dd', null, `${info.files} files, ${size(info.bytes)}, in ${info.store}`),
      make('dt', null, 'Network'), make('dd', null, navigator.onLine ? 'online - not needed' : 'offline - everything works'),
      make('dt', null, 'Build'), make('dd', null, `${info.build}${info.commit ? ` from PAD ${info.commit}` : ''}, ${info.built}`)));
    const actions = make('div', { class: 'row' });
    if (installEvent) actions.append(make('button', { class: 'btn primary', type: 'button', onclick: async () => {
      installEvent.prompt();
      const choice = await installEvent.userChoice.catch(() => null);
      if (choice && choice.outcome === 'accepted') installEvent = null;
      drawAbout();
    } }, 'Install as an application'));
    if (waiting) actions.append(make('button', { class: 'btn primary', type: 'button', onclick: () => {
      reloadAsked = true;
      waiting.postMessage({ type: 'pad-activate' });
    } }, 'A new build is ready - use it'));
    if (actions.childNodes.length) section.append(make('h3', null, 'Actions'), actions);
    section.append(make('h3', null, 'Keys'), make('p', { class: 'dim' }, make('kbd', null, 'Ctrl'), ' + ', make('kbd', null, 'Shift'), ' + ', make('kbd', null, 'K'), ' opens and closes this panel.'));
    $('.name').textContent = info.name || 'PAD';
    $('.sub').textContent = `PHP ${info.php} in this browser`;
  }

  function refreshAbout() {
    if (open && tab === 'about') drawAbout();
  }

  // ---- The badge's dot ---------------------------------------------------------------

  let lastState = {};

  function mark() {
    const state = lastState;
    dot.className = 'dot' + (state.error ? ' error' : state.saving ? ' saving' : waiting ? ' update' : !navigator.onLine ? ' offline' : '');
    $('.badge').title = state.error ? `PAD: ${state.error}`
                      : waiting ? 'PAD: a new build is ready - open the panel'
                      : `PAD in this browser - ${navigator.onLine ? 'saved' : 'offline, saved'} - Ctrl+Shift+K`;
  }

  async function watch() {
    if (!document.hidden) {
      try { lastState = await call('state'); } catch (e) { /* the next one */ }
      mark();
    }
    setTimeout(watch, lastState.saving ? 800 : 4000);
  }

  window.addEventListener('online', mark);
  window.addEventListener('offline', mark);

  (document.body || document.documentElement).append(element);
  window.PadPanel = { toggle, show: name => { toggle(true); show(name); } };
  call('info').then(info => { $('.name').textContent = info.name || 'PAD'; $('.sub').textContent = `PHP ${info.php} in this browser`; }).catch(() => {});
  watch();
  if (remembered.open) toggle(true);

})();
