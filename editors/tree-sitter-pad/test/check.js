#!/usr/bin/env node
// Checks the Tree-sitter grammar, with or without the tree-sitter CLI. ci.sh runs it.
//
//   node editors/tree-sitter-pad/test/check.js
//
// Without the CLI it still proves two things:
//  - src/ is generated from grammar.js as it stands: grammar.js is evaluated with the same
//    DSL the CLI uses, and its rules must equal src/grammar.json - a grammar edited without
//    `tree-sitter generate` leaves a parser.c that no longer matches it;
//  - every query names only node types, fields and tokens the parser has (src/node-types.json),
//    since an editor rejects a whole query file over one unknown name.
// With the CLI (on the PATH, or $TREE_SITTER) it also runs `tree-sitter test`: the corpus
// in test/corpus and the highlight assertions in test/highlight.

const fs = require('fs');
const path = require('path');
const vm = require('vm');
const { spawnSync } = require('child_process');

const dir = path.resolve(__dirname, '..');
const failed = [];

// ---- grammar.js against src/grammar.json ----

function normalize(rule) {
    if (typeof rule === 'string') return { type: 'STRING', value: rule };
    if (Object.prototype.toString.call(rule) === '[object RegExp]') return rule.flags ? { type: 'PATTERN', value: rule.source, flags: rule.flags } : { type: 'PATTERN', value: rule.source };
    if (rule && typeof rule.type === 'string') return rule;
    throw new Error('not a rule: ' + JSON.stringify(rule));
}

function precedence(type) {
    return (value, rule) => rule === undefined
        ? { type, value: 0, content: normalize(value) }
        : { type, value, content: normalize(rule) };
}

const dsl = {
    seq: (...m) => ({ type: 'SEQ', members: m.map(normalize) }),
    choice: (...m) => ({ type: 'CHOICE', members: m.map(normalize) }),
    optional: (r) => ({ type: 'CHOICE', members: [normalize(r), { type: 'BLANK' }] }),
    repeat: (r) => ({ type: 'REPEAT', content: normalize(r) }),
    repeat1: (r) => ({ type: 'REPEAT1', content: normalize(r) }),
    field: (name, r) => ({ type: 'FIELD', name, content: normalize(r) }),
    blank: () => ({ type: 'BLANK' }),
    alias: (r, v) => (typeof v === 'string'
        ? { type: 'ALIAS', content: normalize(r), named: false, value: v }
        : { type: 'ALIAS', content: normalize(r), named: true, value: v.name }),
    token: Object.assign((r) => ({ type: 'TOKEN', content: normalize(r) }),
        { immediate: (r) => ({ type: 'IMMEDIATE_TOKEN', content: normalize(r) }) }),
    prec: Object.assign(precedence('PREC'), {
        left: precedence('PREC_LEFT'), right: precedence('PREC_RIGHT'), dynamic: precedence('PREC_DYNAMIC'),
    }),
};

function evaluate() {
    const sandbox = Object.assign({ module: { exports: {} }, grammar: (spec) => spec }, dsl);
    vm.runInNewContext(fs.readFileSync(path.join(dir, 'grammar.js'), 'utf8'), sandbox, { filename: 'grammar.js' });
    const spec = sandbox.module.exports;
    const $ = new Proxy({}, { get: (_, name) => ({ type: 'SYMBOL', name }) });
    const rules = {};
    for (const [name, fn] of Object.entries(spec.rules)) rules[name] = normalize(fn($, undefined));
    return {
        name: spec.name,
        word: spec.word ? spec.word($).name : undefined,
        rules,
        extras: (spec.extras ? spec.extras($) : [/\s/]).map(normalize),
        externals: (spec.externals ? spec.externals($) : []).map(normalize),
        conflicts: (spec.conflicts ? spec.conflicts($) : []).map((c) => c.map((s) => s.name)),
    };
}

function same(a, b) {
    return JSON.stringify(a) === JSON.stringify(b);
}

let checks = 0;

try {
    const mine = evaluate();
    const made = JSON.parse(fs.readFileSync(path.join(dir, 'src', 'grammar.json'), 'utf8'));
    for (const key of ['name', 'word', 'extras', 'externals', 'conflicts']) {
        checks++;
        if (!same(mine[key], made[key])) failed.push(`src/grammar.json: ${key} differs from grammar.js - run tree-sitter generate --abi 14`);
    }
    checks++;
    if (!same(Object.keys(mine.rules), Object.keys(made.rules)))
        failed.push('src/grammar.json: the rules differ from grammar.js - run tree-sitter generate --abi 14');
    for (const name of Object.keys(mine.rules)) {
        checks++;
        if (!same(mine.rules[name], made.rules[name]))
            failed.push(`src/grammar.json: rule ${name} differs from grammar.js - run tree-sitter generate --abi 14`);
    }
} catch (e) {
    failed.push('grammar.js: ' + e.message);
}

// ---- the queries against src/node-types.json ----

const types = JSON.parse(fs.readFileSync(path.join(dir, 'src', 'node-types.json'), 'utf8'));
const named = new Set(types.filter((t) => t.named).map((t) => t.type));
const anonymous = new Set(types.filter((t) => !t.named).map((t) => t.type));
const fields = new Set();
for (const t of types) for (const f of Object.keys(t.fields || {})) fields.add(f);

const queries = ['queries/highlights.scm', 'queries/injections.scm', 'queries/folds.scm',
    'queries/textobjects.scm', 'helix/highlights.scm', 'helix/textobjects.scm'];

for (const q of queries) {
    checks++;
    const file = path.join(dir, q);
    if (!fs.existsSync(file)) { failed.push(q + ': missing'); continue; }
    // comments, then the predicates - their strings are values, not tokens
    const text = fs.readFileSync(file, 'utf8')
        .split('\n').map((l) => l.replace(/;.*$/, '')).join('\n')
        .replace(/\(#[^()]*\)/g, '');
    if ((text.match(/\(/g) || []).length !== (text.match(/\)/g) || []).length)
        failed.push(q + ': unbalanced parentheses');
    for (const m of text.matchAll(/\((\w+)/g))
        if (m[1] !== '_' && !named.has(m[1])) failed.push(`${q}: no node type '${m[1]}'`);
    for (const m of text.matchAll(/"((?:[^"\\]|\\.)*)"/g))
        if (!anonymous.has(m[1])) failed.push(`${q}: no token "${m[1]}"`);
    for (const m of text.matchAll(/(\w+):/g))
        if (!fields.has(m[1])) failed.push(`${q}: no field '${m[1]}'`);
}

// ---- the corpus and highlight tests, when the CLI is there ----

function findCli() {
    if (process.env.TREE_SITTER) return process.env.TREE_SITTER;
    const which = spawnSync(process.platform === 'win32' ? 'where' : 'which', ['tree-sitter'], { encoding: 'utf8' });
    return which.status === 0 ? which.stdout.split('\n')[0].trim() : null;
}

let summary = 'tree-sitter CLI not installed - corpus not run';
const cli = findCli();

if (cli) {
    // The CLI compiles the parser with $CC or cc; /usr/bin/cc is the system compiler even
    // where a cc earlier on the PATH is something else.
    const env = Object.assign({}, process.env);
    if (!env.CC && fs.existsSync('/usr/bin/cc')) env.CC = '/usr/bin/cc';
    const run = spawnSync(cli, ['test'], { cwd: dir, env, encoding: 'utf8' });
    const out = (run.stdout || '') + (run.stderr || '');
    const plain = out.replace(/\x1b\[[0-9;]*m/g, '');
    const corpus = (plain.match(/^\s+\d+\. ✓/gm) || []).length;
    const assertions = [...plain.matchAll(/\((\d+) assertions\)/g)].reduce((n, m) => n + Number(m[1]), 0);
    if (run.status !== 0) failed.push('tree-sitter test failed:\n' + plain.split('\n').filter((l) => /✗|rror|differ|expected|actual/i.test(l)).slice(0, 30).join('\n'));
    else summary = corpus + ' corpus tests and ' + assertions + ' highlight assertions passed';
}

if (failed.length) {
    console.error('treesitter: ' + failed.length + ' problems');
    for (const f of failed) console.error('  ' + f);
    process.exit(1);
}

console.log(checks + ' grammar and query checks passed, ' + summary);
