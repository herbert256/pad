# TAGS.md - PAD Tags Reference

This file documents all built-in PAD template tags.

---

## Control Flow Tags

| Tag | Description | Example |
|-----|-------------|---------|
| `{if}` | Conditional | `{if $x eq 1}yes{/if}` |
| `{elseif}` | Else-if branch | `{elseif $x eq 2}` |
| `{else}` | Else branch | `{else}` |
| `{case}` | Switch-case | `{case $x}{when 'a'}...{/case}` |
| `{when}` | Case match | `{when 'value'}` |
| `{while}` | While loop | `{while $i lt 10}...{/while}` |
| `{until}` | Until loop | `{until $done eq 1}...{/until}` |
| `{switch}` | Rotating switch | `{switch 'name', 'odd', 'even'}` |

### Conditionals

```
{if $count gt 0}
  Has items
{elseif $count eq 0}
  Empty
{else}
  Invalid
{/if}
```

**Important:** Conditionals need comparison operators:
```
{if $count eq 0}Empty{/if}     # Correct
{if $flag}True{/if}            # WRONG - needs comparison
```

### Case/Switch

```
{case $color}
  {when 'red'} Stop
  {when 'yellow'} Caution
  {when 'green'} Go
  {else} Unknown
{/case}
```

### Rotating Switch

Cycles through values on each iteration:
```
{items}
  <tr style="background: {switch '#fff', '#eee'}">
    <td>{$name}</td>
  </tr>
{/items}
```

---

## Variable and Data Tags

| Tag | Description | Example |
|-----|-------------|---------|
| `{set}` | Assign variable | `{set $count = 0}` |
| `{get}` | Include page | `{get 'fragments/nav'}` |
| `{data}` | Define data | `{data 'items'}[1,2,3]{/data}` |
| `{content}` | Store content | `{content 'header'}...{/content}` |
| `{bool}` | Store boolean | `{bool 'flag'}1{/bool}` |
| `{echo}` | Evaluate/output | `{echo $a + $b}` |

### Variable Assignment

```
{set $name = 'Alice'}              # Assign string
{set $count = 0}                   # Assign number
{set $total = $price * $qty}       # Assign expression
```

### Data Definition

Supports JSON, XML, YAML, and CSV formats:
```
{data 'colors'}
  ["red", "green", "blue"]
{/data}

{data 'myXML'}
  <data><row name="bob" phone="123" /></data>
{/data}

{data 'myCSV'}
  name,phone
  bob,123
  alice,456
{/data}
```

---

## Counter Tags

| Tag | Description | Example |
|-----|-------------|---------|
| `{count}` | Check array elements | `{count 'items'}has items{/count}` |
| `{increment}` | Add 1 | `{increment $i}` |
| `{decrement}` | Subtract 1 | `{decrement $i}` |

---

## Loop Control Tags

| Tag | Description | Example |
|-----|-------------|---------|
| `{continue}` | Skip to next iteration | `{continue 'tagname'}` |
| `{cease}` | Soft stop (graceful end) | `{cease 'tagname'}` |
| `{break}` | Hard stop (immediate exit) | `{break 'tagname'}` |

---

## Database Tags

| Tag | Description | Example |
|-----|-------------|---------|
| `{field}` | Query single value | `{field "count(*) from users"}` |
| `{table}` | Query rows | `{table "SELECT * FROM users"}...{/table}` |
| `{array}` | Query array | `{array 'tablename'}` |
| `{record}` | Query record | `{record 'SQL query'}` |

See [DATABASE.md](DATABASE.md) for complete database documentation.

---

## File Tags

| Tag | Description | Example |
|-----|-------------|---------|
| `{files}` | List files | `{files dir='images' mask='*.jpg'}` |
| `{dir}` | Directory listing | `{dir '/path'}` |
| `{file}` | Write file | `{file dir='output' name='report'}...{/file}` |
| `{exists}` | Check file exists | `{exists APP . 'file.pad'}...{/exists}` |

### File Listing Example

```
{files dir='uploads' mask='*.pdf'}
  <a href="{$path}">{$name}</a> ({$size} bytes)
{/files}
```

---

## Output Tags

| Tag | Description | Example |
|-----|-------------|---------|
| `{output}` | Set output type | `{output 'download'}` |
| `{tidy}` | Format HTML | `{tidy}...{/tidy}` |
| `{ignore}` | Escape PAD syntax | `{ignore}{not processed}{/ignore}` |
| `{open}` | Opening brace | `{open}` → `{` |
| `{close}` | Closing brace | `{close}` → `}` |

### Ignore Tag

Prevents PAD from parsing curly braces - essential for JavaScript/CSS:
```html
{ignore}
<script>
  const user = { name: 'Alice' };
</script>
{/ignore}
```

---

## Navigation Tags

| Tag | Description | Example |
|-----|-------------|---------|
| `{redirect}` | HTTP redirect | `{redirect 'url'}` |
| `{restart}` | Restart processing | `{restart 'page'}` |
| `{page}` | Include page | `{page 'pagename'}` |

---

## Execution Tags

| Tag | Description | Example |
|-----|-------------|---------|
| `{code}` | Execute PHP | `{code 'phpfile'}` |
| `{sandbox}` | Sandboxed PHP | `{sandbox 'phpfile'}` |
| `{ajax}` | AJAX handler | `{ajax 'handler'}` |
| `{curl}` | HTTP request | `{curl 'http://example.com'}` |

---

## Debug Tags

| Tag | Description | Example |
|-----|-------------|---------|
| `{dump}` | Debug output | `{dump}` |
| `{trace}` | Enable tracing | `{trace}...{/trace}` |
| `{error}` | Trigger error | `{error 'message'}` |
| `{exception}` | Throw exception | `{exception 'message'}` |
| `{exit}` | Exit processing | `{exit}` |

---

## Boolean/Value Tags

| Tag | Returns |
|-----|---------|
| `{true}` | TRUE |
| `{false}` | FALSE |
| `{null}` | NULL |

---

## Sequence Tags

| Tag | Description |
|-----|-------------|
| `{sequence}` | Generate sequence |
| `{pull}` | Pull stored sequence |
| `{resume}` | Transform stored sequence |
| `{keep}` | Keep matching values |
| `{remove}` | Remove matching values |
| `{make}` | Transform values |
| `{flag}` | Set sequence flag |

See [sequences/](sequences/) for complete sequence subsystem documentation.

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
