# Bugs Found in /home/herbert/pad/pad/info/

## 1. Logic Error - Variable Compared to Itself in trace/_lib.php

**File:** `/home/herbert/pad/pad/info/types/trace/_lib.php`
**Line:** 337
**Severity:** Logic Error

**Issue:**
```php
if ( $padBase [$pad] == $padBase [$pad] )     return $type . '-true';
```

This condition compares `$padBase[$pad]` to itself, which will always evaluate to `true`. This appears to be a typo where the second variable should likely be `$padTrue[$pad]` based on the context.

**Expected Fix:**
```php
if ( $padBase [$pad] == $padTrue [$pad] )     return $type . '-true';
```

This would make the logic consistent with the next line which compares against `$padFalse`.
