# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

PAD (PHP Application Driver) is an Inversion of Control template engine where templates drive application flow. Instead of PHP code including templates, PAD templates orchestrate everything from data retrieval to output generation.

## Directory Structure

- `pad/` - Core template engine
- `apps/` - Applications (each subdirectory is a separate app)
- `www/` - Web entry points (routes to apps via `pad.php`)
- `DATA/` - Runtime data (regression tests, examples, reference docs)

## Running the Application

PAD runs as a web application. Access apps via URL path:
- `http://localhost/demo` runs the `apps/demo` application
- `http://localhost/regression` shows regression test status
- `http://localhost/develop` provides development tools

For CLI execution, apps can include a `pad` script (see `apps/cli/pad`).

## Development Workflow

### Build and Test

Access via browser at `/develop`:
- **Build all** (`/develop?build`) - Rebuilds regression data, reference docs, and examples
- **Run regression** (`/develop?regression`) - Reset and run regression tests
- **Find errors** (`/develop?errors`) - Show pages with errors

### Regression Tests

Tests compare current output against stored expected output in `DATA/regression/`.
- Status `ok` - Output matches expected
- Status `warning` - Output differs from stored (review needed)
- Status `new` - First run, baseline stored
- Status `error` - HTTP error occurred
- Status `random` - Contains randomness, skipped comparison

View test results at `/regression`.

## Architecture

### Template Processing Flow

1. Entry: `www/pad.php` detects app from URL, sets `$padApp`, `$padApps`, `$padData`
2. Bootstrap: `pad/pad.php` defines constants (PAD, APP, DAT, APPS, DATA, COMMON)
3. Init: `pad/start/enter/pad.php` loads error handling, config, and starts processing
4. Level processing: `pad/level/level.php` - recursive template parser

### Application Structure

Each app in `apps/` can contain:
- `*.pad` - Template files (main content)
- `*.php` - Associated PHP for data/logic (runs before template)
- `_inits.pad` - Layout wrapper (use `@pad@` for content insertion point)
- `_inits.php` - App initialization
- `_exits.php` - Cleanup code
- `_config/config.php` - App-specific configuration
- `_tags/` - Custom tags
- `_lib/` - Helper functions
- `_data/` - Static data files
- `_include/` - Includable templates

### Tag System

Tags are defined in `pad/tags/`. Template syntax:
- `{tagname params}...{/tagname}` - Block tags
- `{$var}` - Variable output
- `{$var | function}` - Piped functions
- `{if condition}...{elseif}...{else}...{/if}` - Conditionals
- `{sequence '1..10'}...{/sequence}` - Iteration

Functions for pipes are in `pad/functions/`.

### Shared Resources

`apps/_common/` provides shared resources across all apps:
- `_inits.pad` - Default HTML wrapper
- `_tags/` - Common custom tags
- `_lib/` - Shared helper functions

### Configuration

Global config: `pad/config/config.php`
App-specific: `apps/[app]/_config/config.php`

Key settings:
- `$padOutputType` - 'web' or 'console'
- `$padTidy` - HTML tidying
- `$padCache` - Caching enabled
- `$padErrorLevel` - Error reporting level
