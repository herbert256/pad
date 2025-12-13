# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

PAD is a PHP template engine/framework that processes template files containing curly-brace tags (`{tagname}...{/tagname}`). It handles template parsing, variable substitution, data binding, control flow, and output generation.

## Architecture

### Entry Point and Initialization
- `pad.php` - Main entry point. Requires APP and DAT constants to be defined before inclusion
- Initialization flow: `pad.php` -> `error/boot.php` -> `config/config.php` -> `start/enter/start.php`
- All library functions are auto-loaded from `lib/` directory recursively via `inits/lib.php`

### Core Processing Pipeline
1. **Build** (`build/`) - Constructs the template base from page files
2. **Occurrence** (`occurrence/`) - Parses template to find tag occurrences
3. **Level** (`level/`) - Processes tags level by level in nested structure
4. **Type Resolution** (`level/type.php`, `lib/type.php`) - Determines tag type (app, pad, data, function, etc.)
5. **Tag Execution** (`types/`) - Executes tag based on resolved type
6. **Output** (`exits/`) - Finalizes and outputs result

### Tag Types (`types/` directory)
Tags are resolved to types which determine how they're processed:
- `app` - Application-defined tags (looked up in APP2 directory)
- `pad` - Built-in PAD tags (in `tags/` directory as .php or .pad files)
- `data` - Data store values (`$padDataStore`)
- `content` - Content store values (`$padContentStore`)
- `function` - Functions (app functions or `functions/` directory)
- `field` - Database/form fields
- `property` - Tag properties accessed via `@`
- `include`, `page`, `sequence`, `script`, `local`, `constant`, etc.

### Built-in Tags (`tags/` directory)
Control flow and logic tags like `if`, `while`, `switch`, `case`, `array`, `data`, `set`, etc.

### Functions (`functions/` directory)
String manipulation and utility functions: `trim`, `upper`, `lower`, `left`, `right`, `replace`, `sanitize`, etc. Used in variable expressions via pipes.

### Variable Expression System (`eval/`, `lib/eval/`)
Handles expressions within curly braces:
- `{$field}` - Field values
- `{&tag}` - Tag values
- `{#opt}` - Option values
- Pipes for transformations: `{$name|upper|trim}`

### Special Prefixes
- `$` - Field/variable reference
- `&` - Tag value reference
- `#` - Option/parameter reference
- `@` - Property access (via `at/` directory)
- `!`, `?` - Conditional prefixes

### Configuration (`config/config.php`)
Key settings:
- `$padErrorAction` - Error handling mode ('pad', 'boot', 'php', 'stop', 'exit', 'ignore', 'log', 'dump')
- `$padOutputType` - Output destination ('web', 'file', 'download', 'console')
- `$padCache` - Caching toggle
- SQL connection parameters for both PAD internal and application databases

### Constants
- `PAD` - Path to PAD framework directory
- `APP` - Application root (must end with `/`)
- `DAT` - Data directory (must end with `/`)
- `PQ` - Sequence directory (`PAD/sequence/`)
- `PT` - Sequence types directory

### Global State Arrays
Level-scoped variables defined in `inits/const.php` as `padLevelVars`:
- `padTag`, `padType`, `padPair` - Current tag info
- `padData`, `padCurrent` - Data context
- `padBase`, `padOut`, `padResult` - Content accumulation
- `padStart`, `padEnd` - Tag lifecycle flags

## Code Conventions

- Function names use `pad` prefix (e.g., `padTypeTag`, `padEval`, `padOutput`)
- Global variables use `$pad` prefix
- Tag handler files return values (TRUE for success, content string, or array for iteration)
- Extensive use of PHP's `include` for code organization and flow control
