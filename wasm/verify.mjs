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

// Exercise the shipped editor sample too: checking only hand-written templates missed
// a legacy tag left in the static page's textarea.
const editorPage = readFileSync(join(here, '../www/wasm/index.html'), 'utf8');
const editorValue = id => editorPage.match(new RegExp(`<textarea id="${id}"[^>]*>([\\s\\S]*?)</textarea>`))[1]
  .replace(/&lt;/g, '<').replace(/&gt;/g, '>').replace(/&quot;/g, '"').replace(/&amp;/g, '&');

const cases = [
  [ 'the shipped editor sample', editorValue('source'), editorValue('data'), /<li>Apple<\/li>\s*<li>Pear - sold out<\/li>/ ],
  [ 'a dynamic subsystem kind', '{icon:$kind/}', { kind: 'check' }, /pad-icon-check/ ],
  [ 'a dynamic subsystem pair', "{widget:$kind 'Saved'}Your changes{/widget:$kind}", { kind: 'toast' }, /Your changes/ ],
  [ 'composite fake rows', "{fake:rows 'name, email', rows=2, seed=7}<b>{$email}</b>{/fake:rows}", {}, html => (html.match(/<b>/g) || []).length === 2 && html.includes('@example.') ],
  [ 'automatic barcode', "{barcode:auto '400638133393'/}", {}, /pad-barcode/ ],
  [ 'automatic diagram preserves a node named auto', '{diagram:auto}auto\nA --> B{/diagram:auto}', {}, /<tspan[^>]*>auto<\/tspan>/ ],
  [ 'a removed wrapper', "{pad:chart 'bar'/}", {}, /no built-in tag named/ ],
  [ 'sequence runtime source', '{sequence:auto $source}{$sequence}{/sequence:auto}', { source: '1..5' }, /^12345$/ ],
  [ 'sequence dynamic camel-case kind', '{sequence:$kind rows=5}{$sequence},{/sequence:$kind}', { kind: 'newmanConway' }, /^1,1,2,2,3,$/ ],
  [ 'sequence dynamic play', "{keep:$kind '1..10'}{$sequence},{/keep:$kind}", { kind: 'prime' }, /^2,3,5,7,$/ ],
  [ 'sequence dynamic make', '{make:$kind 3, rows=4}{$sequence}{/make:$kind}', { kind: 'add' }, /^4567$/ ],
  [ 'sequence keep', "{keep:prime '1..10'}{$sequence},{/keep:prime}", {}, /^2,3,5,7,$/ ],
  [ 'sequence remove', "{remove:prime '1..10'}{$sequence},{/remove:prime}", {}, /^1,4,6,8,9,10,$/ ],
  [ 'sequence make', '{make:add 3, rows=4}{$sequence}{/make:add}', {}, /^4567$/ ],
  [ 'sequence action', "{action:reverse '1..5'}{$sequence}{/action:reverse}", {}, /^54321$/ ],
  ...['sequence', 'action', 'keep', 'make', 'remove'].map(kind =>
    ['removed sequence wrapper ' + kind, '{pad:' + kind + ' prime/}', {}, /no built-in tag named/]),
  [ 'a field',             '<p>{$name}</p>',                                         { name: 'Ann' },                       /<p>Ann<\/p>/ ],
  [ 'a loop',              '{array:items}<li>{$items}</li>{/array:items}',                        { items: [ 'a', 'b' ] },               /<li>a<\/li><li>b<\/li>/ ],
  [ 'a condition',         '{pad:if $n gt 1}many{pad:else}one{/pad:if}',                          { n: 3 },                              /many/ ],
  [ 'a pipe',              '{$title | upper}',                                        { title: 'pad' },                      /PAD/ ],
  [ 'a data block',        "{pad:data 'c'}[\"red\",\"blue\"]{/pad:data}{data:c}{$c} {/data:c}",        {},                                    /red blue/ ],
  [ 'a sequence',          "{sequence:range '1..4'}{$sequence}{/sequence:range}",                {},                                    /1234/ ],
  [ 'properties',          '{array:items}{$items}{property:notLast@items}, {/property:notLast@items}{/array:items}', { items: [ 'x', 'y', 'z' ] },        /x, y, z/ ],
  [ 'a strict error',      '{pad:nosuchtag}',                                             {},                                    /no built-in tag named/ ],
  [ 'a missing type prefix', '{if true}legacy{/if}', {}, /needs a type prefix/ ],
  [ 'JSON text',          '{$name}', ' {"name":"JSON"}', /JSON/ ],
  [ 'YAML text',          '{$name}', 'name: YAML\ncount: 2\n', /YAML/ ],
  [ 'YAML preserves text', '{$zip}|{$time}|{$country}', 'zip: 02134\ntime: 9:30\ncountry: NO\n', /02134\|9:30\|NO/ ],
  [ 'XML text',           '{$name}', '<root><name>XML</name></root>', /XML/ ],
  [ 'CSV rows',           '{array:rows}{$name},{/array:rows}', 'name,stock\nApple,3\nPear,0', /Apple,Pear,/ ],
  [ 'a JSON list',        '{array:rows}{$name},{/array:rows}', '[{"name":"Apple"},{"name":"Pear"}]', /Apple,Pear,/ ],
  [ 'empty data',         '<p>Empty</p>', '', /<p>Empty<\/p>/ ],
  [ 'PHP variables and echo', '{$name}', '', /before PHP/, { mode: 'php', php: '<?php echo "before "; $name = "PHP"; ?>' } ],
  [ 'PHP without opening tag', '{$name}', '', /Bare/, { mode: 'php', php: '$name = "Bare";' } ],
  [ 'empty PHP',          '<p>Empty PHP</p>', '', /<p>Empty PHP<\/p>/, { mode: 'php', php: '<?php\n\n?>' } ],
  [ 'data after PHP',     '{$name}', '{"name":"Data again"}', /Data again/ ],
  [ 'browser runtime flag', '{pad:if padWasm()}browser{pad:else}server{/pad:if}', {}, /^browser$/ ],
  [ 'tabs include their CSS', "{widget:tabs}{widget:tab 'One'}First{/widget:tab}{widget:tab 'Two'}Second{/widget:tab}{/widget:tabs}", {},
    /<style[^>]*>[\s\S]*?\.pad-tabs-panel\{display:none/ ],
  [ 'game CSS and ordered scripts', "{game:memory pairs=3/}", {}, html =>
    html.includes('.mem-grid') && /<script>\s*\/\*[\s\S]*?window\.PadGames[\s\S]*?<\/script>\s*<script>PadGames\.register\('memory'/.test(html)
    && ! /(?:src|href)="[^"]*assets\//.test(html) ],
  [ 'game assets once per page', "{game:memory pairs=3/}{game:memory pairs=4/}", {}, html =>
    (html.match(/<script>PadGames\.register\('memory'/g) || []).length === 1 ],
  [ 'input CSS and scripts', "{input:form 'try'}{input:stepper 'quantity', label='Quantity', value=2/}{/input:form}", {}, html =>
    html.includes('.pi-stepper-row') && html.includes("PadInputs.register('stepper'") && html.includes('window.PadInputs') ],
  [ 'section CSS', "{section:hero title='Browser styling'}Hello{/section:hero}", {}, /<style[^>]*>[\s\S]*?container:pad-section\/inline-size/ ],
  [ 'client chart scripts', "{chart:bar client, label='month', value='amount'}\nmonth,amount\nJan,12\nFeb,9\n{/chart:bar}", {}, html =>
    html.includes('window.PadChart') && html.includes('PadChart.mount(document.currentScript.previousElementSibling)')
    && ! /<script src=/.test(html) ],
];

let failed = 0;

for (const [label, template, data, want, options] of cases) {
  const { status, html } = await padRender(php, template, data, options);
  const ok = status === ((['a strict error', 'a missing type prefix', 'a removed wrapper'].includes(label) || label.startsWith('removed sequence wrapper ')) ? 500 : 200) && (typeof want === 'function' ? want(html) : want.test(html));
  if (!ok) failed++;
  console.log(`${ok ? 'ok  ' : 'FAIL'} ${label} (HTTP ${status})${ok ? '' : '\n     ' + html.slice(0, 300).replace(/\n/g, ' ')}`);
}

console.log(`${cases.length - failed} of ${cases.length} as expected on PHP ${(await php.run({ code: '<?php echo PHP_VERSION;' })).text}`);
process.exit(failed ? 1 : 0);
