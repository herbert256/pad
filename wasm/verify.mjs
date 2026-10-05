// Checks that the PAD engine runs on PHP compiled to WebAssembly, outside a browser: loads
// @php-wasm/node, installs www/wasm/pad-bundle.json with the same module the browser page
// uses (www/wasm/pad-wasm.js), renders a few templates and compares what they answer.
//
//   php wasm/build.php
//   npm install --prefix /tmp/pad-wasm @php-wasm/node @php-wasm/universal
//   NODE_PATH=/tmp/pad-wasm/node_modules node wasm/verify.mjs     (or run it from a directory
//                                                                   whose node_modules has them)
//
// Exits 0 when every case answers as expected.

import { createRequire } from 'module';
import { readFileSync } from 'fs';
import { dirname, join } from 'path';
import { fileURLToPath, pathToFileURL } from 'url';

const here = dirname(fileURLToPath(import.meta.url));
const require = createRequire(join(process.env.NODE_PATH || process.cwd(), 'noop.js'));
const { PHP } = await import(pathToFileURL(require.resolve('@php-wasm/universal')).href);
const { loadNodeRuntime } = await import(pathToFileURL(require.resolve('@php-wasm/node')).href);
const { padInstall, padRender } = await import(pathToFileURL(join(here, '../www/wasm/pad-wasm.js')).href);

const bundle = JSON.parse(readFileSync(join(here, '../www/wasm/pad-bundle.json'), 'utf8'));
const php = new PHP(await loadNodeRuntime(process.env.PHP_VERSION || '8.4', { emscriptenOptions: { processId: 1 } }));

await padInstall(php, bundle);

const cases = [
  [ 'a field',             '<p>{$name}</p>',                                         { name: 'Ann' },                       /<p>Ann<\/p>/ ],
  [ 'a loop',              '{items}<li>{$items}</li>{/items}',                        { items: [ 'a', 'b' ] },               /<li>a<\/li><li>b<\/li>/ ],
  [ 'a condition',         '{if $n gt 1}many{else}one{/if}',                          { n: 3 },                              /many/ ],
  [ 'a pipe',              '{$title | upper}',                                        { title: 'pad' },                      /PAD/ ],
  [ 'a data block',        "{data 'c'}[\"red\",\"blue\"]{/data}{c}{$c} {/c}",        {},                                    /red blue/ ],
  [ 'a sequence',          "{sequence '1..4'}{$sequence}{/sequence}",                {},                                    /1234/ ],
  [ 'properties',          '{items}{$items}{notLast@items}, {/notLast@items}{/items}', { items: [ 'x', 'y', 'z' ] },        /x, y, z/ ],
  [ 'a strict error',      '{nosuchtag}',                                             {},                                    /no tag named/ ],
];

let failed = 0;

for (const [label, template, data, want] of cases) {
  const { status, html } = await padRender(php, template, data);
  const ok = want.test(html);
  if (!ok) failed++;
  console.log(`${ok ? 'ok  ' : 'FAIL'} ${label} (HTTP ${status})${ok ? '' : '\n     ' + html.slice(0, 300).replace(/\n/g, ' ')}`);
}

console.log(`${cases.length - failed} of ${cases.length} as expected on PHP ${(await php.run({ code: '<?php echo PHP_VERSION;' })).text}`);
process.exit(failed ? 1 : 0);
