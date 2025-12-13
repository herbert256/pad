# cache/

Server-side caching system for rendered output.

## Files
- `exits.php` - Cache storage/retrieval at exit time (compares ETags, stores/updates cache)
- `types/` - Cache type handlers

## Behavior
- Stores rendered output with ETag for validation
- Supports gzip compression (`padCacheServerGzip`)
- Updates existing cache if ETag matches
- Deletes and recreates cache if content changed

## Configuration
Controlled by `$padCache` and `$padCacheServerAge` in config.
