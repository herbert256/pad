# Notes

## Introduction

A notebook in Markdown - tags, search as you type, pinned notes, pictures and PDFs attached - with every note in one SQLite database: a file under DATA/ on a server, or in the browser when `pad pack notes` runs it offline.

## Overview

| Page | What it does |
|------|--------------|
| `index` | The notes, pinned first: all, one tag's - the tags are the frame's menu - or what a search finds. The list is a `{live}` region - the star pins a note - and a `{fragment}` that `notes.js` asks for while a search is typed |
| `note` | One note: its Markdown, tags, files - pictures shown - and a `{live}` region for the pin and the colour; removing a file and the note are posts |
| `edit` | A new note or a change: `{form}` with `rules=` on the fields, tags, a colour, a file to attach (`padUpload`); the Preview tab posts the text to `preview` |
| `preview` | The editor's Markdown rendered as the note page renders it - a POST with the session's token in `X-CSRF-Token` |
| `file` | A file of a note, sent back with `padUploadSend` - shown or, with `&download`, downloaded |
| `backup` | The numbers, the notes as JSON (`export`), the database file (`database`), an import of a JSON export |
| `export` | `$padExpose = [ 'notes' ]` - `?export&padFormat=json` answers every note with its tags |
| `database` | The SQLite file itself, downloaded with `&download` |

## The frame

Notes stands in the frame every PAD application shares, `{common:padFrame}` in `_inits.pad`
(with `$padCommon = FALSE` it is reached with the `common:` prefix): the brand top left, the
menu (`$frameMenu`, `_inits.php`) - all notes, the backup, a page per tag, and the export and
the database behind the backup - and the bar with the page's title (`$title`), the search box,
the New note button, the theme switcher and the light/dark switch. `www/notes/notes.css`
colours the pages with the frame's palette, so the themes and light and dark apply to them; the
colours of the notes are their own, light or dark with the page (`light-dark()`). The flash
messages stand above the page, where it runs below it.

## The database

`_config/config.php` makes the application database SQLite - `$padSqlDriver = 'sqlite'`,
`DATA/notes/notes.sqlite`, built from `_install/schema.sql` on the first request that needs it
(`$padSqlSetup`). Files attached to notes are stored by `padUpload` under `DATA/notes/files/`
with a random name; the `attachments` table holds the name they came with.

## Offline

```bash
pad pack notes            # DATA/wasm/notes/ - open index.html, or serve it over https and install it
pad pack notes --serve    # the same, served on http://127.0.0.1:8090/
```

In the browser PHP compiled to WebAssembly runs every page; the database, the attached files
and the session are kept in IndexedDB. `_offline.json` names the application and its icons.
The runtime's panel (the badge in the corner) browses the tables, runs SQL, and exports and
imports the database whole - the file the `database` page downloads on a server can be
imported there.

## JavaScript

`www/notes/notes.js` makes the pages quicker, never necessary: the search asks for the list
fragment while it is typed - the bar's heading saying what it found - the Preview tab posts the text, a file dropped on the editor is
attached, Ctrl+S saves and `/` goes to the search box.
