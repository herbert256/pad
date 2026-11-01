# PAD Tags Reference

This document provides a complete reference for all PAD tags, grouped by functionality.

A tag is written one of two ways. Single, it closes itself with a slash before its brace -
`{echo $x/}`, `{page 'header'/}`, `{flush/}`, or `{echo $x /| upper}` with an opening pipe.
Written without the slash it opens a pair, and its closing tag must follow:
`{items}...{/items}`. Many tags take both forms - `{sequence '1..5'}{$sequence}{/sequence}`
loops, `{sequence '1..5', push='n'/}` only stores - and the slash says which one is meant. A
tag without the slash and without its closer is an error under the strict check, "the pair
{echo} never closes - a single tag is written {echo .../}"; the lenient walk reads it single.
The variable forms `{$x}`, `{!x}`, `{#x}`, `{&x}`, `{?x}`, `{^x}` are never pairs and take no
slash, nor do the branch markers `{else}`, `{elseif}` and `{when}`.

---

## Control Flow Tags

### if
Conditional execution based on expression evaluation.

```html
{if $condition}
  Content shown if condition is true
{elseif $other_condition}
  Content shown if other condition is true
{else}
  Content shown if all conditions are false
{/if}
```

**Parameters:**
- First parameter: Expression to evaluate

**Supports:** `{elseif}` and `{else}` clauses

---

### case
Switch-case style conditional based on value matching.

```html
{case $value}
  {when 'option1'}Content for option1
  {when 'option2', 'option3'}Content for option2 or option3
  {else}Default content
{/case}
```

**Parameters:**
- First parameter: Value to match against

**Supports:** Multiple `{when}` clauses, each listing one value or several separated by
commas; the first one that matches answers. The default
branch is `{else}` - `{when 'default'}` is an ordinary branch that matches the text
`default`.

---

### ifchanged
Render a block when a value differs from the previous row of the enclosing loop - a
heading per group in a sorted list.

```html
{orders sort='customer'}
  {ifchanged $customer}<h2>{$customer}</h2>{/ifchanged}
  <p>{$number}: {$total}</p>
{/orders}
```

**Parameters:** one or more values; the block renders when any of them changed. The first
row always renders; an unchanged value renders the `@else@` branch, if there is one.

**Behavior:** The memory belongs to one run of the enclosing loop - the nearest level that
iterates, past an `if`, `case`, `ifchanged`, `guest`, `can`, a property pair or a snippet,
which render once for the row - so a loop that runs again starts afresh, and to the tag's
place in it: the n-th `ifchanged` of a row is compared with the n-th of the row before.

---

### tree
Renders a tree: its body for every row, and inside the body `{recurse}` renders the same
body again for the current row's children - menus, category trees, threaded comments.

```html
<ul>
{tree 'menu', children='items'}
  <li><a href="?{$page}">{$title}</a>
    {branch}<ul>{recurse/}</ul>{/branch}
  </li>
{/tree}
</ul>
```

**Parameters:**
- First parameter, or `data=`: the rows - a `{data}` block, a stored sequence, an array of
  the page or of an enclosing row, or a `_data/` file
- `children` - the field holding a row's children (default `children`)

**Behavior:** `depth@tree` is 1 for the tree's own rows and one more per `{recurse}`;
`first@tree`, `count@tree` and the other properties speak of the list being rendered. The
handling options `sort`, `where` and `reverse` written on the tree apply to every list, the
children included. The `@else@` part shows when there are no rows at all. Strict mode names a
tree without rows, or with a name nothing holds.

---

### branch
Inside a `{tree}`: renders its content only when the current row has children - the `<ul>`
around a `{recurse}` - and its `@else@` part for a leaf.

```html
{branch}<ul>{recurse/}</ul>@else@<span class="leaf"></span>{/branch}
```

---

### recurse
Inside a `{tree}`: renders the tree's body once more for the children of the current row,
each child a row of its own. A row without children renders nothing. The new level is
named as the tree is, so `depth@tree` and `count@tree` follow it.

```html
{recurse/}
```

---

### switch
Rotating switch that cycles through options on each call.

```html
{switch 'odd', 'even'/}
```

**Parameters:**
- Every parameter is a value to cycle through; the rotation is keyed on the tag's own option text

**Returns:** Next value in rotation sequence

The rotation lives for the request, but not across a nested pass: engine state is restored
when a `{code}` pass of any kind returns, so a pass continues each rotation from where the page
had it, and the steps it took are rolled back when it returns.

---

### while
Loop while condition is true.

```html
{while $condition}
  Loop content
{/while}
```

**Parameters:**
- First parameter: Condition expression

**Behavior:** Continues iterating while condition evaluates to true

---

### until
Loop until condition becomes true.

```html
{until $condition}
  Loop content
{/until}
```

**Parameters:**
- First parameter: Condition expression

**Behavior:** Continues iterating while condition is false (opposite of while)

---

## Variable and Data Tags

### set
Set one or more variables as globals. The assignment sticks: a `{set}` inside a loop or any
other level outlives it - `{set $x = 1/}{sequence 3}{set $x = 8/}{/sequence}{$x}` gives 8.
(A `$name = value` written on another tag, `{users $n = 5}`, is that level's own and is
unwound when the level closes.)

```html
{set $name = 'value', $count = 5, $active = TRUE/}
```

**Parameters:**
- `$name = value` pairs become global variables - a pair without its `$` is an error

**Note:** Cannot be used as open/close tag pair

---

### get
Fetch another page of this application over HTTP and insert its output. It is a real second
request, rendered bare (without the `_inits`/`_exits` wrappers); every variable `{set}` at this
level is passed along on the query string, and the output comes back with its braces escaped
so it is not parsed again. A stored content block is read with `{content:name}` instead.

```html
{get 'page_name'/}
```

**Parameters:**
- First parameter: The page to fetch

**Returns:** The page's output

---

### data
Store data to the data store for iteration.

```html
{data 'store_name'}
  Content to iterate
{/data}
```

**Parameters:**
- First parameter: Data store name
- `format=` (or `type=`): the format of the content - json, xml, yaml, csv or a format of
  `pad/subs/formats/` that reads: toml, ini, tsv, ndjson, markdown, ics, vcf, geojson, gpx;
  left out, the format is recognised on sight (an iCalendar, a vCard, NDJSON, a Markdown pipe
  table and TSV among them) - see [Data formats](../FEATURES.md#data-formats)

**Behavior:** Stores the data under the name and prints nothing; `{store_name}...{/store_name}`
iterates it

---

### content
Store content to the content store.

```html
{content 'store_name'}
  Content to store
{/content}
```

**Parameters:**
- First parameter: Content store name

---

### bool
Store boolean value to store - the content (or the second parameter) reduced to TRUE or FALSE.

```html
{bool 'store_name'}content{/bool}
{bool 'store_name', $value/}
```

**Parameters:**
- First parameter: Store name
- Second parameter: the value, when there is no content - with neither the flag is FALSE

---

### field / array / record / check
Database access tags. The tag name becomes the `db()` command word, so do NOT write `SELECT` in the parameter:

```html
{field "count(*) from users"/}                     <!-- single value -->
{record "* from users where id=5"}...{/record}    <!-- single row -->
{array "* from users order by name"}...{/array}   <!-- iterate rows -->
{check "users where email='x@y.z'"}...{/check}    <!-- existence test -->
```

**Parameters:**
- First parameter: the SQL after the command word (`check` uses the special `table WHERE ...` form, no `* FROM`)

**Returns:** Query results (`check` returns TRUE/FALSE)

**Designer preview:** with `?page&padSample`, a tag with a `name=` the page's sample holds
answers from the sample instead of the database - `{array "* from orders", name='orders'}` -
and `&padSample=capture` records the database's answer under that name (see APP.md).

---

### collection
Iterate a folder of Markdown files - a small flat-file CMS.

```html
{collection 'blog', sort='date DESC', first=10}
  <h2><a href="?blog/post&slug={$slug}">{$title}</a></h2>
{/collection}

{collection 'blog', slug=$slug}<h1>{$title}</h1>{!body}@else@No such post.{/collection}
```

**Behavior:** Reads `_content/<name>/*.md`, looked up like a `_data` file (the page's
directory, its parents, then `_common`). Each file is a row: its front matter (YAML between
`---` lines) as fields, plus `slug` (the file name without `.md`), `body` (the Markdown
written as HTML by the `{markdown}` renderer - print it raw, `{!body}`) and `source` (the
Markdown text). Rows come in file name order; `sort`, `first`, `where` and the other handling
options work as on any data.

| Option | Description |
|--------|-------------|
| `slug` | Only the file of that name - no rows (the `@else@`) when it is missing or not a plain name |
| `html` | Let raw HTML in the bodies through; by default it is escaped |

A missing collection is an error under the strict check. `padCollection('blog')` gives the
rows to a page's PHP.

---

### search
Site search without a search server: the rows of a source a query finds, ranked by BM25
(`pad/subs/search/`).

```html
{search 'blog', q=$q, fields='title^2, body', lang='en'}
  <a href="?blog/post&slug={$slug}">{$title}</a> {$score} {!excerpt}
@else@
  Nothing found.
{/search}
{search 'products', q=$q, link='url'/}                    <!-- a list -->
```

| Option | Description |
|--------|-------------|
| first parameter | The source: a Markdown collection `_content/<name>/`, else a `{data}` store, a page's array (a `db()` result) or a `_data` file of the name |
| `q` | The query: words (any), `"a phrase"` (required, in order), `prefix*`, `-word` and `-"a phrase"` (left out) |
| `fields` | The fields searched, each with its boost: `'title^2, body'` - a collection's default; other rows every field that holds text in any row (none is an error under the strict check) |
| `lang` | The language of the stop words and the stemmer - `en`, `nl`, `de`, `fr`, `es`, the files of `pad/subs/search/types/`; default the locale's language, else `en` |
| `words` | The length of the excerpt in words, default 30 |
| `excerpt` | The field the excerpt is cut from, default the last of `fields` |
| `link` | The single tag's list: a field with the link of a hit - http(s), a page or an anchor; never `javascript:` |

**Behavior:** as a pair it hands over the rows found, the best first: the source's own fields
with `$score` (BM25F, three decimals) and `$excerpt` added - HTML, the words around the first
match with every matched word in a `<mark>`, escaped as `{excerpt}` escapes it; print it
`{!excerpt}` - the style of the marks stands before the first row when the template prints the
excerpt. The handling options work on them, `first=10` for the ten best - when they keep
the first rows only (`first`, `page`, `rows`) the excerpts are made for those rows alone;
nothing found is the `@else@`. As a single tag it answers an `<ol class="pad-search">` of the
`first=` best, else of every hit: the first field of `fields` as the title, a link when
`link=` names a field with one, and the excerpt. The index is kept in
`DATA/cache/search/<app>/` and made again when the source changed. A source that is not there,
a language without a file, a `fields=` that is no list of fields or a `words=` that is no
number is an error under the strict check. `padSearch ( $source, $q, $options )` gives
the same rows to a page's PHP.

---

### at
Evaluate an `@` expression (for example against a sequence or data set).

```html
{at "country.id='f0_325'@mondial"/}
```

**Parameters:**
- First parameter: The `@` expression to evaluate

**Returns:** The evaluated value via `padAtValue()`

---

## Counter Tags

### count
Check if array/data has elements.

```html
{count 'arrayName'}
  Has elements
@else@
  Empty
{/count}
```

**Parameters:**
- First parameter: the quoted name of a data store or page array - `{count $arrayName}`
  evaluates the array itself and is an error. It prints no number; the number of rows is
  `count@tag`

**Returns:** TRUE if has elements, FALSE if empty

---

### increment
Increment a variable by 1.

```html
{increment $counter/}
```

**Parameters:**
- First parameter: Variable name to increment

**Behavior:** Creates variable with value 1 if doesn't exist

---

### decrement
Decrement a variable by 1.

```html
{decrement $counter/}
```

**Parameters:**
- First parameter: Variable name to decrement

**Behavior:** Creates variable with value -1 if doesn't exist

---

## Execution Tags

### page
Include and execute a PAD page.

```html
{page 'pagename'/}
```

**Parameters:**
- First parameter: Page path to include

**Behavior:** Executes the page's PHP and PAD files. A name that is no page of its own resolves
through the clean URL routes: `{page 'products/42'/}` renders `products/[id].pad` with `$id` set
to 42 (see Clean URLs in docs/APP.md).

---

### code
Runs its content as PAD source in a separate engine pass and outputs what that pass
produced.

```html
{code}{echo 2 * 3/}{/code}        → 6
{echo $snippet | code/}           → $snippet run as PAD
```

**Piped into:** a value is text under `$padProtectValues` - a field holding a tag prints
the tag. Piping the value into `code` is the explicit way to run it as PAD, for a snippet
kept in a database.

---

### sandbox
The same as `code`, with the pass sandboxed: the fragment cannot see or leave behind
variables, and the data, content, bool and sequence stores are emptied going in and
restored coming out.

```html
{sandbox}{echo 3 * 3/}{/sandbox}  → 9
{echo $snippet | sandbox/}        → $snippet run as PAD, isolated
```

---

### action
Execute a sequence action, over a stored sequence.

```html
{action myStore, reverse}
{action:reverse myStore/}
```

**Parameters:**
- The store and the action, in either order; the prefix spelling names the action outright

---

### ajax
Embed another page of the application, fetched by the browser after the page has loaded: the
tag writes a `<div>` and a script that requests the page with `&padInclude` and puts its
answer in the div.

```html
{ajax 'orders'/}
{ajax 'orders', fragment='order-list'/}
```

**Parameters:**
- First parameter: the page to fetch
- `app` - a page of another application
- `$name = value` assignments go on the query string
- `fragment` - fetch only that response fragment of the page (see `fragment`)

---

### live
A region of the page that answers clicks, forms and changes by re-rendering on the server
and swapping itself in - without JavaScript of the application's own.

```html
{live 'cart'}
  {cart}<li>{$item} x {$qty}</li>{/cart}
  <button pad-click="add" pad-value="42">Add</button>
{/live}
```

```php
// cart.php - runs for the first render and for every event
if ( padLiveEvent () == 'add' )
  $cart [] = [ 'item' => padLiveValue (), 'qty' => 1 ];   // with $padSessionVars = ['cart']
```

**Parameters:**
- First parameter: the region's name - letters, digits, `_` and `-`

**In the content:**
- `pad-click="event"` on any element, `pad-submit="event"` on a form (its fields come along as
  request values), `pad-change="event"` on a field (its name and value come along - a select
  of many, a multiselect, posts every choice, `tags[]`, and its value is the choices joined by
  `, `); `pad-value="..."` gives the event a value.

**How it works:** the content is wrapped in `<div data-pad-live="cart">`, and the page's first
region brings a small inline script. An event posts `padLive`, `padEvent` and `padValue` to
the page's own URL; the page runs as always - its PHP reads `padLiveEvent()` and
`padLiveValue()` - and the response is the inner content of that region alone, which the
script swaps in, running the scripts it holds as a page that arrives runs its own - a timed
toast or a carousel the answer brings works (under a nonce policy only a script carrying the
answer's nonce runs, given the page's). State lives where a page keeps it: the session (`$padSessionVars`), a
database, or the value the event carries. With `$padCsrf` on, the region carries the
session's token (`data-pad-csrf`) and the script sends it with every event. A post for a
region the page does not render is an error. Under the strict check a `{live}` without a name, or without its `{/live}`, is an
error too.

---

### pad
A level with no behaviour of its own - a carrier for `data=`, `content=`, `name=`, pipes and
properties. It includes nothing.

```html
{pad data='source'}
  Template content
{/pad}
```

**Behavior:** Renders its content once, or per row with `data=`

---

## Navigation Tags

### redirect
Redirect to another page of the application.

```html
{redirect 'page'/}
```

**Parameters:**
- First parameter: URL to redirect to - a page of the application, also in its `?page` form
  (routes resolved, as `{page}` resolves them), or an absolute `http://` / `https://` address
- Variables set on the tag (`$color='red'`): added to the address's query string

**Behavior:** Performs HTTP redirect (302) and ends the request. A page name stays on the
site; an absolute address is sent as written, so a target taken from the request is the
template's to check first. `padRedirect()` in PHP takes page names only and never leaves the
site.

---

### restart
Restart PAD processing with new page.

```html
{restart 'pagename', $param1 = 'value1'/}
```

**Parameters:**
- First parameter: Page name
- `$name = value` assignments become variables of the new page - a plain named parameter is
  not passed

---

### pager
Page links for a tag written with the `page` handling option: the first and the last page, a
window around the current one, gaps, previous and next.

```html
{products page=$pg ?? 1, rows=12}
  <article>{$name}</article>
{/products}

{pager 'products', window=2/}
{-- → ‹ 1 … 4 5 [6] 7 8 … 20 › --}
```

**Parameters:**
- First parameter: the name of the paged tag (its `name=`, or the tag's own name); without
  it the pager follows the last paged tag of the page
- `window` - pages shown on each side of the current one (default 2)
- `query` - the request value the links set; by default the variable `page=` was written
  with (`page=$pg` → `pg`), else `page`

**Behavior:** The handling walk books, per paged tag, its page, its rows per page and the
number of rows the options written before `page` left; the pager reads that, so it comes
after the tag (strict mode names a pager without its paged tag). Links are `$padGo` plus the
requested page plus the request's other query values with the page value set - the
engine's `pad*` switches left out. A sequence written with `page=` books its own page: every
term of a range, a list or a store is counted, and of any other run the pages made, up to the
one after the page shown (see [sequences](../sequences/PAD.md)). As a single tag it answers
`<nav class="pager" aria-label="Pagination">` with `<a>` links, the current page marked
`aria-current="page"`, a missing previous/next as a disabled `<span>`; the labels
`pager.label`, `pager.previous` and `pager.next` come from the `_lang/` catalog when it has
them. With a single page it answers nothing.

As a pair it hands the links over as rows for markup of your own - `kind` (`prev`, `next`,
`page`, `current`, `gap`), `page`, `label` and `href` (empty for a gap and a missing
previous/next); a single page has no rows, so the pair shows its `@else@`:

```html
{pager 'products'}
  {if $href eq ''}<span>{$label}</span>{else}<a href="{$href}">{$label}</a>{/if}
@else@
  All on one page
{/pager}
```

A Select table is paged by its SQL limit, so its rows do not hold the total; the pager then
runs a count of the same query, only when it is there.

---

### jsonld
The structured data of a page for search engines: one `<script type="application/ld+json">`
holding a Schema.org object, built from the fields a kind maps by name - every kind is a file of
`pad/subs/jsonld/types/`, and the files there are the kinds there are (`padJsonLdKinds()`); the
subsystem is `pad/subs/jsonld/`.

```html
{jsonld 'article', headline=$title, author=$author, published=$date/}
{jsonld:article data='post', url=$url/}
{jsonld:faq data='questions'/}
{jsonld:breadcrumb/}
{jsonld:website name='Shop', search='?search&q='/}
```

A block is written two ways, the same block each time: the tag, `{jsonld 'article', .../}`, and the
jsonld: type, `{jsonld:article .../}` - never bare, as its names (article, event, person) are those
of fields and pages. `padJsonLd ( 'article', $post )` answers the same object as a PHP array.

| Kind | Schema.org | Its fields (required in bold) |
|------|------------|-------------------------------|
| `article` | Article | **headline**, description, image, url, author, published, modified, publisher, logo |
| `breadcrumb` | BreadcrumbList | rows of **name** (or label, text) and url (or link, href); without `data=` the page's trail, `home=` its first step, a directory that is no page left out |
| `breadcrumb` | BreadcrumbList | rows of **name** (or label, text) and url (or link, href); without `data=` the page's trail, `home=` its first step |
| `business` | LocalBusiness (or one of its 151 subtypes, type=) | **name**, **city**, type, description, image, url, phone, email, street, region, postcode, country, latitude, longitude, hours, prices |
| `course` | Course | **name**, **description**, provider, providerurl, url, image, code |
| `event` | Event | **name**, **starts**, ends, place, address, description, image, url, price, currency, availability, tickets |
| `faq` | FAQPage | rows of **question** and **answer** |
| `itemlist` | ItemList | rows of **url** (or link, href) and name (or label, title), each a numbered ListItem; name= the list's name |
| `job` | JobPosting | **title**, **description**, **posted**, **company**, expires, employment, companyurl, logo, street, city, region, postcode, country (needed with the place), remote, countries, salary (an amount or a range; needs currency and per), currency, per |
| `organization` | Organization | **name**, url, logo, description, profiles, phone, email, contact |
| `person` | Person | **name**, url, image, job, description, profiles |
| `product` | Product | **name**, description, image, url, sku, brand, price, currency, availability |
| `profile` | ProfilePage, a Person (or Organization, type=) its mainEntity | **name**, type, handle, identifier, description, image, profiles, followers, posts, created, modified |
| `recipe` | Recipe | **name**, **image**, description, author, published, prep, cook, total (durations), yield, category, cuisine, keywords, calories (needs yield), ingredients, steps, rating, ratings |
| `restaurant` | Restaurant (or another FoodEstablishment, type=) | every field of business, cuisine, menu |
| `software` | SoftwareApplication (MobileApplication, WebApplication, type=) | **name**, **price** (0 for free), type, description, url, image, os, category, version, download, currency, rating, ratings |
| `video` | VideoObject | **name**, **thumbnail**, **uploaded**, description, duration, file (contentUrl), player (embedUrl), expires, author, views |
| `website` | WebSite | name, url (the application's address), description, search |

**Options:** `data=` the row or rows - a store, the page's array, a field holding a list or a
`_data` file; several rows of a kind of one object make a `@graph`, the handling options work on
them, and no rows answer nothing, the `@else@` shown. `id=` the `@id`. An article's `url=` is
its own page - `$url = padUrl ( '', [], TRUE )` in the page's PHP; `$padGoExt` is the base a link
is written after, the application's `?`, not the page. The fields of the kind,
by their PAD name or the Schema.org property (`datePublished`), win over the row's - and a row is
never written whole, only what its kind maps. An empty field is left out. Addresses are made
absolute (relative to the application's address; another scheme than http and https left out),
dates written as ISO 8601 (passed as they are, or from what `date_parse` reads in full - `Fri,
20 Nov 2026` when the day is the date's own; a relative date is none), a price is a number with a dot, a currency three letters, an
availability a word of Schema.org; `search=` is the search address up to the term, written with
`{search_term_string}` after it. The JSON is written with `JSON_HEX_TAG` - a `</script>` in a
value cannot end the element - and needs no CSP nonce, a data block not being run. A missing
required field, a value it cannot write, a price without its currency, an option the kind does
not take and content between its tags are errors under the strict check; the lenient walk leaves
the object, or the field, out.

The later kinds' values: a duration is ISO 8601 (`PT1H30M`) or numbers with their units
(`1 h 30 min`, `90 min`), written as ISO 8601; a list - ingredients, steps - is a list in the row
or a text with a line between its values; a rating is a number, a count a whole number, both
written as JSON numbers; opening hours are written as openingHours writes them
(`Mo-Fr 09:00-17:30; Sa 10:00-16:00`), a type and the other words of a list in any case, a
salary as an amount or a range (`3200-3800`), a yes or no (`remote`) as yes, no, 1, 0, true or false.

---

## Web Application Tags

### csrf
The hidden form field holding the CSRF token of the visitor's session.

```html
<form method="post">
  {csrf/}
  ...
</form>

<meta name="csrf-token" content="{csrf token/}">
```

**Options:** `token` - the bare token instead of the field, for a script that sends it in an
`X-CSRF-Token` header.

**Behavior:** The token is 64 hex characters, one per session, made on first use - the
session starts on demand. With `$padCsrf = TRUE` every `<form method="post">` of the page
that posts back to the site gets the field without `{csrf}`, and every POST, PUT, PATCH or
DELETE without the token is answered 403 before the application runs; `padCsrfValid()`
checks a post by hand. A page carrying a token is not stored in the page cache.

---

### form
A form that posts back to the page, carrying the session's CSRF token and its own name.

```html
{form 'contact', class='wide'}
  {input 'email', type='email', label='E-mail', required/}
  {textarea 'message', label='Message', rows=6/}
  <button>Send</button>
{/form}
```

**Items:** the form's name first; `method='get'` for a form that does not post; `error=`
the message above the fields when the form came back with errors (`error` alone: "Please
correct the errors below."); every other item is an attribute of the `<form>` tag
(`action=`, `class=`, `enctype=`), written as `{attrs}` writes them.

**Behavior:** A posting form gets the hidden `padCsrfToken` field - not when its `action=`
(resolved against a `<base href>`), or the `formaction=` of a button in it or naming it with
`form=`, is on another site, which the token would be handed to; a button that sends the form
by GET (`formmethod`) to this site leaves it, and a `{csrf}` the template wrote is never taken
out - and a hidden `padForm` field
holding its name - `padPosted('contact')` is TRUE when this form came back. The fields
inside refill only when their own form came back; a `{form method='get'}` refills from the
query string. A posting form holding a file field gets `enctype="multipart/form-data"`
unless it names an enctype itself. With `error=`, a form that came back while `padValidate`
or the rules of its fields left an error starts with `<div class="error" role="alert">`
holding the message.

**Rules in the template:** the fields of a named form can carry their own rules -
`rules='required|email'` on `{input}` and `{textarea}`, the rules of `padValidate`. They are
read from the page's template before any PHP runs (`_inits.php` included), and a post of
the form is checked against them there: `padPosted('contact')` is TRUE only for a post that
kept them - the PHP that stores and redirects runs for a good post alone - and
`padFormFailed('contact')` for one that broke them, which renders the form again, refilled,
the messages beside the fields. Every field of the form with rules is checked, also one in
an `{if}` branch that did not render; a field that is there only sometimes keeps its check in
the PHP, where `padValidate` adds its messages to those of the template.

```html
{form 'contact', error='Please correct the errors below.'}
  {input 'email', type='email', label='E-mail', rules='required|email'/}
  {textarea 'message', label='Message', rows=6, rules='required|max:2000'/}
{/form}
```

Only the page's own template is read - with its `_inits.pad` and `_exits.pad` - and only what
is written out: quoted rules on a field with a quoted name, in a form with a quoted name. A
field with rules in an `_include` snippet, a custom tag, a `{page}` or an `{extends}` layout,
rules from a variable, rules outside a named form, in a form with `action=` or
`method='get'`, in one htmx sends elsewhere (`hx-post=` to another page, `hx-get=`, `hx-put=` ...)
or holding a button whose `formaction=` names another page, on a file field, a rule that does not exist and one field given two sets of
rules are errors, strict check or not - each would be rules nothing checks. A `|` in the
pattern of a `regex:` belongs to the pattern - `rules='required|regex:/^(S|M|L)$/'`. A rule with
braces writes them as `&open;` and `&close;`, and a backslash as `\\`: `rules='regex:/^\\d&open;4&close;$/'`. Custom
messages stay with `padValidate`'s third argument.

**In the browser too:** `client` on a named form whose fields have rules checks them in the
browser as well - the same rules, the same messages (the rules' scripts in `www/assets/rules/`, written into the
page once, behind the first such form). The rules go in a `data-pad-rules` attribute, the
`error=` text in `data-pad-error`, and the form gets `novalidate` - the browser's own checks
would stop the send first, with messages of their own; a field is checked when it is left, again while it is typed
in once it showed a message, and every field when the form is sent - a form that fails is not
sent, the first field with a message gets the focus. The messages stand where the server's
would, in a `<span class="error" id="<id>-error">` with `aria-invalid` and
`aria-describedby`. A field whose rules look at another - `same:password`,
`required_if:delivery,ship`, `after:start` - is checked again when that one changes, if it
shows a message or was left before: the message follows the other field. The server still checks every post. `client` on a form without a name, or
without a field with rules, is an error.

```html
{form 'signup', client, error}
  {input 'email', type='email', label='E-mail', rules='required|email'/}
{/form}
```

---

### validator
The browser's checker of `padValidate`'s rules, for a form a component draws.

```html
<div x-data="{ rules: {^rules}, form: {}, errors: {} }">
  <input x-model="form.email" @blur="errors = padValidate.check(rules, form)">
</div>
{validator/}
```

**Behavior:** Writes the checker - `www/assets/rules/validate.js` with the script of every rule of `pad/subs/rules/types/` - as one inline script - once a page, also when a
`{form ..., client}` brought it already - carrying the request's nonce when `$padCsp` asks
for one. The script defines `padValidate.check(rules, values)` (the message of every field
that broke a rule), `padValidate.field(spec, value, values)` and `padValidate.form(form)`;
`rules` is what `padValidateClient($rules)` exports in the page's PHP - the list
`padValidate($rules)` checks the post with.

---

### input
A form field that refills from what was posted and shows the error `padValidate` found for it.

```html
{input 'email', type='email', label='E-mail', required, placeholder='you@example.org'/}
{input 'terms', type='checkbox', label='I agree', value='yes'/}
```

**Items:** the field name first; `type=` (default `text`), `label=` (a `<label for>` before
the field, after a checkbox or radio), `value=` (the value before anything was posted),
`id=` (default: the name), `checked` (a checkbox's state before a post), `rules=` (its rules,
checked before the page's PHP - see `{form}`); every other item is an attribute of the field.

**Behavior:** After a post of its form the field shows the posted value, escaped - found
where PHP files the name, so `user[email]` is `$_POST['user']['email']` and `first.name`
is `first_name`, for its rules as well; a checkbox
or radio is checked when the posted value is its own; a password or file field is never
refilled. When `padValidate` reported the field, its message follows in
`<span class="error" id="<id>-error">`, worded with the field's label, and the field gets
`aria-invalid="true"` and `aria-describedby`. A file field shows the reason `padUpload`
refused its file the same way.

---

### textarea
`{input}`'s refill, label and error for a text area.

```html
{textarea 'message', label='Message', rows=6, required/}
{textarea 'message', label='Message', rows=6, rules='required|max:2000'/}
```

---

### flash
The flash messages for this request, one occurrence each.

```html
{flash}
  <p class="notice {$type}">{$message}</p>
{/flash}

{flash 'error'}<p class="error">{$message}</p>{/flash}
```

**Parameters:** optional type - only the messages of that type.

**Fields:** `message`, `type`. No message renders the `@else@` half.

**Behavior:** `padFlash($message, $type = 'info')` in the page's PHP keeps a message in the
session for the next request - typically before `padRedirect()`. A message lives exactly
one request after the one that flashed it, shown or not; one flashed during this very request
shows here too. Each is shown once. A `padFlash` cookie signals the next request, so pages
that never flash do not open the session.

---

### auth
The logged-in user of this application - `padLogin()` - its row as the tag's data.

```html
{auth}Hello {$name}{else}<a href="?login">Log in</a>{/auth}
```

**Behavior:** the content renders once for a logged-in user, with the user's row as its
data (`{$name}`, `{$email}` - the password and token fields were never kept); for a guest
the `{else}` / `@else@` half renders. `{guest}...{/guest}` is the reverse and hands over no
data. Both own their `{else}`: an `{if}` around them keeps its own.

---

### can
Content for a user a gate allows - `padGate()`, `padCan()` (lib/gate.php).

```html
{can 'edit-post', $post}<a href="?edit&id={$id}">Edit</a>{else}Read only{/can}
{posts}{cannot 'delete-post', $posts}locked{/cannot}{/posts}
```

**Parameters:** the ability, then the values the gate gets - an array passes whole; inside
a loop the loop's name is its current row. `{cannot}` is the reverse.

**Behavior:** `padGateBefore` hooks decide first, then the gate. A guest reaches only a gate
whose user parameter takes NULL. An ability nobody defined is FALSE and, under the strict
check, an error.

---

### feature
Content while a feature flag of `$padFeatures` is on for this visitor.

```html
{feature 'search2'}<form action="?search2">...</form>{else}<form action="?search">...</form>{/feature}
```

**Behavior:** `padFeature($name)` decides: `TRUE`, `FALSE`, a share between 0 and 1 of the
visitors - stable per user id, else per a long-lived `padFeatureId` cookie - or the name of
a function called with the user. `padFeatureOverride()` fixes one for a test. An unknown flag
is FALSE and, under the strict check, an error.

---

### oauth
A "Log in with ..." link styled as a button, for the OAuth subsystem (`pad/subs/oauth/`).

```html
{oauth 'github'/}
{oauth 'oidc', href='?login/company', label='Log in with your company account'/}
```

**Parameters:** the provider, a file of `pad/subs/oauth/types/` - apple, discord, forgejo,
github, gitlab, google, microsoft, oidc.

**Options:** `href` - where it links, `oauth/<provider>` of the application by default (a page
`oauth/[provider].php` calling `padOAuthStart` and `padOAuthUser`); `label` - its text instead of
`Log in with <name>`.

**Behavior:** the provider's icon when `pad/subs/icons/` has one of its name, else the log-in
icon; the style once per page, `--pad-oauth-*` custom properties. The button reads no setting -
the page it links to needs the client id and secret. An unknown provider is an error under the
strict check.

---

### notifications
The notifications the database channel of `padNotify` kept for the user logged in, unread,
newest first, each with a link that marks it read.

```html
{notifications/}
{notifications all}<li>{$title} - {$created}{if $read} (read){/if}</li>{/notifications}
```

**Options:** `for` - the recipient, an id or an email, instead of the user logged in; `all` - the
read ones too.

**Behavior:** single, a list in a `<section aria-label="Notifications">` - title, text, time and
a "Mark read" link - or "No new notifications."; nothing for a guest. As a pair the
notifications are its rows: `id`, `name`, `title`, `text`, `html`, `url`, `created` (Y-m-d H:i),
`read`, `link`. The link brings this page back with `padNotificationRead=<id>.<signature>`, an
HMAC of the application, the recipient and the id: it marks only that one of that person.

---

### asset
The address of a file of `www/<app>/` with a version of its contents, for a far-future cache.

```html
<link rel="stylesheet" href="{asset 'charts.css'/}">     <!-- /charts/charts.css?v=1f3a9c0b2e -->
{asset 'app.js', tag/}                                     <!-- <script src="..." defer></script> -->
```

**Parameters:** the file, relative to `www/<app>/` - it never leaves it. `tag` writes the
element: `<link rel="stylesheet">` for `.css`, `<script defer>` for `.js`, a module for
`.mjs`, with `nonce=` when `$padCsp` names `'nonce'`.

**Behavior:** `v=` is the first ten hex digits of a hash of the contents, made once per
request - a changed file gets a new address. A missing file is the plain address and, under
the strict check, an error. `padAsset()` / `padAssetTag()` from PHP.

---

### nonce
This request's Content-Security-Policy nonce.

```html
<script nonce="{nonce/}">{ignore}
  document.title = 'Hello';
{/ignore}</script>
```

**Behavior:** 18 random bytes, base64 - one value for the whole request, made on first use.
`'nonce'` (quotes included) in `$padCsp` becomes `'nonce-<the value>'` in the header, so
`$padCsp = "default-src 'self'; script-src 'self' 'nonce'"` lets exactly the scripts that
carry it run. The engine never adds it to script tags itself. Outside `{ignore}` - inside
one it is text. A page carrying a nonce is not stored in the page cache.

---

## File Operation Tags

### files
List files in a directory with filtering options.

```html
{files 'directory', mask='*.txt', recursive=TRUE}
  {$path} - {$file}
{/files}
```

**Parameters:**
- `dir` / first param: Directory path
- `mask`: File pattern (e.g., `*.txt`)
- `onlyFiles`: Only return files
- `onlyDirs`: Only return directories
- `recursive`: Include subdirectories
- `exclude`: Exclusion pattern
- `includeHidden`: Include hidden files
- `base`: what the directory is relative to - `app` (the application), `data` (DATA/), `pad`
  (the path as given); otherwise the filesystem root. Whatever the base, the directory must
  lie inside the applications, the engine or DATA

**Returns:** Array with `path`, `file`, `ext`, `item`, `dir` for each entry

---

### sitemap
The pages of the application, generated from the file tree - in PAD every page is a file, so
no route list is needed. One row per page, the index of a directory first, then its other
pages, then its subdirectories.

```html
<ul>
  {sitemap}<li><a href="{$url}">{$page}</a> {$lastmod}</li>{/sitemap}
</ul>

{sitemap 'docs'} ... {/sitemap}      {# the pages below one directory #}
```

**Parameters:**
- First parameter (optional): a directory of the application; the pages below it only

**Returns:** Rows with `page` (`docs/intro`), `url` (absolute, `?docs/intro` or the clean form
under `$padCleanUrls`; a directory's index is the directory) and `lastmod` (`Y-m-d`, the newest
of the page's files).

Left out: `_` and dot entries, a directory holding a `_guard.php`, a bracketed route
(`products/[id]`), a page with no template whose PHP only redirects, restarts or writes, a page
whose template says `{meta sitemap=false/}`, and every page or directory `$padSitemapSkip`
names. With `$padSitemap = TRUE` the same list answers `?sitemap.xml` (or `/app/sitemap.xml`)
as the sitemap protocol's XML, and `?robots.txt` points to it - when the application has no
page of that name.

---

### dir
Simple directory listing.

```html
{dir '/path/to/directory'}
```

**Parameters:**
- First parameter: Directory path

**Returns:** Array of filenames (via `scandir`)

---

### file
Write content to file.

```html
{file dir='path', name='filename', ext='txt'}
  File content
{/file}
```

**Parameters:**
- `dir`: Directory path
- `name`: Filename (default: 'file')
- `ext`: Extension (default: 'ext')
- `date`: Include date in filename
- `stamp`: Include timestamp
- `id`: Include unique ID

**Refused:** the file goes under `DATA/`; a name the web server would run (`.php`, `.phtml`,
`.phar` ... anywhere in it) or read as configuration (a part starting with a dot -
`.htaccess`, `.user.ini`) is a PAD error and nothing is written.

---

### exists
Check if file exists.

```html
{exists '/path/to/file'}
```

**Parameters:**
- First parameter: File path

**Returns:** TRUE if file exists, FALSE otherwise - and FALSE for a path outside the
applications, the engine, DATA and the public files of `www/`, and for an address
(`ftp://...`)

---

### open
Return opening brace character.

```html
{open/}
```

**Returns:** `&open;` (entity for `{`)

---

### close
Return closing brace character.

```html
{close/}
```

**Returns:** `&close;` (entity for `}`)

---

## HTTP and Network Tags

### curl
Make HTTP request.

```html
{curl 'http://example.com', post='name=x&id=1'/}
```

**Parameters:**
- `url` / first param: URL to request
- `post` - the body to post (the request is then a POST); `user` and `password` - basic
  authentication; `get`, `cookies`, `headers` and `options` (curl options by name) - arrays,
  as `padCurl()` takes them
- `$name=value` written on the tag are added to the URL as query values
- `SELF://` prefix replaced with current host
- `ttl=600`: keep the answer that many seconds (see below)

**Behavior:** Makes HTTP request; a result other than 200 is a PAD error. An address a
visitor chose - `{curl $v}` with a request value, the rule of `data=` (OPTIONS.md) - is refused:
write the address in the template, or fetch it in the page's PHP with `padCurl`

**Returns:** Response data

**Remote data with a cache:** with `ttl=` the answer is kept, and the requests within that
many seconds are answered from the copy instead of asking the source again. When the source
fails after the ttl - a failed transfer or any status but 2xx - the last good copy is served
and the failure goes to the error log (and to the application's `_events/curl.php`). Only a
good answer is kept; without a copy the failure is an error as before.

```html
{curl 'https://api.example.com/rates.json', ttl=600/}

{pad data='https://api.example.com/rates.json', ttl=600} {$code}: {$rate} {/pad}
```

Remote data takes the same ttl - from the tag, as above, or from a `_data/*.curl` file, which
holds the URL or a `<curl>` document:

```xml
<curl>
  <url>https://api.example.com/rates.json</url>
  <ttl>600</ttl>
</curl>
```

`$padCurlCache` picks the store - `'file'` (`DATA/cache/curl/`, the default), `'apcu'`,
`'redis'` or `'memcached'` (on the page cache's `$padCacheRedis*`/`$padCacheMemcached*`
connection settings) - or `FALSE` to fetch every time. `$padCurlStale` (default 86400) is how
many seconds beyond its ttl a copy is kept for a failing source. From PHP:
`padCurlCached ( $input, $ttl )` answers like `padCurl()` plus `['cache']` - `hit`, `miss`
or `stale` - and `padCurlForget ( $input )` drops one copy.

**Parallel fetching:** several sources of one page are fetched one after another by `data=`
and `{curl}`. `padPrefetch` in the page's PHP puts them on the wire together (curl_multi),
so the wait is the slowest answer rather than the sum, and keeps each answer as named data:

```php
padPrefetch ( [
  'rates'   => 'https://example.com/rates.json',
  'weather' => 'https://example.com/weather.json',
], 600 );
```

```html
{rates}{$code}: {$rate}{/rates}
```

A source is a URL (`SELF://` for this host) or an input array as `padCurl()` takes it; the
optional ttl works as `ttl=` above; a source that fails without a copy is an error, as a
failing `data=` is. The parsed sets are also returned, under the same names.

---

### mail
Render a PAD template to an email - an HTML part and a text part - and send it.

```html
{mail to=$email, template='order', subject='Order confirmation'/}

{mail to=$email, subject='Welcome'}
  <p>Hello {$name}</p>
{/mail}
```

**Parameters:**
- `to`: the address(es) - `ann@example.com`, `Ann <ann@example.com>`, several separated by commas
- `subject`: the subject
- `template`: a template in `_mail/`, looked up like `_include/` from the page's directory up to
  the application and `_common`: `order.pad` (or `.html`) the HTML part, `order.txt` the text
  part, `order.php` run first; `_inits.pad`/`_exits.pad` (and `.txt`) there are the layout,
  with `@page@`. Without a `.txt`, the text part is made from the HTML.
- `from`, `cc`, `bcc`, `replyTo`: the other headers (`from` defaults to `$padMailFrom`)

**Behavior:** As a pair without `template`, the content is the HTML part, rendered where it
stands - in a loop with the fields of the row. The tag shows nothing. The mail sees the page's
variables and leaves them as they were. `$padMailTransport` decides where it goes: `'file'`
(default) writes an `.eml` under `DATA/mail/<app>/`, keeping the newest `$padMailKeep`; `'mail'`
sends through PHP's `mail()`; a function name of the application gets the message array (`to`,
`cc`, `bcc`, `from`, `replyTo`, `subject`, `html`, `text`, `headers`, `raw`) and answers
whether it went. A line break in a header value, an address that is none, a missing subject or
template are errors. From PHP: `padMail ( $to, $template, $subject, $vars, $options )`;
`$padMailLast` holds the last message of the request.

---

### ai
The answer of a language model, printed as text (`pad/subs/ai/`).

```html
{ai provider='anthropic', ttl=86400}Summarise in one sentence: {$body}{/ai}
{ai 'Name three rivers of France.', system='Answer briefly.'/}
```

**Parameters:** single, the prompt.

**Options:** `provider` - a file of `pad/subs/ai/types/`: anthropic, gemini, mistral, ollama,
openai (`PAD_AI_PROVIDER`, else anthropic); `model` - the provider's default unless given;
`system` - the system prompt; `tokens` - the most the answer may take; `ttl` - the seconds an
answer is kept, 3600 unless given, 0 keeps nothing.

**Behavior:** as a pair the content renders first - the end walk - and what it rendered is the
prompt, its fields read as the text they hold. The answer is escaped and its braces inert: a
value, never template code. Answers are kept (`padCachePut`) under a hash of the provider,
model, system prompt, prompt and tokens - a failed answer never, and nothing while the fetches
are faked. The key is the provider's `padEnv` value - `ANTHROPIC_API_KEY`, `OPENAI_API_KEY`,
`MISTRAL_API_KEY`, `GEMINI_API_KEY`; Ollama needs none (`OLLAMA_HOST`). A missing key, an unknown
provider, a wrong option or an error of the API is an error under the strict check; without it
the tag answers nothing and the error is logged. From PHP: `padAi ( $prompt, $options )`,
`padAiError ()`.

---

## Output Tags

### echo
Evaluate and output expression.

```html
{echo $variable/}
{echo 5 + 3/}          → 8 - a quoted '5 + 3' is the text itself
```

**Parameters:**
- First parameter: Expression to evaluate

**Returns:** Evaluated result

---

### output
Set output type.

```html
{output 'download'/}
```

**Parameters:**
- First parameter: Output type (`web`, `console`, `file`, `download`, `json`, `csv`)

`json` and `csv` replace what the template rendered with the page's data: the variables its
`.php` names in `$padExpose`, as one JSON object, or the first of them that is a list as CSV
with a header row. The same answer comes without the tag for `?page&padFormat=json` (or `csv`)
and for an `Accept: application/json` (or `text/csv`) header - and then no template runs at all.

---

### flush
Send the page rendered so far to the browser now, instead of with the rest when the whole
page is done - so the browser fetches the stylesheets and fonts the `<head>` names while a slow
part of the page (a big `{table}`, a `{curl}`) is still rendering.

```html
<!-- _inits.pad -->
<html>
  <head><link rel="stylesheet" href="/css/site.css"></head>
  {flush/}
  <body>@page@</body>
</html>
```

**Behavior:** The first flush switches off, for the request, everything that needs the whole
body: tidy, gzip of the whole body, the ETag and its 304, `Content-Length` and the page cache;
the headers go out with the first part, so a header or cookie set after it is lost. It stands at
the top level of the page or its wrapper - inside another tag, or in a page whose PHP returns
data, strict mode reports it. In a page rendered inside another (`{page}`), for an output
type other than web, and in a request answered with a part of the page - a response fragment
(`padFragment`), the post of a `{live}` region - and in an application with an
`_events/output.php` hook, which sees the page whole, it does nothing. The page's PHP runs before any of the template, so what
a flush wins is the rendering below it. A `{stack}` in the part that goes out early gets the
pushes made before the flush only - a push below it cannot reach text the browser already has.
A `{toc}` lists the headings of the whole page, so a flush with one in the part it would send
sends nothing, nor does any flush after it: the toc goes with the rest of the page.

---

### tidy
Format/beautify HTML content.

```html
{tidy}
  <html>content</html>
{/tidy}
```

**Behavior:** Applies HTML tidying to content

---

### spaceless
Take out the whitespace between HTML tags, and at both ends.

```html
{spaceless}
  <ul>
    <li>a b</li>
  </ul>
{/spaceless}
```

**Result:** `<ul><li>a b</li></ul>` - text inside a tag keeps its spaces.

**Related:** a `~` just inside any tag's brace trims the whitespace on that side of it:
`{~tag}` what stands before, `{tag~}` and `{/tag~}` what follows - newlines included.

---

### markdown
Write Markdown as HTML.

```html
{markdown}
  ## Release notes for {$version}
  - **Faster** first render
  - see [the manual](?manual)
{/markdown}

{markdown $post.body/}
{markdown html} ... {/markdown}
{markdown ignore} ... code with braces ... {/markdown}
```

**Behavior:** The content renders first - its fields and tags resolve - and the result is read
as Markdown. With a parameter instead of a pair the value of the parameter is read, as the
`markdown` pipe does. The text is dedented first, so a block indented along with the template
is not a code block.

**Supported:** ATX and setext headings, paragraphs, `*em*` / `_em_`, `**strong**`, code spans,
fenced (with a language class) and indented code blocks, bullet and ordered lists (nested,
tight or loose, a start number), links, images, `<https://...>` autolinks, block quotes,
thematic breaks, hard line breaks and backslash escapes. Not supported: reference-style links,
tables, footnotes.

**Safety:** raw HTML in the text is escaped, and a link or image whose URL names a scheme other
than http, https, mailto, ftp or tel (`javascript:`, `data:` ...) keeps only its text. An
existing entity is left alone, the way the sanitize chain does.

| Option | Description |
|--------|-------------|
| `html` | Let the author's own raw HTML through: inline tags, and a block-level element or comment at the start of a line up to the next blank line |
| `ignore` | Braces in the content are text, not tags - for code samples |

**Values are text:** the HTML made from a value stays a value - `{php:getcwd}` in a post is
shown, not run. See also the `markdown` pipe in FUNCTIONS.md.

---

### chart
Draw a chart as inline SVG - no JavaScript, so it works in print and caches like any output;
with `client` the browser draws the same SVG from the rows the server hands it.

```html
{chart 'bar', data='sales', label='month', value='amount'/}
{chart 'line', data='visits', value='count', title='Visits this week'/}
{chart 'bar', sequence='fibonacci', rows=12/}
{chart 'bar', data='sales', label='month', value='online, shop', stacked/}
{chart 'pie', data='budget', label='post', value='amount'/}
{chart 'scatter', data='cars', x='weight', y='mpg', color='origin', trend/}
{chart 'heatmap', data='visits', x='hour', y='day', value='count'/}
{chart 'sankey', data='energy', source='from', target='to', value='amount'/}
{chart 'network', data='friends', source='from', target='to', layout='radial'/}
{chart 'treemap', data='spend', levels='dept', label='post', value='amount'/}
{chart 'gauge', value=72, label='Disk in use', unit='%', bands='60, 85', target=80/}
{chart 'radar', data='phones', label='model', value='battery, camera, screen, speed'/}
{chart 'waterfall', data='result', label='step', value='amount', total='Profit'/}
{chart 'histogram', data='people', value='height', bins=12/}
{chart 'boxplot', data='salaries', label='team', value='salary'/}
{chart 'calendar', data='commits', date='day', value='count', year=2026/}
{chart 'gantt', data='plan', label='task', from='begin', to='until', progress='done'/}
{chart 'area', data='energy', label='year', value='coal, gas, wind, solar', stacked/}
{chart 'stream', data='listening', label='month', value='pop, rock, jazz'/}
{chart 'multiples', data='climate', by='city', label='month', value='temperature'/}
{chart 'candlestick', data='share', label='day', volume='volume'/}
{chart 'dual', data='results', label='month', value='revenue', line='margin'/}
{chart 'dumbbell', data='scores', label='team', from='before', to='after', names='2025, 2026', ranked/}
{chart 'errorbar', data='trial', label='dose', value='mean', low='lower', high='upper', interval='95% CI'/}
{chart 'spiral', data='sales', label='month', value='amount', turn='year'/}
{chart 'rose', data='deaths', label='month', value='disease, wounds, other'/}
{chart 'radialbar', data='funds', label='class', value='raised', to=5000/}
{chart 'funnel', data='shop', label='step', value='visitors'/}
{chart 'pyramid', data='people', label='age', value='men, women'/}
{chart 'waffle', data='energy', label='use', value='kwh', cells=100/}
{chart 'marimekko', data='market', label='region', value='alpha, beta, gamma, other'/}
{chart 'parliament', data='election', label='party', value='seats'/}
{chart 'venn', data='speakers', sets='speaks', value='students'/}
{chart 'density', data='trials', label='group', value='ms'/}
{chart 'violin', data='scores', label='class', value='score'/}
{chart 'bullet', data='kpis', label='kpi', value='actual', target='goal', bands='50, 75'/}
{chart 'rings', data='today', label='goal', value='done', to='target'/}
{chart 'parallel', data='cars', value='mpg, cylinders, horsepower, weight', color='origin'/}
{chart 'proportional', data='emissions', label='country', value='mt'/}
{chart 'chord', data='migration', source='from', target='to', value='people'/}
{chart 'arc', data='coauthors', source='author', target='with', value='papers'/}
{chart 'pack', data='files', levels='folder', label='file', value='kb'/}
{chart 'orgchart', data='staff', id='id', parent='boss', label='name', sub='role'/}
```

**Kinds:**

A kind is a file of `pad/subs/charts/types/` - its default size and the function that draws it -
and the files there are the kinds there are; a kind is added by adding its file. The parameter
names it, or the chart type does: `{chart:pie data='budget'/}` is `{chart 'pie', data='budget'/}`,
and so is the bare `{pie data='budget'/}` when no tag, store, field or sequence has the name.
`{sparkline/}` is the sparkline kind. The reference application lists the kinds, each with the
pages that draw it.

| Kind | Draws | Its options |
|------|-------|-------------|
| `bar`, `column` | Columns from a zero baseline; several `value` fields side by side, or one on the other with `stacked` | `label`, `value`, `stacked` |
| `hbar` | The same, lying down, the names on the left | `label`, `value`, `stacked` |
| `line` | A line over a light wash, the last point marked; several `value` fields a line each, named at the end | `label`, `value` |
| `pie`, `donut` | Parts of a whole - positive values only, past eight the smallest folded into a grey 'Other', a legend with value and share; a donut shows the total in its middle | `label`, `value` |
| `scatter` | A dot per row on two numeric axes; `color` colours by a field (eight groups, the rest grey), `trend` adds the least-squares line | `x`, `y`, `label`, `color`, `trend` |
| `bubble` | A scatter whose dots have the area of `size` | `x`, `y`, `size`, `label`, `color`, `trend` |
| `heatmap` | A grid of the categories `x` (columns) and `y` (rows), each cell one of seven steps of one hue; rows with the same cell add up | `x`, `y`, `value` |
| `sankey` | Flows from `source` to `target` as wide as `value`, in columns along the longest path; flows that go round in a circle are an error | `source`, `target`, `value` |
| `network` | Nodes and the links between them; `layout='force'` (default, the same picture for the same data; past 300 nodes they are set on a circle, which the description says) or `'radial'`, `arrows` for the direction, `value` for thicker links | `source`, `target`, `value`, `layout`, `arrows` |
| `treemap` | Nested rectangles (squarified), `levels` the fields above the parts, outermost first | `label`, `value`, `levels` |
| `sunburst` | The same hierarchy as rings around the total | `label`, `value`, `levels` |
| `gauge` | One number on a half ring from `from` (0) to `to` (100); `bands` - one or two thresholds - colour the track green, amber, red; `target` marks a value; `unit` follows the number. `value` is the number itself, or the field of the first row - so a gauge needs no data | `value`, `from`, `to`, `bands`, `target`, `label`, `unit` |
| `radar` | A spoke per measure of `value` (three or more), a polygon per row named by `label` (eight at most), rings from 0 to `to` - else round numbers past the highest value | `label`, `value`, `to` |
| `waterfall` | Each row a step up (green) or down (red) floating from where the step before ended, a closing bar named by `total` ('Total'; `total=''` none), dashed lines carrying the level, the change above each bar | `label`, `value`, `total` |
| `histogram` | The numbers of `value` counted in about `bins` (10) bins of one round width - a whole width for whole numbers; a value on an edge in the bin above | `value`, `bins` |
| `boxplot` | A box per group of `label` - all values in one without it - from the first to the third quartile, the median across, whiskers to the furthest values within 1.5 box heights, the values past them as dots | `label`, `value` |
| `calendar` | A square per day, a column per week (Monday first), the day's sum of `value` - else a count of its rows - in seven steps, an empty day in the grid colour; from the Monday before the first `date` to the last, or the whole `year` | `date`, `value`, `year` |
| `gantt` | A row per task (`label`), a bar from `from` to `to` - dates, or plain numbers when all are; `color` colours by a field with a legend, `progress` (0-100) draws the part done full and the rest light, `mark` a dashed line at a date | `label`, `from`, `to`, `color`, `progress`, `mark` |
| `area` | Filled areas over the `label` axis, a line on each and a legend for several `value` fields; `stacked` lays them on top of each other (the top edge the running total), `percent` stacks every column to 100% - no negative values when stacked; a column per label carries all values in its tooltip | `label`, `value`, `stacked`, `percent` |
| `stream` | A streamgraph: the `value` fields stacked in smooth curves around a wandering middle (the wiggle offset, early peaks inside), no value axis, the labels below and a legend above | `label`, `value` |
| `multiples` | Small multiples: a small line over a wash per group of `by`, all on one scale that takes in zero and the same x positions, in a grid of `columns` (default: the count that gives the best panel shape); the ticks left of the first column, the first and last label under the bottom row | `by`, `label`, `value`, `columns` |
| `candlestick` | A candle per row (`label` the day): the body from `open` to `close`, the wick from `low` to `high` - green when it closed at or above its open, red when lower; `volume` adds light bars in the lower fifth on a scale of their own; the price axis needs no zero | `label`, `open`, `high`, `low`, `close`, `volume` |
| `dual` | Two measures on two axes: `value` as bars on the left axis, `line` as a line with ringed points on the right one, each axis's ticks in its series' colour and the right ticks on the left's grid lines when their zeros can line up; a legend names both | `label`, `value`, `line` |
| `dumbbell` | Two values per row joined by a line: a row per category (`label`), a dot at `from` in the first colour and one at `to` in the second, a legend naming the two ends - `names` (`'2025, 2026'`), else the fields; all rows on one horizontal scale of round numbers, no zero needed - a dot is read by its place - and the zero line drawn stronger when zero lies inside, so negative values read as any other; `ranked` orders the rows by their change, the largest rise first; a row with one end draws that dot alone, a row with neither is left out; the `<desc>` and the line's tooltip say both values and the signed change (+3, -2). A `from` or `to` that no row has is an error | `label`, `from`, `to`, `names`, `ranked` |
| `errorbar` | Estimates with the interval around them, already computed - nothing is computed here, no mean, deviation or confidence level: a point per row at `value` and an upright whisker with caps from `low` to `high` (else the fields named low and high), the categories (`label`) along the bottom, one vertical scale of round numbers taking in every point and whisker drawn, no zero needed and the zero line stronger when zero lies inside; `interval` names what the whisker is - `'95% CI'`, `'min-max'` - in the legend, the title and the `<desc>`, never made up: without it the legend names the bound fields. A row without both bounds draws its point alone. Under the strict check a low above its high is an error naming the row (without it that whisker is left out), and so is a `low` or `high` that no row has | `label`, `value`, `low`, `high`, `interval` |
| `spiral` | A long series wound clockwise from twelve o'clock along an Archimedean spiral, `period` rows (12) to a turn; each row a segment of the band in seven steps of one hue, the scale below; the `label`s of the first turn around the outside, `turn` names each turn where it begins | `label`, `value`, `period`, `turn` |
| `rose` | Nightingale's polar area chart: a wedge per row of one angle, the first centred on twelve o'clock, its area its value (radius by the square root); several `value` fields lie outward as rings, a colour each with a legend; names around the circle, rings of round numbers | `label`, `value` |
| `radialbar` | A ring per row, the first outside, an arc from twelve o'clock over three quarters of the circle as far as its value against `to` - else round numbers past the highest value - on a light track of its colour; the name at the start of the track, the value at the end of the arc | `label`, `value`, `to` |
| `funnel` | The rows top to bottom as centred bars as wide as their value against the largest, joined by light bands; the value in the bar, the share of the first step on the right, the share of the step before in the band | `label`, `value` |
| `pyramid` | One `value` field: a triangle in layers, the first row at the apex, each layer's area its value, named beside it with value and share. Two fields (`value='men, women'`): a population pyramid - the first to the left, the second to the right of a middle column naming the rows, the first row at the bottom, mirrored ticks below, a legend | `label`, `value` |
| `waffle` | A grid of `cells` squares (100, a 10 x 10 grid; 4 to 2500), each part its share in its colour by the largest remainder so they add up exactly, filled row by row from the top left; a legend with value and share; past eight parts the smallest folded into a grey 'Other' | `label`, `value`, `cells` |
| `marimekko` | A column per row as wide as its total of the `value` fields (two or more - else every numeric field), each field stacked as its share of the column to 100%, the percentage in each part with room; a 0-100% axis on the left, names and totals below, a legend | `label`, `value` |
| `parliament` | A dot per seat in rows of half circles, the parties filling wedges from left to right in the order of the rows; the total and the seats a majority needs in the middle, a legend with the seats; whole seats, 5000 at most | `label`, `value` |
| `venn` | Two or three sets as circles, each one's area its size, the overlaps matched (two exactly, three pair by pair); a row names a set or an overlap in `sets` ('A', 'A&B' or 'A, B'), and a set's size counts its overlaps; light fills, names outside, the count of each region where it has room | `sets`, `value` |
| `density` | A smooth curve per group of `label` (Gaussian kernel density, `bandwidth` or else Silverman's rule) over one value axis - a light wash, its line, the median dashed, a legend for several groups | `label`, `value`, `bandwidth` |
| `violin` | A shape per group of `label` on a vertical value axis: the density mirrored around its middle, cut at the lowest and highest value, all groups on one scale - the quartiles as a thick bar and the median as a light dot inside | `label`, `value`, `bandwidth` |
| `bullet` | A track per row with a thin bar of `value` and `target` (a field, or one number) as a short upright line; `bands` - up to four, a number a percentage of the scale, a name a field holding the threshold - split the track into grey steps; `to` ends the scale (a number or a field), else round numbers past the row's highest | `label`, `value`, `target`, `bands`, `to` |
| `rings` | Concentric progress rings, one per row (eight at most, the first outermost): an arc of `value` against `to` - a number (100) or a field of the row - clockwise from twelve over a light track, full past the goal; a legend with value, goal and share | `label`, `value`, `to` |
| `parallel` | Parallel coordinates: an upright axis per measure of `value` (two or more, else every numeric field), each scaled to its own round range, named on top; a line per row, coloured by `color` with a legend or else one lighter colour | `label`, `value`, `color` |
| `proportional` | A circle per row - a square with `square` - whose area is `value`, side by side on a common baseline, wrapping into lines, the value and `label` under each | `label`, `value`, `square` |
| `chord` | Flows between the members of one group: a node an arc around the circle as long as all that flows out of and into it, a ribbon per flow through the middle as wide as the flow, in the colour of its source; without `value` a row counts one | `source`, `target`, `value` |
| `arc` | Nodes on a line in the order they first appear, a dot as large as its number of links, its name under it (turned when names would touch); a link a half circle above the line, `value` for thicker links | `source`, `target`, `value` |
| `pack` | The treemap's hierarchy as circles in circles: a part a circle with the area of its value, the parts of a group packed largest first inside a light circle, coloured by the outermost group with a legend | `label`, `value`, `levels` |
| `orgchart` | A tree top down: a box per row named by `label` (a second line from `sub`), under the row whose `id` its `parent` holds; rows without a known parent are roots side by side, each branch under the top its own colour; an id twice or a circle is an error | `id`, `parent`, `label`, `sub` |
| `sparkline` | See below | `label`, `value` |

**As a pair** the content between the tags is the data, when neither `data` nor `sequence`
names it - JSON, YAML, XML or CSV, recognised on sight as `{data}` recognises it (`type=`
names it outright). The content is read before the level walks it, so the braces of JSON need
no `{ignore}`, and the indent it shares under the tag is taken off:

```html
{chart 'bar', label='month', value='amount'}
  month,amount
  Jan,12400
  Feb,9500
{/chart}

{sparkline}
  [ 3, 1, 4, 1, 5, 9 ]
{/sparkline}
```

A field option left out takes the first field of the first row that fits - a numeric one for
a number, another for a name. A kind reads only its own options, so an option of another kind
is one that nothing reads under the strict check. The handling options (`row`, `group`, `sort`,
...) act on every tag's data, so the chart's own options are named apart from them.

| Option | Description |
|--------|-------------|
| `data` | The rows: a `{data}` store, a sequence store, the page's array or a `_data` file of that name, or a literal (`data='[3,1,4]'`); without it, the content of a pair |
| `type` | For a pair: the format of its content - `json`, `yaml`, `xml`, `csv` - when it is not to be recognised on sight |
| `sequence` | Plot the first `rows` terms (default 10) of a sequence type instead |
| `value` | The field that holds the number - by default the first numeric field; a comma list draws a series each (eight at most) |
| `label` | The field for the category axis - by default the first other field, else the row number |
| `title` | The accessible name; by default made from value and label |
| `width`, `height` | The size, default 600 x 300 (pie, donut, sunburst, rose, radialbar, rings, parliament, venn, waffle 480 x 280; sankey, network, treemap, radar, chord, pack, orgchart, parallel, multiples 600 x 400; gauge 320 x 200; calendar 720 x 150; spiral 480 x 420); `.pad-chart { max-width: 100%; height: auto }` in the page's CSS makes it shrink with its container |
| `client` | Drawn in the browser: the server writes the rows and the options as JSON and a script draws the same SVG there (`www/assets/charts/`) - no SVG drawn on the server |

**Accessibility:** `role="img"`, labelled by a `<title>` and a `<desc>` that lists the values;
each bar, point, slice, cell, flow and node has its own `<title>`, the tooltip on hover - a
sparkline has the chart's one `<title>` only. A row without a number is
left out; a chart without points writes nothing.

**Colours:** CSS custom properties `--pad-chart-series`, `--pad-chart-text`,
`--pad-chart-grid` and `--pad-chart-surface`, overridden on `.pad-chart`; the kinds with
several colours add the categorical slots `--pad-chart-1` (the series colour) to
`--pad-chart-8` in a fixed order, `--pad-chart-other` for what is folded past them, and the
heatmap's ramp `--pad-chart-heat-0` to `--pad-chart-heat-6`. The defaults follow
the page's `color-scheme` (light-dark()), so a page that declares `color-scheme: light dark`
gets the dark steps in dark mode.

---

### sparkline
A word-sized line chart: no axes, the last point marked.

```html
Visits {sparkline data='visits', value='count'/}
{sparkline sequence='fibonacci', rows=12/}
```

**Behavior:** `{chart 'sparkline', ...}` by another name, with the same options; 120 x 32 by
default, scaled to its own minimum and maximum.

---

### qr
A QR code as inline SVG - encoded on the server (`subs/codes/_lib/qr.php`), no image library, no remote
service, no JavaScript. The `qr` symbology of the codes subsystem by a tag of its own: the same
code as `{barcode:qr 'text'/}` and `{barcode 'text', type='qr'/}`.

```html
{qr 'https://example.com/order/42'/}
{qr $url, size=200, level='H', title='Open the order on your phone'/}
{qr 'WIFI:T:WPA;S:Office;P:secret;;', color='#2e7d32'/}
```

| Option | Description |
|--------|-------------|
| first parameter | The text to encode |
| `size` | Width and height in pixels, default 160 - the quiet zone of four modules included |
| `level` | Error correction: `L` (7% of the symbol may be lost), `M` (15%, default), `Q` (25%), `H` (30%) |
| `title` | The accessible name, by default the text itself |
| `color`, `background` | The dark and the light colour, default `#000` on `#fff` - fixed, whatever the page's colour scheme, since a reader needs the contrast |

**Behavior:** the text is encoded in one mode - numeric for digits only, alphanumeric for
upper case, digits and ` $%*+-./:`, else the bytes of its UTF-8 - in the smallest of the 40
versions that holds it at the level asked, under the mask with the lowest penalty. A text
too long for version 40 (2,953 bytes at `L`) or an unknown level is an error under the strict
check, and writes nothing.

---

### barcode
A barcode as inline SVG, encoded on the server by the codes subsystem (`pad/subs/codes/`): a
symbology is a file of `pad/subs/codes/types/` - `ean13`, `ean8`, `upca`, `upce`, `code128`,
`code39`, `code93`, `itf`, `itf14`, `codabar`, `msi`, `qr` and `datamatrix` - and `type=` names
it. The barcode type writes a symbology by its name, `{barcode:upce '0425261'/}`, and so does
the name alone, `{itf14 '1540014128876'/}`, when no tag, store, field or sequence has it.

```html
{barcode '871234567890'/}                          <!-- EAN-13, the check digit added -->
{barcode $parcel, type='code128', height=70/}
{barcode '03600029145', type='upca'/}
{barcode 'PAD', scale=3, plain, color='#2e7d32'/}
{barcode:upce '04210000526'/}                      <!-- a UPC-A in its short form: 04252614 -->
{code39 'PAD-2026', check/}
{datamatrix 'PAD-2026-0042', size=120/}
```

| Option | Description |
|--------|-------------|
| first parameter | The number or text |
| `type` | The symbology, a file of `pad/subs/codes/types/`; left out, twelve or thirteen digits are an EAN-13 and anything else Code 128 |
| `height` | The height of the bars in pixels, default 60 |
| `scale` | The width of one module (the thinnest bar) in pixels, default 2 |
| `title` | The accessible name, by default the code |
| `plain` | Bars only - no digits under them |
| `color`, `background` | Default `#000` on `#fff` |
| `check` | Bare, `code39`: the check symbol of modulo 43; `itf`: the check digit of weights 3 and 1. `msi`: `mod10` (default), `mod1010` or `none` |
| `size` | `qr` and `datamatrix`: the width and height in pixels, default 160 - in place of `height`, `scale` and `plain` |
| `level` | `qr`: the error correction, as `{qr}` takes it |

**Behavior:** an EAN or UPC number is given with or without its check digit - left off, it
is added; given, it is checked. EAN and UPC draw their guard bars longer and set the digits
in their groups; `upce` takes six digits, seven with the number system (0 or 1) in front or
eight with the check digit - that of the UPC-A it stands for - and writes the 11 or 12 digits
of a UPC-A in its short form when it has one. Code 128 takes printable ASCII and packs a run of
four digits or more two to a symbol (set C); Code 93 takes printable ASCII, the characters
Code 39 lacks as shift pairs, with its two check symbols; Code 39 digits, capital letters,
space and `- . $ / + %`. `itf` is Interleaved 2 of 5 (a 0 in front of an odd number of
digits), `itf14` the GS1 carton number between bearer bars, `codabar` digits and `- $ : / . +`
between start and stop letters A to D (A at both ends when the text has none), `msi` digits
with a Luhn check digit. `datamatrix` is Data Matrix ECC 200 in the smallest of its 24 square
sizes, 10 by 10 to 144 by 144 modules, a text beyond ASCII marked as UTF-8 by its ECI
designator; `qr` the QR code of `{qr}`. A wrong check digit, a wrong
length, a character the symbology cannot hold, a text too long for it or an unknown type is an
error under the strict check, and writes nothing.

---

### calendar
A month as a calendar, with its events (`tags/_lib/calendar.php`).

```html
{calendar}                                                   <!-- this month -->
{calendar '2026-10', data='agenda', date='when', title='what', link='url'/}
{calendar data='agenda'}
  <tr>{days}<td class="{$class}">{$day}{events}<b>{$what}</b>{/events}</td>{/days}</tr>
{/calendar}
```

| Option | Description |
|--------|-------------|
| first parameter | The month, `2026-10`; else the request value named by `query`, else this month (`padNow`, so `padNowFreeze` holds it) |
| `data` | The events: a `{data}` store, a page array or a `_data` file, as for `{chart}` |
| `date` | The field with an event's date - by default the first field that reads as one |
| `title` | The field with an event's text - by default the first other text field |
| `link` | A field with a link for the event - http(s), a page or an anchor; never `javascript:` or `data:` |
| `query` | The request value the links to the months around set, default `month` |
| `sunday` | Weeks start on Sunday instead of Monday |
| `holidays` | A country's public holidays marked - `'nl'`, or with its region `'de-by'` (see `{holidays}`): the day's class gets `holiday` (`observance` for a day that is no day off) and its name stands under the number - the local name when the request's locale speaks the country's language; the pair form's days get `$holiday`, the name or `''` - only with `holidays`, so without it a page's own `$holiday` is read there |

**Behavior:** as a single tag it answers a table - the month in its caption between links to
the month before and after (keeping the other request values, as `{pager}` does; none when the
first parameter fixes the month, unless it holds the month the request asked for - `{calendar $month}`), a column
per weekday, a cell per day with its `<time>` and its events as a list; today has
`aria-current="date"`. A small default look rides along under `:where()`, so the page's CSS
wins. As a pair it hands over the weeks as rows - `$week` (ISO number) and `days` - and
every day is a row too: `$date`, `$day`, `$weekday`, `$month`, `$other` (outside the
month), `$today`, `$weekend`, `$count`, `$class` (`other today weekend events`, as they
apply) and `events`, the data rows of that date. A store named `events` hides the day's
field of that name - `{level:events}` reaches it then.

---

### holidays
The public holidays of a country, from the rules of its file in `pad/subs/holidays/types/`.

```html
{holidays 'nl', year=2026}{$date} {$name} ({$local}){/holidays}
{holidays 'de-by', year=2026/}                             <!-- a table -->
{holidays 'gb-sct', from='today', to='+6 months', where='$type eq "public"'}...{/holidays}
```

| Option | Description |
|--------|-------------|
| first parameter | The country, its ISO 3166-1 alpha-2 code in any case - at, be, ca, ch, cz, de, dk, es, fi, fr, gb, ie, it, lu, nl, no, nz, pl, pt, se, sk, us - with a region after it as ISO 3166-2 writes it, `'de-by'` |
| `region` | The region apart: `{holidays 'de', region='by'}` - Germany's states, the United Kingdom's nations (`sct`, `nir`; `eng` and `wls` are the default), France's `57`, `67`, `68`, `971`-`976`, Finland's Åland (`01`), Portugal's Azores (`20`) and Madeira (`30`), New Zealand's provinces by their region's code (`auk`, `can`, `cit`, `hkb`, `mbh`, `nsn`, `ota`, `stl`, `tki`, `wgn`, `wtc`) |
| `year` | The year, 1583 to 9999 - default this year (`padNow`); a country whose file names the years it knows (New Zealand 2010 to 2052) only those |
| `from`, `to` | The days between two dates instead of a year - any date `padDateParse` reads, `today`, `+3 months`; one alone is a year from it (or from today); `to` before `from` is an error under the strict check |
| `lang` | The language of `local` for a country of more than one - be `nl fr de`, lu `lb fr de`, ch `de fr it`, ca `en fr`, fi `fi sv`; the first by default |

**Behavior:** as a pair it hands over the rows, sorted by date: `$date` (YYYY-MM-DD), `$name`
(English), `$local`, `$type` (`public`, a day off; `observance`, a national day that is none),
`$region` (the region of a regional holiday, else `''`) and `$substitute` (TRUE for the day
that makes up for one on a weekend - the United Kingdom's next free weekday, the United
States' Friday before or Monday after); the handling options work on them, an empty year is
the `@else@`. As a single tag it answers a table: the date in the request's locale, the
English name with the local one beside it, an observance marked "(no day off)". A country
without a file, a region it has not, a year out of range or a `lang` it has no names in is an
error under the strict check, and so is a year its file does not know. The helpers `padHolidays`, `padIsHoliday`, `padWorkdays` and
`padAddWorkdays` give the same to a page's PHP.

---

### highlight
Source code coloured on the server (`lib/highlight.php`) - no JavaScript library.

```html
{highlight 'php', lines, mark='3, 5-7'}
  <?php echo padArrSum ( $orders, 'total' ); ?>
{/highlight}
{highlight file='_data/products.json'/}
{echo $query | highlight('sql')/}
```

| Option | Description |
|--------|-------------|
| first parameter | The language: `pad`, `php`, `html` (`xml`, `svg`), `css`, `js` (`ts`, `json5`), `json`, `yaml`, `sql`, `bash` (`sh`), or `text` |
| `file` | A file of the application to show instead of the content - never one outside it, under `_config/` or a dotfile; the language defaults to its extension |
| `lines` | Number the lines |
| `mark` | Lines to set apart: `'4'`, `'3, 5-7'` |

**Behavior:** the content is taken as it stands, before the level walks it - the braces of
PHP, CSS, JSON or PAD itself need no `{ignore}` - with the indent its lines share taken off.
A `{# comment #}` is gone before any tag sees it, so a PAD sample keeps none. Every piece is
HTML-escaped and wrapped in `<span class="hl-...">` - `com`, `str`, `num`, `lit`, `kwd`,
`key`, `var`, `tag`, `att`, `opt`, `brc`, `htm`, `fn`, `pun`, `def` - inside
`<pre class="pad-highlight" data-lang="..."><code>`. The default colours follow the page's
`color-scheme` (light-dark()) under `:where()`, so the page's CSS wins. The `highlight`
pipe does the same for a value, and like `markdown` it skips the sanitize chain of a field
tag, since its output is escaped already. `padHighlightTokens ( $text, $lang )` gives PHP
the coloured text alone.

---

### vite
The scripts and stylesheets of a Vite build, linked by the name of their source.

```html
{vite 'src/main.js'/}                       <!-- the entry's CSS, its chunks preloaded, the script -->
{vite 'src/main.jsx', 'src/admin.js', react/}
```

| Option | Description |
|--------|-------------|
| parameters | The entries, as `build.rollupOptions.input` names them |
| `react` | With the dev server: the preamble the React plugin needs for fast refresh, first |

**Behavior:** in production the manifest Vite writes - `www/<app>/<$padViteBuild>/.vite/manifest.json`
(`build.manifest: true`, `build.outDir` that directory) - gives per entry a
`<link rel="stylesheet">` for its CSS and the CSS of every chunk it imports, a
`<link rel="modulepreload">` for every such chunk, and its `<script type="module">`; a dynamic
import is left to load when asked for. The addresses are `$padRoot`, the application and
`$padViteBuild` (default `build`) - the build's `base`. While the dev server runs - `$padViteDev`
names it, or the file `hot` in the build directory holds its address (a `configureServer` hook
writes it at start and removes it at stop) - the tags point at the dev server instead:
`@vite/client`, then the sources as they are, for hot module replacement. A file goes out once a
page, however many `{vite}` tags name it; a script carries the request's nonce when `$padCsp`
asks for one. An entry the manifest does not have, and a build without a manifest while no dev
server runs, are errors.

---

### shadow
The shadow root of a custom element, rendered on the server - declarative shadow DOM.

```html
<pad-card>
  {shadow css='www:card.css'}
    <header><slot name="title"></slot></header>
    <slot></slot>
  {/shadow}
  <h2 slot="title">{$title}</h2>
</pad-card>
```

| Option | Description |
|--------|-------------|
| `css` | Stylesheets inlined as one `<style>` at the start of the root, a text with commas: a `.css` file of the application, or `www:` one in its `www/` directory |
| `mode` | `open` (default) or `closed` |
| `focus` | `shadowrootdelegatesfocus` |

**Behavior:** writes `<template shadowrootmode="open">` round the rendered content. The browser
builds the shadow root from it while it parses the page, so the element shows - encapsulated
styles, slotted light DOM - before any script ran, and a module that defines the element later
finds the root in `this.shadowRoot`. A shadow root takes no styles from the page, and a
`<style>` written in a template would have its braces read as PAD tags: `css=` inlines the files
instead, each read once a request, a `</style` in one written so it cannot end the element. The
file rule of `{source}` applies; a file that is not there or is no `.css` file, and a mode that
is neither open nor closed, are errors. Inside the pair `{#name}` is `{shadow}`'s own parameter -
in a custom tag, `{set $value = {#value}/}` before the pair carries the tag's into it.

---

### source
The files behind a page as tabs, each coloured as `{highlight}` colours it - the code shown
is read from the files that run, so it never drifts from them.

```html
{source}                                          the page asked for: its template and PHP
{source 'examples/props', files='www:props.js'}   another page, then a file in www/<app>/
{source only='_tags/card.pad, www:card.js'}       those files alone
```

| Option | Description |
|--------|-------------|
| first parameter | The page whose `.pad` (or `.html`) and `.php` come first; default the page asked for |
| `files` | Files after the page's, a text with commas: a file of the application, or `www:` one in its `www/` directory |
| `only` | Those files alone, without the page's |

**Behavior:** a radio button and a label per file, then a panel per file: the tabs work by
CSS alone, no script. A label carries `data-side="server"` for a file of the application and
`"client"` for one under `www/` - what runs on the server, what the browser gets. The rule of
`{highlight file=}`: never a file outside the application or its `www/` directory, under
`_config/` or a dotfile - such a file, one that is not there and a page that has neither a
template nor PHP are errors. The default look - tabs, panels - is written once a page under
`:where()`, so the page's CSS wins; style `.pad-source`, `.pad-source > label` and
`.pad-source-panel`.

---

### ignore
Escape PAD syntax in content.

```html
{ignore}
  {this is not processed}
{/ignore}
```

**Behavior:** Escapes content so PAD tags are not processed

---

### attrs
Write HTML attributes - quoted, escaped, the false ones left out.

```html
<button {attrs disabled=$busy, title=$help, aria-expanded=$open/}>Save</button>
```

**Items:** `name=expression`, a bare `name` (written as a bare attribute), or an expression
whose array value adds its keys as attributes - the keys are data, so one that is no
attribute name, an event handler (`on...`), a script library's directive in any spelling
(`@`, `:`, `x-`, `v-`, `hx-`, `data-hx-`, `ng-`, `ng:`, `data-ng-`, `wire:`, `_`, `script`,
`data-script`, `data-bind`), an iframe's `srcdoc`, and a URL attribute (`href`, `src`, `action` ...) whose value - an array
joined - names a scheme that runs script (`javascript:`, `vbscript:`, `data:` other than
`data:image/`) are left out; `sms:`, `tel:`, a `data:image/` source and the like are kept.
Names are HTML attribute names, dashes included (`data-id`, `aria-label`); `content=` or
`print=` are attributes here, not options.

**Behavior:** A boolean HTML attribute (`disabled`, `checked`, `selected`, `required`,
`hidden`, ...) is written bare when its value is true and left out otherwise. Any other
attribute is left out for FALSE, written bare for TRUE, and otherwise written with its
value - the text `false` stays `aria-expanded="false"`; an empty value is an empty attribute.

---

### classes
Build a class list from fixed and conditional names.

```html
<div class="{classes 'panel', active=$isActive, invalid=$hasErrors/}">
```

**Items:** an expression - a string of one or more names, or an array of them - is always
in; `name=condition` puts that name in when the condition holds. The result is deduplicated
and escaped.

---

### trans
A key of the `_lang/` catalogs, in the request's locale.

```html
{trans 'cart.title'/}
{trans 'cart.items', count=$n/}
{trans 'greeting', name=$user, place='Utrecht'/}
```

**Parameters:** the key; `count=` picks the plural form and replaces `%d`; every other
`name=` replaces `:name`. The items are read raw, so any name may be a substitution.

**Catalogs:** `_lang/<locale>.json`, a flat object of keys - looked up from the page's
directory up to the application root and then `_common`, the most specific winning; for
`nl_NL` the files `nl_NL.json`, `nl-NL.json` and `nl.json` all count. Plural forms are
separated by `|`: two forms are one and other, three are zero, one and other. A key no
catalog knows is answered as itself.

**Escaping:** the catalog's text is written as it stands, markup included; a substitution,
and a key no catalog knows, is data and is written as `{$x}` writes a field - sanitized.

---

### cache
Keep a section of the page rendered - the fragment cache.

```html
{cache 'top-products', ttl=300}
  {topProducts}<li>{$name}</li>{/topProducts}
{/cache}
```

**Parameters:**
- First parameter: the section's name, its key within the application
- `ttl` - seconds the rendering is kept (default 300)
- `vary` - whatever else the rendering depends on: `vary=$userId` keeps one copy per user

**Behavior:** On a hit nothing inside the section runs - no tag, no query - and the stored
rendering takes its place; closing pipes and end options run on it as on a fresh one. The
`cache=<seconds>` option does the same for any tag, keyed on the page, the tag as written and
its evaluated parameters, and skips the tag's own handler on a hit. `$padFragmentCache`
selects the store (`'file'`, `'apcu'`, or `FALSE` to render every time);
`padFragmentForget('top-products')` drops a section, `padFragmentForget()` all of them.
PHP that runs before the template - the page's `.php` - runs on a hit too. What the section
leaves for the rest of the page is kept with it and made again on a hit: its `{push}`es, and
the page booking of a paged tag in it, for a `{pager}` further down. Otherwise a section keeps
its text, not its effects: what it sets, stores or defines - a `{set}`, `{increment}` or
`{decrement}`, a `{data}`, `{content}` or `{bool}` store, a `{define}` or `{macro}`, the totals
of a `callback=` - happens on the miss only, and on a hit the page goes on with the variables
and stores this request has. Put such a tag before the section, or in a `{nocache}` part, which
renders every time. A request for one
response fragment, or the post of a live region, renders the section and stores nothing. A
rendering that holds the visitor's CSRF token (a `{form}`, `{csrf}`, a live region) or the
request's `{nonce}` is not stored either: it renders every time. A `{nocache}` part of the
section renders on every hit.

---

### nocache
A part of a cached page or section that renders on every request, the hit included.

```html
{cache 'top-products', ttl=300}
  {topProducts}<li>{$name} {nocache}- {stock $id} left{/nocache}</li>{/topProducts}
{/cache}

<p>Served at {nocache}{php:date 'H:i:s'/}{/nocache}</p>    the page cache keeps the page round it
```

**Behavior:** where nothing caches, the content renders as any content does. Inside a `{cache}`
section (or a tag's `cache=` option) that is rendering, and on a page the page cache may keep,
the part is stored as its source: on a hit the stored rendering comes back with the part
rendered again, and a part inside a loop gets the fields of its rows back as they were when it
was stored. In a section the part renders where the section stands, with the page's fields;
a variable or store the cached text made before it is not there on a hit - the section keeps
its text, not its effects - so what a part reads is made before the section or in the part.
A page-cache hit runs no PHP - not the page's, nor an `_inits.php` - so there a part sees what a
request has without it: request and session values, the configuration, the `_lib` functions and
the application's tags. The CSRF token of a `{form}` or the `{nonce}` in a part no longer keeps
a section out of the fragment cache - the part renders for each visitor. A page-cache entry with parts is never answered with a 304 from the entry alone, nor
does a hit renew it. `{nocache}` takes no parameter.

---

### Comments
Not a tag: text the engine drops before it scans the template, tags inside included.

```html
{# a comment #}
{-- a comment --}
```

**Rules:** `{--` must be followed by whitespace (so `{--gap:4px}` in CSS is not one); each
form closes at the first `#}` or `--}` after it.

---

### reactData
Render a mount point `<div>` for a React component, filled with data from a provider.

```html
{reactData id='products', provider='products'/}
```

**Parameters:**
- `id` - DOM id of the generated div (default `myReactId`)
- `provider` - Provider file in `_providers/<name>.php` (defaults to `id`)
- `type` - `check` turns the provider's result into 1 or 0; otherwise (`record`, the default,
  or `array`) the result is passed as the provider returned it

**Behavior:** Runs the provider, stores the result in `$padProviders`, and outputs `<div id="..." data="...">` with the JSON HTML-escaped for the attribute. Read it in JS with `getAttribute('data')`. See [REACT.md](../REACT.md).

---

## Presentation Tags

Tags that draw, format and interact - the showcase application has an example of each.

---

### avatar
An initials avatar as inline SVG - drawn on the server (`tags/_lib/avatar.php`), no image, no
remote service: the same name is the same picture on every page and in every e-mail.

```html
{avatar 'Herbert Jebbink'/}
{avatar $name, size=32, shape='square'/}
{users}{avatar $email, size=40/} {$name}{/users}
```

**Parameters:**

| Option | Description |
|--------|-------------|
| first parameter | The name - its first and last word give the letters; an e-mail address counts by the words before its `@` (`herbert.jebbink@...` is HJ) |
| `size` | Width and height in pixels, default 48 |
| `shape` | `circle` (default) or `square` - with rounded corners |
| `label` | The accessible name (`role="img"`, `aria-label`, `<title>`), by default the name |

**Behavior:** the background is one of ten palette colours picked by a SHA-256 hash of the
lower-cased name; the text on it is white or near-black, whichever has the higher WCAG
contrast on that colour. The colours are `--pad-avatar-1` ... `--pad-avatar-10` and
`--pad-avatar-ink-<n>` on `.pad-avatar` with `light-dark()` defaults, written once per page in
a `<style>` (with the request's nonce when `$padCsp` asks for one); the fill attributes carry
the light colours for a reader without the style. A name without letters is drawn as `?`.
No name, or another shape, is an error under the strict check.

---

### identicon
A GitHub-style identicon as inline SVG - a symmetric 5 by 5 pattern and a colour from a SHA-256
hash of the value (`tags/_lib/identicon.php`): every account its own picture, nothing sent anywhere.

```html
{identicon $email/}
{identicon $user.id, size=32, title='Your identicon'/}
```

**Parameters:**

| Option | Description |
|--------|-------------|
| first parameter | The value - an e-mail address, a user id, a key |
| `size` | Width and height in pixels, default 64 |
| `title` | The accessible name, default `Identicon` - the value itself, often an address, is not written into the page |

**Behavior:** the value is trimmed and lower-cased before it is hashed, so every spelling of
an address is one picture. Fifteen nibbles of the hash fill the left three columns, mirrored
to the right; the colour is a hue from the last bytes at a saturation and lightness that read
on the light square behind it, `--pad-identicon-background` (`light-dark()` on
`.pad-identicon`, written once per page). An empty value is an error under the strict check.

---

### placeholder
A grey image placeholder as inline SVG - crossing lines and the size or a text in the middle
(`tags/_lib/placeholder.php`): wireframes and layouts that wait for their pictures, no placeholder
service.

```html
{placeholder '600x300', text='Hero image'/}
{placeholder ratio='16:9', fluid/}
{placeholder ratio='4:3', width=320/}
```

**Parameters:**

| Option | Description |
|--------|-------------|
| first parameter | The size, `width x height` (`600x300`) |
| `ratio` | Proportions instead of a size (`16:9`), drawn `width` pixels wide |
| `width` | The width of a box drawn from `ratio`, default 640 |
| `text` | Written in the middle and the accessible name, by default the size (`600 × 300`) or the ratio |
| `fluid` | As wide as the container (`width="100%"`), the height following from the view box |

**Behavior:** the view box is the size itself, so a box scales without distortion and the
text stays centred; the label's font follows the box, shrunk to fit a narrow one. The colours
are `--pad-placeholder-surface`, `-line` and `-text` on `.pad-placeholder` with `light-dark()`
defaults, written once per page. A size or ratio that is not two whole numbers is an error
under the strict check.

---

### progress
An accessible progress bar - the native `<progress>` element, styled on the server, under a
line with its label and percentage (`tags/_lib/progress.php`); with `steps` a row of step dots. No
script, it prints.

```html
{progress 72, max=100, label='Upload'/}
{progress $done, label=$file, color='success', size='small'/}
{progress 3, max=5, steps, label='Checkout'/}
```

**Parameters:**

| Option | Description |
|--------|-------------|
| first parameter | The value, clamped to 0 ... `max` |
| `max` | What the value counts to, default 100 (for `steps` a whole number up to 50, the number of steps) |
| `label` | Shown above the bar and the accessible name, default `Progress` (not shown) |
| `color` | A colour slot: `accent` (default), `success`, `warning`, `danger`, `neutral` |
| `size` | `small`, `medium` (default) or `large` - the height of the bar or the dots |
| `steps` | A dot per step joined by a line: the steps up to the value filled, the current one ringed |

**Behavior:** the bar is `<progress value max aria-label>` with the rounded percentage as its
fallback text; the steps are `role="progressbar"` with `aria-valuenow`, `-min`, `-max` and
`aria-valuetext="Step 3 of 5"`. The visible label line is `aria-hidden`, so it is not read
twice. Colours are `--pad-progress-track`, `-text` and one per slot on `.pad-progress`, with
`light-dark()` defaults, written once per page. A value that is no number, an unusable max,
an unknown colour or size is an error under the strict check.

---

### rating
A score as stars (or hearts) in inline SVG - whole ones filled, a part of one clipped to the
score, the score said in words (`tags/_lib/rating.php`).

```html
{rating 4.5, max=5/}
{products}{$product} {rating $score, size=16/}{/products}
{rating 3.75, icon='heart'/}
```

**Parameters:**

| Option | Description |
|--------|-------------|
| first parameter | The score, clamped to 0 ... `max` |
| `max` | The number of icons, a whole number from 1 to 20, default 5 |
| `size` | The height in pixels, default 20 |
| `icon` | `star` (default) or `heart` |
| `label` | The accessible name, default `4.5 out of 5` |

**Behavior:** a part of an icon is a nested `<svg>` as wide as the part - no `clipPath`, so no
id two ratings on one page could share - measured over the drawn icon, so 0.5 is exactly
half. `role="img"` with the label as `aria-label` and `<title>`. The colours are
`--pad-rating-star`, `-heart` and `-empty` on `.pad-rating`, `light-dark()` defaults, written
once per page; the fill attributes carry the light ones. A score that is no number, an
unusable max or an unknown icon is an error under the strict check.

---

### icon
An interface icon from PAD's own set, drawn on a 24 by 24 grid as lines, circles and
rectangles and written as inline SVG in the colour of the text around it - no icon font, no
sprite, no request. The icon subsystem (`pad/subs/icons/`): an icon is a file of `pad/subs/icons/types/`,
`<name>.svg` - a whole SVG a browser shows as it is, whose drawing the tag writes - and the
files there are the icons there are; the icon type writes one by its name, `{icon:star/}`. The
names are never reached bare: they are the names of fields and pages too.

```html
{icon 'arrow-right'/}
<button>{icon 'trash', size=16/} Delete</button>
<a href="?next">{icon 'chevron-right', label='Next page'/}</a>
{icon:cloud-upload size=24/}
```

**Parameters:**

| Option | Description |
|--------|-------------|
| first parameter | The name, a file of `pad/subs/icons/types/`: arrows and movement (`arrow-right`, `chevron-down`, `chevrons-right`, `undo`, `redo`, `refresh`, `move`, `trending-up` ...), marks and shapes (`check`, `x`, `plus`, `info`, `alert`, `alert-circle`, `ban`, `star`, `heart`, `thumb-up`, `smile`, `shield` ...), layout and text (`home`, `menu`, `grid`, `table`, `sidebar`, `search`, `zoom-in`, `settings`, `cog`, `align-left`, `toggle-on` ...), people and talk (`user`, `user-plus`, `mail`, `chat`, `bell`, `wifi`, `rss` ...), files and office (`file`, `file-text`, `folder`, `save`, `clipboard`, `paperclip`, `book`, `qr-code`, `barcode` ...), media (`play`, `pause`, `mic`, `volume`, `mute`, `music`, `video` ...), devices and code (`monitor`, `laptop`, `cpu`, `server`, `bug`, `git-branch`, `code`, `terminal`, `database` ...), money and trade (`cart`, `credit-card`, `wallet`, `package`, `truck`, `chart-line`, `chart-pie` ...), time, places and nature (`calendar`, `clock`, `history`, `map`, `compass`, `sun`, `cloud-rain`, `leaf`, `flame` ...) - `padIconNames ()` lists them all |
| `size` | Width and height in pixels, default 20 |
| `label` | The accessible name: the icon becomes `role="img"` with `aria-label` and `<title>` |
| `stroke` | The line width on the 24-unit grid, default 2 |

**Behavior:** `stroke="currentColor"`, round caps and joins, `fill="none"`, so an icon takes the
text colour and the class `pad-icon pad-icon-<name>` for styling. Without `label` it is
decoration - `aria-hidden="true"`, `focusable="false"` - for an icon beside the text it goes
with; an icon alone in a button or link needs `label`. No name, or a name not in the set, is an
error under the strict check, naming the icons close to it (`did you mean arrow-left?`).

---

### gravatar
The Gravatar picture of an e-mail address as an `<img>` (`tags/_lib/gravatar.php`) - only the
SHA-256 hash of the address leaves the page - or, with `fallback`, a local SVG drawn on the
server and no request to a third party at all.

```html
{gravatar $email, size=64, default='identicon', alt=$name/}
{gravatar $email, fallback='avatar', alt=$name/}
```

**Parameters:**

| Option | Description |
|--------|-------------|
| first parameter | The e-mail address - trimmed and lower-cased before it is hashed |
| `size` | Width and height in pixels, default 64; `srcset` asks twice that for a sharp screen |
| `default` | What Gravatar shows for an unknown address: `mp` (default), `identicon`, `monsterid`, `wavatar`, `retro`, `robohash`, `blank`, `404`, or an `https://` image address |
| `alt` | The image text - the person's name - default `Avatar` |
| `fallback` | `avatar` draws the `{avatar}` initials of `alt` (or of the address) instead, `identicon` the `{identicon}` of the address - no `<img>`, no outside request |

**Behavior:** the image is `https://www.gravatar.com/avatar/<sha256>?s=<size>&d=<default>` with
`width`/`height`, `loading="lazy"`, `decoding="async"` and `referrerpolicy="no-referrer"`, class
`pad-gravatar`. Showing it tells Gravatar the visitor's IP address; for a page under a
privacy policy that allows no outside requests, or a test without network, use `fallback`.
A missing address, an unknown default or fallback is an error under the strict check.

---

### notation
A text written in a notation, drawn on the server: a formula in AsciiMath, a chemical equation,
a tune in ABC, a guitar chord, notes on a piano, braille, morse, semaphore, signal flags, a chess,
go or draughts position. Every notation is a file of `pad/subs/notations/types/`, its functions
in `pad/subs/notations/_lib/`, its style in `www/assets/notations/` - written once per page
with the CSP nonce - and the files of `types/` are the notations there are; the subsystem is
`pad/subs/notations/`. Nothing needs a script: MathML, HTML or inline SVG, the same bytes for the
same text.

```html
{notation 'braille', 'Hello'/}                     # the tag: the kind, then its text
{notation:math}x = (-b +- sqrt(b^2-4ac))/(2a){/notation:math}   # the notation: type, text between the tags
{math 'sum_(i=1)^n i = (n(n+1))/2', display='block'/}           # bare
{chem 'CH4 + 2O2 -> CO2 + 2H2O'/}
{abc}X:1 ... K:G ... {/abc}
{notation:chord 'Am', instrument='ukulele'/}       # bare {chord} is the charts' chord diagram
{piano 'C4 E4 G4'/}  {piano 'Dm7'/}
{morse 'SOS'/}  {semaphore 'PAD'/}  {signals 'PAD'/}
{chess 'rnbqkbnr/pppppppp/8/8/4P3/8/PPPP1PPP/RNBQKBNR b KQkq e3 0 1'/}
{go '(;SZ[9]AB[cc][gc]AW[cg];B[ee];W[ec])', numbers/}
{draughts 'W:W31-50:B1-20'/}
```

A notation is written three ways, the same drawing each time: the tag, `{notation 'math', 'a/b'/}`,
whose second parameter is the kind's own text; the notation: type, `{notation:math 'a/b'/}`; and
bare, `{math 'a/b'/}`, after every other kind of tag. Without the text as a parameter the content
between the tags is the text, taken as written before the level walks it, so the braces of
AsciiMath - `{(x, if x >= 0),(-x, if x < 0):}` - and the brackets of SGF need no `{ignore}`. A
field gives the text as a parameter, `{chess $fen/}`; a notation with nothing to draw answers
nothing and its `@else@` shows.

| Notation | Draws | Its options |
|----------|-------|-------------|
| `math` | AsciiMath as MathML Core: fractions, scripts, roots, sums, matrices, text, the symbols | `display` (`inline`, `block`), `alttext` |
| `chem` | Chemical formulas and equations as MathML: counts, charges, coefficients, states, hydrates, isotopes, arrows; every element checked | `display`, `alttext` |
| `abc` | A tune in ABC notation as a staff: clef, key and time signatures, notes, stems, beams, ledger lines, rests, bars, repeats, chord names | `width`, `title` |
| `chord` | The box of a guitar or ukulele chord from its name or its frets (`x32010`) | `instrument` (`guitar`, `ukulele`), `frets`, `size`, `title` |
| `piano` | A keyboard with the keys of notes (`C4 E4 G4`) or a chord (`Dm7`) marked | `from`, `to`, `size`, `title` |
| `braille` | Unified English Braille, grade 1, as braille characters, the text the accessible name | `cells` (bare, or the cells to a line) |
| `morse` | The international Morse code of ITU-R M.1677-1 and its signal drawn to its timing | `plain` |
| `semaphore` | Flag semaphore, a figure per letter | `size`, `title` |
| `signals` | The flags and numeral pennants of the International Code of Signals | `size`, `title` |
| `chess` | A position in FEN - the built-in tag `{chess}` as well, which comes before a field or store of its name | `flip`, `highlight`, `arrow`, `size`, `title` |
| `go` | A game record in SGF played out on 9, 13 or 19 lines, captures taken off | `numbers`, `move`, `plain`, `size`, `title` |
| `draughts` | A position in the FEN of PDN, international 10 by 10 or 8 by 8 | `board` (`10`, `8`), `numbers`, `flip`, `highlight`, `size`, `title` |

**chess:** the first parameter is the position in FEN - the placement alone will do; side to
move, castling, en passant and the two counters are checked when written. `flip` puts Black at
the bottom, `highlight='e2, e4'` marks squares, `arrow='e2-e4, g1-f3'` draws arrows from the
middle of one square to the middle of the other, over the pieces; `size` is the width and height
in pixels, 360 by default, shrinking to fit a narrower container; `title` the accessible name,
`Chess position, White to move` by default. The board is `role="img"` with a `<title>` and a
`<desc>` listing every piece by side and kind (`White: king g1; rooks a1, f1; pawns ...`), the
side to move, castling, en passant, the marks and the arrows. The colours are
`--pad-chess-light`, `-dark`, `-white`, `-black`, `-ink`, `-mark`, `-arrow` and `-text` on
`.pad-chess`, `light-dark()` defaults, written once per page; the ids come from the board itself.

**Behavior:** every drawing is `role="img"` with a name and a description in words - the notes,
the stones, the flags, the strings - or MathML, which a screen reader reads itself; the colours are
`--pad-<kind>-*` custom properties on `.pad-<kind>` with `light-dark()` defaults (the flags keep
the code's colours). A name that is no notation, an option a notation does not read or a value
outside its choices, and a text it cannot read - a FEN with seven ranks, an element the periodic
table does not have, a character morse has no sign for, a move on a taken point - are errors
under the strict check, as are a formula longer than 10 000 bytes and ABC slurs nested more than
8 deep; without it the formula is drawn up to its limit and the slur left out. A byte that is no
UTF-8 is a character no notation has a sign for.

---

### chess
A chess position from FEN as inline SVG - the chess notation of `{notation}` above, kept a
built-in tag (`tags/chess.php`): `{chess}` comes before a field, a data store, a snippet or a
`_data` file named chess, where the bare name of a notation comes after them all, and
`{pad:chess}` names it. The FEN is the first parameter or the content between the tags; the
options and the drawing are those of the chess notation.

```html
{chess $fen, flip, highlight='d1, d8', arrow='g5-d8'/}
{chess '8/8/8/4k3/8/8/4K3/8', size=240, title='Opposition'/}
```

---

### game
A little game played in the browser: the server checks what the game is given and writes its
board, the game's script - once per page, with the CSP nonce - plays it. Every game is a file of
`pad/subs/games/types/`, its `.js` and `.css` in `www/assets/games/`, and the files of `types/` are
the games there are (`padGameTypes()`); the subsystem is `pad/subs/games/`.

```html
{game 'memory', pairs=6/}
{game:tictactoe opens='computer'/}
{sudoku '53..7....6..195....98....6.8...6...34..8.3..17...2...6.6....28....419..5....8..79'/}
{sudoku givens=26, seed=7/}
{crossword}
  ECHO: Writes a value without the sanitize chain
  PIPE: The | that hands a value to a function
{/crossword}
{quiz}
  Which tag repeats its content? | while | if | set
{/quiz}
```

A game is written three ways, the same game each time: the tag, `{game 'memory'/}`; the game:
type, `{game:memory/}`; and bare, `{memory/}`, when no tag, store, field or sequence has the
name. The words, clues, questions, symbols or picture a game takes stand between its tags, a
line each - taken as they are written, so they may hold braces - or come from a store named by
`data=`, or as a comma list in its first value.

| Game | Plays | Its options |
|------|-------|-------------|
| `bullscows` | Bulls and cows: find a number of different digits from the bulls and cows of each guess | `digits` (4), `tries` (10) |
| `crossword` | A crossword from `WORD: clue` lines, laid out on the server; type into the squares, check, reveal | `paper`, `solution` |
| `fifteen` | The sliding puzzle | `size` (4) |
| `gomoku` | Five in a row against the computer | `size` (15), `first`, `two` |
| `hangman` | Guess a word a letter at a time | `tries` (6), the words |
| `hanoi` | The towers of Hanoi | `disks` (4) |
| `maze` | Find the way through a new maze each game | `height` (12), `width` (18) |
| `memory` | Pairs of cards face down | `pairs` (8), the faces |
| `nim` | Take matches from heaps; the last one wins - or loses | `heaps` ('3, 4, 5'), `misere`, `opens` |
| `nonogram` | Uncover a picture from the runs of each row and column | the picture, `#` and `.` |
| `pegsolitaire` | Peg solitaire on the English cross | |
| `quiz` | Questions with answers to choose from: `question \| right \| wrong ...` | `order`; by `data=` the fields question, answer, wrong |
| `reversi` | Reversi against the computer | `opens`, `two` |
| `scramble` | Spell shuffled words again | `rounds` (10), the words |
| `snake` | Steer the snake to the apples | `size` (18), `speed` (6), `wrap` |
| `sudoku` | A given puzzle, or a new one with one solution; notes, hint, check | `givens` (30), `paper`, `solve` |
| `tictactoe` | Tic-tac-toe against the computer, which never loses | `opens`, `two` |
| `wordsearch` | Find the words in a square of letters | `size`, the words |

**Every game:** `seed=` makes the shuffles, boards and puzzles the same on every load (without
it the browser picks), `title=` names it. The board carries `data-result="won"` or `"lost"`
when a game ends and sends a `pad-game-end` event. The colours are `--pad-game-ink`,
`-accent`, `-accent2`, `-good`, `-bad`, `-mark` and their like on `.pad-game`, `light-dark()`
defaults. An option out of its bounds, a word that is no letters, a quiz line without two
answers, a picture row of another length, a sudoku without a solution are errors under the
strict check.

**Printed:** a sudoku or a crossword drawn on the server, no script - `paper` the puzzle,
`solve` the sudoku solved by a backtracking solver (the cell with the fewest candidates first;
the hardest published puzzles well under a second), `solution` the crossword filled in with
the answers behind the clues. The crossword's layout is greedy and the same every time: the
longest word first, across, then each next word where it crosses the most letters already
down, on the smallest grid; a word that fits nowhere is left out and named under the clues.

---

### toy
A small live simulation or visual toy played in the browser: the server checks the options it is
given and writes its frame, the toy's script - once per page, with the CSP nonce - runs it. Every
toy is a file of `pad/subs/toys/types/`, its `.js` (and a `.css` when it needs one) in
`www/assets/toys/`, and the files of `types/` are the toys there are (`padToyTypes()`); the
subsystem is `pad/subs/toys/`.

```html
{toy 'life', pattern='gun', wrap/}
{toy:mandelbrot center='-0.745, 0.11', zoom=40, palette='fire'/}
{sorting race, bars=40/}
{boids birds=300, seed=7/}
{life}
  .O.
  ..O
  OOO
{/life}
{lsystem}
  axiom: X
  X: F[+X]F[-X]+X
  F: FF
  angle: 20
{/lsystem}
```

A toy is written three ways, the same toy each time: the tag, `{toy 'life'/}`; the toy: type,
`{toy:life/}`; and bare, `{life/}`, when no tag, store, field or sequence has the name. The pattern
of a Game of Life and the rules of an L-system stand between its tags, a line each - taken as they
are written, so they may hold braces; an L-system's first value names one of its systems.

| Toy | Shows | Its options |
|-----|-------|-------------|
| `ant` | Langton's ant and the turmites; click to put down another ant | `rule` ('RL'), `ants` (1), `size` (160), `speed` (1000) |
| `automaton` | An elementary cellular automaton, the rows scrolling up; click a case of the rule to flip it | `rule` (30), `cells` (240), `initial` (single, random), `speed` (30) |
| `boids` | A flock from separation, alignment and cohesion; the pointer is a hawk | `birds` (150), `separation`, `alignment`, `cohesion` (5), `speed` (5) |
| `clocks` | An analog, a binary and a word clock; drag the minute hand, run them fast | `show` ('analog, binary, words'), `zone` |
| `fireworks` | Shells bursting in a night sky; click to send one up | `rate` (4), `gravity` (5), `shell` (mix, peony, ring, willow, palm, crossette) |
| `flowfield` | Particles drifting on a noise field, painting the flow; the pointer stirs | `particles` (1500), `scale` (4), `speed` (3), `fade` (3) |
| `galton` | Galton's board, the balls piling into a bell curve | `levels` (12), `balls` (500), `bias` (50) |
| `harmonograph` | Damped pendulums drawing their figure | `frequencies` ('2, 3, 3, 2.01'), `damping` (3), `rotary` |
| `kaleidoscope` | Drawing mirrored round the circle; a pen wanders when nobody draws | `mirrors` (8), `brush` (3), `colour` (rainbow, ink, palette) |
| `life` | Conway's Game of Life; draw cells, step, patterns | `width` (64), `height` (40), `speed` (10), `pattern`, `rule` ('B3/S23'), `wrap`, the pattern |
| `lsystem` | Plants and fractal curves from rewriting rules; drag to bend the angle, wind sways a plant | the system (plant), `iterations`, `angle`, the rules |
| `mandelbrot` | The Mandelbrot set and its Julia sets; click to zoom, drag to move, Play cycles the colours | `iterations` (300), `palette` (ocean, fire, rainbow, mono), `center`, `zoom`, `julia` |
| `orbits` | Bodies under gravity drawing their paths; drag to throw in a planet | `system` (solar, binary, eight, swarm), `trail` (300), `speed` (5) |
| `pendulum` | A double pendulum, chaotic; drag a bob to hold it | `pendulums` (1), `angles` ('120, -10'), `trail` (400), `gravity` (9.81) |
| `ripples` | A ripple tank - interference, slits, raindrops; click the water, build walls | `setup` (two, slits, pond), `frequency` (4) |
| `sand` | Falling sand, water, walls, wood, fire and plants to paint with | `width` (160), `height` (100), `material`, `brush` (3), `scene` (hourglass, garden, empty) |
| `sorting` | Sorting algorithms on bars, comparisons and swaps counted, with sound | `algorithm` (quick), `bars` (60), `order` (random, reversed, nearly, few), `speed` (120), `race`, `sound` |
| `spirograph` | A wheel rolling in a ring, layer over layer; drag round to roll it by hand | `ring` (96), `wheel` (52), `pen` (70), `outside` |
| `starfield` | Flying through stars; the pointer steers, a press speeds up | `stars` (600), `speed` (4), `warp` |

**Every toy:** `seed=` makes what it picks at random the same on every load (without it the
browser picks), `title=` names it, `paused` has it wait for its Play button. It holds while the
page is hidden or the toy is out of view, and waits for Play when the visitor asks for reduced
motion - a drawing toy then shows its figure finished. The root carries `data-state` - `playing`,
`paused` or `held`; the colours are `--pad-toy-ink`, `-canvas`, `-accent`, `-accent2`, `-night` and
their like on `.pad-toy`, `light-dark()` defaults, resolved by the script for the canvas and again
when the scheme changes. An option out of its bounds, a word a choice does not know, a life rule or
pattern row, an L-system line or a time zone that is none are errors under the strict check.

---

### sound
A short sound made from a recipe and played by the browser - a chime, a pop, a coin, a drum.
Every sound is a file of `pad/subs/sounds/types/` that returns its recipe, a PHP array, and the
files there are the sounds there are (`padSoundKinds()`); the subsystem is `pad/subs/sounds/`.

```html
{sound:chime/}
{sound 'pop', button='Pop!', volume=60/}
{sound:success on='click', target='#save'/}
{sound:tick on='hover', target='nav a'/}
<button data-pad-sound="coin">Coin</button>
{sound:bell download/}
{toast 'Saved', sound='success'/}
```

A sound is written two ways, the same sound each time: the tag, `{sound 'chime'/}`, and the sound:
type, `{sound:chime/}`; never bare. Without `target=` it writes a button that plays the sound,
named after the recipe's title or by `button=`; with `target=` - a CSS selector - it writes no
button but plays the sound when an element the selector finds has the event `on=` names: `click`
(the default), `pointerdown`, `hover`, `focus`, `change`, `input` or `submit` - the last three
with `target=` alone, as a button is never changed, typed in or submitted; both together write
the button and bind the selector. `volume=` plays it softer, 0 to 100. `download` adds a
link to the sound as a WAV file - `download='text'` its text - the page asked again with
`&padSound=<kind>`, which answers the WAV in place of the page; without script the button is
hidden and a link to the same WAV stands in its place. Once a sound has written the script, any
element with `data-pad-sound="<kind>"` plays on a click, or on the event its
`data-pad-sound-on` names, and `data-pad-sound-volume` from 0 to 1; `PadSound.play ( 'pop' )`
from a page's own script.

| Sound | What it is |
|-------|------------|
| `bell` | A struck bell, inharmonic partials dying away for two seconds |
| `chime` | Two soft bells a fifth apart - something went well |
| `click` | The short dry click of a switch or a key |
| `coin` | The two square notes of a coin in an old video game |
| `error` | Two low buzzing notes falling a tritone |
| `hihat` | A closed hi-hat, a short highpassed hiss |
| `jump` | A thin square pulse swept up - a platform game's hop |
| `kick` | A bass drum: a sine dropping from 150 Hz to 45 |
| `laser` | A square wave diving from high to low |
| `notify` | A gentle ding-dong - something came in |
| `pop` | A bubble bursting, a sine swept quickly up |
| `powerup` | A fast run of square notes up two octaves |
| `snare` | A snare drum: highpassed noise over a falling tone |
| `success` | A rising major arpeggio |
| `tick` | The tick of a clock, a high wooden tap |
| `warning` | Two tones taking turns, a small siren |
| `whoosh` | Noise through a bandpass sweeping up - air rushing past |

**The recipe:** `voices` played together; a voice has `wave` (sine, square, saw, triangle,
noise), `note` (A4, C#5, Bb3 or Hz; of noise the rate it is held at) or `notes` ('C5 E5 G5', `-`
a rest) with `step`, `to` and `glide` for a sweep or `bend` in semitones for every note of a run,
`start`, `length`, `attack`, `decay`, `sustain`, `release`, `gain`, `duty` (a square's pulse),
`vibrato` ([ Hz, semitones ]) and `filter` ([ lowpass|highpass|bandpass, Hz, to Hz, q ]); the
recipe `title`, `level` (the peak it is scaled to, 0.8) and `seed` (its noise). The server settles
it and both renderings read the settled recipe with the same arithmetic - the browser's script
and `padSoundWav ()` - so the WAV is what the page plays: 16-bit mono PCM at 22050 Hz, the same
bytes every time. An unknown sound, an `on=` that is none of its words, `change`, `input` or
`submit` without `target=`, a `volume=` out of 0 to 100 and a toast's `sound=` that is no sound
are errors under the strict check.

---

### printable
Pages made for paper: the content between the tags is one item - a label, a badge, a ticket, a
letter - rendered once per row of the data and laid out by its kind on sheets of paper. Every kind
is a file of `pad/subs/printables/types/`, and the files there are the kinds there are
(`padPrintableKinds()`); the subsystem is `pad/subs/printables/`.

```html
{printable:labels data='products', sheet='a4-3x8'}<b>{$name}</b> {barcode:ean13 $ean/}{/printable:labels}
{printable 'badges', data='guests', title='PAD Conference'}<h2>{$name}</h2>{$company}{/printable}
{printable:tickets data='seats'}<h2>{$show}</h2>Row {$row}<aside>{qr $code/}</aside>{/printable:tickets}
{printable:letter data='customers', sender='PAD, Main Street 1, 3511 AB Utrecht', pdf}
  <address>{$name}<br>{$street}<br>{$city}</address><aside>1 November 2026</aside>
  <p>Dear {$name},</p>
{/printable:letter}
```

A printable is written two ways, the same each time: the tag, `{printable 'labels'}...{/printable}`,
and the printable: type, `{printable:labels}...{/printable:labels}`; never bare. The data comes as
for any tag - `data=`, `sort=`, `where=`, `first=` - and a level without rows renders its `@else@`
half. Written as a single tag the printable has one item without content.

| Kind | What it lays out | Its options |
|------|------------------|-------------|
| `labels` | A sheet of sticky labels, the grid of its sheet | `sheet` (a4-3x8: a4-3x7, a4-2x7, a4-2x8, a4-2x4, a4-4x10, a4-5x13, letter-3x10, letter-2x10, letter-2x5, letter-2x3, letter-4x20), `outline` |
| `badges` | Name badges under a band of colour, the edges printed | `sheet` (a4-2x5, a4-2x4, letter-2x3), `title` (the band), `color` |
| `cards` | Business cards, 85 by 55 mm, crop marks in the margins | `sheet` (a4-2x5, letter-2x5) |
| `tickets` | Tickets with a stub - the `<aside>`, else the number - behind a perforation | `sheet` (a4-1x4, a4-1x5, letter-1x4), `number` |
| `certificate` | A certificate on A4 turned, framed twice, with signature lines | `title`, `signed` ('Name, Role; Name'), `color`, `paper` (a4, letter, a5), `portrait`, `size` |
| `envelope` | An envelope the size of the page, the sender and the address | `paper` (dl, c5, c6, c4), `sender`, `size` |
| `letter` | A letter for a window envelope, DIN 5008 form B: `<address>`, `<aside>`, `<header>`, `<footer>`, the rest the body | `sender`, `size` |
| `invoice` | The letter's page, a title over the body, its table set as invoice lines | `sender`, `title` (Invoice), `size` |
| `indexcards` | Index cards, ruled, as many to the sheet as fit, crop marks | `card` (5x3, 6x4, a6, a7), `paper`, `plain` |
| `placecards` | Place cards folded into a tent, the upper half turned | `sheet` (a4-2x2, a4-1x3, letter-2x2) |
| `graphpaper` | Graph paper, a grid over the sheet in whole squares, centred, drawn exactly; the content a line over it | `grid` (squares, dots, lines), `spacing` (5 mm; 8 for lines), `major` (every that many lines heavier), `color`, `margin`, `paper`, `landscape` |
| `calendar` | A wall calendar, a month to a sheet, a box for every day, holidays marked; the content over every month, the rows in turn | `year` (this year), `month` ('2027-03'), `months` (12; 1 with month), `sunday`, `weeks`, `holidays` ('nl', 'de-by'), `color`, `paper` (a4, a3, a5, letter), `landscape` |
| `foldcard` | A card folded twice, a quarter fold: `<header>` the front, `<footer>` the back, `<aside>` the inside left, the rest the inside right, the inside upside down | `paper` (a4, a3, letter), `landscape`, `folds` (the folds printed) |
| `staffpaper` | Music paper, empty staves over the sheet, drawn exactly; the content a line over them | `rastral` (0 to 8, 3 = 7.0 mm), `lines` (5; 6 tablature), `staves`, `grand` (pairs for the piano), `margin`, `color`, `paper`, `landscape` |

**Every kind of a grid** - labels, badges, cards, tickets, placecards - takes `paper`,
`landscape`, `across`, `down`, `width`, `height` (mm), `margin` (the top and the left margin, one
number for both; the grid centred without it), `pitch` (from an item to the next, across and
down) and `skip` (the first cells of a used sheet left empty); they and indexcards take
`padding`, `size` (points) and `align` for the text of their items, where the certificate, the
envelope, the letter and the invoice take the `size` of their table above. **Every kind** takes
`copies` (every item that many times, 5000 items at most), `scale` (the preview on the screen
smaller, 0.1 to 3), `pdf` (the links of `{pdf preview}` in its bar: the page asked with `&padPdf`
answers the sheets as a PDF, named by `file=` - no links inside a `{page}`, nor inside a `{pdf}`,
whose document holds the sheets).

**Behavior:** on the screen the sheets lie on a grey desk at their size in millimetres, the edges
of the labels dashed, a bar above them with what they are, a Print button and the PDF links.
Printed, a page with printables prints the sheets alone - the frame and the rest of the page out of
the way - each on a named `@page` of its own size without a margin, the paper's colours kept. In a
`{pdf}` the PDF writer reads the same plan from the `data-pdf-*` attributes: a sheet is a page of
its size, a box sets its content where the CSS puts it - clipped to it, moved to its middle,
turned for a place card - with its text size, alignment and font; rectangles and lines are drawn,
and so are the barcodes and QR codes of the codes subsystem; the sheets carry no page number. The
PDF has no CSS of the page, no picture but JPEG, no SVG with curves, and sets a table in its own
grid. Every item is one region of its sheet: what does not fit is cut at its edge. The positions
are classes of a style written with the CSP nonce. An unknown kind, sheet, paper or card, a number
out of its bounds, a colour that is no #rgb and a grid that does not fit its paper are errors
under the strict check.

---

### diff
What changed between two texts - word by word in the flow of the text, or line by line in
one column or side by side - with `<del>` and `<ins>` marked and styled (`tags/_lib/diff.php`).

```html
{diff $draft, $final/}
{diff $old, $new, lines, from='Monday', to='Friday'/}
{diff $before, $after, side, from='before', to='after'/}
```

**Parameters:**

| Option | Description |
|--------|-------------|
| first and second parameter | The old and the new text |
| `lines` | Compare line by line: a table of the old and the new line number, a sign and the line |
| `side` | The lines side by side, a changed line beside its counterpart, line numbers on both |
| `from`, `to` | Names of the two versions - the column heads beside each other, a caption in one column |

**Behavior:** Myers' O(ND) algorithm finds the shortest edit script, as git diff does. In a
run of changes the deletions come before the insertions, and the space between two changed
words joins the change. A changed line with a counterpart marks the words that changed
within it; a line without one is struck or added whole. Everything of the texts is escaped.
A screen reader hears where each deletion and insertion starts and ends - words in
pseudo-elements hidden from the eye. The colours are `--pad-diff-del`, `-del-mark`, `-ins`,
`-ins-mark`, `-text`, `-muted` and `-line` on `.pad-diff`, `light-dark()` defaults, written
once per page. A missing second text is an error under the strict check.

---

### excerpt
A long text cut to whole words with an ellipsis, centred on the first match of the search
words when given, every match in a `<mark>` (`tags/_lib/excerpt.php`).

```html
{excerpt $post, words=40/}
{results}<p>{excerpt $body, words=24, highlight=$q/}</p>{/results}
{excerpt $page.html, words=30, html/}
```

**Parameters:**

| Option | Description |
|--------|-------------|
| first parameter | The text |
| `words` | The length in words, default 40 |
| `highlight` | Search words - a query, split at everything that is no letter or digit; the 50 longest, each read for its first 64 letters |
| `html` | The text is HTML: its tags (and the content of `script` and `style`) dropped, its entities read, before it is cut |
| `ellipsis` | What stands for the cut-off text, default `…` |

**Behavior:** a search word matches at the start of a word whatever its case - `templ` finds
`Templates` - and the whole word is marked. The window of `words` words is centred on the first
match, moved in where it would run past an end; a text no longer than `words` stays whole.
The text is escaped piece by piece and only the `<mark>` around a match is markup. The mark
colours are `--pad-excerpt-mark` and `--pad-excerpt-marked` on `.pad-excerpt`, `light-dark()`
defaults, written once per page. A `words` that is no number of 1 or more is an error under
the strict check.

---

### lorem
Placeholder text: "Lorem ipsum dolor sit amet, ..." and then Latin words, the same text on
every request for the same seed (`tags/_lib/lorem.php`).

```html
<h2>{lorem words=4/}</h2>
<p>{lorem sentences=2/}</p>
{lorem paragraphs=3, seed=7, varied/}
```

**Parameters:**

| Option | Description |
|--------|-------------|
| `words` | So many words exactly, as sentences; fewer than five are a title, without a full stop. Default 50 when no length is given |
| `sentences` | So many sentences of six to fourteen words; with `paragraphs` the sentences of each |
| `paragraphs` | So many `<p>` paragraphs, of four to seven sentences unless `sentences` says |
| `seed` | Another text, the same again for the same seed - default 0 |
| `varied` | Leave the classic opening out - for the second text on a page |

**Behavior:** the words are picked from a Latin word list by the seeded sequence of
`lib/fake.php` (`padFakeSeed`), the same on every machine; the sequence's state is put back
afterwards, so a page that seeds `padFake*` for its own data gets the same values with or
without a `{lorem}`. A comma now and then, never in the opening. A length that is no number
from 1 to 10000, or `words` beside `sentences` or `paragraphs`, is an error under the strict
check.

---

### fake
A fake value of a kind - a name, an email, a price, an IBAN - or rows of several kinds, for a
prototype, a designer's preview or a demo page: the fake subsystem in `pad/subs/fakes/`, the values
of `lib/fake.php`'s sequence.

```html
{fake 'email'/}
{fake:city/}
{fake 'number', from=1, to=6/}
{fake 'name, email', rows=5, toData='people'/}
{fake 'customer:name, total:price', rows=3, seed=7}<li>{$customer} {$total}</li>{/fake}
{fake 'boolean', chance=30}On offer@else@Full price{/fake}
```

**Parameters:**

| Option | Description |
|--------|-------------|
| first parameter | The kind, a file of `pad/subs/fakes/types/` - address, boolean, city, colour, company, country, currency, date, decimal, ean, email, firstname, iban, ip, ipv6, isbn, job, lastname, latitude, longitude, mac, name, number, paragraph, phone, postcode, price, product, sentence, slug, street, time, timezone, title, url, username, uuid, word - or several, `'name, email'`, a field each under its kind's name or as `'field:kind'` says. The fake type names one: `{fake:email/}` |
| `rows` | So many rows of the kinds, the level's data - iterated by the pair's content, kept by `toData=` |
| `seed` | Values of their own seed: the rows `padFakeRows ( kinds, rows, seed )` gives |
| `from`, `to` | number (0, 100), decimal (0, 1), price (1, 100): the range; date: two moments padDateParse reads (`-1 year`, `now`); time: two clock times (`08:00`, `18:00`) |
| `format` | date: the format, `Y-m-d` |
| `decimals` | decimal: the places, 2; latitude, longitude: the places written, 6 |
| `south`, `north`, `west`, `east` | latitude: the band, -90 to 90; longitude: -180 to 180, a box across the 180th meridian when `west` lies east of `east` |
| `chance` | boolean: the chance of TRUE in percent, 50 |
| `words`, `sentences` | sentence, title, slug: their words; paragraph: its sentences |
| `name` | email: the name it is made from |

**Behavior:** one kind without `rows=` is the tag's value; several kinds, a pair or `rows=` are
rows - a pair of one kind one row, `{$name}` inside - and a FALSE of boolean, like `rows=0`, is a
miss, so its `@else@` shows. An email or a username in a row with a name is that person's, a slug in a row with a title that title's.
Without `seed=` the `{fake}` tags of a request share a sequence of their own from one seed:
the page shows the same values on every request (a date relative to now moves with the day).
The page's own sequence - its `padFakeSeed` and what it made - is put back afterwards, as
`{lorem}` puts it back. The kinds are the files of `pad/subs/fakes/types/`, each a few lines that
answer the value and read their options with `padFakeOpt`; a kind is added by adding its file.
A kind there is none of, a field that is no plain name, `rows` that are no number from 0 to
10000 and an option no kind of the tag reads are errors; `{fake:zebra/}` is "there is no fake
kind named 'zebra'". Fake kinds are never reached bare: their names are those of fields.
A latitude beyond a pole, a longitude beyond the 180th meridian and `decimals` that are no
whole number from 0 to 10 are errors under the strict check; the lenient walk leaves the value
out.

### widget
An interactive part of the page that works without JavaScript and gets better with it - tabs,
an accordion, a carousel, a wizard, a lightbox, a modal, a drawer, a dropdown, a tooltip, a
toast, a copy box, a poll, a breadcrumb. Every widget is a file of
`pad/subs/widgets/types/` - its tag code - with its functions in `pad/subs/widgets/_lib/`, its style and
script in `www/assets/widgets/`, and the files of `types/` are the widgets there are
(`padWidgetTypes()`); the subsystem is `pad/subs/widgets/`.

```html
{widget 'tabs', active=2}{tab 'Overview'}...{/tab}{tab 'Specs'}...{/tab}{/widget}
{widget 'modal', 'terms', title='Terms of sale'}...{/widget}
{widget:modal 'terms', title='Terms of sale'}...{/widget:modal}
{modal 'terms', title='Terms of sale'}...{/modal}
```

A widget is written three ways, the same widget each time: the tag, whose first parameter
names the widget and whose second is the widget's own first - `{widget 'modal', 'terms'}`; the
widget: type, `{widget:modal 'terms'}`; and bare, `{modal 'terms'}`. A bare name comes after
every other kind of tag - a store, a field, an array, a sequence, a chart kind or a game of the
name wins: a page's `$tabs` array is the data of `{tabs}`, and `{pad:tabs}` is no tag. `{copy}`
stays a built-in tag that runs the copy widget, as PHP's `copy()` would take the bare name;
`{tab}`, the item of the widgets that take a list of labelled parts, is a built-in tag.

| Widget | What it is | Its options |
|--------|------------|-------------|
| `accordion` | Every `{tab}` a `<details>`, opened and closed by the browser | `single`, `open` |
| `breadcrumb` | Where the page stands - links from home to the page, from its path, its lines or data | `home`, `label`, `data` |
| `carousel` | Slides that scroll and snap sideways, previous/next and dot links | `label` |
| `copy` | A text with a copy-to-clipboard button | the text, `label` |
| `drawer` | A panel that slides in from a side of the window, with the button that opens it | the name, `title`, `button`, `side` |
| `dropdown` | A button and the menu it opens - the popover attribute, anchored under it | the button's text, `align` |
| `lightbox` | Every `{tab}` a thumbnail that opens its picture large over the page, `:target` | `label` |
| `modal` | A `<dialog>` and the button that opens it | the name, `title`, `button` |
| `poll` | A vote once per visitor, the results as bars | the name, `options`, `title`, `button`, `results` |
| `tabs` | Panels behind a row of tabs - radio buttons and CSS | `active` |
| `toast` | A notice the visitor dismisses, in the flow or in a corner of the window | the title, `tone`, `place`, `timeout` |
| `tooltip` | A short tip while the pointer or the focus is on its trigger - CSS alone | the tip, `place` |
| `wizard` | Every `{tab}` a step, one shown at a time, the steps numbered above, Back and Next under each | `active`, `next`, `back`, `label` |

**Every widget:** its markup is built on the server; a script, where it has one, is written once
per page with the CSP nonce and only makes it smoother. The colours are custom properties on
`.pad-<widget>` - `--pad-tabs-accent`, `--pad-modal-surface` ... - with `light-dark()` defaults,
written once per page. A name that is no widget is an error, "there is no widget named 'x'".

---

### tabs
Panels behind a row of tabs, without JavaScript - a radio button and a label per tab, the CSS
showing the panel of the checked one (`subs/widgets/_lib/tabs.php`). The items are `{tab}` pairs,
which `{accordion}`, `{carousel}`, `{wizard}` and `{lightbox}` take as well - `{tab}` is a
built-in tag.

```html
{tabs active='Specs'}
  {tab 'Overview'}<p>A 1.7 litre kettle ...</p>{/tab}
  {tab 'Specs'}<table>...</table>{/tab}
{/tabs}

{tabs}{plans}{tab $name}<p>{$price}</p>{/tab}{/plans}{/tabs}
```

**Parameters:**

| Option | Description |
|--------|-------------|
| `active` | The tab shown first - its number from 1, or its label; the first one when not given |
| `{tab 'label'}` | One tab: the first parameter is its label, the content its panel |

**Behavior:** a `{tab}` renders its content and hands it to the nearest `{tabs}`,
`{accordion}`, `{carousel}`, `{wizard}` or `{lightbox}` below it instead of printing it - it
may stand in an `{if}` or in a loop that makes a tab per row. The set is a radio group: one stop in the tab order, the
arrow keys move between the tabs, a screen reader hears the label and 'checked'; each panel is
a `region` named by its tab. No fragment goes into the address, so two sets on a page keep
their choices; printed, every panel shows. The ids are made from the labels, the same on every
request. The colours are `--pad-tabs-accent`, `-text`, `-muted` and `-line` on `.pad-tabs`,
`light-dark()` defaults, written once per page. A `{tab}` outside an owner, a tab without a
label, text between the tabs, a set without a tab and an `active=` naming no tab are errors
under the strict check.

---

### accordion
Items that open and close on a click, without JavaScript - every `{tab}` a `<details>` with its
label as the `<summary>` (`subs/widgets/_lib/accordion.php`).

```html
{accordion single, open=1}
  {tab 'Can I return it?'}<p>Within 30 days ...</p>{/tab}
  {tab 'Is there a warranty?'}<p>Two years ...</p>{/tab}
{/accordion}
```

**Parameters:**

| Option | Description |
|--------|-------------|
| `single` | One item open at a time: the items share a `name` - the exclusive accordion of HTML |
| `open` | The item open at first - its number from 1 or its label; `open` alone opens every item |

**Behavior:** the `{tab}` items are collected as `{tabs}` collects them, so they may come from
a loop. The browser opens and closes an item, from the keyboard too, a screen reader hears
'collapsed' or 'expanded', and a find in the page opens the item it finds in. The colours are
`--pad-accordion-accent`, `-text`, `-muted`, `-line` and `-hover` on `.pad-accordion`,
`light-dark()` defaults, written once per page. A tab without a label, `open` alone on a
`single` accordion and an `open=` naming no item are errors under the strict check.

---

### modal
A dialog over the page - an HTML `<dialog>` with the content, and the button that opens it
(`subs/widgets/_lib/modal.php`).

```html
{modal 'terms', title='Terms of sale', button='Read the terms'}
  <p>These terms apply to every order ...</p>
{/modal}
```

**Parameters:**

| Option | Description |
|--------|-------------|
| first parameter | The name - letters, digits, `_` and `-`; the dialog's id is `pad-modal-<name>`, a second modal of the name `pad-modal-<name>.2` |
| `title` | The heading of the dialog, which names it; the button's text when not given |
| `button` | The text of the button that opens it, default `Open` |

**Behavior:** the button opens the dialog as a modal in the first way the browser can: the HTML
invoker commands (`commandfor`, `command="show-modal"`) where it has them, with no script at
all; a small script, once per page with the CSP nonce, calling `showModal()` and `close()`
where it has not; and with scripting off a link to the dialog's id, which the CSS shows as an
overlay (`:target`) with a close link back to the opener - `@media (scripting)` decides between
the button and the links, and a browser too old for that query gets the links. Opened, the page
behind is inert, the focus moves into the dialog and back to the button, and Escape, the close
button or a click on the backdrop (`closedby="any"`) closes it. An address ending in
`#pad-modal-<name>` opens it on arrival. The colours are `--pad-modal-accent`, `-on`, `-text`,
`-muted`, `-line`, `-surface` and `-backdrop` on `.pad-modal`, `light-dark()` defaults, written
once per page. A modal without a valid name is an error under the strict check.

---

### carousel
Slides in a row that scrolls and snaps sideways (CSS scroll snap), with previous and next links
on each slide and a dot per slide below - links to the slides' ids, so no JavaScript is needed
(`subs/widgets/_lib/carousel.php`).

```html
{carousel label='Landscapes'}
  {tab 'Sunrise over the hills'}<svg viewBox="0 0 800 400" role="img" aria-label="...">...</svg>{/tab}
  {tab 'The open sea'}<img src="sea.jpg" alt="Waves under a pale sky">{/tab}
{/carousel}
```

**Parameters:**

| Option | Description |
|--------|-------------|
| `label` | The carousel's name for a screen reader, default `Carousel` |
| `{tab 'caption'}` | One slide; its label, optional, is the caption under it |

**Behavior:** the markup follows the WAI-ARIA carousel pattern - a region with
`aria-roledescription="carousel"`, every slide a group saying 'slide 2 of 4' with its caption -
and the row scrolls by finger, trackpad or the arrow keys when focused. The previous link of
the first slide goes to the last, the next link of the last to the first. There is no autoplay.
Without scripting a link scrolls the page to its slide and the dot of the targeted slide is
marked; a small script, once per page with the CSP nonce, scrolls the row alone and marks the
dot of the slide in view (`aria-current`), also after a swipe. The scrolling is smooth unless
reduced motion is asked for; printed, the slides stand under each other. The colours are
`--pad-carousel-accent`, `-text`, `-muted`, `-surface` and `-dot` on `.pad-carousel`,
`light-dark()` defaults.

---

### copy
A text with a copy-to-clipboard button - the text in a `<pre><code>` that selects as a whole
with one click, so it is copied by hand where the button cannot run (`subs/widgets/_lib/copy.php`).

```html
{copy 'composer require pad/pad'/}
{copy $command, label='Copy command'/}
{copy label='Copy settings'}
  padSqlDatabase={$app}/{$app}.sqlite
{/copy}
```

**Parameters:**

| Option | Description |
|--------|-------------|
| first parameter | The text, a value - escaped |
| `label` | The button's text, default `Copy` |

**Behavior:** as a pair the content is rendered first and becomes the text, trimmed. The button
shows when scripting is on (`@media (scripting: enabled)`): a small script, once per page with
the CSP nonce, puts the text on the clipboard - `navigator.clipboard`, or the selection and
`execCommand` on a page over plain http - and says 'Copied' on the button and, through a status
region beside it, to a screen reader. The colours are `--pad-copy-accent`, `-text`, `-muted`,
`-line`, `-surface` and `-done` on `.pad-copy`, `light-dark()` defaults, written once per page.
A `{copy}` without a text is an error under the strict check.

---

### poll
A vote among a few answers, once per visitor, and then the results as bars with their
percentages - built on a `{live}` region, and a plain form without scripting (`subs/widgets/_lib/poll.php`).

```html
{poll 'favorite-language', options='PHP, Python, Ruby, Go', title='Which language do you reach for first?'/}
{poll 'favorite-language', options='PHP, Python, Ruby, Go', results/}
```

**Parameters:**

| Option | Description |
|--------|-------------|
| first parameter | The name - letters, digits, `_` and `-`: it names the file of the votes and the region |
| `options` | The answers, separated by commas - two or more |
| `title` | The question, by default the name made readable |
| `button` | The text of the vote button, default `Vote` |
| `results` | Show the results to everyone, also before a vote - a results page |

**Behavior:** the form posts to the page itself: with scripting on, the region's script sends it
in the background and swaps in the results; with scripting off it is an ordinary form POST and
the page comes back with them. `$padCsrf` protects it as every form. A visitor votes once per
poll - the answer is kept in the session, and a visitor who voted sees the results; a GET
starts no session for a visitor without one. The votes are counted per answer in
`DATA/poll/<app>/<name>.json` under an exclusive lock (`flock`); an answer taken out of
`options=` keeps its count in the file and shows no more. `padPollVote ( $name, $answer )` adds
a vote from PHP, `padPollCounts ( $name )` reads them. The colours are `--pad-poll-accent`,
`-on`, `-text`, `-muted`, `-line`, `-track` and `-surface` on `.pad-poll`, `light-dark()`
defaults, written once per page. A name that is no file name is an error; fewer than two
answers is one under the strict check.

---

### toast
A notice the visitor dismisses - a `<dialog>` shown from the start, not modal, with its title,
its text and a close button whose invoker command (`commandfor`, `command="close"`) has the
browser close it, the toast's script where the browser has none - no form of its own, so a
toast stands inside a `{form}` as well, its fields staying in the form (`subs/widgets/_lib/toast.php`).

```html
{toast 'Saved', tone='success'}Your changes are kept.{/toast}
{toast 'Payment failed', tone='error', place='top'/}
{toast 'Copied', place='bottom', timeout=4/}
```

**Parameters:**

| Option | Description |
|--------|-------------|
| first parameter | The title; a pair's content is the text under it, rendered first. One of the two is needed |
| `tone` | `info` (the default), `success`, `warning` or `error` - the colour of its edge |
| `place` | `inline` (the default, in the flow of the page), `top` or `bottom` - fixed in that corner of the window |
| `timeout` | Seconds, 1 to 600: the toast closes by itself after them |
| `sound` | A sound of `pad/subs/sounds/types/` - `sound='success'` - played as the toast shows, once the browser lets the page play (after a gesture on it); see `{sound}` |

**Behavior:** the text is a status region, an alert for a warning or an error, so a toast a
fragment or a live region brings is read out; a toast without a title is named by its tone.
The script is written once per page with the CSP nonce: it closes a toast where the browser
has no invoker commands, closes a focused toast with Escape and runs `timeout=`, holding the
time while the pointer or the focus is on the toast; a toast a fragment or a live region
brings later is timed as well. Two toasts in one corner lie over each other, so a page puts one there.
Printed, no toast shows. The colours are `--pad-toast-text`,
`-muted`, `-line`, `-surface`, `-info`, `-success`, `-warning` and `-error` on `.pad-toast`,
`light-dark()` defaults. A toast without title and text, an unknown tone or place and a
timeout that is no whole number of seconds are errors under the strict check.

---

### dropdown
A button and the menu it opens - the menu a popover (the HTML popover attribute) the button
opens through `popovertarget`, so the browser opens, closes and places it without a script
(`subs/widgets/_lib/dropdown.php`).

```html
{dropdown 'Account', align='end'}
  <p>Signed in as {$user}</p>
  <a href="?profile">Profile</a>
  <a href="?settings">Settings</a>
  <hr>
  <a href="?logout">Log out</a>
{/dropdown}
```

**Parameters:**

| Option | Description |
|--------|-------------|
| first parameter | The text of the button |
| `align` | `start` (the default) - the menu's left edge under the button's - or `end`, its right edge |

**Behavior:** the content is rendered first and becomes the menu: links, buttons, a rule
(`<hr>`) and a line of text (`<p>`) are drawn as a menu's. The menu stands in the top layer -
no parent's overflow cuts it off - under its button by CSS anchor positioning, flipped above it
where there is no room below; a click outside it, Escape or opening another menu closes it, the
button is 'expanded' for a screen reader, and the menu follows the button in the tab order. A
small script, once per page with the CSP nonce, adds the arrow keys, Home and End between the
items, opens the menu at its first item on the down arrow, closes it after a click on an item,
and places it under the button in a browser without anchor positioning - where there is no
script either, the menu shows in the middle of the window. The colours are
`--pad-dropdown-accent`, `-text`, `-muted`, `-line`, `-surface` and `-hover` on
`.pad-dropdown`, `light-dark()` defaults. A dropdown without the text of its button, one with
an empty menu and an unknown `align` are errors under the strict check.

---

### tooltip
A short tip shown while the pointer is on its trigger or the trigger has the focus - shown by
CSS alone, the trigger naming it for a screen reader with `aria-describedby`
(`subs/widgets/_lib/tooltip.php`).

```html
The {tooltip 'Billed every month, cancel any time'}Starter plan{/tooltip} costs € 9.
VAT {tooltip 'Value added tax: 21% on most goods'/} included.
Read the {tooltip 'Opens the terms', place='bottom'}<a href="?terms">terms</a>{/tooltip}.
```

**Parameters:**

| Option | Description |
|--------|-------------|
| first parameter | The tip, a text |
| `place` | `top` (the default) or `bottom` - above or below the trigger |

**Behavior:** the pair's content is the trigger, rendered first; the single tag's trigger is a
small information button named 'More information'. A trigger of text gets a stop in the tab
order; content that holds a link, a button or a field takes the focus itself and gets none.
The tip has the `tooltip` role and stays while the pointer moves onto it, so it can be read; a
small script, once per page with the CSP nonce, hides a shown tip on Escape and shifts a tip
that would run off the side of the window back into it. Its style and script follow it: PHP's
tidy (`$padTidy`, on by default) moves a `<style>` into the head and ends a `<p>` it stood in, so
a tip is best written in a `<div>`, a list item or a table cell. Printed, no tip shows. The
colours are `--pad-tooltip-accent`, `-muted`, `-surface` and `-on` on `.pad-tooltip`,
`light-dark()` defaults - a dark tip on a light page, a light one on a dark page. A tooltip
without its tip, a pair with nothing between its tags and an unknown `place` are errors under
the strict check.

---

### wizard
A form or a story in steps, one shown at a time - every `{tab}` a step, the steps numbered in a
row above, the ones done and the current one marked, and a Back and a Next under each; radio
buttons and CSS, as `{tabs}` is, so no script is needed (`subs/widgets/_lib/wizard.php`).

```html
{form 'order'}
  {wizard label='Your order'}
    {tab 'Address'}{input 'street', label='Street'/}{/tab}
    {tab 'Delivery'}{input 'day', type='date', label='Day'/}{/tab}
    {tab 'Payment'}<p>Pay by card or by bank.</p><button>Order</button>{/tab}
  {/wizard}
{/form}
```

**Parameters:**

| Option | Description |
|--------|-------------|
| `active` | The step shown first - its number from 1, or its label; the first one when not given |
| `next`, `back` | The texts of the two buttons, `Next` and `Back` |
| `label` | The wizard's name for a screen reader, default `Steps` |
| `{tab 'label'}` | One step: its name in the row, the content its panel |

**Behavior:** the steps are one radio group - one stop in the tab order, the arrow keys move
between them, a screen reader hears 'Payment, step 3 of 3' - and each panel is a region named
by its step. Back and Next are labels of the radio before and after without scripting, and real
buttons with it (`@media (scripting)`, as for `{modal}`), which a small script, once per page
with the CSP nonce, makes check the step and move the focus to it. The first step has no Back,
the last no Next - its own content ends it. Every step of a form is in the form, shown or not,
so every field is posted; a required field on a step not shown stops the post where the visitor
cannot see it, so the page's PHP is the place to check a step (`padValidate`). Printed, every
step shows. The colours are `--pad-wizard-accent`, `-on`, `-text`, `-muted`, `-line` and
`-surface` on `.pad-wizard`, `light-dark()` defaults. A step without a label, text between the
steps, a wizard without a step and an `active=` naming none are errors under the strict check.

---

### lightbox
A gallery whose pictures open large over the page - every `{tab}` a thumbnail in a grid, a link
to a view of the picture that the CSS shows over the whole window when the address names it
(`:target`), so no script is needed (`subs/widgets/_lib/lightbox.php`).

```html
{lightbox label='The harbour'}
  {tab 'At dawn'}<img src="harbour/dawn.jpg" alt="Boats in the morning mist">{/tab}
  {tab 'At noon'}<img src="harbour/noon.jpg" alt="The quay in bright sun">{/tab}
{/lightbox}
```

**Parameters:**

| Option | Description |
|--------|-------------|
| `label` | The gallery's name for a screen reader, default `Gallery` |
| `{tab 'caption'}` | One picture - an `<img>` or an `<svg>`, shown small and large; its label, optional, is the caption |

**Behavior:** a view holds the picture as large as the window allows, its caption with
'n / count', a link to the picture before and after it (the first's before is the last) and a
close link back to its thumbnail; the back button closes a view, an address ending in a view's
id opens it, and the page behind does not scroll while one is open (`:has`). A small script,
once per page with the CSP nonce, adds Escape to close and the arrow keys to go on, and moves
the focus into a view as it opens and back to its thumbnail as it closes. A caption names the
thumbnail's link; without one the picture's own alt text does. The thumbnails are 4:3 and 9rem
wide at least - `--pad-lightbox-ratio` and `--pad-lightbox-size` change that. Printed, no view
shows. The colours are `--pad-lightbox-accent`, `-shade`, `-backdrop`, `-button` and `-caption`
on `.pad-lightbox`, `light-dark()` defaults. A lightbox without pictures or with text between
them is an error under the strict check.

---

### drawer
A panel that slides in from a side of the window - a modal `<dialog>` along an edge and the
button that opens it, opened the way `{modal}` opens its dialog (`subs/widgets/_lib/drawer.php`).

```html
{drawer 'filters', title='Filters', button='Show filters', side='left'}
  {form 'filter'}...{/form}
{/drawer}
```

**Parameters:**

| Option | Description |
|--------|-------------|
| first parameter | The name - letters, digits, `_` and `-`; the panel's id is `pad-drawer-<name>`, a second drawer of the name `pad-drawer-<name>.2` |
| `title` | The heading of the panel, which names it; the button's text when not given |
| `button` | The text of the button that opens it, default `Open` |
| `side` | `right` (the default) or `left` - the whole height of the window - or `bottom`, a sheet across its width |

**Behavior:** the button opens the panel by the HTML invoker commands where the browser has
them, by a small script, once per page with the CSP nonce, where it has not, and with scripting
off a link to the panel's id shows it as an overlay (`:target`) with a close link back to the
opener. Opened, the page behind is inert, the focus moves into the panel and back afterwards,
and Escape, the close button or a click on the backdrop closes it; an address ending in
`#pad-drawer-<name>` opens it on arrival. The panel slides in unless reduced motion is asked
for. The button, its link and the panel stand in a `<div class="pad-drawer">` of
`display: contents`, which carries the colours: `--pad-drawer-accent`, `-on`, `-text`, `-muted`,
`-line`, `-surface` and `-backdrop`, `light-dark()` defaults. A drawer without a valid name and
an unknown `side` are errors under the strict check.

---

### breadcrumb
Where the page stands - a `<nav>` around an ordered list of links from the application's home
to the page, the last step the page itself (`aria-current="page"`), the separators drawn by the
CSS and never read out (`subs/widgets/_lib/breadcrumb.php`).

```html
{breadcrumb/}
{breadcrumb}
  Home | ?index
  Kettles | ?products/kettles
  Aurora
{/breadcrumb}
{breadcrumb data='trail'/}
```

**Parameters:**

| Option | Description |
|--------|-------------|
| `data` | The steps from the rows of a store, the page's array or a `_data` file - a row's `label` (or `text`, `name`) and `link` (or `href`, `url`) |
| `home` | The first step's text when the trail is the page's path, default `Home` |
| `label` | The trail's name for a screen reader, default `Breadcrumb` |

**Behavior:** alone it follows the file tree: Home, then every directory of the page's path - a
link to its index page where it has one - and the page last, each named by its file name made
readable (`order_list` is `Order list`); a page a route reached is named as it was asked for,
`products/42`. A pair takes the steps from its lines as they rendered - a label and, after a
bar, its link. A label is text; a link of another scheme than http, https and mailto -
`javascript:`, read as a browser reads it - is left out. The separator is
`--pad-breadcrumb-separator` (`"/"`), the colours `--pad-breadcrumb-text`, `-muted`, `-link` and
`-line` on `.pad-breadcrumb`, `light-dark()` defaults. A pair without steps and a `data=` that
names no data are errors under the strict check.

---

### img
A picture of `www/<application>/` as a thumbnail of the size the page shows it at - resized
once with GD (`tags/_lib/img.php`) and kept in `www/<application>/_thumbs/`, where the web server
sends it like any asset.

```html
{img 'photos/harbour.jpg', width=400, height=300, alt='The harbour at dawn'/}
{img 'photos/dunes.jpg', width=300, height=300, fit='contain', format='webp', quality=70, alt=''/}
```

**Parameters:**

| Option | Description |
|--------|-------------|
| first parameter | The picture: a name below `www/<application>/` - a JPEG, PNG, GIF or WebP |
| `width`, `height` | The box in CSS pixels; one of them keeps the picture's proportion, neither keeps its size |
| `fit` | `cover` (default: fill the box, cut around the centre) or `contain` (all of it inside the box) |
| `format` | `jpeg`, `png` or `webp` - default the source's, a GIF becoming a PNG |
| `quality` | 1 to 100 for JPEG and WebP, default 80 |
| `alt` | Required - the text for whoever cannot see the picture; `alt=''` marks it as decoration |
| `filter` | A comma list of filters of `pad/subs/filters/types/`, applied in the order written - see below |
| `colors`, `levels`, `cell`, `radius`, `amount`, `columns`, `chars`, `components` | The options of the filters, each read by the filters that take it |

**Filters:** `filter=` runs the picture through the filter subsystem (`pad/subs/filters/`), the
files of its `types/` the filters there are, written in the order they apply:

```html
{img 'photos/harbour.jpg', width=400, filter='duotone', colors='#123, #fc6', alt='The harbour'/}
{img 'photos/harbour.jpg', width=400, filter='grayscale, dither', alt='The harbour'/}
{img 'photos/harbour.jpg', width=400, filter='halftone', cell=6, alt='The harbour in dots'/}
{img 'photos/harbour.jpg', width=400, filter='blurhash', alt='The harbour'/}
```

| Filter | Does | Its options |
|--------|------|-------------|
| `grayscale` | Shades of grey, the luminance of every pixel | |
| `sepia` | The brown of an old photograph, the classic matrix | `amount` (100) |
| `duotone` | The luminance from one colour in the shadows to another in the highlights | `colors` ('#16213e, #f5c26b') |
| `posterize` | Every channel cut to so many steps; a PNG | `levels` (4) |
| `pixelate` | Blocks, each the average of what it covers; 1x, pixels sharp, a PNG | `cell` (8) |
| `blur` | Out of focus, in CSS pixels on the 1x and the 2x picture | `radius` (6) |
| `sharpen` | Crisper edges, the picture plus its Laplacian | `amount` (50) |
| `vignette` | Darker corners | `amount` (50) |
| `invert` | The negative | |
| `dither` | Floyd and Steinberg's error diffusion to ink and paper; 1x, a PNG | `levels` (2), `colors` ('#000000, #ffffff') |
| `atkinson` | Atkinson's dither, three quarters of the error carried on | as dither |
| `bayer` | Ordered dithering by Bayer's map of 8 by 8 | as dither |
| `halftone` | SVG dots in place of the `<img>`, their area the darkness, named by the alt text | `cell` (8), `colors` ('#1a1a19, #fcfcfb') |
| `ascii` | Text in a `<pre>` in place of the `<img>`, `role="img"` named by the alt text | `columns` (64), `chars` (' .:-=+*#%@') |
| `blurhash` | A placeholder: Wolt's BlurHash in `data-blurhash`, decoded to a small PNG behind the picture | `components` ('4,3') |
| `dominant` | A placeholder: the dominant colour behind the picture, and in `data-color` | |
| `lqip` | A placeholder: a tiny blurred PNG behind the picture | |

The filters that change the picture work on each thumbnail after it was resized and are part of
its name, with the density of that thumbnail - a blur of 6 CSS pixels is another picture at 2x
than at 1x - the same picture and options, the same file, its bytes the same on every run. A pixel
filter (pixelate and the dithers) is made at 1x alone and drawn `image-rendering: pixelated`; a
filter of flat colours is a PNG unless `format=` says otherwise. halftone's dots, ascii's text and
the placeholders are kept in `_thumbs/` as well, beside the thumbnails, made once. A placeholder
is made of the 1x picture as shown - every image filter of the list applied, wherever the
placeholder stands in it: `'invert, dominant'` and `'dominant, invert'` are one. BlurHash is the
reference algorithm, its hash made of the 1x picture at most 64 pixels on its longer side. A
picture with transparent parts keeps them through blur, its colours weighed by their opacity;
dominant counts a pixel by its opacity, and lqip and blurhash lay the picture on white. A filter
that reads every pixel in PHP - sepia, duotone, posterize, vignette, the dithers and dominant -
works on 2 megapixels at most: a larger 1x picture leaves it out, and its 2x picture is made
only to the density those reach (`1.52x`); halftone draws 50,000 dots at most. An unknown
filter, an option out of its bounds - or a fraction for `levels`, `columns` and pixelate's
`cell`, which are counts - a filter after halftone or ascii, a placeholder beside them,
two pictures behind one `<img>`, an option no filter of the list reads and a filter past its
budget are errors under the strict check.

**Behavior:** writes `<img class="pad-img">` with its `width` and `height` (no layout shift),
`loading="lazy"`, `decoding="async"` and a `srcset` whose second picture is twice the size -
as far as the source reaches, the density then said as it is (`1.5x`), and left out under
1.2x. A picture is never made larger than its source. A JPEG is turned upright by its EXIF
orientation. The file name is a hash of the picture's contents and the options, so a changed
picture is a new address and the same page answers the same address on every machine; a
thumbnail is made once, written under a temporary name and renamed into place. `.gitignore`
leaves `_thumbs/` out. A missing `alt`, a file that is not there or is no picture, an
unknown `fit` or `format` and a `quality` outside 1-100 are errors under the strict check; a
`_thumbs/` directory the web server cannot write is an error, and the original picture is
written instead. The colour behind a picture still loading is `--pad-img-placeholder`
(`light-dark()`), and `.pad-img` keeps it inside its column (`max-width:100%`).

---

### pattern
A seeded SVG background or piece of generative art, drawn on the server: every pattern is a
file of `pad/subs/patterns/types/`, and the files there are the patterns there are
(`padPatternKinds()`); the subsystem is `pad/subs/patterns/`.

```html
{pattern 'topography', seed=7, width=960, height=320/}
{pattern 'dots', size=24, colors='#fdfbf7, #e0c9a6'/}
{pattern:voronoi seed=3, title='Shards of colour'/}
<div style="background-image:{pattern 'waves', as='url'/}">...</div>
```

A pattern is written two ways, the same pattern each time: the tag, `{pattern 'dots'/}`, and the
pattern: type, `{pattern:dots/}` - never bare, as its names (dots, grid, waves) are those of fields
and pages. `padPattern ( 'dots', [ 'size' => 24 ] )` draws the same in a page's PHP.

| Pattern | Draws | Its own options |
|---------|-------|-----------------|
| `stripes` | Diagonal stripes, a band of each ink | `angle` (45) |
| `dots` | Polka dots in staggered rows | |
| `grid` | Graph paper, a bolder line every fifth | |
| `checks` | A checkerboard | |
| `hexagons` | A honeycomb, each cell an ink the seed picks | |
| `triangles` | A low-poly mosaic, the inks running corner to corner | |
| `waves` | Rows of wavy lines, slanted by the seed | |
| `chevron` | Bold zigzag bands | |
| `topography` | Contour lines of a landscape the seed makes up - value noise traced by marching squares | `levels` (12) |
| `truchet` | Smith's Truchet tiles, quarter circles turned by the seed | |
| `voronoi` | A Voronoi diagram of scattered points | |
| `circles` | Circle packing, the large circles first | |
| `isometric` | Tumbling blocks, three shades of a cube | |
| `blobs` | Soft organic shapes | |
| `grain` | Film grain - the browser's feTurbulence, seeded | |
| `mesh` | A soft mesh gradient of glowing inks | |

**Every pattern:** `seed=` - a number or a word, 1 when left out - makes the picture: the same
seed the same bytes on every request and every machine (PAD's own sequence, the one of
`padFake`, put back afterwards, and every number written to a fixed precision); a tile repeated
does not change with it. `colors=` is a list, the background first and the inks after it - hex, a
name, `rgb()`, `hsl()` and their like, `none` for no background. `size=` is the motif's size in
pixels, each kind with its own default and bounds; `width=` (480) and `height=` (240) the picture's,
which covers its box (`preserveAspectRatio="xMidYMid slice"`); `opacity=` how strongly the motif
shows (1). `as='url'` writes `url(data:image/svg+xml,...)` for a style attribute - in single or
double quotes - or a style sheet: the quotes, parentheses and white space and the characters a
URL or HTML would read escaped. A pattern is decoration, `aria-hidden`, unless
`title=` names it: then it is `role="img"` with a `<title>`. Its ids are its own on the page. A
pattern is drawn on every request, so the kinds that draw a shape per place - triangles,
hexagons, truchet, chevron and waves 50,000 at most, voronoi 10,000 cells - and topography (its
grid times its levels, 1,500,000 at most) have a budget for the size, the picture and the levels
together: past it the pattern is not drawn. An unknown pattern, a colour that is none, a number
outside its bounds, an `as` other than `svg` or `url`, an option the kind does not take, content
between its tags and a pattern past its budget are errors under the strict check.

---

### video
A YouTube or Vimeo video as its poster with a play button; the player's iframe is loaded
only when the button is pressed (`subs/embeds/_lib/video.php`) - nothing of the video site runs before the
visitor asks for it. A file of `www/<application>/` is a `<video>` element. The addresses are
those the youtube and vimeo providers of the embed subsystem read; [`{embed}`](#embed) is the
general form, of more sites and without the site's thumbnail.

```html
{video 'https://www.youtube.com/watch?v=aqz-KE-bpKQ', title='Big Buck Bunny'/}
{video 'https://vimeo.com/76979871#t=12s', title='The new Vimeo player', poster='photos/still.jpg'/}
{video 'clips/sunrise.webm', title='Sunrise', poster='photos/sunrise.jpg'/}
```

**Parameters:**

| Option | Description |
|--------|-------------|
| first parameter | A YouTube address (`watch?v=`, `youtu.be/`, `/shorts/`, `/embed/`, `/live/`, youtube-nocookie.com), a Vimeo address (`vimeo.com/<id>`, `/channels/x/<id>`, `player.vimeo.com/video/<id>`) or a `.mp4`, `.webm`, `.ogv` or `.mov` file of `www/<application>/` |
| `title` | Required - the button's text, the iframe's title, the video element's name |
| `poster` | A picture of `www/<application>/` (versioned as `{asset}`) in place of the site's thumbnail |
| `at` | The moment to start at - `90`, `1m30s`, `1:30`; a `t=`, `start=` or `#t=` in the address says it too |
| `ratio` | The proportion, default `16:9`; a short is `9:16` and kept to a phone's width |

**Behavior:** the poster of a YouTube video is its thumbnail on i.ytimg.com (an image, no
script or cookie); a Vimeo video without `poster=` gets a quiet drawn poster. The button
holds the embed address - youtube-nocookie.com with `autoplay=1&rel=0`, or player.vimeo.com
with `dnt=1` - and a small script, written once per request with the CSP nonce when `$padCsp`
asks for one, puts the iframe in the poster's place on a click and moves the focus into it.
Without script the button is hidden (`@media (scripting:none)`) and a `<noscript>` link to the
video on its own site stands in its place - inside `<noscript>`, so a video in a card that is
itself a link stays valid HTML. A file is `<video controls preload="none" playsinline>` with
its poster: nothing is loaded before play. The colours are `--pad-video-*` custom properties
with `light-dark()` steps on `.pad-video`. An address that is no known video and a missing
`title` are errors under the strict check.

---

### embed
A video, a sound, a map, a pen or a document of another site behind a click-to-load facade -
the embed subsystem in `pad/subs/embeds/` - nothing of the provider is asked for before the visitor
presses the button; a file of the application is an element that loads nothing before play.

```html
{embed 'https://www.youtube.com/watch?v=aqz-KE-bpKQ', title='Big Buck Bunny'/}
{embed:vimeo '76979871', title='The new Vimeo player', at='1:30'/}
{openstreetmap '52.3731,4.8922', zoom=15, title='Dam Square'/}
{embed 'https://codepen.io/team/codepen/pen/PNaGbb', title='A pen', tab='html,result'/}
{audio 'sounds/bell.mp3', title='The bell'/}
{embed 'docs/leaflet.pdf', title='The leaflet'/}
```

**Parameters:**

| Option | Description |
|--------|-------------|
| first parameter | An address a provider knows - a file of `pad/subs/embeds/types/`: `youtube`, `vimeo`, `peertube` (any instance: `/w/<id>`, `/videos/watch/<uuid>`), `dailymotion`, `internetarchive` (archive.org/details/), `soundcloud`, `spotify` (open.spotify.com, `spotify:track:<id>`), `codepen`, `openstreetmap` (`#map=z/lat/lon`, `?mlat=&mlon=`), and by extension `videofile`, `audio` and `pdffile`, of `www/<application>/` or of the web. Named - `{embed:youtube 'id'/}` or bare `{youtube 'id'/}` - a bare id of that provider as well, and for openstreetmap `'latitude,longitude'` |
| `title` | Required - the button's text, the frame's title, the element's name |
| `poster` | A picture of `www/<application>/` (versioned as `{asset}`) in place of the drawn poster |
| `thumbnail` | Bare: the provider's own picture as the poster where it has one at a known address (YouTube) - asked of the provider before the click |
| `ratio` | The proportion - `16:9`, SoundCloud and Spotify `2:1`, a PDF `4:5`, a YouTube short `9:16` (kept to a phone's width) |
| `at` | youtube, vimeo, peertube, dailymotion: the moment to start at - `90`, `1m30s`, `1:30` |
| `lat`, `lon`, `zoom` | openstreetmap: the place and the zoom, 1 to 19 (13) |
| `tab` | codepen: the panes - `result` (default), `html`, `css`, `js`, or two of them |

**Behavior:** a frame of another site is a facade first: a poster drawn for its kind (video,
audio, map, code, document), the title, "Plays from YouTube" / "Loads from OpenStreetMap" - a
PeerTube instance and the site of a PDF by their host - and a button holding the frame's
address in `data-pad-embed`. A script written once per page with the CSP nonce puts the frame
in the poster's place on a click, with the provider's `allow`, and moves the focus into it;
without script the button is hidden (`@media (scripting:none)`) and a `<noscript>` link to the
address on its own site stands in. The players come privacy-friendly where the site offers it:
youtube-nocookie.com, Vimeo with `dnt=1`. A file of the application is `<video controls
preload="none">`, `<audio controls preload="none">` named by the title under it, or for a PDF
a frame of the application loaded lazily with a link to open it; of the web, a PDF is a facade
too. A proportion other than 16:9 is a class with its own nonce'd rule - no `style=`
attribute, so a strict `style-src` holds. The colours are `--pad-embed-*` custom properties
with `light-dark()` steps on `.pad-embed`.

**Providers:** a provider is a file of `pad/subs/embeds/types/<name>.php` answering its `name`,
`kind`, the `frame` origin(s), whether it is `generic` (known by the shape of its addresses,
tried after those that know their host), its own `options`, `match` - the address read into
its parts, or NULL - and `embed` - the frame's address, the link, the ratio, the `allow`. The
files there are the providers there are (`padEmbedProviders`), and a provider is added by
adding its file. A bare provider name comes after every other kind of tag, so a tag, a store
or a field of the name wins.

**Content Security Policy:** the frames need the providers' origins in `frame-src` -
`padEmbedSources ()` (`lib/embed.php`) lists the ones the files name:
`$padCsp = "default-src 'self'; script-src 'self' 'nonce'; style-src 'self' 'nonce'; frame-src " . implode ( ' ', padEmbedSources () );`.
A PeerTube instance is added by hand, so is `img-src https://i.ytimg.com` for `thumbnail` and
`media-src` for a sound or a video of another site. An address no provider knows, a missing
`title`, a provider's id or place it cannot read and an option of another provider are errors
under the strict check; `{embed:zigzag}` is "there is no embed provider named 'zigzag'".

---

### pdf
What the content renders, sent as a PDF instead of the page - a writer in plain PHP
(`tags/_lib/pdf.php`): PDF 1.4, the standard fonts every reader has, no library, no browser.

```html
{pdf file='invoice-2026-114', size='A4'}
  <h1>Invoice {$number}</h1>
  <table>{lines}<tr><td>{$item}</td><td align="right">{$amount}</td></tr>{/lines}</table>
{/pdf}
{pdf preview, file='invoice'}...{/pdf}      # the content on a sheet, links to ?page&padPdf
{pdf download/}                              # single: the whole page as an attachment
```

**Parameters:**

| Option | Description |
|--------|-------------|
| `file` | The file name of the document, default the page's name (`.pdf` is added) |
| `download` | Sent as an attachment (`Content-Disposition: attachment`) instead of shown inline |
| `size` | `A4` (default), `Letter`, `A5` or `Legal` |
| `landscape` | The page turned |
| `font` | `helvetica` (default), `times` or `courier` - code is always Courier |
| `title` | The document's title, default its first heading |
| `preview` | The page stays a page: the content on a sheet with links "Open name.pdf" and "Download" - the same page asked with `&padPdf` (`padPdf=download` for the attachment) answers the PDF |

**Behavior:** a pair renders its content as ever, and the page's response becomes the PDF of
it (`application/pdf`, `Content-Disposition` with the name) - exits/exits.php makes it in
place of the page, tidy and the page cache left out; several `{pdf}` pairs are one document,
the first one's options counting. The HTML read: `h1`-`h6`, `p`, `b`/`strong`, `i`/`em`,
`u`, `code`, `a` (blue, and a link of the document for an http, https or mailto address),
`br`, `ul`/`ol`/`li` (nested), `blockquote`, `pre`, `hr`, `table`/`tr`/`th`/`td` (a grid with
`colspan`, `align=` or `text-align`, the columns as wide as their content asks, the header
row repeated on every page) and `img` - a JPEG of `www/<application>/` or a `data:` address,
embedded as it is; another picture is its alt text, any other element its text, and script,
style, svg and form fields are left out. The text is WinAnsi (a `?` for what it lacks) and
measured with the AFM widths of Helvetica and Times, so lines wrap where a reader draws
them; pages get margins and, past one, "2 / 5" at the foot. Only the page a request asked
for answers a document: inside a `{page}` the content is shown on its sheet (the
`--pad-pdf-*` colours, `light-dark()`), and a data (json, csv) or live answer goes first.
The document carries no date, so the same page is the same bytes. An unknown `size` or
`font` is an error under the strict check. A printable's sheets (`{printable}`) are pages of their
own size in the document, laid out by place: their boxes, rectangles and lines where the screen has
them, and in their boxes the SVG of a barcode or a QR code drawn. From PHP: `padPdf ( $html, [ 'size' => 'Letter' ] )`
answers the bytes.

---

### diagram
A diagram from a few lines of text in the content, written the Mermaid way and laid out on
the server as inline SVG by the diagram subsystem (`pad/subs/diagrams/`) - no JavaScript. A kind
is a file of `pad/subs/diagrams/types/`: `flowchart` (the default), `sequence`, `state`, `class`,
`er`, `mindmap`, `tree`, `journey`, `gitgraph`, `fishbone`, `railroad` and `packet`.

```html
{diagram}
  A([Order placed]) --> B{In stock?}
  B -->|yes| C[Take payment] --> D((Shipped))
  B -.->|no| E[(Supplier)]
{/diagram}

{diagram type='sequence'}
  Shop ->> Bank: Charge
  Bank -->> Shop: Approved
{/diagram}

{diagram:state}
  [*] --> Idle
  Idle --> Running : start
  Running --> [*]
{/diagram:state}

{diagram:class}
  class Animal {
    +String name
    +move()* bool
  }
  Animal <|-- Duck
{/diagram:class}

{diagram:er}
  CUSTOMER ||--o{ ORDER : places
  ORDER {
    int id PK
  }
{/diagram:er}

{mindmap}
  PAD
    Templates
    Data
{/mindmap}

{diagram:tree}
  Board
    Sales
    Engineering
{/diagram:tree}

{journey}
  section Morning
    Make tea: 5: Me
{/journey}

{gitgraph}
  commit
  branch develop
  commit id: "Parser"
  checkout main
  merge develop tag: "v1.0"
{/gitgraph}

{fishbone}
  Late trains
    Weather
      Snow on the points
    Staff
{/fishbone}

{railroad}
  list ::= '[' ( item ( ',' item )* )? ']'
  item ::= number | 'null'
{/railroad}

{packet}
  title UDP header
  0-15: "Source Port"
  +16: "Destination Port"
{/packet}
```

**Parameters:**

| Option | Description |
|--------|-------------|
| `type` | The kind, a file of `pad/subs/diagrams/types/` - `flowchart` (default; `flow` and `graph` say so too). A first line written the Mermaid way names it as well: `sequenceDiagram`, `stateDiagram` / `stateDiagram-v2`, `classDiagram`, `erDiagram`, `gitGraph` whatever the tag says, and a kind's name alone (`mindmap`, `journey`, `fishbone`, `railroad`, `packet`, Mermaid's `ishikawa-beta` and `packet-beta`) where the tag names none |
| `direction` | flowchart, state, class, er and tree: `TB` (default), `BT`, `LR` or `RL` - else the lines say it: a first line `graph LR` / `flowchart LR` in a flowchart, a line `direction LR` in a state, class or entity-relationship diagram |
| `bits` | packet: the bits in a row, `32` unless given, 4 to 64 |
| `edges` | flowchart, state, class and er: `orthogonal` (default, rounded turns in channels of their own), `straight` or `curved` |
| `title` | The accessible name, default the kind's (`Flowchart`, `Sequence diagram`, `State diagram`, `Class diagram`, `Entity-relationship diagram`, `Mind map`, `Tree`, `Commit graph`, `Fishbone diagram`, `Railroad diagram`; a journey's `title` line, else `User journey`; a packet's, else `Packet diagram`) |

A kind reads only the options it draws with: `direction` on a sequence diagram is an option
nothing reads, which the strict check reports.

**The kind as the tag:** `{diagram:state}...{/diagram:state}` is `{diagram type='state'}...{/diagram}`,
and so is the bare `{state}...{/state}` when no tag, store, field or sequence has the name -
the kinds come after every other kind of tag. `sequence` and `tree` are built-in tags too, so
bare they are those tags: the sequence diagram and the tree are `{diagram:sequence}` and
`{diagram:tree}` (or `type=`). `{diagram:zigzag}` is "there is no diagram type named 'zigzag'".

**Behavior:**

- **flowchart** - a line is a chain of nodes and edges: `A[Box]`, `B(Rounded)`,
  `C([Stadium])`, `D{Decision?}`, `E((Circle))`, `F[(Database)]`, `G{{Hexagon}}` - a quoted
  text keeps its brackets, `<br>` breaks a line, long texts wrap - joined by `-->` (arrow),
  `---` (line), `-.->` / `-.-` (dashed), `==>` / `===` (thick), a label as `-->|text|` or
  `-- text -->`, `&` for several nodes on one side (`A & B --> C`), `%%` a comment. The layout
  is layered: cycles broken by a depth-first walk, ranks by the longest path, a dummy node per
  rank a long edge crosses, the order in a rank by barycentre sweeps keeping the fewest
  crossings, positions by isotonic regression; node sizes follow their text, ports spread
  along a side, labels sit on a plate half way and step aside for each other.
- **sequence** - `participant A as Alice`, `actor U`, messages `->>` (arrow), `-->>` (dashed),
  `->` / `-->` (no head), `-x` (cross), `-)` (open), `Note left of|right of|over A[,B]: text`,
  frames `loop`, `alt` / `else`, `opt`, `par` / `and`, `critical`, `break` closed by `end`, and
  `autonumber`.
- **state** - `[*] --> A` (the start), `A --> B : label`, `A --> [*]` (the end), `state "Long
  name" as A`, `A : a line of description` (under the name), `state C <<choice>>` (a small
  diamond), `<<fork>>` and `<<join>>` (a bar across the flow); laid out as a flowchart. A
  state inside another and a note are lines it cannot read.
- **class** - `class A { ... }` with a member a line (one with `(` a method; `+ - # ~` the
  visibility; a `$` at the end - or after the `()` - static, underlined; a `*` abstract, in
  italics; `List~int~` is `List<int>`), `A : +member`, `<<interface>> A` (the stereotype),
  and the relations `A <|-- B` (B inherits from A, a hollow triangle), `A <|.. B` (realizes),
  `A *-- B` (made of, a filled diamond), `A o-- B` (holds, a hollow one), `A --> B`
  (association), `A ..> B` (depends on), `A -- B` / `A .. B` (a link), the multiplicities in
  quotes by each end (`A "1" --> "*" B`) and a label after `:`. The parent and the whole are
  laid out above, whichever side they are written on.
- **er** - `A ||--o{ B : label` (a quoted label too): at each end how many of that side's
  entity go with one of the other - `||` exactly one, `|o` / `o|` zero or one, `}|` / `|{`
  one or more, `}o` / `o{` zero or more - drawn as the feet of a crow; `--` identifying, `..`
  not (dashed). `A { type name PK "comment" }` - an attribute a line, the keys `PK`, `FK`,
  `UK` - makes the entity a table.
- **mindmap** - the hierarchy from the indentation: the first line the root, a line indented
  under its parent, a shape by its brackets - `[square]`, `(rounded)`, `((circle))`,
  `{{hexagon}}`, `)cloud(`, `))bang((` - with an id before it or not; `::icon(...)` lines and
  `:::class` are Mermaid's own styles and left out. The root's branches are shared over its
  two sides, as many leaves on each as can be, read round the clock; each branch a colour of
  the chart palette its twigs keep - a branch a box, a twig its text over a line. Two lines
  at the root's indentation are an error.
- **tree** - the hierarchy from the indentation, top down: boxes joined by lines with
  rounded turns, each centred over its children; several lines at the least indentation are
  several roots side by side.
- **journey** - `title text`, `section text`, and a step a line, `Make tea: 5: Me, Cat` - its
  score from 1 (bad) to 5 (good), the people after it. Cards in a row under the bands of
  their sections, a face under each at the height of its score (a smile, a straight mouth, a
  frown) joined by a line, a dot per person in the colour the legend gives. A score past 1
  to 5 is an error.
- **gitgraph** - `commit` (`id: "name"`, `tag: "v1"`, `type: HIGHLIGHT` square or `REVERSE`
  crossed out), `branch name` (`order: n` moves its lane; checked out at once), `checkout
  name` / `switch name`, `merge name` (a commit with a ring) and `cherry-pick id: "name"`;
  `main` is the branch at the start. A lane a branch in its colour, a column a commit, left
  to right; the turns between lanes stand between the columns, clear of the names. An unknown
  branch, a commit id twice and a merge into itself are errors.
- **fishbone** - the cause-and-effect diagram of Ishikawa, the hierarchy from the indentation
  as Mermaid's `ishikawa-beta` has it: the first line the effect, drawn in a head on the right
  that the spine runs into; a line under it a category, a bone above or below the spine by
  turns (the first on the left), leaning toward the head, its name in a box at the outer end;
  a line under a category a cause - its text over a rib that ends on the bone - and further in
  a cause of that cause, hanging off its parent's rib by a stem. Each bone reads from its
  category toward the spine, so the half below the spine mirrors the half above; the bones of
  a side stand as close as their texts allow, band by band. A second line at the effect's
  indentation is an error.
- **railroad** - syntax diagrams of a grammar in the EBNF of the W3C (XML 1.0, section 6
  Notation), the notation the railroad diagram generators read: `name ::= expression`, a rule
  on one line or more; `"text"` or `'text'` a terminal (a rounded box, a fixed-width face),
  a name a rule (a square box, a link to that rule's track when the grammar has it),
  `[a-z]`, `[^"]` and `#x20` a character class, `( )` a group, `A B` one after the other,
  `A | B` a choice (stacked, the first on the track), `A?` optional (a way over it), `A+` one
  or more (a way back under it), `A*` the two, `A - B` A but not B (written under it),
  `/* comments */`; a constraint `[ wfc: ... ]` / `[ vc: ... ]` is left out. Every part is
  laid out from its size - its width, how far it reaches above and below the track - so no
  two meet; a rule wider than 640 wraps its sequence onto rows under each other. A text
  before the first rule, a rule named twice, a group or a quote never closed, an empty
  branch of a choice and an operator with nothing before it are errors that name the line.
- **packet** - the fields of a packet header, a frame or a register by their bits, the
  Mermaid way: `0-15: "Source Port"` (bits 0 to 15), `106: "URG"` (one bit), `+16: "Window"`
  (the next 16 bits), `title text` a heading; in rows of `bits=` bits (32), bit 0 on the
  left as the RFCs draw a header. A field running past its row goes on in the next, its name
  in every piece; the first and the last bit stand over every piece, as many as fit; a name
  too wide for its field wraps, or turns on its side in a narrow one, the row as high as it
  asks. A field that does not start where the one before ended (a gap or an overlap), ends
  before it starts, or a line of no field are errors; `bits` outside 4 to 64 is too.

The content is read as it stands - the braces of `B{Paid?}` and `class A {` need no
`{ignore}`. role="img" with a `<title>` and a `<desc>` that says every edge, message,
relation, branch, step or commit in words; the colours are `--pad-diagram-node`, `-border`,
`-decision`, `-decision-border`, `-round`, `-round-border`, `-edge`, `-note` and the journey's
`-score-1` to `-score-5` and `-face` on `.pad-chart-diagram`, `light-dark()` defaults; the mind
map, the journey's people and the commit graph's lanes take the chart palette
(`--pad-chart-1` to `-8`). The same lines always draw the same picture. A diagram without
nodes answers nothing and its `@else@` shows; a line it cannot read, an unknown type,
direction or edges value are errors under the strict check.

---

### map
A choropleth map as inline SVG - every country coloured by a value of its row, drawn on the
server by the map subsystem in `pad/subs/maps/` from Natural Earth's outlines (`subs/maps/_lib/map.php`,
`subs/maps/_lib/world.json`) - no JavaScript, no tiles, no remote service.

```html
{map 'world', data='sales', key='country', value='amount'/}
{map 'europe', data='visitors', scale='quantile', title='Visitors per country'/}
{map 'benelux', data='shops', key='country', value='count'/}
{map 'south-america', key='code', value='forest', width=320}
  code,forest
  BRA,59.1
{/map}
```

**Parameters:**

| Option | Description |
|--------|-------------|
| first parameter | The view, a file of `pad/subs/maps/types/`: `world` (default), the continents `europe`, `africa`, `asia`, `north-america`, `south-america`, `oceania`, and the regions `middle-east`, `mediterranean`, `eu`, `nordic`, `benelux`, `baltic`, `british-isles`, `iberia`, `dach`, `balkans`, `central-america`, `caribbean`, `east-asia`, `southeast-asia`, `south-asia`, `central-asia` |
| `data` | The rows - a `{data}` store, a page array or a `_data` file, as for `{chart}`; as a pair the content is the rows (JSON, YAML, XML or CSV). A row that is a value alone takes its key as the country: `[ 'NL' => 1200, 'BE' => 800 ]` |
| `key` | The field with the country: its ISO 3166 alpha-2 or alpha-3 code or its English name, in any case - else the first field that is no number |
| `value` | The number - else the first numeric field |
| `scale` | `linear` (default: seven steps from the lowest value to the highest) or `quantile` (seven steps by rank) - of the values the view draws |
| `width`, `height` | Default 720 wide; the height follows from the view unless given |
| `title` | The accessible name, default `<Value> by country` |

**Behavior:** rows of one country add up; a key no country has is named in the title
(`not on the map: ...`), not drawn. The colours are the heatmap's seven steps
(`--pad-chart-heat-0` to `-6`) with a scale under the map, a country without a value in
`--pad-map-empty`, the borders `--pad-map-border` (`light-dark()` defaults on
`.pad-chart-map`); every country has a `<title>` - `Netherlands: 1,234` - and the `<desc>`
lists the values. The outlines: Natural Earth 1:50m admin-0 countries (public domain) through
world-atlas@2.0.2 (ISC), codes and names from i18n-iso-countries (MIT), in the Natural Earth
projection; a region view is a crop of the same projection at full detail, the world view
uses paths simplified for its scale; a country too small to keep its outline is a small
diamond. Without data the map is drawn in grey. An unknown view or scale is an error under
the strict check, which lists the views there are.

**Views:** a view is a file of `pad/subs/maps/types/<view>.php` answering its `box` - west, south,
east, north in degrees - and for a region that is a list of countries rather than a stretch
of the globe its `countries` (alpha-2 codes) and the `name` the map's title takes; the files
there are the views there are (`padMapViews`), and a view is added by adding its file. A view
draws the countries with a part in its box - the polygons of each that lie in it; with a list
of countries, the others are the land around them, drawn without a value in
`--pad-map-context`, and a value of theirs is named in the title (`not in this view:
Germany`), as one of a country outside the box is - and is left out of the scale, the legend
and the description, which are the values the map draws.

---

### timeline
Events on a time axis as inline SVG (`tags/_lib/timeline.php`) - cards above and below the axis,
spans as bars, or one under the other for a long list.

```html
{timeline data='releases', date='when', label='what', text='detail'/}
{timeline data='plan', from='start', to='end', date='day', label='name', color='kind'/}
{timeline date='year', label='what', vertical}
  year,what
  1969,Moon landing
{/timeline}
```

**Parameters:**

| Option | Description |
|--------|-------------|
| `data` | The rows - a `{data}` store, a page array or a `_data` file, as for `{chart}`; as a pair the content is the rows (JSON, YAML, XML or CSV) |
| `date` | The field of a moment: a year (`1969`), a month (`2026-10`) or anything `strtotime` reads - else the first field that reads as one |
| `from`, `to` | The fields of a span, drawn as a bar |
| `label`, `text` | The title of an event and the line under it - else the first other text fields |
| `color` | A field that groups the events: a colour each and a legend |
| `format` | A PHP date format for the dates written - else by the date's precision (`1969`, `Oct 2026`, `Oct 8, 2026`) |
| `vertical` | Dates on the left, cards on the right, one under the other |
| `width`, `height` | Default 720 (vertical 600) wide; the height follows from the cards unless given (not in the vertical form) |
| `title` | The accessible name, default `Timeline` |

**Behavior:** the axis has round ticks - years in steps of 1 to 100, months of 1, 3 or 6, or
days - by the span. A moment gets a dot on the axis and a card above or below it, the sides
alternating; a card that would cover another card or a leader line moves to the other side
or a lane further out, hanging right or left of its leader. Spans lie as bars in lanes under
the axis, their label inside or beside. The colours are the chart's categorical slots and
`--pad-timeline-card`, `-border`, `-line` and `-muted` on `.pad-chart-timeline`
(`light-dark()` defaults); every event has a `<title>` and the `<desc>` lists them all. A
timeline without events answers nothing and its `@else@` shows; rows of which none has a
date are an error under the strict check.

---

### datatable
Rows as a complete, accessible HTML table (`tags/_lib/datatable.php`) - a header from the field names,
numbers right-aligned and formatted alike down their column, a totals row, header links that
sort and page links that page, all on the server.

```html
{datatable data='orders', columns='number, customer, total', totals='total'/}
{datatable data='orders', format="total:currency('EUR'), date:date('j M')", sortable, striped/}
{datatable data='cities', rows=10, sortable, caption='Largest cities'/}
{datatable totals='amount'}
  month,amount
  Jan,12400
{/datatable}
```

**Parameters:**

| Option | Description |
|--------|-------------|
| `data` | The rows - a `{data}` store, a page array or a `_data` file, as for `{chart}`; as a pair the content is the rows (JSON, YAML, XML or CSV, told apart on sight, `type=` names it) |
| `columns` | The fields shown and their order, default every field of the first row |
| `labels` | The header of each column, in the order of `columns` - default the field name as a headline (`order_date` is Order Date) |
| `format` | A pipe per column, `column:pipe` - `total:money, date:date('j M Y')` - run as `{echo $value \| pipe/}` |
| `totals` | Columns summed - over every row, not only the page shown - in a totals row with Total in its first cell |
| `sortable` | Each header a link that sorts on the server: `?sort=total&dir=desc` on the same page |
| `rows` | Rows per page, with page links below (`?page=2`) made from the `{pager}`'s items |
| `query` | A prefix for the three request names - `query='o_'` reads `o_sort`, `o_dir`, `o_page` - for two tables on one page |
| `striped` | Every other row shaded |
| `caption` | The table's `<caption>` |

**Behavior:** a column whose filled values are all numbers is right-aligned, shows the most
decimals any of its values has and is grouped by thousands - unless its name says it identifies
(`id`, `number`, `no`, `code`, `year`, `zip` ...), then the digits stand as they are. A sort
accepts only a column the table shows (anything else leaves the order as it came) and is stable;
a header link sorts ascending, on the sorted column the other way, and goes back to page 1;
the sorted column carries `aria-sort`. The links keep the request's other values (not the
engine's `pad*`) through `$padGo`. Everything from the data is escaped. Colours are
`--pad-datatable-*` custom properties with `light-dark()` defaults, the style written once per
page. No rows answers nothing and the `@else@` shows; no data, a column no row has, a `totals`
or `format` naming no column, a total of a column that is not numbers, labels that do not match
the columns and `rows` below 1 are strict errors.

---

### spreadsheet
A grid of cells whose formulas are computed on the server, as an accessible table
(`subs/formulas/spreadsheet.php`) - the formula language is spreadsheet syntax with a parser of
its own, its functions the files of `pad/subs/formulas/types/` (SUM, IF, VLOOKUP, PMT ... 126 of
them), answering what Excel answers. See [Spreadsheets](../FEATURES.md#spreadsheets).

```html
{spreadsheet 'shop', formats='B:0.00, D:€ #,##0.00', totals='D'}
  Item,Price,Qty,Total
  Apples,1.20,3,=B2*C2
  Pears,0.80,5,=B3*C3
{/spreadsheet}
{spreadsheet data='orders', formulas='total=B2*C2, F1=SUM(D:D)'/}
{live 'calc'}{spreadsheet 'calc', editable='B1:B3'}...{/spreadsheet}{/live}
```

**Parameters:**

| Option | Description |
|--------|-------------|
| first parameter | The sheet's name - its edits post as `padSheet[<name>][B2]`; unnamed `sheet1`, `sheet2` ... |
| `data` | The cells: records under a first row of their field names (their values never formulas), a list of rows of values, or cells by name (`[ 'A1' => 10, 'B1' => '=A1*2' ]`); as a pair the content is the cells, a line a row, a comma between cells (a tab when a line holds one) - read as written, its tags not run |
| `formulas` | Formulas put in: `total=B2*C2` fills a column (named in row 1, else a new one) down every row below the first, the references moving as a fill moves them; `D2:D9=B2*C2` fills a range; `F1=SUM(D:D)` one cell |
| `totals` | A row below the last with the SUM of the columns named (letters or names of row 1), Total in the first column |
| `formats` | A format code per column, cell or range - `B:0.00, total:€ #,##0.00, B5:B8:0.0%` - the later deciding; a code keeps its commas, `A:mmm d, hh:mm`, as the next item starts only where a column of the sheet, a cell or a range and its colon follow one; see TEXT |
| `headers` | The column letters and row numbers; `headers=FALSE` leaves them out |
| `caption` | The table's `<caption>` |
| `editable` | Cells as fields: alone every cell holding a number, a date or TRUE/FALSE; `editable='B2:C9, F1'` the cells named, formulas too (their value in an `<output>` under them) |
| `seed` | The seed of RAND, 0 unless given - the same numbers on every request |

**Behavior:** a cell typed `=...` is a formula, `42` and `1,250.5` numbers, `12%` a number shown
as a percentage, `2024-01-15` a date (the serial of the 1900 system), `$4.50` an amount, TRUE and
FALSE logical values, `'...` a text - a number past what a float holds (`1e400`) stays the text
typed and one below 2.2251E-308 is 0, as in Excel; a column without a format shows a cell as it was typed, a
formula as its first cell (DATE, EOMONTH a date), else General. Numbers stand right, errors are
marked (`class="err"`) and explained in their title; a formula's cell carries it in `title` and
`data-formula`, its number in `data-value`. A circle of references answers `#REF!` in every cell
of it, the title naming the circle (`A1 → B1 → A1`). `editable` cells are `<input>`s labelled by
the cell and the texts heading its row and column: in a `{form}` they post with it, in a `{live}`
region the sheet is a form (`pad-submit`) a change submits - every cell's text once - and the
region comes back computed, elsewhere the sheet is a form of its own with a Recalculate button; a post
types into the cells named only, a formula too, 1000 characters at most. Colours are
`--pad-sheet-*` custom properties with `light-dark()` defaults, the style written once per page.
Under the strict check a formula of the template that cannot be read - or calls no function of
`types/`, or with the wrong number of arguments - is an error naming its cell and its position;
so are no cells, a name that is no name, and an option naming no cell, range or column.
Without the check, and for a posted formula, the cell shows `#ERROR!`, `#NAME?`, `#N/A` or
`#VALUE!` (a SUMPRODUCT asking for array evaluation), the reason in its title.

---

### toc
A table of contents of the page from its own rendered headings (`tags/_lib/toc.php`), nested and linked,
written where `{toc}` stands once the whole page has rendered - the headings below it included.

```html
{toc/}
{toc levels='2-4', title='On this page', list='ul'/}
```

**Parameters:**

| Option | Description |
|--------|-------------|
| `levels` | The heading levels listed, `'2,3'` by default - a list or a range, `'2-4'` |
| `title` | The label above the list, `Contents` by default; `''` for none |
| `list` | `ol` (numbered, the default) or `ul` |

**Behavior:** the tag prints a marker, as `{stack}` does, and the marker is replaced once the page
has rendered - from `exits/exits.php` for the request's page, and when a `{page}` pass ends for a
page it renders, so a `{toc}` lists the headings of the page that holds it. A heading without an
id gets one - the slug of its text, `-2`, `-3` behind a repeat, never an id the page has - and a
heading with an id keeps it. One level deeper nests one list deeper, never more than one step in.
The nav is labelled by its title, a paragraph rather than a heading, so it lists itself nowhere.
No heading of those levels writes nothing. Colours are `--pad-toc-*` custom properties with
`light-dark()` defaults. Levels outside 1-6 and a list other than `ol`/`ul` are strict errors.

---

### countdown
The time left until a moment, in words, inside a `<time datetime>` (`tags/_lib/countdown.php`) -
optionally ticking every second in the browser.

```html
{countdown '2026-12-31 00:00'/}                         <!-- 84 days, 3 hours -->
{countdown $launch, units=3, live, past='We are live!'/}
```

**Parameters:**

| Option | Description |
|--------|-------------|
| first parameter | The moment - a date in text, a Unix timestamp or a date object |
| `units` | How many units are shown, 1 to 4 (days, hours, minutes, seconds), default 2 |
| `past` | The text once the moment has gone, default `ended` |
| `live` | A small script, once per page, counts down every second from the browser's clock |
| `now` | Count from this moment instead of now |
| `format` | The PHP date format of the full moment in the `title`, default `l j F Y, H:i` |

**Behavior:** the words start at the largest unit that is not zero and take `units` of them,
a zero one among them left out: `84 days, 3 hours`, `5 minutes, 12 seconds`, `1 day`. Now is
`padNow`'s, so `padNowFreeze` (or `now=`) holds a test still. `live` adds `role="timer"` and the
moment in milliseconds; the script counts the same way and carries this request's nonce when
`$padCsp` asks for one. A moment that is no date and `units` outside 1-4 are strict errors.

---

### timeago
How long ago a moment was, in words, inside a `<time>` with the moment as its `datetime` and, in
full, as its `title` (`tags/_lib/timeago.php`) - the `ago` pipe's words with the semantic element.

```html
{timeago $created/}        <!-- <time datetime="2026-10-08T09:10:00+02:00" title="Thursday 8 October 2026, 09:10">5 minutes ago</time> -->
{timeago $posted, format='j M Y'/}
```

**Parameters:**

| Option | Description |
|--------|-------------|
| first parameter | The moment - a Unix timestamp, a date in text or a date object |
| `now` | Count from this moment instead of now |
| `format` | The PHP date format of the `title`, default `l j F Y, H:i` |

**Behavior:** the words are `padAgo`'s - just now, 5 minutes ago, yesterday, 3 weeks ago, in 2
days - counted from `padNow`, so `padNowFreeze` holds a test still. An empty moment answers
nothing; a moment that is no date is a strict error.

---

### emoji
An emoji by its GitHub or Slack shortcode, as a labelled image (`tags/_lib/emoji.php`); the `emoji` pipe
replaces the shortcodes in a text.

```html
{emoji 'rocket'/}                   <!-- <span class="pad-emoji" role="img" aria-label="rocket">🚀</span> -->
{emoji ':tada:', label='Party!'/}
{$comment | emoji}                 <!-- 'Shipped :rocket:' becomes 'Shipped 🚀' -->
```

**Parameters:**

| Option | Description |
|--------|-------------|
| first parameter | The shortcode, with or without colons, any case - `+1`, `heart`, `white_check_mark` |
| `label` | The name a screen reader says, default the emoji's name |

**Behavior:** a few hundred common shortcodes with their aliases - faces, hands, people at work,
animals, food, travel, objects and symbols, keycaps. An emoji with a text form as well (❤ ☀ ✔)
carries the variation selector that asks for the colour picture. The pipe leaves a shortcode it
does not know, and a time like `10:30:00`, as written. An unknown shortcode in the tag is a
strict error that names the nearest one (`did you mean ':rocket:'?`).

---

### country
A country's flag from its ISO 3166-1 code, optionally with its English name (`tags/_lib/country.php`).

```html
{country 'NL'/}             <!-- <span class="pad-country" role="img" aria-label="Netherlands">🇳🇱</span> -->
{country 'NLD', name/}      <!-- the flag and Netherlands beside it -->
{country $code, name/}
```

**Parameters:**

| Option | Description |
|--------|-------------|
| first parameter | An alpha-2 code (`NL`), an alpha-3 code (`NLD`) or the English name, any case |
| `name` | Writes the name beside the flag; the flag is then decoration (`aria-hidden`) |

**Behavior:** the flag is the code written as two regional indicator symbols, which a system with
flag emoji draws as the flag and one without (Windows) as the two letters. The table holds the
249 entries of ISO 3166-1 with alpha-2, alpha-3 and the English name as CLDR gives it;
`padCountry($code)` answers `[ alpha-2, alpha-3, name ]` to PHP. A code that is no country is a
strict error that names the nearest country when the text is close to a name.

---

### jsonview
A JSON value or a PHP array as a tree to fold open and shut (`tags/_lib/jsonview.php`) - each object and
list a `<details>`, no script.

```html
{jsonview $response, open=1, title='GET /orders/42'/}
{jsonview data='settings'/}
{jsonview}
  {"id": 42, "paid": true, "lines": [ ... ]}
{/jsonview}
```

**Parameters:**

| Option | Description |
|--------|-------------|
| first parameter | The value - a `$field` holding a list (a page array, a row) or a text; a text that is a JSON object or list is decoded |
| `data` | A `{data}` store, a page array or a `_data` file by name - a `.json` file read as it is written |
| `open` | The levels shown unfolded, default 2; 0 folds everything |
| `title` | A label for the outermost level |

**Behavior:** as a pair the content is the JSON, taken as it stands, so its braces need no
`{ignore}`. The tag reads its items raw, as `{debug}` does, so a `$field` that holds an array is
that array. Keys, strings, numbers, booleans and null each have a colour - `--pad-jsonview-*`
custom properties with `light-dark()` defaults, the style written once per page; a folded node
says how many keys or items it holds. Content that is no JSON, an unknown option, a second value,
a `data` name that names nothing and no value at all are strict errors.

---

### section
A block of a landing page - a hero, the features, the prices, what customers say, the questions,
a call to action, the numbers, the steps, the team, the logos, a newsletter form, the contact
details, a gallery or the footer - in HTML and CSS alone. Every section is a file of
`pad/subs/sections/types/`, its style in `www/assets/sections/` (`section.css`, shared, and a
sheet per kind, each written once per page with the CSP nonce); the files of `types/` are the
sections there are, and the subsystem is `pad/subs/sections/`.

```html
{section 'hero', 'Pages that drive themselves', button='Start', link='?start', second='Read more', secondLink='?docs'}
  Write the page, and the page renders itself.
  {slot 'eyebrow'}New in PAD{/slot}
  {slot 'image'}{placeholder ratio='4:3', fluid/}{/slot}
{/section}
{section:features title='Why PAD', columns=3}
  {section:item 'Fast', icon='zap'}Pages render in milliseconds.{/section:item}
  {section:item 'Safe', icon='shield-check'}Every value is text.{/section:item}
{/section:features}
{section:pricing title='Plans', data='plans', note='Without VAT.'/}
{section:faq title='Questions', open=1, single}
  {section:item 'Is it free?'}Yes, under the GPL.{/section:item}
{/section:faq}
{section:newsletter 'Stay in the loop', button='Subscribe'/}
{section:footer brand='PAD', note='© 2026 PAD'}
  {section:item 'Product'}
    Features | ?features
    Pricing | ?pricing
  {/section:item}
{/section:footer}
```

A section is written two ways, the same section each time: the tag, `{section 'hero', ...}`, whose
second value is the section's first, and the section: type, `{section:hero ...}`. It is never
reached bare - hero, team, stats and footer are the names of fields and pages. The section's first
value is its heading (as `title=`), an item's first value its name.

**Parts.** A part comes three ways, the first that has one winning: a `{slot 'name'}` pair standing
directly in the section's content - template, rendered where the section stands, in the scope of
the loop around it - an option of the part's name, escaped, and for `text` the rest of the content.
A slot of a part the section has not is an error under the strict check.

**Items.** A section that lists things takes its items from the rows `data=` names - a store, the
page's array, a field holding a list, a `_data` file - by their fields, a row's other fields left
alone; then from `{section:item}` pairs or single tags inside it, a loop's too: the item's first
value fills its first field, its options the others, its content the content field. The
handling options - `sort=`, `first=`, `where=` ... - work on the rows of `data=`, as on any data
tag's. A list section whose `data=` finds no rows and that writes no items of its own is a miss:
its `@else@` shows, and a single tag writes nothing. An option the section has no field for is
reported, and so is an item outside a list section, a list section given neither `data=` nor
items, and items in a section that lists nothing.

**Forms.** The newsletter and the contact form post back to the page as `{form 'newsletter'}`
does: the session's CSRF token when they post here, the hidden `padForm` field with their name.
The page's PHP asks `padPosted ( 'newsletter' )` and checks the post with
`padValidate ( [ 'email' => 'required|email' ] )`; the fields refill and show the message, tied to
the field for a screen reader, and once the post came back right - posted, and no message for any
of its fields - the section shows `thanks=` in the form's place (`role="status"`). A field's id
is the section's and its name, so two sections of a kind have fields of their own; two forms of
one name on a page are told apart as two `{form 'newsletter'}` are - the second carries its
place - so only the one that was sent says thanks, refills and shows the messages.

**Look.** Every section is a size container: its layout follows the room it has, not the window
(`@container`). The colours are custom properties on `.pad-section` - `--pad-section-bg`,
`-panel`, `-text`, `-soft`, `-dim`, `-line`, `-accent`, `-accent-text`, `-accent-soft` - whose
defaults are the frame's palette (`--bg`, `--panel`, `--text` ... of `www/_common/pad.css`), with
light and dark steps of their own on a page without it; `--pad-section-hero` (the hero's
background), `-radius`, `-max` (the content's width), `-font` and `-heading-font` are the rest. A
theme sets them on `.pad-section`.

| Option | On | Meaning |
|--------|----|---------|
| `title` | every section | The heading - also the first value; a slot `title` of markup wins |
| `eyebrow` | hero, features, pricing, testimonials, faq, cta, stats, steps, team, logos, newsletter, contact, gallery | The small line above the heading |
| `text` | every section | The line under the heading; the content wins over it, a slot `text` over both |
| `level` | every section | The heading's level, 1 to 6 (2); an item's heading one more |
| `tone` | every section | `plain`, `soft` or `accent` - the background (cta: accent, newsletter: soft) |
| `align` | every section | `center` or `left` (hero with a picture, contact and footer: left) |
| `id` | every section | The section's id, an anchor to link to |
| `button`, `link` | hero, cta | The main button and the page it goes to - one without the other is reported |
| `second`, `secondLink` | hero, cta | A quieter second button |
| `image`, `alt` | hero | A picture beside the words - a slot `image` of markup in its place |
| `note` | hero, cta, pricing, newsletter, footer | The small print |
| `data` | the list sections | The items from rows - the handling options on them; none is a miss, `@else@` shows |
| `columns` | features, pricing, testimonials, stats, steps, team, gallery | The cards side by side at the widest, 1 to 6 |
| `open`, `single` | faq | The question open at first by its number, bare every one; one open at a time |
| `form`, `thanks` | newsletter, contact | The form's name (`newsletter`; contact: bare for a message form, or its name); the thanks |
| `button`, `label`, `placeholder` | newsletter | The form's button (`Subscribe`), the field's label and its example |
| `address`, `mail`, `phone`, `hours` | contact | The details - the mail address and the phone number as links |
| `brand` | footer | The name at its side |

| Section | Shows | Parts | An item's fields - its first value first, its content last |
|---------|-------|-------|-----------------------------------------------------------|
| `hero` | The heading of the page, its text, buttons, a picture beside them | eyebrow, title, text, actions, image, note | - |
| `features` | A card per feature, an icon of `{icon}` each | eyebrow, title, text | title, icon, link, text |
| `pricing` | A card per plan, a featured one with its badge | eyebrow, title, text, note | name, price, period, text, button, link, featured, badge, features (a line each) |
| `testimonials` | A card per quote over who said it, initials without a picture | eyebrow, title, text | name, role, image, link, text (the quote) |
| `faq` | A `<details>` per question | eyebrow, title, text | question, answer |
| `cta` | A band in the accent with its buttons | eyebrow, title, text, actions, note | - |
| `stats` | The numbers, a label under each (a `<dl>`) | eyebrow, title, text | value, label, text |
| `steps` | Numbered steps (an `<ol>`) | eyebrow, title, text | title, text |
| `team` | A card per person, a picture or initials | eyebrow, title, text | name, role, image, link, text |
| `logos` | A row of logos, a name in letters without one | eyebrow, title, text | name, image, link, the logo itself (an `<svg>`) |
| `newsletter` | A form with one e-mail field | eyebrow, title, text, note | - |
| `contact` | The address, mail, phone and hours, and a message form | eyebrow, title, text, details | - |
| `gallery` | Pictures in a grid with their captions | eyebrow, title, text | image, caption, alt, link, the caption (or a picture of markup) |
| `footer` | The name and its line, columns of links, the small print | brand, text, note | title, then a link a line - `Label \| ?page`; rows: column, label, link |
| `item` | One item of the list section around it | - | - |

---

### effect
Motion and decoration on a text or a block, CSS first: every effect is a file of
`pad/subs/effects/types/`, its look `www/assets/effects/<kind>.css`, written once per page with
the CSP nonce after the base every effect shares (`effect.css`); confetti alone has a script,
`assets/confetti.js`, written the same way. The files of `types/` are the effects there are; the
subsystem is `pad/subs/effects/`.

```html
{effect 'typewriter'}Hello, world{/effect}
{effect:highlighter}the one thing to remember{/effect:highlighter}
{typewriter speed=20, mono}npm install pad{/typewriter}
{countup to=1250, prefix='€', suffix='+'/}
{gradient colors='#f97316, #db2777', angle=120, animate}Launch week{/gradient}
{marquee duration=25, label='Built with'}<b>PHP</b> <b>SQLite</b> <b>htmx</b>{/marquee}
{reveal motion='left'}<div class="card">...</div>{/reveal}
{spinner style='dots', label='Saving'/}
{confetti}<button type="button">Celebrate</button>{/confetti}
```

An effect is written three ways, the same effect each time: the tag, `{effect 'typewriter'}...
{/effect}`, whose second parameter is the effect's own first; the effect: type,
`{effect:typewriter}...{/effect:typewriter}`; and bare, `{typewriter}...{/typewriter}`, when no tag,
store, field or sequence has the name. A pair works on its content, rendered first - the effect
wraps what rendered; a single tag works on its first parameter, escaped, or on nothing.

Every animation stands inside `@media (prefers-reduced-motion: no-preference)`: under reduced
motion the content is still and whole, never hidden. A screen reader reads the text once - the
moving copies of the wave and the glitch are `aria-hidden`, the count-up reads its last number.
What repeats runs `repeat=` times (3, or `forever`); `duration=` is the seconds of one run and
`delay=` the seconds before the first, where a kind animates in time. Every option is checked
under the strict check: an unknown kind, a value out of its bounds or none of its words, a colour
that is no CSS colour, a kind that wraps a block written single, a text kind without its text.

| Effect | Form | Does | Its options |
|--------|------|------|-------------|
| `typewriter` | both | Types a line out letter by letter behind a caret, once | `speed` (14 letters a second), `delay`, `caret` (bar, none), `mono` |
| `glitch` | both | Tears the text into slices in two colours for a moment; the pointer replays it | `duration` (2.5), `delay`, `repeat` (3) |
| `shimmer` | both | A band of light sweeps across the letters and rests | `duration` (2.5), `delay`, `repeat` (3) |
| `gradient` | both | Letters painted with a gradient; `animate` lets it flow | `colors` (2 to 6), `angle` (90), `animate`, `duration` (4), `delay`, `repeat` (3) |
| `wave` | both | The letters rise and fall, a wave through the word | `height` (0.25 em), `duration` (1.6), `delay`, `repeat` (3) |
| `highlighter` | both | A marker stroke drawn behind the words, a `<mark>` | `color`, `duration` (0.8), `delay`, `scroll` |
| `countup` | both | A whole number counted up by the CSS, read as its last value | `to` (or the parameter or the content), `from` (0), `prefix`, `suffix`, `duration` (2), `delay`, `scroll` |
| `marquee` | both | A band whose content scrolls by without end; a Pause button | `duration` (20), `direction` (left, right), `gap` (48 px), `label` |
| `reveal` | pair | A block that comes into view as it is scrolled to | `motion` (up, down, left, right, fade, zoom), `distance` (32 px) |
| `tilt` | pair | A card that leans in 3D on hover and focus | `angle` (8), `toward` (top-right, top-left, bottom-right, bottom-left), `glare` |
| `spotlight` | pair | A soft light sweeps across the block and rests | `at` (top, center, a side, a corner, '30% 20%'), `color`, `duration` (2.5), `delay`, `repeat` (1) |
| `spinner` | both | A loader, `role="status"`; the pair's content is its shown label | `style` (ring, dots, bars, pulse, orbit), `label` (Loading), `size` (24 px), `color` |
| `confetti` | both | A burst of confetti: single when in view, a pair on a click in it | `pieces` (120), `spread` (70 degrees), `colors` (2 to 8), `seed` (7) |

The colours are custom properties on `.pad-effect` - `--pad-effect-accent`, `-accent2`,
`-accent3`, `-text`, `-mark` (the highlighter's ink) and `-track` (the spinner's ring) - with
light and dark defaults that fall back to the frame's `--accent`, `--text` and `--line`.

---

### theme
The page in a theme: the colours of the frame the PAD applications share swapped for a set of its
own - the side, the bar, the menu and the palette the pages build on (`--bg`, `--text`,
`--accent` ...) - with its `color-scheme`, so the charts, the widgets, the toys, the sections and
the effects follow. Every theme is a file of `pad/subs/themes/types/`; the files there are the
themes there are, and the subsystem is `pad/subs/themes/`.

```html
{theme 'paper'/}
{theme 'terminal'}
  <p>$ pad serve</p>
{/theme}
```

**Parameters:**

| Option | Description |
|--------|-------------|
| first parameter | The theme: light, dark, paper, terminal, newspaper, contrast, contrast-dark, sepia, ocean, forest, mono, solarized-light, solarized-dark, night, blueprint, readable |

**Behavior:** single, the tag writes the theme's `<style>` where it stands, with the request's
nonce, on `:root:root:root` - outweighing the frame's light and dark rules - and a page that
has one gets no theme from `$padTheme` or a visitor's choice on its way out. As a pair the theme colours its content alone, a box with
the theme's background and text, its style once per page. `$padTheme = 'paper'` puts every page
of an application in a theme, `{themes/}` lets the visitor choose (kept in a cookie of the
application, winning over the setting), `$padTheme = FALSE` turns both off. Every theme's text reaches WCAG AA on its
backgrounds, contrast's and contrast-dark's AAA. A name that is no theme, no name and content on the single form are
strict errors.

---

### themes
A switcher of the themes: a list of them, Default first, that applies a theme the moment it is
chosen - the choice posted back to the page and kept in a cookie, the pages built on the frame
in that theme from then on.

```html
{themes/}
{themes label='Colours', button='Use'/}
```

**Parameters:**

| Option | Description |
|--------|-------------|
| `label` | The label of the list (`Theme`), for a screen reader - it is not shown |
| `button` | The text of the button shown without JavaScript (`Apply`) |

**Behavior:** a form with a `<select>` on one line: a small script posts the form when the
list changes, so a theme applies the moment it is chosen; without JavaScript a button, in a
`<noscript>`, does it. The post of the form is taken before the application runs
(`pad/inits/themes.php`) and answered with a 303 back to the page, which comes in the new theme -
the page's own PHP never sees it; Default takes the choice away. The choice is the `padTheme`
cookie (SameSite=Lax, a year) on the directory the PAD applications are served under, read and
checked on every request: it colours the pages built on the frame (`_common/pad.css`) of every
application, or every page of an application that sets `$padTheme`, and no page with a design
of its own; a post from another site (`Sec-Fetch-Site: cross-site`) changes nothing. The frame
every PAD application shares has the switcher in its bar. The form writes no token of its own,
so showing it or choosing starts no session; with `$padCsrf` on it carries the session's token
as every form then does. Content is a strict error.

---

### palette
The colours of a palette as a row of swatches, each with its hex value in black or white,
whichever reads better. Every palette is a file of `pad/subs/palettes/types/`; the files there are
the palettes there are, and the subsystem is `pad/subs/palettes/`.

```html
{palette 'viridis', steps=7/}
{palette 'okabe-ito', title='The chart colours'/}
{chart 'bar', data='sales', value='north, south, west', palette='viridis'/}
```

**Parameters:**

| Option | Description |
|--------|-------------|
| first parameter | The palette: viridis, magma, plasma, inferno, cividis (perceptual, sequential); blues, greens, oranges, greys (sequential); rdbu, brbg, piyg (diverging); okabe-ito, tol-bright, tol-muted, tol-vibrant, pastel, earth (categorical) |
| `steps` | How many colours, 1 to 256 - a scale sampled evenly from its first colour to its last (one: its middle), a categorical palette its colours in order, starting again past its last; without it a categorical palette's own colours, nine of a scale |
| `title` | The row's name for a screen reader (the palette's name and the count) |

**Behavior:** the row is a list for a screen reader, `role="list"`, a swatch an item; its style
is written once per page with the nonce, a swatch's colour a class of its value with its rule
(`pp-c-440154`) - no style attribute, which a CSP nonce drops. `padPalette ( 'viridis', 7 )` answers the same colours
to a page's PHP. `palette='name'` on `{chart}` colours a chart of any kind - a categorical
palette its series in order, a scale spread over the series the chart has and over the
heatmap's ramp - as custom properties of a class of the palette on the svg (`pc-pal-viridis-3`),
its rule in the chart's own `<style>`, server and `client` alike. A
name that is no palette, steps out of bounds and content are strict errors.

---

## Layout Tags

### extends
Frame the page with a layout instead of its directories' wrappers.

```html
{extends '_layouts/report'/}

{block 'title'}Sales report{/block}

{block 'sidebar'}
  {parent/}
  <a href="?export">Export</a>
{/block}

<p>The rest of the page goes where the layout writes @page@.</p>
```

**Behavior:** Resolved while the page is assembled, before anything renders. The layout is
named from the application root, like `{page}` (`.pad`, else `.html`); an underscore
directory keeps it from being a page of its own. It replaces the `_inits.pad`/`_exits.pad`
frame and the `_common` wrapper; a layout may extend a layout. The page's text outside its
blocks goes where the layout writes `@page@`, or in front of it when it has none. The tag
must stand directly in the page's own template; the name may be an expression the page's PHP
set (`{extends $layout/}`).

---

### block
A named region of a layout or wrapper, and a page's override of it.

```html
<title>{block 'title'}My site{/block}</title>      {# in the layout or _inits.pad #}
{block 'title'}Sales{/block}                       {# in the page #}
```

**Rules:** A `{block 'name'}` pair standing directly in the page overrides the block of that
name in its frame - the layout it extends, or its directories' wrappers, so a page without
PHP sets the wrapper's title. Any other named block is a region: its content renders unless
something overrides it. The name is always quoted - a `{block}` without one (the `_common`
snippet) is not a layout block. Under the strict check a block of an extending page that
overrides nothing is reported. Text between `{ignore}` tags is left alone.

---

### parent
Inside an overriding `{block}`: the content it overrides.

```html
{block 'sidebar'}{parent/}<a href="?export">Export</a>{/block}
```

Through a chain of layouts each `{parent}` is the next block down. Outside an overriding block
it is reported under the strict check.

---

### slot
A named place for content in a custom tag's template, and the fill a caller gives it.

```html
{card title='Revenue'}                         _tags/card.pad:
  <strong>{$revenue}</strong>                  <div class="card">
  {slot 'footer'}                                <h2>{#title}</h2>
    <a href="?reports">Report</a>                @content@
  {/slot}                                        {slot 'footer'}<footer>@content@</footer>{/slot}
{/card}                                        </div>
```

**Rules:**
- A `{slot}` pair standing directly in the content of a custom tag - an `_tags` or `_common`
  tag, or an `_include` snippet used as a pair - is a fill: it is taken out of the content
  when the tag opens and kept for that use of the tag, so nested tags keep their own. The
  rest of the content goes to `@content@`. `{slot 'name'/}` there is an empty fill.
- Every other `{slot}` is a place in the template: the fill renders there, or the slot's own
  content as the default. A default holding `@content@` is a frame round the fill, rendered
  only when there is one - otherwise its `@else@` part, or nothing.
- A fill is the caller's text: it renders with the caller's variables, and a `{#name}` or a
  `{slot}` inside it belongs to the caller's tag, which is how a template passes a slot of
  its own on to a tag it uses. From a tag's PHP: `padSlotFill ( 'footer' )`.

---

### parms
Declares the parameters of a custom tag, at the top of its template.

```html
{parms title, subtitle='', tone='info'/}
<h2 class="{#tone}">{#title}</h2>
```

**Behavior:** A name alone is required - leaving it out is an error. `name=default` fills
in what the caller left out, readable as `{#name}` like a given one. Under the strict check
a parameter the tag does not declare is reported: `the tag {card} has no parameter 'titel'`.
The items are read raw, as `{attrs}` reads them.

---

### define
A custom tag written in the template itself - what an `_tags/name.pad` file is, without the file.

```html
{define 'card'}
  {parms title, tone='info'/}
  <div class="card {#tone}">
    <h2>{#title}</h2>
    @content@
    {slot 'footer'}<footer>@content@</footer>{/slot}
  </div>
{/define}

{card title='Revenue'}<strong>{$revenue}</strong>{/card}
{card title='Orders', tone='warn'}{$count}{slot 'footer'}<a href="?orders">All</a>{/slot}{/card}
```

**Behavior:** the body is kept as written and renders at every use, where the use stands and
with its fields - a `{define}` can use itself, for rows below the current one. Everything of a
custom tag works: `{#name}` reads a parameter, `{parms}` declares them, `@content@` takes the
caller's content in, `{slot}`s take fills. A define exists from the point it is walked, as a
`{content}` or `{data}` store does - a use before it is an unknown tag. `define:card` asserts the
kind. Under the strict check a name that is a built-in or application tag, or already a
`{macro}`, is an error, and so is a define without a name or without its body.

---

### macro
A template function: declared parameters, given in their order or by name, read as fields.

```html
{macro 'entry', label, type='text', value=''}
  <label>{$label} <input type="{$type}" name="{$label}" value="{$value}">@content@</label>
{/macro}

{entry 'E-mail', 'email'}
{entry label='Name', value=$name}
{orders}{entry $number}{/orders}
```

**Behavior:** the items after the name declare the parameters: a name alone is required,
`name=default` fills in - the default is evaluated where the macro is used. A use gives values
in their order, by name, or both; inside the body each is a field (`{$label}`) of the macro's
one row, and the fields around the use stay visible behind them. `@content@` takes a pair's
content in, `{slot}`s work as in a `{define}`, and a macro can call itself. The content - and a
slot fill - renders in the body, on that one row: a parameter stands in front of a field of the
same name around the use there too, so in `{rows}{entry 'x'}{$label}{/entry}{/rows}` the
`{$label}` is the parameter; `{$label@rows}` names the row's own. A value given to a
macro is text, never template - as every value is. Under the strict check a required parameter
left out, more values than parameters, a named parameter the macro does not declare, and a
parameter named like an option every tag takes (`sort`, `first`, `content` ...) are errors.
`macro:entry` asserts the kind.

---

### meta
The page's metadata, for a page without PHP as much as one with.

```html
{meta title='Monthly report', layout='_layouts/print', cache=600, access='admin', sitemap=false/}
```

**Items:**
- `layout` - frame the page with that layout, as `{extends}` does
- `cache` - the page's own server-cache time in seconds, in an application with the page
  cache on; `0` or `false` keeps the page out of the cache
- `access`, `sitemap` - kept for access rules and a sitemap to read: `padMeta('access')` for
  the page being built, `padMeta('sitemap', 'reports/monthly')` from another page's file
- any other name becomes a variable of that name: `title=` is `$title`, which the wrapper shows

**Behavior:** Read while the page is assembled - after the PHP of `_inits.php` and the page,
before anything renders - and taken out of the text; the values may use the PHP's variables.
It must stand directly in the page's own template. The cache time is read from the file
before the cache is consulted, so only a plain value counts there.

---

### fragment
A named part of the page that a request can ask for alone.

```html
{fragment 'order-list'}
  <ul>{orders}<li>{$number}</li>{/orders}</ul>
{/fragment}
```

**Behavior:** A normal request renders it in place. A request with `&padFragment=order-list`,
or a page whose PHP sets `$padFragmentOnly = 'order-list'`, gets that fragment's rendering
and nothing else: the page renders up to the end of the fragment, which is then the whole
response, bare and untidied like a `padInclude` request; the rest of the page does not render.
The first fragment of the name to finish is sent. A fragment that never rendered is an error
under the strict check, an empty 404 otherwise - keep conditions inside the fragment. For
HTMX (`hx-get="?orders&padFragment=order-list"`) and `{ajax 'orders', fragment='order-list'/}`.

**htmx's target:** a request htmx makes (`HX-Request: true`) that names no fragment itself
asks for the one called like the element it swaps - the `HX-Target` header, its id: with
`hx-target="#order-list"` no `padFragment` is needed. When the page has no fragment of that
name it renders whole, as before - for a target without an id, or another part than the
target, name the fragment in the address.

---

### push
Add rendered text to a named stack, for a `{stack}` elsewhere in the page to print.

```html
{push 'scripts', once='chart'}
  <script src="chart.js"></script>
{/push}
```

**Parameters:**
- First parameter: the stack's name
- `once='key'` - the first push of that key is kept, later ones are skipped before their
  content renders (per stack); `once` without a key drops a push whose rendered text the
  stack already holds

**Behavior:** The content renders where the tag stands, with the variables of that spot, and
prints nothing there. A push inside a `{cache}` section is stored with the section and made
again on every hit. A plain `{page}` or `{code}` pass adds to the page's stacks; a sandbox, clean
or reset pass keeps its pushes to itself. From PHP: `padStackPush ( 'scripts', $html, $once )`.

---

### stack
Everything pushed to a stack, in push order.

```html
<head>
  {stack 'styles'/}
</head>
```

**Behavior:** The tag prints a marker that is filled in when the whole page has rendered, so
a `{stack}` in the layout's `<head>` gets the pushes of the components below it. A stack
nothing pushed to prints nothing.

---

## Debugging Tags

### dump
Dump debug information.

```html
{dump/}
```

**Behavior:** Calls `padDump()` with message

---

### debug
Show a value in the page while it renders - for a local request only.

```html
{debug $order/}
{debug/}
```

**Behavior:** Writes a collapsible `<details>` tree of the value. `{debug}` alone shows every
field visible where it stands - the rows of the enclosing levels, innermost first - and the
application's variables. A missing `$name` is shown as missing rather than failing. Only a
local request - the command line, or loopback with nothing forwarded - gets it; any other gets
nothing, and `$padDiagnostics = FALSE` switches it off for every web request - the command
line still gets it. Unlike `{dump}` the
request goes on. A page or a `{cache}` section that holds a debug box is never stored in the
page or fragment cache, so no later visitor is served what a local request was shown.

---

### trace
Enable detailed tracing.

```html
{trace}
  Code to trace
{/trace}
```

**Behavior:** Enables trace mode for enclosed content

---

## Error Handling Tags

### assert
A condition a test run holds the page to; silent everywhere else.

```html
{assert $total eq 42/}
{assert $lines gt 0, 'the cart has lines'/}
```

**Parameters:**
- First parameter: the condition, an expression like `{if}`'s
- Second parameter (optional): a message for the report

**Behavior:** With `$padAssert` on - `pad test` turns it on for its run - a false condition
is an error, `assert failed: <condition> - <message>`, and the test fails. With it off, the
default, the tag renders nothing and the condition is not even evaluated, so an `{assert}`
can stay in a page that goes to production. See `pad test` in `apps/cli/README.md`.

---

### error
Trigger a PAD error.

```html
{error 'Error message'/}
```

**Parameters:**
- First parameter: Error message

**Behavior:** Calls `padError()` with message

---

### exception
Throw PHP exception.

```html
{exception 'Exception message'/}
```

**Parameters:**
- First parameter: Exception message

**Behavior:** Throws PHP Exception

---

### exit
Exit PAD processing.

```html
{exit/}
```

**Behavior:** Calls `padExit()` to terminate processing

---

## Boolean/Value Tags

### true
Return TRUE value.

```html
{true}
```

**Returns:** TRUE

---

### false
Return FALSE value.

```html
{false}
```

**Returns:** FALSE

---

### null
Return NULL value.

```html
{null}
```

**Returns:** NULL

---

## Sequence Tags

### sequence
Generate mathematical sequences.

```html
{sequence prime, rows=10}
{sequence fibonacci, from=1, to=100}
{sequence '1..10'}
```

**Parameters:** See sequence subsystem documentation for full parameter list.

**Returns:** Generated sequence array

---

### continue
Skip to next iteration of a loop.

```html
{continue 'tagname'/}
```

**Parameters:**
- First parameter: Tag name to continue

**Behavior:** Skips to next iteration (like PHP's continue)

---

### cease
Soft stop - graceful end of loop.

```html
{cease 'tagname'/}
```

**Parameters:**
- First parameter: Tag name to cease

**Behavior:** Gracefully ends loop processing

---

### break
Hard stop - immediate exit from loop.

```html
{break 'tagname'/}
```

**Parameters:**
- First parameter: Tag name to break

**Behavior:** Immediately exits loop (like PHP's break)

---

### pull
Pull stored sequence data.

```html
{pull 'stored_name'}
```

**Parameters:**
- First parameter: Stored sequence name

---

### flag
Each value of a sequence as 1 or 0: whether it belongs to the named sequence type.

```html
{flag '1..10', even}{$sequence} {/flag}       → 0 1 0 1 0 1 0 1 0 1
```

**Behavior:** Membership flags in place of the values

---

### keep
Keep sequence values matching criteria.

```html
{keep '1..10', prime}{$sequence} {/keep}      → 2 3 5 7
```

**Behavior:** Filter to keep matching values

---

### remove
Remove sequence values matching criteria.

```html
{remove '1..10', prime}{$sequence} {/remove}  → 1 4 6 8 9 10
```

**Behavior:** Filter to remove matching values

---

### make
Transform sequence values.

```html
{make '1..5', add=10}{$sequence} {/make}      → 11 12 13 14 15
```

**Behavior:** Transform values during sequence generation

---

### resume
Transform the last pushed sequence in place, through an action.

```html
{sequence '1..5', push='s'}{/sequence}
{resume reverse/}
{pull 's'}{$sequence} {/pull}                  → 5 4 3 2 1
```

**Behavior:** Delegates to the sequence subsystem (`sequence/start/tags/resume.php`); applies
the action to the sequence pushed last and writes the result back over its store. It prints
nothing, and has nothing to do with `{cease}`. See [sequences](../sequences/).

---

## Summary Table

| Tag | Category | Description |
|-----|----------|-------------|
| `if` | Control Flow | Conditional execution |
| `case` | Control Flow | Switch-case matching |
| `switch` | Control Flow | Rotating value switch |
| `tree` | Control Flow | Render a tree, the body again per level of children |
| `branch` | Control Flow | Inside a tree: render when the row has children |
| `recurse` | Control Flow | Inside a tree: render the body for the row's children |
| `ifchanged` | Control Flow | Render when a value changed since the previous row |
| `while` | Control Flow | Loop while true |
| `until` | Control Flow | Loop until true |
| `set` | Variables | Set global variables |
| `get` | Variables | Fetch another page of the app |
| `data` | Variables | Store/iterate data |
| `content` | Variables | Store content |
| `bool` | Variables | Store boolean |
| `field` | Database | Query single value |
| `array` | Database | Query and iterate rows |
| `record` | Database | Query single row |
| `check` | Database | Boolean existence test |
| `at` | Variables | Evaluate @ expression |
| `collection` | Variables | Markdown files with front matter as rows |
| `search` | Variables | The rows of a source a query finds, ranked by BM25, with a marked excerpt |
| `count` | Counters | Check element count |
| `increment` | Counters | Increment variable |
| `decrement` | Counters | Decrement variable |
| `page` | Execution | Include PAD page |
| `code` | Execution | Run content as PAD |
| `sandbox` | Execution | Run content as PAD, isolated |
| `action` | Execution | Execute action |
| `ajax` | Execution | Embed a page the browser fetches after load (`&padInclude`) |
| `live` | Execution | A region that re-renders on the server when clicked, submitted or changed |
| `pad` | Execution | Generic level for data=, content=, name= |
| `redirect` | Navigation | HTTP redirect |
| `restart` | Navigation | Restart processing |
| `pager` | Navigation | Page links for a tag with the page option |
| `jsonld` | Navigation | The structured data of a page - a Schema.org object of `pad/subs/jsonld/types/` as JSON-LD: article, breadcrumb, business, course, event, faq, itemlist, job, organization, person, product, profile, recipe, restaurant, software, video, website - its fields mapped by name |
| `csrf` | Web | Hidden CSRF token field of the session |
| `extends` | Layout | Frame the page with a layout |
| `block` | Layout | A named region of a layout or wrapper, and its override |
| `parent` | Layout | The overridden content, inside an overriding block |
| `slot` | Layout | A named place for content in a custom tag, and its fill |
| `parms` | Layout | Declare a custom tag's parameters, required or with defaults |
| `meta` | Layout | Page metadata: title and other variables, layout, cache time |
| `fragment` | Layout | A named part of the page a request can ask for alone |
| `push` | Layout | Add rendered text to a named stack |
| `stack` | Layout | Print a stack, filled in after the page has rendered |
| `form` | Web | Form with CSRF token and name, its fields refill |
| `input` | Web | Form field with refill, label and validation error |
| `textarea` | Web | Text area with refill, label and validation error |
| `flash` | Web | Flash messages that survive one redirect |
| `auth` | Web | Content for a logged-in user, the user's row as data; `{else}` for a guest |
| `guest` | Web | Content for a guest; `{else}` when logged in |
| `can` | Web | Content when a gate allows the ability; `{else}` |
| `cannot` | Web | Content when a gate refuses the ability; `{else}` |
| `feature` | Web | Content while a feature flag is on; `{else}` |
| `asset` | Web | The versioned address of a file of `www/<app>/`; `tag` writes the element |
| `avatar` | Output | Initials avatar as inline SVG, coloured by a hash of the name |
| `identicon` | Output | Symmetric 5x5 identicon as inline SVG from a SHA-256 hash |
| `placeholder` | Output | Grey image placeholder as inline SVG, a size or a ratio, fluid |
| `progress` | Output | Accessible progress bar (native `<progress>`) or step dots |
| `rating` | Output | Stars or hearts for a score as inline SVG, parts clipped, said in words |
| `icon` | Output | One of 79 built-in line icons as inline SVG in `currentColor` |
| `gravatar` | Output | Gravatar `<img>` from the SHA-256 of an address, or a local avatar fallback |
| `notation` | Output | A text in a notation drawn on the server - the files of `pad/subs/notations/types/`: AsciiMath, chemistry, ABC music, chords, piano, braille, morse, semaphore, signal flags, chess, go, draughts |
| `chess` | Output | A chess position from FEN as inline SVG - the chess notation, as a built-in tag |
| `game` | Output | A little game played in the browser - 18 of them, the files of `pad/subs/games/types/`: sudoku, crossword, snake, reversi, a quiz ... |
| `toy` | Output | A small live simulation or visual toy in the browser, the files of `pad/subs/toys/types/`: the Game of Life, boids, the Mandelbrot set, falling sand ... |
| `pattern` | Output | A seeded SVG background or generative art, the files of `pad/subs/patterns/types/`: stripes, dots, contour lines, Voronoi, circle packing ...; `as='url'` for a style attribute |
| `printable` | Output | Pages made for paper - the files of `pad/subs/printables/types/`: labels, badges, cards, tickets, a certificate, envelopes, letters, invoices ... - printed alone, the same from the PDF writer |
| `sound` | Output | A short sound made from a recipe - the files of `pad/subs/sounds/types/`: chime, pop, coin, kick ... - a button, a binding or a WAV file |
| `diff` | Output | The differences between two texts - words inline, or lines in one column or side by side |
| `excerpt` | Output | A text cut to whole words around the first search match, the matches marked |
| `lorem` | Output | Lorem ipsum placeholder text - words, sentences or paragraphs, the same for the same seed |
| `fake` | Output | A fake value of a kind - a file of `pad/subs/fakes/types/` - or rows of several kinds, the same for the same seed |
| `widget` | Widget | An interactive part of the page - the files of `pad/subs/widgets/types/`: tabs, accordion, carousel, modal, copy, poll ...; also the `widget:` type and the bare names |
| `tabs` | Widget | Panels behind a row of tabs - radio buttons and CSS, no JavaScript; `{tab}` is one item |
| `accordion` | Widget | Items that open on a click - every `{tab}` a `<details>`, one open at a time with `single` |
| `modal` | Widget | A `<dialog>` and its opening button - invoker commands, a small script, or a link without scripting |
| `carousel` | Widget | Slides that scroll and snap sideways, previous/next and dot links - no autoplay |
| `copy` | Widget | A text with a copy-to-clipboard button, selectable by hand without scripting |
| `poll` | Widget | A vote once per visitor, results as bars - a live region, a plain form without scripting |
| `toast` | Widget | A notice the visitor dismisses - an open `<dialog>` its button closes by an invoker command |
| `dropdown` | Widget | A button and its menu on the popover attribute - opened, closed and placed by the browser |
| `tooltip` | Widget | A short tip on hover and on focus, shown by CSS, named by aria-describedby; Escape hides it |
| `wizard` | Widget | Steps one at a time - every `{tab}` a step, radio buttons and CSS, Back and Next; no JavaScript needed |
| `lightbox` | Widget | A gallery of thumbnails whose pictures open large over the page by `:target` - no JavaScript |
| `drawer` | Widget | A panel sliding in from a side - a modal `<dialog>` opened as `{modal}`'s, a `:target` overlay without scripting |
| `breadcrumb` | Widget | The trail from home to the page - from the page's path, the lines of a pair or `data=` rows; no JavaScript |
| `img` | Output | Resized, cached thumbnail of a picture, with a 2x srcset |
| `video` | Output | YouTube or Vimeo as a poster and play button, the iframe only on a click; a local file as `<video>` |
| `embed` | Output | A video, sound, map, pen or document of another site behind a click-to-load facade - a provider per file of `pad/subs/embeds/types/`; a file as `<video>`, `<audio>` or a lazy frame |
| `pdf` | Output | The rendered content sent as a PDF, written in plain PHP - fonts, tables, JPEG pictures |
| `diagram` | Output | A flowchart, sequence, state, class, entity-relationship, mind map, tree, user journey, commit graph, fishbone, railroad or packet diagram from Mermaid-style lines, laid out on the server as inline SVG |
| `map` | Output | A choropleth map of the world or a region as inline SVG, a colour step per value |
| `timeline` | Output | Events on a time axis as inline SVG - cards above and below, spans as bars, or a vertical list |
| `datatable` | Output | Rows as an accessible HTML table - aligned numbers, totals, sorting and paging links |
| `spreadsheet` | Output | A grid of cells with Excel's formulas computed on the server - formats, totals, editable cells |
| `toc` | Output | A table of contents from the page's own rendered headings, ids added |
| `countdown` | Output | The time left until a moment in words, in a time element, optionally ticking |
| `timeago` | Output | How long ago a moment was, in a time element with the exact moment |
| `emoji` | Output | An emoji by its shortcode, labelled for screen readers; also an `emoji` pipe |
| `country` | Output | A country's flag emoji and English name from an ISO 3166 code |
| `jsonview` | Output | A JSON value or PHP array as a foldable, coloured tree of details elements |
| `section` | Output | A block of a landing page - the files of `pad/subs/sections/types/`: hero, features, pricing, faq, cta, footer ... - pure CSS, coloured by the theme; `{section:item}` an item of a list section |
| `effect` | Output | Motion and decoration, CSS first - the files of `pad/subs/effects/types/`: typewriter, highlighter, countup, reveal, marquee, spinner, confetti ... - still under reduced motion |
| `theme` | Output | The page in a theme of `pad/subs/themes/types/` - the frame's colours swapped; as a pair, its content alone |
| `themes` | Output | A switcher of the themes, the visitor's choice kept in a cookie - applied when chosen |
| `palette` | Output | The colours of a palette of `pad/subs/palettes/types/` as swatches with their hex values; palette= colours a chart |
| `nonce` | Web | The request's CSP nonce for `<script nonce>` |
| `files` | Files | List files |
| `dir` | Files | Directory listing |
| `sitemap` | Files | The application's pages from the file tree |
| `file` | Files | Write file |
| `exists` | Files | Check file exists |
| `open` | Files | Opening brace |
| `close` | Files | Closing brace |
| `curl` | Network | HTTP request |
| `mail` | Network | Send a template email, HTML and text |
| `echo` | Output | Evaluate/output |
| `flush` | Output | Send the page rendered so far now |
| `output` | Output | Set output type |
| `tidy` | Output | Format HTML |
| `spaceless` | Output | Remove whitespace between HTML tags |
| `markdown` | Output | Markdown written as HTML, raw HTML escaped |
| `chart` | Output | A chart as inline SVG - 47 kinds, the files of `pad/subs/charts/types/`, from bar and line to candlestick, chord and org chart |
| `sparkline` | Output | Word-sized line chart as inline SVG |
| `qr` | Output | QR code as inline SVG |
| `barcode` | Output | EAN-13, EAN-8, UPC-A or Code 128 barcode as inline SVG |
| `calendar` | Output | A month as a table with its events, or its weeks and days as rows |
| `holidays` | Output | The public holidays of a country as rows or a table |
| `highlight` | Output | Source code coloured on the server - nine languages |
| `ignore` | Output | Escape content |
| `reactData` | Output | React mount point with provider data |
| `cache` | Output | Keep a section rendered (fragment cache) |
| `attrs` | Output | HTML attributes, the false ones left out |
| `trans` | Output | Translated text from the `_lang/` catalogs |
| `classes` | Output | Class list from conditional names |
| `dump` | Debug | Dump info |
| `debug` | Debug | Show a value inline, local requests only |
| `assert` | Error | Fail a test run when a condition is false, silent otherwise |
| `trace` | Debug | Enable tracing |
| `error` | Errors | Trigger error |
| `exception` | Errors | Throw exception |
| `exit` | Errors | Exit processing |
| `true` | Values | Return TRUE |
| `false` | Values | Return FALSE |
| `null` | Values | Return NULL |
| `sequence` | Sequences | Generate sequence |
| `continue` | Loop Control | Skip to next iteration |
| `cease` | Loop Control | Soft stop (graceful end) |
| `break` | Loop Control | Hard stop (immediate exit) |
| `pull` | Sequences | Pull stored data |
| `flag` | Sequences | Mark membership as 1 or 0 |
| `keep` | Sequences | Keep matching |
| `remove` | Sequences | Remove matching |
| `make` | Sequences | Transform values |
| `resume` | Sequences | Transform the last pushed sequence in place |

---

## Type Prefixes

Resolve naming conflicts with explicit prefixes:

| Prefix | Purpose | Example |
|--------|---------|---------|
| `app:` | App tag from `_tags/` | `{app:mytag}` |
| `common:` | Tag from the `_common` app | `{common:padFrame}` |
| `pad:` | Built-in PAD tag | `{pad:if}` |
| `php:` | Call PHP function | `{php:strlen 'abc'}` as a tag, `{echo $x \| php:strlen(@)/}` in an expression |
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
| `sequence:` | Sequence type | `{sequence:fibonacci}` |
| `action:` | Sequence action | `{action:reverse}` |
| `flag:` / `make:` / `keep:` / `remove:` | Sequence operations | `{make:fibonacci}` |
| `section:` | Section kind | `{section:hero title='...'}...{/section:hero}`, as `{section 'hero', ...}` - a kind is a file of `pad/subs/sections/types/`; never bare |
| `effect:` | Effect kind | `{effect:typewriter}Hi{/effect:typewriter}`, as `{effect 'typewriter'}` or bare `{typewriter}` - a kind is a file of `pad/subs/effects/types/` |
| `jsonld:` | JSON-LD kind | `{jsonld:article headline=$title/}`, as `{jsonld 'article', data='post'/}` - a kind is a file of `pad/subs/jsonld/types/`; never bare |

---

## Options Reference

Options modify tag behavior. Add them to any tag, separated by commas: `{data 'x', ignore}`.
An option name without a value directly after a single parameter may leave the comma out -
`{data 'x' ignore}` is the same tag; one with a value, `{pad 'x' toContent='c'}`, is lost. That holds for the options below and an application's own `_options/`; any
other word after a value is a pipe, `{echo 'abc' upper/}` gives `ABC`.

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
| `bool` | `{if bool='name'}`: the stored flag is the condition |
| `optional` | Suppress not-found errors |
| `demand` | An error when the tag produced nothing |
| `null` | Content (a `{content}` name, snippet or page) shown for NULL |
| `else` | Content shown for an empty or false answer |
| `notOk` / `error` | Content shown for any miss - handling that left no row too - or when the tag's handler or PHP throws |

### Formatting Options

| Option | Description | Example |
|--------|-------------|---------|
| `print` | Print each row's first field | `print` |
| `quote` | Wrap in quotes - with `print` | `quote="'"` |
| `open` | Prefix on first - with `print` | `open="["` |
| `close` | Suffix on last - with `print` | `close="]"` |
| `glue` | Separator - with `print` | `glue=", "` |
| `tidy` | Clean whitespace | |

### Control Options

| Option | Description |
|--------|-------------|
| `sort` | Sort data |
| `rows` | Limit rows |
| `first` | First N items |
| `last` | Last N items |
| `page` | Pagination |
| `cache` | Keep the rendering for so many seconds (fragment cache) |
| `where` | Keep the rows an expression holds for |
| `callback` | Run callback |
| `ignore` | Skip PAD processing |
| `noError` | An unknown tag renders nothing (as `optional`) |
| `dump` | A state dump under `DATA/dumps/` |

### Combined Formatting Example

```
{items print, quote="'", glue=", ", open="[", close="]"}
```
Result: `['item1', 'item2', 'item3']` - quote, open, close and glue work on what `print` prints, and
without it do nothing.

---

## Properties Reference

Access iteration state and metadata using `property@tag` syntax.

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
| `depth@tag` | Depth in a recursion - a `{tree}`'s rows at 1 |
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
| `parameter.n@tag` | Positional parameter n, numbered from 1 |
| `parameters@tag` | All parameters |
| `option.name@tag` | The named option's value |
| `options@tag` | All options |
| `variable.x@tag` | Level variable |
| `variables@tag` | All variables |

### Properties Example

```
{items}
  {first@items}<ul>{/first@items}
  <li class="{even@items ? even : odd}">{$name}</li>
  {last@items}</ul>{/last@items}
  Index: {current@items/} of {count@items/}
{/items}
```

A property also reads as a value inside an expression - `{if first@items}`, `{if current@items
eq 2}` - because a property name followed by `@` and a target is one reference there. Any other
word before `@` keeps the placeholder reading: elsewhere in an expression `@` is the current
value being piped in. Inside an expression the bare spelling is the property and nothing else;
a row field of the same name is what the `$`-spelling reads - `{$first@orders}` - so the sigil
resolves the collision there. Written as a tag - `{first@orders}...{/first@orders}`,
`{name@users}` - a row field of that name wins; use `{if first@orders}` when the rows can carry
such a field.
