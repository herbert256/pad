# Bug Report: /home/herbert/pad/pad/lib/

## Bug 1: Undefined Variable in SQL Query
**File:** `/home/herbert/pad/pad/lib/api.php`
**Line:** 18
**Severity:** High

**Description:**
Variable `$padScript` is used but not defined in the function scope, and it's not declared as global.

```php
return "$padHost$padScript?$fast";
```

**Issue:** Undefined variable `$padScript`. Should be declared as global or passed as parameter.

---

## Bug 2: Function Name Typo
**File:** `/home/herbert/pad/pad/lib/options.php`
**Line:** 46, 51
**Severity:** High

**Description:**
Function calls `pad_error()` with underscore instead of `padError()` with camelCase.

```php
pad_error ("Closing ) without an opening (");
pad_error ("Closing ] without an opening [");
```

**Issue:** Incorrect function name will cause fatal error. Should be `padError()`.

---

## Bug 3: Logic Error with strpos()
**File:** `/home/herbert/pad/pad/lib/level/var.php`
**Line:** 5
**Severity:** High

**Description:**
Using `strpos()` return value directly in boolean context causes bug when pipe is at position 0.

```php
$padPipe = strpos ( $padBetween, '|' );

if ( $padPipe ) {
```

**Issue:** When `|` is at position 0, `strpos()` returns 0, which is falsy. Should use `!== FALSE` check.

---

## Bug 4: Array Index Out of Bounds
**File:** `/home/herbert/pad/pad/lib/table.php`
**Line:** 323
**Severity:** Medium

**Description:**
Array access without checking if the index exists.

```php
if ( ! isset ( $fld, $padTable [$pad] [$table] ) )
  continue 2;
```

**Issue:** `isset()` with multiple parameters checks if all are set, but doesn't validate array structure properly.

---

## Bug 5: Potential SQL Injection in Dynamic Table Names
**File:** `/home/herbert/pad/pad/lib/table.php`
**Line:** 38
**Severity:** Critical

**Description:**
Database table and field names are used directly in SQL queries without proper validation or escaping.

```php
return db ("$type $start $fields from $db $join $where $group $having $union $order $limit");
```

**Issue:** Variables like `$db`, `$fields`, etc. are not properly validated/escaped. If user-controlled, this could lead to SQL injection.

---

## Bug 6: Missing Error Handling in Database Connection
**File:** `/home/herbert/pad/pad/lib/db.php`
**Line:** 36-39
**Severity:** High

**Description:**
The function returns from `padError()` but the calling code may not check for failure properly.

```php
$connect = mysqli_connect ( "$host" , $user , $password , $database );

if ( ! $connect )
  return padError ( mysqli_connect_errno ( ) . ' - ' . mysqli_connect_error ( ) );
```

**Issue:** `padError()` returns `FALSE`, but code continues to use `$connect`. Should throw exception or exit.

---

## Bug 7: Undefined Variable in Query Replacement
**File:** `/home/herbert/pad/pad/lib/db.php`
**Line:** 59
**Severity:** Medium

**Description:**
The condition checks if substring starts with 'x', but the variable name uses `$i` which might not be what's intended.

```php
if (substr($i, 0, 1) <> 'x')
```

**Issue:** Should this be checking `$replace` instead of `$i`? Logic seems incorrect.

---

## Bug 8: Missing Array Key Validation
**File:** `/home/herbert/pad/pad/lib/field/level.php`
**Line:** 7-14
**Severity:** Medium

**Description:**
Complex array access patterns without comprehensive validation.

```php
if ( strlen($field) > 1 and substr($field,0,1) == '-' and is_numeric(substr($field,1)) ) {
  $idx = $pad + $field;
  if ( $type == 1 and $idx and isset ($padCurrent [$idx]) )
    return TRUE;
```

**Issue:** Adding negative string to number without validation could cause unexpected results.

---

## Bug 9: Potential Path Traversal
**File:** `/home/herbert/pad/pad/lib/file.php`
**Line:** 52-56
**Severity:** Critical

**Description:**
While there are some checks, the validation might not catch all path traversal attempts.

```php
if ( ! str_starts_with ( $file, '/' )       ) return "Invalid file (not starting with /): $file";
if ( strpos($file, '..' ) !== FALSE         ) return "Invalid file (contains '..'): $file";
```

**Issue:** The checks are good but might not catch encoded traversal attempts (like `%2e%2e`).

---

## Bug 10: Race Condition in File Operations
**File:** `/home/herbert/pad/pad/lib/file.php`
**Line:** 65-73
**Severity:** Medium

**Description:**
There's a TOCTOU (Time-of-check Time-of-use) race condition between checking if directory is writeable and creating it.

```php
if ( ! is_writeable ( $dir ) ) {
  if ( file_exists ( $dir)  )
    return padError ( "Directory can not be written: $dir" );

  if ( ! mkdir ($dir, $GLOBALS ['padDirMode'], true ) )
    return padError ( "Error creating directory: $dir" );
}
```

**Issue:** Between the check and mkdir, another process could create the directory.

---

## Bug 11: Typo in Error Message
**File:** `/home/herbert/pad/pad/lib/error.php`
**Line:** 251
**Severity:** Low

**Description:**
Spelling error in error message.

```php
echo '<pre>Unknow error occurred.</pre>';
```

**Issue:** "Unknow" should be "Unknown".

---

## Bug 12: Insecure Deserialization
**File:** `/home/herbert/pad/pad/lib/curl.php`
**Line:** Various
**Severity:** Medium

**Description:**
While not directly in this file, the curl functionality could be used to fetch and deserialize untrusted data.

**Issue:** Need to validate data before deserializing in calling code.

---

## Bug 13: Missing Input Validation
**File:** `/home/herbert/pad/pad/lib/other.php`
**Line:** 607
**Severity:** Medium

**Description:**
The `padMakeSafe()` function has good sanitization but max length of 2048 might be too large for some contexts.

```php
function padMakeSafe ( $input, $len=2048 ) {
```

**Issue:** Default length might be too permissive for error messages.

---

## Bug 14: Potential XSS in Error Output
**File:** `/home/herbert/pad/pad/lib/error.php`
**Line:** 249, 273
**Severity:** High

**Description:**
Error messages are echoed without HTML escaping when displayed.

```php
echo "<pre>\nError: $info</pre>";
echo "\n<pre>$error\n\n$buffer</pre>";
```

**Issue:** If error messages contain user input, this could lead to XSS vulnerabilities.

---

## Bug 15: Weak Random String Generation
**File:** `/home/herbert/pad/pad/lib/other.php`
**Line:** 743-756
**Severity:** Medium

**Description:**
Uses `mt_rand()` which is not cryptographically secure.

```php
function padRandomChar () {
  $random = mt_rand(0,61);
```

**Issue:** For session IDs or security tokens, should use `random_int()` instead.

---

## Bug 16: Unsafe Global Variable Access Pattern
**File:** `/home/herbert/pad/pad/lib/other.php`
**Line:** 961, 984
**Severity:** Medium

**Description:**
The code unsets and re-sets global variables without proper locking, which could cause race conditions.

```php
else
  unset ( $GLOBALS [$name] );

$GLOBALS [$name] = $value;
```

**Issue:** In multi-threaded environments (rare in PHP but possible), this could cause issues.

---

## Bug 17: Type Juggling Vulnerability
**File:** `/home/herbert/pad/pad/lib/other.php`
**Line:** 566-568
**Severity:** Medium

**Description:**
Using loose comparison for string counting could lead to type juggling issues.

```php
if ( ( substr_count($string, '{'.$tag.' ' ) + substr_count($string, '{'.$tag.'}' ) )
       <>
     ( substr_count($string, '{/'.$tag.' ') + substr_count($string, '{/'.$tag.'}') ) )
```

**Issue:** Should use strict comparison (`!==`) instead of `<>`.

---

## Bug 18: Potential Information Disclosure
**File:** `/home/herbert/pad/pad/lib/other.php`
**Line:** 4-18
**Severity:** Medium

**Description:**
The `padInfo()` function returns potentially sensitive information including session IDs.

```php
return [
  'session' => $GLOBALS ['padSesID'] ?? '',
  'request' => $GLOBALS ['padReqID'] ?? '',
```

**Issue:** This information should not be exposed to users in production environments.

---

## Bug 19: Missing Bounds Checking
**File:** `/home/herbert/pad/pad/lib/table.php`
**Line:** 179
**Severity:** Medium

**Description:**
Array access without bounds checking.

```php
$where .= padTableField($v) . ' = `' . $db . '`.' . padTableField($values2[$k]);
```

**Issue:** `$values2[$k]` might not exist if arrays have different lengths.

---

## Bug 20: Directory Traversal in Recursive Iterator
**File:** `/home/herbert/pad/pad/lib/at.php`
**Line:** 3
**Severity:** High

**Description:**
Using glob without proper path validation.

```php
foreach  ( glob ( PAD . 'at/_lib/*.php' ) as $padAt )
  include_once "$padAt";
```

**Issue:** If `PAD` constant is user-controllable or contains `..`, this could include files outside intended directory.
