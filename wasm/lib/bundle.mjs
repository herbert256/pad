// The files a packed application carries - shared by wasm/app.mjs, which writes them into
// pad-bundle.js, and wasm/verify-app.mjs, which installs them into PHP in Node without a
// pack. Keyed by their path in the repository; text as it is, anything that is no UTF-8
// base64-encoded, so JSON cannot mangle it.
//
//   pad/                the engine, without the sequence subsystem's large tables (the OEIS
//                       sqlite database and the generated lists over 64 KB - 79 of its 82 MB)
//   apps/<app>/         each application of the pack, its files up to 16 MB each
//   apps/_common/       the shared application
//   www/<app>/          its stylesheets, scripts and pictures, without the PHP entry point
//   runtime/            the PHP side of the runtime's database and storage tools
//
// Never packed: .env files - they hold a server's passwords and keys, which a browser that
// gets the pack can read; an application sets what it needs offline in _offline.json's env
// instead - and node_modules, _frontend (a Vite project's sources) and .DS_Store.

import { readFileSync, readdirSync, statSync, existsSync } from 'fs';
import { join, relative, dirname } from 'path';
import { fileURLToPath } from 'url';

export const home = dirname(dirname(dirname(fileURLToPath(import.meta.url))));

const ENGINE_MAX = 65536;
const APP_MAX    = 16 * 1048576;
const NEVER      = new Set(['.DS_Store', 'node_modules', '_frontend', '.git']);

export function collect(apps) {

  const files = {}, base64 = {}, skipped = [];
  let left = 0;

  function walk(dir, max, keep = () => true) {
    if (!existsSync(dir)) return;
    for (const name of readdirSync(dir)) {
      if (NEVER.has(name)) continue;
      const path = join(dir, name);
      const info = statSync(path);
      const key = relative(home, path).split('\\').join('/');
      if (info.isDirectory()) { walk(path, max, keep); continue; }
      if (!keep(key)) continue;
      if (name === '.env' || name.endsWith('.env')) { skipped.push(key); continue; }
      if (key.startsWith('pad/') && (name.endsWith('.sqlite') || info.size > max)) { left++; continue; }
      if (info.size > max) { skipped.push(key); continue; }
      add(key, readFileSync(path));
    }
  }

  function add(key, bytes) {
    const text = bytes.toString('utf8');
    if (Buffer.from(text, 'utf8').equals(bytes)) files[key] = text; else base64[key] = bytes.toString('base64');
  }

  walk(join(home, 'pad'), ENGINE_MAX);
  for (const app of apps) {
    walk(join(home, 'apps', app), APP_MAX);
    walk(join(home, 'www', app), APP_MAX, key => !key.endsWith('.php'));
  }
  walk(join(home, 'apps', '_common'), APP_MAX);
  add('runtime/pad-runtime.php', readFileSync(join(home, 'wasm', 'app', 'pad-runtime.php')));

  return { files, base64, left, skipped };

}

// What an application says about itself offline - apps/<app>/_offline.json - with the
// defaults for what it leaves out: its name from the README's title, PAD's blue.

export function offline(app) {

  let settings = {};
  const file = join(home, 'apps', app, '_offline.json');

  if (existsSync(file)) {
    try { settings = JSON.parse(readFileSync(file, 'utf8')); }
    catch (e) { throw new Error(`apps/${app}/_offline.json is no JSON: ${e.message}`); }
  }

  // Without them: the name from the README's title, the description from the first sentence
  // of its Introduction - what the apps listing shows.

  let title = app.split('/').pop();
  title = title.charAt(0).toUpperCase() + title.substring(1);
  let description = '';
  const readme = join(home, 'apps', app, 'README.md');
  if (existsSync(readme)) {
    const text = readFileSync(readme, 'utf8');
    const heading = text.match(/^#\s+(.+)$/m);
    if (heading && !settings.name) title = heading[1].replace(/\s+application$/i, '').trim();
    const intro = text.match(/^## Introduction\s*\n+([\s\S]*?)(\n\s*\n|\n## |$)/m);
    if (intro) description = intro[1].replace(/\s+/g, ' ').trim().replace(/^(.{20,180}?[.!?])\s.*$/, '$1');
  }

  return Object.assign({
    name: title, short: title, description, theme: '#2563eb', background: '#ffffff',
    icon: '', icons: {}, start: '', env: {}
  }, settings);

}
