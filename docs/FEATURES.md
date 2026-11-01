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
  - [Structured data (JSON-LD)](#structured-data-json-ld)
  - [JSON and CSV from the same page](#json-and-csv-from-the-same-page)
  - [Data formats](#data-formats)
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
  - [Log in with GitHub, Google ... (OAuth)](#log-in-with-github-google--oauth)
  - [Notifications on every channel](#notifications-on-every-channel)
  - [A language model answering in the page](#a-language-model-answering-in-the-page)
- [Template features](#template-features)
  - [Lookups Between Data Sets](#lookups-between-data-sets)
  - [Grouping with subtotals (group option)](#grouping-with-subtotals-group-option)
  - [Page links (pager tag)](#page-links-pager-tag)
  - [Recursive Trees](#recursive-trees)
  - [Form rules on the fields and in the browser](#form-rules-on-the-fields-and-in-the-browser)
  - [Rich form fields](#rich-form-fields)
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
  - [Site search](#site-search)
  - [Charts](#charts)
  - [Games](#games)
  - [Diagrams](#diagrams)
  - [Maps](#maps)
  - [Spreadsheets](#spreadsheets)
  - [Embeds](#embeds)
  - [Toys](#toys)
  - [Sounds](#sounds)
  - [Printables](#printables)
  - [Widgets](#widgets)
  - [Month calendar](#month-calendar)
  - [Public holidays](#public-holidays)
  - [Syntax highlighting](#syntax-highlighting)
  - [QR codes and barcodes](#qr-codes-and-barcodes)
  - [Icons](#icons)
  - [Patterns](#patterns)
  - [Image filters](#image-filters)
  - [Notations](#notations)
  - [Showcase tags](#showcase-tags)
  - [Live Regions (interactivity without app JavaScript)](#live-regions-interactivity-without-app-javascript)
  - [Sections](#sections)
  - [Effects](#effects)
  - [Themes](#themes)
  - [Palettes](#palettes)
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

### Structured data (JSON-LD)
```
{jsonld:article headline=$title, author=$author, published=$date, image='cover.jpg'/}
{jsonld 'article', data='post', url=$url/}                # a row, an option winning over its field
{jsonld:faq data='questions'/}                            # rows of question and answer
{jsonld:breadcrumb/}                                      # the trail {breadcrumb/} shows for the page
{jsonld:product data='product', availability='InStock'/}  # an Offer of price, currency, availability
{jsonld:website name='Shop', search='?search&q='/}        # a SearchAction ending in {search_term_string}
{jsonld:recipe data='recipe'/}                            # times, ingredients and steps of a dish
{jsonld:video data='clip'/}                               # thumbnails, upload date, length, views
{jsonld:business type='Bakery', name='The Loaf', city='Utrecht', hours='Mo-Fr 08:00-18:00; Sa 08:00-16:00'/}
{jsonld:job data='vacancy', salary='3200-3800', currency='EUR', per='month'/}
{jsonld:software name='Pocket Ledger', category='finance application', price=0, rating='4.6', ratings=1523/}
{jsonld:profile data='member'/}                           # a member's page, the person its mainEntity
{jsonld:course name='PAD in a day', description='From the first tag to a shop.', provider='PAD Academy'/}
{jsonld:itemlist data='recipes', name='Soups'/}            # the pages a summary page lists, numbered
```
```php
$url  = padUrl ( '', [], TRUE );                          // the page's own address, for url=
$data = padJsonLd ( 'event', $concert );                  // the same object as a plain array
```
One `<script type="application/ld+json">` holding a Schema.org object - what search engines read
for a rich result - from ordinary PAD data, explicitly mapped. Seventeen kinds, each a file of
`pad/subs/jsonld/types/` whose header lists its fields, a PAD name and the Schema.org property it
fills: `article` (headline required, description, image, url, author, published, modified,
publisher, logo), `breadcrumb` (rows of name and url - the rows `{breadcrumb data=}` takes,
label and link too - or without `data=` the page's trail as the breadcrumb widget shows it,
`home=` its first step, a directory that is no page left out, as a step needs its address),
`event` (name and starts required, ends, place and address as its Place, description, image,
url, and price, currency, availability and tickets as an Offer - not
`start=`/`end=`, which are the handling options), `faq` (rows of question and answer),
`organization` (name required, url, logo, description, profiles as sameAs, and phone, email
and contact as a ContactPoint), `person` (name required, url, image, job, description,
profiles), `product` (name required, description, image, url, sku, brand, price, currency,
availability) and `website` (name, url - the application's own address when left out -
description, search). A row's field or an option of the Schema.org name, `datePublished`,
counts as its PAD name. `id=` is the `@id`.

The kinds after those take the required and recommended properties of Google's rich result of
the same name. `recipe`: name and image required (one address or more - Google asks for a 16x9,
a 4x3 and a 1x1), description, author, published, prep, cook and total as durations, yield,
category, cuisine, keywords, calories as its NutritionInformation - which needs the yield, the
energy being that of a serving - ingredients and steps, each a step a HowToStep of its text or of
the text, name, url and image of a row, and rating with ratings as an AggregateRating.
`video`: name, thumbnail (one address or more) and uploaded required - with its offset, or Google
takes its own time zone - description, duration, file (contentUrl, the video file itself), player
(embedUrl), expires, author and views as an InteractionCounter of the WatchAction - not
`content=`, the option of every tag. `business`: a LocalBusiness - type (LocalBusiness or the most
specific of the 151 Schema.org types under it, `Dentist`, `BookStore`, `bar or pub` in any case or
spacing), name and city required, description, image, url, phone, email, street, city, region,
postcode and country as its PostalAddress, latitude and longitude as GeoCoordinates (each needing
the other, -90 to 90 and -180 to 180), hours and prices (priceRange). `restaurant`: every field
of the business and cuisine (servesCuisine) and menu, which a business of another type cannot
have - type Restaurant, or Bakery, BarOrPub, CafeOrCoffeeShop and the other food establishments.
`job`: a JobPosting - title, description (which may hold `<p>`, `<ul>` and `<li>`), posted and
company required, expires (validThrough), employment (one or more of FULL_TIME, PART_TIME,
CONTRACTOR, TEMPORARY, INTERN, VOLUNTEER, PER_DIEM and OTHER, `full time` too), companyurl and
logo of the hiring Organization, street, city, region, postcode and country of its place - the
country needed with any of them - remote (yes writes jobLocationType TELECOMMUTE) with countries
(applicantLocationRequirements, a Country each), and salary (an amount or a range, `3200-3800`),
currency and per (HOUR, DAY, WEEK, MONTH or YEAR) as a MonetaryAmount - a salary needs both;
not `min=`/`max=`, which are options of every tag. `software`: a SoftwareApplication - type
(MobileApplication, WebApplication), name and price required (`0` for one that is free), description,
url, image, os, category (one of the 22 applicationCategory values Google takes, `finance
application`), version, download, currency, and rating with ratings as an AggregateRating - Google
shows the rich result for an application with a rating or a review. `profile`: a ProfilePage, the
page of one member of a site, with the person - or `type='organization'` - as its mainEntity: name
required, handle (alternateName), identifier, description, image, profiles (sameAs), followers and
posts as InteractionCounters of the FollowAction and the WriteAction, and created and modified,
the page's own dates. `course`: a Course - name and description required (Google shows its first
60 characters), provider (an Organization by its name) and providerurl (its sameAs), url, image and
code (courseCode); Google shows courses as a list, the itemlist kind on the page that lists them.
`itemlist`: an ItemList - rows of url (required, or link, href - the address of each item's own
page) and name (or label, title), each a ListItem numbered by its position, a list of plain values
a list of addresses, and `name=` the list's own name; with a recipe, a course, a restaurant or a
movie on each page it lists, Google shows the list as a carousel (two items or more, of one kind).

The fields come from the row or rows `data=` names - a `{data}` store, the page's array, a
field holding a list or a `_data` file (a store holds rows, so a single row in a `{data}` block
is a list of one) - and from options of their names, which win over the row's. A row is never
written whole: only what the kind maps, so a password, a cost price or an e-mail address of the
row never reaches the page. Several rows of a kind of one object make a `@graph`; the handling
options (`where=`, `sort=`, `first=`) work on the rows, and a `data=` without rows answers
nothing, its `@else@` shown. An empty field is left out, `0` is a value.

Values are written as Schema.org wants them. An address is made absolute: one of http or https
stands, `//host/x` takes the page's scheme, `/x` is on this host, and `?page`, `#part` or a
relative path are relative to the application's address (`http://host/pad/shop/`), where an
`<img src>` of its pages finds its file; `javascript:`, `data:` or any other scheme is left out.
A date passes as it is when it is ISO 8601 (`2026-11-20`, `2026-11-20T19:30:00+01:00`); another
text that PHP's `date_parse` reads in full is written so (`20 November 2026 19:30` is
`2026-11-20T19:30:00`, with its offset when it names a zone, none invented - the name of its day
beside it, as an RSS `pubDate` has it, `Fri, 20 Nov 2026 19:30:00 +0000`, when it is the date's
own), a date object with its offset and an integer as a Unix time in UTC; a relative date -
tomorrow, next friday - is none, as its value would change by the day. A price is a number with a dot, kept as written (`"39.95"`), a
currency three letters of ISO 4217, an availability one of Schema.org's (`InStock`, `in stock`
or its address). `search=` is the search address up to the term, written as the urlTemplate
with `{search_term_string}` after it - braces that stay text, as every value's do; an address
from PHP that holds `{search_term_string}` keeps it where it stands.

The later kinds' values (`pad/subs/jsonld/_lib/values.php`): a duration passes as ISO 8601
(`PT1H30M`, in any case) or is written so from words - `90 min`, `1 h 30 min`, `1.5 hours`,
`2 days`, each number with its unit, `m` minutes - and a number alone, without its unit, is none.
A list is a list in the row, or a text with a line between its values - an ingredient may hold
a comma; an option holds no list. A rating is a number with a dot, a count a whole number, both
written as JSON numbers. An energy that is a number alone is that many calories. Opening hours
are written as Schema.org's openingHours writes them - `Mo-Fr 09:00-12:00,13:00-17:30; Sa
10:00-16:00`, a day `Mo`, `Mon` or `Monday`, a range past Sunday (`Fr-Mo`), a time 00:00 to 23:59,
closing before opening open past midnight - and become an OpeningHoursSpecification per time,
Google's form, its days a text or a list, opens and closes as hh:mm:ss. A type and the other
words of a list (an employment, a category) are matched in any case and spacing and written as
Schema.org or Google writes them; a long list's error names the word that comes nearest. A value that
is none of these, a row where a list holds texts and a step without its text are errors under
the strict check, naming the field and the kind, and left out without it.

The JSON is encoded for its element: `JSON_HEX_TAG` writes `<` and `>` as `\u003C` and
`\u003E`, so a `</script>` or `<!--` in a value cannot end the element, slashes and Unicode stay
as they are, and every value is taken out of PAD's stand-ins first, so none comes back a live
quote inside a JSON string. No CSP nonce is written: a script of a type other than JavaScript is
a data block, which the browser never runs, and `script-src` governs only scripts that run. The
block stands where the tag stands - in the head or the body, both read by search engines.

A missing required field, a date that is none, a price with a comma, a currency or an
availability that is none, a price without its currency, an option the kind does not take and
content between the tags are errors under the strict check, naming the field and the kind; the
lenient walk leaves the object out (its `@else@` shows) - or, for a value it cannot write, the
field. The kinds are never reached bare: article, event and person are names of fields and
pages. Code: `pad/subs/jsonld/` - `jsonld.php` the body both forms run, `_lib/jsonld.php` the
mapping, `types/` the kinds; `pad/types/jsonld.php` the type, `pad/tags/jsonld.php` the tag,
`pad/lib/jsonld.php` `padJsonLd`.

### JSON and CSV from the same page

A page answers its data instead of its template when asked - `?orders&padFormat=json` (or
`csv`), or an `Accept: application/json` / `text/csv` header that does not accept `text/html`:
a header naming `text/html` at any quality, or `*/*` (`text/*`) at the format's quality or
above, gets the page. What may leave the server is an explicit list in the page's `.php`; a
page that names nothing answers HTML only:

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
  exist, or CSV - any table format - without a list answers 406; asked only through
  `Accept`, the page renders as HTML. An exposing page sends `Vary: Accept`.
- `$padOutputType = 'json'` (or `'csv'`) in `_config/config.php` makes every page of an
  application answer data; `{output 'json'/}` does it from a template.

### Data formats

The formats subsystem in `pad/subs/formats/`: a format is a file of `types/` (`padFormatKinds ()`),
with a reader, a writer or both - added by adding its file, its code in `_lib/`, loaded on use.

| Format | Reads | Writes | |
|--------|-------|--------|-|
| `json`, `csv`, `yaml` | yes | yes | the engine's own readers (`pad/data/`); the JSON and CSV page answers are these writers |
| `xml` | yes | - | rows have no one XML of their own: `?page&padFormat=xml` stays 406 |
| `toml` | yes | yes | TOML 1.0: tables, arrays of tables, inline tables, dotted keys, every string, number and date |
| `ini` | yes | yes | `[sections]`, `; comments`, quoted values, `key[]` lists, `key[name]` records |
| `tsv`, `ndjson`, `markdown` | yes | yes | tab separated (`\t \n \\` escapes, a leading tab an empty cell, a formula cell written with a `'` as in CSV), a JSON value a line, a GFM pipe table (the first of a text; written lined up) |
| `ics` | yes | yes | iCalendar: the VEVENTs as rows - uid, summary, start, end, allday, timezone, location, description, url, status, categories, rrule |
| `vcf` | yes | yes | vCard 4.0 (3.0 read too): name, the parts of N, org, title, email(s), phone(s), url, address, birthday, note, uid |
| `geojson` | yes | yes | a feature a row: its properties, id, geometry, lat/lon of a Point - Points written from lat and lon |
| `gpx` | yes | yes | waypoints, route and track points as rows: type, track, segment, point, name, lat, lon, ele, time, desc |
| `xlsx` | yes | yes | one sheet: a bold frozen header, numbers, booleans and dates as such, text never a formula; read: the first sheet (`sheet=`) |
| `rss`, `atom`, `jsonfeed` | - | yes | feeds of rows with title, link, date, summary, body, id - a Markdown collection's rows as they are |

```
{data 'plan', format='toml'}            # the content in a format - type= says the same
  title = "Weekend"
  [[stops]]
  name = "Gravensteen"
{/data}
{data 'agenda'}BEGIN:VCALENDAR ...{/data}   # known on sight: iCalendar, vCard, NDJSON, a pipe table, TSV
{sales}...{/sales}                      # _data/sales.xlsx - a _data file by its extension
{local:plan.toml}...{/local:plan.toml}  # {local:sales.xlsx sheet='Notes'}
```

```php
$rows = padFormatRead  ( 'ics', $text );                     // a format or an extension: md, jsonl, yml
$text = padFormatWrite ( 'atom', padCollection ( 'blog' ), [ 'title' => 'Blog', 'link' => 'https://example.com/blog/' ] );

$padExpose        = [ 'events' ];                            // ?events&padFormat=ics, Accept: text/calendar
$padFormatOptions = [ 'title' => 'Talks', 'name' => 'Talks' ];  // the writer's options for the answers
```

- A page answers its `$padExpose` in every format that writes, as it answers JSON and CSV:
  `&padFormat=<format>` (or an extension, `md`) or an `Accept` header naming the format's media
  type that does not accept `text/html` - `text/calendar`, a feed reader's `application/rss+xml,
  */*;q=0.8`; Markdown, an agent's `text/markdown` asking for the page, only by `padFormat=`.
  A table format - `ics`, `xlsx`, the feeds, `geojson`, `gpx`, `vcf`, `md`, `tsv`, `ndjson` -
  answers the first exposed list, `toml`, `yaml` and `ini` the variables by name. The answer
  has the format's content type and, for a file, the page's name with the format's
  extension - `inline` (`events.ics`), an `attachment` for `xlsx` and `gpx`,
  none for a feed. A row the format has no place for - an order without a date in a calendar -
  is left out of such an answer; asked of the helper it is an error under the strict check.
  `$padOutputType = 'format'` with `$padOutputFormat = 'ics'` makes every page answer that.
- Deterministic: the same rows give the same bytes - an event's UID is made of the row, its
  DTSTAMP is the row's `stamp`, `stamp=` or its start, a feed's updated the newest entry, a
  zip's parts dated 1980-01-01 - never the clock.
- A text a format cannot read is an error under the strict check naming the format and the
  line - `TOML error on line 3: the key 'name' is defined twice` - and no rows on the lenient
  walk. The formats known on sight are known only where the text would have read as CSV, or
  as a JSON document that is none: TOML, INI, GeoJSON and GPX are named, or read by extension.
  A fetched body - `{curl}`, `data='https://...'` - is read in the format its media type names:
  `application/x-ndjson`, `text/calendar`, `application/toml`, `application/geo+json` ...
- A data file runs as PAD before it is read, as a JSON file does - a `{` in a TOML comment is a
  tag; an `xlsx` is bytes, read as the file holds them - a part of at most 32 MB unpacked,
  500000 cells and shared strings, columns up to XFD: past that, the format's error.
- Option names: `title`, `link`, `self`, `description`, `author`, `language`, `id`, `updated`
  (feeds), `name`, `stamp`, `prodid` (ics), `name`, `as`, `creator` (gpx), `sheet` (xlsx),
  `name` (toml, for a list handed over whole).

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

The subsystems' own style sheets and scripts - the widgets, games, toys, inputs, sections,
effects, notations, printables, sounds, the charts drawn in the browser ... - are files of
`www/assets/<sub>/` (`widgets/tabs.css`, `games/memory.js`, `charts/core.js`), served by the web
server as they are. A page links each one the first time a tag needs it - `<link
rel="stylesheet" href="/pad/assets/widgets/tabs.css?v=<hash>">`, `<script
src="/pad/assets/games/game.js?v=<hash>">` without async or defer, so it runs where it stands -
so the browser keeps them from page to page and a changed file is a new address; the version
is kept between requests with the file's inode, times and size (`DATA/cache/assets/`), so a
page that links a file makes one stat of it, the file read and hashed again only when it
changed. The colours
a tag makes from its roles stay a small `<style>`. The style sheets of a whole page are moved
into its `<head>` before tidy reads it (a bare template's go at its start); a fragment, a live
region's answer and a `{shadow}` root keep theirs where they stand, and a `{live}` answer runs
its scripts in order, one of `www/assets/` loaded before the next. With `$padCsp` naming a
nonce the links and scripts carry it. The text is written into the page instead - a `<style>`
and a `<script>`, as before the files were linked - in a mail, a notification, an offline
application (`pad pack`, which carries `www/assets/`), a static copy (`pad export`), every
output that is not the web's - the command line's (`pad render`) among them - and everywhere
with `$padAssetLinks = FALSE`. The browser's
checker of the form rules stays written in: it is made of the files of `www/assets/rules/` and
the rules' PHP, no file a page could link. With the version in every address a server may send
`www/assets/` with a Cache-Control of a year.

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

The kinds of fake value are the files of `pad/subs/fakes/types/` - address, boolean, city, colour,
company, country, currency (a code of ISO 4217, three capitals the currency pipe takes), date,
decimal, ean (an EAN-13 of the GS1 prefixes 20-29 kept for
restricted circulation, its check digit right), email, firstname, iban (a Dutch number of the bank TEST that
passes the check), ip (the documentation ranges), ipv6 (of 2001:db8::/32, kept for
documentation, as RFC 5952 writes it), isbn (an ISBN-13 of 978 whose check digit holds), job, lastname, latitude and longitude (a
point of the box south=, north=, west=, east=, across the 180th meridian when west= lies east
of east=), mac (locally administered, so no maker's), name, number, paragraph,
phone, postcode, price, product (a look, a material and a thing made of it, Rustic Oak Stool),
sentence, slug (lower-case words and dashes), street, time, timezone (a zone of the IANA
database, never one kept only as a link), title (a
heading of words= words), url, username, uuid, word - each named by
`padFake ( 'email' )` and in a template:
```
{fake 'email'/}  {fake:city/}  {fake 'number', from=1, to=6/}  {fake 'date', from='2026-01-01', format='j M Y'/}
{fake 'name, email', rows=5, toData='people'/}               # rows of fields named after their kinds
{fake 'customer:name, total:price', rows=3}<li>{$customer} {$total}</li>{/fake}
{fake 'name', seed=7/}                                        # a value of its own seed
{fake 'lat:latitude, lon:longitude', south=50.75, north=53.55, west=3.36, east=7.23, rows=5/}  # points of a box
```
`padFakeRows ( 'name, email', 5, 7 )` gives the rows `{fake 'name, email', rows=5, seed=7/}`
gives - a fact of the seed, the sequence put back after them - for a seeder, a prototype's
table or a designer preview's sample. Without `seed=` the `{fake}` tags of a request share a
sequence of their own from one seed, so a page looks the same on every request; either way the
page's own `padFakeSeed` sequence is left where it was. An email or username beside a name is
that person's, a slug beside a title that title's - `{fake 'title, slug, paragraph', rows=3/}`
are the posts of a blog. Locale-neutral English, as the helpers; a fake kind is never reached bare - its
names are those of fields. A kind is added by adding its file, which reads its options with
`padFakeOpt`.

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

### Log in with GitHub, Google ... (OAuth)

The authorization code flow of OAuth 2.0 with PKCE (S256), and OpenID Connect where the
provider speaks it, as two calls on one page - what comes back is a row for `padLogin`:
```php
// oauth/[provider].php - the start and the way back of every provider
if ( ! padRequestHas ( 'code' ) and ! padRequestHas ( 'error' ) )
  padOAuthStart ( $provider );                 // off to the provider: state, PKCE, nonce kept in the session
if ( $user = padOAuthUser ( $provider ) ) {    // back: the state checked, the code exchanged, the user fetched
  padLogin ( $user );
  padRedirectIntended ();
}
$problem = padOAuthError ();                    // a no at the provider, a stale state ...
```
```
{oauth 'github'/}  {oauth 'google'/}  {oauth 'oidc', href='?login/company', label='Company account'/}
```
The providers are the files of `pad/subs/oauth/types/` - apple, discord, forgejo (Codeberg, or
`base=`), github, gitlab (`base=` for one's own), google, microsoft (`tenant=`), oidc (any OpenID
provider, its endpoints from `<issuer>/.well-known/openid-configuration`) - each an array: the
endpoints, the scopes, PKCE, the settings it needs and how its answer maps to the user. The
user: `provider`, `id` (`github:583231` - never the same for two providers, nor for two
instances of one: a GitLab or Forgejo of one's own and every OpenID issuer have their address
in it, `gitlab@git.example.org:42`, `oidc@sso.example.com/realms/main:<sub>`), `sub` (the
provider's own id), `name`, `email`, `email_verified`, `avatar`, `username`; GitHub's private
address comes from `/user/emails`, Apple's name from the first login's post. `email` is a
contact, not an identity: `email_verified` is TRUE only when the provider vouches for the
address - Google's, Apple's and an OpenID provider's `email_verified`, Discord's `verified`,
GitLab's `confirmed_at`, GitHub's and Forgejo's `/user/emails`, Microsoft's optional claim
`xms_edov` (its email is the email claim alone, never `preferred_username`) - so an application
links a login to an existing account by its address only when it is TRUE. The settings are `padEnv` values -
`GITHUB_CLIENT_ID`, `GITHUB_CLIENT_SECRET`, `GITLAB_URL`, `MICROSOFT_TENANT`, `OIDC_ISSUER`;
Apple takes `APPLE_CLIENT_ID`, `APPLE_TEAM_ID`, `APPLE_KEY_ID` and `APPLE_PRIVATE_KEY` and makes
its client secret, an ES256 JWT, with openssl - or options of the same names; a missing one is
a strict error naming the variable. The redirect address is the page itself unless `redirect=`
names another. A state works once, for ten minutes, twenty logins at once, and only with the
token endpoint and issuer it was started at - a way back handled with another `base=`, `issuer=`
or `tenant=` is refused before the code goes anywhere; OpenID Connect's
id_token is checked for its issuer, audience, expiry and nonce - its signature is not: it comes
straight from the token endpoint over TLS, which OpenID Connect Core 3.1.3.7 lets stand for it,
so an issuer, a `base=` and every endpoint a discovery document names must be https (http on
localhost alone).
`padOAuthUrl` gives the address without going there; `padOAuthToken ()` the token answer, for
calls to the provider's API. `{oauth 'github'/}` is the button - its link `oauth/<provider>`
unless `href=`, the provider's icon when `pad/subs/icons/` has one. Apple posts its answer back
from its own site, which brings no `SameSite=Lax` session cookie, so its state also goes in a
sealed cookie, `__Host-padOAuth` (https, no other subdomain can set it), spent once used like
the session's, and holding the last Apple login started alone; with `$padCsrf` on that post is
refused - it carries no token.

### Notifications on every channel

`padNotify ( $user, 'order-shipped', [ 'order' => 42 ], [ 'mail', 'database', 'slack' ] )` renders
`_notifications/order-shipped.pad` for each channel and sends it - a section per channel, or the
plain text for every channel without one:
```
Your order {$order} has shipped.        # the plain text
@title@
Order {$order} shipped                  # the subject, the push title
@mail@
<p>Hello {$name}, your order <b>{$order}</b> is on its way.</p>
@slack@
:package: Order *{$order}* shipped
```
The channels are the files of `pad/subs/channels/types/`: mail (`padMail`), database (kept under
`DATA/notifications/<app>/` - `{notifications/}` lists the unread ones of the user logged in, each
with a signed link that marks it read; `padNotifications`, `padNotificationRead`), webpush (RFC
8291 encryption and VAPID with openssl - `padWebPushKeys ()` makes the key pair), slack, discord,
teams, telegram, ntfy, matrix, sms (a generic HTTP gateway) and those below. The recipient is a row - `email`,
`phone`, and a field per channel naming the person there: `telegram` (a chat id), `ntfy` (a
topic), `matrix` (a room id), `webpush` (the browser's subscriptions) - or an e-mail address.
Each setting comes from the options (`[ 'slack' => [ 'url' => ... ] ]`), the row, or `padEnv`:
`SLACK_WEBHOOK_URL`, `DISCORD_WEBHOOK_URL`, `TEAMS_WEBHOOK_URL`, `TELEGRAM_BOT_TOKEN`,
`NTFY_TOPIC`, `MATRIX_TOKEN`, `SMS_URL`, `VAPID_PUBLIC_KEY` ...; one missing everywhere is a
strict error naming the variable. An address a request goes to - a webhook, a homeserver, a
gateway - is the options' or `padEnv`'s alone, never the row's, and absolute http(s); the row's
fields are checked, and a push endpoint must be on a browser's push service (FCM, Mozilla's,
WNS, Apple's - `WEBPUSH_SERVICES` names more): a value that is none is not sent to. The Telegram
token and the webhooks' addresses are credentials: the dumps, the error report and
`_events/curl.php` see them redacted, as a header's. With
`[ 'queue' => TRUE ]` (or a queue's name) every channel is a job of its own, `@notify`, rendered
and sent by `pad work` - a failing channel is retried alone; the job keeps of the recipient what
`padLogin` keeps of a row, its password hash, tokens and secrets left out. A channel that does not deliver is
no error: FALSE, logged, and `padNotifyResults ()` says why - and which push subscriptions are
gone. A channel is added by adding its file: its settings and a `send` function.

- pushover - a push to a Pushover user or group (pushover.net): `PUSHOVER_TOKEN`, the key in the
  row's `pushover` or `PUSHOVER_USER`; the title, the text - plain, cut at 1024 characters - and
  the url, the options `priority` (-2 to 1; an emergency, 2, is refused), `sound` and `ttl`.
- gotify - a message to a self-hosted Gotify server: `GOTIFY_URL`, the application's
  `GOTIFY_TOKEN` (as a bearer token, never in the address); the title, the text as plain text -
  no markdown - and the url a click opens, the option `priority` (0 to 10).
- mattermost - a message through a Mattermost incoming webhook, `MATTERMOST_WEBHOOK_URL` (its id
  redacted in the reports, as Slack's): Mattermost's markdown as written, every mention switched
  off - a zero-width space after the @ that starts a word - so a value pings nobody.
- twilio - a text message through Twilio's Messages API: `TWILIO_ACCOUNT_SID`, `TWILIO_AUTH_TOKEN`
  (or with `TWILIO_API_KEY` that key's secret), `TWILIO_FROM` - a number, a short code, a sender id
  or a Messaging Service (`MG...`); the row's `phone` in E.164, the text cut at 1600 characters.
- webhook - the notification as JSON to `WEBHOOK_URL` - Zapier, n8n, a service of your own -
  signed as Standard Webhooks has it with `WEBHOOK_SECRET` (`whsec_` and base64, several split by
  spaces while one is rolled in): the headers `webhook-id`, `webhook-timestamp` and
  `webhook-signature` (`v1,` and the HMAC-SHA256 of id.timestamp.body), the body `type`
  (`notification.<name>`), `timestamp` and `data` - name, title, text, html, url, the
  recipient's id and email, the values. A queued notification keeps its id for its retries.

### A language model answering in the page
```
{ai provider='anthropic', ttl=86400}Summarise in one sentence: {$body}{/ai}
{ai 'Name three rivers of France.', model='claude-haiku-5-5', system='Answer briefly.'/}
```
```php
$summary = padAi ( "Summarise: $body", [ 'provider' => 'openai', 'tokens' => 300 ] );
```
The content renders first and is the prompt; the answer is printed as text - a value, never
template code. The providers are the files of `pad/subs/ai/types/` - anthropic
(`claude-opus-5-5` by default), openai, mistral, gemini and ollama (a local server, no key) - each
its endpoint, headers, request and how the text and an error are taken out of the answer. The
key is `ANTHROPIC_API_KEY`, `OPENAI_API_KEY`, `MISTRAL_API_KEY` or `GEMINI_API_KEY`
(`OLLAMA_HOST` for Ollama), the provider `provider=` or `PAD_AI_PROVIDER`. Options `model=`,
`system=`, `tokens=` (the most the answer takes - the handling option `max` is something else)
and `ttl=` (seconds an answer is kept, 3600 by default, keyed on the provider, model, system
prompt and prompt; 0 keeps nothing). A missing key or an error of the API is an error under the
strict check; without it the tag answers nothing, the error is logged (`padLog`) and
`padAiError ()` says it. Tests answer the providers with `padCurlFake`, and keep nothing.

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

The rules are the files of `pad/subs/rules/types/` - `<rule>.php` answers its check, its message and
whether an empty field or a list is judged, `www/assets/rules/<rule>.js` is its check in the
browser - and the files there are the rules there are: besides `required`, `email`, `url`,
`numeric`, `integer`, `min:n`, `max:n`, `in:a,b`, `regex:/x/`, `same:field`, `accepted` and
`date` they are `between:a,b` (a length, or a value with `numeric`), `digits` and `digits:n`,
`alpha`, `alphanum`, `starts_with:a,b`, `ends_with:a,b`, `lowercase`, `uppercase`, `time`
(HH:MM), `color` (#rgb, #rrggbb), `uuid`, `ip` (`ip:v4`, `ip:v6`), `slug`, `json` and `iban`
(its check digits), `not_in:a,b` (the mirror of `in:`, as written). A rule is added by adding its files; an argument a rule cannot use -
`digits:four`, `between:9,1` - is an error, as a pattern PCRE cannot read is. So is a rule that
judges one text - `min:2`, `email` - on a field that posts a list (`tags[]`, a `{multiselect}`):
a list is judged by `required`, the `required_*` rules, `in:` and `not_in:` alone, and any other
refuses every post of it.

`multiple_of:n` holds a number to whole steps - `multiple_of:6` crates of six, `multiple_of:0.25`
quarters of an hour, `multiple_of:0.01` whole cents - reckoned in the decimal digits as written,
on the server and in the browser alike, never in floating point (0.3 is a multiple of 0.1, a
number of thirty digits is judged to its last); the value is a number as `numeric` reads one,
the sign not counting, 0 a multiple of every n, and like `numeric` it makes the field a number
for `min`, `max` and `between`. Its argument is a positive number written plainly, of at most
14 digits: `multiple_of:0` is an error. "Bottles must be a multiple of 6".

`min_words:n` and `max_words:n` count the words of a text - a review of twenty words or more, an
abstract of 250 at most - a word being a run of characters between white space, which is
Unicode's White_Space written out on both sides (line breaks, tabs, the no-break space, the
ideographic space; not a hyphen, a byte order mark or a zero width space), so `e-mail` is one
word and a text without spaces - Japanese - one: "Summary must be at most 250 words".

Some rules speak about a list, a field named `guests[]`: `distinct` refuses a value it holds
twice - as written, each trimmed, an item left blank no value (three fields of `guests[]`, two
of them empty, pass) - "Guests must not hold the same value twice"; a field of one value holds
its value once. `min_items:n` and `max_items:n` count the values of a list - two or three
toppings ticked, `required|min_items:2|max_items:3` - the items left blank not counted, a field
of one value holding one, an empty list judged by `required` alone (as `min:` leaves an empty
text): "Toppings must hold at least 2 values". Their argument is a whole number from 1.

Some rules look at another field of the post:
```
{input 'delivery'/}
{input 'address', rules='required_if:delivery,ship,courier'/}   # required when delivery is one of them
{input 'note', rules='required_unless:delivery,collect'/}       # required unless it is
{input 'code', rules='required_with:phone,fax'/}                # required when one of them is filled
{input 'phone', rules='required_without:email'/}                # required when one of them is empty
{input 'end', type='date', rules='after:start'/}                # or before:end, after:2026-01-01
{input 'leave', type='date', rules='after_or_equal:arrive'/}    # the same day too - or before_or_equal:
{input 'password', rules='different:current_password'/}         # not the other's text - the mirror of same:
{input 'max_price', rules='numeric|gt:min_price'/}              # above the other - gte:, lt:, lte:; or gt:0
```
The other field is found where PHP files the post, by the browser's checker too (`user[kind]`,
`kinds[]` - which `kinds` finds too - the items `kinds[0]` and `kinds[1]`, `first.name` as
`first_name`), its text trimmed; one the post leaves out - a radio group without a choice, a box
not ticked - is read where the page reads it, from the query string of the address the form
posts to (`?checkout&delivery=collect`), by the checker too, and is empty when that has none
either. `required_if`/`required_unless`
compare as written, case and all, and a list meets the value when one of its items does;
filled is what `required` passes (`0` is filled, blanks are not, a list with an item is). The
four judge an empty field, and require a list as `required` does. `before`/`after` read ISO
8601 alone - the day `2026-11-01` (its first moment) or the day with a time `2026-11-01T14:30`
(`:15` seconds and `:15.25` a fraction of one too, as a field with a step under a second posts
it), what date and datetime-local fields post - no other spelling, zone or
word, alike on the server and in the browser; strictly before or after, so the same moment
fails. A value that is no such moment fails; an other field that is empty, no moment or a
list lets the rule pass - its own rules speak. An argument that reads as a date - four
digits, a dash, a digit - is a date written in the rule, whatever field the post has of that
name, and one the calendar lacks (`after:2026-02-30`) is the author's error. The messages:
"Address is required when Delivery is ship", "... unless Delivery is collect", "... when
Phone or Fax is filled in", "... when Email is empty", "End must be a date after Start".
`after_or_equal:`/`before_or_equal:` read the moments as `after:`/`before:` do and take the
same moment too - a stay that leaves on the day it arrives - a day being its first moment, so
`2026-01-01T00:00` is on or after `2026-01-01` and `before_or_equal:2026-12-31` refuses
`2026-12-31T10:00`: "Leave must be a date on or after Arrive".
`gt:`, `gte:`, `lt:` and `lte:` compare with another field or a number written in the rule
(`gt:0` - where `min:0` takes 0 - `lte:100`), measuring as `min` and `max` measure: on a number
field (`numeric`, `integer`, `multiple_of`) the value, on any other the length in characters,
the other field measured alike; an other field that is empty, a list or on a number field no
number lets them pass. "Max price must be greater than Min price", "Code must be shorter than
5 characters", "Nick must be at most as long as Name".
`different:` finds and trims the other field as `same:` does and passes where `same:` fails -
an other field that is left out, empty or a list holds no text the value could be:
"Password must be different from Current password".

`client` on the form checks the same rules in the browser too, with the same messages, before
the form leaves (the runtime `www/assets/rules/validate.js` with every rule's script, written
once a page). A field whose rules look at another - `same:`, `different:`, the `required_*` rules, `before:`,
`after:` and their `_or_equal`, `gt:` and its kin with a field - is checked again when that one changes, if it shows a message or was
checked or left before: "is required when Delivery is ship" goes when delivery becomes
collect, and comes on an address already left when it becomes ship. The rules name those
fields themselves (`fields` in their files); `padValidateClient` hands them on as the field's
`watch`. A message the server wrote beside a field not touched since is one such rule's and
follows the other field too, or it speaks about the value the field still holds - one the
page's PHP added, one of a regex only PCRE reads - and stays, unless the checker has a message
of its own for the field. For a form a component draws,
keep the rules in PHP - `padValidate ( $rules )` checks the post, `padValidateClient ( $rules )`
exports them for `padValidate.check ( rules, values )` in the browser, `{validator}` brings the
checker:
```
{form 'signup', client, error}{input 'email', label='E-mail', rules='required|email'/}{/form}
<div x-data="{ rules: {^rules}, form: {}, errors: {} }">...</div>{validator/}
```

### Rich form fields
```
{form 'trip', error}
  {slider 'volume', label='Volume', unit='%', rules='integer|between:10,90'/}   # or {input:slider ...}
  {stars 'score', label='Your rating', rules='required'/}                    # max=5
  {datepicker 'day', label='Day', min='2026-01-01'/}       # a month to choose from; weekstart=
  {timepicker 'at', label='Time', every=15/}               # the times of a list
  {taginput 'topics', label='Topics', suggestions='pad, php'/}   # posts 'pad, php'
  {combobox 'city', label='City'}Rome
    Oslo{/combobox}                                        # options=, data=, or a line each
  {multiselect 'tags', label='Tags', options='pad, php, maps', rules='required|in:pad,php,maps'/}   # posts the list tags[]
  {signature 'signed', label='Signature'/}                 # posts a PNG data URL
  {dropzone 'cv', label='Your CV', accept='.pdf', limit='2M'/}   # padUpload takes it
{/form}
```
The kinds are the files of `pad/subs/inputs/types/` - colorpicker, combobox, datepicker, dropzone,
mdeditor (Markdown with a live preview), multiselect (chips and a filtered list over a select of
many), newpassword (strength meter, show/hide; `known` for a
login), otp (a box per character), signature, slider, stars, stepper, taginput, timepicker and
toggle (a switch posting `on=`/`off=`) - their script and style in `www/assets/inputs/`. Each
is a field of its `{form}` as `{input}` is: named by its first parameter, labelled by `label=`,
refilled from a post of its own form, the message of `padValidate` or of its `rules=` (read
from the template before the PHP runs, `client` too) after its frame in the words of its label,
every item it does not take an attribute of its field. Every kind writes a native field a form
posts without any script - a range, a date, a radio group, a text field holding a comma list -
and its script, once a page with the CSP nonce, makes more of it; a field a fragment brings later
is enhanced too. The bare name comes after every other kind of tag: on a page with an array
holding a key `slider`, write `input:slider`.

A multiselect holds a list: `{multiselect 'tags'}` posts `tags[]` - the page's PHP gets
`$_POST ['tags']` as an array - and that is the name its rules, `padValidate` and the
browser's checker know it by (`required` asks for a choice, `in:` holds every one to the list;
its messages say Tags). Its choices are `options=`, `data=` with `field=`, or a line each between
its tags, as a combobox's - a source that holds none when the page runs, a table with no rows
yet, writes either field without choices, while a tag given no source at all is the template's
error under the strict check; `value=` - a list or a comma text - the choices made before a post.
After a post of its form it shows the list posted: nothing chosen when the post left the field
out, as the browser posts a select with no choice - never `value=` again - and a posted value
that is no choice is not shown, nor added as an option. A hidden `padLists[]` beside the select,
naming the list, tells that post from any other request - a click in a `{live}` region, a post
of another form of the page, a link with a value to the page of a GET form - which shows
`value=`, as every other field does; a disabled multiselect, which the browser leaves out of
the post, keeps `value=` after a post of its form too.

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

### Site search
```
{search 'blog', q=$q, fields='title^2, body', lang='en', first=10}   # _content/blog/, ranked by BM25
  <a href="?blog/post&slug={$slug}">{$title}</a> {$score} {!excerpt}
@else@
  Nothing found for {$q}.
{/search}
{search 'products', q=$q, fields='name^3, about', link='url'/}   # a page's array or db() result; a list
```
```php
$found = padSearch ( 'blog', $q, [ 'fields' => 'title^2, body' ] );
$found = padSearch ( db ( "ARRAY id, name, about FROM products" ), $q, [ 'name' => 'products', 'lang' => 'nl' ] );
```
Site search without a search server. The source is a Markdown collection (`_content/<name>/`,
read by `padCollection`), else a `{data}` store, a page's array - a `db()` result - or a
`_data` file of the name; `padSearch` takes the rows themselves too. The first search makes an
inverted index of it, kept in `DATA/cache/search/<app>/` in parts - a search reads those of
its words, so a large source costs a request little memory - and made again when what it is
made of changed: the names, times and sizes of a collection's files (and what a file holds,
when it changed in the second before), a hash of the fields searched of any other rows - a
column the search does not read changes nothing - and the code of the search, its stemmers
and stop words. The
query: words (a row with any of them is found), `"a phrase"` (required, its words in order -
`"word"` makes one word required), `prefix*` (any word beginning so, with or without its
accents - `univ*` finds `l'université`), `-word` and `-"a phrase"` (left out). Words are
Unicode letters and digits in lower case, stop words dropped, stemmed by the language's
Snowball stemmer and written without accents - a word with accents stemmed without them as
well - so `baking` finds `baked`, `cafe` finds `Café` and `securite` finds `sécurité`; the
ranking is BM25F - `fields='title^2, body'` weighs a field, k1 1.2, b 0.75. A row found is
the source's row with `score` (three decimals) and `excerpt` added: the words around the first
match, every matched word in a `<mark>` (`padExcerpt`, the function of `{excerpt}`;
`words=` its length, `excerpt=` its field) - HTML, so print it `{!excerpt}`. A collection is
searched in `title^2, body` by default, other rows in their text fields; a field that is HTML -
an end tag, a comment, a `<br>` or `<img>`, a quoted attribute - is read as its text, a `<` in
plain text stays, and an entity is the character it stands for. The languages are the files of `pad/subs/search/types/` - `en` (Porter2), `nl`
(Kraaij-Pohlmann), `de`, `fr`, `es`, each with Snowball's stop words and its stemmer written
after the Snowball 3 source and matching Snowball's own test vocabularies word for word -
`lang=` picks one, the locale's language else English; a language is added by adding its file.
A name that is a collection is the collection: name a page's array otherwise. No exec, no
shell: it runs in pad pack's WebAssembly PHP as well. Code: the search subsystem in
`pad/subs/search/` - `search.php` the body of `{search}`, `_lib/search.php` the index, the
query and the ranking, `_lib/snowball.php` the stemmers' runtime, `types/` the languages;
`pad/lib/search.php` the helper.

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
{chart 'dumbbell', data='scores', label='team', from='before', to='after', ranked/}  # two dots a row joined; names='2025, 2026'
{chart 'errorbar', data='trial', label='dose', value='mean', low='lo', high='hi', interval='95% CI'/}  # computed intervals, drawn as given
{chart 'violin', data='scores', label='class', value='score'/}       # also density, parallel (value= the axes), spiral (period=)
{chart 'chord', data='migration', source='from', target='to', value='people'/}  # also arc
{chart 'orgchart', data='staff', id='id', parent='boss', label='name', sub='role'/}  # pack: the treemap's options
{chart 'bar', label='month', value='amount'}                 # a pair: the content is the data -
  month,amount                                               #   JSON, YAML, XML or CSV, told
  Jan,12400                                                  #   apart on sight (type= names it)
{/chart}
{chart 'bar', data='sales', label='month', value='amount', client/}   # drawn in the browser
```
With `client` the browser draws the chart: the server gathers the rows - the handling options
applied - and the tag's options, writes them into the page as JSON in a span, and a script
draws the SVG in its place (`PadChart.mount`; `PadChart.mountAll ( element )` for a part of a
page that came in later). The scripts are `www/assets/charts/` - `core.js`, written once a page,
and a file per family, the PHP of `_lib/` written again in JavaScript a function for a function,
so the chart is the same SVG, character for character (ids aside), as the server draws:
`apps/regression/pages/charts/_client.mjs` holds every example of the charts application to
that, the `./ci.sh` line `charts`. The server marks the chart's options read, so the strict
check reports no misspelt option of a client chart; what the kind reports as a mistake the
browser writes in the chart's place. calendar and gantt read the date forms their comments
name in the browser - ISO dates and times, timestamps, YYYYMMDD, week and ordinal dates - not
every phrase `strtotime` reads.
`role="img"` with `<title>`/`<desc>`; colours are `--pad-chart-*` custom properties on
`.pad-chart`, following the page's `color-scheme`. A field option left out takes the first
field that fits; the chart's options keep clear of the handling options (`row`, `group`).
The kinds are the files of `pad/subs/charts/types/` - one file a kind, its default size and the
function that draws it; a kind is added by adding its file. Each is written three ways, the
same chart: `{chart 'pie', data='budget'/}`, the chart type `{chart:pie data='budget'/}`, and
bare, `{pie data='budget'/}`, when no tag, store, field or sequence has the name (the bare
name comes last, after every other kind of tag). `{sparkline .../}` is the sparkline kind.
`calendar` is the month calendar tag and `pack` PHP's function, so those two kinds are
`{chart:calendar .../}` and `{chart:pack .../}` (or `{chart 'pack', .../}`).
Code: the chart subsystem in `pad/subs/charts/` - `chart.php` the body every form runs,
`_lib/` the drawing (`chart.php` bar, line and sparkline, the files beside it the other
kinds, `client.php` the hand-over to the browser), `types/` the kinds, `www/assets/charts/`
the drawing in the browser; `pad/types/chart.php` the chart type.

### Games
```
{game 'memory', pairs=6/}                      # 18 little games, played in the browser
{game:tictactoe opens='computer'/}             # the game: type - or bare, {tictactoe/}
{sudoku givens=26, seed=7/}                    # a new puzzle with one solution; seed= the same one
{sudoku '53..7....6..195....98...', paper/}    # print: drawn to print, solve: solved, no script
{hangman data='words'/}                        # the words of a store, else between the tags
{crossword}                                    # a line per word, WORD: clue - laid out on the
  ECHO: Writes a value without the sanitize    #   server, typed into in the page
  PIPE: The | that hands a value to a function
{/crossword}
{quiz}
  Which tag repeats its content? | while | if | set     # the right answer first
{/quiz}
```
The games are the files of `pad/subs/games/types/` - `<game>.php` checks the options and writes the
board; `www/assets/games/<game>.js` plays it, `<game>.css` there draws it - and the files there
are the games there are: bullscows, crossword, fifteen, gomoku (five in a row), hangman, hanoi, maze, memory, nim,
nonogram, pegsolitaire, quiz, reversi, scramble, snake, sudoku, tictactoe, wordsearch. The
script of every game and a small runtime (`www/assets/games/game.js`: a seeded random number,
the status line, the buttons) are written once per page with the CSP nonce; a game a fragment
or a live region brings later starts too. A finished game carries `data-result="won"` or
`"lost"` and sends `pad-game-end`. The `games` application plays every one of them, the
reference lists them with the pages that play them.

### Diagrams
```
{diagram direction='LR'}                       # a flowchart, the default kind: a line a chain
  A([Order]) --> B{Paid?}                      #   of nodes - [box] (round) {decision} ((circle))
  B -->|yes| C[(Orders)]                       #   [(database)] {{hexagon}} - and edges
  B -.->|no| D[Remind] --> B                   #   --> --- -.-> ==> with -->|labels|
{/diagram}
{diagram:sequence}Shop ->> Bank: Charge{/diagram:sequence}   # lifelines, notes, loop/alt frames
{diagram:state}[*] --> Idle{/diagram:state}    # state machine: [*] start/end, <<choice>>, <<fork>>
{diagram:class}Animal <|-- Duck{/diagram:class}   # UML: class A { +field; +m()* }, *-- o-- ..> "1" "*"
{diagram:er}CUSTOMER ||--o{ ORDER : places{/diagram:er}   # crow's feet, entities as attribute tables
{mindmap}...{/mindmap}                         # from the indentation, branches on both sides
{diagram:tree}...{/diagram:tree}               # a hierarchy top down (direction='LR' sideways)
{journey}...{/journey}                         # section, Make tea: 5: Me - a face per score
{gitgraph}...{/gitgraph}                       # commit, branch, checkout, merge, cherry-pick
{fishbone}...{/fishbone}                       # the effect, then categories and causes indented under it
```
The kinds are the files of `pad/subs/diagrams/types/` - one file a kind, reading the options it
draws with - and a kind is added by adding its file. Each is written three ways, the same
picture: `{diagram type='state'}`, the diagram type `{diagram:state}` and bare, `{state}`, when
no tag, store, field or sequence has the name (the bare name comes last); `sequence` and `tree`
are tags too, so those two are `{diagram:sequence}` and `{diagram:tree}`. A first line written
the Mermaid way - `sequenceDiagram`, `stateDiagram-v2`, `classDiagram`, `erDiagram`, `gitGraph`,
or `mindmap`, `journey` and `ishikawa-beta` (the fishbone) in a `{diagram}` without `type=` - names the kind, so Mermaid text
pastes in as it is. The content is read as it stands, braces and all; the layout is
deterministic (the same lines, the same picture); `role="img"` with a `<title>` and a `<desc>`
that says every edge, relation or step in words; the colours are `--pad-diagram-*` custom
properties on `.pad-chart-diagram` with `light-dark()` defaults. Code: the diagram subsystem
in `pad/subs/diagrams/` - `diagram.php` the body every form runs, `_lib/diagram.php` the shared
parts, the flowchart and the layered layout the state, class and entity-relationship diagrams
share (`padDiagramGraph`), the files beside it the other kinds, `types/` the kinds;
`pad/types/diagram.php` the diagram type. The `showcase` application has an example of each.
### Maps
```
{map 'world', data='sales', key='country', value='amount'/}   # choropleth as inline SVG, no script, no tiles
{map 'eu', data='visitors', scale='quantile'/}                 # the 27 member states, the rest of Europe around them
{map 'benelux'}{"NL": 1200, "BE": 800, "LU": 90}{/map}         # a pair: the content is the rows
```
The views are the files of `pad/subs/maps/types/` - each answers its box of longitude and latitude
and, for a region that is a list of countries, those countries and the name its title takes:
world, the six continents (europe, africa, asia, north-america, south-america, oceania) and
middle-east, mediterranean, eu, nordic, benelux, baltic, british-isles, iberia, dach, balkans,
central-america, caribbean, east-asia, southeast-asia, south-asia, central-asia. A view of a
list draws the countries around it quieter, without values (`--pad-map-context`); a value of
a country the view does not show is named in the title, and kept off the scale. A view is
added by adding its file - no new outlines needed, every view is a crop of the same Natural
Earth outlines (`pad/subs/maps/_lib/world.json`).

### Spreadsheets
```
{spreadsheet 'shop', formats='B:€ #,##0.00, D:€ #,##0.00', totals='D'}
  Item,Price,Qty,Total
  Apples,1.20,3,=B2*C2
  Pears,0.80,5,=B3*C3
{/spreadsheet}
{spreadsheet data='orders', formulas='total=B2*C2', formats='total:0.00'/}   # a column filled down the rows
{live 'calc'}{spreadsheet 'calc', editable='B1:B3'}...{/spreadsheet}{/live}  # edited, computed again on the server
```
A grid of cells as an accessible `<table>` - the column letters and row numbers around it
(`headers=FALSE` leaves them out), numbers right-aligned, errors marked and explained in their
title, each formula in its cell's `title` and `data-formula`. The cells are the pair's content -
a line a row, a comma between cells (a tab when a line holds one; a formula runs to the comma
outside its brackets and texts, `=SUM(B2,C2)` needs no quotes) - or the rows of `data=`: records
under a first row of their field names, whose values are never formulas, a list of rows of
values, or cells by name. `formulas=` puts formulas in - `total=B2*C2` fills a column down the
rows, its relative references moving as a spreadsheet's fill moves them, `D2:D9=...` a range,
`F1=...` a cell - `totals=` adds a row of SUMs, `formats=` gives a column, a cell or a range a
format code (`B:0.00, D:€ #,##0.00, E:0%, B5:B8:yyyy-mm-dd`). A cell typed as `12%`,
`2024-01-15` or `$4.50` keeps that look, and a formula takes its first cell's.

The formulas are spreadsheet syntax, read by a parser of their own (`pad/subs/formulas/_lib/
formula.php` - not padEval): `=A1+B2*2`, ranges `A1:B3`, whole columns `A:A` and rows `2:5`,
`$A$1`, texts `"x"` with `""` for a quote, `&`, `= <> < > <= >=`, `%` after a value, `^` - read
from the left, `=2^3^2` is 64, and a minus binds tighter, `=-2^2` is 4 - TRUE and FALSE, and the
error values `#DIV/0! #N/A #NAME? #NULL! #NUM! #REF! #VALUE!`, which travel as Excel's do; a text
holds 32767 characters at most, as a cell of Excel does - a longer one is `#VALUE!`, never built. The
functions are the files of `pad/subs/formulas/types/` - a function is added by adding its file,
names are read in any case - and answer what Excel answers: SUM, AVERAGE, MIN, MAX, COUNT,
COUNTA, COUNTBLANK, COUNTIF(S), SUMIF(S), AVERAGEIF(S), MINIFS, MAXIFS, PRODUCT, SUMPRODUCT
(ranges of one shape multiplied place by place, what is no number 0), MEDIAN, STDEV, VAR (a
sample's, also STDEV.S and VAR.S), STDEV.P and VAR.P (a population's, also STDEVP and VARP), RANK,
LARGE, SMALL, PERCENTILE.INC, PERCENTILE.EXC, QUARTILE.INC and QUARTILE.EXC (interpolated
between the numbers around the place, the older PERCENTILE and QUARTILE the .INC forms); IF,
IFS, AND, OR, NOT, XOR, IFERROR, IFNA; ISBLANK (a reference to an empty cell,
never the text ""), ISNUMBER, ISTEXT, ISNONTEXT, ISLOGICAL, ISERROR, ISERR, ISNA, ISEVEN, ISODD
- an IS function takes the error it tests, never passes it on; ROUND (half away from zero, on 15
digits), ROUNDUP, ROUNDDOWN, TRUNC, CEILING and FLOOR (Excel's signs - CEILING(2.5,0.1) is 2.5,
on 15 digits too), MROUND, ABS, INT, MOD (the divisor's sign), POWER, SQRT, EXP, LN, LOG, PI, RAND
and RANDBETWEEN (seeded, `seed=`), SIGN, QUOTIENT (cut toward 0); CONCAT, TEXTJOIN, LEFT,
RIGHT, MID, LEN, UPPER, LOWER, PROPER, TRIM,
SUBSTITUTE, FIND, SEARCH, TEXT (the common format codes), VALUE, REPT, REPLACE, EXACT (in the
same case); VLOOKUP, HLOOKUP, XLOOKUP,
INDEX, MATCH, CHOOSE; DATE, TODAY, NOW, YEAR, MONTH, DAY, WEEKDAY, EOMONTH, EDATE, DAYS (the
serials of the 1900 system, 1899-12-30 based, with its 29 February 1900), TIME (shown hh:mm),
HOUR, MINUTE, SECOND (the time rounded to the second, as it shows), DATEVALUE and TIMEVALUE
(of a text only), NETWORKDAYS and
WORKDAY (holidays a range of dates, those in the weekend left out once) and their .INTL forms
(a weekend of a number, 1 to 7 and 11 to 17, or seven 0s and 1s from Monday); PMT, FV, PV, NPER,
RATE, NPV, IRR (Excel's signs, RATE and IRR by Newton from 10%, `#NUM!` when 150 steps do not
settle), IPMT and PPMT (the interest and the principal of a period's payment, which add up to
PMT). The cells are computed in the order they need one another; a circle of references
answers `#REF!` in each of its cells, the title naming the circle (`A1 → B1 → A1`). There is no
array evaluation: an operator takes the one cell of a range in the formula's row or column, as
Excel's implicit intersection does, so `=SUMPRODUCT((A2:A9="x")*B2:B9)` - an operator on every
cell of a range - is an error of the formula under the strict check and `#VALUE!` otherwise,
never the number of one cell; SUMIFS and COUNTIFS add up and count with criteria.

`editable` makes cells `<input>`s - alone every number, date and logical value, or the cells and
ranges named - and an edit posts back and is computed on the server, no script of the page's
own: in a `{form}` the cells post with it, in a `{live}` region the sheet is a form a change
submits - every cell's text once - and the region comes back computed, anywhere else the sheet
is a form of its own with a Recalculate button. A post types only into the cells named.
Under the strict check a template formula that cannot be read - or calls a function that is not
there, or with the wrong number of arguments - is an error naming the cell and the position.
`padFormula ( '=SUM(1,2)' )` and `padSheet ( $cells )` compute formulas for a page's PHP
([HELPERS.md](reference/HELPERS.md#spreadsheet-formulas)); the `sheets` application has a
budget, a loan, grades, dates, an editable sheet and a page of every function.

### Embeds
```
{embed 'https://www.youtube.com/watch?v=aqz-KE-bpKQ', title='Big Buck Bunny'/}   # the provider from the address
{embed:vimeo '76979871', title='The new Vimeo player', at='1:30'/}          # the embed: type, a bare id
{openstreetmap '52.3731,4.8922', zoom=15, title='Dam Square'/}              # bare, when nothing else has the name
{embed 'https://soundcloud.com/forss/flickermood', title='Flickermood'/}     # also spotify, peertube, dailymotion, internetarchive, codepen
{audio 'sounds/bell.mp3', title='The bell'/}                                # a file: <audio preload="none">; videofile, pdffile
```
A frame of another site is a click-to-load facade: a poster drawn for its kind (or `poster=`,
or the provider's own picture with `thumbnail`), the title, "Plays from YouTube", a button -
nothing is asked of the provider before the click, which swaps the frame in by a script written
once per page with the CSP nonce; without script a `<noscript>` link to the address on its
own site. The providers are the files of `pad/subs/embeds/types/` - each says the addresses it
knows (`match`), its frame (`embed`), its origin for `frame-src` and its own options - and a
provider is added by adding its file. `padEmbedSources ()` lists the origins a Content
Security Policy needs: `$padCsp = "...; frame-src " . implode ( ' ', padEmbedSources () )`.
`{video}` is the video-only form of the same providers, its markup as before.
### Toys
```
{toy 'life', pattern='gun', wrap/}             # small live simulations and visual toys, in the browser
{toy:mandelbrot center='-0.745, 0.11', zoom=40/}   # the toy: type - or bare, {mandelbrot/}
{sorting algorithm='heap', bars=100/}          # race: every algorithm side by side on the same bars
{boids birds=300, cohesion=8, seed=7/}         # seed= the same start on every load
{pendulum pendulums=3, paused/}                # paused: waits for Play, as under reduced motion
{life}                                         # a pattern of your own, a row a line: O alive, . dead
  .O.
  ..O
  OOO
{/life}
{lsystem}                                      # an L-system of your own: the word, the turn, a rule a line
  axiom: X
  X: F[+X]F[-X]+X
  F: FF
  angle: 20
{/lsystem}
```
The toys are the files of `pad/subs/toys/types/` - `<toy>.php` checks the options and hands them on;
`www/assets/toys/<toy>.js` runs it, `<toy>.css` there styles it when it needs to - and the files
there are the toys there are (`padToyTypes()`): the Game of Life, Langton's ant, an elementary
cellular automaton, the Mandelbrot set, sorting algorithms, a double pendulum, boids, fireworks,
falling sand, a harmonograph, a spirograph, three clocks, a starfield, a flow field, L-systems, a
ripple tank, orbits, a Galton board and a kaleidoscope. Every toy can be touched - draw, drag,
click - and has labelled controls, a status line and a canvas with `role="img"` and a label. The
runtime (`www/assets/toys/toy.js`: a seeded random number, the palette resolved for the canvas, the
pointer, the controls, the animation loop) and every toy's script are written once per page with
the CSP nonce; a toy holds while the page is hidden or it is scrolled away, and waits for Play under
`prefers-reduced-motion` - a drawing toy shows its figure finished. The root carries
`data-state="playing"`, `"paused"` or `"held"`; the colours are `--pad-toy-*` on `.pad-toy`, light and
dark. The `toys` application shows every one of them, the reference lists them with the pages that
show them.

### Sounds
```
{sound:chime/}                                  # a button that plays the chime - named Chime, its recipe's title
{sound 'pop', button='Pop!', volume=60/}        # the tag; button= the label, volume= 0 to 100
{sound:success on='click', target='#save'/}     # no button: played when an element the selector finds is clicked
{sound:tick on='hover', target='nav a'/}        # on= click, pointerdown, hover, focus - change, input, submit with target=
<button data-pad-sound="coin">Coin</button>     # any element, once a sound has put the script on the page
{sound:bell download/}                          # with a link to the sound as a WAV file
{toast 'Saved', tone='success', sound='success'/}   # a toast that sounds as it shows
```
A sound is a recipe, a file of `pad/subs/sounds/types/` that returns a PHP array - no audio file
anywhere: voices played together, each an oscillator (sine, square, saw, triangle) or noise, with
an envelope (attack, decay to a sustain level, release), a pitch that sweeps (`to=`), wobbles
(`vibrato`) or steps through a short run of notes (`notes='C5 E5 G5'`, `step=`), and a filter -
lowpass, highpass or bandpass - whose cutoff may sweep too. The sounds: bell, chime, click, coin,
error, hihat, jump, kick, laser, notify, pop, powerup, snare, success, tick, warning, whoosh; a
sound is added by adding its file. The server settles the recipe - every note to its Hz, every
default filled in - and both renderings read that one definition with the same arithmetic: the
browser's script (`www/assets/sounds/sound.js`, written once per page with the CSP nonce,
every recipe with it) renders the samples and plays them through WebAudio, and `padSoundWav (
'chime' )` renders them on the server as a 16-bit mono WAV at 22050 Hz - the same samples, the
noise from a seeded generator, so the same bytes every time. The page asked again with
`&padSound=chime` answers that WAV - the download link asks for it, and so does the link that
stands in for the button without script; `padSoundSend ( 'chime' )` answers it from a page's PHP.
A browser plays audio only after a gesture on the page: the AudioContext is made or resumed on the
first pointer press or key, so a sound bound to hover or focus, or a toast's sound on a page just
loaded, is silent before that. A sound is never reached bare - click, error and pop are the words
of fields and pages. The `sounds` application is a soundboard of them all, each with its recipe
and its WAV.

### Printables
```
{printable:labels data='products', sheet='a4-3x8'}      # the content is one label, repeated over the data
  <b>{$name}</b> {barcode:ean13 $ean/}
{/printable:labels}
{printable:labels data='people', sheet='a4-2x7', skip=3, copies=2, outline}...{/printable:labels}
{printable:labels across=2, down=5, width=90, height=50, margin='20 15', pitch='95 52'}...{/printable:labels}
{printable:badges data='guests', title='PAD Conference', color='#c0392b'}<h2>{$name}</h2>{$company}{/printable:badges}
{printable:tickets data='seats', number=1}<h2>{$show}</h2><aside>{qr $code/}</aside>{/printable:tickets}
{printable:letter data='customers', sender='PAD, Main Street 1, 3511 AB Utrecht'}
  <header>...</header><address>{$name}<br>{$street}</address><aside>1 November 2026</aside>
  <p>Dear {$name},</p> ... <footer>...</footer>
{/printable:letter}
{printable 'envelope', paper='c5', sender='PAD, Utrecht', scale=0.5, pdf}...{/printable}
{printable:graphpaper grid='dots', major=2, copies=10}Name ________{/printable:graphpaper}   # squares, dots or lines, drawn exactly
{printable:calendar year=2027, holidays='nl', weeks, data='photos'}<h2>{$caption}</h2>{/printable:calendar}   # a month a sheet
{printable:foldcard data='guests'}<header>...</header><aside>...</aside>Dear {$name}<footer>...</footer>{/printable:foldcard}
{printable:staffpaper grand, rastral=2}<b>Sonatina</b>{/printable:staffpaper}   # music paper, empty staves
```
Pages made for paper. A kind is a file of `pad/subs/printables/types/` - labels (sheet grids named
by their paper and their grid: a4-3x8, a4-3x7, a4-2x7, a4-2x8, a4-2x4, a4-4x10, a4-5x13,
letter-3x10, letter-2x10, letter-2x5, letter-2x3, letter-4x20 - or a sheet of one's own with
`across`, `down`, `width`, `height`, `margin` and `pitch` in mm), badges (a band of colour with the
event), cards (business cards, 85 by 55 mm, with crop marks), tickets (a stub, its `<aside>`, behind
a perforation), certificate (A4 on its side, framed, signature lines), envelope (DL, C5, C6 or C4,
the sender and the address), letter (the address in the window of a DL envelope, as DIN 5008
form B places it, the fold marks), invoice (the letter's page, a title and a table set as invoice
lines), indexcards (ruled, as many to the sheet as fit) and placecards (the upper half turned, to
fold into a tent) - and a kind is added by adding its file. The content between the tags is one
item, rendered per row of the data with the handling options of any tag, `copies=` times; a kind
takes the parts of an item from the elements that say what they are - `<aside>`, `<address>`,
`<header>`, `<footer>` - and lays the items out on its sheets as boxes, rectangles and lines in
millimetres. One plan, three renderings: on the screen the sheets lie on a grey desk at their size
(`scale=` makes the preview smaller); printed, a page with printables prints the sheets alone,
each on a page of its own size and no margin (`@page` named per printable, mm-based CSS, the
paper's colours kept); and in a `{pdf}` - or with `pdf` on the tag, whose links ask the page with
`&padPdf`, left out inside a `{page}` as `{pdf}` leaves them and inside a `{pdf}`, whose document
holds the sheets - the PDF writer sets the same sheets as pages of their size, each box where the
CSS puts it, the barcodes and QR codes of the codes subsystem drawn. What the PDF does not do: CSS
of a page's own, pictures other than JPEG, an SVG with curves, a table's own look (its grid is the
writer's), the fonts of the page (Helvetica, Times and Courier stand in). Every item is one region
of a sheet: what does not fit is cut at its edge - a letter is one page. Positions are classes of
a style written with the CSP nonce, no inline style. Never reached bare: labels, cards and letter
are the names of fields and pages. The `printables` application shows every kind, its template
beside what it prints, with the PDF.

Graph paper (`graphpaper`) is a grid over the whole sheet in as many whole squares as fit inside
`margin=` (10 mm), centred - `grid=` squares, dots or lines, `spacing=` the step in mm (5; 8 for
lines), every `major=` line heavier or dot bigger (millimetre paper is `spacing=1, major=10`),
`color=`, `paper=` and `landscape` - the content between the tags a line over it, a name and a date
to fill in. Its lines, and any line of dots, are drawn exactly: one SVG over the sheet in
millimetres, where a line drawn as the border of a box is printed on the nearest of 96 pixels an
inch and a pixel thick at the least - squares of 5 mm 4.76 and 5.03 mm wide, the heavy lines as
thin as the others - so that printed at its actual size 5 mm is 5 mm, on paper as in the PDF.

A wall calendar (`calendar`) is a month to a sheet: the month over a grid of its weeks, a box to
write in for every day - `year=` its twelve months, `month='2026-09'` from that month on and
`months=` how many (1 with `month=`), `sunday` the weeks from Sunday, `weeks` the ISO 8601 week
numbers before them, `holidays=` a country of the holidays subsystem (`'nl'`, `'de-by'`) its public
holidays red with their names and its observances grey, `color=`, `paper=` and `landscape` - the
month, the weekdays and the names of the holidays in the language of the page's locale, as
`{calendar}` writes them; the content between the tags stands over every month, a row of the data
a month, the rows taken in turn.

A folded card (`foldcard`) - a greeting card, an invitation - is printed on one side of a sheet and
folded twice, a quarter fold: the top half folded back, then the sheet in two from left to right,
A4 making a card of A6. Its `<header>` is the front and its `<footer>` the back, on the lower half
of the sheet; its `<aside>` the inside left and the rest of the item the inside right, upside down
on the upper half so that folded they read the right way up. `paper=` a4, a3 or letter,
`landscape` for a card on its side, `padding=` (8 mm) keeps the text off the folds; the folds and
the names of the panels are on the screen alone, `folds` prints the folds as light lines.

Staff paper (`staffpaper`) is music paper: empty staves over the width of the sheet, no clef and
no bar, as many as fit with room to write between them - twelve on A4 - spread from the top
margin to the bottom one. `rastral=` the size of a staff as engravers number them (Gould, *Behind
Bars*): 0 9.2 mm, 1 7.9, 2 7.4, 3 7.0 (the default), 4 6.5, 5 6.0, 6 5.5, 7 4.8, 8 3.7; `lines=`
5 (6 for guitar tablature, 1 for percussion); `staves=` how many; `grand` pairs them for the piano,
joined at their start; `margin=`, `color=`, `paper=` and `landscape`. Its lines are drawn exactly,
as graph paper's are; the content between the tags is a line over the staves.

### Widgets
```
{tabs active='Specs'}{tab 'Overview'}...{/tab}{tab 'Specs'}...{/tab}{/tabs}   # CSS-only tabs: radios, arrow keys
{accordion single, open=1}{tab 'Question'}Answer{/tab}...{/accordion}   # <details> items, one open
{modal 'terms', title='Terms', button='Read the terms'}...{/modal}   # <dialog>, works with and without JS
{carousel label='Photos'}{tab 'Caption'}<img ...>{/tab}...{/carousel}   # scroll-snap slides, link arrows and dots
{copy 'npm install pad', label='Copy'/}             # the text in <pre>, a copy-to-clipboard button
{poll 'lang', options='PHP, Go', title='Favourite?'/}   # one vote per session, results as bars; DATA/poll/
{toast 'Saved', tone='success'}Kept.{/toast}   # a notice: an open <dialog>, its button closes it without JS
{dropdown 'Account'}<a href="?profile">Profile</a>...{/dropdown}   # a popover menu under its button
{tooltip 'Billed every month'}Starter{/tooltip}  VAT {tooltip 'Value added tax'/}   # a tip on hover and focus
{wizard}{tab 'Address'}...{/tab}{tab 'Payment'}...{/tab}{/wizard}   # steps one at a time, Back and Next
{lightbox label='Harbour'}{tab 'Dawn'}<img ...>{/tab}...{/lightbox}   # thumbnails, a picture large on :target
{drawer 'filters', title='Filters', side='left'}...{/drawer}   # a side panel - a <dialog>, with and without JS
{breadcrumb/}  {breadcrumb}Home | ?index{/breadcrumb}   # the trail to the page: its path, its lines or data=
{widget 'modal', 'terms', title='Terms'}...{/widget}   # the widget tag: its first parameter names the widget
{widget:modal 'terms', title='Terms'}...{/widget:modal} # the widget: type - or bare, as above
```
The widgets are the files of `pad/subs/widgets/types/` - each the tag code of one widget, its
functions in `pad/subs/widgets/_lib/<widget>.php`, its style and script in
`www/assets/widgets/<widget>.css` and `.js` - and the files there are the widgets there are.
Each is written three ways, the same widget: `{widget 'modal', 'terms'}` (the tag's second
parameter is the widget's first), the widget type `{widget:modal 'terms'}`, and bare,
`{modal 'terms'}`. The bare name comes after every other kind of tag, so a store, a field or
an array of the name wins - a page's `$tabs` array is the data of `{tabs}`. `{copy}` is a
built-in tag as well, which runs the copy widget, since PHP's `copy()` would take the bare
name first; `{tab}` - the item of `{tabs}`, `{accordion}`, `{carousel}`, `{wizard}` and
`{lightbox}` - is a built-in tag. A widget works without JavaScript; its script, where it has one, is written once per page
with the CSP nonce, and its colours are `--pad-<widget>-*` custom properties with light and
dark defaults. The `showcase` application has examples of each, the tags application's widget
page lists every widget with its options, the manual has a widgets page.

### Month calendar
```
{calendar data='agenda', title='what'/}         # this month (or ?month=2026-11), events, links ‹ ›
{calendar holidays='de-by'/}                   # the public holidays of a country marked and named - see Public holidays
{calendar '2026-10', data='agenda'}            # as a pair: weeks as rows, days inside, events per day
  <tr>{days}<td class="{$class}">{$day}{events}<b>{$what}</b>{/events}</td>{/days}</tr>
{/calendar}
```

### Public holidays
```
{holidays 'nl', year=2026}{$date} {$name} ({$local}){/holidays}   # the rows: date, name, local, type, region, substitute
{holidays 'de-by', year=2026/}                                    # a table; a region by its ISO 3166-2 code - Bavaria
{holidays 'gb', from='today', to='+3 months'}...{/holidays}       # the days between two dates instead of a year
{holidays 'be', year=2026, lang='fr', where='$type eq "public"'}...{/holidays}   # names in French; observances out
{calendar '2026-04', holidays='nl'/}                              # the month with its holidays marked and named
```
```php
padHolidays ( 'nl', 2026 );                         // the rows of a year, region as a third argument or 'de-by'
padIsHoliday ( '2026-04-27', 'nl' );                // TRUE - King's Day; an observance is no holiday here
padWorkdays ( '2026-04-01', '2026-04-30', 'nl' );   // 20 - weekdays that are no public holiday, both ends counted
padAddWorkdays ( '2026-04-24', 1, 'nl' );           // '2026-04-28' - the Monday is King's Day; 0 is the date itself
```
The countries are the files of `pad/subs/holidays/types/`, named by their ISO 3166-1 alpha-2
code - at, be, ca, ch, de, dk, es, fr, gb, ie, it, lu, nl, no, pl, se, us - and a country is
added by adding its file. Each holds its national public holidays as rules, not dates: a fixed
date, days from Easter Sunday (the Gregorian computus), the nth or last weekday of a month, the
weekday on or after a date, the years a holiday is held, the years it moved, a move off a
Sunday (Koningsdag to the 26th) and substitute days - the next free weekday in the United
Kingdom, Friday or Monday in the United States, where an observed day can fall in the year
before. A row's `type` is `public` (a day off) or `observance` (a national day that is none -
Good Friday in the Netherlands, Liberation Day there in the years that do not divide by five);
`region` names the region of a regional holiday; `name` is English, `local` the country's own
language (`lang=` picks one of a country of more: be, lu, ch, ca). Regions: Germany's sixteen
states (`de-by`), the United Kingdom's nations (`gb-sct`, `gb-nir`; England and Wales without),
France's Alsace-Moselle and overseas departments (`fr-57`, `fr-974`); the header of every file
says what it leaves out. A workday is a Monday to Friday that is no public holiday. The
arithmetic is integer days, from 1583 to 9999 - no clock, no zone, no service. Code: the
holidays subsystem in `pad/subs/holidays/` - `holidays.php` the body of `{holidays}`,
`_lib/holidays.php` the rules, `types/` the countries; `pad/lib/holidays.php` the helpers.

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
{qr 'https://example.com/order/42'/}            # inline SVG, encoded on the server (subs/codes/_lib/qr.php)
{qr $url, size=200, level='H', title='Scan'/}   # level L, M (default), Q, H; color=, background=
{barcode '871234567890'/}                       # EAN-13 (check digit added); anything else Code 128
{barcode $parcel, type='code128', height=70/}   # scale= module width, plain = no digits under the bars
{barcode:upce '04210000526'/}                   # the barcode type: a UPC-A in its short form, 04252614
{itf14 '1540014128876'/}                        # bare, when nothing else has the name - between bearer bars
{code39 'PAD-2026', check/}                     # check: code39's modulo 43, itf's digit; msi check='mod1010'|'none'
{datamatrix 'PAD-2026-0042', size=120/}         # Data Matrix ECC 200, the smallest of its 24 square sizes
```
The symbologies are the files of `pad/subs/codes/types/` - `ean13`, `ean8`, `upca`, `upce`,
`code128`, `code39`, `code93`, `itf`, `itf14`, `codabar`, `msi`, `qr` and `datamatrix` - one
file a symbology, which encodes the text and draws it; a symbology is added by adding its file.
Each is written three ways, the same code: `{barcode '...', type='upce'/}`, the barcode type
`{barcode:upce '...'/}`, and bare, `{upce '...'/}`, when no tag, store, field or sequence has
the name; `{qr}` is the qr symbology by a tag of its own. The bars take `height`, `scale`,
`title`, `plain`, `color` and `background`, the two-dimensional codes `size`, `title`, `color`
and `background`; black on white unless told otherwise, as a scanner needs the contrast, and
`role="img"` named by the text or `title`. A text the symbology cannot hold is an error under
the strict check. Code: the codes subsystem in `pad/subs/codes/` - `barcode.php` the body every form
runs, `_lib/` the encoders and the SVG writers, `types/` the symbologies;
`pad/types/barcode.php` the barcode type.

### Icons
```
{icon 'arrow-right'/}                           # an icon in the colour of the text, decoration (aria-hidden)
<button>{icon 'trash', size=16/} Delete</button>
{icon 'settings', size=20, label='Settings'/}   # label= makes it an image of that name, for an icon alone
{icon:cloud-upload stroke=1.5/}                 # the icon type; the names are never reached bare
```
An icon is a file of `pad/subs/icons/types/`, `<name>.svg`: a whole SVG on the 24 by 24 grid -
lines, circles and rectangles, `stroke="currentColor"`, round caps and joins - which a browser
shows as it is and whose drawing `{icon}` writes inside an svg element of its own; the files
there are the icons there are, so an icon is added by adding its file, and `padIconNames ()`
lists them for a page's PHP. A name that is no icon is an error naming the icons close to it.
Code: the icon subsystem in `pad/subs/icons/` - `icon.php` the body both forms run, `_lib/icon.php`
the drawing, `types/` the icons; `pad/types/icon.php` the icon type.

### Patterns
```
{pattern 'topography', seed=7, width=960, height=320/}   # contour lines of a landscape the seed makes up
{pattern 'dots', size=24, colors='#fdfbf7, #e0c9a6'/}     # the background first, then the inks
{pattern:voronoi seed=3, title='Shards of colour'/}       # the pattern: type; title= makes it an image
<div style="background-image:{pattern 'waves', as='url'/}">...</div>   # url(data:image/svg+xml,...), either quotes
```
```php
$hero = padPattern ( 'circles', [ 'seed' => 4, 'width' => 1200, 'height' => 400 ] );   // the same bytes in PHP
```
Seeded SVG backgrounds and generative art drawn on the server: stripes, dots, grid, checks,
hexagons, triangles (a low-poly mosaic), waves, chevron, topography (value noise traced by
marching squares), truchet, voronoi, circles (circle packing), isometric (tumbling blocks), blobs,
grain (the browser's feTurbulence) and mesh (a soft gradient). Every pattern takes `seed=`,
`colors=` (a list, the background first), `size=` (the motif in pixels), `width=` and `height=`
(480 by 240; the SVG covers its box), `opacity=`, `title=` and `as='url'`; stripes `angle=` and
topography `levels=` besides. The same options give the same bytes on every request and every
machine - the random numbers are PAD's own seeded sequence (`padFake`'s, put back afterwards) and
every number is written to a fixed precision - so stored answers hold a pattern; without `seed=`
the seed is 1. A pattern is decoration (`aria-hidden`) unless `title=` names it, and draws no
motion. The patterns are the files of `pad/subs/patterns/types/` - each answers its default size
and colours, its own options and the function that draws it - and a pattern is added by adding
its file; their names are never reached bare, as dots, grid and waves are names of fields and
pages. A pattern is drawn on every request, so a kind that draws a shape per place, and topography
its grid times its levels, has a budget for the size, the picture and the levels together - past
it the pattern is not drawn. An unknown pattern, a colour that is none, a number outside its bounds
and a pattern past its budget are errors under the strict check. Code: the pattern subsystem in `pad/subs/patterns/` - `pattern.php` the
body both forms run, `_lib/` the drawing and its helpers, `types/` the patterns;
`pad/types/pattern.php` the pattern type, `pad/lib/pattern.php` `padPattern`. The `patterns`
application shows every pattern with a seed to change by link.

### Image filters
```
{img 'photos/harbour.jpg', width=400, filter='duotone', colors='#123, #fc6', alt='The harbour'/}
{img 'photos/harbour.jpg', width=400, filter='grayscale, dither', alt='The harbour'/}   # in the order written
{img 'photos/harbour.jpg', width=400, filter='halftone', cell=6, alt='The harbour'/}    # SVG dots for the <img>
{img 'photos/harbour.jpg', width=400, filter='ascii', columns=80, alt='The harbour'/}   # text in a <pre role="img">
{img 'photos/harbour.jpg', width=400, filter='blurhash', alt='The harbour'/}            # a placeholder behind it
```
`filter=` on `{img}` runs the picture through the filter subsystem, `pad/subs/filters/`: a filter
is a file of its `types/` - no tag of its own, a word of `filter=` as a validation rule is a word of
`padValidate` - and the files there are the filters there are. grayscale, sepia (`amount=`),
duotone (`colors=`, dark first), posterize (`levels=`), pixelate (`cell=`), blur (`radius=` CSS
pixels), sharpen and vignette (`amount=`), invert, and the dithers - dither (Floyd and Steinberg),
atkinson and bayer (ordered), with `levels=` and `colors=` - change the picture: they work on the
thumbnail after it was resized, the 1x and the 2x alike, and are part of its file name in
`www/<app>/_thumbs/`. A pixel filter (pixelate and the dithers) is made at 1x and drawn with its
pixels sharp; posterize, pixelate and the dithers are PNGs unless `format=` says otherwise. halftone
(`cell=`, `colors=`) writes the picture as SVG dots and ascii (`columns=`, `chars=`) as text, each
in place of the `<img>` and named by the alt text. blurhash (`components=`, `'4,3'`), dominant and
lqip are placeholders: a BlurHash - the reference algorithm, encode and decode - in `data-blurhash`
and decoded to a small PNG, the dominant colour, a tiny blurred PNG, behind the `<img>` while it
loads. What a filter makes is made once with GD and kept in `_thumbs/` - dots, text and
placeholders too - the same picture and options the same bytes on every run. A filter that reads
every pixel in PHP (sepia, duotone, posterize, vignette, the dithers, dominant) works on 2 megapixels
at most, its 2x picture made only as far as those reach, and halftone draws 50,000 dots at most. An
unknown filter, an option out of its bounds, a filter that cannot follow the one before and a filter
past its budget are errors under the strict check, left out without it. Code: `pad/subs/filters/` - `_lib/filter.php` the chain and its options, `_lib/dither.php`,
`_lib/blurhash.php`, `types/` the filters - and `pad/tags/_lib/img.php`, which applies them. The
`patterns` application shows every filter on the same picture.

### Notations
```
{math}x = (-b +- sqrt(b^2 - 4ac))/(2a){/math}        # AsciiMath as MathML - every browser draws it, no script
{math 'sum_(i=1)^n i = (n(n+1))/2', display='block'/}  # alttext= what a reader without MathML says
{chem 'CH4 + 2O2 -> CO2 + 2H2O'/}                     # counts, charges, states, <=>, ->[Delta], every element checked
{abc}...{/abc}                                        # a tune in ABC - T: M: L: K: lines, then the music - as a staff
{notation:chord 'Am', instrument='ukulele'/}          # a chord box from a name or frets='x32010'
{piano 'C4 E4 G4'/}  {piano 'Dm7'/}                   # keys marked on a keyboard; from=, to=
{braille 'Hello'/}  {braille 'Lift', cells/}          # Unified English Braille grade 1, or its raised dots
{morse 'SOS'/}  {semaphore 'PAD'/}  {signals 'PAD 26'/}   # ITU morse and its signal; flag semaphore; ICS flags
{chess $fen, flip, highlight='e2, e4', arrow='e2-e4'/}    # FEN - the chess notation, the built-in tag {chess} too
{go '(;SZ[9]AB[cc];B[ee];W[ec])', numbers/}           # SGF played out, captures taken off; move=, plain
{draughts 'W:W31-50:B1-20'/}  {draughts 'B:W21-32:B1-12', board=8/}   # the FEN of PDN, 10 by 10 or 8 by 8
{notation 'braille', 'Hello'/}                        # the tag: its second parameter the kind's text
```
A text written in a notation, drawn on the server the way `{diagram}` lays out a flowchart:
MathML for the formulas, HTML and inline SVG for the rest - no script, the same bytes for the
same text, every drawing a named image with a description in words (the pieces, the stones,
the notes, the flags), its colours `--pad-<kind>-*` with `light-dark()` defaults. The text is
the kind's first parameter, or the content between the tags taken as written before the level
walks it, so the braces of AsciiMath and the brackets of SGF need no `{ignore}`. The notations
are the files of `pad/subs/notations/types/` - math (AsciiMath, not TeX, as TeX's braces are
PAD's), chem, abc, chord, piano, braille, morse, semaphore, signals, chess, go and draughts - and
a notation is added by adding its file. Each is written three ways: `{notation 'math', 'a/b'/}`,
the notation type `{notation:math 'a/b'/}` and bare, `{math 'a/b'/}`, after every other kind of
tag; `chord` bare is the charts' chord diagram, so a guitar chord is `{notation:chord}`. `{chess}`
stays a built-in tag that draws the chess notation (`pad/tags/chess.php`), so it comes before a
field, a store or a file named chess, as before. A text a notation cannot read - a FEN with
seven ranks, an element the periodic table lacks, a character morse has no sign for, a move on
a taken point - is an error under the strict check, and so is what would cost time or memory
past reason: a formula longer than 10 000 bytes, slurs nested more than 8 deep. A byte that is
no UTF-8 is a character no notation has a sign for; a text with nothing in it draws nothing, its
options unread, and the `@else@` shows. Code: `pad/subs/notations/` -
`notation.php` the body every form runs, `_lib/` the drawing of each kind, `assets/` the styles,
`types/` the kinds; `pad/types/notation.php` the notation type, `pad/tags/notation.php` the tag.
The `notations` application shows every kind with its examples.

### Showcase tags
```
{avatar 'Herbert Jebbink', size=48, shape='square'/}   # initials in a circle/square, colour from a hash of the name
{identicon $email, size=64/}                   # a symmetric 5x5 pattern from a sha256 hash, own colour
{placeholder '600x300', text='Hero image'/}    # grey SVG box with crossing lines; ratio='16:9', fluid
{progress 72, max=100, label='Upload'/}       # styled <progress> with label and %; steps for dots, color=, size=
{rating 4.5, max=5/}                           # stars as SVG, half stars clipped, aria '4.5 out of 5'; icon='heart'
{icon 'arrow-right', size=20, label='Next'/}   # line icon in currentColor, a file of subs/icons/types/; see Icons
{gravatar $email, size=64, default='identicon'/} # gravatar.com img (sha256, lazy, no referrer); fallback='avatar' stays local
{chess $fen, flip, highlight='e2,e4', arrow='e2-e4'/}   # a FEN as an SVG board, pieces described for a screen reader
{sudoku '53..7....6..195...'/}                   # a game - played in the page; see Games
{diff $old, $new/}                              # word diff with <del>/<ins>, escaped; lines, side, from=, to=
{excerpt $body, words=24, highlight=$q/}         # whole words around the first match, <mark>ed, escaped; html
{lorem paragraphs=3, seed=7/}                    # placeholder text, classic start, reproducible; words=, sentences=, varied
{tabs}...{/tabs}  {modal 'x'}...{/modal}         # the widgets - see Widgets
{img 'photos/harbour.jpg', width=400, height=300, alt='The harbour'/}   # GD thumbnail in www/<app>/_thumbs/, 2x srcset, lazy
{video 'https://youtu.be/ID', title='Launch'/}  # poster + play button, nocookie iframe only after a click; {embed} for more sites
{pdf file='invoice', download}...{/pdf}   # the content as a PDF (plain PHP writer) instead of the page; preview: a sheet + link
{printable:labels data='rows', sheet='a4-3x8', pdf}...{/printable:labels}   # sheets for paper, printed alone, the same in the PDF; see Printables
{diagram direction='LR'}A[Order] --> B{Paid?}{/diagram}   # a flowchart laid out as SVG on the server; the other kinds - see Diagrams
{map 'europe', data='sales', key='country', value='amount'/}   # choropleth: ISO alpha-2/3 or name, world or a region
{diagram direction='LR'}A[Order] --> B{Paid?}{/diagram}   # flowchart (or type='sequence') laid out as SVG on the server
{map 'europe', data='sales', key='country', value='amount'/}   # choropleth: ISO alpha-2/3 or name; a view per file of pad/subs/maps/types/ - world, continents, benelux, eu, middle-east ...
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
and is swapped in. The first region brings a small inline script. A `pad-change` on a select of
many - `{multiselect 'tags', pad-change='pick'/}` - posts every choice, `tags[]`, as its form
would, and `padLiveValue()` is the choices joined by `, ` (`a, c`, what `value=` takes); the
list itself is `$_POST ['tags']`.

### Sections
```
{section:hero title='Pages that drive themselves', button='Start', link='?start', image='hero.jpg', alt='...'}
  The text under the heading.                          # the content is the text
  {slot 'eyebrow'}New in PAD{/slot}                    # a part filled with markup - wins over eyebrow=
{/section:hero}
{section 'cta', 'Ready?', button='Go', link='?go'/}    # the tag; the second value the heading
{section:features title='Why'}                         # a list section: its items are pairs ...
  {section:item 'Fast', icon='zap'}Renders in milliseconds.{/section:item}
{/section:features}
{section:pricing title='Plans', data='plans'/}         # ... or the rows of data=, by their fields
{section:pricing}{section:item 'Pro', price='€12', period='month', featured, link='?pro'}
  Every page                                           # a plan's features, a line each
{/section:item}{/section:pricing}
{section:faq open=1, single}{section:item 'Is it free?'}Yes.{/section:item}{/section:faq}
{section:newsletter 'Stay in the loop'/}               # a form posting back: padPosted ( 'newsletter' )
{section:contact 'Get in touch', mail='hi@example.com', phone='+31 20 123 4567', form/}
{section:footer brand='PAD', note='© 2026'}{section:item 'Product'}Pricing | ?pricing{/section:item}{/section:footer}
```
The blocks a landing page is built of, in HTML and CSS alone - the questions are `<details>`, the
forms post back without a script: hero, features, pricing (a featured plan with its badge),
testimonials, faq, cta, stats, steps, team, logos, newsletter, contact, gallery and footer, and
`item`, the item of the sections that list things. A part of a section comes three ways, the
first that has one winning: a `{slot 'name'}` pair directly in its content - template, rendered
where the section stands, in the scope of the loop around it - an option of the part's name,
and for the text under the heading the rest of the content. A list section takes its items from
the rows `data=` names (a store, the page's array, a `_data` file) and from `{section:item}`
pairs inside it, also ones a loop makes: the item's first value is its name - a plan, a
question, a person - its options its fields and its content its text, a plan's features a line
each. The handling options - `sort=`, `first=`, `where=` - work on the rows of `data=`, and a list
section whose `data=` finds none, with no items of its own, is a miss - its `@else@` shows; an
option the section has no field for, a slot of a part it has not, an item outside a list
section and a list section given neither `data=` nor items are reported under the strict check. The newsletter and the contact form post back
as `{form 'newsletter'}` does - the session's token and the form's name - so the page's PHP asks
`padPosted ( 'newsletter' )` and checks the post with `padValidate`; the fields refill and show
the messages, and once the post came back right the section says `thanks=` in the form's place -
the one that was sent, of two of a name on a page, as of two `{form}`s; a field's id is the
section's and its name.
Every section is a size container - its layout follows the room it has (`@container`), not the
window; the colours are `--pad-section-bg`, `-panel`, `-text`, `-soft`, `-dim`, `-line`,
`-accent`, `-accent-text`, `-accent-soft` on `.pad-section`, their defaults the frame's palette
(`--bg`, `--text`, `--accent` ... of `www/_common/pad.css`) and light and dark steps of their own
without it, so a theme colours the sections without a word; `--pad-section-hero`, `-radius`,
`-max`, `-font` and `-heading-font` are the rest. `tone=` (plain, soft, accent) and `align=`
(center, left) on every section, `level=` its heading's level (2), `id=` an anchor. A section is
written `{section:hero}` or `{section 'hero'}` - never bare: hero, team, stats and footer are the
names of fields and pages. The kinds are the files of `pad/subs/sections/types/`, the style
`www/assets/sections/` - `section.css` shared and a sheet per kind, each written once per
page with the CSP nonce; `pad/subs/sections/section.php` the body every form runs,
`_lib/section.php` the parts, items and forms; `pad/types/section.php` the section type. The
`sections` application shows every one beside its template.

### Effects
```
{typewriter}Pages that write themselves.{/typewriter}           # typed out behind a caret, once
{effect:highlighter}the terms{/effect:highlighter}             # a marker stroke behind the words - a <mark>
{effect 'countup', to=1250, suffix='+'/}                       # counted by the CSS (@property), read as 1250+
{glitch}SIGNAL LOST{/glitch}  {shimmer}New{/shimmer}  {wave}Hi!{/wave}
{gradient colors='#f97316, #db2777', animate}Launch week{/gradient}
{marquee duration=25, label='Built with'}...{/marquee}         # a band that scrolls, a Pause button without script
{reveal motion='left'}<div>...</div>{/reveal}                  # comes into view as it is scrolled to - view()
{tilt glare}<div class="card">...</div>{/tilt}                 # leans in 3D on hover and focus
{spotlight at='top left'}...{/spotlight}                       # a soft light that sweeps across and rests
{spinner style='dots', label='Saving'/}                        # ring, dots, bars, pulse, orbit - a status
{confetti}<button type="button">Celebrate</button>{/confetti}  # the one with a script; single: bursts in view
```
Motion and decoration on a text or a block, CSS first: confetti is the only effect with a script,
`www/assets/effects/confetti.js`; the rest is CSS, a stylesheet per kind written once per page
with the CSP nonce, after the base every effect shares. Every animation stands inside
`@media (prefers-reduced-motion: no-preference)`, so a visitor who asks for less motion gets the
content still and whole - typed out, counted, revealed, marked - and confetti does not burst.
A screen reader reads the text once, as it ends: the letters of a wave and the slices of a glitch
are a copy marked `aria-hidden`, the typewriter's text is whole in the page while the CSS
uncovers it, a count-up reads its last number, a spinner is a `role="status"` with its label.
What repeats runs `repeat=` times - three unless given, `forever` the author's choice; the marquee
stops while the pointer or the focus is on it and has a Pause button that works without script.
The numbers of one effect - its duration, its delay, the length of its text - are custom
properties in a rule of a class of their own, written by the server from checked values once per
page with the nonce - never a style attribute, which a CSP nonce drops; the
colours are `--pad-effect-accent`, `-accent2`, `-accent3`, `-text`, `-mark` and `-track` on
`.pad-effect`, light and dark, falling back to the frame's `--accent`, `--text` and `--line`, so a
theme colours them. The kinds are the files of `pad/subs/effects/types/` - typewriter, glitch,
shimmer, gradient, wave, highlighter, countup, marquee, reveal, tilt, spotlight, spinner, confetti -
and an effect is added by adding its file. Each is written three ways, the same effect:
`{effect 'typewriter'}...{/effect}`, the effect type `{effect:typewriter}...{/effect:typewriter}`
and bare, `{typewriter}...{/typewriter}`, when no tag, store, field or sequence has the name. A pair
works on its content, rendered first; a single tag on its first parameter, `{typewriter 'Hi'/}`.
`highlighter` is so named because `{highlight}` colours code. The reveal, the highlighter's and the
count-up's `scroll` are scroll-driven animations (`animation-timeline: view()`): where a browser
has none, the block stands there and the stroke or the count runs in time, as without scroll. Code: the effects
subsystem in `pad/subs/effects/` - `effect.php` the body every form runs, `_lib/effect.php` the
options and the markup, `types/` the kinds, `assets/` their looks; `pad/types/effect.php` the
effect type, `pad/tags/effect.php` the tag.

### Themes
```
$padTheme = 'paper';               # _config/config.php: every page of the application in paper
{theme 'terminal'/}                # this page in terminal - its <style>, where the tag stands
{theme 'ocean'}...{/theme}         # a pair: its content alone, in a box of the theme
{themes/}                          # a switcher - the visitor's choice, kept in a cookie
{themes label='Colours', button='Use'/}
```
A theme swaps the colours of the frame the PAD applications share (`www/_common/pad.css`) for a
set of its own: the `--frame-*` properties of the side, the bar and the menu and the palette the
pages build on - `--bg`, `--panel`, `--text`, `--soft`, `--dim`, `--line`, `--accent`,
`--accent-soft`, `--head`, `--code`, the `--ok`/`--warn`/`--bad` pairs - with its
`color-scheme`, so every part that follows the scheme with `light-dark()` - the charts, the
widgets, the toys - follows the theme, and the sections and the effects, whose colours fall back
to that palette, take it too. The frame's files are not changed: a theme is a `<style>` on top of
them, on `:root:root:root`, which outweighs the frame's own light and dark rules wherever it
stands, with the request's nonce. A theme may set the font (`--sans`: paper and newspaper a serif, terminal a
monospace), the sections' hero background (`--pad-section-hero`), declarations of the body's own (`body` - blueprint's drawing grid, readable's size and spacing), which the box of a pair gets too, and rules of its own - the
contrast themes underline their links and ring the focus thick. The themes are the files of
`pad/subs/themes/types/` - light, dark, paper, terminal, newspaper, contrast, contrast-dark, sepia, ocean, forest,
mono, solarized-light, solarized-dark (after Ethan Schoonover's Solarized, MIT, its tones darkened or lightened where AA needs), night (warm colours alone, no blue light), blueprint (white lines on a cyanotype's blue, a faint grid), readable (set for readers with dyslexia after the British Dyslexia Association's style guide - cream, a plain sans-serif, 16 pixels, wider letters, words and lines, 70 characters a paragraph, bold for italics) - a theme is added by adding its file, which sets every one of the 24 colours (the framework case
`themes/every_theme_sets_every_colour_of_the_frame` holds it). Every theme's text reaches WCAG AA on each of its
backgrounds (4.5 to 1: the text, the soft and dim greys and the links on the page and on a panel,
a button's text on the accent, the side's texts on the side and the menu, the status colours on
theirs), a theme whose file declares `'needs' => 7` AAA (7 to 1) - contrast and contrast-dark; the framework case `themes/every_theme_s_text_reaches_wcag_aa_on_its_backgrounds`
holds the lowest ratio of each - light 4.57, sepia 5.01, forest 5.17, paper 5.36, dark 5.54,
newspaper 5.97, mono 6.00, terminal 6.01, ocean 6.22, contrast 7.41, contrast-dark 8.98, solarized-light 4.70, solarized-dark 4.86, night 6.46, blueprint 5.75, readable 5.26 - and the sections
application lists every pair of every theme. The light and dark themes are the frame's own
colours, with the four that fall short of AA in its set - the dim grey on the page (4.47), the
side's headings (3.07 light, 4.09 dark), a link on the accent's tint (4.48) - darkened or
lightened to it. Which theme a page gets: the visitor's choice from `{themes/}` - in the bar of
the frame every PAD application shares - posted back to the page and taken before the
application runs (`pad/inits/themes.php`, a 303 back to the page), kept in a cookie of its own,
`padTheme` (SameSite=Lax, a year), on the directory the PAD applications are served under
(`$padRoot`), checked against the themes on every read, never in the session, Default taking it
away; a post from another site (`Sec-Fetch-Site: cross-site`) changes nothing - on the pages
built on the frame (they link `_common/pad.css`) of every application, or on all pages of an
application that sets a `$padTheme`; else `$padTheme`. A page with a design of its own is never
coloured by a visitor's choice, and `$padTheme = FALSE` turns both off. That theme's
`<style>` goes into the page's `<head>` on the way out (`exits/output.php`) - before `</head>`,
in a page without one before its `<body>` or after its doctype - after the page cache, so a
cached page is served in the theme of whoever asks, and the ETag is made from what goes out;
never into an include request, a `{live}` region's or a fragment's answer, a page that is no
HTML, or a page with a `{theme 'x'/}` of its own, which is in that theme. The switcher is a
`<select>` alone - its label there for a screen reader - that applies a theme the moment it is
chosen, a small script posting the form; without JavaScript a button does it - and it
writes no token of its own, so showing it or choosing starts no session; with `$padCsrf` on it carries the
session's token as every form then does. Code: `pad/subs/themes/` - `_lib/theme.php`
(`padThemeCss`, `padThemeChosen`, `padThemeAdd`, `padThemeContrasts`), `theme.php` and
`themes.php` the bodies of `{theme}` and `{themes}`.

### Palettes
```
{palette 'viridis', steps=7/}                    # swatches, each with its hex value
{palette 'okabe-ito'/}                           # a categorical palette: its own colours
{chart 'bar', data='sales', value='north, south, west', palette='viridis'/}   # any kind of chart
{chart 'heatmap', data='temps', x='month', y='day', value='change', palette='rdbu'/}
$colors = padPalette ( 'viridis', 7 );           # [ '#440154', '#443a83', ... ] for the page's PHP
```
Colour scales for charts and anything else. The palettes are the files of
`pad/subs/palettes/types/` - each its name, its type and its colours - and a palette is added by
adding its file. Sequential: viridis, magma, plasma, inferno and cividis, perceptually uniform,
from matplotlib's published 256-colour tables (`_cm_listed.py`), and ColorBrewer's blues, greens,
oranges and greys of nine classes; diverging: ColorBrewer's rdbu, brbg and piyg of eleven;
categorical, told apart by colour-blind eyes: okabe-ito (Okabe and Ito's eight, black put last,
where it vanishes on a dark surface) and Paul Tol's bright, muted and vibrant (from his
`tol_colors` package), and ColorBrewer's pastel (Pastel1) and PAD's own earth. `steps=` samples a
sequential or diverging palette evenly from its first colour to its last, the colours between
two of its own mixed in sRGB - nine steps of a nine-class scale are the scale itself - one step
its middle; a categorical palette gives its colours in order and starts again past its last.
Without `steps` a categorical palette gives its own colours, the others nine; 1 to 256.
`padPalette ( $name, $steps )` answers the same list to a page's PHP. `{palette}` writes a row of
swatches - a list for a screen reader, each hex value in black or white, whichever contrasts
more - its style once per page with the nonce, a swatch's colours a class of its value
(`pp-c-440154`) with its rule; `title=` names the row.
`palette=` on `{chart}` colours a chart of any kind: the kinds draw as ever - their marks carry
the classes of the categorical slots (`pc-c1`...`pc-c8`) and of the heatmap's ramp
(`pc-h0`...`pc-h6`) - and the svg gets a class of the palette and its slots, `pc-pal-viridis-3`,
whose rule in the chart's own `<style>` gives it the palette's colours for those as custom
properties: a categorical palette its colours in order, a sequential or diverging one spread
over the slots the chart uses - the middles of so many equal parts, so three series of viridis
are purple, teal and green, never three purples, and neither pale end is a slot - and its whole
length on the ramp. No colour stands in a style attribute, which a CSP nonce drops. A chart drawn
in the browser (`client`) gets the same: the server hands it the class and the rules for each
number of slots and `www/assets/charts/palette.js` puts on the ones its
drawing needs - the same SVG, character for character, which the `charts` line of `./ci.sh` holds
by drawing every example of the charts application once more in a palette, the palettes in turn.
Without `palette=` a chart is drawn as before, byte for byte. Code: `pad/subs/palettes/`
(`_lib/palette.php`, `palette.php` the body of `{palette}`), `pad/lib/palette.php` (`padPalette`),
`pad/subs/charts/_lib/palette.php` and `client/palette.js` for the charts.

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

// The format of the output type 'format' - ics, xlsx, toml ... (Data formats) - and the
// options of the writer of a page's data answer: a feed's title and link, a calendar's name
$padOutputFormat  = '';
$padFormatOptions = [];

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

Every PAD application shows the same brand top left - `{common:padBrand/}`
(`_common/_tags/padBrand.pad` + `.php`): the `{}` logo, a link to the PAD home, and beside it
`PAD <app>`, the pull-down of every PAD application, grouped as `_common/_data/padApps.php`
lists them (the name and a line about each), the one shown marked. The list opens in front of
everything the page has - a popover in the top layer (`www/_common/brand.js`) - with the page
dimmed behind it; a click on the dimmed page or Escape closes it. The frame below writes it;
an application with a bar of its own would put the tag in it, which links
`www/_common/brand.css` and `brand.js` itself; the brand's colours follow the page's
`color-scheme`, and its name the colour of the bar. Offline (`pad pack`, `padWasm()`) the logo is no link, the name links the
application's home and the stylesheet is written into the page.

Every example an application shows - a template beside its result - has two little icons at
its top right: ▶ takes it to the [playground](../apps/playground/README.md), which renders
a template inside its own request, and ⚡ to the [live](../apps/live/README.md) application,
which runs it as a page of this server - its PHP, request values, sessions, `_common` and its
demo database, forms that post back. Both links carry the template for the template box, and
its PHP for the PHP box or its data for the data box. The `{example}` and `{table}`/`{demo}`
tags of `_common` write them, and so do the applications with examples of their own -
showcase, charts, tags, types, games, toys, inputs, examples - through
`_common/_lib/playground.php`: `padPlayLink ( $template, $data, $php, $inline, $only )` the
icons, `padPlayPage ( $root, $page )` those for a page of an application (its `{meta}` line
left out, its paired PHP or else the `_data` file its template names with `data='...'`, under
that name), `padPlayData ( $name, $file )` such a `_data` file as data, with
`www/_common/play.css`. A `{table}` of demos is one example, every row of it; the icons stand
at the top right of the nearest `position: relative` box (the class `pad-play-box`).

Some examples only a page of its own can run, and they have the live icon alone: the ones the
tags and types applications show without running them - a `{redirect}`, an `{exit}` - and
those of an application that sets `$playExamples = 'live'`, with `$playLivePhp` as PHP the
live link puts before each example's own: classicModels, whose examples need its tables of
the demo database and the declarations of its `_inits.php`. `$playExamples = FALSE` leaves
both out; the examples of htmx, react, alpine, islands and webcomponents - pages with scripts
and endpoints of their own - and padSelect's, on a database of its own, have none, and
offline (`padWasm()`) there is no icon. Both editors fill their boxes from the link and wait
for Render or Run.

Every PAD application - the ones that use `_common`, and the ones that have it off and reach it
with the `common:` prefix - shares one frame from `_common`, which has no wrapper of its own
(no `_common/_inits.pad`): a side with the brand on top - `{common:padBrand frame/}`, its links
in the frame's `<head>` - the application's menu under it, and a bar above the page with the
title and the theme switcher (`{themes/}`, left out with `$padTheme = FALSE`); side and page are
light - `<html data-theme="light">`, whatever the system prefers - or in the theme the visitor
chose, Dark among them, kept in the `padTheme` cookie. The bar has no light/dark switch: Default
is light, and a page with a setting of its own - the editor's - sets `data-theme` through
`www/_common/pad.js`. The
switcher's post is answered before the application runs (`pad/inits/themes.php`) - a 303 back
to the page - so no page's own PHP sees it. Left out on purpose: cli (a console), nono (plain
PHP), test (a scratch application) and the regression applications. Their `_inits.pad`:

```
{common:padFrame}                                  # _common/_tags/padFrame.pad + .php
  {slot 'head'}{asset 'admin.css', tag/}{/slot}     # also 'title', 'tools', 'side', 'foot'
{/common:padFrame}
@page@
{common:padFrameEnd/}                               # _common/_include/padFrameEnd.pad
```

`$frameMenu` (rows of `head`, or `link`, `label`, `icon`, `current`) from `_inits.php` is the
menu, `$title` the bar's title (`$skipTitle` leaves it out); `{common:padFrame fill}` for a
page that fills the window. Offline (`pad pack`, `padWasm()`) the frame writes its stylesheet
and script into the page. The stylesheet is `www/_common/pad.css`: the
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
