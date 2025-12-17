# CLAUDE.md

This file provides guidance to Claude Code when working with this repository. It serves as a comprehensive reference for the PAD framework.

## What is PAD?

**PAD (PHP Application Driver)** is an Inversion of Control PHP template engine. Unlike traditional frameworks where PHP code includes templates, PAD templates drive the execution flow - templates are first-class citizens that orchestrate data retrieval, logic, and output.

**Requirements:** PHP 8.0+

## Core Principle: Inversion of Control

```
Traditional PHP: Controller → includes → Template
PAD:            Template → drives → Data & Logic
```

The template structure mirrors the application flow. No routing, no controllers - just create files.

## Page Pairing

Every page consists of two files that PAD automatically pairs:
- `pagename.php` - Returns data (variables, arrays)
- `pagename.pad` - Template that renders the data

```php
// index.php
<?php
$message = 'Hello World!';
$items = ['Apple', 'Banana', 'Cherry'];
?>
```

```html
<!-- index.pad -->
<h1>{$message}</h1>
<ul>
  {items}<li>{$items}</li>{/items}
</ul>
```

## URL Structure

Pages are accessed via query string (NOT path-based):
- `/myapp/` → `index.pad`
- `/myapp/?about` → `about.pad`
- `/myapp/?admin/users` → `admin/users.pad`

**Important:** Internal links use `?page` format, not `/page`.

---

## Essential Template Syntax

### Variables
```
{$variable}              # Output variable
{$user.name}             # Object/array property
{$items[0]}              # Array index
{!text}                  # HTML-escaped output
```

### Pipe Functions (CRITICAL: Always need {echo})
```
{echo $name | upper}              # Correct
{echo $text | trim | lower}       # Chained functions
{echo $date | date('Y-m-d')}      # With parameters
{echo $value | + 1}               # Arithmetic (space required!)

{$name | upper}                   # WRONG - bare expression won't work!
{echo $value | +1}                # WRONG - needs space before 1
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

### Loops (Data Iteration)
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

### Iteration Properties (property@tag syntax)
```
{items}
  {if first@items}<ul>{/if}
  <li class="{even@items ? even : odd}">{$name}</li>
  {if last@items}</ul>{/if}
  Index: {current@items} of {count@items}
{/items}
```

Properties: `first@`, `last@`, `notFirst@`, `notLast@`, `even@`, `odd@`, `current@`, `count@`, `remaining@`, `key@`, `fields@`

### Case/Switch
```
{case $color}
  {when 'red'} Stop
  {when 'yellow'} Caution
  {when 'green'} Go
  {else} Unknown
{/case}
```

### Data Definition
```
{data 'colors'}
  ["red", "green", "blue"]
{/data}

{colors}{$colors} {/colors}
```

Supports JSON, XML, YAML, and CSV formats.

---

## Critical Syntax Rules (Common Mistakes)

1. **Pipes need `{echo}`** - `{$var | upper}` won't work, use `{echo $var | upper}`
2. **Arithmetic needs space** - `{echo $x | + 1}` not `| +1`
3. **Quote literal strings** - `{count 'items'}` not `{count items}`
4. **No inline CSS/JS** - PAD parses `{ }` as tags; use external files
5. **Use `padRedirect()`** - Don't use `exit` or `die` in PAD apps
6. **Conditionals need comparison** - `{if $flag eq 1}` not `{if $flag}`
7. **`$` vs `%` variables** - `$var` is level (constant), `%var` is occurrence (per-iteration)

---

## Type Prefixes

Resolve naming conflicts with explicit prefixes:

| Prefix | Purpose | Example |
|--------|---------|---------|
| `app:` | App tag from `_tags/` | `{app:mytag}` |
| `pad:` | Built-in PAD tag | `{pad:if}` |
| `php:` | Call PHP function | `{php:strlen(@)}` |
| `data:` | Defined data block | `{data:items}` |
| `content:` | Content block | `{content:header}` |
| `pull:` | Stored sequence | `{pull:mySeq}` |
| `field:` | Database field | `{field:"name from users"}` |
| `table:` | Database table | `{table:users}` |
| `local:` | Files from `_data/` | `{local:menu.json}` |
| `constant:` | PHP constant | `{constant:PHP_VERSION}` |
| `bool:` | Boolean store | `{bool:isAdmin}` |

---

## Project Structure

```
pad/               # Framework core
├── pad.php        # Entry point
├── start/         # Execution lifecycle
├── build/         # Page assembly
├── level/         # Tag processing and scope
├── occurrence/    # Data iteration
├── walk/          # Template tree walking
├── eval/          # Expression parser
├── types/         # Tag type handlers (25+)
├── tags/          # Template tags (40+)
├── functions/     # Pipe functions (40+)
├── options/       # Tag options (50+)
├── properties/    # Tag properties (25)
├── lib/           # PHP helper functions
├── config/        # Configuration
├── sequence/      # Sequence subsystem (80+ sequences)
└── error/         # Error handling

apps/              # PAD applications
├── pad/           # Reference app (manual + regression tests)
├── demo/          # Demo application
├── hello/         # Minimal example
└── ...

www/               # Web entry points (one per app)
DATA/              # Runtime data (cache, logs) - skip when analyzing
docs/              # Documentation
```

## Application Structure

```
apps/myapp/
├── index.php / index.pad     # Page pair
├── _inits.php                # Runs BEFORE all pages
├── _inits.pad                # Wraps all pages (use @pad@ placeholder)
├── _exits.php / _exits.pad   # Runs AFTER all pages
├── _lib/                     # Auto-included PHP functions
├── _tags/                    # Custom template tags
├── _functions/               # Custom pipe functions
├── _data/                    # Static data files (JSON, XML)
├── _include/                 # Template snippets
├── _callbacks/               # Iteration callbacks
├── _options/                 # Custom tag options
└── _config/config.php        # App configuration
```

---

## Database Operations

### PHP db() Function
```php
// RECORD - Single row
$user = db("RECORD * FROM users WHERE id={0}", [$id]);

// ARRAY - Multiple rows
$users = db("ARRAY * FROM users ORDER BY name");

// FIELD - Single value
$count = db("FIELD COUNT(*) FROM users");

// CHECK - Boolean (special syntax - NO "* FROM")
$exists = db("CHECK users WHERE email='{0}'", [$email]);

// INSERT - Returns ID
$id = db("INSERT INTO users (name) VALUES ('{0}')", [$name]);
```

**Important:** PAD does NOT add quotes - you must quote string placeholders:
```php
db("SELECT * FROM users WHERE name='{0}'", [$name]);  // Correct
db("SELECT * FROM users WHERE name={0}", [$name]);    // WRONG for strings
```

### Template Database Tags
```
{field "count(*) from users"}
{table "SELECT * FROM users"}
  <tr><td>{$name}</td></tr>
{/table}
```

---

## Sequence Subsystem (80+ Mathematical Sequences)

### Basic Usage
```
{fibonacci rows=10}{$fibonacci} {/fibonacci}
{prime rows=15}{$prime} {/prime}
{sequence '1..10', name='n'}{$n} {/sequence}
```

### Store and Transform
```
{sequence '1..10', push='nums'}
{resume add=5}
{resume reverse}
{pull:nums}{$sequence} {/pull:nums}
```

### Aggregation
```
{pull:nums sum}{$sequence}{/pull:nums}
{pull:nums average}{$sequence}{/pull:nums}
```

### Sequence Types
Fibonacci, Lucas, prime, composite, triangular, square, cubic, pentagonal, hexagonal, Catalan, Bell, Pell, happy, lucky, palindrome, and 60+ more.

### Actions
`reverse`, `sort`, `shuffle`, `first`, `last`, `slice`, `sum`, `average`, `minimum`, `maximum`, `count`, `dedup`, `append`, `merge`, `intersection`, `difference`

---

## Common Tags Reference

| Tag | Purpose | Example |
|-----|---------|---------|
| `{if}...{/if}` | Conditional | `{if $x eq 1}yes{/if}` |
| `{case}...{/case}` | Switch | `{case $x}{when 'a'}...{/case}` |
| `{while}...{/while}` | While loop | `{while $i lt 10}...{/while}` |
| `{set}` | Assign variable | `{set $count = 0}` |
| `{echo}` | Evaluate/output | `{echo $a + $b}` |
| `{get}` | Include page | `{get 'fragments/nav'}` |
| `{data}` | Define data | `{data 'items'}[1,2,3]{/data}` |
| `{increment}` | Add 1 | `{increment $i}` |
| `{decrement}` | Subtract 1 | `{decrement $i}` |
| `{count}` | Check array | `{count 'items'}has items{/count}` |
| `{files}` | List files | `{files dir='images' mask='*.jpg'}` |
| `{dump}` | Debug output | `{dump}` |
| `{redirect}` | HTTP redirect | `{redirect 'url'}` |

---

## Common Pipe Functions

| Function | Purpose | Example |
|----------|---------|---------|
| `upper`, `lower` | Case | `{echo $x \| upper}` |
| `trim` | Whitespace | `{echo $x \| trim}` |
| `html`, `url` | Encoding | `{echo $x \| html}` |
| `date('fmt')` | Format date | `{echo $x \| date('Y-m-d')}` |
| `+ n`, `- n`, `* n`, `/ n` | Arithmetic | `{echo $x \| + 1}` |
| `left(n)`, `right(n)` | Substring | `{echo $x \| left(5)}` |
| `after('x')`, `before('x')` | Extract | `{echo $email \| after('@')}` |
| `contains('x')` | Check | `{echo $x \| contains('admin')}` |
| `. 'str'` | Concatenate | `{echo $x \| . ' suffix'}` |
| `replace('a','b')` | Replace | `{echo $x \| replace('old','new')}` |

---

## Comparison Operators

| Operator | Meaning |
|----------|---------|
| `eq`, `==` | Equal |
| `ne`, `!=` | Not equal |
| `gt`, `>` | Greater than |
| `lt`, `<` | Less than |
| `ge`, `>=` | Greater or equal |
| `le`, `<=` | Less or equal |
| `and`, `or` | Logical |
| `range (a, b)` | Value in range |

---

## Tag Options

| Option | Purpose | Example |
|--------|---------|---------|
| `sort` | Sort data | `{users sort="name"}` |
| `rows` | Limit rows | `{users rows=10}` |
| `first`, `last` | First/last N | `{users first=5}` |
| `page` | Pagination | `{users page=2 rows=20}` |
| `cache` | Cache output | `{data cache="3600"}` |
| `toData` | Store data | `{users toData="cached"}` |
| `toContent` | Store content | `{header toContent="hdr"}` |
| `toBool` | Store boolean | `{check toBool="flag"}` |
| `glue` | Separator | `{items glue=", "}` |
| `quote` | Wrap in quotes | `{items quote="'"}` |

---

## Entry Point Pattern

```php
<?php
include __DIR__ . '/../padHome.php';
define('APP', "$padHome/apps/myapp/");
define('DAT', "$padHome/DATA/");
include "$padHome/pad/pad.php";
?>
```

---

## Configuration

In `_config/config.php`:
```php
$padSqlHost = 'localhost';
$padSqlDatabase = 'myapp';
$padSqlUser = 'user';
$padSqlPassword = 'pass';

$padErrorAction = 'pad';    // pad, boot, php, stop, exit, ignore, log, dump
$padInfo = 'trace';         // trace, stats, track, xml, xref
$padOutputType = 'web';     // web, file, download, console
```

---

## Debugging

- Set `$padInfo = 'trace'` for execution tracing
- Use `{dump}` tag for variable inspection
- Use `{trace}` tag for execution trace
- Check `DATA/` directory for error dumps and logs

---

## Execution Flow

```
Request → pad.php → config → inits/ (initialization)
                          → build/ (page assembly: _inits + @pad@ + _exits)
                          → level/ (tag processing loop)
                          → Response

For each {tag}:
  level/level.php → detect type → types/{type}.php → process
                  → if data array: occurrence/ (iterate)
```

---

## Level System

Each `{tag}` creates a new level scope. PAD maintains global variables per level in arrays indexed by `$pad` (current level, -1 = root):
- `$padTag[$pad]`, `$padType[$pad]`, `$padOpt[$pad]` - Tag state
- `$padData[$pad]`, `$padCurrent[$pad]` - Data for iteration
- `$padBase[$pad]`, `$padOut[$pad]`, `$padResult[$pad]` - Content/output

---

## Documentation Index

| File | Description |
|------|-------------|
| [ANALYSE.md](ANALYSE.md) | Code analysis and architecture overview |
| [APP.md](APP.md) | Application development guide - creating apps, template syntax, tags, patterns |
| [LICENSE.md](LICENSE.md) | GPLv3 license information |
| [PAD.md](PAD.md) | Framework internals - execution flow, level system, configuration |
| [reference/](reference/README.md) | Reference documentation - tags, functions, options, properties |
| [sequences/](sequences/README.md) | Sequence subsystem - 80+ mathematical sequences and transformations |

### Reference Files
- [docs/reference/TAGS.md](docs/reference/TAGS.md) - All template tags
- [docs/reference/FUNCTIONS.md](docs/reference/FUNCTIONS.md) - All pipe functions
- [docs/reference/OPTIONS.md](docs/reference/OPTIONS.md) - All tag options
- [docs/reference/PROPERTIES.md](docs/reference/PROPERTIES.md) - All iteration properties
- [docs/reference/TYPES.md](docs/reference/TYPES.md) - All tag types
- [docs/sequences/SEQUENCES.md](docs/sequences/SEQUENCES.md) - All sequence types
- [docs/sequences/ACTIONS.md](docs/sequences/ACTIONS.md) - All sequence actions

### Parts op PAD
- [apps/README.md](apps/README.md) - All template tags
- [pad/README.md](pad/README.md) - PHP sources of the PAD framework itself
- [www/README.md](www/README.md) - Webserver files, entry points when PAD is used in Apache

---

## Quick Patterns

### Zebra Striping
```
{items}
  <tr class="{even@items ? even : odd}">{$name}</tr>
{/items}
```

### Comma-Separated List
```
{items}{if notFirst@items}, {/if}{$name}{/items}
```

### First/Last Wrapper
```
{items}
  {if first@items}<ul>{/if}
  <li>{$name}</li>
  {if last@items}</ul>{/if}
{/items}
```

### Counter Display
```
{items}
  {current@items} of {count@items}: {$name}
{/items}
```

### Dynamic Fields
```
{record}
  {fields@record}
    {$name}: {$value}
  {/fields}
{/record}
```

### Global Wrapper (_inits.pad)
```html
<!DOCTYPE html>
<html>
<head><title>{$title}</title></head>
<body>
  <nav>...</nav>
  @pad@
  <footer>...</footer>
</body>
</html>
```

---

## Architecture Notes

- **Procedural Design**: 140+ functions, minimal OOP
- **Global State**: Uses `$GLOBALS` and `global` declarations indexed by level
- **File-Based Dispatch**: `include` statements as control flow
- **Array-Indexed Levels**: Nesting tracked via `$padVariable[$pad]` pattern
- **15+ Years Production**: Stable, battle-tested codebase
