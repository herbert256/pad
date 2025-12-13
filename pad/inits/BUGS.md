# Bug Report: /home/herbert/pad/pad/inits/

## Bug 1: Undefined Variable Usage
**File:** `/home/herbert/pad/pad/inits/clean.php`
**Line:** 3
**Severity:** Medium

**Description:**
The variable `$padIgnored` is passed to `padEmptyBuffers()` but is never defined before use. This will cause a PHP notice about undefined variable.

```php
padEmptyBuffers ( $padIgnored );
```

**Issue:** Undefined variable `$padIgnored`

---

## Bug 2: Deprecated/Unsafe Comparison Operator
**File:** `/home/herbert/pad/pad/inits/cookies.php`
**Line:** 7
**Severity:** Low

**Description:**
The code uses the deprecated `<>` operator instead of `!==` for comparison. While this still works, it's not recommended for modern PHP.

```php
if ( ! isset($_COOKIE['padSesID']) or $_COOKIE['padSesID'] <> $padSesID )
```

**Issue:** Use of deprecated `<>` operator, should use `!==` for strict comparison.

---

## Bug 3: Potential Directory Traversal Vulnerability
**File:** `/home/herbert/pad/pad/inits/database.php`
**Line:** 3-11
**Severity:** Critical

**Description:**
The code uses `RecursiveDirectoryIterator` to include all PHP files from `APP . '_database'` directory without any validation or security checks. If the `APP` constant contains user-controllable data or if malicious PHP files are placed in that directory, they will be automatically included.

```php
$padLibDirectory = new RecursiveDirectoryIterator ( APP . '_database' );
$padLibIterator  = new RecursiveIteratorIterator  ( $padLibDirectory );

foreach ( $padLibIterator as $padLibOne ) {
  $padLibFile = str_replace ('\\', '/' , $padLibOne->getPathname() );
  if ( substr($padLibFile, -4) == '.php' )
    include_once $padLibFile;
}
```

**Issue:** Automatic inclusion of files without validation could lead to code execution vulnerabilities.

---

## Bug 4: Missing Variable Initialization
**File:** `/home/herbert/pad/pad/inits/parms.php`
**Line:** 7
**Severity:** Medium

**Description:**
The code checks `count($padSessionVars)` but `$padSessionVars` may not be defined, leading to a PHP warning.

```php
if (count($padSessionVars) ) {
```

**Issue:** Variable `$padSessionVars` may not be initialized before use.

---

## Bug 5: Insecure extract() Usage
**File:** `/home/herbert/pad/pad/inits/fast.php`
**Line:** 13
**Severity:** Critical

**Description:**
The code uses `extract()` with `EXTR_OVERWRITE` on unserialized data from a database query. This is extremely dangerous as it can overwrite any variables in the current scope, including security-critical variables.

```php
extract ( unserialize ( $padFast ), EXTR_OVERWRITE );
```

**Issue:** Using `extract()` with `EXTR_OVERWRITE` on untrusted data is a severe security vulnerability that could allow variable injection attacks.

---

## Bug 6: SQL Injection Vulnerability
**File:** `/home/herbert/pad/pad/inits/fast.php`
**Line:** 8
**Severity:** Critical

**Description:**
The SQL query directly interpolates `$_SERVER['QUERY_STRING']` without proper escaping, which could lead to SQL injection.

```php
$padFast = padDb ( "field vars from links where link = '{1}'", [ 1 => $_SERVER['QUERY_STRING'] ] );
```

**Issue:** While parameterized, need to verify that `padDb()` properly escapes the parameter. If not, this is an SQL injection vulnerability.

---

## Bug 7: Unsafe Global Variable Manipulation
**File:** `/home/herbert/pad/pad/inits/nono.php`
**Line:** 11-13
**Severity:** Medium

**Description:**
The code iterates through and unsets global variables based on prefix matching, which could accidentally unset critical system variables.

```php
foreach ( $GLOBALS as $key => $value )
  if ( substr ( $key, 0, 3 ) == 'pad' and $key <> 'padNoNo' )
    unset ( $GLOBALS[$key] );
```

**Issue:** Unsetting globals without proper validation could cause unexpected behavior.

---

## Bug 8: Missing File Existence Check
**File:** `/home/herbert/pad/pad/inits/app.php`
**Line:** 6
**Severity:** Medium

**Description:**
The code includes a file without checking if the `PAD` constant is defined or if the file exists.

```php
include PAD . 'build/build.php';
```

**Issue:** Missing file existence check could cause fatal errors.

---

## Bug 9: Deprecated Comparison Operator
**File:** `/home/herbert/pad/pad/inits/host.php`
**Line:** 12
**Severity:** Low

**Description:**
Uses deprecated `<>` operator instead of `!==`.

```php
if ( ($padRequestScheme == 'http'  and $padServerPort <> 80) or
     ($padRequestScheme == 'https' and $padServerPort <> 443) )
```

**Issue:** Should use `!==` for strict comparison.
