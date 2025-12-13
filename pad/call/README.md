# Call Module

This module provides a unified system for including and executing PHP files with consistent error handling, output buffering, and execution control. It supports various inclusion modes (once, output-buffered, no-one) and integrates with PAD's try/catch system.

## Overview

The call module standardizes how PAD includes external PHP files by:
- Providing output buffering for file inclusion
- Supporting "include once" semantics
- Handling files that may not exist gracefully
- Integrating with PAD's error handling system
- Capturing both return values and echoed output
- Supporting event tracking for debugging

## Directory Structure

### Core Files
- **_call.php** - Main file inclusion with try/catch wrapper
- **_init.php** - Initializes variables and starts output buffering
- **_exit.php** - Captures output buffer and cleans up
- **_try.php** - Include wrapper for error handling
- **_tryOnce.php** - Include-once wrapper for error handling
- **_return.php** - Returns value from included file
- **_once.php** - Include-once implementation

### Public Entry Points
- **any.php** - Include file with return value
- **ob.php** - Include file and return output buffer
- **once.php** - Include file once and return value
- **noOne.php** - Include file if exists, empty string if not
- **obNoOne.php** - Include file with output buffering if exists

## Key Components

### _init.php
Prepares the environment for file inclusion.

**Actions:**
1. Initializes `$padCallPHP` (return value) to empty string
2. Initializes `$padCallOB` (output buffer) to empty string
3. Triggers `events/call.php` if `$padInfo` is enabled
4. Starts output buffering with `ob_start()`

**Global Variables Set:**
- `$padCallPHP` - Will store return value from included file
- `$padCallOB` - Will store buffered output from included file

### _exit.php
Cleans up after file inclusion.

**Actions:**
1. Captures output buffer to `$padCallOB`
2. Ends output buffering with `ob_end_clean()`

### _call.php
Main inclusion wrapper with error handling.

**Process:**
1. Includes `_init.php` to prepare environment
2. Checks if file exists using `file_exists($padCall)`
3. If exists:
   - Triggers `events/callStart.php` event
   - Sets `$padTry = 'call/_try'`
   - Includes file via `try/try.php` error handler
   - Stores return value in `$padCallPHP`
   - Triggers `events/callEnd.php` event
4. Includes `_exit.php` to capture output

**Required Variable:**
- `$padCall` - Path to file to include (must be set before including _call.php)

### _try.php
Wrapper that performs the actual include statement.

**Returns:** Result of including the file specified in `$padCall`

### _tryOnce.php
Wrapper that performs include-once statement.

**Returns:** Result of including the file once

### _once.php
Include-once implementation.

**Process:**
1. Includes `_init.php`
2. Checks if file exists
3. If exists:
   - Triggers start event
   - Sets `$padTry = 'call/_tryOnce'`
   - Includes via try/try.php with include_once
   - Triggers end event
4. Includes `_exit.php`

### _return.php
Returns the value from included file.

**Returns:** `$padCallPHP` (the return value from the included file)

## Public Entry Points

### any.php
Includes file and returns its return value.

**Usage:**
```php
$padCall = '/path/to/file.php';
$result = include PAD . 'call/any.php';
```

**Process:**
1. Includes `_call.php` to execute file
2. Returns `$padCallPHP` via `_return.php`

### ob.php
Includes file and returns captured output.

**Usage:**
```php
$padCall = '/path/to/file.php';
$output = include PAD . 'call/ob.php';
```

**Process:**
1. Includes `_call.php` to execute file
2. Returns `$padCallOB` (buffered output)

### once.php
Includes file once and returns its return value.

**Usage:**
```php
$padCall = '/path/to/library.php';
$result = include PAD . 'call/once.php';
```

**Process:**
1. Includes `_once.php` to execute file with include_once
2. Returns `$padCallPHP` via `_return.php`

**Use Case:** Including library files that should only be loaded once

### noOne.php
Includes file if it exists, returns empty string if not.

**Usage:**
```php
$padCall = '/path/to/optional-file.php';
$result = include PAD . 'call/noOne.php';
```

**Process:**
1. Includes `_call.php` (which checks file_exists)
2. Returns `$padCallPHP` or empty string if file doesn't exist

**Use Case:** Optional initialization files like `_inits.php`

### obNoOne.php
Includes file with output buffering if exists, empty string if not.

**Usage:**
```php
$padCall = '/path/to/template.php';
$output = include PAD . 'call/obNoOne.php';
```

**Process:**
1. Includes `_call.php` to execute with buffering
2. Returns `$padCallOB` or empty string if file doesn't exist

**Use Case:** Optional template files that echo content

## Usage Examples

### Example 1: Include Library File Once
```php
$padCall = PAD . 'helpers/utilities.php';
include PAD . 'call/once.php';
// File is included only once, even if called multiple times
```

### Example 2: Capture Return Value
```php
$padCall = APP . 'data/products.php';
$products = include PAD . 'call/any.php';
// $products contains the return value from products.php
```

### Example 3: Capture Output
```php
$padCall = APP . 'templates/header.php';
$headerHtml = include PAD . 'call/ob.php';
// $headerHtml contains all echoed content from header.php
```

### Example 4: Optional Configuration
```php
$padCall = APP . '_config.php';
include PAD . 'call/noOne.php';
// No error if _config.php doesn't exist
```

### Example 5: Optional Template with Output
```php
$padCall = APP . '_inits.php';
$initOutput = include PAD . 'call/obNoOne.php';
// Returns empty string if file doesn't exist
```

## Integration with PAD Framework

The call module is used throughout PAD for:

- **Build System**: Including library files and initialization scripts
- **Template System**: Loading page PHP files and templates
- **Plugin System**: Loading optional plugin files
- **Configuration**: Including optional config files
- **Callbacks**: Executing callback functions
- **Error Handling**: Wrapping includes in try/catch via `try/try.php`

## Error Handling

All inclusions go through PAD's try/catch system:

1. File existence is checked before inclusion
2. Actual include happens in `_try.php` or `_tryOnce.php`
3. These are included via `try/try.php` which provides error handling
4. Errors are caught and can be logged/handled by PAD's error system

## Event System Integration

If `$padInfo` is enabled, the call module triggers events:

- **events/call.php** - Before any inclusion attempt
- **events/callStart.php** - Just before including file
- **events/callEnd.php** - Just after file inclusion completes

These events enable:
- Debugging and logging
- Performance monitoring
- Call stack tracking
- Profiling

## Return Values vs Output

The call module distinguishes between two types of content:

### Return Values (`$padCallPHP`)
- Value returned by `return` statement in included file
- Captured by all call variants
- Used for data passing (arrays, objects, primitives)

### Output (`$padCallOB`)
- Content echoed or printed by included file
- Captured via output buffering
- Used for template rendering and HTML generation
- Only returned by `ob.php` and `obNoOne.php`

## Performance Considerations

- Output buffering has minimal overhead
- File existence checks prevent unnecessary error handling
- Include-once prevents duplicate loading of libraries
- Event system can be disabled by setting `$padInfo = FALSE`
- All file operations are unbuffered for immediate execution

## Variable Scope

Files included through the call module have access to:
- All global variables
- Variables in the calling scope
- Can modify globals using `global` keyword
- Can return values back to caller

## Best Practices

1. **Use once.php for libraries**: Prevents redefinition errors
2. **Use noOne.php for optional files**: Graceful handling of missing files
3. **Use ob.php for templates**: Capture HTML output
4. **Use any.php for data files**: Get return values
5. **Set $padCall before inclusion**: Always define the path first
6. **Check return values**: Handle both TRUE/FALSE and actual data returns
