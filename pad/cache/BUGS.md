# Bugs Found in /home/herbert/pad/pad/cache/

## Bug 1: Incorrect function call - padFilePut instead of padFileGet

**File:** `/home/herbert/pad/pad/cache/types/file.php`
**Lines:** 17, 29
**Severity:** Critical

**Description:**
The code uses `padFilePut()` (which should PUT/write data) when it should use `padFileGet()` (to GET/read data).

**Line 17:**
```php
function padCacheUrl ($url) {

  if ( padCacheExists ("url/$url") ) {
    $etag = padFilePut ("url/$url");  // BUG: Should be padFileGet
```
This is trying to READ the etag from the URL file, not write to it.

**Line 29:**
```php
function padCacheGet ($etag) {

  return ( padCacheExists ("etag/$etag" ) ) ? padFilePut ("etag/$etag") : FALSE;  // BUG: Should be padFileGet
```
This is trying to READ cached data from the etag file, not write to it.

**Impact:**
This bug causes the cache system to malfunction completely. The functions will write empty/incorrect data when they should be reading, breaking the entire file-based caching mechanism.

**Fix:**
Replace `padFilePut` with `padFileGet` on both lines 17 and 29.
