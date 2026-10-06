#!/usr/bin/env node
// PAD MCP server (Model Context Protocol, stdio, no dependencies): lets an AI assistant
// check PAD against the engine itself instead of against what it remembers of CLAUDE.md.
//
// pad_render     render a page, or a template given as text, through PAD on the command
//                line (editors/render.php) - the output, or the PAD error with its tag and line
// pad_check      the strict syntax check of a template, a page, or every page of an app
// pad_trace      render with the execution trace on and read it back (DATA/trace/)
// pad_apps       the applications, with the first line of their README
// pad_pages      an application's pages, and its own _tags, _functions, _include, ...
// pad_builtins   the built-in tags, functions, options, properties, type prefixes, sequence
//                types and operators - the directories editors/generate.php reads
// pad_reference  a tag, function, option, property or prefix in docs/reference, or a doc file
// pad_test       run ./ci.sh - every regression suite - and read back what failed
//
// The protocol is JSON-RPC 2.0, one message per line on stdin and stdout; logs go to stderr.
// Register it with Claude Code:  claude mcp add pad -- node /path/to/pad/editors/mcp/pad-mcp.js
//
// PAD_APPS points the tools at another applications directory (the tests use the fixture in
// editors/fixture/apps); PAD_PHP names the php binary; PAD_HOST is pad_test's default host.

const fs = require('fs');
const path = require('path');
const { spawn } = require('child_process');

const reference = require(path.join(__dirname, '..', 'reference.js'));

const home = path.resolve(__dirname, '..', '..');
const appsDir = process.env.PAD_APPS ? path.resolve(process.env.PAD_APPS) : path.join(home, 'apps');
const php = process.env.PAD_PHP || 'php';
const VERSION = '0.1.0';
const PROTOCOLS = ['2025-11-25', '2025-06-18', '2025-03-26', '2024-11-05'];
const LIMIT = 60000;               // characters of one answer
const VIRTUAL = 'mcpTemplate';     // the page name a template without a page renders as

// ---------------- helpers ----------------

function rel(file) {
    return path.relative(home, file).split(path.sep).join('/');
}

function readText(file) {
    try { return fs.readFileSync(file, 'utf8'); } catch (e) { return ''; }
}

function truncate(text, limit = LIMIT) {
    return text.length > limit ? text.slice(0, limit) + `\n... (${text.length - limit} more characters)` : text;
}

function isDir(dir) {
    try { return fs.statSync(dir).isDirectory(); } catch (e) { return false; }
}

// An application name as a URL carries it - no way out of the applications directory.
function appPath(app) {
    if (typeof app !== 'string' || !/^[A-Za-z0-9][A-Za-z0-9_/-]*$/.test(app) || app.includes('//'))
        throw new Error(`'${app}' is not an application name`);
    const dir = path.join(appsDir, app.replace(/\/+$/, ''));
    if (!isDir(dir)) throw new Error(`there is no application '${app}' in ${rel(appsDir) || appsDir}`);
    return dir;
}

function checkPage(page) {
    if (page !== undefined && (typeof page !== 'string' || !/^[A-Za-z0-9][A-Za-z0-9_/-]*$/.test(page) || page.includes('/_')))
        throw new Error(`'${page}' is not a page name - ?orders, ?admin/users`);
}

// One request through editors/render.php, in its own php process.
function render({ app, page, template, query, trace }) {
    appPath(app);
    checkPage(page);
    const args = [path.join(home, 'editors', 'render.php'), app, page || (template !== undefined ? VIRTUAL : 'index'), '--apps=' + appsDir];
    if (template !== undefined) args.push('--source=-');
    if (query) args.push('--query=' + String(query).replace(/^\?/, ''));
    if (trace) args.push('--trace');
    return new Promise((resolve) => {
        const child = spawn(php, args, { cwd: home });
        let out = '', err = '';
        const timer = setTimeout(() => child.kill('SIGKILL'), 60000);
        child.stdout.on('data', (d) => { out += d; });
        child.stderr.on('data', (d) => { err += d; });
        child.on('error', (e) => { clearTimeout(timer); resolve({ code: -1, out: '', err: String(e.message) }); });
        child.on('close', (code) => { clearTimeout(timer); resolve({ code, out, err }); });
        child.stdin.end(template !== undefined ? String(template) : '');
    });
}

// What a failed render says, for an assistant: the message, the tag it is about and its
// line in the template, where PHP raised it, and the variables the page had - not the
// whole JSON body of pad/error/claude.php, which carries every global.
function describe(result, source) {
    let json = null;
    try { json = JSON.parse(result.out); } catch (e) { json = null; }
    if (!json || typeof json.error !== 'string') {
        const text = (result.out || result.err || 'the render failed').replace(/<[^>]*>/g, ' ').replace(/[ \t]+/g, ' ').trim();
        return truncate(text, 3000);
    }
    const g = json.pad || {};
    const lines = ['PAD error: ' + json.error.replace(/^PAD:\s*/, '').trim()];
    const tag = (typeof g.padBetweenOrg === 'string' && g.padBetweenOrg) || '';
    if (tag) {
        let where = '';
        const at = source ? source.indexOf('{' + tag) : -1;
        if (at >= 0) where = ', line ' + (source.slice(0, at).split('\n').length);
        lines.push('tag: {' + tag + '}' + where);
    }
    if (g.padPage && g.padPage !== VIRTUAL) lines.push('page: ' + g.padPage);
    if (json.file) lines.push('raised in: ' + rel(json.file) + ':' + json.line);
    const vars = Object.keys(json.app || {}).filter((k) => k !== 'argv' && k !== 'argc');
    lines.push('variables: ' + (vars.length ? vars.map((v) => '$' + v).join(', ') : 'none'));
    return lines.join('\n');
}

// The page's own template, for placing an error on its line.
function pageSource(app, page) {
    const base = path.join(appPath(app), ...(page || 'index').split('/'));
    return readText(base + '.pad') || readText(base + '.html');
}

// The templates of an application that are pages: .pad and .html outside the _xxx
// directories, each name once. A bracketed route - products/[id].pad, blog/[year]/ - is
// reached through a path that fills in its value, never by its own name, so it is no page
// to ask for: pad lint and pad export leave it out the same way, and checkPage refused its
// name, which ended pad_check of a whole application that has one - the manual - on it.
function pagesOf(dir) {
    const out = new Set();
    const walk = (d, prefix) => {
        let list = [];
        try { list = fs.readdirSync(d).sort(); } catch (e) { return; }
        for (const n of list) {
            if (n[0] === '_' || n[0] === '.' || n.includes('[')) continue;
            const full = path.join(d, n);
            if (isDir(full)) walk(full, prefix + n + '/');
            else if (/\.(pad|html)$/.test(n)) out.add(prefix + n.replace(/\.(pad|html)$/, ''));
        }
    };
    walk(dir, '');
    return [...out];
}

// The application's own extensions: every _tags, _functions, _include, _callbacks, _data,
// _lib and _scripts directory, by the directory it sits in.
function ownOf(dir) {
    const out = [];
    const kinds = ['_tags', '_functions', '_include', '_callbacks', '_data', '_lib', '_scripts'];
    const walk = (d, prefix) => {
        let list = [];
        try { list = fs.readdirSync(d).sort(); } catch (e) { return; }
        for (const n of list) {
            const full = path.join(d, n);
            if (!isDir(full) || n[0] === '.') continue;
            if (kinds.includes(n)) {
                const files = fs.readdirSync(full).filter((f) => f[0] !== '.').sort();
                const shown = n === '_data' || n === '_scripts' ? files : [...new Set(files.map((f) => f.replace(/\.[^.]+$/, '')))];
                if (shown.length) out.push(`${prefix}${n}: ${shown.join(', ')}`);
            } else if (n[0] !== '_') walk(full, prefix + n + '/');
        }
    };
    walk(dir, '');
    return out;
}

function firstLine(readme) {
    for (const line of readme.split('\n').map((l) => l.trim()))
        if (line && !line.startsWith('#') && !line.startsWith('|') && !line.startsWith('```') && line !== '&nbsp;') return line;
    return '';
}

// The applications: every www/<app>/index.php entry point names one - nested ones too.
function applications() {
    const out = [];
    if (appsDir === path.join(home, 'apps')) {
        const walk = (d, prefix) => {
            let list = [];
            try { list = fs.readdirSync(d).sort(); } catch (e) { return; }
            for (const n of list) {
                const full = path.join(d, n);
                if (!isDir(full)) continue;
                if (fs.existsSync(path.join(full, 'index.php')) && isDir(path.join(appsDir, prefix + n))) out.push(prefix + n);
                walk(full, prefix + n + '/');
            }
        };
        walk(path.join(home, 'www'), '');
    } else {
        for (const n of fs.readdirSync(appsDir).sort()) if (n[0] !== '_' && n[0] !== '.' && isDir(path.join(appsDir, n))) out.push(n);
    }
    return out;
}

function summary(entry) {
    if (!entry) return '';
    if (entry.text.startsWith('### ')) {
        for (const line of entry.text.split('\n').slice(1).map((l) => l.trim()))
            if (line && !line.startsWith('```') && !line.startsWith('|') && !line.startsWith('*') && !line.startsWith('#')) return line;
        return '';
    }
    const row = entry.text.split('\n')[2] || '';
    return row.split('|').slice(2, -1).map((c) => c.trim()).filter(Boolean).join(' - ');
}

// ---------------- the tools ----------------

const app = { type: 'string', description: "The application, as in its URL: 'demo', 'manual', 'regression/errors'" };
const page = { type: 'string', description: "A page of the application, as in ?orders or ?admin/users - default 'index'" };
const template = { type: 'string', description: 'PAD template text to use instead of the page\'s own .pad - it need not exist as a file. With a page, that page\'s .php runs first and its variables are there; without one it renders alone, with the application\'s _tags, _functions and _include.' };
const query = { type: 'string', description: "Request values, as a query string: 'id=7&name=Ann'" };

const TOOLS = [
    {
        name: 'pad_render',
        title: 'Render a PAD page or template',
        description: 'Render a page of a PAD application, or a template given as text, through the PAD engine on the command line - no web server needed. The page\'s .php runs first, as for a GET request. Returns the output; on a PAD error the message, the tag it is about and its line, where PHP raised it, and the variables the page had.',
        inputSchema: { type: 'object', properties: { app, page, template, query }, required: ['app'] },
        run: async (a) => {
            const r = await render(a);
            if (r.code === 0) return { text: truncate(r.out) || '(no output)' };
            return { text: describe(r, a.template !== undefined ? a.template : pageSource(a.app, a.page)), error: true };
        },
    },
    {
        name: 'pad_check',
        title: 'Check PAD syntax',
        description: 'Check PAD with the engine\'s own strict syntax check ($padCheckSyntax): a template given as text, one page, or - with only app - every page of the application (each is rendered, so its .php runs). Answers "ok" or each error with its tag and line. Use it on a template before saving it.',
        inputSchema: { type: 'object', properties: { app, page, template, query }, required: ['app'] },
        run: async (a) => {
            if (a.template !== undefined || a.page) {
                const r = await render(a);
                if (r.code === 0) return { text: 'ok - PAD rendered it without an error' };
                return { text: 'error\n' + describe(r, a.template !== undefined ? a.template : pageSource(a.app, a.page)) };
            }
            const pages = pagesOf(appPath(a.app));
            const shown = pages.slice(0, 300);
            const failures = [];
            let next = 0;
            const worker = async () => {
                while (next < shown.length) {
                    const p = shown[next++];
                    const r = await render({ app: a.app, page: p, query: a.query });
                    if (r.code !== 0) failures.push(p + '\n  ' + describe(r, pageSource(a.app, p)).split('\n').join('\n  '));
                }
            };
            await Promise.all([worker(), worker(), worker(), worker()]);
            failures.sort();
            const head = `${shown.length} pages of ${a.app} checked${pages.length > shown.length ? ` (the first 300 of ${pages.length})` : ''}: `
                + (failures.length ? failures.length + ' with an error' : 'ok, no errors');
            return { text: truncate([head].concat(failures).join('\n\n')) };
        },
    },
    {
        name: 'pad_trace',
        title: 'Trace a PAD request',
        description: 'Render a page (or a template) with PAD\'s execution trace on, and return the trace: every level and occurrence the engine walked, with the tag and the content it produced. The full trace stays under DATA/trace/<page>/.',
        inputSchema: { type: 'object', properties: { app, page, template, query }, required: ['app'] },
        run: async (a) => {
            const name = a.page || (a.template !== undefined ? VIRTUAL : 'index');
            const dir = path.join(home, 'DATA', 'trace', ...name.split('/'));
            const before = new Set(isDir(dir) ? fs.readdirSync(dir) : []);
            const r = await render(Object.assign({}, a, { trace: true }));
            const made = (isDir(dir) ? fs.readdirSync(dir) : []).filter((d) => !before.has(d));
            const status = r.code === 0 ? 'rendered' : 'failed\n' + describe(r, a.template !== undefined ? a.template : pageSource(a.app, a.page));
            if (!made.length) return { text: `the request ${status}\nno trace was written`, error: r.code !== 0 };
            const traceDir = path.join(dir, made.sort().pop());
            return { text: truncate(`the request ${status}\ntrace: ${rel(traceDir)}\n\n` + readText(path.join(traceDir, 'root.txt'))) };
        },
    },
    {
        name: 'pad_apps',
        title: 'List PAD applications',
        description: 'The PAD applications of this checkout, each with the first line of its README.',
        inputSchema: { type: 'object', properties: {} },
        run: async () => ({
            text: applications().map((n) => {
                const line = firstLine(readText(path.join(appsDir, n, 'README.md')));
                return line ? `${n} - ${line}` : n;
            }).join('\n'),
        }),
    },
    {
        name: 'pad_pages',
        title: 'List the pages of a PAD application',
        description: 'The pages of a PAD application (?name), and its own extensions: the _tags, _functions, _include, _callbacks, _data, _lib and _scripts of each directory - what a template can use besides the built-ins.',
        inputSchema: { type: 'object', properties: { app }, required: ['app'] },
        run: async (a) => {
            const dir = appPath(a.app);
            const own = ownOf(dir);
            return { text: truncate('pages: ' + pagesOf(dir).join(', ') + '\n\n' + (own.length ? own.join('\n') : 'no _tags, _functions or _include of its own')) };
        },
    },
    {
        name: 'pad_builtins',
        title: 'List PAD built-ins',
        description: 'The built-in names of PAD, from the engine\'s own directories: tags, pipe functions, options, properties, type prefixes, sequence types and operators - each with a line from docs/reference where there is one.',
        inputSchema: {
            type: 'object',
            properties: { kind: { type: 'string', enum: ['tags', 'functions', 'options', 'properties', 'prefixes', 'sequences', 'operators', 'all'], description: "default 'all'" } },
        },
        run: async (a) => {
            const all = reference.builtins(home);
            const kinds = !a.kind || a.kind === 'all' ? Object.keys(all) : [a.kind];
            const docKind = { tags: 'tag', functions: 'function', options: 'option', properties: 'property', prefixes: 'prefix' };
            const parts = [];
            for (const k of kinds) {
                if (!all[k]) throw new Error(`no kind '${k}'`);
                const lines = all[k].map((n) => {
                    const s = docKind[k] ? summary(reference.lookup(home, docKind[k], n, 1)[0]) : '';
                    return s ? `${n} - ${s}` : n;
                });
                parts.push(`## ${k}\n` + (k === 'sequences' || k === 'operators' ? lines.join(', ') : lines.join('\n')));
            }
            return { text: truncate(parts.join('\n\n')) };
        },
    },
    {
        name: 'pad_reference',
        title: 'Look up the PAD reference',
        description: 'Look up a tag, pipe function, option, property, type prefix or construct in docs/reference - the section that documents it - or read a whole documentation file. Without arguments it lists the files.',
        inputSchema: {
            type: 'object',
            properties: {
                name: { type: 'string', description: "A name: 'if', 'upper', 'sort', 'first', 'app', 'page'" },
                kind: { type: 'string', enum: Object.keys(reference.KINDS), description: 'Narrow the lookup to one kind' },
                file: { type: 'string', description: "A file under docs/, e.g. 'reference/TAGS.md', 'DATABASE.md'" },
            },
        },
        run: async (a) => {
            const docs = path.join(home, 'docs');
            if (a.file) {
                const file = path.resolve(docs, a.file);
                if (!/\.md$/.test(file) || !file.startsWith(docs + path.sep) || !fs.existsSync(file))
                    throw new Error(`no documentation file '${a.file}' - call pad_reference without arguments for the list`);
                return { text: truncate(readText(file)) };
            }
            if (a.name) {
                const name = String(a.name).replace(/^\{|\}$/g, '').replace(/^\|\s*/, '').replace(/[:@]$/, '');
                const found = reference.lookup(home, a.kind, name, 4);
                if (found.length) return { text: truncate(found.map((e) => e.text).join('\n\n---\n\n')) };
                const all = reference.builtins(home);
                const near = Object.values(all).flat().filter((n) => n.toLowerCase().includes(name.toLowerCase())).slice(0, 20);
                return { text: `nothing in docs/reference for '${name}'` + (near.length ? '\nbuilt-in names like it: ' + [...new Set(near)].join(', ') : '') };
            }
            const files = [];
            for (const d of ['reference', '']) {
                const dir = path.join(docs, d);
                for (const f of (isDir(dir) ? fs.readdirSync(dir) : []).filter((n) => n.endsWith('.md')).sort())
                    files.push(`${d ? d + '/' : ''}${f} - ${(readText(path.join(dir, f)).match(/^#\s+(.*)$/m) || ['', ''])[1]}`);
            }
            return { text: files.join('\n') };
        },
    },
    {
        name: 'pad_test',
        title: 'Run the PAD regression suites',
        description: 'Run ./ci.sh - all eight regression suites, fetched from a running PAD web server, plus the editor tooling checks - and report each suite\'s line and every failing test with what was wanted and what came back. Takes 15 to 60 seconds.',
        inputSchema: {
            type: 'object',
            properties: { host: { type: 'string', description: "The base the applications are served under, default $PAD_HOST or 'http://localhost/pad/'" } },
        },
        run: async (a) => {
            const host = a.host || process.env.PAD_HOST || 'http://localhost/pad/';
            if (!/^https?:\/\/[^\s'"]+$/.test(host)) throw new Error(`'${host}' is not a URL`);
            const r = await new Promise((resolve) => {
                const child = spawn('bash', [path.join(home, 'ci.sh'), host], { cwd: home, env: Object.assign({}, process.env, { PAD_HOME: home }) });
                let out = '';
                const timer = setTimeout(() => child.kill('SIGKILL'), 600000);
                child.stdout.on('data', (d) => { out += d; });
                child.stderr.on('data', (d) => { out += d; });
                child.on('error', (e) => { clearTimeout(timer); resolve({ code: -1, out: String(e.message) }); });
                child.on('close', (code) => { clearTimeout(timer); resolve({ code, out }); });
            });
            const suites = process.env.CI_SUITES || path.join(home, 'DATA', 'suites');
            const failing = [];
            for (const f of (isDir(suites) ? fs.readdirSync(suites) : []).filter((n) => n.endsWith('.json')).sort()) {
                let data = null;
                try { data = JSON.parse(readText(path.join(suites, f))); } catch (e) { continue; }
                for (const t of data.tests || [])
                    if (t.status && t.status !== 'ok')
                        failing.push(`${f.replace(/\.json$/, '')}: ${t.name} (${t.status})\n  want: ${String(t.want || '').slice(0, 300)}\n  got:  ${String(t.got || '').slice(0, 300)}`);
            }
            const verdict = r.code === 0 ? 'all suites passed' : `FAILED (ci.sh exit ${r.code})`;
            return { text: truncate(`${verdict}\n\n${r.out.trim()}` + (failing.length ? '\n\nfailing tests:\n' + failing.join('\n') : '')) };
        },
    },
];

// ---------------- JSON-RPC over stdio ----------------

function send(msg) {
    process.stdout.write(JSON.stringify(msg) + '\n');
}

function reply(id, result) { send({ jsonrpc: '2.0', id, result }); }
function fail(id, code, message) { send({ jsonrpc: '2.0', id, error: { code, message } }); }

async function handle(msg) {
    if (!msg || typeof msg !== 'object' || msg.jsonrpc !== '2.0') return;
    const { id, method, params } = msg;
    const request = id !== undefined && id !== null;

    if (method === 'initialize') {
        const asked = params && params.protocolVersion;
        return reply(id, {
            protocolVersion: PROTOCOLS.includes(asked) ? asked : PROTOCOLS[0],
            capabilities: { tools: { listChanged: false } },
            serverInfo: { name: 'pad-mcp', title: 'PAD', version: VERSION },
            instructions: 'Tools for PAD (PHP Application Driver) templates, answered by the PAD engine of this checkout. '
                + 'Before writing or changing a .pad template, look its tags and functions up with pad_reference; '
                + 'afterwards run pad_check on it - the engine\'s strict syntax check - or pad_render to see the output.',
        });
    }
    if (method === 'ping') return request && reply(id, {});
    if (method === 'tools/list')
        return reply(id, { tools: TOOLS.map(({ name, title, description, inputSchema }) => ({ name, title, description, inputSchema })) });
    if (method === 'tools/call') {
        const tool = TOOLS.find((t) => t.name === (params && params.name));
        if (!tool) return fail(id, -32602, `no tool '${params && params.name}'`);
        try {
            const out = await tool.run((params && params.arguments) || {});
            return reply(id, { content: [{ type: 'text', text: out.text }], isError: !!out.error });
        } catch (e) {
            return reply(id, { content: [{ type: 'text', text: String(e && e.message || e) }], isError: true });
        }
    }
    if (typeof method === 'string' && method.startsWith('notifications/')) return;
    if (request) fail(id, -32601, `no method '${method}'`);
}

let buffer = '';
let pending = 0;
let closing = false;

// The client may close stdin with answers still on their way - a render takes a moment -
// so the process ends when the last one is written, not before.
function done() {
    if (closing && pending === 0) process.exit(0);
}

process.stdin.setEncoding('utf8');
process.stdin.on('data', (chunk) => {
    buffer += chunk;
    let nl;
    while ((nl = buffer.indexOf('\n')) >= 0) {
        const line = buffer.slice(0, nl).trim();
        buffer = buffer.slice(nl + 1);
        if (!line) continue;
        let msg;
        try { msg = JSON.parse(line); } catch (e) { send({ jsonrpc: '2.0', id: null, error: { code: -32700, message: 'parse error' } }); continue; }
        for (const one of Array.isArray(msg) ? msg : [msg]) {
            pending++;
            handle(one)
                .catch((e) => process.stderr.write('pad-mcp: ' + (e && e.stack || e) + '\n'))
                .finally(() => { pending--; done(); });
        }
    }
});
process.stdin.on('end', () => { closing = true; done(); });
