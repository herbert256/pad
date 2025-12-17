# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

PAD (PHP Application Driver) is an Inversion of Control PHP template engine where templates drive application flow. Instead of PHP code including templates, PAD templates orchestrate everything from data retrieval to output generation.

**Requirements:** PHP 8.0+

## Documentation Structure

See [docs/README.md](docs/README.md) for the full documentation index.

| File | Contents |
|------|----------|
| [docs/pad/](docs/pad/README.md) | Framework internals, execution flow, configuration |
| [docs/APP.md](docs/APP.md) | Application development, template syntax, tags, patterns |
| [docs/sequences/](docs/sequences/README.md) | Sequence subsystem (80+ mathematical sequences) |

## Quick Start

### Page Pairing
Every page consists of two files:
- `pagename.php` - Returns data (variables, arrays)
- `pagename.pad` - Template that renders the data

### Minimal Example
```php
// apps/hello/index.php
<?php $message = 'Hello World!'; ?>

// apps/hello/index.pad
<html>
<body><h1>{$message}</h1></body>
</html>
```

### URL Structure
- `/myapp/` → `index.pad`
- `/myapp/?about` → `about.pad`
- `/myapp/?admin/users` → `admin/users.pad`

**Important:** Internal links use `?page` format, not `/page`.

## Essential Syntax

```
{$variable}                    # Output variable
{$user.name}                   # Property access
{echo $text | upper | trim}    # Pipe functions (need {echo})
{if $x eq 1}...{/if}          # Conditional
{users}...{/users}            # Iterate array
{set $count = 0}              # Variable assignment
{get 'fragments/nav'}         # Include page
```

## Key Rules

1. **Pipes need `{echo}`** - `{$var | upper}` won't work, use `{echo $var | upper}`
2. **Arithmetic needs space** - `{echo $x | + 1}` not `| +1`
3. **Quote literal strings** - `{count 'items'}` not `{count items}`
4. **No inline CSS/JS** - PAD parses `{ }` as tags; use external files

## Project Structure

```
pad/               # Framework core (see pad/README.md)
apps/              # PAD applications (see APP.md)
www/               # Web entry points
DATA/              # Runtime data (skip when analyzing code)
```

## Application Structure

```
apps/myapp/
├── index.php / index.pad     # Page pair
├── _inits.php / _inits.pad   # Run before/wrap all pages
├── _lib/                     # Auto-included PHP functions
├── _tags/                    # Custom template tags
├── _functions/               # Custom pipe functions
├── _data/                    # Static data files (JSON, XML)
└── _config/config.php        # App configuration
```

## Key References

- [docs/reference/](docs/reference/README.md) - All reference documentation
- [docs/reference/TAGS.md](docs/reference/TAGS.md) - All template tags
- [docs/reference/FUNCTIONS.md](docs/reference/FUNCTIONS.md) - All pipe functions
- [docs/reference/OPTIONS.md](docs/reference/OPTIONS.md) - All tag options
- [docs/reference/PROPERTIES.md](docs/reference/PROPERTIES.md) - All iteration properties (first@tag, count@tag, etc.)
- [docs/NEW.md](docs/NEW.md) - Creating new applications

## Quick Reference

| Syntax | Purpose |
|--------|---------|
| `{$var}` | Output variable |
| `{!var}` | HTML-escaped output |
| `{echo expr}` | Evaluate expression |
| `{echo $x \| func}` | Pipe function |
| `{set $x = val}` | Assign variable |
| `{if cond}...{/if}` | Conditional |
| `{tag}...{/tag}` | Iterate array |
| `{first@tag}` | Iteration property |
| `{break}` / `{continue}` / `{cease}` | Loop control |
| `{get 'page'}` | Include page |
| `{case $x}{when 'a'}...{/case}` | Switch/case |

### Comparison Operators
`eq`/`==`, `ne`/`!=`, `gt`/`>`, `lt`/`<`, `ge`/`>=`, `le`/`<=`, `and`, `or`, `range (a,b)`

### Common Pipes
`upper`, `lower`, `trim`, `html`, `date('fmt')`, `+ n`, `- n`, `* n`, `/ n`, `left(n)`, `after('x')`, `before('x')`

## Type Prefixes

Resolve naming conflicts with explicit prefixes:
`app:`, `pad:`, `php:`, `data:`, `content:`, `pull:`, `field:`, `table:`, `function:`, `local:`, `array:`, `bool:`

Example: `{app:mytag}`, `{pull:mySequence}`, `{php:strlen(@)}`
