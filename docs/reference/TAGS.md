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
  {when 'option2'}Content for option2
  {when 'default'}Default content
{/case}
```

**Parameters:**
- First parameter: Value to match against

**Supports:** Multiple `{when}` clauses

---

### switch
Rotating switch that cycles through options on each call.

```html
{switch 'name', 'odd', 'even'}
```

**Parameters:**
- First parameter: Switch identifier name
- Additional parameters: Values to cycle through

**Returns:** Next value in rotation sequence

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
Set one or more global variables.

```html
{set name='value', count=5, active=TRUE}
```

**Parameters:**
- Named parameters become global variables

**Note:** Cannot be used as open/close tag pair

---

### get
Retrieve content from the content store or include files.

```html
{get 'content_name'}
```

**Parameters:**
- First parameter: Content name to retrieve

**Returns:** Retrieved content

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

### field / array / record
Database record access tags.

```html
{field 'tablename fieldname'}
{array 'tablename'}
{record 'SQL query'}
```

**Parameters:**
- First parameter: SQL query or table/field reference

**Returns:** Database query results

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
Execute PHP code.

```html
{code 'phpfile'}
```

**Parameters:**
- First parameter: PHP file to execute

**Note:** Content is cleared before execution

---

### sandbox
Execute code in isolated sandbox.

```html
{sandbox 'phpfile'}
```

**Parameters:**
- First parameter: PHP file to execute

**Behavior:** Same as code but in sandbox mode

---

### action
Execute a sequence action.

```html
{action 'action_name'}
```

**Parameters:**
- First parameter: Action name

---

### ajax
Handle AJAX request.

```html
{ajax 'handler'}
```

**Parameters:**
- First parameter: AJAX handler name

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

**Behavior:** Makes HTTP request, throws error if result is not 200

**Returns:** Response data

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
- First parameter: Output type (`web`, `console`, `file`, `download`)

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

### ignore
Escape PAD syntax in content.

```html
{ignore}
  {this is not processed}
{/ignore}
```

**Behavior:** Escapes content so PAD tags are not processed

---

## Debugging Tags

### dump
Dump debug information.

```html
{dump}
```

**Behavior:** Calls `padDump()` with message

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

## Summary Table

| Tag | Category | Description |
|-----|----------|-------------|
| `if` | Control Flow | Conditional execution |
| `case` | Control Flow | Switch-case matching |
| `switch` | Control Flow | Rotating value switch |
| `while` | Control Flow | Loop while true |
| `until` | Control Flow | Loop until true |
| `set` | Variables | Set global variables |
| `get` | Variables | Get stored content |
| `data` | Variables | Store/iterate data |
| `content` | Variables | Store content |
| `bool` | Variables | Store boolean |
| `field` | Database | Query field |
| `array` | Database | Query array |
| `record` | Database | Execute SQL |
| `count` | Counters | Check element count |
| `increment` | Counters | Increment variable |
| `decrement` | Counters | Decrement variable |
| `page` | Execution | Include PAD page |
| `code` | Execution | Execute PHP |
| `sandbox` | Execution | Sandboxed PHP |
| `action` | Execution | Execute action |
| `ajax` | Execution | AJAX handler |
| `pad` | Execution | PAD include |
| `redirect` | Navigation | HTTP redirect |
| `restart` | Navigation | Restart processing |
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
| `ignore` | Output | Escape content |
| `dump` | Debug | Dump info |
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

---

## Type Prefixes

Resolve naming conflicts with explicit prefixes:

| Prefix | Purpose | Example |
|--------|---------|---------|
| `app:` | App tag from `_tags/` | `{app:mytag}` |
| `pad:` | Built-in PAD tag | `{pad:if}` |
| `php:` | Call PHP function | `{php:strlen(@)}` |
| `function:` | Custom PAD function | `{$x \| function:myfunc}` |
| `data:` | Defined data block | `{data:items}` |
| `content:` | Content block | `{content:header}` |
| `pull:` | Stored sequence | `{pull:mySeq}` |
| `field:` | Database field | `{field:"name from users"}` |
| `table:` | Database table | `{table:users}` |
| `local:` | Files from `_data/` | `{local:menu.json}` |
| `constant:` | PHP constant | `{constant:PHP_VERSION}` |
| `bool:` | Boolean store | `{bool:isAdmin}` |
| `array:` | Access array as loop | `{array:items}` |
| `action:` | Sequence action | `{action:reverse}` |

---

## Options Reference

Options modify tag behavior. Add them to any tag.

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
| `cache` | Cache output |
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
| `parameter:x@tag` | Named parameter |
| `parameters@tag` | All parameters |
| `option:n@tag` | Positional option |
| `options@tag` | All options |
| `variable:x@tag` | Level variable |
| `variables@tag` | All variables |

### Properties Example

```
{items}
  {if first@items}<ul>{/if}
  <li class="{even@items ? even : odd}">{$name}</li>
  {if last@items}</ul>{/if}
  Index: {current@items} of {count@items}
{/items}
```
