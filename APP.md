# PAD Application Development

This document covers building applications with PAD - template syntax, tags, patterns, and best practices.

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
| `_functions/` | Pipe functions | `{echo $x \| myfunc}` → `myfunc.php` |
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

---

## Template Syntax

### Variables
```
{$variable}                    # Output variable
{$user.name}                   # Object/array property
{$items[0]}                    # Array index
```

**Output escaping options:**
```
{$text}                        # Raw output (no escaping)
{!text}                        # HTML escaped output
{$text | html}                 # HTML escaped via pipe
{$text | url}                  # URL encoded
```

### Pipe Functions
**IMPORTANT:** Pipe functions require a tag like `{echo}` - you cannot use bare expressions.
```
{echo $name | upper}                # Correct - uppercase
{echo $text | trim | lower}         # Correct - chain multiple
{echo $date | date('Y-m-d')}        # Correct - with parameters

{$name | upper}                     # WRONG - bare expression won't work
```

**Common String Functions:**
```
{echo $text | upper}                # Uppercase
{echo $text | lower}                # Lowercase
{echo $text | trim}                 # Remove whitespace
{echo $text | capitalize}           # Capitalize first letter
{echo $text | bold}                 # Wrap in <b> tags
{echo $text | html}                 # HTML-encode
{echo $text | left(5)}              # First 5 characters
{echo $text | cut(100)}             # Truncate to 100 chars
{echo $text | after('@')}           # Everything after first @
{echo $text | before('.')}          # Everything before first .
{echo $text | between('(', ')')}    # Extract between delimiters
{echo $text | contains('word')}     # Check if contains substring
```

**String Concatenation (with @ marker):**
```
{echo $text | . ' suffix'}              # Append string
{echo $text | 'prefix ' . }             # Prepend string
{echo $text | 'prefix ' . @ . ' suffix'}  # @ marks where value goes
```

**The @ placeholder in expressions:**
```
{echo 50 | @ * 4}                       # @ represents the current value
{echo 50 | @ * 4 | @ * 2}               # Chain with @ at each step
{echo 50 | '"' . @ . '"'}               # Wrap value in quotes
```

**Chaining Multiple Functions:**
```
{echo $email | after('@') | before('.')}   # Extract domain name
{echo 'Hello: World' | after(': ') | upper}  # "WORLD"
```

**Number Formatting:**
```
{echo $price | %.2f}                # Format to 2 decimal places
{echo $value | number(2)}           # Alternative number format
```

**Printf-style format specifiers:**
```
{echo $nbr | %.5f}          # 5 decimal places
{echo $nbr | %'.09d}        # Zero-padded to 9 digits
{echo $nbr | %d}            # Integer
{echo $nbr | %e}            # Scientific notation
{echo $nbr | %g}            # General format
{echo $nbr | %o}            # Octal
{echo $nbr | %x}            # Hexadecimal
```

### Pipe Arithmetic
Arithmetic pipes require a space between the operator and operand, and must use `{echo}`:
```
{echo $value | + 1}          # Correct - adds 1
{echo $value | +1}           # Wrong - no space
{echo $value | * 2}          # Correct - multiplies by 2
{$value | + 1}               # Wrong - bare expression
```

### Loops
```
{users}
  <li>{$name} - {$email}</li>
{/users}
```

### While and Until Loops
```
{set $i = 1}
{while $i le 10}
  Item {$i}
  {increment $i}
{/while}

{set $count = 5}
{until $count eq 0}
  Countdown: {$count}
  {decrement $count}
{/until}
```

### Loop Control
```
{items}
  {if $skip eq 1}{break}{/if}      # Break current loop
  {$name}
{/items}

{outer}
  {inner}
    {break 'outer'}                 # Break named outer loop
    {break -2}                      # Break by level
  {/inner}
{/outer}
```

**Three types of loop control:**
```
{staff}
  {if $name eq 'jack'}{continue 'staff'}{/if}  # Skip this iteration
  {if $name eq 'bob'}{cease 'staff'}{/if}      # Soft stop (graceful end)
  {if $name eq 'sue'}{break 'staff'}{/if}      # Hard stop (immediate exit)
  {$name}
{/staff}
```

- `{continue 'tag'}` - Skip to next iteration (like PHP's continue)
- `{cease 'tag'}` - Stop iteration gracefully, process remaining output
- `{break 'tag'}` - Exit immediately, discard remaining

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

**If/Else Syntax:**
Conditionals must use comparison operators (`eq`, `ne`, `gt`, `lt`, `ge`, `le`, `==`, `!=`, etc.):
```
{if $count eq 0}Empty{/if}              # Correct
{if {clock 'L'} eq 1}Leap year{/if}     # Correct - nested tag as value
{if $flag}True{/if}                      # Wrong - needs comparison
```

### Iteration Properties
Properties are tags with the `property:` prefix for clarity and to avoid naming conflicts:
```
{items}
  {property:first}First item{/property:first}
  {property:last}Last item{/property:last}
  {property:even}Even row{/property:even}
  {property:odd}Odd row{/property:odd}
  Count: {property:count}
  Index: {property:current}
{/items}
```

**Available properties:**
- `first`, `last`, `notFirst`, `notLast` - Position checks
- `border` (first or last), `middle` (neither first nor last)
- `even`, `odd` - Alternating rows
- `current` - Current index (1-based)
- `count` - Total items
- `remaining`, `done` - Items left/processed
- `key` - Current array key
- `fields` - Iterate field name/value pairs

**Alternative syntax:** `{&current}`, `{&count}` (shorthand for property access)

### Tags with Options
```
{tagname option="value"}
{data $var=value}
{items sort="name" rows="10"}
```

### Variable Assignment with {set}
```
{set $name = 'Alice'}              # Assign string
{set $count = 0}                   # Assign number
{set $total = $price * $qty}       # Assign expression
{set $upper | upper}               # Assign with pipe (uses previous value)
```

### Level vs Occurrence Variables
Variables prefixed with `$` are level variables (same for all iterations). Variables prefixed with `%` are occurrence variables (change each iteration):
```
{set $range = 10}

{sequence '1..5', $abc=$range, %xyz=$range}
  Level: {$abc}      # Always 10
  Occurrence: {$xyz} # 1, 2, 3, 4, 5
{/sequence}
```

**Per-iteration calculations:**
```
{staff %total = $salary + $bonus}
  {$name}: {$total}
{/staff}
```

### Inline Data Definition with {data}
```
{data 'colors'}
  ["red", "green", "blue"]
{/data}

{colors}
  <li>{$colors}</li>
{/colors}
```

Supports JSON arrays, objects, and tuples:
```
{data 'users'}
  [{"name": "Alice", "role": "admin"}, {"name": "Bob", "role": "user"}]
{/data}

{data 'items'}
  ('one', 'two', 'three')
{/data}
```

**Multiple data formats supported (JSON, XML, YAML, CSV):**
```
{data 'myXML'}
  <data><row name="bob" phone="123" /></data>
{/data}

{data 'myYAML'}
  ---
  - name: bob
    phone: 123
{/data}

{data 'myCSV'}
  name,phone
  bob,123
  alice,456
{/data}
```

### Switch Tag (Alternating Values)
```
{items}
  <tr style="background: {switch '#fff', '#eee'}">
    <td>{$name}</td>
  </tr>
{/items}
```
Alternates between values on each iteration - useful for zebra striping.

### Range Expressions
```
{if $value range (20, 40)}
  Value is between 20 and 40
{/if}

{if 30 range (1, 100)}ok{/if}
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

---

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

---

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

---

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

### Database Template Tags
Query databases directly from templates:
```
{field "count(*) from users"}                    # Single value
{field "name from users where id = 1"}           # Single field

{table "SELECT * FROM users ORDER BY name"}
  <tr><td>{$name}</td><td>{$email}</td></tr>
{/table}
```

---

## Best Practices

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

**IMPORTANT:** Always use `{echo}` or another tag - bare expressions don't work:
```
{echo $text | trim}              # Correct - PAD function
{echo $text | strlen}            # Correct - PHP function
{echo $items | count}            # Correct - PHP function
{echo $name | ucfirst}           # Correct - PHP function

{$text | trim}                   # WRONG - bare expression
```

---

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

**Complete list of type prefixes:**
| Prefix | Purpose |
|--------|---------|
| `app:` | App tag from `_tags/` directory |
| `pad:` | Built-in PAD tag |
| `php:` | Call PHP function directly |
| `function:` | Custom PAD function from `_functions/` |
| `data:` | Defined data block |
| `content:` | Content block definition |
| `local:` | Files from `_data/` directory |
| `script:` | Execute from `_scripts/` |
| `array:` | Access array as loop |
| `constant:` | Access PHP constant |
| `bool:` | Access bool definition |
| `pull:` | Retrieved stored sequence |
| `field:` | Database field query |
| `table:` | Database table query |
| `action:` | Sequence action |
| `shift:` | Sequence shift operation |

**Function type prefixes in pipes:**
```
{$abc | app:substr (1, 1)}    # Call app function
{$abc | pad:substr (1, 1)}    # Call pad function
{$abc | php:substr (@, 1, 1)} # Call raw PHP function (@ = value)
```

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

### The `true` and `false` Tags
Literal boolean conditions for always/never showing content:
```
{true}This is always shown{/true}
{false}This is never shown{/false}
```

### The `code` Tag
Execute PHP code within templates:
```
{code}
  $result = calculateSomething();
{/code}

{code sandbox, function}
  // Sandboxed execution with limited scope
  $local = 'value';
{/code}
```

### The `pad` Tag with Content Blocks
Process template content with data using `@start@` and `@end@` markers:
```
{pad data='myData'}
  @start@
    <li>{$name}</li>
  @end@
{/pad}
```

### The `content` Tag
Define named content templates for reuse:
```
{content 'rowTemplate'}
  @start@
    <tr><td>{$name}</td><td>{$value}</td></tr>
  @end@
{/content}

{pad data='items', content='rowTemplate'}
```

**Content with sorting and pagination:**
```
{myContent data='myData', sort='name'}
{myContent data='myData', sort='name DESC'}
{myContent data='myData', sort='volume;edition'}           # Multiple fields
{myContent data='myData', sort='volume DESC; edition ASC'} # Mixed directions
{myContent data='myData', sort='file NATURAL'}             # Natural ordering
{myContent data='myData', rows=10, page=2}                 # Pagination
```

### The `file` Tag
Write content to files:
```
{file dir='output', name='report', ext='txt'}
  Report content here
{/file}

{file dir='logs', name='entry', ext='log', date, stamp}
  Log entry with date and timestamp in filename
{/file}
```

### The `open` and `close` Tags
Output literal braces (for documentation/examples):
```
{open}echo $var{close}    # Outputs: {echo $var}
```

### Files Tag
Use `base='app'` for application-relative paths:
```
{files 'fragments/claude', base='app', mask='*.pad'}
  {$file}
{/files}
```

---

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

### Alternating Row Colors
```
{items}
  <div style="background: {property:even}#e0e0e0{/property:even}{property:odd}#f0f0f0{/property:odd}">
    {$name}
  </div>
{/items}
```

Or using the `{switch}` tag:
```
{items}
  <tr class="{switch 'odd', 'even'}"><td>{$name}</td></tr>
{/items}
```

### Conditional Table Wrapper
```
{items}
  {property:first}<table border="1">{/property:first}
  <tr><td>{$name}</td></tr>
  {property:last}</table>{/property:last}
{/items}
```

### Comma-Separated List
```
{items}
  {property:notFirst}, {/property:notFirst}{$name}
{/items}
```
Output: `Alice, Bob, Charlie`

### Nested Loop with Arithmetic
```
{sequence '1..3', name='row'}
  <tr>
    {sequence '1..4', name='col'}
      <td>{echo $row * 10 + $col}</td>
    {/sequence}
  </tr>
{/sequence}
```

### Extract Domain from Email
```
{echo $email | after('@') | before('.')}
```

---

## Quick Reference

### Essential Syntax Summary

| Syntax | Purpose | Example |
|--------|---------|---------|
| `{$var}` | Output variable | `{$name}` |
| `{!var}` | HTML-escaped output | `{!userInput}` |
| `{$obj.prop}` | Property access | `{$user.email}` |
| `{echo expr}` | Evaluate expression | `{echo $a + $b}` |
| `{echo $x \| func}` | Pipe function | `{echo $text \| upper}` |
| `{set $x = val}` | Assign variable | `{set $count = 0}` |
| `{if cond}...{/if}` | Conditional | `{if $x eq 1}yes{/if}` |
| `{tag}...{/tag}` | Iterate array | `{users}{$name}{/users}` |
| `{while}...{/while}` | While loop | `{while $i lt 10}...{/while}` |
| `{case}...{/case}` | Switch/case | `{case $x}{when 'a'}...{/case}` |
| `{get 'page'}` | Include page | `{get 'fragments/nav'}` |
| `{data 'name'}...{/data}` | Define data | `{data 'items'}[1,2,3]{/data}` |
| `{property:name}` | Iteration property | `{property:first}...{/property:first}` |
| `{sequence}` | Generate sequence | `{sequence '1..10', name='n'}` (see SEQUENCES.md) |
| `{break}` | Hard stop loop | `{break}` or `{break 'outer'}` |
| `{continue}` | Skip iteration | `{continue 'loopname'}` |
| `{cease}` | Soft stop loop | `{cease 'loopname'}` |

### Comparison Operators

| Operator | Meaning |
|----------|---------|
| `eq`, `==` | Equal |
| `ne`, `!=` | Not equal |
| `gt`, `>` | Greater than |
| `lt`, `<` | Less than |
| `ge`, `>=` | Greater or equal |
| `le`, `<=` | Less or equal |
| `and` | Logical AND |
| `or` | Logical OR |
| `range (a, b)` | Value in range |

### Common Pipe Functions

| Function | Purpose |
|----------|---------|
| `upper`, `lower` | Case conversion |
| `trim` | Remove whitespace |
| `html` | HTML encode |
| `date('fmt')` | Format date |
| `+ n`, `- n`, `* n`, `/ n` | Arithmetic |
| `left(n)`, `cut(n)` | Truncate |
| `after('x')`, `before('x')` | Extract substring |
| `contains('x')` | Check substring |
| `. 'str'` | Concatenate |

### Key Distinctions from PHP

1. **Templates drive execution** - not code including templates
2. **Use `{echo}` for pipes** - bare `{$var | func}` doesn't work
3. **Arithmetic needs space** - `{echo $x | + 1}` not `| +1`
4. **Conditionals need comparison** - `{if $flag eq 1}` not `{if $flag}`
5. **Quote literal strings** - `{count 'items'}` not `{count items}`
6. **`{continue}` skips iterations** - like PHP; use `{resume}` to transform stored sequences
7. **Use type prefixes** - `{pull:seq}`, `{data:items}`, `{php:func}` to disambiguate
8. **`$` vs `%` variables** - `$var` is level (constant), `%var` is occurrence (per-iteration)
9. **`@` placeholder** - represents current value in pipes: `{echo 5 | @ * 2}`
10. **Multiple data formats** - JSON, XML, YAML, CSV all supported in `{data}` blocks
