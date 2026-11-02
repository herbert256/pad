# PAD Functions Reference

This document provides a complete reference for all PAD pipe functions.

## Usage

Pipe functions transform values using the `|` operator. A field tag pipes as it stands;
a literal or an expression goes through `{pad:echo}`:

```
{$name | upper}                   # a field tag pipes
{pad:echo $name | upper/}              # the same value, printed raw - {pad:echo/} skips the sanitize chain
{pad:echo $text | trim | lower/}       # Chained functions
{pad:echo $date | date('Y-m-d')/}      # With parameters
{pad:echo 'hello' | upper/}            # a literal needs {pad:echo/}
```

Multiple functions can be chained:

```
{pad:echo $name | trim | upper | left(10)/}
```

Where nothing is piped in - at the head of an expression, or as an argument of another
call - a function written as a call reads its first argument as the value, as PHP's
functions do; `date` keeps PHP's order, the format first and then the moment:

```
{pad:if left($code, 2) eq 'NL'} ... {/pad:if}
{pad:echo 'Hello ' . upper($name)/}
{pad:echo $text | replace('x', upper($y))/}
{pad:echo date('Y', $created)/}
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
{pad:echo 'hello/world/test' | after('/')/}  → 'world/test'
{pad:echo 'hello/world/test' | afterLast('/')/} → 'test'
{pad:echo 'hello/world/test' | before('/')/} → 'hello'
{pad:echo 'hello/world/test' | beforeLast('/')/} → 'hello/world'
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
| `substr` | start [, length] | `mb_substr()` - extract substring from a 0-based position, counted in characters |
| `left` | count | Returns the first N characters |
| `right` | count | Returns the last N characters |
| `mid` | start, length | Returns substring starting at position (1-based index) |

### Examples

```
{pad:echo 'Hello World' | left(5)/}   → 'Hello'
{pad:echo 'Hello World' | right(5)/}  → 'World'
{pad:echo 'Hello World' | mid(7, 5)/} → 'World'
{pad:echo 'Hello World' | substr(6)/} → 'World'
{pad:echo 'Hello World' | substr(0, 5)/} → 'Hello'
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
| `capitalize` | - | Capitalizes first letter of each word, in UTF-8 (`éric` → `Éric`) - PHP's `ucwords` knows ASCII only |
| `ucwords` | - | Alias for `capitalize` |

### Examples

```
{pad:echo 'hello world' | upper/}  → 'HELLO WORLD'
{pad:echo 'HELLO WORLD' | lower/}  → 'hello world'
{pad:echo 'hello world' | capitalize/} → 'Hello World'
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
{pad:echo '  hello  ' | trim/}              → 'hello'
{pad:echo 'hello world' | replace('world', 'there')/} → 'hello there'
{pad:echo 'hello world' | cut('o')/}        → 'hell wrld'
{pad:echo 'hello   world' | white/}         → 'hello world'
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
| `escape` | strategy | Escapes for a context: `html` (default), `attr`, `js`, `css`, `url` - see below |
| `slug` | separator | A readable URL part: `'Crème Brûlée & Co.'` → `'creme-brulee-co'` |
| `ignore` | - | Escapes PAD syntax characters (`{ } \| = , @`) so the output is not parsed as PAD tags - not HTML escaping: in an attribute use `html` first |

### Examples

```
{pad:echo '<script>' | html/}    → '&lt;script&gt;'
{pad:echo 'hello world' | url/}  → 'hello+world'
{pad:echo "it's here" | slashes/} → "it\'s here"
{app:json 'products' | ignore/}  → an application's own _tags/json.php (CLAUDE.md): the JSON it HTML-escaped itself, kept from the PAD parser
{pad:echo $json | html | ignore/} → any value: html makes it safe inside an attribute, ignore keeps PAD off it
{pad:echo 'Crème Brûlée & Co.' | slug/} → 'creme-brulee-co'
```

### escape

`escape(strategy)` writes the value for the context it lands in:

| Strategy | For | Example output for `it's </b>` |
|----------|-----|-----|
| `html` (default) | text and quoted attributes | `it&#039;s &lt;/b&gt;` |
| `attr` | any attribute, even unquoted | `it&#x27;s&#x20;&#x3C;&#x2F;b&#x3E;` |
| `js` | inside a JavaScript string | `it\x27s\x20\x3C\x2Fb\x3E` |
| `css` | a CSS value | `it\27 s\20 \3C \2F b\3E ` |
| `url` | one path or query part (`rawurlencode`) | `it%27s%20%3C%2Fb%3E` |

```
<script>var name = "{$name | escape('js')}";</script>
```

The sanitize chain a `{$field}` ends with never encodes an entity twice, and the `js`, `css`
and `url` forms leave nothing for it to change, so the two do not stack.

---

## HTML Formatting

Functions that add HTML formatting to text.

| Function | Parameters | Description |
|----------|------------|-------------|
| `bold` | - | Wraps value in `<b>` tags |
| `nbsp` | - | Replaces spaces with `&nbsp;` |
| `markdown` | - | Reads the value as Markdown and writes it as HTML - raw HTML escaped, `javascript:` links dropped |

### Examples

```
{pad:echo 'important' | bold/} → '<b>important</b>'
{pad:echo 'hello world' | nbsp/} → 'hello&nbsp;world'
{pad:echo '**Hi** <b>' | markdown/} → '<p><strong>Hi</strong> &lt;b&gt;</p>'
```

### markdown

`{$post.body | markdown}` shows a post kept in a database. The HTML is safe by construction - the
raw HTML of the value is escaped, a link whose URL names a scheme other than http, https,
mailto, ftp or tel keeps only its text - so a field whose **last** pipe is `markdown` skips the
sanitize chain, which would otherwise escape the markup just made. The same subset as the
`{pad:markdown}` tag (TAGS.md); the tag's `html` option, which lets raw HTML through, is for the
template's own text and has no pipe form. A value stays text: `{php:getcwd}` in a post is shown.

### highlight

`{$query | highlight('sql')}` shows a value as source code, coloured - `pad`, `php`, `html`,
`css`, `js`, `json`, `yaml`, `sql`, `bash` or `text`. Every piece of the value is escaped, so a
field whose **last** pipe is `highlight` skips the sanitize chain, as one ending in `markdown`
does. The same block as the `{pad:highlight}` tag (TAGS.md), without its `lines` and `mark`.

---

## Length Control

Functions that limit or control string length.

| Function | Parameters | Description |
|----------|------------|-------------|
| `max_len` | length | Truncates string to maximum length |
| `truncate` | length, ellipsis | Shortens to at most length characters, ellipsis (default `…`) included, ending on a whole word |
| `bytes` | precision | A byte count for people: `1536` → `1.5 KB` (units of 1024, default 2 decimals) |

### Examples

```
{pad:echo 'Hello World' | max_len(5)/} → 'Hello'
{pad:echo 'Hi' | max_len(5)/}      → 'Hi'
{pad:echo 'The quick brown fox jumps over the lazy dog' | truncate(20)/} → 'The quick brown fox…'
{pad:echo 3221225472 | bytes/}     → '3 GB'
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
{pad:echo 'hello world' | contains('world')/} → TRUE
{$status | in('active', 'pending')}  → '1' or ''
{pad:echo 'test.txt' | like('%.txt')/}    → '1'
{$age | between(17, 66)}             → TRUE if 17 < age < 66
{$score | range(0, 100)}             → TRUE if 0 <= score <= 100
{pad:echo 'templates/page.php' | exists/} → '1' or '0'
{$code | matches('/^[A-Z][A-Z][0-9]+$/')} → '1' or ''
```

The tests read infix in a condition as well - the value on the left is their input:

```
{pad:if $status in ('active', 'pending')} ... {/pad:if}
{pad:if $email matches '/@example\\.com$/'} ... {/pad:if}
{pad:if $name like 'A%'} ... {/pad:if}
{pad:if $age range (18, 65)} ... {/pad:if}
```

A `not` between the value and the test denies it, as SQL writes it:

```
{array:orders where='$status not in ("cancelled", "refunded")'} ... {/array:orders}
{pad:if $name not like 'A%'} ... {/pad:if}
```

### default

`default(fallback)` answers the value, or the fallback when it is empty - `''`, NULL, FALSE
or an empty list (0 is a value). A field piped into it may be missing under the strict check:

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
{pad:echo 'filename.txt' | like('%.txt')/} → '1'
{pad:echo 'test123' | like('test___')/}    → '1'
{pad:echo '100%' | like('%\\%')/}          → '1'
```

---

## Locale

The request's locale is `$padLocale`; `$padLocales` lists the ones the application speaks,
and with any listed, `?lang=` (kept in the `padLang` cookie) or `Accept-Language` picks among
them. `$padTimezone` sets the timezone. Formatting uses PHP's intl extension when it is there.

| Function | Parameters | Description |
|----------|------------|-------------|
| `trans` | count | The value as a key of the `_lang/` catalogs, translated; the count picks the plural form |
| `currency` | code, locale | An amount as money the locale's way - code defaults to EUR |
| `localDate` | date, time, locale | A date the locale's way: styles `none`, `short`, `medium` (default), `long`, `full`, or an ICU pattern as the first argument; an empty style is the default (`medium` date, `none` time) |

### Examples

```
{pad:echo 'cart.title' | trans/}                  → 'Je winkelwagen' in nl
{pad:echo 'cart.items' | trans($n)/}              → '3 artikelen'
{$price | currency('USD', 'en_US')}          → '$1,234.50'
{$created | localDate('long')}               → 'October 5, 2026'
{$created | localDate('d MMMM y', 'none', 'nl')} → '5 oktober 2026'
```

---

## Lookup

| Function | Parameters | Description |
|----------|------------|-------------|
| `lookup` | set, key, field | The field of the row in another data set whose key equals the value - a join between lists from different sources |

The set is named as a data tag names its rows: a `{pad:data}` block, a stored sequence, an array
of the page or of an enclosing row, or a `_data/` file. `key` defaults to `id`; without a
`field` the answer is the row's first field other than the key. The first matching row
counts; no match answers `''`, so `| ?? 'fallback'` supplies one. The set is indexed on the
key once per request - not once per row - and indexed again only when the set itself
changed. Strict mode names a lookup without a set, or with a name nothing holds.

### Examples

```
{array:orders}
  {$number}: {pad:echo $customer_id | lookup('customers', 'id', 'name')/}
{/array:orders}

{$customer_id | lookup('customers')}                          → the first field after id
{$code | lookup('countries', 'code', 'label') | ?? 'unknown'} → a miss with a fallback
{$id | lookup('customers', 'id', 'country') | lookup('countries', 'code', 'label')}
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

The value is a Unix timestamp, a date in text or a date object - a `DateTime` the page's PHP
left (`date`, `localDate` and `ago` take it, as `{time:relative}` and `{time:countdown}` do). A date object
no pipe turned into text is written `Y-m-d H:i:s`: `{$created}`.

### Examples

```
{function:now}                              → current timestamp (e.g., 1702483200)
{function:now | date}                       → formatted with default format
{function:now | date('Y-m-d')}              → '2024-12-13'
{function:now | date('Y-m-d', '+1 week')}   → '2024-12-20'
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
| `+ n` | Add | `{pad:echo $value \| + 1/}` |
| `- n` | Subtract | `{pad:echo $value \| - 5/}` |
| `* n` | Multiply | `{pad:echo $value \| * 2/}` |
| `/ n` | Divide | `{pad:echo $value \| / 4/}` |

```
{pad:echo $value | + 1/}          # Correct - adds 1
{pad:echo $value | * 2/}          # Correct - multiplies by 2
{pad:echo $value | +1/}           # WRONG - no space!
```

---

## Printf-style Format

Use printf format specifiers for number formatting:

```
{pad:echo $nbr | %.5f/}          # 5 decimal places: 3.14159
{pad:echo $nbr | %'.09d/}        # Zero-padded to 9 digits: 000000123
{pad:echo $nbr | %d/}            # Integer: 42
{pad:echo $nbr | %x/}            # Hexadecimal: 2a
{pad:echo $nbr | %05d/}          # Zero-padded to 5 digits: 00042
{pad:echo $nbr | %+d/}           # With sign: +42
```

A format is one pipe segment like any other, anywhere in the chain - it runs to the next
`|`: `{pad:echo $price | * 1.21 | %.2f | . ' EUR'/}`. A `%` that opens no conversion is the
modulo operator: `{pad:echo $n | % 3/}`.

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
{pad:echo 50 | @ * 4/}                # 200 (@ = 50)
{pad:echo $text | '"' . @ . '"'/}     # Wrap value in quotes
{pad:echo $num | @ + @ * 2/}          # Triple the value
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
{pad:echo 'myTag' | open/} → '{app:myTag}'
{pad:echo 'myTag' | close/} → '{/app:myTag}'
{$maybeNull | optional}  → value or ''
```

---

## Helper Pipes

The template side of some of the PHP helpers ([HELPERS.md](HELPERS.md)).

### abbreviate

`abbreviate(precision)` writes a large number short for people - `padNumberAbbreviate` in a
template. K, M, B, T and Q stand for thousand up to quadrillion; the precision (0 unless
given) is the most decimals written, the trailing zeros dropped, and the unit is chosen on the
number as it will be written, so 999999 is `1M`. Below 1000 the number has no letter, a
negative one keeps its sign, and a value that is not a number goes through unchanged. A
negative precision is reported.

```
{$visits | abbreviate}        → '2M'     ($visits = 1534200)
{$visits | abbreviate(1)}     → '1.5M'
{$visits | abbreviate(2)}     → '1.53M'
{pad:echo 950 | abbreviate/}       → '950'
{pad:echo -2500 | abbreviate(1)/}  → '-2.5K'
```

### ordinal

`ordinal` writes a whole number as its English place - `padNumberOrdinal` in a template: st,
nd and rd after 1, 2 and 3, th after the rest and after 11, 12 and 13, in 111 to 113 too. A
number with a fraction is reported; a value that is not a number goes through unchanged.

```
{array:runners}{$place | ordinal}: {$name} {/array:runners}   → '1st: Ann 2nd: Bob 3rd: Cee '
{pad:echo 22 | ordinal/}           → '22nd'
{pad:echo 112 | ordinal/}          → '112th'
```


<!-- pipes: dates -->

### ago

`ago(from)` says how long ago a moment was, in words - the `padAgo` helper
([HELPERS.md](HELPERS.md#dates-and-logging)) in a template:

| Function | Parameters | Description |
|----------|------------|-------------|
| `ago` | [from] | The age of a timestamp or date in words, counted from now - or from `from` |

```
{$created | ago}                 → 'just now', '5 minutes ago', 'yesterday', '3 weeks ago'
{$deadline | ago}                → 'in 2 hours', 'tomorrow', 'in 3 days'
{$start | ago($end)}             → counted from $end instead of now
{pad:echo '2026-10-04' | ago/}        → 'yesterday' on 5 October 2026
```

The value is a Unix timestamp, a date in text or a date object; now is the one `padNowFreeze` fixed, when a
page froze the clock. Under ten seconds is `just now`, then seconds, minutes and hours; from a
day on the calendar counts - `yesterday`, days, weeks, months, years. An empty value answers
empty; a value that is no date answers empty too, and the strict check names it.


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
`html`, `sanitize`, `url`, `escape`, `slug`, `slashes`, `stripslashes`, `encodeHigh`, `stripLow`, `ignore`

### HTML
`bold`, `nbsp`, `markdown`

### Numbers
`abbreviate`, `ordinal`

### Length
`max_len`, `truncate`, `bytes`

### Testing
`contains`, `in`, `like`, `matches`, `between`, `range`, `exists`

### Values
`default`

### Lookup
`lookup`

### Locale
`trans`, `currency`, `localDate`

### Date/Time
`now`, `date`, `time`, `timestamp`, `ago`

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
{array:items | trim}
  <li>x</li>
{/array:items}
```
It is not a way to reorder or filter what a tag iterates - a field written inside would be
transformed with everything else and then not resolve. Sorting is an option:
```
{array:items sort}
  <li>{$name}</li>
{/array:items}
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
{pad:echo $price | money/}     # $1,234.56
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
{pad:echo $name | initials/}   # "John Doe" → "JD"
```

### Available Variables

Each function file receives these variables:

| Variable | Description |
|----------|-------------|
| `$padContent` | The input value being piped |
| `$parm` | Array of parameters passed to the function |
| `$count` | Number of parameters in `$parm` |
