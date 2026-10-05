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
