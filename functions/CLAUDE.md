# functions/

Built-in transformation functions used in pipe expressions like `{$value|function}`.

## String Functions
- `upper.php` / `lower.php` - Case conversion
- `capitalize.php` / `ucwords.php` - Capitalization
- `trim.php` - Whitespace trimming
- `left.php` / `right.php` / `mid.php` - Substring extraction
- `substr.php` - PHP substr wrapper
- `replace.php` - String replacement
- `before.php` / `after.php` / `between.php` - String splitting
- `beforeLast.php` / `afterLast.php` - Split on last occurrence

## Encoding/Escaping
- `html.php` - HTML entity encoding
- `url.php` - URL encoding
- `sanitize.php` - XSS sanitization
- `slashes.php` / `stripslashes.php` - Slash handling
- `encodeHigh.php` - High character encoding
- `stripLow.php` - Strip low ASCII

## Formatting
- `date.php` / `time.php` / `timestamp.php` - Date/time formatting
- `bold.php` - Bold wrapper
- `nbsp.php` - Non-breaking space conversion
- `white.php` - Whitespace normalization
- `max_len.php` - Truncate to max length
- `cut.php` - Cut string at position

## Logic/Testing
- `exists.php` - Check if value exists
- `contains.php` - Check if string contains
- `in.php` - Check if value in list
- `like.php` - Pattern matching
- `range.php` - Check if in range
- `optional.php` - Return empty if falsy

## Other
- `now.php` - Current timestamp
- `tag.php` - Tag wrapper
- `open.php` / `close.php` - Opening/closing wrappers
