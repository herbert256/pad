# PAD Framework Documentation Index

This file provides an index to all documentation files (`*.md`) in the PAD framework.

---

## Overview

| File | Description |
|------|-------------|
| [CLAUDE.md](CLAUDE.md) | Main project overview, architecture, and code conventions |

---

## Bug Reports

| File | Description |
|------|-------------|
| [lib/BUGS.md](lib/BUGS.md) | Bugs found in core library functions (13 issues) |
| [level/BUGS.md](level/BUGS.md) | Bugs found in main processing loop (9 issues) |

---

## Detailed Explanations

| File | Description |
|------|-------------|
| [eval/EXPLAIN.md](eval/EXPLAIN.md) | Deep dive into the expression evaluation system |
| [sequence/EXPLAIN.md](sequence/EXPLAIN.md) | Comprehensive guide to mathematical sequences |

---

## Tag Reference

| File | Description |
|------|-------------|
| [tags/TAGS.md](tags/TAGS.md) | Reference for all built-in PAD tags |

---

## Module Documentation (CLAUDE.md files)

### Core Processing

| Directory | File | Description |
|-----------|------|-------------|
| `level/` | [CLAUDE.md](level/CLAUDE.md) | Main tag processing loop - heart of the engine |
| `occurrence/` | [CLAUDE.md](occurrence/CLAUDE.md) | Template parsing and tag occurrence detection |
| `build/` | [CLAUDE.md](build/CLAUDE.md) | Template construction from page files |
| `types/` | [CLAUDE.md](types/CLAUDE.md) | Tag type handlers (app, pad, data, function, etc.) |

### Evaluation & Expressions

| Directory | File | Description |
|-----------|------|-------------|
| `eval/` | [CLAUDE.md](eval/CLAUDE.md) | Expression parsing and evaluation |
| `at/` | [CLAUDE.md](at/CLAUDE.md) | Property access via `@` syntax |
| `sequence/` | [CLAUDE.md](sequence/CLAUDE.md) | Mathematical sequence generators |

### Tags & Functions

| Directory | File | Description |
|-----------|------|-------------|
| `tags/` | [CLAUDE.md](tags/CLAUDE.md) | Built-in control flow tags (if, while, array, etc.) |
| `tag/` | [CLAUDE.md](tag/CLAUDE.md) | Tag value resolution |
| `functions/` | [CLAUDE.md](functions/CLAUDE.md) | Pipe functions (trim, upper, replace, etc.) |
| `constructs/` | [CLAUDE.md](constructs/CLAUDE.md) | Language constructs |

### Data & Storage

| Directory | File | Description |
|-----------|------|-------------|
| `data/` | [CLAUDE.md](data/CLAUDE.md) | Data store operations |
| `database/` | [CLAUDE.md](database/CLAUDE.md) | Database connectivity |
| `cache/` | [CLAUDE.md](cache/CLAUDE.md) | Caching system |

### Control Flow

| Directory | File | Description |
|-----------|------|-------------|
| `handling/` | [CLAUDE.md](handling/CLAUDE.md) | Tag handling and flow control |
| `walk/` | [CLAUDE.md](walk/CLAUDE.md) | Data iteration/walking |
| `options/` | [CLAUDE.md](options/CLAUDE.md) | Tag option processing |

### Initialization & Configuration

| Directory | File | Description |
|-----------|------|-------------|
| `start/` | [CLAUDE.md](start/CLAUDE.md) | Application startup |
| `inits/` | [CLAUDE.md](inits/CLAUDE.md) | Initialization routines |
| `config/` | [CLAUDE.md](config/CLAUDE.md) | Configuration settings |
| `install/` | [CLAUDE.md](install/CLAUDE.md) | Installation procedures |

### Error Handling & Exit

| Directory | File | Description |
|-----------|------|-------------|
| `error/` | [CLAUDE.md](error/CLAUDE.md) | Error handling system |
| `exits/` | [CLAUDE.md](exits/CLAUDE.md) | Exit and output finalization |
| `try/` | [CLAUDE.md](try/CLAUDE.md) | Try/catch wrappers |

### Utilities

| Directory | File | Description |
|-----------|------|-------------|
| `lib/` | [CLAUDE.md](lib/CLAUDE.md) | Core library functions |
| `call/` | [CLAUDE.md](call/CLAUDE.md) | Function call utilities |
| `callback/` | [CLAUDE.md](callback/CLAUDE.md) | Callback handling |
| `get/` | [CLAUDE.md](get/CLAUDE.md) | Value retrieval utilities |

### Debugging & Events

| Directory | File | Description |
|-----------|------|-------------|
| `info/` | [CLAUDE.md](info/CLAUDE.md) | Debug info and tracing |
| `events/` | [CLAUDE.md](events/CLAUDE.md) | Event system |

---

## Quick Reference

### Finding Information

| Looking for... | See... |
|----------------|--------|
| How PAD works overall | [CLAUDE.md](CLAUDE.md) |
| How expressions are evaluated | [eval/EXPLAIN.md](eval/EXPLAIN.md) |
| How sequences work | [sequence/EXPLAIN.md](sequence/EXPLAIN.md) |
| List of built-in tags | [tags/TAGS.md](tags/TAGS.md) |
| Known bugs | [lib/BUGS.md](lib/BUGS.md), [level/BUGS.md](level/BUGS.md) |
| Main processing loop | [level/CLAUDE.md](level/CLAUDE.md) |
| Pipe functions | [functions/CLAUDE.md](functions/CLAUDE.md) |

### File Statistics

- **Total documentation files:** 36
- **CLAUDE.md files:** 30 (module documentation)
- **BUGS.md files:** 2 (bug reports)
- **EXPLAIN.md files:** 2 (detailed explanations)
- **Other:** 2 (TAGS.md, INDEX.md)

---

*Last updated: December 2024*
