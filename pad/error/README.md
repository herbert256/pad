# Error Handling System

The error handling system in PAD framework provides comprehensive error detection, reporting, and recovery mechanisms for both boot-time and runtime errors.

## Overview

This module implements a dual-stage error handling architecture:
- **Boot-time error handling**: Catches errors during framework initialization
- **Runtime error handling**: Handles errors during normal application execution

The system intercepts PHP errors, exceptions, and shutdown events to provide consistent error reporting across different environments (local development vs. production).

## Directory Structure

### Main Files
- **boot.php** - Boot-time error handler active during framework initialization
- **error.php** - Core error handling functions (handlers, exceptions, shutdown)

### Error Types (`types/` subdirectory)
Different error handling strategies that can be configured:

- **boot.php** - Boot-time error type (used during initialization)
- **pad.php** - Standard PAD error handler with logging, dumping, and graceful exit
- **log.php** - Logs errors to error log without displaying
- **dump.php** - Dumps error information to directory for debugging
- **exit.php** - Immediately exits on error
- **stop.php** - Stops execution with error message
- **ignore.php** - Silently ignores errors (returns empty string)
- **php.php** - Uses PHP's default error handling

## Key Functions

### Boot-time Error Handling
- `padBootHandler()` - Intercepts PHP errors during boot
- `padBootException()` - Intercepts exceptions during boot
- `padBootShutdown()` - Intercepts fatal errors during boot
- `padBootStop()` - Graceful error display and exit
- `padShowErrorLocal()` - Shows detailed errors for local development
- `padShowErrorRemote()` - Shows minimal error ID for production
- `padLocal()` - Determines if running in local development environment

### Runtime Error Handling
- `padErrorHandler()` - Main error handler for PHP errors
- `padErrorException()` - Main exception handler
- `padErrorShutdown()` - Shutdown function to catch fatal errors
- `padErrorReporting()` - Configures error reporting levels (none, error, warning, notice, all)
- `padErrorGo()` - Central error processing function (defined in type-specific files)

## Error Handling Flow

### Boot Time (boot.php)
1. Disables display_errors and sets error_reporting to E_ALL
2. Registers custom handlers for errors, exceptions, and shutdown
3. On error:
   - Cleans all output buffers
   - Sets HTTP 500 status code
   - Shows detailed error (local) or error ID (production)
   - Logs error with unique request ID
   - Gracefully exits

### Runtime (error.php)
1. Sets error reporting level based on configuration
2. Registers runtime error handlers
3. On error:
   - Calls configured error type handler (padErrorGo)
   - Logs error if enabled
   - Dumps debug information if enabled
   - Exits with HTTP 500 status

## Configuration

Error behavior is controlled by:
- `$padErrorLevel` - Error reporting level (none/error/warning/notice/all)
- `$padErrorLog` - Enable/disable error logging
- `$padErrorReport` - Enable/disable error dumping to directory
- Error type files determine handling strategy

## Integration with PAD Framework

The error system is the first component loaded during PAD bootstrap:
1. **boot.php** loads first to catch initialization errors
2. Once PAD is initialized, **error.php** replaces boot handlers
3. `padErrorRestoreBoot()` is called to switch from boot to runtime handlers
4. Error types can be swapped for different environments (development/production)
5. Integrates with exits system for graceful shutdown
6. Supports error tracing when `padInfo` system is enabled

## Environment Detection

The system automatically detects local vs. production environments:
- **Local**: localhost, 127.0.0.1, ::1, penguin.linux.test, CLI mode
- **Production**: All other environments

This determines error display verbosity and logging behavior.
