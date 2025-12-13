# Shutdown and Output Processing

The exits module handles the final stage of PAD request processing, managing output formatting, HTTP response headers, caching, and graceful application shutdown.

## Overview

This module implements the exit and output pipeline that:
- Processes and formats final output
- Applies HTML tidying/beautification
- Handles different output types (web, console, download, file)
- Manages HTTP headers and status codes
- Supports ETags and conditional responses
- Integrates with caching system
- Ensures clean shutdown

## Directory Structure

### Main Files
- **exit.php** - Simple exit script that terminates execution
- **exits.php** - Main exit pipeline that orchestrates output processing
- **output.php** - Output dispatcher that routes to specific output handlers
- **tidy.php** - HTML tidying and beautification
- **myTidy.php** - Custom tidying implementation

### Output Handlers (`output/` subdirectory)
Different output delivery mechanisms:
- **web.php** - Web/HTTP output with headers and ETag support
- **console.php** - Command-line/console output
- **download.php** - File download with proper headers
- **file.php** - File system output

## Exit Pipeline Flow

### Main Exit Sequence (exits.php)
1. **Output Generation**: `padOutput($padResult[0])` - Convert result to output string
2. **Tidying** (optional): Apply HTML tidying if enabled
3. **ETag Generation**: `padMD5($padOutput)` - Generate content hash
4. **Status Code**: Default to 200 OK
5. **Cache Integration**: Check cache settings and handle conditional responses
6. **Output Dispatch**: Route to appropriate output handler
7. **Exit**: Clean shutdown with proper status code

### Output Routing (output.php)
1. Calculate content length (0 for non-200 responses)
2. Check all output buffers are clean
3. Decompress gzipped content if needed for non-web output
4. Include appropriate output handler based on `$padOutputType`
5. Call `padExit()` with proper status code

## Output Types

### Web Output (web.php)
Standard HTTP response handling:
- Checks ETag for 304 Not Modified responses
- Calls `padWebSend()` to send HTTP response
- Supports conditional GET with `If-None-Match` header
- Respects `$padWebEtag304` configuration

**Use Cases:**
- Standard web pages
- API responses
- Dynamic HTML content

### Console Output (console.php)
Command-line interface output:
- Trims output and adds newline
- Prevents duplicate sends via `$padSent` flag
- Immediately exits after output

**Use Cases:**
- CLI scripts
- Cron jobs
- Command-line tools

### Download Output (download.php)
File download with proper headers:
- Generates filename via `padFileName()`
- Sets download headers via `padDownLoadHeaders()`
- Includes content type and content length
- Forces browser download dialog

**Use Cases:**
- PDF downloads
- CSV exports
- Binary file downloads

### File Output (file.php)
Direct file system output:
- Writes to file system instead of HTTP response
- Used for static file generation
- Batch processing outputs

**Use Cases:**
- Static site generation
- Batch report generation
- Cache warming

## HTML Tidying (tidy.php)

The tidying system beautifies HTML output:

### Tidy Modes
1. **Standard Tidy** (`$padTidy`): Always apply tidying
2. **Conditional Tidy**: Only if `@tidy@` marker found in output
3. **Custom Tidy** (`$padMyTidy`): Use custom myTidy.php implementation

### Tidy Configuration
- Loads configuration from `PAD/config/tidy.php`
- Applies `padTidy()` function to format output
- Can use PHP Tidy extension or custom formatter

### Tidy Processing Flow
```php
if ($padTidy or strpos($padOutput, '@tidy@') !== FALSE)
    $padOutput = padTidy($padOutput);
elseif ($padMyTidy)
    include PAD . 'exits/myTidy.php';
```

## Key Functions and Variables

### Global Variables
- `$padResult` - Processing result array
- `$padOutput` - Final output string
- `$padOutputType` - Output type (web/console/download/file)
- `$padEtag` - ETag hash for caching
- `$padStop` - HTTP status code (default 200)
- `$padTidy` - Enable standard tidying
- `$padMyTidy` - Enable custom tidying
- `$padCache` - Enable caching
- `$padCacheServerAge` - Server-side cache age
- `$padLen` - Content length
- `$padContentType` - MIME type for downloads

### Exit Control
- `$GLOBALS['padBootShutdown']` - Prevent boot shutdown handler
- `$GLOBALS['padSkipShutdown']` - Skip error shutdown handler
- `$padSent` - Prevent duplicate console output

### Functions (defined elsewhere)
- `padOutput()` - Convert result to output string
- `padMD5()` - Generate MD5 hash for ETag
- `padCheckBuffers()` - Verify output buffers are clean
- `padUnzip()` - Decompress gzipped content
- `padWebSend()` - Send HTTP response
- `padFileName()` - Generate download filename
- `padDownLoadHeaders()` - Set download HTTP headers
- `padExit()` - Clean exit with status code
- `padTidy()` - HTML tidying function

## Integration with PAD Framework

### Cache Integration
The exits module integrates with the caching system:
1. Generates ETag from output
2. Includes `cache/exits.php` if caching enabled
3. Checks client ETag for 304 responses
4. Handles server-side cache age settings
5. Manages gzip compression for cached content

### Error Handling
Integrates with error system:
- Sets `padBootShutdown` to prevent boot error handler
- Sets `padSkipShutdown` to prevent runtime error handler
- Ensures clean exit even if errors occurred earlier
- Proper HTTP status codes for error conditions

### Output Buffer Management
Ensures all output buffers are handled:
- `padCheckBuffers()` verifies buffers are flushed
- All content goes through official output pipeline
- Prevents accidental output leakage
- Supports nested buffering from template processing

### HTTP Protocol
Implements HTTP best practices:
- Proper status codes (200, 304, 500, etc.)
- Content-Length headers
- Content-Type headers
- ETag support for caching
- Conditional GET support
- Compression handling

## Output Processing Flow

### Successful Request (200 OK)
```
Template Processing → padResult → padOutput()
→ Tidy (optional) → ETag Generation → Cache Check
→ Output Type Handler → HTTP Response → Exit
```

### Cached Response (304 Not Modified)
```
Template Processing → padOutput() → ETag Generation
→ ETag Match → Change Status to 304 → Exit (no body)
```

### Error Response (500 Internal Server Error)
```
Error Detected → Error Handler → padExit(500)
→ Output Handler → HTTP 500 → Exit
```

### Console Output
```
Template Processing → padOutput() → Trim
→ Console Echo → Exit
```

## Performance Optimization

The exits module includes optimizations:
- **Content Length Calculation**: Only for 200 responses
- **Conditional Tidying**: Only when needed
- **ETag Caching**: Avoids re-sending unchanged content
- **Gzip Decompression**: Only for non-web cached content
- **Early Exit**: 304 responses skip body transmission
- **Buffer Cleanup**: Prevents memory leaks from abandoned buffers

## Configuration Options

Key configuration variables:
- `$padOutputType` - Output delivery method
- `$padTidy` - Enable HTML tidying
- `$padMyTidy` - Use custom tidying
- `$padCache` - Enable caching
- `$padCacheServerAge` - Cache duration
- `$padCacheServerGzip` - Enable gzip compression
- `$padWebEtag304` - Enable ETag 304 responses

## Use Cases

### Web Applications
Standard web output with caching, compression, and conditional responses.

### API Endpoints
JSON/XML output with proper content types and caching headers.

### CLI Tools
Console output for scripts, cron jobs, and administrative tools.

### File Downloads
Binary downloads with proper headers and content disposition.

### Static Site Generation
File system output for pre-rendering content.

### Batch Processing
File output for reports, exports, and batch operations.
