# CLAUDE.md - PAD Framework Reference

This file provides guidance for working with the PAD (PHP Application Driver) framework: the
template language, the application structure, the engine and how to test it. The features
beyond that core - emails, queues, charts, layouts, caching, authentication ... - are indexed
under [Features](#features) and described in [docs/FEATURES.md](docs/FEATURES.md).

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
├── pad/      # PAD framework core (template engine, tag processors, expression evaluator);
│             #   pad/subs/ the directory-driven subsystems - charts, games, toys, widgets, icons ...
├── apps/     # PAD applications (each subdirectory is a self-contained app)
├── www/      # Web server entry points (PHP entry points for each app); www/_common/ the frame's CSS and JS;
│             #   www/assets/<sub>/ the subsystems' style sheets and scripts, linked with a ?v= hash
├── editors/  # Editor kits, the language server, the command-line renderer
├── docs/     # Documentation
├── wasm/     # PAD in the browser: wasm/build.php bundles the engine for www/wasm/ (PHP compiled to WebAssembly);
│             #   wasm/app.mjs (pad pack) packs applications to run offline - from file://, or installed over https
└── DATA/     # Runtime data (logs, cache, dumps) - writable, excluded from git
```

## Page Pairing

Every page consists of two files that PAD automatically pairs:
- `pagename.php` - Returns data (variables, arrays)
- `pagename.pad` - Template that renders the data

A `pagename.html` file is a template exactly like a `.pad` - it pairs with the `.php`, and
PAD tags and variables resolve in it - so an existing html file dropped in the application
directory is a page. When both a `.pad` and an `.html` exist, the `.pad` wins.

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

Pages are accessed via query string:
- `/myapp/` → `index.pad`
- `/myapp/?about` → `about.pad`
- `/myapp/?admin/users` → `admin/users.pad`

### Clean URLs and dynamic segments

A path below the entry point names a page as well (`pad/lib/route.php`), and a bracketed
file or directory name binds a segment as a variable before the page's PHP runs:

- `/myapp/products/42` → `products/[id].pad` with `$id = '42'` (a literal `products/new.pad`
  wins over the bracket); `blog/[year]/[slug].pad`; `docs/[path+].pad` takes the rest.
- `?products/42` resolves the same way, so it works on a server that routes no paths, and
  `/myapp/index.php/products/42` works on every server. A bracketed file is never reached
  by its own name. `{page}` and `{redirect}` resolve routes too; `padRedirect()` with no
  page goes back to the name the page was asked by.
- On a clean URL a query string starting with a bare page name (`?about`) names that page;
  `?sort=x` is a value of the path's page.
- `$padCleanUrls = TRUE` makes `$padGo`/`$padGoExt` write `/myapp/products/42`; a link
  written `{$padGo}page&x=1` works in both forms. Apache needs
  `FallbackResource /myapp/index.php`; `php -S` needs no router.

---

## Application Structure

```
apps/myapp/
├── index.php              # Home page data
├── index.pad              # Home page template
│
├── .env                   # Environment values padEnv reads - database settings, keys (optional)
├── _guard.php             # Decides access to every page below - FALSE is 403 (optional)
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
├── _data/                 # Data files (XML, JSON, YAML, CSV) and named .sql queries
├── _lang/                 # Translation catalogs: en.json, nl.json ... ({trans 'key'/})
├── _content/              # Markdown collections: _content/blog/*.md ({collection 'blog'})
├── _errors/               # Error pages: 404.pad, 403.pad, 5xx.pad ... (optional)
├── _migrations/           # Schema changes, run by pad migrate
├── _seeds/                # Seeders, run by pad seed
├── _jobs/                 # Queue job handlers: padQueue ( 'name', $data ) → name.php
├── _schedule.php          # The scheduled entries pad schedule runs (optional)
├── _health.php            # Extra checks of ?up (optional)
│
└── subdir/                # Subdirectories can have own wrappers
    ├── _callbacks/        # Subdirectory callbacks
    ├── _functions/        # Subdirectory functions
    ├── _include/          # Subdirectory includes
    ├── _lib/              # Subdirectory lib
    ├── _options/          # Subdirectory options
    ├── _tags/             # Subdirectory tags
    ├── _guard.php         # Subdirectory guard (runs after the parent's)
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
| `_functions/` | Pipe functions | `{echo $x \| myfunc/}` → `myfunc.php` |
| `_callbacks/` | Iteration hooks | `callback='name'` → `name.php` |
| `_options/` | Tag options | `{tag name}` → `name.php` on the template; `end/name.php` on the rendered result |
| `_events/` | Event hooks | `error.php`, `sql.php`, `curl.php`, `output.php` - run on every request |
| `_config/` | App config | `config.php` overrides |
| `_data/` | Static data, named queries | XML, JSON, YAML, CSV; `name.sql` runs as `{name}` |
| `_scripts/` | Shell scripts | On demand |
| `_mail/` | Email templates | `{mail template='order'}` → `order.pad` + `order.txt` |
| `_tests/` | Application tests | `pad test <app>` - `name.pad` + `name.txt` answer |
| `_lang/` | Translation catalogs | `nl.json` holds the keys `{trans 'key'/}` looks up in locale `nl` |
| `_content/` | Markdown collections | `_content/blog/*.md` with front matter are the rows of `{collection 'blog'}` |
| `_errors/` | Error pages | `404.pad`, `403.pad`, `503.pad`, `4xx.pad`, `5xx.pad` answer in place of the plain line |
| `_migrations/` | Schema changes | `pad migrate <app>` - `.sql` (+ `.down.sql`) or `.php` with up and down |
| `_seeds/` | Seeders | `pad seed <app>` - plain PHP with `db ()` and `padFactory` |
| `_jobs/` | Job handlers | `padQueue ( 'name', $data )` → `name.php`, run by `pad work <app>` |

### Wrapper Files (_inits.pad / _exits.pad)

These files wrap page content at each directory level, creating nested wrappers:

```
/_inits.pad        ← Root wrapper (top)
  /abc/_inits.pad  ← Subdirectory wrapper (top)
    [page content]
  /abc/_exits.pad  ← Subdirectory wrapper (bottom)
/_exits.pad        ← Root wrapper (bottom)
```

### Directory Guards (_guard.php)

A `_guard.php` decides for every page below its directory, before any `_inits.php` runs:
access control follows the file tree. Returning `FALSE` (or anything not true) answers
`403 Forbidden`; `TRUE` or no return lets the request through. Guards run root first, after
`_lib`, in the request's scope (session and request variables, `$padPage`); one can
`padRedirect('login')`. A `{page}` including a refused page renders it as nothing, and a
guarded page never comes from or goes into the page cache.

```php
// apps/shop/admin/_guard.php - session_user and session_role listed in $padSessionVars:
// a guard runs before _inits.php, so a variable set there is not there yet
if ( ! $session_user )
  padRedirect ( 'login' );

return $session_role == 'admin';
```

### PHP Execution Order (_inits.php / _exits.php)

PHP files execute in a specific order - all PHP runs before template rendering (each
directory's `_guard.php`, root first, runs before all of them):

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
  return '<a href="' . htmlspecialchars($href) . '" class="button">' . htmlspecialchars($label) . '</a>';
?>
```

Use in templates: `{button label="Submit", href="?submit"}`

What a tag returns is a value: its braces stay text, as every value's do. `$padContent` is
template source instead - the content the level goes on to process - so text built from a
parameter belongs in the return value. `padTagParm()` hands an application tag its
parameter with `{ }` and `|` as inert stand-ins (quotes stay real, for `htmlspecialchars`),
so a visitor's `{php:...}` echoed or put in `$padContent` prints rather than runs.

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

Use in templates: `{json 'products' | ignore/}` - Outputs JSON from `_data/products.json`

### _functions/ - Pipe Functions

**_functions/money.php**:
```php
<?php
  return '$' . number_format($padContent, 2);
?>
```

Use in templates: `{echo $price | money/}`

### _events/ - Event Hooks

A file in `_events/` runs whenever a request reaches that moment - always, not only under
`$padInfo` like the engine's own `pad/events/`. Looked up like `_callbacks/` (page directory
up to the root). The event's values are the hook's local variables; page variables through
`$GLOBALS`; echo is discarded.

| File | Runs when | Variables |
|------|-----------|-----------|
| `error.php` | an error is raised, under every error action | `$error`, `$file`, `$line` |
| `sql.php` | `db()` ran a statement | `$sql`, `$input`, `$vars`, `$result`, `$rows`, `$ms` |
| `curl.php` | a remote fetch finished (also a failed one) | `$url`, `$result`, `$error`, `$ms` |
| `output.php` | the page is about to be sent - before the ETag and page cache | `$output` (writable) |

---

## Template Syntax

### Single tags and pairs

A tag that is not a pair closes itself with a slash before its brace; a tag written without
the slash opens a pair, and its closing tag must follow:

```
{echo $total | money/}         # a single tag
{page 'header'/}               # one more
{flush/}                       # one without parameters
{items}<li>{$name}</li>{/items}  # a pair
{echo $x /| upper}             # the slash may stand before the opening pipe too
```

`{echo $x}` with no `{/echo}` behind it is an error under the strict check: "the pair {echo}
never closes - a single tag is written {echo .../}". The lenient walk (`$padCheckSyntax =
FALSE`) still reads such a tag single. The variable forms - `{$x}`, `{!x}`, `{#x}`, `{&x}`,
`{?x}`, `{^x}` - are never pairs and take no slash, nor do the branch markers `{else}`,
`{elseif ...}` and `{when ...}` inside their pair, nor the comments.

### Variables
```
{$variable}                    # Output variable
{$user.name}                   # Object/array property
{$items.0}                     # Array element by key - a dotted path, as above
{$$name}                       # Indirection - the name comes from another variable (an application one, never pad*);
                               #   in an expression too: {if $$pick eq 'x'}
{!text}                        # The same field, raw - it skips the sanitize chain {$x} runs
{?text}                        # The field as a url query fragment: &text=url+encoded
{#name}                        # A parameter or option of the tag
{&name}                        # A property of the tag
{^name}                        # The field as JSON, escaped for an HTML attribute
```

### Pipe Functions
```
{echo $name | upper/}              # Uppercase
{echo $text | trim | lower/}       # Chained functions
{echo $date | date('Y-m-d')/}      # With parameters
{echo $value | + 1/}               # Arithmetic (space required!)

{$name | upper}                   # A field tag pipes too - {echo} is for literals
{echo $value | +1/}                # WRONG - needs space before 1
{echo ($price | round) * 2/}       # A pipe inside ( ) or [ ] belongs to that group
{echo php:max(1, $n | + 1)/}       #   - to that argument of a call
```

### Pipe Timing: Opening vs Closing Tags

Pipes can be applied to a tag at two points, and they do not act on the same text.

**Closing tag pipe** - transforms the output, after every occurrence has been rendered and
joined:
```
{message}
  Content: {$message}
{/message | upper}
```
They chain left to right, each over what the one before returned.

**Opening tag pipe** - transforms the *content template*, once, before any occurrence is
rendered. What the function is handed is the source between the tags, not the data:
```
{items | trim}
  <li>x</li>
{/items}
```
It is not a way to reorder or filter what a tag iterates - a field written inside would be
transformed along with everything else and then not resolve. Sorting is an option:
```
{items sort}
  <li>{$name}</li>
{/items}
```

### Pipe Arithmetic
Arithmetic pipes require a space between the operator and operand:
```
{echo $value | + 1/}          # Correct - adds 1
{echo $value | * 2/}          # Correct - multiplies by 2
{echo $value | +1/}           # Wrong - no space
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

A bare value is tested for its truth, as PHP tests it - `''`, `'0'`, `0` and FALSE fail:
```
{if $count eq 0}Empty{/if}     # A comparison
{if $flag}True{/if}            # The value's truth - write a comparison when a value is meant
```

### Loops (Data Iteration)
```
{users}
  <li>{$name} - {$email}</li>
{/users}

{while $i le 10}
  Item {$i}
  {increment $i/}
{/while}

{until $count eq 0}
  Countdown: {$count}
  {decrement $count/}
{/until}
```

### Filtering, sorting and limiting (handling options)
```
{staff where='$salary gt 2500', sort='name', first=3}
  {$name}
@else@
  Nobody earns that much
{/staff}
```
The options run in the order written. `where` takes a quoted expression, evaluated per row
with the row's fields first. When the options leave no row, the `@else@` branch shows.

### Loop Control
```
{continue 'tagname'/}    # Skip to next iteration (like PHP's continue)
{cease 'tagname'/}       # Soft stop (graceful end)
{break 'tagname'/}       # Hard stop (immediate exit)
```

### Iteration Properties (property@tag syntax)

A property is written as a tag pair, in a ternary, or as a value inside a condition -
`{if first@items}` reads the property alone, `{if current@items eq 2}` with an operator. A
property name followed by `@` and a target is one reference inside an expression; only the
names in `pad/properties/` read that way, and any other word before `@` leaves `@` as the
current-value placeholder. Without a target - `{if current@ eq 2}` - it is the loop the
expression stands in; inside an `{if}`, a `{case}` or any other level that renders once for
the row - an `{ifchanged}`, a `{guest}`, a snippet - every property spelling belongs to the
loop around it. The sigil decides a name collision: with a row field named
`first`, `{if first@orders}` is still the iteration state and `{$first@orders}` is the
field.

```
{items}
  {first@items}<ul>{/first@items}
  <li class="{even@items ? even : odd}">{$name}</li>
  {last@items}</ul>{/last@items}
  Index: {current@items/} of {count@items/}
{/items}
```

### Case/Switch
```
{case $color}
  {when 'red'} Stop
  {when 'yellow', 'amber'} Caution
  {when 'green'} Go
  {else} Unknown
{/case}
```

### Group Headings (ifchanged)
```
{orders sort='customer'}
  {ifchanged $customer}<h2>{$customer}</h2>{/ifchanged}
  <p>{$number}</p>
{/orders}
```
Renders when the value differs from the previous row of the loop; the first row always does.

### Switch Tag (Alternating Values)
```
{items}
  <tr style="background: {switch '#fff', '#eee'/}">
    <td>{$name}</td>
  </tr>
{/items}
```

### Forms
```
{form 'contact'}                                      # posts back, CSRF token + name
  {input 'email', type='email', label='E-mail', required/}
  {textarea 'message', label='Message', rows=6/}
  <button>Send</button>
{/form}
```
The fields refill from the post when their form came back and show the message
`padValidate` found - in the page's PHP:
`if ( padPosted ( 'contact' ) ) $errors = padValidate ( [ 'email' => 'required|email' ] );`
Rules - a file each of `pad/subs/rules/types/`: required, email, url, numeric, integer, min:n, max:n,
between:a,b, digits[:n], in:a,b, regex:/x/, same:field, accepted, date, time, alpha, alphanum,
starts_with:x, ends_with:x, lowercase, uppercase, color, uuid, ip[:v4|v6], slug, json, iban,
not_in:a,b, distinct (no value of a list twice), min_items:n, max_items:n (of a list), min_words:n, max_words:n, multiple_of:n (0.25, 0.01 - in decimal digits) - and the rules that look at another field of the post: required_if:field,value[,value],
required_unless:field,value, required_with:field[,field], required_without:field[,field],
before:field|date and after:field|date (ISO `2026-11-01`, `2026-11-01T14:30`, `:15`, `:15.25`). A
different:field (the mirror of same:),
different:field (the mirror of same:), gt, gte, lt and lte:field|n (a value - or a length - above,
at least, below, at most another's or a number: gt:min_price, gt:0),
before:field|date and after:field|date (ISO `2026-11-01` or `2026-11-01T14:30`),
before_or_equal and after_or_equal (the same moment too). A
list passes `required`, the required_* rules, `in:` and `not_in:` (every item), `distinct`, `min_items`, `max_items` only, and only on a field named for one
(`tags[]`, in the rules too); on a field of one value it is refused - "must be a
single value" - and any other rule in the `rules=` of a field that posts a list (`tags[]`, a
`{multiselect}`) is an error when the template is read.

The rules can also stand on the fields, checked before any PHP runs and in the browser too -
[Form rules](docs/FEATURES.md#form-rules-on-the-fields-and-in-the-browser).

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
{set $name = 'Alice'/}              # Assign string
{set $count = 0/}                   # Assign number
{set $total = $price * $qty/}       # Assign expression
{set $tags = ['a', 'b']/}           # Assign a list
{set $user = ['name' => 'Ann', 'age' => 42]/}   # A list with keys - {$user.name}
```

### Level vs Occurrence Variables
- `$var` - Level variable (constant for all iterations)
- `%var` - Occurrence variable (changes each iteration)

The `%` marks the assignment; the value is read back as an ordinary field, with `$`:

```
{staff %total = $salary + $bonus}
  {$name}: {$total}
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
{echo 50 | @ * 4/}                # @ represents the current value
{echo $text | '"' . @ . '"'/}     # Wrap value in quotes
```

### Comments

Two forms, both dropped before the template is scanned, so a tag inside one never runs:
```
{# a comment #}
{-- a comment, the form the editor kits toggle --}
```
`{--` must be followed by whitespace - `:root{--gap:4px}` inside `{ignore}` is CSS, not a
comment - and both forms close at the first `#}` / `--}` after them.

### Whitespace control
A `~` just inside a brace takes the whitespace on that side, newlines included:
```
<ul>
  {~items~}                  # nothing before the tag, nothing at the start of the content
    <li>{$name}</li>
  {~/items~}                 # nothing before or after the closing tag
</ul>
{spaceless}...{/spaceless}   # the whitespace between HTML tags goes
```
Only a brace that opens a tag counts; a `~` in text or a quoted parameter stays.

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
<div data-json="{json 'products' | ignore/}"></div>
<div data-users="{echo $usersJson | html | ignore/}"></div>
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

### Values Are Text

A value never becomes template code - a field, what a tag answers, a fetched page. With
`$v = '{php:getcwd}'`, both `{$v}` and `{echo $v/}` print the text `{php:getcwd}`. The
syntax characters of a value travel as inert stand-ins and are restored only when the page
is written out, so a value inside another tag's quoted parameter - `{echo '{$v}'/}` - stays
text too. To run a value as PAD, say so where it is used - a snippet kept in a database:

```
{echo $snippet | code/}       # runs the value as PAD
{echo $snippet | sandbox/}    # the same, in an isolated pass
```

A field written bare into a tag's parameters - `{echo {$v}/}` - is spliced as a quoted
string, unless it is a plain number, so a value of `php:getcwd` stays text; `{echo $v/}`
says the same more simply. A tag's rendered output written there is template text, by design.

`$padProtectValues = FALSE` brings back the old behaviour: every value re-read as template
source.

---

## Critical Syntax Rules (Common Mistakes)

1. **Pipe a value through `{echo}` or a field tag** - `{$var | upper}` and `{echo $var | upper/}` both work, but only the field tag runs the sanitize chain - `{echo}` prints the value raw; a literal needs the `{echo}`
2. **Arithmetic needs space** - `{echo $x | + 1/}` not `| +1`
3. **Quote literal strings** - `{count 'items'}` not `{count items}`
4. **No inline CSS/JS** - PAD parses `{ }` as tags; use external files or `{ignore}` wrapper
5. **Use `padRedirect()`** - Don't use `exit` or `die` in PAD apps
6. **A bare condition tests truth** - `{if $flag}` fails for `''`, `'0'`, `0` and FALSE; compare when a value is meant: `{if $count eq 0}`
7. **`$` vs `%` variables** - `$var` is level (constant), `%var` is occurrence (per-iteration)
8. **CHECK syntax** - Use `db("CHECK table WHERE...")` NOT `db("CHECK * FROM table...")`
9. **Boolean options** - Use `{tag option}` NOT `{tag option="true"}` - just the option name is enough
10. **A single tag ends in a slash** - `{echo $x/}`, `{set $n = 1/}`, `{page 'x'/}`, `{current@items/}`; `{echo $x}` opens a pair and needs its `{/echo}`

---

## Type Prefixes

Resolve naming conflicts with explicit prefixes:

| Prefix | Purpose | Example |
|--------|---------|---------|
| `app:` | App tag from `_tags/` | `{app:mytag}` |
| `common:` | Tag from the `_common` app | `{common:padFrame}` |
| `pad:` | Built-in PAD tag | `{pad:if}` |
| `php:` | Call PHP function | `{php:strlen 'abc'/}` as a tag, `{echo $x \| php:strlen(@)/}` in an expression |
| `function:` | Custom PAD function | `{$x \| function:myfunc}` |
| `data:` | Defined data block | `{data:items}` |
| `content:` | Content block | `{content:header}` |
| `include:` | Snippet from `_include/` | `{include:header}` |
| `pull:` | Stored sequence | `{pull:mySeq}` |
| `field:` | Field (variable) value | `{field:name}` - the database single value is the `{field "col from table..."/}` tag |
| `select:` | Declared select table | `{select:users}` |
| `local:` | Files from `_data/` | `{local:menu.json}` |
| `constant:` | PHP constant | `{constant:PHP_VERSION}` |
| `bool:` | Boolean store | `{bool:isAdmin}` |
| `array:` | Access array as loop | `{array:items}` |
| `level:` | Array field of an enclosing row | `{level:kids}...{/level:kids}` |
| `parm:` | Tag parameter value | `{parm:name}` |
| `property:` | Tag property | `{property:current}` |
| `script:` | Script from `_scripts/` | `{script:backup}` |
| `sequence:` | Sequence type | `{sequence:fibonacci}` as a tag, `sequence:fibonacci(8)` in an expression - the 8th term |
| `chart:` | Chart type | `{chart:pie data='budget'/}`, the same chart as `{chart 'pie', data='budget'/}` - a kind is a file of `pad/subs/charts/types/` |
| `game:` | Game | `{game:memory pairs=6/}`, the same game as `{game 'memory', pairs=6/}` - a game is a file of `pad/subs/games/types/` |
| `toy:` | Toy | `{toy:life/}`, as `{toy 'life'/}` - a toy is a file of `pad/subs/toys/types/` |
| `diagram:` | Diagram kind | `{diagram:state}...{/diagram:state}`, the same as `{diagram type='state'}` - a kind is a file of `pad/subs/diagrams/types/` |
| `widget:` | Widget | `{widget:modal 'terms'}...{/widget:modal}`, as `{widget 'modal', 'terms'}` or bare `{modal 'terms'}` - a widget is a file of `pad/subs/widgets/types/` |
| `barcode:` | Barcode symbology | `{barcode:ean13 '400638133393'/}`, as `{barcode '...', type='ean13'/}` - a symbology is a file of `pad/subs/codes/types/` |
| `icon:` | Icon | `{icon:star size=20/}`, as `{icon 'star', size=20/}` - an icon is a file of `pad/subs/icons/types/`; never bare |
| `pattern:` | Pattern | `{pattern:topography seed=7/}`, as `{pattern 'topography', seed=7/}` - a pattern is a file of `pad/subs/patterns/types/`; never bare |
| `embed:` | Embed provider | `{embed:youtube 'id'/}`, as `{embed 'https://youtu.be/id'/}` - a provider is a file of `pad/subs/embeds/types/` |
| `fake:` | Fake kind | `{fake:email/}`, as `{fake 'email'/}` - a kind is a file of `pad/subs/fakes/types/`; never bare |
| `notation:` | Notation | `{notation:math}a/b{/notation:math}`, as `{notation 'math', 'a/b'/}` or bare `{math 'a/b'/}` - a notation is a file of `pad/subs/notations/types/`; bare `{chord}` is the charts', so `{notation:chord 'Am'/}` |
| `input:` | Rich form field | `{input:slider 'volume'/}`, as bare `{slider 'volume'/}` - a kind is a file of `pad/subs/inputs/types/` |
| `sound:` | Sound | `{sound:chime/}`, as `{sound 'chime'/}` - a sound is a recipe, a file of `pad/subs/sounds/types/`; never bare |
| `printable:` | Printable | `{printable:labels data='rows'}...{/printable:labels}`, as `{printable 'labels', ...}...{/printable}` - a kind is a file of `pad/subs/printables/types/`; never bare |
| `action:` | Sequence action | `{action:reverse}` |
| `flag:` / `make:` / `keep:` / `remove:` | Sequence operations | `{make:fibonacci}` |
| `section:` | Section of a landing page | `{section:hero title='...'}...{/section:hero}`, as `{section 'hero', ...}` - a kind is a file of `pad/subs/sections/types/`; never bare |
| `effect:` | Effect | `{effect:typewriter}Hi{/effect:typewriter}`, as `{effect 'typewriter'}` or bare `{typewriter}` - a kind is a file of `pad/subs/effects/types/` |
| `jsonld:` | JSON-LD kind | `{jsonld:article headline=$title/}`, as `{jsonld 'article', data='post'/}` - a kind is a file of `pad/subs/jsonld/types/`; never bare |

---

## Tags, Functions, Options & Properties Reference

See the following files for complete reference documentation:

- **[TAGS.md](docs/reference/TAGS.md)** - All built-in tags (control flow, data, database, file, output, navigation, execution, debug), type prefixes, options, and properties
- **[FUNCTIONS.md](docs/reference/FUNCTIONS.md)** - All pipe functions (string, case, HTML, date, arithmetic, printf format)
- **[HELPERS.md](docs/reference/HELPERS.md)** - The functions a page's PHP calls: the helpers (arrays, strings, requests, cache, dates ...) and the engine functions (redirects, forms, database, errors)
- **[FEATURES.md](docs/FEATURES.md)** - Every feature beyond the core language, indexed under Features below
- **[sequences/](docs/sequences/)** - Sequence subsystem (122 sequence types, actions, transformations)

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
| `and`, `&&` | Logical AND |
| `or`, `\|\|` | Logical OR |
| `range (a, b)` | Value in range |
| `in ('a', 'b')` | Value in a list |
| `like 'A%'` | SQL LIKE match |
| `matches '/re/'` | Regular expression match (a backslash written `\\`) |
| `a ?? b` | `a`, or `b` when `a` is empty or a missing field |
| `c ? a : b` | Inline ternary - `{echo $n gt 1 ? 'items' : 'item'/}` |
| `a ?: b` | `a` when it is true, else `b` - `{echo $title ?: 'Untitled'/}` |

---

## Features

Beyond the core language in this file PAD has a large set of features, each with its syntax
in [docs/FEATURES.md](docs/FEATURES.md) - read the section before using one. Every built-in
tag is in [TAGS.md](docs/reference/TAGS.md), every function for a page's PHP in
[HELPERS.md](docs/reference/HELPERS.md).

| Feature | Written as |
|---------|------------|
| [Template emails](docs/FEATURES.md#template-emails) | `{mail to=$email, template='order'}`, `padMail ()`, `_mail/` |
| [Early flush](docs/FEATURES.md#early-flush) | `{flush}` after the wrapper's `</head>` |
| [Server-sent events](docs/FEATURES.md#server-sent-events) | `padSse ( $producer, $options )` - no template |
| [Vite builds](docs/FEATURES.md#vite-builds) | `{vite 'src/main.js'/}` |
| [Sitemap](docs/FEATURES.md#sitemap-from-the-file-tree) | `{sitemap}`, `$padSitemap` → `?sitemap.xml`, `?robots.txt` |
| [Structured data (JSON-LD)](docs/FEATURES.md#structured-data-json-ld) | `{jsonld:article data='post'/}`, `{jsonld:faq data='questions'/}`, `{jsonld:breadcrumb/}`, `padJsonLd ( 'product', $row )` - a Schema.org object per kind, explicitly mapped, a kind per file of `pad/subs/jsonld/types/` |
| [JSON and CSV answers](docs/FEATURES.md#json-and-csv-from-the-same-page) | `$padExpose` + `&padFormat=json` or `Accept` |
| [Data formats](docs/FEATURES.md#data-formats) | `{data 'x', format='toml'}`, `_data/x.ics`, `&padFormat=xlsx`, `padFormatRead ()`, `padFormatWrite ()` - toml, ini, tsv, ndjson, jsonseq, markdown, ics, vcf, geojson, gpx, kml, vtt, srt, xlsx, rss, atom, jsonfeed: a format per file of `pad/subs/formats/types/` |
| [Maintenance mode](docs/FEATURES.md#maintenance-mode) | `pad down shop --secret=word`, `pad up shop` |
| [Custom error pages](docs/FEATURES.md#custom-error-pages) | `_errors/404.pad`, `4xx.pad`, `5xx.pad` |
| [Health check](docs/FEATURES.md#health-check) | `$padHealth` → `?up`, `_health.php` |
| [CORS](docs/FEATURES.md#cross-origin-requests-cors) | `$padCors` |
| [Login, gates, feature flags](docs/FEATURES.md#logging-in-gates-and-feature-flags) | `padLogin`, `padAuthRequire`, `{auth}`, `{guest}`, `{can}`, `padGate`, `{feature}` |
| [Asset versioning](docs/FEATURES.md#asset-versioning) | `{asset 'app.css', tag/}`, `padAsset ()`; the subsystems' files in `www/assets/<sub>/`, linked by a page (`$padAssetLinks`), written into a mail, offline and a static copy |
| [Fake data](docs/FEATURES.md#migrations-and-seeders) | `{fake 'email'/}`, `{fake 'name, email', rows=5, toData='people'/}`, `padFake ()`, `padFakeRows ()` - a kind per file of `pad/subs/fakes/types/` |
| [Migrations and seeders](docs/FEATURES.md#migrations-and-seeders) | `_migrations/`, `_seeds/`, `pad migrate`, `padFactory`, `padFake*` |
| [Queues and the scheduler](docs/FEATURES.md#queues-and-the-scheduler) | `padQueue ()`, `_jobs/`, `pad work`, `_schedule.php` |
| [Offline applications](docs/FEATURES.md#offline-applications-in-the-browser) | `pad pack notes`, `_offline.json`, `padWasm ()` |
| [HTTP fakes for tests](docs/FEATURES.md#http-fakes-for-tests) | `padCurlFake ()`, `padCurlRecorded ()` |
| [Log in with GitHub, Google ... (OAuth)](docs/FEATURES.md#log-in-with-github-google--oauth) | `padOAuthStart ( 'github' )`, `padOAuthUser ()` → `padLogin`, `{oauth 'github'/}` - a provider per file of `pad/subs/oauth/types/` |
| [Notifications on every channel](docs/FEATURES.md#notifications-on-every-channel) | `padNotify ( $user, 'shipped', $data, [ 'mail', 'slack' ] )`, `_notifications/`, `{notifications/}`, `padWebPushKeys ()` - a channel per file of `pad/subs/channels/types/` |
| [A language model answering in the page](docs/FEATURES.md#a-language-model-answering-in-the-page) | `{ai provider='anthropic', ttl=86400}...{/ai}`, `padAi ( $prompt, $options )` - a provider per file of `pad/subs/ai/types/` |
| [Live regions](docs/FEATURES.md#live-regions-interactivity-without-app-javascript) | `{live 'cart'}`, `pad-click`, `padLiveEvent ()` |
| [Lookups between data sets](docs/FEATURES.md#lookups-between-data-sets) | `\| lookup('customers', 'id', 'name')` |
| [Grouping with subtotals](docs/FEATURES.md#grouping-with-subtotals-group-option) | `{orders group='customer', sum='total'}` |
| [Page links](docs/FEATURES.md#page-links-pager-tag) | `page=`, `rows=`, `{pager 'products'}` |
| [Recursive trees](docs/FEATURES.md#recursive-trees) | `{tree}`, `{branch}`, `{recurse}` |
| [Form rules](docs/FEATURES.md#form-rules-on-the-fields-and-in-the-browser) | `rules='required\|email'` on a field, `client`, `{validator}` |
| [Rich form fields](docs/FEATURES.md#rich-form-fields) | `{slider 'v'/}`, `{input:datepicker 'd'/}`, `{stars}`, `{taginput}`, `{multiselect 'tags'/}` (posts the list `tags[]`), `{signature}` ... - native fields made more of, a kind per file of `pad/subs/inputs/types/` |
| [Layouts](docs/FEATURES.md#layouts-extends--block--parent) | `{extends}`, `{block 'title'}`, `{parent}` |
| [Components](docs/FEATURES.md#components-slots--parms) | `{slot}`, `{parms}` in a `_tags/x.pad` |
| [Tags defined in the template](docs/FEATURES.md#tags-defined-in-the-template-define--macro) | `{define}`, `{macro}` |
| [Web components](docs/FEATURES.md#web-components-declarative-shadow-dom) | `{shadow}` |
| [Stacks](docs/FEATURES.md#stacks-push--stack) | `{push 'scripts'}`, `{stack 'scripts'/}` |
| [Page metadata](docs/FEATURES.md#page-metadata) | `{meta title=, layout=, cache=/}` |
| [Response fragments](docs/FEATURES.md#response-fragments) | `{fragment 'x'}`, `&padFragment=`, `HX-Target` |
| [HTML attribute helpers](docs/FEATURES.md#html-attribute-helpers) | `{attrs}`, `{classes}` |
| [Fragment cache](docs/FEATURES.md#fragment-cache) | `{cache 'x', ttl=300}`, `cache=` on a tag, `{nocache}` |
| [Remote data with a cache](docs/FEATURES.md#remote-data-with-a-cache) | `{curl 'https://...', ttl=600}`, `padPrefetch ()` |
| [Translations, locale formatting](docs/FEATURES.md#translations-and-locale-formatting) | `{trans}`, `_lang/`, `\| currency`, `\| localDate` |
| [Markdown](docs/FEATURES.md#markdown) | `{markdown}`, `\| markdown` |
| [Markdown collections](docs/FEATURES.md#markdown-collections) | `{collection 'blog'}`, `_content/` |
| [Site search](docs/FEATURES.md#site-search) | `{search 'blog', q=$q, fields='title^2, body'}...{/search}`, `padSearch ()` - BM25, phrases, prefixes, Snowball stemmers en, nl, de, fr, es, it, pt, sv, da, no, ru: a language per file of `pad/subs/search/types/` |
| [Charts](docs/FEATURES.md#charts) | `{chart 'bar', data=, label=, value=/}` or `{chart:bar .../}` - inline SVG, a kind per file of `pad/subs/charts/types/`; `{sparkline/}`; `client` draws it in the browser (`www/assets/charts/`) |
| [Games](docs/FEATURES.md#games) | `{game 'memory'/}`, `{game:sudoku/}`, `{crossword}...{/crossword}` - 19 games played in the browser, a game per file of `pad/subs/games/types/` |
| [Toys](docs/FEATURES.md#toys) | `{toy 'life'/}`, `{toy:mandelbrot/}`, bare `{boids/}` - live simulations in the browser, a toy per file of `pad/subs/toys/types/` |
| [Printables](docs/FEATURES.md#printables) | `{printable:labels data='products', sheet='a4-3x8'}...{/printable:labels}`, badges, cards, tickets (`<aside>` the stub), certificate, envelope, letter and invoice (`<address>` in the window), indexcards, placecards, graphpaper (squares, dots or lines, drawn exactly), calendar (a month a sheet, `holidays=` marked), foldcard (a quarter fold, `<header>` the front), staffpaper (music paper, `rastral=`, `grand`) - mm-based sheets that print alone, `pdf` for the same sheets from the PDF writer, a kind per file of `pad/subs/printables/types/` |
| [Sounds](docs/FEATURES.md#sounds) | `{sound:chime/}`, `{sound 'pop', button='Pop'/}`, `{sound:coin on='click', target='#save'/}`, `data-pad-sound="pop"`, `{toast ..., sound='success'/}`, `padSoundWav ()` - synthesised in the browser, the same samples as the server's WAV, a recipe per file of `pad/subs/sounds/types/` |
| [Widgets](docs/FEATURES.md#widgets) | `{tabs}`, `{modal 'x'}`, `{toast 'Saved'/}`, `{widget:dropdown 'Menu'}...{/widget:dropdown}`, `{tooltip}`, `{wizard}`, `{lightbox}`, `{drawer}`, `{breadcrumb/}` - work without JavaScript, a widget per file of `pad/subs/widgets/types/` |
| [Diagrams](docs/FEATURES.md#diagrams) | `{diagram}...{/diagram}`, `{diagram:state}`, `{mindmap}`, `{gitgraph}` - flowchart, sequence, state, class, er, mindmap, tree, journey, gitgraph, fishbone, railroad, packet: a kind per file of `pad/subs/diagrams/types/` |
| [Maps](docs/FEATURES.md#maps) | `{map 'eu', data=, key=, value=/}` - a view per file of `pad/subs/maps/types/` |
| [Spreadsheets](docs/FEATURES.md#spreadsheets) | `{spreadsheet 'x'}Item,Price\nTea,=B1*2{/spreadsheet}`, `data=`, `formulas='total=B2*C2'`, `formats=`, `totals=`, `editable` in a `{form}`/`{live}`; `padFormula ()`, `padSheet ()` - Excel's formulas, a function per file of `pad/subs/formulas/types/` |
| [Embeds](docs/FEATURES.md#embeds) | `{embed 'url'/}`, `{youtube 'id'/}`, `{openstreetmap 'lat,lon'/}` - click to load, a provider per file of `pad/subs/embeds/types/`; `padEmbedSources ()` |
| [Month calendar](docs/FEATURES.md#month-calendar) | `{calendar}` |
| [Public holidays](docs/FEATURES.md#public-holidays) | `{holidays 'nl', year=2026}`, `{calendar holidays='de-by'/}`, `padHolidays`, `padIsHoliday`, `padWorkdays`, `padAddWorkdays` - a country per file of `pad/subs/holidays/types/` |
| [Syntax highlighting](docs/FEATURES.md#syntax-highlighting) | `{highlight 'php'}`, `{source}` |
| [QR codes and barcodes](docs/FEATURES.md#qr-codes-and-barcodes) | `{qr}`, `{barcode type='upce'}`, `{barcode:ean13 '...'/}`, bare `{itf14 '...'/}`, `{datamatrix '...'/}`, `{aztec '...'/}`, `{pdf417 '...'/}`, `{gs1128 '(01)...'/}`, `{bookland 'ISBN ...', addon='51995'/}`, `{databar '...'/}` - a symbology per file of `pad/subs/codes/types/` |
| [Icons](docs/FEATURES.md#icons) | `{icon 'star'/}`, `{icon:star/}` - an icon per file of `pad/subs/icons/types/` (an SVG each) |
| [Patterns](docs/FEATURES.md#patterns) | `{pattern 'topography', seed=7/}`, `{pattern:dots as='url'/}`, `padPattern ()` - seeded SVG backgrounds, the same bytes for the same seed, a kind per file of `pad/subs/patterns/types/` |
| [Image filters](docs/FEATURES.md#image-filters) | `{img 'p.jpg', width=400, filter='duotone', colors='#123, #fc6', alt='...'/}`, `filter='grayscale, dither'`, `halftone`, `ascii`, placeholders `blurhash`, `dominant`, `lqip` - a filter per file of `pad/subs/filters/types/` |
| [Notations](docs/FEATURES.md#notations) | `{math}x^2/2{/math}`, `{chem 'H2SO4'/}`, `{abc}...{/abc}`, `{notation:chord 'Am'/}`, `{piano 'C4 E4 G4'/}`, `{braille 'Hi'/}`, `{morse 'SOS'/}`, `{semaphore}`, `{signals}`, `{chess $fen/}`, `{go 'sgf'/}`, `{draughts 'fen'/}`, `{tablature}e|-0-3-|{/tablature}`, `{karnaugh 'm(1,3,7)'/}`, `{bridge 'N:AKQ.. ...'/}`, `{siteswap '531'/}`, `{cube "R U R' U'"/}` - drawn on the server, a notation per file of `pad/subs/notations/types/` |
| [Showcase tags](docs/FEATURES.md#showcase-tags) | `{avatar}`, `{progress}`, `{rating}`, `{icon}`, `{datatable}`, `{toc}`, `{diagram}`, `{map}`, `{pdf}` ... |
| [Settings](docs/FEATURES.md#configuration-settings) | every `$pad*` setting - defaults and comments in `pad/config/config.php` |
| [Designer preview](docs/FEATURES.md#designer-preview-with-sample-data) | `?page&padSample`, `_samples/`, `pad sample` |
| [Expression errors](docs/FEATURES.md#expression-errors) | what the strict check says about a malformed expression |
| [Errors in the template](docs/FEATURES.md#errors-point-into-the-template) | file, line, column and excerpt in every error report |
| [Application tests](docs/FEATURES.md#application-tests) | `_tests/name.pad` + `name.txt`, `{assert}`, `pad test` |
| [The frame of the PAD apps](docs/FEATURES.md#the-frame-of-the-pad-applications) | `{common:padFrame}`, `$frameMenu`, `www/_common/pad.css`, the theme switcher in its bar; the brand top left of every app `{common:padBrand/}`; the playground and live icons top right of every example, `padPlayLink ()` |
| [Common patterns](docs/FEATURES.md#common-patterns) | zebra rows, comma lists, first/last wrappers, counters |
| [Sections](docs/FEATURES.md#sections) | `{section:hero title=}`, `{section:pricing data=/}`, `{section:item 'Pro', price=}`, `{slot 'eyebrow'}` - landing-page blocks in pure CSS, a kind per file of `pad/subs/sections/types/` |
| [Effects](docs/FEATURES.md#effects) | `{typewriter}`, `{highlighter}`, `{countup to=/}`, `{reveal}`, `{marquee}`, `{spinner/}`, `{confetti/}` - CSS first, still under reduced motion, a kind per file of `pad/subs/effects/types/` |
| [Themes](docs/FEATURES.md#themes) | `$padTheme = 'paper'`, `{theme 'terminal'/}`, `{themes/}` - the frame's colours swapped, WCAG AA, a theme per file of `pad/subs/themes/types/` |
| [Palettes](docs/FEATURES.md#palettes) | `{palette 'viridis', steps=7/}`, `palette=` on `{chart}`, `padPalette ()` - colour scales, a palette per file of `pad/subs/palettes/types/` |

---

## Database Operations

See [DATABASE.md](docs/DATABASE.md) for complete database documentation including:
- PHP `db()` function (RECORD, ARRAY, FIELD, CHECK, INSERT, UPDATE)
- Template database tags ({field}, {record}, {array}, {check})
- PAD Select Subsystem (declarative table access, relations, automatic joins)

---

## Configuration

The database settings are no code: their values stand in `.env` files, read with `padEnv`.
`pad/config/config.php` reads every `$padSql*` from the PAD home's `.env`; an application's
own `.env` - or `_common`'s, for an application that runs with `_common` - overrides it:

```
# apps/myapp/.env
padSqlHost=localhost
padSqlDatabase=myapp
padSqlUser=user
padSqlPassword=pass

# Or SQLite through PDO - no server: the file (relative = under DATA/) and a .sql file
# (relative = in the app) that builds it on first use. db() verbs, placeholders, the
# database tags, named queries and Select work alike on both drivers (rollup is MySQL's).
# padSqlDriver=sqlite
# padSqlDatabase=myapp/myapp.sqlite
# padSqlSetup=_install/schema.sql
```

Every other setting is a `$pad*` variable: `pad/config/config.php` lists each one with its
default and a comment saying what it does, and an application's `_config/config.php` (or
`_common`'s) overrides it - [Configuration settings](docs/FEATURES.md#configuration-settings)
shows them in use. The ones met most:

| Setting | Default | |
|---------|---------|-|
| `$padErrorAction` | `'pad'` | pad, boot, php, stop, exit, ignore, log, dump |
| `$padInfo` | `''` | debug mode: trace, stats, track, xml, xref |
| `$padOutputType` | `'web'` | web, file, download, console, json, csv |
| `$padCheckSyntax` | TRUE | the strict syntax check - off, the lenient walk keeps what nothing claims as text |
| `$padProtectValues` | TRUE | values are text, never template code |
| `$padRequestVars` | TRUE | which request values become variables - TRUE all, a list, `[]` none |
| `$padSessionVars` | `[]` | the variables kept in the session |
| `$padPhpFunctions` | TRUE | the PHP functions a template may call |
| `$padCsrf` | FALSE | a token on every post form, a post without it 403 |
| `$padCache` | FALSE | the page cache |
| `$padToolbar`, `$padReload`, `$padCheckOutput` | FALSE | debug toolbar, live reload, output check of local requests |
| `$padCleanUrls` | FALSE | links written as `/myapp/products/42` |
| `$padAssetLinks` | TRUE | the subsystems' style sheets and scripts linked from `www/assets/`, FALSE written into every page |

---

## The pad Command

`apps/cli/pad` is the command-line tool (link it into your PATH as `pad`). Each command is a
file in `apps/cli/_commands/`; without a command word it runs the cli application as before.

```bash
pad new shop                  # apps/shop/ (index.php, index.pad, README.md) + www/shop/index.php
pad new shop/orders/list      # apps/shop/orders/list.php + list.pad - never overwrites
pad serve [port] [host]       # php -S over www/ at http://127.0.0.1:8000/<app>/ (--mount=pad: /pad/<app>/)
pad render demo clock         # a page to stdout; name=value pairs become request values
pad lint shop [dir]           # every page rendered under the strict check, errors with file:line:col
pad export demo out/          # a static copy: page.html files with rewritten links, www/demo/ assets
pad test shop [name]          # the app's own tests in apps/shop/_tests/ (--record, --all)
pad sample shop orders        # the page run once, its variables kept in apps/shop/_samples/orders.json
pad types shop orders         # TypeScript: OrdersVars from the sample, OrdersAnswer from padFormat=json
pad migrate shop [--status]   # the pending migrations of _migrations/; --rollback, --fresh --force, --seed
pad seed shop [name]          # the seeders of _seeds/
pad work shop [--once]        # the queue worker; pad queue shop [--failed] for the queues
pad schedule --all            # what every _schedule.php has due - one cron line, every minute
pad down shop --secret=word   # maintenance until pad up shop
pad pack notes [--serve]      # the application for the browser, offline - DATA/wasm/notes/; --install once
pad help
```

The repository is the one the script stands in unless `PAD_HOME` says otherwise. A failed
render answers the JSON error report and exit status 1; `pad lint` exits 1 when any page failed.

An application keeps its own tests in `_tests/` - see [Application tests](docs/FEATURES.md#application-tests).

---

## Entry Point Pattern

The `www/` directory can be served as the docroot itself (apps at `http://host/<app>/`) or mounted under a URL prefix (apps at `http://host/pad/<app>/` - the current local setup, via a `pad` symlink in the Apache docroot). An app's entry point (`www/myapp/index.php`) is just:

```php
<?php
  include __DIR__ . '/../pad.php';
?>
```

`www/pad.php` does the actual bootstrapping:
1. Includes `home/home.php`, which detects the OS and sets `$padHome` (the repo root) - the single place where machine-specific paths live (shell scripts source `home/home.sh` for the same purpose)
2. Sets `$padApps` (`$padHome/apps/`) and `$padData` (`$padHome/DATA/`)
3. Derives `$padApp` and the mount prefix `$padRoot` (e.g. `/` or `/pad/`) from `SCRIPT_NAME`/`SCRIPT_FILENAME`
4. Includes `pad/pad.php`, which defines the constants (`PAD`, `APP`, `APPS`, `DATA`, `COMMON`) and runs the request

Cross-app URLs (menu links, `padRedirect()`, the regression harness) are built from `$padHost`, which includes the mount prefix (`scheme://host` . `$padRoot`), so they work under any mount prefix. The host is the request's own Host header, but the engine's fetches of its own pages (`{get}`, `{page app=/}`, `SELF://`, `padPrefetch`) always connect to the server's own address (`SERVER_ADDR`:`SERVER_PORT`) - a forged Host cannot make the server fetch from another machine; with `$padHostBase` set (behind a proxy) they go where it says. Off `php -S` a port in the Host header counts only when it is 80/443 or `$padHosts` lists it with the name (`'localhost:8080'`) - Apache fills `SERVER_PORT` from the Host - else they connect to the scheme's port. Page-internal links use `$padGo`/`?page` (from `SCRIPT_NAME`, with `$padGoExt` as the absolute form) and are prefix-safe automatically.

The CLI variant (`apps/cli/pad`) sets `$padApp = 'cli'` explicitly and includes `pad/pad.php` directly.

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

  @page@

  <footer>&copy; 2025 My App</footer>
</body>
</html>
```

The `@page@` placeholder is replaced with each page's content.

Every PAD application shares one frame - `{common:padFrame}` in its `_inits.pad`,
`$frameMenu`, `www/_common/pad.css` - with the same brand top left, `{common:padBrand frame/}`:
the `{}` logo linking the PAD home and the application's name as the pull-down of the
applications (`_common/_data/padApps.php`), and the same bar: the page's title and the theme
switcher `{themes/}` - Default is light, Dark is one of the themes: see
[The frame of the PAD applications](docs/FEATURES.md#the-frame-of-the-pad-applications).

---

## PHP Helpers

Helper functions for a page's `.php` - what modern PHP frameworks ship (Laravel's `Arr`, `Str`,
`blank()`, `session()`, `Cache::remember()`, `now()`, `encrypt()` ...) written the PAD way:
plain functions with a `pad` prefix, loaded on every request from `pad/lib/`. Full reference:
[HELPERS.md](docs/reference/HELPERS.md); a manual page per group under "PHP helpers".

```php
$orders  = db ( "ARRAY * FROM orders" );
$byState = padArrGroupBy ( $orders, 'status' );          // [ 'paid' => [ ... ], ... ]
$city    = padArrGet ( $order, 'customer.address.city', 'unknown' );
$title   = padStrHeadline ( padRequest ( 'view', 'all_orders' ) );   // All Orders
$rates   = padRemember ( 'rates', 600, fn () => padCurl ( $ratesUrl ) ['data'] );
if ( ! padRateLimit ( 'login:' . $_SERVER ['REMOTE_ADDR'], 5, 60 ) ) padAbort ( 429 );
```

| Group | File | Functions |
|-------|------|-----------|
| Arrays | `lib/arr.php` | `padArrGet`, `padArrSet`, `padArrHas`, `padArrForget` (dot paths, `*`), `padArrOnly`, `padArrExcept`, `padArrPluck`, `padArrWhere`, `padArrFirst`, `padArrLast`, `padArrGroupBy`, `padArrKeyBy`, `padArrSortBy`, `padArrFlatten`, `padArrDot`, `padArrUndot`, `padArrWrap`, `padArrSum`, `padArrAvg`, `padArrMin`, `padArrMax` |
| Strings | `lib/str.php` | `padStrSlug`, `padStrLimit`, `padStrWords`, `padStrExcerpt`, `padStrSquish`, `padStrCamel`, `padStrStudly`, `padStrSnake`, `padStrKebab`, `padStrHeadline`, `padStrTitle`, `padStrAfter`, `padStrAfterLast`, `padStrBefore`, `padStrBeforeLast`, `padStrBetween`, `padStrIs`, `padStrMask`, `padStrRandom`, `padStrUuid` (v4, v7), `padStrPlural`, `padStrSingular` |
| Values | `lib/helpers.php` | `padBlank`, `padFilled`, `padValue`, `padTransform`, `padTap`, `padRetry`, `padRescue`, `padOnce` |
| Numbers | `lib/number.php` | `padNumberFormat`, `padNumberPercentage`, `padNumberAbbreviate`, `padNumberForHumans`, `padNumberOrdinal`, `padNumberClamp`, `padNumberFileSize`; pipes `abbreviate`, `ordinal` |
| Requests and sessions | `lib/request.php` | `padRequest`, `padRequestHas`, `padRequestFilled`, `padRequestOnly`, `padRequestExcept`, `padRequestMethod`, `padRequestIs`, `padSession`, `padSessionPut`, `padSessionHas`, `padSessionPull`, `padSessionForget`, `padSessionRegenerate`, `padFlashInput`, `padOld`, `padUrl`, `padBack`, `padAbort` |
| Environment | `lib/env.php` | `padEnv` - real environment, then the `.env` in the app's root, then `_common`'s `.env` (an app with `_common`), then `.env` in the PAD home; usable in `_config/config.php` - every `$padSql*` setting is read with it |
| Cache and rate limits | `lib/remember.php` | `padCacheGet`, `padCachePut`, `padCacheHas`, `padCacheIncrement`, `padCacheForget`, `padCacheFlush`, `padRemember` (files under `DATA/cache/app/<app>/`), `padRateLimit`, `padRateLimitRemaining`, `padRateLimitAvailableIn`, `padRateLimitClear` |
| Dates and logging | `lib/date.php`, `lib/log.php` | `padNow`, `padToday`, `padNowFreeze`, `padDateParse`, `padAgo` (pipe `ago`), `padLog` (PSR-3 levels, `DATA/logs/<app>/<date>.log`) |
| Hashing and encryption | `lib/crypt.php` | `padHash`, `padHashCheck`, `padHashNeedsRehash`, `padEncrypt`, `padDecrypt` (sodium, key `$padAppKey` or `DATA/keys/<app>.key`), `padSignedUrl`, `padSignatureValid` |
| Public holidays | `lib/holidays.php` | `padHolidays`, `padIsHoliday`, `padWorkdays`, `padAddWorkdays` - the rules in `pad/subs/holidays/` |
| Site search | `lib/search.php` | `padSearch` - the index, query and ranking in `pad/subs/search/` |

A wrong argument (an unknown operator, a negative length) is reported with `padError` naming
the function; the function then answers an empty value of its kind.

The engine's own functions for PHP - `padRedirect`, `padFlash`, `padValidate`, `padUpload`,
`padTagParm`, `db`, `padError` ... - are the last chapter of HELPERS.md,
[Engine functions](docs/reference/HELPERS.md#engine-functions).

---

## Framework Architecture

### Execution Flow
```
Request → pad.php → config → start/
                          → build/ (page assembly: _inits + @page@ + _exits)
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

The processing pipeline step by step - pad.php, build/, level/ with occurrence/ and walk/ - is
drawn in [docs/PAD.md](docs/PAD.md#processing-pipeline).

### Architecture Notes

- **Procedural Design**: 140+ functions, minimal OOP
- **Global State**: Uses `$GLOBALS` and `global` declarations indexed by level
- **File-Based Dispatch**: `include` statements as control flow
- **Array-Indexed Levels**: Nesting tracked via `$padVariable[$pad]` pattern
- **15+ Years Production**: Stable, battle-tested codebase
- **Request-level memos**: which global names are the engine's (`padAppVars`,
  `padStrPadVars`, `padValidStore`, `padStrPad`), the tokens of each clean expression
  (`padEvalTokens`) and its tree (`padEvalTreeGet`) are kept for the request, whether an
  engine or application file is there (`padFileExists`, `padCheck`, `padAppCheck`) for the
  second, the text of an engine or application file (`padFileGet`) for the request while
  its time and size stand; the @else@ pair scan runs only when an `@else@` is there.
  Between requests only directory listings are kept (`lib/dirs.php`: `padDirList`,
  `padDirRaw` - `DATA/cache/dirs/`, checked against the directory's inode and times) and
  the versions of the files of `www/assets/` a page links (`padWidgetHash`, lib/widget.php -
  `DATA/cache/assets/hashes.json`, checked against the file's inode, times and size: one stat
  a linked file) - the template is still rescanned each pass (no AST)
- **`$GLOBALS` as a whole is a copy**: since PHP 8.1 `array_key_exists ( $x, $GLOBALS )`,
  `foreach ( $GLOBALS ...)` and any function handed `$GLOBALS` copy the symbol table - some
  450 globals in a page run. A hot path tests `isset ( $GLOBALS [$x] )` first and sifts the
  table with `padAppVars`/`padStrPadVars`, whose answers hold values, never references
- **Code on use**: `pad/lib/` is loaded on every request (its listing kept by `padDirList`); the functions
  of one built-in tag stand in `pad/tags/_lib/<tag>.php`, which `types/pad.php` includes the
  first time a page uses the tag (a tag sharing another's code includes that file itself),
  and those of one engine feature in the `_lib/` of the directory where it starts - health,
  CORS (`inits/_lib/`), toolbar, live reload (`exits/_lib/`), error pages, dumps, the source
  map's error-time walk (`error/_lib/`), data answers, the designer preview (`build/_lib/`),
  the select builder (`select/_lib/`), lookup (`functions/_lib/`), group aggregates
  (`handling/_lib/`), the charts (`subs/charts/_lib/`, a kind per file of `subs/charts/types/`), the
  games (`subs/games/_lib/`, a game per file of `subs/games/types/`) -
  included where the feature runs. What the engine calls on every
  request, a pipe, a documented PHP helper or a page calls stays in `pad/lib/` - as the
  entry, which includes the rest when it runs (`padMarkdown`, `padMail`, `padHighlight`,
  `padDump`, `padErrorTemplate`, `padSitemapPages`)

---

## Debugging

- Set `$padInfo = 'trace'` for execution tracing
- Set `$padToolbar = 'local'` for the debug toolbar at the foot of every local page
- Set `$padReload = 'local'` in `_config/config.php` and a local page reloads itself when a file behind it is saved
- Use `{debug $order/}` for a collapsible view of a value inside the page (local requests only; `{debug}` alone shows every field visible there)
- Use `{dump}` tag for variable inspection
- Use `{trace}` tag for execution trace
- Set `$padCheckOutput = true` (or add `&padCheckOutput` to a local request) to have the finished HTML checked for duplicate ids, images without alt, unlabelled fields and broken `?page` links; `develop/?links` checks every application's templates at once
- Template coverage: `develop/?coverage&start=suites`, run `./ci.sh`, `develop/?coverage&stop`, then `develop/?coverage&run=suites` shows every template with the tags and `{if}`/`{case}` branches that never ran marked, and the files no request read (`$padCoverage` records one app's local requests)
- Replay real traffic: `$padRecord = true` (or `?page&padRecord=name`) keeps each cookie-less GET with its answer; `develop/?replay&store=default` re-asks every recorded page against the current code and lists the pages whose answer changed, to accept or delete
- Check `DATA/` directory for error dumps and logs

Errors name their place in the template - file, line, column, an excerpt and a suggestion
([Errors point into the template](docs/FEATURES.md#errors-point-into-the-template)); the
strict check names the exact fault of a malformed expression
([Expression errors](docs/FEATURES.md#expression-errors)).

### Testing PAD Pages from Command Line

On this machine `www/` is mounted at `http://localhost/pad/`, so every app URL carries that prefix:

```bash
curl "http://localhost/pad/demo/?clock"                 # a page
curl -i "http://localhost/pad/manual/"                  # with its headers
curl "http://localhost/pad/app/?page&param=value"       # with request values
curl "http://localhost/pad/app/?page&padCheckOutput"    # with the output check

./ci.sh                                                 # every suite - one line each, nonzero exit on a failure
CI_BENCH=25 ./ci.sh                                     # also failing when pages got 25% slower (develop/?benchmark/history)
curl -L "http://localhost/pad/regression/main/?index&test"   # the eight suites; results in DATA/suites/
```

A request cannot switch the trace on - `$padInfo` is never filled from the query: set
`$padInfo = 'trace'` in `_config/config.php`, or render with `--trace` (below). DATA/reference
and DATA/examples are harvested by the develop app and stand between builds.

### Rendering on the Command Line

`php editors/render.php <app> [<page>]` renders a page of any application without a
server. The status is 0 when it rendered; 1 and the JSON error body of
`pad/error/claude.php` (`error`, `file`, `line`, the globals) when PAD reported one - a
strict syntax check of a page with its real data:

```bash
php editors/render.php demo clock
php editors/render.php regression/errors syntax/a_case_never_closes   # 1 + {"error":"PAD: the pair ..."}
echo '{$total | money}' | php editors/render.php shop orders --source=-  # a template text, with orders.php's data
php editors/render.php demo clock --trace --host=http://localhost/pad/   # trace in DATA/trace/clock/
```

`--source` hands the text to the engine in `$padPageSource`, which only an entry point can
set: the page then needs no file of its own.

---

## Editor Tooling

`editors/` holds the editor kits (see `editors/README.md`). The language server
`editors/lsp/pad-lsp.js` (Node, no dependencies) gives any LSP editor completion,
diagnostics on open and save from a real render, hover from `docs/reference/*.md`, and
go-to-definition along PAD's lookup order - `{mytag}` to the nearest `_tags/mytag.*`,
`| money` to `_functions/money.php`, `{$total}` to the line of the paired `.php` that
assigns it. `node editors/lsp/test.js` tests it; `./ci.sh` runs that as its `lsp` line.
`editors/tree-sitter-pad` is a Tree-sitter grammar for Zed, Helix and Neovim - highlighting,
folding of tag pairs, text objects - whose scanner pairs `{x}` with `{/x}` the way the
engine does; the built-in names in its highlight queries are written by
`php editors/generate.php`, and `./ci.sh` checks it as its `treesitter` line.

### MCP server

`editors/mcp/pad-mcp.js` lets an assistant ask the engine instead of this file - register it
with `claude mcp add pad -- node /Users/herbert/pad/editors/mcp/pad-mcp.js`. Its tools:
`pad_render` and `pad_check` (a page, or a template string not saved anywhere, through the
strict syntax check - `data` gives it variables without PHP; `pad_check` with only an app
checks every page), `pad_trace`, `pad_php` (PHP run in a booted application - a `padError`
comes back as the answer), `pad_compare` (the same render or snippet on `HEAD` - a snapshot
from git - and on the working tree, with where they part: a fix shown, a new case shown to fail
on the old engine), `pad_case` (one regression test judged as its suite judges it), `pad_apps`,
`pad_pages`, `pad_builtins`, `pad_reference` (a tag or function in `docs/reference`) and
`pad_test` (`./ci.sh`, or one `suite`, with where each failing test's want and got part).
Tested by `node editors/mcp/test.js`, the `mcp` line of `./ci.sh`.

### Claude Code plugin

`editors/claude` is a plugin marketplace with the plugin `pad`: the language server - after
every Edit or Write of a `.pad`/`.html` page Claude Code gets PAD's own error, no call needed -
the MCP server, and the skill `pad-bug-hunt` (reproduce, prove against the old engine, where a
regression case goes and the answer form its suite reads, the gate). `claude plugin marketplace
add /Users/herbert/pad/editors/claude`, `claude plugin install pad@pad --scope local`; its
launcher runs the servers of the checkout it is used in. Tested by `node editors/claude/test.js`,
the `claude` line of `./ci.sh`.

---

## Applications Overview

| App | Type | Description |
|-----|------|-------------|
| `_common` | Shared | Shared resources and utilities for all applications |
| `admin` | Standard | The web PAD console, the successor of `apps`: every application with its pages, parts and settings, files, search, the pad command (lint, test, migrate, queue, schedule ...) with a history, maintenance, queues, schedules, logs, error dumps, the mail outbox, DATA/ storage, PHP, the PAD settings, git and its own users - behind a login, this machine only |
| `alpine` | Standard | PAD + Alpine: state from PHP with `x-data="{^field}"`, components in `www/` with `Alpine.data`, live search over `$padExpose`, one set of rules for both sides, a list kept in the session, server-sent events |
| `apps` | Standard | Lists all PAD applications with descriptions from README files |
| `charts` | Standard | A showcase of the `{chart}` tag - a menu heading per chart family (every kind of `pad/subs/charts/types/`), every example a page of three cells: the data, the tag, the chart |
| `showcase` | Standard | A showcase of the tags that draw, format and interact - a menu heading per group, a section per tag, every example its template beside the result |
| `tags` | Standard | A page per tag, named with its type prefix - every built-in tag, `{pad:if}`, every kind of the subsystems, `{chart:pie}`, and every sequence type, `{sequence:fibonacci}` - one line, syntax with the tag's own options, a longer description and live examples - with indexes by name, group, form and option, a cheat sheet and a search |
| `types` | Standard | A page per type of tag - the prefix of `{pad:if}`, `{chart:pie}`, `{field:name}` - with what it reaches and its tags linked into the tags app, its place in the order a bare name is tried in, its section of TYPES.md and live examples; indexes by name, order and group, a cheat sheet and a search |
| `classicModels` | Standard | PAD Select over the Classic Models sample database |
| `padSelect` | Standard | A showcase of PAD Select over a music catalogue (database `padSelect`) - tables, relations, views, joins, groups, pages, unions, each example with its declarations, template, result and the SQL PAD sent |
| `cli` | CLI | The `pad` command (`apps/cli/pad`): new, serve, render, lint, export, test - and the cli application |
| `demo` | Standard | Interactive demo with guestbook, todo, contact, counter, clock |
| `develop` | Standard | Development tools for PAD - the source trimmer, the harvest of the reference and the examples, the error listing |
| `edit` | Standard | A browser editor for every application, behind a login, this machine only by default - file tree with new, copy, rename, delete, upload and trash; Monaco with PAD colouring, completion, hover, go-to-definition and PAD's own checks, PHP completion; preview, history, search, git, a terminal, an Xdebug step debugger; the framework's own pad/ in the tree too |
| `games` | Standard | Every game of the games subsystem to play - a card per game, a page per game with the template that plays it, all read from `pad/subs/games/types/` |
| `toys` | Standard | Every toy of the toys subsystem to play with - a card per toy, a page per toy that runs it beside its template, read from `pad/subs/toys/types/` |
| `patterns` | Standard | Every pattern of the patterns subsystem - a card per pattern, a page per pattern drawn beside its template with a seed to change by link, its own example and as a background, read from `pad/subs/patterns/types/` - and a page with every image filter of `{img}` on the same picture |
| `printables` | Standard | Every kind of the printables subsystem - labels, badges, cards, tickets, a certificate, envelopes, letters, invoices, index cards, place cards, folded cards, graph paper, music paper, a wall calendar - a page each with its template beside what it prints and a link to the PDF, sample data from `{fake}` with fixed seeds, read from `pad/subs/printables/types/` |
| `sounds` | Standard | A soundboard of the sounds subsystem - every sound a button with its recipe and its WAV download, and the ways a page plays one, read from `pad/subs/sounds/types/` |
| `inputs` | Standard | Every rich form field of `pad/subs/inputs/` to try - a page per kind in a form that posts back with what the page received, one form with them all checked in the browser too |
| `examples` | Standard | Search the harvested examples of DATA/examples and view one with its sources beside the rendered result |
| `notations` | Standard | Every notation of `pad/subs/notations/` - AsciiMath, chemistry, ABC music, chord boxes, piano keys, braille, morse, semaphore, signal flags, chess, go, draughts, guitar tablature, Karnaugh maps, bridge deals, siteswaps, cube moves - a card per kind, a page per kind with its examples, each template beside its result |
| `hello` | Minimal | Hello World example demonstrating page pairing |
| `htmx` | Standard | PAD + htmx: active search, lazy loading, infinite scroll, inline editing, forms, out-of-band updates and server-sent events - htmx asks for a part, PAD answers its `{fragment}` (by `HX-Target`), each example with a request log and its sources |
| `islands` | Standard | Islands of any framework: one runtime mounts React, Vue, Svelte, Preact and Solid components on PAD's islands, each framework fetched when needed, a store they share, a TypeScript island typed by `pad types`, built by Vite (`_frontend/`) and linked by `{vite}` |
| `kanban` | Offline | A task board on SQLite - cards dragged across (a move posted as JSON with `X-CSRF-Token`), the columns refreshed as a `{fragment}`, every open window kept in step by `padSse` - packed by `pad pack kanban` it runs in the browser, two installed windows sharing one database |
| `ledger` | Offline | A household budget on SQLite built by its `_migrations/` (run on the first request, a later one migrating the kept database) - `{chart}`s, `{progress}` budgets, a `{datatable}`, CSV out (`padFormat=csv`) and in, a quick entry posted as JSON - packed by `pad pack ledger` it runs in the browser |
| `manual` | Standard | Interactive documentation and examples |
| `nono` | Plain PHP | PHP application without PAD templating |
| `notes` | Offline | A Markdown notebook on SQLite - tags, search as you type (a `{fragment}`), pins and colours (`{live}`), pictures and PDFs attached (`padUpload`, `padUploadSend`), a JSON export (`$padExpose`) and import - packed by `pad pack notes` it runs in the browser |
| `pad` | Standard | PAD framework introduction and reference |
| `live` | Standard | Like the playground, but Run saves the page and this HTTP server answers it as a real page, `live/?page/<id>` - its PHP, request values, forms that post back, sessions, scripts, `_common` and its demo database - local requests only; every example of the other applications opens here from its ⚡ icon |
| `playground` | Standard | Type a template and its data - JSON, YAML, XML or CSV recognised on sight, or PHP code as a page's `.php` (a button switches) - in editors with PAD's colours and see the result - local requests only, no PHP functions in the template, shareable through the URL hash; every example of the other applications opens here from its icon top right |
| `react` | Standard | PAD + React: components mounted on islands of PAD pages, JSON answers to `fetch()`, forms checked by PAD with the CSRF header, `{fragment}`s, a cart in the session, polling - fourteen examples, each with its sources |
| `reference` | Standard | Cross-reference and directory utilities |
| `regression/main` | Standard | Automated regression testing for PAD - the runner for the eight suites and the fresh build |
| `regression/pages` | Test | The pages suite: every test is a real page, fetched over HTTP and compared with the answer beside it |
| `regression/framework` | Test | The Framework suite: the engine cases as pages, one fetched per case |
| `regression/regression` | Test | The Regression suite's prediction store - one answer per page of the self-testing applications and the runner |
| `regression/sequence` | Test | The Sequence suite's prediction store - one answer per page of the sequence application |
| `regression/manual` | Test | The Manual suite's prediction store - one answer per page of the manual application |
| `regression/other` | Test | The Other suite's prediction store - one answer per page of every application without a suite of its own |
| `regression/errors` | Test | The Errors suite: the tests that fail on purpose, answered lean under the boot action - no dumps |
| `regression/common` | Test | The pages of the suite that use `_common` - `{example}`, `{demo}`, `{table}` - fetched and compared the same way |
| `sequence` | Standard | Mathematical sequence subsystem demos - with a gallery of every type beside its OEIS entry, a sequence played as notes, and a guess-the-next-term game |
| `sheets` | Standard | Spreadsheets made with `{spreadsheet}` - a budget, a loan calculator in a form, grades with VLOOKUP and XLOOKUP, invoice dates, an editable sheet in a live region - each run beside its template, and a page of every function of `pad/subs/formulas/types/` with its examples on a sample sheet |
| `structure` | Example | Demonstrates nested `_xxx` directories and inheritance |
| `test` | Minimal | A scratch application for trying things out, `_common` switched off |
| `webcomponents` | Standard | Web components: PAD custom tags write custom elements with their shadow root (`{shadow}`, declarative shadow DOM) - cards with slots, tabs upgraded by a module, a rating that posts, a form-associated stepper, a Lit element fed with PAD's JSON |
| `sections` | Standard | A landing page built from the sections and effects with the theme switcher in the bar, and a page per section, effect, theme (its contrast ratios) and palette (charts in it), every example its template beside its result - read from `pad/subs/{sections,effects,themes,palettes}/types/` |

Every other `regression/<name>` application is the test of one feature - a page cache backend, an error action, an output type, maintenance, CORS ... - listed in [apps/README.md](apps/README.md), with the rest.

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
{ignore}<style>body {color: red}</style>{/ignore}  <!-- OK -->
<style>body {color: red}</style>        <!-- WRONG - will parse {color: red} -->
```

### PAD + React Integration

See [REACT.md](docs/REACT.md) for complete React integration documentation including:
- Pattern 1: Static data with {json} tag
- Pattern 2: Dynamic data with {reactData} tag and providers
- Pattern 3: Islands - `<div data-island="Name" data-props="{^field}">` mounted by the react
  app's runtime (`www/react/pad-react.js`); talking back with `$padExpose` JSON, posts with the
  `X-CSRF-Token` header, `{fragment}`s, session state and polling
- Critical: Using `getAttribute('data')` NOT `dataset.data`
- File organization and common patterns

### Manual Pages and Fragment Files

When creating documentation with examples:

**Manual page** (`manual/topic.pad`) - Contains all explanatory text:
- The title as `{meta title='...'/}` on the first line - the frame's bar prints it as the
  page's one `<h1>` (from the file name when there is no meta); never an `<h1>` of its own
- Introduction and description
- Section headings (`<h2>`, `<h3>`)
- Explanatory paragraphs
- Tables and reference content
- Use `{example 'fragments/topic_N'/}` to embed working examples

**Fragment files** (`fragments/topic_N.pad` + optional `topic_N.php`) - Minimal executable code only:
- Just the code that demonstrates the feature
- No `<h3>` headings or `<p>` introduction text
- Can have paired `.php` file for server data if needed
- Should be small and focused on one concept

**Example:**
```html
<!-- manual/pipes.pad -->
{meta title='Pipes - Transform Output'/}
<h3>Variable Pipes</h3>
<p>Apply functions to variables:</p>
{example 'fragments/pipes_1'/}

<!-- fragments/pipes_1.pad -->
<p>Original: {$name}</p>
<p>Uppercase: {echo $name | upper/}</p>
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
