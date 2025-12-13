# Bugs Found in /home/herbert/pad/pad/functions/

## 1. Missing Semicolon in range.php

**File:** `/home/herbert/pad/pad/functions/range.php`
**Line:** 3
**Severity:** Syntax Error

**Issue:**
```php
return ( $value >= $parm[0] and $value <= $parm[1] )
```

Missing semicolon at the end of the return statement.

**Fix:**
```php
return ( $value >= $parm[0] and $value <= $parm[1] );
```

---

## 2. Undefined Function Call in dumpxxx.php

**File:** `/home/herbert/pad/pad/functions/dumpxxx.php`
**Line:** 3
**Severity:** Runtime Error

**Issue:**
```php
aaa();
```

Calls an undefined function `aaa()`. This will cause a fatal error at runtime unless this function is defined elsewhere in the codebase.

**Action Required:**
Either define the `aaa()` function or remove/replace this call with the intended functionality.
