# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

PAD (PHP Application Driver) is an Inversion of Control PHP template engine where templates drive application flow. Instead of PHP code including templates, PAD templates orchestrate everything from data retrieval to output generation.

**Requirements:** PHP 8.0+

## Running PAD

### Web Server
PAD runs through Apache or similar. The main entry point pattern:
```php
define('APP', '/path/to/apps/myapp/');  // Must end with /
define('DAT', '/path/to/DATA/');        // Must end with /
include '/path/to/pad/pad.php';
```

Entry points are in `www/` - e.g., `www/index.php` serves the main pad app.

### Testing
Run regression tests by visiting `/regression` in browser (uses `apps/pad/regression/`). Tests compare current output against stored HTML snapshots in `regression/DATA/`.

### Installation
```bash
sudo pad/install/install.sh  # Requires root - sets up DB, Apache, data dirs
```

## Architecture

### Core Execution Flow
```
Request → pad/pad.php → config/config.php → start/enter/start.php
       → build/build.php (page assembly)
       → level/level.php (tag processing)
       → eval/ (expression evaluation)
       → Response
```

### Directory Structure
```
pad/           # Framework core
├── pad.php    # Entry point - validates APP/DAT, loads config, starts execution
├── start/     # Execution lifecycle (enter/, start/, end/)
├── build/     # Page assembly (dirs, _lib collection, base template, page processing)
├── level/     # Tag processing and scope management (26 nested levels max)
├── occurrence/# Data iteration ({users}...{/users} loops)
├── walk/      # Template tree walking
├── eval/      # Expression parser and evaluator
├── types/     # Tag type handlers (25+): app, pad, data, field, table, function, etc.
├── tags/      # Template tags (50+): if, while, data, files, page, etc.
├── functions/ # Pipe functions (44): trim, upper, date, html, contains, etc.
├── options/   # Tag options (50+): sort, first, page, rows, cache, etc.
├── tag/       # Tag properties: @first, @last, @even, @odd, @current, @count, @key
├── lib/       # PHP helper functions
├── config/    # Configuration and presets
├── error/     # Error handling (boot-time and runtime)
└── inits/     # Framework initialization (24 modules)

apps/          # PAD applications
├── pad/       # Main reference app with manual, fragments, regression tests
├── cli/       # CLI application
├── minimal/   # Minimal example
└── nono/      # Plain PHP (no PAD templating)

www/           # Web entry points
DATA/          # Runtime data (cache, logs, temp) - writable by web server
```

### How PAD Pairs Files
- `pagename.php` returns data (array)
- `pagename.pad` is the template
- URL `/pagename` automatically pairs them

### Template Inheritance
```
APP/
├── _inits.pad    # Wraps ALL pages (outer layout)
├── _inits.php    # Runs BEFORE all pages
├── _exits.pad    # Wraps ALL pages (closing)
├── _exits.php    # Runs AFTER all pages
├── _lib/         # Auto-included libraries
└── subdir/
    ├── _inits.pad  # Additional wrapper for this section
    └── page.pad    # Inherits all parent _inits.pad wrappers
```

The `@pad@` placeholder in `_inits.pad` is replaced with child content.

### Level System
Each `{tag}` creates a new level scope. PAD maintains 54 global variables per level in arrays indexed by `$pad` (current level, -1 = root). Key level variables defined in `inits/const.php`:
- `padTag`, `padType`, `padPrm`, `padOpt` - Tag parsing state
- `padData`, `padCurrent`, `padResult`, `padOut` - Data and output
- `padSetLvl`, `padSetOcc` - Variable storage per level/occurrence

### Type Resolution Order
When processing `{tagname}`: app → pad → data → content → field → tag

### Tag Syntax
```
{$variable}                    # Output variable
{$name | upper | trim}         # Pipe functions
{tagname option="value"}       # Tag with options
{data $var=value}              # Set variable
{if $condition}...{/if}        # Paired tags
{@first}, {@last}, {@count}    # Iteration properties
```

## Key Configuration

`pad/config/config.php` controls:
- `$padErrorAction`: 'pad', 'boot', 'php', 'stop', 'exit', 'ignore', 'log', 'dump'
- `$padInfo`: Debug mode - use values from `config/info/` (all, trace, stats, track, xml, xref)
- `$padOutputType`: 'web', 'file', 'download', 'console'
- `$padCache`: Enable caching
- SQL connection settings for PAD internal and application databases

## Debugging

- Set `$padInfo = 'trace'` in config for execution tracing
- Use `{dump}` tag in templates
- Use `{trace}` tag for variable inspection
- Check `DATA/` directory for error dumps and logs

## Creating a New Application

1. Create directory in `apps/myapp/`
2. Add `index.php` (return data array) and `index.pad` (template)
3. Create entry point in `www/myapp/index.php`:
   ```php
   include '../padHome.php';
   define('APP', "$padHome/apps/myapp/");
   define('DAT', "$padHome/DATA/");
   include "$padHome/pad/pad.php";
   ```

## Documentation

Each framework module has:
- `README.md` - Module documentation
- `REFERENCE.md` - API reference (in tags/, functions/, options/, types/, tag/, constructs/)
- `BUGS.md` - Known issues

Key references:
- `pad/tags/REFERENCE.md` - All template tags
- `pad/functions/REFERENCE.md` - All pipe functions
- `pad/options/REFERENCE.md` - All tag options
- `pad/eval/EXPLAIN.md` - Expression evaluation internals
