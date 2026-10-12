#!/usr/bin/env node
// Checks the Claude Code plugin in editors/claude: its manifests say what Claude Code reads,
// and its launcher starts this checkout's servers the way Claude Code starts it - from the
// project directory, ${CLAUDE_PLUGIN_ROOT} anywhere - and answers as servers with nothing to
// offer where there is no PAD checkout. The language server is shown a page of the editors'
// fixture with an error and must publish it. One line on success; ci.sh runs it.
//
//   node editors/claude/test.js

const { spawn } = require('child_process');
const fs = require('fs');
const os = require('os');
const path = require('path');

const home = path.resolve(__dirname, '..', '..');
const plugin = path.join(__dirname, 'pad');
const launch = path.join(plugin, 'bin', 'launch.js');

const failed = [];
let passed = 0;

function expect(name, ok, got) {
    if (ok) passed++;
    else failed.push(name + ' - got ' + JSON.stringify(got).slice(0, 400));
}

function json(file) {
    try { return JSON.parse(fs.readFileSync(file, 'utf8')); } catch (e) { return { error: String(e.message) }; }
}

// The launcher as Claude Code starts it: node, the script, the kind - in a working directory,
// with CLAUDE_PROJECT_DIR naming the project.
function start(kind, project) {
    const env = Object.assign({}, process.env, { CLAUDE_PROJECT_DIR: project });
    delete env.PAD_HOME;
    delete env.PAD_APPS;
    return spawn(process.execPath, [launch, kind], { cwd: project, env });
}

function lsp(child) {
    let buffer = Buffer.alloc(0);
    const waiting = new Map();
    const notes = [];
    child.stdout.on('data', (chunk) => {
        buffer = Buffer.concat([buffer, chunk]);
        for (;;) {
            const end = buffer.indexOf('\r\n\r\n');
            if (end < 0) return;
            const length = +(buffer.slice(0, end).toString().match(/Content-Length: *(\d+)/i) || [0, 0])[1];
            if (buffer.length < end + 4 + length) return;
            const msg = JSON.parse(buffer.slice(end + 4, end + 4 + length).toString());
            buffer = buffer.slice(end + 4 + length);
            if (msg.id !== undefined && waiting.has(msg.id)) { waiting.get(msg.id)(msg); waiting.delete(msg.id); }
            else if (msg.method) notes.push(msg);
        }
    });
    let id = 0;
    const write = (msg) => {
        const body = Buffer.from(JSON.stringify(msg));
        child.stdin.write('Content-Length: ' + body.length + '\r\n\r\n');
        child.stdin.write(body);
    };
    return {
        notes,
        request: (method, params) => new Promise((resolve) => { const n = ++id; waiting.set(n, resolve); write({ jsonrpc: '2.0', id: n, method, params }); }),
        notify: (method, params) => write({ jsonrpc: '2.0', method, params }),
    };
}

function mcp(child) {
    let buffer = '';
    const waiting = new Map();
    child.stdout.setEncoding('utf8');
    child.stdout.on('data', (chunk) => {
        buffer += chunk;
        let nl;
        while ((nl = buffer.indexOf('\n')) >= 0) {
            const msg = JSON.parse(buffer.slice(0, nl));
            buffer = buffer.slice(nl + 1);
            if (waiting.has(msg.id)) { waiting.get(msg.id)(msg); waiting.delete(msg.id); }
        }
    });
    let id = 0;
    return {
        request: (method, params) => new Promise((resolve) => {
            const n = ++id;
            waiting.set(n, resolve);
            child.stdin.write(JSON.stringify({ jsonrpc: '2.0', id: n, method, params }) + '\n');
        }),
    };
}

async function until(test, ms = 20000) {
    for (let waited = 0; waited < ms; waited += 50) {
        if (test()) return true;
        await new Promise((resolve) => setTimeout(resolve, 50));
    }
    return false;
}

async function main() {
    const market = json(path.join(__dirname, '.claude-plugin', 'marketplace.json'));
    expect('the marketplace lists the plugin pad from ./pad', Array.isArray(market.plugins) && market.plugins.some((p) => p.name === 'pad' && p.source === './pad'), market);
    expect('the marketplace has an owner', market.owner && market.owner.name, market);
    const manifest = json(path.join(plugin, '.claude-plugin', 'plugin.json'));
    expect('the plugin is named pad', manifest.name === 'pad' && /^\d+\.\d+\.\d+$/.test(manifest.version), manifest);
    const lspConfig = json(path.join(plugin, '.lsp.json'));
    expect('the language server takes .pad and .html, started by the launcher',
        lspConfig.pad && lspConfig.pad.extensionToLanguage['.pad'] && lspConfig.pad.extensionToLanguage['.html']
        && lspConfig.pad.args.join(' ') === '${CLAUDE_PLUGIN_ROOT}/bin/launch.js lsp', lspConfig);
    const mcpConfig = json(path.join(plugin, '.mcp.json'));
    expect('the MCP server is started by the launcher', mcpConfig.mcpServers && mcpConfig.mcpServers.pad
        && mcpConfig.mcpServers.pad.args.join(' ') === '${CLAUDE_PLUGIN_ROOT}/bin/launch.js mcp', mcpConfig);
    const skill = fs.readFileSync(path.join(plugin, 'skills', 'pad-bug-hunt', 'SKILL.md'), 'utf8');
    expect('the skill has a name and a description', /^---\nname: pad-bug-hunt\ndescription: \S/.test(skill), skill.slice(0, 120));
    for (const tool of (skill.match(/`(pad_[a-z]+)`/g) || []).map((t) => t.slice(1, -1)))
        expect('the skill names a tool the server has: ' + tool, fs.readFileSync(path.join(home, 'editors', 'mcp', 'pad-mcp.js'), 'utf8').includes(`name: '${tool}'`), tool);

    const children = [];
    const elsewhere = fs.mkdtempSync(path.join(os.tmpdir(), 'pad-claude-'));
    try {
        // in the checkout: this checkout's language server
        const a = start('lsp', home);
        children.push(a);
        const l = lsp(a);
        const init = await l.request('initialize', { processId: process.pid, rootUri: 'file://' + home, capabilities: {} });
        expect('in the checkout the launcher runs the PAD language server', init.result && init.result.serverInfo && init.result.serverInfo.name === 'pad-lsp' && init.result.capabilities.hoverProvider, init);
        l.notify('initialized', {});
        const broken = path.join(home, 'editors', 'fixture', 'apps', 'shop', 'broken.pad');
        const uri = 'file://' + broken;
        l.notify('textDocument/didOpen', { textDocument: { uri, languageId: 'pad', version: 1, text: fs.readFileSync(broken, 'utf8') } });
        const published = () => l.notes.find((n) => n.method === 'textDocument/publishDiagnostics' && n.params.uri === uri);
        await until(published);
        const diag = published();
        expect('a page opened as Claude Code opens it after an edit gets the engine\'s error', diag && diag.params.diagnostics.length === 1 && /never closes/.test(diag.params.diagnostics[0].message), diag);

        // in the checkout: this checkout's MCP server
        const b = start('mcp', home);
        children.push(b);
        const m = mcp(b);
        const mi = await m.request('initialize', { protocolVersion: '2025-06-18', capabilities: {} });
        expect('in the checkout the launcher runs the PAD MCP server', mi.result && mi.result.serverInfo.name === 'pad-mcp' && /pad_compare/.test(mi.result.instructions), mi);
        const tools = await m.request('tools/list');
        expect('the MCP server offers the hunting tools', ['pad_render', 'pad_php', 'pad_compare', 'pad_case', 'pad_test'].every((t) => tools.result.tools.some((x) => x.name === t)), tools.result && tools.result.tools.map((x) => x.name));

        // no PAD checkout: servers that offer nothing
        const c = start('lsp', elsewhere);
        children.push(c);
        const ci = await lsp(c).request('initialize', { processId: process.pid, capabilities: {} });
        expect('outside a checkout the language server answers with no capabilities', ci.result && Object.keys(ci.result.capabilities).length === 0, ci);
        const d = start('mcp', elsewhere);
        children.push(d);
        const dm = mcp(d);
        const di = await dm.request('initialize', { protocolVersion: '2025-06-18', capabilities: {} });
        expect('outside a checkout the MCP server says why it has no tools', di.result && /no PAD checkout/.test(di.result.instructions), di);
        const dt = await dm.request('tools/list');
        expect('outside a checkout the MCP server lists no tools', dt.result && dt.result.tools.length === 0, dt);
    } finally {
        for (const child of children) child.kill();
        fs.rmSync(elsewhere, { recursive: true, force: true });
    }

    if (failed.length) {
        console.error('claude: ' + failed.length + ' of ' + (passed + failed.length) + ' checks failed');
        for (const f of failed) console.error('  ' + f);
        process.exit(1);
    }
    console.log(passed + ' checks passed');
    process.exit(0);
}

setTimeout(() => { console.error('claude: the servers did not answer in time'); process.exit(1); }, 60000).unref();
main().catch((e) => { console.error('claude: ' + (e && e.stack || e)); process.exit(1); });
