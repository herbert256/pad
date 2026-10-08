# Showcase Application

## Introduction

A showcase of the tags that draw, format and interact - diagrams, maps, avatars, tables,
diffs, thumbnails, tabs, puzzles and the rest - each with a few worked examples, in the frame
of the charts application. The menu has a pulldown per group of tags; the home page shows
every example as a card with its result rendered live. An example page has its template and,
when it has one, its data side by side with the result - both read from the page's own files,
so what is shown is exactly what runs.

## Files

| File | Description |
|------|-------------|
| `index.pad` | The home page: a section per tag, a card per example, each drawn through `{page}` |
| `_inits.pad` | The frame: the top bar with the pulldowns and the light/dark switch, and on an example page the cells around `@page@` |
| `_inits.php` | The menu from the catalog and, for an example, its data source and template |
| `_lib/catalog.php` | The catalog, read from the directories: a tag is a directory with an `_about.txt` |
| `<tag>/_about.txt` | Three lines: the title, one line about the tag, its group |
| `<tag>/<example>.pad` | An example; its first line `{meta title='...', about='...'}` names it |
| `<tag>/<example>.php` | The data of an example that builds it in PHP |
| `_config/config.php` | Switches `_common` off: the application has a design of its own |

The stylesheet and the small script are `www/showcase/showcase.css` and `showcase.js`.

## Adding a tag

Make the directory `<tag>/` with its `_about.txt` and an example page - nothing else lists it.
