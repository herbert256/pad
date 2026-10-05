# pad-mcp - PAD for AI assistants

A [Model Context Protocol](https://modelcontextprotocol.io) server (Node 18 or newer, no
dependencies, stdio) that lets an assistant such as Claude Code check PAD against the engine
of this checkout, instead of against what it remembers of `CLAUDE.md`.

## Register it with Claude Code

```bash
claude mcp add pad -- node /Users/herbert/pad/editors/mcp/pad-mcp.js

# with the web server's base, for pad_test and for pages that fetch their own URLs
claude mcp add pad -e PAD_HOST=http://localhost/pad/ -- node /Users/herbert/pad/editors/mcp/pad-mcp.js

# for everyone who opens the repository: writes .mcp.json in the project
claude mcp add --scope project pad -- node /Users/herbert/pad/editors/mcp/pad-mcp.js
```

`claude mcp list` shows it, `/mcp` inside a session shows its tools. Other MCP clients take
the same command in their configuration:

```json
{
  "mcpServers": {
    "pad": {
      "command": "node",
      "args": ["/Users/herbert/pad/editors/mcp/pad-mcp.js"],
      "env": { "PAD_HOST": "http://localhost/pad/" }
    }
  }
}
```

## Tools

| Tool | Arguments | What it does |
|------|-----------|--------------|
| `pad_render` | `app`, `page`, `template`, `query` | Renders a page, or a template given as text, through PAD on the command line - no web server needed. The page's `.php` runs first, as for a GET request. Answers the output, or the PAD error: the message, the tag and its line, where PHP raised it, the variables the page had |
| `pad_check` | `app`, `page`, `template` | The engine's strict syntax check: of a template (before it is saved), of a page, or - `app` alone - of every page of the application. Answers `ok` or each error with its tag and line |
| `pad_trace` | `app`, `page`, `template`, `query` | Renders with the execution trace on and answers it: every level and occurrence the engine walked, with the tag and what it produced. The full trace stays under `DATA/trace/<page>/` |
| `pad_apps` | - | The applications, with the first line of each README |
| `pad_pages` | `app` | The pages of an application, and its own `_tags`, `_functions`, `_include`, `_callbacks`, `_data`, `_lib`, `_scripts`, by directory |
| `pad_builtins` | `kind` | The built-in tags, functions, options, properties, type prefixes, sequence types and operators - read from the engine's directories, the same ones `editors/generate.php` reads - each with its line from `docs/reference` |
| `pad_reference` | `name`, `kind`, `file` | The `docs/reference` section of a tag, function, option, property, type prefix or construct; or a whole file under `docs/`; or, without arguments, the list of files |
| `pad_test` | `host` | Runs `./ci.sh` - the eight regression suites against a running web server, plus the editor tooling checks - and answers each suite's line and every failing test with what was wanted and what came back |

A template given as `template` need not exist as a file: with a `page`, that page's `.php`
runs first and its variables are there, and the page's directory gives its `_inits`,
`_tags` and `_include`; without a page it renders in the application's root. This is
`editors/render.php --source`, which hands the text to the engine in `$padPageSource`.

The tools run the pages' PHP as a browser's GET request would - `pad_check` of a whole
application renders every page of it.

## Settings

| Variable | Default | Meaning |
|----------|---------|---------|
| `PAD_HOST` | - | The base the web server serves the applications under, e.g. `http://localhost/pad/`: `pad_test`'s default, and the host the command-line renders say they were asked on, so a page that fetches its own URLs reaches the real server |
| `PAD_PHP` | `php` | The PHP binary |
| `PAD_APPS` | `apps/` of the checkout | Another applications directory (the tests use `editors/fixture/apps`) |

The checkout is the one the server file sits in.

## Protocol

JSON-RPC 2.0, one message per line on stdin and stdout, logs on stderr - the MCP stdio
transport. `initialize` answers the protocol version the client asks for when it knows it
(2025-11-25, 2025-06-18, 2025-03-26, 2024-11-05), the newest otherwise, and gives the
assistant a short instruction: look tags up with `pad_reference` before writing a template,
run `pad_check` on it after. A tool that fails answers `isError: true` with the reason; an
unknown tool or method is a JSON-RPC error.

## Test

```bash
node editors/mcp/test.js
```

It speaks MCP to the server against the fixture application `editors/fixture/apps/shop` -
initialize, tools/list, a call of every tool - and runs `pad_test` in the doctored world
the gate's own test rig uses: a trigger served by a `php -S` of its own, planted suite
results, a known run token. `./ci.sh` runs it as its `mcp` line.
