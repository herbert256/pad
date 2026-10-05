# PAD Functions Reference

This document provides a complete reference for all PAD pipe functions.

## Usage

Pipe functions transform values using the `|` operator. A field tag pipes as it stands;
a literal or an expression goes through `{echo}`:

```
{$name | upper}                   # a field tag pipes
{echo $name | upper}              # the same through {echo}
{echo $text | trim | lower}       # Chained functions
{echo $date | date('Y-m-d')}      # With parameters
{echo 'hello' | upper}            # a literal needs {echo}
```

Multiple functions can be chained:

```
{echo $name | trim | upper | left(10)}
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
{echo 'hello/world/test' | after('/')}  → 'world/test'
{echo 'hello/world/test' | afterLast('/')} → 'test'
{echo 'hello/world/test' | before('/')} → 'hello'
{echo 'hello/world/test' | beforeLast('/')} → 'hello/world'
```

All four skip the whole delimiter, multi-character or not, and give the value back unchanged
when it does not contain the delimiter. An empty delimiter is found everywhere: first at the
start - `after('')` is the whole value, `before('')` is empty - and last at the end -
`afterLast('')` is empty, `beforeLast('')` is the whole value.

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
{echo 'Hello World' | left(5)}   → 'Hello'
{echo 'Hello World' | right(5)}  → 'World'
{echo 'Hello World' | mid(7, 5)} → 'World'
{echo 'Hello World' | substr(6)} → 'World'
{echo 'Hello World' | substr(0, 5)} → 'Hello'
```

A count of zero or less names no characters: `left(0)`, `right(0)` and the negative counts
all answer the empty string. `mid` reads a start below 1 as 1 (its documented base), a
negative or zero length as no characters, and no length at all as the rest of the value.
`substr` alone keeps PHP's negative-offset semantics - use it when counting from the end is
what is meant.

---

## Case Conversion

Functions that change the case of text.

| Function | Parameters | Description |
|----------|------------|-------------|
| `upper` | - | Converts to uppercase, letter by letter in UTF-8 (`café` → `CAFÉ`) |
| `lower` | - | Converts to lowercase, letter by letter in UTF-8 (`ÉCOLE` → `école`) |
| `capitalize` | - | Capitalizes first letter of each word (PHP `ucwords`) |
| `ucwords` | - | Alias for `capitalize` |

### Examples

```
{echo 'hello world' | upper}  → 'HELLO WORLD'
{echo 'HELLO WORLD' | lower}  → 'hello world'
{echo 'hello world' | capitalize} → 'Hello World'
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
{echo '  hello  ' | trim}              → 'hello'
{echo 'hello world' | replace('world', 'there')} → 'hello there'
{echo 'hello world' | cut('o')}        → 'hell wrld'
{echo 'hello   world' | white}         → 'hello world'
```

---

## HTML & Encoding

Functions for encoding and escaping text for various contexts.

| Function | Parameters | Description |
|----------|------------|-------------|
| `html` | - | Escapes HTML special characters (PHP `htmlspecialchars`) |
| `sanitize` | - | Full special character sanitization (FILTER_SANITIZE_FULL_SPECIAL_CHARS) |
| `url` | - | URL-encodes the value (PHP `urlencode`) - a space becomes `+` |
| `slashes` | - | Adds backslashes before quotes (PHP `addslashes`) - not SQL escaping: give values to `db()` as placeholders |
| `stripslashes` | - | Removes backslashes (PHP `stripslashes`) |
| `encodeHigh` | - | Encodes high ASCII characters (>127) |
| `stripLow` | - | Strips low ASCII control characters |
| `ignore` | - | Escapes PAD syntax characters (`{ } \| = , @`) so the output is not parsed as PAD tags - not HTML escaping: in an attribute use `html` first |

### Examples

```
{echo '<script>' | html}    → '&lt;script&gt;'
{echo 'hello world' | url}  → 'hello+world'
{"it's here" | slashes}     → "it\'s here"
{json 'products' | ignore}  → the tag's JSON, which it HTML-escaped itself, kept from the PAD parser
{echo $json | html | ignore} → any value: html makes it safe inside an attribute, ignore keeps PAD off it
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
{echo 'important' | bold} → '<b>important</b>'
{echo 'hello world' | nbsp} → 'hello&nbsp;world'
```

---

## Length Control

Functions that limit or control string length.

| Function | Parameters | Description |
|----------|------------|-------------|
| `max_len` | length | Truncates string to maximum length |

### Examples

```
{echo 'Hello World' | max_len(5)} → 'Hello'
{echo 'Hi' | max_len(5)}      → 'Hi'
```

---

## Testing & Conditions

Functions that test values and return boolean results (returns `'1'` for true, `''` for
false). The one exception is `exists`, which answers `'0'` rather than `''` for a missing
file.

| Function | Parameters | Description |
|----------|------------|-------------|
| `contains` | needle | Returns TRUE if value contains the needle string |
| `in` | values... | Returns `'1'` if value is in the list of parameters |
| `matches` | regex | Regular expression test, `'1'` or `''`; a backslash in a PAD string is written doubled - `'/x\\.y/'` - and a `{n}` quantifier would open a tag, so repeat the class or pass the pattern from PHP |
| `like` | pattern | SQL LIKE pattern matching (`%` = any chars, `_` = single character, counted as UTF-8 characters rather than bytes) |
| `between` | min, max | Returns TRUE if value is exclusively between min and max |
| `range` | min, max | Returns TRUE if value is inclusively in range (min <= value <= max) |
| `exists` | - | Returns `'1'` if file exists in APP directory, `'0'` otherwise |

### Examples

```
{echo 'hello world' | contains('world')} → TRUE
{$status | in('active', 'pending')}  → '1' or ''
{echo 'test.txt' | like('%.txt')}    → '1'
{$age | between(17, 66)}             → TRUE if 17 < age < 66
{$score | range(0, 100)}             → TRUE if 0 <= score <= 100
{echo 'templates/page.php' | exists} → '1' or '0'
{$code | matches('/^[A-Z][A-Z][0-9]+$/')} → '1' or ''
```

The tests read infix in a condition as well - the value on the left is their input:

```
{if $status in ('active', 'pending')} ... {/if}
{if $email matches '/@example\\.com$/'} ... {/if}
{if $name like 'A%'} ... {/if}
{if $age range (18, 65)} ... {/if}
```

### default

`default(fallback)` answers the value, or the fallback when it is empty - `''`, NULL or an
empty list (0 is a value). A field piped into it may be missing under the strict check:

```
{$title | default('Untitled')}
```

It is the function form of the `??` operator: `{$title | ?? 'Untitled'}`.

### Like Pattern Syntax

The `like` function supports SQL-style wildcards:

| Pattern | Meaning |
|---------|---------|
| `%` | Matches any sequence of characters |
| `_` | Matches any single character |
| `\%` | Literal percent sign |
| `\_` | Literal underscore |
| `\\` | Literal backslash |

Inside a quoted pattern in a template the backslash is itself escaped, so a literal percent
sign is written `\\%` there - `like('%\\%')` - while a pattern that comes from the page's
PHP, `like($pattern)`, is written as is.

```
{echo 'filename.txt' | like('%.txt')} → '1'
{echo 'test123' | like('test___')}    → '1'
{echo '100%' | like('%\\%')}          → '1'
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

### Date Format Characters

| Char | Description | Example |
|------|-------------|---------|
| `Y` | 4-digit year | 2025 |
| `m` | Month (01-12) | 03 |
| `d` | Day (01-31) | 15 |
| `H` | Hour (00-23) | 14 |
| `i` | Minutes (00-59) | 30 |
| `s` | Seconds (00-59) | 45 |
| `D` | Day name (short) | Mon |
| `l` | Day name (full) | Monday |
| `M` | Month name (short) | Mar |
| `F` | Month name (full) | March |

---

## Arithmetic

**Important:** Arithmetic pipes require a space between the operator and operand!

| Function | Description | Example |
|----------|-------------|---------|
| `+ n` | Add | `{echo $value \| + 1}` |
| `- n` | Subtract | `{echo $value \| - 5}` |
| `* n` | Multiply | `{echo $value \| * 2}` |
| `/ n` | Divide | `{echo $value \| / 4}` |

```
{echo $value | + 1}          # Correct - adds 1
{echo $value | * 2}          # Correct - multiplies by 2
{echo $value | +1}           # WRONG - no space!
```

---

## Printf-style Format

Use printf format specifiers for number formatting:

```
{echo $nbr | %.5f}          # 5 decimal places: 3.14159
{echo $nbr | %'.09d}        # Zero-padded to 9 digits: 000000123
{echo $nbr | %d}            # Integer: 42
{echo $nbr | %x}            # Hexadecimal: 2a
{echo $nbr | %05d}          # Zero-padded to 5 digits: 00042
{echo $nbr | %+d}           # With sign: +42
```

### Common Format Specifiers

| Specifier | Description |
|-----------|-------------|
| `%d` | Integer |
| `%f` | Float |
| `%.Nf` | Float with N decimals |
| `%s` | String |
| `%x` | Hexadecimal (lowercase) |
| `%X` | Hexadecimal (uppercase) |
| `%0Nd` | Zero-padded integer |
| `%+d` | Signed integer |

---

## The @ Placeholder

The `@` symbol represents the current value in expressions:

```
{echo 50 | @ * 4}                # 200 (@ = 50)
{echo $text | '"' . @ . '"'}     # Wrap value in quotes
{echo $num | @ + @ * 2}          # Triple the value
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
{echo 'myTag' | open} → '{myTag}'
{echo 'myTag' | close} → '{/myTag}'
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
`html`, `sanitize`, `url`, `slashes`, `stripslashes`, `encodeHigh`, `stripLow`, `ignore`

### HTML
`bold`, `nbsp`

### Length
`max_len`

### Testing
`contains`, `in`, `like`, `between`, `range`, `exists`

### Date/Time
`now`, `date`, `time`, `timestamp`

### Arithmetic
`+`, `-`, `*`, `/`

### Printf Format
`%d`, `%f`, `%.Nf`, `%s`, `%x`, `%X`, `%0Nd`, `%+d`

### PAD Helpers
`open`, `close`, `tag`, `optional`

---

## Pipe Timing: Opening vs Closing Tags

Pipes can be applied to a tag at two points, and they do not act on the same text.

### Opening Tag Pipe

Transforms the *content template*, once, before any occurrence is rendered. What the
function is handed is the source between the tags, not the tag's data:
```
{items | trim}
  <li>x</li>
{/items}
```
It is not a way to reorder or filter what a tag iterates - a field written inside would be
transformed with everything else and then not resolve. Sorting is an option:
```
{items sort}
  <li>{$name}</li>
{/items}
```

### Closing Tag Pipe

Transforms the output, after every occurrence has been rendered and joined:
```
{message}
  Content: {$message}
{/message | upper}
```
Closing pipes chain left to right, each over what the one before returned.

---

## Creating Custom Functions

Create custom functions in `_functions/`:

**_functions/money.php:**
```php
<?php
  return '$' . number_format($padContent, 2);
?>
```

Use in templates:
```
{echo $price | money}     # $1,234.56
```

**_functions/initials.php:**
```php
<?php
  $words = explode(' ', $padContent);
  $initials = '';
  foreach ($words as $word) {
    $initials .= strtoupper($word[0]);
  }
  return $initials;
?>
```

Use in templates:
```
{echo $name | initials}   # "John Doe" → "JD"
```

### Available Variables

Each function file receives these variables:

| Variable | Description |
|----------|-------------|
| `$padContent` | The input value being piped |
| `$parm` | Array of parameters passed to the function |
| `$count` | Number of parameters in `$parm` |
