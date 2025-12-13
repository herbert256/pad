# Cache Module

This module provides server-side caching capabilities for PAD applications with support for multiple backend types (file-based, memory-based, database) and HTTP caching headers (ETag, Last-Modified).

## Overview

The cache module implements a sophisticated caching system that:
- Stores rendered page output to avoid regeneration
- Supports ETag-based HTTP caching for bandwidth optimization
- Provides multiple storage backends (file, Memcached, database)
- Implements 304 Not Modified responses for client-side caching
- Handles cache validation and expiration
- Supports optional data-less caching (metadata only)

## Directory Structure

### Core Files
- **inits.php** - Cache initialization and configuration, checks for cache hits
- **hit.php** - Handles cache hit scenarios (304 or 200 responses)
- **exits.php** - Updates cache after page generation

### Cache Types (`types/`)
- **file.php** - File-based caching implementation
- **memory.php** - Memcached-based caching implementation
- **db.php** - Database-based caching (placeholder/minimal)

## Key Components

### inits.php
Initializes the caching system and checks for cache hits before page generation.

**Process:**
1. Loads cache configuration from `config/cache.php`
2. Disables cache for non-GET requests or non-web output
3. Calculates MD5 hash of request URI as cache key
4. Determines maximum cache age from configuration
5. Loads appropriate cache backend type
6. Checks client's If-None-Match header (ETag)
7. Checks server-side cache for URL
8. Returns 304 Not Modified or 200 OK with cached content if valid
9. Sets `$padStop` to terminate processing on cache hit

**Global Variables Used:**
- `$padCache` - Enable/disable caching
- `$padCacheServerAge` - Maximum age in seconds for cached content
- `$padCacheServerType` - Backend type (file, memory, db)
- `$padCacheUrl` - MD5 hash of request URI
- `$padCacheMax` - Timestamp threshold for valid cache
- `$padCacheClient` - Client's ETag from If-None-Match header
- `$padCacheServerNoData` - If true, only store metadata (no content)

### hit.php
Handles cache hit responses, sending appropriate HTTP headers and terminating execution.

**Process:**
1. Sets `$padTime` to cache age
2. Sets `$padCacheStop` to the response code (200 or 304)
3. Includes `exits/output.php` to send response and exit

### exits.php
Updates the cache after page generation completes.

**Process:**
1. Compares new ETag with cached ETag
2. If same: Updates cache timestamps
3. If different: Deletes old cache entry and stores new content
4. Optionally compresses content with gzip before storage

**Global Variables Used:**
- `$padEtag` - Current page ETag
- `$padCacheEtag` - Previously cached ETag
- `$padCacheUrl` - URL cache key
- `$padOutput` - Generated page output

## Cache Backend Types

### File-based Cache (file.php)

Stores cache data in the filesystem.

**Functions:**
- `padCacheInit($url, $etag)` - No initialization needed for file backend
- `padCacheEtag($etag)` - Returns timestamp of ETag file
- `padCacheUrl($url)` - Returns [timestamp, etag] array for URL
- `padCacheGet($etag)` - Retrieves cached content by ETag
- `padCacheStore($url, $etag, $data)` - Stores URL→ETag mapping and content
- `padCacheUpdate($url, $etag)` - Updates ETag timestamp
- `padCacheDelete($url, $etag)` - Removes cache files
- `padCacheExists($file)` - Checks if cache file exists
- `padCacheTouch($file, $time)` - Updates file modification time
- `padCacheTime($file)` - Returns file modification time

**File Structure:**
```
$padCacheFile/
  ├─ url/
  │  └─ {md5_hash} → contains etag value
  └─ etag/
     └─ {etag_hash} → contains page content or empty (metadata-only)
```

**Global Variables:**
- `$padCacheFile` - Base directory for cache files
- `$padDirMode` - Directory permissions for created directories

### Memory-based Cache (memory.php)

Uses Memcached for high-performance caching.

**Functions:**
- `padCacheInit($url, $etag)` - Connects to Memcached server
- `padCacheEtag($get)` - Gets timestamp from Memcached
- `padCacheUrl($url)` - Gets [timestamp, etag] array from Memcached
- `padCacheGet($etag)` - Gets cached content (prefixed with "x")
- `padCacheStore($url, $etag, $data)` - Stores timestamp, URL mapping, and content
- `padCacheUpdate($url, $etag)` - Updates timestamp and touches content
- `padCacheDelete($url, $etag)` - Deletes from Memcached

**Memcached Keys:**
- `$etag` - Timestamp of generation
- `$url` - Array [timestamp, etag]
- `x$etag` - Actual page content (TTL = age + 10 seconds)

**Global Variables:**
- `$padCacheMemory` - Memcached object instance
- `$padCacheMemoryHost` - Memcached server hostname
- `$padCacheMemoryPort` - Memcached server port

### Database Cache (db.php)

Minimal placeholder for database-based caching (not fully implemented).

## Caching Strategies

### Full Content Caching
When `$padCacheServerNoData` is FALSE (default):
- Stores complete rendered page output
- Returns cached content directly on hit (200 OK)
- Best for high-traffic pages with expensive rendering

### Metadata-only Caching
When `$padCacheServerNoData` is TRUE:
- Only stores timestamps (no content)
- Returns 304 Not Modified on hit
- Client must have cached copy
- Saves storage space, relies on client-side cache

## HTTP Caching Integration

### ETag Support
- Generates unique ETag for each page version
- Checks client's `If-None-Match` header
- Returns 304 Not Modified when ETags match
- Reduces bandwidth usage

### Cache Validation
Three-level validation:
1. **Client ETag**: If client sends matching ETag → 304 response
2. **Server Cache + Client Date**: If both valid → 304 response
3. **Server Cache**: If valid cache exists → 200 with cached content

## Usage Example

```php
// Configuration (config/cache.php):
$padCache = TRUE;
$padCacheServerAge = 3600; // 1 hour
$padCacheServerType = 'file'; // or 'memory'
$padCacheFile = '/var/cache/pad/';
$padCacheServerNoData = FALSE; // Store full content
$padCacheServerGzip = TRUE; // Compress cached data

// On first request:
// - inits.php checks cache → miss
// - Page generates normally
// - exits.php stores output in cache

// On subsequent requests (within 1 hour):
// - inits.php checks cache → hit
// - If client has ETag → 304 Not Modified
// - If no client ETag → 200 OK with cached content
// - No page generation occurs

// After 1 hour:
// - Cache expires ($padCacheMax exceeded)
// - Page regenerates
// - New cache entry created
```

## Integration with PAD Framework

The cache module integrates deeply with PAD's request lifecycle:

- **Request Initialization**: Checked early in `inits.php` before page building
- **Output Generation**: Transparent to page rendering logic
- **Response Handling**: Works with `exits/output.php` for HTTP responses
- **Configuration System**: Uses `config/cache.php` for settings
- **Output Pipeline**: Integrates with compression and output buffering

## Configuration Options

### Required Settings
- `$padCache` - Enable/disable caching (boolean)
- `$padCacheServerAge` - Cache lifetime in seconds
- `$padCacheServerType` - Backend: 'file', 'memory', or 'db'

### File Backend Settings
- `$padCacheFile` - Directory path for cache files
- `$padDirMode` - Permissions for created directories

### Memory Backend Settings
- `$padCacheMemoryHost` - Memcached server hostname (default: 'localhost')
- `$padCacheMemoryPort` - Memcached server port (default: 11211)

### Optional Settings
- `$padCacheServerNoData` - Metadata-only mode (boolean, default: FALSE)
- `$padCacheServerGzip` - Compress cached content (boolean)

## Performance Considerations

- Cache checks occur before page building to maximize benefit
- File backend uses filesystem timestamps for efficient validation
- Memory backend provides fastest access but requires Memcached
- ETag-based caching reduces bandwidth even when cache expires
- Gzip compression reduces storage and transfer size
- Only GET requests are cached (POST/PUT/DELETE never cached)
- Non-web output types bypass caching
