// The charts drawn in the browser (www/assets/charts/, the client option) against the charts
// the server draws: every example page of the charts application rendered by PHP, and again
// with client added to its chart tags, the JavaScript drawing those in Node - the two SVGs the
// same, character for character, ids aside (the server makes a client chart's id from its rows
// and options). The ./ci.sh line charts; it fails on the first page that parts, saying where.
// The templates of _parity/ beside this file are drawn the same way after the pages: what is
// no example - an option given an odd value, rows at an edge - where the two once parted.
//
//   node _client.mjs                 every example page, a line of summary
//   node _client.mjs --kinds=gauge   those kinds only, a line per page

import { execFileSync } from 'node:child_process';
import { readFileSync, readdirSync, statSync } from 'node:fs';
import { join, relative, dirname } from 'node:path';
import { fileURLToPath } from 'node:url';

const here = dirname(fileURLToPath(import.meta.url));
const HOME = process.env.PAD_HOME || join(here, '../../../..');
const dir  = join(HOME, 'apps/charts');
const kindsWanted = (process.argv.find(a => a.startsWith('--kinds=')) || '').slice(8).split(',').filter(Boolean);
const verbose = kindsWanted.length > 0;

const files = readdirSync(join(HOME, 'www/assets/charts')).filter(f => f.endsWith('.js'))
  .sort((a, b) => (a === 'core.js' ? -1 : b === 'core.js' ? 1 : a < b ? -1 : 1));
for (const f of files) await import(join(HOME, 'www/assets/charts', f));
const C = globalThis.PadChart;

const pages = [];
(function walk(d) {
  for (const f of readdirSync(d).sort()) {
    if (f.startsWith('_') || f.startsWith('.')) continue;
    const p = join(d, f);
    if (statSync(p).isDirectory()) walk(p);
    else if (f.endsWith('.pad') && !(d === dir && f === 'index.pad') && !f.includes('['))
      pages.push(relative(dir, p).slice(0, -4));
  }
})(dir);

const tagRe   = /\{(chart(?::[a-z]+)?|sparkline) *([^{}]*?)(\/?)\}/g;
const clientOf = src => src.replace(tagRe, (m, name, params, slash) => '{' + name + (params.trim() ? ' ' + params.trimEnd() + ', client' : ' client') + slash + '}');
const kindOf  = src => { const m = src.match(/\{chart(?::([a-z]+)|\s+'([a-z]+)')/); return m ? (m[1] || m[2]) : (src.includes('{sparkline') ? 'sparkline' : '?'); };
const render  = (page, source) => {
  try {
    return execFileSync('php', [join(here, '_client.php'), 'charts', page, ...(source !== undefined ? ['-'] : [])],
                        { input: source, encoding: 'utf8', maxBuffer: 64 << 20, env: { ...process.env, PAD_HOME: HOME } });
  } catch (e) { return e.stdout || ''; }
};
const unhtml = s => s.replace(/&quot;/g, '"').replace(/&#039;/g, "'").replace(/&lt;/g, '<').replace(/&gt;/g, '>').replace(/&amp;/g, '&');
const norm   = s => s.replace(/pad-chart-[0-9a-f]{8}(-\d+)?/g, 'pad-chart-ID');

let alike = 0, charts = 0, alikeParity = 0;
const kinds = new Set();

// A second pass draws every page again in a palette (palette=, pad/subs/charts/_lib/palette.php
// and client/palette.js) - the palettes of pad/subs/palettes/types/ in turn, a page each - so
// the colours the browser puts on a chart are the server's as well.

const palettes  = readdirSync(join(HOME, 'pad/subs/palettes/types')).filter(f => f.endsWith('.php')).map(f => f.slice(0, -4)).sort();
const paletteOf = (src, palette) => src.replace(tagRe, (m, name, params, slash) => '{' + name + ' ' + (params.trim() ? params.trimEnd() + ', ' : '') + "palette='" + palette + "'" + slash + '}');

// A template of _parity/ is rendered as a page of the charts application that has no file,
// parity, its text handed over as a page's text is: pass 2.

const parityDir = join(here, '_parity');
const parity    = readdirSync(parityDir).filter(f => f.endsWith('.pad')).sort().map(f => f.slice(0, -4));

for (const [pass, page, n] of [...pages.map((page, n) => [0, page, n]), ...pages.map((page, n) => [1, page, n]), ...parity.map((page, n) => [2, page, n])]) {

  const palette = (pass == 1) ? palettes[n % palettes.length] : '';
  const text    = readFileSync(pass == 2 ? join(parityDir, page + '.pad') : join(dir, page + '.pad'), 'utf8');
  const src     = palette ? paletteOf(text, palette) : text;

  if (kindsWanted.length && !kindsWanted.includes(kindOf(src)))
    continue;

  const server = (render(pass == 2 ? 'parity' : page, (palette || pass == 2) ? src : undefined).match(/<svg xmlns="http:\/\/www\.w3\.org\/2000\/svg" class="pad-chart[\s\S]*?<\/svg>/g) || []).map(norm);
  const specs  = [...render(pass == 2 ? 'parity' : page, clientOf(src)).matchAll(/<span class="pad-chart-client" data-pad-chart="([^"]*)"><\/span>/g)].map(m => JSON.parse(unhtml(m[1])));
  const client = specs.map(spec => { try { return norm(C.render(spec)); } catch (e) { return 'ERROR ' + e.message; } })
                      .filter(svg => svg !== '');

  let why = '';

  if (!server.length) why = 'the server drew no chart';
  else if (server.length !== client.length) why = `the server drew ${server.length} charts, the browser ${client.length}`;
  else if (palette && !server.every(s => / pc-pal-[a-z0-9-]+-[1-8]"/.test(s) && s.includes('{--pad-chart-series:'))) why = `the server drew no colours of the palette`;
  else {
    const i = server.findIndex((s, j) => s !== client[j]);
    if (i >= 0) {
      const a = server[i], b = client[i];
      let k = 0;
      while (k < a.length && a[k] === b[k]) k++;
      why = b.startsWith('ERROR') ? b : `they part at ${k}:\n  server  …${a.slice(Math.max(0, k - 80), k + 80)}…\n  browser …${b.slice(Math.max(0, k - 80), k + 80)}…`;
    }
  }

  if (why) {
    console.log(`charts: ${pass == 2 ? '_parity/' : ''}${page} [${specs[0]?.kind || kindOf(src)}]${palette ? ' in ' + palette : ''} - ${why}`);
    process.exit(1);
  }

  if (palette) continue;

  if (pass == 2) {
    alikeParity++;
    if (verbose) console.log(`_parity/${page} [${specs.map(s => s.kind).join(', ')}] alike`);
    continue;
  }

  alike++;
  charts += server.length;
  specs.forEach(s => kinds.add(s.kind));

  if (verbose) console.log(`${page} [${specs.map(s => s.kind).join(', ')}] alike`);

}

console.log(`${alike} example pages, ${charts} charts of ${kinds.size} kinds, drawn alike by the server and the browser - and again in the ${palettes.length} palettes - and ${alikeParity} templates of _parity/`);
