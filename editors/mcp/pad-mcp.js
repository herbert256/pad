#!/usr/bin/env node
// PAD MCP server (Model Context Protocol, stdio, no dependencies): lets an AI assistant
// check PAD against the engine itself instead of against what it remembers of CLAUDE.md.
//
// pad_render     render a page, or a template given as text, through PAD on the command
//                line (editors/render.php) - the output, or the PAD error with its tag and line;
//                with data= the template gets those variables instead of running any PHP
// pad_check      the strict syntax check of a template, a page, or every page of an app
// pad_trace      render with the execution trace on and read it back (DATA/trace/)
// pad_php        run a PHP snippet in a booted application - the engine's library, the
//                application's _lib, configuration and database - and see what it answers
// pad_compare    the same render or snippet on two versions of the engine - HEAD and the
//                working tree by default - and the difference: a fix shown to change what it
//                should, a new regression case shown to fail on the old code
// pad_case       one test of the regression suites, judged the way its suite judges it
// pad_apps       the applications, with the first line of their README
// pad_pages      an application's pages, and its own _tags, _functions, _include, ...
// pad_builtins   the built-in tags, functions, options, properties, type prefixes, sequence
//                types and operators - the directories editors/generate.php reads
// pad_reference  a tag, function, option, property or prefix in docs/reference, or a doc file
// pad_test       run ./ci.sh - every regression suite - or one suite, and read back what
//                failed, each failure as the place where want and got part
//
// The protocol is JSON-RPC 2.0, one message per line on stdin and stdout; logs go to stderr.
// Register it with Claude Code:  claude mcp add pad -- node /path/to/pad/editors/mcp/pad-mcp.js
// (or install the plugin in editors/claude, which brings this server and the language server).
//
// Every render loads prepend.php ahead of editors/render.php: at the end of the request it
// writes a trailer with the HTTP status and, for pad_php, what the snippet did - taken off the
// output here.
//
// PAD_APPS points the tools at another applications directory (the tests use the fixture in
// editors/fixture/apps); PAD_PHP names the php binary; PAD_HOST is pad_test's default host;
// PAD_MCP_SNAPSHOTS is where pad_compare keeps its snapshots, DATA/mcp-snapshots by default.

const fs = require('fs');
const os = require('os');
const path = require('path');
const crypto = require('crypto');
const { spawn } = require('child_process');

const reference = require(path.join(__dirname, '..', 'reference.js'));

const home = path.resolve(__dirname, '..', '..');
const appsDir = process.env.PAD_APPS ? path.resolve(process.env.PAD_APPS) : path.join(home, 'apps');
const php = process.env.PAD_PHP || 'php';
const VERSION = '0.2.0';
const PROTOCOLS = ['2025-11-25', '2025-06-18', '2025-03-26', '2024-11-05'];
const LIMIT = 60000;               // characters of one answer
const VIRTUAL = 'mcpTemplate';     // the page name a template without a page renders as
const PREPEND = path.join(__dirname, 'prepend.php');
const TRAILER = '\x1ePAD-MCP\x1e';
const SNAPSHOTS = process.env.PAD_MCP_SNAPSHOTS ? path.resolve(process.env.PAD_MCP_SNAPSHOTS) : path.join(home, 'DATA', 'mcp-snapshots');
const SUITES = ['pages', 'common', 'errors', 'framework', 'regression', 'sequence', 'manual', 'other'];

// The tree a render runs on: this checkout, or a snapshot of another commit (pad_compare).
const CURRENT = { home, apps: appsDir, label: 'working tree' };

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

function isFile(file) {
    try { return fs.statSync(file).isFile(); } catch (e) { return false; }
}

// An application name as a URL carries it - no way out of the applications directory.
function appPath(app, apps = appsDir) {
    if (typeof app !== 'string' || !/^[A-Za-z0-9][A-Za-z0-9_/-]*$/.test(app) || app.includes('//'))
        throw new Error(`'${app}' is not an application name`);
    const dir = path.join(apps, app.replace(/\/+$/, ''));
    if (!isDir(dir)) throw new Error(`there is no application '${app}' in ${rel(apps) || apps}`);
    return dir;
}

function checkPage(page) {
    if (page !== undefined && (typeof page !== 'string' || !/^[A-Za-z0-9][A-Za-z0-9_/-]*$/.test(page) || page.includes('/_')))
        throw new Error(`'${page}' is not a page name - ?orders, ?admin/users`);
}

// A page name as editors/render.php reads it: the slashes at its end left off.
function pageName(page) {
    return page === undefined ? undefined : page.replace(/\/+$/, '');
}

let serial = 0;

// A page name no file has and no other call uses at the same moment.
function unique(prefix) {
    return prefix + process.pid.toString(36) + (++serial).toString(36) + crypto.randomBytes(3).toString('hex');
}

// One request through editors/render.php, in its own php process - of this checkout, or of
// the snapshot of another commit (tree).
//
// bare      the page without the _inits/_exits frame (&padInclude) - by default for a
//           template given as text, which is what its output is asked for
// data      variables for the template instead of PHP: the designer preview's own mechanism
//           (?padSample, lib/sample.php) with the sample under DATA/samples/ for a page name
//           of this call only - no _inits.php or page .php runs. Without a template it is
//           the page's own template that gets them
// code      a PHP file prepend.php runs at the end of the request, in the booted application
// host      the base the request says it came on, for a page that fetches its own server
async function render(a, tree = CURRENT) {
    const { app, query, trace } = a;
    appPath(app, tree.apps);
    checkPage(a.page);
    // The page as render.php takes it, without a slash at the end: products/ was split into
    // products and an empty segment, which the route of products/[id].pad took as its value -
    // the data rendered a page the same name without data does not.
    const page = pageName(a.page);
    if (a.data !== undefined && (a.data === null || typeof a.data !== 'object' || Array.isArray(a.data)))
        throw new Error('data must be a JSON object, its keys the variables: {"name": "Ann", "rows": [{"n": 1}]}');
    let name = page || (a.template !== undefined ? VIRTUAL : 'index');
    let template = a.template !== undefined ? String(a.template) : undefined;
    let sample = null;
    // The values a route binds - 42 of products/42 for products/[id].pad - are the sample's
    // too, under the data's own: as the made-up name of the data render, the page was no
    // longer reached by that path, and {$id} was not found where the engine's own preview of
    // ?products/42&padSample has it.
    let routed = {};
    if (a.data !== undefined) {
        if (template === undefined) {
            // A directory's index renders in that directory, a routed page in the directory of
            // its bracketed file: the sample is named there.
            const own = pageTemplate(app, page, tree.apps);
            if (!own.text) throw new Error(`the page '${name}' has no template of its own to give the data to`);
            template = own.text;
            name = own.page;
            routed = own.vars;
        }
        const dir = name.includes('/') ? name.slice(0, name.lastIndexOf('/') + 1) : '';
        name = dir + (a.dataName || unique('mcpData'));
        sample = path.join(tree.home, 'DATA', 'samples', ...app.split('/'), ...name.split('/')) + '.json';
        fs.mkdirSync(path.dirname(sample), { recursive: true });
        fs.writeFileSync(sample, JSON.stringify(Object.assign({}, routed, a.data)));
    }
    const bare = a.bare !== undefined ? !!a.bare : a.template !== undefined;
    const asked = [String(query || '').replace(/^\?/, ''), bare ? 'padInclude' : '', sample ? 'padSample' : ''].filter(Boolean).join('&');
    const args = ['-d', 'auto_prepend_file=' + PREPEND, path.join(tree.home, 'editors', 'render.php'), app, name, '--apps=' + tree.apps];
    if (template !== undefined) args.push('--source=-');
    // A name made up here - a template's, its data's - is the template's own page: a
    // bracketed route of its directory, products/[id].pad beside products/mcpData..., took it
    // for a value, and the sample was looked for under products/[id].
    if (template !== undefined && name !== page) args.push('--virtual');
    if (asked) args.push('--query=' + asked);
    if (trace) args.push('--trace');
    const env = Object.assign({}, process.env);
    if (a.host) env.PAD_HOST = a.host;
    if (a.codeFile) env.PAD_MCP_CODE = a.codeFile;
    else delete env.PAD_MCP_CODE;
    try {
        const r = await new Promise((resolve) => {
            const child = spawn(php, args, { cwd: tree.home, env });
            let out = '', err = '';
            const timer = setTimeout(() => child.kill('SIGKILL'), 60000);
            child.stdout.on('data', (d) => { out += d; });
            child.stderr.on('data', (d) => { err += d; });
            child.on('error', (e) => { clearTimeout(timer); resolve({ code: -1, out: '', err: String(e.message) }); });
            child.on('close', (code) => { clearTimeout(timer); resolve({ code, out, err }); });
            // A php that ends before it read the template - one that fails to start, or a wrong
            // binary - closes the pipe under the write: the EPIPE has a listener here, and the
            // close above answers the call, where it went unhandled and ended the whole server.
            child.stdin.on('error', () => {});
            child.stdin.end(template !== undefined ? template : '');
        });
        // The trailer prepend.php writes last: the status, and what a snippet did.
        const at = r.out.lastIndexOf(TRAILER);
        if (at >= 0) {
            try { r.trailer = JSON.parse(r.out.slice(at + TRAILER.length).trim()); } catch (e) { r.trailer = null; }
            r.out = r.out.slice(0, at).replace(/\n$/, '');
        }
        r.status = r.trailer && r.trailer.status;
        r.tree = (() => { try { return fs.realpathSync(tree.home); } catch (e) { return tree.home; } })();
        r.php = r.trailer && r.trailer.php;
        r.page = name;
        return r;
    } finally {
        if (sample) fs.rmSync(sample, { force: true });
    }
}

// A PHP snippet as the file prepend.php includes: the statements inside a function, so a
// return is the answer and a snippet without one answers nothing. Two lines go before the
// snippet's own; an error names the snippet's line, not the temporary file's.
function snippetFile(code) {
    if (typeof code !== 'string' || !code.trim()) throw new Error('code is the PHP to run: return padStrSlug ( \'Hello World\' );');
    // The real path, as PHP names the file in an error - /private/var/... on macOS, where
    // the temporary directory is a link.
    const file = path.join(fs.realpathSync(os.tmpdir()), unique('pad-mcp-') + '.php');
    fs.writeFileSync(file, '<?php\nreturn ( function () {\n' + code.replace(/^\s*<\?php\b/, '').replace(/\?>\s*$/, '') + '\n;\n} ) ();\n');
    return file;
}

function snippetText(r, file) {
    const p = r.php;
    if (!p) {
        const said = (r.out || r.err || '').trim();
        return { text: 'the request ended before the snippet answered - an exit, a fatal error' + (said ? ':\n\n' + truncate(said, 3000) : ''), error: true };
    }
    const own = (s) => String(s).split(file).join('snippet').replace(/snippet:(\d+)/g, (m, n) => 'snippet line ' + Math.max(1, n - 2))
        .split(r.tree + path.sep).join('');
    const parts = [];
    if (p.output) parts.push(own(p.output).replace(/\n$/, ''));
    if (p.result !== undefined) parts.push('=> ' + own(p.result));
    if (p.error) parts.push(own(p.error));
    return { text: truncate(parts.join('\n') || '(no output, nothing returned)'), error: !!p.error };
}

// A status line for an answer that was not a plain 200.
function statusLine(r) {
    return r.status && r.status !== 200 ? `HTTP ${r.status}\n` : '';
}

// ---------------- other versions: snapshots for pad_compare ----------------

function run(cmd, args, opts = {}) {
    return new Promise((resolve) => {
        const child = spawn(cmd, args, Object.assign({ cwd: home }, opts));
        let out = '', err = '';
        child.stdout && child.stdout.on('data', (d) => { out += d; });
        child.stderr && child.stderr.on('data', (d) => { err += d; });
        child.on('error', (e) => resolve({ code: -1, out, err: String(e.message) }));
        child.on('close', (code) => resolve({ code, out, err }));
    });
}

const building = new Map();

// The directory the snapshots live in, made readable and writable by this user alone, and
// refused when anyone else could have put a snapshot in it: what a snapshot holds - its
// editors/render.php, its pad/ - runs as the developer. They lived in the system's temporary
// directory, on Linux the /tmp every user shares, where whoever made pad-mcp-snapshots first
// owned it, and a <commit>/.complete planted there had its render.php run by the next
// compare against that commit; now they are under the checkout's own DATA/.
function snapshots() {
    try { fs.mkdirSync(path.dirname(SNAPSHOTS), { recursive: true }); fs.mkdirSync(SNAPSHOTS, { mode: 0o700 }); } catch (e) { /* there already, or the check says why */ }
    let st = null;
    try { st = fs.lstatSync(SNAPSHOTS); } catch (e) { st = null; }
    const posix = typeof process.getuid === 'function';
    if (!st || !st.isDirectory() || (posix && (st.uid !== process.getuid() || (st.mode & 0o022) !== 0)))
        throw new Error(`${SNAPSHOTS} is not a directory of this user alone - pad_compare runs what a snapshot in it holds, so it does not use it`);
}

// Another commit as a tree to render on: its pad/, editors/render.php, home/ and www/ - and,
// with scope 'all', its apps/ - taken out of git with git archive into a directory of its own
// under DATA/mcp-snapshots/, made once per commit and kept for the next call. No worktree, no
// checkout: the repository and its working tree are not touched, so it is safe while other
// work goes on in the checkout. The applications stay this working tree's unless scope is
// 'all' - a regression case written today runs on yesterday's engine.
async function snapshot(ref, scope) {
    if (typeof ref !== 'string' || !/^[A-Za-z0-9][\w./~^@{}-]*$/.test(ref) || ref.includes('..'))
        throw new Error(`'${ref}' is not a commit, branch or tag`);
    const rev = await run('git', ['rev-parse', '--verify', '--quiet', ref + '^{commit}']);
    const sha = rev.out.trim();
    if (rev.code !== 0 || !/^[0-9a-f]{40}$/.test(sha)) throw new Error(`git knows no commit '${ref}'`);
    snapshots();
    const all = scope === 'all';
    const dir = path.join(SNAPSHOTS, sha.slice(0, 12) + (all ? '-all' : ''));
    const tree = { home: dir, apps: all ? path.join(dir, 'apps') : appsDir, label: `${ref} ${sha.slice(0, 9)}`, sha };
    if (isFile(path.join(dir, '.complete'))) {
        const now = new Date();
        try { fs.utimesSync(dir, now, now); } catch (e) { /* only the age the pruning reads */ }
        return tree;
    }
    if (!building.has(dir)) {
        building.set(dir, (async () => {
            const has = await run('git', ['cat-file', '-e', sha + ':editors/render.php']);
            if (has.code !== 0) throw new Error(`${ref} has no editors/render.php - it is older than the command-line render the tools use`);
            const parts = ['pad', 'editors/render.php', 'home', 'www'].concat(all ? ['apps'] : []);
            const present = [];
            for (const p of parts) if ((await run('git', ['cat-file', '-e', sha + ':' + p])).code === 0) present.push(p);
            // A file over a megabyte - the OEIS database of the sequences, 76 MB - is left out of
            // the archive and cloned from the working tree when it is the same there (a clone
            // costs nothing on APFS, a copy elsewhere), or else taken from git on its own.
            const listed = await run('git', ['ls-tree', '-r', '-l', sha, '--', ...present]);
            const large = listed.out.split('\n').map((l) => l.match(/^\d+ blob ([0-9a-f]{40})\s+(\d+)\t(.+)$/))
                .filter((m) => m && +m[2] > 1048576).map((m) => ({ blob: m[1], file: m[3] }));
            fs.mkdirSync(SNAPSHOTS, { recursive: true });
            const tmp = fs.mkdtempSync(path.join(SNAPSHOTS, '.new-'));
            const made = await run('bash', ['-c', 'git archive --format=tar "$1" -- "${@:3}" | tar -x -C "$2"', 'archive', sha, tmp,
                ...present, ...large.map((l) => ':(exclude)' + l.file)]);
            if (made.code !== 0) { fs.rmSync(tmp, { recursive: true, force: true }); throw new Error('git archive failed: ' + made.err.trim()); }
            for (const l of large) {
                const into = path.join(tmp, ...l.file.split('/'));
                const here = path.join(home, ...l.file.split('/'));
                fs.mkdirSync(path.dirname(into), { recursive: true });
                if (isFile(here) && (await run('git', ['hash-object', here])).out.trim() === l.blob)
                    fs.copyFileSync(here, into, fs.constants.COPYFILE_FICLONE);
                else
                    await run('bash', ['-c', 'git cat-file blob "$1" > "$2"', 'blob', l.blob, into]);
            }
            fs.mkdirSync(path.join(tmp, 'DATA'), { recursive: true });
            fs.writeFileSync(path.join(tmp, '.complete'), sha + '\n');
            try { fs.renameSync(tmp, dir); } catch (e) { fs.rmSync(tmp, { recursive: true, force: true }); }   // another server made it first
            prune(dir);
        })().finally(() => building.delete(dir)));
    }
    await building.get(dir);
    return tree;
}

// The six snapshots used last stay, and so does every one used in the last hour; the rest go -
// each is the engine at one commit. A call touches its snapshot when it takes it and renders
// for a minute at the most, so a snapshot another session is still rendering on - the
// parallel sessions of a hunt each compare against a commit of their own - is never taken
// from under it: the seventh made while one rendered deleted the oldest, the one in use, and
// its compare said "they differ" with an engine file missing on the old side. One that goes
// is renamed out of the way first, so no call finds it half deleted.
const KEEP = 6;
const USED = 3600000;

function prune(keep) {
    let list = [];
    try { list = fs.readdirSync(SNAPSHOTS).filter((n) => !n.startsWith('.')).map((n) => path.join(SNAPSHOTS, n)); } catch (e) { return; }
    const aged = list.map((d) => { try { return { d, t: fs.statSync(d).mtimeMs }; } catch (e) { return null; } }).filter(Boolean).sort((x, y) => y.t - x.t);
    for (const { d, t } of aged.slice(KEEP)) {
        if (d === keep || Date.now() - t < USED) continue;
        const gone = path.join(SNAPSHOTS, '.gone-' + crypto.randomBytes(4).toString('hex'));
        try { fs.renameSync(d, gone); } catch (e) { continue; }
        fs.rmSync(gone, { recursive: true, force: true });
    }
}

// What the working tree holds that the commit does not, in the parts a render reads: the
// engine of pad_compare's working-tree side is that, uncommitted changes of anyone included.
async function uncommitted(scope) {
    const r = await run('git', ['status', '--porcelain', '--', 'pad', 'editors/render.php', 'home', 'www'].concat(scope === 'all' ? ['apps'] : []));
    return r.code === 0 ? r.out.split('\n').filter(Boolean) : [];
}

// Where two texts part: the first character that differs, with what each has there.
function parting(a, b) {
    let i = 0;
    while (i < a.length && i < b.length && a[i] === b[i]) i++;
    let j = 0;
    while (j < a.length - i && j < b.length - i && a[a.length - 1 - j] === b[b.length - 1 - j]) j++;
    const line = a.slice(0, i).split('\n').length;
    const show = (s) => JSON.stringify(s.length > 400 ? s.slice(0, 400) + '...' : s);
    return { at: i, line, before: show(a.slice(Math.max(0, i - 60), i)), want: show(a.slice(i, a.length - j)), got: show(b.slice(i, b.length - j)) };
}

// A unified diff of two texts, by git diff --no-index on two temporary files.
async function unified(a, b, labelA, labelB) {
    const dir = fs.mkdtempSync(path.join(os.tmpdir(), 'pad-mcp-diff-'));
    try {
        fs.writeFileSync(path.join(dir, 'a'), a.endsWith('\n') ? a : a + '\n');
        fs.writeFileSync(path.join(dir, 'b'), b.endsWith('\n') ? b : b + '\n');
        const r = await run('git', ['diff', '--no-index', '--no-color', '-U2', '--', 'a', 'b'], { cwd: dir });
        const body = r.out.split('\n');
        const from = body.findIndex((l) => l.startsWith('@@'));
        if (from < 0) return '';
        // A page is often one long line: each line of the diff is cut, and the whole of it,
        // where the place they part - above it - says the rest.
        const lines = body.slice(from).map((l) => l.length > 400 ? l.slice(0, 400) + ` ... (${l.length - 400} more characters)` : l);
        return truncate([`--- ${labelA}`, `+++ ${labelB}`].concat(lines).join('\n').trimEnd(), 12000);
    } finally {
        fs.rmSync(dir, { recursive: true, force: true });
    }
}

// HTML-escaped text as the suite results keep it - htmlspecialchars, then padEscape - back
// to what the page said.
function unescapeSuite(s) {
    return String(s || '')
        .replace(/&open;/g, '{').replace(/&close;/g, '}').replace(/&pipe;/g, '|').replace(/&eq;/g, '=')
        .replace(/&comma;/g, ',').replace(/&at;/g, '@')
        .replace(/&lt;/g, '<').replace(/&gt;/g, '>').replace(/&quot;/g, '"').replace(/&#0?39;/g, "'").replace(/&amp;/g, '&');
}

// A failed test of a suite result, as the place where want and got part.
function failure(t, suite) {
    const want = unescapeSuite(t.want), got = unescapeSuite(t.got);
    const lines = [`${suite}: ${t.name} (${t.status})` + (t.url ? ' ' + t.url : '')];
    if (t.status === 'new') lines.push('  no answer yet - it came back:', '  ' + truncate(got, 600).split('\n').join('\n  '));
    else if (want.length + got.length <= 600) lines.push('  want: ' + want, '  got:  ' + got);
    else {
        const p = parting(want, got);
        lines.push(`  want and got part at character ${p.at} (line ${p.line}), after ${p.before}`, `  want: ${p.want}`, `  got:  ${p.got}`);
    }
    return lines.join('\n');
}

// Every failing test of a suite result - nested where a suite nests them.
function failures(data, suite) {
    const out = [];
    const walk = (x) => {
        if (Array.isArray(x)) { x.forEach(walk); return; }
        if (!x || typeof x !== 'object') return;
        if (typeof x.name === 'string' && x.status && x.status !== 'ok') out.push(failure(x, suite));
        for (const v of Object.values(x)) if (v && typeof v === 'object') walk(v);
    };
    walk(data.tests || []);
    return out;
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
    const t = json.template;
    // The engine's own placing first: the file, line and tag of the report's template. The
    // search below finds only the first spot the tag's text stands, which named line 1 for
    // an unclosed {if 1 eq 1} on line 3 when a closed one stood earlier; it stays for a
    // template given as text, which the engine cannot place in a file.
    if (t && typeof t.file === 'string' && t.line > 0)
        lines.push('tag: ' + (t.tag || '{' + tag + '}') + ', line ' + t.line + ' of ' + t.file);
    else if (tag) {
        let where = '';
        const at = source ? source.indexOf('{' + tag) : -1;
        if (at >= 0) where = ', line ' + (source.slice(0, at).split('\n').length);
        lines.push('tag: {' + tag + '}' + where);
    }
    if (g.padPage && !/^(.*\/)?mcp(Template|Data|Php)/.test(g.padPage)) lines.push('page: ' + g.padPage);
    if (json.file) lines.push('raised in: ' + rel(json.file) + ':' + json.line);
    const vars = Object.keys(json.app || {}).filter((k) => k !== 'argv' && k !== 'argc');
    lines.push('variables: ' + (vars.length ? vars.map((v) => '$' + v).join(', ') : 'none'));
    return lines.join('\n');
}

// The page's own template and the page it is: <page>.pad or .html, or - as the engine serves
// the name of a directory - its index.pad or index.html, ?staff being staff/index.pad. Only
// the first two were read, and the data of pad_render was refused for such a page: "the page
// 'staff' has no template of its own". A name no file has may be served by a bracketed
// route, products/42 by products/[id].pad (routePage) - that file is the template then,
// and vars the values the route binds, { id: '42' }.
function pageTemplate(app, page, apps = appsDir) {
    const name = pageName(page) || 'index';
    const dir = appPath(app, apps);
    const base = path.join(dir, ...name.split('/'));
    for (const [file, as] of [[base + '.pad', name], [base + '.html', name], [path.join(base, 'index.pad'), name + '/index'], [path.join(base, 'index.html'), name + '/index']])
        if (isFile(file)) return { text: readText(file), page: as, vars: {} };
    const routed = routePage(dir, name.split('/'));
    if (routed) {
        const at = path.join(dir, ...routed.page.split('/'));
        for (const file of [at + '.pad', at + '.html'])
            if (isFile(file)) return { text: readText(file), page: routed.page, vars: routed.vars };
    }
    return { text: '', page: name, vars: {} };
}

// The page of an application a bracketed route serves for the segments of a name, as
// pad/lib/route.php (padRouteWalk) finds it: a literal segment first - a page, a directory's
// index, a directory to go on in - then a [name] page, a [name] directory, a [name+] page
// taking the rest; the bracketed names in sorted order - with the values it binds, a name
// bound first kept. null when none does.
function routePage(dir, segments, prefix = '', vars = {}) {
    const [segment, ...rest] = segments;
    const last = rest.length === 0;
    const literal = /^[a-zA-Z0-9][a-zA-Z0-9_-]*$/.test(segment);
    const here = path.join(dir, ...prefix.split('/').filter(Boolean));
    const exists = (base) => ['.php', '.pad', '.html'].some((ext) => isFile(base + ext));
    if (last) {
        if (literal && exists(path.join(here, segment))) return { page: prefix + segment, vars };
        if (literal && isDir(path.join(here, segment)) && exists(path.join(here, segment, 'index'))) return { page: prefix + segment + '/index', vars };
    } else if (literal && isDir(path.join(here, segment))) {
        const found = routePage(dir, rest, prefix + segment + '/', vars);
        if (found) return found;
    }
    const names = { pages: [], dirs: [], rests: [] };
    let entries = [];
    try { entries = fs.readdirSync(here).sort(); } catch (e) { entries = []; }
    for (const entry of entries) {
        const m = entry.match(/^\[([a-zA-Z][a-zA-Z0-9_]*)(\+?)\](\.(php|pad|html))?$/);
        if (!m || /^(pad|pq)/.test(m[1]) || m[1] === 'GLOBALS' || m[1] === 'this') continue;
        const isDirectory = isDir(path.join(here, entry));
        const isPage = !isDirectory && m[3] !== undefined;
        const list = m[2] && isPage ? names.rests : !m[2] && isDirectory ? names.dirs : !m[2] && isPage ? names.pages : null;
        if (list && !list.includes(m[1])) list.push(m[1]);
    }
    const bind = (name, value) => Object.assign({ [name]: value }, vars);
    if (last && names.pages.length) return { page: prefix + '[' + names.pages[0] + ']', vars: bind(names.pages[0], segment) };
    for (const name of names.dirs) {
        if (last) {
            if (exists(path.join(here, '[' + name + ']', 'index'))) return { page: prefix + '[' + name + ']/index', vars: bind(name, segment) };
            continue;
        }
        const found = routePage(dir, rest, prefix + '[' + name + ']/', bind(name, segment));
        if (found) return found;
    }
    if (names.rests.length) return { page: prefix + '[' + names.rests[0] + '+]', vars: bind(names.rests[0], segments.join('/')) };
    return null;
}

// The page's own template, for placing an error on its line.
function pageSource(app, page, apps = appsDir) {
    return pageTemplate(app, page, apps).text;
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

// One line for a name in pad_builtins: the first sentence of a section's first paragraph -
// its lines joined, where the first line alone stopped mid-sentence at the file's wrap
// ("... `padNumberAbbreviate` in a") - or a table row's cells.
function summary(entry) {
    if (!entry) return '';
    if (entry.text.startsWith('### ')) {
        const para = [];
        for (const line of entry.text.split('\n').slice(1).map((l) => l.trim())) {
            if (!line) { if (para.length) break; continue; }
            if (line.startsWith('```') || line.startsWith('|') || line.startsWith('*') || line.startsWith('#') || line.startsWith('{')) { if (para.length) break; continue; }
            para.push(line);
        }
        const text = para.join(' ');
        const end = text.search(/\.(\s|$)/);
        const first = end >= 0 ? text.slice(0, end + 1) : text;
        return first.length > 220 ? first.slice(0, 220).replace(/\s+\S*$/, '') + ' ...' : first;
    }
    const row = entry.text.split('\n')[2] || '';
    return row.split('|').slice(2, -1).map((c) => c.trim()).filter(Boolean).join(' - ');
}

// ---------------- the tools ----------------

const app = { type: 'string', description: "The application, as in its URL: 'demo', 'manual', 'regression/errors'" };
const page = { type: 'string', description: "A page of the application, as in ?orders or ?admin/users - default 'index'" };
const template = { type: 'string', description: 'PAD template text to use instead of the page\'s own .pad - it need not exist as a file. With a page, that page\'s .php runs first and its variables are there; without one it renders alone, with the application\'s _tags, _functions and _include.' };
const query = { type: 'string', description: "Request values, as a query string: 'id=7&name=Ann'" };
const data = { type: 'object', description: 'Variables for the template instead of running PHP - {"name": "Ann", "rows": [{"n": 1}, {"n": 2}]}: each key a variable, a list of objects iterable as {rows}...{/rows}. No _inits.php and no page .php run (the designer preview\'s mechanism, ?padSample). Without a template, the page\'s own template gets them.' };
const bare = { type: 'boolean', description: 'Leave out the _inits/_exits frame (&padInclude) - the default for a template given as text; a page renders framed unless this is true.' };

// The parts of a render's output a comparison or a check reads: the status, the body or the
// error, and what a snippet did.
function outcome(r, source) {
    if (r.php) return snippetText(r, r.codeFile).text;
    if (r.code === 0) return statusLine(r) + r.out;
    return statusLine(r) + describe(r, source);
}

// Runs the tool's snippet and cleans its file up.
async function withSnippet(a, fn) {
    if (a.code === undefined) return fn(a);
    const file = snippetFile(a.code);
    try { return await fn(Object.assign({}, a, { codeFile: file })); } finally { fs.rmSync(file, { force: true }); }
}

const TOOLS = [
    {
        name: 'pad_render',
        title: 'Render a PAD page or template',
        description: 'Render a page of a PAD application, or a template given as text, through the PAD engine on the command line - no web server needed. The page\'s .php runs first, as for a GET request - or, with data, the template gets those variables and no PHP runs. Returns the output, with an HTTP line first when the status was not 200; on a PAD error the message, the tag it is about and its line, where PHP raised it, and the variables the page had.',
        inputSchema: { type: 'object', properties: { app, page, template, query, data, bare }, required: ['app'] },
        run: async (a) => {
            const r = await render(a);
            if (r.code === 0) return { text: truncate(statusLine(r) + r.out) || '(no output)' };
            return { text: statusLine(r) + describe(r, a.template !== undefined ? a.template : pageSource(a.app, a.page)), error: true };
        },
    },
    {
        name: 'pad_check',
        title: 'Check PAD syntax',
        description: 'Check PAD with the engine\'s own strict syntax check ($padCheckSyntax): a template given as text, one page, or - with only app - every page of the application (each is rendered, so its .php runs). Answers "ok" or each error with its tag and line. Use it on a template before saving it; give data to check a template against variables no PHP provides.',
        inputSchema: { type: 'object', properties: { app, page, template, query, data }, required: ['app'] },
        run: async (a) => {
            if (a.template !== undefined || a.page || a.data !== undefined) {
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
                    // A template whose name is no page name - a backup orders.old.pad, a
                    // space, a dot in a directory - is that page's failure, as pad lint has
                    // it: thrown here it ended the whole check on it, every other page unread.
                    let r;
                    try { r = await render({ app: a.app, page: p, query: a.query }); } catch (e) {
                        failures.push(p + '\n  ' + String(e && e.message || e).replace(/ - \?orders, \?admin\/users$/, ''));
                        continue;
                    }
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
        inputSchema: { type: 'object', properties: { app, page, template, query, data, bare }, required: ['app'] },
        run: async (a) => {
            const traceOf = (name) => path.join(home, 'DATA', 'trace', ...name.split('/'));
            const asked = traceOf(a.page || (a.template !== undefined ? VIRTUAL : 'index'));
            const before = new Set(isDir(asked) ? fs.readdirSync(asked) : []);
            const r = await render(Object.assign({}, a, { trace: true }));
            const dir = traceOf(r.page);
            const made = (isDir(dir) ? fs.readdirSync(dir) : []).filter((d) => dir !== asked || !before.has(d));
            const status = r.code === 0 ? 'rendered' + (r.status && r.status !== 200 ? ' - HTTP ' + r.status : '')
                : 'failed\n' + describe(r, a.template !== undefined ? a.template : pageSource(a.app, a.page));
            if (!made.length) return { text: `the request ${status}\nno trace was written`, error: r.code !== 0 };
            const traceDir = path.join(dir, made.sort().pop());
            return { text: truncate(`the request ${status}\ntrace: ${rel(traceDir)}\n\n` + readText(path.join(traceDir, 'root.txt'))) };
        },
    },
    {
        name: 'pad_php',
        title: 'Run PHP in a PAD application',
        description: 'Run PHP statements inside a PAD application after the engine has booted it: the engine\'s library (padArrGet, padStrSlug, padCronNext, padEval, db, padCurl ...), the application\'s _lib, its configuration and database are all there, $GLOBALS holds the request\'s variables. Answers what the code echoed, what it returned (var_export) or what it threw - a warning or notice is thrown too. For trying a helper or an engine function with odd input while hunting a bug, without writing a page. Runs in a request for an empty page of the application, after its _inits.php.',
        inputSchema: {
            type: 'object',
            properties: {
                code: { type: 'string', description: "PHP statements, without <?php: return padCronNext ( '*/5 * * * *', 0 );" },
                app: { type: 'string', description: "The application to boot - default 'test' (the scratch application, _common off)" },
                query,
            },
            required: ['code'],
        },
        run: async (a) => withSnippet(a, async (b) => {
            const r = await render({ app: b.app || 'test', page: unique('mcpPhp'), template: '', query: b.query, bare: true, codeFile: b.codeFile });
            if (!r.php && r.code !== 0) return { text: 'the request ended before the snippet answered\n' + describe(r, ''), error: true };
            return snippetText(r, b.codeFile);
        }),
    },
    {
        name: 'pad_compare',
        title: 'Compare two versions of PAD',
        description: 'Render the same page or template - or run the same PHP - on two versions of the PAD engine, and show whether the answers differ and how: by default the commit HEAD against the working tree, so a fix is shown to change what it should and nothing else, and a new regression case is shown to fail on the old code. The old version is a snapshot of pad/, editors/render.php, home/ and www/ taken from git (kept for the next call; the checkout is not touched); the applications are this working tree\'s for both - so a case not committed yet runs on the old engine - unless scope is "all". Also lists the uncommitted changes the working tree side holds, anyone\'s.',
        inputSchema: {
            type: 'object',
            properties: {
                app, page, template, query, data, bare,
                code: { type: 'string', description: 'PHP statements to run on both versions instead of a render, as pad_php runs them (app defaults to test)' },
                ref: { type: 'string', description: "The old version: a commit, branch or tag - default 'HEAD'" },
                to: { type: 'string', description: 'The new version: a commit, branch or tag - default the working tree' },
                scope: { type: 'string', enum: ['engine', 'all'], description: "'engine' (default): only the engine is the ref's, the applications are the working tree's; 'all': the applications are the ref's too" },
            },
        },
        run: async (a) => withSnippet(a, async (b) => {
            const scope = b.scope === 'all' ? 'all' : 'engine';
            const old = await snapshot(b.ref || 'HEAD', scope);
            const now = b.to ? await snapshot(b.to, scope) : CURRENT;
            // One page name for both sides - a title or a message that names the page then
            // says the same on both.
            const ask = b.codeFile
                ? Object.assign({}, b, { app: b.app || 'test', page: unique('mcpPhp'), template: '', bare: true, data: undefined })
                : Object.assign({}, b, { dataName: unique('mcpData') });
            if (!ask.app) throw new Error('app is needed - the application to render in');
            // A path into a tree is written from the tree's root on both sides: the snapshot
            // lives elsewhere, and its absolute paths made every error report differ.
            const side = async (tree) => {
                const r = await render(ask, tree);
                r.codeFile = b.codeFile;
                const src = b.template !== undefined ? b.template : (b.codeFile ? '' : pageSource(ask.app, ask.page, tree.apps));
                // The longest form first: the absolute path stands inside the relative one.
                let text = outcome(r, src);
                const forms = [];
                for (const root of new Set([tree.home, r.tree])) {
                    forms.push(root + path.sep);
                    if (path.relative(home, root)) forms.push(path.relative(home, root) + '/');
                }
                for (const form of forms.sort((x, y) => y.length - x.length)) text = text.split(form).join('');
                return { r, text };
            };
            const [x, y] = await Promise.all([side(old), side(now)]);
            const head = [`old: ${old.label}`, `new: ${now.label}`];
            if (now === CURRENT) {
                const dirty = await uncommitted(scope);
                head.push(dirty.length ? 'the working tree holds uncommitted changes the new side runs with:\n  ' + dirty.slice(0, 30).join('\n  ') + (dirty.length > 30 ? `\n  ... ${dirty.length - 30} more` : '')
                    : 'the working tree holds no uncommitted change to the engine');
            }
            if (x.text === y.text) return { text: truncate(head.join('\n') + '\n\nthe same on both:\n\n' + (x.text || '(no output)')) };
            const p = parting(x.text, y.text);
            const diff = await unified(x.text, y.text, old.label, now.label);
            return { text: truncate(head.join('\n') + `\n\nthey differ - first at character ${p.at} (line ${p.line}), after ${p.before}:\n  old: ${p.want}\n  new: ${p.got}\n\n` + diff) };
        }),
    },
    {
        name: 'pad_case',
        title: 'Run one regression test',
        description: 'Run one test of the PAD regression suites the way its suite runs it - fetched over HTTP from the running web server, compared with its answer by the suite\'s own comparison (the runner\'s functions in apps/regression/main/_lib/regression.php) - and answer ok, FAILED with want against got, or new with what came back. app and page name the test page: regression/pages, regression/common and regression/errors cases (the .txt beside the page), regression/framework cases, or any other application\'s page, judged by the covering suite whose store holds its answer. Seconds, where pad_test runs every test.',
        inputSchema: {
            type: 'object',
            properties: {
                app: { type: 'string', description: "The test page's application: 'regression/pages', 'regression/framework', 'manual' ..." },
                page: { type: 'string', description: "The test page, as in its URL: 'forms/client_parity', 'tags/a_built_in_function_as_a_tag'" },
                host: { type: 'string', description: "The base the applications are served under, default $PAD_HOST or 'http://localhost/pad/'" },
            },
            required: ['app', 'page'],
        },
        run: async (a) => {
            const dir = appPath(a.app);
            checkPage(a.page);
            const base = path.join(dir, ...a.page.split('/'));
            if (!['.pad', '.php', '.html'].some((e) => isFile(base + e) || isFile(path.join(base, 'index' + e))))
                throw new Error(`there is no page '${a.page}' in ${rel(dir)} - no .pad, .php or .html of that name`);
            const host = a.host || process.env.PAD_HOST || 'http://localhost/pad/';
            if (!/^https?:\/\/[^\s'"]+$/.test(host)) throw new Error(`'${host}' is not a URL`);
            if (!isFile(path.join(appsDir, 'regression', 'main', '_lib', 'regression.php'))) throw new Error('there is no regression runner in ' + rel(appsDir));
            const code = `
                $app  = ${JSON.stringify(a.app)};
                $name = ${JSON.stringify(a.page)};
                $judge = function ( $suite, $name, $url, $want, $framework = FALSE ) {
                  $curl = padCurl ( getSuiteFetchInput ( $url ) );
                  $code = $curl ['result'];
                  $got  = $framework ? trim ( preg_replace ( '/\\n\\s*/', '', $curl ['data'] ) ) : trim ( $curl ['data'] );
                  if ( ! file_exists ( $want ) )
                    return [ 'suite' => $suite, 'name' => $name, 'url' => $url, 'answer' => $want, 'status' => 'new', 'code' => $code, 'got' => $got ];
                  $expect = trim ( padFileGet ( $want ) );
                  list ( $ok, $shown ) = getSuiteCompare ( $expect, $got, $code, $framework ? $curl ['data'] : trim ( $curl ['data'] ) );
                  return [ 'suite' => $suite, 'name' => $name, 'url' => $url, 'answer' => $want, 'status' => $ok ? 'ok' : 'FAILED', 'code' => $code, 'want' => $expect, 'got' => $shown ];
                };
                foreach ( getSuites () as $suite => $entry )
                  if ( ( $entry ['walk'] ?? '' ) === $app )
                    return $judge ( $suite, $name, getSuiteUrl ( $app, $name ), getSuiteWantFile ( $app, $name ) );
                if ( $app === 'regression/framework' )
                  return $judge ( 'framework', $name, getFrameworkUrl ( $name ), getFrameworkDir () . "$name.txt", TRUE );
                foreach ( getSuites () as $suite => $entry )
                  if ( isset ( $entry ['over'] ) and in_array ( $app, getSuiteOverApps ( $entry ['over'] ) ) )
                    return $judge ( $suite, substr ( "$app/$name", strlen ( $entry ['strip'] ) ), getSuiteUrl ( $app, $name ),
                                    APPS . $entry ['store'] . '/' . substr ( "$app/$name", strlen ( $entry ['strip'] ) ) . '.txt' );
                return [ 'status' => 'none', 'message' => "no suite covers the application $app" ];`;
            return withSnippet({ code }, async (b) => {
                const r = await render({ app: 'regression/main', page: unique('mcpPhp'), template: '', bare: true, codeFile: b.codeFile, host });
                const t = r.php && r.php.json;
                if (!t || typeof t !== 'object') {
                    const said = r.php ? snippetText(r, b.codeFile).text : describe(r, '');
                    return { text: 'the runner did not answer\n' + said, error: true };
                }
                if (t.status === 'none') return { text: t.message, error: true };
                const answer = t.answer ? rel(t.answer) : '';
                const lines = [`${t.suite}: ${t.name} - ${t.status}`, `url: ${t.url}`, `answer: ${answer}` + (t.status === 'new' ? ' (not there yet)' : '')];
                if (t.status === 'ok') return { text: lines.join('\n') + (/^(HTTP|matches)/.test(t.got) ? '\n' + t.got : '') };
                if (t.status === 'new') return { text: truncate(lines.join('\n') + `\nHTTP ${t.code}, it came back:\n\n` + t.got) };
                const want = String(t.want), got = String(t.got);
                if (want.length + got.length > 600) {
                    const p = parting(want, got);
                    lines.push(`want and got part at character ${p.at} (line ${p.line}), after ${p.before}`, `  want: ${p.want}`, `  got:  ${p.got}`);
                }
                return { text: truncate(lines.join('\n') + '\n\nwant:\n' + want + '\n\ngot:\n' + got) };
            });
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
        description: 'Run ./ci.sh - all eight regression suites, fetched from a running PAD web server, plus the editor tooling checks - and report each suite\'s line and every failing test, each as the place where what was wanted and what came back part. With suite, run that one suite only (pages, common, errors, framework, regression, sequence, manual, other) - seconds instead of a minute, for checking a fix before the full gate. ./ci.sh takes 15 to 60 seconds; a commit needs it green. Either writes the suites\' results in DATA/suites/ - a ./ci.sh running at the same moment reads a result written by this run as another run\'s and fails.',
        inputSchema: {
            type: 'object',
            properties: {
                host: { type: 'string', description: "The base the applications are served under, default $PAD_HOST or 'http://localhost/pad/'" },
                suite: { type: 'string', enum: SUITES, description: 'Run this suite only, not the whole gate' },
            },
        },
        run: async (a) => {
            const host = a.host || process.env.PAD_HOST || 'http://localhost/pad/';
            if (!/^https?:\/\/[^\s'"]+$/.test(host)) throw new Error(`'${host}' is not a URL`);
            const suites = process.env.CI_SUITES || path.join(home, 'DATA', 'suites');
            if (a.suite) {
                if (!SUITES.includes(a.suite)) throw new Error(`no suite '${a.suite}' - one of ${SUITES.join(', ')}`);
                // The suite's own Test, as the runner's overview starts it, with a run token of
                // this call's: a result that does not carry it is another run's, not this one's.
                const token = crypto.randomBytes(6).toString('hex');
                const trigger = process.env.CI_TRIGGER ? process.env.CI_TRIGGER + `&suite=${a.suite}` : `${host}regression/main/?${a.suite}/index&test`;
                const r = await run('curl', ['-s', '-o', '/dev/null', '-w', '%{http_code}', '-L', '--max-time', '600', `${trigger}&ciRun=${token}`]);
                if (!/^[23]/.test(r.out)) throw new Error(`${host}regression/ answered HTTP ${r.out || 'nothing'}` + (r.err ? ' - ' + r.err.trim() : ''));
                let data = null;
                try { data = JSON.parse(readText(path.join(suites, a.suite + '.json'))); } catch (e) { data = null; }
                if (!data) throw new Error(`the ${a.suite} suite left no result in ${rel(suites) || suites}`);
                if (data.run !== token) throw new Error(`the ${a.suite} result is another run's - did the server run the suite? (${data.summary})`);
                const failing = failures(data, a.suite);
                const verdict = (data.failed || 0) === 0 && (data.new || 0) === 0 ? 'passed' : 'FAILED';
                return { text: truncate(`${a.suite}: ${verdict} - ${data.summary}` + (failing.length ? '\n\n' + failing.join('\n\n') : '')) };
            }
            const r = await new Promise((resolve) => {
                const child = spawn('bash', [path.join(home, 'ci.sh'), host], { cwd: home, env: Object.assign({}, process.env, { PAD_HOME: home }) });
                let out = '';
                const timer = setTimeout(() => child.kill('SIGKILL'), 600000);
                child.stdout.on('data', (d) => { out += d; });
                child.stderr.on('data', (d) => { out += d; });
                child.on('error', (e) => { clearTimeout(timer); resolve({ code: -1, out: String(e.message) }); });
                child.on('close', (code) => { clearTimeout(timer); resolve({ code, out }); });
            });
            const failing = [];
            for (const f of (isDir(suites) ? fs.readdirSync(suites) : []).filter((n) => n.endsWith('.json')).sort()) {
                let data = null;
                try { data = JSON.parse(readText(path.join(suites, f))); } catch (e) { continue; }
                failing.push(...failures(data, f.replace(/\.json$/, '')));
            }
            const verdict = r.code === 0 ? 'all suites passed' : `FAILED (ci.sh exit ${r.code})`;
            return { text: truncate(`${verdict}\n\n${r.out.trim()}` + (failing.length ? '\n\nfailing tests:\n' + failing.join('\n\n') : '')) };
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
                + 'afterwards run pad_check on it - the engine\'s strict syntax check - or pad_render to see the output. '
                + 'Hunting a bug: pad_render a template with data to reproduce it, pad_php to try a helper with odd input, '
                + 'pad_compare to see a fix change what it should against HEAD and a new regression case fail on the old engine, '
                + 'pad_case for one regression test, pad_test with suite for one suite, pad_test alone for the whole gate.',
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
