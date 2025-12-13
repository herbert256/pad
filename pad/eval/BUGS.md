# Bug Report - /home/herbert/pad/pad/eval/

## Bug 1: Division by Zero Risk
**File:** `/home/herbert/pad/pad/eval/go/doubleVarVar.php`
**Line:** 24
**Severity:** High

### Issue
```php
elseif ( $opr == '/' ) $now = $left / $right;
```

Division operation does not check if `$right` is zero before performing the division. This will cause a DivisionByZeroError (PHP 8+) or a warning (PHP 7) if `$right` equals 0.

### Current Code
```php
if     ( $opr == '+' ) $now = $left + $right;
elseif ( $opr == '-' ) $now = $left - $right;
elseif ( $opr == '*' ) $now = $left * $right;
elseif ( $opr == '/' ) $now = $left / $right;  // BUG: No zero check
elseif ( $opr == '%' ) $now = $left % $right;
```

### Fix Needed
Add a zero check before division:
```php
elseif ( $opr == '/' ) $now = ($right != 0) ? $left / $right : 0;
```
Or throw an appropriate error.

---

## Bug 2: Inverted Logic Condition
**File:** `/home/herbert/pad/pad/eval/parms/parm.php`
**Line:** 17-20
**Severity:** High

### Issue
The logic for determining when to include `$padPrmTypeX` in the tag is inverted. The condition on line 17 checks if `$padPrmTypeX` is truthy, but then line 18 returns the tag WITHOUT the type suffix, and line 20 returns the tag WITH the type suffix.

### Current Code
```php
if ( $padPrmTypeX )
    return padTagValue ( "$tag:$name" );        // Returns WITHOUT type when type EXISTS
else
    return padTagValue ( "$tag:$name#$padPrmTypeX" );  // Returns WITH type when type is EMPTY
```

### Expected Behavior
When `$padPrmTypeX` has a value, it should be included in the tag. When it's empty, it shouldn't be.

### Fix Needed
Swap the return statements:
```php
if ( $padPrmTypeX )
    return padTagValue ( "$tag:$name#$padPrmTypeX" );
else
    return padTagValue ( "$tag:$name" );
```

---

All other files have been analyzed and no additional bugs were found.
