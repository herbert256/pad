# Tags Application

## Introduction

A page for every built-in PAD tag: its name and what it does in one line, its syntax with the
options that belong to that tag (not the handling options every tag takes), a longer
description, and examples - each runnable one rendered live beside its template, by the tag
it documents. Over the pages stand the indexes: by name (A-Z), by group (what a tag is for),
by form (single tag, pair, or either), by option (every option and the tags that take it), a
cheat sheet with the syntax of every tag, and a search.

## Files

| File | Description |
|------|-------------|
| `index.pad` | The home page: the indexes and every group with its tags |
| `names.pad` | The tags from A to Z |
| `groups.pad` | The tags by group |
| `forms.pad` | Single tags, tag pairs and the tags that are both |
| `options.pad` | Every option a tag takes of its own, and the tags that take it |
| `cheatsheet.pad` | The syntax of every tag on one page |
| `search.pad` | `?search&q=word` - the tags whose name, line, syntax or options hold every word |
| `_lib/catalog.php` | The catalog, read from `tag/*.php`, and the rows of every index |
| `_inits.pad`, `_inits.php` | The frame: the menu, the search box, the light/dark switch |
| `tag/<name>.php` | What the indexes read: the line, the group, the form, the syntax, the parameters, the options, the related tags |
| `tag/<name>.pad` | The longer description |
| `tag/_inits.pad`, `tag/_exits.pad`, `tag/_exits.php` | The tag page around the description: syntax, parameters and options above; examples, related tags and the neighbours by name below |
| `examples/<name>/<n>-<slug>.pad` | A live example - first line `{meta title='...', about='...'}`; with a paired `.php` for its data |
| `examples/<name>/<n>-<slug>.src` | An example that is shown but never run - a redirect, an exit, a mail |
| `_config/config.php` | Switches `_common` off: the application has a design of its own |

The stylesheet and the small script are `www/tags/tags.css` and `tags.js`.

## Adding a tag

Write `tag/<name>.php` and `tag/<name>.pad`, and its examples under `examples/<name>/` -
every index picks it up; nothing lists the tags by hand.
