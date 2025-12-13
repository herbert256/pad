# Bugs Found in /home/herbert/pad/pad/at/

## Bug 1: Logic error in padAtCheckCondition function

**File:** `/home/herbert/pad/pad/at/_lib/check.php`
**Line:** 82-85
**Severity:** High

**Description:**
The function `padAtCheckCondition` has backwards logic. It returns TRUE when the condition is NOT found in the part, which is incorrect.

```php
function padAtCheckCondition ( $part, $condition ) {

  if ( ! str_contains ( $part, $condition ) )
    return TRUE;  // This returns TRUE when condition is NOT found!
```

**Expected behavior:**
When the condition is not contained in the part, the function should continue to validate the parts. The early return of TRUE bypasses all validation logic.

**Impact:**
This causes the validation to always pass when a condition operator is not present, which may allow invalid input to pass validation checks.
