# PAD Options Reference

This document provides a complete reference for all PAD tag options.

## Usage

Options are specified as attributes on PAD tags:

```
{tagName option="value" anotherOption="value"}
```

Multiple options can be combined:

```
{data toData="myData" quote="'" glue=", " open="[" close="]"}
```

---

## Processing Phases

Options are processed at different phases during tag execution:

| Phase | When | Options |
|-------|------|---------|
| **Start** | Before content generation | `track`, `before`, `dedup`, `page`, `sort`, `ignore`, `print`, `parent`, `trace`, `pre` |
| **End** | After content generation | `toBool`, `toContent`, `toData`, `tidy`, `dump` |
| **Callback** | During callback execution | `callback` |
| **Special** | Handled at specific points | `data`, `content`, `else`, `null`, `bool`, `optional`, `noError` |

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
{users where="active=1" toData="activeUsers"}
{list data="activeUsers"}...{/list}
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
{users where="admin=1" toBool="hasAdmins"}
{if bool="hasAdmins"}...{/if}
```

---

## Boolean & Conditional Options

### bool

Retrieves or creates a boolean flag value.

```
{if bool="flagName"}
```

**Behavior:**
- If flag exists in `$padBoolStore`, returns its value
- If not exists, creates a new flag via `padMakeFlag()`

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

Marks a tag as required (callback mode).

```
{tagName demand}
```

**Behavior:**
- Returns TRUE (for callback processing)
- Indicates the tag must produce output

---

## Conditional Content Options

Options that provide alternative content based on result state.

### null

Provides alternative content when result is NULL.

```
{tagName null="alternativeContent"}
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
{$user.avatar null="defaultAvatar"}
```

### else

Provides alternative content when result is empty/false.

```
{tagName else="alternativeContent"}
```

**Triggers when:**
- Result is empty array `[]`
- Result is `FALSE`
- Result is empty string `''`

**Example:**
```
{users where="premium=1" else="noPremiumUsers"}
```

### notOk

Provides alternative content on error/failure state.

```
{tagName notOk="errorContent"}
```

**Similar to `else` but for error conditions.**

### error

Alias for `notOk`.

```
{tagName error="errorContent"}
```

---

## Output Formatting Options

### quote

Wraps content with specified quote characters.

```
{tagName quote="'"}
{tagName quote='"'}
```

**Result:** `'content'` or `"content"`

**Example:**
```
{$name quote="'"}  →  'John'
```

### open

Prepends content at the start of the first occurrence only.

```
{tagName open="prefix"}
```

**Implementation:** Wraps in `{first}prefix{/first}`

**Example:**
```
{list open="["}  →  [item1, item2, item3
```

### close

Appends content at the end of the last occurrence only.

```
{tagName close="suffix"}
```

**Implementation:** Wraps in `{last}suffix{/last}`

**Example:**
```
{list close="]"}  →  item1, item2, item3]
```

### glue

Adds a separator between items (not after the last one).

```
{tagName glue=", "}
```

**Implementation:** Wraps in `{notLast}separator{/notLast}`

**Example:**
```
{list glue=", "}  →  item1, item2, item3
```

### Combined Formatting

Options can be combined for complex formatting:

```
{list quote="'" glue=", " open="[" close="]"}
```

**Result:** `['item1', 'item2', 'item3']`

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

Suppresses error handling for the tag.

```
{tagName noError}
```

**Behavior:** Handled in `level/var.php` - prevents error reporting

**Use case:** When errors are expected and should be silently ignored.

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
{tagName before callback="initData"}
```

**Behavior:**
1. Calls `padCallbackBeforeXxx('init')`
2. Iterates data with `padCallbackBeforeRow()`
3. Calls `padCallbackBeforeXxx('exit')`

---

## Start Phase Options

Options processed at the start of tag execution.

### track

Enables tracking for the tag.

```
{tagName track}
```

### dedup

Enables deduplication of data.

```
{tagName dedup}
```

### page

Enables pagination.

```
{tagName page}
```

### sort

Enables sorting.

```
{tagName sort}
```

### parent

Handles parent relationship.

```
{tagName parent}
```

### trace

Enables tracing for debugging.

```
{tagName trace}
```

### pre

Enables preprocessing.

```
{tagName pre}
```

---

## Debug Options

### dump

Outputs debug information about current state.

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
{list print quote="'" glue=", "}
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
| `bool` | Check/create boolean flag |
| `optional` | Suppress not-found errors |
| `demand` | Mark as required |
| `null` | Alternative for NULL |
| `else` | Alternative for empty/false |
| `notOk` | Alternative for error |
| `error` | Alias for notOk |

### Formatting
| Option | Description |
|--------|-------------|
| `quote` | Wrap in quotes |
| `open` | Prefix on first item |
| `close` | Suffix on last item |
| `glue` | Separator between items |
| `tidy` | Clean whitespace |

### Control
| Option | Description |
|--------|-------------|
| `ignore` | Skip PAD processing |
| `noError` | Suppress errors |
| `callback` | Run application callback |
| `before` | Callback before content |
| `print` | Direct output mode |
| `dump` | Debug output |

### Start Phase
| Option | Description |
|--------|-------------|
| `track` | Enable tracking |
| `dedup` | Deduplicate data |
| `page` | Enable pagination |
| `sort` | Enable sorting |
| `parent` | Handle parent |
| `trace` | Enable tracing |
| `pre` | Preprocessing |

---

## Application-Specific Options

Applications can define custom options by placing PHP files in:

```
APP/_options/optionName.php
```

These are processed during the `app` phase and have access to:
- `$padContent` - Current content
- `$padGetName` - Option parameter value
- All global PAD variables

---

## Processing Order

1. **Data retrieval**: `data`, `content`
2. **Conditional setup**: `else`
3. **Start options**: `track`, `before`, `dedup`, `page`, `sort`, `ignore`, `print`, `parent`, `trace`, `pre`
4. **App options**: Custom application options
5. **Content generation**: Tag processing
6. **Flag handling**: `null` (if NULL result)
7. **Callback**: `callback`
8. **End options**: `toBool`, `toContent`, `toData`, `tidy`, `dump`
