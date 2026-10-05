// PAD on PHP compiled to WebAssembly - the part shared by the browser page (index.html, with
// @php-wasm/web) and the Node check (wasm/verify.mjs, with @php-wasm/node). Both hand in a
// running PHP; this module puts the engine into its in-memory file system and renders.
//
//   await padInstall(php, bundle)              the engine from pad-bundle.json (wasm/build.php)
//   await padRender(php, template, data)       -> { status, html, errors }
//
// A render writes the template as the page of an application called 'play' - its data, a
// JSON object, becomes the page's variables the way a page's .php sets them - and runs the
// engine the way www/pad.php does. Nothing leaves the browser: no database is reachable,
// _common is off, and the request counts as local, so an error shows its full report. There
// is no PHP function list: what a template can reach is the browser's sandbox, not a server.

const ROOT = '/pad';

const CONFIG = `<?php
  $padCommon = FALSE;
  $padTidy   = FALSE;
  $padCookies = FALSE;
?>`;

const PAGE_PHP = `<?php
  foreach ( json_decode ( file_get_contents ( __DIR__ . '/data.json' ), TRUE ) ?: [] as $playKey => $playValue )
    if ( preg_match ( '/^[A-Za-z_][A-Za-z0-9_]*$/', $playKey ) and ! str_starts_with ( $playKey, 'pad' ) )
      $GLOBALS [$playKey] = $playValue;
  unset ( $playKey, $playValue );
?>`;

const BOOT = `<?php
  $_SERVER ['REMOTE_ADDR'] = '127.0.0.1';
  $padApps = '${ROOT}/apps/';
  $padApp  = 'play';
  $padData = '${ROOT}/DATA/';
  $padPage = 'index';
  include '${ROOT}/pad/pad.php';
?>`;

function mkdirs(php, path) {
  if (!php.fileExists(path)) php.mkdirTree(path);
}

function dirOf(path) {
  return path.substring(0, path.lastIndexOf('/'));
}

function fromBase64(text) {
  const binary = atob(text);
  const bytes = new Uint8Array(binary.length);
  for (let i = 0; i < binary.length; i++) bytes[i] = binary.charCodeAt(i);
  return bytes;
}

export async function padInstall(php, bundle) {
  const made = new Set();
  const write = (name, content) => {
    const path = `${ROOT}/${name}`;
    const dir = dirOf(path);
    if (!made.has(dir)) { mkdirs(php, dir); made.add(dir); }
    php.writeFile(path, content);
  };
  for (const [name, text] of Object.entries(bundle.files)) write(name, text);
  for (const [name, text] of Object.entries(bundle.base64 || {})) write(name, fromBase64(text));
  mkdirs(php, `${ROOT}/DATA`);
  write('apps/play/_config/config.php', CONFIG);
  write('apps/play/index.php', PAGE_PHP);
  write('apps/play/index.pad', '');
  write('apps/play/data.json', '{}');
  write('boot.php', BOOT);
}

export async function padRender(php, template, data) {
  php.writeFile(`${ROOT}/apps/play/index.pad`, template);
  php.writeFile(`${ROOT}/apps/play/data.json`, JSON.stringify(data || {}));
  const response = await php.run({
    scriptPath: `${ROOT}/boot.php`,
    $_SERVER: { REMOTE_ADDR: '127.0.0.1', SCRIPT_NAME: '/play/index.php' }
  });
  return { status: response.httpStatusCode, html: response.text, errors: response.errors };
}
