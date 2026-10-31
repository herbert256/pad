# PAD Features

The features PAD has beyond its core template language - the language itself, the application
structure and the engine are in [CLAUDE.md](../CLAUDE.md), every built-in tag in
[reference/TAGS.md](reference/TAGS.md), every function a page's PHP calls in
[reference/HELPERS.md](reference/HELPERS.md). The `showcase`, `charts`, `tags` and `manual`
applications show them working.

## Contents

- [Requests, responses and operations](#requests-responses-and-operations)
  - [Template emails](#template-emails)
  - [Early flush](#early-flush)
  - [Server-sent events](#server-sent-events)
  - [Vite builds](#vite-builds)
  - [Sitemap from the file tree](#sitemap-from-the-file-tree)
  - [JSON and CSV from the same page](#json-and-csv-from-the-same-page)
  - [Maintenance mode](#maintenance-mode)
  - [Custom error pages](#custom-error-pages)
  - [Health check](#health-check)
  - [Cross-origin requests (CORS)](#cross-origin-requests-cors)
  - [Logging in, gates and feature flags](#logging-in-gates-and-feature-flags)
  - [Asset versioning](#asset-versioning)
  - [Migrations and seeders](#migrations-and-seeders)
  - [Queues and the scheduler](#queues-and-the-scheduler)
  - [Offline applications in the browser](#offline-applications-in-the-browser)
  - [HTTP fakes for tests](#http-fakes-for-tests)
- [Template features](#template-features)
  - [Lookups Between Data Sets](#lookups-between-data-sets)
  - [Grouping with subtotals (group option)](#grouping-with-subtotals-group-option)
  - [Page links (pager tag)](#page-links-pager-tag)
  - [Recursive Trees](#recursive-trees)
  - [Form rules on the fields and in the browser](#form-rules-on-the-fields-and-in-the-browser)
  - [Layouts (extends / block / parent)](#layouts-extends--block--parent)
  - [Components (slots / parms)](#components-slots--parms)
  - [Tags defined in the template (define / macro)](#tags-defined-in-the-template-define--macro)
  - [Web components (declarative shadow DOM)](#web-components-declarative-shadow-dom)
  - [Stacks (push / stack)](#stacks-push--stack)
  - [Page Metadata](#page-metadata)
  - [Response Fragments](#response-fragments)
  - [HTML attribute helpers](#html-attribute-helpers)
  - [Fragment Cache](#fragment-cache)
  - [Remote data with a cache](#remote-data-with-a-cache)
  - [Translations and locale formatting](#translations-and-locale-formatting)
  - [Markdown](#markdown)
  - [Markdown collections](#markdown-collections)
  - [Charts](#charts)
  - [Month calendar](#month-calendar)
  - [Syntax highlighting](#syntax-highlighting)
  - [QR codes and barcodes](#qr-codes-and-barcodes)
  - [Showcase tags](#showcase-tags)
  - [Live Regions (interactivity without app JavaScript)](#live-regions-interactivity-without-app-javascript)
- [Development](#development)
  - [Configuration settings](#configuration-settings)
  - [Designer preview with sample data](#designer-preview-with-sample-data)
  - [Expression errors](#expression-errors)
  - [Errors point into the template](#errors-point-into-the-template)
  - [Application tests](#application-tests)
  - [The frame of the PAD applications](#the-frame-of-the-pad-applications)
  - [Common Patterns](#common-patterns)

## Requests, responses and operations

### Template emails

`{mail to=$email, template='order', subject='Order confirmation'}` renders `_mail/order.pad`
(HTML) and `_mail/order.txt` (text - made from the HTML when absent), after `_mail/order.php`,
inside the `_inits.pad`/`_exits.pad` layout of that `_mail/`; as a pair without `template` the
content is the HTML part. `from`, `cc`, `bcc`, `replyTo` too. From PHP:
`padMail ( $to, 'order', $subject, [ 'order' => $order ] )`. `$padMailTransport`: `'file'`
(default, `.eml` under `DATA/mail/<app>/` - no mail server needed), `'mail'` (PHP `mail()`),
or the name of an app function that gets the message (SMTP through a library).

### Early flush

`{flush}` - after the wrapper's `</head>` - sends the page rendered so far at once, so the
browser loads the stylesheets while a slow part below renders. It switches off tidy, whole-body
gzip, the ETag/304, `Content-Length` and the page cache for the request; it stands at the top
level of the page or wrapper (strict mode says so elsewhere) and does nothing in a page
rendered by `{page}`, for a non-web output type, in an application with an `_events/output.php`
hook (which sees the page whole), or in a request answered with one fragment or live region.

### Server-sent events

A page whose PHP calls `padSse()` keeps the connection open and streams events - what
`EventSource`, htmx's sse extension or a component listen to; there is no template:

```php
padSse ( function ( $send, $lastId ) {               // the producer loops itself
  $send ( 'tick', [ 'n' => 1 ], 1 );                 // event, data (an array goes as JSON), id
}, [ 'retry' => 3000 ] );

padSse ( fn () => [ 'orders' => $count ], [ 'every' => 2, 'for' => 60, 'event' => 'stats' ] );
```

With `every` PAD calls the producer on that interval and sends what it answers (`NULL` sends
nothing, `FALSE` ends); `for` bounds the stream (60 s). The session is closed first - the
visitor's other requests do not wait - and nothing is cached, compressed or buffered.

### Vite builds

`{vite 'src/main.js'/}` links the scripts and stylesheets of a Vite build by the name of their
source: from the manifest of `www/<app>/build/.vite/manifest.json` (`$padViteBuild`) - the
entry's CSS, its chunks as `modulepreload`, its `<script type="module">` - or, while the dev
server runs (`$padViteDev`, or the address in `build/hot`), from the dev server with hot module
replacement; `react` adds the fast-refresh preamble. A component compiled this way is mounted on
the islands PAD writes as before - no Babel in the browser.

### Sitemap from the file tree

`{sitemap}...{/sitemap}` lists every page of the application - `{$page}`, `{$url}`,
`{$lastmod}` - walked from the files; `{sitemap 'docs'}` one directory. With
`$padSitemap = TRUE`, `?sitemap.xml` answers the sitemap XML and `?robots.txt` points to it.
Left out: `_` entries, directories with a `_guard.php`, bracketed routes, action pages (no
template, PHP that only redirects), `{meta sitemap=false/}` and `$padSitemapSkip` names.

### JSON and CSV from the same page

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
  application answer data; `{output 'json'/}` does it from a template.

### Maintenance mode

`pad down shop --secret=letmein --retry=120 --message='Back at ten'` takes an application
down: every web request answers 503 with a Retry-After - `_errors/503.pad` when it has one,
else the message as one line - before the page cache and before any of it runs, a page that
is not there too; `?letmein` gives that browser a cookie that lets it through for twelve
hours. `pad up shop` brings it back; `pad down` lists what is down. The record is
`DATA/maintenance/<app>.json` (the secret only as a hash); the command line is never held back.

### Custom error pages

With `$padErrorPages` (on by default) an application's `_errors/404.pad` - or `4xx.pad`,
`5xx.pad` for a whole class, `.html` too - answers instead of the plain line, the status
kept: a page not found, a guard's 403, a post without its CSRF token, `padAbort(429)`,
maintenance's 503, and under the 'pad' error action a visitor's 500 (this machine keeps the
report). `$status`, `$message`, `$page` (and `$id` on a 500); `_errors/_inits.pad` and
`_exits.pad` with `@page@` are its frame - not the application's `_inits`, `_lib` or guards,
which may be what failed. A page that fails itself answers the plain line - no loop; json
and csv output get the line as well.

### Health check

With `$padHealth = TRUE`, `?up` (when the application has no page `up`) answers
`{"status":"ok","checks":{"data":"ok","database":"ok"}}` - DATA/ writable, the database
answering `SELECT 1` when `$padSqlDatabase` is set, and what `_health.php` returns
(`[ 'queue' => TRUE | FALSE | 'why' ]`). One failing check: 503 and `"fail"`; a database
failure shows "fail" only, the reason goes to the log.

### Cross-origin requests (CORS)

`$padCors = [ 'origins' => [ 'https://app.example.com' ], 'methods' => [ 'GET', 'POST' ],
'headers' => [ 'Content-Type' ], 'credentials' => FALSE, 'maxAge' => 600, 'expose' => [] ]`:
a listed origin (or any, with `'*'`) gets Access-Control-Allow-Origin and the rest, with
`Vary: Origin`; a preflight OPTIONS is answered 204 at once, before the CSRF check and the
application. An origin not listed gets no CORS header. `[]`, the default, sends none.

### Logging in, gates and feature flags

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

### Asset versioning

`{asset 'charts.css'/}` / `padAsset ( 'charts.css' )` is `/<app>/charts.css?v=<hash of the
contents>` of `www/<app>/charts.css` - safe behind a far-future cache header; `{asset
'app.js', tag/}` writes `<script src defer>` (`<link rel="stylesheet">` for .css, module for
.mjs), with the nonce when `$padCsp` asks for one. A missing file is the plain address and a
strict error; a name never leaves `www/<app>/`.

### Migrations and seeders

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

### Queues and the scheduler

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

### Offline applications in the browser

`pad pack notes` packs an application - its pages, PHP, `www/` files - with the engine and PHP
compiled to WebAssembly into `DATA/wasm/notes/`: opened from the disk it runs in the tab (the
pages in a frame whose first script routes the page's `fetch`, XHR, `EventSource`, forms and
uploads to PHP there); served over https or on localhost a service worker runs PHP for every
window and the application installs and works offline. What it writes under `DATA/` - SQLite
databases, uploads, sessions - is kept in IndexedDB after every request; cookies in a jar of the
runtime's own. `pad pack a,b,c` packs several with a launcher; `--serve` serves the result,
`--panel=button|key|none`, `--name`, `--id`; `pad pack --install` puts the packages in
`~/.cache/pad-wasm`. `apps/<app>/_offline.json` names the application, its icons and `env`
values (`padSqlDriver=sqlite` for an application that uses MySQL on its server - `padEnv` reads
them first); `.env` is never packed. A panel (corner badge, Ctrl+Shift+K) browses the SQLite
databases, runs SQL, exports them as `.sqlite` or SQL, imports either, backs `DATA/` up as a
zip. In the browser `padWasm()` is TRUE and `padSse` answers one round per connection. No
MySQL, no network from PHP. `wasm/README.md`; `notes`, `ledger`, `kanban` are the examples.

### HTTP fakes for tests

`padCurlFake ( [ 'https://api.example.com/*' => [ 'status' => 200, 'data' => '{"a":1}' ], '*' => 404 ] )`
answers every fetch of the request - `{curl}`, `data='https://...'`, padCurl, padPrefetch, a ttl
fetch (which skips its cache) - from the fakes, a closure answering per request; a URL no fake
matches is an error without `'*'`. `padCurlRecorded ()` lists the calls, `padCurlFakeStop ()` ends it.

## Template features

### Lookups Between Data Sets
```
{orders}
  {$number}: {echo $customer_id | lookup('customers', 'id', 'name') | ?? 'unknown'/}
{/orders}
```
`lookup(set, key, field)` finds the row of another set - a `{data}` block, a page array, a
`_data/` file - whose key equals the value; the set is indexed once per request. A miss is
`''`; without a field the answer is the row's first field other than the key.

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
{pager 'products', window=2/}        # ‹ 1 … 4 5 [6] 7 8 … 20 ›
```
The pager follows the paged tag (it reads the total the page handling booked), links through
`$padGo` keeping the other query values, sets the variable `page=` was written with (`pg`),
and marks the current page `aria-current="page"`. As a pair it iterates the links as rows -
`$kind`, `$page`, `$label`, `$href` - for markup of your own.

### Recursive Trees
```
<ul>
{tree 'menu', children='items'}
  <li>{$title}{branch}<ul>{recurse/}</ul>{/branch}</li>
{/tree}
</ul>
```
`{recurse}` renders the tree's body again for the current row's children, `{branch}` only
when there are any, and `depth@tree` is 1 for the top rows, one more per level. The rows come
from the first parameter or `data=`; `children` defaults to `children`.

### Form rules on the fields and in the browser

A `{form}` with `{input}` fields and `padValidate` in the page's PHP is described in CLAUDE.md's Forms section.

The rules can stand on the fields instead - checked before any PHP runs:
```
{form 'contact', error='Please correct the errors below.'}     # the message above the fields
  {input 'email', type='email', label='E-mail', rules='required|email'/}
  {textarea 'message', label='Message', rows=6, rules='required|max:2000'/}
{/form}
```
A post of the form is validated against them from the page's text (lib/form.php), so
`padPosted ( 'contact' )` is TRUE only for a post that kept them - the PHP stores and
redirects, a failed post renders refilled with its messages (`padFormFailed`). Only the
page's own template (and its `_inits.pad`/`_exits.pad`) is read, literal rules only: rules in
a snippet, custom tag, `{page}` or layout, from a variable, in an `action=`/GET form or on a
file field are errors.

`client` on the form checks the same rules in the browser too, with the same messages, before
the form leaves (`pad/lib/validate.js`, written once a page). For a form a component draws,
keep the rules in PHP - `padValidate ( $rules )` checks the post, `padValidateClient ( $rules )`
exports them for `padValidate.check ( rules, values )` in the browser, `{validator}` brings the
checker:
```
{form 'signup', client, error}{input 'email', label='E-mail', rules='required|email'/}{/form}
<div x-data="{ rules: {^rules}, form: {}, errors: {} }">...</div>{validator/}
```

### Layouts (extends / block / parent)
```
{extends '_layouts/report'/}                  # replaces the _inits/_exits frame
{block 'title'}Sales{/block}                 # overrides the layout's {block 'title'}...{/block}
{block 'sidebar'}{parent/} <a>Export</a>{/block}
<p>the rest goes to the layout's @page@</p>
```
Resolved in the text while the page is assembled. Without `{extends}` a page's blocks
override the regions of its directories' `_inits.pad` - `{block 'title'}` sets the wrapper's
title without PHP. The block name is always quoted (`{block}` alone is the `_common` snippet).

### Components (slots / parms)
```
{card title='Revenue'}                       # _tags/card.pad:
  <strong>{$revenue}</strong>                #   {parms title, tone='info'/}
  {slot 'footer'}<a>Report</a>{/slot}        #   <h2>{#title}</h2> @content@
{/card}                                      #   {slot 'footer'}<footer>@content@</footer>{/slot}
```
A `{slot}` pair directly in a custom tag's content is a fill for that use of the tag; any
other `{slot}` is where a fill goes, its content the default (`@content@` in it frames the
fill). `{parms}`: a bare name is required, `name=default` fills in; an undeclared parameter
is an error under the strict check.

### Tags defined in the template (define / macro)
```
{define 'card'}{parms title/}<div><h2>{#title}</h2>@content@</div>{/define}   # an _tags/card.pad without the file
{card title='Revenue'}{$revenue}{/card}

{macro 'entry', label, type='text'}<label>{$label} <input type="{$type}"></label>{/macro}
{entry 'E-mail', 'email'}  {entry label='Name'}                 # in order or by name; fields {$label}
```
A `{define}` is a custom tag: `{#name}`, `{parms}`, `@content@`, slots. A `{macro}` declares its
parameters - a name alone required, `name=default` filling in - and they are the fields of its one
row. Both render at each use, in its place; both exist from where they are walked on (a use before
is an unknown tag), and may use themselves. `define:`/`macro:` assert the kind.

### Web components (declarative shadow DOM)
```
<pad-card>                                   # in a custom tag, _tags/card.pad
  {shadow css='www:card.css'}<header><slot name="title"></slot></header><slot></slot>{/shadow}
  <span slot="title">{#title}</span>@content@
</pad-card>
```
`{shadow}` writes `<template shadowrootmode="open">` with the stylesheets inlined (`mode='closed'`,
`focus`): the element shows styled and slotted before any script ran, and a module that defines
it finds `this.shadowRoot` ready.

### Stacks (push / stack)
```
<head>{stack 'scripts'/}</head>                # filled in after the whole page rendered

{push 'scripts', once='chart'}                # where a component needs it; once per key
  <script src="chart.js"></script>
{/push}
```
`once` without a key drops a push whose text the stack holds; `padStackPush('scripts', $html)`
from PHP. A push inside a `{cache}` section is made again on every hit.

### Page Metadata
```
{meta title='Monthly report', layout='_layouts/print', cache=600/}
```
Read while the page is assembled, after the PHP: `title=` (any non-engine name) becomes
`$title` for the wrapper, `layout=` is `{extends}`, `cache=` the page's own cache time (0 keeps
it out) when the app caches; `access=`/`sitemap=` are kept for `padMeta('access')`.

### Response Fragments
```
{fragment 'order-list'}<ul>{orders}<li>{$number}</li>{/orders}</ul>{/fragment}
```
`?orders&padFragment=order-list` (or `$padFragmentOnly` set in PHP) answers with that
fragment alone - for HTMX swaps and `{ajax 'orders', fragment='order-list'/}`; a normal
request renders the whole page. An htmx request needs no parameter when its target is named
like the fragment: `hx-target="#order-list"` sends `HX-Target: order-list`, and a page with
that fragment answers it alone (one without renders whole). The page cache answers a script's request for a part -
`HX-Request`, `X-Requested-With`, `Turbo-Frame`, `X-Up-Target` - fresh and never stores a
fragment the PHP chose, since its key is the address.

### HTML attribute helpers
```
<button {attrs disabled=$busy, title=$help, aria-expanded=$open/}>Save</button>
<div class="{classes 'panel', active=$isActive, invalid=$hasErrors/}">
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

```
{cache 'top-products', ttl=300}{topProducts}<li>{$name} {nocache}{stock $id}{/nocache}</li>{/topProducts}{/cache}
```
A `{nocache}` part of a cached section - or of a page the page cache keeps - renders on every
request, the hit included; inside a loop it gets its row's fields back. A page-cache hit runs no
PHP, so there a part sees the request, the session, `_lib` and the application's tags only.

A cached section keeps its text, not its effects: what it sets, stores or defines - `{set}`,
`{increment}`, `{data}`, `{content}`, `{define}`, `{macro}`, a `callback=`'s totals - happens on
the miss only. Put it before the section, or in a `{nocache}` part, which renders every time.

### Remote data with a cache
```
{curl 'https://api.example.com/rates.json', ttl=600/}
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
{trans 'cart.title'/}                          # _lang/<locale>.json: "cart.title": "Your cart"
{trans 'cart.items', count=$n/}                # "%d item|%d items" - count picks the form
{trans 'greeting', name=$user/}                # "Hello :name"
{echo 'cart.title' | trans/}                   # the pipe form
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
{chart 'bar', data='sales', label='month', value='amount'/}   # inline SVG, no JavaScript
{chart 'line', data='visits', title='Visits this week'/}      # data: store, page array, _data file
{chart 'bar', data='sales', value='online, shop', stacked/}   # several series; 'hbar' lies down
{chart 'pie', data='budget'/}                                 # also donut
{chart 'scatter', data='cars', x='weight', y='mpg', color='origin', trend/}   # bubble: size=
{chart 'heatmap', data='visits', x='hour', y='day', value='count'/}
{chart 'sankey', data='energy', source='from', target='to', value='amount'/}
{chart 'network', data='friends', source='a', target='b', layout='radial', arrows/}
{chart 'treemap', data='spend', levels='dept', label='post', value='amount'/}  # also sunburst
{sparkline sequence='fibonacci', rows=12/}                    # word-sized; sequence terms as data
{chart 'gauge', value=72, unit='%', bands='60, 85', target=80/}   # one number - no data needed
{chart 'radar', data='phones', label='model', value='battery, camera, screen'/}
{chart 'waterfall', data='result', label='step', value='amount', total='Profit'/}
{chart 'histogram', data='people', value='height', bins=12/}  # also boxplot (label= groups)
{chart 'calendar', data='commits', date='day', value='count', year=2026/}
{chart 'gantt', data='plan', label='task', from='begin', to='until', progress='done', mark='2026-10-07'/}
{chart 'area', data='energy', label='year', value='coal, gas, wind', stacked/}   # percent; also stream, multiples (by=)
{chart 'candlestick', data='share', label='day', volume='volume'/}   # open/high/low/close; dual: value= bars, line= right axis
{chart 'funnel', data='shop', label='step', value='visitors'/}       # pyramid: one field a triangle, two back to back
{chart 'waffle', data='energy', cells=100/}                          # also marimekko, parliament, venn (sets=), rose, radialbar
{chart 'bullet', data='kpis', value='actual', target='goal', bands='50, 75'/}   # also rings, proportional
{chart 'violin', data='scores', label='class', value='score'/}       # also density, parallel (value= the axes), spiral (period=)
{chart 'chord', data='migration', source='from', target='to', value='people'/}  # also arc
{chart 'orgchart', data='staff', id='id', parent='boss', label='name', sub='role'/}  # pack: the treemap's options
{chart 'bar', label='month', value='amount'}                 # a pair: the content is the data -
  month,amount                                               #   JSON, YAML, XML or CSV, told
  Jan,12400                                                  #   apart on sight (type= names it)
{/chart}
```
`role="img"` with `<title>`/`<desc>`; colours are `--pad-chart-*` custom properties on
`.pad-chart`, following the page's `color-scheme`. A field option left out takes the first
field that fits; the chart's options keep clear of the handling options (`row`, `group`).
The kinds are the files of `pad/charts/types/` - one file a kind, its default size and the
function that draws it; a kind is added by adding its file. Each is written three ways, the
same chart: `{chart 'pie', data='budget'/}`, the chart type `{chart:pie data='budget'/}`, and
bare, `{pie data='budget'/}`, when no tag, store, field or sequence has the name (the bare
name comes last, after every other kind of tag). `{sparkline .../}` is the sparkline kind.
Code: the chart subsystem in `pad/charts/` - `chart.php` the body every form runs,
`_lib/` the drawing (`chart.php` bar, line and sparkline, the files beside it the other
kinds), `types/` the kinds; `pad/types/chart.php` the chart type.

### Month calendar
```
{calendar data='agenda', title='what'/}         # this month (or ?month=2026-11), events, links ‹ ›
{calendar '2026-10', data='agenda'}            # as a pair: weeks as rows, days inside, events per day
  <tr>{days}<td class="{$class}">{$day}{events}<b>{$what}</b>{/events}</td>{/days}</tr>
{/calendar}
```

### Syntax highlighting
```
{highlight 'php', lines, mark='3'}<?php echo $x; ?>{/highlight}   # pad php html css js json yaml sql bash
{highlight file='_data/products.json'/}                           # a file of the app, language from its extension
{$query | highlight('sql')}                                       # a value - escaped, so no sanitize on top
{source}                                     # the page's own template and PHP as tabs, coloured
{source 'orders', files='_lib/orders.php, www:orders.js'}           # another page, more files (www: = www/<app>/)
```

### QR codes and barcodes
```
{qr 'https://example.com/order/42'/}            # inline SVG, encoded on the server (tags/_lib/qr.php)
{qr $url, size=200, level='H', title='Scan'/}   # level L, M (default), Q, H; color=, background=
{barcode '871234567890'/}                       # EAN-13 (check digit added); type= ean8, upca, code128
{barcode $parcel, type='code128', height=70/}   # scale= module width, plain = no digits under the bars
```

### Showcase tags
```
{avatar 'Herbert Jebbink', size=48, shape='square'/}   # initials in a circle/square, colour from a hash of the name
{identicon $email, size=64/}                   # a symmetric 5x5 pattern from a sha256 hash, own colour
{placeholder '600x300', text='Hero image'/}    # grey SVG box with crossing lines; ratio='16:9', fluid
{progress 72, max=100, label='Upload'/}       # styled <progress> with label and %; steps for dots, color=, size=
{rating 4.5, max=5/}                           # stars as SVG, half stars clipped, aria '4.5 out of 5'; icon='heart'
{icon 'arrow-right', size=20, label='Next'/}   # built-in line icon in currentColor; decorative unless label=
{gravatar $email, size=64, default='identicon'/} # gravatar.com img (sha256, lazy, no referrer); fallback='avatar' stays local
{chess $fen, flip, highlight='e2,e4', arrow='e2-e4'/}   # a FEN as an SVG board, pieces described for a screen reader
{sudoku '53..7....6..195...', solve/}             # 81 cells as a table, givens bold; solve fills the solution in
{crossword}ECHO: Writes a value{/crossword}       # WORD: clue lines laid out, numbered SVG grid + Across/Down clues; solution
{diff $old, $new/}                              # word diff with <del>/<ins>, escaped; lines, side, from=, to=
{excerpt $body, words=24, highlight=$q/}         # whole words around the first match, <mark>ed, escaped; html
{lorem paragraphs=3, seed=7/}                    # placeholder text, classic start, reproducible; words=, sentences=, varied
{tabs active='Specs'}{tab 'Overview'}...{/tab}{tab 'Specs'}...{/tab}{/tabs}   # CSS-only tabs: radios, arrow keys
{accordion single, open=1}{tab 'Question'}Answer{/tab}...{/accordion}   # <details> items, one open
{modal 'terms', title='Terms', button='Read the terms'}...{/modal}   # <dialog>, works with and without JS
{carousel label='Photos'}{tab 'Caption'}<img ...>{/tab}...{/carousel}   # scroll-snap slides, link arrows and dots
{copy 'npm install pad', label='Copy'/}             # the text in <pre>, a copy-to-clipboard button
{poll 'lang', options='PHP, Go', title='Favourite?'/}   # one vote per session, results as bars; DATA/poll/
{img 'photos/harbour.jpg', width=400, height=300, alt='The harbour'/}   # GD thumbnail in www/<app>/_thumbs/, 2x srcset, lazy
{video 'https://youtu.be/ID', title='Launch'/}  # poster + play button, nocookie iframe only after a click
{pdf file='invoice', download}...{/pdf}   # the content as a PDF (plain PHP writer) instead of the page; preview: a sheet + link
{diagram direction='LR'}A[Order] --> B{Paid?}{/diagram}   # flowchart (or type='sequence') laid out as SVG on the server
{map 'europe', data='sales', key='country', value='amount'/}   # choropleth: ISO alpha-2/3 or name, world or a region
{timeline data='releases', date='when', label='what'/}   # cards on a time axis; from=/to= spans, vertical for long lists
{datatable data='orders', columns='number, customer, total', totals='total', sortable, rows=10/}   # a whole table: totals row, server-side sort and page links
{toc levels='2,3', title='Contents'/}         # the page's own h2/h3, nested and linked - filled in after the page rendered
{countdown '2026-12-31 00:00', units=3, live/}   # 84 days, 3 hours, 12 minutes in a <time>; live ticks; past='...'
{timeago $created/}                           # <time datetime title>5 minutes ago</time> - the ago pipe's words
{emoji 'rocket'/}  {$text | emoji}             # role="img" aria-label="rocket" 🚀; the pipe replaces :shortcodes: in text
{country 'NL'/}  {country 'NLD', name/}         # the flag (role="img", named Netherlands); name writes the name beside it
{jsonview $response, open=2/}                 # foldable <details> tree, typed colours, counts on folded nodes; pair: JSON content
```
Every one is rendered on the server; the `showcase` application has examples of each.

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

## Development

### Configuration settings

Every `$pad*` setting with its default and a comment on what it does stands in
`pad/config/config.php`; an application's `_config/config.php` - or `_common`'s - overrides
it. The database settings come from `.env` files (CLAUDE.md, Configuration). The settings
met most, as an application's `_config/config.php` would set them:

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

// Vite: {vite 'src/main.js'} reads www/<app>/<$padViteBuild>/.vite/manifest.json; while the dev
// server runs ($padViteDev, or its address in that directory's hot file) it is asked instead.
$padViteBuild = 'build';
$padViteDev   = '';

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
- unbalanced quotes, `( )` or `[ ]` — `{echo (1 + 2/}` → *the ( opened at position 1 is never closed*
- a misspelled pipe function — `{echo $x | uppr/}` → *there is no pipe function named 'uppr'*
- a comparison operator missing an operand where no pipe value can stand in for it —
  `{if $x eq}` → *the operator 'eq' has nothing on its right*
- an undefined field — `{if $typo eq 1}` → *there is no field named '$typo'*
- what an expression cannot mean — `{if $x === 1}` → *'===' is no operator - eq (==) compares
  two values*; `$n + * 3` → *the operators '+' and '*' stand side by side*; `3 & 1` → *'&' is
  no operator or value*
- a PHP function refusing its arguments — `{echo 'ab' | php:str_pad(8, '*')/}` → *the PHP
  function 'str_pad' refused its arguments: ... @ puts it in its place*

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

### Application tests

An application keeps its own tests in `_tests/`: `cart.pad` (with `cart.php` when it needs
data) is the test page, `cart.txt` its answer - an exact body, a `/regex/`, or `HTTP 500`
with an optional `/regex/` on the next line, as in the framework's suites. A test page renders
bare, no URL reaches it, and it can bring in a real page with `{page 'cart'/}`. `{assert
$total eq 42, 'the total'/}` fails a test run when false and is silent in production
(`$padAssert`, off by default, on under `pad test`). `pad test shop --record` writes the
missing answers; `./ci.sh` runs `pad test --all` and fails on a failing test.

### The frame of the PAD applications

Every application that uses `_common` - and admin, edit and tags, which have it off and reach
it with the `common:` prefix - shares one frame from `_common`, which has no wrapper of its own
(no `_common/_inits.pad`): a side with the `{}` logo and `PAD <app>` - a click opens the menu
of the applications - the application's menu under it, and a bar with the title and the
light/dark switch above the page; side and page are light or dark together, the choice kept
for all of them (`www/_common/pad.js`). Their `_inits.pad`:

```
{common:padFrame}                                  # _common/_tags/padFrame.pad + .php
  {slot 'head'}{asset 'admin.css', tag/}{/slot}     # also 'title', 'tools', 'side', 'foot'
{/common:padFrame}
@page@
{common:padFrameEnd/}                               # _common/_include/padFrameEnd.pad
```

`$frameMenu` (rows of `head`, or `link`, `label`, `icon`, `current`) from `_inits.php` is the
menu, `$title` the bar's title (`$skipTitle` leaves it out); `{common:padFrame fill}` for a
page that fills the window. The stylesheet is `www/_common/pad.css`: the
`--frame-*` properties are the frame's own, `--bg`, `--text`, `--accent` ... the palette the
pages use - the old pages' `bgcolor=`, `<font color>` and `highlight_string` colours read in
dark too.

### Common Patterns

#### Zebra Striping
```
{items}
  <tr class="{even@items ? even : odd}">{$name}</tr>
{/items}
```

Or using the `{switch}` tag:
```
{items}
  <tr class="{switch 'odd', 'even'/}"><td>{$name}</td></tr>
{/items}
```

#### Comma-Separated List
```
{items}{notFirst@items}, {/notFirst@items}{$name}{/items}
```
Output: `Alice, Bob, Charlie`

#### First/Last Wrapper
```
{items}
  {first@items}<ul>{/first@items}
  <li>{$name}</li>
  {last@items}</ul>{/last@items}
{/items}
```

#### Counter Display
```
{items}
  {current@items/} of {count@items/}: {$name}
{/items}
```

#### Dynamic Fields
```
{record "* from users where id=5"}
  {fields@record}
    {$name}: {$value}
  {/fields@record}
{/record}
```

#### Nested Loop with Arithmetic
```
{sequence '1..3', name='row'}
  <tr>
    {sequence '1..4', name='col'}
      <td>{echo $row * 10 + $col/}</td>
    {/sequence}
  </tr>
{/sequence}
```

#### Extract Domain from Email
```
{echo $email | after('@') | before('.')/}
```
