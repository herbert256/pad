# PAD in the browser

PAD running on PHP compiled to WebAssembly - the approach of
[WordPress Playground](https://wordpress.github.io/wordpress-playground/), whose `@php-wasm`
packages supply PHP. A static page loads PHP into the browser tab, writes the PAD engine
into its in-memory file system, and renders templates there: no PAD server is involved.

## Files

| File | What it is |
|------|------------|
| `wasm/build.php` | Builds `www/wasm/pad-bundle.json`: every file of `pad/`, keyed by path, minus the sequence subsystem's large tables (the OEIS sqlite database and generated lists over 64 KB - 79 of the engine's 82 MB). About 1.8 MB, 1300 files. Generated, not in git |
| `www/wasm/pad-wasm.js` | The shared part: `padInstall(php, bundle)` puts the engine into a running PHP, `padRender(php, template, data)` renders a template with a JSON object as its variables |
| `www/wasm/index.html` | The browser page: template and data on the left, the output in a sandboxed frame on the right, both kept in the URL hash. Loads PHP 8.4 (asyncify build) from jsDelivr |
| `wasm/verify.mjs` | The same render in Node with `@php-wasm/node`, over eight templates - a field, a loop, a condition, a pipe, a data block, a sequence, properties, a strict-mode error |

## A whole application from file://

`www/wasm/` renders one template; `wasm/app.mjs` packs a whole application - every page, its
`_inits`, `_lib`, `_tags`, `_data`, its `www/` stylesheets and images - into a directory that
runs from the disk, `file://`, with no server and no network:

```bash
npm install --prefix /tmp/pad-wasm @php-wasm/universal @php-wasm/web-8-4 @php-wasm/node esbuild
NODE_PATH=/tmp/pad-wasm/node_modules node wasm/app.mjs demo          # DATA/wasm/demo/
NODE_PATH=/tmp/pad-wasm/node_modules node wasm/verify-app.mjs        # its requests, in Node
cd DATA/wasm && zip -r demo.zip demo                                 # one file to hand on
```

| File | What it is |
|------|------------|
| `wasm/app.mjs` | The packer. Writes `index.html`, `pad-app.js`, `php.js` (`@php-wasm/universal` and the PHP 8.4 loader bundled by esbuild into a classic script, 0.3 MB), `php-wasm.js` (the PHP binary gzipped and base64-encoded, 10 MB) and `pad-bundle.js` (`pad/`, `apps/<app>/`, `apps/_common/`, `www/<app>/` minus PHP - the limits of `build.php`, 2.4 MB). Zipped about 8 MB |
| `wasm/app/index.html` | The page: a frame filling the window that the application's pages are written into - no bar of its own; the page is kept after the `#`, so the browser's back button and reload work, and the window's title is the page's |
| `wasm/app/pad-app.js` | The runtime: installs the files, runs each request the way `www/pad.php` does for `http://localhost/<app>/`, keeps the cookies (sessions, CSRF, flash), follows redirects, keeps `DATA/` in localStorage. Runs in Node too |
| `wasm/verify-app.mjs` | Walks `demo` in Node: pages, a post with its token through the redirect to the flash message, a post without the token (403), a form breaking its rules, a 404 |

Why it is built this way: a `file://` page may not import a module or `fetch()` a file beside
it, so each part is a classic `<script>` setting a global, and the binary is handed to PHP as
`wasmBinary` instead of being fetched. The page catches every click on a link and every form
submit in the frame: one to the application is a request to PHP in the tab (a POST is
url-encoded), a link elsewhere opens in a new tab. Before a page is written into the frame its
`<link>`, `<script>`, `<img>` and `url(...)` references to the application's `www/` files
become blob URLs. The address of each page is kept in the hash, so the browser's back button
and a reload work; the browser's timezone becomes PHP's.

Verified (October 2026, headless Chromium 141, `@php-wasm` 3.1.56, opened as `file://` from
an unpacked zip, every non-local request blocked) on `poc`, the copy of `demo` it was first
tried on, since removed: all six pages render with their stylesheet, the guestbook and todo
posts, the contact form's rule messages and its accepted post with the flash message, back,
reload, and the guestbook entries surviving a reload of the tab. PHP starts in about 1-2
seconds; a page then takes 90-350 ms.

Not supported in this form: file uploads (`multipart/form-data`), a page's own JavaScript
calling the application (`fetch`, `{ajax}`, `{live}`), the database (no MySQL in the browser -
SQLite through PDO could be, not tried), `{curl}`, and the `.sqlite`/large sequence tables.
Firefox and Safari were not tried; they need `DecompressionStream` (Firefox 113, Safari 16.4).

## Running it

```bash
php wasm/build.php                                   # www/wasm/pad-bundle.json
open http://localhost/pad/wasm/                      # any static web server will do
```

The page needs network access to `cdn.jsdelivr.net` for `@php-wasm/universal` and the 20 MB
PHP binary; after the first load the browser caches them. It must be served over HTTP - a
`file://` page cannot fetch the bundle.

To check the engine on WebAssembly PHP without a browser:

```bash
npm install --prefix /tmp/pad-wasm @php-wasm/node @php-wasm/universal
NODE_PATH=/tmp/pad-wasm/node_modules node wasm/verify.mjs          # PHP 8.4
PHP_VERSION=8.3 NODE_PATH=/tmp/pad-wasm/node_modules node wasm/verify.mjs
```

## What was verified (October 2026, `@php-wasm` 3.1.56)

- `wasm/verify.mjs` passes all eight cases on the PHP 8.3, 8.4 and 8.5 WebAssembly builds in
  Node 26.
- `www/wasm/index.html` loads in Chrome, renders the sample, re-renders in about 35 ms, shows
  a template error as PAD's error report (HTTP 500), and restores the template from a link.

## What was not verified

- Other browsers: Firefox and Safari were not tried. The page uses the asyncify build, which
  needs no JSPI, so they should work, but nobody has looked.
- A static documentation site: the manual's examples are not yet wired to this page - each
  would need its fragment and `.php` data turned into a template plus JSON, which most
  fragments, reading the demo database or `_common` tags, cannot be.
- Pinning: the page loads `@php-wasm` 3.1.56 from jsDelivr. A later release may change the
  loader's options (`processId`, `phpWasmAsyncMode`) or the binary's path; the import map in
  `index.html` names that path.
- The database tags, `{curl}`, and anything else that needs a server: PHP in the browser has
  no MySQL server to reach and no network without a proxy.

## What the browser changes

The server playground (`apps/playground`) has to defend the server: local requests only,
`$padPhpFunctions = []`, time and size limits. In the browser the template can only reach
the tab it runs in, so `pad-wasm.js` sets no PHP function list - the sandbox is the browser's.
The output still renders in a sandboxed frame, so the rendered page's scripts do not run.
