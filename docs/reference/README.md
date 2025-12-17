# PAD Reference Documentation

This directory contains reference documentation for PAD template syntax elements.

## Template Syntax

| File | Description |
|------|-------------|
| [TAGS.md](TAGS.md) | All template tags (if, while, data, files, etc.) |
| [FUNCTIONS.md](FUNCTIONS.md) | All pipe functions (trim, upper, date, html, etc.) |
| [OPTIONS.md](OPTIONS.md) | All tag options (sort, rows, cache, etc.) |
| [PROPERTIES.md](PROPERTIES.md) | Iteration properties (first@tag, count@tag, etc.) |
| [TYPES.md](TYPES.md) | Tag type handlers (app, pad, data, field, etc.) |
| [CONSTRUCTS.md](CONSTRUCTS.md) | Template constructs and syntax structures |

## Processing

| File | Description |
|------|-------------|
| [EVAL.md](EVAL.md) | Expression evaluation internals and parser details |
| [HANDLING.md](HANDLING.md) | Error and exception handling |

## Sequences

| File | Description |
|------|-------------|
| [SEQUENCES.md](../sequences/SEQUENCES.md) | Sequence types (80+ mathematical sequences) |
| [ACTIONS.md](../sequences/ACTIONS.md) | Sequence actions and transformations |

## Quick Reference

### Most Used Tags
`{if}`, `{while}`, `{data}`, `{set}`, `{echo}`, `{get}`, `{case}`

### Most Used Functions
`trim`, `upper`, `lower`, `html`, `date`, `+ n`, `- n`

### Most Used Options
`rows`, `sort`, `first`, `last`, `cache`

### Most Used Properties
`first@tag`, `last@tag`, `count@tag`, `current@tag`, `even@tag`
