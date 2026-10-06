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

A `pagename.html` file is a template exactly like a `.pad`: it pairs with the `.php` and PAD
tags resolve in it, so an existing html file dropped in the application directory is a page.
When both a `.pad` and an `.html` exist, the `.pad` wins.

### URL Structure
Pages are accessed via query string:
- `/myapp/` → `index.pad`
- `/myapp/?about` → `about.pad`
- `/myapp/?admin/users` → `admin/users.pad`

**Important:** Internal links use `?page` format, not `/page` - or `{$padGo}page`, which
follows `$padCleanUrls` (below).

### Clean URLs and dynamic segments

A path below the entry point names a page too, mapped onto the file tree. A file or
directory whose name is a bracketed variable name stands for any one segment and binds it,
before the page's PHP runs; `[name+]` takes the rest of the path:

| URL | File | Variables |
|-----|------|-----------|
| `/shop/products/42` | `products/[id].pad` | `$id = '42'` |
| `/shop/products/new` | `products/new.pad` | a literal name wins over a bracket |
| `/shop/blog/2026/hello-pad` | `blog/[year]/[slug].pad` | `$year`, `$slug` |
| `/shop/docs/a/b/c` | `docs/[path+].pad` | `$path = 'a/b/c'` |
| `/shop/?products/42` | `products/[id].pad` | the same, through the query string |

- Everything that worked keeps working: `?page` URLs, and on a clean URL a query string that
  starts with a bare page name (`/shop/products/42?about`, the relative `href="?about"`)
  names that page instead. `?sort=price` is a value of the page the path names.
- A bracketed file is reached through its route only; `?products/[id]` by its own name is
  not found. `{page 'products/42'}`, `{redirect 'products/42'}` and `padRedirect()` resolve
  the same way.
- `$padCleanUrls = TRUE` makes `$padGo` and `$padGoExt` write `/shop/products/42` instead
  of `/shop/?products/42`; a link written `{$padGo}page&x=1` keeps working in both forms.
  On a clean URL a relative asset link resolves below the path - write it from the root,
  or put `<base href="{$padGo}">` in the wrapper.

The web server has to hand such a path to the application's entry point:

```apache
# Apache: in the application's <Directory> block, or www/shop/.htaccess
# (AllowOverride FileInfo or Indexes) - the URL path of the entry point
FallbackResource /shop/index.php
```

```bash
# PHP's built-in server needs no router: a path that is no file runs the nearest index.php
php -S 127.0.0.1:8000 -t www
```

Without either, `/shop/index.php/products/42` - the path behind the entry point - works on
every server, and `$padCleanUrls` stays off so the links keep the `?page` form.

## Application Structure

```
apps/myapp/
├── index.php              # Home page data
├── index.pad              # Home page template
│
├── _inits.php             # Runs BEFORE all pages (optional)
├── _inits.pad             # Wraps ALL pages - use @page@ placeholder (optional)
├── _exits.php             # Runs AFTER all pages (optional)
├── _exits.pad             # Closing wrapper (optional)
│
├── _lib/                  # Auto-included PHP functions
├── _include/              # Auto-included template snippets
├── _tags/                 # Custom template tags
├── _functions/            # Custom pipe functions
├── _callbacks/            # Data iteration callbacks
├── _options/              # Custom tag options
├── _events/               # Event hooks: error, sql, curl, output
├── _config/               # Application configuration
│   └── config.php
├── _data/                 # Static data files (XML, JSON)
├── _content/              # Markdown collections ({collection 'blog'})
├── _layouts/              # Layouts a page picks with {extends '_layouts/name'} (a convention)
├── _tests/                # The application's own tests, for pad test
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
| `_options/` | Tag options | `{tag name}` → `name.php` on the template; `end/name.php` on the rendered result |
| `_events/` | Event hooks | `error.php`, `sql.php`, `curl.php`, `output.php` - run on every request |
| `_config/` | App config | `config.php` overrides |
| `_data/` | Static data | XML, JSON files |
| `_content/` | Markdown collections | `_content/blog/*.md` are the rows of `{collection 'blog'}` |
| `_mail/` | Email templates | `{mail template='order'}` → `order.pad` + `order.txt` |
| `_tests/` | Application tests | `pad test <app>` - `name.pad` (+ `name.php`) and its answer `name.txt` |

### _lib/ - PHP Functions

Files in `_lib/` are automatically included.

**_lib/helpers.php**:
```php
<?php
  function formatDate ( $date ) {
    return date ( 'F j, Y', strtotime ( $date ) );
  }
?>
```

Use in any `.php` file:
```php
$formatted = formatDate ( '2025-01-15' );
```

### _include/ - Template Snippets

Templates in `_include/` become available as tags.

**_include/card.pad**:
```html
<div class="card">
  @content@
</div>
```

Use in templates:
```
{card}
  <h3>Title</h3>
  <p>Content here</p>
{/card}
```

### _tags/ - Custom Tags

Create custom template tags.

**_tags/button.php**:
```php
<?php
  $label = padTagParm ( 'label', 'Click' );
  $href  = padTagParm ( 'href', '#' );
  $padContent = "<a href=\"$href\" class=\"button\">$label</a>";
  return TRUE;
?>
```

Use in templates:
```
{button label="Submit", href="?submit"}
```

A tag can be a template instead - `_tags/card.pad` - with named slots for the caller to fill
and declared parameters:

```html
{parms title, tone='info'}
<div class="card {#tone}">
  <h2>{#title}</h2>
  @content@
  {slot 'footer'}<footer>@content@</footer>{/slot}
</div>
```

```
{card title="Revenue"}
  <strong>{$revenue}</strong>
  {slot 'footer'}<a href="?reports">Report</a>{/slot}
{/card}
```

### _functions/ - Pipe Functions

Create custom pipe functions.

**_functions/money.php**:
```php
<?php
  return '$' . number_format ( $padContent, 2 );
?>
```

Use in templates:
```
{echo $price | money}
```

### Layouts - {extends} and {block}

A page can pick its frame instead of taking its directories' `_inits.pad`/`_exits.pad`:

**_layouts/report.pad**:
```html
<h1>{block 'title'}Report{/block}</h1>
@page@
```

**sales.pad**:
```
{extends '_layouts/report'}
{block 'title'}Sales{/block}
<p>Revenue ...</p>
```

`{parent}` inside a block is the content it overrides. Without `{extends}`, a page's
`{block 'title'}...{/block}` overrides the region of that name in the directory wrappers.

A page without PHP sets its wrapper's `$title` - and its layout and cache time - with
`{meta title='Sales', layout='_layouts/report', cache=600}`.

### _callbacks/ - Iteration Callbacks

Process data during iteration.

**_callbacks/totals.php**:
```php
<?php
  switch ( $padCallback ) {
    case 'init':
      $total = 0;
      break;
    case 'row':
      $total += $amount;
      break;
    case 'exit':
      // $total now contains sum
      break;
  }
?>
```

Use in templates:
```
{items callback="totals"}
  {$name}: {$amount}
{/items}
Total: {$total}
```

### _options/ - Tag Options

A file `_options/name.php` makes `name` an option of every tag. It runs before the tag
renders and works on its template; a file `_options/end/name.php` runs after it rendered and
works on the result - the fields filled in, every occurrence joined. Both change
`$padContent` and read the option's value from `$padGetName` (TRUE for the bare form).

**_options/end/words.php**:
```php
<?php
  $padContent .= '(' . str_word_count ( strip_tags ( $padContent ) ) . ' words)';
?>
```

`{staff words}{$name} {/staff}` → `joe jim john jack jerry (5 words)`. The end options run
before the built-in `toContent`, `toData` and `tidy`, and before the closing tag's pipe.

### _events/ - Event Hooks

A file in `_events/` runs whenever a request reaches that moment, on every request - the
engine's own hooks in `pad/events/` serve the info modes and run only under `$padInfo`. The
lookup is the one `_callbacks/` uses: the page's directory first, then up to the root, the
first file found wins.

| File | Runs when | Variables |
|------|-----------|-----------|
| `error.php` | an error is raised, before the error action deals with it | `$error`, `$file`, `$line` |
| `sql.php` | `db()` ran a statement (not `padDb()` on PAD's own database) | `$sql` as sent, `$input`, `$vars`, `$result`, `$rows`, `$ms` |
| `curl.php` | a remote fetch finished, a failed one too (not a `_data/` file) | `$url`, `$result` (999 on a failure), `$error`, `$ms` |
| `output.php` | the page is about to be sent - after tidy, before the ETag and the page cache | `$output` - change it to change the page |

A hook runs in a function scope of its own: the event's values are its local variables, and
the page's are reached with `global` or `$GLOBALS`. What it echoes is discarded. A hook is not
re-entered (a query inside the sql hook does not call it again), and a PHP error inside the
error hook is logged and set aside, so the error it was told about is still the one reported.

**_events/sql.php** - log the slow queries:
```php
<?php
  if ( $ms > 100 )
    error_log ( sprintf ( 'slow query %.1f ms: %s', $ms, $sql ) );
?>
```

**_events/output.php** - post-process the final HTML:
```php
<?php
  $output = str_replace ( '</body>', '<!-- served by PAD --></body>', $output );
?>
```

### _tests/ - Application Tests

A test is a page in `_tests/` next to its answer, in the forms the framework's own suites
use: the exact output, a `/regex/` over it, or `HTTP 500` with an optional `/regex/` on the
next line. No URL reaches `_tests/`; `pad test` renders each test page bare, on the command
line, with every `{assert}` checked.

**_tests/cart.php** and **_tests/cart.pad**:
```php
<?php
  include APP . 'cart.php';     // the page's own data
?>
```
```
{assert $total eq 42, 'the cart total'}
<p>Total {$total}</p>
```

**_tests/cart.txt** - what `pad test shop --record` writes from the first run:
```
<p>Total 42</p>
```

`{assert}` is silent outside a test run - nothing evaluated, nothing shown - so it can stay
in the application's own pages too. `./ci.sh` runs the tests of every application.

## Running PAD

### Web Server
PAD runs through Apache or similar. Entry points are in `www/`.

**Entry point pattern** (`www/myapp/index.php`):
```php
<?php
  include __DIR__ . '/../pad.php';
?>
```

`www/pad.php` detects the OS, derives the app name and the URL mount prefix (`$padRoot`) from the entry script's `SCRIPT_NAME`, and includes `pad/pad.php` (which defines `APP`, `DATA` and the other constants). The `www/` tree can be served at the domain root or mounted under a prefix; generated cross-app links use `$padPath` and follow the mount automatically.

## Creating a New Application

### Quick Start

**1. Create the Application Directory:**
```bash
mkdir -p apps/myapp
```

**2. Create the Entry Point** (`www/myapp/index.php`):
```php
<?php
  include __DIR__ . '/../pad.php';
?>
```

**3. Create the Index Page:**

`apps/myapp/index.php` (returns data):
```php
<?php
  $title = 'My App';
  $message = 'Hello World!';
?>
```

`apps/myapp/index.pad` (template):
```
<h1>{$title}</h1>
<p>{$message}</p>
```

**4. Access Your Application:**
Visit `http://yourserver/myapp/` in your browser.

### Minimal Example (`apps/hello/`):
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
{$items.0}                     # Array element by key - a dotted path, as above
```

**Output escaping options:**
```
{$text}                        # Escaped by the sanitize chain (the default)
{!text}                        # Raw output - the sanitize chain is skipped
{$text | html}                 # HTML escaped via pipe
{$text | url}                  # URL encoded
```

### Pipe Functions
A field tag pipes as it stands; a literal or an expression goes through `{echo}`.
```
{$name | upper}                     # a field tag pipes
{echo $name | upper}                # the same value, printed raw - {echo} skips the sanitize chain
{echo $text | trim | lower}         # chain multiple
{echo $date | date('Y-m-d')}        # with parameters
{echo 'hello' | upper}              # a literal needs {echo}
```

**Common String Functions:**
```
{echo $text | upper}                # Uppercase
{echo $text | lower}                # Lowercase
{echo $text | trim}                 # Remove whitespace
{echo $text | capitalize}           # Capitalize each word
{echo $text | bold}                 # Wrap in <b> tags
{echo $text | html}                 # HTML-encode
{echo $text | left(5)}              # First 5 characters
{echo $text | truncate(100)}        # At most 100 chars, ending on a word, with …
{echo $text | after('@')}           # Everything after first @
{echo $text | before('.')}          # Everything before first .
{echo $text | after('(') | before(')')}  # Extract between delimiters
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
{echo $value | number_format(@, 2)} # 1,234.50
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
Arithmetic pipes require a space between the operator and operand:
```
{echo $value | + 1}          # Correct - adds 1
{echo $value | +1}           # Wrong - no space
{echo $value | * 2}          # Correct - multiplies by 2
{$value | + 1}               # Correct - a field tag pipes too
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
A condition compares with the operators (`eq`, `ne`, `gt`, `lt`, `ge`, `le`, `==`, `!=`, etc.),
or tests a bare value for its truth, as PHP tests it - `''`, `'0'`, `0` and FALSE fail:
```
{if $count eq 0}Empty{/if}              # A comparison
{if {clock 'L'} eq 1}Leap year{/if}     # A nested tag as value
{if $flag}True{/if}                      # The value's truth
```

### Iteration Properties
Properties use the `property@tag` syntax to access iteration state. A boolean property is
written as a tag pair - its content renders only when the property is true - in a ternary,
or in a condition: `{if first@items}` reads the property as a value, alone or with any
operator:
```
{items}
  {first@items}First item{/first@items}
  {last@items}Last item{/last@items}
  {even@items ? 'Even row' : 'Odd row'}
  Count: {count@items}
  Index: {current@items}
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

### Tags with Options
```
{tagname option="value"}
{items $var=5}                     # a level variable for the tag's content
{items sort="name", rows=10}       # options are separated by commas
```

### Variable Assignment with {set}
```
{set $name = 'Alice'}              # Assign string
{set $count = 0}                   # Assign number
{set $total = $price * $qty}       # Assign expression
{set $upper = {echo $upper | upper}}   # Assign through a pipe
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
{365 - {clock 'z'}}                      # Wrong - an error: {365 ...} is read as a tag named 365
```

### Calling PHP Functions
PHP functions can be called directly as tags without custom wrappers:
```
{date_default_timezone_get}             # Calls PHP function directly
{php:time}                               # Unix timestamp - a bare {time} is the pipe function
{rand 1, 100}                            # Random number between 1-100
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

### Markdown Collections
A folder of Markdown files under `_content/` is a data source - a small flat-file CMS. Each
file is a row: the keys of its front matter become fields, plus `slug` (the file name without
`.md`), `body` (the Markdown written as HTML) and `source` (the Markdown as it was).

**File** (`_content/blog/hello.md`):
```
---
title: Hello PAD
date: 2026-10-05
---
First post ...
```

**Templates** - the list, and the page of one post (`?blog/post&slug=hello`):
```
{collection 'blog', sort='date DESC', first=10}
  <h2><a href="?blog/post&slug={$slug}">{$title}</a></h2>
{/collection}

{collection 'blog', slug=$slug}
  <h1>{$title}</h1>
  {!body}
@else@
  No such post.
{/collection}
```

The folder is looked up like a `_data` file: the page's directory first, then each parent,
then `_common`. Raw HTML in a body is escaped unless the tag has the `html` option. The front
matter is read with the PHP yaml extension when it is loaded, otherwise with a built-in reader
for flat `key: value` lines, `[a, b]` lists and `- item` lists.

---

## Designer Preview with Sample Data

A designer can work on `orders.pad` without the database or a login: `?orders&padSample`
renders the template with the variables of `_samples/orders.json` (in the page's own
directory) instead of running any PHP - not `orders.php`, nor the `_inits.php` and
`_exits.php` around it.

```json
{
  "title": "Orders",
  "orders": [ { "number": 10100, "customer": "Atelier graphique" } ]
}
```

- **Capture** the variables of a real render: `?orders&padSample=capture` writes them to
  `DATA/samples/<app>/orders.json` (read by the preview as well), and
  `apps/cli/pad sample <app> orders` writes them into the application's `_samples/`.
- **Database tags** in the template still ask the database, unless they have a name the
  sample holds: `{array "* from orders", name='orders'}` reads the sample's `orders`, and a
  capture records the database's answer under that name.
- **Who may ask** is `$padSample`: `'local'` (the default) a request this machine makes to
  itself, `TRUE` everyone - only on a design server without real data, since the preview
  skips the PHP's access checks - `FALSE` no one. A capture is always local-only.
- An engine name (`pad*`, `pq*`, `_*`) in a sample is ignored; a page asked for with
  `padSample` that has no sample is an error under the strict check. The page cache stays
  out of both modes.

## Form Handling

### Automatic Form Variables
PAD makes request values - POST and GET - available as PHP variables matching the field name,
as `$padRequestVars` allows (TRUE, the default: all of them; a list: those names; []: none):
```html
<form method="post">
  <input name="username">    <!-- Available as $username -->
  <input name="email">       <!-- Available as $email -->
</form>
```

**Important:**
- A GET value fills the variable as a POST value does - check the method when it matters:
  `padRequestIs ( 'post' )`, or `padPosted ( 'contact' )` for a `{form}`
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

### Form Fields and Validation
`{form}`, `{input}` and `{textarea}` write fields that refill from the post and show the
message `padValidate` found for them:

```html
{form 'contact'}
  {input 'email', type='email', label='E-mail', required}
  {textarea 'message', label='Message', rows=6}
  <button>Send</button>
{/form}
```

```php
<?php
  if ( padPosted ( 'contact' ) ) {
    $errors = padValidate ( [ 'email'   => 'required|email',
                              'message' => 'required|max:2000' ] );
    if ( ! $errors ) {
      // store it ...
      padRedirect ( 'contact', [ 'sent' => 1 ] );
    }
  }
?>
```

`padValidate($rules, $data = posted, $messages = [])` answers one message per failing field;
the rules are `required`, `email`, `url`, `numeric`, `integer`, `min:n`, `max:n`,
`in:a,b,c`, `regex:/.../`, `same:field`, `accepted`, `date`. An empty field is checked for
`required` only. The field shows the message in the words of its own label, with
`aria-invalid` and `aria-describedby`.

The rules can stand on the fields in the template instead, and the PHP then only acts:

```html
{form 'contact', error='Please correct the errors below.'}
  {input 'email', type='email', label='E-mail', rules='required|email'}
  {textarea 'message', label='Message', rows=6, rules='required|max:2000'}
  <button>Send</button>
{/form}
```

```php
<?php
  if ( padPosted ( 'contact' ) ) {
    // store it ...
    padRedirect ( 'contact', [ 'sent' => 1 ] );
  }
?>
```

The rules are read from the page's template before any PHP runs and a post of the form is
checked against them there, so `padPosted('contact')` is TRUE only for a post that kept them;
one that broke them renders the form again, refilled, with the messages and the `error=`
banner, and `padFormFailed('contact')` is TRUE. Only literal rules in the page's own template
count - rules in a snippet, a custom tag, a `{page}` or a layout are an error, as nothing would
check them; a field that is there only sometimes keeps its check in the PHP, where
`padValidate` adds its messages to the template's.

### File Uploads
`padUpload` takes the file of one field and stores it safely - its real type read from its
content with `finfo` and held against the allowed types (`image/*` for any image), the size
limit enforced, and the file stored under a random name in `DATA/uploads/`:

```php
$avatar = padUpload ( 'avatar', types: [ 'image/png', 'image/jpeg' ], max: '2M' );

if ( $avatar )                       // [ name, file, path, size, type, extension ]
  db ( "UPDATE users SET avatar='{0}' WHERE id={1}", [ $avatar ['file'], $id ] );
elseif ( $avatar === FALSE )         // refused - NULL means no file was sent
  $problem = padUploadError ( 'avatar' );
```

The refusal also shows beside `{input 'avatar', type='file', label='Picture'}`, and a
`{form}` holding a file field posts as `multipart/form-data` by itself.

### Flash Messages
After a valid post, say what happened on the page the redirect leads to:

```php
padFlash ( 'Thanks, your message was sent.' );      // type 'info'; padFlash ( $text, 'error' )
padRedirect ( 'contact' );
```

```html
{flash}<p class="notice {$type}">{$message}</p>{/flash}
```

A message lives exactly one request after the one that flashed it - the redirect's
destination - and each is shown once.

### CSRF Protection
With `$padCsrf = TRUE` in `_config/config.php`, every `<form method="post">` that posts back
to the site gets a hidden `padCsrfToken` field holding the token of the visitor's session,
and every POST (PUT, PATCH, DELETE) that does not bring it back - in that field or an
`X-CSRF-Token` header - is answered `403` before the application runs. `{csrf}` writes the
field by hand, `{csrf token}` the bare token; `padCsrfValid()` checks a post in PHP. The
session starts on demand the first time a token is needed.

---

## Database Operations

### The `db()` Wrapper
PAD provides a `db()` function for database queries. It uses positional placeholders `{0}`, `{1}`, etc.

**Placeholders quote themselves:** written bare, a string value becomes a quoted, escaped
literal and a number stays a number; written inside quotes, the value is only escaped. Both
forms are safe:
```php
// Inside quotes - the value is escaped
db("SELECT * FROM users WHERE username='{0}'", [$username]);
db("INSERT INTO posts (title, content) VALUES ('{0}', '{1}')", [$title, $content]);

// Bare - quoted and escaped for you
db("SELECT * FROM users WHERE username={0}", [$username]);

// A number stays a number - and '5 or 1=1' becomes a quoted string, never SQL
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

{array "* from users order by name"}
  <tr><td>{$name}</td><td>{$email}</td></tr>
{/array}
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
  body {color: red}  <!-- PAD tries to parse {color: red} as a tag! -->
</style>
```

### Select Subsystem vs Direct SQL
PAD has two approaches for database access:

1. **Direct SQL with `db()`** - Write your own queries
2. **Select Subsystem** - Declare `$padSelect` tables and let PAD handle queries

**Do NOT mix them.** If using direct SQL queries, do NOT declare `$padSelect` or `$padRelations`:
```php
// If using db() for all queries, DON'T add these:
// $padSelect['users'] = ['key' => 'id'];
// $padRelations['posts']['users'] = ['key' => 'user_id'];
```

Declaring `$padSelect` activates the select subsystem (`pad/lib/select.php`, type handler `pad/types/select.php`) which can conflict with direct SQL and cause infinite loops. See [DATABASE.md](DATABASE.md) for the full Select reference.

### Pipe Functions
PAD pipe functions come from two sources:
1. Custom functions in `pad/functions/` (trim, upper, date, html, etc.)
2. Standard PHP functions called directly (strlen, count, etc.)

Both pipe from a field tag or through `{echo}`:
```
{echo $text | trim}              # PAD function
{echo $text | strlen}            # PHP function
{echo $name | ucfirst}           # PHP function
{$text | trim}                   # a field tag pipes as it stands
```

---

## Important Tag Behaviors

### Parameter Evaluation
Parameters are evaluated before being passed to tags. Use quotes to pass literal strings:
```
{count items}              # Wrong - items is read as an option, an error
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
| `select:` | Declared select table |
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
Creates a named boolean condition usable as a tag - `{else}` belongs to `{if}` and `{case}`
only, every other tag splits on `@else@`:
```
{bool 'isActive'}1{/bool}      # Define the boolean

{isActive}                      # Use as a tag (NOT {$isActive})
  <p>Active!</p>
@else@
  <p>Inactive</p>
{/isActive}
```

### The `exists` Tag
Block tag for file existence checks (not nested in `{if}`):
```
{exists APP . 'path/to/file.pad'}
  File exists
@else@
  File not found
{/exists}
```

### The `count` Tag
Checks if an array has elements. Quote the array name:
```
{count 'items'}
  Array has elements
@else@
  Array is empty
{/count}
```

### The `output` Tag
Sets output type, does NOT capture content:
```
{output 'web'}        # Normal web output (default)
{output 'console'}    # Console output
{output 'download'}   # File download
{output 'json'}       # The variables the page names in $padExpose, as JSON
{output 'csv'}        # The first list the page names in $padExpose, as CSV
```

### The `true` and `false` Tags
Literal boolean conditions for always/never showing content:
```
{true}This is always shown{/true}
{false}This is never shown{/false}
```

### The `code` Tag
Render its content as PAD in a pass of its own - it runs no PHP: PHP written inside is
printed as text. PHP belongs in the page's `.php` or in `_lib/`.
```
{code}
  {set $total = $price * $qty}{$total}
{/code}

{code sandbox}
  {-- an isolated pass: the page's variables and stores are not seen or changed --}
{/code}

{echo $snippet | code}      {-- a stored value run as PAD --}
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

## Global Wrapper (_inits.pad)

Wrap all pages with a common layout:

**_inits.pad**:
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

  @page@

  <footer>&copy; 2025 My App</footer>
</body>
</html>
```

The `@page@` placeholder is replaced with each page's content.

---

## Global Setup (_inits.php)

Run PHP code before all pages:

**_inits.php**:
```php
<?php
  // Default title
  $title = ucfirst ( $padPage );

  // Check authentication
  session_start ();
  $loggedIn = isset ( $_SESSION ['user'] );
?>
```

---

## Configuration (_config/config.php)

Override framework settings. The file is read more than once per request - once to choose
`$padCommon`, the output type and the info modes, and again after those selectors ran, so
the application has the last word - so it only assigns settings: a function declared in it
is declared twice, and anything it does it does twice. Functions belong in `_lib/`.

```php
<?php
  // Database connection
  $padSqlHost     = 'localhost';
  $padSqlDatabase = 'myapp';
  $padSqlUser     = 'myuser';
  $padSqlPassword = 'mypass';

  // Error handling: pad, boot, php, stop, exit, ignore, log, dump
  $padErrorAction = 'pad';

  // Debug mode: trace, stats, track, xml, xref
  // $padInfo = 'trace';

  // Values are text: a field holding {php:getcwd} prints that text. FALSE re-reads
  // every value as template source; {echo $snippet | code} runs one value on purpose.
  $padProtectValues = TRUE;

  // The PHP functions a template may call: TRUE all, a list only those, [] none
  $padPhpFunctions = [ 'ucfirst', 'number_format' ];

  // The request values promoted to variables: TRUE all, a list only those, [] none
  $padRequestVars = [ 'name', 'email', 'message' ];

  // Every POST must carry the session's CSRF token, added to each POST form
  $padCsrf = TRUE;

  // The key padEncrypt and padSignedUrl seal and sign with: 32 bytes or 'base64:...';
  // empty is the key file DATA/keys/<application>.key, made on first use
  $padAppKey = 'base64:...';

  // Security headers (these two are the default) and the Content-Security-Policy;
  // 'nonce' is this request's nonce, which {nonce} writes: <script nonce="{nonce}">
  $padSecurityHeaders = [ 'X-Content-Type-Options' => 'nosniff',
                          'Referrer-Policy'        => 'strict-origin-when-cross-origin' ];
  $padCsp = "default-src 'self'; script-src 'self' 'nonce'; frame-ancestors 'self'";

  // Links in the clean form, /myapp/products/42, for a server that routes paths
  $padCleanUrls = FALSE;

  // ?sitemap.xml generated from the file tree, ?robots.txt pointing to it
  $padSitemap     = TRUE;
  $padSitemapSkip = [ 'admin', 'login' ];

  // Template emails: 'file' (DATA/mail/), 'mail' (PHP mail()) or a function in _lib/
  $padMailTransport = 'mail';
  $padMailFrom      = 'Shop <shop@example.com>';
?>
```

---

## Template emails

A mail is a PAD template rendered to an HTML and a text part. Templates live in `_mail/`,
looked up like `_include/`: `_mail/order.pad` (HTML), `_mail/order.txt` (text - made from the
HTML when there is none), `_mail/order.php` (runs first), and `_inits.pad`/`_exits.pad` (and
`.txt`) in the same directory as the email's layout around `@page@`.

```html
{mail to=$email, template='order', subject='Order confirmation'}

{orders}
  {mail to=$email, subject='Your order ' . $number}<p>Thank you, {$customer}</p>{/mail}
{/orders}
```

```php
padMail ( $email, 'order', 'Order confirmation', [ 'order' => $order ],
          [ 'cc' => 'sales@example.com', 'replyTo' => 'help@example.com' ] );
```

`$padMailTransport = 'file'` (the default) writes each message as an `.eml` under
`DATA/mail/<app>/` instead of sending it - development needs no mail server - and keeps the
newest `$padMailKeep`; `'mail'` sends through PHP's `mail()`; the name of a function in `_lib/`
gets the message array (`to`, `cc`, `bcc`, `from`, `replyTo`, `subject`, `html`, `text`,
`headers`, `raw`) and returns whether it went - the place for SMTP through a library.
`$padMailFrom` is the sender.

---

## Sitemap

Every page is a file, so the sitemap is generated: with `$padSitemap = TRUE` the application
answers `?sitemap.xml` (`/myapp/sitemap.xml` with clean URLs) with every page and the newest
time of its files as `lastmod`, and `?robots.txt` with a `Sitemap:` line pointing to it - unless
the application has a page of that name itself. `{sitemap}...{/sitemap}` gives the same rows
(`page`, `url`, `lastmod`) to a template, for an HTML sitemap. Left out are `_` entries,
directories holding a `_guard.php`, bracketed routes, action pages (no template, PHP that only
redirects, restarts or writes), pages whose template says `{meta sitemap=false}`, and what
`$padSitemapSkip` names.

---

## Data Storage

Use the `DATA` directory for writable data:

```php
<?php
  $dataFile = DATA . 'myapp/data.json';

  // Ensure directory exists
  if ( ! is_dir ( DATA . 'myapp' ) )
    mkdir ( DATA . 'myapp', 0755, TRUE );

  // Read
  $data = json_decode ( file_get_contents ( $dataFile ), TRUE );

  // Write
  file_put_contents ( $dataFile, json_encode ( $data ) );
?>
```

---

## Subdirectories

Create sections with their own wrappers:

```
apps/myapp/
└── admin/
    ├── _guard.php      # Admin access check - FALSE answers 403
    ├── _inits.pad      # Admin section wrapper
    ├── index.pad
    ├── users.pad
    └── settings.pad
```

Access via `?admin/users`, `?admin/settings`, etc.

Each `_inits.pad` wraps content from its directory and below.

### Directory Guards

A `_guard.php` decides for every page in its directory and below whether the request may
see it, before any `_inits.php` or page PHP runs:

```php
<?php
  // apps/shop/admin/_guard.php
  if ( ! $session_user )
    padRedirect ( 'login' );

  return $session_role == 'admin';   // FALSE → 403
?>
```

- `FALSE`, or any value that is not true (`NULL`, `0`, `''`), refuses the page with
  `403 Forbidden`; `TRUE` or no return at all lets the request through.
- The guards run root first, one per directory level, after the `_lib` files and in the
  request's own scope: the session and request variables and `$padPage` are there.
- A page included with `{page}` is guarded too; a refusal renders it as nothing.
- A guarded page is never answered from the page cache, nor stored in it.

---

## Tips

1. **Start simple** - Begin with just `index.php` and `index.pad`
2. **Add wrapper later** - Create `_inits.pad` when you need common layout
3. **Use DATA for storage** - Never write to APP directory
4. **Check existing apps** - Look at `apps/pad/` for examples
5. **URL format** - Use `?page` or `{$padGo}page` for internal links (see Clean URLs)

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
  <div style="background: {even@items ? '#e0e0e0' : '#f0f0f0'}">
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
  {first@items}<table border="1">{/first@items}
  <tr><td>{$name}</td></tr>
  {last@items}</table>{/last@items}
{/items}
```

### Comma-Separated List
```
{items}{notFirst@items}, {/notFirst@items}{$name}{/items}
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
| `{$var}` | Output variable, HTML-escaped (the sanitize chain) | `{$name}` |
| `{!var}` | Raw output - trusted HTML only | `{!body}` |
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
| `{property@tag}` | Iteration property | `{first@items}`, `{count@users}` |
| `{sequence}` | Generate sequence | `{sequence '1..10', name='n'}` (see [sequences/](sequences/README.md)) |
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
| `left(n)`, `truncate(n)` | Shorten |
| `cut('x')` | Remove every occurrence of x |
| `after('x')`, `before('x')` | Extract substring |
| `contains('x')` | Check substring |
| `. 'str'` | Concatenate |

### Key Distinctions from PHP

1. **Templates drive execution** - not code including templates
2. **A literal pipes through `{echo}`** - `{echo 'x' | upper}`; a field tag pipes as it stands, `{$var | upper}`, and only the field tag escapes
3. **Arithmetic needs space** - `{echo $x | + 1}` not `| +1`
4. **A bare condition tests truth** - `{if $flag}` fails for `''`, `'0'`, `0` and FALSE; compare when a value is meant
5. **Quote literal strings** - `{count 'items'}` not `{count items}`
6. **`{continue}` skips iterations** - like PHP; use `{resume}` to transform stored sequences
7. **Use type prefixes** - `{pull:seq}`, `{data:items}`, `{php:func}` to disambiguate
8. **`$` vs `%` variables** - `$var` is level (constant), `%var` is occurrence (per-iteration)
9. **`@` placeholder** - represents current value in pipes: `{echo 5 | @ * 2}`
10. **Multiple data formats** - JSON, XML, YAML, CSV all supported in `{data}` blocks
