# PAD Documentation

Detailed documentation for the PAD framework.

## Core Concepts

### Templates (.pad files)

PAD templates use curly brace syntax for dynamic content:

```
{# This is a comment #}

{$variable}                    {# Output a variable #}
{$name | upper}                {# Apply a pipe function #}
{$price | number(2)}           {# Pipe with parameters #}

{if $loggedIn}                 {# Conditional #}
  Welcome back!
{/if}

{users}                        {# Iterate over data #}
  <li>{$name} - {$email}</li>
{/users}
```

### Data Files (.php files)

PHP files provide data to templates. Return an array to make it available:

```php
<?php
// users.php
return [
  ['name' => 'Alice', 'email' => 'alice@example.com'],
  ['name' => 'Bob', 'email' => 'bob@example.com'],
];
?>
```

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

---

## Template Syntax

### Variables

```
{$simple}                      {# Simple variable #}
{$user.name}                   {# Object/array property #}
{$items[0]}                    {# Array index #}
{$value | default('N/A')}      {# Default value #}
```

### Pipe Functions

Transform values with pipe functions:

```
{$text | trim | upper}                    {# Chain functions #}
{$date | date('Y-m-d')}                   {# With parameters #}
{$html | html}                            {# Escape HTML #}
{$path | exists}                          {# Check file exists #}
{$name | contains('admin')}               {# String contains #}
```

Common functions: `trim`, `upper`, `lower`, `html`, `url`, `date`, `replace`, `left`, `right`, `contains`, `in`, `between`, `exists`

### Control Structures

**Conditionals:**
```
{if $condition}
  Content if true
{elseif $other}
  Alternative
{/if}

{case $status}
  {when 'active'}Active{/when}
  {when 'pending'}Pending{/when}
{/case}
```

**Loops:**
```
{users}                        {# Iterate data tag #}
  {$name}
{/users}

{while $condition}             {# While loop #}
  ...
{/while}

{files dir="images" mask="*.jpg"}
  <img src="{$path}">
{/files}
```

### Tag Properties

Access iteration state with `@` properties:

```
{items}
  {if @first}<ul>{/if}
  <li class="{if @odd}odd{/if}">{$name}</li>
  {if @last}</ul>{/if}
{/items}
```

Properties: `@first`, `@last`, `@even`, `@odd`, `@current`, `@count`, `@remaining`, `@key`, `@data`

### Tag Options

Control tag behavior with options:

```
{users sort="name" first="10"}           {# Sort and limit #}
{products sort="price DESC" page="1" rows="20"}
{items shuffle first="5"}                {# Random selection #}
{data content="users" toData="cached"}   {# Data operations #}
```

---

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
| `@` | Property | `{@first}` |
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
- Iteration counter (`@current`)
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

The walker (`walk/`) manages the output flow:
- `next.php` - Advance to next iteration
- `end.php` - Finalize level, apply type handler

---

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
├── tag/                 # Tag properties (25 properties)
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

---

## Advanced Features

### Database Integration

PAD includes a database layer for working with tables:

```
{users table}                  {# Query users table #}
  {$name} - {$email}
{/users}

{orders table where="user_id = $userId" sort="date DESC"}
  Order #{$id}: {$total}
{/orders}
```

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

---

## Configuration

### Global Configuration

Configuration is loaded from `config/config.php`. Key settings:

- Error handling mode
- Debug/info mode
- Default date format
- Database connections
- Cache settings

### Application Configuration

Applications can have their own `_config.php` files that are loaded during initialization.

---

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
