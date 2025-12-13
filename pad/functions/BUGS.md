# Bug Report - /home/herbert/pad/pad/functions/

## Critical Bugs

### 1. Undefined Function Call
**File:** `/home/herbert/pad/pad/functions/dumpxxx.php`
**Line:** 3
**Description:** Function `aaa()` is called but never defined anywhere. This will cause a fatal error when the file is included.
**Severity:** Critical

## High Bugs

### 2. Missing Semicolon
**File:** `/home/herbert/pad/pad/functions/range.php`
**Line:** 3
**Description:** Missing semicolon at the end of the return statement. This is a syntax error that will prevent the script from running.
**Severity:** High

### 3. Potential False Positive from strpos()
**File:** `/home/herbert/pad/pad/functions/after.php`
**Line:** 3
**Description:** If `strpos()` returns FALSE (needle not found), `strpos()+1` will equal 1, causing `substr()` to return from position 1 instead of returning an empty string or the full value. Should check if strpos() !== FALSE first.
**Severity:** High

### 4. Potential False Positive from strpos()
**File:** `/home/herbert/pad/pad/functions/before.php`
**Line:** 3
**Description:** If `strpos()` returns FALSE (needle not found), `substr($value, 0, FALSE)` will return an empty string instead of the full value. Should check if strpos() !== FALSE first.
**Severity:** High

### 5. Potential False Positive from strrpos()
**File:** `/home/herbert/pad/pad/functions/beforeLast.php`
**Line:** 3
**Description:** If `strrpos()` returns FALSE (needle not found), `substr($value, 0, FALSE)` will return an empty string instead of the full value. Should check if strrpos() !== FALSE first.
**Severity:** High

## Medium Bugs

### 6. Undefined Variable Access
**File:** `/home/herbert/pad/pad/functions/afterLast.php`
**Line:** 3
**Description:** Variables `$parm` and `$value` are used but not defined. These appear to be expected from the calling context, but there's no error handling if they're not set.
**Severity:** Medium

### 7. Undefined Variables in Multiple Files
**Files:** Most files in the directory
**Description:** Variables `$value`, `$parm`, `$count` are used without being defined in the local scope. These appear to be injected from the calling context, but there's no validation or error handling.
**Severity:** Medium

### 8. Deprecated Filter Constant
**File:** `/home/herbert/pad/pad/functions/sanitize.php`
**Line:** 3
**Description:** `FILTER_SANITIZE_FULL_SPECIAL_CHARS` is deprecated as of PHP 8.1. Should use `htmlspecialchars()` instead.
**Severity:** Medium

### 9. Invalid Filter Flag Usage
**File:** `/home/herbert/pad/pad/functions/stripLow.php`
**Line:** 3
**Description:** `FILTER_FLAG_STRIP_LOW` is being used as the filter type (2nd parameter), but it's a flag that should be used in the 3rd parameter options array. Should be used with FILTER_SANITIZE_STRING or similar.
**Severity:** Medium

### 10. Missing Error Handling for date()
**File:** `/home/herbert/pad/pad/functions/date.php`
**Line:** 17, 21
**Description:** `strtotime()` can return FALSE on failure, which would be passed to `date()`. No validation of the result before using it.
**Severity:** Medium

### 11. Undefined Global Variable
**File:** `/home/herbert/pad/pad/functions/date.php`
**Line:** 8
**Description:** `$GLOBALS['padFmtDate']` is accessed without checking if it exists. Should use `isset()` or null coalescing operator.
**Severity:** Medium

### 12. Undefined Constant
**File:** `/home/herbert/pad/pad/functions/exists.php`
**Line:** 3
**Description:** Constant `APP` is used but not defined in this file. Will cause a fatal error if not defined in the calling context.
**Severity:** Medium

### 13. Missing Array Index Check
**File:** `/home/herbert/pad/pad/functions/like.php`
**Line:** 6
**Description:** `$parm` is accessed without checking if it's an array or if index 0 exists. Will cause errors if $parm is not properly set.
**Severity:** Medium

### 14. Missing Array Index Check
**File:** `/home/herbert/pad/pad/functions/mid.php`
**Line:** 3
**Description:** `$parm[0]` and `$parm[1]` are accessed without checking if they exist. Will cause undefined index warnings.
**Severity:** Medium

### 15. Undefined Constants
**File:** `/home/herbert/pad/pad/functions/time.php` and `/home/herbert/pad/pad/functions/timestamp.php`
**Line:** 3
**Description:** Constant `PAD` is used but not defined in these files. Will cause a fatal error if not defined globally.
**Severity:** Medium

## Low Bugs

### 16. Inefficient Boolean Return
**File:** `/home/herbert/pad/pad/functions/contains.php`
**Lines:** 3-6
**Description:** Could be simplified to `return (strpos($value, $parm[0]) !== FALSE);` instead of if-else structure.
**Severity:** Low

### 17. Inconsistent Return Types
**File:** `/home/herbert/pad/pad/functions/in.php`
**Line:** 13
**Description:** Returns '1' (string) for true and '' (empty string) for false instead of boolean values. This inconsistency could lead to unexpected behavior in strict comparisons.
**Severity:** Low

### 18. Type Inconsistency
**File:** `/home/herbert/pad/pad/functions/like.php`
**Line:** 40
**Description:** Returns '1' (string) or '' (empty string) instead of boolean. Inconsistent with typical PHP boolean returns.
**Severity:** Low

### 19. Missing Input Validation
**File:** `/home/herbert/pad/pad/functions/substr.php`
**Lines:** 3-9
**Description:** No validation that `$count` is set or that `$parm` array has the required indices.
**Severity:** Low

### 20. Duplicate Function Code
**Files:** `/home/herbert/pad/pad/functions/capitalize.php` and `/home/herbert/pad/pad/functions/ucwords.php`
**Description:** Both files contain identical code (`return ucwords($value);`). This is redundant.
**Severity:** Low
