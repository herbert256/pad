# Bug Report - /home/herbert/pad/pad/call/

## Bug #1
**File:** /home/herbert/pad/pad/call/_return.php
**Line:** 3
**Description:** Undefined variable `$padCallPHP` is used without being initialized or checked. This will generate a PHP notice/warning.
**Severity:** Medium

## Bug #2
**File:** /home/herbert/pad/pad/call/_return.php
**Line:** 9
**Description:** Undefined variable `$padCallOB` is used without being initialized or checked. This will generate a PHP notice/warning.
**Severity:** Medium

## Bug #3
**File:** /home/herbert/pad/pad/call/_try.php
**Line:** 3
**Description:** Undefined variable `$padCall` is used without being initialized or checked. This will cause a fatal error if the variable doesn't contain a valid file path.
**Severity:** High

## Bug #4
**File:** /home/herbert/pad/pad/call/_tryOnce.php
**Line:** 3
**Description:** Undefined variable `$padCall` is used without being initialized or checked. This will cause a fatal error if the variable doesn't contain a valid file path.
**Severity:** High

## Bug #5
**File:** /home/herbert/pad/pad/call/_call.php
**Line:** 3, 5, 8
**Description:** Undefined constant `PAD` is used without being defined. This will cause fatal errors.
**Severity:** Critical

## Bug #6
**File:** /home/herbert/pad/pad/call/_call.php
**Line:** 5, 7, 11, 13
**Description:** Undefined variable `$padCall` is used without being initialized. This will generate PHP notices.
**Severity:** Medium

## Bug #7
**File:** /home/herbert/pad/pad/call/_call.php
**Line:** 7, 13
**Description:** `$GLOBALS ['padInfo']` is accessed without checking if it exists. This will generate a PHP notice if not defined.
**Severity:** Medium

## Bug #8
**File:** /home/herbert/pad/pad/call/_call.php
**Line:** 10, 11
**Description:** Undefined variable `$padTry` is assigned but then `$padCallPHP` is assigned from an include that depends on this variable. Poor variable flow.
**Severity:** Medium

## Bug #9
**File:** /home/herbert/pad/pad/call/_init.php
**Line:** 3
**Description:** Variables `$padCallPHP` and `$padCallOB` are initialized as empty strings, but `$padCallPHP` might be expected to be NULL in some cases for proper type checking later.
**Severity:** Low

## Bug #10
**File:** /home/herbert/pad/pad/call/_init.php
**Line:** 5, 6
**Description:** `$GLOBALS ['padInfo']` is accessed without checking if it exists. This will generate PHP notices.
**Severity:** Medium

## Bug #11
**File:** /home/herbert/pad/pad/call/_init.php
**Line:** 6
**Description:** Undefined constant `PAD` is used without being defined. This will cause a fatal error.
**Severity:** Critical

## Bug #12
**File:** /home/herbert/pad/pad/call/_once.php
**Line:** 3, 5, 8, 10, 14, 18
**Description:** Undefined constant `PAD` is used without being defined. This will cause fatal errors.
**Severity:** Critical

## Bug #13
**File:** /home/herbert/pad/pad/call/_once.php
**Line:** 5
**Description:** Undefined variable `$padCall` is used without being initialized. This will generate a PHP notice.
**Severity:** Medium

## Bug #14
**File:** /home/herbert/pad/pad/call/_once.php
**Line:** 7, 13
**Description:** `$GLOBALS ['padInfo']` is accessed without checking if it exists. This will generate PHP notices.
**Severity:** Medium

## Bug #15
**File:** /home/herbert/pad/pad/call/_once.php
**Line:** 10
**Description:** Undefined variable `$padTry` is assigned and used. This assumes the variable will be used by the included file.
**Severity:** Medium

## Bug #16
**File:** /home/herbert/pad/pad/call/any.php
**Line:** 3, 6
**Description:** Undefined constant `PAD` is used without being defined. This will cause fatal errors.
**Severity:** Critical

## Bug #17
**File:** /home/herbert/pad/pad/call/any.php
**Line:** 5
**Description:** Undefined variable `$padCallOB` is used without being initialized (depends on _call.php being included first). This will generate a PHP notice if include fails.
**Severity:** Medium

## Bug #18
**File:** /home/herbert/pad/pad/call/any.php
**Line:** 6, 8
**Description:** Undefined variable `$padCallPHP` is used without being initialized. This will generate PHP notices.
**Severity:** Medium

## Bug #19
**File:** /home/herbert/pad/pad/call/noOne.php
**Line:** 3, 8
**Description:** Undefined constant `PAD` is used without being defined. This will cause fatal errors.
**Severity:** Critical

## Bug #20
**File:** /home/herbert/pad/pad/call/noOne.php
**Line:** 5, 6
**Description:** Undefined variable `$padCallPHP` is used without being initialized. This will generate PHP notices.
**Severity:** Medium

## Bug #21
**File:** /home/herbert/pad/pad/call/ob.php
**Line:** 3
**Description:** Undefined constant `PAD` is used without being defined. This will cause a fatal error.
**Severity:** Critical

## Bug #22
**File:** /home/herbert/pad/pad/call/ob.php
**Line:** 5
**Description:** Undefined variable `$padCallOB` is used without being initialized (depends on _call.php). This will generate a PHP notice if include fails.
**Severity:** Medium

## Bug #23
**File:** /home/herbert/pad/pad/call/obNoOne.php
**Line:** 3
**Description:** Undefined constant `PAD` is used without being defined. This will cause a fatal error.
**Severity:** Critical

## Bug #24
**File:** /home/herbert/pad/pad/call/obNoOne.php
**Line:** 5, 6, 8
**Description:** Undefined variable `$padCallPHP` is used without being initialized. This will generate PHP notices.
**Severity:** Medium

## Bug #25
**File:** /home/herbert/pad/pad/call/obNoOne.php
**Line:** 8
**Description:** Undefined variable `$padCallOB` is returned without being initialized (depends on _call.php). This will generate a PHP notice if include fails.
**Severity:** Medium

## Bug #26
**File:** /home/herbert/pad/pad/call/once.php
**Line:** 3, 5
**Description:** Undefined constant `PAD` is used without being defined. This will cause fatal errors.
**Severity:** Critical

## Bug #27
**File:** /home/herbert/pad/pad/call/_exit.php
**Line:** 3
**Description:** Variable `$padCallOB` is assigned from `ob_get_clean()` but if output buffering was not started, this could return FALSE instead of a string.
**Severity:** Low

## Bug #28
**File:** /home/herbert/pad/pad/call/_exit.php
**Line:** 5, 6
**Description:** Undefined variable `$padCallPHP` is used without being initialized or checked. This will generate PHP notices.
**Severity:** Medium

## Bug #29
**File:** /home/herbert/pad/pad/call/_exit.php
**Line:** 8
**Description:** Checking `is_nan()` on non-float values before checking `is_float()` could potentially cause issues, though the order here is correct.
**Severity:** Low
