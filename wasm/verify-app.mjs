// Checks the runtime of packed applications (wasm/app/pad-app.js) outside a browser: PHP
// from @php-wasm/node, the files wasm/lib/bundle.mjs collects for a pack - no pack is
// written - and the same runtime the page and the service worker use, with DATA/ kept in a
// store in memory. It walks the applications as a visitor would: pages, posts with their
// tokens, uploads, the requests a page's own JavaScript makes, the server-sent events, the
// runtime's database tools - and starts the runtime again on the same store, as a browser
// does on the next visit, to see that everything was kept.
//
//   pad pack --install                                          the packages, once
//   node wasm/verify-app.mjs                                    every check
//   node wasm/verify-app.mjs demo                               the checks of one application
//   node wasm/verify-app.mjs --brief                            one line, and the failures - ./ci.sh's wasm line
//
// The packages come from NODE_PATH, else ~/.cache/pad-wasm/node_modules. Exits 0 when every
// step answers as expected, 1 when one does not, 2 when the packages are not there.

import { createRequire } from 'module';
import { join } from 'path';
import { homedir } from 'os';
import { pathToFileURL } from 'url';
import { home, collect, offline } from './lib/bundle.mjs';

const modules = process.env.NODE_PATH || join(homedir(), '.cache', 'pad-wasm', 'node_modules');
const require = createRequire(join(modules, 'noop.js'));

let universal, node;
try {
  universal = await import(pathToFileURL(require.resolve('@php-wasm/universal')).href);
  node = await import(pathToFileURL(require.resolve('@php-wasm/node')).href);
} catch (e) {
  console.log(`skipped - the @php-wasm packages are not in ${modules} (pad pack --install)`);
  process.exit(2);
}

const PadApp = require(join(home, 'wasm', 'app', 'pad-app.js'));
const brief  = process.argv.includes('--brief');
const only   = process.argv.slice(2).find(a => !a.startsWith('--')) || '';
const apps   = ['demo', 'notes', 'ledger', 'kanban'];
const store  = new PadApp.MemoryStore();
const bundle = Object.assign({ id: 'verify', apps, offline: Object.fromEntries(apps.map(a => [a, offline(a)])), build: 'verify' }, collect(apps));

async function start(withBundle = bundle, withStore = store) {
  const php = new universal.PHP(await node.loadNodeRuntime('8.4', { emscriptenOptions: { processId: 1 } }));
  const pad = new PadApp(php, withBundle, { store: withStore, FS: php[universal.__private__dont__use].FS, mode: 'node', timezone: 'UTC' });
  await pad.install();
  return pad;
}

let pad = await start();
let failed = 0, ran = 0;

function check(label, ok, answer) {
  ran++;
  if (!ok) failed++;
  const text = answer && answer.bytes ? new TextDecoder().decode(answer.bytes) : '';
  if (!ok || !brief) console.log(`${ok ? 'ok  ' : 'FAIL'} ${label}${ok || !answer ? '' : '\n     HTTP ' + answer.status + ' ' + text.slice(0, 400).replace(/\s+/g, ' ')}`);
}

const text = answer => new TextDecoder().decode(answer.bytes);
const json = answer => { try { return JSON.parse(text(answer)); } catch (e) { return {}; } };
const token = answer => (text(answer).match(/name="padCsrfToken" value="([0-9a-f]+)"/) || [])[1];
const get  = (url, headers) => pad.request('GET', url, undefined, headers);
const post = (url, body, headers) => pad.request('POST', url, body, headers);
const call = (path, method = 'GET', body, headers) => pad.handle({ method, url: `http://localhost/_pad/${path}`, body, headers });

// ---- demo: pages, a post with its token, the 403 and 404 of a server ------------------

if (!only || only === 'demo') {

  let answer = await get('http://localhost/demo/');
  check('demo: the home page', answer.status === 200 && /<h1>Welcome to PAD Demo<\/h1>/.test(text(answer)), answer);

  answer = await get('?guestbook');
  const guest = token(answer);
  check('demo: a form carries the CSRF token', !!guest, answer);

  answer = await post('?guestbook', `padCsrfToken=${guest}&action=add&name=Ann&comment=Offline`);
  check('demo: a post is redirected to the flash message', answer.url.endsWith('?guestbook') && /Thank you for signing/.test(text(answer)) && /Offline/.test(text(answer)), answer);

  answer = await post('?guestbook', 'action=add&name=Bob&comment=x');
  check('demo: a post without the token is refused', answer.status === 403, answer);

  answer = await get('?contact');
  answer = await post('?contact', `padCsrfToken=${token(answer)}&padForm=contact&name=Ann`);
  check('demo: a form that breaks its rules comes back with messages', /is required/.test(text(answer)), answer);

  answer = await get('?nosuchpage');
  check('demo: a page that does not exist', answer.status === 404, answer);

  answer = await get('http://localhost/demo/style.css');
  check('demo: a www/ file comes from the bundle', answer.status === 200 && /text\/css/.test(answer.headers['content-type'][0]), answer);

  answer = await get('http://localhost/nosuchapp/');
  check('an address of no application of the pack', answer.status === 404, answer);

}

// ---- notes: SQLite, a live region, a fragment, an upload served back, the JSON export ---

async function multipart(fields) {
  const form = new FormData();
  for (const [name, value] of Object.entries(fields)) form.append(name, value);
  const request = new Request('http://localhost/', { method: 'POST', body: form });
  return { body: new Uint8Array(await request.arrayBuffer()), type: request.headers.get('content-type') };
}

if (!only || only === 'notes') {

  let answer = await get('http://localhost/notes/');
  check('notes: the list, built from _install/schema.sql on the first request', answer.status === 200 && (text(answer).match(/<article class="note/g) || []).length === 4, answer);

  const live = (text(answer).match(/data-pad-csrf="([0-9a-f]+)"/) || [])[1];
  answer = await post('http://localhost/notes/?index', `padLive=notes&padEvent=pin&padValue=3&padCsrfToken=${live}`);
  check('notes: a {live} click pins a note - the region alone comes back', /class="note color-mint pinned"/.test(text(answer)) && !/<html/.test(text(answer)), answer);

  answer = await get('http://localhost/notes/?index&q=bread&padFragment=list', { 'x-requested-with': 'fetch' });
  check('notes: a search answered as the fragment alone', (text(answer).match(/<article/g) || []).length === 1 && /Weekend/.test(text(answer)) && !/<header/.test(text(answer)), answer);

  answer = await get('http://localhost/notes/?edit');
  const editor = token(answer);
  answer = await post('http://localhost/notes/?preview&padInclude', 'body=' + encodeURIComponent('The **ferry**'), { 'x-csrf-token': editor });
  check('notes: the preview, posted with the token in X-CSRF-Token', /<strong>ferry<\/strong>/.test(text(answer)), answer);

  answer = await post('http://localhost/notes/?preview&padInclude', 'body=x');
  check('notes: ... and refused without it', answer.status === 403, answer);

  const picture = new Uint8Array([0x89, 0x50, 0x4e, 0x47, 0x0d, 0x0a, 0x1a, 0x0a, 0, 0, 0, 13, 0x49, 0x48, 0x44, 0x52, 0, 0, 0, 1, 0, 0, 0, 1, 8, 6, 0, 0, 0, 0x1f, 0x15, 0xc4, 0x89,
                                  0, 0, 0, 13, 0x49, 0x44, 0x41, 0x54, 0x78, 0x9c, 0x63, 0xf8, 0xcf, 0xc0, 0xf0, 0x1f, 0, 5, 0, 1, 0xff, 0x0d, 0x9c, 0xa5, 0x6b, 0, 0, 0, 0, 0x49, 0x45, 0x4e, 0x44, 0xae, 0x42, 0x60, 0x82]);
  let form = await multipart({ padCsrfToken: editor, padForm: 'note', title: 'Harbour at night', body: '# Lights', tags: 'travel, photos', color: 'sea',
                               file: new File([picture], 'harbour.png', { type: 'image/png' }) });
  answer = await post('http://localhost/notes/?edit', form.body, { 'content-type': form.type });
  check('notes: a note posted with a picture (multipart) is stored and shown', /<h1>Harbour at night<\/h1>/.test(text(answer)) && /Your note is written/.test(text(answer)) && /<img src="\/notes\/\?file&id=1"/.test(text(answer)), answer);

  answer = await get('http://localhost/notes/?file&id=1');
  check('notes: the picture comes back as image/png (padUploadSend)', answer.status === 200 && answer.headers['content-type'][0] === 'image/png' && answer.bytes.length === picture.length, answer);

  answer = await get('http://localhost/notes/?edit');
  form = await multipart({ padCsrfToken: token(answer), padForm: 'note', title: '', body: 'no title' });
  answer = await post('http://localhost/notes/?edit', form.body, { 'content-type': form.type });
  check('notes: a note without a title comes back with the rule\'s message', /class="error"/.test(text(answer)) && /Title/.test(text(answer)) && /no title/.test(text(answer)), answer);

  answer = await get('http://localhost/notes/?export&padFormat=json');
  const exported = json(answer).notes || [];
  check('notes: the JSON export ($padExpose) holds every note with its tags', exported.length === 5 && exported[4].tags.join() === 'photos,travel', answer);

  answer = await get('http://localhost/notes/?database&download');
  check('notes: the database file downloads', /attachment; filename="notes.sqlite"/.test(answer.headers['content-disposition'][0]) && text({ bytes: answer.bytes.subarray(0, 15) }) === 'SQLite format 3', answer);

}

// ---- ledger: its migrations, a later version migrating the database a visitor kept, ------
//      JSON in and out, CSV out and in

if (!only || only === 'ledger') {

  // The version before the budgets: the same pack without that migration, its own store.

  const before = Object.assign({}, bundle, { files: Object.assign({}, bundle.files) });
  delete before.files['apps/ledger/_migrations/2026_10_01_090000_add_budgets.php'];
  const kept = new PadApp.MemoryStore();
  const sql = async (runtime, query) => json(await runtime.handle({ method: 'POST', url: 'http://localhost/_pad/query?db=ledger/ledger.sqlite', body: JSON.stringify({ sql: query }) }));

  let old = await start(before, kept);
  let answer = await old.request('GET', 'http://localhost/ledger/?transactions&padInclude');
  const counted = (await sql(old, "SELECT count(*) FROM transactions")).results[0].rows[0][0];
  const tables = (await sql(old, "SELECT group_concat(name) FROM sqlite_master WHERE type = 'table' AND name IN ('budgets', 'transactions')")).results[0].rows[0][0];
  check('ledger: the first version builds its database by its migrations and seeds it', answer.status === 200 && counted > 100 && tables === 'transactions', answer);

  let next = await start(bundle, kept);
  answer = await next.request('GET', 'http://localhost/ledger/');
  const after = (await sql(next, "SELECT (SELECT count(*) FROM transactions), (SELECT count(*) FROM budgets)")).results[0].rows[0];
  check('ledger: the next version migrates the database the visitor kept - budgets added, transactions untouched',
        answer.status === 200 && after[0] === counted && after[1] === 8 && /class="pad-progress|<progress/.test(text(answer)), answer);

  answer = await get('http://localhost/ledger/');
  check('ledger: the overview of the newest month, with its two charts', /<h1>September 2026<\/h1>/.test(text(answer)) && /pad-chart-bar/.test(text(answer)) && /pad-chart-donut/.test(text(answer)), answer);

  const meta = (text(answer).match(/<meta name="csrf-token" content="([0-9a-f]+)">/) || [])[1];
  answer = await post('http://localhost/ledger/?quick&padFormat=json', JSON.stringify({ amount: '12.50', category: 4, note: 'Coffee beans', month: '2026-09' }),
                      { 'content-type': 'application/json', accept: 'application/json', 'x-csrf-token': meta });
  const quick = json(answer);
  check('ledger: the quick entry - JSON in, the booking and the new totals out', quick.ok === true && quick.entry.amount === -12.5 && /^€ 2,306.08$/.test(quick.shown.spent), answer);

  answer = await get('http://localhost/ledger/?transactions&month=2026-09&padFormat=csv');
  const csv = text(answer).split('\n');
  check('ledger: the transactions as CSV ($padExpose)', /^text\/csv/.test(answer.headers['content-type'][0]) && csv[0].trim() === 'day,category,account,note,amount' && /Coffee beans/.test(text(answer)), answer);

  answer = await get('http://localhost/ledger/?import');
  let form = await multipart({ padCsrfToken: token(answer), padForm: 'import',
                               csv: new File(['day,amount,category,note\n2026-10-02,-23.40,Groceries,Market\n2026-10-25,3200,Salary,Salary\nyesterday-ish,x,Fun,broken\n'], 'bank.csv', { type: 'text/csv' }) });
  answer = await post('http://localhost/ledger/?import', form.body, { 'content-type': form.type });
  check('ledger: a CSV imported, the row that is no transaction named', /2 transactions imported, 1 rows skipped/.test(text(answer)) && /Line 4: skipped/.test(text(answer)), answer);

  answer = await get('http://localhost/ledger/?index&month=2026-10');
  check('ledger: ... its month on the overview', /<h1>October 2026<\/h1>/.test(text(answer)) && /&euro; 3,200.00/.test(text(answer)), answer);

  answer = await get('http://localhost/ledger/?add');
  form = await multipart({ padCsrfToken: token(answer), padForm: 'entry', day: '2026-10-03', amount: 'lots', category: '4', account: '1', note: '' });
  answer = await post('http://localhost/ledger/?add', form.body, { 'content-type': form.type });
  check('ledger: an amount that is no amount comes back with its rule\'s message', /class="error"/.test(text(answer)) && /Amount/.test(text(answer)), answer);

}

// ---- kanban: a move posted as JSON, the columns as a fragment, the event stream ---------

if (!only || only === 'kanban') {

  let answer = await get('http://localhost/kanban/');
  const meta = (text(answer).match(/<meta name="csrf-token" content="([0-9a-f]+)">/) || [])[1];
  const version = Number((text(answer).match(/data-version="(\d+)"/) || [])[1]);
  check('kanban: the board, its nine cards in four columns', (text(answer).match(/<article class="card/g) || []).length === 9 && version === 5, answer);

  answer = await get('http://localhost/kanban/?events', { accept: 'text/event-stream' });
  check('kanban: the event stream answers one round in the browser - retry, then the board', answer.status === 200 && /^text\/event-stream/.test(answer.headers['content-type'][0])
        && /^retry: 2000\n\nevent: board\nid: 5\ndata: \{"version":5/.test(text(answer)), answer);

  answer = await post('http://localhost/kanban/?move&padFormat=json', JSON.stringify({ card: 3, column: 2 }),
                      { 'content-type': 'application/json', accept: 'application/json', 'x-csrf-token': meta });
  check('kanban: a move posted as JSON - the new version and the counts back', json(answer).ok === true && json(answer).version === 6 && json(answer).counts['2'] === 3, answer);

  answer = await get('http://localhost/kanban/?events', { accept: 'text/event-stream', 'last-event-id': '5' });
  check('kanban: ... which the stream tells a window that had version 5', /event: board\nid: 6\n/.test(text(answer)) && /went to Doing/.test(text(answer)), answer);

  answer = await get('http://localhost/kanban/?events', { accept: 'text/event-stream', 'last-event-id': '6' });
  check('kanban: ... and a window that has it nothing', answer.status === 200 && text(answer).trim() === 'retry: 2000', answer);

  answer = await get('http://localhost/kanban/?index&padFragment=columns');
  check('kanban: the columns as a fragment, the card in its new column', /data-column="2"[\s\S]*data-card="3"[\s\S]*data-column="3"/.test(text(answer)) && !/<header/.test(text(answer)), answer);

  answer = await post('http://localhost/kanban/?move&padFormat=json', JSON.stringify({ card: 3, column: 1 }), { 'content-type': 'application/json' });
  check('kanban: a move without the token is refused', answer.status === 403, answer);

  answer = await get('http://localhost/kanban/?index');
  answer = await post('http://localhost/kanban/?index', `padCsrfToken=${token(answer)}&padForm=card&title=Print+the+price+labels&label=chore`);
  check('kanban: a new card from the dialog\'s form', (text(answer).match(/<article class="card/g) || []).length === 10 && /Print the price labels/.test(text(answer)), answer);

  answer = await get('http://localhost/kanban/?events');
  check('kanban: the stream asked for as a page says what it is', /<h1>Events<\/h1>/.test(text(answer)), answer);

}

// ---- the runtime: its calls, DATA/ kept, a database exported and imported --------------

if (!only || only === 'runtime') {

  pad.php.writeFile('/pad/verify.php', `<?php
    @mkdir ( '/pad/DATA/verify', 0777, TRUE );
    $pdo = new PDO ( 'sqlite:/pad/DATA/verify/shop.sqlite' );
    $pdo->exec ( "CREATE TABLE IF NOT EXISTS item (id INTEGER PRIMARY KEY AUTOINCREMENT, name TEXT, price REAL, picture BLOB)" );
    $pdo->exec ( "INSERT INTO item (name, price, picture) VALUES ('it''s', 1.5, X'00FF10'), ('two', NULL, NULL)" );
    echo 'made';`);
  await pad.enqueue(async () => { await pad.php.run({ scriptPath: '/pad/verify.php' }); await pad.persist(); });

  let answer = await call('databases');
  const databases = json(answer).databases || [];
  const shop = databases.find(d => d.name === 'verify/shop.sqlite');
  check('runtime: the databases under DATA/, with their tables and rows', shop && shop.tables.some(t => t.name === 'item' && t.rows === 2), answer);

  answer = await call('rows?db=verify/shop.sqlite&table=item');
  check('runtime: the rows of a table', json(answer).total === 2 && json(answer).columns.join() === 'id,name,price,picture', answer);

  answer = await call('query?db=verify/shop.sqlite', 'POST', JSON.stringify({ sql: "UPDATE item SET price = 2 WHERE name = 'two'; SELECT name, price FROM item ORDER BY id" }));
  const results = json(answer).results || [];
  check('runtime: statements in the console, the rows of the last', results.length === 2 && results[0].changes === 1 && results[1].rows[1][1] === 2, answer);

  answer = await call('query?db=verify/shop.sqlite', 'POST', JSON.stringify({ sql: 'SELECT * FROM missing_table' }));
  check('runtime: a failing statement is named', /statement 1: .*no such table/.test(json(answer).error || ''), answer);

  answer = await call('export?db=verify/shop.sqlite&format=sql');
  const dump = text(answer);
  check('runtime: a database exported as SQL', /attachment; filename="shop.sql"/.test(answer.headers['content-disposition'][0])
        && /CREATE TABLE item/.test(dump) && /VALUES\(1,'it''s',1.5,X'00FF10'\)/.test(dump) && /INSERT INTO sqlite_sequence/.test(dump), answer);

  answer = await call('export?db=verify/shop.sqlite&format=sqlite');
  const file = answer.bytes;
  check('runtime: a database exported as the .sqlite file', new TextDecoder().decode(file.subarray(0, 15)) === 'SQLite format 3', answer);

  answer = await call('import?db=verify/copy.sqlite', 'POST', new TextEncoder().encode(dump));
  check('runtime: SQL imported as a new database', json(answer).tables && json(answer).tables.join() === 'item', answer);

  answer = await call('import?db=verify/shop.sqlite', 'POST', new TextEncoder().encode('this is not SQL;'));
  check('runtime: an import that is no database is refused, the old one kept', /did not build a database/.test(json(answer).error || ''), answer);

  answer = await call('rows?db=verify/shop.sqlite&table=item');
  check('runtime: ... and the database is as it was', json(answer).total === 2, answer);

  answer = await call('import?db=verify/back.sqlite', 'POST', file);
  check('runtime: a .sqlite file imported', json(answer).imported === 'verify/back.sqlite', answer);

  answer = await call('backup');
  const zip = answer.bytes;
  check('runtime: a backup of DATA/ as one zip', answer.status === 200 && zip[0] === 0x50 && zip[1] === 0x4b, answer);

  answer = await call('reset?db=verify/copy.sqlite', 'POST');
  check('runtime: a reset takes the database away', !(json(await call('databases')).databases || []).some(d => d.name === 'verify/copy.sqlite'), answer);

  answer = await call('restore', 'POST', zip);
  check('runtime: a backup restored', json(answer).restored >= 3 && (json(await call('databases')).databases || []).some(d => d.name === 'verify/copy.sqlite'), answer);

  answer = await call('rows?db=../etc&table=x');
  check('runtime: a database outside DATA/ is refused', answer.status === 400, answer);

  answer = await call('import?db=verify/x.sqlite');
  check('runtime: an import is a POST', answer.status === 405, answer);

  // The next visit: a new PHP on the same store.

  pad = await start();
  answer = await call('rows?db=verify/back.sqlite&table=item');
  check('runtime: the next visit finds DATA/ as it was left', json(answer).total === 2, answer);

  if (!only) {
    answer = await get('http://localhost/demo/?guestbook');
    check('runtime: ... the guestbook entry too', /Offline/.test(text(answer)), answer);
    answer = await get('http://localhost/notes/?index');
    check('runtime: ... the notes database, the new note in it', /Harbour at night/.test(text(answer)) && (text(answer).match(/<article class="note/g) || []).length === 5, answer);
    answer = await get('http://localhost/notes/?file&id=1');
    check('runtime: ... and its picture', answer.status === 200 && answer.headers['content-type'][0] === 'image/png', answer);
  }

  answer = await call('forget', 'POST');
  check('runtime: forget empties DATA/', (json(await call('files')).files || []).length === 0, answer);

}

console.log(`${failed ? `${failed} of ${ran} steps not` : `all ${ran} steps`} as expected - ${apps.join(', ')} and the runtime on PHP ${pad.phpVersion} (WebAssembly)`);
process.exit(failed ? 1 : 0);
