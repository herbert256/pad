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

`claude mcp list` shows it, `/mcp` inside a session shows its tools. The Claude Code plugin in
[`editors/claude`](../claude/README.md) brings this server together with the language server
and a bug-hunting skill - installed that way, there is no `claude mcp add` to do (and one done
before is the same server twice). Other MCP clients take the same command in their
configuration:

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
| `pad_render` | `app`, `page`, `template`, `query`, `data`, `bare` | Renders a page, or a template given as text, through PAD on the command line - no web server needed. The page's `.php` runs first, as for a GET request. Answers the output - with an `HTTP <code>` line first when the status was not 200 - or the PAD error: the message, the tag and its line, where PHP raised it, the variables the page had |
| `pad_check` | `app`, `page`, `template`, `query`, `data` | The engine's strict syntax check: of a template (before it is saved), of a page, or - `app` alone - of every page of the application. Answers `ok` or each error with its tag and line |
| `pad_trace` | `app`, `page`, `template`, `query`, `data`, `bare` | Renders with the execution trace on and answers it: every level and occurrence the engine walked, with the tag and what it produced. The full trace stays under `DATA/trace/<page>/` |
| `pad_php` | `code`, `app`, `query` | Runs PHP statements in a booted application - the engine's library, the application's `_lib`, configuration and database - and answers what they echoed, what they returned (`var_export`) or what they threw. A warning, a notice and a `padError` are thrown, so a helper's report of a wrong argument is the answer rather than the end of the request. `app` defaults to `test` |
| `pad_compare` | `app`, `page`, `template`, `query`, `data`, `bare`, `code`, `ref`, `to`, `scope` | The same render - or the same `code` - on two versions of the engine: `ref` (default `HEAD`) against `to` (default the working tree). Answers "the same on both", or where the two part and a unified diff, and lists the uncommitted engine changes the working tree side runs with |
| `pad_case` | `app`, `page`, `host` | One test of the regression suites, run as its suite runs it - fetched from the web server, judged by the runner's own comparison (`apps/regression/main/_lib/regression.php`): a Pages, Common or Errors case, a Framework case, or a page of another application judged against its covering suite's store. Answers `ok`, `FAILED` with want and got, or `new` with what came back |
| `pad_apps` | - | The applications, with the first line of each README |
| `pad_pages` | `app` | The pages of an application, and its own `_tags`, `_functions`, `_include`, `_callbacks`, `_data`, `_lib`, `_scripts`, by directory |
| `pad_builtins` | `kind` | The built-in tags, functions, options, properties, type prefixes, sequence types and operators - read from the engine's directories, the same ones `editors/generate.php` reads - each with the first sentence of its section in `docs/reference` |
| `pad_reference` | `name`, `kind`, `file` | The `docs/reference` section of a tag, function, option, property, type prefix or construct; or a whole file under `docs/`; or, without arguments, the list of files |
| `pad_test` | `host`, `suite` | Runs `./ci.sh` - the eight regression suites against a running web server, plus the editor tooling checks - or, with `suite`, that one suite, and answers each suite's line and every failing test as the place where what was wanted and what came back part |

A template given as `template` need not exist as a file: with a `page`, that page's `.php`
runs first and its variables are there, and the page's directory gives its `_inits`,
`_tags` and `_include`; without a page it renders in the application's root. This is
`editors/render.php --source`, which hands the text to the engine in `$padPageSource`. Its
output comes bare - without the `_inits`/`_exits` frame (`&padInclude`) - unless `bare` is
false; a page comes framed unless `bare` is true.

`data` is a JSON object of variables - `{"who": "Ann", "rows": [{"n": 1}, {"n": 2}]}`, a list of
objects iterable as `{rows}...{/rows}` - for the template instead of any PHP: the designer
preview's own mechanism (`?padSample`, `pad/lib/sample.php`), with the sample written under
`DATA/samples/` for a page name of that call only and removed afterwards. No `_inits.php` and no
page `.php` run. Given with a `page` and no `template`, it is the page's own template that gets
the variables.

The tools run the pages' PHP as a browser's GET request would - `pad_check` of a whole
application renders every page of it.

### What a render brings back

Every render loads `prepend.php` ahead of `editors/render.php` (`php -d auto_prepend_file=`).
At the very end of the request - after the engine and its own shutdown functions - it writes
one trailer line, which the server takes off the output: the HTTP status the request ended
with (the command line has no headers to read it from) and, for `pad_php`, `pad_case` and a
`pad_compare` of `code`, what the snippet did. It asks nothing of the engine, so an old engine
runs under it as well. Headers are not in it: for a header, a cookie or a session, ask the
running server with `curl -i`.

### Another commit

`pad_compare` renders on a snapshot of another commit: its `pad/`, `editors/render.php`,
`home/` and `www/` - with `scope: "all"` its `apps/` too - taken out of git with `git archive`
into the system's temporary directory (`pad-mcp-snapshots/<commit>`), made once and kept for
the next call; the six used last stay, and every one used in the last hour - one another
session is rendering on is never pruned from under it. No worktree, no checkout, no stash: the repository and
the working tree are not touched, so it is safe while other work goes on in the checkout. A
file over a megabyte - the OEIS database of the sequences - is cloned from the working tree when
it is the same there. By default the applications are the working tree's on both sides, so a
regression case written today runs on yesterday's engine: that is how a new case is shown to
fail on the old code.

## Settings

| Variable | Default | Meaning |
|----------|---------|---------|
| `PAD_HOST` | - | The base the web server serves the applications under, e.g. `http://localhost/pad/`: `pad_test`'s and `pad_case`'s default, and the host the command-line renders say they were asked on, so a page that fetches its own URLs reaches the real server |
| `PAD_PHP` | `php` | The PHP binary |
| `PAD_APPS` | `apps/` of the checkout | Another applications directory (the tests use `editors/fixture/apps`) |

The checkout is the one the server file sits in.

## Protocol

JSON-RPC 2.0, one message per line on stdin and stdout, logs on stderr - the MCP stdio
transport. `initialize` answers the protocol version the client asks for when it knows it
(2025-11-25, 2025-06-18, 2025-03-26, 2024-11-05), the newest otherwise, and gives the
assistant a short instruction: look tags up with `pad_reference` before writing a template,
run `pad_check` on it after - and, hunting a bug, which tool does what. A tool that fails answers `isError: true` with the reason; an
unknown tool or method is a JSON-RPC error.

## Test

```bash
node editors/mcp/test.js
```

It speaks MCP to the server against the fixture application `editors/fixture/apps/shop` -
initialize, tools/list, a call of every tool, `pad_compare` against a snapshot of `HEAD` - and
runs `pad_test` in the doctored world the gate's own test rig uses: a trigger served by a
`php -S` of its own, planted suite results, a known run token, and for one suite a trigger that
writes the result with the token it was given. `./ci.sh` runs it as its `mcp` line.
