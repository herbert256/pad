# PAD Tags Reference

This document describes all built-in PAD tags and their functionality.

---

## Control Flow Tags

### `{if condition}...{/if}`
Conditional rendering. Evaluates the condition and renders content if true.
- Supports `{elseif condition}` for chained conditions
- Supports `{else}` for fallback content
- Condition is evaluated using `padEval()`

```html
{if $age >= 18}Adult{elseif $age >= 13}Teenager{else}Child{/if}
```

### `{while condition}...{/while}`
Loop while condition is true. Re-evaluates condition each iteration.

```html
{while $i < 10}{$i}{increment $i}{/while}
```

### `{until condition}...{/until}`
Loop until condition becomes true (inverse of while). Uses same logic as `{while}`.

```html
{until $found}...{/until}
```

### `{case expression}{when value1}...{when value2}...{/case}`
Switch/case construct. Evaluates expression and matches against `{when}` clauses.

```html
{case $status}{when 'active'}Active{when 'pending'}Pending{when 'closed'}Closed{/case}
```

### `{switch name value1 value2 value3}`
Cycles through values on each occurrence. Useful for alternating styles.

```html
<tr class="{switch row odd even}">
```

### `{check query}`
Database condition check. Returns true/false based on query result.

```html
{if {check user id=$id}}User exists{/if}
```

---

## Data Tags

### `{data name}...{/data}`
Store data for later iteration or access. Creates entry in `$padDataStore`.
- Can load from various formats with `type` parameter
- Iterates over data when used as paired tag

```html
{data users type=json}[{"name":"John"},{"name":"Jane"}]{/data}
{data users}{$name}{/data}
```

### `{content name}...{/content}`
Store content (rendered HTML) for later use. Creates entry in `$padContentStore`.

```html
{content header}<h1>Welcome</h1>{/content}
{header}
```

### `{bool name}...{/bool}`
Store a boolean flag. Creates entry in `$padBoolStore`.

```html
{bool isAdmin}{$role = 'admin'}{/bool}
```

### `{array expression}`
Iterate over an array. Alias for `{record}`.

```html
{array $items}{$value}{/array}
```

### `{record expression}`
Iterate over array/data. Executes database-style query if string.

```html
{record users where active=1}{$name}{/record}
```

### `{field expression}`
Access a field value. Alias for `{record}`.

---

## Variable Tags

### `{set name=value ...}`
Set global variables. Cannot be used as paired tag.

```html
{set total=0 count=0}
```

### `{increment varname}`
Increment a numeric variable by 1. Initializes to 1 if not set.

```html
{increment counter}
```

### `{decrement varname}`
Decrement a numeric variable by 1. Initializes to -1 if not set.

```html
{decrement remaining}
```

### `{count name}`
Check if named data/array has items. Returns true if count > 0.

```html
{if {count users}}Has users{/if}
```

---

## Output Tags

### `{echo expression}`
Evaluate and output an expression.

```html
{echo $firstname . ' ' . $lastname}
```

### `{dump}`
Trigger a debug dump. Calls `padDump()` for debugging.

### `{tidy}...{/tidy}`
Apply HTML tidy to content. Cleans and formats HTML.

```html
{tidy}<div><p>Messy   HTML</p></div>{/tidy}
```

### `{output type}`
Change output type (web, file, console, download).

```html
{output file}
```

---

## File & Directory Tags

### `{file}...{/file}`
Write content to a file. Parameters control filename generation.
- `dir` - Directory path
- `name` - Filename
- `ext` - Extension
- `date` - Add date to filename
- `stamp` - Add timestamp
- `id` - Add unique ID

```html
{file dir=/output name=report ext=html}{content}{/file}
```

### `{files dir options}`
Iterate over files in a directory. Returns array of file info.
- `dir` - Directory to scan
- `mask` - Filename pattern (fnmatch)
- `onlyFiles` - Only return files
- `onlyDirs` - Only return directories
- `recursive` - Scan subdirectories
- `exclude` - Exclusion pattern
- `includeHidden` - Include dot files
- `base` - Base path (app, data, pad)
- `group` - Group by item name

```html
{files dir=images mask=*.jpg onlyFiles}{$file}{/files}
```

### `{dir path}`
Simple directory listing. Returns array from `scandir()`.

```html
{dir /var/log}{$value}{/dir}
```

### `{exists path}`
Check if file/directory exists. Returns boolean.

```html
{if {exists /path/to/file}}File found{/if}
```

---

## HTTP & External Tags

### `{curl url options}`
Make HTTP request. Returns response data.
- `url` - Target URL (supports `SELF://` for current host)
- Additional parameters added as query string

```html
{curl https://api.example.com/data}
```

### `{ajax}`
Handle AJAX request processing.

### `{redirect}`
Perform HTTP redirect.

### `{get}`
Process GET request parameters.

### `{page}`
Include/process another page.

---

## Flow Control Tags

### `{exit}`
Stop processing and exit. Calls `padExit()`.

### `{restart page variables}`
Restart processing with a different page/variables.

```html
{restart login error='Invalid credentials'}
```

### `{continue}`
Continue to next iteration (in sequence context).

### `{ignore}...{/ignore}`
Escape content - prevents PAD tag processing inside.

```html
{ignore}{this is not processed}{/ignore}
```

---

## Error Handling Tags

### `{error message}`
Trigger an error with the given message.

```html
{error 'Required field missing'}
```

### `{exception message}`
Throw a PHP exception with the given message.

```html
{exception 'Critical failure'}
```

---

## Constant Tags

### `{true}`
Returns boolean TRUE. Useful in conditionals.

### `{false}`
Returns boolean FALSE.

### `{null}`
Returns NULL value.

### `{open}`
Returns `&open;` entity (escaped opening brace).

### `{close}`
Returns `&close;` entity (escaped closing brace).

---

## Debugging Tags

### `{trace}...{/trace}`
Enable tracing for enclosed content. Activates detailed execution logging.

```html
{trace}{complex processing}{/trace}
```

### `{dump}`
Trigger debug dump output.

---

## Sequence Tags

### `{sequence}`
Work with mathematical sequences (delegates to sequence system).

### `{action}`
Execute a sequence action.

### `{flag}`
Boolean flag in sequence context.

### `{keep}`
Keep/preserve sequence data.

### `{pull}`
Pull data from sequence store.

### `{remove}`
Remove from sequence store.

### `{make}`
Create/build sequence data.

---

## Special Tags

### `{pad}`
PAD marker tag. Returns TRUE. Used for template structure.

### `{foo}`
Test/demo tag. Returns "Foo tag from pad".

### `{code}...{/code}`
Execute as code block. Clears content and processes as code.

### `{sandbox}...{/sandbox}`
Execute content in sandbox mode. Isolates processing.

---

## Tag Behavior Summary

| Tag | Paired | Returns | Purpose |
|-----|--------|---------|---------|
| `if` | Yes | TRUE/FALSE | Conditional |
| `while` | Yes | Array/NULL | Loop |
| `until` | Yes | Array/NULL | Loop (inverse) |
| `case` | Yes | TRUE/FALSE | Switch |
| `switch` | No | String | Cycle values |
| `data` | Yes | NULL | Store data |
| `content` | Yes | NULL | Store content |
| `bool` | Yes | NULL | Store boolean |
| `array` | Yes | Array | Iterate |
| `record` | Yes | Array | Query/iterate |
| `set` | No | TRUE | Set variables |
| `increment` | No | TRUE | Increment var |
| `decrement` | No | TRUE | Decrement var |
| `echo` | No | Value | Output |
| `file` | Yes | TRUE | Write file |
| `files` | Yes | Array | List files |
| `curl` | No | String | HTTP request |
| `exit` | No | - | Stop |
| `error` | No | - | Raise error |
| `true` | No | TRUE | Constant |
| `false` | No | FALSE | Constant |
| `null` | No | NULL | Constant |
| `tidy` | Yes | TRUE | Format HTML |
| `trace` | Yes | TRUE | Debug |
| `ignore` | Yes | TRUE | Escape |

---

## See Also

- `functions/` - Transformation functions for pipe expressions
- `types/` - Type handlers for tag resolution
- `options/` - Tag option modifiers
- `eval/` - Expression evaluation system
