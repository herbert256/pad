# PAD Tags Reference

This document provides a complete reference for all PAD tags, grouped by functionality.

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
is not an `if`, `case` or `ifchanged` - so a loop that runs again starts afresh, and to the
tag's place in it: the n-th `ifchanged` of a row is compared with the n-th of the row before.

---

### tree
Renders a tree: its body for every row, and inside the body `{recurse}` renders the same
body again for the current row's children - menus, category trees, threaded comments.

```html
<ul>
{tree 'menu', children='items'}
  <li><a href="?{$page}">{$title}</a>
    {branch}<ul>{recurse}</ul>{/branch}
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
{branch}<ul>{recurse}</ul>@else@<span class="leaf"></span>{/branch}
```

---

### recurse
Inside a `{tree}`: renders the tree's body once more for the children of the current row,
each child a row of its own. A row without children renders nothing. The new level is
named as the tree is, so `depth@tree` and `count@tree` follow it.

```html
{recurse}
```

---

### switch
Rotating switch that cycles through options on each call.

```html
{switch 'odd', 'even'}
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
other level outlives it - `{set $x = 1}{sequence 3}{set $x = 8}{/sequence}{$x}` gives 8.
(A `$name = value` written on another tag, `{users $n = 5}`, is that level's own and is
unwound when the level closes.)

```html
{set $name = 'value', $count = 5, $active = TRUE}
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
{get 'page_name'}
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
{bool 'store_name', $value}
```

**Parameters:**
- First parameter: Store name
- Second parameter: the value, when there is no content - with neither the flag is FALSE

---

### field / array / record / check
Database access tags. The tag name becomes the `db()` command word, so do NOT write `SELECT` in the parameter:

```html
{field "count(*) from users"}                     <!-- single value -->
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

### at
Evaluate an `@` expression (for example against a sequence or data set).

```html
{at "country.id='f0_325'@mondial"}
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
{increment $counter}
```

**Parameters:**
- First parameter: Variable name to increment

**Behavior:** Creates variable with value 1 if doesn't exist

---

### decrement
Decrement a variable by 1.

```html
{decrement $counter}
```

**Parameters:**
- First parameter: Variable name to decrement

**Behavior:** Creates variable with value -1 if doesn't exist

---

## Execution Tags

### page
Include and execute a PAD page.

```html
{page 'pagename'}
```

**Parameters:**
- First parameter: Page path to include

**Behavior:** Executes the page's PHP and PAD files. A name that is no page of its own resolves
through the clean URL routes: `{page 'products/42'}` renders `products/[id].pad` with `$id` set
to 42 (see Clean URLs in docs/APP.md).

---

### code
Runs its content as PAD source in a separate engine pass and outputs what that pass
produced.

```html
{code}{echo 2 * 3}{/code}        → 6
{echo $snippet | code}           → $snippet run as PAD
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
{sandbox}{echo 3 * 3}{/sandbox}  → 9
{echo $snippet | sandbox}        → $snippet run as PAD, isolated
```

---

### action
Execute a sequence action, over a stored sequence.

```html
{action myStore, reverse}
{action:reverse myStore}
```

**Parameters:**
- The store and the action, in either order; the prefix spelling names the action outright

---

### ajax
Embed another page of the application, fetched by the browser after the page has loaded: the
tag writes a `<div>` and a script that requests the page with `&padInclude` and puts its
answer in the div.

```html
{ajax 'orders'}
{ajax 'orders', fragment='order-list'}
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
  request values), `pad-change="event"` on a field (its name and value come along);
  `pad-value="..."` gives the event a value.

**How it works:** the content is wrapped in `<div data-pad-live="cart">`, and the page's first
region brings a small inline script. An event posts `padLive`, `padEvent` and `padValue` to
the page's own URL; the page runs as always - its PHP reads `padLiveEvent()` and
`padLiveValue()` - and the response is the inner content of that region alone, which the
script swaps in. State lives where a page keeps it: the session (`$padSessionVars`), a
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
{redirect 'page'}
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
{restart 'pagename', $param1 = 'value1'}
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

{pager 'products', window=2}
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
engine's `pad*` switches left out. As a single tag it answers
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

## Web Application Tags

### csrf
The hidden form field holding the CSRF token of the visitor's session.

```html
<form method="post">
  {csrf}
  ...
</form>

<meta name="csrf-token" content="{csrf token}">
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
  {input 'email', type='email', label='E-mail', required}
  {textarea 'message', label='Message', rows=6}
  <button>Send</button>
{/form}
```

**Items:** the form's name first; `method='get'` for a form that does not post; `error=`
the message above the fields when the form came back with errors (`error` alone: "Please
correct the errors below."); every other item is an attribute of the `<form>` tag
(`action=`, `class=`, `enctype=`), written as `{attrs}` writes them.

**Behavior:** A posting form gets the hidden `padCsrfToken` field - not when its `action=`,
or the `formaction=` of a button in it, is on another site, which the token would be handed
to - and a hidden `padForm` field
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
  {input 'email', type='email', label='E-mail', rules='required|email'}
  {textarea 'message', label='Message', rows=6, rules='required|max:2000'}
{/form}
```

Only the page's own template is read - with its `_inits.pad` and `_exits.pad` - and only what
is written out: quoted rules on a field with a quoted name, in a form with a quoted name. A
field with rules in an `_include` snippet, a custom tag, a `{page}` or an `{extends}` layout,
rules from a variable, rules outside a named form, in a form with `action=` or
`method='get'`, on a file field, a rule that does not exist and one field given two sets of
rules are errors, strict check or not - each would be rules nothing checks. A rule with
braces writes them as `&open;` and `&close;`, and a backslash as `\\`: `rules='regex:/^\\d&open;4&close;$/'`. Custom
messages stay with `padValidate`'s third argument.

---

### input
A form field that refills from what was posted and shows the error `padValidate` found for it.

```html
{input 'email', type='email', label='E-mail', required, placeholder='you@example.org'}
{input 'terms', type='checkbox', label='I agree', value='yes'}
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
{textarea 'message', label='Message', rows=6, required}
{textarea 'message', label='Message', rows=6, rules='required|max:2000'}
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

### nonce
This request's Content-Security-Policy nonce.

```html
<script nonce="{nonce}">{ignore}
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
- `group`: Group results by item name

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
whose template says `{meta sitemap=false}`, and every page or directory `$padSitemapSkip`
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

**Returns:** TRUE if file exists, FALSE otherwise

---

### open
Return opening brace character.

```html
{open}
```

**Returns:** `&open;` (entity for `{`)

---

### close
Return closing brace character.

```html
{close}
```

**Returns:** `&close;` (entity for `}`)

---

## HTTP and Network Tags

### curl
Make HTTP request.

```html
{curl 'http://example.com', post='name=x&id=1'}
```

**Parameters:**
- `url` / first param: URL to request
- `post` - the body to post (the request is then a POST); `user` and `password` - basic
  authentication; `get`, `cookies`, `headers` and `options` (curl options by name) - arrays,
  as `padCurl()` takes them
- `$name=value` written on the tag are added to the URL as query values
- `SELF://` prefix replaced with current host
- `ttl=600`: keep the answer that many seconds (see below)

**Behavior:** Makes HTTP request; a result other than 200 is a PAD error

**Returns:** Response data

**Remote data with a cache:** with `ttl=` the answer is kept, and the requests within that
many seconds are answered from the copy instead of asking the source again. When the source
fails after the ttl - a failed transfer or any status but 2xx - the last good copy is served
and the failure goes to the error log (and to the application's `_events/curl.php`). Only a
good answer is kept; without a copy the failure is an error as before.

```html
{curl 'https://api.example.com/rates.json', ttl=600}

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
{mail to=$email, template='order', subject='Order confirmation'}

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

## Output Tags

### echo
Evaluate and output expression.

```html
{echo $variable}
{echo 5 + 3}          → 8 - a quoted '5 + 3' is the text itself
```

**Parameters:**
- First parameter: Expression to evaluate

**Returns:** Evaluated result

---

### output
Set output type.

```html
{output 'download'}
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
  {flush}
  <body>@page@</body>
</html>
```

**Behavior:** The first flush switches off, for the request, everything that needs the whole
body: tidy, gzip of the whole body, the ETag and its 304, `Content-Length` and the page cache;
the headers go out with the first part, so a header or cookie set after it is lost. It stands at
the top level of the page or its wrapper - inside another tag, or in a page whose PHP returns
data, strict mode reports it. In a page rendered inside another (`{page}`), for an output
type other than web, and in a request answered with a part of the page - a response fragment
(`padFragment`), the post of a `{live}` region - it does nothing. The page's PHP runs before any of the template, so what
a flush wins is the rendering below it. A `{stack}` in the part that goes out early gets the
pushes made before the flush only - a push below it cannot reach text the browser already has.

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

{markdown $post.body}
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
Draw a chart as inline SVG - no JavaScript, so it works in print and caches like any output.

```html
{chart 'bar', data='sales', label='month', value='amount'}
{chart 'line', data='visits', value='count', title='Visits this week'}
{chart 'bar', sequence='fibonacci', rows=12}
```

**Kinds:** `bar` (columns from a zero baseline), `line` (a line over a light wash, the last
point marked), `sparkline` (see below).

| Option | Description |
|--------|-------------|
| `data` | The rows: a `{data}` store, a sequence store, the page's array or a `_data` file of that name, or a literal (`data='[3,1,4]'`) |
| `sequence` | Plot the first `rows` terms (default 10) of a sequence type instead |
| `value` | The field that holds the number - by default the first numeric field |
| `label` | The field for the category axis - by default the first other field, else the row number |
| `title` | The accessible name; by default made from value and label |
| `width`, `height` | The size, default 600 x 300; `.pad-chart { max-width: 100%; height: auto }` in the page's CSS makes it shrink with its container |

**Accessibility:** `role="img"`, labelled by a `<title>` and a `<desc>` that lists the values;
each bar and point has its own `<title>`, the tooltip on hover. A row without a number is
left out; a chart without points writes nothing.

**Colours:** CSS custom properties `--pad-chart-series`, `--pad-chart-text`,
`--pad-chart-grid` and `--pad-chart-surface`, overridden on `.pad-chart`. The defaults follow
the page's `color-scheme` (light-dark()), so a page that declares `color-scheme: light dark`
gets the dark steps in dark mode.

---

### sparkline
A word-sized line chart: no axes, the last point marked.

```html
Visits {sparkline data='visits', value='count'}
{sparkline sequence='fibonacci', rows=12}
```

**Behavior:** `{chart 'sparkline', ...}` by another name, with the same options; 120 x 32 by
default, scaled to its own minimum and maximum.

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
<button {attrs disabled=$busy, title=$help, aria-expanded=$open}>Save</button>
```

**Items:** `name=expression`, a bare `name` (written as a bare attribute), or an expression
whose array value adds its keys as attributes - the keys are data, so one that is no
attribute name, an event handler (`on...`) and a URL attribute (`href`, `src`, `action` ...)
whose value runs script (`javascript:`) are left out. Names are HTML attribute names, dashes
included (`data-id`, `aria-label`); `content=` or `print=` are attributes here, not options.

**Behavior:** A boolean HTML attribute (`disabled`, `checked`, `selected`, `required`,
`hidden`, ...) is written bare when its value is true and left out otherwise. Any other
attribute is left out for FALSE, written bare for TRUE, and otherwise written with its
value - the text `false` stays `aria-expanded="false"`; an empty value is an empty attribute.

---

### classes
Build a class list from fixed and conditional names.

```html
<div class="{classes 'panel', active=$isActive, invalid=$hasErrors}">
```

**Items:** an expression - a string of one or more names, or an array of them - is always
in; `name=condition` puts that name in when the condition holds. The result is deduplicated
and escaped.

---

### trans
A key of the `_lang/` catalogs, in the request's locale.

```html
{trans 'cart.title'}
{trans 'cart.items', count=$n}
{trans 'greeting', name=$user, place='Utrecht'}
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
the page booking of a paged tag in it, for a `{pager}` further down. A request for one
response fragment, or the post of a live region, renders the section and stores nothing. A
rendering that holds the visitor's CSRF token (a `{form}`, `{csrf}`, a live region) or the
request's `{nonce}` is not stored either: it renders every time.

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
{reactData id='products', provider='products'}
```

**Parameters:**
- `id` - DOM id of the generated div (default `myReactId`)
- `provider` - Provider file in `_providers/<name>.php` (defaults to `id`)
- `type` - `check` turns the provider's result into 1 or 0; otherwise (`record`, the default,
  or `array`) the result is passed as the provider returned it

**Behavior:** Runs the provider, stores the result in `$padProviders`, and outputs `<div id="..." data="...">` with the JSON HTML-escaped for the attribute. Read it in JS with `getAttribute('data')`. See [REACT.md](../REACT.md).

---

## Layout Tags

### extends
Frame the page with a layout instead of its directories' wrappers.

```html
{extends '_layouts/report'}

{block 'title'}Sales report{/block}

{block 'sidebar'}
  {parent}
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
set (`{extends $layout}`).

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
{block 'sidebar'}{parent}<a href="?export">Export</a>{/block}
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
{parms title, subtitle='', tone='info'}
<h2 class="{#tone}">{#title}</h2>
```

**Behavior:** A name alone is required - leaving it out is an error. `name=default` fills
in what the caller left out, readable as `{#name}` like a given one. Under the strict check
a parameter the tag does not declare is reported: `the tag {card} has no parameter 'titel'`.
The items are read raw, as `{attrs}` reads them.

---

### meta
The page's metadata, for a page without PHP as much as one with.

```html
{meta title='Monthly report', layout='_layouts/print', cache=600, access='admin', sitemap=false}
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
HTMX (`hx-get="?orders&padFragment=order-list"`) and `{ajax 'orders', fragment='order-list'}`.

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
again on every hit. A `{page}` or `{code}` pass adds to the page's stacks; a sandboxed pass
leaves no trace. From PHP: `padStackPush ( 'scripts', $html, $once )`.

---

### stack
Everything pushed to a stack, in push order.

```html
<head>
  {stack 'styles'}
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
{dump}
```

**Behavior:** Calls `padDump()` with message

---

### debug
Show a value in the page while it renders - for a local request only.

```html
{debug $order}
{debug}
```

**Behavior:** Writes a collapsible `<details>` tree of the value. `{debug}` alone shows every
field visible where it stands - the rows of the enclosing levels, innermost first - and the
application's variables. A missing `$name` is shown as missing rather than failing. Only a
local request - the command line, or loopback with nothing forwarded - gets it; any other gets
nothing, and `$padDiagnostics = FALSE` switches it off everywhere. Unlike `{dump}` the
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
{assert $total eq 42}
{assert $lines gt 0, 'the cart has lines'}
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
{error 'Error message'}
```

**Parameters:**
- First parameter: Error message

**Behavior:** Calls `padError()` with message

---

### exception
Throw PHP exception.

```html
{exception 'Exception message'}
```

**Parameters:**
- First parameter: Exception message

**Behavior:** Throws PHP Exception

---

### exit
Exit PAD processing.

```html
{exit}
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
{continue 'tagname'}
```

**Parameters:**
- First parameter: Tag name to continue

**Behavior:** Skips to next iteration (like PHP's continue)

---

### cease
Soft stop - graceful end of loop.

```html
{cease 'tagname'}
```

**Parameters:**
- First parameter: Tag name to cease

**Behavior:** Gracefully ends loop processing

---

### break
Hard stop - immediate exit from loop.

```html
{break 'tagname'}
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
{resume reverse}
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
| `chart` | Output | Bar, line or sparkline chart as inline SVG |
| `sparkline` | Output | Word-sized line chart as inline SVG |
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
| `sequence:` | Sequence type | `{sequence:fibonacci}` |
| `action:` | Sequence action | `{action:reverse}` |
| `flag:` / `make:` / `keep:` / `remove:` | Sequence operations | `{make:fibonacci}` |

---

## Options Reference

Options modify tag behavior. Add them to any tag, separated by commas: `{data 'x', ignore}`.
An option name without a value directly after a single parameter may leave the comma out -
`{data 'x' ignore}` is the same tag; one with a value, `{pad 'x' toContent='c'}`, is lost. That holds for the options below and an application's own `_options/`; any
other word after a value is a pipe, `{echo 'abc' upper}` gives `ABC`.

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
  Index: {current@items} of {count@items}
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
