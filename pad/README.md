# PAD Framework

**PHP Application Driver** - An Inversion of Control Template Engine

PAD is a PHP template engine that inverts the traditional web application architecture. Instead of PHP code including templates, PAD templates drive the execution flow, seamlessly integrating data access, control structures, and presentation in a unified template syntax.

**Requirements:** PHP 8.0+

## Key Concepts

### Inversion of Control
- **Traditional PHP:** Code includes templates
- **PAD:** Templates drive execution, orchestrating data and output

### Page Pairing
Every page consists of two files that PAD automatically pairs:
- `pagename.php` - Returns data (variables, arrays)
- `pagename.pad` - Template that renders the data

### URL Structure
Pages are accessed via query string:
- `/myapp/` → `index.pad`
- `/myapp/?about` → `about.pad`
- `/myapp/?admin/users` → `admin/users.pad`

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
└── sequence/      # Sequence/query system (80+ mathematical sequences)

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

## Quick Start

### Entry Point
```php
<?php
include __DIR__ . '/../padHome.php';
define('APP', "$padHome/apps/myapp/");
define('DAT', "$padHome/DATA/");
include "$padHome/pad/pad.php";
?>
```

### Hello World Example

**index.php:**
```php
<?php
$message = 'Hello World!';
$items = ['Apple', 'Banana', 'Cherry'];
?>
```

**index.pad:**
```html
<html>
<head><title>Hello</title></head>
<body>
  <h1>{$message}</h1>
  <ul>
    {items}
      <li>{$items}</li>
    {/items}
  </ul>
</body>
</html>
```

## Template Syntax

### Variables
```
{$variable}                    # Output variable
{$user.name}                   # Object/array property
{$items[0]}                    # Array index
{!text}                        # HTML-escaped output
```

### Pipe Functions
```
{echo $name | upper}           # Uppercase
{echo $text | trim | lower}    # Chain multiple functions
{echo $date | date('Y-m-d')}   # With parameters
{echo $value | + 1}            # Arithmetic (space required)
{echo $text | after('@')}      # String manipulation
```

**Important:** Pipes require `{echo}` - bare `{$var | func}` won't work.

### Loops
```
{users}
  <li>{$name} - {$email}</li>
{/users}

{while $i le 10}
  Item {$i}
  {increment $i}
{/while}
```

### Loop Control
```
{continue 'tagname'}    # Skip to next iteration
{cease 'tagname'}       # Soft stop (graceful end)
{break 'tagname'}       # Hard stop (immediate exit)
```

### Conditionals
```
{if $count gt 0}
  Has items
{elseif $count eq 0}
  Empty
{else}
  Invalid
{/if}
```

### Iteration Properties
```
{items}
  {property:first}First!{/property:first}
  {property:even}Even row{/property:even}
  Index: {property:current} of {property:count}
{/items}
```

### Data Definition
```
{data 'colors'}
  ["red", "green", "blue"]
{/data}

{colors}{$colors} {/colors}
```

Supports JSON, XML, YAML, and CSV formats.

### Case/Switch
```
{case $color}
  {when 'red'} Stop
  {when 'yellow'} Caution
  {when 'green'} Go
  {else} Unknown
{/case}
```

## Core Features

### Type Prefixes
Resolve naming conflicts with explicit type prefixes:
```
{app:mytag}              # App tag from _tags/
{pad:tagname}            # Built-in PAD tag
{php:strlen(@)}          # Call PHP function
{data:items}             # Defined data block
{pull:mySequence}        # Stored sequence
{field:"name from users"}  # Database field
```

### Custom Tags
Create `_tags/mytag.php` in your app:
```php
<?php
$format = $padPrm[$pad]['format'] ?? $padOpt[$pad][1] ?? 'default';
return "Output: $format";
?>
```

Use as `{mytag 'value'}` or `{mytag format='value'}`.

### Database Operations
```php
// In PHP
$user = db("RECORD * FROM users WHERE id={0}", [$id]);
$users = db("ARRAY * FROM users ORDER BY name");
$count = db("FIELD COUNT(*) FROM users");
$exists = db("CHECK users WHERE email='{0}'", [$email]);
```

```
// In templates
{field "count(*) from users"}
{table "SELECT * FROM users"}
  <tr><td>{$name}</td></tr>
{/table}
```

### Sequence Subsystem
80+ mathematical sequences with transformations:
```
{fibonacci rows=10}{$fibonacci} {/fibonacci}
{sequence '1..10', push='nums'}
{resume add=5}
{pull:nums}{$sequence} {/pull:nums}
```

See `sequence/README.md` for complete documentation.

## Application Structure

```
apps/myapp/
├── index.php / index.pad     # Page pair
├── _inits.php                # Runs before all pages
├── _inits.pad                # Wraps all pages (use @pad@ placeholder)
├── _exits.php / _exits.pad   # Runs after all pages
├── _lib/                     # Auto-included PHP functions
├── _tags/                    # Custom template tags
├── _functions/               # Custom pipe functions
├── _data/                    # Static data files (JSON, XML)
├── _include/                 # Template snippets
├── _callbacks/               # Iteration callbacks
├── _options/                 # Custom tag options
└── _config/config.php        # App configuration
```

## Configuration

In `_config/config.php` or `pad/config/config.php`:
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

- Set `$padInfo = 'trace'` for execution tracing
- Use `{dump}` tag for variable inspection
- Use `{trace}` tag for execution trace
- Check `DATA/` directory for error dumps and logs

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

## Code Conventions

- Framework uses global variables indexed by level (`$padTag[$pad]`)
- Include-based execution (no function calls between modules)
- Thin dispatcher pattern (most files route to subsystems)
- Output buffering for flexible content handling
- Files prefixed with `_` are auto-discovered by the framework

## Documentation

| Document | Description |
|----------|-------------|
| [INTRO.md](INTRO.md) | Introduction, installation, and Hello World example |
| [DOCUMENTATION.md](DOCUMENTATION.md) | Detailed documentation of concepts, syntax, and architecture |
| [INDEX.md](INDEX.md) | Index of all module README files |
| [REFERENCE.md](REFERENCE.md) | Index of all API reference files |
| [EXPLAIN.md](EXPLAIN.md) | Technical deep dives into complex subsystems |
| [DEVELOPMENT.md](DEVELOPMENT.md) | Resources for maintainers (analysis, known bugs) |

### Module References
- `tags/REFERENCE.md` - All template tags
- `functions/REFERENCE.md` - All pipe functions
- `options/REFERENCE.md` - All tag options
- `eval/EXPLAIN.md` - Expression evaluation internals
- `sequence/README.md` - Sequence subsystem

## Quick Reference

### Comparison Operators
| Operator | Meaning |
|----------|---------|
| `eq`, `==` | Equal |
| `ne`, `!=` | Not equal |
| `gt`, `>` | Greater than |
| `lt`, `<` | Less than |
| `ge`, `>=` | Greater or equal |
| `le`, `<=` | Less or equal |
| `and`, `or` | Logical operators |
| `range (a, b)` | Value in range |

### Common Pipe Functions
| Function | Purpose |
|----------|---------|
| `upper`, `lower` | Case conversion |
| `trim` | Remove whitespace |
| `html`, `url` | Encoding |
| `date('fmt')` | Format date |
| `+ n`, `- n`, `* n`, `/ n` | Arithmetic |
| `left(n)`, `cut(n)` | Truncate |
| `after('x')`, `before('x')` | Extract substring |
| `contains('x')` | Check substring |
| `. 'str'` | Concatenate |

### Key Syntax Rules
1. **Pipes need `{echo}`** - `{$var | upper}` won't work
2. **Arithmetic needs space** - `{echo $x | + 1}` not `| +1`
3. **Quote literal strings** - `{count 'items'}` not `{count items}`
4. **No inline CSS/JS** - PAD parses `{ }` as tags; use external files
5. **Use `padRedirect()`** - Don't use `exit` or `die` in PAD apps

## Installation

```bash
sudo pad/install/install.sh  # Sets up DB, Apache, data directories
```

## Testing

Run regression tests by visiting `/regression` in browser. Tests compare current output against stored HTML snapshots.

## License

See LICENSE file for details.
