# PAD Reference Documentation

This directory contains reference documentation for PAD template syntax elements.

## Documents

| File | Description |
|------|-------------|
| [CONSTRUCTS.md](CONSTRUCTS.md) | Template constructs and syntax structures |
| [EVAL.md](EVAL.md) | Expression evaluation internals and parser details |
| [FUNCTIONS.md](FUNCTIONS.md) | All pipe functions (trim, upper, date, html, etc.) |
| [HANDLING.md](HANDLING.md) | Error and exception handling |
| [OPTIONS.md](OPTIONS.md) | All tag options (sort, rows, cache, etc.) |
| [PROPERTIES.md](PROPERTIES.md) | Iteration properties (first@tag, count@tag, etc.) |
| [TAGS.md](TAGS.md) | All template tags (if, while, data, files, etc.) |
| [TYPES.md](TYPES.md) | Tag type handlers (app, pad, data, field, etc.) |

## Quick Reference

### Most Used Tags
`{if}`, `{while}`, `{data}`, `{set}`, `{echo}`, `{get}`, `{case}`

### Most Used Functions
`trim`, `upper`, `lower`, `html`, `date`, `+ n`, `- n`

### Most Used Options
`rows`, `sort`, `first`, `last`, `cache`

### Most Used Properties
`first@tag`, `last@tag`, `count@tag`, `current@tag`, `even@tag`
