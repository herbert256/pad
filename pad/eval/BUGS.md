# Bug Report - /home/herbert/pad/pad/eval/

## Bugs Found

### 1. Undefined Variable Usage
**File:** /home/herbert/pad/pad/eval/eval.php
**Line:** 14
**Description:** The variable `$value` is used in the foreach loop but is never initialized before the loop. On the first iteration, this will generate an "Undefined variable" notice/warning. It should be initialized to an empty value or null before the loop.
**Severity:** Medium

### 2. Undefined Function Call
**File:** /home/herbert/pad/pad/eval/eval.php
**Line:** 9, 10, 11
**Description:** Functions `padEvalParse()`, `padEvalTrace()`, `padEvalAfter()`, `padEvalPipes()` are called but may not be defined. If these functions don't exist, they will cause fatal errors.
**Severity:** Critical

### 3. Undefined Function Call
**File:** /home/herbert/pad/pad/eval/eval.php
**Line:** 14
**Description:** Function `padEvalResult()` is called but may not be defined. If this function doesn't exist, it will cause a fatal error.
**Severity:** Critical

### 4. Undefined Constant
**File:** /home/herbert/pad/pad/eval/go/go.php
**Line:** 5
**Description:** The constant `padEval_one` is used without being defined. This should either be a string `'padEval_one'` or a defined constant. This will cause a fatal error if not defined.
**Severity:** Critical

### 5. Undefined Function Call
**File:** /home/herbert/pad/pad/eval/go/go.php
**Line:** 18
**Description:** Function `padEvalOpr()` is called but may not be defined. If this function doesn't exist, it will cause a fatal error.
**Severity:** Critical

### 6. Undefined Function Call
**File:** /home/herbert/pad/pad/eval/go/doubleArrArr.php, doubleArrVar.php, doubleVarArr.php
**Line:** 6, 11
**Description:** Function `padError()` is called but may not be defined. If this function doesn't exist, it will cause a fatal error.
**Severity:** Critical

### 7. Division by Zero Risk
**File:** /home/herbert/pad/pad/eval/go/doubleVarVar.php
**Line:** 24
**Description:** Division operation `$left / $right` is performed without checking if `$right` is zero. This will cause a "Division by zero" warning/error.
**Severity:** High

### 8. Modulo by Zero Risk
**File:** /home/herbert/pad/pad/eval/go/doubleVarVar.php
**Line:** 25
**Description:** Modulo operation `$left % $right` is performed without checking if `$right` is zero. This will cause a "Division by zero" warning/error.
**Severity:** High

### 9. Type Mismatch Potential
**File:** /home/herbert/pad/pad/eval/go/doubleVarVar.php
**Line:** 15-19
**Description:** The code uses `strpos()` to detect decimal points, but doesn't validate that `$left` and `$right` are strings first. If they are already numeric types, this could cause unexpected behavior.
**Severity:** Medium

### 10. Undefined Variable Usage
**File:** /home/herbert/pad/pad/eval/actions/double.php, doubleLeft.php, doubleRight.php, single.php
**Line:** Various
**Description:** Variables `$result`, `$f`, `$k`, `$b`, `$myself`, `$start`, `$end` are used but never defined in these files. They must be defined in the including scope, but this is fragile and error-prone. If the including context doesn't define these, it will cause "Undefined variable" errors.
**Severity:** High

### 11. Undefined Function Call
**File:** /home/herbert/pad/pad/eval/actions/double.php, etc.
**Line:** 7
**Description:** Function `padEvalTrace()` is called but may not be defined. If this function doesn't exist, it will cause a fatal error.
**Severity:** Critical

### 12. Undefined Function Call
**File:** /home/herbert/pad/pad/eval/parms/sequence.php
**Line:** 8, 15
**Description:** Constants `PQ` is used but may not be defined. This will cause a fatal error or notice if not defined.
**Severity:** High

### 13. Undefined Function Call
**File:** /home/herbert/pad/pad/eval/parms/app.php
**Line:** 3
**Description:** Function `padAppFunctionCheck()` is called but may not be defined. If this function doesn't exist, it will cause a fatal error. Also, constant `APP2` may not be defined.
**Severity:** Critical

### 14. Array Access Without Existence Check
**File:** /home/herbert/pad/pad/eval/single/content.php
**Line:** 3
**Description:** Accessing `$GLOBALS ['padContentStore'] [$name]` without checking if the key exists first. This will cause an "Undefined index" notice/warning if the key doesn't exist.
**Severity:** Medium

### 15. Array Access Without Existence Check
**File:** /home/herbert/pad/pad/eval/single/data.php
**Line:** 3
**Description:** Accessing `$GLOBALS ['padDataStore'] [$name]` without checking if the key exists first. This will cause an "Undefined index" notice/warning if the key doesn't exist.
**Severity:** Medium

### 16. Array Access Without Existence Check
**File:** /home/herbert/pad/pad/eval/single/flag.php
**Line:** 3
**Description:** Accessing `$GLOBALS ['padBoolStore'] [$name]` without checking if the key exists first. This will cause an "Undefined index" notice/warning if the key doesn't exist.
**Severity:** Medium

### 17. Array Access Without Existence Check
**File:** /home/herbert/pad/pad/eval/single/pull.php
**Line:** 3
**Description:** Accessing `$GLOBALS ['pqStore'] [$name]` without checking if the key exists first. This will cause an "Undefined index" notice/warning if the key doesn't exist.
**Severity:** Medium

### 18. Variable Variable Usage Risk
**File:** /home/herbert/pad/pad/eval/single/object.php
**Line:** 3
**Description:** Using variable variables `$$name` without validation. If `$name` contains an invalid variable name or a variable that doesn't exist, this will cause an "Undefined variable" notice.
**Severity:** Medium

### 19. Undefined Function Call
**File:** /home/herbert/pad/pad/eval/single/object.php
**Line:** 3
**Description:** Function `padToArray()` is called but may not be defined. If this function doesn't exist, it will cause a fatal error.
**Severity:** Critical

### 20. Undefined Function Calls
**File:** Multiple files in /home/herbert/pad/pad/eval/single/
**Line:** Various
**Description:** Functions like `padArrayValue()`, `padFieldValue()`, `padGetLevelArray()`, `padOptValue()`, `padTagValue()`, `padTagAsFunction()` are called but may not be defined. If these functions don't exist, they will cause fatal errors.
**Severity:** Critical

### 21. Undefined Function Call
**File:** /home/herbert/pad/pad/eval/type/parms.php
**Line:** 4
**Description:** Function `padEvalNextKey()` is called but may not be defined. If this function doesn't exist, it will cause a fatal error.
**Severity:** Critical

### 22. Array Access Without Bounds Check
**File:** /home/herbert/pad/pad/eval/type/parms.php
**Line:** 5, 10
**Description:** Array access `$result [$k] [3]` without checking if the key exists. Could cause "Undefined index" warnings.
**Severity:** Medium

### 23. Undefined Constant Usage
**File:** Multiple files in /home/herbert/pad/pad/eval/
**Line:** Various
**Description:** The constant `PAD` is used extensively throughout the eval directory but may not be defined. This will cause fatal errors or notices if not defined.
**Severity:** Critical
