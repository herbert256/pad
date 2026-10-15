# PAD in the browser

PAD runs on PHP compiled to WebAssembly - the `@php-wasm` packages of
[WordPress Playground](https://wordpress.github.io/wordpress-playground/) - in a browser tab,
with no PHP server. Two things are built on that:

- **Offline applications** (`pad pack`, `wasm/app.mjs`): whole PAD applications packed into one
  directory that runs from the disk or installs from any https host, with their SQLite
  databases kept in the browser between visits.
- **The template page** (`www/wasm/`, `wasm/build.php`): type a template and JSON data, see it
  rendered in the tab.

## Offline applications

```bash
pad pack --install                        # once: the @php-wasm packages and esbuild, in ~/.cache/pad-wasm
pad pack notes                            # DATA/wasm/notes/ - the application, the engine, PHP and the runtime
pad pack notes,ledger,kanban DATA/wasm/offline --name='PAD offline'    # several, with a launcher
pad pack notes --serve                    # and serve it on http://127.0.0.1:8090/ - installable there
```

`pad pack` runs `node wasm/app.mjs`, which can be used directly as well:
`node wasm/app.mjs <app>[,<app>...] [<directory>] [--name=x] [--panel=button|key|none] [--id=x]`.

The directory runs two ways:

| | Opened from the disk (`file://`) | Served over https, or on localhost |
|---|---|---|
| Start | double-click `index.html` | open the address; the browser offers to install it |
| PHP runs | in the tab | in the service worker, for every tab |
| The pages | in a frame filling the window, the address after the `#` | at their own addresses - nothing rewritten |
| The page's own requests | routed by a script put first in every page: `fetch`, `XMLHttpRequest`, `EventSource`, `sendBeacon`, `form.submit()` | answered by the service worker, as a server would |
| Offline | always - nothing is fetched | after the first visit - the worker keeps the pack |
| Data | IndexedDB of `file://` (localStorage where there is none) | IndexedDB of the site |

What every request gets, both ways:

- **Cookies** in a jar of the runtime's own - sessions, CSRF tokens, flash messages, remember-me
  - kept with the data, so a reload stays logged in. Sessions are saved under `DATA/sessions/`.
- **Any method and header** of the page's JavaScript - `Accept: application/json` for
  `$padExpose`, `HX-Request`/`HX-Target` for fragments, `X-CSRF-Token`, `Last-Event-ID`.
- **Uploads**: a `multipart/form-data` body fills `$_FILES`; `padUpload` stores the file under
  `DATA/uploads/`, kept like everything else.
- **Binary answers** - a picture a page draws, a PDF, a CSV, a download
  (`Content-Disposition: attachment` is saved as a file).
- **Server-sent events**: in the browser `padSse` answers one round per connection - see
  `padWasm()` and the manual page *Server-sent events* - and `EventSource` reconnects after the
  interval: one PHP answers the whole tab, and a stream that held it would hold everything.

### What is kept

Everything the applications write under `DATA/` - SQLite databases, uploads, JSON files,
sessions, logs - is written to IndexedDB after every request that changed it (only the files
that changed, by their modification time and size), and put back when the runtime starts.
Packs of the same applications share it (`--id` to choose), so a new build of a pack finds
the data of the last: an application that keeps its schema in `_migrations/` and runs
`padMigrate()` brings that data up to date. Two tabs of a pack opened from the disk tell each
other what they wrote (BroadcastChannel); over https there is one PHP for all of them.

### The database: SQLite

`pdo_sqlite` is in the WebAssembly build; MySQL is not. An application configured with
`padSqlDriver=sqlite` works as on a server: the database is a file under `DATA/`, built from
`padSqlSetup` on its first use, or by `_migrations/`. One written for MySQL can switch offline
in `apps/<app>/_offline.json`, whose `env` the runtime passes in `$_SERVER` - where `padEnv`
looks first:

```json
{
  "name": "Shop", "short": "Shop", "description": "Orders, offline",
  "theme": "#0f766e", "background": "#ffffff",
  "icon": "icon.svg", "icons": { "192": "icon-192.png", "512": "icon-512.png", "maskable": "icon-maskable.png" },
  "start": "",
  "env": { "padSqlDriver": "sqlite", "padSqlDatabase": "shop/shop.sqlite", "padSqlSetup": "_install/schema.sql" }
}
```

`icon` and `icons` are files of `www/<app>/`. Without `_offline.json` the name comes from the
README's title. `.env` files are never packed: they hold a server's secrets, and a pack is
readable by whoever has it.

### The panel

A small badge in the corner (or Ctrl+Shift+K) opens the runtime's panel:

| Tab | What it does |
|-----|--------------|
| Database | every SQLite file under `DATA/`, its tables and rows; a SQL console; export as `.sqlite` or as SQL (a dump sqlite3 reads back); import of either - checked by SQLite before it replaces anything; reset (the application builds it again) |
| Storage | the files under `DATA/`, how much of the browser's room they use, *Keep my data* (`navigator.storage.persist()`), a backup of all of it as one zip, restore, forget everything |
| Requests | the requests the application answered - pages, files, fetches, live regions, event streams - with status, time and size |
| About | the applications, PHP's version, the build, how it runs (from the disk, a tab, installed), install, a new build waiting |

The panel's calls are the runtime's own, under `<scope>_pad/` (`databases`, `rows`, `query`,
`export`, `import`, `reset`, `files`, `file`, `backup`, `restore`, `forget`, `log`, `info`,
`state`, `ready`); their PHP side is `wasm/app/pad-runtime.php`, run in the same PHP as the
pages. `--panel=key` leaves the badge out, `--panel=none` the panel.

### Files

| File | What it is |
|------|------------|
| `wasm/app.mjs` | The packer: collects the files (`wasm/lib/bundle.mjs`), bundles `@php-wasm/universal` and the PHP 8.4 loader into a classic script with esbuild, carries the binary gzipped and base64-encoded, writes the page, the worker, the manifest and the icons |
| `wasm/lib/bundle.mjs` | What a pack carries: `pad/` (without the sequence subsystem's large tables), each application and its `www/` files (no PHP entry point, no `.env`, no `node_modules`), `apps/_common/`, the runtime's PHP |
| `wasm/app/pad-app.js` | The runtime, for the page, the worker and Node: requests, cookies, the stores of `DATA/`, the `_pad/` calls |
| `wasm/app/index.html` | The page: from the disk PHP in the tab and the pages in a frame (each page a new frame, the script first in it routing the page's requests); over https the worker's installer; with several applications the launcher |
| `wasm/app/sw.js` | The service worker: PHP for every tab, the pack cached for offline use, a page asked for while PHP starts answered with a short wait page |
| `wasm/app/pad-panel.js` | The panel, in a shadow root; over https it also keeps the worker awake (a ping every 20 s) and offers a new build |
| `wasm/app/pad-runtime.php` | The PHP side of the panel's database and storage calls |
| `wasm/app/icons/` | PAD's icon, for a pack whose application brings none |
| `wasm/verify-app.mjs` | Walks the applications in Node with `@php-wasm/node` - no pack written - and starts the runtime again on the same store, as a next visit does |
| `wasm/package.json` | The packages, pinned; `pad pack --install` installs them in `~/.cache/pad-wasm`, outside the repository |

Why a classic script for everything: a `file://` page may not import a module or `fetch()` a
file beside it, so each part is a `<script>` (in the worker `importScripts`) setting a global,
and the binary is handed to PHP as `wasmBinary` instead of being fetched.

### Limits

- No MySQL; no network from PHP - `{curl}`, `padCurl`, `padPrefetch`, `SELF://` and a
  `{page app=...}` fetched over HTTP answer nothing offline. No sodium (`padEncrypt`) and no
  intl in the build; the sequence subsystem's large tables are left out.
- One PHP answers one request at a time: a page that sleeps holds the others.
- Opened from the disk: a module script of the application (`type="module"`) gets a blob:
  address, and its relative imports do not resolve; `document.cookie` does not show PHP's
  cookies; a page's `history.pushState` is not followed. Served over https none of this applies.
- The service worker keeps PHP in memory while a page of the application is open; a browser
  that stopped it starts it again on the next request (about a second), the data put back.

## The template page

`www/wasm/` renders one template with JSON data in the tab: `php wasm/build.php` writes
`www/wasm/pad-bundle.json` (the engine, about 1.8 MB), and the page loads PHP 8.4 from
jsDelivr. `www/wasm/pad-wasm.js` holds `padInstall(php, bundle)` and
`padRender(php, template, data)`; `wasm/verify.mjs` renders eight templates with them in Node.

```bash
php wasm/build.php                                   # www/wasm/pad-bundle.json
open http://localhost/pad/wasm/                      # any static web server will do
NODE_PATH=~/.cache/pad-wasm/node_modules node wasm/verify.mjs
```

The playground on a server (`apps/playground`) has to defend the server - local requests only,
no PHP functions, limits. In the browser a template can only reach the tab it runs in, so the
page sets no PHP function list; its output still renders in a sandboxed frame.

## Checking it

```bash
node wasm/verify-app.mjs           # every application and the runtime, in Node - also the wasm line of ./ci.sh
```

What was verified, and on what, is listed at the end of this file.

## Verified

October 2026, `@php-wasm` 3.1.58 (PHP 8.4.25), Chrome 141 on macOS:

- `wasm/verify-app.mjs`: demo's pages, posts with their token, the 403 and the 404; the
  runtime's database calls - list, rows, statements, export as SQL and as `.sqlite`, import of
  both, a refused import leaving the database as it was, reset, backup and restore - and a
  second start on the same store finding everything.
- Opened from the disk (headless Chrome, `file://`): demo renders with its stylesheet, a
  guestbook post through the frame's form, the entry kept in IndexedDB across a reload.
- Served on localhost: the service worker installs and answers the pages, the data written in
  the frame mode is there, and with the server stopped the pages still load.

Firefox and Safari were not tried.
