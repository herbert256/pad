#!/usr/bin/env node
// PAD language server (stdio, no dependencies).
//
// completion   the built-in tags, pipe functions, properties, options, type prefixes and
//              sequence types (completions.json, generated from pad/ by
//              editors/generate.php), and after {/ the tags still open, innermost first
// diagnostics  when a page is opened or saved, PAD itself renders it - editors/render.php,
//              on the command line, under the strict syntax check - and an error comes back
//              as the JSON body of pad/error/claude.php; it is placed on the tag it quotes.
//              A guess at PAD syntax in JavaScript would drift from the engine; asking the
//              engine cannot
// hover        the section, or the table row, of docs/reference/*.md for the tag, function,
//              option, property, type prefix or construct under the cursor; on a field, the
//              lines that assign it
// definition   PAD's own lookup order: {mytag} to the nearest _tags/mytag.* up the
//              directories, then _common, then the engine's pad/tags; {include:x} to
//              _include/x.pad; | money to _functions/money.php; callback='x' to
//              _callbacks/x.php; and {$total} in orders.pad to the line of orders.php - or
//              of a _inits.php, _exits.php, _lib file or {set} - that assigns it

const fs = require('fs');
const path = require('path');
const url = require('url');
const { execFile } = require('child_process');

const COMPLETIONS = JSON.parse(
    fs.readFileSync(path.join(__dirname, 'completions.json'), 'utf8'));

// LSP CompletionItemKind numbers
const KIND = {
    Keyword: 14, Function: 3, Property: 10, EnumMember: 20,
    Module: 9, Operator: 24, Class: 7, Snippet: 15,
};

// A ~ just inside the brace is whitespace control - {~items~}, {~/items} - and no part of
// the name: without it here such a pair was never opened or never closed for the close-tag help.
const TAG_RE = /\{~?(\/?)([A-Za-z_][A-Za-z0-9_]*(?::[A-Za-z_][A-Za-z0-9_]*)?)([^{}]*)/g;
const SINGLE_TAGS = new Set([
    'set', 'get', 'echo', 'increment', 'decrement', 'redirect', 'restart',
    'exit', 'break', 'continue', 'cease', 'dump', 'error', 'exception',
    'open', 'close', 'null', 'true', 'false', 'flag', 'page', 'curl',
    'exists', 'at', 'resume', 'switch', 'ajax', 'reactData', 'file',
    'make', 'keep', 'remove', 'action',
    'debug', 'attrs', 'classes', 'trans', 'nonce', 'csrf', 'stack', 'recurse', 'parent',
    'extends', 'meta', 'parms', 'assert', 'flush', 'sparkline', 'chart', 'input', 'textarea',
]);

// The branch words of {if} and {case} divide a pair; they never open one of their own.
const BRANCH_TAGS = new Set(['else', 'elseif', 'when']);

// The comments the engine drops before it scans - {# ... #}, not the option sigil {#name},
// and {-- ... --} - with any tag written inside them.
function stripComments(text) {
    return text
        .replace(/\{#(?![A-Za-z_][A-Za-z0-9_]*\s*[}|])[\s\S]*?#\}/g, '')
        .replace(/\{--\s[\s\S]*?--\}/g, '');
}

const NAME = '[A-Za-z_][A-Za-z0-9_]*';

const documents = new Map();

// Settings the client can pass as initializationOptions: the php binary, diagnostics on or
// off, and how long one render may take.
const settings = {
    php: process.env.PAD_LSP_PHP || 'php',
    diagnostics: true,
    timeout: 15000,
};

// ---------------- completion ----------------

function textBefore(uri, position) {
    const text = documents.get(uri) || '';
    const lines = text.split('\n');
    const upto = lines.slice(0, position.line);
    upto.push(lines[position.line] ? lines[position.line].slice(0, position.character) : '');
    return upto.join('\n');
}

function openTags(before) {
    const stack = [];
    let m;
    before = stripComments(before);
    TAG_RE.lastIndex = 0;
    while ((m = TAG_RE.exec(before)) !== null) {
        if (m[1]) {
            const i = stack.lastIndexOf(m[2]);
            if (i >= 0) stack.length = i;
        } else if (!SINGLE_TAGS.has(m[2]) && !BRANCH_TAGS.has(m[2]) && !m[3].replace(/~$/, '').trimEnd().endsWith('/')) {
            stack.push(m[2]);
        }
    }
    return stack;
}

function completion(params) {
    const before = textBefore(params.textDocument.uri, params.position);

    const closeMatch = before.match(/\{~?\/([A-Za-z0-9_:]*)$/);
    if (closeMatch) {
        const stack = openTags(before.slice(0, -closeMatch[0].length));
        return stack.reverse().map((name, i) => ({
            label: name,
            kind: KIND.Snippet,
            detail: 'close tag',
            insertText: name + '}',
            sortText: String(i).padStart(3, '0'),
        }));
    }

    // only complete inside a PAD tag
    const open = before.lastIndexOf('{');
    if (open < 0 || before.indexOf('}', open) >= 0) return [];
    // past the ~ of whitespace control - {~ec - as past the brace itself
    const first = before.charAt(open + 1) === '~' ? before.charAt(open + 2) : before.charAt(open + 1);
    if (!/[/A-Za-z_$%!@]/.test(first || '')) return [];

    return COMPLETIONS.map((c) => ({
        label: c.label,
        kind: KIND[c.kind] || 1,
        detail: c.detail,
        insertText: c.insert || c.label,
    }));
}

// ---------------- where a file lives ----------------

function uriToPath(uri) {
    try { return uri.startsWith('file:') ? url.fileURLToPath(uri) : null; } catch (e) { return null; }
}

function pathToUri(file) {
    return url.pathToFileURL(file).href;
}

function isFile(file) {
    try { return fs.statSync(file).isFile(); } catch (e) { return false; }
}

function isDir(dir) {
    try { return fs.statSync(dir).isDirectory(); } catch (e) { return false; }
}

// The checkout (the nearest directory holding pad/pad.php), the applications directory and
// the application a file belongs to. Applications nest - apps/regression/errors is one -
// so the application is the longest directory chain with an entry point www/<app>/index.php;
// outside the checkout's own apps/ (the editors' test fixture) it is the first directory.
// The context directory is the one PAD searches from: the file's own, cut above the first
// _xxx directory, so _include/footer.pad looks where the page beside _include/ would.
function locate(file) {
    let home = null;
    for (let d = path.dirname(file); ; d = path.dirname(d)) {
        if (isFile(path.join(d, 'pad', 'pad.php'))) { home = d; break; }
        if (path.dirname(d) === d) break;
    }
    if (!home) {
        const own = path.resolve(__dirname, '..', '..');
        if (isFile(path.join(own, 'pad', 'pad.php'))) home = own;
    }

    let apps = null;
    if (home && file.startsWith(path.join(home, 'apps') + path.sep)) {
        apps = path.join(home, 'apps');
    } else {
        for (let d = path.dirname(file); path.dirname(d) !== d; d = path.dirname(d))
            if (path.basename(d) === 'apps') { apps = d; break; }
    }
    if (!home || !apps) return { home, apps: null };

    const parts = path.relative(apps, file).split(path.sep);
    if (parts.length < 2) return { home, apps: null };

    let appParts = 1;
    if (apps === path.join(home, 'apps'))
        for (let n = parts.length - 1; n >= 1; n--)
            if (isFile(path.join(home, 'www', ...parts.slice(0, n), 'index.php'))) { appParts = n; break; }

    const app = parts.slice(0, appParts).join('/');
    const appDir = path.join(apps, ...parts.slice(0, appParts));
    const inApp = parts.slice(appParts);                 // e.g. ['admin', '_include', 'x.pad']
    const dirs = inApp.slice(0, -1);
    const cut = dirs.findIndex((d) => d.startsWith('_'));
    const context = cut < 0 ? dirs : dirs.slice(0, cut);

    return { home, apps, app, appDir, inApp, context, privateDir: cut >= 0 };
}

// The directories PAD searches, nearest first: the context directory up to the application
// root - padDirs() in pad/lib/paths.php.
function chain(loc) {
    const out = [];
    for (let n = loc.context.length; n >= 0; n--)
        out.push(path.join(loc.appDir, ...loc.context.slice(0, n)));
    return out;
}

function existing(files) {
    return files.filter(isFile);
}

// _tags/x, _include/x, _functions/x, _callbacks/x: the first directory of the chain that
// has one, .php and .pad alike, as padAppCheck finds it.
function inChain(loc, sub, name, exts) {
    for (const dir of chain(loc)) {
        const found = existing(exts.map((e) => path.join(dir, sub, name + e)));
        if (found.length) return found;
    }
    return [];
}

function inCommon(loc, sub, name, exts) {
    return existing(exts.map((e) => path.join(loc.apps, '_common', sub, name + e)));
}

function inEngine(loc, sub, name) {
    return existing([path.join(loc.home, 'pad', sub, name + '.php')]);
}

const TAG_EXT = ['.php', '.pad', '.html'];

function tagFiles(loc, name) {
    let f = inChain(loc, '_tags', name, TAG_EXT);
    if (!f.length) f = inCommon(loc, '_tags', name, TAG_EXT);
    if (!f.length) f = inEngine(loc, 'tags', name);
    return f;
}

function includeFiles(loc, name) {
    let f = inChain(loc, '_include', name, ['.pad', '.html', '.php']);
    if (!f.length) f = inCommon(loc, '_include', name, ['.pad', '.html', '.php']);
    return f;
}

function functionFiles(loc, name) {
    let f = inChain(loc, '_functions', name, ['.php']);
    if (!f.length) f = inEngine(loc, 'functions', name);
    if (!f.length) f = inCommon(loc, '_functions', name, ['.php']);
    return f;
}

// ---------------- assignments of a field ----------------

function positionAt(text, offset) {
    const before = text.slice(0, offset);
    const line = before.split('\n').length - 1;
    return { line, character: offset - before.lastIndexOf('\n') - 1 };
}

function offsetAt(text, position) {
    const lines = text.split('\n');
    let offset = 0;
    for (let i = 0; i < position.line && i < lines.length; i++) offset += lines[i].length + 1;
    return offset + Math.min(position.character, (lines[position.line] || '').length);
}

function readText(file) {
    try { return fs.readFileSync(file, 'utf8'); } catch (e) { return ''; }
}

function hits(file, text, re, group) {
    const out = [];
    re.lastIndex = 0;
    let m;
    while ((m = re.exec(text)) !== null) {
        const at = m.index + m[0].indexOf(m[group]);
        out.push({
            file,
            range: { start: positionAt(text, at), end: positionAt(text, at + m[group].length) },
            line: text.split('\n')[positionAt(text, at).line].trim(),
        });
    }
    return out;
}

// Where a field gets its value, nearest first: the page's paired .php, the template's own
// {set $x = ...} or {tag %x = ...}, the _inits.php and _exits.php of the directory chain,
// then the _lib files. Only when none assigns it, an array key 'x' => - a row field.
function assignments(loc, name, docFile, docText) {
    const esc = name.replace(/[^A-Za-z0-9_]/g, '');
    const phpRe = () => new RegExp('(\\$' + esc + ')\\b\\s*(?:\\[[^\\]\\n]*\\]\\s*)*(?:=(?![=>])|\\.=|\\+=|-=|\\*=|\\?\\?=)', 'g');
    const padRe = () => new RegExp('\\{[^{}]*?([$%]' + esc + ')\\s*=(?![=>])', 'g');
    const keyRe = () => new RegExp('([\'"]' + esc + '[\'"])\\s*=>', 'g');

    const files = [];
    if (/\.(pad|html)$/.test(docFile) && !loc.privateDir && !path.basename(docFile).startsWith('_'))
        files.push(docFile.replace(/\.(pad|html)$/, '.php'));
    const dirs = chain(loc);
    const inits = dirs.map((d) => path.join(d, '_inits.php'));
    const exits = dirs.map((d) => path.join(d, '_exits.php'));
    const libs = [];
    for (const d of dirs) {
        const lib = path.join(d, '_lib');
        if (isDir(lib)) for (const f of fs.readdirSync(lib).sort()) if (f.endsWith('.php')) libs.push(path.join(lib, f));
    }

    let out = [];
    for (const f of existing(files)) out = out.concat(hits(f, readText(f), phpRe(), 1));
    out = out.concat(hits(docFile, docText, padRe(), 1));
    for (const f of existing(inits.concat(exits, libs))) out = out.concat(hits(f, readText(f), phpRe(), 1));
    if (out.length) return out;

    for (const f of existing(files.concat(inits, exits))) out = out.concat(hits(f, readText(f), keyRe(), 1));
    return out;
}

// ---------------- the word under the cursor ----------------

// What the cursor is on, in PAD's terms: a tag name ({badge}, {/badge}, {app:badge}), a
// type prefix (app:), a pipe function (| money), a property (first@items), a field
// ($total, %total, !total, ?total), an option (sort, callback=), a callback name
// (callback='x') or a construct (@page@).
function contextAt(text, offset) {
    const isWord = (c) => /[A-Za-z0-9_]/.test(c || '');
    let s = offset, e = offset;
    if (!isWord(text[s]) && /[$%!?@{|]/.test(text[s] || '') && isWord(text[s + 1])) { s++; e++; }
    while (s > 0 && isWord(text[s - 1])) s--;
    while (isWord(text[e])) e++;
    if (s === e) return null;
    const word = text.slice(s, e);
    if (!/^[A-Za-z_]/.test(word)) return null;

    const before = text[s - 1] || '';
    const after = text[e] || '';

    if (before === '@' && after === '@') return { role: 'construct', word, start: s - 1, end: e + 1 };

    // the tag around the cursor: the last { before it with no } in between
    const open = text.lastIndexOf('{', s);
    const inTag = open >= 0 && text.slice(open, s).indexOf('}') < 0;
    const head = inTag ? text.slice(open + 1, s) : '';

    if ('$%!?'.includes(before)) return { role: 'field', word, start: s - 1, end: e };
    if (after === '@' && inTag) return { role: 'property', word, start: s, end: e + 1 };
    if (!inTag) return null;

    if (after === ':' && /^(?:~?\/?~?)$/.test(head)) return { role: 'prefix', word, start: s, end: e + 1 };
    if (after === ':' && /\|\s*$/.test(head)) return { role: 'prefix', word, start: s, end: e + 1 };

    const prefixed = head.match(new RegExp('(' + NAME + '):$'));
    if (prefixed) {
        const lead = head.slice(0, -prefixed[0].length);
        if (/^(?:~?\/?~?)$/.test(lead)) return { role: 'tag', word, prefix: prefixed[1], start: s - prefixed[0].length, end: e };
        if (/\|\s*$/.test(lead)) return { role: 'function', word, prefix: prefixed[1], start: s - prefixed[0].length, end: e };
    }

    if (/^(?:~?\/?~?)$/.test(head)) return { role: 'tag', word, start: s, end: e };
    if (/\|\s*$/.test(head)) return { role: 'function', word, start: s, end: e };
    if (/callback\s*=\s*['"]$/.test(head)) return { role: 'callback', word, start: s, end: e };
    if (/['"]$/.test(head)) return null;
    if (/[\s,]$/.test(head)) return { role: 'option', word, start: s, end: e };
    return null;
}

// ---------------- the reference documentation ----------------

// The sections and table rows of docs/reference/*.md, shared with the MCP server.
const reference = require(path.join(__dirname, '..', 'reference.js'));

function docText(home, role, word) {
    return reference.lookup(home, role, word, 2).map((e) => e.text).join('\n\n---\n\n');
}

// The opening comment of an application's own tag or function file, or the first lines of
// a template - what the author wrote about it.
function fileSummary(file) {
    const text = readText(file);
    if (file.endsWith('.php')) {
        const comment = [];
        for (const line of text.split('\n').slice(1)) {
            const m = line.match(/^\s*\/\/ ?(.*)$/);
            if (m) comment.push(m[1]);
            else if (comment.length || line.trim()) break;
        }
        return comment.join('\n');
    }
    return '```\n' + text.split('\n').slice(0, 10).join('\n').trim() + '\n```';
}

// ---------------- hover and definition ----------------

function rel(loc, file) {
    return path.relative(loc.home, file).split(path.sep).join('/');
}

// The document, its text and where it lives. A file outside any application still gets
// the engine's own files and the reference: it is placed in an application that has none.
function located(uri) {
    const file = uriToPath(uri);
    if (!file) return null;
    const text = documents.has(uri) ? documents.get(uri) : readText(file);
    const loc = locate(file);
    if (!loc.home) return null;
    if (!loc.apps)
        Object.assign(loc, { apps: path.join(loc.home, 'apps'), appDir: path.join(loc.home, 'apps', '.none'),
                             app: '', inApp: [path.basename(file)], context: [], privateDir: true });
    return { file, text, loc };
}

// What the word under the cursor resolves to, in PAD's lookup order. Each target says what
// it is: 'file' an application's own (_tags, _include, _functions, _callbacks, _common),
// 'engine' a file under pad/, 'assign' a line that assigns a field, 'store' a {data} or
// {content} block of this template.
function targets(loc, ctx, docFile, text) {
    const top = { start: { line: 0, character: 0 }, end: { line: 0, character: 0 } };
    const as = (kind) => (files) => files.map((f) => ({ file: f, range: top, kind }));
    const own = as('file'), engine = as('engine');
    const assigned = () => assignments(loc, w, docFile, text).map((a) => Object.assign(a, { kind: 'assign' }));
    const stored = (kinds) => hits(docFile, text, new RegExp('\\{(?:' + kinds + ")\\s+(['\"]" + w + "['\"])", 'g'), 1)
        .map((a) => Object.assign(a, { kind: 'store' }));
    const w = ctx.word;
    switch (ctx.role) {
    case 'tag': {
        const p = ctx.prefix;
        if (p === 'app') return own(inChain(loc, '_tags', w, TAG_EXT));
        if (p === 'common') return own(inCommon(loc, '_tags', w, TAG_EXT).concat(inCommon(loc, '_include', w, ['.pad', '.html', '.php'])));
        if (p === 'pad') return engine(inEngine(loc, 'tags', w));
        if (p === 'include') return own(includeFiles(loc, w));
        if (p === 'function') return functionTargets(loc, w);
        if (p === 'property') return engine(inEngine(loc, 'properties', w));
        if (p === 'field' || p === 'array' || p === 'level') return assigned();
        if (p === 'data' || p === 'content') return stored(p);
        if (p) return [];
        let f = inChain(loc, '_tags', w, TAG_EXT).concat(inCommon(loc, '_tags', w, TAG_EXT));
        if (f.length) return own(f.slice(0, f[0].endsWith('.php') && f[1] && path.dirname(f[1]) === path.dirname(f[0]) ? 2 : 1));
        f = inEngine(loc, 'tags', w);
        if (f.length) return engine(f);
        const store = stored('data|content');
        if (store.length) return store;
        f = includeFiles(loc, w);
        if (f.length) return own(f.slice(0, 1));
        return assigned();
    }
    case 'prefix': return engine(inEngine(loc, 'types', w));
    case 'function':
        if (ctx.prefix && ctx.prefix !== 'function') return [];
        return functionTargets(loc, w);
    case 'property': return engine(inEngine(loc, 'properties', w));
    case 'option': return engine(inEngine(loc, 'options', w).concat(inEngine(loc, path.join('handling', 'types'), w)));
    case 'callback': return own(inChain(loc, '_callbacks', w, ['.php']));
    case 'field': return assigned();
    case 'construct': return engine(inEngine(loc, 'constructs', w));
    default: return [];
    }
}

function functionTargets(loc, w) {
    const top = { start: { line: 0, character: 0 }, end: { line: 0, character: 0 } };
    let f = inChain(loc, '_functions', w, ['.php']);
    if (f.length) return f.map((file) => ({ file, range: top, kind: 'file' }));
    f = inEngine(loc, 'functions', w);
    if (f.length) return f.map((file) => ({ file, range: top, kind: 'engine' }));
    f = inCommon(loc, '_functions', w, ['.php']);
    return f.map((file) => ({ file, range: top, kind: 'file' }));
}

function definition(params) {
    const d = located(params.textDocument.uri);
    if (!d) return null;
    const ctx = contextAt(d.text, offsetAt(d.text, params.position));
    if (!ctx) return null;
    return targets(d.loc, ctx, d.file, d.text).map((t) => ({ uri: pathToUri(t.file), range: t.range }));
}

function hover(params) {
    const d = located(params.textDocument.uri);
    if (!d) return null;
    const ctx = contextAt(d.text, offsetAt(d.text, params.position));
    if (!ctx) return null;
    const loc = d.loc;
    const range = { start: positionAt(d.text, ctx.start), end: positionAt(d.text, ctx.end) };
    const found = targets(loc, ctx, d.file, d.text);
    const first = found[0];
    let value = '';

    if (first && first.kind === 'assign') {
        value = found.slice(0, 3).map((f) => '`' + rel(loc, f.file) + ':' + (f.range.start.line + 1) + '`\n```php\n' + f.line + '\n```').join('\n');
    } else if (first && first.kind === 'store') {
        value = '**{' + ctx.word + '}** - defined in this template, line ' + (first.range.start.line + 1) + '\n```\n' + first.line + '\n```';
    } else if (first && first.kind === 'file') {
        const what = { _tags: 'application tag', _include: 'include', _functions: 'application function', _callbacks: 'callback' }[path.basename(path.dirname(first.file))] || 'file';
        const shown = ctx.role === 'function' ? '| ' + ctx.word : ctx.role === 'callback' ? ctx.word : '{' + ctx.word + '}';
        value = '**' + shown + '** - ' + what + ' `' + rel(loc, first.file) + '`\n\n' + fileSummary(first.file);
    } else if (ctx.role === 'tag' && ctx.prefix && ctx.prefix !== 'pad') {
        value = docText(loc.home, 'prefix', ctx.prefix);
    } else if (ctx.role !== 'field' && ctx.role !== 'callback') {
        value = docText(loc.home, ctx.role, ctx.word);
    }
    if (!value.trim()) return null;
    return { contents: { kind: 'markdown', value: value.trim() }, range };
}

// ---------------- diagnostics ----------------

// The page a file stands for when it is checked: a .pad or .html outside the _xxx
// directories is a page itself; a _inits.pad or _exits.pad wraps every page of its
// directory, so the directory's index is rendered for it. Snippets (_include, _tags) have
// no page of their own and are not checked, and neither is a bracketed route -
// products/[id].pad, blog/[year]/[slug].pad: it is reached through a path that fills in its
// value, never by its own name, and rendered by name it is a page not found, which stood
// as an error on every such template.
function pageFor(loc) {
    if (!loc.apps || loc.privateDir) return null;
    if (loc.inApp.some((part) => part.includes('['))) return null;
    const base = loc.inApp[loc.inApp.length - 1];
    const dirs = loc.inApp.slice(0, -1);
    if (!/\.(pad|html)$/.test(base)) return null;
    if (/^_(inits|exits)\.(pad|html)$/.test(base)) {
        const index = path.join(loc.appDir, ...dirs, 'index');
        if (!['.php', '.pad', '.html'].some((e) => isFile(index + e))) return null;
        return dirs.concat(['index']).join('/');
    }
    if (base.startsWith('_')) return null;
    return dirs.concat([base.replace(/\.(pad|html)$/, '')]).join('/');
}

function fullLine(text, line) {
    const lines = text.split('\n');
    return { start: { line, character: 0 }, end: { line, character: (lines[line] || '').length } };
}

// Where the error belongs in the template: the tag the message quotes ({if 1 eq 1}), the
// source of the tag the engine was busy with (padBetweenOrg), a quoted name ('$typo') -
// the first one the text holds. An error raised in a PHP file of this template's own
// application is reported with that file and line; one PAD cannot place goes on line 1.
function diagnosticFrom(out, errText, text, file, loc, page) {
    let json = null;
    try { json = JSON.parse(out); } catch (e) { json = null; }

    if (!json || typeof json.error !== 'string') {
        let msg = (out || errText || 'the render failed').replace(/<[^>]*>/g, ' ').replace(/\s+/g, ' ').trim();
        if (msg.length > 300) msg = msg.slice(0, 300) + '...';
        return { range: fullLine(text, 0), severity: 1, source: 'pad', message: msg };
    }

    let message = json.error.replace(/^PAD:\s*/, '').trim();
    const where = json.file ? path.resolve(json.file) : '';

    if (where && where === path.resolve(file) && json.line > 0)
        return { range: fullLine(text, json.line - 1), severity: 1, source: 'pad', message };

    // Raised in a PHP file of this application - a tag's, a pipe function's - the message
    // names that file and line, wherever the mark goes; set before the engine's placing
    // below, which returned without it.
    if (where && loc.appDir && where.startsWith(loc.appDir + path.sep) && where !== path.resolve(file))
        message += ' - ' + path.relative(loc.appDir, where).split(path.sep).join('/') + ':' + json.line;

    // The engine places the error itself when it can: template names the file the tag stands
    // in, its line, and its column counted in characters (code points). In this document the
    // mark goes there - the quoted text below is only found at its first spot, which put "the
    // pair {if 1 eq 1} never closes" on an earlier {if 1 eq 1} that closes fine. The column is
    // turned into the UTF-16 units LSP counts, and the mark covers the tag or the name it opens on.
    const t = json.template;
    if (t && typeof t.path === 'string' && path.resolve(t.path) === path.resolve(file) && t.line > 0 && t.column > 0) {
        const lineText = text.split('\n')[t.line - 1];
        if (lineText !== undefined) {
            const points = [...lineText];
            let character = 0;
            for (let i = 0; i < t.column - 1 && i < points.length; i++) character += points[i].length;
            const rest = lineText.slice(character);
            const tag = typeof t.tag === 'string' ? t.tag : '';
            const word = rest.match(/^[$!#&?^]?[A-Za-z_][\w:.@-]*/);
            const width = tag && rest.startsWith(tag) ? tag.length : word ? word[0].length : 1;
            return { range: { start: { line: t.line - 1, character }, end: { line: t.line - 1, character: character + width } },
                     severity: 1, source: 'pad', message };
        }
    }

    const candidates = [];
    for (const m of message.matchAll(/\{[^{}\n]+\}/g)) candidates.push(m[0]);
    const g = json.pad || {};
    for (const k of ['padBetweenOrg', 'padOrgSet', 'padBetween'])
        if (typeof g[k] === 'string' && g[k].trim()) candidates.push('{' + g[k] + '}', '{' + g[k]);
    for (const m of message.matchAll(/'([^'\n]{2,})'/g)) candidates.push(m[1]);

    let range = null;
    for (const c of candidates) {
        const at = text.indexOf(c);
        if (at >= 0) { range = { start: positionAt(text, at), end: positionAt(text, at + c.length) }; break; }
    }

    if (!range && /^_(inits|exits)\./.test(path.basename(file)))
        message += ' - rendering ?' + page;

    return { range: range || fullLine(text, 0), severity: 1, source: 'pad', message };
}

const checks = new Map();

function publish(uri, diagnostics) {
    send({ jsonrpc: '2.0', method: 'textDocument/publishDiagnostics', params: { uri, diagnostics } });
}

function check(uri) {
    if (!settings.diagnostics) return;
    const file = uriToPath(uri);
    if (!file) return;
    const loc = locate(file);
    const page = pageFor(loc);
    const render = loc.home && path.join(loc.home, 'editors', 'render.php');
    if (!page || !isFile(render)) { publish(uri, []); return; }

    const run = (checks.get(uri) || 0) + 1;
    checks.set(uri, run);

    execFile(settings.php, [render, loc.app, page, '--apps=' + loc.apps],
        { cwd: loc.home, timeout: settings.timeout, maxBuffer: 64 * 1024 * 1024 },
        (err, stdout, stderr) => {
            if (checks.get(uri) !== run) return;          // a newer check is on its way
            const text = documents.has(uri) ? documents.get(uri) : readText(file);
            if (!err) { publish(uri, []); return; }
            if (err.code === 'ENOENT') {
                send({ jsonrpc: '2.0', method: 'window/logMessage', params: { type: 2, message: 'pad-lsp: no php binary "' + settings.php + '" - diagnostics are off' } });
                settings.diagnostics = false;
                return;
            }
            if (err.killed) {
                publish(uri, [{ range: fullLine(text, 0), severity: 2, source: 'pad', message: 'the render took longer than ' + settings.timeout / 1000 + ' s' }]);
                return;
            }
            publish(uri, [diagnosticFrom(stdout, stderr, text, file, loc, page)]);
        });
}

// ---------------- JSON-RPC over stdio ----------------

let buffer = Buffer.alloc(0);

function send(msg) {
    const body = Buffer.from(JSON.stringify(msg), 'utf8');
    process.stdout.write('Content-Length: ' + body.length + '\r\n\r\n');
    process.stdout.write(body);
}

// The text of an open document, kept only when it is one: a didOpen without text, or with
// text null, left a non-string standing for the document, and the check's callback - outside
// the try round handle() - died taking it apart. Without text the file on disk is read.
function keep(uri, text) {
    if (typeof text === 'string') documents.set(uri, text);
    else documents.delete(uri);
}

function handle(msg) {
    const { id, method, params } = msg;
    if (method === 'initialize') {
        const opts = (params && params.initializationOptions) || {};
        if (typeof opts.php === 'string' && opts.php) settings.php = opts.php;
        if (opts.diagnostics === false) settings.diagnostics = false;
        if (opts.timeout > 0) settings.timeout = opts.timeout;
        send({ jsonrpc: '2.0', id, result: {
            capabilities: {
                textDocumentSync: { openClose: true, change: 1, save: { includeText: false } },
                completionProvider: { triggerCharacters: ['{', '/', ':', '$', '|'] },
                hoverProvider: true,
                definitionProvider: true,
            },
            serverInfo: { name: 'pad-lsp', version: '0.2.0' },
        } });
    } else if (method === 'initialized') {
        // notification, nothing to do
    } else if (method === 'textDocument/didOpen') {
        keep(params.textDocument.uri, params.textDocument.text);
        check(params.textDocument.uri);
    } else if (method === 'textDocument/didChange') {
        keep(params.textDocument.uri, params.contentChanges[params.contentChanges.length - 1].text);
    } else if (method === 'textDocument/didSave') {
        check(params.textDocument.uri);
    } else if (method === 'textDocument/didClose') {
        documents.delete(params.textDocument.uri);
        checks.delete(params.textDocument.uri);
        publish(params.textDocument.uri, []);
    } else if (method === 'textDocument/completion') {
        send({ jsonrpc: '2.0', id, result: completion(params) });
    } else if (method === 'textDocument/hover') {
        send({ jsonrpc: '2.0', id, result: hover(params) });
    } else if (method === 'textDocument/definition') {
        send({ jsonrpc: '2.0', id, result: definition(params) });
    } else if (method === 'shutdown') {
        send({ jsonrpc: '2.0', id, result: null });
    } else if (method === 'exit') {
        process.exit(0);
    } else if (id !== undefined) {
        // unknown request - respond so clients don't hang
        send({ jsonrpc: '2.0', id, result: null });
    }
}

process.stdin.on('data', (chunk) => {
    buffer = Buffer.concat([buffer, chunk]);
    for (;;) {
        const headerEnd = buffer.indexOf('\r\n\r\n');
        if (headerEnd < 0) return;
        const header = buffer.slice(0, headerEnd).toString('utf8');
        const m = header.match(/Content-Length: *(\d+)/i);
        if (!m) { buffer = buffer.slice(headerEnd + 4); continue; }
        const length = parseInt(m[1], 10);
        if (buffer.length < headerEnd + 4 + length) return;
        const body = buffer.slice(headerEnd + 4, headerEnd + 4 + length).toString('utf8');
        buffer = buffer.slice(headerEnd + 4 + length);
        let msg = null;
        try { msg = JSON.parse(body); } catch (e) { continue; /* ignore malformed */ }
        // JSON that is no message - null, a number - is malformed too: handle() cannot take
        // it apart, and the catch below then read msg.id of null and the server died
        if (msg === null || typeof msg !== 'object') continue;
        try { handle(msg); } catch (e) {
            if (msg.id !== undefined) send({ jsonrpc: '2.0', id: msg.id, error: { code: -32603, message: String(e && e.message || e) } });
        }
    }
});
