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

## Template Syntax Details

### Pipe Arithmetic
Arithmetic pipes require a space between the operator and operand:
```
{$value | + 1}          # Correct - adds 1
{$value | +1}           # Wrong - won't work
{$value | * 2}          # Correct - multiplies by 2
```

### If/Else Syntax
Conditionals must use comparison operators (`eq`, `ne`, `gt`, `lt`, `ge`, `le`, `==`, `!=`, etc.):
```
{if $count eq 0}Empty{/if}              # Correct
{if {clock 'L'} eq 1}Leap year{/if}     # Correct - nested tag as value
{if $flag}True{/if}                      # Wrong - needs comparison
```

### Expression Evaluation
To evaluate arithmetic expressions, use `{echo expression}`:
```
{echo 365 - {clock 'z'}}                # Evaluates: 365 - 347 = 18
{echo $total * 1.1}                      # Evaluates multiplication
{365 - {clock 'z'}}                      # Wrong - outputs literal "{365 - 347}"
```

### Calling PHP Functions
PHP functions can be called directly as tags without custom wrappers:
```
{date_default_timezone_get}             # Calls PHP function directly
{time}                                   # Returns Unix timestamp
{rand 1 100}                            # Random number between 1-100
```

## Custom Tags

### Creating Tags with Parameters
Tags in `_tags/` receive parameters via `$padOpt[$pad]` array:
- `$padOpt[$pad][0]` - The complete unparsed options string
- `$padOpt[$pad][1]` - First parameter (already parsed/evaluated)
- `$padOpt[$pad][2]` - Second parameter, etc.

Named parameters are in `$padPrm[$pad]`:
- `$padPrm[$pad]['format']` - Named parameter value

**Example** (`_tags/clock.php`):
```php
<?php
  // Usage: {clock 'H:i:s'} or {clock format='Y-m-d'}
  $format = $padPrm[$pad]['format'] ?? $padOpt[$pad][1] ?? 'Y-m-d H:i:s';
  return date($format);
?>
```

### Data Files
JSON/XML files in `_data/` become iterable tags:

**File** (`_data/menu.json`):
```json
[
  { "page": "index", "text": "Home" },
  { "page": "about", "text": "About" }
]
```

**Template**:
```
{menu}
  <a href="?{$page}">{$text}</a>
{/menu}
```

## Form Handling

### Automatic Form Variables
PAD automatically makes POST form fields available as PHP variables matching the field name:
```html
<form method="post">
  <input name="username">    <!-- Available as $username -->
  <input name="email">       <!-- Available as $email -->
</form>
```

**Important:**
- Variables are only populated on POST requests, not GET
- Always check request method: `if ($_SERVER['REQUEST_METHOD'] == 'POST')`
- Watch for naming conflicts with other variables (e.g., form field `message` vs success `$message`)

**Example** (`contact.php`):
```php
<?php
  $successMsg = '';  // Use different name to avoid conflict with form field
  $formEmail = $email ?? '';  // Form field available as $email on POST

  if ($_SERVER['REQUEST_METHOD'] == 'POST' && $action == 'send') {
    // $email, $name, $message are available from form
    // Process form...
    $successMsg = 'Message sent!';
  }
?>
```

## Database Operations

### The `db()` Wrapper
PAD provides a `db()` function for database queries. It uses positional placeholders `{0}`, `{1}`, etc.

**Important:** PAD does NOT add quotes around placeholders - you must add them yourself for string values:
```php
// Correct - quotes around string placeholders
db("SELECT * FROM users WHERE username='{0}'", [$username]);
db("INSERT INTO posts (title, content) VALUES ('{0}', '{1}')", [$title, $content]);

// Wrong - missing quotes for strings
db("SELECT * FROM users WHERE username={0}", [$username]);  // SQL error!

// Numeric values don't need quotes
db("SELECT * FROM users WHERE id={0}", [$id]);
```

### Query Types
The `db()` function supports special prefixes:

```php
// RECORD - Returns single row as associative array
$user = db("RECORD * FROM users WHERE id={0}", [$id]);

// ARRAY - Returns multiple rows as array of arrays
$users = db("ARRAY * FROM users ORDER BY name");

// FIELD - Returns single value
$count = db("FIELD COUNT(*) FROM users");

// CHECK - Returns boolean (row exists)
// Syntax: CHECK tablename WHERE ... (NOT "CHECK * FROM tablename")
$exists = db("CHECK users WHERE username='{0}'", [$username]);

// INSERT - Returns inserted ID
$id = db("INSERT INTO users (name, email) VALUES ('{0}', '{1}')", [$name, $email]);

// UPDATE - Updates rows
db("UPDATE users SET name='{0}' WHERE id={1}", [$name, $id]);
```

### CHECK Syntax
The CHECK command has special syntax - do NOT use `* FROM`:
```php
// Correct
$exists = db("CHECK users WHERE email='{0}'", [$email]);

// Wrong - will cause errors
$exists = db("CHECK * FROM users WHERE email='{0}'", [$email]);
```

## PAD Application Best Practices

### Redirects
PAD applications must NOT use PHP's `exit` or `die`. Use PAD's redirect function:
```php
// Correct - PAD handles cleanup properly
padRedirect('tickets/index');
padRedirect("tickets/view&id=$id");

// Wrong - bypasses PAD's cleanup, causes issues
header('Location: ?tickets/index');
exit;
```

### Variable Naming in `_inits.php`
Variables set in `_inits.php` can overwrite form field variables. Use distinct names:
```php
// In _inits.php - use prefixed names to avoid conflicts
$session_user = $_SESSION['username'] ?? '';  // Good
$username = $_SESSION['username'] ?? '';       // Bad - conflicts with form field 'username'
```

### CSS and JavaScript in Templates
PAD parses `{ }` as template tags. CSS and JavaScript use braces extensively, causing parsing errors and loops.

**Solution:** Move CSS/JS to static files in `www/appname/`:
```html
<!-- In _inits.pad - link to static CSS -->
<link rel="stylesheet" href="style.css">

<!-- NOT inline styles with braces -->
<style>
  body { color: red; }  <!-- PAD tries to parse { color: red } as a tag! -->
</style>
```

### Table Subsystem vs Direct SQL
PAD has two approaches for database access:

1. **Direct SQL with `db()`** - Write your own queries
2. **Table Subsystem** - Define `$padTables` and let PAD handle queries

**Do NOT mix them.** If using direct SQL queries, do NOT set `$padTables` or `$padRelations`:
```php
// If using db() for all queries, DON'T add these:
// $padTables['users'] = ['db' => 'users', 'key' => 'id'];
// $padRelations['posts']['author'] = ['table' => 'users', 'key' => 'user_id'];
```

Setting `$padTables` activates the table subsystem (`pad/lib/table.php`) which can conflict with direct SQL and cause infinite loops.

### Pipe Functions
PAD pipe functions come from two sources:
1. Custom functions in `pad/functions/` (trim, upper, date, html, etc.)
2. Standard PHP functions called directly (strlen, count, etc.)

```
{$text | trim}              # PAD function from pad/functions/
{$text | strlen}            # PHP function called directly
{$items | count}            # PHP function called directly
{$name | ucfirst}           # PHP function called directly
```

## Common Patterns

### Dynamic Menu from JSON
```
{menu}
  <a href="?{$page}"{if $padPage == $page} class="active"{/if}>{$text}</a>
{/menu}
```

### Conditional Display with Nested Tags
```
{if {clock 'L'} eq 1}
  366 days (leap year)
{else}
  365 days
{/if}
```

### Calculated Values
```
Day {clock 'z' | + 1} of {if {clock 'L'} eq 1}366{else}365{/if}
Days remaining: {if {clock 'L'} eq 1}{echo 365 - {clock 'z'}}{else}{echo 364 - {clock 'z'}}{/if}
```

## Important Tag Behaviors

### Parameter Evaluation
Parameters are evaluated before being passed to tags. Use quotes to pass literal strings:
```
{count items}              # Wrong - passes the array itself
{count 'items'}            # Correct - passes the string "items"

{get $message}             # Wrong - evaluates $message first
{get 'fragments/hello'}    # Correct - passes literal page path
```

### Tag Type Prefixes
Tags can have multiple sources (app tags, data, sequences, etc.). Use type prefixes to resolve naming conflicts:
```
{pull:mySequence}...{/pull:mySequence}    # Explicitly use stored sequence
{data:items}...{/data:items}              # Explicitly use data store
{app:mytag}                                # Explicitly use app tag from _tags/
```

Available prefixes match types in `pad/types/`: `app`, `pad`, `data`, `content`, `field`, `pull`, `table`, `function`, etc.

### The `get` Tag
The `get` tag includes PAD pages, NOT variables:
```
{get 'fragments/hello'}     # Includes the page fragments/hello (.php + .pad)
{get 'admin/users'}         # Includes admin/users page
```

### The `case` Tag
Uses `{when value}` syntax for branches:
```
{case $color}
  {when 'red'} Stop
  {when 'yellow'} Caution
  {when 'green'} Go
  {else} Unknown
{/case}
```

### The `bool` Tag
Creates a named boolean condition usable as a tag:
```
{bool 'isActive'}1{/bool}      # Define the boolean

{isActive}                      # Use as a tag (NOT {$isActive})
  <p>Active!</p>
{else}
  <p>Inactive</p>
{/isActive}
```

### The `exists` Tag
Block tag for file existence checks (not nested in `{if}`):
```
{exists APP . 'path/to/file.pad'}
  File exists
{else}
  File not found
{/exists}
```

### The `count` Tag
Checks if an array has elements. Quote the array name:
```
{count 'items'}
  Array has elements
{else}
  Array is empty
{/count}
```

### The `output` Tag
Sets output type, does NOT capture content:
```
{output 'web'}        # Normal web output (default)
{output 'console'}    # Console output
{output 'download'}   # File download
```

### Sequence Subsystem Tags
The sequence tags (`continue`, `pull`, `keep`, `remove`, `flag`, `make`) operate on stored sequences:

```
{sequence 5, push='mySeq'}           # Create and store sequence
{continue add=10}                     # Transform: add 10 to each value
{continue reverse}                    # Transform: reverse order
{pull:mySeq} {$sequence} {/pull:mySeq}  # Iterate stored sequence
```

**Note:** `{continue}` is NOT like PHP's continue statement. It continues operating on a stored sequence with transformations like `add`, `subtract`, `reverse`, `even`, `odd`, etc.

### Sequence Variable Access
Use named sequences instead of level-based `$-1` syntax:
```
{sequence 5, name='n'}
  {$n}                    # Correct - use named variable
{/sequence}

{sequence 5}
  {$-1}                   # Avoid - level-based access can be fragile
{/sequence}
```

### Iteration Properties
Two syntax forms for iteration properties:

**Tag syntax** (block):
```
{items}
  {first}First item{/first}
  {last}Last item{/last}
  {even}Even row{/even}
  {odd}Odd row{/odd}
{/items}
```

**Variable syntax** (with `&` prefix):
```
{items}
  Item {&current} of {&count}: {$name}
  {if &first}(first){/if}
{/items}
```

### Files Tag
Use `base='app'` for application-relative paths:
```
{files 'fragments/claude', base='app', mask='*.pad'}
  {$file}
{/files}
```

## Sequence Subsystem

The sequence subsystem is a powerful mathematical sequence generation and manipulation engine. It allows generating various mathematical sequences, storing them, and applying transformations.

### Basic Sequence Generation

```
{fibonacci rows=10}{$fibonacci} {/fibonacci}      # Fibonacci numbers
{prime rows=15}{$prime} {/prime}                  # Prime numbers
{sequence '1..10', name='n'}{$n} {/sequence}      # Range 1-10
{random minimal=1, maximal=100, rows=5}           # Random numbers
{list '5;2;8;1;9'}{$list} {/list}                 # Custom list
```

### Sequence Types (80+)

**Mathematical sequences:**
- `fibonacci`, `lucas`, `pell`, `tribonacci`, `catalan`, `bell`, `perrin`
- `prime`, `composite`, `perfect`, `mersenne`

**Figurate numbers:**
- `triangular`, `square`, `cubic`, `pentagonal`, `hexagonal`, `heptagonal`, `octagonal`
- `tetrahedral`, `octahedral`, `biquadratic`

**Filters:**
- `even`, `odd`, `happy`, `lucky`, `harshad`, `palindrome`
- `semiprime`, `powerful`, `polite`, `kaprekar`

**Arithmetic operations:**
- `add`, `subtract`, `multiply`, `divide`, `modulo`, `power`/`exponentiation`
- `ceil`, `floor`, `round`, `negation`

**Logical operations:**
- `and`, `or`, `xor`, `not`, `nand`, `nor`, `xnor`

**Generation:**
- `range` (e.g., `'1..10'`), `list` (e.g., `'1;5;3;8'`), `loop`, `random`, `repeat`
- `oeis` - fetch from Online Encyclopedia of Integer Sequences

### Storing and Retrieving Sequences

**IMPORTANT:** Store names cannot be the same as action names (e.g., can't use `push='first'` because `first` is an action).

```
{sequence '1..10', push='mySeq'}           # Store sequence
{pull:mySeq}{$sequence} {/pull:mySeq}      # Retrieve and iterate

# Use pull: prefix to avoid naming conflicts with app tags
{sequence 5, push='nums'}
{pull:nums} {$sequence} {/pull:nums}
```

### Sequence Actions (Transformations)

**Order manipulation:**
```
{pull:nums reverse}       # Reverse order
{pull:nums sort}          # Sort ascending
{pull:nums shuffle}       # Randomize order
```

**Selection:**
```
{pull:nums first}         # First element
{pull:nums first=3}       # First 3 elements
{pull:nums last=3}        # Last 3 elements
{pull:nums shift=2}       # Remove first 2
{pull:nums pop=2}         # Remove last 2
{pull:nums element=5}     # Get 5th element
{pull:nums slice='3|4'}   # From position 3, length 4
```

**Aggregation:**
```
{pull:nums sum}           # Sum of all elements
{pull:nums product}       # Product of all elements
{pull:nums average}       # Mean value
{pull:nums median}        # Median value
{pull:nums minimum}       # Smallest value
{pull:nums maximum}       # Largest value
{pull:nums count}         # Number of elements
{pull:nums distinct}      # Count of unique values
{pull:nums dedup}         # Remove duplicates
```

**Multi-sequence operations:**
```
{sequence '1..5', push='seqA'}
{sequence '3..8', push='seqB'}
{pull:seqA append='seqB'}        # Add seqB to end
{pull:seqA prepend='seqB'}       # Add seqB to start
{pull:seqA merge='seqB'}         # Merge, remove duplicates
{pull:seqA intersection='seqB'}  # Elements in both
{pull:seqA difference='seqB'}    # In seqA but not seqB
```

### The `continue` Tag

Applies transformations to a stored sequence without pulling it:
```
{sequence '1..10', push='nums'}
{continue add=100}                # Add 100 to each value
{continue reverse}                # Reverse order
{pull:nums}{$sequence} {/pull:nums}
```

**Note:** `{continue}` is NOT like PHP's continue statement!

### Sequence Plays (keep, remove, make, flag)

Filter or transform sequences based on sequence types:
```
{pull mySeq, keep, even}     # Keep only even values
{pull mySeq, remove, odd}    # Remove odd values
{pull mySeq, make, prime}    # Transform using prime
{pull mySeq, flag, even}     # Mark even entries
```

### Special Syntax Rules

**Some sequences need `sequence:` prefix:**
```
{sequence:repeat 42, rows=5}{$repeat}{/sequence:repeat}
{sequence:even rows=10}{$even}{/sequence:even}
```

**Chance sequence needs a numeric parameter:**
```
{chance 4, rows=15}    # 1-in-4 chance (not {chance rows=15})
```

### Named vs Unnamed Sequences

Always prefer named sequences for clarity:
```
{sequence 5, name='i'}
  {$i}                  # Clear, explicit
{/sequence}

{sequence '1..3', name='row'}
  {sequence '1..4', name='col'}
    ({$row},{$col})
  {/sequence}
{/sequence}
```

### Common Sequence Patterns

**Generate and transform:**
```
{sequence '1..10', push='nums'}
{continue add=5}
{continue reverse}
{pull:nums sort}{$sequence} {/pull:nums}
```

**Combine sequences:**
```
{sequence '1..5', push='seqA'}
{sequence '10..15', push='seqB'}
{pull:seqA append='seqB'}{$sequence} {/pull:seqA}
```

**Aggregate values:**
```
{sequence '1..100', push='nums'}
Sum: {pull:nums sum}{$sequence}{/pull:nums}
Avg: {pull:nums average}{$sequence}{/pull:nums}
```
