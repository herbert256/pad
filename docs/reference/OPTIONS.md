# PAD Options Reference

This document provides a complete reference for all PAD tag options.

## Usage

Options are specified on PAD tags, separated by commas:

```
{type:tagName option="value", anotherOption="value"}
```

A space does not separate two options: `{local:staff sort="name" first=1}` gives `sort` the value
`"name" first=1`, and `first` is never read. Only a bare option name - one without a value -
directly after a single parameter may leave its comma out - `{pad:data 'x' ignore}`; with a value,
`{pad:pad 'x' toContent='c'}` loses the option silently. Multiple options can be combined:

```
{local:staff print, toContent="list", quote="'", glue=", ", open="[", close="]"}
```

---

## Processing Phases

Options are processed at different phases during tag execution:

| Phase | When | Options |
|-------|------|---------|
| **Data** | Before the tag runs | `data` |
| **Flags** | When the tag has answered | `notOk` / `error`, `null`, `else`, `demand`, then `dump` and `content` |
| **Handling** | Then, in the order written | `sort`, `where`, `group`, `first`, `page`, `rows`, `dedup`, ... - see [HANDLING.md](HANDLING.md) |
| **Start** | Before the occurrences render | application `_options/`, then `ignore`, `print` |
| **Callback** | Around the occurrences | `callback`, `before` |
| **End** | After the occurrences rendered | application `_options/end/`, then `toBool`, `toContent`, `toData`, `tidy` |
| **Special** | Handled at specific points | `bool` (read by `{pad:if}`), `optional`, `noError` (an unknown tag), `cache` |

---

## Data Source Options

Options that specify where to get data from.

### data

Retrieves data from a named source.

```
{type:tagName data="sourceName"}
```

**Resolution order:**
1. `$padDataStore[$sourceName]` - Data store
2. `$pqStore[$sourceName]` - Sequence store
3. `padData($sourceName)` - Load from data provider

An address - `https://...`, `SELF://...` - a visitor chose is refused: a request value (GET,
POST, a JSON body, a cookie, a clean-URL segment) that is the whole address or its start, or
names its host (with or without the port), and a template that does not write that scheme and
host; so is a range of more than 10000 values whose end or whole text a request value gives.
A request value merely holding the host's text chooses nothing, and the page's own values -
`data=$url` with `$url` from its PHP or configuration - are fetched. The `{pad:curl}` tag's address
is judged the same way.

The guard has a limit: a template that builds the host from pieces of a request value is not
caught - `data='http://{$h}/x'` with `h` = `x@127.0.0.1:9`, or a host with a path tail behind
it. Never splice a visitor's value into the scheme or host of an address. Nor is a range whose
end a pipe made from a request value - `data='0..{$n | - 1}'`: never let a visitor's value set
the end of a range.

**Example:**
```
{array:users data="cachedUsers"}
{pad:pad data="queryResults"}...{/pad:pad}
```

### content

Retrieves content from the content store.

```
{type:tagName content="contentName"}
```

**Returns:** Content from `$padContentStore[$contentName]`

**Example:**
```
{pad:pad content="savedHeader"}
```

### merge

How the content `content=` brings joins the tag's own content.

```
{type:tagName content="contentName", merge="bottom"}
```

**Values:**
- `top` - the merged content above the tag's own (the default)
- `bottom` - below it
- `replace` - instead of it

An `@content@` in either one decides the place instead. Any other value is an error under the
strict check.

**Example:**
```
{pad:content 'h'}H{/pad:content}
{pad:pad content='h', merge='top'}X{/pad:pad}      →  HX
{pad:pad content='h', merge='bottom'}X{/pad:pad}   →  XH
{pad:pad content='h', merge='replace'}X{/pad:pad}  →  H
```

---

## Data Storage Options

Options that store results for later use.

### toData

Stores the processed data array to a named variable.

```
{type:tagName toData="variableName"}
```

**Stores to:** `$padDataStore[$variableName]`

**Behavior:**
- If no content/pair and data exists, stores the data array directly
- Otherwise stores the walked data after processing
- Clears the result output (silent storage)

**Example:**
```
{array:users where='$active eq 1', toData="activeUsers"}
{pad:pad data="activeUsers"}...{/pad:pad}
```

### toContent

Stores the generated content/output to a named variable.

```
{type:tagName toContent="variableName"}
```

**Stores to:** `$padContentStore[$variableName]`

**Behavior:**
- Stores `$padResult[$pad]` to the content store
- Clears the result output (silent storage)

**Example:**
```
{pad:pad toContent="pageHeader"}<h1>...</h1>{/pad:pad}
{pad:pad content="pageHeader"}
```

### toBool

Stores a boolean result based on the output state.

```
{type:tagName toBool="flagName"}
```

**Stores to:** `$padBoolStore[$flagName]`

**Boolean logic:**
- `TRUE` if result has non-empty trimmed content
- `FALSE` if null, else condition, or empty result

**Example:**
```
{array:users where='$admin eq 1', toBool="hasAdmins"}{$name}{/array:users}
{pad:if bool="hasAdmins"}...{/pad:if}
```

---

## Boolean & Conditional Options

### bool

Makes the stored boolean flag the condition of an `{pad:if}`.

```
{pad:if bool="flagName"}
```

**Behavior:**
- If flag exists in `$padBoolStore`, returns its value
- If not, the flag is `FALSE`, as an unset flag reads everywhere
- Read by `{pad:if}` only; on any other tag it does nothing

**Example:**
```
{app:checkPermission toBool="canEdit"}
{pad:if bool="canEdit"}Edit{/pad:if}
```

### optional

Marks a tag as optional - suppresses "not found" errors.

```
{$maybeUndefined | optional}
{type:tagName optional}
```

**Behavior:**
- Calls `padLevel('')` - returns empty string on failure
- Prevents error when field/variable doesn't exist

**Example:**
```
{$user.middleName | optional}
```

### demand

Marks a tag as required: it must produce something.

```
{type:tagName demand}
```

**Behavior:**
- A tag that answers NULL, FALSE, `''` or an empty array, or whose rows the handling options
  all took away - after `notOk`, `null` and `else` had their turn - is a PAD error: "Tag 'x'
  carries demand and produced nothing"

---

## Conditional Content Options

Options that show other content based on what the tag itself answered. The value of each is
the **name** of that content - a `{pad:content 'name'}` block, or else an `_include/` snippet or a
page of that name a request could ask for, no segment starting with `_` or `.`
(`get/content.php`) - never a tag, a PHP function or a path (a name with `..`, a backslash or
a leading `/` is refused), and never literal text: under the strict check a name written in
the template that names none of them - `else="Nothing here"` - is an error; a value naming
nothing shows nothing.

### null

Shows the named content when the tag answers NULL.

```
{type:tagName null="contentName"}
```

**Triggers when:**
- Result is `NULL`
- Result is `NaN`

`INF`, a float past the range, is a value: it prints, as `-INF` does.

**Behavior:**
- Resets state and replaces content
- Clears null/else flags, sets hit to TRUE

**Example:**
```
{pad:content 'noAvatar'}<img src="anonymous.png" alt="">{/pad:content}
{pad:avatar null="noAvatar"}...{/pad:avatar}
```

### else

Shows the named content when the tag answers an empty or false result.

```
{type:tagName else="contentName"}
```

**Triggers when:**
- Result is empty array `[]`
- Result is `FALSE`
- Result is empty string `''`

Rows that the handling options take away count too: when the tag found rows and `where=` or
`first=` left none, `else=` stands in as for an empty answer, and without it the level shows its
`@else@` branch ([HANDLING.md](HANDLING.md#emptied-by-handling)).

**Example:**
```
{pad:content 'noPremium'}<p>No premium users yet.</p>{/pad:content}
{premiumUsers else="noPremium"}...{/premiumUsers}
```

### notOk

Shows the named content when the tag produced nothing - NULL, FALSE, `''` or an empty array,
or the handling options left no row - taking precedence over `null` and `else`, or when the
tag's handler or PHP threw an exception (a `php:` call, an application's `_tags/` file). A PAD
error is still reported.

```
{type:tagName notOk="contentName"}
```

### error

Alias for `notOk`.

```
{type:tagName error="contentName"}
```

---

## Output Formatting Options

The formatting options - quote, open, close, glue - work on what the `print` option prints,
each row's first field, and run only through it: written without `print` they do nothing.
With `$staff` rows named joe, jim and john:

### quote

Wraps each printed value in the specified quote characters.

```
{type:tagName print, quote="'"}
{type:tagName print, quote='"'}
```

**Result:** `'content'` or `"content"`

**Example:**
```
{local:staff print, quote="'"}  →  'joe''jim''john'
```

### open

Prepends content at the start of the first occurrence only.

```
{type:tagName print, open="prefix"}
```

**Implementation:** Wraps in `{property:first}prefix{/property:first}`

**Example:**
```
{local:staff print, open="["}  →  [joejimjohn
```

### close

Appends content at the end of the last occurrence only.

```
{type:tagName print, close="suffix"}
```

**Implementation:** Wraps in `{property:last}suffix{/property:last}`

**Example:**
```
{local:staff print, close="]"}  →  joejimjohn]
```

### glue

Adds a separator between items (not after the last one).

```
{type:tagName print, glue=", "}
```

**Implementation:** Wraps in `{property:notLast}separator{/property:notLast}`

**Example:**
```
{local:staff print, glue=", "}  →  joe, jim, john
```

### Combined Formatting

Options can be combined for complex formatting:

```
{local:staff print, quote="'", glue=", ", open="[", close="]"}
```

**Result:** `['joe', 'jim', 'john']`

---

## Processing Control Options

### ignore

Wraps content in ignore tags to skip PAD processing.

```
{type:tagName ignore}
```

**Implementation:** Wraps content as `{pad:ignore}content{/pad:ignore}`

**Use case:** Output literal PAD syntax without evaluation.

### tidy

Cleans up whitespace and formatting in the output.

```
{type:tagName tidy}
```

**Implementation:** Calls `padTidy($padContent, TRUE)`

**Example:**
```
{include:template tidy}  →  Cleaned output with normalized whitespace
```

### noError

The same as `optional`: a tag whose name resolves to no tag renders nothing instead of an error.

```
{type:tagName noError}
```

**Behavior:** Read by `level/no.php`, the fallback for an unknown tag. A tag that exists and
fails still reports its error.

---

## Callback Options

### callback

Invokes an application callback for custom processing.

```
{type:tagName callback="callbackName"}
```

**Calls:** `APP/_callbacks/callbackName.php`

**Example:**
```
{array:users callback="processUsers"}
```

### before

Processes callback in "before" mode - runs before content generation.

```
{type:tagName before, callback="initData"}
```

**Behavior:**
1. Calls `padCallbackBeforeXxx('init')`
2. Iterates data with `padCallbackBeforeRow()`
3. Calls `padCallbackBeforeXxx('exit')`

---

## Handling Options

The handling options are processed after the tag has answered, in the order written - see
[HANDLING.md](HANDLING.md).

### dedup

Enables deduplication of data.

```
{type:tagName dedup}
```

### where

Keeps the rows for which a quoted PAD expression holds, evaluated per row with the row's
fields first. See [HANDLING.md](HANDLING.md#where).

```
{local:staff where='$salary gt 2500'}
```

### group

Folds the rows into one occurrence per distinct value of a field, each with the value,
`count`, the aggregates `sum`, `avg`, `min` and `max` ask for, and its own rows as `rows`.
See [HANDLING.md](HANDLING.md#group).

```
{array:orders group='customer', sum='total'}
  {$customer}: {$count} orders, {$total}
  {array:rows}{$number}: {$total}{/array:rows}
{/array:orders}
```

### page

Keeps one page of the rows - `page` counted from 1, `rows` per page (default 10). A
`{pad:pager}` after the tag writes the links to the other pages. See
[HANDLING.md](HANDLING.md#page).

```
{array:products page=$pg ?? 1, rows=12}...{/array:products}
{pad:pager 'products'/}
```

### sort

Enables sorting.

```
{type:tagName sort}
```

For tracing a page, see `{pad:trace}` in [TAGS.md](TAGS.md#trace) and `$padInfo = 'trace'`.

---

## Cache Option

### cache

Keeps the tag's rendering in the fragment cache for so many seconds; on a hit the tag's
handler does not run and the stored rendering stands in. See `{pad:cache}` in
[TAGS.md](TAGS.md#cache).

```
{app:expensive_query cache=3600, vary=$country}
  ...
{/app:expensive_query}
```

`ttl=` may say the seconds instead; `vary=` adds what else the rendering depends on.

---

## Debug Options

### dump

Writes a state dump under `DATA/dumps/<app>/<page>/` - nothing appears in the page - once
the tag has answered and again after its handling options.

```
{type:tagName dump}
```

**Calls:** `padDumpToDir()`

**Use case:** Debugging template processing

---

## Print Option

### print

Enables direct output printing with formatting options.

```
{type:tagName print}
```

**Behavior:**
- Outputs `{&firstFieldValue}`
- Applies formatting options: `quote`, `open`, `glue`, `close`

**Example:**
```
{local:staff print, quote="'", glue=", "}  →  'joe', 'jim', 'john'
```

---

## Option Summary by Category

### Data Flow
| Option | Direction | Description |
|--------|-----------|-------------|
| `data` | Input | Get data from source |
| `content` | Input | Get content from store |
| `merge` | Input | Where `content` joins the tag's own: top, bottom, replace |
| `toData` | Output | Store data to variable |
| `toContent` | Output | Store content to variable |
| `toBool` | Output | Store boolean flag |

### Conditional
| Option | Description |
|--------|-------------|
| `bool` | `{pad:if bool='name'}`: the stored flag is the condition |
| `optional` | Suppress not-found errors |
| `demand` | An error when the tag produced nothing |
| `null` | Named content for NULL |
| `else` | Named content for empty/false |
| `notOk` | Named content for any miss, or when the tag's handler or PHP throws |
| `error` | Alias for notOk |

### Formatting
| Option | Description |
|--------|-------------|
| `quote` | Wrap in quotes - with `print` |
| `open` | Prefix on first item - with `print` |
| `close` | Suffix on last item - with `print` |
| `glue` | Separator between items - with `print` |
| `tidy` | Clean whitespace |

### Control
| Option | Description |
|--------|-------------|
| `ignore` | Skip PAD processing |
| `noError` | An unknown tag renders nothing (as `optional`) |
| `callback` | Run application callback |
| `before` | Callback before content |
| `print` | Direct output mode |
| `dump` | A state dump under `DATA/dumps/` |

### Handling
| Option | Description |
|--------|-------------|
| `where` | Keep the rows an expression holds for |
| `group` | One occurrence per value of a field, with count, rows and aggregates |
| `sum`, `avg`, `min`, `max` | The aggregates of `group` |
| `dedup` | Deduplicate data |
| `page` | Enable pagination |
| `sort` | Enable sorting |

---

## Application-Specific Options

Applications can define custom options by placing PHP files in:

```
APP/_options/optionName.php        start phase - works on the template
APP/_options/end/optionName.php    end phase   - works on the rendered result
```

`_options/optionName.php` is processed during the `app` phase, before the tag renders:
`$padContent` is the template between the tags. `_options/end/optionName.php` is processed
as the level closes, after every occurrence has rendered: `$padContent` is the result, with
the fields filled in - the place for options such as `{array:report minify}` or `{app:price highlight}`.
An option may have both files; each runs in its own phase.

Both have access to:
- `$padContent` - the template (start) or the rendered result (end); change it to change it
- `$padGetName` - Option parameter value (TRUE for the bare form)
- All global PAD variables

The end-phase handlers run before the built-in end options (`toBool`, `toContent`, `toData`,
`tidy`), so those store or tidy what the application option made, and before the closing
tag's pipe. Both are looked up from the page's directory up to the application root, and
count as readers for the strict unread-option check.

```php
<?php                                   // _options/end/words.php
  $padContent .= '(' . str_word_count ( strip_tags ( $padContent ) ) . ' words)';
?>
```

`{local:staff words}{$name} {/local:staff}` → `joe jim john jack jerry (5 words)`

---

## Processing Order

1. **Data retrieval**: `data`
2. **Tag processing**: the tag's handler runs
3. **Flags**: `notOk` / `error`, `null`, `else`, `demand`
4. **Dump and content**: `dump`, then `content` merged
5. **Handling**: `sort`, `where`, `group`, `first`, `page`, `dedup`, ... in the order written (`dump` again after them)
6. **App options**: Custom application options (`_options/`)
7. **Start options**: `ignore`, `print`
8. **Callback**: `callback` (with `before`)
9. **Occurrences**: the content renders per row
10. **App end options**: Custom application options (`_options/end/`)
11. **End options**: `toBool`, `toContent`, `toData`, `tidy`
