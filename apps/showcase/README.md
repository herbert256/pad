# Showcase Application

## Introduction

A showcase of the tags that draw, format and interact - diagrams, maps, avatars, tables,
diffs, thumbnails, tabs, puzzles and the rest - each with a few worked examples, laid out as
the charts application, in the frame every PAD application shares - the brand and the menu in
the side, the page's title and the theme switcher in the bar. The menu
has a heading per group of tags and a link per example under it; the home page shows every
example as a card with its result rendered live. An example page has its template and,
when it has one, its data side by side with the result - both read from the page's own files,
so what is shown is exactly what runs.

## Files

| File | Description |
|------|-------------|
| `index.pad` | The home page: a section per tag, a card per example, each drawn through `{page}` |
| `_inits.pad` | The shared frame, `{common:padFrame}`, and on an example page the cells around `@page@` |
| `_inits.php` | The frame's menu and title from the catalog and, for an example, its data source and template |
| `_lib/catalog.php` | The catalog, read from the directories: a tag is a directory with an `_about.txt` |
| `<tag>/_about.txt` | Three lines: the title, one line about the tag, its group |
| `<tag>/<example>.pad` | An example; its first line `{meta title='...', about='...'/}` names it - in the menu and in the bar |
| `<tag>/<example>.php` | The data of an example that builds it in PHP |
| `_config/config.php` | Switches `_common` off: the frame is reached with the `common:` prefix, and nothing else of `_common` is needed |

The stylesheet - the cards and the cells, in the frame's colours, so every theme and light
and dark apply - and the small script, the copy buttons, are `www/showcase/showcase.css` and
`showcase.js`.

## Adding a tag

Make the directory `<tag>/` with its `_about.txt` and an example page - nothing else lists it.
