// The PAD reference and the built-in names, read the way the editor tooling needs them -
// shared by the language server (editors/lsp/pad-lsp.js: hover) and the MCP server
// (editors/mcp/pad-mcp.js: lookups and lists). No dependencies.
//
// lookup    the documentation of one name: the ### section of docs/reference/*.md whose
//           heading names it, or else the table row whose first cell does - TAGS.md for a
//           tag, FUNCTIONS.md for a pipe function, OPTIONS.md and HANDLING.md for an option,
//           PROPERTIES.md, TYPES.md for a type prefix, CONSTRUCTS.md for @page@ and the like
// builtins  the built-in names from the directories editors/generate.php reads: pad/tags,
//           pad/functions, pad/options with the start-phase options of inits/const.php and
//           pad/handling/types, pad/properties, pad/types, pad/sequence/types

const fs = require('fs');
const path = require('path');

const KINDS = {
    tag: ['TAGS.md', 'CONSTRUCTS.md'],
    function: ['FUNCTIONS.md'],
    option: ['OPTIONS.md', 'HANDLING.md'],
    property: ['PROPERTIES.md'],
    prefix: ['TYPES.md', 'TAGS.md'],
    construct: ['CONSTRUCTS.md'],
};

const OPERATORS = ['eq', 'ne', 'gt', 'lt', 'ge', 'le', 'and', 'or', 'xor', 'not', 'range'];

const cache = new Map();

function readText(file) {
    try { return fs.readFileSync(file, 'utf8'); } catch (e) { return ''; }
}

// One reference file as two indexes: the ### sections by the name(s) of their heading -
// "### field / array / record / check" names four - and the table rows by the `name` in
// their first cell, each row with its table's header. A section runs to the next heading of
// level 3 or higher, or a --- rule, outside code fences.
function parseDoc(file) {
    const stamp = (() => { try { return fs.statSync(file).mtimeMs; } catch (e) { return 0; } })();
    const hit = cache.get(file);
    if (hit && hit.stamp === stamp) return hit;

    const lines = readText(file).split('\n');
    const sections = new Map();
    const rows = new Map();
    const add = (map, key, val) => { if (!map.has(key)) map.set(key, []); map.get(key).push(val); };
    const keyOf = (t) => t.replace(/`/g, '').trim().replace(/^\{|\}$/g, '').replace(/[:@]$/, '');

    let fence = false, section = null, chapter = '', header = null;
    for (let i = 0; i < lines.length; i++) {
        const line = lines[i];
        if (/^\s*```/.test(line)) fence = !fence;
        const heading = !fence && line.match(/^(#{1,3})\s+(.*)$/);
        if (heading || (!fence && /^---\s*$/.test(line))) {
            if (section) section.end = i;
            section = null;
            if (heading && heading[1].length === 2) chapter = heading[2].trim();
            if (heading && heading[1].length === 3) {
                section = { heading: heading[2].trim(), start: i + 1, end: lines.length, chapter };
                for (const part of heading[2].split(/\s+\/\s+|,\s*/)) {
                    const key = part.replace(/`/g, '').trim();
                    if (/^@?[A-Za-z_][A-Za-z0-9_]*@?$/.test(key)) add(sections, key, section);
                }
            }
        }
        if (!fence && line.startsWith('|')) {
            if (!(lines[i - 1] || '').startsWith('|')) { header = line; continue; }
            if (/^\|[\s:|-]+\|?\s*$/.test(line)) continue;
            const first = line.split('|')[1] || '';
            for (const m of first.matchAll(/`([^`]+)`/g)) {
                const key = keyOf(m[1]);
                if (/^[A-Za-z_][A-Za-z0-9_]*$/.test(key)) add(rows, key, { header, row: line, chapter });
            }
        }
    }
    const doc = { stamp, lines, sections, rows };
    cache.set(file, doc);
    return doc;
}

// The documentation of a name, at most max entries, each as markdown with the file it came
// from. kind is one of KINDS; without it every kind is searched.
function lookup(home, kind, name, max = 2) {
    const out = [];
    const files = kind ? (KINDS[kind] || []) : [...new Set(Object.values(KINDS).flat())];
    const key = kind === 'construct' ? '@' + name.replace(/@/g, '') + '@' : name;
    for (const fileName of files) {
        const file = path.join(home, 'docs', 'reference', fileName);
        if (!fs.existsSync(file)) continue;
        const doc = parseDoc(file);
        const sec = (doc.sections.get(key) || doc.sections.get('@' + key + '@') || [])[0];
        if (sec) {
            let body = doc.lines.slice(sec.start, sec.end).join('\n').trim().split('\n');
            if (body.length > 40) body = body.slice(0, 40).concat(['...']);
            out.push({ file: fileName, heading: sec.heading, chapter: sec.chapter,
                       text: '### ' + sec.heading + '\n\n' + body.join('\n') + '\n\n*docs/reference/' + fileName + '*' });
        } else {
            const row = (doc.rows.get(key) || [])[0];
            if (row) {
                const cols = row.header.split('|').length - 2;
                out.push({ file: fileName, heading: name, chapter: row.chapter,
                           text: row.header + '\n|' + ' --- |'.repeat(Math.max(cols, 1)) + '\n' + row.row
                                 + '\n\n*docs/reference/' + fileName + (row.chapter ? ' - ' + row.chapter : '') + '*' });
            }
        }
        if (out.length >= max) break;
    }
    return out;
}

function names(dir, dirs = false) {
    let list = [];
    try { list = fs.readdirSync(dir); } catch (e) { return []; }
    return list
        .filter((n) => n[0] !== '.' && n[0] !== '_')
        .filter((n) => dirs ? fs.statSync(path.join(dir, n)).isDirectory() : n.endsWith('.php'))
        .map((n) => dirs ? n : n.slice(0, -4))
        .sort((a, b) => a.toLowerCase().localeCompare(b.toLowerCase()));
}

function builtins(home) {
    const pad = path.join(home, 'pad');
    const start = (readText(path.join(pad, 'inits', 'const.php')).match(/define \( 'padOptionsStart', \[(.*?)\] \)/s) || ['', ''])[1];
    const options = [...new Set(names(path.join(pad, 'options'))
        .concat([...start.matchAll(/'([A-Za-z]+)'/g)].map((m) => m[1]))
        .concat(names(path.join(pad, 'handling', 'types'))))]
        .sort((a, b) => a.toLowerCase().localeCompare(b.toLowerCase()));
    return {
        tags: names(path.join(pad, 'tags')),
        functions: names(path.join(pad, 'functions')),
        options,
        properties: names(path.join(pad, 'properties')),
        prefixes: names(path.join(pad, 'types')),
        sequences: names(path.join(pad, 'sequence', 'types'), true),
        operators: OPERATORS,
    };
}

module.exports = { KINDS, OPERATORS, parseDoc, lookup, builtins };
