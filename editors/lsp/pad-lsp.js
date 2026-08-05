#!/usr/bin/env node
// Minimal PAD language server (stdio, no dependencies).
// Provides completions: built-in tags/functions/properties/options/prefixes
// (from completions.json, generated from the framework source) and closing
// suggestions for open tags after typing {/.

const fs = require('fs');
const path = require('path');

const COMPLETIONS = JSON.parse(
    fs.readFileSync(path.join(__dirname, 'completions.json'), 'utf8'));

// LSP CompletionItemKind numbers
const KIND = {
    Keyword: 14, Function: 3, Property: 10, EnumMember: 20,
    Module: 9, Operator: 24, Class: 7, Snippet: 15,
};

const TAG_RE = /\{(\/?)([A-Za-z_][A-Za-z0-9_]*(?::[A-Za-z_][A-Za-z0-9_]*)?)/g;
const SINGLE_TAGS = new Set([
    'set', 'get', 'echo', 'increment', 'decrement', 'redirect', 'restart',
    'exit', 'break', 'continue', 'cease', 'dump', 'error', 'exception',
    'open', 'close', 'null', 'true', 'false', 'flag', 'page', 'curl',
    'exists', 'at', 'resume', 'switch', 'ajax', 'reactData', 'file',
    'make', 'keep', 'remove', 'action',
]);

const documents = new Map();

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
    TAG_RE.lastIndex = 0;
    while ((m = TAG_RE.exec(before)) !== null) {
        if (m[1]) {
            const i = stack.lastIndexOf(m[2]);
            if (i >= 0) stack.length = i;
        } else if (!SINGLE_TAGS.has(m[2])) {
            stack.push(m[2]);
        }
    }
    return stack;
}

function completion(params) {
    const before = textBefore(params.textDocument.uri, params.position);

    const closeMatch = before.match(/\{\/([A-Za-z0-9_:]*)$/);
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
    if (!/[/A-Za-z_$%!@]/.test(before.charAt(open + 1) || '')) return [];

    return COMPLETIONS.map((c) => ({
        label: c.label,
        kind: KIND[c.kind] || 1,
        detail: c.detail,
        insertText: c.insert || c.label,
    }));
}

// ---------------- JSON-RPC over stdio ----------------

let buffer = Buffer.alloc(0);

function send(msg) {
    const body = Buffer.from(JSON.stringify(msg), 'utf8');
    process.stdout.write('Content-Length: ' + body.length + '\r\n\r\n');
    process.stdout.write(body);
}

function handle(msg) {
    const { id, method, params } = msg;
    if (method === 'initialize') {
        send({ jsonrpc: '2.0', id, result: {
            capabilities: {
                textDocumentSync: 1, // full
                completionProvider: { triggerCharacters: ['{', '/', ':', '$', '|'] },
            },
            serverInfo: { name: 'pad-lsp', version: '0.1.0' },
        } });
    } else if (method === 'initialized') {
        // notification, nothing to do
    } else if (method === 'textDocument/didOpen') {
        documents.set(params.textDocument.uri, params.textDocument.text);
    } else if (method === 'textDocument/didChange') {
        documents.set(params.textDocument.uri, params.contentChanges[0].text);
    } else if (method === 'textDocument/didClose') {
        documents.delete(params.textDocument.uri);
    } else if (method === 'textDocument/completion') {
        send({ jsonrpc: '2.0', id, result: completion(params) });
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
        try { handle(JSON.parse(body)); } catch (e) { /* ignore malformed */ }
    }
});
