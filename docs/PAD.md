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

A `pagename.html` file is a template exactly like a `.pad`: it pairs with the `.php` and PAD
tags resolve in it, so an existing html file dropped in the application directory is a page.
When both a `.pad` and an `.html` exist, the `.pad` wins.

### URL Structure
Pages are accessed via query string:
- `/myapp/` → `index.pad`
- `/myapp/?about` → `about.pad`
- `/myapp/?admin/users` → `admin/users.pad`

### Directory Hierarchy

PAD uses a hierarchical directory structure for inheritance:

```
APP/
├── _guard.php          # Access check for every page below (FALSE answers 403)
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

1. Create your app directory under `apps/myapp/`
2. Create the entry point `www/myapp/index.php`:

```php
<?php
  include __DIR__ . '/../pad.php';
?>
```

`www/pad.php` detects the platform, derives the app name from the request URL, and includes `pad/pad.php`, which defines the framework constants (`PAD`, `APP`, `APPS`, `DATA`, `COMMON`).

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

### Comments

```
{# dropped before the template is scanned #}
{-- the same, in the form the editor kits toggle --}
```

### Variables

```
{$simple}                      {# Simple variable #}
{$user.name}                   {# Object/array property #}
{$items[0]}                    {# Array index #}
{!text}                        {# Raw - skips the sanitize chain #}
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

A field tag pipes as it stands - `{$var | upper}` - and a literal goes through `{echo}`: `{echo 'text' | upper}`.

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

Access iteration state with `property@tag` syntax - as a tag pair (content renders only when
the property is true), in a ternary, or as a value in any condition (`{if first@items}`):

```
{items}
  {first@items}<ul>{/first@items}
  <li class="{odd@items ? odd : even}">{$name}</li>
  {last@items}</ul>{/last@items}
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
{field "name from users"}  # Database single value (the field: prefix reads a variable)
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

A tag with a `.pad` template is a component: the caller's content goes to `@content@`,
`{slot 'footer'}...{/slot}` pairs in the content fill the template's named slots - each use
of the tag has its own - and `{parms title, tone='info'}` declares the parameters, a bare name
required and `name=default` filled in.

### Database Operations

```php
// In PHP
$user = db("RECORD * FROM users WHERE id={0}", [$id]);
$users = db("ARRAY * FROM users ORDER BY name");
$count = db("FIELD COUNT(*) FROM users");
$exists = db("CHECK users WHERE email='{0}'", [$email]);
```

```
// In templates (the tag name becomes the db() command word)
{field "count(*) from users"}
{record "* from users where id=5"}
  Name: {$name}
{/record}
{array "* from users order by name"}
  <tr><td>{$name}</td></tr>
{/array}

// Or declare tables in _lib/select.php ($padSelect) and use them as tags
{users}
  {$name} - {$email}
{/users}
```

See [DATABASE.md](DATABASE.md) for the `db()` command words and the Select subsystem.

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
- Validates `APP` and `DATA` constants
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
│   ├── @page@ placeholder
│   └── _exits.pad files (inner to outer)
└── build/page.php      → Process page
    ├── _inits.php execution
    ├── page.php execution → returns data
    ├── page.pad template
    └── _exits.php execution
```

The `@page@` placeholder is replaced with the page content, creating a nested structure where parent templates wrap child content.

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
| `!` | Raw field (no sanitize chain) | `{!snippet}` |
| `?` | Field as a query fragment | `{?id}` → `&id=7` |
| `^` | Field as JSON for an attribute | `data-props="{^product}"` |
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
│  ├── Validate APP/DATA                                          │
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
│  ├── Build base structure (_inits.pad + @page@ + _exits.pad)     │
│  ├── Execute _inits.php                                         │
│  ├── Execute page.php → get data                                │
│  ├── Load page.pad template                                     │
│  └── Replace @page@ with content                                 │
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
├── _inits.pad                # Wraps all pages (use @page@ placeholder)
├── _exits.php / _exits.pad   # Runs after all pages
├── _lib/                     # Auto-included PHP functions
├── _tags/                    # Custom template tags
├── _functions/               # Custom pipe functions
├── _data/                    # Static data files (JSON, XML)
├── _include/                 # Template snippets
├── _callbacks/               # Iteration callbacks
├── _options/                 # Custom tag options
├── _events/                  # Event hooks: error, sql, curl, output - every request
└── _config/config.php        # App configuration
```

## Advanced Features

### Caching

Two levels. The page cache (`$padCache`) answers a whole anonymous GET from a store -
apcu, db, file, memcached or redis. The fragment cache keeps one section of a page
rendered while the rest stays dynamic:

```
{cache 'top-products', ttl=300}  {# a named section, five minutes #}
  {topProducts}<li>{$name}</li>{/topProducts}
{/cache}

{expensive_query cache=3600}      {# any tag: its handler is skipped on a hit #}
  ...
{/expensive_query}
```

`vary=` adds what else the rendering depends on (`vary=$userId`); `$padFragmentCache`
picks the store - `'file'` (default), `'apcu'` or `FALSE`; `padFragmentForget('top-products')`
drops a named section when what it shows has changed.

### Early flush

`{flush}` in the wrapper, after `</head>`, sends the page rendered so far at once, so the
browser fetches stylesheets and fonts while a slow part of the page renders. Tidy, whole-body
gzip, the ETag and `Content-Length` are off for such a request - they need the whole body.

### Clean URLs

A path names a page too - `/shop/products/42` - mapped onto the file tree, a bracketed name
binding the segment it stands for: `products/[id].pad` gets `$id = '42'`, `blog/[year]/[slug].pad`
two variables, `docs/[path+].pad` the rest of the path. `?products/42` resolves the same way,
and every `?page` URL keeps working. `$padCleanUrls = TRUE` makes `$padGo` write the clean
form; the server hands the paths to the entry point - `FallbackResource /shop/index.php` in
Apache, nothing at all under `php -S`.

### JSON and CSV from the same page

The page's `.php` already produces the data, so the same page can answer it instead of its
template - for `?orders&padFormat=json` (or `csv`) and for an `Accept: application/json` (or
`text/csv`) header. Only what the page names leaves the server:

```php
// orders.php
$orders    = db ( "ARRAY * FROM orders" );
$total     = 123.45;
$padExpose = [ 'orders', 'total' ];
```

JSON is one object keyed by those names, CSV the first of them that is a list, with a header
row. A page that exposes nothing renders HTML as always - asked outright with `padFormat=`,
it answers 406. `$padOutputType = 'json'` turns a whole application into one that answers
data.

### Layouts and Components

A component declares the assets it needs where it is used; the layout prints them where
they belong. `{stack}` is filled in after the whole page has rendered, so a stack in the
`<head>` gets the pushes of everything below it:

```
<head>{stack 'scripts'}</head>

{push 'scripts', once='chart'}     {# once per key, however often the component is used #}
  <script src="chart.js"></script>
{/push}
```
Remote data has a cache of its own: `{curl 'https://...', ttl=600}`, `data='https://...'`
with a `ttl=` and a `_data/*.curl` file with a `<ttl>` keep the answer that long, and serve
the last good copy - logging the failure - when the source is down. `$padCurlCache` picks
the store: `'file'` (default), `'apcu'`, `'redis'`, `'memcached'` or `FALSE`.

A page picks a layout of its own and overrides its regions, resolved in the text while the
page is assembled:

```
{extends '_layouts/report'}                {# instead of the _inits.pad/_exits.pad frame #}
{block 'title'}Sales report{/block}
{block 'sidebar'}{parent}<a href="?export">Export</a>{/block}
```

Without `{extends}`, a page's `{block 'title'}` overrides the region of that name in its
directories' wrappers.

A page without PHP says what a `.php` would with `{meta}`, read while the page is assembled:

```
{meta title='Monthly report', layout='_layouts/print', cache=600}
```

### AJAX Support

Handle AJAX requests seamlessly:

```
{ajax}
  {# Content returned as AJAX response #}
{/ajax}
```

A named response fragment lets one template serve both the full page and the region an
update refreshes - `?orders&padFragment=order-list` answers with that fragment alone:

```
{fragment 'order-list'}
  {orders}<li>{$number}</li>{/orders}
{/fragment}

{ajax 'orders', fragment='order-list'}           {# or hx-get="?orders&padFragment=order-list" #}
```

### PAD in the Browser

PAD runs on PHP compiled to WebAssembly: `php wasm/build.php` bundles the engine into
`www/wasm/pad-bundle.json`, and `www/wasm/index.html` loads PHP 8.4 from the `@php-wasm`
packages on jsDelivr, writes the engine into its memory and renders templates in the browser
tab, with no PAD server. Experimental - see `wasm/README.md` for what was verified.

### Live Regions

A `{live}` region re-renders on the server when something in it is clicked, submitted or
changed, and swaps itself in - no JavaScript of the application's own:

```
{live 'counter'}
  <p>{$count}</p>
  <button pad-click="add" pad-value="{$count}">+1</button>
{/live}
```

The page's PHP reads the event with `padLiveEvent()` and `padLiveValue()`; the response to
the event is that region's content alone.

### Sandbox Execution

Execute code in isolation:

```
{sandbox}
  {# Isolated execution environment #}
{/sandbox}
```

`{sandbox}` isolates PAD's state, not PHP. To try templates someone else typed, use the
playground (`apps/playground`, `http://localhost/pad/playground/`): it answers local
requests only, renders with `$padPhpFunctions = []` and `$padRequestVars = []`, limits run
time and source and output size, and shows the result in a sandboxed frame. The URL hash
carries the template and its data, so a link shares an example.

### Event System

An application hooks into four moments of every request through its `_events/` directory -
`error.php`, `sql.php` (each `db()` statement, with its time), `curl.php` (each remote
fetch) and `output.php` (the final page, which the hook may change). The engine's own hooks
in `pad/events/` serve the info modes - trace, stats, xref - and run only under `$padInfo`.

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
$padOutputType    // 'web', 'file', 'download', 'console', 'json', 'csv'
$padExpose        // The page variables answered as JSON or CSV (set in the page's .php)
$padCleanUrls     // Links in the clean form, /shop/products/42
$padCache         // Enable caching
$padCheckOutput   // Check the finished HTML of local requests (ids, alt, labels, links)
$padCoverage      // Record template coverage of local requests (TRUE or a run name)
$padRecord        // Record GET requests with their answers for replay (TRUE or a store name)
$padToolbar       // 'local': the debug toolbar on this machine's own web pages
$padReload        // 'local' / 'engine': live reload of this machine's own web pages

// Database
$padSqlHost
$padSqlDatabase
$padSqlUser
$padSqlPassword
```

## Debugging

- An error names its place in the template - file, line, column, the lines around it with a
  marker, a "did you mean" for a near field, function or tag name, what included it and the
  wrappers around it - on the error page, in the JSON body for local tools (`template`) and in
  the log line; the build's source map (`pad/lib/source.php`) traces the spot back through the
  joined `_inits`/page/`_exits` text
- Set `$padInfo = 'trace'` for execution tracing
- Set `$padToolbar = 'local'` for a collapsible debug toolbar at the foot of every local web
  page: time and memory, the levels and the tag tree, the SQL statements, page and fragment
  cache, the template files read, the application's variables and the session
  (`pad/lib/toolbar.php`); never shown to a visitor, a `&padInclude` fragment or the page cache
- Set `$padReload = 'local'` in `_config/config.php` for live reload: a local page polls
  `?page&padReload` for the newest file time under the application, its `www/` directory and
  `_common` (`'engine'`: `pad/` too) and reloads itself when a file is saved
  (`pad/lib/reload.php`); never for a visitor, a fragment, the page cache or `pad export`
- Use `{dump}` tag for variable inspection
- Use `{trace}` tag for execution trace
- Set `$padCheckOutput = TRUE` to have every local HTML response checked once it has rendered: duplicate ids, images without alt, form fields without a label and `?page` links to pages that do not exist are named in a panel at the end of the page and counted in a `PAD-Output-Check` header (one request: `?page&padCheckOutput`; every application's templates at once: `develop/?links`)
- Set `$padCoverage = TRUE` (or a run name) to record template coverage of local requests - which templates were read, which tags ran, which `{if}`/`{case}` branch was taken - in `DATA/coverage/<run>.jsonl`; `develop/?coverage` starts and stops a recording of every application around a suite run and shows each template with what never ran marked
- Set `$padRecord = TRUE` (or a store name) to keep every GET answered 200 - one that brought no cookie but PAD's own ids - with its answer in `DATA/replay/<store>/`; `develop/?replay` replays a store against the current code and names every page whose answer changed. A replay never writes: `db()` refuses statements that change the database and `padFilePut` the application's own writes
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

1. **Use caching** - Cache expensive operations; remote data takes a `ttl=`
1. **Fetch remote sources together** - `padPrefetch ( [ 'rates' => $url1, 'weather' => $url2 ] )`
   in the page PHP fetches them in parallel and keeps each as named data for `{rates}`
2. **Limit data** - Use `first`, `page`, `rows` options
3. **Sort server-side** - Let database handle sorting when possible
4. **Minimize nesting** - Deep nesting impacts performance

What the engine itself keeps for the length of a request: which global names are its own -
every tag's PHP half snapshots the application's variables before and after it runs, and
sifting the symbol table name by name was a third of the time of a page full of tags - the
tokens of every clean expression, so a loop parses its expressions once, and the @else@ pair
scan, now made only when a level holds an `@else@`. Measured over ten pages of the manual,
reference, demo and sequence applications, a request takes about a third less time. The
template text is still rescanned each pass - there is no compiled form - and the page build
and the name lookups were each about 1% of a request, too little to be worth a cache that
outlives it.

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

1. **A literal pipes through `{echo}`** - `{echo 'x' | upper}`; a field tag pipes as it stands, `{$var | upper}`
2. **Arithmetic needs space** - `{echo $x | + 1}` not `| +1`
3. **Quote literal strings** - `{count 'items'}` not `{count items}`
4. **No inline CSS/JS** - PAD parses `{ }` as tags; use external files
5. **Use `padRedirect()`** - Don't use `exit` or `die` in PAD apps

## Testing

Run regression tests by visiting `/regression` in browser. Tests compare current output against stored HTML snapshots.

Real traffic can be a suite too: with `$padRecord = TRUE` every GET request is kept with its answer, and `develop/?replay` re-asks every recorded page and names the ones whose answer changed.

To see what the suites reach, record template coverage around a run: `develop/?coverage&start=suites`, `./ci.sh`, `develop/?coverage&stop`, then read `develop/?coverage&run=suites`.

Speed is tracked the same way: the develop application's Benchmark times every page, keeps
each run with its commit in `DATA/benchmark/`, and names the pages that got slower than
their median over the last runs; its history page charts the runs. `CI_BENCH=25 ./ci.sh`
makes a 25% slowdown fail the gate like a failing suite.

## License

See LICENSE file for details.

## Reference Documentation

- [TAGS.md](reference/TAGS.md) - All template tags
- [FUNCTIONS.md](reference/FUNCTIONS.md) - All pipe functions
- [OPTIONS.md](reference/OPTIONS.md) - All tag options
- [PROPERTIES.md](reference/PROPERTIES.md) - All iteration properties
- [EVAL.md](reference/EVAL.md) - Expression evaluation internals
- [sequences/](sequences/README.md) - Sequence subsystem
