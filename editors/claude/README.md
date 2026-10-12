# PAD for Claude Code

A Claude Code plugin, in a marketplace of its own, that brings PAD's editor tooling to Claude
Code in one install:

| Part | What Claude Code gets |
|------|-----------------------|
| Language server (`.lsp.json`) | After every edit or write of a `.pad` or `.html` page, PAD itself renders the page under the strict syntax check (`editors/lsp/pad-lsp.js`) and its error - `Field '$titel' not found`, `the pair {if} never closes` - comes back to Claude with the line and column, without Claude asking. The `LSP` tool gets go-to-definition along PAD's lookup order and hover from `docs/reference` |
| MCP server (`.mcp.json`) | The PAD tools of `editors/mcp/pad-mcp.js`: `pad_render`, `pad_check`, `pad_trace`, `pad_php`, `pad_compare`, `pad_case`, `pad_test`, `pad_reference`, `pad_builtins`, `pad_apps`, `pad_pages` - see [../mcp/README.md](../mcp/README.md) |
| Skill `pad-bug-hunt` | How to hunt, prove and fix a bug in PAD with those tools: reproducing, the old engine against the new, which suite a regression case goes in and the answer form it reads, the gate |

## Install

```bash
claude plugin marketplace add /path/to/pad/editors/claude
claude plugin install pad@pad --scope local      # this project only; user for every project
```

`--scope local` keeps it to the PAD checkout you run it in (`.claude/settings.local.json`, not in
git). Inside a session: `/plugin` shows it, `/mcp` its tools. For one session without installing:
`claude --plugin-dir /path/to/pad/editors/claude/pad`.

A `pad` MCP server registered by hand earlier (`claude mcp add pad ...`) is the same server
twice - remove it: `claude mcp remove pad`.

## How it finds PAD

Claude Code copies an installed plugin into its cache, so `bin/launch.js` does not run a server
of its own: it finds the PAD checkout - `$PAD_HOME`, else the project directory, else the working
directory, walked up to the directory with `pad/pad.php` - and runs that checkout's
`editors/lsp/pad-lsp.js` or `editors/mcp/pad-mcp.js`. The servers are always those of the commit
the engine is at, and a git worktree opened as a project runs its own. In a project that is no
PAD checkout both answer as servers with nothing to offer.

The MCP server gets `PAD_HOST` - the base the web server serves the applications under, for
`pad_case`, `pad_test` and pages that fetch their own server - from the environment, or
`http://localhost/pad/`.

## What Claude Code sends the language server

Learned from a server that logged its traffic, Claude Code 2.1.296: a Read sends nothing; the
first Edit or Write of a file sends `textDocument/didOpen` with the new text, every later one
`didChange` and `didSave`. `pad-lsp.js` checks on `didOpen` and `didSave` - the file is on disk by
then - and the diagnostics it publishes reach Claude with its next tool result.

## Test

```bash
claude plugin validate editors/claude/pad
claude plugin validate editors/claude
```

`node editors/claude/test.js` starts the launcher as Claude Code would - in this checkout, in a
directory that is no checkout - and speaks LSP and MCP to it; `./ci.sh` runs it as its `claude`
line.
