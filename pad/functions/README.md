# PAD Functions Directory

## Overview

The `functions/` directory contains built-in utility functions for the PAD framework. These functions provide string manipulation, formatting, date/time operations, validation, and other common operations that can be used within PAD templates and expressions.

## Purpose in PAD Framework

This module serves as the core function library for PAD, providing reusable, composable functions that can be chained together in expressions. These functions are typically invoked using the pipe operator in PAD templates to transform and manipulate data.

## Files

### String Manipulation
- **after.php** - Extract substring after a delimiter
- **afterLast.php** - Extract substring after the last occurrence of a delimiter
- **before.php** - Extract substring before a delimiter
- **beforeLast.php** - Extract substring before the last occurrence of a delimiter
- **between.php** - Check if a value is between two values
- **capitalize.php** - Capitalize first letter of each word (ucwords)
- **cut.php** - Cut/truncate a string
- **left.php** - Get leftmost characters
- **right.php** - Get rightmost characters
- **mid.php** - Get middle substring
- **replace.php** - Replace string occurrences
- **substr.php** - Extract substring (supports 1 or 2 parameters)
- **trim.php** - Remove whitespace from both ends
- **lower.php** - Convert to lowercase
- **upper.php** - Convert to uppercase
- **ucwords.php** - Uppercase the first character of each word

### Encoding & Sanitization
- **encodeHigh.php** - Encode high ASCII characters
- **stripLow.php** - Strip low ASCII characters
- **html.php** - HTML encode/escape
- **url.php** - URL encode
- **tag.php** - HTML tag operations
- **sanitize.php** - Sanitize input
- **slashes.php** - Add slashes
- **stripslashes.php** - Remove slashes

### Formatting
- **bold.php** - Make text bold (likely HTML formatting)
- **nbsp.php** - Convert spaces to non-breaking spaces
- **white.php** - Whitespace handling
- **max_len.php** - Enforce maximum length

### Date & Time
- **date.php** - Format dates with optional format string and time parsing
- **time.php** - Time operations
- **timestamp.php** - Timestamp operations
- **now.php** - Get current timestamp

### Validation & Testing
- **contains.php** - Check if string contains substring
- **exists.php** - Check if value exists
- **in.php** - Check if value is in array/list
- **like.php** - SQL-like pattern matching with wildcards (%, _)
- **range.php** - Range validation

### File Operations
- **open.php** - Open file/resource
- **close.php** - Close file/resource

### Other Utilities
- **optional.php** - Handle optional values
- **dumpxxx.php** - Debug dump functionality

## Key Features

1. **Chainable Functions**: All functions accept a value and return a transformed result, enabling function chaining
2. **Parameter Support**: Functions accept parameters via the `$parm` array and `$count` variable
3. **Type Flexibility**: Functions handle both scalar values and arrays where appropriate
4. **Pattern Matching**: Advanced pattern matching with `like.php` supporting SQL-style wildcards
5. **Date Flexibility**: Date functions support custom formats and relative time parsing
6. **Encoding Safety**: Multiple encoding options for HTML, URL, and character set safety

## Architecture Integration

The functions in this directory are:
- **Loaded dynamically** by the PAD core when referenced in templates
- **Executed in expression context** with access to `$value`, `$parm`, and `$count` variables
- **Composable** through the PAD pipe operator for complex transformations
- **Isolated** - each function is a single-purpose, stateless transformation
- **Framework-agnostic** - can be used in any PAD template or construct

These functions form the functional programming foundation of PAD, enabling declarative data transformation without complex procedural code.
