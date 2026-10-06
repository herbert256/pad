# Playground

## Introduction

Type a PAD template and its data, and see what they render to - for trying a tag, checking
a bug report, or showing someone an example through a link.

## Usage

Open `http://localhost/pad/playground/`. The left pane holds the template and its data, a
JSON object whose keys become the template's variables; the right pane shows the result.
**Render** (or Ctrl+Enter) renders it, **Show the HTML** shows the HTML it produced. The
address bar always holds both panes in its `#s=` hash, so copying it shares the example.

## What it allows

The playground runs the template it is given, so it is closed down:

- **This machine only.** `_inits.php` answers 403 to every request that is not local
  (`padLocal()` - the command line, or loopback with no forwarding header).
- **A post from its own form only.** `$padCsrf = TRUE`: a post to `?render` carries the CSRF
  token of the playground's form, so a page on another site cannot make this machine's
  browser post it a template - which would come from loopback, as local as any other.
- **No PHP from the template.** `$padPhpFunctions = []`: `php:` calls and bare PHP function
  pipes are refused with an error.
- **No request values.** `$padRequestVars = []`: the posted template and data are read by
  `render.php` itself; nothing else in the request becomes a variable.
- **No database.** The application's credentials name no real database, so the database tags
  fail instead of reaching one.
- **Limits.** Five seconds of run time, 64 KB of template, 256 KB of output.
- **A sandboxed output frame.** The rendered page's scripts do not run, and it cannot reach
  the playground page or its cookies.

What it does not stop: tags that read files (`{file}`, `{files}`, `{dir}`, `local:`), fetch
URLs (`{curl}`, `{get}`) or render other applications' pages (`{page app=}`) work as they do
anywhere else - on this machine, for this machine's user.

## Files

| File | Description |
|------|-------------|
| `index.php` / `index.pad` | The editor: template, data, output frame |
| `render.php` / `render.pad` | Renders the posted template with the posted data through `padCode()` |
| `_inits.php` | Turns every non-local request away |
| `_config/config.php` | CSRF tokens; no PHP functions, no request variables, no `_common`, no database |
| `www/playground/playground.js` | Keeps the panes in the URL hash, renders on load and on Ctrl+Enter |
| `www/playground/playground.css` | The two-pane layout |
