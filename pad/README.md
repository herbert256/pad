# PAD Framework

**PHP Application Driver** - An Inversion of Control Template Engine

## Introduction

PAD is a PHP template engine that inverts the traditional web application architecture. Instead of PHP code including templates, PAD templates drive the execution flow, seamlessly integrating data access, control structures, and presentation in a unified template syntax.

### Philosophy

Traditional PHP frameworks follow a "code-first" approach where PHP scripts control the flow and include template fragments. PAD inverts this paradigm: templates are first-class citizens that orchestrate everything from data retrieval to output generation. This "template-first" approach creates a natural separation where the visual structure of the application mirrors its logical structure.

### Key Benefits

- **Visual-Logical Unity**: Template structure reflects application flow
- **Reduced Boilerplate**: No manual routing, controller classes, or view binding
- **Hierarchical Inheritance**: Templates inherit from parent directories automatically
- **Clean Syntax**: Minimal, readable template syntax with `{tags}`
- **Zero Configuration**: Convention over configuration - just create files and directories

---

## Quick Start

### Requirements

- PHP 8.0 or higher
- Web server (Apache, Nginx, etc.)

### Installation

1. Set up two constants before including PAD:
   - `APP` - Path to your application directory (must end with `/`)
   - `DAT` - Path to your data directory (must end with `/`)

2. Include the PAD framework:

```php
<?php
define('APP', '/path/to/your/app/');
define('DAT', '/path/to/your/data/');
include '/path/to/pad/pad.php';
?>
```

### Your First Application

Create a simple page in your APP directory:

**APP/hello.pad**
```
<h1>Hello, {$name}!</h1>
<p>Welcome to PAD.</p>
```

**APP/hello.php**
```php
<?php
return ['name' => 'World'];
?>
```

Visit `/hello` in your browser - PAD automatically pairs the `.php` and `.pad` files.

---

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
├── types/               # Tag type handlers
│   ├── app.php          # Application pages
│   ├── pad.php          # PAD templates
│   ├── data.php         # Data access
│   ├── field.php        # Field access
│   ├── table.php        # Database tables
│   └── ...              # 25+ type handlers
│
├── tags/                # Template tags
│   ├── if.php           # Conditional
│   ├── while.php        # Loop
│   ├── data.php         # Data access
│   ├── files.php        # File listing
│   └── ...              # 40+ tags
│
├── functions/           # Pipe functions
│   ├── trim.php         # String trimming
│   ├── upper.php        # Uppercase
│   ├── date.php         # Date formatting
│   └── ...              # 40+ functions
│
├── options/             # Tag options
│   ├── sort.php         # Sorting
│   ├── first.php        # Limit results
│   ├── page.php         # Pagination
│   └── ...              # 50+ options
│
├── handling/            # Data handling
│   ├── sort.php         # Sort handler
│   ├── shuffle.php      # Shuffle handler
│   └── ...              # 15 handlers
│
├── tag/                 # Tag properties
│   ├── first.php        # @first
│   ├── last.php         # @last
│   └── ...              # 25 properties
│
├── constructs/          # Special constructs
│   ├── pad.php          # @pad@
│   ├── content.php      # @content@
│   └── ...              # 7 constructs
│
├── lib/                 # PHP library
│   └── *.php            # Helper functions
│
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

---

## Documentation Index

### Module Documentation

#### README Files

| Category | File | Description |
|----------|------|-------------|
| **Core** | | |
| | [start/README.md](start/README.md) | Execution lifecycle and entry points |
| | [build/README.md](build/README.md) | Page assembly and build process |
| | [level/README.md](level/README.md) | Tag processing and scope management |
| | [walk/README.md](walk/README.md) | Tree walking and iteration |
| | [occurrence/README.md](occurrence/README.md) | Data iteration and occurrence tracking |
| **Evaluation** | | |
| | [eval/README.md](eval/README.md) | Expression evaluation system |
| **Tags & Types** | | |
| | [tags/README.md](tags/README.md) | Template tag implementations |
| | [types/README.md](types/README.md) | Tag type handlers |
| | [tag/README.md](tag/README.md) | Tag property system |
| **Data Handling** | | |
| | [handling/README.md](handling/README.md) | Data handling functions |
| | [data/README.md](data/README.md) | Data store management |
| | [database/README.md](database/README.md) | Database layer |
| **Functions & Options** | | |
| | [functions/README.md](functions/README.md) | Pipe function implementations |
| | [options/README.md](options/README.md) | Tag option handlers |
| **Infrastructure** | | |
| | [config/README.md](config/README.md) | Configuration system |
| | [cache/README.md](cache/README.md) | Caching system |
| | [error/README.md](error/README.md) | Error handling |
| | [events/README.md](events/README.md) | Event system |
| | [exits/README.md](exits/README.md) | Exit handlers |
| | [info/README.md](info/README.md) | Debug and profiling |
| **Support** | | |
| | [lib/README.md](lib/README.md) | PHP helper library |
| | [inits/README.md](inits/README.md) | Initialization modules |
| | [install/README.md](install/README.md) | Installation utilities |
| | [sequence/README.md](sequence/README.md) | Sequence generation |
| | [constructs/README.md](constructs/README.md) | Special @construct@ patterns |
| **Utilities** | | |
| | [at/README.md](at/README.md) | @ prefix handlers |
| | [get/README.md](get/README.md) | GET request handling |
| | [call/README.md](call/README.md) | Function call utilities |
| | [callback/README.md](callback/README.md) | Callback system |
| | [try/README.md](try/README.md) | Try/catch mechanism |

#### REFERENCE Files

| Category | File | Description |
|----------|------|-------------|
| **Template Syntax** | | |
| | [tags/REFERENCE.md](tags/REFERENCE.md) | All template tags |
| | [functions/REFERENCE.md](functions/REFERENCE.md) | All pipe functions |
| | [options/REFERENCE.md](options/REFERENCE.md) | All tag options |
| | [types/REFERENCE.md](types/REFERENCE.md) | All tag types |
| | [tag/REFERENCE.md](tag/REFERENCE.md) | All tag properties (@) |
| | [constructs/REFERENCE.md](constructs/REFERENCE.md) | All @construct@ patterns |
| **Processing** | | |
| | [handling/REFERENCE.md](handling/REFERENCE.md) | Data handling functions |
| | [lib/REFERENCE.md](lib/REFERENCE.md) | PHP library functions |

#### EXPLAIN Files

| Category | File | Description |
|----------|------|-------------|
| **Deep Dives** | | |
| | [eval/EXPLAIN.md](eval/EXPLAIN.md) | Expression evaluation internals |
| | [sequence/EXPLAIN.md](sequence/EXPLAIN.md) | Sequence generation internals |

#### BUGS Files

| Category | Files |
|----------|-------|
| **Core** | [start/BUGS.md](start/BUGS.md), [build/BUGS.md](build/BUGS.md), [level/BUGS.md](level/BUGS.md), [walk/BUGS.md](walk/BUGS.md), [occurrence/BUGS.md](occurrence/BUGS.md) |
| **Evaluation** | [eval/BUGS.md](eval/BUGS.md) |
| **Tags & Types** | [tags/BUGS.md](tags/BUGS.md), [types/BUGS.md](types/BUGS.md), [tag/BUGS.md](tag/BUGS.md) |
| **Data** | [handling/BUGS.md](handling/BUGS.md), [data/BUGS.md](data/BUGS.md), [database/BUGS.md](database/BUGS.md) |
| **Functions** | [functions/BUGS.md](functions/BUGS.md), [options/BUGS.md](options/BUGS.md) |
| **Infrastructure** | [config/BUGS.md](config/BUGS.md), [cache/BUGS.md](cache/BUGS.md), [error/BUGS.md](error/BUGS.md), [events/BUGS.md](events/BUGS.md), [exits/BUGS.md](exits/BUGS.md), [info/BUGS.md](info/BUGS.md) |
| **Support** | [lib/BUGS.md](lib/BUGS.md), [inits/BUGS.md](inits/BUGS.md), [install/BUGS.md](install/BUGS.md), [sequence/BUGS.md](sequence/BUGS.md), [constructs/BUGS.md](constructs/BUGS.md) |
| **Utilities** | [at/BUGS.md](at/BUGS.md), [get/BUGS.md](get/BUGS.md), [call/BUGS.md](call/BUGS.md), [callback/BUGS.md](callback/BUGS.md), [try/BUGS.md](try/BUGS.md) |

---

## License

PAD Framework - PHP Application Driver

---

## Credits

Developed with a philosophy of simplicity, convention over configuration, and template-first design.
