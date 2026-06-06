# CLAUDE.md - PAD Framework Reference

This file provides guidance for working with the PAD (PHP Application Driver) framework. 

---

## What is PAD?

**PAD (PHP Application Driver)** is an Inversion of Control PHP template engine. Unlike traditional frameworks where PHP code includes templates commands, PAD templates drive the execution flow - templates are first-class citizens that orchestrate data retrieval, logic, and output.

```
Traditional PHP: Controller → includes → Template
PAD:            Template → drives → Data & Logic
```

The template structure mirrors the application flow. No routing, no controllers - just create files.

### Hello World

**hello.php** - The application PHP file:
```php
<?php
  $hi = 'Hello World!';
?>
```

**hello.pad** - The PAD template:
```html
<html>
  <body>
    <h1>{$hi}</h1>
  </body>
</html>
```

---

## Project Structure

```
pad/
├── pad/      # PAD framework core (template engine, tag processors, expression evaluator)
├── apps/     # PAD applications (each subdirectory is a self-contained app)
├── www/      # Web server entry points (PHP entry points for each app)
├── docs/     # Documentation
└── DATA/     # Runtime data (logs, cache, dumps) - writable, excluded from git
```

---

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

---

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
    ├── _callbacks/        # Subdirectory callbacks
    ├── _functions/        # Subdirectory functions
    ├── _include/          # Subdirectory includes
    ├── _lib/              # Subdirectory lib
    ├── _options/          # Subdirectory options
    ├── _tags/             # Subdirectory tags
    ├── _inits.pad         # Subdirectory wrapper (top)
    ├── _exits.pad         # Subdirectory wrapper (bottom)
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
| `_scripts/` | Shell scripts | On demand |

### Wrapper Files (_inits.pad / _exits.pad)

These files wrap page content at each directory level, creating nested wrappers:

```
/_inits.pad        ← Root wrapper (top)
  /abc/_inits.pad  ← Subdirectory wrapper (top)
    [page content]
  /abc/_exits.pad  ← Subdirectory wrapper (bottom)
/_exits.pad        ← Root wrapper (bottom)
```

### PHP Execution Order (_inits.php / _exits.php)

PHP files execute in a specific order - all PHP runs before template rendering:

1. `/_inits.php`
2. `/abc/_inits.php`
3. `/abc/klm/_inits.php`
4. `/abc/klm/page.php`
5. `/abc/klm/_exits.php`
6. `/abc/_exits.php`
7. `/_exits.php`

### Directory Inheritance

When accessing a page in a subdirectory (e.g., `?abc/klm/page`):

- **_lib/** files from ALL parent directories are included (cumulative)
- **_inits.pad** from each level wraps the content (nested)
- **_tags/**, **_functions/**, **_include/**, etc. are searched from current directory up to root

Example for `?abc/klm/page`:
1. Include: `/_lib/*.php` → `/abc/_lib/*.php` → `/abc/klm/_lib/*.php`
2. Wrap: `/_inits.pad` → `/abc/_inits.pad` → `/abc/klm/_inits.pad`
3. Tag lookup: `/abc/klm/_tags/` first, then `/abc/_tags/`, then `/_tags/`

This allows subdirectories to:
- **Override** parent tags/functions with local versions
- **Add** new tags/functions only available in that subdirectory
- **Inherit** all functionality from parent directories

### _lib/ - PHP Functions

Files in `_lib/` are automatically included.

**_lib/helpers.php**:
```php
<?php
  function formatDate($date) {
    return date('F j, Y', strtotime($date));
  }
?>
```

### _tags/ - Custom Tags

**_tags/button.php**:
```php
<?php
  $label = padTagParm('label', 'Click');
  $href  = padTagParm('href', '#');
  $padContent = "<a href=\"$href\" class=\"button\">$label</a>";
?>
```

Use in templates: `{button label="Submit" href="?submit"}`

**_tags/json.php** (for React integration):
```php
<?php
  // Read JSON file from _data/, compact and HTML-escape for attributes
  $jsonContent = file_get_contents(APP . "_data/$padParm.json");
  $jsonData = json_decode($jsonContent, true);
  $jsonCompact = json_encode($jsonData);
  $padContent = htmlspecialchars($jsonCompact, ENT_QUOTES, 'UTF-8');
  return TRUE;
?>
```

Use in templates: `{json 'products' | ignore}` - Outputs JSON from `_data/products.json`

### _functions/ - Pipe Functions

**_functions/money.php**:
```php
<?php
  return '$' . number_format($padContent, 2);
?>
```

Use in templates: `{echo $price | money}`

---

## Template Syntax

### Variables
```
{$variable}                    # Output variable
{$user.name}                   # Object/array property
{$items[0]}                    # Array index
{!text}                        # HTML-escaped output
```

### Pipe Functions (CRITICAL: Always need {echo})
```
{echo $name | upper}              # Correct - uppercase
{echo $text | trim | lower}       # Chained functions
{echo $date | date('Y-m-d')}      # With parameters
{echo $value | + 1}               # Arithmetic (space required!)

{$name | upper}                   # WRONG - bare expression won't work!
{echo $value | +1}                # WRONG - needs space before 1
```

### Pipe Timing: Opening vs Closing Tags

Pipes can be applied to tags at two different points:

**Opening tag pipe** - Processes data BEFORE the tag content is rendered:
```
{items | sort}
  <li>{$name}</li>
{/items}
```

**Closing tag pipe** - Processes output AFTER the tag finishes:
```
{message}
  Content: {$message}
{/message | upper}
```

The placement determines when the pipe function is applied in the processing flow.

### Pipe Arithmetic
Arithmetic pipes require a space between the operator and operand:
```
{echo $value | + 1}          # Correct - adds 1
{echo $value | * 2}          # Correct - multiplies by 2
{echo $value | +1}           # Wrong - no space
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

**Important:** Conditionals need comparison operators:
```
{if $count eq 0}Empty{/if}     # Correct
{if $flag}True{/if}            # WRONG - needs comparison
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

{until $count eq 0}
  Countdown: {$count}
  {decrement $count}
{/until}
```

### Loop Control
```
{continue 'tagname'}    # Skip to next iteration (like PHP's continue)
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

### Case/Switch
```
{case $color}
  {when 'red'} Stop
  {when 'yellow'} Caution
  {when 'green'} Go
  {else} Unknown
{/case}
```

### Switch Tag (Alternating Values)
```
{items}
  <tr style="background: {switch '#fff', '#eee'}">
    <td>{$name}</td>
  </tr>
{/items}
```

### Data Definition
```
{data 'colors'}
  ["red", "green", "blue"]
{/data}

{colors}{$colors} {/colors}
```

Supports JSON, XML, YAML, and CSV formats:
```
{data 'myXML'}
  <data><row name="bob" phone="123" /></data>
{/data}

{data 'myCSV'}
  name,phone
  bob,123
  alice,456
{/data}
```

### Variable Assignment
```
{set $name = 'Alice'}              # Assign string
{set $count = 0}                   # Assign number
{set $total = $price * $qty}       # Assign expression
```

### Level vs Occurrence Variables
- `$var` - Level variable (constant for all iterations)
- `%var` - Occurrence variable (changes each iteration)

```
{staff %total = $salary + $bonus}
  {$name}: {%total}
{/staff}
```

### Range Expressions
```
{if $value range (20, 40)}
  Value is between 20 and 40
{/if}
```

### The @ Placeholder
The `@` represents the current value in expressions:
```
{echo 50 | @ * 4}                # @ represents the current value
{echo $text | '"' . @ . '"'}     # Wrap value in quotes
```

### Ignore - Preventing PAD from Parsing Curly Braces

The `ignore` feature tells PAD not to parse curly braces `{}` as PAD tags. Essential for JavaScript, JSON, CSS, or any content with curly braces.

**Three ways to use ignore:**

1. **Tag Pair** - Wrap content in ignore tags:
```html
{ignore}
<script>
  const user = { name: 'Alice', role: 'Developer' };
  if (user.active) { console.log('Active'); }
</script>
{/ignore}
```

2. **Pipe Function** - Apply to expression output:
```html
<div data-json="{json 'products' | ignore}"></div>
<div data-users="{echo $usersJson | ignore}"></div>
```

3. **Option** - Add to any tag:
```
{data 'rawJson' ignore}
{
  "theme": "dark",
  "settings": { "notifications": true }
}
{/data}
```

**When to use:**
- Inline JavaScript with object literals
- CSS with selector blocks
- JSON data in HTML attributes
- React/JSX components
- Any content with literal curly braces

---

## Critical Syntax Rules (Common Mistakes)

1. **Pipes need `{echo}`** - `{$var | upper}` won't work, use `{echo $var | upper}`
2. **Arithmetic needs space** - `{echo $x | + 1}` not `| +1`
3. **Quote literal strings** - `{count 'items'}` not `{count items}`
4. **No inline CSS/JS** - PAD parses `{ }` as tags; use external files or `{ignore}` wrapper
5. **Use `padRedirect()`** - Don't use `exit` or `die` in PAD apps
6. **Conditionals need comparison** - `{if $flag eq 1}` not `{if $flag}`
7. **`$` vs `%` variables** - `$var` is level (constant), `%var` is occurrence (per-iteration)
8. **CHECK syntax** - Use `db("CHECK table WHERE...")` NOT `db("CHECK * FROM table...")`
9. **Boolean options** - Use `{tag option}` NOT `{tag option="true"}` - just the option name is enough

---

## Type Prefixes

Resolve naming conflicts with explicit prefixes:

| Prefix | Purpose | Example |
|--------|---------|---------|
| `app:` | App tag from `_tags/` | `{app:mytag}` |
| `pad:` | Built-in PAD tag | `{pad:if}` |
| `php:` | Call PHP function | `{php:strlen(@)}` |
| `function:` | Custom PAD function | `{$x \| function:myfunc}` |
| `data:` | Defined data block | `{data:items}` |
| `content:` | Content block | `{content:header}` |
| `pull:` | Stored sequence | `{pull:mySeq}` |
| `field:` | Database field | `{field:"name from users"}` |
| `table:` | Database table | `{table:users}` |
| `local:` | Files from `_data/` | `{local:menu.json}` |
| `constant:` | PHP constant | `{constant:PHP_VERSION}` |
| `bool:` | Boolean store | `{bool:isAdmin}` |
| `array:` | Access array as loop | `{array:items}` |
| `action:` | Sequence action | `{action:reverse}` |

---

## Tags, Functions, Options & Properties Reference

See the following files for complete reference documentation:

- **[TAGS.md](docs/reference/TAGS.md)** - All built-in tags (control flow, data, database, file, output, navigation, execution, debug), type prefixes, options, and properties
- **[FUNCTIONS.md](docs/reference/FUNCTIONS.md)** - All pipe functions (string, case, HTML, date, arithmetic, printf format)
- **[sequences/](docs/sequences/)** - Sequence subsystem (80+ mathematical sequences, actions, transformations)

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
| `and` | Logical AND |
| `or` | Logical OR |
| `range (a, b)` | Value in range |

---

## Database Operations

See [DATABASE.md](docs/DATABASE.md) for complete database documentation including:
- PHP `db()` function (RECORD, ARRAY, FIELD, CHECK, INSERT, UPDATE)
- Template database tags ({field}, {table}, {record}, {array})
- PAD Select Subsystem (declarative table access, relations, automatic joins)

---

## Configuration

In `_config/config.php`:
```php
// Database connection
$padSqlHost     = 'localhost';
$padSqlDatabase = 'myapp';
$padSqlUser     = 'user';
$padSqlPassword = 'pass';

// Error handling: pad, boot, php, stop, exit, ignore, log, dump
$padErrorAction = 'pad';

// Debug mode: trace, stats, track, xml, xref
// $padInfo = 'trace';

// Output type: web, file, download, console
$padOutputType = 'web';

// Cache enabled
$padCache = false;
```

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

## Global Wrapper (_inits.pad)

Wrap all pages with a common layout:
```html
<!DOCTYPE html>
<html>
<head>
  <title>{$title}</title>
</head>
<body>
  <nav>
    <a href="?index">Home</a>
    <a href="?about">About</a>
  </nav>

  @pad@

  <footer>&copy; 2025 My App</footer>
</body>
</html>
```

The `@pad@` placeholder is replaced with each page's content.

---

## Common Patterns

### Zebra Striping
```
{items}
  <tr class="{even@items ? even : odd}">{$name}</tr>
{/items}
```

Or using the `{switch}` tag:
```
{items}
  <tr class="{switch 'odd', 'even'}"><td>{$name}</td></tr>
{/items}
```

### Comma-Separated List
```
{items}{if notFirst@items}, {/if}{$name}{/items}
```
Output: `Alice, Bob, Charlie`

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

## Library Functions Reference

### Navigation & Redirection

| Function | Description |
|----------|-------------|
| `padRedirect($url, $vars)` | Redirect to URL with optional variables |
| `padRestart($page)` | Restart processing with new page |
| `padFastLink($page, $vars)` | Create fast link with serialized variables |

### Field Access

| Function | Description |
|----------|-------------|
| `padFieldValue($name)` | Get field value from current data context |
| `padFieldCheck($name)` | Check if field exists |
| `padOptValue($name)` | Get option value |
| `padTagParm($name, $default)` | Get current tag parameter |

### Database

| Function | Description |
|----------|-------------|
| `db($sql, $vars)` | Execute SQL on application database |
| `padDb($sql, $vars)` | Execute SQL on PAD database |
| `padDbConnect($host, $user, $pass, $db)` | Create database connection |

### Data Processing

| Function | Description |
|----------|-------------|
| `padData($input, $type, $name)` | Convert input to PAD data array |
| `padDataForcePad($data)` | Force data into PAD format |
| `padToArray($obj)` | Convert object/resource to array |
| `padJson($data)` | Convert to JSON |

### File Operations

| Function | Description |
|----------|-------------|
| `padFileGet($file, $default)` | Read file contents |
| `padFilePut($file, $data, $append)` | Write file contents |
| `padFileCheck($file)` | Validate file path |

### Evaluation

| Function | Description |
|----------|-------------|
| `padEval($expr, $value)` | Evaluate expression |
| `padEvalBool($expr)` | Evaluate as boolean |

### Error Handling

| Function | Description |
|----------|-------------|
| `padError($message)` | Report error |
| `padExit($code)` | End request with status code |
| `padDump($error)` | Generate debug dump |

### Validation

| Function | Description |
|----------|-------------|
| `padValid($name)` | Validate tag/type name |
| `padValidFile($file)` | Validate file path |
| `padValidVar($name)` | Validate variable name |

### Utilities

| Function | Description |
|----------|-------------|
| `padRandomString($len)` | Generate random string |
| `padExplode($str, $delim, $limit)` | Smart explode with trimming |
| `padBetween($str, $open, $close)` | Extract between delimiters |
| `padMakeSafe($input, $len)` | Sanitize input |

---

## Framework Architecture

### Execution Flow
```
Request → pad.php → config → start/
                          → build/ (page assembly: _inits + @pad@ + _exits)
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
│                         RESPONSE                                 │
└─────────────────────────────────────────────────────────────────┘
```

### Architecture Notes

- **Procedural Design**: 140+ functions, minimal OOP
- **Global State**: Uses `$GLOBALS` and `global` declarations indexed by level
- **File-Based Dispatch**: `include` statements as control flow
- **Array-Indexed Levels**: Nesting tracked via `$padVariable[$pad]` pattern
- **15+ Years Production**: Stable, battle-tested codebase

---

## Debugging

- Set `$padInfo = 'trace'` for execution tracing
- Use `{dump}` tag for variable inspection
- Use `{trace}` tag for execution trace
- Check `DATA/` directory for error dumps and logs

### Testing PAD Pages from Command Line

You can fetch and analyze PAD pages directly from a running server using `curl`:

```bash
# Fetch a PAD page
curl "http://localhost/demo/?clock"

# Fetch with headers
curl -i "http://localhost/pad/?manual"

# Fetch and save output
curl -o output.html "http://localhost/myapp/?page/subpage"

# Fetch with query parameters
curl "http://localhost/app/?page&param=value"
```

**Examples:**
```bash
# Demo application pages
curl "http://localhost/demo/?index"        # Home page
curl "http://localhost/demo/?guestbook"    # Guestbook
curl "http://localhost/demo/?clock"        # Clock with date/time

# PAD reference application
curl "http://localhost/pad/?manual"        # Framework manual
curl "http://localhost/pad/?reference"     # Cross-reference
curl "http://localhost/pad/?hello/hello"   # Hello World test

# Debugging output
curl "http://localhost/app/?page&padInfo=trace"  # With trace
```

This is particularly useful for:
- **Automated testing** - Verify page output in scripts
- **Debugging** - Inspect generated HTML without a browser
- **Performance testing** - Measure response times
- **Regression testing** - Compare output against expected results
- **CI/CD integration** - Test PAD applications in pipelines

---

## Applications Overview

| App | Type | Description |
|-----|------|-------------|
| `pad` | Reference | Framework manual, regression tests, examples |
| `demo` | Standard | Interactive demo with guestbook, todo, contact, counter, clock |
| `hello` | Standard | Minimal Hello World example |
| `minimal` | Minimal | Single template file (no .php) |
| `cli` | CLI | Command-line interface for running PAD |
| `support` | Standard | Support portal with forum, news, tickets |
| `structure` | Example | Demo of nested _xxx directories and inheritance |
| `nono` | Non-PAD | Plain PHP without PAD framework |

---

## Best Practices

### Redirects
Use PAD's redirect function, NOT PHP's exit/die:
```php
padRedirect('tickets/index');      // Correct
header('Location: ?tickets/index'); exit;  // WRONG
```

### CSS and JavaScript
PAD parses `{ }` as tags. Prefer external files, or use `{ignore}` for inline code:
```html
<link rel="stylesheet" href="style.css">  <!-- Best -->
{ignore}<style>body { color: red; }</style>{/ignore}  <!-- OK -->
<style>body { color: red; }</style>        <!-- WRONG - will parse {} -->
```

### PAD + React Integration

See [REACT.md](docs/REACT.md) for complete React integration documentation including:
- Pattern 1: Static data with {json} tag
- Pattern 2: Dynamic data with {reactData} tag and providers
- Critical: Using `getAttribute('data')` NOT `dataset.data`
- File organization and common patterns

### Manual Pages and Fragment Files

When creating documentation with examples:

**Manual page** (`manual/topic.pad`) - Contains all explanatory text:
- Introduction and description
- Section headings (`<h2>`, `<h3>`)
- Explanatory paragraphs
- Tables and reference content
- Use `{example 'fragments/topic_N'}` to embed working examples

**Fragment files** (`fragments/topic_N.pad` + optional `topic_N.php`) - Minimal executable code only:
- Just the code that demonstrates the feature
- No `<h3>` headings or `<p>` introduction text
- Can have paired `.php` file for server data if needed
- Should be small and focused on one concept

**Example:**
```html
<!-- manual/pipes.pad -->
<h1>Pipes - Transform Output</h1>
<h3>Variable Pipes</h3>
<p>Apply functions to variables:</p>
{example 'fragments/pipes_1'}

<!-- fragments/pipes_1.pad -->
<p>Original: {$name}</p>
<p>Uppercase: {echo $name | upper}</p>
```

**Benefits:**
- Fragments are executable and testable
- Manual pages remain readable without code clutter
- Examples are reusable across documentation
- Keeps manual pages concise - people don't read big pages

### Avoid Over-Engineering
- Only make changes directly requested
- Don't add features beyond what was asked
- Keep solutions simple and focused
- Three similar lines is better than premature abstraction

### Variable Naming in _inits.php
Variables set in `_inits.php` can overwrite form field variables. Use distinct names:
```php
$session_user = $_SESSION['username'] ?? '';  // Good
$username = $_SESSION['username'] ?? '';       // Bad - conflicts with form field
```

---

## License

PAD is licensed under the GNU General Public License v3.0.
