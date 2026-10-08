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
├── editors/  # Editor kits, the language server, the command-line renderer
├── docs/     # Documentation
├── wasm/     # PAD in the browser: wasm/build.php bundles the engine for www/wasm/ (PHP compiled to WebAssembly);
│             #   wasm/app.mjs packs a whole application into a directory that runs from file://
└── DATA/     # Runtime data (logs, cache, dumps) - writable, excluded from git
```

---

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

## Template emails

`{mail to=$email, template='order', subject='Order confirmation'}` renders `_mail/order.pad`
(HTML) and `_mail/order.txt` (text - made from the HTML when absent), after `_mail/order.php`,
inside the `_inits.pad`/`_exits.pad` layout of that `_mail/`; as a pair without `template` the
content is the HTML part. `from`, `cc`, `bcc`, `replyTo` too. From PHP:
`padMail ( $to, 'order', $subject, [ 'order' => $order ] )`. `$padMailTransport`: `'file'`
(default, `.eml` under `DATA/mail/<app>/` - no mail server needed), `'mail'` (PHP `mail()`),
or the name of an app function that gets the message (SMTP through a library).

## Early flush

`{flush}` - after the wrapper's `</head>` - sends the page rendered so far at once, so the
browser loads the stylesheets while a slow part below renders. It switches off tidy, whole-body
gzip, the ETag/304, `Content-Length` and the page cache for the request; it stands at the top
level of the page or wrapper (strict mode says so elsewhere) and does nothing in a page
rendered by `{page}`, for a non-web output type, or in a request answered with one fragment or
live region.

## Sitemap from the file tree

`{sitemap}...{/sitemap}` lists every page of the application - `{$page}`, `{$url}`,
`{$lastmod}` - walked from the files; `{sitemap 'docs'}` one directory. With
`$padSitemap = TRUE`, `?sitemap.xml` answers the sitemap XML and `?robots.txt` points to it.
Left out: `_` entries, directories with a `_guard.php`, bracketed routes, action pages (no
template, PHP that only redirects), `{meta sitemap=false}` and `$padSitemapSkip` names.

## JSON and CSV from the same page

A page answers its data instead of its template when asked - `?orders&padFormat=json` (or
`csv`), or an `Accept: application/json` / `text/csv` header. What may leave the server is an
explicit list in the page's `.php`; a page that names nothing answers HTML only:

```php
// orders.php
$orders    = db ( "ARRAY * FROM orders" );
$total     = 123.45;
$padExpose = [ 'orders', 'total' ];    // never an engine name (pad*, pq*, _*)
```

- JSON is one object keyed by the exposed names; CSV is the first exposed list - a header
  row of every key, then a line per row, a text cell starting with `=`, `+`, `-` or `@` written
  with a `'` in front so a spreadsheet does not run it. The templates do not run for such a
  request.
- `padFormat=` asked outright on a page that exposes nothing, a format that does not
  exist, or CSV without a list answers 406; asked only through `Accept`, the page renders
  as HTML. An exposing page sends `Vary: Accept`.
- `$padOutputType = 'json'` (or `'csv'`) in `_config/config.php` makes every page of an
  application answer data; `{output 'json'}` does it from a template.

## Maintenance mode

`pad down shop --secret=letmein --retry=120 --message='Back at ten'` takes an application
down: every web request answers 503 with a Retry-After - `_errors/503.pad` when it has one,
else the message as one line - before the page cache and before any of it runs, a page that
is not there too; `?letmein` gives that browser a cookie that lets it through for twelve
hours. `pad up shop` brings it back; `pad down` lists what is down. The record is
`DATA/maintenance/<app>.json` (the secret only as a hash); the command line is never held back.

## Custom error pages

With `$padErrorPages` (on by default) an application's `_errors/404.pad` - or `4xx.pad`,
`5xx.pad` for a whole class, `.html` too - answers instead of the plain line, the status
kept: a page not found, a guard's 403, a post without its CSRF token, `padAbort(429)`,
maintenance's 503, and under the 'pad' error action a visitor's 500 (this machine keeps the
report). `$status`, `$message`, `$page` (and `$id` on a 500); `_errors/_inits.pad` and
`_exits.pad` with `@page@` are its frame - not the application's `_inits`, `_lib` or guards,
which may be what failed. A page that fails itself answers the plain line - no loop; json
and csv output get the line as well.

## Health check

With `$padHealth = TRUE`, `?up` (when the application has no page `up`) answers
`{"status":"ok","checks":{"data":"ok","database":"ok"}}` - DATA/ writable, the database
answering `SELECT 1` when `$padSqlDatabase` is set, and what `_health.php` returns
(`[ 'queue' => TRUE | FALSE | 'why' ]`). One failing check: 503 and `"fail"`; a database
failure shows "fail" only, the reason goes to the log.

## Cross-origin requests (CORS)

`$padCors = [ 'origins' => [ 'https://app.example.com' ], 'methods' => [ 'GET', 'POST' ],
'headers' => [ 'Content-Type' ], 'credentials' => FALSE, 'maxAge' => 600, 'expose' => [] ]`:
a listed origin (or any, with `'*'`) gets Access-Control-Allow-Origin and the rest, with
`Vary: Origin`; a preflight OPTIONS is answered 204 at once, before the CSRF check and the
application. An origin not listed gets no CORS header. `[]`, the default, sends none.

## Logging in, gates and feature flags

`padLogin ( $row, $remember )` keeps a user row (an `id` at least; its password/token fields
left out) as the one logged in to this application, with a new session id; `padLogout ()`,
`padUser ( 'name' )`, `padUserId ()`, `padAuthCheck ()`. `padAttempt ( $password, $hash,
$row, $remember, $newHash )` checks and logs in. `padAuthRequire ()` (a page or `_guard.php`)
sends a guest to `login`, keeping the address; `padRedirectIntended ()` goes back to it after
the login. Remember-me is a signed cookie, no table: `$padAuthRemember` names the function
that finds a row by id. `padPasswordToken ( $email, $hash )` / `padPasswordTokenCheck` - a
reset token that dies once the password changes, or after an hour.
```
{auth}Hello {$name}{else}<a href="?login">Log in</a>{/auth}     # the user's row is the data
{guest}...{/guest}
{can 'edit-post', $post}<a href="?edit">Edit</a>{else}read only{/can}
{feature 'search2'}...{else}...{/feature}
```
In `_lib`: `padGate ( 'edit-post', fn ( $user, $post ) => $user ['id'] == $post ['user_id'] )`,
`padGateBefore ( fn ( $user, $ability ) => $user ['role'] == 'admin' ? TRUE : NULL )`; then
`padCan`, `padCannot`, `padAuthorize` (403), `{can}` / `{cannot}` - inside `{posts}`, `$posts` is
the row. A guest reaches only a gate whose user is `?array`.
`$padFeatures = [ 'search2' => 0.25, 'beta' => 'isBeta', 'dark' => TRUE ]` - a share is
decided stably per user (or `padFeatureId` cookie); `padFeature ( 'search2' )`,
`padFeatureOverride ( 'x', TRUE )` for tests. An unknown ability or flag is FALSE and, under
the strict check, an error.

## Asset versioning

`{asset 'charts.css'}` / `padAsset ( 'charts.css' )` is `/<app>/charts.css?v=<hash of the
contents>` of `www/<app>/charts.css` - safe behind a far-future cache header; `{asset
'app.js', tag}` writes `<script src defer>` (`<link rel="stylesheet">` for .css, module for
.mjs), with the nonce when `$padCsp` asks for one. A missing file is the plain address and a
strict error; a name never leaves `www/<app>/`.

## Migrations and seeders

`_migrations/` holds the application's schema as changes, run in name order:
`2026_10_01_120000_create_orders.sql` (statements split on `;` - a trigger body and a
`DELIMITER` line respected), with `..._create_orders.down.sql` as its way back, or
`..._add_status.php` returning `[ 'up' => "SQL" | fn, 'down' => "SQL" | fn ]`. `pad_migrations`
in the app database records what ran, in batches. `padMigrate ()` runs the pending ones as one
batch, each in a transaction (on MySQL a CREATE/ALTER/DROP commits by itself - one change per
migration), stopping at the first failure with the file and statement named;
`padMigrateStatus ()`, `padMigrateRollback ( $steps )`, `padMigrateFresh ( TRUE )` (drops every
table). `_seeds/*.php` - plain PHP with `db ()` - run by `padSeed ( [ 'users' ] )`. Fake data,
reproducible after `padFakeSeed ( 42 )`: `padFakeName`, `padFakeEmail`, `padFakeCity`,
`padFakeSentence`, `padFakeNumber ( 1, 9 )`, `padFakeDate ( '-1 year', 'now' )`,
`padFakeUnique ( 'email', 'padFakeEmail' )` ...; `padFactory ( 'customers', 25, fn ( $i ) =>
[ 'name' => padFakeName () ] )` inserts with placeholders and returns the rows with their ids.
`pad migrate shop [--pretend|--status|--rollback[=n]|--fresh --force|--seed|--new=name]`,
`pad seed shop [name]`.

## Queues and the scheduler

`padQueue ( 'sendInvoice', [ 'order' => 42 ], delay: 60, queue: 'default', tries: 3, backoff: 10 )`
stores a job; `_jobs/sendInvoice.php` is its handler - the data's keys are its locals, `$padJob`
the job, the application's `_lib`, config and database at hand. Returning FALSE, throwing or a
`padError` fails the attempt: retried after the backoff, then kept with the failed jobs
(`padQueueFailed`, `padQueueRetry ( $id | 'all' )`, `padQueueFlush ( 'failed' )`). Jobs are files
under `DATA/queue/<app>/<queue>/`, claimed by a rename - two workers never run one job.
`pad work <app>` is the worker (`--once` for cron and tests); `padQueueWork ()` works a queue from
PHP; `padMail ( ..., [ 'queue' => TRUE ] )` sends through it.

`_schedule.php` returns the entries - `[ 'every' => 'day at 03:00', 'job' => 'cleanup' ]`,
`[ 'cron' => '*/5 * * * mon-fri', 'job' => 'sync', 'queue' => TRUE, 'overlap' => FALSE ]` - and
one cron line, `* * * * * /path/apps/cli/pad schedule --all`, runs every application's due
entries; `pad schedule <app> --list` shows each with its next and last run. `padCronMatch`,
`padCronNext` and `padCronEvery` from PHP.

## HTTP fakes for tests

`padCurlFake ( [ 'https://api.example.com/*' => [ 'status' => 200, 'data' => '{"a":1}' ], '*' => 404 ] )`
answers every fetch of the request - `{curl}`, `data='https://...'`, padCurl, padPrefetch, a ttl
fetch (which skips its cache) - from the fakes, a closure answering per request; a URL no fake
matches is an error without `'*'`. `padCurlRecorded ()` lists the calls, `padCurlFakeStop ()` ends it.

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
├── _lang/                 # Translation catalogs: en.json, nl.json ... ({trans 'key'})
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
| `_functions/` | Pipe functions | `{echo $x \| myfunc}` → `myfunc.php` |
| `_callbacks/` | Iteration hooks | `callback='name'` → `name.php` |
| `_options/` | Tag options | `{tag name}` → `name.php` on the template; `end/name.php` on the rendered result |
| `_events/` | Event hooks | `error.php`, `sql.php`, `curl.php`, `output.php` - run on every request |
| `_config/` | App config | `config.php` overrides |
| `_data/` | Static data, named queries | XML, JSON, YAML, CSV; `name.sql` runs as `{name}` |
| `_scripts/` | Shell scripts | On demand |
| `_mail/` | Email templates | `{mail template='order'}` → `order.pad` + `order.txt` |
| `_tests/` | Application tests | `pad test <app>` - `name.pad` + `name.txt` answer |
| `_lang/` | Translation catalogs | `nl.json` holds the keys `{trans 'key'}` looks up in locale `nl` |
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

### Variables
```
{$variable}                    # Output variable
{$user.name}                   # Object/array property
{$items.0}                     # Array element by key - a dotted path, as above
{$$name}                       # Indirection - the name comes from another variable (an application one, never pad*)
{!text}                        # The same field, raw - it skips the sanitize chain {$x} runs
{?text}                        # The field as a url query fragment: &text=url+encoded
{#name}                        # A parameter or option of the tag
{&name}                        # A property of the tag
{^name}                        # The field as JSON, escaped for an HTML attribute
```

### Pipe Functions
```
{echo $name | upper}              # Uppercase
{echo $text | trim | lower}       # Chained functions
{echo $date | date('Y-m-d')}      # With parameters
{echo $value | + 1}               # Arithmetic (space required!)

{$name | upper}                   # A field tag pipes too - {echo} is for literals
{echo $value | +1}                # WRONG - needs space before 1
```

### Lookups Between Data Sets
```
{orders}
  {$number}: {echo $customer_id | lookup('customers', 'id', 'name') | ?? 'unknown'}
{/orders}
```
`lookup(set, key, field)` finds the row of another set - a `{data}` block, a page array, a
`_data/` file - whose key equals the value; the set is indexed once per request. A miss is
`''`; without a field the answer is the row's first field other than the key.

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
  {increment $i}
{/while}

{until $count eq 0}
  Countdown: {$count}
  {decrement $count}
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

### Grouping with subtotals (group option)
```
{orders group='customer', sum='total'}
  <h2>{$customer}: {$count} orders, {$total}</h2>
  {rows}<p>{$number}: {$total}</p>{/rows}
{/orders}
```
Each group is an occurrence with the grouping field, `$count` and its own rows as `rows`
(inside `{rows}` a field is the row's own value). `sum`, `avg`, `min`, `max` name fields to
aggregate: under the field's own name and as `$sum_total`, `$avg_total`, ... Groups keep the
order of first appearance - a `sort` written after the group sorts the groups.

### Page links (pager tag)
```
{products page=$pg ?? 1, rows=12}<article>{$name}</article>{/products}
{pager 'products', window=2}        # ‹ 1 … 4 5 [6] 7 8 … 20 ›
```
The pager follows the paged tag (it reads the total the page handling booked), links through
`$padGo` keeping the other query values, sets the variable `page=` was written with (`pg`),
and marks the current page `aria-current="page"`. As a pair it iterates the links as rows -
`$kind`, `$page`, `$label`, `$href` - for markup of your own.

### Recursive Trees
```
<ul>
{tree 'menu', children='items'}
  <li>{$title}{branch}<ul>{recurse}</ul>{/branch}</li>
{/tree}
</ul>
```
`{recurse}` renders the tree's body again for the current row's children, `{branch}` only
when there are any, and `depth@tree` is 1 for the top rows, one more per level. The rows come
from the first parameter or `data=`; `children` defaults to `children`.

### Loop Control
```
{continue 'tagname'}    # Skip to next iteration (like PHP's continue)
{cease 'tagname'}       # Soft stop (graceful end)
{break 'tagname'}       # Hard stop (immediate exit)
```

### Iteration Properties (property@tag syntax)

A property is written as a tag pair, in a ternary, or as a value inside a condition -
`{if first@items}` reads the property alone, `{if current@items eq 2}` with an operator. A
property name followed by `@` and a target is one reference inside an expression; only the
names in `pad/properties/` read that way, and any other word before `@` leaves `@` as the
current-value placeholder. Without a target - `{if current@ eq 2}` - it is the loop the
expression stands in; inside an `{if}` or a `{case}` every property spelling belongs to the
loop around it. The sigil decides a name collision: with a row field named
`first`, `{if first@orders}` is still the iteration state and `{$first@orders}` is the
field.

```
{items}
  {first@items}<ul>{/first@items}
  <li class="{even@items ? even : odd}">{$name}</li>
  {last@items}</ul>{/last@items}
  Index: {current@items} of {count@items}
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
  <tr style="background: {switch '#fff', '#eee'}">
    <td>{$name}</td>
  </tr>
{/items}
```

### Forms
```
{form 'contact'}                                      # posts back, CSRF token + name
  {input 'email', type='email', label='E-mail', required}
  {textarea 'message', label='Message', rows=6}
  <button>Send</button>
{/form}
```
The fields refill from the post when their form came back and show the message
`padValidate` found - in the page's PHP:
`if ( padPosted ( 'contact' ) ) $errors = padValidate ( [ 'email' => 'required|email' ] );`
Rules: required, email, url, numeric, integer, min:n, max:n, in:a,b, regex:/x/, same:field,
accepted, date. A list passes `required` and `in:` (every item) only, and only on a field
named for one (`tags[]`, in the rules too); on a field of one value it is refused - "must be a
single value".

The rules can stand on the fields instead - checked before any PHP runs:
```
{form 'contact', error='Please correct the errors below.'}     # the message above the fields
  {input 'email', type='email', label='E-mail', rules='required|email'}
  {textarea 'message', label='Message', rows=6, rules='required|max:2000'}
{/form}
```
A post of the form is validated against them from the page's text (lib/form.php), so
`padPosted ( 'contact' )` is TRUE only for a post that kept them - the PHP stores and
redirects, a failed post renders refilled with its messages (`padFormFailed`). Only the
page's own template (and its `_inits.pad`/`_exits.pad`) is read, literal rules only: rules in
a snippet, custom tag, `{page}` or layout, from a variable, in an `action=`/GET form or on a
file field are errors.

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

### Layouts (extends / block / parent)
```
{extends '_layouts/report'}                  # replaces the _inits/_exits frame
{block 'title'}Sales{/block}                 # overrides the layout's {block 'title'}...{/block}
{block 'sidebar'}{parent} <a>Export</a>{/block}
<p>the rest goes to the layout's @page@</p>
```
Resolved in the text while the page is assembled. Without `{extends}` a page's blocks
override the regions of its directories' `_inits.pad` - `{block 'title'}` sets the wrapper's
title without PHP. The block name is always quoted (`{block}` alone is the `_common` snippet).

### Components (slots / parms)
```
{card title='Revenue'}                       # _tags/card.pad:
  <strong>{$revenue}</strong>                #   {parms title, tone='info'}
  {slot 'footer'}<a>Report</a>{/slot}        #   <h2>{#title}</h2> @content@
{/card}                                      #   {slot 'footer'}<footer>@content@</footer>{/slot}
```
A `{slot}` pair directly in a custom tag's content is a fill for that use of the tag; any
other `{slot}` is where a fill goes, its content the default (`@content@` in it frames the
fill). `{parms}`: a bare name is required, `name=default` fills in; an undeclared parameter
is an error under the strict check.

### Stacks (push / stack)
```
<head>{stack 'scripts'}</head>                # filled in after the whole page rendered

{push 'scripts', once='chart'}                # where a component needs it; once per key
  <script src="chart.js"></script>
{/push}
```
`once` without a key drops a push whose text the stack holds; `padStackPush('scripts', $html)`
from PHP. A push inside a `{cache}` section is made again on every hit.

### Page Metadata
```
{meta title='Monthly report', layout='_layouts/print', cache=600}
```
Read while the page is assembled, after the PHP: `title=` (any non-engine name) becomes
`$title` for the wrapper, `layout=` is `{extends}`, `cache=` the page's own cache time (0 keeps
it out) when the app caches; `access=`/`sitemap=` are kept for `padMeta('access')`.

### Response Fragments
```
{fragment 'order-list'}<ul>{orders}<li>{$number}</li>{/orders}</ul>{/fragment}
```
`?orders&padFragment=order-list` (or `$padFragmentOnly` set in PHP) answers with that
fragment alone - for HTMX swaps and `{ajax 'orders', fragment='order-list'}`; a normal
request renders the whole page. The page cache answers a script's request for a part -
`HX-Request`, `X-Requested-With`, `Turbo-Frame`, `X-Up-Target` - fresh and never stores a
fragment the PHP chose, since its key is the address.

### HTML attribute helpers
```
<button {attrs disabled=$busy, title=$help, aria-expanded=$open}>Save</button>
<div class="{classes 'panel', active=$isActive, invalid=$hasErrors}">
```
A false boolean attribute is left out, a true one written bare; values are escaped. Items
are attribute names (dashes allowed), never engine options.

### Fragment Cache
```
{cache 'top-products', ttl=300}            # a named section, kept five minutes
  {topProducts}<li>{$name}</li>{/topProducts}
{/cache}

{expensive cache=3600, vary=$country}...{/expensive}   # any tag; its handler skips on a hit
```
`$padFragmentCache` = `'file'` (default), `'apcu'` or `FALSE`; `padFragmentForget('name')`
drops a section when what it shows has changed.

### Remote data with a cache
```
{curl 'https://api.example.com/rates.json', ttl=600}
{pad data='https://api.example.com/rates.json', ttl=600} {$rate} {/pad}
_data/rates.curl:  <curl><url>https://...</url><ttl>600</ttl></curl>
```
The answer is kept `ttl` seconds; a source that fails afterwards gets the last good copy
served and the failure logged. `$padCurlCache`: `'file'` (default), `'apcu'`, `'redis'`,
`'memcached'`, `FALSE`. From PHP: `padCurlCached ( $input, $ttl )`.

Several sources on one page are fetched in parallel from the page PHP - the wait is the
slowest answer, not the sum - and read as named data:
```php
padPrefetch ( [ 'rates' => 'https://example.com/rates.json', 'weather' => 'SELF://api/?weather' ], 600 );
```
`{rates}{$code}: {$rate}{/rates}` - the ttl is optional and works as above.

### Translations and locale formatting
```
{trans 'cart.title'}                          # _lang/<locale>.json: "cart.title": "Your cart"
{trans 'cart.items', count=$n}                # "%d item|%d items" - count picks the form
{trans 'greeting', name=$user}                # "Hello :name"
{echo 'cart.title' | trans}                   # the pipe form
{$price | currency('EUR')}                    # € 1.234,50 in nl, €1,234.50 in en
{$created | localDate('long', 'short')}       # date and time styles, or an ICU pattern
```

### Markdown
```
{markdown}                                    # the content renders, then reads as Markdown
  ## Notes for {$version}
  - **Faster** first render
{/markdown}
{$post.body | markdown}                       # a value - raw HTML escaped, no sanitize on top
{markdown html}...{/markdown}                 # the author's own raw HTML let through
{markdown ignore}...{/markdown}               # braces in code samples
```
A built-in CommonMark subset (lib/markdown.php): headings, emphasis, code, lists, links,
images, quotes, rules. `javascript:` links lose their URL; the HTML of a value stays a value.

### Markdown collections
```
{collection 'blog', sort='date DESC', first=10}   # _content/blog/*.md, one row per file
  <h2><a href="?blog/post&slug={$slug}">{$title}</a></h2>
{/collection}
{collection 'blog', slug=$slug}<h1>{$title}</h1>{!body}{/collection}   # one post
```
A row is the file's front matter (YAML between `---` lines) plus `slug` (file name), `body`
(the Markdown as HTML - print it raw with `{!body}`) and `source`. `html` lets raw HTML
through; `padCollection('blog')` gives the rows to PHP.

### Charts
```
{chart 'bar', data='sales', label='month', value='amount'}   # inline SVG, no JavaScript
{chart 'line', data='visits', title='Visits this week'}      # data: store, page array, _data file
{chart 'bar', data='sales', value='online, shop', stacked}   # several series; 'hbar' lies down
{chart 'pie', data='budget'}                                 # also donut
{chart 'scatter', data='cars', x='weight', y='mpg', color='origin', trend}   # bubble: size=
{chart 'heatmap', data='visits', x='hour', y='day', value='count'}
{chart 'sankey', data='energy', source='from', target='to', value='amount'}
{chart 'network', data='friends', source='a', target='b', layout='radial', arrows}
{chart 'treemap', data='spend', levels='dept', label='post', value='amount'}  # also sunburst
{sparkline sequence='fibonacci', rows=12}                    # word-sized; sequence terms as data
{chart 'gauge', value=72, unit='%', bands='60, 85', target=80}   # one number - no data needed
{chart 'radar', data='phones', label='model', value='battery, camera, screen'}
{chart 'waterfall', data='result', label='step', value='amount', total='Profit'}
{chart 'histogram', data='people', value='height', bins=12}  # also boxplot (label= groups)
{chart 'calendar', data='commits', date='day', value='count', year=2026}
{chart 'gantt', data='plan', label='task', from='begin', to='until', progress='done', mark='2026-10-07'}
{chart 'area', data='energy', label='year', value='coal, gas, wind', stacked}   # percent; also stream, multiples (by=)
{chart 'candlestick', data='share', label='day', volume='volume'}   # open/high/low/close; dual: value= bars, line= right axis
{chart 'funnel', data='shop', label='step', value='visitors'}       # pyramid: one field a triangle, two back to back
{chart 'waffle', data='energy', cells=100}                          # also marimekko, parliament, venn (sets=), rose, radialbar
{chart 'bullet', data='kpis', value='actual', target='goal', bands='50, 75'}   # also rings, proportional
{chart 'violin', data='scores', label='class', value='score'}       # also density, parallel (value= the axes), spiral (period=)
{chart 'chord', data='migration', source='from', target='to', value='people'}  # also arc
{chart 'orgchart', data='staff', id='id', parent='boss', label='name', sub='role'}  # pack: the treemap's options
{chart 'bar', label='month', value='amount'}                 # a pair: the content is the data -
  month,amount                                               #   JSON, YAML, XML or CSV, told
  Jan,12400                                                  #   apart on sight (type= names it)
{/chart}
```
`role="img"` with `<title>`/`<desc>`; colours are `--pad-chart-*` custom properties on
`.pad-chart`, following the page's `color-scheme`. A field option left out takes the first
field that fits; the chart's options keep clear of the handling options (`row`, `group`).
Code: `pad/lib/chart.php` (bar, line, sparkline) and `pad/lib/chart/` (the other kinds).

### Month calendar
```
{calendar data='agenda', title='what'}         # this month (or ?month=2026-11), events, links ‹ ›
{calendar '2026-10', data='agenda'}            # as a pair: weeks as rows, days inside, events per day
  <tr>{days}<td class="{$class}">{$day}{events}<b>{$what}</b>{/events}</td>{/days}</tr>
{/calendar}
```

### Syntax highlighting
```
{highlight 'php', lines, mark='3'}<?php echo $x; ?>{/highlight}   # pad php html css js json yaml sql bash
{highlight file='_data/products.json'}                           # a file of the app, language from its extension
{$query | highlight('sql')}                                       # a value - escaped, so no sanitize on top
```

### QR codes and barcodes
```
{qr 'https://example.com/order/42'}            # inline SVG, encoded on the server (lib/qr.php)
{qr $url, size=200, level='H', title='Scan'}   # level L, M (default), Q, H; color=, background=
{barcode '871234567890'}                       # EAN-13 (check digit added); type= ean8, upca, code128
{barcode $parcel, type='code128', height=70}   # scale= module width, plain = no digits under the bars
```

### Showcase tags
```
{avatar 'Herbert Jebbink', size=48, shape='square'}   # initials in a circle/square, colour from a hash of the name
{identicon $email, size=64}                   # a symmetric 5x5 pattern from a sha256 hash, own colour
{placeholder '600x300', text='Hero image'}    # grey SVG box with crossing lines; ratio='16:9', fluid
{progress 72, max=100, label='Upload'}       # styled <progress> with label and %; steps for dots, color=, size=
{rating 4.5, max=5}                           # stars as SVG, half stars clipped, aria '4.5 out of 5'; icon='heart'
{icon 'arrow-right', size=20, label='Next'}   # built-in line icon in currentColor; decorative unless label=
{gravatar $email, size=64, default='identicon'} # gravatar.com img (sha256, lazy, no referrer); fallback='avatar' stays local
{chess $fen, flip, highlight='e2,e4', arrow='e2-e4'}   # a FEN as an SVG board, pieces described for a screen reader
{sudoku '53..7....6..195...', solve}             # 81 cells as a table, givens bold; solve fills the solution in
{crossword}ECHO: Writes a value{/crossword}       # WORD: clue lines laid out, numbered SVG grid + Across/Down clues; solution
{diff $old, $new}                              # word diff with <del>/<ins>, escaped; lines, side, from=, to=
{excerpt $body, words=24, highlight=$q}         # whole words around the first match, <mark>ed, escaped; html
{lorem paragraphs=3, seed=7}                    # placeholder text, classic start, reproducible; words=, sentences=, varied
{tabs active='Specs'}{tab 'Overview'}...{/tab}{tab 'Specs'}...{/tab}{/tabs}   # CSS-only tabs: radios, arrow keys
{accordion single, open=1}{tab 'Question'}Answer{/tab}...{/accordion}   # <details> items, one open
{modal 'terms', title='Terms', button='Read the terms'}...{/modal}   # <dialog>, works with and without JS
{carousel label='Photos'}{tab 'Caption'}<img ...>{/tab}...{/carousel}   # scroll-snap slides, link arrows and dots
{copy 'npm install pad', label='Copy'}             # the text in <pre>, a copy-to-clipboard button
{poll 'lang', options='PHP, Go', title='Favourite?'}   # one vote per session, results as bars; DATA/poll/
{img 'photos/harbour.jpg', width=400, height=300, alt='The harbour'}   # GD thumbnail in www/<app>/_thumbs/, 2x srcset, lazy
{video 'https://youtu.be/ID', title='Launch'}  # poster + play button, nocookie iframe only after a click
{pdf file='invoice', download}...{/pdf}   # the content as a PDF (plain PHP writer) instead of the page; preview: a sheet + link
{diagram direction='LR'}A[Order] --> B{Paid?}{/diagram}   # flowchart (or type='sequence') laid out as SVG on the server
{map 'europe', data='sales', key='country', value='amount'}   # choropleth: ISO alpha-2/3 or name, world or a region
```
Every one is rendered on the server; the `showcase` application has examples of each.

### Variable Assignment
```
{set $name = 'Alice'}              # Assign string
{set $count = 0}                   # Assign number
{set $total = $price * $qty}       # Assign expression
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
{echo 50 | @ * 4}                # @ represents the current value
{echo $text | '"' . @ . '"'}     # Wrap value in quotes
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
<div data-json="{json 'products' | ignore}"></div>
<div data-users="{echo $usersJson | html | ignore}"></div>
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
`$v = '{php:getcwd}'`, both `{$v}` and `{echo $v}` print the text `{php:getcwd}`. The
syntax characters of a value travel as inert stand-ins and are restored only when the page
is written out, so a value inside another tag's quoted parameter - `{echo '{$v}'}` - stays
text too. To run a value as PAD, say so where it is used - a snippet kept in a database:

```
{echo $snippet | code}       # runs the value as PAD
{echo $snippet | sandbox}    # the same, in an isolated pass
```

A field written bare into a tag's parameters - `{echo {$v}}` - is spliced as a quoted
string, unless it is a plain number, so a value of `php:getcwd` stays text; `{echo $v}`
says the same more simply. A tag's rendered output written there is template text, by design.

`$padProtectValues = FALSE` brings back the old behaviour: every value re-read as template
source.

---

## Critical Syntax Rules (Common Mistakes)

1. **Pipe a value through `{echo}` or a field tag** - `{$var | upper}` and `{echo $var | upper}` both work, but only the field tag runs the sanitize chain - `{echo}` prints the value raw; a literal needs the `{echo}`
2. **Arithmetic needs space** - `{echo $x | + 1}` not `| +1`
3. **Quote literal strings** - `{count 'items'}` not `{count items}`
4. **No inline CSS/JS** - PAD parses `{ }` as tags; use external files or `{ignore}` wrapper
5. **Use `padRedirect()`** - Don't use `exit` or `die` in PAD apps
6. **A bare condition tests truth** - `{if $flag}` fails for `''`, `'0'`, `0` and FALSE; compare when a value is meant: `{if $count eq 0}`
7. **`$` vs `%` variables** - `$var` is level (constant), `%var` is occurrence (per-iteration)
8. **CHECK syntax** - Use `db("CHECK table WHERE...")` NOT `db("CHECK * FROM table...")`
9. **Boolean options** - Use `{tag option}` NOT `{tag option="true"}` - just the option name is enough

---

## Type Prefixes

Resolve naming conflicts with explicit prefixes:

| Prefix | Purpose | Example |
|--------|---------|---------|
| `app:` | App tag from `_tags/` | `{app:mytag}` |
| `common:` | Tag from the `_common` app | `{common:menu}` |
| `pad:` | Built-in PAD tag | `{pad:if}` |
| `php:` | Call PHP function | `{php:strlen 'abc'}` as a tag, `{echo $x \| php:strlen(@)}` in an expression |
| `function:` | Custom PAD function | `{$x \| function:myfunc}` |
| `data:` | Defined data block | `{data:items}` |
| `content:` | Content block | `{content:header}` |
| `include:` | Snippet from `_include/` | `{include:header}` |
| `pull:` | Stored sequence | `{pull:mySeq}` |
| `field:` | Field (variable) value | `{field:name}` - the database single value is the `{field "col from table..."}` tag |
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
| `action:` | Sequence action | `{action:reverse}` |
| `flag:` / `make:` / `keep:` / `remove:` | Sequence operations | `{make:fibonacci}` |

---

## Tags, Functions, Options & Properties Reference

See the following files for complete reference documentation:

- **[TAGS.md](docs/reference/TAGS.md)** - All built-in tags (control flow, data, database, file, output, navigation, execution, debug), type prefixes, options, and properties
- **[FUNCTIONS.md](docs/reference/FUNCTIONS.md)** - All pipe functions (string, case, HTML, date, arithmetic, printf format)
- **[sequences/](docs/sequences/)** - Sequence subsystem (80 sequence types, actions, transformations)

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
| `in ('a', 'b')` | Value in a list |
| `like 'A%'` | SQL LIKE match |
| `matches '/re/'` | Regular expression match (a backslash written `\\`) |
| `a ?? b` | `a`, or `b` when `a` is empty or a missing field |
| `c ? a : b` | Inline ternary - `{echo $n gt 1 ? 'items' : 'item'}` |

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
# database tags, named queries and Select work alike on both drivers.
# padSqlDriver=sqlite
# padSqlDatabase=myapp/myapp.sqlite
# padSqlSetup=_install/schema.sql
```

In `_config/config.php`:
```php
// Database connection - the values from the .env files, as pad/config/config.php reads them
$padSqlHost     = padEnv ( 'padSqlHost' );
$padSqlDatabase = padEnv ( 'padSqlDatabase' );
$padSqlUser     = padEnv ( 'padSqlUser' );
$padSqlPassword = padEnv ( 'padSqlPassword' );

// Error handling: pad, boot, php, stop, exit, ignore, log, dump
$padErrorAction = 'pad';

// Debug mode: trace, stats, track, xml, xref
// $padInfo = 'trace';

// Output type: web, file, download, console, json, csv (json and csv answer $padExpose)
$padOutputType = 'web';

// The page variables a page answers as JSON or CSV - set in the page's .php
$padExpose = [];

// Links in the clean form - /myapp/products/42 - for a server that routes paths
$padCleanUrls = false;

// ?sitemap.xml from the file tree, and ?robots.txt pointing to it; the names left out
$padSitemap     = false;
$padSitemapSkip = [];        // e.g. [ 'admin', 'login' ] - a page, or a directory and below

// Template emails: 'file' (DATA/mail/<app>/*.eml), 'mail' (PHP mail()) or a function name
$padMailTransport = 'file';
$padMailFrom      = '';      // empty: noreply@ the host
$padMailKeep      = 100;     // messages the file transport keeps

// Cache enabled
$padCache = false;

// {assert} conditions are checked - and a false one is an error - only when this is on;
// pad test turns it on for its run. Off, every {assert} is silent.
$padAssert = false;

// The debug toolbar: a collapsible bar at the foot of a local web page - time, memory,
// the tag tree, the SQL, page and fragment cache, the template files, the variables and
// the session. 'local' (or TRUE) shows it to this machine's own requests only, never to
// a visitor; not on a &padInclude fragment, and never in the page cache.
$padToolbar = false;

// Live reload: a local page polls for the newest file time behind it and reloads when a
// file changes - 'local' (or TRUE) watches the app, its www/ dir and _common, 'engine' also
// pad/. Local requests only; never in a fragment, the page cache or a pad export.
$padReload = false;

// The strict syntax check, on by default: orphan braces and tags, pairs that never
// close, options nothing reads, misses behind a type prefix - and an undefined $field,
// in an expression and in the {$x} tag form alike. Off, the lenient walk keeps what
// nothing claims as literal text and resolves a missing field to empty.
$padCheckSyntax = true;

// Values are text, never template code (see Values Are Text). Off, every value is
// re-read as template source.
$padProtectValues = true;

// The locale and timezone - {trans}, currency, localDate. With $padLocales listing several,
// ?lang=, the padLang cookie or Accept-Language picks among them.
$padLocale   = 'en';
$padLocales  = [];          // e.g. [ 'en', 'nl', 'de' ]
$padTimezone = '';          // e.g. 'Europe/Amsterdam'

// The PHP functions a template may call - php:, and a bare name like {$x | ucfirst}.
// TRUE allows all; a list allows those names only; [] none.
$padPhpFunctions = true;

// Which request values become variables ({$name} for a form field). TRUE every POST
// and GET value; a list those names only (a cookie only when listed); [] none. A
// $padSessionVars name, and an engine name (pad*, pq*, _*), is never filled from it.
$padRequestVars = true;

// CSRF protection, off by default. On: every <form method="post"> posting back to the
// site gets a hidden padCsrfToken field, and a POST (PUT, PATCH, DELETE) without the
// session's token - field or X-CSRF-Token header - is answered 403 before the app runs.
// {csrf} writes the field by hand, {csrf token} the bare token, padCsrfValid() checks.
$padCsrf = false;

// The output check in development: every local HTML response is read back and duplicate
// ids, images without alt, unlabelled form fields and broken ?page links are named in a
// panel in the page plus a PAD-Output-Check header. ?page&padCheckOutput asks for one
// request; develop/?links checks the literal links of every application's templates.
$padCheckOutput = false;

// Template coverage of local requests: which templates were read, which tags ran, which
// {if}/{case} branch was taken - one JSON line per request in DATA/coverage/<run>.jsonl.
// TRUE is the run 'default', a string names the run; ?page&padCoverage=name for one
// request; develop/?coverage starts/stops recording every app and shows the report.
$padCoverage = false;

// Replay real traffic as tests: TRUE (or a store name) records every GET answered 200 -
// one with no cookie but PAD's ids - with its answer in DATA/replay/<store>/; ?page&padRecord=name
// for one local request. develop/?replay re-asks every recorded page and names what changed.
// A replay never writes: db() refuses writing statements, padFilePut the app's own writes.
$padRecord = false;

// Security headers on every web response; '' drops one, [] sends none, a header the page
// sent itself stands. $padCsp is the Content-Security-Policy ('' none); 'nonce' in it is
// this request's nonce, which {nonce} writes: <script nonce="{nonce}">.
$padSecurityHeaders = ['X-Content-Type-Options' => 'nosniff',
                       'Referrer-Policy' => 'strict-origin-when-cross-origin'];
$padCsp = "frame-ancestors 'self'";

// Designer preview: ?page&padSample renders the template with _samples/page.json instead
// of running the PHP. 'local' for a local request only, TRUE everyone (a design server
// without real data - the preview skips the PHP's login checks), FALSE never.
$padSample = 'local';

// Custom error pages from _errors/ (404.pad, 5xx.pad ...); FALSE keeps the plain lines.
$padErrorPages = true;

// ?up answers the health check as JSON - 200 when every check passes, 503 when one fails.
$padHealth = false;

// CORS: [ 'origins' => [...], 'methods' => [...], 'headers' => [...], 'credentials' => false,
// 'maxAge' => 600 ]; [] sends no CORS header.
$padCors = [];

// Feature flags: name => TRUE, FALSE, a share 0..1 of the visitors, or a function's name.
$padFeatures = [];

// Remember-me: the name of the application function that finds a user's row by its id.
$padAuthRemember = '';
```

### Designer preview with sample data
```
?orders&padSample             # orders.pad with _samples/orders.json - no _inits.php, no orders.php
?orders&padSample=capture     # runs the page for real, writes DATA/samples/<app>/orders.json
apps/cli/pad sample shop orders   # the same capture, into apps/shop/_samples/orders.json
{array "* from orders", name='orders'}   # a named database tag answers from the sample too
```

### Expression errors

Under `$padCheckSyntax` (the default), the expression evaluator reports a malformed
expression in the source's own terms, naming the exact fault and its position:
- unbalanced quotes, `( )` or `[ ]` — `{echo (1 + 2}` → *the ( opened at position 1 is never closed*
- a misspelled pipe function — `{echo $x | uppr}` → *there is no pipe function named 'uppr'*
- a comparison operator missing an operand where no pipe value can stand in for it —
  `{if $x eq}` → *the operator 'eq' has nothing on its right*
- an undefined field — `{if $typo eq 1}` → *there is no field named '$typo'*

With the check off nothing is reported: a value that is no number counts as 0 (`'a' + 1` is 1),
a missing field is empty, and what cannot be reduced at all - an unclosed bracket - is empty.

### Errors point into the template

Every error report - the error page, the JSON body a local tool gets, the console, the log
line - names where in the template it stands, next to the engine's own PHP file and line:

```
apps/shop/orders/list.pad  line 14, column 11

  13 │   <tr>
  14 │     <td>{$totl | money}</td>
     │          ^^^^^ did you mean $total?
  15 │   </tr>

wrapped by apps/shop/_inits.pad
```

The JSON body carries it as `template`: `file`, `line`, `column`, `tag`, `excerpt`, `suggest`,
`included` (the snippet's or `{page}`'s includer), `wrapped` and a `vscode://file/...` `link`.
The build keeps a source map of the joined `_lib`/`_inits`/page/`_exits` text
(`pad/lib/source.php`); a position it cannot place surely is left out rather than guessed.

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
pad migrate shop [--status]   # the pending migrations of _migrations/; --rollback, --fresh --force, --seed
pad seed shop [name]          # the seeders of _seeds/
pad work shop [--once]        # the queue worker; pad queue shop [--failed] for the queues
pad schedule --all            # what every _schedule.php has due - one cron line, every minute
pad down shop --secret=word   # maintenance until pad up shop
pad help
```

The repository is the one the script stands in unless `PAD_HOME` says otherwise. A failed
render answers the JSON error report and exit status 1; `pad lint` exits 1 when any page failed.

### Application tests

An application keeps its own tests in `_tests/`: `cart.pad` (with `cart.php` when it needs
data) is the test page, `cart.txt` its answer - an exact body, a `/regex/`, or `HTTP 500`
with an optional `/regex/` on the next line, as in the framework's suites. A test page renders
bare, no URL reaches it, and it can bring in a real page with `{page 'cart'}`. `{assert
$total eq 42, 'the total'}` fails a test run when false and is silent in production
(`$padAssert`, off by default, on under `pad test`). `pad test shop --record` writes the
missing answers; `./ci.sh` runs `pad test --all` and fails on a failing test.

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

Cross-app URLs (menu links, `padRedirect()`, the regression harness) are built from `$padHost`, which includes the mount prefix (`scheme://host` . `$padRoot`), so they work under any mount prefix. The host is the request's own Host header, but the engine's fetches of its own pages (`{get}`, `{page app=}`, `SELF://`, `padPrefetch`) always connect to the server's own address (`SERVER_ADDR`:`SERVER_PORT`) - a forged Host cannot make the server fetch from another machine; with `$padHostBase` set (behind a proxy) they go where it says. Off `php -S` a port in the Host header counts only when it is 80/443 or `$padHosts` lists it with the name (`'localhost:8080'`) - Apache fills `SERVER_PORT` from the Host - else they connect to the scheme's port. Page-internal links use `$padGo`/`?page` (from `SCRIPT_NAME`, with `$padGoExt` as the absolute form) and are prefix-safe automatically.

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
{items}{notFirst@items}, {/notFirst@items}{$name}{/items}
```
Output: `Alice, Bob, Charlie`

### First/Last Wrapper
```
{items}
  {first@items}<ul>{/first@items}
  <li>{$name}</li>
  {last@items}</ul>{/last@items}
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
{record "* from users where id=5"}
  {fields@record}
    {$name}: {$value}
  {/fields@record}
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
| `padRedirect($page, $vars, $app)` | Redirect to a page of this application (or of `$app`) with optional variables - no page: back to the one asked for; never off the site, `{redirect}` also takes an absolute URL |
| `padRestart($page)` | Restart processing with new page |

### Forms and Security

| Function | Description |
|----------|-------------|
| `padSessionStart()` | Start the PHP session on demand, strict mode and safe cookie flags |
| `padCsrfToken()` | The session's CSRF token (`{csrf}` writes it as a hidden field) |
| `padCsrfValid()` | Whether this request brought the session's token back |
| `padPosted($form)` | Whether this request posted (the `{form}` of that name) - and kept the `rules=` of its template; without a name, on a page whose template has rules, only a post of one of its forms |
| `padFormFailed($form)` | Whether the form came back and broke the `rules=` of its template |
| `padUpload($field, types: [...], max: '2M')` | Store an uploaded file safely: real type (finfo), size limit, random name in `DATA/uploads/` - the record, NULL (none sent) or FALSE (refused) |
| `padUploadError($field)` | Why `padUpload` refused the field's file (also shown by `{input type='file'}`) |
| `padNonce()` | This request's CSP nonce (`{nonce}`), named by `'nonce'` in `$padCsp` |
| `padFlash($message, $type)` | A message for the next request - `padFlash('Saved.'); padRedirect('list');` then `{flash}<p class="{$type}">{$message}</p>{/flash}` |
| `padValidate($rules, $data, $messages)` | `['email' => 'required\|email']` - one message per failing field, shown by `{input}` |

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
| `padPrefetch($sources, $ttl)` | Fetch remote sources in parallel, each kept as named data (`{rates}`) |
| `padCurlCached($input, $ttl)` | `padCurl()` with a ttl cache and the last good copy on a failure |

### File Operations

| Function | Description |
|----------|-------------|
| `padFileGet($file, $default)` | Read file contents - a relative path is under `DATA/` |
| `padFilePut($file, $data, $append)` | Write file contents - under `DATA/` only, a relative path there too; never a name the web server runs (`.php`, `.phtml` ...) or reads as configuration (`.htaccess`, `.user.ini`) |
| `padFileCheck($file)` | Validate file path |

### Mail

| Function | Description |
|----------|-------------|
| `padMail($to, $template, $subject, $vars, $options)` | Render `_mail/<template>` and send it - `$options`: `from`, `cc`, `bcc`, `replyTo`, `html`, `text` |

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
| `padValidVar($name)` | Validate variable name |

### Utilities

| Function | Description |
|----------|-------------|
| `padRandomString($len)` | Generate random string |
| `padExplode($str, $delim, $limit)` | Smart explode with trimming |
| `padBetween($str, $open, $close, &$before, &$between, &$after)` | Split at the first `$open`...`$close` - TRUE when both are there, the three parts by reference |
| `padMakeSafe($input, $len)` | Sanitize input |

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
| Cache and rate limits | `lib/remember.php` | `padCacheGet`, `padCachePut`, `padCacheHas`, `padCacheForget`, `padCacheFlush`, `padRemember` (files under `DATA/cache/app/<app>/`), `padRateLimit`, `padRateLimitRemaining`, `padRateLimitAvailableIn`, `padRateLimitClear` |
| Dates and logging | `lib/date.php`, `lib/log.php` | `padNow`, `padToday`, `padNowFreeze`, `padDateParse`, `padAgo` (pipe `ago`), `padLog` (PSR-3 levels, `DATA/logs/<app>/<date>.log`) |
| Hashing and encryption | `lib/crypt.php` | `padHash`, `padHashCheck`, `padHashNeedsRehash`, `padEncrypt`, `padDecrypt` (sodium, key `$padAppKey` or `DATA/keys/<app>.key`), `padSignedUrl`, `padSignatureValid` |

A wrong argument (an unknown operator, a negative length) is reported with `padError` naming
the function; the function then answers an empty value of its kind.

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
│                         RESPONSE                                 │
└─────────────────────────────────────────────────────────────────┘
```

### Architecture Notes

- **Procedural Design**: 140+ functions, minimal OOP
- **Global State**: Uses `$GLOBALS` and `global` declarations indexed by level
- **File-Based Dispatch**: `include` statements as control flow
- **Array-Indexed Levels**: Nesting tracked via `$padVariable[$pad]` pattern
- **15+ Years Production**: Stable, battle-tested codebase
- **Request-level memos**: which global names are the engine's (`padEngineNames`,
  `padValidStore`, `padStrPad`) and the tokens of each clean expression (`padEvalParsed`) are
  kept for the request; the @else@ pair scan runs only when an `@else@` is there. Nothing is
  kept between requests - the template is still rescanned each pass (no AST)

---

## Debugging

- Set `$padInfo = 'trace'` for execution tracing
- Set `$padToolbar = 'local'` for the debug toolbar at the foot of every local page
- Set `$padReload = 'local'` in `_config/config.php` and a local page reloads itself when a file behind it is saved
- Use `{debug $order}` for a collapsible view of a value inside the page (local requests only; `{debug}` alone shows every field visible there)
- Use `{dump}` tag for variable inspection
- Use `{trace}` tag for execution trace
- Set `$padCheckOutput = true` (or add `&padCheckOutput` to a local request) to have the finished HTML checked for duplicate ids, images without alt, unlabelled fields and broken `?page` links; `develop/?links` checks every application's templates at once
- Template coverage: `develop/?coverage&start=suites`, run `./ci.sh`, `develop/?coverage&stop`, then `develop/?coverage&run=suites` shows every template with the tags and `{if}`/`{case}` branches that never ran marked, and the files no request read (`$padCoverage` records one app's local requests)
- Replay real traffic: `$padRecord = true` (or `?page&padRecord=name`) keeps each cookie-less GET with its answer; `develop/?replay&store=default` re-asks every recorded page against the current code and lists the pages whose answer changed, to accept or delete
- Check `DATA/` directory for error dumps and logs

### Testing PAD Pages from Command Line

You can fetch and analyze PAD pages directly from a running server using `curl`. On this machine `www/` is mounted at `http://localhost/pad/`, so every app URL carries that prefix:

```bash
# Fetch a PAD page
curl "http://localhost/pad/demo/?clock"

# Fetch with headers
curl -i "http://localhost/pad/manual/"

# Fetch and save output
curl -o output.html "http://localhost/pad/myapp/?page/subpage"

# Fetch with query parameters
curl "http://localhost/pad/app/?page&param=value"
```

**Examples:**
```bash
# Demo application pages
curl "http://localhost/pad/demo/?index"        # Home page
curl "http://localhost/pad/demo/?guestbook"    # Guestbook
curl "http://localhost/pad/demo/?clock"        # Clock with date/time

# Documentation and reference apps (each is its own app)
curl "http://localhost/pad/manual/"            # Framework manual
curl "http://localhost/pad/reference/"         # Cross-reference
curl "http://localhost/pad/pad/?hello"         # Hello World test

# Debugging output - a request cannot switch the trace on ($padInfo is never filled from
# the query): set $padInfo = 'trace' in _config/config.php, or render with --trace (below)
curl "http://localhost/pad/app/?page&padCheckOutput"   # With the output check

# Run all eight suites (results in DATA/suites/; DATA/reference and DATA/examples are
# harvested by the develop app and stand between builds)
curl -L "http://localhost/pad/regression/main/?index&test"

# Or as a CI gate from the repo root - one line per suite, nonzero exit on any failure
./ci.sh

# The same, also failing when the pages got more than 25% slower than their median over
# the last kept benchmark runs (develop/?benchmark/history charts them)
CI_BENCH=25 ./ci.sh
```

This is particularly useful for:
- **Automated testing** - Verify page output in scripts
- **Debugging** - Inspect generated HTML without a browser
- **Performance testing** - Measure response times
- **Regression testing** - Compare output against expected results
- **CI/CD integration** - Test PAD applications in pipelines

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
strict syntax check - `pad_check` with only an app checks every page), `pad_trace`,
`pad_apps`, `pad_pages`, `pad_builtins`, `pad_reference` (a tag or function in
`docs/reference`) and `pad_test` (`./ci.sh` with the failing tests). Tested by
`node editors/mcp/test.js`, the `mcp` line of `./ci.sh`.

---

## Applications Overview

| App | Type | Description |
|-----|------|-------------|
| `_common` | Shared | Shared resources and utilities for all applications |
| `apps` | Standard | Lists all PAD applications with descriptions from README files |
| `charts` | Standard | A showcase of the `{chart}` tag - a pulldown per chart family (46 kinds), every example a page of three cells: the data, the tag, the chart |
| `showcase` | Standard | A showcase of the tags that draw, format and interact - a pulldown per group, a section per tag, every example its template beside the result |
| `classicModels` | Standard | PAD Select over the Classic Models sample database |
| `cli` | CLI | The `pad` command (`apps/cli/pad`): new, serve, render, lint, export, test - and the cli application |
| `demo` | Standard | Interactive demo with guestbook, todo, contact, counter, clock |
| `develop` | Standard | Development tools for PAD - the source trimmer, the harvest of the reference and the examples, the error listing |
| `edit` | Standard | A browser editor for every application, behind a login, this machine only by default - file tree with new, copy, rename, delete, upload and trash; Monaco with PAD colouring, completion, hover, go-to-definition and PAD's own checks, PHP completion; preview, history, search, git, a terminal, an Xdebug step debugger; the framework's own pad/ in the tree too |
| `examples` | Standard | Search the harvested examples of DATA/examples and view one with its sources beside the rendered result |
| `hello` | Minimal | Hello World example demonstrating page pairing |
| `manual` | Standard | Interactive documentation and examples |
| `nono` | Plain PHP | PHP application without PAD templating |
| `pad` | Standard | PAD framework introduction and reference |
| `playground` | Standard | Type a template and its JSON data and see the result - local requests only, no PHP functions, shareable through the URL hash |
| `react` | Standard | PAD + React integration examples |
| `reference` | Standard | Cross-reference and directory utilities |
| `regression/main` | Standard | Automated regression testing for PAD - the runner for the eight suites and the fresh build |
| `regression/pages` | Test | The pages suite: every test is a real page, fetched over HTTP and compared with the answer beside it |
| `regression/framework` | Test | The Framework suite: the engine cases as pages, one fetched per case |
| `regression/regression` | Test | The Regression suite's prediction store - one answer per page of the self-testing applications and the runner |
| `regression/sequence` | Test | The Sequence suite's prediction store - one answer per page of the sequence application |
| `regression/manual` | Test | The Manual suite's prediction store - one answer per page of the manual application |
| `regression/other` | Test | The Other suite's prediction store - one answer per page of every application without a suite of its own |
| `regression/cache_apcu` | Test | Regression test for the 'apcu' page cache |
| `regression/cache_db` | Test | Regression test for the 'db' page cache |
| `regression/cache_file` | Test | Regression test for the 'file' page cache |
| `regression/cache_memcached` | Test | Regression test for the 'memcached' page cache |
| `regression/cache_redis` | Test | Regression test for the 'redis' page cache |
| `regression/config_typo` | Test | Regression test for the configuration word check |
| `regression/error_boot` | Test | Regression test for the 'boot' error action |
| `regression/error_dump` | Test | Regression test for the 'dump' error action |
| `regression/error_exit` | Test | Regression test for the 'exit' error action |
| `regression/error_ignore` | Test | Regression test for the 'ignore' error action |
| `regression/error_log` | Test | Regression test for the 'log' error action |
| `regression/events` | Test | Regression test for the `_events/` hooks - error, sql, curl, output |
| `regression/remote` | Test | Regression test for remote data with a ttl cache and parallel fetching - pages that fetch their own |
| `regression/sqlite` | Test | Regression test for SQLite as the application database - built from a .sql file, no server |
| `regression/env` | Test | Regression test for `padEnv` in a configuration file, the application cache's flush and parallel rate-limit hits |
| `regression/error_pad` | Test | Regression test for the 'pad' error action |
| `regression/error_php` | Test | Regression test for the 'php' error action |
| `regression/error_stop` | Test | Regression test for the 'stop' error action |
| `regression/info` | Test | Regression test for the five info modes, every option on |
| `regression/output_console` | Test | Regression test for the 'console' output type |
| `regression/output_download` | Test | Regression test for the 'download' output type |
| `regression/output_file` | Test | Regression test for the 'file' output type |
| `regression/output_web` | Test | Regression test for the 'web' output type |
| `regression/sitemap` | Test | Regression test for `$padSitemap` - sitemap.xml and robots.txt from the file tree |
| `regression/clean_urls` | Test | Regression test for `$padCleanUrls` and the bracketed routes |
| `regression/output_json` | Test | Regression test for the 'json' output type - every page answers what it exposes |
| `regression/try_log` | Test | Regression test for the try guards under the 'log' action |
| `regression/try_pad` | Test | Regression test for the try guards under the 'pad' action |
| `regression/toolbar` | Test | Regression test for the debug toolbar - local page yes, fragment and forwarded request no |
| `regression/cli` | Test | Regression test for the pad command - render, new, lint, serve and export |
| `regression/site` | Test | The fixture pad export is tested on - links in every form, a subdirectory, assets |
| `regression/reload` | Test | Regression test for live reload - script and stamp locally, none elsewhere or in an export |
| `regression/errors` | Test | The Errors suite: the tests that fail on purpose, answered lean under the boot action - no dumps |
| `regression/common` | Test | The pages of the suite that use `_common` - `{example}`, `{demo}`, `{table}` - fetched and compared the same way |
| `regression/errorpages` | Test | Regression test for `$padErrorPages` - every refusal answered with the application's `_errors/` page |
| `regression/maintenance` | Test | Regression test for maintenance mode - pad down/up, 503 with Retry-After, the secret's cookie |
| `regression/health` | Test | Regression test for `$padHealth` - `?up` as JSON, 503 when a check fails |
| `regression/cors` | Test | Regression test for `$padCors` - headers per origin, the preflight answered before CSRF |
| `regression/migrate` | Test | Regression test for migrations, seeders and fake data - an SQLite database built by `_migrations/`, filled by `_seeds/` |
| `regression/queue` | Test | Regression test for the job queue and the scheduler - jobs queued and worked in one request, the schedule at fixed moments |
| `sequence` | Standard | Mathematical sequence subsystem demos - with a gallery of every type beside its OEIS entry, a sequence played as notes, and a guess-the-next-term game |
| `structure` | Example | Demonstrates nested `_xxx` directories and inheritance |
| `test` | Minimal | A scratch application for trying things out, `_common` switched off |

See [apps/README.md](apps/README.md) for the same list with links.

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

### Live Regions (interactivity without app JavaScript)
```
{live 'cart'}
  {cart}<li>{$item}</li>{/cart}
  <button pad-click="add" pad-value="42">Add</button>
{/live}
```
`pad-click` / `pad-submit` (form) / `pad-change` (field) inside a `{live}` region post the event
to the page's own URL; the page's PHP reads `padLiveEvent()` / `padLiveValue()` and keeps state
in the session (`$padSessionVars`) or the event's value; only that region's content comes back
and is swapped in. The first region brings a small inline script.

### PAD + React Integration

See [REACT.md](docs/REACT.md) for complete React integration documentation including:
- Pattern 1: Static data with {json} tag
- Pattern 2: Dynamic data with {reactData} tag and providers
- Critical: Using `getAttribute('data')` NOT `dataset.data`
- File organization and common patterns

### Manual Pages and Fragment Files

When creating documentation with examples:

**Manual page** (`manual/topic.pad`) - Contains all explanatory text:
- The title as `{meta title='...'}` on the first line - the `_common` wrapper prints it as
  the page's one `<h1>` (from the file name when there is no meta); never an `<h1>` of its own
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
{meta title='Pipes - Transform Output'}
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
