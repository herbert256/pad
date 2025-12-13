# Bug Report - Config Directory

## Bugs Found

### 1. Undefined Constant: DAT
**File:** `/home/herbert/pad/pad/config/cache.php`
**Line:** 29
**Severity:** Critical
**Description:** The constant `DAT` is used but not defined in this file. This will cause a fatal error if the constant is not defined elsewhere before this file is included.
```php
$padCacheFile = DAT . 'cache/';
```

### 2. Duplicate Variable Assignment
**File:** `/home/herbert/pad/pad/config/info/xml.php`
**Line:** 6-7
**Severity:** Low
**Description:** Variable `$padInfoXmlCompact` is assigned twice on consecutive lines. The first assignment on line 6 is immediately overwritten by line 7.
```php
$padInfoXmlCompact = TRUE;  // Line 6
$padInfoXmlCompact = TRUE;  // Line 7
```

### 3. Variable Name Typo
**File:** `/home/herbert/pad/pad/config/info/none.php`
**Line:** 62
**Severity:** Medium
**Description:** Variable name `$padInfoTraceFALSE` (all caps FALSE) is inconsistent with the pattern used elsewhere. Should likely be `$padInfoTraceTrue` based on line 63 and the naming convention used throughout the file.
```php
$padInfoTraceFALSE = FALSE;  // Line 62 - should be $padInfoTraceTrue
$padInfoTraceFalse = FALSE;  // Line 63
```

### 4. Undefined Constant: PAD
**File:** `/home/herbert/pad/pad/config/output/download.php`
**Line:** 5
**Severity:** Critical
**Description:** The constant `PAD` is used but not defined in this file. This will cause a fatal error if the constant is not defined elsewhere before this file is included.
```php
include PAD . 'config/output/file.php';
```

### 5. Hardcoded Database Credentials
**File:** `/home/herbert/pad/pad/config/config.php`
**Lines:** 41-44, 48-51
**Severity:** High
**Description:** Database credentials are hardcoded in the configuration file. This is a security risk as passwords should not be stored in plain text in source code. Consider using environment variables or a secure credential management system.
```php
$padSqlPadHost     = '127.0.0.1';
$padSqlPadDatabase = 'pad';
$padSqlPadUser     = 'pad';
$padSqlPadPassword = 'pad';  // Hardcoded password

$padSqlHost     = '127.0.0.1';
$padSqlDatabase = 'app';
$padSqlUser     = 'app';
$padSqlPassword = 'app';  // Hardcoded password
```

### 6. Hardcoded Cache Database Credentials
**File:** `/home/herbert/pad/pad/config/cache.php`
**Lines:** 24-27
**Severity:** High
**Description:** Cache database credentials are hardcoded in the configuration file. Same security concern as issue #5.
```php
$padCacheDbHost     = 'localhost';
$padCacheDbDatabase = 'cache';
$padCacheDbUser     = 'cache';
$padCacheDbPassword = 'cache';  // Hardcoded password
```
