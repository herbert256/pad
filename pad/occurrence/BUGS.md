# Bug Report - occurrence Directory

## Bugs Found

### 1. /home/herbert/pad/pad/occurrence/init.php

#### Line 3: Potential Undefined Variable
**Severity:** High

**Description:** Variable `$padOccur` is used with array access and increment operator without checking if it's initialized or if the `$pad` key exists.

```php
$padOccur [$pad]++;
```

**Issue:** If `$padOccur[$pad]` doesn't exist, this will trigger a PHP notice/warning (depending on error_reporting level) and may produce unexpected results.

**Recommendation:** Initialize or check existence before incrementing:
```php
if (!isset($padOccur[$pad])) $padOccur[$pad] = 0;
$padOccur[$pad]++;
```

---

#### Line 10: Potential Undefined Array Key
**Severity:** Medium

**Description:** The `key()` function is called on `$padData[$pad]` without checking if the array exists or has elements.

```php
$padKey [$pad] = key($padData [$pad]);
```

**Issue:** If `$padData[$pad]` is not set or is an empty array, `key()` will return NULL, which may cause issues in line 11.

---

#### Line 11: Array Access with Potentially NULL Key
**Severity:** Medium

**Description:** Array is accessed using `$padKey[$pad]` which may be NULL if the array was empty.

```php
$padCurrent [$pad] = $padData [$pad] [$padKey [$pad]];
```

**Issue:** This can produce an "Undefined array key" warning if `$padKey[$pad]` is NULL.

---

### 2. /home/herbert/pad/pad/occurrence/set.php

#### Line 17: Function Call Without Error Handling
**Severity:** Medium

**Description:** `padEval()` is called without any error handling or validation of the return value.

```php
foreach ( $padSetOcc [$pad] as $padK => $padV )
  $padCurrent [$pad] [$padK] = padEval ( $padV );
```

**Issue:** If `padEval()` fails or returns an unexpected type, it could cause issues in subsequent operations. Additionally, eval-related functions can be security risks if not properly sanitized.

**Security Concern:** If `padEval()` uses `eval()` internally, it could be vulnerable to code injection attacks.

---

### 3. /home/herbert/pad/pad/occurrence/table.php

#### Line 5: Logical AND with Undefined Variable Check
**Severity:** Medium

**Description:** The condition checks `isset()` but also performs a truthiness check which may be redundant.

```php
if ( isset ($padTableTag [$pad]) and $padTableTag [$pad] )
```

**Issue:** While not strictly a bug, this pattern suggests potential issues. If `$padTableTag[$pad]` exists but is falsy (0, false, empty string), the second condition fails, which may not be intended.

---

#### Line 11: Undefined Array Key Access
**Severity:** High

**Description:** Array access on potentially undefined nested array keys without proper validation.

```php
$padTables [$padK2] = $padTables [$padRelations[$padK] [$padK2] ['table']];
```

**Issue:** If `$padRelations[$padK][$padK2]['table']` doesn't exist in `$padTables`, this will trigger an undefined array key error.

---

### 4. /home/herbert/pad/pad/occurrence/end.php

#### Line 6: File Include Without Existence Check
**Severity:** Medium

**Description:** File is included without verifying it exists.

```php
if ( $padInfo )
  include PAD . 'events/occurEnd.php';
```

**Issue:** If the file doesn't exist, PHP will generate a warning. Should use `file_exists()` or consider using `@include` for optional includes.

---

### 5. /home/herbert/pad/pad/occurrence/occurrence.php

#### Line 11-12: Potential Undefined Array Key Access
**Severity:** Medium

**Description:** Nested array keys accessed without validation.

```php
if ( isset($padPrm [$pad] ['callback']) and ! isset ( $padPrm [$pad] ['before']) )
  include PAD . 'callback/row.php' ;
```

**Issue:** While `isset()` is used correctly here, if `$padPrm[$pad]` doesn't exist, the first `isset()` check will return false, but the pattern could be clearer.

---

## Summary

- **Critical:** 0
- **High:** 3
- **Medium:** 6
- **Low:** 0

**Total Bugs Found:** 9

### Most Critical Issues to Address:
1. Undefined variable initialization in init.php (line 3)
2. Array key existence validation in table.php (line 11)
3. Security review of padEval() function usage
