# Bug Report - /home/herbert/pad/pad/error/

## Bugs Found

### 1. Variable Variable Usage with Unvalidated Input
**File:** /home/herbert/pad/pad/error/error.php
**Line:** 21
**Description:** The code uses `$$level` (variable variable) which can lead to undefined variable errors if `$level` contains a value that doesn't match any of the local variables (`$none`, `$error`, `$warning`, `$notice`, `$all`). If an invalid value is passed, PHP will try to access an undefined variable.
**Severity:** Medium

### 2. Missing Space in Concatenation
**File:** /home/herbert/pad/pad/events/eval/error.php
**Line:** 8
**Description:** Missing space between `$e->getLine()` and `$e->getMessage()` in the concatenation. Should be `$e->getLine() . ' ' . $e->getMessage()` for proper formatting.
**Severity:** Low

### 3. Undefined Function Call
**File:** /home/herbert/pad/pad/error/types/dump.php
**Line:** 9
**Description:** The function `padDumpToDir()` is called but may not be defined at this point. If this function doesn't exist, it will cause a fatal error.
**Severity:** High

### 4. Undefined Function Call
**File:** /home/herbert/pad/pad/error/types/log.php
**Line:** 9
**Description:** The function `padLogError()` is called but may not be defined. If this function doesn't exist, it will cause a fatal error.
**Severity:** High

### 5. Undefined Function Call
**File:** /home/herbert/pad/pad/error/types/pad.php
**Line:** 35
**Description:** The function `padMakeSafe()` is called but may not be defined. If this function doesn't exist, it will cause a fatal error.
**Severity:** High

### 6. Undefined Function Call
**File:** /home/herbert/pad/pad/error/types/pad.php
**Line:** 45
**Description:** The function `padLogError()` is called but may not be defined. If this function doesn't exist, it will cause a fatal error.
**Severity:** High

### 7. Undefined Function Call
**File:** /home/herbert/pad/pad/error/types/pad.php
**Line:** 48
**Description:** The function `padDumpToDir()` is called but may not be defined. If this function doesn't exist, it will cause a fatal error.
**Severity:** High

### 8. Undefined Function Call
**File:** /home/herbert/pad/pad/error/types/pad.php
**Line:** 50
**Description:** The function `padDump()` is called but may not be defined. If this function doesn't exist, it will cause a fatal error.
**Severity:** High

### 9. Undefined Function Call
**File:** /home/herbert/pad/pad/error/types/pad.php
**Line:** 83, 84, 86
**Description:** The functions `padErrorLog()` and `padErrorExit()` are called but are not defined anywhere in the codebase. This will cause fatal errors.
**Severity:** Critical

### 10. Undefined Function Call
**File:** /home/herbert/pad/pad/error/boot.php
**Line:** 135
**Description:** The constant `PAD` is used but may not be defined. If this constant doesn't exist, it will cause a fatal error or notice.
**Severity:** High

### 11. Undefined Function Call
**File:** /home/herbert/pad/pad/error/boot.php
**Line:** 162
**Description:** The constant `PAD` is used but may not be defined. If this constant doesn't exist, it will cause a fatal error or notice.
**Severity:** High

### 12. Undefined Function Call
**File:** /home/herbert/pad/pad/error/boot.php
**Line:** 85, 86
**Description:** The function `padLocal()` is called but is defined later in the same file (line 167). While this works in PHP due to function hoisting, it's defined after the call which can be confusing and may fail if the file execution is interrupted.
**Severity:** Low

### 13. Unreachable Code
**File:** /home/herbert/pad/pad/error/types/php.php
**Line:** 15
**Description:** The `return '';` statement on line 15 is unreachable because line 13 throws an exception. This code will never execute.
**Severity:** Low

### 14. Logic Error in Condition Check
**File:** /home/herbert/pad/pad/error/parms/parm.php
**Line:** 17-20
**Description:** The logic is inverted. If `$padPrmTypeX` is truthy, it returns without the type suffix, but if it's falsy, it includes it. This seems backwards - typically you'd want to include the suffix when you have a type. Line 17 should be `if ( ! $padPrmTypeX )` or the return statements should be swapped.
**Severity:** Medium

### 15. Undefined Function Call
**File:** /home/herbert/pad/pad/error/types/pad.php
**Line:** 23
**Description:** The function `padExit()` is called but may not be defined at this point. If this function doesn't exist, it will cause a fatal error.
**Severity:** High
