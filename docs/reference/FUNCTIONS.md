# PAD Functions Reference

This document provides a complete reference for all PAD pipe functions.

## Usage

Functions are used in PAD expressions via the pipe operator:

```
{$variable | functionName}
{$variable | functionName('parameter')}
{$variable | functionName('param1', 'param2')}
```

Multiple functions can be chained:

```
{$name | trim | upper | left(10)}
```

---

## String Extraction (Delimiter-Based)

Functions that extract parts of a string based on a delimiter.

| Function | Parameters | Description |
|----------|------------|-------------|
| `after` | delimiter | Returns everything after the first occurrence of delimiter |
| `afterLast` | delimiter | Returns everything after the last occurrence of delimiter |
| `before` | delimiter | Returns everything before the first occurrence of delimiter |
| `beforeLast` | delimiter | Returns everything before the last occurrence of delimiter |

### Examples

```
{'hello/world/test' | after('/')}       → 'world/test'
{'hello/world/test' | afterLast('/')}   → 'test'
{'hello/world/test' | before('/')}      → 'hello'
{'hello/world/test' | beforeLast('/')}  → 'hello/world'
```

---

## Substring Operations

Functions that extract substrings by position.

| Function | Parameters | Description |
|----------|------------|-------------|
| `substr` | start [, length] | PHP `substr()` - extract substring from position |
| `left` | count | Returns the first N characters |
| `right` | count | Returns the last N characters |
| `mid` | start, length | Returns substring starting at position (1-based index) |

### Examples

```
{'Hello World' | left(5)}        → 'Hello'
{'Hello World' | right(5)}       → 'World'
{'Hello World' | mid(7, 5)}      → 'World'
{'Hello World' | substr(6)}      → 'World'
{'Hello World' | substr(0, 5)}   → 'Hello'
```

---

## Case Conversion

Functions that change the case of text.

| Function | Parameters | Description |
|----------|------------|-------------|
| `upper` | - | Converts to uppercase (PHP `strtoupper`) |
| `lower` | - | Converts to lowercase (PHP `strtolower`) |
| `capitalize` | - | Capitalizes first letter of each word (PHP `ucwords`) |
| `ucwords` | - | Alias for `capitalize` |

### Examples

```
{'hello world' | upper}       → 'HELLO WORLD'
{'HELLO WORLD' | lower}       → 'hello world'
{'hello world' | capitalize}  → 'Hello World'
```

---

## String Manipulation

Functions that modify string content.

| Function | Parameters | Description |
|----------|------------|-------------|
| `trim` | - | Removes whitespace from both ends (PHP `trim`) |
| `replace` | search, replace | Replaces all occurrences of search with replace |
| `cut` | text | Removes all occurrences of text (replace with empty) |
| `white` | - | Normalizes whitespace - collapses multiple spaces to single space |

### Examples

```
{'  hello  ' | trim}                   → 'hello'
{'hello world' | replace('world', 'there')}  → 'hello there'
{'hello world' | cut('o')}             → 'hell wrld'
{'hello   world' | white}              → 'hello world'
```

---

## HTML & Encoding

Functions for encoding and escaping text for various contexts.

| Function | Parameters | Description |
|----------|------------|-------------|
| `html` | - | Escapes HTML special characters (PHP `htmlspecialchars`) |
| `sanitize` | - | Full special character sanitization (FILTER_SANITIZE_FULL_SPECIAL_CHARS) |
| `url` | - | URL-encodes the value (PHP `urlencode`) |
| `slashes` | - | Adds backslashes before quotes (PHP `addslashes`) |
| `stripslashes` | - | Removes backslashes (PHP `stripslashes`) |
| `encodeHigh` | - | Encodes high ASCII characters (>127) |
| `stripLow` | - | Strips low ASCII control characters |

### Examples

```
{'<script>' | html}         → '&lt;script&gt;'
{'hello world' | url}       → 'hello%20world'
{"it's here" | slashes}     → "it\'s here"
```

---

## HTML Formatting

Functions that add HTML formatting to text.

| Function | Parameters | Description |
|----------|------------|-------------|
| `bold` | - | Wraps value in `<b>` tags |
| `nbsp` | - | Replaces spaces with `&nbsp;` |

### Examples

```
{'important' | bold}      → '<b>important</b>'
{'hello world' | nbsp}    → 'hello&nbsp;world'
```

---

## Length Control

Functions that limit or control string length.

| Function | Parameters | Description |
|----------|------------|-------------|
| `max_len` | length | Truncates string to maximum length |

### Examples

```
{'Hello World' | max_len(5)}  → 'Hello'
{'Hi' | max_len(5)}           → 'Hi'
```

---

## Testing & Conditions

Functions that test values and return boolean results (returns `'1'` for true, `''` for false).

| Function | Parameters | Description |
|----------|------------|-------------|
| `contains` | needle | Returns TRUE if value contains the needle string |
| `in` | values... | Returns `'1'` if value is in the list of parameters |
| `like` | pattern | SQL LIKE pattern matching (`%` = any chars, `_` = single char) |
| `between` | min, max | Returns TRUE if value is exclusively between min and max |
| `range` | min, max | Returns TRUE if value is inclusively in range (min <= value <= max) |
| `exists` | - | Returns `'1'` if file exists in APP directory, `'0'` otherwise |

### Examples

```
{'hello world' | contains('world')}  → TRUE
{$status | in('active', 'pending')}  → '1' or ''
{'test.txt' | like('%.txt')}         → '1'
{$age | between(17, 66)}             → TRUE if 17 < age < 66
{$score | range(0, 100)}             → TRUE if 0 <= score <= 100
{'templates/page.php' | exists}      → '1' or '0'
```

### Like Pattern Syntax

The `like` function supports SQL-style wildcards:

| Pattern | Meaning |
|---------|---------|
| `%` | Matches any sequence of characters |
| `_` | Matches any single character |
| `\%` | Literal percent sign |
| `\_` | Literal underscore |
| `\\` | Literal backslash |

```
{'filename.txt' | like('%.txt')}      → '1'
{'test123' | like('test___')}         → '1'
{'100%' | like('%\%')}                → '1'
```

---

## Date & Time

Functions for working with dates and timestamps.

| Function | Parameters | Description |
|----------|------------|-------------|
| `now` | - | Returns current Unix timestamp |
| `date` | [format [, modifier]] | Formats a timestamp using PHP date format |
| `time` | [format [, modifier]] | Alias for `date` |
| `timestamp` | [format [, modifier]] | Alias for `date` |

### Date Parameters

- No parameters: Uses `$padFmtDate` global format
- One parameter: Uses provided format string
- Two parameters: Format + `strtotime` modifier (e.g., '+1 day')

### Examples

```
{now}                              → current timestamp (e.g., 1702483200)
{now | date}                       → formatted with default format
{now | date('Y-m-d')}              → '2024-12-13'
{now | date('Y-m-d', '+1 week')}   → '2024-12-20'
{$timestamp | time('H:i:s')}       → '14:30:00'
```

---

## PAD Template Helpers

Functions for working with PAD template syntax.

| Function | Parameters | Description |
|----------|------------|-------------|
| `open` | - | Wraps value in `{` and `}` to create a PAD tag |
| `close` | - | Wraps value in `{/` and `}` to create a closing PAD tag |
| `tag` | - | Alias for `open` |
| `optional` | - | Returns value or empty string if null (null coalescing) |

### Examples

```
{'myTag' | open}      → '{myTag}'
{'myTag' | close}     → '{/myTag}'
{$maybeNull | optional}  → value or ''
```

---

## Function Summary by Category

### String Extraction
`after`, `afterLast`, `before`, `beforeLast`

### Substring
`substr`, `left`, `right`, `mid`

### Case
`upper`, `lower`, `capitalize`, `ucwords`

### Manipulation
`trim`, `replace`, `cut`, `white`

### Encoding
`html`, `sanitize`, `url`, `slashes`, `stripslashes`, `encodeHigh`, `stripLow`

### HTML
`bold`, `nbsp`

### Length
`max_len`

### Testing
`contains`, `in`, `like`, `between`, `range`, `exists`

### Date/Time
`now`, `date`, `time`, `timestamp`

### PAD Helpers
`open`, `close`, `tag`, `optional`

---

## Available Variables in Functions

Each function file receives these variables:

| Variable | Description |
|----------|-------------|
| `$value` | The input value being piped |
| `$parm` | Array of parameters passed to the function |
| `$count` | Number of parameters in `$parm` |

---

## Creating Custom Functions

To create a custom function, add a PHP file to the `functions/` directory:

```php
<?php
// functions/myfunction.php

// $value contains the piped input
// $parm contains parameters array
// $count contains parameter count

return strtoupper($value) . $parm[0];

?>
```

Usage:
```
{'hello' | myfunction(' WORLD')}  → 'HELLO WORLD'
```
