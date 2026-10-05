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
when a `{code}` pass of any kind returns, so a rotation counted inside one is rolled back
with it, and a pass starts every rotation it meets from the first value.

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
Set one or more variables. At the top of a page the variable is a global; inside a level the
assignment shadows any global of the name and is unwound when the level closes, so what a
loop sets does not outlive it.

```html
{set name='value', count=5, active=TRUE}
```

**Parameters:**
- Named parameters become global variables

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

**Behavior:** Stores data and iterates over it

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
Store boolean value to store.

```html
{bool 'store_name'}
```

**Parameters:**
- First parameter: Store name

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
{count $arrayName}
  Has elements
{/count}
```

**Parameters:**
- First parameter: Variable or data store name

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

**Behavior:** Executes the page's PHP and PAD files

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
Handle AJAX request.

```html
{ajax 'handler'}
{ajax 'orders', fragment='order-list'}
```

**Parameters:**
- First parameter: AJAX handler name
- `fragment` - fetch only that response fragment of the page (see `fragment`)

---

### pad
Generic PAD include tag.

```html
{pad data='source'}
  Template content
{/pad}
```

**Behavior:** Processes PAD template with data

---

## Navigation Tags

### redirect
Redirect to another URL.

```html
{redirect 'url'}
```

**Parameters:**
- First parameter: URL to redirect to

**Behavior:** Performs HTTP redirect

---

### restart
Restart PAD processing with new page.

```html
{restart 'pagename', param1='value1'}
```

**Parameters:**
- First parameter: Page name
- Additional parameters: Passed to new page

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

**Items:** the form's name first; `method='get'` for a form that does not post; every other
item is an attribute of the `<form>` tag (`action=`, `class=`, `enctype=`), written as
`{attrs}` writes them.

**Behavior:** A posting form gets the hidden `padCsrfToken` field and a hidden `padForm`
field holding its name - `padPosted('contact')` is TRUE when this form came back. The fields
inside refill only when their own form came back; a `{form method='get'}` refills from the
query string.

---

### input
A form field that refills from what was posted and shows the error `padValidate` found for it.

```html
{input 'email', type='email', label='E-mail', required, placeholder='you@example.org'}
{input 'terms', type='checkbox', label='I agree', value='yes'}
```

**Items:** the field name first; `type=` (default `text`), `label=` (a `<label for>` before
the field, after a checkbox or radio), `value=` (the value before anything was posted),
`id=` (default: the name), `checked` (a checkbox's state before a post); every other item is
an attribute of the field.

**Behavior:** After a post of its form the field shows the posted value, escaped; a checkbox
or radio is checked when the posted value is its own; a password or file field is never
refilled. When `padValidate` reported the field, its message follows in
`<span class="error" id="<id>-error">`, worded with the field's label, and the field gets
`aria-invalid="true"` and `aria-describedby`.

---

### textarea
`{input}`'s refill, label and error for a text area.

```html
{textarea 'message', label='Message', rows=6, required}
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
- `base`: Base path (`app`, `data`, `pad`, or absolute)
- `group`: Group results by item name

**Returns:** Array with `path`, `file`, `ext`, `item`, `dir` for each entry

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
{curl 'http://example.com', method='POST', data='payload'}
```

**Parameters:**
- `url` / first param: URL to request
- Additional parameters added as query string
- `SELF://` prefix replaced with current host
- `ttl=600`: keep the answer that many seconds (see below)

**Behavior:** Makes HTTP request, throws error if result is not 200

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

## Output Tags

### echo
Evaluate and output expression.

```html
{echo $variable}
{echo '5 + 3'}
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
whose array value adds its keys as attributes. Names are HTML attribute names, dashes
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
PHP that runs before the template - the page's `.php` - runs on a hit too.

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
{reactData id="products" provider="products" type="array"}
```

**Parameters:**
- `id` - DOM id of the generated div (default `myReactId`)
- `provider` - Provider file in `_providers/<name>.php` (defaults to `id`)
- `type` - `record`, `array` or `check` (default `record`)

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
application's variables. A missing `$name` is shown as missing rather than failing. A
request that is not local (the command line, or loopback with nothing forwarded) gets
nothing, and `$padDiagnostics = FALSE` switches it off everywhere. Unlike `{dump}` the
request goes on.

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
Set sequence flag.

```html
{flag}
```

**Behavior:** Used within sequence processing

---

### keep
Keep sequence values matching criteria.

```html
{keep}
```

**Behavior:** Filter to keep matching values

---

### remove
Remove sequence values matching criteria.

```html
{remove}
```

**Behavior:** Filter to remove matching values

---

### make
Transform sequence values.

```html
{make}
```

**Behavior:** Transform values during sequence generation

---

### resume
Resume a previously ceased sequence iteration.

```html
{resume reverse}
```

**Behavior:** Delegates to the sequence subsystem (`sequence/start/tags/resume.php`); continues iteration of a stored sequence, optionally through an action. See [sequences](../sequences/).

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
| `get` | Variables | Get stored content |
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
| `ajax` | Execution | AJAX handler |
| `pad` | Execution | PAD include |
| `redirect` | Navigation | HTTP redirect |
| `restart` | Navigation | Restart processing |
| `pager` | Navigation | Page links for a tag with the page option |
| `csrf` | Web | Hidden CSRF token field of the session |
| `extends` | Layout | Frame the page with a layout |
| `block` | Layout | A named region of a layout or wrapper, and its override |
| `parent` | Layout | The overridden content, inside an overriding block |
| `slot` | Layout | A named place for content in a custom tag, and its fill |
| `parms` | Layout | Declare a custom tag's parameters, required or with defaults |
| `fragment` | Layout | A named part of the page a request can ask for alone |
| `push` | Layout | Add rendered text to a named stack |
| `stack` | Layout | Print a stack, filled in after the page has rendered |
| `form` | Web | Form with CSRF token and name, its fields refill |
| `input` | Web | Form field with refill, label and validation error |
| `textarea` | Web | Text area with refill, label and validation error |
| `flash` | Web | Flash messages that survive one redirect |
| `files` | Files | List files |
| `dir` | Files | Directory listing |
| `file` | Files | Write file |
| `exists` | Files | Check file exists |
| `open` | Files | Opening brace |
| `close` | Files | Closing brace |
| `curl` | Network | HTTP request |
| `echo` | Output | Evaluate/output |
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
| `flag` | Sequences | Set flag |
| `keep` | Sequences | Keep matching |
| `remove` | Sequences | Remove matching |
| `make` | Sequences | Transform values |
| `resume` | Sequences | Resume ceased iteration |

---

## Type Prefixes

Resolve naming conflicts with explicit prefixes:

| Prefix | Purpose | Example |
|--------|---------|---------|
| `app:` | App tag from `_tags/` | `{app:mytag}` |
| `common:` | Tag from the `_common` app | `{common:menu}` |
| `pad:` | Built-in PAD tag | `{pad:if}` |
| `php:` | Call PHP function | `{php:strlen(@)}` |
| `function:` | Custom PAD function | `{$x \| function:myfunc}` |
| `data:` | Defined data block | `{data:items}` |
| `content:` | Content block | `{content:header}` |
| `include:` | Snippet from `_include/` | `{include:header}` |
| `pull:` | Stored sequence | `{pull:mySeq}` |
| `field:` | Database field | `{field:"name from users"}` |
| `select:` | Declared select table | `{select:users}` |
| `local:` | Files from `_data/` | `{local:menu.json}` |
| `constant:` | PHP constant | `{constant:PHP_VERSION}` |
| `bool:` | Boolean store | `{bool:isAdmin}` |
| `array:` | Access array as loop | `{array:items}` |
| `level:` | Level variable | `{level:varName}` |
| `parm:` | Tag parameter value | `{parm:name}` |
| `property:` | Tag property | `{property:id}` |
| `script:` | Script from `_scripts/` | `{script:backup}` |
| `sequence:` | Sequence type | `{sequence:fibonacci}` |
| `action:` | Sequence action | `{action:reverse}` |
| `flag:` / `make:` / `keep:` / `remove:` | Sequence operations | `{make:fibonacci}` |

---

## Options Reference

Options modify tag behavior. Add them to any tag, separated by commas: `{data 'x', ignore}`.
An option directly after a single parameter may leave the comma out - `{data 'x' ignore}`
is the same tag. That holds for the options below and an application's own `_options/`; any
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
| `cache` | Keep the rendering for so many seconds (fragment cache) |
| `where` | Keep the rows an expression holds for |
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
value being piped in. The bare spelling is the property and nothing else; a row field of the
same name is what the `$`-spelling reads - `{$first@orders}` - so the sigil resolves the
collision.
