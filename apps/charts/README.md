# Charts Application

## Introduction

A showcase of the `{chart}` tag: every kind of chart it draws, each with a few worked examples.
The menu has a pulldown per chart family; the home page shows every example as a card with its
chart drawn live. An example page has three cells - the data (JSON, YAML or PHP), the `{chart}`
tag that draws it, and the result - and both source cells are read from the page's own files,
so what is shown is exactly what runs.

## Files

| File | Description |
|------|-------------|
| `index.pad` | The home page: a hero and a card per example, each drawn through `{page}` |
| `_inits.pad` | The frame: the top bar with the pulldowns and the light/dark switch, and on an example page the three cells around `@page@` |
| `_inits.php` | The menu from the catalog and, for an example, its data source and template, coloured |
| `_lib/catalog.php` | The chart families and their examples - title and line of text per page |
| `_lib/highlight.php` | The colouring of JSON, YAML, PHP and PAD in the source cells |
| `_data/*.json`, `_data/*.yaml` | The data of the examples that read a data file |
| `<family>/<example>.pad` | An example: the `{chart}` tag (a template for the sparkline table) |
| `<family>/<example>.php` | The data of the examples that build it in PHP |
| `_config/config.php` | Switches `_common` off: the application has a design of its own |

The stylesheet and the small script - copy buttons, the theme switch, pulldowns on a tap - are
`www/charts/charts.css` and `www/charts/charts.js`.

## Adding an example

1. Write `family/name.pad` with the `{chart}` tag.
2. Give it data: `family/name.php`, or `_data/<data>.json` / `.yaml` named by `data='<data>'`.
3. Add `'family/name' => [ 'Title', 'One line about it.' ]` to `_lib/catalog.php`.

The YAML examples need PHP's yaml extension, as every YAML data file in PAD does.
