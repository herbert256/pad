# PAD Reference Documentation

This directory contains reference documentation for PAD template syntax elements.

Every named tag requires a type prefix, including closing tags and branches: `{pad:if}`, `{pad:else}`, `{/pad:if}`, `{array:items}` and `{app:card}`. Variable and expression shorthand such as `{$name}`, `{!html}` and `{^data}` is unchanged. See [TYPES.md](TYPES.md) for the namespace map.

## Template Syntax

| File | Description |
|------|-------------|
| [TAGS.md](TAGS.md) | All template tags (if, while, data, files, etc.) |
| [FUNCTIONS.md](FUNCTIONS.md) | All pipe functions (trim, upper, date, html, etc.) |
| [OPTIONS.md](OPTIONS.md) | All tag options (sort, rows, cache, etc.) |
| [PROPERTIES.md](PROPERTIES.md) | Iteration properties (first@tag, count@tag, etc.) |
| [TYPES.md](TYPES.md) | Tag type handlers (app, pad, data, field, etc.) |
| [CONSTRUCTS.md](CONSTRUCTS.md) | Template constructs and syntax structures |

## PHP Code

| File | Description |
|------|-------------|
| [HELPERS.md](HELPERS.md) | Functions for a page's PHP - arrays, strings, values, requests, sessions, cache, dates, hashing, and the engine functions: redirects, forms, database, errors |

## Processing

| File | Description |
|------|-------------|
| [EVAL.md](EVAL.md) | Expression evaluation internals and parser details |
| [HANDLING.md](HANDLING.md) | Data handling options - sort, where, group, first, page, rows, slice, dedup, ... |

## Sequences

| File | Description |
|------|-------------|
| [SEQUENCES.md](../sequences/SEQUENCES.md) | Sequence types (122 sequence types) |
| [ACTIONS.md](../sequences/ACTIONS.md) | Sequence actions and transformations |

## Quick Reference

### Most Used Tags
`{pad:if}`, `{pad:while}`, `{pad:data}`, `{pad:set}`, `{pad:echo}`, `{pad:get}`, `{pad:case}`

### Most Used Functions
`trim`, `upper`, `lower`, `html`, `date`, `+ n`, `- n`

### Most Used Options
`rows`, `sort`, `first`, `last`, `cache`

### Most Used Properties
`first@tag`, `last@tag`, `count@tag`, `current@tag`, `even@tag`
