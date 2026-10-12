#!/usr/bin/env node
// Starts the PAD language server (lsp) or the PAD MCP server (mcp) for Claude Code.
//
//   node launch.js lsp|mcp
//
// Claude Code copies an installed plugin into its own cache, so the plugin cannot reach the
// servers by a path of its own: it finds the PAD checkout it works in - $PAD_HOME, else the
// project directory Claude Code names ($CLAUDE_PROJECT_DIR), else the working directory, each
// walked up to the first directory with pad/pad.php and the server beside it - and runs that
// checkout's server in this process. The server then is the checkout's own, of the same
// commit as its engine, and a git worktree opened as a project runs its own.
//
// In a project that is no PAD checkout it answers as a server with nothing to offer - no
// capabilities, no tools - rather than failing at every start of a session.

const fs = require('fs');
const path = require('path');

const SERVERS = { lsp: 'editors/lsp/pad-lsp.js', mcp: 'editors/mcp/pad-mcp.js' };
const which = process.argv[2];

if (!SERVERS[which]) {
    process.stderr.write('usage: launch.js lsp|mcp\n');
    process.exit(2);
}

function isFile(file) {
    try { return fs.statSync(file).isFile(); } catch (e) { return false; }
}

function checkout() {
    for (const start of [process.env.PAD_HOME, process.env.CLAUDE_PROJECT_DIR, process.cwd()]) {
        if (!start) continue;
        let dir = path.resolve(start);
        for (;;) {
            if (isFile(path.join(dir, 'pad', 'pad.php')) && isFile(path.join(dir, SERVERS[which]))) return dir;
            const up = path.dirname(dir);
            if (up === dir) break;
            dir = up;
        }
    }
    return null;
}

const home = checkout();

if (home) {
    require(path.join(home, SERVERS[which]));
} else if (which === 'lsp') {
    // LSP framing: a Content-Length header, a blank line, the JSON body.
    let buffer = Buffer.alloc(0);
    const send = (msg) => {
        const body = Buffer.from(JSON.stringify(msg), 'utf8');
        process.stdout.write('Content-Length: ' + body.length + '\r\n\r\n');
        process.stdout.write(body);
    };
    process.stdin.on('data', (chunk) => {
        buffer = Buffer.concat([buffer, chunk]);
        for (;;) {
            const end = buffer.indexOf('\r\n\r\n');
            if (end < 0) return;
            const m = buffer.slice(0, end).toString('utf8').match(/Content-Length: *(\d+)/i);
            const length = m ? +m[1] : 0;
            if (buffer.length < end + 4 + length) return;
            let msg = null;
            try { msg = JSON.parse(buffer.slice(end + 4, end + 4 + length).toString('utf8')); } catch (e) { msg = null; }
            buffer = buffer.slice(end + 4 + length);
            if (!msg || typeof msg !== 'object') continue;
            if (msg.method === 'exit') process.exit(0);
            if (msg.id !== undefined && msg.id !== null)
                send({ jsonrpc: '2.0', id: msg.id, result: msg.method === 'initialize' ? { capabilities: {}, serverInfo: { name: 'pad-lsp (no PAD checkout)' } } : null });
        }
    });
} else {
    // MCP over stdio: one JSON-RPC message per line.
    let buffer = '';
    const send = (msg) => process.stdout.write(JSON.stringify(msg) + '\n');
    process.stdin.setEncoding('utf8');
    process.stdin.on('data', (chunk) => {
        buffer += chunk;
        let nl;
        while ((nl = buffer.indexOf('\n')) >= 0) {
            let msg = null;
            try { msg = JSON.parse(buffer.slice(0, nl)); } catch (e) { msg = null; }
            buffer = buffer.slice(nl + 1);
            if (!msg || msg.id === undefined || msg.id === null) continue;
            if (msg.method === 'initialize')
                send({ jsonrpc: '2.0', id: msg.id, result: {
                    protocolVersion: (msg.params && msg.params.protocolVersion) || '2025-06-18',
                    capabilities: { tools: { listChanged: false } },
                    serverInfo: { name: 'pad-mcp', version: '0' },
                    instructions: 'This project is no PAD checkout - no directory with pad/pad.php was found from the project directory up - so the PAD tools are not offered. Set PAD_HOME to use them here.',
                } });
            else if (msg.method === 'tools/list') send({ jsonrpc: '2.0', id: msg.id, result: { tools: [] } });
            else if (msg.method === 'ping') send({ jsonrpc: '2.0', id: msg.id, result: {} });
            else send({ jsonrpc: '2.0', id: msg.id, error: { code: -32601, message: 'no PAD checkout here' } });
        }
    });
    process.stdin.on('end', () => process.exit(0));
}
