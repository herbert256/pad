# Bug Report for /tags Directory

## High Severity Bugs

### 1. Potential SQL Injection Vulnerability
**File:** `/home/herbert/pad/pad/tags/check.php`
**Line:** 3
**Bug:** User input is directly concatenated into what appears to be a database query without sanitization or prepared statements.
**Code:**
```php
return db ( $padTag [$pad] . ' ' . $padParm ) ? TRUE : FALSE;
```
**Severity:** High

### 2. Potential SQL Injection Vulnerability
**File:** `/home/herbert/pad/pad/tags/record.php`
**Line:** 3
**Bug:** User input is directly concatenated into what appears to be a database query without sanitization or prepared statements.
**Code:**
```php
return db ( $padTag [$pad] . ' ' . $padParm );
```
**Severity:** High

### 3. Potential Path Traversal Vulnerability
**File:** `/home/herbert/pad/pad/tags/dir.php`
**Line:** 5
**Bug:** User-controlled variable `$padParm` is passed directly to `scandir()` without validation. Could allow directory traversal attacks.
**Code:**
```php
$padDir = $padParm;
return scandir($padDir);
```
**Severity:** High

### 4. Potential Path Traversal/Code Injection
**File:** `/home/herbert/pad/pad/tags/output.php`
**Line:** 5
**Bug:** User-controlled variable `$padParm` is used in an include path without validation. Could allow arbitrary file inclusion.
**Code:**
```php
$padOutputType = $padParm;
include PAD . "config/output/$padOutputType.php";
```
**Severity:** High

## Medium Severity Bugs

### 5. Array Index Error
**File:** `/home/herbert/pad/pad/tags/files.php`
**Line:** 50
**Bug:** Arithmetic error in substr calculation: `0+1` instead of just `1`, and `strrpos($padFiles ['item'], '.')-1` should subtract from the total length, not from the position.
**Code:**
```php
$padFiles ['item']  = substr ( $padFiles ['item'], 0+1, strrpos($padFiles ['item'], '.')-1 );
```
**Expected:**
```php
$padFiles ['item']  = substr ( $padFiles ['item'], 1, strrpos($padFiles ['item'], '.') - 1 );
```
**Severity:** Medium

### 6. Potential Undefined Variable
**File:** `/home/herbert/pad/pad/tags/case.php`
**Line:** 2-33
**Bug:** Multiple variables (`$padParms`, `$padContent`, `$pad`) used without validation if they exist.
**Severity:** Medium

### 7. Missing Error Handling for CURL
**File:** `/home/herbert/pad/pad/tags/curl.php`
**Line:** 3-14
**Bug:** While there is a check for result code, there's no validation that `$padCurl` array has the expected keys before accessing them.
**Code:**
```php
if ( $padCurl ['result'] <> '200' )
  padError ( "Curl failed: " . $padCurl ['result'] . ' ' . $padCurl ['url'] );
```
**Severity:** Medium

### 8. Type Mismatch in Comparison
**File:** `/home/herbert/pad/pad/tags/curl.php`
**Line:** 13
**Bug:** Using `<>` (old-style not equal) and comparing potentially numeric HTTP status code with string '200'. Should use strict comparison.
**Code:**
```php
if ( $padCurl ['result'] <> '200' )
```
**Expected:**
```php
if ( $padCurl ['result'] !== 200 )
```
**Severity:** Medium

### 9. Potential Undefined Function Calls
**File:** `/home/herbert/pad/pad/tags/decrement.php`
**Line:** 3
**Bug:** Function `padFieldName()` is called but may not be defined. No error handling.
**Severity:** Medium

### 10. Potential Undefined Function Calls
**File:** `/home/herbert/pad/pad/tags/increment.php`
**Line:** 3
**Bug:** Function `padFieldName()` is called but may not be defined. No error handling.
**Severity:** Medium

### 11. Undefined Function Call
**File:** `/home/herbert/pad/pad/tags/echo.php`
**Line:** 3
**Bug:** Function `padEval()` is called but may not be defined. No error handling.
**Severity:** Medium

### 12. Undefined Function Call
**File:** `/home/herbert/pad/pad/tags/if.php`
**Line:** 14, 28
**Bug:** Functions `padEval()` and `padEvalBool()` are called but may not be defined. No error handling.
**Severity:** Medium

### 13. Undefined Function Call
**File:** `/home/herbert/pad/pad/tags/case.php`
**Line:** 2, 16, 33
**Bug:** Functions `padEval()`, `padCheckTag()` are called but may not be defined. No error handling.
**Severity:** Medium

### 14. Undefined Function Call
**File:** `/home/herbert/pad/pad/tags/while.php`
**Line:** 3, 6
**Bug:** Functions `padStartAndClose()`, `padEvalBool()` are called but may not be defined. No error handling.
**Severity:** Medium

### 15. Potential Undefined Variable
**File:** `/home/herbert/pad/pad/tags/go/data.php`
**Line:** 3-10
**Bug:** Variables `$padTag`, `$pad`, `$padParm` used without validation.
**Severity:** Medium

### 16. Potential Undefined Variable
**File:** `/home/herbert/pad/pad/tags/go/store.php`
**Line:** 3-48
**Bug:** Multiple variables (`$padWalk`, `$padPad`, `$padPrmType`, etc.) used without validation if they're defined.
**Severity:** Medium

### 17. Missing Error Handling
**File:** `/home/herbert/pad/pad/tags/files.php`
**Line:** 22-26
**Bug:** No error handling for DirectoryIterator/RecursiveDirectoryIterator constructors. If directory doesn't exist or is unreadable, will throw exception.
**Code:**
```php
$padFilesDirectory = new RecursiveDirectoryIterator ( $padFilesScan );
$padFilesIterator  = new RecursiveIteratorIterator  ( $padFilesDirectory );
```
**Severity:** Medium

### 18. Undefined Constant Reference
**File:** `/home/herbert/pad/pad/tags/action.php`
**Line:** 3
**Bug:** Constant `PQ` is used without checking if defined. Could cause fatal error.
**Code:**
```php
return include PQ . 'start/tags/action.php';
```
**Severity:** Medium

### 19. Undefined Constant Reference
**File:** Multiple files (action.php, continue.php, flag.php, keep.php, make.php, pull.php, remove.php, sequence.php)
**Bug:** Constant `PQ` is used without checking if defined in all these files. Could cause fatal error.
**Severity:** Medium

### 20. Potential Undefined Function Call
**File:** `/home/herbert/pad/pad/tags/go/store.php`
**Line:** 25, 32
**Bug:** Functions `padMakeContent()`, `padData()`, `padIsDefaultData()`, `padMakeFlag()` are called but may not be defined. No error handling.
**Severity:** Medium

### 21. Logic Error in String Manipulation
**File:** `/home/herbert/pad/pad/tags/files.php`
**Line:** 52
**Bug:** `strrpos($padFiles ['item'], '/')` could return false if no '/' found, causing substr to fail.
**Code:**
```php
$padFiles ['dir']   = substr ( $padFiles ['item'], 0, strrpos($padFiles ['item'], '/') );
```
**Severity:** Medium

### 22. Undefined Function Call
**File:** `/home/herbert/pad/pad/tags/file.php`
**Line:** 15
**Bug:** Functions `padTagParm()`, `padFileName()`, `padFilePut()` are called but may not be defined. No error handling.
**Severity:** Medium

## Low Severity Bugs

### 23. Redundant Boolean Conversion
**File:** `/home/herbert/pad/pad/tags/check.php`
**Line:** 3
**Bug:** Using ternary operator to convert to TRUE/FALSE when the expression already returns a boolean.
**Code:**
```php
return db ( $padTag [$pad] . ' ' . $padParm ) ? TRUE : FALSE;
```
**Expected:**
```php
return (bool) db ( $padTag [$pad] . ' ' . $padParm );
```
**Severity:** Low

### 24. Missing Error Handling for Include
**File:** Multiple files
**Bug:** Include statements have no error handling. If file doesn't exist, will cause warning or fatal error.
**Severity:** Low

### 25. Inconsistent Return Types
**File:** `/home/herbert/pad/pad/tags/go/store.php`
**Line:** 47
**Bug:** Function returns `NULL` but other code paths may return different values. Inconsistent return types can cause issues.
**Severity:** Low

### 26. Undefined Constant Reference
**File:** Multiple files
**Bug:** Constant `PAD` is used throughout without validation. If not defined, will cause fatal error.
**Severity:** Low

### 27. Potential Undefined Variable
**File:** `/home/herbert/pad/pad/tags/count.php`
**Line:** 3-12
**Bug:** Variables `$padDataStore`, `$padParm` used without validation if they exist.
**Severity:** Low
