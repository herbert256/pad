# Playground

## Introduction

Type a PAD template and its data - JSON, YAML, XML, CSV or PHP code - and see what they render to:
for trying a tag, checking a bug report, or showing someone an example through a link.

## Usage

Open `http://localhost/pad/playground/`. The page stands in the frame every PAD application
shares and fills the window: the brand and the menu on the side, the bar with the title, the
buttons - **Render**, **Show the HTML**, **Open in live** - the theme switcher and the
light/dark switch. The left pane holds the template and its data; the right pane shows the
result. The data is a text in any format PAD reads data in - JSON, YAML,
XML, CSV ... - its type recognised on sight, as `{data}` recognises it: a record's keys (a
JSON object, a YAML mapping, an XML document) become the template's variables, and a list,
which has no names of its own (CSV, a JSON or YAML array, a range), becomes the variable
`rows` - `{rows}...{/rows}`. **Switch to PHP**, beside the data's label, swaps the text for
PHP code, written as a page's `.php`: the variables it sets become the template's and what
it echoes stands before the template; **Switch to data** swaps back, each pane keeping what
was typed in it.

The [live](../live/README.md) application looks the same and runs what is typed as a real
page of this server instead: forms that post back, request values, sessions. **Open in
live**, beside the buttons in the bar, opens the boxes there as they stand here - the template, the data
and the PHP, and which of the two is used - and the live application's **Open in the
playground** brings them back; the other side waits for its button, as for any link.

The boxes are editors with colours, as in the [edit](../edit/README.md) application: Monaco,
loaded from `$playMonaco`, with the edit application's own PAD colouring
(`www/edit/pad-mode.js`) for the template - and its completion of PAD's built-in names - the
colours of the format the data text reads as (JSON, YAML, XML, plain for CSV), changing as it
is typed, and PHP's for the PHP code - light or dark with the frame, its switch and the theme
chosen in its bar. Without Monaco - no internet, `$playMonaco = ''` - the
boxes are plain text fields.

The examples of the other applications - the manual's and the sequence application's
`{example}`s and `{demo}` tables, showcase, charts, tags, types, games, toys, inputs, examples
- have a little ▶ icon at their top right that opens them here, in such a link, and beside it
a ⚡ that opens them in the live application: the template in the template box, and its PHP
or its data
([The frame of the PAD applications](../../docs/FEATURES.md#the-frame-of-the-pad-applications)).

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
  names a key of the data may have - with all of PHP's reach: the request, files, databases, the
  network, the shell. Of this list the guards above, the limits and the output frame hold
  for it, the rest does not; a link that holds PHP code waits for **Render** like any other.
- **No request values.** `$padRequestVars = []`: the posted template and data are read by
  `render.php` itself; nothing else in the request becomes a variable.
- **No database.** The application's credentials name no real database, so the database tags
  fail instead of reaching one.
- **Limits.** Five seconds of run time, 64 KB of template and of data, 256 KB of output.
- **A sandboxed output frame.** The rendered page's scripts run - a toy moves, a game plays -
  but the frame allows scripts and nothing more (`sandbox="allow-scripts"`, no
  `allow-same-origin`): its page is an origin of its own, so they cannot reach the playground
  page, its form and token, this site's cookies or storage, nor open a window, move the
  playground's or send a form. What they can do - fetch an address - the template could do
  on the server already.
- **One script from elsewhere.** Monaco runs in the playground's own page, from the address of
  `$playMonaco` - pinned to one version of it on jsDelivr, as the edit application's
  `$editMonaco` - so it could post the page's form like the page itself; a copy of its
  `min/vs` under `www/` takes the CDN out, an empty address takes Monaco out.

What it does not stop: tags that read files (`{file}`, `{files}`, `{dir}`, `local:`), fetch
URLs (`{curl}`, `{get}`) or render other applications' pages (`{page app=/}`) work as they do
anywhere else - on this machine, for this machine's user - and a data text that is an address
(`https://...`, `SELF://...`) is fetched, as `{data}` fetches one.

## Files

| File | Description |
|------|-------------|
| `index.php` / `index.pad` | The editor: template, data (a text in any data format, or PHP), output frame - in the shared frame, which this page opens itself (`{common:padFrame fill}`), so `?render` stays unwrapped |
| `render.php` / `render.pad` | Reads the posted data text with the reader of its type, or runs the posted PHP code, then renders the posted template with that data through `padCode()` |
| `_inits.php` | Turns every non-local request away |
| `_config/config.php` | CSRF tokens; no PHP functions, no request variables, no `_common`, no database; `$playMonaco`, where Monaco comes from |
| `www/playground/playground.js` | Makes the boxes Monaco editors with colours, switches the data between the text and PHP, keeps the panes in the URL hash and in the link to the live application, renders the sample on load and on Ctrl+Enter |
| `www/playground/playground.css` | The two-pane layout filling the frame, in the frame's colours |
