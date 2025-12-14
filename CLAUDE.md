# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

PAD (PHP Application Driver) is an Inversion of Control PHP template engine where templates drive application flow. Instead of PHP code including templates, PAD templates orchestrate everything from data retrieval to output generation.

**Requirements:** PHP 8.0+

## Key Concepts

### Inversion of Control
Traditional PHP: Code includes templates.
PAD: Templates drive execution, orchestrating data and output.

### Page Pairing
Every page consists of two files:
- `pagename.php` - Returns data (variables, arrays)
- `pagename.pad` - Template that renders the data

### URL Structure
Pages are accessed via query string:
- `/myapp/` → `index.pad`
- `/myapp/?about` → `about.pad`
- `/myapp/?admin/users` → `admin/users.pad`

**Important:** Internal links use `?page` format, not `/page`.

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
├── tag/           # Tag properties: @first, @last, @even, @odd, @current, @count
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

## Application Structure

```
apps/myapp/
├── index.php              # Home page data
├── index.pad              # Home page template
│
├── _inits.php             # Runs BEFORE all pages (optional)
├── _inits.pad             # Wraps ALL pages - use @pad@ placeholder (optional)
├── _exits.php             # Runs AFTER all pages (optional)
├── _exits.pad             # Closing wrapper (optional)
│
├── _lib/                  # Auto-included PHP functions
├── _include/              # Auto-included template snippets
├── _tags/                 # Custom template tags
├── _functions/            # Custom pipe functions
├── _callbacks/            # Data iteration callbacks
├── _options/              # Custom tag options
├── _config/               # Application configuration
│   └── config.php
├── _data/                 # Static data files (XML, JSON)
│
└── subdir/                # Subdirectories can have own wrappers
    ├── _inits.pad
    └── page.pad
```

### Auto-Loaded Directories

| Directory | Purpose | Usage |
|-----------|---------|-------|
| `_lib/` | PHP functions | All `.php` files auto-included |
| `_include/` | Template snippets | `{name}` → `name.pad` |
| `_tags/` | Custom tags | `{mytag}` → `mytag.php` |
| `_functions/` | Pipe functions | `{$x \| myfunc}` → `myfunc.php` |
| `_callbacks/` | Iteration hooks | `callback='name'` → `name.php` |
| `_options/` | Tag options | Custom option handlers |
| `_config/` | App config | `config.php` overrides |
| `_data/` | Static data | XML, JSON files |

## Running PAD

### Web Server
PAD runs through Apache or similar. Entry points are in `www/`.

**Entry point pattern** (`www/myapp/index.php`):
```php
<?php
  include __DIR__ . '/../padHome.php';
  define ( 'APP', "$padHome/apps/myapp/"  );
  define ( 'DAT', "$padHome/DATA/"        );
  include "$padHome/pad/pad.php";
?>
```

### Testing
Run regression tests by visiting `/regression` in browser. Tests compare current output against stored HTML snapshots in `regression/DATA/`.

### Installation
```bash
sudo pad/install/install.sh  # Requires root - sets up DB, Apache, data dirs
```

## Template Syntax

### Variables
```
{$variable}                    # Output variable
{$user.name}                   # Object/array property
{$items[0]}                    # Array index
```

### Pipe Functions
```
{$name | upper}                # Uppercase
{$text | trim | lower}         # Chain multiple
{$date | date ('Y-m-d')}       # With parameters
```

### Loops
```
{users}
  <li>{$name} - {$email}</li>
{/users}
```

### Conditionals
```
{if $count > 0}
  Has items
{elseif $count == 0}
  Empty
{else}
  Invalid
{/if}
```

### Iteration Properties
```
{items}
  {@first}First item{/first}
  {@last}Last item{/last}
  {@even}Even row{/even}
  {@odd}Odd row{/odd}
  Count: {@count}
  Index: {@current}
{/items}
```

### Tags with Options
```
{tagname option="value"}
{data $var=value}
{items sort="name" rows="10"}
```

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

### Level System
Each `{tag}` creates a new level scope. PAD maintains global variables per level in arrays indexed by `$pad` (current level, -1 = root):
- `$padTag[$pad]`, `$padType[$pad]`, `$padOpt[$pad]` - Tag state
- `$padData[$pad]`, `$padCurrent[$pad]` - Data for iteration
- `$padBase[$pad]`, `$padOut[$pad]`, `$padResult[$pad]` - Content/output

### Type Resolution Order
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

## Creating a New Application

1. Create directory: `apps/myapp/`
2. Create entry point: `www/myapp/index.php`
3. Create index page: `apps/myapp/index.php` + `apps/myapp/index.pad`
4. Optionally add `_inits.pad` for common layout

See `apps/NEW.md` for detailed instructions.

**Minimal example** (`apps/hello/`):
```php
// index.php
<?php $message = 'Hello World!'; ?>

// index.pad
<html>
<head><title>Hello</title></head>
<body><h1>{$message}</h1></body>
</html>
```

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
- `apps/NEW.md` - Creating new applications

## Code Conventions

- Framework uses global variables indexed by level (`$padTag[$pad]`)
- Include-based execution (no function calls between modules)
- Thin dispatcher pattern (most files route to subsystems)
- Output buffering for flexible content handling
- Files prefixed with `_` are auto-discovered by the framework
