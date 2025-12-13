# PAD Framework - Try/Catch Error Handling

## Overview

The `try` module provides PHP try/catch exception handling for the PAD framework. It wraps execution in try/catch blocks to gracefully handle runtime errors and exceptions, allowing the framework to catch, log, and recover from errors rather than terminating abruptly.

## Purpose

This module enables robust error handling by:

- Catching PHP Throwable exceptions during execution
- Providing graceful error recovery mechanisms
- Logging caught exceptions with file and line information
- Allowing execution to continue after handling errors
- Supporting conditional error handling based on framework settings

## Directory Structure

### Root Files

**try.php** - Main try/catch wrapper
- Checks if error handling is enabled via $GLOBALS['padErrorTry']
- If disabled: executes code normally without try/catch
- If enabled: wraps execution in try/catch block
- On exception: logs error via padErrorGo() and includes catch handler
- Catches all Throwable types (errors and exceptions)

### Subdirectories

#### try/catch/call/ - Call Error Handlers

**_try.php** - Try block error handler
- Returns empty string on caught error
- Minimal recovery: suppresses error output

**_tryOnce.php** - Try-once error handler
- Returns empty string on caught error
- Single-attempt error handling

#### try/catch/eval/ - Evaluation Error Handlers

**eval.php** - Eval error handler
- Returns empty string on caught error
- Handles errors during expression evaluation

#### try/catch/level/ - Level Error Handlers

**go.php** - Level navigation error handler
- Sets $padNull[$pad] = TRUE
- Marks level as null/invalid on error
- Allows framework to skip failed level

**var.php** - Variable error handler
- Handles variable-related errors at level

## Error Handling Flow

### Normal Execution (padErrorTry = FALSE)

```
1. Check $GLOBALS['padErrorTry']
2. If FALSE: include PAD . "$padTry.php" directly
3. No error catching
4. Errors propagate normally
```

### Error Handling Enabled (padErrorTry = TRUE)

```
1. Check $GLOBALS['padErrorTry']
2. If TRUE: wrap execution in try block
3. Execute: include PAD . "$padTry.php"
4. On exception:
   a. Catch Throwable $padTryException
   b. Call padErrorGo() with:
      - Message: 'CATCH: ' + exception message
      - File: exception file
      - Line: exception line number
   c. Include catch handler: PAD . "try/catch/$padTry.php"
5. Return result (or empty string from catch handler)
```

## Key Variables

**$GLOBALS['padErrorTry']** - Master switch for try/catch behavior
- FALSE: Errors propagate normally
- TRUE: Errors are caught and handled

**$padTry** - Specifies which operation to try/catch
- Determines both the execution path and catch handler path
- Examples: 'call/_try', 'eval/eval', 'level/go'

**$padTryException** - Caught exception object (Throwable)
- Provides: getMessage(), getFile(), getLine()

**$padNull[$pad]** - Error flag for current level
- Set to TRUE when level encounters error
- Allows framework to skip/bypass failed levels

## Catch Handler Types

### Silent Handlers (Return Empty String)

- **call/_try.php** - Suppress call errors
- **call/_tryOnce.php** - Suppress single-attempt call errors
- **eval/eval.php** - Suppress evaluation errors

These handlers return empty strings, effectively hiding the error output while allowing execution to continue.

### State-Modifying Handlers

- **level/go.php** - Sets $padNull flag
- **level/var.php** - Handles variable errors

These handlers modify framework state to indicate error condition, allowing subsequent logic to check and respond appropriately.

## Integration with PAD Framework

The try module integrates with:

- **Error Logging** - Uses padErrorGo() to record caught exceptions
- **Level Processing** - Sets $padNull flags for failed levels
- **Expression Evaluation** - Catches eval errors
- **Function Calls** - Catches call errors
- **Variable Access** - Catches variable errors

## Usage Pattern

The try module is invoked internally by the PAD framework when executing operations that may throw exceptions. The typical usage pattern:

```php
// Framework sets the operation type
$padTry = 'level/go';

// Include the try wrapper
include PAD . 'try/try.php';

// If error occurs:
// - Exception is caught
// - Error is logged via padErrorGo()
// - Catch handler (try/catch/level/go.php) executes
// - Sets $padNull[$pad] = TRUE
// - Returns empty string or modified state
```

## Error Recovery Strategy

The module implements a graceful degradation strategy:

1. **Attempt Execution** - Try to execute the requested operation
2. **Catch Errors** - Catch any exceptions that occur
3. **Log Error** - Record error details for debugging
4. **Set State** - Mark operation as failed (via $padNull or return value)
5. **Continue** - Allow framework to continue execution
6. **Skip Failed** - Framework can check error state and skip failed operations

This prevents a single error from crashing the entire application while still logging the issue for developer attention.

## Design Rationale

### Conditional Error Handling

The check for $GLOBALS['padErrorTry'] allows the framework to:

- Disable error handling during development (see full error stack traces)
- Enable error handling in production (graceful error recovery)
- Toggle behavior without code changes

### Minimal Catch Handlers

Catch handlers are intentionally minimal:

- Return empty strings (hide error output)
- Set simple flags (mark as failed)
- No complex recovery logic (keep it fast and predictable)

### Throwable vs Exception

The module catches `Throwable` rather than `Exception`:

- Catches both Exceptions and Errors
- Handles all error types (parse errors, type errors, etc.)
- Maximum coverage for robustness

## Error Message Format

Caught errors are logged with prefix "CATCH:" to distinguish them from other error types:

```
CATCH: [Original exception message]
File: [Exception source file]
Line: [Exception line number]
```

This format helps developers identify caught exceptions in error logs and trace them back to the source.
