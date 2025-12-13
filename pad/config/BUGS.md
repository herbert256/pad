# Bugs Found in /home/herbert/pad/pad/config/

## 1. Duplicate Variable Assignment in info/xml.php

**File:** `/home/herbert/pad/pad/config/info/xml.php`
**Lines:** 6-7
**Bug Type:** Logic Error - Redundant Assignment

```php
$padInfoXmlParms     = TRUE;
$padInfoXmlCompact = TRUE;    // Line 6
$padInfoXmlCompact   = TRUE;  // Line 7 - DUPLICATE
$padInfoXmlTidy      = TRUE;
```

**Issue:** The variable `$padInfoXmlCompact` is assigned twice consecutively with the same value. This is redundant and may indicate a copy-paste error where a different variable name was intended.

**Impact:** No functional impact (both assignments are identical), but indicates possible missing configuration variable.

---

## 2. Variable Name Typo in info/none.php

**File:** `/home/herbert/pad/pad/config/info/none.php`
**Line:** 62
**Bug Type:** Variable Name Typo

```php
$padInfoTraceContent     = FALSE;
$padInfoTraceFALSE       = FALSE;  // Line 62 - TYPO: should be $padInfoTraceTrue
$padInfoTraceFalse       = FALSE;  // Line 63 - Correct variable name
```

**Issue:** Line 62 has `$padInfoTraceFALSE` (all caps FALSE) instead of `$padInfoTraceTrue`. This appears to be a typo. Looking at other configuration files (like `/home/herbert/pad/pad/config/info/all.php` line 62 and `/home/herbert/pad/pad/config/info/trace.php` line 49), this variable should be `$padInfoTraceTrue`.

**Impact:** The intended variable `$padInfoTraceTrue` may not be properly initialized, potentially causing undefined variable issues if the code expects this variable to be set.
