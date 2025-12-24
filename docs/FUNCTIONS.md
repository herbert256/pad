# FUNCTIONS.md - PAD Pipe Functions Reference

This file documents all built-in PAD pipe functions for transforming output.

---

## Overview

Pipe functions transform values using the `|` operator. They **always require `{echo}`**:

```
{echo $name | upper}              # Correct - uppercase
{echo $text | trim | lower}       # Chained functions
{echo $date | date('Y-m-d')}      # With parameters

{$name | upper}                   # WRONG - bare expression won't work!
```

---

## String Extraction (Delimiter-Based)

| Function | Description | Example |
|----------|-------------|---------|
| `after(d)` | After first delimiter | `{'a/b/c' \| after('/')}` → `b/c` |
| `afterLast(d)` | After last delimiter | `{'a/b/c' \| afterLast('/')}` → `c` |
| `before(d)` | Before first delimiter | `{'a/b/c' \| before('/')}` → `a` |
| `beforeLast(d)` | Before last delimiter | `{'a/b/c' \| beforeLast('/')}` → `a/b` |

### Example: Extract Domain from Email

```
{echo $email | after('@') | before('.')}
```

---

## Substring Operations

| Function | Description | Example |
|----------|-------------|---------|
| `left(n)` | First N chars | `{'Hello' \| left(3)}` → `Hel` |
| `right(n)` | Last N chars | `{'Hello' \| right(3)}` → `llo` |
| `substr(s,l)` | Substring (0-based) | `{'Hello' \| substr(1,3)}` → `ell` |
| `mid(s,l)` | Middle (1-based) | `{'Hello' \| mid(2,3)}` → `ell` |
| `max_len(n)` | Truncate to max | `{'Hello World' \| max_len(5)}` → `Hello` |

---

## Case Conversion

| Function | Description | Example |
|----------|-------------|---------|
| `upper` | Uppercase | `{'hello' \| upper}` → `HELLO` |
| `lower` | Lowercase | `{'HELLO' \| lower}` → `hello` |
| `capitalize` | Capitalize words | `{'hello world' \| capitalize}` → `Hello World` |
| `ucwords` | Alias for capitalize | |

---

## String Manipulation

| Function | Description | Example |
|----------|-------------|---------|
| `trim` | Remove whitespace | `{'  hello  ' \| trim}` → `hello` |
| `replace(s,r)` | Replace text | `{'hello' \| replace('l','x')}` → `hexxo` |
| `cut(text)` | Remove text | `{'hello world' \| cut('world')}` → `hello ` |
| `white` | Normalize whitespace | Collapses multiple spaces |

---

## HTML & Encoding

| Function | Description | Example |
|----------|-------------|---------|
| `html` | HTML encode | `{'<b>' \| html}` → `&lt;b&gt;` |
| `sanitize` | Full special char sanitization | |
| `url` | URL encode | `{'a b' \| url}` → `a%20b` |
| `slashes` | Add slashes | Escapes quotes |
| `stripslashes` | Remove slashes | |

---

## HTML Formatting

| Function | Description | Example |
|----------|-------------|---------|
| `bold` | Wrap in `<b>` tags | `{'text' \| bold}` → `<b>text</b>` |
| `nbsp` | Replace spaces with `&nbsp;` | `{'a b' \| nbsp}` → `a&nbsp;b` |

---

## Testing & Conditions

| Function | Description | Returns |
|----------|-------------|---------|
| `contains(s)` | Contains substring | `'1'` or `''` |
| `in(vals...)` | In list of values | `'1'` or `''` |
| `like(p)` | SQL LIKE pattern | `'1'` or `''` |
| `between(a,b)` | Exclusively between | TRUE/FALSE |
| `range(a,b)` | Inclusively in range | TRUE/FALSE |
| `exists` | File exists in APP | `'1'` or `'0'` |

### Examples

```
{if {echo $status | in('active','pending')} eq '1'}
  Status is active or pending
{/if}

{if {echo $name | contains('admin')} eq '1'}
  Name contains admin
{/if}
```

---

## Date/Time

| Function | Description | Example |
|----------|-------------|---------|
| `date(fmt)` | Format date | `{echo $ts \| date('Y-m-d')}` |
| `time(fmt)` | Alias for date | `{echo $ts \| time('H:i:s')}` |
| `now` | Current timestamp | `{now}` |

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

## PAD Template Helpers

| Function | Description | Example |
|----------|-------------|---------|
| `open` | Wrap in `{` `}` | `{'if' \| open}` → `{if}` |
| `close` | Wrap in `{/` `}` | `{'if' \| close}` → `{/if}` |
| `tag` | Alias for open | |
| `optional` | Return value or empty | Returns empty if null/false |

---

## The @ Placeholder

The `@` symbol represents the current value in expressions:

```
{echo 50 | @ * 4}                # 200 (@ = 50)
{echo $text | '"' . @ . '"'}     # Wrap value in quotes
{echo $num | @ + @ * 2}          # Triple the value
```

---

## Chaining Functions

Pipe functions can be chained - each function receives the output of the previous:

```
{echo $name | trim | lower | capitalize}
{echo $email | after('@') | before('.') | upper}
{echo $price | * 1.1 | %.2f}     # Add 10% tax, format to 2 decimals
```

---

## Pipe Timing: Opening vs Closing Tags

Pipes can be applied at two different points:

### Opening Tag Pipe

Processes data BEFORE the tag content is rendered:
```
{items | sort}
  <li>{$name}</li>
{/items}
```

### Closing Tag Pipe

Processes output AFTER the tag finishes:
```
{message}
  Content: {$message}
{/message | upper}
```

---

## Custom Pipe Functions

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
