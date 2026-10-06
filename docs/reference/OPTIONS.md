# PAD Options Reference

This document provides a complete reference for all PAD tag options.

## Usage

Options are specified on PAD tags, separated by commas:

```
{tagName option="value", anotherOption="value"}
```

A space does not separate two options: `{staff sort="name" first=1}` gives `sort` the value
`"name" first=1`, and `first` is never read. Only an option directly after a single parameter
may leave its comma out - `{data 'x' ignore}`. Multiple options can be combined:

```
{staff print, toContent="list", quote="'", glue=", ", open="[", close="]"}
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
| **Special** | Handled at specific points | `bool` (read by `{if}`), `optional`, `noError` (an unknown tag), `cache` |

---

## Data Source Options

Options that specify where to get data from.

### data

Retrieves data from a named source.

```
{tagName data="sourceName"}
```

**Resolution order:**
1. `$padDataStore[$sourceName]` - Data store
2. `$pqStore[$sourceName]` - Sequence store
3. `padData($sourceName)` - Load from data provider

**Example:**
```
{users data="cachedUsers"}
{list data="queryResults"}
```

### content

Retrieves content from the content store.

```
{tagName content="contentName"}
```

**Returns:** Content from `$padContentStore[$contentName]`

**Example:**
```
{div content="savedHeader"}
```

---

## Data Storage Options

Options that store results for later use.

### toData

Stores the processed data array to a named variable.

```
{tagName toData="variableName"}
```

**Stores to:** `$padDataStore[$variableName]`

**Behavior:**
- If no content/pair and data exists, stores the data array directly
- Otherwise stores the walked data after processing
- Clears the result output (silent storage)

**Example:**
```
{users where='$active eq 1', toData="activeUsers"}
{pad data="activeUsers"}...{/pad}
```

### toContent

Stores the generated content/output to a named variable.

```
{tagName toContent="variableName"}
```

**Stores to:** `$padContentStore[$variableName]`

**Behavior:**
- Stores `$padResult[$pad]` to the content store
- Clears the result output (silent storage)

**Example:**
```
{header toContent="pageHeader"}
{div content="pageHeader"}
```

### toBool

Stores a boolean result based on the output state.

```
{tagName toBool="flagName"}
```

**Stores to:** `$padBoolStore[$flagName]`

**Boolean logic:**
- `TRUE` if result has non-empty trimmed content
- `FALSE` if null, else condition, or empty result

**Example:**
```
{users where='$admin eq 1', toBool="hasAdmins"}
{if bool="hasAdmins"}...{/if}
```

---

## Boolean & Conditional Options

### bool

Makes the stored boolean flag the condition of an `{if}`.

```
{if bool="flagName"}
```

**Behavior:**
- If flag exists in `$padBoolStore`, returns its value
- If not, the flag is `FALSE`, as an unset flag reads everywhere
- Read by `{if}` only; on any other tag it does nothing

**Example:**
```
{checkPermission toBool="canEdit"}
{if bool="canEdit"}Edit{/if}
```

### optional

Marks a tag as optional - suppresses "not found" errors.

```
{$maybeUndefined | optional}
{tagName optional}
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
{tagName demand}
```

**Behavior:**
- A tag that answers NULL, FALSE, `''` or an empty array - after `notOk`, `null` and `else`
  had their turn - is a PAD error: "Tag 'x' carries demand and produced nothing"

---

## Conditional Content Options

Options that show other content based on what the tag itself answered. The value of each is
the **name** of that content - a `{content 'name'}` block, or else an `_include/` snippet, a
page or a tag of that name (`get/content.php`) - never literal text: `else="Nothing here"`
shows nothing.

### null

Shows the named content when the tag answers NULL.

```
{tagName null="contentName"}
```

**Triggers when:**
- Result is `NULL`
- Result is `INF`
- Result is `NaN`

**Behavior:**
- Resets state and replaces content
- Clears null/else flags, sets hit to TRUE

**Example:**
```
{content 'noAvatar'}<img src="anonymous.png" alt="">{/content}
{avatar null="noAvatar"}...{/avatar}
```

### else

Shows the named content when the tag answers an empty or false result.

```
{tagName else="contentName"}
```

**Triggers when:**
- Result is empty array `[]`
- Result is `FALSE`
- Result is empty string `''`

What the tag answered decides, before the handling options run: rows that `where=` or
`first=` take away leave the level to its `@else@` branch instead
([HANDLING.md](HANDLING.md#emptied-by-handling)).

**Example:**
```
{content 'noPremium'}<p>No premium users yet.</p>{/content}
{premiumUsers else="noPremium"}...{/premiumUsers}
```

### notOk

Shows the named content when the tag produced nothing - NULL, FALSE, `''` or an empty array,
taking precedence over `null` and `else` - or when a built-in tag's handler threw a PHP
exception, as a `php:` call can. A PAD error, and an exception from an application's `_tags/`
file, are still reported.

```
{tagName notOk="contentName"}
```

### error

Alias for `notOk`.

```
{tagName error="contentName"}
```

---

## Output Formatting Options

The formatting options - quote, open, close, glue - work on what the `print` option prints,
each row's first field, and run only through it: written without `print` they do nothing.
With `$staff` rows named joe, jim and john:

### quote

Wraps each printed value in the specified quote characters.

```
{tagName print, quote="'"}
{tagName print, quote='"'}
```

**Result:** `'content'` or `"content"`

**Example:**
```
{staff print, quote="'"}  →  'joe''jim''john'
```

### open

Prepends content at the start of the first occurrence only.

```
{tagName print, open="prefix"}
```

**Implementation:** Wraps in `{first}prefix{/first}`

**Example:**
```
{staff print, open="["}  →  [joejimjohn
```

### close

Appends content at the end of the last occurrence only.

```
{tagName print, close="suffix"}
```

**Implementation:** Wraps in `{last}suffix{/last}`

**Example:**
```
{staff print, close="]"}  →  joejimjohn]
```

### glue

Adds a separator between items (not after the last one).

```
{tagName print, glue=", "}
```

**Implementation:** Wraps in `{notLast}separator{/notLast}`

**Example:**
```
{staff print, glue=", "}  →  joe, jim, john
```

### Combined Formatting

Options can be combined for complex formatting:

```
{staff print, quote="'", glue=", ", open="[", close="]"}
```

**Result:** `['joe', 'jim', 'john']`

---

## Processing Control Options

### ignore

Wraps content in ignore tags to skip PAD processing.

```
{tagName ignore}
```

**Implementation:** Wraps content as `{ignore}content{/ignore}`

**Use case:** Output literal PAD syntax without evaluation.

### tidy

Cleans up whitespace and formatting in the output.

```
{tagName tidy}
```

**Implementation:** Calls `padTidy($padContent, TRUE)`

**Example:**
```
{template tidy}  →  Cleaned output with normalized whitespace
```

### noError

The same as `optional`: a tag whose name resolves to no tag renders nothing instead of an error.

```
{tagName noError}
```

**Behavior:** Read by `level/no.php`, the fallback for an unknown tag. A tag that exists and
fails still reports its error.

---

## Callback Options

### callback

Invokes an application callback for custom processing.

```
{tagName callback="callbackName"}
```

**Calls:** `APP/_callbacks/callbackName.php`

**Example:**
```
{users callback="processUsers"}
```

### before

Processes callback in "before" mode - runs before content generation.

```
{tagName before, callback="initData"}
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
{tagName dedup}
```

### where

Keeps the rows for which a quoted PAD expression holds, evaluated per row with the row's
fields first. See [HANDLING.md](HANDLING.md#where).

```
{staff where='$salary gt 2500'}
```

### group

Folds the rows into one occurrence per distinct value of a field, each with the value,
`count`, the aggregates `sum`, `avg`, `min` and `max` ask for, and its own rows as `rows`.
See [HANDLING.md](HANDLING.md#group).

```
{orders group='customer', sum='total'}
  {$customer}: {$count} orders, {$total}
  {rows}{$number}: {$total}{/rows}
{/orders}
```

### page

Keeps one page of the rows - `page` counted from 1, `rows` per page (default 10). A
`{pager}` after the tag writes the links to the other pages. See
[HANDLING.md](HANDLING.md#page).

```
{products page=$pg ?? 1, rows=12}...{/products}
{pager 'products'}
```

### sort

Enables sorting.

```
{tagName sort}
```

For tracing a page, see `{trace}` in [TAGS.md](TAGS.md#trace) and `$padInfo = 'trace'`.

---

## Cache Option

### cache

Keeps the tag's rendering in the fragment cache for so many seconds; on a hit the tag's
handler does not run and the stored rendering stands in. See `{cache}` in
[TAGS.md](TAGS.md#cache).

```
{expensive_query cache=3600, vary=$country}
  ...
{/expensive_query}
```

`ttl=` may say the seconds instead; `vary=` adds what else the rendering depends on.

---

## Debug Options

### dump

Writes a state dump under `DATA/dumps/<app>/<page>/` - nothing appears in the page - once
the tag has answered and again after its handling options.

```
{tagName dump}
```

**Calls:** `padDumpToDir()`

**Use case:** Debugging template processing

---

## Print Option

### print

Enables direct output printing with formatting options.

```
{tagName print}
```

**Behavior:**
- Outputs `{&firstFieldValue}`
- Applies formatting options: `quote`, `open`, `glue`, `close`

**Example:**
```
{staff print, quote="'", glue=", "}  →  'joe', 'jim', 'john'
```

---

## Option Summary by Category

### Data Flow
| Option | Direction | Description |
|--------|-----------|-------------|
| `data` | Input | Get data from source |
| `content` | Input | Get content from store |
| `toData` | Output | Store data to variable |
| `toContent` | Output | Store content to variable |
| `toBool` | Output | Store boolean flag |

### Conditional
| Option | Description |
|--------|-------------|
| `bool` | `{if bool='name'}`: the stored flag is the condition |
| `optional` | Suppress not-found errors |
| `demand` | An error when the tag produced nothing |
| `null` | Named content for NULL |
| `else` | Named content for empty/false |
| `notOk` | Named content for any miss, or a thrown `php:` call |
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
the fields filled in - the place for options such as `{report minify}` or `{price highlight}`.
An option may have both files; each runs in its own phase.

Both have access to:
- `$padContent` - the template (start) or the rendered result (end); change it to change it
- `$padGetName` - Option parameter value (TRUE for the bare form)
- All global PAD variables

The end-phase handlers run before the built-in end options (`toContent`, `toData`, `tidy`,
`dump`), so those store or tidy what the application option made, and before the closing
tag's pipe. Both are looked up from the page's directory up to the application root, and
count as readers for the strict unread-option check.

```php
<?php                                   // _options/end/words.php
  $padContent .= '(' . str_word_count ( strip_tags ( $padContent ) ) . ' words)';
?>
```

`{staff words}{$name} {/staff}` → `joe jim john jack jerry (5 words)`

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
