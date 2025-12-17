# PAD Framework Internals

This document covers the PAD framework architecture, execution flow, and configuration.

## Project Structure

```
pad/               # Framework core
├── pad.php        # Entry point - validates APP/DAT, loads config, starts execution
├── start/         # Execution lifecycle (enter/, start/, end/)
├── build/         # Page assembly (dirs, _lib collection, base template, page processing)
├── level/         # Tag processing and scope management
├── occurrence/    # Data iteration ({items}...{/items} loops)
├── walk/          # Template tree walking
├── eval/          # Expression parser and evaluator
├── types/         # Tag type handlers: app, pad, data, field, table, function, etc.
├── tags/          # Template tags: if, while, data, files, page, etc.
├── functions/     # Pipe functions: trim, upper, date, html, contains, etc.
├── options/       # Tag options: sort, first, page, rows, cache, etc.
├── tag/           # Tag properties: property:first, property:last, property:even, etc.
├── lib/           # PHP helper functions
├── config/        # Configuration and presets
├── error/         # Error handling (boot-time and runtime)
├── inits/         # Framework initialization modules
└── sequence/      # Sequence/query system

apps/              # PAD applications (each subdirectory is an app)
├── pad/           # Reference app (manual + regression tests)
├── demo/          # Demo application with examples
├── hello/         # Minimal hello world example
├── cli/           # CLI application
├── minimal/       # Minimal example
└── nono/          # Plain PHP (no PAD templating)

www/               # Web entry points (one per app)
DATA/              # Runtime data (cache, logs, temp) - writable by web server
```

**Note:** Directories named `DATA/` contain generated files (regression tests, cross-references). Skip these when analyzing source code.

## Core Execution Flow

```
Request → pad.php → config → inits/ (initialization)
                          → build/ (page assembly)
                          → level/ (tag processing loop)
                          → Response

For each {tag}:
  level/level.php → detect type → types/{type}.php → process
                  → if data array: occurrence/ (iterate)
```

## Level System

Each `{tag}` creates a new level scope. PAD maintains global variables per level in arrays indexed by `$pad` (current level, -1 = root):
- `$padTag[$pad]`, `$padType[$pad]`, `$padOpt[$pad]` - Tag state
- `$padData[$pad]`, `$padCurrent[$pad]` - Data for iteration
- `$padBase[$pad]`, `$padOut[$pad]`, `$padResult[$pad]` - Content/output

## Type Resolution Order

When processing `{tagname}`: app → pad → data → content → field → tag

## Configuration

`pad/config/config.php` or `apps/myapp/_config/config.php`:

```php
$padErrorAction   // 'pad', 'boot', 'php', 'stop', 'exit', 'ignore', 'log', 'dump'
$padInfo          // Debug: 'trace', 'stats', 'track', 'xml', 'xref'
$padOutputType    // 'web', 'file', 'download', 'console'
$padCache         // Enable caching

// Database
$padSqlHost
$padSqlDatabase
$padSqlUser
$padSqlPassword
```

## Debugging

- Set `$padInfo = 'trace'` in config for execution tracing
- Use `{dump}` tag in templates for variable inspection
- Use `{trace}` tag for execution trace
- Check `DATA/` directory for error dumps and logs

## Code Conventions

- Framework uses global variables indexed by level (`$padTag[$pad]`)
- Include-based execution (no function calls between modules)
- Thin dispatcher pattern (most files route to subsystems)
- Output buffering for flexible content handling
- Files prefixed with `_` are auto-discovered by the framework

## Documentation

Each framework module has:
- `README.md` - Module documentation
- `REFERENCE.md` - API reference
- `BUGS.md` - Known issues
- `EXPLAIN.md` - Internal workings

Key references:
- `pad/tags/REFERENCE.md` - All template tags
- `pad/functions/REFERENCE.md` - All pipe functions
- `pad/options/REFERENCE.md` - All tag options
- `pad/eval/EXPLAIN.md` - Expression evaluation internals

## Installation

```bash
sudo pad/install/install.sh  # Requires root - sets up DB, Apache, data dirs
```

## Testing

Run regression tests by visiting `/regression` in browser. Tests compare current output against stored HTML snapshots in `regression/DATA/`.
