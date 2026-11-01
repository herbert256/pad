# Playground

## Introduction

Type a PAD template and its data - a JSON object or PHP code - and see what they render to:
for trying a tag, checking a bug report, or showing someone an example through a link.

## Usage

Open `http://localhost/pad/playground/`. The left pane holds the template and its data, a
JSON object whose keys become the template's variables; the right pane shows the result.
**Switch to PHP**, beside the data's label, swaps the JSON for PHP code, written as a page's
`.php`: the variables it sets become the template's and what it echoes stands before the
template; **Switch to JSON** swaps back, each pane keeping what was typed in it.
**Render** (or Ctrl+Enter) renders it, **Show the HTML** shows the HTML it produced. The
address bar always holds the panes and which data is used in its `#s=` hash, so copying it
shares the example. An example opened from such a link fills the panes but does not run until
**Render** is pressed: its template and its PHP code are the link maker's, and they run on
this machine.

## What it allows

The playground runs the template it is given, so it is closed down:

- **This machine only.** `_inits.php` answers 403 to every request that is not local
  (`padLocal()` - the command line, or loopback with no forwarding header) or that names
  another host than `localhost`, `127.0.0.1` or `[::1]` - a page on another site that has its
  own name resolve to 127.0.0.1 reaches the loopback address too.
- **A post from its own form only.** `$padCsrf = TRUE`: a post to `?render` carries the CSRF
  token of the playground's form, so a page on another site cannot make this machine's
  browser post it a template - which would come from loopback, as local as any other.
- **No PHP from the template.** `$padPhpFunctions = []`: `php:` calls and bare PHP function
  pipes are refused with an error.
- **PHP from the data pane, as written.** Switched to PHP, the data pane's code runs like a
  page's `.php` - in a function of its own, its variables handed to the template under the
  names a JSON key may have - with all of PHP's reach: the request, files, databases, the
  network, the shell. Of this list the guards above, the limits and the output frame hold
  for it, the rest does not; a link that holds PHP code waits for **Render** like any other.
- **No request values.** `$padRequestVars = []`: the posted template and data are read by
  `render.php` itself; nothing else in the request becomes a variable.
- **No database.** The application's credentials name no real database, so the database tags
  fail instead of reaching one.
- **Limits.** Five seconds of run time, 64 KB of template and of PHP code, 256 KB of output.
- **A sandboxed output frame.** The rendered page's scripts do not run, and it cannot reach
  the playground page or its cookies.

What it does not stop: tags that read files (`{file}`, `{files}`, `{dir}`, `local:`), fetch
URLs (`{curl}`, `{get}`) or render other applications' pages (`{page app=/}`) work as they do
anywhere else - on this machine, for this machine's user.

## Files

| File | Description |
|------|-------------|
| `index.php` / `index.pad` | The editor: template, data (JSON or PHP), output frame |
| `render.php` / `render.pad` | Runs the posted PHP code or reads the posted JSON, then renders the posted template with that data through `padCode()` |
| `_inits.php` | Turns every non-local request away |
| `_config/config.php` | CSRF tokens; no PHP functions, no request variables, no `_common`, no database |
| `www/playground/playground.js` | Switches the data between JSON and PHP, keeps the panes in the URL hash, renders the sample on load and on Ctrl+Enter |
| `www/playground/playground.css` | The two-pane layout |
