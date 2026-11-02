// PAD on PHP compiled to WebAssembly - the part shared by the browser page (index.html, with
// @php-wasm/web) and the Node check (wasm/verify.mjs, with @php-wasm/node). Both hand in a
// running PHP; this module puts the engine into its in-memory file system and renders.
//
//   await padInstall(php, bundle)              the engine from pad-bundle.json (wasm/build.php)
//   await padRender(php, template, data, {mode, php}) -> { status, html, errors }
//
// A render writes the template as the page of an application called 'play' - its data, a
// data text or PHP code becomes the page's variables, as in Live - and runs the
// engine the way www/pad.php does. Nothing leaves the browser: no database is reachable,
// _common is off, and the request counts as local, so an error shows its full report. There
// is no PHP function list: what a template can reach is the browser's sandbox, not a server.

import { yamlJSON } from './yaml.js';

const ROOT = '/pad';

const CONFIG = `<?php
  $padCommon = FALSE;
  $padTidy   = FALSE;
  $padCookies = FALSE;
?>`;

const PAGE_PHP = `<?php
  if ( trim ( file_get_contents ( __DIR__ . '/mode.txt' ) ) === 'php' )
    include __DIR__ . '/input.php';
  else
    foreach ( liveData ( file_get_contents ( __DIR__ . '/data.txt' ) ) as $playKey => $playValue )
      if ( preg_match ( '/^[A-Za-z_][A-Za-z0-9_]*$/', $playKey ) and padValidStore ( $playKey ) )
        $GLOBALS [$playKey] = $playValue;
  unset ( $playKey, $playValue );
?>`;

const BOOT = `<?php
  $_SERVER ['PAD_WASM'] = '1';
  $_SERVER ['REMOTE_ADDR'] = '127.0.0.1';
  $padApps = '${ROOT}/apps/';
  $padApp  = 'play';
  $padData = '${ROOT}/DATA/';
  $padPage = empty ( $_SERVER ['PAD_WASM_PREPARE'] ) ? 'index' : 'prepare';
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
  write('apps/play/data.txt', '{}');
  write('apps/play/mode.txt', 'data');
  write('apps/play/input.php', '<?php\n\n?>');
  write('apps/play/prepare.pad', '');
  write('apps/play/prepare.php', `<?php
    $text = padDataText ( file_get_contents ( __DIR__ . '/data.txt' ) );
    $type = $text === '' ? '' : padContentType ( $text );
    echo json_encode ( [ 'type' => $type, 'text' => $text ] );
    exit;
  ?>`);
  write('boot.php', BOOT);
}

export async function padRender(php, template, data, options = {}) {
  php.writeFile(`${ROOT}/apps/play/index.pad`, template);
  php.writeFile(`${ROOT}/apps/play/data.txt`, typeof data === 'string' ? data : JSON.stringify(data || {}));
  php.writeFile(`${ROOT}/apps/play/mode.txt`, options.mode === 'php' ? 'php' : 'data');
  const code = options.php || '';
  php.writeFile(`${ROOT}/apps/play/input.php`, code.trimStart().startsWith('<?') ? code : '<?php ' + code);
  if (typeof data === 'string' && options.mode !== 'php') {
    // Ask PAD itself to sniff and de-indent data; CSV/XML/JSON and every other format
    // continue through its own reader. Only YAML needs the browser-side substitute.
    const prepared = await php.run({ scriptPath: `${ROOT}/boot.php`, $_SERVER: { PAD_WASM_PREPARE: '1' } });
    if (prepared.httpStatusCode !== 200) return { status: prepared.httpStatusCode, html: prepared.text, errors: prepared.errors };
    const input = JSON.parse(prepared.text);
    if (input.type === 'yaml') php.writeFile(`${ROOT}/apps/play/data.txt`, yamlJSON(input.text));
  }
  const response = await php.run({
    scriptPath: `${ROOT}/boot.php`,
    $_SERVER: { REMOTE_ADDR: '127.0.0.1', SCRIPT_NAME: '/play/index.php' }
  });
  return { status: response.httpStatusCode, html: response.text, errors: response.errors };
}
