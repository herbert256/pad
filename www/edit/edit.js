// The PAD editor's client side - a static file, nothing PAD parses. The page it runs in is
// apps/edit/index.pad; everything it reads and writes goes through the one JSON call of
// apps/edit/api.php (POST ?api&action=<name>&padFormat=json, the session's CSRF token in a
// header).
//
// The editor component is Monaco, loaded from the address in the configuration; PAD and PHP
// knowledge come from pad-mode.js and php-mode.js. When Monaco cannot be loaded - no
// internet, a wrong address - a plain textarea takes its place behind the same few calls,
// and opening, editing and saving files still work.
//
// Kept in the browser only (localStorage, each read and write in a try): the settings, the
// sizes of the panes, the open tabs and the folders opened in the tree.

(function () {

  'use strict';

  var boot = JSON.parse(document.body.getAttribute('data-boot') || '{}');
  var csrf = (document.querySelector('meta[name="csrf-token"]') || {}).content || '';
  var isMac = /Mac|iPhone|iPad/.test(navigator.platform || navigator.userAgent);
  var MOD = isMac ? '⌘' : 'Ctrl+';

  var E = {
    app: '',
    apps: [],
    files: [],            // the flat list of the current application's tree
    fileIndex: {},        // root|path -> entry
    git: null,            // root -> path -> mark
    branch: '',
    base: null,           // the language data shared by every application
    appLang: {},          // app -> its own names
    fields: {},           // app|path -> fields of a template
    docs: {},             // app|root|path -> an open document
    tabs: [],             // keys of the open tabs, in order
    active: null,
    expanded: {},         // app -> { root|path: true }
    selected: null,       // { root, path, dir }
    monaco: null,
    real: false,          // Monaco proper, not the fallback
    editor: null,
    previewOn: false,
    previewPage: {},      // app -> the page last shown
    panelTab: 'problems'
  };

  var DEFAULTS = { theme: 'auto', fontSize: 14, tabSize: 2, wordWrap: 'off', minimap: true, checkTyping: false,
                   autosave: 'off', autoClose: true, sideWidth: 280, panelHeight: 220, previewWidth: 480,
                   panelOpen: true, sideOpen: true };

  // ------------------------------------------------------------------------------------
  // Small things
  // ------------------------------------------------------------------------------------

  function $(id) { return document.getElementById(id); }

  function el(tag, attrs, children) {
    var node = document.createElement(tag);
    Object.keys(attrs || {}).forEach(function (k) {
      var v = attrs[k];
      if (v === null || v === undefined || v === false) return;
      if (k === 'class') node.className = v;
      else if (k === 'text') node.textContent = v;
      else if (k === 'html') node.innerHTML = v;
      else if (k.slice(0, 2) === 'on') node.addEventListener(k.slice(2), v);
      else if (k === 'style' && typeof v === 'object') Object.assign(node.style, v);
      else node.setAttribute(k, v === true ? '' : v);
    });
    (children || []).forEach(function (c) {
      if (c === null || c === undefined || c === false) return;
      node.appendChild(typeof c === 'string' ? document.createTextNode(c) : c);
    });
    return node;
  }

  function store(key, value) {
    try {
      if (value === undefined) return JSON.parse(localStorage.getItem('padEdit.' + key) || 'null');
      localStorage.setItem('padEdit.' + key, JSON.stringify(value));
    } catch (e) { /* storage blocked: nothing kept */ }
    return null;
  }

  var settings = Object.assign({}, DEFAULTS, store('settings') || {});

  function saveSettings() { store('settings', settings); }

  function debounce(fn, ms) {
    var t = null;
    return function () {
      var args = arguments, self = this;
      clearTimeout(t);
      t = setTimeout(function () { fn.apply(self, args); }, ms);
    };
  }

  function ext(path) {
    var base = (path || '').split('/').pop().toLowerCase();
    if (base.charAt(0) === '.' && base.indexOf('.', 1) < 0) return base.slice(1);
    var i = base.lastIndexOf('.');
    return i < 0 ? '' : base.slice(i + 1);
  }

  function basename(path) { return (path || '').split('/').pop(); }
  function dirname(path) { var i = (path || '').lastIndexOf('/'); return i < 0 ? '' : path.slice(0, i); }
  function join(dir, name) { return dir ? dir + '/' + name : name; }

  function size(n) {
    if (n < 1024) return n + ' B';
    if (n < 1048576) return (n / 1024).toFixed(1) + ' KB';
    return (n / 1048576).toFixed(1) + ' MB';
  }

  function ago(seconds) {
    var d = Math.max(0, Math.round(Date.now() / 1000 - seconds));
    if (d < 60) return d + ' s ago';
    if (d < 3600) return Math.round(d / 60) + ' min ago';
    if (d < 86400) return Math.round(d / 3600) + ' h ago';
    return new Date(seconds * 1000).toLocaleString();
  }

  function key(app, root, path) { return app + '|' + root + '|' + path; }

  function langOf(root, path) {
    var e = ext(path);
    if (e === 'pad' || (e === 'html' && root === 'app')) return 'pad';
    return ({ php: 'php', js: 'javascript', mjs: 'javascript', jsx: 'javascript', ts: 'typescript', tsx: 'typescript',
              css: 'css', scss: 'scss', less: 'less', json: 'json', md: 'markdown', xml: 'xml', svg: 'xml', curl: 'xml',
              yaml: 'yaml', yml: 'yaml', sql: 'sql', sh: 'shell', ini: 'ini', env: 'ini', htaccess: 'ini',
              html: 'html', htm: 'html' })[e] || 'plaintext';
  }

  var LANG_NAMES = { pad: 'PAD', php: 'PHP', javascript: 'JavaScript', typescript: 'TypeScript', css: 'CSS', scss: 'SCSS',
                     less: 'Less', json: 'JSON', markdown: 'Markdown', xml: 'XML', yaml: 'YAML', sql: 'SQL', shell: 'Shell',
                     ini: 'INI', html: 'HTML', plaintext: 'Plain text' };

  var BADGES = { pad: ['PAD', 'b-pad'], php: ['PHP', 'b-php'], html: ['HTM', 'b-html'], htm: ['HTM', 'b-html'],
                 js: ['JS', 'b-js'], mjs: ['JS', 'b-js'], css: ['CSS', 'b-css'], json: ['{}', 'b-json'], md: ['MD', 'b-md'],
                 xml: ['XML', 'b-xml'], svg: ['SVG', 'b-img'], png: ['IMG', 'b-img'], jpg: ['IMG', 'b-img'], jpeg: ['IMG', 'b-img'],
                 gif: ['IMG', 'b-img'], webp: ['IMG', 'b-img'], ico: ['ICO', 'b-img'], sql: ['SQL', 'b-sql'], txt: ['TXT', 'b-txt'],
                 yaml: ['YML', 'b-xml'], yml: ['YML', 'b-xml'], csv: ['CSV', 'b-txt'], sh: ['SH', 'b-sql'], curl: ['URL', 'b-xml'] };

  var DIR_HINTS = {
    _lib: 'PHP functions, included on every request below',
    _include: 'Template snippets - {name}',
    _tags: 'Custom tags - {name}',
    _functions: 'Pipe functions - | name',
    _callbacks: "Iteration callbacks - callback='name'",
    _options: 'Tag options - {tag name}',
    _events: 'Event hooks - error, sql, curl, output',
    _config: 'Configuration - config.php',
    _data: 'Data files and named .sql queries',
    _scripts: 'Shell scripts',
    _mail: "E-mail templates - {mail template='name'}",
    _tests: 'Application tests - pad test',
    _lang: "Translation catalogs - {trans 'key'}",
    _content: "Markdown collections - {collection 'name'}",
    _samples: 'Sample data for the designer preview',
    _layouts: "Layouts - {extends '_layouts/name'}",
    _guard: 'Decides access to every page below',
    _inits: 'Runs before (php) or wraps (pad) every page below',
    _exits: 'Runs after (php) or closes (pad) every page below'
  };

  // Stroke icons, 16 x 16.
  var ICON_PATHS = {
    file: 'M4 1.5h5l3 3V14.5H4z M9 1.5v3h3',
    filePlus: 'M4 1.5h5l3 3V14.5H4z M9 1.5v3h3 M8 7.5v5 M5.5 10h5',
    folderPlus: 'M1.5 3.5h4l1.5 1.5h7.5v8.5h-13z M8 7v4.5 M5.75 9.25h4.5',
    template: 'M2 2h12v4H2z M2 8h5v6H2z M9 8h5v6H9z',
    upload: 'M8 11V2.5 M4.5 6 8 2.5 11.5 6 M2.5 11v2.5h11V11',
    refresh: 'M13 8a5 5 0 1 1-1.5-3.6 M13 2.5v2.5h-2.5',
    collapse: 'M4 6l4 4 4-4 M2.5 2.5h11',
    search: 'M7 12a5 5 0 1 0 0-10 5 5 0 0 0 0 10z M10.5 10.5l4 4',
    save: 'M2.5 2.5h9l2 2v9h-11z M5 2.5v3.5h6V2.5 M5 13.5V9h6v4.5',
    eye: 'M1 8s2.5-5 7-5 7 5 7 5-2.5 5-7 5-7-5-7-5z M8 10a2 2 0 1 0 0-4 2 2 0 0 0 0 4z',
    panel: 'M1.5 2.5h13v11h-13z M1.5 10h13',
    side: 'M1.5 2.5h13v11h-13z M5.5 2.5v11',
    more: 'M3 8h.01 M8 8h.01 M13 8h.01',
    close: 'M4 4l8 8 M12 4l-8 8',
    chevron: 'M6 4l4 4-4 4',
    phone: 'M5 1.5h6v13H5z M7.5 12.5h1',
    tablet: 'M3 1.5h10v13H3z M7.5 12.5h1',
    desktop: 'M1.5 2.5h13v8.5h-13z M6 14h4 M8 11v3',
    external: 'M9 2.5h4.5V7 M13.5 2.5 7 9 M11.5 9.5v4h-9v-9h4',
    check: 'M3 8.5l3 3 7-7',
    git: 'M5 3v10 M11 5.5v1a3 3 0 0 1-3 3H5 M5 4.5a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3z M11 5.5a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3z M5 14.5a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3z',
    history: 'M2.5 8a5.5 5.5 0 1 0 1.6-3.9 M2.5 2.5v2.5H5 M8 5v3l2 1.5',
    pair: 'M3 5h8l-2-2 M13 11H5l2 2',
    trash: 'M2.5 4h11 M6 4V2.5h4V4 M3.5 4l.75 10h7.5L12.5 4',
    user: 'M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6z M2.5 14.5c.5-3 3-4.5 5.5-4.5s5 1.5 5.5 4.5',
    gear: 'M8 10.25a2.25 2.25 0 1 0 0-4.5 2.25 2.25 0 0 0 0 4.5z M8 1.5v2 M8 12.5v2 M1.5 8h2 M12.5 8h2 M3.4 3.4l1.4 1.4 M11.2 11.2l1.4 1.4 M3.4 12.6l1.4-1.4 M11.2 4.8l1.4-1.4',
    keyboard: 'M1.5 4h13v8h-13z M4 6.5h.01 M6.5 6.5h.01 M9 6.5h.01 M11.5 6.5h.01 M4.5 9.5h7',
    apps: 'M2 2h5v5H2z M9 2h5v5H9z M2 9h5v5H2z M9 9h5v5H9z',
    play: 'M4.5 2.5l9 5.5-9 5.5z',
    logout: 'M6 2.5H2.5v11H6 M10.5 11l3-3-3-3 M13.5 8H6',
    download: 'M8 2.5V11 M4.5 7.5 8 11l3.5-3.5 M2.5 13.5h11',
    copy: 'M5.5 5.5h8v8h-8z M2.5 10.5v-8h8',
    rename: 'M2 14l1-4 8.5-8.5 3 3L6 13z M10 3l3 3',
    warning: 'M8 1.5l7 12.5H1z M8 6v4 M8 12h.01',
    info: 'M8 14.5a6.5 6.5 0 1 0 0-13 6.5 6.5 0 0 0 0 13z M8 7v4.5 M8 4.5h.01',
    sun: 'M8 11a3 3 0 1 0 0-6 3 3 0 0 0 0 6z M8 1v1.5 M8 13.5V15 M1 8h1.5 M13.5 8H15 M3 3l1 1 M12 12l1 1 M3 13l1-1 M12 4l1-1',
    moon: 'M13.5 9.5A6 6 0 0 1 6.5 2.5a6 6 0 1 0 7 7z',
    terminal: 'M1.5 2.5h13v11h-13z M4 6l2.5 2L4 10 M8 10.5h4'
  };

  function icon(name, cls) {
    var ns = 'http://www.w3.org/2000/svg';
    var svg = document.createElementNS(ns, 'svg');
    svg.setAttribute('viewBox', '0 0 16 16');
    svg.setAttribute('class', 'icon' + (cls ? ' ' + cls : ''));
    svg.setAttribute('aria-hidden', 'true');
    var path = document.createElementNS(ns, 'path');
    path.setAttribute('d', ICON_PATHS[name] || ICON_PATHS.file);
    svg.appendChild(path);
    return svg;
  }

  function button(label, iconName, onclick, opts) {
    opts = opts || {};
    return el('button', { type: 'button', class: 'btn' + (opts.class ? ' ' + opts.class : ''), title: opts.title || label,
                          'aria-label': opts.title || label, onclick: onclick, disabled: opts.disabled },
              [iconName ? icon(iconName) : null, opts.iconOnly ? null : el('span', { text: label })]);
  }

  // ------------------------------------------------------------------------------------
  // Toasts
  // ------------------------------------------------------------------------------------

  function toast(message, kind, ms) {
    var box = $('toasts');
    var t = el('div', { class: 'toast toast-' + (kind || 'info'), role: kind === 'error' ? 'alert' : 'status' },
               [icon(kind === 'error' ? 'warning' : kind === 'ok' ? 'check' : 'info'), el('span', { text: message })]);
    box.appendChild(t);
    setTimeout(function () { t.classList.add('gone'); setTimeout(function () { t.remove(); }, 300); }, ms || (kind === 'error' ? 6000 : 2500));
  }

  // ------------------------------------------------------------------------------------
  // The server
  // ------------------------------------------------------------------------------------

  var reloginWait = null;

  function api(action, body, retried) {
    return fetch('?api&action=' + action + '&padFormat=json', {
      method: 'POST',
      credentials: 'same-origin',
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-Token': csrf },
      body: JSON.stringify(body || {})
    }).then(function (res) {
      if (res.status === 403 && !retried) return relogin().then(function () { return api(action, body, true); });
      return res.json().catch(function () { throw new Error('the server answered ' + res.status + ' ' + res.statusText); })
        .then(function (json) {
          if (!json || typeof json !== 'object') throw new Error('no answer (' + res.status + ')');
          if (!json.ok) throw new Error(json.error || 'the call failed (' + res.status + ')');
          return json.data;
        });
    });
  }

  function upload(dir, root, files) {
    var limit = (boot.limits && boot.limits.upload) || 0;
    var total = 0, form = new FormData();
    form.append('app', E.app);
    form.append('root', root);
    form.append('dir', dir);
    for (var i = 0; i < files.length; i++) {
      if (limit && files[i].size > limit) { toast(files[i].name + ' is larger than the ' + size(limit) + ' this server takes', 'error'); return Promise.resolve(); }
      total += files[i].size;
      form.append('files[]', files[i], files[i].name);
    }
    if (boot.limits && boot.limits.post && total > boot.limits.post) { toast('together the files are larger than the ' + size(boot.limits.post) + ' this server takes', 'error'); return Promise.resolve(); }
    return fetch('?api&action=upload&padFormat=json', {
      method: 'POST', credentials: 'same-origin', headers: { 'Accept': 'application/json', 'X-CSRF-Token': csrf }, body: form
    }).then(function (res) { return res.json(); }).then(function (json) {
      if (!json.ok) throw new Error(json.error);
      toast(json.data.saved.length + ' file(s) uploaded', 'ok');
      return refreshTree();
    }).catch(function (e) { toast('Upload: ' + e.message, 'error'); });
  }

  // A 403: the session ran out, or its token did. The unsaved buffers are put aside first,
  // in case this page gets closed; then the login page is asked: when the session still
  // holds a user it sends us on to the editor, whose page has the token; otherwise its form
  // has one, and the login is posted from a dialog.
  function relogin() {
    if (reloginWait) return reloginWait;
    stash();
    reloginWait = fetch('?login', { credentials: 'same-origin' }).then(function (res) { return res.text(); }).then(function (html) {
      var token = html.match(/<meta name="csrf-token" content="([0-9a-f]{64})"/);
      if (token) { csrf = token[1]; return; }
      return loginDialog(html);
    }).then(function () { reloginWait = null; }, function (e) { reloginWait = null; throw e; });
    return reloginWait;
  }

  function loginDialog(html) {
    return new Promise(function (resolve) {
      var token = (html.match(/name="padCsrfToken" value="([0-9a-f]{64})"/) || [])[1] || '';
      var name = el('input', { type: 'text', autocomplete: 'username', value: boot.user || '', required: true });
      var pass = el('input', { type: 'password', autocomplete: 'current-password', required: true });
      var msg = el('p', { class: 'form-error', role: 'alert' });
      var form = el('form', { class: 'form', onsubmit: function (ev) {
        ev.preventDefault();
        var data = new URLSearchParams();
        data.append('padForm', 'login');
        data.append('padCsrfToken', token);
        data.append('name', name.value);
        data.append('password', pass.value);
        fetch('?login', { method: 'POST', credentials: 'same-origin', body: data }).then(function (res) { return res.text(); }).then(function (page) {
          var t = page.match(/<meta name="csrf-token" content="([0-9a-f]{64})"/);
          if (t) { csrf = t[1]; close(); toast('Logged in again', 'ok'); resolve(); return; }
          var again = page.match(/name="padCsrfToken" value="([0-9a-f]{64})"/);
          if (again) token = again[1];
          var err = page.match(/class="login-error"[^>]*>([^<]*)</);
          msg.textContent = err ? err[1].replace(/&#0?39;/g, "'").replace(/&quot;/g, '"').replace(/&amp;/g, '&') : 'That did not work.';
        }).catch(function (e) { msg.textContent = e.message; });
      } }, [
        el('p', { text: 'The session has ended. Log in again to go on - your unsaved changes are kept.' }),
        el('label', {}, ['Name', name]), el('label', {}, ['Password', pass]), msg,
        el('div', { class: 'form-buttons' }, [el('button', { type: 'submit', class: 'btn primary' }, ['Log in'])])
      ]);
      var close = modal('Log in again', form, { closable: false });
      setTimeout(function () { (name.value ? pass : name).focus(); }, 50);
    });
  }

  function stash() {
    var dirty = openDocs().filter(isDirty).map(function (d) { return { app: d.app, root: d.root, path: d.path, text: d.model.getValue(), sha1: d.sha1 }; });
    store('stash', dirty.length ? dirty : null);
  }

  // ------------------------------------------------------------------------------------
  // Dialogs and menus
  // ------------------------------------------------------------------------------------

  var modals = [];

  function modal(title, body, opts) {
    opts = opts || {};
    var layer = $('layer');
    var last = document.activeElement;
    var closeBtn = opts.closable === false ? null : button('Close', 'close', function () { close(); }, { iconOnly: true, class: 'ghost' });
    var box = el('div', { class: 'modal' + (opts.wide ? ' wide' : '') + (opts.full ? ' full' : ''), role: 'dialog', 'aria-modal': 'true', 'aria-label': title }, [
      el('header', { class: 'modal-head' }, [el('h2', { text: title }), opts.headExtra || null, closeBtn]),
      el('div', { class: 'modal-body' }, [body])
    ]);
    var shade = el('div', { class: 'shade', onmousedown: function (e) { if (e.target === shade && opts.closable !== false) close(); } }, [box]);
    layer.appendChild(shade);
    var first = box.querySelector('[autofocus], input, select, textarea, button.primary');
    if (first) first.focus();
    var entry = { close: close, closable: opts.closable !== false };
    modals.push(entry);
    function close() {
      if (!shade.parentNode) return;
      shade.remove();
      modals.splice(modals.indexOf(entry), 1);
      if (opts.onclose) opts.onclose();
      if (last && last.focus) last.focus();
    }
    return close;
  }

  function ask(title, label, value, opts) {
    opts = opts || {};
    return new Promise(function (resolve) {
      var input = el('input', { type: 'text', value: value || '', spellcheck: 'false', autofocus: true });
      var done = false;
      var form = el('form', { class: 'form', onsubmit: function (e) {
        e.preventDefault();
        done = true;
        close();
        resolve(input.value.trim());
      } }, [
        opts.note ? el('p', { class: 'note', text: opts.note }) : null,
        el('label', {}, [label, input]),
        el('div', { class: 'form-buttons' }, [
          el('button', { type: 'button', class: 'btn', onclick: function () { close(); } }, ['Cancel']),
          el('button', { type: 'submit', class: 'btn primary' }, [opts.ok || 'OK'])
        ])
      ]);
      var close = modal(title, form, { onclose: function () { if (!done) resolve(null); } });
      setTimeout(function () {
        input.focus();
        var dot = (value || '').lastIndexOf('.');
        if (opts.selectName && dot > 0) input.setSelectionRange((value || '').lastIndexOf('/') + 1, dot); else input.select();
      }, 40);
    });
  }

  function confirmBox(title, message, ok, danger) {
    return new Promise(function (resolve) {
      var done = false;
      var body = el('div', { class: 'form' }, [
        el('p', { text: message }),
        el('div', { class: 'form-buttons' }, [
          el('button', { type: 'button', class: 'btn', onclick: function () { close(); } }, ['Cancel']),
          el('button', { type: 'button', class: 'btn primary' + (danger ? ' danger' : ''), autofocus: true,
                         onclick: function () { done = true; close(); resolve(true); } }, [ok || 'OK'])
        ])
      ]);
      var close = modal(title, body, { onclose: function () { if (!done) resolve(false); } });
    });
  }

  var menuOpen = null;

  function contextMenu(x, y, items) {
    closeMenu();
    var list = el('div', { class: 'menu', role: 'menu' });
    items.forEach(function (it) {
      if (it === '-') { list.appendChild(el('div', { class: 'menu-sep', role: 'separator' })); return; }
      if (!it) return;
      list.appendChild(el('button', { type: 'button', class: 'menu-item' + (it.danger ? ' danger' : ''), role: 'menuitem', disabled: it.disabled,
                                      onclick: function () { closeMenu(); it.run(); } },
                          [icon(it.icon || 'chevron', 'menu-icon'), el('span', { text: it.label }), it.keys ? el('kbd', { text: it.keys }) : null]));
    });
    document.body.appendChild(list);
    var r = list.getBoundingClientRect();
    list.style.left = Math.min(x, window.innerWidth - r.width - 8) + 'px';
    list.style.top = Math.min(y, window.innerHeight - r.height - 8) + 'px';
    menuOpen = list;
    var first = list.querySelector('button:not([disabled])');
    if (first) first.focus();
    list.addEventListener('keydown', function (e) {
      var all = Array.prototype.slice.call(list.querySelectorAll('button:not([disabled])'));
      var i = all.indexOf(document.activeElement);
      if (e.key === 'ArrowDown') { e.preventDefault(); all[(i + 1) % all.length].focus(); }
      if (e.key === 'ArrowUp') { e.preventDefault(); all[(i - 1 + all.length) % all.length].focus(); }
    });
  }

  function closeMenu() {
    if (menuOpen) { menuOpen.remove(); menuOpen = null; }
  }

  document.addEventListener('mousedown', function (e) { if (menuOpen && !menuOpen.contains(e.target)) closeMenu(); });

  // ------------------------------------------------------------------------------------
  // The editor component - Monaco, or the textarea that stands in for it
  // ------------------------------------------------------------------------------------

  function loadMonaco() {
    return new Promise(function (resolve, reject) {
      if (!boot.monaco) { reject(new Error('no address')); return; }
      var timer = setTimeout(function () { reject(new Error('Monaco did not load in time')); }, 25000);
      var script = document.createElement('script');
      script.src = boot.monaco.replace(/\/$/, '') + '/loader.js';
      script.onerror = function () { clearTimeout(timer); reject(new Error('Monaco could not be loaded from ' + boot.monaco)); };
      script.onload = function () {
        try {
          window.require.config({ paths: { vs: boot.monaco.replace(/\/$/, '') } });
          window.require(['vs/editor/editor.main'], function () { clearTimeout(timer); resolve(window.monaco); },
                         function (err) { clearTimeout(timer); reject(err); });
        } catch (e) { clearTimeout(timer); reject(e); }
      };
      document.head.appendChild(script);
    });
  }

  // The textarea version of the little of Monaco this file uses.
  function fallbackMonaco() {
    var models = [];
    var area = el('textarea', { class: 'fallback-area', spellcheck: 'false', 'aria-label': 'File contents' });
    var current = null, cursorCbs = [];
    function Model(text, lang, uri) {
      this.text = text; this.lang = lang; this.uri = uri; this.version = 1; this.cbs = []; this.view = null;
    }
    Model.prototype.getValue = function () { return this.text; };
    Model.prototype.setValue = function (t) { this.text = t; this.version++; if (current === this) area.value = t; this.cbs.forEach(function (cb) { cb({ changes: [] }); }); };
    Model.prototype.getAlternativeVersionId = function () { return this.version; };
    Model.prototype.onDidChangeContent = function (cb) { this.cbs.push(cb); return { dispose: function () {} }; };
    Model.prototype.getLanguageId = function () { return this.lang; };
    Model.prototype.dispose = function () { models.splice(models.indexOf(this), 1); };
    Model.prototype.isDisposed = function () { return models.indexOf(this) < 0; };
    Model.prototype.getLineCount = function () { return this.text.split('\n').length; };
    area.addEventListener('input', function () {
      if (!current) return;
      current.text = area.value; current.version++;
      current.cbs.forEach(function (cb) { cb({ changes: [] }); });
    });
    area.addEventListener('keyup', function () { cursorCbs.forEach(function (cb) { cb(); }); });
    area.addEventListener('click', function () { cursorCbs.forEach(function (cb) { cb(); }); });
    area.addEventListener('keydown', function (e) {
      if (e.key === 'Tab' && !e.ctrlKey && !e.metaKey) {
        e.preventDefault();
        document.execCommand('insertText', false, ' '.repeat(settings.tabSize));
      }
    });
    var editor = {
      area: area,
      getModel: function () { return current; },
      setModel: function (m) { if (current) current.view = area.selectionStart; current = m; area.value = m ? m.text : ''; area.disabled = !m;
                               if (m && m.view !== null) area.setSelectionRange(m.view, m.view); },
      focus: function () { area.focus(); },
      layout: function () {},
      updateOptions: function (o) { if (o.fontSize) area.style.fontSize = o.fontSize + 'px'; if (o.wordWrap) area.style.whiteSpace = o.wordWrap === 'on' ? 'pre-wrap' : 'pre'; },
      getPosition: function () {
        var before = area.value.slice(0, area.selectionStart).split('\n');
        return { lineNumber: before.length, column: before[before.length - 1].length + 1 };
      },
      setPosition: function (p) {
        var lines = area.value.split('\n'), at = 0;
        for (var i = 0; i < p.lineNumber - 1 && i < lines.length; i++) at += lines[i].length + 1;
        at += (p.column || 1) - 1;
        area.setSelectionRange(at, at);
      },
      revealLineInCenter: function () {},
      saveViewState: function () { return area.selectionStart; },
      restoreViewState: function () {},
      onDidChangeCursorPosition: function (cb) { cursorCbs.push(cb); return { dispose: function () {} }; },
      onDidChangeModelContent: function () { return { dispose: function () {} }; },
      addAction: function () {},
      getSelection: function () { return null; },
      trigger: function () {}
    };
    return {
      fake: true,
      Uri: { parse: function (s) { return { toString: function () { return s; } }; } },
      Range: function () {},
      MarkerSeverity: { Error: 8, Warning: 4 },
      editor: {
        create: function (host) { host.appendChild(area); return editor; },
        createModel: function (text, lang, uri) { var m = new Model(text, lang, uri); models.push(m); return m; },
        setModelMarkers: function () {},
        getModelMarkers: function () { return []; },
        onDidChangeMarkers: function () {},
        setTheme: function () {},
        setModelLanguage: function (m, l) { m.lang = l; },
        registerEditorOpener: function () {}
      },
      languages: {}
    };
  }

  function themeName() {
    var dark = settings.theme === 'dark' || (settings.theme === 'auto' && window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches);
    document.documentElement.setAttribute('data-theme', dark ? 'dark' : 'light');
    return dark ? 'pad-dark' : 'pad-light';
  }

  function editorOptions() {
    return {
      fontSize: settings.fontSize,
      tabSize: settings.tabSize,
      insertSpaces: true,
      detectIndentation: true,
      wordWrap: settings.wordWrap,
      minimap: { enabled: !!settings.minimap },
      linkedEditing: true,
      glyphMargin: true,
      bracketPairColorization: { enabled: true },
      guides: { bracketPairs: 'active', indentation: true },
      renderWhitespace: 'selection',
      smoothScrolling: true,
      cursorBlinking: 'smooth',
      cursorSmoothCaretAnimation: 'on',
      stickyScroll: { enabled: true },
      fontLigatures: true,
      fontFamily: "'JetBrains Mono', 'SF Mono', Menlo, Consolas, 'Liberation Mono', monospace",
      scrollBeyondLastLine: false,
      automaticLayout: true,
      quickSuggestions: { other: true, comments: false, strings: true },
      suggest: { showWords: false, preview: true },
      padding: { top: 8 }
    };
  }

  function setupEditor(monaco) {
    E.monaco = monaco;
    E.real = !monaco.fake;
    var host = $('editorHost');
    if (E.real) {
      var hooks = {
        base: function () { return E.base; },
        appLang: function () { var d = currentDoc(); return E.appLang[d ? d.app : E.app] || null; },
        fileOf: function (model) { var d = docOfModel(model); return d ? { app: d.app, root: d.root, path: d.path } : null; },
        fields: function (model) { var d = docOfModel(model); return d ? (E.fields[d.app + '|' + d.path] || []) : []; },
        modelFor: function (app, root, path) { return ensureDoc(app, root, path).then(function (d) { return d && d.model; }); },
        exists: function (root, path) { return !!E.fileIndex[root + '|' + path]; },
        settings: function () { return settings; }
      };
      window.PadMode.register(monaco, hooks);
      window.PhpMode.register(monaco, hooks);
      monaco.editor.registerEditorOpener({
        openCodeEditor: function (source, resource, selection) {
          var d = docByUri(resource.toString());
          if (!d) return false;
          var line = selection ? (selection.startLineNumber || selection.lineNumber || 1) : 1;
          var col = selection ? (selection.startColumn || selection.column || 1) : 1;
          openFile(d.app, d.root, d.path, { line: line, column: col });
          return true;
        }
      });
      monaco.editor.onDidChangeMarkers(debounce(renderProblems, 150));
    }
    E.editor = monaco.editor.create(host, Object.assign({ model: null, theme: themeName() }, editorOptions()));
    if (E.real) {
      window.PadMode.closeOnType(monaco, hooks, E.editor);
      registerActions();
      if (boot.debug) dbgSetup();
    }
    E.editor.onDidChangeCursorPosition(function () { renderStatus(); });
    if (window.matchMedia)
      window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function () { if (settings.theme === 'auto') applySettings(); });
  }

  function applySettings() {
    if (!E.monaco) return;
    E.monaco.editor.setTheme(themeName());
    E.editor.updateOptions(editorOptions());
    document.documentElement.style.setProperty('--side-width', settings.sideWidth + 'px');
    document.documentElement.style.setProperty('--panel-height', settings.panelHeight + 'px');
    document.documentElement.style.setProperty('--preview-width', settings.previewWidth + 'px');
    document.body.classList.toggle('no-side', !settings.sideOpen);
    document.body.classList.toggle('no-panel', !settings.panelOpen);
    openDocs().forEach(function (d) { if (d.model && d.model.updateOptions) d.model.updateOptions({ tabSize: settings.tabSize }); });
  }

  // ------------------------------------------------------------------------------------
  // Documents and tabs
  // ------------------------------------------------------------------------------------

  function openDocs() { return Object.keys(E.docs).map(function (k) { return E.docs[k]; }); }

  function currentDoc() { return E.active ? E.docs[E.active] : null; }

  function docOfModel(model) {
    if (!model) return null;
    for (var k in E.docs) if (E.docs[k].model === model) return E.docs[k];
    return null;
  }

  function docByUri(uri) {
    for (var k in E.docs) if (E.docs[k].model && E.docs[k].model.uri.toString() === uri) return E.docs[k];
    return null;
  }

  function isDirty(d) { return d.kind === 'text' && d.model && d.model.getAlternativeVersionId() !== d.savedVersion; }

  function uriOf(app, root, path) {
    return E.monaco.Uri.parse('pad://edit/' + encodeURIComponent(app) + '/' + root + '/' + path.split('/').map(encodeURIComponent).join('/'));
  }

  // A document - read from the server once, then kept: a tab shows it, a definition lookup
  // may load one without a tab.
  function ensureDoc(app, root, path) {
    var k = key(app, root, path);
    if (E.docs[k]) return Promise.resolve(E.docs[k]);
    if (E.docs[k + '#loading']) return E.docs[k + '#loading'];
    var p = api('open', { app: app, root: root, path: path }).then(function (info) {
      delete E.docs[k + '#loading'];
      if (E.docs[k]) return E.docs[k];
      var d = { app: app, root: root, path: path, sha1: info.sha1, mtime: info.mtime, size: info.size, writable: info.writable,
                kind: info.text !== null ? 'text' : info.image ? 'image' : 'binary', info: info, model: null, view: null, changedOnDisk: false };
      if (d.kind === 'text') {
        d.model = E.monaco.editor.createModel(info.text, langOf(root, path), uriOf(app, root, path));
        d.savedVersion = d.model.getAlternativeVersionId();
        d.model.onDidChangeContent(function () { onChange(d); });
      }
      E.docs[k] = d;
      if (d.kind === 'text' && d.model.getLanguageId() === 'pad') loadFields(d);
      if (d.kind === 'text') dbgDecorate(d);
      return d;
    });
    E.docs[k + '#loading'] = p;
    p.catch(function () { delete E.docs[k + '#loading']; });
    return p;
  }

  function openFile(app, root, path, at) {
    return ensureDoc(app, root, path).then(function (d) {
      var k = key(app, root, path);
      if (E.tabs.indexOf(k) < 0) {
        var i = E.active ? E.tabs.indexOf(E.active) : -1;
        E.tabs.splice(i + 1, 0, k);
      }
      activate(k);
      if (at && d.kind === 'text') {
        E.editor.setPosition({ lineNumber: at.line || 1, column: at.column || 1 });
        if (E.editor.revealLineInCenter) E.editor.revealLineInCenter(at.line || 1);
        if (at.length && E.real) E.editor.setSelection(new E.monaco.Range(at.line, at.column, at.line, at.column + at.length));
      }
      if (d.kind === 'text') E.editor.focus();
      persistTabs();
      return d;
    }).catch(function (e) { toast(basename(path) + ': ' + e.message, 'error'); });
  }

  function activate(k) {
    var prev = currentDoc();
    if (prev && prev.kind === 'text' && E.editor.getModel() === prev.model) prev.view = E.editor.saveViewState();
    E.active = k;
    var d = E.docs[k];
    $('welcome').hidden = !!d;
    $('viewer').hidden = !d || d.kind === 'text';
    $('editorHost').style.visibility = d && d.kind === 'text' ? 'visible' : 'hidden';
    if (d && d.kind === 'text') {
      E.editor.setModel(d.model);
      if (d.view) E.editor.restoreViewState(d.view);
      E.editor.updateOptions({ readOnly: !d.writable });
    } else {
      E.editor.setModel(null);
      if (d) showViewer(d);
    }
    if (d && d.app === E.app) select(d.root, d.path, false, true);
    if (d && d.kind === 'text' && !d.checked) { d.checked = true; check(d, false); }
    renderTabs();
    renderStatus();
    renderProblems();
    if (E.panelTab === 'history') renderHistory();
    if (E.previewOn) previewFor(d);
    document.title = (d ? basename(d.path) + (isDirty(d) ? ' ●' : '') + ' - ' : '') + 'PAD edit';
  }

  function showViewer(d) {
    var v = $('viewer');
    v.textContent = '';
    if (d.kind === 'image' && d.info.base64) {
      v.appendChild(el('div', { class: 'image-view' }, [
        el('img', { src: 'data:' + d.info.image + ';base64,' + d.info.base64, alt: basename(d.path) }),
        el('p', { text: basename(d.path) + ' - ' + size(d.size) })
      ]));
    } else {
      v.appendChild(el('div', { class: 'binary-view' }, [
        icon('file', 'big'),
        el('h3', { text: basename(d.path) }),
        el('p', { text: 'A binary file of ' + size(d.size) + ' - not shown as text.' }),
        d.info.base64 ? button('Download', 'download', function () { download(d); }) : null
      ]));
    }
  }

  function closeTab(k, force) {
    var d = E.docs[k];
    if (!d) return Promise.resolve();
    var go = function () {
      var i = E.tabs.indexOf(k);
      E.tabs.splice(i, 1);
      if (E.active === k) E.active = null;
      if (d.model) { E.monaco.editor.setModelMarkers(d.model, 'pad', []); d.model.dispose(); }
      delete E.docs[k];
      var next = E.tabs[Math.min(i, E.tabs.length - 1)];
      activate(next || null);
      persistTabs();
    };
    if (!force && isDirty(d)) {
      return confirmBox('Unsaved changes', basename(d.path) + ' has changes that are not saved. Close it anyway and lose them?', 'Close without saving', true)
        .then(function (yes) { if (yes) go(); });
    }
    go();
    return Promise.resolve();
  }

  function onChange(d) {
    dbgTrack(d);
    var wasDirty = d.wasDirty;
    d.wasDirty = isDirty(d);
    if (wasDirty !== d.wasDirty) { renderTabs(); renderStatus(); if (d === currentDoc()) document.title = basename(d.path) + (d.wasDirty ? ' ●' : '') + ' - PAD edit'; }
    var lang = d.model.getLanguageId();
    if (lang === 'php') liveCheckPhp(d);
    if (lang === 'pad' && settings.checkTyping) liveCheckPad(d);
    if (settings.autosave === 'delay' && d.wasDirty) autosave(d);
  }

  var autosave = debounce(function (d) { if (isDirty(d) && E.docs[key(d.app, d.root, d.path)]) save(d); }, 1500);

  function renderTabs() {
    var bar = $('tabs');
    bar.textContent = '';
    E.tabs.forEach(function (k) {
      var d = E.docs[k];
      if (!d) return;
      var e = ext(d.path), badge = BADGES[e] || ['', 'b-txt'];
      var label = basename(d.path);
      var same = E.tabs.filter(function (o) { return E.docs[o] && basename(E.docs[o].path) === label; }).length > 1;
      var tab = el('div', { class: 'tab' + (k === E.active ? ' active' : '') + (isDirty(d) ? ' dirty' : '') + (d.changedOnDisk ? ' stale' : ''),
                            role: 'tab', 'aria-selected': k === E.active ? 'true' : 'false', tabindex: '0', draggable: 'true',
                            title: d.root === 'src' ? d.path + ' (read-only, from the debugger)' : d.app + ' - ' + (d.root === 'www' ? 'www/' : '') + d.path,
                            onclick: function () { activate(k); if (d.kind === 'text') E.editor.focus(); },
                            onauxclick: function (ev) { if (ev.button === 1) closeTab(k); },
                            onkeydown: function (ev) { if (ev.key === 'Enter') activate(k); },
                            oncontextmenu: function (ev) { ev.preventDefault(); tabMenu(ev, k); } }, [
        el('span', { class: 'badge ' + badge[1], text: badge[0] }),
        el('span', { class: 'tab-name', text: label }),
        d.root === 'src' ? el('span', { class: 'tab-dir', text: dbgShort(dirname(d.path)) }) :
        same || d.app !== E.app ? el('span', { class: 'tab-dir', text: (d.app !== E.app ? d.app + ':' : '') + (dirname(d.path) || '/') }) : null,
        el('button', { type: 'button', class: 'tab-close', 'aria-label': 'Close ' + label, title: 'Close (Alt+W)',
                       onclick: function (ev) { ev.stopPropagation(); closeTab(k); } }, [el('span', { class: 'dot' }), icon('close')])
      ]);
      tab.addEventListener('dragstart', function (ev) { ev.dataTransfer.setData('text/x-pad-tab', k); });
      tab.addEventListener('dragover', function (ev) { if (ev.dataTransfer.types.indexOf('text/x-pad-tab') >= 0) ev.preventDefault(); });
      tab.addEventListener('drop', function (ev) {
        var from = ev.dataTransfer.getData('text/x-pad-tab');
        if (!from || from === k) return;
        ev.preventDefault();
        E.tabs.splice(E.tabs.indexOf(from), 1);
        E.tabs.splice(E.tabs.indexOf(k), 0, from);
        renderTabs();
        persistTabs();
      });
      bar.appendChild(tab);
    });
    var act = bar.querySelector('.tab.active');
    if (act && act.scrollIntoView) act.scrollIntoView({ block: 'nearest', inline: 'nearest' });
  }

  function tabMenu(ev, k) {
    var d = E.docs[k];
    contextMenu(ev.clientX, ev.clientY, [
      { label: 'Close', icon: 'close', keys: 'Alt+W', run: function () { closeTab(k); } },
      { label: 'Close others', icon: 'close', run: function () { closeMany(E.tabs.filter(function (o) { return o !== k; })); } },
      { label: 'Close saved', icon: 'close', run: function () { closeMany(E.tabs.filter(function (o) { return !isDirty(E.docs[o]); })); } },
      { label: 'Close all', icon: 'close', run: function () { closeMany(E.tabs.slice()); } },
      '-',
      d.root === 'src' ? null : { label: 'Reveal in tree', icon: 'side', run: function () { if (d.app !== E.app) switchApp(d.app).then(function () { select(d.root, d.path, true); }); else select(d.root, d.path, true); } },
      { label: 'Copy path', icon: 'copy', run: function () { if (d.root === 'src') { if (navigator.clipboard) navigator.clipboard.writeText(d.path); toast('Copied ' + d.path); } else copyPath(d.app, d.root, d.path); } }
    ]);
  }

  function closeMany(keys) {
    return keys.reduce(function (p, k) { return p.then(function () { return closeTab(k); }); }, Promise.resolve());
  }

  function persistTabs() {
    store('tabs', { list: E.tabs.map(function (k) { var d = E.docs[k]; return d && d.root !== 'src' ? [d.app, d.root, d.path] : null; }).filter(Boolean),
                    active: E.active });
  }

  // ------------------------------------------------------------------------------------
  // Saving, and files changed by someone else
  // ------------------------------------------------------------------------------------

  function save(d, force) {
    d = d || currentDoc();
    if (!d || d.kind !== 'text') return Promise.resolve();
    if (!d.writable) { toast(basename(d.path) + ' is read-only', 'error'); return Promise.resolve(); }
    var version = d.model.getAlternativeVersionId();
    var text = d.model.getValue();
    d.saving = true;
    renderStatus();
    return api('save', { app: d.app, root: d.root, path: d.path, text: text, sha1: d.sha1, force: !!force }).then(function (r) {
      d.saving = false;
      if (r.conflict) { renderStatus(); return conflict(d, r.disk); }
      d.sha1 = r.sha1; d.mtime = r.mtime; d.size = r.size; d.savedVersion = version; d.wasDirty = isDirty(d); d.changedOnDisk = false;
      d.savedAt = Date.now();
      renderTabs();
      renderStatus();
      if (d === currentDoc()) document.title = basename(d.path) + (d.wasDirty ? ' ●' : '') + ' - PAD edit';
      afterSave(d);
      return d;
    }).catch(function (e) { d.saving = false; renderStatus(); toast('Saving ' + basename(d.path) + ': ' + e.message, 'error'); });
  }

  function afterSave(d) {
    var lang = d.model.getLanguageId();
    check(d, false);
    if (lang === 'php' || /(^|\/)_(lib|tags|functions|include|options|callbacks|data|lang|mail|content)\//.test(d.path)) {
      openDocs().forEach(function (o) { if (o.app === d.app && o.model && o.model.getLanguageId() === 'pad') loadFields(o); });
      loadAppLang(d.app);
    }
    if (d.app === E.app) refreshTree(true);
    if (E.previewOn) reloadPreview();
    if (E.panelTab === 'history') renderHistory();
    if (lang === 'pad' && d.app === E.app) {
      // a wrapper or snippet changed: the open pages that use it are checked again
      openDocs().forEach(function (o) { if (o !== d && o.app === d.app && o.model && o.model.getLanguageId() === 'pad' && E.tabs.indexOf(key(o.app, o.root, o.path)) >= 0) check(o, false); });
    }
  }

  function saveAll() {
    return openDocs().filter(isDirty).reduce(function (p, d) { return p.then(function () { return save(d); }); }, Promise.resolve());
  }

  function conflict(d, disk) {
    var mine = d.model.getValue();
    return diffView('Changed on disk: ' + basename(d.path),
      'Someone changed ' + d.path + ' since you opened it. Left: the file on disk now. Right: your version - you can still edit it here.',
      disk.text === null ? '' : disk.text, mine, langOf(d.root, d.path), [
        { label: 'Use the disk version', run: function () { d.model.setValue(disk.text || ''); d.sha1 = disk.sha1; d.savedVersion = d.model.getAlternativeVersionId(); d.wasDirty = false; renderTabs(); renderStatus(); } },
        { label: 'Save mine over it', primary: true, run: function (modified) { if (modified !== null) d.model.setValue(modified); d.sha1 = disk.sha1; return save(d, true); } }
      ], true);
  }

  // When the window gets the focus back: did a file that is open change on disk? A clean
  // one is read again; one with changes gets a mark, and its save will show the difference.
  function checkDisk() {
    var byApp = {};
    openDocs().forEach(function (d) { if (d.kind === 'text' && d.root !== 'src') (byApp[d.app] = byApp[d.app] || []).push({ root: d.root, path: d.path }); });
    Object.keys(byApp).forEach(function (app) {
      api('stat', { app: app, files: byApp[app] }).then(function (list) {
        list.forEach(function (s) {
          var d = E.docs[key(app, s.root, s.path)];
          if (!d || !s.exists || s.sha1 === d.sha1) return;
          if (!isDirty(d)) {
            api('open', { app: app, root: d.root, path: d.path }).then(function (info) {
              if (info.text === null || isDirty(d)) return;
              var view = d === currentDoc() ? E.editor.saveViewState() : null;
              d.model.setValue(info.text);
              d.sha1 = info.sha1; d.mtime = info.mtime; d.savedVersion = d.model.getAlternativeVersionId(); d.wasDirty = false;
              if (view) E.editor.restoreViewState(view);
              toast(basename(d.path) + ' was changed on disk and has been read again');
            });
          } else if (!d.changedOnDisk) {
            d.changedOnDisk = true;
            renderTabs();
            toast(basename(d.path) + ' was changed on disk - saving will show the difference', 'error');
          }
        });
      }).catch(function () {});
    });
  }

  // ------------------------------------------------------------------------------------
  // Checks and markers
  // ------------------------------------------------------------------------------------

  function setMarkers(d, owner, markers) {
    if (!d.model || !E.real) return;
    var S = E.monaco.MarkerSeverity;
    E.monaco.editor.setModelMarkers(d.model, owner, (markers || []).map(function (m) {
      return { startLineNumber: m.line, startColumn: m.column, endLineNumber: m.line, endColumn: m.endColumn,
               message: m.message, severity: m.severity === 'warning' ? S.Warning : S.Error, source: m.source === 'php' ? 'PHP' : 'PAD' };
    }));
  }

  function check(d, withText) {
    if (!d || d.kind !== 'text' || !d.model || d.root === 'src') return Promise.resolve();
    var lang = d.model.getLanguageId();
    if (lang !== 'pad' && lang !== 'php') return Promise.resolve();
    if (lang === 'pad' && d.root !== 'app') return Promise.resolve();
    var body = { app: d.app, root: d.root, path: d.path };
    if (withText || lang === 'php') body.text = d.model.getValue();
    d.checking = true;
    renderStatus();
    return api('check', body).then(function (r) {
      d.checking = false;
      d.checkedPage = r.page;
      setMarkers(d, lang, r.markers);
      renderStatus();
      return r;
    }).catch(function (e) { d.checking = false; renderStatus(); toast('Check: ' + e.message, 'error'); });
  }

  var liveCheckPhp = debounce(function (d) { check(d, true); }, 700);
  var liveCheckPad = debounce(function (d) { check(d, true); }, 1500);

  function loadFields(d) {
    if (d.root !== 'app') return;
    api('fields', { app: d.app, path: d.path }).then(function (list) { E.fields[d.app + '|' + d.path] = list; }).catch(function () {});
  }

  function loadAppLang(app) {
    return api('language', { app: app }).then(function (r) { E.appLang[app] = r.app; }).catch(function (e) { toast('Language: ' + e.message, 'error'); });
  }

  function renderProblems() {
    var list = $('problemsList');
    if (!list) return;
    list.textContent = '';
    var total = 0;
    var markers = E.real ? E.monaco.editor.getModelMarkers({}) : [];
    var byUri = {};
    markers.forEach(function (m) { (byUri[m.resource.toString()] = byUri[m.resource.toString()] || []).push(m); });
    Object.keys(byUri).forEach(function (uri) {
      var d = docByUri(uri);
      if (!d || E.tabs.indexOf(key(d.app, d.root, d.path)) < 0) return;
      var ms = byUri[uri].sort(function (a, b) { return a.startLineNumber - b.startLineNumber; });
      total += ms.length;
      list.appendChild(el('div', { class: 'group-head' }, [el('span', { class: 'badge ' + (BADGES[ext(d.path)] || ['', 'b-txt'])[1], text: (BADGES[ext(d.path)] || ['TXT'])[0] }),
                                                          el('strong', { text: basename(d.path) }), el('span', { class: 'muted', text: ' ' + d.app + '/' + dirname(d.path) })]));
      ms.forEach(function (m) {
        list.appendChild(el('button', { type: 'button', class: 'result', onclick: function () {
          openFile(d.app, d.root, d.path, { line: m.startLineNumber, column: m.startColumn });
        } }, [icon(m.severity >= 8 ? 'warning' : 'info', m.severity >= 8 ? 'sev-error' : 'sev-warn'),
              el('span', { class: 'msg', text: m.message }), el('span', { class: 'muted', text: ' ' + (m.source || '') + ' [' + m.startLineNumber + ':' + m.startColumn + ']' })]));
      });
    });
    if (!total) list.appendChild(el('p', { class: 'empty', text: 'No problems in the open files. PAD pages are checked when they are opened and saved (F7 checks now); PHP files while you type.' }));
    $('problemsCount').textContent = total ? String(total) : '';
    E.problemTotal = total;
    renderStatus();
  }

  // ------------------------------------------------------------------------------------
  // The tree
  // ------------------------------------------------------------------------------------

  function refreshTree(quiet) {
    if (!E.app) return Promise.resolve();
    var app = E.app;
    return api('tree', { app: app, git: true }).then(function (r) {
      if (app !== E.app) return;
      E.files = r.files;
      E.fileIndex = {};
      r.files.forEach(function (f) { E.fileIndex[f.root + '|' + f.path] = f; });
      E.git = r.git;
      E.branch = r.branch || '';
      renderTree();
      renderStatus();
    }).catch(function (e) { if (!quiet) toast('Files: ' + e.message, 'error'); });
  }

  function buildTree() {
    var roots = [], nodes = {};
    (E.apps.filter(function (a) { return a.name === E.app; })[0] || { roots: ['app'] }).roots.forEach(function (root) {
      var n = { root: root, path: '', dir: true, name: (root === 'www' ? 'www/' : 'apps/') + E.app, children: [], top: true };
      nodes[root + '|'] = n;
      roots.push(n);
    });
    E.files.forEach(function (f) {
      var n = { root: f.root, path: f.path, dir: f.dir, name: basename(f.path), size: f.size, mtime: f.mtime, children: [] };
      nodes[f.root + '|' + f.path] = n;
      var parent = nodes[f.root + '|' + dirname(f.path)];
      if (parent) parent.children.push(n);
    });
    return roots;
  }

  function expandedSet() { return (E.expanded[E.app] = E.expanded[E.app] || store('expanded.' + E.app) || { 'app|': true }); }

  function renderTree() {
    var box = $('tree');
    box.textContent = '';
    var filter = $('treeFilter').value.trim().toLowerCase();
    if (filter) {
      var hits = fuzzy(E.files.filter(function (f) { return !f.dir; }), filter, function (f) { return (f.root === 'www' ? 'www/' : '') + f.path; }).slice(0, 300);
      hits.forEach(function (f) { box.appendChild(row({ root: f.root, path: f.path, dir: false, name: basename(f.path) }, 0, (f.root === 'www' ? 'www/' : '') + dirname(f.path))); });
      if (!hits.length) box.appendChild(el('p', { class: 'empty', text: 'No file matches.' }));
      return;
    }
    var exp = expandedSet();
    var walk = function (n, depth, parent) {
      var r = row(n, depth);
      parent.appendChild(r);
      if (n.dir && exp[n.root + '|' + n.path]) {
        var group = el('div', { role: 'group' });
        n.children.forEach(function (c) { walk(c, depth + 1, group); });
        if (!n.children.length) group.appendChild(el('div', { class: 'tree-empty', style: { paddingLeft: (depth + 1) * 14 + 22 + 'px' }, text: 'empty' }));
        parent.appendChild(group);
      }
    };
    buildTree().forEach(function (n) { walk(n, 0, box); });
  }

  function gitMark(root, path, dir) {
    if (!E.git || !E.git[root]) return '';
    var g = E.git[root];
    if (!dir) return g[path] || '';
    var pre = path ? path + '/' : '';
    for (var p in g) if (p.indexOf(pre) === 0) return '•';
    return '';
  }

  function row(n, depth, note) {
    var k = n.root + '|' + n.path;
    var exp = expandedSet();
    var open = n.dir && exp[k];
    var sel = E.selected && E.selected.root === n.root && E.selected.path === n.path;
    var mark = n.top ? '' : gitMark(n.root, n.path, n.dir);
    var e = ext(n.name), badge = BADGES[e];
    var hint = DIR_HINTS[n.name] || DIR_HINTS[n.name.replace(/\.(php|pad|html)$/, '')] || '';
    var pair = !n.dir && n.root === 'app' && /\.(pad|html|php)$/.test(n.name) && !/^_/.test(n.name) && pairOf(n.path);
    var openDoc = E.docs[key(E.app, n.root, n.path)];
    var r = el('div', {
      class: 'row' + (n.dir ? ' dir' : ' file') + (sel ? ' selected' : '') + (n.top ? ' top' : '') + (n.name.charAt(0) === '_' ? ' special' : '') +
             (openDoc && E.tabs.indexOf(key(E.app, n.root, n.path)) >= 0 ? ' opened' : '') + (mark ? ' git-' + (mark === '?' ? 'new' : mark === '•' ? 'dirchange' : mark.toLowerCase()) : ''),
      role: 'treeitem', tabindex: sel ? '0' : '-1', 'aria-expanded': n.dir ? (open ? 'true' : 'false') : null, 'aria-selected': sel ? 'true' : 'false',
      'data-key': k, draggable: n.top ? 'false' : 'true', title: hint ? n.name + ' - ' + hint : (n.size !== undefined ? n.name + ' - ' + size(n.size) + ', ' + ago(n.mtime) : n.name),
      style: { paddingLeft: depth * 14 + 6 + 'px' }
    }, [
      n.dir ? icon('chevron', 'chev' + (open ? ' open' : '')) : el('span', { class: 'chev-space' }),
      n.dir ? el('span', { class: 'folder' + (open ? ' open' : '') + (n.top ? ' top' : '') }) : el('span', { class: 'badge ' + (badge ? badge[1] : 'b-txt'), text: badge ? badge[0] : (e || '·').slice(0, 3).toUpperCase() }),
      el('span', { class: 'name', text: n.name }),
      note ? el('span', { class: 'note', text: note }) : null,
      pair ? el('span', { class: 'pair', title: 'Paired with ' + basename(pair) }, [icon('pair')]) : null,
      mark ? el('span', { class: 'git', text: mark === '?' ? 'U' : mark, title: { M: 'Changed', A: 'Added', '?': 'Not in git yet', D: 'Deleted', R: 'Renamed', '•': 'Changes inside' }[mark] }) : null
    ]);
    r.addEventListener('click', function () {
      select(n.root, n.path, false);
      if (n.dir) toggle(n);
      else openFile(E.app, n.root, n.path);
    });
    r.addEventListener('keydown', function (ev) { treeKey(ev, n); });
    r.addEventListener('contextmenu', function (ev) { ev.preventDefault(); select(n.root, n.path, false); nodeMenu(ev.clientX, ev.clientY, n); });
    r.addEventListener('dragstart', function (ev) { ev.dataTransfer.setData('text/x-pad-file', JSON.stringify({ root: n.root, path: n.path })); ev.dataTransfer.effectAllowed = 'move'; });
    r.addEventListener('dragover', function (ev) {
      var types = ev.dataTransfer.types;
      if (!n.dir) return;
      if (Array.prototype.indexOf.call(types, 'text/x-pad-file') >= 0 || Array.prototype.indexOf.call(types, 'Files') >= 0) { ev.preventDefault(); r.classList.add('drop'); }
    });
    r.addEventListener('dragleave', function () { r.classList.remove('drop'); });
    r.addEventListener('drop', function (ev) {
      r.classList.remove('drop');
      if (!n.dir) return;
      ev.preventDefault();
      ev.stopPropagation();
      if (ev.dataTransfer.files && ev.dataTransfer.files.length) { upload(n.path, n.root, ev.dataTransfer.files); return; }
      var from = JSON.parse(ev.dataTransfer.getData('text/x-pad-file') || 'null');
      if (!from || (from.root === n.root && dirname(from.path) === n.path)) return;
      move(from.root, from.path, n.root, join(n.path, basename(from.path)));
    });
    return r;
  }

  function pairOf(path) {
    var m = path.match(/^(.*)\.(pad|html|php)$/);
    if (!m) return null;
    var others = m[2] === 'php' ? ['.pad', '.html'] : ['.php'];
    for (var i = 0; i < others.length; i++) if (E.fileIndex['app|' + m[1] + others[i]]) return m[1] + others[i];
    return null;
  }

  function toggle(n, force) {
    var exp = expandedSet(), k = n.root + '|' + n.path;
    var open = force === undefined ? !exp[k] : force;
    if (open) exp[k] = true; else delete exp[k];
    store('expanded.' + E.app, exp);
    renderTree();
    focusRow(n.root, n.path);
  }

  function select(root, path, reveal, quiet) {
    E.selected = { root: root, path: path, dir: !!(E.fileIndex[root + '|' + path] || { dir: path === '' }).dir };
    if (reveal || quiet) {
      var exp = expandedSet(), parts = path.split('/'), changed = false;
      if (!exp[root + '|']) { exp[root + '|'] = true; changed = true; }
      for (var i = 1; i < parts.length; i++) { var p = root + '|' + parts.slice(0, i).join('/'); if (!exp[p]) { exp[p] = true; changed = true; } }
      if (changed && reveal) store('expanded.' + E.app, exp);
      if (changed && !reveal) { renderTree(); }
    }
    var rows = $('tree').querySelectorAll('.row');
    for (var j = 0; j < rows.length; j++) {
      var on = rows[j].getAttribute('data-key') === root + '|' + path;
      rows[j].classList.toggle('selected', on);
      rows[j].setAttribute('aria-selected', on ? 'true' : 'false');
      rows[j].setAttribute('tabindex', on ? '0' : '-1');
      if (on && (reveal || quiet)) rows[j].scrollIntoView({ block: 'nearest' });
    }
    if (reveal) { renderTree(); focusRow(root, path); }
  }

  function focusRow(root, path) {
    var r = $('tree').querySelector('.row[data-key="' + CSS.escape(root + '|' + path) + '"]');
    if (r) { r.focus(); r.scrollIntoView({ block: 'nearest' }); }
  }

  function treeKey(ev, n) {
    var rows = Array.prototype.slice.call($('tree').querySelectorAll('.row'));
    var i = rows.indexOf(ev.currentTarget);
    var go = function (r) { if (!r) return; var k = r.getAttribute('data-key').split('|'); select(k[0], k.slice(1).join('|'), false); r.focus(); };
    if (ev.key === 'ArrowDown') { ev.preventDefault(); go(rows[i + 1]); }
    else if (ev.key === 'ArrowUp') { ev.preventDefault(); go(rows[i - 1]); }
    else if (ev.key === 'ArrowRight' && n.dir) { ev.preventDefault(); toggle(n, true); }
    else if (ev.key === 'ArrowLeft') { ev.preventDefault(); if (n.dir && expandedSet()[n.root + '|' + n.path]) toggle(n, false); else if (!n.top) { select(n.root, dirname(n.path), false); focusRow(n.root, dirname(n.path)); } }
    else if (ev.key === 'Enter') { ev.preventDefault(); if (n.dir) toggle(n); else openFile(E.app, n.root, n.path); }
    else if (ev.key === 'F2' && !n.top) { ev.preventDefault(); renameNode(n); }
    else if ((ev.key === 'Delete' || (ev.key === 'Backspace' && ev.metaKey)) && !n.top) { ev.preventDefault(); deleteNode(n); }
    else if (ev.key === 'ContextMenu' || (ev.shiftKey && ev.key === 'F10')) { ev.preventDefault(); var b = ev.currentTarget.getBoundingClientRect(); nodeMenu(b.left + 30, b.bottom, n); }
  }

  // The directory a new file goes into: the selected directory, or the one the selected
  // file stands in.
  function targetDir() {
    var s = E.selected;
    if (!s) return { root: 'app', dir: '' };
    var f = E.fileIndex[s.root + '|' + s.path];
    var isDir = s.path === '' || (f && f.dir);
    return { root: s.root, dir: isDir ? s.path : dirname(s.path) };
  }

  function nodeMenu(x, y, n) {
    var file = !n.dir;
    var page = file && n.root === 'app' && pageOf(n.path);
    contextMenu(x, y, [
      file ? { label: 'Open', icon: 'file', run: function () { openFile(E.app, n.root, n.path); } } : null,
      file && pairOf(n.path) ? { label: 'Open ' + basename(pairOf(n.path)), icon: 'pair', keys: 'Alt+O', run: function () { openFile(E.app, 'app', pairOf(n.path)); } } : null,
      page !== null && page !== undefined && page !== false ? { label: 'Preview ?' + (page || 'index'), icon: 'eye', run: function () { showPreview(page); } } : null,
      file ? '-' : null,
      { label: 'New file…', icon: 'filePlus', keys: 'Alt+N', run: function () { newFile(); } },
      { label: 'New folder…', icon: 'folderPlus', run: function () { newFolder(); } },
      n.root === 'app' ? { label: 'New from template…', icon: 'template', run: function () { newFromTemplate(); } } : null,
      n.dir ? { label: 'Upload files…', icon: 'upload', run: function () { pickUpload(); } } : null,
      '-',
      !n.top ? { label: 'Duplicate', icon: 'copy', run: function () { duplicate(n); } } : null,
      !n.top ? { label: 'Rename…', icon: 'rename', keys: 'F2', run: function () { renameNode(n); } } : null,
      !n.top ? { label: 'Move to…', icon: 'chevron', run: function () { moveNode(n); } } : null,
      !n.top ? { label: 'Delete', icon: 'trash', keys: 'Del', danger: true, run: function () { deleteNode(n); } } : null,
      '-',
      file ? { label: 'Download', icon: 'download', run: function () { ensureDoc(E.app, n.root, n.path).then(download); } } : null,
      { label: 'Copy path', icon: 'copy', run: function () { copyPath(E.app, n.root, n.path); } },
      file && E.git ? { label: 'Compare with HEAD', icon: 'git', run: function () { ensureDoc(E.app, n.root, n.path).then(compareHead); } } : null,
      file ? { label: 'History', icon: 'history', run: function () { openFile(E.app, n.root, n.path).then(function () { showPanel('history'); }); } } : null,
      n.dir ? { label: 'Collapse all', icon: 'collapse', run: function () { collapseAll(); } } : null
    ]);
  }

  function collapseAll() {
    E.expanded[E.app] = { 'app|': true, 'www|': true };
    store('expanded.' + E.app, E.expanded[E.app]);
    renderTree();
  }

  function copyPath(app, root, path) {
    var full = (root === 'www' ? 'www/' : 'apps/') + app + (path ? '/' + path : '');
    if (navigator.clipboard) navigator.clipboard.writeText(full).then(function () { toast('Copied ' + full); }, function () { toast(full); });
    else toast(full);
  }

  function download(d) {
    if (!d) return;
    var blob = d.kind === 'text' ? new Blob([d.model.getValue()], { type: 'text/plain' })
                                 : new Blob([Uint8Array.from(atob(d.info.base64 || ''), function (c) { return c.charCodeAt(0); })]);
    var a = el('a', { href: URL.createObjectURL(blob), download: basename(d.path) });
    document.body.appendChild(a);
    a.click();
    setTimeout(function () { URL.revokeObjectURL(a.href); a.remove(); }, 1000);
  }

  // ------------------------------------------------------------------------------------
  // File management
  // ------------------------------------------------------------------------------------

  function newFile() {
    var t = targetDir();
    return ask('New file', 'Name - in ' + ((t.root === 'www' ? 'www/' : 'apps/') + E.app + '/' + t.dir).replace(/\/$/, ''), '', {
      ok: 'Make', note: 'A name with a / makes the directories too: admin/users.pad. A name without an extension makes a page pair - name.php and name.pad.'
    }).then(function (name) {
      if (!name) return;
      var kind = /\.[^\/]+$/.test(name) ? 'file' : (t.root === 'app' ? 'page' : 'file');
      return api('create', { app: E.app, root: t.root, dir: t.dir, name: name, kind: kind }).then(function (r) {
        return refreshTree().then(function () {
          var last = r.made[r.made.length - 1];
          select(t.root, last, true);
          return openFile(E.app, t.root, r.open || last);
        });
      }).catch(function (e) { toast(e.message, 'error'); });
    });
  }

  function newFolder() {
    var t = targetDir();
    return ask('New folder', 'Name - in ' + ((t.root === 'www' ? 'www/' : 'apps/') + E.app + '/' + t.dir).replace(/\/$/, ''), '', { ok: 'Make' }).then(function (name) {
      if (!name) return;
      return api('create', { app: E.app, root: t.root, dir: t.dir, name: name, kind: 'dir' }).then(function (r) {
        expandedSet()[t.root + '|' + r.made[0]] = true;
        return refreshTree().then(function () { select(t.root, r.made[0], true); });
      }).catch(function (e) { toast(e.message, 'error'); });
    });
  }

  var TEMPLATES = [
    { kind: 'page', label: 'Page', hint: 'name.php + name.pad - the data and the template of ?name', ask: 'Page name', def: 'about' },
    { kind: 'tag', label: 'Tag (PHP)', hint: '_tags/name.php - {name} computes its content', ask: 'Tag name', def: 'badge' },
    { kind: 'component', label: 'Tag (component)', hint: '_tags/name.pad - {name} with {parms} and slots', ask: 'Tag name', def: 'card' },
    { kind: 'function', label: 'Pipe function', hint: '_functions/name.php - {$x | name}', ask: 'Function name', def: 'money' },
    { kind: 'include', label: 'Snippet', hint: '_include/name.pad - {name}', ask: 'Snippet name', def: 'footer' },
    { kind: 'callback', label: 'Callback', hint: "_callbacks/name.php - callback='name'", ask: 'Callback name', def: 'total' },
    { kind: 'option', label: 'Option', hint: '_options/name.php - {tag name}', ask: 'Option name', def: 'shout' },
    { kind: 'event', label: 'Event hook', hint: '_events/error.php, sql.php, curl.php or output.php', ask: 'Event (error, sql, curl, output)', def: 'error' },
    { kind: 'guard', label: 'Guard', hint: '_guard.php - access to every page below', none: true },
    { kind: 'wrapper', label: 'Wrapper', hint: '_inits.pad + _exits.pad - around every page below', none: true },
    { kind: 'config', label: 'Configuration', hint: '_config/config.php', none: true },
    { kind: 'data', label: 'Data file', hint: '_data/name.json - {name}', ask: 'Data name', def: 'colors' },
    { kind: 'query', label: 'Named query', hint: '_data/name.sql - {name}', ask: 'Query name', def: 'customers' },
    { kind: 'test', label: 'Test', hint: '_tests/name.pad + name.txt - pad test', ask: 'Test name', def: 'home' },
    { kind: 'mail', label: 'Mail template', hint: "_mail/name.pad - {mail template='name'}", ask: 'Template name', def: 'welcome' },
    { kind: 'lang', label: 'Translation catalog', hint: "_lang/locale.json - {trans 'key'}", ask: 'Locale', def: 'en' },
    { kind: 'content', label: 'Markdown entry', hint: "_content/collection/name.md - {collection 'collection'}", ask: 'collection/name', def: 'blog/first-post' }
  ];

  function newFromTemplate() {
    var t = targetDir();
    if (t.root !== 'app') t = { root: 'app', dir: '' };
    var context = t.dir.split('/').filter(function (p, i, all) { return all.slice(0, i + 1).every(function (q) { return q.charAt(0) !== '_'; }); }).join('/');
    var list = el('div', { class: 'choices' });
    var close = modal('New from template - in apps/' + E.app + (context ? '/' + context : ''), list, { wide: true });
    TEMPLATES.forEach(function (tp) {
      list.appendChild(el('button', { type: 'button', class: 'choice', onclick: function () {
        close();
        var go = function (name) {
          if (name === null) return;
          api('create', { app: E.app, root: 'app', dir: context, name: name || '', kind: tp.kind, template: true }).then(function (r) {
            return refreshTree().then(function () {
              r.made.forEach(function (p) { var parts = p.split('/'); for (var i = 1; i < parts.length; i++) expandedSet()['app|' + parts.slice(0, i).join('/')] = true; });
              renderTree();
              return openFile(E.app, 'app', r.open || r.made[r.made.length - 1]);
            });
          }).catch(function (e) { toast(e.message, 'error'); });
        };
        if (tp.none) go(''); else ask(tp.label, tp.ask, tp.def, { ok: 'Make', note: tp.hint }).then(go);
      } }, [el('strong', { text: tp.label }), el('span', { text: tp.hint })]));
    });
  }

  function duplicate(n) {
    var name = basename(n.path), m = name.match(/^(.*?)(\.[^.]*)?$/);
    var copyName = n.dir ? name + '-copy' : m[1] + '-copy' + (m[2] || '');
    return ask('Duplicate', 'The copy\'s path', join(dirname(n.path), copyName), { ok: 'Duplicate', selectName: true }).then(function (to) {
      if (!to) return;
      return api('copy', { app: E.app, root: n.root, path: n.path, to: to }).then(function () {
        return refreshTree().then(function () { select(n.root, to, true); if (!n.dir) openFile(E.app, n.root, to); });
      }).catch(function (e) { toast(e.message, 'error'); });
    });
  }

  function renameNode(n) {
    return ask('Rename', 'New name', basename(n.path), { ok: 'Rename', selectName: true }).then(function (name) {
      if (!name || name === basename(n.path)) return;
      var to = name.indexOf('/') >= 0 ? name : join(dirname(n.path), name);
      return move(n.root, n.path, n.root, to);
    });
  }

  function moveNode(n) {
    return ask('Move', 'New path, from the root of ' + (n.root === 'www' ? 'www/' : 'apps/') + E.app, n.path, { ok: 'Move' }).then(function (to) {
      if (!to || to === n.path) return;
      return move(n.root, n.path, n.root, to);
    });
  }

  function move(root, path, toRoot, to) {
    var moving = openDocs().filter(function (d) { return d.app === E.app && d.root === root && (d.path === path || d.path.indexOf(path + '/') === 0); });
    var dirty = moving.filter(isDirty);
    var go = function () {
      return api('rename', { app: E.app, root: root, path: path, toRoot: toRoot, to: to }).then(function () {
        moving.forEach(function (d) {
          var oldKey = key(d.app, d.root, d.path), oldAbs = dbgAbs(d);
          d.root = toRoot;
          d.path = to + d.path.slice(path.length);
          var newKey = key(d.app, d.root, d.path);
          delete E.docs[oldKey];
          E.docs[newKey] = d;
          var i = E.tabs.indexOf(oldKey);
          if (i >= 0) E.tabs[i] = newKey;
          if (E.active === oldKey) E.active = newKey;
          var lang = langOf(d.root, d.path);
          if (d.model && d.model.getLanguageId() !== lang) E.monaco.editor.setModelLanguage(d.model, lang);
          if (D.bps[oldAbs]) { D.bps[dbgAbs(d)] = D.bps[oldAbs]; delete D.bps[oldAbs]; dbgSaveBps(); }
        });
        toast('Moved to ' + to, 'ok');
        renderTabs();
        persistTabs();
        return refreshTree().then(function () { select(toRoot, to, true); });
      }).catch(function (e) { toast(e.message, 'error'); });
    };
    if (dirty.length) return confirmBox('Unsaved changes', 'Save ' + dirty.map(function (d) { return basename(d.path); }).join(', ') + ' first?', 'Save and move').then(function (yes) {
      if (!yes) return;
      return Promise.all(dirty.map(function (d) { return save(d); })).then(go);
    });
    return go();
  }

  function deleteNode(n) {
    var what = (n.dir ? 'the folder ' : '') + n.path + (n.dir ? ' and everything in it' : '');
    return confirmBox('Delete', 'Move ' + what + ' to the trash? It can be put back from the trash.', 'Move to trash', true).then(function (yes) {
      if (!yes) return;
      return api('delete', { app: E.app, root: n.root, path: n.path }).then(function () {
        var gone = openDocs().filter(function (d) { return d.app === E.app && d.root === n.root && (d.path === n.path || d.path.indexOf(n.path + '/') === 0); });
        gone.forEach(function (d) { closeTab(key(d.app, d.root, d.path), true); });
        toast('Moved to the trash: ' + basename(n.path), 'ok');
        select(n.root, dirname(n.path), false);
        return refreshTree();
      }).catch(function (e) { toast(e.message, 'error'); });
    });
  }

  function pickUpload() {
    var t = targetDir();
    var input = $('uploadInput');
    input.value = '';
    input.onchange = function () { if (input.files.length) upload(t.dir, t.root, input.files); };
    input.click();
  }

  // ------------------------------------------------------------------------------------
  // Applications
  // ------------------------------------------------------------------------------------

  function loadApps() {
    return api('apps', {}).then(function (r) {
      E.apps = r.apps;
      E.branch = r.branch || '';
      return r;
    });
  }

  function switchApp(app) {
    if (!app || !E.apps.some(function (a) { return a.name === app; })) app = E.apps.some(function (a) { return a.name === 'demo'; }) ? 'demo' : (E.apps[0] || {}).name;
    if (T.ready && !T.job && T.cwd === T.root && app !== E.app) {
      api('terminal', { op: 'where', app: app }).then(function (r) { T.cwd = T.root = r.cwd; termPrompt(); }).catch(function () {});
    }
    E.app = app;
    store('app', app);
    $('appName').textContent = app;
    E.selected = null;
    $('treeFilter').value = '';
    renderTabs();
    history.replaceState(null, '', '?index&app=' + encodeURIComponent(app));
    return Promise.all([refreshTree(), E.appLang[app] ? Promise.resolve() : loadAppLang(app)]).then(function () {
      renderWelcome();
      dbgRender();
      if (E.previewOn && !currentDoc()) showPreview(E.previewPage[app] || '');
    });
  }

  function appPicker() {
    var input = el('input', { type: 'search', placeholder: 'Filter applications', autofocus: true, 'aria-label': 'Filter applications' });
    var list = el('div', { class: 'pick-list', role: 'listbox' });
    var cursor = 0, shown = [];
    var render = function () {
      var q = input.value.trim().toLowerCase();
      shown = E.apps.filter(function (a) { return !q || a.name.toLowerCase().indexOf(q) >= 0 || (a.about || '').toLowerCase().indexOf(q) >= 0; });
      cursor = Math.min(cursor, Math.max(0, shown.length - 1));
      list.textContent = '';
      shown.forEach(function (a, i) {
        list.appendChild(el('button', { type: 'button', class: 'pick' + (i === cursor ? ' cur' : '') + (a.name === E.app ? ' current' : ''), role: 'option',
                                        onclick: function () { close(); switchApp(a.name); } },
                            [icon('apps'), el('strong', { text: a.name }), el('span', { class: 'muted', text: a.about || '' })]));
      });
    };
    input.addEventListener('input', function () { cursor = 0; render(); });
    input.addEventListener('keydown', function (e) {
      if (e.key === 'ArrowDown') { e.preventDefault(); cursor = Math.min(cursor + 1, shown.length - 1); render(); }
      if (e.key === 'ArrowUp') { e.preventDefault(); cursor = Math.max(cursor - 1, 0); render(); }
      if (e.key === 'Enter' && shown[cursor]) { e.preventDefault(); close(); switchApp(shown[cursor].name); }
    });
    var body = el('div', { class: 'picker' }, [input, list,
      el('div', { class: 'form-buttons' }, [button('New application…', 'filePlus', function () { close(); newApp(); })])]);
    var close = modal('Applications', body, { wide: true });
    render();
  }

  function newApp() {
    return ask('New application', 'Name - letters, digits, _ and -', '', {
      ok: 'Make', note: 'Makes apps/<name>/ with a first page and www/<name>/index.php, as pad new does. The regression suite will list its pages as new until they have answers.'
    }).then(function (name) {
      if (!name) return;
      return api('newapp', { name: name }).then(function (r) {
        toast('Made the application ' + name, 'ok');
        E.apps = r.apps;
        return switchApp(name);
      }).catch(function (e) { toast(e.message, 'error'); });
    });
  }

  // ------------------------------------------------------------------------------------
  // Quick open and the command list
  // ------------------------------------------------------------------------------------

  function fuzzy(items, q, text) {
    q = q.toLowerCase();
    var scored = [];
    items.forEach(function (it) {
      var s = text(it).toLowerCase(), score = 0, at = -1, run = 0;
      for (var i = 0; i < q.length; i++) {
        var j = s.indexOf(q.charAt(i), at + 1);
        if (j < 0) return;
        run = j === at + 1 ? run + 1 : 0;
        score += 1 + run * 2 + (j === 0 || '/_.-'.indexOf(s.charAt(j - 1)) >= 0 ? 3 : 0);
        at = j;
      }
      var base = s.split('/').pop();
      if (base.indexOf(q) >= 0) score += 20;
      if (base.indexOf(q) === 0) score += 10;
      scored.push({ it: it, score: score - s.length * 0.01 });
    });
    return scored.sort(function (a, b) { return b.score - a.score; }).map(function (x) { return x.it; });
  }

  function quickOpen(prefix) {
    var input = el('input', { type: 'text', value: prefix || '', spellcheck: 'false', autofocus: true, 'aria-label': 'Go to file',
                              placeholder: 'Go to file - > for commands, : for a line, @ for a tag in this file' });
    var list = el('div', { class: 'pick-list', role: 'listbox' });
    var cursor = 0, shown = [];
    var render = function () {
      var q = input.value;
      list.textContent = '';
      shown = [];
      if (q.charAt(0) === '>') {
        var c = q.slice(1).trim();
        shown = (c ? fuzzy(COMMANDS, c, function (x) { return x.label; }) : COMMANDS).map(function (x) {
          return { label: x.label, note: x.keys || '', icon: x.icon || 'play', run: x.run };
        });
      } else if (q.charAt(0) === ':') {
        var n = parseInt(q.slice(1), 10);
        if (n && currentDoc()) shown = [{ label: 'Go to line ' + n, icon: 'chevron', run: function () { var d = currentDoc(); openFile(d.app, d.root, d.path, { line: n, column: 1 }); } }];
      } else if (q.charAt(0) === '@') {
        var d = currentDoc();
        if (d && d.model && E.base) {
          var pairs = window.PadMode.scanPairs(d.model.getValue(), E.base).pairs.sort(function (a, b) { return a.open.index - b.open.index; });
          var t = q.slice(1).trim();
          var items = pairs.map(function (p) { var pos = d.model.getPositionAt(p.open.index); return { label: p.open.head, note: 'line ' + pos.lineNumber, icon: 'template', run: function () { openFile(d.app, d.root, d.path, { line: pos.lineNumber, column: pos.column }); } }; });
          shown = t ? fuzzy(items, t, function (x) { return x.label; }) : items;
        }
      } else {
        var files = E.files.filter(function (f) { return !f.dir; });
        var hits = q.trim() ? fuzzy(files, q.trim(), function (f) { return (f.root === 'www' ? 'www/' : '') + f.path; }) : recent();
        shown = hits.slice(0, 60).map(function (f) {
          return { label: basename(f.path), note: (f.root === 'www' ? 'www/' : '') + dirname(f.path), badge: BADGES[ext(f.path)], run: function () { openFile(f.app || E.app, f.root, f.path); } };
        });
      }
      cursor = Math.min(cursor, Math.max(0, shown.length - 1));
      shown.forEach(function (s, i) {
        list.appendChild(el('button', { type: 'button', class: 'pick' + (i === cursor ? ' cur' : ''), role: 'option',
                                        onclick: function () { close(); s.run(); } },
                            [s.badge ? el('span', { class: 'badge ' + s.badge[1], text: s.badge[0] }) : icon(s.icon || 'file'),
                             el('strong', { text: s.label }), el('span', { class: 'muted', text: s.note || '' })]));
      });
      if (!shown.length) list.appendChild(el('p', { class: 'empty', text: 'Nothing found.' }));
      var cur = list.querySelector('.cur');
      if (cur) cur.scrollIntoView({ block: 'nearest' });
    };
    input.addEventListener('input', function () { cursor = 0; render(); });
    input.addEventListener('keydown', function (e) {
      if (e.key === 'ArrowDown') { e.preventDefault(); cursor = Math.min(cursor + 1, shown.length - 1); render(); }
      if (e.key === 'ArrowUp') { e.preventDefault(); cursor = Math.max(cursor - 1, 0); render(); }
      if (e.key === 'Enter' && shown[cursor]) { e.preventDefault(); close(); shown[cursor].run(); }
    });
    var close = modal(prefix === '>' ? 'Commands' : 'Go to file', el('div', { class: 'picker' }, [input, list]), { wide: true });
    render();
    setTimeout(function () { input.focus(); input.setSelectionRange(input.value.length, input.value.length); }, 40);
  }

  function recent() {
    return E.tabs.map(function (k) { var d = E.docs[k]; return d && { app: d.app, root: d.root, path: d.path }; }).filter(Boolean).reverse();
  }

  // ------------------------------------------------------------------------------------
  // The bottom panel: problems, search, history
  // ------------------------------------------------------------------------------------

  function showPanel(name) {
    E.panelTab = name;
    settings.panelOpen = true;
    saveSettings();
    applySettings();
    ['problems', 'search', 'history', 'terminal', 'debug'].forEach(function (p) {
      $('panel-' + p).hidden = p !== name;
      var t = $('ptab-' + p);
      t.classList.toggle('active', p === name);
      t.setAttribute('aria-selected', p === name ? 'true' : 'false');
    });
    $('termState').hidden = name !== 'terminal';
    $('termStop').hidden = name !== 'terminal' || !T.job;
    if (name === 'history') renderHistory();
    if (name === 'search') setTimeout(function () { $('searchInput').focus(); $('searchInput').select(); }, 30);
    if (name === 'terminal') termStart().then(function () { $('termInput').focus(); termScroll(); });
    if (name === 'debug') dbgBuild();
  }

  function runSearch(ev) {
    if (ev) ev.preventDefault();
    var q = $('searchInput').value;
    if (!q) return;
    var out = $('searchResults');
    out.textContent = '';
    out.appendChild(el('p', { class: 'empty', text: 'Searching…' }));
    api('search', { app: E.app, query: q, regex: $('searchRegex').checked, case: $('searchCase').checked, word: $('searchWord').checked }).then(function (r) {
      out.textContent = '';
      var groups = {};
      r.hits.forEach(function (h) { (groups[h.root + '|' + h.path] = groups[h.root + '|' + h.path] || []).push(h); });
      out.appendChild(el('p', { class: 'summary', text: r.hits.length + ' result' + (r.hits.length === 1 ? '' : 's') + ' in ' + Object.keys(groups).length + ' file' + (Object.keys(groups).length === 1 ? '' : 's') + (r.more ? ' - more than shown' : '') }));
      Object.keys(groups).forEach(function (g) {
        var hits = groups[g], f = hits[0];
        out.appendChild(el('div', { class: 'group-head' }, [el('span', { class: 'badge ' + (BADGES[ext(f.path)] || ['', 'b-txt'])[1], text: (BADGES[ext(f.path)] || ['TXT'])[0] }),
                                                           el('strong', { text: basename(f.path) }), el('span', { class: 'muted', text: ' ' + (f.root === 'www' ? 'www/' : '') + dirname(f.path) + ' (' + hits.length + ')' })]));
        hits.forEach(function (h) {
          var text = h.text, s = h.start, e = h.start + h.length;
          out.appendChild(el('button', { type: 'button', class: 'result', onclick: function () {
            openFile(E.app, h.root, h.path, { line: h.line, column: h.column, length: h.matchLength });
          } }, [el('span', { class: 'ln', text: String(h.line) }),
                el('span', { class: 'code' }, [text.slice(0, s), el('mark', { text: text.slice(s, e) }), text.slice(e)])]));
        });
      });
      if (!r.hits.length) out.appendChild(el('p', { class: 'empty', text: 'Nothing found in ' + E.app + '.' }));
    }).catch(function (e) { out.textContent = ''; out.appendChild(el('p', { class: 'empty error', text: e.message })); });
  }

  function renderHistory() {
    var box = $('historyList');
    var d = currentDoc();
    box.textContent = '';
    if (!d || d.kind !== 'text') { box.appendChild(el('p', { class: 'empty', text: 'Open a file to see its earlier versions.' })); return; }
    if (d.root === 'src') { box.appendChild(el('p', { class: 'empty', text: 'A file shown by the debugger, read-only: it has no history here.' })); return; }
    var head = el('div', { class: 'panel-tools' }, [
      el('strong', { text: basename(d.path) }),
      E.git ? button('Compare with HEAD', 'git', function () { compareHead(d); }) : null
    ]);
    box.appendChild(head);
    api('history', { app: d.app, root: d.root, path: d.path, op: 'list' }).then(function (list) {
      if (!list.length) { box.appendChild(el('p', { class: 'empty', text: 'No earlier versions yet: each save keeps the version it replaces.' })); return; }
      list.forEach(function (v) {
        box.appendChild(el('div', { class: 'result history-row' }, [
          icon('history'),
          el('span', { class: 'when', text: new Date(v.time * 1000).toLocaleString() }),
          el('span', { class: 'muted', text: ago(v.time) + ' - ' + v.user + ' - ' + size(v.size) }),
          button('Compare', 'eye', function () { compareVersion(d, v); }, { class: 'small' }),
          button('Restore', 'history', function () { restoreVersion(d, v); }, { class: 'small' })
        ]));
      });
    }).catch(function (e) { box.appendChild(el('p', { class: 'empty error', text: e.message })); });
  }

  function compareVersion(d, v) {
    api('history', { app: d.app, root: d.root, path: d.path, op: 'get', id: v.id }).then(function (r) {
      diffView(basename(d.path) + ' - ' + new Date(v.time * 1000).toLocaleString(), 'Left: the version of ' + ago(v.time) + '. Right: the editor now.',
        r.text, d.model.getValue(), langOf(d.root, d.path), [
          { label: 'Restore this version', run: function () { d.model.pushEditOperations ? replaceAll(d, r.text) : d.model.setValue(r.text); toast('Restored into the editor - save to keep it'); } }
        ]);
    }).catch(function (e) { toast(e.message, 'error'); });
  }

  function restoreVersion(d, v) {
    api('history', { app: d.app, root: d.root, path: d.path, op: 'get', id: v.id }).then(function (r) {
      replaceAll(d, r.text);
      toast('Restored the version of ' + ago(v.time) + ' into the editor - save to keep it, undo to go back');
    }).catch(function (e) { toast(e.message, 'error'); });
  }

  // Replaced as an edit, so undo brings the text back.
  function replaceAll(d, text) {
    if (!E.real) { d.model.setValue(text); return; }
    d.model.pushEditOperations([], [{ range: d.model.getFullModelRange(), text: text }], function () { return null; });
  }

  function compareHead(d) {
    if (!d || d.root === 'src') return;
    api('git', { app: d.app, root: d.root, path: d.path, op: 'head' }).then(function (r) {
      if (r.text === null) { toast(basename(d.path) + ' is not in HEAD'); return; }
      diffView(basename(d.path) + ' - HEAD', 'Left: the file in the last commit (' + (E.branch || 'HEAD') + '). Right: the editor now.', r.text, d.model.getValue(), langOf(d.root, d.path), []);
    }).catch(function (e) { toast(e.message, 'error'); });
  }

  // A side-by-side difference, left read-only, right editable; actions get the right side's
  // text.
  function diffView(title, note, left, right, lang, actions, mandatory) {
    return new Promise(function (resolve) {
      if (!E.real) {
        toast('The comparison needs Monaco, which could not be loaded', 'error');
        resolve();
        return;
      }
      var host = el('div', { class: 'diff-host' });
      var a = E.monaco.editor.createModel(left, lang);
      var b = E.monaco.editor.createModel(right, lang);
      var diff;
      var buttons = el('div', { class: 'form-buttons' }, [
        el('button', { type: 'button', class: 'btn', onclick: function () { close(); } }, [mandatory ? 'Cancel' : 'Close'])
      ].concat(actions.map(function (act) {
        return el('button', { type: 'button', class: 'btn' + (act.primary ? ' primary' : ''), onclick: function () {
          var text = b.getValue();
          close();
          resolve(act.run(text));
        } }, [act.label]);
      })));
      var body = el('div', { class: 'diff' }, [el('p', { class: 'note', text: note }), host, buttons]);
      var close = modal(title, body, { full: true, onclose: function () { if (diff) diff.dispose(); a.dispose(); b.dispose(); resolve(); } });
      diff = E.monaco.editor.createDiffEditor(host, { automaticLayout: true, originalEditable: false, renderSideBySide: true, theme: themeName(),
                                                     fontSize: settings.fontSize, minimap: { enabled: false } });
      diff.setModel({ original: a, modified: b });
    });
  }

  // ------------------------------------------------------------------------------------
  // Preview
  // ------------------------------------------------------------------------------------

  // The page a file previews: a template or its PHP is its own page; a wrapper or anything
  // in a directory's _xxx parts the directory's index; www/ files are shown as themselves.
  function pageOf(path) {
    if (!/\.(pad|html|php)$/.test(path)) return null;
    var parts = path.split('/'), file = parts.pop();
    var cut = parts.findIndex(function (p) { return p.charAt(0) === '_'; });
    if (cut >= 0) return null;
    if (/^_(inits|exits)\./.test(file)) return parts.join('/');
    if (file.charAt(0) === '_' || file.indexOf('[') >= 0) return null;
    var page = parts.concat([file.replace(/\.(pad|html|php)$/, '')]).join('/');
    return page === 'index' ? '' : page;
  }

  function previewFor(d) {
    if (!d) return;
    // a page under the debugger keeps the preview: reloading it would abandon the request
    if (D.on && D.state && D.state.session && /XDEBUG_SESSION=/.test($('previewFrame').getAttribute('src') || '')) return;
    if (d.kind === 'text' && ext(d.path) === 'md') { previewMarkdown(d); return; }
    var page = d.root === 'app' && d.app === E.app ? pageOf(d.path) : null;
    if (page !== null) showPreview(page);
    else if (d.root === 'www' && /\.(html?|svg|png|jpe?g|gif|webp)$/.test(d.path)) showPreviewUrl(boot.host + d.app + '/' + d.path);
    else if (!$('previewFrame').getAttribute('src')) showPreview(E.previewPage[E.app] || '');
  }

  function previewUrl(page) { return boot.host + E.app + '/' + (page ? '?' + page : ''); }

  function showPreview(page) {
    E.previewPage[E.app] = page;
    openPreviewPane();
    showPreviewUrl(previewUrl(page));
  }

  function showPreviewUrl(url) {
    var frame = $('previewFrame');
    frame.removeAttribute('srcdoc');
    frame.setAttribute('sandbox', 'allow-scripts allow-forms allow-popups allow-modals allow-downloads');
    if (frame.getAttribute('src') !== url) frame.setAttribute('src', url); else reloadPreview();
    $('previewUrl').value = url;
    $('previewOpen').setAttribute('href', url);
  }

  function previewMarkdown(d) {
    openPreviewPane();
    api('markdown', { text: d.model.getValue() }).then(function (r) {
      var frame = $('previewFrame');
      frame.setAttribute('sandbox', '');
      frame.removeAttribute('src');
      frame.setAttribute('srcdoc', '<!DOCTYPE html><meta charset="utf-8"><style>body{font:15px/1.55 system-ui,sans-serif;max-width:46em;margin:24px auto;padding:0 20px;color:#222}' +
        'pre,code{background:#f3f4f6;border-radius:4px}pre{padding:10px;overflow:auto}code{padding:1px 4px}h1,h2,h3{line-height:1.2}a{color:#0b6bcb}' +
        '@media(prefers-color-scheme:dark){body{background:#1b2028;color:#dde3ea}pre,code{background:#2a313c}a{color:#7fc8f8}}</style>' + r.html);
      $('previewUrl').value = d.path + ' (Markdown)';
    }).catch(function (e) { toast(e.message, 'error'); });
  }

  function reloadPreview() {
    var d = currentDoc();
    if (d && d.kind === 'text' && ext(d.path) === 'md') { previewMarkdown(d); return; }
    var frame = $('previewFrame');
    var src = frame.getAttribute('src');
    if (src) { frame.setAttribute('src', 'about:blank'); setTimeout(function () { frame.setAttribute('src', src); }, 10); }
  }

  function openPreviewPane() {
    E.previewOn = true;
    $('preview').hidden = false;
    $('previewGutter').hidden = false;
    $('previewButton').classList.add('on');
  }

  function togglePreview(on) {
    var want = on === undefined ? !E.previewOn : on;
    if (!want) {
      E.previewOn = false;
      $('preview').hidden = true;
      $('previewGutter').hidden = true;
      $('previewButton').classList.remove('on');
      return;
    }
    openPreviewPane();
    var d = currentDoc();
    if (d) previewFor(d);
    if (!$('previewFrame').getAttribute('src') && !$('previewFrame').getAttribute('srcdoc'))
      showPreviewUrl(previewUrl(E.previewPage[E.app] || ''));
  }

  function previewDevice(width, btn) {
    $('previewFrame').style.width = width ? width + 'px' : '100%';
    Array.prototype.forEach.call(document.querySelectorAll('.device'), function (b) { b.classList.toggle('on', b === btn); });
  }

  // ------------------------------------------------------------------------------------
  // The terminal: a command runs on the server as a job (apps/edit/_lib/terminal.php) whose
  // output is read a few times a second until it is done. Colours and the codes most tools
  // write are shown; a file name of an application in the output opens it. No input reaches
  // a command, and full-screen programs do not work: there is no terminal device.
  // ------------------------------------------------------------------------------------

  var T = { cwd: '', root: '', shell: '', job: null, offset: 0, started: 0, stops: 0, runCwd: '',
            history: store('termHistory') || [], pos: -1, draft: '', row: null, cr: false, style: {}, ready: false };

  function termRel(path) {
    var home = boot.home || '';
    if (home && path === home) return '~pad';
    if (home && path.indexOf(home + '/') === 0) return path.slice(home.length + 1);
    return path;
  }

  function termPrompt() {
    var p = $('termPrompt');
    p.textContent = '';
    p.appendChild(el('span', { class: 'term-dir', text: termRel(T.cwd || T.root || '') }));
    p.appendChild(el('span', { class: 'term-sign', text: T.job ? ' … ' : ' $ ' }));
    p.title = T.cwd || '';
    $('termState').textContent = T.job ? 'running - Ctrl+C stops' : '';
    $('termState').classList.toggle('busy', !!T.job);
    $('termStop').hidden = !T.job || E.panelTab !== 'terminal';
  }

  function termStart() {
    if (T.ready) { termPrompt(); return Promise.resolve(); }
    T.ready = true;
    return api('terminal', { op: 'where', app: E.app }).then(function (r) {
      T.cwd = T.root = r.cwd;
      T.shell = r.shell.split('/').pop();
      termWrite('\x1b[2m' + T.shell + ' in ' + termRel(T.cwd) + ' - commands run on this machine as the web server\'s user, with no input and no terminal device.\n' +
                'Tab completes a name, ↑ ↓ go through the history, Ctrl+C stops a command, clear (or Ctrl+L) empties the screen.\x1b[0m\n');
      termPrompt();
    }).catch(function (e) { T.ready = false; termWrite('\x1b[31m' + e.message + '\x1b[0m\n'); });
  }

  function termColumns() {
    var probe = el('span', { class: 'term-probe', text: 'MMMMMMMMMM' });
    $('termOut').appendChild(probe);
    var w = probe.getBoundingClientRect().width / 10 || 8;
    probe.remove();
    return Math.max(20, Math.floor(($('termScreen').clientWidth - 24) / w));
  }

  function termAtBottom() {
    var s = $('termScreen');
    return s.scrollHeight - s.scrollTop - s.clientHeight < 40;
  }

  function termScroll() { var s = $('termScreen'); s.scrollTop = s.scrollHeight; }

  function termRow() {
    var out = $('termOut');
    T.row = el('div', { class: 'term-row' });
    out.appendChild(T.row);
    while (out.childNodes.length > 5000) out.removeChild(out.firstChild);
    return T.row;
  }

  function termClear() {
    $('termOut').textContent = '';
    T.row = null;
    T.cr = false;
  }

  // The 256 colours past the first sixteen: a 6 x 6 x 6 cube, then 24 greys.
  function termColor(n) {
    if (n < 16) return null;
    if (n >= 232) { var g = 8 + (n - 232) * 10; return 'rgb(' + g + ',' + g + ',' + g + ')'; }
    n -= 16;
    var v = function (x) { return x ? 55 + x * 40 : 0; };
    return 'rgb(' + v(Math.floor(n / 36)) + ',' + v(Math.floor(n / 6) % 6) + ',' + v(n % 6) + ')';
  }

  function termSgr(params) {
    var p = params === '' ? [0] : params.split(';').map(function (x) { return parseInt(x, 10) || 0; });
    var st = T.style;
    for (var i = 0; i < p.length; i++) {
      var c = p[i];
      if (c === 0) { st = {}; }
      else if (c === 1) st.bold = true;
      else if (c === 2) st.dim = true;
      else if (c === 3) st.italic = true;
      else if (c === 4) st.underline = true;
      else if (c === 7) st.inverse = true;
      else if (c === 22) { delete st.bold; delete st.dim; }
      else if (c === 23) delete st.italic;
      else if (c === 24) delete st.underline;
      else if (c === 27) delete st.inverse;
      else if (c >= 30 && c <= 37) st.fg = c - 30;
      else if (c >= 90 && c <= 97) st.fg = c - 90 + 8;
      else if (c === 39) delete st.fg;
      else if (c >= 40 && c <= 47) st.bg = c - 40;
      else if (c >= 100 && c <= 107) st.bg = c - 100 + 8;
      else if (c === 49) delete st.bg;
      else if ((c === 38 || c === 48) && p[i + 1] === 5) { st[c === 38 ? 'fg' : 'bg'] = p[i + 2]; i += 2; }
      else if ((c === 38 || c === 48) && p[i + 1] === 2) { st[c === 38 ? 'fg' : 'bg'] = 'rgb(' + p[i + 2] + ',' + p[i + 3] + ',' + p[i + 4] + ')'; i += 4; }
    }
    T.style = st;
  }

  function termSpan(text) {
    var st = T.style, cls = [], style = {};
    var fg = st.inverse ? st.bg : st.fg, bg = st.inverse ? st.fg : st.bg;
    if (st.inverse && fg === undefined) cls.push('t-inv-fg');
    if (st.inverse && bg === undefined) cls.push('t-inv-bg');
    [['fg', fg], ['bg', bg]].forEach(function (x) {
      if (x[1] === undefined) return;
      if (typeof x[1] === 'number' && x[1] < 16) cls.push('t-' + x[0] + x[1]);
      else style[x[0] === 'fg' ? 'color' : 'backgroundColor'] = typeof x[1] === 'number' ? termColor(x[1]) : x[1];
    });
    if (st.bold) cls.push('t-bold');
    if (st.dim) cls.push('t-dim');
    if (st.italic) cls.push('t-italic');
    if (st.underline) cls.push('t-underline');
    var span = el('span', { class: cls.join(' ') || null, style: style });
    termLinks(span, text);
    return span;
  }

  // A file name in the output that is a file of an application - relative to the
  // directory the command ran in, or absolute - becomes a link that opens it, at its line
  // when one follows the name (file.pad:12).
  var TERM_FILE = /(?:\/|\.{1,2}\/)?(?:[\w@.\-]+\/)*[\w@\-][\w@.\-]*\.(?:pad|php|html|js|css|json|md|txt|sql|xml|yaml|yml|sh|csv)(?::\d+(?::\d+)?)?/g;

  function termLinks(span, text) {
    var last = 0, m;
    TERM_FILE.lastIndex = 0;
    while ((m = TERM_FILE.exec(text)) !== null) {
      var target = termTarget(m[0]);
      if (!target) continue;
      if (m.index > last) span.appendChild(document.createTextNode(text.slice(last, m.index)));
      span.appendChild(el('a', { href: '#', class: 'term-link', title: 'Open ' + target.app + '/' + target.path,
                                 onclick: (function (t) { return function (e) { e.preventDefault(); openFile(t.app, t.root, t.path, t.line ? { line: t.line, column: t.column || 1 } : null); }; })(target) },
                          [m[0]]));
      last = m.index + m[0].length;
    }
    if (last < text.length) span.appendChild(document.createTextNode(text.slice(last)));
  }

  function termTarget(token) {
    var home = boot.home || '';
    var parts = token.match(/^(.*?)(?::(\d+))?(?::(\d+))?$/);
    var name = parts[1];
    var abs = name.charAt(0) === '/' ? name : (T.runCwd || T.cwd) + '/' + name;
    var out = [];
    abs.split('/').forEach(function (seg) { if (seg === '..') out.pop(); else if (seg && seg !== '.') out.push(seg); });
    abs = '/' + out.join('/');
    var rootName = abs.indexOf(home + '/apps/') === 0 ? 'app' : abs.indexOf(home + '/www/') === 0 ? 'www' : '';
    if (!home || !rootName) return null;
    var rest = abs.slice(home.length + (rootName === 'app' ? 6 : 5));
    var app = E.apps.map(function (a) { return a.name; }).filter(function (a) { return rest.indexOf(a + '/') === 0; })
                .sort(function (a, b) { return b.length - a.length; })[0];
    if (!app) return null;
    var path = rest.slice(app.length + 1);
    if (app === E.app && !E.fileIndex[rootName + '|' + path]) return null;
    return { app: app, root: rootName, path: path, line: parts[2] ? parseInt(parts[2], 10) : 0, column: parts[3] ? parseInt(parts[3], 10) : 0 };
  }

  var TERM_CODES = /\x1b\[([0-9;?]*)([A-Za-z])|\x1b\][^\x07\x1b]*(?:\x07|\x1b\\)|\x1b[()][A-Za-z0-9]|\x1b.|\r\n|\n|\r|\x07|\x08|[^\x1b\r\n\x07\x08]+/g;

  function termWrite(text) {
    var stick = termAtBottom(), m;
    TERM_CODES.lastIndex = 0;
    while ((m = TERM_CODES.exec(text)) !== null) {
      var t = m[0];
      if (t === '\n' || t === '\r\n') { if (!T.row) termRow(); T.row = null; T.cr = false; }
      else if (t === '\r') T.cr = true;
      else if (t === '\x07') continue;
      else if (t === '\x08') { if (T.row && T.row.lastChild) { var lc = T.row.lastChild; lc.textContent = lc.textContent.slice(0, -1); } }
      else if (m[2] === 'm') termSgr(m[1]);
      else if (m[2] === 'K') { if (T.row) T.row.textContent = ''; }
      else if (m[2] === 'J' && (m[1] === '2' || m[1] === '3')) termClear();
      else if (t.charAt(0) === '\x1b') continue;
      else {
        if (!T.row) termRow();
        if (T.cr) { T.row.textContent = ''; T.cr = false; }
        T.row.appendChild(termSpan(t));
      }
    }
    if (stick) termScroll();
  }

  function termEcho(command) {
    var row = termRow();
    row.classList.add('term-command');
    row.appendChild(el('span', { class: 'term-dir', text: termRel(T.cwd) }));
    row.appendChild(el('span', { class: 'term-sign', text: ' $ ' }));
    row.appendChild(el('span', { text: command }));
    T.row = null;
    termScroll();
  }

  function termRun(command) {
    if (T.job) return;
    termEcho(command);
    if (/^\s*(clear|cls)\s*$/.test(command)) { termClear(); return; }
    if (!command.trim()) return;
    if (T.history[T.history.length - 1] !== command) T.history.push(command);
    if (T.history.length > 500) T.history = T.history.slice(-500);
    store('termHistory', T.history);
    T.job = 'starting';
    T.runCwd = T.cwd;
    T.style = {};
    T.row = null;
    T.cr = false;
    termPrompt();
    api('terminal', { op: 'run', command: command, cwd: T.cwd, app: E.app, columns: termColumns() }).then(function (r) {
      T.job = r.id;
      T.cwd = T.runCwd = r.cwd;
      T.offset = 0;
      T.started = Date.now();
      T.stops = 0;
      termPrompt();
      termPoll();
    }).catch(function (e) {
      T.job = null;
      termWrite('\x1b[31m' + e.message + '\x1b[0m\n');
      termPrompt();
    });
  }

  // One read at a time: a read asked for while one is under way follows it at once.
  function termPoll() {
    clearTimeout(T.timer);
    var id = T.job;
    if (!id || id === 'starting') return;
    if (T.polling) { T.again = true; return; }
    T.polling = true;
    api('terminal', { op: 'read', id: id, offset: T.offset }).then(function (r) {
      T.polling = false;
      if (T.job !== id) return;
      if (r.text) termWrite(r.text);
      T.offset = r.offset;
      if (r.done) termDone(r);
      else if (T.again) { T.again = false; termPoll(); }
      else T.timer = setTimeout(termPoll, r.text ? 60 : 250);
    }).catch(function (e) {
      T.polling = false;
      if (T.job !== id) return;
      T.job = null;
      termWrite('\n\x1b[31m' + e.message + '\x1b[0m\n');
      termPrompt();
    });
  }

  function termDone(r) {
    T.job = null;
    T.again = false;
    if (r.cwd) T.cwd = r.cwd;
    T.row = null;
    T.cr = false;
    T.style = {};
    var secs = (Date.now() - T.started) / 1000;
    var notes = [];
    if (r.stopped) notes.push('stopped');
    else if (r.exit) notes.push('exit ' + r.exit);
    if (secs >= 2) notes.push(secs < 60 ? secs.toFixed(1) + ' s' : Math.floor(secs / 60) + ' min ' + Math.round(secs % 60) + ' s');
    if (notes.length) termWrite('\x1b[' + (r.stopped || r.exit ? '31' : '2') + 'm[' + notes.join(', ') + ']\x1b[0m\n');
    termPrompt();
    termScroll();
    // A command may have changed files: the tree, and open files nobody is editing.
    refreshTree(true);
    checkDisk();
  }

  function termStop() {
    if (!T.job || T.job === 'starting') return;
    T.stops++;
    if (T.row) termWrite('\n');
    termWrite('\x1b[2m^C\x1b[0m\n');
    // read at once: the job is gone, and the next timer may be a second away in a tab the
    // browser throttles
    api('terminal', { op: 'kill', id: T.job, hard: T.stops > 1 }).then(termPoll, function (e) { toast(e.message, 'error'); });
  }

  function termComplete(input) {
    var value = input.value, at = input.selectionStart;
    var before = value.slice(0, at);
    var word = (before.match(/[^\s'"]*$/) || [''])[0];
    api('terminal', { op: 'complete', cwd: T.cwd, app: E.app, word: word }).then(function (names) {
      if (!names.length) return;
      var common = names.reduce(function (a, b) { var i = 0; while (i < a.length && a[i] === b[i]) i++; return a.slice(0, i); });
      if (common.length > word.length || names.length === 1) {
        var add = (names.length === 1 ? names[0] + (/\/$/.test(names[0]) ? '' : ' ') : common).slice(word.length);
        input.value = before + add + value.slice(at);
        input.setSelectionRange(at + add.length, at + add.length);
      } else {
        termEcho(value);
        termWrite(names.map(function (n) { return n.split('/').filter(Boolean).pop() + (/\/$/.test(n) ? '/' : ''); }).join('  ') + '\n');
      }
    }).catch(function () {});
  }

  function termKeys(e) {
    var input = e.target;
    if (e.key === 'Enter') {
      e.preventDefault();
      if (T.job) return;
      var command = input.value;
      input.value = '';
      T.pos = -1;
      termRun(command);
    } else if (e.key === 'ArrowUp' || e.key === 'ArrowDown') {
      if (!T.history.length) return;
      e.preventDefault();
      if (T.pos === -1) { T.draft = input.value; T.pos = T.history.length; }
      T.pos = Math.max(0, Math.min(T.history.length, T.pos + (e.key === 'ArrowUp' ? -1 : 1)));
      input.value = T.pos === T.history.length ? T.draft : T.history[T.pos];
      if (T.pos === T.history.length) T.pos = -1;
      input.setSelectionRange(input.value.length, input.value.length);
    } else if (e.key === 'Tab' && !e.shiftKey) {
      e.preventDefault();
      termComplete(input);
    } else if (e.ctrlKey && !e.metaKey && e.key.toLowerCase() === 'c') {
      e.preventDefault();
      if (T.job) termStop();
      else { termEcho(input.value + '^C'); input.value = ''; T.pos = -1; }
    } else if (e.ctrlKey && !e.metaKey && e.key.toLowerCase() === 'l') {
      e.preventDefault();
      termClear();
    } else if (e.ctrlKey && !e.metaKey && e.key.toLowerCase() === 'u') {
      e.preventDefault();
      input.value = input.value.slice(input.selectionStart);
      input.setSelectionRange(0, 0);
    }
  }

  function toggleTerminal() {
    if (!boot.terminal) { toast('The terminal is switched off ($editTerminal)'); return; }
    if (settings.panelOpen && E.panelTab === 'terminal' && document.activeElement === $('termInput')) {
      if (currentDoc() && currentDoc().kind === 'text') E.editor.focus();
      return;
    }
    showPanel('terminal');
  }

  // A job still running when the page goes stops with it - a beacon can carry the token
  // in the body, where a fetch would no longer be waited for.
  window.addEventListener('pagehide', function () {
    if (!T.job || T.job === 'starting' || !navigator.sendBeacon) return;
    var form = new FormData();
    form.append('padCsrfToken', csrf);
    form.append('op', 'kill');
    form.append('id', T.job);
    form.append('hard', '1');
    navigator.sendBeacon('?api&action=terminal&padFormat=json', form);
  });

  // ------------------------------------------------------------------------------------
  // The step debugger: Xdebug in the request being debugged talks to the debugger process
  // the editor starts (apps/edit/_bin/debugger.php); this asks it for its state - a long
  // poll - and sends it the breakpoints and the steps. Breakpoints go in PHP files: a click
  // in the gutter, F9 at the cursor, Shift+click for a condition. A request is debugged
  // when it carries XDEBUG_SESSION: Debug the page loads the preview with it, and a cookie
  // can carry it for every request of one application.
  // ------------------------------------------------------------------------------------

  var D = { info: null, on: false, seq: 0, state: null, frame: 0, view: 'locals', polling: false, built: false,
            bps: store('breakpoints') || {}, exceptions: !!store('dbgExceptions'), first: !!store('dbgFirst'),
            watches: store('dbgWatches') || [], cur: null, shownBreak: '' };

  function dbgAbs(d) {
    if (d.root === 'src') return d.path;
    return (boot.home || '') + '/' + (d.root === 'www' ? 'www/' : 'apps/') + d.app + '/' + d.path;
  }

  // A path on this machine as a file of an application, or null - an engine file, say.
  function dbgLocate(file) {
    var home = boot.home || '';
    var root = file.indexOf(home + '/apps/') === 0 ? 'app' : file.indexOf(home + '/www/') === 0 ? 'www' : '';
    if (!home || !root) return null;
    var rest = file.slice(home.length + (root === 'app' ? 6 : 5));
    var app = E.apps.map(function (a) { return a.name; }).filter(function (a) { return rest.indexOf(a + '/') === 0; })
                .sort(function (a, b) { return b.length - a.length; })[0];
    return app ? { app: app, root: root, path: rest.slice(app.length + 1) } : null;
  }

  function dbgShort(file) {
    var home = boot.home || '';
    return home && file.indexOf(home + '/') === 0 ? file.slice(home.length + 1) : file;
  }

  // ---- breakpoints

  function dbgIsPhp(d) { return d && d.model && d.model.getLanguageId() === 'php'; }

  function dbgDecorate(d) {
    if (!E.real || !d || !d.model) return;
    var list = D.bps[dbgAbs(d)] || [];
    var decos = list.map(function (bp) {
      return { range: new E.monaco.Range(bp.line, 1, bp.line, 1),
               options: { glyphMarginClassName: bp.condition ? 'dbg-bp dbg-bp-cond' : 'dbg-bp', stickiness: 1,
                          glyphMarginHoverMessage: { value: bp.condition ? 'Breakpoint when `' + bp.condition + '`' : 'Breakpoint - click to remove, Shift+click for a condition' } } };
    });
    d.bpIds = d.model.deltaDecorations(d.bpIds || [], decos);
  }

  // Lines move as the text is edited; the decorations move with them, so the breakpoints
  // are read back from where their decorations went.
  function dbgTrack(d) {
    if (!d.bpIds || !d.bpIds.length) return;
    var list = D.bps[dbgAbs(d)] || [];
    var moved = false;
    d.bpIds.forEach(function (id, i) {
      var r = d.model.getDecorationRange(id);
      if (r && list[i] && list[i].line !== r.startLineNumber) { list[i].line = r.startLineNumber; moved = true; }
    });
    if (moved) { dbgSaveBps(); dbgRenderBps(); }
  }

  var dbgSyncSoon = debounce(function () { dbgSync(); }, 400);

  function dbgSaveBps() {
    Object.keys(D.bps).forEach(function (k) { if (!D.bps[k].length) delete D.bps[k]; });
    store('breakpoints', D.bps);
    dbgSyncSoon();
  }

  function toggleBreakpoint(d, line) {
    if (!d || !d.model) return;
    if (!dbgIsPhp(d)) { toast('Breakpoints stop PHP code - set them in a .php file; a template is not PHP'); return; }
    var key = dbgAbs(d), list = D.bps[key] = D.bps[key] || [];
    var i = list.findIndex(function (bp) { return bp.line === line; });
    if (i >= 0) list.splice(i, 1); else list.push({ line: line, condition: '' });
    list.sort(function (a, b) { return a.line - b.line; });
    dbgDecorate(d);
    dbgSaveBps();
    dbgRenderBps();
  }

  function editCondition(d, line) {
    if (!dbgIsPhp(d)) return toggleBreakpoint(d, line);
    var key = dbgAbs(d), list = D.bps[key] = D.bps[key] || [];
    var bp = list.filter(function (b) { return b.line === line; })[0];
    ask('Breakpoint condition', 'Stop at line ' + line + ' only when this PHP expression is true', bp ? bp.condition : '', {
      ok: 'Set', note: 'For example $id == 42 or count($rows) > 10. Empty: stop every time.'
    }).then(function (cond) {
      if (cond === null) return;
      if (!bp) { bp = { line: line, condition: '' }; list.push(bp); list.sort(function (a, b) { return a.line - b.line; }); }
      bp.condition = cond;
      dbgDecorate(d);
      dbgSaveBps();
      dbgRenderBps();
    });
  }

  function dbgBpList() {
    var out = [];
    Object.keys(D.bps).forEach(function (file) {
      D.bps[file].forEach(function (bp) { out.push({ file: file, line: bp.line, condition: bp.condition || '' }); });
    });
    return out;
  }

  function dbgSync() {
    if (!D.on) return Promise.resolve();
    return api('debug', { op: 'breakpoints', list: dbgBpList(), exceptions: D.exceptions, first: D.first })
      .catch(function (e) { toast('Breakpoints: ' + e.message, 'error'); });
  }

  // ---- the debugger process

  function dbgStart() {
    return api('debug', { op: 'start' }).then(function (info) {
      D.info = info;
      D.on = true;
      dbgLog('The debugger listens on port ' + info.port + ' - Debug the page, or send a request with XDEBUG_SESSION.');
      return dbgSync().then(function () { dbgLoop(); dbgRender(); });
    }).catch(function (e) { toast('Debugger: ' + e.message, 'error'); dbgLog(e.message, 'error'); dbgRender(); });
  }

  function dbgStop() {
    api('debug', { op: 'stop' }).then(function () {
      D.on = false;
      D.state = null;
      dbgCurrent(null);
      dbgLog('The debugger stopped.');
      dbgRender();
    }).catch(function (e) { toast(e.message, 'error'); });
  }

  function dbgLoop() {
    if (!D.on || D.polling) return;
    D.polling = true;
    api('debug', { op: 'state', since: D.seq }).then(function (st) {
      D.polling = false;
      if (st.off) { D.on = false; D.state = null; dbgCurrent(null); dbgRender(); return; }
      dbgApply(st);
      setTimeout(dbgLoop, 30);
    }).catch(function (e) {
      D.polling = false;
      dbgLog(e.message, 'error');
      setTimeout(dbgLoop, 2000);
    });
  }

  function dbgApply(st) {
    var before = D.state;
    D.state = st;
    D.seq = st.seq;
    (st.log || []).slice(before && before.log ? before.log.length : 0).forEach(function (line) { dbgLog(line.replace(/^\S+ /, ''), 'note'); });
    if (st.status === 'break' && st.location) {
      var mark = st.breaks + '|' + st.location.file + ':' + st.location.line;
      if (mark !== D.shownBreak) {
        D.shownBreak = mark;
        D.frame = 0;
        if (st.message) dbgLog(st.message, 'error');
        dbgReveal(st.location.file, st.location.line, true);
        if (E.panelTab !== 'debug' || !settings.panelOpen) showPanel('debug');
        dbgWatchAll();
      }
    } else {
      dbgCurrent(null);
      D.shownBreak = '';
    }
    dbgRender();
  }

  function dbgCmd(op) {
    if (!D.state || D.state.status !== 'break') return;
    api('debug', { op: op }).then(function () {
      D.state.status = 'running';
      dbgCurrent(null);
      dbgRender();
    }).catch(function (e) { toast(e.message, 'error'); });
  }

  function dbgPaused() { return !!(D.on && D.state && D.state.status === 'break'); }

  // ---- where it stopped

  function dbgReveal(file, line, current) {
    var at = dbgLocate(file);
    var ticket = D.revealed = (D.revealed || 0) + 1;
    var opened = at ? openFile(at.app, at.root, at.path, { line: line, column: 1 }) : openSource(file, line);
    return Promise.resolve(opened).then(function (d) {
      // only the last reveal marks its line: an earlier one that answers late does not
      if (current && d && ticket === D.revealed && dbgPaused()) dbgCurrent(d, line);
      return d;
    });
  }

  // The line a paused request stands on - one at a time: every mark put before is taken off.
  function dbgCurrent(d, line) {
    (D.marks || []).forEach(function (m) { if (m.doc.model && !m.doc.model.isDisposed()) m.doc.model.deltaDecorations(m.ids, []); });
    D.marks = [];
    if (!d || !d.model || !E.real) return;
    D.marks.push({ doc: d, ids: d.model.deltaDecorations([], [{ range: new E.monaco.Range(line, 1, line, 1),
                                                                 options: { isWholeLine: true, className: 'dbg-line', glyphMarginClassName: 'dbg-arrow' } }]) });
  }

  // A file the editor has no root for - the engine's own, under pad/ - is shown read-only,
  // as the debugger reads it.
  function openSource(file, line) {
    var k = key('', 'src', file);
    var show = function (d) {
      if (E.tabs.indexOf(k) < 0) E.tabs.push(k);
      activate(k);
      E.editor.setPosition({ lineNumber: line || 1, column: 1 });
      if (E.editor.revealLineInCenter) E.editor.revealLineInCenter(line || 1);
      return d;
    };
    if (E.docs[k]) return Promise.resolve(show(E.docs[k]));
    if (D.loading && D.loading[k]) return D.loading[k].then(function (d) { return d && show(d); });
    D.loading = D.loading || {};
    return D.loading[k] = api('debug', { op: 'source', file: file }).then(function (r) {
      delete D.loading[k];
      if (E.docs[k]) return show(E.docs[k]);
      var d = { app: '', root: 'src', path: file, kind: 'text', writable: false, sha1: '', size: r.text.length, info: {} };
      d.model = E.monaco.editor.createModel(r.text, langOf('www', file), E.monaco.Uri.parse('pad://src' + file.split('/').map(encodeURIComponent).join('/')));
      d.savedVersion = d.model.getAlternativeVersionId();
      d.model.onDidChangeContent(function () { onChange(d); });
      E.docs[k] = d;
      dbgDecorate(d);
      return show(d);
    }).catch(function (e) { delete D.loading[k]; toast(dbgShort(file) + ': ' + e.message, 'error'); });
  }

  // ---- the panel

  function dbgBuild() {
    if (D.built) return;
    D.built = true;
    var p = $('panel-debug');
    var check = function (id, label, on, title) {
      return el('label', { class: 'dbg-check', title: title }, [el('input', { type: 'checkbox', id: id, checked: on }), label]);
    };
    p.appendChild(el('div', { class: 'dbg-bar', id: 'dbgBar' }, [
      el('button', { type: 'button', class: 'btn small', id: 'dbgPower' }, ['Start the debugger']),
      el('button', { type: 'button', class: 'btn small', id: 'dbgPage', title: 'Load the previewed page with XDEBUG_SESSION, so its PHP stops at the breakpoints' }, [icon('play'), 'Debug the page']),
      check('dbgCookie', 'every request to ' + (E.app || 'the app'), false, 'A cookie, XDEBUG_SESSION, for this application only: its pages stop at the breakpoints in any tab'),
      el('span', { class: 'dbg-sep' }),
      el('button', { type: 'button', class: 'btn small', id: 'dbgRun', title: 'Continue (F5)' }, ['Continue']),
      el('button', { type: 'button', class: 'btn small', id: 'dbgOver', title: 'Step over (F10)' }, ['Over']),
      el('button', { type: 'button', class: 'btn small', id: 'dbgInto', title: 'Step into (F11)' }, ['Into']),
      el('button', { type: 'button', class: 'btn small', id: 'dbgOut', title: 'Step out (Shift+F11)' }, ['Out']),
      el('button', { type: 'button', class: 'btn small danger', id: 'dbgHalt', title: 'Stop the request (Shift+F5)' }, ['Stop']),
      el('span', { class: 'dbg-sep' }),
      check('dbgExc', 'exceptions', D.exceptions, 'Stop where an exception is thrown'),
      check('dbgFirst', 'first line', D.first, 'Stop at the first line of every request'),
      el('span', { class: 'dbg-status', id: 'dbgStatus', 'aria-live': 'polite' })
    ]));
    p.appendChild(el('div', { class: 'dbg-main' }, [
      el('section', { class: 'dbg-col' }, [el('h4', { text: 'Call stack' }), el('div', { id: 'dbgStack', class: 'dbg-list' }),
                                            el('h4', { text: 'Breakpoints' }), el('div', { id: 'dbgBps', class: 'dbg-list' })]),
      el('section', { class: 'dbg-col dbg-vars' }, [
        el('div', { class: 'dbg-tabs', role: 'tablist' }, [['locals', 'Locals'], ['globals', 'Superglobals'], ['watch', 'Watch']].map(function (t) {
          return el('button', { type: 'button', class: 'dbg-vtab', 'data-view': t[0], onclick: function () { D.view = t[0]; dbgRenderVars(); } }, [t[1]]);
        })),
        el('div', { id: 'dbgVars', class: 'dbg-tree' })
      ]),
      el('section', { class: 'dbg-col dbg-console' }, [
        el('h4', { text: 'Console' }),
        el('div', { id: 'dbgLog', class: 'dbg-log', role: 'log' }),
        el('form', { class: 'dbg-eval', onsubmit: function (e) { e.preventDefault(); dbgEvalInput(); } },
           [el('input', { type: 'text', id: 'dbgEval', spellcheck: 'false', autocomplete: 'off', placeholder: 'A PHP expression, evaluated in the paused request', 'aria-label': 'Evaluate' })])
      ])
    ]));
    $('dbgPower').addEventListener('click', function () { if (D.on) dbgStop(); else dbgStart(); });
    $('dbgPage').addEventListener('click', dbgPage);
    $('dbgRun').addEventListener('click', function () { dbgCmd('run'); });
    $('dbgOver').addEventListener('click', function () { dbgCmd('step_over'); });
    $('dbgInto').addEventListener('click', function () { dbgCmd('step_into'); });
    $('dbgOut').addEventListener('click', function () { dbgCmd('step_out'); });
    $('dbgHalt').addEventListener('click', function () { if (D.state && D.state.session) api('debug', { op: 'stop_request' }).catch(function (e) { toast(e.message, 'error'); }); });
    $('dbgExc').addEventListener('change', function () { D.exceptions = this.checked; store('dbgExceptions', D.exceptions); dbgSync(); });
    $('dbgFirst').addEventListener('change', function () { D.first = this.checked; store('dbgFirst', D.first); dbgSync(); });
    $('dbgCookie').addEventListener('change', function () { dbgCookie(E.app, this.checked); });
    if (!D.info) api('debug', { op: 'status' }).then(function (info) {
      D.info = info;
      if (!info.loaded) dbgLog('This PHP has no Xdebug: install it (pecl install xdebug) and set xdebug.mode=debug - see the editor\'s README.', 'error');
      else if (!info.debug) dbgLog('Xdebug ' + info.version + ' runs here without its step debugger: xdebug.mode is "' + info.mode + '" - add debug to it.', 'error');
      else dbgLog('Xdebug ' + info.version + ', step debugging on port ' + info.port + '. Set breakpoints in a .php file (gutter, F9), start the debugger, Debug the page.');
      if (info.running && !D.on) { D.on = true; dbgSync().then(dbgLoop); }
      dbgRender();
    }).catch(function (e) { dbgLog(e.message, 'error'); });
    dbgRender();
  }

  function dbgCookieOn(app) {
    return document.cookie.split('; ').some(function (c) { return c === 'XDEBUG_SESSION=padedit'; }) && (store('dbgCookies') || []).indexOf(app) >= 0;
  }

  // The cookie's path is the application's own, so the editor's requests never carry it.
  function dbgCookie(app, on) {
    var path = (boot.root || '/') + app + '/';
    document.cookie = 'XDEBUG_SESSION=padedit; path=' + path + '; SameSite=Lax' + (on ? '' : '; max-age=0');
    var apps = (store('dbgCookies') || []).filter(function (a) { return a !== app; });
    if (on) apps.push(app);
    store('dbgCookies', apps);
    dbgLog(on ? 'Every request to ' + app + ' now asks for the debugger (a cookie for ' + path + ').' : 'Requests to ' + app + ' no longer ask for the debugger.');
    if (on && !D.on) dbgStart();
  }

  function dbgPage() {
    var go = function () {
      var d = currentDoc();
      var page = d && d.root === 'app' && d.app === E.app ? pageOf(d.path) : null;
      if (page === null) page = E.previewPage[E.app] || '';
      var url = previewUrl(page);
      url += (url.indexOf('?') >= 0 ? '&' : '?') + 'XDEBUG_SESSION=padedit';
      openPreviewPane();
      showPreviewUrl(url);
      dbgLog('Loading ' + url.replace(boot.host, '') + ' under the debugger.');
    };
    if (D.on) go(); else dbgStart().then(function () { if (D.on) go(); });
  }

  function dbgRender() {
    if (!D.built) return;
    var st = D.state, paused = dbgPaused();
    $('dbgPower').textContent = D.on ? 'Stop the debugger' : 'Start the debugger';
    $('dbgPower').classList.toggle('primary', !D.on);
    ['dbgRun', 'dbgOver', 'dbgInto', 'dbgOut'].forEach(function (id) { $(id).disabled = !paused; });
    $('dbgHalt').disabled = !(st && st.session);
    $('dbgCookie').checked = dbgCookieOn(E.app);
    $('dbgCookie').parentNode.lastChild.textContent = 'every request to ' + E.app;
    var text = !D.on ? 'off' : !st ? 'starting…' : st.status === 'break' ? 'paused at ' + dbgShort(st.location ? st.location.file : '') + ':' + (st.location ? st.location.line : '')
             : st.status === 'running' ? 'running ' + dbgShort(st.session ? st.session.file : '') : 'waiting for a request on port ' + st.port;
    $('dbgStatus').textContent = text;
    $('dbgDot').className = 'dbg-dot' + (paused ? ' paused' : D.on ? ' on' : '');
    var stack = $('dbgStack');
    stack.textContent = '';
    (st && st.status === 'break' ? st.stack : []).forEach(function (f, i) {
      var own = !!dbgLocate(f.file);
      stack.appendChild(el('button', { type: 'button', class: 'dbg-frame' + (i === D.frame ? ' active' : '') + (own ? '' : ' engine'), title: f.file + ':' + f.line,
                                       onclick: function () { dbgFrame(i); } },
                           [el('strong', { text: f.where }), el('span', { class: 'muted', text: ' ' + dbgShort(f.file).replace(/^.*\//, '') + ':' + f.line })]));
    });
    if (!stack.childNodes.length) stack.appendChild(el('p', { class: 'empty', text: D.on ? 'Not paused.' : 'The debugger is off.' }));
    dbgRenderBps();
    dbgRenderVars();
  }

  function dbgRenderBps() {
    if (!D.built) return;
    var box = $('dbgBps');
    box.textContent = '';
    var list = dbgBpList();
    list.forEach(function (bp) {
      box.appendChild(el('div', { class: 'dbg-bprow' }, [
        el('button', { type: 'button', class: 'dbg-frame', title: bp.file, onclick: function () { dbgReveal(bp.file, bp.line, false); } },
           [el('span', { class: 'dbg-bpdot' + (bp.condition ? ' cond' : '') }), dbgShort(bp.file).replace(/^.*\//, '') + ':' + bp.line,
            bp.condition ? el('span', { class: 'muted', text: ' if ' + bp.condition }) : null]),
        el('button', { type: 'button', class: 'tab-close', 'aria-label': 'Remove the breakpoint', onclick: function () {
          D.bps[bp.file] = (D.bps[bp.file] || []).filter(function (b) { return b.line !== bp.line; });
          openDocs().forEach(function (d) { if (dbgAbs(d) === bp.file) dbgDecorate(d); });
          dbgSaveBps();
          dbgRenderBps();
        } }, [icon('close')])
      ]));
    });
    if (!list.length) box.appendChild(el('p', { class: 'empty', text: 'None - click in the gutter of a .php file.' }));
  }

  function dbgFrame(i) {
    var st = D.state;
    if (!dbgPaused() || !st.stack[i]) return;
    D.frame = i;
    dbgReveal(st.stack[i].file, st.stack[i].line, true);
    api('debug', { op: 'frame', depth: i }).then(function (r) { st.locals = r.locals; D.view = 'locals'; dbgRender(); })
      .catch(function (e) { toast(e.message, 'error'); });
    dbgRender();
  }

  function dbgValue(p) {
    if (p.type === 'array') return el('span', { class: 'dv-type', text: 'array(' + p.numchildren + ')' });
    if (p.type === 'object') return el('span', { class: 'dv-type', text: (p.classname || 'object') + ' {' + p.numchildren + '}' });
    if (p.type === 'string') return el('span', { class: 'dv-str', text: JSON.stringify(p.value) + (p.cut ? '…' : '') });
    if (p.type === 'null' || p.type === 'uninitialized') return el('span', { class: 'dv-null', text: p.type });
    if (p.type === 'bool') return el('span', { class: 'dv-num', text: p.value === '1' ? 'true' : 'false' });
    return el('span', { class: p.type === 'int' || p.type === 'float' ? 'dv-num' : '', text: p.value });
  }

  function dbgVarRow(p, depth, context, frame, parentBox) {
    var row = el('div', { class: 'dbg-var', style: { paddingLeft: depth * 14 + 6 + 'px' } });
    var kids = el('div', { class: 'dbg-kids', hidden: true });
    var open = false;
    var toggle = p.children ? icon('chevron', 'chev') : el('span', { class: 'chev-space' });
    row.appendChild(toggle);
    row.appendChild(el('span', { class: 'dv-name', text: p.name || p.fullname || '(value)' }));
    row.appendChild(el('span', { class: 'dv-eq', text: p.children ? '' : ' = ' }));
    row.appendChild(dbgValue(p));
    if (p.children) {
      row.classList.add('openable');
      row.addEventListener('click', function () {
        open = !open;
        toggle.classList.toggle('open', open);
        kids.hidden = !open;
        if (!open || kids.childNodes.length) return;
        var fill = function (items) { items.forEach(function (c) { dbgVarRow(c, depth + 1, context, frame, kids); }); };
        if (p.items && p.items.length >= p.numchildren) { fill(p.items); return; }
        if (!p.fullname) { fill(p.items || []); return; }
        api('debug', { op: 'property', name: p.fullname, depth: frame, context: context }).then(function (r) {
          fill((r.property && r.property.items) || []);
          var more = r.property && r.property.numchildren > (r.property.items || []).length;
          if (more) kids.appendChild(el('div', { class: 'dbg-var muted', style: { paddingLeft: (depth + 1) * 14 + 26 + 'px' },
                                                  text: '… ' + (r.property.numchildren - r.property.items.length) + ' more' }));
        }).catch(function (e) { kids.appendChild(el('div', { class: 'dbg-var muted', text: e.message })); });
      });
    }
    row.title = p.fullname + (p.type ? ' : ' + p.type : '');
    parentBox.appendChild(row);
    parentBox.appendChild(kids);
  }

  function dbgRenderVars() {
    if (!D.built) return;
    Array.prototype.forEach.call(document.querySelectorAll('.dbg-vtab'), function (b) { b.classList.toggle('active', b.getAttribute('data-view') === D.view); });
    var box = $('dbgVars'), st = D.state;
    box.textContent = '';
    if (D.view === 'watch') {
      D.watches.forEach(function (w, i) {
        var line = el('div', { class: 'dbg-watch' });
        if (w.result) dbgVarRow(w.result, 0, 0, D.frame, line);
        else line.appendChild(el('div', { class: 'dbg-var' }, [el('span', { class: 'chev-space' }), el('span', { class: 'dv-name', text: w.expression }),
                                                                el('span', { class: 'muted', text: w.error ? '  ' + w.error : '  -' })]));
        line.appendChild(el('button', { type: 'button', class: 'tab-close dbg-unwatch', 'aria-label': 'Remove the watch', onclick: function () {
          D.watches.splice(i, 1); dbgSaveWatches(); dbgRenderVars(); } }, [icon('close')]));
        box.appendChild(line);
      });
      box.appendChild(el('form', { class: 'dbg-eval', onsubmit: function (e) {
        e.preventDefault();
        var v = this.querySelector('input').value.trim();
        if (!v) return;
        D.watches.push({ expression: v });
        dbgSaveWatches();
        dbgWatchAll();
      } }, [el('input', { type: 'text', placeholder: 'Add a watch: a PHP expression', spellcheck: 'false' })]));
      return;
    }
    var list = st && st.status === 'break' ? (D.view === 'globals' ? st.globals : st.locals) : [];
    (list || []).forEach(function (p) { dbgVarRow(p, 0, D.view === 'globals' ? 1 : 0, D.frame, box); });
    if (!box.childNodes.length) box.appendChild(el('p', { class: 'empty', text: dbgPaused() ? 'No variables here.' : 'Variables show when a request is paused.' }));
  }

  function dbgSaveWatches() {
    store('dbgWatches', D.watches.map(function (w) { return { expression: w.expression }; }));
  }

  function dbgWatchAll() {
    if (!dbgPaused()) { dbgRenderVars(); return; }
    Promise.all(D.watches.map(function (w) {
      return api('debug', { op: 'eval', expression: w.expression }).then(function (r) {
        w.result = r.property ? Object.assign({}, r.property, { name: w.expression, fullname: '' }) : null;
        w.error = '';
      }, function (e) { w.result = null; w.error = e.message; });
    })).then(dbgRenderVars);
  }

  function dbgLog(text, kind) {
    var box = $('dbgLog');
    if (!box) return;
    box.appendChild(el('div', { class: 'dbg-msg' + (kind ? ' ' + kind : ''), text: text }));
    while (box.childNodes.length > 300) box.removeChild(box.firstChild);
    box.scrollTop = box.scrollHeight;
  }

  function dbgEvalInput() {
    var input = $('dbgEval'), v = input.value.trim();
    if (!v) return;
    input.value = '';
    var box = $('dbgLog');
    box.appendChild(el('div', { class: 'dbg-msg cmd', text: '› ' + v }));
    if (!dbgPaused()) { dbgLog('Not paused: an expression is evaluated in a paused request.', 'error'); return; }
    api('debug', { op: 'eval', expression: v }).then(function (r) {
      var line = el('div', { class: 'dbg-msg result' });
      if (r.property) dbgVarRow(Object.assign({}, r.property, { name: '', fullname: '' }), 0, 0, D.frame, line);
      else line.textContent = '(nothing)';
      box.appendChild(line);
      box.scrollTop = box.scrollHeight;
    }).catch(function (e) { dbgLog(e.message, 'error'); });
  }

  // While paused, a PHP variable under the mouse shows its value.
  function dbgHover(model, position) {
    if (!dbgPaused()) return null;
    var line = model.getLineContent(position.lineNumber);
    var m, re = /\$[A-Za-z_][A-Za-z0-9_]*(?:(?:->|::)[A-Za-z_][A-Za-z0-9_]*|\[(?:'[^']*'|"[^"]*"|\d+)\])*/g;
    while ((m = re.exec(line)) !== null) {
      var s = m.index + 1, e = s + m[0].length;
      if (position.column < s || position.column > e) continue;
      return api('debug', { op: 'property', name: m[0], depth: D.frame }).then(function (r) {
        var p = r.property;
        if (!p) return null;
        var text = p.children ? (p.type === 'object' ? (p.classname || 'object') : 'array') + ' (' + p.numchildren + ')\n' +
                   (p.items || []).slice(0, 20).map(function (c) { return '  ' + c.name + ' => ' + (c.children ? c.type + '(' + c.numchildren + ')' : c.type === 'string' ? JSON.stringify(c.value) : c.value); }).join('\n')
                 : p.type === 'string' ? JSON.stringify(p.value) : p.type + ' ' + p.value;
        return { range: new E.monaco.Range(position.lineNumber, s, position.lineNumber, e), contents: [{ value: '```\n' + m[0] + ' = ' + text + '\n```' }] };
      }, function () { return null; });
    }
    return null;
  }

  function dbgSetup() {
    if (!E.real) return;
    E.monaco.languages.registerHoverProvider('php', { provideHover: function (model, position) { return dbgHover(model, position); } });
    E.editor.onMouseDown(function (e) {
      if (!e.target || e.target.type !== E.monaco.editor.MouseTargetType.GUTTER_GLYPH_MARGIN || !e.target.position) return;
      var d = currentDoc();
      if (!d) return;
      if (e.event.shiftKey) editCondition(d, e.target.position.lineNumber);
      else toggleBreakpoint(d, e.target.position.lineNumber);
    });
    api('debug', { op: 'status' }).then(function (info) {
      D.info = info;
      if (info.running) { D.on = true; dbgSync().then(dbgLoop); }
      dbgRender();
    }).catch(function () {});
  }

  // ------------------------------------------------------------------------------------
  // Settings, users, trash, shortcuts
  // ------------------------------------------------------------------------------------

  function settingsDialog() {
    var field = function (label, input) { return el('label', { class: 'setting' }, [el('span', { text: label }), input]); };
    var select = function (name, options) {
      var s = el('select', { onchange: function () { settings[name] = s.value; saveSettings(); applySettings(); } });
      options.forEach(function (o) { s.appendChild(el('option', { value: o[0], selected: String(settings[name]) === String(o[0]) }, [o[1]])); });
      return s;
    };
    var number = function (name, min, max) {
      var i = el('input', { type: 'number', min: min, max: max, value: settings[name], onchange: function () { settings[name] = Math.max(min, Math.min(max, parseInt(i.value, 10) || DEFAULTS[name])); saveSettings(); applySettings(); } });
      return i;
    };
    var check = function (name) {
      var c = el('input', { type: 'checkbox', checked: !!settings[name], onchange: function () { settings[name] = c.checked; saveSettings(); applySettings(); } });
      return c;
    };
    modal('Settings', el('div', { class: 'form settings' }, [
      field('Theme', select('theme', [['auto', 'Follow the system'], ['light', 'Light'], ['dark', 'Dark']])),
      field('Font size', number('fontSize', 9, 32)),
      field('Tab size', number('tabSize', 1, 8)),
      field('Word wrap', select('wordWrap', [['off', 'Off'], ['on', 'On'], ['bounded', 'At the ruler']])),
      field('Minimap', check('minimap')),
      field('Close a PAD block when its opening tag is typed', check('autoClose')),
      field('Check PAD pages while typing (runs the page\'s PHP)', check('checkTyping')),
      field('Save automatically', select('autosave', [['off', 'Off'], ['delay', 'After a pause in typing'], ['focus', 'When the window loses focus']])),
      el('p', { class: 'note', text: 'Kept in this browser only.' })
    ]));
  }

  function usersDialog() {
    var list = el('div', { class: 'users' });
    var load = function () {
      api('users', { op: 'list' }).then(function (users) {
        list.textContent = '';
        users.forEach(function (u) {
          list.appendChild(el('div', { class: 'user-row' }, [icon('user'), el('strong', { text: u.name }),
            el('span', { class: 'muted', text: u.self ? 'you' : 'since ' + new Date(u.created).toLocaleDateString() }),
            u.self ? null : button('Delete', 'trash', function () {
              confirmBox('Delete user', 'Delete ' + u.name + '? Their sessions end at once.', 'Delete', true).then(function (yes) {
                if (yes) api('users', { op: 'delete', name: u.name }).then(load).catch(function (e) { toast(e.message, 'error'); });
              });
            }, { class: 'small danger' })]));
        });
      }).catch(function (e) { toast(e.message, 'error'); });
    };
    var newName = el('input', { type: 'text', autocomplete: 'off', placeholder: 'name' });
    var newPass = el('input', { type: 'password', autocomplete: 'new-password', placeholder: 'password, 8 characters or more' });
    var add = el('form', { class: 'inline-form', onsubmit: function (e) {
      e.preventDefault();
      api('users', { op: 'add', name: newName.value, password: newPass.value }).then(function () { newName.value = ''; newPass.value = ''; toast('User added', 'ok'); load(); })
        .catch(function (err) { toast(err.message, 'error'); });
    } }, [newName, newPass, el('button', { type: 'submit', class: 'btn primary' }, ['Add'])]);
    var oldPw = el('input', { type: 'password', autocomplete: 'current-password', placeholder: 'current password' });
    var pw = el('input', { type: 'password', autocomplete: 'new-password', placeholder: 'new password' });
    var change = el('form', { class: 'inline-form', onsubmit: function (e) {
      e.preventDefault();
      api('users', { op: 'password', current: oldPw.value, password: pw.value }).then(function () { oldPw.value = ''; pw.value = ''; toast('Password changed - other sessions of yours have ended', 'ok'); })
        .catch(function (err) { toast(err.message, 'error'); });
    } }, [oldPw, pw, el('button', { type: 'submit', class: 'btn' }, ['Change'])]);
    modal('Users', el('div', { class: 'form' }, [list, el('h3', { text: 'Add a user' }), add, el('h3', { text: 'Your password' }), change,
      el('p', { class: 'note', text: 'Every user can edit every application. Users are kept in DATA/edit/users.json, as password hashes.' })]), { wide: true });
    load();
  }

  function trashDialog() {
    var list = el('div', { class: 'trash' });
    var load = function () {
      api('trash', { op: 'list' }).then(function (items) {
        list.textContent = '';
        if (!items.length) { list.appendChild(el('p', { class: 'empty', text: 'The trash is empty.' })); return; }
        items.forEach(function (t) {
          list.appendChild(el('div', { class: 'trash-row' }, [icon(t.dir ? 'folderPlus' : 'file'),
            el('span', { class: 'path', text: (t.root === 'www' ? 'www/' : 'apps/') + t.app + '/' + t.path }),
            el('span', { class: 'muted', text: ago(t.time) + ' - ' + (t.user || '') }),
            button('Put back', 'history', function () {
              api('trash', { op: 'restore', id: t.id }).then(function () { toast('Put back: ' + t.path, 'ok'); load(); if (t.app === E.app) refreshTree(); })
                .catch(function (e) { toast(e.message, 'error'); });
            }, { class: 'small' }),
            button('Delete for good', 'trash', function () {
              confirmBox('Delete for good', 'Delete ' + t.path + ' for good? This cannot be undone.', 'Delete', true).then(function (yes) {
                if (yes) api('trash', { op: 'drop', id: t.id }).then(load).catch(function (e) { toast(e.message, 'error'); });
              });
            }, { class: 'small danger', iconOnly: true })]));
        });
      }).catch(function (e) { toast(e.message, 'error'); });
    };
    modal('Trash', el('div', { class: 'form' }, [list, el('p', { class: 'note', text: 'Deleted files stay here for ' + (boot.trashDays || 30) + ' days.' })]), { wide: true });
    load();
  }

  function shortcutsDialog() {
    var rows = COMMANDS.filter(function (c) { return c.keys; }).map(function (c) { return el('tr', {}, [el('td', { text: c.label }), el('td', {}, [el('kbd', { text: c.keys })])]); });
    var extra = [['Command palette of the editor', 'F1'], ['Go to definition', MOD + 'click / F12'], ['Find / replace', MOD + 'F / ' + MOD + (isMac ? '⌥F' : 'H')],
                 ['Next problem', 'F8'], ['Outline of the PAD tags', MOD + (isMac ? '⇧O' : 'Shift+O')], ['Multiple cursors', isMac ? '⌥click' : 'Alt+click'],
                 ['Fold / unfold', MOD + (isMac ? '⌥[ / ]' : 'Shift+[ / ]')], ['Toggle comment {-- --}', MOD + '/']];
    extra.forEach(function (x) { rows.push(el('tr', {}, [el('td', { text: x[0] }), el('td', {}, [el('kbd', { text: x[1] })])])); });
    modal('Keyboard shortcuts', el('table', { class: 'keys' }, [el('tbody', {}, rows)]), { wide: true });
  }

  // ------------------------------------------------------------------------------------
  // Commands and keys
  // ------------------------------------------------------------------------------------

  var COMMANDS = [
    { id: 'save', label: 'Save', keys: MOD + 'S', icon: 'save', run: function () { save(); } },
    { id: 'saveAll', label: 'Save all', keys: MOD + (isMac ? '⌥S' : 'Alt+S'), icon: 'save', run: saveAll },
    { id: 'quick', label: 'Go to file', keys: MOD + 'P', icon: 'search', run: function () { quickOpen(''); } },
    { id: 'commands', label: 'Show all commands', keys: MOD + (isMac ? '⇧P' : 'Shift+P'), icon: 'play', run: function () { quickOpen('>'); } },
    { id: 'search', label: 'Search in the application', keys: MOD + (isMac ? '⇧F' : 'Shift+F'), icon: 'search', run: function () { showPanel('search'); } },
    { id: 'close', label: 'Close tab', keys: 'Alt+W', icon: 'close', run: function () { if (E.active) closeTab(E.active); } },
    { id: 'next', label: 'Next tab', keys: 'Alt+]', icon: 'chevron', run: function () { cycle(1); } },
    { id: 'prev', label: 'Previous tab', keys: 'Alt+[', icon: 'chevron', run: function () { cycle(-1); } },
    { id: 'pair', label: 'Switch between page .pad and .php', keys: 'Alt+O', icon: 'pair', run: switchPair },
    { id: 'newFile', label: 'New file', keys: 'Alt+N', icon: 'filePlus', run: newFile },
    { id: 'newFolder', label: 'New folder', icon: 'folderPlus', run: newFolder },
    { id: 'template', label: 'New from template', icon: 'template', run: newFromTemplate },
    { id: 'check', label: 'Check this file', keys: 'F7', icon: 'check', run: function () { var d = currentDoc(); if (d) check(d, isDirty(d)).then(function (r) { if (r && r.checked === false) toast('This file has no page of its own to check'); else if (r) { toast(r.markers.length ? r.markers.length + ' problem(s)' : 'No problems', r.markers.length ? 'error' : 'ok'); if (r.markers.length) showPanel('problems'); } }); } },
    { id: 'preview', label: 'Toggle preview', keys: 'Alt+P', icon: 'eye', run: function () { togglePreview(); } },
    { id: 'reload', label: 'Reload preview', keys: MOD + 'Enter', icon: 'refresh', run: reloadPreview },
    { id: 'side', label: 'Toggle file tree', keys: MOD + 'B', icon: 'side', run: function () { settings.sideOpen = !settings.sideOpen; saveSettings(); applySettings(); } },
    { id: 'panel', label: 'Toggle bottom panel', keys: MOD + 'J', icon: 'panel', run: function () { settings.panelOpen = !settings.panelOpen; saveSettings(); applySettings(); } },
    { id: 'reveal', label: 'Reveal in tree', icon: 'side', run: function () { var d = currentDoc(); if (d) select(d.root, d.path, true); } },
    { id: 'history', label: 'Show the history of this file', icon: 'history', run: function () { showPanel('history'); } },
    { id: 'terminal', label: 'Terminal', keys: 'Ctrl+`', icon: 'terminal', run: toggleTerminal },
    { id: 'debugPanel', label: 'Debugger', icon: 'play', run: function () { showPanel('debug'); } },
    { id: 'debugPage', label: 'Debug the page', icon: 'play', run: function () { showPanel('debug'); dbgPage(); } },
    { id: 'breakpoint', label: 'Toggle breakpoint', keys: 'F9', icon: 'warning', run: function () { var d = currentDoc(); if (d && d.kind === 'text') toggleBreakpoint(d, E.editor.getPosition().lineNumber); } },
    { id: 'continue', label: 'Debugger: continue', keys: 'F5', icon: 'play', run: function () { if (dbgPaused()) dbgCmd('run'); else if (D.on) dbgPage(); } },
    { id: 'stepOver', label: 'Debugger: step over', keys: 'F10', icon: 'chevron', run: function () { dbgCmd('step_over'); } },
    { id: 'stepInto', label: 'Debugger: step into', keys: 'F11', icon: 'chevron', run: function () { dbgCmd('step_into'); } },
    { id: 'stepOut', label: 'Debugger: step out', keys: 'Shift+F11', icon: 'chevron', run: function () { dbgCmd('step_out'); } },
    { id: 'stopRequest', label: 'Debugger: stop the request', keys: 'Shift+F5', icon: 'close', run: function () { if (D.state && D.state.session) api('debug', { op: 'stop_request' }).catch(function (e) { toast(e.message, 'error'); }); } },
    { id: 'head', label: 'Compare with HEAD', icon: 'git', run: function () { compareHead(currentDoc()); } },
    { id: 'apps', label: 'Switch application', keys: 'Alt+A', icon: 'apps', run: appPicker },
    { id: 'newApp', label: 'New application', icon: 'filePlus', run: newApp },
    { id: 'refresh', label: 'Refresh the file tree', icon: 'refresh', run: function () { refreshTree(); } },
    { id: 'settings', label: 'Settings', icon: 'gear', run: settingsDialog },
    { id: 'theme', label: 'Toggle dark / light', icon: 'moon', run: function () { settings.theme = themeName() === 'pad-dark' ? 'light' : 'dark'; saveSettings(); applySettings(); } },
    { id: 'users', label: 'Users', icon: 'user', run: usersDialog },
    { id: 'trash', label: 'Trash', icon: 'trash', run: trashDialog },
    { id: 'keys', label: 'Keyboard shortcuts', icon: 'keyboard', run: shortcutsDialog }
  ];

  function command(id) { return COMMANDS.filter(function (c) { return c.id === id; })[0]; }

  function cycle(step) {
    if (!E.tabs.length) return;
    var i = E.tabs.indexOf(E.active);
    activate(E.tabs[(i + step + E.tabs.length) % E.tabs.length]);
    var d = currentDoc();
    if (d && d.kind === 'text') E.editor.focus();
  }

  function switchPair() {
    var d = currentDoc();
    if (!d || d.root !== 'app') return;
    var p = pairOf(d.path);
    if (p) openFile(d.app, 'app', p);
    else toast('No paired file for ' + basename(d.path));
  }

  function registerActions() {
    COMMANDS.forEach(function (c) {
      E.editor.addAction({ id: 'pad.' + c.id, label: 'PAD: ' + c.label, run: function () { c.run(); } });
    });
  }

  document.addEventListener('keydown', function (e) {
    var mod = isMac ? e.metaKey : e.ctrlKey;
    var k = e.key.toLowerCase();
    // With Alt on a Mac the key is a symbol (Alt+P gives π): the physical key decides, and
    // the key itself when the event carries no code.
    var code = e.code || (/^[a-z]$/.test(k) ? 'Key' + k.toUpperCase() : k === '[' ? 'BracketLeft' : k === ']' ? 'BracketRight' : '');
    var run = function (id) { e.preventDefault(); e.stopPropagation(); closeMenu(); command(id).run(); };
    if (e.key === 'Escape') {
      if (menuOpen) { closeMenu(); return; }
      if (modals.length && modals[modals.length - 1].closable) { e.preventDefault(); modals[modals.length - 1].close(); return; }
    }
    if (mod && e.altKey && code === 'KeyS') return run('saveAll');
    if (mod && !e.shiftKey && k === 's') return run('save');
    if (mod && e.shiftKey && k === 'p') return run('commands');
    if (mod && !e.shiftKey && k === 'p') return run('quick');
    if (mod && e.shiftKey && k === 'f') return run('search');
    if (mod && !e.shiftKey && k === 'b') return run('side');
    if (mod && !e.shiftKey && k === 'j') return run('panel');
    if (mod && k === 'enter') return run('reload');
    if (e.altKey && !mod && code === 'KeyW') return run('close');
    if (e.altKey && !mod && code === 'KeyO') return run('pair');
    if (e.altKey && !mod && code === 'KeyN') return run('newFile');
    if (e.altKey && !mod && code === 'KeyP') return run('preview');
    if (e.altKey && !mod && code === 'KeyA') return run('apps');
    if (e.altKey && !mod && code === 'BracketRight') return run('next');
    if (e.altKey && !mod && code === 'BracketLeft') return run('prev');
    if (e.key === 'F7') return run('check');
    if (e.ctrlKey && !e.metaKey && !e.altKey && (code === 'Backquote' || k === '`')) return run('terminal');
    if (e.key === 'F9' && !e.shiftKey) return run('breakpoint');
    if (e.key === 'F5' && e.shiftKey && D.on) return run('stopRequest');
    if (e.key === 'F5' && !e.shiftKey && !mod && D.on) return run('continue');
    if (e.key === 'F10' && dbgPaused()) return run('stepOver');
    if (e.key === 'F11' && e.shiftKey && dbgPaused()) return run('stepOut');
    if (e.key === 'F11' && !e.shiftKey && dbgPaused()) return run('stepInto');
  }, true);

  window.addEventListener('beforeunload', function (e) {
    if (openDocs().some(isDirty)) { stash(); e.preventDefault(); e.returnValue = ''; }
  });

  window.addEventListener('focus', function () { checkDisk(); });
  window.addEventListener('blur', function () { if (settings.autosave === 'focus') openDocs().filter(isDirty).forEach(function (d) { save(d); }); });

  // The session is kept alive while the editor is on screen.
  setInterval(function () { if (document.visibilityState === 'visible') api('stat', { app: E.app, files: [] }).catch(function () {}); }, 300000);

  // ------------------------------------------------------------------------------------
  // Layout: the gutters between the panes
  // ------------------------------------------------------------------------------------

  function gutter(id, axis, name, min, max, invert) {
    var g = $(id);
    g.addEventListener('mousedown', function (e) {
      e.preventDefault();
      var start = axis === 'x' ? e.clientX : e.clientY, from = settings[name];
      document.body.classList.add('dragging');
      var moveFn = function (ev) {
        var d = (axis === 'x' ? ev.clientX : ev.clientY) - start;
        settings[name] = Math.max(min, Math.min(max, from + (invert ? -d : d)));
        document.documentElement.style.setProperty('--' + name.replace(/[A-Z]/g, function (c) { return '-' + c.toLowerCase(); }), settings[name] + 'px');
      };
      var up = function () {
        document.removeEventListener('mousemove', moveFn);
        document.removeEventListener('mouseup', up);
        document.body.classList.remove('dragging');
        saveSettings();
        if (E.editor) E.editor.layout();
      };
      document.addEventListener('mousemove', moveFn);
      document.addEventListener('mouseup', up);
    });
    g.addEventListener('dblclick', function () { settings[name] = DEFAULTS[name]; saveSettings(); applySettings(); });
  }

  // ------------------------------------------------------------------------------------
  // The status bar and the welcome screen
  // ------------------------------------------------------------------------------------

  function renderStatus() {
    var d = currentDoc();
    var left = $('statusLeft'), right = $('statusRight');
    left.textContent = '';
    right.textContent = '';
    if (E.branch) left.appendChild(el('span', { class: 'st', title: 'git branch' }, [icon('git'), E.branch]));
    left.appendChild(el('button', { type: 'button', class: 'st link', onclick: appPicker, title: 'Switch application (Alt+A)' }, [icon('apps'), E.app || '-']));
    if (d) left.appendChild(el('span', { class: 'st path', text: d.root === 'src' ? dbgShort(d.path) + ' (read-only)' : (d.root === 'www' ? 'www/' : 'apps/') + d.app + '/' + d.path }));
    right.appendChild(el('button', { type: 'button', class: 'st link' + (E.problemTotal ? ' bad' : ''), onclick: function () { showPanel('problems'); }, title: 'Problems' },
                         [icon(E.problemTotal ? 'warning' : 'check'), String(E.problemTotal || 0)]));
    if (d && d.kind === 'text') {
      var pos = E.editor.getPosition() || { lineNumber: 1, column: 1 };
      right.appendChild(el('span', { class: 'st', text: 'Ln ' + pos.lineNumber + ', Col ' + pos.column }));
      right.appendChild(el('span', { class: 'st', text: 'Spaces: ' + settings.tabSize }));
      right.appendChild(el('span', { class: 'st', text: LANG_NAMES[d.model.getLanguageId()] || d.model.getLanguageId() }));
      var state = d.saving ? 'Saving…' : d.checking ? 'Checking…' : isDirty(d) ? 'Not saved' : d.savedAt ? 'Saved ' + new Date(d.savedAt).toLocaleTimeString() : 'Saved';
      right.appendChild(el('span', { class: 'st' + (isDirty(d) ? ' warn' : ''), text: state }));
      if (!d.writable) right.appendChild(el('span', { class: 'st warn', text: 'Read-only' }));
    }
    if (!E.real && E.monaco) right.appendChild(el('span', { class: 'st warn', title: 'Monaco could not be loaded - a plain text area is used', text: 'Plain editor' }));
    right.appendChild(el('span', { class: 'st' }, [icon('user'), boot.user || '']));
  }

  function renderWelcome() {
    var w = $('welcome');
    w.textContent = '';
    var app = E.apps.filter(function (a) { return a.name === E.app; })[0] || {};
    var shortcut = function (label, keys, run) {
      return el('button', { type: 'button', class: 'welcome-action', onclick: run }, [el('span', { text: label }), keys ? el('kbd', { text: keys }) : null]);
    };
    w.appendChild(el('div', { class: 'welcome-inner' }, [
      el('div', { class: 'welcome-logo', 'aria-hidden': 'true', text: '{ }' }),
      el('h2', { text: E.app }),
      el('p', { class: 'muted', text: app.about || '' }),
      el('div', { class: 'welcome-actions' }, [
        shortcut('Go to file', MOD + 'P', function () { quickOpen(''); }),
        shortcut('New file or page', 'Alt+N', newFile),
        shortcut('New from template', '', newFromTemplate),
        shortcut('Search in ' + E.app, MOD + (isMac ? '⇧F' : 'Shift+F'), function () { showPanel('search'); }),
        shortcut('Preview', 'Alt+P', function () { togglePreview(true); }),
        shortcut('All commands', MOD + (isMac ? '⇧P' : 'Shift+P'), function () { quickOpen('>'); })
      ]),
      el('p', { class: 'muted small', text: 'PAD pages are checked by PAD itself when they are opened and saved. Every save keeps the version it replaces; deleted files go to the trash.' })
    ]));
  }

  // ------------------------------------------------------------------------------------
  // Start
  // ------------------------------------------------------------------------------------

  function buildChrome() {
    var side = $('sideTools');
    side.appendChild(button('New file', 'filePlus', newFile, { iconOnly: true, title: 'New file (Alt+N)' }));
    side.appendChild(button('New folder', 'folderPlus', newFolder, { iconOnly: true }));
    side.appendChild(button('New from template', 'template', newFromTemplate, { iconOnly: true }));
    side.appendChild(button('Upload files', 'upload', pickUpload, { iconOnly: true }));
    side.appendChild(button('Refresh', 'refresh', function () { refreshTree(); }, { iconOnly: true }));
    side.appendChild(button('Collapse all', 'collapse', collapseAll, { iconOnly: true }));

    $('appButton').addEventListener('click', appPicker);
    $('quickButton').addEventListener('click', function () { quickOpen(''); });
    $('quickKeys').textContent = MOD + 'P';
    $('saveButton').appendChild(icon('save'));
    $('saveButton').addEventListener('click', function () { save(); });
    $('previewButton').appendChild(icon('eye'));
    $('previewButton').addEventListener('click', function () { togglePreview(); });
    $('panelButton').appendChild(icon('panel'));
    $('panelButton').addEventListener('click', function () { command('panel').run(); });
    $('menuButton').appendChild(icon('more'));
    $('menuButton').addEventListener('click', function (e) {
      var r = e.currentTarget.getBoundingClientRect();
      contextMenu(r.right - 220, r.bottom + 4, [
        { label: 'Commands…', icon: 'play', keys: MOD + (isMac ? '⇧P' : 'Shift+P'), run: function () { quickOpen('>'); } },
        { label: 'Settings', icon: 'gear', run: settingsDialog },
        { label: 'Toggle dark / light', icon: 'moon', run: command('theme').run },
        { label: 'Keyboard shortcuts', icon: 'keyboard', run: shortcutsDialog },
        '-',
        { label: 'New application…', icon: 'filePlus', run: newApp },
        { label: 'Users', icon: 'user', run: usersDialog },
        { label: 'Trash', icon: 'trash', run: trashDialog },
        '-',
        { label: 'Log out', icon: 'logout', run: function () { if (openDocs().some(isDirty)) { confirmBox('Unsaved changes', 'Some files have unsaved changes. Log out anyway?', 'Log out', true).then(function (y) { if (y) { window.onbeforeunload = null; $('logoutForm').submit(); } }); } else $('logoutForm').submit(); } }
      ]);
    });

    $('treeFilter').addEventListener('input', debounce(renderTree, 120));
    $('treeFilter').addEventListener('keydown', function (e) {
      if (e.key === 'Enter') { var r = $('tree').querySelector('.row.file'); if (r) r.click(); }
      if (e.key === 'Escape') { $('treeFilter').value = ''; renderTree(); }
    });
    $('tree').addEventListener('dragover', function (e) { if (Array.prototype.indexOf.call(e.dataTransfer.types, 'Files') >= 0) e.preventDefault(); });
    $('tree').addEventListener('drop', function (e) { if (e.dataTransfer.files && e.dataTransfer.files.length) { e.preventDefault(); var t = targetDir(); upload(t.dir, t.root, e.dataTransfer.files); } });

    ['problems', 'search', 'history', 'terminal', 'debug'].forEach(function (p) { $('ptab-' + p).addEventListener('click', function () { showPanel(p); }); });
    if (!boot.terminal) $('ptab-terminal').hidden = true;
    if (!boot.debug) $('ptab-debug').hidden = true;
    $('termInput').addEventListener('keydown', termKeys);
    $('termStop').addEventListener('click', function () { termStop(); $('termInput').focus(); });
    $('termForm').addEventListener('submit', function (e) { e.preventDefault(); });
    $('termScreen').addEventListener('mouseup', function () { if (!String(window.getSelection())) $('termInput').focus(); });
    $('panelClose').appendChild(icon('close'));
    $('panelClose').addEventListener('click', function () { settings.panelOpen = false; saveSettings(); applySettings(); });
    $('searchForm').addEventListener('submit', runSearch);

    $('previewReload').appendChild(icon('refresh'));
    $('previewReload').addEventListener('click', reloadPreview);
    $('previewOpen').appendChild(icon('external'));
    $('previewClose').appendChild(icon('close'));
    $('previewClose').addEventListener('click', function () { togglePreview(false); });
    $('previewUrl').addEventListener('keydown', function (e) {
      if (e.key !== 'Enter') return;
      var v = $('previewUrl').value.trim();
      if (v.indexOf(boot.host) === 0) showPreviewUrl(v); else showPreview(v.replace(/^\?/, ''));
    });
    [['devicePhone', 390, 'phone'], ['deviceTablet', 820, 'tablet'], ['deviceDesktop', 0, 'desktop']].forEach(function (x) {
      var b = $(x[0]);
      b.appendChild(icon(x[2]));
      b.addEventListener('click', function () { previewDevice(x[1], b); });
    });

    gutter('sideGutter', 'x', 'sideWidth', 160, 640, false);
    gutter('panelGutter', 'y', 'panelHeight', 80, 700, true);
    gutter('previewGutter', 'x', 'previewWidth', 240, 1400, true);
  }

  function restoreStash() {
    var stashed = store('stash');
    if (!stashed || !stashed.length) return;
    store('stash', null);
    confirmBox('Unsaved changes found', stashed.length + ' file(s) had unsaved changes when the editor was left: ' +
               stashed.map(function (s) { return s.path; }).join(', ') + '. Put them back into the editor?', 'Put them back').then(function (yes) {
      if (!yes) return;
      stashed.forEach(function (s) {
        openFile(s.app, s.root, s.path).then(function (d) { if (d && d.model && d.model.getValue() !== s.text) replaceAll(d, s.text); });
      });
    });
  }

  function start() {
    buildChrome();
    applySettings();
    renderStatus();
    var wantApp = new URLSearchParams(location.search).get('app') || store('app') || '';
    var monacoReady = loadMonaco().catch(function (e) {
      toast(e.message + ' - a plain text area is used instead', 'error', 9000);
      return fallbackMonaco();
    });
    var baseReady = api('language', { app: '', base: true }).then(function (r) { E.base = r.base; });
    Promise.all([monacoReady, baseReady, loadApps()]).then(function (all) {
      setupEditor(all[0]);
      applySettings();
      document.body.classList.add('ready');
      return switchApp(wantApp);
    }).then(function () {
      var tabs = store('tabs');
      var chain = Promise.resolve();
      if (tabs && tabs.list) {
        tabs.list.forEach(function (t) { chain = chain.then(function () { return ensureDoc(t[0], t[1], t[2]).then(function () { var k = key(t[0], t[1], t[2]); if (E.tabs.indexOf(k) < 0) E.tabs.push(k); }).catch(function () {}); }); });
        chain = chain.then(function () { activate(tabs.active && E.docs[tabs.active] ? tabs.active : E.tabs[0] || null); });
      } else activate(null);
      return chain.then(restoreStash);
    }).catch(function (e) {
      toast('The editor could not start: ' + e.message, 'error', 20000);
    });
  }

  start();

})();
