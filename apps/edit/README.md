# Edit

## Introduction

A browser editor for the PAD applications on this server, behind a login: the files of an application on the left, Monaco on the right with PAD colouring, completion, documentation and checks in .pad files and PHP completion in .php files.

## Usage

Open `http://localhost/pad/edit/`. The first visit asks for the first user - there is no
default password, and only this machine may make that user; more users are added in the
editor (**⋯ → Users**). Pick an application (Alt+A); its files are on the left, in two roots:
`apps/<app>/` and, when it has one, `www/<app>/` with its stylesheets and scripts.

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
- Light and dark, font size, tab size, word wrap, minimap, autosave (**⋯ → Settings**).
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

Without Monaco - no internet, a wrong address - the editor falls back to a plain text area:
files still open, change and save.

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
| `_lib/api.php` | Access, and the running of a call |
| `_templates/` | The starters of new files |
| `_tests/` | `pad test edit` |
| `www/edit/edit.js` | The editor: tree, tabs, panels, dialogs |
| `www/edit/pad-mode.js` | PAD for Monaco |
| `www/edit/php-mode.js` | PHP completion for Monaco |
| `www/edit/edit.css` | The look, light and dark |
