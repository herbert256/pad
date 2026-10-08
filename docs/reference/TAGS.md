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

### asset
The address of a file of `www/<app>/` with a version of its contents, for a far-future cache.

```html
<link rel="stylesheet" href="{asset 'charts.css'}">     <!-- /charts/charts.css?v=1f3a9c0b2e -->
{asset 'app.js', tag}                                     <!-- <script src="..." defer></script> -->
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
{chart 'bar', data='sales', label='month', value='online, shop', stacked}
{chart 'pie', data='budget', label='post', value='amount'}
{chart 'scatter', data='cars', x='weight', y='mpg', color='origin', trend}
{chart 'heatmap', data='visits', x='hour', y='day', value='count'}
{chart 'sankey', data='energy', source='from', target='to', value='amount'}
{chart 'network', data='friends', source='from', target='to', layout='radial'}
{chart 'treemap', data='spend', levels='dept', label='post', value='amount'}
{chart 'gauge', value=72, label='Disk in use', unit='%', bands='60, 85', target=80}
{chart 'radar', data='phones', label='model', value='battery, camera, screen, speed'}
{chart 'waterfall', data='result', label='step', value='amount', total='Profit'}
{chart 'histogram', data='people', value='height', bins=12}
{chart 'boxplot', data='salaries', label='team', value='salary'}
{chart 'calendar', data='commits', date='day', value='count', year=2026}
{chart 'gantt', data='plan', label='task', from='begin', to='until', progress='done'}
{chart 'area', data='energy', label='year', value='coal, gas, wind, solar', stacked}
{chart 'stream', data='listening', label='month', value='pop, rock, jazz'}
{chart 'multiples', data='climate', by='city', label='month', value='temperature'}
{chart 'candlestick', data='share', label='day', volume='volume'}
{chart 'dual', data='results', label='month', value='revenue', line='margin'}
{chart 'spiral', data='sales', label='month', value='amount', turn='year'}
{chart 'rose', data='deaths', label='month', value='disease, wounds, other'}
{chart 'radialbar', data='funds', label='class', value='raised', to=5000}
{chart 'funnel', data='shop', label='step', value='visitors'}
{chart 'pyramid', data='people', label='age', value='men, women'}
{chart 'waffle', data='energy', label='use', value='kwh', cells=100}
{chart 'marimekko', data='market', label='region', value='alpha, beta, gamma, other'}
{chart 'parliament', data='election', label='party', value='seats'}
{chart 'venn', data='speakers', sets='speaks', value='students'}
{chart 'density', data='trials', label='group', value='ms'}
{chart 'violin', data='scores', label='class', value='score'}
{chart 'bullet', data='kpis', label='kpi', value='actual', target='goal', bands='50, 75'}
{chart 'rings', data='today', label='goal', value='done', to='target'}
{chart 'parallel', data='cars', value='mpg, cylinders, horsepower, weight', color='origin'}
{chart 'proportional', data='emissions', label='country', value='mt'}
{chart 'chord', data='migration', source='from', target='to', value='people'}
{chart 'arc', data='coauthors', source='author', target='with', value='papers'}
{chart 'pack', data='files', levels='folder', label='file', value='kb'}
{chart 'orgchart', data='staff', id='id', parent='boss', label='name', sub='role'}
```

**Kinds:**

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
| `network` | Nodes and the links between them; `layout='force'` (default, the same picture for the same data) or `'radial'`, `arrows` for the direction, `value` for thicker links | `source`, `target`, `value`, `layout`, `arrows` |
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
Visits {sparkline data='visits', value='count'}
{sparkline sequence='fibonacci', rows=12}
```

**Behavior:** `{chart 'sparkline', ...}` by another name, with the same options; 120 x 32 by
default, scaled to its own minimum and maximum.

---

### qr
A QR code as inline SVG - encoded on the server (`lib/qr.php`), no image library, no remote
service, no JavaScript.

```html
{qr 'https://example.com/order/42'}
{qr $url, size=200, level='H', title='Open the order on your phone'}
{qr 'WIFI:T:WPA;S:Office;P:secret;;', color='#2e7d32'}
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
A barcode as inline SVG - EAN-13, EAN-8, UPC-A or Code 128, encoded on the server
(`lib/barcode.php`).

```html
{barcode '871234567890'}                          <!-- EAN-13, the check digit added -->
{barcode $parcel, type='code128', height=70}
{barcode '03600029145', type='upca'}
{barcode 'PAD', scale=3, plain, color='#2e7d32'}
```

| Option | Description |
|--------|-------------|
| first parameter | The number or text |
| `type` | `ean13`, `ean8`, `upca` or `code128`; left out, twelve or thirteen digits are an EAN-13 and anything else Code 128 |
| `height` | The height of the bars in pixels, default 60 |
| `scale` | The width of one module (the thinnest bar) in pixels, default 2 |
| `title` | The accessible name, by default the code |
| `plain` | Bars only - no digits under them |
| `color`, `background` | Default `#000` on `#fff` |

**Behavior:** an EAN or UPC number is given with or without its check digit - left off, it
is added; given, it is checked. EAN and UPC draw their guard bars longer and set the digits
in their groups. Code 128 takes printable ASCII and packs a run of four digits or more two to
a symbol (set C). A wrong check digit, a wrong length, a character Code 128 cannot hold or an
unknown type is an error under the strict check, and writes nothing.

---

### calendar
A month as a calendar, with its events (`lib/calendar.php`).

```html
{calendar}                                                   <!-- this month -->
{calendar '2026-10', data='agenda', date='when', title='what', link='url'}
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

**Behavior:** as a single tag it answers a table - the month in its caption between links to
the month before and after (keeping the other request values, as `{pager}` does), a column
per weekday, a cell per day with its `<time>` and its events as a list; today has
`aria-current="date"`. A small default look rides along under `:where()`, so the page's CSS
wins. As a pair it hands over the weeks as rows - `$week` (ISO number) and `days` - and
every day is a row too: `$date`, `$day`, `$weekday`, `$month`, `$other` (outside the
month), `$today`, `$weekend`, `$count`, `$class` (`other today weekend events`, as they
apply) and `events`, the data rows of that date. A store named `events` hides the day's
field of that name - `{level:events}` reaches it then.

---

### highlight
Source code coloured on the server (`lib/highlight.php`) - no JavaScript library.

```html
{highlight 'php', lines, mark='3, 5-7'}
  <?php echo padArrSum ( $orders, 'total' ); ?>
{/highlight}
{highlight file='_data/products.json'}
{echo $query | highlight('sql')}
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

## Presentation Tags

Tags that draw, format and interact - the showcase application has an example of each.

---

### avatar
An initials avatar as inline SVG - drawn on the server (`lib/avatar.php`), no image, no
remote service: the same name is the same picture on every page and in every e-mail.

```html
{avatar 'Herbert Jebbink'}
{avatar $name, size=32, shape='square'}
{users}{avatar $email, size=40} {$name}{/users}
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
hash of the value (`lib/identicon.php`): every account its own picture, nothing sent anywhere.

```html
{identicon $email}
{identicon $user.id, size=32, title='Your identicon'}
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
(`lib/placeholder.php`): wireframes and layouts that wait for their pictures, no placeholder
service.

```html
{placeholder '600x300', text='Hero image'}
{placeholder ratio='16:9', fluid}
{placeholder ratio='4:3', width=320}
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
line with its label and percentage (`lib/progress.php`); with `steps` a row of step dots. No
script, it prints.

```html
{progress 72, max=100, label='Upload'}
{progress $done, label=$file, color='success', size='small'}
{progress 3, max=5, steps, label='Checkout'}
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
score, the score said in words (`lib/rating.php`).

```html
{rating 4.5, max=5}
{products}{$product} {rating $score, size=16}{/products}
{rating 3.75, icon='heart'}
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
An interface icon from PAD's own set of 79, drawn on a 24 by 24 grid as lines, circles and
rectangles (`lib/icon.php`) and written as inline SVG in the colour of the text around it - no
icon font, no sprite, no request.

```html
{icon 'arrow-right'}
<button>{icon 'trash', size=16} Delete</button>
<a href="?next">{icon 'chevron-right', label='Next page'}</a>
```

**Parameters:**

| Option | Description |
|--------|-------------|
| first parameter | The name: arrows and chevrons (`arrow-right`, `chevron-down`, `external`, `refresh`, `sort`, `maximize` ...), marks (`check`, `x`, `plus`, `minus`, `info`, `alert`, `help`, `star`, `heart`, `flag`, `tag` ...), layout (`home`, `menu`, `grid`, `list`, `search`, `filter`, `settings`, `link` ...), people (`user`, `users`, `mail`, `message`, `phone`, `bell` ...), time and places (`calendar`, `clock`, `globe`, `map-pin`, `sun`, `moon`, `cloud`), files (`file`, `folder`, `copy`, `edit`, `trash`, `download`, `upload`, `image`, `camera`, `cart` ...), media (`play`, `pause`, `stop`), security (`lock`, `unlock`, `key`, `eye`, `eye-off`, `power`) and code (`code`, `terminal`, `database`, `bar-chart`) - `padIconNames ()` lists them all |
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
The Gravatar picture of an e-mail address as an `<img>` (`lib/gravatar.php`) - only the
SHA-256 hash of the address leaves the page - or, with `fallback`, a local SVG drawn on the
server and no request to a third party at all.

```html
{gravatar $email, size=64, default='identicon', alt=$name}
{gravatar $email, fallback='avatar', alt=$name}
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

### chess
A chess position from FEN as inline SVG - the board, the pieces drawn as shapes of PAD's own
(no font, no image), the coordinates, marked squares and arrows (`lib/chess.php`).

```html
{chess 'rnbqkbnr/pppppppp/8/8/4P3/8/PPPP1PPP/RNBQKBNR b KQkq e3 0 1'}
{chess $fen, flip, highlight='d1, d8', arrow='g5-d8'}
{chess '8/8/8/4k3/8/8/4K3/8', size=240, title='Opposition'}
```

**Parameters:**

| Option | Description |
|--------|-------------|
| first parameter | The position in FEN - the placement alone will do; side to move, castling, en passant and the two counters are checked when written |
| `flip` | Black at the bottom |
| `highlight` | Squares to mark, `'e2, e4'` |
| `arrow` | Arrows, `'e2-e4, g1-f3'` - from the middle of one square to the middle of the other, over the pieces |
| `size` | Width and height in pixels, default 360 - shrinks to fit a narrower container |
| `title` | The accessible name, default `Chess position, White to move` |

**Behavior:** `role="img"` with `<title>` and a `<desc>` that lists every piece by side and
kind (`White: king g1; rooks a1, f1; pawns ...`), the side to move, castling, en passant, the
marks and the arrows. The colours are `--pad-chess-light`, `-dark`, `-white`, `-black`,
`-ink`, `-mark`, `-arrow` and `-text` on `.pad-chess`, `light-dark()` defaults, written once
per page; the ids come from the board itself. A FEN with a wrong rank count, an unknown
piece letter or a wrong field, and a square or arrow that is not on the board, are errors
under the strict check.

---

### sudoku
A sudoku as a table - the givens bold, the open cells empty, or with `solve` the solution
filled in by a backtracking solver in another colour (`lib/sudoku.php`).

```html
{sudoku '53..7....6..195....98....6.8...6...34..8.3..17...2...6.6....28....419..5....8..79'}
{sudoku $puzzle, solve, title='Saturday puzzle'}
```

**Parameters:**

| Option | Description |
|--------|-------------|
| first parameter | The 81 cells row by row: a digit 1-9 for a given, `.` `0` `_` or `-` for an open cell; whitespace and the bars and plus signs of a drawn grid are passed over, so nine lines will do |
| `solve` | Fill the open cells with the solution |
| `title` | A caption; without it the table is named by `aria-label` (`Sudoku, 30 givens`) |

**Behavior:** the solver fills the cell with the fewest candidates first, a bit mask per row,
column and box - the hardest published puzzles take well under a second. An open cell says
`empty` to a screen reader. The colours are `--pad-sudoku-line`, `-box`, `-given`, `-solved`,
`-cell` and `-shade` on `.pad-sudoku`, `light-dark()` defaults, written once per page, every
rule under `:where()`. A wrong cell count, an unknown character, two equal givens in a row,
column or box, and - with `solve` - a puzzle without a solution are errors under the strict
check.

---

### crossword
A crossword made from a list of words: laid out across and down so they cross, numbered,
drawn as an empty SVG grid beside the Across and Down clues (`lib/crossword.php`).

```html
{crossword}
  ECHO: Writes a value without the sanitize chain
  PIPE: The | that hands a value to a function
  ARRAY: An ordered map
{/crossword}

{crossword solution, title='Week 41'}...{/crossword}
```

**Parameters:**

| Option | Description |
|--------|-------------|
| content | A line per word, `WORD: clue` - taken as it stands, so a clue may hold braces; spaces, hyphens and apostrophes in a word are dropped, letters only |
| `solution` | The letters in the grid and the answers behind the clues |
| `title` | The grid's accessible name, default `Crossword` |

**Behavior:** the layout is greedy and the same every time - the longest word first, across;
then each next word, longest first, where it crosses the most letters already down on the
smallest grid, never side by side with another word. A word that fits nowhere is left out
and named under the clues (`Not placed: ...`) and in the grid's `<desc>`. The squares are
numbered row by row where a word starts; each clue carries its length. The colours are
`--pad-crossword-square`, `-line`, `-number`, `-letter` and `-answer` on `.pad-crossword`,
`light-dark()` defaults, written once per page. The tag written alone, a line without `:`, a
word that is no letters, a word without a clue or written twice are errors under the strict
check.

---

### diff
What changed between two texts - word by word in the flow of the text, or line by line in
one column or side by side - with `<del>` and `<ins>` marked and styled (`lib/diff.php`).

```html
{diff $draft, $final}
{diff $old, $new, lines, from='Monday', to='Friday'}
{diff $before, $after, side, from='before', to='after'}
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
words when given, every match in a `<mark>` (`lib/excerpt.php`).

```html
{excerpt $post, words=40}
{results}<p>{excerpt $body, words=24, highlight=$q}</p>{/results}
{excerpt $page.html, words=30, html}
```

**Parameters:**

| Option | Description |
|--------|-------------|
| first parameter | The text |
| `words` | The length in words, default 40 |
| `highlight` | Search words - a query, split at everything that is no letter or digit |
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
every request for the same seed (`lib/lorem.php`).

```html
<h2>{lorem words=4}</h2>
<p>{lorem sentences=2}</p>
{lorem paragraphs=3, seed=7, varied}
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

### tabs
Panels behind a row of tabs, without JavaScript - a radio button and a label per tab, the CSS
showing the panel of the checked one (`lib/tabs.php`). The items are `{tab}` pairs, which
`{accordion}` and `{carousel}` take as well.

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
`{accordion}` or `{carousel}` below it instead of printing it - it may stand in an `{if}` or
in a loop that makes a tab per row. The set is a radio group: one stop in the tab order, the
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
label as the `<summary>` (`lib/accordion.php`).

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
(`lib/modal.php`).

```html
{modal 'terms', title='Terms of sale', button='Read the terms'}
  <p>These terms apply to every order ...</p>
{/modal}
```

**Parameters:**

| Option | Description |
|--------|-------------|
| first parameter | The name - letters, digits, `_` and `-`; the dialog's id is `pad-modal-<name>` |
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
(`lib/carousel.php`).

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
with one click, so it is copied by hand where the button cannot run (`lib/copy.php`).

```html
{copy 'composer require pad/pad'}
{copy $command, label='Copy command'}
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
percentages - built on a `{live}` region, and a plain form without scripting (`lib/poll.php`).

```html
{poll 'favorite-language', options='PHP, Python, Ruby, Go', title='Which language do you reach for first?'}
{poll 'favorite-language', options='PHP, Python, Ruby, Go', results}
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

### img
A picture of `www/<application>/` as a thumbnail of the size the page shows it at - resized
once with GD (`lib/img.php`) and kept in `www/<application>/_thumbs/`, where the web server
sends it like any asset.

```html
{img 'photos/harbour.jpg', width=400, height=300, alt='The harbour at dawn'}
{img 'photos/dunes.jpg', width=300, height=300, fit='contain', format='webp', quality=70, alt=''}
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

### video
A YouTube or Vimeo video as its poster with a play button; the player's iframe is loaded
only when the button is pressed (`lib/video.php`) - nothing of the video site runs before the
visitor asks for it. A file of `www/<application>/` is a `<video>` element.

```html
{video 'https://www.youtube.com/watch?v=aqz-KE-bpKQ', title='Big Buck Bunny'}
{video 'https://vimeo.com/76979871#t=12s', title='The new Vimeo player', poster='photos/still.jpg'}
{video 'clips/sunrise.webm', title='Sunrise', poster='photos/sunrise.jpg'}
```

**Parameters:**

| Option | Description |
|--------|-------------|
| first parameter | A YouTube address (`watch?v=`, `youtu.be/`, `/shorts/`, `/embed/`, `/live/`, youtube-nocookie.com), a Vimeo address (`vimeo.com/<id>`, `/channels/x/<id>`, `player.vimeo.com/video/<id>`) or a `.mp4`, `.webm`, `.ogv` or `.mov` file of `www/<application>/` |
| `title` | Required - the button's text, the iframe's title, the video element's name |
| `poster` | A picture of `www/<application>/` (versioned as `{asset}`) in place of the site's thumbnail |
| `start` | The second to start at; a `t=`, `start=` or `#t=` in the address says it too (`90`, `1m30s`, `1:30`) |
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

### pdf
What the content renders, sent as a PDF instead of the page - a writer in plain PHP
(`lib/pdf.php`): PDF 1.4, the standard fonts every reader has, no library, no browser.

```html
{pdf file='invoice-2026-114', size='A4'}
  <h1>Invoice {$number}</h1>
  <table>{lines}<tr><td>{$item}</td><td align="right">{$amount}</td></tr>{/lines}</table>
{/pdf}
{pdf preview, file='invoice'}...{/pdf}      # the content on a sheet, links to ?page&padPdf
{pdf download}                              # single: the whole page as an attachment
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
`font` is an error under the strict check. From PHP: `padPdf ( $html, [ 'size' => 'Letter' ] )`
answers the bytes.

---

### diagram
A flowchart or a sequence diagram from a few lines of text in the content, written the
Mermaid way and laid out on the server as inline SVG (`lib/diagram.php`) - no JavaScript.

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
```

**Parameters:**

| Option | Description |
|--------|-------------|
| `type` | `flowchart` (default) or `sequence` - a first line `sequenceDiagram` says so too |
| `direction` | `TB` (default), `BT`, `LR` or `RL` - else a first line `graph LR` / `flowchart LR` names it |
| `edges` | `orthogonal` (default, rounded turns in channels of their own), `straight` or `curved` |
| `title` | The accessible name, default `Flowchart` / `Sequence diagram` |

**Behavior:** a flowchart line is a chain of nodes and edges: `A[Box]`, `B(Rounded)`,
`C([Stadium])`, `D{Decision?}`, `E((Circle))`, `F[(Database)]`, `G{{Hexagon}}` - a quoted
text keeps its brackets, `<br>` breaks a line, long texts wrap - joined by `-->` (arrow),
`---` (line), `-.->` / `-.-` (dashed), `==>` / `===` (thick), a label as `-->|text|` or
`-- text -->`, `&` for several nodes on one side (`A & B --> C`), `%%` a comment. The layout
is layered: cycles broken by a depth-first walk, ranks by the longest path, a dummy node per
rank a long edge crosses, the order in a rank by barycentre sweeps keeping the fewest
crossings, positions by isotonic regression; node sizes follow their text, ports spread along
a side, labels sit on a plate half way and step aside for each other. A sequence diagram
takes `participant A as Alice`, `actor U`, messages `->>` (arrow), `-->>` (dashed), `->` /
`-->` (no head), `-x` (cross), `-)` (open), `Note left of|right of|over A[,B]: text`, frames
`loop`, `alt` / `else`, `opt`, `par` / `and`, `critical`, `break` closed by `end`, and
`autonumber`. The content is read as it stands - the braces of `B{Paid?}` need no
`{ignore}`. role="img" with a `<title>` and a `<desc>` that says every edge or message; the
colours are `--pad-diagram-node`, `-border`, `-decision`, `-decision-border`, `-round`,
`-round-border`, `-edge` and `-note` on `.pad-chart-diagram`, `light-dark()` defaults. A
diagram without nodes answers nothing and its `@else@` shows; a line it cannot read, an
unknown type, direction or edges value are errors under the strict check.

---

### map
A choropleth map as inline SVG - every country coloured by a value of its row, drawn on the
server from Natural Earth's outlines (`lib/map.php`, `lib/map/world.json`) - no JavaScript,
no tiles, no remote service.

```html
{map 'world', data='sales', key='country', value='amount'}
{map 'europe', data='visitors', scale='quantile', title='Visitors per country'}
{map 'south-america', key='code', value='forest', width=320}
  code,forest
  BRA,59.1
{/map}
```

**Parameters:**

| Option | Description |
|--------|-------------|
| first parameter | The view: `world` (default), `europe`, `africa`, `asia`, `north-america`, `south-america` or `oceania` |
| `data` | The rows - a `{data}` store, a page array or a `_data` file, as for `{chart}`; as a pair the content is the rows (JSON, YAML, XML or CSV) |
| `key` | The field with the country: its ISO 3166 alpha-2 or alpha-3 code or its English name, in any case - else the first field that is no number |
| `value` | The number - else the first numeric field |
| `scale` | `linear` (default: seven steps from the lowest value to the highest) or `quantile` (seven steps by rank) |
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
the strict check.

---

### timeline
Events on a time axis as inline SVG (`lib/timeline.php`) - cards above and below the axis,
spans as bars, or one under the other for a long list.

```html
{timeline data='releases', date='when', label='what', text='detail'}
{timeline data='plan', from='start', to='end', date='day', label='name', color='kind'}
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
Rows as a complete, accessible HTML table (`lib/datatable.php`) - a header from the field names,
numbers right-aligned and formatted alike down their column, a totals row, header links that
sort and page links that page, all on the server.

```html
{datatable data='orders', columns='number, customer, total', totals='total'}
{datatable data='orders', format="total:currency('EUR'), date:date('j M')", sortable, striped}
{datatable data='cities', rows=10, sortable, caption='Largest cities'}
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
| `format` | A pipe per column, `column:pipe` - `total:money, date:date('j M Y')` - run as `{echo $value \| pipe}` |
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

### toc
A table of contents of the page from its own rendered headings (`lib/toc.php`), nested and linked,
written where `{toc}` stands once the whole page has rendered - the headings below it included.

```html
{toc}
{toc levels='2-4', title='On this page', list='ul'}
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
The time left until a moment, in words, inside a `<time datetime>` (`lib/countdown.php`) -
optionally ticking every second in the browser.

```html
{countdown '2026-12-31 00:00'}                         <!-- 84 days, 3 hours -->
{countdown $launch, units=3, live, past='We are live!'}
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
full, as its `title` (`lib/timeago.php`) - the `ago` pipe's words with the semantic element.

```html
{timeago $created}        <!-- <time datetime="2026-10-08T09:10:00+02:00" title="Thursday 8 October 2026, 09:10">5 minutes ago</time> -->
{timeago $posted, format='j M Y'}
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
An emoji by its GitHub or Slack shortcode, as a labelled image (`lib/emoji.php`); the `emoji` pipe
replaces the shortcodes in a text.

```html
{emoji 'rocket'}                   <!-- <span class="pad-emoji" role="img" aria-label="rocket">🚀</span> -->
{emoji ':tada:', label='Party!'}
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
A country's flag from its ISO 3166-1 code, optionally with its English name (`lib/country.php`).

```html
{country 'NL'}             <!-- <span class="pad-country" role="img" aria-label="Netherlands">🇳🇱</span> -->
{country 'NLD', name}      <!-- the flag and Netherlands beside it -->
{country $code, name}
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
A JSON value or a PHP array as a tree to fold open and shut (`lib/jsonview.php`) - each object and
list a `<details>`, no script.

```html
{jsonview $response, open=1, title='GET /orders/42'}
{jsonview data='settings'}
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
again on every hit. A plain `{page}` or `{code}` pass adds to the page's stacks; a sandbox, clean
or reset pass keeps its pushes to itself. From PHP: `padStackPush ( 'scripts', $html, $once )`.

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
| `chess` | Output | A chess position from FEN as inline SVG - pieces, coordinates, marked squares, arrows |
| `sudoku` | Output | A sudoku grid as a table, givens bold - solved on the server with `solve` |
| `crossword` | Output | Words and clues laid out as a numbered crossword grid (SVG) with the Across and Down lists |
| `diff` | Output | The differences between two texts - words inline, or lines in one column or side by side |
| `excerpt` | Output | A text cut to whole words around the first search match, the matches marked |
| `lorem` | Output | Lorem ipsum placeholder text - words, sentences or paragraphs, the same for the same seed |
| `tabs` | Output | Panels behind a row of tabs - radio buttons and CSS, no JavaScript; `{tab}` is one item |
| `accordion` | Output | Items that open on a click - every `{tab}` a `<details>`, one open at a time with `single` |
| `modal` | Output | A `<dialog>` and its opening button - invoker commands, a small script, or a link without scripting |
| `carousel` | Output | Slides that scroll and snap sideways, previous/next and dot links - no autoplay |
| `copy` | Output | A text with a copy-to-clipboard button, selectable by hand without scripting |
| `poll` | Web | A vote once per visitor, results as bars - a live region, a plain form without scripting |
| `img` | Output | Resized, cached thumbnail of a picture, with a 2x srcset |
| `video` | Output | YouTube or Vimeo as a poster and play button, the iframe only on a click; a local file as `<video>` |
| `pdf` | Output | The rendered content sent as a PDF, written in plain PHP - fonts, tables, JPEG pictures |
| `diagram` | Output | A flowchart or sequence diagram from Mermaid-style lines, laid out on the server as inline SVG |
| `map` | Output | A choropleth map of the world or a region as inline SVG, a colour step per value |
| `timeline` | Output | Events on a time axis as inline SVG - cards above and below, spans as bars, or a vertical list |
| `datatable` | Output | Rows as an accessible HTML table - aligned numbers, totals, sorting and paging links |
| `toc` | Output | A table of contents from the page's own rendered headings, ids added |
| `countdown` | Output | The time left until a moment in words, in a time element, optionally ticking |
| `timeago` | Output | How long ago a moment was, in a time element with the exact moment |
| `emoji` | Output | An emoji by its shortcode, labelled for screen readers; also an `emoji` pipe |
| `country` | Output | A country's flag emoji and English name from an ISO 3166 code |
| `jsonview` | Output | A JSON value or PHP array as a foldable, coloured tree of details elements |
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
| `chart` | Output | A chart as inline SVG - 46 kinds, from bar and line to candlestick, chord and org chart |
| `sparkline` | Output | Word-sized line chart as inline SVG |
| `qr` | Output | QR code as inline SVG |
| `barcode` | Output | EAN-13, EAN-8, UPC-A or Code 128 barcode as inline SVG |
| `calendar` | Output | A month as a table with its events, or its weeks and days as rows |
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
