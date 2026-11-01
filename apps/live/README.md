# Live

## Introduction

Type a PAD page - its template, and its data or PHP code - and run it on this server: the
page is saved and asked for like any other page, so its PHP, request values, sessions, forms
that post back and scripts all work as they do in an application.

## Usage

Open `http://localhost/pad/live/`. It looks like the [playground](../playground/README.md):
the template on the left, under it the data - a text in any format PAD reads, JSON, YAML,
XML, CSV, recognised on sight, a record's keys the page's variables and a list the variable
`rows` - or, with **Switch to PHP**, PHP code written as a page's `.php`. The boxes are
editors with PAD's colours when Monaco loads (`$liveMonaco`).

**Run** (or Ctrl+Enter) saves the template with its data or PHP as a page of this server and
shows it on the right as the server answers it - a request of its own, `?page/<id>`, which
**open alone** opens in a tab of its own. Where the playground renders a template inside its
own request, here the page is a page: `padPosted`, `padRequest` and `?name=value` values, a
`{form}` that posts back to it, `{live}` regions, sessions, the database of the PAD home and
scripts all work, and its PHP may call any function a page's PHP may.

The address bar holds the boxes in its `#s=` hash, as the playground's does, so copying it
shares the page; one opened from such a link fills the boxes and waits for **Run**. Every
example of the other applications has such a link, the ⚡ icon at its top right beside the
playground's ▶ - and some have only that one: an example that needs what only a page of this
server has, as classicModels' database examples
([The frame of the PAD applications](../../docs/FEATURES.md#the-frame-of-the-pad-applications)).

The application runs with `_common`, as most PAD applications do: its tags - `{example}`,
`{demo}` - its library and its demo database are there for a page.

## What it allows

The live application runs the code it is given, as a page of this server, so:

- **This machine only.** `_guard.php` answers 403 to every request that is not local
  (`padLocal()`) or that names another host than `localhost`, `127.0.0.1` or `[::1]` - the
  saved pages as well as the editor.
- **A post from its own form only.** `$padCsrf = TRUE`: a save carries the token of the
  editor's form, a post of a saved page the token of that page's form.
- **Everything a page may do.** The PHP runs as written, with all of PHP's reach, and the
  template has every tag and function an application's template has. The page is shown in a
  frame of this site, not kept apart from the editor: a link from elsewhere waits for **Run**.
- **Kept a day.** A page is saved under `DATA/live/<id>` - the id made of what it holds - and
  removed a day after it was last saved; 64 KB of template and 64 KB of data or PHP.

## Files

| File | Description |
|------|-------------|
| `index.php` / `index.pad` | The editor: template, data or PHP, the page |
| `save.php` | Saves the posted page under `DATA/live/` and sends the frame to `?page/<id>` |
| `page/_inits.php` | The PHP of a saved page: its PHP code, or its data text read as PAD reads data |
| `_lib/live.php` | Where the pages are kept, their id, the sweep, the data reader |
| `_guard.php` | Turns every non-local request away |
| `_config/config.php` | CSRF tokens; `$liveMonaco`, where Monaco comes from |
| `www/live/index.php` | The entry point: hands a saved page's template to the engine (`$padPageSource`) |
| `www/live/live.js` | The editors, the switch, the boxes in the URL hash, Run on Ctrl+Enter, open alone |
| `www/live/live.css` | The two-pane layout |
