# error/

Error handling system with multiple modes and handlers.

## Files
- `boot.php` - Lightweight bootstrap error handler (loaded first, before full PAD)
- `error.php` - Full PAD error handler
- `types/` - Error type-specific handlers

## Boot Handler Functions
- `padBootHandler()` - Error handler during bootstrap
- `padBootException()` - Exception handler during bootstrap
- `padBootShutdown()` - Shutdown handler to catch fatal errors
- `padBootStop()` - Stops execution and displays error
- `padLocal()` - Detects if running locally (shows full errors) vs remote (shows error ID only)

## Error Modes (`$padErrorAction`)
- `pad` - Full PAD error handler with logging and reporting
- `boot` - Keep using lightweight bootstrap handler
- `php` - Use PHP defaults
- `stop` - Stop processing but run PAD stop handling
- `exit` - Exit immediately
- `ignore` - Ignore errors and continue
- `log` - Log to Apache error log and continue
- `dump` - Dump to DATA directory and continue
