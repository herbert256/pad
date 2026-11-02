# Charts Application

## Introduction

A showcase of the `{chart:bar}` tag: every kind of chart it draws, each with a few worked examples,
in the frame every PAD application shares - the brand and the menu in the side, the page's
title and the theme switcher in the bar. The menu has a heading per
chart family and a link per example under it; the home page shows every example as a card with
its chart drawn live. An example page has three cells - the data (JSON, YAML or PHP), the
`{chart:bar}` tag that draws it, and the result - and both source cells are read from the page's
own files, so what is shown is exactly what runs. An example written as a pair,
`{chart:bar ...}` with its data (CSV, JSON, YAML or XML) between the tags, has one source cell for
both. The page fits what the bar leaves of the window: the cells keep the same size on every
example, so the previous and next links stay in one place.

## Files

| File | Description |
|------|-------------|
| `index.pad` | The home page: an introduction and a card per example, each drawn through `{pad:page}` |
| `_inits.pad` | The shared frame, `{common:padFrame}`, and on an example page the three cells around `@page@` |
| `_inits.php` | The frame's menu and title from the catalog and, for an example, its data source and template, coloured by the engine's `padHighlightTokens` (lib/highlight.php) |
| `_lib/catalog.php` | The chart families and their examples - title and line of text per page |
| `_data/*.json`, `_data/*.yaml` | The data of the examples that read a data file |
| `<family>/<example>.pad` | An example: the `{chart:bar}` tag, one option per line - or a pair with the data between the tags (a template for the sparkline table) |
| `<family>/<example>.php` | The data of the examples that build it in PHP |
| `_config/config.php` | Switches `_common` off: the frame is reached with the `common:` prefix, and nothing else of `_common` is needed |

The stylesheet - the cards and the cells, in the frame's colours, so every theme and light
and dark apply - and the small script, the copy buttons, are `www/charts/charts.css` and
`www/charts/charts.js`.

## Adding an example

1. Write `family/name.pad` with the `{chart:bar}` tag.
2. Give it data: `family/name.php`, `_data/<data>.json` / `.yaml` named by `data='<data>'`, or
   the data between `{chart:bar ...}` and `{/chart:bar}` - or none, for a gauge of one number.
3. Add `'family/name' => [ 'Title', 'One line about it.' ]` to `_lib/catalog.php`. A family is
   `[ name, tagline, examples ]`.

The YAML examples need PHP's yaml extension, as every YAML data file in PAD does.
