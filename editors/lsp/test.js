#!/usr/bin/env node
// Speaks LSP to pad-lsp.js over stdio, as an editor would, against the fixture application
// editors/fixture/apps/shop: completion, hover from docs/reference, go-to-definition along
// PAD's lookup order, and diagnostics from a real render under the strict syntax check.
// One line on success; every failed check names itself. ci.sh runs it.
//
//   node editors/lsp/test.js

const { spawn } = require('child_process');
const path = require('path');
const fs = require('fs');
const url = require('url');

const home = path.resolve(__dirname, '..', '..');
const shop = path.join(home, 'editors', 'fixture', 'apps', 'shop');
const uri = (rel) => url.pathToFileURL(path.join(shop, rel)).href;
const text = (rel) => fs.readFileSync(path.join(shop, rel), 'utf8');

const server = spawn(process.execPath, [path.join(__dirname, 'pad-lsp.js')], { cwd: home });
let buffer = Buffer.alloc(0);
let nextId = 1;
const waiting = new Map();
const diagnostics = new Map();
const diagnosticWaiters = [];

// A server that dies answers every request - waiting or still to come - with how it ended,
// so a check fails on the spot instead of waiting out the time limit.
let exited;
server.on('exit', (code) => {
    exited = code;
    for (const [id, resolve] of waiting) resolve({ id, exited: code });
    waiting.clear();
});
server.stdin.on('error', () => {});

server.stdout.on('data', (chunk) => {
    buffer = Buffer.concat([buffer, chunk]);
    for (;;) {
        const end = buffer.indexOf('\r\n\r\n');
        if (end < 0) return;
        const length = parseInt(buffer.slice(0, end).toString().match(/Content-Length: *(\d+)/i)[1], 10);
        if (buffer.length < end + 4 + length) return;
        const msg = JSON.parse(buffer.slice(end + 4, end + 4 + length).toString('utf8'));
        buffer = buffer.slice(end + 4 + length);
        if (msg.id !== undefined && waiting.has(msg.id)) {
            waiting.get(msg.id)(msg);
            waiting.delete(msg.id);
        } else if (msg.method === 'textDocument/publishDiagnostics') {
            diagnostics.set(msg.params.uri, msg.params.diagnostics);
            for (const w of diagnosticWaiters.splice(0)) w();
        }
    }
});

function write(msg) {
    const body = Buffer.from(JSON.stringify(Object.assign({ jsonrpc: '2.0' }, msg)), 'utf8');
    server.stdin.write('Content-Length: ' + body.length + '\r\n\r\n');
    server.stdin.write(body);
}

function request(method, params) {
    const id = nextId++;
    if (exited !== undefined) return Promise.resolve({ id, exited });
    return new Promise((resolve) => { waiting.set(id, resolve); write({ id, method, params }); });
}

function notify(method, params) { write({ method, params }); }

async function diagnosticsFor(u, ms = 20000) {
    const until = Date.now() + ms;
    while (!diagnostics.has(u) && Date.now() < until)
        await new Promise((resolve) => { diagnosticWaiters.push(resolve); setTimeout(resolve, 200); });
    return diagnostics.get(u);
}

function open(rel) {
    notify('textDocument/didOpen', { textDocument: { uri: uri(rel), languageId: 'pad', version: 1, text: text(rel) } });
}

// The position of the n-th occurrence of needle in a fixture file, plus an offset into it.
function at(rel, needle, offset = 0, nth = 1) {
    const t = text(rel);
    let i = -1;
    for (let k = 0; k < nth; k++) i = t.indexOf(needle, i + 1);
    if (i < 0) throw new Error(`'${needle}' is not in ${rel}`);
    i += offset;
    const before = t.slice(0, i);
    return { line: before.split('\n').length - 1, character: i - before.lastIndexOf('\n') - 1 };
}

const failed = [];
let passed = 0;

function expect(name, ok, got) {
    if (ok) passed++;
    else failed.push(name + ' - got ' + JSON.stringify(got).slice(0, 300));
}

async function hover(rel, needle, offset) {
    const r = await request('textDocument/hover', { textDocument: { uri: uri(rel) }, position: at(rel, needle, offset) });
    return r.result ? r.result.contents.value : '';
}

async function definition(rel, needle, offset) {
    const r = await request('textDocument/definition', { textDocument: { uri: uri(rel) }, position: at(rel, needle, offset) });
    return (r.result || []).map((l) => path.relative(home, url.fileURLToPath(l.uri)).split(path.sep).join('/') + ':' + (l.range.start.line + 1));
}

async function main() {
    const init = await request('initialize', { processId: null, rootUri: null, capabilities: {} });
    const caps = init.result.capabilities;
    expect('initialize offers hover', caps.hoverProvider === true, caps);
    expect('initialize offers definition', caps.definitionProvider === true, caps);
    expect('initialize asks for saves', caps.textDocumentSync.save !== undefined, caps);
    notify('initialized', {});

    // a body that is JSON but no message - null, a number, a string - is passed over, and
    // what comes after it is answered
    for (const junk of ['null', '5', '"x"']) {
        const body = Buffer.from(junk, 'utf8');
        server.stdin.write('Content-Length: ' + body.length + '\r\n\r\n');
        server.stdin.write(body);
    }
    const alive = await request('textDocument/hover', { textDocument: { uri: uri('orders.pad') }, position: { line: 0, character: 0 } });
    if (alive.exited !== undefined) throw new Error('the language server died on a message that is no object (exit ' + alive.exited + ')');
    expect('a message that is no object leaves the server answering', 'result' in alive, alive);

    for (const f of ['orders.pad', 'admin/report.pad', 'broken.pad', 'undefined.pad', '_include/footer.pad', 'products/[id].pad', 'twice.pad', 'refused.pad']) open(f);

    // completion, as before
    let r = await request('textDocument/completion', { textDocument: { uri: uri('orders.pad') }, position: at('orders.pad', '{badge}', 2) });
    expect('completion lists the built-in tags', r.result.some((c) => c.label === 'if'), r.result.length);
    r = await request('textDocument/completion', { textDocument: { uri: uri('orders.pad') }, position: at('orders.pad', '{/orders}', 2) });
    expect('completion closes the open tag', r.result.length && r.result[0].label === 'orders', r.result);

    // what is not an open pair stays off the close-tag list: a branch word, a self-closed
    // tag, a tag inside a comment
    const branchUri = uri('orders.pad').replace('orders.pad', 'branches.pad');
    const branchText = "{orders}\n{if $x}a{else}b{# {foo} #}{-- {baz} --}{bar /}\n{/";
    notify('textDocument/didOpen', { textDocument: { uri: branchUri, languageId: 'pad', version: 1, text: branchText } });
    r = await request('textDocument/completion', { textDocument: { uri: branchUri }, position: { line: 2, character: 2 } });
    const closers = (r.result || []).map((c) => c.label);
    expect('close-tag completion skips else, self-closed and commented tags', closers.join(',') === 'if,orders', closers);

    // whitespace control: {~items~} opens a pair and {~/orders~} closes one, the ~ just
    // inside the brace taking the whitespace on that side
    const tildeUri = uri('orders.pad').replace('orders.pad', 'tilde.pad');
    notify('textDocument/didOpen', { textDocument: { uri: tildeUri, languageId: 'pad', version: 1, text: "{~items~}\n{orders}\n{~/orders~}\n{~/" } });
    r = await request('textDocument/completion', { textDocument: { uri: tildeUri }, position: { line: 3, character: 3 } });
    const tildeClosers = (r.result || []).map((c) => c.label);
    expect('close-tag completion reads the tags written with whitespace control', tildeClosers.join(',') === 'items', tildeClosers);

    // hover from docs/reference, and from the application's own files
    let h = await hover('orders.pad', '{echo', 2);
    expect('hover on a tag shows its TAGS.md section', h.includes('### echo') && h.includes('TAGS.md'), h);
    h = await hover('orders.pad', '| upper', 3);
    expect('hover on a pipe function shows its FUNCTIONS.md row', h.includes('`upper`') && h.includes('FUNCTIONS.md'), h);
    h = await hover('orders.pad', 'callback=', 2);
    expect('hover on an option shows its OPTIONS.md section', h.includes('### callback') && h.includes('OPTIONS.md'), h);
    h = await hover('orders.pad', 'first@orders', 1);
    expect('hover on a property shows its PROPERTIES.md section', h.includes('### first') && h.includes('PROPERTIES.md'), h);
    h = await hover('orders.pad', 'include:footer', 9);
    expect('hover on an include names its file', h.includes('include `editors/fixture/apps/shop/_include/footer.pad`'), h);
    h = await hover('orders.pad', '{badge}', 2);
    expect('hover on an application tag names its file', h.includes('application tag `editors/fixture/apps/shop/_tags/badge.php`'), h);
    h = await hover('orders.pad', 'echo $total', 6);
    expect('hover on a field shows the line that assigns it', h.includes('orders.php:10') && h.includes('$total = 42;'), h);
    h = await hover('orders.pad', '$total gt', 7);
    expect('hover on an operator says nothing', h === '', h);

    // go-to-definition along PAD's lookup order
    let d = await definition('orders.pad', 'echo $total', 6);
    expect('a field goes to the paired .php line that assigns it', d[0] === 'editors/fixture/apps/shop/orders.php:10', d);
    d = await definition('orders.pad', '{orders', 3);
    expect('a data tag goes to the .php line that sets its array', d[0] === 'editors/fixture/apps/shop/orders.php:5', d);
    d = await definition('orders.pad', '{badge}', 2);
    expect('a tag goes to the nearest _tags file', d.length === 1 && d[0] === 'editors/fixture/apps/shop/_tags/badge.php:1', d);
    d = await definition('admin/report.pad', '{badge}', 2);
    expect('a subdirectory _tags file wins over the root one', d.length === 1 && d[0] === 'editors/fixture/apps/shop/admin/_tags/badge.pad:1', d);
    d = await definition('admin/report.pad', 'money', 1);
    expect('a pipe function is found up the directories', d[0] === 'editors/fixture/apps/shop/_functions/money.php:1', d);
    d = await definition('orders.pad', 'include:footer', 9);
    expect('include: goes to the _include file', d[0] === 'editors/fixture/apps/shop/_include/footer.pad:1', d);
    d = await definition('orders.pad', "'double'", 2);
    expect('a callback goes to its _callbacks file', d[0] === 'editors/fixture/apps/shop/_callbacks/double.php:1', d);
    d = await definition('orders.pad', '{echo', 2);
    expect('a built-in tag goes to the engine', d[0] === 'pad/tags/echo.php:1', d);
    d = await definition('orders.pad', '| upper', 3);
    expect('a built-in function goes to the engine', d[0] === 'pad/functions/upper.php:1', d);
    d = await definition('_include/footer.pad', '$customer', 2);
    expect('a field in an include finds no page of its own', d.length === 0, d);

    // diagnostics: a real render under the strict syntax check
    let g = await diagnosticsFor(uri('broken.pad'));
    expect('an unclosed pair is reported', g && g.length === 1 && g[0].message.includes('never closes'), g);
    expect('the error sits on the tag it names', g && g[0] && g[0].range.start.line === 2 && g[0].range.start.character === 2, g);
    g = await diagnosticsFor(uri('undefined.pad'));
    expect('an undefined field is reported on the field', g && g[0] && g[0].message.includes("'$customer'") && g[0].range.start.line === 0, g);
    g = await diagnosticsFor(uri('orders.pad'));
    expect('a page that renders has no diagnostics', g && g.length === 0, g);
    g = await diagnosticsFor(uri('_include/footer.pad'));
    expect('a snippet is not rendered on its own', g && g.length === 0, g);
    g = await diagnosticsFor(uri('products/[id].pad'));
    expect('a bracketed route is not rendered by its own name', g && g.length === 0, g);
    g = await diagnosticsFor(uri('refused.pad'));
    expect('an error an application tag raised is placed on the tag and names the tag file and line',
        g && g[0] && g[0].message === 'refuse refuses - _tags/refuse.php:3' && g[0].range.start.line === 1, g);
    g = await diagnosticsFor(uri('twice.pad'));
    expect('the error sits where the engine places it, not on an earlier tag of the same text, its column in UTF-16 units past an emoji',
        g && g[0] && g[0].message.includes('never closes') && g[0].range.start.line === 2 && g[0].range.start.character === 10 && g[0].range.end.character === 21, g);

    diagnostics.delete(uri('orders.pad'));
    notify('textDocument/didSave', { textDocument: { uri: uri('orders.pad') } });
    g = await diagnosticsFor(uri('orders.pad'));
    expect('a save checks the page again', g && g.length === 0, g);

    await request('shutdown', null);
    notify('exit', null);

    if (failed.length) {
        console.error('lsp: ' + failed.length + ' of ' + (passed + failed.length) + ' checks failed');
        for (const f of failed) console.error('  ' + f);
        process.exit(1);
    }
    console.log(passed + ' checks passed');
    process.exit(0);
}

setTimeout(() => { console.error('lsp: the language server did not answer in time'); process.exit(1); }, 60000).unref();
main().catch((e) => { console.error('lsp: ' + (e && e.stack || e)); process.exit(1); });
