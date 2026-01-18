# PAD Framework

**PHP Application Driver** - An Inversion of Control Template Engine

PAD is a PHP template engine that inverts the traditional web application architecture. Instead of PHP code including templates, PAD templates drive the execution flow, seamlessly integrating data access, control structures, and presentation in a unified template syntax.

**Requirements:** PHP 8.0+

## Philosophy

Traditional PHP frameworks follow a "code-first" approach where PHP scripts control the flow and include template fragments. PAD inverts this paradigm: templates are first-class citizens that orchestrate everything from data retrieval to output generation. This "template-first" approach creates a natural separation where the visual structure of the application mirrors its logical structure.

### Key Benefits

- **Visual-Logical Unity**: Template structure reflects application flow
- **Reduced Boilerplate**: No manual routing, controller classes, or view binding
- **Hierarchical Inheritance**: Templates inherit from parent directories automatically
- **Clean Syntax**: Minimal, readable template syntax with `{tags}`
- **Zero Configuration**: Convention over configuration - just create files and directories

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

### Directory Hierarchy

PAD uses a hierarchical directory structure for inheritance:

```
APP/
├── _inits.pad          # Initialization template (wraps all pages)
├── _inits.php          # Initialization code (runs before pages)
├── _exits.pad          # Exit template (wraps all pages)
├── _exits.php          # Exit code (runs after pages)
├── _lib/               # Library files (auto-included)
│   └── helpers.php
├── index.pad           # Homepage template
├── index.php           # Homepage data
└── admin/
    ├── _inits.pad      # Admin section wrapper
    ├── dashboard.pad   # Inherits from parent _inits.pad
    └── dashboard.php
```

Child directories automatically inherit parent templates. The `_inits.pad` files wrap content like layouts.

## Quick Start

### Installation

1. Set up two constants before including PAD:
   - `APP` - Path to your application directory (must end with `/`)
   - `DAT` - Path to your data directory (must end with `/`)

2. Include the PAD framework:

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
{$simple}                      {# Simple variable #}
{$user.name}                   {# Object/array property #}
{$items[0]}                    {# Array index #}
{!text}                        {# HTML-escaped output #}
{$value | default('N/A')}      {# Default value #}
```

### Pipe Functions

Transform values with pipe functions:

```
{echo $name | upper}           {# Uppercase #}
{echo $text | trim | lower}    {# Chain multiple functions #}
{echo $date | date('Y-m-d')}   {# With parameters #}
{echo $value | + 1}            {# Arithmetic (space required) #}
{echo $text | after('@')}      {# String manipulation #}
{echo $html | html}            {# Escape HTML #}
{echo $name | contains('admin')} {# String contains #}
```

**Important:** Pipes require `{echo}` - bare `{$var | func}` won't work.

Common functions: `trim`, `upper`, `lower`, `html`, `url`, `date`, `replace`, `left`, `right`, `contains`, `in`, `between`, `exists`

### Control Structures

**Conditionals:**
```
{if $condition}
  Content if true
{elseif $other}
  Alternative
{else}
  Default
{/if}

{case $status}
  {when 'active'}Active{/when}
  {when 'pending'}Pending{/when}
  {else}Unknown{/else}
{/case}
```

**Loops:**
```
{users}                        {# Iterate data tag #}
  <li>{$name} - {$email}</li>
{/users}

{while $i le 10}               {# While loop #}
  Item {$i}
  {increment $i}
{/while}

{files dir="images" mask="*.jpg"}
  <img src="{$path}">
{/files}
```

**Loop Control:**
```
{continue 'tagname'}    # Skip to next iteration
{cease 'tagname'}       # Soft stop (graceful end)
{break 'tagname'}       # Hard stop (immediate exit)
```

### Tag Properties

Access iteration state with `property@tag` syntax:

```
{items}
  {if first@items}<ul>{/if}
  <li class="{if odd@items}odd{/if}">{$name}</li>
  {if last@items}</ul>{/if}
{/items}
```

Properties: `first@tag`, `last@tag`, `even@tag`, `odd@tag`, `current@tag`, `count@tag`, `remaining@tag`, `key@tag`, `data@tag`

### Tag Options

Control tag behavior with options:

```
{users sort="name" first="10"}           {# Sort and limit #}
{products sort="price DESC" page="1" rows="20"}
{items shuffle first="5"}                {# Random selection #}
{data content="users" toData="cached"}   {# Data operations #}
```

### Data Definition

```
{data 'colors'}
  ["red", "green", "blue"]
{/data}

{colors}{$colors} {/colors}
```

Supports JSON, XML, YAML, and CSV formats.

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

{users table}                  {# Query users table #}
  {$name} - {$email}
{/users}

{orders table where="user_id = $userId" sort="date DESC"}
  Order #{$id}: {$total}
{/orders}
```

### Sequence Subsystem

80+ mathematical sequences with transformations:
```
{fibonacci rows=10}{$fibonacci} {/fibonacci}
{sequence '1..10', push='nums'}
{resume add=5}
{pull:nums}{$sequence} {/pull:nums}
```

See [sequences/](sequences/README.md) for complete documentation.

## How PAD Works

### 1. Entry Point

When a request arrives, PAD is initialized via `pad.php`:

```
Request → pad.php → config/config.php → start/enter/start.php
```

The entry point:
- Defines the `PAD` constant (framework path)
- Validates `APP` and `DAT` constants
- Changes to the APP directory
- Loads configuration
- Starts the execution engine

### 2. Build Phase

The build system assembles the complete page:

```
build/build.php
├── build/dirs.php      → Create directory hierarchy
├── build/_lib.php      → Collect library files
├── build/base.php      → Build template structure
│   ├── _inits.pad files (outer to inner)
│   ├── @pad@ placeholder
│   └── _exits.pad files (inner to outer)
└── build/page.php      → Process page
    ├── _inits.php execution
    ├── page.php execution → returns data
    ├── page.pad template
    └── _exits.php execution
```

The `@pad@` placeholder is replaced with the page content, creating a nested structure where parent templates wrap child content.

### 3. Level Processing

PAD processes templates through a level-based system. Each tag creates a new level:

```
{outer}                 ← Level 0
  {inner}               ← Level 1
    {field}             ← Level 2
  {/inner}
{/outer}
```

The level processor (`level/level.php`):
1. Finds tag delimiters `{` and `}`
2. Extracts tag content
3. Detects tag type (variable, tag, field, function)
4. Creates a new level scope
5. Parses parameters, options, and variables
6. Executes the type handler
7. Processes child content (for paired tags)
8. Collects output and returns

### 4. Tag Type Resolution

When a tag is encountered, PAD determines its type:

| Prefix | Type | Example |
|--------|------|---------|
| `$` | Variable | `{$name}` |
| `#` | Option | `{#param}` |
| `&` | Tag reference | `{&tagname}` |
| `@` | Property | `{first@tag}` |
| (none) | Tag/Field | `{users}`, `{if}` |

Types are resolved in order: app → pad → data → content → field → tag

### 5. Data Iteration

When a tag has data (array), PAD iterates through each item:

```
{users}                        ← Data: [{name: 'A'}, {name: 'B'}]
  <li>{$name}</li>             ← Occurrence 1: name = 'A'
                               ← Occurrence 2: name = 'B'
{/users}
```

The occurrence system (`occurrence/`) manages:
- Iteration counter (`current@tag`)
- Current data item
- Scope variables for each iteration
- First/last/even/odd detection

### 6. Expression Evaluation

Expressions in templates are parsed and evaluated by the eval subsystem:

```
{$price * 1.1 | round(2)}
```

Evaluation pipeline:
1. **Parse** → Tokenize into values, operators, variables
2. **Resolve** → Look up variables, resolve types
3. **Split** → Separate by pipe operators
4. **Execute** → Apply operators, call functions

Operator precedence: `!` → `**` `*` `/` `%` `+` `-` → `.` → comparisons → `AND` `XOR` `OR`

### 7. Output Generation

After processing, PAD generates the final output:

```
Template Processing → $padResult → $padOut → Output Buffer → Response
```

## Framework Architecture

### Directory Structure

```
pad/
├── pad.php              # Main entry point
│
├── start/               # Execution lifecycle
│   ├── enter/           # Entry points (page, code, ajax, redirect)
│   ├── start/           # Initialization phase
│   └── end/             # Termination phase
│
├── build/               # Page assembly
│   ├── build.php        # Main build orchestrator
│   ├── base.php         # Template structure
│   ├── page.php         # Page processing
│   └── _lib.php         # Library collection
│
├── level/               # Tag processing
│   ├── level.php        # Main level processor
│   ├── setup.php        # Level initialization
│   ├── parms/           # Parameter parsing
│   └── pipes/           # Pipe operations
│
├── occurrence/          # Data iteration
│   ├── occurrence.php   # Main occurrence handler
│   ├── init.php         # Occurrence initialization
│   ├── set.php          # Variable setup
│   └── end.php          # Occurrence finalization
│
├── walk/                # Tree walking
│   ├── next.php         # Next iteration
│   └── end.php          # Level completion
│
├── eval/                # Expression evaluation
│   ├── eval.php         # Main evaluator
│   ├── parse.php        # Expression parser
│   ├── actions/         # Operator actions
│   ├── go/              # Operator execution
│   └── single/          # Type resolvers
│
├── types/               # Tag type handlers (25+)
├── tags/                # Template tags (40+)
├── functions/           # Pipe functions (40+)
├── options/             # Tag options (50+)
├── handling/            # Data handling (15 handlers)
├── properties/          # Tag properties (25 properties)
├── constructs/          # Special constructs (7 constructs)
│
├── lib/                 # PHP library
├── config/              # Configuration
├── database/            # Database layer
├── cache/               # Caching system
├── error/               # Error handling
├── events/              # Event system
├── info/                # Debug/profiling
└── inits/               # Initialization
```

### Processing Pipeline

```
┌─────────────────────────────────────────────────────────────────┐
│                         REQUEST                                  │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│  pad.php                                                         │
│  ├── Define PAD constant                                        │
│  ├── Validate APP/DAT                                           │
│  └── Include config & start                                     │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│  start/                                                          │
│  ├── Initialize globals                                         │
│  ├── Set up stores                                              │
│  └── Begin execution                                            │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│  build/                                                          │
│  ├── Collect _lib files                                         │
│  ├── Build base structure (_inits.pad + @pad@ + _exits.pad)     │
│  ├── Execute _inits.php                                         │
│  ├── Execute page.php → get data                                │
│  ├── Load page.pad template                                     │
│  └── Replace @pad@ with content                                 │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│  level/ + occurrence/ + walk/                                    │
│  ├── Find { } tag delimiters                                    │
│  ├── Parse tag name, parameters, options                        │
│  ├── Detect type and load handler                               │
│  ├── For data tags: iterate occurrences                         │
│  ├── Process child content recursively                          │
│  └── Collect output                                             │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│  eval/                                                           │
│  ├── Parse expressions                                          │
│  ├── Resolve variables                                          │
│  ├── Execute operators                                          │
│  └── Apply pipe functions                                       │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│                         RESPONSE                                 │
└─────────────────────────────────────────────────────────────────┘
```

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

## Advanced Features

### Caching

Built-in caching system for performance:

```
{expensive_query cache="3600"}   {# Cache for 1 hour #}
  ...
{/expensive_query}
```

### AJAX Support

Handle AJAX requests seamlessly:

```
{ajax}
  {# Content returned as AJAX response #}
{/ajax}
```

### Sandbox Execution

Execute code in isolation:

```
{sandbox}
  {# Isolated execution environment #}
{/sandbox}
```

### Event System

Hook into framework events for debugging and customization:

- Build events
- Walk events (next, end)
- Occurrence events (start, end)
- Error events

### Callbacks

Execute PHP code at specific points:

```
{data callback="myFunction"}
  {# myFunction called for each row #}
{/data}
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

## Best Practices

### Directory Organization

```
APP/
├── _lib/                # Shared libraries
├── _inits.pad           # Global layout
├── _inits.php           # Global initialization
├── components/          # Reusable components
│   ├── header.pad
│   └── footer.pad
├── pages/               # Main pages
│   ├── home/
│   └── about/
└── api/                 # API endpoints
    └── users/
```

### Template Guidelines

1. **Keep templates focused** - One responsibility per template
2. **Use _lib for shared code** - Libraries auto-load from parent directories
3. **Leverage inheritance** - Let parent `_inits.pad` handle layouts
4. **Use meaningful names** - Tag and variable names should be self-documenting

### Performance Tips

1. **Use caching** - Cache expensive operations
2. **Limit data** - Use `first`, `page`, `rows` options
3. **Sort server-side** - Let database handle sorting when possible
4. **Minimize nesting** - Deep nesting impacts performance

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

## Testing

Run regression tests by visiting `/regression` in browser. Tests compare current output against stored HTML snapshots.

## License

See LICENSE file for details.

## Reference Documentation

- [TAGS.md](reference/TAGS.md) - All template tags
- [FUNCTIONS.md](reference/FUNCTIONS.md) - All pipe functions
- [OPTIONS.md](reference/OPTIONS.md) - All tag options
- [PROPERTIES.md](reference/PROPERTIES.md) - All iteration properties
- [EVAL.md](reference/EVAL.md) - Expression evaluation internals
- [sequences/](sequences/README.md) - Sequence subsystem
