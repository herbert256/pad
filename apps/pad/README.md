# PAD Application

This application serves a dual purpose: it is both a **functional example application** demonstrating the PAD framework's capabilities, and the **complete manual and reference documentation** for PAD.

---

## Table of Contents

1. [About This Application](#about-this-application)
2. [The PAD Framework](#the-pad-framework)
   - [Overview](#overview)
   - [Architecture](#architecture)
   - [Core Concepts](#core-concepts)
   - [Tags](#tags)
   - [Sequences](#sequences)
   - [The At Operator](#the-at-operator)
   - [Options System](#options-system)
   - [Data Handling](#data-handling)
   - [Built-in Functions](#built-in-functions)
   - [Configuration](#configuration)
   - [Request Lifecycle](#request-lifecycle)
3. [The PAD Application Structure](#the-pad-application-structure)
   - [Directory Overview](#directory-overview)
   - [Manual Section](#manual-section)
   - [Fragments Section](#fragments-section)
   - [Reference Section](#reference-section)
   - [Custom Tags](#custom-tags)
   - [Helper Libraries](#helper-libraries)
4. [Getting Started](#getting-started)

---

## About This Application

The PAD application (`apps/pad/`) demonstrates PAD framework features through:

- **Manual**: Step-by-step tutorials explaining PAD concepts
- **Fragments**: 295+ executable code examples with source and output
- **Reference**: Complete API documentation for all tags, sequences, functions, and options

Every example is live code that executes when viewed, showing both the source (`.php` and `.pad` files) and the rendered result side-by-side.

---

## The PAD Framework

### Overview

**PAD** (PHP Application Driver) is an **inversion of control** PHP framework that fundamentally inverts the traditional web request flow:

1. **First**: Execute all PHP application code (initializers, business logic)
2. **Then**: Read HTML/template markup files (`.pad` files)
3. **Finally**: Merge results and send to browser

This "don't call us, we'll call you" architecture means application code never directly outputs content or instantiates the framework - PAD handles the entire lifecycle.

**Key Characteristics:**
- Clean separation of business logic (`.php`) from presentation (`.pad`)
- Data-driven templates focused on rendering
- Native database integration without ORM complexity
- Powerful data transformations and sequences
- Extensible through custom tags, functions, and options

### Architecture

The PAD framework (`pad/` directory) is organized into these core subsystems:

```
pad/
├── at/           # Field access with . and @ notation
├── build/        # Page building and compilation
├── cache/        # Caching (db, file, memory)
├── call/         # Function/file execution handlers
├── callback/     # Callback execution system
├── config/       # Configuration loading
├── constructs/   # Basic language constructs
├── data/         # Data format handlers (JSON, CSV, YAML, XML, HTML)
├── database/     # Database definitions and SQL templates
├── error/        # Error handling and reporting
├── eval/         # Expression evaluation engine
├── events/       # Event hooks during processing
├── exits/        # Output and exit handling
├── functions/    # 40+ built-in utility functions
├── get/          # Request/page retrieval
├── handling/     # Data handling (sort, filter, slice, etc.)
├── inits/        # Framework initialization
├── level/        # Nesting level management
├── lib/          # Core library functions
├── occurrence/   # Tag occurrence tracking
├── options/      # Tag options processing
├── sequence/     # Sequence/loop system (80+ types)
├── start/        # Application startup
├── tags/         # 54 built-in tags
├── try/          # Error catching/try-except
├── types/        # Type processors for tags
└── walk/         # Tree walking for nested structures
```

### Core Concepts

#### Templates and Tags

PAD templates (`.pad` files) contain HTML mixed with PAD tags enclosed in curly braces:

```html
<html>
  <head><title>{$title}</title></head>
  <body>
    <h1>{$title}</h1>
    {items}
      <p>{$items}</p>
    {/items}
  </body>
</html>
```

#### Levels and Occurrences

PAD tracks nesting depth (levels) and iterations (occurrences):

- **Level**: The nesting depth of tags (level 0, 1, 2, ...)
- **Occurrence**: The current iteration within a loop

```
{employees}                    <!-- Level 0, occurs N times -->
  Name: {$employees:firstName}
  {orders}                     <!-- Level 1, occurs M times per employee -->
    Order: {$orders:number}
  {/orders}
{/employees}
```

#### PHP and PAD Files

Each page typically has two files:

- `page.php` - Application logic (sets variables, queries data)
- `page.pad` - Template (renders the data)

```php
// index.php
<?php
  $title = 'Welcome';
  $items = ['First', 'Second', 'Third'];
?>
```

```html
<!-- index.pad -->
<h1>{$title}</h1>
{items}
  <p>{$items}</p>
{/items}
```

### Tags

PAD includes 54 built-in tags organized by function:

#### Control Flow
| Tag | Description |
|-----|-------------|
| `{if}...{elseif}...{else}...{/if}` | Conditional blocks |
| `{while}...{/while}` | While loops |
| `{switch}...{case}...{/switch}` | Switch statements |
| `{continue}` | Continue to next iteration |
| `{exit}` | Exit current block |

#### Data & Variables
| Tag | Description |
|-----|-------------|
| `{set}` | Set variable values |
| `{$variable}` | Variable substitution |
| `{get 'name'}` | Get request parameters |
| `{field}` | Field access |
| `{array}` | Create arrays |
| `{data 'file'}` | Load data (JSON/CSV/YAML/XML) |

#### Sequences & Iteration
| Tag | Description |
|-----|-------------|
| `{sequence}...{/sequence}` | Process sequences/arrays |
| `{action}` | Execute sequence actions |
| `{count}` | Count items |
| `{increment}` / `{decrement}` | Counter operations |

#### Templating
| Tag | Description |
|-----|-------------|
| `{content}` | Include content |
| `{page 'name'}` | Include page |
| `{open}...{close}` | Open/close structures |
| `{keep}...{/keep}` | Keep content |
| `{remove}...{/remove}` | Remove content |

#### Execution
| Tag | Description |
|-----|-------------|
| `{callback}` | Execute callback function |
| `{code}` | Execute PHP code |

#### Output
| Tag | Description |
|-----|-------------|
| `{echo}` | Output values |
| `{dump}` | Debug dump |
| `{output}` | Control output format |
| `{redirect}` | HTTP redirect |
| `{ajax}` | AJAX support |

#### Database
| Tag | Description |
|-----|-------------|
| `{record}` | Database records |
| `{file}` | File operations |

### Sequences

Sequences are ordered collections that can be iterated and transformed. PAD includes 80+ sequence types:

#### Mathematical Sequences
- `prime`, `fibonacci`, `lucas`, `catalan`
- `triangular`, `square`, `pentagonal`, `hexagonal`
- `pell`, `perrin`, `bell`, `sylvester`
- And many more...

#### Generator Sequences
| Sequence | Description |
|----------|-------------|
| `range` | Number ranges: `{range 1 to 10}` |
| `list` | Custom lists |
| `loop` | Simple loops |
| `random` | Random values |
| `repeat` | Repeating values |
| `eval` | Evaluated expressions |

#### Arithmetic Sequences
- `add`, `subtract`, `multiply`, `divide`, `modulo`
- `power`, `exponentiation`, `ceiling`, `floor`

#### Logical Sequences
- `and`, `or`, `xor`, `nand`, `nor`, `xnor`, `not`

#### Sequence Actions (Transformations)
| Action | Description |
|--------|-------------|
| `count`, `sum`, `average`, `product` | Aggregations |
| `first`, `last`, `element`, `slice` | Selection |
| `sort`, `reverse`, `shuffle`, `dedup` | Ordering |
| `merge`, `append`, `prepend`, `combine` | Combining |
| `intersection`, `difference` | Set operations |
| `min`, `max`, `median` | Statistics |

**Example:**
```
{range 1 to 5}
  {$current}
{/range}
```
Output: 1 2 3 4 5

### The At Operator

The `@` operator provides advanced field access across nested structures:

```
{$fieldname@*}              # Field at any level
{$table:field@othertable}   # Related table field
{$item.property@*}          # Property path at any level
{@levelname:field}          # Specific level reference
```

This enables powerful navigation across nested data and related database tables.

### Options System

Tags support options that modify behavior:

#### Start Options
| Option | Description |
|--------|-------------|
| `track` | Track data |
| `before` | Execute before block |
| `dedup` | Deduplicate results |
| `sort` | Sort results |
| `ignore` | Ignore errors |
| `print` | Print output |
| `trace` | Debug tracing |

#### End Options
| Option | Description |
|--------|-------------|
| `toBool` | Convert to boolean |
| `toContent` | Convert to content |
| `toData` | Convert to array data |
| `tidy` | Format HTML output |
| `dump` | Debug dump |

**Example:**
```
{sequence print sort ignore}
  ...
{/sequence}
```

### Data Handling

PAD supports multiple data formats:

| Format | Usage |
|--------|-------|
| JSON | `{data 'file.json'}` |
| CSV | `{data 'file.csv'}` |
| XML | `{data 'file.xml'}` |
| YAML | `{data 'file.yaml'}` |
| HTML | `{data 'file.html'}` |

**Data Handling Options:**
- `sort` - Sort by field
- `reverse` - Reverse order
- `shuffle` - Randomize
- `dedup` - Remove duplicates
- `slice` - Select range
- `first` / `last` - Select endpoints
- `rows` - Limit rows

**Example:**
```
{data 'people.csv' sort='name' rows=10}
  Name: {$data:name}, Age: {$data:age}
{/data}
```

#### Database Integration

Configure tables and relationships:

```php
$padTables['employees'] = [
  'db'    => 'employees',
  'key'   => 'employeeNumber',
  'order' => 'lastName,firstName'
];

$padRelations['employees']['offices'] = [];
$padRelations['employees']['managers'] = [
  'table' => 'employees',
  'key'   => 'reportsTo'
];
```

Usage in templates:
```
{employees}
  {$employees:firstName} works in {$offices:city}
{/employees}
```

### Built-in Functions

PAD includes 40+ built-in functions usable in templates via pipe syntax:

#### String Functions
| Function | Description |
|----------|-------------|
| `lower`, `upper`, `capitalize` | Case conversion |
| `trim`, `left`, `right`, `mid` | Trimming/substring |
| `before`, `after`, `between` | String extraction |
| `replace` | String replacement |
| `contains`, `in`, `like` | String matching |

#### Encoding Functions
| Function | Description |
|----------|-------------|
| `html` | HTML encode |
| `url` | URL encode |
| `sanitize` | Sanitize input |
| `slashes`, `stripslashes` | Escape handling |

#### Date/Time Functions
| Function | Description |
|----------|-------------|
| `date`, `time`, `timestamp` | Formatting |
| `now` | Current time |

#### Logic Functions
| Function | Description |
|----------|-------------|
| `exists` | Check existence |
| `optional` | Make field optional |

**Pipe Syntax Example:**
```
{$data:email | lower | sanitize}
{$data:name | upper | trim}
```

### Configuration

Key configuration options in `pad/config/config.php`:

```php
// Error Handling
$padErrorAction    = 'pad';      // pad|boot|php|stop|exit|ignore|log|dump
$padErrorLevel     = 'all';      // all|error|warning|notice|none
$padErrorTry       = TRUE;       // Catch exceptions
$padErrorLog       = TRUE;       // Log to Apache

// Output
$padOutputType    = 'web';       // web|file|download|console

// Caching
$padCache         = FALSE;       // Enable caching

// Database
$padSqlHost       = '127.0.0.1';
$padSqlDatabase   = 'app';
$padSqlUser       = 'app';
$padSqlPassword   = 'app';

// Formatting
$padTidy          = TRUE;        // HTML Tidy formatting
$padFmtDate       = 'Y-m-d';
$padFmtTime       = 'H:i:s';
```

### Request Lifecycle

1. **Bootstrap** - Load `pad.php`, validate `APP` and `DAT` constants
2. **Initialize** - Execute initialization sequence
3. **Enter** - Determine page and entry type
4. **Page Detect** - Find `.php` and `.pad` files
5. **Build** - Execute PHP, load template, merge
6. **Parse** - Process PAD tags recursively
7. **Evaluate** - Resolve variables and functions
8. **Output** - Apply formatting/tidying
9. **Exit** - Close sessions, cleanup
10. **Transmit** - Send to browser

---

## The PAD Application Structure

### Directory Overview

```
apps/pad/
├── manual/           # Tutorial documentation (20+ chapters)
├── fragments/        # Code examples (295+ files)
├── reference/        # API reference documentation
├── develop/          # Development and testing tools
├── _tags/            # Custom tags for this application
├── _lib/             # Helper libraries
├── _functions/       # Custom functions
├── _callbacks/       # Callback handlers
├── _config/          # Application configuration
├── _install/         # Database installation scripts
├── index.php         # Application entry point
└── index.pad         # Main template
```

### Manual Section

The `/manual/` directory contains step-by-step tutorials:

| File | Topic |
|------|-------|
| `tags.pad` | Introduction to PAD tags |
| `levels_and_occurrences.pad` | Data structure fundamentals |
| `properties.pad` | Working with @ variables |
| `php_and_html.pad` | Mixing PHP and HTML |
| `else.pad` | Conditional logic |
| `data_handling.pad` | Data transformations |
| `callback.pad` | Row-level processing |
| `3_ways_to_make_a_table.pad` | Table creation patterns |
| `tag_return_values.pad` | Working with return values |

### Fragments Section

The `/fragments/` directory contains 295+ executable examples organized by topic:

| Directory | Content |
|-----------|---------|
| `hello/` | Hello World example |
| `start/` | Getting started |
| `content/` | Content management |
| `data/` | Data handling |
| `db/` | Database operations |
| `constructs/` | Control flow |
| `tags/` | Tag demonstrations |
| `lvl_occ/` | Levels and occurrences |
| `properties/` | Property examples |
| `tableFun/` | Advanced table features |
| `callback/` | Callback examples |
| `classicModels/` | Sample database examples |
| `miscellaneous/` | Sorting, filtering, etc. |

Each fragment typically includes:
- `.php` file - Setup code preparing variables/data
- `.pad` file - Template showing rendering
- Syntax-highlighted source display
- Live execution results

### Reference Section

The `/reference/` directory provides complete API documentation:

- **Tags**: Documentation for all 54 built-in tags
- **Sequences**: Reference for 80+ sequence types
- **Functions**: Function reference by category
- **Options**: 100+ tag and sequence options
- **Constructs**: PAD language constructs
- **@ Variables**: Groups and types documentation

### Custom Tags

This application defines custom tags in `/_tags/`:

| Tag | Purpose |
|-----|---------|
| `{example}` | Display code examples with source and output |
| `{demo}` | Create demo display tables |
| `{table}` | Table rendering for demos |
| `{extra}` | List related files for a fragment |
| `{tag}` | Format tag references in documentation |
| `{manual}` | Create links to manual pages |
| `{source}` | Source code display |

### Helper Libraries

The `/_lib/` directory contains:

| File | Purpose |
|------|---------|
| `lib.php` | Core helper functions (sequenceDir, getExtra, layout) |
| `colors.php` | Syntax highlighting for PAD and PHP code |
| `util.php` | Utility functions |
| `file.php` | File operations |
| `reference.php` | Reference data handling |
| `menu.php` | Navigation building |

---

## Getting Started

### Prerequisites

- PHP 7.4+
- MySQL/MariaDB (for database examples)
- Apache with mod_rewrite (recommended)

### Installation

1. Configure the web server to point to `www/` directory
2. Set up the database:
   ```bash
   mysql -u root -p < apps/pad/_install/classicmodels.sql
   ```
3. Update database credentials in `apps/pad/_config/config.php`

### Accessing the Application

Navigate to the application URL in your browser to access:

- **Manual**: Step-by-step tutorials
- **Fragments**: Browse and run code examples
- **Reference**: Complete API documentation

### Creating Your Own Application

1. Create a new directory in `apps/`
2. Add `_config/config.php` with database settings
3. Create `index.php` and `index.pad` for your first page
4. Add `_inits.php` for application startup code
5. Define custom tags in `_tags/` as needed

**Minimal Example:**

```php
// apps/myapp/index.php
<?php
  $title = 'My Application';
  $message = 'Hello from PAD!';
?>
```

```html
<!-- apps/myapp/index.pad -->
<!DOCTYPE html>
<html>
<head>
  <title>{$title}</title>
</head>
<body>
  <h1>{$title}</h1>
  <p>{$message}</p>
</body>
</html>
```

---

## Statistics

| Metric | Count |
|--------|-------|
| Framework Lines of Code | ~8,254 |
| Built-in Tags | 54 |
| Sequence Types | 80+ |
| Built-in Functions | 40+ |
| Application PAD Files | 756 |
| Application PHP Files | 170 |
| Code Examples (Fragments) | 295+ |
| Manual Chapters | 20+ |

---

*This application is the authoritative source for learning PAD. Browse the manual for concepts, explore fragments for examples, and consult the reference for complete API details.*
