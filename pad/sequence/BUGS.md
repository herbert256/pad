# Bug Report - sequence Directory

## Bugs Found

### 1. /home/herbert/pad/pad/sequence/actions/types/average.php

#### Line 3: Division by Zero Risk
**Severity:** High

**Description:** Division by count without checking if array is empty.

```php
$pqResult = [ $pqActionKey => array_sum ( $pqResult ) / count ( $pqResult ) ];
```

**Issue:** If `$pqResult` is an empty array, `count($pqResult)` returns 0, causing a division by zero error.

**Recommendation:**
```php
$count = count($pqResult);
if ($count === 0) {
    $pqResult = [$pqActionKey => 0]; // or handle error appropriately
} else {
    $pqResult = [$pqActionKey => array_sum($pqResult) / $count];
}
```

---

### 2. /home/herbert/pad/pad/sequence/actions/types/median.php

#### Line 8: Undefined Variable Reference
**Severity:** High

**Description:** Variable `$pqActionStart` is used but never defined in this file.

```php
foreach ( $pqActionStart as $padK => $padV ) {
```

**Issue:** This variable appears to be expected from the calling context but is not validated. If not set, this will cause a warning or error.

---

#### Line 3-4: Integer Truncation Issue
**Severity:** Medium

**Description:** Median calculation uses integer casting which may not give accurate median for even-sized arrays.

```php
$pqTmp    = count ( $pqResult ) / 2;
$pqMedian = (int) $pqTmp;
```

**Issue:** For even-sized arrays, median should be the average of the two middle values, not just one value. The current implementation will always round down.

---

### 3. /home/herbert/pad/pad/sequence/actions/types/combine.php

#### Line 13: Strict Comparison with FALSE May Fail
**Severity:** Medium

**Description:** Using strict comparison with `FALSE` for array iteration.

```php
while ( $pqMerge1Val !== FALSE or $pqMerge2Val !== FALSE) {
```

**Issue:** If the array contains the boolean value `FALSE` as a legitimate element, the loop will terminate prematurely. Should use additional checks or a different iteration method.

**Recommendation:** Track iteration separately or use different termination logic.

---

### 4. /home/herbert/pad/pad/sequence/actions/types/append.php

#### Line 3-5: Missing Array Validation
**Severity:** Medium

**Description:** Nested foreach loops without validation that arrays exist.

```php
foreach ( $pqActionList as $pqAppendKey )
  foreach ($pqStore [$pqAppendKey] as $pqAppendValue)
    $pqResult [] = $pqAppendValue;
```

**Issue:** If `$pqStore[$pqAppendKey]` doesn't exist or isn't an array, this will generate a warning.

**Recommendation:**
```php
foreach ($pqActionList as $pqAppendKey) {
    if (isset($pqStore[$pqAppendKey]) && is_array($pqStore[$pqAppendKey])) {
        foreach ($pqStore[$pqAppendKey] as $pqAppendValue) {
            $pqResult[] = $pqAppendValue;
        }
    }
}
```

---

### 5. /home/herbert/pad/pad/sequence/actions/types/prepend.php

#### Line 3-6: Same Issue as append.php
**Severity:** Medium

**Description:** Missing validation for `$pqStore[$pqPrependKey]`.

```php
foreach ( $pqActionList as $pqPrependKey ) {
  $pqPrependReverse = array_reverse($pqStore [$pqPrependKey]);
```

**Issue:** If the key doesn't exist, `array_reverse()` will fail.

---

### 6. /home/herbert/pad/pad/sequence/actions/types/splice.php

#### Line 26: Array Access Without Validation
**Severity:** Medium

**Description:** Accessing `$pqStore` array without checking if key exists.

```php
array_splice (
  $pqResult,
  $pqActionList [0],
  null,
  $pqStore [ $pqActionList [1] ]
);
```

**Issue:** If `$pqActionList[1]` doesn't exist in `$pqStore`, this will cause an undefined array key warning.

---

### 7. /home/herbert/pad/pad/sequence/actions/function.php

#### Line 12: Unchecked Function Call
**Severity:** High

**Description:** `call_user_func_array()` called without validating that `$pqFunction` is callable.

```php
return call_user_func_array ( $pqFunction, $pqFunctionParms );
```

**Issue:** If `$pqFunction` is not a valid callable, this will generate a warning or error.

**Recommendation:**
```php
if (!is_callable($pqFunction)) {
    // Handle error
    return null;
}
return call_user_func_array($pqFunction, $pqFunctionParms);
```

---

### 8. /home/herbert/pad/pad/sequence/inits/parms.php

#### Line 8-9: Undefined Array Key in Conditional
**Severity:** Medium

**Description:** Checking `isset()` on `$pqStore[$padPrmName]` but not validating `$padPrmName` exists.

```php
if ( ! $pqPull and isset ( $pqStore [$padPrmName] ) )
  $pqPull = $padPrmName;
```

**Issue:** While this should work, `$padPrmName` comes from extract() which can be risky if the source array contains unexpected keys.

---

### 9. /home/herbert/pad/pad/sequence/inits/find/loop.php

#### Line 5: Use of extract() Function
**Severity:** High (Security)

**Description:** Using `extract()` without proper validation.

```php
extract ( $padParmsOne );
```

**Issue:** `extract()` can overwrite existing variables and is a security risk. If `$padParmsOne` contains malicious keys, it could overwrite critical variables.

**Recommendation:** Manually assign needed array values instead of using `extract()`.

---

### 10. /home/herbert/pad/pad/sequence/actions/set.php

#### Line 6: Undefined Variable in Array Assignment
**Severity:** Medium

**Description:** `$pqActionParm` may not be initialized before first use.

```php
$pqActionParm = '';
```

**Issue:** While this line initializes it, the logic flow suggests it might be used before initialization in some code paths.

---

### 11. /home/herbert/pad/pad/sequence/build/randomly/randomly.php

#### Line 6: Array Access Without Validation
**Severity:** Medium

**Description:** Accessing `$pqFixed` array without bounds checking.

```php
return $pqFixed [$pqRandomlyRand];
```

**Issue:** If `$pqRandomlyRand` is out of bounds for the `$pqFixed` array, this will cause an undefined offset error.

---

### 12. /home/herbert/pad/pad/sequence/plays/play/bool.php

#### Line 3: Dynamic Function Call Without Validation
**Severity:** High

**Description:** Constructing and calling function name dynamically without checking if it exists.

```php
return ( 'pqBool' . ucfirst($pqSeq) ) ( $pqLoop, $pqParm );
```

**Issue:** If the constructed function name doesn't exist, this will cause a fatal error.

**Recommendation:**
```php
$functionName = 'pqBool' . ucfirst($pqSeq);
if (!function_exists($functionName)) {
    // Handle error
    return false;
}
return $functionName($pqLoop, $pqParm);
```

---

### 13. /home/herbert/pad/pad/sequence/plays/play/function.php

#### Line 3: Same Dynamic Function Call Issue
**Severity:** High

**Description:** Same issue as bool.php

```php
return ( 'pq' . ucfirst($pqSeq) ) ( $pqLoop );
```

---

### 14. /home/herbert/pad/pad/sequence/exits/store/set.php

#### Line 3: Potential Undefined Array Access
**Severity:** Medium

**Description:** Accessing `$pqStore[$pqPull]` without validation.

```php
if ( $pqStoreUpdated ) $pqStore [$padLastPush] = array_values ( $pqStore [$pqPull] );
```

**Issue:** If `$pqPull` doesn't exist as a key in `$pqStore`, this will generate a warning.

---

### 15. /home/herbert/pad/pad/sequence/actions/types/shift.php

#### Line 7-9: Nested Array Access Without Validation
**Severity:** Medium

**Description:** Multiple array accesses without proper validation.

```php
if ( count($pqStore [$pqPull]) > $pqActionCnt )
```

**Issue:** If `$pqStore[$pqPull]` doesn't exist or isn't an array, `count()` will generate a warning.

---

### 16. /home/herbert/pad/pad/sequence/plays/parm.php

#### Line 4: Potential Array Access Issue
**Severity:** Medium

**Description:** Accessing array with `count($pqResult)` as index without bounds checking.

```php
$pqParm = $pqStore [$pqParm] [ count ( $pqResult ) ];
```

**Issue:** The index may be out of bounds if the stored array doesn't have enough elements.

---

### 17. /home/herbert/pad/pad/sequence/options/types/ramdomly.php

#### FILENAME: Typo in Filename
**Severity:** Low

**Description:** File is named "ramdomly.php" instead of "randomly.php"

**Issue:** This is a spelling error that could cause confusion and maintenance issues.

---

### 18. /home/herbert/pad/pad/sequence/inits/set.php

#### Line 21: str_contains() Compatibility
**Severity:** Low

**Description:** Using `str_contains()` which is only available in PHP 8.0+

```php
if ( str_contains ( $pqInc, '...' ) ) {
```

**Issue:** If running on PHP < 8.0, this will cause a fatal error. Should check PHP version or use alternative method.

---

## Summary

- **Critical:** 0
- **High:** 5
- **Medium:** 12
- **Low:** 2

**Total Bugs Found:** 19

### Most Critical Issues to Address:
1. Division by zero in average.php (line 3)
2. Undefined variable `$pqActionStart` in median.php
3. Dynamic function calls without validation in bool.php and function.php
4. Security risk: use of `extract()` in loop.php
5. Unchecked callable in function.php
6. Missing array validation in multiple files (append.php, prepend.php, splice.php, shift.php)
7. PHP 8.0+ compatibility issue with str_contains()
