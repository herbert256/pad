# CLAUDE.md - PAD Framework Complete Reference

This file provides comprehensive guidance for working with the PAD (PHP Application Driver) framework. All documentation is contained within this single file.

**Requirements:** PHP 8.0+

---

## What is PAD?

**PAD (PHP Application Driver)** is an Inversion of Control PHP template engine. Unlike traditional frameworks where PHP code includes templates, PAD templates drive the execution flow - templates are first-class citizens that orchestrate data retrieval, logic, and output.

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

**Important:** Internal links use `?page` format, not `/page`.

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

---

## Critical Syntax Rules (Common Mistakes)

1. **Pipes need `{echo}`** - `{$var | upper}` won't work, use `{echo $var | upper}`
2. **Arithmetic needs space** - `{echo $x | + 1}` not `| +1`
3. **Quote literal strings** - `{count 'items'}` not `{count items}`
4. **No inline CSS/JS** - PAD parses `{ }` as tags; use external files
5. **Use `padRedirect()`** - Don't use `exit` or `die` in PAD apps
6. **Conditionals need comparison** - `{if $flag eq 1}` not `{if $flag}`
7. **`$` vs `%` variables** - `$var` is level (constant), `%var` is occurrence (per-iteration)
8. **CHECK syntax** - Use `db("CHECK table WHERE...")` NOT `db("CHECK * FROM table...")`

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

## Tags Reference

### Control Flow Tags

| Tag | Description | Example |
|-----|-------------|---------|
| `{if}` | Conditional | `{if $x eq 1}yes{/if}` |
| `{case}` | Switch-case | `{case $x}{when 'a'}...{/case}` |
| `{while}` | While loop | `{while $i lt 10}...{/while}` |
| `{until}` | Until loop | `{until $done eq 1}...{/until}` |
| `{switch}` | Rotating switch | `{switch 'name', 'odd', 'even'}` |

### Variable and Data Tags

| Tag | Description | Example |
|-----|-------------|---------|
| `{set}` | Assign variable | `{set $count = 0}` |
| `{get}` | Include page | `{get 'fragments/nav'}` |
| `{data}` | Define data | `{data 'items'}[1,2,3]{/data}` |
| `{content}` | Store content | `{content 'header'}...{/content}` |
| `{bool}` | Store boolean | `{bool 'flag'}1{/bool}` |
| `{echo}` | Evaluate/output | `{echo $a + $b}` |

### Counter Tags

| Tag | Description | Example |
|-----|-------------|---------|
| `{count}` | Check array elements | `{count 'items'}has items{/count}` |
| `{increment}` | Add 1 | `{increment $i}` |
| `{decrement}` | Subtract 1 | `{decrement $i}` |

### Database Tags

| Tag | Description | Example |
|-----|-------------|---------|
| `{field}` | Query single value | `{field "count(*) from users"}` |
| `{table}` | Query rows | `{table "SELECT * FROM users"}...{/table}` |
| `{array}` | Query array | `{array 'tablename'}` |
| `{record}` | Query record | `{record 'SQL query'}` |

### File Tags

| Tag | Description | Example |
|-----|-------------|---------|
| `{files}` | List files | `{files dir='images' mask='*.jpg'}` |
| `{dir}` | Directory listing | `{dir '/path'}` |
| `{file}` | Write file | `{file dir='output' name='report'}...{/file}` |
| `{exists}` | Check file exists | `{exists APP . 'file.pad'}...{/exists}` |

### Output Tags

| Tag | Description | Example |
|-----|-------------|---------|
| `{output}` | Set output type | `{output 'download'}` |
| `{tidy}` | Format HTML | `{tidy}...{/tidy}` |
| `{ignore}` | Escape PAD syntax | `{ignore}{not processed}{/ignore}` |
| `{open}` | Opening brace | `{open}` → `{` |
| `{close}` | Closing brace | `{close}` → `}` |

### Navigation Tags

| Tag | Description | Example |
|-----|-------------|---------|
| `{redirect}` | HTTP redirect | `{redirect 'url'}` |
| `{restart}` | Restart processing | `{restart 'page'}` |
| `{page}` | Include page | `{page 'pagename'}` |

### Execution Tags

| Tag | Description | Example |
|-----|-------------|---------|
| `{code}` | Execute PHP | `{code 'phpfile'}` |
| `{sandbox}` | Sandboxed PHP | `{sandbox 'phpfile'}` |
| `{ajax}` | AJAX handler | `{ajax 'handler'}` |
| `{curl}` | HTTP request | `{curl 'http://example.com'}` |

### Debug Tags

| Tag | Description | Example |
|-----|-------------|---------|
| `{dump}` | Debug output | `{dump}` |
| `{trace}` | Enable tracing | `{trace}...{/trace}` |
| `{error}` | Trigger error | `{error 'message'}` |
| `{exception}` | Throw exception | `{exception 'message'}` |
| `{exit}` | Exit processing | `{exit}` |

### Boolean/Value Tags

| Tag | Returns |
|-----|---------|
| `{true}` | TRUE |
| `{false}` | FALSE |
| `{null}` | NULL |

### Sequence Tags

| Tag | Description |
|-----|-------------|
| `{sequence}` | Generate sequence |
| `{pull}` | Pull stored sequence |
| `{resume}` | Transform stored sequence |
| `{keep}` | Keep matching values |
| `{remove}` | Remove matching values |
| `{make}` | Transform values |
| `{flag}` | Set sequence flag |

---

## Functions Reference

### String Extraction (Delimiter-Based)

| Function | Description | Example |
|----------|-------------|---------|
| `after(d)` | After first delimiter | `{'a/b/c' \| after('/')}` → `b/c` |
| `afterLast(d)` | After last delimiter | `{'a/b/c' \| afterLast('/')}` → `c` |
| `before(d)` | Before first delimiter | `{'a/b/c' \| before('/')}` → `a` |
| `beforeLast(d)` | Before last delimiter | `{'a/b/c' \| beforeLast('/')}` → `a/b` |

### Substring Operations

| Function | Description | Example |
|----------|-------------|---------|
| `left(n)` | First N chars | `{'Hello' \| left(3)}` → `Hel` |
| `right(n)` | Last N chars | `{'Hello' \| right(3)}` → `llo` |
| `substr(s,l)` | Substring | `{'Hello' \| substr(1,3)}` → `ell` |
| `mid(s,l)` | Middle (1-based) | `{'Hello' \| mid(2,3)}` → `ell` |
| `max_len(n)` | Truncate to max | `{'Hello World' \| max_len(5)}` → `Hello` |

### Case Conversion

| Function | Description |
|----------|-------------|
| `upper` | Uppercase |
| `lower` | Lowercase |
| `capitalize` | Capitalize words |
| `ucwords` | Alias for capitalize |

### String Manipulation

| Function | Description |
|----------|-------------|
| `trim` | Remove whitespace |
| `replace(s,r)` | Replace text |
| `cut(text)` | Remove text |
| `white` | Normalize whitespace |

### HTML & Encoding

| Function | Description |
|----------|-------------|
| `html` | HTML encode |
| `sanitize` | Full special char sanitization |
| `url` | URL encode |
| `slashes` | Add slashes |
| `stripslashes` | Remove slashes |

### HTML Formatting

| Function | Description |
|----------|-------------|
| `bold` | Wrap in `<b>` tags |
| `nbsp` | Replace spaces with `&nbsp;` |

### Testing & Conditions

| Function | Description | Returns |
|----------|-------------|---------|
| `contains(s)` | Contains substring | `'1'` or `''` |
| `in(vals...)` | In list of values | `'1'` or `''` |
| `like(p)` | SQL LIKE pattern | `'1'` or `''` |
| `between(a,b)` | Exclusively between | TRUE/FALSE |
| `range(a,b)` | Inclusively in range | TRUE/FALSE |
| `exists` | File exists in APP | `'1'` or `'0'` |

### Date/Time

| Function | Description | Example |
|----------|-------------|---------|
| `date(fmt)` | Format date | `{$ts \| date('Y-m-d')}` |
| `time(fmt)` | Alias for date | |
| `now` | Current timestamp | `{now}` |

### Arithmetic (space required!)

| Function | Description |
|----------|-------------|
| `+ n` | Add |
| `- n` | Subtract |
| `* n` | Multiply |
| `/ n` | Divide |

### Printf-style Format

```
{echo $nbr | %.5f}          # 5 decimal places
{echo $nbr | %'.09d}        # Zero-padded to 9 digits
{echo $nbr | %d}            # Integer
{echo $nbr | %x}            # Hexadecimal
```

### PAD Template Helpers

| Function | Description |
|----------|-------------|
| `open` | Wrap in `{` `}` |
| `close` | Wrap in `{/` `}` |
| `tag` | Alias for open |
| `optional` | Return value or empty |

---

## Options Reference

### Data Flow Options

| Option | Direction | Description |
|--------|-----------|-------------|
| `data` | Input | Get data from source |
| `content` | Input | Get content from store |
| `toData` | Output | Store data to variable |
| `toContent` | Output | Store content to variable |
| `toBool` | Output | Store boolean flag |

### Conditional Options

| Option | Description |
|--------|-------------|
| `bool` | Check/create boolean flag |
| `optional` | Suppress not-found errors |
| `demand` | Mark as required |
| `null` | Alternative for NULL |
| `else` | Alternative for empty/false |
| `notOk` / `error` | Alternative for error |

### Formatting Options

| Option | Description | Example |
|--------|-------------|---------|
| `quote` | Wrap in quotes | `quote="'"` |
| `open` | Prefix on first | `open="["` |
| `close` | Suffix on last | `close="]"` |
| `glue` | Separator | `glue=", "` |
| `tidy` | Clean whitespace | |

### Control Options

| Option | Description |
|--------|-------------|
| `sort` | Sort data |
| `rows` | Limit rows |
| `first` | First N items |
| `last` | Last N items |
| `page` | Pagination |
| `cache` | Cache output |
| `callback` | Run callback |
| `ignore` | Skip PAD processing |
| `noError` | Suppress errors |
| `dump` | Debug output |

### Combined Formatting Example
```
{list quote="'" glue=", " open="[" close="]"}
```
Result: `['item1', 'item2', 'item3']`

---

## Properties Reference

### Iteration State Properties

| Property | Description |
|----------|-------------|
| `first@tag` | Is first iteration |
| `last@tag` | Is last iteration |
| `notFirst@tag` | Is NOT first |
| `notLast@tag` | Is NOT last |
| `border@tag` | Is first OR last |
| `middle@tag` | Is neither first nor last |
| `even@tag` | Is even occurrence |
| `odd@tag` | Is odd occurrence |

### Counter Properties

| Property | Description |
|----------|-------------|
| `current@tag` | Current occurrence (1-based) |
| `count@tag` | Total items |
| `remaining@tag` | Items remaining |
| `done@tag` | Items completed |

### Data Access Properties

| Property | Description |
|----------|-------------|
| `key@tag` | Current array key |
| `keys@tag` | All keys with values |
| `fields@tag` | Field name/value pairs |
| `data@tag` | Full data array |
| `firstFieldName@tag` | First field's name |
| `firstFieldValue@tag` | First field's value |

### Tag Metadata Properties

| Property | Description |
|----------|-------------|
| `name@tag` | Tag name |
| `parameter:x@tag` | Named parameter |
| `parameters@tag` | All parameters |
| `option:n@tag` | Positional option |
| `options@tag` | All options |
| `variable:x@tag` | Level variable |
| `variables@tag` | All variables |

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

// UPDATE - Updates rows
db("UPDATE users SET name='{0}' WHERE id={1}", [$name, $id]);
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

## PAD Select Subsystem

PAD Select allows templates to access database tables directly without writing PHP queries. Define tables and relations in `_lib/select.php`, then use table names as tags.

### Configuration (_lib/select.php)

```php
// Define tables with primary key
$padSelect ['users']         = [ 'key' => 'id' ];
$padSelect ['forum_topics']  = [ 'key' => 'id' ];
$padSelect ['forum_posts']   = [ 'key' => 'id', 'order' => 'created_at' ];

// Define relations (foreign keys)
$padRelations ['forum_topics'] ['users']        = [ 'key' => 'user_id'  ];
$padRelations ['forum_posts']  ['forum_topics'] = [ 'key' => 'topic_id' ];
$padRelations ['forum_posts']  ['users']        = [ 'key' => 'user_id'  ];

// Virtual tables (filtered/sorted views)
$padSelect ['openBugs'] = [
    'base'  => "tickets",
    'where' => "`type`='bug' and `status`='open'",
    'order' => "updated_at desc"
];
```

### Using Table Tags in Templates

```html
<!-- List all users -->
{users}
  {$username} - {$email}
{/users}

<!-- Filter by ID -->
{users $id=5}
  {$username}
{/users}

<!-- Filter by field -->
{forum_boards $slug=$slug}
  {$name}
{/forum_boards}

<!-- With options -->
{news sort="created_at desc" rows=10}
  {$title}
{/news}
```

### Nested Relations (Automatic Joins)

When a table tag is nested inside another, PAD automatically uses the defined relation:

```html
{forum_topics $id=$id}
  <h1>{$title}</h1>

  <!-- Gets the topic's author via user_id relation -->
  {users}
    Posted by {$username}
  {/users}

  <!-- Gets posts for this topic via topic_id relation -->
  {forum_posts}
    <div class="post">
      {$content}
      <!-- Gets the post's author -->
      {users}
        by {$username}
      {/users}
    </div>
  {/forum_posts}
{/forum_topics}
```

### Counting with {count}

```html
{forum_boards}
  {$name}: {count 'forum_topics'} topics
{/forum_boards}
```

### Combining with {field} for Stats

```html
<div class="stats">
  {field "count(*) from users"} members
  {field "count(*) from forum_posts"} posts
</div>
```

### PHP Side - Minimal Code

With PAD Select, PHP files become minimal:

```php
// Before (traditional)
$topic = db("RECORD t.*, u.username FROM forum_topics t
             JOIN users u ON t.user_id = u.id WHERE t.id={0}", [$id]);

// After (PAD Select)
if (!db("CHECK forum_topics WHERE id = {0}", [$id]))
    padRedirect('forum/index');
$title = db("FIELD title FROM forum_topics WHERE id = {0}", [$id]);
// Template handles all the data fetching
```

### Key Benefits

1. **Declarative data access** - Template describes what data it needs
2. **Automatic joins** - Relations handle foreign key lookups
3. **Less PHP code** - No need to build arrays in PHP
4. **Cleaner separation** - PHP handles validation/actions, template handles display

---

## Sequence Subsystem (80+ Mathematical Sequences)

### Basic Usage
```
{fibonacci rows=10}{$fibonacci} {/fibonacci}
{prime rows=15}{$prime} {/prime}
{sequence '1..10', name='n'}{$n} {/sequence}
{random minimal=1, maximal=100, rows=5}
```

### Character/Letter Ranges
```
{sequence 'A..Z', name='letter'}{$letter} {/sequence}
{sequence 'a..e', name='c'}{$c} {/sequence}
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
{pull:nums minimum}{$sequence}{/pull:nums}
{pull:nums maximum}{$sequence}{/pull:nums}
```

### Sequence Types

**Mathematical:**
- `fibonacci` - Fibonacci numbers (1, 1, 2, 3, 5, 8, 13, ...)
- `lucas` - Lucas numbers (2, 1, 3, 4, 7, 11, 18, ...)
- `pell` - Pell numbers (0, 1, 2, 5, 12, 29, ...)
- `tribonacci` - Tribonacci numbers
- `catalan` - Catalan numbers
- `bell` - Bell numbers
- `perrin` - Perrin numbers

**Prime-Related:**
- `prime` - Prime numbers (2, 3, 5, 7, 11, 13, ...)
- `composite` - Composite numbers (4, 6, 8, 9, 10, ...)
- `perfect` - Perfect numbers (6, 28, 496, ...)
- `mersenne` - Mersenne numbers

**Figurate Numbers:**
- `triangular` - Triangular numbers (1, 3, 6, 10, 15, ...)
- `square` - Square numbers (1, 4, 9, 16, 25, ...)
- `cubic` - Cubic numbers (1, 8, 27, 64, 125, ...)
- `pentagonal`, `hexagonal`, `heptagonal`, `octagonal`
- `tetrahedral`, `octahedral`, `biquadratic`

**Filters:**
- `even`, `odd` - Even/odd numbers
- `happy` - Happy numbers
- `lucky` - Lucky numbers
- `harshad` - Harshad/Niven numbers
- `palindrome` - Palindromic numbers
- `semiprime` - Semiprimes
- `powerful`, `polite`, `kaprekar`

**Generation:**
- `range` - Range (e.g., `'1..10'`)
- `list` - Custom list (e.g., `'1;5;3;8'`)
- `loop` - Loop iteration
- `random` - Random numbers
- `repeat` - Repeat a value
- `oeis` - Fetch from Online Encyclopedia of Integer Sequences

### Sequence Actions

**Order Manipulation:**
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

**Multi-Sequence Operations:**
```
{pull:seqA append='seqB'}        # Add seqB to end
{pull:seqA prepend='seqB'}       # Add seqB to start
{pull:seqA merge='seqB'}         # Merge, remove duplicates
{pull:seqA intersection='seqB'}  # Elements in both
{pull:seqA difference='seqB'}    # In seqA but not seqB
```

**Eval Parameter:**
```
{pull:nums eval='* 10 | - 1'}    # Multiply by 10, subtract 1
{pull:nums eval='15 + @'}        # Add 15 to each (@ = current value)
```

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
PAD parses `{ }` as tags. Move CSS/JS to external files:
```html
<link rel="stylesheet" href="style.css">  <!-- Correct -->
<style>body { color: red; }</style>        <!-- WRONG -->
```

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
