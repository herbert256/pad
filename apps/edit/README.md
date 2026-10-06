# Edit

## Introduction

A browser editor for the PAD applications on this server, behind a login: the files of an application on the left, Monaco on the right with PAD colouring, completion, documentation and checks in .pad files and PHP completion in .php files.

## Usage

Open `http://localhost/pad/edit/`. The first visit asks for the first user - there is no
default password, and only this machine may make that user; more users are added in the
editor (**⋯ → Users**). Pick an application (Alt+A); its files are on the left, in two roots:
`apps/<app>/` and, when it has one, `www/<app>/` with its stylesheets and scripts.
A link to `edit/?app=demo` opens the editor on that application, through the login when
there is no session yet - the *edit* links of the apps listing (`/pad/apps/`) are such links.

**Files.** The tree's toolbar and its context menu (right click, or Shift+F10) make a file, a
folder, or one of PAD's kinds from a starter - a page pair, a tag, a component, a pipe
function, a snippet, a callback, an option, an event hook, a guard, a wrapper, a data file, a
named query, a test, a mail template, a translation catalog, a Markdown entry - each where
PAD looks for it. Duplicate, rename (F2), move (or drag), delete (to the trash, Del), upload
(or drop files from the desktop), download, copy the path. A file without an extension is
made as a page pair. Underscore directories are marked and explained on hover; a `.pad`
and its `.php` are marked as a pair (Alt+O switches between them); git's view of every file
(changed, new) is shown beside it.

**PAD in .pad files.**
- Colouring of tags (the built-in ones apart from the application's), fields, pipe functions,
  options, properties, type prefixes, strings, operators, comments, `{ignore}` blocks and
  constructs - inside HTML, attribute values included.
- Completion of what PAD would accept where the cursor stands: after `{` every tag, the
  application's own tags and snippets, and pair snippets (`{if}`, `{case}`, `{form}` ...);
  after `{/` the tags still open, innermost first; after `$` the fields the page's PHP
  assigns; after `|` the pipe functions; after a tag name its options; after `first@` the
  open tags; inside `{page '...'}`, `{include '...'}`, `callback='...'`, `{trans '...'}` and
  `{collection '...'}` the pages, snippets, callbacks, translation keys and collections.
- Hover shows the reference documentation (`docs/reference/`), or the comment of an
  application's own tag or function, or the line that assigns a field.
- Go to definition (F12, Ctrl/⌘+click) follows PAD's own lookup: a tag to the nearest
  `_tags/`, a pipe to `_functions/`, a snippet to `_include/`, a field to the PHP that sets it.
- Tag pairs fold, make the outline (Ctrl/⌘+Shift+O), and rename together: editing `{items}`
  edits its `{/items}`. Typing the `}` of `{if ...}`, `{data ...}`, `{form ...}` puts in the
  closing tag when the template has none.
- Checks by PAD itself: a page is rendered under the strict syntax check
  (`editors/render.php`, as the language server does) when it is opened and saved (F7 checks
  now), and the error lands on the tag it is about. Checking while typing is a setting: it
  runs the page's PHP.

**PHP in .php files.** Completion of the PAD functions a page calls (`padRedirect`, `db`,
`padArrGet` ...) with their real signatures and documentation, the functions of the
application's `_lib/`, every internal PHP function, the variables of the file, the `$pad`
settings in `_config/` files, `db()`'s verbs; signature help; hover; go to definition into
`_lib/`; syntax errors while typing.

**Around the editing.**
- Tabs (drag to reorder, middle-click to close), quick open (Ctrl/⌘+P, with `>` for
  commands, `:` for a line, `@` for a tag of this file), search in the application
  (Ctrl/⌘+Shift+F, plain or regular expression, case, whole words).
- A preview of the page beside the editor (Alt+P) that reloads on every save, at phone,
  tablet or full width; `.md` files preview as Markdown.
- Every save keeps the version it replaces: **History** compares and restores them. Compare
  with git's HEAD. A file changed on disk while it is open is read again, or, when it has
  changes of yours, shown side by side with them before it is overwritten.
- A terminal in the bottom panel (Ctrl+\`): commands run in a shell on this machine, in the
  application's directory to start with; `cd` carries over to the next command, the output
  streams in with its colours, a file of an application in it is a link that opens it,
  Ctrl+C (or **Stop**) stops a command and what it started, Tab completes a name, ↑ ↓ go
  through the history, `clear` or Ctrl+L empties the screen. There is no terminal device:
  a command gets no input, and full-screen programs (vi, top, less) do not work. The `pad`
  command is on the PATH.
- Light and dark, font size, tab size, word wrap, minimap, autosave (**⋯ → Settings**).

**Debugging** - the **Debug** tab of the bottom panel, a step debugger for the PHP of a page
through Xdebug:

- Breakpoints go in PHP files: a click in the gutter or F9 at the cursor; Shift+click gives
  one a condition (`$id == 42`). They keep to their lines as the text changes, are listed in
  the panel, and stay set between visits. **exceptions** stops where one is thrown, **first
  line** at the start of every request.
- **Debug the page** starts the debugger and loads the previewed page with `XDEBUG_SESSION`
  in its address; **every request to …** sets that as a cookie for the application's own
  path, so its pages stop in any browser tab - a form posted, a link followed. The editor's
  own requests are never stopped.
- When a request stops, its file opens at the line, marked in the gutter; files of the
  engine open read-only, as the debugger reads them. The panel shows the call stack (a
  click shows that frame's variables), the locals, the superglobals, watches, and a console
  that evaluates PHP in the paused request. Hovering a `$variable` shows its value.
- Continue (F5), step over (F10), into (F11), out (Shift+F11), stop the request (Shift+F5).
- One request at a time is held; another that comes meanwhile runs on without stopping.
- Monaco's own palette (F1) holds every editor command and the PAD ones; **⋯ → Keyboard
  shortcuts** lists the keys.

## Security

The editor writes `.php` files: whoever can use it can run code on this machine.

- **A login, always.** Users live in `DATA/edit/users.json` as `password_hash` hashes
  (mode 0600). A session carries the stamp its user had at login; deleting the user or
  changing the password ends every other session. Five login tries a minute per address.
- **This machine only**, unless `$editRemote` says otherwise: a web request must come from
  the loopback address with no forwarding header, and name this machine in its Host header -
  which keeps out a page on another site whose name resolves to 127.0.0.1. `$editHosts` adds
  host names. Opened up, use it over https only.
- **CSRF tokens** on every post, the editor's own JSON calls included; no request value
  becomes a variable.
- Paths are checked against the application's two roots: no `..`, no `.git`, no symbolic
  link out.
- The preview runs the application's page in a sandboxed frame: its scripts cannot reach the
  editor.
- The debugger listens on this machine's loopback addresses only; the editor talks to it
  on a port of its own, with a token that only the web server's user can read. An
  expression in its console runs in the paused request - as much as the editor can do
  anyway. `$editDebug = FALSE` switches it off.
- The terminal runs commands as the web server's user - no more than the editor can do by
  writing a PHP file, but more directly; `$editTerminal = FALSE` switches it off. Its jobs
  and their output are kept in the system's temporary directory (mode 0700), not under
  `DATA/`; a job whose page is gone is stopped after two minutes.
- `DATA/` must not be served by the web server in production - it holds the users, the
  history and the trash (as it holds the engine's keys).

## Configuration

`_config/config.php`:

| Setting | Default | |
|---------|---------|---|
| `$editRemote` | `FALSE` | let other machines log in |
| `$editHosts` | `[]` | host names a request may name besides localhost |
| `$editMonaco` | jsDelivr, Monaco 0.57.0 | the `min/vs` directory of Monaco - a local copy under `www/` works offline |
| `$editHistoryKeep` | `50` | versions kept per file |
| `$editTrashDays` | `30` | days a deleted file stays in the trash |
| `$editMaxText` | 2 MB | the largest file opened as text |
| `$editTerminal` | `TRUE` | the terminal in the bottom panel |
| `$editShell` | `''` | the terminal's shell - `''` takes bash, zsh or sh |
| `$editDebug` | `TRUE` | the step debugger in the bottom panel |

Without Monaco - no internet, a wrong address - the editor falls back to a plain text area:
files still open, change and save.

The debugger needs Xdebug in the PHP that serves the pages, with its step debugger on - in
`php.ini` (Homebrew: `/opt/homebrew/etc/php/<version>/php.ini`):

```ini
zend_extension = xdebug.so          ; pecl install xdebug
xdebug.mode = debug                 ; develop changes var_dump and error pages - leave it out
xdebug.start_with_request = trigger ; only a request that asks: XDEBUG_SESSION
```

and the web server restarted (`apachectl -k graceful`). `pecl upgrade xdebug` brings a newer
release; PECL refuses to install over an extension the PHP running it has loaded, so the
`zend_extension` line is commented out for the upgrade - PECL writes it back - and the build
needs the C compiler as `cc`: with another `cc` earlier on the PATH, give it as
`CC=/usr/bin/clang pecl upgrade xdebug`. `pecl list` shows the version installed.

Xdebug connects back to `xdebug.client_port` (9003); an IDE
listening there at the same time keeps the editor's debugger from starting. A browser
extension that sets an `XDEBUG_SESSION` cookie for the whole site makes every request ask
for a debugger - the editor's are let go, the pages' stop at their breakpoints.

An application made with **New application** is made as `pad new` makes one; the regression
suite's Other run lists its pages as *new* until they have answers.

## Files

| File | Description |
|------|-------------|
| `_guard.php` | Who may use the editor: this machine, logged in |
| `login.php` / `login.pad` | The login, and the making of the first user |
| `logout.php` | Ends the session (a post) |
| `index.php` / `index.pad` | The editor's page |
| `api.php` | The JSON calls of the editor, one file each in `_api/` |
| `_lib/paths.php` | The applications, their roots, the path checks |
| `_lib/files.php` | Listing, reading, writing, copying and moving files |
| `_lib/store.php` | Users, history and trash under `DATA/edit/` |
| `_lib/language.php` | The names and documentation behind completion and hover |
| `_lib/check.php` | PHP syntax checks, and PAD's own render of a page |
| `_lib/git.php` | Changed files, HEAD versions, the branch |
| `_lib/terminal.php` | The terminal's jobs: run, read, stop, complete |
| `_lib/debug.php` | Starts, asks and stops the debugger process |
| `_lib/dbgp.php` | DBGp, Xdebug's protocol: packets, commands, properties |
| `_bin/debugger.php` | The debugger process: Xdebug's side and the editor's |
| `_lib/api.php` | Access, and the running of a call |
| `_templates/` | The starters of new files |
| `_tests/` | `pad test edit` |
| `www/edit/edit.js` | The editor: tree, tabs, panels, dialogs |
| `www/edit/pad-mode.js` | PAD for Monaco |
| `www/edit/php-mode.js` | PHP completion for Monaco |
| `www/edit/edit.css` | The look, light and dark |
