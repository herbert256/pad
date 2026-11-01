# Tags Application

## Introduction

A page for every PAD tag, named with its type prefix: every built-in tag - `{pad:if}` - every
kind of the ten subsystems - `{chart:pie}`, `{game:memory}`, `{toy:life}`, `{diagram:state}`,
`{widget:tabs}`, `{barcode:ean13}`, `{embed:youtube}`, `{input:slider}`, `{icon:star}`,
`{fake:email}` - and every sequence type, `{sequence:fibonacci}`: its name and what it does in
one line, its syntax with the options that belong to that tag (not the handling options every
tag takes), a longer description, and examples - each runnable one rendered live beside its
template, by the tag it documents. Over the pages stand the indexes: by name (A-Z), by group (what a tag is for),
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
| `_lib/catalog.php` | The catalog, read from `tag/*.php` and `_lib/kinds.php`, keyed by the name with its prefix, and the rows of every index |
| `_lib/kinds.php` | The kinds of the subsystems and the sequence types, read from the engine: a kind's line and description from the comment at the top of its file in `pad/subs/<subsystem>/types/`, a sequence type's from `docs/sequences/SEQUENCES.md`, and its example from what plays it already - the charts application's examples, the templates of the games and the toys applications, the inputs application's pages, this application's widget and diagram examples - or a line made for the kind |
| `tag/[tagType]/[tagKind].pad`, `.php` | The page of a kind, `?tag/chart/pie`, in the frame of the tag pages: how it is written and the comment of its file, then its example |
| `_inits.pad`, `_inits.php` | The frame every PAD application shares (`_common/_tags/padFrame`), with this application's menu and the search box in its bar |
| `tag/<name>.php` | What the indexes read: the line, the group, the form, the syntax, the parameters, the options, the related tags |
| `tag/<name>.pad` | The longer description |
| `tag/_inits.pad`, `tag/_exits.pad`, `tag/_exits.php` | The tag page around the description: syntax, parameters and options above; examples, related tags and the neighbours by name below |
| `examples/<name>/<n>-<slug>.pad` | A live example - first line `{meta title='...', about='...'/}`; with a paired `.php` for its data |
| `examples/<name>/<n>-<slug>.src` | An example that is shown but never run - a redirect, an exit, a mail |
| `_config/config.php` | Switches `_common` off - the frame is reached as `common:padFrame` - and fixes the timezone |

The stylesheet is `www/tags/tags.css`; the frame's own, with the theme switcher, is
`www/_common/pad.css` and `pad.js`.

## Adding a tag

Write `tag/<name>.php` and `tag/<name>.pad`, and its examples under `examples/<name>/` -
every index picks it up; nothing lists the tags by hand. A kind added to a subsystem, or a
sequence type, has its page and its place in every index without a change here.
