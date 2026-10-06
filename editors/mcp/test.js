#!/usr/bin/env node
// Speaks MCP to pad-mcp.js over stdio, as Claude Code would - initialize, tools/list,
// tools/call - with the applications directory pointed at the fixture editors/fixture/apps.
// pad_test runs ./ci.sh in the doctored world the gate's own test rig uses: a trigger served
// by a php -S of its own, a results directory planted here, a known run token. One line on
// success; every failed check names itself. ci.sh runs it.
//
//   node editors/mcp/test.js

const { spawn, spawnSync } = require('child_process');
const fs = require('fs');
const os = require('os');
const path = require('path');
const net = require('net');

const home = path.resolve(__dirname, '..', '..');
const fixture = path.join(home, 'editors', 'fixture', 'apps');

const failed = [];
let passed = 0;

function expect(name, ok, got) {
    if (ok) passed++;
    else failed.push(name + ' - got ' + JSON.stringify(got).slice(0, 400));
}

// Until the stub server takes connections - php -S needs a moment to start.
async function listening(port) {
    for (let i = 0; i < 100; i++) {
        const up = await new Promise((resolve) => {
            const c = net.connect(port, '127.0.0.1', () => { c.end(); resolve(true); });
            c.on('error', () => resolve(false));
        });
        if (up) return true;
        await new Promise((resolve) => setTimeout(resolve, 50));
    }
    return false;
}

function freePort() {
    return new Promise((resolve) => {
        const s = net.createServer();
        s.listen(0, '127.0.0.1', () => { const p = s.address().port; s.close(() => resolve(p)); });
    });
}

async function main() {
    // the doctored world for pad_test
    const tmp = fs.mkdtempSync(path.join(os.tmpdir(), 'pad-mcp-'));
    const docroot = path.join(tmp, 'www');
    const suites = path.join(tmp, 'suites');
    fs.mkdirSync(docroot);
    fs.mkdirSync(suites);
    fs.writeFileSync(path.join(docroot, 'index.php'), '<?php echo "ok"; ?>');
    const port = await freePort();
    const stub = spawn('php', ['-S', '127.0.0.1:' + port, '-t', docroot], { stdio: 'ignore' });
    const run = 'mcptoken12345';
    const commit = (spawnSync('git', ['-C', home, 'rev-parse', '--short', 'HEAD'], { encoding: 'utf8' }).stdout || '').trim();
    const when = Math.floor(Date.now() / 1000) + 30;
    for (const s of ['pages', 'common', 'errors', 'framework', 'regression', 'sequence', 'manual', 'other']) {
        const tests = s === 'manual'
            ? [{ name: 'pages/broken', status: 'failed', want: 'the answer', got: 'something else' }, { name: 'pages/fine', status: 'ok' }]
            : [{ name: 'one', status: 'ok' }];
        fs.writeFileSync(path.join(suites, s + '.json'), JSON.stringify({
            summary: s === 'manual' ? '2 pages, 2 tests, 1 failed' : '1 pages, 1 tests, 0 failed',
            failed: s === 'manual' ? 1 : 0, new: 0, when, run, commit, tests,
        }));
    }

    const env = Object.assign({}, process.env, {
        PAD_APPS: fixture,
        CI_TRIGGER: `http://127.0.0.1:${port}/?index`,
        CI_SUITES: suites,
        CI_RUN: run,
    });
    delete env.PAD_HOST;

    const server = spawn(process.execPath, [path.join(__dirname, 'pad-mcp.js')], { cwd: home, env });
    let buffer = '';
    let nextId = 1;
    const waiting = new Map();
    server.stdout.setEncoding('utf8');
    server.stdout.on('data', (chunk) => {
        buffer += chunk;
        let nl;
        while ((nl = buffer.indexOf('\n')) >= 0) {
            const msg = JSON.parse(buffer.slice(0, nl));
            buffer = buffer.slice(nl + 1);
            if (waiting.has(msg.id)) { waiting.get(msg.id)(msg); waiting.delete(msg.id); }
        }
    });

    const request = (method, params) => {
        const id = nextId++;
        return new Promise((resolve) => {
            waiting.set(id, resolve);
            server.stdin.write(JSON.stringify({ jsonrpc: '2.0', id, method, params }) + '\n');
        });
    };
    const notify = (method, params) => server.stdin.write(JSON.stringify({ jsonrpc: '2.0', method, params }) + '\n');
    const call = async (name, args) => {
        const r = await request('tools/call', { name, arguments: args });
        return r.result ? { text: r.result.content.map((c) => c.text).join(''), error: r.result.isError } : { text: '', rpc: r.error };
    };

    try {
        const init = await request('initialize', { protocolVersion: '2025-06-18', capabilities: {}, clientInfo: { name: 'test', version: '1' } });
        expect('initialize answers the version asked for', init.result.protocolVersion === '2025-06-18', init.result);
        expect('initialize offers tools', init.result.capabilities.tools !== undefined, init.result);
        expect('initialize names the server', init.result.serverInfo.name === 'pad-mcp', init.result);
        const old = await request('initialize', { protocolVersion: '1999-01-01', capabilities: {} });
        expect('an unknown version gets the newest', /^\d{4}-\d\d-\d\d$/.test(old.result.protocolVersion) && old.result.protocolVersion !== '1999-01-01', old.result);
        notify('notifications/initialized');

        const ping = await request('ping');
        expect('ping answers', ping.result && Object.keys(ping.result).length === 0, ping);

        const list = await request('tools/list');
        const names = list.result.tools.map((t) => t.name);
        for (const n of ['pad_render', 'pad_check', 'pad_trace', 'pad_apps', 'pad_pages', 'pad_builtins', 'pad_reference', 'pad_test'])
            expect('tools/list has ' + n, names.includes(n), names);
        expect('every tool has an object input schema', list.result.tools.every((t) => t.inputSchema && t.inputSchema.type === 'object' && t.description), list.result.tools);

        let r = await call('pad_render', { app: 'shop', page: 'orders' });
        expect('pad_render renders a page with its data', !r.error && r.text.includes('Orders for ALICE') && r.text.includes('$42.00'), r);
        r = await call('pad_render', { app: 'shop', template: '<b>{badge}</b> {echo 3 | money}' });
        expect('pad_render renders a template with the application\'s tags and functions', !r.error && r.text.trim() === '<b>shop</b> $3.00', r);
        r = await call('pad_render', { app: 'shop', page: 'orders', template: '{$customer} {$total}' });
        expect('pad_render renders a template with a page\'s data', !r.error && r.text.trim() === 'Alice 42', r);
        r = await call('pad_render', { app: 'shop', page: 'admin/new', template: '{badge}' });
        expect('a template in a subdirectory uses that directory\'s _tags', !r.error && r.text.trim() === '<b>admin</b>', r);
        r = await call('pad_render', { app: 'shop', template: '{$id}', query: 'id=7' });
        expect('pad_render passes request values', !r.error && r.text.trim() === '7', r);
        r = await call('pad_render', { app: 'shop', template: 'a\n{if 1 eq 1}never closed' });
        expect('pad_render reports a PAD error', r.error && r.text.includes('never closes') && r.text.includes('tag: {if 1 eq 1}, line 2'), r);
        r = await call('pad_render', { app: 'nosuchapp' });
        expect('pad_render refuses an unknown application', r.error && r.text.includes('no application'), r);
        r = await call('pad_render', { app: '../etc' });
        expect('pad_render refuses a path for an application', r.error && r.text.includes('not an application name'), r);

        r = await call('pad_check', { app: 'shop', template: '<p>{$customer}</p>' });
        expect('pad_check names an undefined field and the variables there are', !r.error && r.text.startsWith('error') && r.text.includes("'$customer'") && r.text.includes('variables: none'), r);
        r = await call('pad_check', { app: 'shop', page: 'orders', template: '<p>{$customer}</p>' });
        expect('pad_check passes a template the page\'s data satisfies', !r.error && r.text.startsWith('ok'), r);
        r = await call('pad_check', { app: 'shop', page: 'broken' });
        expect('pad_check places an error on its line in the page', r.text.includes('never closes') && r.text.includes('line 3'), r);
        r = await call('pad_check', { app: 'shop', page: 'twice' });
        expect('pad_check names the line the engine places the error on, not an earlier tag of the same text', r.text.includes('tag: {if 1 eq 1}, line 3 of editors/fixture/apps/shop/twice.pad'), r);
        r = await call('pad_check', { app: 'shop' });
        expect('pad_check of an application checks every page', r.text.startsWith('6 pages of shop checked: 3 with an error') && r.text.includes('broken') && r.text.includes('undefined'), r);
        expect('pad_check of an application leaves the bracketed route products/[id] out', !r.error && !r.text.includes('[id]'), r);

        r = await call('pad_trace', { app: 'shop', page: 'orders' });
        expect('pad_trace returns the trace of the request', !r.error && r.text.includes('trace: DATA/trace/orders/') && /level\s+start\s+\{orders callback='double'\}/.test(r.text), r);

        r = await call('pad_apps', {});
        expect('pad_apps lists the applications', r.text.split('\n').includes('shop'), r);
        r = await call('pad_pages', { app: 'shop' });
        expect('pad_pages lists the pages', r.text.includes('pages: admin/report, broken, index, orders, twice, undefined'), r);
        expect('pad_pages lists the application\'s own tags by directory', r.text.includes('_tags: badge') && r.text.includes('admin/_tags: badge') && r.text.includes('_functions: money'), r);

        r = await call('pad_builtins', { kind: 'tags' });
        expect('pad_builtins lists the tags with their reference line', /^if - .+/m.test(r.text) && r.text.includes('## tags'), r);
        r = await call('pad_builtins', {});
        expect('pad_builtins lists every kind', ['tags', 'functions', 'options', 'properties', 'prefixes', 'sequences', 'operators'].every((k) => r.text.includes('## ' + k)), r);

        r = await call('pad_reference', { name: 'echo', kind: 'tag' });
        expect('pad_reference finds a tag\'s section', r.text.includes('### echo') && r.text.includes('TAGS.md'), r);
        r = await call('pad_reference', { name: '| upper' });
        expect('pad_reference finds a function\'s row', r.text.includes('`upper`') && r.text.includes('FUNCTIONS.md'), r);
        r = await call('pad_reference', { name: 'uppr' });
        expect('pad_reference says when there is nothing', r.text.startsWith("nothing in docs/reference for 'uppr'"), r);
        r = await call('pad_reference', { file: 'reference/FUNCTIONS.md' });
        expect('pad_reference reads a whole file', r.text.startsWith('# PAD Functions Reference'), r);
        r = await call('pad_reference', { file: '../ci.sh' });
        expect('pad_reference stays inside docs/', r.error, r);
        r = await call('pad_reference', {});
        expect('pad_reference lists the files', r.text.includes('reference/TAGS.md - PAD Tags Reference'), r);

        expect('the stub server for pad_test is up', await listening(port), port);
        r = await call('pad_test', {});
        expect('pad_test reports a failed run', !r.error && r.text.startsWith('FAILED') && r.text.includes('manual       2 pages, 2 tests, 1 failed'), r);
        expect('pad_test lists the failing test with what was wanted', r.text.includes('manual: pages/broken (failed)') && r.text.includes('want: the answer') && r.text.includes('got:  something else'), r);

        // a php that ends without reading the template it is handed - one that fails to start,
        // a wrong binary - closes the pipe under a large write; the server answers the call
        // with the failure and stays up for the next one
        if (fs.existsSync('/usr/bin/false')) {
            const quick = spawn(process.execPath, [path.join(__dirname, 'pad-mcp.js')], { cwd: home, env: Object.assign({}, env, { PAD_PHP: '/usr/bin/false' }) });
            let said = '';
            quick.stdout.setEncoding('utf8');
            quick.stdout.on('data', (d) => { said += d; });
            const ended = new Promise((resolve) => quick.on('exit', (code) => resolve('exit ' + code)));
            quick.stdin.on('error', () => {});
            quick.stdin.write(JSON.stringify({ jsonrpc: '2.0', id: 1, method: 'tools/call', params: { name: 'pad_render', arguments: { app: 'shop', template: 'x'.repeat(2000000) } } }) + '\n');
            const answered = () => said.includes('"id":1') && said.includes('"id":2');
            for (let i = 0; i < 100 && !answered(); i++) {
                if (i === 10) quick.stdin.write(JSON.stringify({ jsonrpc: '2.0', id: 2, method: 'ping' }) + '\n');
                const end = await Promise.race([ended, new Promise((resolve) => setTimeout(() => resolve(null), 50))]);
                if (end) { said += ' [' + end + ']'; break; }
            }
            expect('a php that does not read the template leaves the server answering', answered() && said.includes('"isError":true'), said.slice(-300));
            quick.kill();
        }

        const bad = await call('pad_nothing', {});
        expect('an unknown tool is a protocol error', bad.rpc && bad.rpc.code === -32602, bad);
        const unknown = await request('resources/list');
        expect('an unknown method is a protocol error', unknown.error && unknown.error.code === -32601, unknown);
    } finally {
        server.stdin.end();
        stub.kill();
        fs.rmSync(tmp, { recursive: true, force: true });
    }

    if (failed.length) {
        console.error('mcp: ' + failed.length + ' of ' + (passed + failed.length) + ' checks failed');
        for (const f of failed) console.error('  ' + f);
        process.exit(1);
    }
    console.log(passed + ' checks passed');
    process.exit(0);
}

setTimeout(() => { console.error('mcp: the server did not answer in time'); process.exit(1); }, 120000).unref();
main().catch((e) => { console.error('mcp: ' + (e && e.stack || e)); process.exit(1); });
